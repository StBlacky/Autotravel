<?php
/**********************************************************************
 * trace.php — страница конкретной трассы или список всех трасс
 * Каркас страницы统一:
 *   head00.php -> header_bs.php -> container -> footer_bs.php
 **********************************************************************/

include_once('include/func_bs.php');
include_once('include/head00.php');
$_totop=1;
$bootstrap = 1;
$colorbox  = 1;


/* ============================================================
 * ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ
 * ============================================================ */

/*
 * get_trace_state($trace_id, $km_raw)
 * Находит последнее сообщение о состоянии трассы на конкретном километре
 */
function get_trace_state($trace_id, $km_raw) {
    global $dbi;

    $state = [
        'class'    => 'good',
        'title'    => 'Нет жалоб, скорее всего всё в порядке',
        'age_days' => 99999,
        'tsid'     => 0,
        'tsids'    => ''
    ];

    $trace_id = (int)$trace_id;
    $km_raw   = (int)$km_raw;

    /* последний отзыв для этого км */
    $sql_state = "
        SELECT
            ts.ido AS tsid,
            ts.datea AS dat,
            t_s.id   AS sid,
            t_r.id   AS rid,
            t_s.descr AS markv,
            t_r.descr AS markg
        FROM otklikt ts
        JOIN t_speed t_s ON t_s.id = ts.markv
        JOIN t_road  t_r ON t_r.id = ts.markg
        WHERE ts.ido >= 0
          AND ts.idtr = {$trace_id}
          AND ts.kend >= {$km_raw}
          AND ts.kbeg <= {$km_raw}
        ORDER BY ts.time DESC
        LIMIT 1
    ";

    $res_state = $dbi->query($sql_state);

    if ($res_state && $state_row = mysqli_fetch_assoc($res_state)) {

        $rid      = (int)$state_row['rid'];
        $sid      = (int)$state_row['sid'];
        $date_raw = trim($state_row['dat']);

        $days = 99999;
        if (preg_match('/^(\d{1,2})\.(\d{4})$/', $date_raw, $m)) {
            $dat = mktime(0,0,0,(int)$m[1],1,(int)$m[2]);
            $days = floor((time() - $dat) / 86400);
        }

        $title = $state_row['markg'] . ', ' . $state_row['markv'];

        if     ($days > 365)               $class = 'old';
        elseif ($rid == 1 || $sid <= 1)    $class = 'bad';
        elseif ($rid == 2 || $sid <= 3)    $class = 'mid';
        else                                $class = 'good';

        $state = [
            'class'    => $class,
            'title'    => $title,
            'age_days' => $days,
            'tsid'     => (int)$state_row['tsid'],
            'tsids'    => ''
        ];
    }

    if ($res_state) mysqli_free_result($res_state);

    /* список ВСЕХ отзывов по этому участку */
    $ids_res = $dbi->query("
        SELECT ido
        FROM otklikt
        WHERE idtr = {$trace_id}
          AND ido >= 0
          AND kend >= {$km_raw}
          AND kbeg <= {$km_raw}
    ");

    $all_ids = [];
    if ($ids_res) {
        while ($r = mysqli_fetch_assoc($ids_res)) {
            $all_ids[] = (int)$r['ido'];
        }
        mysqli_free_result($ids_res);
    }

    $state['tsids'] = implode(',', $all_ids);

    return $state;
}


/*
 * draw_trace_state_box($state, $trace_id, $km_raw)
 * Квадратик состояния трассы
 */
function draw_trace_state_box($state, $trace_id, $km_raw) {

    $trace_id = (int)$trace_id;
    $km_raw   = (int)$km_raw;

    $class = $state['class'] ?? 'good';
    $title = htmlspecialchars($state['title'] ?? 'Состояние неизвестно', ENT_QUOTES);

    $tsids = $state['tsids'] ?? '';
    $tsids_attr = $tsids ? " data-tsids=\",{$tsids},\" " : "";

    return "
      <div class='trace-state-box {$class}'
           title='{$title}'
           data-trace='{$trace_id}'
           data-km='{$km_raw}'{$tsids_attr}
           role='button'
           tabindex='0'></div>
    ";
}


/*
 * draw_trace_node($node)
 * Один узел трассы (км, точка, название, иконки, перекрёстки)
 */
function draw_trace_node($node) {

    $km          = (int)($node['km'] ?? 0);
    $town_id     = (int)($node['town_id'] ?? 0);
    $town_name   = htmlspecialchars($node['town_name'] ?? '');
    $town_hide   = (int)($node['town_hide'] ?? 0);
    $state_box   = $node['state_box'] ?? '';
    $icons_html  = $node['icons_html'] ?? '';
    $cross_html  = $node['cross_html'] ?? '';

    $node_class = 'trace-node';
    if ($town_hide == 2) $node_class .= ' small';
    if ($town_hide == 1) $node_class .= ' cross';

    return "
        <div class='{$node_class}' itemscope itemtype='https://schema.org/Place'>
            <div class='km-col'>
                {$km} км
                {$state_box}
            </div>

            <div class='point-col'>
                <div class='point-dot'></div>
                <div class='point-line'></div>
            </div>

            <div class='town-col'>
                <a href='/town.php/{$town_id}' itemprop='url'>
                    <span itemprop='name'>{$town_name}</span>
                </a>
                {$icons_html}
                {$cross_html}
            </div>
        </div>
    ";
}

/* ============================================================
 * ОПРЕДЕЛЯЕМ ID ТРАССЫ
 * ============================================================ */

$reverse = 0;

if (preg_match("/^\/(\d+)$/", $_pin, $m)) {
    $trace_id = (int)$m[1];

} elseif (preg_match("/^\/(\d+)\/(.+)$/", $_pin, $m)) {
    $trace_id = (int)$m[1];
    $reverse  = 1;

} else {
    $trace_id = (int) mysafestring($_REQUEST['town']);
}

/* ============================================================
 * СПИСОК ТИПОВ ТРАСС (нужен в любом режиме)
 * ============================================================ */

$trace_type = [];
$res_types = $dbi->query("SELECT * FROM trace_t ORDER BY id_order");

if ($res_types) {
    while ($type_row = mysqli_fetch_assoc($res_types)) {
        $trace_type[] = $type_row;
    }
    mysqli_free_result($res_types);
}

/* ============================================================
   SEO для страницы трассы
   ============================================================ */
if ($trace_id) {
$sql_trace = "
    SELECT name, of_name, comment, state
    FROM trace
    WHERE id = '$trace_id'
";

$res_trace = $dbi->query($sql_trace);

if (!$res_trace || !($trace_row = mysqli_fetch_assoc($res_trace))) {
    echo "<p>Неверная ссылка на трассу.</p>";
    echo "</div>";
    include('include/footer_bs.php');
    exit;
}

mysqli_free_result($res_trace);

$trace_name        = $trace_row['name'];
$trace_of_name     = $trace_row['of_name'];
$trace_desc_html   = $trace_row['comment'];
$trace_state_code  = $trace_row['state'];

// Человеческое имя трассы
$fullname = $trace_of_name
    ? trim($trace_of_name . ' ' . $trace_name)
    : $trace_name;

// H1 — простой и стабильный
$h1 = "<h1>Трасса $fullname</h1>";

// TITLE + DESCRIPTION — уникальные и практичные
if ($first_town_name && $last_town_name) {

    // Есть направление
    $title = "Трасса $fullname: $first_town_name — $last_town_name";

    $metadesc = "Трасса $fullname ($first_town_name — $last_town_name): маршрут по регионам, города, состояние дороги, отзывы и полезная информация для автопутешествий.";

} else {

    // Нет чёткого направления
    $title = "Трасса $fullname — маршрут и состояние дороги";

    $metadesc = "Трасса $fullname: маршрут, города, состояние дороги, отзывы водителей и полезная информация для поездок на автомобиле.";
}

// Keywords — без фанатизма, чисто технически
$metatags = "трасса $fullname, маршрут $fullname, состояние дороги $fullname, автопутешествия";

// Индексация
$robots = 'index,follow';

// Канонический URL (важно!)
$canon = "/trace.php/$trace_id";
} else {
    $title = 'Автомобильные трассы России и мира — список дорог, маршруты, состояние';
    $metadesc = 'Полный список автомобильных трасс России и зарубежных стран: федеральные и региональные дороги, европейские маршруты, состояние покрытия, города и отзывы водителей.';
    $robots = 'index,follow';
    $canon = '/trace.php/';
}
/* ============================================================
   конец SEO-блока
   ============================================================ */


/* ============================================================
 * ОБЩИЙ HEADER И КОНТЕЙНЕР (ВСЕГДА)
 * ============================================================ */

include('include/header_bs.php');
echo "<div class='container-fluid'>";

/* ============================================================
 * РЕЖИМ: СПИСОК ВСЕХ ТРАСС
 * ============================================================ */

if (!$trace_id) {
    include 'include/trace_list.php';
    echo "</div>";
    include('include/footer_bs.php');
    exit;
}

/* ============================================================
 * РЕЖИМ: ОДНА КОНКРЕТНАЯ ТРАССА
 * ============================================================ */


/* --- блок состояния трассы --- */
if ($trace_state_code == '') {

    $trace_state_block_html = "
    <p class='note-block'>
        Чтобы посмотреть отзывы по конкретному участку, нажмите на маленькие цветные квадратики
        в линии трассы — откроется карточка с описанием.
        Добавить свой отзыв можно там же.
        <br>
        Актуальные отметки о состоянии $trace_of_name — в разделе
        <a href='/tracestate.php/$trace_id'>состояния трассы</a>.
        Для обсуждений подойдёт раздел
        <a target=\"_blank\" href=\"$forumname/viewforum.php?f=10\">форума</a>.
    </p>";

} else {

    $trace_state_block_html = "
    <p class='note-block'>
        Нажмите на цветной квадрат рядом с нужным километром — откроется карточка с отзывами
        и кнопкой «Добавить». Участок выбирается прямо в форме.
        <br>
        Краткая информация по $trace_of_name — в разделе
        <a href='/tracestate.php/$trace_id'>состояния трассы</a>.
        Для обсуждений доступна тема на форуме:
        <a target=\"_blank\" href=\"$forumname/viewtopic.php?t=$trace_state_code\">
            обсуждение маршрута
        </a>.
    </p>";
}


/* --- новости --- */
$news_html = '';
$res_news = $dbi->query("SELECT date FROM news WHERE exciting='r$trace_id' LIMIT 1");

if ($res_news && mysqli_fetch_assoc($res_news)) {
    $news_html = "<noindex><a href='/news.php?tr=$trace_id'>Архив новостей</a> по трассе $trace_name</noindex>";
}
if ($res_news) mysqli_free_result($res_news);

/* --- список городов трассы --- */

$order = $reverse ? "DESC" : "ASC";

$sql_towns = "
    SELECT 
        t.id   AS town_id,
        t.name AS town_name,
        t.hide AS town_hide,
        tr.cw_far AS km_raw
    FROM towns t
    JOIN far_tr tr ON tr.town = t.id
    WHERE tr.trace = '$trace_id'
    ORDER BY tr.cw_far $order
";

$res_towns = $dbi->query($sql_towns);

$first_town_name = '';
$last_town_name  = '';

if ($res_towns && mysqli_num_rows($res_towns) > 0) {
    mysqli_data_seek($res_towns, 0);
    $first_town_name = mysqli_fetch_assoc($res_towns)['town_name'];

    mysqli_data_seek($res_towns, mysqli_num_rows($res_towns) - 1);
    $last_town_name = mysqli_fetch_assoc($res_towns)['town_name'];

    mysqli_data_seek($res_towns, 0);
}


echo "
<ol class='breadcrumb'>
    <li><a href='/trace.php'>Список дорог России и мира</a></li>
</ol>";

echo $h1;

/* --- подзаголовок маршрута --- */

$reverse_url = "/trace.php/$trace_id" . ($reverse ? "" : "/r");

if ($reverse) {
    $label_from = $last_town_name;
    $label_to   = $first_town_name;
    $arrow_icon = "&larr;";
} else {
    $label_from = $first_town_name;
    $label_to   = $last_town_name;
    $arrow_icon = "&rarr;";
}

if ($first_town_name && $last_town_name) {
echo "
<div class='trace-subinfo' itemscope itemtype='https://schema.org/Route'>
  <meta itemprop='name' content='Трасса ".htmlspecialchars($fullname,ENT_QUOTES)."'>
  Маршрут соединяет города 
  <strong><span itemprop='fromLocation'>".htmlspecialchars($label_from)."</span></strong>
  и <strong><span itemprop='toLocation'>".htmlspecialchars($label_to)."</span></strong>.
  <button class='btn-outline' onclick=\"location.href='{$reverse_url}'\">
    ↺ направление $arrow_icon
  </button>
</div>";
}

echo $news_html;
echo $trace_state_block_html;
echo $trace_desc_html;

/* ============================================================
 * ЛИНИЯ ТРАССЫ
 * ============================================================ */

$trtown_html = "<div class='trace-line'>";

$max_km = 0;

if ($res_towns) {

    while ($town_row = mysqli_fetch_assoc($res_towns)) {

        $town_id   = $town_row['town_id'];
        $town_name = $town_row['town_name'];
        $town_hide = $town_row['town_hide'];
        $km_raw    = $town_row['km_raw'];

        if ($reverse) {
            if (!$max_km) $max_km = $km_raw;
            $km_display = $max_km - $km_raw;
        } else {
            $km_display = $km_raw;
        }

        /* --- состояние --- */
        $state_data = get_trace_state($trace_id, $km_display);
        $state_box  = draw_trace_state_box($state_data, $trace_id, $km_raw);

        /* --- иконки --- */
        $icons_html = get_icons_by_location($town_id, 0);

        /* --- перекрёстки --- */
        $cross_html = '';
        if ($town_id != 777) {
            $sql_cross = "
                SELECT DISTINCT t.id, t.name
                FROM trace t
                JOIN far_tr ft ON ft.trace = t.id
                WHERE ft.town = '$town_id' AND t.id <> '$trace_id'
            ";

            $res_cross = $dbi->query($sql_cross);
            if ($res_cross) {
                $cross_links = [];
                while ($cross_row = mysqli_fetch_assoc($res_cross)) {
                    $cross_links[] = "<a href='/trace.php/{$cross_row['id']}' class='cross-trace'>".
                                      htmlspecialchars($cross_row['name'])."</a>";
                }
                if ($cross_links) {
                    $cross_html = "<div class='cross-box'>".implode('', $cross_links)."</div>";
                }
                mysqli_free_result($res_cross);
            }
        }

        $trtown_html .= draw_trace_node([
            'km'         => $km_display,
            'town_id'    => $town_id,
            'town_name'  => $town_name,
            'town_hide'  => $town_hide,
            'state_box'  => $state_box,
            'icons_html' => $icons_html,
            'cross_html' => $cross_html
        ]);
    }

    mysqli_free_result($res_towns);
}

$trtown_html .= "</div>";

/* --- вывод линии --- */

echo "<div id='trace-layout' class='trace-layout'>
        <div id='trace-main'>
          $trtown_html
        </div>
        <aside id='trace-side' class='trace-side'></aside>
      </div>";

/* --- легенда --- */

if ($first_town_name && $last_town_name) {
echo "
<div class='trace-legend'>
  <div class='trace-legend-title'>🛈 Легенда</div>
  <div class='trace-legend-body'>
    <span class='good'><i></i> всё хорошо</span>
    <span class='mid'><i></i> средне</span>
    <span class='bad'><i></i> проблемы</span>
    <span class='old'><i></i> устарело</span>
    <span class='dot main'><b class='dot-sample main'></b> обычный город</span>
    <span class='dot small'><b class='dot-sample small'></b> малый пункт</span>
    <span class='dot cross'><b class='dot-sample cross'></b> перекрёсток</span>
  </div>
</div>
";
}

echo "</div>"; // container-fluid
?>

<script>
(function(){
  function kmFormat(k){
    try { return new Intl.NumberFormat('ru-RU').format(k); }
    catch(e){ return k; }
  }

  let activeBox = null;

  document.addEventListener('click', async (e)=>{

    const box   = e.target.closest('.trace-state-box');
    const panel = e.target.closest('.trace-inline-panel');
    const oldPanel = document.querySelector('.trace-inline-panel');

    /* ===== КЛИК ВНЕ КВАДРАТИКА И ВНЕ ПАНЕЛИ — ЗАКРЫТЬ ===== */
    if (!box && !panel) {
      if (activeBox) activeBox.classList.remove('is-active');
      activeBox = null;
      if (oldPanel) oldPanel.remove();
      return;
    }

    /* ===== КЛИК ВНУТРИ ПАНЕЛИ — НЕ ЗАКРЫВАТЬ ===== */
    if (!box && panel) {
      return;
    }

    /* ===== ЕСЛИ ПОПАЛИ СЮДА — БЫЛ КЛИК ПО КВАДРАТИКУ ===== */

    // повторный клик по активному квадратику — закрыть
    if (box === activeBox) {
      box.classList.remove('is-active');
      activeBox = null;
      if (oldPanel) oldPanel.remove();
      return;
    }

    // удалить старую активность
    document.querySelectorAll('.trace-state-box.is-active')
      .forEach(el => el.classList.remove('is-active'));
    if (oldPanel) oldPanel.remove();

    // активируем новое
    box.classList.add('is-active');
    activeBox = box;

    const trace = box.dataset.trace;
    const km    = box.dataset.km;
    const node  = box.closest('.trace-node');

    // создаём панель
    const panelDiv = document.createElement('div');
    panelDiv.className = 'trace-inline-panel';
    panelDiv.innerHTML = `
      <div class='ts-meta'>Участок: ~ ${kmFormat(km)} км</div>
      <div>Загружаю данные...</div>
    `;

    // вставляем панель в town-col
    const townCol = node.querySelector('.town-col');
    if (townCol) townCol.appendChild(panelDiv);
    else node.appendChild(panelDiv);

    // загрузка данных AJAX
    try {
      const rsp = await fetch('/ajax/get_tracestate.php?trace=' + trace + '&km=' + km, {
        credentials: 'same-origin'
      });
      panelDiv.innerHTML = await rsp.text();
    } catch(err){
      panelDiv.innerHTML = '<div class="ts-empty">Не удалось загрузить данные.</div>';
    }
  });
})();


/* ========== АВТО-ОТКРЫТИЕ ПО ЯКОРЮ #ts-XXXX ========== */
(function(){

  const hash = window.location.hash;
  if (!hash || !hash.startsWith("#ts-")) return;

  const tsId = hash.substring(4);

  function scrollToReview() {
      const el = document.querySelector(hash);
      if (!el) return false;

      el.scrollIntoView({behavior:'smooth', block:'center'});
      el.classList.add('ts-highlight');
      setTimeout(() => el.classList.remove('ts-highlight'), 2000);
      return true;
  }

  function init() {
      if (scrollToReview()) return;

      const box = document.querySelector(`.trace-state-box[data-tsids*=",${tsId},"]`);
      if (box) box.click();

      const obs = new MutationObserver(() => {
          if (scrollToReview()) obs.disconnect();
      });

      obs.observe(document.body, {childList:true, subtree:true});
  }

  if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', init);
  } else {
      init();
  }

})();

/* ========== СОХРАНЕНИЕ ОТЗЫВА В БАЗУ ========== */
function tsSave(form){
    let fd = new FormData(form);

    fetch('/ajax/save_tracestate.php', {
        method:'POST',
        body:fd,
        credentials:'same-origin'
    })
    .then(r=>r.json())
    .then(d=>{
        if(d.success){
            const box = document.querySelector('.trace-state-box.is-active');
            if (box) {
                const trace = box.dataset.trace;
                const km    = box.dataset.km;

                fetch('/ajax/get_tracestate.php?trace='+trace+'&km='+km)
                    .then(r=>r.text())
                    .then(html=>{
                        const panel = document.querySelector('.trace-inline-panel');
                        if (panel) panel.innerHTML = html;
                    });

            } else {
                location.reload();
            }
        } else {
            alert(d.error || 'Ошибка сохранения');
        }
    })
    .catch(()=>alert('Ошибка соединения'));

    return false;
}
</script>


<?php 

include('include/footer_bs.php');?>

