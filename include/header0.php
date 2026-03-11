<?php
 include 'include/head01.php';
?>
<!-- Гугл эдсенс -->
<script>
  (adsbygoogle = window.adsbygoogle || []).push({
    google_ad_client: "ca-pub-2266145788867998",
    enable_page_level_ads: true
  });
</script>
<!-- Гугл эдсенс --> 

<table width=100% border=0 cellspacing=0 cellpadding=0>
<tr>
<td align=left>
 <TABLE cellSpacing=0 cellpadding=0 border=0 width=100%>

<?php if ($_text_mode != 1) { ?>
  <tr>
   <td width=169 height=147 rowspan=2 align=right valign=top>
    <a href="/index.php">
    <img border=0 src=/jpg/head01.jpg></a>
   </td>
   <td align=left valign=top>
     <a href="/index.php">

    <img border=0 src=/jpg/head02.jpg></a>
   </td>
   <td align=left valign=center>
<?php
// banners
   $banners_show_1 = "";
   $banners_show_2 = "";
   $banners_show_3 = "";


   if ($banners_show_1) {
     include_once($banners_show_1);
     $banners_shown = "yes";
   }
   else {
//     echo "$script_name";
   }
?>
   </td>
  </tr>
  <tr>

<?php } 
  else {
   echo "<tr><td width=169><a class=travell5m href=/index.php>К началу</a></td>\n";
   $banners_shown = "yes";
  }
?>

  <td align=left valign=bottom colspan=2>
   <table border=0 cellspacing=2 cellpadding=1>
   <TBODY class="t13 tl travell0t">
   <tr>
    <td><a class=travell5m href="/towns.php"><?php if ($_text_mode != 1) { ?><img src="/jpg/1p-towns.jpg" alt="Города" border=0><?php } else {echo "&diams;Города";}?></a></td>
    <td><a class=travell5m href="/search.php"><?php if ($_text_mode != 1) { ?><img src="/jpg/1p-ex.jpg"  alt="Достопримечательности" border=0><?php } else {echo "&diams;Достопримечательности";}?></a></TD>
    <td><a class=travell5m href="<?php echo $searchurl; ?>extype[]=2"><?php if ($_text_mode != 1) { ?><img src="/jpg/1p-hotel.jpg"  alt="Где остановиться" border=0><?php } else {echo "&diams;Где остановиться";}?></a></TD>
    <td><a class=travell5m href="<?php echo $searchurl; ?>extype[]=3"><?php if ($_text_mode != 1) { ?><img src="/jpg/1p-cafe.jpg" alt="Где поесть" border=0><?php } else {echo "&diams;Где поесть";}?></a></TD>
    <td><a class=travell5m href="/trace.php"><?php if ($_text_mode != 1) { ?><img src="/jpg/1p-trace.jpg" alt="Трассы" border = 0><?php } else {echo "&diams;Трассы";}?></a></td>
    <td><a class=travell5m href="/date.php"><?php if ($_text_mode != 1) { ?><img src="/jpg/2p-date.jpg" alt="События" border = 0><?php } else {echo "&diams;События";}?></a></td>
    <td><iframe src="/basket_w.php" name="basket" width="120" height="15" align="default"></iframe></td>
   </tr>
   </table>
  </td>
  </tr>
 </tbody>
 </table>
</td>
</tr>
<tr>
<td class=travell01x>
 <TABLE width=100% border=0 borderwidth=0 cellspacing=0 cellpadding=0>
  <td width=169 valign=top align=left class=travell01t>
  <table width=100% border=0 borderwidth=0 cellspacing=0 cellpadding=0>
   <TBODY class="t13 tl travell0t">
   <tr>
   <TD class=travell01y width=169>
     <a class=travell5m href="<?php echo $searchurl; ?>extype[]=1&amp;extype[]=6"><?php if ($_text_mode != 1) { ?><img src="/jpg/1p-find.jpg" alt="Поиск" border = 0><?php } else {echo "&diams;Поиск";}?></a>
   </TD>
   </tr>
   <TR>
   <TD class=travell01x width=169>
     <A class=travell5m href="/triptrack.php"><?php if ($_text_mode != 1) { ?><IMG SRC="/jpg/1p-rt.jpg" alt="Готовые маршруты" border="0"><?php } else {echo "&diams;Готовые&nbsp;маршруты";}?></A> 
   </TD>
   </tr>
   <tr>
   <TD class=travell01x width=169>
     <A class=travell5m href="/ch_trip.php"><?php if ($_text_mode != 1) { ?><IMG SRC="/jpg/1p-trip.jpg" alt="Проложить маршрут" border="0"><?php } else {echo "&diams;Проложить&nbsp;маршрут";}?></A> 
   </TD>
   </tr>
   <TR>
   <TD class=travell01x width=169>
     <A class=travell5m href="/rd_report.php"><?php if ($_text_mode != 1) { ?><IMG SRC="/jpg/2p-read.jpg" alt="Отчёты о поездках" border="0"><?php } else {echo "&diams;Отчеты&nbsp;о&nbsp;поездках";}?></A> 
   </TD>
   </tr>
   <tr>
   <TD class=travell01x width=169>
     <A class=travell5m href="/wr_report.php"><?php if ($_text_mode != 1) { ?><IMG SRC="/jpg/1p-write.jpg" alt="Написать отчет" border="0"><?php } else {echo "&diams;Написать&nbsp;отчет";}?></A> 
   </TD>
   </tr>
   <TR>
    <TD class=travell01x width=169>
    	<A class=travell5m href="/<?php echo $_fotoalbum; ?>"><?php if ($_text_mode != 1) { ?><IMG SRC="/jpg/1p-foto.jpg" alt = "Фотографии" border="0"><?php } else {echo "&diams;Фотоархив";}?></A> 
    </TD>
   </tr>
   <TR>
    <TD class=travell01x width=169>
         <A class=travell5m href="/map.php"><?php if ($_text_mode != 1) { ?><IMG SRC="/jpg/1p-maps.jpg" alt="Карты" border="0"><?php } else {echo "&diams;Карты";}?></A> 
    </TD>
   </tr>
   <TR>
   <TD class=travell01x width=169>
     <A class=travell5m href="/faq.php"><?php if ($_text_mode != 1) { ?><IMG SRC="/jpg/1p-faq.jpg" alt="ЧаВо" border="0"><?php } else {echo "&diams;ЧаВо";}?></A> 
   </TD>
   </tr>
   <tr>
   <TD class=travell01x width=169>
<?php
    if ($forum_user_id > 0) {
     echo "<A class=travell5m href=\"/sitemgr.php\">\n";
     if ($_text_mode != 1) {
      echo "<IMG SRC=\"/jpg/1p-club.jpg\" alt=\"Для форумчан\" border=\"0\"></A>\n";
     }
     else {
      echo "&diams;Для форумчан";
     }
    }
    else {
     echo "<A class=travell5m href=\"/sitemgr.php\">\n";
     if ($_text_mode != 1) {
      echo "<IMG SRC=\"/jpg/1p-logon.jpg\"  alt=\"Войти на сайт\" border=\"0\"></A>\n";
     }
     else {
      echo "&diams;Войти&nbsp;на&nbsp;сайт";
     }
    }
?>
   </TD>
   </tr>
   <TR>
    <TD class=travell01x width=169>
    	<A class=travell5m target=_blank href="<?php echo $forumname; ?>/index.php"><?php if ($_text_mode != 1) { ?><IMG SRC="/jpg/1p-forum.jpg" alt="Форум" border="0"><?php } else {echo "&diams;Форум";}?></A> 
   </TD>
   </tr>
   <tr>
   <TD class=travell01x width=169>
     <?php
      echo "<A class=travell5m href='$calname'>";
      if ($_text_mode != 1) {
       echo "<IMG SRC='/jpg/1p-cal.jpg' alt='Календарь поездок' border='0'></A>\n";
      }
      else {
       echo "&diams;Календарь&nbsp;поездок</a>\n";
      }
      if ($_short_menu != 1) { 
       include "include/ourdates.php";
      }
      ?>
   </TD>
   </tr>
   <tr>
   <TD class=travell01z width=169>
     <A class=travell5m href="/partner.php"><?php if ($_text_mode != 1) { ?><IMG SRC="/jpg/1p-partner.jpg"  alt="Вход для партнеров" border="0"><?php } else {echo "&diams;Для&nbsp;партнеров";}?></A> 
   </TD>
   </tr>


   <?php
   if ($_short_menu != 1) { 
   ?>
   <TR>
    <TD class=travell0 width=169>
    <center>
    <NOINDEX>

    <!--begin of Rambler's Top100 code -->
    <a href="https://top100.rambler.ru/top100/">
    <img src="https://counter.rambler.ru/top100.cnt?528928" alt="" 
    width=1 height=1 border=0></a>
    <!--end of Top100 code-->
    <!--begin of Top100 logo-->
    <a href="https://top100.rambler.ru/top100/"><img 
    src="https://top100-images.rambler.ru/top100/banner-88x31-rambler-gray2.gif"
    alt="Rambler's Top100" width=88 height=31 border=0></a>
    <!--end of Top100 logo -->
<!--LiveInternet counter-->
<script language="JavaScript">
<!--
document.write('<a href="https://www.liveinternet.ru/click" '+
'target=liveinternet><img src="https://counter.yadro.ru/hit?t43.2;r'+
escape(top.document.referrer)+((typeof(screen)=='undefined')?'':
';s'+screen.width+'*'+screen.height+'*'+(screen.colorDepth?
screen.colorDepth:screen.pixelDepth))+';'+Math.random()+
'" border=0 width=31 height=31 alt="liveinternet.ru"></a>')//--></script>
<!--/LiveInternet-->
     </NOINDEX>
     </center>
    </TD>
   </TR>


   <tr>
    <TD class=travell01x width=169>
    <font class=travell5m>&diams;Где бывали:</font><br><font class=travel0>Больше всех оставили отзывов:<br>



<?php
 $sql_wd="select user_id, username, user_excitings from bl_users order by user_excitings desc limit 10";
 $result_wd = $dbi->query($sql_wd);
 //echo $sql_wd;

 while ($myrow_wd = mysqli_fetch_array($result_wd)) {
  $ttuname   = $myrow_wd["username"];
  $ttuid  = $myrow_wd["user_id"];
  $ttucount  = $myrow_wd["user_excitings"];
  echo "&nbsp;&nbsp;<a href=/user.php/$ttuid> $ttuname ($ttucount)</a><br>";
 }


 }
?>
   </font>
   </TD>
   </tr>
  </tbody>
  </table>
 </td>
 <td valign=top class=travell01s>
