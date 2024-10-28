<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_indexnow_Actions_indexnow_add_urls' ) ) :

	/**
	 * Load the indexnow_add_urls action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_indexnow_Actions_indexnow_add_urls {

		public function get_details() {


			$parameter = array(
				'searchengine' => array(
					'required'          => true,
					'label'             => __( 'Search engine', 'wp-webhooks' ),
					'short_description' => __( '(Integer) The search engine you would like to index the URLs for. E.g. www.bing.com', 'wp-webhooks' ),
				),
				'host' => array(
					'required'          => true,
					'label'             => __( 'Host', 'wp-webhooks' ),
					'short_description' => __( '(String) The domain name of the website.', 'wp-webhooks' ),
					'description' => __( 'This field accepts the host name (domain name) of the site you want to index the entries for. E.g. www.yourdomain.com', 'wp-webhooks' ),
				),
				'key' => array(
					'required'          => true,
					'label'             => __( 'Key', 'wp-webhooks' ),
					'short_description' => __( '(String) The unique key for your website.', 'wp-webhooks' ),
					'description' => __( 'If you do not have a key yet, you can create one using a generator like the one hosted by Bing: ', 'wp-webhooks' ) . '<a title="Visit bing.com" target="_blank" href="https://www.bing.com/indexnow">https://www.bing.com/indexnow</a>',
				),
				'keylocation' => array(
					'required'          => true,
					'label'             => __( 'Key location', 'wp-webhooks' ),
					'short_description' => __( '(String) The location of the created key.', 'wp-webhooks' ),
					'description' => __( 'To make sure the search engine can verify you, you must provide proof that you have the correct key for your domain. 
					For that, you need to create a custom text file that contains the key as a value and is named as the key.
					<br>
					An example: <code>https://www.demodomain.test/myIndexNowKey63638.txt</code>
					<br>
					The easiest way to do that is by creating a text file with a key and then upload it to your wordpress website. Once done, you can simply paste the URL into this field.
					<br>
					If you are looking for more details on that, feel free to follow the official IndexNow documentation: 
					', 'wp-webhooks' ) . '<a title="Visit IndexNow" target="_blank" href="https://www.indexnow.org/documentation">https://www.indexnow.org/documentation</a>',
				),
				'url_list' => array(
					'required'          => true,
					'label'             => __( 'URL List', 'wp-webhooks' ),
					'short_description' => __( 'A JSON formatted string of the URLs you want to index.', 'wp-webhooks' ),
					'description' => 
						__( 'Example: ', 'wp-webhooks' ) . '<pre>[
	"https://www.example.test/url1",
	"https://www.example.test/folder/url2",
	"https://www.example.test/url3"
]</pre>' .
						__( 'Add all of the URLs you would like to index (Max 10,000 URLs). To learn more about it, please visit the following manual: ', 'wp-webhooks' ) . '<a title="Visit indexnow.org" target="_blank" href="https://www.indexnow.org/documentation">https://www.indexnow.org/documentation</a>'
						,
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further information about the shortcode.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => '',
				'data' => 
				array (
				),
				'headers' => 
				array (
				  'cache-control' => 'no-cache',
				  'pragma' => 'no-cache',
				  'expires' => '-1',
				  'x-aspnet-version' => '4.0.30319',
				  'accept-ch' => 'Sec-CH-UA-Arch, Sec-CH-UA-Bitness, Sec-CH-UA-Full-Version, Sec-CH-UA-Mobile, Sec-CH-UA-Model, Sec-CH-UA-Platform, Sec-CH-UA-Platform-Version',
				  'x-msedge-ref' => 'Ref A: 151AED5D4FABXXXXXXXXXXXXXXXXXXXX Ref B: MIL30EDGE1420 Ref C: 2022-12-03T19:04:49Z',
				  'content-length' => '0',
				  'date' => 'Sat, 03 Dec 2022 19:04:51 GMT',
				  'alt-svc' => 'h3=":443"; ma=93600',
				  'x-cdn-traceid' => '0.d5aa645f.xxxxxxxxxx.xxxxxxxx',
				),
				'cookies' => 
				array (
				),
				'method' => '',
				'content_type' => '',
				'code' => 202,
				'origin' => '',
				'query' => '',
				'content' => '',
				'response' => 
				array (
				  'code' => 202,
				  'message' => 'Accepted',
				),
				'filename' => NULL,
				'http_response' => 
				array (
				  'data' => NULL,
				  'headers' => NULL,
				  'status' => NULL,
				),
			);

			$description = array(
				'tipps' => array(
					__( 'To learn more about IndexNow, please follow the official documentation:', 'wp-webhooks' ) . ' <a target="_blank" href="https://www.indexnow.org/documentation">https://www.indexnow.org/documentation</a>',
					__( 'Bing offers a simple way to generate a key. You fill find more details here:', 'wp-webhooks' ) . ' <a target="_blank" href="https://www.bing.com/indexnow#implementation">https://www.bing.com/indexnow#implementation</a>',
				)
			);

			return array(
				'action'            => 'indexnow_add_urls', // required
				'name'              => __( 'IndexNow index URLs', 'wp-webhooks' ),
				'sentence'          => __( 'index one or multiple URLs via IndexNow', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Index one or multiple URLs via a IndexNow supported search engine.', 'wp-webhooks' ),
				'description'       => $description,
				'integration'       => 'indexnow',
				'premium'           => true,
			);

		}

		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'     => '',
				'data'    => array(),
			);

			$searchengine = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'searchengine' );
			$host = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'host' );
			$key = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'key' );
			$keylocation = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'keylocation' );
			$url_list = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'url_list' );

			if ( empty( $searchengine ) ) {
				$return_args['msg'] = __( 'Please set the searchengine argument', 'wp-webhooks' );
				return $return_args;
			}

			if ( empty( $host ) ) {
				$return_args['msg'] = __( 'Please set the host argument', 'wp-webhooks' );
				return $return_args;
			}

			if ( empty( $key ) ) {
				$return_args['msg'] = __( 'Please set the key argument', 'wp-webhooks' );
				return $return_args;
			}

			if ( empty( $keylocation ) ) {
				$return_args['msg'] = __( 'Please set the keylocation argument', 'wp-webhooks' );
				return $return_args;
			}

			if ( empty( $url_list ) ) {
				$return_args['msg'] = __( 'Please set the url_list argument', 'wp-webhooks' );
				return $return_args;
			}

			$body = array(
				'host' => $host,
				'key' => $key,
				'keyLocation' => $keylocation,
				'urlList' => array(),
			);

			if( WPWHPRO()->helpers->is_json( $url_list ) ){
				$body['urlList'] = json_decode( $url_list, true );
			}

			$args = array(
				'method' => 'POST',
				'blocking' => true,
				'httpversion' => '1.1',
				'headers' => array(
					'Content-Type' => 'application/json; charset=utf-8',
					'Host' => $searchengine
				),
				'body' => json_encode( $body ),
			);

			$url = 'https://' . $searchengine .'/indexnow';

			$response = WPWHPRO()->http->send_http_request( $url, $args );

			$return_args = array_merge( $return_args, $response );

			return $return_args;

		}

	}

endif; // End if class_exists check.
