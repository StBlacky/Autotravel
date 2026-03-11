<?php
include_once("include/config.php");
include_once("include/func.php");
include_once("include/head00.php");

/* ============================================================
   1. Получаем ID исходной корзины
   ============================================================ */
if (isset($_pin) && preg_match('/^\/(\d+)$/', $_pin, $m)) {
    $_GET['id'] = $m[1];
}

$old_id = (int)($_GET["id"] ?? 0);
if (!$old_id) {
    echo "Не указана корзина.";
    exit;
}

/* ============================================================
   2. Загружаем старую корзину
   ============================================================ */
$sql = "SELECT * FROM basket WHERE id = $old_id";
$res = $dbi->query($sql);
$old = $res ? $res->fetch_assoc() : null;

if (!$old) {
    echo "Корзина не найдена.";
    exit;
}

$old_comment = trim($old["comment"]);
$old_descrip = $old["descrip"];
$old_sum     = (int)$old["sum"];

$res->free();

/* ============================================================
   3. Формируем имя новой корзины
   ============================================================ */
if ($old_comment) {
    $new_comment = "Копия: " . $old_comment;
} else {
    $new_comment = "Копия: N " . $old_id;
}

/* ============================================================
   4. СОЗДАЁМ НОВУЮ КОРЗИНУ
      ВАЖНО: ВЛАДЕЛЕЦ = ТЕКУЩИЙ ПОЛЬЗОВАТЕЛЬ, а не автор оригинала
   ============================================================ */

$sql = sprintf(
    "INSERT INTO basket (userid, comment, descrip, sum)
     VALUES (%d, '%s', '%s', %d)",
    $forum_user_id,  // ← правильный владелец копии!
    $dbi->real_escape_string($new_comment),
    $dbi->real_escape_string($old_descrip),
    $old_sum
);

$dbi->query($sql);

$new_id = $dbi->insert_id;


// --------------------------------------------
// Новая корзина становится активной для юзера
// --------------------------------------------
$dbi->query("
    UPDATE bl_users
    SET user_basket = $new_id
    WHERE user_id = $forum_user_id
");

/* ============================================================
   5. Копируем блоки (basket_blocks)
   ============================================================ */

$map_blocks = [];

$resBlocks = $dbi->query("
    SELECT * FROM basket_blocks
    WHERE basket_id = $old_id
    ORDER BY sort
");

if ($resBlocks) {
    while ($blk = $resBlocks->fetch_assoc()) {

        $sql = sprintf(
            "INSERT INTO basket_blocks (basket_id, title, sort)
             VALUES (%d, '%s', %d)",
            $new_id,
            $dbi->real_escape_string($blk["title"]),
            (int)$blk["sort"]
        );

        $dbi->query($sql);

        $new_block_id = $dbi->insert_id;
        $old_block_id = (int)$blk["id"];

        $map_blocks[$old_block_id] = $new_block_id;
    }
    $resBlocks->free();
}

/* ============================================================
   6. Копируем точки (basket_points)
   ============================================================ */
foreach ($map_blocks as $old_block_id => $new_block_id) {

    $resPoints = $dbi->query("
        SELECT * FROM basket_points
        WHERE block_id = $old_block_id
        ORDER BY sort
    ");

    if ($resPoints) {
        while ($p = $resPoints->fetch_assoc()) {

            $sql = sprintf(
                "INSERT INTO basket_points (block_id, type, object_id, text, sort)
                 VALUES (%d, '%s', %s, '%s', %d)",
                $new_block_id,
                $dbi->real_escape_string($p["type"]),
                is_null($p["object_id"]) ? "NULL" : (int)$p["object_id"],
                $dbi->real_escape_string($p["text"]),
                (int)$p["sort"]
            );

            $dbi->query($sql);
        }
        $resPoints->free();
    }
}

/* ============================================================
   7. Переходим к новой корзине
   ============================================================ */
header("Location: /basket_v.php/$new_id");
exit;

?>

