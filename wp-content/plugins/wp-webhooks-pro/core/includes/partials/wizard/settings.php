<?php

$wpwh_plugin_name   = WPWHPRO()->settings->get_page_title();
$settings = WPWHPRO()->settings->get_settings();
$step_count = intval( WPWHPRO()->wizard->get_current_step_number() );

$log_setting_key = 'wpwhpro_autoclean_logs';
$log_setting = isset( $settings[ $log_setting_key ] ) ? $settings[ $log_setting_key ] : array();
$log_is_checked = '';
$log_value = '1';

if( ! empty( $log_setting ) ){
	$log_is_checked = ( $log_setting['type'] == 'checkbox' && $log_setting['value'] == 'yes' ) ? 'checked' : '';
	$log_value = ( $log_setting['type'] != 'checkbox' ) ? $log_setting['value'] : '1';
}
?>
<header class="wpwh-wizard__header">
	<h2><?php echo sprintf( __( 'Step %d', 'wp-webhooks' ), $step_count ); ?></h2>
	<p><?php echo __( 'Optimize your performance', 'wp-webhooks' ); ?></p>
</header>
<div class="wpwh-separator"></div>
<div class="wpwh-wizard__main">
	
	<div class="wpwh-form-field">
		<label for="form_1" class="wpwh-form-label"><?php echo __( 'Auto-clean logs after x days?', 'wp-webhooks' ); ?></label>
		<p class="wpwh-form-description"><?php echo __( 'By default, we collect all logs without ever deleting them. Activating this setting results in the logs being automatically deleted in case they are older than x days.', 'wp-webhooks' ); ?></p>
		<select
			class="wpwh-form-input"
			name="wpwh_wizard_log_autoclean"
		>
			<?php if( isset( $log_setting['choices'] ) ) : ?>
				<?php foreach( $log_setting['choices'] as $choice_name => $choice_label ) :

					//Compatibility with 4.3.0
					if( is_array( $choice_label ) ){
						if( isset( $choice_label['label'] ) ){
							$choice_label = $choice_label['label'];
						} else {
							$choice_label = $choice_name;
						}
					}

					$selected = '';
					if( is_array( $log_setting['value'] ) ){
						if( isset( $log_setting['value'][ $choice_name ] ) ){
							$selected = 'selected="selected"';
						}
					} else {
						if( (string) $log_setting['value'] === (string) $choice_name ){
							$selected = 'selected="selected"';
						}
					}
				?>
				<option value="<?php echo $choice_name; ?>" <?php echo $selected; ?>><?php echo __( $choice_label, 'wp-webhooks' ); ?></option>
				<?php endforeach; ?>
			<?php endif; ?>
		</select>
	</div>
	
</div>