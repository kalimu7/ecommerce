<?php

namespace Depicter\Editor;

use WPEmerge\ServiceProviders\ServiceProviderInterface;

/**
 * Loads editor.
 */
class EditorServiceProvider implements ServiceProviderInterface {

	/**
	 * {@inheritDoc}
	 */
	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		$container[ 'depicter.editor' ] = function () {
			return new Editor();
		};

		$app = $container[ WPEMERGE_APPLICATION_KEY ];
		$app->alias( 'editor', 'depicter.editor' );

		$container[ 'depicter.editor.assets' ] = function () {
			return new EditorAssets();
		};
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( $container ) {
		\Depicter::app()->editor()->init();
	}

}
