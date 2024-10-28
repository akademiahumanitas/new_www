<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_litespeed_cache_Actions_lsc_purge_cached_widget' ) ) :

	/**
	 * Load the lsc_purge_cached_widget action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_litespeed_cache_Actions_lsc_purge_cached_widget {

		public function get_details() {


			$parameter = array(
				'widget_ids' => array(
					'label'			=> __( 'Widget IDs', 'wp-webhooks' ),
					'required'		=> true,
					'short_description' => __( '(String) Clear specific widget IDs. To clear multiple ones, please comma-separate them.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'    => array( 'short_description' => __( '(String) An informative message about the action.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(array) Further details about the fired actions.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'The widget ID(s) have been purged.',
				'data'	  => array(
					'widget_ids' => array(
						22,
						25,
					)
				)
			);

			return array(
				'action'            => 'lsc_purge_cached_widget', // required
				'name'              => __( 'Purge cached widget', 'wp-webhooks' ),
				'sentence'          => __( 'purge one or multiple cached widgets', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Purge one or multiple cached widgets within LiteSpeed Cache.', 'wp-webhooks' ),
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
					'widget_ids' => array()
				)
			);

			$widget_ids = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'widget_ids' );

			if( empty( $widget_ids ) ){
				$return_args['msg']     = __( 'Please set the widget_ids argument.', 'wp-webhooks' );
				return $return_args;
			}

			$validated_widget_ids = array();
			
			if( WPWHPRO()->helpers->is_json( $widget_ids ) ){
				$widget_ids_data = json_decode( $widget_ids, true );
			} else {
				$widget_ids_data = explode( ',', $widget_ids );
			}

			if( ! empty( $widget_ids_data ) && is_array( $widget_ids_data ) ){
				foreach( $widget_ids_data as $widget ){
					$validated_widget_ids[] = absint( trim( $widget ) );
				}
			}

			if( empty( $validated_widget_ids ) ){
				$return_args['msg']     = __( 'We could not validate the given widget IDs.', 'wp-webhooks' );
				return $return_args;
			}

			if( ! empty( $validated_widget_ids ) ){
				foreach( $validated_widget_ids as $widget ){
					do_action( 'litespeed_purge_widget', $widget );
				}
			}

			$return_args['success'] = true;
			$return_args['msg']     = __( 'The widget ID(s) have been purged.', 'wp-webhooks' );
			$return_args['data']['widget_ids'] = $validated_widget_ids;

			return $return_args;

		}

	}

endif; // End if class_exists check.
