<?php
// ================================================
//  Совместные поездки — список / календарь / FAQ
//  ourtrip.php  (list-режим)
//  Версия 2025
// ================================================

include_once 'include/config.php';
include_once 'include/func.php';
include_once 'include/func_bs.php';
include_once 'include/head00.php';

$bootstrap = 1;
$titleh    = 'Совместные поездки';

// ------------ Параметры запроса ---------------

$it  = (int)($_REQUEST['it']  ?? 0);      // на всякий случай, но здесь не используем
$cy  = (int)($_REQUEST['cy']  ?? 0);
$cm  = (int)($_REQUEST['cm']  ?? 0);
$ddd = mysafestring($_REQUEST['ddd'] ?? '');
$di  = 0;

if ($ddd) {
    $ts = strtotime($ddd);
    if ($ts > 0) {
        $di  = $ts;
        $ddd = date('Y-m-d', $ts);
    } else {
        $ddd = '';
        $di  = 0;
    }
}

include 'include/header_bs.php';

// ----- вспомогательная функция форматирования даты -----
// (для заголовка "Поездки 6 декабря 2025 года")
function at_format_ru_date($ts) {
    if (!$ts) return '';
    $d = strftime('%e %B %Y', $ts); // " 6 декабря 2025"
    return trim($d);
}

// флаг авторизации из head00.php
$is_auth = ($forum_user_id > 0);
?>

<style>
/* Небольшие правки под новый список */
.ourtrip-main h1 {
    margin-top: 0;
}

/* строки списка поездок */
.trip-card-list {
    padding: 6px 0;
    border-bottom: 1px dotted var(--color-border);
}
.trip-card-list:last-child {
    border-bottom: none;
}
.trip-card-list a {
    text-decoration: none;
}
.trip-card-list a:hover {
    text-decoration: underline;
}

/* заголовки разделов внутри страницы */
.ourtrip-section-title {
    font-family: var(--brand-font);
    font-size: 1.2em;
    margin-top: 18px;
    margin-bottom: 6px;
}
</style>

<div class="container-fluid" style="width:100%;">
  <div class="row ourtrip-layout">

    <!-- левая колонка: календарь + кнопка -->
    <aside class="col-md-3 col-sm-4 col-xs-12">

      <?php
      // современный календарь совместных поездок
      // (мы уже вынесли его в отдельный include)
      include 'include/calendar_ourtrip.php';
      ?>

      <div style="margin:10px 0 20px 0;">
        <?php if ($is_auth) { ?>
          <a href="/ourtrip_add.php"
             class="btn-main" style="width:100%; text-align:center;">
             Добавить поездку
          </a>
        <?php } else { ?>
          <a href="/sitemgr.php"
             class="btn-light" style="width:100%; text-align:center;">
             Войти, чтобы добавить поездку
          </a>
        <?php } ?>
      </div>

    </aside>

    <!-- правая колонка: список поездок -->
    <main class="col-md-9 col-sm-8 col-xs-12 ourtrip-main">

      <h1>Совместные поездки</h1>

      <?php if ($is_auth) { ?>
        <div style="color:var(--color-text-light); margin-bottom:10px;">
          Вы вошли как <b><?php echo htmlspecialchars($forum_user_name); ?></b>
        </div>
      <?php } else { ?>
        <div class="note-block" style="margin-bottom:15px;">
          Чтобы добавлять поездки и записываться в них, пожалуйста
          <a href="/sitemgr.php">войдите на сайт</a>.
        </div>
      <?php } ?>


      <?php
      // =====================================================
      // 1. ПОЕЗДКИ КОНКРЕТНОГО ДНЯ (если передан ddd)
      // =====================================================

      if ($ddd && $di) {

          $title_date = at_format_ru_date($di);

          echo '<div class="ourtrip-section-title">Поездки ' . $title_date . '</div>';

          $day_sql = "
            SELECT id, datebeg, dateend, txtobj
            FROM ourtrip
            WHERE datebeg <= '$ddd'
              AND (dateend >= '$ddd' OR dateend IS NULL)
            ORDER BY datebeg ASC
          ";

          if ($res_day = $dbi->query($day_sql)) {

              if ($res_day->num_rows > 0) {
                  while ($row = $res_day->fetch_assoc()) {
                      $id   = (int)$row['id'];
                      $b_ts = strtotime($row['datebeg']);
                      $e_ts = $row['dateend'] ? strtotime($row['dateend']) : $b_ts;

                      $b = date('d.m.Y', $b_ts);
                      $e = date('d.m.Y', $e_ts);
                      $t = htmlspecialchars($row['txtobj']);

                      echo '<div class="trip-card-list">';
                      echo '<a href="/ourtrip_one.php?it=' . $id . '">';
                      if ($b == $e) {
                          echo $b . ' &nbsp; ' . $t;
                      } else {
                          echo $b . ' — ' . $e . ' &nbsp; ' . $t;
                      }
                      echo '</a>';
                      echo '</div>';
                  }
              } else {
                  echo '<div class="note-block" style="margin-bottom:15px;">';
                  echo 'На эту дату поездок не найдено.';
                  echo '</div>';
              }

              $res_day->free();
          }
      } // конец блока "поездки дня"
      ?>


      <?php
      // =====================================================
      // 2. БЛИЖАЙШИЕ ПОЕЗДКИ (все будущие, начиная с сегодня)
      // =====================================================

      echo '<div class="ourtrip-section-title">Ближайшие поездки</div>';

      $future_sql = "
        SELECT id, datebeg, dateend, txtobj
        FROM ourtrip
        WHERE datebeg >= CURDATE()
        ORDER BY datebeg ASC
      ";

      if ($res = $dbi->query($future_sql)) {

          if ($res->num_rows == 0) {
              echo '<div class="note-block" style="background:#f9e4e4; margin-bottom:15px;">';
              echo 'Ни одной поездки не найдено.';
              echo '</div>';
          } else {
              while ($row = $res->fetch_assoc()) {
                  $id   = (int)$row['id'];
                  $b_ts = strtotime($row['datebeg']);
                  $e_ts = $row['dateend'] ? strtotime($row['dateend']) : $b_ts;

                  $b = date('d.m.Y', $b_ts);
                  $e = date('d.m.Y', $e_ts);
                  $t = htmlspecialchars($row['txtobj']);

                  echo '<div class="trip-card-list">';
                  echo '<a href="/ourtrip_one.php?it=' . $id . '">';
                  if ($b == $e) {
                      echo $b . ' &nbsp; ' . $t;
                  } else {
                      echo $b . ' — ' . $e . ' &nbsp; ' . $t;
                  }
                  echo '</a>';
                  echo '</div>';
              }
          }

          $res->free();
      }
      ?>

      <?php
      // =====================================================
      // 3. FAQ по совместным поездкам
      // =====================================================
      include 'include/faq_ourtrip.php';
      ?>

    </main>
  </div><!-- /.row -->
</div><!-- /.container-fluid -->

<?php
include 'include/footer_bs.php';
?>
