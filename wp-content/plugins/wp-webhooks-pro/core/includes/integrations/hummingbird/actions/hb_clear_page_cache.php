<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_hummingbird_Actions_hb_clear_page_cache' ) ) :

	/**
	 * Load the hb_clear_page_cache action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_hummingbird_Actions_hb_clear_page_cache {

		public function get_details() {


			$parameter = array(
				'post_ids' => array( 
					'label' => __( 'Post IDs', 'wp-webhooks' ),
					'short_description' => __( '(String) Clear the page for specific post IDs only. To add multiple ones, please comma-separate them.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(array) Further data about the fired actions.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'The page cache has been cleared.',
			);

			return array(
				'action'            => 'hb_clear_page_cache', // required
				'name'              => __( 'Clear page cache', 'wp-webhooks' ),
				'sentence'          => __( 'clear the page cache', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Clear the page cache within Hummingbird', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'hummingbird',
				'premium'           => true,
			);

		}

		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'     => '',
			);

			$post_ids = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_ids' );

			$validated_post_ids = array();
			
			$post_ids_data = explode( ',', $post_ids );
			if( ! empty( $post_ids_data ) && is_array( $post_ids_data ) ){
				foreach( $post_ids_data as $post_id ){
					$validated_post_ids[] = absint( $post_id );
				}
			}

			if( ! empty( $validated_post_ids ) ){
				foreach( $validated_post_ids as $id ){
					do_action( 'wphb_clear_page_cache', $id );
				}
			} else{
				do_action( 'wphb_clear_page_cache' );
			}

			$return_args['success'] = true;
			$return_args['msg']     = __( 'The page cache has been cleared.', 'wp-webhooks' );

			return $return_args;

		}

	}

endif; // End if class_exists check.
