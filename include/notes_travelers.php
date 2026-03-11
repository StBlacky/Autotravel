<?php
/*
 * Универсальный блок "Заметки путешественников"
 * Используется для городов (t{id}) и объектов (o{id}, e{id} и т.п.)
 *
 * ВХОД:
 *   $obj_key  — строка, например 't123' или 'o456'
 *
 * ВЫХОД:
 *   HTML-блок или пустая строка
 */

if (!isset($obj_key) || !$obj_key) return;

global $dbi, $_desc_mode;

// если включён режим без описаний — ничего не показываем
if ($_desc_mode == 1) return;

$sql = "
  SELECT descr, cook
  FROM l_text
  WHERE obj = '".$dbi->real_escape_string($obj_key)."'
    AND dtizm > 0
  ORDER BY dtizm DESC
";

$res = $dbi->query($sql);
if (!$res || !$res->num_rows) {
  mysqli_free_result($res);
  return;
}

// собираем заметки
$items = [];
while ($row = mysqli_fetch_assoc($res)) {
  $text = trim($row['descr']);
  if (!$text) continue;

  $author = authform($row['cook']);

  $items[] = "
    <div class=\"travel-note\">
      <div class=\"travel-note-text\">".$text."</div>
      <div class=\"travel-note-author\">Автор: ".$author."</div>
    </div>
  ";
}
mysqli_free_result($res);

if (!count($items)) return '';

$html = '
<div class="travel-notes">
  <div class="travel-notes-toggle" onclick="this.parentNode.classList.toggle(\'open\')">
    Заметки путешественников
  </div>
  <div class="travel-notes-content">
    '.implode("\n", $items).'
  </div>
</div>';

return $html;
?>
