<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_bbpress_Actions_bbp_unsubscribe_forum' ) ) :

	/**
	 * Load the bbp_unsubscribe_forum action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_bbpress_Actions_bbp_unsubscribe_forum {

	public function get_details(){

		$parameter = array(
			'user'		=> array( 
				'required' => true, 
				'label' => __( 'User', 'wp-webhooks' ), 
				'short_description' => __( 'The user ID or email of the user you want to unsubscribe from the forums.', 'wp-webhooks' ),
			),
			'forum_ids'		=> array(
				'label' => __( 'Forum ids', 'wp-webhooks' ), 
				'default_value' => 'Y-m-d H:i:s',
				'short_description' => __( 'A comma-separated list of forum IDs you want to unsubscribe the user from.', 'wp-webhooks' ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The user has been unsubscribed successfully from the forums.',
		);

		return array(
			'action'			=> 'bbp_unsubscribe_forum', //required
			'name'			   => __( 'Unsubscribe user from forum', 'wp-webhooks' ),
			'sentence'			   => __( 'unsubscribe a user from a forum', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Unsubscribe a user from one or multiple forums within "bbPress".', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'bbpress',
			'premium'	   => true
		);


		}

		public function execute( $return_data, $response_body ){
			
			$return_args = array(
				'success' => false,
				'msg' => '',
			);

			$user = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$forum_ids = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'forum_ids' );

			if( empty( $forum_ids ) ){
                $return_args['msg'] = __( "Please define the forum_ids argument as it is required.", 'action-bbp_unsubscribe_forum-error' );
				return $return_args;
            }

			$validated_forum_ids = array();
			$forum_ids_array = explode( ',', $forum_ids );
			if( is_array( $forum_ids_array ) ){
				foreach( $forum_ids_array as $single_forum_id ){
					$validated_forum_ids[] = intval( trim( $single_forum_id ) );
				}
			}

			if( empty( $validated_forum_ids ) ){
                $return_args['msg'] = __( "We could not validate the given forum IDs.", 'action-bbp_unsubscribe_forum-error' );
				return $return_args;
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
                $return_args['msg'] = __( "We could not find a user for your given user id.", 'action-bbp_unsubscribe_forum-error' );
				return $return_args;
            }

			$errors = array();

			foreach( $validated_forum_ids as $forum_id ){

				//skip if the user is already unsubscribed
				if ( ! bbp_is_user_subscribed( $user_id, $forum_id, 'post' ) ) {
					continue;
				}

				$check = bbp_remove_user_subscription( $user_id, $forum_id );
				if( empty( $check ) ){
					$errors[] = sprintf( __( "Unsubscribing the user from the blog %d failed.", 'action-bbp_unsubscribe_forum-error' ), $forum_id );
				}
			}

			if( empty( $errors ) ){
				$return_args['success'] = true;
				$return_args['msg'] = __( "The user has been unsubscribed successfully from the forums.", 'action-bbp_unsubscribe_forum-success' );
			} else {
				$return_args['msg'] = __( "One or more issues occured while unsubscribing the user from the forums.", 'action-bbp_unsubscribe_forum-success' );
				$return_args['errors'] = $errors;
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.