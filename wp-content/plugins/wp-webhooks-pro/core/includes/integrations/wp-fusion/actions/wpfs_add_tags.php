<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_fusion_Actions_wpfs_add_tags' ) ) :

	/**
	 * Load the wpfs_add_tags action
	 *
	 * @since 4.3.4
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_fusion_Actions_wpfs_add_tags {

	public function get_details(){

			$parameter = array(
				'user'		=> array( 'required' => true, 'short_description' => __( 'Set this argument to the id of the user. In case you do not have the user id, you can also assign the user via a given email.', 'wp-webhooks' ) ),
				'tags'	=> array( 'required' => true, 'short_description' => __( 'Add the tags you want to add to the user. This argument accepts a comma-separated string, as well as a JSON construct.', 'wp-webhooks' ) ),
				'do_action'	  => array( 'short_description' => __( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', 'wp-webhooks' ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further data about the fired actions.', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			ob_start();
		?>
<?php echo __( "In case you want to add multiple tags to the user, you can either comma-separate them like <code>2,3,12,44</code>, or you can add them via a JSON construct:", 'wp-webhooks' ); ?>
<pre>{
  23,
  3,
  44
}</pre>
		<?php
		$parameter['tags']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>wpfs_add_tags</strong> action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $return_args, $user_id, $validated_tags ){
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
		<strong>$user_id</strong> (integer)<br>
		<?php echo __( "The id of the user.", 'wp-webhooks' ); ?>
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
			'msg' => 'Tags have been added to the given user.',
			'data' => 
			array (
			  'user_id' => 155,
			  'tags' => 
			  array (
				0 => 3,
				1 => 1,
			  ),
			),
		);

		return array(
			'action'			=> 'wpfs_add_tags', //required
			'name'			   => __( 'Add tags', 'wp-webhooks' ),
			'sentence'			   => __( 'add one or multiple tags', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Add one or multiple tags to a user within WP Fusion.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'wp-fusion',
			'premium'	   	=> true,
		);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'user_id' => 0,
					'tags' => '',
				)
			);

			$user		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$tags		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tags' );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $user ) ){
				$return_args['msg'] = __( "Please set the user argument to either the user id or user email.", 'action-wpfs_add_tags-error' );
				return $return_args;
			}

			if( empty( $tags ) ){
				$return_args['msg'] = __( "Please set the tags argument.", 'action-wpfs_add_tags-error' );
				return $return_args;
			}

			$validated_tags = array();
			if( WPWHPRO()->helpers->is_json( $tags ) ){
                $validated_tags = json_decode( $tags, true );
            } else {
				$validated_tags = explode( ',', $tags );
			}

			//Support for single tags
            if( ! is_array( $validated_tags ) && ! empty( $validated_tags ) ){
                $validated_tags = array( $validated_tags );
            }

			foreach( $validated_tags as $tk => $tv ){

				$tv = sanitize_text_field( $tv );
				$tag_id = wp_fusion()->user->get_tag_id( $tv );
				$validated_tags[ $tag_id ] = $tv;

			}

            $user_id = 0;

            if( ! empty( $user ) && is_numeric( $user ) ){
                $user_id = intval( $user );
            } elseif( ! empty( $user ) && is_email( $user ) ) {
                $user_data = get_user_by( 'email', $user );
                if( ! empty( $user_data ) && isset( $user_data->ID ) && ! empty( $user_data->ID ) ){
                    $user_id = $user_data->ID;
                }
            }

            if( empty( $user_id ) ){
                $return_args['msg'] = __( "We could not find a user for your given user id.", 'action-wpfs_add_tags-error' );
				return $return_args;
            }

			//Make sure to register the user first if not already done
			$contact_id = wp_fusion()->user->get_contact_id( $user_id, true );
			if( ! $contact_id ){
				wp_fusion()->user->user_register( $user_id );
			}

            $user_tags = wp_fusion()->user->get_tags( $user_id );

			foreach( $validated_tags as $tag ){
				if( ! in_array( $tag, $user_tags, true ) ){
					wp_fusion()->user->apply_tags( array( $tag ), $user_id );
				}
			}
			
			$return_args['success'] = true;
			$return_args['msg'] = __( "Tags have been added to the given user.", 'action-wpfs_add_tags-success' );
			$return_args['data']['user_id'] = $user_id;
			$return_args['data']['tags'] = $validated_tags;

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $user_id, $validated_tags );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.