<?php
include_once('include/cube.data.php');
?>

<div class="cube-mobile">
<?php
foreach ($td as $region => $types) {

    $areaId   = $ar[$region] ?? 0;
    $areaSlug = $ar_slug[$region] ?? '';
    $areaLink = "/area.php/".$areaId;

    echo "<div class='cube-card'>";

    // --- HEADER ---
    echo "<div class='cube-card-header'>";

    echo "<a href='{$areaLink}' target='_blank' class='cube-area-link'>"
          . htmlspecialchars($region) .
         "</a>";

    echo "<span class='cube-toggle'>+</span>";

    echo "</div>";

    // --- BODY (по умолчанию скрыт) ---
    echo "<div class='cube-card-body' style='display:none;'>";

    foreach ($th as $label) {

        $count = (int)($td[$region][$label]['count'] ?? 0);

        if ($count > 0) {

            $tip = $labels_to_title[$label] ?? $label;
            $ico = $icons_by_desc[$label] ?? '';
            $img = $ico ? "<img src='{$ico}' alt=''>" : '';

            $exid = $td[$region][$label]['exid'] ?? 0;

            $url = cube_get_link($areaId, $areaSlug, $label, $count, $exid);

            echo "<a href='{$url}' target='_blank' class='cube-item'>
                    {$img}
                    <span class='label'>" . htmlspecialchars($tip) . "</span>
                    <span class='count'>{$count}</span>
                  </a>";
        }
    }

    echo "</div>"; // body
    echo "</div>"; // card
}
?>
</div>



<script>
document.querySelectorAll('.cube-toggle').forEach(toggle => {
  toggle.addEventListener('click', function(e) {

    e.preventDefault();
    e.stopPropagation();

    const card = this.closest('.cube-card');
    const body = card.querySelector('.cube-card-body');

    const isOpen = body.style.display === 'block';

    body.style.display = isOpen ? 'none' : 'block';
    this.textContent = isOpen ? '+' : '–';
  });
});
</script>