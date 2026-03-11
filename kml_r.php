<?php
//
// Выгрузка в kml всех объектов с координатами
// списку объектов ($ids=)
// списку городов ($tlist)
//

include_once('include/config.php');
include_once('include/func.php');
include_once('include/head00.php');

$r_types=array();
$r_types[0] = "Города";
$sql_fd = "select name, id from type";
$result_fd = $dbi->query($sql_fd);
while ($row_trip = mysqli_fetch_array($result_fd)) {
    $id_fd = $row_trip["id"];
    $r_types[$id_fd] = $row_trip["name"];
}
mysqli_free_result($result_fd);


$oneid = 0;

$resheader = $_REQUEST["resheader"];
$resheader = mysafestring($resheader);
$tlist = $_REQUEST["tlist"];
$tlist = mysafestring($tlist);


if ($tlist) {
 $tids = explode(',',$tlist);
 for ($ii=0; $ii<sizeof($tids); $ii++) {
  $tids[$ii] = (int)$tids[$ii];
 }
 $tlist = join($tids,",");
}

$nmarray=array();

$sql_x="select t.Nshirota as tNshirota, t.Edolgota as tEdolgota, t.id as etown,
        t.name as tname, t.descrip as tdescrip, t.hide as thide
        from towns t
        where t.id in ($tlist)";

if (!$resheader) {
  $resheader = "Выбранные объекты на сайте autotravel.ru";
}

$tcoords=Array();

 # echo $sql_e;
$result_x = $dbi->query($sql_x);
if($myrow_x = mysqli_fetch_array($result_x)) {
  do {
   $t_name   = $myrow_x["tname"];
   $t_id     = $myrow_x["etown"];
   $Nshirotat = (float)$myrow_x["tNshirota"];
   $Edolgotat = (float)$myrow_x["tEdolgota"];

   if (($Nshirotat > 0) and ($Edolgotat > 0)) {
// города. Всегда должны иметь координаты
       $tcoords[$t_id] = $Nshirotat . "," . $Edolgotat;
       $nmarray["attn$t_id"] = "<name>" . kmlstring($t_name) . "</name>\n" .
           "<styleUrl>#att</styleUrl>\n".
           "<Point><coordinates>$Edolgotat,$Nshirotat,0</coordinates></Point>\n";
   }
  } while($myrow_x = mysqli_fetch_array($result_x));
}
mysqli_free_result($result_x);
//

// маршрут по городам
if ((sizeof($tids) > 0)) {
 $linemap = "";
  for ($ii=0; $ii<sizeof($tids); $ii++) {
   $tcur = $tids[$ii];
   $ccur = split(',',$tcoords[$tcur]);
   if (($ccur[0] > 0) and ($ccur[1] > 0) ) {
       $linemap .= $ccur[1] . "," . $ccur[0] . ",0\n";
   }
  }
}


if ($nmarray) {
 reset($nmarray);

//
// KML begin
//
  header("Content-type: application/vnd.google-earth.kml+xml");
  header("Content-Disposition: attachment; filename=Autotravel_waypoints.kml");
  echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
  echo "<kml xmlns=\"http://earth.google.com/kml/2.1\">\n<Document>\n";
  echo "<name>" . kmlstring($resheader) . "</name>\n";
  echo "<Style id=\"att\">\n<IconStyle>\n<Icon>\n<href>$sitename/jpg/anchor-t.png</href>\n</Icon>\n</IconStyle>\n</Style>\n";
  while ( list($tnmkey,$nmvalue)=each($nmarray) ) {
   echo "<Placemark id=\"$tnmkey\">\n$nmvalue\n</Placemark>\n";
  }
  if ($linemap)  {
   echo "<Placemark>\n<name>" . kmlstring("Трасса") . "</name>\n<Style><LineStyle><color>88ff88ff</color></LineStyle></Style>\n";
   echo "<LineString><tessellate>1</tessellate><coordinates>\n";
   echo $linemap;
   echo "</coordinates>\n</LineString>\n</Placemark>\n";
  }
  echo "</Document>\n</kml>\n";
//
// KML end.
//


}

?>

