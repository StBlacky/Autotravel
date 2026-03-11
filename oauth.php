<?php
include_once('include/config.php');
include_once('include/func.php');
include_once('include/func_bs.php');
include_once('include/func_reloc.php');
// откуда к нам пришли. два глобально разных варианта:
// 1. это первый заход (юзера надо послать на сервис авторизации)
// 2. это второй заход, юзер к нам вернулся с сервера авторизации: надо получить токен и собственно закончить авторизацию 

$_nonce = my_getstate();
$_loc = 'my'; // сервер авторизации.
// 'ya' => яндекс
// '' => форум
if (isset($_GET['return'])) {
  // первый вызов
  $_go = urldecode($_GET['return']);
}
else {
  $_go = 'https://autotravel.ru/sitemgr.php';
}

$_etap = 0; // первый вызов
if (isset($_GET['location'])) {
  // первый вызов
  $_loc = substr($_GET['location'],0,2);
}

$_var = array('loc'=>$_loc,'state'=>$_nonce,'go'=>$_go);

if (isset($_GET['state'])) {
  // не первый вызов
  $_etap = 1;
  $_var = (array)json_decode(base64_decode($_GET['state']));
  $_loc = $_var['loc'];
  if (isset($_var['go'])) {
    $_go = $_var['go'];
  }
}

if ($_loc == "ya") {
  $_my_id = '9f8d20070d2042bc9b8bef802a290762'; // Id приложения
  $_my_secret = '4d39da07c0144697bcaf3db630f3debb'; // Пароль приложения
  $_my_uri = 'https://autotravel.ru/oauth.php'; // Callback URI
}
if ($_loc == "mr") {
  $_my_id = 758367;
  $_my_key = 'e724c4410aad32d0640296592d9611ec';
  $_my_secret = 'b1a3288487b528b513eab27ef4ca7d65';
  $_my_uri = 'https://autotravel.ru/oauth.php'; // Callback URI
}
if ($_loc == "ms") {
  $_my_id = '16a05980-a5ac-42aa-ad9b-d129155d9518';
  $_my_secret = 'nUVLN15;kabykaGSS387!*|';
  $_my_uri = 'https://autotravel.ru/oauth.php'; // Callback URI
}
// 
if ($_etap === 0) {
  sleep(2);
  if ($_loc == "ya" ) {
    $url = 'https://oauth.yandex.ru/authorize?'.http_build_query(
      array(
        'response_type' => 'code',
        'client_id'     => $_my_id,
        'display'       => 'popup',
        'state'         => base64_encode(json_encode($_var))
      ));
    Header( "HTTP/1.1 301 Moved Permanently" );
    Header( "Location: $url" );
    exit;
  }
  if ($_loc == "mr" ) {
    $url = 'https://connect.mail.ru/oauth/authorize?'.http_build_query(
      array(
        'response_type' => 'code',
        'client_id'     => $_my_id,
        'redirect_uri'  => $_my_uri,
        'state'         => base64_encode(json_encode($_var))
      ));
    Header( "HTTP/1.1 301 Moved Permanently" );
    Header( "Location: $url" );
    exit;
  }
  //
}

if ($_etap == 1) {
  if ($_var['state'] === ($_nonce)) {
    if ($_loc == "ya") {
      $_auth2 = my_auth2_yandex($_my_id, $_my_secret, $_GET['code']);
    }
    elseif ($_loc == "mr") {
      $_auth2 = my_auth2_mailru($_my_id, $_my_secret, $_GET['code'], $_my_uri);
    }
    else {
      $_auth2 = array('result' => 0, 'error' => 'Не выбран провайдер электронной почты');
    }
  } 
  else {
    $_auth2 = array('result' => 0, 'error' => 'Не совпадает параметр state');
  }
}

include_once('include/head00.php');

if ($forum_user_id) {
  error_log('logged-in '.$forum_user_id);
  Header( "HTTP/1.1 301 Moved Permanently" );
  Header( "Location: ".$_go );
  exit;
}

$bootstrap = 1;
$noads = 1;
include('include/head01.php');
?>
<div class='container-fluid' style='width:100%;'>
<?php
  if ($_auth2['error']) {
    echo '<div class="col-sm-12 alert alert-danger"><p>Извините, авторизация неусппешна: '.$_auth2['error'].'</p></div>';
    echo '<pre>';
    var_dump($_auth2);
    echo '</pre>';
  }
  else {
    if ($forum_user_id) {
       echo '<div class="col-sm-12 alert alert-info"><p>Здравствуйте, '.$forum_user_name.'. Вы успешно вошли на сайт.</p></div>';
    }
    else {
      echo '<div class="col-sm-12 alert alert-danger"><p>Извините, случилась какая-то ошибка</p></div>';
      echo '<pre>';
      var_dump($_auth2);
      echo '</pre>';
    }
  }
  echo '<div class="col-sm-12 text-center"><a class=\"tb\" href="/">Вернуться на главную страницу сайта</а></div>';
?>
</div>
</body>
</html>