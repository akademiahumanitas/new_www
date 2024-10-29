<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Actions_edd_update_license' ) ) :

	/**
	 * Load the edd_update_license action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_edd_Actions_edd_update_license {

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
				'license_id'       => array( 'required' => true, 'short_description' => __( '(Mixed) The license id or the license key of the license you would like to update. Please see the description for further details.', 'wp-webhooks' ) ),
				'download_id'       => array( 'short_description' => __( '(Integer) The id of the download you want to associate with the license. Please see the description for further details.', 'wp-webhooks' ) ),
				'payment_id'       => array( 'short_description' => __( '(Integer) The id of the payment you want to associate with the license. Please see the description for further details.', 'wp-webhooks' ) ),
				'license_key'       => array( 'short_description' => __( '(String) A new license key for the susbcription. Please see the description for further details.', 'wp-webhooks' ) ),
				'price_id'       => array( 'short_description' => __( '(String) In case you work with multiple pricing options (variations) within the same product, please set the pricing id here. Please see the description for further details.', 'wp-webhooks' ) ),
				'cart_index'       => array( 'short_description' => __( '(Integer) The numerical index in the cart items array of the product the license key is associated with. Please see the description for further details.', 'wp-webhooks' ) ),
				'status'       => array( 'short_description' => __( '(String) The status of the given license. Please see the description for further details.', 'wp-webhooks' ) ),
				'parent_license_id'       => array( 'short_description' => __( '(Integer) Set the parent id of this license in case you want to use this license as a child license. Please see the description for further details.', 'wp-webhooks' ) ),
				'activation_limit'       => array( 'short_description' => __( '(Integer) A number representing the amount of possible activations at the same time. set it to 0 for unlimited activations. Please see the description for further details.', 'wp-webhooks' ) ),
				'date_created'       => array( 'short_description' => __( '(String) In case you want to customize the creation date, you can define the date here. Please see the description for further details.', 'wp-webhooks' ) ),
				'expiration_date'       => array( 'short_description' => __( '(String) In case you want to customize the expiration date, you can define the date here. Otherwise it will be calculated based on the added product. Please see the description for further details.', 'wp-webhooks' ) ),
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
				'data'        => array( 'short_description' => __( '(Array) Containing the license id, as well as the license key and other arguments set during the update of the license.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
                'success' => true,
                'msg' => 'The license was successfully updated.',
                'data' => 
                array (
                  'license_id' => 17,
                  'download_id' => 176,
                  'payment_id' => 711,
                  'price_id' => '2',
                  'cart_index' => 0,
                  'license_options' => 
                  array (
                    'download_id' => 176,
                    'payment_id' => 711,
                    'price_id' => '2',
                    'expiration' => 1621690140,
                    'customer_id' => '1',
                    'user_id' => '1',
                  ),
                  'license_meta' => '{
                "meta_5": "test5",
                "meta_6": "ironikus-serialize{\\"test_key\\":\\"wow\\",\\"testval\\":\\"new\\"}"
              }',
                  'license_key' => 'e5e52aa45bb0e7c82a471e8234f6e427',
                  'logs' => '[
                {
                  "title": "Log 5",
                  "message": "This is my description for log 1"
                },
                {
                  "title": "Log 6",
                  "message": "This is my description for log 2",
                  "type": null
                }
              ]',
                ),
            );

            ob_start();
			?>
<?php echo __( "This argument accepts either the numeric license id or the license key that was set for the license. E.g. 4fc336680bf576cc0298777278ceb15a", 'wp-webhooks' ); ?>
			<?php
			$parameter['license_id']['description'] = ob_get_clean();

            ob_start();
			?>
<?php echo __( "The download id of the download (product) you want to relate with the license. Please note that the product needs to have licensing activated.", 'wp-webhooks' ); ?>
			<?php
			$parameter['download_id']['description'] = ob_get_clean();

            ob_start();
			?>
<?php echo __( "The payment id of the payment you want to relate with the license. It will be used to assign the user to the license, as well as the customer.", 'wp-webhooks' ); ?>
			<?php
			$parameter['payment_id']['description'] = ob_get_clean();

            ob_start();
			?>
<?php echo __( "This argument allows you to update the license key for the given license. Alternatively, you can also set the argument value to <strong>regenerate</strong> to automatically regenerte the license key.", 'wp-webhooks' ); ?>
			<?php
			$parameter['license_key']['description'] = ob_get_clean();

            ob_start();
			?>
<?php echo __( "In case you work with pricing options (variations) for your downloads, use this argument to set the pricing id of the variation price. The pricing id is called <strong>Download file ID</strong> on the edit-download page.", 'wp-webhooks' ); ?>
			<?php
			$parameter['price_id']['description'] = ob_get_clean();

            ob_start();
			?>
<?php echo __( "The identifier of the given download within the cart array. You can use this argument to associate the license with a specifc product wiithin the payment.", 'wp-webhooks' ); ?>
			<?php
			$parameter['cart_index']['description'] = ob_get_clean();

            ob_start();
			?>
<?php echo __( "Use this argument to set a parent license for the updated license.", 'wp-webhooks' ); ?>
			<?php
			$parameter['parent_license_id']['description'] = ob_get_clean();

            ob_start();
			?>
<?php echo __( "In case you would like to customize the licensing slots for your license (the amount of wesbites that can be added), you can use this argument. Please set it to e.g. 20 to allow 20 licensing slots. If you set this argument to 0, the license will contain unlimited license slots.", 'wp-webhooks' ); ?>
			<?php
			$parameter['activation_limit']['description'] = ob_get_clean();

            ob_start();
			?>
<?php echo __( "You can use this argument to customize the creation date. It allows you to set most kind of date formats, but we suggest you using the SQL format: 2021-05-25 11:11:11", 'wp-webhooks' ); ?>
			<?php
			$parameter['date_created']['description'] = ob_get_clean();

            ob_start();
			?>
<?php echo __( "You can use this argument to customize the expiration date. It allows you to set most kind of date formats, but we suggest you using the SQL format: 2021-05-25 11:11:11. If you would like to never expire the license, set this argument to <strong>0</strong>.", 'wp-webhooks' ); ?>
			<?php
			$parameter['expiration_date']['description'] = ob_get_clean();

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
					__( "In case you would like to set the license to a lifetime validity, simply set the <strong>expiration_date</strong> argument to <strong>0</strong>.", 'wp-webhooks' ),
				),
			);

            return array(
                'action'            => 'edd_update_license',
                'name'              => __( 'Update license', 'wp-webhooks' ),
                'sentence'              => __( 'update a license', 'wp-webhooks' ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => __( 'This webhook action allows you to update a license within Easy Digital Downloads - Software Licensing.', 'wp-webhooks' ),
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
					'license_key' => 0,
					'download_id' => 0,
					'payment_id' => 0,
					'price_id' => false,
					'cart_index' => 0,
					'license_options' => array(),
					'license_meta' => array(),
				),
			);

			$license_id   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'license_id' );
			$license_key   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'license_key' );
			$download_id   = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'download_id' ) );
			$payment_id   = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'payment_id' ) );
			$price_id   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'price_id' );
			$status   = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'status' ) );
			$cart_index   = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'cart_index' ) );
			$date_created   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'date_created' );
			$parent_license_id   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'parent_license_id' );
			$activation_limit   = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'activation_limit' ) );
			$expiration_date   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'expiration_date' );
			$manage_sites   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'manage_sites' );
			$logs   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'logs' );
			$license_meta   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'license_meta' );
			$license_action   = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'license_action' ) );
			
			$do_action          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( ! class_exists( 'EDD_SL_License' ) ){
				$return_args['msg'] = __( 'The class EDD_SL_License() does not exist. The license was not created.', 'wp-webhooks' );
				return $return_args;
			}

			if( ! class_exists( 'EDD_Payment' ) ){
				$return_args['msg'] = __( 'The class EDD_Payment() does not exist. The license was not created.', 'wp-webhooks' );
				return $return_args;
			}

			if( empty( $license_id ) ){
				$return_args['msg'] = __( 'The license_id argument cannot be empty. The license was not updated.', 'wp-webhooks' );
				return $return_args;
			}
            
            $payment = new EDD_Payment( $payment_id );
            $license = new EDD_SL_License( $license_id );
            
            if( empty( $price_id ) ){
                $price_id = null;
            }

            $license_options = array();

            if( ! empty( $license_key ) && $license_key !== 'regenerate' ){
                $license_options['license_key'] = $license_key;
            }

            if( ! empty( $download_id ) ){
                $license_options['download_id'] = $download_id;
            }

            if( ! empty( $payment_id ) ){
                $license_options['payment_id'] = $payment_id;
            }

            if( ! empty( $price_id ) ){
                $license_options['price_id'] = $price_id;
            }

            if( ! empty( $status ) ){
                $license_options['status'] = $status;
            }

            if( ! empty( $cart_index ) ){
                $license_options['cart_index'] = $cart_index;
            }

            if( ! empty( $date_created ) ){
                $license_options['date_created'] = date("Y-m-d H:i:s", strtotime( $date_created ) );
            }

            if( ! empty( $expiration_date ) ){
                $license_options['expiration'] = strtotime( $expiration_date );
            } else {
                if( intval( $expiration_date ) === 0 ){
                    $license_options['expiration'] = 0; //make it lifetime
                }
            }

            if( ! empty( $parent_license_id ) ){
                $license_options['parent'] = date("Y-m-d H:i:s", strtotime( $parent_license_id ) );
            }

            if( ! empty( $payment ) ){
                $license_options['customer_id'] = $payment->customer_id;
                $license_options['user_id'] = $payment->user_id;
            }

			$check = $license->update( $license_options );

			if( $check ){

				if( $license_key === 'regenerate' ){
					$license->regenerate_key();
				}

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
				
				$new_fetched_license = new EDD_SL_License( $license->ID );

                $license_id = $license->ID;
				$return_args['msg'] = __( "The license was successfully updated.", 'action-edd_update_license-success' );
				$return_args['success'] = true;
				$return_args['data']['license_id'] = $license->ID;
				$return_args['data']['license_key'] = $new_fetched_license->license_key;
				$return_args['data']['download_id'] = $download_id;
				$return_args['data']['payment_id'] = $payment_id;
				$return_args['data']['price_id'] = $price_id;
				$return_args['data']['cart_index'] = $cart_index;
				$return_args['data']['license_options'] = $license_options;
				$return_args['data']['license_meta'] = $license_meta;
				$return_args['data']['logs'] = $logs;
			} else {
				$return_args['msg'] = __( "Error updating the license.", 'action-edd_update_license-success' );
			}
		
			

			if( ! empty( $do_action ) ){
				do_action( $do_action, $license_id, $license, $return_args );
			}

			return $return_args;
    
        }

    }

endif; // End if class_exists check.