<?php
/**
 * Plugin Name: OTGS Installer
 * Plugin URI: https://wpml.org/
 * Description: Lightweight Installer plugin that allows to install OTGS plugins
 * Author: OnTheGoSystems
 * Author URI: http://www.onthegosystems.com/
 * Version: 3.1.1
 * Plugin Slug: otgs-installer
 *
 * @package WPML\Core
 */

use OTGS\InstallerPlugin\OtgsInstallerPlugin;

define( 'OTGS_INSTALLER_VERSION', '3.1.1' );

define( 'OTGS_INSTALLER_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'OTGS_INSTALLER_PLUGIN_FOLDER', dirname( OTGS_INSTALLER_PLUGIN_BASENAME ) );

define( 'OTGS_INSTALLER_PLUGIN_PATH', __DIR__ );
define( 'OTGS_INSTALLER_PLUGINS_DIR', realpath( __DIR__ . '/..' ) );
define( 'OTGS_INSTALLER_PLUGIN_FILE', basename( OTGS_INSTALLER_PLUGIN_BASENAME ) );

require_once __DIR__ . '/vendor/autoload.php';

include 'vendor/otgs/installer/loader.php';

WP_Installer_Setup(
	$wp_installer_instance,
	['plugins_install_tab'   => true]
);

$p = new OtgsInstallerPlugin();
$p->addHooks();
