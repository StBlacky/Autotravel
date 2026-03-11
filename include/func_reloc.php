<?php

function my_relocate($sitename) {
  if (preg_match("/^\/otklik\.php\?ex\=(\d+)$/",$_SERVER["REQUEST_URI"],$_ttt)) {
    $newuri = "/otklik.php/" . $_ttt[1];
    Header( "HTTP/1.1 301 Moved Permanently" );
    Header( "Location: $newuri" );
    exit;
  }
  # артефакты от неабсолютных ссылок на otklik и tracestate - вернуть на место
  if (preg_match("/^\/index\.php\/.+$/",$_SERVER["REQUEST_URI"],$_ttt)) {
    $newuri = "/index.php" . $_ttt[1];
    Header( "HTTP/1.1 301 Moved Permanently" );
    Header( "Location: $newuri" );
    exit;
  }
  if (preg_match("/^\/towns\.php\?l\=7$/",$_SERVER["REQUEST_URI"],$_ttt)) {
    $newuri = "/countries.php" . $_ttt[1];
    Header( "HTTP/1.1 301 Moved Permanently" );
    Header( "Location: $newuri" );
    exit;
  }
  if (preg_match("/^\/towns\.php\?l\=6$/",$_SERVER["REQUEST_URI"],$_ttt)) {
    $newuri = "/areas.php" . $_ttt[1];
    Header( "HTTP/1.1 301 Moved Permanently" );
    Header( "Location: $newuri" );
    exit;
  }
  if (preg_match("/^\/towns\.php\/(\-*\d+)$/",$_SERVER["REQUEST_URI"],$_ttt)) {
    $newuri = "/area.php/" . $_ttt[1];
    Header( "HTTP/1.1 301 Moved Permanently" );
    Header( "Location: $newuri" );
    exit;
  }
  if (preg_match("/^\/town\.php\?town\=(\d+)$/",$_SERVER["REQUEST_URI"],$_ttt)) {
    $newuri = "/town.php/" . $_ttt[1];
    Header( "HTTP/1.1 301 Moved Permanently" );
    Header( "Location: $newuri" );
    exit;
  }
  if (preg_match("/^\/trace\.php\?town\=(\d+)$/",$_SERVER["REQUEST_URI"],$_ttt)) {
    $newuri = "/trace.php/" . $_ttt[1];
    Header( "HTTP/1.1 301 Moved Permanently" );
    Header( "Location: $newuri" );
    exit;
  }
  if (preg_match("/^\/excite\.php\?/",$_SERVER["REQUEST_URI"],$_ttt)) {
    $et = $_REQUEST["et"];
    $et = (int) mysafestring($et);
    $town = $_REQUEST["town"];
    $town = (int) mysafestring($town);
    $newuri = "/excite.php/" . $town . "/" . $et;
    Header( "HTTP/1.1 301 Moved Permanently" );
    Header( "Location: $newuri" );
    exit;
  }
  if (preg_match("/^\/towns\.php\?.*a\=(\d+)$/",$_SERVER["REQUEST_URI"],$_ttt)) {
    $newuri = "/towns.php/" . $_ttt[1];
    Header( "HTTP/1.1 301 Moved Permanently" );
    Header( "Location: $newuri" );
    exit;
  }
}


function my_auth2_yandex($id, $secret, $code) {
  $res = array('email' => '', 'result' => false, 'var1' => '', 'var2' => '');
  $params = array(
    'grant_type'    => 'authorization_code',
    'code'          => $code,
    'client_id'     => $id,
    'client_secret' => $secret
  );
  $url = 'https://oauth.yandex.ru/token';
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_POST, 1);
  curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
  $result = curl_exec($curl);
  curl_close($curl);
  $res['var1'] = $result;
  $tokenInfo = json_decode($result, true);
  
  if (isset($tokenInfo['access_token'])) {
    $params = array(
      'format'       => 'json',
      'oauth_token'  => $tokenInfo['access_token']
    );
    $ch =  curl_init('https://login.yandex.ru/info' . '?' . http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
    $result = curl_exec($ch);
    curl_close($ch);
    $res['var2'] = $result;
    $userInfo = json_decode($result, true);
    if (isset($userInfo['id'])) {
      $email = $userInfo['default_email'];
      if (preg_match('/^(.+)\@(.+)$/',$email,$m)) {
        $res['email'] = " '" . $email . "', '" . $m[1] . "@yandex.ru', '" . $m[1] . "@ya.ru' ";
        $res['result'] = true;
      }
      else {
        $res['result'] = false;
      }
    }
  }
  return $res;
  
}

function my_auth2_mailru($id, $secret, $code, $uri) {
  $res = array('email' => '', 'result' => false, 'var1' => '', 'var2' => '');
  $params = array(
    'grant_type'    => 'authorization_code',
    'code'          => $code,
    'redirect_uri'  => $uri,
    'client_id'     => $id,
    'client_secret' => $secret
  );
  $url = 'https://connect.mail.ru/oauth/token';
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_POST, 1);
  curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
  $result = curl_exec($curl);
  curl_close($curl);
  $res['var1'] = $result;
  $tokenInfo = json_decode($result, true);
  
  if (isset($tokenInfo['access_token'])) {
    $sign = md5("app_id=".$id."method=users.getInfosecure=1session_key=".$tokenInfo['access_token'].$secret);
    $params = array(
        'method'       => 'users.getInfo',
        'secure'       => '1',
        'app_id'       => $id,
        'session_key'  => $tokenInfo['access_token'],
        'sig'          => $sign
    );
    $ch =  curl_init('https://www.appsmail.ru/platform/api' . '?' . http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
    $result = curl_exec($ch);
    curl_close($ch);
    $res['var2'] = $result;
    $userInfo = json_decode($result, true);
    if (isset($userInfo[0]['uid'])) {
      $res["email"] = " '" .$userInfo[0]['email']."' ";
      $res["result"] = true;
    }
  }
  return $res;
}