<div class="basket-point basket-object"
     data-id="<?= $pid ?>"
     data-point-id="<?= $pid ?>"
     data-type="OBJECT">

    <!-- Заголовок объекта -->
    <div class="object-header">
        <div class="drag-handle" title="Переместить">☰</div>

        <div class="toggle-object"
             onclick="event.stopPropagation(); toggleObject(this)">▶</div>

        <?php
  $tx = gettownex_bs((int)$p["object_id"]);
  $obj_name  = (string)($tx[0] ?? getexname($p["object_id"]));
  $town_name = (string)($tx[1] ?? '');
?>
<a class="object-title"
   href="/otklik.php/<?= (int)$p["object_id"] ?>"
   target="_blank"
   onclick="event.stopPropagation();">
    <?= $obj_name ?>
    <?php if ($town_name !== ''): ?>
      <span style="color:#888;"> — <?= htmlspecialchars($town_name) ?></span>
    <?php endif; ?>
</a>
    </div>

    <!-- Расширенная информация -->
    <div class="object-extra collapsed">
        <?= getexdesc($p["object_id"]) ?>
    </div>

    <!-- Текстовое поле + кнопки -->
    <div class="comment-body">

        <!-- Редактируемая строка -->
        <div class="text-line"
             contenteditable="true"
             data-id="<?= $pid ?>"
             onfocus="saveSelection()"
             oninput="saveSelection()"
             onblur="autosaveText(this)"><?= $p["text"] ?></div>

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

            <!-- Вставить после -->
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

