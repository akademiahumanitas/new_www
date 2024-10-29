<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_update_post' ) ) :

	/**
	 * Load the create_post action
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_update_post {

		/*
	 * The core logic to handle the creation of a user
	 */
	public function get_details(){


		$parameter = array(
			'post_id'			   => array( 'required' => true, 'short_description' => __( '(int) The post id itself. This field is mandatory', 'wp-webhooks' ) ),
			'post_author'		   => array(
				'short_description' => __( '(mixed) The ID or the email of the user who added the post. Default is the current user ID.', 'wp-webhooks' ),
				'description' => __( "The post author argument accepts either the user id of a user, or the email address of an existing user. In case you choose the email adress, we try to match it with the users on your WordPress site. In case we couldn't find a user for the given email, we leave the field empty.", 'wp-webhooks' ),
			),
			'post_date'			 => array( 'short_description' => __( '(string) The date of the post. Default is the current time.', 'wp-webhooks' ) ),
			'post_date_gmt'		 => array( 'short_description' => __( '(string) The date of the post in the GMT timezone. Default is the value of $post_date.', 'wp-webhooks' ) ),
			'post_content'		  => array(
				'short_description' => __( '(string) The post content. Default empty.', 'wp-webhooks' ),
				'description' => __( "The post content is the main content area of the post. It can contain HTML or any other kind of content necessary for your functionality.", 'wp-webhooks' ),
			),
			'post_content_filtered' => array( 'short_description' => __( '(string) The filtered post content. Default empty.', 'wp-webhooks' ) ),
			'post_title'			=> array( 'short_description' => __( '(string) The post title. Default empty.', 'wp-webhooks' ) ),
			'post_excerpt'		  => array( 'short_description' => __( '(string) The post excerpt. Default empty.', 'wp-webhooks' ) ),
			'post_status'		   => array(
				'short_description' => __( '(string) The post status. Default \'draft\'.', 'wp-webhooks' ),
				'description' => __( "The post status defines further details about how your post will be treated. By default, WordPress offers the following post statuses: <strong>draft, pending, private, publish, future</strong>. Please note that other plugins can extend the post status values to offer a bigger variety, e.g. Woocommerce. If you use future, please make sure you set post_date_gmt too.", 'wp-webhooks' ),
			),
			'post_type'			 => array(
				'short_description' => __( '(string) The post type. Default \'post\'.', 'wp-webhooks' ),
				'description' => __( "The post type determines to which group of posts your currently updated post belongs. Please use the slug of the post type.", 'wp-webhooks' ),
			),
			'comment_status'		=> array( 'short_description' => __( '(string) Whether the post can accept comments. Accepts \'open\' or \'closed\'. Default is the value of \'default_comment_status\' option.', 'wp-webhooks' ) ),
			'ping_status'		   => array( 'short_description' => __( '(string) Whether the post can accept pings. Accepts \'open\' or \'closed\'. Default is the value of \'default_ping_status\' option.', 'wp-webhooks' ) ),
			'post_password'		 => array( 'short_description' => __( '(string) The password to access the post. Default empty.', 'wp-webhooks' ) ),
			'post_name'			 => array( 'short_description' => __( '(string) The post name. Default is the sanitized post title when creating a new post.', 'wp-webhooks' ) ),
			'to_ping'			   => array( 'short_description' => __( '(string) Space or carriage return-separated list of URLs to ping. Default empty.', 'wp-webhooks' ) ),
			'pinged'				=> array( 'short_description' => __( '(string) Space or carriage return-separated list of URLs that have been pinged. Default empty.', 'wp-webhooks' ) ),
			'post_modified'		 => array( 'short_description' => __( '(string) The date when the post was last modified. Default is the current time.', 'wp-webhooks' ) ),
			'post_modified_gmt'	 => array( 'short_description' => __( '(string) The date when the post was last modified in the GMT timezone. Default is the current time.', 'wp-webhooks' ) ),
			'post_parent'		   => array( 'short_description' => __( '(int) Set this for the post it belongs to, if any. Default 0.', 'wp-webhooks' ) ),
			'menu_order'			=> array( 'short_description' => __( '(int) The order the post should be displayed in. Default 0.', 'wp-webhooks' ) ),
			'post_mime_type'		=> array( 'short_description' => __( '(string) The mime type of the post. Default empty.', 'wp-webhooks' ) ),
			'guid'				  => array( 'short_description' => __( '(string) Global Unique ID for referencing the post. Default empty.', 'wp-webhooks' ) ),
			'post_category'		 => array( 'short_description' => __( '(string) A comma separated list of category IDs. Defaults to value of the \'default_category\' option. Example: cat_1,cat_2,cat_3. Please note that WordPress just accepts categories of the type "category" here.', 'wp-webhooks' ) ),
			'tags_input'			=> array( 'short_description' => __( '(string) A comma separated list of tag names, slugs, or IDs. Default empty. Please note that WordPress just accepts tags of the type "post_tag" here.', 'wp-webhooks' ) ),
			'tax_input'			 => array( 'short_description' => __( '(string) A simple or JSON formatted string containing existing taxonomy terms. Default empty.', 'wp-webhooks' ) ),
			'meta_input'		  	=> array( 'short_description' => __( '<strong>DEPRECATED! Please use manage_meta_data instead.</strong>', 'wp-webhooks' ) ),
			'meta_update' => array( 
				'type' => 'repeater',
				'label' => __( 'Add/Update Meta', 'wp-webhooks' ),
				'short_description' => __( 'Update (or add) meta keys/values.', 'wp-webhooks' ),
			),
			'manage_meta_data' => array( 
				'label' => __( 'Manage Meta Data (Advanced)', 'wp-webhooks' ),
				'short_description' => __( 'In case you want to add more complex meta data, this field is for you. Check out some examples within our post meta blog post.', 'wp-webhooks' )
			),
			'acf_meta_update' => array( 
				'type' => 'repeater',
				'label' => __( 'Add/Update ACF Meta', 'wp-webhooks' ),
				'short_description' => __( 'Update (or add) Advanced Custom Fields meta keys/values.', 'wp-webhooks' ),
			),
			'manage_acf_data' => array( 
				'label' => __( 'Manage ACF Data (Advanced)', 'wp-webhooks' ),
				'short_description' => __( 'In case you want to add more complex Advanced Custom Fields data, this field is for you. Check out some examples within our post meta blog post.', 'wp-webhooks' )
			),
			'wp_error'			  => array(
				'type' => 'select',
				'choices' => array(
					'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
					'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
				),
				'multiple' => false,
				'default_value' => 'no',
				'short_description' => __( 'Whether to return a WP_Error on failure. Posible values: "yes" or "no". Default value: "no".', 'wp-webhooks' ),
				'description' => __( "In case you set the <strong>wp_error</strong> argument to <strong>yes</strong>, we will return the WP Error object within the response if the webhook action call. It is recommended to only use this for debugging.", 'wp-webhooks' ),
			),
			'create_if_none'		=> array(
				'type' => 'select',
				'choices' => array(
					'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
					'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
				),
				'multiple' => false,
				'default_value' => 'no',
				'short_description' => __( 'Wether you want to create the post if it does not exists or not. Set it to "yes" or "no" Default is "no".', 'wp-webhooks' ),
				'description' => __( "In case you set the <strong>create_if_none</strong> argument to <strong>yes</strong>, a post will be created with the given details in case it does not exist.", 'wp-webhooks' )
			),
			'do_action'			 => array( 'short_description' => __( 'Advanced: Register a custom action after the plugin fires this webhook.', 'wp-webhooks' ) ),
		);

		ob_start();
		?>
<?php echo __( "This argument supports the default tags_input variable of the <strong>wp_update_post()</strong> function. Please use this function only if you are known to its functionality since WordPress might not add the values properly due to permissions. If you are not sure, please use the <strong>tax_input</strong> argument instead.", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Here is an example:", 'wp-webhooks' ); ?>
<pre>342,5678,2</pre>
<?php echo __( "This argument supports a comma separated list of tag names, slugs, or IDs.", 'wp-webhooks' ); ?>
		<?php
		$parameter['tags_input']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo __( "This argument allows you to add/append/delete any kind of taxonomies on your post. It uses a custom functionality that adds the taxonomies independently of the <strong>wp_update_post()</strong> function.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "To make it work, we offer certain different features and methods to make the most out of the taxonomy management. Down below, you will find further information about the whole functionality.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong><?php echo __( "String method", 'wp-webhooks' ); ?></strong><br>
		<?php echo __( "This method allows you to add/update/delete or bulk manage the post taxonomies using a simple string. Both the string and the JSON method support custom taxonomies too. In case you use more complex taxonomies that use semicolons or double points within the slugs, you need to use the JSON method.", 'wp-webhooks' ); ?>
		<ul class="list-group list-group-flush">
			<li class="list-group-item">
				<strong><?php echo __( "Replace existing taxonomy items", 'wp-webhooks' ); ?></strong>
				<br>
				<?php echo __( "This method allows you to replace already existing taxonomy items on the post. In case a taxonomy item does not exists at the point you want to add it, it will be ignored.", 'wp-webhooks' ); ?>
				<pre>taxonomy_1,tax_item_1:tax_item_2:tax_item_3;taxonomy_2,tax_item_5:tax_item_7:tax_item_8</pre>
				<?php echo __( "To separate the taxonomies from the single taxonomy items, please use a comma \",\". In case you want to add multiple items per taxonomy, you can separate them via a double point \":\". To separate multiple taxonomies from each other, please separate them with a semicolon \";\" (It is not necessary to set a semicolon at the end of the last one)", 'wp-webhooks' ); ?>
			</li>
			<li class="list-group-item">
				<strong><?php echo __( "Remove all taxonomy items for a single taxonomy", 'wp-webhooks' ); ?></strong>
				<br>
				<?php echo __( "In case you want to remove all taxonomy items from one or multiple taxonomies, you can set <strong>ironikus-remove-all;</strong> in front of a semicolon-separated list of the taxonomies you want to remove all items for. Here is an example:", 'wp-webhooks' ); ?>
				<pre>ironikus-remove-all;taxonomy_1;taxonomy_2</pre>
			</li>
			<li class="list-group-item">
				<strong><?php echo __( "Remove single taxonomy items for a taxonomy", 'wp-webhooks' ); ?></strong>
				<br>
				<?php echo __( "You can also remove only single taxonomy items for one or multiple taxonomies. Here is an example:", 'wp-webhooks' ); ?>
				<pre>ironikus-append;taxonomy_1,value_1:value_2-ironikus-delete:value_3;taxonomy_2,value_5:value_6:value_7-ironikus-delete</pre>
				<?php echo __( "In the example above, we append the taxonomies taxonomy_1 and taxonomy_2. We also add the taxonomy items value_1, value_3, value_5 and value_6. We also remove the taxonomy items value_2 and value_7.", 'wp-webhooks' ); ?>
			</li>
			<li class="list-group-item">
				<strong><?php echo __( "Append taxonomy items", 'wp-webhooks' ); ?></strong>
				<br>
				<?php echo __( "You can also append any taxonomy items without the existing ones being replaced. To do that, simply add <strong>ironikus-append;</strong> at the beginning of the string.", 'wp-webhooks' ); ?>
				<pre>ironikus-append;taxonomy_1,value_1:value_2:value_3;taxonomy_2,value_1:value_2:value_3</pre>
				<?php echo __( "In the example above, we append the taxonomies taxonomy_1 and taxonomy_2 with multiple taxonomy items on the post. The already assigned ones won't be replaced.", 'wp-webhooks' ); ?>
			</li>
		</ul>
	</li>
	<li>
	<strong><?php echo __( "JSON method", 'wp-webhooks' ); ?></strong><br>
		<?php echo __( "This method allows you to add/update/delete or bulk manage the post taxonomies using a simple string. Both the string and the JSON method support custom taxonomies too.", 'wp-webhooks' ); ?>
		<ul class="list-group list-group-flush">
			<li class="list-group-item">
				<strong><?php echo __( "Replace existing taxonomy items", 'wp-webhooks' ); ?></strong>
				<br>
				<?php echo __( "This JSON allows you to replace already existing taxonomy items on the post. In case a taxonomy item does not exists at the point you want to add it, it will be ignored.", 'wp-webhooks' ); ?>
				<pre>{
  "category": [
	"test-category",
	"second-category"
  ],
  "post_tag": [
	"dog",
	"male",
	"simple"
  ]
}</pre>
				<?php echo __( "The key on the first layer of the JSON is the slug of the taxonomy. As a value, it accepts multiple slugs of the single taxonomy terms. To add multiple taxonomies, simply append them on the first layer of the JSON.", 'wp-webhooks' ); ?>
			</li>
			<li class="list-group-item">
				<strong><?php echo __( "Remove all taxonomy items for a single taxonomy", 'wp-webhooks' ); ?></strong>
				<br>
				<?php echo __( "In case you want to remove all taxonomy items from one or multiple taxonomies, you can set <strong>ironikus-remove-all</strong> as a separate value with the <strong>wpwhtype</strong> key. The <strong>wpwhtype</strong> key is a reserved key for further actions on the data. Here is an example:", 'wp-webhooks' ); ?>
				<pre>{
  "wpwhtype": "ironikus-remove-all",
  "category": [],
  "post_tag": []
}</pre>
			</li>
			<li class="list-group-item">
				<strong><?php echo __( "Append taxonomy items", 'wp-webhooks' ); ?></strong>
				<br>
				<?php echo __( "You can also append any taxonomy items without the existing ones being replaced. To do that, simply add <strong>ironikus-append</strong> to the <strong>wpwhtype</strong> key. The <strong>wpwhtype</strong> key is a reserved key for further actions on the data. All the taxonomies you add after, will be added to the existing ones on the post.", 'wp-webhooks' ); ?>
				<pre>{
  "wpwhtype": "ironikus-append",
  "category": [
	"test-category",
	"second-category"
  ],
  "post_tag": [
	"dog"
  ]
}</pre>
				<?php echo __( "In the example above, we append the taxonomies category and post_tag with multiple taxonomy items on the post. The already assigned ones won't be replaced.", 'wp-webhooks' ); ?>
			</li>
			<li class="list-group-item">
				<strong><?php echo __( "Remove single taxonomy items for a taxonomy", 'wp-webhooks' ); ?></strong>
				<br>
				<?php echo __( "You can also remove only single taxonomy items for one or multiple taxonomies. To do that, simply append <strong>-ironikus-delete</strong> at the end of the taxonomy term slug. This specific taxonomy term will then be removed from the post. Here is an example:", 'wp-webhooks' ); ?>
				<pre>{
  "wpwhtype": "ironikus-append",
  "category": [
	"test-category",
	"second-category-ironikus-delete"
  ],
  "post_tag": [
	"dog-ironikus-delete"
  ]
}</pre>
				<?php echo __( "In the example above, we append the taxonomies category and post_tag. We also add the taxonomy item test-category. We also remove the taxonomy items second-category and dog.", 'wp-webhooks' ); ?>
			</li>
		</ul>
	</li>
</ol>
		<?php
		$parameter['tax_input']['description'] = ob_get_clean();

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
<?php echo __( "This argument is specifically designed to add/update or remove post meta to your updated post.", 'wp-webhooks' ); ?>
					<br>
					<?php echo __( "To create/update or delete custom meta values, we offer you two different ways:", 'wp-webhooks' ); ?>
					<ol>
						<li>
							<strong><?php echo __( "String method", 'wp-webhooks' ); ?></strong>
							<br>
							<?php echo __( "This method allows you to add/update or delete the post meta using a simple string. To make it work, separate the meta key from the value using a comma (,). To separate multiple meta settings from each other, simply separate them with a semicolon (;). To remove a meta value, simply set as a value <strong>ironikus-delete</strong>", 'wp-webhooks' ); ?>
							<pre>meta_key_1,meta_value_1;my_second_key,ironikus-delete</pre>
							<?php echo __( "<strong>IMPORTANT:</strong> Please note that if you want to use values that contain commas or semicolons, the string method does not work. In this case, please use the JSON method.", 'wp-webhooks' ); ?>
						</li>
						<li>
						<strong><?php echo __( "JSON method", 'wp-webhooks' ); ?></strong>
							<br>
							<?php echo __( "This method allows you to add/update or remove the post meta using a JSON formatted string. To make it work, add the meta key as the key and the meta value as the value. To delete a meta value, simply set the value to <strong>ironikus-delete</strong>. Here's an example on how this looks like:", 'wp-webhooks' ); ?>
<pre>{
"meta_key_1": "This is my meta value 1",
"another_meta_key": "This is my second meta key!"
"third_meta_key": "ironikus-delete"
}</pre>
						</li>
					</ol>
					<strong><?php echo __( "Advanced", 'wp-webhooks' ); ?></strong>: <?php echo __( "We also offer JSON to array/object serialization for single post meta values. This means, you can turn JSON into a serialized array or object.", 'wp-webhooks' ); ?>
					<br>
					<?php echo __( "As an example: The following JSON <code>{\"price\": \"100\"}</code> will turn into <code>O:8:\"stdClass\":1:{s:5:\"price\";s:3:\"100\";}</code> with default serialization or into <code>a:1:{s:5:\"price\";s:3:\"100\";}</code> with array serialization.", 'wp-webhooks' ); ?>
					<ol>
						<li>
							<strong><?php echo __( "Object serialization", 'wp-webhooks' ); ?></strong>
							<br>
							<?php echo __( "This method allows you to serialize a JSON to an object using the default json_decode() function of PHP.", 'wp-webhooks' ); ?>
							<br>
							<?php echo __( "To serialize your JSON to an object, you need to add the following string in front of the escaped JSON within the value field of your single meta value of the meta_input argument: <code>ironikus-serialize</code>. Here's a full example:", 'wp-webhooks' ); ?>
<pre>{
"meta_key_1": "This is my meta value 1",
"another_meta_key": "This is my second meta key!",
"third_meta_key": "ironikus-serialize{\"price\": \"100\"}"
}</pre>
							<?php echo __( "This example will create three post meta entries. The third entry has the meta key <strong>third_meta_key</strong> and a serialized meta value of <code>O:8:\"stdClass\":1:{s:5:\"price\";s:3:\"100\";}</code>. The string <code>ironikus-serialize</code> in front of the escaped JSON will tell our plugin to serialize the value. Please note that the JSON value, which you include within the original JSON string of the meta_input argument, needs to be escaped.", 'wp-webhooks' ); ?>
						</li>
						<li>
							<strong><?php echo __( "Array serialization", 'wp-webhooks' ); ?></strong>
							<br>
							<?php echo __( "This method allows you to serialize a JSON to an array using the json_decode( \$json, true ) function of PHP.", 'wp-webhooks' ); ?>
							<br>
							<?php echo __( "To serialize your JSON to an array, you need to add the following string in front of the escaped JSON within the value field of your single meta value of the meta_input argument: <code>ironikus-serialize-array</code>. Here's a full example:", 'wp-webhooks' ); ?>
<pre>{
"meta_key_1": "This is my meta value 1",
"another_meta_key": "This is my second meta key!",
"third_meta_key": "ironikus-serialize-array{\"price\": \"100\"}"
}</pre>
							<?php echo __( "This example will create three post meta entries. The third entry has the meta key <strong>third_meta_key</strong> and a serialized meta value of <code>a:1:{s:5:\"price\";s:3:\"100\";}</code>. The string <code>ironikus-serialize-array</code> in front of the escaped JSON will tell our plugin to serialize the value. Please note that the JSON value, which you include within the original JSON string of the meta_input argument, needs to be escaped.", 'wp-webhooks' ); ?>
						</li>
					</ol>
		<?php
		$parameter['meta_input']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo __( "This argument integrates the full features of managing post related meta values.", 'wp-webhooks' ); ?>
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
<?php echo __( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the update_post action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $post_data, $post_id, $meta_input, $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$post_data</strong> (array)<br>
		<?php echo __( "Contains the data that is used to update the post and some additional data as the meta input.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$post_id</strong> (integer)<br>
		<?php echo __( "Contains the post id of the newly updated post. Please note that it can also contain a wp_error object since it is the response of the wp_update_user() function.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$meta_input</strong> (string)<br>
		<?php echo __( "Contains the unformatted post meta as you sent it over within the webhook request as a string.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo __( "An array containing the information we will send back as the response to the initial webhook caller.", 'wp-webhooks' ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		//Remove if ACF isn't active
		if( ! WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
			unset( $parameter['manage_acf_data'] );
		} else {
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
		$parameter['acf_meta_update']['description'] = ob_get_clean();

			ob_start();
			WPWHPRO()->acf->load_acf_description();
			$parameter['manage_acf_data']['description'] = ob_get_clean();
		}

		$returns = array(
			'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'data'		=> array( 'short_description' => __( '(array) User related data as an array. We return the post id with the key "post_id" and the post data with the key "post_data". E.g. array( \'data\' => array(...) )', 'wp-webhooks' ) ),
			'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
		);

			$returns_code = array (
				'success' => true,
				'msg' => 'Post successfully updated',
				'data' => 
				array (
				  'post_id' => 1339,
				  'post_data' => 
				  array (
					'ID' => 1339,
					'post_content' => 'Some new post content.',
					'post_title' => 'The new post title',
					'post_status' => 'publish',
					'meta_data' => false,
					'tax_input' => false,
				  ),
				  'permalink' => 'https://yourdomain.test/blog/2021/08/28/post-name/',
				),
			);

			$description = array(
				'tipps' => array(
					__( 'To update a post, you only need to set the values you want to update. The undefined settings won\'t be overwritten.', 'wp-webhooks' ),
					__( 'In case you want to create the post if it does not exists at that point, you can set the <strong>create_if_none</strong> argument to <strong>yes</strong>', 'wp-webhooks' ),
				),
			);

			return array(
				'action'			=> 'update_post',
				'name'			  => __( 'Update post', 'wp-webhooks' ),
				'sentence'			  => __( 'update a post', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Update a post. You have all functionalities available from wp_update_post', 'wp-webhooks' ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> true,
			);

		}

		/**
		 * Create a post via an action call
		 *
		 * @param $update - Wether to create or to update the post
		 */
		public function execute( $return_data, $response_body ){

			$update = true;
			$post_helpers = WPWHPRO()->integrations->get_helper( 'wordpress', 'post_helpers' );
			$return_args = array(
				'success'   => false,
				'msg'	   => '',
				'data'	  => array(
					'post_id' => null,
					'post_data' => null,
					'permalink' => ''
				)
			);

			$post_id				= intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_id' ) );

			$post_author			= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_author' );
			$post_date			  = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_date' ) );
			$post_date_gmt		  = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_date_gmt' ) );
			$post_content		   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_content' );
			$post_content_filtered  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_content_filtered' );
			$post_title			 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_title' );
			$post_excerpt		   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_excerpt' );
			$post_status			= sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_status' ) );
			$post_type			  = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_type' ) );
			$comment_status		 = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_status' ) );
			$ping_status			= sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'ping_status' ) );
			$post_password		  = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_password' ) );
			$post_name			  = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_name' ) );
			$to_ping				= sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'to_ping' ) );
			$pinged				 = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'pinged' ) );
			$post_modified		  = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_modified' ) );
			$post_modified_gmt	  = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_modified_gmt' ) );
			$post_parent			= sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_parent' ) );
			$menu_order			 = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'menu_order' ) );
			$post_mime_type		 = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_mime_type' ) );
			$guid				   = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'guid' ) );
			$import_id			  = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'import_id' ) );
			$post_category		  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_category' );
			$tags_input			 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tags_input' );
			$tax_input			  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tax_input' );
			$meta_input			 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'meta_input' ); // Deprecated
			$meta_update 		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'meta_update' );
			$manage_meta_data 		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'manage_meta_data' );
			$acf_meta_update 	= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'acf_meta_update' );
			$manage_acf_data		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'manage_acf_data' );
			$wp_error			   = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'wp_error' ) == 'yes' )	 ? true : false;
			$create_if_none		 = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'create_if_none' ) == 'yes' )	 ? true : false;
			$do_action			  = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' ) );

			if( $update && ! $create_if_none ){
				if ( empty( $post_id ) ) {
					$return_args['msg'] = __( "The post id is required to update a post.", 'wp-webhooks' );

					return $return_args;
				}
			}

			$create_post_on_update = false;
			$post_data = array();

			if( $update ){
				$post = '';

				if( ! empty( $post_id ) ){
					$post = get_post( $post_id );
				}

				if( ! empty( $post ) ){
					if( ! empty( $post->ID ) ){
						$post_data['ID'] = $post->ID;
					}
				}

				if( empty( $post_data['ID'] ) ){

					$create_post_on_update = apply_filters( 'wpwhpro/run/create_action_post_on_update', $create_if_none );

					if( empty( $create_post_on_update ) ){
						$return_args['msg'] = __( "Post not found.", 'wp-webhooks' );

						return $return_args;
					}

				}

			}

			if( ! empty( $post_author ) ){

				$post_author_id = 0;
				if( is_numeric( $post_author ) ){
					$post_author_id = intval( $post_author );
				} elseif ( is_email( $post_author ) ) {
					$get_user = get_user_by( 'email', $post_author );
					if( ! empty( $get_user ) && ! empty( $get_user->data ) && ! empty( $get_user->data->ID ) ){
						$post_author_id = $get_user->data->ID;
					}
				}

				$post_data['post_author'] = $post_author_id;
			}

			if( ! empty( $post_date ) ){
				$post_data['post_date'] = date( "Y-m-d H:i:s", strtotime( $post_date ) );
			}

			if( ! empty( $post_date_gmt ) ){
				$post_data['post_date_gmt'] = date( "Y-m-d H:i:s", strtotime( $post_date_gmt ) );
			}

			if( ! empty( $post_content ) ){
				$post_data['post_content'] = $post_content;
			}

			if( ! empty( $post_content_filtered ) ){
				$post_data['post_content_filtered'] = $post_content_filtered;
			}

			if( ! empty( $post_title ) ){
				$post_data['post_title'] = $post_title;
			}

			if( ! empty( $post_excerpt ) ){
				$post_data['post_excerpt'] = $post_excerpt;
			}

			if( ! empty( $post_status ) ){
				$post_data['post_status'] = $post_status;
			}

			if( ! empty( $post_type ) ){
				$post_data['post_type'] = $post_type;
			}

			if( ! empty( $comment_status ) ){
				$post_data['comment_status'] = $comment_status;
			}

			if( ! empty( $ping_status ) ){
				$post_data['ping_status'] = $ping_status;
			}

			if( ! empty( $post_password ) ){
				$post_data['post_password'] = $post_password;
			}

			if( ! empty( $post_name ) ){
				$post_data['post_name'] = $post_name;
			}

			if( ! empty( $to_ping ) ){
				$post_data['to_ping'] = $to_ping;
			}

			if( ! empty( $pinged ) ){
				$post_data['pinged'] = $pinged;
			}

			if( ! empty( $post_modified ) ){
				$post_data['post_modified'] = date( "Y-m-d H:i:s", strtotime( $post_modified ) );
			}

			if( ! empty( $post_modified_gmt ) ){
				$post_data['post_modified_gmt'] = date( "Y-m-d H:i:s", strtotime( $post_modified_gmt ) );
			}

			if( ! empty( $post_parent ) ){
				$post_data['post_parent'] = $post_parent;
			}

			if( ! empty( $menu_order ) ){
				$post_data['menu_order'] = $menu_order;
			}

			if( ! empty( $post_mime_type ) ){
				$post_data['post_mime_type'] = $post_mime_type;
			}

			if( ! empty( $guid ) ){
				$post_data['guid'] = $guid;
			}

			if( ! empty( $import_id ) && ( ! $update || $create_post_on_update ) ){
				$post_data['import_id'] = $import_id;
			}

			//Setup post categories
			if( ! empty( $post_category ) ){
				$post_category_data = explode( ',', trim( $post_category, ',' ) );

				if( ! empty( $post_category_data ) ){
					$post_data['post_category'] = $post_category_data;
				}
			}

			//Setup meta tags
			if( ! empty( $tags_input ) ){
				$post_tags_data = explode( ',', trim( $tags_input, ',' ) );

				if( ! empty( $post_tags_data ) ){
					$post_data['tags_input'] = $post_tags_data;
				}
			}

			//Fetch the current post type on update
			$current_post_type = $post_type;
			if( empty( $current_pist_type ) ){
				if( $update && ! empty( $post_data['ID'] ) ){
					$current_post_type = get_post_type( intval( $post_data['ID'] ) );
				}
			}

			//Add the meta value accordingly to the post. Priority is set to 8 to fire it BEFORE the initial webhook function
			//Since 3.0.3 we also support meta values for attachments
			if( $current_post_type === 'attachment' ){
				add_action( 'add_attachment', array( $post_helpers, 'create_update_post_add_meta' ), 8, 1 );
				add_action( 'attachment_updated', array( $post_helpers, 'create_update_post_add_meta' ), 8, 1 );
			} else {
				add_action( 'wp_insert_post', array( $post_helpers, 'create_update_post_add_meta' ), 8, 1 );
			}

			if( $update && ! $create_post_on_update ){
				$post_id = wp_update_post( $post_data, $wp_error );
			} else {
				$post_id = wp_insert_post( $post_data, $wp_error );
			}

			if( $current_post_type === 'attachment' ){
				remove_action( 'add_attachment', array( $post_helpers, 'create_update_post_add_meta' ) );
				remove_action( 'attachment_updated', array( $post_helpers, 'create_update_post_add_meta' ) );
			} else {
				remove_action( 'wp_insert_post', array( $post_helpers, 'create_update_post_add_meta' ) );
			}

			if ( ! is_wp_error( $post_id ) && is_numeric( $post_id ) ) {

				//Maybe schedule post
				check_and_publish_future_post( $post_id );

				if( ! empty( $meta_update ) ){
					$manage_meta_data = $post_helpers->merge_repeater_meta_data( $manage_meta_data, $meta_update );
				}

				if( ! empty( $manage_meta_data ) ){
					$post_meta_data_response = $post_helpers->manage_post_meta_data( $post_id, $manage_meta_data );
				}

				$manage_acf_data_response = array();
				if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){

					if( ! empty( $acf_meta_update ) ){
						$manage_acf_data = WPWHPRO()->acf->merge_repeater_meta_data( $manage_acf_data, $acf_meta_update );
					}

					if( ! empty( $manage_acf_data ) ){
						$manage_acf_data_response = WPWHPRO()->acf->manage_acf_meta( $post_id, $manage_acf_data );
					}
					
				}

				//Setup meta tax
				if( ! empty( $tax_input ) ){
					$remove_all = false;
					$tax_append = false; //Default by WP wp_set_object_terms
					$tax_data = array(
						'delete' => array(),
						'create' => array(),
					);

					if( WPWHPRO()->helpers->is_json( $tax_input ) ){
						$post_tax_data = json_decode( $tax_input, true );
						foreach( $post_tax_data as $taxkey => $single_meta ){

							//Validate special values
							if( $taxkey == 'wpwhtype' && $single_meta == 'ironikus-append' ){
								$tax_append = true;
								continue;
							}

							if( $taxkey == 'wpwhtype' && $single_meta == 'ironikus-remove-all' ){
								$remove_all = true;
								continue;
							}

							$meta_key		   = sanitize_text_field( $taxkey );
							$meta_values		= $single_meta;

							if( ! empty( $meta_key ) ){

								if( ! is_array( $meta_values ) ){
									$meta_values = array( $meta_values );
								}

								//separate for deletion and for creation
								foreach( $meta_values as $svalue ){
									if( strpos( $svalue, '-ironikus-delete' ) !== FALSE ){

										if( ! isset( $tax_data['delete'][ $meta_key ] ) ){
											$tax_data['delete'][ $meta_key ] = array();
										}

										//Replace deletion value to correct original value
										$tax_data['delete'][ $meta_key ][] = str_replace( '-ironikus-delete', '', $svalue );
									} else {

										if( ! isset( $tax_data['create'][ $meta_key ] ) ){
											$tax_data['create'][ $meta_key ] = array();
										}

										$tax_data['create'][ $meta_key ][] = $svalue;
									}
								}

							}
						}
					} else {
						$post_tax_data = explode( ';', trim( $tax_input, ';' ) );
						foreach( $post_tax_data as $single_meta ){

							//Validate special values
							if( $single_meta == 'ironikus-append' ){
								$tax_append = true;
								continue;
							}

							if( $single_meta == 'ironikus-remove-all' ){
								$remove_all = true;
								continue;
							}

							$single_meta_data   = explode( ',', $single_meta );
							$meta_key		   = sanitize_text_field( $single_meta_data[0] );
							$meta_values		= explode( ':', $single_meta_data[1] );

							if( ! empty( $meta_key ) ){

								if( ! is_array( $meta_values ) ){
									$meta_values = array( $meta_values );
								}

								//separate for deletion and for creation
								foreach( $meta_values as $svalue ){
									if( strpos( $svalue, '-ironikus-delete' ) !== FALSE ){

										if( ! isset( $tax_data['delete'][ $meta_key ] ) ){
											$tax_data['delete'][ $meta_key ] = array();
										}

										//Replace deletion value to correct original value
										$tax_data['delete'][ $meta_key ][] = str_replace( '-ironikus-delete', '', $svalue );
									} else {

										if( ! isset( $tax_data['create'][ $meta_key ] ) ){
											$tax_data['create'][ $meta_key ] = array();
										}

										$tax_data['create'][ $meta_key ][] = $svalue;
									}
								}

							}
						}
					}

					if( $update && ! $create_post_on_update ){
						foreach( $tax_data['delete'] as $tax_key => $tax_values ){
							wp_remove_object_terms( $post_id, $tax_values, $tax_key );
						}
					}

					foreach( $tax_data['create'] as $tax_key => $tax_values ){

						if( $remove_all ){
							wp_set_object_terms( $post_id, array(), $tax_key, $tax_append );
						} else {
							wp_set_object_terms( $post_id, $tax_values, $tax_key, $tax_append );
						}

					}

					#$post_data['tax_input'] = $tax_data;
				}

				//Map external post data
				$post_data['meta_data'] = $meta_input;
				$post_data['tax_input'] = $tax_input;

				if( $update && ! $create_post_on_update ){
					$return_args['msg'] = __( "Post successfully updated", 'wp-webhooks' );
				} else {
					$return_args['msg'] = __( "Post successfully created", 'wp-webhooks' );
				}

				$return_args['success'] = true;
				$return_args['data']['post_data'] = $post_data;
				$return_args['data']['post_id'] = $post_id;
				$return_args['data']['permalink'] = get_permalink( $post_id );

				if( ! empty( $manage_meta_data ) ){
					$return_args['data']['manage_meta_data'] = $post_meta_data_response;
				}

				if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) && ! empty( $manage_acf_data_response ) ){
					$return_args['data']['manage_acf_data'] = $manage_acf_data_response;
				}

			} else {

				if( is_wp_error( $post_id ) && $wp_error ){

					$return_args['data']['post_data'] = $post_data;
					$return_args['data']['post_id'] = $post_id;
					$return_args['msg'] = __( "WP Error", 'wp-webhooks' );
				} else {
					$return_args['msg'] = __( "Error creating post.", 'wp-webhooks' );
				}
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $post_data, $post_id, $meta_input, $return_args );
			}

			return $return_args;
		}

	}

endif; // End if class_exists check.