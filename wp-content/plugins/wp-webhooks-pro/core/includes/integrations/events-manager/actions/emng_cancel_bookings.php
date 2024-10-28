<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_events_manager_Actions_emng_cancel_bookings' ) ) :

	/**
	 * Load the emng_cancel_bookings action
	 *
	 * @since 4.3.6
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_events_manager_Actions_emng_cancel_bookings {

	public function get_details(){

			$parameter = array(
				'user'		=> array( 'required' => true, 'short_description' => __( 'Set this argument to the id of the user. In case you do not have the user id, you can also assign the user via a given email.', 'wp-webhooks' ) ),
				'event_ids'	=> array( 'required' => true, 'short_description' => __( 'Add the event ids you want to adjust the user bookings for. This argument accepts a comma-separated string, as well as a JSON construct.', 'wp-webhooks' ) ),
				'prevent_emails'	=> array( 'short_description' => __( 'Set this argument to yes if you want to prevent the status email to be sent. Default: no', 'wp-webhooks' ) ),
				'ignore_spaces'	=> array( 'short_description' => __( 'Set this argument to yes if you want to ignore the available slots/spaces for the given event. Default: no', 'wp-webhooks' ) ),
				'do_action'	  => array( 'short_description' => __( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', 'wp-webhooks' ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further data about the fired actions.', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			ob_start();
		?>
<?php echo __( "In case you want to add multiple event IDs, you can either comma-separate them like <code>2,3,12,44</code>, or you can add them via a JSON construct:", 'wp-webhooks' ); ?>
<pre>{
  23,
  3,
  44
}</pre>
<?php echo __( "You can also target all bookings of the user, regardless of the event. To do that, simply set the field to:", 'wp-webhooks' ); ?>
<pre>all</pre>
		<?php
		$parameter['event_ids']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>emng_cancel_bookings</strong> action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 1 );
function my_custom_callback_function( $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo __( "All the values that are sent back as a response to the initial webhook action caller.", 'wp-webhooks' ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns_code = array (
			'success' => true,
			'msg' => 'The bookings have been cancelled.',
			'data' => 
			array (
			  'user_id' => 148,
			  'events' => 
			  array (
				0 => 1,
			  ),
			),
		);

		return array(
			'action'			=> 'emng_cancel_bookings', //required
			'name'			   => __( 'Cancel bookings', 'wp-webhooks' ),
			'sentence'			   => __( 'cancel one or multiple bookings', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Cancel one, multiple, or all bookings for a user within Events Manager.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'events-manager',
			'premium'	   	=> true,
		);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'user_id' => 0,
					'events' => '',
				)
			);

			$user		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$event_ids		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'event_ids' );
			$prevent_emails		= ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'prevent_emails' ) === 'yes' ) ? true : false;
			$ignore_spaces		= ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'ignore_spaces' ) === 'yes' ) ? true : false;
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $user ) ){
				$return_args['msg'] = __( "Please set the user argument to either the user id or user email.", 'action-emng_cancel_bookings-error' );
				return $return_args;
			}

			if( empty( $event_ids ) ){
				$return_args['msg'] = __( "Please set the event_ids argument.", 'action-emng_cancel_bookings-error' );
				return $return_args;
			}

			$trigger_all_events = ( is_string( $event_ids ) && $event_ids === 'all' ) ? true : false;
			$validated_events = array();

			if( ! $trigger_all_events ){
				if( WPWHPRO()->helpers->is_json( $event_ids ) ){
					$validated_events = json_decode( $event_ids, true );
				} else {
					$validated_events = explode( ',', $event_ids );
				}
			}

            if( ! is_array( $validated_events ) && ! empty( $validated_events ) ){
                $validated_events = array( $validated_events );
            }

			foreach( $validated_events as $tk => $tv ){
				$validated_events[ $tk ] = intval( $tv );
			}

            $user_id = 0;

            if( ! empty( $user ) && is_numeric( $user ) ){
                $user_id = intval( $user );
            } elseif( ! empty( $user ) && is_email( $user ) ) {
                $user_data = get_user_by( 'email', $user );
                if( ! empty( $user_data ) && isset( $user_data->ID ) && ! empty( $user_data->ID ) ){
                    $user_id = $user_data->ID;
                }
            }

            if( empty( $user_id ) ){
                $return_args['msg'] = __( "We could not find a user for your given user data.", 'action-emng_cancel_bookings-error' );
				return $return_args;
            }

            $em_person = new EM_Person( array( 'user_id' => $user_id ) );

			if( ! empty( $em_person ) ){
				$em_bookings = $em_person->get_bookings( false );
				if( count( $em_bookings->bookings ) > 0 ){
					
					foreach( $em_bookings as $em_booking ){
						if( $trigger_all_events || in_array( $em_booking->event_id, $validated_events ) ){

							if( $trigger_all_events ){
								$validated_events[] = intval( $em_booking->event_id );
							}

							$send_email = ( ! $prevent_emails ) ? true : false;
							$ignore_spaces = ( $ignore_spaces ) ? true : false;
							$em_booking->set_status( 3, $send_email, $ignore_spaces );
						}
					}

					$return_args['success'] = true;
					$return_args['msg'] = __( "The bookings have been cancelled.", 'action-emng_cancel_bookings-success' );
					$return_args['data']['user_id'] = $user_id;
					$return_args['data']['events'] = $validated_events;

					if( ! empty( $do_action ) ){
						do_action( $do_action, $return_args );
					}
	
				} else {
					$return_args['msg'] = __( "The given user has no bookings.", 'action-emng_cancel_bookings-success' );
				}
			} else {
				$return_args['msg'] = __( "We could not find a booking person for the given user.", 'action-emng_cancel_bookings-success' );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.