<?php
//////////////////////////////////////////////////////////////////////////////

// полномочия по (!!! новой !!!) трасе - begin
 if (($traces_enable) && ($admt == 'trace')) {
    //если в форме нажали кнопку 
    if ($_POST['submittrace']) {  
       $t_name = mysafestring($_REQUEST["t_name"]);
       $of_name = mysafestring($_REQUEST["of_name"]);
       $comment = mysafestring($_REQUEST["comment"]);
       $type = mysafestring($_REQUEST["type"]);
       $spring = mysafestring($_REQUEST["spring"]);
       $forum = mysafestring($_REQUEST["forum"]);
       $_traceid = 0;
       $_name = "";
       $_submit = "1";
       
       echo "<h3>Ввод новой трассы:\n</h3>";
       // добавление. исправления пока нет.
    
       if ($of_name) {
          $sql="select id, name, of_name from trace where of_name = '".$of_name."'";
          //echo $sql;
          $result = $dbi->query($sql);
          if ($result) {
             $myrow = mysqli_fetch_array($result);  
             if($myrow) {
                $of_name = $myrow['of_name'];
                $t_name = $myrow['name'];
                echo "<h4>Трасса с кодом $of_name  $t_name уже есть в базе. Повторный ввод не нужен!</h4>";
                $_submit ="";
                $_traceid=0;
             }
          }
          mysqli_free_result($result);
       }  
       
       if ($_submit) {
          $sql="select max(id) as traceid from trace";
          $result = $dbi->query($sql);
          if ($result) {
             $myrow = mysqli_fetch_array($result); 
             $_traceid = (int) $myrow["traceid"];
          }
          mysqli_free_result($result);
       }

       if ($_traceid) {
          $_traceid=$_traceid + 1;
          $sql_in = "insert into trace
                 (id, name, of_name, comment, type, state) values
                 ('$_traceid','$t_name','$of_name','$comment','$type','$forum')";
          //echo $sql_in;
          $result_in = $dbi->query($sql_in);
          if ($result_in) {
             //надо еще в far_tr
             $sql_ft = "insert into far_tr
                 (trace,town,cw_far,bw_far) values
                 ('$_traceid','$spring','0','0')";
             $result_ft = $dbi->query($sql_ft);
             if ($result_ft) {
                echo "<h3>Трасса $name успешно добавлена id=$_traceid</h3>\n";
                $_traceid  = 0;
                $traceid  = 0;
                $t_name  = "";     
                $of_name  = "";
                $comment     = "";
                $type     = "";
                $forum    = "";
                $spring    = "";
                mysqli_free_result($result_ft);
              }  else {
                echo "<h3>При добавлении произошла ошибка\n<BR>";
                echo mysqli_errno().": ".mysqli_error()."</h3>";
              }
              
          } else {
              echo $sql_in;
                
             echo "<h3>При добавлении произошла ошибка\n<BR>";
             echo mysqli_errno().": ".mysqli_error()."</h3>";
          }
          mysqli_free_result($result_in);
       }
  
 
   }
   // если даже было добавление/исправление, всё равно форму ввода показать надо.

   //типы трасс
   $types = array();
   $sql="select id, descrip from trace_t order by descrip asc";
   $result = $dbi->query($sql);
   if ($result) {
      while ($myrow = mysqli_fetch_array($result)) {
        $types[$myrow["id"]] = $myrow["descrip"];
      }
   }
   mysqli_free_result($result);
  
   echo "<div style='width:100%;text-align:center;'>";
   echo "<form  name=trace action=".$_SERVER["PHP_SELF"]." method=post >\n";
   echo "<INPUT name=admt type=hidden value='trace'>\n";
   echo "<p><label style='padding:6px;' for=of_name>Код трассы (Е95)</label>";
   echo "<INPUT id=of_name name=of_name type=text size=10 value='$of_name'>";
   echo "<input type=button name=sx  onclick=SelId('trace','of_name','/f_trace.php') value='Проверить'></p>";
   echo "<label style='padding:6px;' for=t_type>Вид трассы</label>";
   echo "<select size=1 id=t_type name=type>";
   echo option_list($types,$type);
   echo "</select></p>";
   echo "<label style='padding:6px;' for=spring>Код города 0 км</label>";
   echo "<INPUT id=spring name=spring type=text size=10 value='$spring' required>";
   echo "<input type=button name=ssxx  onclick=SelId('trace','spring','/f_town.php') value='Найти город'></p>";
   echo "<p><label style='padding:6px;' for=t_name>Название трассы</label><br>";
   echo "<INPUT id=t_name name=t_name type=text size=160 value='$t_name' required></p>";
  
   echo "<p><label style='padding:3px;' for=comment>Комментарий - если трасса заслуживает отдельной информации, например, паромы.</label><br>";
   echo "<INPUT id=comment name=comment type=text size=160 value='$comment'></p>";
   echo "<p><label style='padding:3px;' for=forum>ID темы форума, где обсуждается состояние данной трассы</label><br>";
   echo "<INPUT id=forum name=forum type=text size=10 value='$forum'></p>";
   echo "<INPUT name=submittrace type=submit value='Добавить трассу'>\n";
   echo "</FORM>\n";
   echo "</div>";
}
// полномочия по новой трассе - end


//////////////////////////////////////////////////////////////////////////////
?>
