<?php
include_once("include/config.php");
include_once('include/func.php');
include_once('include/func_bs.php');
$title = "Элементы статистики";
$bootstrap = 1;
include_once("include/header_bs.php");
echo "<div class='container-fluid' style='width:100%;'>";

$strdate ="2024-10-17";
$phalbumbeg = "91964"; # 2025 год = 92064
$excidbeg = 38803; # 2025 год= >39927

?>
<div class="row">
<div class="col-xs-12">

<h3 class="t14 tb tc">Элементы статистики за прошедший год (после <?php echo $strdate; ?>)</h3>
<table border=1 borderwidth=1  width=95% cellpadding=8>
<tbody class=travell0>

<?php

$tim = time();
$tim = $tim - (3600 * 24 * 365);

$sql_s = "select count(*) as sct from otklik where time > $tim";
$result_s = $dbi->query($sql_s);
if($myrow_s = mysqli_fetch_array($result_s)) {
  $cntmp = (int) $myrow_s["sct"];
}

echo "<tr><td class=travel11>Отзывов по объектам</td><td class=travel11>$cntmp</td></tr>\n";

$sql_s = "select count(*) as sct, cook from otklik where time > $tim and cook <> 'N' group by cook order by 1 desc limit 10";
$result_s = $dbi->query($sql_s);
while($myrow_s = mysqli_fetch_array($result_s)) {
  $cntmp = (int) $myrow_s["sct"];
  $cook  = $myrow_s["cook"];
  if (preg_match("/forum\:(\d+)\:(.+)/",$cook,$match)) {
    $cook = $match[2];
  }
  else {
    $cook = "Не участник форума";
  }
  echo "<tr><td class=travel11>&nbsp;</td><td class=travel11>$cook: $cntmp</td></tr>\n";
}

$sql_s = "select count(*) as sct from l_reports where time > $tim";
$result_s = $dbi->query($sql_s);
if($myrow_s = mysqli_fetch_array($result_s)) {
  $cntmp = (int) $myrow_s["sct"];
}
echo "<tr><td class=travel11>Отчетов</td><td class=travel11>$cntmp</td></tr>\n";

$sql_s = "select count(*) as sct, sum(score) as scsum, cook from l_reports where time > $tim and cook <> \"\" group by cook order by 1 desc limit 10";
echo "\n<!-- $sql_s -->\n";
$result_s = $dbi->query($sql_s);
while($myrow_s = mysqli_fetch_array($result_s)) {
  $cntmp = (int) $myrow_s["sct"];
  $cnsum = (int) $myrow_s["scsum"];
  $cook  = $myrow_s["cook"];
  if (preg_match("/forum\:(\d+)\:(.+)/",$cook,$match)) {
#    $cook = "<a href='http://forum.autotravel.ru/profile.php?mode=viewprofile&u=".$match[1]."'>Участник форума: ".$match[2]."</a>";
    $cook = $match[2];
  }
  else {
    $cook = "Не участник форума";
  }
  echo "<tr><td class=travel11>&nbsp;</td><td class=travel11>$cook: $cntmp ($cnsum баллов)</td></tr>\n";
}

$sql_s = "select g.id as id, g.name as gr from group_rep g order by id";
$result_s = $dbi->query($sql_s);
$rx = array();
$rn = array();
if ($myrow_s = mysqli_fetch_array($result_s)) {
    do {
      $rx[$myrow_s["id"]] = 0;
      $rn[$myrow_s["id"]] = $myrow_s["gr"];
    } while ($myrow_s = mysqli_fetch_array($result_s));
}

echo "<tr><td class=travel11>Лучшие по группам</td><td class=travel11></td></tr>\n";
echo "<!--\n";
 var_dump($rn);
echo "\n-->\n";

$sql_s = "select score, rid, cook, group_rep from l_reports where time > $tim order by group_rep asc, score desc";
$result_s = $dbi->query($sql_s);

while($myrow_s = mysqli_fetch_array($result_s)) {
  $gr = (int) $myrow_s["group_rep"];
  $id = (int) $myrow_s["rid"];
  $score = (int) $myrow_s["score"];
  $cook  = $myrow_s["cook"];
  if (preg_match("/forum\:(\d+)\:(.+)/",$cook,$match)) {
#    $cook = "<a href='http://forum.autotravel.ru/profile.php?mode=viewprofile&u=".$match[1]."'>Участник форума: ".$match[2]."</a>";
    $cook = $match[2];
  }
  else {
    $cook = "Не участник форума";
  }
  if ($rx[$gr] < 3) {
    $rx[$gr] = $rx[$gr]+1;
    echo "<tr><td class=travel11>".$rn[$gr]."</td><td class=travel11>$cook: <a href=\"/v_report.php/$id\" target=_blank >$score баллов</a></td></tr>\n";
  }
}
$sql_s = "select count(*) as sct from bl_posts where post_time > $tim";
$result_s = $dbi->query($sql_s);
if($myrow_s = mysqli_fetch_array($result_s)) {
  $cntmp = (int) $myrow_s["sct"];
}
echo "<tr><td class=travel11>Сообщений форума</td><td class=travel11>$cntmp</td></tr>\n";

$sql_s = "select count(*) as sct, u.username as uname from bl_posts p, bl_users u
         where p.post_time > $tim and u.user_id = p.poster_id and p.poster_id > 1 group by u.username order by 1 desc limit 10";
$result_s = $dbi->query($sql_s);
while($myrow_s = mysqli_fetch_array($result_s)) {
  $cntmp = (int) $myrow_s["sct"];
  $cook  = $myrow_s["uname"];
  echo "<tr><td class=travel11>&nbsp;</td><td class=travel11>$cook: $cntmp</td></tr>\n";
}


$sql_s = "select count(*) as sct from foto where file>'$phalbumbeg'";
$result_s = $dbi->query($sql_s);
if($myrow_s = mysqli_fetch_array($result_s)) {
  $cntmp = (int) $myrow_s["sct"];
}
echo "<tr><td class=travel11>Фотографий</td><td class=travel11>$cntmp</td></tr>\n";

$sql_s = "select count(*) as sct, author from foto where file>'$phalbumbeg' group by author order by 1 desc limit 10";
$result_s = $dbi->query($sql_s);
while($myrow_s = mysqli_fetch_array($result_s)) {
  $cntmp = (int) $myrow_s["sct"];
  $cook  = $myrow_s["author"];
  if (preg_match("/forum\:(\d+)\:(.+)/",$cook,$match)) {
    $cook = $match[2];
#    $cook = "<a href='http://forum.autotravel.ru/profile.php?mode=viewprofile&u=".$match[1]."'>Участник форума: ".$match[2]."</a>";
  }
  else {
    $cook = "Не участник форума";
  }
  echo "<tr><td class=travel11>&nbsp;</td><td class=travel11>$cook: $cntmp</td></tr>\n";
}


$sql_s = "select count(*) as sct from ourtrip where datebeg > '$strdate'";
$result_s = $dbi->query($sql_s);
if($myrow_s = mysqli_fetch_array($result_s)) {
  $cntmp = (int) $myrow_s["sct"];
}
echo "<tr><td class=travel11>Совместных поездок</td><td class=travel11>$cntmp</td></tr>\n";

$sql_s = "select count(*) as sct, u.iduser as iduser, u.name as name from ourtrip o, trip_user u where
          (o.datebeg > '$strdate') and (o.id = u.idtrip) and (u.iduser > 0) group by u.iduser order by 1 desc limit 10";
$result_s = $dbi->query($sql_s);
while($myrow_s = mysqli_fetch_array($result_s)) {
  $cntmp = (int) $myrow_s["sct"];
  $iduser  = $myrow_s["iduser"];
  $cook  = $myrow_s["name"];
#  $cook = "<a href='http://forum.autotravel.ru/profile.php?mode=viewprofile&u=".$iduser."'>Участник форума: ".$name."</a>";
  echo "<tr><td class=travel11>&nbsp;</td><td class=travel11>$cook: $cntmp</td></tr>\n";
}
echo "<tr><td class=travel11>В т.ч. организаторы:</td><td class=travel11></td></tr>\n";
$sql_s = "select count(*) as sct, u.iduser as iduser, u.name as name from ourtrip o, trip_user u where
          (o.datebeg > '$strdate') and (o.id = u.idtrip) and (u.iduser > 0) and (u.typeuser = 1)  group by u.iduser order by 1 desc limit 10";
$result_s = $dbi->query($sql_s);
while($myrow_s = mysqli_fetch_array($result_s)) {
  $cntmp = (int) $myrow_s["sct"];
  $iduser  = $myrow_s["iduser"];
  $cook  = $myrow_s["name"];
#  $cook = "<a href='http://forum.autotravel.ru/profile.php?mode=viewprofile&u=".$iduser."'>Участник форума: ".$name."</a>";
  echo "<tr><td class=travel11>&nbsp;</td><td class=travel11>$cook: $cntmp</td></tr>\n";
}


echo "<tr><td class=travel11>Добавили объектов</td><td class=travel11></td></tr>\n";
$sql_s = "select count(*) as sct, cook as author from exciting where id>'$excidbeg' and cook > '' group by cook order by 1 desc limit 10";
$result_s = $dbi->query($sql_s);
while($myrow_s = mysqli_fetch_array($result_s)) {
  $cntmp = (int) $myrow_s["sct"];
  $cook  = $myrow_s["author"];
  if (preg_match("/forum\:(\d+)\:(.+)/",$cook,$match)) {
    $cook = $match[2];
#    $cook = "<a href='http://forum.autotravel.ru/profile.php?mode=viewprofile&u=".$match[1]."'>Участник форума: ".$match[2]."</a>";
  }
  else {
    $cook = "Не участник форума";
  }
  echo "<tr><td class=travel11>&nbsp;</td><td class=travel11>$cook: $cntmp</td></tr>\n";
}


/*
$sql_s = "select count(*) as sct from towns";
$result_s = $dbi->query($sql_s);
if($myrow_s = mysqli_fetch_array($result_s)) {
  $cntmp = (int) $myrow_s["sct"];
}

echo "<tr><td class=travel11>Населённых пунктов</td><td class=travel11>$cntmp</td></tr>\n";

$sql_s = "select count(*) as sct from exciting";
$result_s = $dbi->query($sql_s);
if($myrow_s = mysqli_fetch_array($result_s)) {
  $cntmp = (int) $myrow_s["sct"];
}
echo "<tr><td class=travel11>Объектов</td><td class=travel11>$cntmp</td></tr>\n";

$sql_s = "select count(*) as sct from news";
$result_s = $dbi->query($sql_s);
if($myrow_s = mysqli_fetch_array($result_s)) {
  $cntmp = (int) $myrow_s["sct"];
}
echo "<tr><td class=travel11>Записей о датах или событиях</td><td class=travel11>$cntmp</td></tr>\n";

$dtarr = getdate(time());
$cmonth = (int) mktime(0,0,0,$dtarr["mon"],1,$dtarr["year"]);
$sql_s = "select sum(count) as sct from stats_ex where dt >= $cmonth";
$result_s = $dbi->query($sql_s);
if($myrow_s = mysqli_fetch_array($result_s)) {
  $cntmp = (int) $myrow_s["sct"];
}
*/

echo "</tbody></table>\n";
echo "</div></div>\n";

echo "</div>\n <!-- contaiber -->\n"; 
$_totop = 1; // показать кнопку
include_once("include/footer_bs.php");
?>
