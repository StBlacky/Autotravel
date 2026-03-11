<?php
// forumsess надо звать в самом начале. А то переменные переопределятся.
// Но внутри ничего не echo-ить, а то заголовки и куки покривеют.

include_once('include/config.php');
include_once('include/func.php');
$bootstrap = 1;
include_once('include/head00.php');


$reverse = 0; //направление прохода трассы

if (preg_match("/^\/(\d+)$/",$_pin,$exa)) {
 $trid    = $exa[1];
 }
else {
     if (preg_match("/^\/(\d+)\/(.+)$/",$_pin,$exa)) {
          $trid      = $exa[1];
      }
        else {
          $trid = $_REQUEST["trid"];
       }
}

$trid = (int) mysafestring($trid); //трасса
$trodel = (int) $_REQUEST["trodel"]; //признак удаления
$reverse = (int) $_REQUEST["reverse"]; //в обратную сторону
$kmb = (int) $_REQUEST["kmb"]; // начальный км
$kme = (int) $_REQUEST["kme"]; //конечный км


if ($trid) { //если передана трасса
    //klv сначала удалим, если просили
    if ($trodel) {
      if ($_user_is_admin == '1') {
        $sqldel="update otklikt set ido='-$trodel' where ido='$trodel'";
      }
      else {
        $sqldel="delete from otklikt where ido='$trodel' and nick='$forum_user_name'";
      }
      $result_del = $dbi->query($sqldel);
    }

//отбираем все н/п по нашей трассе и формируем массив
     $points=array();
     $towns=array();
     $kms=array();

     if ($reverse) {
         $sqladd = " desc"; //для сортировки городов на трассе
         $sqladdkm = " kend desc "; //для сортировки откликов
    }
    else {
     $sqladd = " asc";
     $sqladdkm = " kbeg ";
    }
    $farend = 0;
//отбираем города
     $sqlkm="select t.id as tid, t.name as tname, f.cw_far as km from far_tr f, towns t where f.trace='$trid' and t.id=f.town order by km $sqladd";
     $result_km = $dbi->query($sqlkm);

     if($myrow_km= mysqli_fetch_array($result_km)) {
          do {
               $tname= $myrow_km["tname"];
               $km= $myrow_km["km"];
               $tid= $myrow_km["tid"];
               if ($reverse) {
                    if (!($farend)) {
                          $farend = $km;
                     }
                     $km_displ = $farend - $km;
                }  else {
                     $km_displ = $km;
                }
               array_push($points,$km_displ);
               $towns[$km_displ]=$tname;
          } while($myrow_km= mysqli_fetch_array($result_km));
          mysqli_free_result($result_km);
      };


 //отрабатываем ввод
     if ($_POST['submit']) {
         $kbeg = (int) $_REQUEST["kmfirst"];
         $kend = (int) $_REQUEST["kmlast"];
         if ($reverse) {
              $kendr = $farend - $kbeg;
              $kbeg = $farend - $kend;
              $kend = $kendr;

         }
         $kbeg = (int) $_REQUEST["kmfirst"];
         $kend = (int) $_REQUEST["kmlast"];
         $speed = (int) $_REQUEST["speed"];
         $ido = (int) $_REQUEST["ido"];
         $dat = $_REQUEST["dat"];
         $gr = $_REQUEST["gr"];
         $message = $_REQUEST["message"];
         $nick = $_REQUEST["nick"];
         $error = "";
         $tmess = $message;
         $_moderated=0;
         if (bannedlist($tmess)) { // там внутри уже есть http и https
           $_moderated = 2;
         }
         if ($forum_user_banned) {
           $_moderated = 2;
         }
         if (!(preg_match("/[йцукенгшщзхъфывапролджэячсмитьбюЙЦУКЕНГШЩЗХЪФЫВАПРОЛДЖЭЯЧСМИТЬБЮ]{4}/", $tmess))) {
             $error="Пишите по-русски.";
         }
         if ((!$speed)&&(!$gr)){
             $error="Укажите среднюю скорость передвижения.";
         }
         if ( ($message!="") and ($error=="")  ) { // есть message
           $message = mysafehtml2($message);
           $gr = (int) mysafestring($gr);
           $speed = (int) mysafestring($speed);
           $kbeg = (int) mysafestring($kbeg);
           $kend = (int) mysafestring($kend);
           $tim = time();
           $dat = mysafestring($dat);
           $nick = mysafestring($nick);
           $mail = mysafestring($mail);
           if (!($ctext)) {
             /* нету куки, приходил робот */
           } 
           else {
            if ($ido) {
           	$sql_in = "UPDATE otklikt SET datea='$dat', descrip='$message', markv='$speed', markg='$gr', kbeg='$kbeg', kend='$kend', ip='$ipp', time='$tim', cook='$ctext' where otklikt.ido=$ido and otklikt.nick='$nick' and otklikt.idtr=$trid";
                $result_in = $dbi->query($sql_in);
                $localid = $ido;
                if ($localid < 0) {
                    $localid = (0 - $localid);
                }
            } 
            else {
              	$sql_in = "INSERT INTO otklikt (idtr, nick, datea, descrip, markv, markg, kbeg, kend, ip, time, cook) VALUES ('$trid', '$nick','$dat', '$message', '$speed', '$gr', '$kbeg', '$kend', '$ipp', '$tim', '$ctext')";
                $result_in = $dbi->query($sql_in);
                $localid = $dbi->insert_id;
            }
            //echo $sql_in;
           }
           if (($_moderated == 2) || ($_bad_count >0)) {
             $sql_in = "UPDATE otklikt set ido='-$localid' where ido='$localid'";
             $dbi->query($sql_in);
           }
          }

     } // end submit

     sort($points,SORT_NUMERIC);
     if ($kmb) {
     	$kmfirst=$kmb;
     } else {
     	$kmfirst=$points[0];
     }

     if ($kme) {
     	$kmlast=$kme;
     } else {
    	 $kmlast=$points[sizeof($points)-1];
     }
     $townfirst=$towns[$kmfirst];
     $townlast=$towns[$kmlast];


     //заголовок - данные по трассе
     $sql="select tr.name as trname, tr.of_name as of_name, tr.comment as comment, tr.state as state from trace tr where tr.id='$trid'";
     $result = $dbi->query($sql);

     if($myrow = mysqli_fetch_array($result)) {
            $trname = $myrow["trname"];
            $of_name = $myrow["of_name"];
     }


 //отберем все отклики по трассе

  $has_otklik = ""; //id отклика
  $has_s = "";//скорость
  $has_g = "";//покрытие
  $has_c = "";//комментарий
  $sqlprev = "select ts.datea as dat, ((SUBSTRING_INDEX(ts.datea, '.', -1)*12)+SUBSTRING_INDEX(ts.datea,'.',1)) as datess, "
          . "ts.kbeg as kbeg, ts.kend as kend, ts.descrip as comment, t_s.descr as markv, "
          . "t_r.descr as markg, ts.nick as nick, ts.ido as ido, ts.cook as cook, ts.markv as id_s, ts.markg as id_g "
          . "from otklikt as ts, t_road as t_r, t_speed as t_s where ts.ido>0 and ts.idtr = '$trid' and t_s.id = ts.markv and t_r.id = ts.markg "
          . "order by datess desc, $sqladdkm, ts.kend ";
  if ($_bad_count>0) {
   $sqlprev = "select ts.datea as dat, ((SUBSTRING_INDEX(ts.datea, '.', -1)*12)+SUBSTRING_INDEX(ts.datea,'.',1)) as datess, "
          . "ts.kbeg as kbeg, ts.kend as kend, ts.descrip as comment, t_s.descr as markv, "
          . "t_r.descr as markg, ts.nick as nick, ts.ido as ido, ts.cook as cook, ts.markv as id_s, ts.markg as id_g "
          . "from otklikt as ts, t_road as t_r, t_speed as t_s where ts.ido>0 and ts.idtr = '$trid' and t_s.id = ts.markv and t_r.id = ts.markg UNION "
          . "select ts2.datea as dat, ((SUBSTRING_INDEX(ts2.datea, '.', -1)*12)+SUBSTRING_INDEX(ts2.datea,'.',1)) as datess, "
          . "ts2.kbeg as kbeg, ts2.kend as kend, ts2.descrip as comment, t_s2.descr as markv, "
          . "t_r2.descr as markg, ts2.nick as nick, ts2.ido as ido, ts2.cook as cook, ts2.markv as id_s, ts2.markg as id_g "
          . "from otklikt as ts2, t_road as t_r2, t_speed as t_s2 where ts2.ido<0 and ts2.cook='".$ctext."' "
          . "and ts2.idtr = '$trid' and t_s2.id = ts2.markv and t_r2.id = ts2.markg "
          . "order by datess desc, $sqladdkm, kend ";
  }
 //echo "\n\n<!-- $sqlprev !-->\n\n";
 $myotklik=array();
 $result_prev = $dbi->query($sqlprev);
 if($myrow_prev= mysqli_fetch_array($result_prev)) {
    $i = 0;
    $otklikedit="";
    $otklikprev="";
  //  $otklikprev="<BR><form name=stateedit>";
      do {
        $prevdat = $myrow_prev["dat"];
        $prevkbeg = $myrow_prev["kbeg"];
        $prevkend = $myrow_prev["kend"];
       	 if ($reverse) {
         	   $prevkendr = $farend - $prevkbeg;
         	   $prevkbeg = $farend - $prevkend;
         	   $prevkend = $prevkendr;
      	  }

	        $prevmarkg = $myrow_prev["markg"];
	        $prevmarkv = $myrow_prev["markv"];
	        $id_s = $myrow_prev["id_s"];
	        $id_g = $myrow_prev["id_g"];
	        $prevcomment = $myrow_prev["comment"];
	        $nick = $myrow_prev["nick"];
	        $cook = $myrow_prev["cook"];
	        $ido = $myrow_prev["ido"];

	        $otkliktr="<td>".$prevdat."</td><td>".$prevkbeg."</td><td>".$prevkend."</td><td>".$prevmarkg."</td><td>".$prevmarkv."</td><td>".$prevcomment."</td><td>";
	        if ($nick==$forum_user_name){ //если свои отклики
	            $otkliktr=$otkliktr."Вы автор <br><input type=\"button\" class=\"button\" value=\"Удалить\" onclick=\"location.href='/tracestate.php/$trid?trodel=$ido'\" /> <input type=\"button\" class=\"button\" value=\"Изменить\" onclick=\"location.href='/tracestate.php/$trid?reverse=$reverse&kmb=$prevkbeg&kme=$prevkend'\" /></td></tr>\n";
	            $myotklik[$i]=$ido; //массив откликов
	            $i=$i+1;
	            //проверим, нет ли соответствующего отклика
	            if (($prevkbeg==$kmfirst)&&($prevkend==$kmlast)) {
	               $has_otklik = $ido;
	               $has_c = $prevcomment;
	               $has_s = $id_s;
	               $has_g = $id_g;
	               $otkliktr="<tr id=$ido bgcolor=\"#CFFFCF\">".$otkliktr;

	            } else {
	            	if ((($prevkbeg>$kmfirst)&&($prevkbeg<$kmlast))||(($prevkend>$kmfirst)&&($prevkend<$kmlast))) {
	            		$otkliktr="<tr id=$ido bgcolor=\"#CFFFCF\">".$otkliktr;
	            		$otklikedit=1;
	            	} else {$otkliktr="<tr id=$ido>".$otkliktr;}

	            }

        } else {
            if (preg_match("/forum\:(\d+)\:(.+)/",$cook,$s_match)) {
              $otkliktr=$otkliktr."<a href='$nick/memberlist.php?mode=viewprofile&u=".$s_match[1]."'>Участник форума ".$s_match[2]."</a>";
            } else {
               $otkliktr=$otkliktr."$ck";
            }
            if ($_user_is_admin == '1') {
	            $otkliktr=$otkliktr."<input type=\"button\" class=\"button\" value=\"Удалить\" onclick=\"location.href='/tracestate.php/$trid?trodel=$ido'\" />\n";
            }
            $otkliktr=$otkliktr."</td></tr>\n";
        }

    $otklikprev=$otklikprev.$otkliktr;

    } while($myrow_prev= mysqli_fetch_array($result_prev));
     $otklikprev="<table border=1 width=95% cellpadding=8 id=stateedit><Tbody class=travell0 align=center><tr><td width=10% rowspan=2>Состояние на дату</td><td width=20% colspan=2>Участок (км)</td><td width=10% rowspan=2>Проходимость</td><td width=10% rowspan=2>Средняя скорость (км)</td><td rowspan=2>Комментарий</td><td rowspan=2>Автор</td></tr><tr><td>От</td><td>До</td></tr>
     </TBody><Tbody class=travell0>".$otklikprev;

    $otklikprev=$otklikprev."</table>";
    mysqli_free_result($result_prev);
  };

  if ($forum_user_id == 0) {
    $otklikprev=$otklikprev."<br>Вы хотите оставить отклик без регистрации на сайте. В этом случае у Вас не будет возможности в дальнейшем отредактировать свой отклик. Возможность редактирования отзывов предоставляется только зарегистрированным пользователям, выполнившим процедуру входа на сайт.";
  }
  if ($error) {
      $otklikprev=$otklikprev."<br>".$error;
}

//берем из базы варианты состояния покрытия
      $sql_tr="select t_r.id as id, t_r.descr as descr from t_road t_r order by t_r.id";
      $result_tr = $dbi->query($sql_tr);
      if($myrow_tr= mysqli_fetch_array($result_tr)) {
          $str_tr = "<select name=gr onchange=\"ChangeGr(this.value)\">";
          $total=mysqli_num_rows($result_tr);
          if ($has_otklik) {$total=$has_g;}
          do {
               $rid= $myrow_tr["id"];
               $rdescr= $myrow_tr["descr"];
               $total = $total - 1;
               if ($total == 0){
                    $str_tr = $str_tr . "<option VALUE=$rid selected> $rdescr</option>";
               }else{
                     $str_tr = $str_tr . "<option VALUE=$rid> $rdescr</option>";
               }


          } while($myrow_tr= mysqli_fetch_array($result_tr));
          $str_tr = $str_tr . "</select>";
          mysqli_free_result($result_tr);
      }

      //берем из базы варианты скоростного режима
      $sql_ts="select t_s.id as id, t_s.descr as descr from t_speed t_s order by t_s.id";
      $result_ts = $dbi->query($sql_ts);
      if($myrow_ts= mysqli_fetch_array($result_ts)) {
          $str_ts = "<select name=speed>";
          do {
              $sid= $myrow_ts["id"];
              $sdescr= $myrow_ts["descr"];
              if (($has_otklik)&&($sid==$has_s)) {
              		$str_ts = $str_ts . "<option VALUE=$sid selected> $sdescr</option>";
              } else {$str_ts = $str_ts . "<option VALUE=$sid> $sdescr</option>";}
          } while($myrow_ts= mysqli_fetch_array($result_ts));
          $str_ts = $str_ts . "</select>";
          mysqli_free_result($result_ts);
      }



// отображаем страницу

 $title = "Состояние дороги $of_name $trname";
 $metatags = "дорога трасса проехать пробки скорость покрытие асфальт бетон грейдер";
 $metadesc = "$title - средняя скорость, состояние покрытия, проходимость, пробки и другие затруднения на трассе.";
 include('include/header_bs.php');


 echo "<div class=\"container-fluid\"><h1 class=travel11>$title</h1>";
 echo "<form><p class=\"t14 tb tc\">Направление от н/п $townfirst до н/п $townlast  <input type=\"button\" class=\"button\" value=\"Изменить направление\" onclick=\"location.href='/tracestate.php/$trid?reverse=";
 if ($reverse==1){
     echo "0'\" /></form>";
 } else     {
     echo "1'\" /></form>";
 }
 echo "<p class=\"t14 tb tc\">Полный список <a href=/trace.php/>дорог России</a><br>\n";
 echo "<a href=/trace.php/$trid>$of_name $trname</a> - подробное описание дороги, легенда трассы.</p>\n";

 echo $otklikprev;


 ?>
 <script type="text/javascript">
 // массив города+километры
 var arrtowns = new Array();
 var arrpoints = new Array();
 var otkliks = new Array();
 <?php
 for ($kmi=0;$kmi<sizeof($points);$kmi=$kmi+1) {
 	$point=$points[$kmi];
  	$town=$towns[$point];
 	echo "arrtowns[$point]=\"$town\";\n";
 	echo "arrpoints[$kmi]=$point;\n";
  }
 for ($i=0;$i<sizeof($myotklik);$i=$i+1) {
  	echo "otkliks[$i]=$myotklik[$i];\n";
 }
 ?>
 function SubmitForm() {// проверка введенных данных
    if (document.tracestate.message.value.replace(/\s+/g, '').length) {//комментарий есть
      if (document.tracestate.speed.value > 0) {// скорость поставили
  	  	return true;
  	  } else { //скорость не установлена
    	if (document.tracestate.gr.value > 1) { //но должна была быть
    		alert('Вы не указали среднюю скорость.');
    		document.tracestate.speed.focus();
    		return false;
    	} else { //и не надо
    		return true;
    	}
   	 }
    } else { // нет коммента
        alert('Обязательно прокомментируйте свою оценку.');
        document.tracestate.message.focus();
        return false;
    }


 }
 function ChangeGr(first) {
 	if (first == 1) {
 		document.tracestate.speed.value = 0;
 		document.tracestate.speed.style.display='none';
 		document.getElementById('ss').style.display='none';
   } else {
   		document.getElementById('ss').style.display='';
   		document.tracestate.speed.style.display='';
   }
 }

  function ChangeKm() {//проверка откликов на пересечение
 	var kmbeg = document.tracestate.kmfirst.value;
 	var kmend = document.tracestate.kmlast.value;
 	var mykmbeg = 0;
 	var mykmend = 0;
 	var showotklik = 0;
    var row = '';
    for (var key in otkliks) {
		row = document.getElementById(otkliks[key]);
 		mykmbeg=row.cells[1].innerText;
 		mykmend=row.cells[2].innerText;
 		//alert (kmbeg + " " +mykmbeg +" " +kmend + " " + mykmend);
 	    if (((+kmbeg < +mykmbeg)&&(+kmend > +mykmbeg))||((+kmend > mykmend)&&(+kmbeg<+mykmend))||((+kmbeg <= +mykmbeg)&&(+mykmend <= +kmend))) {//попали в интервал
 	       showotklik =1;
 	       if ((+kmbeg == +mykmbeg)&&(+kmend == +mykmend)) {
 	   			//document.getElementsByName("rid").value=row.cells[3].innerText;
 	   			//alert(row.cells[3].innerText);
 	   			//document.getElementsByName("sid").value=row.cells[4].innerText;
 	   			document.getElementsByName("message").value=row.cells[5].innerText;
 	   			document.getElementsByName("ido").value=otkliks[key];
 	   			showotklik=0;
 	       }
           row.style.background = "#CFFFCF";
 	  	 } else {
           row.style.background = "#EEE9D5";
 	     }
 	  	}
 	     if (showotklik) { //alert("1");
 	 	  	document.getElementById("totklik").style.display='none';
            document.getElementById("tprevtext").style.display='';
 	   	} else {//alert("0");
 	   		document.getElementById("totklik").style.display='';
            document.getElementById("tprevtext").style.display='none';
        }

 };

 function ChangeTownFirst(first) { //начальный город
  document.tracestate.kmfirst.value = first; // присваиваем километраж
  var endSel = document.tracestate.tlast; // список выбора с хвоста
  var endVal = endSel.options[endSel.selectedIndex].value; // выбранное значение с хвоста
  var point = 0;
  var town = 0;
  endSel.options.length=0; // убиваем список
  for (i=0; i<arrpoints.length; i++){
  		point = arrpoints[i];
  		town = arrtowns[point];
  		if (point > first) {
     // вставляем новое значение в список
			 endSel.options[endSel.options.length]=new Option(town + ": " + point + " км",point);
			 if (point == endVal) {
                endSel.options[endSel.options.length - 1].selected = 1;
             }
        }
   }
  document.tracestate.kmlast.value = document.tracestate.tlast.value;
  ChangeKm();
 };

 function ChangeTownLast(entered) {
    document.tracestate.kmlast.value = entered;
    ChangeKm();
 };

 function ChangeKmfirst(entered) {//начальный км
	 if (+entered.value != entered.value || entered.value.indexOf(".") != -1) {
	    entered.value = document.tracestate.tbeg.value;}

	 if (+entered.value < +document.tracestate.tbeg.value) {
	    entered.value = document.tracestate.tbeg.value;}

	 if (+entered.value >= +document.tracestate.kmlast.value) {
	   entered.value = +document.tracestate.kmlast.value - 1;}

	ChangeKm();

 };

 function ChangeKmLast(entered) {
 if (+entered.value != entered.value || entered.value.indexOf(".") != -1) {
    entered.value = document.tracestate.tlast.value;}

 if (+entered.value > +document.tracestate.tlast.value) {
   entered.value = document.tracestate.tlast.value;}

 if (+entered.value <= +document.tracestate.kmfirst.value) {
   entered.value = +document.tracestate.kmfirst.value + 1;}

   ChangeKm();
 };

 </script>
 <?php
/* ввод информации по трассе */

echo "<BR><form name=tracestate action=/tracestate.php/$trid method=post onSubmit=\"return SubmitForm();\">\n";


echo "<table width=60%><Tbody class=travell0>";

echo "<tr><td width=5%></td>"; //столбец 1 буферный
echo "<td width=20%><b>На дату </b><SELECT name=dat>"; //столбец 2 дата
 $cm = (int) date("m",time());//текущий месяц и год
 $cy = date("Y",time());

 for ($cmi=$cm;$cmi>0;$cmi=$cmi-1) {
   echo "<option value=\"$cmi.$cy\">$cmi.$cy</option>";
 }
 $cy = $cy-1;
 for ($cmi=12;$cmi>11-$cm;$cmi=$cmi-1) {
   echo "<option value=\"$cmi.$cy\">$cmi.$cy</option>";
 }
 $cmi=12-$cm;
echo "<option value=\"$cmi.$cy\">ранее</option></select></td>";

echo "<td><b> Участок от </b>";// столбец 3 от
echo "<SELECT name=tbeg onChange=\"ChangeTownFirst(this.value)\">";
 for ($kmi=0;$kmi<sizeof($points)-1;$kmi=$kmi+1) {
  $tpoints=$points[$kmi];
  echo "<option value=\"$tpoints\">$towns[$tpoints]: $tpoints км</option>";
 }
 echo "</select></td>\n";

 echo "<td align=center><b> до </b>"; // стобец 4 до
 echo "<SELECT name=tlast onChange=\"ChangeTownLast(this.value)\">";
 for ($kmi=1;$kmi<sizeof($points)-1;$kmi=$kmi+1) {
  $tpoints=$points[$kmi];
  echo "<option value=\"$tpoints\">$towns[$tpoints]: $tpoints км</option>";
 }
  $tpoints=$points[$kmi];
  echo "<option selected value=\"$tpoints\">$towns[$tpoints]: $tpoints км</option>";
 echo "</select></td></tr>";
 echo "<tr><td></td><td></td>"; // 2 строка 1 и 2 столбец
 echo "<td colspan=2 align=center>"; //на 3  и 4 столбец
 echo "Выбор участка предусмотрен исключительно для \"привязки к местности\", чтобы наглядно продемонстрировать, между какими населенными пунктами пролегает описываемый участок дороги.
Также является необязательным уточнение границ участка.<br><br></td></tr>";

 echo "<tr><td></td><td></td>"; //  3 строка 1 и 2 столбец
 echo "<td align=center>с "; // 3 столбец
 echo "<INPUT name=kmfirst type=text size=5 value=$kmfirst onChange=\"ChangeKmfirst(this)\"> км </td>";
 echo "<td align=center> по "; // 4 столбец
 echo "<INPUT name=kmlast type=text size=5 value=$kmlast onChange=\"ChangeKmLast(this)\"> км</td></tr>";
 echo "<tr><td></td><td></td>"; // 4 строка 1 и 2 столбец
 echo "<td align=center colspan=2>";// растягиваем на 3 и 4 столбец
 echo "А здесь укажите, при необходимости, более точно границы участка внутри выбранного выше интервала.</td></tr>";
 echo "</table>";

 if (($otklikedit)&&(!$has_otklik)) {
	echo "<table border=1 width=95% cellpadding=8 id=tprevtext>";
 } else {
         echo "<table border=1 width=95% cellpadding=8 id=tprevtext style=\"display:none\">";
 }

 echo "<Tbody class=travell0 align=center><tr><td colspan=7>В указанном интервале у Вас уже есть описание(я). Вы можете отредактировать или удалить их. Для ввода описания по новому интервалу укажите другие границы. Если в указанные границы не попадет ни одного Вашего описания, появится форма ввода.</td></tr></table>";

 if (($otklikedit)&&(!$has_otklik)) {
    echo "<TABLE BORDER=0 cellspacing=0 CELLPADDING=0 class=travell0 width=87% id=totklik style=\"display:none\"> ";
 } else {
     echo "<TABLE BORDER=0 cellspacing=0 CELLPADDING=0 class=travell0 width=87% id=totklik>";
}


// вторая таблица, ее иногда будем прятать

echo "<tr><td width=5%></td>"; //столбец 1 буферный
echo "<td allign=center colspan=2>"; //растянем на 2 и 3 столбец
echo "<br><br>Просим оставлять только личное мнение или исправление информации.<br>\n";
echo "Вопросы, комментарии к сообщениям других посетителей - пожалуйста, на <a target=_blank href=$forumname/>форуме</a>.<br><br>";
 if ($forum_user_id == 0) {
 echo "Если Вы хотите, чтобы отзыв был подписан \"участником форума\", надо <a class=travell5m target=_blank href='/sitemgr.php'>войти на сайт</a>.";}
echo "</td></tr>";
echo "<tr><td width=5% rowspan=4></td>"; //2 строка столбец 1 буферный на 4 строки
echo "<td allign=center rowspan=4><TEXTAREA name=message rows=10 cols=55 wrap=none >"; // столбец 2 на 4 строки
if ($has_otklik) {
   echo $has_c;}
echo "</TEXTAREA></TD>";
echo "<td align=left><b>Проходимость: </b>$str_tr</td></tr>"; //столбец 3
echo "<tr><td align=left id=ss><b>Средняя скорость: </b>$str_ts</td></tr>";//3 строка 3 столбец
echo "<tr><td align=left>"; //4 строка 3 столбец
echo "<b>Имя: </b><INPUT name=nick type=text size=40";
if ($forum_user_id != 0) {
 echo " value='$forum_user_name'";
}
echo "></td></tr>";
echo "<tr><td><br><INPUT name=submit type=submit value=\""; //5 строка 3 столбец
  if ($has_otklik) {
   echo "Изменить свой отзыв";
  }
  else {
   echo "Сохранить на сайте";
  }
echo "\" >";
echo "</td></tr></table>\n";
echo "<input TYPE=hidden VALUE=$has_otklik NAME=ido>";
echo "</FORM>";


}
echo "</div>";
include('include/footer_bs.php');
?>



