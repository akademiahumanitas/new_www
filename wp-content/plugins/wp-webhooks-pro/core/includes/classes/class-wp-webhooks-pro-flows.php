<?php

/**
 * WP_Webhooks_Pro_Flows Class
 *
 * This class contains all of the available flows functions
 *
 * @since 4.3.0
 */

/**
 * The flows class of the plugin.
 *
 * @since 4.3.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Flows {

	/**
	 * The main page name for our admin page
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private $page_name;

	/**
	 * The already loaded data for flow common tags
	 *
	 * @var array
	 * @since 4.3.0
	 */
	private $flow_common_tags_cache = array();

	/**
	 * The already loaded data for flow common tags values
	 *
	 * @var array
	 * @since 4.3.0
	 */
	private $flow_common_tags_cache_value = array();

	/**
	 * A buffer that collects the flow to execute it
	 * accordingly after the trigger was fired.
	 *
	 * @var array
	 * @since 4.3.0
	 */
	private $flow_buffer = array();

	/**
	 * Init everything
	 */
	public function __construct() {
		$this->page_name = WPWHPRO()->settings->get_page_name();

		//Logs
		$this->logs_table_data = WPWHPRO()->settings->get_log_table_data();

		//Flows
		$this->flows_table_data = WPWHPRO()->settings->get_flows_table_data();
		$this->cache_flows = array();
		$this->cache_flows_count = 0;
		$this->table_exists = false;

		//Flows logs
		$this->flow_logs_table_data = WPWHPRO()->settings->get_flow_logs_table_data();
		$this->cache_flow_logs = array();
		$this->cache_flow_logs_count = 0;
		$this->flow_logs_table_exists = false;

		//Async process
		$this->flow_async_class = null;
	}

	/**
	 * Wether the flows feature is active or not
	 *
	 * Authentication will now be active by default
	 *
	 * @deprecated deprecated since version 4.0.0
	 * @return boolean - True if active, false if not
	 */
	public function is_active() {
		return true;
	}

	/**
	 * Execute feature related hooks and logic to get
	 * everything running
	 *
	 * @since 4.3.0
	 * @return void
	 */
	public function execute() {

		//Execute Scheduled Flows
		add_action( 'wpwh_schedule_run_flow_actions_callback', array( $this, 'wpwh_schedule_run_flow_actions_callback' ), 10, 1 );

		add_action( 'wp_ajax_ironikus_flows_handler',  array( $this, 'ironikus_flows_handler' ) );

		//execute single actions
		add_filter( 'wpwhpro/async/process/wpwh_execute_flow',  array( $this, 'wpwh_execute_flow_callback' ), 20, 2 );
		add_action( 'wpwhpro/async/process/completed/partial/wpwh_execute_flow',  array( $this, 'wpwh_execute_flow_completed_callback' ), 20 ); //Make sure partial executions get the correct treatment
		add_action( 'wpwhpro/async/process/completed/wpwh_execute_flow',  array( $this, 'wpwh_execute_flow_completed_callback' ), 20 );

		//Execute flows
		add_action( 'wpwhpro/admin/webhooks/webhook_trigger_sent', array( $this, 'register_buffered_flows' ), 20 );

		//Clean possible temp actions
		$this->clean_abandoned_temp_actions();

		//Load async handler
		add_action( 'plugins_loaded',  array( $this, 'load_flow_async_class' ) );

		//Maybe create a flow export
		add_action( 'admin_init', array( $this, 'maybe_export_flow' ), 20 );

	}

	public function register_buffered_flows( $response ){

		if( ! empty( $this->flow_buffer ) ){
			foreach( $this->flow_buffer as $flow_id => $flows ){
				if( ! empty( $flows ) ){
					foreach( $flows as $flow_data ){
						WPWHPRO()->flows->run_flow( $flow_id, $flow_data );
					}
				}
			}

			//Reset Buffer
			$this->flow_buffer = array();
		}

	}

	/**
	 * Get the flow async class
	 *
	 * @since 5.0
	 *
	 * @return WP_Webhooks_Pro_Async_Process
	 */
	public function get_flow_async(){

		if( $this->flow_async_class === null ){
			$this->load_flow_async_class();
		}

		return $this->flow_async_class;
	}

	/**
	 * Get the customized list class for the Flows
	 *
	 * @since 6.1.0
	 *
	 * @return WP_Webhooks_Pro_WP_List_Table
	 */
	public function get_flow_lists_table_class(){

		$args = array(
			'labels' => array(
				'singular' => __( 'flow', 'wp-webhooks' ),
				'plural' => __( 'flows', 'wp-webhooks' ),
				'search_placeholder' => __( 'ID/Trigger/Name...', 'wp-webhooks' ),
			),
			'columns' => array(
				'id' => array(
					'label' => __( 'ID + Flow name', 'wp-webhooks' ),
					'callback' => array( $this, 'flows_lists_cb_title' ),
					'actions_callback' => array( $this, 'flows_lists_cb_title_actions' ),
					'sortable' => 'ASC',
				),
				'trigger' => array(
					'label' => __( 'Trigger', 'wp-webhooks' ),
					'callback' => array( $this, 'flows_lists_cb_trigger' ),
					'sortable' => 'ASC',
				),
				'status' => array(
					'label' => __( 'Status', 'wp-webhooks' ),
					'callback' => array( $this, 'flows_lists_cb_status' ),
					'sortable' => 'ASC',
				),
				'date' => array(
					'label' => __( 'Created', 'wp-webhooks' ),
					'callback' => array( $this, 'flows_lists_cb_date' ),
					'sortable' => 'ASC',
				),
			),
			'settings' => array(
				'per_page' => 20,
				'default_order_by' => 'id',
				'default_order' => 'DESC',
				'show_search' => true,
			),
			'item_filter' => array( $this, 'flows_lists_filter_items' ),
		);

		$table = WPWHPRO()->lists->new_list( $args );

		return $table;
	}

	/**
	 * The callback for the flows list table title
	 *
	 * @since 6.1.0
	 * @param object $item
	 * @param string $column_name
	 * @param array $column
	 * @return string
	 */
	public function flows_lists_cb_title( $item, $column_name, $column ){
		$content = '';
		$current_url = WPWHPRO()->helpers->get_current_url( false, true );
		$title = '#' . $item->id . ' - ' . $item->flow_title;
		$edit_link = WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'flow_id' => $item->id, ) ) );

		$content = sprintf(
			'<a class="row-title" href="%1$s" aria-label="%2$s">%3$s</a>',
			esc_url( $edit_link ),
			esc_attr( sprintf(
				__( 'Edit &#8220;%s&#8221;', 'wp-webhooks' ),
				$title
			) ),
			esc_html( $title )
		);

		$content = sprintf( '<strong>%s</strong>', $content );

		return $content;
	}

	/**
	 * The callback for the title item of the Flows list
	 *
	 * @since 6.1.0
	 * @param object $item
	 * @param string $column_name
	 * @param bool $primary
	 * @param array $column
	 * @return array
	 */
	public function flows_lists_cb_title_actions( $item, $column_name, $primary, $column ){

		$current_url = WPWHPRO()->helpers->get_current_url( false, true );
		$edit_url = WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'flow_id' => $item->id, ) ) );
		$export_url = WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'create_flow_export' => '1', 'flow_id' => $item->id, ) ) );
		$duplicate_title = __( 'Duplicate', 'wp-webhooks' );
		$export_title = __( 'Export', 'wp-webhooks' );
		$edit_title = __( 'Edit', 'wp-webhooks' );
		$delete_title = __( 'Delete', 'wp-webhooks' );

		$actions = array(
			'edit' => WPWHPRO()->helpers->create_link( 
				$edit_url, 
				$edit_title,
				array(
					'title' => $edit_title,
				)
			),
			'export' => WPWHPRO()->helpers->create_link( 
				$export_url, 
				$export_title,
				array(
					'class' => 'text-success wpwh-export-flow-template',
					'title' => $export_title,
					'data-tippy' => '',
					'data-wpwh-template-id' => $item->id,
					'data-tippy-content' => __( 'This will export this flow. Authentication templates & Data Mapping templates are not exported.', 'wp-webhooks' ),
				)
			),
			'duplicate' => WPWHPRO()->helpers->create_link( 
				'', 
				$duplicate_title,
				array(
					'class' => 'text-success wpwh-duplicate-flow-template',
					'title' => $duplicate_title,
					'data-tippy' => '',
					'data-wpwh-template-id' => $item->id,
					'data-tippy-content' => __( 'This causes the whole flow to be duplicated along with its settings.', 'wp-webhooks' ),
				)
			),
			'delete' => WPWHPRO()->helpers->create_link( 
				'', 
				$delete_title,
				array(
					'class' => 'text-error wpwh-delete-flow-template',
					'title' => $delete_title,
					'data-wpwh-auth-id' => $item->id,
				)
			),
		);

		return $actions;
	}

	/**
	 * The callback for the flows list table trigger
	 *
	 * @since 6.1.0
	 * @param object $item
	 * @param string $column_name
	 * @param array $column
	 * @return string
	 */
	public function flows_lists_cb_trigger( $item, $column_name, $column ){
		$content = '';

		if( isset( $item->flow_trigger ) ){
			$content = sanitize_title( $item->flow_trigger );
		}

		return $content;
	}

	/**
	 * The callback for the flows list table status
	 *
	 * @since 6.1.0
	 * @param object $item
	 * @param string $column_name
	 * @param array $column
	 * @return string
	 */
	public function flows_lists_cb_status( $item, $column_name, $column ){
		$content = '';

		if( isset( $item->flow_status ) ){

			$status_labels = WPWHPRO()->settings->get_flow_status_labels();
			$status_validated = sanitize_title( $item->flow_status );
			if( isset( $status_labels[ $status_validated ] ) ){
				$content = $status_labels[ $status_validated ];
			}
			
		}

		return $content;
	}

	/**
	 * The callback for the flows list table date
	 *
	 * @since 6.1.0
	 * @param object $item
	 * @param string $column_name
	 * @param array $column
	 * @return string
	 */
	public function flows_lists_cb_date( $item, $column_name, $column ){
		return WPWHPRO()->helpers->get_formatted_date( $item->flow_date, 'F j, Y, g:i a' );
	}

	/**
	 * The callback with query arguments to filter the flow logs
	 * 
	 * @since 6.1.0	
	 * @param array $args
	 * @return void
	 */
	public function flows_lists_filter_items( $args ){
		$item_data = array(
			'items' => array(),
			'total' => 0,
		);

		$query_data = $this->flows_query( $args );

		$item_data = array_merge( $item_data, $query_data );

		return $item_data;
	}

	/**
	 * Get the customized list class for the Flow logs
	 *
	 * @since 6.1.0
	 *
	 * @return WP_Webhooks_Pro_WP_List_Table
	 */
	public function get_flow_logs_lists_table_class(){

		$args = array(
			'labels' => array(
				'singular' => __( 'flow log', 'wp-webhooks' ),
				'plural' => __( 'flow logs', 'wp-webhooks' ),
				'search_placeholder' => __( 'Flow/Log ID...', 'wp-webhooks' ),
			),
			'columns' => array(
				'id' => array(
					'label' => __( 'Log ID + Flow name', 'wp-webhooks' ),
					'callback' => array( $this, 'flow_logs_lists_cb_title' ),
					'actions_callback' => array( $this, 'flow_logs_lists_cb_title_actions' ),
					'sortable' => 'ASC',
				),
				'trigger' => array(
					'label' => __( 'Trigger', 'wp-webhooks' ),
					'callback' => array( $this, 'flow_logs_lists_cb_trigger' ),
				),
				'completed' => array(
					'label' => __( 'Completed', 'wp-webhooks' ),
					'callback' => array( $this, 'lists_cb_completed' ),
					'sortable' => 'ASC',
				),
				'date' => array(
					'label' => __( 'Fired at', 'wp-webhooks' ),
					'callback' => array( $this, 'flow_logs_lists_cb_date' ),
					'sortable' => 'ASC',
				),
			),
			'settings' => array(
				'per_page' => 20,
				'show_search' => true,
			),
			'item_filter' => array( $this, 'flow_logs_lists_filter_items' ),
		);

		$table = WPWHPRO()->lists->new_list( $args );

		return $table;
	}

	/**
	 * The callback for the flow logs list table title
	 *
	 * @since 6.1.0
	 * @param object $item
	 * @param string $column_name
	 * @param array $column
	 * @return string
	 */
	public function flow_logs_lists_cb_title( $item, $column_name, $column ){
		$content = '';
		$flow_id = $item->flow_id;
		$current_url = WPWHPRO()->helpers->get_current_url( false, true );
		$flow = $this->get_flows( array( 'template' => $flow_id ) );
		$title = '#' . $item->id . ' - ' . $flow->flow_title;
		$edit_link = WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'wpwhpro_flow_log' => $item->id, ) ) );

		$content = sprintf(
			'<a class="row-title" href="%1$s" aria-label="%2$s">%3$s</a>',
			esc_url( $edit_link ),
			esc_attr( sprintf(
				__( 'Edit &#8220;%s&#8221;', 'wp-webhooks' ),
				$title
			) ),
			esc_html( $title )
		);

		$content = sprintf( '<strong>%s</strong>', $content );

		return $content;
	}

	/**
	 * The callback for the title item of the Flow logs list
	 *
	 * @since 6.1.0
	 * @param object $item
	 * @param string $column_name
	 * @param bool $primary
	 * @param array $column
	 * @return array
	 */
	public function flow_logs_lists_cb_title_actions( $item, $column_name, $primary, $column ){

		$current_url = WPWHPRO()->helpers->get_current_url( false, true );
		$edit_url = WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'wpwhpro_flow_log' => $item->id, ) ) );
		$is_completed = ( $item->flow_completed ) ? true : false;
		$details_title = __( 'Details', 'wp-webhooks' );
		$resend_title = __( 'Resend', 'wp-webhooks' );
		$retry_title = __( 'Retry', 'wp-webhooks' );

		$actions = array(
			'details' => WPWHPRO()->helpers->create_link( 
				$edit_url, 
				$details_title,
				array(
					'title' => $details_title,
					'data-tippy' => '',
					'data-tippy-content' => $details_title,
				)
			),
			'resend-flow' => WPWHPRO()->helpers->create_link( 
				'', 
				$resend_title,
				array(
					'class' => 'text-success',
					'data-wpwh-event' => 'demo',
					'data-wpwh-event-type' => 'flow-send',
					'title' => $resend_title,
					'data-tippy' => '',
					'data-tippy-content' => __( 'Resend the flow with the given trigger data', 'wp-webhooks' ),
					'data-wpwh-flow-log-id' => $item->id,
				)
			),
		);

		//Maybe add the retry action in case the flow is not complete
		if( ! $is_completed ){
			$actions['retry'] = WPWHPRO()->helpers->create_link( 
				'', 
				$retry_title,
				array(
					'class' => 'text-success',
					'data-wpwh-event' => 'retry',
					'data-wpwh-event-type' => 'flow-retry',
					'title' => $retry_title,
					'data-tippy' => '',
					'data-tippy-content' => __( 'Retry the execution from the first cancelled action step.', 'wp-webhooks' ),
					'data-wpwh-flow-log-id' => $item->id,
				)
				);
		}

		return $actions;
	}

	/**
	 * The callback for the flow logs list table trigger
	 *
	 * @since 6.1.0
	 * @param object $item
	 * @param string $column_name
	 * @param array $column
	 * @return string
	 */
	public function flow_logs_lists_cb_trigger( $item, $column_name, $column ){
		$content = '';
		$flow_id = $item->flow_id;
		$flow = $this->get_flows( array( 'template' => $flow_id ) );

		$content = sanitize_title( $flow->flow_trigger );

		return $content;
	}

	/**
	 * The callback for the flow logs list table completed
	 *
	 * @since 6.1.0
	 * @param object $item
	 * @param string $column_name
	 * @param array $column
	 * @return string
	 */
	public function lists_cb_completed( $item, $column_name, $column ){
		$content = '';

		if( isset( $item->flow_completed ) && ! empty( $item->flow_completed ) ){
			$content = '<img src="' . WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/check.svg" />';
		}

		return $content;
	}

	/**
	 * The callback for the flow logs list table date
	 *
	 * @since 6.1.0
	 * @param object $item
	 * @param string $column_name
	 * @param array $column
	 * @return string
	 */
	public function flow_logs_lists_cb_date( $item, $column_name, $column ){
		return WPWHPRO()->helpers->get_formatted_date( $item->flow_date, 'F j, Y, g:i a' );
	}

	/**
	 * The callback with query arguments to filter the flow logs
	 * 
	 * @since 6.1.0	
	 * @param array $args
	 * @return void
	 */
	public function flow_logs_lists_filter_items( $args ){
		$item_data = array(
			'items' => array(),
			'total' => 0,
		);

		$query_data = $this->flows_logs_query( $args );

		$item_data = array_merge( $item_data, $query_data );

		return $item_data;
	}

	/**
	 * Initiate the Flow async class
	 *
	 * @since 5.0
	 *
	 * @return void
	 */
	public function load_flow_async_class(){
		$this->flow_async_class = WPWHPRO()->async->new_process( array(
			'action' => 'wpwh_execute_flow'
		) );
	}

	/**
	 * Maybe export a given flow
	 *
	 * @since 6.1.2
	 * @return void
	 */
	public function maybe_export_flow(){
		
		if( ! isset( $_GET['create_flow_export'] ) || ! isset( $_GET['flow_id'] ) ){
			return;
		}

		if( ! WPWHPRO()->helpers->is_page( WPWHPRO()->settings->get_page_name() ) ){
			return;
		}

		if( ! WPWHPRO()->helpers->current_user_can( WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-flows-export-flow' ), 'wpwhpro-page-flows-export-flow' ) ){
			return;
		}

		$flow_id = intval( $_GET['flow_id'] );
		$flow = $this->get_flows( array( 'template' => $flow_id ) );

		if( empty( $flow ) ){
			return;
		}

		$file_name = 'wpwh-flow-' . $flow_id . '-v' . str_replace( '.', '-', WPWHPRO_VERSION ) . '-' . date( 'Y-m-d-H-i-s' ) . '.txt';
		$flow_content = json_encode( $flow );

		$stream_args = array(
			'headers' => array(
				'Content-Description' 	=> 'File Transfer',
				'Content-Disposition' 	=> 'attachment; filename=' . $file_name,
				'Content-type' 			=> 'text/plain; charset=utf-8',
				'Content-Length' 		=> strlen( $flow_content ),
			),
			'content' => $flow_content
		);

		WPWHPRO()->helpers->stream_file( $stream_args );

	}

	public function wpwh_execute_flow_callback( $data, $class ){
		$return = $data;

		if( ! isset( $data['data']['action'] ) ){
			$data['data']['action'] = array();
		}

		//Initialize retry as false to only renew it once we set that data
		if( ! isset( $data['retry'] ) ){
			$data['retry'] = false;
		}

		$skip_scheduling = false;
		if( isset( $data['skip_scheduling'] ) && $data['skip_scheduling'] ){
			$skip_scheduling = true;
		}

		$flow_id = 0;
		if( isset( $data['flow_id'] ) ){
			$flow_id = intval( $data['flow_id'] );
		}

		$flow_log_id = 0;
		$flow_log = false;
		if( isset( $data['flow_log_id'] ) ){
			$flow_log_id = intval( $data['flow_log_id'] );
			$flow_log = $this->get_flow_log( $flow_log_id, false );
		}

		//Maybe set the flow log ID to correctly handle delayed completions
        if( ! empty( $flow_log_id ) ){
            $return['merge_class_data'] = array(
                'flow_log_ids' => array( $flow_log_id ),
            );
        }

		//If no id is given, abort with the next task
		if( empty( $flow_id ) || empty( $flow_log ) ){
			return $return;
		}

		$trigger_slug = '';
		if(
			isset( $flow_log->flow_config )
			&& isset( $flow_log->flow_config['triggers'] )
		){
			foreach( $flow_log->flow_config['triggers'] as $trigger_key => $trigger_data ){
				if( isset( $trigger_data['trigger'] ) ){
					$trigger_slug = sanitize_title( $trigger_data['trigger'] );
				}
				break;
			}
		}

		$current_action_key = $data['current'];
		$current_action_data = array();
		$validated_body = null;
		$is_action_valid = true;
		$invalid_action_action = 'skip';
		if(
			isset( $flow_log->flow_config )
			&& isset( $flow_log->flow_config['actions'])
			&& isset( $flow_log->flow_config['actions'][ $current_action_key ] )
			&& isset( $flow_log->flow_config['actions'][ $current_action_key ]['action'] )
			&& isset( $flow_log->flow_payload )
		){
			$current_action = $flow_log->flow_config['actions'][ $current_action_key ]['action'];
			$current_action_data = $flow_log->flow_config['actions'][ $current_action_key ];
			if( isset( $current_action_data['fields'] ) ){
				$validated_body = $this->validate_action_fields( $current_action_data['fields'], $flow_log->flow_payload );
			}

			//Check conditionals before the action
			if(
				isset( $current_action_data['conditionals'] )
				&& ! empty( $current_action_data['conditionals'] )
				&& isset( $current_action_data['conditionals']['conditions'] )
				&& ! empty( $current_action_data['conditionals']['conditions'] )
			){
				$is_action_valid = $this->validate_action_conditions( $current_action_data['conditionals'], $flow_log->flow_payload );

				if( isset( $current_action_data['conditionals']['flowAction'] ) ){
					if( ! $is_action_valid && $current_action_data['conditionals']['flowAction'] === 'stop' ){
						$invalid_action_action = 'stop';
					}
				}
			}

		}

		//Append new data to payload
		$payload = $flow_log->flow_payload;
			
		if( ! isset( $payload['actions'] ) ){
			$payload['actions'] = array();
		}

		if( $is_action_valid && $validated_body !== null && ! empty( $current_action ) ){

			$endpoint_url	= '';
			$webhook_action = $this->get_flow_action_url_name( $flow_id, $current_action_key );
			$webhook        = WPWHPRO()->webhook->get_hooks( 'action', $current_action, $webhook_action );

			if( is_array( $webhook ) ){
				if( isset( $webhook['api_key'] ) && isset( $webhook['webhook_name'] ) ){
					$query_params = array(
						'action' => sanitize_title( $current_action ),
						'flow_log_id' => $flow_log_id,
						'block_trigger' => $trigger_slug,
					);

					if( $skip_scheduling ){
						$query_params['skip_scheduling'] = 'yes';
					}

					$endpoint_url = WPWHPRO()->webhook->built_url( $webhook['webhook_url_name'], $webhook['api_key'], $query_params );
				}
			}

			$http_args = array(
				'headers'	=> array(
					'Content-Type' => 'application/json'
				),
				'body'		=> ( is_array( $validated_body ) || is_object( $validated_body ) ) ? json_encode( $validated_body ) : $validated_body,
				'blocking'	=> true,
				'timeout'	=> 60,
				'sslverify'	=> false,
				'reject_unsafe_urls' => false,
			);

			$http_args = apply_filters( 'wpwhpro/flows/fire_flow/http_args', $http_args, $endpoint_url, $webhook );
			$endpoint_url = apply_filters( 'wpwhpro/flows/fire_flow/endpoint_url', $endpoint_url, $http_args, $webhook );

			$action_response = wp_remote_post( $endpoint_url, $http_args );	
			if( ! empty( $action_response ) && ! is_wp_error( $action_response ) ){

				$action_body = wp_remote_retrieve_body( $action_response );

				if( is_string( $action_body ) && WPWHPRO()->helpers->is_json( $action_body ) ){
					$action_body = json_decode( $action_body, true );
				}

				//Assign the new payload
				$payload['actions'][ $current_action_key ] = array(
					'success' => true,
					'msg' => __( 'The action was successfully executed.', 'wp-webhooks' ),
					'wpwh_status' => 'ok',
					'timestamp' => time(),
					'wpwh_payload' => $action_body
				);

				//Check conditionals after action
				$is_after_action_valid = true;
				if(
					! empty( $current_action_data ) 
					&& isset( $current_action_data['conditionals_after'] )
					&& ! empty( $current_action_data['conditionals_after'] )
					&& isset( $current_action_data['conditionals_after']['conditions'] )
					&& ! empty( $current_action_data['conditionals_after']['conditions'] )
				){
					$is_after_action_valid = $this->validate_action_conditions( $current_action_data['conditionals_after'], $payload );
				}

				if( $is_after_action_valid ){
					//Pause the execution if the Flow was scheduled.
					if( 
						is_array( $action_body )
						&& isset( $action_body['success'] )
						&& $action_body['success'] === true
						&& isset( $action_body['content'] )
						&& isset( $action_body['content']['wpwh_schedule'] )
						&& $action_body['content']['wpwh_schedule'] === 'action'
						&& isset( $action_body['content']['scheduled_id'] )
						&& ! empty( $action_body['content']['scheduled_id'] )
						&& isset( $action_body['content']['timestamp'] )
						&& ! empty( $action_body['content']['timestamp'] )
					){

						$timestamp = intval( $action_body['content']['timestamp'] );
						$action_id = $action_body['content']['scheduled_id'];

						if ( $action_id ) {
							try {
								ActionScheduler::store()->cancel_action( $action_id );

								ActionScheduler::logger()->log(
									$action_id,
									__( 'Action canceled by Flow for later execution.', 'wp-webhooks' )
								);
							} catch ( Exception $exception ) {
								ActionScheduler::logger()->log(
									$action_id,
									sprintf( __( 'An error occured while cancelling the action #%d. This might result in the action being fired twice.', 'wp-webhooks' ), $action_id )
								);
							}
						}

						$reminaing_batch = $class->get_remaining_batch();
						if( is_object( $reminaing_batch ) && isset( $reminaing_batch->data ) && is_array( $reminaing_batch->data ) ){

							$reminaing_batch_data = $reminaing_batch->data;

							//make sure to skip scheduling for the current action within the next execution
							foreach( $reminaing_batch_data as $data_key => $data_attributes ){
								if( isset( $data_attributes['current'] ) && $data_attributes['current'] === $current_action_key ){
									$reminaing_batch_data[ $data_key ]['skip_scheduling'] = true;
									break;
								}
							}

							$scheduled = $this->schedule_run_flow_actions( $timestamp, $reminaing_batch_data );

							//overwrite the action body with our newly scheduled entry
							$payload['actions'][ $current_action_key ]['success'] = $scheduled['success'];
							$payload['actions'][ $current_action_key ]['msg'] = $scheduled['msg'];
							$payload['actions'][ $current_action_key ]['wpwh_status'] = 'paused';
							$payload['actions'][ $current_action_key ]['data'] = $scheduled['data'];

							//Pause the flow once it was scheduled
							if( $scheduled['success'] && is_array( $return ) ){
								$return['pause'] = true;
							}
						}

					}
				} else {
					$payload['actions'][ $current_action_key ]['success'] = false;
					$payload['actions'][ $current_action_key ]['msg'] = __( 'The action was cancelled as the post-conditions did not match.', 'wp-webhooks' );
					$payload['actions'][ $current_action_key ]['wpwh_status'] = 'cancelled';

					//If the conditions did not match, the flow will be cancelled
					if( is_array( $return ) ){
						$return['cancel'] = true;
					}
				}

				$update_data = array(
					'flow_payload' => $payload,
				);

				$check = $this->update_flow_log( $flow_log_id, $update_data );

			} else {

				//setup a custom response in case the request fails
				if( is_wp_error( $action_response ) ){

					$payload['actions'][ $current_action_key ] =  array(
						'success' => false,
						'msg' => __( 'An error occured while sending the internal action request.', 'wp-webhooks' ),
						'wpwh_status' => 'cancelled',
						'error' => $action_response->get_error_message(),
						'timestamp' => time(),
						'wpwh_payload' => array(),
					);

					$update_data = array(
						'flow_payload' => $payload,
					);

					$check = $this->update_flow_log( $flow_log_id, $update_data );

					//Prevent the flow from continuing
					if( is_array( $return ) ){
						$return['cancel'] = true;
					}
				}

			}
			
		} else {

			$payload['actions'][ $current_action_key ] =  array(
				'success' => false,
				'msg' => __( 'An error occured while sending the internal action request.', 'wp-webhooks' ),
				'wpwh_status' => 'cancelled',
				'timestamp' => time(),
				'wpwh_payload' => array(),
			);

			//Make sure to only check if an action did not met the conditions
			if( empty( $is_action_valid ) ){

				if( $invalid_action_action === 'stop' ){

					$payload['actions'][ $current_action_key ]['msg'] = __( 'The flow was cancelled as the pre-conditions did not match.', 'wp-webhooks' );

					if( is_array( $return ) ){
						$return['cancel'] = true;
					}
				} else {
					//We consider the action successful in case it was skipped to let the flow contiinue
					$payload['actions'][ $current_action_key ]['success'] = true;
					$payload['actions'][ $current_action_key ]['msg'] = __( 'This action was skipped as the pre-conditions did not match.', 'wp-webhooks' );
					$payload['actions'][ $current_action_key ]['wpwh_status'] = 'ok';
				}
				
			} else {

				//In other cases, we cancel the flow
				if( is_array( $return ) ){
					$return['cancel'] = true;
				}

			}

			$update_data = array(
				'flow_payload' => $payload,
			);

			$check = $this->update_flow_log( $flow_log_id, $update_data );
		}

		return $return;
	}

	public function wpwh_execute_flow_completed_callback( $class ){

		$flow_log_ids = ( isset(  $class->flow_log_ids ) && is_array( $class->flow_log_ids ) ) ?  $class->flow_log_ids : array();

		//Backward compatibility pre 5.2.2
		if( isset( $class->flow_log_id ) ){
			$flow_log_ids[] = $class->flow_log_id;
		}

		if( ! empty( $flow_log_ids ) ){

			foreach( $flow_log_ids as $flow_log_id ){
				
				$flow_log_id = intval( $flow_log_id );
				$was_successful = true;
				$flow_log = $this->get_flow_log( $flow_log_id, false );

				if(
					is_object( $flow_log )
					&& isset( $flow_log->flow_payload )
					&& isset( $flow_log->flow_payload['actions'] )
				){
					foreach( $flow_log->flow_payload['actions'] as $action_key => $payload_data ){

						 //make sure we follow the notation introduced in 6.1.0
						if( 
							is_array( $payload_data )
							&& isset( $payload_data['wpwh_payload'] )
							&& isset( $payload_data['wpwh_status'] )
							&& (
								$payload_data['wpwh_status'] === 'cancelled'
								|| $payload_data['wpwh_status'] === 'paused' //Since 6.1.5
								)
						){

							//Make sure we prevent the flow from completing if an error was given
							$was_successful = false;
							break;
						}
						
					}
				}

				if( $was_successful ){
					$update_data = array(
						'flow_completed' => 1,
					);
		
					$this->update_flow_log( $flow_log_id, $update_data );
				}
				
			}
			
		}

	}

	/**
	 * Handle the callback of a scheduled Flow
	 *
	 * @param integer $flow_id
	 * @param array $args
	 * @return void
	 */
	public function wpwh_schedule_run_flow_actions_callback( $validated_actions = array() ){
		
		if( ! empty( $validated_actions ) ){
			$response = $this->run_flow_actions( $validated_actions );
		}

		do_action( 'wpwhpro/flows/wpwh_schedule_run_flow_actions_callback', $response, $validated_actions );
	}

	/**
	 * Manage WP Webhooks flows
	 *
	 * @return void
	 */
	public function ironikus_flows_handler() {
		check_ajax_referer( md5( $this->page_name ), 'ironikusflows_nonce' );

		$flow_handler = isset( $_REQUEST['handler'] ) ? sanitize_title( $_REQUEST['handler'] ) : '';
		$response = array( 'success' => false );

		if ( empty( $flow_handler ) ) {
			$response['msg'] = __( 'There was an issue localizing the remote data', 'wp-webhooks' );
			return $response;
		}

		$request = WPWHPRO()->http->get_current_request();

		$request_data = array();
		if( is_array( $request ) && isset( $request['content'] ) && is_array( $request ) ){
			$request_data = $request['content'];
		}

		switch( $flow_handler ) {
			case 'get_flows':
				$response['msg'] = __( 'Flows have been successfully returned.', 'wp-webhooks' );
				break;
			case 'get_flow_condition_labels':
				$response = $this->ajax_get_flow_condition_labels( $request_data );
				break;
			case 'get_flow_status_labels':
				$response = $this->ajax_get_flow_status_labels( $request_data );
				break;
			case 'get_flow':
				$response = $this->ajax_get_flow( $request_data );
				break;
			case 'update_flow':
				$response = $this->ajax_update_flow( $request_data );
				break;
			case 'delete_flow':
				$response = $this->ajax_delete_flow( $request_data );
				break;
			case 'duplicate_flow':
				$response = $this->ajax_duplicate_flow( $request_data );
				break;
			case 'get_logs':
				$response = $this->ajax_get_trigger_logs( $request_data );
				break;
			case 'get_integrations':
				$response = $this->ajax_get_integrations( $request_data );
				break;
			case 'get_triggers':
				$response = $this->ajax_get_triggers( $request_data );
				break;
				break;
			case 'get_flow_common_tags':
				$response = $this->ajax_get_flow_common_tags( $request_data );
				break;
			case 'fire_action':
				$response = $this->ajax_fire_step_action( $request_data );
				break;
			case 'get_receivable_trigger_url':
				$response = $this->ajax_get_receivable_trigger_url( $request_data );
				break;
			case 'get_field_query':
				$response = $this->ajax_get_field_query( $request_data );
				break;
		}

		if( $response['success'] ){
			wp_send_json_success( $response );
		} else {
			wp_send_json_error( $response );
		}

		die();
	}

	/**
	 * AJAX: Get flow status labels
	 *
	 * @return void
	 */
	private function ajax_get_flow_condition_labels( $request_data ) {

		$response = array(
			'success' => false,
			'msg' => __( 'No conditionals available.', 'wp-webhooks' ),
		);

		$condition_labels = WPWHPRO()->settings->get_flow_condition_labels();
		$condition_actions_labels = WPWHPRO()->settings->get_flow_condition_action_labels();

		if( ! empty( $condition_labels ) ){
			$response['success'] = true;
			$response['result'] = array(
				'conditionals' => $condition_labels,
				'conditional_actions' => $condition_actions_labels,
			);
			$response['msg'] = __( 'The condition labels have been successfully returned.', 'wp-webhooks' );
		} else {
			$response['msg'] = __( 'An error occured while returning the condition labels.', 'wp-webhooks' );
		}

		return $response;
	}

	/**
	 * AJAX: Get flow status labels
	 *
	 * @return void
	 */
	private function ajax_get_flow_status_labels( $request_data ) {

		$response = array(
			'success' => false,
			'msg' => __( 'No labels available.', 'wp-webhooks' ),
		);

		$status_labels = WPWHPRO()->settings->get_flow_status_labels();

		if( ! empty( $status_labels ) ){
			$response['success'] = true;
			$response['result'] = $status_labels;
			$response['msg'] = __( 'The flow status labels have been successfully returned.', 'wp-webhooks' );
		} else {
			$response['msg'] = __( 'An error occured while returning the flow status labels.', 'wp-webhooks' );
		}

		return $response;
	}

	/**
	 * AJAX: Get flow
	 *
	 * @return void
	 */
	private function ajax_get_flow( $request_data ) {

		$response = array(
			'success' => false,
			'msg' => __( 'Nothing happened.', 'wp-webhooks' ),
		);
		$flow_id = isset( $request_data['flow_id'] ) ? intval( $request_data['flow_id'] ) : 0;

		if( ! empty( $flow_id ) ){

			$flow = $this->get_flows( array( 'template' => $flow_id ) );

			if( is_object( $flow ) && isset( $flow->flow_date ) ){
				$flow->flow_date = date( 'M j, Y, g:i a', strtotime( $flow->flow_date ) );
			}

			if( ! empty( $flow ) ){
				$response['success'] = true;
				$response['result'] = $flow;
				$response['msg'] = __( 'The flow was successfully returned.', 'wp-webhooks' );

				return $response;
			}

		}

		$response['msg'] = __( 'An error occured while returning the flow.', 'wp-webhooks' );
		return $response;
	}

	/**
	 * AJAX: Update flow
	 *
	 * @return void
	 */
	private function ajax_update_flow( $request_data ) {

		$response = array(
			'success' => false,
			'msg' => __( 'Nothing happened.', 'wp-webhooks' ),
		);

		$flow_id = isset( $request_data['flow_id'] ) ? intval( $request_data['flow_id'] ) : 0;
		$flow_title = isset( $request_data['flow_title'] ) ? wp_strip_all_tags( sanitize_text_field( $request_data['flow_title'] ) ) : false;
		$flow_name = isset( $request_data['flow_name'] ) ? sanitize_title( $request_data['flow_name'] ) : sanitize_title( $flow_title );
		$flow_trigger = isset( $request_data['flow_trigger'] ) ? $request_data['flow_trigger'] : '';
		$flow_config = isset( $request_data['flow_config'] ) ? $request_data['flow_config'] : false;
		$flow_status = isset( $request_data['flow_status'] ) ? sanitize_title( $request_data['flow_status'] ) : 'inactive';
		$flow_author = isset( $request_data['flow_author'] ) ? intval( $request_data['flow_author'] ) : 0;

		//validate flow status
		$status_labels = WPWHPRO()->settings->get_flow_status_labels();
		if( ! isset( $status_labels[ $flow_status ] ) ){
			$flow_status = 'inactive';
		}

		if( ! empty( $flow_config ) && is_array( $flow_config ) ){
			//$flow_config = $this->validate_flow_values( 'stripslashes', $flow_config );	
		}

		if( ! empty( $flow_id ) ){

			$flow = $this->update_flow( $flow_id, array(
				'flow_title' => $flow_title,
				'flow_name' => $flow_name,
				'flow_config' => $flow_config,
				'flow_trigger' => $flow_trigger,
				'flow_status' => $flow_status,
				'flow_author' => $flow_author,
			  ) );

			if( ! empty( $flow ) ){
				$response['success'] = true;
				$response['result'] = $flow;
				$response['msg'] = __( 'The flow was successfully updated.', 'wp-webhooks' );
				$response['flow_config'] = $flow_config;
				return $response;
			}

			$response['flow'] = $flow;

		}

		$response['flow_id'] = $flow_id;

		$response['msg'] = __( 'An error occured while updating the flow.', 'wp-webhooks' );
		return $response;
	}

	/**
	 * AJAX: Delete flow
	 *
	 * @return void
	 */
	private function ajax_delete_flow( $request_data ) {

		$response = array(
			'success' => false,
			'msg' => __( 'Nothing happened.', 'wp-webhooks' ),
		);

		$flow_id = isset( $request_data['flow_id'] ) ? intval( $request_data['flow_id'] ) : 0;

		if( ! empty( $flow_id ) ){

			$flow = $this->delete_flow( $flow_id );

			if( ! empty( $flow ) ){
				$response['success'] = true;
				$response['result'] = $flow;
				$response['msg'] = __( 'The flow was successfully deleted.', 'wp-webhooks' );

				return $response;
			}

		}

		$response['msg'] = __( 'An error occured while deleting the flow.', 'wp-webhooks' );

		return $response;
	}

	/**
	 * AJAX: Duplicate flow
	 *
	 * @return void
	 */
	private function ajax_duplicate_flow( $request_data ) {

		$response = array(
			'success' => false,
			'msg' => __( 'Nothing happened.', 'wp-webhooks' ),
		);

		$flow_id = isset( $request_data['flow_id'] ) ? intval( $request_data['flow_id'] ) : 0;

		if( ! empty( $flow_id ) ){

			$flow = $this->get_flows( array( 'template' => $flow_id ) );

			//Remove the predefined ID
			if( isset( $flow->id ) ){
				unset( $flow->id );
			}

			//Set the status to inactive
			if( isset( $flow->flow_status ) ){
				$flow->flow_status = 'inactive';
			}

			//Alter title
			if( isset( $flow->flow_title ) ){
				$flow->flow_title .= __( ' (copy)', 'wp-webhooks' );
			}

			if( ! empty( $flow ) ){

				//Convert object to array 
				$flow_array = json_decode( json_encode( $flow ), true );

				$flow_id = $this->add_flow( $flow_array );
				if( ! empty( $flow_id ) ){

					$response['success'] = true;
					$response['flow_id'] = $flow_id;
					$response['msg'] = __( 'The flow was successfully duplicated.', 'wp-webhooks' );
					
				} else {
					$response['msg'] = __( 'An issue occured while adding the duplicated flow.', 'wp-webhooks' );
				}

			} else {
				$response['msg'] = __( 'An issue occured while fetching the flow.', 'wp-webhooks' );
			}

		}

		$response['msg'] = __( 'An error occured while duplicating the flow.', 'wp-webhooks' );

		return $response;
	}

	/**
	 * AJAX: Get logs
	 *
	 * @return void
	 */
	private function ajax_get_trigger_logs( $request_data ) {

		$response = array(
			'success' => false,
			'msg' => __( 'Nothing happened.', 'wp-webhooks' ),
		);
		$flow_id = isset( $request_data['flow_id'] ) ? intval( $request_data['flow_id'] ) : 0;
		$step_id = isset( $request_data['step_id'] ) ? intval( $request_data['step_id'] ) : 0;
		$integration = isset( $request_data['integration'] ) ? intval( $request_data['integration'] ) : 0;
		$endpoint_name = ( isset( $request_data['trigger'] ) && ! empty( $request_data['trigger'] ) ) ? sanitize_title( $request_data['trigger'] ) : false;
		$endpoint_name = ( empty( $endpoint_name ) && isset( $request_data['wpwh_action'] ) && ! empty( $request_data['wpwh_action'] ) ) ? sanitize_title( $request_data['wpwh_action'] ) : $endpoint_name;
		$webhook_request_type = isset( $request_data['wpwh_action'] ) ? 'action' : 'trigger';
		$endpoint_webhook_url_name = ( $webhook_request_type === 'trigger' ) ? $this->get_flow_trigger_url_name( $flow_id ) : $this->get_flow_action_url_name( $flow_id, $step_id );

		if( ! empty( $flow_id ) ){

			$output = array();
			$logs = WPWHPRO()->logs->get_log( 0, 100 );

			if ( ! empty( $logs ) && is_array( $logs ) ) {
				$i = 0;
				foreach ( $logs as $log  ) {
					$i++;
					$log_time = date( 'F j, Y, g:i a', strtotime( $log->log_time ) );
					$log_version = '';
					$message = htmlspecialchars( base64_decode( $log->message ) );
					$content_backend = base64_decode( $log->content );
					$identifier = '';
					$webhook_type = '';
					$webhook_name = '';
					$webhook_url_name = '';
					$endpoint_response = '';
					$content = '';

					if( WPWHPRO()->helpers->is_json( $content_backend ) ){
							$single_data = json_decode( $content_backend, true );
							if( $single_data && is_array( $single_data ) ){

								if(
									! isset( $single_data['webhook_type'] )
									|| (
										$single_data['webhook_type'] !== $webhook_request_type
										&& $single_data['webhook_type'] !== 'flow_' . $webhook_request_type
										)
								){
									continue;
								}

								if( ! isset( $single_data['webhook_name'] ) || $single_data['webhook_name'] !== $endpoint_name ){
									continue;
								}

								//Skip flows that haven't been triggered by the current URL
								if( isset( $single_data['webhook_url_name'] ) ){
									if( $single_data['webhook_url_name'] !== $endpoint_webhook_url_name ){
										continue;
									}
								}

								if( $webhook_request_type === 'trigger' ){
									if( isset( $single_data['request_data'] ) ){
										if( is_array( $single_data['request_data'] ) && isset( $single_data['request_data']['body'] ) ){
											if( WPWHPRO()->helpers->is_json( $single_data['request_data']['body'] ) ){
												$content = json_encode( json_decode( $single_data['request_data']['body'], true ), JSON_PRETTY_PRINT );
											} else {
												$content = json_encode( WPWHPRO()->logs->sanitize_array_object_values( $single_data['request_data']['body'] ), JSON_PRETTY_PRINT );
											}
	
										} else {
											$content = json_encode( WPWHPRO()->logs->sanitize_array_object_values( $single_data['request_data'] ), JSON_PRETTY_PRINT );
										}
	
									}
								} else {
									if( isset( $single_data['response_data'] ) ){
										if( is_array( $single_data['response_data'] ) && isset( $single_data['response_data']['arguments'] ) ){
											if( WPWHPRO()->helpers->is_json( $single_data['response_data']['arguments'] ) ){
												$content = json_encode( json_decode( $single_data['response_data']['arguments'], true ), JSON_PRETTY_PRINT );
											} else {
												$content = json_encode( WPWHPRO()->logs->sanitize_array_object_values( $single_data['response_data']['arguments'] ), JSON_PRETTY_PRINT );
											}
	
										} else {
											$content = json_encode( array( __( "An error occured while loading the response", 'wp-webhooks' ) ), JSON_PRETTY_PRINT );
										}
	
									}
								}
								

								if( isset( $single_data['response_data'] ) ){
									$endpoint_response = WPWHPRO()->logs->sanitize_array_object_values( $single_data['response_data'] );
								}

								if( isset( $single_data['identifier'] ) ){
									$identifier = htmlspecialchars( $single_data['identifier'] );
								}

								if( isset( $single_data['webhook_type'] ) ){
									$webhook_type = htmlspecialchars( $single_data['webhook_type'] );
								}

								if( isset( $single_data['webhook_name'] ) ){
									$webhook_name = htmlspecialchars( $single_data['webhook_name'] );
								}

								if( isset( $single_data['webhook_url_name'] ) ){
									$webhook_url_name = htmlspecialchars( $single_data['webhook_url_name'] );
								}

								if( isset( $single_data['log_version'] ) ){
									$log_version = htmlspecialchars( $single_data['log_version'] );
								}
							}
					}

					$output[] = array(
						'id' => $log->id,
						'log_time' => $log_time,
						'log_version' => $log_version,
						'message' => $message,
						'content_backend' => $content_backend,
						'identifier' => $identifier,
						'webhook_type' => $webhook_type,
						'webhook_name' => $webhook_name,
						'webhook_url_name' => $webhook_url_name,
						'endpoint_response' => $endpoint_response,
						'content' => $content,
						'title' => sprintf( __( 'Log #%1$s', 'wp-webhooks' ), $log->id) . ' - ' . $log_time,
					);

					//Make sure to break automatically after 20 entries of the given log
					if( count( $output ) >= 20 ){
						break;
					}
				}
			}

			if( ! empty( $logs ) ){
				$response['success'] = true;
				$response['result'] = $output;
				$response['msg'] = __( 'The logs were successfully returned.', 'wp-webhooks' );

				return $response;
			}

		}

		$response['msg'] = __( 'An error occured while returning the logs.', 'wp-webhooks' );

		return $response;
	}

	/**
	 * AJAX: Get Integrations
	 *
	 * @return void
	 */
	private function ajax_get_integrations( $request_data ) {

		$response = array(
			'success' => false,
			'msg' => __( 'Nothing happened.', 'wp-webhooks' ),
		);

		$integrations = WPWHPRO()->integrations->get_integrations();
		$required_trigger_settings = WPWHPRO()->settings->get_required_trigger_settings();
		$default_trigger_settings = WPWHPRO()->settings->get_default_trigger_settings();
		$receivable_trigger_settings = WPWHPRO()->settings->get_receivable_trigger_settings();
		$required_action_settings = WPWHPRO()->settings->get_required_action_settings();
		$data_mapping_templates = WPWHPRO()->data_mapping->get_data_mapping();
		
		$auth_templates = WPWHPRO()->auth->template_query( array( 'items_per_page' => 9999 ) );
		$validated_auth_templates = array();
		if( is_array( $auth_templates ) && isset( $auth_templates['items'] ) ){
			foreach( $auth_templates['items'] as $auth_template ){
				if( isset( $auth_template->id ) && isset( $auth_template->name ) ){
					$validated_auth_templates[ $auth_template->id ] = array(
						'label' => $auth_template->name,
						'value' => $auth_template->id,
					);
				}
			}
		}

		$whitelisted_required_action_settings = array(
			'wpwhpro_action_data_mapping',
			'wpwhpro_action_data_mapping_response',
			'wpwhpro_action_schedule',
		);
		$whitelisted_required_trigger_settings = array(
			'wpwhpro_trigger_data_mapping',
			'wpwhpro_trigger_data_mapping_response',
			'wpwhpro_trigger_data_mapping_cookies',
			'wpwhpro_trigger_allow_unsafe_urls',
			'wpwhpro_trigger_allow_unverified_ssl',
			'wpwhpro_trigger_single_instance_execution',
			'wpwhpro_trigger_schedule', //since 6.0
			// 'wpwhpro_trigger_demo_text', // TODO: Remove after testing
			// 'wpwhpro_trigger_demo_text_variable', // TODO: Remove after testing
			// 'wpwhpro_trigger_demo_select', // TODO: Remove after testing
			// 'wpwhpro_trigger_demo_select_def', // TODO: Remove after testing
			// 'wpwhpro_trigger_demo_select_mult', // TODO: Remove after testing
			// 'wpwhpro_trigger_demo_select_mult_def', // TODO: Remove after testing
			// 'wpwhpro_trigger_demo_checkbox', // TODO: Remove after testing
			// 'wpwhpro_trigger_demo_radio', // TODO: Remove after testing
			// 'wpwhpro_trigger_demo_textarea', // TODO: Remove after testing
			// 'wpwhpro_trigger_demo_wysiwyg', // TODO: Remove after testing
			// 'wpwhpro_trigger_demo_repeater', // TODO: Remove after testing
		);

		if ( ! empty( $integrations ) ) {
			$output = array();

			// Loop through all the ingrations.
			foreach( $integrations as $integration_slug => $integration ) {

				// Don't continue if no details are given
				if ( ! isset( $integration->details ) ) {
					continue;
				}

				$integration_details = $integration->details;
				$integration_image = isset( $integration_details['icon'] ) ? $integration_details['icon'] : WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/wp-icon.svg';
				$integration_name = isset( $integration_details['name'] ) ? $integration_details['name'] : __( 'Undefined', 'wp-webhooks' );

				$actions = WPWHPRO()->integrations->get_actions( $integration_slug );

				foreach( $actions as $ak => $ad ){

					//if( is_array( $actions[ $ak ] ) && isset( $actions[ $ak ]['description'] ) ){
					//	unset( $actions[ $ak ]['description'] );
					//}

					if( ! isset( $actions[ $ak ]['settings'] ) ){
						$actions[ $ak ]['settings'] = array();
					}

					if( ! isset( $actions[ $ak ]['settings']['data'] ) ){
						$actions[ $ak ]['settings']['data'] = array();
					}

					foreach( $required_action_settings as $settings_ident => $settings_data ){

						if( ! in_array( $settings_ident, $whitelisted_required_action_settings ) ){
							unset( $required_action_settings[ $settings_ident ] );
						}

                        if( $settings_ident == 'wpwhpro_action_data_mapping' ){
							if( isset( $required_action_settings[ $settings_ident ] ) ){
								if( ! empty( $data_mapping_templates ) ){

									if( ! is_array( $required_action_settings[ $settings_ident ]['choices'] ) ){
										$required_action_settings[ $settings_ident ]['choices'] = array();
									}

									$required_action_settings[ $settings_ident ]['choices'] = array_replace( $required_action_settings[ $settings_ident ]['choices'], WPWHPRO()->data_mapping->flatten_data_mapping_data( $data_mapping_templates ) );
								} else {
									unset( $required_action_settings[ $settings_ident ] ); //if empty
								}
							}
                        }

                        if( $settings_ident == 'wpwhpro_action_data_mapping_response' ){
							if( isset( $required_action_settings[ $settings_ident ] ) ){
								if( ! empty( $data_mapping_templates ) ){

									if( ! is_array( $required_action_settings[ $settings_ident ]['choices'] ) ){
										$required_action_settings[ $settings_ident ]['choices'] = array();
									}

									$required_action_settings[ $settings_ident ]['choices'] = array_replace( $required_action_settings[ $settings_ident ]['choices'], WPWHPRO()->data_mapping->flatten_data_mapping_data( $data_mapping_templates ) );
								} else {
									unset( $required_action_settings[ $settings_ident ] ); //if empty
								}
							}
                        }

                    }
					$actions[ $ak ]['settings']['data'] = array_merge( $actions[ $ak ]['settings']['data'], $required_action_settings );

				}

				$triggers = WPWHPRO()->integrations->get_triggers( $integration_slug );
				foreach( $triggers as $ak => $ad ){

					//always enforce array
					if( is_array( $triggers[ $ak ] ) ){
						if( ! isset( $triggers[ $ak ]['description'] ) ){
							$triggers[ $ak ]['description'] = WPWHPRO()->webhook->validate_endpoint_description_args( array() );
						} elseif( is_string( $triggers[ $ak ]['description'] ) ){
							$triggers[ $ak ]['description'] = WPWHPRO()->webhook->validate_endpoint_description_args( $triggers[ $ak ] );
						}
					}

					if( ! isset( $triggers[ $ak ]['settings'] ) ){
						$triggers[ $ak ]['settings'] = array();
					}

					if( ! isset( $triggers[ $ak ]['settings']['data'] ) ){
						$triggers[ $ak ]['settings']['data'] = array();
					}

					$required_trigger_settings_temp = $required_trigger_settings;
					foreach( $required_trigger_settings_temp as $settings_ident => $settings_data ){

						if( ! isset( $required_trigger_settings_temp[ $settings_ident ] ) ){
							continue;
						}

						if( ! in_array( $settings_ident, $whitelisted_required_trigger_settings ) ){
							unset( $required_trigger_settings_temp[ $settings_ident ] );
						}

                        if( $settings_ident == 'wpwhpro_trigger_data_mapping' ){
							if( isset( $required_trigger_settings_temp[ $settings_ident ] ) ){
								if( ! empty( $data_mapping_templates ) ){

									if( ! is_array( $required_trigger_settings_temp[ $settings_ident ]['choices'] ) ){
										$required_trigger_settings_temp[ $settings_ident ]['choices'] = array();
									}

									$required_trigger_settings_temp[ $settings_ident ]['choices'] = array_replace( $required_trigger_settings_temp[ $settings_ident ]['choices'], WPWHPRO()->data_mapping->flatten_data_mapping_data( $data_mapping_templates ) );
								} else {
									unset( $required_trigger_settings_temp[ $settings_ident ] ); //if empty
								}
							}
                        }

                        if( $settings_ident == 'wpwhpro_trigger_data_mapping_response' ){
							if( isset( $required_trigger_settings_temp[ $settings_ident ] ) ){
								if( ! empty( $data_mapping_templates ) ){

									if( ! is_array( $required_trigger_settings_temp[ $settings_ident ]['choices'] ) ){
										$required_trigger_settings_temp[ $settings_ident ]['choices'] = array();
									}

									$required_trigger_settings_temp[ $settings_ident ]['choices'] = array_replace( $required_trigger_settings_temp[ $settings_ident ]['choices'], WPWHPRO()->data_mapping->flatten_data_mapping_data( $data_mapping_templates ) );
								} else {
									unset( $required_trigger_settings_temp[ $settings_ident ] ); //if empty
								}
							}
                        }

                        if( $settings_ident == 'wpwhpro_trigger_data_mapping_cookies' ){
							if( isset( $required_trigger_settings_temp[ $settings_ident ] ) ){
								if( ! empty( $data_mapping_templates ) ){

									if( ! is_array( $required_trigger_settings_temp[ $settings_ident ]['choices'] ) ){
										$required_trigger_settings_temp[ $settings_ident ]['choices'] = array();
									}

									$required_trigger_settings_temp[ $settings_ident ]['choices'] = array_replace( $required_trigger_settings_temp[ $settings_ident ]['choices'], WPWHPRO()->data_mapping->flatten_data_mapping_data( $data_mapping_templates ) );
								} else {
									unset( $required_trigger_settings_temp[ $settings_ident ] ); //if empty
								}
							}
                        }

                    }

					if( isset( $triggers[ $ak ]['settings']['load_default_settings'] ) && $triggers[ $ak ]['settings']['load_default_settings'] === true ){
						$triggers[ $ak ]['settings']['data'] = array_merge( $triggers[ $ak ]['settings']['data'], $default_trigger_settings );
					}

					$triggers[ $ak ]['settings']['data'] = array_merge( $triggers[ $ak ]['settings']['data'], $required_trigger_settings_temp );


					if( isset( $triggers[ $ak ]['receivable_url'] ) && $triggers[ $ak ]['receivable_url'] === true ){
						$triggers[ $ak ]['settings']['data'] = array_merge( $receivable_trigger_settings, $triggers[ $ak ]['settings']['data'] );
					}

				}

				$output[] = array(
					'name' => $integration_name,
					'slug' => $integration_slug,
					'icon' => array(
						'src' => $integration_image,
						'alt' => sprintf( __( 'The logo of the %1$s integration.', 'wp-webhooks' ), $integration_name ),
					),
					'actions' => $actions,
					'triggers' => $triggers,
					'helpers' => $integration->helpers,
				);
			}

			$response['success'] = true;
			$response['result'] = $output;
			$response['msg'] = __( 'Integrations have been returned successfully.', 'wp-webhooks' );

			return $response;
		}

		$response['msg'] = __( 'No integrations have been found.', 'wp-webhooks' );

		return $response;
	}

	/**
	 * AJAX: Get Triggers
	 *
	 * @return void
	 */
	public function ajax_get_triggers( $request_data ) {

    $integration = isset( $request_data['integration'] ) ? sanitize_text_field( $request_data['integration'] ) : '';

    // Setting name.
    $triggers = WPWHPRO()->integrations->get_triggers( $integration );

    if ( ! empty( $triggers ) ) {
      $output = array();

      foreach ( $triggers as $trigger_name => $trigger_value ) {
        $output[$trigger_name] = array(
          'name' => $trigger_value['name'],
          'short_description' => $trigger_value['short_description'],
          'trigger' => $trigger_value['trigger'],
          'settings' => $trigger_value['settings'],
          'returns_code' => $trigger_value['returns_code'],
          'premium' => $trigger_value['premium']
        );
      }

	  	$response['success'] = true;
		$response['result'] = $output;
		$response['msg'] = __( 'Triggers have been returned successfully.', 'wp-webhooks' );

		return $response;
    }

	$response['msg'] = __( 'No triggers have been found.', 'wp-webhooks' );

	return $response;
  }

	/**
	 * AJAX: Get Common Flow tags
	 *
	 * @return void
	 */
  public function ajax_get_flow_common_tags( $request_data ) {

    // Setting name.
    $common_tags = WPWHPRO()->settings->get_flow_common_tags();

    if ( is_array( $common_tags ) && ! empty( $common_tags ) ) {
	  	$response['success'] = true;
			$response['result'] = $common_tags;
			$response['msg'] = __( 'Common flow tags have been returned successfully.', 'wp-webhooks' );

			return $response;
    }

		$response['msg'] = __( 'No common flow tags have been found.', 'wp-webhooks' );

		return $response;
  }

	/**
	 * AJAX: Fire step action
	 *
	 * @return void
	 */
  public function ajax_fire_step_action( $request_data ) {

		$step_data = isset( $request_data['step_data'] ) ? $request_data['step_data'] : array();
		$action = isset( $request_data['wpwh_action'] ) ? sanitize_title( $request_data['wpwh_action'] ) : '';
		$flow_id = isset( $request_data['flow_id'] ) ? intval( $request_data['flow_id'] ) : 0;
		$step_id = isset( $request_data['step_id'] ) ? sanitize_text_field( $request_data['step_id'] ) : 0;
		$response = array(
			'success' => false
		);

		if( ! empty( $flow_id ) && ! empty( $step_id ) ){
			$webhook_action = 'wpwh-flow-temp-' . intval( $flow_id ) . '-' . sanitize_title( $step_id );
			$flow = $this->get_flows( array( 'template' => $flow_id ) );
			$action_fields = array();

			//Create the temporary action URL
			$new_action_args = array(
				'flow_id' => $flow_id,
				'flow_step' => $step_id,
				'webhook_name' => $webhook_action,
			);

			if( ! empty( $step_data ) && is_array( $step_data ) ){
				//$step_data = json_decode( stripslashes( json_encode( $step_data ) ), true); //keep that documented in case fe face issues with any previous validation
				//$step_data = $this->validate_flow_values( 'stripslashes', $step_data );
			}

			if( isset( $step_data['integration'] ) ){
				$new_action_args['integration'] = $step_data['integration'];
			}

			if( isset( $step_data['fields'] ) && ! empty( $step_data['fields'] ) ){

				$new_action_args['settings'] = $step_data['fields'];

				foreach( $new_action_args['settings'] as $ssk => $ssv ){

					//Ignore empty values
					if( ! isset( $ssv['value'] ) || ! $this->is_filled_setting( $ssv['value'] ) ){
						unset( $new_action_args['settings'][ $ssk ] );
						continue;
					}

					//Add argument to action fields
					if( isset( $ssv['type'] ) && $ssv['type'] === 'argument' ){
						$action_fields[ $ssk ] = $ssv;
					}

					//Only allow settings
					if( ! isset( $ssv['type'] ) || $ssv['type'] !== 'setting' ){
						unset( $new_action_args['settings'][ $ssk ] );
						continue;
					}

					$new_action_args['settings'][ $ssk ] = $ssv['value'];
				}

			}

			//add whitelisted action
			if( ! empty( $action ) ){
				$new_action_args['settings']['wpwhpro_action_action_whitelist'] = array( $action );
			}

			//remove scheduler if given as we want to get the real response
			if( isset( $new_action_args['settings']['wpwhpro_action_schedule'] ) ){
				unset( $new_action_args['settings']['wpwhpro_action_schedule'] );
			}

			//since 5.0
			$new_action_args['webhook_group'] = $action;

			$this->create_flow_action( $new_action_args );

			//Reload webhooks
			WPWHPRO()->webhook->reload_webhooks();
			$webhook = WPWHPRO()->webhook->get_hooks( 'action', $action, $webhook_action );

			//build the action payload
			$action_payload = array(
				'trigger' => array(),
				'actions' => array(),
			);

			if( isset( $flow->flow_config->triggers ) ){
				foreach( $flow->flow_config->triggers as $single_trigger ){
					if( isset( $single_trigger->variableData ) ){
						$action_payload['trigger'] = $single_trigger->variableData;
						break;
					}
				}
			}

			if( isset( $flow->flow_config->actions ) ){
				foreach( $flow->flow_config->actions as $single_action => $single_action_data ){
					if( isset( $single_action_data->variableData ) ){
						$action_payload['actions'][ $single_action ] = $single_action_data->variableData;
					}

					//No payload data is needed after the current step
					if( $single_action === $step_id ){
						break;
					}
				}
			}

			//Make the temporary API endpoint call
			if( is_array( $webhook ) ){
				if( isset( $webhook['api_key'] ) && isset( $webhook['webhook_name'] ) ){
					$endpoint_url = WPWHPRO()->webhook->built_url( $webhook['webhook_url_name'], $webhook['api_key'], array(
						'action' => $action,
						'flow_log_id' => $flow_id,
						'block_trigger' => true,
					) );

					$validated_body = $this->validate_action_fields( $action_fields, $action_payload );

					if( ! empty( $validated_body ) && ! empty( $action ) ){

						$http_args = array(
							'headers'	=> array(
								'Content-Type' => 'application/json'
							),
							'body'		=> ( is_array( $validated_body ) || is_object( $validated_body ) ) ? json_encode( $validated_body ) : $validated_body,
							'blocking'	=> true,
							'timeout'	=> 360,
							'sslverify'	=> false,
							'reject_unsafe_urls'	=> false,
						);

						$http_args = apply_filters( 'wpwhpro/flows/fire_flow/http_args', $http_args, $endpoint_url, $webhook );
						$endpoint_url = apply_filters( 'wpwhpro/flows/fire_flow/endpoint_url', $endpoint_url, $http_args, $webhook );

						$action_response = wp_remote_post( $endpoint_url, $http_args );

						if( ! empty( $action_response ) && ! is_wp_error( $action_response ) ){

							$action_body = wp_remote_retrieve_body( $action_response );

							if( is_string( $action_body ) && WPWHPRO()->helpers->is_json( $action_body ) ){
								$action_body = json_decode( $action_body, true );
							}

							$response['success'] = true;
							$response['result'] = $action_body;
							$response['msg'] = __( 'The action was executed successfully.', 'wp-webhooks' );

							//Delete temporarily created webhook action
							$this->delete_flow_action( $new_action_args );

							return $response;
						} else {

							if( is_wp_error( $action_response ) ){

								$error_response_data = array(
									'success' => false,
								);

								$error_response_data = array_merge( $error_response_data, WPWHPRO()->http->generate_wp_error_response( $action_response ) );

								$response['success'] = true;
								$response['msg'] = __( 'The action was executed, but it returned an error.', 'wp-webhooks' );
								$response['result'] = $error_response_data;

								return $response;

							}
						}
					}
				}
			}

			//Delete temporarily created webhook action
			$this->delete_flow_action( $new_action_args );

		}

		$response['msg'] = __( 'There was an issue executing the action.', 'wp-webhooks' );

		return $response;
  }

  	public function ajax_get_receivable_trigger_url( $request_data ){

		$flow_id = isset( $request_data['flow_id'] ) ? intval( $request_data['flow_id'] ) : 0;
		$flow_trigger = isset( $request_data['trigger'] ) ? sanitize_title( $request_data['trigger'] ) : '';
		$response = array(
			'success' => false,
			'msg' => __( 'The trigger URL was not returned.', 'wp-webhooks' ),
			'settings_value' => '',
			'settings_key' => 'wpwhpro_trigger_single_receivable_url',
		);

		if( empty( $flow_id ) ){
			return $response;
		}

		$flow_trigger_url_name = $this->get_flow_trigger_url_name( $flow_id );

		if( ! empty( $flow_trigger ) ){
			$response['success'] = true;
			$response['msg'] = __( 'The trigger URL was successfully returned.', 'wp-webhooks' );
			$response['settings_value'] = WPWHPRO()->webhook->built_trigger_receivable_url( $flow_trigger, $flow_trigger_url_name );
		}

		return $response;
	}

  	public function ajax_get_field_query( $request_data ){

        $webhook_type = isset( $request_data['webhook_type'] ) ? sanitize_title( $request_data['webhook_type'] ) : '';
		$webhook_group      = isset( $request_data['webhook_group'] ) ? sanitize_text_field( $request_data['webhook_group'] ) : '';
        $webhook_integration   = ( isset( $request_data['webhook_integration'] ) && ! empty( $request_data['webhook_integration'] ) ) ? sanitize_title( $request_data['webhook_integration'] ) : '';
        $webhook_field   = ( isset( $request_data['webhook_field'] ) && ! empty( $request_data['webhook_field'] ) ) ? sanitize_title( $request_data['webhook_field'] ) : '';
        $field_search   = ( isset( $request_data['field_search'] ) && ! empty( $request_data['field_search'] ) ) ? esc_sql( $request_data['field_search'] ) : '';
        $paged = ( isset( $request_data['page'] ) && ! empty( $request_data['page'] ) ) ? intval( $request_data['page'] ) : 1;
        $selected = ( isset( $request_data['selected'] ) && ! empty( $request_data['selected'] ) ) ? $request_data['selected'] : '';
        $response           = array(
			'success' => false,
			'msg' => __( 'No items have been returned.', 'wp-webhooks' ),
			'data' => array(
				'total' => 0,
				'choices' => array(),
			)
		);
		$endpoint = null;	

		if( ! empty( $webhook_type ) && ! empty( $webhook_group ) && ! empty( $webhook_integration ) && ! empty( $webhook_field ) ){
		    switch( $webhook_type ){
				case 'action':
					$endpoint = WPWHPRO()->integrations->get_actions( $webhook_integration, $webhook_group );
					break;
				case 'trigger':
					$endpoint = WPWHPRO()->integrations->get_triggers( $webhook_integration, $webhook_group );
					break;
			}

			if( ! empty( $endpoint ) ){

				$setting = array();

				if(
					isset( $endpoint['settings']['data'] )
					&& is_array( $endpoint['settings']['data'] )
					&& isset( $endpoint['settings']['data'][ $webhook_field ] )
				){
					$setting = $endpoint['settings']['data'][ $webhook_field ];
				}

				//Make sure we also cover possible queries within action arguments
				if( empty( $setting ) && $webhook_type === 'action' ){
					if( 
						isset( $endpoint['parameter'] ) 
						&& is_array( $endpoint['parameter'] )
						&& isset( $endpoint['parameter'][ $webhook_field ] )
					){
						$setting = $endpoint['parameter'][ $webhook_field ];
					}
				}

				if( ! empty( $setting ) ){
					$query_items = WPWHPRO()->fields->get_query_items( $setting, $args = array(
						's' => $field_search,
						'paged' => $paged,
						'selected' => $selected,
					) );

					$response['data']['total'] = $query_items['total'];
					$response['data']['per_page'] = $query_items['per_page'];
					$response['data']['item_count'] = $query_items['item_count'];
					$response['data']['page'] = $paged;

					if( ! empty( $query_items ) && is_array( $query_items ) && isset( $query_items['items'] ) ){
						$response['success'] = true;

						//validate items to make them compatible with select2
						foreach( $query_items['items'] as $item_name => $item_value ){

							if( ! is_array( $item_value ) || ! isset( $item_value['label'] ) ){
								continue;
							}

							$response['data']['choices'][] = array(
								'id' => $item_value['value'],
								'text' => $item_value['label'],
							);

						}

					}
				}

			}
        }

		//set a success in case items have been given
		if( ! empty( $response['data']['choices'] ) ){
			$response['success'] = true;
			$response['msg'] = __( 'The items have been returned successfully.', 'wp-webhooks' );
		}

        return $response;
	}

  	/**
	 * ######################
	 * ###
	 * #### Flows Logs SQL table
	 * ###
	 * ######################
	 */

	/**
	 * Initialize the flows table
	 *
	 * @return void
	 */
	public function maybe_setup_flows_logs_table() {

		if ( ! WPWHPRO()->sql->table_exists( $this->flow_logs_table_data['table_name'] ) ) {
			WPWHPRO()->sql->run_dbdelta( $this->flow_logs_table_data['sql_create_table'] );

			WPWHPRO()->sql->update_table_exists_cache( $this->flow_logs_table_data['table_name'], 'exists' );
		}

	}

	/**
	 * Get a global count of all flows logs
	 * 
	 * @since 5.2.2
	 *
	 * @return mixed - int if count is available, false if not
	 */
	public function get_flows_logs_count() {

		if ( ! empty( $this->cache_flow_logs_count ) ) {
			return intval( $this->cache_flow_logs_count );
		}

		$this->maybe_setup_flows_logs_table();

		$sql = 'SELECT COUNT(*) FROM {prefix}' . $this->flow_logs_table_data['table_name'] . ';';
		$data = WPWHPRO()->sql->run($sql);

		if ( is_array( $data ) && ! empty( $data ) ) {
			$this->cache_flow_logs_count = intval( $data[0]->{"COUNT(*)"} );
			return $this->cache_flow_logs_count;
		} else {
			return false;
		}

	}

	/**
	 * Returns certain items of the logs table
	 * 
	 * @since 5.2.2
	 *
	 * @param integer $offset
	 * @param integer $limit
	 * @return array - An array of the given log data + extra data
	 */
	public function get_flows_logs( $args = array() ){

		$limit = 10;
		$offset = 0;
		$flow_id = 0;

		if( isset( $args['limit'] ) ){
			$limit = intval( $args['limit'] );
		}

		if( isset( $args['offset'] ) ){
			$offset = intval( $args['offset'] );
		}

		if( isset( $args['flow_id'] ) ){
			$flow_id = intval( $args['flow_id'] );
		}

		$this->maybe_setup_flows_logs_table();

		$sql = 'SELECT * FROM {prefix}' . $this->flow_logs_table_data['table_name'];

		if( ! empty( $flow_id ) ){
			$sql .= ' WHERE flow_id = ' . intval( $flow_id );
		}

		$sql .= ' ORDER BY id DESC LIMIT ' . intval( $limit ) . ' OFFSET ' . intval( $offset ) . ';';	
		$data = WPWHPRO()->sql->run($sql);

		return $data;
	}

	/**
	 * Search and filter the flow logs
	 * 
	 * @since 6.1.0
	 *
	 * @param string $args
	 * @return mixed - an array of the flows logs
	 */
	public function flows_logs_query( $args = array() ){

		$this->maybe_setup_flows_logs_table();

		$template_response = array(
			'total' => 0,
			'per_page' => 0,
			'paged' => 0,
			'items' => array(),
		);

		$defaults = array(
			's' => '',
			'per_page' => 20,
			'paged' => 1,
			'items__in' => array(),
			'orderby' => 'id',
			'order' => 'DESC',
		);

		$query_data = array_merge( $defaults, $args );

		//escape search attribute
		if( ! empty( $query_data['s'] ) ){
			$query_data['s'] = esc_sql( $query_data['s'] );
		}

		//validate items
		if( ! empty( $query_data['items__in'] ) && is_array( $query_data['items__in'] ) ){
			foreach( $query_data['items__in'] as $item_key => $single_item ){
				$query_data['items__in'][ $item_key ] = intval( $single_item );
			}
		} else {
			$query_data['items__in'] = array();
		}

		//maybe correct paged attribute
		$query_data['paged'] = intval( $query_data['paged'] );
		if( $query_data['paged'] < 1 || ! is_numeric( $query_data['paged'] ) ){
			$query_data['paged'] = 1;
		}

		$limit = intval( $query_data['per_page'] );
		$offset = ( $query_data['paged'] - 1 ) * $limit;
		
		switch( $query_data['orderby'] ){
			case 'id':
				$orderby = 'id';
				break;
			case 'completed':
				$orderby = 'flow_completed';
				break;
			case 'name':
				$orderby = 'name';
				break;
			case 'date':
			case 'flow_date':
				$orderby = 'flow_date';
				break;
			case 'id':
			default:
				$orderby = 'id';
				break;
		}
		
		switch( $query_data['order'] ){
			case 'ASC':
			case 'asc':
				$order = 'ASC';
				break;
			case 'DESC':
			case 'desc':
			default:
				$order = 'DESC';
				break;
		}

		if( $offset < 0 ){
			$offset = 0;
		}

		$sql_start_item = 'SELECT * ';
		$sql_start_count = 'SELECT COUNT(*) ';
		
		$core_sql = 'FROM {prefix}' . $this->flow_logs_table_data['table_name'];

		if( $query_data['s'] !== '' || ! empty( $query_data['items__in'] ) ){
			$core_sql .= ' WHERE';
		}

		if( $query_data['s'] !== '' ){
			$core_sql .= ' ( flow_id = \'' . intval( $query_data['s'] ) . '\' OR id = \'' . intval( $query_data['s'] ) . '\' )  AND';
		}

		if( ! empty( $query_data['items__in'] ) ){
			$core_sql .= ' id IN ( ';

			foreach( $query_data['items__in'] as $item_id ){
				$core_sql .= $item_id . ', ';
			}

			$core_sql = trim( $core_sql, ', ' );

			$core_sql .= ' )';
		}

		//clean statement
		$core_sql = trim( $core_sql, ' AND' );

		$core_sql .= ' ORDER BY ' . $orderby . ' ' . $order;

		$sql_item = $sql_start_item . $core_sql;
		$sql_count = $sql_start_count . $core_sql;

		if( $limit > 0 ){
			$sql_item .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
		}

		//end core SQL
		$sql_item .= ';';
		$sql_count .= ';';

		$template_sql_response = WPWHPRO()->sql->run( $sql_item );
		$template_sql_count_response = WPWHPRO()->sql->run( $sql_count );

		$total_count = 0;
		if( ! empty( $template_sql_count_response ) && is_array( $template_sql_count_response ) ){
			foreach( $template_sql_count_response as $single ){
				if( ! empty( $single->{'COUNT(*)'} ) ){
					$total_count = intval( $single->{'COUNT(*)'} );
				}
			}
		}	

		if( ! empty( $total_count ) ){
			$template_response['total'] = $total_count;
		}

		$templates = array();
		if( ! empty( $template_sql_response ) && is_array( $template_sql_response ) ){
			foreach( $template_sql_response as $single ){
				if( ! empty( $single->id ) ){
					$templates[ $single->id ] = $single;
				}
			}
		}

		if( ! empty( $templates ) ){
			$template_response['items'] = $templates;
		}

		$template_response['paged'] = $query_data['paged'];
		$template_response['per_page'] = $query_data['per_page'];

		return $template_response;
    }

	/**
	 * Get the data flow logs
	 *
	 * @param array $args Further flows to filter for
	 * @return mixed - an array of multiple flows or an object for a single flow
	 */
	public function get_flow_log( $id, $cached = true ) {

		if ( empty( $id ) || ! is_numeric( $id ) ) {
			return false;
		}

		$id = intval( $id );

		if ( ! empty( $this->cache_flow_logs ) && $cached ) {

			if ( isset( $this->cache_flow_logs[ $id ] ) ) {
				return $this->cache_flow_logs[ $id ];
			} else {
				return false;
			}

		}

		$this->maybe_setup_flows_table();

		$sql = 'SELECT * FROM {prefix}' . $this->flow_logs_table_data['table_name'] . ' WHERE id = ' . $id . ';';
		$data = WPWHPRO()->sql->run( $sql );

		$validated_data = array();

		if ( ! empty( $data ) && is_array( $data ) ) {
			foreach( $data as $single ) {
				if ( ! empty( $single->id ) ) {
					$newSingle = $single;
					$newSingle->flow_payload = ! empty( $newSingle->flow_payload ) ? json_decode( base64_decode( $newSingle->flow_payload ), true ) : '';
					$newSingle->flow_config = ! empty( $newSingle->flow_config ) ? json_decode( base64_decode( $newSingle->flow_config ), true ) : '';
					$validated_data = $newSingle;
				}
			}
		}

		$this->cache_flow_logs[ $id ] = apply_filters( 'wpwhpro/flows/logs/get_flow_log', $validated_data, $id, $cached, $data );

		return $this->cache_flow_logs[ $id ];
	}

	/**
	 * Add a log data item to the logs
	 *
	 * @param string $msg
	 * @param mixed $data can be everything that should be saved as log data
	 * @return bool - True if the function runs successfully
	 */
	public function add_flow_log( $flow_id, $args = array() ){

		if( empty( $flow_id ) ){
			return false;
		}

		$flow_id = intval( $flow_id );
		$flow_config = isset( $args['flow_config'] ) ? $args['flow_config'] : '';
		$flow_payload = isset( $args['flow_payload'] ) ? $args['flow_payload'] : '';

		$this->maybe_setup_flows_logs_table();

		$sql_vals = array(
			'flow_id' => $flow_id,
			'flow_config' => ( is_string( $flow_config ) ) ?  base64_encode( $flow_config ) : base64_encode( json_encode( $flow_config ) ),
			'flow_payload' => ( is_string( $flow_payload ) ) ?  base64_encode( $flow_payload ) : base64_encode( json_encode( $flow_payload ) ),
			'flow_completed' => 0,
			'flow_date' => date( 'Y-m-d H:i:s' )
		);

		// START UPDATE PRODUCT
		$sql_keys = '';
		$sql_values = '';
		foreach( $sql_vals as $key => $single ){

			$sql_keys .= esc_sql( $key ) . ', ';
			$sql_values .= '"' . $single . '", ';

		}

		$sql = 'INSERT INTO {prefix}' . $this->flow_logs_table_data['table_name'] . ' (' . trim($sql_keys, ', ') . ') VALUES (' . trim($sql_values, ', ') . ');';
		$id = WPWHPRO()->sql->run( $sql, OBJECT, array( 'return_id' => true ) );

		return $id;

	}

	/**
	 * Update an existing flows template
	 *
	 * @param int $id - the template id
	 * @param array $data - the new template data
	 * @return bool - True if update was successful, false if not
	 */
	public function update_flow_log( $id, $data ) {

		$id = intval( $id );

		$this->maybe_setup_flows_logs_table();

		$flow_log = $this->get_flow_log( $id );
		if ( ! $flow_log ) {
			return false;
		}

		$sql_vals = array();

		if ( isset( $data['flow_config'] ) ) {
			$sql_vals['flow_config'] = base64_encode( json_encode( $data['flow_config'] ) );
		}

		if ( isset( $data['flow_payload'] ) ) {
			$sql_vals['flow_payload'] = base64_encode( json_encode( $data['flow_payload'] ) );
		}

		if ( isset( $data['flow_completed'] ) ) {
			$sql_vals['flow_completed'] = intval( $data['flow_completed'] );
		}

		if ( isset( $data['flow_id'] ) ) {
			$sql_vals['flow_id'] = intval( $data['flow_id'] );
		}

		if ( empty( $sql_vals ) ) {
			return false;
		}

		$sql_string = '';
		foreach( $sql_vals as $key => $single ) {

			$sql_string .= $key . ' = "' . $single . '", ';

		}
		$sql_string = trim( $sql_string, ', ' );

		$sql = 'UPDATE {prefix}' . $this->flow_logs_table_data['table_name'] . ' SET ' . $sql_string . ' WHERE id = ' . $id . ';';
		WPWHPRO()->sql->run($sql);

		return true;

	}

	/**
	 * Generate the pagination for the flow logs
	 * 
	 * @since 5.2.2
	 * @deprecated 6.1.0
	 *
	 * @param array $args
	 * @return string
	 */
	public function logs_pagination( $args = array() ) {
	 
		$per_page = isset( $args['per_page'] ) ? intval( $args['per_page'] ) : 10;
		$page = isset( $args['log_page'] ) ? intval( $args['log_page'] ) : 1;

		$page_counter = 1;
		$log_count = $this->get_flows_logs_count();
		$total_pages = ceil( $log_count / $per_page );
		$current_url = WPWHPRO()->helpers->get_current_url( false, true );

		if( $page > $total_pages ){
			$page = $total_pages;
		}
	
		if( $page < 1 ){
			$page = 1;
		}

		$pagination_links_out = array();

		$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => 1, ) ) ) . '" class="wpwh-btn--sm mr-1">' . __( '<<', 'wp-webhooks' ) . '</a>';

		if( $page <= 1 ){
			$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => 1, ) ) ) . '" class="wpwh-btn--sm mr-1">' . __( '<', 'wp-webhooks' ) . '</a>';
		} else {
			$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => ($page-1), ) ) ) . '" class="wpwh-btn--sm mr-1">' . __( '<', 'wp-webhooks' ) . '</a>';
		}
		

		if( $total_pages > 3 ){
			
			
			if( $page === 1 ){
				$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => 1, ) ) ) . '" class="wpwh-btn wpwh-btn--primary wpwh-btn--sm mr-1">' . 1 . '</a>';
				$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => 2, ) ) ) . '" class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm mr-1">' . 2 . '</a>';
				$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => 3, ) ) ) . '" class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm mr-1">' . 3 . '</a>';
			} elseif( $page >= $total_pages ){
				$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => $page-2, ) ) ) . '" class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm mr-1">' . ($page-2) . '</a>';
				$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => $page-1, ) ) ) . '" class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm mr-1">' . ($page-1) . '</a>';
				$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => $page, ) ) ) . '" class="wpwh-btn wpwh-btn--primary wpwh-btn--sm mr-1">' . $page . '</a>';
			} else {
				$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => $page-1, ) ) ) . '" class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm mr-1">' . ($page-1) . '</a>';
				$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => $page, ) ) ) . '" class="wpwh-btn wpwh-btn--primary wpwh-btn--sm mr-1">' . $page . '</a>';
				$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => $page+1, ) ) ) . '" class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm mr-1">' . ($page+1) . '</a>';
			}

			
			
		} else {
			$page_counter = 1;
			$total_pages_tmp = $total_pages;
			while( $total_pages_tmp > 0 ){

				if( $page_counter === $page ){
					$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => $page_counter, ) ) ) . '" class="wpwh-btn wpwh-btn--primary wpwh-btn--sm mr-1">' . $page_counter . '</a>';
				} else {
					$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => $page_counter, ) ) ) . '" class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm mr-1">' . $page_counter . '</a>';
				}
				
				$page_counter++;
				$total_pages_tmp--;
			}
		}
		
		if( $page >= $total_pages ){
			$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => $total_pages, ) ) ) . '" class="wpwh-btn--sm mr-1">' . __( '>', 'wp-webhooks' ) . '</a>';
		} else {
			$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => ($page+1), ) ) ) . '" class="wpwh-btn--sm mr-1">' . __( '>', 'wp-webhooks' ) . '</a>';
		}

		$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => $total_pages, ) ) ) . '" class="wpwh-btn--sm mr-1">' . __( '>>', 'wp-webhooks' ) . '</a>';

		return implode( '', $pagination_links_out );
	}

	/**
	 * ######################
	 * ###
	 * #### Common tags logic
	 * ###
	 * ######################
	 */

	public function get_common_tag_value( $tag ){
		$value = '';

		$user_id = 0;
		if( isset( $this->flow_common_tags_cache['user_id'] ) ){
			$user_id = $this->flow_common_tags_cache['user_id'];
		} else {
			$user_id = get_current_user_id();
			$this->flow_common_tags_cache['user_id'] = $user_id;
		}

		$user = null;
		if( ! empty( $user_id ) ){
			if( isset( $this->flow_common_tags_cache['user'] ) ){
				$user = $this->flow_common_tags_cache['user'];
			} else {
				$user = get_user_by( 'id', $user_id );
				$this->flow_common_tags_cache['user'] = $user;
			}
		}

		if( isset( $this->flow_common_tags_cache_value[ $tag ] ) ){
			$value = $this->flow_common_tags_cache_value[ $tag ];
		} else {

			switch( $tag ){
				case 'common:user_first_name':
					if( ! empty( $user ) && ! is_wp_error( $user ) && isset( $user->first_name ) ){
						$value = $user->first_name;
					}
					break;
				case 'common:user_last_name':
					if( ! empty( $user ) && ! is_wp_error( $user ) && isset( $user->last_name ) ){
						$value = $user->last_name;
					}
					break;
				case 'common:user_login':
					if( ! empty( $user ) && ! is_wp_error( $user ) && isset( $user->data ) && isset( $user->data->user_login ) ){
						$value = $user->data->user_login;
					}
					break;
				case 'common:user_email':
					if( ! empty( $user ) && ! is_wp_error( $user ) && isset( $user->data ) && isset( $user->data->user_email ) ){
						$value = $user->data->user_email;
					}
					break;
				case 'common:user_display_name':
					if( ! empty( $user ) && ! is_wp_error( $user ) && isset( $user->data ) && isset( $user->data->display_name ) ){
						$value = $user->data->display_name;
					}
					break;
				case 'common:user_id':
					if( ! empty( $user_id ) ){
						$value = $user_id;
					}
					break;
				case 'common:reset_pw_url':
					$value = wp_lostpassword_url();
					break;
				case 'common:admin_email':
					$value = get_option( 'admin_email' );
					break;
				case 'common:site_name':
					$value = get_option( 'blogname' );
					break;
				case 'common:site_url':
					$value = get_option( 'siteurl' );
					break;
				case 'common:current_date':
					$value = wp_date( 'Y-m-d H:i:s', time() );
					break;
			}

			$this->flow_common_tags_cache_value[ $tag ] = $value;
		}

		return apply_filters( 'wpwhpro/flows/get_common_tag_value', $value, $tag );
	}

  	/**
	 * ######################
	 * ###
	 * #### Flows SQL table
	 * ###
	 * ######################
	 */

	/**
	 * Initialize the flows table
	 *
	 * @return void
	 */
	public function maybe_setup_flows_table() {

		if ( ! WPWHPRO()->sql->table_exists( $this->flows_table_data['table_name'] ) ) {
			WPWHPRO()->sql->run_dbdelta( $this->flows_table_data['sql_create_table'] );

			WPWHPRO()->sql->update_table_exists_cache( $this->flows_table_data['table_name'], 'exists' );
		}

	}

	/**
	 * Get the data flows template/S
	 *
	 * @param array $further flows to filter for
	 * @return mixed - an array of multiple flows or an object for a single flow
	 */
	public function get_flows( $args = array(), $cached = true ) {

		$template = 'all';
		if( is_array( $args ) && isset( $args['template'] ) ){
			$template = $args['template'];
		}

		$flow_trigger = '';
		if( is_array( $args ) && isset( $args['flow_trigger'] ) ){
			$flow_trigger = $args['flow_trigger'];
		}

		if ( ! is_numeric( $template ) && $template !== 'all' ) {
			return false;
		}

		if( is_numeric( $template ) ){
			$template = intval( $template );
		}

		if ( ! empty( $this->cache_flows ) && $cached ) {

			if ( $template !== 'all' ) {
				if ( isset( $this->cache_flows[ $template ] ) ) {
					return $this->cache_flows[ $template ];
				} else {
					return false;
				}
			} else {

				if( ! $flow_trigger ){
					return $this->cache_flows;
				} else {
					$filtered_flows = array();
					if( ! empty( $this->cache_flows ) && is_array( $this->cache_flows ) ){
						foreach( $this->cache_flows as $fkey => $fdata ){
							if( is_object( $fdata ) && isset( $fdata->flow_trigger ) && $fdata->flow_trigger === $flow_trigger ){
								$filtered_flows[ $fkey ] = $fdata;
							}
						}
					}

					return $filtered_flows;
				}

			}

		}

		$this->maybe_setup_flows_table();

		$sql = 'SELECT * FROM {prefix}' . $this->flows_table_data['table_name'] . ' ORDER BY id ASC;';

		$data = WPWHPRO()->sql->run($sql);

		$validated_data = array();

		if ( ! empty( $data ) && is_array( $data ) ) {
			foreach( $data as $single ) {
				if ( ! empty( $single->id ) ) {
					$newSingle = $single;
					$newSingle->flow_config = json_decode( base64_decode( $newSingle->flow_config, true ) );
					$validated_data[ $single->id ] = $newSingle;
				}
			}
		}

		$this->cache_flows = $validated_data;

		if ( $template !== 'all' ) {
			if ( isset( $this->cache_flows[ $template ] ) ) {
				return $this->cache_flows[ $template ];
			} else {
				return false;
			}
		} else {

			if( ! $flow_trigger ){
				return $this->cache_flows;
			} else {
				$filtered_flows = array();
				if( ! empty( $this->cache_flow ) && ! is_array( $this->cache_flow ) ){
					foreach( $this->cache_flows as $fkey => $fdata ){
						if( is_object( $fdata ) && isset( $fdata->flow_trigger ) && $fdata->flow_trigger === $flow_trigger ){
							$filtered_flows[ $fkey ] = $fdata;
						}
					}
				}

				return $filtered_flows;
			}

		}
	}

	/**
	 * Search and filter the flows
	 * 
	 * @since 6.1.0
	 *
	 * @param string $args
	 * @return mixed - an array of the flows + extra data
	 */
	public function flows_query( $args = array() ){

		$this->maybe_setup_flows_table();

		$template_response = array(
			'total' => 0,
			'per_page' => 0,
			'paged' => 0,
			'items' => array(),
		);

		$defaults = array(
			's' => '',
			'per_page' => 20,
			'paged' => 1,
			'items__in' => array(),
			'orderby' => 'id',
			'order' => 'DESC',
		);

		$query_data = array_merge( $defaults, $args );

		//escape search attribute
		if( ! empty( $query_data['s'] ) ){
			$query_data['s'] = esc_sql( $query_data['s'] );
		}

		//validate items
		if( ! empty( $query_data['items__in'] ) && is_array( $query_data['items__in'] ) ){
			foreach( $query_data['items__in'] as $item_key => $single_item ){
				$query_data['items__in'][ $item_key ] = intval( $single_item );
			}
		} else {
			$query_data['items__in'] = array();
		}

		//maybe correct paged attribute
		$query_data['paged'] = intval( $query_data['paged'] );
		if( $query_data['paged'] < 1 || ! is_numeric( $query_data['paged'] ) ){
			$query_data['paged'] = 1;
		}

		$limit = intval( $query_data['per_page'] );
		$offset = ( $query_data['paged'] - 1 ) * $limit;
		
		switch( $query_data['orderby'] ){
			case 'status':
				$orderby = 'flow_status';
				break;
			case 'trigger':
				$orderby = 'flow_trigger';
				break;
			case 'title':
				$orderby = 'flow_title';
				break;
			case 'date':
			case 'flow_date':
				$orderby = 'flow_date';
				break;
			case 'id':
			default:
				$orderby = 'id';
				break;
		}
		
		switch( $query_data['order'] ){
			case 'ASC':
			case 'asc':
				$order = 'ASC';
				break;
			case 'DESC':
			case 'desc':
			default:
				$order = 'DESC';
				break;
		}

		if( $offset < 0 ){
			$offset = 0;
		}

		$sql_start_item = 'SELECT * ';
		$sql_start_count = 'SELECT COUNT(*) ';
		
		$core_sql = 'FROM {prefix}' . $this->flows_table_data['table_name'];

		if( $query_data['s'] !== '' || ! empty( $query_data['items__in'] ) ){
			$core_sql .= ' WHERE';
		}

		if( $query_data['s'] !== '' ){
			$core_sql .= ' ( flow_title LIKE \'%' . esc_sql( $query_data['s'] ) . '%\' OR flow_trigger LIKE \'%' . esc_sql( sanitize_title( $query_data['s'] ) ) . '%\' OR flow_name LIKE \'%' . esc_sql( sanitize_title( $query_data['s'] ) ) . '%\' )  AND';
		}

		if( ! empty( $query_data['items__in'] ) ){
			$core_sql .= ' id IN ( ';

			foreach( $query_data['items__in'] as $item_id ){
				$core_sql .= $item_id . ', ';
			}

			$core_sql = trim( $core_sql, ', ' );

			$core_sql .= ' )';
		}

		//clean statement
		$core_sql = trim( $core_sql, ' AND' );

		$core_sql .= ' ORDER BY ' . $orderby . ' ' . $order;

		$sql_item = $sql_start_item . $core_sql;
		$sql_count = $sql_start_count . $core_sql;

		if( $limit > 0 ){
			$sql_item .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
		}

		//end core SQL
		$sql_item .= ';';
		$sql_count .= ';';

		$template_sql_response = WPWHPRO()->sql->run( $sql_item );
		$template_sql_count_response = WPWHPRO()->sql->run( $sql_count );

		$total_count = 0;
		if( ! empty( $template_sql_count_response ) && is_array( $template_sql_count_response ) ){
			foreach( $template_sql_count_response as $single ){
				if( ! empty( $single->{'COUNT(*)'} ) ){
					$total_count = intval( $single->{'COUNT(*)'} );
				}
			}
		}	

		if( ! empty( $total_count ) ){
			$template_response['total'] = $total_count;
		}

		$templates = array();
		if( ! empty( $template_sql_response ) && is_array( $template_sql_response ) ){
			foreach( $template_sql_response as $single ){
				if( ! empty( $single->id ) ){
					$templates[ $single->id ] = $single;
				}
			}
		}

		if( ! empty( $templates ) ){
			$template_response['items'] = $templates;
		}

		$template_response['paged'] = $query_data['paged'];
		$template_response['per_page'] = $query_data['per_page'];

		return $template_response;
    }

	/**
	 * Helper function to flatten flows specific data
	 *
	 * @param mixed $data - the data value that needs to be flattened
	 * @return mixed - the flattened value
	 */
	public function flatten_flows_data( $data ) {
		$flattened = array();

		foreach( $data as $id => $sdata ) {
			$flattened[ $id ] = $sdata->flow_title;
		}

		return $flattened;
	}

	/**
	 * Delete a flows template
	 *
	 * @param ind $id - the id of the flows template
	 * @return bool - True if deletion was succesful, false if not
	 */
	public function delete_flow( $id ) {

		$this->maybe_setup_flows_table();

		$id = intval( $id );
		$flow = $this->get_flows( array( 'template' => $id ) );

		if ( ! $flow ) {
			return false;
		}

		//Delete related trigger
		if( isset( $flow->flow_trigger ) && ! empty( $flow->flow_trigger ) ){
			$this->delete_flow_trigger( array( 'flow_id' => $id, 'webhook_group' => $flow->flow_trigger ) );
		}

		//Delete related actions
		if( isset( $flow->flow_config ) && is_object( $flow->flow_config ) && isset( $flow->flow_config->actions ) && ! empty( $flow->flow_config->actions ) ){
			foreach( $flow->flow_config->actions as $sak => $sav ){

				$action = '';
				if( isset( $sav->action ) ){
					$action = sanitize_title( $sav->action );
				}

				$this->delete_flow_action( array( 'flow_id' => $id, 'flow_step' => $sak, 'webhook_group' => $action ) );
			}
		}

		$sql = 'DELETE FROM {prefix}' . $this->flows_table_data['table_name'] . ' WHERE id = ' . $id . ';';
		WPWHPRO()->sql->run($sql);

		return true;

	}

	/**
	 * Delete flow_logs
	 * 
	 * @since 5.2.2
	 *
	 * @param ind $id - the id of the flows template
	 * @return bool - True if deletion was succesful, false if not
	 */
	public function delete_flow_logs( $args = array() ) {

		$this->maybe_setup_flows_table();

		if( ! isset( $args['id'] ) ){
			return false;
		}

		if( is_string( $args['id'] ) && $args['id'] === 'all' ){
			$flow_log_id = 'all';
		} else {
			$flow_log_id = intval( $args['id'] );
		}
		

		$sql = 'DELETE FROM {prefix}' . $this->flow_logs_table_data['table_name'];

		if( $flow_log_id !== 'all' ){
			$sql .= ' WHERE id = ' . $flow_log_id;
		}

		$sql .= ';';

		WPWHPRO()->sql->run($sql);

		return true;

	}

	/**
	 * Get a global count of all flows templates
	 *
	 * @return mixed - int if count is available, false if not
	 */
	public function get_flows_count() {

		if ( ! empty( $this->cache_flows_count ) ) {
			return intval( $this->cache_flows_count );
		}

		$this->maybe_setup_flows_table();

		$sql = 'SELECT COUNT(*) FROM {prefix}' . $this->flows_table_data['table_name'] . ';';
		$data = WPWHPRO()->sql->run($sql);

		if ( is_array( $data ) && ! empty( $data ) ) {
			$this->cache_flows_count = $data;
			return intval( $data[0]->{"COUNT(*)"} );
		} else {
			return false;
		}

	}

	/**
	 * Add a flows template
	 *
	 * @param array $data - an array contianing all relevant data
	 * @return bool - True if the creation was successful, false if not
	 */
	public function add_flow( $data = array() ) {

		$this->maybe_setup_flows_table();

		$flow_author = isset( $data['flow_author'] ) ? $data['flow_author'] : get_current_user_id();
		$flow_status = isset( $data['flow_status'] ) ? $data['flow_status'] : 'inactive';
		$flow_trigger = isset( $data['flow_trigger'] ) ? $data['flow_trigger'] : '';
		$flow_title = isset( $data['flow_title'] ) ? wp_strip_all_tags( sanitize_text_field( $data['flow_title'] ) ) : __( 'unnamed', 'wp-webhooks' );
		$flow_name = isset( $data['flow_name'] ) ? sanitize_title( $data['flow_name'] ) : sanitize_title( $flow_title );
		$flow_date = isset( $data['flow_date'] ) ? $data['flow_date'] : date( 'Y-m-d H:i:s' );

		$sql_vals = array(
			'flow_title' => $flow_title,
			'flow_name' => $flow_name,
			'flow_status' => $flow_status,
			'flow_trigger' => $flow_trigger,
			'flow_author' => intval( $flow_author ),
			'flow_date' => $flow_date,
		);

		if( isset( $data['flow_config'] ) ){

			if( is_array( $data['flow_config'] ) ){
				$flow_config_json = json_encode( $data['flow_config'] );
			} else {
				$flow_config_json = $data['flow_config'];
			}

			//Remove possible non-GSM characters that appear via some WordPress objects
			$flow_config_json = str_replace( '\u0000', '', $flow_config_json );

			$sql_vals['flow_config'] = base64_encode( $flow_config_json );
		}

		if( isset( $data['id'] ) && ! empty( $data['id'] ) && is_numeric( $data['id'] ) ){
			$sql_vals['id'] = intval( $data['id'] );
		}

		$sql_keys = '';
		$sql_values = '';
		foreach( $sql_vals as $key => $single ) {

			$sql_keys .= esc_sql( $key ) . ', ';
			$sql_values .= '"' . $single . '", ';

		}

		$sql = 'INSERT INTO {prefix}' . $this->flows_table_data['table_name'] . ' ( ' . trim($sql_keys, ', ' ) . ' ) VALUES ( ' . trim($sql_values, ', ' ) . ' );';
		$flow_id = WPWHPRO()->sql->run( $sql, OBJECT, array( 'return_id' => true ) );

		return $flow_id;

	}

	/**
	 * Update an existing flows template
	 *
	 * @param int $id - the template id
	 * @param array $data - the new template data
	 * @return bool - True if update was successful, false if not
	 */
	public function update_flow( $id, $data ) {

		$id = intval( $id );

		$this->maybe_setup_flows_table();

		$flow = $this->get_flows( array( 'template' => $id ) );
		if ( ! $flow ) {
			return false;
		}	

		$sql_vals = array();

		if ( isset( $data['flow_title'] ) ) {
			$sql_vals['flow_title'] = wp_strip_all_tags( sanitize_text_field( $data['flow_title'] ) );
		}

		if ( isset( $data['flow_name'] ) ) {
			$sql_vals['flow_name'] = sanitize_title( $data['flow_name'] );
		}

		if ( isset( $data['flow_config'] ) ) {
			
			$flow_config_json = json_encode( $data['flow_config'] );

			//Remove possible non-GSM characters that appear via some WordPress objects
			$flow_config_json = str_replace( '\u0000', '', $flow_config_json );

			$sql_vals['flow_config'] = base64_encode( $flow_config_json );

		}

		if ( isset( $data['flow_status'] ) ) {
			$sql_vals['flow_status'] = sanitize_title( $data['flow_status'] );
		}

		if ( isset( $data['flow_trigger'] ) ) {
			$sql_vals['flow_trigger'] = sanitize_title( $data['flow_trigger'] );
		}

		if ( isset( $data['flow_author'] ) ) {
			$sql_vals['flow_author'] = intval( $data['flow_author'] );
		}

		if ( isset( $data['flow_date'] ) ) {
			$sql_vals['flow_date'] = date( 'Y-m-d H:i:s', strtotime( $data['flow_date'] ) );
		}

		if ( empty( $sql_vals ) ) {
			return false;
		}

		$sql_string = '';
		foreach( $sql_vals as $key => $single ) {

			$sql_string .= $key . ' = "' . $single . '", ';

		}
		$sql_string = trim( $sql_string, ', ' );

		$sql = 'UPDATE {prefix}' . $this->flows_table_data['table_name'] . ' SET ' . $sql_string . ' WHERE id = ' . $id . ';';
		WPWHPRO()->sql->run($sql);

		if( isset( $flow->flow_trigger ) ){

			$date_created = '';
			$trigger_secret = '';
			$new_status = ( isset( $sql_vals['flow_status'] ) ) ? $sql_vals['flow_status'] : $flow->flow_status;

			//Delete old trigger
			if( isset( $flow->flow_trigger ) && ! empty( $flow->flow_trigger ) ){

				$webhook_trigger = $this->get_flow_trigger_url_name( $id );
				$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', $flow->flow_trigger );

				if( isset( $webhooks[ $webhook_trigger ] ) && isset( $webhooks[ $webhook_trigger ]['secret'] ) ){
					$trigger_secret = $webhooks[ $webhook_trigger ]['secret'];
				}

				if( isset( $webhooks[ $webhook_trigger ] ) && isset( $webhooks[ $webhook_trigger ]['date_created'] ) ){
					$date_created = $webhooks[ $webhook_trigger ]['date_created'];
				}

				$this->delete_flow_trigger( array( 'flow_id' => $id, 'webhook_group' => $flow->flow_trigger ) );
			}

			//Add new trigger
			if( isset( $sql_vals['flow_trigger'] ) && ! empty( $sql_vals['flow_trigger'] ) ){
				$new_trigger_args = array(
					'flow_id' => $id,
					'webhook_group' => $sql_vals['flow_trigger'],
					'secret' => $trigger_secret,
					'date_created' => $date_created,
				);

				if( isset( $data['flow_config'] ) && is_array( $data['flow_config'] ) && isset( $data['flow_config']['triggers'] ) && ! empty( $data['flow_config']['triggers'] ) ){
					foreach( $data['flow_config']['triggers'] as $tk => $tv ){

						if( isset( $tv['integration'] ) ){
							$new_trigger_args['integration'] = $tv['integration'];
						}

						if( isset( $tv['fields'] ) && ! empty( $tv['fields'] ) ){
							$new_trigger_args['settings'] = $tv['fields'];

							foreach( $new_trigger_args['settings'] as $ssk => $ssv ){
								if( isset( $ssv['value'] ) && $this->is_filled_setting( $ssv['value'] ) ){
									$new_trigger_args['settings'][ $ssk ] = $ssv['value'];
								} else {
									unset( $new_trigger_args['settings'][ $ssk ] );
								}

							}
						}
						break; // it's only one available anyways
					}
				}
				$this->create_flow_trigger( $new_trigger_args );
			}

			if( $new_status === 'active' ){

				//Delete old actions
				if( isset( $flow->flow_config ) && is_object( $flow->flow_config ) && isset( $flow->flow_config->actions ) && ! empty( $flow->flow_config->actions ) ){
					foreach( $flow->flow_config->actions as $sak => $sav ){

						$action = '';
						if( isset( $sav->action ) ){
							$action = sanitize_title( $sav->action );
						}

						$this->delete_flow_action( array( 'flow_id' => $id, 'flow_step' => $sak, 'webhook_group' => $action ) );
					}
				}

				//Add new actions
				if( isset( $data['flow_config'] ) && is_array( $data['flow_config'] ) && isset( $data['flow_config']['actions'] ) && ! empty( $data['flow_config']['actions'] ) ){
					foreach( $data['flow_config']['actions'] as $tk => $tv ){

						$new_action_args = array(
							'flow_id' => $id,
							'flow_step' => $tk,
						);

						$action = '';
						if( isset( $tv['action'] ) ){
							$action = sanitize_title( $tv['action'] );
						}

						if( isset( $tv['integration'] ) ){
							$new_action_args['integration'] = $tv['integration'];
						}

						if( isset( $tv['fields'] ) && ! empty( $tv['fields'] ) ){

							$new_action_args['settings'] = $tv['fields'];

							//this foreach is only for testing as of now
							foreach( $new_action_args['settings'] as $ssk => $ssv ){

								//Only allow settings
								if( ! isset( $ssv['type'] ) || $ssv['type'] !== 'setting' ){
									unset( $new_action_args['settings'][ $ssk ] );
									continue;
								}

								if( isset( $ssv['value'] ) && $this->is_filled_setting( $ssv['value'] ) ){
									$new_action_args['settings'][ $ssk ] = $ssv['value'];
								} else {
									unset( $new_action_args['settings'][ $ssk ] );
								}
							}

						}

						//add whitelisted action
						if( ! empty( $action ) ){
							$new_action_args['settings']['wpwhpro_action_action_whitelist'] = array( $action );
						}

						//since 5.0
						$new_action_args['webhook_group'] = $action;

						$this->create_flow_action( $new_action_args );
					}
				}
			} else {
				//Delete actions
				if( isset( $flow->flow_config ) && is_object( $flow->flow_config ) && isset( $flow->flow_config->actions ) && ! empty( $flow->flow_config->actions ) ){
					foreach( $flow->flow_config->actions as $sak => $sav ){

						$action = '';
						if( isset( $sav->action ) ){
							$action = sanitize_title( $sav->action );
						}

						$this->delete_flow_action( array( 'flow_id' => $id, 'flow_step' => $sak, 'webhook_group' => $action ) );
					}
				}
			}

		} else {
			//Delete trigger
			$new_flow_trigger = ( isset( $sql_vals['flow_trigger'] ) ) ? $sql_vals['flow_trigger'] : '';
			if( ! empty( $new_flow_trigger ) ){
				$this->delete_flow_trigger( array( 'flow_id' => $id, 'webhook_group' => $new_flow_trigger ) );
			}
		}

		//reload the endpoints
		WPWHPRO()->webhook->reload_webhooks();

		return true;

	}

	/**
	 * Delete the whole flows table
	 *
	 * @return bool - wether the deletion was successful or not
	 */
	public function delete_table() {

		$check = true;

		if ( WPWHPRO()->sql->table_exists( $this->flows_table_data['table_name'] ) ) {
			$check = WPWHPRO()->sql->run( $this->flows_table_data['sql_drop_table'] );
		}

		WPWHPRO()->sql->update_table_exists_cache( $this->flows_table_data['table_name'], 'purge' );

		return $check;
    }

	/**
	 * Delete the whole flow logs table
	 *
	 * @since 5.0
	 *
	 * @return bool - wether the deletion was successful or not
	 */
	public function delete_logs_table() {

		$check = true;

		if ( WPWHPRO()->sql->table_exists( $this->flow_logs_table_data['table_name'] ) ) {
			$check = WPWHPRO()->sql->run( $this->flow_logs_table_data['sql_drop_table'] );
		}

		WPWHPRO()->sql->update_table_exists_cache( $this->flow_logs_table_data['table_name'], 'purge' );

		return $check;
    }

	public function create_flow_trigger( $args = array() ){
		$return = false;

		if( ! isset( $args['flow_id'] ) || empty( $args['flow_id'] ) ){
			return $return;
		}

		if( ! isset( $args['webhook_group'] ) || empty( $args['webhook_group'] ) ){
			return $return;
		}

		$webhook_group = $args['webhook_group'];
		$webhook_trigger = $this->get_flow_trigger_url_name( $args['flow_id'] );

		$trigger_args = array(
			'group' => $webhook_group,
			'webhook_url' => 'wpwhflow',
			'webhook_url_name' => $webhook_trigger,
			'settings' => array(),
		);

		if( isset( $args['secret'] ) && ! empty( $args['secret'] ) ){
			$trigger_args['secret'] = $args['secret'];
		}

		if( isset( $args['integration'] ) && ! empty( $args['integration'] ) ){
			$trigger_args['integration'] = $args['integration'];
		}

		if( isset( $args['date_created'] ) && ! empty( $args['date_created'] ) ){
			$trigger_args['date_created'] = $args['date_created'];
		}

		if( isset( $args['settings'] ) && is_array( $args['settings'] ) && ! empty( $args['settings'] ) ){
			$trigger_args['settings'] = $args['settings'];

			//provide compatibility to the default select field structure
			foreach( $trigger_args['settings'] as $sk => $sv ){

				//Make sure for non-multiple values, we only serve one scalar value
				$allow_multiple = ( isset( $sv['multiple'] ) && $sv['multiple'] ) ? true : false;
				if( isset( $sv['type'] ) && $sv['type'] === 'select' && ! $allow_multiple ){
					if( isset( $sv['value'] ) && $sv['value'] !== '' ){
						$trigger_args['settings'][ $sk ]['value'] = WPWHPRO()->helpers->serve_first( $sv['value'] );
					}
				}

			}
		}

		$check = WPWHPRO()->webhook->create( $webhook_trigger, 'trigger', $trigger_args );

		if( $check ){
			$return = true;
		}

		return $return;
	}

	public function update_flow_trigger( $args = array() ){
		$return = false;

		if( ! isset( $args['flow_id'] ) || empty( $args['flow_id'] ) ){
			return $return;
		}

		if( ! isset( $args['webhook_group'] ) || empty( $args['webhook_group'] ) ){
			return $return;
		}

		$webhook_group = $args['webhook_group'];
		$webhook_trigger = $this->get_flow_trigger_url_name( $args['flow_id'] );

		$trigger_args = array(
			'settings' => array(),
		);

		if( isset( $args['settings'] ) && is_array( $args['settings'] ) && ! empty( $args['settings'] ) ){
			$trigger_args['settings'] = $args['settings'];

			//provide compatibility to the default select field structure
			foreach( $trigger_args['settings'] as $sk => $sv ){

				//Make sure for non-multiple values, we only serve one scalar value
				$allow_multiple = ( isset( $sv['multiple'] ) && $sv['multiple'] ) ? true : false;
				if( isset( $sv['type'] ) && $sv['type'] === 'select' && ! $allow_multiple ){
					if( isset( $sv['value'] ) && $sv['value'] !== '' ){
						$trigger_args['settings'][ $sk ]['value'] = WPWHPRO()->helpers->serve_first( $sv['value'] );
					}
				}

			}
		}

		$check = WPWHPRO()->webhook->update( $webhook_trigger, 'trigger', $webhook_group, $trigger_args );

		if( $check ){
			$return = true;
		}

		return $return;
	}

	public function delete_flow_trigger( $args = array() ){
		$return = false;

		if( ! isset( $args['flow_id'] ) || empty( $args['flow_id'] ) ){
			return $return;
		}

		if( ! isset( $args['webhook_group'] ) || empty( $args['webhook_group'] ) ){
			return $return;
		}

		$webhook_group = $args['webhook_group'];
		$webhook_trigger = $this->get_flow_trigger_url_name( $args['flow_id'] );
		$webhooks       = WPWHPRO()->webhook->get_hooks( 'trigger', $webhook_group );

		if( isset( $webhooks[ $webhook_trigger ] ) ){
			$check = WPWHPRO()->webhook->unset_hooks( $webhook_trigger, 'trigger', $webhook_group );
			if( $check ){
			    $return = true;
            }
		}

		return $return;
	}

	public function create_flow_action( $args = array() ){
		$return = false;

		if( ! isset( $args['flow_id'] ) || empty( $args['flow_id'] ) ){
			return $return;
		}

		if( ! isset( $args['flow_step'] ) || empty( $args['flow_step'] ) ){
			return $return;
		}

		if( ! isset( $args['webhook_group'] ) || empty( $args['webhook_group'] ) ){
			return $return;
		}

		if( isset( $args['webhook_name'] ) ){
			$webhook_action = sanitize_title( $args['webhook_name'] );
		} else {
			$webhook_action = $this->get_flow_action_url_name( $args['flow_id'], $args['flow_step'] );
		}

		$action_args = array(
			'settings' => array(),
			'group' => $args['webhook_group'],
		);

		if( isset( $args['integration'] ) && ! empty( $args['integration'] ) ){
			$action_args['integration'] = $args['integration'];
		}

		if( isset( $args['settings'] ) && is_array( $args['settings'] ) && ! empty( $args['settings'] ) ){
			$action_args['settings'] = $args['settings'];

			//provide compatibility to the default select field structure
			foreach( $action_args['settings'] as $sk => $sv ){

				//Make sure for non-multiple values, we only serve one scalar value
				$allow_multiple = ( isset( $sv['multiple'] ) && $sv['multiple'] ) ? true : false;
				if( isset( $sv['type'] ) && $sv['type'] === 'select' && ! $allow_multiple ){
					if( isset( $sv['value'] ) && $sv['value'] !== '' ){
						$action_args['settings'][ $sk ]['value'] = WPWHPRO()->helpers->serve_first( $sv['value'] );
					}
				}

			}
		}

		$check = WPWHPRO()->webhook->create( $webhook_action, 'action', $action_args );

		if( $check ){
			$return = true;
		}

		return $return;
	}

	public function update_flow_action( $args = array() ){
		$return = false;

		if( ! isset( $args['flow_id'] ) || empty( $args['flow_id'] ) ){
			return $return;
		}

		if( ! isset( $args['flow_step'] ) || empty( $args['flow_step'] ) ){
			return $return;
		}

		if( ! isset( $args['webhook_group'] ) || empty( $args['webhook_group'] ) ){
			return $return;
		}

		if( isset( $args['webhook_name'] ) ){
			$webhook_action = sanitize_title( $args['webhook_name'] );
		} else {
			$webhook_action = $this->get_flow_action_url_name( $args['flow_id'], $args['flow_step'] );
		}

		$action_args = array(
			'settings' => array(),
		);

		if( isset( $args['settings'] ) && is_array( $args['settings'] ) && ! empty( $args['settings'] ) ){
			$action_args['settings'] = $args['settings'];

			//provide compatibility to the default select field structure
			foreach( $action_args['settings'] as $sk => $sv ){

				//Make sure for non-multiple values, we only serve one scalar value
				$allow_multiple = ( isset( $sv['multiple'] ) && $sv['multiple'] ) ? true : false;
				if( isset( $sv['type'] ) && $sv['type'] === 'select' && ! $allow_multiple ){
					if( isset( $sv['value'] ) && $sv['value'] !== '' ){
						$action_args['settings'][ $sk ]['value'] = WPWHPRO()->helpers->serve_first( $sv['value'] );
					}
				}

			}
		}

		$check = WPWHPRO()->webhook->update( $webhook_action, 'action', $args['webhook_group'], $action_args );

		if( $check ){
			$return = true;
		}

		return $return;
	}

	public function delete_flow_action( $args = array() ){
		$return = false;

		if( ! isset( $args['flow_id'] ) || empty( $args['flow_id'] ) ){
			return $return;
		}

		if( ! isset( $args['flow_step'] ) || empty( $args['flow_step'] ) ){
			return $return;
		}

		if( ! isset( $args['webhook_group'] ) || empty( $args['webhook_group'] ) ){
			return $return;
		}

		if( isset( $args['webhook_name'] ) ){
			$webhook_action = sanitize_title( $args['webhook_name'] );
		} else {
			$webhook_action = $this->get_flow_action_url_name( $args['flow_id'], $args['flow_step'] );
		}

		$webhook = WPWHPRO()->webhook->get_hooks( 'action', $args['webhook_group'], $webhook_action );

		if( ! empty( $webhook ) ){
			$check = WPWHPRO()->webhook->unset_hooks( $webhook_action, 'action', $args['webhook_group'] );
			if( $check ){
			    $return = true;
            }
		}

		return $return;
	}

	/**
	 * This function checks prior the execution if there are any
	 * abandoned temp actions available and if so, the will be deleted
	 * to avoid issues and save performance
	 *
	 * @return void
	 */
	private function clean_abandoned_temp_actions(){

		if( ! is_admin() ){
			return;
		}

		$webhooks = WPWHPRO()->webhook->get_hooks( 'action' );
		$ident = 'wpwh-flow-temp-';
		$ident_length = strlen( $ident );

		if( ! empty( $webhooks ) && is_array( $webhooks ) ){
			foreach( $webhooks as $action => $action_data ){

				if( ! isset( $action_data['api_key'] ) || ! is_string( $action_data['api_key'] ) ){
					foreach( $action_data as $action_group => $action_group_data ){
						if( substr( $action_group, 0, $ident_length ) === $ident ){

							if( isset( $action_group_data['date_created'] ) ){
								if( strtotime( $action_group_data['date_created'] ) < strtotime( '-2 days' ) ){
									WPWHPRO()->webhook->unset_hooks( $action_group, 'action', $action );
								}
							}

						}
					}
				} else {
					//compatibility with pre 5.0 versions
					if( substr( $action, 0, $ident_length ) === $ident ){

						if( isset( $action_data['date_created'] ) ){
							if( strtotime( $action_data['date_created'] ) < strtotime( '-2 days' ) ){
								WPWHPRO()->webhook->unset_hooks( $action, 'action' );
							}
						}

					}
				}
			}
		}
	}

	public function process_flow( $flow_id, $args = array() ){
		$return = array(
			'success' => false,
			'msg' => __( 'We had issues processing the flow.', 'wp-webhooks' ),
			'data' => array()
		);

		if( empty( $flow_id ) ){
			$return['msg'] = __( 'We could not process the flow. The flow_id was not given.', 'wp-webhooks' );
			return $return;
		}

		$flow = $this->get_flows( array( 'template' => $flow_id ) );
		if ( ! $flow ) {
			$return['msg'] = __( 'We could not verify the flow. The flow was aborted.', 'wp-webhooks' );
			return $return;
		}

		if( ! isset( $flow->flow_status ) || $flow->flow_status !== 'active' ){
			$return['msg'] = sprintf( __( 'The flow for id %d was aborted as the flow is not active. Logs have been created nevertheless.', 'wp-webhooks' ), $flow_id );
			$return['cancel_processing'] = true;
			return $return;
		}

		if( ! isset( $this->flow_buffer[ $flow_id ] ) ){
			$this->flow_buffer[ $flow_id ] = array();
		}

		$this->flow_buffer[ $flow_id ][] = $args;

		$return['success'] = true;
		$return['msg'] = __( 'The flow was successfully processed.', 'wp-webhooks' );
		$return['data']['flow_id'] = $flow_id;

		return $return;
	}

	public function run_flow( $flow_id, $args = array() ){
		$return = array(
			'success' => false,
			'msg' => __( 'We had issues executing the flow.', 'wp-webhooks' ),
		);

		$flow_id = intval( $flow_id );

		if( empty( $flow_id ) ){
			$return['msg'] = __( 'We could not find the flow id.', 'wp-webhooks' );
			return $return;
		}

		$payload_wrapper = array(
			'success' => true,
			'msg' => __( 'The trigger was successfully executed.', 'wp-webhooks' ),
			'wpwh_status' => 'ok',
			'timestamp' => time(),
			'wpwh_payload' => array()
		);

		//Allow customizations of the wrapper for better error handling and tracking
		if( isset( $args['payload_wrapper'] ) && is_array( $args['payload_wrapper'] ) ){
			$payload_wrapper = array_merge( $payload_wrapper, $args['payload_wrapper'] );
		}
		
		$payload = array();
		if( isset( $args['payload'] ) && is_array( $args['payload'] ) ){
			$payload_wrapper['wpwh_payload'] = $args['payload'];
			$payload = array( 'trigger' => $payload_wrapper );
		}

		$flow = $this->get_flows( array( 'template' => $flow_id ) );

		if( ! empty( $flow ) && is_object( $flow ) ){

			$trigger = false;
			if( isset( $flow->flow_config ) && isset( $flow->flow_config->triggers ) ){
				foreach( $flow->flow_config->triggers as $trigger_data ){
					//streamline data
					$trigger = json_decode( json_encode( $trigger_data ), true );
					break;
				}
			}

			if( ! empty( $trigger ) ){

				$is_trigger_valid = true;

				//Validate conditionals for a given trigger
				if(
					isset( $trigger['conditionals'] )
					&& ! empty( $trigger['conditionals'] )
					&& isset( $trigger['conditionals']['conditions'] )
					&& ! empty( $trigger['conditionals']['conditions'] )
				){
					$is_trigger_valid = $this->validate_trigger_conditions( $trigger['conditionals'], $payload );
				}

				//Add log entry
				$flow_log_data = array(
					'flow_config' => isset( $flow->flow_config ) ? $flow->flow_config : '',
					'flow_payload' => $payload,
				);
				$flow_log_id = $this->add_flow_log( $flow_id, $flow_log_data );

				if( ! empty( $flow_log_id ) && is_numeric( $flow_log_id ) ){

					//In case a trigger is not valid, we still create a log and dispatch it
					//This is important as the trigger bsaically fired and we want to provide the debugging possibility
					if( $is_trigger_valid ){

						$actions = array();
						if( isset( $flow->flow_config ) && isset( $flow->flow_config->actions ) ){
							$actions = (array) $flow->flow_config->actions;
						}
	
						$validated_actions = array();
						if( ! empty( $actions ) ){
							foreach( $actions as $action_key => $action_data ){
	
								$validated_actions[] = array(
									'flow_log_id' => $flow_log_id,
									'current' => $action_key,
									'flow_id' => $flow_id,
									'merge_class_data' => array(
										'flow_log_ids' => array( $flow_log_id ),
									),
								);
							}
						}

						$this->run_flow_actions( $validated_actions );

						$return['success'] = true;
						$return['msg'] = __( 'Flow successfully executed.', 'wp-webhooks' );
					} else {

						$payload['trigger']['success'] = false;
						$payload['trigger']['msg'] = __( 'The flow trigger conditions did not match.', 'wp-webhooks' );
						$payload['trigger']['wpwh_status'] = 'cancelled';

						//Since version 6.1.0, we do not consider failed conditions a valid trigger
						$update_data = array(
							'flow_payload' => $payload,
						);
			
						$this->update_flow_log( $flow_log_id, $update_data );

						$return['msg'] = __( 'The flow was not executed as the trigger conditions did not match.', 'wp-webhooks' );
					}

					
				}

			}

		}

		return $return;
	}

	/**
      * Schedule the execution of an Flow
      * wp_schedule_single_event() 
      *
      * @since 6.0
      * @param integer $timestamp The execution timestamp
      * @param integer $flow_id The Flow ID
      * @param array $args The arguments
      * @return array The response of the request
      */
	  public function schedule_run_flow_actions( $timestamp, $validated_actions ){
        $response = array(
			'success' => false,
			'msg' => __( 'An error occured while scheduling the Flow actions.', 'wp-webhooks' ),
			'data' => array()
		);

        if( ! empty( $timestamp ) ){
            $attributes = array(
                'validated_actions' => $validated_actions,
            );
            
			$response = WPWHPRO()->scheduler->schedule_single_action( array(
                'timestamp' => intval( $timestamp ),
                'hook' => 'wpwh_schedule_run_flow_actions_callback',
                'attributes' => $attributes,
            ) );

			//customize the response message
            if( $response['success'] ){
                $response['msg'] = __( 'The Flow actions have been successfully scheduled.', 'wp-webhooks' );
            }

        }

        return apply_filters( 'wpwhpro/flows/schedule_run_flow_actions', $response, $timestamp, $validated_actions );
    }

	/**
	 * Run the validated actions for a single 
	 * Flow execution
	 *
	 * @since 6.0
	 * @param array $validated_actions
	 * @return void
	 */
	public function run_flow_actions( $validated_actions ){

		//prepare a new queue
		$this->get_flow_async( 'wpwh_install_integrations' )->clear_queue();
	
		foreach( $validated_actions as $vaction ){
			$this->get_flow_async()->push_to_queue( $vaction );
		}

		//dispatch
		$this->get_flow_async()->save()->dispatch();

	}

	public function validate_action_fields( $fields, $payload ){

		$validated_fields = array();

		if( is_array( $fields ) ){
			foreach( $fields as $key => $data ){

				//Only allow arguments
				if( ! isset( $data['type'] ) || $data['type'] !== 'argument' ){
					continue;
				}

				if( isset( $data['value'] ) && $this->is_filled_setting( $data['value'] ) ){

					$field_type = isset( $data['field_type'] ) ? $data['field_type'] : 'string';

					switch( $field_type ){
						case 'repeater':

							$sub_entry = array();

							if( ! empty( $data['value'] ) ){
								foreach( $data['value'] as $entry ){

									if( isset( $entry['key'] ) && isset( $entry['value'] ) ){
										if( isset( $entry['mappings'] ) ){
											$sub_entry[ $entry['key'] ] = $this->validate_mappings( $entry['value'], $entry['mappings'], $payload );
										} else {
											$sub_entry[ $entry['key'] ] = $entry['value'];
										}
										
										$sub_entry[ $entry['key'] ] = $this->maybe_validate_value_format( $sub_entry[ $entry['key'] ] );
									}

								}
							}

							$validated_fields[ $key ] = $sub_entry;

							break;
						case 'list':

							$sub_entry = array();

							if( ! empty( $data['value'] ) ){
								foreach( $data['value'] as $temp_entry_key => $entry ){

									if( isset( $entry['value'] ) ){
										if( isset( $entry['mappings'] ) ){
											$sub_entry[ $temp_entry_key ] = $this->validate_mappings( $entry['value'], $entry['mappings'], $payload );
										} else {
											$sub_entry[ $temp_entry_key ] = $entry['value'];
										}

										$sub_entry[ $temp_entry_key ] = $this->maybe_validate_value_format( $sub_entry[ $temp_entry_key ] );
									}

								}
							}

							$validated_fields[ $key ] = $sub_entry;

							break;
						case 'select':

							if( ! isset( $data['multiple'] ) || $data['multiple'] === false ){
								if( is_array( $data['value'] ) ){
									$data['value'] = WPWHPRO()->helpers->serve_first( $data['value'] );
								}
							}

						case 'text':
						default:

							if( isset( $data['mappings'] ) ){
								$validated_fields[ $key ] = $this->validate_mappings( $data['value'], $data['mappings'], $payload );
							} else {
								$validated_fields[ $key ] = $data['value'];
							}

							$validated_fields[ $key ] = $this->maybe_validate_value_format( $validated_fields[ $key ] );

							break;
					}

				}
			}
		}

		return $validated_fields;
	}

	public function validate_action_conditions( $conditionals, $payload ){

		$is_valid = false;

		if(
			! is_array( $conditionals )
			|| ! isset( $conditionals['relation'] )
			|| empty( $conditionals['relation'] )
			|| ! isset( $conditionals['conditions'] )
			|| empty( $conditionals['conditions'] )
		){
			return $is_valid;
		}

		$relation = $conditionals['relation'];
		$conditions = $conditionals['conditions'];

		if( is_array( $conditions ) ){
			foreach( $conditions as $condition ){

				if( isset( $condition['condition_input']['mappings'] ) ){
					$condition_input = $this->validate_mappings( $condition['condition_input']['value'], $condition['condition_input']['mappings'], $payload );
				} else {
					$condition_input = $condition['condition_input']['value'];
				}

				$condition_operator = $condition['condition_operator']['value'];

				if( isset( $condition['condition_value']['mappings'] ) ){
					$condition_value = $this->validate_mappings( $condition['condition_value']['value'], $condition['condition_value']['mappings'], $payload );
				} else {
					$condition_value = $condition['condition_value']['value'];
				}

				$condition_input = $this->maybe_validate_value_format( $condition_input );
				$condition_value = $this->maybe_validate_value_format( $condition_value );

				switch( $condition_operator ){
					case 'contains':
						if( strpos( $condition_input, $condition_value ) !== FALSE ){
							$is_valid = true;
						} else {
							if( $relation === 'all' ){
								$is_valid = false; //reset current state
								break 2; //condition is false
							}
						}
						break;
					case 'doesnotcontain':
						if( strpos( $condition_input, $condition_value ) === FALSE ){
							$is_valid = true;
						} else {
							if( $relation === 'all' ){
								$is_valid = false; //reset current state
								break 2; //condition is false
							}
						}
						break;
					case 'is':
						if( $condition_input == $condition_value ){
							$is_valid = true;
						} else {
							if( $relation === 'all' ){
								$is_valid = false; //reset current state
								break 2; //condition is false
							}
						}
						break;
					case 'isnot':
						if( $condition_input != $condition_value ){
							$is_valid = true;
						} else {
							if( $relation === 'all' ){
								$is_valid = false; //reset current state
								break 2; //condition is false
							}
						}
						break;
					case 'isempty':
						if( $condition_input === '' ){
							$is_valid = true;
						} else {
							if( $relation === 'all' ){
								$is_valid = false; //reset current state
								break 2; //condition is false
							}
						}
						break;
					case 'isnotempty':
						if( $condition_input !== '' ){
							$is_valid = true;
						} else {
							if( $relation === 'all' ){
								$is_valid = false; //reset current state
								break 2; //condition is false
							}
						}
						break;
					case 'isgreaterthan':
						if( $condition_input > $condition_value ){
							$is_valid = true;
						} else {
							if( $relation === 'all' ){
								$is_valid = false; //reset current state
								break 2; //condition is false
							}
						}
						break;
					case 'isgreaterthanorequalto':
						if( $condition_input >= $condition_value ){
							$is_valid = true;
						} else {
							if( $relation === 'all' ){
								$is_valid = false; //reset current state
								break 2; //condition is false
							}
						}
						break;
					case 'islessthan':
						if( $condition_input < $condition_value ){
							$is_valid = true;
						} else {
							if( $relation === 'all' ){
								$is_valid = false; //reset current state
								break 2; //condition is false
							}
						}
						break;
					case 'islessthanorequalto':
						if( $condition_input <= $condition_value ){
							$is_valid = true;
						} else {
							if( $relation === 'all' ){
								$is_valid = false; //reset current state
								break 2; //condition is false
							}
						}
						break;
				}

			}
		}

		return apply_filters( 'wpwhpro/flows/validate_action_conditions', $is_valid, $conditionals, $payload );
	}

	/**
	 * Validate the conditions of a given Flow trigger execution
	 * 
	 * @since 5.2.4
	 *
	 * @param array $conditionals
	 * @param array $payload
	 * @return bool True if conditions are met, false if not
	 */
	public function validate_trigger_conditions( $conditionals, $payload ){

		$is_valid = false;

		if(
			! is_array( $conditionals )
			|| ! isset( $conditionals['relation'] )
			|| empty( $conditionals['relation'] )
			|| ! isset( $conditionals['conditions'] )
			|| empty( $conditionals['conditions'] )
		){
			return $is_valid;
		}

		$relation = $conditionals['relation'];
		$conditions = $conditionals['conditions'];

		if( is_array( $conditions ) ){
			foreach( $conditions as $condition ){

				if( isset( $condition['condition_input']['mappings'] ) ){
					$condition_input = $this->validate_mappings( $condition['condition_input']['value'], $condition['condition_input']['mappings'], $payload );
				} else {
					$condition_input = $condition['condition_input']['value'];
				}

				$condition_operator = $condition['condition_operator']['value'];

				if( isset( $condition['condition_value']['mappings'] ) ){
					$condition_value = $this->validate_mappings( $condition['condition_value']['value'], $condition['condition_value']['mappings'], $payload );
				} else {
					$condition_value = $condition['condition_value']['value'];
				}

				switch( $condition_operator ){
					case 'contains':
						if( strpos( $condition_input, $condition_value ) !== FALSE ){
							$is_valid = true;
						} else {
							if( $relation === 'all' ){
								$is_valid = false; //reset current state
								break 2; //condition is false
							}
						}
						break;
					case 'doesnotcontain':
						if( strpos( $condition_input, $condition_value ) === FALSE ){
							$is_valid = true;
						} else {
							if( $relation === 'all' ){
								$is_valid = false; //reset current state
								break 2; //condition is false
							}
						}
						break;
					case 'is':
						if( $condition_input == $condition_value ){
							$is_valid = true;
						} else {
							if( $relation === 'all' ){
								$is_valid = false; //reset current state
								break 2; //condition is false
							}
						}
						break;
					case 'isnot':
						if( $condition_input != $condition_value ){
							$is_valid = true;
						} else {
							if( $relation === 'all' ){
								$is_valid = false; //reset current state
								break 2; //condition is false
							}
						}
						break;
					case 'isempty':
						if( $condition_input === '' ){
							$is_valid = true;
						} else {
							if( $relation === 'all' ){
								$is_valid = false; //reset current state
								break 2; //condition is false
							}
						}
						break;
					case 'isnotempty':
						if( $condition_input !== '' ){
							$is_valid = true;
						} else {
							if( $relation === 'all' ){
								$is_valid = false; //reset current state
								break 2; //condition is false
							}
						}
						break;
					case 'isgreaterthan':
						if( $condition_input > $condition_value ){
							$is_valid = true;
						} else {
							if( $relation === 'all' ){
								$is_valid = false; //reset current state
								break 2; //condition is false
							}
						}
						break;
					case 'isgreaterthanorequalto':
						if( $condition_input >= $condition_value ){
							$is_valid = true;
						} else {
							if( $relation === 'all' ){
								$is_valid = false; //reset current state
								break 2; //condition is false
							}
						}
						break;
					case 'islessthan':
						if( $condition_input < $condition_value ){
							$is_valid = true;
						} else {
							if( $relation === 'all' ){
								$is_valid = false; //reset current state
								break 2; //condition is false
							}
						}
						break;
					case 'islessthanorequalto':
						if( $condition_input <= $condition_value ){
							$is_valid = true;
						} else {
							if( $relation === 'all' ){
								$is_valid = false; //reset current state
								break 2; //condition is false
							}
						}
						break;
				}

			}
		}

		return apply_filters( 'wpwhpro/flows/validate_trigger_conditions', $is_valid, $conditionals, $payload );
	}

	public function validate_mappings( $string, $mapping, $payload ){

		if( is_array( $mapping ) ){
			foreach( $mapping as $tag => $data ){

				if( isset( $data['trigger'] ) ){
					$mapping_array = array(
						'trigger',
					);
				} elseif( isset( $data['action'] ) ){

					if( $data['action'] === 'common' ){
						$common_tag = '';
						if( isset( $data['mapping'] ) && is_array( $data['mapping'] ) ){
							foreach( $data['mapping'] as $key ){
								$common_tag = $key;
								break;
							}
						}	

						$string = str_replace( '{{' . $tag . '}}', $this->get_common_tag_value( $common_tag ), $string );
						continue;
					} else {
						$mapping_array = array(
							'actions',
							$data['action'],
						);
					}

				}

				if( isset( $data['mapping'] ) && is_array( $data['mapping'] ) ){
					foreach( $data['mapping'] as $key ){
						$mapping_array[] = $key;
					}
				}

				$value = $this->locate_value( $mapping_array, $payload );

				//Convert bool values to preserve them
				if( is_bool( $value ) ){
					if( $value ){
						$value = 'true';
					} else {
						$value = 'false';
					}
				}

				$string = str_replace( '{{' . $tag . '}}', $value, $string );

			}
		}

		return $string;
	}

	public function locate_value( $mapping, $payload ){
		$string = '';

		//since the support of booleans in 6.1.0, we had to switch from next() to count
		$key_count = count( $mapping );
		foreach( $mapping as $key ){
			$key_count--;

			//Follow the new structure of 6.1.0
			if( is_array( $payload ) && isset( $payload['wpwh_payload'] ) ){
				$payload = $payload['wpwh_payload'];
			}

			//shorten the circle if the full payload is given
			if( $key === 'fullstepdata' && $key_count <= 0 ){
				$string = ( is_array( $payload ) || is_object( $payload ) ) ? json_encode( $payload ) : $payload;
				continue;
			}

			if( is_array( $payload ) && isset( $payload[ $key ] ) ){
				$payload = $payload[ $key ];

				if( $key_count <= 0 ){
					$string = ( is_array( $payload ) || is_object( $payload ) ) ? json_encode( $payload ) : $payload;
					break;
				}
			} elseif( is_object( $payload ) && isset( $payload->{$key} ) ){
				$payload = $payload->{$key};

				if( $key_count <= 0 ){
					$string = ( is_array( $payload ) || is_object( $payload ) ) ? json_encode( $payload ) : $payload;
					break;
				}
			}

		}

		return $string;
	}

	public function is_filled_setting( $value ){
		$return = false;

		if( is_string( $value ) && $value !== '' ){
			$return = true;
		} elseif( is_array( $value ) ) {

			if( count( $value ) > 1 ){
				$return = true;
			} else {
				$first_data = reset( $value );
				if( $first_data !== '' && $first_data !== false ){
					$return = true;
				}
			}

		}

		return $return;
	}

	/**
	 * Validate the flow values to show the real value
	 * This counts as well for the test action
	 *
	 * @since 4.3.4
	 *
	 * @param string $validator
	 * @param array $flow_config
	 * @param boolean $validate_all
	 * @return array
	 */
	public function validate_flow_values( $validator, $flow_config, $validate_all = false ){
		$fields_to_validate = array(
			'value',
			'variableData',
		);

		if( is_array( $flow_config ) ){
			foreach( $flow_config as $fk => $fv ){

				if( is_string( $fv ) ){

					if( $validate_all || in_array( $fk, $fields_to_validate ) ){

						switch( $validator ){
							case 'addslashes':
								$flow_config[ $fk ] = addslashes( $fv );
								break;
							case 'stripslashes':
								$flow_config[ $fk ] = stripslashes( $fv );
								break;
						}

					}

				} elseif( is_array( $fv ) ){

					if( $validate_all || in_array( $fk, $fields_to_validate ) ){
						$flow_config[ $fk ] = $this->validate_flow_values( $validator, $fv, true );
					} else {
						$flow_config[ $fk ] = $this->validate_flow_values( $validator, $fv );
					}

				}



			}
		}

		return $flow_config;
	}

	public function get_flow_trigger_url_name( $flow_id ){
		$flow_name = 'wpwh-flow-' . intval( $flow_id );

		return apply_filters( 'wpwhpro/flows/logs/get_flow_trigger_url_name', $flow_name );
	}

	public function get_flow_action_url_name( $flow_id, $step_id ){
		$flow_name = 'wpwh-flow-' . intval( $flow_id ) . '-' . sanitize_title( $step_id );

		return apply_filters( 'wpwhpro/flows/get_flow_action_url_name', $flow_name, $flow_id, $step_id );
	}

	/**
	 * Maybe validate a string back to its original
	 * data format. If the string differs, we allow 
	 * doublequote wrapping
	 *
	 * @since 6.1.0
	 * @param string $string
	 * @return string
	 */
	public function maybe_validate_value_format( $string ){

		if( $string ){
			
			$original_string = $string;
			$string = WPWHPRO()->helpers->get_original_data_format( $string );

			//If the string is still the same, let's check for preserved, valdiated strings
			if( $original_string === $string ){
				$unquoted_string = WPWHPRO()->helpers->undoublequote_string( $string );
				$unquoted_string_data_formatted = WPWHPRO()->helpers->get_original_data_format( $unquoted_string );

				//If the unquoted string differs after validation, we knwo it can be validated and therefore can be unquoted
				if( $unquoted_string !== $unquoted_string_data_formatted ){
					/**
					 * The double quote setup allows you to wrap a string in double quotes to prevent it from being
					 * auto-converted into its related data format.
					 */
					$string = WPWHPRO()->helpers->undoublequote_string( $string );
				}
			}
			
		}

		return apply_filters( 'wpwhpro/flows/logs/maybe_validate_value_format', $string );
	}

}
