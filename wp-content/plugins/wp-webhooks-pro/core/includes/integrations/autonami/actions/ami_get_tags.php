<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_autonami_Actions_ami_get_tags' ) ) :
	/**
	 * Load the ami_get_tags action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_autonami_Actions_ami_get_tags {

		public function get_details() {
			$parameter         = array(
				'contact_id' => array(
					'label'             => __( 'Search', 'wp-webhooks' ),
					'short_description' => __( '(Integer) Search by Contact ID. If no Contact ID is given, we will search within all tags.', 'wp-webhooks' )
				),
				'search'     => array(
					'label'             => __( 'Search', 'wp-webhooks' ),
					'short_description' => __( '(String) Search by list name. If no search value is given, will search within all tags.', 'wp-webhooks' )
				),
				'tag_ids'    => array(
					'label'             => __( 'List IDs', 'wp-webhooks' ),
					'short_description' => __( '(String) The list IDs you want to look for. To search for multiple list IDs, please comma-separated them.', 'wp-webhooks' )
				),
				'limit'      => array(
					'label'             => __( 'Limit', 'wp-webhooks' ),
					'short_description' => __( '(Integer) Limit the number of search results. Default 0', 'wp-webhooks' )
				),
				'offset'     => array(
					'label'             => __( 'Offset', 'wp-webhooks' ),
					'short_description' => __( '(Integer) The offset for the returned results. Default 0', 'wp-webhooks' )
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'tags'    => array( 'short_description' => __( '(array) Further details about the found tags.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => '"search: tag_ids:Array limit:0 offset:0",',
				'data'    => [
					'tags'       => [
						[
							'ID'         => 66,
							'name'       => 'list1',
							'type'       => '2',
							'created_at' => '2022-08-17 14:29:52',
							'updated_at' => null,
							'data'       => null
						],
						[
							'ID'         => 33,
							'name'       => 'list2',
							'type'       => '2',
							'created_at' => '2022-08-17 08:44:23',
							'updated_at' => null,
							'data'       => null
						]
					],
					'contact_id' => '123',
					'search'     => '',
					'tag_ids'    => [],
					'limit'      => 0,
					'offset'     => 0,
				]
			);

			return array(
				'action'            => 'ami_get_tags',
				'name'              => __( 'Get tags', 'wp-webhooks' ),
				'sentence'          => __( 'get one or multiple tags', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'This webhook action allows you to get one or multiple tags in FunnelKit Automations.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'autonami',
				'premium'           => true,
			);
		}

		public function execute( $return_data, $response_body ) {
			$return_args = array(
				'success' => false,
				'msg'     => '',
				'data'    => array(
					'tags'       => array(),
					'contact_id' => 0,
					'search'     => '',
					'tag_ids'    => array(),
					'limit'      => 0,
					'offset'     => 0,
				)
			);

			$contact_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'contact_id' ) );
			$search     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'search' );
			$tag_ids    = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tag_ids' );
			$limit      = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'limit' );
			$offset     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'offset' );

			$tag_ids  = empty( $tag_ids ) ? array() : explode( ',', $tag_ids );
			$limit    = ! empty( $limit ) ? $limit : 0;
			$offset   = ! empty( $offset ) ? $offset : 0;

			if ( ! empty( $contact_id ) ) {

				$contact = new BWFCRM_Contact( $contact_id );

				if ( ! $contact->is_contact_exists() ) {
					$return_args['msg'] = sprintf( __( 'No contact found with the given ID #%d', 'wp-webhooks' ), $contact_id );

					return $return_args['msg'];
				}

				$tag_data = $contact->get_all_tags();
			} else {
				
				$tag_data = BWFCRM_Tag::get_tags( $tag_ids, $search, $offset, $limit, ARRAY_A );
				if ( ! is_array( $tag_data ) ) {
					$tag_data = array();
				}

			}

			if( empty( $tag_data ) ){
				$return_args['msg'] = __( 'No tags found.', 'wp-webhooks' );
			} else {
				$return_args['msg'] = __( 'Tags successfully retrieved.', 'wp-webhooks' );
			}
			
			$return_args['data']['tags']       = $tag_data;
			$return_args['data']['contact_id'] = $contact_id;
			$return_args['data']['search']     = $search;
			$return_args['data']['tag_ids']    = $tag_ids;
			$return_args['data']['limit']      = $limit;
			$return_args['data']['offset']     = $offset;
			$return_args['success']            = true;

			return $return_args;

		}
	}
endif;

