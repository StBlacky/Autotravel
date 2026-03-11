<?php
include_once('include/config.php');
include_once('include/func.php');
include_once('include/head00.php');
include_once('include/fotoman.php');
$file     = "";
$author   = 0;
$town     = 0;
$area     = 0;
$ex       = 0;
$done     = 0;
$start    = 0;
$cat      = "";
$www_dir = "/home/blacky/data/www";

if ($_pin == "") {
 $title = "Фотографии: информация\n";
 include('include/header.php');
 echo "<table width='100%' border=0 cellpadding=3>\n<tbody class=travell0><tr><td>\n";
 include('phalbum.html');
 echo "</td></tr></table>\n";
 include('include/footer.php');
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
if (preg_match("/^\/area\/(\-*\d+)$/",$_pin,$exa)) {
 $area = $exa[1];
 $start  = 0;
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
// $ij = countfotoexc($ex);???????

 $metatags = "$ii фотографии $exname";
 $metadesc = "$ii фото на тему $exname ($tnname)";
 $title = "$exname ($tnname), $ii фотографии";
 $colorbox = 1;
 include('include/header.php');


 echo "<h1>Фотографии $exname</h1>\n";
 echo "<h2>$tnname</h2>\n";
 echo "<table width='100%' border=0 cellpadding=3>\n<tbody class=\"t12 tl travell0t\">\n";
 echo "<tr><td colspan=2>\n";
 echo "<p class=travel5>Если щёлкнуть по маленькой фотографии, откроется новая страница с увеличенной фотографией.<br>\n";
 echo "С полной информацией на тему <b>$exname</b> и отзывами посетителей можно ознакомиться в разделе <a class=travell5n href='/otklik.php/$ex'>$exname</a></p>\n";
 if ($ik > $ij ) {
  echo "<p class=travel5>Все фотографии ($ik фото) города Вы можете посмотреть в разделе <a class=travell5n href='/".$script_name."/town/$town'>Фотографии города $tnname</a>.</p>\n";
 }
 echo "<p class=travel5>Полную информацию о городе $tnname можно получить на нашем сайте, в разделе Описание города <a class=travell5n href='/town.php/$town'>$tnname</a>.</p>\n";

 echo "</td></tr>\n";
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

   echo "<td width='50%' class=travell01f>\n";
   //echo "<a target=_blank href='/".$script_name."/$file' rel='nofollow'><img border=0 src='/phalbum/". $file . "-s.jpg' alt=\"Фото $exname $tnname\" title=\"Фото $exname $tnname\"></a><br>\n";
   echo "<div align=center><a title='$exname $tnname Фото: $author' class='atcbox' href='/phalbum/$file.jpg'><img src='/phalbum/$file-s.jpg'></a></div><br>\n";
#  if ($author) {
#   echo "<p><b>Автор</b> $author</p>\n";
#  }
   if ($comment) {
    echo "<p><b>Комментарий</b> $comment</p>\n";
   }
   if (!($mode)) {
    echo "<p>Не участвует в индексах</p>\n";
   }
   if ($forum_user_id) {
     echo "<p>Линк для вставки фото в отчет: " .fotolink($file). "</p>\n";
   }
   if ($_fotomanager) {
    echo "<a target=_blank href='/fotoman.php?utown=$town&amp;ufile=$file'>Редактировать</a><br>";
    if (($mode > 0) and ($mode < 10)) {
     echo "<a target=_blank href='/fotoman.php?utown=$town&amp;ufile=$file&amp;mode=0&amp;action=newmode' rel='nofollow'>Удалить из индекса</a>\n";
    }
    if (!($mode)) {
     echo "<a target=_blank href='/fotoman.php?utown=$town&amp;ufile=$file&amp;mode=1&amp;action=newmode' rel='nofollow'>Добавить к индексу</a>\n";
    }
   }
   echo "</td>";
 }
 echo "</tr></table>\n<br>\n";
 mysqli_free_result($result);
 include('include/footer.php');
 $done = 1;
}

if (($file) and (!($done))) {//одна фотка
 $sql = "select exciting, town, author, comment, sale from foto
         where file = '$file'";
 $result = $dbi->query($sql);
 if($myrow = mysqli_fetch_array($result)) {
   $exciting= $myrow["exciting"];
   $town   = $myrow["town"];
   $author = $myrow["author"];
   $comment= $myrow["comment"];
   $sale   = $myrow["sale"];
   $exname = getexname($exciting);
   $tnname = gettownname($town);
   $sale   = saleformat("$file:$sale");
   $author = authformat($author,"album");
   $done = 1;
   $metatags = "фото $exname $tnname";
   $metadesc = "Фотография: $exname";
   $title = "Фотография $exname. Город $tnname.";
   $h1 = " <a class=travell5n title =\"Полная информацией об объекте $exname: адрес, режим работы, отзывы.\" href=\"/otklik.php/$exciting\">$exname</a>";
   $h2 = " <a class=travell5n title =\"Полная информацию о городе $tnname: достопримечательности, гостиницы, кафе, интерактивная карта. \" href='/town.php/$town'>$tnname</a>";
   include("include/header.php");

   $ik = countfototown($town);
   $photoimg = getfotoex($exciting,1,"",1);
   $ij=$photoimg["display"] + $photoimg["hidden"];
   
   echo "<div itemscope itemtype=\"http://schema.org/ImageObject\">";
   echo "<h1 itemprop=\"name\">$h1</h1>";
   echo "<h2>$h2</h2>";
?>
   <!-- предложение поделиться  в социальной сети -->
    
    <script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
    <p class="t12 tl travell0t"> <b>Поделитесь </b> этой фотографией в социальных сетях. Таким образом Вы <b>поможете</b> популяризации сайта. <div class="yashare-auto-init" data-yashareL10n="ru"  data-yashareImage="https://autotravel.ru/jpg/chudick.gif"
                                                             data-yashareType="button" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir,lj,gplus"></div></p>
     <!-- предложение поделиться  в социальной сети -->
<?php
     echo "<table width='100%' border=0 cellpadding=3>\n<tbody class=travell0><tr><td align=center>\n";
   $fn = "phalbum/" . $file . ".jpg";
   $fp = $www_dir . "/" . $fn;
   if (is_file($fp)) {


    echo "<img border=0 src='/$fn' alt='Фотография $exname ($tnname)' title='Фотография $exname ($tnname)' itemprop=\"contentUrl\"><br>\n";
   }
   else {
    echo "$fp\n";
   }

   if ($author) {
    echo "<p><b>Автор</b><span itemprop=\"author\"> $author</span></p>\n";
   }
   if ($comment) {
    echo "<p><b>Комментарий</b><span itemprop=\"description\"> $comment</span></p>\n";
   }
   echo "<p class=travell6> Всего в фотоархиве $ij <a class=travell5n href='/".$script_name."/ex/$exciting'>фото $exname</a> и  $ik <a class=travell5n href='/".$script_name."/town/$town'>фото $tnname</a>.</p>\n";


   if ($forum_user_id) {
    echo "<p>Линк для вставки в отчет: " .fotolink($file). "</p>\n";
   }
   // if ($sale) {
   //  echo "<p>$sale</p>\n";
   // }
   echo "</td></tr></table></div>\n";
   include("include/footer.php");
 }
// mysqli_free_result($result);
// echo $sql;
}


if (($town) and (!($done))) {
 $tnname = gettownname($town);
 $ownfoto = countownfototown($town);
 $ik = countfototown($town);
 $metatags = "фото $tnname";
 $metadesc = "$ik фото на тему $tnname";
 $title = "$tnname фотографии";
 $colorbox = 1;
 include('include/header.php');
 echo "<table width='100%' border=0 cellpadding=3>\n<tbody class=travell0>\n";
 echo "<tr><td colspan=2>\n";
 echo "<h1>Фотографии города $tnname</h1>\n";
 if ($ownfoto > 0) {
     echo "<p class=travell5><a  class=travell5n target=_blank  href='/fotoman.php?utown=$town' rel='nofollow'>Собственные фотографии $tnname</a>: $ownfoto</p>\n";
 }
 echo "<p class=travell5>Полную информацию о городе $tnname можно получить на нашем сайте, в разделе Описание города <a class=travell5n href='/town.php/$town'>$tnname</a>.</p>\n";
 echo "<p class=travell5>$ik фото по городу $tnname. Если щёлкнуть по маленькой фотографии, откроется новая страница с увеличенной фотографией.</p>\n";
 echo "</td></tr>\n";

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
   $sale   = saleformat("$file:$sale");
   $author = authformat($author,"nolink");
   $exname = getexname($exciting);
   if (($exname== 'Нет названия') and ($comment)) {
       $exname = $comment;
   }
   if ($ii == 0) {
    echo "<tr>";
   }
   $ii++;
   if ($ii > 2) {
    $ii = 1;
    echo "</tr>\n<tr>";
   }

   echo "<td width='50%' class=travell01f align=center>\n";
#   echo "<a target=_blank href='/".$script_name."/$file' rel='nofollow'><img border=0 src='/phalbum/". $file . "-s.jpg' alt=\"Фото $exname ($tnname)\" title=\"$tnname: $exname\"></a><br>\n";
   echo "<div align=center><a title='$exname $tnname Фото: $author' class='atcbox' href='/phalbum/$file.jpg'><img alt='$tnname: $exname' src='/phalbum/$file-s.jpg'></a></div><br>\n";
#   if ($author) {
#    echo "<p><b>Автор</b> $author</p>\n";
#   }
   if ($exciting) {
    echo "<b>$exname</b><br>";
   }
   if ($comment) {
    echo " $comment\n";
   }
   if (!($mode)) {
    echo "<p>Не участвует в индексах</p>\n";
   }
   if ($forum_user_id) {
    echo "<p>Линк для вставки фото в отчет: " .fotolink($file). "</p>\n";
   }
   if ($_fotomanager) {
    echo "<a target=_blank href='/fotoman.php?utown=$town&amp;ufile=$file' rel='nofollow'>Редактировать</a><br>";
    if (($mode > 0) and ($mode < 10)) {
     echo "<a target=_blank href='/fotoman.php?utown=$town&amp;ufile=$file&amp;mode=0&amp;action=newmode' rel='nofollow'>Удалить из индекса</a>\n";
    }
    if (!($mode)) {
     echo "<a target=_blank href='/fotoman.php?utown=$town&amp;ufile=$file&amp;mode=1&amp;action=newmode' rel='nofollow'>Добавить к индексу</a>\n";
    }
   }
   echo "</td>";
 }
 echo "</tr></table>\n";
 mysqli_free_result($result);
 include('include/footer.php');
 $done = 1;
}

if (($author) and (!($done))) {
 $authv  = getauthor($author);
 $authn  = authformat($authv,"txt");

 $title = "Фотографии. " . $authn;
 $colorbox = 1;
 include('include/header.php');
 echo "<table width='100%' border=0 cellpadding=3>\n<tbody class=travell0>\n";
 echo "<tr><td colspan=2>\n";
 echo "<h3>Фотографии $authn.</h3>\n";
 echo "</td></tr>\n";
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
   if ($ii == 0) {
    echo "<tr>";
   }
   $ii++;
   if ($ii > 2) {
    $ii = 1;
    echo "</tr>\n<tr>";
   }
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

   echo "<td width='50%' class=travell01f>\n";
   echo "<div align=center>";
   #echo "<a target=_blank href='/".$script_name."/$file'><img border=0 src='/phalbum/". $file . "-s.jpg' alt=\"Фото $exname $tnname\"></a></div><br>\n";
   echo "<a title='$exname $tnname Фото: $authn' class='atcbox' href='/phalbum/$file.jpg'><img src='/phalbum/$file-s.jpg'></a></div><br>\n";
   if ($mode > 0) {
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
   }
   else {
    echo "<p>Не участвует в индексах</p>\n";
   }
   if ($forum_user_id) {
    echo "<p>Линк для вставки в отчет: " .fotolink($file). "</p>\n";
   }
   if ($comment) {
    echo "<p><b>Комментарий</b> $comment</p>\n";
   }
   echo "</td>";
 }
 $ii = mysqli_num_rows($result);
 mysqli_free_result($result);
 echo "</tr></table>\n";
 echo "<table border=0 width='100%'><tr><td width='50%' align=right>\n";

 if ($start > 0) {
   echo "<form method=post action=/".$script_name."/author/$author/$f1><input type=submit value='<<<Предыдущие $_count_list'></form>\n";
 }
 echo "</td><td width='50%' align=left>\n";
 if ($ii >= $_count_list) {
   echo "<form method=post action=/".$script_name."/author/$author/$f2><input type=submit value='Следующие $_count_list>>>'></form>\n";
 }
 echo "</td></tr></table>\n";
 include('include/footer.php');
 $done = 1;
}

if (($area) and (!($done))) {
 $areaname  = getareaname($area);

 $title = "Фотографии. " . $areaname;
 $colorbox = 1;
 include('include/header.php');
 echo "<table width='100%' border=0 cellpadding=3>\n<tbody class=travell0>\n";
 echo "<tr><td colspan=2>\n";
 echo "<h3>Фотографии <a href='/area.php/$area'>$areaname</a></h3>\n";
 echo "</td></tr>\n";
 $f1 = $start - $_count_list;
 if ($f1 < 0) {
  $f1 = 0;
 }
 $f2 = $start + $_count_list;
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
   $sale   = $myrow["sale"];
   $sale   = saleformat("$file:$sale");
   if ($ii == 0) {
    echo "<tr>";
   }
   $ii++;
   if ($ii > 2) {
    $ii = 1;
    echo "</tr>\n<tr>";
   }
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

   echo "<td width='50%' class=travell01f>\n";
   echo "<div align=center><a title='$exname $tnname Фото: $author' class='atcbox' href='/phalbum/$file.jpg'><img src='/phalbum/$file-s.jpg'></a></div><br>\n";
   #echo "<div align=center><a target=_blank href='/".$script_name."/$file'><img border=0 src='/phalbum/". $file . "-s.jpg' alt=\"Фото $exname $tnname\"></a></div><br>\n";
   if ($mode > 0) {
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
   }
   else {
    echo "<p>Не участвует в индексах</p>\n";
   }
   if ($comment) {
    echo "<p><b>Комментарий</b> $comment</p>\n";
   }
   if ($forum_user_id) {
    echo "<p>Линк для вставки в отчет: " .fotolink($file). "</p>\n";
   }
   echo "</td>";
 }
 $ii = mysqli_num_rows($result);
 mysqli_free_result($result);
 echo "</tr></table>\n";
 echo "<table border=0 width='100%'><tr><td width='50%' align=right>\n";

 if ($start > 0) {
   echo "<form method=post action=/".$script_name."/area/$area/$f1><input type=submit value='<<<Предыдущие $_count_list'></form>\n";
 }
 echo "</td><td width='50%' align=left>\n";
 if ($ii >= $_count_list) {
   echo "<form method=post action=/".$script_name."/area/$area/$f2><input type=submit value='Следующие $_count_list>>>'></form>\n";
 }
 echo "</td></tr></table>\n";
 include('include/footer.php');
 $done = 1;
}


if (($cat) and (!($done))) {
 $title = "Фотографии. Каталог " . $cat;
 $colorbox = 1;
 include('include/header.php');
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
 mysqli_free_result($result);
 include('include/footer.php');
 $done = 1;
}


if (!($done)) {
 include "404.php";
}

?>
