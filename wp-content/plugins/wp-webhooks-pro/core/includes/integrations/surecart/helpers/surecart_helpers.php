<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_surecart_Helpers_surecart_helpers' ) ) :

	/**
	 * Load the SureCart helpers
	 *
	 * @since 6.0.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_surecart_Helpers_surecart_helpers {

        public function get_query_products( $entries, $query_args, $args ){

			$default_query = array(
				'archived' => false,
			);

			//skip search values that don't occur if set
			if( isset( $args['s'] ) && $args['s'] !== '' ){
				$default_query['query'] = esc_sql( $args['s'] );
			}

			$paged = 1;
			if( isset( $args['paged'] ) ){
				$paged = intval( $args['paged'] );
			}

            $products = array();
			$product_query = null;
			
			if( class_exists( '\SureCart\Models\Product' ) ){

				//skip unselected values in a selected statement
				if( isset( $args['selected'] ) && ! empty( $args['selected'] ) ){
					$product_query = \SureCart\Models\Product::where( function( $q ){
						$q->where( 'ID', 'in', (array) $args['selected'] );
					} );
				} else {
					$product_query = \SureCart\Models\Product::where(
						$default_query
					);
				}
				
				$product_query->paginate(
					[
						'per_page' => $entries['per_page'],
						'page'     => $paged,
					]
				);

				if( ! is_wp_error( $product_query ) ){
					$products = $product_query->get();
				}
			}

			foreach( $products as $product ){

				$name = ( isset( $product->id ) ) ? $product->id : '';
				$title = ( isset( $product->name ) ) ? $product->name : '';

				$entries['items'][ $name ] = array(
					'value' => $name,
					'label' => $title,
				);
			}

			//calculate total
			$entries['total'] = ( ! empty( $product_query ) && ! is_wp_error( $product_query ) && isset( $product_query->pagination->count ) ) ? $product_query->pagination->count : count( $entries['items'] );

			return $entries;
		}

	}

endif; // End if class_exists check.