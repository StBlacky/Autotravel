<? 
    $m = (!$m) ? date("m",time()) : "$m";
    $y = (!$y) ? date("Y",time()) : "$y";
?>
<html>
<head>
    <title>Календарь совместных поездок</title>
</head>

<h2>Календарь поездок</h2>

<blockquote>
<table><tr><td valign="top">
  <? mk_drawCalendar($m,$y); ?>
</td></tr></table>

</blockquote>
</body>
</html>


<?php

//*********************************************************
// DRAW CALENDAR
//*********************************************************
/*
    Draws out a calendar (in html) of the month/year
    passed to it date passed in format mm-dd-yyyy 
*/

function mk_drawCalendar($m,$y)
{
    setlocale(LC_TIME, "ru_RU.utf8");
    if ((!$m) || (!$y))
    { 
        $m = date("m",time());
        $y = date("Y",time());
    }

    /*== get what weekday the first is on ==*/
    $tmpd = mktime(0,0,0,$m,1,$y);
    $month = strftime("%b",$tmpd); 
    $firstwday= strftime("%w",$tmpd) - 1;
    if ($firstwday == -1) {$firstwday =6;} 
    $lastday = mk_getLastDayofMonth($m,$y);


?>

<table cellpadding=2 cellspacing=0 border=1>
<tr><td colspan=7>
    <table cellpadding=0 cellspacing=0 border=0 width="100%">
    <tr><th width="20"><a href="<?=$SCRIPT_NAME?>?m=<?=(($m-1)<1) ? 12 : $m-1 ?>&y=<?=(($m-1)<1) ? $y-1 : $y ?>">&lt;&lt;</a></th>
    <th><font size=2><?="$month $y"?></font></th>
    <th width="20"><a href="<?=$SCRIPT_NAME?>?m=<?=(($m+1)>12) ? 1 : $m+1 ?>&y=<?=(($m+1)>12) ? $y+1 : $y ?>">&gt;&gt;</a></th>
    </tr></table>
</td></tr>
<tr><th width=22 class="tcell">Пн</th><th width=22 class="tcell">Вт</th>
    <th width=22 class="tcell">Ср</th><th width=22 class="tcell">Чт</th>
    <th width=22 class="tcell">Пт</th><th width=22 class="tcell">Сб</th>
    <th width=22 class="tcell">Вс</th></tr>

<?php  $d = 1;
    $wday = $firstwday;
    $firstweek = true;

    /*== loop through all the days of the month ==*/
    while ( $d <= $lastday) 
    {

        /*== set up blank days for first week ==*/
        if ($firstweek) {
            print "<tr>";
            for ($i=1; $i<=$firstwday; $i++) 
            { print "<td><font size=2>&nbsp;</font></td>"; }
            $firstweek = false;
        }

        /*== Sunday start week with <tr> ==*/
        if ($wday==0) { print "<tr>"; }

        /*== check for event ==*/  
        print "<td class='tcell'>";
        print "<a href=='/ourtrip.php?date=$m-$d-$y';\">$d</a>";
        print "</td>\n";

        /*== Saturday end week with </tr> ==*/
        if ($wday==6) { print "</tr>\n"; }

        $wday++;
        $wday = $wday % 7;
        $d++;
    }
?>
</tr></table>
<?php /*== end drawCalendar function ==*/
} 




/*== get the last day of the month ==*/
function mk_getLastDayofMonth($mon,$year)
{
    for ($tday=28; $tday <= 31; $tday++) 
    {
        $tdate = getdate(mktime(0,0,0,$mon,$tday,$year));
        if ($tdate["mon"] != $mon) 
        { break; }

    }
    $tday--;

    return $tday;
}

?>


