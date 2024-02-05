<?php
/**
 * Block Hero Slider
 */

$block_ID = $block['id'];
$slides = get_field( 'slides' );
$slider_text = get_field( 'text' );
?>

<section class="block-hero-slider fade-in" id="<?php echo $block_ID; ?>">
    <div class="container">
        <div class="block-hero-slider__slider">
            <?php foreach ( $slides as $slide ) :
                    $image = $slide['image'];
                    $text = $slide['text'];
                    $link = $slide['link'];
                ?>
                <div class="block-hero-slider__slide">
                    <figure class="block-hero-slider__slide-image">
                        <?php echo get_image( $image, 'full', 'block-hero-slider__slide-image-img' ); ?>
                    </figure>
                    <h3 class="block-hero-slider__slide-text heading-dot heading-dot--big fade-in">
                        <?php if ( $link ) : ?>
                            <a href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>" aria-label="<?php echo __( 'Link to', 'humanitas' ) . ' ' . $text; ?>" title="<?php echo $text; ?>" class="block-hero-slider__slide-text-link"><?php echo $text; ?></a>
                        <?php else : ?>
                            <?php echo $text; ?>
                        <?php endif; ?>
                    </h3>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="block-hero-slider__text">
            <?= $slider_text; ?>
        </div>
    </div>
    <div class="block-hero-slider__triangle">
        <?= get_image('triangle-bottom-left'); ?>
    </div>
</section>