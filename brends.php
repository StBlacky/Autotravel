<?php
include_once('include/config.php');
include_once('include/func.php');
$bootstrap = 1;
include_once('include/head00.php');
$title = "Популярные туристические маршруты";
include('include/header_bs.php');
?>

<div class="container-fluid" style="width:100%;">
<h1>Известные туристические маршруты</h1>

<?php
$sqlb = "select b.logo as brend, b.id as bid, b.descrip as descrip from brends b order by 1";
$resultb = $dbi->query($sqlb);

if($myrowb = mysqli_fetch_array($resultb)) {
   do {
       $brend = $myrowb["brend"];
       $descrip = $myrowb["descrip"];
       $bid = $myrowb["bid"];
       $sqlt = "select t.name as town, t.id as tid from towns t, brends_t b where b.brend=$bid and t.id = b.town order by t.name";
       $result = $dbi->query($sqlt);
       $numrows = mysqli_num_rows($result);
       $counter = 0;
       $tbltxt1 = "";
       $tbltxt2 = "";
       if($myrowt = mysqli_fetch_array($result)) {
?>
<h2><?php echo $brend; ?></h2>
<p class="t12 tl travell0o"><?php echo $descrip; ?></p>

<?php

          do {
             $town = $myrowt["town"];
             $tid = $myrowt["tid"];
             if ($tlist) {
               $tlist = "$tlist,$tid";
             }
             else {
               $tlist = $tid;
             }
             if ($counter < $numrows) {
                $tbltxt1 = $tbltxt1 . "<a class=travell5s href=\"town.php/$tid\"> $town </a> <font class=travell0></font><BR>\n";
             } else {
                $tbltxt2 = $tbltxt2 . "<a class=travell5s href=\"town.php/$tid\"> $town </a> <font class=travell0></font><BR>\n";
             }
             $counter++;
             $counter++;
           } while($myrowt = mysqli_fetch_array($result));
          if ($tbltxt1) {
           echo "<div class=\"col-md-6 col-xs-12\">";
           echo "$tbltxt1\n";
           echo "</div>";
           echo "<div class=\"col-md-6 col-xs-12\">";
           echo "$tbltxt2\n";
           echo "</div>";
           echo "</div>";
           echo "<div class=\"container-fluid\">";
           echo "<h3>";
           echo "<a class=travell5m target=_blank href=\"/map.php?tlist=$tlist&resheader=".
                 mycodespace($brend) ."\">\n";
           echo "Показать на карте Google</a>\n";
           echo "&nbsp;&nbsp;&nbsp;&nbsp;<a class=travell5m href=\"/triptrackm.php?ex=5\">Возможные варианты маршрутов</a></h3>";
           
        }

       }

  } while($myrowb = mysqli_fetch_array($resultb));
}


?>



</div>

<?php

include('include/footer_bs.php');

?>
