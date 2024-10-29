<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_advanced_custom_fields_Actions_acf_update_options_page' ) ) :

	/**
	 * Load the acf_update_options_page action
	 *
	 * @since 4.2.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_advanced_custom_fields_Actions_acf_update_options_page {

	public function get_details(){

		$parameter = array(
			'meta_update' => array( 
				'type' => 'repeater',
				'label' => __( 'Add/Update ACF Meta', 'wp-webhooks' ),
				'short_description' => __( 'Update (or add) ACF meta keys/values.', 'wp-webhooks' ),
			),
			'manage_acf_data' => array( 
				'label' => __( 'Manage ACF Data (Advanced)', 'wp-webhooks' ),
				'short_description' => __( 'In case you want to add more complex ACF data, this field is for you. Check out some examples within our post meta blog post.', 'wp-webhooks' )
			),
			'do_action' => array( 'short_description' => __( 'Advanced: Register a custom action after the plugin fires this webhook.', 'wp-webhooks' ) )
		);

		$returns = array(
			'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'msg' => array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			'data' => array( 'short_description' => __( '(array) The adjusted meta data, includnig the response of the related ACF function." )', 'wp-webhooks' ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The given ACF data has been successfully executed.',
			'data' => 
			array (
			  'update_field' => 
			  array (
				0 => 
				array (
				  'selector' => 'your_text_field',
				  'value' => 'Some custom value',
				  'response' => true,
				),
			  ),
			),
		);

		ob_start();
		?>
<?php echo __( "This arguments accepts a JSON formatted string with the field key as the key and the ACF value as the value.", 'wp-webhooks' ); ?>
<br>
<pre>
{
	"meta_key": "Meta Value"
}
</pre>
		<?php
		$parameter['meta_update']['description'] = ob_get_clean();

		ob_start();
		WPWHPRO()->acf->load_acf_description();
		$parameter['manage_acf_data']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the acf_update_options_page action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 2 );
function my_custom_callback_function( $manage_acf_data, $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$manage_acf_data</strong> (String)<br>
		<?php echo __( "The ACF data that was sent by the webhook caller.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo __( "An array containing the information we will send back as the response to the initial webhook caller.", 'wp-webhooks' ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

			return array(
				'action'			=> 'acf_update_options_page',
				'name'			  => __( 'Update options page', 'wp-webhooks' ),
				'sentence'			  => __( 'update an options page', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Update custom options page data within "Advanced Custom Fields".', 'wp-webhooks' ),
				'description'	   => array(),
				'integration'	   => 'advanced-custom-fields',
				'premium' 			=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(),
			);
	
			$meta_update = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'meta_update' );
			$manage_acf_data = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'manage_acf_data' );
			$do_action = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $manage_acf_data ) && empty( $meta_update ) ){
				$return_args['msg'] = __( "Please set either the manage_acf_data or the meta_update argument.", 'wp-webhooks' );
				return $return_args;
			}

			if( ! empty( $meta_update ) ){
				$manage_acf_data = WPWHPRO()->acf->merge_repeater_meta_data( $manage_acf_data, $meta_update );
			}

			$return_args = WPWHPRO()->acf->manage_acf_meta( 0, $manage_acf_data, 'option' );

			if( ! empty( $do_action ) ){
				do_action( $do_action, $manage_acf_data, $return_args );
			}
	
			return $return_args;
	
		}

	}

endif; // End if class_exists check.