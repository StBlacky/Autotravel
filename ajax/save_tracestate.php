<?php
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/auth_ajax.php';

$r = [
    'success' => 0,
    'error'   => ''
];
ini_set('display_errors', 1);
error_reporting(E_ALL);
/* ==========================================================
 * ЧТЕНИЕ ПАРАМЕТРОВ
 * ========================================================== */
$trace   = (int)($_POST['trace']   ?? 0);
$ido     = (int)($_POST['ido']     ?? 0);
$markg   = (int)($_POST['markg']   ?? 0);
$markv   = (int)($_POST['markv']   ?? 0);
$kbeg    = (int)($_POST['kbeg']    ?? 0);
$kend    = (int)($_POST['kend']    ?? 0);
$kmclick = (int)($_POST['kmclick'] ?? 0);
$msg     = trim($_POST['msg']      ?? '');
$nick_in = trim($_POST['nick']     ?? '');

$month = (int)($_POST['date_month'] ?? 0);
$year  = (int)($_POST['date_year']  ?? 0);

/* ==========================================================
 * ПРОВЕРКИ БАЗОВЫЕ
 * ========================================================== */
if ($trace <= 0 || $kmclick < 0) {
    $r['error'] = "Неверные параметры.";
    echo json_encode($r); exit;
}

if ($msg === '') {
    $r['error'] = "Комментарий не может быть пустым.";
    echo json_encode($r); exit;
}

/* админские id как в head00.php */
$_user_is_admin = in_array($forum_user_id, [2,3,4]);

/* ==========================================================
 * НИК
 * ========================================================== */
if ($forum_user_id > 0) {
    $nick = $forum_user_name;
} else {
    if ($nick_in === '' || mb_strlen($nick_in) < 3) {
        $r['error'] = "Укажите имя (не короче 3 символов).";
        echo json_encode($r); exit;
    }
    $nick = $nick_in;
    $ido  = 0; // гости никогда не редактируют
}

/* ==========================================================
 * АНТИСПАМ
 * ========================================================== */
if (bannedlist($msg)) {
    $r['error'] = "Комментарий требует модерации и не может быть сохранён этим способом.";
    echo json_encode($r); exit;
}
if ($forum_user_banned) {
    $r['error'] = "Ваш аккаунт ограничен.";
    echo json_encode($r); exit;
}

/* ==========================================================
 * ВАЛИДАЦИЯ ДАТЫ ПРОЕЗДА
 * ========================================================== */
$cy = (int)date("Y");

if ($month < 1 || $month > 12) {
    $month = (int)date("n");
}
if ($year < $cy - 5 || $year > $cy) {
    $year = $cy;
}

$dat = sprintf("%d.%d", $month, $year);   // финальная дата "м.ГГГГ"

/* ==========================================================
 * ОПРЕДЕЛЯЕМ ГРАНИЦЫ СЕГМЕНТА
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
    while ($row = mysqli_fetch_assoc($res_pts)) {
        $points[] = (int)$row['km_raw'];
    }
    mysqli_free_result($res_pts);
}

if (!$points) {
    $r['error'] = "Нет данных по трассе.";
    echo json_encode($r); exit;
}

$traceMaxKm = end($points);

/* диапазон для начала */
$segStart = $kmclick;
$segEnd   = $traceMaxKm;

foreach ($points as $p) {
    if ($p > $kmclick) {
        $segEnd = $p;
        break;
    }
}

/* клэмп начала */
if ($kbeg < $segStart)        $kbeg = $segStart;
if ($kbeg >= $segEnd)         $kbeg = $segEnd - 1;

/* клэмп конца */
if ($kend <= $kbeg)           $kend = $kbeg + 1;
if ($kend >  $traceMaxKm)     $kend = $traceMaxKm;

/* sanity */
if ($kbeg < 0 || $kend <= $kbeg) {
    $r['error'] = "Некорректные границы участка.";
    echo json_encode($r); exit;
}

/* ==========================================================
 * ПОДГОТОВКА ДАННЫХ
 * ========================================================== */
$tim   = time();
$ip    = $ipp;
$nick  = mysafestring($nick);
$msgdb = mysafehtml2($msg);
$cook_raw = mysafestring($ctext);
$cook = substr($cook_raw, 0, 32);

$markg = (int)$markg;
$markv = (int)$markv;

/* ==========================================================
 * UPDATE — только свои отзывы
 * ========================================================== */
if ($ido > 0 && $forum_user_id > 0) {

    $cond = "ido = {$ido} AND idtr = {$trace} AND nick = '{$nick}'";

    if ($_user_is_admin) {
        // админ может редактировать любые
        $cond = "ido = {$ido} AND idtr = {$trace}";
    }

    $sql = "
      UPDATE otklikt SET
        datea = '{$dat}',
        descrip = '{$msgdb}',
        markv = {$markv},
        markg = {$markg},
        kbeg  = {$kbeg},
        kend  = {$kend},
        ip    = '{$ip}',
        time  = {$tim},
        cook  = '{$cook}'
      WHERE {$cond}
      LIMIT 1
    ";

    $dbi->query($sql);

    if ($dbi->error) {
    echo json_encode(['success'=>0, 'error'=>"SQL UPDATE: ".$dbi->error]);
    exit;
}
    $r['success'] = 1;
    echo json_encode($r); exit;
}

/* ==========================================================
 * INSERT — новый отзыв
 * ========================================================== */
$sql = "
  INSERT INTO otklikt
    (idtr, nick, datea, descrip, markv, markg, kbeg, kend, ip, time, cook)
  VALUES
    ({$trace}, '{$nick}', '{$dat}', '{$msgdb}',
     {$markv}, {$markg}, {$kbeg}, {$kend},
     '{$ip}', {$tim}, '{$cook}')
";



     
$dbi->query($sql);
$new_id = $dbi->insert_id;

if ($dbi->error) {
    echo json_encode(['success'=>0, 'error'=>"SQL INSERT: ".$dbi->error, 'sql'=>$sql]);
    exit;
}

$r['success'] = 1;
$r['ido']     = $new_id;

echo json_encode($r);
