<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_cron_scheduler_Triggers_cron_wordpress_executed' ) ) :

 /**
  * Load the cron_wordpress_executed trigger
  *
  * @since 6.1.4
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_cron_scheduler_Triggers_cron_wordpress_executed {

	private $cron_schedules_cache = array();

	public function is_active(){
        return ( ! defined( 'DISABLE_WP_CRON' ) || ! DISABLE_WP_CRON ) ? true : false;
    }

	public function get_callbacks(){

		//Execute custom logic here because it directly validates if endponits are set or not
		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'cron_wordpress_executed' );
		$action_callbacks = array(
			array(
				'type' => 'action',
				'hook' => 'init',
				'callback' => array( $this, 'wpwh_maybe_register_cron_schedules' ),
				'priority' => 1000,
				'arguments' => 1,
				'delayed' => false,
			)
		);

		foreach( $webhooks as $webhook ){

			$scheduler_key = 'wpwh/integrations/cron_scheduler/cron_wordpress_executed/' . $webhook['webhook_url_name'];
			$scheduler_key = str_replace( '-', '_', $scheduler_key );

			$args = array(
				'scheduler_key' => $scheduler_key,
				'webhook_url_name' => $webhook['webhook_url_name'],
				
			);
			$wpwhpro_cron_scheduler_interval = 0;  
			$wpwhpro_cron_scheduler_start_time = 0;  
			$wpwhpro_cron_scheduler_json = 0;  

			if( isset( $webhook['settings'] ) ){

				if( isset( $webhook['settings']['wpwhpro_cron_scheduler_interval'] ) && ! empty( $webhook['settings']['wpwhpro_cron_scheduler_interval'] ) ){
					$wpwhpro_cron_scheduler_interval = intval( $webhook['settings']['wpwhpro_cron_scheduler_interval'] );
				}

				if( isset( $webhook['settings']['wpwhpro_cron_scheduler_start_time'] ) && ! empty( $webhook['settings']['wpwhpro_cron_scheduler_start_time'] ) ){
					$wpwhpro_cron_scheduler_start_time = WPWHPRO()->helpers->get_formatted_date( $webhook['settings']['wpwhpro_cron_scheduler_start_time'] );
				}

				if( isset( $webhook['settings']['wpwhpro_cron_scheduler_json'] ) && ! empty( $webhook['settings']['wpwhpro_cron_scheduler_json'] ) ){
					$wpwhpro_cron_scheduler_json = json_decode( $webhook['settings']['wpwhpro_cron_scheduler_json'], true );
					if( ! is_array( $wpwhpro_cron_scheduler_json ) ){
						$wpwhpro_cron_scheduler_json = array();
					}
				}

			}

			if( empty( $wpwhpro_cron_scheduler_start_time ) ){
				$wpwhpro_cron_scheduler_start_time = gmdate( 'U' );
			}

			$action_callbacks[] = array(
				'type' => 'action',
				'hook' => $scheduler_key,
				'callback' => array( $this, 'wpwh_trigger_cron_wordpress_executed' ),
				'priority' => 10,
				'arguments' => 2,
				'delayed' => false,
			);

			if( 
				! empty( $wpwhpro_cron_scheduler_interval )
			){

				$args['interval'] = $wpwhpro_cron_scheduler_interval;
				$args['start_time'] = WPWHPRO()->helpers->get_formatted_date( $wpwhpro_cron_scheduler_start_time );

				$schedule_time = gmdate( $wpwhpro_cron_scheduler_start_time ) + $wpwhpro_cron_scheduler_interval;
				$args['scheduled_for'] = WPWHPRO()->helpers->get_formatted_date( $schedule_time );

				$args['json_data'] = $wpwhpro_cron_scheduler_json;

				$scheduler_args = array(
					'timestamp' => $schedule_time,
					'hook' => $scheduler_key,
					'attributes' => array( 'arguments' => $args ), //Required as every item is considered a variable
				);

				$this->cron_schedules_cache[] = $scheduler_args;
			}

		}

		return $action_callbacks;
	}

	public function get_details(){

		$parameter = array(
			'scheduler_key' => array( 'short_description' => __( '(String) The key used to schedule the action. This can be used to register a custom WP filter callback.', 'wp-webhooks' ) ),
			'webhook_url_name' => array( 'short_description' => __( '(String) The name of the webhook URL', 'wp-webhooks' ) ),
			'interval' => array( 'short_description' => __( '(Integer) The amount of seconds used between the cron executions for this trigger.', 'wp-webhooks' ) ),
			'start_time' => array( 'short_description' => __( '(String) A specific start date (If no other is specified). E.g. YYYY-MM-DD HH:MM:SS (SQL format: Y-m-d H:i:s)', 'wp-webhooks' ) ),
			'scheduled_for' => array( 'short_description' => __( '(String) A date formatted timestamp of the time this execution was scheduled for.', 'wp-webhooks' ) ),
			'json_data' => array( 'short_description' => __( '(Array) The additional JSON data that was provided within the trigger settings.', 'wp-webhooks' ) ),
			'executed_on' => array( 'short_description' => __( '(String) The date formatted timestamp when this trigger was executed.', 'wp-webhooks' ) ),
		);

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_cron_scheduler_interval' => array(
					'id'		  => 'wpwhpro_cron_scheduler_interval',
					'type'		=> 'text',
					'multiple'	=> true,
					'label'	   => __( 'Cron interval', 'wp-webhooks' ),
					'placeholder' => '',
					'required'	=> true,
					'description' => sprintf( __( 'The interval of the cronjob in seconds. E.g. one day equals <strong>%d</strong> in seconds.', 'wp-webhooks' ), DAY_IN_SECONDS ),
				),
				'wpwhpro_cron_scheduler_start_time' => array(
					'id'		  => 'wpwhpro_cron_scheduler_start_time',
					'type'		=> 'text',
					'multiple'	=> true,
					'label'	   => __( 'Cron start time', 'wp-webhooks' ),
					'placeholder' => '',
					'required'	=> false,
					'description' => __( 'The time for when the cronjob should be executed. Please set the date in the following format: YYYY-MM-DD HH:MM:SS (SQL format: Y-m-d H:i:s). If no specific date/time is set, we use the current date/time.', 'wp-webhooks' ),
				),
				'wpwhpro_cron_scheduler_json' => array(
					'id'		  => 'wpwhpro_cron_scheduler_json',
					'type'		=> 'text',
					'multiple'	=> true,
					'label'	   => __( 'Additional JSON data', 'wp-webhooks' ),
					'placeholder' => '',
					'required'	=> false,
					'description' => __( 'This field accepts a JSON formatted string. It can be used to add additional data to the payload of the trigger request. E.g. {"email":"jon@doe.com"}', 'wp-webhooks' ),
				),
			)
		);

		$description = array(
			'steps' => array(
				__( 'Once you added the trigger, please go to the trigger settings.', 'wp-webhooks' ),
				__( 'Set the Cron interval. This field accepts a number that consists of the amount of seconds you want to wait before the cron event fires the trigger.', 'wp-webhooks' ),
			)
		);

		return array(
			'trigger'		   => 'cron_wordpress_executed',
			'name'			  => __( 'WordPress cron job executed', 'wp-webhooks' ),
			'sentence'			  => __( 'a WordPress cron job executed', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires as soon as a WordPress cron job executed.', 'wp-webhooks' ),
			'description'	   => $description,
			'integration'	   => 'cron-scheduler',
			'premium'		   => true,
		);

	}

	/**
	 * Maybe schedule cron schedules for specific webhook endpoints
	 * This function runs just after cron scheduler was initialized
	 *
	 * @return void
	 */
	public function wpwh_maybe_register_cron_schedules(){

		if( ! empty( $this->cron_schedules_cache ) ){
			foreach( $this->cron_schedules_cache as $schedule ){

				$next_action = WPWHPRO()->scheduler->get_next_action( $schedule );

				//Make sure we only execute the action if no other one exists. Each trigger can only have one action
				if( $next_action ){
					continue;
				}

				$response = WPWHPRO()->scheduler->schedule_single_action( $schedule );
			}
		}

	}
	
	public function wpwh_trigger_cron_wordpress_executed( $arguments = array() ){

		if( ! isset( $arguments['webhook_url_name'] ) ){
			return;
		}

		$payload = $arguments;

		$payload['executed_on'] = WPWHPRO()->helpers->get_formatted_date( time() );

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'cron_wordpress_executed' );
		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			//Only the current webhook should be executed
			if( $arguments['webhook_url_name'] !== $webhook_url_name ){
				$is_valid = false;
			}

			if( $is_valid ){

				if( $webhook_url_name !== null ){
					$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				} else {
					$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				}
			}

		}

		do_action( 'wpwhpro/integrations/cron_scheduler/triggers/cron_wordpress_executed', $payload );
	}

	public function get_demo( $options = array() ) {

		$data = array(
			'scheduler_key' => 'wpwh/integrations/cron_scheduler/cron_wordpress_executed/demo_cron',
			'webhook_url_name' => 'demojob',
			'interval' => 120,
			'start_time' => '2023-04-11 09:06:39',
			'scheduled_for' => '2023-04-11 09:08:39',
			'json_data' => array(
			  'email' => 'jon@doe.com',
		   ),
			'executed_on' => '2023-04-11 09:08:48',
		 );

		return $data;
	}

  }

endif; // End if class_exists check.