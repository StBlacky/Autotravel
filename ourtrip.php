<?php
// общий include, как и раньше
include_once('include/head00.php');
include_once('include/func_bs.php');

$it = (int) ($_REQUEST["it"] ?? 0);

// если есть id поездки — открываем страницу поездки
if ($it > 0) {
    include "ourtrip_one.php";
} else {
    include "ourtrip_list.php";
}
