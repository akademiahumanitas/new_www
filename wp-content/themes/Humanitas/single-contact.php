<?php
namespace Air_Light;

the_post();
get_header(); 

$infobox = get_field('infobox');
$address = get_field('address');
$phone_number = get_field('phone_number');
$email_address = get_field('email_address');
$opening_hours = get_field('opening_hours');
?>

<main class="site-main">
    <?php get_theme_part('blocks/block-hero/index', [
        'title' => get_the_title(),
        'decoration' => false
    ]); ?>
    <article class="single-contact">
        <div class="container">
            <div class="single-contact__wrapper">
                <div class="single-contact__left">
                    <?php get_theme_part('elements/info-box', [
                        'text' => $infobox,
                    ]); ?>
                    <?php get_theme_part('blocks/block-people/index',[
                        'section_title' => __('Pracownicy działu', 'humanitas'),
                        'people' => get_field('people'),
                    ]); ?>
                    <?php get_theme_part('blocks/block-people/index',[
                        'section_title' => __('Inne osoby do kontaktu', 'humanitas'),
                        'people' => get_field('more_people'),
                    ]); ?>
                </div>
                <div class="single-contact__right">
                    <div class="single-contact__box">
                        <h3 class="single-contact__box-title"><?= __('Adres i kontakt', 'humanitas'); ?></h3>
                        <div class="single-contact__box-content"><?= $address; ?></div>
                        <a href="tel:<?= $phone_number; ?>" class="single-contact__link"><?= get_image('phone'); ?><?= $phone_number; ?></a>
                        <a href="mailto:<?= $email_address; ?>" class="single-contact__link"><?= get_image('email'); ?><?= $email_address; ?></a>
                    </div>
                    <div class="single-contact__box">
                        <h3 class="single-contact__box-title"><?= __('Godziny otwarcia', 'humanitas'); ?></h3>
                        <p class="single-contact__opening"><span class="single-contact__opening-day"><?= __('Poniedziałek', 'humanitas');?>:</span><span class="single-contact__opening-time"><?= $opening_hours['monday']; ?></span></p>
                        <p class="single-contact__opening"><span class="single-contact__opening-day"><?= __('Wtorek', 'humanitas');?>:</span><span class="single-contact__opening-time"><?= $opening_hours['tuesday']; ?></span></p>
                        <p class="single-contact__opening"><span class="single-contact__opening-day"><?= __('Środa', 'humanitas');?>:</span><span class="single-contact__opening-time"><?= $opening_hours['wednesday']; ?></span></p>
                        <p class="single-contact__opening"><span class="single-contact__opening-day"><?= __('Czwartek', 'humanitas');?>:</span><span class="single-contact__opening-time"><?= $opening_hours['thursday']; ?></span></p>
                        <p class="single-contact__opening"><span class="single-contact__opening-day"><?= __('Piątek', 'humanitas');?>:</span><span class="single-contact__opening-time"><?= $opening_hours['friday']; ?></span></p>
                        <p class="single-contact__opening"><span class="single-contact__opening-day"><?= __('Sobota', 'humanitas');?>:</span><span class="single-contact__opening-time"><?= $opening_hours['saturday']; ?></span></p>
                        <p class="single-contact__opening"><span class="single-contact__opening-day"><?= __('Niedziela', 'humanitas');?>:</span><span class="single-contact__opening-time"><?= $opening_hours['sunday']; ?></span></p>
                    </div>
                </div>
            </div>
        </div>
    </article>
</main>

<?php get_footer();
