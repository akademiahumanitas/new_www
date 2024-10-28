<?php

if( ! isset( $log_id ) || empty( $log_id ) ){
	wp_die( __( 'The given log is not a valid log.', 'wp-webhooks' ) );
}

$log_id = intval( $log_id );
$log_data = WPWHPRO()->logs->logs_query( array( 'items__in' => $log_id ) );

if(
	empty( $log_data )
	|| ! isset( $log_data['items'] )
	|| empty( $log_data['items'] )
){
	wp_die( __( 'We could not locate the log.', 'wp-webhooks' ) );
}

$log = null;
foreach( $log_data['items'] as $single_log ){
	$log = $single_log;
	break;
}

if( empty( $log ) ){
	wp_die( __( 'We have problems fetching the log.', 'wp-webhooks' ) );
}

?>
<div class="wpwh-container">
  <div class="wpwh-title-area mb-4">
		<h2><?php echo sprintf( __( 'Log #%d', 'wp-webhooks' ), $log_id ); ?></h2>
		<p><?php echo sprintf( __( 'This page contains further data about the given log. Please see the details below.', 'wp-webhooks' ), WPWHPRO()->settings->get_page_title() ); ?></p>
  </div>

  <?php
  		$log_time = date( 'F j, Y, g:i a', strtotime( $log->log_time ) );
		$log_version = '';
		$message = htmlspecialchars( base64_decode( $log->message ) );
		$content_backend = base64_decode( $log->content );
		$identifier = '';
		$webhook_type = '';
		$webhook_url_name = '';
		$endpoint_response = '';
		$content = '';

		if( WPWHPRO()->helpers->is_json( $content_backend ) ){
				$single_data = json_decode( $content_backend, true );
				if( $single_data && is_array( $single_data ) ){

					if( isset( $single_data['request_data'] ) ){
						$content = WPWHPRO()->logs->sanitize_array_object_values( $single_data['request_data'] );
					}

					if( isset( $single_data['response_data'] ) ){
						$endpoint_response = WPWHPRO()->logs->sanitize_array_object_values( $single_data['response_data'] );
					}

					if( isset( $single_data['identifier'] ) ){
						$identifier = htmlspecialchars( $single_data['identifier'] );
					}

					if( isset( $single_data['webhook_type'] ) ){
						$webhook_type = htmlspecialchars( $single_data['webhook_type'] );
					}

					if( isset( $single_data['webhook_name'] ) ){
						$webhook_name = htmlspecialchars( $single_data['webhook_name'] );
					}

					if( isset( $single_data['webhook_url_name'] ) ){
						$webhook_url_name = htmlspecialchars( $single_data['webhook_url_name'] );
					}

					if( isset( $single_data['log_version'] ) ){
						$log_version = htmlspecialchars( $single_data['log_version'] );
					}
				}
		}

		?>
		<div class="log-content-wrapper">
			<div class="log-content--body">
				<div class="row pt-4">
					<div class="wpwh-table-container">
						<table class="wpwh-table wpwh-table--sm">
							<thead>
								<tr>
									<th><?php echo __( 'Webhook Name', 'wp-webhooks' ); ?></th>
									<th><?php echo __( 'Endpoint', 'wp-webhooks' ); ?></th>
									<th><?php echo __( 'Type', 'wp-webhooks' ); ?></th>
									<th><?php echo __( 'Date & Time', 'wp-webhooks' ); ?></th>
									<th><?php echo __( 'Log Version', 'wp-webhooks' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><?php echo sanitize_title( $webhook_url_name ); ?></td>
									<td><?php echo sanitize_title( $webhook_name ); ?></td>
									<td><?php echo $webhook_type; ?></td>
									<td><?php echo $log_time; ?></td>
									<td><?php echo $log_version; ?></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="row pt-4">
					<div class="col-md-12">
						<h4><?php echo __( 'Identifier', 'wp-webhooks' ); ?></h4>
						<p><?php echo __( 'The identifier contains either an IP if the request was an action, or the webhook URL if it was a trigger. It can also contain "test" in case you send a demo request.', 'wp-webhooks' ); ?></p>
						<pre><?php echo $identifier; ?></pre>
					</div>
				</div>
				<?php if( isset( $content['content_type'] ) && ! empty( $content['content_type'] ) ) : ?>
					<div class="row pt-4">
						<div class="col-md-12">
							<h4><?php echo __( 'Content Type', 'wp-webhooks' ); ?></h4>
							<pre><?php echo $content['content_type']; ?></pre>
						</div>
					</div>
				<?php endif; ?>
				<div class="row pt-4">
					<?php if( $webhook_type === 'action' || $webhook_type === 'flow_action' ) : 
					
					$response_data = ( isset( $single_data['response_data'] ) && isset( $single_data['response_data']['arguments'] ) ) ? $single_data['response_data']['arguments'] : null;

					?>

						<div class="col-md-<?php echo ( $response_data ) ? '6' : '12'; ?>">
							<h4><?php echo __( 'Incoming data:', 'wp-webhooks' ); ?></h4>
							<p><?php echo sprintf( __( 'The JSON down below contains the full data that was sent to the webhook URL of %s, after data mapping was applied.', 'wp-webhooks' ), WPWHPRO()->settings->get_page_title()); ?></p>
							<?php if( isset( $content['content'] ) ) : ?>
								<pre id="wpwhpro-log-json-output-response-<?php echo $log->id; ?>"><?php echo json_encode( $content['content'], JSON_PRETTY_PRINT ); ?></pre>
							<?php endif; ?>
						</div>

						<?php if( $response_data ) : ?>
							<div class="col-md-6">
								<h4><?php echo __( 'Outgoing data:', 'wp-webhooks' ); ?></h4>
								<p><?php echo __( 'The JSON down below contains the whole data we sent back to your webhook action caller.', 'wp-webhooks' ); ?></p>
								<?php if( ! empty( $content ) ) : ?>
									<pre id="wpwhpro-log-json-output-payload-<?php echo $log->id; ?>"><?php echo json_encode( $response_data, JSON_PRETTY_PRINT ); ?></pre>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					<?php else : 

					$trigger_content = ( ! empty( $single_data['request_data'] ) && ! is_string( $single_data['request_data'] ) ) ? (array) $single_data['request_data'] : '';

					if( isset( $trigger_content['body'] ) && WPWHPRO()->helpers->is_json( $trigger_content['body'] ) ){
						$trigger_content['body'] = json_decode( $trigger_content['body'], true );
					}

					$trigger_header = ( isset( $trigger_content['headers'] ) ) ? htmlspecialchars( json_encode( $trigger_content['headers'], JSON_PRETTY_PRINT ) ) : '';
					$trigger_payload = ( isset( $trigger_content['body'] ) ) ? htmlspecialchars( json_encode( $trigger_content['body'], JSON_PRETTY_PRINT ) ) : '';
					
					?>
						<div class="col-md-6">
							<h4><?php echo __( 'Outgoing data:', 'wp-webhooks' ); ?></h4>
							<p><?php echo __( 'The JSON down below contains the whole request we sent based on your fired trigger. You will find the data within the body key.', 'wp-webhooks' ); ?></p>
							<?php if( ! empty( $content ) ) : ?>
								<pre id="wpwhpro-log-json-output-payload-<?php echo $log->id; ?>"><?php echo $trigger_payload; ?></pre>
							<?php endif; ?>
						</div>
						<div class="col-md-6">
							<h4><?php echo __( 'Endpoint Response:', 'wp-webhooks' ); ?></h4>
							<p><?php echo __( 'In JSON contains the data we got back from the server where we sent the webhook request to.', 'wp-webhooks' ); ?></p>
							<?php if( isset( $endpoint_response ) ) : ?>
								<pre id="wpwhpro-log-json-output-response-<?php echo $log->id; ?>"><?php echo json_encode( $endpoint_response, JSON_PRETTY_PRINT ); ?></pre>
							<?php endif; ?>
						</div>
						<div class="col-md-6">
							<h4><?php echo __( 'Outgoing header data:', 'wp-webhooks' ); ?></h4>
							<p><?php echo __( 'The JSON down below contains all headers that have been sent along with the trigger.', 'wp-webhooks' ); ?></p>
							<?php if( ! empty( $trigger_header ) ) : ?>
								<pre id="wpwhpro-log-json-output-header-<?php echo $log->id; ?>"><?php echo $trigger_header; ?></pre>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>

</div>