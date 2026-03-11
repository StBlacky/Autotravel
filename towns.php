<?php
include_once('include/config.php');
include_once('include/func.php');
include_once('include/func_bs.php');
include_once('include/head00.php');

//выбор буквы
$larr=array("a01"=>"А","a02"=>"Б","a03"=>"В","a04"=>"Г","a05"=>"Д","a06"=>"Е","a07"=>"Ж","a08"=>"З","a09"=>"И","a10"=>"Й",
    "a11"=>"К","a12"=>"Л","a13"=>"М","a14"=>"Н","a15"=>"О","a16"=>"П","a17"=>"Р","a18"=>"С","a19"=>"Т","a20"=>"У",
    "a21"=>"Ф","a22"=>"Х","a23"=>"Ц","a24"=>"Ч","a25"=>"Ш","a26"=>"Щ","a27"=>"Ы","a28"=>"Э","a29"=>"Ю","a30"=>"Я");

$l = "";
$ll = "";

if (isset($_GET["l"])) {
  $newuri = "/towns.php";
  Header( "HTTP/1.1 301 Moved Permanently" );
  Header( "Location: $newuri" );
  exit;
}
if (isset($_GET["ll"])) {
  $newuri = "/towns.php";
  Header( "HTTP/1.1 301 Moved Permanently" );
  Header( "Location: $newuri" );
  exit;
}
$canon = "/towns.php";  

if (preg_match("/^\/(a\d+)$/",$_pin,$exa)) {
  $l = $exa[1];
  $ll = $larr[$l];
  $canon = "/towns.php/".$l;  
}

$sqltx = "";
if ($_zagr_mode) {
  $menuz = "Города России";
}
else {
  $menuz = "Города России и зарубежных стран";  
}

$menu1 = "<a class=travell5m href=\"/towns.php\">Популярные города</a>";
$menu4 = "<a class=travell5m href=\"/areas.php\">Области России</a>";
$menu5 = "<a class=travell5m href=\"/countries.php\">Зарубежные страны</a>";

if (!($ll)) { //не передана буква
  $sqltt = "select t.name as town, t.url as turl, t.hide as hide, t.rate as rate, t.id as tid, a.name as area from towns t, areas a where a.id > 0 and t.rate > ($thighrate) and a.id=t.area order by t.name";
  $resultt = $dbi->query($sqltt);
  $numrowst = mysqli_num_rows($resultt);
  if (!($_zagr_mode)) {
    $sqltx = "select t.name as town, t.url as turl, t.hide as hide, t.rate as rate, t.id as tid, a.name as area from towns t, areas a where a.id < 0 and t.rate > ($thighrate) and a.id=t.area order by t.name";
    $resultx = $dbi->query($sqltx);
    $numrowsx = mysqli_num_rows($resultx);
    $numrows = $numrowst + $numrowsx;    
  }
  else {
    $numrows = $numrowst;    
  }
  $title = $menuz." - достопримечательности, гостиницы, отзывы";
  $title_h = $menuz;
  $metatags = $menuz.", ".$numrows." ". echo_int($numrows, array("популярных","популярный","популярных")) .", список по алфавиту";
  $metadesc = $menuz." - как добраться, что посмотреть, где остановиться, где поесть, интерактивная карта города.";
}
else {
  $menuz .= " на букву ".$ll;
  $sqltt = "select t.name as town, t.url as turl, t.hide as hide, t.rate as rate, t.id as tid, a.name as area from towns t, areas a where a.id > 0 and t.hide<>1 and t.name like '$ll%' and a.id=t.area order by t.name";
  $resultt = $dbi->query($sqltt);
  $numrowst = mysqli_num_rows($resultt);
  if (!($_zagr_mode)) {
    $sqltx = "select t.name as town, t.hide as hide, t.rate as rate, t.id as tid, a.name as area from towns t, areas a where a.id < 0 and t.hide<>1 and t.name like '$ll%' and a.id=t.area order by t.name";
    $resultx = $dbi->query($sqltx);
    $numrowsx = mysqli_num_rows($resultx);
    $numrows = $numrowst + $numrowsx;    
  }
  else {
    $numrows = $numrowst;    
  }
  $title = $menuz." - достопримечательности, гостиницы, отзывы";
  $title_h = $menuz;
  $metatags = $menuz." - ". $numrows. " ". echo_int($numrows,array("городов и населенных пунктов","город и населенный пункт","города и населенный пункта"));
  $metadesc = $menuz." - как добраться, что посмотреть, где остановиться, где поесть, интерактивная карта города.";  
}

$bootstrap = 1;
include('include/header_bs.php');

$letters = "<div class=\"row\"><div class=\"col-xs-12 well\">";
$letters .= "<h2>Полный список городов по буквам алфавита</h2>\n<h2 align=center>";
foreach ($larr as $lkey=>$lvalue) {
  $letters .= "<b>&nbsp;<a class=travell5m href=\"/towns.php/$lkey\">$lvalue</a>&nbsp;</b>\n";
}
$letters .= "</h2>\n</div></div>\n";

?>
 
<div class="container-fluid" style="width: 100%"> <!--  средняя область -->

<ul class="nav navbar-nav navbar-nav-txt" style="margin:auto; display:inline-block; float:none;">
  <?php if ($ll) {echo "<li>".$menu1."</li>";} ?>
  <li><?php echo $menu4; ?></li>
  <?php if (!($_zagr_mode)) { echo "<li>".$menu5."</li>"; } ?>
</ul>

<div class="row">
<div class="col-xs-12">
<?php 
if (!($ll)) { //не передана буква
  ?>
  <h1>Популярные города России</h1>  
  <p>Здесь
    <?php 
    echo echo_int($numrowst, array("перечислены","перечислен","перечислены"))." <strong>$numrowst ";
    echo echo_int($numrowst,array("наиболее популярных среди путешественников городов и населенных пунктов","наиболее популярный среди путешественников город и населенный пункт","наиболее популярных среди путешественников города и населенных пункта"));
    ?>  нашей страны</strong>. Список городов отсортирован по алфавиту.</p>
    <p>Вы можете выбрать конкретную букву алфавита - откроется полный список городов (не только популярных) на эту букву.</p>
    <p>Щёлкнув по названию города, Вы попадёте на страницу 
    с описанием основных достопримечательностей города и его интерактивной картой, получите всю необходимую информацию о городе.</p>
  <p> 
    На странице каждого города перечислены гостиницы, кафе (рестораны), сувениры и услуги для путешественников. 
    По каждой достопримечательности, гостинице, услуге собраны фотографии и отзывы реальных посетителей, объекты отсортированы по рейтингу, 
    рассчитываемому на основе отзывов.</p>
  <?php
  if (!($_zagr_mode)) {
    echo "<p><a href=\"#zagr\">Чуть ниже</a>, отдельно ";
    echo echo_int($numrowsx, array("перечислены","перечислен","перечислены"))." $numrowsx ";
    echo echo_int($numrowsx,array("популярных городов","популярный город","популярных города")) ." зарубежья.</p>\n";
  }
  echo $letters;
    ?> 

  <?php  
  $tbltxt1 = gettownsresult($resultt,1);
  echo "<div class=column>$tbltxt1</div>\n";
  if (!($_text_mode)) {
  ?> 
  
 

  <?php
  }
  if (!($_zagr_mode)) {
    echo "<h2 id=\"zagr\">Популярные города зарубежных стран</h2>\n";  
    $tbltxt3 = gettownsresult($resultx,0);
    echo "<div class=column>$tbltxt3</div>\n";
  }
  
}
else { // есть буква
  ?>
  <h1>Города России на букву <?php echo $ll; ?></h1>  
  <p>Здесь
    <?php
      echo echo_int($numrowst, array("перечислены","перечислен","перечислены"))." <strong>$numrowst ";
      echo echo_int($numrowst,array("городов и населенных пунктов","город и населенный пункт","города и населенных пункта"));
    ?> нашей страны</strong>, названия которых начинаются <strong>на букву <?php echo $ll; ?></strong>. Щёлкнув по названию города, Вы попадёте на страницу 
    с описанием основных достопримечательностей города и его интерактивной картой.</p>
  <ul><li>простым шрифтом обозначены города, в которых есть только один объект (достопримечательность, гостиница, кафе, пункт оказания услуг или т.п.)</li>
  <li><b>yтолщенным шрифтом</b> обозначены города, в которых есть несколько объектов</li>
  <li><font class="travell5m">зеленым цветом</font> выделены популярные города</li>
  </ul>
  <p> На странице каждого города перечислены гостиницы, кафе (рестораны), сувениры и услуги для путешественников. 
    По каждой достопримечательности, гостинице, услуге собраны фотографии и отзывы реальных посетителей, объекты отсортированы по рейтингу, 
    рассчитываемому на основе отзывов.</p>
  <?php
  if (!($_zagr_mode)) {
    echo "<p><a href=\"#zagr\">Чуть ниже</a>, отдельно ";
    echo echo_int($numrowsx, array("перечислены","перечислен","перечислены"))." $numrowsx ";
    echo echo_int($numrowsx,array("городов","город","города")) ." зарубежья на букву $ll</p>\n";
  }
  echo $letters;
    ?> 
  <!--  Автотревел, таунс реклама -->    
  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- Города автотревел -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-8370262657408864"
     data-ad-slot="6057409832"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>
    <!-- Блок рекламы Автотревел, таунс реклама -->
  <?php  
  if ($numrowst > 15) {
    $tbltxt1 = gettownsresult($resultt,2);
  }
  else {
    $tbltxt1 = gettownsresult($resultt,0);
  }
  echo "<div class=column>$tbltxt1</div>\n";
  if (!($_text_mode)) {
  ?>
    <div style="padding: 10px; text-align: center;"> 
      <!--  Автотревел, таунс реклама -->     
      <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
      <ins class="adsbygoogle"
        style="display:block"
        data-ad-client="ca-pub-2266145788867998"
        data-ad-slot="4585890667"
        data-ad-format="auto"></ins>
      <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
    </div> <!-- Блок рекламы Автотревел, таунс реклама -->
  <?php
  }
  if (!($_zagr_mode)) {
    echo "<h2 id=\"zagr\">Города зарубежных стран на букву $ll</h2>\n";
    if ($numrowst > 15) {
      $tbltxt3 = gettownsresult($resultx,2);
    }
    else {
      $tbltxt3 = gettownsresult($resultx,0);
    }
    echo "<div class=column>$tbltxt3</div>\n";
  }
}
?>
</div>
</div>

</div> <!--  средняя область -->

<?php 
$_totop = 1;
include('include/footer_bs.php');