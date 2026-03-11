<?php 

// предназначается для выбора маршрута
//
// пример маршрута:
// t51,r101x159,t48,r102x126,t47x23yz,r33x180,t1061

include_once("include/func.php");
include_once("include/head00.php");
include_once("include/f_trip.php");
$_text_mode  = 1;
$_short_menu = 1;
$title = "Проложить маршрут, маршрут по России";

$submitt = $_REQUEST["submitt"];  
$submitx = $_REQUEST["submitx"];  

$type = $_REQUEST["type"];  
$type = mysafestring($type);

$view = $_REQUEST["view"];  
$view = mysafestring($view);
$view = substr($view,-1);

$id = $_REQUEST["id"];  
$id = (int) mysafestring($id);

$trace = $_REQUEST["trace"];  
$trace = (int) mysafestring($trace);

$far = $_REQUEST["far"];  
$far = (int) mysafestring($far);

$num = $_REQUEST["num"];  
$num = mysafestring($num);

$trip = $_REQUEST["trip"];  
$trip = mysafestring($trip);

$add = $_REQUEST["add"];
$add = mysafestring($add);

$xid = $_REQUEST["xid"];  
$xid = (int) mysafestring($xid);

$adds=array();
$newadds = $_REQUEST["adds"];
if (is_array($newadds)) {
 reset ($newadds);
 while (list ($key, $val) = each ($newadds)) {
  $adds[] = mysafestring($val);
 }
}
else {
 $adds = mysafestring($newadds);
}

if (!$trip) {
// Если маршрута нет, то выбирать точку старта

 $sql = "";
 $type = "t";
 $trace = "";
 $view="t";
 $xid = 0;
}

if (!$view) {
 $view = "t";
}

//echo "$trip<br>$submitt<br>$num<br>$view<br>$add<br>$xid<br>$type<br>\n";

if ($view == "t") {
 if ($xid > 0) {
// автопостроение
  $spring = lasttown($trip);
  if ($spring > 0) {
   $type  = CalcTrip($spring,$xid);
   $objs = explode(",",$trip);
   $num = sizeof($objs)-1;
   $obje0 = $objs[$num];
   preg_match('/^(\D)(\d+)(x*)([^,]*)$/',$obje0,$obja0);
   $objs2 = explode(",",$type);
   $obje1 = $objs2[0];
   preg_match('/^(\D)(\d+)(x*)([^,]*)$/',$obje1,$obja1);
   $obje2 = $objs2[sizeof($objs2)-1];
   preg_match('/^(\D)(\d+)(x*)([^,]*)$/',$obje2,$obja2);

//   echo $obje1.$obje2.$obje0;

   if (($obja0[1] == "t") and ($obja1[1] == "t") and ($obja2[1] == "t")) {
    if(($obja0[2] == $obja1[2])) {
     $objs2[0] = $objs[$num];
     $objs=array_slice($objs,0,$num);
     $trip=join(array_merge($objs,$objs2),",");
    }
   }
   $submitt = "";
   $trace="";
   $id="";
  }
 }

 if ($num <> "") {
// сюда щёлкают кнопки рядом с точками по маршруту
  switch($add) {
  case "Delete": 

//  убрать из маршрута (не знаю что)

   if ($num == 0) {
    preg_match('/^t\d+.*?(t\d+.*)$/',$trip,$tmp);
    $trip = $tmp[1];
   }
   else {
    $objs = explode(",",$trip);
    if (is_array($objs)) {
     if ($num == (sizeof($objs) -1)) {
      preg_match('/^(.*t\d+x*[^,]*),.*?$/',$trip,$tmp);
      $trip = $tmp[1];
     }
     else {
      $obje = $objs[$num];
      preg_match('/^(\D)(\d+)(x*)([^,]*)$/',$obje,$obja);
      if ($obja[1] == "t") {
       $obje0 = $objs[$num-1];
       preg_match('/^(\D)(\d+)(x*)([^,]*)$/',$obje0,$obja0);
       $obje1 = $objs[$num+1];
       preg_match('/^(\D)(\d+)(x*)([^,]*)$/',$obje1,$obja1);
       if (($obja0[1] == "r") and ($obja1[1] == "r")) {
        $far= (int) $obja0[4] + (int) $obja1[4];
        if ($obja0[2] == $obja1[2]) {
         $trace=$obja1[2];
        }
        else {
         $trace=0;
        }
        $objs[$num-1]="r".$trace."x".$far;
        $objs=array_merge(array_slice($objs,0,$num),array_slice($objs,$num+2));
        $trip=join($objs,",");
#        echo "$trip0";
       }
      }
     }
    }
   }
  break;
  case "DeleteAll": 

//  убрать из маршрута хвост после точки $num

   $objs = explode(",",$trip);
   if (is_array($objs)) {
    $objs=array_slice($objs,0,$num+1);
    $trip=join($objs,",");
   }
   else {
    $trip="";
   }
  break;

  case "DeleteTrip": 

//  убрать из маршрута ответвление в точке $num

    $foundid=CheckTrip($trip,$num);
    $objs = explode(",",$trip);
    if ($foundid > 0) {
      $objs=array_merge(array_slice($objs,0,$num+1),array_slice($objs,$foundid+1));
      $trip=join($objs,",");
    }
  break;

  case "AddATrip": 

// добавить маршрут $type к $trip, в точку $num
//   echo "$type<br>\n$trip<br>\n";
   $objs = explode(",",$trip);
   $obje0 = $objs[$num];
   preg_match('/^(\D)(\d+)(x*)([^,]*)$/',$obje0,$obja0);
   $objs2 = explode(",",$type);
   $obje1 = $objs2[0];
   preg_match('/^(\D)(\d+)(x*)([^,]*)$/',$obje1,$obja1);
   $obje2 = $objs2[sizeof($objs2)-1];
   preg_match('/^(\D)(\d+)(x*)([^,]*)$/',$obje2,$obja2);
   if (($obja0[1] == "t") and ($obja1[1] == "t") and ($obja2[1] == "t")) {
//    echo $obje1.$obje2.$obje0;
    if(($obja0[2] == $obja1[2]) and ($obja0[2] == $obja2[2])) {
     $objs0=array_slice($objs,0,$num);
     $objs2[0] = $objs[$num];
     $objs1=array_merge($objs2,array_slice($objs,$num+1));
     $trip=join(array_merge($objs0,$objs1),",");
    }
   }
  break;

  default:
// для редактирования километража по пользовательской трассе
   $objs = explode(",",$trip);
   if (is_array($objs)) {
    $obje = $objs[$num];
    preg_match('/^(\D)(\d+)(x*)([^,]*)$/',$obje,$obja);
    if ($obja[1] == "r") {
     $objs[$num]="r".$obja[2]."x".(int)$far." км.";
    }
    $trip=join($objs,",");
   }
  break;
  }

  $submitt = "";
  $trace="";
  $id="";
 }

 if ($submitt) {

// Добавляем новую подстроку к маршруту
  $type = substr($type,0,1);
  $id = sprintf("%d",$id);
  switch($type) {
   case "t":
//  Добавляется город
    if ($trace) {
//   Город не первый (до него была трасса и она есть в базе)
//   Первый параметр - путь, второй - трасса, третий - новый город
//   Проверок НЕТ!!!

     $trip = AddTripToTown($trip,$trace,$id);

    }
    else {

     if ($trace == "0") {

//    Пользовательская трасса
      $far = (int) $far;

      $trip = $trip.",r0x".$far.",t".$id;
     }
     else {

//    Самый первый город маршрута.
      $trip = "t".$id;
     }
    }

//  Обработка трассы, шедшей к городу, закончилась

//  Это для списка выбора
//    $sql = "SELECT DISTINCT t.id AS id, (t.name + ' (' + t.of_name + ')') as name FROM trace t, far_tr ft WHERE ft.town = '$id' AND t.id = ft.trace";
    $sql = "SELECT DISTINCT t.id AS id, t.name as name, t.of_name as of_name FROM trace t, far_tr ft WHERE ft.town = '$id' AND t.id = ft.trace";
    $trace = "";
    $type = "r";
    break;
  case "r":

//   Добавляем трассу
     $trace = $id;
     $id = lasttown($trip);
     if ($trace != "0") {

//    Трасса есть в базе, находим координату последнего города по маршруту
      $sql = "SELECT t.id as id, t.name as name, f.cw_far as cw_far, t.hide as hide from towns t, far_tr f where f.trace=$trace and f.town=t.id and t.id<>$id order by cw_far";
      $type = "t";
      $sqlf = "select cw_far from far_tr where trace=$trace and town=$id";
      $resf = $dbi->query($sqlf);
      if ($rowf = mysqli_fetch_array($resf)) {
       $oldf = $rowf["cw_far"];
      }
      else {
       $oldf = 0;
      }
      mysqli_free_result($resf);
     }
     else {

//    Для списка выбора - все города.
      $sql = "";
      $type = "t";
     }
     break;
  }
 }

 if ((!($submitt)) and (!($submitx))) {

// Если вызвали не из формы добавления/удаления

  preg_match('/^(.*t\d+x*[^,]*).*?$/',$trip,$tmp);
  $trip = $tmp[1];
  $id = lasttown($trip);
  if ($id) {
    $sql = "SELECT DISTINCT t.id AS id, t.name as name, t.of_name as of_name FROM trace t, far_tr ft WHERE ft.town = '$id' AND t.id = ft.trace";
    $type = "r";
  }
  else {

//  если при этом в маршруте не нашлось города: пойти на старт

    $sql = "";
    $type="t";
    $trace = "";
    $trip = "";
    $view = "t";
  }
 } 

}



if ($view == "e") {
// начинается обработка "редактирование отчёта": 

 if (!(($add == "AddMany") or ($add == "AddAll") or ($add == "DelMany"))) {

// это если вызывался из всплывающего окна.
  if ($num != "") {
   $objs = explode(",",$trip);
   if (is_array($objs)) {
    $obje = $objs[$num];
#echo "$trip<br>$obje<br>$view<br>$add<br>\n";
    preg_match('/^(\D)(\d+)(x*)([^,]*)$/',$obje,$obja);
    if ($obja[1] == "t") {
     $objs[$num]="t".$obja[2]."x".$add;
    }
    $trip=join($objs,",");
   }
  }
 }
 else {

// Это если прямо из формы

  if (($add == "AddMany") or ($add == "DelMany")) {
   $nums = explode(",",$num);
  }
  if (is_array($adds)) {
   $addin=join($adds);
  }
  else {
   $addin = "";
  }
  if (($trip) and ($addin<>"")) {
   $objs = explode(",",$trip);
   if (is_array($objs)) {
    reset($objs);
    $trip="";
    $ij = 0;
    foreach ($objs as $obje) {
     preg_match('/^(\D)(\d+)(x*)([^,]*)$/',$obje,$obja);
     $addon=$obja[4];
     $ext  =$obja[3];
     if (($add == "AddAll") or (($add == "AddMany") and in_array($ij,$nums))) { 
// обработка только городов
      if ($obja[1] == "t") {
       $addout=$obja[4];
       $thide=1;
       $sqlx = "select hide from towns where id = ".$obja[2];
       $result_town = $dbi->query($sqlx);
       if ($myrow = mysqli_fetch_array($result_town)) {
        $thide = $myrow["hide"];
       }
       if ($thide == 0) {
        $addon="";
        for ($ii=1; $ii<=$tarryl; $ii++) {
         if ((preg_match('/'.$ii.'/',$addin)) or (preg_match('/'.$ii.'/',$addout))) {
          $addon=$addon.$ii;
         }
        }
        if ((preg_match("/0/",$addin)) or (preg_match("/0/",$addout))) {
          $addon=$addon."0";
        }
        if ((preg_match("/x/",$addin)) or (preg_match("/x/",$addout))) {
          $addon=$addon."x";
        }
        if ((preg_match("/y/",$addin)) or (preg_match("/y/",$addout))) {
          $addon=$addon."y";
        }
        if ((preg_match("/z/",$addin)) or (preg_match("/z/",$addout))) {
          $addon=$addon."z";
        }
       }
       if ($thide == 2) {
        $addon="";
        if ((preg_match("/0/",$addin)) or (preg_match("/0/",$addout))) {
          $addon=$addon."0";
        }
        if ((preg_match("/x/",$addin)) or (preg_match("/x/",$addout))) {
          $addon=$addon."x";
        }
        if ((preg_match("/y/",$addin)) or (preg_match("/y/",$addout))) {
          $addon=$addon."y";
        }
        if ((preg_match("/z/",$addin)) or (preg_match("/z/",$addout))) {
          $addon=$addon."z";
        }
       }
       if ($thide == 1) {
         $addon="";
       }
       if ($addon) {
         $ext = "x";
       }
       else {
         $ext = "";
       }
      }
     }

     if (($add == "DelMany") and in_array($ij,$nums)) { 
// обработка только городов
      if ($obja[1] == "t") {
       $addout=$obja[4];
       $thide=1;
       $sqlx = "select hide from towns where id = ".$obja[2];
       $result_town = $dbi->query($sqlx);
       if ($myrow = mysqli_fetch_array($result_town)) {
        $thide = $myrow["hide"];
       }
       if ($thide == 0) {
        $addon="";
        for ($ii=1; $ii<=$tarryl; $ii++) {
         if ((preg_match('/'.$ii.'/',$addout)) and (!(preg_match('/'.$ii.'/',$addin)))) {
          $addon=$addon.$ii;
         }
        }
        if ((preg_match("/0/",$addout)) and (!(preg_match("/0/",$addin)))) {
          $addon=$addon."0";
        }
        if ((preg_match("/x/",$addout)) and (!(preg_match("/x/",$addin)))) {
          $addon=$addon."x";
        }
        if ((preg_match("/y/",$addout)) and (!(preg_match("/y/",$addin)))) {
          $addon=$addon."y";
        }
        if ((preg_match("/z/",$addout)) and (!(preg_match("/z/",$addin)))) {
          $addon=$addon."z";
        }
       }
       if ($thide == 2) {
        $addon="";
        if ((preg_match("/0/",$addout)) and (!(preg_match("/0/",$addin)))) {
          $addon=$addon."0";
        }
        if ((preg_match("/x/",$addout)) and (!(preg_match("/x/",$addin)))) {
          $addon=$addon."x";
        }
        if ((preg_match("/y/",$addout)) and (!(preg_match("/y/",$addin)))) {
          $addon=$addon."y";
        }
        if ((preg_match("/z/",$addout)) and (!(preg_match("/z/",$addin)))) {
          $addon=$addon."z";
        }
       }
       if ($thide == 1) {
         $addon="";
       }
       if ($addon) {
         $ext = "x";
       }
       else {
         $ext = "";
       }
      }
     }

     $ij++;

     if ($trip) {
       $trip=$trip.",".$obja[1].$obja[2].$ext.$addon;
     }
     else {
       $trip=$obja[1].$obja[2].$ext.$addon;
     }

    }
   }
  }
 }
// добавление закончилось

}

if (($view == "w") or($view == "r")) {
 include("include/head01.php");
}
else {
 include("include/header.php");
}

echo "<form name=chform>";
if ($trip) {
  echo "<br>\n";
  include("v_trip.php");
}
echo "</form>";

// показ закончен. Теперь форма ввода

if ($view == "t") {
 echo "<a name=last>&nbsp;</a>";
 echo "<form name=strip method=post action=".$_SERVER["PHP_SELF"]."#last>\n";
 echo "<input type=hidden name=trip value='$trip'>\n";
 echo "<input type=hidden name=type value='$type'>\n";
 echo "<input type=hidden name=trace value='$trace'>\n";
 echo "<input type=hidden name=view value='t'>\n";
// Для javascript-а вызываемого из ed_obj
 echo "<input type=hidden name=num value=''>\n";
 echo "<input type=hidden name=add value=''>\n";
 echo "<table border=0 width=100% cellspacing=10>";
 echo "<tbody class=travell0>";
 if (!(strpos($trip,','))) {
  if ( (!($trip)) and ($tarray["txt"] != 1) ) { ?>
  <?php } ?>
  <tr><td colspan=2><h1 class=travel5>Проложить маршрут</h1></td></tr>
  <tr><td colspan=2><hr><b>Краткое описание прокладки маршрута</b>. Это меню 
   <font class=travell5m>не предназначено для нахождения "кратчайшего расстояния от точки до точки"</font><br>
   А для чего тогда?
   <ul>
   <li>Наметить маршрут</li>
   <li>Посмотреть, что интересного есть по предполагаемому маршруту</li>
   <li>По интересным городам спланировать остановки и ответвления маршрута</li>
   <li>Спланировать посещение достопримечательностей и прочих объектов</li>
   <li>Подготовленный маршрут распечатать или обменяться им со знакомыми</li>
   </ul>
  Полезные приёмы работы:
  <ol>
  <li>Посмотрите в меню <a class=travell5m href=/triptrack.php>готовые маршруты</a>: возможно, всё уже сделано до Вас</li>
  <li>Маршрут получается намного более читаемым, если в него не включать 
  "окрестности родного города", т.е, к примеру, при путешествии <br>
   Москва - Ярославль - Кострома - Плёс - Нижний Новгород - Владимир - Москва<br>
   строить маршрут<br> Ярославль - Кострома - Плёс - Нижний Новгород - Владимир</li>
  <li>При "автоматическом построении" маршрута указывать промежуточные населённые
   пункты, через которые Вы собираетесь проехать. Иначе автоматика легко может увести Вас в сторону от желаемого путешествия</li> 
  <li>Если Вы точно не хотите останавливаться в промежуточных пунктах по трассе, 
   выбирайте "построение вручную - дорога не указана", это сильно сэкономит Ваше время.</li>
  <li>Если воспользоваться "предварительным просмотром в новом окне", то станет очевидно,
  что именно можно найти в том или ином городе</li>
  </ol>
  <p class=travell5m>Внимание! Функциональность рассчитана на работу со "всплывающими окнами". Пожалуйста, включите "всплывающие окна" в своём браузере для нашего сайта autotravel.ru</p>
  <hr>
  </td>
  <?php if (!($trip)) { ?>
  </tr>
  <tr><td align=right width=30%><font class=travell5m>Пункт старта</font>:</td>
  <td align=left><input type=button name=helpid value='Выбрать...' onclick=NewSelId('strip','id','/s_town.php','yes')></td></tr>
  <input type=hidden name=autosubmit value='yes'>
  <input type=hidden name=submitt value='yes'>
  <input type=hidden name="id" value=0></td>
  <?php } ?>
  </tr>
<?php
 }
 echo "<tr>";
 if (($type == "t") and ($trace == "0")) {
// Если последней выбрали "пользовательскую" трассу
   echo "<tr><td align=right width=50%>Введите ориентировочное расстояние:</td>\n";
   echo "<td align=left><input type=input name=far>\n";
   echo "</td></tr>\n";
   echo "<tr><td align=right width=50%><font class=travell5m>Следующий пункт</font> по маршруту:</td>\n";
   echo "<td align=left><input type=button name=helpid value='Выбрать...' onclick=NewSelId('strip','id','/s_town.php','yes')></td></tr>\n";
   echo "<input type=hidden name=autosubmit value='yes'>\n";
   echo "<input type=hidden name=submitt value='yes'>\n";
   echo "<input type=hidden name=id value=0></td></tr>\n";
  }
 if ($sql) {
// Если есть меню выбора
  if ($type == "r") {
   echo "<td width=50% align=right>\n";
   echo "<font class=travell5m>Автоматическое</font> построение дальнейшего маршрута.<br>\nМаршрут строится, исходя из минимизации смены дорог:<br>\n";
   echo "<table border=0 width=100%>\n";
   echo "<tbody class=travell0><tr>\n";
   echo "<td align=right width=50%><font class=travell5m>Следующий пункт</font> по маршруту:</td>\n";
   echo "<td align=left><input type=button name=helpxid value='Выбрать...' onclick=XSelId('strip','xid','/s_town.php','$id')></td></tr>\n";
   echo "<input type=hidden name=autosubmit value='yes'>\n";
   echo "<input type=hidden name='xid' value=0></td></tr></table>\n";
   echo "</td><td>\n";
   echo "Построение <font class=travell5m>вручную</font>. Выберите дорогу, по которой поедете дальше:<br>\n";
  }
  if ($type == "t") {
   echo "<tr><td align=center valign=top colspan=2>\n";
   echo "Выберите пункт на этой дороге:\n";
  }
  echo "<select size=1 name=id>\n";
  $result = $dbi->query($sql);
  while ($myrow = mysqli_fetch_array($result)) {
   $id = $myrow["id"];
   $mname = $myrow["name"];
   $display = $mname;
   if ($type == "r") {
    $tr_ofname = $myrow["of_name"];
    if ($tr_ofname) {
     $display = $display." (".$tr_ofname.")";
    }
   }
   if (($trip) and ($type == "t") and ($trace > 0)) {

// Отображение перекрестков
    $trcount = 0;
    $sql_tr = "SELECT DISTINCT t.id AS trace_id, t.of_name AS trace FROM trace t, far_tr ft WHERE ft.town = '$id' AND t.id = ft.trace";
    $result_tr = $dbi->query($sql_tr); 
    if ($myrow_tr = mysqli_fetch_array($result_tr)) {
      $trview = "П: \n";
      do {
       $tr_id = $myrow_tr["trace_id"];
       if ($tr_id <> $trace) {
        $s_trace = $myrow_tr["trace"];
        $trcount++;
        if ($s_trace) {
         $trview = $trview . "$s_trace \n";
        }
        else {
         $trview = $trview . "? \n";
        }
       }
      } while($myrow_tr = mysqli_fetch_array($result_tr));
    }
    mysqli_free_result($result_tr);

//  Выводим расстояния к городам в списке выбора
    $newf = $myrow["cw_far"];
    $far = abs($oldf - $newf);
    $display = $display." ($far км.)";

    if ($trcount > 0) {
      $display = $display." [$trview]\n";
    }

   }

//  Отображение примечательностей в городах второго типа.
    $thide = $myrow["hide"];
    if ($thide == 2) {
    $sql_e = "select name from exciting where town = $id";
    $result_e = $dbi->query($sql_e);
    if ($myrow_e = mysqli_fetch_array($result_e)) {
     $dname = $myrow_e["name"];
     if (!($mname)) {
        $mname=$display." ".$dname;
     }
     else {
      if ($mname<>$dname) {
        $display = $display.". ".$dname;
      }
     }
    }
    mysqli_free_result($result_e);
    }

   echo "<option value=$id>".$display."\n";
  }

  if (($trip) and ($type == "r")) {

// Дополнительная "пользовательская" трасса
   echo "<option value=0>Нужной дороги не указано.\n";
  }
  mysqli_free_result($result);
  echo "</select>\n";
  echo "</td></tr><tr><td colspan=2 align=center>\n";
  echo "<input type=submit name=submitt value='Продолжить'>\n";
  echo "</td>\n";
 }
 echo "</tr></table></form>\n";
 if (($trip) and ($type == "r")) {
// Если последним был город и маршрут опять готов
  echo "<form name=report method=post action=".$_SERVER["PHP_SELF"].">\n";
  echo "<input type=hidden name=trip value='$trip'>\n";
  echo "<input type=hidden name=view value='e'>\n";
  echo "<div align = center><br>\n";
  echo "<input type=submit name=edit value='Подготовить маршрут для печати'><br>\n";
  echo "</div></form>\n";

  echo "<div align = center><p class=travell0>\n";
  echo "[ <a href='".$_SERVER["PHP_SELF"]."?trip=$trip&view=w' target=_blank>Предпросмотр в отдельном окне</a> ]&nbsp;&nbsp;\n";
  echo "[ <a href='".$_SERVER["PHP_SELF"]."?trip=$trip&view=v'>Прямой линк на этот маршрут</a> ]\n";
  echo "</p></div>\n";

  echo "<BR><BR>\n";
 }
}

if ($view == "e") {
  echo "<div align=center><table border=1 borderwidth=0 width=60%>";
  echo "<tbody class=travell0>";
  echo "<tr><td align=left rowspan=2 width=50%>";
  echo "<form name=strip method=post action=".$_SERVER["PHP_SELF"].">\n";
  echo "<input type=hidden name=trip value='$trip'>\n";
  echo "<input type=hidden name=view value='e'>\n";
// Для javascript-а вызываемого из ed_obj
  echo "<input type=hidden name=num value=''>\n";
  echo "<input type=hidden name=add value='AddAll'>\n";
  for ($ii=1; $ii<=$tarryl; $ii++) {
   if ($typearry[$ii]) {
     echo "<input type=checkbox name=adds[] value=$ii > ".$typearry[$ii]."<BR>\n";
   }
  }
//  echo "<input type=checkbox name=adds[] value='0'> Описание<BR>\n"; -- не работает
  echo "<input type=checkbox name=adds[] value=y> Отзывы<BR>\n";
  echo "<input type=checkbox name=adds[] value=z> Фото<BR>\n";
  echo "</td><td align=left>";
  echo "По всем пунктам маршрута: ";
  echo "<input type=submit name=submitx value='Добавить к объектам'><br>\n";
  echo "</tr><tr><td align=left>";
  echo "По выбранным пунктам маршрута: ";
  echo "<input type=submit name=submitx onclick=AddMany('AddMany') value='Добавить к объектам'><br>\n";
  echo "<input type=submit name=submitx onclick=AddMany('DelMany') value='Убрать из объектов'><br>\n";
  echo "</form>\n";
  echo "</td></tr></table></div>";
  echo "<div align=center><font class=travell5>Маршрут (в отдельном окне):</font>\n";
  echo "<form name=report target=_blank method=post action=".$_SERVER["PHP_SELF"].">\n";
  echo "<input type=hidden name=trip value='$trip'>\n";
  echo "<input class=atb type=submit name=view value='Вывести на печать r'>\n";
  echo "<input class=atb type=submit name=view value='Продолжить редактирование t'>\n";
  echo "<input class=atb type=submit name=view value='Предварительный просмотр w'>\n";
  echo "</form></p>\n";
  echo "<Form name=gmapx method=post action=kml.php target=_blank>\n";
  echo "<input type=hidden name=tlist value=\"$tlist\">\n";
  echo "<input class=atb type=submit name=f value='В файл GPX'>\n";
  echo "<input class=atb type=submit name=f value='В файл OZI WPT'>\n";
  echo "<input class=atb type=submit name=f value='В файл Google KML'>\n";
  echo "<input class=atb type=submit name=f value='На карте Google HTM'>\n";
  echo "</form>\n</div>\n";

  echo "<div align=center><p class=travell0>\n";
  echo "[ <a class=travell5m href='".$_SERVER["PHP_SELF"]."?trip=$trip&view=v'>Прямой линк на этот маршрут</a> ]\n";
  echo "</p></div>\n";

  echo "<div align=center><form target=_blank name=scity method=post action=/rd_report.php>\n";
  echo "<input type=hidden name=rtype[] value=1>\n";
  echo "<input type=hidden name=rtype[] value=2>\n";
  echo "<input type=hidden name=rtype[] value=3>\n";
  echo "<input type=hidden name=selmonth value=3>\n";
  echo "<input type=hidden name=selyear value=1>\n";
  echo "<input type=hidden name=city value=\"$townslist\">\n";
  echo "<font class=travell5>Отчёты о путешествиях по городам $townslist :</font><br>\n";
  echo "<input class=atb type=submit name=submit value=\"Выбрать\"></form></div>\n";


  echo "</p></div></form><BR><BR></p>\n";

}
	
if ($view != "r") {
/*  echo "<br><br><br><br><br><p class=travell3>На правах рекламы:  Устранение скрипов и <a href=http://www.admauto.ru/category/4 target=_blank>шумоизоляция автомобиля</a> необходимы для комфорта.</p>";
*/

 include("include/footer.php");
}
?>
