<?php
// =======================
// ХАБ КОРЗИН ПОЛЬЗОВАТЕЛЯ
// include/basket/basket_hub.php
// =======================
// Важно: хаб предполагает, что есть $dbi и $forum_user_id

$current_user_id = (int)($forum_user_id ?? 0);

// ---------- AJAX/POST actions (внутри хаба) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    header('Content-Type: application/json; charset=utf-8');

    if (!$current_user_id) {
        echo json_encode(['ok' => 0, 'err' => 'not_auth']);
        exit;
    }

    $action = (string)$_POST['action'];

    // создать новую корзину и сразу сделать текущей
    if ($action === 'create_new') {

        $title = 'Новая поездка';

        $dbi->query("
            INSERT INTO basket (userid, comment, descrip, sum, id_rep)
            VALUES ($current_user_id, '" . addslashes($title) . "', '', 0, NULL)
        ");

        $new_id = (int)$dbi->insert_id;

        if ($new_id > 0) {
            $dbi->query("
                UPDATE bl_users
                SET user_basket = $new_id
                WHERE user_id = $current_user_id
            ");

            echo json_encode(['ok' => 1, 'id' => $new_id]);
            exit;
        }

        echo json_encode(['ok' => 0, 'err' => 'create_failed']);
        exit;
    }

    // сделать существующую корзину текущей
    if ($action === 'set_current') {

        $bid = (int)($_POST['id'] ?? 0);
        if ($bid <= 0) {
            echo json_encode(['ok' => 0, 'err' => 'bad_id']);
            exit;
        }

        // проверка, что корзина принадлежит пользователю
        $chk = $dbi->query("
            SELECT id
            FROM basket
            WHERE id = $bid
              AND userid = $current_user_id
            LIMIT 1
        ")->fetch_assoc();

        if (!$chk) {
            echo json_encode(['ok' => 0, 'err' => 'not_owner']);
            exit;
        }

        $dbi->query("
            UPDATE bl_users
            SET user_basket = $bid
            WHERE user_id = $current_user_id
        ");

        echo json_encode(['ok' => 1]);
        exit;
    }

    // удалить корзину (поездку) каскадно
    if ($action === 'delete_basket') {

        $bid = (int)($_POST['id'] ?? 0);
        if ($bid <= 0) { echo json_encode(['ok'=>0,'err'=>'bad_id']); exit; }

        // проверка владения + id_rep (если опубликовано — не удаляем)
        $row = $dbi->query("
            SELECT id, id_rep
            FROM basket
            WHERE id = $bid
              AND userid = $current_user_id
            LIMIT 1
        ")->fetch_assoc();

        if (!$row) { echo json_encode(['ok'=>0,'err'=>'not_owner']); exit; }

        if (!is_null($row['id_rep'])) {
            echo json_encode(['ok'=>0,'err'=>'published']);
            exit;
        }

        // 1) черновики
        $dbi->query("DELETE FROM basket_draft WHERE basket_id = $bid");

        // 2) блоки -> точки
        $block_ids = [];
        $rb = $dbi->query("SELECT id FROM basket_blocks WHERE basket_id = $bid");
        while ($rb && ($b = $rb->fetch_assoc())) { $block_ids[] = (int)$b['id']; }

        if ($block_ids) {
            $ids = implode(',', $block_ids);
            $dbi->query("DELETE FROM basket_points WHERE block_id IN ($ids)");
            $dbi->query("DELETE FROM basket_blocks WHERE id IN ($ids)");
        }

        // 3) сама корзина
        $dbi->query("DELETE FROM basket WHERE id = $bid AND userid = $current_user_id");

        // 4) если удалили текущую — назначаем новую текущую (последнюю по id), иначе создаём
        $u = $dbi->query("SELECT user_basket FROM bl_users WHERE user_id = $current_user_id")->fetch_assoc();
        $user_basket_now = (int)($u['user_basket'] ?? 0);

        if ($user_basket_now === $bid) {

            $nx = $dbi->query("
                SELECT id
                FROM basket
                WHERE userid = $current_user_id
                ORDER BY id DESC
                LIMIT 1
            ")->fetch_assoc();

            if ($nx) {
                $new_current = (int)$nx['id'];
                $dbi->query("UPDATE bl_users SET user_basket = $new_current WHERE user_id = $current_user_id");
                echo json_encode(['ok'=>1,'new_current'=>$new_current]);
                exit;
            } else {
                // корзин больше нет — создаём новую и делаем текущей
                $title = 'Новая поездка';
                $dbi->query("
                    INSERT INTO basket (userid, comment, descrip, sum, id_rep)
                    VALUES ($current_user_id, '" . addslashes($title) . "', '', 0, NULL)
                ");
                $new_id = (int)$dbi->insert_id;
                if ($new_id > 0) {
                    $dbi->query("UPDATE bl_users SET user_basket = $new_id WHERE user_id = $current_user_id");
                    echo json_encode(['ok'=>1,'new_current'=>$new_id]);
                    exit;
                }
            }
        }

        echo json_encode(['ok'=>1]);
        exit;
    }

    echo json_encode(['ok' => 0, 'err' => 'unknown_action']);
    exit;
}

// ---------- UI (HTML) ----------
if (!$current_user_id) {
   echo ' <h1 >Корзина — мои поездки</h1>';
   include("include/faq_mytrip.php"); 
   echo '<p class="note-block">Корзина — это инструмент планирования поездки.<br>
Вы можете собирать интересные объекты, выстраивать маршрут по дням
и сохранять свои поездки. Для работы с корзиной необходимо войти на сайт.

</p>';
   echo '<a href="/sitemgr.php?return=%2Fbasket_v.php">
Войти или зарегистрироваться</a>';

    return;
}

// получаем user_basket
$u = $dbi->query("
    SELECT user_basket
    FROM bl_users
    WHERE user_id = $current_user_id
")->fetch_assoc();

$user_basket = (int)($u['user_basket'] ?? 0);

// проверяем существование текущей корзины
$basket_ok = false;

if ($user_basket > 0) {
    $chk = $dbi->query("
        SELECT id
        FROM basket
        WHERE id = $user_basket
          AND userid = $current_user_id
        LIMIT 1
    ")->fetch_assoc();

    if ($chk) {
        $basket_ok = true;
    }
}

// если текущей нет — создаём новую и назначаем
if (!$basket_ok) {

    $title = 'Новая поездка';

    $dbi->query("
        INSERT INTO basket (userid, comment, descrip, sum, id_rep)
        VALUES ($current_user_id, '" . addslashes($title) . "', '', 0, NULL)
    ");

    $new_id = (int)$dbi->insert_id;

    if ($new_id > 0) {
        $dbi->query("
            UPDATE bl_users
            SET user_basket = $new_id
            WHERE user_id = $current_user_id
        ");
        $user_basket = $new_id;
    }
}

// список всех корзин пользователя
$baskets = [];
$res = $dbi->query("
    SELECT id, comment, id_rep
    FROM basket
    WHERE userid = $current_user_id
    ORDER BY id DESC
");

while ($r = $res->fetch_assoc()) {
    $baskets[] = $r;
}
?>

<h1 >Корзина — мои поездки</h1>
<?php include("include/faq_mytrip.php"); ?>

    <div class="editor-section" style="margin-bottom:10px;">
        Текущая корзина для добавления объектов:
    

    <?php
    $current_title = '';
    foreach ($baskets as $b) {
        if ((int)$b['id'] === $user_basket) {
            $current_title = (string)$b['comment'];
            break;
        }
    }
    ?>

     <div style="font-size:18px; font-weight:600;">
            <?= htmlspecialchars($current_title ?: 'Без названия') ?>
        

        <a class="btn-outline" href="/basket_v.php/<?= (int)$user_basket ?>">Открыть редактор</a>

        <button class="btn-main" type="button" onclick="BasketHubCreateNew()">
            + Создать новую корзину
        </button>
    </div>
 </div>
<div class="note-block">
   Здесь вы управляете своими маршрутами.
   <br>
Одна поездка может быть текущей — именно в неё добавляются объекты с сайта.
Другие остаются как архив или заготовки будущих путешествий.
<br>
Вы можете в любой момент сменить текущую поездку или создать новую.
    
</div>


<div class="editor-section">
 

    <table class="table table-sm">
      
        <tbody>

        <?php foreach ($baskets as $b):
            $bid = (int)$b['id'];
            $is_current = ($bid === $user_basket);
            $is_published = !is_null($b['id_rep']);
        ?>
            <tr <?= $is_current ? 'style="background:#f7f7f7;"' : '' ?>>
                <td>
                    <?= htmlspecialchars(($b['comment'] ?: 'Без названия')) ?>
                    <?php if ($is_current): ?>
                        <span style="color:#888;">(текущая)</span>
                    <?php endif; ?>
                    <?php if ($is_published): ?>
                        <span style="color:#888;">(в отчётах)</span>
                    <?php endif; ?>
                </td>

                <td>
                    <a class="btn-outline" href="/basket_v.php/<?= $bid ?>">Редактировать</a>
                </td>

                <td>
                    <?php if (!$is_current): ?>
                        <button class="btn-light" type="button" onclick="BasketHubSetCurrent(<?= $bid ?>)">
                            Сделать текущей
                        </button>
                    <?php endif; ?>
                </td>

                <td>
                    <?php if (!$is_published): ?>
                        <button class="btn-light" type="button" onclick="BasketHubDelete(<?= $bid ?>)">
                            Удалить
                        </button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>

        </tbody>
    </table>

    <button class="btn-main" type="button" onclick="BasketHubCreateNew()">
        + Создать новую корзину
    </button>
</div>

<script>
async function BasketHubPost(payload) {
    const body = new URLSearchParams(payload).toString();
    const r = await fetch('/basket_v.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body
    });
    return await r.json();
}

async function BasketHubSetCurrent(id) {
    const data = await BasketHubPost({action: 'set_current', id: String(id)});
    if (data && data.ok) {
        location.reload();
    } else {
        alert('Не удалось сделать корзину текущей: ' + (data && data.err ? data.err : 'ошибка'));
    }
}

async function BasketHubCreateNew() {
    const data = await BasketHubPost({action: 'create_new'});
    if (data && data.ok && data.id) {
        location.href = '/basket_v.php/' + data.id;
    } else {
        alert('Не удалось создать корзину: ' + (data && data.err ? data.err : 'ошибка'));
    }
}

async function BasketHubDelete(id) {
    if (!confirm('Удалить поездку? Это удалит блоки, строки и черновики.')) return;

    const data = await BasketHubPost({action: 'delete_basket', id: String(id)});

    if (data && data.ok) {
        location.reload();
    } else {
        const err = (data && data.err) ? data.err : 'ошибка';
        if (err === 'published') {
            alert('Эта поездка опубликована в отчётах и не может быть удалена.');
        } else {
            alert('Не удалось удалить: ' + err);
        }
    }
}
</script>
