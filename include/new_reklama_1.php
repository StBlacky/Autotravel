<?php

$maxstrings = 8; // всего рекламы
$upper = 6; // выше гугля = 5 == 6-1
$yesterday = date('Y-m-d',(time() - (24*3600)));
$sql="select e.name as name, t.name as town, e.id as id, Right( e.name, length( e.name ) - locate( '\"', e.name )) AS loc, r.reklama as rek "
        . "from towns t, exciting e, reklama r where t.id = e.town and r.ex = e.id and r.srock > '$yesterday' and r.auth > 0 "
        . "order by r.dtizm desc limit $maxstrings";
$result = mysql_query($sql, $db);

//третий столбец главной таблицы - реклама
echo "<td width=260 class=travell01r valign=top>\n";

//также оформляем в виде таблицы - переделываем на див

//echo "<table class=travell0 cellpadding=5 cellspacing=0>\n";
echo "<div class=\"rightreklama\">";

//echo "<tr><td class=travel12>\n";
if ($_text_mode != 1) {
 echo "<img src='/jpg/p-welc.jpg' alt='Нас приглашают'>";
}
else {
 echo "<font class=travell5n>Нас&nbsp;приглашают<br>*****</font>";
}
// echo "</td></tr>\n";  
?>

<!--<tr><TD class=travel12> -->
<ul>
    <li>
       <A class=travell5m href="/partner.php" rel="nofollow">
       <?php if ($_text_mode != 1) { ?><IMG SRC="/jpg/1p-partner.jpg"  alt="Вход для партнеров" border="0" title="Информация для действущих и потенциальных партнеров.">
       <?php } else {echo "&diams;Для&nbsp;партнеров";}?></A>
<!-- </TD></tr> -->
    </li>
 <?php    
$upperpart="";
$lowerpart="";
$ii=0;

if ($myrow = mysql_fetch_array($result)) {
    do {
    	$e_name = $myrow["name"];
    	$e_id = $myrow["id"];
	if ($myrow["rek"]) {
            $e_rek = preg_replace('@<script[^>]*?>.*?</script>@si', '', $myrow["rek"]);
            $e_rek = preg_replace("/\<br\>\<br\>/i","<br>",$myrow["rek"]);
	    $excerpt_length = 20;
            $words = explode(' ', $e_rek, $excerpt_length + 1);
            if (count($words)> $excerpt_length) {
               	array_pop($words);
                $e_rek = implode(' ', $words);
            }
        } else { $e_rek = "";}
            $e_rek = closetags($e_rek) . " ...";
            $e_town = $myrow["town"];
           // $currentpart="<tr><td align=center class=travell01x>\n";
            $currentpart="<li>";
            $currentpart.="<a class=travell5r href='/otklik.php/$e_id'>$e_name ($e_town)</a><br>\n";
           // $currentpart.="$e_rek</td></tr>\n";
            $currentpart.="$e_rek</li>";
            $ii++;
            if ($ii<$upper) {
                $upperpart.=$currentpart;
            }
            else {
                $lowerpart.=$currentpart;
        }
    } while($myrow = mysql_fetch_array($result));
}
 echo "<\ul>";
 echo $upperpart;
 if ($_text_mode != 1) {
 ?>
     <!-- Автотревел, первая страница -->
<!--
    <div> <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        
         <ins class="adsbygoogle"
               style="display:block"
               data-ad-client="ca-pub-2266145788867998"
               data-ad-slot="7150907469"
               data-ad-format="auto"></ins>
        <script>(adsbygoogle = window.adsbygoogle || []).push({}); </script>

    </div>-->
<?php
 }
   echo $lowerpart;
// echo "<tr><td class=travel12 align=center>\n";
   echo "<div>";
   if ($_text_mode != 1) {
      echo "<img src='/jpg/rekl.jpg' alt='Рекламка'>";
   }
    else {
      echo "<font class=travell5n>Рекламка</font>";
   }
  // echo "</td></tr>\n";
   
?>
    

<!--<tr><td align=center class=travell01x>-->
    <br><b>
<a href="http://koltrova.narod.ru/" rel="nofollow">Дизайн интерьеров, экстерьеров, витрин, сайтов, полиграфия, логотип и т.п.</a>
</b>
<!--</td></tr>-->

<?php
//echo "</table></td>\n"; //честно закрыли - и таблицу, и 3 столбец главной таблицы
echo "</div></td>\n"; //честно закрыли - и таблицу, и 3 столбец главной таблицы

?>
