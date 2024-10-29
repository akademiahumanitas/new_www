<div class="site-footer__column">
    <?php
    foreach ( $column as $column ) :
        $column_title = $column['column_title'] ?? '';
        $links = $column['links'] ?? array();
    ?>
        <h3 class="site-footer__column-title"><?php echo $column_title; ?></h3>
        <ul class="site-footer__column-links">
            <?php foreach ( $links as $single_row ) : ?>
                <?php $link = $single_row['link'] ?? array(); ?>
                <li><a href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>" class="site-footer__link"><?php echo $link['title']; ?></a></li>
            <?php endforeach; ?>
        </ul>

    <?php endforeach; ?>
</div>