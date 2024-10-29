<?php

namespace OTGS\InstallerPlugin;

use WPML\INSTALLER\FP\Maybe;
use WPML\INSTALLER\FP\Obj;
use WPML\INSTALLER\FP\Relation;
use WPML\INSTALLER\FP\Str;

class PluginDeactivator {
	public static function deactivateIfRequired() {
		return function () {
			global $wp_installer_instances;
			$allInstances = \wpml_collect( $wp_installer_instances );

			$highestVersion = $allInstances->max( 'version' );

			$amIDelegated = function ( $instance ) use ( $highestVersion ) {
				return (bool) Maybe::of( $instance )
				                   ->filter( Relation::propEq( 'version', $highestVersion ) )
				                   ->map( Obj::prop( 'bootfile' ) )
				                   ->map( Str::pos( OTGS_INSTALLER_PLUGIN_FOLDER ) )
				                   ->getOrElse( false );
			};

			$hasInstancesWithSameVersion = function ( $delegatedInstance ) use ( $allInstances ) {
				return $allInstances->reject( Relation::propEq( 'bootfile', Obj::prop( 'bootfile', $delegatedInstance ) ) )
				                    ->filter( Relation::propEq( 'version', Obj::prop( 'version', $delegatedInstance ) ) )
				                    ->count() > 0;
			};

			$delegatedInstance = $allInstances
				->first( $amIDelegated );

			$shouldDisable = ! $delegatedInstance || $hasInstancesWithSameVersion( $delegatedInstance );

			if ( $shouldDisable ) {
				deactivate_plugins( OTGS_INSTALLER_PLUGIN_BASENAME );
			}
		};
	}
}
