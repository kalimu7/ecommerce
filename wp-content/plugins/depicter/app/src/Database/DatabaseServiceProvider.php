<?php

namespace Depicter\Database;

use Depicter\Database\Repository\DocumentRepository;
use WPEmerge\ServiceProviders\ServiceProviderInterface;

/**
 * Load document data manager.
 */
class DatabaseServiceProvider implements ServiceProviderInterface {

	/**
	 * {@inheritDoc}
	 */
	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		$container[ 'depicter.database.migration' ] = function () {
			return new Migration();
		};

		$container[ 'depicter.database.repository.document' ] = function () {
			return new DocumentRepository();
		};

		$app = $container[ WPEMERGE_APPLICATION_KEY ];
		$app->alias( 'documentRepository', 'depicter.database.repository.document' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( $container ) {
		register_activation_hook( DEPICTER_PLUGIN_FILE, [ $this, 'activate' ] );
		add_action( 'wp_insert_site', [ $this, 'activateSingle' ] );
	}

	/**
	 * Plugin activation.
	 *
	 * @return void
	 */
	public function activate( $network_wide ) {
		if ( $network_wide ) {
			$sites = get_sites();
			foreach( $sites as $site ) {
				$this->activateSingle( $site );
			}
		} else {
			$this->migrate();
		}
	}

	/**
	 * Plugin activation on a site
	 *
	 * @param \WP_Site $site
	 *
	 * @return void
	 */
	public function activateSingle( $site ) {
		switch_to_blog( $site->blog_id );
        $this->migrate();
		restore_current_blog();
	}

	/**
	 * Create or update plugin tables
	 *
	 * @return void
	 */
	public function migrate() {
		\Depicter::resolve( 'depicter.database.migration' )->migrate(true);
	}

}
