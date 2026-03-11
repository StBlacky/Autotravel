<?php
include_once('include/func.php');
include_once('include/head00.php');
if (($forum_user_level>0) or ($forum_user_rank>0)) {
  $fotoman_enable = 1; # сдублировано в inclmgrmenu.php
}
$return = $_REQUEST["ret"];
$returl = "";
if ($return == "otklik_new") {
  $returl = "/otklik.php/";
}
if ($return == "foto") {
  $returl = "/$_fotoalbum/";
}
if (!($returl)) {
 include_once('include/inclmgr.php');
 include_once('include/inclmgrmenu.php');
}

$helputown = "";
$helpexutown = "";

$MAX_SIZE = 15000000;
$FILE_MIMES = array('image/pjpeg','image/jpeg','image/jpg');
$FILE_EXTS  = array('.jpg'); 
$site_name = $_SERVER['HTTP_HOST'];

$album_dir="/home/blacky/data/www/phalbum";

if ($fotoman_enable) {
 $sqladd = "and foto.author like 'forum:". $forum_user_id . ":%'";
 include_once('include/fotoman.php');
 if ($_fotomanager) {
  $sqladd = "";
 }

 $action = $_REQUEST["action"];
 $action = mysafestring($action);
 $showmenu = 0;
 $utown = $_REQUEST["utown"];
 if (!($utown)) {
  $utown = "9999";
  $showmenu = 1;
 }
 $utown = (int) mysafestring($utown);

 $exutown = $_REQUEST["exutown"];
 if (!($exutown)) {
  $exutown = "0";
 }
 $exutown = (int) mysafestring($exutown);
 if ($exutown > 0) {
  if ($return == "foto") {
    $returl = "/$_fotoalbum/ex/$exutown";
  }
  if ($return == "otklik") {
    $returl = "/otklik.php/$exutown";
  }
  if ($return == "otklik_new") {
    $returl = "/get_fotoex.php?ex=$exutown";
  }
 }
 else {
  if ($return == "foto") {
    $returl = "/$_fotoalbum/town/$utown";
  }    
 }
 $date = $_REQUEST["date"];
 if (!($date)) {
  $date = strftime("%Y");
 }
 $date = (int) mysafestring($date);

 $ufile = $_REQUEST["ufile"];
 $ufile = mysafestring($ufile);

 $mode = $_REQUEST["mode"];

 $mode = (int)mysafestring($mode);
 if (($utown == 9999) and (!($exutown)))  {
  $mode = 0;
 }

 // Помещать в видимый индекс первые 19 фотографий одного автора имеет право сам автор.
 if (!($_fotomanager)) {
  if ($exutown) {
   if ($mode > 0) {
    $mode = 1;
   }
   $cfile = 0;
   $sql = "select count(*) as xfile from foto where exciting=$exutown and mode>0 and author='".$ctext."'";
   $result = $dbi->query($sql);
   if($myrow = mysqli_fetch_array($result)) {
    $cfile   = $myrow["xfile"];
   }
   if ($cfile > $MAXFOTOS) {
    error_log(" fotos-err ".$cfile." by ".$ctext." ex=".$exutown." >".$MAXFOTOS);
    $mode = 0;
   }
  }
  else {
   if ($utown) {
    if ($mode > 0) {
     $mode = 1;
    }
    $cfile = 0;
    $sql = "select count(*) as xfile from foto where town=$utown and exciting=0 and mode>0 and author='".$ctext."'";
    $result = $dbi->query($sql);
    if($myrow = mysqli_fetch_array($result)) {
     $cfile   = $myrow["xfile"];
    }
    if ($cfile > $MAXFOTOS) {
     $mode = 0;
     error_log(" fotos-err ".$cfile." by ".$ctext." town=".$utown." >".$MAXFOTOS);
    }
   }
   else {
    $mode = 0;  
   }
  }  
 }

 $comment = $_REQUEST["comment"];
 $comment = mysafestring($comment);

 // нам прислали файл 

 $message = "";

 if ((isset($_FILES['userfile']))&&(!(empty($_FILES['userfile']['tmp_name'])))) {
   $ufile = "";
   $temp_name = $_FILES['userfile']['tmp_name'];
   $isize = getimagesize($temp_name);
   if ( $_FILES['userfile']['size'] > $MAX_SIZE) {
     $message = "Размер файла превышает ".$MAX_SIZE." байт. Уменьшите его.";
     if ($return != "otklik_new") {
       $returl = "";
     }  
     unlink($temp_name);
   }
   else {
    if ($isize[2] != 2) {
     $message = "Извините, тип файла - не картинка в формате JPEG.";
     if ($return != "otklik_new") {
       $returl = "";
     }
     unlink($temp_name);
    }
    else {
     $sql = "select max(file) as xfile from foto";
     $result = $dbi->query($sql);
     if($myrow = mysqli_fetch_array($result)) {
       $xfile   = $myrow["xfile"];
       list($album,$fname) = explode("/",$xfile);
       $album = (int)$album;
       $fname = (int)$fname;
       if ($fname > 198) {
        $album++;
        $fname = 100;
       }
       else {
        $fname++;
       }
       $ufile = "$album/$fname";
       $upload_dir = "$album_dir/$album";
       if (!is_dir($upload_dir)) {
         if (!mkdir($upload_dir))
          die ("upload_files directory doesn't exist and creation failed");
         if (!chmod($upload_dir,0775))
          die ("change permission to 775 failed.");
       }
     }
     if ($ufile) {
      $fn  = "$album_dir/$ufile" . ".jpg";
      $ftn = $album_dir."/".$ufile."-s.jpg";
      if (is_file($fn)) {
       if (is_file($ftn)) {
        $message = "Неизвестная ошибка $ufile";
        if ($return != "otklik_new") {
          $returl = "";
        }  
        $ufile = "";
       }
       else {
        unlink($fn);
        if (is_file($fn)) {
         $message = "Неизвестная ошибка $ufile";
         if ($return != "otklik_new") {
           $returl = "";
         }  
         $ufile = "";
        }
       }
      }
      $ftn   = $album_dir."/".$ufile."-s.jpg";
     }
     if ($ufile) {
      if (!($message)) {
        // запись в таблицу foto
        $sql = "insert into foto (file,date,town,exciting,comment,mode,sale,author,type)
           values('$ufile',$date,$utown,$exutown,'$comment','$mode','','$ctext',0)";
        $result = $dbi->query($sql);
        $message = myjpegresize($temp_name, $fn, 1200, 1);
      }
      if (!($message)) {
        $ihash = hash_file('md5', $fn);
        // проверить нет ли такого же файла - ?
        $ppfile = '';
        $sql = "select file from foto where author='".$ctext."' and hash='".$ihash."'";
        $result = $dbi->query($sql);
        if($myrow = mysqli_fetch_array($result)) {
          $ppfile = $myrow["file"];
          if ($ppfile) {
            $message = 'Вы уже раньше загружали <a href="/phalbum/'.$ppfile.'.jpg" target="_blank">эту же самую картинку</a>';  
          }
        }
      }
      if (!($message)) {
        //unlink($temp_name);
        $message = myjpegresize($fn, $ftn, $FOTOSM, 0);
      }
      if (!($message)) {    
        $message = "Файл загружен.";
        error_log(" fotos-ok ".$ufile." by ".$ctext." ex=".$exutown." mode=".$mode);
        try {
          $isize = getimagesize($fn);
          $sql = "update foto set hash='".$ihash."', wi=".$isize[0].', hi='.$isize[1]." where file='".$ufile."'";
          error_log($sql);
          $dbi->query($sql);
        }
        catch (Exception $ex) {
          error_log($ex->getMessage());  
        }
      }
      else {
        $sql = "delete from foto where file='$ufile' and author='$ctext'";
        $result = $dbi->query($sql);
        $message = "Файл не удалось загрузить!!! ".$message;
        if ($return != "otklik_new") {
          $returl = "";
        }
      }
      $ufile  = "";
     }
     else {
      $message .= "Файл НЕ загружен!!!";
      $ufile = "";
      if ($return != "otklik_new") {
        $returl = "";
      }
     }
     unlink($temp_name);
    }
   }
 }

// работаем с одним файлом
 if (($returl)) {
   #Header( "HTTP/1.1 307 Temporary Redirect" );
   if ($return != "otklik_new") {
     Header( "Location: $sitename".$returl ); 
   }
   else {
     Header( "Location: $sitename".$returl."&message=".urlencode($message) ); 
   }
   exit;
 }
 
 if ($ufile) {
  $oldexciting= 0;
  $oldtown    = 0;
  $oldcomment = "";
  $oldauthor = "";
  $oldmode   =  0;
  $olddate   =  0;
  $sql = "select file, author, comment, exciting, town, mode, date from foto
         where file = '$ufile' $sqladd";
  $ufile = "";
  $result = $dbi->query($sql);
  if($myrow = mysqli_fetch_array($result)) {
    $ufile   = $myrow["file"];
    $oldexciting= $myrow["exciting"];
    $oldtown   = $myrow["town"];
    $oldcomment= $myrow["comment"];
    $oldmode   = $myrow["mode"];
    $olddate   = $myrow["date"];
    $oldauthor = $myrow["author"];
   }
   mysqli_free_result($result);
 }

// удаление
 if (($ufile) and ($action == "delete")) {
  $message = "Файл не удален";
  if ($oldmode < 2) {
   if (($_fotomanager) or ($oldauthor === $ctext)) {
    $sql = "delete from foto where file = '$ufile'";
    $result = $dbi->query($sql);
    $fn = "$album_dir/$ufile" . ".jpg";
    // $fp = $_SERVER["DOCUMENT_ROOT"] . "/" . $fn;
    unlink($fn);
    $fn = "$album_dir/$ufile" . "-s.jpg";
    // $fp = $_SERVER["DOCUMENT_ROOT"] . "/" . $fn;
    unlink($fn);
    $ufile = "";
    $message = "";
   }
  }
//  mysqli_free_result($result);
  $action = "";
 }

// надпечатка на имеющийся (!) файл
 if (($ufile) and ($action == "caption")) {
  if ($_fotomanager) {
   if ($ufile) {
    $fn   = $album_dir."/".$ufile.".jpg";
    $ftn   = $album_dir."/".$ufile."-s.jpg";
    $message = myjpegresize($fn, $fn, 1200, 1);
    if (!($message)) {
     //unlink($temp_name);
     $message = myjpegresize($fn, $ftn, 360, 0);
    }
   }
  }
 }
 
// добавляем|удаляем из видимых по объекту
 if (($ufile) and ($action == "newmode")) {
  if ($_fotomanager) {
    if ($mode > 0 ) {
     $mode = 2;
     $message = "Добавлен к индексу";
    }
    else {
     $mode = 0;
     $message = "Удален из индекса";
    }
    $sql = "update foto set mode=$mode where file = '$ufile'";
    error_log(" fotos-ok ".$ufile." mode->".$mode);
    $result = $dbi->query($sql);
  }
//  mysqli_free_result($result);
  $action = "";
  echo $message;
  // exit;
 }


// изменение
 if (($ufile) and ($action)) {
    if (($oldmode < 2) or ($_fotomanager)) { 
     $sql = "update foto set date = $date,
           town = $utown, exciting = $exutown,
           mode = $mode, comment = '$comment'
           where file = '$ufile' $sqladd";
     error_log(" fotos-ok ".$ufile." mode->".$mode);
    }
    else {
     $sql = "update foto set
           comment = '$comment'
           where file = '$ufile' $sqladd";
    }
    $result = $dbi->query($sql);
//    mysqli_free_result($result);
    $ufile = "";
    $action = "";
 }

// не изменяли-удаляли, либо была ошибка

 if ($ufile) {
  $comment  = "";
  $exciting = 0;
  $town     = 0;
  $author   = $ctext;
  $mode     = 0;
  $date     = 0;
  $sql = "select file, author, comment, exciting, town, mode, date from foto
         where file = '$ufile' $sqladd";
  $result = $dbi->query($sql);
  $ufile = "";
  if($myrow = mysqli_fetch_array($result)) {
    $ufile   = $myrow["file"];
    $exciting= $myrow["exciting"];
    $utown   = $myrow["town"];
    $comment= $myrow["comment"];
    $mode   = $myrow["mode"];
    $date   = $myrow["date"];
  }
  else {
    $comment  = "";
  }
  mysqli_free_result($result);
 }

 if ($ufile) {
  $fn = "phalbum/$ufile" . "-s.jpg";
  echo "<div align=center><img border=0 src=/$fn></div><br>\n";
  echo "<form name=foto method=post action=fotoman.php>\n";
  echo "<input type=hidden name=ufile value='$ufile'>\n";
  echo "<table border = 0 width=100%><tbody class=travell0>\n";
// редактирование - только mode < 2
  if (($mode < 2) or ($_fotomanager)) {
   echo "<TR><td width=220 class=travel1 align=right>";
   echo "<input type=button name=ssxx  onclick=NewSelId('foto','utown','/s_town.php') value='Найти (изменить) город >>'></td>\n";
   echo "<TD class=gensmall><INPUT name=utown type=hidden ";
   if (!($utown == 9999)) {
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
   echo "<input type=button name=ssx onclick=NewSelExId('foto','utown','/s_exci.php') value='Найти (изменить) объект >>'></td>\n";
   echo "<TD class=gensmall><INPUT name=exutown type=hidden ";
   if ($exciting > 0) {
     echo "value=$exciting"; 
     $helpexutown = getexname($exciting);
   }
   else {
     $helpexutown = "/объект не указан/";
   }
   echo ">";
   echo "\n";
   echo "<input type=text name=helpexutown size=80 style='width:450px; font-size:90%' class=helpline value='$helpexutown'>";
   echo "</TD></TR>";
   echo "<TR><TD width=220 class=travel1 align=right>В индексах:</TD><TD>";
   if ($_fotomanager) {
    echo "<input type=radio name=mode value=21 ";
    if ($mode > 20) {
     echo "checked=true";
    }
    echo ">По городу<br>\n";
    echo "<input type=radio name=mode value=11 ";
    if (($mode < 21) and ($mode > 10)) {
     echo "checked=true";
    }
    echo ">По объекту<br>\n";
    echo "<input type=radio name=mode value=2 ";
    if ($mode == 2) {
     echo "checked=true";
    }
    echo ">Участвует<br>\n";
   }
   else {
    echo "<input type=radio name=mode value=1 ";
    if ($mode > 0) {
     echo "checked=true";
    }
    echo ">Участвует<br>\n";
   }
   echo "<input type=radio name=mode value=0 ";
   if ($mode < 1) {
    echo "checked=true";
   }
   echo ">Не участвует<br>\n";
   echo "</TD></TR>";
   echo "<TR><TD width=220 class=travel1 align=right>Год:</TD><TD><input type=text name=date value ='$date' size=6>";
   echo "</TD></TR>";
  }
  else {
   echo "<input type=hidden name=utown value='$utown'>\n";
   echo "<input type=hidden name=mode value='$mode'>\n";
   echo "<input type=hidden name=date value='$date'>\n";
   echo "<input type=hidden name=exutown value='$exciting'>\n";
  }
  echo "<TR><TD width=220 class=travel1 align=right>Описание:</TD><TD><input type=text name=comment value ='$comment' size=80>";
  echo "</TD></TR>";
  echo "<TR><TD>&nbsp;</TD><TD><INPUT name=action type=submit value='Изменить'>\n";
  echo "</form>\n";
  echo "<a href=/fotoman.php?utown=$utown><input type=button value=Отмена></a>\n";
  echo "</TD></TR></table>";
 }
// конец работы с одним файлом.


// список файлов по городу
 $displayform = 0;

 if (!($ufile)) {

  if (!($_fotomanager)) {
   $sql = "select count(file) as cfile from foto where mode < 1 $sqladd"; // много мусорных фоток
   $result = $dbi->query($sql);
   if($myrow = mysqli_fetch_array($result)) {
     $cfile   = $myrow["cfile"];
     if ($cfile < 3000) {
      $displayform = 1;
     }
   }
   mysqli_free_result($result);
  }
  else {
   $displayform = 1;
  }
  if (!($utown == 9999)) {
   $tname = gettownname($utown);
   echo "<h3>Фотографии по городу $tname. Ввод и редактирование.</h3>\n";
  }
  else {
   echo "<h3>Фотографии. Ввод и редактирование.</h3>\n";
?>
  <h3>Ввод/редактирование фотографий</h3>
  <p class=travell6>Принимаются файлы-картинки, размером не более 7 Mбайт,
  в формате jpeg.</p>
  <p class=travell6>
  При размещении фотографии Вы должны указать город и, по возможности, объект, который изображен на снимке.<br>
  Фотографии, для которых не определен город (или объект), а также невостребованные фотографии будут удаляться по мере технической необходимости в расчистке свободного места.
  </p>
  <?php 
  if ($forum_user_fotos < 5) {
  ?>
  <p class=travell6>
  <b>Внимание.</b> Напоминаю, что в соответствии с существующей с самого начала
  политикой размещения фотографий в этом альбоме, использование опубликованных
  фотографий (в том фотографическом качестве, в котором они размещены на сайте)
  разрешается безвозмездно, в любом виде и контексте при сохранении
  ссылки на сайт autotravel.ru (autotravel.org.ru). Оригиналы Ваших снимков остаются у Вас
  и Вы вольны ими распоряжаться самостоятельно.<br>
  Администрация сайта оставляет за собой право на использование размещённых здесь 
  фотографий: кадрирование, поворот, ретушь, фотовывод, тиражирование, удаление, надпечатка текста,
  предоставление третьим лицам и иные действия.<br>
  Полный текст <a target="_blank" href="/phalbum.php">описания фотоархива</a>.
  </p>
  <?php
  }
  ?>
<?php
  }

  if ($message) {
   echo "<h3><font color=red>$message</font></h3>\n";
  }
  if ($displayform) {
// форма ввода новой фотографии. Почти повторяет форму редактирования.
   echo "<form name=foto method=post enctype=\"multipart/form-data\" action=fotoman.php>\n";
   echo "<table border = 0 width=100%><tbody class=travell0>\n";
   echo "<tr><td width=220 class=travel1 align=right>Файл : </td>\n";
   echo "<td><input type=\"file\" name=\"userfile\" size=60></td></tr>\n";
   echo "<TR><td width=220 class=travel1 align=right>";
   echo "<input type=button name=ssxx  onclick=NewSelId('foto','utown','/s_town.php') value='Найти (изменить) город >>'></td>\n";
   echo "<TD class=gensmall><INPUT name=utown type=hidden ";
   if (!($utown == 9999)) {
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
   echo "<input type=button name=ssx onclick=NewSelExId('foto','utown','/s_exci.php') value='Найти (изменить) объект >>'></td>\n";
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
   echo "<TR><TD width=220 class=travel1 align=right>В индексах:</TD><TD>";
   if ($_fotomanager) {
    echo "<input type=radio name=mode value=21>По городу<br>\n";
    echo "<input type=radio name=mode value=11>По объекту<br>\n";
    echo "<input type=radio name=mode value=2 checked=true>Участвует<br>\n";
    echo "<input type=radio name=mode value=0>Не участвует<br>\n";
   }
   else {
    echo "<input type=radio name=mode value=1 checked=true>Участвует<br>\n";
    echo "<input type=radio name=mode value=0>Не участвует<br>\n";
   }
   echo "</TD></TR>";
   echo "<TR><TD width=220 class=travel1 align=right>Год:</TD><TD><input type=text name=date value ='$date' size=6>";
   echo "</TD></TR>";
   echo "<TR><TD width=220 class=travel1 align=right>Описание:</TD><TD><input type=text name=comment size=80>";
   echo "</TD></TR>";
   echo "<TR><TD>&nbsp;</TD><TD><INPUT name=action type=submit value='Загрузить на сайт >>'>\n";
   echo "</TD></TR></table>";
   echo "</form>\n";
  }

  if (!($showmenu)) {
   // фото по городу
   echo "<table width=100% border=1 borderwidth=1 cellpadding=3>\n<tbody class=travell0>\n";
   $sql = "select file, author, comment, exciting, town, mode from foto
         where town = $utown $sqladd order by file desc";

   $result = $dbi->query($sql);
   while($myrow = mysqli_fetch_array($result)) {
    $file   = $myrow["file"];
    $exciting= $myrow["exciting"];
    $town   = $myrow["town"];
    $comment= $myrow["comment"];
    $author = $myrow["author"];
    $mode   = $myrow["mode"];

    echo "<tr><td align=center><a href=/fotoman.php?utown=$utown&ufile=$file><img border=0 src=/phalbum/". $file . "-s.jpg></a></td>\n";
    echo "<td align=left>";
    echo "<a class=\"tb tz\" href=/fotoman.php?utown=$utown&ufile=$file>РЕДАКТИРОВАТЬ</a><br>\n";
    echo "<a class=\"tb tz\" target=_blank href=/phalbum.php/$file>ПРЕДПРОСМОТР</a><br>\n";
    echo "Линк для вставки в отчет: " .fotolink($file). "<br>\n";
    if ($_fotomanager) {
     echo "Автор: $author<br>\n";
    }
    if ($exciting > 0) {
     list($town,$tnname,$exname) = explode("\t",gettownex($exciting));
     echo "Объект: $exname<br>\n";
     echo "Город:  $tnname<br>\n";
    }
    else {
     if ($town > 0) {
      $tnname = gettownname($town);
      echo "Город:  $tnname<br>\n";
     }
    }
    if ($mode > 20) {
     echo "Участвует в индексах и отображении города<br>\n";
    }
    elseif ($mode > 10) {
     echo "Участвует в индексах и отображении объекта<br>\n";
    }
    elseif ($mode > 0) {
     echo "Участвует в индексах<br>\n";
    }
    else {
     echo "Не участвует в индексах<br>\n";
    }
    if ($comment) {
     echo "Комментарий: $comment<br>\n";
    }
    if ($mode < 2) {
     echo "<a class=travell5o href=/fotoman.php?utown=$utown&ufile=$file&action=delete>УДАЛИТЬ из фотоархива НАВСЕГДА</a>\n";
    }
    if (($_fotomanager) and (($mode > 0))) {
     echo "<br><br><a target=_blank class=\"tb tz\" href=/fotoman.php?utown=$utown&ufile=$file&action=newmode&mode=0>УБРАТЬ из индекса</a>\n";
     //echo "<br><br><a target=_blank class=travell5m href=/fotoman.php?utown=$utown&ufile=$file&action=caption>НАДПЕЧАТКА</a>\n";
    }
    if (($_fotomanager) and (($mode == 0))) {
     echo "<br><br><a target=_blank class=\"tb tz\" href=/fotoman.php?utown=$utown&ufile=$file&action=newmode&mode=1>ДОБАВИТЬ к индексу</a>\n";
     //echo "<br><br><a target=_blank class=travell5m href=/fotoman.php?utown=$utown&ufile=$file&action=caption>НАДПЕЧАТКА</a>\n";
    }
    echo "</td></tr>\n";

   }
   mysqli_free_result($result);
   echo "</table>\n";
  }
  else {
// Надо показать меню городов
   print "<h3><a href=/fotoman.php?utown=9999>Фото без указания города</a></h3>\n";
   echo "<h3>";
   echo "Список фотографий по городам:\n";
   echo "</h3>\n";
   $sql= "SELECT foto.town AS utown, count( foto.file ) AS cfile, t.name AS tname
         FROM foto, towns t
         WHERE foto.town <> 9999 $sqladd
         AND t.id = foto.town
         GROUP BY foto.town ORDER BY t.name";
   $result = $dbi->query($sql);
//   echo $sql;
   while ($myrow = mysqli_fetch_array($result)) {
    $utown = $myrow["utown"];
    $tname = $myrow["tname"];
    $cfile = $myrow["cfile"];
    print "<h3><a href=/fotoman.php?utown=$utown>$tname ($cfile фотографий)</a></h3>\n";
   }
   mysqli_free_result($result);
// конец меню городов
  }

 }
 echo "</div>\n";

}

?>
