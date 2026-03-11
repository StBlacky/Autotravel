<?php
//отображение календаря
$cm = (int) mysafestring($cm);
$cy = (int) mysafestring($cy);
if ((!$cm) || (!$cy))    {
 if ($di) {
  $cm = date("m",$di);
  $cy = date("Y",$di);
 }
 else {
  $cm = date("m",time());
  $cy = date("Y",time());
 }
}

/*== get what weekday the first is on ==*/
$tmpd = mktime(0,0,0,$cm,1,$cy);
$tmpm  = strftime("%b",$tmpd);
$tmpmm = strftime("%B",$tmpd);
$firstwday= strftime("%w",$tmpd) - 1;
if ($firstwday == -1) {
 $firstwday =6;
}
// ищем предыдущий месяц и делаем линк для отображения
$tmpd2 = $tmpd - (3600*24*20);
$lprev = "cy=" . date("Y",$tmpd2) . "&amp;cm=" . date("m",$tmpd2);

// ищем следующий месяц и делаем линк для отображения
$tmpd2 = $tmpd + (3600*24*40);
$lnext = "cy=" . date("Y",$tmpd2) . "&amp;cm=" . date("m",$tmpd2);

// теперь ищем последний день текущего месяца
$tmpd2 = mktime(0,0,0,date("m",$tmpd2),1,date("Y",$tmpd2)) - 60;

$lastday = date("d",$tmpd2);

$cal_out .= '
<div align=center>
<table cellpadding=1 cellspacing=0 border=1 style="min-width:180px">
<tr><td colspan=7>
  <table cellpadding=0 cellspacing=0 border=0 style="width:100%">
  <tr><th class="t14 tb tc tz" width="10"><a class=travell5m href="'.$calname."?".$lprev.'" rel="nofollow">&lt;&lt;</a></th>
  <th class="t14 tb tc tz"><a class=travell5m href="'.$calname."?cm=".$cm."&amp;cy=".$cy.'" rel=\"nofollow\">'.$tmpm.' '.$cy.'</a></th>
  <th class="t14 tb tc tz" width="10"><a class=travell5m href="'.$calname."?".$lnext.'" rel="nofollow">&gt;&gt;</a></th>
  </tr></table>
</td></tr>
<tr><th width=15 class="travell0">Пн</th><th width=15 class="t12 travell0">Вт</th>
  <th width=15 class="travell0">Ср</th><th width=15 class="t12 travell0">Чт</th>
  <th width=15 class="travell0">Пт</th><th width=15 class="t12 travell0b">Сб</th>
  <th width=15 class="t12 travell0b">Вс</th>
</tr>';
$dit   = Array(); // массив поездок по дням месяца
$dmark = Array(); // массив выделений клеток таблицы по дням месяца
$fdom = date("Y-m-d",mktime(0,0,0,$cm,1,$cy));
$ldom = date("Y-m-d",mktime(0,0,0,$cm,$lastday,$cy));
$prday = time() - (3600*25);

$sql_trip = "select datebeg, dateend from ourtrip
             where (datebeg >= '$fdom' and datebeg <= '$ldom') or
             (dateend >= '$fdom' and dateend <= '$ldom')";
$result_trip = $dbi->query($sql_trip);
if($myrow_trip = mysqli_fetch_array($result_trip)) {
 do {
   $datebeg  = $myrow_trip["datebeg"];
   $dateend  = $myrow_trip["dateend"];
   if (!($dateend)) {
    $dateend = $datebeg;
   }
   $bdate = strtotime($datebeg);
   $edate = strtotime($dateend);
   for ($d=1; $d<=$lastday; $d++) {
    $ctime = mktime(0,0,0,$cm,$d,$cy);
    if (($ctime >= $bdate) && ($ctime <= $edate)) {
     $cdate = date("Y-m-d",$ctime);
     $dit[$d] = "<a href=\"/ourtrip.php?ddd=$cdate\" rel=\"nofollow\"><b>$d</b></a>";
     if ($edate > $prday) {
      $dmark[$d] = " class=\"t12 travell0g\" ";
     }
    }
   }
 } while($myrow_trip = mysqli_fetch_array($result_trip));
}
$d  = 1;
$firstweek = true;
$ii = 1;
$ij = Array();
while ( $d <= $lastday)     {
 if ($firstweek) {
   for ($i=1; $i<=$firstwday; $i++) {
    $ij[$ii] = "&nbsp;";
    $ii++;
   }
   $firstweek = false;
 }
 $ij[$ii] = $d;
 $ii++;
 $d++;
}

$wday = 0;
for ($i=1; $i<$ii; $i++) {
    /*== Sunday start week with <tr> ==*/
    if ($wday==0) { print "<tr>"; }
    $d = $ij[$i];
    if ($dmark[$d]) {
     $cal_out .= "<td " . $dmark[$d] . ">";
    }
    else {
     if (($wday==5) || ($wday==6)) {
      $cal_out .= "<td class='t12 travell0b'>";
     }
     else {
      $cal_out .= "<td class='travell0'>";
     }
    }
    if ($dit[$d]) {
     $cal_out .= $dit[$d];
    }
    else {
     $cal_out .= $d;
    }
    $cal_out .= "</td>\n";

    /*== Saturday end week with </tr> ==*/
    if ($wday==6) { 
      $cal_out .= "</tr>\n";       
    }
    $wday++;
    $wday = $wday % 7;
}
if ($wday > 0) {
 $cal_out .= "</tr>\n";
}
$cal_out .= "</table></div>\n";