<?php
include_once('include/config.php');
include_once('include/func.php');
include_once('include/func_bs.php');
include_once('include/head00.php');
$menuz = "Области и регионы России";  

$menu1 = "<a class=travell5m href=\"/towns.php\">Популярные города</a>";
$menu4 = "<a class=travell5m href=\"/areas.php\">Области России</a>";
$menu5 = "<a class=travell5m href=\"/countries.php\">Зарубежные страны</a>";
$canon = "/areas.php";
$sqltt = "select distinct a.name as area, t.area as aid, 0 as capital from towns t, areas a where a.id > 0 and t.hide <> 1 and a.id = t.area order by 1";
$resultt = $dbi->query($sqltt);
$numrowst = mysqli_num_rows($resultt);
$title = $menuz.": города, достопримечательности, гостиницы";
$title_h = $menuz;
$metatags = $menuz.", ".echo_int($numrows, array("областей","область","области"))." список по алфавиту";
$metadesc = $menuz." - как добраться, что посмотреть, где остановиться, где поесть, интерактивная карта.";

$bootstrap = 1;
include('include/header_bs.php');

?>

<div class="container-fluid" style="width: 100%"> <!--  средняя область -->

<ul class="nav navbar-nav navbar-nav-txt" style="margin:auto; display:inline-block; float:none;">
  <?php echo "<li>".$menu1."</li>"; ?>
  <?php if (!($_zagr_mode)) { echo "<li>".$menu5."</li>"; } ?>
</ul>

<div class="row">
<div class="col-xs-12">
<h1>Области и регионы России</h1>  
  <p>Здесь
    <?php 
    echo echo_int($numrowst, array("перечислены","перечислена","перечислены"))." <strong>$numrowst ";
    echo echo_int($numrowst,array("областей и регионов","область и регион","области и региона"));
    ?>  нашей страны</strong>. Щёлкнув по названию области, Вы попадёте на страницу 
    с перечнем городов, фотографиями и интерактивной картой области.</p>
  <p> 
    На странице каждого города перечислены гостиницы, кафе (рестораны), сувениры и услуги для путешественников. 
    По каждой достопримечательности, гостинице, услуге собраны фотографии и отзывы реальных посетителей, объекты отсортированы по рейтингу, 
    рассчитываемому на основе отзывов.</p>
  <?php
  $tbltxt1 = getareasresult($resultt,1);
  echo "<div class=column>$tbltxt1</div>\n";
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
</div>
</div>

</div> <!--  средняя область -->

<?php 
$_totop = 1;
include('include/footer_bs.php');