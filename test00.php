<?php

//
// Передаётся список id примечательностей
// через запятую в переменной $ids и заголовок в переменной $resheader
//
// Результаты отсортированы по алфавиту.
//
include_once('include/config.php');
include_once('include/func.php');

$ids = $_REQUEST["ids"];
$ids = mysafestring($ids);
$photo = $_REQUEST["photo"];
$photo = mysafestring($photo);
$otk = $_REQUEST["otk"];
$otk = mysafestring($otk);

$reclama01 = "<p class=travell0><center>Материалы сайта \"Автопутешествия по России\" <b>www.autotravel.ru</b><br><br></center></p>\n";
$title = $resheader;
include("include/head01.php");

$_user_is_admin = 0;

if (!(empty($ids))) {
  $idarr = explode(',', $ids);
  $ids = implode(",",$idarr);
}

echo "<h3>$resheader</h3>\n";

if ($_salp_mode) {
  $sql = "SELECT e.id as id, e.rate as rate, e.wrate as wrate, e.town as town,
        e.name as name,e.descrip as descrip,e.type as type,e.kind as kind,
        t.name as townname,Right( e.name, length( e.name ) - locate( '\"', e.name )) AS loc,
        e.Nshirota as Nshirota, e.Edolgota as Edolgota, a.name as aname
        FROM exciting e, towns t, areas a
        where e.id in ($ids) and t.id = e.town and a.id = t.area order by
        townname asc, loc asc";
} else {
  $sql = "SELECT e.id as id, e.rate as rate, e.wrate as wrate, e.town as town,
        e.name as name, e.descrip as descrip, e.type as type, e.kind as kind,
        t.name as townname, e.Nshirota as Nshirota, e.Edolgota as Edolgota,
        a.name as aname
        FROM exciting e, towns t, areas a
        where e.id in ($ids) and t.id = e.town and a.id = t.area
        order by wrate desc, townname asc";
}
if (!(empty($ids))) {
 $result = $dbi->query($sql);
 if ($myrow = mysqli_fetch_array($result)) {
  echo "<p class=travell0>";
  if ($_salp_mode) {
    echo "Сортировка - по алфавиту";
  } else {
    echo "Сортировка - по рейтингу";
  }
  echo "</p>";
  $headt = "<table border=1 width=95% cellpadding=8><Tbody class=travell0>";
  $headt = $headt . "<tr><TD align=center width=\"175\">наименование";
  $headt = $headt . "</TD><TD align=center>описание</td></tr>";
  echo $reclama01;
  echo $headt;
  do {
    $id = $myrow["id"];
    $town = $myrow["town"];
    $area = $myrow["aname"];
    $rate = $myrow["rate"];
    $wrate = (float) $myrow["wrate"];
    $wrate = sprintf("%01.2f", $wrate);
    $ngr = $myrow["Nshirota"];
    $egr = $myrow["Edolgota"];
    $ngrw = degreestotext($ngr);
    $egrw = degreestotext($egr);
    $townname = $myrow["townname"];
    $type = $myrow["type"];
    $kind = $myrow["kind"];
    $name = $myrow["name"];
    $desc = $myrow["descrip"];
    $s_rek = getreklama($id, $photo);
    echo"<tr><TD align=center width=\"175\">";
    if ($s_rek) {
      echo "<b>$name</b>";
    } else {
      echo "$name";
    }
//		if ($townname <> $name) {
    echo " ($townname, $area) <br>Рейтинг: $wrate\n";
//		}
    if ((($ngr > 0) || ($ngr < 0)) && (($egr > 0) || ($egr < 0))) {
      echo "<br><b>N</b>$ngrw, <b>E</b>$egrw";
    }
    echo"</TD><TD align=center>\n";
    if ($s_rek <> "") {
      echo "<table border=1 cellpadding=8 width=97%><tr><td class=travell0><b>Официальная информация: </b><br>$s_rek</td></tr></table>";
    }
    echo "$desc\n";
    if ($photo == '1') {
      $photoimg = getfotoex($id, 3, "", 0 );
      if ($photoimg) {
        echo "<br><div align=center>$photoimg</div><br>";
      }
    }
    $otkl_rep = "";
    if ($otk == '1') {
      $otkl_rep = getotklik($id, "p", "");
    }
    if ($otkl_rep) {
      echo "</td></tr><tr><td class=travell0 colspan=2>\n";
      echo $otkl_rep;
    }
    echo "</td></tr>\n";
  } while ($myrow = mysqli_fetch_array($result));
  echo "</tbody></table>\n";
 }
} else {
  echo "Ошибка. Сообщите, пожалуйста, на форуме.";
}
?>

