<?php

// Add function to pass variables to the theme parts
function get_theme_part( $part_name, $variables = null, $print = true ) {
	$path = dirname( __DIR__, 2 ) . '/template-parts/' . $part_name . '.php';

	if ( $variables !== null ) {
		extract( $variables );
	}
	// Start output buffering
	ob_start();

	if ( file_exists( $path ) ) {
		include $path;
	} else {
		echo '<p>There is no <code>' . $part_name . '</code> part at path: <code>' . $path . '</code></p>';
	}

	// End buffering and return its contents
	$output = ob_get_clean();

	if ( $print ) {
        print $output;
    }
    return $output;
}
