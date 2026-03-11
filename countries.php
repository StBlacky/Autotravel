<?php
include_once('include/config.php');
include_once('include/func.php');
include_once('include/func_bs.php');
include_once('include/head00.php');
$menuz = "Зарубежные страны";  

$menu1 = "<a class=travell5m href=\"/towns.php\">Популярные города</a>";
$menu4 = "<a class=travell5m href=\"/areas.php\">Области России</a>";
$menu5 = "<a class=travell5m href=\"/countries.php\">Зарубежные страны</a>";
$canon = "/countries.php";
$sqltt = "select distinct a.name as area, a.id as aid, a.capital as capital from areas a where a.id < 0 order by 1";
$resultt = $dbi->query($sqltt);
$numrowst = mysqli_num_rows($resultt);
$title = $menuz.": города, достопримечательности, гостиницы";
$title_h = $menuz;
$metatags = $menuz.", ".echo_int($numrows, array("стран","страна","страны"))." список по алфавиту, популярные города";
$metadesc = $menuz." - как добраться, что посмотреть, где остановиться, где поесть, интерактивная карта.";

$sqltx = "select t.name as town, t.url as turl, t.hide as hide, t.rate as rate, t.id as tid, a.name as area from towns t, areas a where a.id < 0 and t.rate > ($thighrate) and a.id=t.area order by t.name";
$resultx = $dbi->query($sqltx);
$numrowsx = mysqli_num_rows($resultx);


$bootstrap = 1;
include('include/header_bs.php');

?>

<div class="container-fluid" style="width: 100%"> <!--  средняя область -->

<ul class="nav navbar-nav navbar-nav-txt" style="margin:auto; display:inline-block; float:none;">
  <?php echo "<li>".$menu1."</li>"; ?>
  <?php echo "<li>".$menu4."</li>"; ?>
</ul>

<div class="row">
<div class="col-xs-12">
<h1>Зарубежные страны</h1>  
  <p>Здесь
    <?php 
    echo echo_int($numrowst, array("перечислены","перечислена","перечислены"))." <strong>$numrowst ";
    echo echo_int($numrowst,array("зарубежных стран","зарубежная страна","зарубежные страны"));
    ?></strong>. Щёлкнув по названию страны, Вы попадёте на страницу 
    с перечнем городов выбранной страны, фотографиями интересных мест и интерактивной картой.</p>
  <p> 
    На странице каждого города перечислены гостиницы, кафе (рестораны), сувениры и услуги для путешественников. 
    По каждой достопримечательности, гостинице, услуге собраны фотографии и отзывы реальных посетителей, объекты отсортированы по рейтингу, 
    рассчитываемому на основе отзывов.</p>
  <?php
  echo "<p><a href=\"#zagr\">Чуть ниже</a>, отдельно ";
  echo echo_int($numrowsx, array("перечислены","перечислен","перечислены"))." $numrowsx ";
  echo echo_int($numrowsx,array("популярных городов","популярный город","популярных города")) ." зарубежья.</p>\n";
  $tbltxt1 = getareasresult($resultt,1);
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
    echo "<h2 id=\"zagr\">Популярные у автопутешественников города</h2>\n";  
    $tbltxt3 = gettownsresult($resultx,0);
    echo "<div class=column>$tbltxt3</div>\n";
  ?>
</div>
</div>

</div> <!--  средняя область -->

<?php 
$_totop = 1;
include('include/footer_bs.php');