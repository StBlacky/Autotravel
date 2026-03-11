<?php
if ($flagresult) {
 if (!($et_name)) {
  $xet_name = "объекты";
 }
 else {
  $xet_name = strtolower($et_name);
 }
?>
<div class="row autocardw">
<div class="col-md-12 col-xs-12 well">
  <Form name="print" method="post" action="/test00.php"  target="_blank">
    <input type=hidden name=ids value=<?php echo "\"$flagresult\""; ?>>
    <input type=hidden name=search value=<?php $search=htmlspecialchars($search,NULL,''); echo "\"$search\""; ?>>
    <input type=hidden name=resheader value=<?php $restext=htmlspecialchars($restext,NULL,''); echo "\"$restext\""; ?>>
    <p class="travell0">Все найденные <?php echo $xet_name;?> вывести в отчет 
      <label class="checkbox-inline"><input type="checkbox" name="otk" value="1">С отзывами</label>
      <label class="checkbox-inline"><input type="checkbox" name="photo" value="1">С фотографиями</label>
      <input class="atb" type="submit" value='На принтер'>
    </p>
  </form>
</div>  
<?php
 if ($_nomap) {
   echo '<div class="col-md-12 col-xs-12 well">';
   echo kmlfooter_bs($flagresult,$restext,"Все найденные $xet_name");
   echo '</div>';
 }
}
?>
</div>