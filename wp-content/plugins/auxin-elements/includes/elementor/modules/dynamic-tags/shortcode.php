<?php
namespace Auxin\Plugin\CoreElements\Elementor\Modules\DynamicTags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Shortcode extends Tag {
	public function get_name() {
		return 'aux-shortcode';
	}

	public function get_title() {
		return __( 'Shortcode', 'auxin-elements' );
	}

	public function get_group() {
		return 'site';
	}

	public function get_categories() {
		return [
			TagsModule::TEXT_CATEGORY,
			TagsModule::URL_CATEGORY,
			TagsModule::POST_META_CATEGORY,
			TagsModule::GALLERY_CATEGORY,
			TagsModule::IMAGE_CATEGORY,
			TagsModule::MEDIA_CATEGORY,
		];
	}

	protected function register_controls() {
		$this->add_control(
			'shortcode',
			[
				'label' => __( 'Shortcode', 'auxin-elements' ),
				'type'  => Controls_Manager::TEXTAREA,
			]
		);
	}

	public function render() {
		$settings = $this->get_settings();

		if ( empty( $settings['shortcode'] ) ) {
			return;
		}

		$shortcode_string = $settings['shortcode'];

		$value = do_shortcode( $shortcode_string );

		echo wp_kses_post( $value ); 
	}
}
