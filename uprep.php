<?php
include_once('include/config.php');
include_once('include/func.php');
include('include/head01.php');

$sql = "select distinct mail from otklik where cook not like 'forum:%' and mail <> ''";
$result = $dbi->query($sql);
while ($row = mysqli_fetch_array($result)) {
 $mail = $row["mail"];
 echo "-- $mail<br>\n";
 $sql3 = "";
 if ($mail) {
  $sql2 = "select user_id, username from bl_users where user_email = '$mail'";
  $result2 = $dbi->query($sql2);
  if ($row2 = mysqli_fetch_array($result2)) {
   $uid  = $row2["user_id"];
   $name = $row2["username"];
   if ($uid) {
//    $sql3 = "update l_reports set cook = 'forum:$uid:$name' where mail = '$mail';";
    $sql3 = "update otklik set cook = 'forum:$uid:$name' where mail = '$mail';";
    echo "$sql3<br>\n";
   }
  }
  mysqli_free_result($result2);
 }
}
/*

$MAX_SIZE = 15000000;
$FILE_MIMES = array('image/pjpeg','image/jpeg','image/jpg');
$FILE_EXTS  = array('.jpg'); 
$site_name = $_SERVER['HTTP_HOST'];

$album_dir="/home/blacky/data/www/phalbum";

$cat = (int) $_REQUEST["cat"];
$sql = "select file, author from foto where file like '".$cat."/%'";
$result = $dbi->query($sql);
while ($myrow = mysqli_fetch_array($result)) {
 $ufile   = $myrow["file"];
 $oldauthor = $myrow["author"];
 $action = "caption";
 if ($oldauthor == 'forum:4:oyster') {
   $action = 'oyster';  
 }   
 if ($oldauthor == 'forum:3:Илюха') {
   $action = 'Илюха';  
 }
 if ($action == "caption") {
    print "$ufile caption $oldauthor<br>\n";  
    $fn   = $album_dir."/".$ufile.".jpg";
    $ftn   = $album_dir."/".$ufile."-s.jpg";
    $message = myjpegresize($fn, $fn, 1200, 1);
    if (!($message)) {
     $message = myjpegresize($fn, $ftn, 250, 0);
    }
 }
 else {
    print "$ufile skip $oldauthor<br>\n";         
 }
}

*/ 

echo "<br>Done.\n";

?>
