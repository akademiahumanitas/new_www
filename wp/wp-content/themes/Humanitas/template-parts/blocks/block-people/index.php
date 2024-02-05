<?php
    $section_title = $section_title ?? get_field('section_title');
    $people = $people ?? get_field('people');
    $block_ID = $block['id'];
    if($people) :
?>
<section class="block-people" id="<?= $block_ID; ?>">
    <div class="container">
        <h3 class="block-people__title fade-in"><?= $section_title; ?></h3>
        <div class="block-people__wrapper fade-in js-delay">
            <?php foreach ($people as $person) : ?>
                <?php get_theme_part('elements/person-card', [
                    'post_ID' => $person,
                    'class' => 'js-delay-item',
                ]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>