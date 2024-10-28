<?php

/**
 * WP_Webhooks_Pro_Migrate Class
 * 
 * DO NOT USE THIS FUNCTIONS ANY WHERE
 * OUTSIDE OF THE PLUGIN
 * 
 * The migration features are provided on-demand and
 * might change during development. 
 * This can cause fatal errors in certain cases.
 *
 * @since 6.0
 */

/**
 * The whitelist class of the plugin.
 *
 * @since 6.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Migrate {

	/**
	 * Execute migration-related logic
	 *
	 * @since 6.0
	 * @return void
	 */
	public function execute(){

		add_action( 'plugins_loaded', array( $this, 'wpwh_check_4_4_action_migration' ), 11 );
		add_action( 'plugins_loaded', array( $this, 'wpwh_check_5_3_integration_migration' ), 11 );

		//Maybe initiate additional migration logic on a plugin update
		add_action( 'upgrader_process_complete',  array( $this, 'execute_additional_migration' ), 20, 2 );

	}
	
	/**
	 * Migrate action endpoints to a grouped version
	 * based on the given action endpoint
	 *
	 * @return void
	 */
	public function wpwh_check_4_4_action_migration(){
		$endpoints = WPWHPRO()->webhook->get_hooks('action');
		$backup_export = null;

		if( is_array( $endpoints ) ){

			if( empty( $endpoints ) ){
				return; //nothing to do if no actions are given
			}	

			//If we made it until here, we can safely synchronize the old action URLs
			$actions = WPWHPRO()->webhook->get_actions();	
			foreach( $actions as $action_slug => $action_config ){

				if( empty( $action_slug ) ){
					continue;
				}
				
				foreach( $endpoints as $action_data_slug => $action_data ){

					if( ! is_array( $action_data ) ){
						continue;
					}

					if( ! isset( $action_data['api_key'] ) || ! is_string( $action_data['api_key'] ) ){
						/*
						 * no migration required if the first given value is not an api_key
						 * In the rare case of an action URL called api_key, we check against
						 * the type of the value as with the 5.0 notation it must be an array
						 */
						continue;
					}

					//only migrate if the action whitelist setting is set to avoid performance issues
					if( 
						! isset( $action_data['settings'] ) 
						|| ! isset( $action_data['settings']['wpwhpro_action_action_whitelist'] ) 
						|| ! is_array( $action_data['settings']['wpwhpro_action_action_whitelist'] )
						|| count( $action_data['settings']['wpwhpro_action_action_whitelist'] ) > 10
					){
						continue;
					}

					//only create if the given action was whitelisted
					if( ! in_array( $action_slug, $action_data['settings']['wpwhpro_action_action_whitelist'] ) ){
						continue;
					}


					if( $backup_export === null ){
						$backup_export = WPWHPRO()->tools->generate_plugin_export(); //store all available hooks
					}

					//add the webhook group
					$action_data['group'] = $action_slug;

					$migrated = WPWHPRO()->webhook->create( $action_data_slug, 'action', $action_data, $action_data['permission'] );
					if( $migrated ){
						WPWHPRO()->webhook->unset_hooks( $action_data_slug, 'action' );
					}
				}

			}

			//Make a backup of the existing and initial hooks if any migration has been done
			if( $backup_export !== null ){
				update_option( 'wpwh_before_migration_5_0_backup', $backup_export, false );
			}
			
			WPWHPRO()->webhook->reload_webhooks();
		}

	}

	/**
     * Maybe migrate endpoints to provide the 
	 * integration on an endpoint-base and maybe
	 * install integrations
     *
     * @return void
     */
    public function wpwh_check_5_3_integration_migration(){

        $has_integrations = WPWHPRO()->integrations->has_integrations_installed();

        //If integrations are given, no migration is needed
        if( ! empty( $has_integrations ) ){
            return;
        }

		$is_active = WPWHPRO()->license->is_active();

		if( ! $is_active ){
			return;
		}

        $hooks = WPWHPRO()->webhook->get_hooks();

        if( empty( $hooks ) || ! is_array( $hooks ) ){
			return;
		}

		//if not set
		if( ! isset( $hooks['trigger'] ) && ! isset( $hooks['action'] ) ){
			return;
		}

		//if empty
		if( 
			isset( $hooks['trigger'] ) && empty( $hooks['trigger'] )
			&& isset( $hooks['action'] ) && empty( $hooks['action'] )
		){
			return;
		}

		//inherit the main flows if a previous version was given within a multisite network
		if( is_multisite() ){
			WPWHPRO()->settings->save_settings( array( 'wpwhpro_sync_network_tables' => true ), false );
		}

		ob_start();
		include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/misc/endpoint-migration.php' );
		$endpoint_mapping_json = ob_get_clean();

		if( empty( $endpoint_mapping_json ) || ! WPWHPRO()->helpers->is_json( $endpoint_mapping_json ) ){
			return;
		}

		$migration_data = json_decode( $endpoint_mapping_json, true );
		$integrations_to_migrate = array();

		//migrate triggers
		if( isset( $hooks['trigger'] ) && is_array( $hooks['trigger'] ) && $migration_data['triggers'] ){
			foreach( $hooks['trigger'] as $trigger_group => $triggers ){
				if( is_array( $triggers ) ){
					$integration = ( isset( $migration_data['triggers'][ $trigger_group ] ) ) ? sanitize_title( $migration_data['triggers'][ $trigger_group ] ) : false;
					if( ! empty( $integration ) ){
						foreach( $triggers as $trigger_name => $trigger_data ){
							if( ! isset( $trigger_data['integration'] ) ){
								$integrations_to_migrate[ $integration ] = $integration;
								$check = WPWHPRO()->webhook->update( $trigger_name, 'trigger', $trigger_group, array( 'integration' => $integration ) );
							} else {
								$integrations_to_migrate[ $trigger_data['integration'] ] = $trigger_data['integration'];
							}
						}
					}
				}
			}
		}

		//migrate actions
		if( isset( $hooks['action'] ) && is_array( $hooks['action'] ) && $migration_data['actions'] ){
			foreach( $hooks['action'] as $action_group => $actions ){
				if( is_array( $actions ) ){
					$integration = ( isset( $migration_data['actions'][ $action_group ] ) ) ? sanitize_title( $migration_data['actions'][ $action_group ] ) : false;
					if( ! empty( $integration ) ){
						foreach( $actions as $action_name => $action_data ){
							if( ! isset( $action_data['integration'] ) ){
								$integrations_to_migrate[ $integration ] = $integration;
								$check = WPWHPRO()->webhook->update( $action_name, 'action', $action_group, array( 'integration' => $integration ) );
							} else {
								$integrations_to_migrate[ $action_data['integration'] ] = $action_data['integration'];
							}
						}
					}
				}
			}
		}

		//maybe download and integrate integrations
		if( ! empty( $integrations_to_migrate ) ){

			$installation_lock = get_transient( 'wpwh_integrations_migration_installer_lock' );

			if( empty( $installation_lock ) || isset( $_GET['wpwhpro_renew_transient'] ) ){
				set_transient( 'wpwh_integrations_migration_installer_lock', true, MINUTE_IN_SECONDS * 15 );
				WPWHPRO()->integrations->maybe_install_integrations( $integrations_to_migrate );
			}
            
		}

    }

	/**
	 * Checks wether WP Webhooks Pro was updated
	 * and fires a migration action afterward
	 *
	 * @param object $instance
	 * @param array $hook_extras
	 * @return void
	 */
	public function execute_additional_migration( $instance, $hook_extras ){

		//* Only interesting when 'our' plugin updates
        if(
			! isset( $hook_extras[ 'type' ] ) 
			|| ! isset( $hook_extras[ 'action' ] ) 
            || 'plugin' !== $hook_extras[ 'type' ] 
            || 'update' !== $hook_extras[ 'action' ]
        ) {
            return;
        }

		//If the updated plugin is not ours
		if( 
			isset( $hook_extras[ 'plugin' ] )
			&& WPWHPRO_PLUGIN_BASE !== $hook_extras[ 'plugin' ]
		 ){
			return;
		}

		if( isset( $hook_extras[ 'plugins' ] ) ){
			if( 
				! is_array( $hook_extras[ 'plugins' ] ) 
				|| ! in_array( WPWHPRO_PLUGIN_BASE, $hook_extras[ 'plugins' ] )
			){
				return;
			}
		}

		//Update the previous versions option
		$previous_versions_key = WPWHPRO()->settings->get_wpwh_previous_versions_key();
		$previous_versions = get_option( $previous_versions_key );
		$timesamp = time();

		if( ! is_array( $previous_versions ) ){
			$previous_versions = array(
				$timesamp => WPWHPRO_VERSION,
			);
		} else {
			$previous_versions[ $timesamp ] = WPWHPRO_VERSION;
		}
		
		update_option( $previous_versions_key, $previous_versions );

		/**
		 * Fires once WP Webhooks was updated
		 */
		do_action( 'wpwhpro/migrate/plugin_updated' );

	}

}
