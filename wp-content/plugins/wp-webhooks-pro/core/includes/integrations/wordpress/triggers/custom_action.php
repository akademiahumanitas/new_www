<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Triggers_custom_action' ) ) :

	/**
	 * Load the custom_action trigger
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Triggers_custom_action {

		public function get_callbacks(){

            return array(
                array(
                    'type' => 'action',
                    'hook' => 'wp_webhooks_send_to_webhook',
                    'callback' => array( $this, 'wp_webhooks_send_to_webhook_action' ),
                    'priority' => 10,
                    'arguments' => 2,
                    'delayed' => false,
                ),
                array(
                    'type' => 'filter',
                    'hook' => 'wp_webhooks_send_to_webhook_filter',
                    'callback' => array( $this, 'wp_webhooks_send_to_webhook_action_filter' ),
                    'priority' => 10,
                    'arguments' => 4,
                    'delayed' => false,
                ),
            );

		}

        public function get_details(){

            $parameter = array(
                'none'   => array( 'short_description' => __( 'No default values given. Send over whatever you like.', 'wp-webhooks' ) ),
            );

            ob_start();
?>
<?php echo __( "You can fire the trigger wherever you want within your PHP code. The only necessity is, that our plugin is fully initialized by WordPress (That means that our plugin has to be fully loaded before the trigger works). Here is a code example:", 'wp-webhooks' ); ?>
<pre>$custom_data = array(
	'data_1' => 'value'
);
$webhook_names = array(
	'15792546059992909'
);
$http_args = array(
	'blocking' => true //Set this to true to receive the response
);

$response = apply_filters( 'wp_webhooks_send_to_webhook_filter', array(), $custom_data, $webhook_names, $http_args );</pre>
<?php echo __( "The <code>apply_filters()</code> function accepts five parameters, which are explained down below:", 'wp-webhooks' ); ?>
<ol>
    <li>
        <strong>'wp_webhooks_send_to_webhook'</strong> (string)
        <br>
        <?php echo __( "This is our trigger identifier so that our plugin knows when to fire the webhook. Please don't change this value.", 'wp-webhooks' ); ?>
    </li>
    <li>
        <strong>array()</strong> (array)
        <br>
        <?php echo __( "An array containing data we will return to the <strong>\$response</strong> variable. Please note that this value will be dynamically filled with all the response data from the sent webhooks. Values you pass into this empty array will be returned as well, except they get overwritten by the dynamic values.", 'wp-webhooks' ); ?>
    </li>
    <li>
        <strong>$custom_data</strong> (array)
        <br>
        <?php echo __( "This variable contains an array of data you want to send over to each of the webhook URL's (payload). Depending on your <strong>Webhook URL</strong> specific settings, it will be sent in different formats (default JSON).", 'wp-webhooks' ); ?>
    </li>
    <li>
        <strong>$webhook_names</strong> (array)
        <br>
        <?php echo __( "This variable contains an array with <strong>Webhook URL</strong>s this trigger should fire on. To add a trigger, add the <strong>Webhook Name</strong> to the array. If you don't send over the this argument at all, all webhook URL's for this webhook trigger will be triggered.", 'wp-webhooks' ); ?>
    </li>
    <li>
        <strong>$http_args</strong> (array)
        <br>
        <?php echo __( "This variable contains an array with further arguments of the http request used with the <strong>wp_safe_remote_request</strong> function. Standard use-cases of it are the <strong>sslverify</strong> and the <strong>blocking</strong> keys.", 'wp-webhooks' ); ?>
    </li>
</ol>
<?php
            $how_to = ob_get_clean();

            $description = array(
                'tipps' => array(
                    __( 'You can fetch the response of each of the sent webhooks requests from the $response variable. You can determine them based on the webhook name, which is used as a key.', 'wp-webhooks' ),
                ),
			);

            $settings = array(
                'load_default_settings' => true
            );

            return array(
                'trigger'           => 'custom_action',
                'name'              => __( 'Custom trigger called', 'wp-webhooks' ),
                'sentence'              => __( 'a custom trigger was called via PHP', 'wp-webhooks' ),
                'parameter'         => $parameter,
                'settings'          => $settings,
                'returns_code'      => $this->get_demo(),
                'short_description' => __( 'This webhook fires after a custom trigger was called. For more information, please check the description.', 'wp-webhooks' ),
                'description'       => $description,
                'callback'          => 'test_custom_action',
                'integration'       => 'wordpress',
            );

        }

        /*
        * Register the post delete trigger logic (DEPRECATED)
        *
        * @since 1.6.4
        */
        public function wp_webhooks_send_to_webhook_action( $data, $webhook_names = array() ){

            $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'custom_action' );
            $response_data = array();

            foreach( $webhooks as $webhook_key => $webhook ){

                if( ! empty( $webhook_names ) ){
                    if( ! empty( $webhook_key ) ){
                        if( ! in_array( $webhook_key, $webhook_names ) ){
                            continue;
                        }
                    }
                }

                $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

                if( $webhook_url_name !== null ){
                    $response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data );
                } else {
                    $response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data );
                }
            }

            do_action( 'wpwhpro/webhooks/trigger_custom_action', $data, $response_data );
        }

        /*
        * Register the custom action trigger logic
        *
        * @since 3.0.5
        */
        public function wp_webhooks_send_to_webhook_action_filter( $response_data, $data, $webhook_names = array(), $http_args = array() ){

            $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'custom_action' );

            if( ! is_array( $response_data ) ){
                $response_data = array();
            }

            foreach( $webhooks as $webhook_key => $webhook ){

                if( ! empty( $webhook_names ) ){
                    if( ! empty( $webhook_key ) ){
                        if( ! in_array( $webhook_key, $webhook_names ) ){
                            continue;
                        }
                    }
                }

                $response_data[ $webhook_key ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data, $http_args );
            }

            do_action( 'wpwhpro/webhooks/trigger_custom_action', $data, $response_data );

            return $response_data;
        }

        /*
        * Register the demo post delete trigger callback
        *
        * @since 1.6.4
        */
        public function get_demo( $options = array() ) {

            return array( __( 'Your very own data construct.', 'wp-webhooks' ) ); // Custom content from the action
        }

    }

endif; // End if class_exists check.