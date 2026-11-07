<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>FAQ диагностика</title>
<style>
body { font-family: sans-serif; padding: 16px; }
.info { background: #eef; border: 1px solid #aaf; padding: 8px; margin: 8px 0; font-size: 13px; }
.ok  { background: #efe; border-color: #afa; }
.err { background: #fee; border-color: #faa; }

/* --- тест 1: чистый details/summary --- */
details.t1 { border: 2px solid #999; margin: 10px 0; padding: 8px; }
details.t1 summary { cursor: pointer; font-weight: bold; }

/* --- тест 2: display:none через CSS --- */
.hidden-by-css { display: none; }
.show-block { display: block; }

/* --- тест 3: ваш текущий CSS из travel_new.css --- */
.faq-details > summary::-webkit-details-marker { display: none; }
.faq-header {
    font-size: 1.1em;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    background: none;
    border: none;
    padding: 0;
    width: 100%;
    text-align: left;
}
.faq-details[open] .faq-chevron { transform: rotate(90deg); }
.faq-chevron { display: inline-block; transition: transform .25s; }
</style>
</head>
<body>

<h2>FAQ диагностика</h2>

<!-- ======= БЛОК 1: инфо о браузере ======= -->
<div class="info" id="browser-info">JS не сработал</div>

<!-- ======= БЛОК 2: details/summary нативный ======= -->
<h3>Тест 1: нативный &lt;details&gt; (без JS и CSS)</h3>
<div class="info">Должен быть закрыт. Нажатие открывает.</div>
<details class="t1">
  <summary>▶ Нажмите чтобы открыть</summary>
  <p>✅ details/summary работает на этом устройстве!</p>
</details>

<!-- ======= БЛОК 3: display:none через CSS ======= -->
<h3>Тест 2: CSS display:none</h3>
<div class="info">Строка ниже должна быть СКРЫТА:</div>
<div class="hidden-by-css" id="css-hidden">❌ CSS display:none НЕ работает — этот текст виден</div>
<div class="info" id="css-check">проверяется...</div>

<!-- ======= БЛОК 4: JS onclick на div ======= -->
<h3>Тест 3: onclick на &lt;div&gt;</h3>
<div class="info">Нажмите на серый блок:</div>
<div style="background:#ddd;padding:10px;cursor:pointer;border:1px solid #999"
     onclick="document.getElementById('div-result').textContent='✅ onclick на div сработал'">
  Нажать здесь
</div>
<div id="div-result" class="info">ожидание нажатия...</div>

<!-- ======= БЛОК 5: ваш FAQ как есть сейчас ======= -->
<h3>Тест 4: ваш FAQ (details/summary + CSS классы)</h3>
<div class="info">Должен быть закрыт. Нажатие открывает.</div>
<section class="faq-ourtrip">
  <details class="faq-details">
    <summary class="faq-header">
      <span class="faq-chevron">▶</span>
      <span>Частые вопросы (тест)</span>
    </summary>
    <div class="faq-content">
      <p>✅ FAQ открылся!</p>
    </div>
  </details>
</section>

<script>
// инфо о браузере
var bi = document.getElementById('browser-info');
bi.textContent = 'UA: ' + navigator.userAgent;
bi.className = 'info ok';

// проверка CSS display:none
var el = document.getElementById('css-hidden');
var cc = document.getElementById('css-check');
var computed = window.getComputedStyle(el).display;
if (computed === 'none') {
    cc.textContent = '✅ CSS display:none работает (computed: none)';
    cc.className = 'info ok';
} else {
    cc.textContent = '❌ CSS display:none НЕ работает! computed display = ' + computed;
    cc.className = 'info err';
}
</script>

</body>
</html>
