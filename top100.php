<?php

include_once('include/config.php');
include_once('include/func.php');
include_once('include/func_bs.php');
include_once('include/head00.php');
$bootstrap = 1;
$masonry = 1;
$colorbox  = 2;
$maxcount = 5; // максимально объектов по области!

function desclimits($c1, $c2) {
  $l1=(float) $c1["lim"];
  $l2=(float) $c2["lim"];
  if ($l1 == $l2) {
    return 0;
  }
  if ($l1 > $l2) {
    return -1;
  }
  if ($l1 < $l2) {
    return 1;
  }
}

function descrates($c1, $c2) {
  $l1=(float) $c1["rate"];
  $l2=(float) $c2["rate"];
  if ($l1 == $l2) {
    return 0;
  }
  if ($l1 > $l2) {
    return -1;
  }
  if ($l1 < $l2) {
    return 1;
  }
}

$sqllocal = " (e.area >0) ";
$title = "Рейтинг достопримечательностей России";
$metatags = "TOP-100 достопримечательностей России по версии  По России на авто";
$metadesc = "рейтинг достопримечательности Россия";
$total_h = "TOP-100 достопримечательностей России";
$comment = "Самые интересные места России, достойные посещения, по мнению посетителей сайта По России на авто.";
$comment_1 = "Каждый, оставляющий отзыв о достопримечательности, может повлиять на этот рейтинг. Просто перейдите на страницу интересного места и поделитесь своими впечатлениями.";
$bread_1 = '<li><a href="/cube.php">Все достопримечательности</a></li>';
$bread_2 = '<li><a href="/top100.php?z=1">TOP-100 достопримечательностей стран мира</a></li>';
if (isset($_REQUEST["z"])) {
 $sqllocal = " (e.area <0) ";  
 $title = "Рейтинг достопримечательностей зарубежных стран";
 $metatags = "TOP-100 достопримечательностей зарубежья по версии по версии  За границу на авто";
 $metadesc = "рейтинг достопримечательности страны";
 $total_h = "TOP-100 достопримечательностей стран мира";
 $comment = "Самые интересные места стран мира, достойные посещения, по мнению посетителей сайта За границу на авто.";
 $bread_1 = '<li><a href="/cube.php">Все достопримечательности</a></li>';
 $bread_2 = '<li><a href="/top100.php">TOP-100 достопримечательностей России</a></li>';
}
// собираем массив "сколько объектов по каждой области"
$sql1 = 'select count(*) as cnt, e.area as area from exciting e where '.$sqllocal.' and (e.type in (1,6,7)) group by area';
$total = 0;
$ii = 0;
$result = $dbi->query($sql1);
if ($result) {
  while ($myrow = mysqli_fetch_array($result)) {
    $counts[$ii]["cnt"] = $myrow["cnt"];
    $counts[$ii]["area"] = $myrow["area"];
    $total = $total + $myrow["cnt"]; // сколько вообще всего объектов
    $ii++;
  }
}
// вычисляем лимит(ы) "сколько взять объектов от области"
$tlim = 0;
foreach ($counts as $area=>$count) {
  $cnt = (float) $counts[$area]["cnt"];
  $lim = ($cnt * 100 / $total);
  if ($lim > $maxcount) {
    $lim=$maxcount;
  }
  $counts[$area]["lim"] = $lim;
  //echo $counts[$area]["area"]." ".$lim."\n";
  $counts[$area]["limi"] = round($lim);
  $tlim = $tlim + round($lim);
}
//var_dump($counts);
// выравнивание на 100
usort($counts, 'desclimits');

foreach ($counts as $area=>$count) {
  if ($tlim>100) {
    $counts[$area]["limi"] = $counts[$area]["limi"]-1;
    $tlim--;
  }
  if ($tlim<100) {
    $counts[$area]["limi"] = $counts[$area]["limi"]+1;
    $tlim++;
  }
}

$cards=array();
$total = 0;
foreach ($counts as $area=>$count) {
  $lim = $count["limi"];
  $aid = $count["area"];
  if ($lim>0) {
    $sql = "select e.id as ex, e.ffile as ffile, e.town as town, e.area as area, e.name as ename, e.wrate as rate, e.descrip as descrip, "
    . " a.name as aname, t.name as tname, t.url as turl from"
    . " exciting e, areas a, towns t where"
    . " (a.id = e.area)"
    . " and (t.id = e.town)"
    . " and (e.type in (1,6,7))"
    . "and (e.area=" . $aid . ")"
    . " order by rate desc limit ".$lim;
    //echo $sql;    

    $result = $dbi->query($sql);
    if ($result) {
      while ($myrow = mysqli_fetch_array($result)) {
        $rate = (float)$myrow["rate"];
        $myrow["rate"] = sprintf("%01.2f",$rate);
        $cards[$ii]=$myrow;
        //if (!($myrow["ffile"])) {
           $cards[$ii]["foto"] = get1fotoex_bs($myrow["ex"], 1, 0, $myrow["ffile"]);
        //}   
        //else {
        //   $cards[$ii]["foto"]="/phalbum/".$myrow["ffile"]."-s.jpg";
        //}
        $total++;
        $ii++;
      }
      mysqli_free_result($result);
    }
    else {
      //echo $sql;    
    }
  }  
}
/**/
foreach ($cards as $ii=>$card) {
  if ($forum_user_id) {
    $result = $dbi->query("select mark from otklik where exciting=".$card["ex"]." and cook like 'forum:".$forum_user_id.":%'");
    if ($result) {
      $myrow = mysqli_fetch_array($result);
      $cards[$ii]["mark"] = $myrow["mark"];
    }
    else {
      $cards[$ii]["mark"] = "";
    }
  }
  else {
    $cards[$ii]["mark"]="";
  }
}  
//
usort($cards, 'descrates');
// вывод на экран
include('include/header_bs.php');

echo '<div class="container-fluid" style="width: 100%">'; //начало раздела 
echo '<h1>'.$total_h.'</h1>';
echo '<p>'.$comment.'</p>';
echo '<ol class="breadcrumb">' . $bread_1 . $bread_2 . '</ol>';//навигация  breadcrumb 
echo '<p>'.$comment_1.'</p>';
echo '<div style="width: 100%" class="autocard-masonry">';
$ii = 1;
  foreach ($cards as $card) {
    $dop = '';  
    if ($forum_user_id) {
      if ($card["mark"]) {
        $dop = ' autovisited';  
      }
    }
    echo '<div class="autocard'.$dop.'">';
    echo '<h2>'.$ii.'. <a class="exclink" href="/otklik.php/'. $card["ex"]. '">' . $card["ename"] . '</a>';
    if ($forum_user_id) {
      if ($card["mark"]) {
        echo "&nbsp;&nbsp;".bs_stars($card["mark"]);
      }
    }  
    echo '</h2>';
    $ii++;
    echo '<p class="mb-2 text-muted t0"><a href="/area.php/'. $card["area"] . '">'. $card["aname"] . "</a> / ";
    if ($card["turl"]) {
      echo '<a href="/gorod/'. $card["turl"] . '.html">';
    }
    else {
      echo '<a href="/town.php/'. $card["town"] . '">';
    }
    echo $card["tname"] .'</a></p>';
    echo '<div>'.$card["foto"].'</div>';
    echo '<div class="tj t0">'.$card["descrip"].'</div>';
    echo '<div class="mt-2">';      
    echo '<p>Рейтинг: <strong>'.$card["rate"].'</strong></p>';
    // echo '<a href="/otklik.php/'. $card["ex"]. '" class="btn btn-primary"><span class="tcrumb">Фотографии, отклики, карта</span></a>';
    echo '</div>';
    echo "<button type=\"button\" class=\"btn exclinkbutton btn-xs btn-info pull-right\">подробнее&nbsp;>>></button>\n";
    echo '</div>';
  }
echo '</div>'; // конец масонри
echo '</div>'; // конец раздела маршруты    
  $customscript .= ' 
    $(".exclinkbutton").click(function() {
      var $link = $(this).parent().find(".exclink");
      $link[0].click();
    });
    ';
include('include/footer_bs.php');
