<?php

/**
 * Block Offer Search
 */

$block_ID = $block['id'];

$title = get_field( 'title' );
$image = get_field( 'image' );

$type_title = get_field( 'type_title' );
$location_title = get_field( 'location_title' );
$format_title = get_field( 'format_title' );
$category_title = get_field( 'category_title' );

$type_terms = get_terms(array(
    'taxonomy' => 'offer_type',
    'hide_empty' => false,
));

$location_terms = get_terms(array(
    'taxonomy' => 'offer_location',
    'hide_empty' => false,
));

$format_terms = get_terms(array(
    'taxonomy' => 'offer_format',
    'hide_empty' => false,
));

$category_terms = get_terms(array(
    'taxonomy' => 'offer_category',
    'hide_empty' => false,
));

?>
<section class="block-offer-search" id="<?php echo $$block_ID; ?>">
    <div class="container">
        <h2 class="block-offer-search__title heading-underline fade-in"><?php echo $title; ?></h2>
        <div class="block-offer-search__wrapper">
            <div class="block-offer-search__left">
                <?php get_theme_part('blocks/block-offer-search/term-filters', array(
                    'title' => $type_title,
                    'terms' => $type_terms,
                )); ?>
                <?php get_theme_part('blocks/block-offer-search/term-filters', array(
                    'title' => $location_title,
                    'terms' => $location_terms,
                )); ?>
                <?php get_theme_part('blocks/block-offer-search/term-filters', array(
                    'title' => $format_title,
                    'terms' => $format_terms,
                )); ?>
                <?php get_theme_part('blocks/block-offer-search/term-filters', array(
                    'title' => $category_title,
                    'terms' => $category_terms,
                )); ?>
                <a data-offer-href="<?= get_post_type_archive_link('oferta'); ?>" class="block-offer-search__submit-button button button-large button-yellow fade-in" aria-label="<?php echo __( 'Search Button', 'humanitas' ); ?>" type="button">
                    <?php _e( 'Szukaj', 'humanitas' ); ?>
                    <span class="block-offer-search__submit-button-icon">
                        <?php echo get_image( 'search-icon' ); ?>
                    </span>
                </a>
                <details class="block-offer-search__keyword-search fade-in">
                    <summary class="block-offer-search__keyword-search-title" 
                        aria-label="<?php echo __( 'Search by keyword', 'humanitas' ); ?>" 
                        aria-expanded="false" 
                        role="button" 
                        tabindex="0" 
                        title="<?php echo __( 'Search by keyword', 'humanitas' ); ?>"
                    ><?php _e( 'lub szukaj według słowa kluczowego', 'humanitas' ); ?> <?php echo get_image( 'chevron-up' ); ?></summary>
                    <form action="<?= get_post_type_archive_link('oferta');?>" class="block-offer-search__keyword-search-wrapper">
                        <input type="text" name="s" class="block-offer-search__keyword-search-input" placeholder="<?php _e( 'Wpisz słowo kluczowe', 'humanitas' ); ?>">
                        <button type="submit" class="block-offer-search__keyword-search-button button button-yellow" aria-label="<?php echo __( 'Search Button', 'humanitas' ); ?>">
                            <?php echo get_image( 'search-icon' ); ?>
                        </button>
                    </form>
                </details>
            </div>
            <div class="block-offer-search__right">
                <figure class="block-offer-search__image fade-in">
                    <?php echo get_image( $image ); ?>
                </figure>
            </div>
        </div>
    </div>
</section>