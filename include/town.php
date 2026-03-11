<?php
    $result_e = $dbi->query($sql_e);
    if($myrow_e = mysqli_fetch_array($result_e)) {
     $ll_count = mysqli_num_rows($result_e); 
     $ex_forms .=  $ex_forms_add;
     $ex_forms .= "<table border=0 width=\"100%\"><tr><td class=travell5>";
     if ($ll_count > 1) {
       $ex_forms .= "<a class=travell5m target=_blank href=\"/excite.php/$town/$ctype_id\">";
     }
     else {
       $ex_forms .= "<p class=travell5m>";
     }
     $ex_forms .= "$ctype_name $s_namefrom";
     if ($ll_count > 1) {
       $ex_forms .= "</a>";
     }
     else {
       $ex_forms .= "</p>";
     }
     $ex_forms .= "</td>\n";
     if ($type_id == 2) {
      $ex_forms .= "<td class=travell5 align=right>".$bookingtext."</td>";        
     }
     $ex_forms .= "</tr></table>\n";
     if ($type_id == 1) {
      $ex_forms .= "<form name=ttype action='/town.php/$town'>\n<select class=travell5c name=type size=1 OnChange='this.form.submit();'>\n";
      $ex_forms .= option_list($town_types,$meta_type);
      $ex_forms .= "</select>\n</form>\n";
     }
     $ex_forms .=  "<form name=jsform" . $ctype_id . " action='#'>\n";
     $ex_forms .=  "\n<table id=\"t" . $ctype_id . "\" border=1 width='95%' cellpadding=4>\n<tbody class=travell0>\n";
     $ex_forms .=  "<tr><TD align=center valign=middle width=\"75\" onmouseover=\"roll(this,'Рейтинг означает привлекательность объекта для посетителей, вычисляется на основании отзывов')\" onmouseout=\"roll(this,'<b>Рейтинг:</b>')\"><b>Рейтинг:</b>\n";
     $ex_forms .=  "</TD><TD width='100%' valign=middle align=left><b>Название, ";
     if ($c_excitings[$ctype_id] > 1) {
      $ex_forms .=  "<a class=travell5r onclick=\"AllToggle('t".$ctype_id."',this);\">подробности <img border=0 alt=\">\" src=\"/jpg/plus.gif\"></a></b>";
     }
     else {
      $ex_forms .=  "подробности</b>";
     }
     $ex_forms .=  "</TD></TR>\n";
     do {
         $e_name = $myrow_e["name"];
         $e_booking = $myrow_e["booking"];
         $Nshirotae = (float)$myrow_e["Nshirota"];
         $Edolgotae = (float)$myrow_e["Edolgota"];
         $e_votes   = (float)$myrow_e["votes"];
         $e_rate    = (float)$myrow_e["rate"];
         $e_wrate   = (float)$myrow_e["wrate"];
         $e_wrate   = sprintf("%01.2f",$e_wrate);
         $e_description = $myrow_e["descrip"];
         $e_id = $myrow_e["id"];
         $ngrve = degreestotext($Nshirotae);
         $egrve = degreestotext($Edolgotae);
         if (($ngrve) and ($egrve)) {
            $coorde = "N$ngrve, E$egrve";
         }
         else {
            $coorde = "";
         }
         $e_descr_a = split_descr($e_description);
         $ex_forms .=  "<tr><TD align=left valign=middle>";
         $ex_forms .=  $e_wrate;
         $ex_forms .=  "</TD><TD align=left valign=middle>";
         $e_rek = getreklama($e_id,0);
         if($e_rek<>"") {
          $ex_forms .=  "<img src=\"/jpg/bigpartner.gif\" width=\"11\" height=\"11\" title=\"Партнер сайта\" alt=\"Партнер сайта\"> ";
         }
         if ($c_excitings[$ctype_id] > 1) {
          $ex_forms .=  "<a target=_blank class=travell5r href=\"/otklik.php/$e_id\"><b>$e_name</b></a> ";
          $ex_forms .=  "<a rel=\"nofollow\" class=travell5r onclick=\"ChToggle('e" . $e_id . "',this);\"><img border=0 alt=\">\" src=\"/jpg/plus.gif\"></a>";
          //$ex_forms .=  "<img border=0 class=travell5r onclick=\"ChToggle('e" . $e_id . "',this);\" alt=\">\" src=\"/jpg/plus.gif\"></a>";
          if ($type_id == 2) {
           if (!(empty($e_booking))) {
            $ex_forms .=  " [доступно бронирование]";
           }
          }
          $ex_forms .=  "<div title=\"CHG\" id=\"e" . $e_id . "\" style=\"visibility: hidden; display: none;\">";
         }
         else {
          $ex_forms .=  "<a target=_blank class=travell5r href=\"/otklik.php/$e_id\"><b>$e_name</b></a> ";
          if ($type_id == 2) {
           if (!(empty($e_booking))) {
            $ex_forms .=  " [доступно бронирование]";
           }
          }
          $ex_forms .=  "<div id=\"e" . $e_id . "\">";
         }
         if ($e_rek <> "") {
           $ex_forms .=  "<table border=1 cellpadding=4 width='97%'><tr><td class=travell0><b>Официальная информация: </b><br>$e_rek</td></tr></table>";
         }
         $ex_forms .= $e_descr_a[0]."<!--noindex-->";
         if (!(empty($e_descr_a[1]))) {
             $ex_forms .= "<b>Адрес: </b>".$e_descr_a[1];
         }
         if (!(empty($e_descr_a[2]))) {
             $ex_forms .= "<b>Режим работы: </b>".$e_descr_a[2];
         }
         if ($coorde) {
          $ex_forms .=  "<br><b>Координаты: </b> $coorde\n";
         }
         $ex_forms .= "<!--/noindex-->";
         $loc = $myrow_e["loc"];
         //$ex_forms .=  "<!--$loc-->\n";
         if (($forum_user_id>1)) {
          $ex_forms .= "<div id=basketadd$e_id  class=travel0><a class=travell5r onclick='BasketAdd($e_id,$forum_user_basket,\"basketadd$e_id\")'>Добавить в $forum_user_basket_name</a></div>\n";
         }
         $ex_forms .=  "</div>\n";
         #include("include/basketadd.php");
         $ex_forms .=  "</TD>";

         $ex_forms .=  "</TR>\n";
       } while($myrow_e = mysqli_fetch_array($result_e));
       $ex_forms .=  "</tbody></table>\n</form>\n";
     }
?>