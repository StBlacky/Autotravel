<?php
include_once('include/config.php');
include_once('include/head00.php');

$u = 0;
if (preg_match("/^\//",$_pin,$exa)) {
 if (preg_match("/^\/(\d+)$/",$_pin,$exa)) {
  $u = $exa[1];
 }
}
else {
 $u = (int) $_REQUEST["u"];
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">
<meta http-equiv="Content-language" content="ru">
</head>
<body>
<?php
$uname = "";
$avatar = "";
$id = "";
$sql = "select username, user_id, user_avatar from bl_users where user_id='$u'";
$result = $dbi->query($sql);
if ($myrow = mysqli_fetch_array($result)) {
  $uname = $myrow["username"];
  $id = $myrow["user_id"];
  $avatar = $myrow["user_avatar"];
}
if ($id) {
  $title = "Профиль ".$uname;
  if ($avatar) {
    echo '<img style="" src="'.$forumname.'/download/file.php?avatar='.$avatar.'"><br>';
  }
  else {
    echo "<h1>".$uname.":</h1>\n";
  }
  echo '<div style="position:absolute; right:5px; top:0px"><a class="travell5n" onclick="PopoverClose($(this))">[X]</a></div>';
  echo "<a class=\"travell5r\" target=\"_blank\" href=\"/user.php/$id\">Интерактивная карта откликов $uname</a><br>";  
  echo "<a class=\"travell5r\" target=\"_blank\" href=\"".$forumname. "/memberlist.php?mode=viewprofile&amp;u=" . $id . "\">Подробная информация про $uname</a><br>";  
}  
?>
</body>
