<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Triggers_send_email' ) ) :

	/**
	 * Load the send_email trigger
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Triggers_send_email {

        public function is_active(){

            //Backwards compatibility for the "Email integration" integration
            if( defined( 'WPWH_EMAILS_PLUGIN_NAME' ) ){
                return false;
            }

            return true;
        }

		public function get_callbacks(){

            return array(
                array(
                    'type' => 'filter',
                    'hook' => 'wp_mail',
                    'callback' => array( $this, 'ironikus_trigger_send_email' ),
                    'priority' => 10,
                    'arguments' => 1,
                    'delayed' => false,
                ),
            );

		}

        public function get_details(){

            $parameter = array(
				'to' => array( 'short_description' => __( '(String) A string containing one or multiple emails (as a comma-separated list) of the receivers of the email.', 'wp-webhooks' ) ),
				'subject' => array( 'short_description' => __( '(String) The subject of the email.', 'wp-webhooks' ) ),
				'message' => array( 'short_description' => __( '(String) The main mesage (body) of the email.', 'wp-webhooks' ) ),
				'headers' => array( 'short_description' => __( '(Array) Further data about the outgoing email.', 'wp-webhooks' ) ),
				'attachments' => array( 'short_description' => __( '(Array) An array of given email attachments.', 'wp-webhooks' ) ),
			);

			$settings = array();

            return array(
                'trigger'           => 'send_email',
                'name'              => __( 'Email sent', 'wp-webhooks' ),
                'sentence'              => __( 'an email was sent', 'wp-webhooks' ),
                'parameter'         => $parameter,
                'settings'          => $settings,
                'returns_code'      => $this->get_demo( array() ),
                'short_description' => __( 'This webhook fires while an email is being sent from your WordPress site.', 'wp-webhooks' ),
                'description'       => array(),
                'callback'          => 'test_send_email',
                'integration'       => 'wordpress',
                'premium'           => false,
            );

        }

        public function ironikus_trigger_send_email( $atts ){
			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'send_email' );
			$response_data = array();

			foreach( $webhooks as $webhook ){
				$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

				if( $webhook_url_name !== null ){
					$response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $atts );
				} else {
					$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $atts );
				}
			}

			do_action( 'wpwhpro/webhooks/trigger_send_email', $atts, $response_data );

			return $atts;
		}

        /*
        * Register the demo post delete trigger callback
        *
        * @since 1.6.4
        */
        public function get_demo( $options = array() ) {

            $data = array (
				"to" => 'test@test.demo',
				"subject" => 'This is the subject',
				"message" => htmlspecialchars( 'This is a <strong>HTML</strong> message!' ),
				"headers" => array(
					"Content-Type: text/html; charset=UTF-8",
					"From: Sender Name <anotheremail@someemail.demo>",
					"Cc: Receiver Name <receiver@someemail.demo>",
					"Cc: onlyemail@someemail.demo",
					"Bcc: bccmail@someemail.demo",
					"Reply-To: Reply Name <replytome@someemail.demo>",
				),
				"attachments" => array(
					"/Your/full/server/path/wp-content/uploads/2020/06/my-custom-file.jpg",
					"/Your/full/server/path/wp-content/uploads/2020/06/another-custom-file.jpg",
				)
			);

            return $data;
        }

    }

endif; // End if class_exists check.