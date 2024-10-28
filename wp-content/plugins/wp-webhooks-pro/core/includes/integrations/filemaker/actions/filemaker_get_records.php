<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_filemaker_Actions_filemaker_get_records' ) ) :

	/**
	 * Load the filemaker_get_records action
	 *
	 * @since 6.1.4
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_filemaker_Actions_filemaker_get_records {

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
					'short_description' => __( 'The name of the layout to use as the context for getting the record.', 'wp-webhooks' ),
				),
				'record_id'		=> array(
					'label' => __( 'Record ID', 'wp-webhooks' ), 
					'short_description' => __( 'The ID of a single record (In case you only want to return one).', 'wp-webhooks' ),
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

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(Array) Further data about the request.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The record(s) have been successfully retrieved.',
				'data' => 
				array (
				  'response' => 
				  array (
					'dataInfo' => 
					array (
					  'database' => 'DEMO_DB',
					  'layout' => 'DEMO_DB',
					  'table' => 'DEMO_DB',
					  'totalRecordCount' => 1,
					  'foundCount' => 1,
					  'returnedCount' => 1,
					),
					'data' => 
					array (
					  0 => 
					  array (
						'fieldData' => 
						array (
						  'Record_ID' => 1,
						  'Text Field 1' => 'Demo 1',
						  'Text Field 2' => 'Demo 2',
						  'Text Field 3' => '',
						  'Text Field 4' => '',
						  'Text Field 5' => '',
						  'Date Field 1' => '',
						  'Container Field 1' => '',
						  'Number Field 1' => 1,
						),
						'portalData' => 
						array (
						),
						'recordId' => '1',
						'modId' => '2',
					  ),
					),
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
					__( 'To learn more about this endpoint, please visit the following URL: ', 'wp-webhooks' ) . '<a title="FileMaker get records" target="_blank" href="https://help.claris.com/en/data-api-guide/content/get-range-of-records.html">https://help.claris.com/en/data-api-guide/content/get-range-of-records.html</a>',
				),
			);

			return array(
				'action'			=> 'filemaker_get_records', //required
				'name'			   	=> __( 'Get records', 'wp-webhooks' ),
				'sentence'			=> __( 'get one or multiple records', 'wp-webhooks' ),
				'parameter'		 	=> $parameter,
				'returns'		   	=> $returns,
				'returns_code'	  	=> $returns_code,
				'short_description' => __( 'Get one or multiple records within FileMaker.', 'wp-webhooks' ),
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
			$record_id = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'record_id' );
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

			$http_args = array(
				'method' => 'GET',
				'timeout' => ( ! empty( $timeout ) && is_numeric( $timeout ) ) ? $timeout : 30,
				'headers' => array(
					'content-type' => 'application/json',
					'expect' => '',
				),
			);

			$filemaker_auth = WPWHPRO()->integrations->get_auth( 'filemaker', 'filemaker_auth' );
			$http_args = $filemaker_auth->apply_auth( $auth_template, $http_args );
	
			if( empty( $http_args ) ){
                $return_args['msg'] = __( "An error occured applying the auth template.", 'wp-webhooks' );
				return $return_args;
            }

			$query_params = array();

			if( ! empty( $script_name ) ){
				$query_params['script'] = $script_name;
			}

			if( ! empty( $script_parameter ) ){
				$query_params['script.param'] = $script_parameter;
			}

			if( ! empty( $prerequest_script_name ) ){
				$query_params['script.prerequest'] = $prerequest_script_name;
			}

			if( ! empty( $prerequest_script_parameter ) ){
				$query_params['script.prerequest.param'] = $prerequest_script_parameter;
			}

			if( ! empty( $presort_script_name ) ){
				$query_params['script.presort'] = $presort_script_name;
			}

			if( ! empty( $presort_script_parameter ) ){
				$query_params['script.presort.param'] = $presort_script_parameter;
			}

			$base_url = $filemaker_auth->get_base_url( $auth_template, $http_args );
			$url = $base_url . '/layouts/' . $layout_name . '/records';

			if( ! empty( $record_id ) ){
				$url .= '/' . $record_id;
			}

			//assign the query params
			if( ! empty( $query_params ) ){
				$url = WPWHPRO()->helpers->built_url( $url, $query_params );
			}

			$response = WPWHPRO()->http->send_http_request( $url, $http_args );

			if( 
				! empty( $response )
				&& is_array( $response )
				&& isset( $response['success'] )
				&& $response['success']
			){

				$content = $response['content'];

				$return_args['success'] = true;
				$return_args['msg'] = __( "The record(s) have been successfully retrieved.", 'wp-webhooks' );
				$return_args['data'] = $content;

			} else {
				$return_args['msg'] = __( "An error occured while retrieving the record(s).", 'wp-webhooks' );

				if( isset( $response['content'] ) ){
					$return_args['data'] = $response['content'];
				}
			}
			
			return $return_args;
	
		}

	}

endif; // End if class_exists check.