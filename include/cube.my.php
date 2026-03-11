<?php
 $vvv = "";
 # массив наименований
 $t = array();
 $z = array();
 $zz = array();
 $tgrps=getextypes2i(1);
 foreach ($tgrps as $grp_ex) {
  $t["--".substr($grp_ex,0,1)]=$grp_ex;
 }
 $sql_xy = "SELECT id, type, `desc`, icon from type_ex where id like '0%'";
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
 $vvv .= "<!-- $sql_xx \n\n";
 $vvv .= "\n\n -->\n";

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
 }

 $vvv .= "<table id=\"cubedata\" width=\"100%\" cellspacing=0 class=\"ttbs tdc table table-hover table-condensed\">\n";
 $vvv .= "<thead><tr><th class=\"ttbs tl\">$tt</th>\n";
 if (sizeof($th) > 2) {
   $mm = 'data-breakpoints="xs sm"';
 }
 foreach($th as $the) {
     $the = substr($the,2);
     switch ($the) {
		case 'Архитектура':
 			$the = "<img src=\"/jpg/01_arch.png\">";
		break;
		default:
                break;
	}
 
     $vvv .= "<th $mm class=\"t14 tb ttbs ww tc\">".$the."</th>\n";
 }
 $vvv .= "</tr></thead>\n";
 $vvv .= "<tbody class=\"t12 ttbs tc\">";
 
 foreach ($td as $arn => $tda) {
   $urlt = $tda["url"];
   # выводим заголовок
   if ($a == 0) {
    # это области
    $vvv .= "<tr><td class=\"ttbs tl\"><a target=_blank class=travell5n href='/cube.php?a=" . $ar[$arn] ."'>$arn</a></td>";
   }
   else {
    # это города
    if ($tda["hide"]) {
      $vvv .= "<tr><td class=\"ttbs tl\"><a target=_blank class=travell5r href='/otklik.php/" . $tda["texid"] ."'>$arn</a></td>";
      //echo "<tr><td class=\"ttbs tl\">$arn</td>";
    } 
    else {
      if ($urlt) {
        $local_url = '/gorod/'.$urlt.'.html';  
      }  
      else {
        $local_url = '/town.php/' .$ar[$arn];  
      }
          
      $vvv .= "<tr><td class=\"ttbs tl\"><a target=_blank class=travell5n href='" . $local_url ."'>$arn</a></td>";
    }  
   }
   reset($th);
   foreach($th as $the) {
    $c = $tda[$the]["count"];
    if ($c == 1) {
      $url = '/otklik.php/'.$tda[$the]["exid"];  
      $vvv .= "<td class=\"ttbs\"><button type=\"button\" class=\"cubebutton travell5n\" Onclick='Bclick(\"$url\");'>$c</button></td>";
    }
    elseif ($c > 0) {
     if ($a==0) {
      $url='/cube.php?a='.$ar[$arn].'&r='.$the;   
      $vvv .= "<td class=\"ttbs\"><button type=\"button\" class=\"cubebutton travell5n\" Onclick='Bclick(\"$url\");'>$c</button></td>";
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
          $vvv .= "<td class=\"ttbs\"><button type=\"button\" class=\"cubebutton travell5n\" Onclick='Bclick(\"$url\");'>$c</button></td>";
        }
        else {
          if ($c>9) {  
            $vvv .= "<td class=\"ttbs\"><button type=\"button\" class=\"cubebutton travell5n\" Onclick='Bclick(\"$url\");'>$c</button></td>";
          }
          else {
            $vvv .= "<td class=\"ttbs\"><span class=travell5n>$c</span></td>";
          }
        }
      }
      else {
        $vvv .= "<td class=\"ttbs\"><button type=\"button\" class=\"cubebutton travell5n\" Onclick='Bclick(\"$url\");'>$c</button></td>";
      }  
     }
    }
    else {
      $vvv .= "<td class=\"ttbs\">&nbsp;</td>";
    }
   }
   $vvv .= "</tr>\n";
 }
 $vvv .= "</tbody></table>\n";
 $customscripttop .= '
    function Bclick(url) {
      var win=window.open(url,\'_blank\');
      win.focus;
      return false;
    }';

?>
