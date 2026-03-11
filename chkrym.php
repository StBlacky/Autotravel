<?php
include_once('include/config.php');
include_once('include/func.php');
include('include/head01.php');

$sql = "select e.descrip as edescrip, e.id as eid, t.id as tid from exciting e, towns t where e.town=t.id and t.area=80";
$result = mysql_query($sql, $db);
while ($row = mysql_fetch_array($result)) {
 $addr = $row["edescrip"];
 echo "$addr<br>\n";
}


?>