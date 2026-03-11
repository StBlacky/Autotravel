<?php
/**
 * auth_ajax.php — ядро для всех AJAX-запросов
 * -------------------------------------------
 * Подключает БД, делает лёгкую авторизацию, НЕ вызывает head00.php.
 *
 * Экспортируемые переменные:
 *   $dbi
 *   $ipp
 *   $ctext
 *   $forum_user_id
 *   $forum_user_name
 *   $forum_user_banned
 */

// ------------------------------------------------------------
// 1. Подключаем конфиг
// ------------------------------------------------------------
require_once __DIR__ . '/../include/config.php';
require_once __DIR__ . '/../include/func.php';
require_once __DIR__ . '/../include/func_bs.php';

// ------------------------------------------------------------
// 2. Подключение к базе
// ------------------------------------------------------------
$dbi = new mysqli($dbhost, $dbuser, $dbpasswd, $dbname);

if ($dbi->connect_errno) {
    die("Ошибка базы данных");
}

$dbi->query("SET NAMES 'utf8'");

// ------------------------------------------------------------
// 3. Базовые переменные
// ------------------------------------------------------------
$ipp = $_SERVER["HTTP_X_REAL_IP"]
    ?? $_SERVER["REMOTE_ADDR"]
    ?? "";

$ctext = "";

$forum_user_id     = 0;
$forum_user_name   = "";
$forum_user_banned = 0;

// ------------------------------------------------------------
// 4. Читаем cookie автотравела
// ------------------------------------------------------------
if (isset($_COOKIE[$cookiename])) {

    $raw = stripslashes($_COOKIE[$cookiename]);
    $carray = @unserialize($raw);

    if (is_array($carray)) {

        // токен
        if (!empty($carray['ctext'])) {
            $ctext = $carray['ctext'];
        }

        // пользователь
        if (!empty($carray['userid'])) {

            $uid = (int)$carray['userid'];

            $sql = "SELECT username, user_type
                    FROM bl_users
                    WHERE user_id = {$uid}
                    LIMIT 1";

            if ($res = $dbi->query($sql)) {
                if ($row = mysqli_fetch_assoc($res)) {
                    $forum_user_id     = $uid;
                    $forum_user_name   = $row['username'];
                    $forum_user_banned = ($row['user_type'] == 2 ? 1 : 0);
                }
                mysqli_free_result($res);
            }
        }
    }
}

// ------------------------------------------------------------
// 5. Гость → генерируем идентификатор
// ------------------------------------------------------------
if (!$ctext) {
    $agent = $_SERVER["HTTP_USER_AGENT"] ?? "";
    $ctext = hash('sha256', time() . $ipp . $agent);
}

