<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wpcode_Actions_wpcode_activate_snippet' ) ) :

	/**
	 * Load the wpcode_activate_snippet action
	 *
	 * @since 6.1.4
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wpcode_Actions_wpcode_activate_snippet {

		public function get_details(){

				//These are the main arguments the user can use to input. You should always grab them within your action function.
			$parameter = array(
				'snippet_id'	   => array( 
					'required' => true, 
					'multiple' => false, 
					'type' => 'select', 
					'query'			=> array(
						'filter'	=> 'posts',
						'args'		=> array(
							'post_type' => 'wpcode'
						)
					),
					'label' => __( 'Snippet ID', 'wp-webhooks' ), 
					'short_description' => __( '(string) The ID of the snippet you want to activate.', 'wp-webhooks' ), 
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
				'msg' => 'The snippet has been successfully deactivated.',
				'data' => 
				array (
				  'snippet_id' => 9767,
				  'snippet_name' => 'Display a message after the 1st paragraph of posts',
				  'device_type' => 'any',
				  'location' => 'after_paragraph',
				  'location_extra' => '',
				  'author' => '1',
				  'priority' => 10,
				  'insert_method' => 1,
				  'note' => '',
				  'tags' => 
				  array (
					0 => 'message',
					1 => 'sample',
				  ),
				  'rules' => 
				  array (
				  ),
				  'code_type' => 'text',
				  'code' => 'Thank you for reading this post, don\'t forget to subscribe!',
				),
			);

			return array(
				'action'			=> 'wpcode_activate_snippet',
				'name'			  => __( 'Activate snippet', 'wp-webhooks' ),
				'sentence'			  => __( 'activate a snippet', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'This webhook action allows you to activate a snippet within the WPCode plugin.', 'wp-webhooks' ),
				'description'	   => array(),
				'integration'	   => 'wpcode',
				'premium' 			=> false,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(),
			);

			$snippet_id = absint( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'snippet_id' ) );

			if( empty( $snippet_id ) ){
				$return_args['msg'] = __( "No snippet was found based on your given data.", 'wp-webhooks' );
				return $return_args;
			}

			if( ! class_exists( 'WPCode_Snippet' ) ){
				$return_args['msg'] = __( "The WPCode_Snippet class does not exist.", 'wp-webhooks' );
				return $return_args;
			}

			$snippet = new WPCode_Snippet( $snippet_id );

			$snippet_data = array(
				'snippet_id' => $snippet->get_id(),
				'snippet_name' => $snippet->get_title(),
				'device_type' => $snippet->get_device_type(),
				'location' => $snippet->get_location(),
				'location_extra' => $snippet->get_location_extra(),
				'author' => $snippet->get_snippet_author(),
				'priority' => $snippet->get_priority(),
				'insert_method' => $snippet->get_auto_insert(),
				'note' => $snippet->get_note(),
				'tags' => $snippet->get_tags(),
				'rules' => $snippet->get_conditional_rules(),
				'code_type' => $snippet->get_code_type(),
				'code' => $snippet->get_code(),
			);

			if( $snippet->is_active() ){
				$return_args['success'] = true;
				$return_args['msg'] = __( "The snippet is already active.", 'wp-webhooks' );
				$return_args['data'] = $snippet_data;
				return $return_args;
			}

			$snippet->active = true;
			$snippet->run_activation_checks();

			$is_active = false;
			if( $snippet->active ){
				
				$post_args = array(
					'ID' 			=> $snippet->get_id(),
					'post_status' 	=> 'publish',
				);
				$success_on_update = wp_update_post( $post_args );

				if( $success_on_update ){
					$is_active = true;
				}
			}

			if( $is_active ){
				$return_args['msg'] = __( "The snippet has been successfully activated.", 'wp-webhooks' );
				$return_args['success'] = true;
				$return_args['data'] = $snippet_data;	
			} else {
				$return_args['msg'] = sprintf( __( "An error occured while activating the snippet. Details: %s", 'wp-webhooks' ), wpcode()->error->get_last_error_message() );
				$return_args['data'] = $snippet_data;
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.