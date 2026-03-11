<?php

// 
// блок кода по отображению фотографий объекта
// НЕ ФУНКЦИЯ !
//
$fotodisplay = "";
if ($photocount) {
  $fotodisplay .= "<div id=\"fotocontent\"><div id=\"fotoimages\" align=center>";
  if ($photoimg["display"]>0) {
    $fotodisplay .= $photoimg["foto"];
  }
  // тут надо посмотреть что там внутри. если фото вообще нет (гостиница), то убрать "все". Если скрытых фото нет, то поубирать
  if ($photoimg["hidden"]>0) {
    if ($photoimg["display"]>0) {
      $fotodisplay .= "<br><font class=\"t13 tc\"><a class=travell5r onclick='ShowAllImg(); $(this).hide();'>Открыть ";
      $fotodisplay .= "все ";  
      $fotodisplay .= "фотографии ($photocount фото)</a></font>\n";
    }
    else {
      $fotodisplay .= $photoimg["all"];
    }
  }
  $fotodisplay .= "</div>\n";
  if (isset($_err_message)) {
    if ($_err_message) {
      $fotodisplay .= '<div id="fotomessage" class="alert alert-danger">'.$_err_message.'</div>';
    }
    else {
      $fotodisplay .= '<div id="fotomessage"  style="display: none" class="alert alert-danger"></div>';
    }
  }
  else {
    $fotodisplay .= '<div id="fotomessage"  style="display: none" class="alert alert-danger"></div>';
  }
  if (($photoimg["display"]>0) or ($photoimg["hidden"]>0)) {
  // убираем линк на фотоархив  $fotodisplay .= "<br><p class=travell0>Посмотреть фотоархив ($photocount фотографий) в разделе <a class=travell5r href='/$_fotoalbum/ex/$ex'>$s_ex: фотографии</a></p>\n";
  } 
  if ($forum_user_id == 4) {
    $fotodisplay .= "<br><p class=travell0>Посмотреть фотоархив ($photocount фотографий) в разделе <a class=travell5r href='/$_fotoalbum/ex/$ex'>$s_ex: фотографии</a></p>\n";
  }    
  $ownfoto = countownfotoexc($ex); // преобразование в int внутри
  if ($ownfoto > 0) {
    $fotodisplay .= "<p class=travell5><a class=travell5r target=_blank href='/fotoman.php?utown=$town'>Собственных фотографий $s_ex</a>: $ownfoto</p>\n";
  }
  $fotodisplay .= "</div>\n";
}
else {
  if (isset($_err_message)) {
    if ($_err_message) {
      $fotodisplay .= '<div id="fotocontent"><div id="fotomessage" class="alert alert-danger">'.$_err_message.'</div></div>';
    }
  }
}


