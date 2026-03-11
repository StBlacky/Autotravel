<?php
// ===============================
//  basket_api.php
//  AJAX API для basket_v.php
// ===============================

require_once __DIR__ . '/auth_ajax.php';

header("Content-Type: application/json; charset=utf-8");

// Проверяем авторизацию
if (!$forum_user_id) {
    echo json_encode(["error" => "not_logged_in"]);
    exit;
}

$action    = $_POST["action"]    ?? "";
$basket_id = (int)($_POST["basket_id"] ?? 0);

// Для дублирования: исходная корзина может быть чужой
$source_basket_id = (int)($_POST["source_basket_id"] ?? 0);

// ---------------------- Проверка владельца ----------------------
// Владелец обязателен для всех действий, КРОМЕ duplicate_basket
if ($basket_id > 0 && $action !== 'duplicate_basket') {

    $row = $dbi->query("SELECT userid FROM basket WHERE id=$basket_id")->fetch_assoc();
    if (!$row) {
        echo json_encode(["error" => "basket_not_found"]);
        exit;
    }

    $owner_id = (int)$row["userid"];
    if ($owner_id !== $forum_user_id) {
        echo json_encode(["error" => "not_owner"]);
        exit;
    }
}

// =========================
//        ACTIONS
// =========================

switch ($action) {

    // -------------------------------------------------------
    // SAVE TRIP TITLE
    // -------------------------------------------------------
    case "save_trip_title":
        $title = $dbi->real_escape_string($_POST["title"] ?? "");
        $dbi->query("UPDATE basket SET comment='$title' WHERE id=$basket_id");
        echo json_encode(["ok" => 1]);
        break;


    // -------------------------------------------------------
    // SAVE BLOCK TITLE
    // -------------------------------------------------------
    case "save_block_title":
        $block_id = (int)$_POST["block_id"];
        $title    = $dbi->real_escape_string($_POST["title"] ?? "");

        $dbi->query("UPDATE basket_blocks SET title='$title' WHERE id=$block_id");
        echo json_encode(["ok" => 1]);
        break;


    // -------------------------------------------------------
    // CREATE BLOCK
    // -------------------------------------------------------
    case "create_block":

        $q = $dbi->query("SELECT MAX(sort) AS m FROM basket_blocks WHERE basket_id=$basket_id");
        $m = $q->fetch_assoc();
        $sort = (($m["m"] ?? 0) + 1);

        $dbi->query("
            INSERT INTO basket_blocks (basket_id, title, sort, created_at, updated_at)
            VALUES ($basket_id, 'Новый блок', $sort, NOW(), NOW())
        ");

        echo json_encode([
            "ok"       => 1,
            "block_id" => $dbi->insert_id,
            "title"    => "Новый блок"
        ]);
        break;


    // -------------------------------------------------------
    // DELETE BLOCK + ALL POINTS
    // -------------------------------------------------------
    case "delete_block":

        $block_id = (int)$_POST["block_id"];

        $dbi->query("DELETE FROM basket_points WHERE block_id=$block_id");
        $dbi->query("DELETE FROM basket_blocks WHERE id=$block_id");

        echo json_encode(["ok" => 1]);
        break;


    // -------------------------------------------------------
    // CREATE EMPTY COMMENT (COMMENT type)
    // -------------------------------------------------------
    case "create_comment":

        $block_id = (int)$_POST["block_id"];

        $q = $dbi->query("SELECT MAX(sort) AS m FROM basket_points WHERE block_id=$block_id");
        $m = $q->fetch_assoc();
        $sort = (($m["m"] ?? 0) + 1);

        $dbi->query("
            INSERT INTO basket_points (block_id, type, object_id, text, sort, created_at, updated_at)
            VALUES ($block_id, 'COMMENT', NULL, '', $sort, NOW(), NOW())
        ");

        echo json_encode([
            "ok"       => 1,
            "point_id" => $dbi->insert_id
        ]);
        break;


    // -------------------------------------------------------
    // SAVE TEXT IN ANY POINT
    // -------------------------------------------------------
    case "save_text":

        $point_id = (int)$_POST["point_id"];
        $txt      = $dbi->real_escape_string($_POST["text"] ?? "");

        $dbi->query("
            UPDATE basket_points
               SET text='$txt', updated_at=NOW()
             WHERE id=$point_id
        ");

        echo json_encode(["ok" => 1]);
        break;


    // -------------------------------------------------------
    // REORDER POINTS INSIDE BLOCK
    // -------------------------------------------------------
    case "reorder_points":

        $block_id = (int)$_POST["block_id"];
        $order    = $_POST["order"] ?? "";

        if ($order) {
            $ids = explode(",", $order);
            $sort = 1;

            foreach ($ids as $pid) {
                $pid = (int)$pid;
                if ($pid > 0) {
                    $dbi->query("UPDATE basket_points SET sort=$sort WHERE id=$pid AND block_id=$block_id");
                    $sort++;
                }
            }
        }

        echo json_encode(["ok" => 1]);
        break;


    // -------------------------------------------------------
    // REORDER BLOCKS
    // -------------------------------------------------------
    case "reorder_blocks":

        $order = $_POST["order"] ?? "";

        if ($order) {
            $ids = explode(",", $order);
            $sort = 1;

            foreach ($ids as $bid) {
                $bid = (int)$bid;
                if ($bid > 0) {
                    $dbi->query("UPDATE basket_blocks SET sort=$sort WHERE id=$bid AND basket_id=$basket_id");
                    $sort++;
                }
            }
        }

        echo json_encode(["ok" => 1]);
        break;


    // -------------------------------------------------------
    // MOVE POINT (drag & drop between blocks)
    // -------------------------------------------------------
    case "move_point":

        $point_id     = (int)$_POST["point_id"];
        $new_block_id = (int)$_POST["new_block_id"];

        $dbi->query("
            UPDATE basket_points
               SET block_id=$new_block_id
             WHERE id=$point_id
        ");

        echo json_encode(["ok" => 1]);
        break;


    // -------------------------------------------------------
    // PASTE CUT POINTS
    // -------------------------------------------------------
    case "paste_points":

        $ids_raw         = $_POST["ids"] ?? "";
        $target_point_id = (int)($_POST["target_point_id"] ?? 0);
        $target_block_id = (int)($_POST["target_block_id"] ?? 0);

        if (!$ids_raw || !$target_block_id) {
            echo json_encode(["error" => "invalid_params"]);
            exit;
        }

        $ids = array_filter(array_map('intval', explode(",", $ids_raw)));

        if (!count($ids)) {
            echo json_encode(["error" => "empty_ids"]);
            exit;
        }

        // sort точки, после которой вставляем
        $insert_after_sort = 0;

        if ($target_point_id > 0) {
            $row = $dbi->query("
                SELECT sort FROM basket_points
                WHERE id=$target_point_id
            ")->fetch_assoc();

            if ($row) {
                $insert_after_sort = (int)$row["sort"];
            }
        }

        // если вставляем в пустой блок или не указан target_point_id
        if ($insert_after_sort === 0) {

            $row = $dbi->query("
                SELECT MAX(sort) AS m
                FROM basket_points
                WHERE block_id=$target_block_id
            ")->fetch_assoc();

            $insert_after_sort = (int)($row["m"] ?? 0);

        } else {
            // сдвигаем вниз существующие строки
            $dbi->query("
                UPDATE basket_points
                   SET sort = sort + " . count($ids) . "
                 WHERE block_id=$target_block_id
                   AND sort > $insert_after_sort
            ");
        }

        // вставляем сами строки
        $offset = 1;
        foreach ($ids as $pid) {

            $new_sort = $insert_after_sort + $offset;

            $dbi->query("
                UPDATE basket_points
                   SET block_id=$target_block_id,
                       sort=$new_sort
                 WHERE id=$pid
            ");

            $offset++;
        }

        echo json_encode(["ok" => 1]);
        break;


    // -------------------------------------------------------
    // SAVE SNAPSHOT
    // -------------------------------------------------------
    case "save_snapshot":

        $q = $dbi->query("SELECT COUNT(*) AS c FROM basket_draft WHERE basket_id=$basket_id");
        $n = $q->fetch_assoc()["c"] ?? 0;

        if ($n >= 3) {
            echo json_encode(["error" => "limit"]);
            exit;
        }

        $draft = ["blocks" => []];

        $res = $dbi->query("
            SELECT * FROM basket_blocks
            WHERE basket_id=$basket_id
            ORDER BY sort ASC
        ");

        while ($b = $res->fetch_assoc()) {
            $bid = (int)$b["id"];

            $block = [
                "id"    => $bid,
                "title" => $b["title"],
                "sort"  => $b["sort"],
                "points"=> []
            ];

            $rp = $dbi->query("
                SELECT *
                FROM basket_points
                WHERE block_id=$bid
                ORDER BY sort ASC
            ");

            while ($p = $rp->fetch_assoc()) {
                $block["points"][] = [
                    "id"        => (int)$p["id"],
                    "type"      => $p["type"],
                    "object_id" => (int)$p["object_id"],
                    "text"      => $p["text"],
                    "sort"      => (int)$p["sort"]
                ];
            }

            $draft["blocks"][] = $block;
        }

        $json = $dbi->real_escape_string(json_encode($draft, JSON_UNESCAPED_UNICODE));

        $dbi->query("
            INSERT INTO basket_draft (basket_id, draft_json, created_at)
            VALUES ($basket_id, '$json', NOW())
        ");

        echo json_encode(["ok" => 1]);
        break;


    // -------------------------------------------------------
    // RESTORE SNAPSHOT
    // -------------------------------------------------------
    case "restore_snapshot":

        $snapshot_id = (int)$_POST["snapshot_id"];

        $row = $dbi->query("SELECT draft_json FROM basket_draft WHERE id=$snapshot_id")->fetch_assoc();
        if (!$row) {
            echo json_encode(["error" => "not_found"]);
            exit;
        }

        $data = json_decode($row["draft_json"], true);

        // чистка
        $dbi->query("DELETE FROM basket_points WHERE block_id IN (SELECT id FROM basket_blocks WHERE basket_id=$basket_id)");
        $dbi->query("DELETE FROM basket_blocks WHERE basket_id=$basket_id");

        // восстановление
        foreach ($data["blocks"] as $b) {

            $title = $dbi->real_escape_string($b["title"]);
            $sort  = (int)$b["sort"];

            $dbi->query("
                INSERT INTO basket_blocks (id, basket_id, title, sort, created_at, updated_at)
                VALUES ({$b['id']}, $basket_id, '$title', $sort, NOW(), NOW())
            ");

            foreach ($b["points"] as $p) {

                $text = $dbi->real_escape_string($p["text"]);

                $obj = isset($p["object_id"]) ? (int)$p["object_id"] : 0;
                $obj_sql = ($p["type"] === "OBJECT") ? $obj : "NULL";

                $dbi->query("
                    INSERT INTO basket_points (id, block_id, type, object_id, text, sort, created_at, updated_at)
                    VALUES ({$p['id']}, {$b['id']}, '{$p['type']}', $obj_sql, '$text', {$p['sort']}, NOW(), NOW())
                ");
            }
        }

        echo json_encode(["ok" => 1]);
        break;

        // -------------------------------------------------------
// DELETE POINT (hard delete)
// -------------------------------------------------------
case "delete_point":

    $point_id = (int)($_POST["point_id"] ?? 0);
    if ($point_id <= 0) {
        echo json_encode(["error" => "bad_point_id"]);
        exit;
    }

    // Проверим, что точка принадлежит корзине basket_id (и значит владельцу, т.к. owner-check выше уже прошёл)
    $row = $dbi->query("
        SELECT p.id
        FROM basket_points p
        JOIN basket_blocks b ON b.id = p.block_id
        WHERE p.id = $point_id
          AND b.basket_id = $basket_id
        LIMIT 1
    ")->fetch_assoc();

    if (!$row) {
        echo json_encode(["error" => "point_not_found"]);
        exit;
    }

    $dbi->query("DELETE FROM basket_points WHERE id = $point_id");
    echo json_encode(["ok" => 1]);
    break;


    // -------------------------------------------------------
    // DELETE SNAPSHOT
    // -------------------------------------------------------
    case "delete_snapshot":

        $snapshot_id = (int)$_POST["snapshot_id"];
        $dbi->query("DELETE FROM basket_draft WHERE id=$snapshot_id");

        echo json_encode(["ok" => 1]);
        break;


    // -------------------------------------------------------
    // DUPLICATE basket in new basket for current user
    // -------------------------------------------------------
    case "duplicate_basket":

        $src_id = $source_basket_id;
        if ($src_id <= 0) {
            echo json_encode(["error" => "bad_source_id"]);
            exit;
        }

        $src = $dbi->query("
            SELECT id, comment, descrip
            FROM basket
            WHERE id=$src_id
            LIMIT 1
        ")->fetch_assoc();

        if (!$src) {
            echo json_encode(["error" => "basket_not_found"]);
            exit;
        }

        $dbi->query("START TRANSACTION");

        try {

            $new_title = trim((string)$src["comment"]);
            if ($new_title === "") $new_title = "Без названия";
            $new_title = $dbi->real_escape_string($new_title . " (копия)");

            $new_descrip = $dbi->real_escape_string((string)($src["descrip"] ?? ""));

            $dbi->query("
                INSERT INTO basket (userid, comment, descrip, sum, id_rep)
                VALUES ($forum_user_id, '$new_title', '$new_descrip', 0, NULL)
            ");

            $new_basket_id = (int)$dbi->insert_id;
            if ($new_basket_id <= 0) throw new Exception("create_failed");

            // копируем блоки
            $map = []; // old_block_id => new_block_id

            $rb = $dbi->query("
                SELECT id, title, sort
                FROM basket_blocks
                WHERE basket_id=$src_id
                ORDER BY sort ASC, id ASC
            ");

            while ($rb && ($b = $rb->fetch_assoc())) {

                $old_block_id = (int)$b["id"];
                $title = $dbi->real_escape_string((string)$b["title"]);
                $sort  = (int)$b["sort"];

                $dbi->query("
                    INSERT INTO basket_blocks (basket_id, title, sort, created_at, updated_at)
                    VALUES ($new_basket_id, '$title', $sort, NOW(), NOW())
                ");

                $new_block_id = (int)$dbi->insert_id;
                if ($new_block_id <= 0) throw new Exception("block_copy_failed");

                $map[$old_block_id] = $new_block_id;
            }

            // копируем точки
            foreach ($map as $old_block_id => $new_block_id) {

                $rp = $dbi->query("
                    SELECT type, object_id, text, sort
                    FROM basket_points
                    WHERE block_id=$old_block_id
                    ORDER BY sort ASC, id ASC
                ");

                while ($rp && ($p = $rp->fetch_assoc())) {

                    $type = ($p["type"] === "OBJECT") ? "OBJECT" : "COMMENT";
                    $object_id = isset($p["object_id"]) ? (int)$p["object_id"] : 0;
                    $text = $dbi->real_escape_string((string)($p["text"] ?? ""));
                    $sort = (int)($p["sort"] ?? 0);

                    $obj_sql = ($type === "OBJECT") ? $object_id : "NULL";

                    $dbi->query("
                        INSERT INTO basket_points (block_id, type, object_id, text, sort, created_at, updated_at)
                        VALUES ($new_block_id, '$type', $obj_sql, '$text', $sort, NOW(), NOW())
                    ");
                }
            }

            // делаем новую корзину текущей
            $dbi->query("
                UPDATE bl_users
                   SET user_basket=$new_basket_id
                 WHERE user_id=$forum_user_id
            ");

            $dbi->query("COMMIT");
            echo json_encode(["ok" => 1, "basket_id" => $new_basket_id]);
            break;

        } catch (Exception $e) {
            $dbi->query("ROLLBACK");
            echo json_encode(["error" => "duplicate_failed"]);
            break;
        }


    // -------------------------------------------------------
    // UNKNOWN ACTION
    // -------------------------------------------------------
    default:
        echo json_encode(["error" => "unknown_action"]);
        break;
}
