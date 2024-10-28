<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_affiliatewp_Actions_affwp_add_referral' ) ) :

	/**
	 * Load the affwp_add_referral action
	 *
	 * @since 4.2.3
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_affiliatewp_Actions_affwp_add_referral {

		function __construct(){
			$this->page_title   = WPWHPRO()->settings->get_page_title();
		}

		public function get_details(){

			$third_party_integrations = array();
			$validated_types = array();
			$validated_statuses = array();

			if( function_exists( 'affiliate_wp' ) ){
				$third_party_integrations = affiliate_wp()->integrations->get_integrations();
			}

			if( function_exists( 'affiliate_wp' ) ){
				foreach ( affiliate_wp()->referrals->types_registry->get_types() as $type_slug => $type ) {
					$validated_types[ $type_slug ] = ( isset( $type['label'] ) && ! empty( $type['label'] ) ) ? sanitize_text_field( $type['label'] ) : $type_slug;
				}
			}

			if( function_exists( 'affwp_get_referral_statuses' ) ){
				$validated_statuses = affwp_get_referral_statuses();
			}

			$parameter = array(
				'user_id' => array( 'required' => true, 'short_description' => __( 'The id or email of the related user. (Optional in case affiliate_id is set).', 'wp-webhooks' ) ),
				'affiliate_id' => array( 'required' => true, 'short_description' => __( 'The id of the related affiliate. (Optional in case user_id is set).', 'wp-webhooks' ) ),
				'amount' => array( 'short_description' => __( 'The amount that is paid to the affiliate.', 'wp-webhooks' ) ),
				'description' => array( 'short_description' => __( 'A description for this referral.', 'wp-webhooks' ) ),
				'reference' => array( 'short_description' => __( 'A reference for this referral. Usually this would be the transaction ID of the associated purchase.', 'wp-webhooks' ) ),
				'parent_id' => array( 'short_description' => __( 'An id of a different referral you want to associate as a parent.', 'wp-webhooks' ) ),
				'currency' => array( 'short_description' => __( 'An custom currency code such as EUR. Please note that the currency will only have effect it is is selected within the settings of AffiliateWP.', 'wp-webhooks' ) ),
				'campaign' => array( 'short_description' => __( 'Set a referral campaign. This can be a referral you created for a specific project. E.g. Summer Promotion', 'wp-webhooks' ) ),
				'context' => array( 'short_description' => __( 'The context usually is the slug of the payment provider. E.g. fastspring or paypal. You can also use the third-party integration.', 'wp-webhooks' ) ),
				'custom' => array( 'short_description' => __( 'Add any kind of data to your referral.', 'wp-webhooks' ) ),
				'date' => array( 'short_description' => __( 'The date and time of creation of the referral. In case nothing is set, the current time is used.', 'wp-webhooks' ) ),
				'type' => array( 'short_description' => __( 'The referral type. E.g.: sale', 'wp-webhooks' ) ),
				'products' => array( 'short_description' => __( 'In case you use a third-party integration, you can also relate specific products to your referral.', 'wp-webhooks' ) ),
				'status' => array( 'short_description' => __( 'The status of your current referral. E.g.: unpaid', 'wp-webhooks' ) ),
				'do_action'	 => array( 'short_description' => __( 'Advanced: Register a custom action after this webhook was fired.', 'wp-webhooks' ) ),
			);

			ob_start();
			?>
<?php echo __( "The reference is usually the transaction id of the payment provider. In case you use a third-party plugin, you can also add the order id here. In case of Easy Digital Downloads, this might be the payment id. E.g.: 1344", 'wp-webhooks' ); ?>
			<?php
			$parameter['reference']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "As a contect, you can also use the third-party integration slug. Down below you will find a list of all integrated third-party integrations for AffiliateWP. If you want to, for example, create the referral for the Easy Digital Downloads integration, set the context to <strong>edd</strong>.", 'wp-webhooks' ); ?>
<ol>
	<?php foreach( $third_party_integrations as $slug => $name ) : ?>
	<li>
		<?php echo sanitize_text_field( $name ); ?>: <strong><?php echo sanitize_text_field( $slug ); ?></strong>
	</li>
	<?php endforeach; ?>
</ol>
			<?php
			$parameter['context']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "The type determines what kind of affiliation this referral is. In case it was a sale, you can use <strong>sale</strong> - for an opt in, you can use <strong>opt-in</strong>. Down below you will find a full list of all types.", 'wp-webhooks' ); ?>
<ol>
	<?php foreach( $validated_types as $slug => $name ) : ?>
	<li>
		<?php echo sanitize_text_field( $name ); ?>: <strong><?php echo sanitize_text_field( $slug ); ?></strong>
	</li>
	<?php endforeach; ?>
</ol>
			<?php
			$parameter['type']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "In case of a referral from Woocommerce or Easy Digital Downloads (or any other third-party integration), it is possible to relate products to your given order. This argument accepts a JSON formatted value containing one or multiple products. Down below you will find an example.", 'wp-webhooks' ); ?>
<pre>
[
   {
      "name":"Demo Article",
      "id":285,
      "price":39,
      "referral_amount":"3.9"
   }
]
</pre>
<?php echo __( "To give you an explanation about each value, please refer to the lsit down below. Every data within the curly brackets {} refers to one product.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>name</strong>: <?php echo __( "The name refers to the name of the added product.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>id</strong>: <?php echo __( "The id refers to the id of the added product within the third-party integration.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>price</strong>: <?php echo __( "The price refers to the price of the added product within the third-party integrations currency.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>referral_amount</strong>: <?php echo __( "This is the amount your affiliate gets paid for that specific product referral.", 'wp-webhooks' ); ?>
	</li>
</ol>
			<?php
			$parameter['products']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "You can also customize the status of the given referral. If you want to mark a referral as paid, simple set the status to <strong>paid</strong>. A list of all possible statuses is down below.", 'wp-webhooks' ); ?>
<ol>
	<?php foreach( $validated_statuses as $slug => $name ) : ?>
	<li>
		<?php echo sanitize_text_field( $name ); ?>: <strong><?php echo sanitize_text_field( $slug ); ?></strong>
	</li>
	<?php endforeach; ?>
</ol>
			<?php
			$parameter['status']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $referral_id, $args, $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$referral_id</strong> (integer)<br>
		<?php echo __( "The id of the newly created referral.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$args</strong> (array)<br>
		<?php echo __( "The data used to create the referral.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$args</strong> (array)<br>
		<?php echo __( "An array containing the information we will send back as the response to the initial webhook caller.", 'wp-webhooks' ); ?>
	</li>
</ol>
			<?php
			$parameter['do_action']['description'] = ob_get_clean();

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further data in relation the current webhook action.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'Referral was successfully created.',
				'data' => 
				array (
				  'referral_id' => 15,
				  'referral' => 
				  array (
					'referral_id' => 15,
					'affiliate_id' => 8,
					'visit_id' => 2,
					'rest_id' => '',
					'customer_id' => '0',
					'parent_id' => 0,
					'description' => 'This is a demo description',
					'status' => 'paid',
					'amount' => '18.00',
					'currency' => 'eur',
					'custom' => 'Some custom information',
					'context' => 'edd',
					'campaign' => 'Demo Campaign',
					'reference' => '1344',
					'products' => 
					array (
					  0 => 
					  array (
						'name' => 'Demo Article',
						'id' => 285,
						'price' => 39,
						'referral_amount' => '3.9',
					  ),
					),
					'date' => '2021-05-12 14:10:23',
					'type' => 'sale',
					'payout_id' => '0',
				  ),
				),
			);

			$description = array(
				'tipps' => array(
					__( "This endpoint also supports all of the, by AffiliateWP integrated, third-party integrations. For further details, please take a look at the <strong>context</strong> argument.", 'wp-webhooks' ),
				)
			);

			return array(
				'action'			=> 'affwp_add_referral',
				'name'			  => __( 'Add referral', 'wp-webhooks' ),
				'sentence'			  => __( 'add a referral', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Create a referral within AffiliateWP via a webhook call.', 'wp-webhooks' ),
				'description'	   => $description,
				'integration'	   => 'affiliatewp',
				'premium'		   => true,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => __( "No referral was created.", 'action-affwp_add_referral-error' ),
				'data' => array(),
			);

			$user_id = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_id' );
			$affiliate_id = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'affiliate_id' );
			$amount = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'amount' );
			$description = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'description' );
			$reference = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'reference' );
			$parent_id = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'parent_id' );
			$currency = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'currency' );
			$campaign = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'campaign' );
			$context = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'context' );
			$custom = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'custom' );
			$date = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'date' );
			$type = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'type' );
			$products = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'products' );
			$status = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'status' );
			$visit_id = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'visit_id' );
			$do_action = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( ! empty( $user_id ) ){
				
				if( is_numeric( $user_id ) ){
					$user_id = intval( $user_id );
				} elseif( is_email( $user_id ) ){
					$user = get_user_by( 'email', $user_id );
					if( ! empty( $user ) ){
						if( ! empty( $user->ID ) ){
							$user_id = $user->ID;
						}
					}
				}

			}

			if( empty( $affiliate_id ) && ! empty( $user_id ) ){
				$affiliate_id = affwp_get_affiliate_id( $user_id );
			} elseif( is_numeric( $affiliate_id ) ) {
				$affiliate_id = intval( $affiliate_id );
				$affiliate = affwp_get_affiliate( $affiliate_id );
				if( ! empty( $affiliate ) && isset( $affiliate->user_id ) ){
					$user_id = $affiliate->user_id;
				}
			}
	
			if( empty( $user_id ) ){
				$return_args['msg'] = __( "We have trouble to find a user for your given user_id.", 'action-affwp_add_referral-error' );
				return $return_args;
			}
	
			if( empty( $affiliate_id ) ){
				$return_args['msg'] = __( "We have trouble to find an affiliate for your given affiliate_id.", 'action-affwp_add_referral-error' );
				return $return_args;
			}

			$user = get_user_by( 'id', $user_id );
			if( empty( $user ) ){
				$return_args['msg'] = __( "We have trouble to get the found user.", 'action-affwp_add_referral-error' );
				return $return_args;
			}

			if( ! empty( $products ) && WPWHPRO()->helpers->is_json( $products ) ){
				$products = json_decode( $products, true );
			}

			$args = array(
				'user_id'		=> $user_id,
				'affiliate_id'	=> $affiliate_id,
				'user_name'		=> $user->user_login,
				'amount'		=> ! empty( $amount ) 		? sanitize_text_field( $amount )			: '',
				'description'  	=> ! empty( $description ) 	? sanitize_text_field( $description )		: '',
				'reference'  	=> ! empty( $reference ) 	? sanitize_text_field( $reference ) 		: '',
				'parent_id'  	=> ! empty( $parent_id ) 	? intval( $parent_id ) 						: '',
				'currency'  	=> ! empty( $currency ) 	? sanitize_text_field( $currency ) 			: '',
				'campaign'  	=> ! empty( $campaign ) 	? sanitize_text_field( $campaign ) 			: '',
				'context'  		=> ! empty( $context ) 		? sanitize_text_field( $context ) 			: '',
				'custom'  		=> ! empty( $custom ) 		? $custom						 			: '',
				'date'  		=> ! empty( $date ) 		? date( "Y-m-d H:i:s", strtotime( $date ) )	: '',
				'type'  		=> ! empty( $type ) 		? $type										: '',
				'products'  	=> ! empty( $products ) 	? $products									: '',
				'status'  		=> ! empty( $status ) 		? $status									: '',
				'visit_id'  	=> ! empty( $visit_id ) 	? $visit_id									: '',
			);
	
			$referral_id = affwp_add_referral( $args );
	
			if( ! empty( $referral_id ) ){
				$return_args['success'] = true;
				$return_args['data']['referral_id'] = $referral_id;
				$return_args['data']['referral'] = affwp_get_referral( $referral_id );
				$return_args['msg'] = __( "Referral was successfully created.", 'action-affwp_add_referral-success' );
			} else {
				$return_args['msg'] = __( "An error occured while creating your referral.", 'action-affwp_add_referral-success' );
			}
	
			if( ! empty( $do_action ) ){
				do_action( $do_action, $referral_id, $args, $return_args );
			}
	
			return $return_args;
	
		}

	}

endif; // End if class_exists check.