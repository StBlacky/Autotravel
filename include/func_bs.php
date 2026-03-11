<?php
function show_panel($title,$titletag,$body,$opened,$label,$titleadd = '') {
  $res = '<div class="row"><div class="col-md-12"><div class="panel">';
  $res .= '<div class="panel-heading">';
  if ($label) {
   $res .= '<button type="button" class="btn btn-xs btn-info pull-right" data-toggle="collapse" data-target="#'.$label.'">';
   if ($opened) {
    $res .= '<span id="txt-'.$label.'" class="chevron">Спрятать</span><span id="ico-'.$label.'" class="glyphicon glyphicon-chevron-up"></span></button>';
   }
   else {
    $res .= '<span id="txt-'.$label.'" class="chevron">Раскрыть</span><span id="ico-'.$label.'" class="glyphicon glyphicon-chevron-down"></span></button>';
   }
   $res .= $titleadd.'<'.$titletag.' id="title-'.$label.'">';
  } 
  else {
   $res .= $titleadd.'<'.$titletag.'>';
  }
  $res .= $title;
  if ($label) {
   if ($opened) {
    $res .= '</'.$titletag.'></div><div id="'.$label.'" class="panel-collapse collapse in tocollapse"><div class="panel-body travell0 tj" id="content-'.$label.'">';
   }
   else {
    $res .= '</'.$titletag.'></div><div id="'.$label.'" class="panel-collapse collapse tocollapse"><div class="panel-body travell0 tj" id="content-'.$label.'">';
   }
  } 
  else {
    $res .= '</'.$titletag.'></div><div class="panel-body travell0 tj" id="content-'.$label.'">';    
  }
  $res .= $body;
  if ($label) {
   $res .= '</div></div></div></div></div>';
  }
  else {
   $res .= '</div></div></div></div>';
  }
  return $res;
}

/**
 * Выводит в html часть bootstrap формы с полем ввода
 */
function my_echo_input($field) {
  $name = $field['name'];
  $type = $field['type'];
  $default = "";
  if (isset($field['default'])) {
    $default = $field['default'];
  }
  $place = "";
  if ($type === "hidden") {
    return "<input type=\"$type\" name=\"$name\" value=\"$default\">\n";
  }
  $label = $field['label'];
  if (isset($field['placeholder'])) {
    $place = " placeholder=\"".$field['placeholder']."\" ";
  }
  $res = "<div class=\"form-horizontal\"><div class=\"form-group\"><label for=\"f_".$name."\" class=\"col-sm-3\"><span class=\"pull-right\">$label</span></label><div class=\"col-sm-9\">\n";
  if ($type === "textarea") {
    $res .= "<textarea $place class=\"form-control\" id=\"f_".$name."\" name=\"$name\">".$default."</textarea>\n";
  }
  elseif ($type === "select") {
    $res .= "<select class=\"form-control\" name=\"$name\">";
    $res .= show_int_options($default, $field['options']);
    $res .= "</select>\n";
  }
  elseif ($type === "password") {
    $res .= "<input type=\"password\" $place class=\"form-control\" id=\"f_".$name."\" name=\"$name\" value=\"".$default."\">\n";
  }
  else {
    if ($type === 'date') {
      $class = '"form-control mydatepick"';
    }  
    else {
      $class = '"form-control"';
    }
    $res .= "<input type=\"text\" $place class=$class id=\"f_".$name."\" name=\"$name\" value=\"".$default."\">\n";
  }
  $res .= "</div></div></div>\n";
  return $res;
}

function my_getstate() {
  return hash('ripemd256','2985092843098757651'.$_SERVER["HTTP_X_REAL_IP"].$_SERVER["HTTP_USER_AGENT"]);
}

function get_ex_rate($id) {
  global $dbi;
  $id = (int)$id;

  $sql = "select wrate from exciting where id = $id";
  $res = $dbi->query($sql);
  if ($res && $row = mysqli_fetch_array($res)) {
    return sprintf("%01.2f", $row['wrate']);
  }
  return '';
}

function bs_stars($count) {
  $ii = 0;
  $res = "";
  $limit = (int) $count;
  while ($ii < $limit) {
    $res .= "&#9734;";
    $ii++;
  }
  return $res;
}


function show_half_panel($title,$titletag,$body,$opened,$label,$part,$large=0) {
  if ($large) {  
    $res = '<div class="col-lg-'.$part.' col-md-12 col-xs-12"><div class="panel">';
  }
  else {
    $res = '<div class="col-md-'.$part.' col-xs-12"><div class="panel">';
  }  
  if ($label) {
    $res .= '<div class="panel-heading"><button type="button" class="btn btn-xs btn-info pull-right" data-toggle="collapse" data-target="#'.$label.'">';
    if ($opened) {
      $res .= '<span id="txt-'.$label.'" class="chevron">Спрятать</span><span id="ico-'.$label.'" class="glyphicon glyphicon-chevron-up"></span></button><'.$titletag.' id="title-'.$label.'">';
    }
    else {
      $res .= '<span id="txt-'.$label.'" class="chevron">Раскрыть</span><span id="ico-'.$label.'" class="glyphicon glyphicon-chevron-down"></span></button><'.$titletag.' id="title-'.$label.'">';
    }
    $res .= $title;
    if ($opened) {
      $res .= '</'.$titletag.'></div><div id="'.$label.'" class="panel-collapse collapse in tocollapse"><div class="panel-body travell0 tj">';
    }
    else {
      $res .= '</'.$titletag.'></div><div id="'.$label.'" class="panel-collapse collapse tocollapse"><div class="panel-body travell0 tj">';
    }
  }
  else {
    if ($title) {
      $res .= '<div class="panel-heading">';
      $res .= '<'.$titletag.'>';    
      $res .= $title;
      $res .= '</'.$titletag.'></div>';
    }
    $res .= '<div><div class="panel-body travell0 tj">';
  }
  $res .= $body;
  $res .= '</div></div></div></div>';
  return $res;
}

function getrtypes_bs() {
  Global $dbi;
  $r_types=Array();
  $sql_fd = "select longname, allname, name, id from type order by srtfld";
  $result_fd = $dbi->query($sql_fd);
  while ($row_trip = mysqli_fetch_array($result_fd)) {
    $r_types[$row_trip["id"]] = Array("name"=>$row_trip["name"],"longname"=>$row_trip["longname"],"allname"=>$row_trip["allname"]);
  }
  mysqli_free_result($result_fd);
  return $r_types;
}

function gettowntext_bs($id) {
  Global $dbi, $_desc_mode, $bootstrap;
  if ($_desc_mode == 1) {
    return "";  
  }
  $name = "";
  $sql_r = "select descr, cook from l_text where obj = 't" . $id . "' and dtizm > 00000000000000";
  $result_r = $dbi->query($sql_r);
  if ($myrow_r = mysqli_fetch_array($result_r)) {
    $name = $myrow_r["descr"];
    if ($_desc_mode) {
      $name = truncastring($name, 1000, 'townt',"tv");
    }
    $name .= "<div class=\"tb pull-right\">Автор: ". authform($myrow_r["cook"])."</div>";
  }
  mysqli_free_result($result_r);
  return $name;
}

function getexcount_bytown($town, $type) {
  Global $dbi;
  $sql = "select count(*) as excount from exciting where town=$town and type=$type";
  $result = $dbi->query($sql);
  if ($myrow = mysqli_fetch_array($result)) {
    do {
      $cnt = (int) $myrow["excount"];
    } while ($myrow = mysqli_fetch_array($result));
  }
  return $cnt;
}  

/*
 * HTML код <option> элементов по массиву для фильтра
 * описаний видов примечательностей и сервиса (kind)
 * $typex  - список кодов
 * $prefix - префикс для value
 * $preselected - массив
 */
function gettypex_bs($typex, $prefix, $preselected) {
  Global $dbi;
  $typexid = 0;
  $typex_txt = "";
  $_desc = "";
  $sql = "select `id`,`desc`,`type` from type_ex where id in ( $typex ) order by `desc`,`type` asc";
  $result = $dbi->query($sql);
  if ($myrow = mysqli_fetch_array($result)) {
    do {
      $tid = (string) $myrow["id"];
      $tname = $myrow["type"];
      if (!($_desc == $myrow["desc"])) {
        if ($_desc) {
           $typex_txt .= "</optgroup>\n";
        }
        $_desc = $myrow["desc"];
        $typex_txt .= "<optgroup label=\"$_desc\">\n";
      }
      $typex_txt .= "<option value='".$prefix.$tid."' ";
      if (empty($preselected)) {
        $typex_txt .= "SELECTED";
      }
      else {
        if (in_array($tid, $preselected)) {
          $typex_txt .= "SELECTED";
        }
      }
      $typex_txt .= ">$tname</option>\n";
    } while ($myrow = mysqli_fetch_array($result));
  }
  if ($_desc) {
    $typex_txt .= "</optgroup>\n";
  }
  mysqli_free_result($result);
  return $typex_txt;
}

/*
 * HTML код <option> элементов по массиву для фильтра
 * описаний видов примечательностей и сервиса (kind)
 * $typex  - список кодов
 * $prefix - префикс для value
 * $preselected - массив
 */
function getkindstxt_bs($typex) {
  Global $dbi;
  if (empty($typex)) {
    return '';
  }
  $typexv = '';
  foreach ($typex as $t) {
    if ($typexv) {
      $typexv .= ", '".$t."'";
    }
    else {
      $typexv = "'".$t."'";
    }
  }
  $typex_txt = array();
  $_desc = "";
  $sql = "select `id`,`type` from type_ex where id in ( $typex ) order by `type` asc";
  $result = $dbi->query($sql);
  while ($myrow = mysqli_fetch_array($result)) {
      $typex_txt[] = $myrow["type"];
  }
  mysqli_free_result($result);
  return implode("; ",$typex_txt);
}


function bs_calc_rating($votes, $wrate) {
  Global $highrate;
  if ($votes == 0) {
    return 0;
  }
  $m = (int) $wrate;
  $calc = 0;
  if ($wrate > ($highrate * 5)) {
    $calc = 5;
  }
  if ($wrate > ($highrate * 3)) {
    $calc = 4;
  }
  if ($wrate < 0 ) {
    $calc = 1;
  }
  if ($calc == 0) {
    $calc = (int) (1 + ($wrate / $votes) );
    if ($calc < 1) {
      $calc = 1;
    }
    if ($calc > 5) {
      $calc = 5;
    }
  }
  return $calc;
}

function bs_getotklik($ide) { //возвращает массив откликов
  Global $dbi, $authtype, $forum_user_id, $ctext, $_has_otkl, $_has_mark, $_exct_mode, $_otkl_mode, $forumname, $_user_is_admin, $_moderated, $_bad_count;
  $_has_otkl = "";
  $_has_mark = "";
  $otkl = array();
  $sqladd = "";
  $sqladdx = "";
  $ii = 0;
  if ($_exct_mode<0) {
    return $otkl;
  }
  if ($_exct_mode>0) {
    $sqladdx = " and o.cook like 'forum:%' ";
  }
  $sql_o = "select FROM_UNIXTIME(o.time, '%e-%m-%Y') as o_date, o.time as o_time, o.nick as nick,
           o.mail as mail, o.descrip as descrip, o.comment as comment, o.mark as mark, o.cook as cook, o.oid as oid
           from otklik o where o.exciting = '$ide'";
  if ((($_moderated) or ($_bad_count>0)) and ($sqladdx=="") and ($sqladd=="")) {
    $sql_o = $sql_o . " UNION select FROM_UNIXTIME(om.time, '%e-%m-%Y') as o_date, om.time as o_time, om.nick as nick,
           om.mail as mail, om.descrip as descrip, \"\" as comment, om.mark as mark, om.cook as cook, 0 as oid
           from otklikm om where om.exciting = '$ide' and om.cook='$ctext' order by o_time desc";      
  }
  else {
    $sql_o = $sql_o . " $sqladdx order by o.time desc $sqladd";      
  }
  //echo "<!-- $sql_o -->";
  $result_o = $dbi->query($sql_o);
  if (!($result_o)) {
    return $otkl;
  }
  if (mysqli_num_rows($result_o) == 0 ) {
    return $otkl;
  }
  while ($myrow_o = mysqli_fetch_array($result_o)) {
    $s_date = $myrow_o["o_date"];
    $s_time = $myrow_o["o_time"];
    $s_description = $myrow_o["descrip"];
    $s_cook = $myrow_o["cook"];
    $s_oid = $myrow_o["oid"];
    $s_mark = $myrow_o["mark"];
    $_ext_otkl = 1;
    if ($s_cook == $ctext) {
      $_ext_otkl = 0;
      $_has_otkl = $s_description;
      $_has_mark = $s_mark;
      $_has_otkl = my_unhtml($_has_otkl);
      $_has_otkl = str_replace("[BR]", "[br]", $_has_otkl);
      $_has_otkl = str_replace("[br]", "\n", $_has_otkl);
    }
    if (preg_match("/forum\:(\d+)\:(.+)/", $s_cook, $s_match)) {
      //$s_nick = "<a class=travell5r href=\"".$forumname. "/memberlist.php?mode=viewprofile&amp;u=" . $s_match[1] . "\">" . $s_match[2] . "</a>";
      $s_nick = "<a class=travell5r onclick=\"PoLoad($(this))\" data-poload=\"/profile.php/" . $s_match[1] . "\">" . $s_match[2] . "</a>";  
    }
    else {
      $s_nick = "<span class=\"t0 to tb\">".$myrow_o["nick"]."</span>";      
    }
    $s_comment = $myrow_o["comment"];
    if (preg_match("/^partner\:(\d+)\:(.+)/", $s_comment, $s_match)) {
      $s_comment = "<br><hr><b>Комментарий партнера:</b> " . $s_match[2] . "\n";
    } 
    else {
      if (preg_match("/^admin\:(\d+)\:(.+)/", $s_comment, $s_match)) {
        $s_comment = "<br><hr><b>Комментарий администрации сайта:</b> " . $s_match[2] . "\n";
      }
      else {
        $s_comment = "";
      }
    }
    if ($authtype == 'partner') {
      // открыты комменты от партнеров
      $addform = "<br><form method=post action=" . $_SERVER["PHP_SELF"] . " name=\"addcomment$ide\">\n";
      $addform .= "<input type=hidden name=id value=$ide>\n";
      $addform .= "<input type=hidden name=stime value=$s_time>\n";
      $addform .= "<input type=hidden name=scook value='$s_cook'>\n";
      $addform .= "<input type=submit name=submitcomment value=Комментировать></FORM>\n";
    }  
    else {
      $addform = "";
    }
    // устрицам можно удалять каменты !!!
    if ($_user_is_admin == '1') {
      $addform = "<br><a class=atc href=\"/sitemgr.php?admt=delcomment&amp;exc=$ide&amp;scook=$s_cook&amp;stime=$s_time\">Стереть</a>\n";
    }

    if (!($_otkl_mode)) {
      $s_description = truncastring($s_description, 500, $s_oid,"zf");
      $s_comment = truncastring($s_comment, 500, $s_oid,"zs");
    }
    
    // оценка отзыва
    $otksc = "";
    if ($forum_user_id > 0) {
      if ($_ext_otkl == 1) { // кроме своих
        $sqlsc = "select score from score where type = \"excite\" and idtype=$s_oid and cook=\"$ctext\"";
        $resultsc = $dbi->query($sqlsc);
        $score="";
        if ($myrowsc = mysqli_fetch_array($resultsc)) {
          $score = $myrowsc["score"];
          mysqli_free_result($resultsc);
        }
        $otksc="<div id=\"score$s_oid\">";
        $plus="aplus.png";
        $minus="aminus.png";
        if ($score==1) {
          $plus="plus.png";
        }
        if ($score==-1) {
          $minus="minus.png";
        }
        // тут можно сделать разноцветные иконки. и в явоскрипте их тоже менять
        $otksc .= "<img src=\"/png/$plus\" id=\"plus".$s_oid."\" onclick=\"buttonclick('1','$s_oid')\">&nbsp;";
        $otksc .= "<img src=\"/png/$minus\" id=\"minus".$s_oid."\" onclick=\"buttonclick('-1','$s_oid')\">";
        $otksc .= "</div>\n";
      }
    }       
    // закончили оценку отзыва
    
    $otkl[$ii]["nick"] .= $s_nick;
    $otkl[$ii]["mark"] .= $s_mark;
    $otkl[$ii]["date"] .= $s_date;
    $otkl[$ii]["time"] .= $s_time;
    $otkl[$ii]["score"] .= $otksc;
    $otkl[$ii]["comment"] .= $s_description;
    $otkl[$ii]["addon"] .= $s_comment.$addform;
    $ii++;
  }
  mysqli_free_result($result_o);
  return $otkl;
}

function s_stars($count) {
  $ii = 0;
  $res = "";
  $limit = (int) $count;
  while ($ii < $limit) {
    $res .= "&#9733;";
    $ii++;
  }
  while ($ii < 5) {
    $res .= "&#9734;";
    $ii++;
  }
  return $res;
}

function getallfotoex($id, $lim) {
  Global $dbi, $_foto_mode, $sitename;
  $foto = array(); 
  $fotos = "";
  $display = 0; 
  $hidden = 0;
  if ($_foto_mode) {
    $sqladd = "";
  } else {
    $sqladd = " and f.mode > 0 ";
  }
  $sql = "select f.file as file, f.mode as mode, f.author as author,f.comment as comment,
         e.name as exname, e.type as extype, e.town as town
         from foto f, exciting e
         where e.id = $id and f.exciting = $id $sqladd
         order by f.mode desc, f.date desc";
  $result = $dbi->query($sql);
  $numrows = mysqli_num_rows($result);
  $town = "";
  $iii = 0;
  while ($myrow = mysqli_fetch_array($result)) {
    $file = $myrow["file"];
    $mode = $myrow["mode"];
    $exname = $myrow["exname"];
    $extype = $myrow["extype"];
    $comment = $myrow["comment"];
    $exname = str_replace("\"", "&quot;", $exname);
    $exname = str_replace("\'", "&quot;", $exname);
    if (!($town)) { // не бывает так чтобы примечательность была одна, а городов много
      $town = $myrow["town"];
      $tnname = gettownname($town);
    }
    $author = authformat($myrow["author"],"nolink");
    if ($comment) {
        $title = $exname . ": " . $comment . " Фото: " . $author;
        $alt = $exname . " (" . $tnname . "): " .  $comment;
    } else {
        $title = $exname . " (" . $tnname . ") Фото: " . $author;
        $alt = $exname . " (" . $tnname . ")";
    }
    $foto[$iii]  = "<div><a title='$title' ref='object' class='atcbox' href='/phalbum/$file.jpg'><img src=\"/phalbum/$file-s.jpg\"></a></div>\n";
    $iii++;
    if (($extype != 2) and ($extype != 3)) {
      if (!$display) {
        $itempropfotos = "itemprop=\"photo\" content=\"https://autotravel.ru/phalbum/$file.jpg\""; //image - микроразметка для отклика, photo для организации
        $foto1 = "https:".$sitename."/phalbum/".$file.".jpg"; // фото для фейсбука
      } 
      $display++;
    }
    else {
      $hidden++;
    }
  }
  mysqli_free_result($result);
  if ($iii) {
    $fotos .= "<div id=\"fotoslider\">\n";
    foreach ($foto as $f) {
      $fotos .= $f;
    }
    $fotos .= "</div>\n";
  }
  return Array("foto" => $fotos,
    "display" => $display,
    "hidden" => $hidden,
    "foto1" => $foto1,
    "town" => $town,
    "townname" => $tnname,
    "exname" => $exname,
    "extype" => $extype,
    "itempropfotos" => $itempropfotos);  //для микроразметки в отклике (организация), может, еще где пригодится
}


function kmlfooter_bs($ids, $header, $text)
{
    $h = htmlspecialchars($header, ENT_QUOTES, 'UTF-8');

    $v  = "<div class=\"card-block mt-2 text-small\">";

    // подпись
    if ($text) {
        $v .= "<div class=\"mb-2\">$text: передать в навигатор</div>";
    }

    // --- формы экспорта ---
    $v .= "<div class=\"mb-2\">";

    // KML / GPX
    $v .= "<form class=\"d-inline\" method=\"post\" action=\"/kml.php\" target=\"_blank\">";
    $v .= "<input type=\"hidden\" name=\"ids\" value=\"$ids\">";
    $v .= "<input type=\"hidden\" name=\"resheader\" value=\"$h\">";
    $v .= "<button class=\"btn-light\" type=\"submit\" name=\"f\" value=\"В формате Google KML\">KML</button> ";
    $v .= "<button class=\"btn-light\" type=\"submit\" name=\"f\" value=\"В формате Garmin GPX\">GPX</button>";
    $v .= "</form>";

    // разделитель
    $v .= "<span class=\"text-muted\"> &nbsp;или&nbsp; </span>";

    // Показать на карте
    $v .= "<form class=\"d-inline\" method=\"get\" action=\"/map.php\" target=\"_blank\">";
    $v .= "<input type=\"hidden\" name=\"ids\" value=\"$ids\">";
    $v .= "<button class=\"btn-outline\" type=\"submit\">Показать на карте</button>";
    $v .= "</form>";

    $v .= "</div>"; // mb-2
    $v .= "</div>"; // card-block

    return $v;
}

function getgrdescr($id, $var) {
  Global $dbi;
  $sql = "select name from exciting where mainid = $id order by wrate desc";
  $result = $dbi->query($sql);
  $numrows = mysqli_num_rows($result);
  $v = 'Сгруппировано '.$numrows.' '.echo_int($numrows, array("точек","точка","точки"));
  
  $j=0;
  if ($var) {
    $v .= ', в том числе:';
    while (($myrow = mysqli_fetch_array($result)) and ($j<$var)) {
      $name = $myrow["name"];
      $v .= '<br><strong>'.$name.'</strong>';
      $j=$j+1;
    }
  }
  return $v;
  mysqli_free_result($result);
}

function get1fotoex_bs($id, $lim, $var, $ffile = '') {
  Global $dbi, $_foto_mode, $sitename, $colorbox;
  $fotos = "";
  $lim2 = $lim;
  if ($colorbox == 2) {
    if ($lim < 3) {
      $lim2 = 3;
    }
  }
  $limx = $lim2 * 5;
  if ($_foto_mode) {
   $sqladd = "";
  } else {
   $sqladd = " and f.mode > 0 ";
  }
  if ($ffile) {
    $sqlsort = " (f.file='".$ffile."') desc, ";  
  }
  else {
    $sqlsort = "";  
  }
  $sql = "select f.file as file, f.mode as mode, f.author as author,
         e.name as exname, e.type as extype, e.town as town
         from foto f, exciting e
         where e.id = $id and f.exciting = $id $sqladd
         order by $sqlsort f.mode desc, f.date desc limit $limx";
  $result = $dbi->query($sql);
  $numrows = mysqli_num_rows($result);
  $ii = 0;
  while ($myrow = mysqli_fetch_array($result)) {
    $file = $myrow["file"];
    $mode = $myrow["mode"];
    $exname = $myrow["exname"];
    $extype = $myrow["extype"];
    $exname = str_replace("\"", "&quot;", $exname);
    $exname = str_replace("\'", "&quot;", $exname);
    $town = $myrow["town"];
    $author = authformat($myrow["author"],"nolink");
    $tnname = gettownname($town);

    if (($extype == 1) or ($var == 2) or ($var == 3)) {
      if ($ii < $lim2) {
        if (!($var)) {
          $fotos .= "<a title='$exname $tnname Фото: $author' ref='object' ";
          if ($colorbox == 2) {
            $fotos .= "rel='exf-".$id."' ";
          }
          $fotos .= "class='atcbox' href='/phalbum/$file.jpg'>";
          if ($ii<$lim) {
            $fotos .= "<img itemprop=\"photo\" class='latcbox' alt='$tnname: Достопримечательность $exname' src='/phalbum/$file-s.jpg'>";
          }
          $fotos .= "</a>\n";
        }
        if ($var == 1) {
          $fotos = "https:".$sitename."/phalbum/".$file.".jpg";  
        }
        if ($var == 2) {
          //$fotos .= "<a rel=\"nofollow\" href='/otklik.php/$id'>";
          if ($ii<$lim) {
            $fotos .= "<img style=\"float:left; padding-right:10px\" alt='$exname' src='/phalbum/$file-s.jpg'>";
          }
          //$fotos .= "</a>\n";
        }
        if ($var == 3) {
          //$fotos .= "<a rel=\"nofollow\" href='/otklik.php/$id'>";
          if ($ii<$lim) {
            $fotos .= "<img alt='$exname' src='/phalbum/$file-s.jpg'>";
          }
          //$fotos .= "</a>\n";
        }
      }
      $ii++;
    }
  }
  mysqli_free_result($result);
  return $fotos;
}

function get1grfotoex_bs($id, $lim, $var, $ffile = '') {
  Global $dbi, $_foto_mode, $sitename, $colorbox;
  $fotos = "";
  $lim2 = $lim;
  if ($colorbox == 2) {
    if ($lim < 3) {
      $lim2 = 3;
    }
  }
  $limx = $lim2 * 5;
  if ($_foto_mode) {
   $sqladd = "";
  } else {
   $sqladd = " and f.mode > 0 ";
  }
  if ($ffile) {
    $sqlsort = " (f.file='".$ffile."') desc, ";  
    $sql = "select q.* from (
            select f.file as file, f.mode as mode, f.author as author, f.date as date,
            e.name as exname, e.type as extype, e.town as town
            from foto f, exciting a
            left outer join exciting e on e.mainid=a.id
            where a.id = $id and f.exciting = e.id $sqladd 
            union    
            select f.file as file, f.mode as mode, f.author as author, f.date as date,
            e.name as exname, e.type as extype, e.town as town
            from foto f, exciting e
            where e.id = $id and f.file = '".$ffile."' ) q 
            order by (q.file='".$ffile."') desc, q.mode desc, q.date desc limit $limx";
  }
  else {
    $sql = "select f.file as file, f.mode as mode, f.author as author,
            e.name as exname, e.type as extype, e.town as town
            from foto f, exciting a
            left outer join exciting e on e.mainid=a.id
            where a.id = $id and f.exciting = e.id $sqladd
            order by f.mode desc, f.date desc limit $limx";
  }
  //error_log($sql);
  $result = $dbi->query($sql);
  $numrows = mysqli_num_rows($result);
  $ii = 0;
  while ($myrow = mysqli_fetch_array($result)) {
    $file = $myrow["file"];
    $mode = $myrow["mode"];
    $exname = $myrow["exname"];
    $extype = $myrow["extype"];
    $exname = str_replace("\"", "&quot;", $exname);
    $exname = str_replace("\'", "&quot;", $exname);
    $town = $myrow["town"];
    $author = authformat($myrow["author"],"nolink");
    $tnname = gettownname($town);

    if (($extype == 1) or ($var == 2) or ($var == 3)) {
      if ($ii < $lim2) {
        if (!($var)) {
          $fotos .= "<a title='$exname $tnname Фото: $author' ref='object' ";
          if ($colorbox == 2) {
            $fotos .= "rel='exf-".$id."' ";
          }
          $fotos .= "class='atcbox' href='/phalbum/$file.jpg'>";
          if ($ii<$lim) {
            $fotos .= "<img itemprop=\"photo\" class='latcbox' alt='$tnname: Достопримечательность $exname' src='/phalbum/$file-s.jpg'>";
          }
          $fotos .= "</a>\n";
        }
        if ($var == 1) {
          $fotos = "https:".$sitename."/phalbum/".$file.".jpg";  
        }
        if ($var == 2) {
          //$fotos .= "<a rel=\"nofollow\" href='/otklik.php/$id'>";
          if ($ii<$lim) {
            $fotos .= "<img style=\"float:left; padding-right:10px\" alt='$exname' src='/phalbum/$file-s.jpg'>";
          }
          //$fotos .= "</a>\n";
        }
        if ($var == 3) {
          //$fotos .= "<a rel=\"nofollow\" href='/otklik.php/$id'>";
          if ($ii<$lim) {
            $fotos .= "<img alt='$exname' src='/phalbum/$file-s.jpg'>";
          }
          //$fotos .= "</a>\n";
        }
      }
      $ii++;
    }
  }
  mysqli_free_result($result);
  return $fotos;
}


function echo_int($val, $arr) {
  $v = ($val % 10);
  $vv = ($val % 100);
  if ($v == 1) {
    if ($vv == 11) {  
      return $arr[0];
    }
    return $arr[1];
  }
  if (($v == 2) or ($v == 3) or ($v == 4)) {
    if (($vv == 12) or ($vv == 13) or ($vv == 14)) {  
      return $arr[0];
    }
    return $arr[2];
  }
  return $arr[0];
}

function getareasresult($result,$block) {
  Global $thighrate;
  $tbltxt = "";
  $lx = "";
  if($myrow = mysqli_fetch_array($result)) {
    do {
      $arean = $myrow["area"];
      $aid = $myrow["aid"];
      $dclass = "travell9c";
      $aclass="travell5n";
      $cclass="travell9b";
      if ($block) {
        $lc = mb_substr($arean, 0, $block);
        if (!($lx)) {
          $tbltxt .= "<div class=\"tblock\">";
          $lx = $lc;
        }
        if ($lx != $lc) {
          $tbltxt .= "</div>\n<div class=\"tblock\">";
          $lx = $lc;
        }
      }
      $capital = $myrow["capital"];
      if ($capital) {
        $tcap=gettownnamearr($capital);
        if ($tcap[10]) {
          $capitalname = " <span class=travell0>(столица  <a class=\"$cclass\" href=\"/gorod/".$tcap[10].".html\">".$tcap[3]."</a>)</span>";
        }
        else {
          $capitalname = " <span class=travell0>(столица  <a class=\"$cclass\" href=\"/town.php/$capital\">".$tcap[3]."</a>)</span>";
        }
      }
      else {
        $capitalname = "";
      }
      $tbltxt .= "<div class=\"$dclass\"><a class=\"$aclass\" href=\"/area.php/$aid\">$arean</a>$capitalname</div>\n";
      $capitalname='';
    } while($myrow = mysqli_fetch_array($result));
    if ($block) {
      $tbltxt .= "</div>\n";
    }
    mysqli_free_result($result);
  }  
  return $tbltxt;
}


function gettownsresult($result,$block) {
  Global $thighrate;
  $tbltxt = "";
  $lx = "";
  if($myrow = mysqli_fetch_array($result)) {
    do {
      $s_town = cyrname($myrow["town"]);
      if ($block) {
        $lc = mb_substr($s_town, 0, $block);
        if (!($lx)) {
          $tbltxt .= "<div class=\"tblock\">";
          $lx = $lc;
        }
        if ($lx != $lc) {
          $tbltxt .= "</div>\n<div class=\"tblock\">";
          $lx = $lc;
        }
      }
      $s_hide = $myrow["hide"];
      $s_url = $myrow["turl"];
      $s_townid = $myrow["tid"];
      if ($s_hide == 2) { //cовсем мелкие
        $dclass="travell9m";        
        $aclass="travell9s";
      } 
      else {
        $dclass="travell9n"; //обычные
        $aclass="travell9b";
      }
      $s_rate = $myrow["rate"];
      if ($s_rate > ($thighrate)) { //популярные
        $dclass = "travell9c";
        $aclass="travell5n";
      }
      else {
        $class = "travell9n";
      }
      $tbltxt .= "<div class=\"$dclass\"><a class=\"$aclass\" href=\"";
      if ($s_url) {
        $tbltxt .= "/gorod/".$s_url.".html";
      }
      else {
        $tbltxt .= "/town.php/$s_townid";
      }
      $tbltxt .= "\"> $s_town </a>";
      $s_area = $myrow["area"];
      $tbltxt .= " <span>($s_area)</span>";
      $tbltxt .= "</div>\n";
    } while($myrow = mysqli_fetch_array($result));
    if ($block) {
      $tbltxt .= "</div>\n";
    }
    mysqli_free_result($result);
  }  
  return $tbltxt;
}


function gettownsresultex($result, $block) {
  global $thighrate;
  $tbltxt = "";
  $lx = "";

  if ($myrow = mysqli_fetch_array($result)) {

    // --- открываем контейнер ItemList ---
    $tbltxt .= "<div itemscope itemtype=\"https://schema.org/ItemList\">\n";
    $i = 1;

    do {
      $s_town   = cyrname($myrow["town"]);
      $s_hide   = $myrow["hide"];
      $s_townid = $myrow["tid"];
      $s_rate   = $myrow["rate"];
      $s_area   = $myrow["area"];

      if ($block) {
        $lc = mb_substr($s_town, 0, $block);
        if (!($lx)) {
          $tbltxt .= "<div class=\"tblock\">\n";
          $lx = $lc;
        }
        if ($lx != $lc) {
          $tbltxt .= "</div>\n<div class=\"tblock\">";
          $lx = $lc;
        }
      }

      // Определяем CSS-классы
      if ($s_hide == 2) {
        $dclass = "travell9m";
        $aclass = "travell9s";
      } else {
        $dclass = "travell9n";
        $aclass = "travell9b";
      }
      if ($s_rate > ($thighrate)) {
        $dclass = "travell9c";
        $aclass = "travell5n";
      }

      // --- разметка каждого города как ListItem ---
      $tbltxt .= "<div class=\"$dclass\" itemprop=\"itemListElement\" itemscope itemtype=\"https://schema.org/ListItem\">";
      $tbltxt .= "<meta itemprop=\"position\" content=\"$i\">";
      $tbltxt .= "<a class=\"$aclass\" itemprop=\"item\" href=\"/excite.php/$s_townid/1\">";
      $tbltxt .= "<span itemprop=\"name\">$s_town</span></a>";
      $tbltxt .= " <span>($s_area)</span>";
      $tbltxt .= "</div>\n";

      $i++;
      
    } while ($myrow = mysqli_fetch_array($result));

    if ($block) {
      $tbltxt .= "</div>\n";
    }
 
    
    // --- закрываем ItemList ---
    $tbltxt .= "</div>\n";

    mysqli_free_result($result);
  }

  return $tbltxt;
}
function kml_display($kml) {
  $kml_out = "<p class=travell0>";
  $kml_out .= "Скачать координаты достопримечательностей (POI) на навигатор в формате <a class=travell5n href='$kml" . ".wpt'>WPT</a>&nbsp;\n";
  $kml_out .= "<a class=travell5n href='$kml" . ".kml'>KML</a>&nbsp;\n";
  $kml_out .= "<a class=travell5n href='$kml" . ".gpx'>GPX</a>\n";
  $kml_out .= "</p>\n";
  return $kml_out;
}

function gettownex_bs($id) {
  Global $dbi;
  $name = Array();
  $name[0] = ""; // ex name
  $name[1] = ""; // town name
  $name[2] = ""; // area name
  $name[3] = ""; // town id 
  $name[4] = ""; // area id
  $name[5] = ""; // town name_to
  $name[6] = ""; // town name_from
  $name[7] = ""; // town name_po
  $name[8] = ""; // town hide
  $name[9] = ""; // town url
  $name[10] = ""; // short town url
  $name[11] = ""; // town name_around
  $name[12] = ""; // town name_in
  $name[13] = ""; // town ffoto
  $name[14] = ""; // ex ffoto
  $sql_r = "select e.name as ename, e.ffile as effoto, t.id as tid, t.name as tname, t.hide as thide, t.url as turl,
                  t.area as tarea, t.ffile as tffoto, t.nameto as tnameto, t.namefrom as tnamefrom, t.namepo as tnamepo,
                  t.namearound as tnamearound, t.namein as tnamein,
                  a.id as aid, a.name as aname from towns t, areas a, exciting e
                  where e.id = $id and t.id=e.town and a.id=t.area";
  $result_r = $dbi->query($sql_r);
  if ($myrow_r = mysqli_fetch_array($result_r)) {
    $ename = $myrow_r["ename"];
    $ename = str_replace("\"", "&quot;", $ename);
    $ename = str_replace("\'", "&quot;", $ename);
    $name[0] = $ename;
    $name[1] = $myrow_r["tname"];
    $name[2] = $myrow_r["aname"];
    $name[3] = $myrow_r["tid"];
    $name[4] = $myrow_r["aid"];
    if ($myrow_r["tnameto"]) {
      $name[5] = $myrow_r["tnameto"];
    }
    else {
      $name[5] = "город ".$name[1];
    }
    if ($myrow_r["tnamefrom"]) {
      $name[6] = $myrow_r["tnamefrom"];
    }
    else {
      $name[6] = "города ".$name[1];
    }
    if ($myrow_r["tnamepo"]) {
      $name[7] = $myrow_r["tnamepo"];
    }
    else {
      $name[7] = "городу ".$name[1];
    }
    $name[8] = $myrow_r["thide"];
    if (($myrow_r["tid"]) && ($myrow_r["tid"] != 9999) && ($myrow_r["thide"] == 0)) {
      if ($myrow_r["turl"]) {
        $name[9] = '/gorod/'.$myrow_r["turl"].'.html';
      }
      else {    
        $name[9] = '/town.php/'.$myrow_r["tid"];
      }
    }  
    $name[10] = $myrow_r["turl"];
    $name[11] = $myrow_r["tnamearound"];
    $name[12] = $myrow_r["tnamein"];
    $name[13] = $myrow_r["tffoto"];
    $name[14] = $myrow_r["effoto"];
  }
  mysqli_free_result($result_r);
  return $name;
}

function gettown_bs($id) {
  Global $dbi;
  $name = Array();
  $name[0] = ""; // ""
  $name[1] = ""; // town name
  $name[2] = ""; // area name
  $name[3] = ""; // town id 
  $name[4] = ""; // area id
  $name[5] = ""; // town name_to
  $name[6] = ""; // town name_from
  $name[7] = ""; // town name_po
  $name[8] = ""; // town hide
  $name[9] = ""; // town url as link
  $name[10] = ""; // town url as seo
  $name[11] = ""; // town name_around
  $name[12] = ""; // town name_in
  $name[13] = ""; // town ffoto
  $name[14] = ""; // ex ffoto
  $sql_r = "select t.id as tid, t.ffile as tffoto, t.name as tname, t.hide as thide, t.url as turl,
                  t.area as tarea, t.nameto as tnameto, t.namefrom as tnamefrom, t.namepo as tnamepo,
                  t.namearound as tnamearound, t.namein as tnamein,
                  a.id as aid, a.name as aname from towns t, areas a, exciting e
                  where t.id = $id and a.id=t.area";
  $result_r = $dbi->query($sql_r);
  if ($myrow_r = mysqli_fetch_array($result_r)) {
    $name[1] = $myrow_r["tname"];
    $name[2] = $myrow_r["aname"];
    $name[3] = $myrow_r["tid"];
    $name[4] = $myrow_r["aid"];
    if ($myrow_r["tnameto"]) {
      $name[5] = $myrow_r["tnameto"];
    }
    else {
      $name[5] = "город ".$name[1];
    }
    if ($myrow_r["tnamefrom"]) {
      $name[6] = $myrow_r["tnamefrom"];
    }
    else {
      $name[6] = "города ".$name[1];
    }
    if ($myrow_r["tnamepo"]) {
      $name[7] = $myrow_r["tnamepo"];
    }
    else {
      $name[7] = "городу ".$name[1];
    }
    $name[8] = $myrow_r["thide"];
    if (($myrow_r["tid"]) && ($myrow_r["tid"] != 9999) && ($myrow_r["thide"] == 0)) {
      if ($myrow_r["turl"]) {
        $name[9] = '/gorod/'.$myrow_r["turl"].'.html';
      }
      else {    
        $name[9] = '/town.php/'.$myrow_r["tid"];
      }
    }  
    $name[10] = $myrow_r["turl"];
    $name[11] = $myrow_r["tnamearound"];
    $name[12] = $myrow_r["tnamein"];
    $name[13] = $myrow_r["tffoto"];
    $name[14] = "";
  }
  mysqli_free_result($result_r);
  return $name;
}

function fotos_town($town, $tnname, $result) {
 Global $forum_user_id, $FOTOSM;   
 $sp_slides = "";
 $featured = "";
 $ii = 0;
 while($myrow = mysqli_fetch_array($result)) {
   $file   = $myrow["file"];
   $exciting= $myrow["exciting"];
   $author = $myrow["author"];
   $comment= $myrow["comment"];
   $mode   = $myrow["mode"];
   $author = authformat($author,"nolink");
   $exname = getexname($exciting);
   if (($exname== 'Нет названия') and ($comment)) {
    $exname = $comment;
   }
   $dcomment = $exname;
   if ($comment) {
    $dcomment .= "<br>".$comment ;
   }
   $dcomment .= "<br>Фото: <b>".$author."</b>";
   if (!($mode)) {
    $dcomment .= "<br>Не участвует в индексах<br>";
   }
   if ($forum_user_id) {
    $dcomment .= "<br>Линк для вставки фото в отчет: " .fotolink2($file);
   }
   if (!($featured)) {
     $featured = $file; // первый  
   }
   $sp_slides .= '<div class="tile">'."\n";
   $sp_slides .= '<a class="tile-inner js-lightbox" data-title=\''.$dcomment.'\' data-lightbox="gallery" href="/phalbum.php/'.$file.'" data-img="/phalbum/'.$file.'.jpg">';
//   $sp_slides .= '<a class="tile-inner" data-title=\''.$dcomment.'\' data-lightbox="gallery" href="/phalbum/'.$file.'.jpg">';
   $sp_slides .= "<img class=\"item\" src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7' ";
   $sp_slides .= " data-src='/phalbum/".$file."-s.jpg'>\n"; // 
   //$sp_slides .= '<div class="caption-block"><div class="text-wrapper"><h4 class="title">.'."$exname ($tnname)".'</h4>';
   //$sp_slides .= '<h5 class="subtitle">'.$dcomment.'</h5>';
   //$sp_slides .= "</div></div>\n";
   $sp_slides .= "</a>\n";
   $sp_slides .= "</div>\n";
   
 }
 return Array($sp_slides, $featured);
}

function count_fotos_area($area) {
  Global $dbi, $_foto_mode, $_fotomanager;  
  if ($_fotomanager) {
    $sqladd = "";
  }
  else {
    $sqladd = " mode > 0 and ";
  }
  if ($_foto_mode) {
    $sqladd = "";
  }
  
  $sql = "select count(*) as cnt from foto f, towns t
         where $sqladd f.town = t.id and t.area = $area";
  $result = $dbi->query($sql);
  if($myrow = mysqli_fetch_array($result)) {
    $cnt   = $myrow["cnt"];
  }
  else {
    $cnt = 0;  
  }
  mysqli_free_result($result);
  return $cnt;    
}

function count_ex_area($area) {
  Global $dbi;  
  $total = "";
  $sql = "SELECT e.type, y.*, count(*) as c FROM exciting e, towns t, type y where t.area in ($area) and t.id=e.town and y.id=e.type group by e.type order by srtfld;";
  $result = $dbi->query($sql);
  while ($myrow = mysqli_fetch_array($result)) {
    $cnt = $myrow["c"];
    $txt = $cnt." ".echo_int($cnt, array($myrow["name5"],$myrow["name1"],$myrow["name2"]));
    if ($total) {
      $total .= ", ".$txt;  
    }
    else {
      $total = $txt;
    }
  }
  mysqli_free_result($result);
  return $total;    
}

function count_ex_town($town) {
  Global $dbi;  
  $total = "";
  $sql = "SELECT e.type, y.*, count(*) as c FROM exciting e, type y where e.town in ($town) and y.id=e.type group by e.type order by srtfld;";
  $result = $dbi->query($sql);
  while ($myrow = mysqli_fetch_array($result)) {
    $cnt = $myrow["c"];
    $txt = $cnt." ".echo_int($cnt, array($myrow["name5"],$myrow["name1"],$myrow["name2"]));
    if ($total) {
      $total .= ", ".$txt;  
    }
    else {
      $total = $txt;
    }
  }
  mysqli_free_result($result);
  return $total;    
}

function count_towns_area($area) {
  Global $dbi;  
  $sql = "select count(*) as cnt from towns where hide=0 and area = $area";
  $result = $dbi->query($sql);
  if($myrow = mysqli_fetch_array($result)) {
    $cnt   = $myrow["cnt"];
  }
  else {
    $cnt = 0;  
  }
  mysqli_free_result($result);
  return $cnt;    
}

function count_fotos_town($town) {
  Global $dbi, $_foto_mode, $_fotomanager;  
  if ($_fotomanager) {
    $sqladd = "";
  }
  else {
    $sqladd = " mode > 0 and ";
  }
  if ($_foto_mode) {
    $sqladd = "";
  }
  
  $sql = "select count(*) as cnt from foto where $sqladd town = $town";
  $result = $dbi->query($sql);
  if($myrow = mysqli_fetch_array($result)) {
    $cnt   = $myrow["cnt"];
  }
  else {
    $cnt = 0;  
  }
  mysqli_free_result($result);
  return $cnt;    
}


function fotos_area($result) {
 Global $forum_user_id;   
 $sp_slides = "";
 while($myrow = mysqli_fetch_array($result)) {
   $file   = $myrow["file"];
   $exciting= $myrow["exciting"];
   $tname= $myrow["tname"];
   $author = $myrow["author"];
   $comment= $myrow["comment"];
   $mode   = $myrow["mode"];
   $author = authformat($author,"nolink");
   $exname = getexname($exciting);
   if (($exname== 'Нет названия') and ($comment)) {
    $exname = $comment;
   }
   $dcomment = $exname . ' ('.$tname.')';
   $dcomment .= "<br>Фото: <b>".$author."</b>";
   if (!($mode)) {
    $dcomment .= "<br>Не участвует в индексах<br>";
   }
   if ($forum_user_id) {
    $dcomment .= "<br>Линк для вставки фото в отчет: " .fotolink2($file);
   }
   $sp_slides .= '<div class="tile">'."\n";
   $sp_slides .= '<a class="tile-inner js-lightbox" data-title=\''.$dcomment.'\' data-lightbox="gallery" href="/phalbum.php/'.$file.'" data-img="/phalbum/'.$file.'.jpg">';
//   $sp_slides .= '<a class="tile-inner" data-title=\''.$dcomment.'\' data-lightbox="gallery" href="/phalbum/'.$file.'.jpg">';
   $sp_slides .= "<img class=\"item\" src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7' ";
   $sp_slides .= " data-src='/phalbum/".$file."-s.jpg'>\n"; // 
   $sp_slides .= "</a>\n";
   $sp_slides .= "</div>\n";
 }
 return $sp_slides;
}

function getfotoarea_bs($id, $lim, $arean) {
  Global $dbi, $_foto_mode;
  if ($_foto_mode) {
    $sqladd = "";
  } else {
    $sqladd = "and f.mode > 0 and f.type = 1 ";
  }
  $sql = "select t.name as tname, f.file as fname, f.mode as mode, f.exciting as exciting, 
          f.town as town, f.author as author from foto f, towns t
          where f.town = t.id and t.area = $id $sqladd";
  $result = $dbi->query($sql);
  $files = array();
  $ii = -1;
  if ($result) {
    while ($myrow = mysqli_fetch_array($result)) {
      $file = $myrow;
      $files[] = $file;
      $ii++;
    }
    mysqli_free_result($result);
  }
  if ($ii<1) {
    return '';  
  }
  if ($lim < $ii) {
    $ffiles = array_rand($files, $lim);
  } else {
    $ffiles = array_keys($files);
  }
  foreach ($ffiles as $ii) {
    $myrow = $files[$ii];
    $file = $myrow["fname"];
    $mode = $myrow["mode"];
    $ex = $myrow["exciting"];
    $tn = $myrow["town"];
    $author = authformat($myrow["author"],"nolink");
    if ($ex > 0) {
        $exname = getexname($ex);
        $tnname = $myrow["tname"] . ". ";
    } else {
        $exname = $myrow["tname"];
        $tnname = "";
    }
    $exname = str_replace("\"", "&quot;", $exname);
    $exname = str_replace("\'", "&quot;", $exname);
    $dcomment = "$arean. $tnname $exname";
    if ($forum_user_id) {
      $dcomment .= "<br>Линк для вставки фото в отчет: " .fotolink2($file);
    }
    // слайд
    $fotos .= "<img class='sp-image' alt='$dcommen' src='/phalbum/$file-s.jpg' data-image='/phalbum/$file.jpg' data-description='$dcomment  Фото: $author'>\n"; // 
    //$fotos .= "<a title='$area. $tnname $exname Фото: $author' ref='arearand' class='mw' href='/phalbum/$file.jpg'>";
    //$fotos .= "<img itemprop=\"image\" src='/phalbum/$file-s.jpg' alt='$area.$exname'></a>\n";//07.02.2017 добавила alt, говорят хорошо для оптимизации
  }
  return $fotos;
}

function getareaobj($area) {
  Global $dbi;    
  $limit = 6;
  //проходим по записям для вывода
  $sql="select e.name as ename, e.descrip as descrip, e.Nshirota as shirota,
  e.Edolgota as dolgota, e.id as id, e.booking as booking,
  e.votes as votes, e.rate as rate, e.type as etype, e.kind as ekind,
  e.wrate as wrate, t.name as tname, t.hide as thide
  from exciting e, towns t 
  where e.type in (1,6) and e.town=t.id and t.area=$area
  order by wrate desc LIMIT $limit";

  $result = $dbi->query($sql);
  $cards = array(); // все объекты
  $ii = 0;
  if($myrow = mysqli_fetch_array($result)) {
   do {
    $card = $myrow;
    $s_id = $myrow["id"];
    $card["ex"] = $s_id;
    $wrate   = (float)$myrow["wrate"];
    $card["wrate"] = sprintf("%01.2f",$wrate);
    $t_r = split_descr($myrow["descrip"]); //описание объекта
    $card["descr_local"] = $t_r[0]; // описание без адреса и режима работы
    $card["descr_local_full"] = display_t_r($t_r, "tl travell0t",0);
    $card["mark"] = bs_calc_rating($myrow["votes"],$myrow["wrate"]);
    $cards[] = $card;
   } while($myrow = mysqli_fetch_array($result));
   mysqli_free_result($result);
  }
  return $cards;
}

function draw_card($card, $cardclass, $txtclass) {
  Global $forum_user_id, $forum_user_basket, $forum_user_basket_name;  
  $t = "";
  if ($card["ffile"]) {
    $foto = "<img alt='".$card["ename"]."' src='/phalbum/".$card["ffile"]."-s.jpg'>";  
  }
  else {
    $foto = get1fotoex_bs($card["ex"], 1, 3);
  }  
  $t .= '<div class="exccard '.$cardclass.'">';
  $t .= '<div class="cardhead">';
  $t .= '<a class="exclink" target="_blank" href="/';
  if ($card["mainid"]>=0) {
     $t .= 'otklik.php';
  }
  else {
     $t .= 'group.php';
  }
  $t .= '/'. $card["ex"]. '"><h4>' . $card["s_rek"] . $card["ename"];
  //$t .= "&nbsp;&nbsp;".bs_stars($card["mark"]);
  $t .= '</h4></a>';
  $t .= '<span class="tb excrate">Рейтинг сайта '.$card["wrate"].'</span></div>';
  $t .= '<div class="cardtxt" id="excv-'.$card['ex'].'">';
  if ($foto) {
    //$t .= '<div class="exclinkbutton">'.$foto.'</div>';
    $t .= $foto; 
    if ($card["booking"]) {
     $t .= '<br>[Доступно бронирование]';
    }
  }
  else {
   if ($card["booking"]) {
    $t .= '[Доступно бронирование]';
   }
  } 
  $t .= '<div class="'.$txtclass.'">'.$card["descr_local_full"]; // descrip
  $t .= "</div>\n";
  //$t .= '<div class="mt-2">';      
  //$t .= '<p class="travell0"> Рейтинг: <strong>'.$card["wrate"].'</strong></p>';
  //$t .= '</div>';
  $t .= "</div>\n";

  $buttontext = "Подробности";
  if ($card["mainid"]>=0) {
    if ($foto) {
      $buttontext .= ", фото";
    }
    if ($card["votes"]) {
      $buttontext .= ", отклики";
    }
    $t .= "<button type=\"button\" class=\"btn cardlinkbutton btn-xs btn-info pull-right\">".$buttontext.">>></button>\n";
    if (($forum_user_id>1)) {
     $t .= "\n<div class=\"btn btn-xs basketlinkbutton\" id=\"basketadd".$card["ex"]."\"><a onclick='BasketAdd(".$card["ex"].",".$forum_user_basket.",\"basketadd".$card["ex"]."\")'>Добавить в ".$forum_user_basket_name."</a></div>\n";
    }          
  }
  else   {
    $t .= "<button type=\"button\" class=\"btn cardlinkbutton btn-xs btn-info pull-right\">".$buttontext.">>></button>\n";
  }
  $t .= "</div>\n";
  return $t;
}
function news_excite($exciting) { //ссылка на объект по новости
    $ext = substr($exciting,0,1);
    $exc = substr($exciting,1);
    $t = "";
    if ($ext == 'e') {
       $ex_name=getexname($exc);
       $t = "<a href='/otklik.php/$exc'>$ex_name:</A>";
    }
    if ($ext == 't') {
       $ex_name=gettownname($exc);
       $t = "<a href='/town.php/$exc'>$ex_name:</A>";
    }
    if ($ext == 'r') {
       $ex_name=gettracename($exc);
       $t = "<a href='/trace.php?town=$exc'>$ex_name:</A>";
    }
    if ($ext == 'a') {
       $ex_name=getareaname($exc);
       $t = "<a href='/towns.php/$exc'>$ex_name:</A>";
    }
    return $t;
}

function breadcrumbs($params = []) {
  /*
    Параметры (старые сохраняем):
    - area  => id области (int)
    - town  => id города (int)
    - ex    => id объекта (int)
    - title => название текущей страницы (string)

    Новые (для excite_area.php и подобных):
    - excite_area => bool (если true — вставляет "Достопримечательности" после области)
    - excite_area_url => string (URL узла "Достопримечательности", напр. "/excite_area.php/dagestan/")
    - category => string (категория, напр. "Архитектура")
    - category_url => string (URL категории, напр. "/excite_area.php/dagestan/architecture/")
    - subcategory => string (подтип, напр. "Церкви")
    - subcategory_url => string (URL подтипа, напр. "/excite_area.php/dagestan/architecture/churches")
  */

  $items = [];
  $position = 1;

  // helpers
  $h = function($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_HTML401, 'UTF-8');
  };

  $add = function(&$items, $name, $url = '') {
    $name = (string)$name;
    $url  = (string)$url;
    if ($name === '') return;
    $items[] = ['name' => $name, 'url' => $url];
  };

  // --- Россия или Зарубежье (по области) ---
  if (!empty($params['area'])) {
    $area = (int)$params['area'];
    $areadata = getareaall($area);
    $areaname = isset($areadata[0]) ? $areadata[0] : '';

    if ($area > 0) {
      $add($items, 'Области России', '/areas.php');
    } else {
      $add($items, 'Зарубежные страны', '/countries.php');
    }

    $add($items, $areaname, "/area.php/$area");

    // --- узел "Достопримечательности" (для excite_area) ---
    if (!empty($params['excite_area'])) {
      $ea_name = 'Достопримечательности';
      $ea_url  = !empty($params['excite_area_url']) ? (string)$params['excite_area_url'] : '';
      $add($items, $ea_name, $ea_url);
    }

    // --- категория/подтип (опционально) ---
    if (!empty($params['category'])) {
      $c_url = !empty($params['category_url']) ? (string)$params['category_url'] : '';
      $add($items, (string)$params['category'], $c_url);
    }

    if (!empty($params['subcategory'])) {
      $sc_url = !empty($params['subcategory_url']) ? (string)$params['subcategory_url'] : '';
      $add($items, (string)$params['subcategory'], $sc_url);
    }
  }

  // --- Город ---
  if (empty($params['area']) && !empty($params['town'])) {
    $town = (int)$params['town'];
    $tdata = gettown_bs($town);
    $tname = isset($tdata[1]) ? $tdata[1] : '';
    $tarea = isset($tdata[2]) ? $tdata[2] : '';
    $tareaid = isset($tdata[4]) ? (int)$tdata[4] : 0;

    if ($tareaid > 0) {
      $add($items, 'Области России', '/areas.php');
    } else {
      $add($items, 'Зарубежные страны', '/countries.php');
    }
    $add($items, $tarea, "/area.php/$tareaid");
    $add($items, $tname, "/town.php/$town");
  }

  // --- Объект ---
  if (!empty($params['ex'])) {
    $ex = (int)$params['ex'];
    $exname = getexname($ex);
    $add($items, $exname, "/otklik.php/$ex");
  }

  // --- Текущая страница (без ссылки) ---
  if (!empty($params['title'])) {
    $add($items, (string)$params['title'], '');
  }

  // --- HTML вывод ---
  $html = "<ul class='breadcrumb'>";
  foreach ($items as $b) {
    $name = $h($b['name']);
    $url  = (string)$b['url'];
    if ($url) {
      $url = $h($url);
      $html .= "<li><a href='{$url}'>{$name}</a></li>";
    } else {
      $html .= "<li>{$name}</li>";
    }
  }
  $html .= "</ul>";

  // --- JSON-LD (schema.org/BreadcrumbList) ---
  $json_items = [];
  foreach ($items as $b) {
    $json_items[] = [
      "@type" => "ListItem",
      "position" => $position++,
      "name" => (string)$b['name'],
      "item" => ($b['url'] ? ("https://autotravel.ru" . $b['url']) : null)
    ];
  }

  $jsonld = '<script type="application/ld+json">' . json_encode([
    "@context" => "https://schema.org",
    "@type" => "BreadcrumbList",
    "itemListElement" => $json_items
  ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';

  return $html . "\n" . $jsonld;
}


/* ============================================================
   get_icons_by_location($town_id = 0, $area_id = 0)
   ------------------------------------------------------------
   Возвращает html-строку с иконками типов достопримечательностей
   для города или области — с кликами как в cube.php.
   ============================================================ */
function get_icons_by_location($town_id = 0, $area_id = 0) {
    global $dbi;

    if ($town_id) {
        $where = "g.town = ".(int)$town_id;
    } elseif ($area_id) {
        $where = "g.area = ".(int)$area_id;
    } else {
        return '';
    }

    // основная выборка: тип + количество + любой exid (для прямых ссылок)
    $sql = "
        SELECT g.exgrp AS label, COUNT(*) AS cnt, MIN(g.exid) AS exid
        FROM exbygroup g
        WHERE $where
        GROUP BY g.exgrp
    ";
    $res = $dbi->query($sql);
    if (!$res) return '';

    $icons = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $label = $row['label'];
        $count = (int)$row['cnt'];
        $exid  = (int)$row['exid'];

        $clean = preg_replace('/^\s*\d+[\.\)]\s*/u', '', $label);
        $key   = mb_strtolower($clean, 'UTF-8');

        // ищем иконку — один раз, без вложенного цикла
        $sql2 = "SELECT icon FROM type_ex WHERE LOWER(`desc`) LIKE '%$key%' LIMIT 1";
        $res2 = $dbi->query($sql2);
        $ico  = ($r2 = mysqli_fetch_assoc($res2)) ? '/jpg/'.$r2['icon'] : '';

        $res2 = $dbi->query($sql2);
$ico  = ($r2 = mysqli_fetch_assoc($res2)) ? '/jpg/'.$r2['icon'] : '';

if (!$ico) {
    $prefix = substr($label, 0, 1);
    $service_icons = [
        '2' => '10_hotels.png',
        '3' => '11_cafe.png',
        '5' => '12_souvenirs.png',
        '6' => '14_service.png',
        '8' => '15_auto_tours.png'
    ];
    if (isset($service_icons[$prefix])) {
        $ico = '/jpg/' . $service_icons[$prefix];
    } else {
        continue;
    }
}


        // --- логика переходов (как в cube) ---
        if ($count === 1 && $exid) {
            // один объект → прямая ссылка
            $link = "/otklik.php/" . $exid;
        } else {
            $prefix = substr($label, 0, 1);
            if ($prefix === '1') {
                // достопримечательности — cat
                $cat_param = rawurlencode($label);
                $link = "/excite.php/" . ($town_id ?: $area_id) . "/1?cat=" . $cat_param;
            } else {
                // сервисные категории
                $link = "/excite.php/" . ($town_id ?: $area_id) . "/" . $prefix;
            }
        }

        $title = htmlspecialchars($clean.' — '.$count, ENT_QUOTES);
        $icons[] = "<a href='{$link}' target='_blank'>
                       <img src='{$ico}' title='{$title}'
                            style='width:20px;height:20px;margin:1px;opacity:.9'>
                    </a>";
    }

    return $icons ? '<span class="town-icons">'.implode('', $icons).'</span>' : '';
}
function local_nav($params = []) {
    /*
      Параметры массива:
      - towns   => true/false — есть ли блок городов
      - map     => true/false — есть ли карта
      - ex      => true/false — есть ли достопримечательности
      - photos  => ссылка на фотоальбом (false, если нет)
    */

    $items = [];

    if (!empty($params['towns'])) {
        $items[] = ['name' => 'Города', 'anchor' => '#areatowns'];
    }

    if (!empty($params['map'])) {
        $items[] = ['name' => 'Карта', 'anchor' => '#title-areamap'];
    }

    if (!empty($params['ex'])) {
        $items[] = ['name' => 'Достопримечательности', 'anchor' => '#title-areaex'];
    }

    if (!empty($params['photos'])) {
        $items[] = ['name' => 'Фотографии', 'url' => $params['photos']];
    }

    if (!empty($params['bring'])) {
    $items[] = ['name' => 'Что привезти', 'url' => $params['bring']];
}

    if (!$items) return "";

    $html = "<ul class='area-nav'>";

    foreach ($items as $i) {

        // ссылка на фото
        if (!empty($i['url'])) {
            $html .= "<li><a class='travell5r' href=\"{$i['url']}\">{$i['name']}</a></li>";
        
        // якорь-прокрутка
        } else {
            $html .= "<li><a class='travell5r' onclick=\"ScrollD('{$i['anchor']}')\">{$i['name']}</a></li>";
        }
    }

    $html .= "</ul>";

    return $html;
}
/*
function at_init_gmap($dispgmap, $kml, $label = 'areamap') {

    if (!$dispgmap) return "";

    return "
<script>
document.addEventListener('DOMContentLoaded', function () {

    let mapInited = false;

    function initMapOnce() {
        if (!mapInited) {
            mapInited = true;
            initmapv{$dispgmap}('{$kml}.kml', null, 0, 0);
        }
    }

    const panel = document.getElementById('{$label}');
    if (panel && panel.classList.contains('in')) {
        setTimeout(initMapOnce, 250);
    }

    $('#{$label}').on('shown.bs.collapse', function () {
        initMapOnce();
    });

});
</script>
";
} */

/**
 * Вводный текст: до первого <b> (у вас в базе HTML) или до [b] (на всякий).
 * Возвращаем plain-text (для аккуратной карточки).
 */
function bring_intro($txt) {

  if (!$txt) return '';

  // 1. Обрезаем до первого <b>
  $pos = mb_stripos($txt, '<b>');
  if ($pos !== false) {
    $txt = mb_substr($txt, 0, $pos);
  } else {
    // fallback на старый формат [b]
    $pos = mb_stripos($txt, '[b]');
    if ($pos !== false) {
      $txt = mb_substr($txt, 0, $pos);
    }
  }

  // 2. Декодируем HTML-сущности (важно для &quot;)
  $txt = html_entity_decode($txt, ENT_QUOTES | ENT_HTML5, 'UTF-8');

  // 3. Убираем HTML-теги
  $txt = strip_tags($txt);

  // 4. Нормализуем пробелы
  $txt = preg_replace('/\s+/u', ' ', $txt);

  return trim($txt);
}


/**
 * Фото: только объектное (вариант Б)
 * 1) exciting.ffile -> /phalbum/{ffile}-s.jpg
 * 2) иначе: foto where exciting=id and mode>0, max(mode) -> /phalbum/{foto.file}-s.jpg
 * 3) иначе: null (без картинки)
 */
function bring_photo_url($eid, $ffile) {
  global $dbi;

  $ffile = trim((string)$ffile);
  if ($ffile !== "") {
    return "/phalbum/".$ffile."-s.jpg";
  }

  $eid = (int)$eid;
  $sql = "
    SELECT file
    FROM foto
    WHERE exciting = $eid AND mode > 0
    ORDER BY mode DESC
    LIMIT 1
  ";
  $r = $dbi->query($sql);
  if ($r && ($row = mysqli_fetch_assoc($r))) {
    $f = trim((string)$row['file']);
    mysqli_free_result($r);
    if ($f !== "") {
      return "/phalbum/".$f."-s.jpg";
    }
  } elseif ($r) {
    mysqli_free_result($r);
  }

  return null;
}

/**
 * Карточка: заголовок + город(ссылка) · тип + вводный текст + фото (если есть).
 * anchorId ставим только на первую карточку города.
 */
function render_bring_card($eid, $name, $introText, $photoUrl, $townId, $townName, $typeDesc, $anchorId = "") {
  $eid    = (int)$eid;
  $townId = (int)$townId;

  $url_obj  = "/otklik.php/".$eid;
  $url_town = ($townId > 0) ? ("/town.php/".$townId) : "";

  $name      = (string)$name;
  $introText = (string)$introText;
  $townName  = (string)$townName;
  $typeDesc  = (string)$typeDesc;

  $esc = function ($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
  };

  $id_attr = "";
  if ($anchorId !== "") {
    $id_attr = " id=\"".$esc($anchorId)."\"";
  }

  $html = "<div class=\"bring-card\"{$id_attr}>";

  if (!empty($photoUrl)) {
    $html .= "<a class=\"bring-photo\" href=\"".$esc($url_obj)."\">";
    $html .= "<img loading=\"lazy\" src=\"".$esc($photoUrl)."\" alt=\"".$esc($name)."\">";
    $html .= "</a>";
  }

  $html .= "<div class=\"bring-body\">";

  $html .= "<a class=\"bring-title\" href=\"".$esc($url_obj)."\">".$esc($name)."</a>";

  // строка "город · тип"
  if ($townName !== "" || $typeDesc !== "") {
    $html .= "<div class=\"bring-meta\">";

    if ($townName !== "" && $url_town !== "") {
      $html .= "<a class=\"bring-city\" href=\"".$esc($url_town)."\">".$esc($townName)."</a>";
    } elseif ($townName !== "") {
      $html .= "<span class=\"bring-city\">".$esc($townName)."</span>";
    }

    if ($townName !== "" && $typeDesc !== "") {
      $html .= "<span class=\"bring-dot\">&middot;</span>";
    }

    if ($typeDesc !== "") {
      $html .= "<span class=\"bring-type\">".$esc($typeDesc)."</span>";
    }

    $html .= "</div>";
  }

  if ($introText !== "") {
    $html .= "<div class=\"bring-text\">".$esc($introText)."</div>";
  }

  $html .= "</div>"; // bring-body
  $html .= "</div>"; // bring-card

  return $html;
}

