<?php 
include_once('include/config.php');
include_once('include/func.php');
include_once('include/func_bs.php');
include_once('include/head00.php');
include_once('include/fotoman.php');

$bootstrap = 1;
$file     = "";
$author   = 0;
$town     = 0;
$area     = 0;
$ex       = 0;
$done     = 0;
$start    = 0;
$cat      = "";
$www_dir  = "/home/blacky/data/www";

/* --------------------------- парсинг PATH_INFO --------------------------- */
if ($_pin == "") {
  $title = "Фотографии: информация";
  include('include/header_bs.php');
  echo "<div class='container-fluid' style='width:100%;'>";
  include('phalbum.html');
  echo "</div>";
  include('include/footer_bs.php');
  $done = 1;
}

if (preg_match("/^\/(\d+)\/(\d+)$/",$_pin,$exa)) $file = $exa[1]."/".$exa[2];
if (preg_match("/^\/(\d+)$/",$_pin,$exa)) $cat = $exa[1];
if (preg_match("/^\/(\d+)\/$/",$_pin,$exa)) $cat = $exa[1];
if (preg_match("/^\/(\d+)\/index.html$/",$_pin,$exa)) $cat = $exa[1];
if (preg_match("/^\/town\/(\d+)$/",$_pin,$exa)) $town = $exa[1];
if (preg_match("/^\/area\/(\-*\d+)$/",$_pin,$exa)) $area = $exa[1];
if (preg_match("/^\/ex\/(\d+)$/",$_pin,$exa)) $ex = $exa[1];
if (preg_match("/^\/author\/(\d+)\/(\d+)$/",$_pin,$exa)) { $author=$exa[1]; $start=$exa[2]; }

if ($town == 9999) $town = "";

/* --------------------------- служебные флаги --------------------------- */
if ($_fotomanager) $sqladd = "";
else $sqladd = " mode > 0 and ";
if ($_foto_mode) $sqladd = "";

/* --------------------------- фото одного объекта --------------------------- */
if (($ex > 0) and (!($done))) {
  $name = gettownex_bs($ex);
  $town = $name[3];
  $tnname = $name[1];
  $exname = $name[0];
  $area = $name[2];
  $typename = gettypename($ex);

  $sql = "select file, wi, hi, author, comment, mode 
          from foto 
          where $sqladd exciting = $ex 
          order by mode desc, date desc";
  $result = $dbi->query($sql);
  $ii = $result->num_rows;

  // --- SEO ---
  $title = "$exname — $typename $tnname ($area)";
  $metadesc = "Фотографии $typename «$exname» в городе $tnname ($area). Авторские снимки, архитектура и атмосфера места.";
  $metatags = "$ii фото $typename $exname ($tnname)";
  $canon = "/otklik.php/$ex";
  $ug_gallery = 1;
  $customscript = '
    jQuery(".sp-slideshow").sliderPro({
      width: "100%",
      height: 500,
      fade: true,
      arrows: true,
      buttons: false,
      waitForLayers: true,
      autoScaleLayers: false
    });
  ';

  include('include/header_bs.php');
  echo '<div class="container-fluid">';
  echo breadcrumbs( ['town'  => $town, 'ex'    => $ex, 'title' => "Фотографии $exname"]);
  
  
  echo "<h1>$exname ($tnname), $ii " . echo_int($ii, ['фотографий','фотография','фотографии']) . "</h1>";
  echo "<p class='note-block'>Фотографии $typename \"$exname\" — это снимки путешественников и гостей, запечатлевших что-то для себя необычное и удивительное, чем захотелось поделться.
С полной информацией на тему <b>$exname</b> можно ознакомиться в разделе <a class='travell5n' href='/otklik.php/$ex'>$exname</a>.</p>";
  echo '<div id="fotoimages">';
  
  while ($myrow = mysqli_fetch_array($result)) {
    $file = $myrow["file"];
    $author = authformat($myrow["author"], "nolink");
    $comment = $myrow["comment"];
    $wi = $myrow["wi"];
    $hi = $myrow["hi"];
    $alt = htmlspecialchars("$exname ($tnname)");
    $desc = htmlspecialchars(($comment ? $comment.". " : "")."Фото: ".$author);
    echo "<img class='sp-image' alt='$alt' src='/phalbum/$file-s.jpg' data-image='/phalbum/$file.jpg' data-description='$desc'>\n";
  }
  
  mysqli_free_result($result);
  echo '</div></div>';
  ?>

  <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ImageGallery",
  "name": "<?php echo htmlspecialchars($exname . ' — ' . $tnname); ?>",
  "image": [
    <?php
      $photo_array = [];
      $sql_imgs = "SELECT file FROM foto WHERE $sqladd exciting = '$ex' ORDER BY mode DESC, date DESC LIMIT 20";
      $res_imgs = $dbi->query($sql_imgs);
      while ($r = mysqli_fetch_array($res_imgs)) {
        $photo_array[] = "https://autotravel.ru/phalbum/".$r['file'].".jpg";
      }
      echo '"' . implode('","', $photo_array) . '"';
      mysqli_free_result($res_imgs);
    ?>
  ]
}
</script>
<?php
  
  include('include/footer_bs.php');
  $done = 1;
}

/* --------------------------- одна фотография --------------------------- */
if (($file) and (!($done))) {
  $sql = "select exciting, town, author, comment from foto where file = '$file'";
  $result = $dbi->query($sql);
  if($myrow = mysqli_fetch_array($result)) {
    $exciting = $myrow["exciting"];
    $town = $myrow["town"];
    $author = $myrow["author"];
    $comment= $myrow["comment"];
    $exname = getexname($exciting);
    $tnname = gettownname($town);
    $author = authformat($author,"album");

    $title = "Фотография $exname — $tnname";
    $metadesc = "Фотография объекта $exname в городе $tnname.";
    $canon = "/phalbum.php/$file";

    include('include/header_bs.php');
    echo "<div class='container-fluid' style='width:100%;'>";
    echo "<h1>$exname ($tnname)</h1>";

    $fn = "phalbum/" . $file . ".jpg";
    $fp = $www_dir . "/" . $fn;
    if (is_file($fp)) {
      echo "<img src='/$fn' alt='Фотография $exname ($tnname)' class='img-responsive'><br>";
    }

    if ($author)  echo "<p><b>Автор:</b> $author</p>";
    if ($comment) echo "<p><b>Комментарий:</b> $comment</p>";

    echo "</div>";
    include('include/footer_bs.php');
    $done = 1;
  }
}

/* --------------------------- фотографии города --------------------------- */

if (($town) and (!($done))) {
  $name   = gettown_bs($town);
  $ik     = countfototown($town);
  $tnname = $name[1];
  $area   = $name[2];
  $tnnamefrom = $name[6];

  // --- SEO данные ---
  $excount = count_ex_town($town);
  if (function_exists('count_fotos_town')) {
    $photocount = count_fotos_town($town);
  } else {
    $photocount = count_fotos_area($area);
  }

  $title     = "Фотографии города $tnnamefrom  — $area";
  $metadesc  = "Фотогалерея $tnnamefrom  — $photocount фотографий путешественников и гостей, передающих атмосферу. В базе Autotravel.ru — $excount достопримечательностей, кафе и гостиниц.";
  $metatags  = "$photocount фото $tnname ($area)";
  $canon     = "/town.php/$town";
  $ftg_gallery = 1;

  $customscript = '
    jQuery(".final-tiles-gallery").finalTilesGallery({
      layout: "columns",
      autoLoadURL: "/tech_town_f.php/'.$town.'"
    });
  ';

  include('include/header_bs.php');

  echo '<div class="container-fluid">';
  // --- хлебные крошки ---
  echo breadcrumbs(['town'  => $town, 'title' => "Фотогалерея $tnnamefrom"]);
  echo "<h1>Фотогалерея $tnnamefrom</h1>";
  echo "<p class='note-block'>
    Фотогалерея города $tnname — это $photocount фотографий путешественников и гостей,
    передающих атмосферу места. В базе Autotravel.ru — вы можете найти $excount. Список объектов постоянно пополняется.
    Смотрите фото, читайте описания и планируйте собственное путешествие.
  </p>";
 
  $sql = "select file, wi, hi, exciting, author, comment, mode 
          from foto 
          where $sqladd town = '$town' 
          order by mode desc, date desc LIMIT 40";
  $result = $dbi->query($sql);

  echo '<div class="row"><div class="col-xs-12">';
  echo '<div id="gallery" class="final-tiles-gallery caption-color-light"><div class="ftg-items">';
  echo fotos_town($town, $tnname, $result)[0];
  echo '</div></div></div></div>';
  echo '</div>';

  mysqli_free_result($result);
 ?>
  <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ImageGallery",
  "name": "Фотографии <?php echo htmlspecialchars($tnname . ' — ' . $area); ?>",
  "image": [
    <?php
      $photo_array = [];
      $sql_imgs = "SELECT file FROM foto WHERE $sqladd town = '$town' ORDER BY mode DESC, date DESC LIMIT 20";
      $res_imgs = $dbi->query($sql_imgs);
      while ($r = mysqli_fetch_array($res_imgs)) {
        $photo_array[] = "https://autotravel.ru/phalbum/".$r['file'].".jpg";
      }
      echo '"' . implode('","', $photo_array) . '"';
      mysqli_free_result($res_imgs);
    ?>
  ]
}
</script>
<?php
  include('include/footer_bs.php');
  $done = 1;
}

if (($area) and (!($done))) {
  $areax = getareaall($area);
  $areaname  = $areax[0]; 
  $areaname_r = $areax[1];

  $count   = count_fotos_area($area);
  $tncount = count_towns_area($area);
  $excount = count_ex_area($area);

  // --- SEO блок ---
  $title = "Фотографии региона $areaname_r";
  $metadesc = "Фото регионов и городов $areaname_r: архитектура, памятники, путешествия по автодорогам России.";
  $metatags = "$count фото $areaname_r";
  $canon = "/area.php/$area";

  $ftg_gallery = 1;
  $customscript = '
    jQuery(".final-tiles-gallery").finalTilesGallery({
      layout: "columns",
      autoLoadURL: "/tech_area_f.php/'.$area.'"
    });
  ';

  include('include/header_bs.php');

  echo "<div class='container-fluid'>";
  echo breadcrumbs(['area' => $area, 'title' => "Фотогалерея $areaname_r"]);

  // --- Новый текстовый блок (как у города) ---
  
  echo "<h1>$count " . echo_int($count, ['фотографий','фотография','фотографии']) . " $areaname_r</h1>";

 echo "<p class='note-block'>
  Фотогалерея региона $areaname_r объединяет снимки путешественникв, сделанные в разных городах области — от архитектурных памятников до повседневных пейзажей.
  ";

  echo " Выберите интересное место и посмотрите его фото — возможно, оно станет целью вашего следующего путешествия.
  </p>";

  // --- Галерея ---
  $sql = "select f.file as file, f.exciting as exciting, f.author as author, f.comment as comment, f.mode as mode, t.name as tname 
          from foto f, towns t 
          where $sqladd f.town = t.id and t.area = $area 
          order by mode desc, date desc LIMIT 40";
  $result = $dbi->query($sql);

  if ($result) {
    echo "<div id='gallery' class='final-tiles-gallery caption-color-light'><div class='ftg-items'>";
    $sp_slides = fotos_area($result);
    echo $sp_slides;
    echo "</div></div>";
    mysqli_free_result($result);
  }

  echo "</div>";
  include('include/footer_bs.php');
  $done = 1;
}

/* ==========================================================
      АДМИНСКИЙ ПРОСМОТР НОВЫХ ФОТО /phalbum.php/admin
   ========================================================== */

if ($_fotomanager && preg_match("/^\/admin\/?(\d+)?$/", $_pin, $m) && !$done) {
include("include/header_bs.php");
    $start = isset($m[1]) ? intval($m[1]) : 0;
    $perpage = 50;

    echo "<div class='container-fluid' style='width:100%;'>";
    echo "<h1>Новые фотографии (админ-просмотр)</h1>";

/* ===== ВСЕГО ФОТО ===== */
$sqlc = "SELECT COUNT(*) FROM foto";
$resc = $dbi->query($sqlc);
list($total) = mysqli_fetch_row($resc);

/* ===== ВЫБОРКА НОВЫХ ФОТО (сортировка по id) ===== */
$sql = "
    SELECT f.file, f.town, f.exciting, f.author, f.comment, f.mode, f.date
    FROM foto f
    ORDER BY 
        CAST(SUBSTRING_INDEX(f.file, '/', 1) AS UNSIGNED) DESC,
        f.file DESC
    LIMIT $start, $perpage
";

$res = $dbi->query($sql);

/* ===== ОСНОВНОЙ ЦИКЛ ===== */
while ($r = mysqli_fetch_assoc($res)) {

    $id      = $r["id"];
    $file    = $r["file"];
    $town    = $r["town"];
    $ex      = $r["exciting"];
    $author  = $r["author"];
    $comment = $r["comment"];
    $mode    = $r["mode"];
    $date    = $r["date"] ?: "(дата не указана)";

    // Названия
    $tnname = $town ? gettownname($town) : "(нет города)";
    $exname = $ex   ? getexname($ex)     : "(нет объекта)";
    $author_fmt = authformat($author, "nolink");


    echo "<div class='row admin-photo-item' style='margin-bottom:40px;'>";

    /* =====================================
          ЛЕВАЯ КОЛОНКА — ИНФО + КНОПКИ
       ===================================== */
    echo "<div class='col-md-6 admin-photo-info' style='padding-right:25px;'>";

    echo "<p><b>ID:</b> $id<br>";
    echo "<b>Файл:</b> $file<br>";
    echo "<small>$date</small></p>";

    echo "<p><b>Город:</b> $tnname</p>";
    echo "<p><b>Объект:</b> $exname</p>";
    echo "<p><b>Автор:</b> $author_fmt</p>";

    if ($comment) {
        echo "<p><b>Комментарий:</b> $comment</p>";
    }

    echo "<p><b>Статус:</b> ";
    echo ($mode > 0
        ? "<span style='color:green;'>✔ в индексе</span>"
        : "<span style='color:red;'>✖ вне индекса</span>"
    );
    echo "</p>";

    /* --- КНОПКИ --- */
    echo "<p><a class='adm-btn' target='_blank'
               href='/fotoman.php?ufile=$file&amp;utown=$town'>
               Редактировать
             </a></p>";

    if ($mode > 0) {
        echo "<p><a class='adm-btn' target='_blank'
                   href='/fotoman.php?ufile=$file&amp;utown=$town&amp;mode=0&amp;action=newmode'>
                   Убрать из индекса
                 </a></p>";
    } else {
        echo "<p><a class='adm-btn' target='_blank'
                   href='/fotoman.php?ufile=$file&amp;utown=$town&amp;mode=1&amp;action=newmode'>
                   В индекс
                 </a></p>";
    }

    echo "<p><a class='adm-btn' target='_blank'
               href='/fotoman.php?ufile=$file&amp;utown=$town&amp;action=del'>
               Удалить
             </a></p>";

    echo "<p><a class='adm-btn primary' target='_blank'
               href='/phalbum.php/$file'>
               Открыть фото полностью
             </a></p>";

    echo "</div>"; // col-md-6



    /* =====================================
          ПРАВАЯ КОЛОНКА — КРУПНОЕ ФОТО
       ===================================== */
    echo "<div class='col-md-6 admin-photo-image' style='text-align:center;'>";

    echo "<a href='/phalbum.php/$file' target='_blank'>
            <img class='admin-big-photo'
                 src='/phalbum/{$file}.jpg'
                 style='width:95%; max-width:700px; height:auto; border:1px solid #ccc;'
                 alt='Фото $exname $tnname'>
          </a>";

    echo "</div>"; // col-md-6

    echo "</div>"; // row
    echo "<hr>";
}


    mysqli_free_result($res);

    /* -------- Пагинация -------- */
    echo "<div style='margin:20px 0; text-align:center;'>";

    if ($start > 0) {
        $prev = max(0, $start - $perpage);
        echo "<a class='btn btn-default' href='/phalbum.php/admin/$prev'>← Предыдущие</a> ";
    }

    if ($start + $perpage < $total) {
        $next = $start + $perpage;
        echo "<a class='btn btn-default' href='/phalbum.php/admin/$next'>Следующие →</a>";
    }

    echo "</div>";

    echo "</div>";

    $done = 1;
}

if (($author) and (!($done))) {

  $authv = getauthor($author);
  $authn = authformat($authv, "txt");

  // --- SEO ---
  $title      = "Фотографии — $authn";
  $metadesc   = "Фотографии автора $authn: города, достопримечательности, архитектура и путешествия по России.";
  $metatags   = "фото, $authn";
  $canon      = "/phalbum.php/author/$author/$start";

  include('include/header_bs.php');

  echo "<div class='container-fluid' style='width:100%;'>";
  echo "<h1>Фотографии $authn</h1>";

  $f1 = $start - $_count_list;
  if ($f1 < 0) $f1 = 0;
  $f2 = $start + $_count_list;
  $ii = 0;

  $sql = "select file, exciting, town, comment, mode 
          from foto 
          where author = '$authv' 
          order by file desc 
          LIMIT $start, $_count_list";
  $result = $dbi->query($sql);

  echo "<table width='100%' border='0' cellpadding='3'><tbody class='travell0'>";
  while ($myrow = mysqli_fetch_array($result)) {
    $file     = $myrow["file"];
    $mode     = $myrow["mode"];
    $exciting = $myrow["exciting"];
    $town     = $myrow["town"];
    $comment  = $myrow["comment"];

    if ($ii % 2 == 0) echo "<tr>";
    $ii++;

    $exname = "";
    $tnname = "";

    if ($mode > 0) {
      if ($exciting) {
        list($town,$tnname,$exname) = explode("\t", gettownex($exciting));
      } elseif ($town) {
        $tnname = gettownname($town);
      }
    }

    echo "<td width='50%' class='travell01f' align='center'>";
    echo "<a title='$exname $tnname Фото: $authn' class='atcbox js-lightbox' href='/phalbum.php/$file' data-img='/phalbum/$file.jpg'>
            <img src='/phalbum/$file-s.jpg' alt='Фото $exname $tnname'>
          </a>";

    if ($mode > 0) {
      if ($exciting) {
        echo "<p><b>Город:</b> $tnname</p>";
        echo "<p><b>$exname</b></p>";
      } elseif ($town) {
        echo "<p><b>Город:</b> $tnname</p>";
      }
    } else {
      echo "<p>Не участвует в индексах</p>";
    }

    if ($comment) echo "<p><b>Комментарий:</b> $comment</p>";

    echo "</td>";
    if ($ii % 2 == 0) echo "</tr>";
  }
  mysqli_free_result($result);

  if ($ii % 2 != 0) echo "</tr>";
  echo "</tbody></table>";

  // --- Навигация ---
  echo "<table border='0' width='100%'><tr><td width='50%' align='right'>";
  if ($start > 0) {
    echo "<form method='post' action='/phalbum.php/author/$author/$f1'>
            <input type='submit' value='<<< Предыдущие $_count_list'>
          </form>";
  }
  echo "</td><td width='50%' align='left'>";
  if ($ii >= $_count_list) {
    echo "<form method='post' action='/phalbum.php/author/$author/$f2'>
            <input type='submit' value='Следующие $_count_list >>>'>
          </form>";
  }
  echo "</td></tr></table>";

  echo "</div>";
  include('include/footer_bs.php');
  $done = 1;
}




  
if (!($done)) include "404.php";
?>
