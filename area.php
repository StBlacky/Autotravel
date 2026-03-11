<?php
include_once('include/config.php');
include_once('include/func.php');
include_once('include/func_bs.php');
include_once('include/head00.php');
$_totop = 1;
$colorbox = 0;
$ug_gallery = 1;
$bootstrap = 1;
$footable = 1;
$masonry = 1;


//собираем массив типов достопримечательностей
$r_types=array();
$r_types[0] = "Города";
$sql_fd = "select name, id from type"; //типы - достопримечательности, гостиницы, ...
$result_fd = $dbi->query($sql_fd);

while ($row_trip = mysqli_fetch_array($result_fd)) {
    $id_fd = $row_trip["id"];
    $r_types[$id_fd] = $row_trip["name"];
} mysqli_free_result($result_fd);

//разбираем параметры вызова скрипта
if (preg_match("/^\//",$_pin,$exa)) { // в $_pin уходит все, что после скрипта *.php
    $a  = 0;
    if (preg_match("/^\/(\-*\d+)$/",$_pin,$exa)) {
      $a = $exa[1];      
    }  //если число с минусом или без - это область
} else {
    $a = $_REQUEST["a"]; //штатное место для ?а=2
    $a = mysafestring($a);
}

if (!($a)) {
  $newuri = "/areas.php";
  Header( "HTTP/1.1 301 Moved Permanently" );
  Header( "Location: $newuri" );
  exit;
}
$canon = "/area.php/$a";
$kml = "";

$tmarray=array();
$emarray=array();
$omarray=array();
$xmarray=array();

$sql_e = "";

$searchurl = "/test.php?area[]=$a&amp;";
$kml = "/kml.php/a/$a";
if ($_gmap_mode==9) {
  $dispgmap = "";   
} // не показывать
elseif($_gmap_mode==1) {
  $dispgmap = 1;  
} // osm
elseif($_gmap_mode==2) {
  $dispgmap = 2;   
} // osm
else {
  $dispgmap = 3;   
} // google
   
// ======= AT MAP FILTER BEGIN (UI + autostart) =======

$mapiframe = "<div id='at-map-filter' class='note-block' style='margin-bottom:10px; display:none;'>
<strong>Фильтр карты:</strong>
<div id='at-map-types' style='margin-top:6px;'></div>
<hr style='margin:10px 0;'>
<div id='at-map-groups'></div>
<label style='margin-left:15px'>
  <input type='checkbox' id='at-toggle-group-colors'>
  Показывать группы цветом
</label>
</div>";

$mapiframe .= "<div id=\"gmapv3\" style=\"display:none;\">";
//$mapiframe = "<div id=\"gmapv3\"></div>";
if ($dispgmap == 3) {
  $mapiframe .= "Используется Google Maps, на части территории РФ может не работать из-за технических ошибок при блокировке провайдерами зарубежных мессенджеров. Может быть изменена в настройке сайта.";
}
$mapiframe .= "</div>";
$mapiframe .= "<div class='map-downloads note-block' style='margin-top:10px;'>
<strong>Скачать координаты объектов (POI):</strong>
<a  href='{$kml}.wpt'>WPT</a>&nbsp;
<a  href='{$kml}.kml'>KML</a>&nbsp;
<a  href='{$kml}.gpx'>GPX</a>
</div>";




// --- карта области: автозапуск и фильтр (etype + группы) ---
$map_kml_url = $kml ? ($kml . ".kml") : "";
$map_type_labels = array();
$map_type_icons  = array();
$sql_map_types = "SELECT id, name, icon FROM type ORDER BY srtfld";
$res_map_types = $dbi->query($sql_map_types);
if ($res_map_types) {
  while ($row_mt = mysqli_fetch_assoc($res_map_types)) {
    $map_type_labels[intval($row_mt['id'])] = $row_mt['name'];
    $map_type_icons[intval($row_mt['id'])]  = $row_mt['icon'];
  }
  mysqli_free_result($res_map_types);
}
$mapiframe .= "<script>
window.atMapMode = ".(int)$dispgmap.";
</script>";

$mapiframe .= "<script>
window.atMapJsonUrl = \"/kml/area_map_json.php/$a\";
window.atMapAllowedTypes = [0,1,2,3,5,6,8];
window.atMapTypeLabels = ".json_encode($map_type_labels, JSON_UNESCAPED_UNICODE).";
window.atMapTypeIcons  = ".json_encode($map_type_icons, JSON_UNESCAPED_UNICODE).";
window.atMapInitDone = false;

(function(){

  function atCheckAndStart() {

    if (window.atHideMap) return;
    if (window.atMapInitDone) return;

    var p = document.getElementById('areamap');
    if (!p) return;

 var style = window.getComputedStyle(p);
if (style.display === 'none') return;

    if (!window.atHideMapFilter) {
      var f = document.getElementById('at-map-filter');
      if (f) f.style.display = 'block';
    }

    if (typeof atMapInit === 'function') {
      atMapInit();
    }
  }

  // автозапуск если блок уже открыт
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', atCheckAndStart);
  } else {
    atCheckAndStart();
  }

  // запуск при ручном раскрытии
  if (window.jQuery) {
    jQuery(function($){
      $('#areamap').on('shown.bs.collapse', atCheckAndStart);
    });
  }

})();
</script>";
// ======= AT MAP FILTER END =======


if (strstr($areaname,'/')) {$areaname = strstr($areaname,'/',true);} //иногда официальное название отличается от желаемого, поэтому в базе Желаемое/Официальное


// ====================================================
// Города области + иконки типов объектов (финал с excite.php)
// ====================================================

include_once("include/cube.data.php");

// --- только города региона с типом = 2 (открытые) ---
$thide = Array();
$sql_t2 = "SELECT t.id AS tid, e.id AS exid
           FROM towns t, exciting e
           WHERE t.area IN ($a) AND (t.hide = 2) AND e.town = t.id";
$result_t2 = $dbi->query($sql_t2);
if ($result_t2) {
  while ($myrow2 = mysqli_fetch_array($result_t2)) {
    $thide[$myrow2["tid"]] = $myrow2["exid"];
  }
  mysqli_free_result($result_t2);
}

// --- выбираем города области ---
$sql_e = "SELECT t.name AS town, t.url AS turl, t.hide AS hide,
                 t.rate AS rate, t.id AS tid
          FROM towns t
          WHERE t.area IN ($a) AND (t.hide <> 1)
          ORDER BY t.name";

$tncount = 0;
$result_e = $dbi->query($sql_e);
if ($result_e) {
  $tbltxt1 = "";
  $block = 1;
  $lx = "";
  while ($myrow = mysqli_fetch_array($result_e)) {
    $s_town = cyrname($myrow["town"]);
    $lc = mb_substr($s_town, 0, $block);
    if (!($lx)) {
      $tbltxt1 .= "<div class=\"tblock\">";
      $lx = $lc;
    }
    if ($lx != $lc) {
      $tbltxt1 .= "</div>\n<div class=\"tblock\">";
      $lx = $lc;
    }

    $s_hide = $myrow["hide"];
    $s_url = $myrow["turl"];
    $s_townid = $myrow["tid"];

    if ($s_url) {
      $local_url = '/gorod/'.$s_url.'.html';
    } else {
      $local_url = '/town.php/'.$s_townid;
    }

    if ($s_hide == 2) {
      $dclass = "travell9m";
      $aclass = "travell9s";
      $local_url = '/otklik.php/'.$thide[$s_townid];
    } else {
      $dclass = "travell9n";
      $aclass = "travell9b";
      $tncount++;
    }

   $s_rate = (int)$myrow["rate"];
if (isset($ahighrate) && $s_rate > $ahighrate) {
    $dclass = "travell9c";
    $aclass = "tz tb";
}

    // --- вывод строки города ---
    $tbltxt1 .= "<div class=\"$dclass\"><a class=\"$aclass\" href=\"$local_url\">$s_town</a>";

    $tbltxt1 .= get_icons_by_location($s_townid);

    $tbltxt1 .= "</div>\n"; // конец города
  }
  $tbltxt1 .= "</div>\n";
  mysqli_free_result($result_e);
}


$areax       = getareaall($a);
$areaname    = $areax[0]; // именительный
$areaname_r  = $areax[1]; // родительный
$areaname_v  = $areax[5]; // винительный
$areaname_po = $areax[6]; // предложный, если где-то используется

if (!$areaname) {
    Header("HTTP/1.1 301 Moved Permanently");
    Header("Location: /areas.php");
    exit;
}

if (strpos($areaname, '/') !== false) {
    $areaname = strstr($areaname, '/', true);
}

// предлог для "куда поехать"
$prep_v = 'в ';
if (preg_match('/^(Камчат|Чукот|Сахалин|Алтай)/ui', $areaname_v)) {
    $prep_v = 'на ';
}

// проверяем, есть ли кастомные метатеги в БД
$sql_meta = "SELECT meta_title, meta_description FROM areas WHERE id = '$a' LIMIT 1";
$res_meta = $dbi->query($sql_meta);
$meta = ($res_meta && mysqli_num_rows($res_meta)) ? mysqli_fetch_assoc($res_meta) : [];
mysqli_free_result($res_meta);

// если есть значения — используем их, иначе формируем по шаблону
if (!empty($meta['meta_title'])) {
    $title = trim($meta['meta_title']);
} else {
    $title = "Автопутешествия по {$areaname_r} — города, маршруты и достопримечательности | Autotravel.ru";
}

if (!empty($meta['meta_description'])) {
    $metadesc = trim($meta['meta_description']);
} else {
    $metadesc = "Куда поехать {$prep_v}{$areaname_v}? Города, достопримечательности и маршруты автопутешествий {$areaname_r}. Фото, советы и интерактивная карта региона.";
}






if ($a == -3) {
   $newstyle = '<style> .area-bg::before { content: \' \'; display: block; position: absolute; left: 0; top: 0; width: 100%; height: 100%; ';
   $newstyle .= 'z-index: 1;  opacity: 0.15; background-image: url(\'/png/bandera/3.png\');  background-repeat: no-repeat; background-position: auto; background-size: contain; }</style>'; 
}

#  $banners_shown = "yes";
include_once("include/header_bs.php");

if (!($_text_mode)) {
  $fotos = getfotoarea_bs($a,30,$areaname);
}  

echo "<div class=\"container-fluid\" style=\"width:100%;\">\n"; 

$aname = "";
// раздел для заграницы
if ($a<0) {
  $v_curr = "<div style=\"width:100%; overflow: hidden; margin: auto;\">";
  $v_curr .= "<div style=\"float:left; width: 230px;\">"
    . "<dl style=\"margin:10px;\">"
    . "<dt class=\"t12 tb tdl\">Столица"
    . "<dd class=\"tdl travell0\"><a class=\"travell5o\" href=\"/town.php/".$areax[2]."\">" . $areax[3]."</a></dl></div>\n";
  
  $c_curr = $areax[4];
  if ($c_curr == 643) {
    $v_curr .= "<div style=\"float:left; width: 230px;\">"
      . "<dl style=\"margin:10px;\">"
      . "<dt class=\"t12 tb tdl\">Валюта - российский рубль</dl>"
      . "</div>\n";
  }
  else {
    $a_curr = getcurrency($c_curr);
    $valcur = "не установлен";
    if ($a_curr[3] >0) {
      $valcur =  $a_curr[3] . " руб за " . $a_curr[2] . " " . $a_curr[1];
    }
    $v_curr .= "<div style=\"float:left; width: 230px;\">"
      . "<dl style=\"margin:10px;\">"
      . "<dt class=\"t12 tb tdl\">Валюта<dd class=\"tdd travell0\">" .getcurrency($areax[4])[0] .  ""
      . "<dt class=\"t12 tb tdl\">Текущий курс (ЦБ)<dd class=\"tdd travell0\">" . $valcur ."</dl>"
      . "</div>\n";
  }  
  $v_curr .= "</div>\n" ;
          
          
  $aname .= "<div class=\"row\">";
  if ($dispgmap) {//пояснение к интерактивной карте
    $aname .= "<div class=\"col-md-3 col-xs-12\">\n";  
    $aname .= "<H1 itemprop=\"name\">$areaname</H1>\n";
    $aname .= $v_curr;
    $aname .= "</div>\n";
    $aname .= "<div class=\"col-md-9 col-xs-12\">";
  //  $aname .= show_panel("Интерактивная карта","h4",$mapiframe,1,"areamap");    
    if ($kml) {
     $aname .=  "<p class=travell0>";
     $aname .=  "Скачать координаты достопримечательностей (POI) на навигатор в формате <a class=travell5m href='$kml" . ".wpt'>WPT</a>&nbsp;\n";
     $aname .=  "<a class=travell5m href='$kml" . ".kml'>KML</a>&nbsp;\n";
     $aname .=  "<a class=travell5m href='$kml" . ".gpx'>GPX</a>\n";
     $aname .=  "</p>\n";
    }// скачать точки
    $aname .= "</div>\n";
  }  
  else {
    $aname .= "<div class=\"col-md-4 col-xs-12\">\n";  
    $aname .= "<H1 itemprop=\"name\">$areaname</H1>\n";
    $aname .= "</div>\n";
    $aname .= "<div class=\"col-md-8 col-xs-12\">";
    $aname .= $v_curr;
    $aname .= "</div>\n";
    if ($kml) {
     $aname .=  "<p class=travell0>";
     $aname .=  "Посмотреть на карте <a class=travell5m target=_blank href=\"" . $kml . ".htm\">Google MAP</a><br>\n";
     $aname .=  "<a class=travell5m href='$kml" . ".kml'>KML</a>&nbsp;\n";
     $aname .=  "<a class=travell5m href='$kml" . ".gpx'>GPX</a>\n";
     $aname .=  "</p>\n";
    }// скачать точки
  }
  $aname .= "</div>\n" ;
  // закончилась заграница
  // закончилась область заголовка    
  //  echo "Виза: Шенген  ";
  //  echo "ПДД: есть  ";
  //  echo "Платные дороги: есть</p>";
}
else {
  $aname .= "<H1 itemprop=\"name\">$areaname</H1>";
}

$bring_exists = false;

$stmt = $dbi->prepare("
  SELECT 1
  FROM exciting
  WHERE area = ?
    AND type = 5
    AND (mainid IS NULL OR mainid <= 0)
  LIMIT 1
");
$stmt->bind_param("i", $a);
$stmt->execute();
$stmt->store_result();
$bring_exists = $stmt->num_rows > 0;
$stmt->close();

$bring_url = false;

if ($bring_exists) {
  $adata = getareaall($a);
  $areaslug = (is_array($adata) && isset($adata[6])) ? trim((string)$adata[6]) : "";

  if ($areaslug !== "") {
    $bring_url = "/excite_bring_area/".$areaslug."/";
  } else {
    $bring_url = "/excite_bring_area/".$a."/"; // fallback
  }
}


echo breadcrumbs(['area' => $a]);
echo "<h1 class=\"area-title\">$areaname</h1>";
// --- Локальное меню-якоря (Города / Карта / Достопримечательности / Фото) ---
echo local_nav([
    'towns'  => !empty($tbltxt1),
    'map'    => !empty($dispgmap),
    'ex'     => true,
    'photos' => $fotos ? "/phalbum.php/area/$a" : false,
    'bring'  => $bring_url
]);

if ($fotos) {
  echo "<div class=\"row\"><!-- main -->\n<div class=\"col-md-9 col-xs-12\"><!-- left -->\n"; 
}
else {
  echo "<div class=\"row\"><!-- main -->\n<div class=\"col-md-12 col-xs-12\"><!-- left -->\n"; 
} 

// область заголовка   
 $areadesc=getareadesc($a);
if ($areadesc) {
  echo "<div style =\"padding: 5px;\" itemprop=\"text\" class=\"travell0\" id=\"areadescr\">$areadesc</div>";    
} //выводим описание
    
//область городов
if ($tbltxt1) { //есть города
  echo "<!-- towns -->\n<H2 itemprop=\"description\">Города $areaname_r</H2>";?>
  
  <div class="note-block">
  <p>В списке ниже представлены города и населённые пункты <?php echo $areaname_r; ?>. Иконки рядом с названиями показывают, какие объекты есть в городе: достопримечательности, гостиницы, кафе и сервисы для путешественников.</p>

  <p>Размер и цвет названия отражают насыщенность объектами: <strong><span class="travell5m">зелёным</span></strong> выделены популярные города, <strong>черным</strong> — города с несколькими объектами, шрифтом помельче — небольшие населённые пункты, где объектов немного.</p>
</div>
<?php
  
  echo "<div class=\"column\"  id=\"areatowns\">$tbltxt1</div><!-- /towns -->\n";
}

if ($dispgmap) {//пояснение к интерактивной карте
  $mapiframe = "<p class=note-block><strong>$areaname</strong> - <b>города</b> и <b>достопримечательности</b>. Карта задумана так, чтобы вам было проще ориентироваться: нажимайте на любую метку, открывайте описание объекта, смотрите фото, отзывы и что находится рядом.
Надеемся, что этот сервис поможет быстро понять, куда поехать и как удобнее построить свой маршрут по " . $areaname_r . ".</p>\n".$mapiframe;  
  echo show_panel("Интерактивная карта $areaname_r","h2",$mapiframe,1,"areamap");    
 }// собственно карта
 if ($kml) {
//  include("include/kml.php");    
 }// скачать точки
 
// echo at_init_gmap($dispgmap, $kml);


// =====================
// Достопримечательности
// =====================

// Карточки "Топ мест"
$cards  = getareaobj($a);
$images = 0;
$mtm    = "";

foreach ($cards as $card) {

  $foto_ex = get1fotoex_bs($card["ex"], 1, 2);

  $mtm .= '<div class="autocard">';

  $mtm .= '<a class="exclink" target="_blank" href="/otklik.php/'.(int)$card["ex"].'">';
  $mtm .= '<h3>'.htmlspecialchars($card["ename"]).' ('.htmlspecialchars($card["tname"]).') '.bs_stars($card["mark"]).'</h3>';
  $mtm .= '</a>';

  if ($foto_ex) {
    $mtm .= '<div class="exclinkbutton">'.$foto_ex.'</div>';
    $images = 1;
  }

  // укороченное описание
  $mtm .= '<div class="tj t0">'.$card["descr_local"]."</div>\n";

  $mtm .= '<div class="mt-2">';
  $mtm .= '<p class="travell0">Рейтинг: <strong>'.htmlspecialchars($card["wrate"]).'</strong></p>';
  $mtm .= '</div>';

  $mtm .= '<button type="button" class="btn exclinkbutton btn-xs btn-info pull-right">подробнее&nbsp;>>></button>'."\n";

  if (($forum_user_id > 1)) {
    $mtm .= "\n<div class=\"pull-left\" id=\"basket".(int)$card["ex"]."\"><a class=\"btn\" onclick='BasketAdd(".(int)$card["ex"].",".$forum_user_basket.",\"basketadd".(int)$card["ex"]."\")'>Добавить в ".$forum_user_basket_name."</a></div>\n";
  }

  $mtm .= '</div>';
}

// Превью + объяснение (note-block)
// ВАЖНО: это именно "Топ", а хабы — навигация по темам внутри топа
$mtmprev  = '<p class="note-block">';
$mtmprev .= 'Здесь — только самые заметные достопримечательности '.$areaname_r.' по мнению наших посетителей. ';
$mtmprev .= 'Выберите категорию, чтобы посмотреть лучшие места по направлениям (архитектура, музеи, природа и т.д.), или перейдите к полному списку достопримечательностей  '.$areaname_r.' .';
$mtmprev .= '</p>'."\n";

// Хабы (верхнеуровневые типы) — сразу после note-block
include_once('include/area_hubs.php');
$hub = area_hub_render($a);

// Обёртка под карточки (masonry / light)
if ($images) {
  $mtm_cards = "<div id=\"texa\" class=\"autocard-masonry\">\n".$mtm."</div>\n";
} else {
  $mtm_cards = "<div id=\"texa\" class=\"autocard-mason-light\">\n".$mtm."</div>\n";
}

// Итоговый контент панели
$panel_body = $mtmprev . $hub . $mtm_cards;

// Панель раздела
echo show_panel("Топ мест $areaname_r", "h2", $panel_body, 1, "areaex");

// ==== конец блока Достопримечательности ====








echo "\n</div><!-- /left -->\n"; //конец левой части средней области
if ($fotos) {
  echo "<div class=\"col-md-3 col-xs-12\"><!-- right -->\n";
  echo "<h2 id=\"areafoto\" itemprop=\"description\">Фотографии $areaname_r</h2>\n";

  // лёгкий SEO-текст, без дублей
  echo "<p class=note-block>Случайные кадры региона — города, дороги, пейзажи, достопримечательности. ";
  echo "Это лишь часть большого архива. Все фото можно посмотреть в разделе ";
  echo "<a class='travell5r' href='/phalbum.php/area/$a'><strong>Фотографии $areaname_r</strong></a>.</p>";

  // вывод самих фотографий
  echo "<div id='fotoimages'>$fotos</div>\n";
  echo "</div><!-- /right -->\n";
}
 
echo "</div><!-- /main -->\n</div>";

if ($dispgmap) {
  //$customscripttop .= "\ninitmapv" . $dispgmap . "(\"" . $kml . ".kml\",null,0,0);\n";
}  
$customscript .= ' 
  $(".exclinkbutton").click(function() {
    var $link = $(this).parent().children(".exclink");
    $link[0].click();
  });
  ';

// ==========================
// JSON-LD MICRODATA FOR AREA
// ==========================

// 1. AdministrativeArea
$schema_area = [
    "@context" => "https://schema.org",
    "@type" => "AdministrativeArea",
    "name" => $areaname,
    "description" => "Города, достопримечательности, пляжи, озёра, карта и идеи для путешествий по $areaname_r.",
    "url" => "https://autotravel.ru/area.php/$a"
];

// 2. ItemList — города (на основе SQL $sql_e)

$towns_json = [];
$pos = 1;

// повторяем SQL, который уже использовался выше
$sql_towns = "SELECT t.name AS town, t.id AS tid, t.hide AS hide
              FROM towns t
              WHERE t.area IN ($a) AND (t.hide <> 1)
              ORDER BY t.name";

$res_towns = $dbi->query($sql_towns);

if ($res_towns) {
    while ($row = mysqli_fetch_assoc($res_towns)) {
        $towns_json[] = [
            "@type" => "ListItem",
            "position" => $pos++,
            "name" => cyrname($row["town"]),
            "item" => "https://autotravel.ru/town.php/" . $row["tid"]
        ];
    }
    mysqli_free_result($res_towns);
}

$schema_towns = [
    "@context" => "https://schema.org",
    "@type" => "ItemList",
    "name" => "Города $areaname_r",
    "itemListElement" => $towns_json
];

// 3. ItemList — топ-мест
$places = [];
$pos = 1;
foreach ($cards as $card) {
    $places[] = [
        "@type" => "ListItem",
        "position" => $pos++,
        "name" => $card['ename'],
        "item" => "https://autotravel.ru/otklik.php/" . $card['ex']
    ];
}

$schema_places = [
    "@context" => "https://schema.org",
    "@type" => "ItemList",
    "name" => "Топ достопримечательностей $areaname_r",
    "itemListElement" => $places
];

// 4. WebPage
$schema_webpage = [
    "@context" => "https://schema.org",
    "@type" => "WebPage",
    "name" => "$areaname — автопутешествия, города и достопримечательности",
    "url" => "https://autotravel.ru/area.php/$a",
    "description" => $metadesc
];

// вывод одним блоком
$customjsonld .= "\n<script type='application/ld+json'>"
    . json_encode(
        [$schema_area, $schema_towns, $schema_places, $schema_webpage],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
      )
    . "</script>\n";

echo '<script src="/js/at-map-unified.js?v=2"></script>';
include("include/footer_bs.php");
