<?php
    $title = get_field('section_title');
    $logos = get_field('logos');
    $block_id = $block['id'];
?>
<section class="block-logo-slider" id="<?= $block_id;?>">
    <h2 class="block-logo-slider__title heading-underline fade-in"><?= $title; ?></h2>
    <div class="block-logo-slider__logos fade-in">
        <?php foreach($logos as $logo) : ?>
            <figure class="block-logo-slider__logo">
                <?= get_image($logo); ?>
            </figure>
        <?php endforeach; ?>
    </div>
</section>