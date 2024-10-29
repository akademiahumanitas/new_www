<?php
    $title = get_field('section_title');
    $logos = get_field('logos');
    $block_style = get_field('block_style') ?? 'logos';
    $partners = get_field('partners');
    $block_id = $block['id'];
?>
<section class="block-logo-slider block-logo-slider--<?= $block_style; ?>" id="<?= $block_id;?>">
    <div class="container">
        <h2 class="block-logo-slider__title heading-underline fade-in"><?= $title; ?></h2>
    </div>
    <?php if($block_style === 'with-text') : ?>
        <div class="block-logo-slider__partners">
            <div class="container">
                <?php foreach($partners as $partner) : ?>
                    <div class="block-logo-slider__partner fade-in">
                        <figure class="block-logo-slider__partner-logo">
                            <?= get_image($partner['logo']); ?>
                        </figure>
                        <div class="block-logo-slider__partner-content">
                            <h3 class="block-logo-slider__partner-title"><?= $partner['title']; ?></h3>
                            <div class="block-logo-slider__partner-text"><?= $partner['content']; ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="block-logo-slider__logos fade-in">
            <?php foreach($logos as $logo) : ?>
                <figure class="block-logo-slider__logo">
                    <?= get_image($logo); ?>
                </figure>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>