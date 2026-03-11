<?php

include_once('include/config.php');
include_once('include/func.php');
include_once('include/head00.php');

print "<h1>Города, не имеющие координат, объекты в которых имеют координаты:</h1>\n";

// Select all the rows in the markers table
$query = "SELECT towns.id as tid, towns.name as tname, towns.area as area,
          exciting.id as eid, exciting.descrip as descrip,
          exciting.Nshirota as Nshirota, exciting.Edolgota as Edolgota,
          exciting.name as ename FROM towns, exciting 
          WHERE exciting.town = towns.id
          and (not towns.id = 9999) 
          and towns.NShirota = 0 
          and exciting.NShirota > 1
          ORDER BY exciting.ID ASC";
$result = $dbi->query($query);

while ($row = @mysqli_fetch_assoc($result)) {
    $tname  = $row["tname"];
    $tid    = $row["tid"];
    $ename  = $row["ename"];
    $eid    = $row["eid"];
    $edg    = $row["Edolgota"];
    $nsh    = $row["Nshirota"];
    $caddr  = $row["descrip"];
    print "-- $tid : $tname, <br>\n -- $eid : $ename<br>\n";
    print "update towns set nshirota = $nsh, edolgota = $edg where id=$tid ;\n<br>";
    print "-- [<a target=_blank href=http://maps.google.com/maps?ll=".$nsh.",".$edg."&spn=0.05,0.1&t=k&hl=ru&t=h>Карта&nbsp;Гугль</a>]<br>\n";
}
?>
