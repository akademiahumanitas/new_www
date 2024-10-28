<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_fluent_crm_Actions_fcrm_contact_remove_tag' ) ) :

	/**
	 * Load the fcrm_contact_remove_tag action
	 *
	 * @since 4.3.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_fluent_crm_Actions_fcrm_contact_remove_tag {

	public function get_details(){

			$parameter = array(
				'email'			=> array( 'short_description' => __( 'Set the email of the contact/user you want to remove the tags from.', 'wp-webhooks' ) ),
				'user_id'		=> array( 'short_description' => __( 'In case you did not set the email, you can also assign the user via a given user id.', 'wp-webhooks' ) ),
				'tags'	=> array( 'required' => true, 'short_description' => __( 'Add the tags you want to remove from the user. This argument accepts a comma-separated string, as well as a JSON construct.', 'wp-webhooks' ) ),
				'do_action'	  => array( 'short_description' => __( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', 'wp-webhooks' ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further data about the fired triggers.', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			ob_start();
		?>
<?php echo __( "In case you want to remove multiple tags from the contact, you can either comma-separate them like <code>2,3,12,44</code>, or you can remove them via a JSON construct:", 'wp-webhooks' ); ?>
<pre>{
  23,
  3,
  44
}</pre>
		<?php
		$parameter['tags']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>fcrm_contact_remove_tag</strong> action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $return_args, $validated_user_email, $contact, $validated_tags ){
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
		<strong>$validated_user_email</strong> (string)<br>
		<?php echo __( "The email of the contact we removed the tags from.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$contact</strong> (array)<br>
		<?php echo __( "Further data about the contact.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$validated_tags</strong> (array)<br>
		<?php echo __( "An array of the tags that have been removed from the user.", 'wp-webhooks' ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns_code = array (
            'success' => true,
            'msg' => 'Tags have been removed from the given contact.',
            'data' => 
            array (
              'contact' => 
              array (
                'id' => '1',
                'user_id' => NULL,
                'hash' => 'c152149c03d10e23c036edbaXXXXXXX',
                'contact_owner' => NULL,
                'company_id' => NULL,
                'prefix' => 'Mr',
                'first_name' => 'Jon',
                'last_name' => 'Doe',
                'email' => 'jon.doe@demodomain.test',
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
                'phone' => '123456789',
                'status' => 'subscribed',
                'contact_type' => 'lead',
                'source' => NULL,
                'avatar' => NULL,
                'date_of_birth' => '1999-11-11',
                'created_at' => '2021-11-30 20:40:50',
                'last_activity' => NULL,
                'updated_at' => '2021-11-30 21:10:32',
                'photo' => 'https://www.gravatar.com/avatar/c152149c03d10e23c036edba08f95775?s=128',
                'full_name' => 'Jon Doe',
                'tags' => 
                array (
                  0 => 
                  array (
                    'id' => '1',
                    'title' => 'Demo Tag 1',
                    'slug' => 'demo-tag-1',
                    'description' => '',
                    'created_at' => '2021-12-01 10:22:36',
                    'updated_at' => '2021-12-01 10:22:36',
                    'pivot' => 
                    array (
                      'subscriber_id' => '1',
                      'object_id' => '1',
                      'object_type' => 'FluentCrm\\App\\Models\\Tag',
                      'created_at' => '2021-12-01 13:30:37',
                      'updated_at' => '2021-12-01 13:30:37',
                    ),
                  ),
                  1 => 
                  array (
                    'id' => '2',
                    'title' => 'Demo Tag 2',
                    'slug' => 'demo-tag-2',
                    'description' => '',
                    'created_at' => '2021-12-01 10:22:44',
                    'updated_at' => '2021-12-01 10:22:44',
                    'pivot' => 
                    array (
                      'subscriber_id' => '1',
                      'object_id' => '2',
                      'object_type' => 'FluentCrm\\App\\Models\\Tag',
                      'created_at' => '2021-12-01 13:28:27',
                      'updated_at' => '2021-12-01 13:28:27',
                    ),
                  ),
                ),
              ),
            ),
        );

		return array(
			'action'			=> 'fcrm_contact_remove_tag', //required
			'name'			   => __( 'Remove tags from contact', 'wp-webhooks' ),
			'sentence'			   => __( 'remove one or multiple tags from a contact', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Remove one or multiple tags from a contact within FluentCRM.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'fluent-crm',
            'premium'		   => true,
		);


		}

		public function execute( $return_data, $response_body ){

            $fcrm_helpers = WPWHPRO()->integrations->get_helper( 'fluent-crm', 'fcrm_helpers' );
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'contact' => array()
				)
			);

			$email		= sanitize_email( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'email' ) );
			$user_id		= intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_id' ) );
			$tags		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tags' );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $email ) && empty( $user_id ) ){
				$return_args['msg'] = __( "Please set either the user_email or the user_id argument.", 'action-fcrm_contact_remove_tag-error' );
				return $return_args;
			}

			if( empty( $tags ) ){
				$return_args['msg'] = __( "Please set the tags argument.", 'action-fcrm_contact_remove_tag-error' );
				return $return_args;
			}

			$validated_tags = array();
			if( WPWHPRO()->helpers->is_json( $tags ) ){
                $validated_tags = json_decode( $tags, true );
            } else {
				$validated_tags = explode( ',', $tags );
			}

            if( ! is_array( $validated_tags ) && ! empty( $validated_tags ) ){
                $validated_tags = array( $validated_tags );
            } 

            $validated_user_email = '';

            if( ! empty( $email ) ){
                $validated_user_email = $email;
            } elseif( ! empty( $user_id ) && is_numeric( $user_id ) ) {
                $user_data = get_userdata( $user_id );
                if( ! empty( $user_data ) && isset( $user_data->user_email ) && ! empty( $user_data->user_email ) ){
                    $validated_user_email = $user_data->user_email;
                }
            }

            if( empty( $validated_user_email ) ){
                $return_args['msg'] = __( "We could not find a contact for your given email or id.", 'action-fcrm_contact_remove_tag-error' );
				return $return_args;
            }

            $contact = $fcrm_helpers->get_contact( 'email', $validated_user_email );
			if( empty( $contact ) ) {
                $return_args['msg'] = __( "We could not fetch the current tags from your given contact.", 'action-fcrm_contact_remove_tag-error' );
				return $return_args;
            }

			
			if( is_array( $validated_tags ) ){
				$contact->detachTags( $validated_tags );

				$return_args['success'] = true;
				$return_args['msg'] = __( "Tags have been removed from the given contact.", 'action-fcrm_contact_remove_tag-success' );
				$return_args['data']['contact'] = $contact;
			} else {
				$return_args['msg'] = __( "Error: There was an issue validating the tags.", 'action-fcrm_contact_remove_tag-error' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $validated_user_email, $contact, $validated_tags );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.