<?php

include_once("include/func.php");

$trip = $_REQUEST["trip"];  
$trip = mysafestring($trip);

$num = $_REQUEST["num"];  
$num = (int) mysafestring($num);

include("include/head01.php");
include_once("include/f_trip.php");
#echo $trip;

if ($trip) {

 $objs = explode(",",$trip);
 if (is_array($objs)) {
   $obje = $objs[$num];
   $add="";
   preg_match('/^(\D)(\d+)(x*)([^,]*)$/',$obje,$obja);
   if ($obja[1] == "t") {
    if ($obja[3] == "x") {
     $add=$obja[4];
    }
   }
   echo "<form name=strip method=post action=#".$num.">\n";
   if (($obja[1] == "t") and ($obja[2])) {
       $id=$obja[2];

       $sql = "select name, hide, descrip from towns where id = $id";
       $result_town = $dbi->query($sql);
       if ($myrow = mysqli_fetch_array($result_town)) {
        $thide = $myrow["hide"];
        $mname = $myrow["name"];
        if ($thide == 1) {
// hide = 1 Это развилка. Вообще не должна была сюда попасть.
         echo "<font class=travel11>\n";
         echo "$mname";
         echo "</font>\n";
        }
        if ($thide == 0) {
// hide = 0 сам по себе город.
           echo "<div align=center><p class=travel11>\n";
           echo "<a target=_blank href=town.php/$id>";
           echo $mname;
           echo "</a></p></div>\n";
           $photoimg=getfototown($id,3);
           if ($photoimg) {
            echo "<div align=center>$photoimg</div><br>";
           }
           echo "<p class=travel10>\n";
//           $tdescrip = gettowntext($id);
//           if (!($tdescrip)) {
//            $tdescrip = $myrow["descrip"];
//           }
           echo $tdescrip;
           echo "</p>\n";
           $typearr=array();
           $sql1="select distinct type from exciting where town=$id";
           $result1 = $dbi->query($sql1);
           if($myrow1 = mysqli_fetch_array($result1)) {
            $ex_flag="1";
            do {
             $typearr[$myrow1["type"]] = "1";
            } while($myrow1 = mysqli_fetch_array($result1));
           }
           mysqli_free_result($result1);
           echo "<div align=center><table border=0 width=40%><tr><td align=left>";
           echo "Включать в отчёт:<br>";
           echo "<input type=checkbox name=comment ";
           if (!(preg_match('/0/',$add))) { echo "CHECKED"; }
           echo "> Описание<br>\n";
           for ($ii=1; $ii<=$tarryl; $ii++) {
            if ($typearr[$ii]) {
             echo "<input type=checkbox name=type$ii ";
             if (preg_match('/'.$ii.'/',$add)) { echo "CHECKED"; }
             echo "> ".$typearry[$ii]."<BR>\n";
            }
            else {
             echo "<input type=hidden name=type$ii value=''>";
            }
           }
           echo "<input type=checkbox name=otklik ";
           if (preg_match('/y/',$add)) { echo "CHECKED"; }
           echo "> Отзывы<br>\n";
           if (($photoimg) or ($ex_flag)) {
            echo "<input type=checkbox name=foto ";
            if (preg_match('/z/',$add)) { echo "CHECKED"; }
            echo "> Фото<br>\n";
           }
           else {
              echo "<input type=hidden name=foto value=''>";
           }
           echo "</table></div>";
        }
        if ($thide == 2) {
// hide = 2 == Объект на трассе

          $sql_e = "select id, name, descrip, type, Nshirota, Edolgota from exciting where town = $id";
          $result_e = $dbi->query($sql_e);
          if ($myrow_e = mysqli_fetch_array($result_e)) {
              $ide = $myrow_e["id"];
              $dname = $myrow_e["name"];
              $Nshirota = (float)$myrow_e["Nshirota"];
              $Edolgota = (float)$myrow_e["Edolgota"];
              $ngrv = degreestotext($Nshirota);
              $egrv = degreestotext($Edolgota);
              $coord = "";
              if (($ngrv) and ($egrv)) {
                $coord = "N$ngrv, E$egrv";
              }
              if (!($mname)) {
                $mname = $dname;
              }
              if ($mname<>$dname) {
                 $mname = $mname.". ".$dname;
              }
//  Это отображение для формы выбора
//  Цельнотянуто из откликов
//  Нет только "фотографий на тему"

              echo "<input type=hidden name=type1 value=''>\n";
              echo "<input type=hidden name=type2 value=''>\n";
              echo "<input type=hidden name=type3 value=''>\n";
              echo "<input type=hidden name=type4 value=''>\n";
              echo "<input type=hidden name=type5 value=''>\n";
              echo "<input type=hidden name=type6 value=''>\n";
              echo "<input type=hidden name=type7 value=''>\n";
              echo "<input type=hidden name=type8 value=''>\n";
              echo "<input type=hidden name=type9 value=''>\n";
              if ($coord) {
                echo "<div align=center><font class=\"t13 tc\">$coord";
                echo "&nbsp;[<a target=_blank href=//maps.google.com/maps?ll=".$Nshirota.",".$Edolgota."&spn=0.01,0.01&t=k&hl=en>Карта&nbsp;Гугль</a>]\n";
                echo "</font></div>\n";
               }
               $ymap_link="";
               if (is_file("ymaps/e-".$ide.".php")) {
                 include("ymaps/e-".$ide.".php");
               }
               $photoimg=getfotoex($ide,3,"",0);
               echo "<div align=center><p class=travel11>\n";
               echo "<a target=_blank href=otklik.php/$ide>";
               echo $myrow_e["name"];
               echo "</a></p></div>\n";
               if ($photoimg) {
                echo "<div align=center>$photoimg</div><br>";
               }
               if ($ymap_link) {
                echo "<p class=travel13><a target=_blank href=\"$ymap_link\">Карта Яндекс</a></p>";
               }
               echo "<p class=travel13>\n";
               echo $myrow_e["descrip"];
               echo "</p>\n";
               $otkl_rep = getotklik($ide,"","");
               echo $otkl_rep;
// отклики закончились

               echo "<div align=center><table border=0 width=40%><tr><td align=left>";
               echo "Включать в отчёт:<br>";
               echo "<input type=checkbox name=comment ";
               if (!(preg_match('/0/',$add))) { echo "CHECKED"; }
               echo "> Описание<br>\n";
               if ($otkl_rep) {
                echo "<input type=checkbox name=otklik ";
                if (preg_match('/y/',$add)) { echo "CHECKED"; }
                echo "> Отклики<br>\n";
               }
               else {
                echo "<input type=hidden name=otklik value=''>";
               }
               if ($photoimg) {
                echo "<input type=checkbox name=foto ";
                if (preg_match('/z/',$add)) { echo "CHECKED"; }
                echo "> Фото<br>\n";
               }
               else {
                echo "<input type=hidden name=foto value=''>";
               }
               echo "</table></div>";
          }
          mysqli_free_result($result_e);
        }
       }
       mysqli_free_result($result_town);
   }
   echo "<p><div align=center><input type=submit value=\"Изменить\" onclick=ButObj($num)></div></p>\n";
   echo "</form>";
 }

}

?>