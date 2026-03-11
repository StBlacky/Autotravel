<?php

include_once('include/config.php');
include_once('include/func.php');
include_once('include/func_bs.php');
include_once('include/head00.php');
$bootstrap = 1;
// не включать: не дружит с фильтром и последними откликами $masonry = 1;
if ($_text_mode) {
  $_gmap_mode=9;
}

if ($_gmap_mode==9) { // не показывать
   $dispgmap = "";
}
elseif($_gmap_mode==1) { // osm
   $dispgmap = 1;   
}
elseif($_gmap_mode==2) { // osm
   $dispgmap = 2;   
}
else { // google
   $dispgmap = 3;   
}
// для initmap
$nlat = 0; 
$nlng = 0;
//
if (!($_text_mode)) {
  $colorbox = 2;
}
else {
  $colorbox = 0;  
}

if (preg_match("/^\/(\d+)\/(\d+)(.*)$/",$_pin,$exa)) {
 $town = $exa[1];
 $et   = $exa[2];
 $garbage = $exa[3];
 if ($garbage) {
   $newuri = "/town.php/" . $town;
   Header( "HTTP/1.1 301 Moved Permanently" );
   Header( "Location: $newuri" );
   exit;
 }
 if ($et < 1) {
   $newuri = "/town.php/" . $town;
   Header( "HTTP/1.1 301 Moved Permanently" );
   Header( "Location: $newuri" );
   exit;
 }
}
elseif (preg_match("/^\/(\d+)$/",$_pin,$exa)) {
 $town = $exa[1];
 $et = 0;
}
else {
 $et = $_REQUEST["et"];
 $et = (int) mysafestring($et);
 $town = $_REQUEST["town"];
 $town = (int) mysafestring($town);
}

if (!($town)) {
  $newuri = "/cube.php";
  Header( "HTTP/1.1 301 Moved Permanently" );
  Header( "Location: $newuri" );
  exit;  
}

if (!($et > 0)) {
  $newuri = "/town.php/" . $town;
  Header( "HTTP/1.1 301 Moved Permanently" );
  Header( "Location: $newuri" );
  exit;
}

$canon = "/excite.php/".$town."/".$et;
if(isset($_GET["z"])) {
  $z = mysafestring($_GET["z"]);
  $preselected = explode(',', $z); // сюда можно положить перечень типов примечательностей, по которым отфильтровать фильтр
}
else {
  $preselected=array();
}

$flagresult="";

$r_types=getrtypes(); //наименование типа объекта (дотсоприм, гостиницы и т.д.)
$et_name=$r_types[$et];
if (!($et_name)) {
  $newuri = "/town.php/" . $town;
  Header( "HTTP/1.1 301 Moved Permanently" );
  Header( "Location: $newuri" );
  exit;
}

$t_name = gettownnamearr($town);
$town_name = $t_name[1]; //НАЗВАНИЕ (ОБЛАСТЬ)

if ($t_name[13]) {
  $metafbimage = "https:".$sitename."/phalbum/".$t_name[13].".jpg"; // фото для фейсбука
}  


//проходим по записям для вывода
if ($_salp_mode) {
  $sqladd = "";
}
else {
  $sqladd = " wrate desc, ";
}
$sql="select e.name as name, e.ffile as ffile, e.descrip as description, e.Nshirota as shirota,
  e.Edolgota as dolgota, e.id as id, e.booking as booking, e.mainid,
  e.votes as votes, e.rate as rate, e.type as etype, e.kind as ekind,
  Right( e.name, length( e.name ) - locate( '\"', e.name )) AS loc,
  e.wrate as wrate
  from exciting e where e.town='$town' and e.type in ($et) and e.mainid<=0
  order by $sqladd loc asc";

$result = $dbi->query($sql);
$cnt = $result->num_rows;

if ($t_name[10]) {
  if ($cnt<5) {
    $newuri = "/gorod/" . $t_name[10] . ".html";
    Header( "HTTP/1.1 301 Moved Permanently" );
    Header( "Location: $newuri" );
    exit;
  }
}  

if (!($cnt)) {
    $newuri = "/town.php/" . $town;
    Header( "HTTP/1.1 301 Moved Permanently" );
    Header( "Location: $newuri" );
    exit;
}

$searchurl="/test.php?tid=$town&amp;adiff=$defdiff&amp;thistid=1&amp;";

$title_h=$et_name . " " . $t_name[6]; //Достопримечательности Тамбова - отражение на странице H1
$title = $t_name[1] . " Что посмотреть" ; //для поисковиков и выдачи . ": адреса, режим работы, отзывы" уберем пока

$metatags = $title_h;  //Достопримечательности Тамбова
$metadesc = str_replace('"',' ',$metatags);

$metadesc = $metadesc . ", адреса, режим работы, описания, фотографии, отзывы, интерактивная карта $t_name[6]. "; //описание для поисковиков и выдачи
$metadesc = str_replace("\n",' ',$metadesc);
$metadesc = str_replace("\r",' ',$metadesc);
$metadesc = str_replace('"',' ',strip_tags($metadesc));

include("include/header_bs.php");

$out = "";
$outd = ""; //пояснительный текст
$outmap = "";
$out2 = "";
$flagresult = "";
if ($et==1) { //достопримечательности
  $outd = "<p class=travell0>";
  if ($_salp_mode) {
    $outd .= "Список <b>достопримечательностей</b> формируется на основе <b>отзывов посетителей</b> и отсортирован по алфавиту.\n";
    $outd .= "По <b>рейтингу</b>, который также формируется на основе отзывов посетителей, Вы можете выделить ";
    $outd .= "<b>самые интересные достопримечательности</b>.\n";
  }
  else {
    $outd .= "Список <b>достопримечательностей</b> формируется на основе <b>отзывов посетителей</b> и отсортирован по <b>рейтингу</b>, который также формируется на основе отзывов посетителей.\n";
    $outd .= "Таким образом, <b>вверху списка</b> находятся <b>самые интересные достопримечательности</b>.\n";
  }
  $outd .= "<br><br>Посмотреть <b>фотографии</b>, прочитать <b>отзывы</b>, на основе которых построен рейтинг, <b>оставить свой отзыв</b>, можно перейдя по ссылке на названии конкретной <b>достопримечательности</b>.<br>\n";
  $outd .= "<br>Если Вы не нашли в этом списке интересного места, о котором хотели бы поделиться впечатлениями - оставьте информацию на нашем форуме, в разделе ";
  $outd .= "<a class=travell5n target= _blank href=\"$forumname/viewforum.php?f=5\">Что посмотреть</a>.";
  $outd .= "</p>";
}

if ($et==2) {//госитиницы
 $outd =  "<p class=travell0>";
 if ($_salp_mode) {
  $outd .= "Список <b>гостиниц/b> и <b>отелей</b> формируется на основе <b>отзывов посетителей</b> и отсортирован по алфавиту.\n";
  $outd .= "По <b>рейтингу</b>, который также формируется на основе отзывов посетителей, Вы можете выделить ";
  $outd .= "<b>наиболее понравившиеся</b> автопутешественникам <b>гостиницы</b>.\n";
 }
 else {
  $outd .= "Список <b>гостиниц</b> и <b>отелей</b> формируется на основе <b>отзывов посетителей</b> и отсортирован по <b>рейтингу</b>, который также формируется на основе отзывов посетителей.\n";
  $outd .= "Таким образом, <b>вверху списка</b> находятся <b>наиболее понравившиеся</b> автопутешественникам <b>гостиницы</b>.\n";
 }
 $outd .= "<br><br>Посмотреть <b>фотографии</b>, прочитать <b>отзывы</b>, на основе которых построен рейтинг, <b>оставить свой отзыв</b>, можно перейдя по ссылке на названии конкретной <b>гостиницы</b>.<br>\n";
 $outd .= "<br>Если Вы не нашли в этом списке гостиницу, о которой хотели бы поделиться впечатлениями - <b>оставьте информацию</b> на нашем форуме, в разделе ";
 $outd .= "<a class=travell5n target= _blank href=\"$forumname/viewforum.php?f=7\">Где остановиться</a>.";
 $outd .= "</p>";
}

if ($et==3) { //рестораны
 $outd =  "<p class=travell0>";
 if ($_salp_mode) {
  $outd .=  "Список  кафе и ресторанов формируется на основе <b>отзывов посетителей</b> и отсортирован по  по алфавиту.\n";
  $outd .=  "По <b>рейтингу</b>, который также формируется на основе отзывов посетителей, Вы можете выделить ";
  $outd .=  "<b>наиболее понравившиеся автопутешественникам  кафе и рестораны</b>.\n";
 }
 else {
  $outd .= "Список <b>кафе</b> и <b>ресторанов</b>  формируется на основе <b>отзывов посетителей</b> и отсортирован по <b>рейтингу</b>, который также формируется на основе отзывов посетителей.\n";
  $outd .= "Таким образом, <b>вверху списка</b> находятся <b>наиболее понравившиеся</b> автопутешественникам <b>кафе</b> и <b>рестораны</b>.\n";
 }
 $outd .= "<br><br>Посмотреть <b>фотографии</b>, прочитать <b>отзывы</b>, на основе которых построен рейтинг, <b>оставить свой отзыв</b>, можно перейдя по ссылке на названии конкретного <b>кафе</b> или <b>ресторана</b>.<br>\n";
 $outd .= "<br>Если Вы не нашли в этом списке кафе или ресторан, о котором хотели бы поделиться впечатлениями - <b>оставьте информацию</b> на нашем форуме, в разделе ";
 $outd .= "<a class=travell5n target= _blank href=\"$forumname/viewforum.php?f=6\">Где поесть</a>.";
 $outd .= "</p>";
}

if ($et==8) {//Праздники и события
 $outd =  "<p class=travell0>";
 if ($_salp_mode) {
  $outd .= "Список  праздников и мероприятий формируется на основе <b>отзывов посетителей</b> и отсортирован по  по алфавиту.\n";
  $outd .= "По <b>рейтингу</b>, который также формируется на основе отзывов посетителей, Вы можете выделить ";
  $outd .= "<b>наиболее интересные события</b>.\n";
 }
 else {
  $outd .= "Список праздников и мероприятий формируется на основе <b>отзывов посетителей</b> и отсортирован по <b>рейтингу</b>, который также формируется на основе отзывов посетителей.\n";
  $outd .= "Таким образом, <b>вверху списка</b> находятся <b>наиболее интересные события</b>.\n";
 }
 $outd .= "<br>Если Вы не нашли в этом списке интересное мероприятие или праздник, о котором хотели бы поделиться впечатлениями - <b>оставьте информацию</b> на нашем форуме, в разделе ";
 $outd .= "<a class=travell5n target= _blank href=\"$forumname/viewforum.php?f=18\">Праздники и события</a>.";
 $outd .= "</p>";
}

// рисуем таблицу

$out2 .= "<div class=\"excitinglist\">";
$out2 .= "<table cellspacing=0 class=\"ttb tdc\" width='95%' cellpadding=3><Tbody class=\"ttb travell0 tl\">";
$out2 .= "<tr><TD class=\"ttb tb tc\" width=\"175\">$et_name $t_name[6]</TD>\n";
$out2 .= "<TD class=\"ttb tb tc\" >Описание</TD>\n";
$out2 .= "</TR>\n";


$out2 .= "\n<!-- $sql -->\n";

$cards = array();
$kinds = array();
$ii = 0;

if($myrow = mysqli_fetch_array($result)) {
  do {
    $cards[$ii]["ename"] = $myrow["name"];
    $cards[$ii]["mainid"] = $myrow["mainid"];
    $cards[$ii]["Nshirota"] = (float)$myrow["shirota"];
    $cards[$ii]["Edolgota"] = (float)$myrow["dolgota"];
    if (($cards[$ii]["Edolgota"] > 0) and ($cards[$ii]["Nshirota"] > 0)) {
      $cards[$ii]["coords"] = "N" . degreestotext($cards[$ii]["Nshirota"]) . ", E" . degreestotext($cards[$ii]["Edolgota"]);
    }
    else {
      $cards[$ii]["coords"] = "";
    }
    $cards[$ii]["votes"] = $myrow["votes"];
    $cards[$ii]["kind"] = $myrow["ekind"];
    $s_id = $myrow["id"];
    $cards[$ii]["ex"] = $s_id;
    if ($flagresult) {
      $flagresult=$flagresult . "," . $s_id;
    }
    else {
      $flagresult=$s_id;
    }
    $wrate   = (float)$myrow["wrate"];
    $cards[$ii]["wrate"] = sprintf("%01.2f",$wrate);
    $ngrv = degreestotext($cards[$ii]["Nshirota"]);
    $egrv = degreestotext($cards[$ii]["Edolgota"]);
    $cards[$ii]["rek"] = getreklama($s_id, 0);  
    if ($myrow["etype"] == 2) {
     $cards[$ii]["booking"] = $myrow["booking"];
    } 
    else {
     $cards[$ii]["booking"] = "";
    }
    if (!($_text_mode)) {
      if ($myrow["mainid"]>=0) { 
        $cards[$ii]["foto"]=get1fotoex_bs($s_id,1,0,$myrow["ffile"]);
      }
      else {
        $cards[$ii]["foto"]=get1grfotoex_bs($s_id,1,0,$myrow["ffile"]);
      }
    }  
    //
    if ($myrow["mainid"]>=0) { 
      $cards[$ii]["descrip"] = $myrow["description"];
      $t_r = split_descr($myrow["description"]); //описание объекта
      $cards[$ii]["descr_local"] = display_t_r($t_r, "tl travell0t",0);
    } 
    else {
      $cards[$ii]["descrip"] = getgrdescr($s_id, 5);
      $cards[$ii]["descr_local"] = $cards[$ii]["descrip"];
      $t_r = split_descr($cards[$ii]["descrip"]);
    }
    $cards[$ii]["mk_type"] = "";
    $cards[$ii]["mk_content"]  = "";
    //
    //  микроразметка
    //
    /*
    $cards[$ii]["mk_type"] = " itemscope itemtype=\"http://schema.org/Place\" ";
    if (($t_r[4]) and ($t_r[3])) {
      if ($myrow["etype"] == 2) {$mk_type = " itemscope itemtype=\"http://schema.org/Hotel\" ";}
      if ($myrow["etype"] == 3) {$mk_type = " itemscope itemtype=\"http://schema.org/Restaurant\" ";}
    }
    if (($ngrv) and ($egrv)) {
      $_nomap = 1;
      $cards[$ii]["mk_content"] = "<div itemprop=\"geo\" itemscope itemtype=\"http://schema.org/GeoCoordinates\">"
        . "<meta itemprop=\"latitude\" content=\"".$cards[$ii]["Nshirota"]."\"/><meta itemprop=\"longitude\" content=\"".$cards[$ii]["Edolgota"]."\"/></div>";
    }
    else {
      $cards[$ii]["mk_content"]  = "";
    }
     * 
     */
    $s_ex0 = htmlspecialchars(strip_tags(htmlspecialchars_decode($myrow["name"])),ENT_QUOTES |ENT_HTML401 , "UTF-8" , 0 );
    //$cards[$ii]["mk_content"] .= "<span itemprop=\"name\" content=\"$s_ex0\"></span>";
    //
    $ratingvalue = $myrow["wrate"];
    $ratingcount = $myrow["votes"];
    if ($ratingvalue <1) {
      $ratingvalue=1;
    }
    $bestRating = bs_calc_rating($ratingcount,$ratingvalue);
    $cards[$ii]["mk_rating"] = "";
    /*
    $cards[$ii]["mk_rating"] = "\n<div class=travell5 itemprop=\"aggregateRating\" itemscope itemtype=\"http://schema.org/AggregateRating\"> ";
    $cards[$ii]["mk_rating"] .= "<span itemprop=\"ratingValue\"  content=\"$ratingvalue\"></span>";
    $cards[$ii]["mk_rating"] .= "<span itemprop=\"ratingCount\" content=\"$ratingcount\"></span>";
    $cards[$ii]["mk_rating"] .= "<span itemprop=\"bestRating\" content=\"$bestRating\"></span>";
    $cards[$ii]["mk_rating"] .= "</div>";
     * 
     */
    $cards[$ii]["bestrating"] = $bestRating;
    $cards[$ii]["mark"]="";
    $ii++;
    if ($myrow["ekind"]) {
      // сделаю массив
      $lakind = explode(',', $myrow["ekind"]);
      //$cards[$ii]["akind"] = $lakind;
      $kinds = array_merge($kinds,$lakind);
    }
  } while($myrow = mysqli_fetch_array($result));
  mysqli_free_result($result);
}

/**/
if ($forum_user_id) {
  $stmt = $dbi->prepare("select mark from otklik where exciting=? and cook like 'forum:".$forum_user_id.":%'");
  foreach ($cards as $ii=>$card) {
    $stmt->bind_param("i", $card["ex"]);
    $stmt->execute();
    $stmt->bind_result($mymark);
    if ($stmt->fetch()) {
      $cards[$ii]["mark"] = $mymark;
    }
    else {
      $cards[$ii]["mark"] = "";
    }
  }  
  $stmt->close();
}

$mapiframe = "";
if ($flagresult) {
  if ($dispgmap) {   
    $mapiframe .= "<div id=gmapv3 class=\"embed-responsive\" style=\"display:none;\">&nbsp;</div>";
    $customscripttop .= "function showmap() { initmapv".$dispgmap."('/kml.php?ids=".$flagresult."',null,$nlat,$nlng);\n }\n";
  }
}

echo '<div class="container-fluid" style="width: 100%">'; //начало раздела 

echo '<ol class="breadcrumb">';
if ($t_name[8]<0) {
  echo "<li><a class=travell5n href=\"/countries.php\">Зарубежные страны</a></li>";
}
else {
  echo "<li><a class=travell5n href=\"/areas.php\">Области России</a></li>";  
}
echo "<li><a class=travell5n href=\"/area.php/$t_name[8]\">$t_name[2]</a></li>";
if ($t_name[10]) {
  echo "<li><a class=travell5n href=\"/gorod/".$t_name[10].".html\">$t_name[3]</a></li>";
}
else {
  echo "<li><a class=travell5n href=\"/town.php/$town\">$t_name[3]</a></li>";
}
echo "</ol>\n";
echo "<h1>".$title_h."</h1>";

echo '<div class=row>';
if ($outd) {
  echo '<div class="col-md-12 col-xs-12">';
  echo $outd;
  echo '</div>';
}
if ($mapiframe) {
  echo show_panel("Интерактивная карта","h3",$mapiframe,0,"maphidden");
}
echo "</div>\n";

echo kml_display('/kml.php/t/'.$town);

//
// Селект по типам
//
if (!(empty($kinds))) {
  //sort($kinds);
  $lakind = "'".implode("', '", $kinds)."'";
  $typex_txt=gettypex_bs($lakind, '.atc-', $preselected);
  echo '<div class="row"><div class="col-sm-2 col-xs-12"><label label-for="selectex" class="travell0">Показать в списке</label></div><div class="col-sm-10 col-xs-12">';
  echo "<select class=\"form-control\" name=\"extype[]\" id=\"selectex\" multiple=\"multiple\">$typex_txt</select>";
  $customscript .= '
  $(\'select[multiple]\').multiselect({
    enableClickableOptGroups: true,
    enableCollapsibleOptGroups: true,
    includeSelectAllOption: true,
    /* enableFiltering: true,
       filterBehavior: \'text\',
       buttonWidth: \'100%\', */
    nonSelectedText: \'Ничего не выбрано...\',
    nSelectedText: \' разделов\',
    allSelectedText: \'Все...\',
    selectAllText: \'Все\',
    onInitialized: function(select, container) {
      var selectval = $("#selectex").val();
      var selectedopts = "";
      if (selectval !== null) {
        selectedopts = selectval.join(\', \');
      }
      $(\'.atc-xxx\').hide();
      if (selectedopts) {
        $(selectedopts).show();
      }  
    },
    onDropdownHidden: function(event) {
      var selectval = $("#selectex").val();
      var selectedopts = "";
      if (selectval !== null) {
        selectedopts = selectval.join(\', \');
      }
      $(\'.atc-xxx\').hide();
      if (selectedopts) {
        $(selectedopts).show();
      }  
    }
  });
  ';
  echo '</div></div>';

}  
//
//  блок с объектами
//
echo '<div class=row><div class="col-md-12 col-xs-12">';
foreach ($cards as $card) {
  if ($card["kind"]) {
    $local = "atc-".str_replace(',', ' atc-', $card["kind"]);
  }
  else {
    $local = "";
  }
  echo '<div class="row atc-xxx '.$local.'" '.$card["mk_type"].'><div class="col-md-12 col-xs-12 exclinkbutton autocardw"';
  if ($forum_user_id) {
    if ($card["mark"]) {
      echo ' style="opacity:0.8" ';
    }
  }
  echo '>';
  echo $card["mk_content"].$card["mk_rating"];
  if ($card["rek"]) {
    $s_rek = "<img src='/jpg/bigpartner.gif' width=11 height=11 title=\"Партнер сайта\" alt=\"Партнер сайта\">&nbsp;";
  }
  else {
    $s_rek = "";
  }
  if (($forum_user_id>1)) {
    echo '<div class=row><div class="col-md-6 col-xs-12"><h3><a href="/';
    if ($card["mainid"]>=0) {
       echo 'otklik.php';
    }
    else {
       echo 'group.php';
    }
    echo '/'. $card["ex"]. '">' . $s_rek . $card["ename"] . '</a></h3></div>';
    echo "\n<div class=\"col-md-6 col-xs-12 pull-right\" id=\"basket".$card["ex"]."\"><a class=\"travell5r\" onclick='BasketAdd(".$card["ex"].",".$forum_user_basket.",\"basketadd".$card["ex"]."\")'>Добавить в ".$forum_user_basket_name."</a></div></div>\n";
  }
  else {
    echo '<h3><a href="/';
    if ($card["mainid"]>=0) {
       echo 'otklik.php';
    }
    else {
       echo 'group.php';
    }
    echo '/'. $card["ex"]. '">' . $s_rek . $card["ename"] . '</a></h3>';
  }
  if ($forum_user_id) {
    if ($card["mark"]) {
      echo "<h4>Моя оценка: ".bs_stars($card["mark"])."</h4>\n";
    }
  }  
  echo '<div>';
  echo $card["foto"]."</div>\n";
  echo '<div class="tj t0">'.$card["descr_local"]; // descrip
  if ($card["coords"]) {
    echo '<p class="travell0"> Координаты: <strong>'.$card["coords"].'</strong></p>';
  }  
  echo '<p class="travell0"> Рейтинг: <strong>'.$card["wrate"].'</strong></p>';
  echo "</div>\n";
  echo '<div class="mt-2">';      
  if ($card["booking"]) {
    echo ' [Доступно бронирование]';
  }
  echo '</div>';
  if ($card["rek"]) {
    echo show_panel("Официальная информация:","h4",$card["rek"],1,0);
  }
  echo "</div>\n";
  if ($card["votes"]>0) {
    echo "<div class=\"col-md-12 col-xs-12 travell0\"><a class=travell5r onclick=\"ChTjsBs('".$card["ex"]."',this);\">Прочитать последние несколько откликов: <img border=0 alt=\">\" src=\"/jpg/plus.gif\"></a><div id=\"o-".$card["ex"]."\"></div></div>\n";
  }
  echo "</div>\n";
}
echo "</div></div>\n"; // конец 

//include('include/footprint_bs.php');
$_totop = 1;
echo "</div>\n";
include('include/footer_bs.php');

?>
