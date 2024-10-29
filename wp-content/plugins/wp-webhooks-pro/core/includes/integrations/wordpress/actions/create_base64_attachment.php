<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_create_base64_attachment' ) ) :

	/**
	 * Load the create_base64_attachment action
	 *
	 * @since 6.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_create_base64_attachment {

		public function get_details(){

			$parameter = array(
				'base64'		   => array( 
					'label' => __( 'Base64 string', 'wp-webhooks' ), 
					'required' => true, 
					'short_description' => __( 'The relative path of the file you want to create the attachment of. Please see the description for more information.', 'wp-webhooks' ),
				),
				'parent_post_id' => array( 
					'label' => __( 'Parent post ID', 'wp-webhooks' ), 
					'short_description' => __( 'The parent post id in case you want to set a parent for it. Default: 0', 'wp-webhooks' ),
				),
				'file_name'	  => array( 
					'label' => __( 'File name', 'wp-webhooks' ), 
					'short_description' => __( 'Customize the file name of the attachment. - Please see the description for further information.', 'wp-webhooks' ),
				),
				'add_post_thumbnail' => array( 
					'label' => __( 'Add as post thumbnail', 'wp-webhooks' ), 
					'short_description' => __( 'Assign this attachment as a post thumbnail to one or multiple posts. Please see the description for further details.', 'wp-webhooks' ),
				),
				'attachment_image_alt' => array( 
					'label' => __( 'Attachment Image alt', 'wp-webhooks' ), 
					'short_description' => __( 'Add a custom Alternative Text to the attachment (Image ALT).', 'wp-webhooks' ),
				),
				'attachment_title' => array( 
					'label' => __( 'Attachment title', 'wp-webhooks' ), 
					'short_description' => __( 'Add a custom title to the attachment (Image Title).', 'wp-webhooks' ),
				),
				'attachment_caption' => array( 
					'label' => __( 'Attachment caption', 'wp-webhooks' ), 
					'short_description' => __( 'Add a custom caption to the attachment (Image Caption).', 'wp-webhooks' ),
				),
				'attachment_description' => array( 
					'label' => __( 'Attachment description', 'wp-webhooks' ), 
					'short_description' => __( 'Add a custom description to the attachment (Image Descripiton).', 'wp-webhooks' ),
				),
			);

			ob_start();
			?>
<?php echo __( "Make sure to set a base64-encoded string of an image. It should have the following format (The file type may vary):", 'wp-webhooks' ); ?>
<pre>data:image/png;base64, /9j/4AAQSkZJRgAB....</pre>
			<?php
			$parameter['base64']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "Using the <strong>file_name</strong> argument, you can set a custom file name as otherwise we auto-generate one for you. Please make sure to also include the extension since it tells this webhook what file type to use. Changing the extension also means changing the filetype. E.g.:", 'wp-webhooks' ); ?>
<pre>demo-file.txt</pre>
			<?php
			$parameter['file_name']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "The <strong>add_post_thumbnail</strong> argument allows you to assign the attachment, as a featured image, to one or multiple posts. To use it, simply include a comma-separated list of post IDs as a value. Custom post types are supported as well. E.g.:", 'wp-webhooks' ); ?>
<pre>42,134,251</pre>
			<?php
			$parameter['add_post_thumbnail']['description'] = ob_get_clean();

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(Array) Further details about the action request.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'File successfully created.',
				'data' => 
				array (
				  'path' => NULL,
				  'attach_id' => 9539,
				  'post_info' => NULL,
				  'attachment_metadata' => 
				  array (
					'width' => 144,
					'height' => 144,
					'file' => '2022/12/wpwh-file-2022-12-02-05-37-07.png',
					'sizes' => 
					array (
					  'demo_size_thumbnail' => 
					  array (
						'file' => 'wpwh-file-2022-12-02-05-37-07-100x100.png',
						'width' => 100,
						'height' => 100,
						'mime-type' => 'image/jpeg',
					  ),
					),
					'image_meta' => 
					array (
					  'aperture' => '0',
					  'credit' => '',
					  'camera' => '',
					  'caption' => '',
					  'created_timestamp' => '0',
					  'copyright' => '',
					  'focal_length' => '0',
					  'iso' => '0',
					  'shutter_speed' => '0',
					  'title' => '',
					  'orientation' => '0',
					  'keywords' => 
					  array (
					  ),
					),
				  ),
				  'add_post_thumbnail' => 
				  array (
				  ),
				),
			);

			return array(
				'action'			=> 'create_base64_attachment',
				'name'			  => __( 'Create base64 attachment', 'wp-webhooks' ),
				'sentence'			  => __( 'create an attachment from a base64 string', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Create an attachment from a base64 string using webhooks.', 'wp-webhooks' ),
				'description'	   => array(),
				'integration'	   => 'wordpress',
				'premium' 			=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'path' => null,
					'attach_id' => null,
					'post_info' => null,
				)
			);

			$base64		   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'base64' );
			$file_name	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'file_name' );
			$parent_post_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'parent_post_id' ) );
			$add_post_thumbnail = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'add_post_thumbnail' );
			$attachment_image_alt = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'attachment_image_alt' );
			$attachment_title = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'attachment_title' );
			$attachment_caption = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'attachment_caption' );
			$attachment_description = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'attachment_description' );

			$file_type = false;
			$file_content = false;
			if( ! empty( $base64 ) ){

				if( preg_match( '/^data:image\/(\w+);base64,/', $base64, $type ) ){

					$file_type = strtolower( $type[1] ); // jpg, png, gif
				
					$base64 = preg_replace( '#^data:image/\w+;base64,#i', '', $base64 );
					$base64 = base64_decode( $base64 );
				
					if( $base64 === false ){
						$return_args['msg'] = __( "Decoding the string failed", 'wp-webhooks' );
						return $return_args;
					}

					$file_content = $base64;
				} else {
					$return_args['msg'] = __( "The given data is not a valid base64 string.", 'wp-webhooks' );
					return $return_args;
				}

				if( empty( $file_type ) ){
					$return_args['msg'] = __( "No valid file type was given.", 'wp-webhooks' );
					return $return_args;
				}

				if( empty( $file_name ) ){
					$file_name = apply_filters( 'wpwh/integrations/wordpress/base64_file_name', 'wpwh-file-' . date( 'Y-m-d-H-i-s' ) . '.' . $file_type );
				}

				$upload = wp_upload_bits( $file_name, null, $file_content );
				if( empty( $upload['error'] ) ) {

					if( empty( $parent_post_id ) ){
						$parent_post_id = 0;

					}

					$file_path = $upload['file'];
					$file_type = wp_check_filetype( $file_name, null );
					$org_attachment_title = sanitize_file_name( pathinfo( $file_name, PATHINFO_FILENAME ) );
					$wp_upload_dir = wp_upload_dir();
					$post_info = array(
						'guid'		   => $wp_upload_dir['url'] . '/' . $file_name,
						'post_mime_type' => $file_type['type'],
						'post_title'	 => $org_attachment_title,
						'post_content'   => '',
						'post_status'	=> 'inherit',
					);
					// Create the attachment
					$attach_id = wp_insert_attachment( $post_info, $file_path, $parent_post_id );

					if( ! is_wp_error( $attach_id ) ){

						// Include image.php
						require_once( ABSPATH . 'wp-admin/includes/image.php' );
						// Define attachment metadata
						$attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );
						// Assign metadata to attachment
						wp_update_attachment_metadata( $attach_id,  $attach_data );
						$return_args['data']['attachment_metadata'] = $attach_data;
						
						$return_args['data']['add_post_thumbnail'] = array();
						if( ! empty( $add_post_thumbnail ) ){
							$posts_to_attach = explode( ',', $add_post_thumbnail );
							foreach( $posts_to_attach as $single_post ){
								$single_post = intval( trim( $single_post ) );

								$sub_post_thumb_data = array(
									'post_id' => $single_post,
									'attachment_id' => $attach_id,
									'response' => set_post_thumbnail( $single_post, $attach_id ),
								);

								$return_args['data']['add_post_thumbnail'][] = $sub_post_thumb_data;
							}
						}

						if( ! empty( $attachment_image_alt ) ){
							update_post_meta( $attach_id, '_wp_attachment_image_alt', $attachment_image_alt );
							$return_args['data']['attachment_image_alt'] = $attachment_image_alt;
						}

						$attachment_meta = array();

						if( ! empty( $attachment_title ) ){
							$attachment_meta['post_title'] = $attachment_title;
							$return_args['data']['attachment_title'] = $attachment_title;
						}

						if( ! empty( $attachment_caption ) ){
							$attachment_meta['post_excerpt'] = $attachment_caption;
							$return_args['data']['attachment_caption'] = $attachment_caption;
						}

						if( ! empty( $attachment_description ) ){
							$attachment_meta['post_content'] = $attachment_description;
							$return_args['data']['attachment_description'] = $attachment_description;
						}

						if( ! empty( $attachment_meta ) ){
							$attachment_meta = array_merge( array( 'ID' => $attach_id ), $attachment_meta );
							wp_update_post( $attachment_meta );
						}

						$return_args['data']['attach_id'] = $attach_id;
						$return_args['success'] = true;
						$return_args['msg'] = __( "File successfully created.", 'wp-webhooks' );

					} else {

						$return_args['data']['attach_id'] = $attach_id;
						$return_args['data']['post_info'] = $post_info;
						$return_args['data']['upload_info'] = $upload;
						$return_args['msg'] = __( "An error occured while inserting the file.", 'wp-webhooks' );

					}


				} else {

					$return_args['data']['upload_info'] = $upload;
					$return_args['msg'] = __( "An error occured while uploading the file.", 'wp-webhooks' );

				}

			} else {

				$return_args['msg'] = __( "Please set the base64 argument.", 'wp-webhooks' );

			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.