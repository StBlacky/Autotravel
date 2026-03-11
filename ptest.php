<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><head>
<?php
 include_once 'include/config.php';
 include_once 'include/func.php';
 $ourname='Гостиница Удобная.';
 $ourinfo='Расположена в тихом уютном месте. Номера различного уровня комфорта. Разнообразные услуги.<br><b>Адрес: </b>Родная страна</p>';
 $otklik='<table class="travel0" borderwidth="0" border="1" cellpadding="8" width="95%"><tbody><tr><td class="travell0" width="150"><b>Посетитель</b><br><br><b>оценка: 5</b> (01-01-2007)</td><td class="travell0">Много желающих отдохнуть в этом райском уголке, но свободные места всегда есть.</td></tr></tbody></table>';
?>
  
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Content-language" content="ru">
<meta name="keywords" content="автотуризм гостиница">
<title>Автопутешествия по России: тест</title>

<link rel="StyleSheet" href="<?php echo $CSS; ?>" type="text/css"></head>
<body class="travell0">

<script type="text/javascript" language="JavaScript" src="js/travel7.js">
</script>
<script type="text/javascript" language="JavaScript" src="js/trip.js">
</script>
<h1>Здравствуйте, Гость.</h1>
<table border="0" cellpadding="1" cellspacing="0" width="100%">
<tbody>
<tr><td colspan="3">
<p class="t12 tl travell0o">Это - тестовая страничка ввода информации. 
Она похожа на рабочую версию личной страницы, 
доступной по имени и паролю.
В окне ввода "Ваша информация" (справа чуть ниже) можно размещать свой текст. После нажатия на кнопку "изменить"
текст поменяется. Внизу этой тестовой страницы показано, как именно этот текст будет выглядеть
на сайте (на настоящей странице эта часть отсутствует, поскольку текст реально размещается на сайт).
Слева показано, как выглядит окно вывода статистики по посещениям и окно вывода отзывов.
Разумеется, и статистика, и отзывы на этой тестовой странице не относятся ни к
каким реальным объектам сайта, в отличие от настоящей.
</p>
</td></tr>
<tr><td colspan="3"><p class="travell5c"><?php echo $ourname; ?></p>
<p class="t12 tl travell0o"><?php echo $ourinfo; ?>
</td></tr></tbody></table>
<table border="1" cellpadding="1" cellspacing="0" width="100%">
<tbody><tr>
<td rowspan="2" valign="top" width="40%">
<p class="travell5c">Ваша информация <font class="travell0">
(показ оплачен до 2017-12-31)</font>:</p>
<form name="excited" method="post" action="/ptest.php">
<textarea name="extext" rows="15" cols="40" wrap="none"></textarea>
<br><input name="submitexc" value="Изменить" type="submit">
</form>

<script type="text/javascript">
<!--
<?php
 $extext  = mysafehtml2($extext);
 if (!($extext)) {
  $extext = "Принимаются заявки на бронирование номеров.\nСтоимость одноместного номера - от 450 до 1230 рублей в сутки\n";
 }
 $_extext = my_unhtml($extext);
 echo "var str00='$_extext';\n";
?>

var rexp = /\[br\]/gi;
str00 = str00.replace(rexp,'\n');
document.forms['excited'].elements['extext'].value = str00;
//-->
</script>
</td><td align="left" valign="top"><p class="travell5c">Статистика посещений:</p>
<p class="travell0">За текущий месяц: <b>34</b><br>
За предыдущий месяц: <b>20</b><br>
С начала года: <b>116</b></p>
</td></tr>
<tr><td align="left" valign="top"><p class="travell5c">Отзывы посетителей:</p>
<?php echo $otklik; ?>
</td></tr>
</tbody></table>

<hr width=85%>
<table width=100% border=0><tr><td>
<h3 class="t14 tb tc">А вот как это будет выглядеть на странице сайта:</h3>

<hr width=85%>

<h3 class=travel11>
<a href="town.php/1055">Город</a>: 
<a href="excite.php?town=1055&et=2">Где остановиться?</a></h3>
<h4 class=travel11><img src=/jpg/bigpartner.gif width=10 height=10 
alt="Партнер сайта">&nbsp;&nbsp;<?php echo $ourname; ?></h4>
<table class=travell0 border=1 cellpadding=8 width=97%>
<tr><td><b>Официальная информация: </b>
<br><?php echo $extext; ?></td></tr></table>
<center><hr width=80%></center>
<p class="t12 tl travell0o"><?php echo $ourinfo; ?></p>
<center><hr width=80%></center>
<?php echo $otklik; ?>
</td></tr>
</table>
</body></html>
