<?php

/**
 * Class WP_Webhooks_Pro_Run
 *
 * Thats where we bring the plugin to life
 *
 * @since 1.0.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */

class WP_Webhooks_Pro_Run{

	/**
	 * The main page name for our admin page
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private $page_name;

	/**
	 * The main page title for our admin page
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private $page_title;

	/**
	 * Our WP_Webhooks_Pro_Run constructor.
	 */
	function __construct(){
		$this->page_name    = WPWHPRO()->settings->get_page_name();
		$this->page_title   = WPWHPRO()->settings->get_page_title();
		$this->add_hooks();
		$this->execute_features();
	}

	/**
	 * Define all of our general hooks
	 */
	private function add_hooks(){

		add_action( 'plugin_action_links_' . WPWHPRO_PLUGIN_BASE, array( $this, 'plugin_action_links') );
		add_filter( 'admin_footer_text', array( $this, 'display_footer_information' ), 50, 2 );
		add_action( 'in_admin_header', array( $this, 'prevent_admin_notices' ) );

		add_action( 'admin_enqueue_scripts',    array( $this, 'enqueue_scripts_and_styles' ) );
		add_action( 'admin_menu', array( $this, 'add_user_submenu' ), 150 );
		add_filter( 'submenu_file', array( $this, 'filter_active_wpwh_submenu_page' ), 150, 2 );
		add_action( 'admin_init', array( $this, 'maybe_redirect_wpwh_submenu_items' ), 150, 2 );
		add_filter( 'wpwhpro/helpers/throw_admin_notice_bootstrap', array( $this, 'throw_admin_notice_bootstrap' ), 100, 1 );

		// Ajax related
		add_action( 'wp_ajax_ironikus_remove_webhook_trigger',  array( $this, 'ironikus_remove_webhook_trigger' ) );
		add_action( 'wp_ajax_ironikus_remove_webhook_action',  array( $this, 'ironikus_remove_webhook_action' ) );
		add_action( 'wp_ajax_ironikus_change_status_webhook_action',  array( $this, 'ironikus_change_status_webhook_action' ) );
		add_action( 'wp_ajax_ironikus_test_webhook_trigger',  array( $this, 'ironikus_test_webhook_trigger' ) );
		add_action( 'wp_ajax_ironikus_resend_flow_log',  array( $this, 'ironikus_resend_flow_log' ) );
		add_action( 'wp_ajax_ironikus_retry_flow_log',  array( $this, 'ironikus_retry_flow_log' ) );
		add_action( 'wp_ajax_ironikus_save_webhook_trigger_settings',  array( $this, 'ironikus_save_webhook_trigger_settings' ) );
		add_action( 'wp_ajax_ironikus_save_webhook_action_settings',  array( $this, 'ironikus_save_webhook_action_settings' ) );
		add_action( 'wp_ajax_wp_webhooks_validate_field_query',  array( $this, 'wp_webhooks_validate_field_query' ) );


		// Load admin page tabs
		add_filter( 'wpwhpro/admin/settings/menu_data', array( $this, 'add_main_settings_tabs' ), 10 );
		add_action( 'wpwhpro/admin/settings/menu/place_content', array( $this, 'add_main_settings_content' ), 10 );

		// Validate settings
		add_action( 'admin_init',  array( $this, 'ironikus_save_main_settings' ) );

		//Reset wp webhooks
		add_action( 'admin_init', array( $this, 'reset_wpwhpro_data' ), 10 );

		//Setup for the no-conflict mode
		if ( is_admin() ){

			$deactivate_no_conflict_mode = get_option( 'wpwhpro_deactivate_no_conflict_mode' );

			if( empty( $deactivate_no_conflict_mode ) || $deactivate_no_conflict_mode !== 'yes' ){
				add_action( 'wp_print_scripts', array( $this, 'no_conflict_mode_scripts' ), 1000 );
				add_action( 'admin_print_footer_scripts', array( $this, 'no_conflict_mode_scripts' ), 9 );
	
				add_action( 'wp_print_styles', array( $this, 'no_conflict_mode_styles' ), 1000 );
				add_action( 'admin_print_styles', array( $this, 'no_conflict_mode_styles' ), 1 );
				add_action( 'admin_print_footer_scripts', array( $this, 'no_conflict_mode_styles' ), 1 );
				add_action( 'admin_footer', array( $this, 'no_conflict_mode_styles' ), 1 );
			}
		}

	}

	/**
	 * Execute the plugin related features
	 *
	 * @since 4.2.3
	 * @return void
	 */
	private function execute_features(){

		WPWHPRO()->webhook->execute();
		WPWHPRO()->fields->execute();
		WPWHPRO()->integrations->execute();
		WPWHPRO()->logs->execute();
		WPWHPRO()->auth->execute();
		WPWHPRO()->data_mapping->execute();
		WPWHPRO()->extensions->execute();
		WPWHPRO()->whitelabel->execute();
		WPWHPRO()->http->execute();
		WPWHPRO()->flows->execute();
		WPWHPRO()->license->execute();
		WPWHPRO()->migrate->execute();
		WPWHPRO()->tools->execute();
		WPWHPRO()->wizard->execute();
		WPWHPRO()->usage->execute();

	}

	/**
	 * Plugin action links.
	 *
	 * Adds action links to the plugin list table
	 *
	 * Fired by `plugin_action_links` filter.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $links An array of plugin action links.
	 *
	 * @return array An array of plugin action links.
	 */
	public function plugin_action_links( $links ) {
		$settings_link = sprintf( '<a href="%s">%s</a>', admin_url( 'options-general.php?page=' . $this->page_name ), __( 'Settings', 'wp-webhooks' ) );

		array_unshift( $links, $settings_link );

		$links['our_shop'] = sprintf( '<a href="%s" target="_blank" style="font-weight:700;color:#f1592a;">%s</a>', 'https://wp-webhooks.com/?utm_source=wp-webhooks-pro&utm_medium=plugin-overview-shop-button&utm_campaign=WP%20Webhooks%20Pro', __( 'Our Shop', 'wp-webhooks' ) );

		return $links;
	}

	/**
	 * Add footer information about our plugin
	 *
	 * @since 4.2.1
	 * @access public
	 *
	 * @param string The current footer text
	 *
	 * @return string Our footer text
	 */
	public function display_footer_information( $text ) {

		if( WPWHPRO()->helpers->is_page( $this->page_name ) ){
			$text = sprintf(
				__( '%1$s version %2$s', 'wp-webhooks' ),
				'<strong>' . $this->page_title . '</strong>',
				'<strong>' . WPWHPRO_VERSION . '</strong>'
			);
		}

		return $text;
	}

	/**
	 * Prevent plugin notices from other plugins to not interfere with the layout
	 * of our plugin.
	 *
	 * @since 5.2.4
	 * @access public
	 *
	 * @return void
	 */
	public function prevent_admin_notices() {

		if( WPWHPRO()->helpers->is_page( $this->page_name ) ){
			remove_all_actions( 'network_admin_notices' );
			remove_all_actions( 'user_admin_notices' );
			remove_all_actions( 'admin_notices' );

			//only enqueue our notices
			add_action( 'admin_notices', array( WPWHPRO()->license, 'ironikus_throw_admin_notices' ), 100 );
			if( function_exists('wpwhpro_free_version_custom_notice') ){
				add_action( 'admin_notices', 'wpwhpro_free_version_custom_notice', 100 );
			}
		}

	}

	/**
	 * ######################
	 * ###
	 * #### SCRIPTS & STYLES
	 * ###
	 * ######################
	 */

	/**
	 * Register all necessary scripts and styles
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts_and_styles() {
		if( WPWHPRO()->helpers->is_page( $this->page_name ) && is_admin() ) {

			$is_dev_mode = WPWHPRO()->helpers->is_dev();
			$is_flow = ( isset( $_GET['wpwhprovrs'] ) && $_GET['wpwhprovrs'] === 'flows' && isset( $_GET['flow_id'] ) ) ? true : false;
			$is_flows_main = ( isset( $_GET['wpwhprovrs'] ) && $_GET['wpwhprovrs'] === 'flows' && ! isset( $_GET['flow_id'] ) ) ? true : false;
			$ajax_nonce = wp_create_nonce( md5( $this->page_name ) );
			$language = get_locale();

			wp_enqueue_style( 'wpwhpro-google-fonts', 'https://fonts.googleapis.com/css2?family=Mulish:wght@300;400;700&family=Poppins:wght@500&display=swap', array(), null );

			// wp_enqueue_style( 'wpwhpro-admin-styles-old', WPWHPRO_PLUGIN_URL . 'core/includes/assets/dist/css/styles.min.css', array(), WPWHPRO_VERSION, 'all' );

			if( $is_flow ){
				wp_enqueue_style( 'wpwhpro-codemirror', WPWHPRO_PLUGIN_URL . 'core/includes/assets/dist/css/codemirror' . ( $is_dev_mode ? '' : '.min' ) . '.css', array(), WPWHPRO_VERSION, 'all' );
				wp_enqueue_style( 'wpwhpro-sweetalert2', WPWHPRO_PLUGIN_URL . 'core/includes/assets/dist/css/sweetalert2.min.css', array(), WPWHPRO_VERSION, 'all' );
				wp_enqueue_style( 'wpwhpro-vue-select', WPWHPRO_PLUGIN_URL . 'core/includes/assets/dist/css/vue-select.css', array(), WPWHPRO_VERSION, 'all' );
			}

			wp_enqueue_style( 'wpwhpro-admin-styles', WPWHPRO_PLUGIN_URL . 'core/includes/assets/dist/css/admin-styles' . ( $is_dev_mode ? '' : '.min' ) . '.css', array(), WPWHPRO_VERSION, 'all' );

			wp_enqueue_script( 'jquery-ui-sortable');
			wp_enqueue_editor();
			wp_enqueue_media();

			if( $is_flow ){
				wp_enqueue_script( 'wpwhpro-flows-vendor', WPWHPRO_PLUGIN_URL . 'core/includes/assets/dist/js/flows-vendor' . ( $is_dev_mode ? '' : '.min' ) . '.js', array( 'jquery' ), WPWHPRO_VERSION, true );
				wp_enqueue_script( 'wpwhpro-flows', WPWHPRO_PLUGIN_URL . 'core/includes/assets/dist/js/flows' . ( $is_dev_mode ? '' : '.min' ) . '.js', array( 'jquery' ), WPWHPRO_VERSION, true, true );
			} else {
				wp_enqueue_script( 'wpwhpro-admin-vendors', WPWHPRO_PLUGIN_URL . 'core/includes/assets/dist/js/admin-vendor' . ( $is_dev_mode ? '' : '.min' ) . '.js', array( 'jquery' ), WPWHPRO_VERSION, true );
				wp_enqueue_script( 'wpwhpro-admin-scripts', WPWHPRO_PLUGIN_URL . 'core/includes/assets/dist/js/admin-scripts' . ( $is_dev_mode ? '' : '.min' ) . '.js', array( 'jquery' ), WPWHPRO_VERSION, true );
			}

			wp_localize_script( 'wpwhpro-admin-scripts', 'ironikus', array(
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => $ajax_nonce,
				'language' => $language,
			));

			if( $is_flow ) {
				wp_localize_script( 'wpwhpro-flows', 'ironikusflows', array(
					'ajax_url'   => admin_url( 'admin-ajax.php' ),
					'ajax_nonce' => $ajax_nonce,
					'plugin_url' => WPWHPRO_PLUGIN_URL,
					'language' => $language,
				));
			}

			if( $is_flows_main ) {
				wp_localize_script( 'wpwhpro-admin-scripts', 'ironikusflows', array(
					'ajax_url'   => admin_url( 'admin-ajax.php' ),
					'ajax_nonce' => $ajax_nonce,
					'plugin_url' => WPWHPRO_PLUGIN_URL,
					'language' => $language,
				));
			}

			// wp_enqueue_script( 'wpwhpro-admin-scripts-old', WPWHPRO_PLUGIN_URL . 'core/includes/assets-old/dist/js/admin-scripts.js', array( 'jquery' ), WPWHPRO_VERSION, true );
		}
	}

	/**
	 * Register the bootstrap styling for posts on our own settings page
	 *
	 * @since    1.0.0
	 */
	public function throw_admin_notice_bootstrap( $bool ) {
		if( WPWHPRO()->helpers->is_page( $this->page_name ) && is_admin() ) {
			$bool = true;
		}

		return $bool;
	}

	/*
     * Functionality to save the main settings of the settings page
     */
	public function ironikus_save_main_settings(){

        if( ! is_admin() || ! WPWHPRO()->helpers->is_page( $this->page_name ) ){
			return;
		}

		if( ! isset( $_POST['wpwh_settings_submit'] ) ){
			return;
		}

		$settings_nonce_data = WPWHPRO()->settings->get_settings_nonce();

		if ( ! check_admin_referer( $settings_nonce_data['action'], $settings_nonce_data['arg'] ) ){
			return;
		}

		if( ! WPWHPRO()->helpers->current_user_can( WPWHPRO()->settings->get_admin_cap( 'wpwh-save-settings' ), 'wpwhpro-page-settings-save' ) ){
			return;
		}

		$current_url = WPWHPRO()->helpers->get_current_url();

		WPWHPRO()->settings->save_settings( $_POST );

		wp_redirect( $current_url );
		exit;

    }

	/**
	 * ######################
	 * ###
	 * #### AJAX
	 * ###
	 * ######################
	 */

    /*
     * Remove the action via ajax
     */
	public function ironikus_remove_webhook_action(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $webhook_group = isset( $_REQUEST['webhook_group'] ) ? sanitize_title( $_REQUEST['webhook_group'] ) : '';
        $webhook        = isset( $_REQUEST['webhook'] ) ? sanitize_title( $_REQUEST['webhook'] ) : '';
		$response       = array( 'success' => false );

		$check = WPWHPRO()->webhook->unset_hooks( $webhook, 'action', $webhook_group );
		if( $check ){
			$response['success'] = true;
		}

        echo json_encode( $response );
		die();
    }

    /*
     * Change the status of the action via ajax
     */
	public function ironikus_change_status_webhook_action(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $webhook        = isset( $_REQUEST['webhook'] ) ? sanitize_title( $_REQUEST['webhook'] ) : '';
        $webhook_group = isset( $_REQUEST['webhook_group'] ) ? sanitize_text_field( $_REQUEST['webhook_group'] ) : '';
        $webhook_status = isset( $_REQUEST['webhook_status'] ) ? sanitize_title( $_REQUEST['webhook_status'] ) : '';
        $webhook_type = isset( $_REQUEST['webhook_type'] ) ? sanitize_title( $_REQUEST['webhook_type'] ) : '';
		$response       = array( 'success' => false, 'new_status' => '', 'new_status_name' => '' );

		$new_status = null;
		$new_status_name = null;
		switch( $webhook_status ){
			case 'active':
				$new_status = 'inactive';
				$new_status_name = 'Inactive';
				break;
			case 'inactive':
				$new_status = 'active';
				$new_status_name = 'Active';
				break;
		}

		if( ! empty( $webhook ) ){

			if( $webhook_type === 'send' ){
				$check = WPWHPRO()->webhook->update( $webhook, 'trigger', $webhook_group, array(
					'status' => $new_status
				) );
			} else {
				$check = WPWHPRO()->webhook->update( $webhook, 'action', $webhook_group, array(
					'status' => $new_status
				) );
			}

			if( $check ){
				$response['success'] = true;
				$response['new_status'] = $new_status;
				$response['new_status_name'] = $new_status_name;
			}
		}

        echo json_encode( $response );
		die();
    }

    /*
     * Remove the trigger via ajax
     */
	public function ironikus_remove_webhook_trigger(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $webhook        = isset( $_REQUEST['webhook'] ) ? sanitize_title( $_REQUEST['webhook'] ) : '';
        $webhook_group  = isset( $_REQUEST['webhook_group'] ) ? sanitize_text_field( $_REQUEST['webhook_group'] ) : '';
		$webhooks       = WPWHPRO()->webhook->get_hooks( 'trigger', $webhook_group );
		$response       = array( 'success' => false );

		if( isset( $webhooks[ $webhook ] ) ){
			$check = WPWHPRO()->webhook->unset_hooks( $webhook, 'trigger', $webhook_group );
			if( $check ){
			    $response['success'] = true;
            }
		}


        echo json_encode( $response );
		die();
    }

    /*
     * Functionality to load all of the available demo webhook triggers
     */
	public function ironikus_test_webhook_trigger(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $webhook            = isset( $_REQUEST['webhook'] ) ? sanitize_title( $_REQUEST['webhook'] ) : '';
        $webhook_group      = isset( $_REQUEST['webhook_group'] ) ? sanitize_text_field( $_REQUEST['webhook_group'] ) : '';
        $webhook_callback   = isset( $_REQUEST['webhook_callback'] ) ? sanitize_text_field( $_REQUEST['webhook_callback'] ) : '';
		$webhooks           = WPWHPRO()->webhook->get_hooks( 'trigger', $webhook_group );
        $response           = array( 'success' => false );

		if( isset( $webhooks[ $webhook ] ) ){
			$data = WPWHPRO()->integrations->get_trigger_demo( $webhook_group, array(
				'webhook' => $webhook,
				'webhooks' => $webhooks,
				'webhook_group' => $webhook_group,
			) );

			if( ! empty( $webhook_callback ) ){
				$data = apply_filters( 'ironikus_demo_' . $webhook_callback, $data, $webhook, $webhook_group, $webhooks[ $webhook ] );
			}

			$response_data = WPWHPRO()->webhook->post_to_webhook( $webhooks[ $webhook ], $data, array( 'blocking' => true ), true );

			if ( ! empty( $response_data ) ) {
				$response['data']       = $response_data;
				$response['success']    = true;
			}
		}

        echo json_encode( $response );
		die();
    }

    /*
     * Resend a given flow log
     */
	public function ironikus_resend_flow_log(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $flow_log_id        = isset( $_REQUEST['flow_log_id'] ) ? intval( $_REQUEST['flow_log_id'] ) : 0;

        $response           = array( 'success' => false );

		if( ! empty( $flow_log_id ) ){
			$flow_log = WPWHPRO()->flows->get_flow_log( $flow_log_id );
			$flow_id = 0;
			$flow_trigger_data = array();

			if( 
				! empty( $flow_log ) 
				&& isset( $flow_log->flow_payload )
				&& isset( $flow_log->flow_payload['trigger'] )
				&& ! empty( $flow_log->flow_payload['trigger'] )
			){
				$flow_trigger_data = $flow_log->flow_payload['trigger'];

				//Make sure we access the actual payload
				if( isset( $flow_trigger_data['wpwh_payload'] ) ){
					$flow_trigger_data = $flow_trigger_data['wpwh_payload'];
				}
			}

			if( 
				! empty( $flow_log ) 
				&& isset( $flow_log->flow_id )
				&& ! empty( $flow_log->flow_id )
			){
				$flow_id = intval( $flow_log->flow_id );
			}	

			$response_data = WPWHPRO()->flows->run_flow( $flow_id, array(
				'payload' => $flow_trigger_data,
			) );	

			if ( ! empty( $response_data ) ) {
				$response['data']       = $response_data;
				$response['success']    = true;
			}
		}

        echo json_encode( $response );
		die();
    }

    /*
     * Retry a given flow log based on the first cancelled action/trigger
	 * 
	 * @since 6.1.0
     */
	public function ironikus_retry_flow_log(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $flow_log_id        = isset( $_REQUEST['flow_log_id'] ) ? intval( $_REQUEST['flow_log_id'] ) : 0;

        $response           = array( 'success' => false );

		if( ! empty( $flow_log_id ) ){
			$flow_log = WPWHPRO()->flows->get_flow_log( $flow_log_id );
			$flow_id = 0;

			if( 
				! empty( $flow_log ) 
				&& isset( $flow_log->flow_id )
				&& ! empty( $flow_log->flow_id )
			){
				$flow_id = intval( $flow_log->flow_id );
			}	

			$actions = array();
			if( isset( $flow_log->flow_config ) && isset( $flow_log->flow_config['actions'] ) ){
				$actions = (array) $flow_log->flow_config['actions'];
			}

			$actions_payloads = array();
			if( isset( $flow_log->flow_payload ) && isset( $flow_log->flow_payload['actions'] ) ){
				$actions_payloads = (array) $flow_log->flow_payload['actions'];
			}

			$force_continue = false;

			$validated_actions = array();
			if( ! empty( $actions ) ){
				foreach( $actions as $action_key => $action_data ){

					$payload_data = ( isset( $actions_payloads[ $action_key ] ) ) ? $actions_payloads[ $action_key ] : array();

					//Make sure we onlt support logs from 6.1.0 onward
					if( ! is_array( $payload_data ) || ! isset( $payload_data['wpwh_payload'] ) ){
						//Make sure we only skip if the flow isn't forced
						if( ! $force_continue ){
							continue;
						}
					}

					if( isset( $payload_data['wpwh_status'] ) && $payload_data['wpwh_status'] === 'cancelled' ){
						$force_continue = true;
					} else {

						//Make sure we only skip if the flow isn't forced
						if( ! $force_continue ){
							continue;
						}
					}

					$validated_actions[] = array(
						'flow_log_id' => $flow_log_id,
						'current' => $action_key,
						'flow_id' => $flow_id,
						'merge_class_data' => array(
							'flow_log_ids' => array( $flow_log_id ),
						),
					);
				}

				WPWHPRO()->flows->run_flow_actions( $validated_actions );	

				$response['msg']       = __( 'The retry was initiated successfully.', 'wp-webhooks' );
				$response['success']    = true;
			} else {
				$response['msg']       = __( 'No actions given that we could retry.', 'wp-webhooks' );
			}

		}

        echo json_encode( $response );
		die();
    }

    /*
     * Functionality to load all of the available demo webhook triggers
     */
	public function ironikus_save_webhook_trigger_settings(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $webhook            = isset( $_REQUEST['webhook_id'] ) ? sanitize_title( $_REQUEST['webhook_id'] ) : '';
        $webhook_group      = isset( $_REQUEST['webhook_group'] ) ? sanitize_text_field( $_REQUEST['webhook_group'] ) : '';
		$trigger_settings   = ( isset( $_REQUEST['trigger_settings'] ) && ! empty( $_REQUEST['trigger_settings'] ) ) ? $_REQUEST['trigger_settings'] : '';
        $response           = array( 'success' => false );

		parse_str( $trigger_settings, $trigger_settings_data );

		if( ! empty( $webhook_group ) && ! empty( $webhook ) ){
		    $check = WPWHPRO()->webhook->update( $webhook, 'trigger', $webhook_group, array(
                'settings' => $trigger_settings_data
            ) );

		    if( ! empty( $check ) ){
		        $response['success'] = true;
            }
        }

        echo json_encode( $response );
		die();
    }

    /*
     * Functionality to save all available webhook actions
     */
	public function ironikus_save_webhook_action_settings(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $webhook            = isset( $_REQUEST['webhook_id'] ) ? sanitize_title( $_REQUEST['webhook_id'] ) : '';
		$webhook_group      = isset( $_REQUEST['webhook_group'] ) ? sanitize_text_field( $_REQUEST['webhook_group'] ) : '';
        $action_settings   = ( isset( $_REQUEST['action_settings'] ) && ! empty( $_REQUEST['action_settings'] ) ) ? $_REQUEST['action_settings'] : '';
        $response           = array( 'success' => false );

		parse_str( $action_settings, $action_settings_data );

		if( ! empty( $webhook ) ){
		    $check = WPWHPRO()->webhook->update( $webhook, 'action', $webhook_group, array(
                'settings' => $action_settings_data
            ) );

		    if( ! empty( $check ) ){
		        $response['success'] = true;
            }
        }

        echo json_encode( $response );
		die();
    }

    /*
     * Functionality to save all available webhook actions
     */
	public function wp_webhooks_validate_field_query(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $webhook_type = isset( $_REQUEST['webhook_type'] ) ? sanitize_title( $_REQUEST['webhook_type'] ) : '';
		$webhook_group      = isset( $_REQUEST['webhook_group'] ) ? sanitize_text_field( $_REQUEST['webhook_group'] ) : '';
        $webhook_integration   = ( isset( $_REQUEST['webhook_integration'] ) && ! empty( $_REQUEST['webhook_integration'] ) ) ? sanitize_title( $_REQUEST['webhook_integration'] ) : '';
        $webhook_field   = ( isset( $_REQUEST['webhook_field'] ) && ! empty( $_REQUEST['webhook_field'] ) ) ? sanitize_title( $_REQUEST['webhook_field'] ) : '';
        $field_search   = ( isset( $_REQUEST['field_search'] ) && ! empty( $_REQUEST['field_search'] ) ) ? esc_sql( $_REQUEST['field_search'] ) : '';
        $paged = ( isset( $_REQUEST['page'] ) && ! empty( $_REQUEST['page'] ) ) ? intval( $_REQUEST['page'] ) : 1;
        $selected = ( isset( $_REQUEST['selected'] ) && ! empty( $_REQUEST['selected'] ) ) ? $_REQUEST['selected'] : '';
        $settings = array();
        $default_settings = array();
		$response           = array(
			'success' => false,
			'data' => array(
				'total' => 0,
				'choices' => array(),
			)
		);
		$endpoint = null;

		if( ! empty( $webhook_type ) && ! empty( $webhook_group ) && ! empty( $webhook_integration ) && ! empty( $webhook_field ) ){
		    switch( $webhook_type ){
				case 'action':

					//Add required settings
					$settings = array_merge( $settings, WPWHPRO()->settings->get_required_action_settings() );

					$endpoint = WPWHPRO()->integrations->get_actions( $webhook_integration, $webhook_group );
					break;
				case 'trigger':

					//Add required settings
					$settings = array_merge( $settings, WPWHPRO()->settings->get_required_trigger_settings() );
					$default_settings = WPWHPRO()->settings->get_default_trigger_settings();

					$endpoint = WPWHPRO()->integrations->get_triggers( $webhook_integration, $webhook_group );
					break;
			}

			if( ! empty( $endpoint ) ){

				if( isset( $endpoint['settings'] ) ){

					//Load the default settings if available
					if( 
						isset( $endpoint['settings']['load_default_settings'] )
						&& ! empty( $endpoint['settings']['load_default_settings'] )
					){
						$settings = array_merge( $settings, $default_settings );
					}

					//Map the endpoint-specific settings
					if(
						isset( $endpoint['settings']['data'] )
						&& is_array( $endpoint['settings']['data'] )
					){
						$settings = array_merge( $settings, $endpoint['settings']['data'] );
					}

				}

				if( isset( $settings[ $webhook_field ] ) ){
					$query_items = WPWHPRO()->fields->get_query_items( $settings[ $webhook_field ], $args = array(
						's' => $field_search,
						'paged' => $paged,
						'selected' => $selected,
					) );

					$response['data']['total'] = $query_items['total'];
					$response['data']['per_page'] = $query_items['per_page'];
					$response['data']['item_count'] = $query_items['item_count'];

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

        echo json_encode( $response );
		die();
    }

	/**
	 * ######################
	 * ###
	 * #### MENU TEMPLATE ITEMS
	 * ###
	 * ######################
	 */

	/**
	 * Add our custom admin user page
	 */
	public function add_user_submenu(){
		$menu_position = get_option( 'wpwhpro_show_sub_menu' );

		if( ! empty( $menu_position ) && $menu_position == 'yes' ){
			add_submenu_page(
				'options-general.php',
				__( $this->page_title, 'wp-webhooks' ),
				__( $this->page_title, 'wp-webhooks' ),
				WPWHPRO()->settings->get_admin_cap( 'admin-add-submenu-page-item' ),
				$this->page_name,
				array( $this, 'render_admin_submenu_page' )
			);
		} else {
			add_menu_page(
				__( $this->page_title, 'wp-webhooks' ),
				__( $this->page_title, 'wp-webhooks' ),
				WPWHPRO()->settings->get_admin_cap( 'admin-add-menu-page-item' ),
				$this->page_name,
				array( $this, 'render_admin_submenu_page' ) ,
				WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/logo-menu-wp-webhooks.svg',
				'81.025'
			);

			/**
			 * Originally called within /core/includes/partials/wpwhpro-page-display.php,
			 * but used here to re-validate the available menu items dynamically
			 */
			$menu_endpoints = apply_filters( 'wpwhpro/admin/settings/menu_data', array() );
			if( is_array( $menu_endpoints ) && ! empty( $menu_endpoints ) ){
				foreach( $menu_endpoints as $endpoint_slug => $endpoint_data ){

					//Skip the whitelabel tab
					if( $endpoint_slug === 'whitelabel' ){
						continue;
					}

					$sub_page_title = ( is_array( $endpoint_data ) ) ? $endpoint_data['label'] : $endpoint_data;

					add_submenu_page(
						$this->page_name,
						__( $sub_page_title, 'wp-webhooks' ),
						__( $sub_page_title, 'wp-webhooks' ),
						WPWHPRO()->settings->get_admin_cap( 'admin-add-submenu-page-item' ),
						$this->page_name . '-' . sanitize_title( $endpoint_slug ),
						array( $this, 'render_admin_submenu_page' )
					);
				}
			}

			//Remove its duplicate sub menu item
			remove_submenu_page( $this->page_name, $this->page_name);
		}

	}

	/**
	 * Mark our dynamic sub menu item as active
	 *
	 * @param string $submenu_file
	 * @param string $parent_file
	 * @return string The submenu item in case given
	 */
	public function filter_active_wpwh_submenu_page( $submenu_file, $parent_file ){

		if( $parent_file === $this->page_name ){
			if( isset( $_REQUEST['wpwhprovrs'] ) && ! empty( $_REQUEST['wpwhprovrs'] ) ){

				$sub_menu_slug = $_REQUEST['wpwhprovrs'];

				/**
				 * Originally called within /core/includes/partials/wpwhpro-page-display.php,
				 * but used here to re-validate the available menu items dynamically
				 */
				$menu_endpoints = apply_filters( 'wpwhpro/admin/settings/menu_data', array() );
				if( is_array( $menu_endpoints ) && ! empty( $menu_endpoints ) ){

					//Set the parent slug in case a child item is given
					if( ! isset( $menu_endpoints[ $sub_menu_slug ] ) ){
						foreach( $menu_endpoints as $endpoint_slug => $endpoint_data ){

							// Skip non sub menus
							if( ! isset( $endpoint_data['items'] ) ){
								continue;
							}

							if( isset( $endpoint_data['items'][ $sub_menu_slug ] ) ){
								$sub_menu_slug = $endpoint_slug;
							}

						}
					}

				}

				$submenu_file = $this->page_name . '-' . sanitize_title( $sub_menu_slug );
			}
		}

		return $submenu_file;
	}

	/**
	 * Maybe redirect a menu item from a submenu URL
	 *
	 * @since 5.0
	 *
	 * @return void
	 */
	public function maybe_redirect_wpwh_submenu_items(){

		if( ! isset( $_GET['page'] ) ){
			return;
		}

		//shorten the circle if nothing was set.
		if( isset( $_GET['wpwhprovrs'] ) ){
			return;
		}

		$page = $_GET['page'];
		$ident = $this->page_name;

		//Only redirect if it differs
		if( $ident === $page ){
			return;
		}

		if( strlen( $page ) < strlen( $ident ) ){
			return;
		}

		if( substr( $page, 0, strlen( $ident ) ) !== $ident ){
			return;
		}

		$page_slug = str_replace( $this->page_name, '', $page );

		$url = WPWHPRO()->helpers->get_current_url( false );
		$redirect_uri = WPWHPRO()->helpers->built_url( $url, array(
			'page' => $this->page_name,
			'wpwhprovrs' => sanitize_title( $page_slug ),
		) );

		wp_redirect( $redirect_uri );
		exit;

	}

	/**
	 * Render the admin submenu page
	 *
	 * You need the specified capability to edit it.
	 */
	public function render_admin_submenu_page(){
		if( ! current_user_can( WPWHPRO()->settings->get_admin_cap('admin-submenu-page') ) ){
			wp_die( __( WPWHPRO()->settings->get_default_string( 'sufficient-permissions' ), 'wp-webhooks' ) );
		}

		$wpwh_page = WPWHPRO_PLUGIN_DIR . 'core/includes/partials/wpwhpro-page-display.php';

		/*
		 * Filter the core display page
		 *
		 * @param $wpwh_page The page template
		 */
		$wpwh_page = apply_filters( 'wpwhpro/admin/page_template_file', $wpwh_page );

		if( file_exists( $wpwh_page ) ){
			include( $wpwh_page );
		}

	}

	/**
	 * Register all of our default tabs to our plugin page
	 *
	 * @param $tabs - The previous tabs
	 *
	 * @return array - Return the array of all available tabs
	 */
	public function add_main_settings_tabs( $tabs ){

		$tabs['home']           = __( 'Home', 'wp-webhooks' );

		if( ! WPWHPRO()->whitelabel->is_active() || WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_hide_flows' ) !== 'yes' ){
			$tabs['flows'] = array(
				'label' => __( 'Automations (Flows)', 'wp-webhooks' ),
				'title' => __( 'An automated workflow that can fire multiple actions consecutively.', 'wp-webhooks' ),
				'items' => array(
					'flows' => __( 'All Automations', 'wp-webhooks' )
				),
			);
		}

		if( ! WPWHPRO()->whitelabel->is_active() || WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_hide_authentication' ) !== 'yes' ){
			$tabs['flows']['items']['authentication'] = __( 'Authentication', 'wp-webhooks' );
		}

		if( ! WPWHPRO()->whitelabel->is_active() || WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_hide_flow_logs' ) !== 'yes' ){
			$tabs['flows']['items']['flow-logs']  = __( 'Flow Logs', 'wp-webhooks' );
		}

		if( ! WPWHPRO()->whitelabel->is_active() || WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_hide_logs' ) !== 'yes' ){
			$tabs['flows']['items']['logs']  = __( 'Request Logs', 'wp-webhooks' );
		}

		//unset items if no features are activated
		if( count( $tabs['flows']['items'] ) <= 1 ){
			unset( $tabs['flows']['items'] );
		}

		$tabs['send-data']      = array(
			'label' => __( 'Webhooks', 'wp-webhooks' ),
			'title' => __( 'A single trigger or action that can receive or sent data from/to a single source.', 'wp-webhooks' ),
			'items' => array(
				'send-data'  	=> __( 'Send Data (Triggers)', 'wp-webhooks' ),
				'receive-data'  	=> __( 'Receive Data (Actions)', 'wp-webhooks' ),
			)
		);

		if( ! WPWHPRO()->whitelabel->is_active() || WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_hide_authentication' ) !== 'yes' ){
			$tabs['send-data']['items']['authentication'] = __( 'Authentication', 'wp-webhooks' );
		}

		if( ! WPWHPRO()->whitelabel->is_active() || WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_hide_data_mapping' ) !== 'yes' ){
			$tabs['send-data']['items']['data-mapping'] = __( 'Data Mapping', 'wp-webhooks' );
		}

		if( ! WPWHPRO()->whitelabel->is_active() || WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_hide_logs' ) !== 'yes' ){
			$tabs['send-data']['items']['logs']  = __( 'Request Logs', 'wp-webhooks' );
		}

		if( isset( $_GET['wpwh_whitelabel_settings'] ) && $_GET['wpwh_whitelabel_settings'] === 'visible' ){
			$tabs['whitelabel']  = __( 'Whitelabel', 'wp-webhooks' );
		}

		$tabs['integrations']      = array(
			'label' => __( 'Integrations', 'wp-webhooks' ),
			'title' => __( 'All available integrations.', 'wp-webhooks' ),
		);

		if( ! WPWHPRO()->whitelabel->is_active() || WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_hide_settings' ) !== 'yes' ){
			$tabs['settings']   = array(
				'label' => __( 'Settings', 'wp-webhooks' ),
				'items' => array(
					'settings'  		=> __( 'All Settings', 'wp-webhooks' ),
				),
			);
		}

		if( ! WPWHPRO()->whitelabel->is_active() || WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_hide_extensions' ) !== 'yes' ){

			if( isset( $tabs['settings'] ) && is_array( $tabs['settings'] ) && isset( $tabs['settings']['items'] ) ){
				$tabs['settings']['items']['extensions'] = __( 'Extensions', 'wp-webhooks' );
			} else {
				$tabs['extensions'] = __( 'Extensions', 'wp-webhooks' );
			}

		}

		if( ! WPWHPRO()->whitelabel->is_active() || WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_hide_tools' ) !== 'yes' ){

			if( isset( $tabs['settings'] ) && is_array( $tabs['settings'] ) && isset( $tabs['settings']['items'] ) ){
				$tabs['settings']['items']['tools'] = __( 'Tools', 'wp-webhooks' );
			} else {
				$tabs['tools'] = __( 'Tools', 'wp-webhooks' );
			}

		}

		if( WPWHPRO()->whitelist->is_active() ){
			if( ! WPWHPRO()->whitelabel->is_active() || WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_hide_ip_whitelist' ) !== 'yes' ){

				if( isset( $tabs['settings'] ) && is_array( $tabs['settings'] ) && isset( $tabs['settings']['items'] ) ){
					$tabs['settings']['items']['whitelist'] = __( 'IP Whitelist', 'wp-webhooks' );
				} else {
					$tabs['whitelist'] = __( 'IP Whitelist', 'wp-webhooks' );
				}
	
			}
		}

		if( ! WPWHPRO()->whitelabel->is_active() || WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_hide_licensing' ) !== 'yes' ){

			if( isset( $tabs['settings'] ) && is_array( $tabs['settings'] ) && isset( $tabs['settings']['items'] ) ){
				$tabs['settings']['items']['license'] = __( 'License', 'wp-webhooks' );
			} else {
				$tabs['license'] = __( 'License', 'wp-webhooks' );
			}

		}

		return $tabs;

	}

	/**
	 * Load the content for our plugin page based on a specific tab
	 *
	 * @param $tab - The currently active tab
	 */
	public function add_main_settings_content( $tab ){

		if( ! WPWHPRO()->integrations->has_integrations_installed() ){
			$included_pages = array(
				'send-data',
				'receive-data',
				'flows',
				'data-mapping',
				'authentication',
			);

			if( in_array( $tab, $included_pages ) ){
				include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/integrations-installer.php' );
				return;
			}
			
		}

		switch($tab){
			case 'license':
				include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/license.php' );
				break;
			case 'send-data':
				include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/send-data.php' );
				break;
			case 'recieve-data': // Keep it backwards compatible
			case 'receive-data':
				include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/receive-data.php' );
				break;
			case 'settings':
				include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/settings.php' );
				break;
			case 'whitelist':
				include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/whitelist.php' );
				break;
			case 'flows':
				if( isset( $_GET['flow_id'] ) && current_user_can( WPWHPRO()->settings->get_admin_cap( 'flow-edit-single' ) ) ){

					$flow_id = intval( $_GET['flow_id'] );

					include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/flows-single.php' );
				} else {
					include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/flows.php' );
				}
				break;
			case 'logs':
				if( isset( $_GET['wpwhpro_log'] ) && current_user_can( WPWHPRO()->settings->get_admin_cap( 'log-edit-single' ) ) ){

					$log_id = intval( $_GET['wpwhpro_log'] );

					include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/logs-single.php' );
				} else {
					include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/logs.php' );
				}
				break;
			case 'flow-logs':
				if( isset( $_GET['wpwhpro_flow_log'] ) && current_user_can( WPWHPRO()->settings->get_admin_cap( 'flow-log-edit-single' ) ) ){

					$flow_log_id = intval( $_GET['wpwhpro_flow_log'] );

					include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/flow-logs-single.php' );
				} else {
					include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/flow-logs.php' );
				}
				break;
			case 'data-mapping':
				include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/data-mapping.php' );
				break;
			case 'authentication':
				if( isset( $_GET['wpwhpro_auth'] ) && current_user_can( WPWHPRO()->settings->get_admin_cap( 'auth-edit-single' ) ) ){

					$auth_id = intval( $_GET['wpwhpro_auth'] );

					include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/authentication-single.php' );
				} else {
					include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/authentication.php' );
				}
				break;
			case 'extensions':
				include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/extensions.php' );
				break;
			case 'whitelabel':
				include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/whitelabel.php' );
				break;
			case 'home':
				include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/home.php' );
				break;
			case 'features':
				include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/features.php' );
				break;
			case 'tools':
				include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/tools.php' );
				break;
			case 'integrations':
				include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/integrations.php' );
				break;
		}

	}

	/**
	 * ######################
	 * ###
	 * #### SETTINGS EXTENSIONS
	 * ###
	 * ######################
	 */

	/*
	 * Reset the settings and webhook data
	 */
	public function reset_wpwhpro_data(){

	    if( ! is_admin() || ! is_user_logged_in() ){
	        return;
        }

		$current_url_full = WPWHPRO()->helpers->get_current_url();
		$reset_all = get_option( 'wpwhpro_reset_data' );
		if( $reset_all && $reset_all === 'yes' ){
			delete_option( 'wpwhpro_reset_data' );

			WPWHPRO()->webhook->reset_wpwhpro();

			wp_redirect( $current_url_full );
			die();
		}
    }

	/**
	 * ######################
	 * ###
	 * #### NO CONFLICT MODE
	 * ###
	 * ######################
	 */

	/**
	 * Define the required no-conflict mode scripts.
	 *
	 * @since  5.2.2
	 * @access public
	 * @global $wp_scripts
	 *
	 */
	public function no_conflict_mode_scripts(){

		//Bail if not a WP Webhooks page
	    if( ! WPWHPRO()->helpers->is_page( $this->page_name ) ) {
			return;
		}

		global $wp_scripts;

		$required_scripts = WPWHPRO()->settings->get_no_conflict_scripts_styles( 'scripts' );

		//Queue only required scripts
		$queue = array();
		foreach( $wp_scripts->queue as $object ){
			if( in_array( $object, $required_scripts ) ){
				$queue[] = $object;
			}
		}
		$wp_scripts->queue = $queue;

		//Add possible dependencies
		$dependencies = array();
		foreach( $required_scripts as $script ){
			$deps = isset( $wp_scripts->registered[ $script ] ) && is_array( $wp_scripts->registered[ $script ]->deps ) ? $wp_scripts->registered[ $script ]->deps : array();
			foreach( $deps as $dep ){
				if( ! in_array( $dep, $required_scripts ) && ! in_array( $dep, $dependencies ) ){
					$dependencies[] = $dep;
				}
			}
		}

		$required_objects = array_merge( $required_scripts, $dependencies );

		//Register only required scripts
		$registered = array();
		foreach( $wp_scripts->registered as $script_name => $script_registration ){
			if( in_array( $script_name, $required_objects ) ){
				$registered[ $script_name ] = $script_registration;
			}
		}
		$wp_scripts->registered = $registered;
    }

	/**
	 * Define the required no-conflict mode scripts.
	 *
	 * @since  5.2.2
	 * @access public
	 * @global $wp_styles
	 *
	 */
	public function no_conflict_mode_styles(){

		//Bail if not a WP Webhooks page
	    if( ! WPWHPRO()->helpers->is_page( $this->page_name ) ) {
			return;
		}

		global $wp_styles;

		$required_styles = WPWHPRO()->settings->get_no_conflict_scripts_styles( 'styles' );

		//Queue only required styles
		$queue = array();
		foreach( $wp_styles->queue as $object ){
			if( in_array( $object, $required_styles ) ){
				$queue[] = $object;
			}
		}
		$wp_styles->queue = $queue;

		//Add possible dependencies
		$dependencies = array();
		foreach( $required_styles as $style ){
			$deps = isset( $wp_styles->registered[ $style ] ) && is_array( $wp_styles->registered[ $style ]->deps ) ? $wp_styles->registered[ $style ]->deps : array();
			foreach( $deps as $dep ){
				if( ! in_array( $dep, $required_styles ) && ! in_array( $dep, $dependencies ) ){
					$dependencies[] = $dep;
				}
			}
		}

		$required_objects = array_merge( $required_styles, $dependencies );

		//Register only required styles
		$registered = array();
		foreach( $wp_styles->registered as $style_name => $style_registration ){
			if( in_array( $style_name, $required_objects ) ){
				$registered[ $style_name ] = $style_registration;
			}
		}
		$wp_styles->registered = $registered;
    }

}
