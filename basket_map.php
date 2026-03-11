<?php
/********************************************************************
 *   КАРТА КОРЗИНЫ / КАРТА БЛОКА
 *   - basket_map.php/{basket_id}          : карта всей поездки
 *   - basket_map.php/{basket_id}?block_id : карта одного блока
 *
 *   Выгрузка:
 *   - на карте всей поездки: скачать точки всей поездки (без разбивки по блокам)
 *   - на карте блока: скачать точки этого блока
 *
 *   Если точек нет: показать "Нечего отображать" и не инициализировать карту.
 ********************************************************************/

include_once('include/config.php');
include_once('include/func.php');
include_once('include/head00.php');

// -------------------------------------------------------------
// Получаем ID корзины и ID блока (если есть)
// -------------------------------------------------------------
if (isset($_pin) && preg_match('/^\/(\d+)$/', $_pin, $m)) {
    $_GET['id'] = $m[1];
}

$basket_id = (int)($_GET['id'] ?? 0);
$block_id  = (int)($_GET['block_id'] ?? 0);

if (!$basket_id) {
    $bootstrap = 1;
    include("include/header_bs.php");
    echo "<div class='container-fluid'>
            <h1 class='travel12'>Карта поездки</h1>
            <p class='travel1'>Ошибка: не указана корзина.</p>
          </div>";
    include("include/footer_bs.php");
    exit;
}

// -------------------------------------------------------------
// Собираем OBJECT ids (точно те, что карта должна показывать)
// -------------------------------------------------------------
$ids = [];

if ($block_id > 0) {

    $qPts = $dbi->query("
        SELECT object_id
        FROM basket_points
        WHERE block_id = {$block_id}
          AND type='OBJECT'
        ORDER BY sort ASC
    ");

    if ($qPts) {
        while ($p = $qPts->fetch_assoc()) {
            $oid = (int)$p['object_id'];
            if ($oid > 0) $ids[$oid] = 1;
        }
        $qPts->free();
    }

} else {

    $qBlocks = $dbi->query("
        SELECT id
        FROM basket_blocks
        WHERE basket_id = {$basket_id}
        ORDER BY sort ASC
    ");

    if ($qBlocks) {
        while ($b = $qBlocks->fetch_assoc()) {
            $bid = (int)$b['id'];

            $qPts = $dbi->query("
                SELECT object_id
                FROM basket_points
                WHERE block_id = {$bid}
                  AND type='OBJECT'
                ORDER BY sort ASC
            ");

            if ($qPts) {
                while ($p = $qPts->fetch_assoc()) {
                    $oid = (int)$p['object_id'];
                    if ($oid > 0) $ids[$oid] = 1; // unique по всей поездке
                }
                $qPts->free();
            }
        }
        $qBlocks->free();
    }
}

$flag_arr = array_keys($ids);
$flagresult = implode(",", $flag_arr);

// -------------------------------------------------------------
// Если точек нет — вывести сообщение и выйти
// -------------------------------------------------------------
if (!$flagresult) {
    $bootstrap = 1;
    include("include/header_bs.php");

    $title_txt = ($block_id > 0) ? "Карта блока" : "Карта поездки";

    echo "<div class='container-fluid'>
            <h1 class='travel12'>{$title_txt}</h1>
            <p class='travel1'>Нечего отображать: в этом наборе нет объектов для карты.</p>";

    if ($block_id > 0) {
        echo "<p class='travel1'><a href='/basket_v.php/{$basket_id}#block-{$block_id}' class='travell5m'>Вернуться к блоку</a></p>";
    } else {
        echo "<p class='travel1'><a href='/basket_v.php/{$basket_id}' class='travell5m'>Вернуться к поездке</a></p>";
    }

    echo "</div>";

    include("include/footer_bs.php");
    exit;
}

// -------------------------------------------------------------
// Настройки карты (как в старом map.php)
// -------------------------------------------------------------
$dispgmap = 4; // оставляем для совместимости
$title = ($block_id > 0) ? "Карта блока" : "Карта поездки";
$metatags = $title;
$banners_shown = "yes";

include_once("include/head01.php"); // подключает JS карты
?>

<div class="container-fluid">

    <h1 class="travel12"><?= htmlspecialchars($title) ?></h1>

    <p class="note-warn">
        <?php if ($block_id > 0): ?>
            <a href="/basket_v.php/<?= (int)$basket_id ?>#block-<?= (int)$block_id ?>" class="travell5m">Вернуться к блоку</a>
        <?php else: ?>
            <a href="/basket_v.php/<?= (int)$basket_id ?>" class="travell5m">Вернуться к поездке</a>
        <?php endif; ?>
    </p>

    <div class="note-block" >
        <?php if ($block_id > 0): ?>
            <strong>Скачать координаты точек блока:</strong>
        <?php else: ?>
            <strong>Скачать координаты точек поездки:</strong>
        <?php endif; ?>
        <a  href="/kml.php?ids=<?= htmlspecialchars($flagresult) ?>&amp;f=wpt">WPT</a>&nbsp;
        <a  href="/kml.php?ids=<?= htmlspecialchars($flagresult) ?>&amp;f=kml">KML</a>&nbsp;
        <a  href="/kml.php?ids=<?= htmlspecialchars($flagresult) ?>&amp;f=gpx">GPX</a>
    </div>

    <table width="100%">
        <tr>
            <td valign="top" width="330">
                <div class="sidebar-ct">
                    <div id="the_side_bar" class="sidebar"></div>
                </div>
            </td>

            <td valign="top" width="100%">

                <div id="gmapv4" class="map-ct"></div>

                <script type="text/javascript">
                    document.addEventListener('DOMContentLoaded', function () {
                        var mapdiv = document.getElementById("gmapv4");
                        initmapv5('/kml.php?ids=<?= $flagresult ?>', mapdiv);
                    });
                </script>

            </td>
        </tr>
    </table>

</div>

<?php include("include/footer_bs.php"); ?>
