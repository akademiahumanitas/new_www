<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_fluent_support_Helpers_flsup_helpers' ) ) :

	/**
	 * Load the FuentCRM helpers
	 *
	 * @since 4.3.4
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_fluent_support_Helpers_flsup_helpers {

        public function get_person_types(){

            $types = array(
                'agent' => __( 'Agent', 'wp-webhooks' ),
                'customer' => __( 'Customer', 'wp-webhooks' ),
            );

            $types = apply_filters( 'wpwhpro/webhooks/fluent_support/helpers/flsup_helpers', $types );

            return $types;

        }

        public function get_query_types( $entries, $query_args, $args ){

			//bail for paged values as everything is returned at once
			if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
				return $entries;
			}

            $type_items = $this->get_person_types();

			foreach( $type_items as $name => $title ){

				//skip search values that don't occur if set
				if( isset( $args['s'] ) && $args['s'] !== '' ){
					if( 
						strpos( $name, $args['s'] ) === false
						&& strpos( $title, $args['s'] ) === false
					){
						continue;
					}
				}

				//skip unselected values in a selected statement
				if( isset( $args['selected'] ) && ! empty( $args['selected'] ) ){
					if( ! in_array( $name, (array) $args['selected'] ) ){
						continue;
					}
				}

				$entries['items'][ $name ] = array(
					'value' => $name,
					'label' => $title,
				);
			}

			//calculate total
			$entries['total'] = count( $entries['items'] );

			//set all items to be visible on one page
			$entries['per_page'] = count( $entries['items'] );

			return $entries;
		}

        public function get_query_ticket_priorities( $entries, $query_args, $args ){

			//bail for paged values as everything is returned at once
			if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
				return $entries;
			}

            $priorities = array();
			if( class_exists( '\FluentSupport\App\Services\Helper' ) ){
				$helpers = new \FluentSupport\App\Services\Helper();
				if( method_exists( $helpers, 'adminTicketPriorities' ) ){
					$priorities = $helpers->adminTicketPriorities();
				}
			}

			foreach( $priorities as $name => $title ){

				//skip search values that don't occur if set
				if( isset( $args['s'] ) && $args['s'] !== '' ){
					if( 
						strpos( $name, $args['s'] ) === false
						&& strpos( $title, $args['s'] ) === false
					){
						continue;
					}
				}

				//skip unselected values in a selected statement
				if( isset( $args['selected'] ) && ! empty( $args['selected'] ) ){
					if( ! in_array( $name, (array) $args['selected'] ) ){
						continue;
					}
				}

				$entries['items'][ $name ] = array(
					'value' => $name,
					'label' => $title,
				);
			}

			//calculate total
			$entries['total'] = count( $entries['items'] );

			//set all items to be visible on one page
			$entries['per_page'] = count( $entries['items'] );

			return $entries;
		}

        public function get_query_client_ticket_priorities( $entries, $query_args, $args ){

			//bail for paged values as everything is returned at once
			if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
				return $entries;
			}

            $priorities = array();
			if( class_exists( '\FluentSupport\App\Services\Helper' ) ){
				$helpers = new \FluentSupport\App\Services\Helper();
				if( method_exists( $helpers, 'customerTicketPriorities' ) ){
					$priorities = $helpers->customerTicketPriorities();
				}
			}

			foreach( $priorities as $name => $title ){

				//skip search values that don't occur if set
				if( isset( $args['s'] ) && $args['s'] !== '' ){
					if( 
						strpos( $name, $args['s'] ) === false
						&& strpos( $title, $args['s'] ) === false
					){
						continue;
					}
				}

				//skip unselected values in a selected statement
				if( isset( $args['selected'] ) && ! empty( $args['selected'] ) ){
					if( ! in_array( $name, (array) $args['selected'] ) ){
						continue;
					}
				}

				$entries['items'][ $name ] = array(
					'value' => $name,
					'label' => $title,
				);
			}

			//calculate total
			$entries['total'] = count( $entries['items'] );

			//set all items to be visible on one page
			$entries['per_page'] = count( $entries['items'] );

			return $entries;
		}

	}

endif; // End if class_exists check.