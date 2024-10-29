<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_cron_scheduler_Triggers_cron_executed' ) ) :

 /**
  * Load the cron_executed trigger
  *
  * @since 6.1.4
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_cron_scheduler_Triggers_cron_executed {

	public function get_details(){

		$parameter = array(
			'custom_construct' => array( 'short_description' => __( '(Mixed) The data that was sent along with the HTTP call that was made to the receivable URL from within Zapier.', 'wp-webhooks' ) ),
		);

		$description = array(
			'steps' => array(
				__( 'Within the settings of this webhook trigger, copy the receivable URL.', 'wp-webhooks' ),
				__( 'Head into your cronjob configuration and add the recevable URL. If you cannot add a URL but a PHP file instead, use cURL to call the URL: <a title="Visit the docs" target="_blank" href="https://wp-webhooks.com/docs/knowledge-base/how-to-use-curl-to-call-a-webhook-using-a-receivable-url/">https://wp-webhooks.com/docs/knowledge-base/how-to-use-curl-to-call-a-webhook-using-a-receivable-url/</a>', 'wp-webhooks' ),
				__( 'Place the receivable URL there and send data based on your requirements.', 'wp-webhooks' ),
				__( 'In case your cron setup requires a custom PHP file to be called, please create one and connect it to your cron job as required by your server host.', 'wp-webhooks' ),
				__( 'Once you created the file, please add the following code into the file after the <?php tag.', 'wp-webhooks' ),
				__( 'When you now manually call the file, you will see the response of the cURL requrest. If everything looks fine, please deactivat the lines starting with "echo". You can do that by adding double slashes in front: //', 'wp-webhooks' ),
			),
			'tipps' => array(
				__( 'To receive data on the receivable URL, please use the "Webhooks by Zapier" app within Zapier.', 'wp-webhooks' ),
				__( 'The receivable URL accepts content types such as JSON, form data, or XML.', 'wp-webhooks' ),
			)
		);

		$settings = array(
			'load_default_settings' => false,
			'data' => array(
				'wpwhprocron_scheduler_return_full_request' => array(
					'id'		  => 'wpwhprocron_scheduler_return_full_request',
					'type'		=> 'checkbox',
					'label'	   => __( 'Send full request', 'wp-webhooks' ),
					'placeholder' => '',
					'required'	=> false,
					'description' => __( 'Send the full, validated request instead of the payload (body) data only. This gives you access to header, cookies, response type and much more.', 'wp-webhooks' )
				),
			)
		);

		ob_start();
		?><pre>
// Set the API endpoint URL
$url = "https://your-domain.test/your-receivable-url";

// Initialize cURL.
$ch = curl_init();

// Execute the cURL call.
$response = curl_exec( $ch );

// Check for any errors.
if ( curl_errno( $ch ) ) {
echo 'Error: ' . curl_error( $ch );
} else {
echo 'Response: ' . $response;
}

// Close the cURL session.
curl_close( $ch );
</pre><?php
		$curl_example = ob_get_clean();

		$description['steps'][] = $curl_example;

		return array(
			'trigger'		   => 'cron_executed',
			'name'			  => __( 'Cron job executed', 'wp-webhooks' ),
			'sentence'			  => __( 'a cron job was executed', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => sprintf( __( 'This webhook fires as soon as a cron job was executed and called the callback URL of this webhook.', 'wp-webhooks' ), WPWHPRO()->settings->get_page_title() ),
			'description'	   => $description,
			'integration'	   => 'cron-scheduler',
			'receivable_url'	=> true,
			'premium'		   => true,
		);

	}

	public function execute( $return_data, $response_body, $trigger_url_name ){

		if( $trigger_url_name !== null ){
			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'cron_executed', $trigger_url_name );
			if( ! empty( $webhooks ) ){
				$webhooks = array( $webhooks );
			} else {
				$return_data['msg'] = __( 'We could not locate a callable trigger URL.', 'wp-webhooks' );
				return $return_data;
			}
		} else {
			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'cron_executed' );
		}
		

		$payload = $response_body['content'];

		$response_data_array = array();

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				foreach( $webhook['settings'] as $settings_name => $settings_data ){
	  
				  if( $settings_name === 'wpwhprocron_scheduler_return_full_request' && ! empty( $settings_data ) ){
					$payload = $response_body;
				  }
	  
				}
			}

			if( $is_valid ){

				$webhook_response = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload, array( 'blocking' => true ) );

				if( $webhook_url_name !== null ){
					$response_data_array[ $webhook_url_name ] = $webhook_response;
				} else {
					$response_data_array[] = $webhook_response;
				}
			}

		}

		$return_data['success'] = true;
		$return_data['data'] = ( count( $response_data_array ) > 1 ) ? $response_data_array : reset( $response_data_array );

		do_action( 'wpwhpro/webhooks/trigger_cron_executed', $return_data, $response_body, $trigger_url_name, $response_data_array );

		return $return_data;
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'custom_construct' => 'The data that was sent to the receivable data URL. Or the full request array.',
		);

		return $data;
	}

  }

endif; // End if class_exists check.