<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Webhooks_Integrations_jetpack_crm_Triggers_jpcrm_company_created' ) ) :

	/**
	 * Load the jpcrm_company_created trigger
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_jetpack_crm_Triggers_jpcrm_company_created {

		public function get_callbacks() {

			return array(
				array(
					'type'      => 'action',
					'hook'      => 'zbs_new_company',
					'callback'  => array( $this, 'jpcrm_company_created_callback' ),
					'priority'  => 10,
					'arguments' => 1,
					'delayed'   => true,
				),
			);
		}

		public function get_details() {


			$parameter = array(
				'success' => array( 'short_description' => __( '(Bool) True if the company was successfully created.', 'wp-webhooks' ) ),
                'msg' => array( 'short_description' => __( '(String) Further details about the creation of a company.', 'wp-webhooks' ) ),
				'company_id'   => array(
					'label'             => __( 'Company ID', 'wp-webhooks' ),
					'short_description' => __( '(Integer) The company id.', 'wp-webhooks' ),
				),
				'company'   => array(
					'label'             => __( 'Company data', 'wp-webhooks' ),
					'short_description' => __( '(Array) Further details about the created company.', 'wp-webhooks' ),
				),
			);

			$settings = array(
				'load_default_settings' => true,
			);

			return array(
				'trigger'           => 'jpcrm_company_created',
				'name'              => __( 'Company created', 'wp-webhooks' ),
				'sentence'          => __( 'a company was created', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'settings'          => $settings,
				'returns_code'      => $this->get_demo( array() ),
				'short_description' => __( 'This webhook fires as soon as a company was created within Jetpack CRM.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'jetpack-crm',
				'premium'           => true,
			);

		}

		/**
		 * Triggers when Jetpack CRM creates a company
		 *
		 * @param $company_id Company's id
		 */
		public function jpcrm_company_created_callback( $company_id ) {

			global $zbs;

			$company = $zbs->DAL->companies->getCompany( $company_id );

			$webhooks            = WPWHPRO()->webhook->get_hooks( 'trigger', 'jpcrm_company_created' );
			$response_data_array = array();
			
			$payload = array(
				'success' => true,
				'msg'     => __( 'The company has been created', 'wp-webhooks' ),
				'company_id' => $company_id,
				'company' => $company,
			);

			foreach ( $webhooks as $webhook ) {

				$webhook_url_name = ( is_array( $webhook ) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
				$is_valid         = true;

				if ( $is_valid ) {
					if ( $webhook_url_name !== null ) {
						$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
					} else {
						$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
					}
				}
			}

			do_action( 'wpwhpro/webhooks/trigger_jpcrm_company_created', $payload, $response_data_array );
		}

		public function get_demo( $options = array() ) {

			$data = array (
				'success' => true,
				'msg' => 'The company has been created',
				'company_id' => 2,
				'company' => 
				array (
				  'id' => '2',
				  'owner' => '1',
				  'status' => 'Customer',
				  'name' => 'Demo Corporation',
				  'email' => 'demo@company.test',
				  'addr1' => 'Demo Address',
				  'addr2' => 'Apartment 123',
				  'city' => 'Demo city',
				  'county' => 'Demo State',
				  'country' => 'Germany',
				  'postcode' => '12345',
				  'secaddr1' => 'Demo Addres',
				  'secaddr2' => 'Apartment 1234',
				  'seccity' => 'Demo City',
				  'seccounty' => 'Demo State',
				  'seccountry' => 'Germany',
				  'secpostcode' => '12345',
				  'maintel' => '123456789',
				  'sectel' => '123456789',
				  'wpid' => 0,
				  'avatar' => '',
				  'tw' => '',
				  'li' => '',
				  'fb' => '',
				  'created' => 1665903508,
				  'created_date' => '10/16/2022',
				  'lastupdated' => 1665903508,
				  'lastupdated_date' => '10/16/2022',
				  'lastcontacted' => 0,
				  'lastcontacted_date' => false,
				  'contacts' => 
				  array (
				  ),
				),
			);

			return $data;
		}

	}

endif; // End if class_exists check.
