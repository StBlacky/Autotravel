<?php
include_once('include/cube.data.php');

$mm = (sizeof($th) > 2) ? 'data-breakpoints="xs sm"' : '';
$iconpath = "/jpg/";

$vvv  = "<table id=\"cubedata\" width=\"100%\" cellspacing=\"0\" class=\"ttbs tdc table table-hover table-condensed\">\n";
$vvv .= "<thead><tr><th class=\"ttbs tl\">$tt</th>\n";

/* --- заголовки категорий --- */
foreach ($th as $label) {

  $desc = $labels_to_title[$label] ?? $label;
  $ico  = $icons_by_desc[$label] ?? '';
  $tip  = htmlspecialchars($desc, ENT_QUOTES);

  if ($ico) {
    $vvv .= "<th $mm class=\"t14 tb ttbs ww tc\" title=\"{$tip}\">
               <img src=\"{$ico}\" alt=\"{$tip}\" style=\"width:24px;height:24px;display:block;margin:0 auto;\">
             </th>\n";
  } else {
    $vvv .= "<th $mm class=\"t14 tb ttbs ww tc\" title=\"{$tip}\">{$tip}</th>\n";
  }
}

$vvv .= "</tr></thead>\n";
$vvv .= "<tbody class=\"t12 ttbs tc\">";

/* --- строки --- */
foreach ($td as $arn => $tda) {

  $urlt    = $tda["url"] ?? '';
  $area_id = $ar[$arn] ?? 0;

  // левая ячейка (область / город)
  if ($a == 0) {

    $vvv .= "<tr>
               <td class=\"ttbs tl\">
                 <a target=\"_blank\" class=\"travell5n\" href=\"/area.php/{$area_id}\">{$arn}</a>
               </td>";

  } else {

    if (!empty($tda["hide"])) {

      $texid = $tda["texid"] ?? 0;

      $vvv .= "<tr>
                 <td class=\"ttbs tl\">
                   <a target=\"_blank\" class=\"travell5r\" href=\"/otklik.php/{$texid}\">{$arn}</a>
                 </td>";

    } else {

      $local_url = $urlt ? "/gorod/{$urlt}.html" : "/town.php/{$area_id}";

      $vvv .= "<tr>
                 <td class=\"ttbs tl\">
                   <a target=\"_blank\" class=\"travell5n\" href=\"{$local_url}\">{$arn}</a>
                 </td>";
    }
  }

  /* --- ячейки с числами --- */
  foreach ($th as $label) {

    $c = (int)($tda[$label]["count"] ?? 0);

    if ($c > 0) {

      $areaSlug = $ar_slug[$arn] ?? '';
      $exid     = $tda[$label]['exid'] ?? 0;

      $url = cube_get_link($area_id, $areaSlug, $label, $c, $exid);

      $vvv .= "<td class=\"ttbs\">
                 <a href=\"{$url}\" target=\"_blank\" class=\"cubebutton travell5n\">{$c}</a>
               </td>";

    } else {

      $vvv .= "<td class=\"ttbs\">&nbsp;</td>";

    }
  }

  $vvv .= "</tr>\n";
}

$vvv .= "</tbody></table>\n";

echo '<div class="cube-wrapper"><div class="cube-scroll">'.$vvv.'</div></div>';
?>
