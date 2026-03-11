<?php

$kml = "";

//блок выбора буквы
$larr=array("a01"=>"А","a02"=>"Б","a03"=>"В","a04"=>"Г","a05"=>"Д","a06"=>"Е","a07"=>"Ж","a08"=>"З","a09"=>"И","a10"=>"Й",
    "a11"=>"К","a12"=>"Л","a13"=>"М","a14"=>"Н","a15"=>"О","a16"=>"П","a17"=>"Р","a18"=>"С","a19"=>"Т","a20"=>"У",
    "a21"=>"Ф","a22"=>"Х","a23"=>"Ц","a24"=>"Ч","a25"=>"Ш","a26"=>"Щ","a27"=>"Ы","a28"=>"Э","a29"=>"Ю","a30"=>"Я");
$litstr = "<div><h2>";
foreach ($larr as $lkey=>$lvalue) {
   $litstr .= "<b><a class=travell5m href=\"/towns.php/$lkey\"> $lvalue </a></b>&nbsp;\n";
}
$litstr .= "</h2>
      <p class=travell0 align=center> Выберите букву алфавита и перейдите к списку городов, отфильтрованному по этой букве.</p>
  </div>";
  
$tlist = "";
$sqltx = "";
if ($_zagr_mode) {$menuz = "Полный список городов России";}
else {$menuz = "Полный список городов России и зарубежных стран";}

$menu1 = "<a class=travell5m href=\"/towns.php\">Города России по алфавиту</a>";
$menu2 = "<a class=travell5m href=\"/towns.php?l=3\">Популярные города России</a>";
$menu3 = "<a class=travell5m href=\"/towns.php?l=5\">$menuz</a>";
$menu4 = "<a class=travell5m href=\"/areas.php\">Области России</a>";
$menu5 = "<a class=travell5m href=\"/countries.php\">Зарубежные страны</a>";
$title2 = "Зарубежные страны";
$texttownsr = " <p class=\"t12 tl travell0t\"><strong>Городами</strong> здесь называются любые населенные пункты России, для краткости.
В списке городов используются такие условные обозначения: </p>
<ul class=\"t12 tl travell0t\" type=none>
<li>простым шрифтом обозначены города, в которых есть только один объект
(достопримечательность, гостиница, кафе, пункт оказания услуг или т.п.)</li>
<li><b>yтолщенным шрифтом</b> обозначены города, в которых есть несколько объектов</li>
<li><font class=travell5m>зеленым цветом</font> выделены популярные города</li>
</ul>
<p class=\"t12 tl travell0t\"><font class=travell5m>Популярные</font> - это те города, в которых либо много объектов (в первую очередь - достопримечательностей), либо у объектов высокий рейтинг,
либо и то и другое в совокупности.<br>Перейдя по ссылке на страницу города Вы сможете увидеть расположение города на интерактивной карте России. Также на карте будут отмечены
достопримечательности города, гостиницы, места питания. Со страницы города можно ознакомиться с отчетами путешественников, посетивших город, узнать
новости и предстоящие праздники, отмечаемые в этом городе. </p>";

$texttownsall = "<p class=\"t12 tl travell0t\"><strong>Городами</strong> здесь называются любые населенные пункты, для краткости.
В списке городов используются такие условные обозначения: </p>
<ul class=\"t12 tl travell0t\" type=none>
    <li>простым шрифтом обозначены города, в которых есть только один объект
(достопримечательность, гостиница, кафе, пункт оказания услуг или т.п.)</li>
<li><b>yтолщенным шрифтом</b> обозначены города, в которых есть несколько объектов</li>
<li><font class=travell5m>зеленым цветом</font> выделены популярные города</li>
</ul>
<p class=\"t12 tl travell0t\"><font class=travell5m>Популярные</font> - это те города, в которых либо много объектов (в первую очередь - достопримечательностей), либо у объектов высокий рейтинг,
либо и то и другое в совокупности.<br>Перейдя по ссылке на страницу города Вы сможете увидеть расположение города на интерактивной карте России. Также на карте будут отмечены
достопримечательности города, гостиницы, места питания. Со страницы города можно ознакомиться с отчетами путешественников, посетивших город, узнать
новости и предстоящие праздники, отмечаемые в этом городе. </p>";

$textpopular = " <p class=\"t12 tl travell0t\"><strong>Городами</strong> здесь называются любые населенные пункты России, для краткости.</p>
<p class=\"t12 tl travell0t\"><strong>Популярные</strong> - это те города России, в которых либо много объектов (в первую очередь - достопримечательностей), либо у объектов высокий рейтинг,
либо и то и другое в совокупности. Как правило,  это города России или зарубежных стран наиболее посещаемые и любимые путешественниками.</p>
<p class=\"t12 tl travell0t\">Перейдя по ссылке на страницу города Вы сможете увидеть расположение города на интерактивной карте. Также на карте будут отмечены
достопримечательности города, гостиницы, места питания. Со страницы города можно ознакомиться с отчетами путешественников, посетивших город, узнать
новости и предстоящие праздники, отмечаемые в этом городе. </p>";

$textabroad = "<p class=\"t12 tl travell0t\">Перейдя по ссылке на страницу конкретной <strong>зарубежной страны</strong>,
Вы сможете увидеть на интерактивной карте города и сможете перейти на страницы этих городов. Также на карте страны отмечены
достопримечательности, гостиницы и  места питания. Со страницы страны можно узнать
новости страны и предстоящие праздники отмечаемые здесь.</p>
<p class=\"t12 tl travell0t\">Ниже списка стран мира представлен список <strong>популярных городов</strong>. <font class=travell5m>Популярные</font> - это те города, в которых либо много объектов (в первую очередь - достопримечательностей), либо у объектов высокий рейтинг,
либо и то и другое в совокупности.<br>Перейдя по ссылке на страницу города Вы сможете увидеть расположение города на интерактивной карте России. Также на карте будут отмечены
достопримечательности города, гостиницы, места питания. Со страницы города можно ознакомиться с отчетами путешественников, посетивших город, узнать
новости и предстоящие праздники, отмечаемые в этом городе. </p>";

switch ($l) {
 case "": //не передан ни один ключ
  $title = "Города России по алфавиту - достопримечательности, гостиницы, отзывы";
  $title_h = "Города России по алфавиту";
  $menu1 = "<strong>".$title_h."</strong>";
  $sqltt = "select t.name as town, t.hide as hide, t.rate as rate, t.id as tid, a.name as area from towns t, areas a where a.id > 0 and t.rate > ($thighrate) and a.id=t.area order by t.name";
  $sqltx = "select t.name as town, t.hide as hide, t.rate as rate, t.id as tid, a.name as area from towns t, areas a where a.id < 0 and t.rate > ($thighrate) and a.id=t.area order by t.name";
  $metatags="Города России по алфавиту, список городов России, популярные города России";
  $metadesc="Города России - как добраться, что посмотреть, где остановиться, где поесть, интерактивная карта города. ";
  $text = $texttownsr;
 break;
 case "3": // популярные города России
  $title = "Популярные города России - достопримечательности, гостиницы, отзывы";
  $title_h = "Популярные города России";
  $menu2 = "<strong>".$title_h."</strong>";
  $sqltt = "select t.name as town, t.hide as hide, t.rate as rate, t.id as tid, a.name as area from towns t, areas a where a.id > 0 and t.rate > ($thighrate) and a.id=t.area order by t.name";
  $sqltx = "";
  $metatags="Популярные города России";
  $metadesc="Наиболее посещаемые и любимые путешественниками города России и зарубежных стран - как добраться, что посмотреть, где остановиться, где поесть. ";
  $text = $textpopular;
break;
 case "5": //полная солянка - для тех, кому не лень смотреть весь список
  if ($_zagr_mode) {
   $title = "Полный список городов России";
   $title_h = "Полный список городов России";
  }
  else {
   $title = "Полный список городов России и зарубежных стран";
   $title_h = "Полный список городов России и зарубежных стран";
  }
  $menu3 = "<strong>".$title_h."</strong>";
  $sqltt = "select t.name as town, t.hide as hide, t.rate as rate, t.id as tid, a.name as area from towns t, areas a where a.id > 0 and t.hide<>1 and a.id=t.area order by t.name";
  $sqltx = "select t.name as town, t.hide as hide, t.rate as rate, t.id as tid, a.name as area from towns t, areas a where a.id < 0 and t.hide<>1 and a.id=t.area order by t.name";
  $text = $texttownsall;
 break;
 case "6":
  if ($a) { //переход на страницу area.php
   $arean = getareaname($a);
   $title = "$arean: города, популярные достопримечательности, интерактивная карта.";
   $title_h = "$arean: города, популярные достопримечательности, интерактивная карта.";
   $sqltt = "select t.name as town, t.hide as hide, t.rate as rate, t.id as tid  from towns t where t.hide<>1  and t.area = '$a' order by t.name";
   $text = "  ";
  }
  else { //список областей России
   $title_h = "Области и регионы России";
   $menu4 = "<strong>".$title_h."</strong>";
   $title = "Области и регионы России: города, достопримечательности, гостиницы";
   $sqltt = "select distinct a.name as area, t.area as aid from towns t, areas a where a.id > 0 and t.hide <> 1 and a.id = t.area order by 1";
   $metatags="Области России, регионы России";
   $metadesc="Области и регионы России - как добраться, что посмотреть, где остановиться, где поесть. ";
   $text = "<p class=\"t12 tl travell0t\">Перейдя по ссылке на страницу конкретной области России
Вы сможете увидеть на интерактивной карте России города и достопримечательности области, сможете перейти на страницы этих городов и достопримечательностей. Также на карте области отмечены
гостиницы и  места питания. Со страницы области можно узнать новости региона и предстоящие праздники отмечаемые здесь.</p>";
  }
 break;
 case "7":
  if ($a) {//переход на страницу зарубежной страны - area.php
   $arean = getareaname($a);
   $title = "$arean: города, популярные достопримечательности, интерактивная карта.";
   $title_h = "$arean: города, популярные достопримечательности, интерактивная карта.";
   $sqltt = "select t.name as town, t.hide as hide, t.rate as rate, t.id as tid  from towns t where t.hide<>1  and t.area = '$a' order by t.name";
   $text = "  ";
  }
  else { //переход на countries.php
   $title = "Зарубежные страны - города, достопримечательности, отзывы";
   $title_h = "Зарубежные страны";
   $menu5 = "<strong>".$title_h."</strong>";
   $sqltt = "select distinct a.name as area, a.id as aid, a.capital as capital from areas a where a.id < 0 order by 1";
   $title2 = "Популярные города";
   $sqltx = "select t.name as town, t.hide as hide, t.rate as rate, t.id as tid, a.name as area from towns t, areas a where a.id < 0 and t.rate > ($thighrate) and a.id=t.area order by t.name";
   $metatags="Города зарубежных стран";
   $metadesc="Зарубежные страны - как добраться, что посмотреть, где остановиться, где поесть";
   $text = $textabroad;
  }
 break;
 default:
  $ll="";
  if (substr($l,0,1)=="a") {
    $ll=$larr[$l];  
  }
  else {
    $ll=$l;
  }
  if ($_zagr_mode) {
   $title = "Города России на букву $ll";
   $title_h = "Города России на букву $ll";
  }
  else {
   $title = "Города России и зарубежных стран на букву $ll";
   $title_h = "Города России и зарубежных стран на букву $ll";
  }
  $sqltt = "select t.name as town, t.hide as hide, t.rate as rate, t.id as tid, a.name as area from towns t, areas a where a.id > 0 and t.hide<>1 and t.name like '$ll%' and a.id=t.area order by t.name";
  $sqltx = "select t.name as town, t.hide as hide, t.rate as rate, t.id as tid, a.name as area from towns t, areas a where a.id < 0 and t.hide<>1 and t.name like '$ll%' and a.id=t.area order by t.name";
  $text = "
  ";
 break;
}

if ($sqltt) {
 $result = mysql_query($sqltt, $db);
 $numrows = mysql_num_rows($result);
 $counter = 0;
}
$tbltxt1 = "";
$tbltxt2 = "";

?>

<div class="container-fluid" style="width: 100%"> <!--  средняя область -->
    
<div> <!--  строка меню -->        
<ul class="menuline">
    <li><?php echo $menu1; ?></li>
    <li><?php echo $menu2; ?></li>
    <li><?php echo $menu3; ?></li>
    <li><?php echo $menu4; ?></li>
    <li><?php if (!($_zagr_mode)) { echo $menu5; } ?></li>
</ul>
</div> <!--  строка меню -->        

<?php echo " <div><h1>$title_h</h1><p class=\"t12 tl travell0t\">$text</div>"; //раздел комментария к разделу  ?>

<div style="padding: 10px; text-align: center;"> 
<!--  Автотревел, таунс реклама -->     
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-2266145788867998"
     data-ad-slot="4585890667"
     data-ad-format="auto"></ins>
<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
</div> <!-- Блок рекламы Автотревел, таунс реклама -->

<?php 
 $tbltxt1 = "";
 $tbltxt2 = "";
 $tbltxt3 = "";

 if (!($l)) {  $l = 3; //если ничего, то популярные города
    echo "<h2>Популярные города России</h2>";
  }

 $capitalname = "";
 if ($sqltt) {
// всё остальное.
  if (($l == "6")||($l == "7")) {// города по областям
   if($myrow = mysql_fetch_array($result)) {
     do {
      $arean = $myrow["area"];
      $aid = $myrow["aid"];
      if ($l == "7") {
          $capital = $myrow["capital"];
          if ($capital) {
              $capitalname = " (столица  <a href=\"/town.php/$capital\">".gettownnamearr($capital)[3]."</a>)";
          } 
      }    
      $tbltxt1 = $tbltxt1 . "<a class=travell5 href=\"/area.php/$aid\"> $arean </a>$capitalname<br>";
      $capitalname='';
     } while($myrow = mysql_fetch_array($result));
   } // список областей
  } // города по областям
  else {
   if ($l) { // полный список и список на букву отображаются одинаково
    if($myrow = mysql_fetch_array($result)) {
     do {
      $s_town = cyrname($myrow["town"]);
      $s_area = $myrow["area"];
      $s_hide = $myrow["hide"];
      $s_townid = $myrow["tid"];
      if ($s_hide == 2) {$class="travell5s";} else {$class="travell5";}
      $s_rate = $myrow["rate"];
      if ($l == "3") {// популярные
           if ($tlist) {$tlist .= ",$s_townid";} else {$tlist = "$s_townid";}
      }
      if ($s_rate > ($thighrate)) {$class = "travell5c";}
      
      $tbltxt1 = $tbltxt1 . "<a class=$class href=\"/town.php/$s_townid\"> $s_town </a> <font class=travell0>($s_area)</font><BR>\n";
     } while($myrow = mysql_fetch_array($result));
    }
   }
   else { echo "<div>Этой страницы не существует</div>";}
  }
 } // буквы или sql



 if ($tbltxt1) {
     if ($l<6) {echo "$litstr";}
     echo "<div class=column>$tbltxt1</div>"; } //если чего нашли, отразили в диве

 if ((!($_zagr_mode)) and ($sqltx)) {
      $result = mysql_query($sqltx, $db);
      if ($l == "6") {// города по областям
            if($myrow = mysql_fetch_array($result)) {
                do {
                    $arean = $myrow["area"];
                    $aid = $myrow["aid"];
                    $tbltxt3 = $tbltxt3 . "<a class=travell5 href=\"/area.php/$aid\"> $arean </a><br>";
                  } while($myrow = mysql_fetch_array($result));
                  mysql_free_result($result);
              }
        }  else {
            if ($l) { // полный список и список на букву отображаются одинаково
                if($myrow = mysql_fetch_array($result)) {
                    do {
                        $s_town = cyrname($myrow["town"]);
                        $s_area = $myrow["area"];
                        $s_hide = $myrow["hide"];
                        $s_townid = $myrow["tid"];
                        if ($s_hide == 2) {$class="travell5s";} else {$class="travell5";}
                        $s_rate = $myrow["rate"];
                        if ($s_rate > ($thighrate)) {$class = "travell5c";}
                        $tbltxt3 = $tbltxt3 . "<a class=$class href=\"/town.php/$s_townid\"> $s_town </a> <font class=travell0>($s_area)</font><BR>\n";
                    } while($myrow = mysql_fetch_array($result));
                    mysql_free_result($result);
                }
            }
        }
 }

 if ($tbltxt3) {
   echo "<h2>$title2</h2>";
   echo "<div class=column>$tbltxt3</div>";
   if ($l == "7") {
     echo '<div style="text-align: center;"><a href="https://partner.ingos.ru/scripts/ahch0f3?affid=autotravel&amp;b=d4f0959b" target="_top"><img src="//partner.ingos.ru/accounts/default1/ahbh0f3/d4f0959b.gif" alt="" title="" width="728" height="90" /></a><img style="border:0" src="https://partner.ingos.ru/scripts/ahih0f3?affid=autotravel&amp;b=d4f0959b" width="1" height="1" alt="" /></div>';
   }   
 }
 echo "</div>"; //конец средней области

?>
