<?php

/**
 * Class WP_Webhooks_Pro_Settings
 *
 * This class contains all of our important settings
 * Here you can configure the whole plugin behavior.
 *
 * @since 1.0.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Settings{

	/**
	 * Our globally used capability
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private $admin_cap;

	/**
	 * The main page name
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private $page_name;

	/**
	 * Our global array for translateable strings
	 *
	 * @var array
	 * @since 1.0.0
	 */
	private $trans_strings;

	/**
	 * The current license key
	 *
	 * This array is just mentioned for the definition.
	 * It will be overwritten inside of the setup_license function
	 *
	 * @var array
	 * @since 1.0.0
	 */
	private $license = array(
		'key'       => '',
		'expires'   => '',
		'status'    => ''
	);

	/**
	 * The license option key
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private $license_option_key;

	/**
	 * The license nonce data
	 *
	 * @var array
	 * @since 1.0.0
	 */
	private $license_nonce;

	/**
	 * The whitelist nonce data
	 *
	 * @var array
	 * @since 1.0.0
	 */
	private $whitelist_nonce;

	/**
	 * The action nonce data
	 *
	 * @var array
	 * @since 1.0.0
	 */
	private $action_nonce;

	/**
	 * The trigger nonce data
	 *
	 * @var array
	 * @since 4.0.0
	 */
	private $trigger_nonce;

	/**
	 * Backwards compatibility for other plugins
	 * fetching the active webhooks
	 *
	 * @var array
	 * @since 4.0.0
	 */
	private $active_webhooks = null;

	/**
	 * WP_Webhooks_Pro_Settings constructor.
	 *
	 * We define all of our necessary settings in here.
	 * If you need to do plugin related changes, everything will
	 * be available in this file.
	 */
	function __construct(){
		$this->admin_cap            = 'manage_options';
		$this->page_name            = 'wp-webhooks-pro';
		$this->page_title           = WPWHPRO_NAME;
		$this->site_ident           = null; //the site ident is automatically calculated within the get_site_ident function
		$this->site_identifier_key   = 'wpwh_site_ident';
		$this->license_option_key   = 'ironikus_webhook_pro_license';
		$this->license_transient_key   = 'ironikus_license_transient';
		$this->webhook_settings_key = 'ironikus_webhook_webhooks';
		$this->whitelist_settings_key = 'ironikus_webhook_whitelist';
		$this->whitelist_requested_key = 'ironikus_webhook_whitelist_requests';
		$this->whitelabel_settings_key = 'ironikus_webhook_whitelabel';
		$this->news_transient_key   = 'ironikus_cached_news_' . WPWHPRO_NEWS_FEED_ID;
		$this->extensions_transient_key   = 'ironikus_cached_extensions';
		$this->integrations_transient_key   = 'ironikus_cached_integrations';
		$this->wpwh_previous_versions_key   = 'wpwh_previous_versions';
		$this->webhook_ident_param  = 'wpwhpro_action';
		$this->active_webhook_ident_param  = 'wpwhpro_active_webhooks';
		$this->get_webhook_receivable_trigger_param  = 'wpwhreceivable';
		$this->default_settings     = $this->load_default_settings();
		$this->whitelabel_settings     = $this->load_whitelabel_settings();
		$this->required_trigger_settings     = $this->load_required_trigger_settings();
		$this->default_trigger_settings     = $this->load_default_trigger_settings();
		$this->receivable_trigger_settings     = $this->load_receivable_trigger_settings();
		$this->required_action_settings     = $this->load_required_action_settings();
		$this->data_mapping_template_settings     = $this->load_data_mapping_template_settings();
		$this->data_mapping_key_settings     = $this->load_data_mapping_key_settings();
		$this->authentication_methods     = $this->load_authentication_methods();
		$this->log_table_data   = $this->setup_log_table_data();
		$this->data_mapping_table_data   = $this->setup_data_mapping_table_data();
		$this->authentication_table_data   = $this->setup_authentication_table_data();
		$this->flows_table_data   = $this->setup_flows_table_data();
		$this->default_integration_dependencies = array(
            'helpers',
            'auth',
            'actions',
            'triggers',
        );
		$this->flow_status_labels = array(
			'inactive' => __( 'Inactive', 'wp-webhooks' ),
			'active' => __( 'Active', 'wp-webhooks' ),
		);
		$this->flow_condition_labels = array(
			'default' => 'is',
			'conditions' => array(
				'is' => __( 'is', 'wp-webhooks' ),
				'isnot' => __( 'is not', 'wp-webhooks' ),
				'contains' => __( 'contains', 'wp-webhooks' ),
				'doesnotcontain' => __( 'does not contain', 'wp-webhooks' ),
				'isempty' => __( 'is empty', 'wp-webhooks' ),
				'isnotempty' => __( 'is not empty', 'wp-webhooks' ),
				'isgreaterthan' => __( 'is greater than', 'wp-webhooks' ),
				'isgreaterthanorequalto' => __( 'is greater than or equal to', 'wp-webhooks' ),
				'islessthan' => __( 'is less than', 'wp-webhooks' ),
				'islessthanorequalto' => __( 'is less than or equal to', 'wp-webhooks' ),
			)
		);
		$this->flow_condition_action_labels = array(
			'default' => 'continue',
			'actions' => array(
				'skip' => __( 'Skip step', 'wp-webhooks' ),
				'stop' => __( 'Stop flow', 'wp-webhooks' ),
			)
		);
		$this->flow_common_tags = array(
			'common:user_first_name' => array(
				'label' => __( 'Current user first name', 'wp-webhooks' ),
				'example' => 'Jon',
			),
			'common:user_last_name' => array(
				'label' => __( 'Current user last name', 'wp-webhooks' ),
				'example' => 'Doe',
			),
			'common:user_login' => array(
				'label' => __( 'Current user login', 'wp-webhooks' ),
				'example' => 'jondoe',
			),
			'common:user_email' => array(
				'label' => __( 'Current user email', 'wp-webhooks' ),
				'example' => 'jon@doe.test',
			),
			'common:user_display_name' => array(
				'label' => __( 'Current user display name', 'wp-webhooks' ),
				'example' => 'Jon Doe',
			),
			'common:user_id' => array(
				'label' => __( 'Current user ID', 'wp-webhooks' ),
				'example' => 123,
			),
			'common:reset_pw_url' => array(
				'label' => __( 'User reset password URL', 'wp-webhooks' ),
				'example' => 'https://youdomain.com/password-reset-link',
			),
			'common:admin_email' => array(
				'label' => __( 'Admin email', 'wp-webhooks' ),
				'example' => 'admin@email.test',
			),
			'common:site_name' => array(
				'label' => __( 'Site name', 'wp-webhooks' ),
				'example' => 'The site name',
			),
			'common:site_url' => array(
				'label' => __( 'Site URL', 'wp-webhooks' ),
				'example' => 'https://the-siteurl.test',
			),
			'common:current_date' => array(
				'label' => __( 'Current date', 'wp-webhooks' ),
				'example' => date( "Y-m-d H:i:s" ),
			),
		);
		$this->license_nonce        = array(
			'action' => 'ironikus_wpwhpro_license',
			'arg'    => 'ironikus_wpwhpro_license_nonce'
		);
		$this->settings_nonce        = array(
			'action' => 'ironikus_wpwhpro_settings',
			'arg'    => 'ironikus_wpwhpro_settings_nonce'
		);
		$this->wizard_nonce        = array(
			'action' => 'ironikus_wpwhpro_wizard',
			'arg'    => 'ironikus_wpwhpro_wizard_nonce'
		);
		$this->tools_import_nonce        = array(
			'action' => 'ironikus_wpwhpro_tools_import',
			'arg'    => 'ironikus_wpwhpro_tools_import_nonce'
		);
		$this->whitelist_nonce        = array(
			'action' => 'ironikus_wpwhpro_whitelist',
			'arg'    => 'ironikus_wpwhpro_whitelist_nonce'
		);
		$this->trigger_nonce        = array(
			'action' => 'ironikus_wpwhpro_triggers',
			'arg'    => 'ironikus_wpwhpro_triggers_nonce'
		);
		$this->action_nonce        = array(
			'action' => 'ironikus_wpwhpro_actions',
			'arg'    => 'ironikus_wpwhpro_actions_nonce'
		);
		$this->log_nonce        = array(
			'action' => 'ironikus_wpwhpro_logs',
			'arg'    => 'ironikus_wpwhpro_logs_nonce'
		);
		$this->flows_nonce        = array(
			'action' => 'ironikus_wpwhpro_flows',
			'arg'    => 'ironikus_wpwhpro_flows_nonce'
		);
		$this->data_mapping_nonce        = array(
			'action' => 'ironikus_wpwhpro_data_mapping',
			'arg'    => 'ironikus_wpwhpro_data_mapping_nonce'
		);
		$this->authentication_nonce        = array(
			'action' => 'ironikus_wpwhpro_authentication',
			'arg'    => 'ironikus_wpwhpro_authentication_nonce'
		);
		$this->whitelabel_nonce        = array(
			'action' => 'ironikus_wpwhpro_whitelabel',
			'arg'    => 'ironikus_wpwhpro_whitelabel_nonce'
		);
		$this->no_conflict_scripts_styles = array(
			'scripts' => array(
				//WordPress related
				'admin-bar',
				'common',
				'jquery-color',
				'utils',
				'svg-painter',
				'mce-view',
				'backbone',
				'editor',
				'jquery',
				'jquery-ui-autocomplete',
				'jquery-ui-core',
				'jquery-ui-datepicker',
				'jquery-ui-sortable',
				'jquery-ui-draggable',
				'jquery-ui-droppable',
				'jquery-ui-tabs',
				'jquery-ui-accordion',
				'json2',
				'media-editor',
				'media-models',
				'media-upload',
				'media-views',
				'plupload',
				'plupload-flash',
				'plupload-html4',
				'plupload-html5',
				'quicktags',
				'rg_currency',
				'thickbox',
				'word-count',
				'wp-plupload',
				'wp-tinymce',
				'wp-tinymce-root',
				'wp-tinymce-lists',
				'wpdialogs-popup',
				'wplink',
				'wp-pointer',

				//WP Webhooks related
				'wpwhpro-admin-vendors',
				'wpwhpro-admin-scripts',

				//Flows specific
				'wpwhpro-flows-vendor',
				'wpwhpro-flows',
			),
			'styles'    => array(
				//WordPress related
				'admin-bar', 
				'colors', 
				'ie', 
				'wp-admin', 
				'editor-style',
				'thickbox',
				'editor-buttons',
				'wp-jquery-ui-dialog',
				'media-views',
				'buttons',
				'wp-pointer',

				//WP Webhooks related
				'wpwhpro-admin-styles',
				'wpwhpro-google-fonts',

				//Flows specific
				'wpwhpro-codemirror',
				'wpwhpro-sweetalert2',
				'wpwhpro-vue-select',
			)
		);

		$this->license              = $this->setup_license();
		$this->trans_strings        = $this->load_default_strings();
	}

	/**
	 * Load the license into the current object cache
	 *
	 * @return array - the license data
	 */
	private function setup_license(){
		$license_data = get_option( $this->license_option_key );
		if( empty( $license_data ) ){
			$license_data = array(
				'key'       => '',
				'expires'   => '',
				'status'    => ''
			);
		}

		return $license_data;
	}

	/**
	 * Maybe generate a unique identifier
	 *
	 * @return void
	 */
	private function maybe_generate_site_ident(){

		$site_identifier = get_option( $this->site_identifier_key );

		if( empty( $site_identifier ) ){
			$site_identifier = time() . 'x' . strtolower( wp_generate_password( 20, false ) );
			update_option( $this->site_identifier_key, $site_identifier );
		}

		return $site_identifier;
	}

	/**
	 * Setup the data mapping table data
	 *
	 * @return array - the data mappingn table data
	 */
	public function setup_data_mapping_table_data(){

		$data = array();
		$table_name = 'wpwhpro_data_mapping';
		$data['table_name'] = $table_name;

		$data['sql_create_table'] = "
		  CREATE TABLE {prefix}$table_name (
		  id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  name VARCHAR(100),
		  template LONGTEXT,
		  log_time DATETIME
		) {charset_collate};";
		$data['sql_drop_table'] = "DROP TABLE {prefix}$table_name;";

		return $data;

	}

	/**
	 * Setup the authentication table data
	 *
	 * @return array - the authentication table data
	 */
	public function setup_authentication_table_data(){

		$data = array();
		$table_name = 'wpwhpro_authentication';
		$data['table_name'] = $table_name;

		$data['sql_create_table'] = "
		  CREATE TABLE {prefix}$table_name (
		  id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  name VARCHAR(100),
		  auth_type VARCHAR(100),
		  template LONGTEXT,
		  log_time DATETIME
		) {charset_collate};";
		$data['sql_drop_table'] = "DROP TABLE {prefix}$table_name;";

		return $data;

	}

	/**
	 * Setup the flows table data
	 *
	 * @return array - the flows table data
	 */
	public function setup_flows_table_data(){

		$data = array();
		$table_name = 'wpwhpro_flows';
		$data['table_name'] = $table_name;

		$data['sql_create_table'] = "
		  CREATE TABLE {prefix}$table_name (
		  id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  flow_author BIGINT(20) unsigned NOT NULL,
		  flow_title VARCHAR(100),
		  flow_name VARCHAR(100),
		  flow_trigger VARCHAR(100),
		  flow_status VARCHAR(20),
		  flow_config LONGTEXT,
		  flow_date DATETIME
		) {charset_collate};";
		$data['sql_drop_table'] = "DROP TABLE {prefix}$table_name;";

		return $data;

	}

	/**
	 * Setup the flows logs table data
	 *
	 * @return array - the flows logs table data
	 */
	public function get_flow_logs_table_data(){

		$data = array();
		$table_name = 'wpwhpro_flows_logs';
		$data['table_name'] = $table_name;

		$data['sql_create_table'] = "
		  CREATE TABLE {prefix}$table_name (
		  id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  flow_id BIGINT(20) unsigned NOT NULL,
		  flow_config LONGTEXT,
		  flow_payload LONGTEXT,
		  flow_completed TINYINT(1),
		  flow_date DATETIME
		) {charset_collate};";
		$data['sql_drop_table'] = "DROP TABLE {prefix}$table_name;";

		return $data;

	}

	/**
	 * Setup the log table data
	 *
	 * @return array - the log table data
	 */
	public function setup_log_table_data(){

		$data = array();
		$table_name = 'wpwhpro_logs';
		$data['table_name'] = $table_name;

		$data['sql_create_table'] = "
		  CREATE TABLE {prefix}$table_name (
		  id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  message LONGTEXT,
		  content LONGTEXT,
		  log_time DATETIME
		) {charset_collate};";
		$data['sql_drop_table'] = "DROP TABLE {prefix}$table_name;";

		return $data;

	}

	/**
	 * Load the default settings for the main settings page
	 * of our plugin.
	 *
	 * @return array - an array of all available settings
	 */
	private function load_default_settings(){
		$fields = array(

			/**
			 * ACTIVATE WHITELIST
			 */
			'wpwhpro_activate_whitelist' => array(
				'id'          => 'wpwhpro_activate_whitelist',
				'type'        => 'checkbox',
				'label'       => __( 'Activate Whitelist', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'dangerzone'  => false,
				'description' => __( 'Enhance your website security by activating the global whitelist. This will restrict all incoming webhook connections by default and only allows them when you want to. If you want to whitelist only specific webhook action URLs, please see the webhook URL settings. By default, activating this setting affects the "receive Data" webhook actions, as well as the receivable webhook URLs.', 'wp-webhooks' )
			),

			/**
			 * Clean logs
			 */
			'wpwhpro_autoclean_logs' => array(
				'id'          => 'wpwhpro_autoclean_logs',
				'type'        => 'select',
				'choices'        => array(
					'never' => array( 'label' => __( 'Never', 'wp-webhooks' ) ),
					'1day' => array( 'label' => __( 'Every day', 'wp-webhooks' ) ),
					'2days' => array( 'label' => __( 'Every 2 days', 'wp-webhooks' ) ),
					'5days' => array( 'label' => __( 'Every 5 days', 'wp-webhooks' ) ),
					'10days' => array( 'label' => __( 'Every 10 days', 'wp-webhooks' ) ),
					'15days' => array( 'label' => __( 'Every 15 days', 'wp-webhooks' ) ),
					'30days' => array( 'label' => __( 'Every 30 days', 'wp-webhooks' ) ),
					'60days' => array( 'label' => __( 'Every 60 days', 'wp-webhooks' ) ),
					'180days' => array( 'label' => __( 'Every 180 days', 'wp-webhooks' ) ),
					'365days' => array( 'label' => __( 'Every 365 days', 'wp-webhooks' ) ),
				),
				'label'       => __( 'Auto-clean logs', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'dangerzone'  => false,
				'description' => sprintf( __( 'Choose an interval to autoclean your logs. This saves database space and performance.', 'wp-webhooks' ), $this->get_page_title() )
			),

			/**
			 * SUB MENU ITEM
			 */
			'wpwhpro_show_sub_menu' => array(
				'id'          => 'wpwhpro_show_sub_menu', //originally wpwhpro_show_main_menu
				'type'        => 'checkbox',
				'label'       => __( 'Show in settings sub menu', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'dangerzone'  => false,
				'description' => sprintf( __( 'Check this button if you want to show %s within the sub menu of the settings menu item instead of an own menu item.', 'wp-webhooks' ), $this->get_page_title() )
			),

			/**
			 * Deactivate Post Delay
			 */
			'wpwhpro_deactivate_post_delay' => array(
				'id'          => 'wpwhpro_deactivate_post_delay',
				'type'        => 'checkbox',
				'label'       => __( 'Deactivate Post Trigger Delay', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'dangerzone'  => false,
				'description' => __( 'Advanced: By default, we delay every webhook trigger until the WordPress "shutdown" hook fires. This allows us to also keep track of the changes third-party plugins make. If you do not want that, check this box.', 'wp-webhooks' )
			),

			/**
			 * Deactivate Debug mode
			 */
			'wpwhpro_activate_debug_mode' => array(
				'id'          => 'wpwhpro_activate_debug_mode',
				'type'        => 'checkbox',
				'label'       => __( 'Activate Debug Mode', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'dangerzone'  => false,
				'description' => __( 'This feature adds additional debug information to the plugin. It logs, e.g. further details within the WordPress debug.log file about issues that occur from a configurational perspective.', 'wp-webhooks' )
			),

			/**
			 * Deactivate No conflict mode
			 */
			'wpwhpro_deactivate_no_conflict_mode' => array(
				'id'          => 'wpwhpro_deactivate_no_conflict_mode',
				'type'        => 'checkbox',
				'label'       => __( 'Deactivate No-Conflict Mode', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'dangerzone'  => false,
				'description' => sprintf( __( 'By default, we prevent extraneous scripts and styles from being loaded on pages of %s. This reduces conflicts with other third-party plugins and makes sure everything works smoothly. If you do not want that, please activate this setting (Not recommended).', 'wp-webhooks' ), $this->get_page_title() )
			),

			/**
			 * Maybe centralize settings for multisites
			 */
			'wpwhpro_sync_network_tables' => array(
				'id'          => 'wpwhpro_sync_network_tables',
				'type'        => 'checkbox',
				'label'       => __( 'Sync network site tables', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'dangerzone'  => true,
				'description' => sprintf( __( 'Activate this settings if you want to inherit the Flows, Logs, Authentication & Data Mapping templates from the main network site.', 'wp-webhooks' ), $this->get_page_title() )
			),

			/**
			 * Maybe centralize settings for multisites
			 */
			'wpwhpro_load_local_integrations' => array(
				'id'          => 'wpwhpro_load_local_integrations',
				'type'        => 'checkbox',
				'label'       => __( '(Temporary setting) Load integrations locally', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'dangerzone'  => false,
				'description' => sprintf( __( 'This is a temporary setting that got introduced with the release of version 6.0 just to make sure you can rely on the integrations in case the remote installation is not working as expected. Since this setting will be removed in one of our future releases, please make sure to reach out to us in case you face an issue installing them without it.', 'wp-webhooks' ), $this->get_page_title() )
			),

			/**
			 * Reset WP Webbhooks Pro
			 */
			'wpwhpro_reset_data' => array(
				'id'          => 'wpwhpro_reset_data',
				'type'        => 'checkbox',
				'label'       => sprintf( __( 'Reset %s', 'wp-webhooks' ), $this->get_page_title() ),
				'placeholder' => '',
				'required'    => false,
				'dangerzone'  => true,
				'description' => sprintf( __( 'Reset %s and set it back to its default settings (Excludes license & Extensions). BE CAREFUL: Once you activate the button and click save, all of your saved data for the plugin is gone.', 'wp-webhooks' ), $this->get_page_title() )
			),
		);

		//unset multisite settings if no multisite is given
		if( ! is_multisite() ){
			unset( $fields['wpwhpro_sync_network_tables'] );
		}

		foreach( $fields as $key => $field ){
			$value = get_option( $key );

			$fields[ $key ]['value'] = $value;

			if( $fields[ $key ]['type'] == 'checkbox' ){
				if( empty( $fields[ $key ]['value'] ) || $fields[ $key ]['value'] == 'no' ){
					$fields[ $key ]['value'] = 'no';
				} else {
					$fields[ $key ]['value'] = 'yes';
				}
			}
		}

		return apply_filters('wpwhpro/settings/fields', $fields);
	}

	/**
	 * Load the whitelabel settings
	 * of our plugin.
	 *
	 * @since - 3.0.6
	 * @return array - an array of all available whitelabel settings
	 */
	private function load_whitelabel_settings(){
		$fields = array(

			/**
			 * Activate whitelabeling
			 */
			'wpwhpro_whitelabel_activate' => array(
				'id'          => 'wpwhpro_whitelabel_activate',
				'type'        => 'checkbox',
				'label'       => __( 'Activate Whitelabel', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'description' => __( 'Check this box if you want to activate the whitelabel feature.', 'wp-webhooks' )
			),

			/**
			 * Whitelabel Name
			 */
			'wpwhpro_whitelabel_name' => array(
				'id'          => 'wpwhpro_whitelabel_name',
				'type'        => 'text',
				'label'       => __( 'Whitelabel Name', 'wp-webhooks' ),
				'placeholder' => $this->get_page_title(),
				'required'    => false,
				'description' => __( 'This is the name of the whitelabeled version of this plugin.', 'wp-webhooks' ),
			),

			/**
			 * Hide Logs tab
			 */
			'wpwhpro_whitelabel_hide_logs' => array(
				'id'          => 'wpwhpro_whitelabel_hide_logs',
				'type'        => 'checkbox',
				'label'       => __( 'Hide Logs tab', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'description' => __( 'Check this box if you want to hide the Logs tab from the menu. This way, your customers won\'t be able to see the logs anymore. The functionality for logs continues to work.', 'wp-webhooks' )
			),

			/**
			 * Hide Flows Logs tab
			 */
			'wpwhpro_whitelabel_hide_flow_logs' => array(
				'id'          => 'wpwhpro_whitelabel_hide_flow_logs',
				'type'        => 'checkbox',
				'label'       => __( 'Hide Flows Logs tab', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'description' => __( 'Check this box if you want to hide the Flows Logs tab from the menu. This way, your customers won\'t be able to see the flows logs anymore. The functionality for logs continues to work.', 'wp-webhooks' )
			),

			/**
			 * Hide Authentication tab
			 */
			'wpwhpro_whitelabel_hide_authentication' => array(
				'id'          => 'wpwhpro_whitelabel_hide_authentication',
				'type'        => 'checkbox',
				'label'       => __( 'Hide Authentication tab', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'description' => __( 'Check this box if you want to hide the Authentication tab from the menu. This way, your customers won\'t be able to see the Authentications anymore. The functionality for Authentications continues to work.', 'wp-webhooks' )
			),

			/**
			 * Hide Data Mapping tab
			 */
			'wpwhpro_whitelabel_hide_data_mapping' => array(
				'id'          => 'wpwhpro_whitelabel_hide_data_mapping',
				'type'        => 'checkbox',
				'label'       => __( 'Hide Data Mapping tab', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'description' => __( 'Check this box if you want to hide the Data Mapping tab from the menu. This way, your customers won\'t be able to see the Data Mapping anymore. The functionality for Data Mapping continues to work.', 'wp-webhooks' )
			),

			/**
			 * Hide Licensing tab
			 */
			'wpwhpro_whitelabel_hide_licensing' => array(
				'id'          => 'wpwhpro_whitelabel_hide_licensing',
				'type'        => 'checkbox',
				'label'       => __( 'Hide Licensing tab', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'description' => __( 'Check this box if you want to hide the licensing tab from the menu. This way, your customers won\'t be able to see the license anymore. The functionality for licenses continues to work.', 'wp-webhooks' )
			),

			/**
			 * Hide Tools tab
			 */
			'wpwhpro_whitelabel_hide_tools' => array(
				'id'          => 'wpwhpro_whitelabel_hide_tools',
				'type'        => 'checkbox',
				'label'       => __( 'Hide Tools tab', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'description' => __( 'Check this box if you want to hide the tools tab from the menu. This way, your customers won\'t be able to see the tools anymore. The functionality for tools continues to work.', 'wp-webhooks' )
			),

			/**
			 * Hide Whitelist tab
			 */
			'wpwhpro_whitelabel_hide_ip_whitelist' => array(
				'id'          => 'wpwhpro_whitelabel_hide_ip_whitelist',
				'type'        => 'checkbox',
				'label'       => __( 'Hide IP Whitelist tab', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'description' => __( 'Check this box if you want to hide the IP Whitelist tab from the menu. This way, your customers won\'t be able to see the IP Whitelist anymore. The functionality for IP Whitelist continues to work.', 'wp-webhooks' )
			),

			/**
			 * Hide Flows tab
			 */
			'wpwhpro_whitelabel_hide_flows' => array(
				'id'          => 'wpwhpro_whitelabel_hide_flows',
				'type'        => 'checkbox',
				'label'       => __( 'Hide Flows tab', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'description' => __( 'Check this box if you want to hide the Flows tab from the menu. This way, your customers won\'t be able to see the Flows anymore. The functionality for Flows continues to work.', 'wp-webhooks' )
			),

			/**
			 * Hide Settings tab
			 */
			'wpwhpro_whitelabel_hide_settings' => array(
				'id'          => 'wpwhpro_whitelabel_hide_settings',
				'type'        => 'checkbox',
				'label'       => __( 'Hide Settings tab', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'description' => __( 'Check this box if you want to hide the Settings tab from the menu. This way, your customers won\'t be able to see the Settings anymore. The functionality for settings continues to work.', 'wp-webhooks' )
			),

			/**
			 * Hide Extensions tab
			 */
			'wpwhpro_whitelabel_hide_extensions' => array(
				'id'          => 'wpwhpro_whitelabel_hide_extensions',
				'type'        => 'checkbox',
				'label'       => __( 'Hide Extensions tab', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'description' => __( 'Check this box if you want to hide the Extensions tab from the menu. This way, your customers won\'t be able to see the Extensions anymore.', 'wp-webhooks' )
			),

			/**
			 * Custom home tab content
			 */
			'wpwhpro_whitelabel_custom_home' => array(
				'id'          => 'wpwhpro_whitelabel_custom_home',
				'type'        => 'textarea',
				'label'       => __( 'Customize Home tab', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'description' => __( 'Add your custom code into this field to customize the home tab of our plugin. You can also use our predefined template tags such as %home_url%, %admin_url%, %product_version%, %product_name% or %user_name% - all of them will be automatically replaced on the home tab.', 'wp-webhooks' )
			),

			/**
			 * Custom send data text
			 */
			'wpwhpro_whitelabel_custom_text_send_data' => array(
				'id'          => 'wpwhpro_whitelabel_custom_text_send_data',
				'type'        => 'textarea',
				'label'       => __( 'Customize Send Data Sub Text', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'description' => __( 'Replace the default text of the Send Data tab with your very own text.', 'wp-webhooks' )
			),

			/**
			 * Custom receive data text
			 */
			'wpwhpro_whitelabel_custom_text_receive_data' => array(
				'id'          => 'wpwhpro_whitelabel_custom_text_receive_data',
				'type'        => 'textarea',
				'label'       => __( 'Customize Receive Data Sub Text', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'description' => __( 'Replace the default text of the Receive Data tab with your very own text.', 'wp-webhooks' )
			),

			/**
			 * Custom featured tab text
			 */
			'wpwhpro_whitelabel_custom_text_features' => array(
				'id'          => 'wpwhpro_whitelabel_custom_text_features',
				'type'        => 'textarea',
				'label'       => __( 'Customize Features Sub Text', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'description' => __( 'Replace the default text of the Features tab with your very own text.', 'wp-webhooks' )
			),

			/**
			 * Custom whitelist data text
			 */
			'wpwhpro_whitelabel_custom_text_whitelist' => array(
				'id'          => 'wpwhpro_whitelabel_custom_text_whitelist',
				'type'        => 'textarea',
				'label'       => __( 'Customize Whitelist Sub Text', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'description' => __( 'Replace the default text of the Whitelist tab with your very own text.', 'wp-webhooks' )
			),

			/**
			 * Custom logs data text
			 */
			'wpwhpro_whitelabel_custom_text_logs' => array(
				'id'          => 'wpwhpro_whitelabel_custom_text_logs',
				'type'        => 'textarea',
				'label'       => __( 'Customize Logs Sub Text', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'description' => __( 'Replace the default text of the Logs tab with your very own text.', 'wp-webhooks' )
			),

			/**
			 * Custom data mapping tab text
			 */
			'wpwhpro_whitelabel_custom_text_data_mapping' => array(
				'id'          => 'wpwhpro_whitelabel_custom_text_data_mapping',
				'type'        => 'textarea',
				'label'       => __( 'Customize Data Mapping Sub Text', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'description' => __( 'Replace the default text of the Data Mapping tab with your very own text.', 'wp-webhooks' )
			),

			/**
			 * Custom data mapping tab preview text
			 */
			'wpwhpro_whitelabel_custom_text_data_mapping_preview' => array(
				'id'          => 'wpwhpro_whitelabel_custom_text_data_mapping_preview',
				'type'        => 'textarea',
				'label'       => __( 'Customize Data Mapping Preview Sub Text', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'description' => __( 'Replace the default preview text of the Data Mapping tab with your very own text.', 'wp-webhooks' )
			),

			/**
			 * Custom authentication tab text
			 */
			'wpwhpro_whitelabel_custom_text_authentication' => array(
				'id'          => 'wpwhpro_whitelabel_custom_text_authentication',
				'type'        => 'textarea',
				'label'       => __( 'Customize Authentication Sub Text', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'description' => __( 'Replace the default text of the Authentication tab with your very own text.', 'wp-webhooks' )
			),

			/**
			 * Custom flows tab text
			 */
			'wpwhpro_whitelabel_custom_text_flows' => array(
				'id'          => 'wpwhpro_whitelabel_custom_text_flows',
				'type'        => 'textarea',
				'label'       => __( 'Customize Flows Sub Text', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'description' => __( 'Replace the default text of the Flows tab with your very own text.', 'wp-webhooks' )
			),

			/**
			 * Custom extensions tab text
			 */
			'wpwhpro_whitelabel_custom_text_extensions' => array(
				'id'          => 'wpwhpro_whitelabel_custom_text_extensions',
				'type'        => 'textarea',
				'label'       => __( 'Customize Extensions Sub Text', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'description' => __( 'Replace the default text of the Extensions tab with your very own text.', 'wp-webhooks' )
			),

			/**
			 * Custom settings tab text
			 */
			'wpwhpro_whitelabel_custom_text_settings' => array(
				'id'          => 'wpwhpro_whitelabel_custom_text_settings',
				'type'        => 'textarea',
				'label'       => __( 'Customize Settings Sub Text', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'description' => __( 'Replace the default text of the Settings tab with your very own text.', 'wp-webhooks' )
			),

			/**
			 * Custom license tab text
			 */
			'wpwhpro_whitelabel_custom_text_license' => array(
				'id'          => 'wpwhpro_whitelabel_custom_text_license',
				'type'        => 'textarea',
				'label'       => __( 'Customize License Sub Text', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'description' => __( 'Replace the default text of the License tab with your very own text.', 'wp-webhooks' )
			),

			/**
			 * Custom tools tab text
			 */
			'wpwhpro_tools_custom_text_settings' => array(
				'id'          => 'wpwhpro_tools_custom_text_settings',
				'type'        => 'textarea',
				'label'       => __( 'Customize Tools Sub Text', 'wp-webhooks' ),
				'placeholder' => '',
				'required'    => false,
				'description' => __( 'Replace the default text of the Tools tab with your very own text.', 'wp-webhooks' )
			),

		);

		$whitelabel_settings_data = get_option( $this->whitelabel_settings_key );

		foreach( $fields as $key => $field ){
			$value = isset( $whitelabel_settings_data[ $key ] ) ? $whitelabel_settings_data[ $key ] : '';

			$fields[ $key ]['value'] = $value;

			if( $fields[ $key ]['type'] == 'checkbox' ){
				if( empty( $fields[ $key ]['value'] ) || $fields[ $key ]['value'] == 'no' ){
					$fields[ $key ]['value'] = 'no';
				} else {
					$fields[ $key ]['value'] = 'yes';
				}
			}
		}

		return apply_filters( 'wpwhpro/settings/whitelabel_fields', $fields );
	}

	/**
	 * Load the strictly necessary trigger settings
	 * to any available trigger.
	 *
	 * @return array - the trigger settings
	 */
	private function load_required_trigger_settings(){

		$fields = array();

		//todo - remove if done
		if( WPWHPRO()->helpers->is_dev() ){

			$fields['wpwhpro_trigger_demo_text'] = array(
				'id'          => 'wpwhpro_trigger_demo_text',
				'type'        => 'text',
				'label'       => __( 'Demo Text', 'wp-webhooks' ),
				'placeholder' => '',
				'default_value' => '',
				'required' 		=> true,
				'description' => __( 'This is the demo text field description.', 'wp-webhooks' )
			);

			$fields['wpwhpro_trigger_demo_text_variable'] = array(
				'id'          => 'wpwhpro_trigger_demo_text_variable',
				'type'        => 'text',
				'variable'    => true,
				'label'       => __( 'Demo Text Variable', 'wp-webhooks' ),
				'placeholder' => '',
				'default_value' => '',
				'required' 		=> true,
				'description' => __( 'This is the demo text field description with dynamic variables.', 'wp-webhooks' )
			);

			$fields['wpwhpro_trigger_demo_select'] = array(
				'id'          => 'wpwhpro_trigger_demo_select',
				'type'        => 'select',
				'label'       => __( 'Demo Simple Select', 'wp-webhooks' ),
				'choices'     => array(
					'demo1' => array( 'label' => 'First value' ),
					'demo2' => array( 'label' => 'Second Value' ),
					'demo3' => array( 'label' => 'Third Value' ),
				),
				'placeholder' => '',
				'default_value' => '',
				'description' => __( 'This is the demo select field description.', 'wp-webhooks' )
			);

			$fields['wpwhpro_trigger_demo_select_def'] = array(
				'id'          => 'wpwhpro_trigger_demo_select_def',
				'type'        => 'select',
				'label'       => __( 'Demo Simple Default Select', 'wp-webhooks' ),
				'choices'     => array(
					'demo1' => array( 'label' => 'First value' ),
					'demo2' => array( 'label' => 'Second Value' ),
					'demo3' => array( 'label' => 'Third Value' ),
				),
				'placeholder' => '',
				'default_value' => 'demo2',
				'description' => __( 'This is the demo select field description.', 'wp-webhooks' )
			);

			$fields['wpwhpro_trigger_demo_select_mult'] = array(
				'id'          => 'wpwhpro_trigger_demo_select_mult',
				'type'        => 'select',
				'label'       => __( 'Demo Multiple Select', 'wp-webhooks' ),
				'choices'     => array(
					'demo1' => array( 'label' => 'First value' ),
					'demo2' => array( 'label' => 'Second Value' ),
					'demo3' => array( 'label' => 'Third Value' ),
				),
				'placeholder' => '',
				'default_value' => '',
				'multiple' => true,
				'description' => __( 'This is the demo select field description.', 'wp-webhooks' )
			);

			$fields['wpwhpro_trigger_demo_select_mult_def'] = array(
				'id'          => 'wpwhpro_trigger_demo_select_mult_def',
				'type'        => 'select',
				'label'       => __( 'Demo Multiple Default Select', 'wp-webhooks' ),
				'choices'     => array(
					'demo1' => array( 'label' => 'First value' ),
					'demo2' => array( 'label' => 'Second Value' ),
					'demo3' => array( 'label' => 'Third Value' ),
				),
				'placeholder' => '',
				'default_value' => array(
					'demo1',
					'demo3',
				),
				'multiple' => true,
				'description' => __( 'This is the demo select field description.', 'wp-webhooks' )
			);

			$fields['wpwhpro_trigger_demo_radio'] = array(
				'id'          => 'wpwhpro_trigger_demo_radio',
				'type'        => 'radio',
				'label'       => __( 'Demo Radio', 'wp-webhooks' ),
				'choices'     => array(
					'demo1' => array(
						'label' => 'First value',
						'description' => 'This is a short value description'
					),
					'demo2' => array(
						'label' => 'Second value',
						'description' => 'This is a short value description'
					),
					'demo3' => array(
						'label' => 'Third value',
						'description' => 'This is a short value description'
					),
				),
				'placeholder' => '',
				'default_value' => '',
				'description' => __( 'This is the demo radio field description.', 'wp-webhooks' )
			);

			$fields['wpwhpro_trigger_demo_textarea'] = array(
				'id'          => 'wpwhpro_trigger_demo_textarea',
				'type'        => 'textarea',
				'label'       => __( 'Demo Textarea', 'wp-webhooks' ),
				'placeholder' => '',
				'default_value' => '',
				'description' => __( 'This is the demo textarea field description.', 'wp-webhooks' )
			);

			$fields['wpwhpro_trigger_demo_wysiwyg'] = array(
				'id'          => 'wpwhpro_trigger_demo_wysiwyg',
				'type'        => 'wysiwyg',
				'label'       => __( 'Demo Wysiwyg', 'wp-webhooks' ),
				'placeholder' => '',
				'default_value' => '',
				'description' => __( 'This is the demo Wysiwyg field description.', 'wp-webhooks' )
			);

			$fields['wpwhpro_trigger_demo_repeater'] = array(
				'id'          => 'wpwhpro_trigger_demo_repeater',
				'type'        => 'repeater',
				'label'       => __( 'Demo Repeater', 'wp-webhooks' ),
				'placeholder' => '',
				'default_value' => '',
				'description' => __( 'This is the demo repeater field description.', 'wp-webhooks' )
			);

			$fields['wpwhpro_trigger_demo_list'] = array(
				'type' => 'list', 
				'label' => 'Link Label',
				'short_description' => __( 'Similar to a repeater field, but with only one input field.', 'wp-webhooks' )
			);

		}

		$real_fields = array(

			'wpwhpro_trigger_response_type' => array(
				'id'          => 'wpwhpro_trigger_response_type',
				'type'        => 'select',
				'label'       => __( 'Change the data request type', 'wp-webhooks' ),
				'choices'     => array(
					'json' => array( 'label' => 'JSON' ),
					'xml' => array( 'label' => 'XML' ),
					'form' => array( 'label' => 'X-WWW-FORM-URLENCODE' ),
					'form-data' => array( 'label' => 'FORM-DATA' ),
				),
				'placeholder' => '',
				'default_value' => '',
				'description' => __( 'Set a custom request type for the data that gets send to the specified URL. Default is JSON.', 'wp-webhooks' )
			),
			'wpwhpro_trigger_request_method' => array(
				'id'          => 'wpwhpro_trigger_request_method',
				'type'        => 'select',
				'label'       => __( 'Change the data request method', 'wp-webhooks' ),
				'choices'     => array(
					'POST' => array( 'label' => 'POST' ),
					'GET' => array( 'label' => 'GET' ),
					'HEAD' => array( 'label' => 'HEAD' ),
					'PUT' => array( 'label' => 'PUT' ),
					'DELETE' => array( 'label' => 'DELETE' ),
					'TRACE' => array( 'label' => 'TRACE' ),
					'OPTIONS' => array( 'label' => 'OPTIONS' ),
					'PATCH' => array( 'label' => 'PATCH' ),
				),
				'placeholder' => '',
				'default_value' => '',
				'description' => __( 'Set a custom request method for the data that gets send to the specified URL. Default is POST.', 'wp-webhooks' )
			),
			'wpwhpro_trigger_data_mapping' => array(
				'id'          => 'wpwhpro_trigger_data_mapping',
				'type'        => 'select',
				'label'       => __( 'Add request data mapping template', 'wp-webhooks' ),
				'choices'     => array(
					//Settings are loaded dynamically within the send-data.php page
					'0' => array( 'label' => __( 'Choose...', 'wp-webhooks' ) )
				),
				'placeholder' => '',
				'default_value' => '',
				'description' => __( 'Set a custom data mapping template to map your request (outgoing) data to your very own structure.', 'wp-webhooks' )
			),
			'wpwhpro_trigger_data_mapping_header' => array(
				'id'          => 'wpwhpro_trigger_data_mapping_header',
				'type'        => 'select',
				'label'       => __( 'Add request header data mapping template', 'wp-webhooks' ),
				'choices'     => array(
					//Settings are loaded dynamically within the send-data.php page
					'0' => array( 'label' => __( 'Choose...', 'wp-webhooks' ) )
				),
				'placeholder' => '',
				'default_value' => '',
				'description' => __( 'Set a custom data mapping template to map the header data of your request (outgoing data) to your very own structure.', 'wp-webhooks' )
			),
			'wpwhpro_trigger_data_mapping_cookies' => array(
				'id'          => 'wpwhpro_trigger_data_mapping_cookies',
				'type'        => 'select',
				'label'       => __( 'Add request cookie data mapping template', 'wp-webhooks' ),
				'choices'     => array(
					//Settings are loaded dynamically within the send-data.php page
					'0' => array( 'label' => __( 'Choose...', 'wp-webhooks' ) )
				),
				'placeholder' => '',
				'default_value' => '',
				'description' => __( 'Set a custom data mapping template to map the cookie data of the request (outgoing data) to your very own structure.', 'wp-webhooks' )
			),
			'wpwhpro_trigger_data_mapping_response' => array(
				'id'          => 'wpwhpro_trigger_data_mapping_response',
				'type'        => 'select',
				'label'       => __( 'Add response data mapping template', 'wp-webhooks' ),
				'choices'     => array(
					//Settings are loaded dynamically within the send-data.php page
					'0' => array( 'label' => __( 'Choose...', 'wp-webhooks' ) )
				),
				'placeholder' => '',
				'default_value' => '',
				'description' => __( 'Set a custom data mapping template to map the response data to your very own structure.', 'wp-webhooks' )
			),
			'wpwhpro_trigger_authentication' => array(
				'id'          => 'wpwhpro_trigger_authentication',
				'type'        => 'select',
				'label'       => __( 'Add authentication template', 'wp-webhooks' ),
				'choices'     => array(),
				'query'			=> array(
					'filter'	=> 'authentications',
					'args'		=> array()
				),
				'multiple'  => false,
				'placeholder' => '',
				'default_value' => '',
				'description' => __( 'Set a custom authentication template in case the other endpoint requires authentication.', 'wp-webhooks' )
			),
			'wpwhpro_trigger_allow_unsafe_urls' => array(
				'id'          => 'wpwhpro_trigger_allow_unsafe_urls',
				'type'        => 'checkbox',
				'label'       => __( 'Allow unsafe URLs', 'wp-webhooks' ),
				'placeholder' => '',
				'default_value' => '',
				'description' => __( 'Activating this setting allows you to use unsafe looking URLs like zfvshjhfbssdf.szfdhdf.com.', 'wp-webhooks' )
			),
			'wpwhpro_trigger_allow_unverified_ssl' => array(
				'id'          => 'wpwhpro_trigger_allow_unverified_ssl',
				'type'        => 'checkbox',
				'label'       => __( 'Allow unverified SSL', 'wp-webhooks' ),
				'placeholder' => '',
				'default_value' => '',
				'description' => __( 'Activating this setting allows you to use unverified SSL connections for this URL (We won\'t verify the SSL for this webhook URL).', 'wp-webhooks' )
			),
			'wpwhpro_trigger_wait_response' => array(
				'id'          => 'wpwhpro_trigger_wait_response',
				'type'        => 'checkbox',
				'label'       => __( 'Wait for response', 'wp-webhooks' ),
				'placeholder' => '',
				'default_value' => '',
				'description' => __( 'By default, the trigger is sent asynchronously to save loading time and performance. If you wish to wait for the response to come back, please activate this setting.', 'wp-webhooks' )
			),
			'wpwhpro_trigger_single_instance_execution' => array(
				'id'          => 'wpwhpro_trigger_single_instance_execution',
				'type'        => 'checkbox',
				'label'       => __( 'Fire only once per instance', 'wp-webhooks' ),
				'placeholder' => '',
				'default_value' => '',
				'description' => __( 'By default, our plugin is able to fire on an event multiple times (in case the event was called more than once per WordPress instance). If you check this box, we make sure to fire the webhook only once per instance. A WordPress instance is a single website call from beginning to end.', 'wp-webhooks' )
			),
			'wpwhpro_trigger_schedule' => array(
				'id'          => 'wpwhpro_trigger_schedule',
				'type'        => 'text',
				'label'       => __( 'Schedule trigger', 'wp-webhooks' ),
				'placeholder' => '',
				'default_value' => '',
				'description' => __( 'Schedule the trigger by a time of your choice. This field accepts an number containing the seconds of delay. E.g. 1 hour equals 3600 (60 seconds x 60 minutes).', 'wp-webhooks' )
			),

		);

		$fields = array_merge( $fields, $real_fields );

		return apply_filters('wpwhpro/settings/required_trigger_settings', $fields);
	}

	/**
	 * Load the default trigger settings.
	 *
	 * These settings can be loaded optionally with every single webhook trigger.
	 *
	 * @return array - the default trigger settings
	 */
	private function load_default_trigger_settings(){
		$fields = array(

			'wpwhpro_user_must_be_logged_in' => array(
				'id'          => 'wpwhpro_user_must_be_logged_in',
				'type'        => 'checkbox',
				'label'       => __( 'User must be logged in', 'wp-webhooks' ),
				'placeholder' => '',
				'default_value' => '',
				'description' => __( 'Check this button if you want to fire this webhook only when the user is logged in ( is_user_logged_in() function is used ).', 'wp-webhooks' )
			),
			'wpwhpro_user_must_be_logged_out' => array(
				'id'          => 'wpwhpro_user_must_be_logged_out',
				'type'        => 'checkbox',
				'label'       => __( 'User must be logged out', 'wp-webhooks' ),
				'placeholder' => '',
				'default_value' => '',
				'description' => __( 'Check this button if you want to fire this webhook only when the user is logged out ( ! is_user_logged_in() function is used ).', 'wp-webhooks' )
			),
			'wpwhpro_trigger_backend_only' => array(
				'id'          => 'wpwhpro_trigger_backend_only',
				'type'        => 'checkbox',
				'label'       => __( 'Trigger from backend only', 'wp-webhooks' ),
				'placeholder' => '',
				'default_value' => '',
				'description' => __( 'Check this button if you want to fire this trigger only from the backend. Every post submitted through the frontend is ignored ( is_admin() function is used ).', 'wp-webhooks' )
			),
			'wpwhpro_trigger_frontend_only' => array(
				'id'          => 'wpwhpro_trigger_frontend_only',
				'type'        => 'checkbox',
				'label'       => __( 'Trigger from frontend only', 'wp-webhooks' ),
				'placeholder' => '',
				'default_value' => '',
				'description' => __( 'Check this button if you want to fire this trigger only from the frontent. Every post submitted through the backend is ignored ( ! is_admin() function is used ).', 'wp-webhooks' )
			)

		);

		return apply_filters('wpwhpro/settings/default_trigger_settings', $fields);
	}

	/**
	 * Load the default settings for the main settings page
	 * of our plugin.
	 *
	 * @return array - an array of all available settings
	 */
	private function load_receivable_trigger_settings(){
		$fields = array(
			'wpwhpro_trigger_single_receivable_url' => array(
				'id'          => 'wpwhpro_trigger_single_receivable_url',
				'type'        => 'text',
				'label'       => __( 'Receivable trigger URL', 'wp-webhooks' ),
				'placeholder' => '',
				'default_value' => '',
				'readonly' => 'readonly',
				'copyable' => true,
				'description' => __( 'Sending data to this URL will fire the trigger with the data that was sent along.', 'wp-webhooks' )
			)
		);

		return apply_filters('wpwhpro/settings/receivable_trigger_settings', $fields);
	}

	/**
	 * Load the strictly necessary action settings
	 * to any available action.
	 *
	 * @return array - the action settings
	 */
	private function load_required_action_settings(){
		$fields = array(

			'wpwhpro_action_data_mapping' => array(
				'id'          => 'wpwhpro_action_data_mapping',
				'type'        => 'select',
				'label'       => __( 'Add request data mapping template', 'wp-webhooks' ),
				'choices'     => array(
					//Settings are loaded dynamically within the receive-data.php page
					'0' => array( 'label' => __( 'Choose...', 'wp-webhooks' ) )
				),
				'placeholder' => '',
				'default_value' => '',
				'description' => sprintf( __( 'Set a custom data mapping template to map your incoming data to the structure of %s.', 'wp-webhooks' ), $this->get_page_title() )
			),

			'wpwhpro_action_data_mapping_response' => array(
				'id'          => 'wpwhpro_action_data_mapping_response',
				'type'        => 'select',
				'label'       => __( 'Add response data mapping template', 'wp-webhooks' ),
				'choices'     => array(
					//Settings are loaded dynamically within the receive-data.php page
					'0' => array( 'label' => __( 'Choose...', 'wp-webhooks' ) )
				),
				'placeholder' => '',
				'default_value' => '',
				'description' => sprintf( __( 'Set a custom data mapping template to map the response data for the incoming webhook of %s.', 'wp-webhooks' ), $this->get_page_title() )
			),

			//Deprecated since 6.1.0
			'wpwhpro_action_authentication' => array(
				'id'          => 'wpwhpro_action_authentication',
				'type'        => 'select',
				'label'       => __( 'Add authentication template', 'wp-webhooks' ),
				'choices'     => array(),
				'query'			=> array(
					'filter'	=> 'authentications',
					'args'		=> array()
				),
				'multiple'  => false,
				'placeholder' => '',
				'default_value' => '',
				'deprecated' => true,
				'description' => __( 'Set a custom authentication template in case the other endpoint requires authentication. Currently, only API Keys and Basic Auth is allowed for webhook actions.', 'wp-webhooks' )
			),

			'wpwhpro_action_access_token' => array(
				'id'          => 'wpwhpro_action_access_token',
				'type'        => 'text',
				'label'       => __( 'Add access token', 'wp-webhooks' ),
				'placeholder' => '',
				'variable'		=> true,
				'default_value' => '',
				'description' => __( 'Set an access token for enhanced security. If set, you need to add another argument within your request called "access_token".', 'wp-webhooks' )
			),

			'wpwhpro_action_accepted_methods' => array(
				'id'          => 'wpwhpro_action_accepted_methods',
				'type'        => 'select',
				'label'       => __( 'Accepted request methods', 'wp-webhooks' ),
				'choices'     => array(
					'POST' => array( 'label' => 'POST' ),
					'GET' => array( 'label' => 'GET' ),
					'HEAD' => array( 'label' => 'HEAD' ),
					'PUT' => array( 'label' => 'PUT' ),
					'DELETE' => array( 'label' => 'DELETE' ),
					'TRACE' => array( 'label' => 'TRACE' ),
					'OPTIONS' => array( 'label' => 'OPTIONS' ),
					'PATCH' => array( 'label' => 'PATCH' ),
				),
				'multiple'    => true,
				'placeholder' => '',
				'default_value' => '',
				'description' => __( 'Choose which of the incoming request methods should be allowed on this webhook endpoint. If none is selected, all are allowed.', 'wp-webhooks' )
			),

			'wpwhpro_action_schedule' => array(
				'id'          => 'wpwhpro_action_schedule',
				'type'        => 'text',
				'label'       => __( 'Schedule action', 'wp-webhooks' ),
				'placeholder' => '',
				'default_value' => '',
				'description' => __( 'Schedule the action execution by a time of your choice. This field accepts an number containing the seconds of delay. E.g. 1 hour equals 3600 (60 seconds x 60 minutes). If you schedule an action, the execution begins after the number of seconds given.', 'wp-webhooks' )
			),

		);

		return apply_filters('wpwhpro/settings/required_action_settings', $fields);
	}

	/**
	 * Load the settings for our data mapping template
	 *
	 * @return array - the data mapping template settings
	 */
	private function load_data_mapping_template_settings(){
		$fields = array(

			'wpwhpro_data_mapping_whitelist_payload' => array(
				'id'          => 'wpwhpro_data_mapping_whitelist_payload',
				'type'        => 'select',
				'label'       => __( 'Whitelist/Blacklist Payload', 'wp-webhooks' ),
				'choices'     => array(
					'none' => array( 'label' => __( 'Choose..', 'wp-webhooks' ) ),
					'whitelist' => array( 'label' => __( 'Whitelist', 'wp-webhooks' ) ),
					'blacklist' => array( 'label' => __( 'Blacklist', 'wp-webhooks' ) ),
				),
				'placeholder' => '',
				'default_value' => '',
				'description' => __( 'Check this settings item to only send over the keys defined within this template (Whitelist) or every key except of the ones in this template. This way, you can prevents unnecessary data to be sent over via the endpoint. To only map a key without modifications, simply define the same key as the new key and assign the same key again. E.g.: user_email -> user_email', 'wp-webhooks' )
			),

		);

		return apply_filters('wpwhpro/settings/data_mapping_template_settings', $fields);
	}

	/**
	 * Load the settings for our data mapping template keys
	 *
	 * @return array - the action settings
	 */
	private function load_data_mapping_key_settings(){
		$fields = array(

			'wpwhpro_data_mapping_value_type' => array(
				'id'          => 'wpwhpro_data_mapping_value_type',
				'type'        => 'select',
				'label'       => __( 'Value Type', 'wp-webhooks' ),
				'choices'     => array(
					'key_mapping' => __( 'Mapping Key', 'wp-webhooks' ),
					'data_value' => __( 'Data Value', 'wp-webhooks' ),
				),
				'placeholder' => '',
				'default_value' => '',
				'description' => __( 'Choose "Mapping Key" if you want to use the above value to map it to the incoming data. Use "Data Value" in case you want to create a new, static value.', 'wp-webhooks' )
			),

			'wpwhpro_data_mapping_convert_data' => array(
				'id'          => 'wpwhpro_data_mapping_convert_data',
				'type'        => 'select',
				'label'       => __( 'Convert Data To', 'wp-webhooks' ),
				'choices'     => array(
					'none' => __( 'Choose...', 'wp-webhooks' ),
					'string' => __( 'String', 'wp-webhooks' ),
					'integer' => __( 'Integer', 'wp-webhooks' ),
					'float' => __( 'Float', 'wp-webhooks' ),
					'null' => __( 'Null', 'wp-webhooks' ),
					'bool' => __( 'Bool', 'wp-webhooks' ),
				),
				'placeholder' => '',
				'default_value' => '',
				'description' => __( 'In case you need to convert the current value to a specific format, you can do that with this setting sitem.', 'wp-webhooks' )
			),

			'wpwhpro_data_mapping_decode_data' => array(
				'id'          => 'wpwhpro_data_mapping_decode_data',
				'type'        => 'select',
				'label'       => __( 'Format value', 'wp-webhooks' ),
				'choices'     => array(
					'none' => __( 'Choose...', 'wp-webhooks' ),
					'json_encode' 	=> __( 'JSON Encode', 'wp-webhooks' ),
					'json_decode' 	=> __( 'JSON Decode', 'wp-webhooks' ),
					'serialize' 	=> __( 'Serialize', 'wp-webhooks' ),
					'unserialize' 	=> __( 'Unserialize', 'wp-webhooks' ),
					'urlencode' 	=> __( 'URL Encode', 'wp-webhooks' ),
					'urldecode' 	=> __( 'URL Decode', 'wp-webhooks' ),
					'stripslashes' 	=> __( 'Strip Slashes', 'wp-webhooks' ),
					'addslashes' 	=> __( 'Add Slashes', 'wp-webhooks' ),
				),
				'placeholder' 	=> '',
				'default_value' => '',
				'description' 	=> __( 'Reformat the given mapping key or value. For incoming data, use JSON Encode and Serialize to format all the data within the given key value as a encoded/serialized string - this causes the value to be treated as a string. Use the JSON decode and unserialize values to make data available that is currently saved as a serialized/encoded string - this is useful if the API sends over a JSON string as a value and you want to access only a single value within this JSON string - once decoded, you can access the whole data within this mapping line. The add/strip slashes will sanitize the given value. This is useful if you want to add slashed to make values compatible with JSON constructs.', 'wp-webhooks' )
			),

			'wpwhpro_data_mapping_fallback_value' => array(
				'id'          => 'wpwhpro_data_mapping_fallback_value',
				'type'        => 'text',
				'label'       => __( 'Fallback Value', 'wp-webhooks' ),
				'placeholder' => '',
				'default_value' => '',
				'description' => __( 'In case you use the value type "Mapping Key", this value will be used as a fallback mapping key in case the initial value is not available within the payload. If you use the value type "Data Value", this value will be used if your given value is empty.', 'wp-webhooks' )
			),

		);

		return apply_filters('wpwhpro/settings/data_mapping_key_settings', $fields);
	}

	/**
	 * Load all available authentication methods
	 *
	 * @return array - the action settings
	 */
	private function load_authentication_methods(){
		$methods = array(
			//APi Key Authentication
			'api_key' => array(
				'name' => __( 'API Key', 'wp-webhooks' ),
				'short_description' => __( 'Add an API key to your request header/body', 'wp-webhooks' ),
				'description' => '',
				'fields' => array(

					'wpwhpro_auth_api_key_key' => array(
						'id'          => 'wpwhpro_auth_api_key_key',
						'type'        => 'text',
						'label'       => __( 'Key', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'short_description' => __( 'Set the key you have to use to recognize the API key from the other endpoint.', 'wp-webhooks' ),
						'description' => __( 'Set the key you have to use to recognize the API key from the other endpoint.', 'wp-webhooks' ),
					),

					'wpwhpro_auth_api_key_value' => array(
						'id'          => 'wpwhpro_auth_api_key_value',
						'type'        => 'text',
						'label'       => __( 'Value', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'short_description' => __( 'This is the field you can include your API key. ', 'wp-webhooks' ),
						'description' => '',
					),

					'wpwhpro_auth_api_key_add_to' => array(
						'id'          => 'wpwhpro_auth_api_key_add_to',
						'type'        => 'select',
						'label'       => __( 'Add to', 'wp-webhooks' ),
						'choices'     => array(
							'header' => array( 'label' => __( 'Header', 'wp-webhooks' ) ),
							'body' => array( 'label' => __( 'Body', 'wp-webhooks' ) ),
							'both' => array( 'label' => __( 'Header & Body', 'wp-webhooks' ) ),
						),
						'placeholder' => '',
						'default_value' => '',
						'short_description' => __( 'Choose where you want to place the API Key within the request.', 'wp-webhooks' ),
						'description' => '',
					),

				),
			),

			//Bearer Token Authentication
			'bearer_token' => array(
				'name' => __( 'Bearer Token', 'wp-webhooks' ),
				'short_description' => __( 'Authenticate yourself on an external API using a Bearer token.', 'wp-webhooks' ),
				'description' => '',
				'fields' => array(
					'wpwhpro_auth_bearer_token_scheme' => array(
						'id'          => 'wpwhpro_auth_bearer_token_scheme',
						'type'        => 'text',
						'label'       => __( 'Scheme', 'wp-webhooks' ),
						'placeholder' => 'Bearer',
						'default_value' => 'Bearer',
						'short_description' => __( 'In case you use a custom scheme, you can adjust it here. In most cases, you can leave it at "Bearer".', 'wp-webhooks' ),
						'description' => '',
					),
					'wpwhpro_auth_bearer_token_token' => array(
						'id'          => 'wpwhpro_auth_bearer_token_token',
						'type'        => 'text',
						'label'       => __( 'Token', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'short_description' => __( 'Add the bearer token you received from the other endpoint here. Please add only the token, without the "Bearer " in front.', 'wp-webhooks' ),
						'description' => '',
					),
				),
			),

			//Basic Authentication
			'basic_auth' => array(
				'name' => __( 'Basic Auth', 'wp-webhooks' ),
				'short_description' => __( 'Authenticate yourself on an external API using Basic Authentication.', 'wp-webhooks' ),
				'description' => '',
				'fields' => array(
					'wpwhpro_auth_basic_auth_username' => array(
						'id'          => 'wpwhpro_auth_basic_auth_username',
						'type'        => 'text',
						'label'       => __( 'Username', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'short_description' => __( 'Add the username you want to use for the authentication.', 'wp-webhooks' ),
						'description' => '',
					),
					'wpwhpro_auth_basic_auth_password' => array(
						'id'          => 'wpwhpro_auth_basic_auth_password',
						'type'        => 'text',
						'label'       => __( 'Password', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'short_description' => __( 'Add the password you want to use for the authentication.', 'wp-webhooks' ),
						'description' => '',
					),
				),
			),

			//Digest Authentication
			'digest_auth' => array(
				'name' => __( 'Digest Auth', 'wp-webhooks' ),
				'short_description' => __( 'Authenticate yourself on an external API using Digest Authentication.', 'wp-webhooks' ),
				'description' => '',
				'fields' => array(
					'wpwhpro_auth_digest_auth_username' => array(
						'id'          => 'wpwhpro_auth_digest_auth_username',
						'type'        => 'text',
						'label'       => __( 'Username', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'short_description' => __( 'Add the username you want to use for the authentication.', 'wp-webhooks' ),
						'description' => '',
					),
					'wpwhpro_auth_digest_auth_password' => array(
						'id'          => 'wpwhpro_auth_digest_auth_password',
						'type'        => 'text',
						'label'       => __( 'Password', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'short_description' => __( 'Add the password you want to use for the authentication.', 'wp-webhooks' ),
						'description' => '',
					),
					'wpwhpro_auth_digest_auth_realm' => array(
						'id'          => 'wpwhpro_auth_digest_auth_realm',
						'type'        => 'text',
						'label'       => __( 'Realm', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'short_description' => __( 'A string specified by the server in the WWW-Authenticate response header. It should contain at least the name of the host performing the authentication.', 'wp-webhooks' ),
						'description' => '',
					),
					'wpwhpro_auth_digest_auth_nonce' => array(
						'id'          => 'wpwhpro_auth_digest_auth_nonce',
						'type'        => 'text',
						'label'       => __( 'Nonce', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'short_description' => __( 'A unique string specified by the server in the WWW-Authenticate response header.', 'wp-webhooks' ),
						'description' => '',
					),
					'wpwhpro_auth_digest_auth_qop' => array(
						'id'          => 'wpwhpro_auth_digest_auth_qop',
						'type'        => 'text',
						'label'       => __( 'Quality of protection (qop)', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'short_description' => __( 'This value is given by the server in the WWW-Authenticate response header.', 'wp-webhooks' ),
						'description' => '',
					),
					'wpwhpro_auth_digest_auth_nonce_count' => array(
						'id'          => 'wpwhpro_auth_digest_auth_nonce_count',
						'type'        => 'text',
						'label'       => __( 'Nonce Count', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'short_description' => __( 'The hexadecimal count of the number of requests (Including the current request) that the client has sent (within this request) with the nonce value. This value must be specified if a quality of service (qop) value is sentand must not be specified of the server did not sent a qop directive within the WWW-Authenticate response header.', 'wp-webhooks' ),
						'description' => '',
					),
					'wpwhpro_auth_digest_auth_client_nonce' => array(
						'id'          => 'wpwhpro_auth_digest_auth_client_nonce',
						'type'        => 'text',
						'label'       => __( 'Client Nonce', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'short_description' => __( 'An opaque quoted string value provided by the client and used by both the client and the server to avoid chosen plaintext attacks, to provide mutual authentication and to provide message integrity protection.', 'wp-webhooks' ),
						'description' => __( 'This must bbe specified if a quality of service (quo) directive is set, and must not be specified if the server did not send a qop directive in the WWW-Authenticate response header.', 'wp-webhooks' ),
					),
					'wpwhpro_auth_digest_auth_opaque' => array(
						'id'          => 'wpwhpro_auth_digest_auth_opaque',
						'type'        => 'text',
						'label'       => __( 'Opaque', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'short_description' => __( 'This is a string of data, specified by the server in the WWW-Authenticate response header and should be used here unchanged with URIs in the same protection space.', 'wp-webhooks' ),
						'description' => '',
					),
				),
			),

		);

		return apply_filters('wpwhpro/settings/authentication_methods', $methods);
	}

	/**
	 * Initialize all available, active webhooks
	 *
	 * @deprecated deprecated since version 4.0.0
	 * @return array - active webhooks
	 */
	public function setup_active_webhooks(){

		$webhooks = array(
			'triggers' => array(),
			'actions' => array(),
		);

		$triggers_data = WPWHPRO()->webhook->get_triggers();
		if( ! empty( $triggers_data ) && is_array( $triggers_data ) ){
			foreach( $triggers_data as $td ){
				$trigger = $td['trigger'];
				$webhooks['triggers'][ $trigger ] = array();
			}
		}

		$action_data = WPWHPRO()->webhook->get_actions();
		if( ! empty( $action_data ) && is_array( $action_data ) ){
			foreach( $action_data as $td ){
				$action = $td['action'];
				$webhooks['actions'][ $action ] = array();
			}
		}

		$this->active_webhooks = $webhooks;

		return $webhooks;
	}

	/*
	 * ######################
	 * ###
	 * #### TRANSLATEABLE STRINGS
	 * ###
	 * ######################
	 */

	 /**
	  * Default settings that are used multiple times throughout the page
	  *
	  * @return array - the default settings
	  */
	private function load_default_strings(){
		$trans_arr = array(
			'sufficient-permissions'    => 'You do not have sufficient permissions to access this page.',
		);

		return apply_filters( 'wpwhpro/admin/default_strings', $trans_arr );
	}

	/**
	 * ######################
	 * ###
	 * #### CALLABLE FUNCTIONS
	 * ###
	 * ######################
	 */

	/**
	 * Our admin cap handler function
	 *
	 * This function handles the admin capability throughout
	 * the whole plugin.
	 *
	 * $target - With the target function you can make a more precised filtering
	 * by changing it for specific actions.
	 *
	 * @param string $target - A identifier where the call comes from
	 * @return mixed
	 */
	public function get_admin_cap($target = 'main'){
		/**
		 * Customize the globally used capability for this plugin
		 *
		 * This filter is called every time the capability is needed.
		 */
		return apply_filters( 'wpwhpro/admin/settings/capability', $this->admin_cap, $target );
	}

	/**
	 * Return the page name for our admin page
	 * 
	 * @since 5.0
	 *
	 * @return string - the page name
	 */
	public function get_site_ident(){

		if( $this->site_ident === null ){
			$this->site_ident = $this->maybe_generate_site_ident();
		}

		return $this->site_ident;
	}

	/**
	 * Return the page name for our admin page
	 *
	 * @return string - the page name
	 */
	public function get_page_name(){
		/*
		 * Filter the page name based on your needs
		 */
		return apply_filters( 'wpwhpro/admin/settings/page_name', $this->page_name );
	}

	/**
	 * Return the page title for our admin page
	 *
	 * @return string - the page title
	 */
	public function get_page_title(){
		/*
		 * Filter the page title based on your needs.
		 */
		return apply_filters( 'wpwhpro/admin/settings/page_title', $this->page_title );
	}

	/**
	 * Return the log table data from our custom logging table
	 *
	 * @return string - the log table data
	 */
	public function get_log_table_data(){
		/*
		 * Filter the log table data based on your needs.
		 */
		return apply_filters( 'wpwhpro/admin/settings/log_table_data', $this->log_table_data );
	}

	/**
	 * Return the data mapping table data
	 *
	 * @return string - the log table data
	 */
	public function get_data_mapping_table_data(){
		/*
		 * Filter the data mapping table data based on your needs.
		 */
		return apply_filters( 'wpwhpro/admin/settings/data_mapping_table_data', $this->data_mapping_table_data );
	}

	/**
	 * Return the authentication table data
	 *
	 * @return string - the log table data
	 */
	public function get_authentication_table_data(){
		/*
		 * Filter the authentication table data based on your needs.
		 */
		return apply_filters( 'wpwhpro/admin/settings/authentication_table_data', $this->authentication_table_data );
	}
	/**
	 * Return the flows table data
	 *
	 * @return string - the log table data
	 */
	public function get_flows_table_data(){
		/*
		 * Filter the flows table data based on your needs.
		 */
		return apply_filters( 'wpwhpro/admin/settings/flows_table_data', $this->flows_table_data );
	}

	/**
	 * Return the flows common tag definition
	 *
	 * @return void
	 */
	public function get_flow_common_tags(){
		return apply_filters( 'wpwhpro/flows/get_common_tags', $this->flow_common_tags );
	}

	/**
	 * Return the page title for our admin page
	 *
	 * @param $value - A single license value field
	 *
	 * @return mixed - the page title
	 */
	public function get_license($value = ''){

		if( ! empty( $value ) ){
			return isset( $this->license[ $value ] ) ? $this->license[ $value ] : false;
		} else {
			return $this->license;
		}

	}

	/**
	 * Return the license option key
	 *
	 * @return string - the option key
	 */
	public function get_license_option_key(){

		return $this->license_option_key;

	}

	/**
	 * Return the site identifier option key
	 * 
	 * @since 5.0
	 *
	 * @return string - the option key
	 */
	public function get_site_identifier_key(){

		return $this->site_identifier_key;

	}

	/**
	 * Return the license transient key
	 *
	 * @return string - the transient key
	 */
	public function get_license_transient_key(){

		return $this->license_transient_key;

	}

	/**
	 * Return the webhook option key
	 *
	 * @return string - the option key
	 */
	public function get_webhook_option_key(){

		return $this->webhook_settings_key;

	}

	/**
	 * Return the whitelist option key
	 *
	 * @return string - the option key
	 */
	public function get_whitelist_option_key(){

		return $this->whitelist_settings_key;

	}

	/**
	 * Return the whitelist requests option key
	 *
	 * @return string - the option key
	 */
	public function get_whitelist_requests_option_key(){

		return $this->whitelist_requested_key;

	}

	/**
	 * Return the whitelabel settings option key
	 *
	 * @return string - the option key
	 */
	public function get_whitelabel_settings_option_key(){

		return $this->whitelabel_settings_key;

	}

	/**
	 * Return the news transient key
	 *
	 * @return string - the news transient key
	 */
	public function get_news_transient_key(){

		return $this->news_transient_key;

	}

	/**
	 * Return the extensions transient key
	 *
	 * @return string - the extensions transient key
	 */
	public function get_extensions_transient_key(){

		return $this->extensions_transient_key;

	}

	/**
	 * Return the integrations transient key
	 *
	 * @since 6.0
	 * @return string - the integrations transient key
	 */
	public function get_integrations_transient_key(){

		return $this->integrations_transient_key;

	}

	/**
	 * Return the previous versions key
	 *
	 * @since 6.0
	 * @return string - the previous versions key
	 */
	public function get_wpwh_previous_versions_key(){

		return $this->wpwh_previous_versions_key;

	}

	/**
	 * Return the parameter that is used to identify incoming webhook actions
	 *
	 * @return string - the webhook action query argument
	 */
	public function get_webhook_ident_param(){
		/*
		 * Filter the page title based on your needs.
		 */
		return apply_filters( 'wpwhpro/admin/settings/webhook_ident_param', $this->webhook_ident_param );
	}

	/**
	 * Return the parameter that is used for receivable webhook triggers
	 *
	 * @since 4.3.7
	 *
	 * @return string - the receivable webhook trigger query argument
	 */
	public function get_webhook_receivable_trigger_param(){
		return apply_filters( 'wpwhpro/admin/settings/webhook_receivable_trigger_param', $this->get_webhook_receivable_trigger_param );
	}

	/**
	 * Return the default integration depenencies
	 *
	 * @return array - the default integration depenencies
	 */
	public function get_default_integration_dependencies(){
		return apply_filters( 'wpwhpro/admin/settings/default_integration_dependencies', $this->default_integration_dependencies );
	}

	/**
	 * Return the license nonce data
	 *
	 * @return array - the license nonce data
	 */
	public function get_license_nonce(){

		return $this->license_nonce;

	}

	/**
	 * Return the settings nonce data
	 *
	 * @return array - the settings nonce data
	 */
	public function get_settings_nonce(){

		return $this->settings_nonce;

	}

	/**
	 * Return the wizard nonce data
	 *
	 * @return array - the wizard nonce data
	 */
	public function get_wizard_nonce(){

		return $this->wizard_nonce;

	}

	/**
	 * Return the tools import nonce data
	 *
	 * @return array - the tools import nonce data
	 */
	public function get_tools_import_nonce(){

		return $this->tools_import_nonce;

	}

	/**
	 * Return the whitelist nonce data
	 *
	 * @return array - the whitelist nonce data
	 */
	public function get_whitelist_nonce(){

		return $this->whitelist_nonce;

	}

	/**
	 * Return the action nonce data
	 *
	 * @return array - the action nonce data
	 */
	public function get_action_nonce(){

		return $this->action_nonce;

	}

	/**
	 * Return the trigger nonce data
	 *
	 * @return array - the trigger nonce data
	 */
	public function get_trigger_nonce(){

		return $this->trigger_nonce;

	}

	/**
	 * Return the log nonce data
	 *
	 * @return array - the log nonce data
	 */
	public function get_log_nonce(){

		return $this->log_nonce;

	}

	/**
	 * Return the flows nonce data
	 *
	 * @return array - the flows nonce data
	 */
	public function get_flows_nonce(){

		return $this->flows_nonce;

	}

	/**
	 * Return the data mapping nonce data
	 *
	 * @return array - the data mapping nonce data
	 */
	public function get_data_mapping_nonce(){

		return $this->data_mapping_nonce;

	}

	/**
	 * Return the authentication nonce data
	 *
	 * @return array - the authentication nonce data
	 */
	public function get_authentication_nonce(){

		return $this->authentication_nonce;

	}

	/**
	 * Return the whitelabel nonce data
	 *
	 * @return array - the whitelabel nonce data
	 */
	public function get_whitelabel_nonce(){

		return $this->whitelabel_nonce;

	}

	/**
	 * Return the scripts and styles for the no-conflict mode
	 * 
	 * @since 5.2.2
	 *
	 * @return array - the scripts and styles
	 */
	public function get_no_conflict_scripts_styles( $type = 'all' ){

		$scripts_styles = apply_filters( 'wpwhpro/admin/settings/no_conflict_scripts_styles', $this->no_conflict_scripts_styles, $type );

		switch( $type ){
			case 'scripts':
				$scripts_styles = $scripts_styles['scripts'];
				break;
			case 'styles':
				$scripts_styles = $scripts_styles['styles'];
				break;
		}

		return $scripts_styles;
	}

	/**
	 * Return the settings data
	 *
	 * @return array - the settings data
	 */
	public function get_settings(){

		return $this->default_settings;

	}

	/**
	 * Return the whitelabel settings data
	 *
	 * @return array - the whitelabel settings data
	 */
	public function get_whitelabel_settings( $only_values = false ){

		$field_values = $this->whitelabel_settings;

		if( $only_values ){
			$only_field_values = array();

			foreach( $field_values as $key => $data ){
				if( isset( $data['value'] ) ){
					$only_field_values[ $key ] = $data['value'];
				}
			}

			$field_values = $only_field_values;
		}

		return $field_values;

	}

	/**
	 * Return the required trigger settings data
	 *
	 * @since 1.6.5
	 * @return array - the default trigger settings data
	 */
	public function get_required_trigger_settings(){

		return $this->required_trigger_settings;

	}

	/**
	 * Return the default trigger settings data
	 *
	 * @since 1.6.4
	 * @return array - the default trigger settings data
	 */
	public function get_default_trigger_settings(){

		return $this->default_trigger_settings;

	}

	/**
	 * Return the receivable trigger settings data
	 *
	 * @since 4.3.7
	 * @return array - the receivable trigger settings data
	 */
	public function get_receivable_trigger_settings(){

		return $this->receivable_trigger_settings;

	}

	/**
	 * Return the required action settings data
	 *
	 * @since 2.0.0
	 * @return array - the default action settings data
	 */
	public function get_required_action_settings(){

		return $this->required_action_settings;

	}

	/**
	 * Return the data mapping template settings data
	 *
	 * @since 3.0.6
	 * @return array - the default data mapping template settings data
	 */
	public function get_data_mapping_template_settings(){

		return $this->data_mapping_template_settings;

	}

	/**
	 * Return the data mapping key settings data
	 *
	 * @since 3.0.6
	 * @return array - the default data mapping key settings data
	 */
	public function get_data_mapping_key_settings(){

		return $this->data_mapping_key_settings;

	}

	/**
	 * Return the flow status labels
	 *
	 * @since 4.3.0
	 * @return array - the flow status labels
	 */
	public function get_flow_condition_labels(){

		return apply_filters( 'wpwhpro/admin/settings/flow_condition_labels', $this->flow_condition_labels );

	}

	/**
	 * Return the flow action status labels
	 *
	 * @since 5.2.4
	 * @return array - the flow status labels
	 */
	public function get_flow_condition_action_labels(){

		return apply_filters( 'wpwhpro/admin/settings/flow_condition_action_labels', $this->flow_condition_action_labels );

	}

	/**
	 * Return the flow status labels
	 *
	 * @since 4.3.0
	 * @return array - the flow status labels
	 */
	public function get_flow_status_labels(){

		return apply_filters( 'wpwhpro/admin/settings/flow_status_labels', $this->flow_status_labels );

	}

	/**
	 * Return all default authentication methods
	 *
	 * @since 3.0.0
	 * @return array - all available authentication methods
	 */
	public function get_authentication_methods(){

		return $this->authentication_methods;

	}

	/**
	 * Return the active webhook ident
	 *
	 * @return string - the active webhook ident
	 */
	public function get_active_webhooks_ident(){

		return $this->active_webhook_ident_param;

	}

	/**
	 * Return the currently active webhooks
	 *
	 * @param string $type - wether you want to receive active webhooks or triggers
	 *
	 * @deprecated deprecated since version 4.0.0
	 * @return array - the active webhooks
	 */
	public function get_active_webhooks( $type = 'all' ){

		if( $this->active_webhooks === null ){
			$return = $this->setup_active_webhooks();
		} else {
			$return = $this->active_webhooks;
		}

		switch( $type ){
			case 'actions':
				$return = $this->active_webhooks['actions'];
				break;
			case 'triggers':
				$return = $this->active_webhooks['triggers'];
				break;
		}

		return $return;

	}

	/**
	 * Return the default strings that are available
	 * for this plugin.
	 *
	 * @param $cname - the identifier for your specified string
	 * @return string - the default string
	 */
	public function get_default_string( $cname ){
		$return = '';

		if(empty( $cname )){
			return $return;
		}

		if( isset( $this->trans_strings[ $cname ] ) ){
			$return = $this->trans_strings[ $cname ];
		}

		return $return;
	}

	public function get_all_post_statuses(){

		$post_statuses = array();

		//Merge default statuses
		$post_statuses = array_merge( $post_statuses, get_post_statuses() );

		//Merge woocommerce statuses
		if ( class_exists( 'WooCommerce' ) && function_exists( 'wc_get_order_statuses' ) ) {
			$post_statuses = array_merge( $post_statuses, wc_get_order_statuses() );
		}


		return apply_filters( 'wpwhpro/admin/settings/get_all_post_statuses', $post_statuses );
	}

	/**
	 * Return the default wpwh_call_action details
	 *
	 * @return array
	 */
	public function get_wpwh_call_action_action(){

		$action_data = array(
			'id' => 'wpwh_call_action',
			'type' => 'text',
			'required' => false,
			'variable' => true,
			'label' => __( 'WordPress action callback (Advanced)', 'wp-webhooks' ), 
			'short_description' => __( '(String) Register a custom WordPress hook callback. The value will be called as followed: do_action( $yourdefinedaction, $action_return_data, $request_data )', 'wp-webhooks' ),
			'description' => __( '(String) Register a custom WordPress hook callback. The value will be called as followed: do_action( $yourdefinedaction, $action_return_data, $request_data )', 'wp-webhooks' ),
		);

		ob_start();
		?>
<?php echo __( "The <strong>wpwh_call_action</strong> argument allows you to fire custom PHP code after the initial action fired. Thsi way, you can register custom code depending on your needs.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "Below you will find an example that shows you a code snippet you can use to receive the data within the custom code:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>wpwh_call_action</strong> argument <strong>fire_this_php_function</strong> as a value. In this case, we will trigger an action with the hook name <strong>fire_this_php_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_php_function', 'fire_this_php_function_callback', 20, 2 );
function fire_this_php_function_callback( $return_data, $request_data ){

	// This is how you can accept data from the request directly within the callback function
	// In this case, we assume you set an argument called "date" with a value of "23.12.2022"
	// This will cause the variale $date to contain the date "23.12.2022"
	$date = WPWHPRO()->helpers->validate_request_value( $request_data['content'], 'date' );

	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$return_data</strong> (array)<br>
		<?php echo __( "This variable contains all the data that are sent back as a response to the initial webhook action caller.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$request_data</strong> (object)<br>
		<?php echo __( "The data that was sent along within the request from the initial webhook caller.", 'wp-webhooks' ); ?>
	</li>
</ol>
		<?php
		$action_data['description'] = ob_get_clean();

		return apply_filters( 'wpwhpro/admin/settings/get_wpwh_call_action_action', $action_data );
	}

	public function save_settings( $new_settings, $update_all = true ){
		$success = false;

		if( empty( $new_settings ) ) {
			return $success;
		}

		$settings = WPWHPRO()->settings->get_settings();

		// START General Settings
		foreach( $settings as $settings_name => $setting ){

			if( ! $update_all && ! isset( $new_settings[ $settings_name ] ) ){
				continue;
			}

			$value = '';

			if( $setting['type'] == 'checkbox' ){
				if( ! isset( $new_settings[ $settings_name ] ) || ! $new_settings[ $settings_name ] ){
					$value = 'no';
				} else {
					$value = 'yes';
				}
			} elseif( $setting['type'] == 'text' ){
				if( isset( $new_settings[ $settings_name ] ) ){
					$value = sanitize_title( $new_settings[ $settings_name ] );
				}
			} elseif( $setting['type'] == 'select' ){
				if( isset( $new_settings[ $settings_name ] ) ){
					$value = esc_html( $new_settings[ $settings_name ] );
				}
			}

			update_option( $settings_name, $value );
			$settings[ $settings_name ][ 'value' ] = $value;
		}
		// END General Settings

		$success = true;

		do_action( 'wpwh/admin/settings/settings_saved', $new_settings );

		return $success;
	 }

	public function save_whitelabel_settings( $new_settings ){
		$success = false;

		if( empty( $new_settings ) ) {
			return $success;
		}

		$settings = WPWHPRO()->settings->get_whitelabel_settings();
		$whitelabel_settings_data = get_option( $this->whitelabel_settings_key );

		// START General Settings
		foreach( $settings as $settings_name => $setting ){

			$value = '';

			if( $setting['type'] == 'checkbox' ){
				if( ! isset( $new_settings[ $settings_name ] ) ){
					$value = 'no';
				} else {
					$value = 'yes';
				}
			} elseif( $setting['type'] == 'text' ){
				if( isset( $new_settings[ $settings_name ] ) ){
					$value = sanitize_text_field( $new_settings[ $settings_name ] );
				}
			} elseif( $setting['type'] == 'textarea' ){
				if( isset( $new_settings[ $settings_name ] ) ){
					$value = $new_settings[ $settings_name ];
				}
			}

			$whitelabel_settings_data[ $settings_name ] = $value;
		}

		update_option( $this->whitelabel_settings_key, $whitelabel_settings_data );

		//relad settings
		$this->whitelabel_settings = $this->load_whitelabel_settings();

		$success = true;

		do_action( 'wpwh/admin/settings/settings_saved', $new_settings );

		return $success;
	}

}