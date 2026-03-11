<?php
// forumsess надо звать в самом начале. А то переменные переопределятся.
// Но внутри ничего не echo-ить, а то заголовки и куки покривеют.
include_once('include/func.php');
include_once('include/func_bs.php');
include_once('include/head00.php');
include_once('include/meta.php'); // сео настройки

if (!function_exists('normalize_rating')) {
    function normalize_rating($r) {
        if ($r == 0) return 3.0;
        if ($r < 0) return 3 - atan(abs($r)/20) * 2 / M_PI;
        return 3 + atan($r/20) * 2 / M_PI;
    }
}
    
if ($ratingcount > 0 && $ratingvalue != 0) {
    $ratingAvg  = $ratingvalue / $ratingcount;
    $ratingNorm = max(1, min(5, round(normalize_rating($ratingAvg), 1))); // ограничиваем 1–5

    $json_ld = [
        "@context" => "https://schema.org",
        "@type"    => "TouristAttraction",
        "name"     => htmlspecialchars($s_ex0, ENT_QUOTES, "UTF-8"),
        "image"    => htmlspecialchars($metafbimage, ENT_QUOTES, "UTF-8"),
        "description" => htmlspecialchars($metadesc, ENT_QUOTES, "UTF-8"),
        "url"      => "https://autotravel.ru/otklik.php/$ex",
        "aggregateRating" => [
            "@type"        => "AggregateRating",
            "ratingValue"  => (string)$ratingNorm,
            "ratingCount"  => (string)$ratingcount,
            "bestRating"   => "5",
            "worstRating"  => "1"
        ],
        "itemReviewed" => [
            "@type" => "Place", // ← теперь корректно
            "name"  => htmlspecialchars($s_ex0, ENT_QUOTES, "UTF-8"),
            "url"   => "https://autotravel.ru/otklik.php/$ex"
        ],
        "publisher" => [
            "@type" => "Organization",
            "name"  => "Autotravel.ru",
            "url"   => "https://autotravel.ru"
        ]
    ];

    echo "\n<script type=\"application/ld+json\">\n" .
         json_encode($json_ld, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) .
         "\n</script>\n";
}



$bootstrap = 1;
// теперь всё как обычно

if (preg_match("/^\/(\d+)$/",$_pin,$exa)) {
  $ex = $exa[1];
}
else {
  $ex = (int) $_REQUEST["ex"];  
}

if (!($ex)) {
  include('404.php');
  exit;
}

if ($_text_mode) {
  $_gmap_mode=9;
}

// пользовательское описание
$ex_descr = getextext($ex);

if (ifmoderated($ex)>0) {
  if ($forum_user_weight < 1) {
    $_moderated = 1;      
  }
}

// автоловилка писателей в otklikm
if ($_bad_count>1) {
  $_moderated = 2;  
}

//выбираем информацию про объект
$coord="";
$sqlex = "select e.id as eid, e.ffile as ffile, e.name as exciting, e.descrip as description, e.booking as booking,
         e.town as town, e.type as type, e.Nshirota as shirota, e.exact as exact,
         t.Nshirota as tshirota, t.Edolgota as tdolgota, e.mainid,
         e.Edolgota as dolgota, e.wrate as ratingvalue, e.rate as ratingall, e.votes as ratingcount,
         type.name as typename, type.allname as typeallname
         from exciting e, towns t, type where e.id = '$ex' and e.type = type.id and t.id = e.town";
$resultex = $dbi->query($sqlex);

$s_town = "";
$t_coord = "";

$ex = 0;
$s_ex="Нет названия";
$s_descr="Нет описания";
$typename = "";
$typeallname = "";
$town=0;
$et=1;
$s_booking = "";
$main_ex = 0;

if($resultex) {
  if (mysqli_num_rows($resultex)>0) {
    $myrowex = mysqli_fetch_array($resultex);
    $ex = $myrowex["eid"]; //нашлось!
    $main_ex = $myrowex["mainid"]; //если не 0 то это была группа 
    $s_ex = $myrowex["exciting"]; //название объекта
    $Nshirota = (float)$myrowex["shirota"];
    $Edolgota = (float)$myrowex["dolgota"];
    $tlat = (float)$myrowex["tshirota"];
    $tlng = (float)$myrowex["tdolgota"];
    $exact = (int)$myrowex["exact"];
    $ffile = $myrowex["ffile"];
    $s_descr = $myrowex["description"]; //описание объекта
    
    // --- извлекаем адрес и режим работы из описания ---
    $s_addr  = '';
    $s_hours = '';
    // убираем HTML и BB-код для анализа
     $plain_descr = strip_tags($s_descr);

// адрес: ловит [b]Адрес:[/b] и варианты вроде "Адрес:" или "Адрес -"
if (preg_match('/Адрес\s*[:\-–]\s*([^\[\]<\n\r]+?)(?=\[b\]|$)/ui', $plain_descr, $m)) {
    $s_addr = trim($m[1]);
}

// режим работы: ловит [b]Режим работы:[/b] или "Режим работы:"
if (preg_match('/Режим\s+работы\s*[:\-–]\s*([^\[\]<\n\r]+)/ui', $plain_descr, $m)) {
    $s_hours = trim($m[1]);
}

    $typename = $myrowex["typename"];
    $typeallname = $myrowex["typeallname"];
    $town = $myrowex["town"];
    $ratingvalue = $myrowex["ratingvalue"];
    $ratingall = $myrowex["ratingall"];
    $ratingcount = $myrowex["ratingcount"];
    $et = $myrowex["type"]; //категория объекта 2 - гостиницы, 3 - еда
    $ngrv = degreestotext($Nshirota);
    $egrv = degreestotext($Edolgota);
    if (($ngrv) and ($egrv)) {
      $nlat = $Nshirota; 
      $nlng = $Edolgota;
      if ($egrv<0) {
        $coord = "N$ngrv, W".str_replace("-","",$egrv);
      } 
      else {
        $coord = "N$ngrv, E$egrv";       
      }
      $metacoord = "<meta name=\"geo.position\" content=\"$Nshirota; $Edolgota\">\n<meta name=\"ICBM\" content=\"$Nshirota, $Edolgota\">";
    }
    $btype="ex";
    if ($et==2) {
      $btype="ho";
      $s_booking = $myrowex["booking"];
    }
    if ($et==3) {
      $btype="op";
    }
    $tt = gettownnamearr($town);
    $s_town = $tt[0]; //название города
    $t_url = $tt[10]; // URL
    $t_namefrom = $tt[6]; // родительный падеж
    $s_areaname = $tt[2]; //название области
    $s_area = $tt[8]; //id области
    $t_hide = $tt[9]; // тип города
    if  ($s_area>0) { //для крошек 
      $hreffirst='/areas.php';
      $namefirst='Области и регионы России';
    } 
    else {
      $hreffirst='/countries.php';
      $namefirst='Зарубежные страны'; 
    }
  }  
} 

if (!($ex)) {
  include('404.php');
  exit;
}

if ($main_ex<0) {
  $newuri = "/group.php/" . $ex;
  Header( "HTTP/1.1 301 Moved Permanently" );
  Header( "Location: $newuri" );
  exit;
}

//зачем-то ближайшую дату новости по объекту
$sqlnd = "select max(date) as news_date from news where exciting='e$ex'";
$resultnd = $dbi->query($sqlnd);
if($myrownd = mysqli_fetch_array($resultnd)) {
  $news_date = $myrownd["news_date"];
}
else {
  $news_date = "";
}

//блок партнера
$s_rek = getreklama($ex,1);
$s_ex0 = $s_ex;
//$metafbtitle = htmlspecialchars(strip_tags(htmlspecialchars_decode($s_ex)) . " (" . $s_town ." - " . $s_areaname .")",ENT_QUOTES |ENT_HTML401 , "UTF-8" , 0 );
//$title = $metafbtitle;

if ($s_rek <> "") {
  $s_ex = "<img src='/jpg/bigpartner.gif' width=11 height=11 title=\"Партнер сайта\" alt=\"Партнер сайта\">&nbsp;&nbsp;".$s_ex;
}

$customscript = "";
$customscripttop = "";

$searchurl="/test.php?tid=$town&amp;adiff=$defdiff&amp;thistid=1&amp;";

$t_r = split_descr($s_descr); //описание объекта
//  htmlentities ( $string , ENT_HTML401 , "UTF-8" , 0 )

//$metafbdescription = htmlspecialchars(strip_tags(htmlspecialchars_decode($t_r[0])),ENT_QUOTES|ENT_HTML401 , "UTF-8" , 0 );



// --- мета-блок для otklik.php ---

// Базовые значения
$metatags = str_replace('"',' ',"$s_town $s_ex0");
$canon = "/otklik.php/$ex";

// Фото: для og:image и меток "Фотографии"
if (!$_text_mode) {
    $photoimg = getfotoex($ex, 3, "", 1);
    $photocount = $photoimg["display"] + $photoimg["hidden"];
    if ($photocount) {
        if ($ffile) {
            $metafbimage = "https:{$sitename}/phalbum/{$ffile}.jpg";
        } else {
            $metafbimage = $photoimg["foto1"];
        }
        $metatags .= " Фотографии";
    }
} else {
    $photocountx = countfotoexc($ex);
}

// --- meta.php: универсальные метатеги ---
$__meta = build_meta('otklik', [
    'object_name' => $s_ex0,
    'town_name'   => $s_town,
    'area_name_r' => $s_areaname,
    'descrip'     => $ex_descr ?: $s_descr,
    'ffile'       => $ffile ?? ''
]);

$__ld = build_ld('otklik', [
    'object_name'  => $s_ex0,
    'town_name'    => $s_town,
    'area_name_r'  => $s_areaname,
    'desc'         => $metadesc,
    'ffile'        => $ffile ?? '',
    'Nshirota'     => $Nshirota ?? '',
    'Edolgota'     => $Edolgota ?? '',
    'addr'         => $s_addr ?? '',
    'ratingvalue'  => $ratingvalue ?? '',
    'ratingcount'  => $ratingcount ?? '',
    'openhours'    => $openhours ?? '',
    'mk_type'      => $mk_type ?? ''
]);
echo $__ld;


// Применяем результат meta.php
// --- применяем результат meta.php ---
if (!empty($__meta['title'])) {
    $title = $__meta['title'];
    $metafbtitle = $title; // синхронизация OG-title
}
if (!empty($__meta['desc'])) {
    $metadesc = $__meta['desc'];
    $metafbdescription = $metadesc; // синхронизация OG-description
}
if (!empty($__meta['ogimg'])) {
    $metafbimage = $__meta['ogimg'];
}




// Карта отображается независимо от meta.php
if ($_gmap_mode == 9) { 
    $dispgmap = "";
} elseif ($_gmap_mode == 1) {
    $dispgmap = 1; 
} elseif ($_gmap_mode == 2) {
    $dispgmap = 2;
} else {
    $dispgmap = 3;
}

// Вспомогательные флаги
$jplist    = 1;
$ratingjs  = 1;
$validator = 1;
$colorbox  = $_text_mode ? 0 : 1;

// --- конец блока мета ---
include('include/header_bs.php'); // должно стоять после всех мета-тегов

echo "\n<!-- |$_pin| -->\n";

// 
// хлебные крошки 
//
$breadcrumbs = [
  "@context" => "https://schema.org",
  "@type" => "BreadcrumbList",
  "itemListElement" => [
    ["@type" => "ListItem","position"=>1,"name"=>$namefirst,"item"=>"https://autotravel.ru".$hreffirst],
    ["@type" => "ListItem","position"=>2,"name"=>$s_areaname,"item"=>"https://autotravel.ru/area.php/".$s_area],
    ["@type" => "ListItem","position"=>3,"name"=>$s_town,"item"=>"https://autotravel.ru/town.php/".$town],
    ["@type" => "ListItem","position"=>4,"name"=>$s_ex0,"item"=>"https://autotravel.ru/otklik.php/".$ex]
  ]
];
echo "<script type='application/ld+json'>".json_encode($breadcrumbs,JSON_UNESCAPED_UNICODE)."</script>";



// --- микроразметка объекта (schema.org) ---
switch ($et) {
    case 1:  // достопримечательности
        switch (true) {
            // архитектура, крепости, дворцы, монастыри
            case in_array($type, [2, 3, 5, 6, 7, 9, 11, 15, 18, 29, 53]):
                $mk_type = ' itemscope itemtype="http://schema.org/LandmarksOrHistoricalBuildings" ';
                break;

            // религиозные объекты — храмы, монастыри, церкви, соборы
            case in_array($type, [2, 5, 6]): // уточнение для PlaceOfWorship
                $mk_type = ' itemscope itemtype="http://schema.org/PlaceOfWorship" ';
                break;

            // музеи всех направлений
            case in_array($type, [4, 10, 12, 16, 20, 21, 22, 23, 24, 28, 32, 33, 38, 39, 42, 43, 52, 54, 55, 56]):
                $mk_type = ' itemscope itemtype="http://schema.org/Museum" ';
                break;

            // памятники и скульптуры
            case in_array($type, [14, 9]):
                $mk_type = ' itemscope itemtype="http://schema.org/Monument" ';
                break;

            // природные объекты
            case in_array($type, [1, 8, 13, 25, 26, 27, 30, 31, 36]):
                $mk_type = ' itemscope itemtype="http://schema.org/NaturalFeature" ';
                break;

            // парки и улицы
            case in_array($type, [8, 15, 34]):
                $mk_type = ' itemscope itemtype="http://schema.org/Park" ';
                break;

            // руины и археология
            case in_array($type, [47, 48]):
                $mk_type = ' itemscope itemtype="http://schema.org/ArchaeologicalSite" ';
                break;

            // промышленные, технические объекты
            case in_array($type, [20, 21, 22, 49]):
                $mk_type = ' itemscope itemtype="http://schema.org/TouristAttraction" ';
                break;

            // детские и развлекательные
            case in_array($type, [40, 50, 51]):
                $mk_type = ' itemscope itemtype="http://schema.org/TouristAttraction" ';
                break;

            // панорамы и смотровые площадки
            case in_array($type, [34]):
                $mk_type = ' itemscope itemtype="http://schema.org/TouristDestination" ';
                break;

            default:
                $mk_type = ' itemscope itemtype="http://schema.org/TouristAttraction" ';
                break;
        }
        break;

    case 2:  // гостиницы
        $mk_type = ' itemscope itemtype="http://schema.org/Hotel" ';
        break;

    case 3:  // кафе, рестораны
        $mk_type = ' itemscope itemtype="http://schema.org/Restaurant" ';
        break;

    case 5:  // сувениры, промыслы
        $mk_type = ' itemscope itemtype="http://schema.org/Store" ';
        break;

    case 6:  // сервис
        $mk_type = ' itemscope itemtype="http://schema.org/Service" ';
        break;

    case 8:  // праздники и события
        $mk_type = ' itemscope itemtype="http://schema.org/Event" ';
        break;

    default:
        $mk_type = ' itemscope itemtype="http://schema.org/Place" ';
        break;
}



// координаты
$mk_geo = '';
if (!empty($Nshirota) && !empty($Edolgota)) {
    $mk_geo = '
    <div itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
        <meta itemprop="latitude" content="'.$Nshirota.'">
        <meta itemprop="longitude" content="'.$Edolgota.'">
    </div>';
}

// фото
$mk_photo = '';
if (!empty($ffile)) {
    $mk_photo = '<meta itemprop="image" content="https://'.$sitename.'/phalbum/'.$ffile.'.jpg">';
} elseif (!empty($photoimg["foto1"])) {
    $mk_photo = '<meta itemprop="image" content="'.$photoimg["foto1"].'">';
}

// рейтинг
$mk_rating = '';
if ($ratingcount > 0) {
    $bestRating = 5;
    $ratingValue = round(min($ratingvalue, $bestRating), 1);
    $mk_rating = '
    <div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
        <meta itemprop="ratingValue" content="'.$ratingValue.'">
        <meta itemprop="ratingCount" content="'.$ratingcount.'">
        <meta itemprop="bestRating" content="'.$bestRating.'">
    </div>';
}

// итог
$mk_content = '
<span itemprop="name" content="'.htmlspecialchars($s_ex0, ENT_QUOTES, "UTF-8").'"></span>
'.$mk_photo.'
'.$mk_geo.'
'.$mk_rating.'
'.(!empty($s_addr) ? '
<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
    <meta itemprop="streetAddress" content="'.htmlspecialchars($s_addr, ENT_QUOTES, "UTF-8").'">
</div>' : '').'
'.(!empty($s_hours) ? '
<meta itemprop="openingHours" content="'.htmlspecialchars($s_hours, ENT_QUOTES, "UTF-8").'">' : '').'
';



if (!empty($metadesc)) {
    $mk_content .= '<meta itemprop="description" content="'.htmlspecialchars($metadesc, ENT_QUOTES, "UTF-8").'">';
}


if ($ratingvalue <1) {
  $ratingvalue=1;
}
$bestRating = bs_calc_rating($ratingcount,$ratingvalue);
$highrating = $bestrating * $ratingcount;
// нормирую на "5" баллов
if ($highrating < $ratingvalue) {
  $ratingvalue = $highrating;  
}
$mk_rating .= "<span itemprop=\"ratingValue\"  content=\"$ratingvalue\"></span>";
$mk_rating .= "<span itemprop=\"ratingCount\" content=\"$ratingcount\"></span>";
$mk_rating .= "<span itemprop=\"bestRating\" content=\"$bestRating\"></span>";
$mk_rating .= "</div>";

//
// карта
//
$mapiframe = "";
$mapheader = "";
if ($coord) {
  if ($dispgmap) {  
    $kml = "/kml.php/e/$ex";
    $mapheader = "Карта окрестностей <span class=\"t13\">($coord)</span>";
    $mapiframe .= "<div align=center><font class=\"t13 tc\">\n".$coord;
    $mapiframe .= "</font></div>\n";
    $mapiframe .= "<div id=gmapv3 class=\"embed-responsive\" style=\"display:none;\">";
    if ($dispgmap == 3) {
      $mapiframe .= "Используется Google Maps, на части территории РФ может не работать из-за технических ошибок при блокировке провайдерами зарубежных сервисов. Может быть изменена в настройке сайта.";
    }
    $mapiframe .= "</div>";
    $mapiframe .= "<a target=_blank href=\"/map.php/e/$ex\">Открыть карту в новом окне</a><br>";
    $customscripttop .= "function showmap() {  initmapv".$dispgmap."(\"".$kml.$kmladd.".kml\",null,$nlat,$nlng);\n }\n";
  }  
}
else {
  if ($dispgmap) {
    $kml = "/kml.php/t/$town";
    if (($forum_user_rank) || ($forum_user_level)) {
      $mapiframe .= "<div align=center><font class=\"t13 tc\">\n".$coord;
      $mapiframe .= "</font></div>\n";
    }
    $mapheader = "$s_town. Карта окрестностей";
    $mapiframe .= "<div id=gmapv3 class=\"embed-responsive\" style=\"display:none;\">&nbsp;</div>";
    $mapiframe .= "<a target=_blank href=\"/map.php/t/$town\">Открыть карту в новом окне</a><br>";
    $customscript .= "function showmap() {  initmapv".$dispgmap."(\"".$kml.$kmladd.".kml\",null,$tlat,$tlng);\n}\n";    
  }
}

// 
// фотографии
//
include_once('include/getfotoex.php');
$fotoform = "";

if (!($_text_mode)) {
 if (($forum_user_level>0) or ($forum_user_rank>0)) { # сдублировано в fotoman.php
  if (!($fotodisplay)) {
    $fotodisplay = "<div id=\"fotocontent\"></div>\n";
  }
  $fotoform .= "<form name=\"foto\" id=\"foto\" method=\"post\" enctype=\"multipart/form-data\"  action=\"/fotoman.php\">\n";
  $fotoform .= "<input type=hidden name=utown value=$town>\n";
  $fotoform .= "<input type=hidden name=ret value=\"otklik_new\">\n";
  if ($ownfoto < $MAXFOTOS) {
    $fotoform .= "<input type=hidden name=mode value=1>\n";
  }
  else {
    $fotoform .= "<input type=hidden name=mode value=0>\n";
  }
  $fotoform .= "<input type=hidden name=exutown value=$ex>\n";
  $fotoform .= "<p class=travell0>Вы можете загрузить в фотоархив свои фотографии этого места</p>\n";
  $fotoform .= "<p class=travell0 style=\"display: inline-grid;\">\n";
  $fotoform .= '<label id="file-upload-label" for="file-upload" class="ttb2 tzh t14 ttr tp10">Выберите фотографию</label>';
  $fotoform .= '<input id="file-upload" type="file" name="userfile" style="display: none;" onchange="readIMG(this);"/>';
  $fotoform .= '<img id="imgpreview" src="#" style="display: none;" />';
  $fotoform .= "<INPUT class=\"ttb2 tzh t14 ttr tp10\" name=\"action\" type=\"submit\" id=\"imgsubmit\" style=\"display: none;\"  value='Загрузить на сайт >>'></p></form>\n";
  $customscript .= '
  $("form#foto").submit(
    function(e) {
      $(\'#file-upload-label\').hide();
      $(\'#fotomessage\').text(\'... подождите ...\');
      $(\'#fotomessage\').show();
      pseudo_ajax_form(\'#foto\',\'#fotocontent\',\'#fotocontent\',\'#file-upload-label\');
      $(\'#imgpreview\').hide();
      $(\'#imgsubmit\').hide();
    }
  ); 
  ';
 }
} 

//
// описание
//

$descr_local = display_t_r($t_r, "tl travell0t",1);        

//$descr_local .= $mk_rating;
if ($ex_descr) {
  $descr_local .= "<div width=95% class=\"tm10 ttb ttr\"><p class=\"tp10\">$ex_descr</p></div>";
}
//$descr_local .= "\n".$mk_content."\n"."</div>\n";
if (($forum_user_id>1)) {
  $descr_local .= "<div id=basketadd$ex  class=travell0><a class=travell5r onclick='BasketAdd($ex,$forum_user_basket,\"basketadd$ex\")'>Добавить в $forum_user_basket_name</a></div>\n";
}
//
if ($_user_is_admin == '1') {
  $descr_local .= "<div align=center><form name=editex action=\"/sitemgr.php\">\n";
  $descr_local .= "<input type=hidden name=admt value=\"excited\">";
  $descr_local .= "<input type=hidden name=excid value=\"$ex\">";
  $descr_local .= "<input type=hidden name=submitexn value=\"1\">";
  $descr_local .= "<input class=\"ttb2 tzh t14 ttr tp10\" type=submit value=\"Отредактировать объект\"></form></div>\n";
}
//
if($s_booking) {
  $descr_local .= "<!--noindex--><div align=center><form name=reservation action=\"$s_booking\" target=_blank>\n";
  //$descr_local .= "<input type=hidden name=aid value=337822>";
  $descr_local .= "<input class=\"ttb2 tzh t14 ttr tp10\" type=submit value=\"Забронировать онлайн на сайте Booking.COM\"></form></div><!--/noindex-->\n";
}

//
//
if ($news_date <> "") {
  $descr_local .= "<p class=travell0>Посмотреть <a class=travell5r href='/news.php?ex=$ex'>архив новостей по объекту $s_ex0</a></p>";
}

// 
// начало вывода
//
echo "<div class='container-fluid' style='width:100%;'>";

//соберем крошки
echo '<ol class="breadcrumb">';
echo '<li><a href="' . $hreffirst . '">' . $namefirst . '</a></li>';
echo '<li><a href="/area.php/' . $s_area . '">' . $s_areaname . '</a></li>';
if ($t_hide == 0) { //это для открытого города
   $counte = getexcount_bytown($town, $et);
   if ($t_url) {
     echo '<li><a href="/gorod/' . $t_url . '.html">' . $s_town . '</a></li>';
     if ($counte>4) {
       echo '<li><a href="/excite.php/' . $town . '/' . $et. '">' . $typeallname . ' ' . $t_namefrom . '</a></li>';
     }
     else {
       echo '<li>' . $typename. '</li>';
     }
   }
   else {
     echo '<li><a href="/town.php/' . $town . '">' . $s_town . '</a></li>';
     if ($counte>2) {
       echo '<li><a href="/excite.php/' . $town . '/' . $et. '">' . $typeallname . ' ' . $t_namefrom . '</a></li>';
     }
     else {
       echo '<li>' . $typename. '</li>';
     }
   }
}
else {
   echo '<li>' . $s_town . '</li>';
}
echo '</ol>';

//
// макет страницы:
// ==============================================================================
//          Описание                    //    Карта (свернута)
//      (в т.ч. пользовательское)       //
// ------------------------------------------------------------------------------
//        Описание от партнера (если есть)
// ------------------------------------------------------------------------------
//        Фотографии (могут быть скрытыми)
// ------------------------------------------------------------------------------
//        Реклама (если описания от партнера нет)       
// ==============================================================================
//        Отклики которые уже есть
// ------------------------------------------------------------------------------
//        Форма ввода/редактирования отклика
// ------------------------------------------------------------------------------
echo "\n<div ".$mk_type.">\n".$mk_content; // микроразметка
if ($dispgmap) {
  echo '<div class="row">';
  echo show_half_panel($s_ex,"h1",$descr_local,1,null,6);
  echo show_half_panel($mapheader,"h2",$mapiframe,0,"maphidden",6);
  echo '</div>';
}
else {
  echo show_panel($s_ex,"h1",$descr_local,1,null);
}
if ($s_rek <> "") {
  echo show_panel("Официальная информация:","h2",$s_rek,1,"objofficial");
}

if ($_text_mode) {
  if ($photocountx) {
    echo "<p class=travell0>Посмотреть фотоархив ($photocountx фотографий) в разделе <a target=\"_blank\" class=\"travell5r\" href='/$_fotoalbum/ex/$ex'>$s_ex: фотографии</a></p>\n";
  }    
}
else {
 if (($fotodisplay) || ($fotoform)) {
  if (($photoimg["display"] == 0) && ((!($fotoform)) || ($photoimg["hidden"] > 0))) { // фотки спрятаны, формы ввода нет
    echo show_panel("Фотографии","h2",$fotodisplay.$fotoform,0,"objfoto");
  }
  else {
    echo show_panel("Фотографии","h2",$fotodisplay.$fotoform,1,"objfoto");    
  }
 }
} 
echo "</div>\n";
if (!($_text_mode)) {
/* if ($s_rek == "") {
  $g_rek= '  
  <div width="100%" height="160px">  
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
  </div>
  ';
  echo show_panel("Реклама:","h4",$g_rek,1,"objreklama");
 }*/
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
  if ($forum_user_banned) {
    $_moderated = 2;
  }

  if ((preg_match("/href\=/i",$tmess)) or
    (preg_match("/https\:/i",$tmess)) or
    (preg_match("/https\:/i",$name)) or
    (preg_match("/http\:/i",$tmess)) or
    (preg_match("/http\:/i",$name)) or
    (preg_match("/http\:/i",$mail)) ) 
    {
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
      error_log('no_cookie');
    }
    elseif ($ex) {
      $attable = "otklik";
      if ($mark >5) {
        $_moderated = 1;
      }
      if ($mark <1) {
        $_moderated = 1;
      }
      if ($_moderated) {
        $attable = "otklikm";
      }
      $sql_r2 = "select descrip from $attable where exciting = $ex and cook = '$ctext'";
      $result_r2 = $dbi->query($sql_r2);
      if ($myrow_r2 = mysqli_fetch_array($result_r2)) {
        $descr_old = $myrow["descrip"];
        $sql_in = "update $attable set descrip = '$message', nick='$name',
                   mail= '$mail', mark= '$mark', ip = '$ipp', time = '$tim', archived = 0
                   where exciting=$ex and cook = '$ctext'";
        $sql_h = "insert into history (tblname,cook,ip,id,field,valueold,valuenew) values ('otklik','$ctext','$ipp',$ex,'descrip','$descr_old','$message')";
        error_log($sql_h);
        $dbi->query($sql_h);
      }
      else {
        $sql_in = "INSERT INTO $attable (exciting, nick, mail, descrip, mark, ip, time, archived, cook) "
              . "VALUES ('$ex', '$name','$mail', '$message', '$mark', '$ipp', '$tim', 0, '$ctext')";
      }
      error_log($sql_in);
      $result_in = $dbi->query($sql_in);
    }
    if ($_moderated == 1) {
      $error="Отзыв направлен на модерирование. Не нужно его дублировать. Спасибо.";
    }
    else {
      $error="";
    }
  }
  else {
    if ($error=="") {
      $error="Вы не все рассказали или не поставили оценку. Оценки без комментария не бывает, как и комментария без оценки. Исправьте.";
    }
    error_log($error);
  }
}
else {
  // это был просмотр: надо обновить статистику
  $dtarr = getdate(time());
  $sttime = mktime(0,0,0,$dtarr["mon"],1,$dtarr["year"]);
  $sql_in = "insert into stats_ex (ex, dt, count) values($ex,$sttime,1) ON DUPLICATE KEY UPDATE count=count+1";
  $result_in = $dbi->query($sql_in);
}
//
// 
//

$_has_mark = "";
$otkl_rep = bs_getotklik($ex);

if (!($_exct_mode == 0)) {
  echo "<p class=\"t13 tc\">";
  if ($_exct_mode > 0) {
    echo "Не забудьте: у Вас включен фильтр отзывов [только участников форума]";
  }
  if ($_exct_mode < 0) {
    echo "Не забудьте: у Вас отключено отображение отзывов ";
  }
  echo ". Отключается в меню <a class=travell5r href=/sitemgr.php>настроек сайта</a></p>";
}

$otkl_panel = "";

if (count($otkl_rep) > 0) {
  if ((count($otkl_rep) >10) and (!($_excv_mode))) {
    $customscript .= '
    $(\'#objopinions\').jplist({				
	itemsBox: \'.o_list\' 
	,itemPath: \'.o_block\' 
	,panelPath: \'.jplist-panel\'	
        ,deepLinking: false
    });
    ';
    $otkl_panel = '
    <div 
        class="jplist-pagination" 
        data-control-type="pagination" 
        data-control-name="paging" 
        data-control-action="paging">
    </div>
    <!-- items per page dropdown -->
    <div 
        class="jplist-drop-down" 
        data-control-type="items-per-page-drop-down" 
        data-control-name="paging" 
        data-control-action="paging">
        <ul>
          <li><span data-number="7" data-default="true"> по 7 на страницу </span></li>
          <li><span data-number="25"> по 25 на страницу </span></li>
          <li><span data-number="all"> Все </span></li>
        </ul>
    </div>
    ';
    $otkl_total = '<div class="jplist-panel panel-top" style="display: inline-table; width: 100%">'.$otkl_panel.'</div>';
  }
  $otkl_total .= '<div class="o_list">';
  foreach ($otkl_rep as $otkl) {
    $otkl_total .= '<div class="o_block"><div class="row">';
    $otkl_total .= '<div class="col-xs-6 o_mark">'.$otkl["nick"]." ".s_stars($otkl["mark"])."</div>";
    $otkl_total .= '<div class="col-xs-6"><span class="o_date pull-right">&nbsp;('.$otkl["date"].")</span>\n";
    //$otkl_total .= '<span class="o_nick pull-right">'.$otkl["nick"]."</span>";
    $otkl_total .= "</div>\n";
    $otkl_total .= "</div>\n";
    $otkl_total .= '<div class="o_opinion">'.$otkl["comment"].$otkl["addon"]."</div>\n";
    $otkl_total .= '</div>';
  }
  $otkl_total .= '</div>';
  $otkl_total .= '<div class="jplist-panel panel-bottom" style="display: inline-table; width: 100%">'.$otkl_panel.'</div>';
  echo show_panel("Отзывы посетителей","h2",$otkl_total,1,"objopinions");
}
else {
  if (!($_otkl_mode)) {
    if ($_exct_mode == 0) {
      echo "<p class=\"t13 tc\">Пока нет ни одного отзыва. Значит... пора посетить это место и поделиться своими впечатлениями!</p>";
    }  
  }
}
if ($error) {
  echo "<div class=\"alert alert-danger\">$error</div>\n";
}

// оценка отзыва
$customscripttop .= '
  var reader = new FileReader();

  reader.onloadend = function () {
    $(\'#imgpreview\').attr(\'src\', reader.result) .width(\'auto\') .height(200);
    $(\'#imgpreview\').show();    
    $(\'#imgsubmit\').show();    
  }

  function readIMG(input) {
    if (input.files && input.files[0]) {
      $(\'#imgpreview\').hide();    
      $(\'#imgsubmit\').hide();    
      reader.readAsDataURL(input.files[0]);
    }
  }

  function buttonclick(sc,s_oid) {
    var result = $.post("/upscore.php", { type: "excite", score: sc, idtype: s_oid});
      result.done(function(scoredata) {
      jQuery.globalEval(scoredata);
    });
  };
';


echo "<form name=\"otklik\" id=\"otklikform\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">";
echo "<div class=\"col-sm-1\"></div><div class=\"col-sm-11\">";
if ((!($ex_descr)) && ($et == 1) && (($forum_user_level) || ($forum_user_rank)) ) {
  echo "<p>Для достопримечательности нет <b>подробного</b> описания. Можете его <a href=\"/sitemgr.php?admt=text&s=edit&exutown=$ex\"><b>создать</b></a> или просто оставить свое мнение</p>\n";
}
else {
  echo "<p><b>Оставить свое мнение</b></p>\n";   
}

echo "<p>Просим оставлять только личное мнение о посещении или исправление информации.<br>\n";
echo "Вопросы, комментарии к отзывам других посетителей - пожалуйста, на <a class=travell5r target=_blank href=$forumname/>форуме</a>.</p></div>\n";
echo "<div class=\"form-group\"><label class=\"col-sm-1 control-label tr\" for=\"otklik-name\">Ваше имя:</label>\n";
echo "<div class=\"col-sm-11\"><INPUT id=\"otklik-name\" minlength=\"3\" required=\"1\" class=\"o_opinion\" name=\"name\" type=\"text\" style=\"width: 100%;\"";
if ($forum_user_id != 0) {
  echo " value='$forum_user_name'";
}
echo "></div></div>\n";

if ($forum_user_id == 0) {
  echo "<p class=\"t13 tc\">Если Вы хотите, чтобы отзыв был подписан \"участником форума\",надо <a class=travell5r href='https:".$sitename."/sitemgr.php'>войти на сайт</a>.</p>\n";
  echo "<div class=\"form-group\"><label class=\"col-sm-1 control-label tr\" for=\"otklik-mail\">Эл. адрес:</label>\n";
  echo "<div class=\"col-sm-11\"><INPUT id=\"otklik-mail\" name=mail type=text style=\"width: 100%;\"></div></div>\n";
}
else {
  echo "<input type=hidden name=\"mail\" value=\"\">";
}
echo "<input type=hidden name=\"valck\" value=\"".$_valck."\">";
echo "<div class=\"form-group\"><label class=\"col-sm-1 control-label tr tmt1\" for=\"otklik-message\">Ваш<br>отзыв:</label>\n";
echo "<div class=\"col-sm-11 tmt1\"><TEXTAREA id=\"otklik-message\"  minlength=\"7\" required=\"1\" class=\"o_opinion\" name=message style=\"width: 100%; height: 20vh; box-sizing: border-box;\" wrap=top>";
if ($_has_otkl) {
  echo $_has_otkl;
}
echo "</TEXTAREA></div></div>\n";
echo "<div class=\"form-group\"><label class=\"col-sm-1 control-label tr tmt1\">Ваша<br>оценка:</label>\n";
echo "<div class=\"col-sm-11 tmt1\">";
if (!($_has_mark)) {
  echo '<div class="tz t24"><input type="number" required="1" name="mark" id="ratingmy" class="rating"/></div>';
}
else {
  echo '<div class="tz t24"><input type="number" required="1" name="mark" id="ratingmy" class="rating" value="'.$_has_mark.'"/></div>';
}
echo "</div></div>\n";
echo "<div class=\"col-sm-2\"></div><div class=\"col-sm-10\">";
echo "<input TYPE=hidden VALUE=$town NAME=town>";
echo "<input TYPE=hidden VALUE=$et NAME=et>";
echo "<input TYPE=hidden VALUE=$ex NAME=ex>";
echo "<INPUT class=\"ttb2 tzh t14 ttr tm10 tp10\" name=submit type=submit value=\"";
if ($_has_otkl) {
  echo "Изменить свой отзыв";
}
else {
  echo "Сохранить на сайте";
}
echo "\"></div>\n";
echo "</FORM>"; //конец столбца октлика и формы ввода отклика

if ($forum_user_id) {
  if ($forum_user_time < (time() - 1800)) {
    $customscript .= "\n alert('Регистрация на сайте заканчивается меньше чем через полчаса.');\n";
  }
}

$_totop = 1; // показать кнопку
echo "</div>\n <!-- wraper -->\n"; 
include('include/footer_bs.php');
