<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_send_email' ) ) :

	/**
	 * Load the send_email action
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_send_email {

		public function is_active(){

			//Backwards compatibility for the "Email integration" integration
			if( defined( 'WPWH_EMAILS_PLUGIN_NAME' ) ){
				return false;
			}

			return true;
		}

		public function get_details(){

				//These are the main arguments the user can use to input. You should always grab them within your action function.
			$parameter = array(
				'send_to'	   => array( 'required' => true, 'short_description' => __( '(string) Comma-separated list of email addresses you want to send the message to.', 'wp-webhooks' ) ),
				'subject'	   => array( 'required' => true, 'short_description' => __( '(string) Email subject', 'wp-webhooks' ) ),
				'message'	   => array( 'required' => true, 'short_description' => __( '(string) Message contents', 'wp-webhooks' ) ),
				'headers'	=> array( 'short_description' => __( '(string) A JSON formatted string contaiing additional settings for the email such as CC, BCC, From etc. - Please see the description for further details.', 'wp-webhooks' ) ),
				'attachments'	=> array( 'short_description' => __( '(string) A JSON formatted string contaiing attachments that should be added to the email. Please see the description for further information.', 'wp-webhooks' ) ),
				'do_action'	=> array( 'short_description' => __( 'Advanced: Register a custom action after the webhook fires.', 'wp-webhooks' ) ),
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful.', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further details about the sent data.', 'wp-webhooks' ) ),
			);

			ob_start();
		?>
<?php echo __( "This argument should contain the email address(es) you want to send this email to. To use multiple ones, simply separate them with a comma:", 'wp-webhooks' ); ?>
<pre>demoemail@somedomain.demo,anotheremail@somedomain.demo</pre>
		<?php
		$parameter['send_to']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "This argument allows you to add further settings to your email using a JSON formatted string. Down below you will find a predefined JSON with the most common settings. If you want to read further on what's possible, you can checkout the following documentation:", 'wp-webhooks' ); ?> 
<a target="_blank" href="https://developer.wordpress.org/reference/functions/wp_mail/" title="https://developer.wordpress.org/reference/functions/wp_mail/">https://developer.wordpress.org/reference/functions/wp_mail/</a>
<br>
<?php echo __( "The example below shows common settings within the formatted JSON for the <strong>headers</strong> argument. Explanations for each line are down below.", 'wp-webhooks' ); ?>
<pre>[
  "Content-Type: text/html; charset=UTF-8",
  "From: Sender Name <anotheremail@someemail.demo>",
  "Cc: First CC Name <receiver@someemail.demo>",
  "Cc: onlyemail@someemail.demo",
  "Bcc: bccmail@someemail.demo",
  "Reply-To: Reply Name <replytome@someemail.demo>"
]</pre>
<ol>
	<li><strong>Content-Type</strong>: <?php echo __( "This entry show you how you can customize the default content type.", 'wp-webhooks' ); ?></li>
	<li><strong>Cc</strong>: <?php echo __( "This line allows you to show a custom from address. You can either use the simply email or the notation in the example to show further details about the receiver.", 'wp-webhooks' ); ?></li>
	<li><strong>Cc</strong>: <?php echo __( "You can also define multiple times the Cc or Bcc entries if you want to send it to multiple persons. Also, this example shows how you can use only the email.", 'wp-webhooks' ); ?></li>
	<li><strong>Bcc</strong>: <?php echo __( "The Bcc entry allows the same settings as the Cc entry.", 'wp-webhooks' ); ?></li>
	<li><strong>Reply-To</strong>: <?php echo __( "This entry allows you to specify a different reply address for your email. You can either use the seen notation or a simple email as seen in the Cc example.", 'wp-webhooks' ); ?></li>
</ol>
		<?php
		$parameter['headers']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "This argument allows you to attach one or multiple files to the email using a JSON formatted string. If you want to read further on what's possible, you can check out the following documentation:", 'wp-webhooks' ); ?> 
<a target="_blank" href="https://developer.wordpress.org/reference/functions/wp_mail/" title="https://developer.wordpress.org/reference/functions/wp_mail/">https://developer.wordpress.org/reference/functions/wp_mail/</a>
<br>
<?php echo __( "The example below shows how you can use this argument. Explanations for each line are down below.", 'wp-webhooks' ); ?>
<pre>[
  "/Your/full/server/path/wp-content/uploads/2020/06/my-custom-file.jpg",
  "{content-dir}/uploads/2020/06/another-file.png"
]</pre>
<ol>
	<li><strong>Direct path</strong>: <?php echo __( "The fist line adds a jpeg image to the email. It does it by using the direct path of the image on the server.", 'wp-webhooks' ); ?></li>
	<li><strong>Dynamic path</strong>: <?php echo __( "In case you do not want to hardcode the dynamic path, you can also use our <strong>{content-dir}</strong> tag, which automatically adds the direct path.", 'wp-webhooks' ); ?></li>
</ol>
		<?php
		$parameter['attachments']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>send_email</strong> action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 2 );
function my_custom_callback_function( $check, $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$check</strong> (bool)<br>
		<?php echo __( "Returns true if the email was sent and false if not.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo __( "Contains the response data of the request, which also include the complete validated data we used for sending the email.", 'wp-webhooks' ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

			$returns_code = array (
				'success' => true,
				'msg' => 'Email successfully sent.',
				'data' => 
				array (
				  'send_to' => 'demo@demo.demo',
				  'subject' => 'This is my email Subject',
				  'message' => 'This is my <strong>HTML</strong> message!',
				  'headers' => 
				  array (
					0 => 'Content-Type: text/html; charset=UTF-8',
					1 => 'From: Sender Name <anotheremail@someemail.demo>',
					2 => 'Cc: Receiver Name <receiver@someemail.demo>',
					3 => 'Cc: onlyemail@someemail.demo',
					4 => 'Bcc: bccmail@someemail.demo',
					5 => 'Reply-To: Reply Name <replytome@someemail.demo>',
				  ),
				  'attachments' => 
				  array (
					0 => '/Your/full/server/path/wp-content/uploads/2020/06/my-custom-file.jpg',
				  ),
				),
			);

			$description = array(
				'tipps' => array(
					__( "To use HTML within your email, you need to set the content type to text/html. Please see the <strong>headers</strong> argument for further details.", 'wp-webhooks' ),
					__( "You can also set further settings like CC emails or BCC emails. Please see the <strong>headers</strong> argument for further details.", 'wp-webhooks' ),
				),
			);

			return array(
				'action'			=> 'send_email',
				'name'			  => __( 'Send email', 'wp-webhooks' ),
				'sentence'			  => __( 'send an email', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'This webhook action allows you to send an email from your WordPress site.', 'wp-webhooks' ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> false,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'data' => array(
					'send_to' => '',
					'subject' => '',
					'message' => '',
					'headers' => array(),
					'attachments' => array(),
				)
			);

			//This is how defined parameters look - you can use the exact same structure and catch the data you need
			$sent_to	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'sent_to' ); //For Fallback compatibility
			$send_to	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'send_to' );
			$subject	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'subject' );
			$message	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'message' );
			$additional_headers	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'headers' );
			$additional_attachments	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'attachments' );
			$do_action		  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			//Correct incompatible values
			if( empty( $send_to ) && ! empty( $sent_to ) ){
				$send_to = $sent_to;
			}

			//Validate required fields
			if( empty( $send_to ) ){
				$return_args['msg'] = __( "The send_to argument cannot be empty.", 'action-send_email-failure' );
				return $return_args;
			}
			if( empty( $subject ) ){
				$return_args['msg'] = __( "The subject argument cannot be empty.", 'action-send_email-failure' );
				return $return_args;
			}
			if( empty( $message ) ){
				$return_args['msg'] = __( "The message argument cannot be empty.", 'action-send_email-failure' );
				return $return_args;
			}

			$headers = array();

			if( ! empty( $additional_headers ) && WPWHPRO()->helpers->is_json( $additional_headers ) ){
				$encoded_additional_headers = json_decode( $additional_headers, true );
				if( ! empty( $encoded_additional_headers ) && is_array( $encoded_additional_headers ) ){
					$headers = array_merge( $headers, $encoded_additional_headers );
				}
			}

			$attachments = array();

			if( ! empty( $additional_attachments ) && WPWHPRO()->helpers->is_json( $additional_attachments ) ){
				$encoded_additional_attachments = json_decode( $additional_attachments, true );
				if( ! empty( $encoded_additional_attachments ) && is_array( $encoded_additional_attachments ) ){
					$attachments = array_merge( $attachments, $encoded_additional_attachments );
				}
			}

			//apply the dynamic content dir
			if( defined( 'WP_CONTENT_DIR' ) ){
				foreach( $attachments as $key => $file ){
					$attachments[ $key ] = str_replace( '{content-dir}', WP_CONTENT_DIR, $file );
				}
			}

			$check = wp_mail( $send_to, $subject, $message, $headers, $attachments );

			$return_args['data'] = array(
				'send_to' => $send_to,
				'subject' => $subject,
				'message' => $message,
				'headers' => $headers,
				'attachments' => $attachments,
			);

			if( $check ){
				$return_args['msg'] = __( "Email successfully sent.", 'action-send_email-success' );
				$return_args['success'] = true;
			} else {
				$return_args['msg'] = __( "Your email was not sent since wp_mail() returned false.", 'action-send_email-success' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $check, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.