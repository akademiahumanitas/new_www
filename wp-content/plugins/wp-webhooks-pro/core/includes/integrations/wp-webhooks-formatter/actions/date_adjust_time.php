<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_date_adjust_time' ) ) :

	/**
	 * Load the date_adjust_time action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_date_adjust_time {

	public function get_details(){

		$parameter = array(
			'date'		=> array( 
				'required' => true, 
				'label' => __( 'Date', 'wp-webhooks' ), 
				'short_description' => __( 'The date you would like to add/subtract time from.', 'wp-webhooks' ),
			),
			'expression' => array( 
				'required' => true, 
				'label' => __( 'Expression', 'wp-webhooks' ), 
				'short_description' => __( 'Set the amount of time you would like to add or substract. Some examples: +4 hours, 1 month, +1 minute, -4 months, -2 hours (You can also combine them: +20 year +2 hours)', 'wp-webhooks' ),
			),
			'to_format'		=> array(
				'label' => __( 'Format to change to', 'wp-webhooks' ), 
				'default_value' => 'Y-m-d H:i:s',
				'short_description' => __( 'The date format you would like to change the date to. By default, we set it to: Y-m-d H:i:s', 'wp-webhooks' ),
			),
			'from_format'		=> array(
				'label' => __( 'Current format', 'wp-webhooks' ), 
				'short_description' => __( 'By default, we try to map the date format automatically. However, if you see the date format is wrongly interpreted, you can tell us the format here.', 'wp-webhooks' ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The date has been successfully adjusted.',
			'data' => '2022-03-10 17:16:18',
		);

		return array(
			'action'			=> 'date_adjust_time', //required
			'name'			   => __( 'Date add/subtract time', 'wp-webhooks' ),
			'sentence'			   => __( 'add or subtract time for a date', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Add or subtract time from a date.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'wp-webhooks-formatter',
			'premium'	   => true
		);


		}

		public function execute( $return_data, $response_body ){
			
			$return_args = array(
				'success' => false,
				'msg' => '',
			);

			$date = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'date' );
			$expression = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'expression' );
			$to_format = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'to_format' );
			$from_format = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'from_format' );
			
			if( empty( $expression ) ){
				$return_args['msg'] = __( "Please set the expression argument as it is required.", 'action-date_adjust_time-error' );
				return $return_args;
			}
			
			if( empty( $to_format ) ){
				$to_format = 'Y-m-d H:i:s';
			}

			if( ! empty( $from_format ) ){
				$date_instance = date_create_from_format( $from_format, $date );
				$date = date_format( $date_instance, 'Y-m-d H:i:s' );
			} else {
				$date = date( 'Y-m-d H:i:s', strtotime( $date ) );
			}

			$date_validated = date( $to_format, strtotime( $date . ' ' . $expression ) );

			$return_args['success'] = true;
			$return_args['msg'] = __( "The date has been successfully adjusted.", 'action-date_adjust_time-success' );
			$return_args['data'] = $date_validated;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.