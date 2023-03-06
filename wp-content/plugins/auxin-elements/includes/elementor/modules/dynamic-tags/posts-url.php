<?php
namespace Auxin\Plugin\CoreElements\Elementor\Modules\DynamicTags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Auxin_Posts_Url extends Tag {

	public function get_name() {
		return 'aux-posts-url';
	}

	public function get_title() {
		return __( 'Posts URL', 'auxin-elements' );
	}

	public function get_group() {
		return 'URL';
	}

	public function get_categories() {
		return [
			TagsModule::URL_CATEGORY
		];
    }

    public function get_posts_list() {

		$items = [
            '' => __( 'Select...', 'auxin-elements' ),
        ];
        $posts = get_posts( array(
            'post_type'   =>'post',
            'numberposts' => -1
		) );

        foreach ( $posts as $post ) {
            $items[ $post->ID ] = $post->post_title;
        }

        return $items;
    }

	public function is_settings_required() {
		return true;
	}

	protected function register_controls() {
		$this->add_control(
			'key',
			[
				'label'   => __( 'Posts URL', 'auxin-elements' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $this->get_posts_list(),
				'default' => ''
            ]
        );
	}

	protected function get_post_url() {
		if( $key = $this->get_settings( 'key' ) ){
			return get_permalink( $key );
		}

		return '';
	}

	public function get_value() {
		return $this->get_post_url();
	}

	public function render() {
		echo esc_url( $this->get_post_url() );
	}

}
