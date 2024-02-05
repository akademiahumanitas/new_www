<?php

namespace WPML\INSTALLER\FP\Strings;

use function WPML\INSTALLER\FP\partial;
use function WPML\INSTALLER\FP\partialRight;
use function WPML\INSTALLER\FP\pipe;

/**
 * ltrimWith :: string -> ( string -> string )
 *
 * @param string $trim
 *
 * @return callable
 */
function ltrimWith( $trim ) {
	return partialRight( 'ltrim', $trim );
}

/**
 * rtrimWith :: string -> ( string -> string )
 *
 * @param string $trim
 *
 * @return callable
 */
function rtrimWith( $trim ) {
	return partialRight( 'rtrim', $trim );
}

/**
 * explodeToCollection :: string -> ( string -> Collection )
 *
 * @param string $delimiter
 *
 * @return callable
 */
function explodeToCollection( $delimiter ) {
	return pipe( partial( 'explode', $delimiter ), 'wpml_collect' );
}

/**
 * replace :: string -> string -> ( string -> string )
 *
 * @param string $search
 * @param string $replace
 *
 * @return callable
 */
function replace( $search, $replace ) {
	return partial( 'str_replace', $search, $replace );
}

/**
 * remove :: string -> ( string -> string )
 *
 * @param string $remove
 *
 * @return callable
 */
function remove( $remove ) {
	return partial( 'str_replace', $remove, '' );
}

