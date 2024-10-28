<?php

namespace WPML\INSTALLER\FP\System;

use WPML\INSTALLER\FP\Either;

class System {

	/**
	 * @return \Closure
	 */
	public static function getPostData() {
		return function () {
			return Either::right( wpml_collect( $_POST ) );
		};
	}
}
