<?php
namespace Depicter\WordPress;

use Averta\WordPress\Models\WPOptions;
use WPEmerge\ServiceProviders\ServiceProviderInterface;

/**
 * Register plugin general hooks.
 */
class PluginServiceProvider implements ServiceProviderInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		$app = $container[ WPEMERGE_APPLICATION_KEY ];

		// register depicter options
		$container[ 'depicter.options' ] = function () {
			return new WPOptions('depicter_');
		};
		$app->alias( 'options', 'depicter.options' );

	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( $container ) {
		register_activation_hook(  DEPICTER_PLUGIN_FILE, [ $this, 'activate'  ] );
		register_deactivation_hook(DEPICTER_PLUGIN_FILE, [ $this, 'deactivate'] );

		add_action( 'plugins_loaded', [$this, 'loadTextDomain'] );
		add_action( 'upgrader_process_complete',[ $this,  'check_plugin_upgrade' ],10, 2 );
		add_action( 'admin_init', [ $this, 'check_plugin_upgrade_via_upload' ] );
	}

	/**
	 * Plugin activation.
	 *
	 * @return void
	 */
	public function activate() {
		// Nothing to do right now.
	}

	/**
	 * Plugin deactivation.
	 *
	 * @return void
	 */
	public function deactivate() {
		// Nothing to do right now.
	}

	/**
	 * Load text domain.
	 *
	 * @return void
	 */
	public function loadTextDomain() {
		load_plugin_textdomain( 'depicter', false, basename( dirname( DEPICTER_PLUGIN_FILE ) ) . DIRECTORY_SEPARATOR . 'languages' );
	}

	/**
	 * Check and fire plugin update hook if plugin upgraded
	 *
	 * @param $upgraderObject
	 * @param $options
	 */
	public function check_plugin_upgrade( $upgraderObject, $options ) {

		if ( $options['action'] == 'update' && $options['type'] == 'plugin' ) {
			if( !empty( $options['plugins'] ) ){
				foreach( $options['plugins'] as $plugin) {
					if ( $plugin == DEPICTER_PLUGIN_BASENAME ) {
						$previousVersion = \Depicter::options()->get( 'version', 0 );
						\Depicter::options()->set( 'version_previous', $previousVersion );
						\Depicter::options()->set( 'version', DEPICTER_VERSION );
						do_action( 'depicter/plugin/updated' );
					}
				}
			}
		}
	}

	/**
	 * Check if plugin updated via upload or not
	 */
	public function check_plugin_upgrade_via_upload() {
		$previousVersion = \Depicter::options()->get( 'version', 0 );
		if ( version_compare( DEPICTER_VERSION, $previousVersion, '>' ) ) {
			\Depicter::options()->set( 'version_previous', $previousVersion );
			\Depicter::options()->set( 'version', DEPICTER_VERSION );
			do_action( 'depicter/plugin/updated' );
		}
	}
}
