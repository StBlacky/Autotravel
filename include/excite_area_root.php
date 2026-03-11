<?php
// expects from excite_area.php:
// $dbi, $area (assoc), $area_id (int), $area_slug (string)
// and includes already loaded: config/func/func_bs/head00.php

function ea_h($s){ return htmlspecialchars((string)$s, ENT_QUOTES | ENT_HTML401, 'UTF-8'); }

$perPage = 300;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// 301: ?page=1 -> canonical без параметра
if ($page === 1 && isset($_GET['page'])) {
  $u = "/excite_area.php/".$area_slug;
  Header("HTTP/1.1 301 Moved Permanently");
  Header("Location: $u");
  exit;
}

$areaname_r = trim((string)($area['name_r'] ?? $area['name'] ?? ''));

$title_h = "Достопримечательности ".$areaname_r;
if ($page > 1) $title_h .= " — страница ".$page;

$title     = $title_h." — что посмотреть";
$metatags  = $title_h;
$metadesc  = $title_h;

// canonical
$canon = "/excite_area.php/".$area_slug;
$canonical = ($page > 1) ? ($canon."?page=".$page) : $canon;

// map mode
if ($_gmap_mode==9) $dispgmap = 0;
elseif($_gmap_mode==1) $dispgmap = 1;
elseif($_gmap_mode==2) $dispgmap = 2;
else $dispgmap = 3;

include("include/header_bs.php");


// breadcrumbs
echo breadcrumbs([
  'area' => $area_id,
  'excite_area' => true,
  'excite_area_url' => "/excite_area.php/".$area_slug
]);

echo "<h1>".ea_h($title_h)."</h1>";

// ===== MAP PANEL (unified) =====
if ($dispgmap) {

  $mapiframe = "
<div id='at-map-filter' class='note-block' style='margin-bottom:10px; display:none;'>
  <strong>Фильтр карты:</strong>
  <div id='at-map-types' style='margin-top:6px;'></div>
  <hr style='margin:10px 0;'>
  <div id='at-map-groups'></div>
  <label style='margin-left:15px'>
    <input type='checkbox' id='at-toggle-group-colors'>
    Показывать группы цветом
  </label>
</div>

<div id='gmapv3' style='display:none;'>&nbsp;</div>
";

  echo "<div class='row'><div class='col-xs-12'>";
  echo show_panel("Интерактивная карта","h2",$mapiframe,1,"areamap");
  echo "</div></div>";

  // ВАЖНО: задаём переменные прямо в HTML, чтобы не зависеть от $customscripttop
  $jsonUrl = "/kml/area_map_json.php/".$area_id."?etype=1";

  echo "<script>
  window.atMapMode = ".(int)$dispgmap.";
  window.atMapJsonUrl = ".json_encode($jsonUrl).";
  window.atMapAllowedTypes = [0,1];
  window.atMapTypeLabels = ".json_encode([0=>'Города (есть объекты без координат)', 1=>'Достопримечательности'], JSON_UNESCAPED_UNICODE).";
  window.atMapInitDone = false;
  </script>";

  // скрываем блок типов (но DOM оставляем, иначе фильтры не построятся)
  echo "<style>#at-map-types{ display:none !important; }</style>";

  // подключаем JS карты
  echo '<script src="/js/at-map-unified.js?v=3"></script>';

  // автозапуск карты (если панель открыта) + запуск при раскрытии
  echo "<script>
  (function(){
    function start(){
      var p = document.getElementById('areamap');
      if(!p) return;

      var style = window.getComputedStyle(p);
      if(style && style.display === 'none') return;

      var f = document.getElementById('at-map-filter');
      if(f) f.style.display = 'block';

      if(typeof atMapInit === 'function') atMapInit();
    }

    if(document.readyState === 'loading'){
      document.addEventListener('DOMContentLoaded', start);
    } else {
      start();
    }

    if(window.jQuery){
      jQuery(function($){
        $('#areamap').on('shown.bs.collapse', start);
      });
    }
  })();
  </script>";
}

// ===== HUBS (как в area.php) =====
include_once(__DIR__ . "/area_hubs.php");
echo "<div class='row'><div class='col-xs-12'>";
echo area_hub_render($area_id);
echo "</div></div>";

// ===== TABLE (paged) =====
$limit  = (int)($perPage + 1);
$offset = (int)(($page - 1) * $perPage);

// LIMIT/OFFSET числами (надёжнее, чем placeholders)
$sql = "
  SELECT e.id, e.town, e.name, e.wrate, e.votes, e.mainid,
         t.name AS town_name, t.url AS town_url
  FROM exciting e
  LEFT JOIN towns t ON t.id = e.town
  WHERE e.area = ?
    AND e.type = 1
    AND e.mainid <= 0
  ORDER BY e.wrate DESC, e.votes DESC, e.id DESC
  LIMIT $limit OFFSET $offset
";

$stmt = $dbi->prepare($sql);
if (!$stmt) {
  echo "<pre style='white-space:pre-wrap;background:#fee;padding:10px;'>SQL prepare error: ".ea_h($dbi->error)."</pre>";
  echo "</div>";
  include("include/footer_bs.php");
  exit;
}
$stmt->bind_param("i", $area_id);
if (!$stmt->execute()) {
  echo "<pre style='white-space:pre-wrap;background:#fee;padding:10px;'>SQL execute error: ".ea_h($stmt->error)."</pre>";
  $stmt->close();
  echo "</div>";
  include("include/footer_bs.php");
  exit;
}
$res = $stmt->get_result();

$items = [];
while ($row = $res->fetch_assoc()) $items[] = $row;
$stmt->close();

$hasMore = (count($items) > $perPage);
if ($hasMore) array_pop($items);

// сортировка: стрелочки
echo "<style>
#excite-area-table th.at-sortable{ position:relative; padding-right:18px; }
#excite-area-table th.at-sortable:after{
  content:'▴▾'; position:absolute; right:6px; top:50%;
  transform:translateY(-50%); color:#bbb; font-size:10px;
}
#excite-area-table th.at-sortable.sort-asc:after{ content:'▴'; color:#333; }
#excite-area-table th.at-sortable.sort-desc:after{ content:'▾'; color:#333; }
</style>";

if (!$items) {
  echo "<div class='alert alert-info'>Пока нет объектов для отображения.</div>";
  echo "</div>";
  include("include/footer_bs.php");
  exit;
}

// town filter select (клиентский, по текущей странице)
$towns = [];
foreach ($items as $r) {
  $tid = (int)$r['town'];
  if ($tid <= 0) continue;
  if (!isset($towns[$tid])) {
    $towns[$tid] = (string)($r['town_name'] ?? '');
  }
}
asort($towns, SORT_STRING);

echo "<div class='row' style='margin:6px 0 10px;'><div class='col-xs-12'>";
echo "<label class='travell0' style='margin-right:8px;'>Город:</label>";
echo "<select id='excite-town-filter' class='form-control' style='display:inline-block; width:auto; max-width:100%;'>";
echo "<option value=''>Все</option>";
foreach ($towns as $tid => $tname) {
  echo "<option value='".(int)$tid."'>".ea_h($tname)."</option>";
}
echo "</select>";
echo "</div></div>";

echo "<table id='excite-area-table' class='table table-hover tdc' style='width:100%'>";
echo "<thead><tr>";
echo "<th class='at-sortable' data-sort='text'>Объект</th>";
echo "<th class='at-sortable' data-sort='text' style='width:180px;'>Город</th>";
echo "<th class='at-sortable' data-sort='num'  style='width:120px;'>Рейтинг</th>";
echo "<th class='at-sortable' data-sort='num'  style='width:120px;'>Отклики</th>";
echo "</tr></thead><tbody>";

foreach ($items as $r) {
  $id     = (int)$r['id'];
  $name   = (string)$r['name'];
  $wr     = sprintf("%01.2f", (float)$r['wrate']);
  $vv     = (int)$r['votes'];
  $mainid = (int)$r['mainid'];

  $href = ($mainid >= 0) ? "/otklik.php/$id" : "/group.php/$id";

  $town_id = (int)$r['town'];
  $town_html = "&nbsp;";
  if ($town_id > 0) {
    $town_name = (string)($r['town_name'] ?? '');
    $town_url  = (string)($r['town_url'] ?? '');
    $town_href = $town_url ? ("/gorod/".$town_url.".html") : ("/town.php/".$town_id);
    $town_html = "<a class='travell5n' href='".ea_h($town_href)."'>".ea_h($town_name)."</a>";
  }

  echo "<tr data-town-id='".$town_id."'>";
  echo "<td><a class='travell5n' href='".ea_h($href)."'>".ea_h($name)."</a></td>";
  echo "<td>".$town_html."</td>";
  echo "<td><b>".$wr."</b></td>";
  echo "<td>".$vv."</td>";
  echo "</tr>";
}

echo "</tbody></table>";

// показать ещё
if ($hasMore) {
  $next = $page + 1;
  $nextUrl = $canon . "?page=" . $next;
  echo "<div style='margin:10px 0 20px;'>";
  echo "<a class='btn btn-default' href='".ea_h($nextUrl)."'>Показать ещё</a>";
  echo "</div>";
}

// JS сортировки + город-фильтр
echo "<script>
(function(){
  function getCellText(td){
    if(!td) return '';
    return (td.textContent || td.innerText || '').trim();
  }
  function parseNum(s){
    s = (s||'').toString().replace(/\\s+/g,'').replace(',', '.');
    var v = parseFloat(s);
    return isNaN(v) ? 0 : v;
  }
  function sortTable(table, colIdx, dir){
    var tbody = table.tBodies[0];
    var rows = Array.prototype.slice.call(tbody.rows, 0);

    var ths = table.tHead ? table.tHead.rows[0].cells : [];
    var mode = ths[colIdx] ? (ths[colIdx].getAttribute('data-sort') || 'text') : 'text';

    rows.sort(function(a,b){
      var ta = getCellText(a.cells[colIdx]);
      var tb = getCellText(b.cells[colIdx]);

      if(mode === 'num'){
        var na = parseNum(ta), nb = parseNum(tb);
        return dir * (na - nb);
      } else {
        try { return dir * ta.localeCompare(tb, 'ru', {sensitivity:'base'}); }
        catch(e){ return dir * (ta > tb ? 1 : (ta < tb ? -1 : 0)); }
      }
    });

    for(var i=0;i<rows.length;i++) tbody.appendChild(rows[i]);
  }

  function initSortableTable(id){
    var table = document.getElementById(id);
    if(!table || !table.tHead || !table.tHead.rows.length) return;

    var headers = table.tHead.rows[0].cells;

    for(let i=0;i<headers.length;i++){
      let th = headers[i];
      th.style.cursor = 'pointer';
      th.setAttribute('data-dir','0');

      th.addEventListener('click', function(){
        for(var j=0;j<headers.length;j++){
          headers[j].setAttribute('data-dir','0');
          headers[j].classList.remove('sort-asc','sort-desc');
        }
        var cur = parseInt(th.getAttribute('data-dir') || '0', 10);
        var next = (cur === 1) ? -1 : 1;
        th.setAttribute('data-dir', String(next));
        th.classList.add(next === 1 ? 'sort-asc' : 'sort-desc');
        sortTable(table, i, next);
      });
    }
  }

  function initTownFilter(){
    var sel = document.getElementById('excite-town-filter');
    var table = document.getElementById('excite-area-table');
    if(!sel || !table) return;

    sel.addEventListener('change', function(){
      var v = sel.value;
      var rows = table.tBodies[0].rows;
      for(var i=0;i<rows.length;i++){
        var tid = String(rows[i].getAttribute('data-town-id') || '');
        rows[i].style.display = (!v || tid === String(v)) ? '' : 'none';
      }
    });
  }

  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', function(){
      initSortableTable('excite-area-table');
      initTownFilter();
    });
  } else {
    initSortableTable('excite-area-table');
    initTownFilter();
  }
})();
</script>";

echo "</div>";
include("include/footer_bs.php");