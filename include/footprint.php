<?php
if ($flagresult) {
 if (!($et_name)) {
  $xet_name = "объекты";
 }
 else {
  $xet_name = strtolower($et_name);
 }
?>
 <hr width='90%'>
 <Form name=print method=post action="/test00.php"  target="_blank">
 <table border=1  width='95%' cellpadding=8>
 <tbody><tr><td class=travell5>
 <input type=hidden name=ids value=<?php echo "\"$flagresult\""; ?>>
 <input type=hidden name=search value=<?php $search=htmlspecialchars($search,NULL,''); echo "\"$search\""; ?>>
 <input type=hidden name=resheader value=<?php $restext=htmlspecialchars($restext,NULL,''); echo "\"$restext\""; ?>>
 Вывести все найденные <?php echo $xet_name;?> в отчет для печати
 <input class=atb type=submit value='На принтер'>
 </td>
 <td width=275 class=travell0>
 <input type=checkbox name=otk value=1>С отзывами<br>
 <input type=checkbox name=photo value=1>С фотографиями
 </td></tr></table>
 </form>
<?php
 if ($_nomap) {
  kmlfooter($flagresult,$restext,"Все найденные $xet_name");
 }
 echo "<br>&nbsp;";
}

?>