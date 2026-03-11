<?php
// ourtrip_add.php — создание и редактирование поездки (add/edit)

include_once('include/func_bs.php');
include_once('include/head00.php');

$bootstrap = 1;
include('include/header_bs.php');

$errors = [];

// -------------------------------------------------
// режим
// -------------------------------------------------
$it   = (int)($_GET['it'] ?? $_POST['it'] ?? 0);
$mode = $it ? 'edit' : 'add';

// -------------------------------------------------
// защита: редактировать может только организатор или админ
// -------------------------------------------------
if ($mode === 'edit') {

    $is_admin = false;
    if (!empty($all_rights) && preg_match('/,admin,/', $all_rights)) {
        $is_admin = true;
    }

    $is_organizer = false;
    if (!$is_admin && $forum_user_id) {
        $chk = $dbi->query("
            SELECT 1
            FROM trip_user
            WHERE idtrip = $it
              AND iduser = $forum_user_id
              AND typeuser = 1
            LIMIT 1
        ");
        if ($chk && $chk->num_rows > 0) {
            $is_organizer = true;
        }
    }

    if (!$is_admin && !$is_organizer) {
        echo '<div class="alert alert-danger">У вас нет прав на редактирование этой поездки.</div>';
        include('include/footer_bs.php');
        exit;
    }
}

// -------------------------------------------------
// значения по умолчанию
// -------------------------------------------------
$txtobj     = '';
$datebeg   = '';
$dateend   = '';
$descrip   = '';
$idforumid = null;

// -------------------------------------------------
// загрузка данных при редактировании
// -------------------------------------------------
if ($mode === 'edit' && !isset($_POST['submit'])) {

    $res = $dbi->query("SELECT * FROM ourtrip WHERE id = $it LIMIT 1");
    if (!$res || !$res->num_rows) {
        echo '<div class="alert alert-danger">Поездка не найдена.</div>';
        include('include/footer_bs.php');
        exit;
    }

    $trip = $res->fetch_assoc();

    $txtobj     = $trip['txtobj'];
    $datebeg    = $trip['datebeg'];
    $dateend    = $trip['dateend'];
    $descrip    = $trip['descrip'];
    $idforumid  = $trip['idforum'];
}

// -------------------------------------------------
// обработка формы
// -------------------------------------------------
if (isset($_POST['submit'])) {

    $txtobj     = trim($_POST['txtobj'] ?? '');
    $datebeg    = $_POST['datebeg'] ?? '';
    $dateend    = $_POST['dateend'] ?? '';
    $descrip    = trim($_POST['descrip'] ?? '');
    $idforumid  = (int)($_POST['idforumid'] ?? 0);

    if ($txtobj === '') {
        $errors[] = 'Не указано название поездки';
    }

    if ($datebeg === '') {
        $errors[] = 'Не указана дата начала';
    }

    // нормализация дат
    $cdatebeg = $datebeg ? date('Y-m-d', strtotime($datebeg)) : null;
    $cdateend = $dateend ? date('Y-m-d', strtotime($dateend)) : $cdatebeg;

    // ❗ строгая проверка дат
    if ($cdateend < $cdatebeg) {
        $errors[] = 'Дата окончания не может быть раньше даты начала';
    }

    // форум не обязателен
    if ($idforumid <= 0) {
        $idforumid = null;
    } else {
        $chk = $dbi->query("
            SELECT id FROM ourtrip
            WHERE idforum = $idforumid
            " . ($mode === 'edit' ? "AND id <> $it" : "") . "
            LIMIT 1
        ");
        if ($chk && $chk->num_rows > 0) {
            $errors[] = 'По выбранной теме форума поездка уже существует';
        }
    }

    // -------------------------------------------------
    // сохранение
    // -------------------------------------------------
    if (!$errors) {

        if ($mode === 'add') {

            $stmt = $dbi->prepare("
                INSERT INTO ourtrip
                (datebeg, dateend, txtobj, descrip, idforum)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                'ssssi',
                $cdatebeg,
                $cdateend,
                $txtobj,
                $descrip,
                $idforumid
            );

            if ($stmt->execute()) {

                $new_id = $stmt->insert_id;

                // автор сразу становится организатором
                $dbi->query("
                    INSERT INTO trip_user
                    (idtrip, iduser, name, cntad, cntch, typeuser)
                    VALUES
                    ($new_id, $forum_user_id, '$forum_user_name', 1, 0, 1)
                ");

                header("Location: /ourtrip_one.php?it=$new_id");
                exit;
            }

            $errors[] = 'Ошибка сохранения поездки';

        } else {

            $stmt = $dbi->prepare("
                UPDATE ourtrip
                SET datebeg = ?, dateend = ?, txtobj = ?, descrip = ?, idforum = ?
                WHERE id = ?
            ");
            $stmt->bind_param(
                'ssssii',
                $cdatebeg,
                $cdateend,
                $txtobj,
                $descrip,
                $idforumid,
                $it
            );

            if ($stmt->execute()) {
                header("Location: /ourtrip_one.php?it=$it");
                exit;
            }

            $errors[] = 'Ошибка сохранения изменений';
        }
    }
}
?>

<div class="container my-4" >

<h1 class="mb-4">
    <?= $mode === 'edit' ? 'Редактирование поездки' : 'Создание поездки' ?>
</h1>

<?php if ($errors): ?>
  <div class="alert alert-danger">
    <?= implode('<br>', $errors) ?>
  </div>
<?php endif; ?>

<form method="post" id="addourtrip">

  <?php if ($mode === 'edit'): ?>
    <input type="hidden" name="it" value="<?= $it ?>">
  <?php endif; ?>

  <div class="mb-3">
    <label class="form-label fw-bold">Название поездки *</label>
    <input type="text" name="txtobj" class="form-control"
           value="<?= htmlspecialchars($txtobj, ENT_QUOTES, 'UTF-8') ?>">
  </div>

  <div class="row mb-3">
    <div class="col-md-6">
      <label class="form-label fw-bold">Дата начала *</label>
      <input type="date" name="datebeg" class="form-control"
             value="<?= htmlspecialchars($datebeg, ENT_QUOTES, 'UTF-8') ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label">Дата окончания</label>
      <input type="date" name="dateend" class="form-control"
             value="<?= htmlspecialchars($dateend, ENT_QUOTES, 'UTF-8') ?>">
    </div>
  </div>

  <div class="mb-3">
    <label class="form-label">Описание поездки</label>
    <textarea name="descrip" class="form-control" rows="5"><?= htmlspecialchars($descrip, ENT_QUOTES, 'UTF-8') ?></textarea>
  </div>

  <div class="mb-4">
    <label class="form-label">Тема на форуме (необязательно)</label>
    <select name="idforumid" class="form-select">
      <option value="">— без привязки —</option>
      <?php
      $res = $dbi->query("
        SELECT topic_id, topic_title
        FROM bl_topics
        WHERE forum_id IN (8,20)
        ORDER BY topic_last_post_id DESC
        LIMIT 20
      ");
      while ($r = $res->fetch_assoc()):
      ?>
        <option value="<?= $r['topic_id'] ?>"
          <?= ($idforumid == $r['topic_id'] ? 'selected' : '') ?>>
          <?= htmlspecialchars($r['topic_title'], ENT_QUOTES, 'UTF-8') ?>
        </option>
      <?php endwhile; ?>
    </select>
  </div>

  <button type="submit" name="submit" class="btn btn-main">
    <?= $mode === 'edit' ? 'Сохранить изменения' : 'Создать поездку' ?>
  </button>

</form>
</div>
<style>
    #addourtrip div {
    margin-top: 5px;
    margin-bottom: 5px;
}
</style>

<?php include('include/footer_bs.php'); ?>
