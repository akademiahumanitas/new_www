<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_litespeed_cache_Actions_lsc_purge_cached_post_type' ) ) :

	/**
	 * Load the lsc_purge_cached_post_type action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_litespeed_cache_Actions_lsc_purge_cached_post_type {

		public function get_details() {


			$parameter = array(
				'post_types' => array(
					'type'			=> 'select',
					'multiple'		=> true,
 					'choices'		=> array(),
 					'query'			=> array(
						 'filter'	=> 'post_types',
						 'args'		=> array()
					),
					'label'			=> __( 'Post Types', 'wp-webhooks' ),
					'placeholder'	=> '',
					'required'		=> true,
					'short_description' => __( '(String) Clear specific post types only.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'    => array( 'short_description' => __( '(String) An informative message about the action.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(array) Further details about the fired actions.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'The post type(s) have been purged.',
				'data'	  => array(
					'post_types' => array(
						'post',
						'page',
					)
				)
			);

			return array(
				'action'            => 'lsc_purge_cached_post_type', // required
				'name'              => __( 'Purge cached post type', 'wp-webhooks' ),
				'sentence'          => __( 'purge one or multiple cached post types', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Purge one or multiple cached post types within LiteSpeed Cache.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'litespeed-cache',
				'premium'           => true,
			);

		}

		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'     => '',
				'data'	  => array(
					'post_types' => array()
				)
			);

			$post_types = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_types' );

			if( empty( $post_types ) ){
				$return_args['msg']     = __( 'Please set the post_types argument.', 'wp-webhooks' );
				return $return_args;
			}

			$validated_post_types = array();
			
			if( WPWHPRO()->helpers->is_json( $post_types ) ){
				$post_types_data = json_decode( $post_types, true );
			} else {
				$post_types_data = explode( ',', $post_types );
			}

			if( ! empty( $post_types_data ) && is_array( $post_types_data ) ){
				foreach( $post_types_data as $post_type ){
					$validated_post_types[] = trim( $post_type );
				}
			}

			if( empty( $validated_post_types ) ){
				$return_args['msg']     = __( 'We could not validate the given post types.', 'wp-webhooks' );
				return $return_args;
			}

			if( ! empty( $validated_post_types ) ){
				foreach( $validated_post_types as $post_type ){
					do_action( 'litespeed_purge_posttype', $post_type );
				}
			}

			$return_args['success'] = true;
			$return_args['msg']     = __( 'The post type(s) have been purged.', 'wp-webhooks' );
			$return_args['data']['post_types'] = $validated_post_types;

			return $return_args;

		}

	}

endif; // End if class_exists check.
