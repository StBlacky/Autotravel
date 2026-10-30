<?php
// include/area_hubs.php
// Хаб достопримов области — верхний уровень (группы desc_slug: architecture/nature/museums/...)
// + первая карточка: "Все достопримечательности {области} (N)"
// ВХОД: $a (id области), соединение берём: $dbi из $GLOBALS

if (!function_exists('area_hub_render')) {

  function area_hub_render(int $a): string {

    if ($a == 0) return "<!-- area_hub: invalid area id -->";

    if (!isset($GLOBALS['dbi']) || !($GLOBALS['dbi'] instanceof mysqli)) {
      return "<div class='alert alert-danger'>area_hub: нет соединения с БД (\$dbi)</div>";
    }
    $dbi = $GLOBALS['dbi'];

    // Берём slug области + name_r (нужен для URL и для SEO-названия)
    $area_slug = '';
    $area_name_r = '';
    $area_name = '';

    $stmt = $dbi->prepare("SELECT slug, name_r, name FROM areas WHERE id=? LIMIT 1");
    if (!$stmt) {
      return "<div class='alert alert-danger'>area_hub: prepare error (areas)</div>";
    }
    $stmt->bind_param("i", $a);
    $stmt->execute();
    $stmt->bind_result($area_slug, $area_name_r, $area_name);
    $stmt->fetch();
    $stmt->close();

    $area_slug = trim((string)$area_slug);
    $area_name_r = trim((string)($area_name_r ?: $area_name));

    if ($area_slug === '') {
      return "<!-- area_hub: no area slug -->";
    }

    // Счётчик для root: как в таблице "все достопримечательности области"
    $all_cnt = 0;
    $stmt = $dbi->prepare("
      SELECT COUNT(*)
      FROM exciting
      WHERE area = ?
        AND type = 1
        AND mainid <= 0
    ");
    if ($stmt) {
      $stmt->bind_param("i", $a);
      $stmt->execute();
      $stmt->bind_result($all_cnt);
      $stmt->fetch();
      $stmt->close();
    }
    $all_cnt = (int)$all_cnt;

    // Группы: считаем уникальные объекты по desc_slug/desc
    // icon берём "любой" из группы (обычно одинаковый внутри группы)
    // Сортировка: по алфавиту (te.`desc`)
    $sql = "
      SELECT 
        te.desc_slug,
        te.`desc`,
        MIN(te.icon) AS icon,
        COUNT(DISTINCT g.exid) AS cnt
      FROM exbytype g
      JOIN type_ex te ON te.id = g.extyp
      WHERE g.area = $a
        AND te.desc_slug IS NOT NULL
        AND te.desc_slug <> ''
      GROUP BY te.desc_slug, te.`desc`
      HAVING cnt > 0
      ORDER BY te.`desc` ASC
    ";

    $res = $dbi->query($sql);
    if (!$res) {
      return "<div class='alert alert-danger'>area_hub: SQL error</div>";
    }

    $items = [];
    while ($row = mysqli_fetch_assoc($res)) {
      $items[] = $row;
    }
    mysqli_free_result($res);

    if (!$items) {
      return "<div class='alert alert-warning'>Нет данных по группам достопримов</div>";
    }

    $out = "<div class='area-hub'><div class='area-hub-grid'>";

    // --- первая карточка: все достопримечательности области ---
    $root_url  = "/excite_area.php/" . $area_slug;
    $root_name = "Все интересное " . $area_name_r;
    $root_name_html = htmlspecialchars($root_name, ENT_QUOTES | ENT_HTML401, 'UTF-8');

    $out .= "<a href='{$root_url}' class='travell5n area-hub-card'>";
    $out .=   "<span><span class='area-hub-title'>{$root_name_html} ({$all_cnt})</span></span>";
    $out .= "</a>";

    // --- остальные карточки: группы ---
    foreach ($items as $it) {
      $url  = "/excite_area.php/" . $area_slug . "/" . rawurlencode($it['desc_slug']);
      $name = htmlspecialchars((string)$it['desc'], ENT_QUOTES | ENT_HTML401, 'UTF-8');
      $ico  = trim((string)$it['icon']);

      $img = $ico ? "<img src='/jpg/{$ico}' alt=''>" : "";

      $out .= "<a href='{$url}' class='travell5n area-hub-card'>";
      $out .=   "<span class='area-hub-ico'>{$img}</span>";
      $out .=   "<span><span class='area-hub-title'>{$name}</span></span>";
      $out .= "</a>";
    }

    $out .= "</div></div>";

    return $out;
  }
}