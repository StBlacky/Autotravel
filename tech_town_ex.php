<?php 
include_once('include/config.php');
include_once('include/func.php');
include_once('include/func_bs.php');
include_once('include/head00.php');

$www_dir = "/home/blacky/data/www";
if (preg_match("/(\d+)\/(\d)$/",$_pin,$exa)) {
 $town = $exa[1];
 $etype = $exa[2];
}

if ($town) {
 $name = gettown_bs($town);
 $tnname = $name[1];
 //проходим по записям для вывода
 $sql="select e.name as name, e.descrip as description, e.Nshirota as shirota,
  e.Edolgota as dolgota, e.id as id, e.booking as booking, e.mainid,
  e.votes as votes, e.rate as rate, e.type as etype, e.kind as ekind,
  Right( e.name, length( e.name ) - locate( '\"', e.name )) AS loc,
  e.wrate as wrate
  from exciting e where e.town=$town and e.type=$etype and e.mainid<=0
  order by wrate desc, loc asc";

 $result = $dbi->query($sql);
 $ii = 0;
 echo '<table id="t'.$etype.'" width="95%" class="table"><tbody>';
 echo '<tr><th class="table-rate">Рейтинг</th><th>Название, <a class="travell5r" onclick="AllToggle(\'t'.$etype.'\',this);">подробности <img alt=">" src="/jpg/plus.gif" border="0"></a><div></div></th></tr>';

 if($myrow = mysqli_fetch_array($result)) {
  do {
    $etype = $myrow["etype"];
    $card = array();
    $card["ename"] = $myrow["name"];
    $card["mainid"] = $myrow["mainid"];
    $card["Nshirota"] = (float)$myrow["shirota"];
    $card["Edolgota"] = (float)$myrow["dolgota"];
    $card["descrip"] = $myrow["description"];
    $card["votes"] = $myrow["votes"];
    $card["kind"] = $myrow["ekind"];
    $s_id = $myrow["id"];
    $card["ex"] = $s_id;
    $wrate   = (float)$myrow["wrate"];
    $card["wrate"] = sprintf("%01.2f",$wrate);
    $ngrv = degreestotext($cards[$ii]["Nshirota"]);
    $egrv = degreestotext($cards[$ii]["Edolgota"]);
    if ($myrow["etype"] == 2) {
   	$card["booking"] = $myrow["booking"];
    } 
    else {
   	$card["booking"] = "";
    }
    $t_r = split_descr($myrow["description"]); //описание объекта
    $card["descr_local"] = $t_r[0]; // описание без адреса и режима работы
    $card["descr_local_full"] = display_t_r($t_r, "",0);

    $card["mark"] = bs_calc_rating($myrow["votes"],$myrow["wrate"]);
    
    //echo draw_card($card,"autocard1","tl");
    echo '<tr><td class="table-rate">'.$card["wrate"].'</td><td valign="middle" align="left">';
    echo '<a target="_blank" class="travell5r" href="/';
    if ($card["mainid"]>=0) {
       echo 'otklik.php';
    }
    else {
       echo 'group.php';
    }
    echo '/'.$card["ex"].'"><b>'.$card["ename"].'</b></a>';
    echo '<a rel="nofollow" class="travell5r" onclick="ChToggle(\'e'.$card["ex"].'\',this);"><img alt=">" src="/jpg/plus.gif" border="0"></a>';
    echo '<div title="CHG" id="e'.$card["ex"].'" style="visibility: hidden; display: none;">'.$card["descr_local_full"];
    //<div id="basketadd6110" class="travel0"><a class="travell5r" onclick="BasketAdd(6110,3349,&quot;basketadd6110&quot;)">Добавить в Испания. Июль 2018. Вариант 3</a></div>
    if (($forum_user_id) and ($card["mainid"]>=0)) {
      echo "\n<div class=\"travel0\" id=\"basketadd".$card["ex"]."\"><a onclick='BasketAdd(".$card["ex"].",".$forum_user_basket.",\"basketadd".$card["ex"]."\")'>Добавить в ".$forum_user_basket_name."</a></div>\n";
    }
    echo '</div>';
    echo "</td></tr>\n";
    
  } while($myrow = mysqli_fetch_array($result));
  mysqli_free_result($result);
 }
 echo '</tbody></table>';
} 
else {
  echo $_pin;
}
