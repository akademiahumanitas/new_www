<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Actions_edd_create_license' ) ) :

	/**
	 * Load the edd_create_license action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_edd_Actions_edd_create_license {

        public function is_active(){

            $is_active = class_exists( 'EDD_Software_Licensing' );

            //Backwards compatibility for the "Easy Digital Downloads" integration
            if( defined( 'WPWH_EDD_NAME' ) ){
                $is_active = false;
            }

            return $is_active;
        }

        public function get_details(){

			$parameter = array(
				'download_id'       => array( 'required' => true, 'short_description' => __( '(Integer) The id of the download you want to associate with the license. Please see the description for further details.', 'wp-webhooks' ) ),
				'payment_id'       => array( 'required' => true, 'short_description' => __( '(Integer) The id of the payment you want to associate with the license. Please see the description for further details.', 'wp-webhooks' ) ),
				'price_id'       => array( 'short_description' => __( '(String) In case you work with multiple pricing options (variations) within the same product, please set the pricing id here. Please see the description for further details.', 'wp-webhooks' ) ),
				'cart_index'       => array( 'short_description' => __( '(Integer) The numerical index in the cart items array of the product the license key is associated with. Please see the description for further details.', 'wp-webhooks' ) ),
				'existing_license_ids'       => array( 'short_description' => __( '(String) A JSON formatted string of existing license ids. Please see the description for further information.', 'wp-webhooks' ) ),
				'parent_license_id'       => array( 'short_description' => __( '(Integer) Set the parent id of this license in case you want to use this license as a child license. Please see the description for further details.', 'wp-webhooks' ) ),
				'activation_limit'       => array( 'short_description' => __( '(Integer) A number representing the amount of possible activations at the same time. set it to 0 for unlimited activations. Please see the description for further details.', 'wp-webhooks' ) ),
				'license_length'       => array( 'short_description' => __( '(Integer) The length of the license key.', 'wp-webhooks' ) ),
				'expiration_date'       => array( 'short_description' => __( '(String) In case you want to customize the expiration date, you can define the date here. Otherwise it will be calculated based on the added product. Please see the description for further details.', 'wp-webhooks' ) ),
				'is_lifetime'       => array( 
                    'type' => 'select',
					'choices' => array(
						'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
					),
                    'multiple' => false,
					'default_value' => 'no',
                    'short_description' => __( '(String) Set this value to "yes" to mark the license as a lifetime license. Default: no', 'wp-webhooks' ),
                ),
				'manage_sites'       => array( 'short_description' => __( '(String) A JSON formatted string containing one or multiple site urls. Please see the description for further details.', 'wp-webhooks' ) ),
				'logs'       => array( 'short_description' => __( '(String) A JSON formatted string containing one or multiple logs. Please see the description for further details.', 'wp-webhooks' ) ),
				'license_meta'       => array( 'short_description' => __( '(String) A JSON formatted string containing one or multiple meta values. Please see the description for further details.', 'wp-webhooks' ) ),
				'license_action'       => array( 'short_description' => __( '(String) Do additional, native actions using the license. Please see the description for further details.', 'wp-webhooks' ) ),
				'do_action'     => array( 'short_description' => __( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More info is within the description.', 'wp-webhooks' ) ),
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'        => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'        => array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data'        => array( 'short_description' => __( '(Array) Containing the new license id and other arguments set during the creation of the license.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
                    'success' => true,
                    'msg' => 'The license was successfully created.',
                    'data' => 
                    array (
                    'license_id' => 17,
                    'download_id' => 285,
                    'payment_id' => 843,
                    'price_id' => '2',
                    'cart_index' => 0,
                    'license_options' => 
                    array (
                        'activation_limit' => 0,
                        'license_length' => '32',
                        'expiration_date' => 1621654140,
                        'is_lifetime' => true,
                    ),
                    'license_meta' => '{
                    "meta_1": "test1",
                    "meta_2": "ironikus-serialize{\\"test_key\\":\\"wow\\",\\"testval\\":\\"new\\"}"
                    }',
                        'logs' => '[
                        {
                        "title": "Log 1",
                        "message": "This is my description for log 1"
                        },
                        {
                        "title": "Log 2",
                        "message": "This is my description for log 2",
                        "type": null
                        }
                ]',
                ),
            );

            ob_start();
			?>
<?php echo __( "The download id of the download (product) you need to relate with the license. Please note that the product needs to have licensing activated. We will use this download to fetch certain information as expiration, pricing, bundles, etc.", 'wp-webhooks' ); ?>
			<?php
			$parameter['download_id']['description'] = ob_get_clean();

            ob_start();
			?>
<?php echo __( "The payment id of the payment you need to relate with the license. It will be used to assign the payment with the newly created license. EDD also uses this argument to assign the user to the license, as well as the customer.", 'wp-webhooks' ); ?>
			<?php
			$parameter['payment_id']['description'] = ob_get_clean();

            ob_start();
			?>
<?php echo __( "In case you work with pricing options (variations) for your download, please set the pricing id of the variation price you want to use here. The pricing id is called <strong>Download file ID</strong> on the edit-download page.", 'wp-webhooks' ); ?>
			<?php
			$parameter['price_id']['description'] = ob_get_clean();

            ob_start();
			?>
<?php echo __( "The identifier of the given download within the cart array. You can use this argument to associate the license with a specifc product wiithin the payment.", 'wp-webhooks' ); ?>
			<?php
			$parameter['cart_index']['description'] = ob_get_clean();

            ob_start();
			?>
<?php echo __( "Use this argument to add one or multiple, existing licenses to the subscription using the license id. This value accepts a JSON, containing one license id per line. Here is an example:", 'wp-webhooks' ); ?>
<pre>[
  342,
  365
]</pre>
<?php echo __( "The example above adds two licenses.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "<strong>Please note</strong>: Defining ids within this argument causes the added licenses to bbe added as child-licenses (the parent will be set to this license).", 'wp-webhooks' ); ?>
			<?php
			$parameter['existing_license_ids']['description'] = ob_get_clean();

            ob_start();
			?>
<?php echo __( "Use this argument to set a parent license for the newly created license.", 'wp-webhooks' ); ?>
			<?php
			$parameter['parent_license_id']['description'] = ob_get_clean();

            ob_start();
			?>
<?php echo __( "In case you would like to customize the licensing slots for your license (the amount of wesbites that can be added), you can use this argument. Please set it to e.g. 20 to allow 20 licensing slots. If you leave it empty, the values re fetched accordingly from the given download. If you set this argument to 0, the license will contain unlimited license slots.", 'wp-webhooks' ); ?>
			<?php
			$parameter['activation_limit']['description'] = ob_get_clean();

            ob_start();
			?>
<?php echo __( "The length of the license key itself. In case you set this argument to 64, the license key will look as followed:", 'wp-webhooks' ); ?>
<pre>d96ef9c6e8d4259c11bf5f7bad4f6d67232daddee75cced747de0aed7d2d6c99</pre>
			<?php
			$parameter['license_length']['description'] = ob_get_clean();

            ob_start();
			?>
<?php echo __( "You can use this argument to customize the expiration date. It allows you to set most kind of date formats, but we suggest you using the SQL format: 2021-05-25 11:11:11", 'wp-webhooks' ); ?>
			<?php
			$parameter['expiration_date']['description'] = ob_get_clean();

            ob_start();
			?>
<?php echo __( "Set the value to <strong>yes</strong> to never expire this license. Default: <strong>no</strong>. Please note that setting this argument to <strong>yes</strong> will ignore the expiration date.", 'wp-webhooks' ); ?>
			<?php
			$parameter['is_lifetime']['description'] = ob_get_clean();

            ob_start();
			?>
<?php echo __( "Use this argument to add and/or remove sites on a license. It accepts a JSON formatted string containg the site URLs. Here is an example:", 'wp-webhooks' ); ?>
<pre>[
  "https://demo.com",
  "https://demo.demo",
  "remove:https://demo3.demo"
]</pre>
<?php echo __( "The example above adds two new site URLs. It also removes one site URL. To remove a site URL, please place <strong>remove:</strong> in front of the site URL.", 'wp-webhooks' ); ?>
			<?php
			$parameter['manage_sites']['description'] = ob_get_clean();

            ob_start();
			?>
<?php echo __( "Use this argument to add one or multiple log entries to the license. This value accepts a JSON formated string. Here is an example:", 'wp-webhooks' ); ?>
<pre>[
  {
    "title": "Log 1",
    "message": "This is my description for log 1"
  },
  {
    "title": "Log 2",
    "message": "This is my description for log 2",
    "type": null
  }
]</pre>
<?php echo __( "The example above adds two logs. The <strong>type</strong> key can contain a single term slug, single term id, or array of either term slugs or ids. For further details on the type key, please check out the \$terms variable within the wp_set_object_terms() function:", 'wp-webhooks' ); ?>
<a href="https://developer.wordpress.org/reference/functions/wp_set_object_terms/" target="_blank">https://developer.wordpress.org/reference/functions/wp_set_object_terms/</a>
			<?php
			$parameter['logs']['description'] = ob_get_clean();

            ob_start();
			?>
<?php echo __( "This argument allows you to add/update or remove one or multiple license meta values to your newly created license, using a JSON string. Easy Digital Downloads uses a custom table for these meta values. Here are some examples on how you can use it:", 'wp-webhooks' ); ?>
<ul class="list-group list-group-flush">
    <li class="list-group-item">
        <strong><?php echo __( "Add/update meta values", 'wp-webhooks' ); ?></strong>
        <br>
        <?php echo __( "This JSON shows you how to add simple meta values for your license.", 'wp-webhooks' ); ?>
        <pre>{
  "meta_1": "test1",
  "meta_2": "test2"
}</pre>
        <?php echo __( "The key is always the license meta key. On the right, you always have the value for the license meta value. In this example, we add two meta values to the license meta. In case a meta key already exists, it will be updated.", 'wp-webhooks' ); ?>
    </li>
    <li class="list-group-item">
        <strong><?php echo __( "Delete meta values", 'wp-webhooks' ); ?></strong>
        <br>
        <?php echo __( "You can also delete existing meta key by setting the value to <strong>ironikus-delete</strong>. This way, the meta will be removed. Here is an example:", 'wp-webhooks' ); ?>
        <pre>{
  "meta_1": "test1",
  "meta_2": "ironikus-delete"
}</pre>
        <?php echo __( "The example above will add the meta key <strong>meta_1</strong> with the value <strong>test1</strong> and it deletes the meta key <strong>meta_2</strong> including its value.", 'wp-webhooks' ); ?>
    </li>
    <li class="list-group-item">
        <strong><?php echo __( "Add/update/remove serialized meta values", 'wp-webhooks' ); ?></strong>
        <br>
        <?php echo __( "Sometimes, it is necessary to add serialized arrays to your data. Using the json below, you can do exactly that. You can use a simple JSON string as the meta value and we automatically convert it to a serialized array once you place the identifier <strong>ironikus-serialize</strong> in front of it. Here is an example:", 'wp-webhooks' ); ?>
        <pre>{
  "meta_1": "test1",
  "meta_2": "ironikus-serialize{\"test_key\":\"wow\",\"testval\":\"new\"}"
}</pre>
        <?php echo __( "This example adds a simple meta with <strong>meta_1</strong> as the key and <strong>test1</strong> as the value. The second meta value contains a json value with the identifier <strong>ironikus-serialize</strong> in the front. Once this value is saved to the database, it gets turned into a serialized array. In this example, it would look as followed: ", 'wp-webhooks' ); ?>
        <pre>a:2:{s:8:"test_key";s:3:"wow";s:7:"testval";s:3:"new";}</pre>
    </li>
</ul>
			<?php
			$parameter['license_meta_arr']['description'] = ob_get_clean();

            ob_start();
			?>
<?php echo __( "This argument allows you to fire further native features of the licensing class. Please find further details down below:", 'wp-webhooks' ); ?>
<ul class="list-group list-group-flush">
    <li class="list-group-item">
        <strong><?php echo __( "Enable licenses", 'wp-webhooks' ); ?></strong>
        <br>
        <?php echo __( "This value allows you to enable the license and all of its child licenses. It does it by checking on the activation count and if some sites are active, it will set the license to <strong>active</strong>, otherwise it will set it to <strong>inactive</strong>.", 'wp-webhooks' ); ?>
        <pre>enable</pre>
    </li>
    <li class="list-group-item">
        <strong><?php echo __( "Disable licenses", 'wp-webhooks' ); ?></strong>
        <br>
        <?php echo __( "This value allows you to disable the license and all of its child licenses. It will set the license to <strong>disabled</strong>.", 'wp-webhooks' ); ?>
        <pre>disable</pre>
    </li>
</ul>
			<?php
			$parameter['license_action']['description'] = ob_get_clean();

			$description = array(
				'tipps' => array(
					__( "This webhook creates the license by default with the status <strong>inactive</strong>, which will automatically switch to <strong>active</strong> once the user activates his first site.", 'wp-webhooks' ),
                    __( "Please note that the download you would like to connect, must have licensing activated within the product. Otherwise we throw an error.", 'wp-webhooks' ),
				),
			);

            return array(
                'action'            => 'edd_create_license',
                'name'              => __( 'Create license', 'wp-webhooks' ),
                'sentence'              => __( 'create a license', 'wp-webhooks' ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => __( 'This webhook action allows you to create a license within Easy Digital Downloads - Software Licensing.', 'wp-webhooks' ),
                'description'       => $description,
                'integration'       => 'edd',
                'premium' 			=> false,
            );

        }

        public function execute( $return_data, $response_body ){

            $license_id = 0;
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'license_id' => 0,
					'download_id' => 0,
					'payment_id' => 0,
					'price_id' => false,
					'cart_index' => 0,
					'license_options' => array(),
					'license_meta' => array(),
				),
			);

			$download_id   = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'download_id' ) );
			$payment_id   = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'payment_id' ) );
			$price_id   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'price_id' );
			$cart_index   = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'cart_index' ) );
			$existing_license_ids   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'existing_license_ids' );
			$parent_license_id   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'parent_license_id' );
			$activation_limit   = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'activation_limit' ) );
			$license_length   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'license_length' );
			$expiration_date   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'expiration_date' );
			$is_lifetime   = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'is_lifetime' ) === 'yes' ) ? true : false;
			$manage_sites   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'manage_sites' );
			$logs   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'logs' );
			$license_meta   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'license_meta' );
			$license_action   = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'license_action' ) );
			
			$do_action          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( ! class_exists( 'EDD_SL_License' ) ){
				$return_args['msg'] = __( 'The class EDD_SL_License() does not exist. The license was not created.', 'wp-webhooks' );
				return $return_args;
			}

			if( ! class_exists( 'EDD_SL_Download' ) ){
				$return_args['msg'] = __( 'The class EDD_SL_Download() does not exist. The license was not created.', 'wp-webhooks' );
				return $return_args;
			}

			if( empty( $download_id ) ){
				$return_args['msg'] = __( 'The download_id argument cannot be empty. The license was not created.', 'wp-webhooks' );
				return $return_args;
			}

			if( empty( $payment_id ) ){
				$return_args['msg'] = __( 'The payment_id argument cannot be empty. The license was not created.', 'wp-webhooks' );
				return $return_args;
            }
            
            $purchased_download   = new EDD_SL_Download( $download_id );
            if( ! $purchased_download->licensing_enabled() ){
                $return_args['msg'] = __( 'The download given within the download_id argument has no licensing activated within the product. The license was not created.', 'wp-webhooks' );
				return $return_args;
            }

            $license = new EDD_SL_License();
            
            if( empty( $price_id ) ){
                $price_id = false;
            }

            $license_options = array();

            if( ! empty( $existing_license_ids ) ){
                if( WPWHPRO()->helpers->is_json( $existing_license_ids ) ){
                    $existing_license_ids_arr = json_decode( $existing_license_ids, true );
                    if( is_array( $existing_license_ids_arr ) && ! empty( $existing_license_ids_arr ) ){
                        $license_options['existing_license_ids'] = $existing_license_ids_arr;
                    }
                }
            }

            if( ! empty( $parent_license_id ) ){
                $license_options['parent_license_id'] = $parent_license_id;
            }

            if( ! empty( $activation_limit ) || $activation_limit === 0 ){
                $license_options['activation_limit'] = $activation_limit;
            }

            if( ! empty( $license_length ) ){
                $license_options['license_length'] = $license_length;
            }

            if( ! empty( $expiration_date ) ){
                $license_options['expiration_date'] = strtotime( $expiration_date );
            }

            if( ! empty( $is_lifetime ) ){
                $license_options['is_lifetime'] = $is_lifetime;
            }

			$check = $license->create( $download_id, $payment_id, $price_id, $cart_index, $license_options );

			if( $check ){

                //Make sure we set again the activation limit since by default it was not set properly
                if( ! empty( $activation_limit ) || $activation_limit === 0 ){
                    $license->update_meta( '_edd_sl_limit', $activation_limit );
                }

                if( ! empty( $logs ) ){
					if( WPWHPRO()->helpers->is_json( $logs ) ){
						$logs_arr = json_decode( $logs, true );
						foreach( $logs_arr as $slog ){

                            $title = WPWHPRO()->settings->get_page_title();
                            if( isset( $slog['title'] ) && ! empty( $slog['title'] ) ){
                                $title = $slog['title'];
                            }

                            $message = '';
                            if( isset( $slog['message'] ) && ! empty( $slog['message'] ) ){
                                $message = $slog['message'];
                            }

                            $type = null;
                            if( isset( $slog['type'] ) && ! empty( $slog['type'] ) ){
                                $type = $slog['type'];
                            }

							$license->add_log( $title, $message, $type );
						}
					}
				}

                if( ! empty( $manage_sites ) ){
                    if( WPWHPRO()->helpers->is_json( $manage_sites ) ){
                        $manage_sites_arr = json_decode( $manage_sites, true );
                        foreach( $manage_sites_arr as $site ){

                            $ident = 'remove:';
                            if( is_string( $site ) && substr( $site , 0, strlen( $ident ) ) === $ident ){
                                $saction = 'remove';
                                $site = str_replace( $ident, '', $site );
                            } else {
                                $saction = 'add';
                            }

                            switch( $saction ){
                                case 'remove':
                                    $license->remove_site( $site );
                                break;
                                case 'add':
                                default: 
                                    $license->add_site( $site );
                                break;
                            }
                        }
                    }
                }

                if( ! empty( $license_meta ) ){
                    if( WPWHPRO()->helpers->is_json( $license_meta ) ){
                        $license_meta_arr = json_decode( $license_meta, true );
                        foreach( $license_meta_arr as $skey => $sval ){

                            if( ! empty( $skey ) ){
                                if( $sval == 'ironikus-delete' ){
                                    $license->delete_meta( $skey );
                                } else {
                                    $ident = 'ironikus-serialize';
                                    if( is_string( $sval ) && substr( $sval , 0, strlen( $ident ) ) === $ident ){
                                        $serialized_value = trim( str_replace( $ident, '', $sval ),' ' );

                                        if( WPWHPRO()->helpers->is_json( $serialized_value ) ){
                                            $serialized_value = json_decode( $serialized_value );
                                        }

                                        $license->update_meta( $skey, $serialized_value );

                                    } else {
                                        $license->update_meta( $skey, maybe_unserialize( $sval ) );
                                    }
                                }
                            }
                        }
                    }
				}
				
				if( ! empty( $license_action ) ){
					switch( $license_action ){
						case 'enable':
							$license->enable();
						break;
						case 'disable':
							$license->disable();
						break;
					}
				}

                $license_id = $license->ID;
				$return_args['msg'] = __( "The license was successfully created.", 'action-edd_create_license-success' );
				$return_args['success'] = true;
				$return_args['data']['license_id'] = $license->ID;
				$return_args['data']['license_key'] = isset( $license->license_key ) ? $license->license_key : '';
				$return_args['data']['download_id'] = $download_id;
				$return_args['data']['payment_id'] = $payment_id;
				$return_args['data']['price_id'] = $price_id;
				$return_args['data']['cart_index'] = $cart_index;
				$return_args['data']['license_options'] = $license_options;
				$return_args['data']['license_meta'] = $license_meta;
				$return_args['data']['logs'] = $logs;
			} else {
				$return_args['msg'] = __( "Error creating the license.", 'action-edd_create_license-success' );
			}
		
			

			if( ! empty( $do_action ) ){
				do_action( $do_action, $license_id, $license, $return_args );
			}

			return $return_args;
    
        }

    }

endif; // End if class_exists check.