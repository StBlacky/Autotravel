<?php

include_once('include/config.php');
include_once('include/func.php');
include('include/head01.php');

$sqlx="select file from foto f where hash=''";
$result = $dbi->query($sqlx); 

while ($myrow = mysqli_fetch_array($result)) {
    $file = $myrow["file"];
    $fn = '/home/blacky/data/www/phalbum/'.$file.'.jpg';
        try {
          $ihash = hash_file('md5', $fn);
          $isize = getimagesize($fn);
          $sql = "update foto set hash='".$ihash."', wi=".$isize[0].', hi='.$isize[1]." where file='".$file."'";
          print($sql."<br>\n");
          $dbi->query($sql);
        }
        catch (Exception $ex) {
          error_log($ex->getMessage());  
        }
}
print "done";