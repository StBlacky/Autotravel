<?php
include_once('include/head00.php');
include_once('include/func_bs.php');

$bootstrap = 1;
$colorbox  = 1;

/* ---------- helpers ---------- */

function block_open($name,$ii,$opened) {
  echo '<div class="col-md-12">';
  echo '<div class="panel">';
  echo '<div class="panel-heading">';
  echo '<button type="button" class="btn btn-xs btn-info pull-right" data-toggle="collapse" data-target="#collapsedx'.$ii.'">';
  echo ($opened=="in")
    ? '<span class="glyphicon glyphicon-chevron-up"></span>'
    : '<span class="glyphicon glyphicon-chevron-down"></span>';
  echo '</button>';
  echo '<h3 class="helpheader">'.$name.'</h3>';
  echo '</div>';
  echo '<div id="collapsedx'.$ii.'" class="panel-collapse collapse '.$opened.'">';
  echo '<div class="panel-body">';
}

function block_close() {
  echo '</div></div></div></div>';
}

/* ---------- объект ---------- */

function display_trip_obj($obj, $objid, $objname = '', $cardtype = 1) {
    global $dbi;



    // --- ГОРОДА (t777) ---
    if ($obj === 't') {
        if ($objid) {
            $name = gettownname($objid);
            echo ' - <a href="/town.php/'.$objid.'">'.$name.'</a>';
        }
        return;
    }

 

    // --- ДОСТОПРИМЕЧАТЕЛЬНОСТИ (e) ---
    if ($obj !== 'e' || !$objid) return;

    $sql = "SELECT name, descrip, wrate FROM exciting WHERE id = $objid";
    $res = $dbi->query($sql);
    if (!$res || !($row = mysqli_fetch_array($res))) return;

    $name = $row['name'];
    $desc = $row['descrip'];
    $rate = sprintf('%01.2f', $row['wrate']);

    $foto = get1fotoex_bs($objid, 1, 0);
    $cid  = 'exdesc_'.$objid;

    echo '<div class="col-md-3 col-sm-6 col-xs-12">';
    echo '  <div class="panel panel-default autocard-clean">';

    echo '    <div class="panel-heading">';
    echo '      <h4><a href="/otklik.php/'.$objid.'">'.$name.'</a></h4>';
    echo '      <div class="autocard-rate">Рейтинг: '.$rate.'</div>';
    echo '    </div>';

    if ($foto) {
        echo '<div class="autocard-photo">'.$foto.'</div>';
    }

    if ($desc) {
        echo '<div class="autocard-actions">';
        echo '  <a data-toggle="collapse"  class="btn-outline" href="#'.$cid.'">Показать описание</a>';
        echo '</div>';
        echo '<div id="'.$cid.'" class="collapse autocard-desc">';
        echo nl2br($desc);
        echo '</div>';
    }

    echo '  </div>';
    echo '</div>';
}

/* ---------- вход ---------- */


$id = "";
$ty = "";

if (preg_match("/^\//",$_pin,$exa)) {
 if (preg_match("/^\/(\d+)$/",$_pin,$exa)) {
  $id = $exa[1];
 }
}
else {
 $id = (int) $_REQUEST["trip"];
 $ty = (int) $_REQUEST["ty"];
}

$ttype = array(
  "tr" => "Дорога",
  "ex" => "Достопримечательности",
  "ed" => "Дополнительно",
  "op" => "Рекомендации по приёму пищи",
  "ho" => "Рекомендации по ночлегу",
  "co" => ""
);

$extypes = array(
  "tr" => "99",
  "ex" => "1",
  "ed" => "1",
  "op" => "3",
  "ho" => "2"
);

if(!$id) exit;

/* ---------- данные маршрута ---------- */

$res=$dbi->query("SELECT * FROM trip_desc WHERE id=$id");
$row=mysqli_fetch_array($res);

$title=$row['title'];
$track=$row['track'];
$descr=$row['descrip'];
$t_ex=$row['ex'];
$t_d =$row['d'];

include('include/header_bs.php');
echo '<div class="container-fluid">';

/* ---------- хлебные крошки ---------- */

echo '<ol class="breadcrumb">';
echo '<li><a href="/marshruty.php">Маршруты</a></li>';
echo '<li class="active">'.$title.'</li>';
echo '</ol>';

echo "<h1>$title</h1>";

if($descr){
  echo '<div class="note-block">'.$descr.'</div>';
}

/* ---------- разбор track ---------- */

$trs=explode('#',$track);
$hidden_opened=0;
$day_opened   = 0;
$panel_opened = 0;
$blocks = 0;

foreach ($trs as $trc) {

    /* ---------- ДЕНЬ ---------- */
    if (preg_match('/^d(\d+)$/', $trc, $m)) {

        if ($panel_opened) {
            block_close();
            $panel_opened = 0;
        }

        if ($day_opened) {
            block_close();
        }

        $blocks++;
        block_open("День ".$m[1], $blocks, "in");
        $day_opened = 1;
        continue;
    }

    $tri  = explode('|', $trc);
    $type = $tri[0];

    /* ---------- ДОРОГА ---------- */
    if ($type == 'tr') {

        if ($panel_opened) {
            block_close();
            $panel_opened = 0;
        }

        $road = [];
        for ($i = 1; $i < count($tri); $i++) {
            if (preg_match('/^t(\d+)$/', $tri[$i], $m)) {
                $road[] = gettownname((int)$m[1]);
            }
        }

        if ($road) {
            echo '<p class="travell5c">Дорога: '.implode(' — ', $road).'</p>';
        }
        continue;
    }

    
    /* ---------- КОММЕНТАРИЙ (co и x) ---------- */
if ($type == 'co' || $type == 'x') {

    // ⬅️ КЛЮЧЕВОЕ: закрываем блок достов
    if ($hidden_opened) {
        block_close();
        $hidden_opened = 0;
    }

    $text = $tri[1] ?? '';

    // xn — служебный префикс
    if (preg_match('/^xn(.+)/s', $text, $m)) {
        $text = $m[1];
    }

    echo '<div class="note-block">'.nl2br($text).'</div>';
    continue;
}


    /* ---------- БЛОКИ (досты / еда / отели) ---------- */

    if (isset($ttype[$type])) {

        if ($panel_opened) {
            block_close();
            $panel_opened = 0;
        }

        $blocks++;
        block_open($ttype[$type], $blocks, 'in');
        $panel_opened = 1;

        echo '<div class="row">';

        for ($i = 1; $i < count($tri); $i++) {

    /* --- прямой объект --- */
    if (preg_match('/^e(\d+)$/', $tri[$i], $m)) {
        display_trip_obj('e', (int)$m[1]);
        continue;
    }

    /* --- выбор по рейтингу --- */
    if (preg_match('/^rate(\d+)-(\d+)$/', $tri[$i], $m)) {

        $limit = (int)$m[1];
        $town  = (int)$m[2];
        $extype = $extypes[$type] ?? 1;

        $sql = "
            SELECT id
            FROM exciting
            WHERE town = $town
              AND type = $extype
            ORDER BY wrate DESC
            LIMIT $limit
        ";

        $res = $dbi->query($sql);
        if ($res) {
            while ($row = mysqli_fetch_array($res)) {
                display_trip_obj('e', (int)$row['id']);
            }
            mysqli_free_result($res);
        }
        continue;
    }
}


        echo '</div>';

        block_close();
        echo '<div class="clearfix"></div>';

        $panel_opened = 0;
        continue;
    }
}

/* ---------- ФИНАЛЬНЫЕ ЗАКРЫТИЯ ---------- */

if ($panel_opened) block_close();
if ($day_opened) block_close();

echo '</div>';
include('include/footer_bs.php');


