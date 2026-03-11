// ===============================
//      basket_v.js
// ===============================

window.BASKET_ID = window.BASKET_ID || 0;
window.IS_OWNER  = window.IS_OWNER  || 0;

// буфер вырезанных строк (ID точек)
window.CUT_POINTS = [];

// ------------------------------
// Заглушка для onfocus/oninput
// ------------------------------
function saveSelection() {}

// ------------------------------
// Общий AJAX helper
// ------------------------------
async function api(action, data = {}) {
    data.action = action;

    const res = await fetch("/ajax/basket_api.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams(data)
    });

    let json;
    try {
        json = await res.json();
    } catch (e) {
        alert("Ошибка сервера");
        return null;
    }

    if (json.error) {
        alert(json.error);
        return null;
    }

    return json;
}

// ------------------------------
// Форматирование текста
// ------------------------------
function applyFormat(cmd, ev) {
    if (ev && ev.preventDefault) ev.preventDefault();
    document.execCommand(cmd, false, null);
}

// ------------------------------
// Сохранение текста строки
// ------------------------------
async function autosaveText(el) {
    const html = el.innerHTML;

    await api("save_text", {
        basket_id: window.BASKET_ID,
        point_id: el.dataset.id,
        text: html
    });
}

// ------------------------------
// Автосохранение названия поездки
// ------------------------------
function autosaveTripTitle() {
    const input = document.getElementById("trip-title");
    if (!input) return;

    const title = input.value;
    api("save_trip_title", {
        basket_id: window.BASKET_ID,
        title
    });
}

// ------------------------------
// Автосохранение названия блока
// ------------------------------
function autosaveBlockTitle(input) {
    api("save_block_title", {
        basket_id: window.BASKET_ID,
        block_id: input.dataset.blockId,
        title: input.value
    });
}

// ------------------------------
// Блоки: свернуть / развернуть
// ------------------------------
function toggleBlock(el) {
    const block = el.closest(".basket-block");
    if (!block) return;

    const body = block.querySelector(".block-body");
    if (!body) return;

    const collapsed = body.classList.toggle("collapsed");
    el.textContent = collapsed ? "▶" : "▼";
}

// ------------------------------
// Объекты: сворачивание extra
// ------------------------------
function toggleObject(el) {
    const obj = el.closest(".basket-object");
    if (!obj) return;

    const extra = obj.querySelector(".object-extra");
    if (!extra) return;

    const collapsed = extra.classList.toggle("collapsed");
    el.textContent = collapsed ? "▶" : "▼";
}

// ------------------------------
// Создать блок
// ------------------------------
async function createBlock() {
    const json = await api("create_block", {
        basket_id: window.BASKET_ID
    });

    if (json) location.reload();
}

// ------------------------------
// Удалить блок
// ------------------------------
async function deleteBlock(block_id) {
    if (!confirm("Удалить блок и все его строки?")) return;

    const json = await api("delete_block", {
        basket_id: window.BASKET_ID,
        block_id
    });

    if (json) location.reload();
}

// ------------------------------
// Создать текстовую строку (COMMENT)
// ------------------------------
async function doComment(block_id, ev) {
    if (ev && ev.stopPropagation) ev.stopPropagation();

    const json = await api("create_comment", {
        basket_id: window.BASKET_ID,
        block_id
    });

    // ожидаем { ok:1, point_id: N }
    if (json && json.point_id) {
        try {
            localStorage.setItem("basket_new_point", String(json.point_id));
        } catch (e) {
            // если localStorage недоступен — просто переживём без подсветки
        }
    }

    if (json) location.reload();
}

// =============================================================
//      SNAPSHOTS (ЧЕРНОВИКИ)
// =============================================================
async function saveSnapshot() {
    const json = await api("save_snapshot", { basket_id: window.BASKET_ID });
    if (json) location.reload();
}

async function deleteSnapshot(id) {
    if (!confirm("Удалить этот черновик?")) return;

    const json = await api("delete_snapshot", {
        basket_id: window.BASKET_ID,
        snapshot_id: id
    });

    if (json) location.reload();
}

async function restoreSnapshot(id) {
    if (!confirm("Восстановить черновик и потерять текущие изменения?")) return;

    const json = await api("restore_snapshot", {
        basket_id: window.BASKET_ID,
        snapshot_id: id
    });

    if (json) location.reload();
}

// =============================================================
//      CUT / PASTE
// =============================================================

// Вырезать / снять вырезание одной строки
function cutPoint(pointId, btn) {
    pointId = parseInt(pointId, 10);
    if (!pointId) return;

    const idx = window.CUT_POINTS.indexOf(pointId);

    if (idx === -1) {
        // добавить в буфер
        window.CUT_POINTS.push(pointId);
    } else {
        // повторный клик — убрать из буфера
        window.CUT_POINTS.splice(idx, 1);
    }

    // визуальное состояние
    const row = document.querySelector('.basket-point[data-id="' + pointId + '"]');
    if (row) {
        if (idx === -1) {
            row.classList.add("basket-cut");
        } else {
            row.classList.remove("basket-cut");
        }
    }
}

// Вставить вырезанные строки после конкретной строки
async function pasteAfter(pointId, btn, ev) {
    if (ev && ev.stopPropagation) ev.stopPropagation();

    if (!window.CUT_POINTS || window.CUT_POINTS.length === 0) {
        alert("Нет вырезанных строк для вставки.");
        return;
    }

    pointId = parseInt(pointId, 10);
    if (!pointId) return;

    const row = document.querySelector('.basket-point[data-id="' + pointId + '"]');
    if (!row) return;

    const area = row.closest(".sortable-area");
    if (!area) return;

    const blockId = parseInt(area.dataset.blockId || "0", 10);
    if (!blockId) return;

    const idsStr = window.CUT_POINTS.join(",");

    const json = await api("paste_points", {
        basket_id: window.BASKET_ID,
        target_point_id: pointId,
        target_block_id: blockId,
        ids: idsStr
    });

    if (!json) return;

    // очищаем состояние
    window.CUT_POINTS = [];
    document.querySelectorAll(".basket-cut").forEach(el => el.classList.remove("basket-cut"));

    location.reload();
}

// Вставить вырезанные строки в конец блока
async function pasteIntoBlock(blockId, ev) {
    if (ev && ev.stopPropagation) ev.stopPropagation();

    if (!window.CUT_POINTS || window.CUT_POINTS.length === 0) {
        alert("Нет вырезанных строк для вставки.");
        return;
    }

    blockId = parseInt(blockId, 10);
    if (!blockId) return;

    const idsStr = window.CUT_POINTS.join(",");

    const json = await api("paste_points", {
        basket_id: window.BASKET_ID,
        target_point_id: 0,
        target_block_id: blockId,
        ids: idsStr
    });

    if (!json) return;

    window.CUT_POINTS = [];
    document.querySelectorAll(".basket-cut").forEach(el => el.classList.remove("basket-cut"));

    location.reload();
}

// =============================================================
//      DRAG & DROP
// =============================================================
document.addEventListener("DOMContentLoaded", function () {

    if (!window.IS_OWNER) return; // сортировка только владельцу

    // --- 1. Прокрутка к только что созданной строке ---
    try {
        const newId = localStorage.getItem("basket_new_point");
        if (newId) {
            const el = document.querySelector('.basket-point[data-id="' + newId + '"]');
            if (el) {
                el.scrollIntoView({ behavior: "smooth", block: "center" });
                el.classList.add("highlight-new");
            }
            localStorage.removeItem("basket_new_point");
        }
    } catch (e) {
        // если localStorage недоступен — просто игнорируем
    }

    // --- 2. Сортировка блоков ---
    const blocksContainer = document.getElementById("blocks");
    if (blocksContainer && typeof Sortable !== "undefined") {
        new Sortable(blocksContainer, {
            animation: 150,
            handle: ".block-drag-handle",
            draggable: ".basket-block",
            ghostClass: "sortable-ghost",
            fallbackOnBody: true,
            swapThreshold: 0.65,

            onEnd: async function () {
                const ids = Array.from(
                    document.querySelectorAll(".basket-block")
                ).map(b => b.dataset.blockId).join(",");

                await api("reorder_blocks", {
                    basket_id: window.BASKET_ID,
                    order: ids
                });
            }
        });
    }

    // --- 3. Сортировка строк + перенос между блоками ---
    if (typeof Sortable !== "undefined") {
        document.querySelectorAll(".sortable-area").forEach(area => {

            new Sortable(area, {
                group: "points",
                animation: 150,
                handle: ".drag-handle",
                draggable: ".basket-point",
                ghostClass: "sortable-ghost",
                swapThreshold: 0.65,

                onAdd: async function (evt) {
                    const el = evt.item;
                    const point_id = el.dataset.id;
                    const new_block_id = evt.to.dataset.blockId;

                    // сменили блок для точки
                    await api("move_point", {
                        basket_id: window.BASKET_ID,
                        point_id,
                        new_block_id
                    });

                    // пересортировка в новом блоке
                    const ids_new = Array.from(evt.to.querySelectorAll(".basket-point"))
                        .map(e => e.dataset.id)
                        .join(",");

                    await api("reorder_points", {
                        basket_id: window.BASKET_ID,
                        block_id: new_block_id,
                        order: ids_new
                    });

                    // пересортировка в старом блоке
                    const old_area = evt.from;
                    const old_block_id = old_area.dataset.blockId;

                    const ids_old = Array.from(old_area.querySelectorAll(".basket-point"))
                        .map(e => e.dataset.id)
                        .join(",");

                    await api("reorder_points", {
                        basket_id: window.BASKET_ID,
                        block_id: old_block_id,
                        order: ids_old
                    });
                },

                onUpdate: async function (evt) {
                    const area = evt.to;
                    const block_id = area.dataset.blockId;

                    const ids = Array.from(area.querySelectorAll(".basket-point"))
                        .map(e => e.dataset.id)
                        .join(",");

                    await api("reorder_points", {
                        basket_id: window.BASKET_ID,
                        block_id,
                        order: ids
                    });
                }
            });
        });
    }

});

// Удалить строку (реальное удаление из БД)
async function deletePoint(pointId) {
    if (!window.IS_OWNER) return; // на всякий случай

    pointId = parseInt(pointId, 10);
    if (!pointId) return;

    if (!confirm("Удалить строку из поездки?")) return;

    const json = await api("delete_point", {
        basket_id: window.BASKET_ID,
        point_id: pointId
    });

    if (!json) return;

    // убрать из DOM сразу (без reload)
    const row = document.querySelector('.basket-point[data-id="' + pointId + '"]');
    if (row) row.remove();

    // если была в буфере вырезанных — тоже убрать
    const idx = window.CUT_POINTS.indexOf(pointId);
    if (idx !== -1) window.CUT_POINTS.splice(idx, 1);
}
