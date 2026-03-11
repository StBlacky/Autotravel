<?php
include_once('include/config.php');
include_once('include/func.php');
include_once('include/func_bs.php');
include_once('include/head00.php');


$flagresult = "";
$action = $_REQUEST["a"];
$subaction = $_REQUEST["f"];
$id = (int) $_REQUEST["id"];
$oldid = $id;
$copy = (int) $_REQUEST["copy"];

if (($id) and ($action == "default")) {
  $sql = "select id,comment,sum from basket where id='$id' and userid='$forum_user_id'";
  $id = "";
  $result = $dbi->query($sql);
  if ($result && ($myrow = $result->fetch_array())) {
    $id = $myrow["id"];
    $comment = $myrow["id"]; // сохранено как в оригинале
    $sum = $myrow["id"];     // сохранено как в оригинале
  }
  if ($result) { $result->free(); }
  if ($id) {
    $forum_user_basket = $id;
    $forum_user_basket_count = $sum;
    $forum_user_basket_name = $comment;
    $sql = "update bl_users set user_basket='$id' where user_id='$forum_user_id'";
    $dbi->query($sql);
    $id = "";
  }
}

if (($action == "new") and ($forum_user_id)) {
  $id = "";
  $sql = "insert into basket (userid,comment,descrip,sum) values('$forum_user_id','','',0)";
  $dbi->query($sql);
  $forum_user_basket = $dbi->insert_id;
  $forum_user_basket_count = 0;
  $forum_user_basket_name = "N " . $forum_user_basket;
  $b_content = "";
  $b_count   = 0;

  if ($copy) {
    $sql = "select * from basket where id='$copy'";
    $result = $dbi->query($sql);
    if ($result && ($myrow = $result->fetch_array())) {
      $b_content = $myrow["descrip"];
      $b_count   = $myrow["sum"];
    }
    if ($result) { $result->free(); }
    $id = $forum_user_basket;
  }

  $sql = "update bl_users set user_basket='$forum_user_basket' where user_id='$forum_user_id'";
  $dbi->query($sql);

  if (($copy) and ($forum_user_basket > 0)) {
    $sql = "update basket set comment='$forum_user_basket_name', descrip='$b_content', sum=$b_count 
            where id=$forum_user_basket and userid=$forum_user_id";
    $dbi->query($sql);
  }
}

if ($action == "update") {
  if ($id) {
    $sql = "select * from basket where id='$id' and userid='$forum_user_id'";
    $result = $dbi->query($sql);
    $id = "";
    if ($result && ($myrow = $result->fetch_array())) {
      $b_user    = $myrow["userid"];
      $id        = $myrow["id"];
      $b_descr   = $myrow["comment"];
      $b_content = $myrow["descrip"];
      $b_count   = $myrow["sum"];
    }
    if ($result) { $result->free(); }
  }

  if ($id) {
    $trs = explode('#', $b_content);
    $trn = array();

    for ($ii = 0; $ii < sizeof($trs); $ii++) {
      $trc   = $trs[$ii];
      $trcnew = "";

      // это одна ячейка
      $tri  = explode('|', $trc);
      $type = $tri[0]; // устарело

      $obj   = "";
      $objid = "";

      // передали из формы
      if (preg_match('/^(\w)(\d+)$/', $_REQUEST["obj" . $ii], $tmp)) {
        $obj   = $tmp[1];
        $objid = $tmp[2];
      }

      // есть дополнительные сохраненные в другой сессии
      if (!($objid)) {
        if (preg_match('/^(\w)(\d+)$/', $tri[1], $tmp)) {
          $obj   = $tmp[1];
          $objid = $tmp[2];
        }
      }

      if ($objid) {
        $conew = $_REQUEST["co" . $ii];
        $conew = str_replace("<BR>", "[br]", $conew);
        $conew = str_replace("~", " ", $conew);
        $conew = str_replace("|", " ", $conew);

        if ($obj == "c") {
          $objid = sizeof($trn) + 1;
        }

        if ($_REQUEST["tr" . $ii] != "del") {
          $trcnew = "|" . $obj . $objid . "|" . "~" . mysafehtml2($conew) . "~" . "|";
        }
      }

      if ($trcnew) {
        array_push($trn, $trcnew);
      }
    }

    if ($subaction == "line") {
      array_push($trn, "|c" . (sizeof($trn) + 1) . "|" . "~" . "~" . "|");
    }

    $n_content = implode('#', $trn);
    $conew     = $_REQUEST["comment"];
    $conew     = str_replace("<BR>", "[br]", $conew);
    $n_descr   = mysafehtml2($conew);
    $n_count   = count($trn);

    $sql = "update basket set comment='$n_descr', descrip='$n_content', sum=$n_count where id=$id";
    $dbi->query($sql);
  }
}

if (($action == "update") && ($subaction == "delete")) {
  $sql = "delete from basket where userid='$forum_user_id' and id='$id' and id_rep is NULL";
  $dbi->query($sql);
  $id = "";
}

if (preg_match("/^\//", $_pin, $exa)) {
  if (preg_match("/^\/(\d+)$/", $_pin, $exa)) {
    $id = $exa[1];
  }
}



if ($id) {
  $sql = "select * from basket where id='$id'";
  $result = $dbi->query($sql);
  $id = "";
  if ($result && ($myrow = $result->fetch_array())) {
    $b_user    = $myrow["userid"];
    $id        = $myrow["id"];
    $b_descr   = $myrow["comment"];
    $b_content = $myrow["descrip"];
    $b_count   = $myrow["sum"];
  }
  if ($result) { $result->free(); }
} else {
  // выбор для редактирования или создание новой
  $sql = "select * from basket where userid='$forum_user_id' order by id desc";
  $result = $dbi->query($sql);
  $id     = "";
  $count  = 0;
  $optlist  = "";
  $optlist2 = "";

  if ($result) {
    while ($myrow = $result->fetch_array()) {
      $b_user    = $myrow["userid"];
      $b_id      = $myrow["id"];
      $b_descr   = $myrow["comment"];
      $b_content = $myrow["descrip"];
      $b_count   = $myrow["sum"];
      $count++;

      if (!($b_descr)) {
        $x_descr = "N " . $b_id;
      } else {
        $x_descr = $b_descr;
      }

      if ($count == 1) {
        $id = $b_id;
        $optlist .= "<option selected value=$b_id>$x_descr\n";
      } else {
        $optlist .= "<option value=$b_id>$x_descr\n";
      }

      if ($b_id == $forum_user_basket) {
        $optlist2 .= "<option selected value=$b_id>$x_descr\n";
      } else {
        $optlist2 .= "<option value=$b_id>$x_descr\n";
      }
    }
    $result->free();
  }

// -------------------------------
// Копирование поездки
// -------------------------------
if ($subaction === "copyall" && $id) {
    header("Location: /basket_copy.php/" . $oldid);
    exit;
}

  $id = "";
  include("include/inclmgrmenu.php");

  echo "<h1 class=travel12>Управление поездками</h1>\n";

  if ($count != 1) {
    echo "<p class=travel1>Выбрать текущую поездку (для сбора объектов сайта):<br>\n<form method=post action=/basket_v.php>\n";
    echo "<select size=1 name=id>\n";
    echo $optlist2 . "</select>\n";
    echo "<input type=hidden name=a value=default>\n";
    echo "<input type=submit name=submitt value=\"Выбрать\">\n";
    echo "</form></p>\n";
  }

  echo "<p class=travel1><a class=travell5m href='/basket_v.php?a=new'>Создать поездку</a></p>\n";

  if ($count != 1) {
    echo "<p class=travel1>Редактировать:<br>\n<form method=post action=/basket_v.php>\n";
    echo "<select size=1 name=id>" . $optlist . "</select>\n";
    echo "<input type=submit name=submitt value=\"Редактировать\">\n";
    echo "</form></p>\n";
  } else {
    echo "<p class=travel1><a class=travell5m href='/basket_v.php/$b_id'>Редактировать $b_descr</a></p>\n";
  }

  $id = "";
}


// ----------------------------------------------------------
// Основной экран корзины (просмотр / редактирование)
// ----------------------------------------------------------
if ($b_user === $forum_user_id) {
  $basketmode = 1;
} else {
  $basketmode = 0;
}
if ($_REQUEST["m"] == "view") {
  $basketmode = 0;
}
if ($subaction == "view") {
  $basketmode = 0;
}

if ($id) {

  $tlist      = "";

  /******************************************************************
   *   НОВАЯ МОДЕЛЬ: получаем точки из basket_blocks / basket_points
   *   Используется для построения маршрута ($flagresult), дальше
   *   фронт пока живёт на $b_content.
   ******************************************************************/

  $flagresult = "";
  $flag_arr   = array();

  $resBlocks = $dbi->query("
      SELECT id 
      FROM basket_blocks 
      WHERE basket_id = $id
      ORDER BY sort ASC
  ");

  if ($resBlocks) {
      while ($b = $resBlocks->fetch_assoc()) {
          $block_id = (int)$b['id'];

          $resPoints = $dbi->query("
              SELECT object_id
              FROM basket_points
              WHERE block_id = $block_id AND type='OBJECT'
              ORDER BY sort ASC
          ");

          if ($resPoints) {
              while ($p = $resPoints->fetch_assoc()) {
                  $flag_arr[] = (int)$p['object_id'];
              }
              $resPoints->free();
          }
      }
      $resBlocks->free();
  }

  if (count($flag_arr)) {
      $flagresult = implode(",", $flag_arr);
  }
  /* ============================================================
 *  ОБРАБОТКА ДЕЙСТВИЙ ПОСЛЕ ТОГО, КАК МЫ УЖЕ СОБРАЛИ $flagresult
 *  — теперь карта, KML, GPX и копирование работают корректно
 * ============================================================ */

// -------------------------------
// Показать на карте (как в старом коде)
// -------------------------------
if ($subaction === "showmap" && $id) {
    header("Location: /basket_map.php/" . $id);
    exit;
}

// -------------------------------
// Экспорт KML
// -------------------------------
if ($subaction === "kml" && $flagresult) {
    header("Location: /kml.php?ids=" . $flagresult);
    exit;
}

// -------------------------------
// Экспорт GPX
// -------------------------------
if ($subaction === "gpx" && $flagresult) {
    header("Location: /kml.php?ids=" . $flagresult . "&fmt=gpx");
    exit;
}

// -------------------------------
// Копирование (как в оригинале)
// -------------------------------
if ($subaction === "copyall" && $id) {
    header("Location: /basket_copy.php?id=" . $id);
    exit;
}




  if ($b_descr) {
    $title = $b_descr;
  } else {
    $title = "";
  }
  $resheader = $title;

  $colorbox  = 1;
  include_once('include/pf/HTMLPurifier.standalone.php');

  $_p_config = HTMLPurifier_Config::createDefault();
  $purifier  = new HTMLPurifier($_p_config);

  $bootstrap = 1;
  //$footable = 1;
  include('include/header_bs.php');
  echo "<div class='container-fluid'>";

  echo "<script type=\"text/javascript\" language=\"JavaScript\" src=\"/js/basketv3.js\"></script>\n";
  // echo "<!-- $b_content -->\n";
  $jsedit = "";


/******************************************************************
 *   НОВАЯ ШАПКА ДЛЯ КОРЗИНЫ (basket_v.php)
 *   — хлебные крошки
 *   — редактируемый заголовок
 *   — возвращение к редактированию (если нужно)
 ******************************************************************/

  // редактирование заголовка — тот же мини-редактор, что в комментариях
  if ($basketmode) {
      $jsedit = " onclick=\"atedithtml('headval')\" onblur=\"atblur('headval')\" contentEditable=\"false\" ";
  } else {
      $jsedit = "";
  }

  // --------- ХЛЕБНЫЕ КРОШКИ ---------------------------------------
  echo "<nav class=\"basket-breadcrumbs\">";
  echo   "<a href=\"/basket_v.php\">Мои поездки</a>";
  echo   "<span>/</span>";
  echo   "<span>".($title ?: "Новая поездка")."</span>";
  echo "</nav>";


  // --------- ЗАГОЛОВОК ПОЕЗДКИ -----------------------------------
  echo "<div class=\"basket-title-block\">";

  echo   "<div id=\"headval\" $jsedit class=\"basket-title\">";
  if ($title) {
      echo $title;
  } else {
      if ($basketmode) echo "{Щёлкните, чтобы изменить название}";
  }
  echo   "</div>";

  if ($basketmode) {
      echo "<div id=\"edheadval\" class=\"basket-edit-toolbar\"></div>";
  }

  // --------- ВЕРНУТЬСЯ К РЕДАКТИРОВАНИЮ ---------------------------
  if (!$basketmode && ($forum_user_id == $b_user)) {
      echo "<div class=\"basket-return-link\">";
      echo   "<a href=\"/basket_v.php/$id\">Вернуться к редактированию</a>";
      echo "</div>";
  }

  echo "</div>"; // basket-title-block


  // Форма – одна для всех действий (редактирование, экспорт, карта и т.п.)
  echo "<form name=\"basketform\" action=\"/basket_v.php\" method=\"POST\">\n";

  echo "<table id=\"baskettable\" width=100% cellpadding=1 cellspacing=0>\n";
  echo "<tbody class=travell12>\n";

  if ($basketmode) {
    // без шапки — только небольшой отступ перед первой карточкой
    echo "<tr class=\"basket-header-spacer\"><td colspan=\"4\" style=\"height:8px\"></td></tr>\n";
  } else {
    // шапка в режиме просмотра
    echo "<tr>\n";
    echo "<td class='travell5c travell01y'>\n";
    echo "<a class=travell5n onclick=\"AllToggle('baskettable',this);\">Информация, фотографии сайта <img border=0 alt=\">\" src=\"/jpg/plus.gif\"></a>\n";
    echo "</td>\n";
    echo "</tr>\n";
  }

  $trs = explode('#', $b_content);

  for ($ii = 0; $ii < sizeof($trs); $ii++) {

    $trc = $trs[$ii];
    // это одна ячейка
    $tri = explode('|', $trc);
    $disp_obj = "";
    $disp_cmt = "";
    $disp_hr  = 0;

    if (preg_match('/^\~(.*)\~$/', $tri[2], $tmp)) {
      // комментарий
      $disp_cmt .= $tmp[1];
      if ($disp_cmt) {
        $disp_hr = 1;
      }
    }

    $obj   = "";
    $count = "";
    $objid = "";

    if (preg_match('/^(\w)(\d+)$/', $tri[1], $tmp)) {
      // отдельный объект
      $obj    = $tmp[1];
      $objid  = $tmp[2];
      $eobjid = $obj . $objid;

      // выводим объект
      if ($obj == "e") {
        if ($basketmode) {
          $disp_obj .= display_basket_obj($objid, "W", $b_user, $disp_hr);
        } else {
          $disp_obj .= display_basket_obj($objid, "V", $b_user, $disp_hr);
        }
      }

      // строка-комментарий (без объекта)
      if ($obj == "c") {
        if ($basketmode) {
          $disp_obj .= "<p class=travell0><i>Эта строка предназначена для комментария, не связанного с объектом</i></p>\n";
        } else {
          $disp_obj = "&nbsp;"; // обязательно хоть что-то !
        }
      }

      // ВАЖНО: старый код "для карты" здесь удалён.
      // Теперь маршрут для карты берётся только из basket_blocks / basket_points.
    }

    if ($disp_obj) {

      if ($basketmode) {

          // jsedit ПРАВИЛЬНЫЙ: уникальный для каждой строки
          $jsedit = " onclick=\"atedithtml('c$ii')\" onblur=\"atblur('c$ii')\" contentEditable=\"false\" ";

          echo "<tr class=\"basket-row\" data-row=\"$ii\">
                  <td colspan=\"4\" class=\"basket-cell\">
                    <div class=\"basket-item\">";

          /* ---------- ВЕРХНИЙ БЛОК (ОБЪЕКТ + КНОПКИ) ---------- */

          echo "<div class=\"basket-item__top\">";

          // объект
          echo   "<div class=\"basket-item__main\" id=\"b$ii\">";
          echo       $disp_obj;
          echo   "</div>";

          // блок кнопок
          echo   "<div class=\"basket-item__controls\">";

          // ↑ ↓
          echo     "<div class=\"basket-item__move\">
                      <input type=\"button\" id=\"vv$ii\" class=\"atc btn-move\" onclick=\"basketup('$ii')\" value=\"↑\">
                      <input type=\"button\" id=\"vn$ii\" class=\"atc btn-move\" onclick=\"basketdown('$ii')\" value=\"↓\">
                    </div>";

          // вырезать/вставить
          echo     "<div class=\"basket-item__cut\">
                      <input type=\"button\" id=\"d$ii\" class=\"atc btn-cut\" onclick=\"basketdel('$ii',this)\" value=\"Вырезать\">
                      <div id=\"dx$ii\"></div>
                    </div>";

          echo   "</div>"; // controls

          // служебные поля
          echo   "<input type=\"hidden\" id=\"obj$ii\" name=\"obj$ii\" value=\"$eobjid\">
                  <input type=\"hidden\" id=\"x$ii\" name=\"tr$ii\" value=\"ok\">";

          echo "</div>"; // basket-item__top


          /* ---------- КОММЕНТАРИЙ ---------- */

          echo "<div class=\"basket-item__comment-block\">";

          // ——— СТРОКА КОММЕНТАРИЯ (УНИКАЛЬНЫЙ ID!) ———
          echo   "<div id=\"c$ii\" $jsedit class=\"basket-comment t12 tl travell0t\">";
          echo       ($disp_cmt ? antihtml2($disp_cmt) : "{Щёлкните, чтобы изменить комментарий}");
          echo   "</div>";

          echo   "<div id=\"edc$ii\" class=\"basket-edit-toolbar\"></div>";
          echo   "<textarea name=\"co$ii\" id=\"oc$ii\" style=\"display:none;\">".$disp_cmt."</textarea>";

          echo "</div>"; // comment-block

          echo "</div>"; // basket-item
          echo "</td></tr>";

      } else {

          // ======= РЕЖИМ ПРОСМОТРА — почти без изменений =======
          echo "<tr id=\"b$ii\"><td>";
          echo $disp_obj;
          if ($disp_cmt) {
              $view_cmt = fotoreplace(antihtml2($disp_cmt));
              echo "<div class=\"t12 tl travell0t\">$view_cmt</div>";
          }
          echo "</td></tr>";
      }
    }
  }

  echo "</tbody></table>\n";
?>

  <!-- =============================================== -->
  <!-- ЕДИНЫЙ ПОДВАЛ КОРЗИНЫ (просмотр + редактирование) -->
  <!-- =============================================== -->

  <div class="card-block mt-2">

      <?php if ($basketmode) { ?>
          <!-- предупреждение -->
          <div id="mgrmenu" class="note-block note-warn mb-2">
              Изменения будут потеряны, если не нажать одну из кнопок.
          </div>
      <?php } ?>

      <!-- скрытые поля для всех действий (в т.ч. показать на карте / экспорт) -->
      <input type="hidden" name="id" value="<?php echo $id; ?>">
      <input type="hidden" name="comment" id="oheadval" value="<?php echo $b_descr; ?>">
      <input type="hidden" name="a" value="update">
      <input type="hidden" name="f" value="">

      <?php if ($basketmode) { ?>
          <!-- кнопки сохранения -->
          <div class="mb-2">
              <button type="button" class="btn-main mb-1" onclick='atsend(this.form,"")'>
                  Сохранить
              </button>

              <button type="button" class="btn-outline mb-1" onclick='atsend(this.form,"view")'>
                  Сохранить и посмотреть
              </button>

              <button type="button" class="btn-outline mb-1" onclick='atsend(this.form,"line")'>
                  Сохранить и вставить строку
              </button>

              <?php if (!$flagresult) { ?>
                  <button type="button" class="btn-light mb-1" onclick='atsend(this.form,"delete")'>
                      Удалить навсегда
                  </button>
              <?php } ?>
          </div>

          <div class="section-line"></div>
      <?php } ?>


      <?php if ($flagresult) { ?>

          <!-- показать на карте -->
          <div class="mb-2">
              <button type="button" class="btn-outline" onclick="window.open('/basket_map.php/<?php echo $id; ?>','_blank');">
    Показать на карте
</button>
          </div>

          <!-- экспорт -->
          <div class="mb-2">
              Экспорт:
              <button type="button" class="btn-light" onclick='atsend(this.form,"kml")'>KML</button>
              <button type="button" class="btn-light" onclick='atsend(this.form,"gpx")'>GPX</button>
          </div>

          <!-- прямой линк -->
          <div class="note-block text-small mb-2">
              Прямой линк на эту поездку:<br>
              <a href="https://autotravel.ru/basket_v.php/<?php echo $id; ?>">
                  https://autotravel.ru/basket_v.php/<?php echo $id; ?>
              </a>
          </div>

          <!-- копирование -->
          <?php if ($forum_user_id) { ?>
              <div class="mb-2">
                  <button type="button" class="btn-outline" onclick='atsend(this.form,"copyall")'>
                      Скопировать все объекты и комментарии в новую поездку
                  </button>
              </div>
          <?php } ?>

      <?php } ?>

  </div>
  <!-- =============================================== -->

  <?php
  

// закрываем форму
  
  echo "</form>\n";

  // подключаем общий футер сайта
  include("include/footer_bs.php");
}

?>
