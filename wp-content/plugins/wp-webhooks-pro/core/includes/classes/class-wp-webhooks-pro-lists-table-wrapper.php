<?php

/**
 * WP_Webhooks_Pro_WP_List_Table_Wrapper Class
 *
 * This class contains a wrapper for the extended WP_List_Table class
 *
 * @since 6.1.0
 */

/**
 * The WP_List_Table wrapper class of the plugin.
 *
 * @since 6.1.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_WP_List_Table_Wrapper {

	public function new_list( $args = array() ){
        $return = null;

		$list_table_class_file = WPWHPRO_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-lists-table.php';

        if( 
			! class_exists( 'WP_Webhooks_Pro_WP_List_Table' )
		 	&& file_exists( $list_table_class_file ) 
		) {
			include( $list_table_class_file );
		}

		$return = new WP_Webhooks_Pro_WP_List_Table( $args );

        return $return;
    }

}
