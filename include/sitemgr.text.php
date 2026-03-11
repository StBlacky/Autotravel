<?php                                       


///////////////////////////////////////////////////////////
// вставка обширных комментариев - begin


 if (($text_enable) && ($admt == 'text')) {
  $textadmin = 0;
  if ($exciting_enable) {
   $textadmin = 1;
  }
  $submit  = mysafestring($_REQUEST['s']);
  $message = mysafehtml($_REQUEST['message']);
  $utown   = (int)mysafestring($_REQUEST['utown']);
  $exutown = (int)mysafestring($_REQUEST['exutown']);
  
  echo "<h1 class=travell5m><center>Ввод и редактирование описания</center></h1>\n";

  $helputown   = "";
  $helpexutown = "";
  $obj = "";

  if ($exutown) {
   $obj = "e" . $exutown;
  }
  else {
   $obj = "t" . $utown;
  }

  if (($submit === "query") && ($textadmin) && ($obj)) {
   $sql         = "select * from l_text where obj='$obj' ";
   $cook        = "";
   $selected    = "";
   $result      = $dbi->query($sql);
   if($myrow    = mysqli_fetch_array($result)) { 
    $message    = $myrow['descr'];
    $cook       = $myrow['cook'];
    $dtizm      = $myrow['dtizm'];
   }
   echo "<TABLE BORDER=0 cellspacing=2 CELLPADDING=2><tbody class=travell0>\n";
   echo "<TR><TD>\n";
   echo "<p class=travel1>\n";
   if ($exutown) {
     list($exname,$exdesc,$exdesc2,$exdesc4) = explode('\|',getexdesc($exutown));
     echo "<b>" . $exname . "</b><br>" . $exdesc . "<br>\n";
   }
   else {
     echo gettownname($utown) . ":<br>\n";
   }
   echo "<hr width=40%>\n";
   echo authform($cook) . ":<br/>\n";
   if ($dtizm == "0000-00-00 00:00:00") {
    echo "<a href=/sitemgr.php?admt=text&utown=$utown&exutown=$exutown&s=pub>Опубликовать</a><br/>\n";
    echo "<a href=/sitemgr.php?admt=text&utown=$utown&exutown=$exutown&s=edit>Подредактировать и опубликовать</a><br/>\n";
   }
   else {
    echo "<a href=/sitemgr.php?admt=text&utown=$utown&exutown=$exutown&s=unpub>Снять с публикации</a><br/>\n";
   }
   echo "$message\n";
   echo "</p></td></tr>\n";
   echo "</table>\n";
   mysqli_free_result($result);
  }

  if (($submit === "pub") && ($textadmin) && ($obj)) {
   $sql         = "update l_text set dtizm=now() where obj='$obj' ";
   $result      = $dbi->query($sql);
   mysqli_free_result($result);
   $submit      = "";
  }

  if (($submit === "unpub") && ($textadmin) && ($obj)) {
   $sql         = "update l_text set dtizm=00000000000000 where obj='$obj' ";
   $result      = $dbi->query($sql);
   mysqli_free_result($result);
   $submit      = "";
  }

  if (($submit === "post") && ($obj)) {
   $sql         = "select * from l_text where obj='$obj' ";
   $cook        = "";
   $selected    = "";
   $result      = $dbi->query($sql);
   if($myrow    = mysqli_fetch_array($result)) { 
    $oldmessage = $myrow['descr'];
    $cook       = $myrow['cook'];
    $selected   = "1";
   }
   mysqli_free_result($result);
   if (($cook) && ($cook != $ctext) && (!($textadmin)) ) {
    echo "Это описание создал " . authform($cook) . ", а не Вы. Редактирование невозможно.";
   }
   else {
    if (mb_strlen($message) < 10 ) {
     $sql = "delete from l_text where obj='$obj' and cook = '$cook' ";
     $dbi->query($sql);
    }
    else {
     echo "<div align=left class=travel0>$message</div>\n";
     if ($textadmin) {
      $sqladd = "now()";
     }
     else {
      $sqladd = "00000000000000";
     }
     if ($selected) {
      $sql = "update l_text set pubcook='$ctext', descr = '$message', dtizm=$sqladd where obj='$obj' ";
     }
     else {
      $sql = "insert into l_text (cook, obj, descr, dtizm, pubcook) values('$ctext', '$obj', '$message', $sqladd, '$ctext') ";
     }
     $dbi->query($sql);
    }
    $submit = "";
   }
  }
  if (($submit === "edit") && ($obj)) {

   $sql  = "select * from l_text where obj='$obj' ";
   $cook = "";
   $result = $dbi->query($sql);
   if($myrow = mysqli_fetch_array($result)) { 
    $message = $myrow['descr'];
    $cook    = $myrow['cook'];
   }
   mysqli_free_result($result);
   if (($cook) && ($cook != $ctext)) {
    echo "<font color=red>Это описание создал " . authform($cook) . ", а не Вы!!!</font>";
    if (!($textadmin)) {
     $submit = "";
    }
   }
   if ($submit) {
    echo "<form name=l_text action=".$_SERVER["PHP_SELF"]." method=post>";
    echo "<TABLE BORDER=0 cellspacing=2 CELLPADDING=2><tbody class=travell0>\n";
    echo "<TR><TD>\n";
    if ($exutown) {
     list($exname,$exdesc,$exdesc2,$exdesc3) = explode('\|',getexdesc($exutown));
     echo "<b>$exname</b><br>$exdesc<br>\n";
    }
    else {
     echo gettownname($utown) . ":<br>\n";
    }
    echo "</TD></TR>\n";
    echo "<TR><TD>\n";

    echo "<b>Внимание!</b><br>\n";
    echo "Подробное описание - авторский текст, содержащий изложенные в литературной форме подробные сведения о достопримечательности или городе.<br>\n";
    echo "Здесь Вы можете разместить только своё описание, чужие тексты размещать не нужно.<br>\n";
    echo "Подумайте: если у Вас нет склонностей к литературному творчеству, может оказаться гораздо удобнее и правильнее разместить просто ";
    if ($exutown) {
     echo "<a href=/otklik.php/$exutown><b>отзыв</b></a>.<br>\n";
    }
    else {
     echo "отзыв.<br>\n";
    }
    echo "Администрация сайта оставляет за собой право на использование размещённых здесь текстов: редактирование, удаление, предоставление третьим лицам, использование данных в своих текстах и иные действия, соответствующие целям и тематике сайта.<br>\n"; 
    echo "</TD></TR>\n";
    echo "<TR><TD><TEXTAREA name=message rows=30 style=\"width:98vw\"  wrap=none>\n";
    $message = my_unhtml($message);
    $message = str_replace("[br][br]","[br]",$message);
    $message = str_replace("[br]","\n\n",$message);
    echo str_replace("[BR]","\n\n",$message);
    echo "</TEXTAREA></TD></TR>\n";
    echo "<TR><TD align=center><INPUT name=submit type=submit value='Изменить'>\n";
    echo "<INPUT name=s type=hidden value=post>\n";
    echo "<INPUT name=utown   type=hidden value=$utown>\n";
    echo "<INPUT name=exutown type=hidden value=$exutown>\n";
    echo "<INPUT name=admt type=hidden value=text>\n";
    echo "&nbsp;</TD></TR></TABLE></FORM>\n";

   }
  }
 
// форма выбора

  if (!($submit)) {
   echo "<h3>Выбор/поиск объекта:\n</h3>";
   echo "<form name=ltext action=".$_SERVER["PHP_SELF"]." method=post>";
   echo "<INPUT name=s type=hidden value=edit>\n";
   echo "<INPUT name=admt type=hidden value=text>\n";
   echo "<TABLE BORDER=0 cellspacing=2 CELLPADDING=2><tbody class=travell0>\n";
/*
   echo "<TR><td width=220 class=travel1 align=right>";
   echo "Тип:&nbsp;</td><td class=travel1>\n";
   echo "<input TYPE=radio VALUE=t NAME=t";
   if ($objtype == "t") { echo " CHECKED=true"; }
   echo ">Город&nbsp;\n";
   echo "<input TYPE=radio VALUE=e NAME=t";
   if ($objtype == "e") { echo " CHECKED=true"; }
   echo ">Объект&nbsp;\n";
   echo "</TD></TR>\n";
   echo "<TR><td width=220 class=travel1 align=right>id (для редактирования):</b>&nbsp;</TD>\n";
   echo "<TD><INPUT name=i type=text size=20 value=$objid>";
   echo "<input type=button name=ssxxz onclick=SelId('ltext','i','/f_town.php') value='Найти город'>\n";
   echo "<input type=button name=ssxxy onclick=SelId('ltext','i','/f_exci.php') value='Найти объект'>\n";
   echo "</TD></TR>\n";
   echo "<TR><TD>&nbsp;</TD><TD class=gensmall><input type=text name=helpi size=80 style='width:450px; font-size:90%' class=helpline value=''>";
   echo "</TD></TR>";
*/
   echo "<TR><td width=220 class=travel1 align=right>";
   echo "<input type=button name=ssxx  onclick=NewSelId('ltext','utown','/s_town.php') value='Найти (изменить) город >>'></td>\n";
   echo "<TD class=gensmall><INPUT name=utown type=hidden ";
   if ($utown > 0) {
     echo "value=$utown"; 
     $helputown = gettownname($utown);
   }
   else {
     $helputown = "/город не указан/";
   }
   echo ">";
   echo "<input type=text name=helputown size=80 ";
   echo "style='width:450px; font-size:90%' class=helpline value='$helputown'>\n";
   echo "</TD></TR>";
   echo "<TR><td width=220 class=travel1 align=right>";
   echo "<input type=button name=ssx onclick=NewSelExId('ltext','utown','/s_exci.php') value='Найти (изменить) объект >>'></td>\n";
   echo "<TD class=gensmall><INPUT name=exutown type=hidden ";
   if ($exutown > 0) {
     echo "value=$exutown"; 
     $helpexutown = getexname($exutown);
   }
   else {
     $helpexutown = "/объект не указан/";
   }
   echo ">";
   echo "\n";
   echo "<input type=text name=helpexutown size=80 style='width:450px; font-size:90%' class=helpline value='$helpexutown'>";
   echo "</TD></TR>";

   echo "<TR><TD>&nbsp;</TD><TD><INPUT name=submit type=submit value='Перейти к редактированию'>\n";
   echo "</TD></TR>\n";
   echo "</TABLE></FORM>\n";

   echo "<p class=travel1>Редактировать описания:</p>\n";
   echo "<table border=1 width=100% class=travell0><tr>\n";
   if ($textadmin) {
// выбор из неопубликованных

     echo "<td colspan=2><p class=travel5>Неопубликованные описания:</p>\n";
     $cmts = array();
     $sql2    = "select obj from l_text where dtizm=00000000000000 and obj like 't%'";
     $result2 = $dbi->query($sql2);
     if($myrow2 = mysqli_fetch_array($result2)) {
      echo "<p class=travel1>Города:\n";
      do {                   
       $obj   = $myrow2["obj"];
       $key   = (int) substr($obj,1);
       $cmts["$key"] = gettownname($key) . $dtizm;
      } while($myrow2 = mysqli_fetch_array($result2)); 
      asort($cmts);
      reset($cmts);
      while ( list($key,$val)=each($cmts) ) {
       echo "<br><a href=/sitemgr.php?admt=text&utown=$key&s=query>$val</a>\n";
      }
      echo "</p>\n";
     }
     mysqli_free_result($result2);
     $cmts = array();
     $sql2    = "select obj from l_text where dtizm=00000000000000 and obj like 'e%'";
     $result2 = $dbi->query($sql2);
     if($myrow2 = mysqli_fetch_array($result2)) {
      echo "<p class=travel1>Объекты:\n";
      do {
       $obj  = $myrow2["obj"];
       $key  = (int) substr($obj,1);
       $cmts["$key"] = getexname($key);
      } while($myrow2 = mysqli_fetch_array($result2)); 
      asort($cmts);
      reset($cmts);
      while ( list($key,$val)=each($cmts) ) {
       echo "<br><a href=/sitemgr.php?admt=text&exutown=$key&s=query>$val</a>\n";
      }
      echo "</p>\n";
     }
     mysqli_free_result($result2);
     echo "</td></tr><tr>\n";
    
   }


   echo "<td width=50%><p class=travel5>Свои описания:</p></td>\n";
   echo "<td width=50%><p class=travel5>Не хватает описаний:</p></td></tr>\n";
   echo "<td width=50% valign=top>\n";

// выбор из своих

    $cmts = array();
    $sql2    = "select obj,dtizm from l_text where cook='$ctext' and obj like 't%'";
    $result2 = $dbi->query($sql2);
    if($myrow2 = mysqli_fetch_array($result2)) {
     echo "<p class=travel1>Города:\n";
     do {                   
      $dtizm = $myrow2["dtizm"];
      if ($dtizm == "0000-00-00 00:00:00") {
       $dtizm = " [На одобрении]";
      }
      else {
       $dtizm = " [Опубликовано]";
      }
      $obj   = $myrow2["obj"];
      $key   = (int) substr($obj,1);
      $cmts["$key"] = gettownname($key) . $dtizm;
     } while($myrow2 = mysqli_fetch_array($result2)); 
     asort($cmts);
     reset($cmts);
     while ( list($key,$val)=each($cmts) ) {
      echo "<br><a href=/sitemgr.php?admt=text&utown=$key&s=edit>$val</a>\n";
     }
     echo "</p>\n";
    }
    mysqli_free_result($result2);

    $cmts = array();
    $sql2    = "select obj,dtizm from l_text where cook='$ctext' and obj like 'e%'";
    $result2 = $dbi->query($sql2);
    if($myrow2 = mysqli_fetch_array($result2)) {
     echo "<p class=travel1>Объекты:\n";
     do {
      $dtizm = $myrow2["dtizm"];
      if ($dtizm == "0000-00-00 00:00:00") {
       $dtizm = " [На одобрении]";
      }
      else {
       $dtizm = " [Опубликовано]";
      }
      $obj  = $myrow2["obj"];
      $key  = (int) substr($obj,1);
      $cmts["$key"] = getexname($key);
     } while($myrow2 = mysqli_fetch_array($result2)); 
     asort($cmts);
     reset($cmts);
     while ( list($key,$val)=each($cmts) ) {
      echo "<br><a href=/sitemgr.php?admt=text&exutown=$key&s=edit>$val$dtizm</a>\n";
     }
     echo "</p>\n";
    }
    mysqli_free_result($result2);
//
   echo "</td><td width=50% valign=top>\n";
// выбор из популярных
    echo "<p class=travel1>Города:\n";
    $sql1 = "select id from towns where rate > (20 * $highrate) order by name";
    $result1 = $dbi->query($sql1);
    while($myrow1 = mysqli_fetch_array($result1)) {
      $key  = $myrow1["id"];
      $descr = "";
      $sql2 = "select descr from l_text where obj = 't$key'";
      $result2 = $dbi->query($sql2);
      if ($myrow2 = mysqli_fetch_array($result2)) {
       $descr  = $myrow2["descr"];
      }
      mysqli_free_result($result2);
      if (!($descr)) {
       $val = gettownname($key);
       echo "<br><a href=/sitemgr.php?admt=text&utown=$key&s=edit>$val</a>\n";
      }
    } 

    echo "</p><p class=travel1>Объекты:\n";
    $sql1 = "select id from exciting where wrate > (5 * $highrate) and type=1 order by name";
    $result1 = $dbi->query($sql1);
    while($myrow1 = mysqli_fetch_array($result1)) {
      $key  = $myrow1["id"];
      $descr = "";
      $sql2 = "select descr from l_text where obj = 'e$key'";
      $result2 = $dbi->query($sql2);
      if ($myrow2 = mysqli_fetch_array($result2)) {
       $descr  = $myrow2["descr"];
      }
      mysqli_free_result($result2);
      if (!($descr)) {
       $val = getexname($key);
       echo "<br><a href=/sitemgr.php?admt=text&exutown=$key&s=edit>$val</a>\n";
      }
    } 

  }
 }
// вставка обширных комментариев - end

?>
