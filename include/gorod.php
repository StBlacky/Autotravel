<?php

/* 
 * Общий блок кода для gorod.php и town.php
 */

if (!($_display_tables)) {
  if ($_tblx_mode) {
    $_display_tables = 1;  
  }
  if ($_text_mode) {
    $_display_tables = 1;  
  }  
}
if ($_text_mode) {
  $_gmap_mode=9;
}

if ($t_hide == 2) {
  // редирект на отклик
  $e_id = getextown($town);
  $newuri = "/otklik.php/" . $e_id;
  Header( "HTTP/1.1 301 Moved Permanently" );
  Header( "Location: $newuri" );
  exit;
}

$searchurl="/test.php?tid=$town&amp;adiff=$defdiff&amp;thistid=1&amp;";
$ffile = $myrow["ffile"];
$s_name_arr = preg_split ("/\//" , $myrow["name"]);
$s_name = $s_name_arr[0];
$s_name2 = $s_name_arr[1];
if ($s_name == "") {
  $s_name =  $myrow["name"];
}
$booking = $myrow["booking"];
$s_nameto = $myrow["nameto"];

$has_booking = 0;

if ($s_nameto == "") {
  $s_nameto = "город ".$s_name;  
}
$s_namefrom = $myrow["namefrom"];
if ($s_namefrom == "") {
  $s_namefrom = "города ".$s_name;  
}
$s_namepo = $myrow["namepo"];
if ($s_namepo == "") {
  $s_namepo = "городу ".$s_name;  
}
$s_namearound = $myrow["namearound"];
if ($s_namearound == "") {
  $s_namearound = "города ".$s_name; 
}

$s_namein = $myrow["namein"];
if ($s_namein == "") {
  $s_namein = "городе ".$s_name;  
}
$s_area = $myrow["id_area"];
$Nshirota = (float)$myrow["Nshirota"];
$Edolgota = (float)$myrow["Edolgota"];
$ngrv = degreestotext($Nshirota);
$egrv = degreestotext($Edolgota);
if (!($decrip)) {
  $decrip = $myrow["description"];
}


if (($ngrv) and ($egrv)) {
  $nlat = $Nshirota;
  $nlng = $Edolgota;
  $coord = "N$ngrv, E$egrv";
  $metacoord = "<meta name=\"geo.position\" content=\"$Nshirota; $Edolgota\">\n<meta name=\"ICBM\" content=\"$Nshirota, $Edolgota\">";
}

$a_name = getareaname($s_area);
$title=$myrow["metatitle"];
if (!$title) {
      $title= "{$s_name} — что посмотреть и где остановиться | Autotravel.ru";
}
 
$ex_forms = "";

if (!($_text_mode)) {
  $photocount=countfototown($town);
  $photoimga=getfototownl($town,6,"");
  $photoimg = $photoimga[0];

  if (!($ffile)) {
    $ffile = $photoimga[1];  
    
  }
}
else {
  $photocountx=countfototown($town);
}
if ($ffile) {
  $metafbimage = "https:".$sitename."/phalbum/".$ffile.".jpg"; // фото для фейсбука
} 

$metadesc =  "{$s_name}: достопримечательности, маршруты, гостиницы и советы путешественников. Что посмотреть в {$s_namein}, где остановиться и чем заняться во время поездки в {$s_nameto}.";
$metadescown="";
$metadescown=$myrow["metadesc"];
if ($metadescown) {
$metadesc =  $metadescown;
}
$r_types=getrtypes_bs();
 
//проходим по записям для вывода
$sql="select e.name as name, e.descrip as description, e.Nshirota as shirota,
  e.Edolgota as dolgota, e.id as id, e.booking as booking, e.mainid,
  e.votes as votes, e.rate as rate, e.type as etype, e.kind as ekind,
  Right( e.name, length( e.name ) - locate( '\"', e.name )) AS loc,
  e.wrate as wrate, e.ffile as ffile
  from exciting e where e.town='$town' and e.mainid<=0 
  order by wrate desc, loc asc";

$result = $dbi->query($sql);
$outxx = "\n<!-- $sql -->\n";
$cards = array(); // все объекты
$c_excitings = array(); // количества по типам объектов
$ii = 0;
if($myrow = mysqli_fetch_array($result)) {
  do {
    $etype = $myrow["etype"];
    $card = array();
    $card["ename"] = $myrow["name"];
    $card["mainid"] = $myrow["mainid"];
    $card["Nshirota"] = (float)$myrow["shirota"];
    $card["Edolgota"] = (float)$myrow["dolgota"];
    if (($card["Edolgota"] > 0) and ($card["Nshirota"] > 0)) {
      $card["coords"] = "N" . degreestotext($card["Nshirota"]) . ", E" . degreestotext($card["Edolgota"]);
    }
    else {
      $card["coords"] = "";
    }
    $card["descrip"] = $myrow["description"];
    $card["votes"] = $myrow["votes"];
    $card["ffile"] = $myrow["ffile"];
    $card["kind"] = $myrow["ekind"];
    $s_id = $myrow["id"];
    $card["ex"] = $s_id;
    $wrate   = (float)$myrow["wrate"];
    $card["wrate"] = sprintf("%01.2f",$wrate);
    $ngrv = degreestotext($cards[$ii]["Nshirota"]);
    $egrv = degreestotext($cards[$ii]["Edolgota"]);
    if ($myrow["etype"] == 2) {
   	$card["booking"] = $myrow["booking"];
    } 
    else {
   	$card["booking"] = "";
    }
    $t_r = split_descr($myrow["description"]); //описание объекта
    $card["descr_local"] = $t_r[0]; // описание без адреса и режима работы
    $card["descr_local_full"] = display_t_r($t_r, "",0);
    if ((!($card["descr_local_full"])) && ($card["mainid"]<0)) {
      $card["descr_local_full"] = getgrdescr($myrow["id"], 5);  
    }

    $card["mark"] = bs_calc_rating($myrow["votes"],$myrow["wrate"]);

    $cards[$etype][] = $card;
    if (!(isset($c_excitings[$etype]))) {
      $c_excitings[$etype] = 1;
    }
    else {
      $c_excitings[$etype]++;
    }
  } while($myrow = mysqli_fetch_array($result));
  mysqli_free_result($result);
}

// --- Что привезти: справочник категорий (type_ex.id => type_ex.desc)
$type_ex_desc = array();
$res_k = $dbi->query("SELECT id, `desc` FROM type_ex");
if ($res_k) {
  while ($r = mysqli_fetch_assoc($res_k)) {
    $type_ex_desc[trim($r['id'])] = $r['desc'];
  }
  mysqli_free_result($res_k);
}


// диференцируем ключевые слова и описания, делаем все как в тайтле
//  $metadesc = $metadesc . "Отзывы посетителей, ";
//  $metatags = $metatags . "Отзывы посетителей ";

reset($r_types);
$searchlist = "";
$ex_header = "";
$addhead = "";
 
$searchlist .=  "<h4>Поиск в окрестностях ".$s_namefrom."</h4>";
$searchlist .=  "<p class=travell0>Что посмотреть в радиусе ".$defdiff." км от $s_namefrom. Переход на страницу поиска.</p><ul>\n";

while(list($type_id,$type_name)=each($r_types)) {
  if (($type_id == 1) or ($type_id == 6)) {
    $searchlist .=  "<li><a class=travell5n target=_blank href=\"/test.php?tid=$town&amp;extype[]=$type_id&amp;adiff=$defdiff&amp;thistid=1&amp;psubmit=Найти\">".$type_name["name"]."</a></li>\n";
  }
  if ($c_excitings[$type_id]) {
    if ($ex_header) {
      $ex_header .= ", " . strtolower($type_name["name"]);
    }
    else {
      $ex_header .= strtolower($type_name["name"]);
    }
  }
}
$searchlist .= "</ul>\n";
// $metadesc = $metadesc . "Отчеты о поездках";
$metadesc = str_replace("\n",' ',$metadesc);
$metadesc = str_replace("\r",' ',$metadesc);
$metadesc = str_replace('"',' ',strip_tags($metadesc));
if ($_gmap_mode==9) { // не показывать
  $dispgmap = "";
}
elseif($_gmap_mode==1) { // osm
  $dispgmap = 1;   
}
elseif($_gmap_mode==2) { // yandex
  $dispgmap = 2;   
}

else { // google
  $dispgmap = 3;   
}
$_totop = 1;

include('include/header_bs.php');

$mapiframe = "";
$fotoframe= "";
if ($coord) {
  $kml = "/kml.php/t/$town";
  $mapiframe  = "<div id=gmapv3 style=\"display:none;\">&nbsp;</div><script type=\"text/javascript\">";
  $mapiframe .= "initmapv".$dispgmap;
  $mapiframe .= "(\"".$kml.$kmladd.".kml\",null,$nlat,$nlng);</script> <a target=_blank href=\"/map.php/t/$town\">Открыть карту в новом окне</a><br>";
}
if (!($_text_mode)) {
  $ownfoto = countownfototown($town);
}
if (($photocount) or ($ownfoto)) {
  $fotoframe = "<div style=\"text-align:center\"><div id=\"fotoimages\">$photoimg</div>\n";
  if (($photocount>6) or ($_fotomanager)) {
    //$fotoframe .= "<p id=\"fotostownstart\" class=\"travell5m\" style=\"cursor:hand\">Открыть все фотографии ($photocount фото) $namefrom</p>\n";
    $fotoframe .= "<a class=\"btn btn-info\" target=\"_blank\" href='/".$_fotoalbum."/town/$town'>Открыть все фотографии ($photocount фото) $s_namefrom</a>\n";
  }  
  if ($ownfoto > 0) {
    $fotoframe .= "<a class=\"btn btn-info\" target=_blank href='/fotoman.php?utown=$town'>Собственные фотографии $s_namefrom: $ownfoto</a>\n";
  }
  $fotoframe .= "</div>\n";
  
  $customscript .= ' 
    $(".cardlinkbutton").click(function() {
      var $link = $(this).closest(".exccard").children(".cardhead").children(".exclink");
      $link[0].click();
    });
    ';
}

# $sql_r = "select l.key_towns as key_town, l.nick as nick, l.month as month, l.year as year, l.group_rep as gr, l.ip as ip, l.time as t from l_reports l where l.key_towns regexp '[[:<:]]".$s_name."[[:>:]]'";
$sql_r = "select l.key_towns as key_town, l.cook as cook, l.nick as nick, l.month as month, l.year as year, l.group_rep as gr, l.rid as rid, l.spring as spring 
           from l_reports l, t_reports t where 
           l.rid>0 and l.rid = t.rid and t.tid = $town order by l.year desc, l.month desc limit 10";
$result_r = $dbi->query($sql_r);
$vv = '';
$rep_count = $result_r->num_rows;
$cnt = 0;
if ($rep_count) {
 $vv = "<p class=travell0>";
 while($myrow_r = mysqli_fetch_array($result_r)) {
  $s_rid = $myrow_r["rid"];
  $s_key = $myrow_r["key_town"];
  $s_key = getskeytext($s_key,$rid);
  $s_month = $myrow_r["month"];
  $s_year = $myrow_r["year"];
  if (($cnt<5) or ($rep_count<8)) {
    $vv .= "<a class=travell5r href=\"/v_report.php/$s_rid\">$s_key</a>&nbsp;($s_month/$s_year)";
    $vv .= "<br/>";
  }
  $cnt++;  
 }
 mysqli_free_result($result_r);
 if ($rep_count>7) {
   $vv .= "<a class=travell5n href=\"/t_report.php?town=$town\">Другие отчеты о поездках в $s_nameto</a>\n";
 }  
 $vv .= "</p>\n";
}

$traces = "";
$sql_tr = "SELECT DISTINCT t.id AS trace_id, t.name AS trace, t.of_name as of_name FROM trace t, far_tr ft WHERE ft.town = '$town' AND t.id = ft.trace order by t.of_name";
$result_tr = $dbi->query($sql_tr);

if (mysqli_num_rows($result_tr) > 0) {
  while ($myrow_tr = mysqli_fetch_array($result_tr)) {
    $s_trace = $myrow_tr["trace"];
    $s_traceid = $myrow_tr["trace_id"];
    $s_on = $myrow_tr["of_name"];
    $traces .= "<a class=travell5r href=\"/trace.php/$s_traceid\">$s_on $s_trace </a><br>";
  } 
}
mysqli_free_result($result_tr);
echo "<div class=\"container-fluid\">"; 
$head00 = '<ol itemscope itemtype="http://schema.org/BreadcrumbList" class="breadcrumb">';
if ($s_area<0) {
  $head00 .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
  $head00 .= '<a itemprop="item" itemscope itemtype="http://schema.org/Thing" href="/countries.php">';
  $head00 .= '<span itemprop="name">';
  $head00 .= 'Зарубежные страны';
  $head00 .= '</span></a><meta itemprop="position" content="1" /></li>';
}
else {
  $head00 .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
  $head00 .= '<a itemprop="item" itemscope itemtype="http://schema.org/Thing" itemid="//autotravel.ru/areas.php" href="/areas.php">';
  $head00 .= '<span itemprop="name">';
  $head00 .= 'Области России';
  $head00 .= '</span></a><meta itemprop="position" content="1" /></li>';
}
$head00 .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
$head00 .= '<a itemscope itemtype="http://schema.org/Thing" itemprop="item" itemid="//autotravel.ru/area.php/'.$s_area.'" href="/area.php/'.$s_area.'">';
$head00 .= '<span itemprop="name">';
$head00 .= $a_name;
$head00 .= '</span></a><meta itemprop="position" content="2" /></li>';
$head00 .= "<h1 class=\"neucha\">$s_name</h1>";
if ($photocount) {  
    $head00 .= "<li><a class=\"travell5r\" href=\"/phalbum.php/town/$town\">Фотографии</a></li>"; 
  }

  
reset($r_types);
while(list($type_id,$type_name)=each($r_types)) {  
  if ($c_excitings[$type_id]) {
    $head00 .= '<li><a class="travell5r" OnClick="ScrollD(\'#title-objii'.$type_id.'\');">'.$type_name["name"].'</a></li>';    
  }
} 
if (($dispgmap) and ($coord)) {
  $head00 .= '<li><a class="travell5r" OnClick="ScrollD(\'#title-objmap\');">Карта</a></li>';    
}
if ($vv) {
  $head00 .= '<li><a class="travell5r" OnClick="ScrollD(\'#title-objreports\');">Отчеты</a></li>';    
}
if ($traces) {
  $head00 .= '<li><a class="travell5r" OnClick="ScrollD(\'#title-itraces\');">Трассы</a></li>';    
}
$head00 .= "</ol>\n";

$news = '';
if ($t_hide == 0) {
  $sql2 = "select max(date) as news_date from news where exciting='t$town'";
  $result2 = $dbi->query($sql2);
  if($myrow2 = mysqli_fetch_array($result2)) {
    $news_date = $myrow2["news_date"];
  }
  else {
    $news_date = "";
  }
  
  mysqli_free_result($result2);
  if ($news_date <> "") {
    $news .= "<p class=travell5><a target=\"_blank\" class=travell5n href='/news.php?tn=$town'>Архив новостей по $s_namepo</a></p>";
  }
}

/**  разметка страницы
 *  ----------+----------
 *  заголовок +  карта (может не отображаться)   
 *  фото      +
 * -----------+----------
 *  описание  
 * 
 */

if ($dispgmap) {
  echo '<div class="row"><div class="col-md-6 col-sm-12">';
}
else {
  echo '<div class="row"><div class="col-sm-12">';
}  
// заголовок
echo "<div class=\"row\"><div class=\"col-xs-12\">".$head00;
echo "</div></div>\n";
// фотографии
if ($_text_mode) {
  if ($photocountx) {  
    echo "<a class=\"btn btn-info\" target=\"_blank\" href='/".$_fotoalbum."/town/$town'>Открыть $photocountx фото $s_namefrom</a>\n";
  }  
}
else {
 if ($fotoframe) {
  //echo show_panel("Фотографии $s_namefrom\n", "h2", $fotoframe, 1, "fotoframeq");
  echo $fotoframe;  
 }
} 
if ($dispgmap) {
  echo '</div><div class="col-md-6 col-sm-12">';
  if ($_text_mode) {
    echo show_panel("Интерактивная карта ".$s_namefrom." и окрестностей","h3",$mapiframe,0,"objmap");  
  }
  else {
    echo show_panel("Интерактивная карта ".$s_namefrom." и окрестностей","h3",$mapiframe,1,"objmap");  
  }  
}
if ($coord) {
  echo "<div class=\"t14 tb tc\">\n";
  echo kml_display($kml);
  echo "</div>\n";
}
echo "</div></div>\n";

$obj_key = 't'.$town;
$notes = include($_SERVER['DOCUMENT_ROOT'].'/include/notes_travelers.php');

if ($decrip || $notes) {
  echo "<div class=\"col-xs-12 well tj t0\">";

  if ($decrip) {
    echo "<div class=\"note-block\">".$decrip."</div>";
  }

  if ($notes) {
    echo $notes;
  }

  echo "</div>\n";
}




// карта описание фото закончены
// объекты по разделам
// Если объектов в разделе меньше 5 - то все только тут, без линка на отдельный раздел с объектами по городу.
// Если объектов 5 или больше - то тут только 4 объекта, остальные в отдельном разделе + линк на него с указанием количества
//
//
echo $outxx;
reset($r_types);
while(list($type_id,$type_name)=each($r_types)) {  
 if ($_display_tables) {
  // старый "табличный" вывод
  if ($c_excitings[$type_id]) {
   $localcount = $c_excitings[$type_id];
   $h = "";
   $t = "";
   $f = "";
   if ($localcount>4) {
     $h .= "<p>Полный список - <strong>" . $c_excitings[$type_id]. " " . echo_int($c_excitings[$type_id], array("объектов","объект","объекта"))." ".$type_name["longname"]." в ".$s_namein."</strong> - на отдельной странице</p>"; 
     //$h .= "<h4 class=\"text-uppercase\">Все ";
     $h .= "<a class=\"btn tz ttb travell5m\" target=_blank href=\"/excite.php/$town/$type_id\">";
     $h .= $type_name["allname"] . " " . $s_namefrom . "</a>"; //</h4>
   }
   $t = '<table id="t'.$type_id.'" width="95%" class="table"><tbody>';
   $t .= '<tr><th class="table-rate">Рейтинг</th><th>Название, <a class="travell5n" onclick="AllToggle(\'t'.$type_id.'\',this);">подробности <img alt=">" src="/jpg/plus.gif" border="0"></a><div></div></th></tr>';
   
   foreach ($cards[$type_id] as $card) {  
     $t .= '<tr><td class="table-rate">'.$card["wrate"].'</td><td valign="middle" align="left">';
     $t .= '<a target="_blank" class="travell5n" href="/';
     if ($card["mainid"]>=0) {
       $t .= 'otklik.php';
     }
     else {
       $t .= 'group.php';
     }
     $t .= '/'.$card["ex"].'">'.$card["ename"].'</a>';
     $t .= '&nbsp;&nbsp;<a rel="nofollow" class="travell5n" onclick="ChToggle(\'e'.$card["ex"].'\',this);"><img alt=">" src="/jpg/plus.gif" border="0"></a>';
     $t .= '<div title="CHG" id="e'.$card["ex"].'" style="visibility: hidden; display: none;">'.$card["descr_local_full"];
     if ($card["coords"]) {
       $t .= '<p class="travell0"> Координаты: <strong>'.$card["coords"].'</strong></p>';    
     }
     //<div id="basketadd6110" class="travel0"><a class="travell5r" onclick="BasketAdd(6110,3349,&quot;basketadd6110&quot;)">Добавить в Испания. Июль 2018. Вариант 3</a></div>
     if (($forum_user_id) and ($card["mainid"]>=0)) {
       $t .= "\n<div class=\"travel0\" id=\"basketadd".$card["ex"]."\"><a onclick='BasketAdd(".$card["ex"].",".$forum_user_basket.",\"basketadd".$card["ex"]."\")'>Добавить в ".$forum_user_basket_name."</a></div>\n";
     }  
     $t .= '</div>';
     $t .= "</td></tr>\n";
   }
   $t .= '</tbody></table>';
   if ($type_id == 2) { //
      if ($booking) { 
        $f .= "<h5><a class=travell5n rel=\"nofollow\" target=_blank href=\"$booking?aid=337822\">А ещё можно забронировать гостиницу $s_namefrom на сайте&nbsp;";        
        $f .= "<img border=0 src='/jpg/booking_small.gif' width=89 height=10 alt=\"Booking.com\" title=\"Booking.com\"></a></h5>\n";
        $has_booking = 1;
      }  
   }
   echo show_panel($type_name["name"]." ".$s_namefrom,"h2",$h.$t.$f,1,"objii".$type_id,$addbutton);    
   if ($vv) {
     echo show_panel("Отчеты о поездках в ".$s_nameto,"h3",$vv,1,"objreports");  
     //echo "<div class=\"col-xs-12 t0\" id=\"title-objreports\" style=\"padding-bottom: 16px\"><h3>Отчеты о поездках в ".$s_nameto."</h3>".$vv."</div>\n";      
     $vv='';
   }
  }   
 } 
 else {
  // плиточный с карточками   
  if ($c_excitings[$type_id]) {
     if ((int)$type_id === 5) {

        // 1) справочник категорий type_ex.id => type_ex.desc (локально для города)
        $type_ex_desc = array();
        $res_k = $dbi->query("SELECT id, `desc` FROM type_ex");
        if ($res_k) {
          while ($r = mysqli_fetch_assoc($res_k)) {
            $type_ex_desc[trim($r['id'])] = $r['desc'];
          }
          mysqli_free_result($res_k);
        }

        $localcount = (int)$c_excitings[$type_id];

        // 2) заголовочный текст + ссылка "Смотреть всё по региону"
        $h  = "";
        $hl = "";
        $f  = "";

        if (!empty($s_area)) {
          $hl .= "<a class=\"btn tz ttb travell5m\" href=\"/excite_bring_area.php?area=".$s_area."\">Смотреть всё по региону</a>";
        }

        // 3) карточки
        $t = "\n<div id=\"t".$type_id."\" class=\"bring-grid\">\n";

        $limit = 6; // сколько показываем на странице города
        $n = 0;

        foreach ($cards[$type_id] as $card) {
          if ($n >= $limit) break;

          $kind_id = isset($card["kind"]) ? trim((string)$card["kind"]) : "";
          $typeDesc = ($kind_id !== "" && isset($type_ex_desc[$kind_id])) ? $type_ex_desc[$kind_id] : "";

          $intro = bring_intro($card["descrip"]);
          $photo = bring_photo_url($card["ex"], $card["ffile"]);

          // город скрываем: townId=0, townName=""
          $t .= render_bring_card(
            $card["ex"],
            $card["ename"],
            $intro,
            $photo,
            0,
            "",
            $typeDesc,
            ""
          );

          $n++;
        }

        $t .= "</div>\n";

        // 4) показать панель
        echo show_panel($type_name["name"]." ".$s_namefrom, "h2", $h.$hl.$t.$f, 1, "objii".$type_id, "");

        // отчёты (как у тебя)
        if ($vv) {
          echo show_panel("Отчеты о поездках в ".$s_nameto, "h3", $vv, 1, "objreports");
          $vv='';
        }

      }
      // --- ВСЕ ОСТАЛЬНЫЕ ТИПЫ: твой текущий плиточный вывод (как был)
      else {  
    $t = "\n<div id=\"t" . $type_id . "\" class=\"autocard-simple\">\n";
    $localcount = $c_excitings[$type_id];
    $cardclass = "autocards";
    $txtclass = "tj";
    if ($localcount == 1) {
      $cardclass = "tl autocard1";
      $txtclass = "tl";
    }
    if ($localcount == 2) {
      $cardclass = "tl autocard2";
      $txtclass = "tl";
    }
    $h = "";
    $hl = '';
    $f = "";
    $display = 0;
    $uho = "";
    $odd = 0;
    // полный список, если мало объектов  
    foreach ($cards[$type_id] as $card) {  
      if ($localcount>2) {  
        if ($display == 0) {  
          $t .= "<div class=\"col-sm-12 cardbox col-md-6\">\n";
        }
        if ($display==2) {
          $t .= "</div><div class=\"col-sm-12 cardbox col-md-6\">\n";
          $odd = 1;
        }
        if ($display==4) {
          $t .= "</div>\n";
          $odd = 0;
        }
      }    
      if ($display<4) {  
        $t .= draw_card($card,$cardclass,$txtclass);
      }
      //else {
        if ($display<8) {
          if ($uho) {
            $uho .= ", ".  $card["ename"];
          }
          else {
            $uho = $card["ename"];
          }
        }
      //}
      $display++;
    }
    if ($odd) {
       $t.= "</div>\n";
       $odd = 0;
    }   
    $t .= "</div>\n";
    if ($localcount>4) {
      $hl .= "<p>Полный список - <strong>" . $c_excitings[$type_id]. " " . echo_int($c_excitings[$type_id], array("объектов","объект","объекта"))." ".$type_name["longname"]." в ".$s_namein."</strong> - на отдельной странице</p>"; 
      //$hl .= "<h4 class=\"text-uppercase tb t18\">Все ";
      $hl .= "<a class=\"btn tz ttb travell5m\" target=_blank href=\"/excite.php/$town/$type_id\">";
      $hl .= $type_name["allname"] . " " . $s_namefrom . "</a>"; //</h4>
      $hl .= "<p>В том числе ".$uho.". 4 самые популярные:</p>";
    }  
    if ($type_id == 2) { //
      if ($booking) {  
        $f .= "<h5><a class=travell5n rel=\"nofollow\" target=_blank href=\"$booking?aid=337822\">А ещё можно забронировать гостиницу $s_namefrom на сайте&nbsp;";        
        $f .= "<img border=0 src='/jpg/booking_small.gif' width=89 height=10 alt=\"Booking.com\" title=\"Booking.com\"></a></h5>\n";
        $has_booking = 1;
      }  
    }
    if ($f) {
      $f = "<div class=\"autocard-footer\">".$f."</div>\n";  
    }
    $addbutton = '';
    if ($localcount>4) {
      $addbutton="<button class=\"btn btn-xs btn-info pull-right\" id=\"button-objii$type_id\" OnClick=\"LoadUrlBtn('/tech_town_ex.php/$town/$type_id','$type_id')\">Загрузить список в это окно</button>";
    }
    echo show_panel($type_name["name"]." ".$s_namefrom,"h2",$h.$hl.$t.$f,1,"objii".$type_id,$addbutton);    
    if ($vv) {
      echo show_panel("Отчеты о поездках в ".$s_nameto,"h3",$vv,1,"objreports");  
      //echo "<div class=\"col-xs-12 t0\" id=\"title-objreports\" style=\"padding-bottom: 16px\"><h3>Отчеты о поездках в ".$s_nameto."</h3>".$vv."</div>\n";      
      $vv='';
    }
      }
  }
 } 
}

if ($vv) {
  echo show_panel("Отчеты о поездках в ".$s_nameto,"h3",$vv,1,"objreports");  
  $vv='';
}

if (!($_display_tables)) {
  $customscripttop .= '
  function LoadUrlBtn(url,id) {
    $("#content-objii"+id).load(url, function() {
      $("#button-objii"+id).hide();
    });
  } 
  ';
}

$tv = "";

if ($traces) {
  $tv .= show_panel("Трассы, проходящие через $s_nameto", "h3", $traces, 1, "itraces");    
}

$tv .= $news;

if (($booking) and ($has_booking==0)) {
  $tv .= "<h5><a class=travell5n rel=\"nofollow\" target=_blank href=\"$booking?aid=337822\">А ещё можно забронировать гостиницу $s_namefrom на сайте&nbsp;";        
  $tv .= "<img border=0 src='/jpg/booking_small.gif' width=89 height=10 alt=\"Booking.com\" title=\"Booking.com\"></a></h5>\n";    
}

//$tv .= $searchlist;
if ($tv) {
  echo show_panel("Полезная информация для путешествующих в $s_nameto\n", "h2", $tv, 1, "usefulinfo");
} 

 ?>
<div class="row">
  <div class="col-sm-6 col-xs-12 well">
    <?php echo $searchlist; ?>  
  </div>    
  <div class="col-sm-6 col-xs-12 well">
  <Form name="print" method="post" action="/exi.php"  target="_blank">
  <div class="col-sm-6">
  <?php    
   if ($addhead) {
     echo "<input type=hidden name=head value='$addhead'>\n";
   }
   echo "<input type=hidden name=town value=$town>\n";
  ?> 
    <p class="travell0">Информацию по <?php echo $s_namepo;?> 
    </p>
    <input class="atb" type="submit" value='Распечатать на принтер'>
  </div>  
  <div class="col-sm-6">
     <?php
       reset($r_types); 
       while(list($type_id,$type_name)=each($r_types)) {
         if ($c_excitings[$type_id]) {
           echo "<label class=\"checkbox\"><input type=\"checkbox\" name=et0".$type_id." value=1 CHECKED>".$type_name["name"]."</label>\n";
         }  
      }
      ?>
      <label class="checkbox"><input type="checkbox" name="otk" value="1">С отзывами</label>
      <label class="checkbox"><input type="checkbox" name="photo" value="1">С фотографиями</label>
  </div>
  </form>
  </div>  
</div>  

</div>
<?php
include('include/footer_bs.php');

