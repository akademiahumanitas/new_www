<?php

    $button = $button ?? get_field( 'button' );
    $button_classes = $button_classes ?? '';
    $icon = $icon ? $icon : '';
    $version = $version ?? 'primary';
?>

<?php if ( $button ) : ?>
    <a 
        href="<?= $button['url']; ?>" 
        target="<?= $button['target']; ?>" 
        aria-label="<?= $button['title']; ?>" 
        title="<?= $button['title']; ?>" 
        class="button-card button-card--<?= $version; ?> <?= $button_classes; ?>">
        <?php if($icon) : ?>
            <span class="button-card__icon"><?= get_image($icon); ?></span>
        <?php endif; ?>
        <span class="button-card__title"><?= $button['title']; ?></span>
    </a>
<?php endif; ?>