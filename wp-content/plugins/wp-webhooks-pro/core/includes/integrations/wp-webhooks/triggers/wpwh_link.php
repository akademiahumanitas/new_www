<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_Triggers_wpwh_link' ) ) :

	/**
	 * Load the wpwh_link trigger
	 *
	 * @since 4.3.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_Triggers_wpwh_link {

		/**
		 * Register the actual functionality of the webhook
		 *
		 * @param mixed $response
		 * @param string $action
		 * @param string $response_ident_value
		 * @param string $response_api_key
		 * @return mixed The response data for the webhook caller
		 */
		public function get_callbacks(){

            return array(
                array(
                    'type' => 'shortcode',
                    'hook' => 'wpwh_link',
                    'callback' => array( $this, 'ironikus_trigger_wpwh_link' ),
                    'priority' => 10,
                    'arguments' => 2,
                    'delayed' => false,
                ),
                array(
                    'type' => 'action',
                    'hook' => 'wp',
                    'callback' => array( $this, 'ironikus_trigger_wpwh_link_callback' ),
                    'priority' => 10,
                    'arguments' => 1,
                    'delayed' => false,
                ),
            );

		}

        /*
        * Register the user login trigger as an element
        */
        public function get_details(){

            $wpwh_helpers = WPWHPRO()->integrations->get_helper( 'wp-webhooks', 'wpwh_helpers' );
            $parameter = array(
                'custom_data'   => array( 'short_description' => __( 'Your custom data construct build out of the shortcode arguments, as well as the data mapping.', 'wp-webhooks' ) ),
            );

            ob_start();
?>
<p><?php echo __( "The trigger will be fired whenever someone clicks the link that was added with the shortcode <code>[wpwh_link]</code>", 'wp-webhooks' ); ?>
</p>
<?php echo __( "While the shortcode itself does not do much except of displaying a custom link on your page, you might want to add some data to it. To do that, you have two different ways of doing so:", 'wp-webhooks' ); ?>
<ol>
    <li><?php echo __( "You can add the data using the data mapping feature by assigning a data mapping template to your webhook URL.", 'wp-webhooks' ); ?></li>
    <li>
        <?php echo __( "You can also add the data using the shortcode parameters. E.g. <code>[wpwh_link param=\"some value\"]</code>", 'wp-webhooks' ); ?>
        <br>
        <?php echo __( "While <strong>param</strong> is the key within the data response, <strong>some value</strong> is the value. The example above will cause an output similar to:", 'wp-webhooks' ); ?>
        <br>
        <pre>
{
    "param": "some value"
}
</pre>
<br>
<?php echo __( "We also support a variety os special attributes within the shortcode tag (e.g. <code>[wpwh_link wpwh_custom_url_redirect=\"https://yourdomain.com/new-redirect-url/\"]</code> ). Down below you will find a list of those, as well as a short description of what they are good for.", 'wp-webhooks' ); ?>
<table class="wpwh-table wpwh-text-small">
    <thead>
        <tr>
            <td><?php echo __( "Special tag name", 'wp-webhooks' ); ?></td>
            <td><?php echo __( "Special tag description", 'wp-webhooks' ); ?></td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>wpwh_id</td>
            <td><?php echo __( "Use this attribute to set a custom id for the link.", 'wp-webhooks' ); ?></td>
        </tr>
        <tr>
            <td>wpwh_class</td>
            <td><?php echo __( "Set custom CSS classes for the link. If you want to set multiple ones, simply leave a space in between: class-1 class-2", 'wp-webhooks' ); ?></td>
        </tr>
        <tr>
            <td>wpwh_link_label</td>
            <td><?php echo __( "Customizes the text of the link.", 'wp-webhooks' ); ?></td>
        </tr>
        <tr>
            <td>wpwh_new_window</td>
            <td><?php echo __( "Set this argument to \"yes\" if you want the link click to open within a new window.", 'wp-webhooks' ); ?></td>
        </tr>
        <tr>
            <td>wpwh_trigger_names</td>
            <td><?php echo __( "If you want this link to only fire specific webhook URLs, you can define their names here. If you want to use multiple ones, simply separate them via a comma: webhook-1,url-2,demo-3", 'wp-webhooks' ); ?></td>
        </tr>
        <tr>
            <td>wpwh_do_redirect</td>
            <td><?php echo __( "By default we redirect the link click back to the same page to keep the URL format clean. If you do not want that, set this argument to \"no\".", 'wp-webhooks' ); ?></td>
        </tr>
        <tr>
            <td>wpwh_custom_url_redirect</td>
            <td><?php echo __( "If you define this attribute along with a URL, the user will be redirected to the given URL after the link was clicked. This field is prioritised, even when the <code>wpwh_do_redirect</code> argument is set.", 'wp-webhooks' ); ?></td>
        </tr>
        <tr>
            <td>wpwh_raw_url</td>
            <td><?php echo __( "If you prefer to display the URL and not the full link, set this argument to \"yes\".", 'wp-webhooks' ); ?></td>
        </tr>
    </tbody>
</table>

        <?php echo __( "We do also support custom tags, meaning you can add dynamic values from the currently given data. E.g. <code>email=\"%user_email%\"</code> - This will add the email of the currently logged in user. For a full list of the dynamic arguments, please take a look at the list down below.", 'wp-webhooks' ); ?>

        <table class="wpwh-table wpwh-text-small">
            <thead>
                <tr>
                    <td><?php echo __( "Tag name", 'wp-webhooks' ); ?></td>
                    <td><?php echo __( "Tag description", 'wp-webhooks' ); ?></td>
                </tr>
            </thead>
            <tbody>
                <?php foreach( $wpwh_helpers->get_shortcode_tags() as $tag ) : 
                
                if( ! isset( $tag['tag_name'] ) ){
                    continue;
                }

                $title = '';
                if( isset( $tag['title'] ) ){
                    $title = '<strong>' . $tag['title'] . '</strong><br>';
                }

                $description = '';
                if( isset( $tag['description'] ) ){
                    $description = $tag['description'];
                }
                
                ?>
                <tr>
                    <td><?php echo '%' . $tag['tag_name'] . '%'; ?></td>
                    <td><?php echo $title . $description; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </li>
</ol>
<?php
            $how_to = ob_get_clean();

            $description = array(
				'post_delay' => false,
				'steps' => array(
                    $how_to,
                ),
                'tipps' => array(
                    __( 'If the shortcode is not executed and you see the shortcode itself within the frontend, please add make sure the trigger got saved. We only load the shortcode if either a Flow used the endpoint, or a webhook trigger was added.', 'wp-webhooks' ),
                )
			);

            return array(
                'trigger'           => 'wpwh_link',
                'name'              => __( 'Custom link clicked', 'wp-webhooks' ),
                'sentence'              => __( 'a custom link was clicked', 'wp-webhooks' ),
                'parameter'         => $parameter,
                'returns_code'      => $this->get_demo( array() ),
                'short_description' => __( 'This webhook fires as soon as a custom link was clicked.', 'wp-webhooks' ),
                'description'       => $description,
                'integration'       => 'wp-webhooks',
                'premium'           => true,
            );

        }

        public function ironikus_trigger_wpwh_link( $attr = array(), $content = '' ){

            if( ! is_array( $attr ) ){
                $attr = array();
            }

            $id_out = '';
            if( isset( $attr['wpwh_id'] ) ){
                $id_out = 'id="wpwh_link_id_' . intval( $attr['wpwh_id'] ) . '" ';
            }

            $button_label_out = 'Send data';
            if( isset( $attr['wpwh_link_label'] ) ){
                $button_label_out = sanitize_text_field( $attr['wpwh_link_label'] );
            }

            $setting_new_window = '';
            if( isset( $attr['wpwh_new_window'] ) && $attr['wpwh_new_window'] == 'yes' ){
                $setting_new_window = ' target="_blank"';
            }

            $setting_raw_url = false;
            if( isset( $attr['wpwh_raw_url'] ) && $attr['wpwh_raw_url'] == 'yes' ){
                $setting_raw_url = true;
            }

            $html_classes = array(
                'wpwh_link'
            );
            if( isset( $attr['wpwh_class'] ) ){
                $class_array = explode( ' ', $attr['wpwh_class'] );

                if( is_array( $class_array ) ){
                    $html_classes = array_merge( $html_classes, $class_array );
                }
            }

            $query_args = array();
            
            $signature_string = '';
            $secret = wp_create_nonce( 'trigger_wpwh_link' );

            foreach( $attr as $attr_name => $attr_value ){

                $validated_value = $attr_value;
                if( is_array( $attr_value ) || is_object( $attr_value ) ){
                    $validated_value = json_encode( $attr_value );
                }

                $validated_value = htmlspecialchars( $validated_value );
                $signature_string .= $validated_value;
                $attr_name = str_replace( '-', '_', sanitize_title( $attr_name ) );

                $query_args[ 'wpwh_link_dt_' . $attr_name ] = $validated_value;

            }
            
            $signature = WPWHPRO()->helpers->generate_signature( $signature_string, $secret );
            
            $query_args['wpwh_link_signature'] = $signature;
            $query_args['wpwh_link_signature_key'] = base64_encode( $secret );
            $query_args['trigger_wpwh_link_nonce'] = wp_create_nonce( 'trigger_wpwh_link' );

            //encode arguments
            foreach( $query_args as $arg_key => $arg_val ){
                $query_args[ $arg_key ] = urlencode( $arg_val );
            }
            
            if( $setting_raw_url ){
                $link_html = add_query_arg( $query_args );
            } else {
                $link_html = '<a ' . $id_out . 'class="' . esc_attr( implode( ' ', $html_classes ) ) . '" href="' . add_query_arg( $query_args ) . '"' . $setting_new_window . '>' . __(  $button_label_out, 'wp-webhooks' ) . '</a>';
            }

            $link_html = apply_filters( 'wpwhpro/webhooks/wpwh_link/link_html', $link_html, $attr );

            return $link_html;
            
        }

        public function ironikus_trigger_wpwh_link_callback(){

            if( 
                ! isset( $_GET['trigger_wpwh_link_nonce'] ) 
                || ! isset( $_GET['wpwh_link_signature'] ) 
                || ! isset( $_GET['wpwh_link_signature_key'] ) 
            ){
                return;
            }

            if( ! check_admin_referer( 'trigger_wpwh_link', 'trigger_wpwh_link_nonce' ) ){
                return;
            }

            $link_data = array();
            foreach( $_GET as $gk => $gv ){

                $ident = 'wpwh_link_dt_';
                if( substr( $gk, 0, strlen( $ident ) ) === $ident ){
                    $link_data_key = substr( $gk, strlen( $ident ) );
                    $link_data[ $link_data_key ] = $gv;
                }
            }

            $testing_signature = '';
            foreach( $link_data as $signature_part ){
                $testing_signature .= $signature_part;
            }
            $form_key = base64_decode( $_GET['wpwh_link_signature_key'] );

            if( $_GET['wpwh_link_signature'] !== WPWHPRO()->helpers->generate_signature( $testing_signature, $form_key ) ){
                return;
            }
   
            $attr = $link_data;
            $response_data = array();
            $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wpwh_link' );
            $wpwh_helpers = WPWHPRO()->integrations->get_helper( 'wp-webhooks', 'wpwh_helpers' );
            $special_arguments = array(
                'wpwh_trigger_names' => 'all',
                'wpwh_id' => 0,
                'wpwh_link_label' => '',
                'wpwh_new_window' => '',
                'wpwh_class' => '',
                'wpwh_do_redirect' => 'yes',
                'wpwh_custom_url_redirect' => '',
                'wpwh_raw_url' => 'yes',
            );

            foreach( $special_arguments as $ak => $dv ){
                if( isset( $attr[ $ak ] ) ){
                    $special_arguments[ $ak ] = $attr[ $ak ];
                    unset( $attr[ $ak ] );
                }
            }

            $shortcode_tags = $wpwh_helpers->get_shortcode_tags();
            $attr_validated = $wpwh_helpers->validate_data( $attr, $shortcode_tags );

            $trigger_name_whitelist = array();
            if( $special_arguments['wpwh_trigger_names'] !== 'all' ){
                $trigger_names_array = explode( ',', $special_arguments['wpwh_trigger_names'] );
                if( is_array( $trigger_names_array ) ){
                    foreach( $trigger_names_array as $single_trigger ){
                        $trigger_name_whitelist[] = trim( $single_trigger );
                    }
                }
            } 

            foreach( $webhooks as $webhook ){

                $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
                
                if( ! empty( $trigger_name_whitelist ) && ! in_array( $webhook_url_name, $trigger_name_whitelist ) ){
                    continue;
                }

                if( $webhook_url_name !== null ){
                    $response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $attr_validated );
                } else {
                    $response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $attr_validated );
                }

            }

            do_action( 'wpwhpro/webhooks/wpwh_link', $attr_validated, $attr, $response_data );
 
            if( $special_arguments['wpwh_custom_url_redirect'] !== '' ){
                wp_redirect( $special_arguments['wpwh_custom_url_redirect'] );
                die();
            } elseif( $special_arguments['wpwh_do_redirect'] !== 'no' ){
                wp_redirect( WPWHPRO()->helpers->get_current_url(false) );
                die();
            }
            
        }

        /*
        * Register the demo data response
        *
        * @param $data - The default options
        *
        * @return array - The demo data
        */
        public function get_demo( $options = array() ){

            $data = array (
                'your custom data construct'
            );

            return $data;
        }

    }

endif; // End if class_exists check.