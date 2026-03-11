<?php

 $begun = "?> <script type=\"text/javascript\">
var begun_auto_pad = 442263744;
var begun_block_id = 442272728;
</script>
<script src=\"//autocontext.begun.ru/autocontext2.js\" type=\"text/javascript\"></script>

<?php";
 
$kml = "";

$larr=array("a01"=>"А","a02"=>"Б","a03"=>"В","a04"=>"Г","a05"=>"Д","a06"=>"Е","a07"=>"Ж","a08"=>"З","a09"=>"И","a10"=>"Й",
    "a11"=>"К","a12"=>"Л","a13"=>"М","a14"=>"Н","a15"=>"О","a16"=>"П","a17"=>"Р","a18"=>"С","a19"=>"Т","a20"=>"У",
    "a21"=>"Ф","a22"=>"Х","a23"=>"Ц","a24"=>"Ч","a25"=>"Ш","a26"=>"Щ","a27"=>"Ы","a28"=>"Э","a29"=>"Ю","a30"=>"Я");

$tlist = "";
$sqltx = "";
$menu1 = "<a class=travell5m href=\"/towns.php\">Города России по алфавиту</a>";
$menu2 = "<a class=travell5m href=\"/towns.php?l=3\">Популярные города России</a>";
$menu3 = "<a class=travell5m href=\"/towns.php?l=5\">Список городов России</a>";
$menu4 = "<a class=travell5m href=\"/areas.php\">Области России</a>";
$menu5 = "<a class=travell5m href=\"/countries.php\">Зарубежные страны</a>";
$title2 = "Зарубежные страны";

switch ($l) {
 case "":
  $title = "Города России по алфавиту - достопримечательности, гостиницы, отзывы";
  $title_h = "Города России по алфавиту";
  $menu1 = $title_h;
  $sqltt = "select t.name as town, t.hide as hide, t.rate as rate, t.id as tid, a.name as area from towns t, areas a where a.id > 0 and t.rate > ($thighrate) and a.id=t.area order by t.name";
  $sqltx = "select t.name as town, t.hide as hide, t.rate as rate, t.id as tid, a.name as area from towns t, areas a where a.id < 0 and t.rate > ($thighrate) and a.id=t.area order by t.name";
  $metatags="Города России по алфавиту, список городов России, популярные города России, зарубежные страны, города зарубежных стран";
  $metadesc="Города России и зарубежных стран - как добраться, что посмотреть, где остановиться, где поесть. ";
  $text = " <p class=\"t12 tl travell0t\"><b>Городами</b> здесь называются любые населенные пункты, для краткости.
В списке городов используются такие условные обозначения: <br>
&nbsp;простым шрифтом обозначены города, в которых есть только один объект
(достопримечательность, гостиница, кафе, пункт оказания услуг или т.п.).<br>
&nbsp;<b>Утолщенным шрифтом</b> обозначены города, в которых есть несколько объектов.<br>
&nbsp;Наконец, <font class=travell5m>зеленым цветом</font> выделены популярные города. <br>
Популярные - это те города, в которых либо много объектов, либо у объектов высокий рейтинг,
либо и то и другое в совокупности.<br>Перейдя по ссылке на страницу города Вы сможете увидеть расположение города на карте России или зарубежной страны. Также на карте будут отмечены
достопримечательности города, гостиницы, места питания. Со страницы города можно ознакомиться с отчетами путешественников, посетивших город, узнать
новости и предстоящие праздники, отмечаемые в этом городе. </p>";
 break;
 case "3":
  $title = "Популярные города России - достопримечательности, гостиницы, отзывы";
  $title_h = "Популярные города России";
  $menu2 = $title_h;
  $sqltt = "select t.name as town, t.hide as hide, t.rate as rate, t.id as tid, a.name as area from towns t, areas a where a.id > 0 and t.rate > ($thighrate) and a.id=t.area order by t.name";
  $sqltx = "";
  $metatags="Популярные города России";
  $metadesc="Наиболее посещаемые и любимые путешественниками города России и зарубежных стран - как добраться, что посмотреть, где остановиться, где поесть. ";
  $text = " <p class=\"t12 tl travell0t\"><b>Городами</b> здесь называются любые населенные пункты, для краткости.<br>
Популярные - это те города России, в которых либо много объектов, либо у объектов высокий рейтинг,
либо и то и другое в совокупности. В первую очередь это города России или зарубежных стран наиболее посещаемые и любимые путешественниками.
<br>Перейдя по ссылке на страницу города Вы сможете увидеть расположение города на карте России или зарубежной страны. Также на карте будут отмечены
достопримечательности города, гостиницы, места питания. Со страницы города можно ознакомиться с отчетами путешественников, посетивших город, узнать
новости и предстоящие праздники, отмечаемые в этом городе. </p>";
break;
 case "5":
  if ($_zagr_mode) {
   $title = "Полный список городов России";
   $title_h = "Полный список городов России";
  }
  else {
   $title = "Полный список городов России и зарубежных стран";
   $title_h = "Полный список городов России и зарубежных стран";
  }
  $menu3 = $title_h;
  $sqltt = "select t.name as town, t.hide as hide, t.rate as rate, t.id as tid, a.name as area from towns t, areas a where a.id > 0 and t.hide<>1 and a.id=t.area order by t.name";
  $sqltx = "select t.name as town, t.hide as hide, t.rate as rate, t.id as tid, a.name as area from towns t, areas a where a.id < 0 and t.hide<>1 and a.id=t.area order by t.name";
  $text = " <p class=\"t12 tl travell0t\"><b>Городами</b> здесь называются любые населенные пункты, для краткости.
В списке городов используются такие условные обозначения: <br>
&nbsp;простым шрифтом обозначены города, в которых есть только один объект
(достопримечательность, гостиница, кафе, пункт оказания услуг или т.п.).<br>
&nbsp;<b>Утолщенным шрифтом</b> обозначены города, в которых есть несколько объектов.<br>
&nbsp;Наконец, <font class=travell5m>зеленым цветом</font> выделены популярные города. <br>
Популярные - это те города, в которых либо много объектов, либо у объектов высокий рейтинг,
либо и то и другое в совокупности.<br>Перейдя по ссылке на страницу города Вы сможете увидеть расположение города на карте России или зарубежной страны. Также на карте будут отмечены
достопримечательности города, гостиницы, места питания. Со страницы города можно ознакомиться с отчетами путешественников, посетивших город, узнать
новости и предстоящие праздники, отмечаемые в этом городе. </p>";
 break;
 case "6":
  if ($a) {
   $arean = getareaname($a);
   $title = "$arean: города, популярные достопримечательности, интерактивная карта.";
   $title_h = "$arean: города, популярные достопримечательности, интерактивная карта.";
   $sqltt = "select t.name as town, t.hide as hide, t.rate as rate, t.id as tid  from towns t where t.hide<>1  and t.area = '$a' order by t.name";
   $text = "  ";
  }
  else {
   $menu4 = "Области и регионы России";
   $title_h = $menu4;
   $title = "Области и регионы России: города, достопримечательности, гостиницы";
   $sqltt = "select distinct a.name as area, t.area as aid from towns t, areas a where a.id > 0 and t.hide <> 1 and a.id = t.area order by 1";
   $metatags="Области России, регионы России";
   $metadesc="Области и регионы России - как добраться, что посмотреть, где остановиться, где поесть. ";
   $text = "<p class=\"t12 tl travell0t\">Перейдя по ссылке на страницу конкретной области России
Вы сможете увидеть на интерактивной карте России города области и сможете перейти на страницы этих городов. Также на карте области отмечены
достопримечательности, гостиницы и  места питания. Со страницы области можно узнать
новости региона и предстоящие праздники отмечаемые здесь.</p>";
  }
 break;
 case "7":
  if ($a) {
   $arean = getareaname($a);
   $title = "$arean: города, популярные достопримечательности, интерактивная карта.";
   $title_h = "$arean: города, популярные достопримечательности, интерактивная карта.";
   $sqltt = "select t.name as town, t.hide as hide, t.rate as rate, t.id as tid  from towns t where t.hide<>1  and t.area = '$a' order by t.name";
   $text = "  ";
  }
  else {
   $title = "Зарубежные страны - города, достопримечательности, отзывы";
   $title_h = "Зарубежные страны";
   $menu5 = $title_h;
   $sqltt = "select distinct a.name as area, t.area as aid from towns t, areas a where a.id < 0 and t.hide <> 1 and a.id = t.area order by 1";
   $title2 = "Популярные города";
   $sqltx = "select t.name as town, t.hide as hide, t.rate as rate, t.id as tid, a.name as area from towns t, areas a where a.id < 0 and t.rate > ($thighrate) and a.id=t.area order by t.name";
   $metatags="Города зарубежных стран";
   $metadesc="Зарубежные страны - как добраться, что посмотреть, где остановиться, где поесть";
   $text = "<p class=\"t12 tl travell0t\">Перейдя по ссылке на страницу конкретной зарубежной страны,
Вы сможете увидеть на интерактивной карте города страны и сможете перейти на страницы этих городов. Также на карте области отмечены
достопримечательности, гостиницы и  места питания. Со страницы страны можно узнать
новости страны и предстоящие праздники отмечаемые здесь.</p>";
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

include('include/header.php');

if ($sqltt) {
 $result = $dbi->query($sqltt);
 $numrows = mysqli_num_rows($result);
 $counter = 0;
}
$tbltxt1 = "";
$tbltxt2 = "";

?>

<center>
<table width='100%'>
<tbody class="t14 tb tc">

<tr><td>
<TABLE width='100%' cellpadding=3>
<tbody class="t14 tb tc">
   <tr><td width='20%' align=left><?php echo $menu1; ?></td>
   <td width='20%' align=center><?php echo $menu2; ?></td>
   <td width='20%' align=center><?php echo $menu3; ?></td>
   <td width='20%' align=center><?php echo $menu4; ?></td>
   <td align=right><?php if (!($_zagr_mode)) { echo $menu5; } ?></td></tr>
</tbody>
</table>
</td></tr>

<?php
 echo " <tr><td class=travell01y><p class=\"t12 tl travell0t\">$text</td></tr>\n";
 
 ?>
 

<?php echo "<TR><TD align=center><br><h1>$title_h</h1></td></tr>\n";
 
 $tbltxt1 = "";
 $tbltxt2 = "";
 $tbltxt3 = "";

 if (!($l)) {
  $l = 3;
  ?>


  <TR><TD align=center>
      <font class=travell0> Выберите букву алфавита и перейдите к списку городов, отфильтрованному по этой букве.</font>
  </TD></TR>
  <TR><TD align=center>
          
          <tr><td colspan=2 align=center>
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- Автотревел, таунс реклама -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-2266145788867998"
     data-ad-slot="4585890667"
     data-ad-format="auto"></ins>
<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
</td></tr>
  <?php
  foreach ($larr as $lkey=>$lvalue) {
   echo "<b><a class=travell5m href=\"/towns.php/$lkey\"> $lvalue </a></b>&nbsp;\n";
  }
  ?>
            </TD></TR>


  <TR><TD><h2>Популярные города России</h2></TD></TR>


 

  <?php
 }

 if ($sqltt) {
// всё остальное.
  if (($l == "6")||($l == "7")) {// города по областям
   if($myrow = mysqli_fetch_array($result)) {
     do {
      $arean = $myrow["area"];
      $aid = $myrow["aid"];
      if ($counter < $numrows) {
       $tbltxt1 = $tbltxt1 . "<a class=travell5 href=\"/area.php/$aid\"> $arean </a><br>";
      }
      else {
       $tbltxt2 = $tbltxt2 . "<a class=travell5 href=\"/area.php/$aid\"> $arean </a><br>";
      }
      $counter++;
      $counter++;
     } while($myrow = mysqli_fetch_array($result));
   } // список областей
  } // города по областям


  else {
   if ($l) { // полный список и список на букву отображаются одинаково
    if($myrow = mysqli_fetch_array($result)) {
     do {
      $s_town = cyrname($myrow["town"]);
      $s_area = $myrow["area"];
      $s_hide = $myrow["hide"];
      $s_townid = $myrow["tid"];
      if ($s_hide == 2) {
        $class="travell5s";
      }
      else {
        $class="travell5";
      }
      $s_rate = $myrow["rate"];
      if ($l == "3") {// популярные
       if ($tlist) {
        $tlist .= ",$s_townid";
       }
       else {
        $tlist = "$s_townid";
       }
      }
      if ($s_rate > ($thighrate)) {
        $class = "travell5c";
      }
      


      if ($counter < $numrows) {
       $tbltxt1 = $tbltxt1 . "<a class=$class href=\"/town.php/$s_townid\"> $s_town </a> <font class=travell0>($s_area)</font><BR>\n";
      }
      else {
       $tbltxt2 = $tbltxt2 . "<a class=$class href=\"/town.php/$s_townid\"> $s_town </a> <font class=travell0>($s_area)</font><BR>\n";
      }
      $counter++;
      $counter++;
     } while($myrow = mysqli_fetch_array($result));
    }
   }
   else {
    echo "Этой страницы не существует";
   }
  }
 } // буквы или sql



 if ($tbltxt1) {
       
  echo "<TR><TD class=travell5>";
  echo "<div align=center>\n";
  echo "<Table Border=0 Cellspacing=0 cellpadding=3 width='90%'><tr><td width='50%' align = left>\n";
  echo "$tbltxt1\n";
  echo "</TD><TD width='50%' align=left>\n";
  echo "$tbltxt2\n";
  echo "</TD></TR>\n";
 }

 $tbltxt3 = "";
 $tbltxt4 = "";
 if ((!($_zagr_mode)) and ($sqltx)) {
  $result = $dbi->query($sqltx);
  $numrows = mysqli_num_rows($result);
  $counter = 0;
  if ($l == "6") {// города по областям
                  // список областей
    if($myrow = mysqli_fetch_array($result)) {
     do {
      $arean = $myrow["area"];
      $aid = $myrow["aid"];
      if ($counter < $numrows) {
       $tbltxt3 = $tbltxt3 . "<a class=travell5 href=\"/area.php/$aid\"> $arean </a><br>";
      }
      else {
       $tbltxt4 = $tbltxt4 . "<a class=travell5 href=\"/area.php/$aid\"> $arean </a><br>";
      }
      $counter++;
      $counter++;
     } while($myrow = mysqli_fetch_array($result));
     mysqli_free_result($result);
    }

  } // города по областям
  else {
   if ($l) { // полный список и список на букву отображаются одинаково
    if($myrow = mysqli_fetch_array($result)) {
     do {
      $s_town = cyrname($myrow["town"]);
      $s_area = $myrow["area"];
      $s_hide = $myrow["hide"];
      $s_townid = $myrow["tid"];
      if ($s_hide == 2) {
        $class="travell5s";
      }
      else {
        $class="travell5";
      }
      $s_rate = $myrow["rate"];
      if ($s_rate > ($thighrate)) {
        $class = "travell5c";
      }
      if ($counter < $numrows) {
       $tbltxt3 = $tbltxt3 . "<a class=$class href=\"/town.php/$s_townid\"> $s_town </a> <font class=travell0>($s_area)</font><BR>\n";
      }
      else {
       $tbltxt4 = $tbltxt4 . "<a class=$class href=\"/town.php/$s_townid\"> $s_town </a> <font class=travell0>($s_area)</font><BR>\n";
      }
      $counter++;
      $counter++;
     } while($myrow = mysqli_fetch_array($result));
     mysqli_free_result($result);
    }
   }
  }
 }

 if ($tbltxt3) {
   echo "<tr><td colspan=2 align=left><hr><h2>$title2</h2></td></tr>\n";
    ?>


<?php
   echo "<tr><td width=50%>$tbltxt3</td><td width=50%>$tbltxt4</td></tr>\n";
 }
 echo "</TABLE>\n</div>\n</Table>\n</center>";

?>
