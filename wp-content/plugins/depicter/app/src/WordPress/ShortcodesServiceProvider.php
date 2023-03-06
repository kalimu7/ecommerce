<?php
namespace Depicter\WordPress;

use Averta\WordPress\Utility\Extract;
use WPEmerge\ServiceProviders\ServiceProviderInterface;

/**
 * Register shortcodes.
 */
class ShortcodesServiceProvider implements ServiceProviderInterface {

	const SHORTCODE_NAME  = 'depicter';
	const SHORTCODE_ATTRS = [ 'id', 'alias', 'slug' ];

	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		// Nothing to register.
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( $container ) {
		add_shortcode(self::SHORTCODE_NAME , [ $this, 'process_shortcode' ]);
		add_action( 'wp_enqueue_scripts', [ $this, 'loadShortcodeAssets'] );
	}

	/**
	 * Load assets if the shortcode exists in the page content
	 *
	 * @return void
	 */
	public function loadShortcodeAssets() {
		global $post;

		if ( !empty( \Depicter::options()->get( 'always_load_assets', 1 ) ) ) {
			\Depicter::front()->assets()->enqueueStyles();
			\Depicter::front()->assets()->enqueueScripts();
		}

		if( empty( $post->post_content ) ){
			return;
		}
		
		$content = $post->post_content;

		$shortcodeInfo = Extract::shortcodeAttributes( $content, self::SHORTCODE_NAME, self::SHORTCODE_ATTRS );

		// the shortcode exist in the content
		if( $shortcodeInfo !== false ){
			// load common assets
			\Depicter::front()->assets()->enqueueStyles();
			\Depicter::front()->assets()->enqueueScripts();

			// Load custom css files
			foreach( self::SHORTCODE_ATTRS as $documentAttribute ){
				if( ! empty( $shortcodeInfo[ $documentAttribute ][0] ) ){
					\Depicter::front()->assets()->enqueueCustomAssets( $shortcodeInfo[ $documentAttribute ][0] );
					\Depicter::front()->assets()->enqueuePreloadTags( $shortcodeInfo[ $documentAttribute ][0] );
				}
			}
		}

		if ( false != strpos( $post->post_content, 'wp:depicter/slider' ) ) {
			// load common assets
			\Depicter::front()->assets()->enqueueStyles();
			\Depicter::front()->assets()->enqueueScripts();

			preg_match_all( '/wp:depicter\/slider\s+{"id":(\d+)/', $post->post_content, $sliderIDs, PREG_SET_ORDER );
			if ( !empty( $sliderIDs ) ) {
				foreach( $sliderIDs as $sliderID ) {
					\Depicter::front()->assets()->enqueueCustomAssets( $sliderID[1] );
					\Depicter::front()->assets()->enqueuePreloadTags( $sliderID[1] );
				}
			}
		}
	}


	/**
	 * Shortcode callback
	 *
	 * @param array  $attrs
	 * @param null   $content
	 *
	 * @return string
	 * @throws \Exception
	 */
	function process_shortcode( $attrs, $content = null ) {

		extract( shortcode_atts(
			array_fill_keys( self::SHORTCODE_ATTRS, '' ),
			$attrs,
			'depicter'
		));

		$documentId = $id;

		if ( empty( $documentId ) ) {
			if( empty( $slug ) && ! empty( $alias ) ){
				$slug = $alias;
			}
			$documentId = $slug;
		}

		return \Depicter::front()->render()->document( $documentId, [ 'echo' => false ] );
	}
}
