<?php

 include_once("include/config.php");
 $sql = "select post_id, poster_id from bl_posts";
 $result = $dbi->query($sql); 
 while($myrow = mysqli_fetch_array($result)){
   $id = $myrow["post_id"];
   $uid = $myrow["poster_id"];
   $sql2 = "select post_id from bl_posts_text where post_id = $id";
   $result2 = $dbi->query($sql2); 
   $id2 = 0;
   if($myrow = mysqli_fetch_array($result2)){
    $id2 = $myrow["post_id"];
   };
   if ( $id != $id2 ) {
    echo "delete from bl_posts where post_id = $id;<br>\n";
   }
   else {
    echo "                            \n";
   }
   $sql2 = "select user_id from bl_users where user_id = $uid";
   $result2 = $dbi->query($sql2); 
   $uid2 = 0;
   if($myrow = mysqli_fetch_array($result2)){
    $uid2 = $myrow["user_id"];
   };
   if ( $uid != $uid2 ) {
    echo "delete from bl_posts where post_id = $id;<br>\n";
   }
   else {
    echo "                            \n";
   }
 };


?>
