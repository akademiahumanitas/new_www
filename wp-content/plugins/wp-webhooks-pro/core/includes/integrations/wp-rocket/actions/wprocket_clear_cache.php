<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_rocket_Actions_wprocket_clear_cache' ) ) :

	/**
	 * Load the wprocket_clear_cache action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_rocket_Actions_wprocket_clear_cache {

		public function get_details() {


			$parameter = array(
				'language' => array(
					'label'             => __( 'Language code', 'wp-webhooks' ),
					'short_description' => __( 'The language code of a given language. Leave it empty to clear all languages.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'    => array( 'short_description' => __( '(String) An informative message about the fired action.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'The cache has been cleared.',
			);

			return array(
				'action'            => 'wprocket_clear_cache', // required
				'name'              => __( 'Clear cache', 'wp-webhooks' ),
				'sentence'          => __( 'clear the full cache', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Clear the full cache within WP Rocket.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'wp-rocket',
				'premium'           => true,
			);

		}

		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'     => '',
			);

			$lang = sanitize_key( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'language' ) );

			if ( empty( $lang ) ) {
				$lang = '';
			}

			rocket_clean_domain( $lang );

			if ( '' === $lang ) {
				// Remove all minify cache files.
				rocket_clean_minify();
				rocket_clean_cache_busting();

				// Generate a new random key for minify cache file.
				$options                   = get_option( WP_ROCKET_SLUG );
				$options['minify_css_key'] = create_rocket_uniqid();
				$options['minify_js_key']  = create_rocket_uniqid();
				remove_all_filters( 'update_option_' . WP_ROCKET_SLUG );
				update_option( WP_ROCKET_SLUG, $options );
			}

			if ( get_rocket_option( 'manual_preload' ) && ( ! defined( 'WP_ROCKET_DEBUG' ) || ! WP_ROCKET_DEBUG ) ) {
				$home_url = get_rocket_i18n_home_url( $lang );

				/**
				 * Filters the arguments for the preload request being triggered after clearing the cache.
				 *
				 * @since  3.4 (WP Rocket)
				 *
				 * @param array $args Request arguments.
				 */
				$args = (array) apply_filters(
					'rocket_preload_after_purge_cache_request_args',
					array(
						'blocking'   => false,
						'timeout'    => 0.01,
						'user-agent' => 'WP Rocket/Homepage_Preload_After_Purge_Cache',
						'sslverify'  => apply_filters( 'https_local_ssl_verify', false ), // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
					)
				);

				wp_safe_remote_get( $home_url, $args );

				/**
				 * Fires after automatically preloading the homepage, which occurs after purging the cache.
				 *
				 * @since  3.5 (WP Rocket)
				 *
				 * @param string $home_url URL to the homepage being preloaded.
				 * @param string $lang     The lang of the homepage.
				 * @param array  $args     Arguments used for the preload request.
				 */
				do_action( 'rocket_after_preload_after_purge_cache', $home_url, $lang, $args );
			}

			$return_args['success'] = true;
			$return_args['msg']     = __( 'The cache has been cleared.', 'wp-webhooks' );

			return $return_args;

		}

	}

endif; // End if class_exists check.
