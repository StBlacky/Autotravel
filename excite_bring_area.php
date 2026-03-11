<?php
include_once('include/config.php');
include_once('include/func.php');
include_once('include/func_bs.php');
include_once('include/head00.php');

$bootstrap = 1;

// --- input ---
$aid = 0;

// старый формат: ?area=33
if (isset($_GET['area'])) {
  $aid = (int)$_GET['area'];
}

// новый формат: /excite_bring_area/{id|slug}/ -> rewrite в excite_bring_area.php/{id|slug}
$area_slug = "";
if (!$aid && !empty($_SERVER['PATH_INFO'])) {
  $pi = trim($_SERVER['PATH_INFO'], "/ \t\n\r\0\x0B");
  if ($pi !== '') {
    if (ctype_digit($pi)) {
      $aid = (int)$pi;
    } else {
      $area_slug = $dbi->real_escape_string($pi);
    }
  }
}

// если пришли по slug — получаем id
if (!$aid && $area_slug !== "") {
  $sql_aid = "SELECT id FROM areas WHERE slug = '".$area_slug."' LIMIT 1";
  $res_aid = $dbi->query($sql_aid);
  if ($res_aid && ($r = mysqli_fetch_assoc($res_aid))) {
    $aid = (int)$r['id'];
    mysqli_free_result($res_aid);
  } elseif ($res_aid) {
    mysqli_free_result($res_aid);
  }
}

if (!$aid) {
  header("HTTP/1.1 404 Not Found");
  $title = "Что привезти";
  $title_h = "Что привезти";
  $metatags = "что привезти";
  $metadesc = "Справочный раздел: что привезти из региона или страны.";
  include('include/header_bs.php');
  echo "<div class=\"container-fluid\" style=\"width:100%\"><h1>Что привезти</h1><p>Не указан регион/страна.</p></div>";
  $_totop = 1;
  include('include/footer_bs.php');
  exit;
}

// --- area name ---
$areaname = "";
$areaname_r = "";
$areaslug = "";
$bring_desc = "";

$adata = getareaall($aid);
if (is_array($adata)) {
  $areaname    = isset($adata[0]) ? $adata[0] : "";
  $areaname_r  = isset($adata[1]) ? $adata[1] : "";
  $areaslug    = isset($adata[6]) ? trim((string)$adata[6]) : "";
  $bring_desc  = isset($adata[7]) ? trim((string)$adata[7]) : "";
}

if (!$areaname) {
  header("HTTP/1.1 404 Not Found");
  $title = "Что привезти";
  $title_h = "Что привезти";
  $metatags = "что привезти";
  $metadesc = "Справочный раздел: что привезти из региона или страны.";
  include('include/header_bs.php');
  echo "<div class=\"container-fluid\" style=\"width:100%\"><h1>Что привезти</h1><p>Регион/страна не найдены.</p></div>";
  $_totop = 1;
  include('include/footer_bs.php');
  exit;
}
// --- 301 на канонический slug-URL (до любого вывода!) ---
if (!empty($areaslug)) {
  $target = "/excite_bring_area/".$areaslug."/";

  // сравниваем только путь без параметров
  $cur_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
  $cur_path = rtrim($cur_path, "/")."/";

  if ($cur_path !== $target) {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: ".$target);
    exit;
  }
}

// --- SEO ---
$menuz   = "Что привезти";
$title_h = "Что привезти из ".($areaname_r ? $areaname_r : $areaname);
$title   = $title_h;

$metatags = "что привезти, ".$areaname.", изделия";

// canonical: slug при наличии, иначе id
if ($areaslug !== "") {
  $canon = "/excite_bring_area/".$areaslug."/";
} else {
  $canon = "/excite_bring_area/".$aid."/";
}

if ($bring_desc !== "") {
  $metadesc = $bring_desc;
} else {
  $metadesc = "Что привезти из ".($areaname_r ? $areaname_r : $areaname).": список местных изделий и региональных специалитетов. Подборка по городам с переходом на карточки объектов.";
}





include('include/header_bs.php');




// --------------------------------------------------------------------
// справочник типов: type_ex.id -> desc
// --------------------------------------------------------------------
$typeDescById = array();
$sqlType = "SELECT id, `desc` FROM type_ex";
$resType = $dbi->query($sqlType);
if ($resType) {
  while ($r = mysqli_fetch_assoc($resType)) {
    $tid = trim((string)$r['id']); // id = char(3)
    $tdesc = trim((string)$r['desc']);
    if ($tid !== "") {
      $typeDescById[$tid] = $tdesc;
    }
  }
  mysqli_free_result($resType);
}

// --------------------------------------------------------------------
// выборка изделий (ВАЖНО: ffile)
// --------------------------------------------------------------------
$sql = "
SELECT
  e.id,
  e.name,
  e.descrip,
  e.town,
  e.kind,
  e.ffile
FROM exciting e
LEFT JOIN towns t ON t.id = e.town
WHERE
  e.area = $aid
  AND e.type = 5
  AND (e.mainid IS NULL OR e.mainid <= 0)
ORDER BY
  CASE WHEN e.town = 0 THEN 1 ELSE 0 END,  -- 'Без города' в конец
  t.name,                                  -- города по алфавиту
  e.kind,
  e.name
";

$res = $dbi->query($sql);

if ($res === false) {
  $num = 0;
} else {
  $num = mysqli_num_rows($res);
}

// --------------------------------------------------------------------
// собираем плоский список, но считаем города для якорей и имён
// --------------------------------------------------------------------
$items = array();
$townIds = array();
$townCnt = array();

if ($res) {
  while ($row = mysqli_fetch_assoc($res)) {

    $eid = (int)$row['id'];
    $ename = trim((string)$row['name']);
    if ($ename === "") $ename = "Без названия";

    $townId = (int)$row['town'];
    $kindId = trim((string)$row['kind']);
    $ffile  = trim((string)$row['ffile']);

    if (!isset($townCnt[$townId])) $townCnt[$townId] = 0;
    $townCnt[$townId]++;

    $townIds[$townId] = 1;

    $typeDesc = "";
    if ($kindId !== "" && isset($typeDescById[$kindId])) {
      $typeDesc = $typeDescById[$kindId];
    }

    $items[] = array(
      'id' => $eid,
      'name' => $ename,
      'descrip' => (string)$row['descrip'],
      'town' => $townId,
      'kind' => $kindId,
      'type_desc' => $typeDesc,
      'ffile' => $ffile
    );
  }
  mysqli_free_result($res);
}

// --- имена городов ---
$townNameById = array();
if (count($townIds) > 0) {
  $ids = array_keys($townIds);

  // уберём 0 (если есть) — обработаем отдельно
  $ids2 = array();
  foreach ($ids as $t) {
    if ((int)$t > 0) $ids2[] = (int)$t;
  }

  if (count($ids2) > 0) {
    $in = implode(",", $ids2);
    $sqlTown = "SELECT id, name FROM towns WHERE id IN ($in)";
    $resTown = $dbi->query($sqlTown);
    if ($resTown) {
      while ($r = mysqli_fetch_assoc($resTown)) {
        $townNameById[(int)$r['id']] = trim((string)$r['name']);
      }
      mysqli_free_result($resTown);
    }
  }
}

// --- порядок городов для верхних якорей: сортировка по имени города ---
$townOrder = array_keys($townCnt);
usort($townOrder, function($a, $b) use ($townNameById) {
  $an = ($a > 0 && isset($townNameById[$a])) ? $townNameById[$a] : (($a==0) ? "Без города" : ("Город ".$a));
  $bn = ($b > 0 && isset($townNameById[$b])) ? $townNameById[$b] : (($b==0) ? "Без города" : ("Город ".$b));
  return strcasecmp($an, $bn);
});

?>


<div class="container-fluid" style="width:100%">

  <div class="row">
    <div class="col-xs-12">

      <?php echo breadcrumbs(['area' => $aid, 'title' => $title_h]); ?>

      <h1><?php echo htmlspecialchars($title_h); ?></h1>

      <p>
        Здесь собраны местные <strong>изделия</strong>, которые обычно привозят из поездок:
        продукты, ремёсла и региональные специалитеты.
      </p>

      <?php if ($num <= 0) { ?>
        <p>Пока нет записей для этого региона/страны.</p>
      <?php } else { ?>

        <p>
          <?php
            echo "Найдено <strong>$num</strong> ";
            echo echo_int($num, array("изделий","изделие","изделия"));
            echo ".";
          ?>
        </p>

        <?php
        // --- навигация по городам (якоря), если городов больше 1
        if (count($townOrder) > 1) {
          echo "<div class='travell0' style='padding:6px 0 12px 0;'>";
          foreach ($townOrder as $tid) {
            $tname = ($tid > 0 && isset($townNameById[$tid])) ? $townNameById[$tid] : (($tid==0) ? "Без города" : ("Город ".$tid));
            $tcnt = isset($townCnt[$tid]) ? (int)$townCnt[$tid] : 0;
            $anchor = "city-".$tid;
            echo "<a class='travell5n' href='#$anchor' style='margin-right:14px;'>".htmlspecialchars($tname)." ($tcnt)</a>";
          }
          echo "</div>";
        }
        ?>

        <div class="bring-grid">
          <?php
          // Плоский поток карточек, отсортирован уже SQL-ом (town, kind, name)
          $seenTown = array();

          foreach ($items as $it) {
            $eid = (int)$it['id'];
            $nm  = (string)$it['name'];

            $tid = (int)$it['town'];
            $townName = ($tid > 0 && isset($townNameById[$tid])) ? $townNameById[$tid] : (($tid==0) ? "Без города" : ("Город ".$tid));

            $typeDesc = (string)$it['type_desc'];

            $intro = bring_intro($it['descrip']);
            $photo = bring_photo_url($eid, $it['ffile']);

            $anchorId = "";
            if (!isset($seenTown[$tid])) {
              $seenTown[$tid] = 1;
              $anchorId = "city-".$tid;
            }

            echo render_bring_card($eid, $nm, $intro, $photo, $tid, $townName, $typeDesc, $anchorId);
          }
          ?>
        </div>

      <?php } ?>


    </div>
  </div>
</div>

<?php
$_totop = 1;
include('include/footer_bs.php');
?>
