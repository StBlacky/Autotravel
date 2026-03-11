<?php 

// функции display***
// Показывают один объект:
// display_obj - строка в просмотре.
// display_etripobj - строка при редактировании маршрута
// display_editobj - строка при редактировании свойств отчёта
// display_vobj - предпросмотр при редактировании маршрута/отчета
// display_report - таблица в отчёте (по объекту).
// display_trace - трасса, она всюду одинаковая

// в display_report реклама показывается текстом, в остальных - иконка

// function AddTripToTown($trip,$trace,$id) {
//   Добавляет к текущему пути трассу и промежуточные пункты до указанного города
//   Первый параметр - путь, второй - трасса, третий - новый город
//   Проверок НЕТ!!!

// function lasttown($trip) {
// возвращает id последнего города по маршруту

// function find_town($spring,$direct,$prev_trip) {
// раньше была в ch_test

// function GetDistance($trip) {
// возвращает сумму "расстояний между точками" в $trip.

// function CheckTrip($trip,$num) {
// проверяет, нет ли в дальнейшем $trip того же town, что и в номере $num. 
// если есть - возвращяет номер, иначе 0.

// function gettown($trip,$num) {
// возвращает id города в маршруте по номеру. Если неуспех - возвращает 0.

// function CalcTrip($spring,$direct) {
// рассчитывает маршрут от $spring до $direct Раньше была в ch_trip и add_t

 include_once('include/config.php');
 include_once('include/func.php');
 include_once('include/head00.php');
 $tarryl = 0;
 $sql_ftrip = "select name, id from type order by srtfld";
 $result_ftrip = $dbi->query($sql_ftrip);
 while ($row_trip = mysqli_fetch_array($result_ftrip)) {
    $id_ftrip = $row_trip["id"];
    if ($id_ftrip > $tarryl) {
     $tarryl = $id_ftrip;
    }
    $typearry[$id_ftrip] = $row_trip["name"];
 }
 mysqli_free_result($result_ftrip);


 function display_trace($id,$add) {
  Global $dbi;

// показывает отдельную трассу
// первый параметр - id объекта
// второй параметр - текст справа (километраж)
       if ($id != "0") {
	$sql_t = "select name, of_name, comment from trace where id = $id";              
	$result_t = $dbi->query($sql_t);
	if ($myrow_t = mysqli_fetch_array($result_t)) {
                $tr_ofname=$myrow_t["of_name"];
	        $tr_desc = $myrow_t["comment"];
                if (preg_match('/Паром/iu',$tr_ofname)) {
                 echo "<table border=0 width=100%>";
                 echo "<tbody class=travell0>";
                 echo "<tr><TD align=right width=47%>";
                 echo "<font class=travell0>\n";
                 echo $tr_ofname;
                 echo " ".$myrow_t["name"]."</font></TD><TD align=center WIDTH=6%>|";
                 echo "</TD><TD align=left><font class=\"t14 tb tc\">".$add;
                 echo "</font>\n";
                 echo "</TD></tr>\n";
                 echo "<tr><td colspan=3 align=center><font class=travell0>$tr_desc</font></td></tr>\n";
                 echo "<tr><TD align=right width=47%>";
                 echo "<font class=travell0>\n";
                 echo $tr_ofname;
                 echo " ".$myrow_t["name"]."</font></TD><TD align=center WIDTH=6%>|";
                 echo "</TD><TD align=left><font class=\"t14 tb tc\">".$add;
                 echo "</font>\n";
                 echo "</TD></tr>\n";
                }
                else {
                 echo "<table border=0 width=100%>";
                 echo "<tbody class=travell0>";
                 echo "<tr><TD align=right width=47%>";
                 echo "<font class=\"t14 tb tc\">\nТрасса ";
                 echo $tr_ofname;
                 echo "</font></TD><TD align=center WIDTH=6%>|";
                 echo "</TD><TD align=left><font class=\"t14 tb tc\">".$add;
                 echo "</font>\n";
                 echo "</TD></tr>\n";
                }
                echo "</tbody>";
                echo "</table>\n";
	}
        mysqli_free_result($result_t);
       }
       else {
        	echo "<table border=0 width=100%>";
                echo "<tbody class=travell0>";
                echo "<tr><TD align=right width=47%>";
                echo "<font class=\"t14 tb tc\">\n";
                echo " Трасса не определена ";
                echo "</font></TD><TD align=center WIDTH=6%>|";
                echo "</TD><TD align=left><font class=\"t14 tb tc\">".$add;
                echo "</font>\n";
                echo "</TD></tr>\n";
                echo "</tbody>";
                echo "</table>\n";
       }
 }
// закончилась display_trace
// показывает отдельную трассу

 function display_obj($id,$add) {

// показывает отдельный объект, для просмотра
// первый параметр - id объекта
// второй параметр - доп.инфо: выбираемые в отчёт объекты и свойства (
// [1234...] - типы объектов [syz] - окрестности, отклики и фото, 
// 0 - ИСКЛЮЧИТЬ описание)

  Global $dbi, $typearry, $townslist, $tarryl;
       $addispl="";
       $sql = "select name, hide, descrip from towns where id = $id";
       $result_town = $dbi->query($sql);
       if ($myrow = mysqli_fetch_array($result_town)) {
        $thide = $myrow["hide"];
        if ($add != "") {
         for ($ii=1; $ii<=$tarryl; $ii++) {
          if (preg_match('/'.$ii.'/',$add)) { $addispl .= " /".$typearry[$ii]."/ "; }
         }
         if (preg_match('/0/',$add)) { $addispl .= " /Без описания/ "; }
         if (preg_match('/s/',$add)) { $addispl .= " /Окрестности/ "; }
         if (preg_match('/y/',$add)) { $addispl .= " /Отзывы/ "; }
         if (preg_match('/z/',$add)) { $addispl .= " /Фото/ "; }
        }
        $mname = $myrow["name"];
        if ($thide == 1) {
         echo "<font class=travell5>\n";
         echo "$mname";
         echo "</font>\n";
         if (($id <> 777) and ($id <>1055)) {
          $trcount = 0;
          $sql_tr = "SELECT DISTINCT t.id AS trace_id, t.name AS trace FROM trace t, far_tr ft WHERE ft.town = '$id' AND t.id = ft.trace";
          $result_tr = $dbi->query($sql_tr); 
          if ($myrow_tr = mysqli_fetch_array($result_tr)) {
            $trview = " - пересечение дорог: ";
            do {
                $s_trace = $myrow_tr["trace"];
                $trcount++;
                $trview = $trview . "<br>$s_trace\n";
            } while($myrow_tr = mysqli_fetch_array($result_tr));
          }
          mysqli_free_result($result_tr);
          if ($trcount > 1) {
            echo "<font class=travell0>$trview</font>\n";
          }
         }
        }
        if ($thide == 0) {
          echo "<font class=travell5>\n";
          echo "<a target=_blank href=town.php/$id>";
          echo $mname;
          echo "</a>\n";
          echo "</font>\n";
          if ($townslist) {
           $townslist=$townslist.", ".$mname;
          }
          else {
           $townslist=$mname;
          }
        }

        if ($thide == 2) {
          $sql_e = "select id, name, descrip, type from exciting where town = $id";
          $result_e = $dbi->query($sql_e);
          if ($myrow_e = mysqli_fetch_array($result_e)) {
           $ide = $myrow_e["id"];
           $dname = $myrow_e["name"];
           if (!($mname)) {
             $mname=$dname;
           }
           if ($mname<>$dname) {
             $mname=$mname.". ".$dname;
           }
           echo "<font class=travell5>\n";
           if (getreklama($ide,0) <> "") {
            echo "<img src=jpg/bigpartner.gif width=11 height=11 title=\"Партнер сайта\" alt=\"Партнер сайта\">&nbsp;&nbsp;";
           }
           echo "<a target=_blank href=\"/otklik.php/$ide\">";
           echo $mname;
           echo "</a>\n";
           echo "</font>\n";
          }
          mysqli_free_result($result_e);
        }
       }
       if ($thide != 1) {
        if ($addispl) {
          echo "<br><font class=travell0> В отчёт включать: $addispl</font><br>\n";
        }
        else {
          echo "<br><font class=travell0>/В отчёте только описание/</font><br>\n";
        }
       }
       mysqli_free_result($result_town);
 }

// display_obj закончилась
// показывает отдельный объект, для просмотра

 function display_objt($id) {

// показывает отдельный объект, для просмотра при добавлении трассы
// параметр - id объекта

  Global $dbi;
       $sql = "select name, hide, descrip from towns where id = $id";
       $result_town = $dbi->query($sql);
       if ($myrow = mysqli_fetch_array($result_town)) {
        $thide = $myrow["hide"];
        $mname = $myrow["name"];
        if ($thide == 1) {
         echo "<font class=travell5>\n";
         echo "$mname";
         echo "</font>\n";
        }
        if ($thide == 0) {
          echo "<font class=travell5>\n";
          echo $mname;
          echo "</font>\n";
        }

        if ($thide == 2) {
          $sql_e = "select id, name, descrip, type from exciting where town = $id";
          $result_e = $dbi->query($sql_e);
          if ($myrow_e = mysqli_fetch_array($result_e)) {
           $ide = $myrow_e["id"];
           $dname = $myrow_e["name"];
           if (!($mname)) {
               $mname=$dname;
           }
           if ($mname<>$dname) {
               $mname=$mname.". ".$dname;
           }
           echo "<font class=travell5>\n";
           if (getreklama($ide,0) <> "") {
            echo "<img src=jpg/bigpartner.gif width=11 height=11 title=\"Партнер сайта\" alt=\"Партнер сайта\">&nbsp;&nbsp;";
           }
           echo $mname;
           echo "</font>\n";
          }
          mysqli_free_result($result_e);
        }
       }
       if (($id <> 777) and ($id <>1055)) {
          $trcount = 0;
          $sql_tr = "SELECT DISTINCT t.id AS trace_id, t.name AS trace FROM trace t, far_tr ft WHERE ft.town = '$id' AND t.id = ft.trace";
          $result_tr = $dbi->query($sql_tr); 
          if ($myrow_tr = mysqli_fetch_array($result_tr)) {
            $trview = " - пересечение дорог: ";
            do {
                $s_trace = $myrow_tr["trace"];
                $trcount++;
                $trview = $trview . "<br>$s_trace\n";
            } while($myrow_tr = mysqli_fetch_array($result_tr));
          }
          mysqli_free_result($result_tr);
          if ($trcount > 1) {
            echo "<br><font class=travell0>$trview</font>\n";
          }
       }
       mysqli_free_result($result_town);
 }

// display_objt закончилась
// показывает отдельный объект, для просмотра при добавлении трассы


function display_etripobj($id,$num,$tripadd) {

// показывает отдельный объект редактируемого маршрута
// первый параметр - id объекта
// второй параметр - номер объекта в маршруте (нужен для ссылок) 
// третий параметр - какие показывать ссылки ("" - только "стереть", 
// 0 - кроме "стереть ответвление", не 0 - все.

  Global $dbi, $typearry, $tarryl;
       $sql = "select name, hide, descrip from towns where id = $id";
       $result_town = $dbi->query($sql);
       
       if ($myrow = mysqli_fetch_array($result_town)) {
        $thide = $myrow["hide"];
        $mname = $myrow["name"];
        if ($thide == 1) {
         echo "<font class=travell5>\n";
         echo "$mname";
         echo "</font>\n";
        }
        if ($thide == 0) {
            echo "<font class=travell5>\n";
            echo "<a name=$num target=_blank href=town.php/$id>";
            echo $mname;
            echo "</a>\n";
            echo "</font>\n";
        }
        if ($thide == 2) {
          $sql_e = "select id, name, descrip, type from exciting where town = $id";
          $result_e = $dbi->query($sql_e);
          if ($myrow_e = mysqli_fetch_array($result_e)) {
           $ide = $myrow_e["id"];
           $dname = $myrow_e["name"];
           if (!($mname)) {
                  $mname=$dname;
           }
           if ($mname<>$dname) {
                  $mname=$mname.". ".$dname;
           }
           echo "<font class=travell5>\n";
           if (getreklama($ide,0) <> "") {
            echo "<img src=jpg/bigpartner.gif width=11 height=11 title=\"Партнер сайта\" alt=\"Партнер сайта\">&nbsp;&nbsp;";
           }
           echo "<a name=$num target=_blank href=\"/otklik.php/$ide\">";
           echo $mname;
           echo "</a>\n";
           echo "</font>\n";
          }
          mysqli_free_result($result_e);
        }
       }
       echo "<br><font class=travell0>";
       echo " [<a href=javascript:RmObj($num)>Стереть</a>]</a>";
       echo " [<a href=#"."$num onclick=AddObj($num)>Добавить&nbsp;ответвление</a>]";
       if ((string)$tripadd != "") {
        if ($tripadd > 0) {
         echo " [<a href=javascript:RmTrip($num)>Стереть&nbsp;ответвление</a>]</a>";
        }
        echo " [<a href=javascript:RmObjAll($num)>Стереть&nbsp;все&nbsp;дальше</a>]</a>";
       }
       echo "</font>\n";
       if (($id <> 777) and ($id <>1055)) {
          $trcount = 0;
          $sql_tr = "SELECT DISTINCT t.id AS trace_id, t.name AS trace FROM trace t, far_tr ft WHERE ft.town = '$id' AND t.id = ft.trace";
          $result_tr = $dbi->query($sql_tr); 
          if ($myrow_tr = mysqli_fetch_array($result_tr)) {
            $trview = " - пересечение дорог: ";
            do {
                $s_trace = $myrow_tr["trace"];
                $trcount++;
                $trview = $trview . "<br>$s_trace\n";
            } while($myrow_tr = mysqli_fetch_array($result_tr));
          }
          mysqli_free_result($result_tr);
          if ($trcount > 1) {
            echo "<br><font class=travell0>$trview</font>\n";
          }
       }

       mysqli_free_result($result_town);
 }

// display_etripobj закончилась
// показывает отдельный объект редактируемого маршрута


 function display_editobj($id,$add,$num) {

// показывает отдельный объект при редактировании отчета
// первый параметр - id объекта
// второй параметр - доп.инфо: выбираемые в отчёт объекты и свойства (
// [1234...] - типы объектов, [yz] - отклики и фото,
// 0 - ИСКЛЮЧЕНИЕ описания)
// третий параметр - номер объекта в маршруте (нужен для чекбоксов)

  Global $dbi, $typearry, $townslist, $tarryl;
       $addispl="";
       $sql = "select name, hide, descrip from towns where id = $id";
       $result_town = $dbi->query($sql);
       if ($myrow = mysqli_fetch_array($result_town)) {
        $thide = $myrow["hide"];
        if ($add != "") {
         for ($ii=1; $ii<=$tarryl; $ii++) {
          if (preg_match('/'.$ii.'/',$add)) { $addispl .= " /".$typearry[$ii]."/ "; }
         }
         if (preg_match('/0/',$add)) { $addispl .= " /Без описания/ "; }
         if (preg_match('/s/',$add)) { $addispl .= " /Окрестности/ "; }
         if (preg_match('/y/',$add)) { $addispl .= " /Отзывы/ "; }
         if (preg_match('/z/',$add)) { $addispl .= " /Фото/ "; }
        }
        $mname = $myrow["name"];
        if ($thide == 1) {
         echo "<font class=travell5>\n";
         echo "$mname";
         echo "</font>\n";
         if (($id <> 777) and ($id <>1055)) {
          $trcount = 0;
          $sql_tr = "SELECT DISTINCT t.id AS trace_id, t.name AS trace FROM trace t, far_tr ft WHERE ft.town = '$id' AND t.id = ft.trace";
          $result_tr = $dbi->query($sql_tr); 
          if ($myrow_tr = mysqli_fetch_array($result_tr)) {
            $trview = " - пересечение дорог: ";
            do {
                $s_trace = $myrow_tr["trace"];
                $trcount++;
                $trview = $trview . "<br>$s_trace\n";
            } while($myrow_tr = mysqli_fetch_array($result_tr));
          }
          mysqli_free_result($result_tr);
          if ($trcount > 1) {
            echo "<font class=travell0>$trview</font>\n";
          }
         }
        }
        if ($thide == 0) {
// По умолчанию -- ссылка на редактирование
            echo "<input type=checkbox value=$num><font class=travell5> <a name=$num>\n";
            echo $mname;
            echo "</a></font>\n";
            echo "<font class=travell0>";
            echo " [<a href=#"."$num onclick=EdObj($num)>Редактировать</a>]";
            echo "</font>\n";
            if ($townslist) {
             $townslist=$townslist.", ".$mname;
            }
            else {
             $townslist=$mname;
            }
        }
        if ($thide == 2) {
          $sql_e = "select id, name, descrip, type from exciting where town = $id";
          $result_e = $dbi->query($sql_e);
          if ($myrow_e = mysqli_fetch_array($result_e)) {
           $ide = $myrow_e["id"];
           $dname = $myrow_e["name"];
           if (!($mname)) {
               $mname=$dname;
           }
           if ($mname<>$dname) {
                  $mname=$mname.". ".$dname;
           }
           echo "<input type=checkbox value=$num><font class=travell5>\n";
           if (getreklama($ide,0) <> "") {
            echo "<img src=jpg/bigpartner.gif width=11 height=11 title=\"Партнер сайта\" alt=\"Партнер сайта\">&nbsp;&nbsp;";
           }
           echo "<a name=$num>\n";
           echo "$mname";
           echo "</a></font>\n";
           echo "<font class=travell0>";
           echo " [<a name=$num href=#"."$num onclick=EdObj($num)>Редактировать</a>]";
           echo "</font>\n";
          }
          mysqli_free_result($result_e);
        }
        if ($thide != 1) {
         if ($addispl) {
          echo "<br><font class=travell0> В отчёт включать: $addispl</font><br>\n";
         }
         else {
          echo "<br><font class=travell0>/В отчёте только описание/</font><br>\n";
         }
        }
       }
       mysqli_free_result($result_town);

 }

// display_editobj закончилась
// показывает отдельный объект при редактировании отчета

 function display_vobj($id) {

// показывает отдельный объект при предварительном просмотре
// параметр - id объекта

  Global $dbi, $typearry, $tarryl;

#  $reclama01="<p class=travell0><center>Материалы сайта \"Автопутешествия по России\" <b>www.autotravel.ru</b><br><br></center></p>\n";

       $sql = "select name, hide, descrip from towns where id = $id";
       $result_town = $dbi->query($sql);
       if ($myrow = mysqli_fetch_array($result_town)) {
        $thide = $myrow["hide"];
        $mname = $myrow["name"];
        if ($thide == 1) {
         echo "<font class=\"t14 tb tc\">\n";
         echo "$mname";
         echo "</font>\n";
         if (($id <> 777) and ($id <>1055)) {
          $trcount = 0;
          $sql_tr = "SELECT DISTINCT t.id AS trace_id, t.name AS trace FROM trace t, far_tr ft WHERE ft.town = '$id' AND t.id = ft.trace";
          $result_tr = $dbi->query($sql_tr); 
          if ($myrow_tr = mysqli_fetch_array($result_tr)) {
            $trview = " - пересечение дорог: ";
            do {
                $s_trace = $myrow_tr["trace"];
                $trcount++;
                $trview = $trview . "<br>$s_trace\n";
            } while($myrow_tr = mysqli_fetch_array($result_tr));
          }
          mysqli_free_result($result_tr);
          if ($trcount > 1) {
            echo "$trview\n";
          }
         }
        }
        if ($thide == 0) {
            echo "<div align=center><table border=1 width=97%>\n";
            echo "<tr><td align=center>\n";
            echo "<p class=\"t14 tb tc\">";
            echo $mname;
            $sql1="select distinct type from exciting where town=$id";
            $result1 = $dbi->query($sql1);
            if($myrow1 = mysqli_fetch_array($result1)) {
               echo "<br>\n";
               echo "Имеется: ";
               do {
                $ii=$myrow1["type"];
                echo "$typearry[$ii] \n";
               } while($myrow1 = mysqli_fetch_array($result1));
               echo "\n";
            }
            mysqli_free_result($result1);
            echo "</p></td></tr>\n";
//            $descrip = gettowntext($id);
//            if (!($descrip)) {
//             $descrip = $myrow["descrip"];
//            }
//            $photoimg=getfototown($id,3);
//            if (($descrip)) {
//             echo "<tr><td class=travell0 align=left>\n";
//             echo "\n";
//             echo $descrip;
//             echo "<br>";
//             if ($photoimg) {
//              echo "<div align=center>$photoimg</div><br>";
//             }
//            }
            echo "</td></tr>\n";
            echo "</table>\n";
// Город типа 0 закончен
        }

        if ($thide == 2) {

// hide = 2 == Объект на трассе
          $sql_e = "select id, name, descrip, type from exciting where town = $id";
          $result_e = $dbi->query($sql_e);
          if ($myrow_e = mysqli_fetch_array($result_e)) {
                $ide = $myrow_e["id"];
                $dname = $myrow_e["name"];
                if (!($mname)) {
                  $mname=$dname;
                }
                if ($mname<>$dname) {
                  $mname=$mname.". ".$dname;
                }
// Отображение для печати
                $sql_x = "select id, name, descrip, type from exciting where town = $id";
                $result_x = $dbi->query($sql_x);
                if ($myrow_x = mysqli_fetch_array($result_x)) {
                 $ide = $myrow_x["id"];
                 $dname = $myrow_x["name"];
                 if (!($mname)) {
                  $mname=$dname;
                 }
                 if ($mname<>$dname) {
                  $mname=$mname.".".$dname;
                 }
                 $descripe=$myrow_x["descrip"];

                 echo "<div align=center><table border=1 width=97%>\n";
                 echo "<tr><td>\n";
                 echo "<p class=\"t14 tb tc\">";
                 echo $mname;
                 echo "</p>\n";
                 echo "<p class=travell0>\n";
                 echo "$descripe</p>";
                 $s_rek = getreklama($ide,1);
                 if ($s_rek <> "") {
                   echo "<table class=travell0 border=1 cellpadding=8 width=97%><tr><td><b>Официальная информация: </b><br>$s_rek</td></tr></table>";
                 }
//                 $photoimg=getfotoex($ide,3);
//                 if ($photoimg) {
//                   echo "<br><div align=center>$photoimg</div>";
//                 }
                 echo "</td></tr>\n";
                }
                mysqli_free_result($result_x);
                echo "</td></tr></table>\n";

          }
          mysqli_free_result($result_e);
        }
       }
       mysqli_free_result($result_town);
 }

// display_vobj закончилась
// показывает отдельный объект в предпросмотре

// вспомогательная. Отображает объект в отчёте.
 function repdisplayexc($myrow1,$tname,$add) {
                $ide=$myrow1["id"];
                $namee  = $myrow1["name"];
                $votese = $myrow1["votes"];
                $ratee  = $myrow1["rate"];
                $wrate   = (float)$myrow1["wrate"];
                $nshe = (float)$myrow1["Nshirota"];
                $edge = (float)$myrow1["Edolgota"];
                $descripe=$myrow1["descrip"];

                if ($tname<>$namee) {
                 if ($tname) {
                  $namee=$tname.". ".$namee;
                 }
                }


                $wrate   = sprintf("%01.2f",$wrate);
                if ($votese > 0) {
                 $votext = " Рейтинг: $wrate, Балл: " . sprintf("%01.2f",(3 + ($ratee / $votese)));
                } else {
                 $votext = "";
                }
                $ngrv = degreestotext($nshe);
                $egrv = degreestotext($edge);
                if (($nshe > 0) and ($edge > 0)) {
                  $coorde = " /<b>N</b>$ngrv, <b>E</b>$egrv/ ";
                } else {
                  $coorde = "";
                }
                echo "<tr><td align=left>\n";
                echo "<p class=travell0><font class=travell5>\n";
                echo "$namee $coorde $votext</font>";
                echo "<br>\n$descripe</p>\n";
                if (preg_match("/y/",$add)) {
                 $s_rek = getreklama($ide,1);
                }
                else {
                 $s_rek = getreklama($ide,0);
                }
                if ($s_rek <> "") {
                 echo "<table class=travell0 border=1 cellpadding=8 width=97%><tr><td><b>Официальная информация: </b><br>$s_rek</td></tr></table>";
                }
                if (preg_match("/z/",$add)) {
                 $photoimg=getfotoex($ide,3,"",3);
                 if ($photoimg) {
                  echo "<div align=center>$photoimg</div>";
                 }
                }
                if (preg_match("/y/",$add)) {
                 $otkl_rep = getotklik($ide,"p","");
                 if ($otkl_rep) {
                   echo "<table border=0 width=100% cellpadding=0 class=travell0><tr><td class=travell0>\n";
                   echo $otkl_rep;
                   echo "</td></tr></table>\n";
                 }
                }
                echo "</td></tr>\n";
 }

 function display_report($id,$add) {

// показывает отдельный объект в отчете
// первый параметр - id объекта
// второй параметр - доп.инфо: выбираемые в отчёт объекты и свойства (
// [1234...] - типы объектов, [syz] - окрестности, отклики и фото,
// 0 - ИСКЛЮЧЕНИЕ описания)

  Global $dbi, $typearry, $tarryl, $townslist, $exclist, $_salp_mode, $_desc_mode;
  if (!($_desc_mode == 1)) {
   $_desc_mode = 0;
  }
  if ($_salp_mode) {
   $sqladds = "order by loc asc";
  }
  else {
   $sqladds = "order by wrate desc, loc asc";
  }
  $reclama01="<p class=travell0><center>Материалы сайта \"Автопутешествия по России\" <b>www.autotravel.ru</b><br><br></center></p>\n";

       $sql = "select t.name as name, t.hide as hide, t.descrip as descrip, a.name as aname from towns t, areas a where t.id = $id and a.id = t.area";
       $result_town = $dbi->query($sql);

       if ($myrow = mysqli_fetch_array($result_town)) {
        $thide = $myrow["hide"];
        $mname = $myrow["name"];
        $aname = $myrow["aname"];
        if ($thide == 1) {
         echo "<font class=\"t14 tb tc\">\n";
         echo "$mname";
         echo "</font>\n";
         if (($id <> 777)) {
          $trcount = 0;
          $sql_tr = "SELECT DISTINCT t.id AS trace_id, t.name AS trace FROM trace t, far_tr ft WHERE ft.town = '$id' AND t.id = ft.trace";
          $result_tr = $dbi->query($sql_tr); 
          if ($myrow_tr = mysqli_fetch_array($result_tr)) {
            $trview = " - пересечение дорог: ";
            do {
                $s_trace = $myrow_tr["trace"];
                $trcount++;
                $trview = $trview . "<br>$s_trace\n";
            } while($myrow_tr = mysqli_fetch_array($result_tr));
          }
          mysqli_free_result($result_tr);
          if ($trcount > 1) {
            echo "$trview\n";
          }
         }
        }

        if ($thide == 0) {
            echo "<div align=center><table border=1 width=97%>\n";
            echo "<tr><td align=center>\n";
            echo "<p class=\"t14 tb tc\">";
            echo "$mname ($aname)";
            echo "</p></td></tr>\n";
            echo "<tr><td class=travell0 align=left>\n";
            if (!(preg_match("/0/",$add))) {
             $tdescrip = gettowntext($id);
             if (!($tdescrip)) {
              $tdescrip = $myrow["descrip"];
             }
             echo $tdescrip;
             echo "<br>";
            }
            if (preg_match("/z/",$add)) {
             $photoimg=getfototown($id,0);
             if ($photoimg) {
              echo "<div align=center>$photoimg</div><br>";
             }
            }
            echo "</td></tr>\n";

// без окрестностей
            for ($ii=1; $ii<=$tarryl; $ii++) {
             if (preg_match('/'.$ii.'/',$add)) {
              $sql1="select id,name,descrip, Nshirota, Edolgota, votes, rate, wrate,
                     Right( name, (length(name) - locate('\"', name)) ) AS loc
                     from exciting where town=$id and type=$ii $sqladds";
#              echo "<br>$sql1<br>\n";
              $result1 = $dbi->query($sql1);
              if($myrow1 = mysqli_fetch_array($result1)) {
               echo "<tr><td align=center>\n";
               echo "<p class=\"t14 tb tc\">\n";
               echo $typearry[$ii];
               echo "</p>\n";
               echo "</td></tr>\n";
               do {
                 repdisplayexc($myrow1,"",$add);
               } while($myrow1 = mysqli_fetch_array($result1));
              }
              mysqli_free_result($result1);
             }
            }
//без окрестностей -- end.

            echo "</table>\n";
            echo "$reclama01\n";
// Город типа 0 закончен
        }

        if ($thide == 2) {

// hide = 2 == Объект на трассе
// Отображение для печати

              $sql_e="select id,name,descrip, Nshirota, Edolgota, votes, rate, 
                     wrate from exciting where town=$id";
              $result_e = $dbi->query($sql_e);
              if ($myrow_e = mysqli_fetch_array($result_e)) {
               echo "<div align=center><table border=1 width=97%>\n";
               repdisplayexc($myrow_e,$mname,$add);
               echo "</table>\n";
              }
              mysqli_free_result($result_e);


        }
       }
       mysqli_free_result($result_town);
 }

// display_report закончилась
// показывает отдельный объект в отчете



 function lasttown($trip) {
// возвращает id последнего города по маршруту

  if ($trip) {
   preg_match('/.*t(\d+).*?$/',$trip,$obj);
   $id=$obj[1];
  }
  else {
   $id=0;
  }
  return $id;
 }
// lasttown закончилась


 function AddTripToTown($trip,$trace,$id) {

//   Добавляет к текущему пути трассу и промежуточные пункты до указанного города
//   Первый параметр - путь, второй - трасса, третий - новый город
//   Проверок НЕТ!!!

  Global $dbi;

     $oldt = lasttown($trip);
     $sqlf = "select cw_far from far_tr where trace=$trace and town=$oldt";
     $resf = $dbi->query($sqlf);
     if ($rowf = mysqli_fetch_array($resf)) {
       $oldf = $rowf["cw_far"];
     }
     else {
       $oldf = 0;
     }
     mysqli_free_result($resf);
     $sqlf = "select cw_far from far_tr where trace=$trace and town=$id";
     $resf = $dbi->query($sqlf);
     if ($rowf = mysqli_fetch_array($resf)) {
       $newf = $rowf["cw_far"];
     }
     else {
       $newf = 0;
     }
     mysqli_free_result($resf);

//   Были найдены два расстояния по трассе между последним и новым городом.
//   Теперь надо выбрать все промежуточные объекты по ней, по порядку

     if ($oldf > $newf) {
      $sqlt = "select town,cw_far from far_tr where trace=$trace and cw_far<=$oldf and cw_far>=$newf and town <> $oldt order by cw_far desc";
     }
     else {
      $sqlt = "select town,cw_far from far_tr where trace=$trace and cw_far>=$oldf and cw_far<=$newf and town <> $oldt order by cw_far";
     }

//   Добавляем к маршруту все промежуточные объекты

     $rest = $dbi->query($sqlt);
     while ($rowt = mysqli_fetch_array($rest)) {
       $tfar = $rowt["cw_far"];
       $tid  = $rowt["town"];
       $far  = abs($tfar - $oldf);
       $oldf = $tfar;
       $trip = $trip.",r".$trace."x".$far.","."t".$tid . $add;
     }
     mysqli_free_result($rest);

     return $trip;
 }

 function gettown($trip,$num) {
// возвращает id города в маршруте по номеру. Если неуспех - возвращает 0.
  $id=0;
  if ($trip) {

   $objs = explode(",",$trip);
   $obje = $objs[$num];
   preg_match('/^(\D)(\d+)(x*)([^,]*)$/',$obje,$obja);
   if ($obja[1] == "t") {
    $id = (int) $obja[2];
   }
  }
  return $id;
 }

 function CheckTrip($trip,$num) {
// проверяет, нет ли в дальнейшем $trip того же town, что и в номере $num. 
// если есть - возвращяет номер, иначе 0.

  $foundid=0;
  $objs = explode(",",$trip);
  $obje = $objs[$num];
  preg_match('/^(\D)(\d+)(x*)([^,]*)$/',$obje,$obja);
  $lastid = sizeof($objs)-1;
  if ($obja[1] == "t") {
   $townid=$obja[2];
   for ($ii=$num+1; $ii<=$lastid; $ii++) {
    $obje = $objs[$ii];
    preg_match('/^(\D)(\d+)(x*)([^,]*)$/',$obje,$obja);
    if (($obja[1] == "t") and ($obja[2] == $townid)) {
     $foundid = $ii;
    }
   }
  }
  return $foundid;
 }

 function GetDistance($trip) {
// возвращает сумму "расстояний между точками" в $trip.

  $objs = explode(",",$trip);
  if (is_array($objs)) {
   reset($objs);
   $totaltrip = 0;
   foreach ($objs as $obje) {
    preg_match('/^(\D)(\d+)(x*)([^,]*)$/',$obje,$obja);
    if ($obja[1] == "r") {
     if (($obja[3] == "x") and ($obja[4] <> "")) {
      $totaltrip = $totaltrip + (int) $obja[4];
     }
    }
   }
  }
  return $totaltrip;
 }

 function find_town($spring,$direct,$prev_trip) {
// раньше была в ch_test
  Global $dbi;

  $sql = "SELECT fs.trace as trace FROM far_tr AS fs, far_tr AS fd WHERE fs.town =$spring AND fd.town = $direct AND fs.trace = fd.trace"; 
  //echo "$sql<br>";
  $result = $dbi->query($sql); 
  if ($myrow =  mysqli_fetch_array($result)) {
	$trace = $myrow["trace"];
	$trip=AddTripToTown($prev_trip,$trace,$direct);  
	return $trip;
  }
 }

 function CalcTrip($spring,$direct) {
  Global $dbi;
 
  // Рассчитываем маршрут
  $prev_trip = "t".$spring; 
  $trip = find_town($spring,$direct,$prev_trip);
  $dt_min = 0;
  if ($trip == "") {
   $sql_t = "select fn.town as town 
             from far_tr as fs, far_tr as fd, far_tr as fn, far_tr as fn1 
             where fs.town = $spring and fn.trace = fs.trace and 
             fn.town = fn1.town and fd.trace = fn1.trace and fd.town = $direct";
   $result_t = $dbi->query($sql_t); 
   //echo $sql_t;
   if ($myrow_t =  mysqli_fetch_array($result_t)) {
    do {
     $temp_t = $myrow_t["town"];
     $t_trip = find_town($spring,$temp_t,$prev_trip);
     $trip_m = find_town($temp_t,$direct,$t_trip);
//Блок минимизации на скорую руку
     $dt = Getdistance($trip_m);
     if ($dt_min <> 0) {
       if ($dt < $dt_min) {
       $trip = $trip_m;
       $dt_min = $dt;
     }
     } else {
       $dt = Getdistance($trip_m);
       $dt_min = $dt;
       $trip = $trip_m;
     } 
//Блок минимизации на скорую руку
                                   
    } while ($myrow_t =  mysqli_fetch_array($result_t));	

   } else {
    $sql_tt = "select f1.town as town_1, f3.town as town_2 
               from far_tr as fs, far_tr as fd, far_tr as f1, far_tr as f2, far_tr as f3, far_tr as f4 
               where fs.town = $spring and fs.trace = f1.trace and 
               f1.town != $spring and f1.town = f2.town and 
               f2.trace = f3.trace and f3.town != f2.town and 
               f4.town = f3.town and f4.trace = fd.trace and fd.town = $direct";
    $result_tt = $dbi->query($sql_tt); 
    // echo "$sql_tt<br>";
    if ($myrow_tt =  mysqli_fetch_array($result_tt)) {
      do {
       $temp_1 = $myrow_tt["town_1"];
       //echo $temp_1;
       $temp_2 = $myrow_tt["town_2"];
       $trip_1 = find_town($spring,$temp_1,$prev_trip);
       $trip_2 = find_town($temp_1,$temp_2,$trip_1);
       $trip_m = find_town($temp_2,$direct,$trip_2);
//Блок минимизации на скорую руку
       $dt = Getdistance($trip_m);
       if ($dt_min <> 0) {
        if ($dt < $dt_min) {
         $trip = $trip_m;
         $dt_min = $dt;
        }
       } else {
        $dt = Getdistance($trip_m);
        $dt_min = $dt;
        $trip = $trip_m;
       } 
//Блок минимизации на скорую руку
   
      }  while ($myrow_tt =  mysqli_fetch_array($result_tt));
    } 
   }
  }
// здесь лежит готовый $trip.
  return $trip;
 }
?>
