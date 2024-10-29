<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_autonami_Actions_ami_get_lists' ) ) :
	/**
	 * Load the ami_get_lists action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_autonami_Actions_ami_get_lists {

		public function get_details() {
			$parameter         = array(
				'contact_id' => array(
					'label'             => __( 'Contact ID', 'wp-webhooks' ),
					'short_description' => __( '(Integer) Search by Contact ID. If no Contact ID is given, we will search within all lists.', 'wp-webhooks' )
				),
				'search'     => array(
					'label'             => __( 'Search', 'wp-webhooks' ),
					'short_description' => __( '(String) Search by list name. If no search value is given, we will search within take all lists.', 'wp-webhooks' )
				),
				'list_ids'   => array(
					'label'             => __( 'List IDs', 'wp-webhooks' ),
					'short_description' => __( '(String) The list IDs you want to look for. To search for multiple ones, please comma-separated them.', 'wp-webhooks' )
				),
				'limit'      => array(
					'label'             => __( 'Limit', 'wp-webhooks' ),
					'short_description' => __( '(Integer) Limit the number of results that should be returned. Default: 0 (All)', 'wp-webhooks' )
				),
				'offset'     => array(
					'label'             => __( 'Offset', 'wp-webhooks' ),
					'short_description' => __( '(Integer) The offset for the results. Default: 0', 'wp-webhooks' )
				)
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'lists'   => array( 'short_description' => __( '(array) Further details about the found lists.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'Lists successfully retrieved.',
				'data'    => [
					'lists' => [
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
					'search' => '',
					'list_ids' => [],
					'limit' => 0,
					'offset' => 0,
				]
			);

			return array(
				'action'            => 'ami_get_lists',
				'name'              => __( 'Get lists', 'wp-webhooks' ),
				'sentence'          => __( 'get one or multiple lists', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'This webhook action allows you to get one or multiple lists wihtin FunnelKit Automations.', 'wp-webhooks' ),
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
					'lists' => array(),
					'contact_id' => 0,
					'search' => '',
					'list_ids' => array(),
					'limit' => 0,
					'offset' => 0,
				)
			);

			$contact_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'contact_id' ) );
			$search     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'search' );
			$list_ids   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'list_ids' );
			$limit      = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'limit' );
			$offset     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'offset' );

			$list_ids_to_return = $list_ids;
			$list_ids           = empty( $list_ids ) ? array() : explode( ',', $list_ids );
			$limit              = ! empty( $limit ) ? $limit : 0;
			$offset             = ! empty( $offset ) ? $offset : 0;

			if ( ! empty( $contact_id ) ) {
				$contact = new BWFCRM_Contact( $contact_id );
				if ( ! $contact->is_contact_exists() ) {
					$return_args['msg'] = sprintf( __( 'No contact found related with the contact ID: %s', 'wp-webhooks' ), $contact_id );

					return $return_args['msg'];
				}

				$list_data = $contact->get_all_lists();

			} else {
				$list_data = BWFCRM_Lists::get_lists( $list_ids, $search, $offset, $limit, ARRAY_A );
				if ( ! is_array( $list_data ) ) {
					$list_data = array();
				}
			}

			if( empty( $list_data ) ){
				$return_args['msg'] = __( 'No lists have been found.', 'wp-webhooks' );
			} else {
				$return_args['msg'] = __( 'Lists successfully retrieved.', 'wp-webhooks' );
			}

			$return_args['data']['lists'] 		= $list_data;
			$return_args['data']['contact_id'] 	= $contact_id;
			$return_args['data']['search'] 		= $search;
			$return_args['data']['list_ids'] 	= $list_ids_to_return;
			$return_args['data']['limit'] 		= $limit;
			$return_args['data']['offset'] 		= $offset;
			$return_args['success']       		= true;

			return $return_args;

		}
	}
endif;

