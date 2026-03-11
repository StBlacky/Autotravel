<?php
include_once('include/config.php');
include_once('include/func.php');
include('include/head01.php');

$ex = $_REQUEST["ex"];  
$ex = (int) mysafestring($ex);
if ($ex) {

 $sql2 = "select id, town from exciting where id >= '$ex' and mainid>=0 order by id";
 print "<p>$sql2</p>\n";
 $result2 = $dbi->query($sql2);

 while ($myrow2 = mysqli_fetch_array($result2)) {
     $id   = $myrow2["id"];
     $town = $myrow2["town"];
     $exname = getexname($id);
     $tname =  gettownname2($town);
     print "<p>$exname ($tname) $sitename/otklik.php/$id</p>\n";
 }
 mysqli_free_result($result2);

}
else {
 print "<p>Надо было указать параметр ex</p>";
}

?>

