<?php

//
// Предназначена для поиска по базе.
// Кнопка "показать" из интерфейса ввода убрана. Просто если в строке
// поиска передано пусто -- работает как "показать".
// В качестве массивов принимает: массив выбранных областей area
// массив выбранных типов объектов extype
// массив выбранных типов примечательностей typex
// минимальный рейтинг mrate
// Если взведены флаги "все области" allareas или "все примечательности"
// alltypex, то массивы обнуляются
//

include_once('include/config.php');
include_once('include/func.php');
include_once('include/func_bs.php');

$search = trim(mysafestring($_REQUEST["search"]));

$mark = $_REQUEST["mark"];
$mark = (int) mysafestring($mark);

$psubmit = $_REQUEST["psubmit"];

$ksubmit = $_REQUEST["ksubmit"];

$mrate = $_REQUEST["mrate"];
$mrate = mysafestring($mrate);
if (!($mrate)) {
 $mrate = 0;
}
$mrate = $mrate * 100;
$mrate = $mrate / 100;

$tid   = $_REQUEST["tid"];
$tid   = (int) mysafestring($tid);
$thistid = $_REQUEST["thistid"];
$thistid = mysafestring($thistid);
$adiff = $_REQUEST["adiff"];
$adiff = (int) mysafestring($adiff);
//
// хотя бы 1 регион должен был быть выбран. Если не выбрано ни одного, значит ищем по всем(!)
//
$area=array();
$newadds = $_REQUEST["area"];
if (!($newadds)) {
 $allareas='Y';
}
else {
 $allareas = "";   
}

if (is_array($newadds)) {
 reset ($newadds);
 while (list ($key, $val) = each ($newadds)) {
   $area[] = mysafestring($val);
 }
}
else {
 $area[] = mysafestring($newadds);
}

//
// типы объектов. примечательности 1 и сервис 6 смотрим по выбранному списку
// но из прямых линков могут прилетать прямые запросы.
//
$extype=array();
$newadds = $_REQUEST["extype"];
if (is_array($newadds)) {
  reset ($newadds);
  while (list ($key, $val) = each ($newadds)) {
    $val = (int) mysafestring($val);
    if ($val) {
      $extype[] = $val;  
    }
  }
}
else {
  $val = (int) mysafestring($newadds);
  if ($val) {
    $extype[] = $val;  
  }
}

//
//  по рейтингу
//
if ($mrate > 0) {
 $mrateqry = " and e.wrate >= $mrate ";
}
else {
 $mrateqry = "";
}

// заголовок

$jqueryon = 1;
$colorbox = 0;
$bootstrap = 1;
$_totop = 1;

include("include/header_bs.php");
$_user_is_admin=0;
echo "<div class=\"container-fluid\" style=\"width:100%;\">\n"; 


//
//  типы достопримечательностей
//
$typex=array();
$newadds = $_REQUEST["typex"];
if (is_array($newadds)) {
 reset ($newadds);
 while (list ($key, $val) = each ($newadds)) {
    $val = mysafestring($val);
    if (!($val === "")) {
      $val = (int) $val;  
      $typex[]=str_pad($val, 3, '0', STR_PAD_LEFT);
    }
  }
}
else {
 $val = mysafestring($newadds);
 if (!($val === "")) {
   $val = (int) $val;  
   $typex[]=str_pad($val, 3, '0', STR_PAD_LEFT);
 }
}
// текст для формы выбора типов примечательностей. Если передали extype=1 то все!
$alltypex=in_array(1, $extype);
$typex_txt=gettypexopt('0%',$typex,$alltypex);

if (!(empty($typex))) {
  $extype[] = 1;  // для sql поиска
}

// типы сервиса. копия типов примечательностей

$typex6=array();
$newadds = $_REQUEST["typex6"];
if (is_array($newadds)) {
 reset ($newadds);
 while (list ($key, $val) = each ($newadds)) {
    $val = mysafestring($val);
    if (!($val === "")) {
      $val = (int) $val;  
      $typex6[]=str_pad($val, 3, '0', STR_PAD_LEFT);
    }
  }
}
else {
 $val = mysafestring($newadds);
 if (!($val === "")) {
   $val = (int) $val;  
   $typex6[]=$val;   
 }
}

// текст для формы выбора типов примечательностей. Если передали extype=6 то все!
$alltypex6=in_array(6, $extype);
$typex6_txt=gettypexopt('6%',$typex6,$alltypex6);

if (!(empty($typex6))) {
  $extype[] = 6;  // для sql поиска
}

//
//  поиск в окрестностях города
//
$tidname = "";
$diffqry = "";
if ($tid > 0) {
 if ($adiff < 20) {
  $adiff = 20;
 }
 if ($adiff > 700) {
  $adiff = 700;
 }

 $rdiff  = ($adiff / 111);
 $rdiff2 = $rdiff * $rdiff;
 $sql    = "select id, Nshirota, Edolgota from towns where id='$tid'";

 $result = $dbi->query($sql);
 $tid      = 0;
 $Nshirota = 0;
 $Edolgota = 0;
 if($myrow = mysqli_fetch_array($result)) {
  $Nshirota = (float)$myrow["Nshirota"];
  $Edolgota = (float)$myrow["Edolgota"];
  $tid      = $myrow["id"];
 }

 if ($tid > 0) {
  $tidname = gettownname($tid);
 }

 mysqli_free_result($result);
 $tlist = "";
 $sqldeg = "";
 // у города координаты есть. но для 9999 это не так
 if ($tid == 9999) {
  $tlist = $tid;  
 }
 //
 if ((($Nshirota > 0) or ($Nshirota < 0)) and (($Edolgota > 0) or ($Edolgota < 0))) {
   $ctt = cos(deg2rad($Nshirota));
   $ctt = $ctt*$ctt;
   if ($ctt < 0.1) {
     $ctt = 0.1;
   }

   if ($thistid) {
    $sqladd = "";
    $sqldeg = " (
      ((e.Nshirota - $Nshirota)*(e.Nshirota - $Nshirota) +
      ((e.Edolgota - $Edolgota)*(e.Edolgota - $Edolgota)*$ctt)) < $rdiff2
     ) ";
   }
   else {
    $sqladd = " and id <> $tid ";
    $sqldeg = " ((
      ((e.Nshirota - $Nshirota)*(e.Nshirota - $Nshirota) +
      ((e.Edolgota - $Edolgota)*(e.Edolgota - $Edolgota)*$ctt)) < $rdiff2
     ) ) and t. id <> $tid ";
   }
   $sql = "select id from towns
            where (
            ((Nshirota - $Nshirota)*(Nshirota - $Nshirota) +
            ((Edolgota - $Edolgota)*(Edolgota - $Edolgota)*$ctt)) < $rdiff2
            ) $sqladd and hide <> 1";
   $result = $dbi->query($sql);
   while ($myrow = mysqli_fetch_array($result)) {
    $ttid = $myrow["id"];
    if ($tlist) {
     $tlist = $tlist . "," . $ttid;
    }
    else {
     $tlist = $ttid;
    }
   }
   mysqli_free_result($result);
 }
 if ($tlist) {
  if ($sqldeg) {
   $diffqry = " and ((t.id in ($tlist)) or $sqldeg)";
  }
  else {
   $diffqry = " and t.id in ($tlist) ";
  }
 }
 else {
  if ($tid > 0) {
// городов или координат не нашлось.
   if ($sqldeg) {
    $diffqry = " and ((t.id = 0) or $sqldeg) ";
   }
   else {
    $diffqry = " and t.id = 0 ";
   }
  }
  else {
   $diffqry = $sqldeg;
  }
 }
}

$flagresult="";

$typexs=join($typex,',');
$typexs6=join($typex6,',');
$extypes=join($extype,',');

if ($psubmit) {
 $submit="Показать";
 $search="";
}
else {
 if ($search) {
  $submit="Найти";
  if ($mark == 2) {
    $fulltextqry = " and match (e.name, e.descrip) against ('$search' IN BOOLEAN MODE ) ";
  }
  else {
    $fulltextqry = " and match (e.name) against ('$search' IN BOOLEAN MODE ) ";
  }
 }
 else {
  $fulltextqry = "";   
  if ($ksubmit) {
   $submit="Показать";
  }
  else {
   $submit="";
  }
 }
}


//
// Заполнение формы выбора областей и стран
//
$areas=join($area,',');
if ($allareas) {
 $areas='';
 $area=array();
}
$area_txt="";
$area_txt2="";
$areaid=0;
$sql="select id,name from areas order by name asc";
$result = $dbi->query($sql);
$areacount = mysqli_num_rows($result);
if($myrow = mysqli_fetch_array($result)) {
 do {
   $id = $myrow["id"];
   $name = $myrow["name"];
   if ($id>0) { // области
    if ((in_array($id,$area))||($allareas)) {
     $area_txt .= "<option value=$id SELECTED>$name</option>\n";
    }
    else {
     $area_txt .= "<option value=$id>$name</option>\n";
    }
   }
   elseif ($id<0) { // заграница
    if (!($_zagr_mode)) {
      if ((in_array($id,$area))||($allareas)) {
        $area_txt2 .= "<option value=$id SELECTED>$name</option>\n";
      }
      else {
        $area_txt2 .= "<option value=$id>$name</option>\n";
      }
     }
   }
 } while($myrow = mysqli_fetch_array($result));
}
mysqli_free_result($result);

// 
// Заполнение формы выбора типов объектов
//
$sql="select count(*) as count from type_ex where id like '0%'";
$result = $dbi->query($sql);
if($myrow = mysqli_fetch_array($result)) {
  $typexcount = (int) $myrow["count"];
}
$extype_txt="";
$sql="select id,name from type order by srtfld asc";
$result = $dbi->query($sql);
if($myrow = mysqli_fetch_array($result)) {
  do {
    $id = $myrow["id"];
    $name = $myrow["name"];
    if (($id != 1) && ($id != 6)) {
      // перешли на мультиселект
      if (in_array($id,$extype)) {
        $extype_txt = $extype_txt."<option value=$id SELECTED>$name</option>\n";
      }
      else {
        $extype_txt = $extype_txt."<option value=$id>$name</option>\n";
      }
    }
  } while($myrow = mysqli_fetch_array($result));
}
mysqli_free_result($result);

if (!($restext)) {
 if ((($submit == 'Найти') and ($search))) {
  if ($tidname) {
   $restext="Результат поиска \"$search\" по окрестностям $tidname";
  }
  else {
   $restext="Результат поиска \"$search\" по объектам базы";
  }
  echo "<h1>$restext</h1>\n";
 }
 else {
  if ($submit == 'Показать') {
   if ($tidname) {
    $restext="Результат выбора объектов в окрестностях $tidname";
   }
   else {
    $restext="Результат выбора объектов базы";
   }
   echo "<h1>$restext</h1>\n";
   echo "<p class=travell0>Если ниже вы не нашли места, о котором вам бы хотелось здесь прочитать, значит мы о нем еще не знаем. Напишите о нем на нашем";
   echo "<a target= _blank href=\"$forumname/viewforum.php?f=5\"> форуме</a>.";
   echo "<br>";
   if ($_salp_mode) {
    echo "Сортировка - по алфавиту";
   }
   else {
    echo "Сортировка - по рейтингу";
   }
   echo "</p>";
  }
  else {
?>

<h1>Поиск по объектам базы</h1>
<p class=travell0>
Интерфейс предназначен для поиска объектов, занесённых в базу данных.
Возможен выбор объектов по типам и по регионам (областям).
Также возможен полнотекстовый поиск по описанию и наименованию объекта,
поиск учитывает выбранный для объектов фильтр.
</p>
<p class=travell0>
Для выбора объектов по городам предназначен интерфейс
<a class="travell5m" href=/towns.php>"города"</a>.
</p>
<p class=travell0>
Для выбора объектов по трассам предназначен интерфейс
<a class="travell5m" href=/trace.php>"трассы"</a>.
</p>

<?php
  }
 }
}
else {
 echo "<h1>$restext</h1>\n";
}

if ((($submit == 'Найти') and ($search)) or ($submit == 'Показать')) {
 if ($_salp_mode) {
   $sqladds = "townname, loc";
 }
 else {
   $sqladds = "wrate desc, townname, loc";
 }
 
 if (($areas) && (!($tid))) { // области ТОЛЬКО если не задан город
  if ($extype) {
   $sql = "SELECT e.id as id, e.ffile as featured, e.rate as rate, e.wrate as wrate, e.booking as booking, e.town as town, "
           . "e.Nshirota as nsh, e.EDolgota as edg, e.name as name,e.descrip as descrip,e.type as type, "
           . "e.kind as kind,t.name as townname, t.hide as thide, Right( e.name, length( e.name ) - locate( '\"', e.name )) AS loc, "
           . "a.name as aname from exciting e, towns t, areas a where t.id=e.town "
           . "and t.area in ($areas) and e.type in ($extypes) and e.mainid>=0 and a.id = t.area "
           . " $fulltextqry $mrateqry $diffqry order by $sqladds";
  }
  else {
   $sql = "SELECT e.id as id, e.ffile as featured, e.rate as rate, e.wrate as wrate, e.booking as booking, e.town as town, "
           . "e.Nshirota as nsh, e.EDolgota as edg, e.name as name,e.descrip as descrip,e.type as type, "
           . "e.kind as kind, t.name as townname, t.hide as thide, Right( e.name, length( e.name ) - locate( '\"', e.name )) AS loc, "
           . "a.name as aname from exciting e, towns t, areas a where t.id=e.town "
           . "and t.area in ($areas) and a.id = t.area and e.mainid>=0 $fulltextqry  $mrateqry $diffqry order by $sqladds";
  }
 }
 else {
  if ($extype) {
   $sql= "SELECT e.id as id, e.ffile as featured, e.rate as rate, e.wrate as wrate, e.booking as booking, e.town as town, "
           . "e.Nshirota as nsh, e.EDolgota as edg, e.name as name,e.descrip as descrip,e.type as type,"
           . "e.kind as kind, t.name as townname, t.hide as thide, Right( e.name, length( e.name ) - locate( '\"', e.name )) AS loc, "
           . "a.name as aname FROM exciting e, towns t, areas a where e.type in ($extypes) and e.mainid>=0 "
           . "and t.id=e.town and a.id = t.area $fulltextqry  $mrateqry $diffqry order by $sqladds";
  }
  else {
   $sql= "SELECT e.id as id, e.ffile as featured, e.rate as rate, e.wrate as wrate, e.booking as booking, e.town as town, "
           . "e.Nshirota as nsh, e.EDolgota as edg, e.name as name,e.descrip as descrip,e.type as type,"
           . "e.kind as kind, t.name as townname, t.hide as thide, Right( e.name, length( e.name ) - locate( '\"', e.name )) AS loc, "
           . "a.name as aname FROM exciting e, towns t, areas a where t.id=e.town "
           . "and a.id = t.area and e.mainid>=0 $fulltextqry  $mrateqry $diffqry order by $sqladds";
#   $sql= "SELECT id,town,name,descrip,type,kind FROM exciting order by name";
  }
 }
 echo "\n<!-- $sql -->\n";

 $result = $dbi->query($sql);
   if($myrow = mysqli_fetch_array($result)) {
     $headt="<table class=\"ttb tdc\" style=\"width:100%\"><Tbody class=\"travell0 ttb tl\">";
     $headt=$headt."<tr><TD class=\"ttb tb tc\" align=center style=\"min-width:175px; width: 15vw\">Наименование";
     $headt=$headt."</TD><TD class=\"ttb tb tc\" align=center>Описание</td></tr>";
     $flag=0; // ничего нет ?
     do {
    	$id = $myrow["id"];
    	$town = $myrow["town"];
    	$featured = $myrow["featured"];
    	$area = $myrow["aname"];
    	$rate = $myrow["rate"];
    	$booking = $myrow["booking"];
        $wrate   = (float)$myrow["wrate"];
        $wrate   = sprintf("%01.2f",$wrate);
        $townname = $myrow["townname"];
    	$thide = $myrow["thide"];
    	$type = $myrow["type"];
    	$kind = $myrow["kind"];
    	$nshe = (float)$myrow["nsh"];
    	$edge = (float)$myrow["edg"];
        if (($nshe > 0) and ($edge > 0)) {
           $_nomap = 1;
           $coords = "N" . degreestotext($nshe) . ", E" . degreestotext($edge);
        }
        else {
           $coords = "";
        }
        $name = $myrow["name"];
    	$desc = $myrow["descrip"];
        // фильтр по типу примечательности или сервиса
        $flag_loc=1;
	if (($type == 1) && ($alltypex != 1)) {
	  if (!($kind)) {
	    $flag_loc=0;
	  }
	  else {
	    $typex_loc=explode(',',$kind);
	    if (!(array_intersect($typex,$typex_loc))) {
 	       $flag_loc=0;
            }
          }
        }
	if (($type == 6) && ($alltypex6 != 1)) {
	  if (!($kind)) {
	    $flag_loc=0;
	  }
	  else {
	    $typex_loc=explode(',',$kind);
	    if (!(array_intersect($typex6,$typex_loc))) {
 	        $flag_loc=0;
            }
         }
       }  
       echo "\n<!-- $id $name $kind $flag_loc -->";  
       //
       if ($flag_loc) {
        if (1==1) { //  ($submit == 'Показать') {
          if ($flag==0) {
		echo $headt;
		$flag=1;
          }
	  if ($flagresult) {
	    $flagresult=$flagresult . "," . $id;
	  }
	  else {
	    $flagresult=$id;
	  }
	  $s_rek = getreklama($id,0);
	  echo"<tr><TD class=\"ttb tl\" align=center width=\"175\">\n";
	  if($s_rek<>"") {
	    echo "<img src=\"jpg/partner.jpg\" width=\"10\" height=\"10\" alt=\"Партнер сайта\"> ";
	  }
	  echo "<a class=\"travell5n\" href=\"/otklik.php/$id\">$name</a>";
          echo " ($townname, $area)\n";
	  if ($coords) {
            echo "<br>$coords\n";
          }
          echo "<br>Рейтинг: $wrate\n";
	  if ($type == 2) {
            if (!(empty($booking))) {
              echo "<br>[доступно бронирование]";
            }
          }
  	  if (($forum_user_id>1)) {
            echo "<div id=basketadd$id ><a onclick='BasketAdd($id,$forum_user_basket,\"basketadd$id\")'>Добавить в $forum_user_basket_name</a></div>\n";
          }
    	  echo"</TD><TD class=\"ttb tl\" align=left>\n";
	  if ($s_rek <> "") {
	    echo "<table border=1 cellpadding=8 width=97%><tr><td class=travell0><b>Официальная информация: </b><br>$s_rek</td></tr></table>";
	  }
          if (!($_text_mode)) {
            if ($featured) {
              $photoimg=get1fotoex($id,1,$featured);
            }  
            else {
              $photoimg=get1fotoex($id,1,0);
            }
            if ($photoimg) {
              echo $photoimg; 
            }
          }  
          echo $desc;      
          echo "<br><a onclick=\"ChTjs('".$id."',this);\">Прочитать последние несколько откликов: <img border=0 alt=\">\" src=\"/jpg/plus.gif\"></a><div id=\"o-".$id."\"></div>\n";
          echo "</td></tr>\n";
        }
      } // конец flag_lock

    } while($myrow = mysqli_fetch_array($result));
    if (!($flag==0)) {
      echo "</tbody></table>\n";
    }
  } // конец mysqli_result
  else {
     $message="Ошибка. Сообщите, пожалуйста, на форуме.";
  }
  mysqli_free_result($result);
  if ($flag==0) {
     $message="Ничего не найдено";
     $flagresult=0;
  }
  if ($message) {
    $flagresult=0;
  }

} // конец обработки сабмита

if ($message) {
  echo "<h4>Увы. $message. Попробуйте ещё раз</h4>\n";
# echo "$sql\n";
}

 if (!($flagresult)) {
  echo "<div id=test01form class=test01form>\n";
 }
 else{
  echo "<p class=travell5><a class=travell5n onclick=\"ChToggle('test00form',this);\"><b>Форма поиска</b> <img border=0 alt=\">\" src=\"/jpg/plus.gif\"></a></p>";
  echo "<div id=test00form style=\"visibility: hidden; display: none;\">\n";
 }


?>
<Form class="form-horizontal" name=sform action="/test.php">
<div class="panel">
<div class="panel-heading">
<h3>Фильтр по объектам, внесённым в базу данных:</h3>
</div>
<div class="panel-body">    
<div class="form-group">
  <label class="control-label col-sm-2 col-xs-12" for="selectat">
  <b>Области и регионы: </b>
  </label>
  <div class="col-sm-10 col-xs-12">
  <?php
    echo "<select name=\"area[]\" class=\"form-control\" id=\"selectat\" multiple=\"multiple\">$area_txt</select>";
    if (!($_zagr_mode)) {
  ?>
  </div>
</div>    
<div class="form-group">
  <label class="control-label col-sm-2 col-xs-12" for="selectat2">
  <b>Зарубежные страны: </b>
  </label>
  <div class="col-sm-10 col-xs-12">
  <?php
    echo "<select name=\"area[]\" class=\"form-control\" id=\"selectat2\" multiple=\"multiple\">$area_txt2</select>";
  }
  ?>
  </div>
</div>
<div class="form-group">
  <label class="control-label col-sm-2 col-xs-12" for="selecttx">
  <b>Виды достопримечательностей: </b>
  </label>
  <div class="col-sm-10 col-xs-12">
  <?php
    echo "<select name=\"typex[]\" class=\"form-control\" id=\"selecttx\" multiple=\"multiple\">$typex_txt</select>";
  ?>
  </div>    
</div>
<div class="form-group">
  <label class="control-label col-sm-2 col-xs-12" for="selecttx2">
  <b>Виды сервиса: </b>
  </label>
  <div class="col-sm-10 col-xs-12">
  <?php
    echo "<select name=\"typex6[]\" class=\"form-control\" id=\"selecttx2\" multiple=\"multiple\">$typex6_txt</select>";
  ?>
  </div>    
</div>
<div class="form-group">
  <label class="control-label col-sm-2 col-xs-12" for="selectex">
  <b>Прочие объекты:</b>
  </label>
  <div class="col-sm-10 col-xs-12">
  <?php
    echo "<select name=\"extype[]\" class=\"form-control\" id=\"selectex\" multiple=\"multiple\">$extype_txt</select>";
  ?>
  </div>    
</div>
<div class="form-group">
  <label class="control-label col-sm-2 col-xs-12" for="mrate">
  <b>Рейтинг:</b><br>
  </label>
  <div class="col-sm-10 col-xs-12">
  <select class="form-control" style="width:auto;" size=1 name="mrate" id="mrate">
   <option value="0" <?php if($mrate < "0.01") { echo "SELECTED"; } ?> >Любой
   <option value="0.01" <?php if (($mrate > "0") and ($mrate < $highrate)) { echo "SELECTED"; } ?> >Положительный
   <option value=<?php if($mrate >= $highrate) { echo "\"$highrate\" SELECTED"; } else { echo "\"$highrate\""; } ?> >Высокий
  </select>
  </div>
</div>
</div>
</div>
    
<div class="panel">
<div class="panel-heading">
<h3 align=left>Дополнительный фильтр по окрестности:</h3>
</div>
<div class="panel-body">    
<div class="col-sm-12 col-xs-12">
  <div class="col-sm-2 col-xs-12" style="text-align: right;">
  <a name="tselect"></a>
  [<a href=#tselect onclick=NewSelId('sform','tid','/s_town.php')>Найти&nbsp;город</a>]<br>
  [<a href=#tselect onclick=NewClrId('sform','tid')>Убрать&nbsp;город</a>]
  </div>
  <div class="col-sm-10 col-xs-12">
   <b>Находится на расстоянии не более</b> <input class="form-control" style="display: inline; width: auto;" type=text size=4 name="adiff" value=<?php echo $adiff; ?>> <b>км</b>
   <br>
   <input type=hidden name="tid" value=<?php if ($tid > 0) {echo $tid;} else {echo "0";} ?> >
   <b>От города </b>
   <input type=text name="helptid" size=80 style='width:20vw; min-width:260px; font-size:100%' class=helpline value='<?php if ($tid > 0) {echo $tidname;} ?>'>
   <br><input type=checkbox name=thistid value=Y <?php if ($thistid) {echo "CHECKED"; } ?>><b>Включая этот город</b>
  </div>
</div>
</div>
</div>
    
<div class="panel">
<div class="panel-heading">
<h3 align=left>Полнотекстовый регистронезависимый поиск (с учётом фильтра по объектам):</h3>
</div>
<div class="panel-body">    
<div class="form-group">
  <label class="control-label col-sm-2 col-xs-12" for="isearch">
  <b>Что искать:</b>
  </label>
  <div class="col-sm-10 col-xs-12">
  <INPUT name="search" class="form-control" id="isearch" type=text size=40 value="<?php echo $search; ?>"></TD>
  </div>
</div>    
<div class="form-group">
  <label class="control-label col-sm-2 col-xs-12" for="imark">
  <b>Где искать:</b>
  </label>
  <div class="col-sm-10 col-xs-12">
  <?php
  if ($mark == 2) {
    echo "<input TYPE=radio VALUE=1 NAME=mark>В наименовании<br>\n";
    echo "<input id=imark TYPE=radio VALUE=2 NAME=mark CHECKED>В наименовании и описании<br>\n";
  }
  else {
   echo "<input id=imark TYPE=radio VALUE=1 NAME=mark CHECKED>В наименовании<br>\n";
   echo "<input TYPE=radio VALUE=2 NAME=mark>В наименовании и описании<br>\n";
  }
  ?>
  </div>
</div>
</div>
</div>    
<div class="form-group">
  <div class="col-sm-offset-2 col-sm-10">
    <INPUT class="btn btn-default travell5r" name=ksubmit type=submit value='Найти'>
  </div>
</div>
</form>

</div>
<?php

include('include/footprint.php');
echo "</div>\n";
  $customscript .= '
  $(\'select[multiple]\').multiselect({
    enableClickableOptGroups: true,
    enableCollapsibleOptGroups: true,
    includeSelectAllOption: true,
    /* buttonWidth: \'100%\', */
    nonSelectedText: \'Ничего не выбрано...\',
    nSelectedText: \' значений\',
    allSelectedText: \'Все...\',
    selectAllText: \'Все\'
  });
  ';

include('include/footer_bs.php');

?>

