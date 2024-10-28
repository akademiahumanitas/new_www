<?php
    $text = $text ?? get_field('text');
    if($text) :
?>
<p class="info-box fade-in">
    <?= get_image('info'); ?>
    <?= $text; ?>
</p>

<?php endif; ?>