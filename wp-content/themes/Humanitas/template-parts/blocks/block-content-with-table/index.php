<?php 

$title = get_field( 'title' );
$sub_title = get_field( 'sub_title' );
$description = get_field( 'description' );
$content = get_field( 'content' );
$table = get_field( 'table' ); // has 4 colimns col_1, col_2, col_3, col_4
$information = get_field( 'information' ); // repeater with title, content
$is_hidden = get_field('is_hidden');

$block_ID = $block['id'];

$col_amount = get_field('col_amount');
$class = "cell-size-{$col_amount}";
$row_index = 0;

if(str_word_count($title, 0, 'ąćęłńóśźżĄĆĘŁŃÓŚŹŻ') > 1) {
    $title = preg_replace('/\b([\p{L}]+)$/u','<span class="text-highlight">$1</span>', $title);
}

?>
<?php if(!$is_hidden) : ?>
	<section class="block-content-with-table" id="<?= $block_ID; ?>">
		<?php get_theme_part('elements/triangle', ['position' => 'top-right']); ?>
		<div class="container">
			<?php if ($title) : ?>
				<h2 class="block-content-with-table__title heading-underline heading-dot fade-in"><?= $title; ?></h2>
			<?php endif; ?>
            <div class="block-content-with-table__content fade-in">
				<?= $content; ?>
            </div>

			<div class="block-content-with-table__information">
				<?php foreach($information as $info) : ?>
					<div class="block-content-with-table__information-item fade-in">
						<h3 class="block-content-with-table__information-title"><?= $info['title']; ?></h3>
						<div class="block-content-with-table__information-content"><?= $info['content']; ?></div>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="block-content-with-table__table fade-in">
				<div class="block-content-with-table__table-wrapper">
					<?php foreach($table as $row) : ?>
						<div class="block-content-with-table__table-row">
							<?php for ($i = 1; $i <= 5; $i++): ?>
								<?php if ($i === 1 && $row_index === 0): ?>
									<div class="block-content-with-table__table-cell <?= esc_attr($class); ?>">
										<?= esc_html($row["col_$i"] ?? ''); ?>
									</div>
								<?php elseif (!empty($row["col_$i"])): ?>
									<div class="block-content-with-table__table-cell <?= esc_attr($class); ?>">
										<?= esc_html($row["col_$i"]); ?>
									</div>
								<?php endif; ?>
							<?php endfor; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<?php if ($sub_title) : ?>
				<h3 class="block-content-with-table__sub-title fade-in"><?php echo $sub_title; ?></h3>
			<?php endif; ?>
			<?php if ($description) : ?>
				<h4 class="block-content-with-table__description fade-in"><?php echo $description; ?></h4>
			<?php endif; ?>

		</div>
		<?php get_theme_part('elements/triangle', ['position' => 'bottom-left']); ?>
	</section>
<?php endif; ?>