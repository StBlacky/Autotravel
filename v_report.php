<?php

include_once('include/head00.php');

// --- NORMALIZE URL /v_report.php/{id} ---

$request = $_SERVER["REQUEST_URI"];



// Достаём последнюю группу цифр из URL
if (preg_match('#/(\d+)(?:[^0-9].*)?$#', $request, $m)) {
    $id = (int)$m[1];

    // Проверяем, существует ли отчёт
    $check = $dbi->query("SELECT rid FROM l_reports WHERE rid=$id")->fetch_assoc();

    if ($check) {
        // Если URL не совпадает с каноном — делаем 301
        $canonical = "/v_report.php/$id";
        if ($request !== $canonical) {
            header("Location: $canonical", true, 301);
            exit;
        }
    } else {
        // ID не существует — отдаём 410
        header("HTTP/1.1 410 Gone");
        echo "Отчёт не найден.";
        exit;
    }
} else {
    // Вообще нет числа — 410
    header("HTTP/1.1 410 Gone");
    echo "Некорректный адрес отчёта.";
    exit;
}

$bootstrap = 1;
$colorbox = 1;

if (preg_match("/^\/(\d+)\s*$/", $_pin, $rida)) {
  $rid = $rida[1];
} 
else {
  $ip = mysafestring($_REQUEST["ip"]);
  $rid = (int) mysafestring($_REQUEST["rid"]);
  $hl = mysafestring($_REQUEST["hl"]);
  $ht = (int) $_REQUEST["ht"];
  $t = (int) $_REQUEST["t"];
}

if ($ht) {
  $hl = getshorttownname($ht);
  if (!($hl)) {
    $ht = "";
  }
}

if ($rid) {
  $sql = "
    SELECT r.*, g.name AS group_name
    FROM l_reports r
    LEFT JOIN group_rep g ON g.id = r.group_rep
    WHERE r.rid = $rid
";

} else {
  $sql = "select * from l_reports where ip = '$ip' and rid >0 and time = $t";
}
$result = $dbi->query($sql);

if ($result) {
  $myrow = mysqli_fetch_array($result);
  $s_key = $myrow["key_towns"];
  $rid = $myrow["rid"];
  $ext_rep = "";
  $s_key = getskeytext($s_key, $rid);
  $s_keyl = getskeytext('table', $rid, 1);
  $s_ids = getskeyid($rid);
  $s_spring = $myrow["spring"];
  $ext_rep_id = (int) $s_spring;
  $s_nick = $myrow["nick"];
  $x_nick = $s_nick;
  $s_month = $myrow["month"];
  if ($s_month > "") {
    $s_month = $s_month . "/";
  }  
  $s_year = $myrow["year"];
  $s_rep = $myrow["report"];
  $s_cook = $myrow["cook"];
  $s_score = $myrow["score"];
  if (!($s_score)) {
    $s_score = 0;  
  }
  $s_time = $myrow["time"];
  if (preg_match("/forum\:(\d+)\:(.+)/", $s_cook, $s_match)) {
    if (($ext_rep_id == $s_spring)&&($s_rep == $ext_rep_id)) $ext_rep = $ext_rep_id; // надо проверить собственника отчетов
    $s_nick = "<a href='$forumname/memberlist.php?mode=viewprofile&u=" . $s_match[1] . "'>Участник форума: " . $s_match[2] . "</a>";
    $x_nick = $s_match[2];
    $ss_nick = $s_cook;
  } 
  else {
    $ss_nick = $s_nick;
  }
  
  function seo_season_by_month($m) {
    $m = (int)$m;
    if (in_array($m, [12,1,2])) return "зимой";
    if ($m == 5) return "на майские";
    if (in_array($m, [6,7,8])) return "летом";
    if (in_array($m, [9,10,11])) return "осенью";
    if (in_array($m, [3,4])) return "весной";
    return "";
}

function seo_quality_by_rating($score) {
    if ($score >= 9) return "насыщенный";
    if ($score >= 7) return "интересный";
    if ($score >= 5) return "познавательный";
    return "";
}

function seo_epoch_by_year($year) {
    $y = (int)$year;
    if ($y >= 2019) return "современный маршрут";
    if ($y >= 2013) return "проверенный маршрут";
    if ($y >= 2007) return "классический маршрут";
    return "первые автопутешествия";
}

function seo_seed_from_nick($nick) {
    $nick = mb_strtolower(trim($nick));
    if ($nick === "") return 0;
    return ord(mb_substr($nick, 0, 1)) % 6;
}

  if ($ext_rep) { // новый отчет
    header("Location: https://autotravel.ru/basket_v.php/".$ext_rep."?m=view");
    die();  
  }
  else { // старый отчет
$route = trim($s_key);
$group = trim($myrow['group_name']);
$SEO_FIXED_REPORTS = [
    2624
];
$route = trim($s_key);
$group = trim($myrow['group_name'] ?? "");

// --- ПРОВЕРКА НА ЗАЩИЩЁННЫЙ ОТЧЁТ ---
if (in_array($rid, $SEO_FIXED_REPORTS, true)) {

    $title = "Автопутешествие $route — $group";
    $metadesc = "Автопутешествие по маршруту $route. $group. Личный опыт самостоятельной поездки на машине.";

} else {

    $season  = seo_season_by_month($myrow['month']);
    $quality = seo_quality_by_rating($s_score);
    $epoch   = seo_epoch_by_year($s_year);
    $seed    = seo_seed_from_nick($x_nick);

    // --- TITLE ---
    $title = "Автопутешествие $route";
    if ($group) {
        $title .= " — $group";
    }

    $marker = "";

    if ($quality && $seed % 2 === 0) {
        $marker = $quality;
    } elseif ($season && $seed % 3 === 0) {
        $marker = $season;
    } elseif ($epoch && $seed % 5 === 0) {
        $marker = $epoch;
    }

    if ($marker) {
        $title .= ", $marker";
    }

    $desc = [];
$desc[] = "Автопутешествие по маршруту $route";

/* сезонная фраза */
$season_phrase = "";
if ($season) {
    $season_adj = [
        "зимой"      => "Зимняя",
        "весной"     => "Весенняя",
        "летом"      => "Летняя",
        "осенью"     => "Осенняя",
        "на майские" => "на майские"
    ][$season] ?? "поездка $season";

}

    $season_phrase = $season_adj . " поездка";
}
if ($season === "на майские") {
    $season_phrase = "Поездка на майские";
/* сезон + группа (один раз!) */
if ($season_phrase && $group) {
    $desc[] = "$season_phrase $group";
} elseif ($group) {
    $desc[] = $group;
} elseif ($season_phrase) {
    $desc[] = $season_phrase;
}

/* дополнительные маркеры */
if ($quality) $desc[] = "$quality маршрут";
if ($epoch && $s_year < 2015) $desc[] = $epoch;

$metadesc = implode(". ", $desc) . ". Личный опыт самостоятельного путешествия на машине.";

}


      
      
    include('include/header_bs.php');
    echo '<div class="container-fluid" style="width: 100%">'; //начало раздела 
    // линки на города
    if (!(empty($s_keyl))) {
      echo '<ol class="breadcrumb">';
      echo "<li>Подробнее о городах из отчета:</li>";
      foreach ($s_keyl as $link) {
        echo "<li>".$link."</li>";
      }
      echo '</ol>';
    }  

    //микроразметка страницы
    echo "<span itemprop=\"about\" content=\"Отчеты на сайте По России на авто. Отчет о путешествии по маршурту $s_key.\"></span>";
    //микроразметка статьи 
    echo "<div itemscope itemtype=\"http://schema.org/CreativeWork\">"; //начало микроразметки статьи
    echo "<span itemprop=\"description\" content=\"Отчеты на сайте По России на авто. Отчет о путешествии по маршурту $s_key.\"></span>";
    echo "<span itemprop=\"headline\" content=\"Отчет о путешествии $s_month$s_year по маршурту $s_key.\"></span>";
    echo "<span  itemprop=\"datePublished\" content=\"". strftime("%Y-%m-%d %H:%M:%S",$s_time) . "\"></span>";
    echo "<p class=\"t16 tz tc tm10\">Откуда: <font color=black>$s_spring  </font>Когда: <font color=black>$s_month$s_year </font></p>\n";
    echo "<p class=\"t16 tz tc tm10\">По маршруту: <font color=black>$s_key</font>";
    echo "<p class=\"t16 tz tc tm10\">Рейтинг отчета: <text class=reiting>$s_score</text></p>\n";
    echo "<p class=\"t16 tz tc tm10\">Автор: <span itemprop=\"author\">$s_nick</span></p>\n";
    echo "<div class=\"row tj travell0\">";
    $s_rep = fotoreplace($s_rep);
    // закончено
    // поиск подсвечиваемого 
    //if ($ht) {
    //  $s_rep = preg_replace("/(" . $hl . ")/iu", "<a href=/town.php/$ht><strong>$1</strong></a>", $s_rep);
    //} 
    /* 
     * $s_rep = preg_replace("/<div/", "<div class=\"tj travell0\" ", $s_rep);
     */
    echo "$s_rep"; //собственно сам отчет
    echo "</div></div>\n"; //конец разметки статьи
    echo "<div class=\"row well\" style=\"display:flex\">";
    // устрицам можно удалять каменты !!!
    if ($_user_is_admin == '1') {
      echo "<br><a href=\"/sitemgr.php?admt=delreport&rid=$rid\">Стереть</a><br><br><hr>\n";
    }
    $cook="forum:$forum_user_id:$forum_user_name";
    ////автор может редактировать
    if ($s_cook == $cook) {
      echo "<form class=travell5 name=sedit method=post action=/wr_report.php>";
      echo "<input type=hidden name=rid value=$rid>\n";
      echo "<input type=hidden name=submitback value=edit>\n";
      echo "Этот отчёт \n";
      echo "<input type=submit name=submitt value=\"Редактировать\"></form>\n";
    }
    echo "</div>";
    ?>
    <div class="row well">
      <form class="t16 tz tc" id="sreports" action="/rd_report.php" method="post">
        <input type=hidden name="rtype[]" value="1">
        <input type=hidden name="rtype[]" value="2">
        <input type=hidden name="rtype[]" value="3">
        <input type=hidden name="selmonth" value="3">
        <input type=hidden name="hl" value="<?php echo $hl; ?>">
        <input type=hidden name="selyear" value="1">
        <input type=hidden name="ids" value="">
        <input type=hidden name="author" value="">
        <input type=hidden name=submitt value="Выбрать">
        <p>Выбрать: </p>
        <nav class="navbar"><ul class="nav navbar-nav">
        <li><a class=buttr onclick="fillf('#sreports','author','<?php echo $ss_nick; ?>','ids','');">Другие отчёты того же автора</a></li>
        <?php
        if ($s_ids) {
          ?>
          <li><a class=buttr onclick="fillf('#sreports','ids','<?php echo $s_ids; ?>','author','');">Другие отчёты по городам <?php echo $s_key; ?></a></li>
          <?php
        }
        ?>
        </ul></nav>
      </form>
      <script type="text/javascript" >
        function fillf(f,i,v,i2,v2) {
          $(f + ' input[name="' + i + '"]').val(v);
          $(f + ' input[name="' + i2 + '"]').val(v2);
          $(f).submit();
        }
      </script>
    </div>

    <div class="row well">
    <div class="col-md-6 col-xs-12" style="display:inline-block; margin:auto;">
    <a class="btn buttr btn-default" href="<?php echo $forumname; ?>/viewforum.php?f=12">Обсудить этот отчет на форуме</a>
    <p></p>
    <?php
    //БЛОК ОЦЕНКИ ОТЧЕТА - ОЦЕНКА
    // отзыв об отчете - только зарегистрированные польователи и только про чужие отчеты
    if (($forum_user_id > 0) and ($forum_user_weight>0.5) and ($s_cook <> $cook))  {
      ?>
      <div class="well col-md-12">
      <?php  
      $sqlsc = "select score from score where type = \"report\" and idtype=$rid and cook=\"$cook\"";
      $resultsc = $dbi->query($sqlsc);
      $myrowsc = mysqli_fetch_array($resultsc);
      $score = $myrowsc["score"];
      echo "<b><font color=#00552B>Оцените этот отчет:</font></b><form  name=scoreform>";
      $variants = array("2"=>"Отлично!", "1"=>"Можно прочитать", "0"=>"Прочитал и забыл", "-1" => "Лучше не читать");
      foreach ($variants as $sc=>$comsc){
        echo "<input type=\"radio\"  name=\"scorerep\"";
        if ($sc==$score) {
          echo " checked ";
        }
        echo " value=\"$sc\" >$comsc \n";
      }
      echo "<input type=hidden name=idtype value=\"$rid\">\n";
      echo "</form>"; 
      ?>
      <script type="text/javascript" >
        $("input[name='scorerep']").change(function(){
          var checked = $("input[name='scorerep']:checked").val();
          var cook = $("input[name='cook']").val();
          var idtype = $("input[name='idtype']").val();
          var result = $.post("/upscore.php", { type: "report", score: checked, cook: cook, idtype: idtype });
          result.done(function(scoredata) {
            $('.reiting').text(scoredata);
          });
        });
      </script>
      </div>
      <?php 
    } // конец блока отзыва об отчете
    ?>

    </div>
    <div class="col-md-6 col-xs-12">
      <!-- предложение поделиться отчетом в социальной сети -->
      <script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
      <p class="t12 tl travell0t"> Можно <b>разместить</b> этот отчет в социальных сетях. Таким образом Вы <b>помогаете</b> популяризации сайта. <div class="yashare-auto-init" data-yashareL10n="ru"  data-yashareImage="https://autotravel.ru/jpg/chudick.gif"
        data-yashareType="button" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir,lj,gplus"></div></p>
        <!-- предложение поделиться отчетом в социальной сети -->
    </div>
    </div>  
      
    <?php
  } // старый отчет
  echo "</div>";
} 
else {
  include('include/header_bs.php');
}
include('include/footer_bs.php');
?>
