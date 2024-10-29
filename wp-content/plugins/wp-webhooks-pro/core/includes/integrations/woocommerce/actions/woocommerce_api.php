<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_Actions_woocommerce_api' ) ) :

	/**
	 * Load the woocommerce_api action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_woocommerce_Actions_woocommerce_api {

		public function get_details(){

				$parameter = array(
				'consumer_key'	  => array( 'required' => true, 'short_description' => __( 'Your API consumer key. Please see the description for more information.', 'wp-webhooks' ) ),
				'consumer_secret'   => array( 'required' => true, 'short_description' => __( 'Your API consumer secret. Please see the description for more information.', 'wp-webhooks' ) ),
				'api_base'		  => array( 'required' => true, 'short_description' => __( 'The action you want to use. E.g. products/1234 - Please see the description for more information.', 'wp-webhooks' ) ),
				'api_method'		=> array( 'required' => true, 'short_description' => __( 'The method of the api call. E.g. get - Please see the description for more information.', 'wp-webhooks' ) ),
				'api_data'	  	=> array( 'short_description' => __( 'Additional data you want to send to the api call. Please see the description for more information.', 'wp-webhooks' ) ),
				'api_options'	  	=> array( 'short_description' => __( 'Extra arguments. Please see the description for more information.', 'wp-webhooks' ) ),
				'do_action'	  	=> array( 'short_description' => __( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', 'wp-webhooks' ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(mixed) The webhook data and response data.', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => false,
				'msg' => '',
				'data' => array()
			);

			$description = array(
				'steps' => array(
					__( 'Due to the complexity of this webhook, we added the exact documentaton within a separate article. Please read it here: ', 'wp-webhooks' ) . '<a href="https://wp-webhooks.com/docs/article-categories/wp-webhooks-pro-woocommerce/" target="_blank" title="Go to our docs">https://wp-webhooks.com/docs/article-categories/wp-webhooks-pro-woocommerce/</a>',
				),
				'tipps' => array(
					__( 'This webhook enables you to use the full functionality of the woocommerce REST API. It also works with all integrated extensions like <strong>Woocommerce Memberships</strong>, <strong>Woocommerce Subscriptions</strong> or <strong>Woocommerce Bookings</strong>.', 'wp-webhooks' ) . '<br>' . __( 'You can also add a custom action, that fires after the webhook was called. Simply specify your webhook identifier (e.g. my_csutom_webhook) and call it within your theme or plugin (e.g. add_action( "my_csutom_webhook", "my_csutom_webhook_callback" ) ).', 'wp-webhooks' ),
				),
			);

			return array(
				'action'			=> 'woocommerce_api', //required
				'name'			   => __( 'Woocommerce API call', 'wp-webhooks' ),
				'sentence'			   => __( 'perform a Woocommerce API call', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'The full power of the woocommerce API, packed within a webhook.', 'wp-webhooks' ),
				'description'	   => $description,
				'integration'	   => 'woocommerce',
				'premium'		  => true,
			);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array()
			);

			$consumer_key = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'consumer_key' );
			$consumer_secret = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'consumer_secret' );
			$api_base = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'api_base' );
			$api_method = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'api_method' );
			$api_data = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'api_data' );
			$api_options = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'api_options' );

			$do_action = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $api_base ) ){
				$return_args['msg'] = __( "The parameter api_base is required. Please set it first.", 'wp-webhooks' );
				return $return_args;
			} elseif( empty( $consumer_key ) ){
				$return_args['msg'] = __( "The parameter consumer_key is required. Please set it first.", 'wp-webhooks' );
				return $return_args;
			} elseif( empty( $consumer_secret ) ){
				$return_args['msg'] = __( "The parameter consumer_secret is required. Please set it first.", 'wp-webhooks' );
				return $return_args;
			} elseif( empty( $api_method ) ){
				$return_args['msg'] = __( "The parameter api_method is required. Please set it first.", 'wp-webhooks' );
				return $return_args;
			}

			$validated_data = null;
			if( WPWHPRO()->helpers->is_json( $api_data ) ){
				$validated_data = json_decode( $api_data );
			}

			
			switch( strtolower( $api_method ) ){
				case 'post':
					$method = 'post';
					break;
				case 'put':
					$method = 'put';
					break;
				case 'get':
					$method = 'get';
					break;
				case 'delete':
					$method = 'delete';
					break;
				case 'options':
					$method = 'options';
					break;
				default:
					$method = null;
					break;
			}

			$options = array();
			if( ! empty( $api_options ) && WPWHPRO()->helpers->is_json( $api_options ) ){
				$options = json_decode( $api_options, true );
				if( ! is_array( $options ) ){
					$options = array();
				}
			}

			if( $method !== null ){
				//Require Woocommerce Rest API Class
				$integration_folder = WPWHPRO()->integrations->get_integrations_folder();
				require( $integration_folder . '/woocommerce/misc/class-wpwhpro-woocommerce-api.php' );
				$api_handler = new WPWHPRO_Woocommerce_Load_API();

				$api_handler->load_woocommerce_api( array(
					'store_url' => home_url(),
					'consumer_key' => $consumer_key,
					'consumer_secret' => $consumer_secret,
					'options' => $options
				) );

				$response = $api_handler->wc_call( $method, $api_base, $validated_data );
				if( is_array( $response ) && $response['success'] ){
					$return_args['success'] = true;
					$return_args['data'] = $response;
					$return_args['msg'] = __( 'The api call was successful', 'wp-webhooks' );
				} else {
					$return_args['data'] = $response;
					$return_args['msg'] = __( 'Error making the API call', 'wp-webhooks' );
				}

			} else {
				$return_args['msg'] = __( "Your defined method is not registered. Please set a correct method. Possible values: post, put, get, delete, options", 'wp-webhooks' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.