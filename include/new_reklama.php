<?php

$maxstrings = 8; // всего рекламы
$upper = 6; // выше гугля = 5 == 6-1
$yesterday = date('Y-m-d',(time() - (24*3600)));
$sql="select e.name as name, t.name as town, e.id as id, Right( e.name, length( e.name ) - locate( '\"', e.name )) AS loc, r.reklama as rek "
        . "from towns t, exciting e, reklama r where t.id = e.town and r.ex = e.id and r.srock > '$yesterday' and r.auth > 0 "
        . "order by r.dtizm desc limit $maxstrings";
$result = $dbi->query($sql);

//третий столбец главной таблицы - реклама
echo "<td width=260 class=travell01r valign=top>\n";

echo "<div class=\"right-reklama\">";

if ($_text_mode != 1) {
 echo "<img src='/jpg/p-welc.jpg' alt='Нас приглашают'>";
}
else {
 echo "<font class=travell5n>Нас&nbsp;приглашают<br>*****</font>";
}
?>

<ul>
    <li>
       <A class=travell5m href="/partner.php" rel="nofollow">
       <?php if ($_text_mode != 1) { ?><IMG SRC="/jpg/1p-partner.jpg"  alt="Вход для партнеров" border="0" title="Информация для действущих и потенциальных партнеров.">
       <?php } else {echo "&diams;Для&nbsp;партнеров";}?></A>
   </li>
 <?php    
$upperpart="";
$lowerpart="";
$ii=0;

if ($myrow = mysqli_fetch_array($result)) {
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
            $currentpart="<li>";
            $currentpart.="<a class=travell5r href='/otklik.php/$e_id'>$e_name ($e_town)</a><br>\n";
            $currentpart.="$e_rek</li>";
            $ii++;
            if ($ii<$upper) {
                $upperpart.=$currentpart;
            }
            else {
                $lowerpart.=$currentpart;
        }
    } while($myrow = mysqli_fetch_array($result));
}
 
 echo $upperpart;

 echo $lowerpart;
 echo "</ul>";

 echo "<div class=\"reklamka\">";
 if ($_text_mode != 1) {
      echo "<img src='/jpg/rekl.jpg' alt='Рекламка'>";
 }
 else {
      echo "<font class=travell5n>Рекламка</font>";
 }
   
?>
    

    <br><b>
<a href="http://koltrova.narod.ru/" rel="nofollow">Дизайн интерьеров, экстерьеров, витрин, сайтов, полиграфия, логотип и т.п.</a>
</b>

<?php
echo "</div></td>\n"; //честно закрыли - и таблицу, и 3 столбец главной таблицы

?>
