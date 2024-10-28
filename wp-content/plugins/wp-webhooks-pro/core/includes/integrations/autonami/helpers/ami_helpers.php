<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_autonami_Helpers_ami_helpers' ) ) :

	/**
	 * Load the FuentCRM helpers
	 *
	 * @since 5.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_autonami_Helpers_ami_helpers {

		public function get_query_lists( $entries, $query_args, $args ) {

			// bail for paged values as everything is returned at once
			if ( isset( $args['paged'] ) && (int) $args['paged'] > 1 ) {
				return $entries;
			}

			$available_lists = array();

			if( class_exists( 'BWFCRM_Lists' ) ){
				$lists = \BWFCRM_Lists::get_lists();

				if ( ! empty( $lists ) ) {
					foreach ( $lists as $list ) {
						$list_id = intval( $list['ID'] );
						$available_lists[ $list_id ] = esc_html( $list['name'] );
					}
				}
			}

			foreach ( $available_lists as $name => $title ) {

				// skip search values that don't occur if set
				if ( isset( $args['s'] ) && $args['s'] !== '' ) {
					if ( strpos( $name, $args['s'] ) === false
						&& strpos( $title, $args['s'] ) === false
					) {
						continue;
					}
				}

				// skip unselected values in a selected statement
				if ( isset( $args['selected'] ) && ! empty( $args['selected'] ) ) {
					if ( ! in_array( $name, $args['selected'] ) ) {
						continue;
					}
				}

				$entries['items'][ $name ] = array(
					'value' => $name,
					'label' => $title,
				);
			}

			// calculate total
			$entries['total'] = count( $entries['items'] );

			// set all items to be visible on one page
			$entries['per_page'] = count( $entries['items'] );

			return $entries;
		}

		public function get_query_tags( $entries, $query_args, $args ) {

			// bail for paged values as everything is returned at once
			if ( isset( $args['paged'] ) && (int) $args['paged'] > 1 ) {
				return $entries;
			}

            $available_tags = array();
			
			if( class_exists( 'BWFCRM_Tag' ) ){
				$tags = \BWFCRM_Tag::get_tags();

				if ( ! empty( $tags ) ) {
					foreach ( $tags as $tag ) {
						$tag_id = intval( $tag['ID'] );
						$available_tags[ $tag_id ] = esc_html( $tag['name']);
					}
				}
			}

			foreach ( $available_tags as $name => $title ) {

				// skip search values that don't occur if set
				if ( isset( $args['s'] ) && $args['s'] !== '' ) {
					if ( strpos( $name, $args['s'] ) === false
						&& strpos( $title, $args['s'] ) === false
					) {
						continue;
					}
				}

				// skip unselected values in a selected statement
				if ( isset( $args['selected'] ) && ! empty( $args['selected'] ) ) {
					if ( ! in_array( $name, $args['selected'] ) ) {
						continue;
					}
				}

				$entries['items'][ $name ] = array(
					'value' => $name,
					'label' => $title,
				);
			}

			// calculate total
			$entries['total'] = count( $entries['items'] );

			// set all items to be visible on one page
			$entries['per_page'] = count( $entries['items'] );

			return $entries;
		}

	}

endif; // End if class_exists check.
