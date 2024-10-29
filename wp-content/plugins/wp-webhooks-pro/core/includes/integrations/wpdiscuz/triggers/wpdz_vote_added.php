<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_wpdiscuz_Triggers_wpdz_vote_added' ) ) :

 /**
  * Load the wpdz_vote_added trigger
  *
  * @since 5.1.1
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_wpdiscuz_Triggers_wpdz_vote_added {

  public function get_callbacks(){

   return array(
	array(
		'type' => 'action',
		'hook' => 'wpdiscuz_add_vote',
		'callback' => array( $this, 'ironikus_trigger_wpdz_vote_added' ),
		'priority' => 20,
		'arguments' => 2,
		'delayed' => true,
	  ),
	);

  }

	public function get_details(){

		$parameter = array(
			'voteType' => array( 'short_description' => __( '(Integer) The type of the vote. 1 equals an upvote, -1 a downvote.', 'wp-webhooks' ) ),
			'comment' => array( 'short_description' => __( '(Array) Further details about the comment.', 'wp-webhooks' ) ),
		);

	  	$settings = array(
			'load_default_settings' => true,
			'data' => array(
			'wpwhpro_wpdz_vote_added_trigger_on_type' => array(
				'id'	 => 'wpwhpro_wpdz_vote_added_trigger_on_type',
				'type'	=> 'select',
				'multiple'  => true,
				'choices'   => array(
					'upvote' => __( 'Upvote', 'wp-webhooks' ),
					'downvote' => __( 'Downvote', 'wp-webhooks' ),
				),
				'label'	=> __( 'Trigger on selected type', 'wp-webhooks' ),
				'placeholder' => '',
				'required'  => false,
				'description' => __( 'Select only the vote types you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', 'wp-webhooks' )
			),
			)
		);

		return array(
			'trigger'	  => 'wpdz_vote_added',
			'name'	   => __( 'Vote added', 'wp-webhooks' ),
			'sentence'	   => __( 'a vote was added', 'wp-webhooks' ),
			'parameter'	 => $parameter,
			'settings'	 => $settings,
			'returns_code'   => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires after a vote was added within wpDiscuz.', 'wp-webhooks' ),
			'description'	=> array(),
			'integration'	=> 'wpdiscuz',
			'premium'	=> true,
		);

	}

	public function ironikus_trigger_wpdz_vote_added( $voteType, $comment ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wpdz_vote_added' );
		$data_array = array(
			'voteType' => $voteType,
			'comment' => $comment,
		);
		$response_data = array();

		foreach( $webhooks as $webhook ){

			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
			foreach( $webhook['settings'] as $settings_name => $settings_data ){

				if( $settings_name === 'wpwhpro_wpdz_vote_added_trigger_on_courses' && ! empty( $settings_data ) ){
					$is_valid = false;

					if( in_array( 'upvote', $settings_data ) && $voteType > 0 ){
						$is_valid = true;
					} elseif( in_array( 'downvote', $settings_data ) && $voteType < 0 ) {
						$is_valid = true;
					}
				}

			}
			}

			if( $is_valid ) {
			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

			if( $webhook_url_name !== null ){
				$response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
			} else {
				$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
			}
			}
		}

		do_action( 'wpwhpro/webhooks/trigger_wpdz_vote_added', $data_array, $response_data );
	}

	/*
	* Register the demo post delete trigger callback
	*
	* @since 1.2
	*/
	public function get_demo( $options = array() ) {

	  $data = array (
		'voteType' => '1',
		'comment' => 
		array (
			'comment_ID' => '320',
			'comment_post_ID' => '7912',
			'comment_author' => 'demouser',
			'comment_author_email' => 'demouser@demo.test',
			'comment_author_url' => '',
			'comment_author_IP' => '127.0.0.1',
			'comment_date' => '2022-04-26 12:19:52',
			'comment_date_gmt' => '2022-04-26 12:19:52',
			'comment_content' => 'This is a demo comment',
			'comment_karma' => '0',
			'comment_approved' => '1',
			'comment_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.83 Safari/537.36',
			'comment_type' => 'comment',
			'comment_parent' => '0',
			'user_id' => '1',
		),
	);

	  return $data;
	}

  }

endif; // End if class_exists check.