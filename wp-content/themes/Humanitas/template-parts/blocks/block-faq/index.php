<?php

$faq = $faq ?? get_field('faq');
$section_title = $section_title ?? get_field('section_title');
$block_ID = $block['id'];
?>
<section class="block-faq" id="<?= $block_ID; ?>">
    <div class="container">
        <h3 class="block-faq__title fade-in"><?= $section_title; ?></h3>
        <div class="block-faq__wrapper fade-in js-delay">
            <?php foreach ($faq as $item) : ?>
                <?php get_theme_part('blocks/block-faq/faq-element', [
                    'item' => $item,
                    'class' => 'js-delay-item',
                ]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>