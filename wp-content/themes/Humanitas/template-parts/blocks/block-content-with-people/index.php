<?php

$title = get_field( 'title' );
$content = get_field( 'content' );
$contact_person = get_field( 'contact_person' );
$people = get_field( 'people' );
$block_ID = $block['id'];
$block_style = $block_style ?? get_field('block_style') ?? 'with-contact-person';

?>

<section class="block-content-with-people block-content-with-people--<?= $block_style;?>" id="<?=$block_ID; ?>">
    <div class="container">
        <h2 class="block-content-with-people__title heading-underline heading-dot fade-in"><?= $title; ?></h2>
        <?php if($content) :?>
            <div class="block-content-with-people__content fade-in">
                <?= $content; ?>
            </div>
        <?php endif; ?>
        <div class="block-content-with-people__wrapper">
            <?php if($contact_person && $block_style === 'with-contact-person') : ?>
                <div class="block-content-with-people__contact-person">
                    <?php
                        $image = get_post_thumbnail_id($contact_person);
                        $title = get_the_title($contact_person);
                        $text = get_field('short_description', $contact_person);
                        $link = ['url' => '#', 'title' => 'Skontaktuj się'];
                        ?>
                    <?php get_theme_part('elements/contact-box', [
                        'contact_box' => [
                            'image' => $image,
                            'subtitle' => __('Twój opiekun', 'humanitas'),
                            'title' => $title,
                            'text' => $text,
                            'link' => $link,
                        ],
                        'version' => 'secondary'
                    ]); ?>
                </div>
            <?php endif; ?>
            <div class="block-content-with-people__people">
                <?php foreach($people as $person) : ?>
                    <?php get_theme_part('elements/person-card', [
                        'post_ID' => $person,
                        'version' => $block_style === 'big-people' ? 'big' : 'primary'
                    ]); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>