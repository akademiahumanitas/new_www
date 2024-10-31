<?php

$post_ID = $post_ID ?? get_the_ID();
$position = get_field('position', $post_ID);
$position_description = get_field('position_description', $post_ID);
$name = get_the_title($post_ID);
// post thumbnail id
$thumbnail = get_post_thumbnail_id($post_ID);
$phone_number = get_field('phone_number', $post_ID);
$email_address = get_field('email_address', $post_ID);
$additional_email_address = get_field('additional_email_address', $post_ID);
$additional_phone_number = get_field('additional_phone_number', $post_ID);
$class = $class ?? '';
$version = $version ?? 'primary';

?>
<div class="person-card person-card--<?= $version; ?> <?= $class; ?>">
    <figure class="person-card__image">
        <?php if($thumbnail) : ?>
            <?= wp_get_attachment_image($thumbnail, 'full', false, array('class' => 'person-card__image-img')); ?>
        <?php else : ?>
            <?= get_image('user-line'); ?>
        <?php endif; ?>
    </figure>
    <div class="person-card__content">
        <div class="person-card__position"><?= $position; ?></div>
		<?php if ($position_description) : ?>
			<div class="person-card__position-description"><?= $position_description; ?></div>
		<?php endif; ?>
        <h3 class="person-card__name"><?= $name; ?></h3>
        <?php if($version === 'primary') : ?>
            <div class="person-card__contact">
                <?php if ($phone_number) : ?>
                    <a href="tel:<?= $phone_number; ?>" class="person-card__contact-item">
                        <span class="person-card__contact-icon">
                            <?= get_image('phone'); ?>
                        </span>
                        <span class="person-card__contact-text"><?= $phone_number; ?></span>
                    </a>
                <?php endif; ?>

				<?php if( $additional_phone_number ): ?>
			        <?php foreach ( $additional_phone_number as $phone_number ) : ?>
                        <?php if (!empty($phone_number['phone_number'])) : ?>
                            <a href="tel:<?= $phone_number; ?>" class="person-card__contact-item">
                                <span class="person-card__contact-icon">
                                    <?= get_image('phone'); ?>
                                </span>
                                <span class="person-card__contact-text"><?= $phone_number['phone_number']; ?></span>
                            </a>
                        <?php endif; ?>
			        <?php endforeach; ?>
		        <?php endif; ?>
				
                <?php if ($email_address) : ?>
                    <a href="mailto:<?= $email_address; ?>" class="person-card__contact-item">
                        <span class="person-card__contact-icon">
                            <?= get_image('email'); ?>
                        </span>
                        <span class="person-card__contact-text"><?= $email_address; ?></span>
                    </a>
                <?php endif; ?>
                <?php if( $additional_email_address ): ?>
			        <?php foreach ( $additional_email_address as $email ) : ?>
                        <?php if (!empty($email['single_mail'])) : ?>
                            <a href="mailto:<?= esc_attr($email['single_mail']); ?>" class="person-card__contact-item">
                                <span class="person-card__contact-icon">
                                    <?= get_image('email'); ?>
                                </span>
                                <?= esc_html($email['single_mail']); ?>
                            </a>
                        <?php endif; ?>
			        <?php endforeach; ?>
		        <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>