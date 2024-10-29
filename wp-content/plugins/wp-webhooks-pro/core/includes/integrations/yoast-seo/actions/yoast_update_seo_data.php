<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_yoast_seo_Actions_yoast_update_seo_data' ) ) :
	/**
	 * Load the yoast_update_seo_data action
	 *
	 * @since 6.1.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_yoast_seo_Actions_yoast_update_seo_data {

		public function get_details() {
			$parameter = array(
				'post_id'     => array(
					'required'          => true,
					'label'             => __( 'Post', 'wp-webhooks' ),
					'type'              => 'select',
					'query'             => array(
						'filter' => 'posts',
						'args'   => array(),
					),
					'short_description' => __(
						'(String) The post ID (Custom post types supported).',
						'wp-webhooks'
					),
				),
				'title'       => array(
					'required'          => true,
					'label'             => __( 'Title', 'wp_webhooks' ),
					'short_description' => __( '(String) The SEO title.', 'wp_webhooks' ),
				),
				'description' => array(
					'label'             => __( 'Description', 'wp_webhooks' ),
					'short_description' => __( '(String) The SEO description.', 'wp_webhooks' ),
				),
				'focus_keyword'    => array(
					'label'             => __( 'Focus keyword', 'wp_webhooks' ),
					'short_description' => __( '(String) The SEO focus keyword.', 'wp_webhooks' ),
				),
				'keywords'    => array(
					'label'             => __( 'Keywords', 'wp_webhooks' ),
					'short_description' => __( '(String) The SEO keywords. You can choose multiple ones by comma-separating them.', 'wp_webhooks' ),
				),
				'nofollow'    => array(
					'label'             => __( 'Nofollow', 'wp_webhooks' ),
					'type' => 'select',
					'choices' => array(
						'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
					),
					'multiple' => false,
					'default_value' => 'no',
					'short_description' => __( '(String) Set this to "yes" to prevent search engines from following this post.', 'wp_webhooks' ),
				),
				'nofollow'    => array(
					'label'             => __( 'Nofollow', 'wp_webhooks' ),
					'type' => 'select',
					'choices' => array(
						'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
					),
					'multiple' => false,
					'default_value' => 'no',
					'short_description' => __( '(String) Set this to "yes" to prevent search engines from following this post.', 'wp_webhooks' ),
				),
				'robot_advanced'    => array(
					'label'             => __( 'Meta robots advanced', 'wp_webhooks' ),
					'type' => 'select',
					'choices' => array(
						'noarchive' => array( 'label' => __( 'No archive', 'wp-webhooks' ) ),
						'noimageindex' => array( 'label' => __( 'No image index', 'wp-webhooks' ) ),
						'nosnippet' => array( 'label' => __( 'No snippet', 'wp-webhooks' ) ),
					),
					'multiple' => true,
					'short_description' => __( '(String) Further customize the restrictions for meta robots.', 'wp_webhooks' ),
				),
				'canonical'    => array(
					'label'             => __( 'Canonical', 'wp_webhooks' ),
					'short_description' => __( '(String) A different canonical URL of your choice.', 'wp_webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action status.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'The post SEO data has been updated successfully.',
			);

			$description = array(
				'tipps'     => array(
					__( 'This action supports replacement variables. You can use all of the Yoast replacement variables such as <code>%%sitename%%</code> to display the dynamic site name.', 'wp-webhooks' )
				),
			);

			return array(
				'action'            => 'yoast_update_seo_data', // required
				'name'              => __( 'Update post SEO data', 'wp-webhooks' ),
				'sentence'          => __( 'update the SEO data of a post', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Update the SEO data of a post within Yoast SEO.', 'wp-webhooks' ),
				'description'       => $description,
				'integration'       => 'yoast-seo',
				'premium'           => true,
			);

		}

		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'     => '',
			);

			$post_id        = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_id' ) );
			$metatitle      = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'title' );
			$metadesc       = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'description' );
			$focus_keyword  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'focus_keyword' );
			$metakeywords   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'keywords' );
			$nofollow       = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'nofollow' ) === 'yes' ) ? true : false;
			$noindex        = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'noindex' ) === 'yes' ) ? true : false;
			$robot_advanced = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'robot_advanced' );
			$canonical      = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'canonical' );

			if ( ! empty( $metatitle ) ) {
				update_post_meta( $post_id, '_yoast_wpseo_title', $metatitle );
			}

			if ( ! empty( $metadesc ) ) {
				update_post_meta( $post_id, '_yoast_wpseo_metadesc', $metadesc );
			}

			if ( ! empty( $focus_keyword ) ) {
				update_post_meta( $post_id, '_yoast_wpseo_focuskw', $focus_keyword );
			}

			if ( ! empty( $metakeywords ) ) {
				update_post_meta( $post_id, '_yoast_wpseo_metakeywords', $metakeywords );
			}

			if ( ! empty( $nofollow ) ) {
				update_post_meta( $post_id, '_yoast_wpseo_meta-robots-nofollow', 1 );
			} else {
				delete_post_meta( $post_id, '_yoast_wpseo_meta-robots-nofollow' );
			}

			if ( ! empty( $noindex ) ) {
				update_post_meta( $post_id, '_yoast_wpseo_meta-robots-noindex', 1 );
			} else {
				delete_post_meta( $post_id, '_yoast_wpseo_meta-robots-noindex' );
			}

			if( ! empty( $robot_advanced ) ){
				if( is_array( $robot_advanced ) || WPWHPRO()->helpers->is_json( $robot_advanced ) ){

					if( is_string( $robot_advanced ) ){
						$robot_advanced = json_decode( $robot_advanced, true );
					}

					update_post_meta( $post_id, '_yoast_wpseo_meta-robots-adv', implode( ',', $robot_advanced ) );
				}
			} else {
				delete_post_meta( $post_id, '_yoast_wpseo_meta-robots-adv' );
			}

			if ( ! empty( $canonical ) ) {
				update_post_meta( $post_id, '_yoast_wpseo_canonical', $canonical );
			} else {
				delete_post_meta( $post_id, '_yoast_wpseo_canonical' );
			}

			$return_args['success'] = true;
			$return_args['msg']     = __( 'The post SEO data has been updated successfully.', 'wp-webhooks' );

			return $return_args;

		}

	}
endif;
