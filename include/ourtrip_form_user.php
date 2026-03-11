<?php
/**
 * Универсальная форма участника совместной поездки
 *
 * Режимы:
 *  - self : редактирование своих данных
 *  - add  : добавление участника (организатором)
 *  - edit : редактирование чужого участника (организатором)
 *
 * Ожидаемые переменные:
 *  $form_mode    string  self | add | edit
 *  $user_data    array   данные участника (пусто для add)
 *  $is_organizer bool
 *  $trip_id      int
 */

// -------------------- защита --------------------
if (!isset($form_mode)) {
    return;
}

// -------------------- режимы --------------------
$is_self = ($form_mode === 'self');
$is_add  = ($form_mode === 'add');
$is_edit = ($form_mode === 'edit');

// -------------------- данные (ИМЕНА = как в ourtrip_one.php) --------------------
$nick     = (string)($user_data['nick']     ?? $user_data['name'] ?? '');
$cntad    = (int)($user_data['cntad']        ?? 1);
$cntch    = (int)($user_data['cntch']        ?? 0);
$typeuser = (int)($user_data['typeuser']     ?? -1);
$descrip  = (string)($user_data['descrip']   ?? '');
$uid      = (int)($user_data['uid']          ?? $user_data['id'] ?? 0);

// -------------------- отображение --------------------
$show_username = $is_add;
$show_status   = !$is_self;

// -------------------- подписи --------------------
$submit_text = 'Сохранить';
if ($is_add)  $submit_text = 'Добавить';
if ($is_edit) $submit_text = 'Сохранить изменения';

// -------------------- статусы --------------------
$statuses = [
    1  => 'Организатор',
    0  => 'Участник',
    -1 => 'Желающий',
    -3 => 'Размышляющий',
];
?>

<form method="post"
      action="/ourtrip_one.php?it=<?= (int)$trip_id ?>"
      class="ourtrip-user-form">

    <?php if ($is_self): ?>
        <input type="hidden" name="act" value="self_save">
    <?php else: ?>
        <input type="hidden" name="act" value="admin_save">
    <?php endif; ?>

    <?php if ($is_edit): ?>
        <input type="hidden" name="uid" value="<?= $uid ?>">
        <input type="hidden" name="nick" value="<?= htmlspecialchars($nick, ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>

    <?php if ($is_edit): ?>
        <div class="mb-2 text-muted">
            Редактирование участника:
            <strong><?= htmlspecialchars($nick, ENT_QUOTES, 'UTF-8') ?></strong>
        </div>
    <?php endif; ?>

    <?php if ($show_username): ?>
        <div class="mb-3">
            <label class="form-label fw-bold">Ник участника</label>
            <input type="text"
                   name="nick"
                   class="form-control"
                   value="<?= htmlspecialchars($nick, ENT_QUOTES, 'UTF-8') ?>"
                   required>
        </div>
    <?php endif; ?>

    <!-- ВЗРОСЛЫЕ + ДЕТИ + СТАТУС В ОДНУ СТРОКУ -->
    <div class="mb-3"
         style="display:flex; gap:16px; align-items:flex-end; flex-wrap:wrap;">

        <div>
            <label class="form-label">Взрослые</label>
            <input type="number"
                   name="cntad"
                   value="<?= $cntad ?>"
                   min="0"
                   style="width:70px;">
        </div>

        <div>
            <label class="form-label">Дети</label>
            <input type="number"
                   name="cntch"
                   value="<?= $cntch ?>"
                   min="0"
                   style="width:70px;">
        </div>

        <?php if ($show_status): ?>
            <div>
                <select name="typeuser"
                        class="form-select"
                        style="width:180px;">
                    <?php foreach ($statuses as $k => $v): ?>
                        <option value="<?= $k ?>" <?= ($k === $typeuser ? 'selected' : '') ?>>
                            <?= htmlspecialchars($v, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

    </div>

    <!-- КОММЕНТАРИЙ БЕЗ ЛЕЙБЛА -->
    <div class="mb-3">
        <textarea name="descrip"
                  rows="3"
                  style="width:100%;"
                  placeholder="Комментарий"><?= htmlspecialchars($descrip, ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><?= $submit_text ?></button>
        <?php if (!$is_self): ?>
            <a href="/ourtrip_one.php?it=<?= (int)$trip_id ?>"
               class="btn btn-outline-secondary">Отмена</a>
        <?php endif; ?>
    </div>

</form>
