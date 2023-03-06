<?php
namespace Auxin\Plugin\CoreElements\Elementor\Settings\Base;

use Elementor\Core\Base\Document;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Core\Schemes\Typography;
use Elementor\Control_Media;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Core\Kits\Controls\Repeater as Global_Style_Repeater;
use Elementor\Repeater;


abstract class Manager {

    public function __construct(){
        $this->register_hooks();
    }

    protected function register_hooks(){
        add_action( 'elementor/documents/register_controls', [ $this, 'register_controls' ] );
        add_action( 'elementor/document/after_save', [ $this, 'on_save_settings' ], 10, 2 );
        add_action( 'elementor/document/after_save', [ $this, 'elementor_after_save' ], 10, 2 );
        add_action( 'customize_save_after', [ $this, 'sync_customizer_with_elementor'] );
        add_action( 'auxin_demo_import_finish', [ $this, 'after_demo_import'] );
    }

    /**
     * Register Document Controls
     *
     * Add New Controls to Elementor Page Options
     * @param $document
     */
    abstract public function register_controls ( $document );

    /**
     * Stores the changed controllers
     *
     * @param array $settings    list of settings for changes controllers
     * @param Document $document Elementor base Document class
     * @param array|null $data   All document info passed for saving
     * @return mixed
     */
    abstract protected function save_settings( array $settings, $document, $data = null );

    /**
     * Parsing custom  control settings
     *
     * @param $document
     * @param $data
     */
    public function on_save_settings( $document, $data ){
        if( empty( $data['settings'] ) ){
            return;
        }
        $settings_to_save = $this->get_settings_to_save( $data['settings'] );
        $this->save_settings( $settings_to_save, $document, $data );
    }

    /**
     * Default special page settings in Elementor document
     *
     * @return array
     */
    protected function get_special_settings_names(){
        return [
			'id',
			'post_title',
			'post_status',
			'template',
			'post_excerpt',
			'post_featured_image',
		];
    }

    /**
     * Get custom control settings which are changed
     *
     * @param array $settings
     * @return array
     */
    private function get_settings_to_save( array $settings ){
        $special_settings = $this->get_special_settings_names();

		$settings_to_save = $settings;

		foreach ( $special_settings as $special_setting ) {
			if ( isset( $settings_to_save[ $special_setting ] ) ) {
				unset( $settings_to_save[ $special_setting ] );
			}
		}

		return $settings_to_save;
    }

    /**
     * Save auxin colors
     *
     * @param object $object
     * @param array $data
     * @return void
     */
    public function elementor_after_save( $object, $data ) {
        if ( $data && isset( $data['settings']['system_colors'] ) && is_array( $data['settings']['system_colors'] ) ) {
            foreach ( $data['settings']['system_colors'] as $key => $value ) {
                if ( !empty( $value['color'] ) ) {
                    auxin_update_option( 'elementor_color_' . $value['_id'], $value['color'] );
                }
            }

        }
    }

    /**
     * update elementor global colors in customizer
     *
     * @return void
     */
    public function after_demo_import() {

        // get and update system colors in customizer
        $current_settings = \Elementor\Plugin::$instance->kits_manager->get_current_settings();
        foreach ( $current_settings['system_colors'] as $color ) {
            auxin_update_option( 'elementor_color_' . $color['_id'], $color['color'] );
        }
    }

    /**
     * Sync Customizer with elementor for elementor global colors
     *
     * @return void
     */
    public function sync_customizer_with_elementor() {

        // get elementor settings
        $active_kit = get_option( 'elementor_active_kit', '' );
        if ( empty( $active_kit ) ) {
            return;
        }

        $settings = get_post_meta( $active_kit, '_elementor_page_settings', true );

        // get elementor global custom colors from customizer
        $added_custom_colors = auxin_get_option( 'elementor_global_custom_colors_repeater', '');
        $colors = json_decode( $added_custom_colors, true );
        $custom_colors = [];
        if ( !empty( $colors ) ) {
            foreach( $colors as $key => $color ) {
                if( empty( $color['color'] ) ) {
                    continue;
                }
                $custom_colors[] = [
                    '_id'   => !empty( $color['_id'] ) ? sanitize_text_field( $color['_id'] ) : \Elementor\Utils::generate_random_string(),
                    'title' => !empty( $color['title'] ) ? sanitize_text_field( $color['title'] ) : __('New Color', 'auxin-elements') . ' #' . $key,
                    'color' => $color['color']
                ];
            }
        }

        if ( empty( $settings ) ) {

            // get elementor system colors form customizer
            $system_colors_key = ['primary', 'secondary', 'text', 'accent' ];
            foreach( $system_colors_key as $key ) {
                $system_colors[] = [
                    '_id'   => $key,
                    'title' => ucfirst( $key ),
                    'color' => auxin_get_option( 'elementor_color_' . $key, '' )
                ];
            }

            // udpate system colors
            \Elementor\Plugin::$instance->kits_manager->update_kit_settings_based_on_option( 'system_colors', $system_colors );

            // update custom colors
            if ( !empty( $custom_colors ) ) {
                \Elementor\Plugin::$instance->kits_manager->update_kit_settings_based_on_option( 'custom_colors', $custom_colors );
            }

        } else {
            // get and update system colors => executing these codes to remain title of system colors from elementor settings unchanged
            $current_settings = \Elementor\Plugin::$instance->kits_manager->get_current_settings();
            foreach ( $current_settings['system_colors'] as $color ) {
                $system_colors[] = [
                    '_id'   => $color['_id'],
                    'title' => $color['title'],
                    'color' => auxin_get_option( 'elementor_color_' . $color['_id'], $color['color'] )
                ];
            }
            \Elementor\Plugin::$instance->kits_manager->update_kit_settings_based_on_option( 'system_colors', $system_colors );

            // update custom colors -> updating via post meta because we have to replace all custom colors from customizer with the one in elemnetor settings
            if ( ! empty( $custom_colors ) ) {
                $settings['custom_colors'] = $custom_colors;
                update_post_meta( $active_kit, '_elementor_page_settings', $settings );
            }
        }

        auxin_update_option( 'elementor_global_custom_colors_repeater', '' );
        \Elementor\Plugin::instance()->files_manager->clear_cache();

    }
}
