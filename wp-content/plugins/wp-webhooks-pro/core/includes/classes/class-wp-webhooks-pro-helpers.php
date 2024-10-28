<?php

/**
 * WP_Webhooks_Pro_Helpers Class
 *
 * This class contains all of the available helper functions
 *
 * @since 1.0.0
 */

/**
 * The helpers of the plugin.
 *
 * @since 1.0.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Helpers {

	/**
     * The current requests response body
     *
	 * @var mixed - the current response body
	 */
    private $response_body = false;

	/**
	 * WP_Webhooks_Pro_Helpers constructor.
	 */
    public function __construct() {
		$this->country_list = array();
        $this->activate_debugging = ( get_option( 'wpwhpro_activate_debug_mode' ) == 'yes' ) ? true : false;
    }

	/**
	 * Translate custom Strings
	 * This version is deprecated. After many suggestions, 
	 * we reverse back to bring back the automatic translations
	 * 
	 * @deprecated 6.0.1 
	 *
	 * @param $string - The language string
	 * @param null $cname - If no custom name is set, return the default one
	 * @return string - The translated language string
	 */
	public function translate( $string, $cname = null, $prefix = null ){

		/**
		 * Filter to control the translation and optimize
		 * them to a specific output
		 */
		$trigger = apply_filters( 'wpwhpro/helpers/control_translations', true, $string, $cname ); //translations have been set to true by default from here on
		if( empty( $trigger ) ){
			return $string;
		}

		if( empty( $string ) ){
			return $string;
		}

		if( ! empty( $cname ) ){
			$context = $cname;
		} else {
			$context = 'default';
		}

		if( $prefix == 'default' ){
			$front = 'WPWHPRO: ';
		} elseif ( ! empty( $prefix ) ){
			$front = $prefix;
		} else {
			$front = '';
		}

		// WPML String Translation Logic (WPML itself has problems with _x in some versions)
		if( function_exists( 'icl_t' ) ){
			return icl_t( (string) 'wp-webhooks-pro', $context, $string );
		} else {
			return $front . _x( $string, $context, 'wp-webhooks-pro' );
		}
	}

	/**
	 * Checks if the parsed param is available on a given site
	 *
	 * @return bool
	 */
	public function is_page( $param = null ){

		if( isset( $_GET['page'] ) ){
			if( ! empty( $param ) ){
				if( $_GET['page'] == $param ){
					return true;
				} else {
					return false;
				}
			} else {
				return true; //set it to true if no parameter is given but it is a page
			}
		}

		return false;
	}

	/**
	 * Creates a formatted admin notice
	 *
	 * @param $content - notice content
	 * @param string $type - Status of the specified notice
	 * @param bool $is_dismissible - If the message should be dismissible
	 * @return string - The formatted admin notice
	 */
	public function create_admin_notice($content, $type = 'info', $is_dismissible = true){
		if(empty($content))
			return '';

		/**
		 * Block an admin notice based onn the specified values
		 */
		$throwit = apply_filters('wpwhpro/helpers/throw_admin_notice', true, $content, $type, $is_dismissible);
		if(!$throwit)
			return '';

		if($is_dismissible !== true){
			$isit = '';
			$bs_isit = '';
		} else {
			$isit = 'is-dismissible';
			$bs_isit = 'alert-dismissible fade show';
		}


		switch($type){
			case 'info':
				$notice = 'notice-info';
				$bs_notice = 'alert-info';
				break;
			case 'success':
				$notice = 'notice-success';
				$bs_notice = 'alert-success';
				break;
			case 'warning':
				$notice = 'notice-warning';
				$bs_notice = 'alert-warning';
				break;
			case 'error':
				$notice = 'notice-error';
				$bs_notice = 'alert-danger';
				break;
			default:
				$notice = 'notice-info';
				$bs_notice = 'alert-info';
				break;
		}

		if( is_array( $content ) ){
			$validated_content = sprintf( __( $content[0], 'wp-webhooks' ), $content[1] );
        } else {
			$validated_content = __( $content, 'wp-webhooks' );
		}

		$bootstrap_layout = apply_filters('wpwhpro/helpers/throw_admin_notice_bootstrap', false, $content, $type, $is_dismissible);
		if( $bootstrap_layout ){
			ob_start();
			?>
			<div class="alert <?php echo $bs_notice; ?> <?php echo $bs_isit; ?>" role="alert">
				<p class="m-0"><?php echo $validated_content; ?></p>
				<?php if( ! empty( $bs_isit ) ) : ?>
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				<?php endif; ?>
			</div>
			<?php
			$res = ob_get_clean();
		} else {
			ob_start();
			?>
			<div class="notice <?php echo $notice; ?> <?php echo $isit; ?>">
				<p class="m-0"><?php echo $validated_content; ?></p>
			</div>
			<?php
			$res = ob_get_clean();
		}

		return $res;
	}

	/**
	 * Formats a specific date to datetime
	 *
	 * @param $date
	 * @return DateTime
	 */
	public function get_datetime($date){
		$date_new = date('Y-m-d H:i:s', strtotime($date));
		$date_new_formatted = new DateTime($date_new);

		return $date_new_formatted;
	}

	/**
	 * Retrieves a response from an url
	 *
	 * @param $url
	 * @param $data - specifies a special part of the response
	 * @param $args The body arguments
	 * @return array|bool|int|string|WP_Error
	 */
	public function get_from_api( $url, $data = '', $args = array() ){

		if(empty($url))
			return false;

		if(!empty($this->disable_ssl)){
			$setting = array(
				'sslverify'     => false,
				'timeout' => 30
			);
		} else {
			$setting = array(
				'timeout' => 30
			);
		}

		if( is_array( $args ) ){
			$setting = array_merge( $setting, $args );
		}

		$val = wp_remote_get( $url, $setting );

		if($data == 'body'){
			$val = wp_remote_retrieve_body( $val );
		} elseif ($data == 'response'){
			$val = wp_remote_retrieve_response_code( $val );
		}

		return $val;
	}

	/**
	 * Remove quotes wrapped around a string if given
	 * It is required that the string starts and ends
	 * with the given quotes in order to be unquoted
	 *
	 * @since 6.1.0
	 * @param string $string
	 * @param string $quotes
	 * @return string
	 */
	public function undoublequote_string( $string, $quotes = '"' ){
		$original_string = $string;

		if( is_string( $string ) ){

			$pre_quote_length = strlen( $quotes );
			$post_quote_length = $pre_quote_length * -1;

			if( 
				substr( $string, 0, $pre_quote_length ) === $quotes //beginning of the string
				&& substr( $string, $post_quote_length ) === $quotes //end of the string
			){
				$string = substr( $string, $pre_quote_length, $post_quote_length );
			}
		}

		return apply_filters( 'wpwhpro/helpers/undoublequote_string', $string, $quotes, $original_string );
	}

	/**
	 * Get the original data format for a given value
	 *
	 * @param string $value
	 * @return void
	 */
	public function get_original_data_format( $value ){
		$data_type = false;
		$original_value = $value;

		if( is_string( $value ) ){

			if( $value === 'true' ){
				$data_type = 'boolean';
				$value = true;
			} elseif ( $value === 'false' ){
				$data_type = 'boolean';
				$value = false;
			} elseif ( strtolower( $value ) === 'null' ){
				$data_type = 'null';
				$value = null;
			} elseif ( is_numeric( $value ) ){
				
				//Check for numbers only
				if( preg_match('/^[0-9]+$/', $value) ){
					$data_type = 'integer';
					$value = intval( $value );
				}

			}

		}

		return apply_filters( 'wpwhpro/helpers/get_original_data_format', $value, $data_type, $original_value );
	}

	/**
	 * Builds an url out of the given values
	 *
	 * @param $url - the default url to set the params to
	 * @param $args - the available args
	 * @return string - the url
	 */
	public function built_url( $url, $args ){
		if( ! empty( $args ) ){
			$url .= '?' . http_build_query( $args );
		}

		return $url;
	}

	/**
	 * Creates the home url in a more optimized way
	 *
	 * @since 2.0.4
	 *
	 * @param $path - the default url to set the params to
	 * @param $scheme - the available args
	 * @return string - the validated url
	 */
	public function safe_home_url( $path = '', $scheme = 'irndefault' ){

		if( $scheme === 'irndefault' ){
			if( is_ssl() ){
				$scheme = 'https';
			} else {
				$scheme = null;
			}
		}

		return home_url( $path, $scheme );
	}

	/**
     * Get Parameters from URL string
     *
	 * @param $url - the url
	 *
	 * @return array - the parameters of the url
	 */
	public function get_parameters_from_url( $url ){

		$parts = parse_url($url);

		parse_str($parts['query'], $url_parameter);

		return empty( $url_parameter ) ? array() : $url_parameter;

    }

	/**
	 * Builds an url out of the mai values
	 *
	 * @param $url - the default url to set the params to
	 * @param $args - the available args
	 * @return string - the url
	 */
	public function get_current_url( $with_args = true, $relative = false ){	
		if( ! $relative ){
			$current_url = ( isset( $_SERVER['HTTPS'] ) && in_array( $_SERVER['HTTPS'], array( 'on', 'On', 'ON', '1', true ) ) ) ? 'https://' : 'http://';

			$host_part = $_SERVER['HTTP_HOST'];
	
			//Support custom ports (since 4.2.0)
			$host_part = str_replace( ':80', '', $host_part );
			$host_part = str_replace( ':443', '', $host_part );
	
			$current_url .= sanitize_text_field( $host_part ) . sanitize_text_field( $_SERVER['REQUEST_URI'] );
		} else {
			$current_url = sanitize_text_field( $_SERVER['REQUEST_URI'] );
		}

	    if( ! $with_args ){
	        $current_url = strtok( $current_url, '?' );
        }

		return apply_filters( 'wpwhpro/helpers/get_current_url', $current_url, $with_args, $relative );
	}

	public function get_nonce_field( $nonce_data ){

		if( ! is_array( $nonce_data ) || ! isset( $nonce_data['action'] ) || ! isset( $nonce_data['arg'] ) ){
			return '';
		}

		ob_start();
		wp_nonce_field( $nonce_data['action'], $nonce_data['arg'] );
		$nonce = ob_get_clean();

		$nonce = str_replace( 'id="', 'id="' . mt_rand( 1, 999999 ) . '-', $nonce );

		return apply_filters( 'wpwhpro/helpers/get_nonce_field', $nonce, $nonce_data );
	}

	/**
     * Get value in between two of our custom tags
     *
     * Usage example:
     * if you want to get the key post_id, you need
     * to have the following tags inside of your content
     * @post_id-start@12345@post_id-end@
     *
	 * @param $ident - the key for a tag you want to check against
	 * @param $content - the content that should be checked against the tag
	 *
	 * @return mixed
	 */
	function get_value_between($ident, $content){
		$matches = array();
		$t = preg_match('/@' . $ident . '-start@(.*?)\\@' . $ident . '-end@/s', $content, $matches);
		return isset( $matches[1] ) ? $matches[1] : '';
	}

	/**
	 * Decode a json response
	 *
	 * @param $response - the response
	 * @return array|mixed|object - the encoded content
	 */
	public function decode_response($response){
		if(!empty($response))
			return json_decode($response, true);

		return $response;
	}

	/**
	 * Evaluate the content type and validate its data
	 * This function is deprecated since version 5.0
	 * Please use 
	 * 		WPWHPRO()->http->get_current_request() In case it is the current request
	 * 		WPWHPRO()->http->get_response( $custom_data ) in case it is a response
	 * 
	 * @deprecated 5.0
	 * @return array - the response content and content_type
	 */
	public function get_response_body( $custom_data = array(), $cached = true ){

		$is_custom = ( ! empty( $custom_data ) ) ? true : false;

		if( ! $is_custom ){
			$response_body = WPWHPRO()->http->get_current_request();
		} else {
			$response_body = WPWHPRO()->http->get_response( $custom_data );
		}

		return apply_filters( 'wpwhpro/helpers/get_deprecated_response_body', $response_body, $custom_data );
	}

	/**
     * Check if a given string is a json
     *
	 * @param $string - the string that should be checked
	 *
	 * @return bool - True if it is json, otherwise false
	 */
	public function is_json( $string ) {

		if( ! is_string( $string ) ){
			return false;
		}

		json_decode( $string );
		if( json_last_error() == JSON_ERROR_NONE ){
			return true;
		}

		json_decode( $string, true );
		if( json_last_error() == JSON_ERROR_NONE ){
			return true;
		}

		return false;
	}

	/**
     * Check if a specified content is xml
     *
	 * @param $content - the string that should be checked
	 *
	 * @return bool - True if it is xml, otherwise false
	 */
	public function is_xml($content) {
	    //Make sure libxml is available
	    if( ! function_exists( 'libxml_use_internal_errors' ) ){
	        return false;
        }

		$content = trim( $content );
		if( empty( $content ) ) {
			return false;
		}

		if( stripos( $content, '<!DOCTYPE html>' ) !== false ) {
			return false;
		}

		libxml_use_internal_errors( true );
		simplexml_load_string( $content );
		$errors = libxml_get_errors();
		libxml_clear_errors();

		return empty( $errors );
	}

	/**
	 * Check if a specified content is xml
	 *
	 * @param $object - the simplexml object
	 * @param $data - the data that should be converted
	 *
	 * @return $obbject - The current simple xml element
	 */
	function convert_to_xml( SimpleXMLElement $object, $data = array() ) {
		$validated_data = array();

		if( ! is_array( $data ) ){

			if( is_string( $data ) && $this->is_json( $data ) ){
				$validated_data = json_decode( $data, true );
			} elseif( is_object( $data ) ){
				$validated_data = json_decode( json_encode( $data ), true );
			}

		} else {
			$validated_data = $data;
		}

		foreach( $validated_data as $key => $value ) {
			if( is_array( $value ) ) {
				$new_object = $object->addChild( $key );
				$this->convert_to_xml( $new_object, $value );
			} elseif( is_object( $value ) ) {
				$new_object = $object->addChild( $key );
				$this->convert_to_xml( $new_object, (array) $value );
			} else {
				// if the key is an integer, it needs text with it to actually work.
				if( is_numeric( $key ) ) {
					$prefix = apply_filters( 'wpwhpro/helpers/convert_to_xml_int_prefix', 'wpwh_', $object, $data );
					$key = $prefix . $key;
				}

				$object->addChild( $key, $value );
			}
		}

		return $object;
	}

	/**
     * This function validates all necessary tags for displayable content.
     *
	 * @param $content - The validated content
	 * @since 1.4
	 * @return mixed
	 */
	public function validate_local_tags( $content ){

	    $user = get_user_by( 'id', get_current_user_id() );

	    $user_name = 'there';
	    if( ! empty( $user ) && ! empty( $user->data ) && ! empty( $user->data->display_name ) ){
	        $user_name = $user->data->display_name;
        }

		$content = str_replace(
			array( '%home_url%', '%admin_url%', '%product_version%', '%product_name%', '%user_name%' ),
			array( home_url(), get_admin_url(), WPWHPRO_VERSION, WPWHPRO_NAME, $user_name ),
			$content
		);

		return $content;
    }

	/**
     * Creates a unique user name for existing users
     *
	 * @param $email - the email address of the user
	 * @param string $prefix - a custom prefix
	 *
	 * @return string
	 */
	public function create_random_unique_username( $email, $prefix = '' ){
		$user_exists = 1;
		$email = sanitize_title( $email );
		do {
			$rnd_str = sprintf("%0d", mt_rand(1, 999999));
			$user_exists = username_exists( $prefix . $email . $rnd_str );
		} while( $user_exists > 0 );
		return $prefix . $email . $rnd_str;
	}

	/**
	 * Serve the user id by a given input
	 *
	 * @param mixed $user
	 * @return integer The user id if given
	 */
	public function serve_user_id( $user ){
		$user_id = 0;

		if( ! empty( $user ) ){
			if( is_numeric( $user ) ){
				$user_id = intval( $user );
			} elseif( is_email( $user ) ){
				$user_data = get_user_by( 'email', $user );
				if( ! empty( $user_data ) && isset( $user_data->ID ) ){
					$user_id = intval( $user_data->ID );
				}
			}
		}

		return apply_filters( 'wpwhpro/helpers/serve_user_id', $user_id, $user );
	}

	/**
     * Display a particular variable
     *
	 * @param string $code - the variable
	 *
	 * @return false|string
	 */
	public function display_var( $code = '' ){
	    ob_start();
	    print_r( $code );
	    return ob_get_clean();
	}

	/**
     * Log certain data within the debug.log file
	 */
	public function log_issue( $text ){

		if( $this->activate_debugging ){
			error_log( $text );
		}

	}

	/**
     * Main value validator
     *
     * You can use it to validate various tags as listed
     * down below.
     *
     * For our string values, we focus on grabbing
     * the content that is set within our custo mtag logic
     *
	 * @param $content
	 * @param $key
	 */
	public function validate_request_value( $content, $key ){
		$return = false;

        if( is_object( $content ) ){

            if( isset( $content->$key ) ){
                $return = $content->$key;
            }

        } elseif( is_array( $content ) ){

            if( isset( $content[ $key ] ) ){
                $return = $content[ $key ];
            }

        } elseif( is_string( $content ) ) {

	        $return = $this->get_value_between( $key, $content );

        }

        //Validate a left over object to an array
        if( is_object( $return ) ){
            $return = json_decode( json_encode( $return ), true );
        }

        if( is_array( $return ) ){
			//Make sure we don't pass single arrays as well
			if( isset( $return[0] ) && is_string( $return[0] ) && count( $return ) <= 1 ){
				$return = $return[0];
			} else {
				//other arrays will be again encoded to a json
				$return = json_encode( $return );
			}

		}

		//Validate form url encode strings again
		if( is_string( $return ) ){
            $stripslashes = apply_filters( 'wpwhpro/helpers/request_values_stripslashes', false, $return );
			if( $stripslashes ){
				$return = stripslashes( $return );
			}
		}

        return apply_filters( 'wpwhpro/helpers/request_return_value', $return, $content, $key );
	}

	/**
	 * Validate a given server header and return its value
	 *
	 * @param string $key
	 * @return string The server header
	 */
	public function validate_server_header( $key ){
		$header = null;
		$uppercase_header = 'HTTP_' . strtoupper( str_replace( '-', '_', $key ) );

        if( isset( $_SERVER[ $key ] ) ) {
            $header = trim( $_SERVER[ $key ] );
        } elseif( isset( $_SERVER[ $uppercase_header ] ) ) {
            $header = trim( $_SERVER[ $uppercase_header ] );
        } elseif( function_exists( 'apache_request_headers' ) ) {
            $request_headers = apache_request_headers();
            $request_headers = array_combine( array_map( 'ucwords', array_keys( $request_headers ) ), array_values( $request_headers ) );

			if( isset( $request_headers[ $key ] ) ) {
                $header = trim( $request_headers[ $key ] );
            }
        }
        return $header;
    }

	/**
	 * Get the WordPress content directory
	 *
	 * @since 6.0
	 * @return string The content dir
	 */
	public function get_wp_content_dir(){
		$wp_content_dir = ( defined( 'WP_CONTENT_DIR' ) ) ? WP_CONTENT_DIR : ABSPATH . DIRECTORY_SEPARATOR . 'wp-content';

		return apply_filters( 'wpwhpro/helpers/get_wp_content_dir', $wp_content_dir );
	}

	/**
	 * Grab the current user IP from the
	 * server variabbles
	 *
	 * @return string - The IP address
	 */
	public function get_current_ip() {
		$ipaddress = false;
		if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ){
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ){
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif( isset( $_SERVER['HTTP_X_FORWARDED'] ) ){
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } elseif( isset( $_SERVER['HTTP_FORWARDED_FOR'] ) ){
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif( isset( $_SERVER['HTTP_FORWARDED'] ) ){
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
        } elseif( isset( $_SERVER['REMOTE_ADDR'] ) ){
			$ipaddress = $_SERVER['REMOTE_ADDR'];
        }

		return $ipaddress;
	}

	/**
	 * Get all folders within a given path
	 *
	 * @since 4.2.0
	 * @return array The folders
	 */
	public function get_folders( $path ){

		$folders = array();

		if( ! empty( $path ) && is_dir( $path ) ){
			$all_folders = scandir( $path );
			foreach( $all_folders as $single ){
				$full_path = $path . DIRECTORY_SEPARATOR . $single;

				if( $single == '..' || $single == '.' || ! is_dir( $full_path ) ){
					continue;
				}

				$folders[] = $single;

			}
		}


		return apply_filters( 'wpwhpro/helpers/get_folders', $folders );
	}

	/**
	 * Get all files within a given path
	 *
	 * @since 4.2.0
	 * @return array The files
	 */
	public function get_files( $path, $ignore = array() ){

		$files = array();
		$default_ignore = array(
			'..',
			'.'
		);

		$ignore = array_merge( $default_ignore, $ignore );

		if( ! empty( $path ) && is_dir( $path ) ){
			$all_files = scandir( $path );
			foreach( $all_files as $single ){
				$full_path = $path . DIRECTORY_SEPARATOR . $single;

				if( in_array( $single, $ignore ) || ! file_exists( $full_path ) ){
					continue;
				}

				$files[] = $single;

			}
		}


		return apply_filters( 'wpwhpro/helpers/get_files', $files );
	}

	/**
	 * Clean a folder completely along with all its files and sub folders
	 *
	 * @since 6.0
	 * @param string $path
	 * @return bool
	 */
	public function clean_folder( $path ){
		$cleaned = false;

		$folders = $this->get_folders( $path );
		foreach( $folders as $folder ){
			$this->clean_folder( $path . '/' . $folder );

			if( is_dir( $path . '/' . $folder ) ){
				rmdir( $path . '/' . $folder );
			}
			
		}
	
		$files = $this->get_files( $path );
		foreach( $files as $file ){
			unlink( $path . '/' . $file );
		}
	
		if( is_dir( $path ) ){
			rmdir( $path );
			$cleaned = true;
		}

		return $cleaned;
	}

	/**
	 * Create a index.php file in a given folder
	 *
	 * @since 6.0
	 * @param string $folder
	 * @return bool
	 */
	public function create_index_php( $folder ){
		$return = false;

		if( is_dir( $folder ) ){
			if( ! file_exists( $folder . '/index.php' ) ){
				if( wp_is_writable( $folder ) ) {
					@file_put_contents( $folder . '/index.php', '<?php' . PHP_EOL . '// Silence is golden.' );
					$return = true;
				}
			} else {
				$return = true; //if it already exists
			}
		}
		
		return $return;
	 }

	 /**
	  * Create link HTML
	  *
	  * @since 6.1.0
	  *
	  * @param string $url
	  * @param string $text
	  * @param array $args
	  * @return string
	  */
	 public function create_link( $url, $text = '', $args = array() ){
		$defaults = array(
			'id' => '',
			'class' => '',
		);
	
		$args = wp_parse_args( $args, $defaults );
		$attributes = $this->format_html_attributes( $args );
	
		$link = sprintf( '<a href="%1$s"%3$s>%2$s</a>',
			esc_url( $url ),
			$text,
			$attributes ? ( ' ' . $attributes ) : ''
		);
		
		return apply_filters( 'wpwhpro/helpers/create_link', $link );
	 }

	 /**
	  * Format HTML attributes
	  *
	  * @since 6.1.0
	  *
	  * @param array $atts
	  * @return string
	  */
	 public function format_html_attributes( $atts ) {
		$output = '';
	
		foreach( $atts as $att => $value ){
			$att = strtolower( trim( $att ) );
	
			if( ! preg_match( '/^[a-z_:][a-z_:.0-9-]*$/', $att ) ){
				continue;
			}
	
			$value = trim( $value );
	
			if( $value !== '' ){
				$output .= sprintf( ' %s="%s"', $att, esc_attr( $value ) );
			}
		}
	
		$output = trim( $output );
	
		return apply_filters( 'wpwhpro/helpers/format_html_attributes', $output );
	}

	/**
	 * Get the current request method
	 *
	 * @since 4.0.0
	 * @return string The request method
	 */
	public function get_current_request_method(){
		return apply_filters( 'wpwhpro/helpers/get_current_request_method', $_SERVER['REQUEST_METHOD'] );
	}

	/**
	* Check if a given plugin is installed
	*
	* @param $slug - Plugin slug
	* @return boolean
	*/
	public function is_plugin_installed( $slug ){
		if( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$all_plugins = get_plugins();

		if( ! empty( $all_plugins[ $slug ] ) ){
			return true;
		} else {
			return false;
		}
	}

	/**
	* Check if a given plugin is active
	*
	* @param $plugin - Plugin identifier
	* @return boolean
	*/
	public function is_plugin_active( $plugin = null ){
		$is_active = false;

		if( ! empty( $plugin ) ){
			switch( $plugin ){
				case 'advanced-custom-fields':
					if( class_exists('ACF') ){
						$is_active = true;
					}
				break;
			}
		}

		return apply_filters( 'wpwhpro/helpers/is_plugin_active', $is_active, $plugin );
	}

	/**
	 * Create signature from a given string
	 *
	 * @since 4.3.1
	 * @param mixed $data
	 * @return string
	 */
	public function generate_signature( $data, $secret ) {

		if( is_array( $data ) || is_string( $data ) ){
			$data = json_encode( $data );
		}

		$data = base64_encode( $data );
		$hash_signature = apply_filters( 'wpwhpro/helpers/generate_signature', 'sha256', $data );

		return base64_encode( hash_hmac( $hash_signature, $data, $secret, true ) );
	}

	/**
	 * Return an array in any circumstance
	 *
	 * @since 4.3.6
	 * @param mixed $data
	 * @return array
	 */
	public function force_array( $data ) {

		$return = array();

		if( empty( $data ) ){
			return $return;
		}

		if( is_numeric( $data ) ){
			$return = array( $data );
		} elseif( is_string( $data ) && $this->is_json( $data ) ){
			$return = json_decode( $data, true );
		} elseif( is_array( $data ) || is_object( $data ) ){
			$return = json_decode( json_encode( $data ), true ); //streamline data
		} else {
			$return = array( $data );
		}

		return apply_filters( 'wpwhpro/helpers/force_array', $return, $data );
	}

	/**
	 * Corrects or re-formats a value based on a given format
	 *
	 * @since 6.0
	 * @param mixed $data
	 * @return mixed
	 */
	public function maybe_format_string( $string ) {

		$return = $string;

		if( ! empty( $return ) ){

			if( $this->is_json( $return ) ){
				$return = json_decode( $return, true );
			} else {
				$return = maybe_unserialize( $return );
			}

			//verify JSON and serialized information within 
			if( is_string( $return ) ){
				$trimmed_value = trim( $return, '"' );
				
				if( ! empty( $trimmed_value ) ){
					if( $this->is_json( $trimmed_value ) ){
						$return = $trimmed_value;
					} elseif( is_serialized( $trimmed_value ) ){
						$return = $trimmed_value;
					}
				}
			}

		}

		return apply_filters( 'wpwhpro/helpers/maybe_format_string', $return, $string );
	}

	/**
	 * Serves the first value of a given variable. 
	 * 
	 * - If it is a string, the string is served
	 * - With an array, we serve the first value
	 * - With an object, we serve the first value
	 *
	 * @since 5.1.1
	 * @param mixed $data
	 * @return array
	 */
	public function serve_first( $data ) {

		$return = '';

		if( is_array( $data ) ){
			$return = reset( $data );
		} elseif( is_object( $data ) ){
			$return = reset( $data );
		} else {
			$return = (string) $data;
		}

		return apply_filters( 'wpwhpro/helpers/serve_first', $return, $data );
	}

	/**
	 * Return an array in any circumstance
	 *
	 * @since 4.3.6
	 * @param mixed $data
	 * @return array
	 */
	public function get_formatted_date( $date, $date_format = 'Y-m-d H:i:s' ) {

		$return = false;

		if( empty( $date ) ){
			return $return;
		}

		if( is_numeric( $date ) ){
			$return = date( $date_format, $date );
		} else {
			$return = date( $date_format, strtotime( $date ) );
		}

		return apply_filters( 'wpwhpro/helpers/get_formatted_date', $return, $date, $date_format );
	}

	/**
	 * Verify the current user and make allow the 
	 * permission to be customized
	 *
	 * @param string $capability
	 * @param string $permission_type
	 * @return bool
	 */
	public function current_user_can( $capability = '', $permission_type = 'default' ){
		return apply_filters( 'wpwhpro/helpers/current_user_can', current_user_can( $capability ), $capability, $permission_type );
	}

	/**
	 * Create a dynamic URL that points to the equivalend endpoint 
	 * at the store page
	 * 
	 * @since 5.0
	 *
	 * @param string $integration
	 * @param string $endpoint
	 * @param string $type
	 * @return string
	 */
	public function get_wp_webhooks_endpoint_url( $integration, $endpoint = '', $type = 'trigger' ){

		$url = '';

		$integration = sanitize_title( $integration );
		$endpoint = sanitize_title( $endpoint );

		if( ! empty( $integration ) ){
			$url = IRONIKUS_STORE . '/integrations/' . $integration;

			if( ! empty( $endpoint ) ){
				$url .= '/' . sanitize_title( $type ) . 's/' . $endpoint;
			}
		}

		return $url;
	}

	/**
	 * Get all Server request headers
	 * 
	 * @since 5.2
	 *
	 * @return array The server headers
	 */
	public function get_all_headers(){

		$headers = array();

		if( function_exists('getallheaders') ){
			$headers = getallheaders();
		} else {

			foreach( $_SERVER as $name => $value ){
				if( substr ($name, 0, 5 ) == 'HTTP_' ){
					$header_key = str_replace( ' ', '-', ucwords( strtolower( str_replace( '_', ' ', substr( $name, 5 ) ) ) ) );
					$headers[ $header_key ] = $value;
				}
			}
			
		}

		return apply_filters( 'wpwhpro/helpers/get_all_headers', $headers );
	}

	/**
	 * Get a list of countries
	 * 
	 * @since 6.0.1
	 *
	 * @return array 
	 */
	public function get_country_list(){
		$countries = array();

		if( ! empty( $this->country_list ) ){
			$countries = $this->country_list;
		} else {
			$country_file = WPWHPRO_PLUGIN_DIR . 'core/includes/partials/misc/country_list.php';
			if( file_exists( $country_file ) ){
				ob_start();
				include( $country_file );
				$list = ob_get_clean();
				
				$countries = json_decode( $list, true );
			}
		}

		return apply_filters( 'wpwhpro/helpers/get_country_list', $countries );
	}

	/**
	 * Check whether a developer version is used or not
	 * It is not recommended using this function
	 * anywhere.
	 *
	 * @since 6.1.0
	 * @return boolean
	 */
	public function is_dev(){
		$is_dev = false;

		if( defined( 'WPWH_DEV' ) ){
			if( WPWH_DEV ){
				$is_dev = true;
			}
		}

		return $is_dev;
	}

	/**
	 * Stream a file
	 *
	 * @since 6.1.1
	 * @param array $args
	 * @return mixed False on failue, stream on success
	 */
	public function stream_file( $args = array() ){

		if( ! isset( $args['headers'] ) || ! isset( $args['content'] ) ){
			return false;
		}

		foreach( $args['headers'] as $hk => $hv ){
			header( $hk . ': ' . $hv );
		}

		echo $args['content'];
		die();

	}

	/**
	 * Return an array of all timezones
	 *
	 * @since 6.1.4
	 * @return array timezones
	 */
	public function get_timezones_array() {
		$timezones = array();
		$all_timezones = timezone_identifiers_list();
	
		if (empty($all_timezones)) {
			return $timezones;
		}
	
		foreach ($all_timezones as $timezone_id) {
			try {
				$timezone = new DateTimeZone($timezone_id);
				$offset = $timezone->getOffset(new DateTime());
				$hours = floor($offset / 3600);
				$minutes = floor(($offset % 3600) / 60);
				$offset_string = sprintf('%+03d:%02d', $hours, $minutes);
	
				$timezones[] = array(
					'value' => $timezone_id,
					'label' => str_replace('_', ' ', $timezone_id) . ' (' . $offset_string . ')',
				);
			} catch (Exception $e) {
				continue;
			}
		}
	
		return $timezones;
	}

}
