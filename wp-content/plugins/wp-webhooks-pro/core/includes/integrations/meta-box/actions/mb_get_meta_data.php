<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_meta_box_Actions_mb_get_meta_data' ) ) :

	/**
	 * Load the mb_get_meta_data action
	 *
	 * @since 5.2.4
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_meta_box_Actions_mb_get_meta_data {

		public function get_details(){

				$parameter = array(
				'object_id' => array( 
					'required' => true, 
					'short_description' => __( 'Object (post, term, user) ID. If you need to set value for an option (using MB Settings Page), object ID is the option name.', 'wp-webhooks' )
				),
				'field_ids' => array(
					'required' => true, 
					'label' => __( 'Field IDs', 'wp-webhooks' ),
					'short_description' => __( 'A comma-separated string of the field IDs you want to return the value for.', 'wp-webhooks' ),
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
				'msg' => 'The meta data has been successfully returned.',
				'data' => 
				array (
				  'meta_fields' => 
				  array (
					'semail' => 
					array (
					  'meta_key' => 'semail',
					  'meta_value' => 'demo@domain.test',
					),
					'demo1' => 
					array (
					  'meta_key' => 'demo1',
					  'meta_value' => false,
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
				'action'			=> 'mb_get_meta_data',
				'name'			  => __( 'Get meta values', 'wp-webhooks' ),
				'sentence'			  => __( 'get Meta Box meta values', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Get one or multiple meta values via Meta Box.', 'wp-webhooks' ),
				'description'	   => $description,
				'integration'	   => 'meta-box',
				'premium' 			=> false,
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
			$field_ids = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'field_ids' );
			$object_type = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'object_type' );
			$args = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'args' );

			if( empty( $object_id ) ){
				$return_args['msg'] = __( "Please set the object_id argument first.", 'wp-webhooks' );
				return $return_args;
			}

			if( empty( $field_ids ) ){
				$return_args['msg'] = __( "Please set the field_ids argument first.", 'wp-webhooks' );
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
			$meta_fields_array = explode( ',', $field_ids );
			if( is_array( $meta_fields_array ) ){
				$validated_meta_fields = $meta_fields_array;
			}

			foreach( $validated_meta_fields as $field_id ){

				$meta_data_response = rwmb_get_value( $field_id, $validated_args, $object_id );

				$return_args['data']['meta_fields'][ $field_id ] = array(
					'meta_key' => $field_id,
					'meta_value' => $meta_data_response,
				);

			}
			
			$return_args['success'] = true;
			$return_args['msg'] = __( "The meta data has been successfully returned.", 'wp-webhooks' );
	
			return $return_args;
	
		}

	}

endif; // End if class_exists check.