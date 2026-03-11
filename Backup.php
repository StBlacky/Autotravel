<?php
$path='/home/blacky/data/www/Backup/';
$tables= array ();

include ('include/config.php');
include_once('include/head00.php');

$ff=fopen($path."descript.ion","w");

$result = mysql_query ("SHOW TABLES FROM $dbname",$db);
$fields = mysql_num_fields ($result);
$rows   = mysql_num_rows ($result);
while ($row = mysql_fetch_array ($result)) {
 $i = 0;
 while ($i < $fields) {
    $name  = mysql_field_name  ($result, $i);
    $tblname = $row[$name];
    if ((substr($tblname,0,3) != "bl_") or ($tblname == "bl_users")) {
     if ($tblname != "stats_ex") {
      echo $name." : ".$tblname." OK<BR>\n";
      array_push($tables,$tblname);
     }
    }
    else {
     echo $name." : ".$tblname." SKIPPED<BR>\n";
    }
    $i++;
 }
}
mysql_free_result ($result);


while ($table=array_pop($tables) ) {

#	$sql="backup table $table to '$path'";
#	$result = mysql_query($sql, $db);
#	if($myrow = mysql_fetch_array($result)) {
#		echo "<br>Table ".$myrow["table"]."<br>Op ".$myrow["op"];
#		echo "<br>Msg_type ".$myrow["msg_type"]."<br>Msg_text ".$myrow["msg_text"];
#	}
#	else {
#		echo "unsuccessful<br>";
#               echo mysql_errno().": ".mysql_error()."<BR>";
#	}

	$result = mysql_query ("SHOW CREATE TABLE $table",$db);
	$fields = mysql_num_fields ($result);
	$rows   = mysql_num_rows ($result);
	while ($row = mysql_fetch_array ($result)) {
	 $i = 0;
	 while ($i < $fields) {
	    $name  = mysql_field_name  ($result, $i);
	    $tmpstr = $row[$name];
	    echo $name." : ".$tmpstr." <BR>\n";
	    fputs($ff,"\n\n".$tmpstr."\n\n");
	    $i++;
	 }
	}
	mysql_free_result ($result);

	echo "</p><p>$table backup: ";

        $result = mysql_query ("SELECT * FROM $table",$db);
	$fields = mysql_num_fields ($result);
	$rows   = mysql_num_rows ($result);
	$i = 0;
	echo "Your '".$table."' table has ".$fields." fields and ".$rows." records <BR>";
	echo "The table has the following fields <BR>"; 
	fputs($ff,"create table $table (\n");
	while ($i < $fields) {
	    $type  = mysql_field_type  ($result, $i);
	    $name  = mysql_field_name  ($result, $i);
	    $len   = mysql_field_len   ($result, $i);
	    $flags = mysql_field_flags ($result, $i);
	    echo $name." $type (".$len.") ".$flags."<BR>";
	    fputs($ff,"\t".$name." $type (".$len.") ".$flags.",\n");
	    $i++;
	}
	fputs($ff,")\n");
	$fp = fopen("$path"."$table","w");

	while ($row = mysql_fetch_array ($result)) {
		$i=0;
		while ($i < $fields) {
		    echo ".";
		    $name  = mysql_field_name($result, $i);
		    $tmpstr = $row[$name];
		    $tmpstr = preg_replace("/\r/", "", $tmpstr);
		    $tmpstr = preg_replace("/\n/", " ", $tmpstr);
		    fputs($fp,$tmpstr."||");
		    $i++;
		}
		fputs($fp,"\n");
	}
	fclose($fp);
	mysql_free_result ($result);

echo "</p>\n";
}
fclose($ff);

//$fp = fopen("phalbum/city.txt","w");
//$result = mysql_query ("SELECT id,name FROM towns where hide=0 order by name asc",$db);
//while ($row = mysql_fetch_array ($result)) {
//    $id  = $row["id"];
//    $name  = trim($row["name"]);
//    fputs($fp,"$id\t$name\tyes\n");
//}
//fclose($fp);
//mysql_free_result ($result);

mysql_close();
?>