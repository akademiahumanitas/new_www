<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_freshdesk_Actions_freshdesk_create_contact' ) ) :

	/**
	 * Load the freshdesk_create_contact action
	 *
	 * @since 6.1.3
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_freshdesk_Actions_freshdesk_create_contact {

		public function get_details(){

				//These are the main arguments the user can use to input. You should always grab them within your action function.
			$parameter = array(
				'auth_template'		=> array( 
					'required' => true, 
					'type' => 'select', 
					'multiple' => false, 
					'label' => __( 'Authentication template', 'wp-webhooks' ), 
					'query'			=> array(
						'filter'	=> 'authentications',
						'args'		=> array(
							'auth_methods' => array( 'freshdesk_auth' )
						)
					),
					'short_description' => __( 'Use globally defined Freshdesk credentials to authenticate this action.', 'wp-webhooks' ),
					'description' => __( 'This argument accepts the ID of a Freshdesk authentication template of your choice. You can create new templates within the "Authentication" tab.', 'wp-webhooks' ),
				),
				'name'	   => array( 
					'required' => true,
					'label' => __( 'Contact Name', 'wp-webhooks' ), 
					'short_description' => __( '(string) The name of the contact.', 'wp-webhooks' ), 
				),
				'email'	   => array( 
					'required' => true,
					'label' => __( 'Contact Email', 'wp-webhooks' ), 
					'short_description' => __( '(string) The email of the contact.', 'wp-webhooks' ), 
				),
				'custom_fields'	   => array( 
					'type' => 'repeater', 
					'variable' => false, 
					'multiple' => true, 
					'label' => __( 'Custom Fields', 'wp-webhooks' ), 
					'short_description' => __( '(string) A JSON formatted string containing custom contact fields.', 'wp-webhooks' ),
				),
				'full_response' => array( 
					'label' => __( 'Return full response', 'wp-webhooks' ), 
					'type' => 'select',
					'multiple' => false,
					'choices' => array(
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
						'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
					),
					'default_value' => 'no',
					'short_description' => __( 'Return the full HTTP response instead of our simplified version. This gives you access to cookies, headers, and much more. Default: "no"', 'wp-webhooks' ),
				),
			);

			//This is a more detailed view of how the data you sent will be returned.
			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further details about the sent data.', 'wp-webhooks' ) ),
			);

			ob_start();
			?>
			<?php echo __( "The custom_fields argument accepts a JSON formatted string, containing custom fields for the ticket. Down below you will find an example using two simple custom fields:", 'wp-webhooks' ); ?>
			<pre>{
  "gadget": "Cold Welder",
  "contact-type": "B2B"
}</pre>
			<?php
			$parameter['custom_fields']['description'] = ob_get_clean();

			$returns_code = array (
				'success' => true,
				'msg' => 'The contact has been successfully created.',
				'data' => 
				array (
				  'active' => false,
				  'address' => NULL,
				  'deleted' => false,
				  'description' => NULL,
				  'email' => 'jondoe@demodomain.com',
				  'id' => 47000000000,
				  'job_title' => NULL,
				  'language' => 'en',
				  'mobile' => NULL,
				  'name' => 'Jon Doe',
				  'phone' => NULL,
				  'time_zone' => 'Amsterdam',
				  'twitter_id' => NULL,
				  'custom_fields' => 
				  array (
				  ),
				  'tags' => 
				  array (
				  ),
				  'other_emails' => 
				  array (
				  ),
				  'facebook_id' => NULL,
				  'created_at' => '2023-03-08T16:01:01Z',
				  'updated_at' => '2023-03-08T16:01:01Z',
				  'csat_rating' => NULL,
				  'preferred_source' => NULL,
				  'company_id' => NULL,
				  'view_all_tickets' => NULL,
				  'avatar' => NULL,
				  'twitter_profile_status' => false,
				  'twitter_followers_count' => NULL,
				),
			);

			return array(
				'action'			=> 'freshdesk_create_contact',
				'name'			  => __( 'Create contact', 'wp-webhooks' ),
				'sentence'			  => __( 'create a contact', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Create a contact within Freshdesk.', 'wp-webhooks' ),
				'description'	   => array(),
				'integration'	   => 'freshdesk',
				'premium' 			=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array()
			);

			$auth_template	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'auth_template' );
			$name	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'name' );
			$email	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'email' );
			$custom_fields	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'custom_fields' );
			$full_response	 = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'full_response' ) === 'yes' ) ? true :false;
			$api_domain = '';
			$api_key = '';

			if( empty( $name ) ){
				$return_args['msg'] = __( "Please set the name argument.", 'wp-webhooks' );
				return $return_args;
			}

			if( empty( $email ) || ! is_email( $email ) ){
				$return_args['msg'] = __( "Please set the email argument.", 'wp-webhooks' );
				return $return_args;
			}

			$email = sanitize_email( $email );

			if( ! empty( $auth_template ) ){
				$emailable_auth = WPWHPRO()->integrations->get_auth( 'emailable', 'emailable_auth' );
				$credentials = $emailable_auth->get_credentials( $auth_template );

				if( 
					isset( $credentials['wpwhpro_freshdesk_domain'] )
					&& ! empty( $credentials['wpwhpro_freshdesk_domain'] )
				){
					$api_domain = $credentials['wpwhpro_freshdesk_domain'];
				}

				if( 
					isset( $credentials['wpwhpro_freshdesk_api_key'] )
					&& ! empty( $credentials['wpwhpro_freshdesk_api_key'] )
				){
					$api_key = $credentials['wpwhpro_freshdesk_api_key'];
				}
			}

			if( empty( $api_key ) ){
                $return_args['msg'] = __( "The provided API key is invalid.", 'wp-webhooks' );
				return $return_args;
            }

			if( empty( $api_domain ) ){
                $return_args['msg'] = __( "Please provide a valid subdomain within your authentication template.", 'wp-webhooks' );
				return $return_args;
            }

			$body = array(
				'name' => $name,
				'email' => $email,
			);

			if( ! empty( $custom_fields ) ){
				$validated_custom_fields = array();

				if( WPWHPRO()->helpers->is_json( $custom_fields ) ){
					$custom_fields = json_decode( $custom_fields, true );
					if( is_array( $custom_fields ) ){
						$validated_custom_fields = $custom_fields;
					}
				}

				$body['custom_fields'] = $validated_custom_fields;
			}

			$http_args = array(
				'method' => 'POST',
				'blocking' => true,
				'httpversion' => '1.1',
				'timeout' => 20,
				'headers' => array(
					'content-type' => 'application/json',
					'Authorization' => 'Basic ' . base64_encode( $api_key . ':X' ),
				),
				'body' => $body
			);
	
			$api_url = 'https://' . $api_domain . '.freshdesk.com/api/v2/contacts';

			$response = WPWHPRO()->http->send_http_request( $api_url, $http_args );

			if( $full_response ){
				//Map the response
				$return_args = array_merge( $return_args, $response );
			} else {
				if( $response['success'] ){

					$return_args['success'] = true;
					$return_args['msg'] = __( "The contact has been successfully created.", 'wp-webhooks' );
					$return_args['data'] = $response['content'];

				} else {
					$return_args['msg'] = __( "An error occured while creating the contact.", 'wp-webhooks' );
					$return_args['data'] = $response['content'];
				}
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.