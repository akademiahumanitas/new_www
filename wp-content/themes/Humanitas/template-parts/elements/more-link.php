<?php 
    $link = $link ?? get_field('link');
    $class = $class ?? '';
    if ($link) : ?>
    <a 
        href="<?= $link['url']; ?>" 
        class="more-link fade-in <?= $class; ?>"
        target="<?= $link['target']; ?>"
        rel="<?= $link['target'] === '_blank' ? 'noopener noreferrer' : ''; ?>"
        title="<?= $link['title']; ?>"
        aria-label="<?= $link['title']; ?>" 
    >
        <span class="more-link__text"><?= $link['title']; ?></span>
        <span class="more-link__icon">
            <?= get_image('arrow-up-right'); ?>
        </span>
    </a>
<?php endif; ?>