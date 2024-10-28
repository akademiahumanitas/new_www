<?php

namespace WPML\INSTALLER\FP;

trait Applicative {

	/**
	 * @param mixed $otherContainer
	 *
	 * @return mixed
	 */
	public function ap( $otherContainer ) {
		return $otherContainer->map( $this->value );
	}
}
