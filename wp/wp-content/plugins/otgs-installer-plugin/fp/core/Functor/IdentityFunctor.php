<?php

namespace WPML\INSTALLER\FP\Functor;

class IdentityFunctor {
	use Functor;
	use Pointed;

	/**
	 * @param callable $callback
	 *
	 * @return IdentityFunctor
	 */
	public function map( $callback ) {
		return new self( $callback( $this->get() ) );
	}
}
