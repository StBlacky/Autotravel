<?php

// полномочия по новостям - begin
 if (($news_enable) && ($admt == 'news')) {
  $newsid = mysafehtml2($_REQUEST["newsid"]);
  $nid = (int) mysafestring($newsid);
  if ($nid){ 
      echo "<h3>Редактируем новость:\n</h3>";
       $sql = "select * from news where id=$nid";
       $result = $dbi->query($sql);
       while ($myrow = mysqli_fetch_array($result)) {
              $cdate = $myrow["calendar"];
              $date = $myrow["date"];
              $title = $myrow["title"];
              $spring = $myrow["spring"];
              $turl = $myrow["url"];
              $exciting = $myrow["exciting"];
              $message = $myrow["text"];
              $extype = substr($exciting,0,1);
              $exid = substr($exciting,1);
       }
  } else { echo "<h3>Ввод новостей:\n</h3>";}
// если данные добавляются
 
  if ($_POST['submitnews']) { 
   $message = mysafehtml2($_REQUEST["message"]);
   $extype  = mysafestring($_REQUEST["extype"]);
   $helpexid  = mysafestring($_REQUEST["helpexid"]);
   $exid = (int) mysafestring($_REQUEST["exid"]);
   $title = mysafestring($_REQUEST["title"]);
   $turl = mysafestring($_REQUEST["turl"]);
   $spring = mysafestring($_REQUEST["spring"]);
   $cdate = mysafestring($_REQUEST["cdate"]);
   $nid = mysafestring($_REQUEST["nid"]);

   if ($cdate == "") {
    $ccdate = "0000-00-00";
   } else {
      if (substr($cdate,4,1) == "-") {$ccdate = $cdate; }
         else {$ccdate = substr($cdate,6,4) . "-" . substr($cdate,3,2) . "-" . substr($cdate,0,2);
      }
   }

// преобразование типов объектов
   if (($extype != "a") and ($extype != "t") and ($extype != "e") and ($extype != "r")) {
    $extype = "x";
    $exid   = 0;
    $helpexid    = "";
   }  else {
    if ($exid == 0) {
// если не было типа, то и id быть не должно
      $extype = "x"; }
   }
   if ($exid > 0) {$exc = $extype . (string) $exid; }  else {$exc = "";}
   
  if (($title != "") and ($message != "") and ($extype != "")) {
      if ($nid > 0) {
//изменяем новость
          $sql_upd = "UPDATE news SET 
              calendar = '$ccdate',
              text= '$message',
              title = '$title',
              spring = '$spring',
              url = '$turl',
              exciting = '$exc' where id = '$nid';";
              $result_upd = $dbi->query($sql_upd);
          if ($result_upd) {
              echo "<h3>Новость успешно изменена\n</h3>";
              $nid = 0;
              $message = "";
              $extype  = "x";
              $cdate   = "";
              $date   = "";
              $title   = "";
              $exid    = "";
              $helpexid    = "";
          } else {
               echo "<h3>При изменении произошла ошибка\n<BR>";
               echo $dbi->errno.": ".$dbi->error."</h3>";
          }  
      }  else {
   // вводим новость  
           $sql_in = "INSERT INTO news (date, calendar, text, title, 
                spring, url, exciting) VALUES 
                (CURRENT_DATE, '$ccdate','$message', '$title',
                '$spring', '$turl', '$exc')";
           $result_in = $dbi->query($sql_in);
           if ($result_in) {
               echo "<h3>Новость успешно добавлена</h3>\n";
               $message = "";
               $extype  = "x";
               $cdate   = "";
               $title   = "";
               $exid    = "";
               $helpexid    = "";
            } else {
               echo "<h3>При добавлении произошла ошибка\n<BR>";
               echo $dbi->errno.": ".$dbi->error."</h3>";
            }
       }
   }
  }
// если даже было добавление/исправление, всё равно форму ввода показать надо.
  echo "<form name=news action=".$_SERVER["PHP_SELF"]." method=post >\n";
  echo "<INPUT name=admt type=hidden value='news'>\n";
  echo "<TABLE BORDER=0 cellspacing=2 CELLPADDING=2><tbody class=travell0>\n";
  echo "<TR><td width=220 class=travel1 align=right>Дата события (или пусто):&nbsp;</TD><TD><INPUT name=cdate type=text size=80 onchange=ChDate(this) value=$cdate></TD></TR>\n";
  echo "<TR><td width=220 class=travel1 align=right>Заголовок события:&nbsp;</TD><TD><INPUT name=title type=text size=80 value='$title'></TD></TR>\n";
  echo "<TR><td width=220 class=travel1 align=right>Источник новости:&nbsp;</TD><TD><INPUT name=spring type=text size=80 value='$spring'></TD></TR>\n";
  echo "<TR><td width=220 class=travel1 align=right>URL источника новости:&nbsp;</TD><TD><INPUT name=turl type=text size=80 value='$turl'></TD></TR>\n";
  echo "<TR><td width=220 class=travel1 align=right>Текст новости:&nbsp;</TD><TD><TEXTAREA name=message rows=10 cols=60 wrap=none value='$message'>\n";
  echo "</TEXTAREA></TD></TR>\n";
  if ($message) {
   echo "\n<script type='text/javascript'>\n";
   echo "<!--\n";
   echo "var str00='$message';\n";
   echo "var rexp = /\[br\]/gi;\n";
   echo "str00 = str00.replace(rexp,'\\n');\n";
   echo "document.forms['news'].elements['message'].value = str00;\n";
   echo "//-->\n";
   echo "</script>\n";
  }
  echo "</TR><TR><td width=220 class=travel1 align=right>";
  echo "Тип связи:&nbsp;</td><td class=travel1>\n";
  echo "<input TYPE=radio VALUE=a NAME=extype";
  if ($extype == "a") { echo " CHECKED=true"; }
  echo ">Область&nbsp;\n";
  echo "<input TYPE=radio VALUE=t NAME=extype";
  if ($extype == "t") { echo " CHECKED=true"; }
  echo ">Город&nbsp;\n";
  echo "<input TYPE=radio VALUE=e NAME=extype";
  if ($extype == "e") { echo " CHECKED=true"; }
  echo ">Объект&nbsp;\n";
  echo "<input TYPE=radio VALUE=r NAME=extype";
  if ($extype == "r") { echo " CHECKED=true"; }
  echo ">Трасса&nbsp;\n";
  echo "<input TYPE=radio VALUE=x NAME=extype";
  if ($extype == "x") { echo " CHECKED=true"; }
  echo ">Нет привязки&nbsp;</TD</TR>\n";
  echo "<TR><td width=220 class=travel1 align=right>id связи:&nbsp;</TD>\n";
  echo "<TD><INPUT name=exid type=text size=20 ";
  if ($exid > 0) { echo "value=$exid"; }
  echo ">";
  echo "<input type=button name=ssx   onclick=SelId('news','exid','/f_area.php') value='Найти область'>\n";
  echo "<input type=button name=ssxx  onclick=SelId('news','exid','/f_town.php') value='Найти город'>\n";
  echo "<input type=button name=ssxxx onclick=SelId('news','exid','/f_exci.php') value='Найти объект'>\n";
  echo "</TD></TR>\n";
  echo "<TR><TD>&nbsp;</TD><TD class=gensmall><input type=text name=helpexid size=80 style='width:450px; font-size:90%' class=helpline value='$helpexid'>";
  echo "</TD></TR>";
  if ($nid > 0) {
   echo "<INPUT name=nid type=hidden value=$nid>\n";
   echo "<TR><TD>&nbsp;</TD><TD><INPUT name=submitnews type=submit value='Отредактировать новость'>\n";
  } else {
   $nid = 0;
   echo "<TR><TD>&nbsp;</TD><TD><INPUT name=submitnews type=submit value='Добавить новость'>\n";
  }
  echo "&nbsp;</TD></TR></TABLE></FORM>\n";
 }
// полномочия по новостям - end

?>