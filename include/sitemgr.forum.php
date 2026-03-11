<?php

 if ($admt == 'forum') {
    if ($_user_is_admin == '1') { 
       $curdate = $_REQUEST["curdate"];
       if ($curdate == '') $curdate = date('d-m-Y');
       //   echo $curdate;
 ?>

    <script type="text/javascript" >
       var $zdp = $('#element').data('Zebra_DatePicker');
       $(document).ready(function() {
          //  $(this).val() =   $today;  
          $('input.datepicker').Zebra_DatePicker({
                direction: false
          });
        });
    </script>
   
   <?php 
  
  
    $sql = "SELECT DISTINCT bu.user_id, bu.username as username FROM `bl_posts` as bp, `bl_users` as bu WHERE FROM_UNIXTIME(bp.post_time,'%d-%m-%Y') = '$curdate' and bu.user_id = bp.poster_id ORDER BY bu.username";
    //echo "$sql";
    $username = "";
    $result = $dbi->query($sql);
    if ($result) {
       echo  '<form action="" metod = method="POST" class="mycurdate"><input type="text" name = "curdate" class="datepicker" value="'.$curdate .'">'
           . '<input type="hidden" name="admt" value="forum">'
           . '<input type="submit" value = "Подтвердить дату"></form>' ;
       echo "<br>\n Сегодня на форуме писали: \n";
       while ($myrow = mysqli_fetch_array($result)) {
          $username   = $myrow["username"];
          echo "<a href=\"https://forum.autotravel.ru/search.php?author_id=".$myrow["user_id"]."&sr=posts\" target=\"_blank\" style=\"font-weight: bold;\">$username</a> ";
       }  
    }
    mysqli_free_result($result);
 }
}

?>
