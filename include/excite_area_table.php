<?php
// expects:
// $ea_items (array), $ea_hasMore (bool), $ea_page (int), $ea_baseUrl (string)
// baseUrl without ?page

if (!function_exists('ea_h')) {
  function ea_h($s){ return htmlspecialchars((string)$s, ENT_QUOTES | ENT_HTML401, 'UTF-8'); }
}

if (empty($GLOBALS['ea_table_assets_loaded'])) {
  $GLOBALS['ea_table_assets_loaded'] = 1;

  echo "<style>
#excite-area-table th.at-sortable{ position:relative; padding-right:18px; }
#excite-area-table th.at-sortable:after{
  content:'▴▾'; position:absolute; right:6px; top:50%;
  transform:translateY(-50%); color:#bbb; font-size:10px;
}
#excite-area-table th.at-sortable.sort-asc:after{ content:'▴'; color:#333; }
#excite-area-table th.at-sortable.sort-desc:after{ content:'▾'; color:#333; }
</style>";

  echo "<script>
(function(){
  function getCellText(td){
    if(!td) return '';
    return (td.textContent || td.innerText || '').trim();
  }
  function parseNum(s){
    s = (s||'').toString().replace(/\\s+/g,'').replace(',', '.');
    var v = parseFloat(s);
    return isNaN(v) ? 0 : v;
  }
  function sortTable(table, colIdx, dir){
    var tbody = table.tBodies[0];
    var rows = Array.prototype.slice.call(tbody.rows, 0);

    var ths = table.tHead ? table.tHead.rows[0].cells : [];
    var mode = ths[colIdx] ? (ths[colIdx].getAttribute('data-sort') || 'text') : 'text';

    rows.sort(function(a,b){
      var ta = getCellText(a.cells[colIdx]);
      var tb = getCellText(b.cells[colIdx]);

      if(mode === 'num'){
        var na = parseNum(ta), nb = parseNum(tb);
        return dir * (na - nb);
      } else {
        try { return dir * ta.localeCompare(tb, 'ru', {sensitivity:'base'}); }
        catch(e){ return dir * (ta > tb ? 1 : (ta < tb ? -1 : 0)); }
      }
    });

    for(var i=0;i<rows.length;i++) tbody.appendChild(rows[i]);
  }

  function initSortableTable(id){
    var table = document.getElementById(id);
    if(!table || !table.tHead || !table.tHead.rows.length) return;

    var headers = table.tHead.rows[0].cells;

    for(let i=0;i<headers.length;i++){
      let th = headers[i];
      th.classList.add('at-sortable');
      th.style.cursor = 'pointer';
      th.setAttribute('data-dir','0');

      th.addEventListener('click', function(){
        for(var j=0;j<headers.length;j++){
          headers[j].setAttribute('data-dir','0');
          headers[j].classList.remove('sort-asc','sort-desc');
        }
        var cur = parseInt(th.getAttribute('data-dir') || '0', 10);
        var next = (cur === 1) ? -1 : 1;
        th.setAttribute('data-dir', String(next));
        th.classList.add(next === 1 ? 'sort-asc' : 'sort-desc');
        sortTable(table, i, next);
      });
    }
  }

  function initTownFilter(){
    var sel = document.getElementById('excite-town-filter');
    var table = document.getElementById('excite-area-table');
    if(!sel || !table) return;

    sel.addEventListener('change', function(){
      var v = sel.value;
      var rows = table.tBodies[0].rows;
      for(var i=0;i<rows.length;i++){
        var tid = String(rows[i].getAttribute('data-town-id') || '');
        rows[i].style.display = (!v || tid === String(v)) ? '' : 'none';
      }
    });
  }

  function init(){
    initSortableTable('excite-area-table');
    initTownFilter();
  }

  if(document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();
</script>";
}

// --- build town filter options (current page only) ---
$towns = [];
foreach ($ea_items as $r) {
  $tid = (int)($r['town'] ?? 0);
  if ($tid <= 0) continue;
  if (!isset($towns[$tid])) {
    $towns[$tid] = (string)($r['town_name'] ?? '');
  }
}
asort($towns, SORT_STRING);

echo "<div class='row' style='margin:6px 0 10px;'><div class='col-xs-12'>";
echo "<label class='travell0' style='margin-right:8px;'>Город:</label>";
echo "<select id='excite-town-filter' class='form-control' style='display:inline-block; width:auto; max-width:100%;'>";
echo "<option value=''>Все</option>";
foreach ($towns as $tid => $tname) {
  echo "<option value='".(int)$tid."'>".ea_h($tname)."</option>";
}
echo "</select>";
echo "</div></div>";

echo "<table id='excite-area-table' class='table table-hover tdc' style='width:100%'>";
echo "<thead><tr>";
echo "<th class='at-sortable' data-sort='text'>Объект</th>";
echo "<th class='at-sortable' data-sort='text' style='width:180px;'>Город</th>";
echo "<th class='at-sortable' data-sort='num'  style='width:120px;'>Рейтинг</th>";
echo "<th class='at-sortable' data-sort='num'  style='width:120px;'>Отклики</th>";
echo "</tr></thead><tbody>";

foreach ($ea_items as $r) {
  $id     = (int)$r['id'];
  $name   = (string)$r['name'];
  $wr     = sprintf("%01.2f", (float)$r['wrate']);
  $vv     = (int)$r['votes'];
  $mainid = (int)$r['mainid'];

  $href = ($mainid >= 0) ? "/otklik.php/$id" : "/group.php/$id";

  $town_id = (int)($r['town'] ?? 0);
  $town_html = "&nbsp;";
  if ($town_id > 0) {
    $town_name = (string)($r['town_name'] ?? '');
    $town_url  = (string)($r['town_url'] ?? '');
    $town_href = $town_url ? ("/gorod/".$town_url.".html") : ("/town.php/".$town_id);
    $town_html = "<a class='travell5n' href='".ea_h($town_href)."'>".ea_h($town_name)."</a>";
  }

  echo "<tr data-town-id='".$town_id."'>";
  echo "<td><a class='travell5n' href='".ea_h($href)."'>".ea_h($name)."</a></td>";
  echo "<td>".$town_html."</td>";
  echo "<td><b>".$wr."</b></td>";
  echo "<td>".$vv."</td>";
  echo "</tr>";
}

echo "</tbody></table>";

if (!empty($ea_hasMore)) {
  $next = (int)$ea_page + 1;
  $nextUrl = $ea_baseUrl . "?page=" . $next;
  echo "<div style='margin:10px 0 20px;'>";
  echo "<a class='btn btn-default' href='".ea_h($nextUrl)."'>Показать ещё</a>";
  echo "</div>";
}