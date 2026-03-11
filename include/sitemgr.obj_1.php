<?php
//////////////////////////////////////////////////////////////////////////////

// полномочия по (!!! новым !!!) объектам - begin
 if (($exciting_enable) && ($admt == 'exciting')) {
// подготовка текстов
  $extext = mysafehtml2($_REQUEST["extext"],'<a>');
  $extaddr = mysafestring($_REQUEST["extaddr"]);
  $extwork = mysafestring($_REQUEST["extwork"]);
  $townid = (int) mysafestring($_REQUEST["townid"]);
  $helpexcid = mysafestring($_REQUEST["helpexcid"]);
  $helptownid = mysafestring($_REQUEST["helptownid"]);
  $egrw = mysafestring($_REQUEST["egrw"]);
  $ngrw = mysafestring($_REQUEST["ngrw"]);
  $exact = (int) mysafestring($_REQUEST["exact"]);
  $submitexc = $_REQUEST["submitexc"];
  $submitexn = $_REQUEST["submitexn"];
  $extype = (int)mysafestring($_REQUEST["extype"]);
  
  $booking = $_REQUEST["booking"];
  $booking = booklink($booking);
  $emoderated = (int) $_REQUEST["emoderated"];
  if ($emoderated == "100") {
   $emoderated = 1;
  }
  else {
   $emoderated = 0;
  }
  if (preg_match("/\,admin\,/i",$all_rights)) {
    $ffile = foto_find(mysafestring($_REQUEST["ffile"]));
  }
  else {
    $ffile = '';
  }
  $ngr = texttodegrees($ngrw);
  $egr = texttodegrees($egrw);
  $ngrw = degreestotext($ngr);
  $egrw = degreestotext($egr);
  if ($exact == "100") {
   $exact = 1;
  }
  else {
   $exact = 0;
  }
  $typex=array();
  $newadds = $_REQUEST["typex"];
  if (is_array($newadds)) {
    reset ($newadds);
    while (list ($key, $val) = each ($newadds)) {
     $typex[] = mysafestring($val);
    }
  }
  else {
    $typex[] = mysafestring($newadds);
  }
  $typexs=join($typex,',');

  $typex6=array();
  $newadds = $_REQUEST["typex6"];
  if (is_array($newadds)) {
    reset ($newadds);
    while (list ($key, $val) = each ($newadds)) {
     $typex6[] = mysafestring($val);
    }
  }
  else {
    $typex6[] = mysafestring($newadds);
  }
  $typexs6=join($typex6,',');

  $extype_txt="";
  $sql="select id,name from type order by srtfld asc";
  $result = $dbi->query($sql);
  if($myrow = mysqli_fetch_array($result)) {
   do {
    $tid = $myrow["id"];
    $tname = $myrow["name"];
    if ($extype == $tid) {
      $extype_txt = $extype_txt."<input type=radio name=extype value=$tid CHECKED";
      if ($tid == 1) {
        $extype_txt .= " onclick='ChHide(\"extype6\"); ChOpen(\"extype1\")' ";
      }
      elseif ($tid == 6) {
        $extype_txt .= " onclick='ChHide(\"extype1\"); ChOpen(\"extype6\")' ";
      }
      else {
        $extype_txt .= " onclick='ChHide(\"extype1\"); ChHide(\"extype6\")' ";
      }
      $extype_txt .= ">$tname &nbsp;";
    }
    else {
      $extype_txt = $extype_txt."<input type=radio name=extype value=$tid";
      if ($tid == 1) {
        $extype_txt .= " onclick='ChHide(\"extype6\"); ChOpen(\"extype1\")' ";
      }
      elseif ($tid == 6) {
        $extype_txt .= " onclick='ChHide(\"extype1\"); ChOpen(\"extype6\")' ";
      }
      else {
        $extype_txt .= " onclick='ChHide(\"extype1\"); ChHide(\"extype6\")' ";
      }
      $extype_txt .= ">$tname &nbsp;";
    }
   } while($myrow = mysqli_fetch_array($result));
  }
  mysqli_free_result($result);


  echo  "<div class=\"col-md-10 text-left\" style=\" padding: 1em;\"><h2>Ввод объекта:\n</h2>";
  if ($submitexc) {
   $sql="select id, name, hide from towns where id=$townid";
   $townid = 0;
   $townhide = 0;
   $helptownid = "";
   $result = $dbi->query($sql);
   if($myrow = mysqli_fetch_array($result)) {
      $townid = (int) $myrow["id"];
      $townhide = $myrow["hide"];
      $helptownid = $myrow["name"];
   }
   mysqli_free_result($result);
  }
// если данные добавляются
  if ($submitexc) {
   if (($helpexcid != "") and ($extype != 0) and ($townid != 0)) {
    if ((($extype == 1) and ($typexs !="")) or (($extype == 6) and ($typexs6 !="")) or (($extype != 1) and ($extype != 6))) {
     $kind = "";
     if ($extype == 1) {
       $kind = $typexs;
     }
     if ($extype == 6) {
       $kind = $typexs6;
     }
     $newtext = $extext;
     if ($extaddr != "") {
      if ($newtext) {
       $newtext = $newtext . "<br><b>Адрес: </b>$extaddr";
      }
      else {
       $newtext = "<b>Адрес: </b>$extaddr";
      }
     }
     if ($extwork != "") {
      if ($newtext) {
       $newtext = $newtext . "<br><b>Режим работы: </b>$extwork";
      }
      else {
       $newtext = "<b>Режим работы: </b>$extwork";
      }
     }
     $areaid = 0;
     $sql_ar = "select area from towns where id=$townid";
     $result_ar = $dbi->query($sql_ar);
     if ($myrow_ar = mysqli_fetch_array($result_ar)) {
      $areaid = $myrow_ar["area"];
     }
     $sql_in = "insert into exciting
                 (id,town, name, descrip, type, kind, Nshirota, Edolgota, area, booking, emoderated, exact, cook, cooked, ffile) values
                 (0,'$townid','$helpexcid','$newtext','$extype','$kind',
                 '$ngr','$egr',$areaid,'$booking', $emoderated, $exact, '$ctext','$ctext','$ffile')";
     $result_in = $dbi->query($sql_in);
     if ($result_in) {
      $ex_id = mysqli_insert_id($dbi);
      //mysqli_free_result($result_in);
      if ($extype == 1) {
       extypes_upd($ex_id,$townid,$areaid,$extype,$typex);
      }
      elseif ($extype == 6) {
       extypes_upd($ex_id,$townid,$areaid,$extype,$typex6);
      }
      else {
       extypes_upd($ex_id,$townid,$areaid,$extype,Array());
      }
      if ($ffile) {
        $sql_uf = "update foto set exciting = $ex_id, town = $townid where file='$ffile'";  
        $dbi->query($sql_uf);
      }
      if (($townhide != 0) and ($townid != 0)) {
// добавляем примечательность к городу, у которого hide <>0
       if ($townhide == 1) {
        $sql_in = "update towns set hide = 2 where id = $townid";
        $dbi->query($sql_in);
       }
       else {
        $sql_in = "update towns set hide = 0 where id = $townid";
        $dbi->query($sql_in);
       }
      }
      echo "<h3>Объект успешно добавлен</h3>\n";
      // посыл в телеграм
      $helpexcid  = "";
      $extext   = "";
      $extaddr  = "";
      $extwork  = "";
      $ngrw     = "";
      $egrw     = "";
      $ffile    = "";
      $typex    = array();
      $typex6   = array();
      $typexs   = "";
      $typexs6  = "";
      $parentobject = "";
      $childobject = "";
     }
     else {
      echo "<h3>При добавлении произошла ошибка\n<BR>";
      echo $dbi->errno.": ".$dbi->error." <br>$sql_in</h3>";
     }
     //mysqli_free_result($result_in);
    }
    else {
      echo "<h3>Ошибка\n<BR>";
      echo "не указаны обязательные признаки объекта</h3>";
    }
   }
   else {
     echo "<h3>Ошибка\n<BR>";
     echo "не указан тип объекта или город</h3>";
   }
  }
  else {
//   echo "!!!";
  }

// только тут, иначе после добавления залетают "галки" в тип примечательности

  $typex_txt=array();
  $typex_txt = gettypextxt('0%',$typex,'typex[]');
  $typex6_txt=array();
  $typex6_txt = gettypextxt('6%',$typex6,'typex6[]');

// если даже было добавление/исправление, всё равно форму ввода показать надо.
  echo "<form name=exciting action=".$_SERVER["PHP_SELF"]." method=post >\n";
  echo "<INPUT name=admt type=hidden value='exciting'>\n";
  
  
  echo "<div class=\"row \"><div class=\"col-md-8\"><h4>Наименование</h4><INPUT name=helpexcid type=text size=80 value='$helpexcid'></div></div>\n";
  
  echo "<div class=\"row \"><div class=\"col-md-6\"><div class=\"col-md-12\"><h4>id города</h4><INPUT name=townid type=text size=20 ";
  if ($townid > 0) { echo "value=$townid"; }
  echo ">";
  echo "<input type=button name=ssxxx onclick=SelId('exciting','townid','/f_town.php') value='Найти по имени'></div>";
  echo "<div class=\"col-md-12\"class=gensmall><input type=text name=helptownid size=80 style='width:450px; font-size:90%' class=helpline value='$helptownid'></div></div>";
  
  echo "<div class=\"col-md-6\"><h4>Координаты (или пусто)</h4> <b>N</b> ";
  if ($exact == 1) {
    echo "$ngrw<input type=hidden name=ngrw value='$ngrw'>&nbsp;&nbsp;<b>E</b> $egrw<input type=hidden name=egrw value='$egrw'>\n";
  }
  else {
    echo "<input type=text name=ngrw value='$ngrw'>&nbsp;&nbsp;<b>E</b> <input type=text name=egrw value='$egrw'>\n";
  }
  echo "<input type=checkbox name=exact value=100 ";
  if ($exact == 1) { echo " CHECKED ";}
  echo ">Координаты проверены</div></div>\n";
  
  echo "<div class=\"row \"><div class=\"col-md-12\"><h4>Описание</h4><TEXTAREA name=extext rows=10 style=\"width:100%\" wrap=none>\n </TEXTAREA>\n";
  if ($extext) {
   echo "\n<script type='text/javascript'>\n";
   echo "<!--\n";
   echo "var str00='$extext';\n";
   echo "var rexp = /\[br\]/gi;\n";
   echo "str00 = str00.replace(rexp,'\\n');\n";
   echo "document.forms['exciting'].elements['extext'].value = str00;\n";

//   echo "document.forms['exciting'].elements['extext'].value = '$extext';\n";
   echo "//-->\n";
   echo "</script>\n";
  }
  if (preg_match("/\,admin\,/i",$all_rights)) {
   echo "<br><input type=checkbox name=emoderated value=100 ";
   if ($emoderated == 1) { echo " CHECKED ";}
   echo ">Предмодерирование комментариев\n";
  }
  echo "</div></div>";
  
  echo "<div class=\"row \"><div class=\"col-md-6\"><h4>Адрес, телефоны (или пусто)</h4><INPUT name=extaddr type=text size=80 value='$extaddr'></div>\n";
  echo "<div class=\"col-md-6\"><h4>Режим работы (или пусто)</h4><INPUT name=extwork type=text size=80 value='$extwork'></div></div>\n";
  
  echo "<div class=\"row \"><div class=\"col-md-12\"><h4>Вид объекта</h4>";
  echo $extype_txt;
  echo "</div><div class=\"col-md-12\">";
  echo "<table id=extype1 border=0 style=\"visibility:hidden; display:none;\">";
  echo "<tbody valign=top><tr><td>".$typex_txt[1]."</td><td>".$typex_txt[2]."</td></tr></table>\n";
  echo "<table id=extype6 border=0 style=\"visibility:hidden; display:none;\">";
  echo "<tbody valign=top><tr><td>".$typex6_txt[1]."</td><td>".$typex6_txt[2]."</td></tr></table>\n";
  echo "</div></div>";
 
  if (preg_match("/\,admin\,/i",$all_rights)) {
   echo "<div class=\"row \"><div class=\"col-md-6\"><h4>BOOKING URL</h4>";
   echo "<INPUT name=booking type=text size=80 value='$booking'></div>\n";
   echo "<div class=\"col-md-6\">";
  }
  if (preg_match("/\,admin\,/i",$all_rights)) {
    echo "<h4>Фото:&nbsp;</h4>\n";
    echo "<INPUT name=ffile type=text size=80 value='$ffile'></div></div>\n";
  }
  echo "<div class=\"row \"><div class=\"col-md-6\"><INPUT name=submitexc type=submit value='Добавить'>\n";
  echo "</div></div></FORM>\n";
 }
// полномочия по новым объектам - end

//////////////////////////////////////////////////////////////////////////////

// полномочия по изменению объектов - begin
 if (($admt == 'excited' ) && ($exciting_enable)) {

// подготовка текстов
  $extext = mysafehtml2($_REQUEST["extext"]);
  $extaddr = mysafestring($_REQUEST["extaddr"]);
  $extwork = mysafestring($_REQUEST["extwork"]);
  $townid = (int) mysafestring($_REQUEST["townid"]);
  $excid = (int) mysafestring($_REQUEST["excid"]);
  $exact = (int) mysafestring($_REQUEST["exact"]);
  $helptownid = mysafestring($_REQUEST["helptownid"]);
  $helpexcid = mysafestring($_REQUEST["helpexcid"]);
  $oldhelpexcid = mysafestring($_REQUEST["oldhelpexcid"]);
  $egrw = mysafestring($_REQUEST["egrw"]);
  $ngrw = mysafestring($_REQUEST["ngrw"]);
  $extype = (int)mysafestring($_REQUEST["extype"]);
  $submitexc = $_REQUEST["submitexc"];
  $submitexn = $_REQUEST["submitexn"];
  $parentobject = (int)mysafestring($_REQUEST["parentobject"]);
  $childobject = (int)mysafestring($_REQUEST["childobject"]);
  $booking = $_REQUEST["booking"];
  $booking = booklink($booking);
  $emoderated = (int) $_REQUEST["emoderated"];
  if ($emoderated == "100") {
   $emoderated = 1;
  }
  else {
   $emoderated = 0;
  }
  if ($parentobject == 100 ) { $exmainid=-1*$excid;} else { $exmainid=$childobject;}
echo $exid;
  if (preg_match("/\,admin\,/i",$all_rights)) {
    $ffile = foto_find(mysafestring($_REQUEST["ffile"]));
  }
  else {
    $ffile = '';
  }
  $ngr = texttodegrees($ngrw);
  $egr = texttodegrees($egrw);
  $ngrw = degreestotext($ngr);
  $egrw = degreestotext($egr);
  if ($exact == "100") {
   $exact = 1;
  }
  else {
   $exact = 0;
  }
  $typex=array();
  $typex6=array();
  if (!($excid > 0 )) {
   $excid = 0;
   $oldhelpexcid="";
  }

  if ($excid != 0) {
// для хистори и для извлечения
   $sql="select * from exciting where id=$excid";
   $result = $dbi->query($sql);
   $_excid = 0;
   $_exact = 0;
   $_extext = "";
   $_ecook = "";
   $_ecooked = "";
   $_helpexcid = "";
   $_booking = "";
   $_emoderated = "";
   $_typexs = "";
   $_booking = "";
   $_ngr = 0;
   $_egr = 0;
// добавлять сейчас не надо
   if($myrow = mysqli_fetch_array($result)) {
     $_excid = (int) $myrow["id"];
     if ($_excid != 0) {
      $_extext = $myrow["descrip"];
      $_ffile = $myrow["ffile"];
      $_townid = $myrow["town"];
      $_helpexcid = $myrow["name"];
      $_extype = (int) $myrow["type"];
      $_typexs = $myrow["kind"];
      $_booking = $myrow["booking"];
      $_emoderated = $myrow["emoderated"];
      $_ngr = $myrow["Nshirota"];
      $_egr = $myrow["Edolgota"];
      $_exact = $myrow["exact"];
      $_mainid = $myrow["mainid"];
      $_ecook = $myrow["cook"];
      $_ecooked = $myrow["cooked"];
     }
   }
   mysqli_free_result($result);
  }

  if (($submitexn) && ($excid != 0)) {
// Если вызов из базы старых данных.
   $excid = 0;
   $exact = 0;
   $extext = "";
   $helpexcid = "";
   $typexs = "";
   $typexs6 = "";
   $ngr = 0;
   $egr = 0;
   $ffile = '';
   $extaddr = "";
   $extwork = "";
   $booking = "";
   $emoderated = "";
   $oldhelpexcid = "";
// добавлять сейчас не надо
   $submitexc = "";
   $typex = array();
   $typex6 = array();
   $excid = (int) $_excid;
   if ($excid != 0) {
      $extext = $_extext;
      $townid = $_townid;
      $ffile = $_ffile;
      $helpexcid = $_helpexcid;
      $oldhelpexcid = $helpexcid;
      $extype = (int) $_extype;
      $exact = (int) $_exact;
      $booking = $_booking;
      $mainid = $_mainid;
      $emoderated = $_emoderated;
      if ($extype == 1) {
       $typexs = $_typexs;
       $typex = explode(',',$typexs);
      }
      if ($extype == 6) {
       $typexs6 = $_typexs;
       $typex6 = explode(',',$typexs6);
      }
      $ngr = $_ngr;
      $egr = $_egr;
      $ngrw = degreestotext($ngr);
      $egrw = degreestotext($egr);
      $extext =  str_replace("[BR]","[br]",my_unhtml($extext));
   }
// Если данные - новые.
  }
  else {
   $newadds = $_REQUEST["typex"];
   if (is_array($newadds)) {
    reset ($newadds);
    while (list ($key, $val) = each ($newadds)) {
     $typex[] = mysafestring($val);
    }
   }
   else {
    $typex[] = mysafestring($newadds);
   }
   $typexs=join($typex,',');
   
   $newadds = $_REQUEST["typex6"];
   if (is_array($newadds)) {
    reset ($newadds);
    while (list ($key, $val) = each ($newadds)) {
     $typex6[] = mysafestring($val);
    }
   }
   else {
    $typex6[] = mysafestring($newadds);
   }
   $typexs6=join($typex6,',');
  }

  $extype_txt="";
  $sql="select id,name from type order by srtfld asc";
  $result = $dbi->query($sql);
  if($myrow = mysqli_fetch_array($result)) {
   do {
    $tid = $myrow["id"];
    $tname = $myrow["name"];
    if ($extype == $tid) {
      $extype_txt = $extype_txt."<input type=radio name=extype value=$tid CHECKED";
      if ($tid == 1) {
        $extype_txt .= " onclick='ChHide(\"extype6\"); ChUnVis(this,\"extype1\")' ";
      }
      elseif ($tid == 6) {
        $extype_txt .= " onclick='ChHide(\"extype1\"); ChUnVis(this,\"extype6\")' ";
      }
      else {
        $extype_txt .= " onclick='ChHide(\"extype1\"); ChHide(\"extype6\")' ";
      }
      $extype_txt .= ">$tname &nbsp;";
    }
    else {
      $extype_txt = $extype_txt."<input type=radio name=extype value=$tid";
      if ($tid == 1) {
        $extype_txt .= " onclick='ChHide(\"extype6\"); ChUnVis(this,\"extype1\")' ";
      }
      elseif ($tid == 6) {
        $extype_txt .= " onclick='ChHide(\"extype1\"); ChUnVis(this,\"extype6\")' ";
      }
      else {
        $extype_txt .= " onclick='ChHide(\"extype1\"); ChHide(\"extype6\")' ";
      }
      $extype_txt .= ">$tname &nbsp;";
    }
   } while($myrow = mysqli_fetch_array($result));
  }
  mysqli_free_result($result);

  if ($excid == 0) {
   echo "<h3>Выбор/поиск объекта:\n</h3>";
  }
  else {
   echo "<h3>Изменение объекта:\n</h3>";
  }
  if ($submitexc) {
   $sql="select id, name, hide from towns where id=$townid";
   $townid = 0;
   $townhide = 0;
   $helptownid = "";
   $result = $dbi->query($sql);
   if($myrow = mysqli_fetch_array($result)) {
      $townid = (int) $myrow["id"];
      $townhide = $myrow["hide"];
      $helptownid = $myrow["name"];
   }
   mysqli_free_result($result);
  }
// если данные добавляются
  if (($submitexc) and ($excid != 0)) {
   if (($helpexcid != "") and ($extype != 0) and ($townid != 0)) {
    if ((($extype == 1) and ($typexs !="")) or (($extype == 6) and ($typexs6 !="")) or (($extype != 1) and ($extype != 6))) {
     $kind = "";
     if ($extype == 1) {
       $kind=$typexs;
     }
     if ($extype == 6) {
       $kind=$typexs6;
     }
     if ($_exact) {
      $ngr=$_ngr;
      $egr=$_egr;
     }
     $newtext = $extext;
     if ($extaddr != "") {
      if ($newtext) {
       $newtext = $newtext . "<br><b>Адрес: </b>$extaddr";
      }
      else {
       $newtext = "<b>Адрес: </b>$extaddr";
      }
     }
     if ($extwork != "") {
      if ($newtext) {
       $newtext = $newtext . "<br><b>Режим работы: </b>$extwork";
      }
      else {
       $newtext = "<b>Режим работы: </b>$extwork";
      }
     }
     if ($helpexcid <> $_helpexcid) {
      $sql_in = "insert into history (tblname,cook,ip,id,field,valueold,valuenew)
                values ('exciting','$ctext','$ipp',$excid,'name','$_helpexcid','$helpexcid')";
      $result_in = $dbi->query($sql_in);

     }
     if ($newtext <> $_extext) {
      $sql_in = "insert into history (tblname,cook,ip,id,field,valueold,valuenew)
                values ('exciting','$ctext','$ipp',$excid,'descrip','$_extext','$newtext')";
      $result_in = $dbi->query($sql_in);
     }
     if ($ngr <> $_ngr) {
      $sql_in = "insert into history (tblname,cook,ip,id,field,valueold,valuenew)
                values ('exciting','$ctext','$ipp',$excid,'Nshirota','$_ngr','$ngr')";
      $result_in = $dbi->query($sql_in);
     }
     if ($ngr <> $_ngr) {
      $sql_in = "insert into history (tblname,cook,ip,id,field,valueold,valuenew)
                values ('exciting','$ctext','$ipp',$excid,'Edolgota','$_egr','$egr')";
      $result_in = $dbi->query($sql_in);
     }
     if ($extype <> $_extype) {
      $sql_in = "insert into history (tblname,cook,ip,id,field,valueold,valuenew)
                values ('exciting','$ctext','$ipp',$excid,'type','$_extype','$extype')";
      $result_in = $dbi->query($sql_in);
     }
     if ($townid <> $_townid) {
      $sql_in = "insert into history (tblname,cook,ip,id,field,valueold,valuenew)
                values ('exciting','$ctext','$ipp',$excid,'town','$_townid','$townid')";
      $result_in = $dbi->query($sql_in);
      if (($townhide != 0) and ($townid != 0)) {
// добавляем примечательность к городу, у которого hide <>0
       if ($townhide == 1) {
        $sql_in = "update towns set hide = 2 where id = $townid";
        $result_in = $dbi->query($sql_in);
       }
       else {
        $sql_in = "update towns set hide = 0 where id = $townid";
        $result_in = $dbi->query($sql_in);
       }
      }
     }
     $areaid = 0;
     $sql_ar = "select area from towns where id=$townid";
     $result_ar = $dbi->query($sql_ar);
     if ($myrow_ar = mysqli_fetch_array($result_ar)) {
      $areaid = $myrow_ar["area"];
     }
     $sqladm = "";
     if (preg_match("/\,admin\,/i",$all_rights)) {
       $sqladm = ", booking='$booking', emoderated=$emoderated, ffile='$ffile' ";
     }
     $sql_in = "update exciting set town=$townid, mainid=$exmainid, name='$helpexcid', descrip='$newtext',
                type='$extype',kind='$kind',Nshirota='$ngr',Edolgota='$egr',area=$areaid,exact=$exact,cooked='$ctext'"
                .$sqladm." where id=$excid";
     
     $result_in = $dbi->query($sql_in);
     if ($result_in) {
      if ($townid <> $_townid) {
       $sql_in = "update foto set town=$townid where exciting=$excid";
       $dbi->query($sql_in);
      }
      if ($ffile) {
        $sql_uf = "update foto set exciting = $excid, town = $townid where file='$ffile'";
        error_log($sql_uf);
        $dbi->query($sql_uf);
      }
      if (($townid <> $_townid) or ($areaid <> $_areaid) or ($extype <> $_extype) or ($typexs <> $_typexs)) {
       extypes_upd($excid,$townid,$areaid,$extype,$typex);
      }
      echo "<h3>Объект успешно изменён</h3>\n";
      $excid = 0;
      $helpexcid  = "";
      $extext   = "";
      $extaddr  = "";
      $extwork  = "";
      $ngrw     = "";
      $ffile = "";
      $egrw     = "";
      $typex    = array();
      $typexs   = "";
      $typex6    = array();
      $typexs6   = "";
     }
     else {
      echo "<h3>При изменении произошла ошибка\n<BR>";
      echo $dbi->errno.": ".$dbi->error."</h3>";
     }
//     mysqli_free_result($result_in);
    }
   }
  }
  else {
//   echo "!!!";
  }
  $typex_txt=array();
  $typex_txt = gettypextxt('0%',$typex,'typex[]');
  $typex6_txt=array();
  $typex6_txt = gettypextxt('6%',$typex6,'typex6[]');

// если нет $excid, показываем укороченную форму.
  if ($excid == 0 ) {
   echo "<form name=excited action=".$_SERVER["PHP_SELF"]." method=post >\n";
   echo "<INPUT name=admt type=hidden value='excited'>\n";
   echo "<TABLE BORDER=0 cellspacing=5 CELLPADDING=5><tbody valign=top>\n";
   echo "<TR><td width=260 class=travel1 align=right>id объекта (для редактирования):</b>&nbsp;</TD>\n";
   echo "<TD><INPUT name=excid type=text size=20 value=$excid>";
   echo "<input type=button name=ssxxy onclick=SelId('excited','excid','/f_exci.php') value='Найти по имени'>\n";
   echo "</TD></TR>\n";
   echo "<TR><TD>&nbsp;</TD><TD class=gensmall><input type=text name=helpexcid size=80 style='width:450px; font-size:90%' class=helpline value='$helpexcid'>";
   echo "</TD></TR>";
   echo "<TR><TD>&nbsp;</TD><TD><INPUT name=submitexn type=submit value='Вывести информацию'>\n";
   echo "</TD></TR>\n";
   echo "</TABLE></FORM>\n";
  }
  else {
   echo "<form name=excited action=".$_SERVER["PHP_SELF"]." method=post >\n";
   echo "<INPUT name=admt type=hidden value='excited'>\n";
   echo "<INPUT name=excid type=hidden value=$excid>\n";
   echo "<TABLE BORDER=0 cellspacing=2 CELLPADDING=2><tbody valign=top>\n";
   echo "<TR><td width=260 class=travel1 align=right>Прежнее наименование:&nbsp;</TD>\n";
   echo "<TD>$oldhelpexcid<input type=hidden name=oldhelpexcid value='$oldhelpexcid'></TD></TR>\n";
   echo "<TR><td width=260 class=travel1 align=right>Наименование:&nbsp;</TD>\n";
   echo "<TD><INPUT name=helpexcid type=text size=80 value='$helpexcid'></TD></TR>\n";
   echo "<TR><td width=260 class=travel1 align=right>id города:</b>&nbsp;</TD>\n";
   echo "<TD><INPUT name=townid type=text size=20 ";
    if ($townid > 0) { echo "value=$townid"; }
   echo ">";
   echo "<input type=button name=ssxxx onclick=SelId('excited','townid','/f_town.php') value='Найти по имени'>\n";
   echo "</TD></TR>\n";
   echo "<TR><TD>&nbsp;</TD><TD class=gensmall><input type=text name=helptownid size=80 style='width:450px; font-size:90%' class=helpline value='$helptownid'>";
   echo "</TD></TR>";
   echo "<TR class=\"gensmall\"><TD align=right>Создал:</TD><TD>$_ecook</TR><TR class=\"gensmall\"><TD align=right>Редактировал:</TD><TD>$_ecooked</TD></TR>\n";
   echo "<TR><td width=260 class=travel1 align=right>Координаты (или пусто):&nbsp;</TD>\n";
   echo "<TD><b>N</b> ";
   if ($exact == 1) {
    echo "$ngrw<input type=hidden name=ngrw value='$ngrw'>&nbsp;&nbsp;<b>E</b> $egrw<input type=hidden name=egrw value='$egrw'>\n";
   }
   else {
    echo "<input type=text name=ngrw value='$ngrw'>&nbsp;&nbsp;<b>E</b> <input type=text name=egrw value='$egrw'>\n";
   }
   echo "<input type=checkbox name=exact value=100 ";
   if ($exact == 1) { echo " CHECKED ";}
   echo ">Координаты проверены</TD></TR>\n";
   echo "<TR><td width=260 class=travel1 align=right>Описание:&nbsp;</TD>\n";
   echo "<TD><TEXTAREA name=extext rows=10  style=\"width:100%\" wrap=none>\n";
   if ($extext) {
    echo str_replace("[br]","\n",$extext);
   }
   echo "</TEXTAREA>\n";
   if (preg_match("/\,admin\,/i",$all_rights)) {
    echo "<br><input type=checkbox name=emoderated value=100 ";
    if ($emoderated == 1) { echo " CHECKED ";}
    echo ">Предмодерирование комментариев\n";
   }
   echo "</TD></TR>";
   echo "<TR><td width=260 class=travel1 align=right>Адрес, телефоны (или пусто):&nbsp;</TD>\n";
   echo "<TD><INPUT name=extaddr type=text size=80 value='$extaddr'></TD></TR>\n";
   echo "<TR><td width=260 class=travel1 align=right>Режим работы (или пусто):&nbsp;</TD>\n";
   echo "<TD><INPUT name=extwork type=text size=80 value='$extwork'></TD></TR>\n";
   echo "<TR><td width=260 class=travel1 align=right>Вид объекта:&nbsp;</TD>\n";
   echo "<TD>";
   echo $extype_txt;
   echo "</TD></TR>";
    echo "<TR><td width=260 class=travel1 align=right>Групповой объект</TD><TD>";
  echo "<input type=checkbox name=parentobject value=100";
  if  ($mainid < 0 ) { echo " CHECKED ";}
   echo ">&nbsp;Да. Если отмечен, следующий раздел - В подчинении - не обрабатывается.";
  echo "</TD></TR>";
  echo "<TR><td width=260 class=travel1 align=right>В подчинении</TD><TD>";
  echo "<INPUT name=childobject type=text size=10 value='$mainid'>&nbsp;id группового объекта</TD></TR>\n";
   
   if (preg_match("/\,admin\,/i",$all_rights)) {
    echo "<TR><td width=260 class=travel1 align=right>BOOKING URL:&nbsp;</TD>\n";
    echo "<TD>";
    echo "<INPUT name=booking type=text size=80 value='$booking'>\n";
    echo "</TD></TR>";
   }
   if (preg_match("/\,admin\,/i",$all_rights)) {
     echo "<TR><td width=260 class=travel1 align=right>Фото:&nbsp;</TD>\n";
     echo "<TD><INPUT name=ffile type=text size=80 value='$ffile'></TD></TR>\n";
   }
   echo "<TR><td width=260 class=travel1 align=right>Тип достопримечательности:&nbsp;</TD>\n";
   echo "<TD>";
   if ($extype==1) {
    echo "<table id=extype1 border=0>";
   }
   else {
    echo "<table id=extype1 border=0 style=\"visibility:hidden; display:none;\">";
   }
   echo "<tbody valign=top><tr><td>".$typex_txt[1]."</td><td>".$typex_txt[2]."</td></tr></table>\n";
   echo "</TD></TR>\n";
   echo "<TR><td width=260 class=travel1 align=right>Тип сервиса:&nbsp;</TD>\n";
   echo "<TD>";
   if ($extype==6) {
    echo "<table id=extype6 border=0>";
   }
   else {
    echo "<table id=extype6 border=0 style=\"visibility:hidden; display:none;\">";
   }
   echo "<tbody valign=top><tr><td>".$typex6_txt[1]."</td><td>".$typex6_txt[2]."</td></tr></table>\n";
   echo "</TD></TR>\n";
   echo "<TR><td>&nbsp;</TD><TD><INPUT name=submitexc type=submit value='Изменить'>\n";
   echo "</TD></TR>\n";
   echo "</div></FORM>\n";
  }
 }
// полномочия по исправлению объекта - end


//////////////////////////////////////////////////////////////////////////////
?>
