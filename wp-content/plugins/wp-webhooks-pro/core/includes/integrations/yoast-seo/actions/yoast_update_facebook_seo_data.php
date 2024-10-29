<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_yoast_seo_Actions_yoast_update_facebook_seo_data' ) ) :
	/**
	 * Load the yoast_update_facebook_seo_data action
	 *
	 * @since 6.1.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_yoast_seo_Actions_yoast_update_facebook_seo_data {

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
					'short_description' => __( '(String) The Facebook SEO title.', 'wp_webhooks' ),
				),
				'description' => array(
					'label'             => __( 'Description', 'wp_webhooks' ),
					'short_description' => __( '(String) The Facebook SEO description.', 'wp_webhooks' ),
				),
				'image'       => array(
					'label'             => __( 'Facebook image', 'wp_webhooks' ),
					'type'              => 'select',
					'query'             => array(
						'filter' => 'posts',
						'args'   => array(
							'post_type'      => 'attachment',
							'post_status'    => 'any',
							'post_mime_type' => 'image',
						),
					),
					'short_description' => __( '(String) An image of your choice. Images are listed from the media library of your website.', 'wp_webhooks' ),
					'description' => __( '(String) You can also upload an image to your WordPress media library using the "WordPres" integration along with the "Create URL attachment" action.', 'wp_webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action status.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'The Facebook SEO data has been updated successfully.',
			);

			return array(
				'action'            => 'yoast_update_facebook_seo_data', // required
				'name'              => __( 'Update Facebook SEO data', 'wp-webhooks' ),
				'sentence'          => __( 'update the Facebook SEO data', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Update the Facebook SEO data within Yoast SEO.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'yoast-seo',
				'premium'           => true,
			);

		}

		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'     => '',
			);

			$post_id   = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_id' ) );
			$metatitle = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'title' );
			$metadesc  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'description' );
			$image_id  = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'image' ) );

			if ( ! empty( $metatitle ) ) {
				update_post_meta( $post_id, '_yoast_wpseo_opengraph-title', $metatitle );
			}

			if ( ! empty( $metadesc ) ) {
				update_post_meta( $post_id, '_yoast_wpseo_opengraph-description', $metadesc );
			}

			if ( ! empty( $image_id ) && get_post_type( $image_id ) === 'attachment'  ) {
				update_post_meta( $post_id, '_yoast_wpseo_opengraph-image-id', $image_id );

				$image_url  = wp_get_attachment_image_url( $image_id, 'full' );
				update_post_meta( $post_id, '_yoast_wpseo_opengraph-image', $image_url );
			}

			$return_args['success'] = true;
			$return_args['msg']     = __( 'The Facebook SEO data has been updated successfully.', 'wp-webhooks' );

			return $return_args;

		}

	}
endif;
