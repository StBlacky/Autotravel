<?php
include_once('include/config.php');
include_once('include/func.php');
include('include/head01.php');

$rcvrid = (int)$_REQUEST['rid'];
$rcvcity = mysafestring($_REQUEST['city']);
$rcvid = (int)$_REQUEST['town'];
$rcvname = mysafestring($_REQUEST['name']);

if ($rcvrid>0) {
 $sql = "update bookingh set townid=".$rcvid.", city='".$rcvname."' where bookingid=$rcvrid and city='".$rcvcity."'";
 echo $sql."<br>\n";
 $result = mysql_query($sql, $db);
 mysql_free_result($result);
}
$sql2 = "select bookingid, name, link, city, address, description, Nshirota, Edolgota from bookingh where excitingid is null";
$result2 = mysql_query($sql2, $db);

$ai = Array();

echo "<h1>Соответствие линков:</h1>";
while ($myrow2 = mysql_fetch_array($result2)) {
 $rid   = $myrow2["bookingid"];
 $link  = booklink($myrow2["link"]);
 $nshb  = $myrow2["Nshirota"];
 $edgb  = $myrow2["Edolgota"];
 $id="";
 $sql  = "select id from exciting where booking='".$link."'";
 $result = mysql_query($sql, $db);
 while ($myrow = mysql_fetch_array($result)) {
  $id   = $myrow["id"];
 }
 if ($id) {
  $ai[$rid]=$id;
 }
}

mysql_free_result($result2);


foreach ($ai as $key => $value) {
  $sql = "update bookingh set excitingid=$value where bookingid=$key";
  echo $sql."<br>\n";
  $result = mysql_query($sql, $db);
  mysql_free_result($result);
}

?>

