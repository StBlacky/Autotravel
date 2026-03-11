<?php

 # массив наименований
 $t = array();
 $z = array();
 $zz = array();
 $tgrps=getextypes2(1);
 foreach ($tgrps as $grp_ex) {
  $t["--".substr($grp_ex,0,1)]=$grp_ex;
 }
 $sql_xy = "SELECT id, type, `desc` from type_ex where id like '0%'";
 $result = $dbi->query($sql_xy);
 if($result) {
   while($myrow = mysqli_fetch_array($result)) {
    $t[$myrow["id"]] = "1.".$myrow["type"];
    $z["1.".$myrow["type"]] = $myrow["id"];
    if (!(is_array($zz["1.".$myrow["desc"]]))) {
      $zz["1.".$myrow["desc"]] = array($myrow["id"]);
    }
    else {
      array_push($zz["1.".$myrow["desc"]],$myrow["id"]);
    }
   }  
 }
 echo "<!-- $sql_xx \n\n";
 echo  "\n\n -->\n";

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
    $td[$tname][$extyp]["count"] = $myrow["c"];
    if (!($a == 0)) {
      $td[$tname]["hide"] = $myrow["thide"];
      $td[$tname]["url"] = $myrow["turl"];
      $td[$tname]["texid"] = $myrow["exid"];
    }
    if ($myrow["c"] == 1) {
      $td[$tname][$extyp]["exid"] = $myrow["exid"];
    }
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

 echo "<table id=\"cubedata\" width=\"100%\" cellspacing=0 class=\"ttbs tdc\">\n";
 echo "<thead><tr><th class=\"ttbs tl\">$tt</th>\n";
 if (sizeof($th) > 2) {
   $mm = 'data-breakpoints="xs sm"';
 }
 foreach($th as $the) {
    echo "<th $mm class=\"t14 tb ttbs ww tc\">".substr($the,2)."</th>\n";
 }
 echo "</tr></thead>\n";
 echo "<tbody class=\"t12 ttbs tc\">";
 
 foreach ($td as $arn => $tda) {
   $urlt = $tda["url"];
   # выводим заголовок
   if ($a == 0) {
    # это области
    echo "<tr><td class=\"ttbs tl\"><a target=_blank class=travell5n href='/cube.php?a=" . $ar[$arn] ."'>$arn</a></td>";
   }
   else {
    # это города
    if ($tda["hide"]) {
      echo "<tr><td class=\"ttbs tl\"><a target=_blank class=travell5r href='/otklik.php/" . $tda["texid"] ."'>$arn</a></td>";
      //echo "<tr><td class=\"ttbs tl\">$arn</td>";
    } 
    else {
      if ($urlt) {
        echo "<tr><td class=\"ttbs tl\"><a target=_blank class=travell5n href='/gorod/" . $urlt .".html'>$arn</a></td>";
      }
      else{
        echo "<tr><td class=\"ttbs tl\"><a target=_blank class=travell5n href='/town.php/" . $ar[$arn] ."'>$arn</a></td>";
      }
    }  
   }
   reset($th);
   foreach($th as $the) {
    $c = $tda[$the]["count"];
    if ($c == 1) {
      echo "<td class=\"ttbs\"><a class=travell5r target=_blank href='/otklik.php/".$tda[$the]["exid"]."'>$c</a></td>";
    }
    elseif ($c > 0) {
     if ($a==0) {
      echo "<td class=\"ttbs\"><a class=travell5n href='/cube.php?a=$ar[$arn]&r=$the'>$c</a></td>";
     }
     else {
      $dtype=substr($the,0,1);
      $direct = 1;
      if ($z[$the]) {
       $url="/excite.php/$ar[$arn]/$dtype?z=$z[$the]";
      }
      else {
       if ($dtype==1) {
         $ttz = $zz[$the];
         if (is_array($ttz)) {
           $url="/excite.php/$ar[$arn]/1?z=". implode(",", $ttz);
         }
         else {
           $url="/excite.php/$ar[$arn]/1?r=".$the;
         }
       }
       else {
        $url="/excite.php/$ar[$arn]/$dtype";
        $direct = 0;
       }
      }
      if ($urlt) {
        if ($direct) {  
          echo "<td class=\"ttbs\"><a class=travell5n target=_blank href='$url'>$c</a></td>";
        }
        else {
          if ($c>9) {  
            echo "<td class=\"ttbs\"><a class=travell5n target=_blank href='$url'>$c</a></td>";
          }
          else {
            echo "<td class=\"ttbs\"><span class=travell5n>$c</span></td>";
          }
        }
      }
      else {
        echo "<td class=\"ttbs\"><a class=travell5n target=_blank href='$url'>$c</a></td>";
      }  
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
