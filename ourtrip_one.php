<?php
// ourtrip_one.php — страница одной поездки (хаб).
// 1) Организатор/админ управляет участниками здесь же (без sitemgr.php).
// 2) Зарегистрированный пользователь видит форму "Записаться/редактировать себя" всегда (форма открыта).
// 3) Саморегистрация: ник НЕ вводится (подставляется из $forum_user_name), статус можно менять только между
//    "Желающий" (-1) и "Размышляющий" (-3). Организатор потом меняет на Участник/Организатор.
// 4) При добавлении участника организатором ник ищется в базе bl_users (строго).
//
// ВАЖНО: любые header("Location") — только ДО вывода HTML.

$bootstrap = 1;


include_once('include/func_bs.php');   // тут у тебя bootstrap-обвязка и форумные переменные

include_once('include/head00.php');

// -------------------------------------------------------
// helpers
// -------------------------------------------------------
function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function role_label($typeuser) {
    if ($typeuser == 1) return "Организатор";
    if ($typeuser == 0) return "Участник";
    if ($typeuser == -1) return "Желающий";
    if ($typeuser == -3) return "Размышляющий";
    return (string)$typeuser;
}

function role_badge_class($typeuser) {
    if ($typeuser == 1) return "bg-primary";
    if ($typeuser == 0) return "bg-success";
    if ($typeuser == -1) return "bg-secondary";
    if ($typeuser == -3) return "bg-warning text-dark";
    return "bg-secondary";
}

function is_post() {
    return (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST');
}

// -------------------------------------------------------
// input
// -------------------------------------------------------
$it  = (int)($_REQUEST['it'] ?? 0);
$act = (string)($_REQUEST['act'] ?? '');     // self_save | admin_save | admin_del | admin_form
$uid = (int)($_REQUEST['uid'] ?? 0);         // trip_user.id (для редактирования/удаления)

if ($it <= 0) {
    // тут пока без шаблонов — чтобы точно не было вывода до редиректов
    echo "Поездка не указана.";
    exit;
}

// -------------------------------------------------------
// current user
// -------------------------------------------------------
$forum_uid  = (int)($forum_user_id ?? 0);
$forum_nick = (string)($forum_user_name ?? '');
$all_rights = (string)($all_rights ?? '');
$forumname  = (string)($forumname ?? '');
$sitename   = (string)($sitename ?? '');

// -------------------------------------------------------
// load trip (нужно и для проверок действий)
// -------------------------------------------------------
$sql_trip = "
    SELECT id, txtobj, descrip, datebeg, dateend, idforum
    FROM ourtrip
    WHERE id = $it
    LIMIT 1
";
$res_trip = $dbi->query($sql_trip);
$trip = $res_trip ? mysqli_fetch_assoc($res_trip) : null;

if (!$trip) {
    echo "Поездка не найдена.";
    exit;
}

$idtrip      = (int)$trip['id'];
$titletrip   = (string)$trip['txtobj'];
$tripdescrip = (string)($trip['descrip'] ?? '');
$datebeg     = (string)$trip['datebeg'];
$dateend     = (string)$trip['dateend'];
$idforum     = (int)($trip['idforum'] ?? 0);

$dtbeg = strtotime($datebeg ?: '1970-01-01');
$dtend = strtotime($dateend ?: $datebeg ?: '1970-01-01');

// -------------------------------------------------------
// permissions (typeuser of current in this trip)
// -------------------------------------------------------
$typeuserit = -2; // -2 = не участник
$self_uid   = 0;  // trip_user.id for current user

if ($forum_uid) {
    $sql_my = "SELECT id, typeuser FROM trip_user WHERE idtrip=$it AND iduser=$forum_uid LIMIT 1";
    $res_my = $dbi->query($sql_my);
    if ($res_my && ($r = mysqli_fetch_assoc($res_my))) {
        $self_uid   = (int)$r['id'];
        $typeuserit = (int)$r['typeuser'];
    }
}

$is_admin = false;
if ($typeuserit == 1) $is_admin = true;
if (!$is_admin && $all_rights && preg_match('/\,admin\,/i', $all_rights)) $is_admin = true;

// -------------------------------------------------------
// ACTIONS (до любого вывода HTML!)
// -------------------------------------------------------
$errors = [];
$infos  = [];

// ---------- Self save (зарегистрированный пользователь) ----------
if ($act === 'self_save' && is_post()) {

    if (!$forum_uid) {
        $errors[] = "Для записи в поездку нужно войти.";
    } else {

        $cntad  = (int)($_POST['cntad'] ?? 0);
        $cntch  = (int)($_POST['cntch'] ?? 0);
        $desc   = trim((string)($_POST['descrip'] ?? ''));
        $self_status = (int)($_POST['self_status'] ?? -1);

        if ($cntad < 0) $cntad = 0;
        if ($cntch < 0) $cntch = 0;

        // Пользователь сам может только желающий/размышляющий
        if ($self_status !== -1 && $self_status !== -3) {
            $self_status = -1;
        }

        
        if (!$errors) {

            $desc_sql = $dbi->real_escape_string($desc);
            $nick_sql = $dbi->real_escape_string($forum_nick);

            // если уже есть запись — update, иначе insert
            if ($self_uid > 0) {

                // Нельзя трогать организаторство/участника самим пользователем:
                // если его typeuser сейчас 1 или 0 — мы НЕ меняем typeuser (оставляем как есть).
                $cur_type = $typeuserit;

                if ($cur_type == 1 || $cur_type == 0) {
                    $sql_up = "
                        UPDATE trip_user SET
                            cntad=$cntad,
                            cntch=$cntch,
                            descrip='$desc_sql'
                        WHERE id=$self_uid AND idtrip=$it AND iduser=$forum_uid
                        LIMIT 1
                    ";
                } else {
                    $sql_up = "
                        UPDATE trip_user SET
                            cntad=$cntad,
                            cntch=$cntch,
                            typeuser=$self_status,
                            descrip='$desc_sql'
                        WHERE id=$self_uid AND idtrip=$it AND iduser=$forum_uid
                        LIMIT 1
                    ";
                }

                $dbi->query($sql_up);

            } else {

                // INSERT: всегда из forum user, без ручного ника
                $sql_in = "
                    INSERT INTO trip_user (id, idtrip, iduser, name, cntad, cntch, typeuser, descrip)
                    VALUES (NULL, $it, $forum_uid, '$nick_sql', $cntad, $cntch, $self_status, '$desc_sql')
                ";
                $dbi->query($sql_in);
            }

            // PRG
            header("Location: /ourtrip_one.php?it=$it");
            exit;
        }
    }
}

// ---------- Admin delete ----------
if ($act === 'admin_del' && $is_admin && $uid > 0) {

    $res_chk = $dbi->query("SELECT iduser, typeuser FROM trip_user WHERE idtrip=$it AND id=$uid LIMIT 1");
    $row_chk = $res_chk ? mysqli_fetch_assoc($res_chk) : null;

    if (!$row_chk) {
        $errors[] = "Участник не найден.";
    } else {
        $t = (int)$row_chk['typeuser'];
        if ($t >= 1) {
            $errors[] = "Организатора удалить нельзя.";
        } else {
            $dbi->query("DELETE FROM trip_user WHERE idtrip=$it AND id=$uid AND typeuser < 1");
            header("Location: /ourtrip_one.php?it=$it");
            exit;
        }
    }
}

// ---------- Admin save (add/edit) ----------
$admin_form_mode = ""; // "add" | "edit"
$admin_form = [
    "uid" => 0,
    "nick" => "",
    "cntad" => 1,
    "cntch" => 0,
    "typeuser" => -1,
    "descrip" => "",
];

if ($is_admin && $act === 'admin_form') {

    // просто открыть форму
    if ($uid > 0) {
        $res_u = $dbi->query("SELECT * FROM trip_user WHERE idtrip=$it AND id=$uid LIMIT 1");
        $u = $res_u ? mysqli_fetch_assoc($res_u) : null;
        if ($u) {
            $admin_form_mode = "edit";
            $admin_form["uid"] = (int)$u["id"];
            $admin_form["nick"] = (string)$u["name"];
            $admin_form["cntad"] = (int)$u["cntad"];
            $admin_form["cntch"] = (int)$u["cntch"];
            $admin_form["typeuser"] = (int)$u["typeuser"];
            $admin_form["descrip"] = (string)($u["descrip"] ?? "");
        } else {
            $errors[] = "Участник не найден.";
            $admin_form_mode = "add";
        }
    } else {
        $admin_form_mode = "add";
    }
}

if ($act === 'admin_save' && $is_admin && is_post()) {

    $uid_post = (int)($_POST['uid'] ?? 0);
    $nick     = trim((string)($_POST['nick'] ?? ''));
    $cntad    = (int)($_POST['cntad'] ?? 0);
    $cntch    = (int)($_POST['cntch'] ?? 0);
    $typeu    = (int)($_POST['typeuser'] ?? -1);
    $desc     = trim((string)($_POST['descrip'] ?? ''));

    if ($cntad < 0) $cntad = 0;
    if ($cntch < 0) $cntch = 0;

    // допустимые роли
    $allowed_roles = [1, 0, -1, -3];
    if (!in_array($typeu, $allowed_roles, true)) $typeu = -1;

    if ($nick === '') $errors[] = "Ник не указан.";

    // поиск пользователя форума по нику
    $iduser = 0;
    if (!$errors) {
        $nick_sql = $dbi->real_escape_string($nick);
        $res_fu = $dbi->query("SELECT user_id, username FROM bl_users WHERE username='$nick_sql' LIMIT 1");
        $fu = $res_fu ? mysqli_fetch_assoc($res_fu) : null;
        if (!$fu) {
            $errors[] = "Пользователь форума с таким ником не найден.";
        } else {
            $iduser = (int)$fu['user_id'];
            $nick  = (string)$fu['username']; // нормализованный ник
        }
    }

    // защита: организатор не может понизить сам себя и не может заменить себя на другого
    if (!$errors && $uid_post > 0) {
        $res_old = $dbi->query("SELECT iduser, typeuser, name FROM trip_user WHERE idtrip=$it AND id=$uid_post LIMIT 1");
        $old = $res_old ? mysqli_fetch_assoc($res_old) : null;
        if ($old) {
            $old_iduser = (int)$old['iduser'];
            $old_type   = (int)$old['typeuser'];
            $old_name   = (string)$old['name'];

            if ($forum_uid && $old_iduser == $forum_uid && $old_type == 1) {
                $typeu = 1;
                $iduser = $old_iduser;
                $nick   = $old_name;
            }
        }
    }

    if (!$errors) {

        $desc_sql = $dbi->real_escape_string($desc);
        $nick_sql = $dbi->real_escape_string($nick);

        if ($uid_post == 0) {

            // не добавлять дубль по iduser
            $res_dupe = $dbi->query("SELECT id FROM trip_user WHERE idtrip=$it AND iduser=$iduser LIMIT 1");
            if ($res_dupe && $res_dupe->num_rows > 0) {
                $errors[] = "Этот пользователь уже добавлен в поездку.";
            } else {

                $sql_in = "
                    INSERT INTO trip_user (id, idtrip, iduser, name, cntad, cntch, typeuser, descrip)
                    VALUES (NULL, $it, $iduser, '$nick_sql', $cntad, $cntch, $typeu, '$desc_sql')
                ";
                $dbi->query($sql_in);

                header("Location: /ourtrip_one.php?it=$it");
                exit;
            }

        } else {

            $sql_up = "
                UPDATE trip_user SET
                    iduser=$iduser,
                    name='$nick_sql',
                    cntad=$cntad,
                    cntch=$cntch,
                    typeuser=$typeu,
                    descrip='$desc_sql'
                WHERE idtrip=$it AND id=$uid_post
                LIMIT 1
            ";
            $dbi->query($sql_up);

            header("Location: /ourtrip_one.php?it=$it");
            exit;
        }
    }

    // если ошибки — форма должна остаться открытой
    $admin_form_mode = ($uid_post > 0) ? "edit" : "add";
    $admin_form["uid"] = $uid_post;
    $admin_form["nick"] = $nick;
    $admin_form["cntad"] = $cntad;
    $admin_form["cntch"] = $cntch;
    $admin_form["typeuser"] = $typeu;
    $admin_form["descrip"] = $desc;
}

// -------------------------------------------------------
// load participants (после действий)
// -------------------------------------------------------
$participants = [];
$res_u = $dbi->query("
    SELECT id, iduser, name, cntad, cntch, typeuser, descrip
    FROM trip_user
    WHERE idtrip=$it
    ORDER BY typeuser DESC, name ASC
");
if ($res_u) {
    while ($row = mysqli_fetch_assoc($res_u)) {
        $participants[] = $row;
    }
}

// заново вычислим self_uid/self_type (после изменений это уже актуально)
$typeuserit = -2;
$self_uid = 0;
$self_row = null;

if ($forum_uid) {
    foreach ($participants as $p) {
        if ((int)$p['iduser'] === $forum_uid) {
            $self_uid = (int)$p['id'];
            $typeuserit = (int)$p['typeuser'];
            $self_row = $p;
            break;
        }
    }
}

// -------------------------------------------------------
// Теперь можно выводить HTML
// -------------------------------------------------------
include('include/header_bs.php');

// FAQ блок (свернутый/раскрываемый ты уже делала в include/faq_ourtrip.php)
ob_start();
@include("include/faq_ourtrip.php");
$faq_block = ob_get_clean();

?>
<div class="container-fluid" >

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
        <div>
            <h1 class="mb-1">Совместные поездки</h1>
            <h2 class="mb-0"><?=h($titletrip)?></h2>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-outline-secondary btn-sm" href="/ourtrip.php">← К списку</a>

            <?php if ($is_admin) { ?>
                <a class="btn btn-outline-primary btn-sm" href="/ourtrip_add.php?it=<?=$it?>">Редактировать поездку</a>
                <a class="btn btn-primary btn-sm" href="/ourtrip_one.php?it=<?=$it?>&act=admin_form">Добавить участника</a>
            <?php } ?>
        </div>
    </div>

    <?php if ($errors) { ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $e) { ?><li><?=h($e)?></li><?php } ?>
            </ul>
        </div>
    <?php } ?>

    <div class="row">

        <!-- LEFT -->
        <div class="col-md-6 mb-4">

            <table class="table table-sm table-borderless mb-3">
                <tr>
                    <td class="text-end fw-bold" style="width:160px;">Начало:</td>
                    <td><?=h(strftime('%d %B %Y', $dtbeg))?></td>
                </tr>
                <tr>
                    <td class="text-end fw-bold">Конец:</td>
                    <td><?=h(strftime('%d %B %Y', $dtend))?></td>
                </tr>
                <?php if (trim($tripdescrip) !== "") { ?>
                    <tr>
                        <td class="text-end fw-bold align-top">Описание:</td>
                        <td><?=nl2br(h($tripdescrip))?></td>
                    </tr>
                <?php } ?>
            </table>

            <?php if ($idforum > 0) { ?>
                <div class="mb-3">
                    <a class="btn btn-outline-success btn-sm" target="_blank"
                       href="<?=h($forumname)?>/viewtopic.php?t=<?=$idforum?>">
                        Обсуждение на форуме
                    </a>
                </div>
            <?php } else { ?>
                <div class="mb-3 text-muted">Тема форума не привязана.</div>
            <?php } ?>

            <!-- SELF FORM (всегда открыта для вошедшего) -->
            <div class="card mb-3">
    <div class="card-body">

        <?php if (!$forum_uid) { ?>

            <div class="alert alert-info mb-0">
                Чтобы записаться в поездку, нужно <a href="/sitemgr.php">войти</a>.
            </div>

        <?php } else { ?>

            <?php
            // -------- состояние участия --------
            if ($self_uid > 0) {
                // пользователь уже записан
                $form_mode = 'self';
                $user_data = $self_row ?: [];
            } else {
                // пользователь не записан
                $form_mode = 'self';
                $user_data = [];
                echo '<div class="mb-2 text-muted">Вы пока не записаны в поездку.</div>';
            }

            $trip_id = $it;
            $is_organizer = $is_admin;

            include('include/ourtrip_form_user.php');
            ?>

        <?php } ?>

    </div>
</div>

           

            <!-- FAQ -->
            <div class="mb-3">
                <?=$faq_block?>
            </div>

        </div>

        <!-- RIGHT -->
        <div class="col-md-6 mb-4">

          

<div class="table-responsive">
<table class="table table-bordered table-sm align-middle ourtrip-users-table">
    <thead>
    <tr>
        <th>Участник</th>
        <th style="width:80px;">Состав</th>
        <th style="width:140px;">Статус</th>
        <th>Комментарий</th>
        <?php if ($is_admin): ?>
            <th style="width:80px;"></th>
        <?php endif; ?>
    </tr>
    </thead>

    <tbody>
    <?php
    $totalad = 0;
    $totalch = 0;

    foreach ($participants as $p) {
        $tuid   = (int)$p['id'];
        $iduser = (int)$p['iduser'];
        $name   = (string)$p['name'];
        $cntad  = (int)$p['cntad'];
        $cntch  = (int)$p['cntch'];
        $typeu  = (int)$p['typeuser'];
        $udesc  = (string)($p['descrip'] ?? '');

        // считаем всех, кроме "желающих"
        if ($typeu != -1) {
            $totalad += $cntad;
            $totalch += $cntch;
        }

        $name_html = h($name);
        if ($iduser > 0 && $forumname) {
            $name_html = "<a target='_blank' href='".h($forumname)."/memberlist.php?mode=viewprofile&u=$iduser'>$name_html</a>";
        }

        // статус текстом, организатор — жирным
        if ($typeu == 1) {
            $status_html = '<strong>Организатор</strong>';
        } else {
            $status_html = h(role_label($typeu));
        }

        echo "<tr>";
        echo "<td>$name_html</td>";
        echo "<td>{$cntad}+{$cntch}</td>";
        echo "<td>$status_html</td>";
        echo "<td>".($udesc !== '' ? nl2br(h($udesc)) : '&nbsp;')."</td>";

        if ($is_admin) {
            echo "<td class='text-nowrap'>";
            echo "<a href='/ourtrip_one.php?it=$it&act=admin_form&uid=$tuid' title='Редактировать'>✏️</a> ";

            // нельзя удалить только СЕБЯ как организатора
            if (!($typeu == 1 && $iduser == $forum_uid)) {
                echo "<a href='/ourtrip_one.php?it=$it&act=admin_del&uid=$tuid'
                         onclick='return confirm(\"Удалить участника?\");'
                         title='Удалить'>🗑️</a>";
            }

            echo "</td>";
        }

        echo "</tr>";
    }

    echo "<tr class='fw-bold'>";
    echo "<td>Итого</td>";
    echo "<td>{$totalad}+{$totalch}</td>";
    echo "<td colspan='".($is_admin ? 3 : 2)."'></td>";
    echo "</tr>";
    ?>
    </tbody>
</table>


</div>



            <?php if ($is_admin && $admin_form_mode !== "") { ?>

               

<?php
$form_mode = ($admin_form_mode === 'edit') ? 'edit' : 'add';
$user_data = $admin_form;
$trip_id = $it;
$is_organizer = true;?>
<div class="edit-user-box">
 <?php include('include/ourtrip_form_user.php');?>
</div>


          <?php   } ?>

        </div>

    </div>

</div>
<style>
    /* ===== Таблица участников ===== */

.table-users {
    width: 100%;
    table-layout: fixed;
}

.table-users th,
.table-users td {
    vertical-align: top;
    word-wrap: break-word;
}

.table-users td:nth-child(2) {
    text-align: center;
    white-space: nowrap;
}

/* ===== Иконки ===== */

.icon-btn {
    text-decoration: none;
    font-size: 16px;
    margin-right: 6px;
}

.icon-btn.danger {
    opacity: 0.7;
}

.icon-btn.danger:hover {
    opacity: 1;
}

/* ===== Режим редактирования ===== */

.edit-user-box {
    margin-top: 24px;
    padding: 16px;
    background: #f7f7f7;
    border: 1px solid #ddd;
}

.edit-user-box strong {
    color: #333;
}

/* ===== Моё участие — тихий блок ===== */

.ourtrip-user-form {
    background: #fafafa;
    padding: 12px;
    border: 1px solid #e0e0e0;
}

</style>

<?php include('include/footer_bs.php'); ?>
