<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_date_change_format' ) ) :

	/**
	 * Load the date_change_format action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_date_change_format {

	public function get_details(){

		$parameter = array(
			'date'		=> array( 
				'required' => true, 
				'label' => __( 'Date', 'wp-webhooks' ), 
				'short_description' => __( 'The date you would like to change the format for.', 'wp-webhooks' ),
			),
			'to_format'		=> array(
				'label' => __( 'Format to change to', 'wp-webhooks' ), 
				'default_value' => 'Y-m-d H:i:s',
				'short_description' => __( 'The date format you would like to change the date to. By default, we set it to: Y-m-d H:i:s. Set this to "timestamp" to return a Unix timestamp instead.', 'wp-webhooks' ),
				'description' => sprintf( __( 'See the description for examples. To learn more about the date and time format values, please refer to the table using the following link: %s', 'wp-webhooks' ), '<a target="_blank" href="https://www.php.net/manual/en/datetime.format.php">https://www.php.net/manual/en/datetime.format.php</a>' ),
			),
			'from_format'		=> array(
				'label' => __( 'Current format', 'wp-webhooks' ),
				'short_description' => __( 'By default, we try to map the date format automatically. However, if you see the date format is wrongly interpreted, you can tell us the format here. In case you are adding a unix timestamp, please set this value to: timestamp', 'wp-webhooks' ),
			),
			'to_timezone'		=> array(
				'label' => __( 'Timezone to change to', 'wp-webhooks' ),
				'type' => 'select',
				'query'			=> array(
					'filter'	=> 'timezones',
					'args'		=> array()
				),
				'short_description' => __( 'By default, we keep the time in the timezone you spefied the date for without altering it. When you provide a different, we will convert the time to it, considering it UTC if not otherwise specified. E.g. America/Los_Angeles', 'wp-webhooks' ),
			),
			'from_timezone'		=> array(
				'label' => __( 'Current timezone', 'wp-webhooks' ),
				'type' => 'select',
				'query'			=> array(
					'filter'	=> 'timezones',
					'args'		=> array()
				),
				'short_description' => __( 'The default timezone is UTC. If you specify a different timezone, we will consider the from_format from that timezone.', 'wp-webhooks' ),
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

		$description = array (
			'tipps' => array(
				__( 'This field supports all PHP time formats including custom text. Below are some examples.', 'wp-webhooks' ),
				__( '<code>F j, Y, g:i a</code> March 10, 20022, 5:16 pm', 'wp-webhooks' ),
				__( '<code>m.d.y</code> 03.10.22', 'wp-webhooks' ),
				__( '<code>j, n, Y</code> 10, 3, 2022', 'wp-webhooks' ),
				__( '<code>Ymd</code> 20220310', 'wp-webhooks' ),
				__( '<code>\i\t \i\s \t\h\e jS \d\a\y.</code> it is the 10th day.', 'wp-webhooks' ),
				__( '<code>D M j G:i:s T Y</code> Sat Mar 10 17:16:18 MST 2022', 'wp-webhooks' ),
				__( '<code>Y-m-d H:i:s</code> 2022-03-10 17:16:18 (the MySQL DATETIME format)', 'wp-webhooks' ),
				__( '<code>Y-m-d\TH:i:s\Z</code> 2022-06-28T22:15:00+00:00 (Datetime format using ISO8601 standard)', 'wp-webhooks' ),
				sprintf( __( 'To learn more about the date and time format values, please refer to the table using the following link: %s', 'wp-webhooks' ), '<a target="_blank" href="https://www.php.net/manual/en/datetime.format.php">https://www.php.net/manual/en/datetime.format.php</a>' ),
			),
		);

		return array(
			'action'			=> 'date_change_format', //required
			'name'			   => __( 'Date change format', 'wp-webhooks' ),
			'sentence'			   => __( 'change the date format', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Change the format of a given date and time.', 'wp-webhooks' ),
			'description'	   => $description,
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
			$to_format = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'to_format' );
			$from_format = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'from_format' );
			$to_timezone = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'to_timezone' );
			$from_timezone = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'from_timezone' );
			$default_timezone = date_default_timezone_get();
			
			if( empty( $to_format ) ){
				$to_format = 'Y-m-d H:i:s';
			}
			
			if( empty( $from_timezone ) ){
				$from_timezone = $default_timezone;
			}

			if( ! empty( $from_format ) ){
				
				if( $from_format === 'timestamp' ){
					$date = date( 'Y-m-d H:i:s', intval( $date ) );
				} else {
					$date_instance = date_create_from_format( $from_format, $date );
					$date = date_format( $date_instance, 'Y-m-d H:i:s' );
				}
				
			} else {
				$date = date( 'Y-m-d H:i:s', strtotime( $date ) );
			}

			if( ! empty( $to_timezone ) || $from_timezone !== $default_timezone ){

				//In case the from was set but not the to_timezone
				if( empty( $to_timezone ) ){
					$to_timezone = $default_timezone;
				}

				$from_timezone_date = new DateTimeZone( $from_timezone ); // Accepts: America/Los_Angeles
				$datetime = new DateTime( $date, $from_timezone_date );
				$to_timezone_date = new DateTimeZone( $to_timezone );

				$datetime->setTimezone( $to_timezone_date );

				$date = $datetime->format('Y-m-d H:i:s'); // Outputs: 2023-04-19 16:00:00
			}

			if( $to_format === 'timestamp' ){
				$date_validated = strtotime( $date );
			} else {
				$date_validated = date( $to_format, strtotime( $date ) );
			}

			$return_args['success'] = true;
			$return_args['msg'] = __( "The date has been successfully adjusted.", 'action-date_change_format-success' );
			$return_args['data'] = $date_validated;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.