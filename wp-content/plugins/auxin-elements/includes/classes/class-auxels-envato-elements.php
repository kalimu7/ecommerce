<?php
/**
 * Main class for comunicating with envato elements
 *
 * 
 * @package    Auxin
 * @license    LICENSE.txt
 * @author     averta
 * @link       http://phlox.pro/
 * @copyright  (c) 2010-2023 averta
*/

// no direct access allowed
if ( ! defined('ABSPATH') )  exit;

/*--------------------------------*/


class Auxels_Envato_Elements {

    /**
     * Instance of this class.
     *
     * @var      object
     */
    protected static $instance = null;

    public $extension_base_endpoint = 'https://api.extensions.envato.com';

    public $token_verification_endpoint = 'https://api.extensions.envato.com/extensions/user_info';

    /**
     * Return an instance of this class.
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance() {

        // If the single instance hasn't been set, set it now.
        if ( null == self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function __construct() {
        add_action( 'wp_ajax_aux_verify_envato_elements_token', array( $this, 'ajax_verify_token' ) );
    }

    private function encode_url_parameter( $parameter ) {
		$parameter = html_entity_decode( $parameter, ENT_QUOTES | ENT_XML1, 'UTF-8' );
		$parameter = str_replace( '#', '', $parameter );

		return urlencode( $parameter );
	}

    /**
     * Token generator url
     *
     * @return string
     */
    public function get_token_url() {
		$extension_description = trim( get_bloginfo( 'name' ) );
		if ( strlen( $extension_description ) > 0 ) {
			$extension_description .= ' (' . get_home_url() . ')';
		} else {
			$extension_description = get_home_url();
		}
		$extension_description = substr( $extension_description, 0, 254 );

		return $this->extension_base_endpoint . "/extensions/begin_activation?extension_id=" . md5( get_site_url() ) . "&extension_type=envato-wordpress&extension_description=" . $this->encode_url_parameter( $extension_description );
	}

    /**
     * Verify Envato Elemnents token
     */
    public function ajax_verify_token() {
        wp_send_json( $this->verify_token() );
    }

    public function verify_token() {

        if ( ! empty( get_option( 'phlox_envato_elements_token', '' ) ) ) {
            return array( 'status' => true, 'message' => __( 'Token is valid.', 'auxin-elements') );
        }

        $extension_id = md5( get_site_url() );
        $token = sanitize_text_field( $_POST['token'] );

        $args['headers'] = array(
            'Extensions-Extension-Id'   => $extension_id,
            'Extensions-Token'          => $token
        );

        $response = wp_remote_get( $this->token_verification_endpoint, $args );

        if ( $response && ! is_wp_error( $response ) ) {
            $response = json_decode( $response['body'], true );
            if ( isset( $response['error'] ) && isset( $response['error']['message'] ) ) {
                return array( 'status' => false, 'message' => $response['error']['message'] );
            }

            if ( isset( $response['subscription_status'] ) && $response['subscription_status'] == 'paid' ) {
                update_option( 'phlox_envato_elements_token', $token );
                return array( 'status' => true, 'message' => __( 'Token is valid.', 'auxin-elements' ) );
            } else {
                return array( 'status' => false, 'message' => __( ' Token is not valid.', 'auxin-elements' ) );
            }
        } else {
            if ( is_wp_error( $response ) ) {
                return array( 'status' => false, 'message' => $response->get_error_message() );
            } else {
                return array( 'status' => false, 'message' => __( 'Something went wrong. Please try again later', 'auxin-elements' ) );
            }
        }
    }

    /**
     * Check if envato elements token provided or not
     */
    public function is_envato_element_enabled() {
        return ! empty( get_option( 'phlox_envato_elements_token', '' ) );
    }

} // end widget class

