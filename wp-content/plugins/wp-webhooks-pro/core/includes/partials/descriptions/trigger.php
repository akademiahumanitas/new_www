<?php 

/**
 * The global template used for a single webhook trigger
 *
 * @since 4.2.2
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 * @version 1.0.0
 */

$webhook_name = '';
if( isset( $data['webhook_name'] ) ){
    $webhook_name = esc_html( $data['webhook_name'] );
}

$webhook_slug = '';
if( isset( $data['webhook_slug'] ) ){
    $webhook_slug = esc_html( $data['webhook_slug'] );
}

$trigger_hooks = array();
if( isset( $data['trigger_hooks'] ) ){
    $trigger_hooks = $data['trigger_hooks'];
}

$post_delay = false;
if( isset( $data['post_delay'] ) ){
    $post_delay = (bool) $data['post_delay'];
}

$tipps = array();
if( isset( $data['tipps'] ) ){
    $tipps = $data['tipps'];
}

?>
<?php if( isset( $data['before_description'] ) ) : ?>
<?php echo __(  $data['before_description'], 'wp-webhooks' ); ?>
<?php endif; ?>
<br>
<?php echo sprintf( __( 'This description is made for the <strong>%1$s</strong> (%2$s) webhook trigger.', 'wp-webhooks' ), $webhook_name, $webhook_slug ); ?>
<br><br>
<h4><?php echo sprintf( __( 'How to use the <strong>%1$s</strong> (%2$s) trigger', 'wp-webhooks' ), $webhook_name, $webhook_slug ); ?></h4>
<ol>
    <li><?php echo __( 'To get started, please copy the webhook URL of the endpoint you want to send the data to. This can be an external URL from your third-party provider, or any other URL that accepts webhooks.', 'wp-webhooks' ); ?></li>
    <li><?php echo __( 'Once you have this URL, please click on the <strong>Add Webhook URL</strong> button and place the URL into the <strong>Webhook URL</strong> field.', 'wp-webhooks' ); ?></li>
    <li><?php echo __( 'For a better identification of the webhook URL, we recommend to also fill the <strong>Webhook Name</strong> field. This field will be used as the slug for your webhook URL. In case you leave it empty, we will automatically generate a random number as an identifier.', 'wp-webhooks' ); ?></li>
    <li><?php echo sprintf( __( 'After you added your <strong>Webhook URL</strong>, press the <strong>Add for %1$s</strong> button to finish the setup.', 'wp-webhooks' ), $webhook_slug ); ?></li>
    <li><?php echo __( 'That\'s it! Now you are able to recieve data on the URL once the trigger fires.', 'wp-webhooks' ); ?></li>
    <li><?php echo __( 'Next to the <strong>Webhook URL</strong>, you will find three dots that contain further actions once you click on it. Within these actions you can test your webhook trigger, customize the data and request, as well as deactivate or delete it.', 'wp-webhooks' ); ?></li>
</ol>
<br><br>

<?php if( ! empty( $trigger_hooks ) ) : ?>
<h4><?php echo __( 'When does this trigger fire?', 'wp-webhooks' ); ?></h4>
<br>
<?php echo sprintf( __( 'This trigger is registered on the following hooks of the <strong>%1$s</strong> trigger:', 'wp-webhooks' ), $webhook_name ); ?>
<ol>
    <?php foreach( $trigger_hooks as $trigger_hook ) : ?>
        <li>
            <?php if( isset( $trigger_hook['url'] ) && ! empty( $trigger_hook['url'] ) ) : ?>
                <a href="<?php echo esc_url( $trigger_hook['url'] ); ?>" target="_blank" title="<?php echo __( 'Visit documentation', 'wp-webhooks' ); ?>"><strong><?php echo isset( $trigger_hook['hook'] ) ? $trigger_hook['hook'] : ''; ?></strong></a>
            <?php else : ?>
                <strong><?php echo isset( $trigger_hook['hook'] ) ? $trigger_hook['hook'] : ''; ?></strong>
            <?php endif; ?>
            <?php if( isset( $trigger_hook['description'] ) ) : ?>
                <?php echo $trigger_hook['description']; ?>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ol>
<?php endif; ?>

<?php if( $post_delay ) : ?>
<?php echo sprintf( __( '<strong>IMPORTANT</strong>: Please note that this webhook does not fire, by default, once the actual trigger (%1$s) is fired, but as soon as the WordPress <strong>shutdown</strong> hook fires. This is important since we want to allow third-party plugins to make their relevant changes before we send over the data. To deactivate this functionality, please go to our <strong>Settings</strong> and activate the <strong>Deactivate Post Trigger Delay</strong> settings item. This results in the webhooks firing straight after the initial hook is called.', 'wp-webhooks' ), $webhook_slug ); ?>
<br><br>
<?php endif; ?>

<?php if( isset( $data['how_to'] ) ) : ?>
    <h4><?php echo __( 'How to fire the trigger?', 'wp-webhooks' ); ?></h4>
    <br>
    <?php echo $data['how_to']; ?>
<?php endif; ?>

<h4><?php echo __( 'Tipps', 'wp-webhooks' ); ?></h4>
<ol>
    <?php if( ! empty( $tipps ) ) : ?>
        <?php foreach( $tipps as $tipp ) : ?>
            <li><?php echo $tipp; ?></li>
        <?php endforeach; ?>
    <?php endif; ?>
    <li><?php echo __( 'In case you don\'t need a specified webhook URL at the moment, you can simply deactivate it by clicking the <strong>Deactivate</strong> link within the Actions menu right next to the <strong>Webhook URL</strong>. This results in the specified URL not being fired once the trigger fires.', 'wp-webhooks' ); ?></li>
    <li><?php echo __( 'You can use the <strong>Send demo</strong> feature within the <strong>Actions</strong> menu to send a static request to your specified <strong>Webhook URL</strong>. Please note that the data sent within the request might differ from your live data as it is predefined by us.', 'wp-webhooks' ); ?></li>
    <li><?php echo __( 'Via the <strong>Settings</strong> feature within the <strong>Action</strong> menu right next to your <strong>Webhook URL</strong>, you can use customize the functionality of the request. It contains certain default settings like changing the request type the data is sent in, or custom settings, depending on your trigger. An explanation for each setting is right next to it. (Please don\'t forget to save the settings once you changed them - the button is at the end of the popup.)', 'wp-webhooks' ); ?></li>
    <li><?php echo __( 'You can also check the response you get from the webhook call. To check it, simply open the console of your browser and you will find an entry there, which gives you all the details about the response.', 'wp-webhooks' ); ?></li>
</ol>
<br><br>

<?php echo __( 'In case you would like to learn more about our plugin, please check out our documentation at:', 'wp-webhooks' ); ?>
<br>
<a title="<?php echo __( 'Go to wp-webhooks.com/docs', 'wp-webhooks' ); ?>" target="_blank" href="https://wp-webhooks.com/docs/article-categories/get-started/">https://wp-webhooks.com/docs/article-categories/get-started/</a>