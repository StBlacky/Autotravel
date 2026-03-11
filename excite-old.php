<?php


include_once('include/config.php');
include_once('include/func.php');
include_once('include/head00.php');

if (preg_match("/^\/(\d+)\/(\d+)$/",$_pin,$exa)) {
 $town = $exa[1];
 $et   = $exa[2];
}
else {
 $et = $_REQUEST["et"];
 $et = (int) mysafestring($et);
 if ($et < 1) {
  $et = 1;
 }
 $town = $_REQUEST["town"];
 $town = (int) mysafestring($town);
}

$zzz = mysafestring($_REQUEST["z"]);
$sql_xy = 'SELECT `id`, `type`, `desc` from type_ex where id='."'$zzz'";
$zzz = "";
$zzzz = "";
$result = mysql_query($sql_xy, $db);
if($myrow = mysql_fetch_array($result)) {
  $zzzz = $myrow["desc"] . " - " . $myrow["type"];
  $zzz = $myrow["id"];
}


$flagresult="";

$r_types=array();
$sql_fd = "select name, id from type";
$result_fd = mysql_query($sql_fd, $db);
while ($row_trip = mysql_fetch_array($result_fd)) {
    $id_fd = $row_trip["id"];
    $r_types[$id_fd] = $row_trip["name"];
}
mysql_free_result($result_fd);

$et_name=$r_types[$et];
if (!($et_name)) {
 $et = 1;
 $et_name = $r_types[$et];
}

$sql1 ="select type, count(*) as ecount from exciting where town='$town' group by type";
$result1 = mysql_query($sql1, $db);
$r_excitings=array();
$c_excitings=array();
if($myrow1 = mysql_fetch_array($result1)) {

  do {
   $type_id = $myrow1["type"];
   $type_name = $r_types[$type_id];
   $r_excitings[$type_id] = $type_name;
   $c_excitings[$type_id] = (int) $myrow1["ecount"];
   $metadesc = $metadesc . $type_name . ", ";
  } while($myrow1 = mysql_fetch_array($result1));
}
mysql_free_result($result1);


$t_name = gettownnamearr($town);
$town_name = $t_name[1];

$title_h=$et_name . " " . $t_name[6];
$title = $title_h . " адреса, режим работы, отзывы";

if ($zzzz) {
 $title_h = $title_h . ": $zzzz";
}
$metatags = $title_h;
$metadesc = str_replace('"',' ',$metatags);

$metadesc = $metadesc . ", поиск по окрестностям, отзывы посетителей, интерактивная карта. ";

$metadesc = str_replace("\n",' ',$metadesc);
$metadesc = str_replace("\r",' ',$metadesc);
$metadesc = str_replace('"',' ',strip_tags($metadesc));

include("include/header.php");

echo "<h1>$title_h</h1>\n";

if ($et==1) {
 echo "<p class=travell0>";

 if ($_salp_mode) {
  echo "Список <b>достопримечательностей</b> формируется на основе <b>отзывов посетителей</b> и отсортирован по алфавиту.\n";
  echo "По <b>рейтингу</b>, который также формируется на основе отзывов посетителей, Вы можете выделить ";
  echo "<b>самые интересные достопримечательности</b>.\n";
 }
 else {
  echo "Список <b>достопримечательностей</b> формируется на основе <b>отзывов посетителей</b> и отсортирован по <b>рейтингу</b>, который также формируется на основе отзывов посетителей.\n";
  echo "Таким образом, <b>вверху списка</b> находятся <b>самые интересные достопримечательности</b>.\n";
 }
 echo "<br><br>Посмотреть <b>фотографии</b>, прочитать <b>отзывы</b>, на основе которых построен рейтинг, <b>оставить свой отзыв</b>, можно перейдя по ссылке на названии конкретной <b>достопримечательности</b>.<br>\n";
 echo "<br>Если Вы не нашли в этом списке интересного места, о котором хотели бы поделиться впечатлениями - оставьте информацию на нашем форуме, в разделе ";
 echo "<a class=travell5n target= _blank href=\"$forumname/viewforum.php?f=5\">Что посмотреть</a>.";
 echo "</p>";
}

if ($et==2) {
 echo "<p class=travell0>";
 if ($_salp_mode) {
  echo "Список <b>гостиниц/b> и <b>отелей</b> формируется на основе <b>отзывов посетителей</b> и отсортирован по  по алфавиту.\n";
  echo "По <b>рейтингу</b>, который также формируется на основе отзывов посетителей, Вы можете выделить ";
  echo "<b>наиболее понравившиеся</b> автопутешественникам <b>гостиницы</b>.\n";
 }
 else {
  echo "Список <b>гостиниц</b> и <b>отелей</b> формируется на основе <b>отзывов посетителей</b> и отсортирован по <b>рейтингу</b>, который также формируется на основе отзывов посетителей.\n";
  echo "Таким образом, <b>вверху списка</b> находятся <b>наиболее понравившиеся<b> автопутешественникам <b>гостиницы</b>.\n";
 }
 echo "<br><br>Посмотреть <b>фотографии</b>, прочитать <b>отзывы</b>, на основе которых построен рейтинг, <b>оставить свой отзыв</b>, можно перейдя по ссылке на названии конкретной <b>гостиницы</b>.<br>\n";
 echo "<br>Если Вы не нашли в этом списке гостиницу, о которой хотели бы поделиться впечатлениями - <b>оставьте информацию</b> на нашем форуме, в разделе ";
 echo "<a class=travell5n target= _blank href=\"$forumname/viewforum.php?f=7\">Где остановиться</a>.";
 echo "</p>";
}

if ($et==3) {
 echo "<p class=travell0>";
 if ($_salp_mode) {
  echo "Список  кафе и ресторанов формируется на основе <b>отзывов посетителей</b> и отсортирован по  по алфавиту.\n";
  echo "По <b>рейтингу</b>, который также формируется на основе отзывов посетителей, Вы можете выделить ";
  echo "<b>наиболее понравившиеся автопутешественникам  кафе и рестораны</b>.\n";
 }
 else {
  echo "Список <b>кафе</b> и <b>ресторанов</b>  формируется на основе <b>отзывов посетителей</b> и отсортирован по <b>рейтингу</b>, который также формируется на основе отзывов посетителей.\n";
  echo "Таким образом, <b>вверху списка</b> находятся <b>наиболее понравившиеся</b> автопутешественникам <b>кафе</b> и <b>рестораны</b>.\n";
 }
 echo "<br><br>Посмотреть <b>фотографии</b>, прочитать <b>отзывы</b>, на основе которых построен рейтинг, <b>оставить свой отзыв</b>, можно перейдя по ссылке на названии конкретного <b>кафе</b> или <b>ресторана</b>.<br>\n";
 echo "<br>Если Вы не нашли в этом списке кафе или ресторан, о котором хотели бы поделиться впечатлениями - <b>оставьте информацию</b> на нашем форуме, в разделе ";
 echo "<a class=travell5n target= _blank href=\"$forumname/viewforum.php?f=6\">Где поесть</a>.";
 echo "</p>";
}

if ($et==8) {
 echo "<p class=travell0>";
 if ($_salp_mode) {
  echo "Список  праздников и мероприятий формируется на основе <b>отзывов посетителей</b> и отсортирован по  по алфавиту.\n";
  echo "По <b>рейтингу</b>, который также формируется на основе отзывов посетителей, Вы можете выделить ";
  echo "<b>наиболее интересные события</b>.\n";
 }
 else {
  echo "Список праздников и мероприятий формируется на основе <b>отзывов посетителей</b> и отсортирован по <b>рейтингу</b>, который также формируется на основе отзывов посетителей.\n";
  echo "Таким образом, <b>вверху списка</b> находятся <b>наиболее интересные события</b>.\n";
 }
 echo "<br>Если Вы не нашли в этом списке интересное мероприятие или праздник, о котором хотели бы поделиться впечатлениями - <b>оставьте информацию</b> на нашем форуме, в разделе ";
 echo "<a class=travell5n target= _blank href=\"$forumname/viewforum.php?f=18\">Праздники и события</a>.";
 echo "</p>";
}


echo "<p class=travell0u>Дополнительные сведения о <b>$t_name[7]</b> можно получить в разделах:<br>\n";
echo "<b>Описание <a class=travell5n href=\"/town.php/$town\">$t_name[6]</a></b>";
$noprint = 1;
reset($r_types);
while(list($type_id,$type_name)=each($r_types)) {
 if (($type_id != $et) or ($zzz)) {
  if ($r_excitings[$type_id]) {
   if ($c_excitings[$type_id]) {
    print "<br>\n<b><a class=travell5n href=\"/excite.php/$town/$type_id\">$type_name $t_name[6]</a></b>";
   }
  }
 }
}
print "</p>\n";


// рисуем таблицу

echo "<table border=1 width='95%' cellpadding=8><Tbody class=travell0>";
echo "<tr><TD align=center width=\"175\"><b>$et_name $t_name[6]</b></TD>\n";
echo "<TD align=center width=\"400\"><b>Описание</b></TD>\n";
echo "<TD align=center><b>Рейтинг</b></TD>\n";
echo "<TD align=center><b>Оценка</b></TD>\n";
echo "</TR>\n";


//проходим по записям для вывода
if ($_salp_mode) {
 $sqladd = "";
}
else {
 $sqladd = " wrate desc, ";
}
if (!($zzz)) {
 $sql="select e.name as name, e.descrip as description, e.Nshirota as shirota,
 e.Edolgota as dolgota, e.id as id, e.booking as booking,
 e.votes as votes, e.rate as rate,
 Right( e.name, length( e.name ) - locate( '\"', e.name )) AS loc,
 e.wrate as wrate
 from exciting e where e.town='$town' and e.type in ($et)
 order by $sqladd loc asc";
}
else {
 $sql="select e.name as name, e.descrip as description, e.Nshirota as shirota,
 e.Edolgota as dolgota, e.id as id, e.booking as booking,
 e.votes as votes, e.rate as rate,
 Right( e.name, length( e.name ) - locate( '\"', e.name )) AS loc,
 e.wrate as wrate
 from exciting e, exbytype g where g.town='$town' and g.extyp='$zzz' and e.id=g.exid and e.type in ($et)
 order by $sqladd loc asc";
}

$result = mysql_query($sql, $db);

// echo "\n$sql\n";

if($myrow = mysql_fetch_array($result)) {
	do {
	      	$s_name = $myrow["name"];
//	      echo "\n$s_name\n";
	      	$Nshirota = (float)$myrow["shirota"];
	      	$Edolgota = (float)$myrow["dolgota"];
	      	$s_description = $myrow["description"];
		$s_id = $myrow["id"];

		if ($flagresult) {
			$flagresult=$flagresult . "," . $s_id;
		}
		else {
			$flagresult=$s_id;
		}
		$votes = (float)$myrow["votes"];
		$rate  = (float)$myrow["rate"];
                $wrate   = (float)$myrow["wrate"];
                $wrate   = sprintf("%01.2f",$wrate);
                $ngrv = degreestotext($Nshirota);
                $egrv = degreestotext($Edolgota);
                if (($ngrv) and ($egrv)) {
                 $coord = "N$ngrv, E$egrv";
                 $_nomap = 1;
                }
	        else {
                 $coord = "";
                }
//		echo"<tr><TD class=travel1 align=left><b>";
		echo"<tr><TD align=left><b>";
		$s_rek = getreklama($s_id, 0);
                if($s_rek<>"") {
			echo "<img src=\"/jpg/bigpartner.gif\" width=\"11\" height=\"11\" title=\"Партнер сайта\" alt=\"Партнер сайта\"> ";
		}

	      	echo "<a class=travell5n href=\"/otklik.php/$s_id\">$s_name</a>";
		echo "</b>";
		if ($coord) {
		  echo "<br>$coord\n";
		}
                if ($et == 2) {
                 if (!(empty($myrow["booking"]))) {
                  echo "<br>[доступно бронирование]";
                 }
                }

                echo "</TD><TD>";

		if ($s_rek <> "") {
		       echo "<table border=1 cellpadding=8 width='97%'><tr><td class=travell0><b>Официальная информация: </b><br>$s_rek</td></tr></table>";
		}
		echo $s_description;

		echo "</TD><TD align=center>";
		echo $wrate;

		echo "</TD><TD align=center>";
		if ($votes > 0) {
                 $itog_ball = sprintf ("%01.2f", (($rate/$votes) + 3));
                }
                else {
                 $itog_ball = 0;
                }
		echo $itog_ball;
		echo"</TD></TR>";

   	} while($myrow = mysql_fetch_array($result));


        mysql_free_result($result);
	echo "</tbody></table>\n";
}
else {
	echo "Увы, ничего не нашлось!";
}

  echo "<form name=findex method=post action='/test.php'>\n";
  echo "<input type=hidden name=tid value='$town'>\n";
  echo "<input type=hidden name='extype[]' value='$et'>\n";
  if ($zzz) {
   echo "<input type=hidden name='typex[]' value='$zzz'>\n";
  }
  echo "<input type=hidden name=psubmit value='Найти'>\n";
  echo "<input type=hidden name=adiff value='$defdiff'>\n";
  echo "<div align = center class=travell0><br>Если Вы не нашли, что искали, попробуйте посмотреть <b>$et_name</b> поблизости от <b>$t_name[6]</b>.<br>\n";
  echo "<input type=submit name=exfind value='Поиск по окрестностям (до $defdiff км)'><br>\n";
  echo "</div></form>\n";


include('include/footprint.php');
include('include/footer.php');

?>
