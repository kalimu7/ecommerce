<?php
/**
 * Products Grid Element
 *
 * 
 * @package    Auxin
 * @license    LICENSE.txt
 * @author     averta
 * @link       http://phlox.pro/
 * @copyright  (c) 2010-2023 averta
 */

function auxin_widget_products_grid_callback( $atts, $shortcode_content = null ){

    // Defining default attributes
    $default_atts = array(
        'product_type'          => '',    // available values : recent, featured, top_rated, sale, deal, best_selling
        'title'                 => '',    // header title (required)
        'subtitle'              => '',    // header subtitle
        'cat'                   => '',
        'num'                   => '8',   // max generated entry
        'exclude_without_media' => 0,
        'order_by'              => 'date',
        'order'                 => 'DESC',
        'only_products__in'     => '',   // display only these post IDs. array or string comma separated
        'include'               => '',    // include these post IDs in result too. array or string comma separated
        'exclude'               => '',    // exclude these post IDs from result. array or string comma separated
        'offset'                => '',
        'desktop_cnum'          => 4,
        'post_type'             => 'product',
        'taxonomy_name'         => 'product_cat', // the taxonomy that we intent to display in post info
        'tax_args'              => '',
        'terms'                 => '',
        'extra_classes'         => '',
        'universal_id'          => '',
        'use_wp_query'          => false, // true to use the global wp_query, false to use internal custom query
        'reset_query'           => true,
        'wp_query_args'         => array(), // additional wp_query args
        'query_args'            => array(),
        'custom_wp_query'       => '',
        'template_part_file'    => 'products-grid',
        'extra_template_path'   =>  AUXELS_PUB_DIR . '/templates/woocommerce',
        'base'                  => 'aux_products_grid',
        'base_class'            => 'aux-widget-products-grid'
    );

    $result = auxin_get_widget_scafold( $atts, $default_atts, $shortcode_content );

    ob_start();

    wc_setup_loop([
        'columns'=> $result['parsed_atts']['desktop_cnum']
    ]);
    
    echo "<div class='woocommerce woocommerce-page aux-shop-archive'>";
    woocommerce_product_loop_start();
    // widget custom output -----------------------
    include_once auxin_get_template_file( $result['parsed_atts']['template_part_file'], '', $result['parsed_atts']['extra_template_path'] );
    echo auxin_products_grid( $result['parsed_atts'] );
    woocommerce_product_loop_end();
    unset( $GLOBALS['woocommerce_loop'] );
    echo '</div>';

    return ob_get_clean();
}