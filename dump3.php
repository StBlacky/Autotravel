<?php
// Регистрация в oplataprosto тестового заказа testshop номер 903 на сумму 215.11
setlocale(LC_CTYPE, "ru_RU.utf8");
setlocale(LC_TIME, "ru_RU.utf8");
setlocale(LC_NUMERIC, "C");
date_default_timezone_set("Etc/UTC");
// Константы
$posturl = 'https://info.oplataprosto.ru/register.php';
$login = "testshop";
$password = "asf45buytae726";
$amount = number_format(215.11,2,".","");
$id = "903";
$url = "https://testshop.ru/oplata.php?order=".$id;
$hash = hash('SHA256',hash('SHA256', $id).hash('SHA256', $amount).$password);
// Собственно отправка запроса на регистрацию
$xml = '<?xml version=\'1.0\' encoding=\'utf-8\'?>'."\n"
        . '<body>'."\n".'<order login="'.$login.'" orderid="'.$id.'" amount="'.$amount.'" hash="'.$hash.'">'."\n"
        . '<url>' . $url . '</url>' ."\n"
        . '</order>'."\n".'</body>'."\n";  
$c = curl_init();
curl_setopt($c,CURLOPT_URL,$posturl);
curl_setopt($c,CURLOPT_RETURNTRANSFER,1);
curl_setopt($c,CURLOPT_POST,1);
curl_setopt($c,CURLOPT_POSTFIELDS,$xml);
curl_setopt($c,CURLOPT_HTTPHEADER,array('Content-Type: text/xml')); 
// получение результата
echo "Registration:<br><pre>";
echo htmlentities(curl_exec($c));
echo "</pre>\n";
curl_close($c);
// теперь запрос статуса этого же заказа
$posturl = 'https://info.oplataprosto.ru/query.php';
$login = "testshop";
$password = "asf45buytae726";
$hash2 = hash('SHA256',hash('SHA256', $id).$password);
$xml2 = '<?xml version=\'1.0\' encoding=\'utf-8\'?>'."\n"
        . '<body>'."\n".'<query login="'.$login.'" orderid="'.$id.'" hash="'.$hash2.'" />'."\n"
        . '</body>'."\n";
$c2 = curl_init();
curl_setopt($c2,CURLOPT_URL,$posturl);
curl_setopt($c2,CURLOPT_RETURNTRANSFER,1);
curl_setopt($c2,CURLOPT_POST,1);
curl_setopt($c2,CURLOPT_POSTFIELDS,$xml2);
curl_setopt($c2,CURLOPT_HTTPHEADER,array('Content-Type: text/xml')); 

echo "<hr>Status:<pre>";
echo htmlentities(curl_exec($c2));
echo "</pre>\n";
curl_close($c2);

die;
//
?>
