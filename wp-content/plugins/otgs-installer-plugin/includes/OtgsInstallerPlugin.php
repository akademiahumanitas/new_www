<?php

namespace OTGS\InstallerPlugin;

use function WPML\INSTALLER\FP\spreadArgs;

class OtgsInstallerPlugin {
	const REDIRECT_AFTER_ACTIVATION_OPTION = 'OTGS_INSTALER_PLUGIN_REDIRECT_AFTER_ACTIVATION';

	public static function addHooks() {
		self::addInstallerMenuItem();
		self::addRedirectAfterActivation();
		self::addDeactivationWhenInstallerInstancesDetected();
	}

	private static function addInstallerMenuItem() {

		add_action( 'admin_menu', function () {
			add_menu_page(
				__( 'OTGS Installer', 'installer' ),
				__( 'OTGS Installer', 'installer' ),
				'manage_options',
				'plugin-install.php?tab=commercial',
				null
			);
		} );
	}

	private static function addDeactivationWhenInstallerInstancesDetected() {
		add_action( 'activated_plugin',
			function () {
				add_action( 'shutdown', PluginDeactivator::deactivateIfRequired() );
			} );
	}


	private static function addRedirectAfterActivation() {
		$addRedirection = function ( $plugin ) {
			if ( $plugin == OTGS_INSTALLER_PLUGIN_BASENAME ) {
				add_option( self::REDIRECT_AFTER_ACTIVATION_OPTION, true );
			}
		};

		$redirectToCommercialTab = function () {
			if ( get_option( self::REDIRECT_AFTER_ACTIVATION_OPTION ) ) {
				delete_option( self::REDIRECT_AFTER_ACTIVATION_OPTION );
				wp_safe_redirect( network_admin_url( 'plugin-install.php?tab=commercial' ) );
			}
		};

		add_action( 'activated_plugin', $addRedirection );
		add_action( 'admin_init', $redirectToCommercialTab );
	}
}
