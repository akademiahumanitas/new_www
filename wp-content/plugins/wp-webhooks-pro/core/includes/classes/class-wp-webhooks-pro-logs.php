<?php

/**
 * WP_Webhooks_Pro_Logs Class
 *
 * This class contains all of the available logging functions
 *
 * @since 1.6.3
 */

/**
 * The log class of the plugin.
 *
 * @since 1.6.3
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Logs {

	/**
	 * WP_Webhooks_Pro_Logs constructor.
	 */
	public function __construct() {

		$this->log_table_data = WPWHPRO()->settings->get_log_table_data();
		$this->cache_log = array();
		$this->cache_log_count = 0;
		$this->table_exists = false;

	}

	/**
	 * Execute feature related hooks and logic to get 
	 * everything running
	 *
	 * @since 4.3.3
	 * @return void
	 */
	public function execute(){

		//Maintenance filter
		add_action( 'wpwh_daily_maintenance', array( $this, 'maybe_clean_logs' ), 10 );

	}

	/**
	 * Get the customized list class for the Flows
	 *
	 * @since 6.1.0
	 *
	 * @return WP_Webhooks_Pro_WP_List_Table
	 */
	public function get_log_lists_table_class(){

		$args = array(
			'labels' => array(
				'singular' => __( 'log', 'wp-webhooks' ),
				'plural' => __( 'logs', 'wp-webhooks' ),
				'search_placeholder' => __( 'ID/Trigger/Name...', 'wp-webhooks' ),
			),
			'columns' => array(
				'id' => array(
					'label' => __( 'Log ID', 'wp-webhooks' ),
					'callback' => array( $this, 'logs_lists_cb_id' ),
					'actions_callback' => array( $this, 'logs_lists_cb_id_actions' ),
					'sortable' => 'ASC',
				),
				'name' => array(
					'label' => __( 'Webhook name', 'wp-webhooks' ),
					'callback' => array( $this, 'logs_lists_cb_name' ),
				),
				'endpoint' => array(
					'label' => __( 'Endpoint', 'wp-webhooks' ),
					'callback' => array( $this, 'logs_lists_cb_endpoint' ),
				),
				'type' => array(
					'label' => __( 'Type', 'wp-webhooks' ),
					'callback' => array( $this, 'logs_lists_cb_type' ),
				),
				'version' => array(
					'label' => __( 'Log Version', 'wp-webhooks' ),
					'callback' => array( $this, 'logs_lists_cb_version' ),
				),
				'date' => array(
					'label' => __( 'Date & Time', 'wp-webhooks' ),
					'callback' => array( $this, 'logs_lists_cb_date' ),
					'sortable' => 'ASC',
				),
			),
			'settings' => array(
				'per_page' => 10,
				'default_order_by' => 'id',
				'default_order' => 'DESC',
				'show_search' => false,
			),
			'item_filter' => array( $this, 'logs_lists_filter_items' ),
		);

		$table = WPWHPRO()->lists->new_list( $args );

		return $table;
	}

	/**
	 * The callback for the logs table ID
	 *
	 * @since 6.1.0
	 * @param object $item
	 * @param string $column_name
	 * @param array $column
	 * @return string
	 */
	public function logs_lists_cb_id( $item, $column_name, $column ){
		$content = '';
		$current_url = WPWHPRO()->helpers->get_current_url( false, true );
		$title = '#' . $item->id;
		$edit_link = WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'wpwhpro_log' => $item->id, ) ) );

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
	 * The callback for the title item of the logs list
	 *
	 * @since 6.1.0
	 * @param object $item
	 * @param string $column_name
	 * @param bool $primary
	 * @param array $column
	 * @return array
	 */
	public function logs_lists_cb_id_actions( $item, $column_name, $primary, $column ){

		$current_url = WPWHPRO()->helpers->get_current_url( false, true );
		$details_url = WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'wpwhpro_log' => $item->id, ) ) );
		$delete_url = WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'wpwhpro_log_delete' => $item->id, ) ) );
		$details_title = __( 'Details', 'wp-webhooks' );
		$delete_title = __( 'Delete', 'wp-webhooks' );

		$actions = array(
			'details' => WPWHPRO()->helpers->create_link( 
				$details_url, 
				$details_title,
				array(
					'title' => $details_title,
				)
			),
			'delete' => WPWHPRO()->helpers->create_link( 
				$delete_url, 
				$delete_title,
				array(
					'class' => 'text-error',
					'title' => $delete_title,
				)
			),
		);

		return $actions;
	}

	/**
	 * The callback for the logs list table name
	 *
	 * @since 6.1.0
	 * @param object $item
	 * @param string $column_name
	 * @param array $column
	 * @return string
	 */
	public function logs_lists_cb_name( $item, $column_name, $column ){
		$content = '';

		if( isset( $item->content ) ){

			$item_content = base64_decode( $item->content );

			if( $item_content ){
				if( WPWHPRO()->helpers->is_json( $item_content ) ){
					$single_data = json_decode( $item_content, true );
					if( $single_data && is_array( $single_data ) ){
	
						if( isset( $single_data['webhook_url_name'] ) ){
							$content = htmlspecialchars( $single_data['webhook_url_name'] );
						}
	
					}
				}
			}
			
		}

		return $content;
	}

	/**
	 * The callback for the logs list table endpoint
	 *
	 * @since 6.1.0
	 * @param object $item
	 * @param string $column_name
	 * @param array $column
	 * @return string
	 */
	public function logs_lists_cb_endpoint( $item, $column_name, $column ){
		$content = '';

		if( isset( $item->content ) ){

			$item_content = base64_decode( $item->content );

			if( $item_content ){
				if( WPWHPRO()->helpers->is_json( $item_content ) ){
					$single_data = json_decode( $item_content, true );
					if( $single_data && is_array( $single_data ) ){
	
						if( isset( $single_data['webhook_name'] ) ){
							$content = htmlspecialchars( $single_data['webhook_name'] );
						}
	
					}
				}
			}
			
		}

		return $content;
	}

	/**
	 * The callback for the logs list table type
	 *
	 * @since 6.1.0
	 * @param object $item
	 * @param string $column_name
	 * @param array $column
	 * @return string
	 */
	public function logs_lists_cb_type( $item, $column_name, $column ){
		$content = '';

		if( isset( $item->content ) ){

			$item_content = base64_decode( $item->content );

			if( $item_content ){
				if( WPWHPRO()->helpers->is_json( $item_content ) ){
					$single_data = json_decode( $item_content, true );
					if( $single_data && is_array( $single_data ) ){
	
						if( isset( $single_data['webhook_type'] ) ){
							$content = htmlspecialchars( $single_data['webhook_type'] );
						}
	
					}
				}
			}
			
		}

		return $content;
	}

	/**
	 * The callback for the logs list table version
	 *
	 * @since 6.1.0
	 * @param object $item
	 * @param string $column_name
	 * @param array $column
	 * @return string
	 */
	public function logs_lists_cb_version( $item, $column_name, $column ){
		$content = '';

		if( isset( $item->content ) ){

			$item_content = base64_decode( $item->content );

			if( $item_content ){
				if( WPWHPRO()->helpers->is_json( $item_content ) ){
					$single_data = json_decode( $item_content, true );
					if( $single_data && is_array( $single_data ) ){
	
						if( isset( $single_data['log_version'] ) ){
							$content = htmlspecialchars( $single_data['log_version'] );
						}
	
					}
				}
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
	public function logs_lists_cb_date( $item, $column_name, $column ){
		return WPWHPRO()->helpers->get_formatted_date( $item->log_time, 'F j, Y, g:i a' );
	}

	/**
	 * The callback with query arguments to filter the flow logs
	 * 
	 * @since 6.1.0	
	 * @param array $args
	 * @return void
	 */
	public function logs_lists_filter_items( $args ){
		$item_data = array(
			'items' => array(),
			'total' => 0,
		);

		$query_data = $this->logs_query( $args );

		$item_data = array_merge( $item_data, $query_data );

		return $item_data;
	}

	/**
	 * Maybe clean logs based on the given maintenance scheduled event
	 *
	 * @since 4.3.3
	 * @return void
	 */
	public function maybe_clean_logs(){

		$log_cleanup_interval = get_option( 'wpwhpro_autoclean_logs' );

		if( empty( $log_cleanup_interval ) || $log_cleanup_interval === 'never' ){
			return;
		}

		$interval = 30;

		switch( $log_cleanup_interval ){
			case '1day':
				$interval = 1;
				break;
			case '2days':
				$interval = 2;
				break;
			case '5days':
				$interval = 5;
				break;
			case '10days':
				$interval = 10;
				break;
			case '15days':
				$interval = 15;
				break;
			case '30days':
				$interval = 30;
				break;
			case '60days':
				$interval = 60;
				break;
			case '180days':
				$interval = 180;
				break;
			case '365days':
				$interval = 365;
				break;
		}

		$interval = apply_filters( 'wpwhpro/logs/clean_interval', $interval );
		$this->delete_log( 'daily-' . $interval );

	}

	/**
	 * Wether the log functionality is active or not
	 *
	 * @deprecated deprecated since version 4.0.0
	 * @return boolean - True if active, false if not
	 */
	public function is_active(){
		return true;
	}

	/**
	 * Init the base logging table to the database
	 *
	 * @return void
	 */
	public function maybe_setup_logs_table(){
			
		if( ! WPWHPRO()->sql->table_exists( $this->log_table_data['table_name'] ) ){
			WPWHPRO()->sql->run_dbdelta( $this->log_table_data['sql_create_table'] );

			WPWHPRO()->sql->update_table_exists_cache( $this->log_table_data['table_name'], 'exists' );
		}
		
	}

	/**
	 * Returns certain items of the logs table
	 *
	 * @param integer $offset
	 * @param integer $limit
	 * @return array - An array of the given log data
	 */
	public function get_log( $offset = 0, $limit = 10 ){

		if( ! empty( $this->cache_log ) ){
			return $this->cache_log;
		}

		$this->maybe_setup_logs_table();

		$sql = 'SELECT * FROM {prefix}' . $this->log_table_data['table_name'] . ' ORDER BY id DESC LIMIT ' . intval( $limit ) . ' OFFSET ' . intval( $offset ) . ';';
		$data = WPWHPRO()->sql->run($sql);
		$this->cache_log = $data;

		return $data;
	}

	/**
	 * Search and filter the logs
	 * 
	 * @since 6.1.0
	 *
	 * @param string $args
	 * @return mixed - an array of the flows + extra data
	 */
	public function logs_query( $args = array() ){

		$this->maybe_setup_logs_table();

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
		
		$core_sql = 'FROM {prefix}' . $this->log_table_data['table_name'];

		if( $query_data['s'] !== '' || ! empty( $query_data['items__in'] ) ){
			$core_sql .= ' WHERE';
		}

		//Not optimized for searches
		// if( $query_data['s'] !== '' ){
		// 	$core_sql .= ' ( flow_title LIKE \'%' . esc_sql( $query_data['s'] ) . '%\' OR flow_trigger LIKE \'%' . esc_sql( sanitize_title( $query_data['s'] ) ) . '%\' OR flow_name LIKE \'%' . esc_sql( sanitize_title( $query_data['s'] ) ) . '%\' )  AND';
		// }

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
	 * Count the given log data
	 *
	 * @param integer $offset
	 * @param integer $limit
	 * @return mixed - Integer if log data found, false if not
	 */
	public function get_log_count( $offset = 0, $limit = 10 ){

		if( ! empty( $this->cache_log_count ) ){
			return intval( $this->cache_log_count );
		}

		$this->maybe_setup_logs_table();

		$sql = 'SELECT COUNT(*) FROM {prefix}' . $this->log_table_data['table_name'] . ';';
		$data = WPWHPRO()->sql->run($sql);

		if( is_array( $data ) && ! empty( $data ) ){
			$this->cache_log_count = $data[0]->{"COUNT(*)"};
			return intval( $data[0]->{"COUNT(*)"} );
		} else {
			return false;
		}

	}


	/**
	 * Add a log data item to the logs
	 *
	 * @param string $msg
	 * @param mixed $data can be everything that should be saved as log data
	 * @return bool - True if the function runs successfully
	 */
	public function add_log( $msg, $data ){

		$this->maybe_setup_logs_table();

		$sql_vals = array(
			'message' => base64_encode( $msg ),
			'content' => ( is_array( $data ) || is_object( $data ) ) ? base64_encode( json_encode( $data ) ) : base64_encode( $data ),
			'log_time' => date( 'Y-m-d H:i:s' )
		);

		// START UPDATE PRODUCT
		$sql_keys = '';
		$sql_values = '';
		foreach( $sql_vals as $key => $single ){

			$sql_keys .= esc_sql( $key ) . ', ';
			$sql_values .= '"' . $single . '", ';

		}

		$sql = 'INSERT INTO {prefix}' . $this->log_table_data['table_name'] . ' (' . trim($sql_keys, ', ') . ') VALUES (' . trim($sql_values, ', ') . ');';
		$id = WPWHPRO()->sql->run( $sql, OBJECT, array( 'return_id' => true ) );

		do_action( 'wpwhpro/logs/add_log', $id, $msg, $data );

		return $id;

	}

	/**
	 * Add a log data item to the logs
	 *
	 * @param string $msg
	 * @param mixed $data can be everything that should be saved as log data
	 * @return bool - True if the function runs successfully
	 */
	public function delete_log( $log = 'all' ){

		$this->maybe_setup_logs_table();

		$check = false;

		if( $log === 'all' ){
			$check = $this->delete_table();
		} else {

			$sql = '';

			$ident = 'daily-';
			if( strlen( $log ) > strlen( $ident ) && substr( $log, 0, strlen( $ident ) ) === $ident ){
				$interval = str_replace( $ident, '', $log );
				if( ! empty( $interval ) && is_numeric( $interval ) ){

					$interval = intval( $interval );

					$sql = "
						DELETE FROM {prefix}" . $this->log_table_data['table_name'] . " 
						WHERE log_time < DATE_SUB(NOW(), INTERVAL %d DAY);
					";
					$sql = WPWHPRO()->sql->prepare( $sql, array( $interval ) );
				}
			} else {
				$log = intval( $log );
				$sql = 'DELETE FROM {prefix}' . $this->log_table_data['table_name'] . ' WHERE id = "' . $log . '";';
			}
			
			if( ! empty( $sql ) ){
				$check = WPWHPRO()->sql->run($sql);
			}
			
		}

		return $check;

	}

	/**
	 * Delete the log data table
	 *
	 * @return bool - True if the log table was deleted successfully
	 */
	public function delete_table(){

		$check = true;

		if( WPWHPRO()->sql->table_exists( $this->log_table_data['table_name'] ) ){
			$check = WPWHPRO()->sql->run( $this->log_table_data['sql_drop_table'] );
		}

		WPWHPRO()->sql->update_table_exists_cache( $this->log_table_data['table_name'], 'purge' );

		return $check;
	}

	/**
	 * Sanitize the values of a given content array to prevent the log oview from breaking
	 */
	public function sanitize_array_object_values( $array ){

		if( is_array( $array ) ){
			foreach( $array as $key => $val ){
				if( is_string( $val ) ){
					$array[ $key ] = htmlspecialchars( str_replace( '"', '&quot', $val ) );
				} else {
					$array[ $key ] = $this->sanitize_array_object_values( $val );
				}
			}
		} elseif( is_object( $array ) ){
			foreach( $array as $key => $val ){
				if( is_string( $val ) ){
					$array->{$key} = htmlspecialchars( str_replace( '"', '&quot', $val ) );
				} else {
					$array->{$key} = $this->sanitize_array_object_values( $val );
				}
			}
		}

		return $array;

	}

	public function pagination( $args = array() ) {
	 
		$per_page = isset( $args['per_page'] ) ? intval( $args['per_page'] ) : 10;
		$page = isset( $args['log_page'] ) ? intval( $args['log_page'] ) : 1;

		$page_counter = 1;
		$log_count = $this->get_log_count();
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

}
