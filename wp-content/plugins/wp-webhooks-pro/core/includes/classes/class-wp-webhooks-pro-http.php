<?php

/**
 * WP_Webhooks_Pro_HTTP Class
 *
 * This class is a wrapper for the standard WP_Http class
 * that optimizes the responses based on certain values
 *
 * @since 5.0
 */

/**
 * The api class of the plugin.
 *
 * @since 5.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_HTTP {

    /**
     * The cached, current request
     *
     * @var mixed
     */
    private $request = null;

    /**
     * The cached request body of 
     * the current request
     *
     * @var mixed
     */
    private $request_body = '';

    /**
	 * Execute HTTP related hooks and logic to get 
	 * everything running
	 *
	 * @since 6.0
	 * @return void
	 */
	public function execute(){

		//Execute Scheduled requests
		add_action( 'wpwh_schedule_http_request_callback', array( $this, 'wpwh_schedule_http_request_callback' ), 10, 2 );

	}

    /*
     * ###############################
     * ###
     * ###### HOOK CALBACKS
     * ###
     * ###############################
     */

     /**
      * Handles the callback of a scheduled HTTP request
      *
      * @since 6.0
      * @param string $url The request URL
      * @param array $args The request arguments
      * @return void
      */
     public function wpwh_schedule_http_request_callback( $url, $args = array() ){
        
        $response = $this->send_http_request( $url, $args );

        do_action( 'wpwhpro/http/wpwh_schedule_http_request_callback', $response, $url, $args );

     }

    /*
     * ###############################
     * ###
     * ###### CORE DEFINITIONS
     * ###
     * ###############################
     */

    /**
     * This is the default request structure used for 
     *
     * @return array
     */
	public function get_default_request_structure(){
        $structure = array(
            'headers' => array(),
            'cookies' => array(),
            'method' => '',
            'content_type' => '',
            'code' => '', //http_response_code()
            'origin' => '',
            'query' => '',
            'content' => '', //same as body but necessary for backward compatibility
            'msg' => '',
            'ip' => '',
        );

        return apply_filters( 'wpwhpro/http/get_default_request_structure', $structure );
    }

    /**
     * This is the default request structure used for 
     *
     * @return array
     */
	public function get_default_response_structure(){
        $structure = array(
            'success' => false,
            'msg' => '',
            'headers' => array(),
            'cookies' => array(),
            'method' => '',
            'content_type' => '',
            'code' => '', //http_response_code()
            'origin' => '',
            'query' => '',
            'content' => '', //same as body but necessary for backward compatibility
        );

        return apply_filters( 'wpwhpro/http/get_default_response_structure', $structure );
    }

    public function get_http_origin(){
        $validated_request_origin = get_http_origin();

        return apply_filters( 'wpwhpro/http/get_http_origin', $validated_request_origin );
    }

    /**
     * Validate a given HTTP code based on the possible request data
     * 
     * @since 5.2.4
     *
     * @param array $args
     * @return string The response code
     */
    public function validate_http_response_code( $args ){
        $validated_response_code = '';

        if( is_array( $args ) && isset( $args['code'] ) && is_numeric( $args['code'] ) ){
            $validated_response_code = $args['code'];
        } elseif( is_array( $args ) && isset( $args['response'] ) && isset( $args['response']['code'] ) && is_numeric( $args['response']['code'] ) ){
            $validated_response_code = $args['response']['code'];
        }

        return apply_filters( 'wpwhpro/http/validate_http_response_code', $validated_response_code );
    }

    /*
     * ###############################
     * ###
     * ###### CURRENT REQUEST FUNCTIONS
     * ###
     * ###############################
     */

    public function get_current_request( $cached = true ){

        if( $cached && $this->request !== null ){
            return $this->request;
        }

        $args = array(
            'headers' => $this->get_current_request_headers(),
            'cookies' => $this->get_current_request_cookies(),
            'method' => $this->get_current_request_method(),
            'content_type' => $this->get_current_request_content_type(),
            'code' => $this->get_current_request_code(),
            'origin' => $this->get_http_origin(),
            'query' => $this->get_current_request_query(),
            'content' => $this->get_current_request_body(),
            'ip' => $this->get_current_request_ip(),
        );

        $request = wp_parse_args( $args, $this->get_default_request_structure() );

        //Parameters are kept for backward compatibility
        $request = apply_filters( 'wpwhpro/helpers/validate_response_body', $request, $request['content_type'], file_get_contents('php://input'), array() );

        $request = apply_filters( 'wpwhpro/http/get_current_request', $request );
        $this->request = $request;

        return $request;
    }

    public function get_current_request_headers(){
        $validated_headers = array();

        $headers = WPWHPRO()->helpers->get_all_headers();
        if( ! empty( $headers ) && is_array( $headers ) ){
            foreach( $headers as $header_key => $header_value ){

                $header_key_validated = sanitize_title( $header_key );

                //Skip cookies as they are fetched from get_current_cookies()
                if( $header_key_validated === 'cookie' ){
                    continue;
                }

                $validated_headers[ $header_key_validated ] = $header_value;
            }
        }

        return apply_filters( 'wpwhpro/http/get_current_request_headers', $validated_headers );
    }

    public function get_current_request_cookies(){
        $validated_cookies = array();

        $cookies = $_COOKIE;
        if( ! empty( $cookies ) && is_array( $cookies ) ){
            foreach( $cookies as $cookie_key => $cookie_value ){

                $cookie_key_validated = sanitize_title( $cookie_key );

                $validated_cookies[ $cookie_key_validated ] = $cookie_value;
            }
        }

        return apply_filters( 'wpwhpro/http/get_current_request_cookies', $validated_cookies );
    }

    public function get_current_request_method(){
        $validated_method = '';

        if( isset( $_SERVER['REQUEST_METHOD'] ) ){
			$validated_method = $_SERVER["REQUEST_METHOD"];
		}

        return apply_filters( 'wpwhpro/http/get_current_request_method', $validated_method );
    }

    public function get_current_request_content_type(){
        $validated_content_type = '';

        if( isset( $_SERVER["CONTENT_TYPE"] ) ){
			$validated_content_type = $_SERVER["CONTENT_TYPE"];
		}

        return apply_filters( 'wpwhpro/http/get_current_request_content_type', $validated_content_type );
    }

    public function get_current_request_code(){
        $validated_request_code = http_response_code();

        return apply_filters( 'wpwhpro/http/get_current_request_code', $validated_request_code );
    }

    public function get_current_request_query(){
        $validated_request_query = $_GET;

        return apply_filters( 'wpwhpro/http/get_current_request_query', $validated_request_query );
    }

    public function get_current_request_body( $cached = true ){

		$validated_data = '';
        $request_type = $this->get_current_request_content_type();

        //Cache current content
        if( $cached && ! empty( $this->request_body ) ){
			return $this->request_body;
        }

		$request_body = file_get_contents('php://input');
		$content_evaluated = false;

		if( 
            $this->is_content_type( $request_type, 'application/json' )
            || $this->is_content_type( $request_type, 'application/ld+json' )
         ){
			if( WPWHPRO()->helpers->is_json( $request_body ) ){
				$validated_data = ( json_decode( $request_body ) !== null ) ? json_decode( $request_body ) : (object) json_decode( $request_body, true );
				$content_evaluated = true;
			} else {
				WPWHPRO()->helpers->log_issue( __( "The incoming webhook content was sent as application/json, but did not contain a valid JSON: ", 'admin-debug-feature' ) . WPWHPRO()->helpers->display_var( $request_body ) );
			}
        }

        if( ! $content_evaluated ){
            if( 
                $this->is_content_type( $request_type, 'application/xml' )
                || $this->is_content_type( $request_type, 'text/xml' )
                || $this->is_content_type( $request_type, 'application/xhtml+xml' )
            ){
                if( WPWHPRO()->helpers->is_xml( $request_body ) ){
                    $validated_data = simplexml_load_string( $request_body );
                    $content_evaluated = true;
                } else {
                    WPWHPRO()->helpers->log_issue( __( "The incoming webhook content was sent as application/xml, but did not contain a valid XML: ", 'admin-debug-feature' ) . WPWHPRO()->helpers->display_var( $request_body ) );
                }
            }
        }

		if( ! $content_evaluated && $this->is_content_type( $request_type, 'multipart/form-data' )){

			$multipart = array();

			if( isset( $_POST ) ){
				$multipart = array_merge( $multipart, $_POST );
			}

			if( isset( $_FILES ) ){
				$multipart = array_merge( $multipart, $_FILES );
			}

			$validated_data = (object) $multipart;
			$content_evaluated = true;

			if( empty( $multipart ) ){
				WPWHPRO()->helpers->log_issue( __( "The incoming webhook content was sent as multipart/form-data, but did not contain any values: ", 'admin-debug-feature' ) . WPWHPRO()->helpers->display_var( $request_body ) );
			}

        }

		if( ! $content_evaluated && $this->is_content_type( $request_type, 'application/x-www-form-urlencoded' )){
			parse_str( $request_body, $form_data );
			$form_data = (object)$form_data;
			if( is_object( $form_data ) ){
				$validated_data = $form_data;
				$content_evaluated = true;
            }
        }

        //Added for backward compatibility
        //If nothing is set, we take the content as it comes
        if( ! $content_evaluated && is_string( $request_body ) ){
			if( ! empty( $request_body ) && is_string( $request_body ) ){
				$validated_data = $request_body;
			} else {

                //Provide a more optimized way of validating only GET requests
                if( $this->get_current_request_method() === 'GET' ){
                    $validated_data = ! empty( $_GET ) ? $_GET : array();
                }
			}
		}

        //force array
        if( ! is_array( $validated_data ) ){
            $validated_data = WPWHPRO()->helpers->force_array( $validated_data );
        }

        //Backward compatibility with the Zapier setup
		if( is_object( $validated_data ) && isset( $validated_data->wpwhpro_zapier_arguments ) ){
			foreach( $validated_data->wpwhpro_zapier_arguments as $zap_key => $zap_val ){
				$validated_data->{$zap_key} = $zap_val;
			}
		} elseif( is_array( $validated_data ) && isset( $validated_data['wpwhpro_zapier_arguments'] ) ){
			foreach( $validated_data['wpwhpro_zapier_arguments'] as $zap_key => $zap_val ){
				$validated_data[ $zap_key ] = $zap_val;
			}
		}

        $validated_data_original = $validated_data; //Preserve original data for additional filtering
		
		$this->request_body = $validated_data;

		return apply_filters( 'wpwhpro/http/get_current_request_body', $validated_data, $cached, $validated_data_original );
	}

    public function get_current_request_ip(){
        $validated_request_ip = WPWHPRO()->helpers->get_current_ip();

        return apply_filters( 'wpwhpro/http/get_current_request_ip', $validated_request_ip );
    }

    /*
     * ###############################
     * ###
     * ###### REQUEST FUNCTIONS
     * ###
     * ###############################
     */

     /**
      * Schedule the execution of an HTTP request using
      * wp_schedule_single_event() 
      *
      * @since 6.0
      * @param integer $timestamp The execution timestamp
      * @param string $url The request URL
      * @param array $args The arguments
      * @return array The response of the request
      */
    public function schedule_http_request( $timestamp, $url, $args ){
        $response = false;

        if( ! empty( $timestamp ) ){
            $attributes = array(
                'url' => $url,
                'args' => $args,
            );

            $response = WPWHPRO()->scheduler->schedule_single_action( array(
                'timestamp' => intval( $timestamp ),
                'hook' => 'wpwh_schedule_http_request_callback',
                'attributes' => $attributes,
            ) );

            //customize the response message
            if( $response['success'] ){
                $response['msg'] = __( 'The trigger was successfully scheduled.', 'wp-webhooks' );
            }

        }

        //validate response
        $validated_response = $this->get_response( $response );

        return apply_filters( 'wpwhpro/http/schedule_http_request', $validated_response, $response, $url, $args );
    }

    public function send_http_request( $url, $args = array() ){

        $method = ( isset( $args['method'] ) && ! empty( $args['method'] ) ) ? $args['method'] : 'POST';
        $args['content_type'] = $this->get_request_content_type( $args );
        $args = $this->validate_request_body( $args );  

        $url = apply_filters( 'wpwhpro/http/send_http_request/url', $url, $args );
        $args = apply_filters( 'wpwhpro/http/send_http_request/args', $args, $url );
        $method = apply_filters( 'wpwhpro/http/send_http_request/method', $method, $args, $url );

        //assign the method
        $args['method'] = $method;
  
        if( $method === 'GET' ){
            $response = wp_remote_get( $url, $args );
        } else {
            $response = wp_remote_post( $url, $args );
        }    

        $validated_response = $this->get_response( $response );

        return apply_filters( 'wpwhpro/http/send_http_request', $validated_response, $url, $args );
    }

    public function get_request_content_type( $data ){
        $content_type = '';

        if( is_array( $data ) ){
            if( isset( $data['content_type'] ) ){
                $content_type = $data['content_type'];
            } elseif( isset( $data['headers'] ) && ! empty( $data['headers'] ) ){

                foreach( $data['headers'] as $header_key => $header_value ){
                    if( strtolower( $header_key ) === 'content-type' ){
                        $content_type = $data['headers'][ $header_key ];
                        break;
                    }
                }

            }
        }

        return apply_filters( 'wpwhpro/http/get_request_content_type', $content_type, $data );
    }

    public function validate_request_body( $data ){
        $validated_data = '';
        $original_data = $data;
        $method = ( isset( $data['method'] ) && ! empty( $data['method'] ) ) ? $data['method'] : 'POST';

        if( ! isset( $data['body'] ) ){
            $data['body'] = $validated_data;
        }

        $request_type = isset( $data['content_type'] ) ? $data['content_type'] : ''; 

        if( empty( $request_type ) || $data['body'] === '' ){
            return apply_filters( 'wpwhpro/http/validate_request_body/no_validation', $data, $original_data );
        }

        //Skip validation in case the GET Method is used as it only accepts arrays
        if( $method === 'GET' ){
            $validated_data = array();

            if( is_array( $data['body'] ) || is_object( $data['body'] ) ){

                //streamline arrays and objects
                $validated_data = json_decode( json_encode( $data['body'] ), true );

            } elseif( WPWHPRO()->helpers->is_json( $data['body'] ) ){

                $validated_data = json_decode( $data['body'], true );

            } elseif( WPWHPRO()->helpers->is_xml( $data['body'] ) ){

                $temp_xml = simplexml_load_string( $data['body'], "SimpleXMLElement", LIBXML_NOCDATA );
                $temp_json = json_encode( $temp_xml );

                if( ! empty( $temp_json ) ){
                    $validated_data = json_decode( $temp_json, true );
                }

            }

            if( is_array( $validated_data ) ){
                $data['body'] = $validated_data;
            }

            return apply_filters( 'wpwhpro/http/validate_request_body', $data, $original_data );
        }

		if( 
            $this->is_content_type( $request_type, 'application/json' )
            || $this->is_content_type( $request_type, 'application/ld+json' )
         ){

            if( WPWHPRO()->helpers->is_json( $data['body'] ) ){
                $validated_data = trim( $data['body'] );
            } else {
                $validated_data = trim( wp_json_encode( $data['body'] ) );
            }
			
        } elseif( 
            $this->is_content_type( $request_type, 'application/xml' )
            || $this->is_content_type( $request_type, 'text/xml' )
            || $this->is_content_type( $request_type, 'application/xhtml+xml' )
        ){

            if( WPWHPRO()->helpers->is_xml( $data['body'] ) ){
                $validated_data = $data['body'];
            } else{
                $sxml_data = apply_filters( 'wpwhpro/http/validate_request_body/simplexml_data', '<data/>', $data );
                $xml = WPWHPRO()->helpers->convert_to_xml( new SimpleXMLElement( $sxml_data ), $data['body'] );
                $validated_data = $xml->asXML();
            }
			
        } elseif( 
            $this->is_content_type( $request_type, 'application/x-www-form-urlencoded' )
            || $this->is_content_type( $request_type, 'multipart/form-data' )
        ){

            if( WPWHPRO()->helpers->is_json( $data['body'] ) ){
                $validated_data = http_build_query( json_decode( $data['body'], true ), 'item_' );
            } elseif( is_array( $data['body'] ) ) {
                $validated_data = http_build_query( $data['body'], 'item_' );
            } elseif( is_object( $data['body'] ) ) {
                $validated_data = http_build_query( json_decode( json_encode( $data['body'] ), true ), 'item_' );
            }
			
        } else {
            $validated_data = $data['body'];
        }

        $data['body'] = $validated_data;

		return apply_filters( 'wpwhpro/http/validate_request_body', $data, $original_data );
    }

    /*
     * ###############################
     * ###
     * ###### RESPONSE FUNCTIONS
     * ###
     * ###############################
     */

     /**
      * Generate a predefined structure for a 
      * specific request
      *
      * @param mixed $args
      * @return array The validated request
      */
    public function get_response( $args = array() ){

        if( is_wp_error( $args ) ){
            $args = $this->generate_wp_error_response( $args );
        } elseif( is_array( $args ) ){
            $args['success'] = true;
        }

        //set up a default response in case no array given
        if( ! is_array( $args ) ){
            $args = $this->get_default_response_structure();
            $args['msg'] = __( 'An invalid response was given.', 'wp-webhooks' );
        }

        //Keep for backwards compatibility
        if( isset( $args['payload'] ) ){
            $args['content'] = $args['payload'];
            unset( $args['payload'] );
        }

        //Merge WP_Http object keys
        $args = $this->merge_wp_http_object_data( $args );

        if( ! isset( $args['content_type'] ) ){
            if( isset( $args['headers'] ) && isset( $args['headers']['content-type'] ) ){
                $args['content_type'] = $args['headers']['content-type'];
            }
        }
          
        $args['origin'] = $this->get_http_origin();
        $args['code'] = $this->validate_http_response_code( $args );

        //Validate against a given HTTP response
        if( is_numeric( $args['code'] ) ){
            $args['success'] = false;
            $response_code = intval( $args['code'] );

            if( $response_code >= 200 && $response_code <= 299 ){
                $args['success'] = true;
            }
        }

        $args = $this->validate_response_body( $args );

        $default_structure = $this->get_default_response_structure();
        $response = wp_parse_args( $args, $default_structure );

        return apply_filters( 'wpwhpro/http/get_request', $response, $args );
    }

    /**
     * Merge a given WP_Http response to our 
     * own structure for cross-compatibility    
     *
     * @return array
     */
    public function merge_wp_http_object_data( $args ){

        if( is_array( $args ) ){

            //Merge the body to our content key
            if( isset( $args['body'] ) ){
                $args['content'] = $args['body'];
                unset( $args['body'] );
            }

            //Merge response code values
            if( isset( $args['response'] ) ){

                if( ! is_wp_error( $args['response'] ) ){
                    $code = wp_remote_retrieve_response_code( $args );
                    if( ! empty( $code ) ){
                        $args['code'] = $code;
                    }
                } else {
                    unset( $args['response'] );
                }
                
            }

            //Merge the headers
            if( isset( $args['headers'] ) && is_object( $args['headers'] ) ){
                $headers_validated = array();
                $headers = wp_remote_retrieve_headers( $args ); //Used to validate against object erorrs
                if( ! empty( $headers ) ){
                    foreach( $headers as $header_key => $header_value ){
                        $headers_validated[ $header_key ] = $header_value;
                    }
                }

                $args['headers'] = $headers_validated;
            }

            //Merge the cookies
            if( isset( $args['cookies'] ) && ( is_object( $args['cookies'] ) || is_array( $args['cookies'] ) ) ){
                $cookies_validated = array();

                $cookies = wp_remote_retrieve_cookies( $args ); //Used to validate against object erorrs
                if( ! empty( $cookies ) ){
                    foreach( $cookies as $cookie_key => $cookie ){

                        if ( ! is_a( $cookie, 'WP_Http_Cookie' ) ) {
                            $cookies_validated[ $cookie->name ] = $cookie->value;
                        } else {
                            $cookies_validated[ $cookie_key ] = $cookie;
                        }
    
                    }
                }

                $args['cookies'] = $cookies_validated;
            }
        }

        return $args;
    }

    /**
     * Validate the data of any kind of response body
     * This function is used for validating an existing request
     *
     * @param array $args
     * @return mixed
     */
    public function validate_response_body( $args = true ){

		$validated_data = '';

        if( ! isset( $args['content'] ) ){
            $args['content'] = $validated_data;
            return $args;
        }

        $request_type = isset( $args['content_type'] ) ? $args['content_type'] : '';

        if( empty( $request_type ) ){
            return $args;
        }

		$request_body = $args['content'];
		$content_evaluated = false;

		if( 
            $this->is_content_type( $request_type, 'application/json' )
            || $this->is_content_type( $request_type, 'application/ld+json' )
        ){
			if( WPWHPRO()->helpers->is_json( $request_body ) ){
				$validated_data = ( json_decode( $request_body ) !== null ) ? json_decode( $request_body ) : (object) json_decode( $request_body, true );
				$content_evaluated = true;
			} else {
				WPWHPRO()->helpers->log_issue( __( "The incoming webhook content was sent as application/json, but did not contain a valid JSON: ", 'admin-debug-feature' ) . WPWHPRO()->helpers->display_var( $request_body ) );
			}
        }

		if( 
            ! $content_evaluated
            && (
                $this->is_content_type( $request_type, 'application/xml' )
                || $this->is_content_type( $request_type, 'text/xml' )
                || $this->is_content_type( $request_type, 'application/xhtml+xml' )
            )
        ){
			if( WPWHPRO()->helpers->is_xml( $request_body ) ){
				$validated_data = simplexml_load_string( $request_body );
				$content_evaluated = true;
			} else {
				WPWHPRO()->helpers->log_issue( __( "The incoming webhook content was sent as application/xml, but did not contain a valid XML: ", 'admin-debug-feature' ) . WPWHPRO()->helpers->display_var( $request_body ) );
			}
        }

		if(
            ! $content_evaluated 
            && $this->is_content_type( $request_type, 'application/x-www-form-urlencoded' )
        ){
			parse_str( $request_body, $form_data );
			$form_data = (object)$form_data;
			if( is_object( $form_data ) ){
				$validated_data = $form_data;
				$content_evaluated = true;
            }
        }

        //Added for backward compatibility
        //If nothing is set, we take the content as it comes
        if( ! $content_evaluated && is_string( $request_body ) ){
			if( ! empty( $request_body ) && is_string( $request_body ) ){
				$validated_data = $request_body;
			}
		}

        //force array
        if( ! is_array( $validated_data ) ){
            $validated_data = WPWHPRO()->helpers->force_array( $validated_data );
        }

        $original_args = $args; //Preserve original data for additional filtering
        $args['content'] = $validated_data;

        //Parameters are kept for backward compatibility
        $args = apply_filters( 'wpwhpro/helpers/validate_response_body', $args, $request_type, $request_body, $args );

		return apply_filters( 'wpwhpro/http/validate_response_body', $args, $original_args );
	}

    /**
     * Generate a formatted response
     * based on a given WP_Error object
     *
     * @param WP_Error $wp_error
     * @return array The formatted data 
     */
    public function generate_wp_error_response( $wp_error ){

        $response_data = $this->get_default_response_structure();

        if( empty( $wp_error ) || ! is_wp_error( $wp_error ) ){
            return $response_data;
        }

        $response_data['msg'] = $wp_error->get_error_message();
        $response_data['code'] = $wp_error->get_error_code();
        $response_data['content'] = $wp_error->get_all_error_data();

        return apply_filters( 'wpwhpro/http/generate_wp_error_response', $response_data, $wp_error );
    }

    /**
	 * Validate and check if a string contains a given content type
	 *
     * @since 6.1.0
	 * @param string $content_type
	 * @return boolean
	 */
	public function is_content_type( $content_type_to_check, $content_type ){
        $is_type = false;

        if( ! empty( $content_type_to_check ) && ! empty( $content_type ) ){
            $validated_content_type_to_check = strtolower( $content_type_to_check );
            $validated_content_type = strtolower( $content_type );

            if( strpos( $validated_content_type_to_check, $validated_content_type ) !== false ){
				$is_type = true;
			}
        }

        return apply_filters( 'wpwhpro/http/is_content_type', $is_type, $content_type_to_check, $content_type );
    }

}
