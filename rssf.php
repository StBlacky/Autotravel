<?php  

include_once('include/config.php');
require_once "include/func.php";
include_once "include/head00.php";

$typ = "f";
//$hid = 8;

if (preg_match("/^\/(.)\/(\d+)$/",$_pin,$exa)) {
 $typ = $exa[1];
 $hid = (int) $exa[2];
}
if ($typ == "p") {
 $sqladd = " and t.topic_id = $hid ";
 $sqlpopt = "";
}
else {
 if ($hid>0) {   
   $sqladd = " and p.forum_id = $hid ";
 }  
 $sqlpopt = " FORCE INDEX (post_time) ";
}

header("Content-type: application/rss+xml");
setlocale(LC_TIME, "C");
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
echo "<rss version=\"2.0\">\n<channel>\n";
// echo "<image>http://autotravel.ru/jpg/chudick.gif</image>\n";
$printed = "";

$sql = "SELECT p.post_username as puname, p.post_time as ptime, p.post_id as pid, "
        . " p.forum_id as fid, p.topic_id as tid, p.poster_id as poid, t.topic_title as ttitle, "
        . " f.forum_name as fname, u.username as uname, p.post_subject as xsubj, p.post_text as xtext "
        . " FROM bl_posts p " .$sqlpopt . ", bl_forums f, bl_topics t, bl_users u "
        . " where f.forum_id = p.forum_id and t.topic_id = p.topic_id "
        . " and u.user_id  = p.poster_id $sqladd order by p.post_time desc limit 15";
//error_log($sql);
if ($result = $dbi->query($sql)) {

  while ($myrow = mysqli_fetch_array($result)) {
    $ptime  = $myrow["ptime"];
    $pid    = $myrow["pid"];
    $poid   = $myrow["poid"];
    $tid    = $myrow["tid"];
    $fid    = $myrow["fid"];
    $puname = $myrow["puname"];
    $uname  = $myrow["uname"];
    $fname  = $myrow["fname"];
    $ttitle = $myrow["ttitle"];
    $uname  = $myrow["uname"];
    $xsubj  = $myrow["xsubj"];
    $xtext  = $myrow["xtext"];
    if (!($printed)) {
     echo "<title>Сообщения форума autotravel.ru ";
     if ($typ == "p") {
      echo "Тема $ttitle ($fname)";
     }
     else {
      echo "Подфорум $fname ";
     }
     echo "</title>\n";
     echo "<link>$sitename/</link>\n";
     echo "<description>Форум сайта \"По России на авто\" ";
     if ($typ == "p") {
      echo "Тема $ttitle ($fname)";
     }
     else {
      echo "Подфорум $fname ";
     }
     echo "</description>\n";
     echo "<lastBuildDate>" . strftime("%a, %d %b %Y %H:%M:%S %z",$ptime) . "</lastBuildDate>\n";
     $printed = "1";
    }
    if (!($typ == "p")) {
     $title = "$ttitle: от $uname";
    }
    else {
     $title = "Сообщение от $uname";
    }
    echo "<item>\n";
    echo "<title>$title</title>\n";
    echo "<link>$forumname/viewtopic.php?p=$pid#$pid</link>\n";
    echo "<description><![CDATA[$xsubj<br>\n$xtext]]></description>\n";
    echo "<pubDate>". strftime("%a, %d %b %Y %H:%M:%S %z",$ptime) ."</pubDate>\n";
    echo "</item>\n";
  }  
}
echo "</channel>\n</rss>\n";
