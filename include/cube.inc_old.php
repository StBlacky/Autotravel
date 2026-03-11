<?php

 # массив наименований
 $t = array();
 $z = array();
 $tgrps=getextypes2(1);
 foreach ($tgrps as $grp_ex) {
  $t["--".substr($grp_ex,0,1)]=$grp_ex;
 }
 $sql_xy = "SELECT id, type from type_ex where id like '0%'";
 $result = $dbi->query($sql_xy);
 if($myrow = mysqli_fetch_array($result)) {
   do {
    $t[$myrow["id"]] = "1.".$myrow["type"];
    $z["1.".$myrow["type"]] = $myrow["id"];
   }  while($myrow = mysqli_fetch_array($result));
 }
 echo "<!-- $sql_xx -->\n";

 $result = $dbi->query($sql_xx);
 $td=array();
 $ar=array();
 $th=array();
 if($myrow = mysqli_fetch_array($result)) {
   do {
    $tname = cyrname($myrow["name"]);
    $extyp = $myrow["extyp"];
    $ar[$tname] = $myrow["id"];
    # надо сделать текстовый тип из числового и набрать заголовки
    if (substr($extyp,1,1)!=".") {
      $extyp = $t[$extyp];
    }
    if (!(in_array($extyp, $th))) {
      $th[] = $extyp;
    }
    $td[$tname][$extyp] = $myrow["c"];
   } while($myrow = mysqli_fetch_array($result));
 }

 sort($th);
 if ($a==0) {
  $tt = "Область";
  
 }
 else {
  $tt = "Город";
  echo "<h2 itemprop=\"description\">Достопримечательности $areaname_r</h2>";
 }

 echo "<p class=travell0><b>$areaname</b> - объекты сгрупированы по типам <b>достопримечательностей</b> и сервисов. 
   По ссылкам можно перейти как на страницу города, где будут указаны все достопримечательности и сервисы, так и на страницу с достопримечательностями конкретного типа.</p>\n";

 echo "<table class=travell0 border=1>";
 $ijk=0;

 foreach ($td as $arn => $tda) {
   # выводим заголовки через 15 строк
   if ($ijk == 0) {
    echo "<tr><th>$tt\n";
    foreach($th as $the) {
     echo "<th>".substr($the,2)."\n";
    }
    echo "</tr>\n";
   }
   $ijk = $ijk + 1;
   if ($ijk == 15) {
    $ijk = 0;
   }

   if ($a == 0) {
    # это области
    echo "<tr><td><a target=_blank class=travell5m href='/cube.php/?a=" . $ar[$arn] ."'>$arn</a></td>";
   }
   else {
    # это города
    echo "<tr><td><a target=_blank class=travell5m href='/town.php/" . $ar[$arn] ."'>$arn</a></td>";
   }
   reset($th);
   foreach($th as $the) {
    $c = $tda[$the];
    if ($c > 0) {
     if ($a==0) {
      echo "<td class=\"t16 tc tb\"><a class=travell5m href='/cube.php?a=$ar[$arn]&r=$the'>$c</a></td>";
     }
     else {
      $dtype=substr($the,0,1);
      if ($z[$the]) {
       $url="/excite.php/$ar[$arn]/$dtype?z=$z[$the]";
      }
      else {
       if ($dtype==1) {
        $url="/town.php/$ar[$arn]?type=".substr($the,2);
       }
       else {
        $url="/excite.php/$ar[$arn]/$dtype";
       }
      }
      echo "<td class=\"t16 tc tb\"><a class=travell5m target=_blank href='$url'>$c</a></td>";
     }
    }
    else {
     echo "<td>&nbsp;</td>";
    }
   }
   echo "</tr>\n";
 }
 echo "</table>\n";

?>
