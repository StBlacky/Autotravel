<?php
include_once('include/config.php');
include_once('include/func.php');
include('include/head01.php');

$rcvrid = (int)$_REQUEST['rid'];
$rcvcity = mysafestring($_REQUEST['city']);
$rcvid = (int)$_REQUEST['exciting'];

if (($rcvrid>0) and (!($rcvid>0))) {
 $sql="SELECT (max(id)+1) as mid FROM `exciting` WHERE id < 16000"; 
 $result = mysql_query($sql, $db);
 $newid=0;
 if ($myrow = mysql_fetch_array($result)) {
  $newid = $myrow["mid"];
 }
 //echo "$sql -> $newid<br>\n";
 mysql_free_result($result);
 $sql = "select bookingid, name, link, city, address, description, Nshirota, Edolgota, townid "
        . "from bookingh where excitingid is null and townid is not null and bookingid=$rcvrid ";
 $rid = "";
 $result = mysql_query($sql, $db);
 if ($myrow = mysql_fetch_array($result)) {
  $rid   = $myrow["bookingid"];
  $link  = booklink($myrow["link"]);
  $nameb  = $myrow["name"];
  $cityb  = $myrow["city"];
  $addrb  = $myrow["address"];
  $townb  = $myrow["townid"];
  $descb  = $myrow["description"];
  $nshb  = $myrow["Nshirota"];
  $edgb  = $myrow["Edolgota"];
  $dnew = $descb . "<BR>" . "<b>Адрес:</b> ";
  if ($cityb) {
   $dnew .= trim($cityb).", ";     
  }
  $dnew .= trim($addrb);
 }
 mysql_free_result($result);
 //echo "$sql -> $link<br>\n";
 $sql="SELECT area FROM `towns` WHERE id=$townb"; 
 $result = mysql_query($sql, $db);
 $area=0;
 if ($myrow = mysql_fetch_array($result)) {
  $area = $myrow["area"];
 }
 mysql_free_result($result);
 
 if ($rid>0) {
  $sql="SELECT id FROM `exciting` WHERE booking='".$link."'"; 
  $result = mysql_query($sql, $db);
  $oldid=0;
  if ($myrow = mysql_fetch_array($result)) {
   $oldid = $myrow["id"];
  }
  mysql_free_result($result);
  if ($oldid>0) {
    $rid = 0;
    $rcvid = $oldid;
    $city = $cityb;
  }
 }
 
 if ($rid > 0) {
  $sql_in = "insert into exciting
             (id,    town,   name,   descrip, type, kind, Nshirota, Edolgota, area, booking, emoderated, exact, cook, cooked) values
             ($newid,$townb,'$nameb','$dnew','2','',
                 '$nshb','$edgb',$area,'$link', 0, 0, '','')";
  echo $sql_in."<br>\n";
  $result = mysql_query($sql_in, $db);

  $sql="SELECT id FROM `exciting` WHERE booking='".$link."'"; 
  $result = mysql_query($sql, $db);
  $oldid=0;
  if ($myrow = mysql_fetch_array($result)) {
   $oldid = $myrow["id"];
  }
  if ($oldid>0) {
    $rid = 0;
    $rcvid = $oldid;
    $city = $cityb;
  }
 }
}

if (($rcvrid>0) and ($rcvid>0)) {
 $sql="SELECT link FROM `bookingh` WHERE bookingid=$rcvrid and city='".$rcvcity."'";
 $result = mysql_query($sql, $db);
 $link="";
 if ($myrow = mysql_fetch_array($result)) {
   $link = booklink($myrow["link"]);
 }

 if ($link) {    
  $sql = "update bookingh set excitingid=".$rcvid." where bookingid=$rcvrid and city='".$rcvcity."'";
  //echo $sql."<br>\n";
  $result = mysql_query($sql, $db);
  $sql = "update exciting set booking='".$link."' where id='".$rcvid."'";
  //echo $sql."<br>\n";
  $result = mysql_query($sql, $db);
 }
 // надо ещё booking на линк обновить!!!
 
}


$sql2 = "select bookingid, name, link, city, address, description, Nshirota, Edolgota, townid "
        . "from bookingh where excitingid is null and townid is not null order by city";
$result2 = mysql_query($sql2, $db);

$rid = "";
$id  = "";
$ei  = Array();
$ti  = Array();

echo "<h1>Соответствие гостиницы:</h1>";

if ($myrow2 = mysql_fetch_array($result2)) {
 $rid   = $myrow2["bookingid"];
 $link  = booklink($myrow2["link"]);
 $nameb  = $myrow2["name"];
 $cityb  = $myrow2["city"];
 $addrb  = $myrow2["address"];
 $townb  = $myrow2["townid"];
 //echo "$townb<br>\n";
 $descb  = $myrow2["description"];
 $nshb  = $myrow2["Nshirota"];
 $edgb  = $myrow2["Edolgota"];
 $dnew = $descb . "<BR>" . "<b>Адрес:</b> ";
 if ($cityb) {
   $dnew .= trim($cityb).", ";     
 }
 $dnew .= trim($addrb);
 echo "<b>".$link."</b><br><b>".$nameb."</b><br>$dnew<br>";
 $diff = 0.05;
 $sql  = "select id, Nshirota, Edolgota from exciting where (Nshirota<".($nshb+$diff).
         ") and (Nshirota>".($nshb-$diff).") and (Edolgota<".($edgb+$diff).") and (Edolgota>".($edgb-$diff).") ".
         " and type=2 and booking='' ";
 $result = mysql_query($sql, $db);
 while ($myrow = mysql_fetch_array($result)) {
  $id   = $myrow["id"];
  array_push($ei,$id);
 } 
 
 array_push($ti,$townb);
 
 $sql = "select id, Nshirota, Edolgota from towns where (Nshirota<".($nshb+$diff).
         ") and (Nshirota>".($nshb-$diff).") and (Edolgota<".($edgb+$diff).") and (Edolgota>".($edgb-$diff).") ";
 //echo "$sql<br>\n";
 while ($myrow = mysql_fetch_array($result)) {
  $id   = $myrow["id"];
  array_push($ti,$id);
 } 
 //
 
 $tlist = implode(",",$ti);
 $sql  = "select id, booking from exciting where town in (".$tlist.") and type=2";
 //echo "$sql<br>\n";
 $result = mysql_query($sql, $db);
 while ($myrow = mysql_fetch_array($result)) {
  $id   = $myrow["id"];
  if (!(trim($myrow["booking"]))) {
   array_push($ei,$id);
  }
 }
 //
 $elist = implode(",",$ei);
 if (!(empty($elist))) {
  echo "<table border=1 width=100%>";

  $sql  = "select id, name, descrip from exciting where id in (".$elist.")";
  //echo "$sql<br>\n";
  $result = mysql_query($sql, $db);
  while ($myrow = mysql_fetch_array($result)) {
   $id   = $myrow["id"];
   echo "<tr><td>";
   echo "<b>".$myrow['name']."</b><br>".$myrow["descrip"];
   echo "</td><td>";
   echo "<form action=".$_SERVER["PHP_SELF"]." method=post >\n";
   echo "<INPUT name=rid type=hidden value='$rid'>\n";
   echo "<INPUT name=exciting type=hidden value='$id'>\n";
   echo "<INPUT name=city type=hidden value='".$cityb."'>\n";
   echo "<INPUT type=submit value='Точное соответствие'>\n";
   echo "</form>\n";
   echo "</td></tr>\n";
  }
  echo "</table>";
 }
}

echo "<form action=".$_SERVER["PHP_SELF"]." method=post >\n";
echo "<INPUT name=rid type=hidden value='$rid'>\n";
echo "<INPUT name=city type=hidden value='".$cityb."'>\n";
echo "<INPUT type=submit value='Новый объект !!!'>\n";
echo "</form>\n";

?>

