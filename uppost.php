<?php

phpinfo();
exit;

include_once('include/config.php');
include_once('include/func.php');
include('include/head01.php');



$sql="select topic_id, max(post_id) as lastp, count(*) as countp from bl_posts group by topic_id";

$result = $dbi->query($sql);
while ($row = mysqli_fetch_array($result)) {
 $postid = $row["topic_id"];
 $lastp  = $row["lastp"];
 $countp = $row["countp"];
 $sql2   = "update bl_topics set topic_replies=$countp, topic_last_post_id=$lastp where topic_id=$postid";
 $result2 = $dbi->query($sql2);
 print "$postid $lastp $countp<br>\n";
}


?>
