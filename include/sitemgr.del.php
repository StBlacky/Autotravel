<?php

 if ($admt == 'delcomment') {
  if ($_user_is_admin == '1') {
   $scook  = mysafestring($_REQUEST["scook"]);
   $stime  = (int) $_REQUEST["stime"];
   $excid  = (int) $_REQUEST["exc"];
   if (($stime) && ($excid)) {
    $sql="select nick,cook,descrip,mark,time from otklik where cook='$scook' and exciting=$excid and time=$stime";
    $xnick = "";
    $xcook = "";
    $xtime = "";
    $xmark = "";
    $xdescrip = "";
    $result = $dbi->query($sql);
    if($myrow = mysqli_fetch_array($result)) {
      $xnick    = $myrow["nick"];
      $xmark    = $myrow["mark"];
      $xtime    = $myrow["time"];
      $xcook    = $myrow["cook"];
      $xdescrip = $myrow["descrip"];
    }
    mysqli_free_result($result);
    if ($xcook) {
     $sql_in="delete from otklik where cook='$scook' and exciting=$excid and time=$stime";
     $result_in = $dbi->query($sql_in);
     $sql_in = "insert into history (tblname,cook,ip,id,field,valueold,valuenew) 
                values ('otklik','$ctext','$ipp',$excid,'nick,time,mark,cook,descrip','$xnick,$xtime,$xmark,$xcook,$xdescrip','')";
     $result_in = $dbi->query($sql_in);
     echo "<br>\nУдален отклик от $xnick ($xcook) <<$xdescrip>>\n";
    }
   }
   echo "<br><a href=/otklik.php/$excid>Обратно к откликам</a>\n";
  }
 }

 if ($admt == 'delreport') {
  if ($_user_is_admin == '1') {
   $rid  = (int) $_REQUEST["rid"];
   if (($rid)) {
    $sql = "select * from l_reports where rid = $rid";
    $result = $dbi->query($sql);
    if($myrow = mysqli_fetch_array($result)) {
	$s_key = $myrow["key_towns"];
	$s_spring = $myrow["spring"];

	$s_nick = $myrow["nick"];

	$s_month = $myrow["month"];

	$s_year = $myrow["year"];

	$s_rep = $myrow["report"];

	$s_cook = $myrow["cook"];

	$s_time = $myrow["time"];

    } 
    $sql_in="delete from l_reports where rid='$rid'";
    $result_in = $dbi->query($sql_in);
    $sql_in = "insert into history (tblname,cook,ip,id,field,valueold,valuenew) 
                values ('l_reports','$ctext','$ipp',$rid,'nick,time,cook,report','$s_nick|$s_time|$s_cook|$s_rep','')";
    $result_in = $dbi->query($sql_in);
   echo "<br>Отчет удален\n";
   }
  }
 }


?>
