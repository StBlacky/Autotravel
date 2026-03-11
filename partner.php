<?php
include_once("include/inclptn.php");
include("include/head01.php");
// тут пользователь уже авторизован 

if ($_user_id>0) {

 $mess = '';
 $authtype = 'partner';
 $authid   = $_user_id;
 $yesterday = date('Y-m-d',time-3600*24);
 $id    = (int)mysafestring($_REQUEST["id"]);
 $_r_id = 0;
// поле формы теперь называется message
 $message = $_REQUEST["message"];
 $fotodel = $_REQUEST["fotodel"];
 $submitexc  = $_REQUEST["submitexc"];
 $submitfile = $_REQUEST["submitfile"];
 $submitcomment = $_REQUEST["submitcomment"];
 $changecomment = $_REQUEST["changecomment"];
 $extext  = mysafehtml($_REQUEST["message"]);
 $excomment  = mysafehtml2($_REQUEST["excomment"]);
 $stime  = (int) mysafestring($_REQUEST["stime"]);
 $scook  = mysafestring($_REQUEST["scook"]);
 $_r_srok = "";
 $_r_auth = "";
 $_r_text = "";
 $_r_name = "";
 $_r_desc = "";
 $_r_town = "";
 $_r_tnam = "";
 $_r_stcm = 0;
 $_r_stpm = 0;
 $_r_stcy = 0;
 $MAX_SIZE = 7500000;
 $maxfiles = 3;
 $album_dir="/home/blacky/data/www/photo";

 $sql = "select r.srock as srok, r.auth as auth, r.reklama as text, r.ex as id,
             e.name as name, e.descrip as desr, e.town as town,
             t.name as tnam
             from reklama r, exciting e, towns t
             where r.partner = $_user_id and r.ex = e.id
             and t.id = e.town";
 $result = $dbi->query($sql);
 echo "<h1>Здравствуйте, $_user_dopinfo</h1>\n";
 echo "<table width=100% border=0 cellpadding=1 cellspacing=0>\n";
 $xcount = mysqli_num_rows($result);
 if($myrow = mysqli_fetch_array($result)) {
   if ($xcount > 1) {
    echo "<tr><td><p class=travell5c>Другие объекты для управления:</p>\n";
    echo "</td></tr>\n";
   }
   do {
     $r_id   = (int) $myrow["id"];
     $r_srok = $myrow["srok"];
     $r_auth = $myrow["auth"];
     $r_text = $myrow["text"];
     $r_name = $myrow["name"];
     $r_desc = $myrow["desr"];
     $r_town = $myrow["town"];
     $r_tnam = $myrow["tnam"];
     if (($id == 0) and (!($submitexc))) {
      $id = $r_id;
     }
     if ($id  == $r_id) {
      $_r_id   = $r_id;
      $_r_srok = $r_srok;
      $_r_auth = $r_auth;
      $_r_text = $r_text;
      $_r_name = $r_name;
      $_r_desc = $r_desc;
      $_r_town = $r_town;
      $_r_tnam = $r_tnam;
     }
     else {
      if ($xcount > 1) {
       echo "<tr><td colspan=3 class=\"t12 travell0t\">\n";
       echo "<a href=".$_SERVER["PHP_SELF"]."?id=$r_id>$r_name</a> ($r_tnam)\n";
       echo "</td></tr>\n";
      }
     }
   } while($myrow = mysqli_fetch_array($result)); 
   mysqli_free_result($result);


// фотографии - подготовка
   $usernew = $_r_id;
   $upload_dir = $album_dir."/".$usernew;
   if ($usernew) {
         if (!(is_dir($upload_dir))) {
           if (!mkdir($upload_dir))
            die ("upload_files directory doesn't exist and creation failed");
           if (!chmod($upload_dir,0775))
            die ("change permission to 775 failed.");
         }
   }
// сперва - удаление фото.
   if ((($_r_id) == ($id)) and ($fotodel) and ($id > 0)) {
    $foto = preg_replace('/[^\da-z]/i', '', $fotodel);
    if (($foto)) {
     if(is_file($upload_dir."/".$foto.".jpg")) {
      unlink($upload_dir."/".$foto.".jpg");
     }
     if(is_file($upload_dir."/".$foto."-s.jpg")) {
      unlink($upload_dir."/".$foto."-s.jpg");
     }
    }
   }

// теперь - добавление фото
  /* нам прислали файл */
  if ((($_r_id) == ($id)) and ($submitfile) and ($id > 0)) {
   if(isset($_FILES['userfile']) ) {
    $temp_name = $_FILES['userfile']['tmp_name'];
    if ( $_FILES['userfile']['size'] > $MAX_SIZE) {
     $mess = "Размер файла превышает ".$MAX_SIZE." байт. Уменьшите его.";
     unlink($temp_name);
    }
    else {
     $isize = getimagesize($temp_name);
     if ($isize[2] != 2) {
      $message = "Извините, тип файла - не картинка в формате JPEG.";
      $returl = "";
      unlink($temp_name);
     }
     else {
      $fname = "";
      $file_jpeg = "";
      $file_tn   = "";
      $fotoar = listfoto($upload_dir);
      $fotocount = sizeof($fotoar);
      if ($fotocount<$maxfiles) {
        $fname = random_string(30);
        $file_jpeg = $upload_dir . "/" . $fname . ".jpg";
        $file_tn = $upload_dir . "/" . $fname . "-s.jpg";
      }
      if ($file_jpeg) {
       $mess = myjpegresize($temp_name, $file_jpeg, 900, 0);
       if (!($message)) {
        //unlink($temp_name);
         $mess = myjpegresize($file_jpeg, $file_tn, 250, 0);
       }
       if (!($message)) {
        $mess = "Файл загружен.";
       }
       else {
        $mess = "Файл НЕ загружен!!!";
        unlink($temp_name);
       }
      }
      else {
       $mess = "Файл НЕ загружен!!!";
       unlink($temp_name);
      }
     }
    }
   }
  }
  /* конец нам прислали файл */


// теперь - отображение фотографий. Пока, во временную переменную fotoout
  $fotoar = listfoto($upload_dir);
  $fotocount = sizeof($fotoar);
  $fotoout = "";
  for ($i=0; $i<$fotocount; $i++) {
   $fotoout = $fotoout . "<img src=/photo/$usernew/".$fotoar[$i]."-s.jpg><br>\n";
   if ((($_r_id) == ($id)) and (!($stime)) and ($_r_auth)) {
      $fotoout = $fotoout .  "<form method=post action=".$_SERVER["PHP_SELF"]." name=\"fotodel$fotocount\">\n";
      $fotoout = $fotoout .  "<input type=hidden name=fotodel value=\"$fotoar[$i]\">\n";
      $fotoout = $fotoout .  "<input type=hidden name=id value=$_r_id>\n";
      $fotoout = $fotoout .  "<input type=submit value=Удалить></FORM><br><hr>\n";
   }
  }
  if (($fotocount < $maxfiles)  and (!($stime))) {
// если не коммент к отклику
    $fotoout = $fotoout . "<form name=\"upload\" enctype=\"multipart/form-data\"  method=post action=".$_SERVER["PHP_SELF"].">\n";
    $fotoout = $fotoout . "<input type=hidden name=id value=$_r_id>\n";
    $fotoout = $fotoout . "<input type=hidden name=submitfile value=$_r_id>\n";
    $fotoout = $fotoout . "Фото: <input type=\"file\" name=\"userfile\" size=30>\n";
    $fotoout = $fotoout . "<input type=submit name=\"upload\" value=\"Загрузить\"></FORM><br>\n";
  }

/* обновляем описание */
  if ((($_r_id) == ($id)) and ($submitexc) and ($id > 0)) {
    $sql = "insert into history (tblname,cook,ip,id,field,valueold,valuenew) 
                values ('reklama','$ctext','$ipp',$_r_id,'reklama','$_r_text','$extext')";
    $result = $dbi->query($sql);
    mysqli_free_result($result);
    $sql = "update reklama set reklama = '$extext' where ex = $_r_id";
    $result = $dbi->query($sql);
    if ($result) {
     $_r_text = $extext;
    }
    else {
// ошибка обновления
    }
    mysqli_free_result($result);
  }
  $_r_text = my_unhtml($_r_text);

/* меняем/добавляем комментарий */
  if (($stime)) {
   $c_descrip = "";
   $c_comment = "";
   if ($scook) {
     $tcook = "='$scook'";
   }
   else {
     $tcook = " is null";
   }
   $sql = "select descrip, comment from otklik where exciting=$id and time=$stime and cook".$tcook;
   $result = $dbi->query($sql);
   if($myrow = mysqli_fetch_array($result)) {
     $c_descrip = $myrow["descrip"];
     $c_comment = $myrow["comment"];
   }
   mysqli_free_result($result);

   if (preg_match("/^\w+\:(\d+)\:(.+)/",$c_comment,$s_match)) {
      $c_comment = $s_match[2];
   }
   else {
      $c_comment = "";
   }

// сперва - меняем
   if ((($_r_id) == ($id)) and ($changecomment) and ($id > 0)) {
    $sql = "insert into history (tblname,cook,ip,id,field,valueold,valuenew) 
                values ('otklik','$ctext','$ipp',$_r_id,'comment:$stime:$scook','$c_comment','$excomment')";
    $result = $dbi->query($sql);
//    mysqli_free_result($result);
    if ($excomment) {
     $sql = "update otklik set comment = '$authtype:$authid:$excomment' where exciting = $id and time=$stime and cook".$tcook;
//    echo $sql;
    }
    else {
     $sql = "update otklik set comment = NULL where exciting = $id and time=$stime and cook".$tcook;
    }
    $result = $dbi->query($sql);
//    mysqli_free_result($result);
   }
   if ((($_r_id) == ($id)) and ($submitcomment) and ($id > 0)) {
// рисуем форму изменения
    $c_comment = my_unhtml($c_comment);
    echo "<form name=submcomment method=post action=".$_SERVER["PHP_SELF"].">\n";
    echo "<b>Отзыв посетителя:</b><br>\n";
    echo "$c_descrip<br>\n";
    echo "<b>Комментарий:</b><br>\n";
    echo "<TEXTAREA name=excomment rows=15 cols=40 wrap=none>\n";
    echo "</TEXTAREA>\n";
    echo "<INPUT TYPE=HIDDEN NAME=id VALUE=$id>\n";
    echo "<input type=hidden name=stime value=$stime>\n";
    echo "<input type=hidden name=scook value='$scook'>\n";
    echo "<BR><INPUT name=changecomment type=submit value='Изменить'>\n";
    echo "</form>\n";
    echo "\n<script type='text/javascript'>\n";
    echo "<!--\n";
    echo "var str00='$c_comment';\n";
    echo "var rexp = /\[br\]/gi;\n";
    echo "str00 = str00.replace(rexp,'\\n');\n";
    echo "document.forms['submcomment'].elements['excomment'].value = str00;\n";
    echo "//-->\n";
    echo "</script>\n";
   }             	
   else {
// будет другая форма
    $stime = 0;
    $scook = "";
   }
  }

   echo "<tr><td colspan=3><p class=travell5c>$_r_name ($_r_tnam).</p>\n";
   echo "<p class=\"t12 tl travell0o\">". $_r_desc . "</p>\n";
   echo "</td></tr></table>\n";
   if ($mess) {
    echo "<h3 class=travell5c>$mess</h3>\n";
   }
   echo "<table width=100% border=1 cellpadding=1 cellspacing=0>\n";
   $dtarr = getdate(time());
   $cmonth = (int) mktime(0,0,0,$dtarr["mon"],1,$dtarr["year"]);
   $cyear  = (int) mktime(0,0,0,1,1,$dtarr["year"]);
   if ($dtarr["mon"] > 1) {
    $pmonth = (int) mktime(0,0,0,$dtarr["mon"]-1,1,$dtarr["year"]);
    $sql_s = "select dt,count from stats_ex where ex=$_r_id and dt >= $cyear";
   }
   else {
    $pmonth = (int) mktime(0,0,0,12,1,$dtarr["year"]-1);
    $sql_s = "select dt,count from stats_ex where ex=$_r_id and dt >= $pmonth";
   }
   $result_s = $dbi->query($sql_s);
   if($myrow_s = mysqli_fetch_array($result_s)) {
     do {
       $dttmp = (int) $myrow_s["dt"];
       $cntmp = (int) $myrow_s["count"];
       if ($dttmp === $pmonth) {
        $_r_stpm = $cntmp;
       }
       if ($dttmp === $cmonth) {
        $_r_stcm = $cntmp;
       }
       if ($dttmp >= $cyear) {
        $_r_stcy = $_r_stcy + $cntmp;
       }
     } while ($myrow_s = mysqli_fetch_array($result_s));
   }
// форма ввода -- только если активно. Иначе только статистика.
   echo "<tr>\n";
   echo "<TD rowspan=2 width=40% valign=top>\n";
   echo "<p class=travell5c>Ваша информация <font class=travell0>(показ оплачен до ".displaydate($_r_srok).")</font>:</p>\n";
   if (($_r_auth) and (!($stime))) {
    echo "<form name=l_report method=post action=".$_SERVER["PHP_SELF"].">\n";
?>
  <script language="JavaScript" SRC="js/report.js"></script>
  <table border="0" cellspacing="0" cellpadding="1">
  <tr align="center" valign="middle">
  <td>
  <input type="button" class="button" accesskey="b" name="addrcode0" value=" жирный " style="font-weight:bold; width: 60px;" onClick="rstyle(0)" onMouseOver="helpline('b')" />
  </td>
  <td>
  <input type="button" class="button" accesskey="i" name="addrcode2" value=" курсив " style="font-style:italic; width: 60p;" onClick="rstyle(2)" onMouseOver="helpline('i')" />
  </td>
  <td>
  <input type="button" class="button" accesskey="u" name="addrcode4" value=" подчерк " style="text-decoration: underline; width: 60px;" onClick="rstyle(4)" onMouseOver="helpline('u')" />
  </td>
  <td>
  <input type="button" class="button" accesskey="w" name="addrcode6" value=" URL " style="text-decoration: underline; width: 60px" onClick="rstyle(8)" onMouseOver="helpline('w')" />
  </td>
  <tr>
  <td colspan="4"> <span class="gensmall">
  <input type="text" name="helpbox" size="45" maxlength="100" style="width:450px; font-size:90%" class="helpline" value="Подсказка: Можно быстро применить стили к выделенному тексту" />
  </span></td>
  </tr>
  </table>
<?php
    echo "<TEXTAREA name=message rows=10 cols=55 wrap=none>\n";
    $extextr = str_replace("[br]","\n",$_r_text);
    echo str_replace("[BR]","\n",$extextr);
    echo "</TEXTAREA>\n";
    echo "<INPUT TYPE=HIDDEN NAME=id VALUE=$_r_id>\n";
    echo "<BR><INPUT name=submitexc type=submit value='Записать (изменить) текст'>\n";
    echo "</form>\n";
   }
   else {
    echo "<p class=\"t12 tl travell0o\">" . mysafehtml($_r_text) . "</p>\n";
   }
   echo "<hr width=95%>\n";
   echo $fotoout;
   echo "</td><td align=left valign=top>";
// Статистика посещений
   echo "<p class=travell5c>Статистика посещений:</p>\n";
   echo "<p class=travell0>За текущий месяц: <b>$_r_stcm</b><BR>\n";
   echo "За предыдущий месяц: <b>$_r_stpm</b><BR>\n";
   echo "С начала года: <b>$_r_stcy</b></p>\n";
   echo "</td></tr>\n";
   echo "<tr><td align=left valign=top>";
   echo "<p class=travell5c>Отзывы посетителей:</p>\n";
   $otkl_rep = getotklik($_r_id,"","");
   echo $otkl_rep . "</td></tr>\n";
 }
 else {
   echo "<tr><td><p class=travell5>Нет объектов для управления.</p>";
   echo "</td></tr>\n";
 }
 echo "</table>\n";
}

else {
// если есть $messagetext, значит авторизация неуспешна.
// иначе - просто её не было, надо показать текст с предложением
 if ($messagetext) {
   echo "<div align=center>\n";
   echo "<table width=100% border=1 cellpadding=1 cellspacing=0>\n";
   echo "<tr><td align=center colspan=2>\n";
   echo "<h3 class=travell5>$messagetext</h3>\n";
   echo "</td></tr>\n";
   echo "<td align=center><a class=travell5c href=partner.php>Прочитать условия программы</a></td>\n";
   echo "<td align=center><a class=travell5c href=partner.php?login=1>Ввести имя и пароль ещё раз</a>\n";
   echo "</td></tr></table</div>\n";
 }
 else {
 ?>
<div align=center>
<table width=100% border=1 cellpadding=1 cellspacing=0><tr><td align=left>
<h3>Уважаемые господа !</h3>
<p class=travell0>Теперь на нашем сайте для каждого объекта, имеющее  хоть какое-то отношение к  туризму открыта новая возможность.</p>
<p class=travell0>Теперь Вы не только сможете заявить о себе , но и получить   непредвзятую информацию от людей ,  уже когда-то посетивших Ваше заведение.  А также  воспользоваться  достоверной и актуальной информацией  как о интересе  к Вашему объекту , так и о  сходных видах  объектов , а так же общей  картине  по Вашей местности.</p> 
<p class=travell0>Для всего этого   разработаны следующие сервисы :
<ul class=travell0>
<li>Сервис <b>дополнительная информация об объекте</b> . В этом разделе Вы 
можете  размещать дополнительную информацию о Вашем объекте в объеме не более 5000 симводов (одна печатная страница).
Вся внесенная в  этом окне информация отображается в  разделе "Нас приглашают" 
на главной странице сайта сразу же после ее обновления. 
Количество обновлений не ограничено, Вы  всегда можете оперативно 
информировать Ваших потенциальных клиентов о новостях или промопрограммах, 
которые у Вас проходят. Также, размещенная Вами дополнительная информация 
отображается  при любом запросе по Вашему объекту одновременно 
с базовой информацией, приведенной на сайте. 
Также, Вы можете добавить до 3 фотографий Вашего объекта, 
которые будут доступны для просмотра всеми посетителями, 
просматривающими информацию и фотографии Вашего объекта.</li>
<li>Сервис <b>статистика объекта</b> В этом разделе  можно получить информацию 
по общему количеству запросов по Вашему объекту. 
Это позволит определить уровень общего интереса к объекту в целом. Данные 
по запросам представлены за год, за предыдущий и за последний месяц.</li>
<li>Сервис <b>отзывы по объекту</b>В этом разделе Вы можете ознакомиться с 
отзывами посетителей сайта, посетивших Ваш объект. Впечатление оценивается по 
5-ти балльной шкале, дополнительно каждый посетитель оставляет текстовый комментарий.
Данный сервис выводит все отзывы по объекту в хронологическом порядке. 
<!-- К каждому отзыву Вы можете оставить свой комментарий, который будет доступен 
для прочтения всеми посетителями сайта, одновременно с отзывом.
!-->
</li>
</p>
<p class=travell0>Для того, чтобы стать обладателем всего пакета сервисов
сроком на 1 год , необходимо  ознакомиться с <b>договором</b>, регламентирующим
отношения сторон. В случае согласия и для получения доступа к сервису, 
а также при возникновении организационных вопросов, следует отправить запрос 
на электронный адрес <a mailto=plo@autotravel.ru>plo@autotravel.ru</a>.
</p>
<p class=travell0>Техническая поддержка, вопросы и пожелания по эксплуатации: 
<a mailto=help@autotravel.ru>help@autotravel.ru</a></p>
</td></tr>
<tr><td align=center>
<a class=travell5c href=partner.php?login=1>Вход для обладателей имени и пароля</a>
</td></tr>
</table>
</div>
 <?php
 }
}

?>
</body></html>
