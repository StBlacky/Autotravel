<?php
include_once('include/config.php');
include_once('include/func.php');
include_once('include/func_bs.php');
include_once('include/head00.php'); // если у вас уже переведено на head01.php — поменяй здесь

// ------------------------------
// Настройки страницы
// ------------------------------
$menuz  = "Что привезти";


$canon = "/bring/";  // каноникал под будущий rewrite; если пока нет — можно поставить "/excite_bring.php"

// SEO (под ваш header_bs.php)
$title     = $menuz." — по регионам и странам";
$title_h   = $menuz;
$metatags  = $menuz.", регионы, страны, список по алфавиту";
$metadesc  = "Справочник по местным товарам, ремёслам и региональным специалитетам: выбрать регион России или страну.";

$bootstrap = 1;
include('include/header_bs.php');

// ------------------------------
// SQL: две выборки (Россия / Страны), только где есть exciting.type=5
// ВАЖНО: я не добавляю условий по hide, потому что по полям exciting тут нет факта.
// ------------------------------
$sql_ru = "
  SELECT a.name AS area, a.id AS aid, 0 AS capital, a.slug AS slug, COUNT(e.id) AS cnt
  FROM areas a
  JOIN exciting e ON e.area = a.id AND e.type = 5
  WHERE a.id > 0
  GROUP BY a.id, a.name
  HAVING cnt > 0
  ORDER BY 1
";
$res_ru = $dbi->query($sql_ru);
$num_ru = ($res_ru) ? mysqli_num_rows($res_ru) : 0;

$sql_ct = "
  SELECT a.name AS area, a.id AS aid, 0 AS capital, COUNT(e.id) AS cnt, a.slug AS slug
  FROM areas a
  JOIN exciting e ON e.area = a.id AND e.type = 5
  WHERE a.id < 0
  GROUP BY a.id, a.name
  HAVING cnt > 0
  ORDER BY 1
";
$res_ct = $dbi->query($sql_ct);
$num_ct = ($res_ct) ? mysqli_num_rows($res_ct) : 0;

// ------------------------------
// Рендер списка (по аналогии с getareasresult), но:
// - ссылка ведёт в раздел "Что привезти" по area id
// - показываем счётчик cnt
// ------------------------------
function getareasresult_bring($result, $block) {
  $tbltxt = "";
  $lx = "";

  if ($result && ($myrow = mysqli_fetch_array($result))) {
    do {
      $arean = $myrow["area"];
      $aid   = (int)$myrow["aid"];
      $cnt   = (int)$myrow["cnt"];
      $slug  = isset($myrow["slug"]) ? trim($myrow["slug"]) : "";

      $dclass = "travell9c";
      $aclass = "travell5n";

      if ($block) {
        $lc = mb_substr($arean, 0, $block);
        if (!($lx)) {
          $tbltxt .= "<div class=\"tblock\">";
          $lx = $lc;
        }
        if ($lx != $lc) {
          $tbltxt .= "</div>\n<div class=\"tblock\">";
          $lx = $lc;
        }
      }

      // ссылка на 2-й уровень раздела
    if ($slug !== "") {
  $href = "/excite_bring_area/".rawurlencode($slug)."/";
} else {
  $href = "/excite_bring_area/".$aid."/";
}


      // счётчик — серым, без лишнего шума
      $cnt_html = " <span class=travell0>(".$cnt.")</span>";

      $tbltxt .= "<div class=\"$dclass\"><a class=\"$aclass\" href=\"$href\">".htmlspecialchars($arean)."</a>$cnt_html</div>\n";
    } while ($myrow = mysqli_fetch_array($result));

    if ($block) {
      $tbltxt .= "</div>\n";
    }
    mysqli_free_result($result);
  }
  return $tbltxt;
}
?>

<div class="container-fluid" style="width: 100%">

  <div class="row">
    <div class="col-xs-12">
      <h1><?php echo htmlspecialchars($menuz); ?></h1>

      <div class="note-block">
        Здесь собраны местные изделия, которые обычно привозят из поездок: продукты, ремёсла и региональные специалитеты.
Раздел справочный — он помогает понять, что характерно для региона или страны, без советов и рекомендаций. Выберите регион России или страну.
      </div>

      <?php if ($num_ru > 0) { ?>
        <h2>Россия</h2>
        <?php
          $tbl_ru = getareasresult_bring($res_ru, 1);
          echo "<div class=column>$tbl_ru</div>\n";
        ?>
      <?php } ?>

      <?php if ($num_ct > 0) { ?>
        <h2>Страны</h2>
        <?php
          $tbl_ct = getareasresult_bring($res_ct, 1);
          echo "<div class=column>$tbl_ct</div>\n";
        ?>
      <?php } ?>

      </div>
  </div>

</div>

<?php
$_totop = 1;
include('include/footer_bs.php');
?>
