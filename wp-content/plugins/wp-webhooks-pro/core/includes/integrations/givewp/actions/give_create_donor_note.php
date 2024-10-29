<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_givewp_Actions_give_create_donor_note' ) ) :

	/**
	 * Load the give_create_donor_note action
	 *
	 * @since 4.3.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_givewp_Actions_give_create_donor_note {

	public function get_details(){

			$parameter = array(
				'donor'			=> array( 'required' => true, 'short_description' => __( 'Set either the email for the donor or the user_id.', 'wp-webhooks' ) ),
				'donor_note'	=> array( 'short_description' => __( 'Set the note for the donor.', 'wp-webhooks' ) ),
				'do_action'	  => array( 'short_description' => __( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More infos are in the description.', 'wp-webhooks' ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further data about the fired triggers.', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>give_create_donor_note</strong> action was fired.", 'wp-webhooks' ); ?>
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
			'msg' => 'The donor note has been successfully created.',
			'data' => 
			array (
			  'donor' => 'jondoe@democustomer.test',
			  'donor_note' => 'This is a sample note for the given donor.',
			  'formatted_note' => 'January 24, 2022 06:00:49 - This is a sample note for the given donor.',
			),
		);

		return array(
			'action'			=> 'give_create_donor_note', //required
			'name'			   => __( 'Create donor note', 'wp-webhooks' ),
			'sentence'			   => __( 'create a donor note', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Create a donor note within GiveWP.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'givewp',
            'premium'		   => true,
		);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array()
			);

			$donor		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'donor' );
			$donor_note		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'donor_note' );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $donor ) ){
				$return_args['msg'] = __( "Please set the donor argument with either a valid email address or the user id.", 'action-give_create_donor_note-error' );
				return $return_args;
			}

			if( is_email( $donor ) ){
				$donor = sanitize_email( $donor );
				$donor_object = new Give_Donor( $donor, false );
			} else {
				$donor_object = new Give_Donor( $donor );
			}

            
			$formatted_note = $donor_object->add_note( $donor_note );

			
			if( ! empty( $formatted_note ) ){
				
				$return_args['success'] = true;
				$return_args['msg'] = __( "The donor note has been successfully created.", 'action-give_create_donor_note-success' );
				$return_args['data']['donor'] = $donor;
				$return_args['data']['donor_note'] = $donor_note;
				$return_args['data']['formatted_note'] = $formatted_note;
				
			} else {
				$return_args['msg'] = __( "Error: There was an issue creating the donor note.", 'action-give_create_donor_note-error' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.