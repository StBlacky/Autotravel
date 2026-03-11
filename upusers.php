<?php
include_once('include/config.php');
include_once('include/func.php');
include('include/head01.php');

$sql1 = "update bl_users set user_trips = 0, user_orgtrips = 0,
          user_texts = 0, user_reports = 0, user_excitings = 0";
echo "$sql1<br>\n";
$result1 = $dbi->query($sql1);
#mysqli_free_result($result1);

$sql    = "update user_weight set count=0,weight=0 where sid <> 'N'";
echo "$sql<br>\n";
$result = $dbi->query($sql);
#mysqli_free_result($result);

$sql    = "SELECT cook, count(*) as cw from otklik where cook <> 'N' group by cook order by 2 desc";
$result = $dbi->query($sql);
while ($myrow = mysqli_fetch_array($result)) {
   $cook    = $myrow["cook"];
   $cw      = (int)$myrow["cw"];
   if ($cw > 1 ) {
    if (preg_match("/forum\:(\d+)\:(.+)/",$cook,$s_match)) {
      $sql1    = "update bl_users set user_excitings = $cw where user_id = " . $s_match[1];
      //echo "$sql1<br>\n";
    }
    else {
     $wt = log10($cw);
     $sql1    = "select id from user_weight where sid = '$cook'";
     $result1 = $dbi->query($sql1);
     if ($myrow1 = mysqli_fetch_array($result1)) {
      $uid = $myrow1["id"];
     }
     else {
       $uid = 0;
     }
     mysqli_free_result($result1);
     if ($uid) {
       $sql1    = "update user_weight set count = $cw, weight = $wt where id = $uid";
       //echo "$sql1<br>\n";
     }
     else {
       $sql1    = "insert into user_weight (sid,count,weight) values('$cook',$cw,$wt)";
       //echo "$sql1<br>\n";
     }

    }
   }
   $dbi->query($sql1);
   //mysqli_free_result($result1);
}
mysqli_free_result($result);

$sql    = "SELECT cook, count(*) as cw from l_reports where cook like 'forum:%' group by cook";
$result = $dbi->query($sql);
while ($myrow = mysqli_fetch_array($result)) {
   $cook    = $myrow["cook"];
   $cw      = (int)$myrow["cw"];
   if (preg_match("/forum\:(\d+)\:(.+)/",$cook,$s_match)) {
      $sql1    = "update bl_users set user_reports = $cw where user_id = " . $s_match[1];
      //echo "$sql1<br>\n";
      $result1 = $dbi->query($sql1);
      #mysqli_free_result($result1);
   }
}
mysqli_free_result($result);

$sql    = "SELECT author, count(*) as cw from foto where author like 'forum:%' group by author";
$result = $dbi->query($sql);
while ($myrow = mysqli_fetch_array($result)) {
   $cook    = $myrow["author"];
   $cw      = (int)$myrow["cw"];
   if (preg_match("/forum\:(\d+)\:(.+)/",$cook,$s_match)) {
      $sql1    = "update bl_users set user_fotos = $cw where user_id = " . $s_match[1];
      //echo "$sql1<br>\n";
      $result1 = $dbi->query($sql1);
      #mysqli_free_result($result1);
   }
}
mysqli_free_result($result);

$sql    = "SELECT cook, count(*) as cw from l_text where cook like 'forum:%' group by cook";
$result = $dbi->query($sql);
while ($myrow = mysqli_fetch_array($result)) {
   $cook    = $myrow["cook"];
   $cw      = (int)$myrow["cw"];
   if (preg_match("/forum\:(\d+)\:(.+)/",$cook,$s_match)) {
      $sql1    = "update bl_users set user_texts = $cw where user_id = " . $s_match[1];
      //echo "$sql1<br>\n";
      $result1 = $dbi->query($sql1);
      #mysqli_free_result($result1);
   }
}
mysqli_free_result($result);

$sql    = "SELECT iduser, count(*) as cw from trip_user where iduser > 0 group by iduser";
$result = $dbi->query($sql);
while ($myrow = mysqli_fetch_array($result)) {
   $uid    = $myrow["iduser"];
   $cw     = (int)$myrow["cw"];
   $sql1    = "update bl_users set user_trips = $cw where user_id = " . $uid;
   //echo "$sql1<br>\n";
   $result1 = $dbi->query($sql1);
   #mysqli_free_result($result1);
}
mysqli_free_result($result);

$sql    = "SELECT iduser, count(*) as cw from trip_user where iduser > 0
            and typeuser = 1 group by iduser";
$result = $dbi->query($sql);
while ($myrow = mysqli_fetch_array($result)) {
   $uid    = $myrow["iduser"];
   $cw     = (int)$myrow["cw"];
   $sql1    = "update bl_users set user_orgtrips = $cw where user_id = " . $uid;
   //echo "$sql1<br>\n";
   $result1 = $dbi->query($sql1);
   #mysqli_free_result($result1);
}
mysqli_free_result($result);

$sql    = "SELECT user_id, user_trips, user_orgtrips, user_posts, user_excitings,
            user_fotos, user_reports, user_texts from bl_users where user_id > 0";
$result = $dbi->query($sql);
while ($myrow = mysqli_fetch_array($result)) {
   $id  = $myrow["user_id"];
   $cw1 = (int)$myrow["user_posts"];
   $cw2 = (int)$myrow["user_excitings"];
   $cw3 = (int)$myrow["user_reports"];
   $cw4 = (int)$myrow["user_texts"];
   $cw5 = (int)$myrow["user_trips"];
   $cw6 = (int)$myrow["user_orgtrips"];
   $cw7 = (int)$myrow["user_fotos"];
   $cw = (
         $cw2 + $cw3 + 
         ($cw4 * 3) + ($cw6 * 3) + 
         ($cw5 / 3) + ($cw7 / 3) + ($cw1 / 3)
         );
   if ($cw < 1) {
     $cw = 1;
   }
   $wt = log10($cw);
   $sql1    = "update bl_users set user_weight = $wt where user_id = " . $id;
   //echo "$sql1<br>\n";
   $result1 = $dbi->query($sql1);
//   mysqli_free_result($result1);
}
mysqli_free_result($result);


$sql    = "delete from user_weight where weight=0 and sid <> 'N'";
$result = $dbi->query($sql);

$sql    = "delete from user_weight where sid like 'forum:%'";
$result = $dbi->query($sql);

$sql = "update bl_users set user_rank = 4 where user_posts > 9 and user_rank = 0";
$result = $dbi->query($sql);

$sql = "update bl_users set user_rank = 4 where user_weight > 1 and user_rank = 0";
$result = $dbi->query($sql);

echo "Done";

?>