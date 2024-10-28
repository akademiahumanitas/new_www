<?php
$post_ID = $post_ID ?? get_the_ID();
$event_date = get_field('event_date', $post_ID);
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
$event_title = get_the_title($post_ID);
$event_link = get_the_permalink($post_ID);
$event_thumbnail = get_post_thumbnail_id($post_ID);
$class = $class ?? '';
$today = new DateTime();

$old_class = $today > $date
    ? 'event-card--old'
    : '';
?>
<div class="event-card <?= $class;?> <?=$old_class; ?>">
    <figure class="event-card__image">
        <a href="<?= $event_link; ?>" aria-label="<?= __('Link do wydarzenia', 'humanitas') . ' ' . $event_title; ?>" title="<?= $event_title; ?>">
            <?= wp_get_attachment_image($event_thumbnail, 'full', false, array('class' => 'event-card__image-img')); ?>
        </a>
    </figure>
    <div class="event-card__content">
        <p class="event-card__date"><?php echo $old_class ? __('Minęło ', 'humanitas') : '' ;?><?= $formatted_date; ?></p>
        <h4 class="event-card__title">
            <a href="<?= $event_link; ?>" aria-label="<?= __('Link do wydarzenia', 'humanitas') . ' ' . $event_title; ?>" title="<?= $event_title; ?>" class="event-card__title-link"><?= $event_title; ?></a>
        </h4>
    </div>
</div>
