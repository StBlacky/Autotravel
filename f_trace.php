<?php
// всплывающее окно по поиску трассы
// 

include_once('include/config.php');
include_once("include/func.php");
include("include/head01.php");

$submitx = $_REQUEST["submitx"];

$tname = $_REQUEST["find"];  
$tname = mysafestring($tname);
$form = mysafestring($_REQUEST["form"]);
$field = mysafestring($_REQUEST["field"]);
$id = (int)mysafestring($_REQUEST["id"]);

if (!$submitx) {
   $listtrace="";
   if ($tname != "") {
      // передана часть кода. Надо поискать трассы и построить список.
      $sql2 = "select name, id, of_name from trace where of_name like '".$tname."%' order by of_name asc";
      $result2 = $dbi->query($sql2);
      if ($result2) {
         while ($myrow2 = mysqli_fetch_array($result2)) {
            $id = $myrow2["id"];
            $name = $myrow2["name"];
            $of_name = $myrow2["of_name"];
            $listtrace=$listtrace . "<a href=/trace.php/$id>$of_name - $name</a><br>";   
         } 
      } else {
         $error = "!";
      }
      mysqli_free_result($result2);
   }
   
   if (!$error) {       
      echo "<div class=travel0 align=center>\n";
      echo "Уже есть трассы с похожим кодом: <br>$listtrace";
      echo "</div>\n";
   }
   
}
?>