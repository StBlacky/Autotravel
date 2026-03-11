<?php
include_once('include/config.php');
include_once('include/func.php');
include_once('include/func_bs.php');
$bootstrap = 1;
include_once('include/head00.php');

/**
 * URL:
 *  /excite_area.php/{area-slug}
 *  /excite_area.php/{area-slug}/{desc_slug}
 *  /excite_area.php/{area-slug}/{desc_slug}/{type_slug}
 * Без завершающего слэша.
 */

if (!isset($customscripttop)) $customscripttop = '';
if (!isset($customscript)) $customscript = '';

/* ---------------- helpers ---------------- */
function excite_safe(string $s): string {
  return htmlspecialchars($s, ENT_QUOTES | ENT_HTML401, 'UTF-8');
}

/* ---------------- 1) parse URL ---------------- */
$area_slug = '';
$desc_slug = '';
$type_slug = '';
$garbage   = '';

// 1) prefer internal $_pin if exists
if (isset($_pin) && preg_match("~^/([a-z0-9_-]+)(?:/([a-z0-9_-]+))?(?:/([a-z0-9_-]+))?(.*)$~i", (string)$_pin, $m)) {
  $area_slug = strtolower($m[1] ?? '');
  $desc_slug = strtolower($m[2] ?? '');
  $type_slug = strtolower($m[3] ?? '');
  $garbage   = $m[4] ?? '';
} else {
  // fallback (old query params)
  $area_slug = isset($_REQUEST['area']) ? mysafestring($_REQUEST['area']) : '';
  $desc_slug = isset($_REQUEST['g'])    ? mysafestring($_REQUEST['g'])    : '';
  $type_slug = isset($_REQUEST['t'])    ? mysafestring($_REQUEST['t'])    : '';
}

$area_slug = trim($area_slug);
$desc_slug = trim($desc_slug);
$type_slug = trim($type_slug);

// 2) HARD fallback: parse REQUEST_URI (works even if $_pin is empty)
if ($area_slug === '' && !empty($_SERVER['REQUEST_URI'])) {
  $u = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
  if (preg_match('~^/excite_area\.php/([a-z0-9_-]+)(?:/([a-z0-9_-]+))?(?:/([a-z0-9_-]+))?(.*)$~i', (string)$u, $m)) {
    $area_slug = strtolower($m[1] ?? '');
    $desc_slug = strtolower($m[2] ?? '');
    $type_slug = strtolower($m[3] ?? '');
    $garbage   = $m[4] ?? '';
  }
}

if (!$area_slug) {
  Header("HTTP/1.1 404 Not Found");
  include("include/header_bs.php");
  echo "<div class='container-fluid'><h1>Страница не найдена</h1></div>";
  include("include/footer_bs.php");
  exit;
}

// canonical cleanup: any garbage (including trailing slash) => 301
if ($garbage) {
  $newuri = "/excite_area.php/".$area_slug
          . ($desc_slug ? "/".$desc_slug : "")
          . ($type_slug ? "/".$type_slug : "");
  Header("HTTP/1.1 301 Moved Permanently");
  Header("Location: $newuri");
  exit;
}

/* ---------------- 2) area by slug ---------------- */
$stmt = $dbi->prepare("SELECT id, slug, name, name_r, name_v, meta_title, meta_description FROM areas WHERE slug=? LIMIT 1");
$stmt->bind_param("s", $area_slug);
$stmt->execute();
$res = $stmt->get_result();
$area = $res ? $res->fetch_assoc() : null;
$stmt->close();

if (!$area) {
  Header("HTTP/1.1 404 Not Found");
  include("include/header_bs.php");
  echo "<div class='container-fluid'><h1>Область не найдена</h1></div>";
  include("include/footer_bs.php");
  exit;
}
$area_id   = (int)$area['id'];
$area_name = (string)$area['name'];
$area_name_r = trim((string)($area['name_r'] ?: $area_name));

/* ---------------- pagination (common) ---------------- */
$perPage = 300;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

/* =========================================================
   ROOT MODE: /excite_area.php/{area-slug}
   карта + хабы + таблица (paged)
   ========================================================= */
if ($desc_slug === '') {

  // 301: ?page=1 -> clean url
  if ($page === 1 && isset($_GET['page'])) {
    $u = "/excite_area.php/".$area_slug;
    Header("HTTP/1.1 301 Moved Permanently");
    Header("Location: $u");
    exit;
  }

  $canon_base = "/excite_area.php/".$area_slug;
  $canonical = ($page > 1) ? ($canon_base."?page=".$page) : $canon_base;

  $title_h = "Достопримечательности ".$area_name_r;
  if ($page > 1) $title_h .= " — страница ".$page;

  $title    = $title_h." — что посмотреть";
  $metatags = $title_h;
  $metadesc = $title_h;

  // map mode
  if ($_gmap_mode==9) $dispgmap = 0;
  elseif($_gmap_mode==1) $dispgmap = 1;
  elseif($_gmap_mode==2) $dispgmap = 2;
  else $dispgmap = 3;

  include("include/header_bs.php");

  echo "<div class='container-fluid' style='width:100%'>";

  // root crumbs: область -> Достопримечательности (как текущая)
  echo breadcrumbs([
    'area' => $area_id,
    'excite_area' => true
  ]);

  echo "<h1>".excite_safe($title_h)."</h1>";

  // MAP (unified)
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

    $jsonUrl = "/kml/area_map_json.php/".$area_id."?etype=1";

    echo "<style>#at-map-types{ display:none !important; }</style>";
    echo "<script>
      window.atMapMode = ".(int)$dispgmap.";
      window.atMapJsonUrl = ".json_encode($jsonUrl).";
      window.atMapAllowedTypes = [0,1];
      window.atMapTypeLabels = ".json_encode([0=>'Города (есть объекты без координат)', 1=>'Достопримечательности'], JSON_UNESCAPED_UNICODE).";
      window.atMapInitDone = false;
    </script>";
    echo '<script src="/js/at-map-unified.js?v=3"></script>';
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

        if(document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start);
        else start();

        if(window.jQuery){
          jQuery(function($){
            $('#areamap').on('shown.bs.collapse', start);
          });
        }
      })();
    </script>";
  }

  // HUBS (1:1 как area.php)
  include_once(__DIR__ . "/include/area_hubs.php");
  echo "<div class='row'><div class='col-xs-12'>";
  echo area_hub_render($area_id);
  echo "</div></div>";

  // TABLE (paged) - only top-level: mainid <= 0
  $limit  = (int)($perPage + 1);
  $offset = (int)(($page - 1) * $perPage);

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
    echo "<pre style='white-space:pre-wrap;background:#fee;padding:10px;'>SQL prepare error: ".excite_safe($dbi->error)."</pre>";
    echo "</div>";
    include("include/footer_bs.php");
    exit;
  }
  $stmt->bind_param("i", $area_id);
  $stmt->execute();
  $res = $stmt->get_result();

  $items = [];
  while ($r = $res->fetch_assoc()) $items[] = $r;
  $stmt->close();

  $hasMore = (count($items) > $perPage);
  if ($hasMore) array_pop($items);

  if (empty($items)) {
    echo "<div class='alert alert-info'>Пока нет объектов для отображения.</div>";
    echo "</div>";
    include("include/footer_bs.php");
    exit;
  }

  $ea_items   = $items;
  $ea_hasMore = $hasMore;
  $ea_page    = $page;
  $ea_baseUrl = $canon_base;

  include __DIR__ . "/include/excite_area_table.php";

  echo "</div>";
  include("include/footer_bs.php");
  exit;
}

/* =========================================================
   SLUG MODES: /{area}/{desc} and /{area}/{desc}/{type}
   ========================================================= */

/* ---------------- 3) group by desc_slug ---------------- */
$stmt = $dbi->prepare("SELECT `desc`, desc_slug, icon FROM type_ex WHERE desc_slug=? LIMIT 1");
$stmt->bind_param("s", $desc_slug);
$stmt->execute();
$res = $stmt->get_result();
$grp = $res ? $res->fetch_assoc() : null;
$stmt->close();

if (!$grp) {
  Header("HTTP/1.1 404 Not Found");
  include("include/header_bs.php");
  echo "<div class='container-fluid'><h1>Категория не найдена</h1></div>";
  include("include/footer_bs.php");
  exit;
}
$group_title = (string)$grp['desc'];

/* ---------------- 4) subtype optional ---------------- */
$sub = null;
$sub_count = 0;

if ($type_slug) {
  $stmt = $dbi->prepare("SELECT id, slug, type, desc_slug FROM type_ex WHERE desc_slug=? AND slug=? LIMIT 1");
  $stmt->bind_param("ss", $desc_slug, $type_slug);
  $stmt->execute();
  $res = $stmt->get_result();
  $sub = $res ? $res->fetch_assoc() : null;
  $stmt->close();

  if ($sub) {
    $stmt = $dbi->prepare("SELECT COUNT(DISTINCT exid) FROM exbytype WHERE area=? AND extyp=?");
    $stmt->bind_param("is", $area_id, $sub['id']);
    $stmt->execute();
    $stmt->bind_result($sub_count);
    $stmt->fetch();
    $stmt->close();
  } else {
    $type_slug = '';
  }
}

$mode_sub = ($sub && $sub_count >= 5);

// 301: ?page=1 -> clean url
if ($page === 1 && isset($_GET['page'])) {
  $u = "/excite_area.php/".$area_slug."/".$desc_slug.($mode_sub ? "/".$type_slug : "");
  Header("HTTP/1.1 301 Moved Permanently");
  Header("Location: $u");
  exit;
}

/* ---------------- 5) canon + meta ---------------- */
$canon_base = "/excite_area.php/".$area_slug."/".$desc_slug.($mode_sub ? "/".$type_slug : "");
$canonical = ($page > 1) ? ($canon_base."?page=".$page) : $canon_base;

$title_h  = $mode_sub ? ($sub['type']." — ".$area_name) : ($group_title." — ".$area_name);
if ($page > 1) $title_h .= " — страница ".$page;

$title    = $title_h." — что посмотреть";
$metatags = $title_h;
$metadesc = strip_tags(str_replace('"', ' ', $title_h));

/* ---------------- 6) subtype chips (SEO-links) ---------------- */
$subtypes = [];

$stmt = $dbi->prepare("
  SELECT x.slug, x.type, COUNT(DISTINCT t.exid) AS cnt
  FROM type_ex x
  JOIN exbytype t ON t.extyp=x.id
  JOIN exciting e ON e.id=t.exid
  WHERE t.area=? AND x.desc_slug=? AND e.mainid<=0
  GROUP BY x.id
  HAVING cnt>=5
  ORDER BY x.type
");
$stmt->bind_param("is", $area_id, $desc_slug);
$stmt->execute();
$res = $stmt->get_result();
if ($res && $res->num_rows) {
  while ($r = $res->fetch_assoc()) $subtypes[] = $r;
}
$stmt->close();

/* ---------------- 7) list data (paged) ---------------- */
$items = [];
$limit  = (int)($perPage + 1);
$offset = (int)(($page - 1) * $perPage);

if ($mode_sub) {
  $sql = "SELECT DISTINCT e.id, e.town, e.name, e.wrate, e.votes, e.mainid,
                 tt.name AS town_name, tt.url AS town_url
          FROM exbytype t
          JOIN exciting e ON e.id=t.exid
          LEFT JOIN towns tt ON tt.id=e.town
          WHERE t.area=? AND t.extyp=? AND e.mainid<=0
          ORDER BY e.wrate DESC, e.votes DESC, e.id DESC
          LIMIT $limit OFFSET $offset";
  $stmt = $dbi->prepare($sql);
  $stmt->bind_param("is", $area_id, $sub['id']);
} else {
  $sql = "SELECT DISTINCT e.id, e.town, e.name, e.wrate, e.votes, e.mainid,
                 tt.name AS town_name, tt.url AS town_url
          FROM exbytype t
          JOIN type_ex x ON x.id=t.extyp
          JOIN exciting e ON e.id=t.exid
          LEFT JOIN towns tt ON tt.id=e.town
          WHERE t.area=? AND x.desc_slug=? AND e.mainid<=0
          ORDER BY e.wrate DESC, e.votes DESC, e.id DESC
          LIMIT $limit OFFSET $offset";
  $stmt = $dbi->prepare($sql);
  $stmt->bind_param("is", $area_id, $desc_slug);
}

$stmt->execute();
$res = $stmt->get_result();
while ($r = $res->fetch_assoc()) $items[] = $r;
$stmt->close();

$hasMore = (count($items) > $perPage);
if ($hasMore) array_pop($items);

/* ---------------- 8) map (old ids-based; current page only) ---------------- */
$mapiframe = "";
$flagresult = "";

foreach ($items as $r) {
  $id = (int)$r['id'];
  $flagresult = $flagresult ? ($flagresult.",".$id) : (string)$id;
}

if ($_gmap_mode==9) $dispgmap = 0;
elseif($_gmap_mode==1) $dispgmap = 1;
elseif($_gmap_mode==2) $dispgmap = 2;
else $dispgmap = 3;

if ($flagresult && $dispgmap) {
  $mapiframe .= "<div id=gmapv3 class=\"embed-responsive\" style=\"display:none;\">&nbsp;</div>";
  $customscripttop .= "function showmap(){ initmapv".$dispgmap."('/kml.php?ids=".$flagresult."',null,0,0); }\n";
  $customscripttop .= "$(function(){ if (typeof showmap==='function') showmap(); });\n";
}

include("include/header_bs.php");

/* ---------------- output ---------------- */
echo "<div class='container-fluid' style='width:100%'>";

// breadcrumbs (area -> Достопримечательности -> категория -> (подтип))
$bc = [
  'area' => $area_id,
  'excite_area' => true,
  'excite_area_url' => "/excite_area.php/".$area_slug,
  'category' => $group_title,
  'category_url' => $mode_sub ? ("/excite_area.php/".$area_slug."/".$desc_slug) : '', // category is current if no sub
];
if ($mode_sub) {
  $bc['subcategory'] = $sub['type'];
  $bc['subcategory_url'] = ''; // current
}
echo breadcrumbs($bc);

echo "<h1>".excite_safe($title_h)."</h1>";

/* subtype chips row */
echo "<div class='row' style='margin-bottom:6px;'><div class='col-xs-12'>";

if (!empty($subtypes)) {
  $cls_all = $mode_sub ? "btn btn-default btn-xs" : "btn btn-success btn-xs";
  echo "<a class='".$cls_all."' style='margin:0 6px 6px 0;' href='/excite_area.php/".$area_slug."/".$desc_slug."'>Все</a>";

  foreach ($subtypes as $st) {
    $slug = (string)$st['slug'];
    $type = (string)$st['type'];
    $cnt  = (int)$st['cnt'];
    $href = "/excite_area.php/".$area_slug."/".$desc_slug."/".$slug;

    $is_active = ($mode_sub && $type_slug && $slug === $type_slug);
    $cls = $is_active ? "btn btn-success btn-xs" : "btn btn-default btn-xs";

    echo "<a class='".$cls."' style='margin:0 6px 6px 0;' href='".excite_safe($href)."'>".excite_safe($type)." (".$cnt.")</a>";
  }
}
echo "</div></div>";

/* map panel */
if ($mapiframe) {
  echo "<div class='row'><div class='col-xs-12'>";
  echo show_panel("Интерактивная карта","h3",$mapiframe,1,"maphidden");
  echo "</div></div>";
}

/* table */
if (empty($items)) {
  echo "<div class='alert alert-info'>Пока нет объектов для отображения.</div>";
  echo "</div>";
  include("include/footer_bs.php");
  exit;
}

$ea_items   = $items;
$ea_hasMore = $hasMore;
$ea_page    = $page;
$ea_baseUrl = $canon_base;

include __DIR__ . "/include/excite_area_table.php";

echo "</div>";
include("include/footer_bs.php");