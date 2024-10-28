<?php
$url = home_url( $_SERVER['REQUEST_URI'] );
// remove page number from url
$url = preg_replace('/\/page\/\d+\//', '/', $url);
// remove search query from url
$url = preg_replace('/\?s=[^&]+/', '', $url);
// remove trailing slash
$url = rtrim($url, '/');
$terms = get_taxonomy_terms_by_post_type($post_type);

$input_title = [
    'post' => __('Wyszukaj według tytułu artykułu', 'humanitas'),
    'oferta' => __('Wyszukaj według tytułu kierunku', 'humanitas'),
    'events' => __('Wyszukaj według tytułu artykułu', 'humanitas'),
    'books' => __('Wyszukaj według tytułu', 'humanitas'),
]

?>

<div class="archive-page__search">
    <form class="archive-page__search-form" action="<?= $url;?>" method="get">
        <div class="archive-page__search-wrapper">
            <?= get_image('search-icon'); ?>
            <input class="archive-page__search-input" type="text" name="s" placeholder="<?= $input_title[$post_type]; ?>" value="<?= $_GET['s'];?>" id="search-input"/>
        </div>
        <button class="archive-page__search-button button button-blue" type="submit">
            <?= __('Szukaj', 'humanitas'); ?>
        </button>
    </form>
</div>
<?php if($terms) : ?>
    <button class="button button-secondary button-large archive-page__filters-toggle" aria-label="Pokaż filtry">
        <?= __('Filtry', 'humanitas'); ?>
        <span class="js-selected-filters  archive-page__filters-toggle-number"></span>
        <?= get_image('filter'); ?>
    </button>
<?php endif; ?>