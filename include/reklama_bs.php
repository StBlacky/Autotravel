<?php

$maxstrings = 8; // всего рекламы
$upper = 6; // выше гугля = 5 == 6-1
$yesterday = date('Y-m-d',(time() - (24*3600)));
$sql = "select e.name as name, t.name as town, e.id as id, Right( e.name, length( e.name ) - locate( '\"', e.name )) AS loc, r.reklama as rek "
        . "from towns t, exciting e, reklama r where t.id = e.town and r.ex = e.id and r.srock > '$yesterday' and r.auth > 0 "
        . "order by r.dtizm desc limit $maxstrings";
$result =$dbi->query($sql);



/** 07.02.2022 года Партнеры отключены потому что кончились

//третий столбец главной таблицы - реклама
echo "<h2 class='tu2 neucha'>Нас приглашают</h2>\n";
echo "<div class=\"tl travell9c\"><h4 class=\"neucha\"><a href=\"/partner.php\" rel=\"nofollow\">";
echo "Вход для партнеров >>>";
echo "</a></h4></div>\n";
echo "<div class=\"right-reklama\">";
echo "<ul>\n";
$upperpart="";
$lowerpart="";
$ii=0;

if ($myrow = mysqli_fetch_array($result)) {
  do {
  	$e_name = $myrow["name"];
  	$e_id = $myrow["id"];
  	if ($myrow["rek"]) {
      $e_rek = preg_replace('@<script[^>]*?>.*?</script>@si', '', $myrow["rek"]);
      $e_rek = preg_replace("/\<br\>\<br\>/i","<br>",$myrow["rek"]);
	    $excerpt_length = 20;
      $words = explode(' ', $e_rek, $excerpt_length + 1);
      if (count($words)> $excerpt_length) {
       	array_pop($words);
        $e_rek = implode(' ', $words);
      }
    } else { $e_rek = "";}
    $e_rek = closetags($e_rek) . " ...";
    $e_town = $myrow["town"];
    $currentpart="<li>";
    $currentpart.="<h6 class='travell5n'><a href='/otklik.php/$e_id'>$e_name ($e_town)</a></h6>\n";
    $currentpart.="$e_rek</li>";
    $ii++;
    if ($ii<$upper) {
      $upperpart.=$currentpart;
    }
    else {
      $lowerpart.=$currentpart;
    }
  } while($myrow = mysqli_fetch_array($result));
}
 
echo $upperpart;
echo "</ul>\n";
if ($lowerpart) {
  echo "<ul>";
  echo $lowerpart;
  echo "</ul>";
}
*/

//раздел куда поехать
$sql_event = "SELECT * FROM `news` WHERE `calendar` >= CURDATE() ORDER BY `calendar` ASC";
$result_event = $dbi->query($sql_event);


  echo '<div class="hr"></div>';  
  echo "<h2 id=\"events\" class =\"tu2 neucha\">Куда поехать</h2>";
  echo "<a class =\"btn-main\" href=\"/basket_v.php\" style=\"width:100%; font-size:1.2em; padding: 10px 0px; margin: 10px 0px; box-shadow: 0 3px 8px rgba(0, 0, 0, 0.10);\">Создай свою поездку</a>";
 if($result_event) { 
  echo "<h4 class =\"tu2 neucha\">&nbsp;событийный туризм</h4>";
  echo "<ul><div class=\"bodyline-events\">\n";
  $dateprev = "";
  while ($myrow_event = mysqli_fetch_array($result_event)) {
    $id = $myrow_event["id"];
    $date = $myrow_event["calendar"];
    $datex = strftime("%A %d.%m.%Y", strtotime($date));
    $title = $myrow_event["title"];
    $excite = $myrow_event["exciting"];
    if ($datex <> $dateprev) {
        echo "<p class=\"left-column neucha breadcrumb\">$datex</p>";
        $dateprev = $datex;
    }
    if ($excite) {
      echo "<h6 class=travell5n>" . news_excite($excite). "   </h6>";
    }
    echo "<p class=travell0><a href='/news.php?id=$id' rel=\"nofollow\">$title</a></p>"; 
     
  } 
  mysqli_free_result($result);
echo "</div></ul>";
} ?>
 <!--раздел куда поехать -->
 
 <div class="hr"></div>
<h2 class="tu2 neucha">
  <a href="/triptrackm.php/d1">Маршруты выходного дня</a>
</h2>

<ul>
<div class="bodyline-events">
<?php
$sql = "
  SELECT id, title
  FROM trip_desc
  WHERE d = 1
  ORDER BY id DESC
  LIMIT 5
";

$res = $dbi->query($sql);
while ($row = $res->fetch_assoc()) {
  $id = (int)$row['id'];
  $title = htmlspecialchars($row['title']);

  echo "<p class=\"travell0\">
          <a href=\"/trip_rec.php/$id\">$title</a>
        </p>";
}
?>
</div>
</ul>
<div class="hr"></div>
<h2 class="tu2 neucha">
  <a href="/triptrackm.php/d2">Праздничные маршруты</a>
</h2>

<ul>
<div class="bodyline-events">
<?php
$sql = "
  SELECT id, title
  FROM trip_desc
  WHERE d = 2
  ORDER BY id DESC
  LIMIT 5
";

$res = $dbi->query($sql);
while ($row = $res->fetch_assoc()) {
  $id = (int)$row['id'];
  $title = htmlspecialchars($row['title']);

  echo "<p class=\"travell0\">
          <a href=\"/trip_rec.php/$id\">$title</a>
        </p>";
}
?>
</div>
</ul>



 
<!--раздел рейтинги-->
<h2 class='tu2 neucha'>Наши рейтинги</h2>
<div class='top-left'>
         <a href="/top100.php"><img src='/jpg/bkcgrRUS.png' alt='ТОП-100 достопримечательностей России' title='ТОП-100 достопримечательностей России по версии сайта По России на авто'></a>
         <a href="/top100.php?z=1"><img src='/jpg/bckgrEUR.png' alt='ТОП-100 достопримечательностей за пределами России' title='ТОП-100 достопримечательностей стран мира по версии сайта По России на авто'></a>
     </div>  
</div>

</div>
