<?php

/**
 * WP_Webhooks_Pro_Integrations Class
 *
 * This class contains all of the webhook integrations
 *
 * @since 4.2.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * The webhook integration class of the plugin.
 *
 * @since 4.2.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Integrations {

	/**
	 * If an action call is present, this var contains the webhook
	 *
	 * @since 4.2.0
	 * @var - The currently present action webhook
	 */
	public $integrations = array();

	/**
	 * A cached version of all the available actions
	 *
	 * @since 5.0
	 * @var - The currently present action webhook
	 */
	private $actions = null;

	/**
	 * The async class
	 *
	 * @since 6.0
	 * @var - The class for asynchronous tasks
	 */
	private $async_class = array();

	/**
	 * A temporary bool used to determine the local folder
     * This variable will be removed in a future release
	 *
	 * @since 6.0
	 * @var
	 */
	private $use_local_folder = null;

    /**
	 * Execute feature related hooks and logic to get 
	 * everything running
	 *
	 * @since 5.0
	 * @return void
	 */
	public function execute(){

        if( $this->use_local_folder === null ){
            $this->use_local_folder = get_option( 'wpwhpro_load_local_integrations' );
        }

		add_action( 'plugins_loaded', array( $this, 'load_integrations' ), 10 );

		add_action( 'plugins_loaded',  array( $this, 'load_async_classes' ) );
		add_action( 'wpwhpro/migrate/plugin_updated',  array( $this, 'maybe_update_integrations' ), 20 );
        add_filter( 'wpwhpro/async/process/wpwh_install_integrations',  array( $this, 'wpwh_install_integrations_callback' ), 20 );
        add_filter( 'wpwhpro/async/process/completed/wpwh_install_integrations',  array( $this, 'wpwh_install_integrations_completed_callback' ), 20 );
        add_action( 'wp_ajax_ironikus_manage_integrations',  array( $this, 'ironikus_manage_integrations' ) );

        add_shortcode( 'WPWH_INTEGRATION_MANAGER', array( $this, 'load_integrations_manager' ) );

	}

    /**
     * ######################
     * ###
     * #### SHORTCODES
     * ###
     * ######################
     */

     /**
      * The integrations manager shortcode
      * This is used to display HTML that allows
      * the user to manage the integrations
      *
      * @since 6.0
      * @param array $attr
      * @param string $content
      * @return string The content
      */
     public function load_integrations_manager( $attr = array(), $content = '' ){

        $wpwh_page = WPWHPRO_PLUGIN_DIR . 'core/includes/partials/misc/integrations-manager.php';

        ob_start();

        if( file_exists( $wpwh_page ) ){
            include( $wpwh_page );
        }
        
        $html = ob_get_clean(); 

        return $html;
     }

    /**
     * ######################
     * ###
     * #### INTEGRATION Installer
     * ###
     * ######################
     */

     /**
      * Load async classes for integrations
      *
      * @return void
      */
     public function load_async_classes(){
        $this->load_async_class( 'wpwh_install_integrations' );
     }

     /**
	 * Get the async class
	 *
	 * @since 6.0
	 *
	 * @return WP_Webhooks_Pro_Async_Process
	 */
	public function get_async( $action ){

		if( ! isset( $this->async_class[ $action ] ) ){
			$this->load_async_class( $action );
		}

		return $this->async_class[ $action ];
	}

	/**
	 * Initiate the Flow async class
	 *
	 * @since 5.0
	 *
	 * @return void
	 */
	public function load_async_class( $action ){
		$this->async_class[ $action ] = WPWHPRO()->async->new_process( array(
			'action' => $action
		) );
	}

     /**
      * Maybe update an integration on a plugin update
      *
      * @return void
      */
     public function maybe_update_integrations(){

        $upgrader_lock = get_transient( 'wpwh_integrations_updater_lock' );

        if( ! empty( $upgrader_lock ) && ! isset( $_GET['wpwhpro_renew_transient'] ) ){
            return;
        }

        set_transient( 'wpwh_integrations_updater_lock', true, MINUTE_IN_SECONDS * 10 );
        
        //fetch integrations from the registered folder
        $integration_folders = $this->get_integrations_directories();
         if( is_array( $integration_folders ) && ! empty( $integration_folders ) ){

            //prepare a new queue
            $this->get_async( 'wpwh_install_integrations' )->clear_queue();

             foreach( $integration_folders as $integration ){
                $integration = sanitize_title( $integration );
                $this->get_async( 'wpwh_install_integrations' )->push_to_queue( array( 'integration' => $integration ) ); 
             }

			//dispatch
			$this->get_async( 'wpwh_install_integrations' )->save()->dispatch();   
         }

    }

    /**
     * The asynchronous integrations callback
     *
     * @param array $data
     * @return void
     */
    public function wpwh_install_integrations_callback( $data ){

        $return = false;

        if( ! isset( $data['integration'] ) || empty( $data['integration'] ) ){
            return $return;
        }

        $integration = sanitize_title( $data['integration'] );

        $is_installed = $this->maybe_install_integration( $integration, true );
        if( $is_installed ){
            $return = true;
        }

        return $return;
    }

    /**
     * The callback function on an async installation task
     *
     * @since 6.0
     * @param object $class
     * @return void
     */
    public function wpwh_install_integrations_completed_callback( $class ){

        //clean the lock transient and return the process as usual
        delete_transient( 'wpwh_integrations_updater_lock' );

    }

    /**
     * The ajax handler to manage the instegrations
     *
     * @since 6.0
     * @return void
     */
	public function ironikus_manage_integrations(){
        check_ajax_referer( md5( WPWHPRO()->settings->get_page_name() ), 'ironikus_nonce' );

        $integration_slug            = isset( $_REQUEST['integration_slug'] ) ? sanitize_text_field( $_REQUEST['integration_slug'] ) : '';
        $integration_action            = isset( $_REQUEST['integration_action'] ) ? sanitize_text_field( $_REQUEST['integration_action'] ) : '';
        $response           = array( 'success' => false );

		if( empty( $integration_slug ) || empty( $integration_action ) ){
			$response['msg'] = __( 'An error occured while doing this action.', 'wp-webhooks' );
			return $response;
		}
        
		switch( $integration_action ){
			case 'install': //runs when the "Install" link was clicked
				$response['new_class'] = 'text-danger';
				$response['new_action'] = 'uninstall';
				$response['new_label'] = __( 'Delete', 'wp-webhooks' );
				$response['success'] = $this->maybe_install_integration( $integration_slug );
				$response['msg'] = __( 'The integration was successfully installed.', 'wp-webhooks' );
				break;
			case 'uninstall': //runs when the "Uninstall" link was clicked
				$response['new_class'] = 'text-green';
				$response['new_action'] = 'install';
				$response['new_label'] = __( 'Install', 'wp-webhooks' );
				$response['success'] = $this->maybe_uninstall_integration( $integration_slug );  
				$response['msg'] = __( 'The integration was successfully deleted.', 'wp-webhooks' );
				break;
		}

        echo json_encode( $response );
		die();
    }

    /**
     * Maybe Install multiple integrations using
     * our asynchronous installation method
     *
     * @param array $integrations
     * @return bool True if installation started
     */
    public function maybe_install_integrations( $integrations = array() ){

        //todo add transient
        $installation_initiated = false;

        if( is_array( $integrations ) && ! empty( $integrations ) ){

            //prepare a new queue
            $this->get_async( 'wpwh_install_integrations' )->clear_queue();

            foreach( $integrations as $integration ){
				$this->get_async( 'wpwh_install_integrations' )->push_to_queue( array( 'integration' => $integration ) );
			}

			//dispatch
			$this->get_async( 'wpwh_install_integrations' )->save()->dispatch();
            $installation_initiated = true;
        }

        return apply_filters( 'wpwhpro/integrations/maybe_install_integrations', $installation_initiated, $integrations );
    }

    /**
     * Maybe add an integration
     *
     * @param string $integration_slug
     * @param boolean $reinstall
     * @return bool Whether the integration is installed or not
     */
    public function maybe_install_integration( $integration_slug, $reinstall = false ){
        global $wp_filesystem;

        // Initialize the WP filesystem, no more using 'file-put-contents' function
        if( empty( $wp_filesystem ) ) {
            $path = ABSPATH . '/wp-admin/includes/file.php';
            if( file_exists( $path ) ){
                require_once( $path );
            }

            if( function_exists( 'WP_Filesystem' ) ){
                WP_Filesystem();
            }
        }

        $is_installed = false;
        
        if( $this->is_integration_installed( $integration_slug ) && ! $reinstall ){
            $is_installed = true;
        } else {
            
            $integration = WPWHPRO()->api->get_integration_package( $integration_slug );
 
            if( 
                ! empty( $integration ) 
                && is_array( $integration ) 
                && $integration['success']
                && isset( $integration['stream'] )
                && ! empty( $integration['stream'] )
            ){

                $integration_folder_folder = $this->get_integrations_folder();
                $temp_folder = $this->get_wpwh_folder( 'temp' );
                $zip_destination = $temp_folder . DIRECTORY_SEPARATOR . $integration_slug . '.zip';

                if( file_exists( $zip_destination ) ){
                    unlink( $zip_destination );
                }
                
                $added = $wp_filesystem->put_contents(
                    $zip_destination,
                    $integration['stream'],
                    FS_CHMOD_FILE // predefined mode settings for WP files
                );

                if( $added && file_exists( $zip_destination ) ){

                    $unzipped = unzip_file( $zip_destination, $integration_folder_folder );

                    if( $unzipped ){
                        $is_installed = true;
                    }

                    if( file_exists( $zip_destination ) ){
                        unlink( $zip_destination );
                    }
                    
                }

            }

        }

        return apply_filters( 'wpwhpro/integrations/maybe_install_integration', $is_installed );
    }

    /**
     * Maybe uninstall an integration
     *
     * @since 6.0
     * @param string $integration_slug
     * @return bool True if it was uninstalled, false if not
     */
    public function maybe_uninstall_integration( $integration_slug ){

        $is_uninstalled = false;
        
        if( 
            ! WPWHPRO()->helpers->is_dev() 
            && $this->use_local_folder !== 'yes' 
            && $this->is_integration_installed( $integration_slug )
        ){
            $integration_folder = $this->get_integrations_folder() . DIRECTORY_SEPARATOR . $integration_slug;

            /**
             * Fired before an integration is uninstalled
             * 
             * @param $integration_slug The slug of the integration
             * @param $integration_folder The folder path of the integraiton
             */
            do_action( 'wpwhpro/integrations/maybe_uninstall_integration/before_uninstall', $integration_slug, $integration_folder );

            $cleaned_folder = WPWHPRO()->helpers->clean_folder( $integration_folder );

            /**
             * Fired after an integration was uninstalled
             * 
             * @param $integration_slug The slug of the integration
             * @param $integration_folder The folder path of the integraiton
             * @param $cleaned_folder True if the integration was deleted
             */
            do_action( 'wpwhpro/integrations/maybe_uninstall_integration/after_uninstall', $integration_slug, $integration_folder, $cleaned_folder );
            
            if( $cleaned_folder ){
                $is_uninstalled = true;
            }
        }

        return apply_filters( 'wpwhpro/integrations/maybe_uninstall_integration', $is_uninstalled );
    }

    /**
     * Maybe uninstall all or multiple integrations
     *
     * @since 6.0
     * @param string $integration_slug
     * @return bool True if all are uninstalled, false if not
     */
    public function maybe_uninstall_integrations(){

        $all_uninstalled = false;
        
        $integration_folders = $this->get_integrations_directories();    
        if( is_array( $integration_folders ) && ! empty( $integration_folders ) ){
            foreach( $integration_folders as $integration ){
                $integration = sanitize_title( $integration );
                $this->maybe_uninstall_integration( $integration ); 
            }
        }

        return apply_filters( 'wpwhpro/integrations/maybe_uninstall_integrations', $all_uninstalled, $integration_folders );
    }

    /**
     * Is integration installed
     *
     * @since 6.0
     * @param string $integration_slug
     * @return boolean True if installed, false if not
     */
    public function is_integration_installed( $integration_slug ){
        $is_installed = false;

        if( ! empty( $integration_slug ) ){
            //the slug is separated as otherwise the folder is created
            $integrations_folder = $this->get_integrations_folder() . DIRECTORY_SEPARATOR . $integration_slug;
            if( is_dir( $integrations_folder ) ){
                $is_installed = true;
            }
        }
        
        return apply_filters( 'wpwhpro/integrations/is_integration_installed', $is_installed, $integration_slug );
    }

    /**
     * Check if any integrations are installed
     *
     * @since 6.0
     * @return boolean True if integrations are installed
     */
    public function has_integrations_installed(){
        $has_installed = false;

        $integration_folders = $this->get_integrations_directories();
        if( is_array( $integration_folders ) && ! empty( $integration_folders ) ){
            $has_installed = true;
        }
        
        return apply_filters( 'wpwhpro/integrations/has_integrations_installed', $has_installed );
    }

    /**
     * Get a validated list of all available integrations
     *
     * @since 6.0
     * @return array The list of integrations
     */
    public function get_integration_list(){
        $integrations = array();

        $integrations_list = WPWHPRO()->api->get_integrations_list();
        if( ! empty( $integrations_list ) ){
            foreach( $integrations_list as $integration_key => $integration ){
                
                //Validate validations
                $can_install = true;
                if( isset( $integration['validations'] ) && ! empty( $integration['validations'] ) ){
                    $can_install = false;

                    foreach( $integration['validations'] as $validation ){

                        if( isset( $validation['type'] ) && $validation['value'] ){
                            switch( $validation['type'] ){
                                case 'class_exists':
                                    if( class_exists( esc_html( $validation['value'] ) ) ){
                                        $can_install = true;
                                    }
                                    break;
                                case 'function_exists':
                                    if( function_exists( esc_html( $validation['value'] ) ) ){
                                        $can_install = true;
                                    }
                                    break;
                                case 'defined':
                                    if( defined( esc_html( $validation['value'] ) ) ){
                                        $can_install = true;
                                    }
                                    break;
                                case 'theme_template':
                                    $theme = wp_get_theme();
                                    if( ! empty( $theme ) && $theme->get_template() === esc_html( $validation['value'] ) ){
                                        $can_install = true;
                                    }
                                    break;
                            }
                        } else {
                            break;
                        }

                    }
                }

                $integration['can_install'] = $can_install;
                $integrations[ $integration_key ] = $integration;

            }
        }
        
        return apply_filters( 'wpwhpro/integrations/get_integration_list', $integrations );
    }

    /**
	 * ######################
	 * ###
	 * #### INTEGRATION AUTOLOADER
	 * ###
	 * ######################
	 */

     /**
      * Initialize all default integrations
      *
      * @return void
      */
     public function load_integrations(){
         $integration_folder = $this->get_integrations_folder();
         $integration_folders = $this->get_integrations_directories();
         if( is_array( $integration_folders ) ){
             foreach( $integration_folders as $integration ){
                 $file_path = $integration_folder . DIRECTORY_SEPARATOR . $integration . DIRECTORY_SEPARATOR . $integration . '.php';
                 $this->register_integration( array(
                     'slug' => $integration,
                     'path' => $file_path,
                 ) );   
             }
         }

         //register authentications
         $this->register_authentications();

         //register the trigger callbacks
         $this->register_trigger_callbacks();
     }

     /**
      * Get an array contianing all of the currently given default integrations
      * The directory folder name acts as well as the integration slug.
      *
      * @return array The available default integrations
      */
    public function get_integrations_directories() {

        $integrations = array();
		
        try {
            $integrations = WPWHPRO()->helpers->get_folders( $this->get_integrations_folder() );
        } catch ( Exception $e ) {
            throw WPWHPRO()->helpers->log_issue( $e->getTraceAsString() );
        }

		return apply_filters( 'wpwhpro/integrations/get_integrations_directories', $integrations );
	}

    /**
     * Get the WP Webhooks content folder
     * If it does not exist, create it
     *
     * @return string The folder path
     */
    public function get_wpwh_folder( $sub_path = '' ){

        $sub_path = sanitize_title( $sub_path );
        $wp_upload_dir = wp_upload_dir();
        $folder_base = $wp_upload_dir['basedir'] . DIRECTORY_SEPARATOR . WPWHPRO_TEXTDOMAIN;

        /**
         * Filter the folder base of WP Webhooks
         * 
         * @since 6.0
         * @param string The folder path
         * @param string The sub path if given
         */
        $folder_base = apply_filters( 'wpwhpro/integrations/get_wpwh_folder/folder_base', $folder_base, $sub_path );
 
        if( WPWHPRO()->helpers->is_dev() || $this->use_local_folder === 'yes' ){
            $folder_base = WPWHPRO_PLUGIN_DIR . 'core' . DIRECTORY_SEPARATOR . 'includes';
        }

        if( ! is_dir( $folder_base ) ){
            wp_mkdir_p( $folder_base );
            WPWHPRO()->helpers->create_index_php( $folder_base );
        }

        if( $sub_path ){
            $folder_base .= DIRECTORY_SEPARATOR . $sub_path;

            if( ! is_dir( $folder_base ) ){
                wp_mkdir_p( $folder_base );
                WPWHPRO()->helpers->create_index_php( $folder_base );
            }
        }

        return apply_filters( 'wpwhpro/integrations/get_wpwh_folder', $folder_base, $sub_path );
    }

    /**
     * Get the integration folder
     * If it does not exist, create it
     *
     * @return string The folder path
     */
    public function get_integrations_folder( $integration = '' ){

        $integration = sanitize_title( $integration );
        $folder_base = $this->get_wpwh_folder( 'integrations' );

        if( $integration ){
            $folder_base .= DIRECTORY_SEPARATOR . $integration;

            if( ! is_dir( $folder_base ) ){
                wp_mkdir_p( $folder_base );
                WPWHPRO()->helpers->create_index_php( $folder_base );
            }
        }

        return apply_filters( 'wpwhpro/integrations/get_integrations_folder', $folder_base, $integration );
    }

    public function get_integrations_url( $integration = '' ){

        $integration = sanitize_title( $integration );
        $integrations_path = 'wpwh' . DIRECTORY_SEPARATOR . 'integrations';

        if( $integration ){
            $integrations_path .= DIRECTORY_SEPARATOR . $integration;
        }

        $integrations_url = content_url( $integrations_path . DIRECTORY_SEPARATOR );    

        return apply_filters( 'wpwhpro/integrations/get_integrations_url', $integrations_url );
    }

    /**
     * Register an integration 
     * 
     * This function can also be used to register third-party extensions. 
     * The following parameters are required: 
     * 
     * "path" => contains the integrations full path + file name + file extension
     * "slug" => contains the slug (folder name) of the integration
     * 
     * All other values are dynamically included (in case you define them.)
     *
     * @param array $integration
     * @return bool Whether the integration was added or not
     */
    public function register_integration( $integration = array() ){
        $return = false;
        $default_dependencies = WPWHPRO()->settings->get_default_integration_dependencies();
        $wp_content_dir = WPWHPRO()->helpers->get_wp_content_dir();

        if( is_array( $integration ) && isset( $integration['slug'] ) && isset( $integration['path'] ) ){
            $path = $integration['path'];
            $slug = $integration['slug'];
            $integration_basename = wp_basename( $path );

            if( file_exists( $path ) ){
                require_once $path;
                
                $directory = dirname( $path );
                $class = $this->get_integration_class( $slug );
                if( ! empty( $class ) && class_exists( $class ) && ! isset( $this->integrations[ $slug ] ) ){
                    $integration_class = new $class();
        
                    $is_active = ( ! method_exists( $integration_class, 'is_active' ) || method_exists( $integration_class, 'is_active' ) && $integration_class->is_active() ) ? true : false;
                    $is_active = apply_filters( 'wpwhpro/integrations/integration/is_active', $is_active, $slug, $class, $integration_class );

                    if( $is_active ) {
                        $this->integrations[ $slug ] = $integration_class;

                        //Since v5.2, we pre-load the details within the integration for performance and to centralize
                        $integration_details = ( method_exists( $integration_class, 'get_details' ) ) ? $integration_class->get_details() : null;
                        if( $integration_details !== null ){
                            $this->integrations[ $slug ]->details = $integration_details;

                            //automatically assign the correct integration icon path if not done manually
                            if( is_array( $this->integrations[ $slug ]->details ) && isset( $this->integrations[ $slug ]->details['icon'] ) ){

                                //prevent custom integrations from auto-applying the path
                                $url_protocol = 'http';
                                if( substr( $this->integrations[ $slug ]->details['icon'], 0, strlen( $url_protocol ) ) !== $url_protocol ){
                                    
                                    //In some environments this is necessary to adjust the path to the local separator
                                    $wp_content_dir_path_validated = str_replace( '/', DIRECTORY_SEPARATOR, $wp_content_dir );
                                    
                                    $icon_url = str_replace( $wp_content_dir_path_validated, '', $path );
                                    $icon_url = str_replace( $integration_basename, ltrim( $this->integrations[ $slug ]->details['icon'], '/' ), $icon_url );
    
                                    $this->integrations[ $slug ]->details['icon'] = content_url( $icon_url );
                                }
                                
                            }
                        }
        
                        //Register Depenencies
                        foreach( $default_dependencies as $default_dependency ){

                            //Make sure the default dependencies exists
                            if( ! property_exists( $this->integrations[ $slug ], $default_dependency ) ){
                                $this->integrations[ $slug ]->{$default_dependency} = new stdClass();
                            }

                            if( ! is_array( $this->integrations[ $slug ]->{$default_dependency} ) ){
                                $this->integrations[ $slug ]->{$default_dependency} = new stdClass();
                            }

                            $dependency_path = $directory . DIRECTORY_SEPARATOR . $default_dependency;
                            if( is_dir( $dependency_path ) ){
                                $dependencies = array();
    
                                try {
                                    $dependencies = WPWHPRO()->helpers->get_files( $dependency_path, array(
                                        'index.php'
                                    ) );
                                } catch ( Exception $e ) {
                                    throw WPWHPRO()->helpers->log_issue( $e->getTraceAsString() );
                                }
    
                                if( is_array( $dependencies ) && ! empty( $dependencies ) ){

                                    foreach( $dependencies as $dependency ){
                                        $basename = basename( $dependency );
                                        $basename_clean = basename( $dependency, ".php" );
    
                                        $ext = pathinfo( $basename, PATHINFO_EXTENSION );
                                        if ( (string) $ext !== 'php' ) {
                                            continue;
                                        }
    
                                        require_once $dependency_path . DIRECTORY_SEPARATOR . $dependency;
    
                                        $dependency_class = $this->get_integration_class( $slug, $default_dependency, $basename_clean );

                                        if( class_exists( $dependency_class ) ){
                                            $dependency_class_object = new $dependency_class();
    
                                            $is_active = ( ! method_exists( $dependency_class_object, 'is_active' ) || method_exists( $dependency_class_object, 'is_active' ) && $dependency_class_object->is_active() ) ? true : false;
                                            $is_active = apply_filters( 'wpwhpro/integrations/dependency/is_active', $is_active, $slug, $basename_clean, $dependency_class, $dependency_class_object );

                                            if( $is_active ){

                                                //Since v5.2, we pre-load the details within the integration for performance and to centralize
                                                $details = ( method_exists( $dependency_class_object, 'get_details' ) ) ? $dependency_class_object->get_details() : null;
                                                if( $details !== null && is_array( $details ) ){

                                                    //Add the integration slug to the details if not already given
                                                    if( ! isset( $details['integration'] ) || empty( $details['integration'] ) ){
                                                        $details['integration'] = $slug;
                                                    }

                                                    if( ! isset( $details['description'] ) || ! is_array( $details['description'] ) ){
                                                        $details['description'] = array();
                                                    }

                                                    $details['description'] = WPWHPRO()->webhook->validate_endpoint_description_args( $details );

                                                    $dependency_class_object->details = $details;
                                                }

                                                $this->integrations[ $slug ]->{$default_dependency}->{$basename_clean} = $dependency_class_object;
                                            }
    
                                        }
                                    }
                                }
                            }
                        }
        
                    }
        
                    $return = true;{

                    }
                }
    
            }
        }

        return $return;
    }

    /**
     * Builds the dynamic class based on the integration name and a sub file name
     *
     * @param string $integration The integration slug
     * @param string $type The type fetched from WPWHPRO()->settings->get_default_integration_dependencies()
     * @param string $sub_class A sub file name in case we add something from te default dependencies
     * @return string The integration class
     */
    public function get_integration_class( $integration, $type = '', $sub_class = '' ){
        $class = false;

        if( ! empty( $integration ) ){
            $class = 'WP_Webhooks_Integrations_' . $this->validate_class_name( $integration );
        }

        if( ! empty( $type ) && ! empty( $sub_class ) ){
            $validate_class_type = ucfirst( strtolower( $type ) );
            $class .= '_' . $validate_class_type . '_' . $this->validate_class_name( $sub_class );
        }
        
        return apply_filters( 'wpwhpro/integrations/get_integration_class', $class );
    }

    /**
     * Format the class name to make it compatible with our
     * dynamic structure
     *
     * @param string $class_name
     * @return string The class name
     */
    public function validate_class_name( $class_name ){

        $class_name = str_replace( ' ', '_', $class_name );
        $class_name = str_replace( '-', '_', $class_name );

        return apply_filters( 'wpwhpro/integrations/validate_class_name', $class_name );
    }

    /**
     * Grab the details of a given integration
     *
     * @param string $slug
     * @return array The integration details
     */
    public function get_details( $slug ){
        $return = array();

        if( ! empty( $slug ) ){
            if( isset( $this->integrations[ $slug ] ) ){
                if( isset( $this->integrations[ $slug ]->details ) ){
                    $return = $this->integrations[ $slug ]->details;
                }
            }
        }

        return apply_filters( 'wpwhpro/integrations/get_details', $return );
    }

    /**
     * Get all available integrations
     *
     * @param string $slug
     * @return array The integration details
     */
    public function get_integrations( $slug = false ){
        $return = $this->integrations;

        if( $slug !== false ){
            if( isset( $this->integrations[ $slug ] ) ){
                $return = $this->integrations[ $slug ];
            } else {
                $return = false;
            }
        }

        return apply_filters( 'wpwhpro/integrations/get_integrations', $return );
    }

    /**
     * Grab a specific helper from the given integration
     *
     * @param string $integration The integration slug (folder name)
     * @param string $helper The helper slug (file name)
     * @return object|stdClass The helper class
     */
    public function get_helper( $integration, $helper ){
        $return = new stdClass();

        if( ! empty( $integration ) && ! empty( $helper ) ){
            if( isset( $this->integrations[ $integration ] ) ){
                if( property_exists( $this->integrations[ $integration ], 'helpers' ) ){
                    if( property_exists( $this->integrations[ $integration ]->helpers, $helper ) ){
                        $return = $this->integrations[ $integration ]->helpers->{$helper};
                    }
                }
            }
        }

        return apply_filters( 'wpwhpro/integrations/get_helper', $return );
    }

    /**
     * Grab a specific authentication from the given integration
     *
     * @since 6.1.0
     * @param string $integration The integration slug (folder name)
     * @param string $helper The auth slug (file name)
     * @return object|stdClass The helper class
     */
    public function get_auth( $integration, $auth_name ){
        $return = new stdClass();

        if( ! empty( $integration ) && ! empty( $auth_name ) ){
            if( isset( $this->integrations[ $integration ] ) ){
                if( property_exists( $this->integrations[ $integration ], 'auth' ) ){
                    if( property_exists( $this->integrations[ $integration ]->auth, $auth_name ) ){
                        $return = $this->integrations[ $integration ]->auth->{$auth_name};
                    }
                }
            }
        }

        return apply_filters( 'wpwhpro/integrations/get_auth', $return );
    }

    /**
     * Get a list of all available actions
     * 
     * @since 5.0
     *
     * @param mixed $integration_slug - The slug of a single integration
     * @param mixed $integration_action - The slug of a single action
     * 
     * @return array A list of actions or a single action
     */
    public function get_actions( $integration_slug = false, $integration_action = false ){

        $actions = array();
        $wpwh_call_action_action = WPWHPRO()->settings->get_wpwh_call_action_action();

        if( ! empty( $this->integrations ) ){
            foreach( $this->integrations as $si ){
                if( property_exists( $si, 'actions' ) ){
                    foreach( $si->actions as $action_slug => $action ){

                        if( isset( $this->actions[ $action_slug ] ) ){
                            $actions[ $action_slug ] = $this->actions[ $action_slug ];
                        } else {
                            if( isset( $action->details ) ){
                                $details = $action->details;
                                if( is_array( $details ) && isset( $details['action'] ) && ! empty( $details['action'] ) ){
        
                                    //Validate parameter globally
                                    if( isset( $details['parameter'] ) && is_array( $details['parameter'] ) ){

                                        foreach( $details['parameter'] as $arg => $arg_data ){
        
                                            //Add name
                                            if( ! isset( $details['parameter'][ $arg ]['id'] ) ){
                                                $details['parameter'][ $arg ]['id'] = $arg;
                                            }
        
                                            //Add label
                                            if( ! isset( $details['parameter'][ $arg ]['label'] ) ){
                                                $details['parameter'][ $arg ]['label'] = $arg;
                                            }
        
                                            //Add type
                                            if( ! isset( $details['parameter'][ $arg ]['type'] ) ){
                                                $details['parameter'][ $arg ]['type'] = 'text';
                                            }
        
                                            //Add required
                                            if( ! isset( $details['parameter'][ $arg ]['required'] ) ){
                                                $details['parameter'][ $arg ]['required'] = false;
                                            }
        
                                            //Add variable
                                            if( ! isset( $details['parameter'][ $arg ]['variable'] ) ){
                                                $details['parameter'][ $arg ]['variable'] = true;
                                            }
        
                                            //Verify choices to the new structure
                                            if( isset( $details['parameter'][ $arg ]['choices'] ) ){
                                                foreach( $details['parameter'][ $arg ]['choices'] as $single_choice_key => $single_choice_data ){

                                                    //Make sure we always serve the same values
                                                    if( is_array( $single_choice_data ) ){

                                                        if( ! isset( $single_choice_data['value'] ) ){
                                                            $details['parameter'][ $arg ]['choices'][ $single_choice_key ]['value'] = $single_choice_key;
                                                        }

                                                        if( ! isset( $single_choice_data['label'] ) ){
                                                            $details['parameter'][ $arg ]['choices'][ $single_choice_key ]['label'] = $single_choice_key;
                                                        }

                                                    } elseif( is_string( $single_choice_data ) ){
                                                        $details['parameter'][ $arg ]['choices'][ $single_choice_key ] = array(
                                                            'label' => $single_choice_data,
                                                            'value' => $single_choice_key,
                                                        );
                                                    }

                                                }
                                            }
                                            
                                        }

                                        //Dynamically append the new action callback parameter
                                        $details['parameter']['wpwh_call_action'] = $wpwh_call_action_action;
                                    }

                                    $actions[ $details['action'] ] = $details;
                                    $this->actions[ $action_slug ] = $details;
                                }
                            }
                        }
                        
                    }
                }
            }
        }

        $actions = apply_filters( 'wpwhpro/integrations/get_actions', $actions, $integration_slug, $integration_action );

        $actions_output = $actions;

        if( $integration_slug !== false ){
            $actions_output = array();

            foreach( $actions as $action_slug => $action_data ){

                //Continue only if the integration matches
                if( 
                    ! is_array( $action_data ) 
                    || ! isset( $action_data['integration'] ) 
                    || $action_data['integration'] !== $integration_slug 
                ){
                    continue;
                }

                $actions_output[ $action_slug ] = $action_data;

            }
        }
        
        if( $integration_action !== false ){
            if( isset( $actions_output[ $integration_action ] ) ){
                $actions_output = $actions_output[ $integration_action ];
            }
        }

        return apply_filters( 'wpwhpro/integrations/get_actions/output', $actions_output, $actions, $integration_slug, $integration_action );
    }

    /**
     * Execute the acion logic
     *
     * @param array $default_return_data
     * @param string $action
     * @return array The data we return to the webhook caller
     */
    public function execute_actions( $default_return_data, $action, $request = array() ){
        $return_data = $default_return_data;

        if( ! empty( $request ) ){
            $current_request = $request;
        } else {
            $current_request = WPWHPRO()->http->get_current_request();
        }

        if( ! empty( $this->integrations ) ){
            foreach( $this->integrations as $si ){
                if( property_exists( $si, 'actions' ) ){
                    $actions = $si->actions;
                    if( is_object( $actions ) && isset( $actions->{$action} ) ){
                        if( method_exists( $actions->{$action}, 'execute' ) ){
                            $return_data = $actions->{$action}->execute( $return_data, $current_request );
                        }
                    }
                }
            }
        }

        return apply_filters( 'wpwhpro/integrations/execute_actions', $return_data, $action, $request, $default_return_data );
    }

    /**
     * Get all available triggers
     *
     * @return array Te triggers
     */
    public function get_triggers( $integration_slug = false, $integration_trigger = false ){
        $triggers = array();

        if( ! empty( $this->integrations ) ){
            foreach( $this->integrations as $si ){
                if( property_exists( $si, 'triggers' ) ){
                    foreach( $si->triggers as $trigger ){
                        if( isset( $trigger->details ) ){
                            $details = $trigger->details;
                            if( is_array( $details ) && isset( $details['trigger'] ) && ! empty( $details['trigger'] ) ){
                                $triggers[ $details['trigger'] ] = $details;
                            }
                        }
                    }
                }
            }
        }

        $triggers_output = $triggers;

        if( $integration_slug !== false ){
            $triggers_output = array();

            foreach( $triggers as $action_slug => $action_data ){

                //Continue only if the integration matches
                if( 
                    ! is_array( $action_data ) 
                    || ! isset( $action_data['integration'] ) 
                    || $action_data['integration'] !== $integration_slug 
                ){
                    continue;
                }

                $triggers_output[ $action_slug ] = $action_data;

            }
        }
        
        if( $integration_trigger !== false ){
            if( isset( $triggers_output[ $integration_trigger ] ) ){
                $triggers_output = $triggers_output[ $integration_trigger ];
            }
        }

        return apply_filters( 'wpwhpro/integrations/get_triggers', $triggers_output, $triggers, $integration_slug, $integration_trigger );
    }

    /**
     * Execute the receivable triggers
     *
     * @param array $default_return_data
     * @param string $trigger - the trigger name
     * @param string $trigger_url_name - The name of the trigger URL
     * @return array The data we return to the webhook caller
     */
    public function execute_receivable_triggers( $default_return_data, $trigger, $trigger_url_name = null ){
        $return_data = $default_return_data;
        $response_body = WPWHPRO()->http->get_current_request();

        if( ! empty( $this->integrations ) ){
            foreach( $this->integrations as $si ){
                if( property_exists( $si, 'triggers' ) ){
                    $triggers = $si->triggers;
                    if( is_object( $triggers ) && isset( $triggers->{$trigger} ) ){
                        if( method_exists( $triggers->{$trigger}, 'execute' ) ){
                            $return_data = $triggers->{$trigger}->execute( $return_data, $response_body, $trigger_url_name );
                            break; //shorten the circle
                        }
                    }
                }
            }
        }

        return apply_filters( 'wpwhpro/integrations/execute_receivable_triggers', $return_data );
    }

    /**
     * Get demo data from a given trigger
     *
     * @param string $trigger
     * @param array $options
     * @return array The demo data
     */
    public function get_trigger_demo( $trigger, $options = array() ){
        $demo_data = array(); 

        if( ! empty( $this->integrations ) ){
            foreach( $this->integrations as $si ){
                if( property_exists( $si, 'triggers' ) ){
                    $triggers = get_object_vars( $si->triggers );
                    
                    if( is_array( $triggers ) && isset( $triggers[ $trigger ] ) ){
                        if( is_object( $triggers[ $trigger ] ) && method_exists( $triggers[ $trigger ], 'get_demo' ) ){
                            $demo_data = $triggers[ $trigger ]->get_demo( $options );
                            break;
                        }
                    }
                }
            }
        }  

        return apply_filters( 'wpwhpro/integrations/get_trigger_demo', $demo_data );
    }

    /**
     * Register the callbacks for all available triggers
     *
     * @return void
     */
    private function register_trigger_callbacks(){
        $default_callback_vars = apply_filters( 'wpwhpro/integrations/default_callback_vars', array(
            'priority' => 10,
            'arguments' => 1,
            'delayed' => false,
        ) );

        if( ! empty( $this->integrations ) ){
            foreach( $this->integrations as $si ){
                if( property_exists( $si, 'triggers' ) ){
                    $triggers = get_object_vars( $si->triggers );
                    if( is_array( $triggers ) ){
                        foreach( $triggers as $trigger_name => $trigger ){
                            if( is_object( $trigger ) && method_exists( $trigger, 'get_callbacks' ) && ! empty( WPWHPRO()->webhook->get_hooks( 'trigger', $trigger_name ) ) ){
                                $callbacks = $trigger->get_callbacks();
                                if( ! empty( $callbacks ) && is_array( $callbacks ) ){
                                    foreach( $callbacks as $callback ){
                                        if( 
                                            isset( $callback['type'] ) 
                                            && isset( $callback['hook'] ) 
                                            && isset( $callback['callback'] )
                                        ){
                                            $type = $callback['type'];
                                            $hook = $callback['hook'];
                                            $hook_callback = $callback['callback'];
                                            $priority = isset( $callback['priority'] ) ? $callback['priority'] : $default_callback_vars['priority'];
                                            $arguments = isset( $callback['arguments'] ) ? $callback['arguments'] : $default_callback_vars['arguments'];
                                            $delayed = isset( $callback['delayed'] ) ? $callback['delayed'] : $default_callback_vars['delayed'];

                                            $callback_func = function() use ( $type, $hook_callback, $trigger_name, $trigger, $delayed ) {
                                                $func_args = func_get_args();
                                                $return = WPWHPRO()->delay->add_post_delayed_trigger( $hook_callback, $func_args, array(
                                                    'trigger_name' => $trigger_name,
                                                    'trigger' => $trigger,
                                                    'delay' => ( $type !== 'shortcode' ) ? $delayed : false, //don't allow shortcodes to be post-delayed
                                                ) );

                                                if( $type === 'filter' ){
                                                    $return ='';

                                                    if( is_array( $func_args ) && isset( $func_args[0] ) ){
                                                        $return = $func_args[0];
                                                    }

                                                    return $return;
                                                } elseif( $type === 'shortcode' ){
                                                    return $return;
                                                }
                                            };

                                            switch( $type ){
                                                case 'filter':
                                                    add_filter( $hook, $callback_func, $priority, $arguments );
                                                    break;
                                                case 'action':
                                                    add_action( $hook, $callback_func, $priority, $arguments );
                                                    break;
                                                case 'shortcode':
                                                    add_shortcode( $hook, $callback_func, $priority, $arguments );
                                                    break;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        do_action( 'wpwhpro/integrations/callbacks_registered' );
    }

    /**
     * Register all available authentications
     *
     * @since 6.1.0
     * @return void
     */
    private function register_authentications(){

        if( ! empty( $this->integrations ) ){
            foreach( $this->integrations as $integration_slug => $si ){
                if( property_exists( $si, 'auth' ) ){
                    $auths = get_object_vars( $si->auth );
                    if( is_array( $auths ) ){
                        foreach( $auths as $auth_type => $auth ){

                            if( is_object( $auth ) ){

                                if( isset( $auth->details ) ){
                                    $auth_type = sanitize_title( $auth_type );
                                    $auth_type = str_replace( '-', '_', $auth_type );
        
                                    WPWHPRO()->auth->register_authentication_method( $auth_type, $auth->details );
                                }

                            }

                        }
                    }
                }
            }
        }

        do_action( 'wpwhpro/integrations/authentications_registered' );
    }

}
