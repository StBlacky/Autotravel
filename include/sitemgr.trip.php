<?php
// Поездки. Немытыми руками не трогать, гора стрёмной логики inside.
//////////////////////////////////////////////////////////////////////////////

// самодобавление. Пока просто добавляет участника.
// надо - посылать приват организатору и емейл, если эта опция активирована.
$bootstrap=1;
 if ($admt == 'reguser') {
  $it      = (int) mysafestring($_REQUEST["it"]);
  $cntad   = (int) mysafestring($_REQUEST["cntad"]);
  $cntch   = (int) mysafestring($_REQUEST["cntch"]);
  $tripuserdescrip = mysafestring($_REQUEST["userdescrip"]);

  if ($cntad < 1) {
   $chtad = 1;
  }
  if ($cntch < 0) {
   $cntch = 0;
  }
  if ($it > 0) {
   $typeuserit = -2;
   $sql_tu = "select typeuser from trip_user where idtrip=$it and iduser=$forum_user_id";
   $result_tu = $dbi->query($sql_tu);
   if($myrow_tu = mysqli_fetch_array($result_tu)) {
    $typeuserit = $myrow_tu["typeuser"];
//    echo "$sql_tu => $typeuserit";
   }
   mysqli_free_result($result_tu);
   $nameuserit = "";
   $sql_tu = "select name from trip_user where idtrip=$it and name='$forum_user_name'";
   $result_tu = $dbi->query($sql_tu);
//   echo "<br>$sql_tu<br>";
   if($myrow_tu = mysqli_fetch_array($result_tu)) {
    $nameuserit = $myrow_tu["name"];
//    echo "$sql_tu => $nameuserit";
   }
   mysqli_free_result($result_tu);
   if (($typeuserit == -2) && ($nameuserit == "")) {
// если участник уже регистрировался - ничего делать не надо.
    $sql_in = "insert into trip_user 
               (id, idtrip, iduser, name, cntad, cntch, typeuser, descrip)
         values(NULL, $it, $forum_user_id, '$forum_user_name', $cntad, $cntch, '-1', '$tripuserdescrip')";
    $dbi->query($sql_in);
    if (true) {
     $tripname = gettripname($it);

     $sql_in = "select iduser from trip_user where idtrip=$it and typeuser=1";
     //error_log($sql_in);
     $result_in = $dbi->query($sql_in);
     while($myrow_in = mysqli_fetch_array($result_in)) {
      $trip_admin = $myrow_in["iduser"];
//      echo "$trip_admin\n";
      $sql_if = "INSERT INTO bl_privmsgs(
             msg_id,
             message_subject,
             message_text,
             author_id,
             to_address,
             message_time
            )
            VALUES (
            NULL, 
            'Желание попасть в поездку', 
            '$forum_user_name зарегистрировался как желающий попасть в <a href=\"$sitename/ourtrip.php?it=$it&txt=$tripname\">поездку $tripname</a>.<br>$tripuserdescrip',
            $forum_user_id,
            'u_".$trip_admin."',
            unix_timestamp()
            )";
      //error_log($sql_if);
      $dbi->query($sql_if);
//      $sql_if = "update bl_users set user_unread_privmsg = user_unread_privmsg + 1 where
//            userid = $trip_admin";
//      echo "$sql_if\n";
//      $dbi->query($sql_if);
     }
     mysqli_free_result($result_in);


     echo "<p class=\"alert alert-info\">Вы зарегистрированы. Информация направлена организатору.</p>\n";

    }
    else {
      echo "<p class=\"alert alert-danger\">При регистрации возникли ошибки. Сообщите о них на форуме.</p>\n";
    }
   }
   else {
      echo "<p class=\"alert alert-info\">Вы уже зарегистрированы.<br>\n";
      echo "Если Вам надо внести изменения или просто связаться с организатором, используйте \"приват\" на форуме.</p>\n";
   }
   $admt = 'ourtrip';
   $adu = 0; // пойдёт в просмотр информации о поездке
  }
  else {
// такого не бывает, но мало ли.
  }
 }
// самодобавление end

// добавление/изменение участников поездки организатором - begin
// добавление/изменение участников поездки организатором - begin
if ($admt == 'tripuser') {

    $it   = (int) mysafestring($_REQUEST["it"]);
    $uid  = (int) mysafestring($_REQUEST["uid"]);
    $adu  = (int) mysafestring($_REQUEST["adu"]); // 0 – обычное действие, 1 – удалить, 2 – показать форму

    $triptypeuser    = (int) mysafestring($_REQUEST["triptypeuser"]);
    $tripusernameid  = (int) mysafestring($_REQUEST["tripusernameid"]);
    $tripusername    = mysafestring($_REQUEST["tripusername"]);
    $cntad           = (int) mysafestring($_REQUEST["cntad"]);
    $cntch           = (int) mysafestring($_REQUEST["cntch"]);
    $tripuserdescrip = mysafestring($_REQUEST["userdescrip"]);

    // по умолчанию
    $tripcntad       = $cntad;
    $tripcntch       = $cntch;

    $typeuserit = -2;

    // права текущего пользователя на эту поездку
    if ($it > 0) {
        $sql_tu = "SELECT typeuser FROM trip_user WHERE idtrip=$it AND iduser=$forum_user_id";
        $result_tu = $dbi->query($sql_tu);
        if ($row_tu = mysqli_fetch_array($result_tu)) {
            $typeuserit = $row_tu["typeuser"];
        }
        mysqli_free_result($result_tu);
    }

    // ==========================================
    // 1. РЕЖИМ ПОКАЗА ФОРМЫ (adu = 2) — БЕЗ SQL
    // ==========================================
    if ($adu == 2) {

        // форма добавления нового участника
        if ($uid < 0) {
            $uid             = 0;
            $tripusername    = "";
            $tripusernameid  = 0;
            $tripcntad       = 0;
            $tripcntch       = 0;
            $tripuserdescrip = "";
            $triptypeuser    = 0;
        }
        // форма редактирования существующего участника
        elseif ($uid > 0) {
            $sql_u  = "SELECT * FROM trip_user WHERE idtrip = $it AND id = $uid";
            $res_u  = $dbi->query($sql_u);
            if ($row = mysqli_fetch_array($res_u)) {
                $tripusernameid  = $row["iduser"];
                $tripusername    = $row["name"];
                $triptypeuser    = $row["typeuser"];
                $tripcntad       = $row["cntad"];
                $tripcntch       = $row["cntch"];
                $tripuserdescrip = $row["descrip"];
            } else {
                // не нашли участника — просто выходим в просмотр поездки
                $adu = 0;
            }
            mysqli_free_result($res_u);
        }

        // дальше будет блок ourtrip, который, увидев $adu == 1,
        // покажет include("include/sitemgr.tripuser.form.php");
        $admt = 'ourtrip';
        $adu  = 1;

    }
    // ==========================================
    // 2. РЕЖИМ ИЗМЕНЕНИЯ ДАННЫХ (adu = 0 или 1)
    // ==========================================
    else {

        // только организатор или админ могут менять состав
        if ($it > 0 && ( $typeuserit == 1 || preg_match("/\,admin\,/i", $all_rights) )) {

            // нормализуем тип участника
            if ($triptypeuser != 1) {
                $triptypeuser = 0;
            }

            // проверяем/нормализуем id пользователя форума
            if ($tripusernameid > 0) {
                $sql = "SELECT username, user_id 
                        FROM bl_users 
                        WHERE user_id = $tripusernameid AND username = '$tripusername'";
                $result = $dbi->query($sql);
                $tripusernameid = 0;
                if ($row = mysqli_fetch_array($result)) {
                    $tripusernameid = $row["user_id"];
                }
                mysqli_free_result($result);
            } else {
                $tripusernameid = 0;
            }

            $sql_in = "";

            // ---------- ДОБАВЛЕНИЕ НОВОГО ----------
            // POST формы добавления: uid = 0, adu = 0
            if ($uid == 0 && $adu == 0 && ($cntad + $cntch) > 0 && $tripusername != "") {

                $sql_in = "INSERT INTO trip_user
                           (id,idtrip,iduser,name,cntad,cntch,typeuser,descrip)
                           VALUES
                           (NULL,'$it','$tripusernameid','$tripusername',
                            '$cntad','$cntch','$triptypeuser','$tripuserdescrip')";
            }

            // ---------- ИЗМЕНЕНИЕ СУЩЕСТВУЮЩЕГО ----------
            // POST формы редактирования: uid > 0, adu = 0
            elseif ($uid > 0 && $adu == 0) {

                // защита от того, чтобы организатор не снимал сам с себя организаторство
                $sql_tu = "SELECT * FROM trip_user WHERE idtrip=$it AND id=$uid";
                $result_tu = $dbi->query($sql_tu);
                $oldusernameid = 0;
                $oldusername   = "";
                if ($row = mysqli_fetch_array($result_tu)) {
                    $oldusernameid = $row["iduser"];
                    $oldusername   = $row["name"];
                }
                mysqli_free_result($result_tu);

                if ($oldusernameid == $forum_user_id && $typeuserit == 1) {
                    $triptypeuser   = $typeuserit;
                    $tripusername   = $oldusername;
                    $tripusernameid = $oldusernameid;
                }

                $sql_in = "UPDATE trip_user SET
                              iduser   = '$tripusernameid',
                              name     = '$tripusername',
                              cntad    = '$cntad',
                              cntch    = '$cntch',
                              typeuser = '$triptypeuser',
                              descrip  = '$tripuserdescrip'
                           WHERE id = $uid AND idtrip = $it";
            }

            // ---------- УДАЛЕНИЕ ----------
            // ссылка Удалить: uid > 0, adu = 1
            elseif ($uid > 0 && $adu == 1) {
                // организатора удалить нельзя — сначала понизить статус
                $sql_in = "DELETE FROM trip_user 
                           WHERE id = $uid AND idtrip = $it AND typeuser < 1";
            }

            // ---------- ВЫПОЛНЕНИЕ SQL ----------
            if ($sql_in != "") {

                $result_in = $dbi->query($sql_in);

                if ($result_in) {
                    // успешное изменение — возвращаемся в просмотр поездки
                    $admt = 'ourtrip';
                    $adu  = 0;
                } else {
                    echo "<p class='alert alert-danger'>
                            Ошибка при добавлении/изменении участника:<br>
                            {$dbi->errno}: {$dbi->error}
                          </p>";
                    $admt = 'ourtrip';
                    $adu  = 1;   // вернёмся к форме
                }

            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // был POST, но ни одно из условий не сработало — реально что-то не так
                echo "<p class='alert alert-danger'>Непредвиденная ошибка при обработке участника.</p>";
                $admt = 'ourtrip';
                $adu  = 0;
            }
        }
        // если прав нет — просто уходим обратно в просмотр поездки
        else {
            $admt = 'ourtrip';
            $adu  = 0;
        }
    }
}
// добавление/изменение участников поездки организатором - end



//////////////////////////////////////////////////////////////////////////////
// ввод/редактирование поездок - begin
 if ($admt == 'edtrip') { 
  $submitourtrip = $_REQUEST["submitourtrip"];
  $it  = (int) mysafestring($_REQUEST["it"]);
  $helpidforum = "";
  if ($it == 0) {
   $it = -1;                // новая поездка
  }
//  Сперва - обработка, потом формы отображения/ввода/редактирования.

// нам прислали файл 
  $file_tmp_name = "";
  if (isset($_FILES['userfile']) ) {
   $file_tmp_name = $_FILES['userfile']['tmp_name'];
   $file_new_name = $_FILES['userfile']['name'];
   $file_new_name = mysafename($file_new_name);
  }


  $typeuserit = 0;
  if ($it > 0)  {
   $sql_tu = "select typeuser as  typeuser from trip_user where idtrip=$it and iduser=$forum_user_id";
   $result_tu = $dbi->query($sql_tu);
   if($myrow_tu = mysqli_fetch_array($result_tu)) {
    $typeuserit = $myrow_tu["typeuser"];
   }
   mysqli_free_result($result_tu);
  }

// форма для редактирования поездки
  if (($admt == 'edtrip') && ($it > 0) && (!($submitourtrip))) { 
    $sql = "select * from ourtrip where id=$it";
    $result = $dbi->query($sql);
    if($myrow = mysqli_fetch_array($result)) {
     $idtrip = $myrow["id"];
     $datebeg = $myrow["datebeg"];
     $dateend = $myrow["dateend"];
     $datebeg = substr($datebeg,8,2) . "." . substr($datebeg,5,2) . "." . substr($datebeg,0,4);
     $dateend = substr($dateend,8,2) . "." . substr($dateend,5,2) . "." . substr($dateend,0,4);
     $idobj = $myrow["idobj"];
     $txtobj = $myrow["txtobj"];
     $tripdescrip = $myrow["descrip"];
     $idforum = $myrow["idforum"];
    }
  }
  else {
   $tripdescrip = mysafehtml2($_REQUEST["tripdescrip"]);
   $extype   = mysafestring($_REQUEST["extype"]);
   $datebeg  = mysafestring($_REQUEST["datebeg"]);
   $dateend  = mysafestring($_REQUEST["dateend"]);
   $helpexid = mysafestring($_REQUEST["helpexid"]);
   $exid     = (int) mysafestring($_REQUEST["exid"]);
   $idforum  = (int) mysafestring($_REQUEST["idforum"]);
   $txtobj   = mysafestring($_REQUEST["txtobj"]);
   $idobj    = mysafestring($_REQUEST["idobj"]);

   if (($extype != "a") and ($extype != "t") and ($extype != "e")) {
    $extype = "x";
    $exid   = 0;
    $helpexid    = "";
   } else {
    if ($exid == 0) {
    // если не было типа, то и id быть не должно
     $extype = "x";
    }
   }
  }

  if ($datebeg == "") {
    $cdatebeg = "2000-01-01";
  } else {
    $cdatebeg = substr($datebeg,6,4) . "-" . substr($datebeg,3,2) . "-" . substr($datebeg,0,2);
  }
  if ($dateend == "") {
    $cdateend = $cdatebeg;
  } else {
    $cdateend = substr($dateend,6,4) . "-" . substr($dateend,3,2) . "-" . substr($dateend,0,2);
  }

//  был ввод новой поездки 
  if ($it <> 0) {

// либо ввод новой, либо редактирование имеющейся - надо найти ветку форума
   if ($idforum > 0) {
    $fsql = "SELECT t.topic_id as topic_id, p.post_id as post_id,
            t.topic_title as name
            FROM bl_topics t, bl_posts p 
            WHERE (t.forum_id = 8 or t.forum_id = 20) and t.topic_id = $idforum 
            and p.post_id = t.topic_last_post_id";
// and  p.post_time > (unix_timestamp() - 3600*24*30)";
// and topic_time > (unix_timestamp() - 3600*24*30) -- надо переделать на дату последнего сообщения
    $idforum = 0;
    $fresult = $dbi->query($fsql);
    if($myrow = mysqli_fetch_array($fresult)) {
     $idforum = $myrow["topic_id"];
     $helpidforum = $myrow["name"];
    }
    mysqli_free_result($fresult);
   }
   else {
    $idforum = 0;
   }

// проверка - есть ли ветка форума
   if ($idforum == 0) {
//    echo $fsql;
    echo "<p class=\"alert alert-danger\">На форуме 'Ищу попутчика' не найдена указанная тема</p>\n";
    $submitourtrip = '';
   }

// проверка - может к этой ветке форума уже есть поездка
   $newidforum = 0;
   $sql = "SELECT id, descrip
           FROM ourtrip
           WHERE idforum = $idforum and
           idforum >0 and    
           id <> $it";
   $result = $dbi->query($sql);
   if($myrow = mysqli_fetch_array($result)) {
    $newidforum =  $myrow["id"];
   }
   mysqli_free_result($result);

   if ($newidforum <> 0) {
    echo "<p class=\"alert alert-danger\">По указанной теме форума уже организуется <a href=/sitemgr.php?admt=ourtrip&it=$newidforum>поездка</a></p>\n";
    $submitourtrip = '';
   }

// проверка - дата завершения

  }

  if (($it < 0) && ($submitourtrip))  {
    $sql_in = "insert into ourtrip 
    (id,datebeg,dateend,idobj,txtobj,descrip,idforum) values
    (NULL,'$cdatebeg','$cdateend','$idobj','$txtobj','$tripdescrip','$idforum')";
//    echo $sql_in;
    $result_in = $dbi->query($sql_in);
    if ($result_in) {
     echo "<p class=\"alert alert-info\">Поездка успешно добавлена</p>\n";
     $sql_id = "select last_insert_id() as idtrip";
     $result_id = $dbi->query($sql_id);
     if($myrow_id = mysqli_fetch_array($result_id)) {
      $it =  $myrow_id["idtrip"];
      $sql_it = "insert into trip_user
      (id,idtrip,iduser,name,cntad,cntch,typeuser,descrip) values
      (NULL,'$it','$forum_user_id','$forum_user_name ','1','0','1','')";
      $result_it = $dbi->query($sql_it);
      if ($result_it) {
        echo "<h3>Организатор - $forum_user_name</h3>\n";
        if ($file_tmp_name) {
         mkdir("ourtrip/$it");
         chmod("ourtrip/$it",0755);
         move_uploaded_file($file_tmp_name, "ourtrip/$it/$file_new_name");
         chmod("ourtrip/$it/$file_new_name",0644);
        }
        $admt = 'ourtrip'; // теперь пойдёт в просмотр
      } else {
        echo "<p class=\"alert alert-danger\">При добавлении произошла ошибка\n<BR>";
        echo $dbi->errno.": ".$dbi->error."</p>";
      }
      mysqli_free_result($result_it);
     }
     else {
      echo "<p class=\"alert alert-danger\">Не найдена поездка\n<BR>";
      echo $dbi->errno.": ".$dbi->error."</p>";
     }
     mysqli_free_result($result_id);
//     $tripdescrip = '';
//     $idforum =  '';
//     $txtopbj = '';
//     $idobj = '';
//     $datebeg = '';
//     $dateend = '';
    } else {
     echo "<p class=\"alert alert-danger\">При добавлении произошла ошибка\n<BR>";
     echo $dbi->errno.": ".$dbi->error."</p>";
    }
    mysqli_free_result($result_in);
  }
  if (
      ($admt == 'edtrip') && ($it > 0) && ($submitourtrip) && 
      (($typeuserit == 1) || (preg_match("/\,admin\,/i",$all_rights)))
     ) { 
    $sql_upd = "update ourtrip set datebeg='$cdatebeg', dateend='$cdateend',idobj = '$idobj',txtobj = '$txtobj', descrip='$tripdescrip',idforum ='$idforum'  where id = $it";
    $result_upd = $dbi->query($sql_upd);
    if ($result_upd) {
//     mysqli_free_result($result_upd);
     echo "<p class=\"alert alert-info\">Данные успешно изменены</p>\n";

//  удаляем файлы
     $filerms = $_REQUEST["filerm"];
     if (is_array($filerms)) {
       reset ($filermss);
       while (list ($tkey, $tval) = each ($filerms)) {
        if (is_file("ourtrip/$it/$tval")) {
         unlink("ourtrip/$it/$tval");
        }
       }
     }

// сохраняем файл
     if ($file_tmp_name) {
         mkdir("ourtrip/$it");
         chmod("ourtrip/$it",0755);
         move_uploaded_file($file_tmp_name, "ourtrip/$it/$file_new_name");
         chmod("ourtrip/$it/$file_new_name",0644);
     }
     $admt = 'ourtrip'; // теперь пойдёт в просмотр
//     $tripdescrip = '';
//     $idforum =  '';
//     $txtopbj = '';
//     $idobj = '';
//     $datebeg = '';
//     $dateend = '';
    } else {
      echo "<p class=\"alert alert-danger\">При редактировании произошла ошибка\n<BR>";
      echo $dbi->errno.": ".$dbi->error."</p>";
      mysqli_free_result($result_upd);
    }
  }

// сюда надо попадать и при ошибке.

if ($admt == 'edtrip') {

    echo "<h3 class='mb-3'>Ввод (изменение) поездки:</h3>";
    echo "<p class='text-muted mb-4'>Обязательные поля выделены жирным.</p>";

    echo "<form action='" . $_SERVER["PHP_SELF"] . "' method='post' enctype='multipart/form-data' class='container-fluid p-0' style='max-width:750px;'>";

    echo "<input type='hidden' name='admt' value='edtrip'>";
    echo "<input type='hidden' name='it' value='$it'>";

    // ---- ТЕМА Поездки ----
    echo "
        <div class='mb-3'>
            <label class='form-label fw-bold'>Тема поездки</label>
            <input type='text' name='txtobj' class='form-control' value=\"" . htmlspecialchars($txtobj) . "\">
        </div>
    ";

    // ---- ДАТЫ ----
    echo "
        <div class='row'>
            <div class='col-md-6 mb-3'>
                <label class='form-label fw-bold'>Начало поездки</label>
                <input type='text' name='datebeg' class='form-control' onchange='ChDate(this)' value='$datebeg'>
            </div>
            <div class='col-md-6 mb-3'>
                <label class='form-label fw-bold'>Конец поездки</label>
                <input type='text' name='dateend' class='form-control' onchange='ChDate(this)' value='$dateend'>
            </div>
        </div>
    ";

    // ---- ОПИСАНИЕ ----
    $tripdescrip = str_replace("[BR]","[br]",my_unhtml($tripdescrip));
    $tripdescrip = str_replace("[br]","\n",$tripdescrip);

    echo "
        <div class='mb-3'>
            <label class='form-label'>Описание поездки</label>
            <textarea name='tripdescrip' class='form-control' rows='8'>"
                . htmlspecialchars($tripdescrip) .
            "</textarea>
        </div>
    ";

    // ---- ФАЙЛЫ ----
    $filelist = "";
    if (is_dir("ourtrip/$it")) {
        $dlist = @dir("ourtrip/$it");
        while(false !== ($fentry = $dlist->read())) {
            if($fentry[0] == ".") continue;
            if(is_file("ourtrip/$it/$fentry")) {
                $filelist .= "
                    <div class='form-check'>
                        <input class='form-check-input' type='checkbox' name='filerm[]' value='$fentry'>
                        <label class='form-check-label'>$fentry</label>
                    </div>";
            }
        }
        $dlist->close();
    }

    if ($filelist) {
        echo "
            <div class='mb-3'>
                <label class='form-label'>Удалить файлы</label>
                <div class='border rounded p-2'>
                    $filelist
                </div>
            </div>
        ";
    }

    echo "
        <div class='mb-4'>
            <label class='form-label'>Присоединить новый файл</label>
            <input type='file' class='form-control' name='userfile'>
        </div>
    ";

    // ---- ТЕМА на форуме ----
    echo "
        <div class='mb-3'>
            <label class='form-label fw-bold'>Номер темы на форуме</label>
            <div class='input-group'>
                <input type='text' name='idforum' class='form-control' value='" . htmlspecialchars($idforum) . "'>
                <button type='button' class='btn btn-outline-secondary'
                    onclick=\"SelId('ourtrip','idforum','/f_topic.php')\">
                    Найти тему
                </button>
            </div>
        </div>
    ";

    echo "
        <div class='mb-3'>
            <input type='text' name='helpidforum' class='form-control'
                   placeholder='Название темы' value='" . htmlspecialchars($helpidforum) . "'>
        </div>
    ";

    // ---- ПРИВЯЗКА поездки ----
    echo "
        <div class='mb-3'>
            <label class='form-label fw-bold d-block'>Привязка поездки</label>

            <div class='form-check form-check-inline'>
                <input class='form-check-input' type='radio' name='extype' value='a' " . ($extype=='a'?"checked":"") . ">
                <label class='form-check-label'>Область</label>
            </div>

            <div class='form-check form-check-inline'>
                <input class='form-check-input' type='radio' name='extype' value='t' " . ($extype=='t'?"checked":"") . ">
                <label class='form-check-label'>Город</label>
            </div>

            <div class='form-check form-check-inline'>
                <input class='form-check-input' type='radio' name='extype' value='e' " . ($extype=='e'?"checked":"") . ">
                <label class='form-check-label'>Объект</label>
            </div>

            <div class='form-check form-check-inline'>
                <input class='form-check-input' type='radio' name='extype' value='x' " . ($extype=='x'?"checked":"") . ">
                <label class='form-check-label'>Нет привязки</label>
            </div>
        </div>
    ";

    // ---- ID связи ----
    echo "
        <div class='mb-3'>
            <label class='form-label fw-bold'>ID связи</label>
            <div class='input-group'>
                <input type='text' name='exid' class='form-control' value='" . htmlspecialchars($exid) . "'>
                <button type='button' class='btn btn-outline-secondary' onclick=\"SelId('ourtrip','exid','/f_area.php')\">Область</button>
                <button type='button' class='btn btn-outline-secondary' onclick=\"SelId('ourtrip','exid','/f_town.php')\">Город</button>
                <button type='button' class='btn btn-outline-secondary' onclick=\"SelId('ourtrip','exid','/f_exci.php')\">Объект</button>
            </div>
        </div>
    ";

    echo "
        <div class='mb-4'>
            <input type='text' name='helpexid' class='form-control'
                   placeholder='Название связанного объекта'
                   value='" . htmlspecialchars($helpexid) . "'>
        </div>
    ";

    // ---- SUBMIT ----
    echo "
        <div class='mb-5'>
            <button type='submit' name='submitourtrip' class='btn btn-primary'>
                Сохранить
            </button>
        </div>
    ";

    echo "</form>";
}

  
// закончен ввод новой поездки
 }


// ===============================
//    ПОКАЗ ПОЕЗДКИ (ourtrip)
// ===============================
if ($admt == 'ourtrip') {

    $it = (int) mysafestring($_REQUEST["it"]);
    $typeuserit = 0;
    $adu = (int) ($_REQUEST["adu"] ?? 0);
    $uid = (int) ($_REQUEST["uid"] ?? 0);

    // Определяем права пользователя
    if ($it > 0) {
        $sql_tu = "SELECT typeuser FROM trip_user WHERE idtrip=$it AND iduser=$forum_user_id";
        $result_tu = $dbi->query($sql_tu);
        if ($row_tu = mysqli_fetch_array($result_tu)) {
            $typeuserit = $row_tu["typeuser"];
        }
        mysqli_free_result($result_tu);
    }

    // === ПРОСМОТР ПОЕЗДКИ ===
    if ($it > 0) {

        $sql_trip = "
            SELECT descrip AS tripdescrip, datebeg, dateend, txtobj, idforum
            FROM ourtrip WHERE id=$it
        ";
        $res_trip = $dbi->query($sql_trip);

        if ($trip = mysqli_fetch_array($res_trip)) {

            $tripdescrip = $trip["tripdescrip"];
            $datebeg     = $trip["datebeg"];
            $dateend     = $trip["dateend"];
            $txtobj      = $trip["txtobj"];
            $idforum     = $trip["idforum"];

            echo "<h3 class='mb-3'>$txtobj</h3>";

            // ---- Файлы ----
            $filelist = "";
            if (is_dir("ourtrip/$it")) {
                $d = @dir("ourtrip/$it");
                while (false !== ($f = $d->read())) {
                    if ($f[0] == ".") continue;
                    if (is_file("ourtrip/$it/$f")) {
                        $filelist .= "<a href='/ourtrip/$it/$f' class='d-block mb-1'>$f</a>";
                    }
                }
                $d->close();
            }

            // === ДВЕ КОЛОНКИ ===
            echo "<div class='container-fluid'><div class='row'>";

            // ---------- ЛЕВАЯ колонка ----------
            echo "<div class='col-md-6 mb-4'>";
            echo "<table class='table table-sm table-borderless'>
                    <tr><td class='text-end fw-bold' style='width:130px;'>Начало:</td>
                        <td>" . strftime('%d.%m.%Y', strtotime($datebeg)) . "</td></tr>
                    <tr><td class='text-end fw-bold'>Конец:</td>
                        <td>" . strftime('%d.%m.%Y', strtotime($dateend)) . "</td></tr>
                    <tr><td class='text-end fw-bold'>Описание:</td>
                        <td>" . nl2br($tripdescrip) . "</td></tr>";

            if ($filelist) {
                echo "<tr><td class='text-end fw-bold'>Файлы:</td>
                          <td>$filelist</td></tr>";
            }

            echo "</table>";

            echo "<h4 class='mt-4'>
                    <a class='travell5' target='_blank' href='$forumname/viewtopic.php?t=$idforum'>
                        Обсуждение поездки →
                    </a>
                  </h4>";

            echo "</div>"; // левая колонка

            // ---------- ПРАВАЯ колонка ----------
            echo "<div class='col-md-6 mb-4'>";

            echo "<h4>Участники</h4>";

            $sql_u = "
                SELECT id, iduser, name, cntad, cntch, typeuser
                FROM trip_user
                WHERE idtrip=$it
                ORDER BY typeuser DESC, name ASC
            ";
            $res_u = $dbi->query($sql_u);

            $is_admin = ($typeuserit == 1 || preg_match('/\,admin\,/i', $all_rights));

            if ($u = mysqli_fetch_array($res_u)) {

                echo "<div class='table-responsive'>
                        <table class='table table-bordered table-sm align-middle'>
                          <thead class='table-light'>
                            <tr>
                              <th>Участник</th>
                              <th>Взр.</th>
                              <th>Дет.</th>
                              <th>Статус</th>";

                if ($is_admin) echo "<th>Ред.</th><th>Уд.</th>";

                echo "</tr></thead><tbody>";

                $totalad = 0;
                $totalch = 0;

                do {
                    $tuid   = $u["id"];
                    $iduser = $u["iduser"];
                    $name   = $u["name"];
                    $cntad  = $u["cntad"];
                    $cntch  = $u["cntch"];
                    $typeu  = $u["typeuser"];

                    $status = ($typeu == 1 ? "Организатор" :
                              ($typeu == 0 ? "Участник" :
                              ($typeu == -1 ? "Желающий" :
                              ($typeu == -3 ? "Размышляющий" : ""))));

                    if ($iduser > 0) {
                        $name = "<a target='_blank'
                                 href='$forumname/memberlist.php?mode=viewprofile&u=$iduser'>$name</a>";
                    }

                    echo "<tr>
                            <td>$name</td>
                            <td>$cntad</td>
                            <td>$cntch</td>
                            <td>$status</td>";

                    if ($is_admin) {
                        echo "<td><a href='/sitemgr.php?admt=tripuser&it=$it&uid=$tuid&adu=2'>✎</a></td>
                              <td><a href='/sitemgr.php?admt=tripuser&it=$it&uid=$tuid&adu=1'>✖</a></td>";
                    }

                    echo "</tr>";

                    if ($typeu > -1) {
                        $totalad += $cntad;
                        $totalch += $cntch;
                    }

                } while ($u = mysqli_fetch_array($res_u));

                echo "<tr class='fw-bold'>
                        <td>Итого:</td><td>$totalad</td><td>$totalch</td><td></td>";

                if ($is_admin) echo "<td></td><td></td>";

                echo "</tr></tbody></table></div>";

            } else {
                echo "<p>Пока нет участников.</p>";
            }

            echo "</div>"; // правая колонка
            echo "</div></div>"; // row + container

            // ===============================
            //       ФОРМА ПОД ВСЕМ
            // ===============================
            if ($is_admin && $adu == 1) {
                echo "<div class='container-fluid col-12 mt-4'>";
                include("include/sitemgr.tripuser.form.php");
                echo "</div>";
            }

        } else {
            echo "<h3>Поездка не найдена</h3>";
        }
    }
}


?>