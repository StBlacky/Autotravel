<?php
//оценка отчетов и отзывов - обновление данных в базе данных
//вызывается из форм оценки
include_once('include/config.php');
include_once('include/func.php');
include('include/head00.php');

$type = $_POST["type"];
$idtype = $_POST["idtype"];
$score = $_POST["score"];

$oldscore = 0;

if (($forum_user_id>0) and ($forum_user_weight>0.5)) { // только для авторизованного форум-жителя

 //надо найти, оценивался ли данный объект данным участником, если нет - вставить запись, если да - изменить оценку
 $sql = "INSERT INTO score (type, idtype, score, cook) VALUES ('$type', '$idtype', '$score', '$ctext') "
        . "ON DUPLICATE KEY UPDATE score = '$score'";
 //echo $sql;
 $result = $dbi->query($sql);
 //echo "result  ".$result;

 if ($result<>0) { //если все прошло хорошо, пересчитаем рейтинг объекта
  if ($type=="excite") { // отклики
   if ($score==1) {
     echo "$('#plus".$idtype."').attr('src','/png/plus.png');\n";
     echo "$('#minus".$idtype."').attr('src','/png/aminus.png');\n";
   }
   if ($score==-1) {
     echo "$('#plus".$idtype."').attr('src','/png/aplus.png');\n";
     echo "$('#minus".$idtype."').attr('src','/png/minus.png');\n";
   }
  }
  $sql = "select sum(score) as score from score  where idtype='$idtype' and type='$type'";
    //echo $sql;
  $resultrep = $dbi->query($sql);
  $myrow = mysqli_fetch_array($resultrep);
  $totalscore = $myrow["score"];
  

  if ($type=="report") {//мы начнем с  отчетов
    $sqlrep = "update l_reports set score=$totalscore where rid='$idtype'";
    $resultrep = $dbi->query($sqlrep);
    echo $totalscore;
  } //отработали отчеты
    
 } // поменяли рейтинг

 
}

?>
