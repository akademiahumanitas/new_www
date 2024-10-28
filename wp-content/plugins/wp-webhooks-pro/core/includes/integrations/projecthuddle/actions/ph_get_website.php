<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_projecthuddle_Actions_ph_get_website' ) ) :

	/**
	 * Load the ph_get_website action
	 *
	 * @since 5.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_projecthuddle_Actions_ph_get_website {

		public function get_details(){

				//These are the main arguments the user can use to input. You should always grab them within your action function.
			$parameter = array(
				'website_id'	   => array( 
					'required' => true, 
					'multiple' => true, 
					'label' => __( 'Website ID', 'wp-webhooks' ), 
					'short_description' => __( '(Integer) The ID of the website you would like to return the details for.', 'wp-webhooks' ), 
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
				'msg' => 'The website was retrieved successfully.',
				'data' => 
				array (
				  'id' => 9340,
				  'date' => '2022-08-13T11:21:42',
				  'date_gmt' => '2022-08-13T11:21:42',
				  'modified' => '2022-08-13T20:13:04',
				  'modified_gmt' => '2022-08-13T20:13:04',
				  'slug' => 'yourdomain',
				  'status' => 'publish',
				  'type' => 'ph-website',
				  'link' => 'https://yourdomain.test/website/yourdomain/',
				  'title' => 
				  array (
					'rendered' => 'yourdomain',
				  ),
				  'author' => 1,
				  'parent' => 0,
				  'meta' => 
				  array (
					'demo_meta' => '',
				  ),
				  'model_type' => 'website',
				  'ph_short_link' => 'https://yourdomain.test/?p=9340',
				  'website_url' => '',
				  'pages' => 
				  array (
				  ),
				  'allow_guests' => false,
				  'force_login' => false,
				  'thread_subscribers' => 'all',
				  'project_approval' => true,
				  'project_unapproval' => true,
				  'webhook' => '',
				  'ph_installed' => true,
				  'child_site' => '',
				  'child_plugin_installed' => '',
				  'access_token' => '',
				  'project_members' => 
				  array (
					0 => 1,
				  ),
				  'resolve_status' => 
				  array (
					'total' => 4,
					'resolved' => 0,
				  ),
				  'items_status' => 
				  array (
					'total' => 4,
					'approved' => 1,
					'by' => 'admin',
					'on' => '2022-08-13 21:22:42',
				  ),
				  'approved' => false,
				  'comment_scroll' => NULL,
				),
			);

			return array(
				'action'			=> 'ph_get_website',
				'name'			  => __( 'Get website', 'wp-webhooks' ),
				'sentence'			  => __( 'get the details of a website', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'This webhook action allows you to get the details of a website within ProjectHuddle.', 'wp-webhooks' ),
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

			$website_id	 = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'website_id' ) );

			if( empty( $website_id ) ){
				$return_args['msg'] = __( "Please set the website_id argument.", 'action-ph_get_website-failure' );
				return $return_args;
			}

			$website = PH()->website->rest->get( $website_id );

			if( ! empty( $website ) ){

				//unset unnecessary data
				if( isset( $website['me'] ) ){
					unset( $website['me'] );
				}

				foreach( $website as $entry_key => $entry ){
					if( strlen( $entry_key ) > 1 && substr( $entry_key, 0, 1 ) === '_' ){
						unset( $website[ $entry_key ] );
					}
				}

				$return_args['data'] = $website;	
				$return_args['msg'] = __( "The website was retrieved successfully.", 'action-ph_get_website-success' );
				$return_args['success'] = true;
			} else {
				$return_args['msg'] = __( "We could not fetch the website from the given ID.", 'action-ph_get_website-success' );
				$return_args['data'] = $website;
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.