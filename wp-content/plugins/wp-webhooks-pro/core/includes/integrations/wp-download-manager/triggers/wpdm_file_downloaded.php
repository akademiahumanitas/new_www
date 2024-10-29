<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Webhooks_Integrations_wp_download_manager_Triggers_wpdm_file_downloaded' ) ) :

	/**
	 * Load the wpdm_file_downloaded trigger
	 *
	 * @since 6.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_download_manager_Triggers_wpdm_file_downloaded {

		public function get_callbacks() {

			return array(
				array(
					'type'      => 'action',
					'hook'      => 'wpdm_onstart_download',
					'callback'  => array( $this, 'wpdm_file_downloaded_callback' ),
					'priority'  => 10,
					'arguments' => 1,
					'delayed'   => true,
				),
			);
		}

		public function get_details() {

			$parameter = array(
				'success' => array( 'short_description' => __( '(Bool) True if the file was successfully downloaded.', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the downloaded file.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) The detais about the downloaded file.', 'wp-webhooks' ) ),
			);

			$settings = array(
				'load_default_settings' => true,
				'data'                  => array(
					'wpwhpro_wp_download_manager_trigger_on_files' => array(
						'id'          => 'wpwhpro_surecart_trigger_on_files',
						'type'        => 'select',
						'multiple'    => true,
						'choices'     => array(),
						'query'       => array(
							'filter' => 'posts',
							'args'   => array(
								'post_type' => 'wpdmpro',
							),
						),
						'label'       => __( 'Files', 'wp-webhooks' ),
						'placeholder' => '',
						'required'    => false,
						'description' => __( 'Select only the files you want to fire the trigger on. You can also choose multiple ones. If none are selected, all are triggered.', 'wp-webhooks' ),
					),
				),
			);

			return array(
				'trigger'           => 'wpdm_file_downloaded',
				'name'              => __( 'File downloaded', 'wp-webhooks' ),
				'sentence'          => __( 'a file was downloaded', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'settings'          => $settings,
				'returns_code'      => $this->get_demo( array() ),
				'short_description' => __( 'This webhook fires as soon as a file was downloaded within WordPress Download Manager.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'wp-download-manager',
				'premium'           => true,
			);

		}

		/**
		 * Triggers when WordPress Download Manager file was downloaded
		 *
		 * @param $package
		 */
		public function wpdm_file_downloaded_callback( $package ) {

			$webhooks            = WPWHPRO()->webhook->get_hooks( 'trigger', 'wpdm_file_downloaded' );
			$response_data_array = array();

			$file                     = array();
			$file['ID']               = $package['ID'];
			$file['file_title']       = $package['title'];
			$file['file_description'] = $package['description'];
			$file['file_excerpt']     = $package['excerpt'];
			$tags                     = wp_get_post_terms( $package['ID'], 'wpdmtag', array( 'fields' => 'names' ) );
			$file['file_tags']        = join( ', ', $tags );
			$categories               = wp_get_post_terms( $package['ID'], 'wpdmcategory', array( 'fields' => 'names' ) );
			$file['file_categories']  = join( ', ', $categories );
			$file['file_author']      = get_the_author_meta( 'display_name', $package['author'] );
			$file['file_url'] = get_permalink( $package['ID'] );

			$payload = array(
				'success' => true,
				'msg'     => __( 'The file has been downloaded.', 'wp-webhooks' ),
				'data'    => $file,
			);

			foreach ( $webhooks as $webhook ) {

				$webhook_url_name = ( is_array( $webhook ) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
				$is_valid         = true;

				if ( isset( $webhook['settings'] ) ) {
					foreach ( $webhook['settings'] as $settings_name => $settings_data ) {
						if ( $settings_name === 'wpwhpro_wp_download_manager_trigger_on_files' && ! empty( $settings_data ) ) {
							if ( ! in_array( $package['ID'], $settings_data ) ) {
								$is_valid = false;
							}
						}
					}
				}

				if ( $is_valid ) {
					if ( $webhook_url_name !== null ) {
						$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
					} else {
						$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
					}
				}
			}

			do_action( 'wpwhpro/webhooks/trigger_wpdm_file_downloaded', $payload, $response_data_array );
		}

		public function get_demo( $options = array() ) {

			$data = array (
				'success' => true,
				'msg' => 'The file has been downloaded.',
				'data' => 
				array (
					'ID'               => 376,
					'file_title'       => 'Demo File',
					'file_description' => '<p>Demo content to fillt</p>',
					'file_excerpt'     => '<p>demo-some</p>',
					'file_tags'        => 'demo, test',
					'file_categories'  => 'demo category',
					'file_author'      => 'John Doe',
					'file_url'         => 'https://yourdomain.test/site/download/somefile/',
				),
			);

			return $data;
		}

	}

endif; // End if class_exists check.
