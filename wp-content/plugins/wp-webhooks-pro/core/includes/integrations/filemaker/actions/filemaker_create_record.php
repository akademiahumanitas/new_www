<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_filemaker_Actions_filemaker_create_record' ) ) :

	/**
	 * Load the filemaker_create_record action
	 *
	 * @since 6.1.4
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_filemaker_Actions_filemaker_create_record {

		public function get_details(){

			$parameter = array(
				'auth_template'		=> array( 
					'required' => true, 
					'type' => 'select', 
					'multiple' => false, 
					'label' => __( 'Authentication template', 'wp-webhooks' ), 
					'query'			=> array(
						'filter'	=> 'authentications',
						'args'		=> array(
							'auth_methods' => array( 'filemaker_auth' )
						)
					),
					'short_description' => __( 'Use globally defined FileMaker credentials to authenticate this action.', 'wp-webhooks' ),
					'description' => __( 'This argument accepts the ID of a FileMaker authentication template of your choice. You can create new templates within the "Authentication" tab.', 'wp-webhooks' ),
				),
				'layout_name'		=> array(
					'required' => true, 
					'label' => __( 'Layout name', 'wp-webhooks' ), 
					'short_description' => __( 'The name of the layout to use as the context for creating the record.', 'wp-webhooks' ),
				),
				'field_data' => array(
					'label' => __( 'Field Data', 'wp-webhooks' ), 
					'short_description' => __( 'Add a JSON encoded string contianing the field data for your record.', 'wp-webhooks' ),
				),
				'script_name' => array(
					'label' => __( 'Script name', 'wp-webhooks' ), 
					'short_description' => __( 'The name of the script to be run after the action was executed.', 'wp-webhooks' ),
				),
				'script_parameter' => array(
					'label' => __( 'Script parameter', 'wp-webhooks' ), 
					'short_description' => __( 'The text string to use as a parameter for the script that was named by script.', 'wp-webhooks' ),
				),
				'prerequest_script_name' => array(
					'label' => __( 'Pre-request script name', 'wp-webhooks' ), 
					'short_description' => __( 'The name of the script to be run before the action and the subsequent sort.', 'wp-webhooks' ),
				),
				'prerequest_script_parameter' => array(
					'label' => __( 'Pre-request script parameter', 'wp-webhooks' ), 
					'short_description' => __( 'The text string to use as a parameter for the script that was named by prerequest_script_parameter.', 'wp-webhooks' ),
				),
				'presort_script_name' => array(
					'label' => __( 'Pre-sort script name', 'wp-webhooks' ), 
					'short_description' => __( 'The name of the script to be run after the action but before the subsequent sort.', 'wp-webhooks' ),
				),
				'presort_script_parameter' => array(
					'label' => __( 'Pre-sort script parameter', 'wp-webhooks' ), 
					'short_description' => __( 'The text string to use as a parameter for the script that was named by presort_script_name.', 'wp-webhooks' ),
				),
				'timeout'		=> array( 
					'required' => false, 
					'default_value' => 30, 
					'label' => __( 'Timeout', 'wp-webhooks' ), 
					'short_description' => __( 'Set the number of seconds you want to wait before the request to the FileMaker  runs into a timeout.', 'wp-webhooks' ),
				),
			);

			ob_start();
			?>
			<p><?php echo __( 'Add a JSON formattd string along with the Field names as the key and the field value as the value. Here is a basic example', 'wp-webhooks' ) ?></p>
<pre>
{ 
    "String Field": "value_1", 
    "Number Field": 99.99, 
    "repetitionField(1)" : "fieldValue" 
} 
</pre>
<?php
			$parameter['field_data']['description'] = ob_get_clean();

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(Array) Further data about the request.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The record has been successfully created.',
				'data' => 
				array (
				  'response' => 
				  array (
					'recordId' => '2',
					'modId' => '0',
				  ),
				  'messages' => 
				  array (
					0 => 
					array (
					  'code' => '0',
					  'message' => 'OK',
					),
				  ),
				),
			);

			$description = array(
				'tipps' => array(
					__( 'To learn more about this endpoint, please visit the following URL: ', 'wp-webhooks' ) . '<a title="FileMaker Helpcenter create record" target="_blank" href="https://help.claris.com/en/data-api-guide/content/create-record.html">https://help.claris.com/en/data-api-guide/content/create-record.html</a>',
				),
			);

			return array(
				'action'			=> 'filemaker_create_record', //required
				'name'			   	=> __( 'Create record', 'wp-webhooks' ),
				'sentence'			=> __( 'create a record', 'wp-webhooks' ),
				'parameter'		 	=> $parameter,
				'returns'		   	=> $returns,
				'returns_code'	  	=> $returns_code,
				'short_description' => __( 'Create a record within FileMaker.', 'wp-webhooks' ),
				'description'	   	=> $description,
				'integration'	   	=> 'filemaker',
				'premium'	   		=> true
			);

		}

		public function execute( $return_data, $response_body ){
			
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(),
			);

			$auth_template = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'auth_template' );
			$layout_name = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'layout_name' );
			$field_data = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'field_data' );
			$script_name = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'script_name' );
			$script_parameter = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'script_parameter' );
			$prerequest_script_name = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'prerequest_script_name' );
			$prerequest_script_parameter = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'prerequest_script_parameter' );
			$presort_script_name = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'presort_script_name' );
			$presort_script_parameter = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'presort_script_parameter' );
			$timeout = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'timeout' );

			if( empty( $auth_template ) ){
                $return_args['msg'] = __( "Please set the auth_template argument.", 'wp-webhooks' );
				return $return_args;
            }

			if( empty( $layout_name ) ){
                $return_args['msg'] = __( "Please set the layout_name argument.", 'wp-webhooks' );
				return $return_args;
            }

			if( ! empty( $field_data ) && ! WPWHPRO()->helpers->is_json( $field_data ) ){
                $return_args['msg'] = __( "The field_data argument is not a valid JSON.", 'wp-webhooks' );
				return $return_args;
            }

			$body = array();
			$field_data_arr = json_decode( $field_data, true );
			if( is_array( $field_data_arr ) ){
				$body['fieldData'] = $field_data_arr;
			}

			if( ! empty( $script_name ) ){
				$body['script'] = $script_name;
			}

			if( ! empty( $script_parameter ) ){
				$body['script.param'] = $script_parameter;
			}

			if( ! empty( $prerequest_script_name ) ){
				$body['script.prerequest'] = $prerequest_script_name;
			}

			if( ! empty( $prerequest_script_parameter ) ){
				$body['script.prerequest.param'] = $prerequest_script_parameter;
			}

			if( ! empty( $presort_script_name ) ){
				$body['script.presort'] = $presort_script_name;
			}

			if( ! empty( $presort_script_parameter ) ){
				$body['script.presort.param'] = $presort_script_parameter;
			}

			$http_args = array(
				'method' => 'POST',
				'timeout' => ( ! empty( $timeout ) && is_numeric( $timeout ) ) ? $timeout : 30,
				'headers' => array(
					'content-type' => 'application/json',
					'expect' => '',
				),
				'body' => json_encode( $body ),
			);

			$filemaker_auth = WPWHPRO()->integrations->get_auth( 'filemaker', 'filemaker_auth' );
			$http_args = $filemaker_auth->apply_auth( $auth_template, $http_args );
	
			if( empty( $http_args ) ){
                $return_args['msg'] = __( "An error occured applying the auth template.", 'wp-webhooks' );
				return $return_args;
            }

			$base_url = $filemaker_auth->get_base_url( $auth_template, $http_args );
			$url = $base_url . '/layouts/' . $layout_name . '/records';

			$response = WPWHPRO()->http->send_http_request( $url, $http_args );

			if( 
				! empty( $response )
				&& is_array( $response )
				&& isset( $response['success'] )
				&& $response['success']
			){

				$content = $response['content'];

				$return_args['success'] = true;
				$return_args['msg'] = __( "The record has been successfully created.", 'wp-webhooks' );
				$return_args['data'] = $content;

			} else {
				$return_args['msg'] = __( "An error occured while creating the record.", 'wp-webhooks' );

				if( isset( $response['content'] ) ){
					$return_args['data'] = $response['content'];
				}
			}
			
			return $return_args;
	
		}

	}

endif; // End if class_exists check.