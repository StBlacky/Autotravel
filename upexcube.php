<?php
include_once('include/config.php');
include_once('include/func.php');
include('include/head01.php');

$sql="select id, town, area, type, kind from exciting where mainid>=0";
$result = $dbi->query($sql); 

while ($myrow = mysqli_fetch_array($result)) {
 $arr = Array();
 if (($myrow["type"]==1)) {
   $arr = split(',',$myrow["kind"]) ;
 }
 extypes_upd($myrow["id"],$myrow["town"],$myrow["area"],$myrow["type"],$arr);
 echo ".";
}

echo "<br>Done.\n";

?>

