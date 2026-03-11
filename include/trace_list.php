<?php

/*
 * КАТАЛОГ ТРАСС — актуальная версия
 */

/* ================================================================
 * 1. Загружаем классификатор классов дорог
 * ================================================================ */

$sql_classes = "
    SELECT id, code, name, description, sort_order
    FROM trace_class
    ORDER BY sort_order ASC, id ASC
";
$res = $dbi->query($sql_classes);

$classes = [];
while ($row = mysqli_fetch_assoc($res)) {
    $classes[(int)$row['id']] = $row;
}
mysqli_free_result($res);

if (!$classes) {
    echo "<p class='note-block'>Каталог недоступен.</p>";
    return;
}

/* ================================================================
 * 2. Загружаем трассы + тип (страна)
 * ================================================================ */

$sql_all = "
    SELECT
        t.id,
        t.name,
        t.of_name,
        t.road_class_id,
        t.type,
        c.code AS class_code,
        tt.country AS country_code,
        tt.descrip AS type_desc
    FROM trace AS t
    JOIN trace_class AS c ON t.road_class_id = c.id
    LEFT JOIN trace_t AS tt ON t.type = tt.id
";

$res2 = $dbi->query($sql_all);

$roads = [];
while ($row = mysqli_fetch_assoc($res2)) {
    $cid = (int)$row['road_class_id'];
    if (isset($classes[$cid])) {
        $roads[$cid][] = $row;
    }
}
mysqli_free_result($res2);

/* ================================================================
 * 3. Парсер дорожного кода
 * ================================================================ */
function parse_trace_number(string $code): array {
    if (preg_match('/([A-Za-zА-Яа-яЁё])\s*([0-9]+)/u', $code, $m)) {
        return [mb_strtoupper($m[1],'UTF-8'), (int)$m[2]];
    }
    return ['Z', 999999];
}

/* ================================================================
 * 4. Сортировка внутри групп — по of_name (он — код)
 * ================================================================ */

foreach ($roads as $cid => &$items) {
    usort($items, function($a, $b) {
        $A = trim($a['of_name'] ?: "");
        $B = trim($b['of_name'] ?: "");

        [$la,$na] = parse_trace_number($A);
        [$lb,$nb] = parse_trace_number($B);

        return [$la,$na] <=> [$lb,$nb];
    });
}
unset($items);


// -----------------------------
// Верхний текст страницы каталога
// -----------------------------

$title = "Дороги и трассы России и мира";

echo "<h1>$title</h1>";

// Подменю для классов дорог (вместо старой ленты ссылок)

echo '<div class="trace-subnav">';

$first = true;
foreach ($classes as $cid => $c) {
    if (!isset($roads[$cid])) continue;

    
    if (!$first) {
    echo '<span class="sep">·</span>';
}
$first = false;

echo '<span class="cls-item">'
   . '<a href="#cls-' . $cid . '">'
   . htmlspecialchars($c['name'])
   . '</a>'
   . '</span>';

}

echo '</div>';


echo "
<div class='trace-intro'>

<p>
Список автодорог России и <a href=\"#fc\">зарубежных стран</a> — от основных трасс до дорог местного значения.
Автодороги отсортированы по официальному номеру.<br>
Официальный номер трассы отражает её назначение:
</p>

<ul>
    <li><b>Е</b> — европейская сеть маршрутов, дороги, выходящие к границам европейских стран;</li>
    <li><b>М</b> — федеральные магистральные автодороги, соединяющие федеральные округа;</li>
    <li><b>А</b> — трассы административного назначения;</li>
    <li><b>Р</b> — дороги районного значения;</li>
</ul>

<p>
Кроме того, здесь представлены дороги и просёлочные участки без официального номера,
но пригодные для прокладки маршрута.
Для удобства в отдельную категорию вынесены <a href=\"#par\">паромы и понтонные мосты</a>.<br>
Перейдя по ссылке на трассу, можно ознакомиться с <b>легендой дороги</b>,
состоянием покрытия, придорожным сервисом и оставить отзыв.
</p>

</div>
";

/* ================================================================
 *  ПОИСК ТРАСС ПО ГОРОДУ — улучшенная логика (каталог trace)
 * ================================================================ */

$search_results  = [];
$search_query    = trim($_GET['townfind'] ?? '');
$matched_cities  = [];     // ID найденных городов
$city_names      = [];     // имена найденных городов

if ($search_query !== '') {

    $q = $dbi->real_escape_string($search_query);

    // Ищем ВСЕ городы, содержащие введённую строку
    // ORDER BY LENGTH(name) — самые релевантные (короткие) идут первыми
    $sqlTown = "
        SELECT id, name
        FROM towns
        WHERE hide = 0
          AND LOWER(name) LIKE LOWER('%{$q}%')
        ORDER BY LENGTH(name), name
        LIMIT 20
    ";

    $townRes = $dbi->query($sqlTown);

    while ($row = $townRes->fetch_assoc()) {
        $matched_cities[] = (int)$row['id'];
        $city_names[]     = $row['name'];
    }

    if ($matched_cities) {

        // Находим трассы по ВСЕМ найденным городам
        $sqlTr = "
            SELECT DISTINCT trace
            FROM far_tr
            WHERE town IN (" . implode(',', $matched_cities) . ")
        ";

        $rset = $dbi->query($sqlTr);

        $ids = [];
        while ($r = $rset->fetch_assoc()) {
            $ids[] = (int)$r['trace'];
        }

        // Загружаем трассы
        if ($ids) {
            $sqlRoads = "
                SELECT id, name, of_name, road_class_id, type
                FROM trace
                WHERE id IN (" . implode(',', $ids) . ")
            ";

            $roadRes = $dbi->query($sqlRoads);
            while ($row = $roadRes->fetch_assoc()) {
                $search_results[] = $row;
            }
        }
    }
}
?>



<div class="trace-search-block">
    <form method="GET" action="">
        <label for="townfind"><b>Найти трассы по городу</b></label>

        <input type="text"
               name="townfind"
               id="townfind"
               value="<?= htmlspecialchars($search_query) ?>"
               placeholder="Введите название города">

        <button type="submit">Поиск</button>
    </form>

    <p class="note-block" style="margin-top:8px;">
        Введите название города полностью или частично, чтобы увидеть трассы, проходящие через него.
    </p>
</div>


<?php
/* ================================================================
 *  ВЫВОД РЕЗУЛЬТАТОВ ПОИСКА
 * ================================================================ */

if ($search_query !== '') {

    // Заголовок: список всех подходящих городов
    if ($city_names) {
        echo "<h2>Трассы, проходящие через: &laquo;"
            . htmlspecialchars(implode(', ', $city_names))
            . "&raquo;</h2>";
    } else {
        echo "<h2>Город не найден</h2>";
    }

    // Вывод найденных трасс
    if ($search_results) {

        echo "<div class='trace-grid'>";

        foreach ($search_results as $r) {

            $cid   = (int)$r['road_class_id'];
            $ccode = $classes[$cid]['code'] ?? 'X';

            if (in_array($ccode, ['E','M','A','R','H'], true)) {
                echo card_big($r);
            }
            elseif (in_array($ccode, ['L','X'], true)) {
                echo card_small($r);
            }
            else {
                echo card_small($r);
            }
        }

        echo "</div>";

    } else {

        echo "<p class='note-block'>Трассы не найдены.</p>";
    }

    echo "<hr>";
}




/* ================================================================
 * 6. Карточки
 * ================================================================ */

function card_big($r) {
    $id = (int)$r['id'];

    $title = trim($r['of_name'] ?: "");
    $title = htmlspecialchars($title);

    $code_raw = trim($r['name'] ?: "");
    $code = $code_raw !== "" ? htmlspecialchars($code_raw) : "";

    $code_block  = $code ? "<div class='tc-name'>{$code}</div>" : "";
    $title_block = "<div class='tc-code'>{$title}</div>";

    return "
    <div class='trace-card big'>
        <a href='/trace.php/{$id}'>
            {$title_block}
            {$code_block}
        </a>
    </div>";
}

function card_small($r) {
    $id = (int)$r['id'];

    $title = htmlspecialchars($r['of_name'] ?: "");
    $code  = trim($r['name'] ?: "");
    $code_block = $code !== "" ? "<div class='tc-code'>".htmlspecialchars($code)."</div>" : "";

    return "
    <div class='trace-card small'>
        <a href='/trace.php/{$id}'>
            {$code_block}
            <div class='tc-sub'>{$title}</div>
        </a>
    </div>";
}

function card_mini($r) {
    $id = (int)$r['id'];

    $title = trim($r['name']);
    if ($title === "") $title = "Переправа";

    $title = htmlspecialchars($title);

    $sub = trim($r['of_name'] ?: "");
    $sub = $sub !== "" ? "<div class='tc-mini-sub'>".htmlspecialchars($sub)."</div>" : "";

    return "
    <div class='trace-card mini'>
        <a href='/trace.php/{$id}'>
            <div class='tc-mini-title'>{$title}</div>
            {$sub}
        </a>
    </div>";
}

/* ================================================================
 * 7. Новый порядок секций:
 *
 * E, M, A, R, H, L
 * X (Прочие)
 * D, P
 * F (Иностранные) — последним
 * ================================================================ */

$order_normal = [];
$order_x = [];
$order_pd = [];
$order_f = [];

foreach ($classes as $cid => $c) {
    if (!isset($roads[$cid])) continue;

    $cc = strtoupper($c['code']);

    if ($cc === 'F') {
        $order_f[$cid] = $c;
    }
    elseif ($cc === 'X') {
        $order_x[$cid] = $c;
    }
    elseif ($cc === 'P' || $cc === 'D') {
        $order_pd[$cid] = $c;
    }
    else {
        $order_normal[$cid] = $c;
    }
}

$ordered = $order_normal + $order_x + $order_pd + $order_f;

/* ================================================================
 * 8. Рендер
 * ================================================================ */

foreach ($ordered as $cid => $c) {

    echo "<section id='cls-$cid' class='trace-block'>";
    echo "<h2>" . htmlspecialchars($c['name']) . "</h2>";

    if ($c['description']) {
        echo "<p class='note-block'>" . htmlspecialchars($c['description']) . "</p>";
    }

    $cc = strtoupper($c['code']);

    /* ----------------------------- F: иностранные ----------------------------- */
    if ($cc === 'F') {

        $byCountry = [];

        foreach ($roads[$cid] as $r) {
            $cCode = $r['country_code'] ?: 'OTHER';

            if (!isset($byCountry[$cCode])) {
                $byCountry[$cCode] = [
                    'label' => $cCode,
                    'roads' => []
                ];
            }
            $byCountry[$cCode]['roads'][] = $r;
        }

        ksort($byCountry);

        foreach ($byCountry as $cCode => $block) {
            echo "<h3>" . htmlspecialchars($block['label']) . "</h3>";
            echo "<div class='trace-grid'>";
            foreach ($block['roads'] as $r) echo card_big($r);
            echo "</div>";
        }

        echo "</section>";
        continue;
    }

    /* ----------------------------- обычные секции ----------------------------- */

    echo "<div class='trace-grid'>";

    foreach ($roads[$cid] as $r) {

        if (in_array($cc, ['E','M','A','R','H'], true)) {
            echo card_big($r);
        }
        elseif (in_array($cc, ['L','X'], true)) {
            echo card_small($r);
        }
        elseif (in_array($cc, ['P','D'], true)) {
            echo card_mini($r);
        }
        else {
            echo card_small($r);
        }
    }

    echo "</div>";
    echo "</section>";
}

?>
<?php
/* ================================================================
 * JSON-LD: ItemList для каталога трасс
 * ================================================================ */

// собираем все уникальные трассы в один плоский список
$itemList = [];
$pos = 1;

foreach ($ordered as $cid => $c) {
    if (!isset($roads[$cid])) continue;

    foreach ($roads[$cid] as $r) {
        $id = (int)$r['id'];
        if (!$id) continue;

        $itemList[] = [
            "@type"    => "ListItem",
            "position" => $pos++,
            "url"      => "https://autotravel.ru/trace.php/$id"
        ];
    }
}

// выводим JSON-LD, если есть элементы
if ($itemList) {
    echo "\n<script type=\"application/ld+json\">\n";
    echo json_encode([
        "@context"         => "https://schema.org",
        "@type"            => "ItemList",
        "name"             => "Каталог автомобильных трасс России и мира",
        "itemListElement"  => $itemList
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    echo "\n</script>\n";
}
?>

