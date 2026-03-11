<?php

// forumsess надо звать в самом начале. А то переменные переопределятся.
// Но внутри ничего не echo-ить, а то заголовки и куки покривеют.
include_once('include/head00.php');
$bootstrap=1;
include_once('include/pf/HTMLPurifier.standalone.php');

$_p_config = HTMLPurifier_Config::createDefault();
$_p_config->set('Attr.EnableID', false);
$_p_config->set('AutoFormat.RemoveEmpty',true);
//$_p_config->set('HTML.TidyRemove', 'br@clear');
$_p_config->set('Attr.AllowedClasses',array());
$_p_config->set('HTML.TargetBlank',true);
$purifier = new HTMLPurifier($_p_config);

//if (($_REQUEST['submitback'] === "edit") and (!($forum_user_id > 0))) {
//  include_once('include/inclmgr.php');
//}


$jhtmleditor = 1;
$table = (string) $_REQUEST["submitraw"];
if ($table != "") {
 $table = "m_reports";
}
else {
 if ($_REQUEST["table"] == "m_reports") {
  $table = "m_reports";
 }
 else {
  $table = "l_reports";   
 }
}
$_moderated = 0;
if (!(empty($_REQUEST["message"]))) {
 if (bannedlist($_REQUEST["message"])) {
  $table = "m_reports";
  $_REQUEST["submitback"]="edit";
 }
 if ($forum_user_banned) {
  $table = "m_reports";
  $_REQUEST["submitback"]="edit";
 }
}

if (((!($_REQUEST['submitp'])) and (!($_REQUEST['submitraw'])) and (!($_REQUEST['submitr']))) or ($_REQUEST['submitback'])) {
  include('include/header_bs.php');
  //echo "<script language=\"JavaScript\" SRC=\"/js/report.js\"></script>";
  $year = (string) ((int) $_REQUEST["year"]);
  $month = (string) ((int) $_REQUEST["month"]);
  $gr = (string) ((int) $_REQUEST["gr"]);
  $rid = (int) (mysafestring($_REQUEST["rid"]));
  $ip = mysafestring($_REQUEST["ip"]);
  $time = (int) mysafestring($_REQUEST["time"]);
  $key = mysafestring($_REQUEST["key"]);
  $message = $_REQUEST["message"];
  $message = preg_replace(array("#&lt;#i","#&gt;#i"), array("<",">"), $message);
  $message = $purifier->purify($_REQUEST["message"]);
  //$message = preg_replace("#<p>#i", "<p class=\"t12 tl travell0t\">", $message);

  //$message = mysafehtml($_REQUEST["message"]);
  $keys = mysafestring($_REQUEST["keys"]);
  $name = mysafestring($_REQUEST["name"]);
  $mail = mysafestring($_REQUEST["mail"]);

  if ($_REQUEST["submitback"] === "edit") {

    $sql = "select * from $table where rid='$rid' and cook='$ctext'";

    $result = $dbi->query($sql);
    if ($myrow = mysqli_fetch_array($result)) {
      $message = $myrow['report'];
      $key = $myrow['key_towns'];
      $key = getskeytext($key, $rid);
      $gr = $myrow['group_rep'];
      $name = $myrow['nick'];
      $mail = $myrow['mail'];
      $ip = $myrow['ip'];
      $year = $myrow['year'];
      $month = $myrow['month'];
      $keys = $myrow['spring'];
    }
    mysqli_free_result($result);
  }
  echo "<div class=\"container-fluid\" style=\"width:100%;\"><div class=\"row\"><div class=\"col-xs-12 well\">\n";
  echo "<p class=travell5>Внимание!</p>\n";
  echo "<p class=travell0>Здесь Вы можете разместить только свой отчёт, чужие тексты размещать не нужно.</p>\n";
  echo "<p class=travell0>Просьба к заполняющим форму. Пожалуйста, перечисляйте города именно через запятую. Иначе не будет работать поиск Вашего отчёта по городам.</p>\n";
  echo "<p class=travell0>Подумайте: если у Вас нет склонностей к литературному творчеству, может оказаться гораздо удобнее и правильнее разместить несколько отдельных отзывов по достопримечательностям.</p>\n";
  echo "<p class=travell0>Администрация сайта оставляет за собой право на использование размещённых здесь отчётов: редактирование, удаление, предоставление третьим лицам, использование данных отчёта в своих текстах и иные действия, соответствующие целям и тематике сайта.</p>\n";
  echo "</div></div>\n";
  echo "<form class=\"form-horizontal\" name=\"l_report\" action=\"" . $_SERVER["PHP_SELF"] . "\" method=\"post\" onsubmit=\"return formcheck()\">";
  echo "<div class=\"row\"><div class=\"col-xs-12\">\n";
  echo "<div class=\"form-group\"><label class=\"control-label col-sm-3\">Выберите длительность вашего путешествия</label><div class=\"col-sm-9\">";

  if ($gr) {
    $marked = 1;
  } else {
    $marked = 0;
  }

  $sql_g = "select g.id as id, g.name as gr from group_rep g order by id";
  $result_g = $dbi->query($sql_g);
  if ($myrow_g = mysqli_fetch_array($result_g)) { // выводим группы
    echo "<Select class=\"form-control col-sm-10\" name=gr size=1>\n";
    do {
      $s_id_g = $myrow_g["id"];
      $s_group = $myrow_g["gr"];
      if (!($marked)) {
        echo "<option SELECTED value=$s_id_g>&nbsp;$s_group\n";
        $marked = 1;
      } else {
        if ($gr == $s_id_g) {
          echo "<option SELECTED value=$s_id_g>&nbsp;$s_group\n";
        } else {
          echo "<option value=$s_id_g>&nbsp;$s_group\n";
        }
      }
    } while ($myrow_g = mysqli_fetch_array($result_g));
    echo "</select>\n</div></div>\n";
  }
  mysqli_free_result($result_g);

  echo "<div class=\"form-group\"><label class=\"control-label col-sm-3\">Укажите пункт старта и (если он отличается) пункт назначения, через запятую</label>\n";
  echo "<div class=\"col-sm-9\"><input  class=\"form-control\" type=text name=keys size=80 value=\"$keys\">\n</div></div>";
  echo "<div class=\"form-group\"><label class=\"control-label col-sm-3\">Укажите через запятую города, посещённые и описанные Вами</label>\n";
  echo "<div class=\"col-sm-9\"><TEXTAREA  class=\"form-control\" name=\"key\" rows=1 cols=80 wrap=\"none\">";
  echo $key;
  echo "</TEXTAREA>\n</div></div>";
  echo "<div class=\"row\"><div class=\"col-sm-3 col-xs-6\"><label class=\"pull-right\">Когда это было?</label></div><div class=\"col-sm-9 col-xs-6\">\n";

  echo "<div class=\"form-group\"><label class=\"control-label col-sm-3\">Месяц (1, 2, ..., 12):</label><div class=\"col-sm-9\"><select class=\"form-control\"  size=1 name=month>\n";

  $i = 1;
  $len = "%'002s";
  while ($i <= 12) {
    if ($i == $month) {
      echo "<option SELECTED>" . sprintf($len, $i) . "\n";
    } else {
      echo "<option>" . sprintf($len, $i) . "\n";
    }
    $i++;
  }

  echo "</select></div></div>\n";
  echo "<div class=\"form-group\"><label class=\"control-label col-sm-3\">Год (2017, 2018 ...):</label><div class=\"col-sm-9\"><INPUT class=\"form-control\"  name=year type=text size=4 value=";
  if ($year) {
    echo $year;
  } else {
    echo date("Y");
  }
  echo "></div></div></div></div>\n";
  echo "<div class=\"form-group\"><label class=\"control-label col-sm-3\">Имя:</label><div class=\"col-sm-9\"><INPUT  class=\"form-control\"  name=name type=text size=40 ";
  if ($name) {
    echo " value='$name'";
  } else {
    if ($forum_user_id != 0) {
      echo " value='$forum_user_name'";
    }
  }
  echo "><br>\n";
  if ($forum_user_id == 0) {
    echo "<p>Если Вы хотите, чтобы отчет мог быть отредактирован после размещения,<br> надо <a class=travell5m href='/sitemgr.php'>войти на сайт</a>.</p>\n";
  }
  echo "</div></div></div>";
  if ($forum_user_id == 0) {
    echo "<div class=\"form-group\"><label class=\"control-label col-sm-3\">Эл. адрес:</label><div class=\"col-sm-9\"><INPUT class=\"form-control\" name=mail type=text size=40 value='$mail'></div></div>";
  }
  echo "";
  echo "<div class=\"row\"><div class=\"col-xs-12\">";
  echo "<TEXTAREA class=\"form-control\" id=\"msginput\" rows=90 name=\"message\" wrap=none>";
  $tmess = $purifier->purify($message);
  echo $tmess;
  echo "</TEXTAREA><br>";
  echo "<INPUT class=\"btn btn-default\" name=submitp type=submit value='Предпросмотр'>";

  if ((($rid) and ($table == "m_reports")) or (!($rid))) {
   echo "&nbsp;&nbsp;";
   echo "<INPUT  class=\"btn btn-default\" name=submitraw type=submit value='Сохранить черновик'>";
  }
  if ((($rid) and ($table == "l_reports")) or (!($rid))) {
   echo "&nbsp;&nbsp;";
   echo "<INPUT name=submitr  class=\"btn btn-default\" type=submit value='Разместить отчёт на сайте'>";
  }
  echo "<input TYPE=hidden VALUE='$table' NAME=table>\n";
  echo "<input TYPE=hidden VALUE=$ipp NAME=ip>";
  echo "<input TYPE=hidden VALUE=";
  if ($time) {
    echo $time;
  } else {
    echo time();
  }
  echo " NAME=time>";
  echo "<input TYPE=hidden VALUE=";
  if ($rid) {
    echo $rid;
  } else {
    echo "0";
  }
  echo " NAME=rid>";
  echo "</div></div>\n";
  echo "</FORM>";
  echo "</div>\n";
}

// заполнена форма
else {
  $bootstrap=1;
  if ($_REQUEST['submitp']) {
    $colorbox = 1;
  }
  include('include/header_bs.php');
  echo "<div class=\"container-fluid\" style=\"width:100%;\"><div class=\"row\"><div class=\"col-xs-12\">\n";

  $name = mysafestring(stripslashes($_REQUEST["name"]));
  $key = mysafestring(stripslashes($_REQUEST["key"]));
  $year = (int) mysafestring(stripslashes($_REQUEST["year"]));
  $gr = (int) mysafestring(stripslashes($_REQUEST["gr"]));
  $rid = (int) mysafestring(stripslashes($_REQUEST["rid"]));
  $month = mysafestring(stripslashes($_REQUEST["month"]));
  $mail = mysafestring(stripslashes($_REQUEST["mail"]));
  $key = mysafestring(stripslashes($_REQUEST["key"]));
  $keys = mysafestring(stripslashes($_REQUEST["keys"]));
  $message = $_REQUEST["message"];
  //error_log($message);
  $message = preg_replace(array("#&lt;#i","#&gt;#i"), array("<",">"), $message);
  $message = $purifier->purify($message);
  $message = uft8_clearstr($message);
  //$message = preg_replace("#<p>#i", "<p class=\"t12 tl travell0t\">", $message);
  $ip = mysafestring($_REQUEST["ip"]);
  $time = (int) mysafestring($_REQUEST["time"]);

  if (($_REQUEST['submitr']) or ($_REQUEST['submitraw'])) {
    $messagesql=$dbi->real_escape_string($message);
    if (($ctext) and ($message) and ($year) and ($month) and ($ctext)) {
      $screen = "";
      if ($rid) {
        $sql_r = "select * from $table where cook = \"$ctext\" and rid = $rid";
      } else {
        $sql_r = "";
      }
      if ($sql_r) {
        $result_r = $dbi->query($sql_r);
        if (!($myrow_r = mysqli_fetch_array($result_r))) {
          $sql_r = "";
        } else {
          if (($_REQUEST['submitr']) and ($table === "m_reports")) {
           // insert
           $sql_in = "update $table set rid='-$rid' where rid=$rid and cook='$ctext'";
           $table = "l_reports";
           $sql_r = "";
          }
          else {
           // update
           $sql_in = "UPDATE $table set report='$messagesql', group_rep='$gr',
                       key_towns='$key', nick='$name', mail='$mail', ip='$ip',
                       year='$year', month='$month', spring='$keys' where
                       cook='$ctext' and rid = $rid";
          }
          $result_in = $dbi->query($sql_in);
          if ($result_in) {
            file_put_contents("/home/blacky/data/www/sql/insreports.sql",
                  "----\n".$_REQUEST["message"]."----\n".$sql_in.";\n-------------\n\n",FILE_APPEND);
            $screen = "Спасибо. Информация в отчёте обновлена.";
          }
          else {
           file_put_contents("/home/blacky/data/www/sql/insreports.err",
                "----\n".$_REQUEST["message"]."----\n".$sql_in.";\n-------------\n\n",FILE_APPEND);
           $screen = "Ой. Что-то пошло не так и отчет НЕ СОХРАНИЛСЯ :(";
          }
        }
      }
      if (!($sql_r)) {
        if (preg_match("/[йцукенгшщзхъфывапролджэячсмитьбю]/", $message)) {
          if (preg_match("/[йцукенгшщзхъфывапролджэячсмитьбю]/", $key)) {
# отчет должен содержать русские буквы
            $sql_in = "INSERT INTO $table (report, group_rep, key_towns, nick,
                    mail, ip, time, year, month, spring, cook) VALUES
                    ('$messagesql', '$gr','$key', '$name', '$mail','$ip',
                    '$time', '$year', '$month','$keys', '$ctext')";
            $result_in = $dbi->query($sql_in);
            if ($result_in) {
             file_put_contents("/home/blacky/data/www/sql/insreports.sql",
                  "----\n".$_REQUEST["message"]."----\n".$sql_in.";\n-------------\n\n",FILE_APPEND);
             $rid = $dbi->insert_id;
             if ($table === "m_reports") {
              $screen = "Черновик сохранен";
             }
             else {
              $screen = "Большое спасибо за ваш отчет. Он добавлен к списку отчетов!";
             }
            }
            else {
             file_put_contents("/home/blacky/data/www/sql/insreports.err",
                  "----\n".$_REQUEST["message"]."----\n".$sql_in.";\n-------------\n",FILE_APPEND);
             file_put_contents("/home/blacky/data/www/sql/insreports.err",
                  "Error description: " . $dbi -> error . "\n-------------\n\n",FILE_APPEND);
             $screen = "Ой. Что-то пошло не так и отчет НЕ СОХРАНИЛСЯ :(";
            }
          } else {
            $screen = "Это не отчет. Он не был добавлен!";
          }
        }
        else {
          $screen = "Это не отчет. Он не был добавлен!";
        }
      }
      echo "$screen<br>\n";
      if ($rid) {
        echo "<a href=\"" . $_SERVER["PHP_SELF"] . "?rid=$rid&submitback=edit";
        if ($table === "m_reports") {
          echo "&submitraw=edit";  
        }
        echo "\">Редактировать отчет</a></br>";
      }
      // добавить "к редактированию отчетов"
      if ($forum_user_id) {
        echo "<a href=/sitemgr.php?admt=report>К выбору отчетов для редактирования</a>";  
      }
    }
  } 
  elseif ($_REQUEST['submitp']) {

    echo "<center><table width=95% border=1 cellpadding=5 cellspacing=0><tr><td>\n";
    echo "<p class=\"t14 tb tc\">Пункты старта и назначения: $keys</p>\n";
    echo "<p class=\"t14 tb tc\">Посещены: $key</p><p class=travel10>Автор: $name<br>Месяц и год: $month/$year<br></p>\n";
    echo "</td></tr>\n<tr><td><div class=\"t12 tl travell0t\">\n";
    $tmess = fotoreplace($message);
    echo $tmess;
    echo "</div></td></tr></table>\n";
    echo "<form name=l_report action=" . $_SERVER["PHP_SELF"] . " method=post>";
    echo "<hr width=80%><center><input name=submitback type=submit value='Назад к редактированию'>";
    if ((($rid) and ($table == "m_reports")) or (!($rid))) {
     echo "<INPUT name=submitraw type=submit value='Сохранить черновик'>";
    }
    echo "<INPUT name=submitr type=submit value='Разместить отчёт на сайте'></center>";
    echo "<input TYPE=hidden VALUE='$ip'    NAME=ip>\n";
    echo "<input TYPE=hidden VALUE='$table' NAME=table>\n";
    echo "<input TYPE=hidden VALUE='$time'  NAME=time>\n";
    echo "<input TYPE=hidden VALUE='$rid'   NAME=rid>\n";
    echo "<textarea name=\"message\" style=\"display:none;\">".$message."</textarea>\n";
    echo "<input TYPE=hidden VALUE='$name'  NAME=name>\n";
    echo "<input TYPE=hidden VALUE='$year'  NAME=year>\n";
    echo "<input TYPE=hidden VALUE='$mail'  NAME=mail>\n";
    echo "<input TYPE=hidden VALUE='$key'   NAME=key>\n";
    echo "<input TYPE=hidden VALUE='$keys'  NAME=keys>\n";
    echo "<input TYPE=hidden VALUE='$gr'    NAME=gr>\n";
    echo "<input TYPE=hidden VALUE='$month' NAME=month>\n";
    echo "</td>";
  }
  echo "</div>\n</div>";
}
$_totop=1;
echo "</div>";
include('include/footer_bs.php');
?>
