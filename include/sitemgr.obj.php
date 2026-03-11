<?php

// =======================
// EXCITING SUBTYPES ENGINE (shared for exciting+excited)
// PHP 7.3 compatible
// =======================

if (!function_exists('ex_subtype_prefix')) {
  function ex_subtype_prefix($extype) {
    $extype = (int)$extype;
    return ($extype === 1) ? '0%' : ($extype . '%');
  }
}

if (!function_exists('ex_get_types_with_subtypes')) {
  function ex_get_types_with_subtypes($dbi) {
    static $cache = null;
    if (is_array($cache)) return $cache;

    $types = array();
    $sql = "SELECT DISTINCT LEFT(id,1) AS pfx FROM type_ex";
    $res = $dbi->query($sql);
    if ($res) {
      while ($r = mysqli_fetch_assoc($res)) {
        $p = (int)$r['pfx'];
        $t = ($p === 0) ? 1 : $p;     // 0xx => extype=1
        $types[$t] = 1;
      }
      mysqli_free_result($res);
    }
    $cache = array_keys($types);
    return $cache;
  }
}

if (!function_exists('ex_read_subtypes_from_request')) {
  function ex_read_subtypes_from_request($extype) {
    $extype = (int)$extype;
    $vals = array();

    if (isset($_REQUEST['typex']) && is_array($_REQUEST['typex']) && isset($_REQUEST['typex'][$extype])) {
      $raw = $_REQUEST['typex'][$extype];
      if (is_array($raw)) {
        foreach ($raw as $v) { $vals[] = mysafestring($v); }
      } else {
        $vals[] = mysafestring($raw);
      }
    }

    $vals = array_values(array_filter($vals, function($x) {
      return (string)$x !== '';
    }));
    return $vals;
  }
}

if (!function_exists('ex_kind_from_subvals')) {
  function ex_kind_from_subvals($subvals) {
    if (!is_array($subvals) || !$subvals) return "";
    return implode(',', $subvals);
  }
}

if (!function_exists('ex_has_subtypes')) {
  function ex_has_subtypes($extype, $types_with_subtypes) {
    return in_array((int)$extype, (array)$types_with_subtypes, true);
  }
}

if (!function_exists('ex_render_extype_radios')) {
  function ex_render_extype_radios($dbi, $current_extype) {
    $current_extype = (int)$current_extype;
    $html = "";
    $sql="select id,name from type order by srtfld asc";
    $result = $dbi->query($sql);
    if($result && ($myrow = mysqli_fetch_array($result))) {
      do {
        $tid = (int)$myrow["id"];
        $tname = $myrow["name"];
        $checked = ($current_extype == $tid) ? " CHECKED" : "";
        $html .= "<input type=radio name=extype value=$tid$checked onclick='exShowSubtypes($tid)'>".$tname." &nbsp;";
      } while($myrow = mysqli_fetch_array($result));
      mysqli_free_result($result);
    }
    return $html;
  }
}

if (!function_exists('ex_render_subtype_blocks')) {
  function ex_render_subtype_blocks($types_with_subtypes, $current_extype, $current_subvals) {
    $current_extype = (int)$current_extype;

    $html = "";
    foreach ((array)$types_with_subtypes as $t) {
      $t = (int)$t;
      $prefix = ex_subtype_prefix($t);
      $name   = "typex[".$t."][]";
      $vals   = ($current_extype === $t) ? (array)$current_subvals : array();

      $txt = gettypextxt($prefix, $vals, $name);
      $style = ($current_extype === $t) ? "" : " style=\"visibility:hidden; display:none;\"";
      $html .= "<table id=\"extype-sub-".$t."\" class=\"extype-sub\" border=0$style>";
      $html .= "<tbody valign=top><tr><td>".$txt[1]."</td><td>".$txt[2]."</td></tr></tbody></table>\n";
    }
    return $html;
  }
}

// общий JS (один раз на страницу)
if (!isset($GLOBALS['_ex_subtypes_js_printed'])) {
  $GLOBALS['_ex_subtypes_js_printed'] = 1;
  echo "<script>
function exShowSubtypes(tid){
  var blocks = document.querySelectorAll('.extype-sub');
  for (var i=0;i<blocks.length;i++){
    blocks[i].style.visibility='hidden';
    blocks[i].style.display='none';
  }
  var el = document.getElementById('extype-sub-' + tid);
  if (el){
    el.style.visibility='visible';
    el.style.display='table';
  }
}
</script>";
}

//////////////////////////////////////////////////////////////////////////////

if (($exciting_enable) && ($admt == 'exciting')) {

  // =======================
  // ВВОД ОБЪЕКТА (exciting)
  // универсальные подтипы type_ex
  // =======================

  // ---------- подготовка текстов ----------
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
  $submitexn = $_REQUEST["submitexn"]; // оставил как было, хоть тут не используется
  $extype = (int) mysafestring($_REQUEST["extype"]);

  $booking = $_REQUEST["booking"];
  $booking = booklink($booking);

  $emoderated = (int) $_REQUEST["emoderated"];
  $emoderated = ($emoderated == "100") ? 1 : 0;

  if (preg_match("/\,admin\,/i",$all_rights)) {
    $ffile = foto_find(mysafestring($_REQUEST["ffile"]));
  } else {
    $ffile = '';
  }

  $ngr = texttodegrees($ngrw);
  $egr = texttodegrees($egrw);
  $ngrw = degreestotext($ngr);
  $egrw = degreestotext($egr);
  $exact = ($exact == "100") ? 1 : 0;

  // ---------- универсальные подтипы ----------
  $types_with_subtypes = ex_get_types_with_subtypes($dbi);         // например [1,5,6]
  $has_subtypes = ex_has_subtypes($extype, $types_with_subtypes);  // для выбранного типа

  $subvals = ex_read_subtypes_from_request($extype);               // выбранные подтипы (массив)
  $kind = ex_kind_from_subvals($subvals);                          // CSV

  // ---------- радиокнопки типа (переключение подтипов) ----------
  $extype_txt = ex_render_extype_radios($dbi, $extype);

  // JS один раз (если он уже где-то выше на странице — можно завернуть в if(window...))
  echo "<script>
  function exShowSubtypes(tid){
    var blocks = document.querySelectorAll('.extype-sub');
    for (var i=0;i<blocks.length;i++){
      blocks[i].style.visibility='hidden';
      blocks[i].style.display='none';
    }
    var el = document.getElementById('extype-sub-' + tid);
    if (el){
      el.style.visibility='visible';
      el.style.display='table';
    }
  }
  </script>";

  echo "<div class=\"col-md-10 text-left\" style=\" padding: 1em;\"><h2>Ввод объекта:\n</h2>";

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

      // обязательность подтипов: только если для extype они вообще существуют в type_ex
      if ( (!$has_subtypes) || ($kind !== "") ) {

        $newtext = $extext;
        if ($extaddr != "") {
          $newtext = $newtext ? ($newtext . "<br><b>Адрес: </b>$extaddr") : ("<b>Адрес: </b>$extaddr");
        }
        if ($extwork != "") {
          $newtext = $newtext ? ($newtext . "<br><b>Режим работы: </b>$extwork") : ("<b>Режим работы: </b>$extwork");
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

          // ВАЖНО: передаём в extypes_upd именно выбранные подтипы текущего extype
          extypes_upd($ex_id,$townid,$areaid,$extype,$subvals);

          if ($ffile) {
            $sql_uf = "update foto set exciting = $ex_id, town = $townid where file='$ffile'";
            $dbi->query($sql_uf);
          }

          if (($townhide != 0) and ($townid != 0)) {
            if ($townhide == 1) $dbi->query("update towns set hide = 2 where id = $townid");
            else $dbi->query("update towns set hide = 0 where id = $townid");
          }

          echo "<h3>Объект успешно добавлен</h3>\n";

          // сброс
          $helpexcid  = "";
          $extext   = "";
          $extaddr  = "";
          $extwork  = "";
          $ngrw     = "";
          $egrw     = "";
          $ffile    = "";
          $kind     = "";
          $subvals  = array();

        } else {
          echo "<h3>При добавлении произошла ошибка\n<BR>";
          echo $dbi->errno.": ".$dbi->error." <br>$sql_in</h3>";
        }

      } else {
        echo "<h3>Ошибка\n<BR>не указаны обязательные признаки объекта (подтипы)</h3>";
      }

    } else {
      echo "<h3>Ошибка\n<BR>не указан тип объекта или город</h3>";
    }
  }

  // ---------- РЕНДЕР ПОДТИПОВ (универсально) ----------
  $subtype_blocks_html = ex_render_subtype_blocks($types_with_subtypes, $extype, $subvals);

  // ---------- форма ввода ----------
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
  } else {
    echo "<input type=text name=ngrw value='$ngrw'>&nbsp;&nbsp;<b>E</b> <input type=text name=egrw value='$egrw'>\n";
  }
  echo "<input type=checkbox name=exact value=100 ";
  if ($exact == 1) { echo " CHECKED "; }
  echo ">Координаты проверены</div></div>\n";

  echo "<div class=\"row \"><div class=\"col-md-12\"><h4>Описание</h4><TEXTAREA name=extext rows=10 style=\"width:100%\" wrap=none>\n </TEXTAREA>\n";
  if ($extext) {
    echo "\n<script type='text/javascript'>\n";
    echo "var str00='$extext';\n";
    echo "var rexp = /\\[br\\]/gi;\n";
    echo "str00 = str00.replace(rexp,'\\n');\n";
    echo "document.forms['exciting'].elements['extext'].value = str00;\n";
    echo "</script>\n";
  }
  if (preg_match("/\,admin\,/i",$all_rights)) {
    echo "<br><input type=checkbox name=emoderated value=100 ";
    if ($emoderated == 1) { echo " CHECKED "; }
    echo ">Предмодерирование комментариев\n";
  }
  echo "</div></div>";

  echo "<div class=\"row \"><div class=\"col-md-6\"><h4>Адрес, телефоны (или пусто)</h4><INPUT name=extaddr type=text size=80 value='$extaddr'></div>\n";
  echo "<div class=\"col-md-6\"><h4>Режим работы (или пусто)</h4><INPUT name=extwork type=text size=80 value='$extwork'></div></div>\n";

  echo "<div class=\"row \"><div class=\"col-md-12\"><h4>Вид объекта</h4>";
  echo $extype_txt;
  echo "</div><div class=\"col-md-12\">";

  echo $subtype_blocks_html;

  // при первом рендере (если extype уже выбран) — открыть нужный блок
  echo "<script>exShowSubtypes(" . (int)$extype . ");</script>";

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

//////////////////////////////////////////////////////////////////////////////

// полномочия по изменению объектов - begin
if (($admt == 'excited' ) && ($exciting_enable)) {

  // =======================
  // ИЗМЕНЕНИЕ ОБЪЕКТА (excited)
  // универсальные подтипы type_ex
  // =======================

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
  if ($emoderated == "100") { $emoderated = 1; } else { $emoderated = 0; }

  // mainid
  if ($parentobject == 100 ) { $exmainid = -1*$excid; } else { $exmainid = $childobject; }

  // было: echo $exid;  (переменной нет) — убрал, чтобы не мусорить вывод и не ловить notice

  if (preg_match("/\,admin\,/i",$all_rights)) {
    $ffile = foto_find(mysafestring($_REQUEST["ffile"]));
  } else {
    $ffile = '';
  }

  $ngr = texttodegrees($ngrw);
  $egr = texttodegrees($egrw);
  $ngrw = degreestotext($ngr);
  $egrw = degreestotext($egr);

  if ($exact == "100") { $exact = 1; } else { $exact = 0; }

  // ---------- универсальные подтипы ----------
  $types_with_subtypes = ex_get_types_with_subtypes($dbi);
  $has_subtypes = ex_has_subtypes($extype, $types_with_subtypes);

  $subvals = array(); // выбранные подтипы
  $kind = "";         // CSV для поля exciting.kind

  if (!($excid > 0 )) {
    $excid = 0;
    $oldhelpexcid = "";
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
    $_ngr = 0;
    $_egr = 0;
    $_areaid = 0;
    $_townid = 0;
    $_extype = 0;
    $_mainid = 0;
    $_ffile = "";

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
        $_areaid = $myrow["area"];
      }
    }
    mysqli_free_result($result);

    // подтипы из БД
    $subvals = ($_typexs !== "") ? explode(',', $_typexs) : array();
    $kind = $_typexs;
  }

  if (($submitexn) && ($excid != 0)) {
    // Если вызов из базы старых данных.
    $excid = 0;
    $exact = 0;
    $extext = "";
    $helpexcid = "";
    $ngr = 0;
    $egr = 0;
    $ffile = '';
    $extaddr = "";
    $extwork = "";
    $booking = "";
    $emoderated = "";
    $oldhelpexcid = "";
    $submitexc = "";

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

      // универсальные подтипы из БД
      $subvals = ($_typexs !== "") ? explode(',', $_typexs) : array();
      $kind = $_typexs;

      $ngr = $_ngr;
      $egr = $_egr;
      $ngrw = degreestotext($ngr);
      $egrw = degreestotext($egr);
      $extext =  str_replace("[BR]","[br]",my_unhtml($extext));
    }

    // пересчитать признаки для текущего extype
    $types_with_subtypes = ex_get_types_with_subtypes($dbi);
    $has_subtypes = ex_has_subtypes($extype, $types_with_subtypes);
  }
  else {
    // новые данные из формы (единый формат)
    $subvals = ex_read_subtypes_from_request($extype);
    $kind = ex_kind_from_subvals($subvals);

    $types_with_subtypes = ex_get_types_with_subtypes($dbi);
    $has_subtypes = ex_has_subtypes($extype, $types_with_subtypes);
  }

  // --- радиокнопки вида объекта (универсально) ---
  $extype_txt = ex_render_extype_radios($dbi, $extype);

  if ($excid == 0) {
    echo "<h3>Выбор/поиск объекта:\n</h3>";
  } else {
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

  // если данные изменяются
  if (($submitexc) and ($excid != 0)) {
    if (($helpexcid != "") and ($extype != 0) and ($townid != 0)) {

      // обязательность подтипов: только если для extype они реально существуют в type_ex
      if ( (!$has_subtypes) || ($kind !== "") ) {

        if ($_exact) {
          $ngr = $_ngr;
          $egr = $_egr;
        }

        $newtext = $extext;
        if ($extaddr != "") {
          if ($newtext) $newtext = $newtext . "<br><b>Адрес: </b>$extaddr";
          else $newtext = "<b>Адрес: </b>$extaddr";
        }
        if ($extwork != "") {
          if ($newtext) $newtext = $newtext . "<br><b>Режим работы: </b>$extwork";
          else $newtext = "<b>Режим работы: </b>$extwork";
        }

        if ($helpexcid <> $_helpexcid) {
          $sql_in = "insert into history (tblname,cook,ip,id,field,valueold,valuenew)
                    values ('exciting','$ctext','$ipp',$excid,'name','$_helpexcid','$helpexcid')";
          $dbi->query($sql_in);
        }

        if ($newtext <> $_extext) {
          $sql_in = "insert into history (tblname,cook,ip,id,field,valueold,valuenew)
                    values ('exciting','$ctext','$ipp',$excid,'descrip','$_extext','$newtext')";
          $dbi->query($sql_in);
        }

        if ($ngr <> $_ngr) {
          $sql_in = "insert into history (tblname,cook,ip,id,field,valueold,valuenew)
                    values ('exciting','$ctext','$ipp',$excid,'Nshirota','$_ngr','$ngr')";
          $dbi->query($sql_in);
        }

        if ($egr <> $_egr) {
          $sql_in = "insert into history (tblname,cook,ip,id,field,valueold,valuenew)
                    values ('exciting','$ctext','$ipp',$excid,'Edolgota','$_egr','$egr')";
          $dbi->query($sql_in);
        }

        if ($extype <> $_extype) {
          $sql_in = "insert into history (tblname,cook,ip,id,field,valueold,valuenew)
                    values ('exciting','$ctext','$ipp',$excid,'type','$_extype','$extype')";
          $dbi->query($sql_in);
        }

        if ($townid <> $_townid) {
          $sql_in = "insert into history (tblname,cook,ip,id,field,valueold,valuenew)
                    values ('exciting','$ctext','$ipp',$excid,'town','$_townid','$townid')";
          $dbi->query($sql_in);

          if (($townhide != 0) and ($townid != 0)) {
            // добавляем примечательность к городу, у которого hide <>0
            if ($townhide == 1) $dbi->query("update towns set hide = 2 where id = $townid");
            else $dbi->query("update towns set hide = 0 where id = $townid");
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

          // обновляем extypes при изменении town/area/type/kind
          if (($townid <> $_townid) or ($areaid <> $_areaid) or ($extype <> $_extype) or ($kind <> $_typexs)) {
            extypes_upd($excid,$townid,$areaid,$extype,$subvals);
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
          $subvals  = array();
          $kind     = "";

        } else {
          echo "<h3>При изменении произошла ошибка\n<BR>";
          echo $dbi->errno.": ".$dbi->error."</h3>";
        }
      }
    }
  }

  // ---------- РЕНДЕР ПОДТИПОВ (универсально) ----------
  $subtype_blocks_html = ex_render_subtype_blocks($types_with_subtypes, $extype, $subvals);

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

  } else {

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
    } else {
      echo "<input type=text name=ngrw value='$ngrw'>&nbsp;&nbsp;<b>E</b> <input type=text name=egrw value='$egrw'>\n";
    }
    echo "<input type=checkbox name=exact value=100 ";
    if ($exact == 1) { echo " CHECKED "; }
    echo ">Координаты проверены</TD></TR>\n";
    echo "<TR><td width=260 class=travel1 align=right>Описание:&nbsp;</TD>\n";
    echo "<TD><TEXTAREA name=extext rows=10  style=\"width:100%\" wrap=none>\n";
    if ($extext) {
      echo str_replace("[br]","\n",$extext);
    }
    echo "</TEXTAREA>\n";
    if (preg_match("/\,admin\,/i",$all_rights)) {
      echo "<br><input type=checkbox name=emoderated value=100 ";
      if ($emoderated == 1) { echo " CHECKED "; }
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
    if  ($mainid < 0 ) { echo " CHECKED "; }
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

    // --- универсальные подтипы ---
    echo "<TR><td width=260 class=travel1 align=right>Подтипы:&nbsp;</TD>\n";
    echo "<TD>";
    echo $subtype_blocks_html;
    echo "<script>exShowSubtypes(".(int)$extype.");</script>";
    echo "</TD></TR>\n";

    echo "<TR><td>&nbsp;</TD><TD><INPUT name=submitexc type=submit value='Изменить'>\n";
    echo "</TD></TR>\n";
    echo "</TABLE></FORM>\n";
  }
}
// полномочия по изменению объектов - end


//////////////////////////////////////////////////////////////////////////////
?>
