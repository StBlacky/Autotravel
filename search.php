<?php
include_once('include/config.php');
include_once('include/func.php');
$sort = $_REQUEST["sort"];
$sort = (int) mysafestring($sort);
$ae = $_REQUEST["ae"];
$ae = (int) mysafestring($ae);
if ($sort==2) {
 if ($ae) {
  $newuri = "/cube.php/?a=$ae";
 }
 else {
  $newuri = "/cube.php/";
 }
 Header( "HTTP/1.1 301 Moved Permanently" );
 Header( "Location: $newuri" ); 
 exit;
}
include_once('include/head00.php');

$kml = "";
$tt = $_REQUEST["tt"];
$tt = (int) mysafestring($tt);
$menu1 = "";
$typextxt = getextypes();

$sql_xx = "";

if ($tt) {
 $t_name = gettownname($tt);
 $title = $t_name . ": Трассы";
 $metatags = $title;
 $sql_x = "SELECT DISTINCT t.id AS trace_id, t.name AS trace, t.of_name as of_name FROM trace t, far_tr ft WHERE ft.town = '$tt' AND t.id = ft.trace order by t.of_name";
}
else {
 $menu1 = "<a class=travell5m href=\"test.php\">Поиск объекта</a>";
 $menu2 = "<a class=travell5m href=\"search.php?sort=4\">Достопримечательности по типу</a>";
 $menu3 = "<a class=travell5m href=\"search.php?sort=2\">Достопримечательности по областям</a>";
 if ($ae) {
  $a_name    = getareaname($ae);
  $title    = "$a_name: Достопримечательности Карта";
  $sql_x    = "SELECT distinct te.type AS ex_type, te.id AS te_id from type_ex te, towns t, exciting e where t.hide<>1  and t.area = '$ae' and e.town = t.id  and e.kind REGEXP(te.id)  order by te.desc, te.type";
  $kml      = "/kml.php/a/$ae";
 }
 else {
  $title='Достопримечательности';
  if ($sort==2) {
    $sql_x  = "select distinct a.name as a_name,t.area as a_id from towns t, areas a where a.id > 0 and a.id = t.area order by 1";
    $sql_xx = "select distinct a.name as a_name,t.area as a_id from towns t, areas a where a.id < 0 and a.id = t.area order by 1";
// Достопримечательности по областям
    $title='Достопримечательности по областям';
  }
  else {
    $sql_x = "SELECT te.type AS ex_type, te.id AS te_id FROM type_ex te ORDER BY te.desc,te.type ";
// Достопримечательности по типу
    $title='Достопримечательности по типу';
  }
 }
}

include('include/header.php');

if ($sql_x) {
 $result = $dbi->query($sql_x);
 $numrows = mysqli_num_rows($result);
 $counter = 0;
}
$tbltxt1 = "";
$tbltxt2 = "";
?>  


<center>
<TABLE width=100% cellpadding=8>
<tbody class="t14 tb tc">

<?php 
if ($menu1) {
	echo "<tr><td width=34% align=left>$menu1</td>";	
	echo "<td width=33% align=center>$menu2</td>";
	echo "<td align=right>$menu3</td></tr>\n";
}
?>
</tbody>
</table>
<h3 class="t14 tb tc"><?php echo $title; ?></h3>
<table cellpadding=2>
<tbody>

<?php

if ($tt)	{
	if($myrow = mysqli_fetch_array($result)) {
    	do {
      		$s_trace = $myrow["trace"];
    		$s_traceid = $myrow["trace_id"];
    		$s_on = $myrow["of_name"];
 		echo"<TR><TD class=travell5>\n";
      		echo "<a href=\"trace.php?town=$s_traceid\">$s_on $s_trace </a>";
      		echo"</TR></TD>\n";
   		} while($myrow = mysqli_fetch_array($result));
   	}
}
else {
 if ($kml) {
             echo "<tr><td colspan=2 class=\"t14 tb tc\">\n";
             include('include/kml.php');
             echo "</td></tr>\n";
 }
 if ($ae ) {
        echo "<tr><td colspan=2 class=\"t14 tb tc\">\n";
        echo "<br><br><div align=center><a class=travell5m href=\"test.php?extype[]=1&area[]=$ae&psubmit=Показать\">Полный список достопримечательностей области </a><br>\n";
        echo "<div align=center><a class=travell5m href=\"test.php?extype[]=1&area[]=$ae&mrate=$highrate&psubmit=Показать\">Cписок популярных достопримечательностей области </a><br><br>Достопримечательности по категориям:</div>\n";
        echo "</td></tr>\n";
	if($myrow = mysqli_fetch_array($result)) {
           do {
      		$s_e_id = $myrow["te_id"];
      		$s_e_name = $typextxt[$s_e_id]."/".$myrow["ex_type"];
      		$tbltxt = "<a class=travell5m href=\"test.php?typex[]=$s_e_id&area[]=$ae&psubmit=Показать\"> $s_e_name </a>";
	       if ($counter < $numrows) {
	        $tbltxt1 = $tbltxt1 . "$tbltxt<br>";
	       }
	       else {
	        $tbltxt2 = $tbltxt2 . "$tbltxt<br>";
	       }
	       $counter++;
	       $counter++;
   	   } while($myrow = mysqli_fetch_array($result));

   	   echo "<tr><td width=50% class=travell5>$tbltxt1</td>\n<td class=travell5>$tbltxt2</td></tr>\n";
   	}
 }
 else {
  if ($sort==2)	{
	if($myrow = mysqli_fetch_array($result)) {
    	do {
    		$name = $myrow["a_name"];
    		$id = $myrow["a_id"];
      		$tbltxt = "<a href=\"/area.php/$id\"> $name </a>";
	       if ($counter < $numrows) {
	        $tbltxt1 = $tbltxt1 . "$tbltxt<br>";
	       }
	       else {
	        $tbltxt2 = $tbltxt2 . "$tbltxt<br>";
	       }
	       $counter++;
	       $counter++;
   		} while($myrow = mysqli_fetch_array($result));
   	   echo "<tr><td width=50% class=travell5>$tbltxt1</td>\n<td class=travell5>$tbltxt2</td></tr>\n";
   	}
  }
  else	{
	if($myrow = mysqli_fetch_array($result)) {
    	 do {
      		$s_e_id = $myrow["te_id"];
      		$s_e_name = $typextxt[$s_e_id]."/".$myrow["ex_type"];
      		$tbltxt = "<a href=\"test.php?typex[]=$s_e_id&psubmit=Показать\"> $s_e_name </a>";
	       if ($counter < $numrows) {
	        $tbltxt1 = $tbltxt1 . "$tbltxt<br>";
	       }
	       else {
	        $tbltxt2 = $tbltxt2 . "$tbltxt<br>";
	       }
	       $counter++;
	       $counter++;
   	 } while($myrow = mysqli_fetch_array($result));
   	   echo "<tr><td width=50% class=travell5>$tbltxt1</td>\n<td class=travell5>$tbltxt2</td></tr>\n";
   	}
  }

 }
}

if ($sql_xx) {
 $tbltxt3 = "<div align=center>Заграница: </div>";
 $result = $dbi->query($sql_xx);
 if($myrow = mysqli_fetch_array($result)) {
  do {
   $name = $myrow["a_name"];
   $id = $myrow["a_id"];
   $tbltxt3 = $tbltxt3 . "\n<a href=\"/area.php/$id\"> $name </a><br>";
  } while($myrow = mysqli_fetch_array($result));
 }
 if ($tbltxt3) {
  echo "<tr><td class=travell5 colspan=2 align=center><hr>$tbltxt3</td></tr>\n";
 }
}

?>
</tbody>
</TABLE>
</center>

<?php  

include('include/footer.php');

?>   
