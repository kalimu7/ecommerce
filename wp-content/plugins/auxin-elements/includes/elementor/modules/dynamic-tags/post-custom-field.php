<?php
namespace Auxin\Plugin\CoreElements\Elementor\Modules\DynamicTags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Post_Custom_Field extends Tag {

	public function get_name() {
		return 'aux-post-custom-field';
	}

	public function get_title() {
		return __( 'Post Custom Field', 'auxin-elements' );
	}

	public function get_group() {
		return 'post';
	}

	public function get_categories() {
		return [
			TagsModule::TEXT_CATEGORY,
			TagsModule::URL_CATEGORY,
			TagsModule::POST_META_CATEGORY
		];
	}

	public function get_panel_template_setting_key() {
		return 'key';
	}

	public function is_settings_required() {
		return true;
	}

	protected function register_controls() {
		$this->add_control(
			'key',
			[
				'label' => __( 'Key List', 'auxin-elements' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_custom_keys_array(),
			]
		);

		$this->add_control(
			'handy_custom_keys',
			[
				'label' => __( 'Phlox Custom Keys', 'auxin-elements' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_handy_custom_keys_array(),
			]
		);

		$this->add_control(
			'custom_key',
			[
				'label' => __( 'Custom Key', 'auxin-elements' ),
				'type' => Controls_Manager::TEXT,
				'options' => $this->get_custom_keys_array(),
                'condition'    => array(
                    'key' => '',
                )
			]
		);
	}

	public function render() {
		$key = $this->get_settings( 'key' );
		$handy_custom_keys = $this->get_settings( 'handy_custom_keys' );
		$key = empty( $key ) ? ( empty( $handy_custom_keys ) ? $this->get_settings( 'custom_key' ) : $handy_custom_keys ) : $key;

		if ( empty( $key ) ) {
			return;
		}

		$value = get_post_meta( get_the_ID(), $key, true );

		echo wp_kses_post( $value );
	}

	private function get_custom_keys_array() {
		$custom_keys = get_post_custom_keys();
		$options = [
			'' => __( 'Select...', 'auxin-elements' ),
		];

		if ( ! empty( $custom_keys ) ) {
			foreach ( $custom_keys as $custom_key ) {
				if ( '_' !== substr( $custom_key, 0, 1 ) ) {
					$options[ $custom_key ] = $custom_key;
				}
			}
		}

		return $options;
	}

	private function get_handy_custom_keys_array() {

		$options = [
			'' => __( 'Select...', 'auxin-elements' ),
			'_format_audio_attachment' => __( 'Post - Audio file (MP3 or ogg)', 'auxin-elements' ),
			'_format_audio_embed' => __( 'Post - Audio URL (oEmbed)', 'auxin-elements' ),
			'_format_gallery_type' => __( 'Post - The Gallery Images', 'auxin-elements' ),
			'_format_quote_source_name' => __( 'Post - Source Name', 'auxin-elements' ),
			'_format_quote_source_url' => __( 'Post - Source URL', 'auxin-elements' ),
			'_format_video_attachment' => __( 'Post - Video file', 'auxin-elements' ),
			'_format_video_embed' => __( 'Post - Video URL (oEmbed)', 'auxin-elements' ),
			'_format_video_attachment_poster' => __( 'Post - Poster image', 'auxin-elements' ),
			'_thumbnail_id2' => __( 'Portfolio - Featured Image (Secondary)', 'auxin-elements' ),
			'_overview' => __( 'Portfolio - Overview', 'auxin-elements' ),
			'_overview_title' => __( 'Portfolio - Overview Title', 'auxin-elements' ),
			'_lunch_button_url' => __( 'Portfolio - URL for Launch Button', 'auxin-elements' ),
			'_auxin_meta_url' => __( 'Portfolio - Project URL', 'auxin-elements' ),
			'_auxin_meta_client' => __( 'Portfolio - Client', 'auxin-elements' ),
			'_auxin_meta_release_date' => __( 'Portfolio - Release Date', 'auxin-elements' ),
		];

		return $options;
	}
}
