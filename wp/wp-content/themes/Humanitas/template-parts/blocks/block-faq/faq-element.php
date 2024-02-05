<?php 
?>
<details class="block-faq__item">
    <summary class="block-faq__item-title">
        <?= $item['question']; ?>
        <span class="block-faq__item-icon">
            <?= get_image('chevron-up'); ?>
        </span>
    </summary>
    <div class="block-faq__item-content">
        <?= $item['answer']; ?>
    </div>
</details>