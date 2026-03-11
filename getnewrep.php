<?php
include_once('include/config.php');
include_once('include/func.php');
include('include/head01.php');

$t = $_REQUEST["t"];  
$t = (int) mysafestring($t);
if ($t) {

 $sql2 = "select key_towns, nick, rid from l_reports where time > $t order by time desc";
 print "<p>$sql2</p>\n";
 $result2 = $dbi->query($sql2);

 while ($myrow2 = mysqli_fetch_array($result2)) {
     $nick      = $myrow2["nick"];
     $s_key     = $myrow2["key_towns"];
     $rid       = $myrow2["rid"];
     $s_key = getskeytext($s_key,$rid);
     print "<p>$s_key ($nick) $sitename/v_report.php/$rid</p>\n";
 }
 mysqli_free_result($result2);

}
else {
 print "<p>Надо было указать параметр ex</p>";
}

?>

