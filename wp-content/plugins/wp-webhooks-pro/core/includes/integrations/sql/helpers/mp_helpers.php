<?php

use SQL\Form\FormsRepository;
use SQL\DI\ContainerWrapper;

if ( ! class_exists( 'WP_Webhooks_Integrations_sql_Helpers_mp_helpers' ) ) :

	/**
	 * Load the FuentCRM helpers
	 *
	 * @since 6.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_sql_Helpers_mp_helpers {

        public function get_query_forms( $entries, $query_args, $args){

			//bail for paged values as everything is returned at once
			if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
				return $entries;
			}

            $form_items = array();
			
			if( class_exists('SQL\Form\FormsRepository') ){
				$formsRepository = ContainerWrapper::getInstance()->get(FormsRepository::class);
				$forms = $formsRepository->findAll();
				
				foreach ( $forms as $form ) {
					$form_items[ $form->getId() ] = esc_html( $form->getName());
				}
			}	

			foreach( $form_items as $name => $title ){

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

        public function get_lists( $entries, $query_args, $args){

			//bail for paged values as everything is returned at once
			if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
				return $entries;
			}

            $list_items = array();

			if ( class_exists( '\SQL\API\API' ) ) {
				$sql = \SQL\API\API::MP( 'v1' );
				$lists = $sql->getLists();
				
				if( is_array( $lists ) ){
					foreach( $lists as $list ){
						if( is_array( $list ) && isset( $list['id'] ) && isset( $list['name'] ) ){
							$list_items[ $list['id'] ] = $list['name'];
						}
					}
				}
			}

			foreach( $list_items as $name => $title ){

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