<?php

/**
 * WP_Webhooks_Pro_Tools Class
 *
 * This class contains all of the available tools functions
 *
 * @since 5.0
 */

/**
 * The tools class of the plugin.
 *
 * @since 5.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Tools {

	/**
	 * The executional part of the tools
	 *
	 * @since 6.1.1
	 * @return void
	 */
	public function execute(){

		add_action( 'admin_init', array( $this, 'maybe_create_plugin_export' ), 20 );
		add_action( 'admin_init', array( $this, 'maybe_create_system_report_export' ), 20 );

	}

	/**
	 * Maybe create a plugin export 
	 *
	 * @since 6.1.1
	 * @return void
	 */
	public function maybe_create_plugin_export(){

		if( ! isset( $_GET['create_plugin_export'] ) ){
			return;
		}

		if( ! WPWHPRO()->helpers->is_page( WPWHPRO()->settings->get_page_name() ) ){
			return;
		}

		if( ! WPWHPRO()->helpers->current_user_can( WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-tools-export-plugin-data' ), 'wpwhpro-page-tools-export-plugin-data' ) ){
			return;
		}

		$plugin_export = WPWHPRO()->tools->generate_plugin_export();

		$file_name = 'wpwh-export-v' . str_replace( '.', '-', WPWHPRO_VERSION ) . '-' . date( 'Y-m-d-H-i-s' ) . '.txt';

		$export_string = base64_encode( json_encode( $plugin_export ) );

		$stream_args = array(
			'headers' => array(
				'Content-Description' 	=> 'File Transfer',
				'Content-Disposition' 	=> 'attachment; filename=' . $file_name,
				'Content-type' 			=> 'text/plain; charset=utf-8',
				'Content-Length' 		=> strlen( $export_string ),
			),
			'content' => $export_string
		);

		WPWHPRO()->helpers->stream_file( $stream_args );
	}

	/**
	 * Maybe create a system report export 
	 *
	 * @since 6.1.1
	 * @return void
	 */
	public function maybe_create_system_report_export(){

		if( ! isset( $_POST['wpwhpro_tools_create_system_report'] ) ){
			return;
		}

		if( ! WPWHPRO()->helpers->is_page( WPWHPRO()->settings->get_page_name() ) ){
			return;
		}

		if( ! WPWHPRO()->helpers->current_user_can( WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-tools-create-system-report' ), 'wpwhpro-page-tools-create-system-report' ) ){
			return;
		}

		$tools_import_nonce_data = WPWHPRO()->settings->get_tools_import_nonce();

		if ( ! check_admin_referer( $tools_import_nonce_data['action'], $tools_import_nonce_data['arg'] ) ){
			return;
		}

		$plugin_system_report = WPWHPRO()->system->generate_report();

		$file_name = 'wpwh-system-report-export-v' . str_replace( '.', '-', WPWHPRO_VERSION ) . '-' . date( 'Y-m-d-H-i-s' ) . '.json';

		$export_string = json_encode( $plugin_system_report );

		$stream_args = array(
			'headers' => array(
				'Content-Description' 	=> 'File Transfer',
				'Content-Disposition' 	=> 'attachment; filename=' . $file_name,
				'Content-type' 			=> 'text/plain; charset=utf-8',
				'Content-Length' 		=> strlen( $export_string ),
			),
			'content' => $export_string
		);

		WPWHPRO()->helpers->stream_file( $stream_args );
	}
	
	/**
	 * Create an export of the given plugin data
	 *
	 * @return array
	 */
	public function generate_plugin_export(){

		$export_data = array(
			'webhook_options' => WPWHPRO()->webhook->get_hooks(),
			'flows' => WPWHPRO()->flows->get_flows(),
			'authentication_templates' => WPWHPRO()->auth->template_query( array( 'items_per_page' => -1 ) ),
			'data_mapping_templates' => WPWHPRO()->data_mapping->get_data_mapping(),
			'whitelist' => WPWHPRO()->whitelist->get_list(),
			'settings' => WPWHPRO()->settings->get_settings(),
		);

		return apply_filters( 'wpwhpro/tools/generate_plugin_export', $export_data );
	}

	public function import_plugin_export( $data ){
		$errors = array();

		$data = apply_filters( 'wpwhpro/tools/import_plugin_export', $data );

		if( empty( $data ) ){
			return $errors;
		}

		if( is_string( $data ) && WPWHPRO()->helpers->is_json( $data ) ){
			$data = json_decode( $data, true );
		}

		if( ! is_array( $data ) ){
			$errors[] = __( 'The given import data could not be validated.', 'wp-webhooks' );
			return $errors;
		}

		//reset the existing data
		WPWHPRO()->webhook->reset_wpwhpro();

		// Add all webhook related settings
		if( isset( $data['webhook_options'] ) && ! empty( $data['webhook_options'] ) ){
			$webhook_options_key = WPWHPRO()->settings->get_webhook_option_key();
			update_option( $webhook_options_key, $data['webhook_options'] );
		}

		// Add all flow settings
		if( isset( $data['flows'] ) && ! empty( $data['flows'] ) && is_array( $data['flows'] ) ){
			foreach( $data['flows'] as $flow_id => $flow_data ){
				$check = WPWHPRO()->flows->add_flow( $flow_data );
				if( empty( $check ) ){
					$errors[] = sprintf( __( 'There was an issue creating the flow with the id: %d', 'wp-webhooks' ), intval( $flow_id ) );
				}
			}
		}

		// Add all authentication templates
		if( isset( $data['authentication_templates'] ) && ! empty( $data['authentication_templates'] ) && is_array( $data['authentication_templates'] ) ){
			foreach( $data['authentication_templates'] as $auth_id => $auth_data ){

				if( 
					! isset( $auth_data['auth_type'] ) 
					|| ! isset( $auth_data['id'] ) 
					|| ! isset( $auth_data['name'] )
				){
					continue;
				}

				$authentication_args = array(
					'id' => $auth_data['id'],
				);

				if( isset( $auth_data['template'] ) && ! empty( $auth_data['template'] ) ){
					$authentication_args['template'] = base64_decode( $auth_data['template'] );
				}

				$check = WPWHPRO()->auth->add_template( $auth_data['name'], $auth_data['auth_type'], $authentication_args );
				if( empty( $check ) ){
					$errors[] = sprintf( __( 'There was an issue creating the authentication template with the id: %d', 'wp-webhooks' ), intval( $auth_id ) );
				}
			}
		}

		// Add all data mapping templates
		if( isset( $data['data_mapping_templates'] ) && ! empty( $data['data_mapping_templates'] ) && is_array( $data['data_mapping_templates'] ) ){
			foreach( $data['data_mapping_templates'] as $mapping_id => $mapping_data ){

				if( ! isset( $mapping_data['name'] ) ){
					continue;
				}

				if( isset( $mapping_data['template'] ) && ! empty( $mapping_data['template'] ) ){
					$mapping_data['template'] = base64_decode( $mapping_data['template'] );
				}

				$check = WPWHPRO()->data_mapping->add_template( $mapping_data['name'], $mapping_data );
				if( empty( $check ) ){
					$errors[] = sprintf( __( 'There was an issue creating the data mapping template with the id: %d', 'wp-webhooks' ), intval( $mapping_id ) );
				}
			}
		}

		// Add all whitelist items
		if( isset( $data['whitelist'] ) && ! empty( $data['whitelist'] ) && is_array( $data['whitelist'] ) ){
			foreach( $data['whitelist'] as $whitelist_key => $whitelist_ip ){

				if( empty( $whitelist_ip ) ){
					continue;
				}

				$check = WPWHPRO()->whitelist->add_item( esc_html( $whitelist_ip ), array( 'key' => $whitelist_key ) );
				if( empty( $check ) ){
					$errors[] = sprintf( __( 'There was an issue creating the whitelist item with the id: %s', 'wp-webhooks' ), sanitize_title( $whitelist_key ) );
				}
			}
		}

		// Add all settings data
		if( isset( $data['settings'] ) && ! empty( $data['settings'] ) && is_array( $data['settings'] ) ){
			foreach( $data['settings'] as $settings_key => $settings_data ){

				if( ! is_array( $settings_data ) || ! isset( $settings_data['value'] ) || $settings_data['value'] === '' ){
					continue;
				}

				update_option( $settings_key, $settings_data['value'] );
			}
		}

		//initiate the integrations installer
		if( isset( $data['webhook_options'] ) && ! empty( $data['webhook_options'] ) ){

			$integrations = array();
			if( is_array( $data['webhook_options'] ) ){

				if( isset( $data['webhook_options']['action'] ) ){
					foreach( $data['webhook_options']['action'] as $action_group => $action_data ){
						if( ! empty( $action_data ) ){
							foreach( $action_data as $action ){
								if( isset( $action['integration'] ) && ! empty( $action['integration'] ) ){
									$integration = sanitize_title( $action['integration'] );
									$integrations[ $integration ] = $integration;
								}
							}
						}
					}
				}

			}

			if( ! empty( $integrations ) ){
				//make sure to reinstall all to provide the correct version
				WPWHPRO()->integrations->maybe_install_integrations( $integrations );
			}
			
		}
		

		return $errors;
	}
}
