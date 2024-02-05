<?php
    $text = $text ?? get_field('text');
    if($text) :
?>
<p class="info-box">
    <?= get_image('info'); ?>
    <?= $text; ?>
</p>

<?php endif; ?>