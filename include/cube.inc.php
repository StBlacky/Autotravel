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
 if($result) {
   while($myrow = mysqli_fetch_array($result)) {
    $t[$myrow["id"]] = "1.".$myrow["type"];
    $z["1.".$myrow["type"]] = $myrow["id"];
   }  
 }
 echo "<!-- $sql_xx -->\n";

 $result = $dbi->query($sql_xx);
 $td=array();
 $ar=array();
 $th=array();
 if($result) {
   while($myrow = mysqli_fetch_array($result)) {
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
   } 
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

 echo "<table cellspacing=0 class=\"ttbs tdc table table-hover\">";
 $ijk=0;

 foreach ($td as $arn => $tda) {
   # выводим заголовки через 15 строк
   if ($ijk == 0) {
    echo "<thead><tr><th class=\"ttbs tl\">$tt\n";
    foreach($th as $the) {
     echo "<th class=\"t12 tb ttbs ww tc\">".substr($the,2)."\n";
    }
    echo "</tr></thead><tbody class=\"t12 ttbs tc \">\n";
   }
   $ijk = $ijk + 1;
   if ($ijk == 25) {
    $ijk = 0;
   }

   if ($a == 0) {
    # это области
    echo "<tr><td class=\"ttbs tl\"><a target=_blank class=travell5n href='/cube.php/?a=" . $ar[$arn] ."'>$arn</a></td>";
   }
   else {
    # это города
    echo "<tr><td class=\"ttbs tl\"><a target=_blank class=travell5n href='/town.php/" . $ar[$arn] ."'>$arn</a></td>";
   }
   reset($th);
   foreach($th as $the) {
    $c = $tda[$the];
    if ($c > 0) {
     if ($a==0) {
      echo "<td class=\"ttbs tl\"><a class=travell5n href='/cube.php?a=$ar[$arn]&r=$the'>$c</a></td>";
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
      echo "<td class=\"ttbs\"><a class=travell5n target=_blank href='$url'>$c</a></td>";
     }
    }
    else {
     echo "<td class=\"ttbs\">&nbsp;</td>";
    }
   }
   echo "</tr>\n";
 }
 echo "</tbody></table>\n";

?>
