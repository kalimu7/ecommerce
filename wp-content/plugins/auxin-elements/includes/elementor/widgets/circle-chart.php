<?php
namespace Auxin\Plugin\CoreElements\Elementor\Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor 'CircleChart' widget.
 *
 * Elementor widget that displays an 'CircleChart' with lightbox.
 *
 * @since 1.0.0
 */
class CircleChart extends Widget_Base {

    /**
     * Get widget name.
     *
     * Retrieve 'CircleChart' widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'aux_circle_chart';
    }

    /**
     * Get widget title.
     *
     * Retrieve 'CircleChart' widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return __('Circle Chart', 'auxin-elements' );
    }

    /**
     * Get widget icon.
     *
     * Retrieve 'CircleChart' widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'eicon-counter-circle auxin-badge';
    }

    /**
     * Get widget categories.
     *
     * Retrieve 'CircleChart' widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_categories() {
        return array( 'auxin-core' );
    }

    /**
     * Register 'CircleChart' widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_controls() {

        /*-----------------------------------------------------------------------------------*/
        /*  Contact Info section
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'chart_info_section',
            array(
                'label'      => __('Chart Info', 'auxin-elements' )
            )
        );

        $this->add_control(
            'percentage',
            array(
                'label'      => __('Percentage','auxin-elements' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array('%'),
                'range'      => array(
                    '%' => array(
                        'min'  => 1,
                        'max'  => 100,
                        'step' => 1
                    ),
                ),
                'selectors'          => array(
                    '{{WRAPPER}} .aux-circle-chart-wrapper' => '--aux-chart-percentage:{{SIZE}};'
                ),
                'default'   => array(
                    'size' => 20,
                ),
            )
        );

        $this->add_control(
            'title',
            array(
                'label'       => __('Title','auxin-elements'),
                'type'        => Controls_Manager::TEXT
            )
        );

        $this->add_control(
            'subtitle',
            array(
                'label'       => __('Subtitle','auxin-elements'),
                'type'        => Controls_Manager::TEXT
            )
        );

        $this->add_responsive_control(
            'alignment',
            array(
                'label'       => __('Alignment', 'auxin-elements'),
                'type'        => Controls_Manager::CHOOSE,
                'default'     => 'center',
                'options'     => array(
                    'left' => array(
                        'title' => __( 'Left', 'auxin-elements' ),
                        'icon'  => 'eicon-text-align-left',
                    ),
                    'center' => array(
                        'title' => __( 'Center', 'auxin-elements' ),
                        'icon'  => 'eicon-text-align-center',
                    ),
                    'right' => array(
                        'title' => __( 'Right', 'auxin-elements' ),
                        'icon'  => 'eicon-text-align-right',
                    )
                ),
                'selectors_dictionary' => [
					'left'   => '',
					'center' => 'text-align:center;margin-left:auto !important;margin-right:auto !important;',
					'right'  => 'text-align:right;margin-left:auto !important;'
                ],
                'selectors' => [
					'{{WRAPPER}} .aux-chart-title, {{WRAPPER}} .aux-circle-chart-wrapper' => '{{value}}'
				]
            )
        );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  Style Tab
        /*-----------------------------------------------------------------------------------*/

        /*  Color Section
        /*-------------------------------------*/

        $this->start_controls_section(
            'chart_style_section',
            array(
                'label'     => __( 'Chart Style', 'auxin-elements' ),
                'tab'       => Controls_Manager::TAB_STYLE
            )
        );

        $this->add_responsive_control(
            'circle-width',
            array(
                'label'      => __('Chart Width','auxin-elements' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array('px'),
                'range'      => array(
                    'px' => array(
                        'min'  => 1,
                        'max'  => 1000,
                        'step' => 1
                    ),
                ),
                'default'   => array(
                    'size' => 150,
                ),
                'selectors'          => array(
                    '{{WRAPPER}} .aux-circle-chart-wrapper' => '--aux-chart-width:{{SIZE}}{{UNIT}};'
                )
            )
        );

        $this->add_responsive_control(
            'bg-circle-border-width',
            array(
                'label'      => __('Border Thickness','auxin-elements' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array('px'),
                'range'      => array(
                    'px' => array(
                        'min'  => 1,
                        'max'  => 1000,
                        'step' => 1
                    ),
                ),
                'selectors'          => array(
                    '{{WRAPPER}} .aux-bg-circle' => 'border-width:{{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .aux-circle-chart-wrapper' => '--aux-chart-border-width:{{SIZE}}{{UNIT}};'
                )
            )
        );

        $this->add_control(
            'bg-circle-color',
            [
                'label' => __( 'Default Circle Color', 'auxin-elements' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .aux-bg-circle' => 'border-color: {{VALUE}};'
                ]
            ]
        );

        $this->add_control(
            'circle-color',
            [
                'label' => __( 'Active Circle Color', 'auxin-elements' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .aux-circle-chart-wrapper' => '--aux-chart-color: {{VALUE}};'
                ]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'text_style_section',
            array(
                'label'     => __( 'Text Style', 'auxin-elements' ),
                'tab'       => Controls_Manager::TAB_STYLE
            )
        );

        $this->add_control(
			'title_style',
			[
				'label' => esc_html__( 'Title Style', 'auxin-elements' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'      => 'title_typography',
                'scheme'    => Typography::TYPOGRAPHY_1,
                'selector'  => '{{WRAPPER}} .aux-chart-title'
            )
        );

        $this->add_control(
            'title_color',
            array(
                'label' => __( 'Color', 'auxin-elements' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .aux-chart-title' => 'color: {{VALUE}};',
                )
            )
        );

        $this->add_control(
			'number_style',
			[
				'label' => esc_html__( 'Number Style', 'auxin-elements' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'      => 'number_typography',
                'scheme'    => Typography::TYPOGRAPHY_1,
                'selector'  => '{{WRAPPER}} .aux-number'
            )
        );

        $this->add_control(
            'number_color',
            array(
                'label' => __( 'Color', 'auxin-elements' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .aux-number' => 'color: {{VALUE}};',
                )
            )
        );

        $this->add_control(
			'subtitle_style',
			[
				'label' => esc_html__( 'Subtitle Style', 'auxin-elements' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'      => 'subtitle_typography',
                'scheme'    => Typography::TYPOGRAPHY_1,
                'selector'  => '{{WRAPPER}} .aux-chart-subtitle'
            )
        );

        $this->add_control(
            'subtitle_color',
            array(
                'label' => __( 'Color', 'auxin-elements' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .aux-chart-subtitle' => 'color: {{VALUE}};',
                )
            )
        );

        $this->end_controls_section();
    }

  /**
   * Render 'CircleChart' widget output on the frontend.
   *
   * @access protected
   */
    protected function render() {

        $settings   = $this->get_settings_for_display();

        ?>
        <span class='aux-chart-title'><?php echo esc_html( $settings['title'] );?></span>
        <div class="aux-circle-chart-wrapper">
            <div class="aux-bg-circle"></div>
            <div class="aux-circle-chart aux-animate">
                <span class='aux-number'><?php echo esc_html( $settings['percentage']['size'] );?><span>%</span></span>
                <span class="aux-chart-subtitle"><?php echo esc_html( $settings['subtitle'] );?></span>
            </div>
        </div>
        <?php
    }

    protected function content_template() {
        ?>
        <span class='aux-chart-title'>{{{settings.title}}}</span>
        <div class="aux-circle-chart-wrapper">
            <div class="aux-bg-circle"></div>
            <div class="aux-circle-chart aux-animate">
                <span class='aux-number'>{{{settings.percentage.size}}}<span>%</span></span>
                <span class="aux-chart-subtitle">{{{settings.subtitle}}}</span>
            </div>
        </div>
        <?php
    }

}
