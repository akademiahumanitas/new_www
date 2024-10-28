<?php

    $button = $button ?? get_field( 'button' );
    $size = isset($size) ? 'button-'.$size : '';
    $color = isset($color) ? 'button-'.$color : '';
    $button_classes = $button_classes ?? '';
    $icon = $icon ? get_image($icon) : '';
    $icon_position = $icon_position ? 'icon-'.$icon_position : 'icon-left';
    $download = $download ?? false;
?>

<?php if ( $button ) : ?>
    <a 
        href="<?= $button['url']; ?>" 
        target="<?= $button['target']; ?>" 
        aria-label="<?= $button['title']; ?>" 
        title="<?= $button['title']; ?>" 
        <?php echo $download ? 'download' : ''; ?>
        class="button <?= $color; ?> <?= $size; ?> <?= $button_classes; ?>">
        <?php if($icon) : ?>
            <span class="button__icon <?= $icon_position; ?>"><?= $icon; ?></span>
        <?php endif; ?>
        <?= $button['title']; ?>
        </a>
<?php endif; ?>