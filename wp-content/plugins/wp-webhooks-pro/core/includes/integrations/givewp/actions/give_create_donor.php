<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_givewp_Actions_give_create_donor' ) ) :

	/**
	 * Load the give_create_donor action
	 *
	 * @since 4.3.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_givewp_Actions_give_create_donor {

	public function get_details(){

			$parameter = array(
				'email'			=> array( 'required' => true, 'short_description' => __( 'Set the email for the donor.', 'wp-webhooks' ) ),
				'name'		=> array( 'short_description' => __( 'Set the donor name. In case you leave this field empty, we try to fetch the name from the first_name and last_name arguments.', 'wp-webhooks' ) ),
				'user_id'		=> array( 'short_description' => __( 'The id of a WordPress user that you want to connect. If you leave it empty, we try to fetch the user from the given email.', 'wp-webhooks' ) ),
				'donor_company'	=> array( 'short_description' => __( 'The company of the donor.', 'wp-webhooks' ) ),
				'first_name'	=> array( 'short_description' => __( 'The first name of the donor.', 'wp-webhooks' ) ),
				'last_name'	=> array( 'short_description' => __( 'The last name of the donor.', 'wp-webhooks' ) ),
				'title_prefix'	=> array( 'short_description' => __( 'A title prefix for the donor name.', 'wp-webhooks' ) ),
				'do_action'	  => array( 'short_description' => __( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', 'wp-webhooks' ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further data about the fired triggers.', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>give_create_donor</strong> action was fired.", 'wp-webhooks' ); ?>
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
			'msg' => 'The donor has been successfully created.',
			'data' => 
			array (
			  'donor_id' => '3',
			  'donor_data' => 
			  array (
				'email' => 'jondoe@democustomer.test',
				'name' => 'Jon Doe',
				'user_id' => 154,
			  ),
			  'donor_company' => 'Demo Corp',
			  'first_name' => 'Jon',
			  'last_name' => 'Doe',
			  'title_prefix' => 'Dr. Dr.',
			),
		);

		return array(
			'action'			=> 'give_create_donor', //required
			'name'			   => __( 'Create donor', 'wp-webhooks' ),
			'sentence'			   => __( 'create a donor', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Create a donor within GiveWP.', 'wp-webhooks' ),
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

			$name		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'name' );
			$email		= sanitize_email( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'email' ) );
			$user_id		= intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_id' ) );
			$donor_company		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'donor_company' );
			$first_name		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'first_name' );
			$last_name		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'last_name' );
			$title_prefix		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'title_prefix' );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $email ) || ! is_email( $email ) ){
				$return_args['msg'] = __( "Please set the email argument with a valid email address.", 'action-give_create_donor-error' );
				return $return_args;
			}

			$donor_data = array(
				'email' => $email,
			);

			if( ! empty( $name ) ){
				$donor_data['name'] = $name;
			} elseif( ! empty( $first_name ) || ! empty( $last_name ) ){
				$donor_data['name'] = $first_name . ' ' . $last_name;
				$donor_data['name'] = trim( $donor_data['name'], ' ' );
			}

            if( empty( $user_id ) ){
                $user_data = get_user_by( 'email', $email );
                if( ! empty( $user_data ) && isset( $user_data->ID ) && ! empty( $user_data->ID ) ){
                    $user_id = $user_data->ID;
                }
            }

            if( ! empty( $user_id ) ){
                $donor_data['user_id'] = $user_id;
            }

            $donor    = new Give_Donor();
			$donor_id = $donor->create( $donor_data );

			
			if( ! empty( $donor_id ) ){
				$return_args['data']['donor_id'] = $donor_id;
				$return_args['data']['donor_data'] = $donor_data;
				
				if( ! empty( $donor_company ) ){
					Give()->donor_meta->update_meta( $donor_id, '_give_donor_company', $donor_company );
					$return_args['data']['donor_company'] = $donor_company;
				}
				
				if( ! empty( $first_name ) ){
					Give()->donor_meta->update_meta( $donor_id, '_give_donor_first_name', $first_name );
					$return_args['data']['first_name'] = $first_name;
				}
				
				if( ! empty( $last_name ) ){
					Give()->donor_meta->update_meta( $donor_id, '_give_donor_last_name', $last_name );
					$return_args['data']['last_name'] = $last_name;
				}
				
				if( ! empty( $title_prefix ) ){
					Give()->donor_meta->update_meta( $donor_id, '_give_donor_title_prefix', $title_prefix );
					$return_args['data']['title_prefix'] = $title_prefix;
				}

				$return_args['success'] = true;
				$return_args['msg'] = __( "The donor has been successfully created.", 'action-give_create_donor-success' );
				
			} else {
				$return_args['msg'] = __( "Error: There was an issue creating the donor.", 'action-give_create_donor-error' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.