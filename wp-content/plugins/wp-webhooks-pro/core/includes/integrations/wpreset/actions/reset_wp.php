<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wpreset_Actions_reset_wp' ) ) :

	/**
	 * Load the reset_wp action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wpreset_Actions_reset_wp {

	public function get_details(){

			$parameter = array(
				'confirm'			=> array( 
					'required' => true,
					'type' => 'select',
					'multiple' => false,
					'choices' => array(
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
						'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
					),
					'default_value' => 'no',
					'short_description' => __( 'Please set this value to "yes". If not set, nothing gets reset.', 'wp-webhooks' ),
				),
				'reactivate_theme'   => array( 
					'type' => 'select',
					'multiple' => false,
					'choices' => array(
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
						'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
					),
					'default_value' => 'no',
					'short_description' => __( 'Wether you want to reactivate the currently active theme again or not. Possible values: "yes" and "no". Default: "no"', 'wp-webhooks' ),
				),
				'reactivate_plugins' => array( 
					'type' => 'select',
					'multiple' => false,
					'choices' => array(
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
						'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
					),
					'default_value' => 'no',
					'short_description' => __( 'Wether you want to reactivate the currently active plugins again or not. Possible values: "yes" and "no". Default: "no"', 'wp-webhooks' ),
				),
				'reactivate_wpreset' => array( 
					'type' => 'select',
					'multiple' => false,
					'choices' => array(
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
						'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
					),
					'default_value' => 'no',
					'short_description' => __( 'Wether you want to reactivate WP Reset again or not. Possible values: "yes" and "no". Default: "no"', 'wp-webhooks' ),
				),
				'do_action'		  => array( 'short_description' => __( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', 'wp-webhooks' ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		   => array( 'short_description' => __( '(mixed) The attachment id on success, wp_error on inserting error, upload error on wrong upload or status code error.', 'wp-webhooks' ) ),
				'msg'			=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>reset_wp</strong> action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $reactivate_theme, $reactivate_plugins, $reactivate_wpreset ){
	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$reactivate_theme</strong> (bool)<br>
		<?php echo __( "True if you chose to reactivate installed themes.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$reactivate_plugins</strong> (bool)<br>
		<?php echo __( "True if you chose to reactivate installed plugins.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$reactivate_wpreset</strong> (bool)<br>
		<?php echo __( "True if you chose to reactivate WP Reset.", 'wp-webhooks' ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

			$returns_code = array (
				'success' => true,
				'msg' => 'Reset was successful.',
			);

			return array(
				'action'			=> 'reset_wp', //required
				'name'			   => __( 'Reset WordPress', 'wp-webhooks' ),
				'sentence'			   => __( 'reset WordPress', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Reset your whole website using webhooks.', 'wp-webhooks' ),
				'description'	   => array(),
				'integration'	   => 'wpreset'
			);


		}

		public function execute( $return_data, $response_body ){

			$reset_helpers = WPWHPRO()->integrations->get_helper( 'wpreset', 'reset_helpers' );
			$return_args = array(
				'success' => false,
				'msg' => '',
			);

			$confirm		= ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'confirm' ) == 'yes' ) ? true : false;
			$reactivate_theme	= ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'reactivate_theme' ) == 'yes' ) ? true : false;
			$reactivate_plugins  = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'reactivate_plugins' ) == 'yes' ) ? true : false;
			$reactivate_wpreset  = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'reactivate_wpreset' ) == 'yes' ) ? true : false;

			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( $confirm ){

				$args = array(
					'reactivate_theme' => $reactivate_theme,
					'reactivate_plugins' => $reactivate_plugins,
					'reactivate_wpreset' => $reactivate_wpreset
				);

				//Remove the redirect
				add_filter( 'wp_redirect', array( $reset_helpers, 'wpwhpro_remove_redirect_filter' ), 100, 2 );
				add_filter( 'wp-reset-override-is-cli-running', array( $reset_helpers, 'activate_cli_for_wp_reset' ), 100 );

				$reset_helpers->get_wp_reset()->do_reinstall( $args );

				//Add the redirect again
				remove_filter( 'wp_redirect', array( $reset_helpers, 'wpwhpro_remove_redirect_filter' ) );
				remove_filter( 'wp-reset-override-is-cli-running', array( $reset_helpers, 'activate_cli_for_wp_reset' ), 100 );

				$return_args['success'] = true;
				$return_args['msg'] = __( "Reset was successful.", 'action-create_url_attachment-success' );

			} else {
				$return_args['msg'] = __( "Error: Nothing was reset. You did not set the confirmation parameter.", 'action-delete_transients-success' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $reactivate_theme, $reactivate_plugins, $reactivate_wpreset );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.