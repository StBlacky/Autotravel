<?php
include_once('include/config.php');
include_once('include/func.php');
include_once('include/head00.php');

$town = $_REQUEST["town"];
$town = (int) mysafestring($town);

$hl = $_REQUEST["hl"];
$hl = mysafestring($hl);

$s_name = gettownnamearr($town);

$title = "Отчеты о поездках в " . $s_name[5]; 

include('include/header.php');
echo "<h1>$title</h1>\n";
$sql_r = "select l.key_towns as key_town, l.cook as cook, l.nick as nick, l.month as month, l.year as year, l.group_rep as gr, l.rid as rid, l.spring as spring 
           from l_reports l, t_reports t where 
           l.rid>0 and l.rid = t.rid and t.tid = $town order by l.year desc, l.month desc";

$result_r = $dbi->query($sql_r);
if($myrow_r = mysqli_fetch_array($result_r)) {
do {
	$s_rid = $myrow_r["rid"];
	$s_key = $myrow_r["key_town"];
	$s_key = getskeytext($s_key,$rid);
	$s_cook = $myrow_r["cook"];
	$s_spring = $myrow_r["spring"];
	$s_month = $myrow_r["month"];
	$s_year = $myrow_r["year"];
        if (preg_match("/forum\:(\d+)\:(.+)/",$s_cook,$s_match)) {
         $s_nick = "<a href='$forumname/memberlist.php?mode=viewprofile&u=".$s_match[1]."'>Участник форума: ".$s_match[2]."</a>";
        }
        else {
         $s_nick = $myrow_r["nick"];
        }
	echo "<p class=travell0>";
	echo "<a class=travell5n href=\"v_report.php/$s_rid\">($s_spring) $s_key</a>&nbsp;($s_nick&nbsp;$s_month/$s_year)";
	echo "</p>";
  } while($myrow_r = mysqli_fetch_array($result_r));
}  

echo "<p class=travell0>Подробную информацию о $s_name[5] можно найти на странице <a class=travell5m href=\"town.php/$town\">$s_name[0]</a>.";
echo "</p>\n";

?>
<?php
include('include/footer.php');
?>
