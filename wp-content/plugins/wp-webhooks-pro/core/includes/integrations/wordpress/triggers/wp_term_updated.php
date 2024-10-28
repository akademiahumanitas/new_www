<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Triggers_wp_term_updated' ) ) :

	/**
	 * Load the wp_term_updated trigger
	 *
	 * @since 5.2.6
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Triggers_wp_term_updated {

		public function get_callbacks(){

            return array(
                array(
                    'type' => 'action',
                    'hook' => 'edited_term',
                    'callback' => array( $this, 'ironikus_trigger_wp_term_updated' ),
                    'priority' => 20,
                    'arguments' => 3,
                    'delayed' => true,
                ),
            );

		}

        public function get_details(){

			$parameter = array(
				'term_id'   => array( 'short_description' => __( 'The term ID.', 'wp-webhooks' ) ),
				'taxonomy_id'   => array( 'short_description' => __( 'The ID of the taxonomy.', 'wp-webhooks' ) ),
				'taxonomy_slug'   => array( 'short_description' => __( 'The slug of the taxonomy.', 'wp-webhooks' ) ),
				'term_data'   => array( 'short_description' => __( 'The term data.', 'wp-webhooks' ) ),
				'term_meta'   => array( 'short_description' => __( 'Further details of the term.', 'wp-webhooks' ) ),
			);

			$settings = array(
				'load_default_settings' => true,
				'data' => array()
			);

            return array(
                'trigger'           => 'wp_term_updated',
                'name'              => __( 'Taxonomy term updated', 'wp-webhooks' ),
                'sentence'              => __( 'a taxonomy term was updated', 'wp-webhooks' ),
                'parameter'         => $parameter,
                'settings'          => $settings,
                'returns_code'      => $this->get_demo( array() ),
                'short_description' => __( 'This webhook fires after a taxonomy term was updated.', 'wp-webhooks' ),
                'description'       => array(), 
                'integration'       => 'wordpress',
                'premium'           => true,
            );

        }

        public function ironikus_trigger_wp_term_updated( $term_id, $tt_id, $taxonomy ){

			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wp_term_updated' );
			$data_array = array(
				'term_id' => $term_id,
				'taxonomy_id' => $tt_id,
				'taxonomy_slug' => $taxonomy,
				'term_data' => get_term( $term_id, $taxonomy ),
				'term_meta' => get_term_meta( $term_id ),
			);
			$response_data = array();

			foreach( $webhooks as $webhook ){

				$is_valid = true;

				if( $is_valid ){
					$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

					if( $webhook_url_name !== null ){
						$response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
					} else {
						$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
					}
				}
			}

		}

        public function get_demo( $options = array() ) {

            $data = array (
				'term_id' => 117,
				'taxonomy_id' => 117,
				'taxonomy_slug' => 'category',
				'term_data' => 
				array (
				  'term_id' => 117,
				  'name' => 'Demo Term',
				  'slug' => 'demo-term',
				  'term_group' => 0,
				  'term_taxonomy_id' => 117,
				  'taxonomy' => 'category',
				  'description' => 'This is a demo term description.',
				  'parent' => 0,
				  'count' => 0,
				  'filter' => 'raw',
				),
				'term_meta' => 
				array (
				  'demo_field' => 
				  array (
					0 => '',
				  ),
				),
			);

            return $data;
        }

    }

endif; // End if class_exists check.