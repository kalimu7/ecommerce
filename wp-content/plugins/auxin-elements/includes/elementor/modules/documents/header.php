<?php
namespace Auxin\Plugin\CoreElements\Elementor\Modules\Documents;

use Elementor\Modules\Library\Documents\Library_Document;
use Elementor\Core\DocumentTypes\Post;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Elementor section library document.
 *
 * Elementor section library document handler class is responsible for
 * handling a document of a section type.
 *
 * @since 2.0.0
 */
class Header extends Library_Document {

	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['location'] = 'header';
		$properties['support_kit'] = true;

		return $properties;
	}

	public function get_name() {
		return 'header';
	}

	public static function get_title() {
		return __( 'Header', 'auxin-elements' );
	}

	public function get_css_wrapper_selector() {
		return '.elementor-' . $this->get_main_id();
	}

	protected static function get_editor_panel_categories() {
		// Move to top as active.
		$categories = [
			'auxin-theme-elements' => [
				'title'  => __( 'Site Header', 'auxin-elements' ),
				'active' => true,
			],
		];

		return $categories + parent::get_editor_panel_categories();
	}

	protected function register_controls() {
		parent::register_controls();

		Post::register_style_controls( $this );
	}
	
	/*  For Adding Custom Classnames To Elementor Header
	/*-------------------------------------*/
	// public function get_container_classes() {
	// 	$classes  = parent::get_container_classes();
	// 	$classes .= ' aux-elementor-header';
	// 	return $classes;
	// }
}