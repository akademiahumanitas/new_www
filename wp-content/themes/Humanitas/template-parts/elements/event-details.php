<?php
    $event_date = get_field('event_date');
    $address_line_1 = get_field('address_line_1');
    $address_line_2 = get_field('address_line_2');
    $sign_up_link = get_field('sign_up_link');
    $date = new DateTime($event_date);

    $formatter = new IntlDateFormatter(
        'pl_PL', 
        IntlDateFormatter::FULL, 
        IntlDateFormatter::FULL,
        'Europe/Warsaw',
        IntlDateFormatter::GREGORIAN,
        'd MMMM yyyy, HH:mm'
    );
    $formatted_date = $formatter->format($date);
    $isPastDate = new DateTime() > $date;
?>

<div class="event-details">
    <div class="event-details__header"><?= $formatted_date; ?></div>
    <div class="event-details__content">
        <figure class="event-details__icon">
            <?= get_image('location'); ?>
        </figure>
        <div class="event-details__address">
            <p class="event-details__address-line"><?= $address_line_1; ?></p>
            <p class="event-details__address-line"><?= $address_line_2; ?></p>
        </div>
    </div>
    <?php if(!$isPastDate && $sign_up_link) :?>
        <div class="event-details__button">
            <?php get_theme_part('elements/button', [
                'button' => [
                    'url' => $sign_up_link,
                    'title' => __('Zapisz się', 'humanitas'),
                    'target' => '_blank'
                ],
                'button_classes' => 'button-primary button-large button-yellow'
            ]); ?>
        </div>
    <?php endif; ?>
</div>