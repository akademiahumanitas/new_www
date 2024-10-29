<?php

/*
 * Settings Template
 */

$settings = WPWHPRO()->settings->get_settings();
$settings_nonce_data = WPWHPRO()->settings->get_settings_nonce();

if( did_action( 'wpwh/admin/settings/settings_saved' ) ){
	echo WPWHPRO()->helpers->create_admin_notice( 'The settings have been successfully updated. Please refresh the page.', 'success', true );
}

?>
<div class="wpwh-container">

    <form id="wpwh-main-settings-form" method="post" action="">

		<div class="wpwh-title-area mb-4">
			<h2><?php echo __( 'Global Settings', 'wp-webhooks' ); ?></h2>
			<?php if( WPWHPRO()->whitelabel->is_active() && ! empty( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_settings' ) ) ) : ?>
				<p><?php echo __( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_settings' ), 'wp-webhooks' ); ?></p>
			<?php else : ?>
				<p><?php echo sprintf( __( 'Down below you can customize the global settings for %s. Please make sure to read the settings descriptions carefully before saving the settings.', 'wp-webhooks' ), WPWHPRO()->settings->get_page_title() ); ?></p>
			<?php endif; ?>
		</div>

		<div class="wpwh-settings">
			<?php foreach( $settings as $setting_name => $setting ) :

			if( isset( $setting['dangerzone'] ) && $setting['dangerzone'] ){
				continue;
			}

			$is_checked = ( $setting['type'] == 'checkbox' && $setting['value'] == 'yes' ) ? 'checked' : '';
			$value = ( $setting['type'] != 'checkbox' ) ? $setting['value'] : '1';

			$validated_atributes = '';
			if( isset( $setting['attributes'] ) ){
				foreach( $setting['attributes'] as $attribute_name => $attribute_value ){
					$validated_atributes .=  $attribute_name . '="' . $attribute_value . '" ';
				}
			}

			?>
			<div class="wpwh-setting">
				<div class="wpwh-setting__title">
				<label for="<?php echo $setting['id']; ?>"><?php echo $setting['label']; ?></label>
				</div>
				<div class="wpwh-setting__desc">
				<?php echo wpautop( $setting['description'] ); ?>
				</div>
				<div class="wpwh-setting__action d-flex justify-content-end wpwh-text-left " style="width:200px;max-width:200px;">
				<?php if( in_array( $setting['type'], array( 'checkbox' ) ) ) : ?>
					<div class="wpwh-toggle wpwh-toggle--on-off">
					<input type="<?php echo $setting['type']; ?>" id="<?php echo $setting['id']; ?>" name="<?php echo $setting_name; ?>" class="wpwh-toggle__input" <?php echo $is_checked; ?>>
					<label class="wpwh-toggle__btn" for="<?php echo $setting['id']; ?>"></label>
					</div>
				<?php elseif( in_array( $setting['type'], array( 'select' ) ) ) : ?>
					<select
						class="wpwh-form-input"
						name="<?php echo $setting_name; ?><?php echo ( isset( $setting['multiple'] ) && $setting['multiple'] ) ? '[]' : ''; ?>" <?php echo $validated_atributes; ?> <?php echo ( isset( $setting['multiple'] ) && $setting['multiple'] ) ? 'multiple' : ''; ?>
					>
						<?php if( isset( $setting['choices'] ) ) : ?>
							<?php foreach( $setting['choices'] as $choice_name => $choice_label ) :

								//Compatibility with 4.3.0
								if( is_array( $choice_label ) ){
									if( isset( $choice_label['label'] ) ){
										$choice_label = $choice_label['label'];
									} else {
										$choice_label = $choice_name;
									}
								}

								$selected = '';
								if( is_array( $setting['value'] ) ){
									if( isset( $setting['value'][ $choice_name ] ) ){
										$selected = 'selected="selected"';
									}
								} else {
									if( (string) $setting['value'] === (string) $choice_name ){
										$selected = 'selected="selected"';
									}
								}
							?>
							<option value="<?php echo $choice_name; ?>" <?php echo $selected; ?>><?php echo __( $choice_label, 'wp-webhooks' ); ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				<?php else : ?>
					<input id="<?php echo $setting['id']; ?>" name="<?php echo $setting_name; ?>" type="<?php echo $setting['type']; ?>" class="regular-text" value="<?php echo $value; ?>" />
				<?php endif; ?>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
		<div class="wpwh-text-center mt-4 pt-3">
			<button class="wpwh-btn wpwh-btn--secondary active" type="submit" name="wpwh_settings_submit">
			<span><?php echo __( 'Save All Settings', 'wp-webhooks' ); ?></span>
			</button>
		</div>

		<div class="wpwh-title-area mb-4 mt-4">
			<h2 class="wpwh-text-danger"><?php echo __( 'Danger Zone', 'wp-webhooks' ); ?></h2>
			<p class="wpwh-text-small"><?php echo sprintf( __( 'The settings down below are very powerful and have a huge impact to the functionality of the plugin. Please use them with caution.', 'wp-webhooks' ), WPWHPRO()->settings->get_page_title() ); ?></p>
		</div>

		<div class="wpwh-settings">
			<?php foreach( $settings as $setting_name => $setting ) :

			if( isset( $setting['dangerzone'] ) && ! $setting['dangerzone'] ){
				continue;
			}

			$is_checked = ( $setting['type'] == 'checkbox' && $setting['value'] == 'yes' ) ? 'checked' : '';
			$value = ( $setting['type'] != 'checkbox' ) ? $setting['value'] : '1';

			?>
			<div class="wpwh-setting">
				<div class="wpwh-setting__title">
				<label for="<?php echo $setting['id']; ?>"><?php echo $setting['label']; ?></label>
				</div>
				<div class="wpwh-setting__desc">
				<?php echo wpautop( $setting['description'] ); ?>
				</div>
				<div class="wpwh-setting__action">
				<?php if( in_array( $setting['type'], array( 'checkbox' ) ) ) : ?>
					<div class="wpwh-toggle wpwh-toggle--on-off">
					<input type="<?php echo $setting['type']; ?>" id="<?php echo $setting['id']; ?>" name="<?php echo $setting_name; ?>" class="wpwh-toggle__input" <?php echo $is_checked; ?>>
					<label class="wpwh-toggle__btn" for="<?php echo $setting['id']; ?>"></label>
					</div>
				<?php elseif( in_array( $setting['type'], array( 'select' ) ) ) : ?>
				<?php else : ?>
					<input id="<?php echo $setting['id']; ?>" name="<?php echo $setting_name; ?>" type="<?php echo $setting['type']; ?>" class="regular-text" value="<?php echo $value; ?>" />
				<?php endif; ?>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
		<div class="wpwh-text-center mt-4 pt-3">
			<button class="wpwh-btn wpwh-btn--secondary active" type="submit" name="wpwh_settings_submit">
			<span><?php echo __( 'Save All Settings', 'wp-webhooks' ); ?></span>
			</button>
		</div>

		<?php echo WPWHPRO()->helpers->get_nonce_field( $settings_nonce_data ); ?>
    </form>
</div>