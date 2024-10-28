<?php 

$title = get_field( 'title' );
$content = get_field( 'content' );
$table = get_field( 'table' ); // has 4 colimns col_1, col_2, col_3, col_4
$information = get_field( 'information' ); // repeater with title, content

$block_ID = $block['id'];

?>
<section class="block-content-with-table" id="<?= $block_ID; ?>">
    <?php get_theme_part('elements/triangle', ['position' => 'top-right']); ?>
    <div class="container">
        <h2 class="block-content-with-table__title heading-underline heading-dot fade-in"><?= $title; ?></h2>
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
        <div class="block-content-with-table__table">
            <div class="block-content-with-table__table-wrapper">
                <?php foreach($table as $row) : ?>
                    <div class="block-content-with-table__table-row">
                        <div class="block-content-with-table__table-cell"><?= $row['col_1']; ?></div>
                        <div class="block-content-with-table__table-cell"><?= $row['col_2']; ?></div>
                        <div class="block-content-with-table__table-cell"><?= $row['col_3']; ?></div>
                        <div class="block-content-with-table__table-cell"><?= $row['col_4']; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php get_theme_part('elements/triangle', ['position' => 'bottom-left']); ?>
</section>