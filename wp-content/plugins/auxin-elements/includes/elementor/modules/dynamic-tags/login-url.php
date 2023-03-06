<?php
namespace Auxin\Plugin\CoreElements\Elementor\Modules\DynamicTags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Auxin_Login_Url extends Tag {

	public function get_name() {
		return 'aux-login-url';
	}

	public function get_title() {
		return __( 'Login/SingUp Page URL', 'auxin-elements' );
	}

	public function get_group() {
		return 'URL';
	}

	public function get_categories() {
		return [
			TagsModule::URL_CATEGORY
		];
    }


	public function is_settings_required() {
		return true;
	}

	protected function register_controls() {
		$this->add_control(
			'key',
			[
				'label'   => __( 'Pages URL', 'auxin-elements' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
                    'login'     => __( 'Login Page', 'auxin-elements' ),
                    'register'  => __( 'SignUp Page', 'auxin-elements' )
                ],
				'default' => 'login'
            ]
        );
	}

	protected function get_page_url() {
        global $wp;
        $current_url = home_url( add_query_arg( [], $wp->request ) );
		if( $this->get_settings( 'key' ) == 'login' ){
			return wp_login_url( $current_url );
		} else if ( get_option( 'users_can_register' ) ) {
            $reg_url = site_url( 'wp-login.php?action=register', 'login' );
            $reg_url = add_query_arg( 'redirect_to', $current_url, $reg_url );
            return $reg_url;
        }

        return '';
	}

	public function get_value( array $options = [] ) {
		return $this->get_page_url();
	}

	public function render() {
		echo esc_url( $this->get_page_url() );
	}

}
