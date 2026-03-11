<?php
// РАННИЙ JSON-ХЕНДЛЕР ДЛЯ ХАБА КОРЗИН
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    include_once("include/head00.php");
    include __DIR__ . "/include/basket/basket_hub.php";
    exit;
}

include_once("include/head00.php");
 include_once("include/func_bs.php");

// ---------------------- Получение ID корзины ----------------------
if (isset($_pin) && preg_match('/^\/(\d+)$/', $_pin, $m)) {
    $_GET['id'] = $m[1];
}

$basket_id = (int)($_GET["id"] ?? 0);
if (!$basket_id) {
    // Обвязка ТОЛЬКО для хаба. Поведение с id не трогаем.
    $bootstrap = 1;
    include(__DIR__ . "/include/header_bs.php");
    echo '<div class="container-fluid" id="basket-hub">';
    include __DIR__ . "/include/basket/basket_hub.php";
    echo '</div>';

    include(__DIR__ . "/include/footer_bs.php");
    exit;
}

// ---------------------- Загружаем поездку ----------------------
$trip = $dbi->query("SELECT * FROM basket WHERE id=$basket_id")->fetch_assoc();
if (!$trip) { echo "Поездка не найдена."; exit; }

$trip_title      = $trip["comment"];
$basket_owner_id = (int)$trip["userid"];
$current_user_id = (int)$forum_user_id;

// строгая проверка
$is_owner = ($basket_owner_id == $current_user_id ? 1 : 0);

// ---------------------- Загружаем блоки ----------------------
$blocks = [];
$res = $dbi->query("
    SELECT *
    FROM basket_blocks
    WHERE basket_id=$basket_id
    ORDER BY sort ASC
");
while ($b = $res->fetch_assoc()) {
    $blocks[] = $b;
}

// ---------------------- Загружаем точки ----------------------
$points_by_block = [];
foreach ($blocks as $b) {

    $bid = (int)$b["id"];
    $points_by_block[$bid] = [];

    $q = $dbi->query("
        SELECT *
        FROM basket_points
        WHERE block_id=$bid
        ORDER BY sort ASC
    ");

    while ($p = $q->fetch_assoc()) {
        $points_by_block[$bid][] = $p;
    }
}

// ---------------------- Экспорт точек (OBJECT) ----------------------
$export_ids_set = [];
foreach ($blocks as $b) {
    $bid = (int)$b["id"];
    if (!isset($points_by_block[$bid])) continue;
    foreach ($points_by_block[$bid] as $p) {
        if (($p["type"] ?? "") !== "OBJECT") continue;
        $oid = (int)($p["object_id"] ?? 0);
        if ($oid > 0) $export_ids_set[$oid] = 1; // unique
    }
}
$export_ids = array_keys($export_ids_set);
$export_ids_str = implode(",", $export_ids);

// ---------------------- Загружаем черновики ----------------------
$snapshots = [];
$q = $dbi->query("
    SELECT *
    FROM basket_draft
    WHERE basket_id=$basket_id
    ORDER BY created_at DESC
");

if ($q) {
    while ($s = $q->fetch_assoc()) {
        $snapshots[] = $s;
    }
}

// ---------------------- Заголовок Bootstrap ----------------------
$bootstrap = 1;
include("include/header_bs.php");
?>

<link rel="stylesheet" href="/css/basket_new.css">



<div class="container-fluid" id="basket-editor">

    <script>
        window.BASKET_ID = <?= (int)$basket_id ?>;
        window.IS_OWNER  = <?= (int)$is_owner ?>;

        async function duplicateBasket(sourceId) {
            const body = new URLSearchParams({
                action: 'duplicate_basket',
                source_basket_id: String(sourceId)
            }).toString();

            const r = await fetch('/ajax/basket_api.php', {
                method: 'POST',
                headers: {'Content-Type':'application/x-www-form-urlencoded'},
                body
            });

            const txt = await r.text();
            let data = null;
            try { data = JSON.parse(txt); } catch(e) {
                alert('Ошибка ответа сервера (ожидали JSON). Первые 200 символов:\n' + txt.slice(0,200));
                return;
            }

            if (data && data.ok && data.basket_id) {
                location.href = '/basket_v.php/' + data.basket_id;
            } else {
                alert('Не удалось сделать клон: ' + ((data && data.error) ? data.error : 'ошибка'));
            }
        }
    </script>

 <!-- ========================= Название поездки ========================= -->

<div class="editor-section">

    <label>Название поездки:</label><h1>

    <?php if ($is_owner): ?>
        <input type="text"
               id="trip-title"
               value="<?= htmlspecialchars($trip_title) ?>"
               onblur="autosaveTripTitle()">
    <?php else: ?>
        <div class="readonly"><?= htmlspecialchars($trip_title) ?></div>
         <p class="note-block">Вы просматриваете чужую поездку (только чтение).</p>
    <?php endif; ?>
    </h1>
  
    <button type="button" class="btn-outline"
            onclick="window.open('/basket_map.php/<?= (int)$basket_id ?>')">
        Показать на карте
    </button>

    <?php 
            
    if ((int)$forum_user_id > 0): ?>
        <button type="button" class="btn-outline"
                onclick="duplicateBasket(<?= (int)$basket_id ?>)">
            Сделать клон
        </button>
    <?php endif; ?>

    

    <div class="note-block">
        Прямой линк: 
        <a href="/basket_v.php/<?= (int)$basket_id ?>">
            https://autotravel.ru/basket_v.php/<?= (int)$basket_id ?>
        </a><br>
        <?php if ($export_ids_str): ?>
        
            Скачать координаты точек поездки:
            <a  href="/kml.php?ids=<?= htmlspecialchars($export_ids_str) ?>&amp;f=wpt">WPT</a>&nbsp;
            <a  href="/kml.php?ids=<?= htmlspecialchars($export_ids_str) ?>&amp;f=kml">KML</a>&nbsp;
            <a  href="/kml.php?ids=<?= htmlspecialchars($export_ids_str) ?>&amp;f=gpx">GPX</a>
         <?php endif; ?>
    </div>
    <?php include("include/faq_mytrip.php"); if ($is_owner): ?>
    <?php if (count($snapshots) < 3): ?>
        <button class="btn-light" onclick="saveSnapshot()">Сохранить текущее состояние в черновик</button>
    <?php else: ?>
        <button class="btn-light" disabled>Лимит 3 черновика</button>
    <?php endif; ?>
    <button class="btn-light" onclick="createBlock()">+ Добавить блок</button>
<?php endif; ?>
        
  
</div>

<!-- ========================= Черновики ========================= -->

<?php if ($is_owner): ?>
<div class="editor-section">
    <label>Черновики:</label>



    <?php foreach ($snapshots as $s): ?>
        <div  id="snapshot-<?= $s['id'] ?>">
            Черновик <?= $s["id"] ?> 
            <small><?= $s["created_at"] ?></small>

            <button class="btn-outline" onclick="restoreSnapshot(<?= $s['id'] ?>)">Восстановить</button>
            <button class="btn-light" onclick="deleteSnapshot(<?= $s['id'] ?>)">Удалить</button>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>




<!-- ========================= Блоки ========================= -->



<div id="blocks">

<?php foreach ($blocks as $b):
    $bid = (int)$b["id"];
?>
    <div class="basket-block" data-block-id="<?= $bid ?>">

        <!-- Заголовок блока -->
        <div class="block-header" id="block-<?= $bid ?>">
            <span class="block-drag-handle">☰</span>

            <span class="toggle-block" onclick="toggleBlock(this)">▼</span>
            <!-- Кнопка карты блока (видна всем) -->
            <a href="/basket_map.php/<?= (int)$basket_id ?>?block_id=<?= $bid ?>"
               class="block-map-icon btn-light"
               title="Показать на карте">🗺</a>

            <?php if ($is_owner): ?>
                <button class="btn-light" onclick="deleteBlock(<?= $bid ?>)">Удалить блок</button>
            <?php endif; ?>

            <?php if ($is_owner): ?>
                <input type="text"
                       class="block-title"
                       data-block-id="<?= $bid ?>"
                       value="<?= htmlspecialchars($b['title']) ?>"
                       onblur="autosaveBlockTitle(this)">
            <?php else: ?>
                <div class="readonly"><?= htmlspecialchars($b['title']) ?></div>
            <?php endif; ?>

            
        </div>


        <!-- Тело блока -->
        <div class="block-body" id="block-body-<?= $bid ?>">

            <?php if ($is_owner): ?>
                <button class="btn-light mb-2"
                        onclick="doComment(<?= $bid ?>, event)">
                    + Добавить текстовую строку
                </button>

                <button class="btn-light mb-2"
                        onclick="pasteIntoBlock(<?= $bid ?>, event)">
                    Вставить вырезанные строки в блок
                </button>
            <?php endif; ?>

            <div class="basket-block-body sortable-area"
                 data-block-id="<?= $bid ?>"
                 id="sortable-<?= $bid ?>">

                <?php foreach ($points_by_block[$bid] as $p):
                    $pid = (int)$p["id"];

                    if ($p["type"] === "COMMENT") {
                        if ($is_owner)
                            include("include/basket/put_comment_edit.php");
                        else
                            include("include/basket/put_comment_view.php");
                    } else {
                        if ($is_owner)
                            include("include/basket/put_object_edit.php");
                        else
                            include("include/basket/put_object_view.php");
                    }

                endforeach; ?>

            </div>
        </div>

    </div>
<?php endforeach; ?>

</div>


<?php include("include/footer_bs.php"); ?>

<!-- JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script src="/js/basket_v.js?v=13"></script>

</div>
