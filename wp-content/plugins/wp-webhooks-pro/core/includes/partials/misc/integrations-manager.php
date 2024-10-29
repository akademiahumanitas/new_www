<?php
$integrations_list = WPWHPRO()->integrations->get_integration_list();
$is_license_active = WPWHPRO()->license->is_active();
$is_locally_loaded = get_option( 'wpwhpro_load_local_integrations' );

$columns = isset( $attr['columns_size'] ) ? intval( $attr['columns_size'] ) : 6;

?>
<style>
    .wpwh-filter-actions{
        display: flex;
        flex-flow: column;
        justify-content: center;
        align-items: center;
        margin-bottom: 40px;
    }

    .wpwh-card{
        border-radius: 20px !important;
        height: 100%;
    }

    .wpwh-integration{
        margin-bottom:20px;
    }

    .wpwh-hidden {
        display: none;
    }

    .wpwh-filters {
        width: 100%;
        text-align: center;
    }

    .wpwh-filters ul {
        list-style: none;
        padding: 20px 0;
    }

	.filter-active{
		background: #3b3578 !important;
	}

    .wpwh-filters li {
        display: inline;
        padding: 10px 25px;
        font-size: 14px;
        color: #fff;
        font-weight: 400;
        cursor: pointer;
        text-transform: uppercase;
        letter-spacing: .2em;
        background: #6358dc;
        border-radius: 10px;
    }

    .wpwh-filters li:hover {
        color: rgb(255 255 255 / 80%);
    }

    .integration-workflow-item {
        display: flex;
        border-radius: 20px;
        overflow: hidden;
        height: 100%;
        position: relative;
    }

    .box-shadow {
        box-shadow: 0px 5px 10px 0px rgb(14 10 29 / 12%);
    }

    .integration-workflow-image {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding: 20px;
        background: #6358dc;
    }

    .integration-workflow-image img {
        width: 60px;
        height: 60px;
        max-width: inherit;
    }

    .integration-workflow-image-cover {
        padding: 15px;
        background: #fff;
        border-radius: 20px;
    }

    .integration-workflow-text {
        display: flex;
        padding: 20px;
        flex-flow: column;
        justify-content: space-between;
    }
</style>
<div class="wpwh-integrations-manager">

    <div class="wpwh-filter-actions">
        <div class="wpwh-filters filter-button-group">
            <ul>
                <li data-filter-selector="wpwhcaninstall">Available</li>
                <li data-filter-selector="wpwhinstalled">Installed</li>
                <li data-filter-selector="wpwhstarter">Starter Kit</li>
				<li data-filter-selector="wpwhall">All</li>
            </ul>
        </div>

        <div class="search">
            <input id="wpwh-integration-search" class="wpwh-form-input" type="search" placeholder="<?php echo __( 'Search integrations', 'wp-webhooks' ); ?>" data-search />
        </div>
    </div>

    <div class="row">
        <?php if( ! empty( $integrations_list ) ) : ?>
            <?php foreach( $integrations_list as $integration ) :

                if( 
                    ! isset( $integration['name'] ) 
                    || ! isset( $integration['slug'] ) 
                    || ! isset( $integration['description'] ) 
                    || ! isset( $integration['thumbnail'] )
                ){
                    continue;
                }

                $background = isset( $integration['brand_color'] ) ? $integration['brand_color'] : '#6358dc';
                $permalink = isset( $integration['permalink'] ) ? $integration['permalink'] : '';
                $is_starter = ( isset( $integration['starter'] ) && $integration['starter'] ) ? 'yes' : 'no';
                $is_installed = WPWHPRO()->integrations->is_integration_installed( $integration['slug'] );
                $can_install = ( $integration['can_install'] ) ? true : false;
                $integration_slug = sanitize_title( $integration['slug'] );

            ?>
                <div id="integration-<?php echo $integration_slug; ?>" class="col-xl-<?php echo $columns; ?> col-lg-<?php echo $columns; ?> col-md-<?php echo $columns; ?> wpwh-integration <?php echo ( $is_installed ) ? 'is_installed' : ''; ?>" data-filter-item data-filter-is-starter="<?php echo $is_starter; ?>" data-filter-can-install="<?php echo ( $can_install ) ? 'yes' : 'no'; ?>" data-filter-is-installed="<?php echo ( $is_installed ) ? 'yes' : 'no'; ?>" data-filter-name="<?php echo $integration['slug']; ?>">
                    <div class="wpwh-card">
                        <div class="integration-workflow-item box-shadow">
                            <div class="integration-workflow-image" style="background:<?php echo $background; ?>">
                                <div class="integration-workflow-image-cover box-shadow">
                                    <img width="256px" height="256px" src="<?php echo $integration['thumbnail']; ?>" class="attachment-entry-fullwidth size-entry-fullwidth wp-post-image" alt="The Formidable Logo for our WP Webhooks integration" sizes="(max-width: 256px) 100vw, 256px" />
                                </div>
                            </div>
                            <div class="integration-workflow-text">
                                <div>
                                    <strong><?php echo $integration['name']; ?></strong>
                                    <div><?php echo $integration['description']; ?></div>
                                </div>
                                <div class="wpwh-card__actions">

                                    <?php if( $is_installed ) : ?>
                                        <?php if( $is_locally_loaded !== 'yes' ) : ?>
                                        <a href="#" class="text-danger wpwh-integrations-manage text-uppercase" data-wpwh-integration-action="uninstall" data-wpwh-integration-slug="<?php echo $integration_slug; ?>" data-tippy data-tippy-placement="top" data-tippy-content="<?php echo __( 'Deletes only the integration files. Your triggers and actions are stored wihtin the database.', 'wp-webhooks' ); ?>">
                                            <span><?php echo __( 'Delete', 'wp-webhooks' ); ?></span>
                                        </a>
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <?php if( $can_install ) : ?>
											<?php if( $is_license_active ) : ?>
                                            <a href="#" class="text-green wpwh-integrations-manage" data-wpwh-integration-action="install" data-wpwh-integration-slug="<?php echo $integration_slug; ?>">
                                                <span><?php echo __( 'Install', 'wp-webhooks' ); ?></span>
                                            </a>
											<?php else : ?>
												<a href="<?php echo get_admin_url(); ?>options-general.php?page=wp-webhooks-pro&wpwhprovrs=license" class="text-primary">
													<span><?php echo __( 'Activate License', 'wp-webhooks' ); ?></span>
												</a>
											<?php endif; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    
                                    <?php if( $permalink ) : ?>
                                        <a href="<?php echo esc_url( $permalink ); ?>" target="_blank" class="text-success">
                                        <?php echo __( 'More Info', 'wp-webhooks' ); ?>
                                        </a>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="wpwhpro-empty">
                <?php echo __( 'There are currently no extensions available.', 'wp-webhooks' ); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
    jQuery(document).ready( function() {   

        $('.wpwh-filters li').on('click', function() {
			var $selector = $(this);
            var selectorval = $selector.data('filter-selector');

			$('.wpwh-filters li').removeClass('filter-active');
			$selector.addClass('filter-active');

            if( selectorval != '' ) {
                $('#wpwh-integration-search').val( selectorval ).trigger("input");
            }
        });

        $('[data-search]').on('input', function() {
            var searchVal = $(this).val();
            var filterItems = $('[data-filter-item]');

            if ( searchVal != '' && searchVal != 'wpwhall' ) {

                if( searchVal == 'wpwhinstalled' ){
                    filterItems.addClass('wpwh-hidden');
                    $('[data-filter-item][data-filter-is-installed="yes"]').removeClass('wpwh-hidden');
                } else if( searchVal == 'wpwhcaninstall' ){
                    filterItems.addClass('wpwh-hidden');
                    $('[data-filter-item][data-filter-can-install="yes"]').removeClass('wpwh-hidden');
                } else if( searchVal == 'wpwhstarter' ){
                    filterItems.addClass('wpwh-hidden');
                    $('[data-filter-item][data-filter-is-starter="yes"]').removeClass('wpwh-hidden');
                } else {
                    filterItems.addClass('wpwh-hidden');
                    $('[data-filter-item][data-filter-name*="' + searchVal.toLowerCase() + '"]').removeClass('wpwh-hidden');
                }
                
            
            } else {
                filterItems.removeClass('wpwh-hidden');
            }
        });

		//initialize starter kit
		$('[data-filter-selector="wpwhstarter"]').click();
    });
</script>