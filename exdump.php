<?php
include_once('include/func.php');
include_once('include/head00.php');
$sql2 = "select e.id as ex, e.name as exname, e.descrip as descr, e.booking as booking,
         e.town as town, e.type as type, e.Nshirota as shirota,
         e.Edolgota as dolgota, type.name as typename
         from exciting e, type where e.type = type.id";
$result2 = $dbi->query($sql2);
$noout=array("\t","\n","\r");
header("Content-type: text/tab-separated-values");
header("Content-Disposition: attachment; filename=exdump.csv");

while($myrow = mysqli_fetch_array($result2)) {
 $ex = $myrow["ex"];
 $exname = $myrow["exname"];
 $town = $myrow["town"];
 $typename = $myrow["typename"];
 $descr = str_replace($noout," ",$myrow["descr"]);
 $ngrv = degreestotext($myrow["shirota"]);
 $egrv = degreestotext($myrow["dolgota"]);
 $s_booking = $myrow["booking"];
 $photocount=countfotoexc($ex);
 $tname=gettownnamearr($town);
 echo "$ex\t$typename\t$exname\t$tname[0]\t$tname[2]\t$descr\t$s_booking\n";

}
?>
