<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_mailpoet_Actions_mp_remove_subscriber_from_lists' ) ) :
	/**
	 * Load the mp_remove_subscriber_from_lists action
	 *
	 * @since 6.0.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_mailpoet_Actions_mp_remove_subscriber_from_lists {

		public function get_details() {
			$parameter         = array(
				'email' => array(
					'required'          => true,
					'label'             => __( 'Subscriber email', 'wp-webhooks' ),
					'short_description' => __( '(String) The email address of the subscriber.', 'wp-webhooks' ),
				),
				'lists' => array(
					'required'          => true,
					'label'             => __( 'Lists', 'wp-webhooks' ),
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => array(),
					'query'			=> array(
						'filter'	=> 'helpers',
						'args'		=> array(
							'integration' => 'mailpoet',
							'helper' => 'mp_helpers',
							'function' => 'get_lists',
						)
					),
					'short_description' => __( '(String) The lists you want to remove the subscriber from. Comma-separate them in case you want to add multiple ones.', 'wp-webhooks' ),
				),
			);
			$returns           = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action status.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further information about the request.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg' => 'The subscriber has been removed from the lists.',
				'data' => 
			   array(
				  'id' => '130',
				  'wp_user_id' => NULL,
				  'is_woocommerce_user' => false,
				  'subscriptions' => 
				 array (
				   0 => 
				   array(
					  'id' => 130,
					  'subscriber_id' => '130',
					  'created_at' => '2022-10-14 06:08:17',
					  'segment_id' => '3',
					  'status' => 'unsubscribed',
					  'updated_at' => '2022-10-14 06:10:32',
				   ),
				 ),
				  'unsubscribes' => 
				 array (
				 ),
				  'status' => 'unconfirmed',
				  'last_name' => 'Doe',
				  'first_name' => 'Jon',
				  'email' => 'demouser@demo.test',
				  'created_at' => '2022-10-14 06:07:42',
				  'updated_at' => '2022-10-14 06:07:42',
				  'deleted_at' => NULL,
				  'subscribed_ip' => '127.0.0.1',
				  'confirmed_ip' => NULL,
				  'confirmed_at' => NULL,
				  'last_subscribed_at' => NULL,
				  'unconfirmed_data' => NULL,
				  'source' => 'api',
				  'count_confirmations' => 0,
				  'unsubscribe_token' => '70xzuusfu98okkg',
				  'link_token' => 'ddaef23bad57b33ea259c99f656bb163',
				  'tags' => 
				 array (
				 ),
			   ),
			);

			return array(
				'action'            => 'mp_remove_subscriber_from_lists', // required
				'name'              => __( 'Remove subscriber from lists', 'wp-webhooks' ),
				'sentence'          => __( 'remove a subscriber from one or multiple lists', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Remove a subscriber from one or multiple lists withing MailPoet.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'mailpoet',
				'premium'           => true,
			);

		}

		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'     => '',
				'data'    => array(),
			);

			if ( ! class_exists( '\MailPoet\API\API' ) ) {
				return $return_args['msg'] = 'The class \MailPoet\API\API does not exist';
			}

			$subscriber          = array();
			$subscriber['email'] = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'email' );
			$subscriber['lists'] = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'lists' );

			if( empty( $subscriber['email'] ) ){
				$return_args['msg']     = __( 'Please set the email argument.', 'wp-webhooks' );
				return $return_args;
			}

			if( empty( $subscriber['lists'] ) ){
				$return_args['msg']     = __( 'Please set the lists argument.', 'wp-webhooks' );
				return $return_args;
			}

			if( WPWHPRO()->helpers->is_json( $subscriber['lists'] ) ){
				$lists = json_decode( $subscriber['lists'], true );
			} else {
				$lists_array = explode( ',', $subscriber['lists'] );
				$lists       = array();
				if ( is_array( $lists_array ) ) {
					foreach ( $lists_array as $list ) {
						$lists[] = intval( trim( $list ) );
					}
				}
			}	

			if( empty( $lists ) ){
				$return_args['msg'] = __( 'We could not validate your given lists.', 'wp-webhooks' );
				return $return_args;
			}

			$lists = (array) $lists;

			$mailpoet = \MailPoet\API\API::MP( 'v1' );
			try {
				$subscriber_exists = \MailPoet\Models\Subscriber::findOne( $subscriber['email'] );
				if ( $subscriber_exists ) {
					$return_args['success'] = true;
					$return_args['msg']     = __( 'The subscriber has been removed from the lists.', 'wp-webhooks' );
					$return_args['data']    = $mailpoet->unsubscribeFromLists( $subscriber['email'], $lists );
				}
			} catch ( \MailPoet\API\MP\v1\APIException $e ) {
				$return_args['msg'] = $e->getMessage();
			}

			return $return_args;

		}
	}
endif;
