<?php
include_once('include/config.php');
include_once('include/func.php');
include_once('include/head00.php');
$title = "Самостоятельные путешествия: прокладка маршрутов, каталог рекомендованных маршрутов";
$metatags="Автомобильные маршруты, маршруты для путешествий, самостоятельные путешествия, прокладка маршрутов";
$metadesc="Маршруты для самостоятельных путешествий на автомобиле. Описание дороги, расстояние между городами, советы по осмотру достопримечательностей, выбору мест для питания и ночевок.";
include('include/header.php');
?>

<h1>Маршруты для самостоятельных путешествий на автомобиле</h1>
<p class=travell0>
Уже готовые автомобильные маршруты на любой вкус. Мы постарались представить Вам как сбалансированные маршруты с разнообразными достопримечательностями, 
так и маршруты конкретной направленности. </p>
<p class=travell0>
   Например, 
если Вам близка и интересна архитектура, то Вам подойдут 
маршруты из категории <b>Усадьбы</b>. Если Вы любитель старинных построек, то 
  Вам будут небезинтересны маршруты из категории <b>Кремли</b>. Так же стоит 
обратить внимание на маршруты из раздела <b>Монастыри</b> - отличная 
возможность совершить самостоятельное путешествие не только по 
физической истории, но и истории духа Росси.<br>
Для Вашего удобства все маршруты собраны не только по категориям, но и по
длительности путешествия - <b>маршруты выходного дня</b>, праздничные маршруты.
<br>
Особое внимание уделено <b>отдыху с детьми</b>. Для путешествий с детьми подходят маршруты с
разнообразными интерактивными достопримечательностями и не напряженным графиком.
<br>
Все маршруты созданы с учетом своего опыта путешествий и представляют собой 
фактически квинтэссенцию автомобильных маршрутов сайта. 
</p>
<?php
echo "<h2>Маршруты по теме и длительности путешествия</h2>\n";

$sql="select distinct trip_desc.ex as ex, trip_ex.descrip as descrip from trip_desc, trip_ex where trip_ex.id = trip_desc.ex order by 2";
$sql1="select distinct trip_desc.d as d, trip_d.descrip as descrip from trip_desc, trip_d where trip_d.id = trip_desc.d order by 2";
$result = $dbi->query($sql);
$result1 = $dbi->query($sql1);

echo "<table width='90%'><tr>";

if($myrow = mysqli_fetch_array($result)) {
   echo "<td width = '40%' align=center valign=top> <p class=travell5>По тематике</p>\n";
  do {
     $ex = $myrow["ex"];
     $desc = $myrow["descrip"];
     echo "<p class=travell5><a class=travell5m href=/triptrackm.php/e$ex>$desc</A></p>\n";
   } while($myrow = mysqli_fetch_array($result)); 
   echo "</td>";
 }
mysqli_free_result($result);
if($myrow1 = mysqli_fetch_array($result1)) {
   echo "<td align=center valign=top><p class=travell5>По длительности</p>\n";
  do {
     $ddd = $myrow1["d"];
     $desc = $myrow1["descrip"];
     echo "<p class=travell5><a class=travell5m href=/triptrackm.php/d$ddd>$desc</A></p>\n";
   } while($myrow1 = mysqli_fetch_array($result1)); 
  echo "</td>";
 }
mysqli_free_result($result1);

echo "</tr></table>";

echo "<p class=travell5>Это интересно: <a class=travell5m href=/brends.php>Известные туристические маршруты</A> </p>\n";

include('include/footer.php');

?>
