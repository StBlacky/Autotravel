<?php
//////////////////////////////////////////////////////////////////////////////

// полномочия по (!!! новому !!!) городу - begin
 if (($towns_enable) && ($admt == 'town')) {
  // $townid = (int) mysafestring($_REQUEST["townid"]);
  $areaid = (int) mysafestring($_REQUEST["areaid"]);
  $egrw = mysafestring($_REQUEST["egrw"]);
  $ngrw = mysafestring($_REQUEST["ngrw"]);
  $name = mysafestring($_REQUEST["name"]);
  $namefrom = mysafestring($_REQUEST["namefrom"]);
  $nameto = mysafestring($_REQUEST["nameto"]);
  $namepo = mysafestring($_REQUEST["namepo"]);
  $namearound = mysafestring($_REQUEST["namearound"]);
  $namein = mysafestring($_REQUEST["namein"]);
  $seourl = mysafestring($_REQUEST["seourl"]);
  $booking = mysafestring($_REQUEST["booking"]);
  if (!(empty($booking))) {
   $arboo =  parse_url($booking);
   $booking = "https://".$arboo[host].$arboo[path];
  }
  $submit = $_REQUEST["submitexc"];

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
  $_townid = 0;
  $_name = "";

  echo "<h3>Ввод города:\n</h3>";
  // добавление. исправления пока нет.
  if (($submit)&&($name)&&($areaid)) {
   $sql="select id, name, hide from towns where name='$name' and area=$areaid";
   $result = $dbi->query($sql);
   if($myrow = mysqli_fetch_array($result)) {
    $_townid = (int) $myrow["id"];
    $_name = $myrow["name"];
   }
   mysqli_free_result($result);
  }
  if ($_townid) {
   echo "<h4>Город $name уже есть в такой области. Повторный ввод не нужен!</h4>";
   $submit ="";
   $_townid=0;
  }

  if (($submit)&&($seourl)) {
   $sql="select id, name, hide from towns where url='$seourl'";
   $result = $dbi->query($sql);
   if($myrow = mysqli_fetch_array($result)) {
    $_townid = (int) $myrow["id"];
    $_name = $myrow["name"];
   }
   mysqli_free_result($result);
  }
  if ($_townid) {
   echo "<h4>Город $name уже имеет url = $seourl !</h4>";
   $submit ="";
   $_townid=0;
  }
  
  if (($submit)&&($name)&&($areaid)) {
   $sql="select max(id) as townid from towns where id<>9999";
   $result = $dbi->query($sql);
   if($myrow = mysqli_fetch_array($result)) {
    $_townid = (int) $myrow["townid"];
   }
   mysqli_free_result($result);
  }

  if ($_townid) {
     $_townid=$_townid + 1;
     $sql_in = "insert into towns
                 (id, name, nameto, namefrom, namepo, namearound, namein, url, booking, hide, area, Nshirota, Edolgota, ffile) values
                 ('$_townid','$name','$nameto','$namefrom','$namepo','$namearound','$namein','$seourl','$booking','1','$areaid','$ngr','$egr','$ffile')";
     echo $sql_in;
     $result_in = $dbi->query($sql_in);
     if ($result_in) {
      echo "<h3>Город $name успешно добавлен id=$_townid</h3>\n";
      $_townid  = 0;
      $townid  = 0;
      $areaid   = "";
      $name  = "";
      $nameto  = "";
      $namepo  = "";
      $ffile = "";
      $namefrom  = "";
      $namearound  = "";
      $seourl  = "";
      $booking  = "";
      $ngrw     = "";
      $egrw     = "";
      mysqli_free_result($result_in);
     }
     else {
      echo "<h3>При добавлении произошла ошибка\n<BR>";
      echo $dbi->errno.": ".$dbi->error."</h3>";
     }
  }
  $areas = array();
  $sql="select id, name from areas order by name asc";
  $result = $dbi->query($sql);
  while ($myrow = mysqli_fetch_array($result)) {
    $areas[$myrow["id"]] = $myrow["name"];
  }
  mysqli_free_result($result);

// если даже было добавление/исправление, всё равно форму ввода показать надо.
  echo "<form name=town action=".$_SERVER["PHP_SELF"]." method=post >\n";
  echo "<INPUT name=admt type=hidden value='town'>\n";
  echo "<TABLE BORDER=0 cellspacing=2 CELLPADDING=2><tbody class=travell0>\n";
  echo "<TR><td width=220 class=travel1 align=right>Наименование:&nbsp;</TD>\n";
  echo "<TD><INPUT name=name type=text size=80 value='$name'></TD></TR>\n";
  echo "<TR><td width=220 class=travel1 align=right>\"Поездка в\":&nbsp;</TD>\n";
  echo "<TD><INPUT name=nameto type=text size=80 value='$nameto'></TD></TR>\n";
  echo "<TR><td width=220 class=travel1 align=right>\"Поездка из\":&nbsp;</TD>\n";
  echo "<TD><INPUT name=namefrom type=text size=80 value='$namefrom'></TD></TR>\n";
  echo "<TR><td width=220 class=travel1 align=right>\"Прогулка по\":&nbsp;</TD>\n";
  echo "<TD><INPUT name=namepo type=text size=80 value='$namepo'></TD></TR>\n";
  echo "<TR><td width=220 class=travel1 align=right>\"Прогулка рядом с\":&nbsp;</TD>\n";
  echo "<TD><INPUT name=namearound type=text size=80 value='$namearound'></TD></TR>\n";
  echo "<TR><td width=220 class=travel1 align=right>\"Чем заняться в\":&nbsp;</TD>\n";
  echo "<TD><INPUT name=namein type=text size=80 value='$namein'></TD></TR>\n";
  echo "<TR><td width=220 class=travel1 align=right>\"SEO URL\":&nbsp;</TD>\n";
  echo "<TD><INPUT name=seourl type=text size=80 value='$seourl'></TD></TR>\n";
  echo "<TR><td width=220 class=travel1 align=right>Линк на букинг:&nbsp;</TD>\n";
  echo "<TD><INPUT name=booking type=text size=80 value='$booking'></TD></TR>\n";
  echo "<TR><td width=220 class=travel1 align=right>\"Фото\":&nbsp;</TD>\n";
  echo "<TD><INPUT name=ffile type=text size=80 value='$ffile'></TD></TR>\n";
  echo "<TR><td width=220 class=travel1 align=right>Область:</b>&nbsp;</TD>\n";
  echo "<TD><select size=1 name=areaid>";
  echo option_list($areas,$areaid);
  echo "</select></TD></TR>";
  echo "<TR><td width=220 class=travel1 align=right>Координаты:&nbsp;</TD>\n";
  echo "<TD><b>N</b> ";
  echo "<input type=text name=ngrw value='$ngrw'>&nbsp;&nbsp;<b>E</b> <input type=text name=egrw value='$egrw'>\n";
  echo "</TD></TR>\n";
  echo "<TR><td>&nbsp;</TD><TD><INPUT name=submitexc type=submit value='Добавить'>\n";
  echo "</TD></TR>\n";
  echo "</TABLE></FORM>\n";
 }
// полномочия по новому городу - end

//////////////////////////////////////////////////////////////////////////////

// полномочия по изменению города - begin
 if (($admt == 'towned' ) && ($towns_enable)) {
  // $townid = (int) mysafestring($_REQUEST["townid"]);
  $areaid = (int) mysafestring($_REQUEST["areaid"]);
  $townid = (int) mysafestring($_REQUEST["townid"]);
  $egrw = mysafestring($_REQUEST["egrw"]);
  $ngrw = mysafestring($_REQUEST["ngrw"]);
  $name = mysafestring($_REQUEST["name"]);
  $namefrom = mysafestring($_REQUEST["namefrom"]);
  $nameto = mysafestring($_REQUEST["nameto"]);
  $namepo = mysafestring($_REQUEST["namepo"]);
  $namein = mysafestring($_REQUEST["namein"]);
  $seourl = mysafestring($_REQUEST["seourl"]);
  $namearound = mysafestring($_REQUEST["namearound"]);
  $booking = mysafestring($_REQUEST["booking"]);
  $ffile = foto_find(mysafestring($_REQUEST["ffile"]));
  if (!(empty($booking))) {
   $arboo =  parse_url($booking);
   $booking = "https://".$arboo[host].$arboo[path];
  }
  $submittown = $_REQUEST["submittown"];
  
  $ngr = texttodegrees($ngrw);
  $egr = texttodegrees($egrw);
  $ngrw = degreestotext($ngr);
  $egrw = degreestotext($egr);

  if (($submittown)&&($seourl)) {
   $sql="select id, name, hide from towns where url='$seourl' and id<>$townid";
   $result = $dbi->query($sql);
   if($myrow = mysqli_fetch_array($result)) {
    $_townid = (int) $myrow["id"];
    $_name = $myrow["name"];
   }
   mysqli_free_result($result);
  }
  if ($_townid) {
   echo "<h4>Город $name уже имеет url = $seourl !</h4>";
   $submit ="";
   $_townid=0;
  }
  
  if (!($townid > 0 )) {
   $townid = 0;
  }
  if (!($townid == 0)) {
   // собрать значения "что меняем"
   $_areaid="";
   $_name="";
   $_namefrom="";
   $_nameto="";
   $_namepo="";
   $_namein="";
   $_seourl="";
   $_ffile = "";
   $_namearound="";
   $_egr="";
   $_ngr="";
   $_booking="";
   $sql = "select * from towns where id=$townid";
   $result = $dbi->query($sql);
   $townid=0;
   if ($myrow = mysqli_fetch_array($result)) {
    $townid = (int) $myrow["id"];
    $_areaid = $myrow["area"];
    $_name = $myrow["name"];
    $_namefrom = $myrow["namefrom"];
    $_nameto = $myrow["nameto"];
    $_namepo = $myrow["namepo"];
    $_namein = $myrow["namein"];
    $_ffile = $myrow["ffile"];
    $_seourl = $myrow["url"];
    $_namearound = $myrow["namearound"];
    $_egr = $myrow["Edolgota"];
    $_ngr = $myrow["Nshirota"];
    $_booking = $myrow["booking"];
    $_ngrw = degreestotext($_ngr);
    $_egrw = degreestotext($_egr);
   }
   mysqli_free_result($result);
  }
  // формы для интерфейса
  if ($townid > 0) {
   // изменение данных и обновление
   if ($submittown == "Изменить") {
    $sql = "update towns set area='$areaid', name='$name', namefrom='$namefrom', nameto='$nameto', namein='$namein', ffile='$ffile', url='$seourl',"
            . " namepo='$namepo', namearound='$namearound', Edolgota='$egr', Nshirota='$ngr', booking='$booking' where id=$townid";
    if ($dbi->query($sql)) {
     $townid = 0; // чтоб показалась форма поиска
     echo "<h3>Город $name успешно обновлен!\n</h3>";
    }
    else {
     // вновь показать форму ввода. Что-то где-то не так было   
     echo "<h3>Город $name обновить не удалось<br>";
     echo $dbi->errno.": ".$dbi->error."</h3>";
     $_areaid = $areaid;
     $_name = $name;
     $_namefrom = $namefrom;
     $_nameto = $nameto;
     $_namepo = $namepo;
     $_ffile = $ffile;
     $_namein = $namein;
     $_seourl = $seourl;
     $_namearound = $namearound;
     $_egr = $egr;
     $_ngr = $ngr;
     $_booking = $booking;
     $_ngrw = degreestotext($ngr);
     $_egrw = degreestotext($egr);
     $submittown="";
    }
   }
   // вывод данных для изменения
   if ($submittown == "") {
    $areas = array();
    $sql="select id, name from areas order by name asc";
    $result = $dbi->query($sql);
    while ($myrow = mysqli_fetch_array($result)) {
      $areas[$myrow["id"]] = $myrow["name"];
    }
    mysqli_free_result($result);
    echo "<h3>Изменение города:\n</h3>";
    echo "<form name=town action=".$_SERVER["PHP_SELF"]." method=post >\n";
    echo "<INPUT name=admt type=hidden value='towned'>\n";
    echo "<INPUT name=townid type=hidden value='$townid'>\n";
    echo "<TABLE BORDER=0 cellspacing=2 CELLPADDING=2><tbody class=travell0>\n";
    echo "<TR><td width=220 class=travel1 align=right>Наименование:&nbsp;</TD>\n";
    echo "<TD><INPUT name=name type=text size=80 value='$_name'></TD></TR>\n";
    echo "<TR><td width=220 class=travel1 align=right>\"Поездка в\":&nbsp;</TD>\n";
    echo "<TD><INPUT name=nameto type=text size=80 value='$_nameto'></TD></TR>\n";
    echo "<TR><td width=220 class=travel1 align=right>\"Поездка из\":&nbsp;</TD>\n";
    echo "<TD><INPUT name=namefrom type=text size=80 value='$_namefrom'></TD></TR>\n";
    echo "<TR><td width=220 class=travel1 align=right>\"Прогулка по\":&nbsp;</TD>\n";
    echo "<TD><INPUT name=namepo type=text size=80 value='$_namepo'></TD></TR>\n";
    echo "<TR><td width=220 class=travel1 align=right>\"Прогулка рядом с\":&nbsp;</TD>\n";
    echo "<TD><INPUT name=namearound type=text size=80 value='$_namearound'></TD></TR>\n";
    echo "<TR><td width=220 class=travel1 align=right>\"Чем заняться в\":&nbsp;</TD>\n";
    echo "<TD><INPUT name=namein type=text size=80 value='$_namein'></TD></TR>\n";
    echo "<TR><td width=220 class=travel1 align=right>\"SEO URL\":&nbsp;</TD>\n";
    echo "<TD><INPUT name=seourl type=text size=80 value='$_seourl'></TD></TR>\n";
    echo "<TR><td width=220 class=travel1 align=right>\"Фото\":&nbsp;</TD>\n";
    echo "<TD><INPUT name=ffile type=text size=80 value='$_ffile'></TD></TR>\n";
    echo "<TR><td width=220 class=travel1 align=right>Линк на букинг:&nbsp;</TD>\n";
    echo "<TD><INPUT name=booking type=text size=80 value='$_booking'></TD></TR>\n";
    echo "<TR><td width=220 class=travel1 align=right>Область:</b>&nbsp;</TD>\n";
    echo "<TD><select size=1 name=areaid>";
    echo option_list($areas,$_areaid);
    echo "</select></TD></TR>";
    echo "<TR><td width=220 class=travel1 align=right>Координаты:&nbsp;</TD>\n";
    echo "<TD><b>N</b> ";
    echo "<input type=text name=ngrw value='$_ngrw'>&nbsp;&nbsp;<b>E</b> <input type=text name=egrw value='$_egrw'>\n";
    echo "</TD></TR>\n";
    echo "<TR><td>&nbsp;</TD><TD><INPUT name=submittown type=submit value='Изменить'>\n";
    echo "</TD></TR>\n";
    echo "</TABLE></form>\n";
   }
  }

  if ($townid == 0) { // поиск города
   echo "<h3>Выбор/поиск города:\n</h3>";
   echo "<form name=towned action=".$_SERVER["PHP_SELF"]." method=post >\n";
   echo "<INPUT name=admt type=hidden value='towned'>\n";
   echo "<TABLE BORDER=0 cellspacing=2 CELLPADDING=2><tbody class=travell0>\n";
   echo "<TR><td width=220 class=travel1 align=right>id города (для редактирования):</b>&nbsp;</TD>\n";
   echo "<TD><INPUT name=townid type=text size=20 value=$townid>";
   echo "<input type=button name=ssxxy onclick=SelId('towned','townid','/f_town.php') value='Найти по имени'>\n";
   echo "</TD></TR>\n";
   echo "<TR><TD>&nbsp;</TD><TD class=gensmall><input type=text name=helptownid size=80 style='width:450px; font-size:90%' class=helpline value='$helptownid'>";
   echo "</TD></TR>";
   echo "<TR><TD>&nbsp;</TD><TD><INPUT name=submittselect type=submit value='Вывести информацию'>\n";
   echo "</TD></TR>\n";
   echo "</TABLE></FORM>\n";
  }

 }
//////////////////////////////////////////////////////////////////////////////
?>
