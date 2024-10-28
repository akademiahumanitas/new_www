<?php
$content = get_field('content');
$boxes = get_field('boxes');
$left_icons_title = get_field('left_icons_title');
$left_icons = get_field('left_icons');
$right_icons_title = get_field('right_icons_title');
$right_icons = get_field('right_icon');
$block_ID = $block['id'];
?>

<section class="block-three-boxes" id="<?php echo $block_ID; ?>">
    <div class="container">
        <?php if ($content) : ?>
            <div class="block-three-boxes__content heading-dot heading-dot--small"><?php echo $content; ?></div>
        <?php endif; ?>

        <div class="block-three-boxes__boxes">
            <?php if ($boxes) : ?>
                <?php foreach ($boxes as $box) : ?>
                    <div class="block-three-boxes__box<?php echo $box['featured'] ? ' block-three-boxes__box--featured' : '' ?>">
                        <div class="block-three-boxes__box-icon">
                            <?php echo wp_get_attachment_image($box['icon'], 'full'); ?>
                        </div>
                        <div class="block-three-boxes__box-content">
                            <h3 class="block-three-boxes__box-title"><?php echo $box['title']; ?></h3>
                            <p><?php echo $box['text']; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="block-three-boxes__columns">
            <?php get_theme_part('blocks/block-three-boxes/single-icons', [
                'icons_title' => $left_icons_title,
                'icons' => $left_icons
            ]) ?>
            <?php get_theme_part('blocks/block-three-boxes/single-icons', [
                'icons_title' => $right_icons_title,
                'icons' => $right_icons
            ]) ?>
        </div>
    </div>
</section>