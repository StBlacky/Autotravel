<?php

include_once('include/config.php');
include_once('include/func.php');
include_once('include/head00.php');

print "<h1>Города, не имеющие координат, но содержащие объекты</h1>\n";

// Select all the rows in the markers table
$query = "SELECT *
          FROM towns  WHERE towns.NShirota < 1 and (not towns.id = 9999) order by towns.id asc";
$result = $dbi->query($query);

while ($row = @mysqli_fetch_assoc($result)) {
    $tname  = $row["name"];
    $tid    = $row["id"];
    $edg    = $row["Edolgota"];
    $nsh    = $row["Nshirota"];
    $area   = $row["area"];
    $aname  = getareaname($area);

 $query2 = "SELECT id 
           FROM exciting WHERE town = $tid";
 $result2 = $dbi->query($query2);
 if ($row2 = @mysqli_fetch_assoc($result2)) {
  print "$tid : $tname ($aname) \n ";
  print "[<a target=_blank href=/town.php/$tid>Город на сайте</a>] ";
  print "[<a target=_blank href=/testcc.php?tid=$tid>Геокод</a>]<br>\n";
  mysqli_free_result($result2);
  print "<hr>\n";
 }
}
?>
