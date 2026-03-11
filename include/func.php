<?php

function isIPIn($ip, $net, $mask) {
  $ip = ip2long($ip);
  $rede = ip2long($net);
  $mask = ip2long($mask);
  $res = $ip & $mask;
  return ($res == $rede);
}

function fotolink($file) {
  return "&lt;img src=\"/phalbum/$file"."-s.jpg\"&gt;";
}

function show_int_options($var, $vals) {
  $res = "";
  $vv = (int) $var;
  foreach ($vals as $k=>$v) {
    $res .= "<option value=\"$k\" ";
    if ($vv === $k) {
      $res .= " SELECTED";
    }
    $res .= " > ".$v."\n";
  }
  return $res;
}

function fotolink2($file) {
  return "&amp;lt;img src=\"/phalbum/$file"."-s.jpg\"&amp;gt;";
}

/**
 * Дата с месяцем прописью
 * @param string $date строка даты YYYY-mm-dd
 * @return string
 */
function displaydate($date){
  $date=explode("-", $date);
  switch ($date[1]){
    case 1: $m='января'; break;
    case 2: $m='февраля'; break;
    case 3: $m='марта'; break;
    case 4: $m='апреля'; break;
    case 5: $m='мая'; break;
    case 6: $m='июня'; break;
    case 7: $m='июля'; break;
    case 8: $m='августа'; break;
    case 9: $m='сентября'; break;
    case 10: $m='октября'; break;
    case 11: $m='ноября'; break;
    case 12: $m='декабря'; break;
  }
  return $date[2].' '.$m.' '.$date[0];
}

function booklink($link) {
  $ret = "";
  if (empty($link)) {
    return "";  
  }
  $larr = parse_url($link);
  $path = $larr['path'];
  $path = preg_replace('#/+#', '/', $path);
  $path = preg_replace('/\.ru\.html$/','.html',$path);
  if (($larr['host']) and ($larr['path'])) {
   $ret="https://".$larr['host'].$path;
  }
  return $ret;
}

function colobox($cat) {
  Global $dbi, $colorbox;
  $sql = "select author, exciting, town, comment from foto where file='$cat[1]'";
  $result = $dbi->query($sql);
  $exname = "";
  if ($myrow = mysqli_fetch_array($result)) {
   $exname = $myrow["comment"];
   if ($exname) {
     $exname .= ", ";  
   }
   if ($myrow["exciting"] > 0) {
     $exname .= getexname($myrow["exciting"]) ;
   } 
   if ($exname) {
     $exname .= ", ";  
   }
   if ($myrow["town"]!=9999) {
     $exname .= gettownname($myrow["town"]);
   }  
   $exname = str_replace("\"", "&quot;", $exname);
   $exname = str_replace("\'", "&quot;", $exname);
   if ($exname) {
     $exname .= ", ";  
   }
   $exname .= "Фото: " . authformat($myrow["author"], "nolink");
  }
  if ($colorbox) {
   $a = "<a TITLE=\"$exname\" rel=\"main\" class=\"atcbox\" href=\"/phalbum/$cat[1].jpg\"><img border=0 src='/phalbum/$cat[1]"."-s.jpg'></a> ";
  }
  else {
   //$a = "<a TITLE=\"$exname\" target=_blank class=\"atcbox\" href=\"/phalbum/$cat[1].html\"><img border=0 src='/phalbum/$cat[1]"."-s.jpg'></a> ";
    $a = "<img border=0 src='/phalbum/$cat[1]"."-s.jpg'>";
  }
  return $a;
}

function kmlfooter($ids,$header,$text) {
 echo "<table border=1  width='95%' cellpadding=8>\n";
 echo "<tr><td class=travell5 colspan=2>\n";
 echo "<Form name=gmapx method=post action=\"/kml.php\"  target=\"_blank\">\n";
 echo "<input type=hidden name=ids value=\"$ids\">\n";
 echo "<input type=hidden name=resheader value=\"".htmlspecialchars($header,NULL,'')."\">\n";
 echo "$text передать в навигатор: ";
 echo "<input class=atb type=submit name=f value='В формате Google KML'>\n";
 echo "<input class=atb type=submit name=f value='В формате Garmin GPX'>\n";
 echo "</form></td>\n";
 echo "<td  class=travell5>";
 echo "<Form name=gmapxx method=get action=\"/map.php\" target=\"_blank\">\n";
 echo "<input type=hidden name=ids value=\"$ids\">\n";
 echo "<input class=atb type=submit name=f value='Показать на карте'>\n";
 echo "</form>";
 echo "</td>";
 echo "</tr></table>\n";
}

function myjpegresize($file,$newfile,$size,$annotate) {
  global $MAX_SIZE;
  if ( $_FILES['userfile']['size'] > $MAX_SIZE) {
   unlink($file);
   return "Размер файла превышает ".$MAX_SIZE." байт. Уменьшите его.";
  }
  $isize = getimagesize($file);
  if ($isize[2] != 2) {
   unlink($file);
   return "Извините, тип файла ".$_FILES['userfile']." - не картинка в формате JPEG.";
  }
  if (($isize[0] > $size) or ($isize[1] > $size)) { // иначе просто копируем
   if ($isize[0] > $isize[1]){
    $width = $size;
    $height = intval($size * ($isize[1]/$isize[0]));
   }
   else {
    if ($annotate) { // для больших фоток 3:4   
     $height = (int)($size * 3 / 4);
    }
    else { // для иконок пока оставляем как раньше
     $height = $size; 
    }
    $width = intval($height * ($isize[0]/$isize[1]));
   }
   $tryresize = 1;
  }
  else {
    $width = $isize[0];
    $height = $isize[1];
    $tryresize = 0;
  }
  #error_log('image '.$file.' isize0='.$isize[0].' isize1='.$isize[1].' width='.$width.' height='.$height);
  #error_log('image '.$file.' '.print_r($isize,true));
  try {
    $img = new Gmagick($file);
    if ($tryresize) {
     if (function_exists('exif_read_data')) {
         if (is_array($va_exif = @exif_read_data($file, 'EXIF', true, false))) {
             #error_log('image '.$file.' '.print_r($va_exif,true));
             if (isset($va_exif['IFD0']['Orientation'])) {
                 $vn_orientation = $va_exif['IFD0']['Orientation'];
                 switch ($vn_orientation) {
                     case 3:
                         $img->rotateimage("#FFFFFF", 180);
                         break;
                     case 6:
                         $ttt = $width;
                         $width = $height;
                         $height = $ttt;
                         $img->rotateimage("#FFFFFF", 90);
                         break;
                     case 8:
                         $ttt = $width;
                         $width = $height;
                         $height = $ttt;
                         $img->rotateimage("#FFFFFF", -90);
                         break;
                 }
             }
         }
     }

    }
    if ($tryresize) {
      $img->thumbnailimage($size,$size, true);
    }
    if ($annotate) {
     #$draw = new GmagickDraw();
     #$draw->setfillcolor('#ffd0a0d0');
     #$$draw->setfont('utopia-bold');
     #$draw->setfontsize(22);
     #$img->annotateimage($draw,21,$height-9,0,"http://autotravel.ru/");
     #$draw->setfillcolor('#206620d0');
     #$img->annotateimage($draw,20,$height-10,0,"http://autotravel.ru/");
    }
    $img->writeimage($newfile);
  }
  catch(Exception $e) {
   die ('Caught exception: '. $e->getMessage(). "\n");
  }    
  //else {
  //  copy($file,$newfile);  
  //}
  return "";
}

function bannedlist($tmess) {
 global $forum_user_weight;
 $res = 0; 
 if (!(preg_match("/[йцукенгшщзхъфывапролджэячсмитьбю]/", $tmess))) { // нет вообще русских букв
   $res = 1;
 }
 if (preg_match("/ашла\s+только\s+здесь/ui",$tmess)) {
  if ($forum_user_weight < 0.1) {
   $res = 1;
  }
 }
 if (preg_match("/ашла\s+только\s+в\s+этом\s+магазине/ui",$tmess)) {
  if ($forum_user_weight < 0.1) {
   $res = 1;
  }
 }
 if (preg_match("/наш\s+сервис\s+предоставляет/iu",$tmess)) {
   $res = 1;
 }
 if (preg_match("/\s+тыц/ui",$tmess)) {
  if ($forum_user_weight < 0.1) {
   $res = 1;
  }
 }
 /*
  * все домены .tk - дерьмо по определению
  */ 
 if (preg_match("/www\.\S+\.tk/i",$tmess)) {
   $res = 1;
 }
 if (preg_match("/gelendzhik\-travel\.ru/ui",$tmess)) {
   $res = 1;
 }
 if (preg_match("/www\.youtube\.com/ui",$tmess)) {
  if ($forum_user_weight < 0.1) {
   $res = 1;
  }
 }
 if (preg_match("/www\.youtube\.com/ui",$tmess)) {
  if ($forum_user_weight < 0.1) {
   $res = 1;
  }
 }
 if (preg_match("/http\:/ui",$tmess)) {
  if ($forum_user_weight < 0.1) {
   $res = 1;
  } 
 }
 if (preg_match("/https\:/ui",$tmess)) {
  if ($forum_user_weight < 0.1) {
   $res = 1;
  } 
 }
if (preg_match("/нахуй/iu",$tmess)) {
   $res = 1;
 }
 if (preg_match("/охуели/iu",$tmess)) {
   $res = 1;
 }
 if (preg_match("/бляди/iu",$tmess)) {
   $res = 1;
 }
 if (preg_match("/хуйня/iu",$tmess)) {
   $res = 1;
 }
 if (preg_match("/ебаные/iu",$tmess)) {
   $res = 1;
 }
 if (preg_match("/пидарас/ui",$tmess)) {
   $res = 1;
 }
 if (preg_match("/педераст/iu",$tmess)) {
  if ($forum_user_weight < 0.1) {
   $res = 1;
  } 
 }
 return $res;
}

function authformat($author, $mode) {
  Global $_fotoalbum;  
  $ret = $author;
  if (preg_match("/forum\:(\d+)\:(.+)/", $ret, $s_match)) {
    if ($mode == "album") {
      $ret = "<a href='/$_fotoalbum/author/" . $s_match[1] . "/0'>" . $s_match[2] . "</a>";
    }
    if ($mode == "nolink") {
      $ret = $s_match[2];
    }
    if ($mode == "txt") {
      $ret = "Участник форума " . $s_match[2];
    }
  }
  if (preg_match("/unknown\:(\d+)\:(.+)/", $ret, $s_match)) {
    $ret = $s_match[2];
  }
  if (preg_match("/partner\:(\d+)\:(.+)/", $ret, $s_match)) {
    $ret = "Партнер сайта " . $s_match[2];
  }
  return $ret;
}

function authform($author) {
  Global $forumname, $bootstrap;
  $ret = $author;
  if (preg_match("/forum\:(\d+)\:(.+)/", $author, $match)) {
    if ($bootstrap) {  
      $ret = "<a onclick=\"PoLoad($(this))\" data-poload=\"/profile.php/" . $match[1] . "\">Участник форума " . $match[2] . "</a>";  
    }
    else {  
      $ret = "<a href='$forumname/memberlist.php?mode=viewprofile&amp;u=" . $match[1] . "' rel=\"nofollow\">Участник форума " . $match[2] . "</a>";
    }  
  }
  return $ret;
}

function saleformat($sale) {
  $ret = "";
  if (preg_match("/(.+)\|(\d)/", $sale, $s_match)) {
    $quality = $s_match[2] + 1;
    $ret = "<b>Снимок можно купить.</b> Номер для заказа $s_match[1] Относительное качество $quality.";
  }
  return $ret;
}

function make_url($url, $name) {
  echo "<font class=\"normal\"><a href=\"$url\" class=\"link\"><b>$name</b></a></font>";
}

function error_die($error) {
  echo "<center><b>$error</b></center>";
  include("footer.php");
  die();
}

/*
 * Перевод даты из dd.mm.yyyy в yyyy-mm-dd
 */
function datetoiso($d) {
  return implode("-", array_reverse(explode(".", $d)));    
}
/*
 * Перевод строки 34,1234 в 34.1234
 */
function dectoiso($d) {
  return strtr($d, ',', '.');  
}

function ConvertDate($date, $hour = false) {
  if ($hour) {
    $hhmm = substr($date, 11, 5);
  }
  $day = substr($date, 8, 2);
  $month = substr($date, 5, 2);
  $year = substr($date, 0, 4);
  if ($hour) {
   $date = "$day.$month.$year $hhmm";
  }
  else {
   $date = "$day.$month.$year";
  }
  return ($date);
}

function option_list($array, $defaultid) {
  $text = "";
// sort ($array);
  reset($array);
  while (list($key, $value) = each($array)) {
    if ($key == $defaultid) {
      $text = $text . "<option value=\"$key\" SELECTED>$value";
    } else {
      $text = $text . "<option value=\"$key\">$value";
    }
  }
  return $text;
}

function extypes_upd($excid, $townid, $areaid, $extype, $typex) {
  Global $dbi;
  # сперва стереть всё
  $sql = "delete from exbytype where exid=$excid";
  error_log($sql);
  $dbi->query($sql);
  $sql = "delete from exbygroup where exid=$excid";
  $dbi->query($sql);
  error_log($sql);
  if ($extype == 1) {
    # только примечательности имеют подтипы
    $exgrps = getextypes();
    foreach ($typex as $type_ex) {
      $exgrp = $exgrps[$type_ex];
      $sql = "replace into exbytype(exid,extyp,town,area,exgrp) values($excid,'$type_ex',$townid,$areaid,'1.$exgrp')";
      error_log($sql);
      $dbi->query($sql);
      $sql = "replace into exbygroup(exid,town,area,exgrp) values($excid,$townid,$areaid,'1.$exgrp')";
      error_log($sql);
      $dbi->query($sql);
    }
  } else {
    $exgrp = gettypename($extype);
    $sql = "replace into exbytype(exid,extyp,town,area,exgrp) values($excid,'" . "--" . "$extype',$townid,$areaid,'$extype.$exgrp')";
    error_log($sql);
    $dbi->query($sql);
    $sql = "replace into exbygroup(exid,town,area,exgrp) values($excid,$townid,$areaid,'$extype.$exgrp')";
    error_log($sql);
    $dbi->query($sql);
  }
  #echo "-->\n";
}

function getotklik($ide, $otype, $oadd) { //возвращает отформатированную строку отклика 
  Global $dbi, $authtype, $forum_user_id, $ctext, $_has_otkl, $_excc_mode, $_exct_mode, $_otkl_mode, $forumname, $_user_is_admin, $_moderated, $_bad_count;
  $_has_otkl = "";
  $otkl = "";
  $sqladd = "";
  $sqladdx = "";
  if ($oadd) {
    $sqladdx = $oadd;
  } else {
    if ($_excc_mode < 0) {
      return $otkl;
    }
    if ($_excc_mode > 0) {
      $sqladd = " LIMIT $_excc_mode";
    }
    if ($_exct_mode) {
      $sqladdx = " and o.cook like 'forum:%' ";
    }
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
  if ($myrow_o = mysqli_fetch_array($result_o)) {
    if ($otype == "p") {
      $otkl = "";
    } else {
      if ($otype == "z") {
        $otkl = "<table class=travel0 border=0 borderwidth=0 cellpadding=2 width=98%>\n";
      } else {
        $otkl = "<table class=\"ttb tl t12\" cellpadding=8 width=95%>\n";
      }
    }
    do {
      $s_nick = $myrow_o["nick"];
      $s_date = $myrow_o["o_date"];
      $s_time = $myrow_o["o_time"];
      $s_description = $myrow_o["descrip"];
      $s_cook = $myrow_o["cook"];
      $s_oid = $myrow_o["oid"];
      $_ext_otkl = 1;
      if ($s_cook == $ctext) {
        $_ext_otkl = 0;
        $_has_otkl = $s_description;
        $_has_otkl = my_unhtml($_has_otkl);
        $_has_otkl = str_replace("[BR]", "[br]", $_has_otkl);
        $_has_otkl = str_replace("[br]", "\n", $_has_otkl);
      }
      if (preg_match("/forum\:(\d+)\:(.+)/", $s_cook, $s_match)) {
        // $s_mail = "<a href='$forumname/memberlist.php?mode=viewprofile&amp;u=" . $s_match[1] . "'>Участник форума " . $s_match[2] . "</a>";
        $s_nick = "<a class=travell5r href=".$forumname. "/memberlist.php?mode=viewprofile&amp;u=" . $s_match[1] . ">" . $s_match[2] . "</a>";
      } else {
        //$s_mail = $myrow_o["mail"];
      }
      $s_mark = $myrow_o["mark"];
      $s_comment = $myrow_o["comment"];
      if (preg_match("/^partner\:(\d+)\:(.+)/", $s_comment, $s_match)) {
        $s_comment = "<br><hr><b>Комментарий партнера:</b> " . $s_match[2] . "\n";
      } else {
        if (preg_match("/^admin\:(\d+)\:(.+)/", $s_comment, $s_match)) {
          $s_comment = "<br><hr><b>Комментарий администрации сайта:</b> " . $s_match[2] . "\n";
        } else {
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
      } else {
        $addform = "";
      }

// устрицам можно удалять каменты !!!

      if ($_user_is_admin == '1') {
        $addform = "<br><a class=atc href=\"/sitemgr.php?admt=delcomment&amp;exc=$ide&amp;scook=$s_cook&amp;stime=$s_time\">Стереть</a>\n";
      }
      if (!($_otkl_mode)) {
        if ($otype != "z") {
          $s_description = truncastring($s_description, 500, $s_time,"zf");
          $s_comment = truncastring($s_comment, 500, $s_time,"zs");
        }
      }
      
      
      //формируем микроразметку itemreviewed
        //$exname=getexname($ide); //название объекта
        list($exname,$longdescr,$exdesc2,$exdesc3)=getexdesc($ide);
        $exdesc = split_descr($longdescr); //описание объекта
       // если есть телефон и адрес- организация, иначе место
        $itemreviewed = "<div hidden  itemprop=\"itemReviewed\" itemscope itemtype=\"http://schema.org/";
        if (($exdesc[4]) and ($exdesc[3])) {$itemreviewed .= "Organization\">";} else {$itemreviewed .= "Place\">";}
        $itemreviewed .= "<span  itemprop=\"name\" content=\"$exname\"></span>";
        $itemreviewed .= "<span itemprop=\"telephone\" content=\"$exdesc[4]\"></span>";
        $itemreviewed .= "<span itemprop=\"address\" content=\"$exdesc[3]\"></span>";
        $itemreviewed .= "<a href=\"".$sitename."/otklik.php/$ide\" itemprop=\"url\"></a>";
        $itemreviewed .= "</div></div>"; //конец и этого дива, и охватывающего 
        
        $divreviw = "<div itemscope itemtype=\"http://schema.org/Review\">"
                 . "<a href=\"".$sitename."/otklik.php/$ide\" itemprop=\"url\"></a>"; //микроразметка каждого отклика
      
      //начинаем рисовать таблички отзывов
      //нужно добавить оценку отзыва
      // только для зарегиных
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
       
       // закончили формировать интерфейс ввода оценки отзыва
    
      if ($otype == "p") {
        $otkl .= "<br>\n&nbsp;<font class=travell5>$s_nick (<i>$s_mail</i>) <b>Оценка: $s_mark</b> ($s_date)</font><br>\n";
        $otkl .=  $s_description. $s_comment . $addform . "</p>\n";
      } else {
        $otkl .= "<tr>";
        if ($otype == "z") {
         $otkl .= "<td class='t12 tl travell0t'>";
        }
        else {
         $otkl .= "<td class=\"ttb tl\" width=\"10%\">";
        }
        if ($otype != "z") {
          $otkl .= "<b>$s_nick</b><br>\n"; //<i>$s_mail</i><br>\n";
        }
        $otkl .= "<b>оценка: $s_mark</b> ($s_date)";
        $otkl .= $otksc;
        $otkl .= "</td>\n";
        if ($otype == "z") {
         $otkl .= "<td class='t12 tl travell0t travell01r'>";
        }
        else {
         $otkl .= "<td class=\"ttb tl\">";
        }
        if ($s_nick =='') {$s_nick="Аноним";}
        $otkl .= $divreviw . "<span itemprop=\"author\" content=\"$s_nick\"></span>"
                . "<span itemprop=\"datePublished\" content=\"". strftime("%Y-%m-%d %H:%M:%S",$s_time) . "\"></span>" 
                . "<span itemprop=\"reviewBody\">".$s_description."</span>".$itemreviewed . $s_comment . $addform . "</td></tr>\n";
      }
    } while ($myrow_o = mysqli_fetch_array($result_o));
    if ($otype != "p") {
      $otkl .= "</table>";
    }
  }
  mysqli_free_result($result_o);
  return $otkl;
}

function getfotoex($id, $lim, $fadd, $type) {
  Global $dbi, $_foto_mode, $sitename;
  $fotos = ""; 
  $foto1 = "";
  $fall = "";
  $display = 0; 
  $hidden = 0;
  if ($fadd) {
    $sqladd = $fadd;
  } else {
    if ($_foto_mode) {
      $sqladd = "";
    } else {
      $sqladd = " and f.mode > 0 ";
    }
  }
  $sql = "select e.ffile as ffile, f.file as file, f.mode as mode, f.author as author,f.comment as comment,
         e.name as exname, e.type as extype, e.town as town
         from foto f, exciting e
         where e.id = $id and f.exciting = $id $sqladd
         order by f.mode desc, f.date desc";
  $result = $dbi->query($sql);
  $numrows = mysqli_num_rows($result);
  $town = "";
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
        
    //if ($extype == 1) {
    if ( ((($extype != 2) and ($extype != 3)) or ($type<0)) and (($numrows < $lim) or ($mode < 21)) and (($mode > 10) or ($display < $lim))  ) {
        $fotos .= "<a title='$title' ref='object' class='atcbox' href='/phalbum/$file.jpg'><img ";
        if (!$display) {
          $itempropfotos = "itemprop=\"photo\" content=\"https://autotravel.ru/phalbum/$file.jpg\""; //image - микроразметка для отклика, photo для организации
          $foto1 = "https:".$sitename."/phalbum/".$file.".jpg"; // фото для фейсбука
        } 
        $fotos .= " alt='$alt' src='/phalbum/$file-s.jpg'></a>\n";
        $display++;
    }
    else {
        $fotos .= "<a title='Фото: $author' ref='object' class='atcbox' href='/phalbum/$file.jpg'></a>\n";
        $hidden++;
    }
    $fall .= "<a title='$title' ref='object' class='atcbox' href='/phalbum/$file.jpg'><img alt='$alt' src='/phalbum/$file-s.jpg'></a>\n";
  }
  mysqli_free_result($result);
  if ($type>0) {
    return Array("foto" => $fotos,
        "display" => $display,
        "hidden" => $hidden,
        "all" => $fall,
        "foto1" => $foto1,
        "town" => $town,
        "townname" => $tnname,
        "exname" => $exname,
        "extype" => $extype,
        "itempropfotos" =>   $itempropfotos);  //для микроразметки в отклике (организация), может, еще где пригодится
  }
  else {
   return $fotos;
  }
}

function get1fotoex($id, $lim, $var) {
  Global $dbi, $_foto_mode, $sitename;
  $fotos = "";
  if ($_foto_mode) {
   $sqladd = "";
  } else {
   $sqladd = " and f.mode > 0 ";
  }
  if (($var) and ($var!='1')) {
    $sqladd .= " and f.file = '".$var."' ";
  }
  $sql = "select f.file as file, f.mode as mode, f.author as author,
         e.name as exname, e.type as extype, e.town as town
         from foto f, exciting e
         where e.id = $id and f.exciting = $id $sqladd
         order by f.mode desc, f.date desc";
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

    if ($extype == 1) {
      if ($ii < $lim) {
         
        if (!($var=='1')) {
          $fotos .= "<a title='$exname $tnname Фото: $author' ref='object' class='atcbox' href='/phalbum/$file.jpg'><img itemprop=\"photo\" class='latcbox' alt='$tnname: Достопримечательность $exname' src='/phalbum/$file-s.jpg'></a>\n";
        }
        else {
          $fotos = "https:".$sitename."/phalbum/".$file.".jpg";  
        }
      }
      $ii++;
    }
  }
  mysqli_free_result($result);
  return $fotos;
}

function get1grfotoex($id, $lim, $var) {
  Global $dbi, $_foto_mode, $sitename;
  $fotos = "";
  if ($_foto_mode) {
   $sqladd = "";
  } else {
   $sqladd = " and f.mode > 0 ";
  }
  if (($var) and ($var!='1')) {
    $sqladd .= " and f.file = '".$var."' ";
  }
  $sql = "select f.file as file, f.mode as mode, f.author as author,
         e.name as exname, e.type as extype, e.town as town
         from foto f, exciting a
         left outer join exciting e on e.mainid=a.id
         where a.id = $id and f.exciting = e.id $sqladd
         order by f.mode desc, f.date desc";
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

    if ($extype == 1) {
      if ($ii < $lim) {
         
        if (!($var=='1')) {
          $fotos .= "<a title='$exname $tnname Фото: $author' ref='object' class='atcbox' href='/phalbum/$file.jpg'><img itemprop=\"photo\" class='latcbox' alt='$tnname: Достопримечательность $exname' src='/phalbum/$file-s.jpg'></a>\n";
        }
        else {
          $fotos = "https:".$sitename."/phalbum/".$file.".jpg";  
        }
      }
      $ii++;
    }
  }
  mysqli_free_result($result);
  return $fotos;
}

function getfototown($id, $lim) {
  $ret = getfototownl($id, $lim, "");
  return $ret[0];
}

function getfototownl($id, $lim, $exlist) {
  Global $dbi, $_foto_mode, $colorbox, $ug_gallery;
  $fotos = Array();
  $smalllim = 3;
  $sqladdl = "";
  if ($exlist) {
    $sqladdl = " and exciting in ($exlist) ";
  }
  if ($_foto_mode) {
    $sqladd = "";
  } else {
    $sqladd = "and mode > 0 and (type = 1 or mode > 20) ";
  }
  $sql = "select file, mode, author, exciting from foto
         where town = $id $sqladd $sqladdl
         order by mode desc, date desc, exciting asc";
  $result = $dbi->query($sql);
  $ii = 0;
  $tnname = gettownname($id);
  $noindex = 0;
  
  while ($myrow = mysqli_fetch_array($result)) {
    $file = $myrow["file"];
    $mode = $myrow["mode"];
    $ex = $myrow["exciting"];
    $author = authformat($myrow["author"],"nolink");

    if ($ex > 0) {
      $exname = getexname($ex);
    } 
    else {
      $exname = gettownname($id);
    }
    $exname = str_replace("\"", "&quot;", $exname);
    $exname = str_replace("\'", "&quot;", $exname);
    if (($mode > 20) or ($ii<$lim)) {
      if ($ii<$smalllim) {
        $addclass="";  
      }  
      else {
        $addclass=" hidden-xs";  
      }
      if ($ug_gallery) {
        $fotos[0] .= "<img class=\"sp-image\" alt='$exname' src='/phalbum/$file-s.jpg' data-image='/phalbum/$file.jpg' data-description='$author'>\n"; // 
      }  
      else {
        $fotos[0] .= "<a title='$exname Фото: $author' class='atcbox $addclass' href='/phalbum/$file.jpg'><img alt='$exname' src='/phalbum/$file-s.jpg'></a>\n";
      }
      if (!($fotos[1])) {
        $fotos[1]=$file; // featured foto  
      }
    } 
    $ii++;
  }
  mysqli_free_result($result);
  return $fotos;
}

function getfotoarea($id, $lim) {
  Global $dbi, $_foto_mode;
  if ($_foto_mode) {
    $sqladd = "";
  } else {
    $sqladd = "and f.mode > 0 and f.type = 1 ";
  }
  $sql = "select f.file as file from foto f, towns t
         where f.town = t.id and t.area = $id $sqladd";
  $result = $dbi->query($sql);
  $files = array();
  $ii = -1;
  if ($result) {
    while ($myrow = mysqli_fetch_array($result)) {
      $file = $myrow["file"];
      $files[] = $file;
      $ii++;
    }
    mysqli_free_result($result);
  }
  // добавим поиск названия страны 07.02.2017
  $area = "";
  $sql = "select name from areas where id = $id";
  $result = $dbi->query($sql);
  if ($result) {
    while ($myrow = mysqli_fetch_array($result)) {
      $area = $myrow["name"];
    }  
    mysqli_free_result($result);
  }
  
  if ($lim < $ii) {
    $ffiles = array_rand($files, $lim);
  } else {
    $ffiles = array_keys($files);
  }
  foreach ($ffiles as $ii) {
    $file = $files[$ii];
    $sql = "select mode, exciting, town, author from foto where file = '$file'";
    $result = $dbi->query($sql);
    if ($myrow = mysqli_fetch_array($result)) {
      $mode = $myrow["mode"];
      $ex = $myrow["exciting"];
      $tn = $myrow["town"];
      $author = authformat($myrow["author"],"nolink");
      if ($ex > 0) {
        $exname = getexname($ex);
        $tnname = gettownnamearr($tn)[3] . ". ";
      } else {
        $exname = gettownname($tn);
        $tnname = "";
      }
      
      $exname = str_replace("\"", "&quot;", $exname);
      $exname = str_replace("\'", "&quot;", $exname);
      $fotos .= "<a title='$area. $tnname $exname Фото: $author' ref='arearand' class='atcbox mw' href='/phalbum/$file.jpg'><img itemprop=\"image\" src='/phalbum/$file-s.jpg' alt='$area.$exname'></a>\n";//07.02.2017 добавила alt, говорят хорошо для оптимизации
    } else {
      echo "$sql not ok <br>\n";
    }
    mysqli_free_result($result);
  }
  return $fotos;
}

function mysafestring($message) {
  $message = stripslashes($message);
  $message = chop($message);
  $message = preg_replace("/\</", "", $message);
  $message = preg_replace("/\>/", "", $message);
  $message = preg_replace("/\'/", "", $message);
  $message = preg_replace('/\xA0/u', ' ', $message);
//      $message = preg_replace("\"", "", $message);
  $message = preg_replace("/\n/", "", $message);
  $message = preg_replace("/\r/", "", $message);
  return $message;
}

function my_unhtml($message) {
  $message = stripslashes($message);
  $message = chop($message);
  $message = str_replace("<br>", "[br]", $message);
  $message = str_replace("<BR>", "[br]", $message);
  $message = str_replace("<B>", "[b]", $message);
  $message = str_replace("<b>", "[b]", $message);
  $message = str_replace("</B>", "[/b]", $message);
  $message = str_replace("</b>", "[/b]", $message);
  $message = str_replace("<i>", "[i]", $message);
  $message = str_replace("</i>", "[/i]", $message);
  $message = str_replace("<I>", "[i]", $message);
  $message = str_replace("</I>", "[/i]", $message);
  $message = str_replace("<u>", "[u]", $message);
  $message = str_replace("</u>", "[/u]", $message);
  $message = str_replace("<U>", "[u]", $message);
  $message = str_replace("</U>", "[/u]", $message);
  $message = preg_replace("/\n/", " ", $message);
  $message = str_replace("'", "\"", $message);
  $message = preg_replace("/\r/", "", $message);
  $message = preg_replace("/\<a href\=\"(.+?)\".*?\>(.+?)\<\/a\>/i", "[URL=\\1]\\2[/URL]", $message);
  $message = preg_replace("/\<a href\=(.+?)\>(.+?)\<\/a\>/i", "[URL=\\1]\\2[/URL]", $message);
#      $message = preg_replace("/\<img src\=\"(.+?)\"\>/i","[IMG]\\1[/IMG]",$message);
  $message = preg_replace("/\<img src\=\"(.+?)\".*?\>/i", "[IMG]\\1[/IMG]", $message);
  $message = preg_replace("/\<img src\=(.+?)\>/i", "[IMG]\\1[/IMG]", $message);

  return $message;
}

function strip_word_html($text) {
  mb_regex_encoding('UTF-8');
  //replace MS special characters first
  $search = array('/&lsquo;/u', '/&rsquo;/u', '/&ldquo;/u', '/&rdquo;/u', '/&mdash;/u');
  $replace = array('\'', '\'', '"', '"', '-');
  $text = preg_replace($search, $replace, $text);
  //make sure _all_ html entities are converted to the plain ascii equivalents - it appears
  //in some MS headers, some html entities are encoded and some aren't
  $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
  //try to strip out any C style comments first, since these, embedded in html comments, seem to
  //prevent strip_tags from removing html comments (MS Word introduced combination)
  if(mb_stripos($text, '/*') !== FALSE){
     $text = mb_eregi_replace('#/\*.*?\*/#s', '', $text, 'm');
  }
  //introduce a space into any arithmetic expressions that could be caught by strip_tags so that they won't be
  //'<1' becomes '< 1'(note: somewhat application specific)
  $text = preg_replace(array('/<([0-9]+)/'), array('< $1'), $text);
  $text = strip_tags($text, '<b><i><sup><sub><em><strong><u><br>');
  //eliminate extraneous whitespace from start and end of line, or anywhere there are two or more spaces, convert it to one
  $text = preg_replace(array('/^\s\s+/', '/\s\s+$/', '/\s\s+/u'), array('', '', ' '), $text);
  //strip out inline css and simplify style tags
  $search = array('#<(strong|b)[^>]*>(.*?)</(strong|b)>#isu', '#<(em|i)[^>]*>(.*?)</(em|i)>#isu', '#<u[^>]*>(.*?)</u>#isu');
  $replace = array('<b>$2</b>', '<i>$2</i>', '<u>$1</u>');
  $text = preg_replace($search, $replace, $text);
  //on some of the ?newer MS Word exports, where you get conditionals of the form 'if gte mso 9', etc., it appears
  //that whatever is in one of the html comments prevents strip_tags from eradicating the html comment that contains
  //some MS Style Definitions - this last bit gets rid of any leftover comments */
  $num_matches = preg_match_all("/\<!--/u", $text, $matches);
  if($num_matches){
      $text = preg_replace('/\<!--(.)*--\>/isu', '', $text);
  }
  return $text;
} 
function mysafehtml($message) {
  $message = stripslashes($message);
  $message = chop($message);
  $message = str_replace("<", "&lt;", $message);
  $message = str_replace(">", "&gt;", $message);
  $message = str_replace("'", "&quot;", $message);
//      $message = str_replace("[IMG]", "<IMG SRC=", $message);
//      $message = str_replace("[img]", "<IMG SRC=", $message);
//      $message = str_replace("[/IMG]", ">", $message);
//      $message = str_replace("[/img]", ">", $message);
  $message = str_replace("[b]", "<b>", $message);
  $message = str_replace("[/b]", "</b>", $message);
  $message = str_replace("[u]", "<u>", $message);
  $message = str_replace("[/u]", "</u>", $message);
  $message = str_replace("[i]", "<i>", $message);
  $message = str_replace("[/i]", "</i>", $message);
//      $message = str_replace("[/url]", "</a>", $message);
  $message = str_replace("[B]", "<b>", $message);
  $message = str_replace("[/B]", "</b>", $message);
  $message = str_replace("[U]", "<u>", $message);
  $message = str_replace("[/U]", "</u>", $message);
  $message = str_replace("[I]", "<i>", $message);
  $message = str_replace("[/I]", "</i>", $message);
//      $message = str_replace("[/URL]", "</a>", $message);
  $message = str_replace("jpg]", "jpg>", $message);
  $message = str_replace("JPG]", "JPG>", $message);
  $message = str_replace("gif]", "gif>", $message);
  $message = str_replace("GIF]", "GIF>", $message);
  $message = str_replace("png]", "png>", $message);
  $message = str_replace("PNG]", "PNG>", $message);
  $message = str_replace("jpeg]", "jpeg>", $message);
  $message = str_replace("JPEG]", "JPEG>", $message);

  $patterns = array();
  $replacements = array();

  // [img]image_url_here[/img] code..
  $patterns[] = "#\[img\]([^?](?:[^\[]+|\[(?!url))*?)\[/img\]#i";
  $replacements[] = '<img src="\\1" border="0" />';
  // matches a [url=/path]name[/url] code.
  $patterns[] = "#\[url=(/.+?)\](.+?)\[/url\]#is";
  $replacements[] = '<a href="\\1" target=\"_blank\" class=\"tz\">\\2</a>';
  // matches a [url=https://path]name[/url] code.
  $patterns[] = "#\[url=(http://.+?)\](.+?)\[/url\]#is";
  $replacements[] = '<a href="\\1" target=\"_blank\" class=\"tz\">\\2</a>';
  // matches a [url=https://path]name[/url] code.
  $patterns[] = "#\[url=(https://.+?)\](.+?)\[/url\]#is";
  $replacements[] = '<a href="\\1" target=\"_blank\" class=\"tz\">\\2</a>';
  // [email]user@domain[/email] code..
  $patterns[] = "#\[email\]([a-z0-9&\-_.]+?@[\w\-]+\.([\w\-\.]+\.)?[\w]+)\[/email\]#si";
  $replacements[] = '<a href="mailto:\\1">{EMAIL}</a>';

  $message = preg_replace($patterns, $replacements, $message);

  $message = preg_replace("/\n/", "<BR>", $message);
  $message = str_replace("[br]", "<BR>", $message);
  $message = preg_replace("/\r/", "", $message);

  return $message;
}

function mysafehtml2($message, $opt='') {
  $message = stripslashes($message);
  $message = chop($message);
  //error_log($message);
  $message = mysafehtml($message);
  //error_log($message);
  $message = strip_tags($message, $opt.'<p><b><i><br>');
  return $message;
}

function antihtml2($message) {
  $message = str_replace("&lt;", "<", $message);
  $message = str_replace("&gt;", ">", $message);
  $message = str_replace("&quot;", "\"", $message);
  return $message;
}

function getreklama($id, $foto) {
  Global $dbi;
  $www_dir = "/home/blacky/data/www";
  $yesterday = date('Y-m-d', (time() - (3600 * 24)));
  $rek = "";
  $ids = 0;
  $sql_r = "select auth, partner, reklama, dopinfo from reklama where ex = '$id' and srock >'$yesterday'";
  $result_r = $dbi->query($sql_r);
  if ($myrow_r = mysqli_fetch_array($result_r)) {
    $ptn = $myrow_r["partner"];
    $aut = (int) $myrow_r["auth"];
    $rek = $myrow_r["reklama"];
    if ($aut == 0) {
//  дополнительная реклама в dopinfo
      $ids = (int) $myrow_r["dopinfo"];
      ;
    }
  }
  if ($ids) {
    $rek = "";
// только для того же партнёра -- это очень важно!
    $sql_r = "select reklama from reklama where ex = '$ids' and partner='$ptn' and srock >'$yesterday' and auth > 0";
    $result_r = $dbi->query($sql_r);
    if ($myrow_r = mysqli_fetch_array($result_r)) {
      $rek = $myrow_r["reklama"];
    }
  }
  mysqli_free_result($result_r);

  if ($rek) {
// фотографии
    if ($ids) {
      $upload_dir = $www_dir . "/photo/" . $ids;
    } else {
      $upload_dir = $www_dir . "/photo/" . $id;
    }
    $fotocount=0;
    if (($foto) and (is_dir($upload_dir))) {
      $fotoar = listfoto($upload_dir);
      $fotocount = sizeof($fotoar);
      $fotoout = "";
      for ($i=0; $i<$fotocount; $i++) {
        $fotoout = $fotoout . "<a TITLE=\"\" rel=\"partner\" class=\"atcbox\" href='/photo/$id/$fotoar[$i].jpg'><img border=0 src='/photo/$id/" . $fotoar[$i] . "-s.jpg'></a>\n";
      };
    }
    if ($fotocount) {
      $rek = $rek . "\n<br><hr><div align=center>" . $fotoout . "</div><br>\n";
    }
  }
  return $rek;
}

function listfoto($upload_dir) {
  $fotocount=0;
  $foto=array();
  $handle = opendir($upload_dir . "/");
  while ($file = readdir($handle)) {
   if ((is_file($upload_dir . "/" . $file)
     and (substr($file, -4)) == ".jpg")
     and (substr($file,-6) != "-s.jpg")
   ) {
    $foto[$fotocount] = substr($file, 0, -4);
    //$fotoout = $fotoout . "<a target=_blank href='/photo/$id/$foto.jpg'><img border=0 src='/photo/$id/" . $foto . "-s.jpg'></a>\n";
    $fotocount++;
   }
  }
  closedir($handle);
  return $foto;
}

function random_string($length) {
 $key = '';
 $keys = array_merge(range(0, 9), range('a', 'z'));
 for ($i = 0; $i < $length; $i++) {
   $key .= $keys[array_rand($keys)];
 }
 return $key;
}

function gettracename($id) {
  Global $dbi;
  $sql = "select name, of_name from trace where id='$id'";
  $result = $dbi->query($sql);

  if ($myrow = mysqli_fetch_array($result)) {
    $name = "Автодорога " . $myrow["of_name"] . " " . $myrow["name"];
  }
  mysqli_free_result($result);
  return $name;
}

function gettownname($id) {
  Global $dbi;
  $name = "*";
  $sql_r = "select t.name as tname,t.hide as thide,t.area as tarea, a.name as aname from towns t, areas a where t.id='$id' and a.id=t.area";
  $result_r = $dbi->query($sql_r);
  if ($myrow_r = mysqli_fetch_array($result_r)) {
    $name = $myrow_r["tname"];
    $area = $myrow_r["aname"];
    $hide = $myrow_r["thide"];
    $name = $name . " ($area)";
  }
  mysqli_free_result($result_r);
  return $name;
}

function gettowndescr($id) {
  Global $dbi;
  $name = gettowntext($id);
  if (!($name)) {
    $sql_r = "select descrip from towns where id='$id'";
    $result_r = $dbi->query($sql_r);
    if ($myrow_r = mysqli_fetch_array($result_r)) {
      $name = $myrow_r["descrip"];
    }
    mysqli_free_result($result_r);
  }
  return $name;
}

function gettownnamearr($id) {
  Global $dbi;
  $name[0] = ""; // name
  $name[1] = ""; // name (area) НАЗВАНИЕ (ОБЛАСТЬ)
  $name[2] = ""; // area
  $name[3] = ""; // name cyrillic
  $name[4] = ""; // name nls
  $name[5] = ""; // name_to
  $name[6] = ""; // name_from
  $name[7] = ""; // name_po
  $name[8] = ""; // area_id
  $name[9] = ""; // hide
  $name[10] = ""; // town url as seo
  $name[11] = ""; // town name_around
  $name[12] = ""; // town name_in
  $name[13] = ""; // town ffoto
  $sql_r = "select t.name as tname, t.url as turl, t.namearound as namearound, t.namein as namein, t.ffile as ffoto, t.nameto as tnameto, t.namefrom as tnamefrom,t.namepo as tnamepo,"
          . "t.area as tarea, a.name as aname, t.hide as thide from towns t, areas a where t.id='$id' and a.id=t.area";
  $result_r = $dbi->query($sql_r);
  if ($myrow_r = mysqli_fetch_array($result_r)) {
    $name[0] = $myrow_r["tname"];
    $area = $myrow_r["aname"];
    $name[1] = $name[0] . " ($area)";
    $name[2] = $area;
    $name[5] = $myrow_r["tnameto"];
    $name[6] = $myrow_r["tnamefrom"];
    $name[7] = $myrow_r["tnamepo"];
    $name[9] = $myrow_r["thide"];
    $name[10] = $myrow_r["turl"]; //
    $name[11] = $myrow_r["namearound"]; //
    $name[12] = $myrow_r["namein"]; //
    $name[13] = $myrow_r["ffoto"]; //
    $t_name_arr = preg_split ("/\//" , $myrow_r["tname"]);
    $name[3] = $t_name_arr[0];
    $name[4] = $t_name_arr[1];
    $name[8] = $myrow_r["tarea"]; // в excite нужен еще id области
    if ($name[3] == "") {
     $name[3] = $name[0];
    }
    if ($name[5] == "") {
     $name[5] = "город ".$name[3];  
    }
    if ($name[6] == "") {
     $name[6] = "города ".$name[3];  
    }
    if ($name[7] == "") {
     $name[7] = "городу ".$name[3];  
    }
    if ($name[11] == "") {
     $name[11] = "города ".$name[3];  
    }
    if ($name[12] == "") {
     $name[12] = "город ".$name[3];  
    }
  }
  mysqli_free_result($result_r);
  return $name;
}

function getshorttownname($id) {//название города
  Global $dbi;
  $name = "*";
  $sql_r = "select name from towns where id=$id";
  $result_r = $dbi->query($sql_r);
  if ($myrow_r = mysqli_fetch_array($result_r)) {
    $name = $myrow_r["name"];
  }
  mysqli_free_result($result_r);
  return $name;
}

function gettownex($id) {
  Global $dbi;
   $sql_r = "select e.name as ename, t.id as tid, t.name as tname,t.hide as thide,t.area as tarea,
                  a.name as aname from towns t, areas a, exciting e
                  where e.id = $id and t.id=e.town and a.id=t.area";
  $result_r = $dbi->query($sql_r);
  if ($myrow_r = mysqli_fetch_array($result_r)) {
    $tid = $myrow_r["tid"];
    $name = $myrow_r["tname"];
    $ename = $myrow_r["ename"];
    $area = $myrow_r["aname"];
    $name = $name . " ($area)";
  }
  mysqli_free_result($result_r);
  $ename = str_replace("\"", "&quot;", $ename);
  $ename = str_replace("\'", "&quot;", $ename);
  return "$tid\t$name\t$ename";
}

function getextown($id) {
  Global $dbi;
  $eid = "";
  $sql_r = "select exciting.id as eid
                  from towns, exciting
                  where towns.id = $id and exciting.town=towns.id";
  $result_r = $dbi->query($sql_r);
  if ($myrow_r = mysqli_fetch_array($result_r)) {
    $eid = $myrow_r["eid"];
  }
  return "$eid";
}

function gettypename($type) {
  Global $dbi;
  $name = "";
  $sql = "select name from type where id=$type";
  $result = $dbi->query($sql);
  if ($myrow = mysqli_fetch_array($result)) {
    $name = $myrow["name"];
  }
  return $name;
}


function getrtypes() {
  Global $dbi;
  $r_types=Array();
  $sql_fd = "select name, id from type order by srtfld";
  $result_fd = $dbi->query($sql_fd);
  while ($row_trip = mysqli_fetch_array($result_fd)) {
    $r_types[$row_trip["id"]] = $row_trip["name"];
  }
  mysqli_free_result($result_fd);
  return $r_types;
}
function getrtypesdesc() {
  Global $dbi;
  $r_types=Array();
  $sql_fd = "select descrip, id from type order by srtfld";
  $result_fd = $dbi->query($sql_fd);
  while ($row_trip = mysqli_fetch_array($result_fd)) {
    $r_types[$row_trip["id"]] = $row_trip["descrip"];
  }
  mysqli_free_result($result_fd);
  return $r_types;
}
/*
 * HTML код <input> элементов по массиву для поиска
 */
function gettypextxt($typex,$check,$name) {
  Global $dbi;
  $typexid = 0;
  $typex_txt = array();
  $typex_txt1 = "";
  $typex_txt2 = "";
  $_desc = "";
  $sql = "select `id`,`desc`,`type` from type_ex where id like '".$typex."' order by `desc`,`type` asc";
  #echo "\n<!-- $sql $name ".var_dump($check)." !-->\n";
  $result = $dbi->query($sql);
  $typexcount = mysqli_num_rows($result);
  if ($myrow = mysqli_fetch_array($result)) {
    do {
      $typexid++;
      $typexid++;
      $tid = (string) $myrow["id"];
      $tname = $myrow["desc"] . "/" . $myrow["type"];
      if ($typexid > $typexcount) {
        if (($_desc) and (!($_desc == $myrow["desc"]))) {
          $typex_txt2 = $typex_txt2 . "\n<hr width=80%>";
        }
        $_desc = $myrow["desc"];
        if (in_array($tid, $check)) {
          $typex_txt2 = $typex_txt2 . "<input type=checkbox name=$name value=$tid CHECKED>$tname<br>\n";
        } else {
          $typex_txt2 = $typex_txt2 . "<input type=checkbox name=$name value=$tid>$tname<br>\n";
        }
      } else {
        if (($_desc) and (!($_desc == $myrow["desc"]))) {
          $typex_txt1 = $typex_txt1 . "\n<hr width=80%>";
        }
        $_desc = $myrow["desc"];
        if (in_array($tid, $check)) {
          $typex_txt1 = $typex_txt1 . "<input type=checkbox name=$name value=$tid CHECKED>$tname<br>\n";
        } else {
          $typex_txt1 = $typex_txt1 . "<input type=checkbox name=$name value=$tid>$tname<br>\n";
        }
      }
    } while ($myrow = mysqli_fetch_array($result));
  }
  mysqli_free_result($result);
  $typex_txt[1] = $typex_txt1;
  $typex_txt[2] = $typex_txt2;
  return $typex_txt;
}

/*
 * HTML код <option> элементов по массиву для поиска
 * описаний видов примечательностей и сервиса (kind)
 * $typex - маска кодов - '0%' или '6%'
 * $check - массив выбранных в интерфейсе кодов, которые надо отметить SELECTED
 * $all   - выделить все
 */
function gettypexopt($typex,$check,$all) {
  Global $dbi;
  $typexid = 0;
  $typex_txt = "";
  $_desc = "";
  $sql = "select `id`,`desc`,`type` from type_ex where id like '".$typex."' order by `desc`,`type` asc";
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
      if ((in_array($tid, $check))||($all)) {
        $typex_txt .= "<option value=$tid SELECTED>$tname</option>\n";
      } else {
        $typex_txt .= "<option value=$tid>$tname</option>\n";
      }
    } while ($myrow = mysqli_fetch_array($result));
  }
  if ($_desc) {
    $typex_txt .= "</optgroup>\n";
  }
  mysqli_free_result($result);
  return $typex_txt;
}

function getauthor($id) {
  Global $dbi;

  $sql2 = "select distinct author from foto where author like 'forum:" . $id . ":%'";
  $result2 = $dbi->query($sql2);
  $s_ex = "****";

  if ($myrow2 = mysqli_fetch_array($result2)) {
    $s_ex = $myrow2["author"];
  }
  mysqli_free_result($result2);
  return $s_ex;
}

function getexname($id) {
  Global $dbi;

  $sql2 = "select name, town from exciting where id = $id";
  $result2 = $dbi->query($sql2);

  if ($myrow2 = mysqli_fetch_array($result2)) {
    $s_ex = $myrow2["name"];
    $town = $myrow2["town"];
  } else {
    $s_ex = "Нет названия";
    $town = 0;
  }
  $s_ex = str_replace("\"", "&quot;", $s_ex);
  $s_ex = str_replace("\'", "&quot;", $s_ex);
  mysqli_free_result($result2);
  return $s_ex;
}

function getexdesc($id) {
  Global $dbi, $_nomap;

  $sql2 = "select name, descrip, wrate, Nshirota, Edolgota from exciting where id = $id";
  $result2 = $dbi->query($sql2);

  if ($result2) {
    $myrow2 = mysqli_fetch_array($result2);
    $s_ex = $myrow2["name"] . "|" . $myrow2["descrip"] . "|<b>Рейтинг</b>: " . sprintf("%01.2f", $myrow2["wrate"]);
    $nsh = (float) $myrow2["Nshirota"];
    $edg = (float) $myrow2["Edolgota"];
    $ngrv = degreestotext($nsh);
    $egrv = degreestotext($edg);
    if ((($nsh > 0) or ($nsh < 0)) and (($edg > 0) or ($egd < 0))) {
      $_nomap = 1;
      $s_ex = $s_ex . "|<b>Координаты</b>: <b>N</b>$ngrv, <b>E</b>$egrv";
    }
    mysqli_free_result($result2);
  } 
  else {
    $s_ex = "|||";
  }
  return $s_ex;
}

function getareadesc($id) {
  Global $dbi;
  $a_desc = "";
  $sql_a = "select a.descrip as descrip from areas a where a.id = '$id'";
  $result_a = $dbi->query($sql_a);
  if ($myrow_a = mysqli_fetch_array($result_a)) {
    $a_desc = $myrow_a["descrip"];
  }
  $a_desc = truncastring($a_desc, 2000, 'area',"dsc");
  return $a_desc;
}


function getareaall($id) {
    global $dbi;

    $id = (int)$id;

    // массив фиксированной длины + расширение под новые поля
    // 0 name, 1 name_r, 2 capital_id, 3 capital_name, 4 currency, 5 name_v, 6 slug, 7 bring_description
    $aname = array_fill(0, 8, "");

    $sql_a = "
        SELECT 
            a.name             AS name,
            a.name_r           AS name_r,
            a.name_v           AS name_v,
            a.capital          AS capital,
            a.currency         AS currency,
            a.slug             AS slug,
            a.bring_description AS bring_description
        FROM areas a 
        WHERE a.id = $id
        LIMIT 1
    ";

    $result_a = $dbi->query($sql_a);
    if (!$result_a) {
        error_log("getareaall() SQL error: " . $dbi->error . " | Query: " . $sql_a);
        return $aname;
    }

    if ($myrow_a = mysqli_fetch_assoc($result_a)) {
        $aname[0] = (string)$myrow_a['name'];               // именительный
        $aname[1] = (string)$myrow_a['name_r'];             // родительный
        $aname[2] = (int)$myrow_a['capital'];               // id столицы
        $aname[4] = (string)$myrow_a['currency'];           // валюта
        $aname[5] = (string)$myrow_a['name_v'];             // винительный
        $aname[6] = trim((string)$myrow_a['slug']);         // slug
        $aname[7] = trim((string)$myrow_a['bring_description']); // bring_description
    }

    mysqli_free_result($result_a);

    // название столицы, если есть id
    if (!empty($aname[2])) {
        $tn = gettownnamearr($aname[2]);
        $aname[3] = isset($tn[3]) ? $tn[3] : "";
    }

    return $aname;
}



  
function getareaname($id) {
  Global $dbi;
  $a_name = "";
  $sql_a = "select a.name as name from areas a where a.id = '$id'";
  $result_a = $dbi->query($sql_a);
  if ($myrow_a = mysqli_fetch_array($result_a)) {
    $a_name = $myrow_a["name"];
  }
  return $a_name;
}

function getareaname_r($id) {
  Global $dbi;
  $a_name = "";
  $sql_a = "select a.name_r as name from areas a where a.id = '$id'";
  $result_a = $dbi->query($sql_a);
  if ($myrow_a = mysqli_fetch_array($result_a)) {
    $a_name = $myrow_a["name"];
  }
  return $a_name;
}

function getcurrency($id) {
  Global $dbi;
  $currency[0] = ""; // Название валюты
  $currency[1] = ""; // Код валюты
  $currency[2] = ""; //  Номинал валюты
  $currency[3] = ""; // Курс валюты
  $sql_a = "select * from valcurs  where numcode like '$id'";
  $result_a = $dbi->query($sql_a);
  if ($myrow_a = mysqli_fetch_array($result_a)) {
    $currency[0] = $myrow_a["name"];
    $currency[1] = $myrow_a["charcode"];
    $currency[2] = $myrow_a["nominal"];
    $currency[3] = $myrow_a["value"];
  }
  
  return $currency;
}  

function degreestotext($dg) {
  $dg = (double) $dg;
  $grv = "";
  if ($dg > 0) {
    $gr = (double) floor($dg);
    $mn = ($dg - $gr) * 60;
    $grv = sprintf("%03d", $gr) . " " . sprintf("%02.3f", $mn);
  }
  if ($dg < 0) {
    $dg = (-1) * $dg;
    $gr = (double) floor($dg);
    $mn = ($dg - $gr) * 60;
    $grv = "-" . sprintf("%03d", $gr) . " " . sprintf("%02.3f", $mn);
  }
  return $grv;
}

function texttodegrees($ngrw) {
  $ngr = 0.00;
  if (preg_match("/^\s*(\-*)(\d+)\s+(\d+[\.\,]\d+)\s*$/i", $ngrw, $mngrw)) {
// ГГ ММ.МММММ
    $ngr = ((float) $mngrw[2]) + ((float) $mngrw[3]) / 60;
    if ($mngrw[1]) {
      $ngr = (-1) * $ngr;
    }
//    echo "\n1\n";
  } else {
// ГГ ММ СС.СССС
    if (preg_match("/^\s*(\-*)(\d+)\s+(\d+)(\s+)(\d+[\.\,]\d+)\s*$/i", $ngrw, $mngrw)) {
      $ngr = ((float) $mngrw[2]) + ((float) $mngrw[3]) / 60 + ((float) $mngrw[5]) / 3600;
      if ($mngrw[1]) {
        $ngr = (-1) * $ngr;
      }
    } else {
// ГГ ММ СС
      if (preg_match("/^\s*(\-*)(\d+)\s+(\d+)(\s+)(\d+)\s*$/i", $ngrw, $mngrw)) {
        $ngr = ((float) $mngrw[2]) + ((float) $mngrw[3]) / 60 + ((float) $mngrw[5]) / 3600;
        if ($mngrw[1]) {
          $ngr = (-1) * $ngr;
        }
      } else {
// ГГ ММ (?)
        if (preg_match("/^\s*(\-*)(\d+)\s+(\d+)\s*$/i", $ngrw, $mngrw)) {
          $ngr = ((float) $mngrw[2]) + ((float) $mngrw[3]) / 60;
          if ($mngrw[1]) {
            $ngr = (-1) * $ngr;
          }
//     echo "\n3\n";
        } else {
// ГГ.ГГГГГГГ
          if (preg_match("/^\s*(\-*\d+)\.(\d+)\s*$/i", $ngrw, $mngrw)) {
            $ngr = (float) $ngrw;
//       echo "\n4\n";
          }
        }
      }
    }
  }
  return $ngr;
}

function getcoords($id) {
  Global $dbi, $_nomap;
  $coord = "";
  $sql = "SELECT id, Nshirota, Edolgota from exciting where id = $id";
// $sql = "SELECT e.id as id, e.Nshirota as Nshirota, e.Edolgota as Edolgota, t.Nshirota as tshirota, t.Edolgota as tdolgota from exciting e, towns t where e.id = $id and t.id = e.town";
  $result = $dbi->query($sql);
  if ($myrow = mysqli_fetch_array($result)) {
    $nsh = (float) $myrow["Nshirota"];
    $edg = (float) $myrow["Edolgota"];
    $ngrv = degreestotext($nsh);
    $egrv = degreestotext($edg);
    if ((($nsh > 0) or ($nsh < 0)) and (($edg > 0) or ($edg < 0))) {
      $_nomap = 1;
      $coord = "<b>N</b>$ngrv, <b>E</b>$egrv";
    }
  }
  return $coord;
}

function gettcoords($id) {
  Global $dbi;
  $coord = array(0, 0);
  $sql = "SELECT id, Nshirota, Edolgota from towns where id = $id";
  $result = $dbi->query($sql);
  if ($myrow = mysqli_fetch_array($result)) {
    $coord[0] = (float) $myrow["Nshirota"];
    $coord[1] = (float) $myrow["Edolgota"];
  }
  return $coord;
}

function ifmoderated($id) {
  Global $dbi;
  $ifmoderated = 0;
  $sql = "SELECT emoderated from exciting where id = $id";
  $result = $dbi->query($sql);
  if ($myrow = mysqli_fetch_array($result)) {
    $ifmoderated = (int) $myrow["emoderated"];
  }
  return $ifmoderated;
}

function getecoords($id) {
  Global $dbi;
  $coord = array(0, 0);
  $sql = "SELECT e.id as id, e.Nshirota as Nshirota, e.Edolgota as Edolgota, t.Nshirota as tshirota, t.Edolgota as tdolgota from exciting e, towns t where e.id = $id and t.id = e.town";
  $result = $dbi->query($sql);
  if ($myrow = mysqli_fetch_array($result)) {
    $coord[0] = (float) $myrow["Nshirota"];
    $coord[1] = (float) $myrow["Edolgota"];
    if (!((($coord[0] > 0) or ($coord[0] < 0)) && (($coord[1] > 0) or ($coord[1] < 0)))) {
      $coord[0] = (float) $myrow["tshirota"];
      $coord[1] = (float) $myrow["tdolgota"];
    }
  }
  return $coord;
}
/*
 * display_t_r 
 * форматирует массив элементов описания объекта $t_r 
 * в соответствии с указанным классом $class , 
 * если задан $micro - добавляет микроразметку
 */
function display_t_r($t_r, $class, $micro) {
   $out = "";   
   // описание
   if ($t_r[0]) {
     $out .= "<p class=\"$class\"";
     if ($micro) {
       $out .= " itemprop=\"description\""; //микроразметка описания
     }
     $out .= ">$t_r[0]</p>"; 
   }
   //режим работы
   if ($t_r[2]) {
       $out .= "<p class=\"$class\"><b>Режим работы: </b>$t_r[2]</p>";
   } 
   //адрес без телефона
   if ($t_r[3]) {
     $out .= "<p class=\"$class\"><div class=\"$class\"";
     if ($micro) {
       $out .= " itemprop=\"address\" itemscope itemtype=\"http://schema.org/PostalAddress\"";
     }
     $out .= "><b>Адрес: </b><span";
     if ($micro) {
       $out .= " itemprop=\"streetAddress\"";
     }
     $out .= ">$t_r[3]</span></div>";
   }
   //телефон
   if ($t_r[4]) {
     $out .= "<p class=\"$class\"><b>Телефон: </b><span";
     if ($micro) {
       $out .= " itemprop=\"telephone\"";
     }
     $out .= ">$t_r[4]</span></p>";
   }
        
   return $out;
}

function display_basket_obj($objid, $action, $author, $hr) {
  Global $forum_user_id, $forum_user_basket, $forum_user_basket_name;
  $result = "";
  if ($objid) {
    list($objname, $objdesc, $objdesc2) = explode('|', getexdesc($objid));
    list($town, $tnname, $exname) = explode("\t", gettownex($objid));
    $result .= "<p class=travell9><font class=travell5m>$tnname: </font><a target=_blank class=travell5m name=\"ex-$objid\" id=\"ex-$objid\" href='/otklik.php/$objid'>$objname</a>\n";
    $result .= "<a class=travell5n onclick=\"ChToggle('e$objid',this);\"><img border=0 alt=\">\" src=\"/jpg/plus.gif\"></a></p>\n";
    $result .= "<div title=\"CHG\" id=\"e$objid\" style=\"visibility: hidden; display: none;\">\n";
    $result .= "<p class=travell0>$objdesc<br>$objdesc2</p>\n";
    $s_rek = getreklama($objid, 0);
    if ($s_rek <> "") {
      $result = $result . "<p class=travell0><img src=\"jpg/bigpartner.gif\" width=\"11\" height=\"11\" title=\"Партнер сайта\" alt=\"Партнер сайта\"><b>Официальная информация:</b>\n";
      $result .= " $s_rek</p>\n";
    }
    if ($action == "W") {
      $result .= "</div>\n";
      $result .= getotklik($objid, "z", " and o.cook like 'forum:$author:%' ");
    } else {
      # показать отклики пользователя и его фотографии. или не его ???
      if (($forum_user_id > 1)) {
        $result .= "<div id=basketadd$objid  class=travell0><a class=travell5m onclick='BasketAdd($objid,$forum_user_basket,\"basketadd$objid\")'>Добавить в $forum_user_basket_name</a></div>\n";
      }
      $result .= "<table border=0 cellpadding=2 width=98%><tr><td align=center>" . getfotoex($objid, 3, "", 0) . "</td></tr></table>\n";
      if ($hr) {
        $result .= "<hr width=85%>\n";
      }
      $result .= "</div>\n";
      $result .= getotklik($objid, "z", " and o.cook like 'forum:$author:%' ");
    }
  }
  return $result;
}

function gettownname2($id) {
  Global $dbi;
  $rek = "";
  $sql_r = "select t.name as tname,t.hide as thide,t.area as tarea,a.name as aname from towns t, areas a where t.id='$id' and a.id=t.area";
  $result_r = $dbi->query($sql_r);
  if ($myrow_r = mysqli_fetch_array($result_r)) {
    $name = $myrow_r["tname"];
    $area = $myrow_r["aname"];
    $hide = $myrow_r["thide"];
    $name = "$area, $name";
  }
  mysqli_free_result($result_r);
  return $name;
}

function truncastring($string, $length, $id, $addon) {
  if (mb_strlen($string) > $length) {
    $flag0 = 0;
    $length2 = (int) (1.4 * $length);
    // обрезать по тэг если укладывается в длину * 1.4
    if (preg_match("/^(.{" . $length . "}[^>]*?\>)(.*)$/msu", $string, $s_match)) {
      if (mb_strlen($s_match[1]) < ($length2)) {
        if ($s_match[2]) {
          $stringm = $s_match[1] . " ...<a class=travell5r onclick=\"ChToggle('" . $addon . $id . "',this);\">продолжение<img border=0 alt=\">\" src=\"/jpg/plus.gif\"></a>"
                  . "<div id=\"" . $addon . $id . "\" style='visibility:hidden;display:none;'>"
                  . $s_match[2] . "</div>\n";
          $flag0 = 1;
        }
      }
    }
    // обрезать по точку если укладывается в длину * 1.4
    if (!($flag0)) {
     if (preg_match("/^(.{" . $length . "}[^\.]*?\.)(.*)$/msu", $string, $s_match)) {
      if (mb_strlen($s_match[1]) < ($length2)) {
        if ($s_match[2]) {
          $stringm = $s_match[1] . " ...<a class=travell5r onclick=\"ChToggle('" . $addon . $id . "',this);\">продолжение<img border=0 alt=\">\" src=\"/jpg/plus.gif\"></a>"
                  . "<div id=\"" . $addon . $id . "\" style='visibility:hidden;display:none;'>"
                  . $s_match[2] . "</div>\n";
          $flag0 = 1;
        }
      }
     }
    }
    // обрезать по пробел  если укладывается в длину * 1.4
    if (!($flag0)) {
     if (preg_match("/^(.{" . $length . "}[^ ]*?\ )(.*)$/msu", $string, $s_match)) {
      if (mb_strlen($s_match[1]) < ($length2)) {
        if ($s_match[2]) {
          $stringm = $s_match[1] . " ...<a class=travell5r onclick=\"ChToggle('" . $addon . $id . "',this);\">продолжение<img border=0 alt=\">\" src=\"/jpg/plus.gif\"></a>"
                  . "<div id=\"" . $addon . $id . "\" style='visibility:hidden;display:none;'>"
                  . $s_match[2] . "</div>\n";
          $flag0 = 1;
        }
      }
     }
    }
    // просто обрезать по длине *1.4 и аллах с ними обоими
    if (!($flag0)) {
     if (preg_match("/^(.{" . $length2 . "})(.*)$/msu", $string, $s_match)) {
      if ($s_match[2]) {
        $stringm = $s_match[1] . " ...<a class=travell5r onclick=\"ChToggle('x" . $addon . $id . "',this);\">продолжение<img border=0 alt=\">\" src=\"/jpg/plus.gif\"></a>"
                . "<div id=\"x" . $addon . $id . "\" style='visibility:hidden;display:none;'>"
                . $s_match[2] . "</div>\n";
      }
     }
    }
    if ($stringm) {
      $string = $stringm;
    }
  }
  return $string;
}

function gettowntext($id) {
  Global $dbi, $_desc_mode, $bootstrap;
  $name = "";
  if (!($_desc_mode == 1)) {
    $sql_r = "select descr, cook from l_text where obj = 't" . $id . "' and dtizm > 00000000000000";
    $result_r = $dbi->$sql_r;
    if ($result_r) {
      if ($myrow_r = mysqli_fetch_array($result_r)) {
        $name = $myrow_r["descr"];
        $cook = $myrow_r["cook"];
        if ($name) {
          if ($_desc_mode) {
            $name = truncastring($name, 1000, 'townt',"tv");
          }
          $name = authform($cook) . ":<br>\n$name";
        }
      }
      mysqli_free_result($result_r);
    }
  }
  return $name;
}

/**
 * Пользовательское описание объекта (если есть)
 * @global object $dbi
 * @global int $_desc_mode
 * @param int $id
 * @return string
 */
function getextext($id) { 
  Global $dbi, $_desc_mode;
  $name = "";
  if (!($_desc_mode == 1)) {
    $sql_r = "select descr, cook from l_text where obj = 'e" . $id . "' and dtizm > 00000000000000";
    $result_r = $dbi->query($sql_r);
    if ($myrow_r = mysqli_fetch_array($result_r)) {
      $name = $myrow_r["descr"];
      $cook = $myrow_r["cook"];
      if ($name) {
        if ($_desc_mode) {
          $name = truncastring($name, 1000, 'ext', 'ez');
        }
        $name = authform($cook) . ":<br>\n$name";
      }
      mysqli_free_result($result_r);
    }
  }
  return $name;
}

function getextext0($id) {
  Global $dbi, $_desc_mode;
  $name = "";
  if (!($_desc_mode == 1)) {
    $sql_r = "select descr, cook from l_text where obj = 'e" . $id . "' and dtizm > 00000000000000";
    $result_r = $dbi->query($sql_r);
    if ($myrow_r = mysqli_fetch_array($result_r)) {
      $name = $myrow_r["descr"];
      $cook = $myrow_r["cook"];
      if ($name) {
        if ($_desc_mode) {
          # echo "truncastring";
          $name = truncastring($name, 1000, 'ext', 'ze');
        }
        $name = authform($cook) . ":<br>\n$name";
      }
    }
    mysqli_free_result($result_r);
  }
  return $name;
}

function getgmtype($ex_type) {
  $gmtype = "d";
  if ($ex_type == "2") {
    $gmtype = "o";
  }
  if ($ex_type == "3") {
    $gmtype = "e";
  }
  if ($ex_type == "t") {
    $gmtype = "t";
  }
  return $gmtype;
}

function gettripname($id) {
  Global $dbi;
  $txtobj = "";
  $sql_trip = "select txtobj from ourtrip  where id = $id";
  $result_trip = $dbi->query($sql_trip);
  if ($myrow_trip = mysqli_fetch_array($result_trip)) {
    $txtobj = $myrow_trip["txtobj"];
  }
  mysqli_free_result($result_trip);
  return $txtobj;
}

function utffromhtml($m) {
  return mb_convert_encoding($m[1], 'UTF-8', 'HTML-ENTITIES');
}

function textstring($html) {
  $tags = array(
      0 => '~<h[123][^>]+>~si',
      1 => '~<h[456][^>]+>~si',
      2 => '~<table[^>]+>~si',
      3 => '~<tr[^>]+>~si',
      4 => '~<li[^>]+>~si',
      5 => '~<br[^>]+>~si',
      6 => '~<p[^>]+>~si',
      7 => '~<div[^>]+>~si',
  );
  $html = preg_replace($tags, "\n", $html);
  $html = preg_replace('~</t(d|h)>\s*<t(d|h)[^>]+>~si', ' - ', $html);
  $html = preg_replace('~<[^>]+>~s', '', $html);
  // reducing spaces
  $html = preg_replace('~ +~s', ' ', $html);
  $html = preg_replace('~^\s+~m', '', $html);
  $html = preg_replace('~\s+$~m', '', $html);
  // reducing newlines
  $html = preg_replace('~\n+~s', " ", $html);
  $html = preg_replace('~\,+~s', "'", $html);
  return $html;
}

function kmlstring($html) {
  $tags = array(
      0 => '~<h[123][^>]+>~si',
      1 => '~<h[456][^>]+>~si',
      2 => '~<table[^>]+>~si',
      3 => '~<tr[^>]+>~si',
      4 => '~<li[^>]+>~si',
      5 => '~<p[^>]+>~si',
      6 => '~<div[^>]+>~si',
  );
  $html = preg_replace($tags, "\n", $html);
  $html = preg_replace('~</t(d|h)>\s*<t(d|h)[^>]+>~si', ' - ', $html);
  $html = preg_replace('~<[^>]+>~s', '', $html);
  // reducing spaces
  $html = preg_replace('~\t~s', ' ', $html);
  $html = preg_replace('~\s+~ms', ' ', $html);
  $html = preg_replace('~^\s+~m', '', $html);
  $html = preg_replace('~\s+$~m', '', $html);
  // reducing newlines
  $html = preg_replace('~\n+~ms', ' ', $html);
  $html = preg_replace_callback('~(\&#[0-9]+;)~', "utffromhtml", $html);
  $html = preg_replace('~\&~s', '&amp;', $html);
  $html = preg_replace('~\"~s', '`', $html);
  $html = preg_replace('~\'~s', '`', $html);
  $html = preg_replace('~<br>~si', '\n&lt;BR&gt;', $html);
  $html = preg_replace('~<b>~si', '', $html);
  $html = preg_replace('~</b>~si', '', $html);
  return $html;
}

function csvstring($html) {
  $tags = array(
      0 => '~<h[123][^>]+>~si',
      1 => '~<h[456][^>]+>~si',
      2 => '~<table[^>]+>~si',
      3 => '~<tr[^>]+>~si',
      4 => '~<li[^>]+>~si',
      5 => '~<p[^>]+>~si',
      6 => '~<div[^>]+>~si',
  );

  $html = preg_replace('~,~', '","', $html);

  $html = preg_replace($tags, ',', $html);
  $html = preg_replace('~</t(d|h)>\s*<t(d|h)[^>]+>~si', ' - ', $html);
  $html = preg_replace('~<br>~si', ',', $html);
  $html = preg_replace('~<b>~si', '', $html);
  $html = preg_replace('~</b>~si', '', $html);
  $html = preg_replace('~<[^>]+>~s', '', $html);
  // reducing spaces
  $html = preg_replace('~ +~s', ' ', $html);
  $html = preg_replace('~^\s+~m', '', $html);
  $html = preg_replace('~\s+$~m', '', $html);
  //$html = preg_replace_callback('~(\&#[0-9]+;)~', "utffromhtml", $html);
  $html = preg_replace('~\& ~m', 'and ', $html);
  // reducing newlines
  $html = preg_replace('~\n+~s', " ", $html);

  return $html;
}

function mycodespace($html) {
  $html = preg_replace('~\n+~s', " ", $html);
  $html = preg_replace('~^\s+~m', '', $html);
  $html = preg_replace('~\s+$~m', '', $html);
  $html = preg_replace('~ +~s', ' ', $html);
  $html = str_replace(" ", '_', $html);
  $html = str_replace("#", 'N', $html);
  return $html;
}

function comparecity($key, $city) {
  $array_key = explode(',', $key);
  $array_city = explode(',', $city);
  while (list($k, $v) = each($array_key)) {
    $v = trim($v);
    $v = strtr($v, 'ИЖСЙЕМЦЬЫГУЗТШБЮОПНКДФЩЪВЯЛХРЭАЧЛcCeEHKoOpPaAxXBMT', 'ижсйемцьыгузтшбюопнкдфщъвялхрэач»ЯяЕемйНнПпЮюУублр');
    $v = strtr($v, 'ижсйемцьыгузтшбюопнкдфщъвялхрэач»', 'bolc6f4qr9nsmt31higd58vzpjeaku2w7');
    $array_key[$k] = $v;
  }
  while (list($k, $v) = each($array_city)) {
    $v = trim($v);
    $v = strtr($v, 'ИЖСЙЕМЦЬЫГУЗТШБЮОПНКДФЩЪВЯЛХРЭАЧЛcCeEHKoOpPaAxXBMT', 'ижсйемцьыгузтшбюопнкдфщъвялхрэач»ЯяЕемйНнПпЮюУублр');
    $v = strtr($v, 'ижсйемцьыгузтшбюопнкдфщъвялхрэач»', 'bolc6f4qr9nsmt31higd58vzpjeaku2w7');
    $array_city[$k] = $v;
  }
  $r = sizeof(array_intersect($array_key, $array_city));
  return $r;
}

function mysafename($x) {
  $UploadNameChars = '0123456789qwertyuiopasdfghjklzxcvbnm.-_QWERTYUIOPASDFGHJKLZXCVBNM';

  $y = preg_replace("/[^$UploadNameChars]/", '', $x);
  $y = preg_replace('/\\.[^.]*$/', "strtolower('$0')", $y);
  $y = preg_replace('/^[^[:alnum:]]+/', '', $y);
  $y = preg_replace('/[^[:alnum:]_]+$/', '', $y);
  if (!($x === $y)) {
    $y = substr(md5($x), 0, 5) . $x;
    $y = preg_replace("/[^$UploadNameChars]/", '', $y);
    $y = preg_replace('/\\.[^.]*$/', "strtolower('$0')", $y);
    $y = preg_replace('/^[^[:alnum:]]+/', '', $y);
    $y = preg_replace('/[^[:alnum:]_]+$/', '', $y);
  }
  return $y;
}

function getskeytext($s_key, $rid, $link=0) {
  Global $dbi;
  if (($s_key == 'table') or ($link)) {
    if ($link) {
      $o_key=Array();  
    }  
    else {
      $o_key = "";
    }  
    $sql_r = "select t.name as tname, t.url as url, t.id as tid from towns t, t_reports r where t.id=r.tid and r.rid=$rid";
    $result_r = $dbi->query($sql_r);
    if ($result_r) {
     while ($myrow_r = mysqli_fetch_array($result_r)) {
      $name = $myrow_r["tname"];
      if ($link) {
        $url = $myrow_r["url"];
        $tid = $myrow_r["tid"];
        if ($url) {
          $name = '<a class="travell5n" href="/gorod/'.$url.'.html">'.$name.'</a>';  
        }
        else {
          $name = '<a class="travell5n" href="/town.php/'.$tid.'">'.$name.'</a>';  
        }
        array_push($o_key, $name);
      }
      else {
        if ($o_key) {
          $o_key = $o_key . ", $name";
        } else {
          $o_key = $name;
        }
      }  
     }
     mysqli_free_result($result_r);
    } 
  } else {
    $o_key = $s_key;
  }
  return $o_key;
}

function gettownnames($ids) {
  Global $dbi;
  $o_key = "";
  $sql_r = "select name from towns where id in ($ids)";
  $result_r = $dbi->query($sql_r);
  if ($result_r) {
   while ($myrow_r = mysqli_fetch_array($result_r)) {
    $name = $myrow_r["name"];
    if ($o_key) {
      $o_key = $o_key . ", $name";
    } else {
      $o_key = $name;
    }
   }
   mysqli_free_result($result_r);
  }
  
  
  
  return $o_key;
}

function getskeyid($rid) {
  Global $dbi;
  $o_key = "";
  $sql_r = "select tid from t_reports where rid=$rid";
  $result_r = $dbi->query($sql_r);
  if ($result_r) {
    $myrow_r = mysqli_fetch_array($result_r);
    $name = $myrow_r["tid"];
    if ($o_key) {
      $o_key = $o_key . ", $name";
    } else {
      $o_key = $name;
    }
    mysqli_free_result($result_r);
  }
  return $o_key;
}

function getskeyids($key) {//ищем id города по имени - полезно в отчетах
  Global $dbi;
  $ak = explode(',', $key);
  $ai = "";
  foreach ($ak as $k) {
    $xid = "";
    $name = trim($k);
    $sql_r = "select id from towns where name = '$name'";
    $result_r = $dbi->query($sql_r);
    if ($result_r) {
      if ($myrow_r = mysqli_fetch_array($result_r)) {
        $xid = $myrow_r["id"];
      }
      if ($xid) {
        if ($ai) {
          $ai = $ai . ", $xid";
        } else {
          $ai = $xid;
        }
      }
      mysqli_free_result($result_r);
    }
  }
  return $ai;
}

function fusername($author) {
  Global $dbi;
  $ret = $author;
  $sql = "select username, user_id from bl_users where user_id='$author'";
  $result = $dbi->query($sql);
  if ($myrow = mysqli_fetch_array($result)) {
    $name = $myrow["username"];
    $ret = $name;
  }
  return $ret;
}

function mark2stars($mark) {
  $ret = "&nbsp;&nbsp;";
  switch ($mark) {
    case 5:
      $ret = "++";
      break;
    case 4:
      $ret = "&nbsp;+";
      break;
    case 2:
      $ret = "&nbsp;-";
      break;
    case 1:
      $ret = "--";
      break;
  }
  return $ret;
}

function rate2stars($rate) {
  $ret = "";
  if ($rate > 100) {
    $ret = "+++";
    return $ret;
  }
  if ($rate > 10) {
    $ret = "++";
    return $ret;
  }
  if ($rate > 0) {
    $ret = "+";
    return $ret;
  }
  if ($rate < -100) {
    $ret = "???";
    return $ret;
  }
  if ($rate < -10) {
    $ret = "??";
    return $ret;
  }
  if ($rate < 0) {
    $ret = "?";
    return $ret;
  }
  return $ret;
}

function countfototown($town) {
  Global $dbi, $_foto_mode;
  $ii = 0;
  if (empty($town)) {
    return 0;
  }
  $ltown = (int) $town;
  if ($_foto_mode) {
    $sqladd = "";
  } else {
    $sqladd = " mode > 0 and (type = 1 or mode > 20) and ";
  }
  $sql = "select count(file) as ff from foto where $sqladd town = $ltown";
  $result = $dbi->query($sql);
  if ($myrow = mysqli_fetch_array($result)) {
    $ii = $myrow["ff"];
  }
  return $ii;
}

function countownfototown($town) {
  Global $dbi, $forum_user_id;
  if (!($town)) {
    return 0;
  }
  if (!($forum_user_id)) {
    return 0;
  }
  $ii = 0;
  $sql = "select count(file) as ff from foto where town = $town and foto.author like 'forum:" . $forum_user_id . ":%'";
  $result = $dbi->query($sql);
  if ($myrow = mysqli_fetch_array($result)) {
    $ii = $myrow["ff"];
  }
  return $ii;
}

function countfotoexc($ex) {
  Global $dbi, $_foto_mode;
  $ii = 0;
  if (empty($ex)) {
    return 0;
  }
  if ($_foto_mode) {
    $sqladd = "";
  } else {
    $sqladd = " mode > 0 and ";
  }
  $sql = "select count(file) as ff from foto where $sqladd exciting = $ex";
  $result = $dbi->query($sql);
  if ($myrow = mysqli_fetch_array($result)) {
    $ii = $myrow["ff"];
  }
  return $ii;
}

/**
 * Число "своих" фотографий объекта
 * @global object $dbi
 * @global int $forum_user_id
 * @param int $ext
 * @return int
 */
function countownfotoexc($ext) {
  Global $dbi, $forum_user_id;
  $exc = (int) $ext;
  if (!($exc)) {
    return 0;
  }
  if (!($forum_user_id)) {
    return 0;
  }
  $ii = 0;

  $sql = "select count(file) as ff from foto where exciting = $exc  and foto.author like 'forum:" . $forum_user_id . ":%'";
  $result = $dbi->query($sql);
  if ($myrow = mysqli_fetch_array($result)) {
    $ii = $myrow["ff"];
  }
  return $ii;
}

function getextypes() { //виды достопримов
  Global $dbi;
  $meta_types = array();
  $sql0 = "select *  from type_ex where id like '0%'";
  $result0 = $dbi->query($sql0);
  while ($myrow0 = mysqli_fetch_array($result0)) {
    $t_id = $myrow0["id"];
    $t_type = $myrow0["type"];
    $t_desc = $myrow0["desc"];
    $meta_types[$t_id] = $t_desc;
  }
  mysqli_free_result($result0);
  return $meta_types;
}

function getextypes2($id) {
  Global $dbi;
  $meta_types = array();
  $sql0 = "select * from type where id > $id";
  $result0 = $dbi->query($sql0);
  while ($myrow0 = mysqli_fetch_array($result0)) {
    $t_id = $myrow0["id"];
    $t_type = $myrow0["name"];
    $meta_types[$t_id] = $t_id . "." . $t_type;
  }
  mysqli_free_result($result0);
  return $meta_types;
}

function getextypes2i($id) { //аналогично без i, но с картинками
  Global $dbi;
  $meta_types = array();
  $sql0 = "select * from type where id > $id";
  $result0 = $dbi->query($sql0);
  while ($myrow0 = mysqli_fetch_array($result0)) {
    $t_id = $myrow0["id"];
    $t_type = $myrow0["name"];
    $t_icon = $myrow0["icon"];
    $meta_types[$t_id] = $t_id . "." . $t_type."|".$t_icon;
  }
  mysqli_free_result($result0);
  return $meta_types;
}

function sortextypes() { //отсортированные виды достопримов
  Global $dbi;
  $sort_types = array();
  $ii = 1;
  $sql0 = "select distinct `desc` from type_ex where id like '0%' order by `desc`";
  $result0 = $dbi->query($sql0);
  while ($myrow0 = mysqli_fetch_array($result0)) {
    $sort_types[$ii] = $myrow0["desc"];
    $ii = $ii + 1;
  }
  return $sort_types;
}

function fillnewsarr() {
  Global $newsarr, $sql, $dbi;
  $result = $dbi->query($sql);
  if ($myrow = mysqli_fetch_array($result)) {
    do {
      $id = $myrow["id"];
      $date = $myrow["date"];
      $datex = strftime("%d.%m.%Y", strtotime($date));
      $title = $myrow["title"];
      $newsarr[$date . $id] =
              "<tr><td align=right><b>$datex</b></td><td align=left>\n"
              . "<a href='//autotravel.ru/news.php?id=" . $id . "'>$title</a></td></tr>";
    } while ($myrow = mysqli_fetch_array($result));
  }
  mysqli_free_result($result);
}

function fotoreplace($l_text) {
  $s_rep = preg_replace("/(<img\s+src\=[^>]*?\.jpg[^>]*?>)/i", "$1", $l_text);
  $s_rep = preg_replace_callback("#\<img\s+src\=\"/phalbum/(\d+/\d+)-s\.jpg\"[^>]*?>#isu", "colobox", $s_rep);
  $s_rep = preg_replace_callback("#\<img\s+src\=\"/phalbum/(\d+/\d+)\.jpg\"[^>]*?>#isu", "colobox", $s_rep);
  $s_rep = preg_replace_callback("#\<img\s+src=/phalbum/(\d+/\d+)-s\.jpg[^>]*?>#isu", "colobox", $s_rep);
  $s_rep = preg_replace_callback("#\<img\s+src=/phalbum/(\d+/\d+)\.jpg[^>]*?>#isu", "colobox", $s_rep);
  $s_rep = preg_replace_callback("#\<img\s+src=[^>]*?autotravel.org.ru/[^>]*?(\d+/\d+)-s\.jpg[^>]*?>#isu", "colobox", $s_rep);
  $s_rep = preg_replace_callback("#\<img\s+src=[^>]*?autotravel.org.ru/[^>]*?(\d+/\d+)\.jpg[^>]*?>#isu", "colobox", $s_rep);
  $s_rep = preg_replace_callback("#\<img\s+src=[^>]*?autotravel.ru/[^>]*?(\d+/\d+)-s\.jpg[^>]*?>#isu", "colobox", $s_rep);
  $s_rep = preg_replace_callback("#\<img\s+src=[^>]*?autotravel.ru/[^>]*?(\d+/\d+)\.jpg[^>]*?>#isu", "colobox", $s_rep);  
  return $s_rep;
}

function draw_news($date, $title, $spring, $url, $text) {
  $datex = strftime("%d.%m.%Y", strtotime($date));
  echo "<h3 class=travell5>$datex: $title</h3>\n";
  echo "<p class=travell0>$text</p>\n";
  echo "<p class=travell0>\n";
  if ($url) {
    echo "Источник сведений: <b><a target=_blank href='$url'>$spring</a></b></p>\n";
  } else {
    echo "Источник сведений: <b>$spring</b></p>\n";
  }
}

// подстановка парных тегов

function closetags($text) {

// Выбираем абсолютно все теги
  if (preg_match_all("/<([\/]?)([\w\d]+)[^>\/]*>/", $text, $matches, PREG_SET_ORDER)) {
    $stack = array();
    foreach ($matches as $k => $match) {
      $tag = strtolower($match[2]);
      // если тег открывается добавляем в стек
      if (!$match[1]) {
        $stack[] = $tag;
      }

      // если тег закрывается, удаляем из стека
      elseif (end($stack) == $tag) {
        array_pop($stack);
      }
    }
  }
  if (is_array($stack)) {
   while ($tag = array_pop($stack)) {
    // закрываем все открытые теги, <br> не трогаем
    if ($tag <> "br") {
      $text .= '</' . $tag . '>';
    }
   }
  }
  return $text;
}

// Отрезаем кириллическую часть названия

function cyrname($text) {
  $name_arr = preg_split("/\//", $text);
  $name = $name_arr[0];
  if ($name == "") {
    $name = $text;
  }
  return $name;
}

// дробим описание объекта на отдельно описание, отдельно адрес и отдельно режим работы.

function split_descr($descr) {
  $t = $descr;
  $t = str_replace('\n',' ',$t);
  $t = str_replace('\r',' ',$t);
  $r[0] = ''; // описание
  $r[1] = ''; // адрес с телефоном вместе
  $r[2] = ''; // режим работы
  $r[3] = ''; // адрес без телефона
  $r[4] = ''; // Отдельно телефон
  // Если в описании слово Адрес: есть два раза, ничего не дробим, не переделываем а возвращаем as is
  $acount = mb_substr_count($descr, "Адрес:");
  if ($acount>1) {
    $r[0] = $descr;
  }
  else {
   preg_match('/^(.*)(\<b\>Адрес:\ *\<\/b\>)/uis',$t,$m);
   if (!empty($m[1])) {
    $r[0] = $m[1];
   }
   else {
    if (empty($m[2])) {   
      preg_match('/^(.*)\<b\>Режим работы:\ *\<\/b\>/uis',$t,$m);
      if (!empty($m[1])) {
        $r[0] = $m[1];
      }
    }
   }
   preg_match('/\<b\>Адрес:\ *\<\/b\>(.*?)\<b\>/uis',$t,$m);
   if (!empty($m[1])) {
    $r[1] = $m[1];
   }
   else {
    preg_match('/\<b\>Адрес:\ *\<\/b\>(.*?)$/uis',$t,$m);
    if (!empty($m[1])) {
      $r[1] = $m[1];
    }
   }
   preg_match('/\<b\>Режим работы:\ *\<\/b\>(.*)$/ui',$t,$m);
   if (!empty($m[1])) {
    $r[2] = $m[1];
   }
   if (!(empty($r[1]))) {
    $t = str_replace('<BR>',' ',$r[1]);
    preg_match('/^(.*)\,*\s*Тел(\.|:| )+(.*?)$/ui',$t,$m);
    if ((!empty($m[1])) && (!empty($m[3]))) {
     $r[3] = $m[1];
     $r[4] = $m[3];
    }
    else {
     $r[3] = $t;   
    }
   }
   if ((empty($r[1])) && (empty($r[2]))) {
    $r[0] = $descr; // ничего пропарсить не удалось  
   }
  }
  return $r;
}

function foto_find($txt) {
  Global $dbi;
  $file = '';
  preg_match('/(\d+)\/(\d+)/ui',$txt,$m);
  if ((!empty($m[1])) && (!empty($m[2]))) {
    $file = $m[1].'/'.$m[2]; 
  }
  if ($file) {
    $sql_f = "select file from foto where file='$file'";
    $result_f = $dbi->query($sql_f);
    $file = '';
    if ($result_f) {
      $file = mysqli_fetch_array($result_f)['file'];
      mysqli_free_result($result_f);
    }
  }
  return $file;
}

/* проверка пароля по phpBB hash */
function phpbb_userid($username, $password) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,"https://forum.autotravel.ru/at_chk.php");
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_ENCODING, "UTF-8" );
  curl_setopt($ch, CURLOPT_POSTFIELDS,
            array(
                'username' => base64_encode($username),
                'password' => base64_encode($password),
                 ));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  $response = (int) curl_exec($ch);
  curl_close ($ch);
  return $response;
}

function uft8_clearstr($string) {
  $s = trim($string);
  $s = iconv("UTF-8", "UTF-8//IGNORE", $s); // drop all non utf-8 characters
  // this is some bad utf-8 byte sequence that makes mysql complain - control and formatting i think
  $s = preg_replace('/(?>[\x00-\x1F]|\xC2[\x80-\x9F]|\xE2[\x80-\x8F]{2}|\xE2\x80[\xA4-\xA8]|\xE2\x81[\x9F-\xAF])/', ' ', $s);
  $s = preg_replace('/(\xF0\x9F[\x00-\xFF][\x00-\xFF])/', ' ', $s);
  $s = preg_replace('/\s+/', ' ', $s); // reduce all multiple whitespace to a single space
  return $s;
}

//функции, написанные в соавторстве с Колей
if (!function_exists('getfotoex_array')) {
    function getfotoex_array($id, $limit = 50) {
        $photoimg = getfotoex($id, $limit, "", 1);
        $photos = [];

        if (!empty($photoimg["all"])) {
            $html = $photoimg["all"];

            // парсим <a href="BIG.jpg"><img alt="..." src="SMALL-s.jpg">
            $pattern = '/<a[^>]+href=[\'"]([^\'"]+)[\'"][^>]*>\s*<img[^>]+alt=[\'"]([^\'"]*)[\'"][^>]*src=[\'"]([^\'"]+)[\'"][^>]*>\s*<\/a>/i';

            if (preg_match_all($pattern, $html, $matches, PREG_SET_ORDER)) {
                $i = 0;
                foreach ($matches as $m) {
                    $big   = $m[1]; // большой файл
                    $alt   = $m[2]; // подпись
                    $small = $m[3]; // маленький превью

                    $photos[] = [
                        "big"   => "https://autotravel.ru".$big,
                        "small" => "https://autotravel.ru".$small,
                        "alt"   => htmlspecialchars($alt ?: "Фото", ENT_QUOTES, "UTF-8"),
                    ];
                    $i++;
                    if ($i >= $limit) break;
                }
            }
        }

        return $photos;
    }
}
