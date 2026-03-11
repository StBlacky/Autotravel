<?php
include_once('include/config.php');
include_once('include/func.php');
include('include/head01.php');

$sqlt = "select id,name from towns";
$resultt = $dbi->query($sqlt);

while ($myrowt = mysqli_fetch_array($resultt)) {
 $t_name_arr = preg_split ("/\//" , $myrowt["name"]);
 $name = $t_name_arr[0];
 if ($name == "") {
  $name = $myrowt["name"];
 }
 $name=trim($name);
 if ($name != "") {
  $tarr[$name]=$myrowt["id"];
 }
}
mysqli_free_result($resultt);

$sql2 = "select rid, key_towns from l_reports where not (key_towns='table')";
$result2 = $dbi->query($sql2);

while ($myrow2 = mysqli_fetch_array($result2)) {
 $rid   = $myrow2["rid"];
 $key   = $myrow2["key_towns"];
 if (!($key == 'table')) {
   $ak = explode(',',$key);
   $ai = array();
   $success = "";
   foreach ($ak as $k) {
    $id   = "";
    $name = trim($k);
    $id  = $tarr[$name];
    if ($id) {
     array_push($ai, $id);
    }
    else {
     $success = $success . " " . $name;
    }
   }
   if (!($success)) {
#    echo "success : <br>\n";
#    print_r($ai);
     echo "";
   }
   else {
    //echo "$rid: $key<br>\n";
    //echo "fail $success\n";
    //echo "<br>\n";
   }

   $sql  = "delete from t_reports where rid=$rid";
   $dbi->query($sql);
   //mysqli_free_result($result);
   foreach ($ai as $k) {
    $sql  = "insert into t_reports (rid, tid) values($rid,$k)";
    #echo "$sql<br>\n";
    $result = $dbi->query($sql);
   }
   if (!($success)) {
    $sql  = "update l_reports set key_towns='table' where rid=$rid";
#    echo "$sql<br>\n";
#    $result = $dbi->query($sql);
   }

  }
}
mysqli_free_result($result2);


$sql2 = "select id, hide, rate from towns";
$result2 = $dbi->query($sql2);

while ($myrow2 = mysqli_fetch_array($result2)) {
 $id   = $myrow2["id"];
 if ($id <> 9999) {
   $hide = $myrow2["hide"];
   $rate = $myrow2["hide"];
   $sql  = "select count(*) as cnt, sum(wrate) as rate from exciting where town = $id and mainid>=0";
   $cnt  = 0;
   $nrate  = 0;
   $wrate  = 0.0;
   $result = $dbi->query($sql);
   if ($myrow = mysqli_fetch_array($result)) {
    $cnt = $myrow["cnt"];
    $wrate = (float)$myrow["rate"];
    $nrate = $myrow["rate"];
    $nrate = ($cnt * 10) + $nrate;
   }
   mysqli_free_result($result);
   $newhide = 1;
   if ($cnt == 1) {
    $newhide = "2";
   }
   if ($cnt > 1) {
    $newhide = "0";
   }
   if (($hide <> $newhide) or ($rate <> $nrate)) {
    $sql = "update towns set hide=$newhide, rate=$nrate, wrate=$wrate where id=$id";
    $result = $dbi->query($sql);
//    mysqli_free_result($result);
    //print "<p>$sql</p>\n";
   }
 }
}
mysqli_free_result($result2);

?>

