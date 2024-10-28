<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_affiliatewp_Actions_affwp_add_visit' ) ) :

	/**
	 * Load the affwp_add_visit action
	 *
	 * @since 4.2.3
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_affiliatewp_Actions_affwp_add_visit {

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
				'affiliate_id' => array( 'required' => true, 'short_description' => __( 'The id or email of the related affiliate. (Optional in case user_id is set).', 'wp-webhooks' ) ),
				'ip' => array( 'short_description' => __( 'The ip of the person who should be assigned to the visit. (Will be anonymized in case IP logging is disabled within AffiliateWP).', 'wp-webhooks' ) ),
				'campaign' => array( 'short_description' => __( 'Set a visit campaign. This can be a identifier you created for a specific project. E.g. Summer Promotion', 'wp-webhooks' ) ),
				'context' => array( 'short_description' => __( 'Some more details of where the user comes from. E.g.: cta-button', 'wp-webhooks' ) ),
				'url' => array( 'short_description' => __( 'A URL the visitor arrived at.', 'wp-webhooks' ) ),
				'referrer' => array( 'short_description' => __( 'A referral URL from where the visitor came from If nothing is set, it will be counted as direct traffic.', 'wp-webhooks' ) ),
				'date' => array( 'short_description' => __( 'The date and time of creation of the visit. In case nothing is set, the current time is used.', 'wp-webhooks' ) ),
				'do_action'	 => array( 'short_description' => __( 'Advanced: Register a custom action after this webhook was fired.', 'wp-webhooks' ) ),
			);

			ob_start();
			?>
<?php echo __( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $visit_id, $args, $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$visit_id</strong> (integer)<br>
		<?php echo __( "The id of the newly created visit.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$args</strong> (array)<br>
		<?php echo __( "The data used to create the visit.", 'wp-webhooks' ); ?>
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
				'msg' => 'The visit was successfully created.',
				'data' => 
				array (
				  'visit_id' => 2,
				  'visit' => 
				  array (
					0 => 
					array (
					  'visit_id' => 2,
					  'affiliate_id' => 8,
					  'referral_id' => 0,
					  'rest_id' => '',
					  'url' => 'https://mydomain.test/custom-landing-page/',
					  'referrer' => 'https://somereferrer.test/custompath/',
					  'campaign' => 'Summer Promotion',
					  'context' => 'cta-button',
					  'ip' => '192.168.0.1',
					  'date' => '2021-04-10 11:25:33',
					),
				  ),
				),
			);

			$description = array(
				'tipps' => array(
					__( 'This webhook enables you to create a new visit within AffiliateWP using a webhook endpoint. A visit is a database entry about a user that clicked on an affiliate link from a specific affiliate.', 'wp-webhooks' ),
				)
			);

			return array(
				'action'			=> 'affwp_add_visit',
				'name'			  => __( 'Add visit', 'wp-webhooks' ),
				'sentence'			  => __( 'add a visit', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Create a visit within AffiliateWP via a webhook call.', 'wp-webhooks' ),
				'description'	   => $description,
				'integration'	   => 'affiliatewp',
				'premium'		   => true,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => __( "No visit was created.", 'action-affwp_add_visit-error' ),
				'data' => array(),
			);

			$user_id = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_id' );
			$affiliate_id = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'affiliate_id' );
			$ip = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'ip' );
			$campaign = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'campaign' );
			$context = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'context' );
			$url = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'url' );
			$referrer = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'referrer' );
			$date = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'date' );
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

			if( ! empty( $user_id ) ){
				$affiliate_id = affwp_get_affiliate_id( $user_id );
			} elseif( is_numeric( $affiliate_id ) ) {
				$affiliate_id = intval( $affiliate_id );
			} elseif( is_email( $affiliate_id ) ){
				$user = get_user_by( 'email', $affiliate_id );
				if( ! empty( $user ) ){
					if( ! empty( $user->ID ) ){
						$user_id = $user->ID;
						$affiliate_id = affwp_get_affiliate_id( $user_id );
					}
				}
			}
	
			if( empty( $affiliate_id ) ){
				$return_args['msg'] = __( "We could not find an affiliate for your given data.", 'action-affwp_add_visit-error' );
				return $return_args;
			}

			$args = array(
				'affiliate_id' => $affiliate_id,
				'ip'           => $ip,
				'url'          => $url,
				'campaign'     => $campaign,
				'referrer'     => $referrer,
				'context'      => $context,
				'date'         => $date,
			);
	
			$visit_id = affiliate_wp()->visits->add( $args );
	
			if( ! empty( $visit_id ) ){
				$return_args['success'] = true;
				$return_args['data']['visit_id'] = $visit_id;
				$return_args['data']['visit'] = affiliate_wp()->visits->get_visits( array( 'visit_id' => intval( $visit_id ) ) );
				$return_args['msg'] = __( "The visit was successfully created.", 'action-affwp_add_visit-error' );
			} else {
				$return_args['msg'] = __( "An error occured while creating your visit.", 'action-affwp_add_visit-success' );
			}
	
			if( ! empty( $do_action ) ){
				do_action( $do_action, $visit_id, $args, $return_args );
			}
	
			return $return_args;
	
		}

	}

endif; // End if class_exists check.