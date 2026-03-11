<?php
include_once('include/config.php');
include_once('include/func.php');
include_once('include/head00.php');
$bootstrap=1; //это новый дизайн

if (preg_match("/^\/(\d+)$/",$_pin,$exa)) {
 $town = $exa[1];
}
else {
 $town = $_REQUEST["town"];
 $town = (int) mysafestring($town);
}

$meta_type = $_REQUEST["type"]; // НИКОГДА не надо передавать строковые кириллические параметры!
if (!($meta_type)) {
 $meta_type = "all";
}

$meta_types=getextypes(); //виды достопримов
$sort_types=sortextypes(); //отсортированные виды достопримов
$meta_key="";
$kmladd = "";
$coord = "";

// информация по городу в целом
$sql="select t.hide as thide, t.name as name, t.nameto as nameto, t.namefrom as namefrom, t.namepo as namepo, t.namearound as namearound, t.booking as booking, "
        . "t.descrip as description, t.area as id_area, t.Nshirota as Nshirota, t.Edolgota as Edolgota  from towns t where t.id='$town'";
$result = $dbi->query($sql);

$decrip = gettowntext($town); //авторское описание города

if($myrow = mysqli_fetch_array($result)) {
   $searchurl="/test.php?tid=$town&amp;adiff=$defdiff&amp;thistid=1&amp;";
   $s_name_arr = preg_split ("/\//" , $myrow["name"]);
   $s_name = $s_name_arr[0];
   $s_name2 = $s_name_arr[1];
   if ($s_name == "") {$s_name =  $myrow["name"];}
   $s_booking = $myrow["booking"]; //есть ссылка на общий букинг
   $s_nameto = $myrow["nameto"];
   if ($s_nameto == "") {$s_nameto = "город ".$s_name;}
   $s_namefrom = $myrow["namefrom"];
   if ($s_namefrom == "") {$s_namefrom = "города ".$s_name;}
   $s_namepo = $myrow["namepo"];
   if ($s_namepo == "") {$s_namepo = "городу ".$s_name;}
   $s_namearound = $myrow["namearound"];
   if ($s_namearound == "") {$s_namearound = "городом ".$s_name;}
   $s_area = $myrow["id_area"];
   $t_hide = $myrow["thide"];
   $Nshirota = (float)$myrow["Nshirota"];
   $Edolgota = (float)$myrow["Edolgota"];
   $ngrv = degreestotext($Nshirota);
   $egrv = degreestotext($Edolgota);
   if (!($decrip)) {$decrip = $myrow["description"];}
   if (($ngrv) and ($egrv)) {
      $nlat = $Nshirota;
      $nlng = $Edolgota;
      $coord = "N$ngrv, E$egrv";
      $metacoord = "<meta name=\"geo.position\" content=\"$Nshirota; $Edolgota\">\n<meta name=\"ICBM\" content=\"$Nshirota, $Edolgota\">";
   }
   //если в городе признак 2 редирект на отклик
   if ($t_hide == 2) {
       $e_id = getextown($town);
       $newuri = "$sitename/otklik.php/" . $e_id;
       Header( "HTTP/1.1 301 Moved Permanently" );
       Header( "Location: $newuri" );
       exit;
    }
 $a_name = getareaname($s_area); //название области для ссылки
 $title= $s_name ;
 //$weather = "weather/".$town.".html"; пока погоду не показываем
 
 $ex_forms = ""; //строка вывода объектов пока пуста
 $metadesc = "$s_name ($a_name): достопримечательности, гостиницы, интерактивная карта города, полезная информация для поездки в $s_nameto"; //описание для страницы города
 $metatags = "$a_name $s_name";
 $r_types=getrtypes(); //имена типов достопримов (Достопримечательности, ...)
 $r_typesdesc=getrtypesdesc(); //описание типов достопримов (Что посмотреть, ...)
 
 //выгружаем все типы достопримов города
 /* $sql1 ="select id,type,kind from exciting where town='$town'";
 $result1 = $dbi->query($sql1);
 $r_excitings=array(); //Достопримичательности
 $d_excitings=array(); //Что посмотреть
 $c_excitings=array(); //Кол-во
 $l_excitings=array();*/
 
 //собираем массив из типов достопримов по городу - с кол-вом, названием, описанием, нужной сортировкой
 $sql_type="select count(*) as cnt, e.type, t.name, t.descrip, t.srtfld from exciting e, type t where e.town='$town' and t.id=e.type group by 2";
 $result_type = $dbi->query($sql_type);
 $ar_type=array();
 while ($myrow = mysqli_fetch_array($result_type)) {
    $id=$myrow['srtfld'];   
    $ar_type[$id]=$myrow;
 }  
   
 /* пока не знаю зачем это было нужно
  if ($s_booking) {
  $r_excitings[2]=$r_types[2];
  $c_excitings[2]="0";
 }
  */
  
 while ($myrow1 = mysqli_fetch_array($result1)) { //идем по всем объектам города
   $exc_id = $myrow1["id"]; //объект
   $type_id = $myrow1["type"]; //тип
   $kind_list = $myrow1["kind"]; //вид
   $kind_arr = explode(",",$kind_list); //список видов достопримов (Архитектура, Природа и т.д.)
   while($kind_item=each($kind_arr)) {
         $kind_id = $kind_item["value"];  //вид код
         $kind_name = $meta_types[$kind_id]; //вид название
         if ($kind_name) {$town_types[$kind_name] = $kind_name;}
         $r_excitings[$type_id] = $r_typename[$type_id]; 
         $d_excitings[$type_id] = $r_typedesc[$type_id]; 
         $c_excitings[$type_id] = $c_excitings[$type_id] + 1; 
   }
 }
 
 reset($r_types);
 
 $bookingtown = "";
 $ex_header = "";
 $addhead = "";

 //дополниельная инфа
 $doplist = ""; 
 $doplist .=  "<h3>Интересные места рядом с ".$s_namearound."</h3>";
 $doplist .=  "<p class=travell0>Что посмотреть в радиусе ".$defdiff." км от города. Переход на страницу достопримечательностей и прочих мест в окрестности ".$s_namefrom.".</p>\n";
 
 // поиск объектов в окрестности
 while(list($type_id,$type_name)=each($r_types)) {
   if (($type_id == 1) or ($type_id == 6)) {
    $doplist .=  "<p class=travell5><a class=travell5n target=_blank href=\"/test.php?tid=$town&amp;extype[]=$type_id&amp;adiff=$defdiff&amp;thistid=1&amp;psubmit=Найти\">$type_name</a></p>\n";
   }
   $bookingtext = "";
   if (($type_id==2) && ($s_booking)) { 
       $bookingtext = "<div class=\"travell5\">" .
       "<a class=travell5n rel=\"nofollow\" target=_blank href=\"$s_booking?aid=337822\">Забронировать гостиницу $s_namefrom на сайте&nbsp;";        
       if ($_text_mode != 1) {
           $bookingtext .= "<img border=0 src='/jpg/booking_small.gif' width=89 height=10 alt=\"Booking.com\" title=\"Booking.com\"></a>\n";
       } else {
           $bookingtext .= "Booking.com</a>\n";
       }
   }
   if ($r_excitings[$type_id]) {
      if ($c_excitings[$type_id]) {
          if ($ex_header) {
              $ex_header .= ", " . strtolower($type_name);
          } else {
              $ex_header .= strtolower($type_name);
          }
          if ($_salp_mode) {
              $sqladds = "loc asc";
          }  else {
              $sqladds = "wrate desc, loc asc";
          }
          $sql_e="select *,
             Right( name, (length(name) - locate('\"', name)) ) AS loc
             from exciting
             where town='$town' and type='$type_id'
             order by $sqladds";
          $ctype_id=$type_id;
          $ctype_name=$type_name;
          $ctype_desc=$type_desc;
          include("include/town_new.php");
     }  else { // для гостиниц линк на букинг по городу
          if (($type_id==2) && ($s_booking)) { //
             $ex_forms .= "<div class=\"bookingtext\">" . $bookingtext . "</div>";
          }
     }
   }
 } //

 $ex_forms .=  "\n";
 $exlist="";
 $photocount=countfototown($town);
 $photoimg=getfototown($town,3);

 if ($coord) { }
 if ($photoimg) { }
 
 $metadesc = str_replace("\n",' ',$metadesc);
 $metadesc = str_replace("\r",' ',$metadesc);
 $metadesc = str_replace('"',' ',strip_tags($metadesc));
 
 if ($_gmap_mode==9) { // не показывать
     $dispgmap = "";
 } elseif($_gmap_mode==1) { // osm
     $dispgmap = 1;   
 } elseif($_gmap_mode==2) { // yandex
     $dispgmap = 2;   
 } else { // google
     $dispgmap = 3;   
 }
 
 $colorbox = 1;

 //вывод  на экран
 include('include/header_bs.php');
 $mapiframe = "";
 $customscript= "";
 $photoframe= "";
 if ($coord) {
  $kml = "/kml.php/t/$town";
  $mapiframe  = "<div id=gmapv3 class=\"embed-responsive\" style=\"width:100%; height:30vw; min-height:350px; display:none;\">&nbsp;</div>";
  $mapiframe .= "<a target=_blank href=\"/map.php/t/$town\">Открыть карту в новом окне</a><br>";
  $customscript .= "  initmapv".$dispgmap."(\"".$kml.$kmladd.".kml\",null,$nlat,$nlng);\n";
 }
 $ownfoto = countownfototown($town);
 if (($photocount) or ($ownfoto)) {
  $fotoframe = "<p class=travel10>$photoimg</p>\n<p class=travel10><b>Все фотографии ($photocount фото) достопримечательностей</b> города можно посмотреть, перейдя в фотоархив в раздел <a class=travell5n href='/$_fotoalbum/town/$town'>Фотографии $s_namefrom</a></p>\n";
  if ($ownfoto > 0) {
    $fotoframe .= "<p class=travell5><a class=travell5n target=_blank href='/fotoman.php?utown=$town'>Собственные фотографии $s_namefrom</a>: $ownfoto</p>\n";
  }
 }

 ?>
<div class="container-fluid townpage" style="width:100%;"> <!--начало вывода страницы города -->

 <?php
 echo "<h1>$s_name</h1>";
 if (($a_name <> "")||($s_name2 <> "")) {
     if ($s_name2 <> "") { echo "$s_name2, ";  }
     if  ($s_area>0) {
          $hreffirst='/areas.php';
          $namefirst='Области и регионы России';
     } else {
          $hreffirst='/countries.php';
          $namefirst='Зарубежные страны'; 
     }
     echo '<ol class="breadcrumb">';
     echo '<li><a href="' . $hreffirst . '">' . $namefirst . '</a></li>';
     echo '<li><a href="/area.php/' . $s_area . '">' . $a_name . '</a></li>';
     echo '</ol>';
    
 }
 

 if ($decrip) {
   ?>
    <div class="row">
      <div class="col-md-12">
        <div class="col-md-6 col-xs-12 pull-right">
          <div class="panel">
            <div class="panel-heading">
              <button type="button" class="btn btn-xs btn-info pull-right" data-toggle="collapse" data-target="#collapsemap">
                <span class="glyphicon glyphicon-chevron-up"></span>
              </button>
              <h3 class="panel-title">Карта <?php echo $s_namefrom; ?> и окрестностей</h3>
            </div>
            <div id="collapsemap" class="panel-collapse collapse in tocollapse">
              <div class="panel-body">
                <?php echo $mapiframe; ?>
              </div>  
            </div>
          </div>  
        </div>
        <p class="travell0"> <?php echo $decrip; ?></p>
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
            <h3 class="panel-title">Фотографии <?php echo $s_namefrom; ?></h3>
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
            <h3 class="panel-title">Карта <?php echo $s_namefrom; ?> и окрестностей</h3>
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
  
// левая часть
 $strleft = '<div class=\"townleft\">';
 //echo $ex_forms;
 ksort($ar_type);
 foreach ($ar_type as $type) {
    $strex = $strex . '<h2>' . $type['descrip'] . '</h2>';
    if ($type['type'] == 2) {
      $strex = $strex .$bookingtext;        
     }
    if ($type['cnt'] >= 25) {$objects=8;}
        elseif ($type['cnt'] >= 15) {$objects=5;}
           elseif ($type['cnt'] >= 5 ) {$objects=3;}
            else  {$objects=$type['cnt'];}
    //отбираем объекты в нужном количестве и качестве         
    $sql_ex="select e.* from exciting e where e.town='$town' and e.type='" . $type['type'] ."' order by e.wrate desc limit $objects";
    //echo $sql_ex;
    $result_ex = $dbi->query($sql_ex);
   // $ar_ex=array();
    while ($myrow = mysqli_fetch_array($result_ex)) {
       $id=$myrow['id'];   
       
    }  
}
     
 //}
 $strleft = $strleft .  $strex . '</div>';   
 echo $strleft;
 
 
 echo "<h2>Полезная информация для путешествующих в $s_nameto</a></h2>\n"; 
 $r_reports0="";
 $sql_r = "select rid from t_reports where tid=$town";
 $result_r = $dbi->query($sql_r);
 if($myrow_r = mysqli_fetch_array($result_r)) {
  echo "<h3>Отчеты о поездках в $s_nameto</h3>\n";
  echo "<p class=travell0>Здесь будут 3 последних отчета и кнопка - посмотреть больше с переходом уже в отчеты</p>\n";
  $r_reports0="Y";
 }
 $r_reports1="";
 $has_trace = 0;
 $traces = "";
 $sql_tr = "SELECT DISTINCT t.id AS trace_id, t.name AS trace, t.of_name as of_name FROM trace t, far_tr ft WHERE ft.town = '$town' AND t.id = ft.trace order by t.of_name";
 $result_tr = $dbi->query($sql_tr);

 if($myrow_tr = mysqli_fetch_array($result_tr)) {
  if (mysqli_num_rows($result_tr) > 2) {
  // $traces .= "<p class=travell5><a class=travell5n onclick=\"ChToggle('divxtr',this);\">Трассы, проходящие через $s_nameto <img border=0 alt=\">\" src=\"/jpg/plus.gif\"></a></p>";
  // $traces .= "<table id=divxtr style='visibility:hidden;display:none;' border=0 width='95%' cellpadding=0><tbody class=travell0>";
       $traces .= "<h3>Трассы, проходящие через $s_nameto: </h3>\n";
  }
  else {
   $traces .= "<h3>Трассы, проходящие через $s_nameto: </h3>\n";
  }
  do {
   $has_trace = 1;
   $s_trace = $myrow_tr["trace"];
   $s_traceid = $myrow_tr["trace_id"];
   $s_on = $myrow_tr["of_name"];
   $traces .= "<a class=travell5r href=\"/trace.php/$s_traceid\">$s_on $s_trace </a>";
  } while($myrow_tr = mysqli_fetch_array($result_tr));
 }


 if ($t_hide == 0) {
  if ($has_trace) {
    echo '<h3>Проложить маршрут из ' .$s_namefrom. '</h3>' ; 
   //echo "<p class=travell5><a class=travell5n href='/ch_trip.php?trip=t$town&amp;type=r&amp;view=t'>Проложить маршрут из $s_namefrom</a></p>";
   echo "<p class=travell0>Перейдя по ссылке выше Вы попадете на страницу построителя маршрута со стартом в городе $s_name.<br></p>\n";
   }

  $sql2 = "select max(date) as news_date from news where exciting='t$town'";
  $result2 = $dbi->query($sql2);
  if($myrow2 = mysqli_fetch_array($result2)) {
   $news_date = $myrow2["news_date"];
  }
  else {
   $news_date = "";
  }

  if ($news_date <> "") {
   echo "<p class=travell5><a class=travell5n href='/news.php?tn=$town'>Архив новостей по $s_namepo</a></p>";
  }
 }

    echo $traces;
    echo $doplist; //дополнительная информация


 include('include/formexi.php');
 if ($coord) {
  echo "<div class=\"t14 tb tc\">\n";
  include('include/kml.php');
  echo "</div>\n";
 }
 echo "</div>"; //конец вывода страницы города
 include('include/footer_bs.php');
 }
else {
 include("404.php");
}
?>
