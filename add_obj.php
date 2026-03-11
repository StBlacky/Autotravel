<?php

include_once("include/config.php");
include_once("include/func.php");
include("include/head01.php");
include_once("include/f_trip.php");

// всплывающее окно по добавлению ответвления. Функциональность минимальна.

#echo $oldtrip;
$oldtrip = $_REQUEST["oldtrip"];  
$oldtrip = mysafestring($oldtrip);

$trip = $_REQUEST["trip"];  
$trip = mysafestring($trip);

$submitt = $_REQUEST["submitt"];  
$submitt = mysafestring($submitt);
$submitx = $_REQUEST["submitx"];  

$type = $_REQUEST["type"];  
$type = mysafestring($type);

$view = $_REQUEST["view"];  
$view = mysafestring($view);
$view = substr($view,0,1);

$id = $_REQUEST["id"];  
$id = (int) mysafestring($id);

$trace = $_REQUEST["trace"];  
$trace = (int) mysafestring($trace);

$far = $_REQUEST["far"];  
$far = (int) mysafestring($far);

$num = $_REQUEST["num"];  
$num = (int) mysafestring($num);

$oldnum = $_REQUEST["oldnum"];  
$oldnum = (int) mysafestring($oldnum);

$oldtown = $_REQUEST["oldtown"];  
$oldtown = (int) mysafestring($oldtown);

if ($oldtrip) {
 $id=gettown($oldtrip,$num);
 $type="r";
 $trip="";
 $oldnum=$num;
 $trace = "";
 if ($id) { 
    $trip="t$id";
//  Это для списка выбора
    $sql = "SELECT DISTINCT t.id AS id, t.name as name, t.of_name FROM trace t, far_tr ft WHERE ft.town = '$id' AND t.id = ft.trace";
 }
 $oldtown=$id;
}

if ($submitt) {

 $type = substr($type,0,1);
 $id = sprintf("%d",$id);
 switch($type) {
  case "t":
    if ($trace) {
     $trip = AddTripToTown($trip,$trace,$id);
     $trip = AddTripToTown($trip,$trace,$oldtown);
    }
    else {
     if ($trace == "0") {
      $far = (int) $far;
      $trip = $trip.",r0x".$far.",t".$id.",r0x".$far.",t".$oldtown;
     }
    }
    $sql="";
    $type="";
  break;
  case "r":
//   Добавляем трассу
     $trace = $id;
     $id = $oldtown;
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
      $sql = "select name, id, hide from towns where hide=0 order by name";
      $type = "t";
     }
     break;
 }
}

$view = "a";
if ($trip) {
 echo "<br><br>";
 include("v_trip.php");
}

echo "<form name=strip method=post action=".$_SERVER["PHP_SELF"]."#last>\n";
echo "<input type=hidden name=trip value='$trip'>\n";
echo "<input type=hidden name=oldtown value='$oldtown'>\n";
echo "<input type=hidden name=oldnum value='$oldnum'>\n";
echo "<input type=hidden name=type value='$type'>\n";
echo "<input type=hidden name=trace value='$trace'>\n";
if ($trace == "0") {
   $far = (int) $far;
// если на предыдущем шаге была "пользовательская трасса"
  echo "<input type=hidden name=far value='$far'>\n";
}

if ($sql) {
//  echo "\n\n$sql\n\n";
// Если есть меню выбора
  echo "<table border=0 width=100%>";
  echo "<tbody class=travell0>";
  if (($type == "t") and ($trace == "0")) {

// Если последней выбрали "пользовательскую" трассу
   echo "<tr><td align=right width=50%>Введите ориентировочное расстояние:</td>\n";
   echo "<td align=left><input type=input name=far>\n";
   echo "</td></tr>\n";
  }

  echo "<tr><td align=center valign=top colspan=2>\n";
  if ($type == "r") {
   echo "Выберите дорогу, по которой поедете дальше:\n";
  }
  if ($type == "t") {
    echo "Выберите пункт на этой дороге:\n";
  }
  echo "</td></tr>\n";
  echo "<tr><td align=center valign=top colspan=2>\n";
  echo "<select size=1 name=id>\n";
  $result = $dbi->query($sql);
  while ($myrow = mysqli_fetch_array($result)) {
   $id = $myrow["id"];
   $display = $myrow["name"];
   if ($type == "r") {
    $tr_ofname = $myrow["of_name"];
    if (preg_match('/Паром/iu',$tr_ofname)) {
     $display = $display." (".$tr_ofname.")";
    }
   }
   if (($type == "t") and ($trace > 0)) {

//  Выводим расстояния к городам в списке выбора
    $newf = $myrow["cw_far"];
    $far = abs($oldf - $newf);

// Отображение перекрестков
    $trcount = 0;
    if (($id <> 777) and ($id <>1055)) {
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

    $display = $display." ($far км.)";

    if ($trcount > 0) {
      $display = $display." [$trview]\n";
    }

   }
   echo "<option value=$id>".$display."\n";
  }
  if ($type == "r") {

// Дополнительная "пользовательская" трасса
   echo "<option value=0>Нужной дороги не указано.\n";
  }
  mysqli_free_result($result);
  echo "</select>\n";
  echo "<input type=submit name=submitt value='Выбрать'>\n";
  echo "</td></tr>\n";
  echo "</tbody>\n";
  echo "</table>\n";
 }

 else {
  echo "<div align=center><input type=submit name=submitx value='Добавить' Onclick=AddATrip($oldnum)></div>\n";
 }

 echo "</form>\n";



?>