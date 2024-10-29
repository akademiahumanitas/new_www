<?php if ($icons && $icons_title) : ?>
    <div class="block-three-boxes__icons">
        <h3 class="block-three-boxes__icons-title"><span><?php echo $icons_title; ?></span></h3>
        <div class="block-three-boxes__icons-wrapper">
            <?php foreach ($icons as $icon) : ?>
                <?php if ($icon['link']) : ?>
                    <a href="<?php echo $icon['link']['url']; ?>"
                        aria-label="<?php echo $icon['title']; ?>"
                        target="<?= $icon['link']['target']; ?>" class="block-three-boxes__icon-link">
                <?php endif; ?>
                    <div class="block-three-boxes__icon">
                        <figure class="block-three-boxes__icon-icon">
                            <?php echo wp_get_attachment_image($icon['icon'], 'full'); ?>
                        </figure>
                        <h4 class="block-three-boxes__icon-title"><?= $icon['title']; ?></h4>
                    </div>
                <?php if ($icon['link']) : ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>