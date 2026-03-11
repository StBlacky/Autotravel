<?php

include_once('include/config.php');
include_once('include/func.php');
include_once('include/head00.php');



$bootstrap=1;
$ex = "";
$ddd = "";
$seo_intro = '';

if (preg_match("/^\//", $_pin, $exa)) {
  if (preg_match("/^\/d(\d+)$/", $_pin, $exa)) {
    $ddd = $exa[1];
  }
  if (preg_match("/^\/e(\d+)$/", $_pin, $exa)) {
    $ex = $exa[1];
  }
} else {
  $ddd = (int) mysafestring($_REQUEST["ddd"]);
  $ex = (int) mysafestring($_REQUEST["ex"]);
}

$ddd = (int)$ddd;
// -------------------------------
// BreadcrumbList JSON-LD для d1 / d2 / d3
// -------------------------------
if (!empty($ddd) && in_array($ddd, [1, 2, 3], true)) {

 $crumbs = [
    1 => [
      'name' => 'Маршруты выходного дня (1–2 дня)',
      'url'  => 'https://autotravel.ru/triptrackm.php/d1',
    ],
    2 => [
      'name' => 'Праздничные маршруты (3–4 дня)',
      'url'  => 'https://autotravel.ru/triptrackm.php/d2',
    ],
    3 => [
      'name' => 'Маршруты для отпуска (от 5 дней)',
      'url'  => 'https://autotravel.ru/triptrackm.php/d3',
    ],
  ];

  $jsonld = [
    '@context' => 'https://schema.org',
    '@type'    => 'BreadcrumbList',
    'itemListElement' => [
      [
        '@type'    => 'ListItem',
        'position' => 1,
        'name'     => 'Готовые маршруты',
        'item'     => 'https://autotravel.ru/marshruty.php',
      ],
      [
        '@type'    => 'ListItem',
        'position' => 2,
        'name'     => $crumbs[$ddd]['name'],
        'item'     => $crumbs[$ddd]['url'],
      ],
    ],
  ];

  $customjsonld =
    "<script type=\"application/ld+json\">\n"
    . json_encode($jsonld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
    . "\n</script>";
}



if ($ex) { // по типу достопримечательностей
  $sqlex = "select descrip as ed from trip_ex where id = $ex";
  $resultex = $dbi->query($sqlex);
  if ($myrowex = mysqli_fetch_array($resultex)) { //ищем этот тип достопримечательностей, формируем тэги
    $ed = $myrowex["ed"];
    $title = $ed . ": маршруты для путешествий";
    $metatags= $ed . 'самостоятельные путешествия';
    $metadesc= 'Маршруты самостоятельных путешествий, в которых подобраны достопримечательности по тематике' . $ed;
  }
  $sql = "select trip_desc.title as tt, trip_desc.id as id, trip_desc.descrip as descrip, trip_d.descrip as dd "
          . "from trip_desc, trip_d where trip_d.id = trip_desc.d and trip_desc.ex = $ex order by 1";
  $h1=$ed;
} 

if ($ddd) { //если длительность 
  $sqld = "select descrip as dd, days  from trip_d where id = $ddd";
  $resultd = $dbi->query($sqld);
  $dd = "";
  $days = "";
  if ($myrowd = mysqli_fetch_array($resultd)) { //ищем маршруты конкретной длительности
    $dd = $myrowd["dd"];
    $days = $myrowd["days"];
    $h1= 'Маршруты путешествий длительностью ' . $days;
    if ($ddd == 1) {
       $title = 'Маршруты выходного дня по России — автопутешествия на 1–2 дня';
       $h1 = 'Маршруты выходного дня (1–2 дня)';
        $seo_intro = 'Готовые маршруты выходного дня на автомобиле: города, достопримечательности и идеи поездок на 1–2 дня.';
        $metatags = 'маршруты выходного дня автопутешествия';
        $metadesc = 'Готовые маршруты выходного дня по России: куда поехать на машине на 1–2 дня, что посмотреть и как спланировать поездку.';
    }
    if ($ddd == 2) {
    $title = 'Праздничные маршруты по России — автопутешествия на 3–4 дня';
    $h1 = 'Праздничные маршруты (3–4 дня)';
    $seo_intro = 'Готовые праздничные маршруты по России для поездок на автомобиле: идеи путешествий на длинные выходные, города и достопримечательности.';
    $metatags = 'праздничные маршруты автопутешествия по россии';
    $metadesc = 'Праздничные маршруты по России на автомобиле: куда поехать на 3–4 дня, что посмотреть и как спланировать поездку на длинные выходные.';
}

    if ($ddd == 3) {
    $title = 'Маршруты для отпуска по России — автопутешествия от 5 дней';
    $h1 = 'Маршруты для отпуска (от 5 дней)';
    $seo_intro = 'Готовые маршруты для отпуска по России на автомобиле: дальние поездки, интересные регионы и насыщенные путешествия от пяти дней.';
    $metatags = 'маршруты для отпуска автопутешествия по россии';
    $metadesc = 'Маршруты для отпуска по России на автомобиле: идеи дальних поездок, регионы и достопримечательности для путешествий от 5 дней.';
}

  }
  

  
  $sql = "select trip_desc.title as tt, trip_desc.id as id, trip_desc.descrip as descrip, trip_ex.descrip as dd "
          . "from trip_desc, trip_ex where trip_ex.id=trip_desc.ex and trip_desc.d = $ddd order by 1";
}
$ii=0;
$cards=array();
if ($sql) {
  $result = $dbi->query($sql);
  if ($result) {
    while ($myrow = mysqli_fetch_array($result)) {
      $id = $myrow["id"];
      $desc = $myrow["descrip"];
      $dd = $myrow["dd"];
      $tt = $myrow["tt"];
      if ($tt=='') {
        $tt="Маршрут для путешествия №$id";
      }
      $cards[$ii]=array("id"=>$id, "head"=>$tt, "subhead"=>$dd, "desc"=>$desc);
      $ii++;
    }
    mysqli_free_result($result);
  }
  else {
    // ошибка sql -- наверное надо куда то сообщение послать ?
  }
}
// вывод на экран

include('include/header_bs.php');
echo '<div class="container-fluid">'; 
// breadcrumb — контекст

echo '<nav aria-label="breadcrumb" class="mb-2">';
echo '  <ol class="breadcrumb mb-2">';
echo '    <li class="breadcrumb-item"><a href="/marshruty.php">Все готовые маршруты</a></li>';
echo '    <li class="breadcrumb-item active" aria-current="page">'.$h1.'</li>';
echo '  </ol>';
echo '</nav>';

// H1 — смысл страницы
echo '<h1 class="mt-2">'.$h1.'</h1>';

// note-block — пояснение + альтернатива
echo '<div class="note-block">';

if ($seo_intro) {
  echo '<p class="mb-1">'.$seo_intro.'</p>';
}

echo '<p class="mb-0">';
echo 'Или вы можете <a href="/ch_trip.php">проложить маршрут самостоятельно</a>.';
echo '</p>';

echo '</div>';




echo '<div style="width: 100%" data-masonry=\'{ "itemSelector": ".autocard", "columnWidth": ".autocard" }\'>';

foreach ($cards as $strcard) {

  echo '<div class="autocard mb-4">'; // воздух вместо линий

  // Заголовок маршрута
  echo '<h2 class="mb-1">' . $strcard["head"] . '</h2>';

  // Подзаголовок / тип — приглушённо
  if ($strcard["subhead"]) {
    echo '<div class="text-muted small mb-2">'. $strcard["subhead"] .'</div>';
  }

  // Короткое описание
  $short = mb_substr(strip_tags($strcard["desc"]), 0, 350) . '…';
  echo '<div class="tj t0 mb-2">'.$short.'</div>';

  // CTA — единственный акцент
  echo '<div>';
  echo '<a href="/trip_rec.php/'. $strcard["id"]. '" class="btn btn-outline">';
  echo 'Подробное описание маршрута';
  echo '</a>';
  echo '</div>';

  echo '</div>';
}

echo '</div></div>'; // конец раздела маршруты

include('include/footer_bs.php');
?>
