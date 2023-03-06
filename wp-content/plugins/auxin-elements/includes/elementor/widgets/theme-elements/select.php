<?php
namespace Auxin\Plugin\CoreElements\Elementor\Elements\Theme_Elements;
/**
 * Elementor Select Widget
 *
 * @since 1.2.2
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;

class Select extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve Icon Box Left widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'aux_select_box';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Icon Box Left widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Select Box', 'auxin-elements' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Icon Box Left widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-select auxin-badge';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the Icon Box Left widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'auxin-core', 'auxin-theme-elements' );
	}

	/**
	 * Register Icon Box Left widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Select', 'auxin-elements' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
        );

		$this->add_control(
			'dropdown_icon',
			[
				'label' => __( 'Dropdown icon', 'auxin-elements' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'auxicon auxicon-chevron-down',
					'library' => 'auxicon',
				],
			]
        );
        
        $repeater = new \Elementor\Repeater();
        
        $repeater->add_control(
			'item_title',
			array(
				'label'       => esc_html__( 'Title', 'auxin-elements' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Link title', 'auxin-elements' ),
			)
        );
        
        $repeater->add_control(
			'item_link',
			[
				'label' => __( 'Link', 'auxin-elements' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'auxin-elements' ),
				'show_external' => true,
				'default' => [
					'url' => '#',
					'is_external' => false,
					'nofollow' => true,
				],
				'dynamic' => array(
					'active' => true
                )
			]
        );
        
        $repeater->add_control(
			'item_icon',
			[
				'label' => __( 'Icon', 'auxin-elements' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'solid',
				],
			]
        );

		$repeater->add_control(
            'is_language_switcher',
            array(
                'label'       => __('Is lanugage swicher ?', 'auxin-elements'),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'auxin-elements' ),
                'label_off'    => __( 'No', 'auxin-elements' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

		$repeater->add_control(
            'language',
            array(
                'label'       => __('Language', 'auxin-elements'),
                'type'        => Controls_Manager::SELECT,
                'options'     => $this->get_languages_list(),
				'condition'	  => [
					'is_language_switcher' => 'yes'
				]
            )
        );
        
        $this->add_control(
			'select_items',
			array(
				'label'       => esc_html__( 'Items', 'auxin-elements' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'item_title' => esc_html__( 'First Item', 'auxin-elements' ),
					),
					array(
						'item_title' => esc_html__( 'Second Item', 'auxin-elements' ),
					),
				),
				'title_field' => '{{{ item_title }}}',
			)
		);

		$this->add_control(
			'action',
			array(
				'label'        => esc_html__( 'Display Items On Hover', 'auxin-elements' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Hover', 'auxin-elements' ),
				'label_off'    => esc_html__( 'Click', 'auxin-elements' ),
				'return_value' => 'hover',
				'default'      => 'hover',
			)
		);

		$this->end_controls_section();
		
		$this->start_controls_section(
			'select_style_section',
			array(
				'label' => esc_html__( 'Select Style', 'auxin-elements' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'select_box_margin',
			array(
				'label'      => esc_html__( 'Select Margin', 'auxin-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
        );
		
		$this->add_responsive_control(
			'select_align',
			array(
				'label'        => esc_html__( 'Alignment', 'auxin-elements' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => array(
					'left'    => array(
						'title' => __( 'Left', 'auxin-elements' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'  => array(
						'title' => __( 'Center', 'auxin-elements' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => __( 'Right', 'auxin-elements' ),
						'icon'  => 'eicon-text-align-right',
					)
				),
				'default'      => 'left',
				'selectors_dictionary' => [
					'left' 		=> 'text-align: left;justify-content: flex-start;',
					'center' 	=> 'text-align: center;justify-content: center;',
					'right'		=> 'text-align: right;justify-content: flex-end;'
				],
				'selectors'    => array(
					'{{WRAPPER}} div.aux-select-element' => '{{VALUE}}',
					'{{WRAPPER}} div.aux-select-element ul li' => '{{VALUE}}',
				),
			)
		);

        $this->add_responsive_control(
            'select_width',
            array(
                'label'      => __('Select Width', 'auxin-elements' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array('px', '%'),
                'range'      => array(
                    'px' => array(
                        'min'  => 0,
                        'max'  => 2000,
                        'step' => 5
					),
					'%' => array(
                        'min'  => 0,
                        'max'  => 100,
                        'step' => 1
                    )
                ),
                'selectors' => array(
                    '{{WRAPPER}} .aux-select-element' => 'width: {{SIZE}}{{UNIT}};'
                )
            )
        );

        $this->add_responsive_control(
            'select_height',
            array(
                'label'      => __('Select Height', 'auxin-elements' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array('px', '%'),
                'range'      => array(
                    'px' => array(
                        'min'  => 0,
                        'max'  => 2000,
                        'step' => 5
					),
					'%' => array(
                        'min'  => 0,
                        'max'  => 100,
                        'step' => 1
                    )
                ),
                'selectors' => array(
                    '{{WRAPPER}} .aux-select-element' => 'height: {{SIZE}}{{UNIT}};'
                )
            )
        );

		$this->start_controls_tabs( 'select_state_colors' );

		$this->start_controls_tab(
            'select_normal',
            array(
                'label' => __( 'Normal' , 'auxin-elements' )
            )
		);
        
        $this->add_control(
			'current_color_normal',
			array(
				'label'   => esc_html__( 'Current Item Color ', 'auxin-elements' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .current, {{WRAPPER}} .current a' => 'color: {{VALUE}};'
				]
			)
        );
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name' => 'select_background_normal',
                'label' => __( 'Select Background', 'auxin-elements' ),
                'types' => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .aux-select-element',
            )
        );
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'select_hover',
            array(
                'label' => __( 'Hover' , 'auxin-elements' )
            )
		);
        
        $this->add_control(
			'current_color_hover',
			array(
				'label'   => esc_html__( 'Current Item Color ', 'auxin-elements' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .current:hover, {{WRAPPER}} .current:hover a' => 'color: {{VALUE}};'
				]
			)
        );
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name' => 'select_background_hover',
                'label' => __( 'Select Background', 'auxin-elements' ),
                'types' => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .aux-select-element',
            )
		);
		
		$this->add_responsive_control(
            'select_transition',
            array(
                'label'      => __('Transition (ms)', 'auxin-elements' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array('px'),
                'range'      => array(
                    'px' => array(
                        'min'  => 0,
                        'max'  => 5000,
                        'step' => 100
                    )
                ),
                'selectors' => array(
                    '{{WRAPPER}} .aux-select-element, {{WRAPPER}} .current' => 'transition: all {{SIZE}}ms ease;'
                )
            )
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();

		$this->add_control(
			'select_border_options',
			[
				'label' => __( 'Select Border Options', 'auxin-elements' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
            Group_Control_Border::get_type(),
            array(
				'name'      => 'select_border',
				'label'      => esc_html__( 'Select Border', 'auxin-elements' ),
                'selector'  => '{{WRAPPER}} div.aux-select-element',
                'separator' => 'none'
            )
        );

		$this->add_responsive_control(
			'select_border_radius',
			array(
				'label'      => esc_html__( 'Select Border Radius', 'auxin-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} div.aux-select-element' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'dropdown_style_section',
			array(
				'label' => esc_html__( 'Dropdown Style', 'auxin-elements' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'select_list_border_options',
			[
				'label' => __( 'Dropdown Border Options', 'auxin-elements' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
            Group_Control_Border::get_type(),
            array(
				'name'      => 'select_list_border',
				'label'      => esc_html__( 'Select List Border', 'auxin-elements' ),
                'selector'  => '{{WRAPPER}} div.aux-select-element ul',
                'separator' => 'none'
            )
        );

		$this->add_responsive_control(
			'select_list_border_radius',
			array(
				'label'      => esc_html__( 'Dropdown Border Radius', 'auxin-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} div.aux-select-element ul' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'      => 'dropdown_box_shadow',
                'selector'  => '{{WRAPPER}} div.aux-select-element ul',
            )
        );

		$this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name' => 'dropdown_background',
                'label' => __( 'Dropdown Background', 'auxin-elements' ),
                'types' => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} ul',
            )
        );

        $this->end_controls_section();

		$this->start_controls_section(
			'select_items_style_section',
			array(
				'label' => esc_html__( 'Items Style', 'auxin-elements' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'item_typography',
				'label'     => __( 'Item Typography', 'auxin-elements' ),
				'scheme'    => Typography::TYPOGRAPHY_1,
				'selector'  => '{{WRAPPER}} .current .selected, {{WRAPPER}} ul li',
			)
		);

        $this->add_responsive_control(
			'item_padding',
			array(
				'label'      => esc_html__( 'Padding', 'auxin-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ul li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
        );

		$this->add_responsive_control(
			'item_margin',
			array(
				'label'      => esc_html__( 'Margin', 'auxin-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ul li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
        );

		$this->add_group_control(
            Group_Control_Border::get_type(),
            array(
				'name'      => 'item_border',
				'label'      => esc_html__( 'Border', 'auxin-elements' ),
                'selector'  => '{{WRAPPER}} div.aux-select-element li:not(:last-child)',
                'separator' => 'none'
            )
        );
        
        $this->add_responsive_control(
			'icon_padding',
			array(
				'label'      => esc_html__( 'Icon/Image Padding', 'auxin-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} span.element-icon , {{WRAPPER}} img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'item_state_colors' );

		$this->start_controls_tab(
            'item_normal',
            array(
                'label' => __( 'Normal' , 'auxin-elements' )
            )
		);
        
        $this->add_control(
			'icon_color_normal',
			array(
				'label'   => esc_html__( 'Icon Color ', 'auxin-elements' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .element-icon' => 'color: {{VALUE}};'
				]
			)
        );
        
        $this->add_control(
			'title_color_normal',
			array(
				'label'   => esc_html__( 'Text Color ', 'auxin-elements' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} ul li a' => 'color: {{VALUE}};'
				]
			)
        );
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name' => 'item_background_normal',
                'label' => __( 'Item Background', 'auxin-elements' ),
                'types' => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} ul li',
            )
        );
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'item_hover',
            array(
                'label' => __( 'Hover' , 'auxin-elements' )
            )
		);
        
        $this->add_control(
			'icon_color_hover',
			array(
				'label'   => esc_html__( 'Icon Color ', 'auxin-elements' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} ul li:hover .element-icon' => 'color: {{VALUE}};'
				]
			)
        );
        
        $this->add_control(
			'title_color_hover',
			array(
				'label'   => esc_html__( 'Text Color ', 'auxin-elements' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} ul li:hover a' => 'color: {{VALUE}};'
				]
			)
        );
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name' => 'item_background_hover',
                'label' => __( 'Item Background', 'auxin-elements' ),
                'types' => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} ul li:hover',
            )
		);
		
		$this->add_responsive_control(
            'dropdown_transition',
            array(
                'label'      => __('Transition (ms)', 'auxin-elements' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array('px'),
                'range'      => array(
                    'px' => array(
                        'min'  => 0,
                        'max'  => 5000,
                        'step' => 100
                    )
                ),
                'selectors' => array(
                    '{{WRAPPER}} ul li, {{WRAPPER}} ul li a, {{WRAPPER}} ul li .element-icon' => 'transition: all {{SIZE}}ms ease;'
                )
            )
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();

		$this->end_controls_section();

	}

    /**
	 * Render Icon Box Left widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
        $settings = $this->get_settings_for_display();

		$action = $settings['action'] == 'hover' ? 'hover' : 'click';

		$current_item = $this->get_current_item( $settings );

		?>
		<div class="aux-select-element" data-action="<?php echo esc_attr( $action );?>">
            <span class="current">
				<span class="selected">
					<?php 
					if ( !empty( $settings['select_items'][ $current_item ]['item_icon']['library'] ) && $settings['select_items'][ $current_item ]['item_icon']['library'] == 'svg'  ) {
						$icon = '<img src="' . $settings['select_items'][ $current_item ]['item_icon']['value']['url'] . '">';
					} elseif ( !empty( $settings['select_items'][ $current_item ]['item_icon']['value'] ) ) {
						$icon = '<span class="element-icon ' . $settings['select_items'][ $current_item ]['item_icon']['value'] . '"></span>';
					} else {
						$icon = '';
					}
					echo auxin_kses( $icon ) . esc_html( $settings['select_items'][ $current_item ]['item_title'] ); 
					?>
				</span>
				<?php
				if ( !empty( $settings['dropdown_icon']['library'] ) && $settings['dropdown_icon']['library'] == 'svg'  ) {
					echo '<img class="dropdown-icon" src="' . esc_url( $settings['dropdown_icon']['value']['url'] ) . '">';
				} elseif ( !empty( $settings['dropdown_icon']['value'] ) ) {
					echo '<span class="dropdown-icon ' . esc_attr( $settings['dropdown_icon']['value'] ) . '"></span>';
				}
				?>
			</span>
            <ul class="list">
                <?php 
                foreach( $settings['select_items'] as $key => $item ) {
                    $is_external = $item['item_link']['is_external'] ? '_blank' : '_self';
                    if ( !empty( $item['item_icon']['library'] ) && $item['item_icon']['library'] == 'svg'  ) {
                        $icon = '<img src="' . esc_url( $item['item_icon']['value']['url'] ) . '">';
                    } elseif ( !empty( $item['item_icon']['value'] ) ) {
                        $icon = '<span class="element-icon ' . esc_attr( $item['item_icon']['value'] ) . '"></span>';
                    }

					$item_link = $item['is_language_switcher'] ? $this->get_item_language_link( $item ) : rtrim( $item['item_link']['url'], '/' );
                ?>
                    <li class="option"><?php echo auxin_kses( $icon );?><a href="<?php echo esc_url( $item_link );?>" target="<?php echo esc_attr( $is_external );?>"><?php echo esc_html( $item['item_title'] );?></a></li>
                <?php } ?>
            </ul>
        </div>
		<?php		
	}
	/**
	 * Whether the reload preview is required or not.
	 *
	 * Used to determine whether the reload preview is required.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return bool Whether the reload preview is required.
	 */
	public function is_reload_preview_required() {
		return false;
	}

	/**
	 * Render shortcode widget as plain content.
	 *
	 * Override the default behavior by printing the shortcode instead of rendering it.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render_plain_content() {

	}

	protected function content_template() {
	}

	/**
	 * Get current item for url visiting
	 *
	 * @param array $settings
	 * @return int
	 */
	public function get_current_item( $settings ) {
		global $wp;
		$current_link = rtrim( home_url( $wp->request ), '/' );

		// if permalink structure is pretty
		foreach( $settings['select_items'] as $key => $item ) {
			$item_link = !empty( $item['is_language_switcher'] ) ? $this->get_item_language_link($item) : rtrim( $item['item_link']['url'], '/' );
			if ( !empty( $item_link ) && ( $item_link == $current_link || strpos( $current_link, $item_link ) != false ) ) {
				return $key;
			}
		}

		// if permalink structure is not pretty and hast query variables inside
		foreach( $settings['select_items'] as $key => $item ) {
			$item_link = !empty( $item['is_language_switcher'] ) ? $this->get_item_language_link($item) : rtrim( $item['item_link']['url'], '/' );
			if ( !empty( $item_link ) ) {
				$query = parse_url($item_link, PHP_URL_QUERY);
				parse_str($query, $params);
				if ( !empty( $params ) ) {
					$paramKeyFound = true;
					foreach( $params as $paramKey => $paramValue ) {
						if ( empty( $_GET[ $paramKey ] ) || $_GET[ $paramKey ] != $paramValue ) {
							$paramKeyFound = false;
							break;
						}
					}
					
					if ( $paramKeyFound ) {
						return $key;
					}
				}
			}
		}

		return 0;
	}

	/**
	 * List available languages
	 *
	 * @return array
	 */
	public function get_languages_list() {
		if ( function_exists('pll_languages_list') ) {
			$languages_list = pll_languages_list();
			return array_combine( $languages_list, $languages_list );
		}

		if ( function_exists('wpml_get_active_languages_filter') ) {
			$languages_list = wpml_get_active_languages_filter('');
			return array_combine( array_keys( $languages_list ), array_keys( $languages_list ) );
		}
		return [];
	}

	/**
	 * Get relative language link for an item
	 *
	 * @param array $item
	 * @return string
	 */
	public function get_item_language_link( $item  ) {
		global $post;
		if ( is_object( $post ) && !is_archive() ) {
			if ( function_exists('pll_get_post') ) {
				$translated_post = pll_get_post( $post->ID, $item['language'] );
				return !empty( $translated_post ) ? rtrim( get_permalink( $translated_post ), '/') : rtrim( $item['item_link']['url'], '/' );
			}
	
			if ( function_exists('wpml_object_id_filter') ) {
				$translated_post = wpml_object_id_filter( $post->ID, $post->post_type, false, $item['language'] );
				return !empty( $translated_post ) ? rtrim( wpml_permalink_filter( get_permalink( $translated_post ), $item['language'] ), '/') : rtrim( $item['item_link']['url'], '/' );
			}
		}
		
		return rtrim( $item['item_link']['url'], '/' );
	}
}
