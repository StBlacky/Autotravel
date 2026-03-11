<?php
 // запрос курсов с YaHoo, запись в свою таблицу
 // https://www.bloomberg.com/markets/api/security/currency/cross-rates/RUB,MNT -- json
 include_once 'include/config.php';
 include_once 'include/func.php';
 include_once 'include/head00.php';
 // пока только MNT, но можно и больше
 $url = 'https://query.yahooapis.com/v1/public/yql?q=select+*+from+yahoo.finance.xchange+where+pair+=+%22RUBMNT,GELRUB,MKDRUB,BAMRUB,RSDRUB,HRKRUB%22&format=xml&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=';
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
  $res = $xml->results;
  if ($res) {
    foreach ($res->rate as $el)  {
      //var_dump($el);
      $name = $el->Name;
      $date = $el->Date;
      $ydate = date_create_from_format( "n/j/Y" , $date );
      $date = date_format($ydate , "Y-m-d");
      $cname = substr($name,4);
      if ($cname === "MNT") {
        $nominal = 1000;  
        $value = ( $nominal /( $el->Rate ));
      }
      else {
        $nominal = 1;  
        $cname = substr($name,0,3);  
        $value = $el->Rate;
      }
      $sql = " UPDATE valcurs set "
        . " nominal= " . $nominal . ","  
        . " value=" . $value . ","  
        . " date='" . $date . "' where charcode = '" . $cname . "'"; 
      $dbi->query($sql);
      echo "$sql\n";  
    }  
  }
 }

?>
