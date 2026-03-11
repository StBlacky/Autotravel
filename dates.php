<?php
include_once('include/config.php');
$title = "Календарь событий";
include('include/header.php');

$sql="select d.month as m_id, d.when as when, d.what as what, m.name as m_name from dates d, month m where d.month = m.id";
$sql_m="select m.id as id, m.name as name from month m order by 1";
$result_m = $dbi->query($sql_m);

/* echo "<font class=small> Примерный список мероприятий, которые было бы интересно посетить. </font><br><br><br>"; */
   echo "<br><center> <font class=\"t14 tb tc\">Если ниже вы не нашли события, которе вы считаете, надо посетить, значит мы о нем еще не знаем. Напишите о нем на нашем <a href=\"$forumname/viewtopic.php?t=293\">Форуме</a>.";
if($myrow_m = mysqli_fetch_array($result_m)) {
	echo "<h1>\n";
        	do {	$m_id = $myrow_m["id"];
			$m_name = $myrow_m["name"];
			$sql="select d.when as d_when, d.what as what from dates d where d.month = $m_id";
			$result = $dbi->query($sql);
			if($myrow = mysqli_fetch_array($result)) {
                        	echo "<h3>$m_name</font></h3>";
				echo "<table border=1 width=95% cellpadding=8><Tbody class=travell0>";
                        	do {	$what = $myrow["what"];
					$when = $myrow["d_when"];
                        		echo "<tr><td width=20%><font class=normal>$when</font></td><td><font class=normal>$what</font></td></tr>";

		 		} while ($myrow = mysqli_fetch_array($result)); 
			 	echo "</table>";	
                        }
 			
 		} while ($myrow_m = mysqli_fetch_array($result_m)); 
}

include('include/footer.php');
?>
