<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_mailpoet_Actions_mp_add_subscriber_to_lists' ) ) :
	/**
	 * Load the mp_add_subscriber_to_lists action
	 *
	 * @since 6.0.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_mailpoet_Actions_mp_add_subscriber_to_lists {
		
		public function get_details() {
			$parameter         = array(
				'email'        => array(
					'required'          => true,
					'label'             => __( 'Email', 'wp-webhooks' ),
					'short_description' => __( '(String) The email address.', 'wp-webhooks' ),
				),
				'lists'        => array(
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
					'short_description' => __( '(String) The lists where subscriber will be attached. Comma-separate them if you want to add multiple ones. The MailPoet will send messages if any active for your lists.', 'wp-webhooks' ),
				),
				'first_name'   => array(
					'label'             => __( 'First name', 'wp-webhooks' ),
					'short_description' => __( '(String) The first name.', 'wp-webhooks' ),
				),
				'last_name'    => array(
					'label'             => __( 'Last name', 'wp-webhooks' ),
					'short_description' => __( '(String) The last name.', 'wp-webhooks' ),
				),
				'status'       => array(
					'label'             => __( 'Status', 'wp-webhooks' ),
					'type'              => 'select',
					'default_value'     => 'subscribed',
					'choices'           => array(
						'subscribed'   => array( 'label' => __( 'Subscribed', 'wp-webhooks' ) ),
						'unconfirmed'  => array( 'label' => __( 'Unconfirmed', 'wp-webhooks' ) ),
						'insubscribed' => array( 'label' => __( 'Unsubscribed', 'wp-webhooks' ) ),
						'inactive'     => array( 'label' => __( 'Inactive', 'wp-webhooks' ) ),
						'bounced'      => array( 'label' => __( 'Bounced', 'wp-webhooks' ) ),
					),
					'short_description' => __( '(String) The status of the subscriber.', 'wp-webhooks' ),
				),
				'confirmation_email' => array(
					'label'             => __( 'Confirmation email ', 'wp-webhooks' ),
					'type' => 'select',
					'choices' => array(
						'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
					),
					'multiple' => false,
					'default_value' => 'no',
					'short_description' => __( '(String) Set this to "yes" to send a confirmation email.', 'wp-webhooks' ),
				),
			);
			$returns           = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action status.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further information about the request.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'The new subscriber has been added to list.',
				'data'    =>
				array(
					'id'                  => '5',
					'wp_user_id'          => null,
					'is_woocommerce_user' => false,
					'subscriptions'       =>
					array(
						0 =>
						array(
							'id'            => 5,
							'subscriber_id' => '5',
							'created_at'    => '2022-10-11 06:38:16',
							'segment_id'    => '3',
							'status'        => 'subscribed',
							'updated_at'    => '2022-10-11 06:38:16',
						),
					),
					'unsubscribes'        =>
					array(),
					'status'              => 'unconfirmed',
					'last_name'           => 'DemoLast',
					'first_name'          => 'Demo',
					'email'               => 'demo@gmail.com',
					'created_at'          => '2022-10-11 06:38:16',
					'updated_at'          => '2022-10-11 06:38:16',
					'deleted_at'          => null,
					'subscribed_ip'       => '::1',
					'confirmed_ip'        => null,
					'confirmed_at'        => null,
					'last_subscribed_at'  => null,
					'unconfirmed_data'    => null,
					'source'              => 'api',
					'count_confirmations' => 0,
					'unsubscribe_token'   => '1bj6dnxsmpmsg4c',
					'link_token'          => '0d15056a7de6366942bb59de930afee5',
					'tags'                =>
					array(),
				),
			);

			return array(
				'action'            => 'mp_add_subscriber_to_lists', // required
				'name'              => __( 'Add subscriber to lists', 'wp-webhooks' ),
				'sentence'          => __( 'add a subscriber to one or multiple lists', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Adding a subscriber to one or multiple lists withing MailPoet.', 'wp-webhooks' ),
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

			$subscriber               = array();
			$subscriber['email']      = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'email' );
			$subscriber['first_name'] = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'first_name' );
			$subscriber['last_name']  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'last_name' );
			$subscriber['status']     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'status' );
			$subscriber['lists']      = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'lists' );
			$confirmation_email       = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'confirmation_email' ) === 'yes' ) ? true : false;

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
				if ( ! $subscriber_exists ) {
					$return_args['success'] = true;
					$return_args['msg']     = __( 'The new subscriber has been added to list.', 'wp-webhooks' );
					$return_args['data']    = $mailpoet->addSubscriber( $subscriber, $lists, array( 'send_confirmation_email' => $confirmation_email ) );
				} else {
					$return_args['success'] = true;
					$return_args['msg']     = __( 'The subsriber has been added to list.', 'wp-webhooks' );
					$return_args['data']    = $mailpoet->subscribeToLists( $subscriber_exists->id, $lists, array( 'send_confirmation_email' => $confirmation_email ) );
				}
			} catch ( \MailPoet\API\MP\v1\APIException $e ) {
				$return_args['msg'] = $e->getMessage();
			}

			return $return_args;

		}
	}
endif;
