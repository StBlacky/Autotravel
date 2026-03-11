<div class="basket-point basket-object readonly" data-id="<?= $pid ?>">

    <div class="object-header">
        <div class="toggle-object" onclick="toggleObject(this)">▶</div>
 <?php
  $tx = gettownex_bs((int)$p["object_id"]);
  $obj_name  = (string)($tx[0] ?? getexname($p["object_id"]));
  $town_name = (string)($tx[1] ?? '');
?>
        <a class="object-title"
           href="/otklik.php/<?= $p["object_id"] ?>"
           target="_blank">
           <?= $obj_name ?>
    <?php if ($town_name !== ''): ?>
      <span style="color:#888;"> — <?= htmlspecialchars($town_name) ?></span>
    <?php endif; ?>
        </a>
    </div>

    <div class="object-extra collapsed">
        <?= getexdesc($p["object_id"]) ?>
    </div>

    <div class="comment-body">
        <div class="text-line readonly">
            <?= $p["text"] ?>
        </div>
    </div>

</div>
