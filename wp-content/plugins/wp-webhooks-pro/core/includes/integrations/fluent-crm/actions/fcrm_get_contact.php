<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_fluent_crm_Actions_fcrm_get_contact' ) ) :

	/**
	 * Load the fcrm_get_contact action
	 *
	 * @since 4.3.5
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_fluent_crm_Actions_fcrm_get_contact {

	public function get_details(){

			$parameter = array(
				'contact_value'			=> array( 'required' => true, 'short_description' => __( 'Set this field to either the contact email or contact id.', 'wp-webhooks' ) ),
				'value_type'		=> array( 'short_description' => __( 'If you want to fetch the contact by a given user id, set this field do user_id. Default: default', 'wp-webhooks' ) ),
				'do_action'	  => array( 'short_description' => __( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More infos are in the description.', 'wp-webhooks' ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further data about the fired triggers.', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>fcrm_get_contact</strong> action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $return_args, $contact_value, $contact, $value_type ){
	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo __( "All the values that are sent back as a response to the initial webhook action caller.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$contact_value</strong> (string)<br>
		<?php echo __( "The value set with the contact_value argument.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$contact</strong> (array)<br>
		<?php echo __( "Further data about the contact.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$value_type</strong> (string)<br>
		<?php echo __( "The value set with the value_type argument.", 'wp-webhooks' ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns_code = array (
			'success' => true,
			'msg' => 'The contact has been successfully retrieved.',
			'data' => 
			array (
			  'contact' => 
			  array (
				'id' => '3',
				'user_id' => NULL,
				'hash' => 'eb87ffdba30105af809xxxxxx64b5fc7',
				'contact_owner' => NULL,
				'company_id' => NULL,
				'prefix' => NULL,
				'first_name' => 'Demo',
				'last_name' => 'contact',
				'email' => 'demoemail@demo.test',
				'timezone' => NULL,
				'address_line_1' => '',
				'address_line_2' => '',
				'postal_code' => '',
				'city' => '',
				'state' => '',
				'country' => '',
				'ip' => NULL,
				'latitude' => NULL,
				'longitude' => NULL,
				'total_points' => '0',
				'life_time_value' => '0',
				'phone' => '',
				'status' => 'subscribed',
				'contact_type' => 'lead',
				'source' => NULL,
				'avatar' => NULL,
				'date_of_birth' => '0000-00-00',
				'created_at' => '2022-01-11 07:04:02',
				'last_activity' => NULL,
				'updated_at' => '2022-01-23 12:03:39',
				'photo' => 'https://www.gravatar.com/avatar/eb87ffdba301xxxxxxxxxx5fc7?s=128',
				'full_name' => 'Jon Doe',
			  ),
			  'custom_fields' => array(
				'demo_field_1' => 23,
				'demo_field_2' => "Some text value of a custom field",
			  ),
			),
		);

		return array(
			'action'			=> 'fcrm_get_contact', //required
			'name'			   => __( 'Get contact', 'wp-webhooks' ),
			'sentence'			   => __( 'retrieve a contact', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Retrieve a contact within FluentCRM.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'fluent-crm',
			'premium'	   		=> true,
		);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'contact' => array(),
					'custom_fields' => array(),
				)
			);

			$contact_value		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'contact_value' );
			$value_type		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_id' );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $contact_value ) ){
				$return_args['msg'] = __( "Please set contact_value argument.", 'action-fcrm_get_contact-error' );
				return $return_args;
			}

			if( is_numeric( $contact_value ) ){
				$contact_value = intval( $contact_value );
			} else {
				$contact_value = sanitize_email( $contact_value );
			}

			if( empty( $value_type ) ){
				$value_type = 'default';
			}

			$contact_api = FluentCrmApi( 'contacts' );

			if( $value_type === 'user_id' ){
				$contact = $contact_api->getContactByUserRef( $contact_value );
			} else {
				$contact = $contact_api->getContact( $contact_value );
			}

			
			if( ! empty( $contact ) ){
				$return_args['success'] = true;
				$return_args['msg'] = __( "The contact has been successfully retrieved.", 'action-fcrm_get_contact-success' );
				$return_args['data']['contact'] = $contact;
				$return_args['data']['custom_fields'] = ( is_object( $contact ) && method_exists( $contact, 'custom_fields' ) ) ? $contact->custom_fields() : array();
			} else {
				$return_args['msg'] = __( "Error: There was an issue retrieveing the contact.", 'action-fcrm_get_contact-error' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $contact_value, $contact, $value_type );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.