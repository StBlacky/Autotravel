<?php
include_once('include/config.php');
include_once('include/func.php');
include_once('include/head00.php');
include_once('include/func_bs.php');

//exit;

$u = 0;
if (preg_match("/^\//",$_pin,$exa)) {
 if (preg_match("/^\/(\d+)$/",$_pin,$exa)) {
  $u = $exa[1];
 }
 if (preg_match("/^\/(\d+)\/(\-*\d+)$/",$_pin,$exa)) {
  $u = $exa[1];
  $a = $exa[2];
 }
}
else {
 $u = $_REQUEST["u"];
 $a = $_REQUEST["a"];
}

$jqueryon = 1;
$dispgmap=4;
$metatags = "Интерактивная карта ".fusername($u);
$title = "Интерактивная карта ".fusername($u);
$banners_shown = "yes";
include_once("include/head01.php");
?>
<table border=0 width=100%><tr><td>
<table border=0 width=100%><tr><td>
<table width="100%">
    <form method="post" action="javascript:filterForFolder();">
    <tr><td class="t12 tl travell0t" align="right">Фильтр по странам и областям:</td>
        <td class="t12 tl travell0t">
        <select name="mselectat" id="selectat" multiple="multiple"></select>
        </td>
        <td class="t12 tl travell0t"><input type="submit" value="Применить фильтр" /></td>
</form>
<tr><td valign="top" width="330">
<div class="sidebar-ct"><div id="the_side_bar" class="sidebar"></div></div>
</td><td colspan="2" valign="top" width=100%>
<div id="gmapv4" class="map-ct"></div>
<script type="text/javascript" language="JavaScript">
 var mapdiv = document.getElementById("gmapv4");
 initmapv6('/kml.php/u/<?php echo $u; ?>.kml',mapdiv);
</script>
</td></tr>
</table>
<?php
include("include/footer.php");
?>

