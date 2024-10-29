<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Actions_edd_create_download' ) ) :

	/**
	 * Load the edd_create_download action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_edd_Actions_edd_create_download {

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
				'price'           => array( 'short_description' => __( '(float) The price of the download you want to use. Format: 19.99', 'wp-webhooks' ) ),
				'is_variable_pricing'           => array( 
					'type' => 'select',
					'choices' => array(
						'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
					),
					'multiple' => false,
					'default_value' => 'no',
					'short_description' => __( '(string) Set this value to "yes" if you want to activate variable pricing for this product. Default: no', 'wp-webhooks' ),
				),
				'variable_prices'           	=> array( 'short_description' => __( '(string) A JSON formatted string, containing all of the variable product prices. Please see the description for further details.', 'wp-webhooks' ) ),
				'default_price_id'           	=> array( 'short_description' => __( '(integer) The ID of the price variation you want to use as the default price.', 'wp-webhooks' ) ),
				'download_files'          		=> array( 'short_description' => __( '(String) A JSON formatted string containing all of the downloable file. Please see the description for further details.', 'wp-webhooks' ) ),
				'bundled_products'           	=> array( 'short_description' => __( '(String) A JSON formatted string, containing all of the bundled products. Please see the description for further details.', 'wp-webhooks' ) ),
				'bundled_products_conditions'   => array( 'short_description' => __( '(String) A JSON formatted string that contains the price dependencies. Please see the description for further details.', 'wp-webhooks' ) ),
				'increase_earnings'           	=> array( 'short_description' => __( '(Float) The price you would like to increase the lifetime earnings of this product. Please see the description for further details.', 'wp-webhooks' ) ),
				'decrease_earnings'           	=> array( 'short_description' => __( '(Float) The price you would like to decrease the lifetime earnings of this product. Please see the description for further details.', 'wp-webhooks' ) ),
				'increase_sales'           		=> array( 'short_description' => __( '(Integer) Increase the number of sales from a statistical point of view. Please see the description for further details.', 'wp-webhooks' ) ),
				'decrease_sales'           		=> array( 'short_description' => __( '(Integer) Decrease the number of sales from a statistical point of view. Please see the description for further details.', 'wp-webhooks' ) ),
				'hide_purchase_link'           	=> array(
					'type' => 'select',
					'choices' => array(
						'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
					),
					'multiple' => false,
					'default_value' => 'no',
					'short_description' => __( '(String) Set this string to "yes" to hide the purchase button under the download. Please see the description for more details.', 'wp-webhooks' ),
				),
				'download_limit'           		=> array( 'short_description' => __( '(Integer) Limits how often a customer can globally download the purchase. Please see the description for further details.', 'wp-webhooks' ) ),
				'download_author'           	=> array( 'short_description' => __( '(mixed) The ID or the email of the user who added the post. Default is the current user ID.', 'wp-webhooks' ) ),
				'download_date'             	=> array( 'short_description' => __( '(string) The date of the post. Default is the current time. Format: 2018-12-31 11:11:11', 'wp-webhooks' ) ),
				'download_date_gmt'         	=> array( 'short_description' => __( '(string) The date of the post in the GMT timezone. Default is the value of $post_date.', 'wp-webhooks' ) ),
				'download_content'          	=> array( 'short_description' => __( '(string) The post content. Default empty.', 'wp-webhooks' ) ),
				'download_content_filtered' 	=> array( 'short_description' => __( '(string) The filtered post content. Default empty.', 'wp-webhooks' ) ),
				'download_title'            	=> array( 'short_description' => __( '(string) The post title. Default empty.', 'wp-webhooks' ) ),
				'download_excerpt'          	=> array( 'short_description' => __( '(string) The post excerpt. Default empty.', 'wp-webhooks' ) ),
				'download_status'           	=> array( 'short_description' => __( '(string) The post status. Default \'draft\'.', 'wp-webhooks' ) ),
				'comment_status'        		=> array( 'short_description' => __( '(string) Whether the post can accept comments. Accepts \'open\' or \'closed\'. Default is the value of \'default_comment_status\' option.', 'wp-webhooks' ) ),
				'ping_status'           		=> array( 'short_description' => __( '(string) Whether the post can accept pings. Accepts \'open\' or \'closed\'. Default is the value of \'default_ping_status\' option.', 'wp-webhooks' ) ),
				'download_password'         	=> array( 'short_description' => __( '(string) The password to access the post. Default empty.', 'wp-webhooks' ) ),
				'download_name'             	=> array( 'short_description' => __( '(string) The post name. Default is the sanitized post title when creating a new post.', 'wp-webhooks' ) ),
				'to_ping'               		=> array( 'short_description' => __( '(string) Space or carriage return-separated list of URLs to ping. Default empty.', 'wp-webhooks' ) ),
				'pinged'                		=> array( 'short_description' => __( '(string) Space or carriage return-separated list of URLs that have been pinged. Default empty.', 'wp-webhooks' ) ),
				'download_parent'           	=> array( 'short_description' => __( '(int) Set this for the post it belongs to, if any. Default 0.', 'wp-webhooks' ) ),
				'menu_order'            		=> array( 'short_description' => __( '(int) The order the post should be displayed in. Default 0.', 'wp-webhooks' ) ),
				'download_mime_type'        	=> array( 'short_description' => __( '(string) The mime type of the post. Default empty.', 'wp-webhooks' ) ),
				'guid'                  		=> array( 'short_description' => __( '(string) Global Unique ID for referencing the post. Default empty.', 'wp-webhooks' ) ),
				'download_category'         	=> array( 'short_description' => __( '(string) A comma separated list of category names, slugs, or IDs. Defaults to value of the \'default_category\' option. Example: cat_1,cat_2,cat_3', 'wp-webhooks' ) ),
				'tags_input'            		=> array( 'short_description' => __( '(string) A comma separated list of tag names, slugs, or IDs. Default empty.', 'wp-webhooks' ) ),
				'tax_input'             		=> array( 'short_description' => __( '(string) A simple or JSON formatted string containing existing taxonomy terms. Default empty. More details within the description.', 'wp-webhooks' ) ),
				'meta_input'            		=> array( 'short_description' => __( '(string) A json or a comma and semicolon separated list of post meta values keyed by their post meta key. Default empty. More info in the description.', 'wp-webhooks' ) ),
				'wp_error'              		=> array( 
					'type' => 'select',
					'choices' => array(
						'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
					),
					'multiple' => false,
					'default_value' => 'no',
					'short_description' => __( 'Whether to return a WP_Error on failure. Posible values: "yes" or "no". Default value: "no".', 'wp-webhooks' ),
				),
				'do_action'             		=> array( 'short_description' => __( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', 'wp-webhooks' ) )
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'       => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'        	=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data'        	=> array( 'short_description' => __( '(array) Within the data array, you will find further details about the response, as well as the payment id and further information.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'Download successfully created',
				'data' => 
				array (
				  'download_id' => 797,
				  'download_data' => 
				  array (
					'post_type' => 'download',
					'meta_data' => '{
				"meta_key_1": "This is my meta value 1",
				"another_meta_key": "This is my second meta key!",
				"third_meta_key": "ironikus-serialize{\\"price\\": \\"100\\"}"
			  }',
					'tax_input' => false,
					'edd' => 
					array (
					  'increase_earnings' => '25.49',
					  'decrease_earnings' => false,
					  'increase_sales' => '15',
					  'decrease_sales' => false,
					  'edd_price' => '11.11',
					  'is_variable_pricing' => 1,
					  'edd_variable_prices' => '{
				  "1": {
					  "index": "1",
					  "name": "Variation 1",
					  "amount": "39.90",
					  "license_limit": "0",
					  "is_lifetime": "1"
				  },
				  "2": {
					  "index": "2",
					  "name": "Variation 2",
					  "amount": "49.90",
					  "license_limit": "4"
				  }
			  }',
					  'default_price_id' => '2',
					  'edd_download_files' => '{
				  "1": {
					  "index": "0",
					  "attachment_id": "",
					  "thumbnail_size": "",
					  "name": "wp-webhooks-pro-",
					  "file": "https:\\/\\/domain.demo\\/wp-content\\/uploads\\/edd\\/2020\\/02\\/wp-webhooks-pro.zip",
					  "condition": "all"
				  }
			  }',
					  'edd_bundled_products' => false,
					  'bundled_products_conditions' => false,
					  'hide_purchase_link' => 'on',
					  'download_limit' => 45,
					),
				  ),
				  'edd' => 
				  array (
				  ),
				),
			);

            $default_download_variations = array(
                '1' => array(
                    'index' => '1',
                    'name' => 'Variation Name',
                    'amount' => '39.90',
                    'license_limit' => '0',
                    'is_lifetime' => '1',
                ),
                '2' => array(
                    'index' => '2',
                    'name' => 'Variation Name',
                    'amount' => '49.90',
                    'license_limit' => '4',
                ),
            );
            $default_download_variations = apply_filters( 'wpwh/descriptions/actions/edd_create_download/default_download_variations', $default_download_variations );
            
            $beautified_download_variations = json_encode( $default_download_variations, JSON_PRETTY_PRINT );
            
            $default_download_files = array(
                "1" => 
                array (
                  'index' => '0',
                  'attachment_id' => '177',
                  'thumbnail_size' => 'false',
                  'name' => 'wp-webhooks.2.0.5',
                  'file' => 'https://downloads.wordpress.org/plugin/wp-webhooks.2.0.5.zip',
                  'condition' => 'all',
                ),
                "2" => 
                array (
                  'index' => '',
                  'attachment_id' => '184',
                  'thumbnail_size' => 'false',
                  'name' => 'wp-webhooks.2.0.5',
                  'file' => 'https://downloads.wordpress.org/plugin/wp-webhooks.2.0.5.zip',
                  'condition' => '2',
                )
              );
            $default_download_files = apply_filters( 'wpwh/descriptions/actions/edd_create_download/default_download_files', $default_download_files );
            
            $beautified_download_files = json_encode( $default_download_files, JSON_PRETTY_PRINT );
            
            $default_bundled_products = array(
                "285_1",
                "23"
              );
            $default_bundled_products = apply_filters( 'wpwh/descriptions/actions/edd_create_download/default_bundled_products', $default_bundled_products );
            
            $beautified_bundled_products = json_encode( $default_bundled_products, JSON_PRETTY_PRINT );
            
            $default_bundled_products_conditions = array(
                "1" => "all",
                "2" => "2"
              );
            $default_bundled_products_conditions = apply_filters( 'wpwh/descriptions/actions/edd_create_download/bundled_products_conditions', $default_bundled_products_conditions );
            
            $beautified_bundled_products_conditions = json_encode( $default_bundled_products_conditions, JSON_PRETTY_PRINT );
            
			ob_start();
			?>
<?php echo __( "The default price of the Easy Digital Downloads product. Please set it in the following format: <strong>19.99</strong>", 'wp-webhooks' ); ?>
			<?php
			$parameter['price']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "In case you set this value to <strong>yes</strong>, the default <strong>edd_price</strong> is ignored.", 'wp-webhooks' ); ?>
			<?php
			$parameter['is_variable_pricing']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "This argument accepts a JSON formatted string of the variations you want to add. Down below, you will find an example, containing all values you can set.", 'wp-webhooks' ); ?>
<pre><?php echo $beautified_download_variations; ?></pre>
<?php echo __( "Here is a more detailed explanation for each of the values.", 'wp-webhooks' ); ?>
<br>
<ol>
    <li><?php echo __( "The number in front of each entry and the index should always be the same. They identify which variation is which one. It has to be always a numeric string.", 'wp-webhooks' ); ?></li>
    <li><?php echo __( "The <strong>name</strong> argument defined the name of the variation.", 'wp-webhooks' ); ?></li>
    <li><?php echo __( "The <strong>amount</strong> argument defines the price of the variation. Please use the following format: 19.99.", 'wp-webhooks' ); ?></li>
    <li><?php echo __( "The <strong>license_limit</strong> accepts a number to limit the amount of license slots created. 0 is unlimited.", 'wp-webhooks' ); ?></li>
    <li><?php echo __( "The <strong>is_lifetime</strong> argument is optional. Set it to 1 to make it lifetime.", 'wp-webhooks' ); ?></li>

    <?php do_action( 'wpwh/descriptions/actions/edd_create_download/after_edd_variable_prices_items', $default_download_variations ); ?>

</ol>
			<?php
			$parameter['variable_prices']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "The default price id of the variation you want to set as the fault. You only need to set this value in case you set <strong>is_variable_pricing</strong> to yes.", 'wp-webhooks' ); ?>
			<?php
			$parameter['default_price_id']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "This argument accepts a JSON formatted string of the downloadable files you want to add to the download. Down below, you will find an example, containing all values you can set.", 'wp-webhooks' ); ?>
<pre><?php echo $beautified_download_files; ?></pre>
<?php echo __( "Here is a more detailed explanation for each of the values.", 'wp-webhooks' ); ?>
<br>
<ol>
    <li><?php echo __( "The number in front of each entry and the index are the identifier of the file. They identify which downloadable file is which one. It has to be always a numeric string.", 'wp-webhooks' ); ?></li>
    <li><?php echo __( "The <strong>attachment_id</strong> argument can contain an attachment id (in case the download you add is available within the WordPress media library).", 'wp-webhooks' ); ?></li>
    <li><?php echo __( "The <strong>thumbnail_size</strong> argument can contain a specific thumbnail size (in case the download you add is available within the WordPress media library and contains the thumbnail size you defined).", 'wp-webhooks' ); ?></li>
    <li><?php echo __( "The <strong>name</strong> argument defines the name of the file (without the extension).", 'wp-webhooks' ); ?></li>
    <li><?php echo __( "The <strong>file</strong> argument defines the full, downloadable file URL.", 'wp-webhooks' ); ?></li>
    <li><?php echo __( "The <strong>condition</strong> argument defines if you want to enable this file only for a specific variation or not. Set <strong>all</strong> if you want to make it available for all variations. Otherwise, please use the index id.", 'wp-webhooks' ); ?></li>

    <?php do_action( 'wpwh/descriptions/actions/edd_create_download/after_edd_download_files_items', $default_download_files ); ?>

</ol>
			<?php
			$parameter['download_files']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "This argument allows you to create download bundles. This argument accepts a JSON formatted string of the downloadads you want to bundle. You can also target only a specific variation of a product by defining the variation id, separated by an underscore.", 'wp-webhooks' ); ?>
<pre><?php echo $beautified_bundled_products; ?></pre>
<?php echo __( "Here is a more detailed explanation for each of the values.", 'wp-webhooks' ); ?>
<br>
<ol>
    <li><?php echo __( "Each line contains one download id.", 'wp-webhooks' ); ?></li>
    <li><?php echo __( "On the first line, we add only the first variation of the download with the id 285.", 'wp-webhooks' ); ?></li>
    <li><?php echo __( "The second line adds the full download with the id 23.", 'wp-webhooks' ); ?></li>

    <?php do_action( 'wpwh/descriptions/actions/edd_create_download/after_edd_bundled_products', $default_bundled_products ); ?>

</ol>
			<?php
			$parameter['bundled_products']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "This argument accepts a JSON formatted string of the downloadads you want to bundle. It contains further definitions on which price assignment should be given for which download.", 'wp-webhooks' ); ?>
<pre><?php echo $beautified_bundled_products_conditions; ?></pre>
<?php echo __( "Here is a more detailed explanation for each of the values.", 'wp-webhooks' ); ?>
<br>
<ol>
    <li><?php echo __( "The first argument contains the index id of the bundled product from the <strong>bundled_products</strong> argument. The value contains the Price assignment for your given variation.", 'wp-webhooks' ); ?></li>
    <li><?php echo __( "The second line adds the second bundled product with the price assigment of the second variation.", 'wp-webhooks' ); ?></li>
    
    <?php do_action( 'wpwh/descriptions/actions/edd_create_download/after_edd_bundled_products_conditions', $default_bundled_products_conditions ); ?>

</ol>
			<?php
			$parameter['bundled_products_conditions']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "This argument allows you to increase the lifetime earnings for this product. In case the product does not have any lifetime earnings yet and you set this value to 25.00, then the lifetime earnings will be 0 + 25.00 = <strong>25</strong> $ (or the currency you set by default).", 'wp-webhooks' ); ?>
			<?php
			$parameter['increase_earnings']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "This argument allows you to decrease the lifetime earnings for this product. In case the product has lifetime earnings of 100$ and you set this value to 25.00, then the lifetime earnings will be 100 - 25.00 = <strong>75</strong> $ (or the currency you set by default).", 'wp-webhooks' ); ?>
			<?php
			$parameter['decrease_earnings']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "Increase the number of sales that have been made for this product. In case you set it to 5, it will add five sales to the lifetime sales of the product (it only increases the number, no payments or anything else is added).", 'wp-webhooks' ); ?>
			<?php
			$parameter['increase_sales']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "Decrease the number of sales that have been made for this product. In case you set it to 5, it will remove five sales from the lifetime sales of the product (it only decreases the number, no payments or anything else are removed).", 'wp-webhooks' ); ?>
			<?php
			$parameter['decrease_sales']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "By default, the purchase buttons will be displayed at the bottom of the download, when disabled you will need to use the Purchase link shortcode to output the ability to buy the product where you prefer. To hide the link, set this value to <strong>yes</strong>", 'wp-webhooks' ); ?>
			<?php
			$parameter['hide_purchase_link']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "Limit the number of times a customer who purchased this product can access their download links. This is a global limit. If you want to set different limits for variations, please do that within the <strong>variable_prices</strong> argument.", 'wp-webhooks' ); ?>
			<?php
			$parameter['download_limit']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "The download author argument accepts either the user id of a user, or the email address of an existing user. In case you choose the email adress, we try to match it with the users on your WordPress site. In case we couldn't find a user for the given email, we leave the field empty.", 'wp-webhooks' ); ?>
			<?php
			$parameter['download_author']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "The download content is the main content area of the download. It can contain HTML or any other kind of content necessary for your functionality.", 'wp-webhooks' ); ?>
			<?php
			$parameter['download_content']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "The download status defines further details about how your download will be treated. By default, WordPress offers the following download statuses: <strong>draft, pending, private, publish</strong>. Please note that other plugins can extend the download status values to offer a bigger variety.", 'wp-webhooks' ); ?>
			<?php
			$parameter['download_status']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "This argument supports the default tags_input variable of the <strong>wp_insert_post()</strong> function. Please use this function only if you are known to its functionality since WordPress might not add the values properly due to permissions. If you are not sure, please use the <strong>tax_input</strong> argument instead.", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Here is an example:", 'wp-webhooks' ); ?>
<pre>342,5678,2</pre>
<?php echo __( "This argument supports a comma separated list of tag names, slugs, or IDs.", 'wp-webhooks' ); ?>
			<?php
			$parameter['tags_input']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "This argument allows you to add/append/delete any kind of taxonomies on your download. It uses a custom functionality that adds the taxonomies independently of the <strong>wp_update_post()</strong> function.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "To make it work, we offer certain different features and methods to make the most out of the taxonomy management. Down below, you will find further information about the whole functionality.", 'wp-webhooks' ); ?>
<ol>
    <li>
        <strong><?php echo __( "String method", 'wp-webhooks' ); ?></strong>
        <br>
        <?php echo __( "This method allows you to add/update/delete or bulk manage the download taxonomies using a simple string. Both the string and the JSON method support custom taxonomies too. In case you use more complex taxonomies that use semicolons or double points within the slugs, you need to use the JSON method.", 'wp-webhooks' ); ?>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <strong><?php echo __( "Replace existing taxonomy items", 'wp-webhooks' ); ?></strong>
                <br>
                <?php echo __( "This method allows you to replace already existing taxonomy items on the download. In case a taxonomy item does not exists at the point you want to add it, it will be ignored.", 'wp-webhooks' ); ?>
                <pre>taxonomy_1,tax_item_1:tax_item_2:tax_item_3;taxonomy_2,tax_item_5:tax_item_7:tax_item_8</pre>
                <?php echo __( "To separate the taxonomies from the single taxonomy items, please use a comma \",\". In case you want to add multiple items per taxonomy, you can separate them via a double point \":\". To separate multiple taxonomies from each other, please separate them with a semicolon \";\" (It is not necessary to set a semicolon at the end of the last one)", 'wp-webhooks' ); ?>
            </li>
            <li class="list-group-item">
                <strong><?php echo __( "Remove all taxonomy items for a single taxonomy", 'wp-webhooks' ); ?></strong>
                <br>
                <?php echo __( "In case you want to remove all taxonomy items from one or multiple taxonomies, you can set <strong>ironikus-remove-all;</strong> in front of a semicolon-separated list of the taxonomies you want to remove all items for. Here is an example:", 'wp-webhooks' ); ?>
                <pre>ironikus-remove-all;taxonomy_1;taxonomy_2</pre>
            </li>
            <li class="list-group-item">
                <strong><?php echo __( "Remove single taxonomy items for a taxonomy", 'wp-webhooks' ); ?></strong>
                <br>
                <?php echo __( "You can also remove only single taxonomy items for one or multiple taxonomies. Here is an example:", 'wp-webhooks' ); ?>
                <pre>ironikus-append;taxonomy_1,value_1:value_2-ironikus-delete:value_3;taxonomy_2,value_5:value_6:value_7-ironikus-delete</pre>
                <?php echo __( "In the example above, we append the taxonomies taxonomy_1 and taxonomy_2. We also add the taxonomy items value_1, value_3, value_5 and value_6. We also remove the taxonomy items value_2 and value_7.", 'wp-webhooks' ); ?>
            </li>
            <li class="list-group-item">
                <strong><?php echo __( "Append taxonomy items", 'wp-webhooks' ); ?></strong>
                <br>
                <?php echo __( "You can also append any taxonomy items without the existing ones being replaced. To do that, simply add <strong>ironikus-append;</strong> at the beginning of the string.", 'wp-webhooks' ); ?>
                <pre>ironikus-append;taxonomy_1,value_1:value_2:value_3;taxonomy_2,value_1:value_2:value_3</pre>
                <?php echo __( "In the example above, we append the taxonomies taxonomy_1 and taxonomy_2 with multiple taxonomy items on the download. The already assigned ones won't be replaced.", 'wp-webhooks' ); ?>
            </li>
        </ul>
    </li>
    <li>
    <strong><?php echo __( "JSON method", 'wp-webhooks' ); ?></strong>
        <br>
        <?php echo __( "This method allows you to add/update/delete or bulk manage the download taxonomies using a simple string. Both the string and the JSON method support custom taxonomies too.", 'wp-webhooks' ); ?>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <strong><?php echo __( "Replace existing taxonomy items", 'wp-webhooks' ); ?></strong>
                <br>
                <?php echo __( "This JSON allows you to replace already existing taxonomy items on the download. In case a taxonomy item does not exists at the point you want to add it, it will be ignored.", 'wp-webhooks' ); ?>
                <pre>{
    "category": [
    "test-category",
    "second-category"
    ],
    "post_tag": [
    "dog",
    "male",
    "simple"
    ]
}</pre>
                <?php echo __( "The key on the first layer of the JSON is the slug of the taxonomy. As a value, it accepts multiple slugs of the single taxonomy terms. To add multiple taxonomies, simply append them on the first layer of the JSON.", 'wp-webhooks' ); ?>
            </li>
            <li class="list-group-item">
                <strong><?php echo __( "Remove all taxonomy items for a single taxonomy", 'wp-webhooks' ); ?></strong>
                <br>
                <?php echo __( "In case you want to remove all taxonomy items from one or multiple taxonomies, you can set <strong>ironikus-remove-all</strong> as a separate value with the <strong>wpwhtype</strong> key. The <strong>wpwhtype</strong> key is a reserved key for further actions on the data. Here is an example:", 'wp-webhooks' ); ?>
                <pre>{
    "wpwhtype": "ironikus-remove-all",
    "category": [],
    "post_tag": []
}</pre>
            </li>
            <li class="list-group-item">
                <strong><?php echo __( "Append taxonomy items", 'wp-webhooks' ); ?></strong>
                <br>
                <?php echo __( "You can also append any taxonomy items without the existing ones being replaced. To do that, simply add <strong>ironikus-append</strong> to the <strong>wpwhtype</strong> key. The <strong>wpwhtype</strong> key is a reserved key for further actions on the data. All the taxonomies you add after, will be added to the existing ones on the download.", 'wp-webhooks' ); ?>
                <pre>{
    "wpwhtype": "ironikus-append",
    "category": [
    "test-category",
    "second-category"
    ],
    "post_tag": [
    "dog"
    ]
}</pre>
                <?php echo __( "In the example above, we append the taxonomies category and post_tag with multiple taxonomy items on the download. The already assigned ones won't be replaced.", 'wp-webhooks' ); ?>
            </li>
            <li class="list-group-item">
                <strong><?php echo __( "Remove single taxonomy items for a taxonomy", 'wp-webhooks' ); ?></strong>
                <br>
                <?php echo __( "You can also remove only single taxonomy items for one or multiple taxonomies. To do that, simply append <strong>-ironikus-delete</strong> at the end of the taxonomy term slug. This specific taxonomy term will then be removed from the download. Here is an example:", 'wp-webhooks' ); ?>
                <pre>{
    "wpwhtype": "ironikus-append",
    "category": [
    "test-category",
    "second-category-ironikus-delete"
    ],
    "post_tag": [
    "dog-ironikus-delete"
    ]
}</pre>
                <?php echo __( "In the example above, we append the taxonomies category and post_tag. We also add the taxonomy item test-category. We also remove the taxonomy items second-category and dog.", 'wp-webhooks' ); ?>
            </li>
        </ul>
    </li>
</ol>
			<?php
			$parameter['tax_input']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "This argument is specifically designed to add/update or remove download meta to your created download.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "To create/update or delete custom meta values, we offer you two different ways:", 'wp-webhooks' ); ?>
<ol>
    <li>
        <strong><?php echo __( "String method", 'wp-webhooks' ); ?></strong>
        <br>
        <?php echo __( "This method allows you to add/update or delete the download meta using a simple string. To make it work, separate the meta key from the value using a comma (,). To separate multiple meta settings from each other, simply separate them with a semicolon (;). To remove a meta value, simply set as a value <strong>ironikus-delete</strong>", 'wp-webhooks' ); ?>
        <pre>meta_key_1,meta_value_1;my_second_key,ironikus-delete</pre>
        <?php echo __( "<strong>IMPORTANT:</strong> Please note that if you want to use values that contain commas or semicolons, the string method does not work. In this case, please use the JSON method.", 'wp-webhooks' ); ?>
    </li>
    <li>
    <strong><?php echo __( "JSON method", 'wp-webhooks' ); ?></strong>
        <br>
        <?php echo __( "This method allows you to add/update or remove the download meta using a JSON formatted string. To make it work, add the meta key as the key and the meta value as the value. To delete a meta value, simply set the value to <strong>ironikus-delete</strong>. Here's an example on how this looks like:", 'wp-webhooks' ); ?>
        <pre>{
    "meta_key_1": "This is my meta value 1",
    "another_meta_key": "This is my second meta key!"
    "third_meta_key": "ironikus-delete"
}</pre>
    </li>
</ol>
<strong><?php echo __( "Advanced", 'wp-webhooks' ); ?></strong>: <?php echo __( "We also offer JSON to array serialization for single download meta values. This means, you can turn JSON into a serialized array.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "As an example: The following JSON <strong>{\"price\": \"100\"}</strong> will turn into <strong>a:1:{s:5:\"price\";s:3:\"100\";}</strong>", 'wp-webhooks' ); ?>
<br>
<?php echo __( "To make it work, you need to add the following string in front of the escaped JSON within the value field of your single meta value of the meta_input argument: <strong>ironikus-serialize</strong>. Here's a full example:", 'wp-webhooks' ); ?>
<pre>{
    "meta_key_1": "This is my meta value 1",
    "another_meta_key": "This is my second meta key!",
    "third_meta_key": "ironikus-serialize{\"price\": \"100\"}"
}</pre>
<?php echo __( "This example will create three download meta entries. The third entry has the meta key <strong>third_meta_key</strong> and a serialized meta value of <strong>a:1:{s:5:\"price\";s:3:\"100\";}</strong>. The string <strong>ironikus-serialize</strong> in front of the escaped JSON will tell our plugin to serialize the value. Please note that the JSON value, which you include within the original JSON string of the meta_input argument, needs to be escaped.", 'wp-webhooks' ); ?>
			<?php
			$parameter['meta_input']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "In case you set the <strong>wp_error</strong> argument to <strong>yes</strong>, we will return the WP Error object within the response if the webhook action call. It is recommended to only use this for debugging.", 'wp-webhooks' ); ?>
			<?php
			$parameter['wp_error']['description'] = ob_get_clean();

			$description = array(
				'tipps' => array(
					__( "By default, we create each download in a draft state. If you want to directly publish a download, use the <strong>download_status</strong> argument and set it to <strong>publish</strong>.", 'wp-webhooks' ),
					__( "In case you want to set a short description for your download, you can use the <strong>download_excerpt</strong> argument.", 'wp-webhooks' ),
				),
			);

            return array(
                'action'            => 'edd_create_download',
                'name'              => __( 'Create download', 'wp-webhooks' ),
                'sentence'              => __( 'create a download', 'wp-webhooks' ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => __( 'This webhook action allows you to create a download within Easy Digital Downloads.', 'wp-webhooks' ),
                'description'       => $description,
                'integration'       => 'edd',
                'premium' 			=> false,
            );

        }

        public function execute( $return_data, $response_body ){

            $update = false;
            $edd_helpers = WPWHPRO()->integrations->get_helper( 'edd', 'edd_helpers' );
            $post_type = 'download';
			$download = null;
			$return_args = array(
				'success'   => false,
				'msg'       => '',
				'data'      => array(
					'download_id' => null,
					'download_data' => null,
					'edd' => array()
				)
			);

			$post_id                		= intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'download_id' ) );

			//edd related
			$increase_earnings      		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'increase_earnings' );//float
			$decrease_earnings      		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'decrease_earnings' );//float
			$increase_sales      			= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'increase_sales' ); //int
			$decrease_sales      			= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'decrease_sales' );//int
			$edd_price      				= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'price' );//float
			$is_variable_pricing      		= ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'is_variable_pricing' ) === 'yes' ) ? 1 : 0;//integer
			$edd_variable_prices      		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'variable_prices' );//json string
			$default_price_id      			= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'default_price_id' );//integer
			$edd_download_files      		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'download_files' );//json string
			$edd_bundled_products      		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'bundled_products' );//json string
			$bundled_products_conditions    = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'bundled_products_conditions' );//json string
			$hide_purchase_link      		= ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'hide_purchase_link' ) === 'yes' ) ? 'on': 'off';
			$download_limit      			= intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'download_limit' ) );

			//default wp
			$post_author            = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'download_author' );
			$post_date              = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'download_date' ) );
			$post_date_gmt          = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'download_date_gmt' ) );
			$post_content           = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'download_content' );
			$post_content_filtered  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'download_content_filtered' );
			$post_title             = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'download_title' );
			$post_excerpt           = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'download_excerpt' );
			$post_status            = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'download_status' ) );
			$comment_status         = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_status' ) );
			$ping_status            = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'ping_status' ) );
			$post_password          = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'download_password' ) );
			$post_name              = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'download_name' ) );
			$to_ping                = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'to_ping' ) );
			$pinged                 = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'pinged' ) );
			$post_modified          = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'download_modified' ) );
			$post_modified_gmt      = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'download_modified_gmt' ) );
			$post_parent            = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'download_parent' ) );
			$menu_order             = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'menu_order' ) );
			$post_mime_type         = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'download_mime_type' ) );
			$guid                   = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'guid' ) );
			$post_category          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'download_category' );
			$tags_input             = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tags_input' );
			$tax_input              = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tax_input' );
			$meta_input             = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'meta_input' );
			$wp_error               = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'wp_error' ) == 'yes' )     ? true : false;
			$create_if_none         = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'create_if_none' ) == 'yes' )     ? true : false;
			$do_action              = sanitize_text_field( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' ) );

			if( ! class_exists( 'EDD_Download' ) ){
				if ( empty( $post_id ) ) {
					$return_args['msg'] = __( "The class EDD_Download does not exist. Please check if the plugin is active.", 'wp-webhooks' );

					return $return_args;
				}
			}

			if( $update && ! $create_if_none ){
				if ( empty( $post_id ) ) {
					$return_args['msg'] = __( "The download id is required to update a download.", 'wp-webhooks' );

					return $return_args;
				}
			}

			if( ! empty( $post_id ) && get_post_type( $post_id ) !== 'download' ){
				$return_args['msg'] = __( "The given download id is not a download.", 'wp-webhooks' );

				return $return_args;
			}

			$create_post_on_update = false;
			$post_data = array();

			if( $update ){
				$post = '';

				if( ! empty( $post_id ) ){
					$post = get_post( $post_id );
				}

				if( ! empty( $post ) ){
					if( ! empty( $post->ID ) ){
						$post_data['ID'] = $post->ID;
					}
				}

				if( empty( $post_data['ID'] ) ){

					$create_post_on_update = apply_filters( 'wpwhpro/run/create_action_edd_download_on_update', $create_if_none );

					if( empty( $create_post_on_update ) ){
						$return_args['msg'] = __( "Download not found.", 'wp-webhooks' );

						return $return_args;
					}

				}

			}

			if( ! empty( $post_author ) ){

				$post_author_id = 0;
				if( is_numeric( $post_author ) ){
					$post_author_id = intval( $post_author );
				} elseif ( is_email( $post_author ) ) {
					$get_user = get_user_by( 'email', $post_author );
					if( ! empty( $get_user ) && ! empty( $get_user->data ) && ! empty( $get_user->data->ID ) ){
						$post_author_id = $get_user->data->ID;
					}
				}

				$post_data['post_author'] = $post_author_id;
			}

			if( ! empty( $post_date ) ){
				$post_data['post_date'] = date( "Y-m-d H:i:s", strtotime( $post_date ) );
			}

			if( ! empty( $post_date_gmt ) ){
				$post_data['post_date_gmt'] = date( "Y-m-d H:i:s", strtotime( $post_date_gmt ) );
			}

			if( ! empty( $post_content ) ){
				$post_data['post_content'] = $post_content;
			}

			if( ! empty( $post_content_filtered ) ){
				$post_data['post_content_filtered'] = $post_content_filtered;
			}

			if( ! empty( $post_title ) ){
				$post_data['post_title'] = $post_title;
			}

			if( ! empty( $post_excerpt ) ){
				$post_data['post_excerpt'] = $post_excerpt;
			}

			if( ! empty( $post_status ) ){
				$post_data['post_status'] = $post_status;
			}

			if( ! empty( $post_type ) ){
				$post_data['post_type'] = $post_type;
			}

			if( ! empty( $comment_status ) ){
				$post_data['comment_status'] = $comment_status;
			}

			if( ! empty( $ping_status ) ){
				$post_data['ping_status'] = $ping_status;
			}

			if( ! empty( $post_password ) ){
				$post_data['post_password'] = $post_password;
			}

			if( ! empty( $post_name ) ){
				$post_data['post_name'] = $post_name;
			}

			if( ! empty( $to_ping ) ){
				$post_data['to_ping'] = $to_ping;
			}

			if( ! empty( $pinged ) ){
				$post_data['pinged'] = $pinged;
			}

			if( ! empty( $post_modified ) ){
				$post_data['post_modified'] = date( "Y-m-d H:i:s", strtotime( $post_modified ) );
			}

			if( ! empty( $post_modified_gmt ) ){
				$post_data['post_modified_gmt'] = date( "Y-m-d H:i:s", strtotime( $post_modified_gmt ) );
			}

			if( ! empty( $post_parent ) ){
				$post_data['post_parent'] = $post_parent;
			}

			if( ! empty( $menu_order ) ){
				$post_data['menu_order'] = $menu_order;
			}

			if( ! empty( $post_mime_type ) ){
				$post_data['post_mime_type'] = $post_mime_type;
			}

			if( ! empty( $guid ) ){
				$post_data['guid'] = $guid;
			}

			//Setup post categories
			if( ! empty( $post_category ) ){
				$post_category_data = explode( ',', trim( $post_category, ',' ) );

				if( ! empty( $post_category_data ) ){
					$post_data['post_category'] = $post_category_data;
				}
			}

			//Setup meta tags
			if( ! empty( $tags_input ) ){
				$post_tags_data = explode( ',', trim( $tags_input, ',' ) );

				if( ! empty( $post_tags_data ) ){
					$post_data['tags_input'] = $post_tags_data;
				}
			}

			add_action( 'wp_insert_post', array( $edd_helpers, 'edd_create_update_download_add_meta' ), 8, 1 );

			if( $update && ! $create_post_on_update ){
				$post_id = wp_update_post( $post_data, $wp_error );
			} else {
				$download = new EDD_Download();
				if( ! empty( $download ) ){
					$new_dl = $download->create( $post_data ); //$wp_error is useless here
					if( ! empty( $new_dl ) && ! empty( $download->ID ) ){
						$post_id = $download->ID;
					} else {
						//fallback
						$post_id = wp_insert_post( $post_data, $wp_error );
					}
				} else {
					$post_id = wp_insert_post( $post_data, $wp_error );
				}
			}
			
			remove_action( 'wp_insert_post', array( $edd_helpers, 'edd_create_update_download_add_meta' ) );
			
			if ( ! is_wp_error( $post_id ) && is_numeric( $post_id ) ) {

				//Setup meta tax
				if( ! empty( $tax_input ) ){
					$remove_all = false;
					$tax_append = false; //Default by WP wp_set_object_terms
					$tax_data = array(
						'delete' => array(),
						'create' => array(),
					);

					if( WPWHPRO()->helpers->is_json( $tax_input ) ){
						$post_tax_data = json_decode( $tax_input, true );
						foreach( $post_tax_data as $taxkey => $single_meta ){
		
							//Validate special values
							if( $taxkey == 'wpwhtype' && $single_meta == 'ironikus-append' ){
								$tax_append = true;
								continue;
							}
		
							if( $taxkey == 'wpwhtype' && $single_meta == 'ironikus-remove-all' ){
								$remove_all = true;
								continue;
							}
		
							$meta_key           = sanitize_text_field( $taxkey );
							$meta_values        = $single_meta;
		
							if( ! empty( $meta_key ) ){
		
								if( ! is_array( $meta_values ) ){
									$meta_values = array( $meta_values );
								}
		
								//separate for deletion and for creation
								foreach( $meta_values as $svalue ){
									if( strpos( $svalue, '-ironikus-delete' ) !== FALSE ){
		
										if( ! isset( $tax_data['delete'][ $meta_key ] ) ){
											$tax_data['delete'][ $meta_key ] = array();
										}
		
										//Replace deletion value to correct original value
										$tax_data['delete'][ $meta_key ][] = str_replace( '-ironikus-delete', '', $svalue );
									} else {
		
										if( ! isset( $tax_data['create'][ $meta_key ] ) ){
											$tax_data['create'][ $meta_key ] = array();
										}
		
										$tax_data['create'][ $meta_key ][] = $svalue;
									}
								}
		
							}
						}
					} else {
						$post_tax_data = explode( ';', trim( $tax_input, ';' ) );
						foreach( $post_tax_data as $single_meta ){
		
							//Validate special values
							if( $single_meta == 'ironikus-append' ){
								$tax_append = true;
								continue;
							}
		
							if( $single_meta == 'ironikus-remove-all' ){
								$remove_all = true;
								continue;
							}
		
							$single_meta_data   = explode( ',', $single_meta );
							$meta_key           = sanitize_text_field( $single_meta_data[0] );
							$meta_values        = explode( ':', $single_meta_data[1] );
		
							if( ! empty( $meta_key ) ){
		
								if( ! is_array( $meta_values ) ){
									$meta_values = array( $meta_values );
								}
		
								//separate for deletion and for creation
								foreach( $meta_values as $svalue ){
									if( strpos( $svalue, '-ironikus-delete' ) !== FALSE ){
		
										if( ! isset( $tax_data['delete'][ $meta_key ] ) ){
											$tax_data['delete'][ $meta_key ] = array();
										}
		
										//Replace deletion value to correct original value
										$tax_data['delete'][ $meta_key ][] = str_replace( '-ironikus-delete', '', $svalue );
									} else {
		
										if( ! isset( $tax_data['create'][ $meta_key ] ) ){
											$tax_data['create'][ $meta_key ] = array();
										}
		
										$tax_data['create'][ $meta_key ][] = $svalue;
									}
								}
		
							}
						}
					}

					if( $update && ! $create_post_on_update ){
						foreach( $tax_data['delete'] as $tax_key => $tax_values ){
							wp_remove_object_terms( $post_id, $tax_values, $tax_key );
						}
					}

					foreach( $tax_data['create'] as $tax_key => $tax_values ){

						if( $remove_all ){
							wp_set_object_terms( $post_id, array(), $tax_key, $tax_append );
						} else {
							wp_set_object_terms( $post_id, $tax_values, $tax_key, $tax_append );
						}

					}

					#$post_data['tax_input'] = $tax_data;
				}

				//Map response data
				$post_data['meta_data'] = $meta_input;
				$post_data['tax_input'] = $tax_input;
				$post_data['create_if_none'] = $create_if_none;
				$post_data['edd'] = array(
					'increase_earnings' => $increase_earnings,
					'decrease_earnings' => $decrease_earnings,
					'increase_sales' => $increase_sales,
					'decrease_sales' => $decrease_sales,
					'edd_price' => $edd_price,
					'is_variable_pricing' => $is_variable_pricing,
					'edd_variable_prices' => $edd_variable_prices,
					'default_price_id' => $default_price_id,
					'edd_download_files' => $edd_download_files,
					'edd_bundled_products' => $edd_bundled_products,
					'bundled_products_conditions' => $bundled_products_conditions,
					'hide_purchase_link' => $hide_purchase_link,
					'download_limit' => $download_limit,
				);

				//START EDD logic
				$download = new EDD_Download( $post_id );
				if( ! empty( $download ) ){

					if( ! empty( $increase_earnings ) && is_numeric( $increase_earnings ) ){
						$download->increase_earnings( $increase_earnings );
					}

					if( ! empty( $decrease_earnings ) && is_numeric( $decrease_earnings ) ){
						$download->decrease_earnings( $decrease_earnings );
					}

					if( ! empty( $increase_sales ) && is_numeric( $increase_sales ) ){
						$download->increase_sales( $increase_sales );
					}

					if( ! empty( $decrease_sales ) && is_numeric( $decrease_sales ) ){
						$download->decrease_sales( $decrease_sales );
					}

				}
				//END EDD logic

				if( $update && ! $create_post_on_update ){
					$return_args['msg'] = __( "Download successfully updated", 'wp-webhooks' );
				} else {
					$return_args['msg'] = __( "Download successfully created", 'wp-webhooks' );
				}

				$return_args['data']['download_data'] = $post_data;
				$return_args['data']['download_id'] = $post_id;
				$return_args['success'] = true;

			} else {

				if( is_wp_error( $post_id ) && $wp_error ){

					$return_args['data']['download_data'] = $post_data;
					$return_args['data']['download_id'] = $post_id;
					$return_args['msg'] = __( "WP Error", 'wp-webhooks' );
				} else {
					$return_args['msg'] = __( "Error creating download.", 'wp-webhooks' );
				}
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $post_data, $post_id, $meta_input, $return_args );
			}

			return $return_args;
    
        }

    }

endif; // End if class_exists check.