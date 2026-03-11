<?php  
include_once('include/config.php');
include_once("include/func.php");
$title="Календарь событий";
include("include/header.php");

$id = $_REQUEST["id"];

 $sql = "SELECT id, date, title, spring, url, text, exciting, calendar, UNIX_TIMESTAMP(calendar) as calendr FROM news WHERE calendar between DATE_SUB(CURRENT_DATE, INTERVAL 4 DAY) and DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR) order by calendar asc";
 $result = $dbi->query($sql);
 echo "<h1 class=\"t14 tb tc\">$title</h1>\n";

 echo "<table class=travell0 width=95% border=1 cellpadding=3 cellspacing=3>\n";

 while ($myrow = mysqli_fetch_array($result)) {
    $id = $myrow["id"];
    $date = $myrow["date"];
    $calendr = $myrow["calendr"];
    $calendar = strftime ("%d&nbsp;%b&nbsp;%Y",$calendr);
    $title = $myrow["title"];
    $spring = $myrow["spring"];
    $url = $myrow["url"];
    $exciting = $myrow["exciting"];
    $text = $myrow["text"];
    if ($exciting) {
     $ext = substr($exciting,0,1);
     $exc = substr($exciting,1);
     $ex_url = "";
     if ($ext == 'e') {
      $ex_name=getexname($exc);
      $ex_url="<b><a href=/otklik.php/$exc>$ex_name:</A> </b>\n";
     }
     if ($ext == 'a') {
      $ex_name=getareaname($exc);
      $ex_url="<b><a href=/search.php?a=$exc>$ex_name:</A> </b>\n";
     }
     if ($ext == 'r') {
      $ex_name=gettracename($exc);
      $ex_url="<b><a href=/trace.php?town=$exc>$ex_name:</A> </b>\n";
     }
     if ($ext == 't') {
      $ex_name=gettownname($exc);
      $ex_url="<b><a href=/town.php/$exc>$ex_name: </A></b>\n";
     }
    }
    else {
     $ex_url = "";
    }  
    echo "<tr><td class=\"t16 tc tb\">$calendar</td>\n";
    echo "<td align=left>$ex_url $text</td>\n";
 }
 echo "</table>\n";

include("include/footer.php");
?>

