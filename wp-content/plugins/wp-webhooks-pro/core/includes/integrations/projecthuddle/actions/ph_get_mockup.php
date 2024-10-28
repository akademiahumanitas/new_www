<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_projecthuddle_Actions_ph_get_mockup' ) ) :

	/**
	 * Load the ph_get_mockup action
	 *
	 * @since 5.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_projecthuddle_Actions_ph_get_mockup {

		public function get_details(){

				//These are the main arguments the user can use to input. You should always grab them within your action function.
			$parameter = array(
				'mockup_id'	   => array( 
					'required' => true, 
					'multiple' => true, 
					'label' => __( 'Mockup ID', 'wp-webhooks' ), 
					'short_description' => __( '(Integer) The ID of the mockup you would like to return the details for.', 'wp-webhooks' ), 
				),
			);

			//This is a more detailed view of how the data you sent will be returned.
			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further details about the sent data.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The mockup was retrieved successfully.',
				'data' => 
				array (
				  'id' => 9346,
				  'date' => '2022-08-13T11:56:57',
				  'date_gmt' => '2022-08-13T11:56:57',
				  'modified' => '2022-08-13T11:57:18',
				  'modified_gmt' => '2022-08-13T11:57:18',
				  'slug' => '9346',
				  'status' => 'publish',
				  'type' => 'ph-project',
				  'link' => 'https://yourdomain.test/mockup/9346/',
				  'title' => 
				  array (
					'rendered' => '',
				  ),
				  'content' => 
				  array (
					'rendered' => '',
					'protected' => false,
				  ),
				  'author' => 1,
				  'parent' => 0,
				  'model_type' => 'mockup',
				  'ph_short_link' => 'https://yourdomain.test/?p=9346',
				  'project_access' => 'login',
				  'thread_subscribers' => 'all',
				  'retina' => false,
				  'sharing' => true,
				  'zoom' => true,
				  'tooltip' => true,
				  'allow_guests' => true,
				  'force_login' => true,
				  'project_download' => false,
				  'project_comments' => false,
				  'project_approval' => true,
				  'project_unapproval' => true,
				  'access_token' => '',
				  'project_members' => 
				  array (
					0 => 1,
				  ),
				  'resolve_status' => 
				  array (
					'total' => 3,
					'resolved' => 0,
					'by' => false,
					'on' => false,
				  ),
				  'items_status' => 
				  array (
					'total' => 1,
					'approved' => 0,
					'by' => false,
					'on' => false,
				  ),
				  'approved' => false,
				),
			);

			return array(
				'action'			=> 'ph_get_mockup',
				'name'			  => __( 'Get mockup', 'wp-webhooks' ),
				'sentence'			  => __( 'get the details of a mockup', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'This webhook action allows you to get the details of a mockup within ProjectHuddle.', 'wp-webhooks' ),
				'description'	   => array(),
				'integration'	   => 'projecthuddle',
				'premium' 			=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array()
			);

			$mockup_id	 = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'mockup_id' ) );

			if( empty( $mockup_id ) ){
				$return_args['msg'] = __( "Please set the mockup_id argument.", 'action-ph_get_mockup-failure' );
				return $return_args;
			}

			$mockup = PH()->mockup->rest->get( $mockup_id );

			if( ! empty( $mockup ) ){

				//unset unnecessary data
				if( isset( $mockup['me'] ) ){
					unset( $mockup['me'] );
				}

				foreach( $mockup as $entry_key => $entry ){
					if( strlen( $entry_key ) > 1 && substr( $entry_key, 0, 1 ) === '_' ){
						unset( $mockup[ $entry_key ] );
					}
				}

				$return_args['data'] = $mockup;	
				$return_args['msg'] = __( "The mockup was retrieved successfully.", 'action-ph_get_mockup-success' );
				$return_args['success'] = true;
			} else {
				$return_args['msg'] = __( "We could not fetch the mockup from the given ID.", 'action-ph_get_mockup-success' );
				$return_args['data'] = $mockup;
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.