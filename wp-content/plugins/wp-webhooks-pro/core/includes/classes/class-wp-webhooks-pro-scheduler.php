<?php

/**
 * WP_Webhooks_Pro_Scheduler Class
 *
 * This class contains all of the Action Scheduler related customization
 * https://actionscheduler.org/
 *
 * @since 6.0
 */

/**
 * The Action Scheduler Wrapper
 *
 * @since 6.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Scheduler {

    /**
     * The wrapper to fire a scheduled action using 
     * the action scheduler 
     *
     * @since 6.0
     * @param array $args
     * @return array
     */
	public function schedule_single_action( $args ){
        $response = array(
            'success' => false,
            'msg' => __( 'The action was not scheduled.', 'wp-webhooks' ),
        );

        if( function_exists( 'as_schedule_single_action' ) ){

            /**
             * Allow filtering the arguments for scheduling the action
             */
            $args = apply_filters( 'wpwhpro/scheduler/schedule_single_action/args', $args );

            if( 
                isset( $args['timestamp'] )
                && isset( $args['hook'] )
                && isset( $args['attributes'] )
             ){

                $timestamp = ( isset( $args['timestamp'] ) && is_numeric( $args['timestamp'] ) ) ? intval( $args['timestamp'] ) : array();
                $attributes = ( isset( $args['attributes'] ) && is_array( $args['attributes'] ) ) ? $args['attributes'] : array();
                $group = ( isset( $args['group'] ) && ! empty( $args['group'] ) ) ? $args['group'] : '';

                $scheduled_id = as_schedule_single_action( $timestamp, $args['hook'], $attributes, $group );

                if( is_numeric( $scheduled_id ) && ! empty( $scheduled_id ) ){
                    $response['success'] = true;
                    $response['msg'] = __( 'The action was successfully scheduled.', 'wp-webhooks' );
                    $response['content'] = array(
                        'wpwh_schedule' => 'action',
                        'scheduled_id' => $scheduled_id,
                        'timestamp' => $timestamp,
                        'log_url' => $this->get_scheduler_page_url( array( 's' => $args['hook'] ) ),
                    );
                }

             } else {
                $response['msg'] = __( 'The action was not scheduled as one of the following arguments was not provided: timestamp, attributes, hook.', 'wp-webhooks' );
             }
            
        } else {
            $response['msg'] = __( 'The action was not scheduled as the action scheduler is not available.', 'wp-webhooks' );
        }

        /**
         * Filter the response of the schedule_single_action() function
         * 
         * @since 6.0
         * @param array $response The response data
         * @param array $arguments The arguments
         */
        return apply_filters( 'wpwhpro/scheduler/schedule_single_action', $response, $args );
    }

    /**
     * Retrieve the page URL for the action scheduler
     *
     * @since 6.0
     * @return string The page URL
     */
    public function get_scheduler_page_url( $args = array() ){

        $query_args = array(
			'page'   => 'action-scheduler',
			'order'  => 'desc',
		);

        $query_args = array_merge( $query_args, $args );

        /**
         * Filter the query args used for the scheduler page URL
         * 
         * @since 6.0
         * @param array $query_args
         * @param array $args
         */
        $query_args = apply_filters( 'wpwhpro/scheduler/get_scheduler_page_url/query_args', $query_args, $args );

        $scheduler_url = add_query_arg( $query_args, admin_url( 'tools.php' ) );

        /**
         * Filter the scheduler page URL
         * 
         * @since 6.0
         * @param string $scheduler_url
         * @param array $query_args
         * @param array $args
         */
        return apply_filters( 'wpwhpro/scheduler/get_scheduler_page_url', $scheduler_url, $query_args, $args );
    }

    /**
     * Retrieve the next action for a given schedule
     *
     * @since 6.0
     * @return string The page URL
     */
    public function get_next_action( $args = array() ){
        $next_action = null;

        if( function_exists( 'as_next_scheduled_action' ) && isset( $args['hook'] ) ){
            $next_action = as_next_scheduled_action( $args['hook'] );
        }

        /**
         * Filter the next action data
         * 
         * @since 6.1.4
         * @param mixed $next_action
         * @param array $args
         */
        return apply_filters( 'wpwhpro/scheduler/get_next_action', $next_action, $args );
    }

}
