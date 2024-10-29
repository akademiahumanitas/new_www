<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_jetpack_crm_Helpers_jpcrm_helpers' ) ) :
		/**
		 * Load the Jetpack CRM helpers
		 *
		 * @since 6.0.3
		 * @author Ironikus <info@ironikus.com>
		 */
	class WP_Webhooks_Integrations_jetpack_crm_Helpers_jpcrm_helpers {

		public function get_query_companies( $entries, $query_args, $args ) {
			global $zbs;

			$available_companies = array();
			
			$company_query_args = array(				

				// Search/Filtering (leave as false to ignore)
				'searchPhrase' 		=> isset( $args['s'] ) ? $args['s'] : false,
				'inArr' 			=> isset( $args['selected'] ) ? $args['selected'] : false,
				'simplified'		=> false,
				'withCustomFields'	=> false,
				'withQuotes' 		=> false,
				'withInvoices' 		=> false,
				'withTransactions' 	=> false,
				'withLogs' 			=> false,
				'withLastLog'		=> false,
				'withTags' 			=> false,
				'withOwner' 		=> false,
				'withValues'		=> false,
				'sortOrder' 		=> 'DESC',
				'page'				=> isset( $args['paged'] ) ? $args['paged'] : 1,
				'perPage'			=> isset( $entries['per_page'] ) ? $entries['per_page'] : 20,

				//More possible values
				//'isTagged'		=> $hasTagIDs,
				//'quickFilters'  => $quickFilters,
				//'sortByField' 	=> $sortByField,
				//'ignoreowner'		=> zeroBSCRM_DAL2_ignoreOwnership(ZBS_TYPE_COMPANY)


			);

			$companies = $zbs->DAL->companies->getCompanies( $company_query_args );

			$company_query_args_count = $company_query_args;
			$company_query_args_count['count'] = true;
			unset( $company_query_args_count['page'] ); //important as otherwise the count query breaks
			$company_count = $zbs->DAL->companies->getCompanies( $company_query_args_count );

			foreach( $companies as $company ){
				if( 
					is_array( $company ) 
					&& isset( $company['id'] )
					&& isset( $company['name'] )
				){
					$available_companies[ $company['id'] ] = $company['name'];
				}
			}

			foreach ( $available_companies as $name => $title ) {
				$entries['items'][ $name ] = array(
					'value' => $name,
					'label' => $title,
				);
			}

			// calculate total
			$entries['total'] = $company_count;

			return $entries;

		}

		public function get_query_contacts( $entries, $query_args, $args ) {
			global $zbs;

			$available_companies = array();
			
			$contact_query_args = array(				

				// Search/Filtering (leave as false to ignore)
				'searchPhrase' 		=> isset( $args['s'] ) ? $args['s'] : false,
				'inArr' 			=> isset( $args['selected'] ) ? $args['selected'] : false,
				'simplified' 		=> false,
				'withCustomFields' 	=> false,
				'sortByField' 		=> 'fullname',
				'sortOrder' 		=> 'DESC',
				'page'				=> isset( $args['paged'] ) ? $args['paged'] : 1,
				'perPage'			=> isset( $entries['per_page'] ) ? $entries['per_page'] : 20,

			);

			$contacts = $zbs->DAL->contacts->getContacts( $contact_query_args );

			$contact_query_args_count = $contact_query_args;
			$contact_query_args_count['count'] = true;
			unset( $contact_query_args_count['page'] ); //important as otherwise the count query breaks
			$company_count = $zbs->DAL->contacts->getContacts( $contact_query_args_count );

			foreach( $contacts as $contact ){
				if( 
					is_array( $contact ) 
					&& isset( $contact['id'] )
					&& isset( $contact['email'] )
				){

					$display_name = '';
					if( isset( $contact['name'] ) && ! empty( $contact['name'] ) ){
						$display_name .= $contact['name'];
					} else {
						$display_name .= __( 'Undefined', 'wp-webhooks' );
					}

					$display_name .= ' (' . $contact['email'] . ')';
					
					$available_companies[ $contact['id'] ] = $display_name;
				}
			}

			foreach ( $available_companies as $name => $title ) {
				$entries['items'][ $name ] = array(
					'value' => $name,
					'label' => $title,
				);
			}

			// calculate total
			$entries['total'] = $company_count;

			return $entries;

		}

		public function get_query_tags( $entries, $query_args, $args ) {

			// bail for paged values as everything is returned at once
			if ( isset( $args['paged'] ) && (int) $args['paged'] > 1 ) {
				return $entries;
			}

			$type           = constant( $query_args['type'] );

			$available_tags = array();
			
			global $zbs;
			$tags = $zbs->DAL->getTagsForObjType(
				array(
					'objtypeid'    => $type,
					'excludeEmpty' => false,
					'withCount'    => true,
					'ignoreowner'  => zeroBSCRM_DAL2_ignoreOwnership( $type ),
				)
			);

			if ( ! empty( $tags ) ) {
				foreach ( $tags as $tag ) {
					$tag_id                    = intval( $tag['id'] );
					$available_tags[ $tag_id ] = esc_html( $tag['name'] );
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
endif;
