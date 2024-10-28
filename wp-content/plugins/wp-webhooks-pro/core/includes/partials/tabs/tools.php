<?php

/*
 * Settings Template
 */

$settings = WPWHPRO()->settings->get_settings();
$clear_page_url = WPWHPRO()->helpers->get_current_url( true, true );
$tools_export_url = WPWHPRO()->helpers->built_url( $clear_page_url, array_merge( $_GET, array( 'create_plugin_export' => 'yes', ) ) );
$tools_import_nonce_data = WPWHPRO()->settings->get_tools_import_nonce();
$wizard_nonce_data = WPWHPRO()->settings->get_wizard_nonce();

//Import/Export logic
if( ! empty( $_FILES['wpwh_plugin_import_file'] ) && isset( $_FILES['wpwh_plugin_import_file'] ) ){

	if ( check_admin_referer( $tools_import_nonce_data['action'], $tools_import_nonce_data['arg'] ) ) {

		if( WPWHPRO()->helpers->current_user_can( WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-tools-import-plugin-data' ), 'wpwhpro-page-tools-import-plugin-data' ) ){

			if ( empty( $_FILES['wpwh_plugin_import_file']['size'] ) ) {
				echo WPWHPRO()->helpers->create_admin_notice( 'No file selected.', 'warning', true );
			} else {
				$file = $_FILES['wpwh_plugin_import_file'];

				if ( $file['error'] ) {
					echo WPWHPRO()->helpers->create_admin_notice( 'An error occured while uploading the file. Please try again.', 'warning', true );
				} else {

					if ( pathinfo( $file['name'], PATHINFO_EXTENSION ) !== 'txt' ) {
						echo WPWHPRO()->helpers->create_admin_notice( 'The uploaded file has an incorrect file type.', 'warning', true );
					} else {

						$file_content = file_get_contents( $file['tmp_name'] );
						$base64 = base64_decode( $file_content );

						if( ! WPWHPRO()->helpers->is_json( $base64 ) ){
							echo WPWHPRO()->helpers->create_admin_notice( 'The import file is corrupt and cannot be imported.', 'warning', true );
						} else {

							$json = json_decode( $base64, true );
			
							if ( ! $json || ! is_array( $json ) ) {
								echo WPWHPRO()->helpers->create_admin_notice( 'The import data cannot be empty.', 'warning', true );
							} else {
								$import_errors = WPWHPRO()->tools->import_plugin_export( $json );
						
								if( empty( $import_errors ) ){
									echo WPWHPRO()->helpers->create_admin_notice( 'The plugin import was successful.', 'success', true );
								} else {
									echo WPWHPRO()->helpers->create_admin_notice( 'One or multiple errors occured. Please see the notices down below.', 'warning', true );
									foreach( $import_errors as $error ){
										echo WPWHPRO()->helpers->create_admin_notice( esc_html( $error ), 'warning', true );
									}
								}
							}

						}

					}

				}
				
			}

		} else {
			echo WPWHPRO()->helpers->create_admin_notice( 'You don\'t have permission to import data.', 'warning', true );
		}
  
	}
}

?>
<div class="wpwh-container">

		<div class="wpwh-title-area mb-4">
			<h2><?php echo __( 'Tools', 'wp-webhooks' ); ?></h2>
			<?php if( WPWHPRO()->whitelabel->is_active() && ! empty( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_tools_custom_text_settings' ) ) ) : ?>
				<p><?php echo __( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_tools_custom_text_settings' ), 'wp-webhooks' ); ?></p>
			<?php else : ?>
				<p><?php echo sprintf( __( 'Down below you will find a list of all available tools we offer for %s.', 'wp-webhooks' ), WPWHPRO()->settings->get_page_title() ); ?></p>
			<?php endif; ?>
		</div>

		<div class="wpwh-box wpwh-box--big mb-3">
			<div class="wpwh-box__body">
				<h2><?php echo __( 'Relaunch wizard', 'wp-webhooks' ); ?></h2>
				<p class="mb-4">
					<?php echo sprintf( __( 'Using the button below, you can relaunch the setup wizard.', 'wp-webhooks' ), $this->page_title ); ?>
				</p>
				<form id="wpwh-relaunch-wizard-form" method="post" action="">
					<button type="submit" class="wpwh-btn wpwh-btn--secondary" name="wpwhpro_tools_relaunch_wizard">Relaunch wizard</button>

					<?php echo WPWHPRO()->helpers->get_nonce_field( $wizard_nonce_data ); ?>
				</form>
			</div>
		</div>

		<div class="wpwh-box wpwh-box--big mb-3">
			<div class="wpwh-box__body">
				<h2><?php echo __( 'Import / Export plugin data', 'wp-webhooks' ); ?></h2>
				<p class="mb-4">
					<?php echo sprintf( __( 'This tool allows you to import or export plugin data for %s. Down below is a list of what the export file includes and what not:', 'wp-webhooks' ), $this->page_title ); ?>
				</p>
				<h4><?php echo __( 'Included in export', 'wp-webhooks' ); ?></h4>
				<ul class="wpwh-checklist wpwh-checklist--two-col">
					<li><?php echo __( 'All Flows', 'wp-webhooks' ); ?></li>
					<li><?php echo __( 'All "Send Data" URLs and settings', 'wp-webhooks' ); ?></li>
					<li><?php echo __( 'All "Receive Data" URLs and settings', 'wp-webhooks' ); ?></li>
					<li><?php echo __( 'All Authentication and Data Mapping templates', 'wp-webhooks' ); ?></li>
					<li><?php echo __( 'The IP Whitelist configuration', 'wp-webhooks' ); ?></li>
					<li><?php echo __( 'All of the plugin settings', 'wp-webhooks' ); ?></li>
				</ul>
				<h4><?php echo __( 'Not included in export', 'wp-webhooks' ); ?></h4>
				<ul class="wpwh-checklist wpwh-checklist--two-col">
					<li><?php echo __( 'The Logs data', 'wp-webhooks' ); ?></li>
					<li><?php echo __( 'The license', 'wp-webhooks' ); ?></li>
					<li><?php echo __( 'Whitelist requests', 'wp-webhooks' ); ?></li>
					<li><?php echo __( 'Whitelabel data', 'wp-webhooks' ); ?></li>
					<li><?php echo __( 'Integrations', 'wp-webhooks' ); ?></li>
					<li><?php echo __( 'Extensions', 'wp-webhooks' ); ?></li>
					<li><?php echo __( 'Backup data from previous versions', 'wp-webhooks' ); ?></li>
				</ul>
				<ul class="wpwh-checklist--two-col">
					<li class="p-2">
						<p><strong><?php echo __( 'Export', 'wp-webhooks' ); ?></strong></p>
						
						<p class="wpwh-text-small"><?php echo __( 'To create a plugin export, please click the button down below. This will start an importable file download.', 'wp-webhooks' ); ?></p>
						<a title="<?php echo __( 'Download export file', 'wp-webhooks' ); ?>" class="wpwh-btn wpwh-btn--secondary" href="<?php echo $tools_export_url; ?>"><?php echo __( 'Download export file', 'wp-webhooks' ); ?></a>
					</li>
					<li class="p-2">
						<p><strong><?php echo __( 'Import', 'wp-webhooks' ); ?></strong></p>
						<p class="wpwh-text-small"><?php echo __( 'Import an existing data export from any other version. If you want to import an export string from an older version, please save it within a .txt file first.', 'wp-webhooks' ); ?></p>
						<p class="wpwh-text-small wpwh-text-danger"><?php echo __( 'Please note: Importing a plugin configuration will reset the plugin and fill it with all the import data. The data not included in the export will be lost. This action is irreversible.', 'wp-webhooks' ); ?></p>
						<form id="wpwh-import-export-form" enctype="multipart/form-data" method="post" action="">
							<div class="wpwh-file-uploader mt-4 mb-4">
								<input type="file" id="wpwh_plugin_import_file" name="wpwh_plugin_import_file" accept=".txt">
							</div>
							<button type="submit" class="wpwh-btn wpwh-btn--secondary" name="wpwhpro_tools_import_plugin"><?php echo __( 'Import', 'wp-webhooks' ); ?></button>

							<?php echo WPWHPRO()->helpers->get_nonce_field( $tools_import_nonce_data ); ?>
						</form>
					</li>
				</ul>
			
				
			</div>
		</div>

		<div class="wpwh-box wpwh-box--big mb-3">
			<div class="wpwh-box__body">
				<h2><?php echo __( 'Create system report', 'wp-webhooks' ); ?></h2>
				<p class="mb-4">
					<?php echo sprintf( __( 'Use the button below to create a system report file your current system. This will automatically download the system report file.', 'wp-webhooks' ), $this->page_title ); ?>
				</p>

				<form id="wpwh-create-system-export-form" method="post" action="">
					<button type="submit" class="wpwh-btn wpwh-btn--secondary" name="wpwhpro_tools_create_system_report"><?php echo __( 'Download system export', 'wp-webhooks' ); ?></button>

					<?php echo WPWHPRO()->helpers->get_nonce_field( $tools_import_nonce_data ); ?>
				</form>
			</div>
		</div>

</div>