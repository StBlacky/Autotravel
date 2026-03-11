<?php
 // запрос курсов с сайта ЦБ, запись в свою таблицу
 include_once 'include/config.php';
 include_once 'include/func.php';
 include_once 'include/head00.php';

 $url = "https://www.cbr.ru/scripts/XML_daily.asp";
 //
 $c = curl_init();
 curl_setopt($c, CURLOPT_URL, $url);
 curl_setopt($c, CURLOPT_HEADER, 0);
 curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
 curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 2); //
 curl_setopt($c, CURLOPT_SSL_VERIFYPEER, true);
 curl_setopt($c, CURLOPT_TIMEOUT, 30);
 curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 10);
 curl_setopt($c, CURLOPT_MAXREDIRS, 0);
 curl_setopt($c, CURLOPT_BINARYTRANSFER, 1);
 curl_setopt($c, CURLOPT_FAILONERROR, 1);
 //
 $c_r = curl_exec($c);
 $c_e = curl_error($c);
 $processed = 0;
 libxml_use_internal_errors(true);
 if ($c_e) {
  // обработка технической ошибки (таймаут, не тот url и т.п.)
  die($c_e);
 }
 else {
  // обработка успеха и/или логической ошибки
  try {
   $xml = new SimpleXMLElement($c_r);
  }
  catch (Exception $e) {
   // обработка последней технической ошибки - обрыв ответа, ответ не от того url   
   die("Ошибка данных: ответ не формата xml ".htmlspecialchars($c_r));   
  }
  // реальная обработка
  $date = $xml->attributes()->{'Date'}; 
  if (!(empty($date))) { // попался атрибут Date - значит есть
    $date = datetoiso($date); 
    foreach ($xml as $el) {
      $sql = "insert into valcurs(charcode,numcode,name,nominal,value,date) values("
        . "'" . $el->{"CharCode"} . "',"  
        . "'" . $el->{"NumCode"} . "',"  
        . "'" . $el->{"Name"} . "',"  
        . $el->{"Nominal"} . ","  
        . dectoiso($el->{"Value"}) . ",'" . $date ."')"
        . " ON DUPLICATE KEY UPDATE "
        . " numcode = '" . $el->{"NumCode"} . "',"
        . " nominal=" . $el->{"Nominal"} . ","  
        . " value=" . dectoiso($el->{"Value"}) . ","  
        . " date='" . $date . "'"; 
      $dbi->query($sql);
      echo "$sql\n";  
      if (($el->{"NumCode"}) == '826') {
        $sql = "update valcurs set value=" . dectoiso($el->{"Value"}) . "," . " date='" . $date . "' where numcode=292";
        $dbi->query($sql);
        echo "$sql\n";  
      }
    }  
  }
  else {
    echo "Error: xml data not found!";  
  }
 }

?>
