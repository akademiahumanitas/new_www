<?php

$title = get_field( 'title' );
$content = get_field( 'content' );
$column_style = get_field( 'column_style' ); // with-image / with-icon
$columns = get_field( 'columns' ); // repeater with icon / image, title, content
$background_color = get_field( 'background_color' ); // white / light-blue / dark-blue
$block_ID = $block['id'];

$is_hidden = get_field('is_hidden');
$sub_title = get_field('sub_title');
$description = get_field('description');

if($background_color === 'dark-blue' && str_word_count($title, 0, 'ąćęłńóśźżĄĆĘŁŃÓŚŹŻ!?()[]{}') > 1) {
    $title = preg_replace('/\b([\p{L}]+)$/u','<span class="text-highlight">$1</span>', $title);
}

?>

<?php if(!$is_hidden) : ?>
	<section class="block-content-with-columns block-content-with-columns--<?= $background_color; ?>" id="<?= $block_ID; ?>">
		<?php if($background_color !=='white'): ?>
			<?php get_theme_part('elements/triangle', ['position' => 'top-right']); ?>
		<?php endif; ?>
		<div class="container">
			<?php if ($title) : ?>
				<h2 class="block-content-with-columns__title heading-underline heading-dot fade-in"><?= $title; ?></h2>
			<?php endif; ?>
			<div class="block-content-with-columns__content fade-in">
				<?= $content; ?>
			</div>
			<div class="block-content-with-columns__columns">
				<?php foreach($columns as $column) : ?>
					<div class="block-content-with-columns__column block-content-with-columns__column--<?= $column_style; ?> js-delay fade-in">
						<?php if($column_style === 'with-image') : ?>
							<figure class="block-content-with-columns__column-image">
								<?= get_image($column['image']); ?>
							</figure>
						<?php endif; ?>
						<?php if($column_style === 'with-icon') : ?>
							<figure class="block-content-with-columns__column-icon"><?= get_image($column['icon']); ?></figure>
						<?php endif; ?>
						<div class="block-content-with-columns__column-wrapper">
							<h3 class="block-content-with-columns__column-title"><?= $column['title']; ?></h3>
							<div class="block-content-with-columns__column-content"><?= $column['content']; ?></div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<?php if ($sub_title) : ?>
				<h3 class="block-content-with-columns__sub-title fade-in"><?php echo $sub_title; ?></h3>
			<?php endif; ?>
			<?php if ($description) : ?>
				<h4 class="block-content-with-columns__description fade-in"><?php echo $description; ?></h4>
			<?php endif; ?>
		</div>
		<?php if($background_color !=='white'): ?>
			<?php get_theme_part('elements/triangle', ['position' => 'bottom-left']); ?>
		<?php endif; ?>
	</section>
<?php endif; ?>