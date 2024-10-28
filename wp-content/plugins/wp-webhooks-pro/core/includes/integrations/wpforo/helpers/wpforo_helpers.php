<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_wpforo_Helpers_wpforo_helpers' ) ) :
	/**
	 * Load the wpForo helpers
	 *
	 * @since 6.0.3
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wpforo_Helpers_wpforo_helpers {

		public function get_query_groups( $entries, $query_args, $args ){

			//bail for paged values as everything is returned at once
			if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
				return $entries;
			}

            $usergroups = WPF()->usergroup->get_usergroups();

			foreach( $usergroups as $group ){

				if( 
					! is_array( $group ) 
					|| ! isset( $group['groupid'] )
					|| ! isset( $group['name'] )
				){
					continue;
				}

				//skip search values that don't occur if set
				if( isset( $args['s'] ) && $args['s'] !== '' ){
					if( 
						strpos( $group['groupid'], $args['s'] ) === false
						&& strpos( $group['name'], $args['s'] ) === false
					){
						continue;
					}
				}

				//skip unselected values in a selected statement
				if( isset( $args['selected'] ) && ! empty( $args['selected'] ) ){
					if( ! in_array( $group['groupid'], (array) $args['selected'] ) ){
						continue;
					}
				}

				$entries['items'][ $group['groupid'] ] = array(
					'value' => $group['groupid'],
					'label' => $group['name'],
				);
			}

			//calculate total
			$entries['total'] = count( $entries['items'] );

			//set all items to be visible on one page
			$entries['per_page'] = count( $entries['items'] );

			return $entries;
		}

		public function get_query_levels( $entries, $query_args, $args ){

			//bail for paged values as everything is returned at once
			if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
				return $entries;
			}

            $levels = WPF()->member->levels();

			foreach( $levels as $level ){

				$level_title = esc_attr__( 'Level', 'wpforo' ) . ' ' . $level . ' - ' . WPF()->member->rating( $level, 'title' );

				//skip search values that don't occur if set
				if( isset( $args['s'] ) && $args['s'] !== '' ){
					if( 
						strpos( $level, $args['s'] ) === false
						&& strpos( $level_title, $args['s'] ) === false
					){
						continue;
					}
				}

				//skip unselected values in a selected statement
				if( isset( $args['selected'] ) && ! empty( $args['selected'] ) ){
					if( ! in_array( $level, (array) $args['selected'] ) ){
						continue;
					}
				}

				$entries['items'][ $level ] = array(
					'value' => $level,
					'label' => $level_title,
				);
			}

			//calculate total
			$entries['total'] = count( $entries['items'] );

			//set all items to be visible on one page
			$entries['per_page'] = count( $entries['items'] );

			return $entries;
		}

		public function get_query_forums( $entries, $query_args, $args ){

			//bail for paged values as everything is returned at once
			if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
				return $entries;
			}

            $forums = WPF()->forum->get_forums( array( 'type' => 'forum' ) );

			foreach( $forums as $forum ){

				if( ! is_array( $forum ) || ! isset( $forum['forumid'] ) || ! isset( $forum['title'] ) ){
					continue;
				}

				//skip search values that don't occur if set
				if( isset( $args['s'] ) && $args['s'] !== '' ){
					if( 
						strpos( $forum['forumid'], $args['s'] ) === false
						&& strpos( $forum['title'], $args['s'] ) === false
					){
						continue;
					}
				}

				//skip unselected values in a selected statement
				if( isset( $args['selected'] ) && ! empty( $args['selected'] ) ){
					if( ! in_array( $forum['forumid'], (array) $args['selected'] ) ){
						continue;
					}
				}

				$entries['items'][ $forum['forumid'] ] = array(
					'value' => $forum['forumid'],
					'label' => $forum['title'],
				);
			}

			//calculate total
			$entries['total'] = count( $entries['items'] );

			//set all items to be visible on one page
			$entries['per_page'] = count( $entries['items'] );

			return $entries;
		}

	}
endif;
