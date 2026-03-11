<?php


include('include/config.php');
include_once('include/func.php');
include_once('include/head00.php');

$town = $_REQUEST["town"];  
$town = (int) mysafestring($town);

$exlist = $_REQUEST["list"];  
$exlist = mysafestring($exlist);

$addhead = $_REQUEST["head"];  
$addhead = mysafestring($addhead);


if ($exlist) {
 $eids = explode(',',$exlist);
 for ($ii=0; $ii<sizeof($eids); $ii++) {
  $eids[$ii] = (int)$eids[$ii];
 }
 $exlist = join($eids,",");
}

$i01=0;
$sql="select t.name as name, t.descrip as descrip, a.name as aname from towns t, areas a "
        . "where t.id='$town' and a.id=t.area";
$result = $dbi->query($sql);
if($myrow = mysqli_fetch_array($result)) {
 $t_name = $myrow["name"];
 $area=$myrow["aname"];
 $title=$t_name . " ($area) $addhead";
 include('include/head01.php');
 echo "<h2>";
 echo "$t_name, $area";
 echo "</h2>";
 echo "$reclama01\n";
 if (!($exlist)) {
  echo "<p><br>";
  $tdescrip = gettowntext($town);
  if (!($tdescrip)) {
   $tdescrip = $myrow["descrip"];
  }
  echo $tdescrip;
  echo "</p>\n";
 }

 if ($photo == '1') {
     $photoimg=getfototownl($town,0,$exlist);
     if ($photoimg[0]) {
       echo "<div align=center>$photoimg</div><br>";
     }
 }

}
else {
  include("include/header.php");
  echo "<p class=travel0> К сожалению, ссылка была неправильной. Попробуйте обратиться к <a href=/>главной странице сайта</a>. О зависших ссылках сообщайте на форуме или в гостевой книге</p>";
}

$et='';
if ($_REQUEST["et01"] == '1') {
 $et=$et."1,";
}
if ($_REQUEST["et02"] == '1') {
 $et=$et."2,";
}
if ($_REQUEST["et03"] == '1') {
 $et=$et."3,";
}
if ($_REQUEST["et04"] == '1') {
 $et=$et."4,";
}
if ($_REQUEST["et05"] == '1') {
 $et=$et."5,";
}
if ($_REQUEST["et06"] == '1') {
 $et=$et."6,";
}
if ($_REQUEST["et07"] == '1') {
 $et=$et."7,";
}
if ($_REQUEST["et08"] == '1') {
 $et=$et."8,";
}
if ($_REQUEST["et09"] == '1') {
 $et=$et."9,";
}

$et=preg_replace("/\,$/","",$et);
$sqladd = "";
if ($exlist) {
 $sqladd = " and id in ($exlist) ";
}

$sql="select name, descrip, id, type, Nshirota, Edolgota, votes, rate, wrate from exciting "
        . "where town='$town' and mainid>=0 and type in ($et) $sqladd order by type, name";

if ($et) {
 $flag01="";
 $result = $dbi->query($sql);


 if($myrow = mysqli_fetch_array($result)) {
   echo "<p class=travell0>";
   do {
      $s_id = $myrow["id"];
      $s_rek = getreklama($s_id, $photo);
      $ett = $myrow["type"];
      $ngr = $myrow["Nshirota"];
      $egr = $myrow["Edolgota"];
      if (($ngr > 0) and ($egr > 0)) {
        $egrw = degreestotext($egr);
        $ngrw = degreestotext($ngr);
        $coorde = " /<b>N</b>$ngrw, <b>E</b>$egrw/ ";
      } else {
        $coorde = "";
      }
      $s_name = $myrow["name"];
      $wrate   = (float)$myrow["wrate"];
      $wrate   = sprintf("%01.2f",$wrate);
      $s_description = $myrow["descrip"];
      $votese = $myrow["votes"];
      $ratee  = $myrow["rate"];
      if ($votese > 0) {
        $votext = " Рейтинг: $wrate, Балл: " . sprintf("%01.2f",(3 + ($ratee / $votese)));
      } else {
        $votext = "";
      }
      if ($ett !== $i01) {
       $i01=$ett;
       $sql02 ="select name from type where id='$ett'";
       $result02 = $dbi->query($sql02);
       if($myrow02 = mysqli_fetch_array($result02)) {
        $type_name = $myrow02["name"];
        mysqli_free_result($result02);
        if ($flag01 == '1') {
         echo "</tbody></table>\n$reclama01";
        }
        echo "<table border=1 borderwidth=0 width=100% cellpadding=2><Tbody class=travell0>";
        $flag01="1";
        echo"<tr><TD align=center class=\"t16 tc tb\">$type_name $addhead ($t_name, $area)";
        echo"</TD></TR>";
       }
       else {
        if ($flag01) {
         echo "</tbody></table>$reclama01\n";
        }
        echo "<table border=1 borderwidth=0 width=100% cellpadding=2><Tbody class=travell0>";
        $flag01="1";
        echo"<tr><TD align=center class=\"t16 tc tb\">$t_name, $area";
        echo"</TD></TR>";
       }
      }
      echo"<tr><TD>";
      echo "<p class=travell5>\n";
      echo "$s_name $coorde $votext</font></p>";
      if ($s_rek <> "") {
       echo "<br>\n<table border=1 cellpadding=2 width=100%><tr><td class=\"t12 travell0t\">\n<b>Официальная информация: </b><br>$s_rek</td></tr></table>\n";
      }
      echo "<p class=travell0>\n$s_description</p>\n";
//      if ($s_rek <> "") {
//	      echo "<b>$s_name</b>";
//      }
//      else {
//	      echo "$s_name";
//      }
      if ($_REQUEST["photo"] == '1') {
       $photoimg=getfotoex($s_id,3,"",0);
       if ($photoimg) {
        echo "<br><div align=center>$photoimg</div><br>";
       }
      }

      if ($_REQUEST["otk"] == '1') {
       $otkl_rep = getotklik($s_id,"p","");

       if ($otkl_rep) {
        echo "<hr width=95%><div class=travell0>\n";
        echo $otkl_rep;
        echo "</div>\n";
       }
      }
      echo"</TD></TR>";
   } while($myrow = mysqli_fetch_array($result));
   if ($flag01) {
    echo "</tbody></table>\n$reclama01";
   }
 }
 else {
        echo "<p class=\"t14 tb tc\"> К сожалению, ссылка была неправильной. Попробуйте обратиться к <a href=/>главной странице сайта</a>. О зависших ссылках сообщайте на форуме или в гостевой книге</p>";
        echo $sql;
 }
}

/*
 * Это страшно мусорная хрень
 *
if ($_REQUEST["et00"] == '1') {
 $sql_r01 = "select l.key_towns as key_town, l.nick as nick, l.month as month, l.year as year, l.group_rep as gr, l.rid as rid, l.time as t, l.report as rep 
             from l_reports l, t_reports t where 
             l.rid=t.rid and t.tid=$town";
 $result_r01 = $dbi->query($sql_r01);
 if($myrow_r01 = mysqli_fetch_array($result_r01)) {
  echo "<br><br><table border=1 width=100% cellpadding=4><Tbody class=travell0>";
  echo"<tr><TD align=center class=\"t16 tc tb\">Отчеты";
  echo"</TD></TR>";
  do {
    $rid = $myrow_r01["rid"];
    $s_key = $myrow_r01["key_town"];
    $s_key = getskeytext($s_key,$rid);
    $s_nick = $myrow_r01["nick"];
    $s_month = $myrow_r01["month"];
    $s_rep = $myrow_r01["rep"];
    if ($s_month > "") $s_month = $s_month."/";
    $s_year = $myrow_r01["year"];
    echo "<TD align=left>";
    $s_key=preg_replace("/\,/",", ",$s_key);
    $s_rep=preg_replace("/\>\<IMG/i","> <img",$s_rep);
    echo "<tr><td><b>Автор: $s_nick<br>Дата: $s_month$s_year<br>Посещены: $s_key</b><br><hr width=80%>\n";
    echo "<div class=travell0>\n$s_rep</div>";
    echo "</td></tr>\n";
  } while($myrow_r01 = mysqli_fetch_array($result_r01));
  echo "</tbody></table>$reclama01\n";
 }  
}
*/

?>
</body>
</html>
