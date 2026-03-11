<?php
$noads = 1;
$canon = '';
$jqueryon = 0;
$jhtmleditor = 0;
$hidemap = 0; //карта по умолчанию открыта
$hidemapfilter = 0; //на карте по умолчанию есть фильтр
$ipp = $_SERVER["HTTP_X_REAL_IP"];
$_pin = $_SERVER["PATH_INFO"];
$script_name = $_SERVER["SCRIPT_NAME"];
$script_name = substr($script_name, strrpos($script_name,"/") + 1);
$at_req_name = $_SERVER["DOCUMENT_ROOT"] . $_SERVER["REQUEST_URI"];
if ($_pin === '/') {
  $newuri = "/".$script_name;
  Header( "HTTP/1.1 301 Moved Permanently" );
  Header( "Location: $newuri" );
  exit;
}
if (preg_match("/autotravel.ru\/(.+)$/",$_pin,$exa)) {
  $newuri = "/".$exa[1];
  Header( "HTTP/1.1 301 Moved Permanently" );
  Header( "Location: $newuri" );
  exit;
}

$_uagent_full = $_SERVER["HTTP_USER_AGENT"];
$_uagent      = substr($_uagent_full,0,6);
$_nomap       = 0;
$banners_shown="";

$http_host = $_ENV["HTTP_HOST"];

include_once('include/func_reloc.php');
my_relocate($sitename);
include_once('include/config.php'); // ?????
include_once('include/func.php'); // ?????

$forum_user_id    = 0;
$_user_is_admin   = "";
$forum_user_banned = 0;
$forum_user_name  = "";
$forum_user_level = 0;
$forum_user_rank  = 0;
$forum_user_fotos = 0;
$forum_user_weight = 0;
$forum_user_time  = 0;
$forum_user_basket = 0;
$forum_user_basket_name = "";
$forum_user_basket_count = 0;

$ctext = "";

// переходим на mysqli
$dbi = new mysqli($dbhost, $dbuser, $dbpasswd, $dbname);
if (!($dbi)) {
  echo "Технические неполадки на хостинге: недоступна база данных";
exit;
}
$dbi->query("SET NAMES 'utf8'");

if (!(isset($_COOKIE[$cookiename]))) {
  if  ($_SERVER['REQUEST_METHOD']=='GET') {
    $carray = Array();
    $carray["userid"] = 0;
    $carray["txt"] = 0;
    $carray["text"] = 2;
    $carray["inws"] = 5;
    $carray["iexc"] = 10;
    $carray["otkl"] = 0;
    $carray["foto"] = 0;
    $carray["gmap"] = 2;
    $carray["tblx"] = 0;
    $carray["exct"] = 0;
    $carray["excv"] = 0;
    $carray["zagr"] = 1;
    $ctext = hash('sha256',date("Y-m-d H:i:s",time()).$_SERVER["HTTP_X_REAL_IP"].$_SERVER["HTTP_USER_AGENT"]);
    $carray["ctext"] = $ctext;
  }
}
else {
  $ctext = stripslashes($_COOKIE[$cookiename]);
  $carray = unserialize($ctext);
  if (!(is_array($carray))) {
    $carray = Array();
    $carray["userid"] = 0;
    $carray["txt"] = 0;
    $carray["text"] = 2;
    $carray["inws"] = 5;
    $carray["iexc"] = 10;
    $carray["otkl"] = 0;
    $carray["foto"] = 0;
    $carray["gmap"] = 2;
    $carray["tblx"] = 0;
    $carray["exct"] = 0;
    $carray["excv"] = 0;
    $carray["zagr"] = 1;
    $carray["ctext"] = hash('sha256',date("Y-m-d H:i:s",time()).$_SERVER["HTTP_X_REAL_IP"].$_SERVER["HTTP_USER_AGENT"]);
  }
}
$_regerror = '';
$_text_mode = 0;
$_desc_mode = 2;
$_otkl_mode = 0;
$_foto_mode = 0;
$_gmap_mode = 3;
$_exct_mode = 0;
$_excv_mode = 0;
$_salp_mode = 0; // сортировка объектов по рейтингу
$_tblx_mode = 0;
$_zagr_mode = 1;
$_inws_mode = 5;
$_iexc_mode = 10;

// ТОЛЬКО если установлены куки

if ($ctext) {
  if ($_REQUEST["tforumlogout"]) {
    $sql = "delete from sessionsf where cook='" . $carray["ctext"] ."'";
    $result = $dbi->query($sql);
    $carray["userid"] = 0;
  }
  if ($carray["mtxt"]) {
    $_text_mode = (int) $carray["mtxt"];
  }
  if (isset($carray["text"])) {
    $_desc_mode = (int) $carray["text"];
  }
  if (isset($carray["otkl"])) {
    $_otkl_mode = (int) $carray["otkl"];
  }
  if (isset($carray["foto"])) {
    $_foto_mode = (int) $carray["foto"];
  }
  if (isset($carray["gmap"])) {
    $_gmap_mode = (int) $carray["gmap"];
  }
  if (isset($carray["tblx"])) {
    $_tblx_mode = (int) $carray["tblx"];
  }
  if (isset($carray["exct"])) {
    $_exct_mode = (int) $carray["exct"];
  }
  if (isset($carray["excv"])) {
    $_excv_mode = (int) $carray["excv"];
  }
  if (isset($carray["zagr"])) {
    $_zagr_mode = (int) $carray["zagr"];
  }
  if (isset($carray["inws"])) {
    $_inws_mode = (int) $carray["inws"];
  }
  if (isset($carray["iexc"])) {
    $_iexc_mode = (int) $carray["iexc"];
  }

  if ($carray["userid"]) {
    // в куках есть сведения о регистрации. надо проверить
    $currenttime = time() - 3600;
    
    $sql = "delete from sessionsf where (expire < '$currenttime')";
    $result = $dbi->query($sql);
    $sqlii = "select s.userid as userid, s.expire as expire, u.username as username,
           u.user_type as user_type, u.user_rank as user_rank, u.user_fotos as user_fotos,
           u.user_weight as user_weight, u.user_basket as user_basket from sessionsf s, bl_users u
           where s.cook='" . $carray["ctext"] ."' and u.user_id = s.userid";
    $result = $dbi->query($sqlii);
    if ($row = mysqli_fetch_array($result)) {
      $forum_user_id      = $row["userid"];
      //error_log($forum_user_id);
      $forum_user_name    = $row["username"];
      if ($row["user_type"] == 3) {
        $forum_user_level   = 1;
      }
      else {
        $forum_user_level   = 0;
      }
      $forum_user_banned = 0;
      $forum_user_rank    = $row["user_rank"];
      $forum_user_fotos    = $row["user_fotos"];
      $forum_user_weight  = $row["user_weight"];
      $forum_user_time    = $row["expire"];
      $forum_user_basket    = $row["user_basket"];
    }
    mysqli_free_result($result);
  }
 
  $sqlii = "";
 
  // регистрация незарегенного
  if (!($forum_user_id)) {
    if (isset($_REQUEST["tforumtime"])) { 
      $tforumtime     = (int) mysafestring($_REQUEST["tforumtime"]);
    }
    else {
      $tforumtime     = 365; // 365 дней
    }
    $tforumtx     = time() + (24 * 3600 * $tforumtime);
    // регистрация через форум
    if ((isset($_REQUEST["tforumname"])) && (isset($_REQUEST["tforumpassword"]))) { 
      $tforumname     = mysafestring($_REQUEST["tforumname"]);
      $tforumpassword = mysafestring($_REQUEST["tforumpassword"]);
      if (($tforumname > "") && ($tforumpassword > "")) {
        error_log("logging in ".$tforumname);
        sleep(10);
        // чтоб не брутфорсили
        $remoteuserid = phpbb_userid($tforumname, $tforumpassword);
        if ($remoteuserid>1) {
          error_log("logging in id=".$remoteuserid);
          $sqlii = "SELECT user_id, username, user_password, user_type, user_weight,
            user_rank, user_fotos, user_basket
            FROM bl_users
            WHERE user_id = $remoteuserid";
          $result = $dbi->query($sqlii);
          if($row = mysqli_fetch_array($result)) {
            $thisuserid = $row['user_id'];
            if ( (($row['user_type'] == 0) or ($row['user_type'] == 3) )) {
              $forum_user_id    = $row['user_id'];
              $forum_user_name  = $row['username'];
              if ($row['user_type'] == 3) {
                $forum_user_level = 1;
              }
              else {
                $forum_user_level = 0;
              }
              $forum_user_banned = 0;
              $forum_user_rank  = $row['user_rank'];
              $forum_user_fotos  = $row['user_fotos'];
              $forum_user_weight  = $row['user_weight'];
              $forum_user_basket  = $row['user_basket'];
              $forum_user_time  = $tforumtx;          
              error_log("logged in ".$forum_user_id);
            }
            else {
              error_log("name=".$tforumname." locked down");
            }
          }
          else {
            error_log("name=".$tforumname." password does not fit");
          }
        }
        if (!($forum_user_id)) {
           $_regerror = 'Или имя, или пароль указаны неверно';
        }
      }
      else {
        $_regerror = 'Вы не указали имя или пароль';
      }
    }  
    // регистрация через соцсети
    if ($script_name == 'oauth.php') {
      // по e-mail
      if (($_auth2['result'] == 1) && (isset($_auth2['email']))) {
        $email = strtolower($_auth2['email']); // может через prepare ?
        sleep(2); // чтоб не брутфорсили
        $sqlii = "SELECT user_id, username, user_password, user_weight,
          user_type, user_rank, user_fotos, user_basket
          FROM bl_users
          WHERE LOWER(user_email) in ($email) and user_type in (0,3)"; // тут нам active не обязательно (?)
        $result = $dbi->query($sqlii);
        if($row = mysqli_fetch_array($result)) {
          $forum_user_id    = $row['user_id'];
          $forum_user_name  = $row['username'];
          if ($row['user_type'] == 3) {
            $forum_user_level = 1;
          }
          else {
            $forum_user_level = 0;
          }
          $forum_user_banned = 0;
          $forum_user_rank  = $row['user_rank'];
          $forum_user_fotos  = $row['user_fotos'];
          $forum_user_weight  = $row['user_weight'];
          $forum_user_basket  = $row['user_basket'];
          $forum_user_time  = $tforumtx;          
        }
        if (!($forum_user_id > 0)) {
          $_auth2['error'] = 'Не нашлось пользователя форума с e-mail = '.$email;
        }
      }
      else {
        if (!(isset($_auth2['error']))) {
          $_auth2['error'] = 'Неизвестная ошибка';
        }
      }
      // ? не по e-mail нам не надо
    }
    //
    if ($forum_user_id > 0) {
      $sqlii = "delete from sessionsf
        where cook = '" . $carray["ctext"] ."'";
      $result = $dbi->query($sqlii);
      $sqlii = "insert into sessionsf
              (userid,cook,username,user_level,user_rank,expire)
              values ('$forum_user_id',
              '" . $carray["ctext"] ."',
              '$forum_user_name','$forum_user_level','$forum_user_rank',
              '$tforumtx')";
      $result = $dbi->query($sqlii);
    }
  }

  // ------------------ PATCH: считаем только count для актуальной корзины ------------------

$forum_user_basket_name  = "";
$forum_user_basket_count = 0;

if ($forum_user_id > 0 && (int)$forum_user_basket > 0) {

  $bid = (int)$forum_user_basket;

  $sqlii  = "SELECT id, comment FROM basket WHERE id=".$bid." LIMIT 1";
  $result = $dbi->query($sqlii);

  if ($result && ($row = mysqli_fetch_assoc($result))) {

    $tmp_basket_name = trim((string)$row['comment']);

    if ($tmp_basket_name !== "") {

      $forum_user_basket_name = $tmp_basket_name;

      $sqlii = "SELECT COUNT(DISTINCT p.object_id) AS cnt
                FROM basket_blocks b
                INNER JOIN basket_points p ON p.block_id=b.id
                WHERE b.basket_id=".$bid."
                  AND p.type='OBJECT'
                  AND p.object_id IS NOT NULL
                  AND p.object_id>0";
      $result2 = $dbi->query($sqlii);

      if ($result2 && ($row2 = mysqli_fetch_assoc($result2))) {
        $forum_user_basket_count = (int)$row2['cnt'];
      }
      if ($result2) mysqli_free_result($result2);

    }

  } else {
    $sqlii = "UPDATE bl_users SET user_basket=0 WHERE user_id=".(int)$forum_user_id." LIMIT 1";
    $dbi->query($sqlii);
  }

  if ($result) mysqli_free_result($result);
}





}

// установка настроек

if (($script_name == 'sitemgr.php')||($script_name == 'sitemgr_bs.php')) {
  if ($_REQUEST["xsubmit"]) {
    $_text_mode = $_REQUEST["text"];
    $_text_mode = (int) mysafestring($_text_mode);
    $_desc_mode = $_REQUEST["desc"];
    $_desc_mode = (int) mysafestring($_desc_mode);
    $_otkl_mode = $_REQUEST["otkl"];
    $_otkl_mode = (int) mysafestring($_otkl_mode);
    $_foto_mode = $_REQUEST["foto"];
    $_foto_mode = (int) mysafestring($_foto_mode);
    $_gmap_mode = $_REQUEST["gmap"];
    $_gmap_mode = (int) mysafestring($_gmap_mode);
    $_excv_mode = $_REQUEST["excv"];
    $_excv_mode = (int) mysafestring($_excv_mode);
    $_tblx_mode = $_REQUEST["tblx"];
    $_tblx_mode = (int) mysafestring($_tblx_mode);
    $_exct_mode = $_REQUEST["exct"];
    $_exct_mode = (int) mysafestring($_exct_mode);
    $_zagr_mode = $_REQUEST["zagr"];
    $_zagr_mode = (int) mysafestring($_zagr_mode);
    $_inws_mode = $_REQUEST["inws"];
    $_inws_mode = (int) mysafestring($_inws_mode);
    $_iexc_mode = $_REQUEST["iexc"];
    $_iexc_mode = (int) mysafestring($_iexc_mode);
  }
}


if (preg_match($_bad_browsers,$_uagent_full)) {
  //$_text_mode  = 0;
  $_short_menu = 1;
  $_bad_browser= 1;
  $_inws_mode= 5;
  $_iexc_mode= 10;
}

// запись куки

$tarray=Array();
$tarray["userid"] = $forum_user_id;
$tarray["ctext"]  = $carray["ctext"];
$tarray["mtxt"]    = $_text_mode;
$tarray["text"]   = $_desc_mode;
$tarray["otkl"]   = $_otkl_mode;
$tarray["foto"]   = $_foto_mode;
$tarray["gmap"]   = $_gmap_mode;
$tarray["excv"]   = $_excv_mode;
$tarray["tblx"]   = $_tblx_mode;
$tarray["exct"]   = $_exct_mode;
$tarray["zagr"]   = $_zagr_mode;
$tarray["inws"]   = $_inws_mode;
$tarray["iexc"]   = $_iexc_mode;

if (($_SERVER['REQUEST_METHOD']=='GET') || ($script_name == 'sitemgr.php')) {
  setcookie($cookiename, serialize($tarray), time()+2592000, "/", $domainname);
  //if ($forum_user_id == 2) {
  //  error_log("set cookie for ".$forum_user_id." in ".$domainname);
  //}
}

$ctext = "";

if (($forum_user_id == '2') || ($forum_user_id == '3') || ($forum_user_id == '4')) {
  $_user_is_admin = "1";
}

if ($forum_user_id > 0) {
  $ctext = "forum:".$forum_user_id.":".$forum_user_name;
}
else {
  if (is_array($carray)) {
    $ctext = $carray["ctext"];
  }
}

// проверка не спамер ли

$_bad_count=0;
if (!($forum_user_id > 0)) {
  $sqlii = "select count(*) as cnt from otklikm where (cook='$ctext') and (time>(" . time() . " - 72000))";
  $result = $dbi->query($sqlii);
  if ($row = mysqli_fetch_array($result)) {
    $_bad_count = $row["cnt"];
  }
  $sqlii = "select count(*) as cnt from otklikt where cook='$ctext' and ido<0 and (time>(" . time() . " - 720000))";
  $result = $dbi->query($sqlii);
  if ($row = mysqli_fetch_array($result)) {
    $_bad_count += $row["cnt"];
  }
  mysqli_free_result($result);
  $sqlii = "";
}
$dispgmap = 0;

?>
