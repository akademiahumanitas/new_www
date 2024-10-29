<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_autonami_Actions_ami_add_new_contact' ) ) :
    /**
     * Load the ami_add_new_contact action
     *
     * @since 6.0
     * @author Ironikus <info@ironikus.com>
     */
    class WP_Webhooks_Integrations_autonami_Actions_ami_add_new_contact {

        public function get_details(){

            $parameter = array(
                'email' => array( 
                    'required' => true,
                    'label' => __( 'Email', 'wp-webhooks' ),
                    'short_description' => __( '(String) The email you want to add. In case the email exist within a contact, the data will not be updated.', 'wp-webhooks' ),
                ),
                'first_name' => array(
                    'label' => __( 'First Name', 'wp-webhooks' ),
                    'short_description' => __( '(String) The first name of the new contact.', 'wp-webhooks' ),
                ),
                'last_name' => array(
                    'label' => __( 'Last Name', 'wp-webhooks' ),
                    'short_description' => __( '(String) The last name of the new contact.', 'wp-webhooks' ),
                ),
                'contact_no' => array(
                    'label' => __( 'Contact Number', 'wp-webhooks' ),
                    'short_description' => __( '(String) The contact number.', 'wp-webhooks' ),
                ),
                'lists' => array(
                    'label' => __( 'List Names', 'wp-webhooks' ),
                    'short_description' => __( '(String) The list name you want to add. In case the list doesn\'t exist, we create a new one. To add multiple lists, please comma-separate them.', 'wp-webhooks' ),
                ),
                'tags' => array(
                    'label' => __( 'Tag Names', 'wp-webhooks' ),
                    'short_description' => __( '(String) The tag you want to add. In case the tag doesn\'t exist, we create a new one. To add multiple tags, please comma-separate them.', 'wp-webhooks' ),
                ),
            );

            $returns = array(
                'success'	=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
                'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
                'data'		=> array( 'short_description' => __( '(array) Further details about the sent data.', 'wp-webhooks' ) ),
            );

            $returns_code =  [
                "success" => true,
                "msg" => "Contact created.",
                "data" => [
                    "contact" => [
                        "id" => 7,
                        "wpid" => 0,
                        "email" => "democontact@yourdomain.test",
                        "first_name" => "Demo",
                        "last_name" => "User",
                        "contact_no" => "",
                        "state" => "",
                        "country" => "",
                        "creation_date" => "2022-08-23 23:35:49",
                        "timezone" => "",
                        "fields" => [],
                        "last_modified" => "",
                        "unsubscribed" => false,
                        "source" => "",
                        "type" => "lead",
                        "status" => 0,
                        "tags" => [
                            [
                                "ID" => 1,
                                "name" => "testtag",
                                "type" => "1",
                                "created_at" => "2022-08-23 23:08:58",
                                "updated_at" => null,
                                "data" => null,
                            ],
                        ],
                        "lists" => [],
                        "display_status" => 3,
                        "last_email_sent" => "",
                        "last_email_open" => 0,
                        "last_sms_sent" => "",
                        "last_click" => 0,
                        "last_login" => "",
                        "link_triggers" => [],
                    ],
                ],
            ];

            return array(
                'action'            => 'ami_add_new_contact',
                'name'              => __( 'Add new contact', 'wp-webhooks' ),
                'sentence'          => __( 'add a new contact', 'wp-webhooks' ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => __( 'This webhook action allows you to add a new contact in FunnelKit Automations.', 'wp-webhooks' ),
                'description'       => array(),
                'integration'       => 'autonami',
                'premium'           => true,
            );
        }

        public function execute( $return_data, $response_body ){

            $return_args = array(
                'success' => false,
                'msg' => '',
                'data' => array(
                    'contact' => array(),
                ),
            );

            $email	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'email' );
            $params  = array(
                'f_name' => WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'first_name' ),
                'l_name' => WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'last_name' ),
                'contact_no' => WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'contact_no' ),
                'lists'	 => WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'lists' ),
                'tags'	 => WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tags' )
            );

            if( empty( $email )  || ! is_email( $email )){
                $return_args['msg'] = __( "Email is not valid", 'action-ami_add_new_contact-failure' );
                return $return_args;
            }

            $contact = new BWFCRM_Contact( $email, true, $params );
            if ( isset( $params['tags'] ) ) {

                $tags = explode( ',', $params['tags'] );
                $tags_array = array();

                if( is_array( $tags ) ){

                    foreach( $tags as $tag_id ){
                        $tag_id = trim( $tag_id );

                        $tags_array[] = array( 'id' => 0, 'value' => $tag_id );
                    }

                }
                
                if( ! empty( $tags_array ) ){
                    $tags_response = $contact->set_tags( $tags_array, true, false );
                }
                
            }

            if ( isset( $params['lists'] ) ) {
                
                $lists = explode( ',', $params['lists'] );
                $lists_array = array();

                if( is_array( $lists ) ){

                    foreach( $lists as $list_id ){
                        $list_id = trim( $list_id );

                        $lists_array[] = array( 'id' => 0, 'value' => $list_id );
                    }

                }
                
                if( ! empty( $lists_array ) ){
                    $lists_response = $contact->set_lists( $lists_array, true, false );
                }

            }

            $contact->save();

            if ( $contact->already_exists ) {
                $return_args['success'] = true;
                $return_args['msg'] = __( "The contact already exists.", 'action-ami_add_new_contact-failure' );
                $return_args['data']['contact'] = $contact->get_array();
                return $return_args;
            }

            if ( $contact->is_contact_exists() ) {
                $return_args['success'] = true;
                $return_args['msg'] = __( "Contact created.", 'action-ami_add_new_contact-success' );
                $return_args['data']['contact'] = $contact->get_array();
            } else {
                $return_args['msg'] = __( "An error occured creating the contact.", 'action-ami_add_new_contact-success' );
            }

            return $return_args;
        }
    }
endif;