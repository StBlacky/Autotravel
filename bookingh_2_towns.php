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
$sql2 = "select bookingid, name, link, city, address, description, Nshirota, Edolgota from bookingh where excitingid is null and townid is null";
$result2 = mysql_query($sql2, $db);

$ai = Array();
$bi = Array();

echo "<h1>Соответствие городов:</h1>";
echo "<table class=travel0 border=1 width=100%>";
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
  echo "<tr><td>!!! ".$link."</td><td></td><td></td></tr>\n";
  $ai[$rid]=$id;
 }
 mysql_free_result($result);
 if (!($id)) {
  $id = "";
  $diff = 0.05;
  $sql  = "select id, name, Nshirota, Edolgota from towns where (Nshirota<".($nshb+$diff).
         ") and (Nshirota>".($nshb-$diff).") and (Edolgota<".($edgb+$diff).") and (Edolgota>".($edgb-$diff).") ";
  $result = mysql_query($sql, $db);
  while ($myrow = mysql_fetch_array($result)) {
   $id   = $myrow["id"];
   $town  = $myrow["name"];
   if ($town === $city) {
     $bi[$rid] = $id;  
   }
   else {
    echo "<tr><td>".$link."</td><td>";
    echo $myrow2['city']." => ".$id.",".$town;
    echo "</td><td>";
    //echo "<form action=".$_SERVER["PHP_SELF"]." method=post >\n";
    //echo "<INPUT name=rid type=hidden value='$rid'>\n";
    //echo "<INPUT name=town type=hidden value='$id'>\n";
    //echo "<INPUT name=city type=hidden value='".$myrow2['city']."'>\n";
    //echo "<INPUT name=name type=hidden value='".$town."'>\n";
    //echo "<INPUT type=submit value='Точное соответствие'>\n";
    //echo "</form>\n";
    echo "</td></tr>\n";
   }
  }
  if (!($id)) {
    echo "<tr><td>".$link."</td><td>";
    echo $myrow2['city'];
    echo "</td><td>";
    echo "</td></tr>\n";
  }
  mysql_free_result($result);
 }
}

mysql_free_result($result2);

echo "</table>";

foreach ($ai as $key => $value) {
  $sql = "update bookingh set excitingid=$value where bookingid=$key";
  //echo $sql."<br>\n";
  $result = mysql_query($sql, $db);
  mysql_free_result($result);
}

foreach ($bi as $key => $value) {
  $sql = "update bookingh set townid=$value where bookingid=$key";
  //echo $sql."<br>\n";
  $result = mysql_query($sql, $db);
  mysql_free_result($result);
}

?>

