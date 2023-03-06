<?php
function auxin_products_grid( $args= array() ) {

    global $post;

    $defaults = array (
    	'product_type'          => '',    // available values : recent, featured, top_rated, sale, deal, best_selling
        'title'                 => '',    // header title (required)
        'subtitle'              => '',    // header subtitle
        'cat'                   => '',
        'num'                   => '8',   // max generated entry
        'order_by'              => 'date',
        'order'                 => 'DESC',
        'only_products__in'     => '',   // display only these post IDs. array or string comma separated
        'include'               => '',    // include these post IDs in result too. array or string comma separated
        'exclude'               => '',    // exclude these post IDs from result. array or string comma separated
        'offset'                => '',
        'desktop_cnum'          => 4,
        'post_type'             => 'product',
        'taxonomy'              => 'product_cat', // the taxonomy that we intent to display in post info
        'tax_args'              => '',
        'content_width'         => '',
        'extra_classes'         => '',
        'terms'                 => ' ',
        'universal_id'          => '',
        'use_wp_query'          => false, // true to use the global wp_query, false to use internal custom query
        'reset_query'           => true,
        'wp_query_args'         => array(), // additional wp_query args
        'query_args'            => array(),
        'custom_wp_query'       => '',
        'base'                  => 'aux_products_grid',
        'base_class'            => 'aux-widget-products-grid'
    );

    $args = wp_parse_args( $args, $defaults );

    if( ( empty( $args['terms'] ) || $args['terms'] === "all" ) && !empty( $args['cat'] ) ) {
        $tax_args = array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $args['cat']
            )
        );
    } else if ( ! empty( $args['terms'] ) ) {
        $tax_args = array(
            array(
                'taxonomy' => $args['taxonomy'],
                'field'    => 'term_id',
                'terms'    => $args['terms']
            )
        );
    } else if ( empty( $args['cat'] ) ) {
        $tax_args = array();
    }

    if ( empty( $args['cat'] ) && $args['taxonomy'] == 'product_tag'  && $args['terms'] === "all" ) {
        $tax_args = [];
    }

    $query_args = array(
        'post_type'             => 'product',
        'posts_per_page'        => $args['num'],
        'orderby'               => $args['order_by'],
        'order'                 => $args['order'],
        'tax_query'             => $tax_args,
        'post_status'           => 'publish',
        'include_posts__in'     => $args['include'], // include posts in this list
        'posts__not_in'         => $args['exclude'], // exclude posts in this list
        'posts__in'             => $args['only_products__in'], // only posts in this list
        'exclude_without_media' => $args['exclude_without_media']
    );

    if ( isset( $args['meta_key'] ) ) {
        $query_args['meta_key'] = $args['meta_key'];
    }


    // add the additional query args if available
    if( $args['query_args'] ){
        $query_args = wp_parse_args( $query_args, $args['query_args'] );
    }

    if ( $args['product_type'] ) {
        switch ( $args['product_type'] ) {
            case 'featured':
                $query_args['tax_query'][] = array(
                    'taxonomy' => 'product_visibility',
                    'field'    => 'name',
                    'terms'    => 'featured',
                );
                break;

            case 'best_selling':
                $query_args['orderby'] = 'meta_value_num';
                break;

            case 'sale':
                $query_args['meta_query'] = WC()->query->get_meta_query();
                $query_args['post__in'] = wc_get_product_ids_on_sale();
                break;

            case 'deal':
                $today = time();
                $query_args['meta_query'] = array (
                    'relation' => 'AND',
                    array (
                        'key' => '_sale_price_dates_from',
                        'value' => '',
                        'compare' => '!='
                    ),
                    array (
                        'key' => '_sale_price_dates_from',
                        'value' => $today,
                        'compare' => '<='
                    ),
                    array (
                        'key' => '_sale_price_dates_to',
                        'value' => '',
                        'compare' => '!='
                    ),
                    array (
                        'key' => '_sale_price_dates_to',
                        'value' => $today,
                        'compare' => '>='
                    ),
                );
                break;

            case 'top_rated':
                $query_args['meta_query'] = WC()->query->get_meta_query();
                add_filter( 'posts_clauses', array( 'WC_Shortcodes', 'order_by_rating_post_clauses' ) );
                break;
            default: // i.e. recent product
                $query_args['meta_query'] = WC()->query->get_meta_query();
                break;
        }
    }

    ob_start();

    // pass the args through the auxin query parser
    $wp_query = new WP_Query( auxin_parse_query_args( $query_args ) );

    $have_posts = $wp_query->have_posts();

    if( $have_posts ){

        while ( $wp_query->have_posts() ) {

            $wp_query->the_post();

			wc_get_template( 'content-product.php' );

        }

    }


    if( $args['reset_query'] ){
        wp_reset_postdata();
    }

    // return false if no result found
    if( ! $have_posts ){
        ob_get_clean();
        return false;
    }

    return ob_get_clean();
};
