<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_gravity_pdf_Actions_gpdf_get_pdf_url' ) ) :

	/**
	 * Load the gpdf_get_pdf_url action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_gravity_pdf_Actions_gpdf_get_pdf_url {

	public function get_details(){

			$parameter = array(
				'pdf_id'		=> array( 
					'required' => true, 
					'label'			=> __( 'PDF ID', 'wp-webhooks' ),
					'short_description' => __( 'The ID of the PDF template. You will find the ID within the shortcodes of the PDF list inside of a form.', 'wp-webhooks' )
				),
				'entry_id'		=> array( 
					'required' => true, 
					'label'			=> __( 'Entry ID', 'wp-webhooks' ),
					'short_description' => __( 'The entry ID of the Gravity Forms entry you want to get the PDF URL for.', 'wp-webhooks' )
				),
				'download'		=> array( 
					'label'			=> __( 'Download URL', 'wp-webhooks' ),
					'type' => 'select',
					'choices' => array(
						'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
					),
					'multiple' => false,
					'default_value' => 'no',
					'short_description' => __( 'Set this argument to "yes" to display the link to download the PDF instead of viewing it within the browser. Default: no', 'wp-webhooks' )
				),
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further data about the fired actions.', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			ob_start();
		?>
<?php echo __( 'To get the ID of a specific PDF template, please head over to Gravity Forms and open the form of your choice. From there, head to the settings and locate the PDF tab. Once there, you can copy the shortcode and paste it somewhere to copy the ID from the id="" argument.', 'wp-webhooks' ); ?>
		<?php
		$parameter['pdf_id']['description'] = ob_get_clean();

		$returns_code = array (
			'success' => true,
			'msg' => 'The URL has been returned successfully.',
			'data' => 
			array (
			  'url' => 'https://yourdomain.test/pdf/62f2104411980/38/',
			),
		  );

		return array(
			'action'			=> 'gpdf_get_pdf_url', //required
			'name'			   => __( 'Get PDF URL', 'wp-webhooks' ),
			'sentence'			   => __( 'get the URL of a PDF', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Get the URL of a PDF within Gravity PDF.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'gravity-pdf',
			'premium'	   	=> true,
		);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'url' => '',
				)
			);

			$pdf_id = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'pdf_id' );
			$entry_id = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'entry_id' );
			$download = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'download' ) === 'yes' ) ? true : false;

			if( empty( $pdf_id ) ){
				$return_args['msg'] = __( "Please set the pdf_id argument.", 'action-gpdf_get_pdf_url-error' );
				return $return_args;
			}

			if( empty( $entry_id ) ){
				$return_args['msg'] = __( "Please set the entry_id argument.", 'action-gpdf_get_pdf_url-error' );
				return $return_args;
			}

			if( ! class_exists( 'GPDFAPI' ) ){
				$return_args['msg'] = __( "The GPDFAPI class does not exist.", 'action-gpdf_get_pdf_url-error' );
				return $return_args;
			}

			$pdf = GPDFAPI::get_mvc_class( 'Model_PDF' );
			$url = $pdf->get_pdf_url( $pdf_id, $entry_id, $download, false );
			
			$return_args['success'] = true;
			$return_args['msg'] = __( "The URL has been returned successfully.", 'action-gpdf_get_pdf_url-success' );
			$return_args['data']['url'] = $url;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.