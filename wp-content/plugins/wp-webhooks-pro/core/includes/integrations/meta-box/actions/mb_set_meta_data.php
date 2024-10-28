<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_meta_box_Actions_mb_set_meta_data' ) ) :

	/**
	 * Load the mb_set_meta_data action
	 *
	 * @since 5.2.4
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_meta_box_Actions_mb_set_meta_data {

		public function get_details(){

				$parameter = array(
				'object_id' => array( 
					'required' => true, 
					'short_description' => __( 'Object (post, term, user) ID. If you need to set value for an option (using MB Settings Page), object ID is the option name.', 'wp-webhooks' )
				),
				'meta_fields' => array( 
					'type' => 'repeater',
					'required' => true, 
					'label' => __( 'Meta fields', 'wp-webhooks' ),
					'short_description' => __( 'Update (or add) Meta Box meta keys/values. For grouped fields, add a JSON formatted string.', 'wp-webhooks' ),
				),
				'object_type' => array( 
					'type' => 'select',
					'choices' => array(
						'post' => array( 'label' => __( 'Post', 'wp-webhooks' ) ),
						'term' => array( 'label' => __( 'Taxonomy Term', 'wp-webhooks' ) ),
						'user' => array( 'label' => __( 'User', 'wp-webhooks' ) ),
						'setting' => array( 'label' => __( 'Settings page', 'wp-webhooks' ) ),
					), 
					'label' => __( 'Object type', 'wp-webhooks' ),
					'short_description' => __( 'Set a object type. For custom object types, use the args argument.', 'wp-webhooks' ),
				),
				'args' => array( 
					'label' => __( 'Custom arguments (Advanced)', 'wp-webhooks' ),
					'short_description' => __( 'A JSON formatted string. Can be used for extra arguments for some object types or storages. More details at: https://docs.metabox.io/functions/rwmb-set-meta/', 'wp-webhooks' )
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg' => array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data' => array( 'short_description' => __( '(array) The adjusted meta data, includnig the response of the related ACF function." )', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The meta data has been sent to Meta Box',
				'data' => 
				array (
				'meta_fields' => 
				array (
					'semail' => 
					array (
					'meta_key' => 'semail',
					'meta_value' => 'demo@domain.test',
					'response' => 'No response given by Meta Box',
					),
				),
				),
			);

			$description = array(
				'tipps' => array(
					__( "If you want to update meta values in a custom table, you can use the args argument along with the following JSON: <code>{
						\"storage_type\": \"custom_table\",
						\"table\": \"custom_metabox_table\"
					  }</code>", 'wp-webhooks' ),
				)
			);

			return array(
				'action'			=> 'mb_set_meta_data',
				'name'			  => __( 'Set meta data', 'wp-webhooks' ),
				'sentence'			  => __( 'set Meta Box meta data', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Set one or multiple meta data via Meta Box.', 'wp-webhooks' ),
				'description'	   => $description,
				'integration'	   => 'meta-box',
				'premium' 			=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'meta_fields' => array()
				),
			);
	
			$object_id = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'object_id' );
			$meta_fields = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'meta_fields' );
			$object_type = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'object_type' );
			$args = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'args' );

			if( empty( $object_id ) ){
				$return_args['msg'] = __( "Please set the object_id argument first.", 'wp-webhooks' );
				return $return_args;
			}

			if( empty( $meta_fields ) ){
				$return_args['msg'] = __( "Please set the meta_fields argument first.", 'wp-webhooks' );
				return $return_args;
			}

			$validated_args = array();
			if( WPWHPRO()->helpers->is_json( $args ) ){
				$validated_args_array = json_decode( $args, true );
				if( is_array( $validated_args_array ) ){
					$validated_args = $validated_args_array;
				}
			}

			//Overwrite the object type if a custom one is given
			if( ! isset( $validated_args['object_type'] ) ){
				if( ! empty( $object_type ) ){
					$validated_args['object_type'] = $object_type;
				} else {
					$validated_args['object_type'] = 'post';
				}
			}

			$validated_meta_fields = array();
			if( WPWHPRO()->helpers->is_json( $meta_fields ) ){
				$validated_meta_fields_array = json_decode( $meta_fields, true );
				if( is_array( $validated_meta_fields_array ) ){
					$validated_meta_fields = $validated_meta_fields_array;
				}
			}

			foreach( $validated_meta_fields as $meta_key => $meta_value ){

				if( WPWHPRO()->helpers->is_json( $meta_value ) ){
					//decode the meta if a JSON is given
					$meta_value = json_decode( $meta_value, true );
				} else {
					//save as a JSON if it is set within double brackets
					$trimmed_meta_value = trim( $meta_value, '"' );
					$is_json = WPWHPRO()->helpers->is_json( $trimmed_meta_value );
						
					if( $is_json ){
						$meta_value = $trimmed_meta_value;
					}
				}

				$meta_data_response = rwmb_set_meta( $object_id, $meta_key, $meta_value, $validated_args );

				$return_args['data']['meta_fields'][ $meta_key ] = array(
					'meta_key' => $meta_key,
					'meta_value' => $meta_value,
					'response' => ( $meta_data_response !== null ) ? $meta_data_response : __( "No response given by Meta Box", 'wp-webhooks' ),
				);

			}
			
			$return_args['success'] = true;
			$return_args['msg'] = __( "The meta data has been sent to Meta Box", 'wp-webhooks' );
	
			return $return_args;
	
		}

	}

endif; // End if class_exists check.