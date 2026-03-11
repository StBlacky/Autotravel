<?php
include_once('include/config.php');
include_once('include/func.php');
include('include/head01.php');

$sql="select f.file as file, f.town as ftown, f.exciting as fexciting,
 e.town as etown, f.type as ftype, e.type as etype 
 from foto f, exciting e where e.id = f.exciting and e.mainid>=0";
$result = $dbi->query($sql); 

while ($myrow = mysqli_fetch_array($result)) {
 $file       = $myrow["file"];
 $fexciting  = $myrow["fexciting"];
 $ftown      = $myrow["ftown"];
 $etown      = $myrow["etown"];
 $ftype      = $myrow["ftype"];
 $etype      = $myrow["etype"];
 if ($ftown != $etown) {
  echo "$file  $fexciting f=$ftown e=$etown<br>\n";
  $sqlu = "update foto set town=$etown where file='$file'";
  $result_u = $dbi->query($sqlu); 
 }
 if ($ftype != $etype) {
  $sqlu = "update foto set type=$etype where file='$file'";
  $result_u = $dbi->query($sqlu); 
 }
}

$tim = (time() - 100000000);
$sql2 = "select id, votes, rate, wrate from exciting where mainid>=0";
$result2 = $dbi->query($sql2);

while ($myrow2 = mysqli_fetch_array($result2)) {
   //echo ". ";
   $id   = $myrow2["id"];
   $oldvotes   = $myrow2["votes"];
   $oldball    = $myrow2["rate"];
   $oldwball   = $myrow2["wrate"];
   $cnt  = 0;
   $wball= 0;
   $ball = 0;

   $sql  = "select cook, count(*) as c from otklik where exciting = $id
            and cook like 'forum%' group by cook";
   $result = $dbi->query($sql);
#  echo "$sql<br>\n";
   while ($myrow = mysqli_fetch_array($result)) {
     $cook  = $myrow["cook"];
     $count = $myrow["c"];
     if ($count > 1) {
      echo "<br>!!! exciting $id cook $cook count $count <br>\n";
     }
   }
   mysqli_free_result($result);

   $sql  = "select mark, cook from otklik where exciting = $id";
   $result = $dbi->query($sql);
#   echo "$sql<br>\n";
   while ($myrow = mysqli_fetch_array($result)) {
     $cook  = $myrow["cook"];
     $mark  = $myrow["mark"];
     $weight = 0;
     $fuid = 0;
     if (preg_match("/forum\:(\d+)\:(.+)/",$cook,$s_match)) {
      $fuid = $s_match[1]; 
      $sql3    = "select user_weight as weight from bl_users where user_id = " . $fuid;
     }
     else {
      $sql3  = "select weight from user_weight where sid = '$cook'";
     }
     $result3 = $dbi->query($sql3);
     if ($myrow3 = mysqli_fetch_array($result3)) {
      $weight= (double)$myrow3["weight"];
     }
     mysqli_free_result($result3);
#     echo "$id $cnt $cook $mark $weight<br>\n";
     $cnt   = $cnt + 1;
     $ball  = $ball + $mark - 3;
     $wball = $wball + (($mark - 3) * ($weight + 0.01));
   }
   mysqli_free_result($result);
   if (($oldvotes <> $cnt) or ($oldball <> $ball) or ($oldwball <> $wball)) {
    $sql = "update exciting set wrate=$wball, rate=$ball, votes=$cnt where id=$id";
    $result = $dbi->query($sql);
//    mysqli_free_result($result);
//    print "<p>$sql</p>\n";
   }
}
mysqli_free_result($result2);

$sql2 = 'select a.id, a.votes as oldvotes, a.rate as oldrate, a.wrate as oldwrate, '
        .' b.id as bid, b.votes, b.rate, b.wrate from exciting a '
        .' join exciting b on b.id = (select c.id from exciting c where c.mainid=a.id group by c.wrate desc limit 1) '
        .' where a.mainid<0 ';
$result2 = $dbi->query($sql2);
while ($myrow2 = mysqli_fetch_array($result2)) {
   //echo ". ";
   $id   = $myrow2["id"];
   $oldvotes   = $myrow2["oldvotes"];
   $oldball    = $myrow2["oldrate"];
   $oldwball   = $myrow2["oldwrate"];
   $votes   = $myrow2["votes"];
   $ball    = $myrow2["rate"];
   $wball   = $myrow2["wrate"];
   if (($oldvotes <> $cnt) or ($oldball <> $ball) or ($oldwball <> $wball)) {
    $sql = "update exciting set wrate=$wball, rate=$ball, votes=$cnt where id=$id";
    $result = $dbi->query($sql);
   } 
}
mysqli_free_result($result2);

   
   
$sql2 = "select id from areas";
$result2 = $dbi->query($sql2);

while ($myrow2 = mysqli_fetch_array($result2)) {
   $id   = $myrow2["id"];
   $ids  = "";
   $sql  = "select id from towns where area = $id";
   $result = $dbi->query($sql);
   while ($myrow = mysqli_fetch_array($result)) {
    if ($ids) {
     $ids = $ids . "," . $myrow["id"];
    }
    else {
     $ids = $myrow["id"];
    }
   }
   mysqli_free_result($result);
   if ($ids) {
    $sql  = "update exciting set area=$id where town in ($ids)";
    //echo "<p>$sql</p>\n";
    $result = $dbi->query($sql);
   }
}
mysqli_free_result($result2);

echo "<br>Done.\n";

?>

