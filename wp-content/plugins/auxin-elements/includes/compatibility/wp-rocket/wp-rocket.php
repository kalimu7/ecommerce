<?php

/**
 * Check if minify and combine js option of wp rocket is enabled, then load unminified version of our assets
 *
 * @return void
 */
function auxels_check_wp_rocket_minify_option() {
    if ( !defined( 'WP_ROCKET_VERSION' ) ) {
        return;
    }

    $rocket_options = get_option( 'wp_rocket_settings', []);
    if ( isset( $rocket_options['minify_concatenate_js'] ) && auxin_is_true( $rocket_options['minify_concatenate_js'] ) ) {
        add_filter( 'auxin_load_minified_assets', '__return_false' );
    }
}
add_action('init', 'auxels_check_wp_rocket_minify_option' );