<?php
namespace Auxin\Plugin\CoreElements\Elementor\Modules\ThemeBuilder\Classes;

use Elementor\Plugin;
use Auxin\Plugin\CoreElements\Elementor\Modules\ThemeBuilder\Module;
use Auxin\Plugin\CoreElements\Elementor\Modules\ThemeBuilder\Theme_Document;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Preview_Manager {

	public function __construct() {
		add_filter( 'elementor/query_control/get_query_args/current_query', [ $this, 'filter_query_control_args' ] );
		add_filter( 'elementor/theme/posts_archive/query_posts/query_vars', [ $this, 'filter_query_control_args' ] );
		add_filter( 'elementor/dynamic_tags/post_terms/taxonomy_args', [ $this, 'filter_post_terms_taxonomy_arg' ] );

		add_action( 'elementor/template-library/before_get_source_data', [ $this, 'switch_to_preview_query' ] );
		add_action( 'elementor/template-library/after_get_source_data', [ $this, 'restore_current_query' ] );
		add_action( 'elementor/dynamic_tags/before_render', [ $this, 'switch_to_preview_query' ] );
		add_action( 'elementor/dynamic_tags/after_render', [ $this, 'restore_current_query' ] );
	}

	public function filter_post_terms_taxonomy_arg( $taxonomy_args ) {
		$current_post_id = get_the_ID();
		$document = Module::instance()->get_document( $current_post_id );

		if ( $document ) {
			// Show all taxonomies
			unset( $taxonomy_args['object_type'] );
		}

		return $taxonomy_args;
	}

	/**
	 * @access public
	 *
	 * @param $query_vars array
	 *
	 * @return array
	 */
	public function filter_query_control_args( $query_vars ) {
		$document = Plugin::instance()->documents->get_doc_or_auto_save( get_the_ID() );

		if ( $document && $document instanceof Theme_Document ) {
			$query_vars = $document->get_preview_as_query_args();
		}

		return $query_vars;
	}

	/**
	 * @access public
	 */
	public function switch_to_preview_query() {
		$current_post_id = get_the_ID();
		$document = Plugin::instance()->documents->get_doc_or_auto_save( $current_post_id );

		if ( ! $document || ! $document instanceof Theme_Document ) {
			return;
		}

		$new_query_vars = $document->get_preview_as_query_args();

		Plugin::instance()->db->switch_to_query( $new_query_vars, true );
	}

	/**
	 * @access public
	 */
	public function restore_current_query() {
		Plugin::instance()->db->restore_current_query();
	}
}
