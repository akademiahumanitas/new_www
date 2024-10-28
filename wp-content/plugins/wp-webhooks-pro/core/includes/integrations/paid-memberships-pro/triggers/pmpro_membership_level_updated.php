<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_paid_memberships_pro_Triggers_pmpro_membership_level_updated' ) ) :

 /**
  * Load the pmpro_membership_level_updated trigger
  *
  * @since 6.1.4
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_paid_memberships_pro_Triggers_pmpro_membership_level_updated {

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
                'type' => 'action',
                'hook' => 'pmpro_updated_membership_level',
                'callback' => array( $this, 'pmpro_updated_membership_level_callback' ),
                'priority' => 20,
                'arguments' => 1,
                'delayed' => true,
            ),
            array(
                'type' => 'action',
                'hook' => 'pmpro_save_membership_level',
                'callback' => array( $this, 'pmpro_save_membership_level_callback' ),
                'priority' => 20,
                'arguments' => 1,
                'delayed' => true,
            ),
        );
    }

    public function get_details(){

        $parameter = array(
            'id' => array( 'short_description' => __( '(Integer) The ID of the membership level.', 'wp-webhooks' ) ),
            'name' => array( 'short_description' => __( '(String) The name of the level.', 'wp-webhooks' ) ),
            'description' => array( 'short_description' => __( '(String) The description.', 'wp-webhooks' ) ),
            'confirmation' => array( 'short_description' => __( '(String) The confirmation message of the subscription.', 'wp-webhooks' ) ),
            'initial_payment' => array( 'short_description' => __( '(String) The initial payment price.', 'wp-webhooks' ) ),
            'billing_amount' => array( 'short_description' => __( '(String) The billing amount.', 'wp-webhooks' ) ),
            'cycle_number' => array( 'short_description' => __( '(String) The cycle number.', 'wp-webhooks' ) ),
            'cycle_period' => array( 'short_description' => __( '(String) The cycle period.', 'wp-webhooks' ) ),
            'billing_limit' => array( 'short_description' => __( '(String) The billing limit.', 'wp-webhooks' ) ),
            'trial_amount' => array( 'short_description' => __( '(String) The trial amount.', 'wp-webhooks' ) ),
            'trial_limit' => array( 'short_description' => __( '(String) The trial limit.', 'wp-webhooks' ) ),
            'expiration_number' => array( 'short_description' => __( '(String) The expiration number.', 'wp-webhooks' ) ),
            'expiration_period' => array( 'short_description' => __( '(String) The expiration period.', 'wp-webhooks' ) ),
            'allow_signups' => array( 'short_description' => __( '(String) Displays 0 if no sign ups allowed. Otherwise 1.', 'wp-webhooks' ) ),
            'categories' => array( 'short_description' => __( '(String) A comma-separated string of category IDs.', 'wp-webhooks' ) ),
        );

        $settings = array(
            'load_default_settings' => true,
            'data' => array(
                'wpwhpro_pmpro_trigger_on_membership_level' => array(
                    'id'		  => 'wpwhpro_pmpro_trigger_on_membership_level',
                    'type'		=> 'select',
                    'multiple'	=> true,
                    'choices'	  => array(),
                    'query'			=> array(
                        'filter'	=> 'helpers',
                        'args'		=> array(
                            'integration' => 'paid-memberships-pro',
                            'helper' => 'pmpro_helpers',
                            'function' => 'get_query_levels',
                        )
                    ),
                    'label'	   => __( 'Trigger on selected membership level', 'wp-webhooks' ),
                    'placeholder' => '',
                    'required'	=> false,
                    'description' => __( 'Select only the membership levels you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', 'wp-webhooks' )
                ),
            )
        );

        return array(
            'trigger'           => 'pmpro_membership_level_updated',
            'name'              => __( 'Membership level updated', 'wp-webhooks' ),
            'sentence'              => __( 'a membership level was updated', 'wp-webhooks' ),
            'parameter'         => $parameter,
            'settings'          => $settings,
            'returns_code'      => $this->get_demo( array() ),
            'short_description' => __( 'This webhook fires as soon as a membership level was updated within Paid Memberships Pro.', 'wp-webhooks' ),
            'description'       => array(),
            'integration'       => 'paid-memberships-pro',
            'premium'           => true,
        );

    }

    /**
     * An alterantive function to track the saving 
     * of a membership level on the admin membership level page
     *
     * @return void
     */
    public function pmpro_save_membership_level_callback( $level_id ){

        if( class_exists( 'PMPro_Membership_Level' ) ){
            $level = new PMPro_Membership_Level( intval( $level_id ) );
            $this->pmpro_updated_membership_level_callback( $level );
        }
        
    }

    /**
     * Triggers once a new Paid Membership Pro membership level was updated
     *
	 * @param object|PMPro_Membership_Level $level The data about the current membership level
     */
    public function pmpro_updated_membership_level_callback( PMPro_Membership_Level $level ){

        $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'pmpro_membership_level_updated' );

        $membership_id = isset( $level->id ) ? $level->id : 0;

        //Bail if no membership ID is given
        if( empty( $level->id ) ){
            return;
        }

        $category_string = '';
        $categories = $level->get_membership_level_categories( $membership_id );
        if( is_array( $categories ) ){
            $category_string = implode( ',', $categories );
        }

        $payload = array(
            'id'=> $membership_id,
            'name' => isset( $level->name ) ? $level->name : '',
            'description' => isset( $level->description ) ? $level->description : '',
            'confirmation' => isset( $level->confirmation ) ? $level->confirmation : '',
            'initial_payment' => isset( $level->initial_payment ) ? $level->initial_payment : '',
            'billing_amount' => isset( $level->billing_amount ) ? $level->billing_amount : '',
            'cycle_number' => isset( $level->cycle_number ) ? $level->cycle_number : '',
            'cycle_period' => isset( $level->cycle_period ) ? $level->cycle_period : '',
            'billing_limit' => isset( $level->billing_limit ) ? $level->billing_limit : '',
            'trial_amount' => isset( $level->trial_amount ) ? $level->trial_amount : '',
            'trial_limit' => isset( $level->trial_limit ) ? $level->trial_limit : '',
            'expiration_number' => isset( $level->expiration_number ) ? $level->expiration_number : '',
            'expiration_period' => isset( $level->expiration_period ) ? $level->expiration_period : '',
            'allow_signups' => isset( $level->allow_signups ) ? $level->allow_signups : '',
            'categories' => $category_string,
        );

        $response_data_array = array();

        foreach( $webhooks as $webhook ){

            $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
            $is_valid = true;

            if( isset( $webhook['settings'] ) ){
                if( isset( $webhook['settings']['wpwhpro_pmpro_trigger_on_membership_level'] ) && ! empty( $webhook['settings']['wpwhpro_pmpro_trigger_on_membership_level'] ) ){
                    if( ! in_array( $membership_id, $webhook['settings']['wpwhpro_pmpro_trigger_on_membership_level'] ) ){
                        $is_valid = false;
                    }
                }
            }

            if( $is_valid ){
                if( $webhook_url_name !== null ){
                    $response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
                } else {
                    $response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
                }
            }

        }

        do_action( 'wpwhpro/webhooks/trigger_pmpro_membership_level_updated', $payload, $response_data_array );
    }

    public function get_demo( $options = array() ) {

        $data = array (
            'id' => '2',
            'name' => 'Demo Level',
            'description' => 'This is a demo level.',
            'confirmation' => '',
            'initial_payment' => '10.00000000',
            'billing_amount' => '20.00000000',
            'cycle_number' => '1',
            'cycle_period' => 'Day',
            'billing_limit' => '0',
            'trial_amount' => '0.00000000',
            'trial_limit' => '0',
            'expiration_number' => '0',
            'expiration_period' => '',
            'allow_signups' => '0',
            'categories' => '91,119,120',
        );

        return $data;
    }

  }

endif; // End if class_exists check.