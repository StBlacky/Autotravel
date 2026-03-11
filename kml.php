<?php
//
// Выгрузка в kml wpt или gpx всех объектов с координатами
// по области (a=),
// городу с окрестностями (t=)
// списку объектов через запятую (ids=)
// списку городов через запятую (tlist=)
// юзеру (u=)
// или произвольной области (BBOX=) (долгота мин, широта мин, долгота макс, широта макс)
// списки объектов и городов могут использоваться совместно (*И*).
// Поиска по окрестностям для списков -- НЕТ.
//

include_once('include/config.php');
include_once('include/func.php');
include_once('include/head00.php');
mysqli_set_charset($dbi, 'utf8mb4');
$r_types=array();
$r_types[0] = "Города";
$sql_fd = "select name, id from type";
$result_fd = $dbi->query($sql_fd);
while ($row_trip = mysqli_fetch_array($result_fd)) {
    $id_fd = $row_trip["id"];
    $r_types[$id_fd] = $row_trip["name"];
}
mysqli_free_result($result_fd);

// --- AT MAP: справочник групп (type_ex.desc -> slug/icon) ---
// --- AT MAP: справочник групп (type_ex.desc -> slug/icon) ---
$at_grp_map = array();

$res_tx = $dbi->query("
    SELECT `desc` AS grp_name, `desc_slug`, `icon`
    FROM type_ex
    WHERE `desc` IS NOT NULL AND `desc` <> ''
");

if ($res_tx) {
  while ($row = mysqli_fetch_assoc($res_tx)) {

    $name = trim((string)$row['grp_name']);
    if ($name === '') continue;

    $key = trim(mb_strtolower($name, 'UTF-8'));

    $at_grp_map[$key] = array(
      'name' => $name,
      'slug' => trim((string)$row['desc_slug']),
      'icon' => trim((string)$row['icon']),
    );
  }
  mysqli_free_result($res_tx);
}

function at_exgrp_name($raw) {
  $raw = trim((string)$raw);
  if ($raw === "") return "";
  // "1.Природа" -> "Природа"
  $raw = preg_replace('/^\s*\d+\s*\.\s*/u', '', $raw);
  return trim((string)$raw);
}

$oneid = 0;

if (preg_match("/^\//",$_pin,$exa)) {
 $resheader = "";
 $t     = 0;
 $a     = 0;
 $u     = 0;
 $tlist = "";
 $drawline = "";
 $ids   = "";
 $box   = "";
 $form  = "";
 $meta_type = "";
 if (preg_match("/^\/t\/(\d+)\.(.+)$/",$_pin,$exa)) {
  $t = $exa[1];
  $form = strtoupper($exa[2]);
 }
 if (preg_match("/^\/t\/(\d+)\/(\d+)\.(.+)$/",$_pin,$exa)) {
  $t = $exa[1];
  $meta_types=getextypes();
  $sort_types=sortextypes();
  $meta_type = $sort_types[$exa[2]];
  $form = strtoupper($exa[3]);
 }
 if (preg_match("/^\/a\/(\-*\d+)\.(.+)$/",$_pin,$exa)) {
  $a = $exa[1];
  $form = strtoupper($exa[2]);
 }
 if (preg_match("/^\/u\/(\d+)\.(.+)$/",$_pin,$exa)) {
  $u = $exa[1];
  $form = strtoupper($exa[2]);
 }
 if (preg_match("/^\/e\/(\d+)\.(.+)$/",$_pin,$exa)) {
  $ids  = $exa[1];
  $oneid = 1;
  $form = strtoupper($exa[2]);
 }
}
else {
 $a = $_REQUEST["a"];
 $a = mysafestring($a);
 $t = $_REQUEST["t"];
 $t = (int)mysafestring($t);
 $box = $_REQUEST["BBOX"];
 $box = mysafestring($box);
 $resheader = $_REQUEST["resheader"];
 $resheader = mysafestring($resheader);
 $ids = $_REQUEST["ids"];
 $ids = mysafestring($ids);
 $tlist = $_REQUEST["tlist"];
 $tlist = mysafestring($tlist);
 $form = $_REQUEST["f"];
 $form = substr(mysafestring($form),-3);
 $drawline = $_REQUEST["drawline"];
}

$sqlxadd = "";
$sqladd  = "";
$sqltadd = "";

if (($form != 'WPT') and ($form != 'KML') and ($form != 'GPX')) {
 $form = 'KML';
}

$eids = Array();

if ($ids) {
 $eids = explode(',',$ids);
 for ($ii=0; $ii<sizeof($eids); $ii++) {
  $eids[$ii] = (int)$eids[$ii];
 }
 $ids = join($eids,",");
}

if ($a) {
 $eids = explode(',',$a);
 for ($ii=0; $ii<sizeof($eids); $ii++) {
  $eids[$ii] = (int)$eids[$ii];
 }
 $a = join($eids,",");
}

$tids = Array();

if ($tlist) {
 $tids = explode(',',$tlist);
 for ($ii=0; $ii<sizeof($tids); $ii++) {
  $tids[$ii] = (int)$tids[$ii];
 }
 $tlist = join($tids,",");
}

$nmarray=array();

$sql_eee="select '' as odate, '' as otime, '' as omark,
        e.name as ename, e.type as etype, e.town as etown, e.id as eid, e.kind as ekind, g.exgrp as exgrp,
        e.descrip as edescrip, e.Nshirota as eNshirota, e.Edolgota as eEdolgota, e.wrate as ewrate,
        t.Nshirota as tNshirota, t.Edolgota as tEdolgota, a.name as aname,
        t.name as tname, t.descrip as tdescrip, t.hide as thide
        from exciting e
join towns t on e.town = t.id
join areas a on a.id = t.area
left join (
  select exid, min(exgrp) as exgrp
  from exbygroup
  group by exid
) g on g.exid = e.id
where 1=1
 ";

$sql_e = "";
$nmin  = 0;
$nmax  = 0;
$emin  = 0;
$emax  = 0;
#$rdiff = ($defdiff / 120);
$rdiff = 0.2;

if (($box) and (!($t)) and (!($u)) and (!($a)) and (!($ids)) and (!($tlist))) {
 if (!$resheader) {
  $resheader = "Географическая область на сайте autotravel.ru";
 }
 list($emin,$nmin,$emax,$nmax)=explode(",",$box);
 $nmin = (float)$nmin;
 $nmax = (float)$nmax;
 $emin = (float)$emin;
 $emax = (float)$emax;
 if ($nmin <=1) {
  $nmin = 20;
 }
 if ($emin <=-10) {
  $emin = 20;
 }
 if ($nmax < $nmin) {
  $nmax = $nmin + 1;
 }
 if ($emax < $emin) {
  $emax = $emin + 1;
 }
 if ($emax > 180) {
  $emax = 180;
 }
 $ndiff = $nmax - $nmin;
 if ($ndiff > 6) {
   $nmax = (($nmax + $nmin) / 2) + 3;
   $nmin = $nmax - 6;
 }
 $ediff = $emax - $emin;
 if ($ediff > 8) {
   $emax = (($emax + $emin) / 2) + 4;
   $emin = $emax - 8;
 }
}

$sqltadd = "";

if (($ids) or ($tlist)) {
 if (!$resheader) {
  $resheader = "Выбранные объекты на сайте autotravel.ru";
 }
 if (($ids) and (!($tlist))) {
  $sqladd = "and e.id in ($ids)";
  $sqltadd = " and id in ($ids) ";
 }
 if (($tlist) and (!($ids))) {
  $sqladd = "and t.id in ($tlist)";
 }
 if (($tlist) and ($ids)) {
  $sqladd = "and ((t.id in ($tlist)) and (e.id in ($ids)))";
  $sqltadd = " and id in ($ids) ";
 }
 $sql_e="$sql_eee and
        ((e.Nshirota <> 0 and e.Edolgota <> 0) or (t.Nshirota <> 0 and t.Edolgota <> 0))
        $sqladd ";
}

if ($a) {
 if (strstr($a,',')) {
  $resheader = "Выбранные области на сайте autotravel.ru";
 }
 else {
  $resheader = getareaname($a) . " на сайте autotravel.ru";
 }
 $sql_e="$sql_eee and t.area in ($a) and
        ((e.Nshirota <> 0 and e.Edolgota <> 0) or (t.Nshirota <> 0 and t.Edolgota <> 0)) ";
}


if ($t > 0) {
 $resheader = gettownname($t) . " и окрестности на сайте autotravel.ru";
 $sql    = "select Nshirota, Edolgota from towns where id='$t'";
 $result = $dbi->query($sql);
 $nsh   = 0;
 $edg   = 0;
 if($myrow = mysqli_fetch_array($result)) {
  $nsh = (float)$myrow["Nshirota"];
  $edg = (float)$myrow["Edolgota"];
 }
 if (($nsh != 0) and ($edg != 0)) {
     $nmin     = $nsh - $rdiff;
     $nmax     = $nsh + $rdiff;
     $emin     = $edg - ($rdiff/cos(deg2rad($nsh)));
     $emax     = $edg + ($rdiff/cos(deg2rad($nsh)));
 }

 mysqli_free_result($result);
 $sqladd = " UNION $sql_eee and t.id in ( $t ) ";

}

if (($nmin > 0) and ($emin > -10)) {
 $sql_e="$sql_eee and
        (e.Nshirota > $nmin and e.Edolgota > $emin and e.Nshirota < $nmax and e.EDolgota < $emax)
        UNION $sql_eee and
        (t.Nshirota > $nmin and t.Edolgota > $emin and t.Nshirota < $nmax and t.EDolgota < $emax)
        $sqladd ";
}

if ($u) {
 $uuname  = fusername($u);
 $resheader = $uuname . " - интерактивная карта: отклики";
 $sql_e="select FROM_UNIXTIME(o.time, '%e-%m-%Y') as odate, o.time as otime,
        o.mark as omark, o.descrip as edescrip,
        e.name as ename, e.type as etype, e.town as etown, e.id as eid,
        e.kind as ekind, e.wrate as ewrate, a.name as aname,
        e.Nshirota as eNshirota, e.Edolgota as eEdolgota,
        t.Nshirota as tNshirota, t.Edolgota as tEdolgota,
        t.name as tname, t.descrip as tdescrip, t.hide as thide, t.area as tarea
        from exciting e, towns t, otklik o, areas a
        where o.cook like 'forum:$u:%' and
        e.id = o.exciting and t.id = e.town and
        a.id = t.area and
        ((((e.Nshirota > 0) or (e.Nshirota < 0)) and 
        ((e.Edolgota < 0) or (e.Edolgota > 0))) 
        or 
        (((t.Nshirota > 0) or (t.Nshirota < 0)) and ((t.Edolgota > 0) or (t.Edolgota < 0))))
        ";
}

if ($sql_e) {
 //echo $sql_e;
 $result_e = $dbi->query($sql_e);
 if($myrow_e = mysqli_fetch_array($result_e)) {
  do {
   $e_name   = $myrow_e["ename"];
   $kind_list   = $myrow_e["ekind"];
   $e_descr  = $myrow_e["edescrip"];
   $o_mark   = $myrow_e["omark"];
   $o_date   = $myrow_e["odate"];
   if ($o_date) {
     $e_descr = $o_date . " " . $e_descr;  
   }
   $t_name   = $myrow_e["tname"];
#   $t_descr  = $myrow_e["tdescrip"];
   $t_descr  = "";
   $type_id  = $myrow_e["etype"];
   $wrate    = $myrow_e["ewrate"];
   $e_id     = $myrow_e["eid"];
   $t_id     = $myrow_e["etown"];
   $a_name   = $myrow_e["aname"];
   $t_hide   = $myrow_e["thide"];
   $Nshirotae = (float)$myrow_e["eNshirota"];
   $Edolgotae = (float)$myrow_e["eEdolgota"];
   $Nshirotat = (float)$myrow_e["tNshirota"];
   $Edolgotat = (float)$myrow_e["tEdolgota"];
   if (($Nshirotat != 0) and ($Edolgotat != 0)) {
    if (($nmin <= 0) or ($nmin > $Nshirotat)) { $nmin = $Nshirotat; }
    if (($nmax <= 0) or ($nmax < $Nshirotat)) { $nmax = $Nshirotat; }
    if (($emin <= -10) or ($emin > $Edolgotat)) { $emin = $Edolgotat; }
    if (($emax <= -10) or ($emax < $Edolgotat)) { $emax = $Edolgotat; }
   }
   if (($Nshirotae != 0) and ($Edolgotae != 0)) {
    if (($nmin <= 0) or ($nmin > $Nshirotae)) { $nmin = $Nshirotae; }
    if (($nmax <= 0) or ($nmax < $Nshirotae)) { $nmax = $Nshirotae; }
    if (($emin <= -10) or ($emin > $Edolgotae)) { $emin = $Edolgotae; }
    if (($emax <= -10) or ($emax < $Edolgotae)) { $emax = $Edolgotae; }
   }
   $atdx     = $type_id;
   $attx     = 0;
   $atd      = "d";
   if ($type_id == 2) {
      $atd      = "o";
   }
   if ($type_id == 3) {
      $atd      = "e";
   }

   if ($t_hide <> 2 ) {
    if (!($t)) {
      $e_name = $e_name . " ($t_name)";
    }
    $t_descr  = "";
    $att    = "t";
   }
   else {
    $t_name   = $e_name . " ($t_name)";
    $e_name   = $t_name;
    $t_descr  = $e_descr;
    $att      = $atd;
    $attx     = $atdx;
   }
   if ($o_mark) {
     $e_name   .= " /".$o_mark."/";  
   }
   if ($oneid) {
    $resheader = "$e_name";
    $t_link  = "<a href=\"/map.php/t/$t_id\">Посмотреть карту окрестностей</a>\n";
   }
   $place_mark = "1";
   $kind_txt = Array();
   if ($meta_type) {
    $place_mark = "0";
    $kind_arr = explode(",",$kind_list);
    while($kind_item=each($kind_arr)) {
     $kind_id = $kind_item["value"];
     $kind_name = $meta_types[$kind_id];
     if ($kind_name) {
      array_push($kind_txt, $kind_name);
      if ($kind_name == $meta_type) {
       $place_mark = "1";
      }
     }
    }
   }
 if (($Nshirotae != 0) and ($Edolgotae != 0)) {

  $t_descr = "";

  if ($place_mark) {

    if ($form == 'KML') {

      // --- AT MAP: группа/иконка ---
      $grp_raw  = isset($myrow_e["exgrp"]) ? $myrow_e["exgrp"] : "";
$grp_name = at_exgrp_name($grp_raw);

$grp_key = ($grp_name !== "") 
    ? trim(mb_strtolower($grp_name, 'UTF-8')) 
    : "";

$grp_slug = "";
$grp_icon = "";

if ($grp_key && isset($at_grp_map[$grp_key])) {
  $grp_slug = $at_grp_map[$grp_key]['slug'];
  $grp_icon = $at_grp_map[$grp_key]['icon'];
}

      if (!isset($at_grp_styles)) $at_grp_styles = array();
      if ($grp_slug !== "" && $grp_icon !== "") {
        $at_grp_styles[$grp_slug] = $grp_icon;
      }

      //$styleUrl = ($grp_slug !== "" ? "#grp-" . $grp_slug : "#at$atd");
$styleUrl = "#at$atd";
      // ✅ data-grp
      $grp_attr = "";
 /*     if ($grp_name !== "") {
        $grp_attr = " data-grp=\"" . kmlstring($grp_name) . "\"";
      }*/

      $kmlval =
        "<name>" . kmlstring($e_name) . "</name>\n" .
        "<description>" .
          "&lt;div$grp_attr&gt;" .
          kmlstring($e_descr) .
          "\n&lt;BR&gt;\n" .
          "&lt;a target=_blank href=$sitename/otklik.php/$e_id&gt;" .
          kmlstring("Подробности, фото, отзывы посетителей") .
          "&lt;/a&gt;&lt;/div&gt;" .
        "</description>\n" .
        "<styleUrl>$styleUrl</styleUrl>\n" .
        "<Point><coordinates>$Edolgotae,$Nshirotae,0</coordinates></Point>\n";

      $kmlval .= "<ExtendedData>";
      $kmlval .= "<Data name=\"TYPE\"><value>".$r_types[$type_id]."</value></Data>\n";
      $kmlval .= "<Data name=\"ETYPE\"><value>".$type_id."</value></Data>\n";

      if ($grp_slug !== "") {
        $kmlval .= "<Data name=\"GRP_SLUG\"><value>".kmlstring($grp_slug)."</value></Data>\n";
      }
      if ($grp_name !== "") {
        $kmlval .= "<Data name=\"GRP_NAME\"><value>".kmlstring($grp_name)."</value></Data>\n";
      }
      if ($grp_icon !== "") {
  $kmlval .= "<Data name=\"GRP_ICON\"><value>".kmlstring($grp_icon)."</value></Data>\n";
}
      $kmlval .= "<Data name=\"TOWN\"><value>".$t_name."</value></Data>\n";
      $kmlval .= "<Data name=\"REGION\"><value>".$a_name."</value></Data>\n";
      $kmlval .= "<Data name=\"RATING\"><value>".$wrate."</value></Data>\n";

      if ($o_mark) {
        $kmlval .= "<Data name=\"MARK\"><value>".$o_mark."</value></Data>\n";
      }

      foreach ($kind_txt as &$newvalue) {
        $kmlval .= "<Data name=\"SUBTYPE\"><value>".$newvalue."</value></Data>\n";
      }

      $kmlval .= "</ExtendedData>";
    

      if (!($u)) {
          $nmarray[$r_types[$type_id]]["atex$e_id"] = $kmlval;
      } else {
          $nmarray[$a_name]["atex$e_id"] = $kmlval;
      }
    }
  }
}

   if (($Nshirotat != 0) and ($Edolgotat != 0) and (!($u))) { // для юзера не надо
// города. Всегда должны иметь координаты
    if (($place_mark) and (!($nmarray["attn$t_id"]))) {
     if (!($t_descr)) {
//    примечательности без координат - для $ids не все.
      $sql_a="select name from exciting where town = $t_id and
            Nshirota = 0 and Edolgota = 0 $sqltadd";
      $result_a = $dbi->query($sql_a);
      if($myrow_a = mysqli_fetch_array($result_a)) {
       do {
        if ($t_descr) {
         $t_descr = $t_descr . "<BR>\n" . $myrow_a["name"];
        }
        else {
         $t_descr = "На сайт autotravel.ru занесены:<BR>\n" . $myrow_a["name"];
        }
       } while($myrow_a = mysqli_fetch_array($result_a));
      }
     }

// город нуждается в отображении - есть примечательности без координат

     if ($t_descr) {
      if ($form == 'KML') {
       $kmlval = "<name>" . kmlstring($t_name) . "</name><description>" .
           kmlstring($t_descr) . "\n&lt;BR&gt;\n&lt;a target=_blank href=$sitename/town.php/" .
           $t_id . "&gt;" . kmlstring("Подробности, фото, отзывы посетителей") . "&lt;/a&gt;\n</description>\n" .
           "<styleUrl>#at$att</styleUrl>\n".
           "<Point><coordinates>$Edolgotat,$Nshirotat,0</coordinates></Point>\n";
       $kmlval .= "<ExtendedData>";
       $kmlval .= "<Data name=\"TYPE\"><value>".$r_types[0]."</value></Data>\n";
       $kmlval .= "<Data name=\"TOWN\"><value>".kmlstring($t_name)."</value></Data>\n";
       $kmlval .= "<Data name=\"REGION\"><value>".kmlstring($a_name)."</value></Data>\n";
       $kmlval .= "</ExtendedData>";
       $nmarray[$r_types[0]]["attn$t_id"] = $kmlval;
      }
    
      if (($form == 'GPX')) {
       $nmarray["attn$t_id"] = "<wpt lon=\"$Edolgotat\" lat=\"$Nshirotat\">\n" .
        "<name>" . kmlstring($t_name) . "</name><desc>" .
        kmlstring($t_descr) . "</desc>\n" .
        "<link href=\"$sitename/town.php/$t_id\" />\n<sym />\n</wpt>\n";
      }
      if ($form == 'WPT') {
       $nmarray["attn$t_id"]=textstring($t_name) . ",$Nshirotat,$Edolgotat,,0,1,3,0,65535," .
                            textstring($t_descr) . ",0,0,0,-777";
      }
     }

// город

    }
   }
  } while($myrow_e = mysqli_fetch_array($result_e));
 }
 mysqli_free_result($result_e);
}
//

// маршрут по городам
if ((sizeof($tids) > 0)&&(!($drawline))) {
 $linemap = "";
 if ($form == 'KML') {
  for ($ii=0; $ii<sizeof($tids); $ii++) {
   $tcur = $tids[$ii];
   $ccur = gettcoords($tcur);
   if (($ccur[0] != 0) and ($ccur[1] != 0) ) {
     $linemap .= $ccur[1] . "," . $ccur[0] . ",0\n";
   }
  }
 }
}

// маршрут по точкам вмето городов
if ((sizeof($eids) > 0)&&($drawline)) {
 $linemap = "";
 if ($form == 'KML') {
  for ($ii=0; $ii<sizeof($eids); $ii++) {
   $tcur = $eids[$ii];
   $ccur = getecoords($tcur);
   if (($ccur[0] != 0) and ($ccur[1] != 0) ) {
     $linemap .= $ccur[1] . "," . $ccur[0] . ",0\n";
   }
  }
 }
}

if ($nmarray) {
 reset($nmarray);

//
// KML begin
//
 if ($form == 'KML') {
  header("Content-type: application/vnd.google-earth.kml+xml");
  header("Content-Disposition: attachment; filename=Autotravel_waypoints.kml");
  echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
  //echo "<kml xmlns=\"http://earth.google.com/kml/2.1\">\n<Document>\n";
  echo "<kml xmlns=\"http://www.opengis.net/kml/2.2\">\n<Document>\n";
  echo "<name>" . kmlstring($resheader) . "</name>\n";
  echo "<Style id=\"ate\">\n<IconStyle>\n<Icon>\n<href>$sitename/jpg/anchor-e.png</href>\n</Icon>\n</IconStyle>\n</Style>\n";
  echo "<Style id=\"ato\">\n<IconStyle>\n<Icon>\n<href>$sitename/jpg/anchor-o.png</href>\n</Icon>\n</IconStyle>\n</Style>\n";
  echo "<Style id=\"atd\">\n<IconStyle>\n<Icon>\n<href>$sitename/jpg/anchor-d.png</href>\n</Icon>\n</IconStyle>\n</Style>\n";
  echo "<Style id=\"att\">\n<IconStyle>\n<Icon>\n<href>$sitename/jpg/anchor-t.png</href>\n</Icon>\n</IconStyle>\n</Style>\n";
  // --- AT MAP: styles for groups (custom icons) ---
if (isset($at_grp_styles) && is_array($at_grp_styles) && count($at_grp_styles)) {
  foreach ($at_grp_styles as $slug => $icon) {
    $slug = preg_replace('/[^a-z0-9\-_]+/i', '', $slug);
    $icon = trim((string)$icon);
    if ($slug === "" || $icon === "") continue;
    echo "<Style id=\"grp-$slug\">\n<IconStyle>\n<Icon>\n<href>$sitename/jpg/$icon</href>\n</Icon>\n</IconStyle>\n</Style>\n";
  }
}

  ksort($nmarray);
  reset($nmarray);
  foreach ($nmarray as $type_id => $arrayx) {
   echo "<Folder><name>$type_id</name>\n";
   asort($arrayx);
   reset($arrayx); 
   foreach ($arrayx as $tnmkey => $nmvalue) {
    echo "<Placemark id=\"$tnmkey\">\n$nmvalue\n</Placemark>\n";
   }
   echo "</Folder>\n";
  }
  if ($linemap)  {
   // новый кмл парсер где-то в ней падает; отключена   
   //echo "<Placemark>\n<name>" . kmlstring("Трасса") . "</name>\n<Style><LineStyle><color>88dd88ff</color></LineStyle></Style>\n";
   //echo "<LineString><tessellate>1</tessellate><coordinates>\n";
   //echo $linemap;
   //echo "</coordinates>\n</LineString>\n</Placemark>\n";
  }
  echo "</Document>\n</kml>\n";
 }
//
// KML end.
//

 if ($form == 'WPT') {
  header("Content-type: application/x-ozi-wpt");
  header("Content-Disposition: attachment; filename=Autotravel_waypoints.wpt");
  echo "OziExplorer Waypoint File Version 1.0\nWGS 84\r\n";
  echo "Reserved 2\r\nReserved 3\r\n";
  $ii = 1;
  while ( list($tnmkey,$nmvalue)=each($nmarray) ) {
    echo "$ii,$nmvalue\r\n";
    $ii++;
  }
 }

 if ($form == 'GPX') {
  header("Content-type: application/gpx+kml");
  header("Content-Disposition: attachment; filename=Autotravel_waypoints.gpx");
  echo "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\" ?>\n";
  echo "<gpx version=\"1.1\" creator=\"autotravel.ru\" xmlns=\"http://www.topografix.com/GPX/1/1\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd\">\n";
  while ( list($tnmkey,$nmvalue)=each($nmarray) ) {
    echo $nmvalue;
  }
  echo "</gpx>";
 }

}

?>

