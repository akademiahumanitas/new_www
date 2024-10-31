<?php 
$title = get_field( 'section_title' );
$links = get_field( 'linki' );
$image = get_field( 'background_image' );
$block_ID = $block['id'];

if(str_word_count($title, 0, 'ąćęłńóśźżĄĆĘŁŃÓŚŹŻ') > 1) {
    $title = preg_replace('/\b([\p{L}]+)$/u','<span class="text-highlight">$1</span>', $title);
}
?>

<section class="block-offer-links" id="<?= $block_ID; ?>">
    <?php get_theme_part('elements/triangle', [
        'position' => 'top-right',
    ]); ?>
    <?php if($image) : ?>
        <figure class="block-offer-links__background-image fade-in">
            <?php echo wp_get_attachment_image($image, 'full'); ?>
        </figure>
    <?php endif; ?>
    <div class="container">
        <h2 class="block-offer-links__title heading-underline heading-dot fade-in"><?= $title; ?></h2>
        <div class="block-offer-links__wrapper">
            <div class="block-offer-links__links fade-in js-delay">
                <?php foreach ( $links as $index => $link ) : ?>
                    <?php get_theme_part('blocks/block-offer-links/offer-card', [
                        'link' => $link['odnosniki_do_kierunkow'] ? false : $link['link_to_offer'],
                        'button_classes' => 'block-offer-links__link js-delay-item',
                        'icon' => $link['icon'],
                        'title' => $link['title'],
                        'color' => $index < 3 ? 'blue' : '',
                        'index' => $index,
                    ]); ?>
                <?php endforeach; ?>
            </div>
            <div class="block-offer-links__sidebar fade-in">
                <?php foreach ( $links as $index => $link ) : 
                    $sidebar_links = $link['odnosniki_do_kierunkow'];
                    $sidebar_cta = $link['link_to_offer'];
                    ?>
                    <div class="block-offer-links__sidebar-single" id="offer-<?= $index; ?>">
                        <h3 class="block-offer-links__sidebar-title"><?= strip_tags($link['title']);?></h3>
                        <div class="block-offer-links__sidebar-content">
                            <ul class="block-offer-links__sidebar-list list-styles">
                                <?php foreach ( $sidebar_links as $single_link ) : 
                                    $link = $single_link['link']; ?>
                                    <li class="block-offer-links__sidebar-item">
                                        <a href="<?= $link['url']; ?>" class="block-offer-links__sidebar-link"
                                            target="<?= $link['target']; ?>"
                                            aria-label="<?= strip_tags($link['title']); ?>"
                                            >
                                            <span class="block-offer-links__sidebar-link-title"><?= $link['title']; ?></span>
                                            <?= get_image('arrow-up-right'); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php if($sidebar_cta) : ?>
                                <a href="<?= $sidebar_cta['url']; ?>" class="block-offer-links__sidebar-cta-link">
                                    <?= __('Przejdź do strony oferty', 'Humanitas'); ?>
                                    <?= get_image('arrow-right'); ?>
                                </a>
                            <?php endif; ?>
                            
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php get_theme_part('elements/triangle', [
        'position' => 'bottom-left',
    ]); ?>
</section>
