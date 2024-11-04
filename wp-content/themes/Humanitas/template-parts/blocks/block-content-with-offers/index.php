<?php

$title = get_field('title');
$sub_title = get_field('sub_title');
$description = get_field('description');

$content = get_field( 'content' );
$image = get_field( 'image' );
$offers = get_field( 'offers' ); // repeater with offer - offer post type id, title

$block_ID = $block['id'];
$is_hidden = get_field('is_hidden');
?>

<?php if(!$is_hidden) : ?>
	<section class="block-content-with-offers" id="<?= $block_ID; ?>">
		<div class="container">
			<div class="block-content-with-offers__wrapper">
				<figure class="block-content-with-offers__image fade-in">
					<?= get_image($image); ?>
				</figure>
				<div class="block-content-with-offers__container">
					<?php if ($title) : ?>
						<h2 class="block-content-with-offers__title heading-underline heading-dot fade-in"><?= $title; ?></h2>
					<?php endif; ?>
					<?php if ($sub_title) : ?>
						<h3 class="block-content-with-offers__sub-title fade-in"><?php echo $sub_title; ?></h3>
					<?php endif; ?>
					<?php if ($description) : ?>
						<h4 class="block-content-with-offers__description fade-in"><?php echo $description; ?></h4>
					<?php endif; ?>
					<div class="block-content-with-offers__content fade-in">
						<?= $content; ?>
					</div>
					<div class="block-content-with-offers__offers">
						<?php foreach($offers as $offer) : ?>
							<h4 class="block-content-with-offers__offer-title fade-in"><?= $offer['title']; ?></h4>
							<div class="block-content-with-offers__offer-items fade-in js-delay">
								<?php foreach($offer['offers'] as $offer_id) : ?>
									<?php get_theme_part('elements/offer-card', ['post_ID' => $offer_id, 'class' => 'js-delay-item']); ?>
								<?php endforeach; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>