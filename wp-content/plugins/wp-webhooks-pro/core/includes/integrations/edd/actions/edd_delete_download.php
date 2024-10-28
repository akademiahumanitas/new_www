<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Actions_edd_delete_download' ) ) :

	/**
	 * Load the edd_delete_download action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_edd_Actions_edd_delete_download {

        public function is_active(){

            $is_active = true;

            //Backwards compatibility for the "Easy Digital Downloads" integration
            if( defined( 'WPWH_EDD_NAME' ) ){
                $is_active = false;
            }

            return $is_active;
        }

        public function get_details(){

            $parameter = array(
				'download_id'       => array( 'required' => true, 'short_description' => __( 'The download id of the download you want to delete.', 'wp-webhooks' ) ),
				'force_delete'  	=> array( 
					'type' => 'select',
					'choices' => array(
						'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
					),
					'multiple' => false,
					'default_value' => 'no',
					'short_description' => __( '(optional) Whether to bypass trash and force deletion. Possible values: "yes" and "no". Default: "no".', 'wp-webhooks' ),
				),
				'do_action'     	=> array( 'short_description' => __( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', 'wp-webhooks' ) )
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'        	=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'        		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data'        		=> array( 'short_description' => __( '(array) Within the data array, you will find further details about the response, as well as the download id and further information.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The download was successfully deleted.',
				'data' => 
				array (
				  'post_id' => 747,
				  'force_delete' => false,
				),
			);

			ob_start();
			?>
<?php echo __( "In case you set the <strong>force_delete</strong> argument to <strong>yes</strong>, the download will be completely removed from your WordPress website.", 'wp-webhooks' ); ?>
			<?php
			$parameter['force_delete']['description'] = ob_get_clean();

            return array(
                'action'            => 'edd_delete_download',
                'name'              => __( 'Delete download', 'wp-webhooks' ),
                'sentence'              => __( 'delete a download', 'wp-webhooks' ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => __( 'This webhook action allows you to delete (or trash) a download within Easy Digital Downloads.', 'wp-webhooks' ),
                'description'       => array(),
                'integration'       => 'edd',
                'premium' 			=> false,
            );

        }

        public function execute( $return_data, $response_body ){

            $return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'post_id' => 0,
					'force_delete' => false
				)
			);

			$post_id         = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'download_id' ) );
			$force_delete    = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'force_delete' ) == 'yes' ) ? true : false;
			$do_action       = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' ) ) ? WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' ) : '';
			$post = '';
			$check = '';

			if( ! empty( $post_id ) ){
				$post = get_post( $post_id );
			}

			if( ! empty( $post ) ){
				if( ! empty( $post->ID ) ){

					if( $force_delete ){
						$check = wp_delete_post( $post->ID, $force_delete );
					} else {
						$check = wp_trash_post( $post->ID );
					}

					if ( $check ) {

						if( $force_delete  ){
							$return_args['msg']     = __( "Download successfully deleted.", 'wp-webhooks' );
						} else {
							$return_args['msg']     = __( "Download successfully trashed.", 'wp-webhooks' );
						}
						
						$return_args['success'] = true;
						$return_args['data']['post_id'] = $post->ID;
						$return_args['data']['force_delete'] = $force_delete;
					} else {
						if( $force_delete  ){
							$return_args['msg']  = __( "Error deleting download. Please check wp_delete_post() for more information.", 'wp-webhooks' );
						} else {
							$return_args['msg']  = __( "Error trashing download. Please check wp_trash_post() for more information.", 'wp-webhooks' );
						}
						
						$return_args['data']['post_id'] = $post->ID;
						$return_args['data']['force_delete'] = $force_delete;
					}

				} else {
					$return_args['msg'] = __( "Could not delete the download: No ID given.", 'wp-webhooks' );
				}
			} else {
				$return_args['msg']  = __( "No download found to your specified download id.", 'wp-webhooks' );
				$return_args['data']['post_id'] = $post_id;
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $post, $post_id, $check, $force_delete );
			}

			return $return_args;
            
        }

    }

endif; // End if class_exists check.