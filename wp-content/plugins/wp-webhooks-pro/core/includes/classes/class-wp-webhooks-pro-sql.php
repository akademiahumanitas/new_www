<?php

/**
 * WP_Webhooks_Pro_SQL Class
 *
 * This class contains all of the available SQL functions
 *
 * @since 1.6.3
 */

/**
 * The SQL class of the plugin.
 *
 * @since 1.6.3
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_SQL{

	/**
	 * Whether the custom tables should be inherited from 
	 * the main network site or not
	 *
	 * @var mixed
	 */
	private $inherited_tables = null;

	/**
	 * Cache requests for checks of an existing table
	 *
	 * @var array
	 */
	private $table_exists_cache = array();

	/**
	 * Run certain queries using dbbdelta
	 *
	 * @param string $sql
	 * @return bool - true for success
	 */
	public function run_dbdelta($sql){
		global $wpdb;

		$sql = $this->replace_tags($sql);

		if(empty($sql))
			return false;

		if(!function_exists('dbDelta'))
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		dbDelta($sql);
		$success = empty($wpdb->last_error);

		return $success;
	}

	/**
	 * Run certain SQL Queries
	 *
	 * @param string $sql
	 * @param string $type
	 * @return void
	 */
	public function run( $sql, $type = OBJECT, $args = array() ){
		global $wpdb;

		$sql = $this->replace_tags($sql);

		if(empty($sql))
			return false;

		$result = $wpdb->get_results($sql, $type);

		if( isset( $args['return_id'] ) && $args['return_id'] ){
			if( isset( $wpdb->insert_id ) ){
				$result = $wpdb->insert_id;
			}
		}

		return $result;
	}

	/**
	 * Prepare certain SQL Queries
	 *
	 * @param string $sql
	 * @param string $type
	 * @since 4.3.3
	 * @return void
	 */
	public function prepare( $sql, $values = array() ){
		global $wpdb;

		$sql = $this->replace_tags($sql);

		if( empty( $sql ) ){
			return false;
		}

		$sql = $wpdb->query( $wpdb->prepare( $sql, $values ) );

		return $sql;
	}

	/**
	 * Get all available tags including descriptions and values
	 * 
	 * @since 6.1.0
	 *
	 * @return array The tags
	 */
	public function get_tags(){
		global $wpdb;

		$tags = array(
			'charset_collate' => array(
				'label' => __( 'Charset collate', 'wp-webhooks' ),
				'value' => $wpdb->get_charset_collate(),
				'short_description' => __( 'The database character set and collation.', 'wp-webhooks' ),
			),
			'base_prefix' => array(
				'label' => __( 'Base prefix', 'wp-webhooks' ),
				'value' => $wpdb->base_prefix,
				'short_description' => __( 'The base prefix. If you are using a multisite network, this one will assure to get the prefix without the blog id in it.', 'wp-webhooks' ),
			),
			'prefix' => array(
				'label' => __( 'Prefix', 'wp-webhooks' ),
				'value' => $this->get_db_prefix(),
				'short_description' => __( 'The prefix. Within multisite, this prefix contains the number. E.g.: <strong>wp_2_</strong>', 'wp-webhooks' ),
			),
			'posts' => array(
				'label' => __( 'Posts', 'wp-webhooks' ),
				'value' => $wpdb->posts,
				'short_description' => __( 'The posts table name including the prefix.', 'wp-webhooks' ),
			),
			'postmeta' => array(
				'label' => __( 'Postmeta', 'wp-webhooks' ),
				'value' => $wpdb->postmeta,
				'short_description' => __( 'The postmeta table name including the prefix.', 'wp-webhooks' ),
			),
		);

		return apply_filters( 'wpwhpro/sql/get_tags', $tags );
	}

	/**
	 * Replace generic tags with values
	 *
	 * @param $string - string to fill
	 * @return mixed - filles string
	 */
	public function replace_tags($string){

		if(!is_string($string) || empty($string))
			return false;

		$tags = $this->get_tags();
		$in = array();
		$out = array();

		foreach( $tags as $tag => $tag_data ){
			$in[] = '{' . $tag . '}';
			$out[] = $tag_data['value'];
		}

		/**
		 * Pre filter a string based on given tags
		 * 
		 * @since 6.0
		 */
		$string = apply_filters( 'wpwhpro/sql/replace_tags/pre_filter_string', $string, $in, $out );

		return str_replace($in, $out, $string);

	}

	/**
	 * Checks if a table exists or not
	 *
	 * @param $table_name - the table name
	 * @return bool - true if the table exists
	 */
	public function table_exists( $table_name ){
		global $wpdb;

		$return = false;
		$prefix = $this->get_db_prefix();
		$table_name = esc_sql($table_name);

		if(substr($table_name, 0, strlen($prefix)) != $prefix){
			$table_name = $prefix . $table_name;
		}

		if( isset( $this->table_exists_cache[ $table_name ] ) ){
			return $this->table_exists_cache[ $table_name ];
		}

		$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name );

		if( $wpdb->get_var( $query ) == $table_name ){
			$return = true;
		}

		$this->table_exists_cache[ $table_name ] = $return;

		return $return;
	}

	/**
	 * Purge table cache for one or all tables
	 * 
	 * Set it to 'purge' to clear the full cache
	 *
	 * @since 6.0
	 * @param mices $table_name
	 * @return void
	 */
	public function update_table_exists_cache( $table_name = null, $action = '' ){
		global $wpdb;
		
		if( $action === 'purge' && $table_name === null ){
			$this->table_exists_cache = array();
		} elseif ( $table_name !== null ){

			$prefix = $this->get_db_prefix();
			$table_name = esc_sql($table_name);

			if(substr($table_name, 0, strlen($prefix)) != $prefix){
				$table_name = $prefix . $table_name;
			}

			switch( $action ){
				case 'purge':
					if( isset( $this->table_exists_cache[ $table_name ] ) ){
						unset( $this->table_exists_cache[ $table_name ] );
					}
					break;
				case 'exists':
					$this->table_exists_cache[ $table_name ] = true;
					break;
				case 'notexists':
					$this->table_exists_cache[ $table_name ] = true;
					break;
			}
			
		} 

	}

	/**
	 * Checks if one or multiple column exists or not
	 *
	 * @param $table_name - the table name
	 * @param $column_name - the column name
	 * @return bool - true if the column exists
	 */
	public function column_exists( $table_name, $column_name ){
		global $wpdb;

		$return = false;
		$prefix = $this->get_db_prefix();
		$table_name = esc_sql($table_name);
		$column_name = esc_sql($column_name);

		if(substr($table_name, 0, strlen($prefix)) != $prefix){
			$table_name = $prefix . $table_name;
		}

		$query = $wpdb->prepare( 'SHOW COLUMNS FROM %1$s LIKE \'%2$s\';', $table_name, $column_name );

		if( $wpdb->get_var( $query ) == $column_name ){
			$return = true;
		}

		return $return;
	}

	public function get_db_prefix(){
		global $wpdb;

		$base_prefix = $wpdb->base_prefix;
		$prefix = $wpdb->prefix;

		//Provide feature-compatibility to centralize custom tables
		if( $this->inherited_tables !== null ){
			$centralize_tables = $this->inherited_tables;
		} else {
			$option = get_option( 'wpwhpro_sync_network_tables' );
			if( ! empty( $option ) && $option !== 'no' ){
				$centralize_tables = true;
			} else {
				$centralize_tables = false;
			}

			$this->inherited_tables = $centralize_tables;
		}

		if( $centralize_tables ){
			$prefix = $base_prefix;
		}

		return $prefix;
	}

}