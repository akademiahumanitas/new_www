<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_groundhogg_Actions_ghogg_user_tags_remove' ) ) :

	/**
	 * Load the ghogg_user_tags_remove action
	 *
	 * @since 4.3.5
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_groundhogg_Actions_ghogg_user_tags_remove {

	public function get_details(){

			$parameter = array(
				'contact_value'		=> array( 'required' => true, 'short_description' => __( 'Set this argument to either the contact id or the contact/user email. You can also set it to the user id if you set the value_type argument to user_id.', 'wp-webhooks' ) ),
				'tags'	=> array( 'required' => true, 'short_description' => __( 'Add the tags you want to remove from the contact. This argument accepts a comma-separated string, as well as a JSON construct.', 'wp-webhooks' ) ),
				'value_type'		=> array( 'short_description' => __( 'Set this argument to user_id to use the id of the user within the contact value argument. Default: default.', 'wp-webhooks' ) ),
				'do_action'	  => array( 'short_description' => __( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', 'wp-webhooks' ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further data about the fired actions.', 'wp-webhooks' ) ),
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
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>ghogg_user_tags_remove</strong> action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $return_args, $contact_value, $value_type ){
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
		<?php echo __( "The value used to identify the contact.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$value_type</strong> (mixed)<br>
		<?php echo __( "Either string or bool. String if set to user_id.", 'wp-webhooks' ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns_code = array (
			'success' => true,
			'msg' => 'Tags have been removed from the respective contact.',
			'data' => 
			array (
			  'user_id' => 1,
			  'tags' => 
			  array (
				0 => 12,
				1 => 4,
			  ),
			  'contact_id' => 1,
			),
		  );

		return array(
			'action'			=> 'ghogg_user_tags_remove', //required
			'name'			   => __( 'Remove user tags', 'wp-webhooks' ),
			'sentence'			   => __( 'remove one or multiple tags from a user', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Remove one or multiple tags from a user within Groundhogg.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'groundhogg',
			'premium'	   	=> true,
		);


		}

		public function execute( $return_data, $response_body ){

			$ghogg_helpers = WPWHPRO()->integrations->get_helper( 'groundhogg', 'ghogg_helpers' );
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'user_id' => 0,
					'tags' => '',
				)
			);

			$contact_value		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'contact_value' );
			$value_type		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'value_type' );
			$tags		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tags' );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $contact_value ) ){
				$return_args['msg'] = __( "Please set the contact_value argument.", 'action-ghogg_user_tags_remove-error' );
				return $return_args;
			}

			if( empty( $tags ) ){
				$return_args['msg'] = __( "Please set the tags argument.", 'action-ghogg_user_tags_remove-error' );
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

			foreach( $validated_tags as $tk => $tv ){
				$validated_tags[ $tk ] = intval( $tv );
			}

            if( $value_type === 'user_id' ){
				$contact = $ghogg_helpers->get_contact( $contact_value, true );
			} else {
				$contact = $ghogg_helpers->get_contact( $contact_value );
			}

			if( ! $contact->exists() ) {
				$return_args['msg'] = __( "The contact you try to update does not exist.", 'action-ghogg_user_tags_remove-error' );
				return $return_args;
			}

			$contact->remove_tag( $validated_tags );
			
			$return_args['success'] = true;
			$return_args['msg'] = __( "Tags have been removed from the respective contact.", 'action-ghogg_user_tags_remove-success' );
			$return_args['data']['contact_id'] = $contact->get_id();
			$return_args['data']['user_id'] = $contact->get_user_id();
			$return_args['data']['tags'] = $validated_tags;

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $contact_value, $value_type );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.