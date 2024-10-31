<?php
    $title = get_field('title');
    $sub_title = get_field('sub_title');
    $description = get_field('description');

    $content = get_field( 'content' );
    $list = get_field( 'list' );
    $image = get_field( 'image' );
    $block_ID = $block['id'];
    $background_color = get_field( 'background_color' );
    $image_position = get_field( 'image_position' );

    if($background_color === 'dark-blue' && str_word_count($title, 0, 'ąćęłńóśźżĄĆĘŁŃÓŚŹŻ') > 1) {
        $title = preg_replace('/\b([\p{L}]+)$/u','<span class="text-highlight">$1</span>', $title);
    }
?>
<section class="block-content-with-list block-content-with-list--<?= $background_color;?> block-content-with-list--<?= $image_position;?>" id="<?= $block_ID; ?>">
    <?php if($background_color !=='white'): ?>
        <?php get_theme_part('elements/triangle', ['position' => 'top-left']); ?>
    <?php endif; ?>
    <div class="container">
        <div class="block-content-with-list__wrapper">
            <figure class="block-content-with-list__image fade-in">
                <?= get_image( $image, 'full' ); ?>
            </figure>
            <div class="block-content-with-list__content">

                <?php if ($title) : ?>
                    <h2 class="block-content-with-list__title heading-underline heading-dot fade-in"><?= $title; ?></h2>
                <?php endif; ?>
                <?php if ($sub_title) : ?>
                    <h3 class="block-content-with-list__sub-title fade-in"><?php echo $sub_title; ?></h3>
                <?php endif; ?>
                <?php if ($description) : ?>
                    <h4 class="block-content-with-list__description fade-in"><?php echo $description; ?></h4>
                <?php endif; ?>

                <div class="block-content-with-list__text fade-in">
                    <?= $content; ?>
                </div>
                <div class="block-content-with-list__list block-content-with-list__list--<?= $button_style;?> js-delay fade-in">
                    <?php foreach ( $list as $item ) :
                        ?>
                       <div class="block-content-with-list__item">
                           <div class="block-content-with-list__item-icon">
                               <?= get_image($item['icon']); ?>
                           </div>
                           <div class="block-content-with-list__item-content">
                                <h4 class="block-content-with-list__item-title">
                                    <?= $item['title']; ?>
                                </h4>
                                <p class="block-content-with-list__item-text">
                                    <?= $item['content']; ?>
                                </p>
                           </div>
                        </div>

                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <?php if($background_color !=='white'): ?>
        <?php get_theme_part('elements/triangle', ['position' => 'bottom-right']); ?>
    <?php endif; ?>
</section>
