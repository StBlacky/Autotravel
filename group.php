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

if (preg_match("/^\/(\d+)$/",$_pin,$exa)) {
 $gr = $exa[1];
}

if (!($gr)) {
  include('404.php');
  exit;
}

$canon = "/group.php/".$gr;

// заголовок и фото от группы.

//выбираем информацию про объект
$coord="";
$sqlex = "select e.id as eid, e.ffile as ffile, e.name as exciting, e.descrip as description, e.booking as booking,
         e.town as town, e.type as type, e.mainid,
         e.wrate as ratingvalue, e.rate as ratingall, e.votes as ratingcount, type.name as typename
         from exciting e, towns t, type where e.id = '$gr' and e.type = type.id and t.id = e.town and e.mainid<0";
$resultex = $dbi->query($sqlex);

$s_town = "";
$t_coord = "";

$ex = 0;
$s_ex="Нет названия";
$s_descr="Нет описания";
$typename = "";
$town=0;
$et=1;
$s_booking = "";
$main_ex = 0;

if($resultex) {
  if (mysqli_num_rows($resultex)>0) {
    $myrowex = mysqli_fetch_array($resultex);
    $gr = $myrowex["eid"]; //нашлось!
    $main_ex = $myrowex["mainid"]; //если не 0 то это была группа 
    $s_ex = $myrowex["exciting"]; //название объекта
    $ffile = $myrowex["ffile"];
    $outd = $myrowex["description"]; //описание объекта
    $typename = $myrowex["typename"];
    $town = $myrowex["town"];
    $ratingvalue = $myrowex["ratingvalue"];
    $ratingall = $myrowex["ratingall"];
    $ratingcount = $myrowex["ratingcount"];
  }  
} 


if (!($gr)) {
  include('404.php');
  exit;
}

$t_r = split_descr($outd); //описание объекта

$descr_gr = display_t_r($t_r, "tl travell0t",1);        
// пользовательское описание
$ex_descr = getextext($gr);
if ($ex_descr) {
  $descr_gr .= "<div width=95% class=\"tm10 ttb ttr\"><p class=\"tp10\">$ex_descr</p></div>";
}

//  htmlentities ( $string , ENT_HTML401 , "UTF-8" , 0 )

$metafbdescription = htmlspecialchars(strip_tags(htmlspecialchars_decode($t_r[0])),ENT_QUOTES|ENT_HTML401 , "UTF-8" , 0 );


//проходим по записям для вывода
if ($_salp_mode) {
  $sqladd = "";
}
else {
  $sqladd = " wrate desc, ";
}
$sql="select e.name as name, e.ffile as ffile, e.descrip as description, e.Nshirota as shirota,
  e.Edolgota as dolgota, e.id as id, e.booking as booking, e.mainid, e.town as town,
  e.votes as votes, e.rate as rate, e.type as etype, e.kind as ekind,
  Right( e.name, length( e.name ) - locate( '\"', e.name )) AS loc,
  e.wrate as wrate
  from exciting e where e.mainid=$gr
  order by $sqladd loc asc";

$result = $dbi->query($sql);
$cnt = $result->num_rows;

if (!($cnt)) {
    $newuri = "/cube.php";
    Header( "HTTP/1.1 301 Moved Permanently" );
    Header( "Location: $newuri" );
    exit;
}


$cards = array();
$ii = 0;
$flagresult = "";

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
    $cards[$ii]["descrip"] = $myrow["description"];
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
      $cards[$ii]["foto"]=get1fotoex_bs($s_id,1,0,$myrow["ffile"]);
    }  
    //}
    $t_r = split_descr($myrow["description"]); //описание объекта
    $cards[$ii]["descr_local"] = display_t_r($t_r, "tl travell0t",0);
    $cards[$ii]["mk_type"] = "";
    $cards[$ii]["mk_content"]  = "";
    $s_ex0 = htmlspecialchars(strip_tags(htmlspecialchars_decode($myrow["name"])),ENT_QUOTES |ENT_HTML401 , "UTF-8" , 0 );
    $ratingvalue = $myrow["wrate"];
    $ratingcount = $myrow["votes"];
    if ($ratingvalue <1) {
      $ratingvalue=1;
    }
    $bestRating = bs_calc_rating($ratingcount,$ratingvalue);
    $cards[$ii]["mk_rating"] = "";
    $cards[$ii]["bestrating"] = $bestRating;
    $cards[$ii]["mark"]="";
    $ii++;
  } while($myrow = mysqli_fetch_array($result));
  mysqli_free_result($result);
}


$t_name = gettownnamearr($town);
$town_name = $t_name[1]; //НАЗВАНИЕ (ОБЛАСТЬ)

$searchurl="/test.php?tid=$town&amp;adiff=$defdiff&amp;thistid=1&amp;";

$title_h=$s_ex;
$title = $s_ex;

$metatags = $title_h;
$metadesc = str_replace('"',' ',$metatags);

$metadesc = $metadesc . ", адреса, режим работы, описания, фотографии, отзывы, интерактивная карта"; //описание для поисковиков и выдачи
$metadesc = str_replace("\n",' ',$metadesc);
$metadesc = str_replace("\r",' ',$metadesc);
$metadesc = str_replace('"',' ',strip_tags($metadesc));

include("include/header_bs.php");

$out = "";
$outmap = "";
$out2 = "";

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
    $mapiframe .= kml_display('/kml.php/t/'.$town);
    $customscripttop .= "function showmap() { initmapv".$dispgmap."('/kml.php?ids=".$flagresult."',null,$nlat,$nlng);\n }\n";
  }
}

echo '<div class="container-fluid" style="width: 100%">'; //начало раздела 

echo '<ol class="breadcrumb">';
if ($t_name[8]<0) {
  echo "<li><a href=\"/countries.php\">Зарубежные страны</a></li>";
}
else {
  echo "<li><a href=\"/areas.php\">Области России</a></li>";  
}
echo "<li><a href=\"/area.php/$t_name[8]\">$t_name[2]</a></li>";
if ($t_name[10]) {
  echo "<li><a href=\"/gorod/".$t_name[10].".html\">$t_name[3]</a></li>";
}
else {
  echo "<li><a href=\"/town.php/$town\">$t_name[3]</a></li>";
}
echo "</ol>\n";

if (($outd) && ($mapiframe)) {
  echo '<div class="row">';
  //echo show_half_panel($title_h,"h1",$outd,1,null,6);
  echo show_half_panel($title_h,"h1",$descr_gr,1,null,6);
  echo show_half_panel('Интерактивная карта',"h3",$mapiframe,0,"maphidden",6);
  echo '</div>';
}
else {
  echo "<h1>".$title_h."</h1>";
  echo '<div class="row">';
  if ($outd) {
    echo '<div class="col-md-12 col-xs-12">';
    echo $outd;
    echo '</div>';
  }
  if ($mapiframe) {
    echo show_panel("Интерактивная карта","h3",$mapiframe,0,"maphidden");
  }
  echo '</div>';
}

echo '<div class="row">';
echo '<div class="col-md-12">';
echo '<div class="autocard-masonry">'; // ← Masonry контейнер

foreach ($cards as $card) {

    echo '<div class="autocard">';  // ← Masonry элемент

        echo '<h3><a href="/otklik.php/'.$card["ex"].'">'.$card["ename"].'</a></h3>';

        echo '<div class="ac-photo">'.$card["foto"].'</div>';

        echo '<div class="ac-text">';
        echo $card["descr_local"];

        if ($card["coords"]) {
            echo '<p class="travell0">Координаты: <strong>'.$card["coords"].'</strong></p>';
        }

        echo '<p class="travell0">Рейтинг: <strong>'.$card["wrate"].'</strong></p>';
        echo '</div>';

    echo '</div>'; // .autocard
}

echo '</div>'; // .autocard-masonry
echo '</div>'; // col
echo '</div>'; // row


echo "</div>\n"; // конец container-fluid

//include('include/footprint_bs.php');
$_totop = 1;

include('include/footer_bs.php');
?>
