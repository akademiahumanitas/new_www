<?php
function my_render_block_data( $parsed_block ) {
    // Check whether it's a group block.

        if ( 
            'core/heading' === $parsed_block['blockName']
        ) {
            $block   = $parsed_block;
            $content = $block['innerContent'][0];
            $id      = sanitize_title( $content );

            global $toc_headings;
            $toc_headings = is_array( $toc_headings ) ? $toc_headings : array();

            // Add the heading to our global headings array, in the form of
            // `$toc_headings['<HTML id>'] = '<heading text>'`. But it's up to
            // you if you want to use another format.
            $toc_headings[ $id ] = wp_strip_all_tags( $content );

            // Add an `id` attribute to the heading tag.
            $new_content = preg_replace(
                '/<h(\d)( |>)/',
                '<h$1 id="' . esc_attr( $id ) . '"$2',
                $content
            );
            // Alternatively, you can add an anchor, e.g.
            //$anchor      = sprintf( '<a id="toc-%s"></a>', esc_attr( $id ) );
            //$new_content = $anchor . $content;

            // Modify the block's content.
            $parsed_block['innerContent'][0] = $new_content;
        }

    return $parsed_block;
}