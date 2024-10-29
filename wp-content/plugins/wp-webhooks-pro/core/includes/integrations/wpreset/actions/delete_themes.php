<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wpreset_Actions_delete_themes' ) ) :

	/**
	 * Load the delete_themes action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wpreset_Actions_delete_themes {

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
					'short_description' => __( 'Please set this value to "yes". If not set, no theme will be deleted.', 'wp-webhooks' ),
				),
				'keep_default_theme' => array(
					'type' => 'select',
					'multiple' => false,
					'choices' => array(
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
						'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
					),
					'default_value' => 'yes',
					'short_description' => __( 'Wether to keep the default theme or not. Possible values: "yes" and "no". Default: "yes"', 'wp-webhooks' ),
				),
				'do_action'	  => array( 'short_description' => __( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', 'wp-webhooks' ) )
			);

			ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the webhook action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $return_args, $confirm, $count ){
	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo __( "Contains all the data we send back to the webhook action caller.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$confirm</strong> (bool)<br>
		<?php echo __( "Returns true if the confirm argument was set correctly and false if not.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$count</strong> (integer)<br>
		<?php echo __( "Contains the number of deleted themes.", 'wp-webhooks' ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(mixed) Count of all the deleted themes.', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'Themes successfully deleted.',
				'data' => 
				array (
				  'count' => 2,
				),
			);

			return array(
				'action'			=> 'delete_themes', //required
				'name'			   => __( 'Delete themes', 'wp-webhooks' ),
				'sentence'			   => __( 'delete all themes', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Delete all themes on your website using webhooks.', 'wp-webhooks' ),
				'description'	   => array(),
				'integration'	   => 'wpreset'
			);


		}

		public function execute( $return_data, $response_body ){

			$reset_helpers = WPWHPRO()->integrations->get_helper( 'wpreset', 'reset_helpers' );
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'count' => 0
				)
			);

			$confirm			= ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'confirm' ) == 'yes' ) ? true : false;
			$keep_default_theme = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'keep_default_theme' ) == 'no' ) ? false : true;
			$do_action		  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( $confirm ){

				if (!function_exists('delete_theme')) {
					require_once ABSPATH . 'wp-admin/includes/ajax-actions.php';
				}

				$count = $reset_helpers->get_wp_reset()->do_delete_themes( $keep_default_theme );

				$return_args['success'] = true;
				$return_args['msg'] = __( "Themes successfully deleted.", 'action-delete_themes-success' );
				$return_args['data']['count'] = $count;

			} else {

				$return_args['msg'] = __( "Error: Themes not deleted. You did not set the confirmation parameter.", 'action-delete_themes-success' );

			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $confirm, $count );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.