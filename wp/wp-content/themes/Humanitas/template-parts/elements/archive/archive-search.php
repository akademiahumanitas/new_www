<?php
$url = home_url( $_SERVER['REQUEST_URI'] );
// remove page number from url
$url = preg_replace('/\/page\/\d+\//', '/', $url);
// remove search query from url
$url = preg_replace('/\?s=[^&]+/', '', $url);
// remove trailing slash
$url = rtrim($url, '/');
?>

<div class="archive-page__search">
    <form class="archive-page__search-form" action="<?= $url;?>" method="get">
        <div class="archive-page__search-wrapper">
            <?= get_image('search-icon'); ?>
            <input class="archive-page__search-input" type="text" name="s" placeholder="Szukaj" value="<?= $_GET['s'];?>" id="search-input"/>
        </div>
        <button class="archive-page__search-button button button-blue" type="submit">
            <?= __('Szukaj', 'humanitas'); ?>
        </button>
    </form>
</div>