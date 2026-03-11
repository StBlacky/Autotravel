<?php
/* ===============================
   cube.data.php — подготовка данных для cube.php
   =============================== */

// --- определяем SQL для сводной таблицы ---
// 1) Зарубежные страны (m=1 → id областей < 0)
if ($a == 0 && $m) {
    $sql_xx = "
        SELECT a.name AS name, a.id AS id, a.slug AS slug,g.exid AS exid, g.exgrp AS extyp, COUNT(*) AS c
        FROM exbytype g
        JOIN areas a ON a.id = g.area
        WHERE a.id < 0
        GROUP BY a.name, g.exgrp
        ORDER BY a.name ASC
    ";
// 2) Россия (m пуст)
} elseif ($a == 0 && !$r) {
    $sql_xx = "
        SELECT a.name AS name, a.id AS id, a.slug AS slug, g.exid AS exid, g.exgrp AS extyp, COUNT(*) AS c
        FROM exbytype g
        JOIN areas a ON a.id = g.area
        WHERE a.id > 0
        GROUP BY a.name, g.exgrp
        ORDER BY a.name ASC
    ";
} elseif ($a != 0 && !$r) {
    // Конкретная область/страна → города
    $sql_xx = "
        SELECT t.name AS name, t.url AS turl, t.hide AS thide, t.id AS id,
               g.exid AS exid, g.exgrp AS extyp, COUNT(*) AS c
        FROM exbytype g
        JOIN towns t ON t.id = g.town
        WHERE t.area = $a
        GROUP BY t.name, g.exgrp
        ORDER BY t.name ASC
    ";
} elseif ($a && $r) {
    // Область/страна + конкретный тип
    $sql_xx = "
        SELECT t.name AS name, t.url AS turl, t.hide AS thide, t.id AS id,
               g.exid AS exid, g.exgrp AS extyp, COUNT(*) AS c
        FROM exbytype g
        JOIN towns t ON t.id = g.town
        WHERE t.area = $a AND g.exgrp = '$r'
        GROUP BY t.name, g.exgrp
        ORDER BY t.name ASC
    ";
}

// --- карта типов и иконок (основная) ---
$types_map = [];
$types_by_desc = []; // ключ: нижний регистр desc → ['desc','icon']

$res = $dbi->query("SELECT id, `desc`, icon FROM type_ex");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $types_map[$row['id']] = [
            'desc' => $row['desc'],
            'icon' => $row['icon']
        ];
        $key = mb_strtolower(trim($row['desc']), 'UTF-8');
        $types_by_desc[$key] = [
            'desc' => $row['desc'],
            'icon' => $row['icon']
        ];
    }
}

// --- ручные иконки для сервисных типов ---
$service_icons = [
    '2' => ['desc' => 'Гостиницы',          'icon' => '10_hotels.png'],
    '3' => ['desc' => 'Кафе, рестораны',    'icon' => '11_cafe.png'],
    '5' => ['desc' => 'Что привезти', 'icon' => '12_souvenirs.png'],
    '6' => ['desc' => 'Сервис',             'icon' => '14_service.png'],
    '8' => ['desc' => 'Праздники и события','icon' => '15_auto_tours.png']
];

// --- вспомогательная функция для очистки префиксов ---
function cube_clean_desc($s) {
    // "1. Архитектура" → "Архитектура"
    $s = preg_replace('/^\s*\d+[\.\)]\s*/u', '', (string)$s);
    return trim($s);
}

// --- читаем основную выборку ---
$result = $dbi->query($sql_xx);
$td = [];
$ar = [];
$th = [];

if ($result) {
    while ($myrow = mysqli_fetch_array($result)) {
        $tname = cyrname($myrow["name"]);
        $extyp = $myrow["extyp"];
        $ar[$tname] = $myrow["id"];
        $ar_slug[$tname] = $myrow["slug"] ?? '';
        // добавляем тип в список заголовков
        if (!in_array($extyp, $th)) {
            $th[] = $extyp;
        }

        // считаем количество
        $td[$tname][$extyp]["count"] = $myrow["c"];
        // если в категории ровно 1 объект — сохраним его exid для прямого перехода
        if ((int)$myrow["c"] === 1 && !empty($myrow["exid"])) {
        $td[$tname][$extyp]["exid"] = (int)$myrow["exid"];
}

        $td[$tname]["url"]   = $myrow["turl"] ?? '';
        $td[$tname]["hide"]  = $myrow["thide"] ?? 0;
        $td[$tname]["texid"] = $myrow["exid"];
    }
}

// --- сортировка типов: досты (1.*) → сервисы (2.* и далее) ---
$th_dost = [];
$th_serv = [];
foreach ($th as $k) {
    if (substr($k, 0, 1) == '1') $th_dost[] = $k;
    else $th_serv[] = $k;
}
$th = array_merge($th_dost, $th_serv);

// --- создаём карты для шаблонов ---
$icons_by_desc = [];   // ключ: исходная метка (exgrp) → /jpg/icon
$labels_to_title = []; // исходная метка → чистый desc без цифр

foreach ($th as $label) {
    $clean = cube_clean_desc($label);
    $labels_to_title[$label] = $clean;

    $key = mb_strtolower($clean, 'UTF-8');
    if (!empty($types_by_desc[$key]['icon'])) {
        $icons_by_desc[$label] = "/jpg/" . $types_by_desc[$key]['icon'];
    } else {
        // пробуем по первой цифре определить сервисную иконку
        $prefix = substr($label, 0, 1);
        if (isset($service_icons[$prefix])) {
            $icons_by_desc[$label] = "/jpg/" . $service_icons[$prefix]['icon'];
        } else {
            $icons_by_desc[$label] = "";
        }
    }
}

function cube_get_link($areaId, $areaSlug, $label, $count, $exid = 0) {

    global $a, $dbi;

    $prefix = substr($label, 0, 1);

    // если 1 объект — прямой переход
    if ($count == 1 && $exid > 0) {
        return "/otklik.php/".$exid;
    }

    // === РЕЖИМ ОБЩЕЙ ТАБЛИЦЫ (a == 0) ===
    if ($a == 0) {

        // 1.* → SEO
        if ($prefix === '1' && $areaSlug) {

            $clean = cube_clean_desc($label);

            $stmt = $dbi->prepare("SELECT desc_slug FROM type_ex WHERE `desc`=? LIMIT 1");
            $stmt->bind_param("s", $clean);
            $stmt->execute();
            $stmt->bind_result($desc_slug);
            $stmt->fetch();
            $stmt->close();

            if ($desc_slug) {
                return "/excite_area.php/".$areaSlug."/".$desc_slug;
            }
        }

        // 5 → bring
        if ($prefix === '5' && $areaSlug) {
            return "/excite_bring_area/".$areaSlug."/";
        }

        // ✅ 2,3,6,8 → ВОЗВРАЩАЕМ старую механику
        return "/cube.php?a=".$areaId."&r=".urlencode($label);
    }

    // === ВНУТРИ РЕГИОНА (a != 0) — оставляем старое ===
    if ($prefix === '1') {
        return "/excite.php/".$areaId."/1?cat=".urlencode($label);
    }

    return "/excite.php/".$areaId."/".$prefix;
}



// --- отладка ---
$vvv .= "<!-- SQL: $sql_xx -->\n";
?>
