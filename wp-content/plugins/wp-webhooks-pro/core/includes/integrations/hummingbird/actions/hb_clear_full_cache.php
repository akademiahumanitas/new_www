<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_hummingbird_Actions_hb_clear_full_cache' ) ) :

	/**
	 * Load the hb_clear_full_cache action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_hummingbird_Actions_hb_clear_full_cache {

		public function get_details() {


			$parameter = array(
				'remove_data' => array( 
					'label' => __( 'Remove data', 'wp-webhooks' ),
					'short_description' => __( '(String) Set this to yes to remove data and files.', 'wp-webhooks' ),
				),
				'remove_settings' => array( 
					'label' => __( 'Remove settings', 'wp-webhooks' ),
					'short_description' => __( '(String) Set this to yes to remove additional settings such as gzip and caching from the .htaccess file.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(array) Further data about the fired actions.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'The cache has been cleared.',
			);

			return array(
				'action'            => 'hb_clear_full_cache', // required
				'name'              => __( 'Clear full cache', 'wp-webhooks' ),
				'sentence'          => __( 'clear the full cache', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Clear the full cache within Hummingbird', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'hummingbird',
				'premium'           => true,
			);

		}

		public function execute( $return_data, $response_body ) {

			$hb_helpers = WPWHPRO()->integrations->get_helper( 'hummingbird', 'hb_helpers' );
			$return_args = array(
				'success' => false,
				'msg'     => '',
			);

			$remove_data = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tags' ) === 'yes' ) ? true : false;
			$remove_settings = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tags' ) === 'yes' ) ? true : false;

			$hb_helpers->clear_cache( $remove_data, $remove_settings );

			$return_args['success'] = true;
			$return_args['msg']     = __( 'The cache has been cleared.', 'wp-webhooks' );

			return $return_args;

		}

	}

endif; // End if class_exists check.
