<?php

use Hummingbird\WP_Hummingbird;

if ( ! class_exists( 'WP_Webhooks_Integrations_hummingbird_Helpers_hb_helpers' ) ) :

	/**
	 * Load the Hummingbird helpers
	 *
	 * @since 5.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_hummingbird_Helpers_hb_helpers {

        public function clear_cache( $remove_data = false, $remove_settings = false ){
			WP_Hummingbird::flush_cache( $remove_data, $remove_settings );
		}

	}

endif; // End if class_exists check.