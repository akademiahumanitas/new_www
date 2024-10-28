<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_advanced_coupons_Helpers_ac_helpers' ) ) :

	/**
	 * Load the Advanced Coupons helpers
	 *
	 * @since 5.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_advanced_coupons_Helpers_ac_helpers {

		public function get_total_credits_user( $user_id ) {

			global $wpdb;

			$amount_query = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT SUM(entry_amount + 0) AS amount
                    FROM {$wpdb->prefix}acfw_store_credits
                    WHERE user_id = %d",
					$user_id
				),
                ARRAY_A
			);

			$total_amount = floatval( $amount_query[0]['amount'] );

			return apply_filters( 'ac_total_amount', $total_amount );
		}

	}

endif; // End if class_exists check.
