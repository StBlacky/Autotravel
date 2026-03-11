<div class="double-div"> <!-- если надо два раздела -->
   <div class="newobjects-otkliks"> <!-- новые отзывы -->
   <?php
   $sql = "SELECT o.nick as nick, t.id as town_id, o.exciting as ex_id, e.type as type, t.name as town_name,"
           . " t.hide as town_hide, e.name as ex_name, e.area as ex_area FROM otklik  o, towns t, exciting e where e.id = o.exciting and t.id = e.town $sqlzagr "
           . "order by o.time desc limit $_iexc_mode";
   if ($_bad_count>0) {
       $sql = "SELECT o.nick as nick, t.id as town_id, o.exciting as ex_id, o.time as o_time, e.type as type, t.name as town_name,"
          . " t.hide as town_hide, e.name as ex_name, e.area as ex_area FROM otklik  o, towns t, exciting e"
          . " where e.id = o.exciting and t.id = e.town $sqlzagr "
          . "UNION SELECT om.nick as nick, tm.id as town_id, om.exciting as ex_id, om.time as o_time, em.type as type, tm.name as town_name,"
          . " tm.hide as town_hide, em.name as ex_name, em.area as ex_area FROM otklikm  om, towns tm, exciting em"
          . " where em.id = om.exciting and om.cook='$ctext' and tm.id = em.town $sqlzagr "
          . "order by o_time desc limit $_iexc_mode";
  }
  $result = $dbi->query($sql);

  if ($_text_mode != 1) {
    echo "<h2><img src='/jpg/new_otklik.jpg' alt=\"Новые отзывы\"></h2>";
  }
  else {
    echo "<h2>Новые&nbsp;отзывы&nbsp;>>></h2>";
  }

  if($result) {
    while($myrow = mysqli_fetch_array($result)) {
      $s_nick = $myrow["nick"];
      $s_town = $myrow["town_name"];
    	$s_townid = $myrow["town_id"];
    	$t_hide = $myrow["town_hide"];
      $s_ex = $myrow["ex_id"];
      $s_exname = $myrow["ex_name"];
      $s_type = $myrow["type"];
		  echo "<a itemprop=\"significantLink\" href=\"/otklik.php/$s_ex\">$s_exname ($s_town)</a> - от $s_nick";
      echo"<br>\n";
	  }
	  mysqli_free_result($result);
  }
  echo "<br><a class=arc atr=\"nofollow\" href='/archive.php/o/0'>&gt;&gt;&gt;Архив отзывов&gt;&gt;&gt;</a>";
  echo "</div>"; //новые отзывы

  echo "<div class=\"newobjects-objects\">"; //новые объекты
  if ($_text_mode != 1) {
     echo "<h2><img src='/jpg/new_ex.jpg' alt=\"Новые объекты\"></h2>";
  } else {
    echo "<h2>Новые&nbsp;объекты&nbsp;>>></h2>\n";
  }

  $sqlex = "SELECT t.id AS town_id, e.id AS ex_id, e.type AS TYPE, t.name AS town_name, t.hide AS town_hide, e.name AS ex_name, e.area as ex_area "
        . "FROM towns t, exciting e WHERE t.id = e.town $sqlzagr ORDER BY e.id DESC LIMIT $_iexc_mode ";
  $resultex = $dbi->query($sqlex);

  if($resultex) {
    while($myrowex = mysqli_fetch_array($resultex)) {
    	$s_town = $myrowex["town_name"];
    	$s_townid = $myrowex["town_id"];
    	$t_hide = $myrowex["town_hide"];
    	$s_ex = $myrowex["ex_id"];
    	$s_exname = $myrowex["ex_name"];
    	$s_type = $myrowex["type"];
		  echo "<a itemprop=\"significantLink\" href=\"/otklik.php/$s_ex\">$s_exname ($s_town)</a>";
   		echo"<br>\n";
  	} 
	  mysqli_free_result($resultex);
  }
  echo "<br><a class=arc atr=\"nofollow\" href='/archive.php/e/0'>&gt;&gt;&gt;Архив объектов&gt;&gt;&gt;</a>";
  echo "</div>";
  echo "</div>"; //сдвоенный блок

// вставим сюда наиболее важные для нас направления
/* echo "<tr><td width='2%'>&nbsp;</td><td class=travel11 valign=top colspan=2>";
echo "<span itemscope itemtype=\"http://schema.org/WebPage\"><span itemprop=\"about\" content=\"Крым - достопримечательности, фотографии, отчеты, советы путешественикам.\"></span>"
. "<a itemprop=\"relatedLink\" href=\"/area.php/80\">"
. "<img itemprop=\"image\" src=\"/phalbum/90186/109-s.jpg\" title=\"Крым. Достопримечательности.\" alt=\"Крым. Достопримечательности.\"></a>  "
. "</span>";
echo "<span itemscope itemtype=\"http://schema.org/WebPage\"><span itemprop=\"about\" content=\"Румыния - достопримечательности, фотографии, отчеты, советы путешественикам.\"></span>"
. "<a itemprop=\"relatedLink\" href=\"/area.php/-12\">"
. "<img itemprop=\"image\" src=\"/phalbum/90421/170-s.jpg\" title=\"Румыния. Достопримечательности.\" alt=\"Румыния. Достопримечательности.\"></a>  "
        . "</span>";
echo "<span itemscope itemtype=\"http://schema.org/WebPage\"><span itemprop=\"about\" content=\"Алтай - достопримечательности, фотографии, отчеты, советы путешественикам.\"></span>"
. "<a itemprop=\"relatedLink\" href=\"/area.php/48\">"
. "<img itemprop=\"image\" src=\"/phalbum/90601/140-s.jpg\" title=\"Алтай. Достопримечательности.\" alt=\"Румыния. Достопримечательности.\"></a>"
        . "</span>";
echo "</td></tr>"; */

echo "<div class=\"double-div\">"; // <!-- если надо два раздела -->
echo "<div class=\"newobjects-reports\">"; //новые отчеты
echo "<h2>Новые отчеты >>></h2><br><font class=travell0>Отчеты об автопутешествиях</font><br>";
$sql_wd = "select rid, key_towns, time from l_reports order by time desc limit 5";
$result_wd = $dbi->query($sql_wd);
while ($myrow_wd = mysqli_fetch_array($result_wd)) {
  $s_key = $myrow_wd["key_towns"];
  $rid   = $myrow_wd["rid"];
  echo "<a href='/v_report.php/$rid'>$s_key</a><br>\n";
}
mysqli_free_result($result_wd);
echo "</div>";

echo "<div class=\"newobjects-descriptions\">"; //новые описания
echo "<h2>Новые описания >>></h2>";
$sql_wd="select cook, obj, dtizm from l_text order by dtizm desc limit 5";
$result_wd = $dbi->query($sql_wd);
while ($myrow_wd = mysqli_fetch_array($result_wd)) {
     $obj   = $myrow_wd["obj"];
     $cook  = $myrow_wd["cook"];
     $type  = substr($obj,0,1);
     $key   = (int) substr($obj,1);
     $link = "";
     if ($type == 't') {
        $link = "<a itemprop=\"significantLink\" href='/town.php/$key'>" . gettownname($key) . "</a>";
     }
     if ($type == 'e') {
       $link = "<a itemprop=\"significantLink\" href='/otklik.php/$key'>" . getexname($key) . "</a>";
     }
     echo "$link<br>\n";

}
mysqli_free_result($result_wd);
echo "</div>"; //описания
echo "</div>"; //сдвоенный блок
echo "<div class=\"newobjects-traces\">"; //cостояние трасс
   echo "<h2>Новости о состоянии дорог >>></font></h2>";
   echo "Недавно проехали по нашим дорогам и есть что рассказать? <br>Поделитесь с другими автопутешественниками,  перейдите на страницу <a href=\"trace.php\">Дороги России</a>.<br>";

   $sql_tr = "select ts.kbeg as kbeg, ts.kend as kend, tr.id as trace, tr.name as tr_name, tr.of_name as of_name  "
        . "from otklikt as ts, trace as tr where ts.ido>0 and tr.id=ts.idtr ORDER BY ts.time DESC LIMIT 10 ";
   if ($_bad_count>0) {
      $sql_tr = "SELECT ts.kbeg as kbeg, ts.time as time, ts.kend as kend, tr.id as trace, tr.name as tr_name, tr.of_name as of_name  "
        . "from otklikt as ts, trace as tr where ts.ido>0 and tr.id=ts.idtr UNION "
        . "SELECT ts2.kbeg as kbeg, ts2.time as time, ts2.kend as kend, tr2.id as trace, tr2.name as tr_name, tr2.of_name as of_name  "
        . "from otklikt as ts2, trace as tr2 where ts2.ido<0 and ts2.cook='".$ctext."' and tr2.id=ts2.idtr ORDER BY time DESC LIMIT 10 ";
   }
    $result_tr = $dbi->query($sql_tr);
   
    if($result_tr) {
    	while($myrow_tr = mysqli_fetch_array($result_tr)) {
      	$kbeg = $myrow_tr["kbeg"];
    		$kend = $myrow_tr["kend"];
        $trace = $myrow_tr["trace"];
        $tr_name = $myrow_tr["tr_name"];
    		$of_name = $myrow_tr["of_name"];
    		if (!$of_name){
          $tr_name = $of_name . " " . $tr_name;          
        }
    		echo "<a itemprop=\"significantLink\" href=\"/tracestate.php/$trace\">$tr_name (от $kbeg км до $kend км)</a>";
      	echo"<br>\n";
	    } 
	    mysqli_free_result($result_tr);
    }
    echo "</div>"; //состояние трасс


?>
