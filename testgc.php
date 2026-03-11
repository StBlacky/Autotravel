<?php
// Џ®ЁбЄ ЇаЁ¬Ґз вҐ«м­®бвҐ© б«ЁиЄ®¬ ¤ «ҐЄ® ­ е®¤пйЁебп ®в Ј®а®¤ 

include_once('include/config.php');
include_once('include/func.php');
include_once('include/head00.php');

// Select all the rows in the markers table
$query = "SELECT towns.id as tid, towns.name as tname, towns.area as area,
          exciting.id as eid, exciting.descrip as descrip,
          exciting.name as ename FROM towns, exciting 
          WHERE exciting.town = towns.id 
          and towns.NShirota > 1 
          and exciting.NShirota > 1
          and (
           ((exciting.Nshirota - towns.Nshirota) > 0.2) or
           ((towns.Nshirota - exciting.Nshirota) > 0.2) or
           ((exciting.Edolgota - towns.Edolgota) > 0.2) or
           ((towns.Edolgota - exciting.Edolgota) > 0.2)
          )
          ORDER BY exciting.ID ASC";
$result = $dbi->query($query);

// Initialize delay in geocode speed
$delay = 0;
$ii = 1;
# $base_url = "http://maps.google.com/maps/geo?output=csv&key=ABQIAAAAFMZwoCk_W0FlOrGWEcG8-hSrOn_KGLvWfx-bYd1aYMTYLrRKqBR-yj6I_NPUaZ-KZCinzOBr_cGd9w";

print "<h1>Объекты, расположенные дальше чем в 20 км от города, к которому привязаны:</h1>\n";

// Iterate through the rows, geocoding each address
while (($row = @mysqli_fetch_assoc($result)) and ($ii < 100)) {
  flush();
#  $ii++;
#  $geocode_pending = true;

#  while ($geocode_pending) {
    $tname  = $row["tname"];
    $tid    = $row["tid"];
    $ename  = $row["ename"];
    $eid    = $row["eid"];
    $caddr  = $row["descrip"];
    $area   = $row["area"];
    $a_name = getareaname($area);
    print "<br>$tid : $tname ($a_name)<br>\n $eid : $ename<br>\n$caddr\n<br><hr>";

#  }
}
/*
    $request_url = $base_url . "&hl=ru&oe=windows-1251&q=" . urlencode($haddr);
    $csv = file_get_contents($request_url) or die("url not loading");

    $csvSplit = explode(",", $csv);
    $status = $csvSplit[0];
    $lat = $csvSplit[2];
    $lng = $csvSplit[3];
    if (strcmp($status, "200") == 0) {
      // successful geocode
      $geocode_pending = false;
      $lat = $csvSplit[2];
      $lng = $csvSplit[3];

      $query = sprintf("UPDATE towns " .
             " SET NShirota = '%s', EDolgota = '%s' " .
             " WHERE id = %s LIMIT 1;",
             mysqli_real_escape_string($lat),
             mysqli_real_escape_string($lng),
             mysqli_real_escape_string($id));
      $update_result = $dbi->query($query);
      if (!$update_result) {
        die("Invalid query: " . mysqli_error());
      }
    } else if (strcmp($status, "620") == 0) {
      // sent geocodes too fast
      $delay += 1;
    } else {
      // failure to geocode
      $geocode_pending = false;
      echo "\n<br>\nAddress $id: $a_name $t_name failed to geocoded. ";
      echo "Received status " . $status . "\n<br>\n";
    }
    sleep($delay);
*/
?>
<br>

