<?php

namespace PublishPress\Capabilities\Classes;

class PP_Capabilities_Installer
{
    /**
     * Runs methods when the plugin is running for the first time.
     *
     * @param string $currentVersion
     */
    public static function runInstallTasks($currentVersion)
    {
        self::addPluginCapabilities();

        /**
         * @param string $currentVersion
         */
        do_action('pp_capabilities_installed', $currentVersion);
    }

    /**
     * Runs methods when the plugin is being upgraded to a most recent version.
     *
     * @param string $currentVersions
     */
    public static function runUpgradeTasks($currentVersions)
    {
        if (version_compare($currentVersions, '2.8.0', '<')) {
            self::addPluginCapabilities();
        }
        if (version_compare($currentVersions, '2.9.0', '<')) {
            self::addFrontendFeaturesCapabilities();
        }

        /**
         * @param string $previousVersion
         */
        do_action('pp_capabilities_upgraded', $currentVersions);
    }

    private static function addPluginCapabilities()
    {
        $eligible_roles  = [];
        $pp_capabilities = apply_filters('cme_publishpress_capabilities_capabilities', []);

        /**
         * We're not saving installation version prior to 2.8.0.
         * So, we need another way to know if this is an upgrade or 
         * new installs to add or upgrade role capabilities.
         */
        foreach ( wp_roles()->roles as $role_name => $role ) {
            $role_object = get_role($role_name);
            if (is_object($role_object) && $role_object->has_cap('manage_capabilities')) {
                $eligible_roles[] = $role_name;
            }
        }

        /**
         * If it's a fresh installation, we're giving 'administrator' and 'editor'
         * all capabilities
         */
        if (empty($eligible_roles)) {
            $eligible_roles = ['administrator', 'editor'];
        }

        /**
         * Add capabilities to eligible roles
         */
        foreach ($eligible_roles as $eligible_role) {
            $role = get_role($eligible_role);
            foreach ($pp_capabilities as $cap) {
                if (is_object($role) && !$role->has_cap($cap)) {
                    $role->add_cap($cap);
                }
            }
        }
    }

    private static function addFrontendFeaturesCapabilities()
    {

        $eligible_roles = ['administrator', 'editor'];

        /**
         * Add frontend features capabilities to admin and editor roles
         */
        foreach ($eligible_roles as $eligible_role) {
            $role = get_role($eligible_role);
            if (is_object($role) && !$role->has_cap('manage_capabilities_frontend_features')) {
                $role->add_cap('manage_capabilities_frontend_features');
            }
        }
    }

}
