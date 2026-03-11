<?php
require_once __DIR__ . '/../include/config.php';
require_once __DIR__ . '/../include/func.php';

header('Content-Type: application/json; charset=utf-8');

/* ========= ПАРСИНГ a (ЧПУ + ?a=) ========= */

$a = 0;

if (isset($_GET['a'])) {
    $a = (int)$_GET['a'];
}

if (!$a && isset($_SERVER['REQUEST_URI'])) {
    if (preg_match('~/area_map_json\.php/(-?\d+)~', $_SERVER['REQUEST_URI'], $m)) {
        $a = (int)$m[1];
    }
}

if ($a == 0) {
    echo json_encode(['items' => []], JSON_UNESCAPED_UNICODE);
    exit;
}

/* ========= ФИЛЬТР ПО ТИПУ (exciting.type) =========
   etype=1 => только достопримечательности
   etype=0/нет => без фильтра (как раньше)
*/
$etype = isset($_GET['etype']) ? (int)$_GET['etype'] : 0;

/* ========= БД ========= */

$dbi = new mysqli($dbhost, $dbuser, $dbpasswd, $dbname);
if ($dbi->connect_errno) {
    echo json_encode(['items' => []], JSON_UNESCAPED_UNICODE);
    exit;
}
$dbi->query("SET NAMES 'utf8'");

/* ========= СПРАВОЧНИК ГРУПП (как в kml) ========= */

$at_grp_map = [];

$res_tx = $dbi->query("
    SELECT `desc` AS grp_name, `desc_slug`, `icon`
    FROM type_ex
    WHERE `desc` IS NOT NULL AND `desc` <> ''
");

if ($res_tx) {
    while ($row = $res_tx->fetch_assoc()) {
        $name = trim($row['grp_name']);
        if ($name === '') continue;

        $key = mb_strtolower($name, 'UTF-8');

        $at_grp_map[$key] = [
            'slug' => trim($row['desc_slug']),
            'icon' => trim($row['icon'])
        ];
    }
    $res_tx->free();
}

function at_exgrp_name($raw) {
    $raw = trim((string)$raw);
    if ($raw === '') return '';
    return preg_replace('/^\s*\d+\s*\.\s*/u', '', $raw);
}

/* ========= ОБЪЕКТЫ С КООРДИНАТАМИ ========= */

$items = [];

$sql = "
SELECT 
    e.id,
    e.name,
    e.descrip,
    e.type,
    e.Nshirota,
    e.Edolgota,
    g.exgrp
FROM exciting e
LEFT JOIN (
    SELECT exid, MIN(exgrp) AS exgrp
    FROM exbygroup
    GROUP BY exid
) g ON g.exid = e.id
WHERE 
    e.area = ?
    AND e.mainid >= 0
    AND e.Nshirota <> 0
    AND e.Edolgota <> 0
";

if ($etype > 0) {
    $sql .= " AND e.type = ? ";
}

$stmt = $dbi->prepare($sql);
if (!$stmt) {
    echo json_encode(['items' => []], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($etype > 0) $stmt->bind_param("ii", $a, $etype);
else $stmt->bind_param("i", $a);

$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {

    $grp_raw  = $row['exgrp'] ?? '';
    $grp_name = at_exgrp_name($grp_raw);
    $grp_key  = $grp_name !== '' ? mb_strtolower($grp_name, 'UTF-8') : '';

    $grp_slug = null;
    $grp_icon = null;

    if ($grp_key && isset($at_grp_map[$grp_key])) {
        $grp_slug = $at_grp_map[$grp_key]['slug'] ?: null;
        $grp_icon = $at_grp_map[$grp_key]['icon'] ?: null;
    }

    $items[] = [
        'id'          => (int)$row['id'],
        'lat'         => (float)$row['Nshirota'],
        'lng'         => (float)$row['Edolgota'],
        'name'        => $row['name'],
        'description' => $row['descrip'],
        'url'         => "/otklik.php/" . $row['id'],
        'etype'       => (int)$row['type'],
        'grpSlug'     => $grp_slug,
        'grpName'     => $grp_name ?: null,
        'grpIcon'     => $grp_icon,
    ];
}

$stmt->close();

/* ========= ГОРОДА (etype = 0)
   ВАЖНО: если задан etype (например etype=1),
   то города показываем ТОЛЬКО если есть объекты БЕЗ координат этого типа (type=1),
   чтобы не попадали гостиницы/кафе.
========= */

$sql_towns = "
SELECT t.id, t.name, t.Nshirota, t.Edolgota
FROM towns t
WHERE 
    t.area = ?
    AND t.hide <> 1
    AND t.Nshirota <> 0
    AND t.Edolgota <> 0
    AND EXISTS (
        SELECT 1
        FROM exciting e2
        WHERE 
            e2.town = t.id
            AND e2.mainid >= 0
            AND e2.Nshirota = 0
            AND e2.Edolgota = 0
";

if ($etype > 0) {
    $sql_towns .= " AND e2.type = ? ";
}

$sql_towns .= "
    )
";

$stmt = $dbi->prepare($sql_towns);
if (!$stmt) {
    echo json_encode(['items' => $items], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($etype > 0) $stmt->bind_param("ii", $a, $etype);
else $stmt->bind_param("i", $a);

$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {

    $list = [];

    $sql2 = "
        SELECT name
        FROM exciting
        WHERE town = ?
          AND mainid >= 0
          AND Nshirota = 0
          AND Edolgota = 0
    ";

    if ($etype > 0) {
        $sql2 .= " AND type = ? ";
    }

    $stmt2 = $dbi->prepare($sql2);
    if ($etype > 0) $stmt2->bind_param("ii", $row['id'], $etype);
    else $stmt2->bind_param("i", $row['id']);

    $stmt2->execute();
    $res2 = $stmt2->get_result();

    while ($r2 = $res2->fetch_assoc()) {
        $list[] = $r2['name'];
    }
    $stmt2->close();

    $descr = "";
    if ($list) {
        $descr = "На сайт занесены:<br>" . implode("<br>", $list);
    }

    $items[] = [
        'id'          => (int)$row['id'],
        'lat'         => (float)$row['Nshirota'],
        'lng'         => (float)$row['Edolgota'],
        'name'        => $row['name'],
        'description' => $descr,
        'url'         => "/town.php/" . $row['id'],
        'etype'       => 0
    ];
}

$stmt->close();

echo json_encode(['items' => $items], JSON_UNESCAPED_UNICODE);