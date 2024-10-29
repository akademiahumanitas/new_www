<?php

/**
 * WP_Webhooks_Pro_Authentication Class
 *
 * This class contains all of the available authentication functions
 *
 * @since 3.0.0
 */

/**
 * The authentication class of the plugin.
 *
 * @since 3.0.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Authentication {

	/**
	 * Init everything
	 */
	public function __construct() {
		$this->page_name    = WPWHPRO()->settings->get_page_name();
        $this->authentication_table_data = WPWHPRO()->settings->get_authentication_table_data();
        $this->default_auth_methods = WPWHPRO()->settings->get_authentication_methods(); //Load the default authentication methods
        $this->auth_methods = array();
		$this->cache_authentication = array();
		$this->cache_authentication_count = 0;
		$this->table_exists = false;

		$this->register_default_auth_methods();
	}

	/**
	 * Wether the authentication feature is active or not
	 *
	 * Authentication will now be active by default
	 *
	 * @deprecated deprecated since version 4.0.0
	 * @return boolean - True if active, false if not
	 */
	public function is_active(){
		return true;
	}

	/**
	 * Execute feature related hooks and logic to get 
	 * everything running
	 *
	 * @since 4.2.3
	 * @return void
	 */
	public function execute(){

		//Authentication for webhook endpoints
		add_filter( 'wpwhpro/admin/webhooks/webhook_data', array( $this, 'apply_authentication_template_data' ), 100, 5 );
		add_filter( 'wpwhpro/admin/webhooks/webhook_http_args', array( $this, 'apply_authentication_template_header' ), 100, 5 );

		//Ajax related
		add_action( 'wp_ajax_ironikus_add_authentication_template',  array( $this, 'ironikus_add_authentication_template' ) );
		add_action( 'wp_ajax_ironikus_load_authentication_template_data',  array( $this, 'ironikus_load_authentication_template_data' ) );
		add_action( 'wp_ajax_ironikus_save_authentication_template',  array( $this, 'ironikus_save_authentication_template' ) );
		add_action( 'wp_ajax_ironikus_delete_authentication_template',  array( $this, 'ironikus_delete_authentication_template' ) );

	}

	/**
	 * Apply the authentication template to the given http_args
	 *
	 * @since 6.1.0
	 * @param array $http_args
	 * @param array $auth_data
	 * @param array $args
	 * @return array
	 */
	public function apply_auth( $http_args, $auth_template, $args = array() ){

		if( ! empty( $auth_template ) ){

			$auth_type = '';

			if( is_numeric( $auth_template ) ){
				$auth_template = WPWHPRO()->auth->get_template( intval( $auth_template ) );
			}

			if( is_object( $auth_template ) && isset( $auth_template->template ) && isset( $auth_template->auth_type ) ){
				
				$auth_type = $auth_template->auth_type;
				$auth_data = $auth_template->template;
			
			} elseif( is_array( $auth_template ) && isset( $auth_template['auth_type'] ) ){

				$auth_type = $auth_template['auth_type'];
				$auth_data = array();
				
				
				if( isset( $auth_template['template'] ) ){

					$auth_data = $auth_template['template'];

				} elseif( isset( $auth_template['data'] ) ){

					//Provide backwards compatibility to the data attribute
					$auth_data = $auth_template['data'];

				}
				
			}

			switch( $auth_type ){
				case 'api_key':
					$http_args = $this->validate_http_api_key( $http_args, $auth_data );
					break;
				case 'bearer_token':
					$http_args = $this->validate_http_bearer_token_header( $http_args, $auth_data );
					break;
				case 'basic_auth':
					$http_args = $this->validate_http_basic_auth_header( $http_args, $auth_data );
					break;
				case 'digest_auth':
					if( isset( $args['webhook'] ) ){
						$http_args = $this->validate_http_digest_header( $http_args, $auth_data, $args['webhook'] );
					} else {
						$http_args = $this->validate_http_digest_header( $http_args, $auth_data );
					}
					break;
			}

		}

		return apply_filters( 'wpwhpro/auth/apply_auth', $http_args, $auth_template, $args );
	}

	/**
	 * Old logic to validate authentication templates
	 * Do not use anymore from 6.1.0 onward
	 *
	 * @param array $data
	 * @param array $response
	 * @param string $url
	 * @param array $webhook
	 * @param array $authentication_data
	 * @return array
	 */
	public function apply_authentication_template_data( $data, $response, $webhook, $args, $authentication_data ){

		if( empty( $authentication_data ) ){
			return $data;
		}

		$auth_type = $authentication_data['auth_type'];
		$auth_data = $authentication_data['data'];

		switch( $auth_type ){
			case 'api_key':
				$data = $this->validate_http_api_key_body( $data, $auth_data );
			break;
		}

		return $data;
	}

	/**
	 * Old logic to validate authentication templates
	 * Do not use anymore from 6.1.0 onward
	 *
	 * @param array $http_args
	 * @param array $args
	 * @param string $url
	 * @param array $webhook
	 * @param array $authentication_data
	 * @return array
	 */
	public function apply_authentication_template_header( $http_args, $args, $url, $webhook, $authentication_data ){

		if( empty( $authentication_data ) ){
			return $http_args;
		}

		$http_args = $this->apply_auth( $http_args, $authentication_data, array( 'webhook' => $webhook ) );	
	
		return $http_args;
	}

	/*
     * Functionality to add the currently chosen data mapping
     */
	public function ironikus_add_authentication_template(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $auth_template    = isset( $_REQUEST['auth_template'] ) ? sanitize_title( $_REQUEST['auth_template'] ) : '';
        $auth_type    = isset( $_REQUEST['auth_type'] ) ? sanitize_title( $_REQUEST['auth_type'] ) : '';
		$response           = array( 'success' => false );

		if( ! empty( $auth_template ) && ! empty( $auth_type ) ){
		    $check = $this->add_template( $auth_template, $auth_type );

		    if( ! empty( $check ) ){

				$response['success'] = true;

            }
        }

        echo json_encode( $response );
		die();
	}

	/*
     * Functionality to load the currently chosen authentication
     */
	public function ironikus_load_authentication_template_data(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $auth_template_id    = isset( $_REQUEST['auth_template_id'] ) ? intval( $_REQUEST['auth_template_id'] ) : '';
        $response           = array( 'success' => false );

		if( ! empty( $auth_template_id ) && is_numeric( $auth_template_id ) ){
		    $check = $this->get_template( intval( $auth_template_id ) );

		    if( ! empty( $check ) ){

				$response['success'] = true;
		        $response['text'] 	 = array(
					'save_button_text' => __( 'Save Template', 'wp-webhooks' ),
					'delete_button_text' => __( 'Delete Template', 'wp-webhooks' ),
				);
				$response['id'] = '';
				$response['content'] = '';

				if( isset( $check->id ) && ! empty( $check->id ) ){
					$response['id'] = $check->id;
				}

				$template_data = ( isset( $check->template ) && ! empty( $check->template ) ) ? base64_decode( $check->template ) : '';

				if( isset( $check->auth_type ) && ! empty( $check->auth_type )  ){
					$response['content'] = $this->get_html_fields_form( $check->auth_type, $template_data );
				}

            }
        }

        echo json_encode( $response );
		die();
	}

	/*
     * Functionality to save the current authentication template
     */
	public function ironikus_save_authentication_template(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $data_auth_id    = isset( $_REQUEST['data_auth_id'] ) ? intval( $_REQUEST['data_auth_id'] ) : '';
        $datastring    = isset( $_REQUEST['datastring'] ) ? $_REQUEST['datastring'] : '';
		$response           = array( 'success' => false );

		parse_str( $datastring, $authentication_template );

		//Maybe validate the incoming template data
		if( empty( $authentication_template ) ){
			$authentication_template = array();
		}

		//Validate arrays
		if( is_array( $authentication_template ) ){
			$authentication_template = json_encode( $authentication_template );
		}

		if( ! empty( $data_auth_id ) && is_string( $authentication_template ) ){

			if( WPWHPRO()->helpers->is_json( $authentication_template ) ){
				$check = $this->update_template( $data_auth_id, array(
					'template' => $authentication_template
				) );

				if( ! empty( $check ) ){

					$response['success'] = true;

				}
			}
		}

        echo json_encode( $response );
		die();
	}

	/*
     * Functionality to delete the currently chosen authentication template
     */
	public function ironikus_delete_authentication_template(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $data_auth_id    = isset( $_REQUEST['data_auth_id'] ) ? intval( $_REQUEST['data_auth_id'] ) : '';
        $response           = array( 'success' => false );

		if( ! empty( $data_auth_id ) && is_numeric( $data_auth_id ) ){
		    $check = $this->delete_authentication_template( intval( $data_auth_id ) );

		    if( ! empty( $check ) ){

				$response['success'] = true;

            }
        }

        echo json_encode( $response );
		die();
	}

	/**
	 * ################################
	 * ###
	 * ##### DYNAMIC AUTHENTICATION LOGIC
	 * ###
	 * ################################
	 */

	 /**
	  * Register the default authentication method
	  * within the authentication buffer
	  *
	  * @since 6.1.0
	  * @return void
	  */
	private function register_default_auth_methods(){

		foreach( $this->default_auth_methods as $auth_method => $auth_data ){
			$this->register_authentication_method( $auth_method, $auth_data );
		}

	}

	/**
	 * Register custom authentication methods for the 
	 * authentication buffer
	 *
	 * @since 6.1.0
	 * @param string $auth_method
	 * @param array $data
	 * @return bool
	 */
	public function register_authentication_method( $auth_method, $data ){
		$success = false;

		$auth_method = sanitize_title( $auth_method );

		if( 
			! empty( $auth_method )
			&& ! isset( $this->auth_methods[ $auth_method ] )
		){
			$this->auth_methods[ $auth_method ] = $data;
			$success = true;
		}

		return $success;
	}

	/**
	 * Get all avaialble auth methods from the authentication buffer
	 *
	 * @since 6.1.0
	 * @return array
	 */
	public function get_auth_methods(){
		return apply_filters( 'wpwhpro/auth/get_auth_methods', $this->auth_methods );
	}


	/**
	 * ################################
	 * ###
	 * ##### QUERY LOGIC
	 * ###
	 * ################################
	 */


	/**
	 * Get the customized list class for the Flows
	 *
	 * @since 6.1.0
	 *
	 * @return WP_Webhooks_Pro_WP_List_Table
	 */
	public function get_auth_lists_table_class(){

		$args = array(
			'labels' => array(
				'singular' => __( 'auth template', 'wp-webhooks' ),
				'plural' => __( 'auth templates', 'wp-webhooks' ),
				'search_placeholder' => __( 'ID/Name/Auth type...', 'wp-webhooks' ),
			),
			'columns' => array(
				'id' => array(
					'label' => __( 'ID + Name', 'wp-webhooks' ),
					'callback' => array( $this, 'auth_lists_cb_id' ),
					'actions_callback' => array( $this, 'auth_lists_cb_id_actions' ),
					'sortable' => 'ASC',
				),
				'auth_type' => array(
					'label' => __( 'Auth Type', 'wp-webhooks' ),
					'callback' => array( $this, 'auth_lists_cb_type' ),
					'sortable' => 'ASC',
				),
				'date' => array(
					'label' => __( 'Date & Time', 'wp-webhooks' ),
					'callback' => array( $this, 'auth_lists_cb_date' ),
					'sortable' => 'ASC',
				),
			),
			'settings' => array(
				'per_page' => 10,
				'default_order_by' => 'id',
				'default_order' => 'DESC',
				'show_search' => true,
			),
			'item_filter' => array( $this, 'auth_lists_filter_items' ),
		);

		$table = WPWHPRO()->lists->new_list( $args );

		return $table;
	}

	/**
	 * The callback for the auth table ID
	 *
	 * @since 6.1.0
	 * @param object $item
	 * @param string $column_name
	 * @param array $column
	 * @return string
	 */
	public function auth_lists_cb_id( $item, $column_name, $column ){
		$content = '';
		$current_url = WPWHPRO()->helpers->get_current_url( false, true );
		$title = '#' . $item->id . ' - ' . $item->name;
		$edit_link = WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'wpwhpro_auth' => $item->id, ) ) );

		$content = sprintf(
			'<a class="row-title" href="%1$s" aria-label="%2$s">%3$s</a>',
			esc_url( $edit_link ),
			esc_attr( sprintf(
				__( 'Details &#8220;%s&#8221;', 'wp-webhooks' ),
				$title
			) ),
			esc_html( $title )
		);

		$content = sprintf( '<strong>%s</strong>', $content );

		return $content;
	}

	/**
	 * The callback for the title item of the auth list
	 *
	 * @since 6.1.0
	 * @param object $item
	 * @param string $column_name
	 * @param bool $primary
	 * @param array $column
	 * @return array
	 */
	public function auth_lists_cb_id_actions( $item, $column_name, $primary, $column ){

		$current_url = WPWHPRO()->helpers->get_current_url( false, true );
		$edit_url = WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'wpwhpro_auth' => $item->id, ) ) );
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
			'delete' => WPWHPRO()->helpers->create_link( 
				'', 
				$delete_title,
				array(
					'class' => 'text-error wpwh-delete-auth-template',
					'title' => $delete_title,
					'data-wpwh-auth-id' => $item->id,
				)
			),
		);

		return $actions;
	}

	/**
	 * The callback for the auth list table auth type
	 *
	 * @since 6.1.0
	 * @param object $item
	 * @param string $column_name
	 * @param array $column
	 * @return string
	 */
	public function auth_lists_cb_type( $item, $column_name, $column ){
		$content = '';

		if( isset( $item->auth_type ) ){
			$content = $item->auth_type;

			if( isset( $this->auth_methods[ $item->auth_type ] ) ){
				$content = $this->auth_methods[ $item->auth_type ]['name'];
			}
		}

		return $content;
	}

	/**
	 * The callback for the logs list table date
	 *
	 * @since 6.1.0
	 * @param object $item
	 * @param string $column_name
	 * @param array $column
	 * @return string
	 */
	public function auth_lists_cb_date( $item, $column_name, $column ){
		return WPWHPRO()->helpers->get_formatted_date( $item->log_time, 'F j, Y, g:i a' );
	}

	/**
	 * The callback with query arguments to filter the flow logs
	 * 
	 * @since 6.1.0	
	 * @param array $args
	 * @return void
	 */
	public function auth_lists_filter_items( $args ){
		$item_data = array(
			'items' => array(),
			'total' => 0,
		);

		//Make sure we force all tempaltes to be visible
		$args['auth_methods'] = 'all';

		$query_data = $this->template_query( $args );

		$item_data = array_merge( $item_data, $query_data );

		return $item_data;
	}

	/**
	 * Initialize the authentication table
	 *
	 * @return void
	 */
	public function maybe_setup_authentication_table(){

		if( ! WPWHPRO()->sql->table_exists( $this->authentication_table_data['table_name'] ) ){
			WPWHPRO()->sql->run_dbdelta( $this->authentication_table_data['sql_create_table'] );

			WPWHPRO()->sql->update_table_exists_cache( $this->authentication_table_data['table_name'], 'exists' );
		}

	}

	/**
	 * Get the data authentication template/s
	 * 
	 * @deprecated 5.2.4
	 *
	 * @param string $template
	 * @return mixed - an array of the authentication templates or an object for a specific template
	 */
	public function get_auth_templates( $template = 'all', $cached = true ){

		if( ! is_numeric( $template ) && $template !== 'all' ){
			return false;
		}

		if( ! empty( $this->cache_authentication ) && $cached ){

			if( $template !== 'all' ){
				if( isset( $this->cache_authentication[ $template ] ) ){
					return $this->cache_authentication[ $template ];
				} else {
					return false;
				}
			} else {
				return $this->cache_authentication;
			}

		}

		$this->maybe_setup_authentication_table();

		$sql = 'SELECT * FROM {prefix}' . $this->authentication_table_data['table_name'] . ' ORDER BY name ASC;';

		$data = WPWHPRO()->sql->run($sql);

		$validated_data = array();
		if( ! empty( $data ) && is_array( $data ) ){
			foreach( $data as $single ){
				if( ! empty( $single->id ) ){
					$validated_data[ $single->id ] = $single;
				}
			}
		}

		$this->cache_authentication = $validated_data;

		if( $template !== 'all' ){
			if( isset( $this->cache_authentication[ $template ] ) ){
				return $this->cache_authentication[ $template ];
			} else {
				return false;
			}
		} else {
			return $this->cache_authentication;
		}
    }

	/**
	 * Fetch a single template
	 * 
	 * @since 5.2.4
	 *
	 * @param integer $template_id
	 * @return object
	 */
	public function get_template( $template_id, $args = array() ){
		$cached = ( isset( $args['cached'] ) ) ? $args['cached'] : true;
		$template = null;
		$template_id = intval( $template_id );

		if( ! empty( $template_id ) ){

			//shorten circle on cached entries
			if( $cached && isset( $this->cache_authentication[ $template_id ] ) ){
				return $this->cache_authentication[ $template_id ];
			}

			$template_data = $this->template_query( array( 
				'items_per_page' => 1, 
				'items__in' => array( $template_id ),
				'auth_methods' => 'all'
			)  );

			if( is_array( $template_data ) && isset( $template_data['items'] ) && ! empty( $template_data['items'] ) ){
				foreach( $template_data['items'] as $s_template ){
					$template = $s_template;

					//Maybe decode template
					if( is_object( $template ) && isset( $template->template ) ){
						$template_data = base64_decode( $template->template );
						if( ! empty( $template_data ) && WPWHPRO()->helpers->is_json( $template_data ) ){
							$template->template = json_decode( $template_data, true );
						}
					}

					$this->cache_authentication[ $template_id ] = $template;

					break;
				}
			}
		}

		return $template;
	}

	/**
	 * Search and filter the authentication templates
	 * 
	 * @since 5.2.4
	 *
	 * @param string $template
	 * @return mixed - an array of the authentication templates or an object for a specific template
	 */
	public function template_query( $args = array() ){

		$this->maybe_setup_authentication_table();

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
			'auth_methods' => array_keys( $this->default_auth_methods ), //By default, we only offer support for the default methods 
		);

		$query_data = array_merge( $defaults, $args );

		//escape search attribute
		if( ! empty( $query_data['s'] ) ){
			$query_data['s'] = esc_sql( $query_data['s'] );
		}

		//validate items
		if( ! empty( $query_data['items__in'] ) ){

			if( is_array( $query_data['items__in'] ) ){
				foreach( $query_data['items__in'] as $item_key => $single_item ){
					$query_data['items__in'][ $item_key ] = intval( $single_item );
				}
			} elseif( is_numeric( $query_data['items__in'] ) ) {
				$query_data['items__in'] = array( intval( $query_data['items__in'] ) );
			} else {
				$query_data['items__in'] = array();
			}
			
		} else {
			$query_data['items__in'] = array();
		}

		//validate auth_methods
		if( ! empty( $query_data['auth_methods'] ) ){

			if( is_array( $query_data['auth_methods'] ) ){
				foreach( $query_data['auth_methods'] as $item_key => $single_item ){
					$query_data['auth_methods'][ $item_key ] = sanitize_title( $single_item );
				}
			} elseif( $query_data['auth_methods'] === 'all' ) {
				$query_data['auth_methods'] = 'all';
			} elseif( is_string( $query_data['auth_methods'] ) ) {
				$query_data['auth_methods'] = array( sanitize_title( $query_data['auth_methods'] ) );
			} else {
				$query_data['auth_methods'] = array();
			}
			
		} else {
			$query_data['auth_methods'] = array();
		}

		//maybe correct paged attribute
		$query_data['paged'] = intval( $query_data['paged'] );
		if( $query_data['paged'] < 1 || ! is_numeric( $query_data['paged'] ) ){
			$query_data['paged'] = 1;
		}

		$limit = intval( $query_data['per_page'] );
		$offset = ( $query_data['paged'] - 1 ) * $limit;
		
		switch( $query_data['orderby'] ){
			case 'date':
			case 'log_time':
				$orderby = 'log_time';
				break;
			case 'auth_type':
			case 'type':
				$orderby = 'auth_type';
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
		
		$core_sql = 'FROM {prefix}' . $this->authentication_table_data['table_name'];

		if( 
			$query_data['s'] !== '' 
			|| ! empty( $query_data['items__in'] )
			|| ( ! empty( $query_data['auth_methods'] ) && $query_data['auth_methods'] !== 'all' )
		){
			$core_sql .= ' WHERE';
		}

		if( $query_data['s'] !== '' ){
			$core_sql .= ' ( name LIKE \'%' . sanitize_title( $query_data['s'] ) . '%\' OR auth_type LIKE \'%' . str_replace( '-', '_', sanitize_title( $query_data['s'] ) ) . '%\' ) AND';
		}

		if( ! empty( $query_data['items__in'] ) ){
			$core_sql .= ' id IN ( ';

			foreach( $query_data['items__in'] as $item_id ){
				$core_sql .= $item_id . ', ';
			}

			$core_sql = trim( $core_sql, ', ' );

			$core_sql .= ' ) AND';
		}

		if( 
			! empty( $query_data['auth_methods'] )
			&& $query_data['auth_methods'] !== 'all'
		){
			$core_sql .= ' auth_type IN ( ';

			foreach( $query_data['auth_methods'] as $item_id ){
				$core_sql .= '\'' . $item_id . '\', ';
			}

			$core_sql = trim( $core_sql, ', ' );

			$core_sql .= ' ) AND';
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
	 * Generate a pagination layout for the authentication templates
	 *
	 * @param array $args
	 * @return void
	 */
	public function pagination( $args = array() ) {
	 
		$per_page = isset( $args['items_per_page'] ) ? intval( $args['items_per_page'] ) : 20;
		$page = isset( $args['paged'] ) ? intval( $args['paged'] ) : 1;
		$auth_count = isset( $args['count_total'] ) ? intval( $args['count_total'] ) : 0;

		$page_counter = 1;
		$total_pages = ceil( $auth_count / $per_page );
		$current_url = WPWHPRO()->helpers->get_current_url( false, true );

		if( $page > $total_pages ){
			$page = $total_pages;
		}

		if( $page < 1 ){
			$page = 1;
		}

		$pagination_links_out = array();

		$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'auth_page' => 1, ) ) ) . '" class="wpwh-btn--sm mr-1">' . __( '<<', 'wp-webhooks' ) . '</a>';

		if( $page <= 1 ){
			$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'auth_page' => 1, ) ) ) . '" class="wpwh-btn--sm mr-1">' . __( '<', 'wp-webhooks' ) . '</a>';
		} else {
			$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'auth_page' => ($page-1), ) ) ) . '" class="wpwh-btn--sm mr-1">' . __( '<', 'wp-webhooks' ) . '</a>';
		}
		

		if( $total_pages > 3 ){
			
			
			if( $page === 1 ){
				$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'auth_page' => 1, ) ) ) . '" class="wpwh-btn wpwh-btn--primary wpwh-btn--sm mr-1">' . 1 . '</a>';
				$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'auth_page' => 2, ) ) ) . '" class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm mr-1">' . 2 . '</a>';
				$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'auth_page' => 3, ) ) ) . '" class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm mr-1">' . 3 . '</a>';
			} elseif( $page >= $total_pages ){
				$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'auth_page' => $page-2, ) ) ) . '" class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm mr-1">' . ($page-2) . '</a>';
				$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'auth_page' => $page-1, ) ) ) . '" class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm mr-1">' . ($page-1) . '</a>';
				$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'auth_page' => $page, ) ) ) . '" class="wpwh-btn wpwh-btn--primary wpwh-btn--sm mr-1">' . $page . '</a>';
			} else {
				$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'auth_page' => $page-1, ) ) ) . '" class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm mr-1">' . ($page-1) . '</a>';
				$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'auth_page' => $page, ) ) ) . '" class="wpwh-btn wpwh-btn--primary wpwh-btn--sm mr-1">' . $page . '</a>';
				$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'auth_page' => $page+1, ) ) ) . '" class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm mr-1">' . ($page+1) . '</a>';
			}

			
			
		} else {
			$page_counter = 1;
			$total_pages_tmp = $total_pages;
			while( $total_pages_tmp > 0 ){

				if( $page_counter === $page ){
					$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'auth_page' => $page_counter, ) ) ) . '" class="wpwh-btn wpwh-btn--primary wpwh-btn--sm mr-1">' . $page_counter . '</a>';
				} else {
					$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'auth_page' => $page_counter, ) ) ) . '" class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm mr-1">' . $page_counter . '</a>';
				}
				
				$page_counter++;
				$total_pages_tmp--;
			}
		}
		
		if( $page >= $total_pages ){
			$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'auth_page' => $total_pages, ) ) ) . '" class="wpwh-btn--sm mr-1">' . __( '>', 'wp-webhooks' ) . '</a>';
		} else {
			$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'auth_page' => ($page+1), ) ) ) . '" class="wpwh-btn--sm mr-1">' . __( '>', 'wp-webhooks' ) . '</a>';
		}

		$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'auth_page' => $total_pages, ) ) ) . '" class="wpwh-btn--sm mr-1">' . __( '>>', 'wp-webhooks' ) . '</a>';

		return implode( '', $pagination_links_out );
	}

    /**
	 * Helper function to flatten authentication specific data
	 *
	 * @param mixed $data - the data value that needs to be flattened
	 * @return mixed - the flattened value
	 */
	public function flatten_authentication_data( $data ){
		$flattened = array();

		foreach( $data as $id => $sdata ){
			$flattened[ $id ] = $sdata->name;
		}

		return $flattened;
	}

	/**
	 * Delete a authentication template
	 *
	 * @param ind $id - the id of the authentication template
	 * @return bool - True if deletion was succesful, false if not
	 */
	public function delete_authentication_template( $id ){

		$this->maybe_setup_authentication_table();

		$id = intval( $id );

		if( ! $this->get_template( $id ) ){
			return false;
		}

		$sql = 'DELETE FROM {prefix}' . $this->authentication_table_data['table_name'] . ' WHERE id = ' . $id . ';';
		WPWHPRO()->sql->run($sql);

		return true;

	}

	/**
	 * Get a global count of all authentication templates
	 *
	 * @return mixed - int if count is available, false if not
	 */
	public function get_authentication_count(){

		if( ! empty( $this->cache_authentication_count ) ){
			return intval( $this->cache_authentication_count );
		}

		$this->maybe_setup_authentication_table();

		$sql = 'SELECT COUNT(*) FROM {prefix}' . $this->authentication_table_data['table_name'] . ';';
		$data = WPWHPRO()->sql->run($sql);

		if( is_array( $data ) && ! empty( $data ) ){
			$this->cache_authentication_count = $data;
			return intval( $data[0]->{"COUNT(*)"} );
		} else {
			return false;
		}

	}

	/**
	 * Add a authentication template
	 *
	 * @param string $name - the name of the authentication template
	 * @return bool - True if the creation was successful, false if not
	 */
	public function add_template( $name, $auth_type, $args = array() ){

		$this->maybe_setup_authentication_table();

		$sql_vals = array(
			'name' => sanitize_title( $name ),
			'auth_type' => sanitize_title( $auth_type ),
			'log_time' => date( 'Y-m-d H:i:s' )
		);

		if( isset( $args['id'] ) && ! empty( $args['id'] ) && is_numeric( $args['id'] ) ){
			$sql_vals['id'] = intval( $args['id'] );
		}

		if( isset( $args['template'] ) && ! empty( $args['template'] ) ){
			$sql_vals['template'] = base64_encode( $args['template'] );
		}

		if( isset( $args['log_time'] ) && ! empty( $args['log_time'] ) ){
			$sql_vals['log_time'] = date( 'Y-m-d H:i:s', strtotime( $args['log_time'] ) );
		}

		$sql_keys = '';
		$sql_values = '';
		foreach( $sql_vals as $key => $single ){

			$sql_keys .= esc_sql( $key ) . ', ';
			$sql_values .= '"' . $single . '", ';

		}

		$sql = 'INSERT INTO {prefix}' . $this->authentication_table_data['table_name'] . ' (' . trim($sql_keys, ', ') . ') VALUES (' . trim($sql_values, ', ') . ');';
		WPWHPRO()->sql->run($sql);

		return true;

	}

	/**
	 * Update an existing authentication template
	 *
	 * @param int $id - the template id
	 * @param array $data - the new template data
	 * @return bool - True if update was successful, false if not
	 */
	public function update_template( $id, $data ){

		$id = intval( $id );

		$this->maybe_setup_authentication_table();

		if( ! $this->get_template( $id ) ){
			return false;
		}

		$sql_vals = array();

		if( isset( $data['name'] ) ){
			$sql_vals['name'] = sanitize_title( $data['name'] );
		}

		if( isset( $data['template'] ) ){
			$sql_vals['template'] = base64_encode( $data['template'] );
		}

		if( empty( $sql_vals ) ){
			return false;
		}

		$sql_string = '';
		foreach( $sql_vals as $key => $single ){

			$sql_string .= $key . ' = "' . $single . '", ';

		}
		$sql_string = trim( $sql_string, ', ' );

		$sql = 'UPDATE {prefix}' . $this->authentication_table_data['table_name'] . ' SET ' . $sql_string . ' WHERE id = ' . $id . ';';
		WPWHPRO()->sql->run($sql);

		return true;

	}

	/**
	 * Delete the whole authentication table
	 *
	 * @return bool - wether the deletion was successful or not
	 */
	public function delete_table(){

		$check = true;

		if( WPWHPRO()->sql->table_exists( $this->authentication_table_data['table_name'] ) ){
			$check = WPWHPRO()->sql->run( $this->authentication_table_data['sql_drop_table'] );
		}

		WPWHPRO()->sql->update_table_exists_cache( $this->authentication_table_data['table_name'], 'purge' );

		return $check;
    }

	/**
	 * The form for an authentication template
	 *
	 * @param mixed $current_method - since 6.1.0, this argument accepts a auth ID
	 * @param mixed $template_json_deprecated Deprecated since version 6.1.0
	 * @return void
	 */
    public function get_html_fields_form( $auth_data, $template_json_deprecated = false ){

		//Prevent old data from being returned
		if( 
			! is_object( $auth_data )
			|| ! isset( $auth_data->id )
			|| ! isset( $auth_data->name )
			|| ! isset( $auth_data->auth_type )
			|| empty( $auth_data->auth_type )
			|| ! isset( $this->auth_methods[ $auth_data->auth_type ] )
		){
			return '';
		}

        $return = '';
		$authentication_nonce = WPWHPRO()->settings->get_authentication_nonce();
        $current_method = $auth_data->auth_type;
        $template_data = ( isset( $auth_data->template ) && is_array( $auth_data->template ) ) ? $auth_data->template : array();
        $template_data_original = $template_data;

		//logic to save the template
		if( isset( $_POST['wpwh-authentication-submit'] ) ){
			if ( check_admin_referer( $authentication_nonce['action'], $authentication_nonce['arg'] ) ) {
		  
			  if( WPWHPRO()->helpers->current_user_can( WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-authentication-update-template' ), 'wpwhpro-page-authentication-update-template' ) ){
				
				if( isset( $this->auth_methods[ $auth_data->auth_type ]['fields'] ) ){
					foreach( $this->auth_methods[ $auth_data->auth_type ]['fields'] as $field_name => $field_data ){
						if( isset( $_POST[ $field_name ] ) ){
							$template_data[ $field_name ] = $_POST[ $field_name ];
						}
					}
				}
				
				if( $template_data !== $template_data_original ){
				  $check = WPWHPRO()->auth->update_template( $auth_data->id, array(
					'name' => $auth_data->name,
					'template' => json_encode( $template_data ),
				  ) );
		  
				  if( $check ){
					echo WPWHPRO()->helpers->create_admin_notice( 'The auth template was successfully updated.', 'success', true );
				  } else {
					echo WPWHPRO()->helpers->create_admin_notice( 'An error occured while updating the template. Please try again.', 'warning', true );
				  }
				}
			  }
		  
			}
		}

		ob_start();
		?>
		<form id="wpwh-authentication-template-form" method="post">

			<?php if( isset( $_REQUEST['page'] ) ) : ?>
				<input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
			<?php endif; ?>

			<?php if( isset( $_REQUEST['wpwhprovrs'] ) ) : ?>
				<input type="hidden" name="wpwhprovrs" value="<?php echo esc_attr( $_REQUEST['wpwhprovrs'] ); ?>" />
			<?php endif; ?>

			<div class="wpwhpro-authentication-table mb-4">

				<?php foreach( $this->auth_methods[ $current_method ]['fields'] as $setting_name => $setting ) :

					if( ! isset( $setting['value'] ) ){
						$setting['value'] = $setting['default_value'];
					}

					//Map settings values
					if( isset( $template_data[ $setting_name ] ) ){
						$setting['value'] = $template_data[ $setting_name ];
					}

					$is_checked = ( $setting['type'] == 'checkbox' && $setting['value'] == 'yes' ) ? 'checked' : '';
					$value = ( $setting['type'] != 'checkbox' && isset( $setting['value'] ) ) ? $setting['value'] : '1';
					$placeholder = ( $setting['type'] != 'checkbox' && isset( $setting['placeholder'] ) ) ? $setting['placeholder'] : '';
					
					$attributes = '';
					if( isset( $setting['attributes'] ) ){
						foreach( $setting['attributes'] as $attr_name => $attr_value ){

							if( is_string( $attr_value ) ){
								$attributes .= $attr_name . '="' . $attr_value . '" ';
							} else {
								$attributes .= $attr_name;
							}
							
						}
					}
					$attributes = trim( $attributes, ' ' );

					?>
					<div class="wpwh-auth-item mb-3">

						<div class="wpwh-aut-details mb-1">
							<label class="wpwh-auth-label mb-1 pt-2" for="iroikus-input-id-<?php echo $setting_name; ?>">
								<strong><?php echo $setting['label']; ?></strong>
							</label>
							<div>
								<?php echo ( isset( $setting['short_description'] ) ) ? $setting['short_description'] : ''; ?>
							</div>
						</div>
						
						<?php if( in_array( $setting['type'], array( 'text' ) ) ) : ?>

							<input type="<?php echo $setting['type']; ?>" class="wpwh-form-input wpwh-w-100" id="iroikus-input-id-<?php echo $setting_name; ?>" name="<?php echo $setting_name; ?>" aria-describedby="iroikus-input-id-<?php echo $setting_name; ?>"  placeholder="<?php echo $placeholder; ?>" value="<?php echo $value; ?>" <?php echo $is_checked; ?> <?php echo $attributes; ?> />

						<?php elseif( in_array( $setting['type'], array( 'checkbox' ) ) ) : ?>
							<label class="wpwh-form-label" for="iroikus-input-id-<?php echo $setting_name; ?>">
								<strong><?php echo $setting['label']; ?></strong>
							</label>
							<div class="wpwh-toggle wpwh-toggle--on-off">
								<input class="wpwh-toggle__input" id="iroikus-input-id-<?php echo $setting_name; ?>" name="<?php echo $setting_name; ?>" type="<?php echo $setting['type']; ?>" placeholder="<?php echo $placeholder; ?>" value="<?php echo $value; ?>" <?php echo $is_checked; ?> <?php echo $attributes; ?> >
								<label class="wpwh-toggle__btn" for="iroikus-input-id-<?php echo $setting_name; ?>"></label>
							</div>
						<?php elseif( $setting['type'] === 'select' && isset( $setting['choices'] ) ) : ?>
							<select id="iroikus-select-id-<?php echo $setting_name; ?>" class="wpwh-form-input wpwh-w-100" name="<?php echo $setting_name; ?><?php echo ( isset( $setting['multiple'] ) && $setting['multiple'] ) ? '[]' : ''; ?>" <?php echo $attributes; ?> <?php echo ( isset( $setting['multiple'] ) && $setting['multiple'] ) ? 'multiple' : ''; ?>>
								<?php foreach( $setting['choices'] as $choice_name => $choice_label ) : 
									
									//Compatibility with 4.3.0
									if( is_array( $choice_label ) ){
										if( isset( $choice_label['label'] ) ){
											$choice_label = $choice_label['label'];
										} else {
											$choice_label = $choice_name;
										}
									}

									$selected = '';
									if( $choice_name === $value ){
										$selected = 'selected="selected"';
									}
								?>
								<option value="<?php echo $choice_name; ?>" <?php echo $selected; ?>><?php echo __( $choice_label, 'wp-webhooks' ); ?></option>
								<?php endforeach; ?>
							</select>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>

			<?php echo WPWHPRO()->helpers->get_nonce_field( $authentication_nonce ); ?>

			<button type="submit" name="wpwh-authentication-submit" class="wpwh-btn wpwh-btn--secondary">
				<span><?php echo __( 'Save Template', 'wp-webhooks' ); ?></span>
			</button>
		</form>
		<?php
		$return .= ob_get_clean();

        return $return;
    }

    /**
	 * ######################
	 * ###
	 * #### CORE TRIGGER AUTHENTICATONS
	 * ###
	 * ######################
	 */

    public function validate_http_api_key_body( $body, $auth_data ){

        if(
            ! isset( $auth_data['wpwhpro_auth_api_key_add_to'] )
            || ! isset( $auth_data['wpwhpro_auth_api_key_key'] )
            || ! isset( $auth_data['wpwhpro_auth_api_key_value'] )
        ){
            return $body;
        }

        if( is_array( $body ) && $auth_data['wpwhpro_auth_api_key_add_to'] === 'both' || $auth_data['wpwhpro_auth_api_key_add_to'] === 'body' ){
            $body[ $auth_data['wpwhpro_auth_api_key_key'] ] = $auth_data['wpwhpro_auth_api_key_value'];
        }

        return $body;
    }

	/**
	 * Old function to validate the header for an API key
	 * Do not use anymore
	 * 
	 * @deprecated 6.1.0
	 * @param array $http_args
	 * @param array $auth_data
	 * @return array
	 */
	public function validate_http_api_key_header( $http_args, $auth_data ){
		return $this->validate_http_api_key( $http_args, $auth_data, array( 'no_body' => true ) );
	}

	/**
	 * Validate an API key based on 
	 *
	 * @param [type] $http_args
	 * @param [type] $auth_data
	 * @return void
	 */
    public function validate_http_api_key( $http_args, $auth_data, $args = array() ){

        if(
            ! isset( $auth_data['wpwhpro_auth_api_key_add_to'] )
            || ! isset( $auth_data['wpwhpro_auth_api_key_key'] )
            || ! isset( $auth_data['wpwhpro_auth_api_key_value'] )
        ){
            return $http_args;
        }

        if( is_array( $http_args ) ){

			if( ! isset( $http_args['headers'] ) ){
                $http_args['headers'] = array();
            }

			if( $auth_data['wpwhpro_auth_api_key_add_to'] === 'both' || $auth_data['wpwhpro_auth_api_key_add_to'] === 'header' ){
				$http_args['headers'][ $auth_data['wpwhpro_auth_api_key_key'] ] = $auth_data['wpwhpro_auth_api_key_value'];
			}

			if( ! isset( $args['no_body'] ) || ! $args['no_body'] ){ //Required for backward compatibility

				if( ! isset( $http_args['body'] ) ){
					$http_args['body'] = array();
				}

				if( $auth_data['wpwhpro_auth_api_key_add_to'] === 'both' || $auth_data['wpwhpro_auth_api_key_add_to'] === 'body' ){

					if( is_array( $http_args['body'] ) ){
						$body[ $auth_data['wpwhpro_auth_api_key_key'] ] = $auth_data['wpwhpro_auth_api_key_value'];
					} elseif( is_object( $http_args['body'] ) ){
						$http_args['body']->{$auth_data['wpwhpro_auth_api_key_key']} = $auth_data['wpwhpro_auth_api_key_value'];
					} elseif( is_string( $http_args['body'] ) ){

						if( WPWHPRO()->helpers->is_json( $http_args['body'] ) ){

							$temp_body = json_decode( $http_args['body'], true );
							if( is_array( $temp_body ) ){
								$temp_body[ $auth_data['wpwhpro_auth_api_key_key'] ] = $auth_data['wpwhpro_auth_api_key_value'];

								//assign back to the body as JSON
								$http_args['body'] = json_encode( $temp_body );
							}

						}

					}

				}
			}
			
        }

        return $http_args;
    }

    public function validate_http_bearer_token_header( $http_args, $auth_data ){

        if( ! isset( $auth_data['wpwhpro_auth_bearer_token_token'] ) ){
            return $http_args;
        }

        if( is_array( $http_args ) ){

            if( ! isset( $http_args['headers'] ) ){
                $http_args['headers'] = array();
            }

			if( ! empty( $auth_data['wpwhpro_auth_bearer_token_token'] ) ){

				$scheme = 'Bearer';
				if( isset( $auth_data['wpwhpro_auth_bearer_token_scheme'] ) && ! empty( $auth_data['wpwhpro_auth_bearer_token_scheme'] ) ){
					$scheme = $auth_data['wpwhpro_auth_bearer_token_scheme'];
				}

				$http_args['headers']['Authorization'] = $scheme . ' ' . $auth_data['wpwhpro_auth_bearer_token_token'];
			}
        }

        return $http_args;
    }

    public function validate_http_basic_auth_header( $http_args, $auth_data ){

        if(
            ! isset( $auth_data['wpwhpro_auth_basic_auth_username'] )
            || ! isset( $auth_data['wpwhpro_auth_basic_auth_password'] )
        ){
            return $http_args;
        }

        if( is_array( $http_args ) ){
            if( isset( $http_args['headers'] ) ){
                $http_args['headers']['Authorization'] = 'Basic ' . base64_encode( $auth_data['wpwhpro_auth_basic_auth_username'] . ':' . $auth_data['wpwhpro_auth_basic_auth_password'] );
            }
        }

        return $http_args;
    }

    public function validate_http_digest_header( $http_args, $auth_data, $webhook = array() ){

        if(
            ! isset( $auth_data['wpwhpro_auth_digest_auth_username'] )
            || ! isset( $auth_data['wpwhpro_auth_digest_auth_password'] )
        ){
            return $http_args;
		}

		$username = ( isset( $auth_data['wpwhpro_auth_digest_auth_username'] ) && ! empty( $auth_data['wpwhpro_auth_digest_auth_username'] ) ) ? $auth_data['wpwhpro_auth_digest_auth_username'] : '';
		$password = ( isset( $auth_data['wpwhpro_auth_digest_auth_password'] ) && ! empty( $auth_data['wpwhpro_auth_digest_auth_password'] ) ) ? $auth_data['wpwhpro_auth_digest_auth_password'] : '';
		$realm = ( isset( $auth_data['wpwhpro_auth_digest_auth_realm'] ) && ! empty( $auth_data['wpwhpro_auth_digest_auth_realm'] ) ) ? $auth_data['wpwhpro_auth_digest_auth_realm'] : '';
		$nonce = ( isset( $auth_data['wpwhpro_auth_digest_auth_nonce'] ) && ! empty( $auth_data['wpwhpro_auth_digest_auth_nonce'] ) ) ? $auth_data['wpwhpro_auth_digest_auth_nonce'] : '';
		$nonce_count = ( isset( $auth_data['wpwhpro_auth_digest_auth_nonce_count'] ) && ! empty( $auth_data['wpwhpro_auth_digest_auth_nonce_count'] ) ) ? $auth_data['wpwhpro_auth_digest_auth_nonce_count'] : '';
		$client_nonce = ( isset( $auth_data['wpwhpro_auth_digest_auth_client_nonce'] ) && ! empty( $auth_data['wpwhpro_auth_digest_auth_client_nonce'] ) ) ? $auth_data['wpwhpro_auth_digest_auth_client_nonce'] : uniqid();
		$qop = ( isset( $auth_data['wpwhpro_auth_digest_auth_qop'] ) && ! empty( $auth_data['wpwhpro_auth_digest_auth_qop'] ) ) ? $auth_data['wpwhpro_auth_digest_auth_qop'] : uniqid();
		$opaque = ( isset( $auth_data['wpwhpro_auth_digest_auth_opaque'] ) && ! empty( $auth_data['wpwhpro_auth_digest_auth_opaque'] ) ) ? $auth_data['wpwhpro_auth_digest_auth_opaque'] : uniqid();
		$path = isset( $webhook['webhook_url'] ) ? parse_url( $webhook['webhook_url'], PHP_URL_PATH ) : '';

		$ha1 = md5( $username . ':' . $realm . ':' . $password );
		$ha2 = md5( 'GET:' . $path );
		// The order of this array matters, because it affects resulting hashed val
		$response_bits = array(
			$ha1,
			$nonce,
			$nonce_count,
			$client_nonce,
			$qop,
			$ha2
		);

		$digest_header_values = array(
			'username'       => '"' . $username . '"',
			'realm'          => '"' . $realm . '"',
			'nonce'          => '"' . $nonce  . '"',
			'uri'            => '"' . $path . '"',
			'response'       => '"' . md5( implode( ':', $response_bits ) ) . '"',
			'opaque'         => '"' . $opaque . '"',
			'qop'            => $qop,
			'nc'             => $nonce_count,
			'cnonce'         => '"' . $client_nonce . '"',
			);
		$digest_header = 'Digest ';
		foreach( $digest_header_values as $key => $value ) {
			$digest_header .= $key . '=' . $value . ', ';
		}
		$digest_header = rtrim( $digest_header, ', ' );

        if( is_array( $http_args ) ){
            if( isset( $http_args['headers'] ) ){
                $http_args['headers']['Authorization'] = $digest_header;
            }
        }

        return $http_args;
	}
	
	/**
	 * ######################
	 * ###
	 * #### CORE ACTION AUTHENTICATONS
	 * #### TODO - Decide what to do with the incoming action validation. Do we continue to support it?
	 * ###
	 * ######################
	 */

	 public function verify_incoming_request( $settings_data ){
		 $return = array(
			 'success' => false
		 );

		$template = $this->get_template( $settings_data );
		if( ! empty( $template ) && ! empty( $template->template ) && ! empty( $template->auth_type ) ){
			if( is_array( $template->template ) ){

				switch( $template->auth_type ){
					case 'api_key':
						$return = $this->action_validate_api_key( $template->template );
						break;
					case 'basic_auth':
						$return = $this->action_validate_basic_auth( $template->template );
						break;
				}

			}
		}

		return $return;
	}

	public function action_validate_api_key( $template_data ){
		$return = array(
			'success' => false,
			'msg' => __( 'No response message was added.', 'wp-webhooks' ),
		);
		$response_body = WPWHPRO()->http->get_current_request();
		$auth_api_key = $template_data['wpwhpro_auth_api_key_key'];
		$auth_api_val = $template_data['wpwhpro_auth_api_key_value'];
		$auth_api_pos = $template_data['wpwhpro_auth_api_key_add_to'];

		switch( $auth_api_pos ){
			case 'header':

				$header_value = WPWHPRO()->helpers->validate_server_header( $auth_api_key );
				
				if( $header_value !== NULL ){
					if( $header_value === $auth_api_val ){
						$return['success'] = true;
						$return['msg'] = __( 'Authentication was successful.', 'wp-webhooks' );
					} else {
						$return['msg'] = __( 'Authentication denied. API Key not valid.', 'wp-webhooks' );
					}
				} else {
					$return['msg'] = __( 'Authentication denied. No API Key found.', 'wp-webhooks' );
				}
				
				break;
			case 'body':

				$live_body_key = WPWHPRO()->helpers->validate_request_value( $response_body['content'], $auth_api_key );
				if( ! empty( $live_body_key ) ){
					if( $live_body_key === $auth_api_val ){
						$return['success'] = true;
						$return['msg'] = __( 'Authentication was successful.', 'wp-webhooks' );
					} else {
						$return['msg'] = __( 'Authentication denied. API Key not valid.', 'wp-webhooks' );
					}
				} else {
					$return['msg'] = __( 'Authentication denied. No API Key found.', 'wp-webhooks' );
				}

				break;
			case 'both':

				$live_body_key = WPWHPRO()->helpers->validate_request_value( $response_body['content'], $auth_api_key );
				$header_value = WPWHPRO()->helpers->validate_server_header( $auth_api_key );
				if( $header_value !== NULL && ! empty( $live_body_key ) ){
					if( $header_value === $auth_api_val && $live_body_key === $auth_api_val ){
						$return['success'] = true;
						$return['msg'] = __( 'Authentication was successful.', 'wp-webhooks' );
					} else {
						$return['msg'] = __( 'Authentication denied. API Key not valid.', 'wp-webhooks' );
					}
				} else {
					$return['msg'] = __( 'Authentication denied. No API Keys found.', 'wp-webhooks' );
				}
				
				break;
		}

		return $return;
	}

	public function action_validate_basic_auth( $template_data ){
		$return = array(
			'success' => false
		);
		
		//User validation
		if( isset( $_SERVER['PHP_AUTH_USER'] ) && ! empty( $_SERVER['PHP_AUTH_USER'] ) ){
			if( $_SERVER['PHP_AUTH_USER'] === $template_data['wpwhpro_auth_basic_auth_username'] ){

				//Password validation
				if( isset( $_SERVER['PHP_AUTH_PW'] ) && ! empty( $_SERVER['PHP_AUTH_PW'] ) ){
					if( $_SERVER['PHP_AUTH_PW'] === $template_data['wpwhpro_auth_basic_auth_password'] ){
						$return['success'] = true;
						$return['msg'] = __( 'Authentication was successful.', 'wp-webhooks' );
					} else {
						$return['msg'] = __( 'Wrong username or password.', 'wp-webhooks' );
					}
				} else {
					$return['msg'] = __( 'Authentication denied. No auth password given.', 'wp-webhooks' );
				}
				
			} else {
				$return['msg'] = __( 'Wrong username or password.', 'wp-webhooks' );
			}
		} else {
			$return['msg'] = __( 'Authentication denied. No auth user given.', 'wp-webhooks' );
		}

		return $return;
	}

}
