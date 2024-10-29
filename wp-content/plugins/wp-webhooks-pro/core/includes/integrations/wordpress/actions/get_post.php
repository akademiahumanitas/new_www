<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_get_post' ) ) :

	/**
	 * Load the get_post action
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_get_post {

		/*
	 * The core logic to get a single post
	 */
	public function get_details(){


		$parameter = array(
			'post_value'	   => array( 'required' => true, 'short_description' => __( 'The post id (default) of the post you want to fetch. See the Details for further information.', 'wp-webhooks' ) ),
			'value_type'	   => array( 'short_description' => __( 'Set this to either post_id or attachment_url, depending on your used post_value.', 'wp-webhooks' ) ),
			'return_only'	=> array( 'short_description' => __( 'Select the values you want to return. Default is all.', 'wp-webhooks' ) ),
			'thumbnail_size'	=> array( 'short_description' => __( 'Pass the size of the thumbnail of your given post id. Default is full.', 'wp-webhooks' ) ),
			'post_taxonomies'	=> array( 'short_description' => __( 'Single value or comma separated list of the taxonomies you want to return. Default: post_tag.', 'wp-webhooks' ) ),
			'do_action'	 => array( 'short_description' => __( 'Advanced: Register a custom action after our plugin fires this webhook.', 'wp-webhooks' ) )
		);

		ob_start();
		?>
<?php echo __( "This argument accepts various values, depending on what you set up within the return_only argument. By default, you can enter the post ID. In case you want to search an attachment, you can also set the attachment URL as a value - please note that in this case you have to adjust the <strong>value_type</strong> argument to <strong>attachment_url</strong>.", 'wp-webhooks' ); ?>
		<?php
		$parameter['post_value']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo __( "You can also manipulate the result of the post data gathering using the <strong>return_only</strong> parameter. This allows you to output only certain elements of the request. Here is an example:", 'wp-webhooks' ); ?>
<pre>post,post_thumbnail,post_terms,post_meta,post_permalink</pre>
<?php echo __( "Here's a list of all available values for the <strong>return_only</strong> argument. In case you want to use multiple ones, simply separate them with a comma.", 'wp-webhooks' ); ?>
<ol>
	<li><strong>all</strong></li>
	<li><strong>post</strong></li>
	<li><strong>post_thumbnail</strong></li>
	<li><strong>post_terms</strong></li>
	<li><strong>post_meta</strong></li>
	<li><strong>post_permalink</strong></li>
	<?php if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
		echo '<li><strong>acf_data</strong> (' . __( "Integrates Advanced Custom Fields", 'wp-webhooks' ) . ')</li>';
	} ?>
</ol>
		<?php
		$parameter['return_only']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo __( "This argument allows you to return one or multiple thumbnail_sizes for the given post thumbnail. By default, we output only the full image. Here is an example: ", 'wp-webhooks' ); ?>
<pre>full,medium</pre>
<?php echo __( "Here's a list of all available sizes for the <strong>thumbnail_size</strong> argument (The availalbe sizes may vary since you can also use third-party size definitions). In case you want to use multiple ones, simply separate them with a comma.", 'wp-webhooks' ); ?>
<ol>
	<li><strong>thumbnail</strong> <?php echo __( "(150px square)", 'wp-webhooks' ); ?></li>
	<li><strong>medium</strong> <?php echo __( "(maximum 300px width and height)", 'wp-webhooks' ); ?></li>
	<li><strong>large</strong> <?php echo __( "(maximum 1024px width and height)", 'wp-webhooks' ); ?></li>
	<li><strong>full</strong> <?php echo __( "(full/original image size you uploaded)", 'wp-webhooks' ); ?></li>
</ol>
		<?php
		$parameter['thumbnail_size']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo __( "You can also customize the output of the returned taxonomies using the <strong>post_taxonomies</strong> argument. Default is post_tag. This argument accepts a string of a single taxonomy slug or a comma separated list of multiple taxonomy slugs. Please see the example down below:", 'wp-webhooks' ); ?>
<pre>post_tag,custom_taxonomy_1,custom_taxonomy_2</pre>
		<?php
		$parameter['post_taxonomies']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>get_post</strong> action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $return_args, $post_id, $thumbnail_size, $post_taxonomies ){
	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo __( "All the values that are sent back as a response the the initial webhook action caller.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$post_id</strong> (integer)<br>
		<?php echo __( "The id of the currently fetched post.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$thumbnail_size</strong> (string)<br>
		<?php echo __( "The string formatted thumbnail sizes sent by the caller within the thumbnail_size argument.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$post_taxonomies</strong> (string)<br>
		<?php echo __( "The string formatted taxonomy slugs sent by the caller within the post_taxonomies argument.", 'wp-webhooks' ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns = array(
			'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'data'		=> array( 'short_description' => __( 'The data construct of the single post. This depends on the parameters you send.', 'wp-webhooks' ) ),
			'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
		);

			$returns_code = array (
				'success' => true,
				'msg' => 'Post was successfully returned.',
				'data' => 
				array (
				  'post' => 
				  array (
					'ID' => 7920,
					'post_author' => '1',
					'post_date' => '2021-12-31 11:11:11',
					'post_date_gmt' => '2021-12-31 11:11:11',
					'post_content' => 'The content of the post, including all HTML',
					'post_title' => 'A demo title',
					'post_excerpt' => 'The short description of the post',
					'post_status' => 'future',
					'comment_status' => 'open',
					'ping_status' => 'open',
					'post_password' => '',
					'post_name' => 'somedemoname',
					'to_ping' => '',
					'pinged' => '',
					'post_modified' => '2021-12-31 11:11:11',
					'post_modified_gmt' => '2021-12-31 11:11:11',
					'post_content_filtered' => '',
					'post_parent' => 0,
					'guid' => 'https://yourdomain.test/?p=7920',
					'menu_order' => 0,
					'post_type' => 'post',
					'post_mime_type' => '',
					'comment_count' => '0',
					'filter' => 'raw',
				  ),
				  'post_thumbnail' => false,
				  'post_terms' => 
				  array (
				  ),
				  'post_meta' => 
				  array (
					'first_custom_key' => 
					array (
					  0 => 'Some custom value',
					),
					'second_custom_key' => 
					array (
					  0 => 'The new value',
					),
					'wpwhpro_create_post_temp_status_jobs' => 
					array (
					  0 => 'future',
					),
				  ),
				  'post_permalink' => 'https://yourdomain.test/?p=7920',
				  'acf_data' => false,
				),
			);

			$description = array(
				'tipps' => array(
					__( 'This webhook action uses the default WordPress function get_post():', 'wp-webhooks' ) . ' <a title="wordpress.org" target="_blank" href="https://developer.wordpress.org/reference/functions/get_post/">https://developer.wordpress.org/reference/functions/get_post/</a>',
				),
			);

			return array(
				'action'			=> 'get_post',
				'name'			  => __( 'Get post', 'wp-webhooks' ),
				'sentence'			  => __( 'get a post', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Returns the post/custom post from your given data.', 'wp-webhooks' ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> false,
			);

		}

		/**
		 * Get a single post using get_post
		 */
		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'	 => '',
				'data' => array()
			);
			$post_id = 0;

			$fetched_post_id	 	 = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_id' ) );
			if( ! empty( $fetched_post_id ) ){
				$post_value = $fetched_post_id;
			} else {
				$post_value	 	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_value' );
			}
			
			$value_type	 	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'value_type' );
			$return_only	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'return_only' );
			$thumbnail_size	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'thumbnail_size' );
			$post_taxonomies	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_taxonomies' );
			$do_action   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $value_type ) ){
				$value_type = 'post_id';
			}

			if( $value_type === 'post_id' && is_numeric( $post_value ) ){
				$post_id = intval( $post_value );
			} elseif( $value_type === 'attachment_url' && is_string( $post_value ) ){
				$attachment_url_url = strtok( $post_value, '?' );
				$post_id = attachment_url_to_postid( $attachment_url_url );
			}

			if( empty( $post_id ) ){
				$return_args['msg'] = __( "We did not find any post for your given post_value.", 'action-get_post-failure' );

				return $return_args;
			}

			$return = array( 'all' );
			if( ! empty( $return_only ) ){
				$return = array_map( 'trim', explode( ',', $return_only ) );
			}

			$thumbnail_sizes = 'full';
			if( ! empty( $thumbnail_size ) ){
				$thumbnail_sizes = array_map( 'trim', explode( ',', $thumbnail_size ) );
			}

			$post_taxonomies_out = 'post_tag';
			if( ! empty( $post_taxonomies ) ){
				$post_taxonomies_out = array_map( 'trim', explode( ',', $post_taxonomies ) );
			}

			if( ! empty( $post_id ) ){
				$post = get_post( $post_id );
				$post_thumbnail = get_the_post_thumbnail_url( $post_id, $thumbnail_sizes );
				$post_terms = wp_get_post_terms( $post_id, $post_taxonomies_out );
				$post_meta = get_post_meta( $post_id );
				$permalink = get_permalink( $post_id );

				$acf_data = '';
				if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
					$acf_data = get_fields( $post_id );
				}

				if ( is_wp_error( $post ) ) {
					$return_args['msg'] = __( $post->get_error_message(), 'wp-webhooks' );
				} else {

					foreach( $return as $single_return ){

						switch( $single_return ){
							case 'all':
								$return_args['data'][ 'post' ] = $post;
								$return_args['data'][ 'post_thumbnail' ] = $post_thumbnail;
								$return_args['data'][ 'post_terms' ] = $post_terms;
								$return_args['data'][ 'post_meta' ] = $post_meta;
								$return_args['data'][ 'post_permalink' ] = $permalink;

								if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
									$return_args['data'][ 'acf_data' ] = $acf_data;
								}

								break;
							case 'post':
								$return_args['data'][ $single_return ] = $post;
								break;
							case 'post_thumbnail':
								$return_args['data'][ $single_return ] = $post_thumbnail;
								break;
							case 'post_terms':
								$return_args['data'][ $single_return ] = $post_terms;
								break;
							case 'post_meta':
								$return_args['data'][ $single_return ] = $post_meta;
								break;
							case 'post_permalink':
								$return_args['data'][ $single_return ] = $permalink;
								break;
							case 'acf_data':
								if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
									$return_args['data'][ $single_return ] = $acf_data;
								}
								break;
						}
					}

					$return_args['msg'] = __( "Post was successfully returned.", 'wp-webhooks' );
					$return_args['success'] = true;

				}

			} else {
				$return_args['msg'] = __( "There is an issue with your defined arguments. Please check them first.", 'wp-webhooks' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $post_id, $thumbnail_size, $post_taxonomies );
			}

			return $return_args;
		}

	}

endif; // End if class_exists check.