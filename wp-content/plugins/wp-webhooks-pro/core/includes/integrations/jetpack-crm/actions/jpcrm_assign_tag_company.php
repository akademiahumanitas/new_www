<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_jetpack_crm_Actions_jpcrm_assign_tag_company' ) ) :
	/**
	 * Load the jpcrm_assign_tag_company action
	 *
	 * @since 6.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_jetpack_crm_Actions_jpcrm_assign_tag_company {
		public function get_details() {

			$parameter = array(
				'company' => array(
					'label'             => __( 'Company', 'wp-webhooks' ),
					'required'          => true,
					'short_description' => __( '(String) The company id or email.', 'wp-webhooks' ),
				),
				'tags'    => array(
					'label'             => __( 'Tags', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(),
					'multiple'          => true,
					'query'             => array(
						'filter' => 'helpers',
						'args'   => array(
							'integration' => 'jetpack-crm',
							'helper'      => 'jpcrm_helpers',
							'function'    => 'get_query_tags',
							'type'        => 'ZBS_TYPE_COMPANY',
						),
					),
					'short_description' => __( '(String) The tags to assign to the company.', 'wp-webhooks' ),
				),
				'tag_mode'    => array(
					'label'             => __( 'Tag mode', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(
						'append' => array(
							'label' => __( 'Append', 'wp-webhooks' ),
						),
						'replace' => array(
							'label' => __( 'Replace', 'wp-webhooks' ),
						),
					),
					'multiple'          => false,
					'default_value'     => 'append',
					'short_description' => __( '(String) Choose whether you want to replace the other tags or if you want to append the new ones.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further details about the action.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'The tags have been assigned to the company.',
				'data'    =>
				array(
					'company_id' => '23',
				),
			);

			return array(
				'action'            => 'jpcrm_assign_tag_company', // required
				'name'              => __( 'Assign tag to company', 'wp-webhooks' ),
				'sentence'          => __( 'assign one or multiple tags to a company', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Assign one or multiple tags to a company within Jetpack CRM.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'jetpack-crm',
				'premium'           => true,
			);

		}

		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'     => '',
				'data'    => array(),
			);

			$company            = array();
			$company['company'] = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'company' );
			$company['tags']    = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tags' );
			$company['tag_mode'] = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tag_mode' );

			if ( isset( $company['tags'] ) ) {
				if ( WPWHPRO()->helpers->is_json( $company['tags'] ) ) {
					$company['tags'] = json_decode( $company['tags'], true );
				} else {
					$company['tags'] = preg_split( '/(\s*,*\s*)*,+(\s*,*\s*)*/', $company['tags'] );
				}
			}

			if( ! is_array( $company['tags'] ) && is_numeric( $company['tags'] ) ){
				$company['tags'] = array( $company['tags'] );
			}

			$company_id = 0;

			global $zbs;
			if ( is_email( $company['company'] ) ) {
				$company_id = zeroBS_getCompanyIDWithEmail( $company['company'] );
			} else {
				$company_id = $zbs->DAL->companies->getCompany(
					intval( $company['company'] ),
					array(
						'ignoreOwner' => 1,
						'onlyID'      => 1,
					)
				);
			}

			if ( $company_id <= 0 ) {
				$return_args['msg'] = __( 'The given company doesn\'t exist.', 'wp-webhooks' );
				return $return_args;
			}

			if( empty( $company['tag_mode'] ) ){
				$company['tag_mode'] = 'append';
			}

			$result = $zbs->DAL->companies->addUpdateCompanyTags(
				array(
					'id'        => $company_id,
					'tag_input' => $company['tags'],
					'mode'      => $company['tag_mode'],
				)
			);

			if ( $result == true ) {
				$return_args['success']            = true;
				$return_args['data']['company_id'] = $company_id;
				$return_args['msg']                = __( 'The tags have been assigned to the company.', 'wp-webhooks' );
			} else {
				$return_args['msg'] = __( 'An error occurred while adding the tags.', 'wp-webhooks' );
			}

			return $return_args;
		}

	}
endif;
