<?php 
include_once('include/config.php');
include_once('include/func.php');
include_once('include/func_bs.php');
include_once('include/head00.php');
include_once('include/fotoman.php');
$bootstrap = 1;
$file     = "";
$author   = 0;
$town     = 0;
$area     = 0;
$ex       = 0;
$done     = 0;
$start    = 0;
$cat      = "";
$www_dir = "/home/blacky/data/www";
$ramadiv = "<div id=\"fotos\" class=\"fotorama\" data-width=\"800px\" data-minheight=\"300px\" data-maxheight=\"800px\" data-ratio=\"4/3\" data-navposition=\"top\" \n";
$ramadiv .= " data-nav=\"thumbs\" data-thumbheight=\"200px\" data-thumbwidth=\"200px\" data-thumbfit=\"scaledown\" data-auto=\"false\">\n";
$ramaend = "</div>\n<div id=\"fotocaption\"></div>\n";
$ramafill = "<script language=\"JavaScript\">\n"
        . "var frdiv = $('#fotos').fotorama();\n"
        . "var fotorama = frdiv.data('fotorama');\n"
        . "var frcaption = $('#fotocaption');\n"
        . "$(function () {"
        . " $('#fotos').on('fotorama:show',\n"
        . "  function (e, fotorama, direct) {\n"
        . "   var activeFrame = fotorama.activeFrame;\n"
        . "   frcaption.html(activeFrame.comment + '<br>' + (fotorama.activeIndex + 1) + '/' + fotorama.size);\n"
        . "  }\n"
        . " ).fotorama();\n"
        . "});\n"; 
$ramafill .= "function ramaresize() {\n"
        . " var actwidth=($(window).width());\n"
        . " if (actwidth>971) {"
        . "  actwidth=($(window).width() - 220);\n"
        . " }\n"
        . " else {"
        . "  actwidth=($(window).width() - 60);\n"
        . " }\n"
        . " if (actwidth>260) {"
        . "  fotorama.resize({width: actwidth }); changed=1;\n"
        . " }\n"
        . " var activeFrame = fotorama.activeFrame;\n"
        . " frcaption.html(activeFrame.comment + '<br>' + (fotorama.activeIndex + 1) + '/' + fotorama.size);\n"
        . "};\n"
        . "$(window).resize(\n"
        . " function() {\n"
        . "  ramaresize();\n"
        . " }\n"
        . ");\n"
        . "ramaresize();\n"
        . "</script>\n";
if ($_pin == "") {
 $title = "Фотографии: информация\n";
 include('include/header_bs.php');
 echo "<div class=\"container-fluid\" style=\"width:100%;\">\n";
 include('phalbum.html');
 echo "</div>\n";
 include('include/footer_bs.php');
 $done = 1;
}

if (preg_match("/^\/(\d+)\/(\d+)$/",$_pin,$exa)) {
 $file = $exa[1] . "/" . $exa[2];
}
if (preg_match("/^\/(\d+)$/",$_pin,$exa)) {
 $cat = $exa[1];
}
if (preg_match("/^\/(\d+)\/$/",$_pin,$exa)) {
 $cat = $exa[1];
}
if (preg_match("/^\/(\d+)\/index.html$/",$_pin,$exa)) {
 $cat = $exa[1];
}
if (preg_match("/^\/town\/(\d+)$/",$_pin,$exa)) {
 $town = $exa[1];
}
if (preg_match("/^\/area\/(\-*\d+)\/(\d+)$/",$_pin,$exa)) {
 $area = $exa[1];
 $start  = $exa[2];
}
if (preg_match("/^\/ex\/(\d+)$/",$_pin,$exa)) {
 $ex = $exa[1];
}
if (preg_match("/^\/author\/(\d+)\/(\d+)$/",$_pin,$exa)) {
 $author = $exa[1];
 $start  = $exa[2];
}

if (preg_match("/^\/index\/author\.(.+)$/",$_pin,$exa)) {
 $authcode = $exa[1];
// найти $author;
}
if (preg_match("/^\/index\/city\.(.+)$/",$_pin,$exa)) {
 $towncode = $exa[1];
// найти $town;
}
if (preg_match("/^\/index\/exciting\.(.+)$/",$_pin,$exa)) {
 $excode = $exa[1];
// найти $ex;
}

if ($town == 9999) {
 $town = "";
}

if ($_fotomanager) {
 $sqladd = "";
}
else {
 $sqladd = " mode > 0 and ";
}

if ($_foto_mode) {
 $sqladd = "";
}

if (($ex > 0) and (!($done))) {//есть объект
 $file="";
 $photoimg = getfotoex($ex,1,"",1);
 $ii=$photoimg["display"] + $photoimg["hidden"];
 if ($ii=="1") {
  $sql = "select file from foto where $sqladd exciting = $ex";
  $result = $dbi->query($sql);
  if($myrow = mysqli_fetch_array($result)) {
   $file = $myrow["file"];
  }
 }
 if ($file) {
  $ex="";
 }
}
if (($town) and (!($done))) {//есть город
 $file="";
 $ii=countfototown($town);
 if ($ii=="1") {
  $sql = "select file from foto where $sqladd town = $town";
  $result = $dbi->query($sql);
  if($myrow = mysqli_fetch_array($result)) {
   $file = $myrow["file"];
  }
 }
 if ($file) {
  $town="";
 }
}

if (($ex > 0) and (!($done))) {//все фото одного объекта
 list($town,$tnname,$exname) = explode("\t",gettownex($ex));

 $ik = countfototown($town);

 $metatags = "$ii фотографии $exname";
 $metadesc = "$ii фото на тему $exname ($tnname)";
 $title = "$exname ($tnname), $ii фотографии";
 $fotorama = 1;
  include('include/header_bs.php');

  echo "<div class=\"container-fluid\" style=\"width:100%;\">\n";
  echo '<ol class="breadcrumb">';
  if ($town != 9999) {
    echo '<li><a href="/phalbum.php/town/'.$town.'">' . $tnname . '</a></li>';
  }
  echo '<li>' . $exname . '</li>';
  echo "</ol>";
 /*
 echo "<h1>Фотографии $exname</h1>\n";
 if ($town != 9999) {
  echo "<h2>$tnname</h2>\n";
 }
  * 
  */
 echo "<p class=travel5>С полной информацией и отзывами посетителей можно ознакомиться в разделе <a class=travell5n href='/otklik.php/$ex'>$exname</a></p>\n";
 if (($ik > $ij ) and ($town != 9999)) {
  echo "<p class=travel5>Все фотографии ($ik фото) города Вы можете посмотреть в разделе <a class=travell5n href='/".$script_name."/town/$town'>Фотографии города $tnname</a>.</p>\n";
 }
 if ($town != 9999) {
  echo "<p class=travel5>Полную информацию о городе $tnname можно получить на нашем сайте, в разделе Описание города <a class=travell5n href='/town.php/$town'>$tnname</a>.</p>\n";
 }
 echo "<div style='text-align:center;'>\n" . $ramadiv;

 $ii = 0;
 $sql = "select file, author, comment, mode from foto
         where $sqladd exciting = $ex order by mode desc, date desc";
 $result = $dbi->query($sql);
 while($myrow = mysqli_fetch_array($result)) {
   $file   = $myrow["file"];
//   $exciting= $myrow["exciting"];
//   $town   = $myrow["town"];
   $author = $myrow["author"];
   $comment= $myrow["comment"];
   $mode   = $myrow["mode"];
   $author = authformat($author,"nolink");
   $dcomment = "";
   if ($comment) {
    $dcomment .= $comment ;
   }
   $dcomment .= "<br>Фото: <b>".$author."</b>";
   if (!($mode)) {
    $dcomment .= "<br>Не участвует в индексах<br>";
   }
   if ($forum_user_id) {
    $dcomment .= "<br>Линк для вставки фото в отчет: " .fotolink2($file);
   }
   // микроразметка
   //echo "<div itemscope itemtype=\"http://schema.org/ImageObject\">\n";
   //echo "<span style='display:none; visibility:hidden' itemprop='name'>$exname</span>\n";
   //echo "<span style='display:none; visibility:hidden' itemprop='caption'>$exname $tnname. Фото: $author</span>\n";
   //if ($comment) {
   //     echo "<span style='display:none; visibility:hidden' itemprop='description'>$comment</span>\n";
   //}
   echo "<a title='$exname $tnname Фото: $author' href='/phalbum/$file.jpg' data-comment='$dcomment'>\n";
   echo "<img alt='$tnname: $exname' src='/phalbum/$file-s.jpg' >\n";
   echo "</a>\n"; // 
   // echo "</div>\n"; // 
 }
 mysqli_free_result($result);
 echo $ramaend;
 echo "</div>\n";
 echo "</div>\n";
 include('include/footer_bs.php');
 $done = 1;
}

if (($file) and (!($done))) {//одна фотка
 $ramafill = "";   
 $fn = "phalbum/" . $file . ".jpg";
 $fp = $www_dir . "/" . $fn;
 $sql = "select exciting, town, author, comment from foto where file = '$file'";
 // error_log($sql);
 $result = $dbi->query($sql);
 if ($myrow = mysqli_fetch_array($result)) {
   $exciting = (int) $myrow["exciting"];
   $town   = (int) $myrow["town"];
   $author = $myrow["author"];
   $comment= $myrow["comment"];
   if ($exciting > 0) {
     $exname = getexname($exciting);
   }
   else {
     $exname = "";
   }
   if ($town > 0) {
     $tnname = gettownname($town);
   }
   else {
     $tnname = "";
   }
   if ((!($exname)) and (!($tnname))) {
     $fotoname = $file.".jpg";
   }
   else {
     if ($exname) {
       $fotoname = "$exname, город $tnname"; 
     }
     else {
       $fotoname = "города $tnname";
     }
   }    
   $author = authformat($author,"album");
   $done = 1;
   $metatags = "фото $fotoname";
   $metadesc = "Фотография $fotoname";
   $title = "Фотография $fotoname";
   if ($exname) {
     $h1 = " <a class=travell5n title =\"Полная информацией об объекте $exname: адрес, режим работы, отзывы.\" href=\"/otklik.php/$exciting\">$exname</a>";
   }
   if ($tnname) {
     $h2 = " <a class=travell5n title =\"Полная информацию о городе $tnname: достопримечательности, гостиницы, кафе, интерактивная карта. \" href='/town.php/$town'>$tnname</a>";
   }
   include("include/header_bs.php");

   if ($tnname) {
     $ik = countfototown($town);
   }
   else {
     $ik = 0;
   }
   if ($exname) {
     $photoimg = getfotoex($exciting,1,"",1);
     $ij=$photoimg["display"] + $photoimg["hidden"];
   }
   else {
     $photoimg = "";
     $ij = 0;
   }
   echo "<div class=\"container-fluid\" style=\"width:100%;\">\n";   
   echo "<div itemscope itemtype=\"http://schema.org/ImageObject\">";
   if (($tnname) or ($exname)) {
     echo '<ol class="breadcrumb">';
     if ($tnname) {
       echo '<li>' . $h2 . '</li>';
     }
     if ($exname) {
       echo '<li>' . $h1 . '</li>';
     }
     echo "</ol>";
   }
   echo "<div style='text-align: center;'>\n";
   if (is_file($fp)) {
    echo "<img border=0 src='/$fn' alt='Фотография $fotoname' title='Фотография $fotoname' itemprop=\"contentUrl\"><br>\n";
   }
   else {
    echo "$fp\n";
   }

   if ($author) {
    echo "<p><b>Автор</b> <span itemprop=\"author\">$author</span></p>\n";
   }
   if ($comment) {
    echo "<p><b>Комментарий</b> <span itemprop=\"description\">$comment</span></p>\n";
   }
   if (($ij + $ik)>0) {
     echo "<p class=travell6>Всего в фотоархиве ";
     if ($ij > 0) {
       echo "$ij <a class=travell5n href='/".$script_name."/ex/$exciting'>фото $exname</a> и ";
     }
     if ($ik > 0) {
       echo "$ik <a class=travell5n href='/".$script_name."/town/$town'>фото $tnname</a>.";
     }
     echo "</p>\n";
   }

   if ($forum_user_id) {
    echo "<p>Линк для вставки в отчет: " .fotolink($file). "</p>\n";
   }
   echo "</div>\n";
   echo "</div>\n";
?>
   <!-- предложение поделиться  в социальной сети -->
    
   <script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
   <p class="t12 tl travell0t"> <b>Поделитесь </b> этой фотографией в социальных сетях. Таким образом Вы <b>поможете</b> популяризации сайта. <div class="yashare-auto-init" data-yashareL10n="ru"  data-yashareImage="https://autotravel.ru/jpg/chudick.gif"
                                                             data-yashareType="button" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir,lj,gplus"></div></p>
   <!-- предложение поделиться  в социальной сети -->
<?php
   echo "</div>\n";
   include("include/footer_bs.php");
   $done = 1;
 }
 elseif (is_file($fp)) {
   // файл есть, записи в таблице нет
   $title = "Фотография ".$file.".jpg";
   include("include/header_bs.php");
   echo "<div class=\"container-fluid\" style=\"width:100%;\">\n";   
   echo "<img border=0 src='/$fn' alt='$title' title='$title'><br>\n";
   echo "</div>\n";
   include("include/footer_bs.php");
   $done = 1;
 }
 else {
   // файла тоже нет
   $newuri = "/";
   Header( "HTTP/1.1 301 Moved Permanently" );
   Header( "Location: $newuri" );
   exit;   
 }
// echo $sql;
}


if (($town) and (!($done))) {
 $tnname = gettownnamearr($town);
 if ($tnname[10]) {
    $newuri = "/gorod/" . $tnname[10] . ".html";
    Header( "HTTP/1.1 301 Moved Permanently" );
    Header( "Location: $newuri" );
    exit;
 }  
 $ownfoto = countownfototown($town);
 $ik = countfototown($town);
 $metatags = "фото ".$tnname[6];
 $metadesc = "$ik фото ".$tnname[6];
 $title = "Фотографии ".$tnname[6];
 $fotorama = 1;
 include('include/header_bs.php');
 echo "<div class=\"container-fluid\" style=\"width:100%;\">\n";
 echo "<h1>Фотографии ".$tnname[6]."</h1>\n";
 if ($ownfoto > 0) {
  echo "<p class=travell5><a class=travell5n target=_blank  href='/fotoman.php?utown=$town' rel='nofollow'>Собственные фотографии ".$tnname[6]."</a>: $ownfoto</p>\n";
 }
 if ($town != 9999) {
  echo "<p class=travell5>Полную информацию по ".$tnname[7]." можно получить на нашем сайте, в разделе Описание <a class=travell5n href='/town.php/$town'>$tnname[6]</a>.</p>\n";
 }
 echo "<p class=travell5>$ik фото ".$tnname[6]."</p>\n";
 echo "<table width='100%' border=0 cellpadding=3>\n<tbody class=travell0>\n";
 echo "<tr><td id=\"fotocontainer\"align=center valign=top>\n" . $ramadiv;
 $ii = 0;
 $sql = "select file, exciting, author, comment, mode from foto
         where $sqladd town = '$town' order by mode desc, date desc";
 $result = $dbi->query($sql);
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
   $dcomment = "";
   if ($exciting) {
    $dcomment .= "<b><a class=travell5n target=\"_blank\" href=\"/otklik.php/$exciting\">$exname</a></b>";
   }
   if ($comment) {
    $dtitle .= $comment ;
   }
   $dcomment .= "<br>Фото: <b>".$author."</b>";
   if (!($mode)) {
    $dcomment .= "<br>Не участвует в индексах<br>";
   }
   if ($forum_user_id) {
    $dcomment .= "<br>Линк для вставки фото в отчет: " .fotolink2($file);
   }
   echo "<a title='$exname $tnname[3] Фото: $author' href='/phalbum/$file.jpg' data-comment='$dcomment'>\n";
   echo "<img alt='$tnname[3]: $exname' src='/phalbum/$file-s.jpg'></a>\n"; // 
 }
 echo $ramaend;
 echo "</td></tr>\n";
 echo "</table>\n";
 mysqli_free_result($result);
 echo "</div>\n"; 
 ?> 
 <?php
 include('include/footer_bs.php');
 $done = 1;
}

if (($author) and (!($done))) {
 $authv  = getauthor($author);
 $authn  = authformat($authv,"txt");

 $title = "Фотографии. " . $authn;
 $fotorama = 1;
 include('include/header_bs.php');
 echo "<div class=\"container-fluid\" style=\"width:100%;\">\n";
 echo "<h3>Фотографии $authn.</h3>\n";
 echo "<table width='100%' border=0 cellpadding=3>\n<tbody class=travell0>\n";
 echo "<tr><td id=\"fotocontainer\"align=center valign=top>\n" . $ramadiv;
 $f1 = $start - $_count_list;
 if ($f1 < 0) {
  $f1 = 0;
 }
 $f2 = $start + $_count_list;
 $ii = 0;
 $sql = "select file, exciting, town, comment, mode from foto
         where author = '$authv' order by file desc
         LIMIT $start, $_count_list";
 $result = $dbi->query($sql);
 while($myrow = mysqli_fetch_array($result)) {
   $file   = $myrow["file"];
   $mode   = $myrow["mode"];
   $exciting= $myrow["exciting"];
   $town   = $myrow["town"];
   $comment= $myrow["comment"];
   $exname = "";
   $tnname = "";
   if ($mode > 0) {
    if ($exciting) {
     list($town,$tnname,$exname) = explode("\t",gettownex($exciting));
    }
    else {
     if ($town) {
      $tnname = gettownname($town);
     }
    }
   }

   if ($town != 9999) {
    $dcomment = "Город <b>".$tnname."</b>";
   }
   else {
    $dcomment = "";   
   }
   if ($exciting) {
    $dcomment .= "<br><b><a class=travell5n target=\"_blank\" href=\"/otklik.php/$exciting\">$exname</a></b>";
   }
   if ($comment) {
    $dcomment .= "<br>".$comment ;
   }
   if (!($mode)) {
    $dcomment .= "<br>Не участвует в индексах<br>";
   }
   if ($forum_user_id) {
    $dcomment .= "<br>Линк для вставки фото в отчет: " .fotolink2($file);
   }
   echo "<a title='$exname $tnname Фото: $authn' href='/phalbum/$file.jpg' data-comment='$dcomment'>\n";
   echo "<img alt='$tnname: $exname' src='/phalbum/$file-s.jpg'></a>\n"; // 
 }
 $ii = mysqli_num_rows($result);
 mysqli_free_result($result);
 echo $ramaend;
 echo "</td></tr>\n";
 echo "</table>\n";
 
 echo "<table border=0 width='100%'><tr><td width='50%' align=right>\n";
 if ($start > 0) {
   echo "<form method=post action=/".$script_name."/author/$author/$f1><input type=submit value='<<<Предыдущие $_count_list'></form>\n";
 }
 echo "</td><td width='50%' align=left>\n";
 if ($ii >= $_count_list) {
   echo "<form method=post action=/".$script_name."/author/$author/$f2><input type=submit value='Следующие $_count_list>>>'></form>\n";
 }
 echo "</td></tr></table>\n";
 echo "</div>\n";
 include('include/footer_bs.php');
 $done = 1;
}

if (($area) and (!($done))) {
 $areaname  = getareaname($area);

 $f1 = $start - $_count_list;
 if ($f1 < 0) {
  $f1 = 0;
 }
 $f2 = $start + $_count_list;
 $fotorama = 1;
 //
 $title = "Фотографии. " . $areaname;
 include('include/header_bs.php');
 echo "<div class=\"container-fluid\" style=\"width:100%;\">\n";
 echo "<h3>Фотографии <a href='/area.php/$area'>$areaname</a></h3>\n";
 echo "<table width='100%' border=0 cellpadding=3>\n<tbody class=travell0>\n";
 echo "<tr><td id=\"fotocontainer\"align=center valign=top>\n" . $ramadiv;
 $ii = 0;
 $sql = "select f.file as file, f.author as author, f.exciting as exciting, f.town as town,
         f.comment as comment, f.sale as sale, f.mode as mode from foto f, towns t
         where t.area = '$area' and t.id=f.town and f.mode > 0
         order by f.mode desc, f.date desc, f.file desc
         LIMIT $start, $_count_list";
 $result = $dbi->query($sql);
 while($myrow = mysqli_fetch_array($result)) {
   $file   = $myrow["file"];
   $author = $myrow["author"];
   $author = authformat($author,"nolink");
   $mode   = $myrow["mode"];
   $exciting= $myrow["exciting"];
   $town   = $myrow["town"];
   $comment= $myrow["comment"];
   $exname = "";
   $tnname = "";
   if ($mode > 0) {
    if ($exciting) {
     list($town,$tnname,$exname) = explode("\t",gettownex($exciting));
    }
    else {
     if ($town) {
      $tnname = gettownname($town);
     }
    }
   }
   $dcomment = "Город <b>".$tnname."</b>";
   if ($exciting) {
    $dcomment .= " <b><a class=travell5n target=\"_blank\" href=\"/otklik.php/$exciting\">$exname</a></b>";
   }
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
   echo "<a title='$exname $tnname Фото: $author' href='/phalbum/$file.jpg' data-comment='$dcomment'>\n";
   echo "<img alt='$tnname: $exname' src='/phalbum/$file-s.jpg'></a>\n"; // 
 }
 $ii = mysqli_num_rows($result);
 mysqli_free_result($result);
 echo $ramaend;
 echo "</td></tr>\n";
 echo "</table>\n";

 echo "<table border=0 width='100%'><tr><td width='50%' align=right>\n";
 if ($start > 0) {
   echo "<form method=post action=/".$script_name."/area/$area/$f1><input type=submit value='<<<Предыдущие $_count_list'></form>\n";
 }
 echo "</td><td width='50%' align=left>\n";
 if ($ii >= $_count_list) {
   echo "<form method=post action=/".$script_name."/area/$area/$f2><input type=submit value='Следующие $_count_list>>>'></form>\n";
 }
 echo "</td></tr></table>\n";
 echo "</div>\n";
 include('include/footer_bs.php');
 $done = 1;
}

if (($cat) and (!($done))) {
 $title = "Фотографии. Каталог " . $cat;
 $colorbox = 1;
 include('include/header_bs.php');
 echo "<div class=\"container-fluid\" style=\"width:100%;\">\n";
 echo "<table width='100%' border=0 cellpadding=3>\n<tbody class=travell0>\n";
 echo "<tr><td colspan=2>\n";
 echo "<h3>Фотографии. Каталог $cat.</h3>\n";
 echo "</td></tr>\n";

 $ii = 0;
 $sql = "select file, town, exciting, author, comment, sale, mode from foto
         where $sqladd file like '$cat/%' order by file asc";
 $result = $dbi->query($sql);
 while($myrow = mysqli_fetch_array($result)) {
   $file   = $myrow["file"];
   $exciting= $myrow["exciting"];
   $town   = $myrow["town"];
   $mode   = $myrow["mode"];
   $author = $myrow["author"];
   $comment= $myrow["comment"];
   $sale   = $myrow["sale"];
   $sale   = saleformat("$file:$sale");
   $author = authformat($author,"nolink");
   if ($ii == 0) {
    echo "<tr>";
   }
   $ii++;
   if ($ii > 2) {
    $ii = 1;
    echo "</tr>\n<tr>";
   }

   if ($exciting) {
     list($town,$tnname,$exname) = explode("\t",gettownex($exciting));
   }
   else {
     if ($town) {
      $tnname = gettownname($town);
     }
   }

   echo "<td width='50%' class=travell01f>\n";
   echo "<div align=center><a title='$exname $tnname Фото: $author' class='atcbox' href='/phalbum/$file.jpg'><img src='/phalbum/$file-s.jpg'></a></div><br>\n";
   #echo "<div align=center><a target=_blank href='/".$script_name."/$file'><img border=0 src='/phalbum/". $file . "-s.jpg' alt=\"Фото $exname $tnname\"></a></div><br>\n";
   if ($author) {
    echo "<p><b>Автор</b> $author</p>\n";
   }
   if ($exciting) {
     echo "<p><b>Город</b> $tnname";
     echo "</p>\n";
     echo "<p><b>$exname</b>";
     echo "</p>\n";
   }
   else {
     if ($town) {
      echo "<p><b>Город</b> $tnname";
      echo "</p>\n";
     }
   }
   if ($comment) {
    echo "<p><b>Комментарий</b> $comment</p>\n";
   }
   if (!($mode)) {
    echo "<p>Не участвует в индексах</p>\n";
   }
   if ($forum_user_id) {
    echo "<p>Линк для вставки в отчет: " .fotolink($file). "</p>\n";
   }
   if ($_fotomanager) {
    echo "<a target=_blank href='/fotoman.php?utown=$town&amp;ufile=$file'>Редактировать</a><br>";
    if (($mode > 0) and ($mode < 10)) {
     echo "<a target=_blank href='/fotoman.php?utown=$town&amp;ufile=$file&amp;mode=0&amp;action=newmode'>Удалить из индекса</a>\n";
    }
    if (!($mode)) {
     echo "<a target=_blank href='/fotoman.php?utown=$town&amp;ufile=$file&amp;mode=1&amp;action=newmode'>Добавить к индексу</a>\n";
    }
   }
   echo "</td>";
 }
 echo "</tr></table>";
 echo "</div>\n";
 mysqli_free_result($result);
 include('include/footer_bs.php');
 $done = 1;
}

if (!($done)) {
 include "404.php";
}

?>
