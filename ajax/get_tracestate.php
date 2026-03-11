<?php
header('Content-Type: text/html; charset=UTF-8');
require_once __DIR__ . '/auth_ajax.php';

/* параметры */
$trace = isset($_GET['trace']) ? (int)$_GET['trace'] : 0;
$km    = isset($_GET['km'])    ? (int)$_GET['km']    : 0;

if ($trace <= 0 || $km < 0) {
    echo '<div class="ts-empty">Неверные параметры.</div>';
    exit;
}

/* ==========================================================
 * ГРАНИЦЫ
 * ========================================================== */
$sql_pts = "
  SELECT tr.cw_far AS km_raw
  FROM far_tr tr
  WHERE tr.trace = {$trace}
  ORDER BY tr.cw_far ASC
";
$res_pts = $dbi->query($sql_pts);
$points = [];

if ($res_pts) {
    while ($r = mysqli_fetch_assoc($res_pts)) {
        $points[] = (int)$r['km_raw'];
    }
    mysqli_free_result($res_pts);
}

if (!$points) {
    echo '<div class="ts-empty">Нет данных по этой трассе.</div>';
    exit;
}

$traceMaxKm  = end($points);
$kmCellStart = $km;
$kmCellEnd   = $traceMaxKm;

foreach ($points as $p) {
    if ($p > $km) {
        $kmCellEnd = $p;
        break;
    }
}

/* ==========================================================
 * ОТЗЫВЫ
 * ========================================================== */
$sql = "
  SELECT
    ts.ido AS tsid,
    ts.datea AS dat,
    COALESCE(ts.nick, '') AS nick,
    ts.markg,
    ts.markv,
    COALESCE(t_r.descr, '—') AS road_descr,
    COALESCE(t_s.descr, '—') AS speed_descr,
    TRIM(CONCAT(COALESCE(ts.comment,''), ' ', COALESCE(ts.descrip,''))) AS msg,
    ts.kbeg, ts.kend,
    ts.cook
  FROM otklikt ts
  LEFT JOIN t_road  t_r ON t_r.id = ts.markg
  LEFT JOIN t_speed t_s ON t_s.id = ts.markv
  WHERE ts.idtr = {$trace}
    AND ts.ido >= 0
    AND ts.kbeg <= {$km}
    AND ts.kend >= {$km}
  ORDER BY ts.time DESC
  LIMIT 20
";

$res = $dbi->query($sql);
$items = [];
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) $items[] = $r;
    mysqli_free_result($res);
}

/* ==========================================================
 * СВОЙ ОТЗЫВ
 * ========================================================== */
$my_ido   = 0;
$my_msg   = "";
$my_markg = 0;
$my_markv = 0;
$my_kbeg  = $kmCellStart;
$my_kend  = $kmCellEnd;
$my_date  = ""; // важно!

if ($forum_user_name) {
    foreach ($items as $it) {
        if ($it['nick'] === $forum_user_name) {
            $my_ido   = (int)$it['tsid'];
            $my_msg   = $it['msg'];
            $my_markg = (int)$it['markg'];
            $my_markv = (int)$it['markv'];
            $my_kbeg  = (int)$it['kbeg'];
            $my_kend  = (int)$it['kend'];
            $my_date  = $it['dat'];
            break;
        }
    }
}

/* ==========================================================
 * СПРАВОЧНИКИ
 * ========================================================== */
function car_icon($g){
    switch ((int)$g) {
        case 1: return 'road_car_sedan.svg';
        case 2: return 'road_car_hatch.svg';
        case 3: return 'road_car_sedan.svg';
        case 4: return 'road_blocked.svg';
        default: return 'road_car_sedan.svg';
    }
}
function speed_icon($v){
    switch ((int)$v) {
        case 1: return 'speed20.svg';
        case 2: return 'speed40.svg';
        case 3: return 'speed60.svg';
        case 4: return 'speed90.svg';
        case 5: return 'speed100plus.svg';
        default: return 'speed_none.svg';
    }
}

/* ==========================================================
 * СПРАВОЧНИКИ: t_road / t_speed
 * ========================================================== */

/* --- t_road --- */
$road_opts = [];
$resg = $dbi->query("SELECT id, descr FROM t_road ORDER BY id ASC");

if ($resg) {
    while ($r = mysqli_fetch_assoc($resg)) {
        $road_opts[] = [
            'id'   => (int)$r['id'],
            'name' => $r['descr'],
            'sel'  => ($my_markg == $r['id']) ? "selected" : ""
        ];
    }
    mysqli_free_result($resg);
}

/* --- t_speed --- */
$speed_opts = [];
$resv = $dbi->query("SELECT id, descr FROM t_speed ORDER BY id ASC");

if ($resv) {
    while ($r = mysqli_fetch_assoc($resv)) {
        $speed_opts[] = [
            'id'   => (int)$r['id'],
            'name' => $r['descr'],
            'sel'  => ($my_markv == $r['id']) ? "selected" : ""
        ];
    }
    mysqli_free_result($resv);
}

/* ==========================================================
 * ВЫВОД HTML
 * ========================================================== */
?>
<div class="ts-meta">
    Участок: ~ <?= $km ?> км (от <?= $kmCellStart ?> до <?= $kmCellEnd ?> км)
</div>

<?php if (!$items): ?>

    <div class="ts-empty">Пока нет данных по этому месту.</div>

<?php else: ?>

    <?php foreach ($items as $i): ?>
        <div id="ts-<?= $i['tsid'] ?>" class="ts-item">

            <div class="ts-row-top">
                <span class="ts-date"><?= htmlspecialchars($i['dat']) ?></span>
                <span class="ts-km"><?= $i['kbeg'] ?>–<?= $i['kend'] ?> км</span>
            </div>

            <div class="ts-icons-line">
                <img src="/jpg/ts/<?= car_icon($i['markg']) ?>" class="ts-ico">
                <img src="/jpg/ts/<?= speed_icon($i['markv']) ?>" class="ts-ico">
            </div>

            <div class="ts-comment"><?= htmlspecialchars($i['msg'] ?: 'Без комментария') ?></div>
            <div class="ts-author">Автор: <?= htmlspecialchars($i['nick'] ?: '—') ?></div>

        </div>
    <?php endforeach; ?>

<?php endif; ?>

<hr class="ts-hr">

<?php
$is_guest = ($forum_user_id == 0);

/* ===== дата проезда ===== */
$my_month = 0;
$my_year  = 0;

if ($my_date && preg_match('/^(\d{1,2})\.(\d{4})$/', $my_date, $m)) {
    $my_month = (int)$m[1];
    $my_year  = (int)$m[2];
}

if (!$my_month) $my_month = date('n');
if (!$my_year)  $my_year  = date('Y');
?>


<div class="trace-inline-panel">

    <h4 class="ts-form-title">
        <?php if ($my_ido && !$is_guest): ?>
            Редактировать отзыв
        <?php else: ?>
            Добавить отзыв
        <?php endif; ?>
    </h4>

    <form id="ts-form" onsubmit="return tsSave(this);">

        <!-- скрытые параметры -->
        <input type="hidden" name="trace"   value="<?= $trace ?>">
        <input type="hidden" name="ido"     value="<?= $is_guest ? 0 : $my_ido ?>">
        <input type="hidden" name="kmclick" value="<?= $km ?>">

        <!-- =========================================================
             ДАТА ПРОЕЗДА
        ========================================================= -->
        <div class="ts-form-row ts-row-2col">

            <div class="ts-subrow">
                <label>Месяц:</label>
                <select name="date_month">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>" <?= ($m == $my_month ? 'selected' : '') ?>>
                            <?= $m ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="ts-subrow">
                <label>Год:</label>
                <select name="date_year">
                    <?php $cy = date('Y'); ?>
                    <?php for ($y = $cy - 5; $y <= $cy; $y++): ?>
                        <option value="<?= $y ?>" <?= ($y == $my_year ? 'selected' : '') ?>>
                            <?= $y ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

        </div>

        <!-- =========================================================
             НАЧАЛО / КОНЕЦ УЧАСТКА
        ========================================================= -->
        <div class="ts-form-row ts-row-2col">

            <div class="ts-subrow">
                <label>Начало участка:</label>
                <input type="number"
                       name="kbeg"
                       value="<?= $my_kbeg ?>"
                       min="<?= $kmCellStart ?>"
                       max="<?= $kmCellEnd - 1 ?>">
            </div>

            <div class="ts-subrow">
                <label>Конец:</label>
                <input type="number"
                       name="kend"
                       value="<?= $my_kend ?>"
                       min="<?= $my_kbeg + 1 ?>"
                       max="<?= $traceMaxKm ?>">
            </div>

        </div>

        <div class="ts-info-note">
            Начало — внутри участка <?= $kmCellStart ?>–<?= $kmCellEnd ?>,
            конец — до <?= $traceMaxKm ?> км.
        </div>

        <!-- =========================================================
             ПРОХОДИМОСТЬ / СКОРОСТЬ
        ========================================================= -->
        <div class="ts-form-row ts-row-2col">

            <div class="ts-subrow">
                <label>Проходимость:</label>
                <select name="markg">
                    <?php foreach ($road_opts as $opt): ?>
                        <option value="<?= $opt['id'] ?>" <?= $opt['sel'] ?>>
                            <?= htmlspecialchars($opt['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="ts-subrow">
                <label>Скорость:</label>
                <select name="markv">
                    <?php foreach ($speed_opts as $opt): ?>
                        <option value="<?= $opt['id'] ?>" <?= $opt['sel'] ?>>
                            <?= htmlspecialchars($opt['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

        </div>

        <!-- =========================================================
             КОММЕНТАРИЙ
        ========================================================= -->
        <div class="ts-form-row">
            <label>Комментарий:</label>
            <textarea name="msg" rows="4"><?= htmlspecialchars($my_msg) ?></textarea>
        </div>

        <!-- =========================================================
             ГОСТЬ / АВТОРИЗОВАННЫЙ
        ========================================================= -->
        <?php if ($is_guest): ?>

            <div class="ts-form-row ts-row-2col">
                <div class="ts-subrow">
                    <label>Имя:</label>
                    <input type="text" name="nick" maxlength="40">
                </div>
            </div>

        <?php else: ?>

            <div class="ts-form-row ts-row-2col">
                <div class="ts-subrow">
                    <label>Вы вошли как:</label>
                    <div class="ts-user"><?= htmlspecialchars($forum_user_name) ?></div>
                </div>
            </div>

        <?php endif; ?>

        <!-- =========================================================
             КНОПКА
        ========================================================= -->
        <div class="ts-form-buttons">
            <button type="submit" class="btn-main">
                <?php if ($my_ido && !$is_guest): ?>
                    Сохранить изменения
                <?php else: ?>
                    Добавить отзыв
                <?php endif; ?>
            </button>
        </div>

    </form>

</div>


