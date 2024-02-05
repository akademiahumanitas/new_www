<?php
    $faq = $faq ?? get_field('faq');
?>
<?php get_theme_part('blocks/block-faq/index', [
    'faq' => $faq,
    'section_title' => __('Najczęściej zadawane pytania (FAQ)', 'humanitas'),
]); ?>