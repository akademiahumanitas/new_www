<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_wp_manage_post_meta_data' ) ) :

	/**
	 * Load the wp_manage_post_meta_data action
	 *
	 * @since 4.3.7
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_wp_manage_post_meta_data {

	public function get_details(){

		$parameter = array(
			'post_id' => array( 'required' => true, 'short_description' => __( 'The ID of the post you want to perform the action for.', 'wp-webhooks' ) ),
			'meta_update' => array( 
				'type' => 'repeater',
				'label' => __( 'Add/Update Meta', 'wp-webhooks' ),
				'short_description' => __( 'Update (or add) meta keys/values.', 'wp-webhooks' ),
			),
			'manage_meta_data' => array( 
				'label' => __( 'Manage Meta Data (Advanced)', 'wp-webhooks' ),
				'short_description' => __( 'In case you want to add more complex meta data, this field is for you. Check out some examples within our post meta blog post.', 'wp-webhooks' )
			),
			'do_action' => array( 'short_description' => __( 'Advanced: Register a custom action after the plugin fires this webhook.', 'wp-webhooks' ) )
		);

		$returns = array(
			'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'msg' => array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			'data' => array( 'short_description' => __( '(array) The adjusted meta data, includnig the response of the related WP function." )', 'wp-webhooks' ) ),
		);

		$returns_code = array(
			'success' => true,
			'msg' => 'The meta data was successfully executed.',
			'data' => 
			array (
			  'update_post_meta' => 
			  array (
				0 => 
				array (
				  'meta_key' => 'demo_field',
				  'meta_value' => 'Some custom value',
				  'prev_value' => false,
				  'response' => 74015,
				),
			  ),
			),
		);

		ob_start();
		?>
<?php echo __( "This arguments accepts a JSON formatted string with the meta key as the key and the meta value as the value.", 'wp-webhooks' ); ?>
<br>
<pre>
{
	"meta_key": "Meta Value"
}
</pre>
		<?php
		$parameter['meta_update']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo __( "This argument integrates the full features of managing post related meta values (Advanced).", 'wp-webhooks' ); ?>
<br>
<p class="text-secondary">
  <strong><?php echo __( "Important:", 'wp-webhooks' ); ?></strong> <?php echo __( "We created a meta value generator that helps you to get started using this argument. You will find the generator here within the ACF section:", 'wp-webhooks' ); ?> <a target="_blank" title="<?php echo __( "Advanced Custom Fields Meta value generator", 'wp-webhooks' ); ?>" href="https://wp-webhooks.com/blog/how-to-update-custom-post-meta-values-with-wp-webhooks/">https://wp-webhooks.com/blog/how-to-update-custom-post-meta-values-with-wp-webhooks/</a>.
</p>
<br>
<br>
<?php echo __( "<strong>Please note</strong>: This argument is very powerful and requires some good understanding of JSON. It is integrated with the commonly used functions for managing post meta within WordPress. You can find a list of all avaialble functions here: ", 'wp-webhooks' ); ?>
<ul>
	<li><strong>add_post_meta()</strong>: <a title="Go to WordPress" target="_blank" href="https://developer.wordpress.org/reference/functions/add_post_meta/">https://developer.wordpress.org/reference/functions/add_post_meta/</a></li>
	<li><strong>update_post_meta()</strong>: <a title="Go to WordPress" target="_blank" href="https://developer.wordpress.org/reference/functions/update_post_meta/">https://developer.wordpress.org/reference/functions/update_post_meta/</a></li>
	<li><strong>delete_post_meta()</strong>: <a title="Go to WordPress" target="_blank" href="https://developer.wordpress.org/reference/functions/delete_post_meta/">https://developer.wordpress.org/reference/functions/delete_post_meta/</a></li>
</ul>
<br>
<?php echo __( "Down below you will find a complete JSON example that shows you how to use each of the functions above.", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "We also offer JSON to array/object serialization for single post meta values. This means, you can turn JSON into a serialized array or object.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "This argument accepts a JSON construct as an input. This construct contains each available function as a top-level key within the first layer and the assigned data respectively as a value. If you want to learn more about each line, please take a closer look at the bottom of the example.", 'wp-webhooks' ); ?>
<?php echo __( "Down below you will find a list that explains each of the top level keys.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong><?php echo __( "add_post_meta", 'wp-webhooks' ); ?></strong>
		<pre>{
   "add_post_meta":[
	  {
		"meta_key": "first_custom_key",
		"meta_value": "Some custom value"
	  },
	  {
		"meta_key": "second_custom_key",
		"meta_value": { "some_array_key": "Some array Value" },
		"unique": true
	  }
	]
}</pre>
		<?php echo __( "This key refers to the <strong>add_post_meta()</strong> function of WordPress:", 'wp-webhooks' ); ?> <a title="Go to WordPress" target="_blank" href="https://developer.wordpress.org/reference/functions/add_post_meta/">https://developer.wordpress.org/reference/functions/add_post_meta/</a><br>
		<?php echo __( "In the example above, you will find two entries within the add_post_meta key. The first one shows the default behavior using only the meta key and the value. This causes the meta key to be created without checking upfront if it exists - that allows you to create the meta value multiple times.", 'wp-webhooks' ); ?><br>
		<?php echo __( "As seen in the second entry, you will find a third key called <strong>unique</strong> that allows you to check upfront if the meta key exists already. If it does, the meta entry is neither created, nor updated. Set the value to <strong>true</strong> to check against existing ones. Default: false", 'wp-webhooks' ); ?><br>
		<?php echo __( "If you look closely to the second entry again, the value included is not a string, but a JSON construct, which is considered as an array and will therefore be serialized. The given value will be saved to the database in the following format: <code>a:1:{s:14:\"some_array_key\";s:16:\"Some array Value\";}</code>", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong><?php echo __( "update_post_meta", 'wp-webhooks' ); ?></strong>
		<pre>{
   "update_post_meta":[
	  {
		"meta_key": "first_custom_key",
		"meta_value": "Some custom value"
	  },
	  {
		"meta_key": "second_custom_key",
		"meta_value": "The new value",
		"prev_value": "The previous value"
	  }
	]
}</pre>
		<?php echo __( "This key refers to the <strong>update_post_meta()</strong> function of WordPress:", 'wp-webhooks' ); ?> <a title="Go to WordPress" target="_blank" href="https://developer.wordpress.org/reference/functions/update_post_meta/">https://developer.wordpress.org/reference/functions/update_post_meta/</a><br>
		<?php echo __( "The example above shows you two entries for this function. The first one is the default set up thats used in most cases. Simply define the meta key and the meta value and the key will be updated if it does exist and if it does not exist, it will be created.", 'wp-webhooks' ); ?><br>
		<?php echo __( "The third argument, as seen in the second entry, allows you to check against a previous value before updating. That causes that the meta value will only be updated if the previous key fits to whats currently saved within the database. Default: ''", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong><?php echo __( "delete_post_meta", 'wp-webhooks' ); ?></strong>
		<pre>{
   "delete_post_meta":[
	  {
		"meta_key": "first_custom_key"
	  },
	  {
		"meta_key": "second_custom_key",
		"meta_value": "Target specific value"
	  }
	]
}</pre>
		<?php echo __( "This key refers to the <strong>delete_post_meta()</strong> function of WordPress:", 'wp-webhooks' ); ?> <a title="Go to WordPress" target="_blank" href="https://developer.wordpress.org/reference/functions/delete_post_meta/">https://developer.wordpress.org/reference/functions/delete_post_meta/</a><br>
		<?php echo __( "Within the example above, you will see that only the meta key is required for deleting an entry. This will cause all meta keys on this post with the same key to be deleted.", 'wp-webhooks' ); ?><br>
		<?php echo __( "The second argument allows you to target only a specific meta key/value combination. This gets important if you want to target a specific meta key/value combination and not delete all available entries for the given post. Default: ''", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong><?php echo __( "wp_meta_fields", 'wp-webhooks' ); ?></strong>
		<pre>{
	"wp_meta_fields":{
		"meta_key_1":[
			"First value"
		],
		"meta_key_2":[
			"First value",
			"Second value"
		]
	}
}</pre>
		<?php echo __( "This key <strong>wp_meta_fields()</strong> is a special key that allows you to define a default WordPress meta field array, formatted within a JSON string.:", 'wp-webhooks' ); ?><br>
		<?php echo __( "Within the example above, you will see two meta keys are added. The first one contains only one value for the meta key. Within the second key (meta_key_2), you will see two values, which causes the meta key to be available twice within the post meta.", 'wp-webhooks' ); ?><br>
		<?php echo __( "This field is specifically useful if you want to synchronize data from another JSON that returns the WordPress meta in it's default format. Within Flows, you can define this field as followed:", 'wp-webhooks' ); ?>
		<pre>{
	"wp_meta_fields": YOUR_DYNAMIC_META_DATA_FROM_ANOTHER_RESPONSE
}</pre>
	</li>
</ol>
<strong><?php echo __( "Some tipps:", 'wp-webhooks' ); ?></strong>
<ol>
	<li><?php echo __( "You can include the value for this argument as a simple string to your webhook payload or you integrate it directly as JSON into your JSON payload (if you send a raw JSON response).", 'wp-webhooks' ); ?></li>
	<li><?php echo __( "Changing the order of the functions within the JSON causes the post meta to behave differently. If you, for example, add the <strong>delete_post_meta</strong> key before the <strong>update_post_meta</strong> key, the meta values will first be deleted and then added/updated.", 'wp-webhooks' ); ?></li>
	<li><?php echo __( "The webhook response contains a validted array that shows each initialized meta entry, as well as the response from its original WordPress function. This way you can see if the meta value was adjusted accordingly.", 'wp-webhooks' ); ?></li>
</ol>
		<?php
		$parameter['manage_meta_data']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the wp_manage_post_meta_data action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $manage_meta_data, $return_args, $meta_update ){
	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$manage_meta_data</strong> (String)<br>
		<?php echo __( "The WP data that was sent by the webhook caller.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo __( "An array containing the information we will send back as the response to the initial webhook caller.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$meta_update</strong> (array)<br>
		<?php echo __( "An array containing further information about the simplified meta that should be added/updated.", 'wp-webhooks' ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

			$description = array(
				'tipps' => array(
					__( "To create post meta values visually, you can use our meta value generator at: <a title=\"Visit our meta value generator\" href=\"https://wp-webhooks.com/blog/how-to-update-custom-post-meta-values-with-wp-webhooks/\" target=\"_blank\">https://wp-webhooks.com/blog/how-to-update-custom-post-meta-values-with-wp-webhooks/</a>.", 'wp-webhooks' ),
				)
			);

			return array(
				'action'			=> 'wp_manage_post_meta_data',
				'name'			  => __( 'Manage WP post meta', 'wp-webhooks' ),
				'sentence'			  => __( 'add, update, or delete WP post meta', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Add, update, or delete custom post meta data within "WordPress".', 'wp-webhooks' ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(),
			);
	
			$post_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_id' ) );
			$meta_update = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'meta_update' );
			$manage_meta_data = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'manage_meta_data' );
			$do_action = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $post_id ) ){
				$return_args['msg'] = __( "Please set the post_id argument first.", 'wp-webhooks' );
				return $return_args;
			}

			$post = get_post( $post_id );
			if( empty( $post ) ){
				$return_args['msg'] = __( "The post you try to update does not exist.", 'wp-webhooks' );
				return $return_args;
			}

			if( empty( $meta_update ) && empty( $manage_meta_data ) ){
				$return_args['msg'] = __( "Please set either the manage_meta_data or the meta_update argument.", 'wp-webhooks' );
				return $return_args;
			}

			$post_helpers = WPWHPRO()->integrations->get_helper( 'wordpress', 'post_helpers' );

			if( ! empty( $meta_update ) ){
				$manage_meta_data = $post_helpers->merge_repeater_meta_data( $manage_meta_data, $meta_update );
			}

			$return_args = $post_helpers->manage_post_meta_data( $post_id, $manage_meta_data );

			if( ! empty( $do_action ) ){
				do_action( $do_action, $manage_meta_data, $return_args, $meta_update );
			}
	
			return $return_args;
	
		}

	}

endif; // End if class_exists check.