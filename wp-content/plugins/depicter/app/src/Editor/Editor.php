<?php
namespace Depicter\Editor;


use Depicter\Editor\Migrations\JobsQueue;


class Editor
{
	protected $document_id;

	public function __construct(){
	}

	public function init()
	{
		add_action( 'admin_action_depicter', [ $this, 'make' ] );
		add_action( 'depicter/plugin/updated', [ $this, 'check_migration_tasks' ] );
	}

	public function make()
	{
		if ( empty( $_REQUEST['document'] ) ) {
			return;
		}

		define( 'IS_DEPICTER_EDITOR_PREVIEW', true );

		$this->document_id = absint( $_REQUEST['document'] );

		$this->clearEditPage()
			->enqueueAssets()
			->printEditorPage();

		do_action( 'depicter/editor/open' );

		die();
	}

	protected function clearEditPage()
	{
		// Send MIME Type header like WP admin-header.
		header( 'Content-Type: ' . get_option( 'html_type' ) . '; charset=' . get_option( 'blog_charset' ) );

		add_filter( 'show_admin_bar', '__return_false' );

		// Remove all WordPress actions
		remove_all_actions( 'wp_head' );
		remove_all_actions( 'wp_print_styles' );
		remove_all_actions( 'wp_print_head_scripts' );
		remove_all_actions( 'wp_footer' );

		// Handle `wp_head`
		add_action( 'wp_head', 'wp_enqueue_scripts', 1 );
		add_action( 'wp_head', 'wp_print_styles', 8 );
		add_action( 'wp_head', 'wp_print_head_scripts', 9 );
		add_action( 'wp_head', 'wp_site_icon' );

		// Handle `wp_footer`
		add_action( 'wp_footer', 'wp_print_footer_scripts', 20 );

		// Handle `wp_enqueue_scripts`
		remove_all_actions( 'wp_enqueue_scripts' );

		// Also remove all scripts hooked into after_wp_tiny_mce.
		remove_all_actions( 'after_wp_tiny_mce' );

		// Change heartbeat options
		add_filter( 'heartbeat_settings', function( $settings ) {
			$settings['interval'] = 15;
			return $settings;
		});

		add_filter('wp_title', function( $title ){
			if( $document = \Depicter::document()->repository()->findOne( $this->document_id ) ){
				if( $documentTitle = $document->getFieldValue('name') ){
					$title = __( 'Depicter', 'depicter' ) . ' | ' . $documentTitle;
				}
			}
			return $title;
		} );

		return $this;
	}

	/**
	 * @return $this
	 */
	protected function enqueueAssets(){
		\Depicter::resolve('depicter.editor.assets')->bootstrap();
		return $this;
	}

	/**
	 * @return $this
	 */
	private function printEditorPage(){
		echo \Depicter::view('admin/editor/open/content.php')->toString();
		return $this;
	}

	/**
	 * Whether we are in the editor preview mode or not.
	 *
	 * @return bool
	 */
	public function isPreview(){
		return defined( 'IS_DEPICTER_EDITOR_PREVIEW' ) && IS_DEPICTER_EDITOR_PREVIEW;
	}

	/**
	 * Retrieves the document edit page
	 *
	 * @param $id
	 *
	 * @return mixed|void
	 */
	public function getEditUrl( $id ) {
		$url = add_query_arg(
			[
				'document' => $id,
				'action'   => 'depicter'
			],
			self_admin_url( 'post.php' )
		);

		return apply_filters( 'depicter/document/urls/edit', $url, $this );
	}

	/**
	 * Check migration tasks after plugin upgraded
	 */
	public function check_migration_tasks() {
		( new JobsQueue() )->migrate();
	}
}


// http://idev/wp/en/wp-admin/post.php?post=105&action=depicter
// http://idev/wp/en/wp-admin/post.php?post&action=depicter
