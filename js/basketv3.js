/******************************************************************
 *  КОРЗИНА — ОСТАВЛЕНО ТОЛЬКО НУЖНОЕ
 *  — перемещение
 *  — вырезать / восстановить
 *  — фиксация изменений
 *  — мини-редактор комментариев
 ******************************************************************/

/* ======================== ВЫРЕЗАТЬ / ВОССТАНОВИТЬ ======================== */

function basketdel(itemname, item) {
  atsaveall();

  var bitem = document.getElementById('b' + itemname);
  var citem = document.getElementById('c' + itemname);
  var xitem = document.getElementById('x' + itemname);

  if (xitem.value === 'ok') {
    bitem.style.textDecoration = 'line-through';
    citem.style.textDecoration = 'line-through';
    xitem.value = 'del';
    item.value = 'Восстановить';
    drawbuttons(0);
  } else {
    bitem.style.textDecoration = '';
    citem.style.textDecoration = '';
    xitem.value = 'ok';
    item.value = 'Вырезать';
    checkbuttons();
  }

  atchange();
}


/* ======================== ЗАГРУЗКА / СОХРАНЕНИЕ ЭЛЕМЕНТОВ ======================== */

function elload(el, id) {
  if (!document.getElementById('b' + id)) return;

  el[0] = document.getElementById('b' + id).innerHTML;
  el[1] = document.getElementById('c' + id).innerHTML;
  el[2] = document.getElementById('x' + id).value;
  el[3] = document.getElementById('oc' + id).value;
  el[4] = document.getElementById('obj' + id).value;
}

function elsave(el, id) {
  if (!document.getElementById('b' + id)) return;

  document.getElementById('b' + id).innerHTML = el[0];
  document.getElementById('c' + id).innerHTML = el[1];
  document.getElementById('x' + id).value = el[2];
  document.getElementById('oc' + id).value = el[3];
  document.getElementById('obj' + id).value = el[4];

  if (el[2] === 'del') {
    document.getElementById('b' + id).style.textDecoration = 'line-through';
    document.getElementById('c' + id).style.textDecoration = 'line-through';
    document.getElementById('d' + id).value = 'Восстановить';
  } else {
    document.getElementById('b' + id).style.textDecoration = '';
    document.getElementById('c' + id).style.textDecoration = '';
    document.getElementById('d' + id).value = 'Вырезать';
  }
}


/* ======================== ПЕРЕМЕЩЕНИЕ ВВЕРХ / ВНИЗ ======================== */

function basketup(itemname) {
  atsaveall();

  var prev = itemname - 1;
  if (!document.getElementById('b' + prev)) return;

  var el0 = [], el1 = [];
  elload(el0, itemname);
  elload(el1, prev);
  elsave(el0, prev);
  elsave(el1, itemname);
}

function basketdown(itemname) {
  atsaveall();

  var next = Number(itemname) + 1;
  if (!document.getElementById('b' + next)) return;

  var el0 = [], el1 = [];
  elload(el0, itemname);
  elload(el1, next);
  elsave(el0, next);
  elsave(el1, itemname);
}


/* ======================== ФИКСАЦИЯ ИЗМЕНЕНИЙ ======================== */

function atchange() {
  var m = document.getElementById("mgrmenu");
  if (m) m.innerHTML = "Имеются несохраненные изменения. Они будут отменены, если не нажать одну из кнопок:";
}

function atsend(frm, val) {
  frm.elements["f"].value = val;
  atsaveall();
  frm.submit();
}


/* ======================== ПРОВЕРКА ВЫРЕЗАННЫХ ЭЛЕМЕНТОВ ======================== */

function checkbuttons() {
  var count = 0;
  var inputs = document.getElementsByTagName("input");

  for (var i = 0; i < inputs.length; i++) {
    if (inputs[i].id.startsWith("x") && inputs[i].value === "del") count++;
  }

  drawbuttons(count === 0 ? 1 : 0);
}


/* ======================== ПЕРЕСТАНОВКА ВЫРЕЗАННОГО ======================== */

function drawbuttons(w) {
  var divs = document.getElementsByTagName("div");

  for (var i = 0; i < divs.length; i++) {
    if (divs[i].id.startsWith("dx")) {
      if (w === 0) {
        let id = divs[i].id.substring(2);
        divs[i].innerHTML =
          "<input type='button' class='atc' onclick=\"pastedel('" + id + "')\" value='Вставить'>";
      } else {
        divs[i].innerHTML = "";
      }
    }
  }
}

function pastedel(ii) {
  var pp = [], cp = 0;
  var pu = [], cu = 0;
  var pd = [], cd = 0;

  var inputs = document.getElementsByTagName("input");

  for (var i = 0; i < inputs.length; i++) {
    var x = inputs[i].id;

    if (x.startsWith('x')) {
      var id = x.substring(1);
      var el = [];
      elload(el, id);

      if (inputs[i].value === 'del') {
        el[2] = 'ok';
        pp[cp++] = el;
      } else {
        if (Number(ii) < Number(id)) pd[cd++] = el;
        else pu[cu++] = el;
      }
    }
  }

  var inputs2 = document.getElementsByTagName("input");

  for (var j = 0; j < inputs2.length; j++) {
    var x2 = inputs2[j].id;

    if (x2.startsWith('x')) {
      var id2 = x2.substring(1);
      var target;

      if (Number(id2) < cu) target = pu[id2];
      else {
        var idx = Number(id2) - cu;
        if (idx < cp) target = pp[idx];
        else target = pd[idx - cp];
      }

      if (target) elsave(target, id2);

      document.getElementById('b' + id2).style.textDecoration = '';
      document.getElementById('c' + id2).style.textDecoration = '';
      document.getElementById('d' + id2).value = 'Вырезать';
    }
  }

  drawbuttons(1);
}


/******************************************************************
 *  МИНИ-РЕДАКТОР (B / I / U)
 ******************************************************************/

var currentEditorId = null;

// старт редактирования
function atedithtml(itemname) {
  var el = document.getElementById(itemname);
  if (!el) return;

  if (currentEditorId && currentEditorId !== itemname) {
    finishEdit(currentEditorId);
  }

  currentEditorId = itemname;

  el.contentEditable = 'true';
  el.classList.add('basket-comment--editing');
  el.focus();

  var toolbar = document.getElementById('ed' + itemname);
  if (toolbar) {
    toolbar.innerHTML =
      "<button type='button' class='atb' data-cmd='bold'>B</button>" +
      "<button type='button' class='atb' data-cmd='italic'>I</button>" +
      "<button type='button' class='atb' data-cmd='underline'>U</button>";
  }
}

// выполнение команд
document.addEventListener("click", function (e) {
  if (!e.target.closest(".basket-edit-toolbar")) return;

  let btn = e.target.closest("button[data-cmd]");
  if (!btn) return;

  let cmd = btn.getAttribute("data-cmd");
  if (!cmd || !currentEditorId) return;

  document.execCommand(cmd, false, null);
  document.getElementById(currentEditorId).focus();

  e.stopPropagation();
}, true);

// завершение
function finishEdit(itemname) {
  var el = document.getElementById(itemname);
  if (!el) return;

  el.contentEditable = 'false';
  el.classList.remove('basket-comment--editing');

  var nTxt = el.innerHTML.replace(/<script[\s\S]*?<\/script>/gi, '');

  var hidden = document.getElementById('o' + itemname);
  if (hidden) {
    hidden.value = nTxt
      .replace(/'/g, "&quot;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;");
  }

  var toolbar = document.getElementById('ed' + itemname);
  if (toolbar) toolbar.innerHTML = "";

  atchange();
}

// blur → завершить редактирование
function atblur(itemname) {
  finishEdit(itemname);
}

// сохранить все
function atsaveall() {
  var divs = document.getElementsByTagName("div");

  for (var i = 0; i < divs.length; i++) {
    if (divs[i].contentEditable === 'true') finishEdit(divs[i].id);
  }
}

// мобильная защита от закрытия редактора при нажатии на кнопки
document.addEventListener(
  "touchstart",
  function (e) {
    if (e.target.closest(".basket-edit-toolbar")) {
      e.stopImmediatePropagation();
    }
  }, 
  true
);

async function deletePoint(pointId) {
  if (!confirm('Удалить строку из поездки?')) return;

  const body = new URLSearchParams({
    action: 'delete_point',
    basket_id: String(window.BASKET_ID),
    point_id: String(pointId)
  }).toString();

  const r = await fetch('/ajax/basket_api.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body
  });

  const data = await r.json();

  if (data && data.ok) {
    // убираем строку из DOM
    const el = document.querySelector('[data-point-id="' + pointId + '"]');
    if (el) el.remove();
  } else {
    alert('Не удалось удалить: ' + (data && data.error ? data.error : 'ошибка'));
  }
}
