<?php

/**
 * initiate ti wishlist plugin with our required options
 *
 * @return void
 */
function auxels_set_ti_wishlist_required_options() {
    if ( !function_exists( 'tinv_update_option' ) || get_theme_mod( 'ti_wishlist_initiated', '' ) == 'yes' ) {
        return;
    }

    tinv_update_option( 'add_to_wishlist', 'position', 'shortcode' );
    tinv_update_option( 'add_to_wishlist_catalog', 'position', 'shortcode' );
    tinv_update_option( 'general', 'redirect', 0 );
    tinv_update_option( 'general', 'simple_flow', 1 );
    set_theme_mod( 'ti_wishlist_initiated', 'yes' );
}
add_action( 'admin_init', 'auxels_set_ti_wishlist_required_options' );