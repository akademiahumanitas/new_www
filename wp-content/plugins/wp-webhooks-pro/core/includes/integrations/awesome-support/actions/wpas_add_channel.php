<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_awesome_support_Actions_wpas_add_channel' ) ) :
	/**
	 * Load the wpas_add_channel action
	 *
	 * @since 6.0.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_awesome_support_Actions_wpas_add_channel {

		public function get_details() {
			$parameter = array(
				'name'        => array(
					'required'          => true,
					'label'             => __( 'Name', 'wp-webhooks' ),
					'short_description' => __(
						'(String) The name of the channel. You can provide the existing name or an ID of the channel. If no channel exists, a new one will be added.',
						'wp-webhooks'
					),
				),
				'slug'        => array(
					'label'             => __( 'Slug', 'wp-webhooks' ),
					'short_description' => __( '(String) The slug of the channel. The “slug” is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.', 'wp-webhooks' ),
				),
				'parent'      => array(
					'label'             => __( 'Parent channel', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(),
					'query'             => array(
						'filter' => 'terms',
						'args'   => array(
							'taxonomy'   => 'ticket_channel',
							'orderby'    => 'name',
							'order'      => 'ASC',
							'hide_empty' => false,
						),
					),
					'short_description' => __( '(String) The ID of another channel to set it as a parent. Otherwise leave empty.', 'wp-webhooks' ),
				),
				'description' => array(
					'label'             => __( 'Description', 'wp-webhooks' ),
					'short_description' => __( '(String) The description of the channel.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action status.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further information about the request.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'Channel has been added successfully.',
				'data' => 
				array (
				  'term_id' => 147,
				  'term_taxonomy_id' => 147,
				),
			);

			return array(
				'action'            => 'wpas_add_channel', // required
				'name'              => __( 'Add channel', 'wp-webhooks' ),
				'sentence'          => __( 'Add a channel', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Add a channel within Awesome Support.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'awesome-support',
				'premium'           => true,
			);

		}

		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'     => '',
				'data'    => array(),
			);

			$parent = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'parent' );

			if ( WPWHPRO()->helpers->is_json( $parent ) ) {
				$parent = json_decode( $parent, true );
			}

			$channel['name']        = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'name' );
			$channel['description'] = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'description' );
			$channel['parent']      = ( $parent ) ? intval( $parent ) : 0;
			$channel['slug']        = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'slug' );
			$channel['taxonomy']    = 'ticket_channel';

			if( ! empty( $channel['parent'] ) ){
				$parent_term = get_term( $channel['parent'] );

				if( $parent_term->taxonomy !== $channel['taxonomy'] ){
					$return_args['msg'] = __( 'The parent channel is from a different taxonomy.', 'wp_webhooks' );
					return $return_args;
				}
			}

			$channel_result = wp_insert_term( $channel['name'], $channel['taxonomy'], $channel );

			if ( is_array( $channel_result ) ) {
				$return_args['success'] = true;
				$return_args['msg']     = __( 'Channel has been added successfully.', 'wp_webhooks' );
			} elseif( is_wp_error( $channel_result ) ){
				$return_args['msg'] = $channel_result->get_error_message();
			} else {
				$return_args['msg'] = __( 'An error occured while adding the channel.', 'wp_webhooks' );
			}

			$return_args['data'] = $channel_result;

			return $return_args;

		}


	}
endif;
