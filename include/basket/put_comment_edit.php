<div class="basket-point basket-comment"
     data-id="<?= $pid ?>"
     data-point-id="<?= $pid ?>"
     data-type="COMMENT">

    <div class="comment-body">
        <div class="drag-handle" title="Переместить">☰</div>

        <!-- Редактируемый текст -->
        <div class="text-line"
             contenteditable="true"
             data-id="<?= $pid ?>"
             onfocus="saveSelection()"
             oninput="saveSelection()"
             onblur="autosaveText(this)"><?= $p["text"] ?></div>

        <!-- Кнопки управления -->
        <div class="editor-toolbar">

            <!-- Форматирование -->
            <button type="button"
                    onmousedown="event.stopPropagation(); applyFormat('bold', event)">
                <b>B</b>
            </button>

            <button type="button"
                    onmousedown="event.stopPropagation(); applyFormat('italic', event)">
                <i>I</i>
            </button>

            <button type="button"
                    onmousedown="event.stopPropagation(); applyFormat('removeFormat', event)">
                Tx
            </button>

            <!-- Вырезать -->
            <button type="button"
                    class="btn-cut"
                    onclick="event.stopPropagation(); cutPoint(<?= $pid ?>, this)">
                ✂
            </button>

            <!-- Вставить после строки -->
            <button type="button"
                    class="btn-paste"
                    onclick="pasteAfter(<?= $pid ?>, this, event)">
                📋
            </button>

            <!-- Удалить навсегда -->
            <button type="button"
                    class="btn-cut"
                    title="Удалить строку"
                    onclick="event.stopPropagation(); deletePoint(<?= $pid ?>)">
                🗑
            </button>

        </div>

    </div>
</div>
