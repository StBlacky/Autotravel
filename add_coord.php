<?php

exit;

include_once('include/func.php');
include_once('include/forumsess.php');
include("include/head01.php");
if (($forum_user_rank) || ($forum_user_level)) {

// всплывающее окно по изменению координат объекта. Функциональность минимальна.

$id = $_REQUEST["id"];  
$id = (int) mysafestring($id);
$egrw = $_REQUEST["egrw"];  
$egrw = mysafestring($egrw);
$ngrw = $_REQUEST["ngrw"];  
$ngrw = mysafestring($ngrw);
$flag = 0;
$nsho = 0;
$edgo = 0;
$nsht = 0;
$edgt = 0;
$exact = 0;

if ($id >0) {
 $sql = "SELECT e.id as id, e.exact as exact, e.Nshirota as Nshirota, e.Edolgota as Edolgota, "
         . "t.Nshirota as tshirota, t.Edolgota as tdolgota from exciting e, towns t where e.id = $id and t.id = e.town";
 $result = $dbi->query($sql);
 if ($myrow = mysqli_fetch_array($result)) {
   $id = $myrow["id"];
   $exact = (int)$myrow["exact"];
   $nsho = (float)$myrow["Nshirota"];
   $edgo = (float)$myrow["Edolgota"];
   $nsht = (float)$myrow["tshirota"];
   $edgt = (float)$myrow["tdolgota"];
 }
# error_log("coord $ngrw $nsho $nsht : $egrw $edgo $edgt");
# изменяем только если:
# 1) старые координаты равны 0
# 2) новые координаты не дальше 0.5 градуса от координат города либо координаты города - нуль
# 3) не установлен exact
 if (($id) and ($ngrw) and ($egrw) and (!($exact)) ) {
# обновляем координаты. Если, конечно, не полная фигня понаписана.
  $ngr = texttodegrees($ngrw);
  $egr = texttodegrees($egrw);

  if 
  ((
   ($id >0) and ($ngr > 0) and ($egr > 0) and 
   ((abs($ngr - $nsht) < 0.5 ) or ($nsht == 0)) and
   ((abs($egr - $edgt) < 0.5 ) or ($edgt == 0))
  ) or (
   ($nsho == 0) and ($edgo == 0)
  ))

   {

#   echo "$ngrw -> $ngr<br>\n";
#   echo "$egrw -> $egr<br>\n";
   $flag=1;
   if ($nsho <> 0) {
    $sql = "insert into history (tblname,cook,ip,id,field,valueold,valuenew) values ('exciting','$ctext','$ipp',$id,'Nshirota','$nsho','$ngr')";
    if (!($dbi->query($sql))){
     $flag=0;
//     echo "$sql";
    }
   }
   if ($edgo <> 0) {
    $sql = "insert into history (tblname,cook,ip,id,field,valueold,valuenew) values ('exciting','$ctext','$ipp',$id,'Edolgota','$edgo','$egr')";
    if (!($dbi->query($sql))){
     $flag=0;
//     echo "2";
    }
   }
   if ($flag) {
    $sql = "update exciting set Nshirota=$ngr, Edolgota=$egr where id = $id";
//    echo $sql;
    if (!($dbi->query($sql))){
     $flag = 0;
//     echo "3";
    }
   }
   if ($flag) {
    $sql = "SELECT id, name, Nshirota, Edolgota from exciting where id = $id";
    $result = $dbi->query($sql);
    if ($myrow = mysqli_fetch_array($result)) {
     $exname = $myrow["name"];
     $nsh = (float)$myrow["Nshirota"];
     $edg = (float)$myrow["Edolgota"];
     $ngrv = degreestotext($nsh);
     $egrv = degreestotext($edg);
//     echo "$nsh -> $ngrv<br>\n";
//     echo "$edg -> $ngrv<br>\n";
   
     echo "<h3>$exname</h3>\n";
     if (($ngrv) and ($egrv)) {
      $coord = "N$ngrv E$egrv";
      echo "<p>Координаты успешно изменены: $coord</p>";
     }
    }
   }
   else {
    print "<p>Что-то совсем неправильное. Попытайтесь ещё раз и если не выйдет, сообщите на форуме обстоятельства</p>\n";
   }
  }
  else {
   print "<p>Кажется, Вы не совсем правильно ввели координаты. Между числами градусов и минут нужен пробел, а внутри минут только цифры и точка (или запятая).</p>\n";
   print "<!-- nsht = $nsht, edgt = $edgt\n";
   print "     ngr  = $ngr,  egr  = $egr\n";
   print "     a1   = " . abs($ngr - $nsht) . ", a2 = " . abs($egr - $edgt) . " -->\n";
  }
 }
 if (!$flag) {
# выводим форму ввода
  $sql = "SELECT id, name, Nshirota, Edolgota from exciting where id = $id";
  $result = $dbi->query($sql);
  if ($myrow = mysqli_fetch_array($result)) {
   $exname = $myrow["name"];
   $nsh = (float)$myrow["Nshirota"];
   $edg = (float)$myrow["Edolgota"];
   $ngrv = degreestotext($nsh);
   $egrv = degreestotext($edg);
   echo "<h3>$exname</h3>\n";
   if (($ngrv) and ($egrv)) {
    $coord = "N$ngrv E$egrv";
    echo "<p>Координаты: $coord</p>";
   }
   echo "<form name=coord method=post action=".$_SERVER["PHP_SELF"].">\n";
   echo "<input type=hidden name=id value='$id'>\n";
   echo "Введите новые значения GPS-координат в виде<br>\n";
   echo "XX.XXXXX - градусы с долями<br>\n";
   echo "или XX YY.YYY, где XX - градусы, YY.YYY - угловые минуты с долями<br>\n";
   echo "или XX YY ZZ.ZZZ, где XX - градусы, YY - угловые минуты, ZZ.ZZZ - угловые секунды с долями<br>\n";
   echo "<b>N</b> <input type=text name=ngrw value='$ngrv'><br>\n";
   echo "<b>E</b> <input type=text name=egrw value='$egrv'><br>\n";
   echo "<input type=submit value='Изменить'>\n";
   echo "</form>\n";
  }
 }
}

}
?>
