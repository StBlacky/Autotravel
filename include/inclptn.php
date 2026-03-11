<?php
// инклюдник для авторизованных "партнерских" страничек
include_once('include/config.php');
//$db = mysql_connect($dbhost, $dbuser, $dbpasswd ); // здесь head00 не канает
                                                   //  - нужны свои хидеры
//$base = mysql_select_db ($dbname, $db);  
$dbi = new mysqli($dbhost, $dbuser, $dbpasswd, $dbname);
require_once('include/func.php');

$_user_id = 0;
$_user_name = "";
$_user_password = "";
$_user_email = "";
$_user_status = 0;
$_user_dopinfo = "";

$ctext = "";
if (!($_COOKIE[$cookiename])) {
  if  ($_SERVER['REQUEST_METHOD']=='GET') {
   $carray = Array();
   $carray["table"] = "forum";
   if (!($forum_user_id > 0)) {
     $forum_user_id = 0;
     $forum_user_name = "";
   }
   $carray["userid"] = $forum_user_id;
   $carray["username"] = $forum_user_name;
   $carray["ctext"] = md5(date("Y-m-d H:i:s",time()).$_SERVER["HTTP_X_REAL_IP"]."attr");
   setcookie($cookiename, serialize($carray), time()+1720000, "/", $domainname);
   $ctext = $carray["ctext"];
  }
}
else {
   $ctext = stripslashes($_COOKIE[$cookiename]);
   $carray = unserialize($ctext);
   if (!(is_array($carray))) {
    $carray = Array();
    $carray["table"] = "forum";
    if (!($forum_user_id > 0)) {
     $forum_user_id = 0;
     $forum_user_name = "";
    }
    $carray["userid"] = $forum_user_id;
    $carray["username"] = $forum_user_name;
    $carray["ctext"] = md5(date("Y-m-d H:i:s",time()).$REMOTE_ADDR."attr");
    $ctext=serialize($carray);
   } 
   if  ($_SERVER['REQUEST_METHOD']=='GET') {
    setcookie($cookiename, serialize($carray), time()+1720000, "/", $domainname);
   }
   $ctext = $carray["ctext"];
   // надо бы посмотреть, не ли готовой сессии с такой кукой
   // сперва - стираем всё старьё
   $sql = "delete from sessions where (expire < (CURRENT_TIMESTAMP - 9600))";
   $result = $dbi->query($sql);
//   mysql_free_result($result);
   $sql = "select p.id as id, p.username as username, p.email as email, p.status as status, p.dopinfo as dopinfo from partner p, sessions s where s.cook='$ctext' and s.userid = p.id";
   $result = $dbi->query($sql);
   if ($row = mysqli_fetch_array($result)) {
    $_user_id = $row["id"];
    $_user_name = $row["username"];
    $_user_email = $row["email"];
    $_user_status = $row["status"];
    $_user_dopinfo = $row["dopinfo"];
   }
   mysqli_free_result($result);
   if ($_user_id > 0) {
    $sql = "update sessions set expire = CURRENT_TIMESTAMP where cook = '$ctext'";
    $result = $dbi->query($sql);
//    mysql_free_result($result);
   }
}
// если куки нету вовсе

if (($_user_id == 0) && ($_REQUEST["login"])) {
 $messagetext = "";
 if(!isset($_SERVER["PHP_AUTH_USER"])) {
    Header("WWW-Authenticate: Basic realm=\"Autotravel partnership\"");
    Header("HTTP/1.0 401 Unauthorized");
    $messagetext = "Нужно было вводить имя/пароль партнерской программы";
    $_user_id = 0;
 } 

 else {
   $_user_name = mysafestring($_SERVER["PHP_AUTH_USER"]);
   $_user_password = mysafestring($_SERVER["PHP_AUTH_PW"]);
   $sql = "select * from partner where username = '$_user_name' and password = '$_user_password'";
   $result = $dbi->query($sql);
   if ($row = mysqli_fetch_array($result)) {
    $_user_id = $row["id"];
    $_user_name = $row["username"];
    $_user_email = $row["email"];
    $_user_status = $row["status"];
    $_user_dopinfo = $row["dopinfo"];
   }
   else {
    $_user_id = 0;
    Header("WWW-Authenticate: Basic realm=\"Autotravel partnership\"");
    Header("HTTP/1.0 401 Unauthorized");
    $messagetext = "Имя/пароль партнерской программы введены неправильно";
   }
   mysqli_free_result($result);
   if ($_user_id > 0) {
    $sql = "insert into sessions (userid,cook) values ('$_user_id','$ctext')";
    $result = $dbi->query($sql);
//    mysql_free_result($result);
   }
 }
}

?>