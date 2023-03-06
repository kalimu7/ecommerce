<?php

if ( ! function_exists( 'depicter_initialize_extension' ) ):
/**
 * Creates the extension's main class instance.
 *
 * @since 1.0.0
 */
function depicter_initialize_extension() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/DepicterDivi.php';
}
add_action( 'divi_extensions_init', 'depicter_initialize_extension' );
endif;
