<?php

/**
 * @param $img (string) (required) - File source;
 */
function get_clean_svg( $img ) {
	$img_svg = file_get_contents( $img );
	$matches = array();
	preg_match( '/<svg[^>]*>[^<]*<\/svg>/m', $img_svg, $matches );

	if ( isset( $matches[0] ) ) {
		return $matches[0];
	}

	return $img_svg;
}

/**
 * Retrieve an image or an svg to represent an attachment - based on file name or WP Image ID;
 * @param $attachment (string|int) (required) - File name or WP image ID;
 * @param $thumbnail (string|array) (optional) - WP thumbnail size - usable only with image ID and NOT SVG files;
 * @param $force_img (bool) (optional) - When true, it will return img tag instead inline svg
 * @param $path (string) (optional) - Path to file;
 * @param $ext (string) (optional) - File extension;
 */
function get_image( $attachment, $thumbnail = 'full-size', $force_img = false, $path = '/svg/', $ext = '.svg' ) {
	if ( is_int( $attachment ) ) {
		$src = wp_get_attachment_image_src( $attachment, $thumbnail );

		if ( ! $src ) {
			return '';
		}

		$ext = pathinfo( $src[0], PATHINFO_EXTENSION );

		if ( $ext != 'svg' || $force_img ) {
			return wp_get_attachment_image( $attachment, $thumbnail );
		}

		$file = get_attached_file( $attachment, true );
		$img = realpath( $file );

		if ( $img ) {
            return get_clean_svg( $img );
		}
	} else {
		$filename = strpos( $attachment, $ext ) !== false ? $attachment : $attachment . $ext;
		$file = get_template_directory() . $path . $filename;
		if ( ! file_exists( $file ) ) {
			return '';
		}

		if ( $force_img ) {
			$file = get_template_directory_uri() . $path . $filename;
			return '<img src="' . $file . '" class="svg-in-img"/>';
		}

		return get_clean_svg( $file );
	}
}
