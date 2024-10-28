<?php

$page_title = get_the_title();
$background_image = get_field('background_image');

$duration = get_field('duration');
$position = get_field('position');
$contact_person = get_field('contact_person');
$recruitment = get_field('recruitment');
$contact_link = get_field('contact_link');
$buttons = get_field('buttons');
$sign_up_link = get_field('sign_up_link');
$ask_contact_link = get_field('ask_contact_link');

// get offer_type taxonomy
$offer_type = get_the_terms(get_the_ID(), 'offer_type');
$offer_language = get_the_terms(get_the_ID(), 'offer_language');
$offer_format = get_the_terms(get_the_ID(), 'offer_format');
// iterate through offer_type and get the name of the term
$types = [];
foreach($offer_type as $type) {
    $types[] = $type->name;
}

$languages = [];
foreach($offer_language as $language) {
    $languages[] = $language->name;
}

$formats = [];
foreach($offer_format as $format) {
    $formats[] = $format->name;
}
$boxes[] = [
    'title' => join(', ', $types),
    'subtitle' => __('Poziom', 'humanitas'),
    'icon' => 'trophy',
];
$boxes[] = [
    'title' => $duration,
    'subtitle' => __('Czas', 'humanitas'),
    'icon' => 'clock',
];
if($position) {
    $boxes[] = [
        'title' => $position,
        'subtitle' => __('Uzyskany tytuł', 'humanitas'),
        'icon' => 'graduation-cap',
    ];
}
if($offer_language) {
    $boxes[] = [
        'title' => join(' / ', $languages),
        'subtitle' => __('Język', 'humanitas'),
        'icon' => 'globe',
    ];
}
if($offer_format) {
    $boxes[] = [
        'title' => join(' / ', $formats),
        'subtitle' => __('Tryb', 'humanitas'),
        'icon' => 'building',
    ];
}
if($recruitment) {
    $boxes[] = [
        'title' => $recruitment,
        'subtitle' => __('Start rekrutacji', 'humanitas'),
        'icon' => 'calendar',
    ];
}
?>
<section class="offer-hero">
    <?php if($background_image) : ?>
        <figure class="offer-hero__background-image">
            <?= get_image($background_image, 'hero'); ?>
        </figure>
    <?php endif; ?>
    <div class="container">
        <div class="offer-hero__wrapper">
            <div class="offer-hero__left">
                <?php get_theme_part('elements/breadcrumbs', ['version' => 'secondary']); ?>
                <h1 class="offer-hero__title fade-in"><?= $page_title; ?></h1>
                <div class="offer-hero__boxes">
                    <?php foreach($boxes as $box) : ?>
                        <div class="offer-hero__box">
                            <div class="offer-hero__box-icon"><?= get_image($box['icon']); ?></div>
                            <div class="offer-hero__box-content">
                                <div class="offer-hero__box-subtitle"><?= $box['subtitle']; ?></div>
                                <div class="offer-hero__box-title"><?= $box['title']; ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if($buttons) :?>
                    <div class="offer-hero__buttons">
                        <?php foreach($buttons as $button) : ?>
                            <?php get_theme_part('elements/button', [
                                'button' => $button['link'],
                                'button_classes' => 'button-tertiary button-small button-white',
                                'icon_position' => 'right',
                                'icon' => $button['download'] ? 'download' : '',
                                'download' => $button['download'],
                            ]); ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php if($sign_up_link || $ask_contact_link) : ?>
                    <div class="offer-hero__buttons">
                        <?php get_theme_part('elements/button', [
                            'button' => $sign_up_link,
                            'button_classes' => 'button-primary button-yellow offer-hero__button-yellow',
                            'icon' => 'arrow-up-right',
                            'icon_position' => 'right'
                        ]); ?>
                        <?php get_theme_part('elements/button', [
                            'button' => $ask_contact_link,
                            'button_classes' => 'button-tertiary button-white offer-hero__button-contact',
                        ]); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="offer-hero__right">
                <?php if($contact_person) : 
                        $image = get_post_thumbnail_id($contact_person);
                        $subtitle = __('Twój opiekun', 'humanitas');
                        $title = get_the_title($contact_person);
                        $text = get_field('short_description', $contact_person);
                    ?>
                    <?php get_theme_part('elements/contact-box', [
                        'contact_box' => [
                            'image' => $image,
                            'subtitle' => $subtitle,
                            'title' => $title,
                            'text' => $text,
                            'link' => ['url' => $contact_link, 'title' => __('Skontaktuj się', 'humanitas')],
                        ],
                        'version' => 'secondary',
                    ]); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php get_theme_part('blocks/block-secondary-menu/index', [
    'show_secondary_menu' => true,
    'secondary_menu_automatic' => true,
]); ?>