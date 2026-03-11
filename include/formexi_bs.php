<?php
 ?>
<div class="row well">
  <Form name="print" method="post" action="/exi.php"  target="_blank">
  <div class="col-sm-4 col-xs-12">
  <?php    
   if ($exlist) {
     echo "<input type=hidden name=list value='$exlist'>\n";
   }
   if ($addhead) {
     echo "<input type=hidden name=head value='$addhead'>\n";
   }
   echo "<input type=hidden name=town value=$town>\n";
  ?> 
    <p class="travell0">Информацию по <?php echo $s_namepo;?> 
    </p>
    <input class="atb" type="submit" value='Распечатать на принтер'>
  </div>  
  <div class="col-sm-4 col-xs-12">
     <?php
       reset($r_types); 
       while(list($type_id,$type_name)=each($r_types)) {
         if ($c_excitings[$type_id]) {
           echo "<label class=\"checkbox\"><input type=\"checkbox\" name=et0".$type_id." value=1 CHECKED>".$type_name["name"]."</label>\n";
         }  
      }
      ?>
      <label class="checkbox"><input type="checkbox" name="otk" value="1">С отзывами</label>
      <label class="checkbox"><input type="checkbox" name="photo" value="1">С фотографиями</label>
  </div>  
  <div class="col-sm-4 col-xs-12">
  </div>    
  </form>
</div>  
