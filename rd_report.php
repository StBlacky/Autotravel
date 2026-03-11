<?php                                 
include_once('include/head00.php');
$bootstrap=1;
$masonry = 1;
//страница отчетов может быть с разными параметрами 
$ids     = $_REQUEST["ids"];
$submitt = $_REQUEST["submitt"];

$search = $_REQUEST["search"];
$search = mysafestring($search);

$hl = $_REQUEST["hl"];
$hl = mysafestring($hl);

$author = $_REQUEST["author"];
$author = mysafestring($author);

$city = $_REQUEST["city"];
$city = trim(mysafestring($city));

$selyear = $_REQUEST["selyear"];
$selyear = (int) mysafestring($selyear);

$selmonth = $_REQUEST["selmonth"];
$selmonth = mysafestring($selmonth);

if ($ids) {//города
 $eids = explode(',',$ids);
 for ($ii=0; $ii<sizeof($eids); $ii++) {
  $eids[$ii] = (int)$eids[$ii];
 }
 $ids = join($eids,",");
 $city = gettownnames($ids);
}
else {
 if ($city) { //передано имя города, а не id - ищем id
  $ids = getskeyids($city);
  if (!($ids)) {
   $ids = "-1";
  }
 }
}

// echo "b" . $ids . "b";

// echo "b" . $ids . "b";

$rtype=array();

$newrtype = $_REQUEST["rtype"];
if (!($newrtype)) {
 $rtype=array('2');
}
else {
 if (is_array($newrtype)) {
  reset ($newrtype);
  while (list ($key, $val) = each ($newrtype)) {
     $rtype[] = (int) mysafestring($val);
  }
 }
 else {
  $rtype[] = (int) mysafestring($newrtype);
 }
}


$rtypes=join($rtype,',');

if (!($submitf)) {
 $search="";
}

if ($search) {
 $search=trim($search);
 if (($submitf) and (mb_strlen($search)<3)) {
  $search="";
  $message="Слишком короткое слово";
 }
}

if ($author) {
 if (preg_match("/forum\:(\d+)\:(.+)/",$author,$s_match)) {
   $authorname = $s_match[2];
   $sqlauthor=" and l.cook like '$author' ";
 }
 else {
  $author     = addslashes(htmlspecialchars($author,NULL,''));
  $sqlauthor  = " and l.nick like '$author' and (l.cook not like 'forum:%') ";
  $authorname = $author;
 }
 $authortxt="<option>\n";
}
else {
 $sqlauthor=" ";
 $authortxt="<option SELECTED>\n";
}


$rtype_txt="";
$rtype_ar =array();
$sql="select id, name from group_rep order by id asc";
$result = $dbi->query($sql);
   if($myrow = mysqli_fetch_array($result)) {
    	do {
	 $id = $myrow["id"];
	 $name = $myrow["name"];
         $rtype_ar[$id]=$name;
	 if (in_array($id,$rtype)) {
	  $rtype_txt = $rtype_txt."<div class = \"checkbox\"><label><input type=checkbox name=rtype[] value='$id' CHECKED>$name</label></div>";
	 }
	 else {
    	  $rtype_txt = $rtype_txt."<div class = \"checkbox\"><label><input type=checkbox name=rtype[] value='$id'>$name</label></div>";
	 }
	} while($myrow = mysqli_fetch_array($result)); 
    }
mysqli_free_result($result);

if ($selyear == 1) {
  $sqlyear=" ";
}
else {
  $sqlyear=" and l.year in ('".(string)((int)date("Y")-1)."','".date("Y")."') ";
}


if (!$selmonth) {
 if (( ((int)date("m"))<4 ) or ( ((int)date("m"))>9 ) ) {
   $sqlmonth=" and l.month in ('01','02','03','10','11','12') ";
   $selmonth=1;
 }
 else {
   $sqlmonth=" and l.month in ('04','05','06','07','08','09') ";
   $selmonth=2;
 }
}
else {
 $selmonth=(int)$selmonth;
 if ($selmonth == 1) {
   $sqlmonth=" and l.month in ('01','02','03','10','11','12') ";
 }
 else {
  if ($selmonth == 2) {
   $sqlmonth=" and l.month in ('04','05','06','07','08','09') ";
  }
  else {
   $sqlmonth=" ";
  }
 }
  
}

$o_key = "";
if ($ids) {
  $o_key = "";
  $sql_r = "select distinct rid from t_reports where tid in ($ids) and rid>0";
  $result_r = $dbi->query($sql_r);
  while($myrow_r = mysqli_fetch_array($result_r)) {
   $name = $myrow_r["rid"];
   if ($o_key) {
    $o_key = $o_key . ", $name";
   }
   else {
    $o_key = $name;
   }
  }
  mysqli_free_result($result_r);
}
if ($o_key) {
 $sqlfr = " and rid in ($o_key) ";
}
else {
 if ($city) {
  $sqlfr = " and l.key_towns like '%$city%' ";
 }
 else {
  if ($ids == "-1") {
   $sqlfr = " and (rid = -1) ";
  }
 }
}

//считаем количество отчетов
$sql_xnt = "select count(*) as cnt from l_reports";
$rep_cnt = 100;
$result_clm = $dbi->query($sql_xnt);
if($myrow_clm = mysqli_fetch_array($result_clm)) { 
    $rep_cnt = $myrow_clm["cnt"];
}
$rep_cnt = round(($rep_cnt / 100) - 1) * 100;
//посчитали, можем использовать где хотим, в первую очередь - в заголовке
$metadesc = "На сайте размещено более $rep_cnt отчётов о самостоятельных путешествиях по России и зарубежным странам. На первую страницу выведены самые свежие отчет. Для удобства поиска по базе отчетов мы предлагаем удобный фильтр по сезонам и направлениям путешествий.";

if ($submitt) {
 $sql_clm = "select l.rid as rid, l.key_towns as key_towns, l.nick as nick, 
             l.month as month, l.year as year, l.group_rep as group_rep, 
             l.ip as ip, l.time as time, l.cook as cook, l.spring as spring, l.score as score
             from l_reports l
        where rid>0 and group_rep in ($rtypes) 
        $sqlyear
        $sqlmonth
        $sqlauthor
        $sqlfr
        order by l.group_rep asc, l.year desc, l.month desc";
 $title ="Отчёты о путешествиях";
 $header="";
}
else {
 if ($search) {
  $sql_clm = "select l.rid as rid, l.key_towns as key_towns, l.nick as nick, 
             l.month as month, l.year as year, l.group_rep as group_rep, 
             l.ip as ip, l.time as time, l.cook as cook, l.spring as spring, l.score as score
             from l_reports l
        where rid>0 and group_rep in ($rtypes) 
        $sqlyear
        $sqlmonth
        $sqlauthor
        $sqlfr
        order by l.group_rep asc, l.year desc, l.month desc";
  $title ="Отчёты о путешествиях";
  $header="";
 }
 else {
  $sql_clm = "select rid, key_towns, nick, month, year, group_rep, ip, time, cook, spring, score  from l_reports where rid>0 order by time desc limit 10";
  $title ="Отчёты о путешествиях";
  $header="Новые отчёты";
 }
}


// вывод на экран
include('include/header_bs.php');
echo "<div class=\"container-fluid\" style=\"width:90%;\">"; //весь контейнер
echo "\n<!-- $sql_clm -->\n";

$out="";
$result_clm = $dbi->query($sql_clm);
$flagresult=0;
$currentype=0;
   if($myrow_clm = mysqli_fetch_array($result_clm)) { 
     do {
         $s_ip = $myrow_clm["ip"];
         $s_t = $myrow_clm["time"];
         $s_date = date('d.m.Y', $s_t);
         $s_key = $myrow_clm["key_towns"];
         $rid   = $myrow_clm["rid"];
         $s_key = getskeytext($s_key,$rid);
         $rept = $myrow_clm["group_rep"];
         $s_spring = $myrow_clm["spring"];
         $s_month = $myrow_clm["month"];
         $s_cook  = $myrow_clm["cook"];
         $s_year = $myrow_clm["year"];
         $s_score = $myrow_clm["score"];
#         $authors[$s_nick]=$s_nick;
         if (preg_match("/forum\:(\d+)\:(.+)/",$s_cook,$s_match)) {
          $s_nick = "<a href='$forumname/memberlist.php?mode=viewprofile&u=".$s_match[1]."'>Участник форума ".$s_match[2]."</a>";
         }
         else {
          $s_nick = $myrow_clm["nick"];
         }
         if ($submitt) {
           if ($rept != $currenttype) {
             if ($currenttype) {
               $out .= '</div>';  
             }  
             $currenttype=$rept;
             $out .="<h3>".$rtype_ar[$rept].":</h3>\n";
             $out .='<div style="width: 100%" class="autocard-masonry">';
           }
         }
         if ($_text_mode) {
             $out .="<p class=travell0><a itemprop=\"significantLink\" class=travell5r href=\"/v_report.php/$rid";
             $out .= "\">($s_spring) $s_key</a></b>\n";
             $out .=" $s_nick $s_month/$s_year</p>\n";
         } else{
             $out .="<div class='autocard'>";
             $out .="<h4>Маршрут поездки: <a class=\"exclink\" itemprop=\"significantLink\" href=\"/v_report.php/$rid";
             $out .= "\">$s_key</a></h4>";
             $out .="<p>Рейтинг отчета: $s_score</p>\n";
             $out .="<p>Время путешествия: $s_month/$s_year</p>\n";
             if (!($submitt)) {
               $out .="<p>Длительность поездки: $rtype_ar[$rept]</p>\n";
             }  
             $out .="<p>Автор отчета: $s_nick</p>\n";
             $out .="<p>Отчет размещен: $s_date</p>\n";
             $out .="</div>";
         }
         $flagresult=1;


      } while  ($myrow_clm = mysqli_fetch_array($result_clm));
    }
    

if ($flagresult == 0) {
 $out="";
}

$sql_clm = "select nick, cook from l_reports";
if ($result_clm = $dbi->query($sql_clm)) {
  $authors=array();
  if($myrow_clm = mysqli_fetch_array($result_clm)) { 
    do {
         $s_nick = $myrow_clm["nick"];
         $s_cook = $myrow_clm["cook"];
         if (preg_match("/forum\:(\d+)\:(.+)/",$s_cook,$s_match)) {
          $k = strtolower($s_match[2] . $s_cook);
          $authors[$k]=$s_cook;
         }
         else {
          $k = strtolower($s_nick);
          $authors[$k]=$s_nick;
         }
      } while  ($myrow_clm = mysqli_fetch_array($result_clm));
  }
  mysqli_free_result($result_clm);
}

ksort($authors);

while(list($k,$ss_nick)=each($authors)) {
    if ($ss_nick == $author) {
          $authortxt .= "<option SELECTED ";
    }
    else {
          $authortxt .= "<option ";
    }

    if (preg_match("/forum\:(\d+)\:(.+)/",$ss_nick,$s_match)) {
      $s_nick = $s_match[2] . '(участник форума)';
    }
    else {
      $s_nick = $ss_nick;
    }
    $authortxt .= " value=\"$ss_nick\">$s_nick\n";
}
echo "<h1 itemprop=\"name\">$title</h1>\n";
echo "<center><p itemprop=\"description\" class=travell0>На сайте размещено более <b>$rep_cnt</b> отчётов о самостоятельных путешествиях по России и за ее рубежи. </p></center>";

if (!($submitt)) {
 if ($header) {
  echo "<div class=\"row\"><div class=\"col-xs-6 col-md-3\"> <h3>$header</h3></div>";
  echo "<div class=\"col-xs-6 col-md-3\"><h4><a class=\"exclink\" href=\"#searchrep\">Поиск по базе отчетов</a></h4></div></div>";
 }
 echo '<div style="width: 100%" class="autocard-masonry">';
 echo $out;
 echo '</div>';
}

if ($submitt) {
 if ($header) {
  echo "<h3>$header</h3>\n";
 }
 if ($out) {
 echo $out;
 echo '</div>';

 }
 else {
  echo"<h3 class=travell5>Увы, ничего не найдено</h3>\n";
 }
}

echo "<div class=\"float-left pull-left row\" >"; //поиск
echo "<h3  id=\"searchrep\">Поиск по базе отчетов</h3>\n";
echo "<p class=travell0>Основная часть отчетов о путешествиях скрыта фильтром, чтобы не отображать на странице <b>более $rep_cnt строк</b>."
        . " Если есть желание вывести весь список - установите Любой сезон и Любой год, отметьте все длительности путешествий. Фильтр предусматривает поиск отчетов по датам и городам.</br>";
echo "Все отчёты связаны с населёнными пунктами, указанными в маршруте, и <b>доступны со страниц соответстующих городов и областей России и зарубежных стран</b>.<br>\n";


echo "<form name=selection method=post actionaction=\"/rd_report.php\">";

echo "<div class=\"col-xs-12 col-md-4\">";
echo "<div class=\"form-group\">\n";
echo "<label class=\"control-label \" for=\"selmonth\">Сезон</label>\n";
if ($selmonth == 2) {
 echo "<div class=\"radio\" ><label><input class=\"form-check-input\" TYPE=radio VALUE=1 NAME=selmonth>   Зима (с октября по март)</label></div>";
 echo "<div class=\"radio\" ><label><input TYPE=radio VALUE=2 NAME=selmonth CHECKED>   Лето (с апреля по сентябрь)</label></div>";
 echo "<div class=\"radio\" ><label><input class=\"form-check-input\" TYPE=radio VALUE=3 NAME=selmonth>   Любой сезон</label></div>";
}
else {
 if ($selmonth == 1) {
  echo "<div class=\"radio\" ><label><input TYPE=radio VALUE=1 NAME=selmonth CHECKED>Зима (с октября по март)</label></div>";
  echo "<div class=\"radio\" ><label><input TYPE=radio VALUE=2 NAME=selmonth>Лето (с апреля по сентябрь)</label></div>";
  echo "<div class=\"radio\" ><label><input TYPE=radio VALUE=3 NAME=selmonth>Любой сезон</label></div>";
 }
 else {
  echo "<div class=\"radio\" ><label><input  TYPE=radio VALUE=1 NAME=selmonth>Зима (с октября по март)</label></div>";
  echo "<div class=\"radio\" ><label><input  TYPE=radio VALUE=2 NAME=selmonth>Лето (с апреля по сентябрь)</label></div>";
  echo "<div class=\"radio\" ><label><input  TYPE=radio VALUE=3 NAME=selmonth CHECKED>Любой сезон</label></div>";
 }
}
echo "</div></div>\n";

echo "<div class=\"col-xs-12 col-md-4\">";
echo "<div class=\"form-group\">\n";
echo "<label class=\"control-label \" for=\"selmonth\">Длительность путешествия</label>\n";

echo $rtype_txt;
echo "</div></div>\n";

echo "<div class=\"col-xs-12 col-md-4\">";
echo "<div class=\"form-group\">";
echo "<label class=\"control-label \" for=\"selyear\">Актуальность отчета</label>\n";

if ($selyear == 1) {
 echo "<div class=\"radio\" ><label><input TYPE=radio VALUE=1 NAME=selyear CHECKED>Любой год</label></div>";
 echo "<div class=\"radio\" ><label><input TYPE=radio VALUE=2 NAME=selyear>Только последние 2 года</label></div>";
}
else {
 echo "<div class=\"radio\" ><label><input TYPE=radio VALUE=1 NAME=selyear>Любой год</label></div>";
 echo "<div class=\"radio\" ><label><input TYPE=radio VALUE=2 NAME=selyear CHECKED>Только последние 2 года</label></div>";
}
echo "</div></div>";
if ( ($ids) and (!($ids == "-1")) ) {
 echo "<div class=\"row well\">\n";
 echo "По городам <b>$city</b>\n";
 echo "<input class=\"form-control\" TYPE=hidden NAME=ids value='$ids'>\n";
 echo "</div>\n";
}
else {
 echo "<div class=\"col-xs-12 col-md-7\">";
 echo "<div class=\"form-group\">";
 echo "<label class=\"control-label \" for=\"city\">По городам</label>";
 echo "<input TYPE=text class=\"form-control\" VALUE=\"$city\" NAME=city size=50>";
 echo "</div></div>";
}
echo "<div class=\"col-xs-12 col-md-3\">";
echo "<div class=\"form-group\">";
echo "<label class=\"control-label\" for=\"author\">По автору</label>";
echo "<select class=\"form-control\" size=1 name=author>$authortxt</select>";
echo "</div></div>";


echo "<div class=\"col-xs-12 col-md-3\">";
echo "<div class=\"form-group\">";
echo "<input class=\"btn btn-primary form-control\" type=submit name=submitt value=\"Выбрать\">";
echo "</div></div>";
echo "</form>";


echo "</div>"; //поиск
echo "</div>"; //весь контейнер
$_totop = 1;
include('include/footer_bs.php');
