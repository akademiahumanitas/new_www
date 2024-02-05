<?php

    $title = $title ?? '';
    $icon = $icon ? $icon : '';
    $button_classes = $button_classes ? ' '.$button_classes : '';
    $color = $color ? ' button-offer-card--'.$color.' ' : '';

    $element = $link ? 'a href="'.$link['url'].'" target="'.$link['target'].'"' : 'button';
    $index = $index ?? 0;
?>

<?php if ( $title ) : ?>
    <<?=$element ?> class="button-offer-card<?=$color;?><?= $button_classes; ?>" data-id="offer-<?= $index; ?>">
        <?php if($icon) : ?>
            <span class="button-offer-card__icon"><?= get_image($icon); ?></span>
        <?php endif; ?>
        <span class="button-offer-card__title"><?= $title; ?></span>
    </<?=$element ?>>
<?php endif; ?>