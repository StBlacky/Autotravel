<?php
// Для pop-up окна с одним объектом.
include_once('include/func.php');
include_once('include/func_bs.php');
include_once('include/head00.php');
$bootstrap = 1;
// теперь всё как обычно

if (preg_match("/^\/(\d+)$/",$_pin,$exa)) {
  $ex = $exa[1];
}

if (!($ex)) {
  $newuri = "/";
  Header( "HTTP/1.1 301 Moved Permanently" );
  Header( "Location: $newuri" );
  exit;
}

// пользовательское описание
$ex_descr = getextext($ex);

$sqlex = "select e.id as eid, e.name as exciting, e.descrip as description, e.booking as booking,
         e.town as town, e.type as type, e.Nshirota as shirota, e.exact as exact,
         t.Nshirota as tshirota, t.Edolgota as tdolgota,
         e.Edolgota as dolgota, e.wrate as ratingvalue, e.rate as ratingall, e.votes as ratingcount, type.name as typename
         from exciting e, towns t, type where e.id = '$ex' and e.mainid>=0 and e.type = type.id and t.id = e.town";
$resultex = $dbi->query($sqlex);

$s_town = "";
$t_coord = "";

$ex = 0;
$s_ex="Нет названия";
$s_descr="Нет описания";
$typename = "";
$town=0;
$et=1;
$s_booking = "";

if($resultex) {
  if (mysqli_num_rows($resultex)>0) {
    $myrowex = mysqli_fetch_array($resultex);
    $ex = $myrowex["eid"]; //нашлось!
    $s_ex = $myrowex["exciting"]; //название объекта
    $Nshirota = (float)$myrowex["shirota"];
    $Edolgota = (float)$myrowex["dolgota"];
    $tlat = (float)$myrowex["tshirota"];
    $tlng = (float)$myrowex["tdolgota"];
    $exact = (int)$myrowex["exact"];
    $s_descr = $myrowex["description"]; //описание объекта
    $typename = $myrowex["typename"];
    $town = $myrowex["town"];
    $ratingvalue = $myrowex["ratingvalue"];
    $ratingall = $myrowex["ratingall"];
    $ratingcount = $myrowex["ratingcount"];
    $et = $myrowex["type"]; //категория объекта 2 - гостиницы, 3 - еда
    $ngrv = degreestotext($Nshirota);
    $egrv = degreestotext($Edolgota);
    if (($ngrv) and ($egrv)) {
      $nlat = $Nshirota; 
      $nlng = $Edolgota;
      if ($egrv<0) {
        $coord = "N$ngrv, W".str_replace("-","",$egrv);
      } 
      else {
        $coord = "N$ngrv, E$egrv";       
      }
    }
    $btype="ex";
    if ($et==2) {
      $btype="ho";
      $s_booking = $myrowex["booking"];
    }
    if ($et==3) {
      $btype="op";
    }
  }
} 

if (!($ex)) {
  $newuri = "/";
  Header( "HTTP/1.1 301 Moved Permanently" );
  Header( "Location: $newuri" );
  exit;
}

$t_r = split_descr($s_descr); //описание объекта

$photoimg = getfotoex($ex,1,"",1);

include('include/head01.php'); //должно стоять после всех мета-тегов

$class = "tl travell0t";

$descr_local = '';

$descr_local .= display_t_r($t_r, $class, 1);        

if ($ex_descr) {
  $descr_local .= "<div width=95% class=\"tm10 ttb ttr\"><p class=\"tp10\">$ex_descr</p></div>";
}

//
if($s_booking) {
  $descr_local .= "<div align=center><form name=reservation action=\"$s_booking\" target=_blank>\n";
  $descr_local .= "<input type=hidden name=aid value=337822>";
  $descr_local .= "<input class=\"ttb2 tzh t14 ttr tp10\" type=submit value=\"Забронировать онлайн на сайте Booking.COM\"></form></div>\n";
}
// 
// начало вывода
//
echo "<div class='container-fluid' style='width:100%;'>";

echo '<div class="row"><div class="col-md-12"><div class="panel"><div class="panel-heading"><h1>';
echo $s_ex.'</h1></div><div class="panel-body travell0 tj">';

if ($photoimg["foto1"]) {
  echo '<img src="'.$photoimg["foto1"].'" style="width: 100%; max-width: 270px; float: left; margin: 0 1rem 1rem 0;">';
}

echo $descr_local;

echo "<p class=\"$class\"><b>Рейтинг сайта: ".$ratingvalue."</b></p>";

echo "<p class=\"$class\"><b><a target=\"_blank\" href=\"/otklik.php/".$ex."\"> >>> Подробности, фото, отклики посетителей</a></b></p>";

echo "</div></div></div></div>\n"; 

echo "</div>\n <!-- wraper -->\n"; 
?>
</body>
</html>

