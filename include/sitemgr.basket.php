<?php

if (($admt == 'basket')) {
 $action  = mysafestring($_REQUEST['s']);
 $basketid  = (int) $_REQUEST['basket'];


 if ($action=="new") {
  $sqlx = "insert into basket (userid,comment,descrip,sum) values('$forum_user_id','','',0)";
  file_put_contents("/home/blacky/data/www/sql/ins-basket.sql",
          "-------------\n".$sqlx.";\n-------------\n\n",FILE_APPEND);
  $dbi->query($sqlx);
  $forum_user_basket  = mysqli_insert_id();
  $forum_user_basket_name = "N ".$_forum_user_basket;
  $sqlx = "update bl_users set user_basket=$forum_user_basket where user_id=$forum_user_id";
  $dbi->query($sqlx);
 }

 if ($action=="select") {
   $sqlx    = "select * from basket where id=$basketid and userid=$forum_user_id";
   $resultx = $dbi->query($sqlx);
   $basketid="";
   if($myrowx = mysqli_fetch_array($resultx)) {
       $forum_user_basket  = $myrowx["id"];
       $forum_user_basket_name = $myrowx["comment"];
       $forum_user_basket_count = $myrowx["sum"];
   }
   mysqli_free_result($resultx);
   if ($basketid) {
     $sqlx = "update bl_users set user_basket=$_forum_user_basket where user_id=$forum_user_id";
     $dbi->query($sqlx);
   }
 }
 $sqlx="select * from basket where userid='$forum_user_id' order by id desc";
 $resultx = $dbi->query($sqlx);
 $count=0;
 $optlist="";
 while ($myrowx = mysqli_fetch_array($resultx)) {
   $b_user = $myrowx["userid"];
   $b_id = $myrowx["id"];
   $b_descr = $myrowx["comment"];
   $b_content = $myrowx["descrip"];
   $b_count = $myrowx["sum"];
   $count=$count + 1;
   if (!($b_descr)) {
       $x_descr="N ".$b_id;
   }
   else {
       $x_descr = $b_descr;
   }
   if ($count == 1) {
    $id = $b_id;
    $optlist .= "<option selected value=$b_id>$x_descr\n";
   }
   else {
    $optlist .= "<option value=$b_id>$x_descr\n";
   }
 }
 if ($count != 1 ) {
     $id="" ; # нарисовать выбор
     echo "Выбрать поездку для редактирования: <form method=post action=/basket_v.php>\n";
     echo "<select size=1 name=id>".$optlist."</select>\n";
     echo "<input type=submit name=submitt value=\"Выбрать\">\n";
     echo "</form>\n";
 }
 mysqli_free_result($result);

}

?>
