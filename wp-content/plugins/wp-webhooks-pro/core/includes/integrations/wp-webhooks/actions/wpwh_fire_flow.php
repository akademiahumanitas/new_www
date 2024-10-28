<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_Actions_wpwh_fire_flow' ) ) :

	/**
	 * Load the wpwh_fire_flow action
	 *
	 * @since 5.2.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_Actions_wpwh_fire_flow {

	public function get_details(){

			$parameter = array(
				'flow_ids' => array( 
					'type' => 'select',
					'variable' => false,
					'multiple' => true,
					'label' => __( 'Flow(s)', 'wp-webhooks' ), 
					'short_description' => __( 'Please select the Flow(s) you would like to fire.', 'wp-webhooks' ),
					'query'			=> array(
						'filter'	=> 'flows',
						'args'		=> array()
					),
				),
				'raw_body'	   => array(
					'label' => __( 'Raw body (Payload data)', 'wp-webhooks' ), 
					'short_description' => __( '(string) This data will be used to fire the Flow of your choice. It means that this data will also be selected within the trigger of the fired flow. The argument accepts a string-formatted JSON.', 'wp-webhooks' ),
				),
				'body'	   => array( 
					'type' => 'repeater', 
					'variable' => false, 
					'multiple' => true, 
					'label' => __( 'Body (Payload data)', 'wp-webhooks' ), 
					'short_description' => __( '(string) This data will be used to fire the Flow of your choice. It means that this data will also be selected within the trigger of the fired flow.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further data about the fired triggers.', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			ob_start();
		?>
<?php echo __( "The <strong>flow_ids</strong> argument accepts a JSON formatted string, containing the flow IDs of the flows you want to fire.", 'wp-webhooks' ); ?>
<pre>[
	2,
	3
]
</pre>
		<?php
		$parameter['flow_ids']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "The <strong>raw_body</strong> argument accepts a JSON formatted string, containing further data you want to send along with the request.", 'wp-webhooks' ); ?>
<pre>{
	"demo-key": "This is some demo data"
}
</pre>
		<?php
		$parameter['raw_body']['description'] = ob_get_clean();

		$returns_code = array (
			'success' => true,
			'msg' => 'All Flows have been successfully fired.',
			'data' => array (
				'response' => array(
					'success' => array(
						2 => array(
							'success' => true,
							'msg' => 'Flow successfully executed.',
						)
					),
					'errors' => array()
				)
			),
		  );

		return array(
			'action'			=> 'wpwh_fire_flow', //required
			'name'			   => __( 'Fire flow', 'wp-webhooks' ),
			'sentence'			   => __( 'fire a flow', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Fire a flow using webhooks.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'wp-webhooks'
		);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'response' => array()
				)
			);

			$flow_ids		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'flow_ids' );
			$raw_body	= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'raw_body' );
			$body	= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'body' );

			if( empty( $flow_ids ) ){
				$return_args['msg'] = __( "Please set the flow_ids argument as it is required.", 'action-wpwh_fire_flow-error' );
				return $return_args;
			}

			$validated_flow_ids = array();

			if( is_string( $flow_ids ) && WPWHPRO()->helpers->is_json( $flow_ids ) ){
				$json_data = json_decode( $flow_ids, true );
				if( ! empty( $json_data ) && is_array( $json_data ) ){
					foreach( $json_data as $flow_id ){

						if( ! empty( $flow_id ) ){
							$flow_id = intval( trim( $flow_id ) );

							$validated_flow_ids[] = $flow_id;
						}

					}
				}
			}
	
			$validated_data = array();

			if( is_string( $raw_body ) && WPWHPRO()->helpers->is_json( $raw_body ) ){
				$raw_body_json = json_decode( $raw_body, true );
				if( ! empty( $raw_body_json ) && is_array( $raw_body_json ) ){
					$validated_data = array_merge( $validated_data, $raw_body_json );
				}
			}

			if( is_string( $body ) && WPWHPRO()->helpers->is_json( $body ) ){
				$body_json = json_decode( $body, true );
				if( ! empty( $body_json ) && is_array( $body_json ) ){
					$validated_data = array_merge( $validated_data, $body_json );
				}
			}

			$flows_feedback = array(
				'success' => array(),
				'errors' => array(),
			);

			foreach( $validated_flow_ids as $flow_id ){
				$flow_process = WPWHPRO()->flows->run_flow( $flow_id, array(
					'payload' => $validated_data,
				) );

				if( ! empty( $flow_process ) && is_array( $flow_process ) && $flow_process['success'] ){
					$flows_feedback['success'][ $flow_id ] = $flow_process;
				} else {
					$flows_feedback['errors'][ $flow_id ] = $flow_process;
				}
			}
			
			
			if( empty( $flows_feedback['errors'] ) ){

				$return_args['success'] = true;
				$return_args['msg'] = __( "All Flows have been successfully fired.", 'action-wpwh_fire_flow-success' );
				$return_args['data']['response'] = $flows_feedback;
			} else {
				$return_args['msg'] = __( "Error: One or multiple flows had errors during the execution.", 'action-wpwh_fire_flow-error' );
				$return_args['data']['response'] = $flows_feedback;
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.