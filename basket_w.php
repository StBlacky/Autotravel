<?php
include_once('include/config.php');
include_once('include/func.php');
include_once('include/head00.php');

if (!$forum_user_id) {
    exit;
}

/* ==============================
   Подключение MySQLi
   ============================== */
$dbi = new mysqli($dbhost, $dbuser, $dbpasswd, $dbname);
if ($dbi->connect_errno) {
    echo "Ошибка базы данных";
    exit;
}
$dbi->set_charset("utf8");

$id     = (int) $_REQUEST["id"];
$action = mysafestring($_REQUEST["a"]);
$obj    = (int) $_REQUEST["o"];

/* ==============================
   Выбор существующей корзины
   ============================== */

$b_user    = 0;
$b_descr   = "";
$b_content = "";
$b_count   = 0;

if ($id > 0) {
    $res = $dbi->query("SELECT * FROM basket WHERE userid=$forum_user_id AND id=$id LIMIT 1");
    if ($row = $res->fetch_assoc()) {
        $b_user    = (int)$row["userid"];
        $b_descr   = $row["comment"];
        $b_content = $row["descrip"];
        $b_count   = (int)$row["sum"];
    }
} else {
    // последняя корзина пользователя
    $res = $dbi->query("SELECT * FROM basket WHERE userid=$forum_user_id ORDER BY id DESC LIMIT 1");
    if ($row = $res->fetch_assoc()) {
        $id        = (int)$row["id"];
        $b_user    = (int)$row["userid"];
        $b_descr   = $row["comment"];
        $b_content = $row["descrip"];
        $b_count   = (int)$row["sum"];
    }
}

/* ==============================
   СТАРАЯ ЛОГИКА: запись в descrip
   ============================== */

if ($action == "add" && $obj > 0) {

    if ($id > 0) {
        // объект в старый формат
        $b_content = $b_content . "#|e" . $obj;
        $b_count++;

        $esc_content = $dbi->real_escape_string($b_content);

        $dbi->query("
            UPDATE basket 
            SET descrip='$esc_content', sum=$b_count 
            WHERE id=$id AND userid=$forum_user_id
        ");

    } else {
        // создаём корзину
        $b_content = "#|e" . $obj;
        $esc_content = $dbi->real_escape_string($b_content);

        $dbi->query("
            INSERT INTO basket (userid, comment, descrip, sum)
            VALUES ($forum_user_id, '', '$esc_content', 1)
        ");

        $id = $dbi->insert_id;
        $b_count = 1;
    }
}

/* ==============================
   НОВАЯ ЛОГИКА: blocks + points
   ============================== */

if ($action == "add" && $obj > 0 && $id > 0) {

    /* --- 1. Берём последний блок (по sort) --- */
    $res = $dbi->query("
        SELECT id 
        FROM basket_blocks 
        WHERE basket_id=$id 
        ORDER BY sort DESC 
        LIMIT 1
    ");

    if ($row = $res->fetch_assoc()) {

        $block_id = (int)$row["id"];

    } else {
        /* нет блоков → создаём первый */
        $dbi->query("
            INSERT INTO basket_blocks (basket_id, title, sort)
            VALUES ($id, '', 1)
        ");

        $block_id = $dbi->insert_id;

        $title = "Блок " . $block_id;
        $esc_title = $dbi->real_escape_string($title);

        $dbi->query("
            UPDATE basket_blocks 
            SET title='$esc_title' 
            WHERE id=$block_id
        ");
    }

    /* --- 2. Следующий sort в block_id --- */
    $res2 = $dbi->query("
        SELECT MAX(sort) AS maxs 
        FROM basket_points 
        WHERE block_id=$block_id
    ");

    $next_sort = 1;
    if ($r2 = $res2->fetch_assoc()) {
        $next_sort = ((int)$r2["maxs"]) + 1;
    }

    /* --- 3. Вставляем точку --- */
    $dbi->query("
        INSERT INTO basket_points (block_id, type, object_id, text, sort)
        VALUES ($block_id, 'OBJECT', $obj, '', $next_sort)
    ");
}

/* ==============================
   Ответ для интерфейса (как раньше)
   ============================== */

header("Content-Type: text/html; charset=utf-8");

if ($b_count == 0) {
    echo "Поездка $forum_user_basket_name пуста ";
} else {
    echo "Поездка $forum_user_basket_name: $b_count объектов";
}

?>
