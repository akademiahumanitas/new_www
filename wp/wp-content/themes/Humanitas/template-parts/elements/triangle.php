<?php
    $position = $position ?? 'top-left';
?>

<div class="triangle triangle--<?= $position; ?>">
    <?= get_image('triangle-'.$position); ?>
</div>