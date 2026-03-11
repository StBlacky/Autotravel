<?php
// forumsess надо звать в самом начале. А то переменные переопределятся.
// Но внутри ничего не echo-ить, а то заголовки и куки покривеют.
include_once('include/func.php');
include_once('include/head00.php');
// теперь всё как обычно

if (preg_match("/^\/(\d+)$/",$_pin,$exa)) { $ex = $exa[1];}
else { $ex = $_REQUEST["ex"]; $ex = (int) mysafestring($ex);}

$ex_descr = getextext($ex);

if (ifmoderated($ex)>0) {
   if ($forum_user_weight < 1) {  $_moderated = 1; }
}

// автоловилка писателей в otklikm
if ($_bad_count>1) { $_moderated = 2;}
// МГТС
//if ( isIPIn($ipp,"109.252.0.0","255.255.0.0")) {if ($forum_user_weight < 0.1) {$_moderated=2; }}

//выбираем информацию про объект
$coord="";
$sqlex = "select e.name as exciting, e.descrip as description, e.booking as booking,
         e.town as town, e.type as type, e.Nshirota as shirota, e.exact as exact,
         e.Edolgota as dolgota, e.wrate as ratingvalue, e.votes as ratingcount, type.name as typename
         from exciting e, type where e.id = '$ex' and e.type = type.id";
$resultex = $dbi->query($sqlex);

$s_town = "";
$t_coord = "";

if($myrowex = mysqli_fetch_array($resultex)) {
     $s_ex = $myrowex["exciting"]; //название объекта
     $Nshirota = (float)$myrowex["shirota"];
     $Edolgota = (float)$myrowex["dolgota"];
     $exact = (int)$myrowex["exact"];
     $s_descr = $myrowex["description"]; //описание объекта
     $s_booking = $myrowex["booking"];
     $typename = $myrowex["typename"];
     $town = $myrowex["town"];
     $ratingvalue = $myrowex["ratingvalue"];
     $ratingcount = $myrowex["ratingcount"];
     $et = $myrowex["type"]; //категория объекта 2 - гостиницы, 3 - еда
     $ngrv = degreestotext($Nshirota);
     $egrv = degreestotext($Edolgota);
     if (($ngrv) and ($egrv)) {
        $nlat = $Nshirota; 
        $nlng = $Edolgota;
        if ($egrv<0) {$coord = "N$ngrv, W".str_replace("-","",$egrv);
        } else {$coord = "N$ngrv, E$egrv"; }
        $metacoord = "<meta name=\"geo.position\" content=\"$Nshirota; $Edolgota\">\n<meta name=\"ICBM\" content=\"$Nshirota, $Edolgota\">";
     }
     $btype="ex";
     if ($et==2) {$btype="ho";}
     if ($et==3) {$btype="op";}
     $s_town = gettownnamearr($town)[0]; //название города
     $t_namefrom = gettownnamearr($town)[6]; // родительный падеж
     $s_areaname = gettownnamearr($town)[2]; //название области
     $s_area = gettownnamearr($town)[8]; //id области
     if  ($s_area>0) { //для крошек 
          $hreffirst='/areas.php';
          $namefirst='Области и регионы России';
     } else {
          $hreffirst='/countries.php';
          $namefirst='Зарубежные страны'; 
     }
  } else {
     $s_ex="Нет названия";
     $s_descr="Нет описания";
     $typename = "";
     $town=0;
     $et=1;
  }

//зачем-то ближайшую дату новости по объекту
$sqlnd = "select max(date) as news_date from news where exciting='e$ex'";
$resultnd = $dbi->query($sqlnd);
if($myrownd = mysqli_fetch_array($result2)) {$news_date = $myrownd["news_date"];}
else { $news_date = "";}

//блок партнера
$s_rek = getreklama($ex,1);
$title = $s_ex; //сохранили название объекта без партнера
if ($s_rek <> "") {
   $s_ex = "<img src='/jpg/bigpartner.gif' width=11 height=11 title=\"Партнер сайта\" alt=\"Партнер сайта\">&nbsp;&nbsp;".$s_ex;
}


$searchurl="/test.php?tid=$town&amp;adiff=$defdiff&amp;thistid=1&amp;";

$t_r = split_descr($s_descr); //описание объекта
//  htmlentities ( $string , ENT_HTML401 , "UTF-8" , 0 )
$metafbtitle = htmlspecialchars(strip_tags(htmlspecialchars_decode($s_ex0)) . " (" . $t_name ." - " . $t_area .")",ENT_QUOTES |ENT_HTML401 , "UTF-8" , 0 );

$metafbdescription = htmlspecialchars(strip_tags(htmlspecialchars_decode($t_r[0])),ENT_QUOTES|ENT_HTML401 , "UTF-8" , 0 );

//собираем мета-поля
$metatags = "$s_town $title";
$metatags = str_replace('"',' ',$metatags);
$metadesc = $title;


$photoimg = getfotoex($ex,3,"",1);
$photocount = $photoimg["display"] + $photoimg["hidden"]; //countfotoexc($ex);
if ($photocount) {
    $metafbimage = $photoimg["foto1"];
    $metatags = $metatags . " Фотографии";
    $metadesc = $metadesc . ", фотографии";
}

if ($metacoord) {$metadesc = $metadesc . ", карта окрестностей";}

$metadesc = $metadesc . ", отзывы посетителей";


$metadesc = str_replace("\n",' ',$metadesc);
$metadesc = str_replace("\r",' ',$metadesc);
$metadesc = str_replace('"',' ',strip_tags($metadesc));

$bootstrap = 1;
$colorbox  = 1;
include('include/header_bs.php'); //должно стоять после всех мета-тегов


// выбираем режим карты
if ($_gmap_mode==9) { // не показывать
   $dispgmap = "";}
   elseif($_gmap_mode==1) { // osm
      $dispgmap = 1; }
      elseif($_gmap_mode==2) { // yandex
         $dispgmap = 2;}
         else { // google
            $dispgmap = 3;}
            

//формируем микроразметку - по организации 
$organization = "<div itemscope itemtype=\"http://schema.org/Place\">";
if ($t_shirota) $geomicro = "<div itemprop=\"geo\" itemscope itemtype=\"http://schema.org/GeoCoordinates\">"
        . "<meta itemprop=\"latitude\" content=\"".$t_shirota."\"/><meta itemprop=\"longitude\" content=\"".$t_dolgota."\"/></div>";
$organization .= $geomicro;   
if (($t_r[4]) and ($t_r[3])) {
        if ($et == 2) {$organization = "<div itemscope itemtype=\"http://schema.org/Hotel\">";}
        if ($et == 3) {$organization = "<div itemscope itemtype=\"http://schema.org/Restaurant\">";}
}
$organization .= "<span itemprop=\"name\" content=\"$s_ex0\"></span>";
if ($photoimg["itempropfotos"]) {$organization .= "<span " . $photoimg["itempropfotos"] ."></span>";} 
else {$organization .= "<span itemprop=\"image\" content=\"http://forum.autotravel.ru/templates/subSilver/images/chudick.gif\"></span>";}

$aggregaterating = "<div class=travell5 itemprop=\"aggregateRating\" itemscope itemtype=\"http://schema.org/AggregateRating\"> ";
if ($ratingvalue <1) {$ratingvalue=1;}
$aggregaterating .= "<span itemprop=\"ratingValue\"  content=\"$ratingvalue\"></span>";
$aggregaterating .= "<span itemprop=\"ratingCount\" content=\"$ratingcount\"></span>";
$bestRating = max($ratingcount*5,round($ratingvalue)+1);
$aggregaterating .= "<span itemprop=\"bestRating\" content=\"$bestRating\"></span>";
$aggregaterating .= "</div></div>"; //второй див для охватывающего



//начало вывода
echo "<div class='container-fluid' style='width:100%;'>";
echo "$organization <h1>$s_ex</h1>"; 

//соберем крошки
echo '<ol class="breadcrumb">';
echo '<li><a href="' . $hreffirst . '">' . $namefirst . '</a></li>';
echo '<li><a href="/area.php/' . $s_area . '">' . $s_areaname . '</a></li>';
if ($t_hide == 0) { //это для открытого города
   echo '<li><a href="/town.php/' . $town . '">' . $s_town . '</a></li>';
   echo '<li><a href="/excite.php/' . $town . '/' . $et. '">Все ' . $typename . ' ' . $t_namefrom . '</a></li>';
 
} 
echo '</ol>';

//$t_descrip = gettowntext($town); это для скрытого города - пусть побудет, пока не знаю

if ($s_descr)  {
   ?>
    <div class="row">
      <div class="col-md-12">
        <div class="col-md-6 col-xs-12 pull-right">
          <div class="panel">
            <div class="panel-heading">
              <button type="button" class="btn btn-xs btn-info pull-right" data-toggle="collapse" data-target="#collapsemap">
                <span class="glyphicon glyphicon-chevron-up"></span>
              </button>
              <h3 class="panel-title">Карта <?php echo $t_namefrom; ?> и окрестностей</h3>
            </div>
            <div id="collapsemap" class="panel-collapse collapse in tocollapse">
              <div class="panel-body">
                <?php echo $mapiframe; ?>
              </div>  
            </div>
          </div>  
        </div>
        <p class="travell0"> <?php echo $s_descr; ?></p>
      </div>
    </div>
    <?php
    echo "<div class=\"townfotowide\">". $fotoframe ."</div>"; //фото
 } else { //нет описания
   ?>
   <div class="row">
      <div class="col-md-6">
        <div class="panel">
          <div class="panel-heading">
            <button type="button" class="btn btn-xs btn-info pull-right" data-toggle="collapse" data-target="#collapsefoto">
              <span class="chevron">Спрятать</span>
              <span class="glyphicon glyphicon-chevron-up"></span>
            </button>
            <h3 class="panel-title">Фотографии <?php echo $t_namefrom; ?></h3>
          </div>
          <div id="collapsefoto" class="panel-collapse collapse in tocollapse">
            <div class="panel-body">
              <?php echo $fotoframe; ?>
            </div>  
          </div>
        </div>  
      </div>
      <div class="col-md-6"> 
        <div class="panel">
          <div class="panel-heading">
            <button type="button" class="btn btn-xs btn-info pull-right" data-toggle="collapse" data-target="#collapsemap">
              <span class="chevron">Спрятать</span>
              <span class="glyphicon glyphicon-chevron-up"></span>
            </button>
            <h3 class="panel-title">Карта <?php echo $t_namefrom; ?> и окрестностей</h3>
          </div>
          <div id="collapsemap" class="panel-collapse collapse in tocollapse">
            <div class="panel-body">
              <?php echo $mapiframe; ?>
            </div>  
          </div>
        </div>  
      </div>
    </div>
<?php 
}
if ($coord) {
     echo "<div align=center><font class=\"t13 tc\">$coord";
     if (!($exact)) {
      if (($forum_user_rank) || ($forum_user_level)) {
       echo "[<a href=#"."$ex onclick=AddCoord($ex)>Изменить&nbsp;GPS&nbsp;координаты</a>]";
      }
     }
     #if ($forum_user_id < 4) {
     # echo "[<a href=\"igomyway://A$Nshirota;$Edolgota\">$s_ex</a>]";
     #}
     echo "</font>";
     echo "</div>\n";
}
else {
     echo "<div align=center><font class=\"t13 tc\">\n";
     echo " [<a href=#"."$ex onclick=AddCoord($ex)>Указать&nbsp;GPS&nbsp;координаты</a>]";
     echo "</font></div>\n";
}

if ($coord) {
  if ($dispgmap) {  
     echo "<div id='gmapdiv' align=center><font class=\"t13 tc\">\n";
     echo "<a class=travell5r onClick=\"";
     echo "initmapv".$dispgmap;
     echo "('/kml.php/e/$ex.kml',this,$nlat,$nlng);\">$s_ex0 на карте: посмотреть <img src='/jpg/plus.gif' border=0 alt='>'></a>\n";
     echo "</font></div>\n";
     echo "<div align=center id='gmapv3' style=\"width:100%; height:400px; visibility:hidden; display:none;\">&nbsp;</div>\n";
  }
}
else {
     echo "<p class=\"t13 tc\">\n";
     if ($t_hide == 0) {
      echo "Посмотреть <a class=travell5r target=_blank href=\"/map.php/t/$town" . ".htm\">окрестности $t_namefrom на карте Google MAP</a>\n";
     }
     else {
      echo "Посмотреть <a class=travell5r target=_blank href=\"/map.php/t/$town" . ".htm\">окрестности объекта $s_ex0 на карте Google MAP</a>\n";
     }
     echo "</p>\n";
}

if ($s_rek <> "") {
 echo "<div width=95% class=\"t12 tm10 ttb ttr\"><p class=\"t12 tp10\"><b>Официальная информация: </b><br>$s_rek</p></div>";
}

$addr_print = display_t_r($t_r, "t12 tl travell0t",1);        

echo "<center><hr width='80%'></center>$addr_print $aggregaterating\n"; 
if ($ex_descr) {
 echo "<div width=95% class=\"t12 tm10 ttb ttr\"><p class=\"t12 tp10\">$ex_descr</p></div>"; //вывод авторского описания
}
else {
 echo "<center><hr width='80%'></center>\n";
}

if (($forum_user_id>1)) {
 echo "<div id=basketadd$ex  class=travell0><a class=travell5r onclick='BasketAdd($ex,$forum_user_basket,\"basketadd$ex\")'>Добавить в $forum_user_basket_name</a></div>\n";
}
if ($photocount) {
 if (!($s_rek)) {
  echo "<div align=center>".$photoimg["foto"];
  // тут надо посмотреть что там внутри. если фото вообще нет (гостиница), то убрать "все". Если скрытых фото нет, то поубирать
  if ($photoimg["hidden"]>0) {
   echo "<br><font class=\"t13 tc\"><a class=travell5r onclick='ShowAllImg(); $(this).hide();'>Открыть ";
   if ($photoimg["display"]>0) {
     echo "все ";  
   }
   echo "фотографии ($photocount фото)</a></font>\n";
  }
  echo "</div><br>";
 }
 echo "<p class=travell0>Посмотреть фотоархив ($photocount фотографий) в разделе <a class=travell5r href='/$_fotoalbum/ex/$ex'>$s_ex0: фотографии</a></p>\n";
 
 $ownfoto = countownfotoexc($ex);
 if ($ownfoto > 0) {
    echo "<p class=travell5><a class=travell5r target=_blank href='/fotoman.php?utown=$town'>Собственных фотографий $s_ex0</a>: $ownfoto</p>\n";
 }
}
# добавление фотографии из формы отклика
#if (($photocount < 9)) {
 if (($forum_user_level>0) or ($forum_user_rank>0)) { # сдублировано в fotoman.php
  if ($forum_user_fotos > 0) {
   echo "<form name=foto  method=post  enctype=\"multipart/form-data\"  action=\"/fotoman.php\">\n";
   echo "<input type=hidden name=utown value=$town>\n";
   echo "<input type=hidden name=ret value=\"otklik\">\n";
   if ($ownfoto < $MAXFOTOS) {
    echo "<input type=hidden name=mode value=1>\n";
   }
   else {
    echo "<input type=hidden name=mode value=0>\n";
   }
   echo "<input type=hidden name=exutown value=$ex>\n";
   echo "<p class=travell0>Фотографию:<input type=file name=userfile size=40>\n";
   echo "<INPUT name=action type=submit value='Загрузить на сайт >>'></p></form>\n";
  }
  else {
   echo "<p class=travell0>После ознакомления с <a href='/phalbum.php'>условиями размещения фотографий</a> Вы сможете публиковать свои фотографии на сайте.</p>";
  }
 }
#}

if ($news_date <> "") {
   echo "<p class=travell0>Посмотреть <a class=travell5r href='/news.php?ex=$ex'>архив новостей по объекту $s_ex0</a></p>";
}
if ($t_hide != 0) {
 $sql_tr = "SELECT DISTINCT t.id AS trace_id, t.name AS trace, t.of_name as of_name FROM trace t, far_tr ft WHERE ft.town = '$town' AND t.id = ft.trace order by t.of_name";
 $result_tr = mysql_query($sql_tr, $db);

 if($myrow_tr = mysql_fetch_array($result_tr)) {
  if (mysql_num_rows($result_tr) > 2) {
   echo "<p class=travell0><a target=_blank href=\"/search.php?tt=$town\">Трассы, проходящие через $t_nameto</a> : <input type=checkbox name=divxxtr onclick='ChUnVis(this,\"divxtr\")'>Показать в этом же окне</p>";
   echo "<table id=divxtr style='visibility:hidden;display:none;' border=0 width='95%' cellpadding=0><tbody class=travell0>";
  }
  else {
   echo "<p class=travell0>Трассы, проходящие рядом с объектом $s_ex0:</p>\n";
   echo "<table id=divxtr border=0 width='95%' cellpadding=0><tbody class=travell0>";
  }
  do {
   $s_trace = $myrow_tr["trace"];
   $s_traceid = $myrow_tr["trace_id"];
   $s_on = $myrow_tr["of_name"];
   echo"<TR><td width=50>&nbsp;</td><TD>\n";
   echo "<a class=travell5r href=\"/trace.php/$s_traceid\">$s_on $s_trace </a>";
   echo"</TD></TR>\n";
  } while($myrow_tr = mysql_fetch_array($result_tr));
  echo "</table>\n";
 }
 mysql_free_result($result_tr);
}

$_valck = md5("dfRt56Ergh67@".$ipp);

if ($_POST['submit']) {
 $message = $_REQUEST["message"];
 $mark = $_REQUEST["mark"];
 $mail = $_REQUEST["mail"];
 $name = $_REQUEST["name"];
 $valck = $_REQUEST["valck"];
 $error = "";
 $tmess = $message;

 if (bannedlist($tmess)) {
   $_moderated = 2;
 }

 if (
     (preg_match("/href\=/i",$tmess)) or
     (preg_match("/https\:/i",$tmess)) or
     (preg_match("/https\:/i",$name)) or
     (preg_match("/http\:/i",$tmess)) or
     (preg_match("/http\:/i",$name)) or
     (preg_match("/http\:/i",$mail)) ) {
  $error="Пожалуйста, изложите своё мнение о посещаемом месте. Напоминаем, что здесь не допускается указание ссылок на сайты и ресурсы Интернет. Исправьте.";
 }

 if (!(preg_match("/[йцукенгшщзхъфывапролджэячсмитьбюЙЦУКЕНГШЩЗХЪФЫВАПРОЛДЖЭЯЧСМИТЬБЮ]{4}/", $tmess))) {
  $error="Пожалуйста, изложите своё мнение о посещаемом месте.";
 }


 if (($message!="") and ($mark!="") and ($error=="")  ) {
   $message = mysafehtml2($message);
   $mark = (int) mysafestring($mark);
   $tim = time();
   $name = mysafestring($name);
   $mail = mysafestring($mail);
   if (!($ctext)) {
     /* нету куки, приходил робот */
   }
   elseif ($ex) {
     $attable = "otklik";
     if ($_moderated) {
         $attable = "otklikm";
     }
     $sql_r2 = "select descrip from $attable where exciting = $ex and cook = '$ctext'";
     $result_r2 = mysql_query($sql_r2, $db);
     if ($myrow_r2 = mysql_fetch_array($result_r2)) {
         $descr_old = $myrow["descrip"];
         $sql_in = "update $attable set descrip = '$message', nick='$name',
                    mail= '$mail', mark= '$mark', ip = '$ipp', time = '$tim', archived = 0
                    where exciting=$ex and cook = '$ctext'";
         $sql_h = "insert into history (tblname,cook,ip,id,field,valueold,valuenew) values ('otklik','$ctext','$ipp',$ex,'descrip','$descr_old','$message')";
         mysql_query($sql_h,$db);
     }
     else {
      $sql_in = "INSERT INTO $attable (exciting, nick, mail, descrip, mark, ip, time, archived, cook) "
              . "VALUES ('$ex', '$name','$mail', '$message', '$mark', '$ipp', '$tim', 0, '$ctext')";
     }
     $result_in = mysql_query($sql_in, $db);
     //увы, при обновлении отклика прилетают артефакты
     //if (!($_moderated)) {
     // $sql_in = "update exciting set rate = (rate + '$mark' - 3), votes = (votes + 1) where id = '$ex'";
     // $result_in = mysql_query($sql_in, $db);
     //}
//     mysql_free_result($result_in);
   }
   if ($_moderated == 1) {
    $error="Отзыв направлен на модерирование. Не нужно его дублировать. Спасибо.";
   }
   else {
    $error="";
   }
//   mysql_free_result($result_r);
 }

 else {
  if ($error=="") {
   $error="Вы не все рассказали или не поставили оценку. Оценки без комментария не бывает, как и комментария без оценки. Исправьте.";
  }
 }


}
else {
// это был просмотр: надо обновить статистику
 $dtarr = getdate(time());
 $sttime = mktime(0,0,0,$dtarr["mon"],1,$dtarr["year"]);
# $sql_in = "update exciting set st_cm=st_cm+1, st_cy=st_cy+1 where id='$ex'";
# $result_in = mysql_query($sql_in, $db);
# mysql_free_result($result_in);
 $sql_in = "insert into stats_ex (ex, dt, count) values($ex,$sttime,1) ON DUPLICATE KEY UPDATE count=count+1";
 $result_in = mysql_query($sql_in, $db);
# mysql_free_result($result_in);
}
if ($_user_is_admin == '1') {
  echo "<div align=center><form name=editex action=\"/sitemgr.php\">\n";
  echo "<input type=hidden name=admt value=\"excited\">";
  echo "<input type=hidden name=excid value=\"$ex\">";
  echo "<input type=hidden name=submitexn value=\"1\">";
  echo "<input type=submit value=\"Отредактировать объект\"></form></div>\n";

}
if($s_booking) {
  #echo "<p class=travell5><a class=travell5n target=_blank href=\"$s_booking\">Забронировать онлайн на сайте&nbsp;";
  #if ($_text_mode != 1) {
  # echo "<img border=0 src=/jpg/booking_small.gif width=89 height=10 alt=\"Booking.com\" title=\"Booking.com\"></a></p>\n";
  #}
  #else {
  # echo "Booking.com</a></p>\n";
  #}
  echo "<!--noindex--><div align=center><form name=reservation action=\"$s_booking\" target=_blank>\n";
  echo "<input type=hidden name=aid value=337822>";
  echo "<input type=submit value=\"Забронировать онлайн на сайте Booking.COM\"></form></div><!--/noindex-->\n";

}


$otkl_rep = getotklik($ex,"","");
if (($_excc_mode) or ($_exct_mode)) {
  echo "<p class=\"t13 tc\">Не забудьте: у Вас включен фильтр отзывов";
  if ($_excc_mode > 0) {
   echo " [только $_excc_mode отзывов]";
  }
  if ($_excc_mode < 0) {
   echo " [отключены отзывы]";
  }
  else {
   if ($_exct_mode) {
    echo " [только участников форума]";
   }
  }
  echo ". Отключается в меню <a class=travell5r href=/sitemgr.php>настроек сайта</a></p>";
}
if ($otkl_rep) {
    //отклик идет таблицей сразу, обрамим его еще в одну таблицу - и отклик, и форму ввода
  echo "<table width=97%><tr>"; 
  echo "<td>$otkl_rep"; //собственно, отклик - там надо микроразметку ревью делать
   
  ?> 
  
 <!-- обработка оценки отзыва -->
 
<script type="text/javascript" >
   function buttonclick(sc,s_oid) {
            var result = $.post("/upscore.php", { type: "excite", score: sc, idtype: s_oid});
            result.done(function(scoredata) {
              jQuery.globalEval(scoredata);
            });
   };
</script>
<?php
//конец обработки оценки отзыва
}
else {
 echo "<p class=\"t13 tc\">Пока нет ни одного отзыва. Значит... пора посетить это место и поделиться своими впечатлениями!</p>";
}
echo "<BR><form name=otkik action=/otklik.php method=post>";
echo "<TABLE BORDER=0 cellspacing=0 CELLPADDING=0 class=travell0 width=100%>";

if ((!($ex_descr)) && ($et == 1) && (($forum_user_level) || ($forum_user_rank)) ) {
 echo "<tr><td>&nbsp;</td><td colspan=2>Для достопримечательности нет <b>подробного</b> описания. Можете его <a href=/sitemgr.php?admt=text&s=edit&exutown=$ex>создать</a> или просто</td></tr>\n";
}

# mysql_free_result($result);

if ($error) {
 echo "<tr><td colspan=3><h3 class=travel11>$error</h3></TD></tr>\n";
}

echo "<tr><td>&nbsp;</TD><TD><b>Оставить свое мнение:</b><br>\n"; 
echo "Просим оставлять только личное мнение о посещении или исправление информации.<br>\n";
echo "Вопросы, комментарии к отзывам других посетителей - пожалуйста, на <a class=travell5r target=_blank href=$forumname/>форуме</a>.</td></tr>\n";
echo "<tr><td align=right><label for=\"otklik-name\"><b>Имя:</b></label>&nbsp;</TD><TD><INPUT id=\"otklik-name\" name=name type=text style=\"width: 100%;\"";
if ($forum_user_id != 0) {
 echo " value='$forum_user_name'";
}
echo "></TD></TR>\n";
if ($forum_user_id == 0) {
 echo "<TR><td>&nbsp;</td><td class=\"t13 tc\">Если Вы хотите, чтобы отзыв был подписан \"участником форума\",надо <a class=travell5r target=_blank href='/sitemgr.php'>войти на сайт</a>.<br></TD><td></td></TR>\n";
}
echo "<TR><td align=right><label for=\"otklik-mail\"><b>Эл. адрес:</b></label>&nbsp;</TD><TD><INPUT id=\"otklik-mail\" name=mail type=text style=\"width: 100%;\">";
echo "<input type=hidden name=\"valck\" value=\"".$_valck."\"></td></TR>";
echo "<TR><TD align=right valign=top><br><br><label for=\"otklik-message\"> <b>Оставить отзыв:</b></label>&nbsp;</TD><TD>"
. "<TEXTAREA id=\"otklik-message\" name=message style=\"width: 100%; height: 100%; min-height: 14em; box-sizing: border-box;\" wrap=top>";
if ($_has_otkl) {
   echo $_has_otkl;
}
echo "</TEXTAREA></TD>\n";
echo "<TD align=left>";
echo "<br>&nbsp;&nbsp;<b>Оценка:</b><br>";
echo "<input TYPE=radio VALUE=1 NAME=mark>Никогда больше сюда не поеду! (1)<br>";
echo "<input TYPE=radio VALUE=2 NAME=mark>Если уж совсем делать нечего (2)<br>";
echo "<input TYPE=radio VALUE=3 NAME=mark>Могло бы быть и лучше (3)<br>";
echo "<input TYPE=radio VALUE=4 NAME=mark>Неплохо, очень неплохо (4)<br>";
echo "<input TYPE=radio VALUE=5 NAME=mark>Обязательно надо посетить! (5)<br><br>";
echo "<INPUT name=submit type=submit value=\"";
  if ($_has_otkl) {
   echo "Изменить свой отзыв";
  }
  else {
   echo "Сохранить на сайте";
  }
echo "\">";
if ($_has_otkl) {
 echo "И не забудьте поставить оценку";
}
echo "<input TYPE=hidden VALUE=$town NAME=town>";
echo "<input TYPE=hidden VALUE=$et NAME=et>";
echo "<input TYPE=hidden VALUE=$ex NAME=ex>";
echo "</td></tr></table>\n";
echo "</FORM></td>"; //конец столбца октлика и формы ввода отклика

?> <td width=160px align=right valign="top">
  
  <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- Автотревел, отклики -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-2266145788867998"
     data-ad-slot="9127168265"
     data-ad-format="auto"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
  </td></tr></table>

      <?php
if ($forum_user_id) {
 if ($forum_user_time < (time() - 1800)) {
  echo "<script language=\"javascript\" type=\"text/javascript\">alert('Регистрация на сайте заканчивается меньше чем через полчаса.');</script>\n";
 }
}

if (is_file("reklama/e-".$ex)) {
 include("reklama/e-".$ex);
}
echo "</div>";
include('include/footer_bs.php');

?>

