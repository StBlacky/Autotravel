<?php                                       


///////////////////////////////////////////////////////////

 if (($exciting_enable) && ($admt == 'otklik')) {
  $submit  = mysafestring($_REQUEST['s']);

  if ($submit=="delete") {
   $otime   = (int)mysafestring($_REQUEST['otime']);
   $oobj    = (int)mysafestring($_REQUEST['oex']);
   $sql2    = "delete from otklikm where exciting=$oobj and time=$otime";
   $result2 = $dbi->query($sql2);
  }

  if ($submit=="add") {
   $otime   = (int)mysafestring($_REQUEST['otime']);
   $oobj    = (int)mysafestring($_REQUEST['oex']);
   $sql2    = "select * from otklikm where exciting=$oobj and time=$otime";
   echo $sql2;
   $result2 = $dbi->query($sql2);
   $oobj="";
   if($myrow2 = mysqli_fetch_array($result2)) {
       $oobj  = $myrow2["exciting"];
       $otime  = $myrow2["time"];
       $onick  = $myrow2["nick"];
       $odescrip  = $myrow2["descrip"];
       $omark  = $myrow2["mark"];
       $ocook  = $myrow2["cook"];
       $oip    = $myrow2["ip"];
       $omail    = $myrow2["mail"];
   }
   if ($oobj) {
    $sql2    = "insert into otklik (exciting,time,nick,descrip,mark,cook,ip,mail) values
               ($oobj,$otime,'$onick','$odescrip',$omark,'$ocook','$oip','$omail')";
    echo $sql2;
    $result2 = $dbi->query($sql2);
    $sql2    = "delete from otklikm where exciting=$oobj and time=$otime";
    $result2 = $dbi->query($sql2);
   }

  }
  $sql2    = "delete from otklikm where time<(". time() . " - 7776000)";
  $result2 = $dbi->query($sql2);


  $sql2    = "select ip,exciting,nick,descrip,mark,time,FROM_UNIXTIME(time, '%e-%m-%Y') as odate from otklikm order by time desc";
  $result2 = $dbi->query($sql2);
  if($myrow2 = mysqli_fetch_array($result2)) {
      do {
       $oobj  = $myrow2["exciting"];
       $odate  = $myrow2["odate"];
       $otime  = $myrow2["time"];
       $onick  = $myrow2["nick"];
       $odescrip  = $myrow2["descrip"];
       $omark  = $myrow2["mark"];
       $oip    = $myrow2["ip"];
       $oexname = getexname($oobj);
       echo "<P><b><a target=_blank href=/otklik.php/$oobj>$oexname</a></b><br>$oip $onick <b>$odate</b> Оценка <b>$omark</b><br>$odescrip<br>\n";
       echo "<a href=?admt=otklik&otime=$otime&oex=$oobj&s=delete>Удалить</a> ";
       echo "<a href=?admt=otklik&otime=$otime&oex=$oobj&s=add>Поместить в отклики</a> ";
      } while($myrow2 = mysqli_fetch_array($result2)); 
  }
  mysqli_free_result($result2);

 }
// 

?>
