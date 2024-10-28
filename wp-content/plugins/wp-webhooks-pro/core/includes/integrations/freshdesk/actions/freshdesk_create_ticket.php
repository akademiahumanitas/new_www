<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_freshdesk_Actions_freshdesk_create_ticket' ) ) :

	/**
	 * Load the freshdesk_create_ticket action
	 *
	 * @since 6.1.3
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_freshdesk_Actions_freshdesk_create_ticket {

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
				'subject'	   => array( 
					'required' => true,
					'label' => __( 'Ticket subject', 'wp-webhooks' ), 
					'short_description' => __( '(string) This will be the title of the ticket.', 'wp-webhooks' ), 
				),
				'description'	   => array( 
					'required' => true,
					'label' => __( 'Ticket description', 'wp-webhooks' ), 
					'short_description' => __( '(string) This will be the description of the ticket.', 'wp-webhooks' ), 
				),
				'email'	   => array( 
					'required' => true,
					'label' => __( 'Contact Email', 'wp-webhooks' ), 
					'short_description' => __( '(string) The email of the contact you want to assign to the ticket.', 'wp-webhooks' ), 
				),
				'priority'	=> array( 
					'label' => __( 'Proprity', 'wp-webhooks' ), 
					'default_value' => 'prio_1', 
					'type' => 'select', 
					'choices' => array(
						'prio_1' => array( 'label' => __( 'Low', 'wp-webhooks' ) ),
						'prio_2' => array( 'label' => __( 'Medium', 'wp-webhooks' ) ),
						'prio_3' => array( 'label' => __( 'High', 'wp-webhooks' ) ),
						'prio_4' => array( 'label' => __( 'Urgent', 'wp-webhooks' ) ),
					), 
					'short_description' => __( '(string) The ticket priority.', 'wp-webhooks' ),
				),
				'status'	=> array( 
					'label' => __( 'Status', 'wp-webhooks' ), 
					'default_value' => 'status_2', 
					'type' => 'select', 
					'choices' => array(
						'status_2' => array( 'label' => __( 'Open', 'wp-webhooks' ) ),
						'status_3' => array( 'label' => __( 'Pending', 'wp-webhooks' ) ),
						'status_4' => array( 'label' => __( 'Resolved', 'wp-webhooks' ) ),
						'status_5' => array( 'label' => __( 'Closed', 'wp-webhooks' ) ),
						'status_6' => array( 'label' => __( 'Waiting on Customer', 'wp-webhooks' ) ),
						'status_7' => array( 'label' => __( 'Waiting on Third Party', 'wp-webhooks' ) ),
					), 
					'short_description' => __( '(string) The status for the ticket.', 'wp-webhooks' ),
				),
				'cc_emails'	   => array(
					'label' => __( 'CC Emails', 'wp-webhooks' ), 
					'short_description' => __( '(string) Additional email addresses you want to set for CC. Separate multiple ones via a comma or as a JSON construct.', 'wp-webhooks' ),
				),
				'custom_fields'	   => array( 
					'type' => 'repeater', 
					'variable' => false, 
					'multiple' => true, 
					'label' => __( 'Custom Fields', 'wp-webhooks' ), 
					'short_description' => __( '(string) A JSON formatted string containing custom ticket fields.', 'wp-webhooks' ),
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
  "customer-type": "B2B"
}</pre>
			<?php
			$parameter['custom_fields']['description'] = ob_get_clean();

			ob_start();
			?>
			<?php echo __( "The cc_emails argument accepts a JSON formatted string or a comma-separated list of additional email addresses. Here is an example:", 'wp-webhooks' ); ?>
			<pre>{
  "user-email": "jon@doe.test",
  "user-name": "Jon Doe"
}</pre>
			<?php
			$parameter['cc_emails']['description'] = ob_get_clean();

			$returns_code = array (
				'success' => true,
				'msg' => 'The ticket has been successfully created.',
				'data' => 
				array (
				  'cc_emails' => 
				  array (
					0 => 'jondoe@demodomain.com',
				  ),
				  'fwd_emails' => 
				  array (
				  ),
				  'reply_cc_emails' => 
				  array (
					0 => 'jondoe@demodomain.com',
				  ),
				  'ticket_cc_emails' => 
				  array (
					0 => 'jondoe@demodomain.com',
				  ),
				  'fr_escalated' => false,
				  'spam' => false,
				  'email_config_id' => NULL,
				  'group_id' => NULL,
				  'priority' => 2,
				  'requester_id' => 47003385765,
				  'responder_id' => NULL,
				  'source' => 2,
				  'company_id' => NULL,
				  'status' => 2,
				  'subject' => 'Demo Ticket Subject',
				  'support_email' => NULL,
				  'to_emails' => NULL,
				  'product_id' => NULL,
				  'id' => 2589,
				  'type' => NULL,
				  'due_by' => '2023-03-09T15:52:10Z',
				  'fr_due_by' => '2023-03-08T23:52:10Z',
				  'is_escalated' => false,
				  'description' => 'This is some demo description for the ticket.',
				  'description_text' => 'This is some demo description for the ticket.',
				  'custom_fields' => 
				  array (
				  ),
				  'created_at' => '2023-03-08T15:52:10Z',
				  'updated_at' => '2023-03-08T15:52:10Z',
				  'tags' => 
				  array (
				  ),
				  'attachments' => 
				  array (
				  ),
				),
			);

			return array(
				'action'			=> 'freshdesk_create_ticket',
				'name'			  => __( 'Create ticket', 'wp-webhooks' ),
				'sentence'			  => __( 'create a ticket', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Create a ticket within Freshdesk.', 'wp-webhooks' ),
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
			$subject	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'subject' );
			$description	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'description' );
			$email	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'email' );
			$priority	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'priority' );
			$status	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'status' );
			$cc_emails	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'cc_emails' );
			$custom_fields	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'custom_fields' );
			$full_response	 = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'full_response' ) === 'yes' ) ? true :false;
			$api_domain = '';
			$api_key = '';

			if( empty( $subject ) ){
				$return_args['msg'] = __( "Please set the subject argument.", 'wp-webhooks' );
				return $return_args;
			}

			if( empty( $description ) ){
				$return_args['msg'] = __( "Please set the description argument.", 'wp-webhooks' );
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
				'subject' => $subject,
				'description' => $description,
				'email' => $email,
				'priority' => 1,
				'status' => 0,
			);

			if( ! empty( $priority ) ){
				switch( $priority ){
					case 'prio_1': 
						$body['priority'] = 1;
						break;
					case 'prio_2': 
						$body['priority'] = 2;
						break;
					case 'prio_3': 
						$body['priority'] = 3;
						break;
					case 'prio_4': 
						$body['priority'] = 5;
						break;
				}
			}

			if( ! empty( $status ) ){
				switch( $status ){
					case 'status_2': 
						$body['status'] = 2;
						break;
					case 'status_3': 
						$body['status'] = 3;
						break;
					case 'status_4': 
						$body['status'] = 4;
						break;
					case 'status_5': 
						$body['status'] = 5;
						break;
					case 'status_6': 
						$body['status'] = 6;
						break;
					case 'status_7': 
						$body['status'] = 7;
						break;
				}
			}

			if( ! empty( $cc_emails ) ){
				$validated_emails = array();

				if( WPWHPRO()->helpers->is_json( $cc_emails ) ){
					$cc_emails = json_decode( $cc_emails, true );
					if( is_array( $cc_emails ) ){
						$validated_emails = $cc_emails;
					}
				} else {
					$cc_emails = explode( ',', $cc_emails );
					if( is_array( $cc_emails ) ){
						foreach( $cc_emails as $email ){
							$validated_emails[] = trim( $email );
						}
					}
				}

				$body['cc_emails'] = $validated_emails;
			}

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
	
			$api_url = 'https://' . $api_domain . '.freshdesk.com/api/v2/tickets';

			$response = WPWHPRO()->http->send_http_request( $api_url, $http_args );

			if( $full_response ){
				//Map the response
				$return_args = array_merge( $return_args, $response );
			} else {
				if( $response['success'] ){

					$return_args['success'] = true;
					$return_args['msg'] = __( "The ticket has been successfully created.", 'wp-webhooks' );
					$return_args['data'] = $response['content'];

				} else {
					$return_args['msg'] = __( "An error occured while creating the ticket.", 'wp-webhooks' );
					$return_args['data'] = $response['content'];
				}
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.