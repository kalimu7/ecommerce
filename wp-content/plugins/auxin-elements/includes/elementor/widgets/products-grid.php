<?php
namespace Auxin\Plugin\CoreElements\Elementor\Elements;

use Elementor\Plugin;
use Elementor\Core\Files\CSS\Post;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Core\Schemes\Typography;
use Elementor\Utils;
use Elementor\Control_Media;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

/**
 * Elementor 'AdvancedRecentProducts' widget.
 *
 * Elementor widget that displays an 'AdvancedRecentProducts' with lightbox.
 *
 * @since 1.0.0
 */
class ProductsGrid extends Widget_Base {

    /**
     * Get widget name.
     *
     * Retrieve 'AdvancedRecentProducts' widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'aux_product_grid';
    }

    /**
     * Get widget title.
     *
     * Retrieve 'AdvancedRecentProducts' widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return __('Products Grid', 'auxin-elements' );
    }

    /**
     * Get widget icon.
     *
     * Retrieve 'AdvancedRecentProducts' widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'eicon-woocommerce auxin-badge-pro';
    }

    /**
     * Get widget categories.
     *
     * Retrieve 'AdvancedRecentProducts' widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_categories() {
        return array( 'auxin-pro' );
    }

    /**
     * Retrieve the terms in a given taxonomy or list of taxonomies.
     *
     * Retrieve 'AdvancedRecentProducts' widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_terms() {
        // Get terms
        $terms = get_terms(
            array(
                'taxonomy'   => 'product_cat',
                'orderby'    => 'count',
                'hide_empty' => true
            )
        );

        // Then create a list
        $list  = array();

        if ( ! is_wp_error( $terms ) && is_array( $terms ) ){
            foreach ( $terms as $key => $value ) {
                $list[$value->term_id] = $value->name;
            }
        }

        return $list;
    }

    /**
     * Register 'AdvancedRecentProducts' widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_controls() {
        /*-------------------------------------------------------------------*/
        /*  Layout TAB
        /*-------------------------------------------------------------------*/

        /*  Layout Section
        /*-------------------------------------*/

        $this->start_controls_section(
            'layout_section',
            array(
                'label' => __('Layout', 'auxin-elements' ),
                'tab'   => Controls_Manager::TAB_LAYOUT
            )
        );

        $this->add_control(
            'columns',
            array(
                'label'          => __( 'Columns', 'auxin-elements' ),
                'type'           => Controls_Manager::SELECT,
                'default'        => '4',
                'options'        => array(
                    '1'       => '1',
                    '2'       => '2',
                    '3'       => '3',
                    '4'       => '4',
                    '5'       => '5',
                ),
                'frontend_available' => true,
            )
        );

        $this->add_control(
            'num',
            array(
                'label'       => __('Number of posts to show', 'auxin-elements'),
                'label_block' => true,
                'type'        => Controls_Manager::NUMBER,
                'default'     => '8',
                'min'         => 1,
                'step'        => 1
            )
        );
        
        $this->end_controls_section();
        /*-------------------------------------------------------------------*/
        /*  Content TAB
        /*-------------------------------------------------------------------*/

        /*  Query Section
        /*-------------------------------------*/

        $this->start_controls_section(
            'query_section',
            array(
                'label'      => __('Query', 'auxin-elements' ),
            )
        );

        $this->add_control(
            'product_type',
            array(
                'label'        => __('Products Type','auxin-elements' ),
                'label_block'  => true,
                'type'         => Controls_Manager::SELECT,
                'options'      => array(
                    'recent'            => __('Recent Products' , 'auxin-elements'),
                    'featured'          => __('Featured Products' , 'auxin-elements'),
                    'top_rated'         => __('Top Rated Products', 'auxin-elements'),
                    'best_selling'      => __('Best Selling Products'     , 'auxin-elements'),
                    'sale'              => __('On Sale Products'   , 'auxin-elements'),
                    'deal'              => __('Deal Products'   , 'auxin-elements'),
                ),
                'default'      => 'recent',
            )
        );

        $this->add_control(
            'cat',
            array(
                'label'       => __('Categories', 'auxin-elements'),
                'description' => __('Specifies a category that you want to show posts from it. In order to choose the all categories leave the field empty', 'auxin-elements' ),
                'type'        => Controls_Manager::SELECT2,
                'multiple'    => true,
                'options'     => $this->get_terms(),
                'default'     => array(),
            )
        );

        $this->add_control(
            'exclude_without_media',
            array(
                'label'        => __('Exclude products without media','auxin-elements' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'On', 'auxin-elements' ),
                'label_off'    => __( 'Off', 'auxin-elements' ),
                'return_value' => 'yes',
                'default'      => 'yes'
            )
        );

        $this->add_control(
            'order_by',
            array(
                'label'       => __('Order by', 'auxin-elements'),
                'type'        => Controls_Manager::SELECT,
                'default'     => 'date',
                'options'     => array(
                    'date'            => __('Date', 'auxin-elements'),
                    'menu_order date' => __('Menu Order', 'auxin-elements'),
                    'title'           => __('Title', 'auxin-elements'),
                    'ID'              => __('ID', 'auxin-elements'),
                    'rand'            => __('Random', 'auxin-elements'),
                    'comment_count'   => __('Comments', 'auxin-elements'),
                    'modified'        => __('Date Modified', 'auxin-elements'),
                    'author'          => __('Author', 'auxin-elements'),
                    'post__in'        => __('Inserted Post IDs', 'auxin-elements')
                ),
            )
        );

        $this->add_control(
            'order',
            array(
                'label'       => __('Order', 'auxin-elements'),
                'type'        => Controls_Manager::SELECT,
                'default'     => 'DESC',
                'options'     => array(
                    'DESC'          => __('Descending', 'auxin-elements'),
                    'ASC'           => __('Ascending', 'auxin-elements'),
                ),
            )
        );

        $this->add_control(
            'only_products__in',
            array(
                'label'       => __('Only products','auxin-elements' ),
                'description' => __('If you intend to display ONLY specific products, you should specify the products here. You have to insert the Products IDs that are separated by comma (eg. 53,34,87,25).', 'auxin-elements' ),
                'type'        => Controls_Manager::TEXT
            )
        );

        $this->add_control(
            'include',
            array(
                'label'       => __('Include products','auxin-elements' ),
                'description' => __('If you intend to include additional products, you should specify the products here. You have to insert the Products IDs that are separated by comma (eg. 53,34,87,25)', 'auxin-elements' ),
                'type'        => Controls_Manager::TEXT
            )
        );

        $this->add_control(
            'exclude',
            array(
                'label'       => __('Exclude products','auxin-elements' ),
                'description' => __('If you intend to exclude specific products from result, you should specify the products here. You have to insert the Products IDs that are separated by comma (eg. 53,34,87,25)', 'auxin-elements' ),
                'type'        => Controls_Manager::TEXT
            )
        );

        $this->add_control(
            'offset',
            array(
                'label'       => __('Start offset','auxin-elements' ),
                'description' => __('Number of products to displace or pass over.', 'auxin-elements' ),
                'type'        => Controls_Manager::NUMBER,
                'default'     => ''
            )
        );

        $this->end_controls_section();

        /*-------------------------------------------------------------------*/
        /*  Settings TAB
        /*-------------------------------------------------------------------*/
        /*-----------------------------------------------------------------------------------*/
        /*  Rating Style Section
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'wrappers_section',
            array(
                'label'     => __( 'Wrappers', 'auxin-elements' ),
                'tab'       => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_responsive_control(
            'wrapper_margin_bottom',
            array(
                'label' => __( 'Product Bottom space', 'auxin-elements' ),
                'type' => Controls_Manager::SLIDER,
                'range' => array(
                    'px' => array(
                        'max' => 100,
                    ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} .type-product' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'wrapper_info_padding',
            array(
                'label'      => __( 'Info Wrapper Padding', 'auxin-elements' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .aux-shop-info-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'wrapper_meta_padding',
            array(
                'label'      => __( 'Meta Wrapper Padding', 'auxin-elements' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .aux-shop-meta-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'wrapper_desc_padding',
            array(
                'label'      => __( 'Description Wrapper Padding', 'auxin-elements' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .aux-shop-desc-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'wrapper_btn_padding',
            array(
                'label'      => __( 'Buttons Wrapper Padding', 'auxin-elements' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .aux-shop-btns-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        /*-----------------------------------------------------------------------------------*/
        /*  title_style_section
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'title_style_section',
            array(
                'label'     => __( 'Title', 'auxin-elements' ),
                'tab'       => Controls_Manager::TAB_STYLE,
            )
        );

        $this->start_controls_tabs( 'title_colors' );

        $this->start_controls_tab(
            'title_color_normal',
            array(
                'label' => __( 'Normal' , 'auxin-elements' ),
            )
        );

        $this->add_control(
            'title_color',
            array(
                'label' => __( 'Color', 'auxin-elements' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .auxshp-loop-title, {{WRAPPER}} .woocommerce-loop-product__title' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'title_color_hover',
            array(
                'label' => __( 'Hover' , 'auxin-elements' ),
            )
        );

        $this->add_control(
            'title_hover_color',
            array(
                'label' => __( 'Color', 'auxin-elements' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .auxshp-loop-title:hover, {{WRAPPER}} .woocommerce-loop-product__title:hover' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name' => 'title_typography',
                'scheme' => Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .auxshp-loop-title, {{WRAPPER}} .woocommerce-loop-product__title',
            )
        );

        $this->end_controls_section();

        /*-----------------------------------------------------------------------------------*/
        /*  price_style_section
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'price_style_section',
            array(
                'label'     => __( 'Price', 'auxin-elements' ),
                'tab'       => Controls_Manager::TAB_STYLE,
            )
        );

        $this->start_controls_tabs( 'price_colors' );

        $this->start_controls_tab(
            'price_color_normal',
            array(
                'label' => __( 'Normal' , 'auxin-elements' ),
            )
        );

        $this->add_control(
            'price_color',
            array(
                'label' => __( 'Color', 'auxin-elements' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .woocommerce-Price-amount' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'price_color_hover',
            array(
                'label' => __( 'Hover' , 'auxin-elements' ),
            )
        );

        $this->add_control(
            'price_hover_color',
            array(
                'label' => __( 'Color', 'auxin-elements' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .woocommerce-Price-amount:hover' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name' => 'price_typography',
                'scheme' => Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .woocommerce-Price-amount',
            )
        );

        $this->end_controls_section();

        if ( class_exists( 'AUXSHP') ) {

            /*-----------------------------------------------------------------------------------*/
            /*  info_style_section
            /*-----------------------------------------------------------------------------------*/

            $this->start_controls_section(
                'info_style_section',
                array(
                    'label'     => __( 'Product Info', 'auxin-elements' ),
                    'tab'       => Controls_Manager::TAB_STYLE,
                )
            );

            $this->start_controls_tabs( 'info_colors' );

            $this->start_controls_tab(
                'info_color_normal',
                array(
                    'label' => __( 'Normal' , 'auxin-elements' ),
                )
            );

            $this->add_control(
                'info_color',
                array(
                    'label' => __( 'Color', 'auxin-elements' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => array(
                        '{{WRAPPER}} .aux-shop-meta-terms > a' => 'color: {{VALUE}};',
                    ),
                )
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'info_color_hover',
                array(
                    'label' => __( 'Hover' , 'auxin-elements' ),
                )
            );

            $this->add_control(
                'info_hover_color',
                array(
                    'label' => __( 'Color', 'auxin-elements' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => array(
                        '{{WRAPPER}} .aux-shop-meta-terms > a:hover' => 'color: {{VALUE}};',
                    ),
                )
            );

            $this->end_controls_tab();

            $this->end_controls_tabs();

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                array(
                    'name' => 'info_typography',
                    'scheme' => Typography::TYPOGRAPHY_1,
                    'selector' => '{{WRAPPER}} .aux-shop-meta-terms, {{WRAPPER}} .aux-shop-meta-terms a',
                )
            );

            $this->end_controls_section();

            /*-----------------------------------------------------------------------------------*/
            /*  Description Style Section
            /*-----------------------------------------------------------------------------------*/

            $this->start_controls_section(
                'desc_style_section',
                array(
                    'label'     => __( 'Description', 'auxin-elements' ),
                    'tab'       => Controls_Manager::TAB_STYLE,
                )
            );

            $this->start_controls_tabs( 'desc_colors' );

            $this->start_controls_tab(
                'desc_color_normal',
                array(
                    'label' => __( 'Normal' , 'auxin-elements' ),
                )
            );

            $this->add_control(
                'desc_color',
                array(
                    'label' => __( 'Color', 'auxin-elements' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => array(
                        '{{WRAPPER}} .aux-shop-desc-wrapper' => 'color: {{VALUE}};',
                    ),
                )
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'desc_color_hover',
                array(
                    'label' => __( 'Hover' , 'auxin-elements' ),
                )
            );

            $this->add_control(
                'desc_hover_color',
                array(
                    'label' => __( 'Color', 'auxin-elements' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => array(
                        '{{WRAPPER}} .aux-shop-desc-wrapper:hover' => 'color: {{VALUE}};',
                    ),
                )
            );

            $this->end_controls_tab();

            $this->end_controls_tabs();

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                array(
                    'name' => 'desc_typography',
                    'scheme' => Typography::TYPOGRAPHY_1,
                    'selector' => '{{WRAPPER}} .aux-shop-desc-wrapper',
                )
            );

            $this->end_controls_section();
            
            /*-----------------------------------------------------------------------------------*/
            /*  meta_fields_style_section
            /*-----------------------------------------------------------------------------------*/

            $this->start_controls_section(
                'meta_fields_section',
                array(
                    'label'     => __( 'Meta Fields', 'auxin-elements' ),
                    'tab'       => Controls_Manager::TAB_STYLE,
                )
            );

            $this->start_controls_tabs( 'meta_fields_colors' );

            $this->start_controls_tab(
                'meta_fields_color_normal',
                array(
                    'label' => __( 'Normal' , 'auxin-elements' ),
                )
            );

            $this->add_control(
                'meta_fields_color',
                array(
                    'label' => __( 'Color', 'auxin-elements' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => array(
                        '{{WRAPPER}} .aux-shop-meta-field span' => 'color: {{VALUE}};',
                    ),
                )
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'meta_fields_color_hover',
                array(
                    'label' => __( 'Hover' , 'auxin-elements' ),
                )
            );

            $this->add_control(
                'meta_fields_hover_color',
                array(
                    'label' => __( 'Color', 'auxin-elements' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => array(
                        '{{WRAPPER}} .aux-shop-meta-field span:hover' => 'color: {{VALUE}};',
                    ),
                )
            );

            $this->end_controls_tab();

            $this->end_controls_tabs();

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                array(
                    'name' => 'meta_fields_typography',
                    'scheme' => Typography::TYPOGRAPHY_1,
                    'selector' => '{{WRAPPER}} .aux-shop-meta-field span',
                )
            );

            $this->add_responsive_control(
                'meta_fields_padding',
                array(
                    'label' => __( 'Padding', 'auxin-elements' ),
                    'type'       => Controls_Manager::DIMENSIONS,
                    'size_units' => array( 'px', '%' ),
                    'selectors'  => array(
                        '{{WRAPPER}} .aux-shop-meta-field' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ),
                )
            );

            $this->end_controls_section();

            /*-----------------------------------------------------------------------------------*/
            /*  Rating Style Section
            /*-----------------------------------------------------------------------------------*/

            $this->start_controls_section(
                'rating_style_section',
                array(
                    'label'     => __( 'Rating', 'auxin-elements' ),
                    'tab'       => Controls_Manager::TAB_STYLE,
                )
            );


            $this->add_control(
                'rating_empty_color',
                array(
                    'label'     => __( 'Empty Color', 'auxin-elements' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => array(
                        '{{WRAPPER}} .aux-rating-box.aux-star-rating::before' => 'color: {{VALUE}} !important;'
                    )
                )
            );

            $this->add_control(
                'rating_fill_color',
                array(
                    'label'     => __( 'Fill Color', 'auxin-elements' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => array(
                        '{{WRAPPER}} .aux-rating-box.aux-star-rating span::before' => 'color: {{VALUE}} !important;'
                    )
                )
            );

            $this->add_responsive_control(
                'rating_size',
                array(
                    'label'      => __( 'Size', 'auxin-elements' ),
                    'type'       => Controls_Manager::SLIDER,
                    'size_units' => array( 'px', 'em', 'rem' ),
                    'range' => array(
                        'px' => array(
                            'min' => 1,
                            'max' => 200
                        )
                    ),
                    'selectors' => array(
                        '{{WRAPPER}} .aux-star-rating' => 'font-size: {{SIZE}}{{UNIT}};'
                    )
                )
            );


            $this->end_controls_section();
        }

        /*-----------------------------------------------------------------------------------*/
        /*  Badge Style Section
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'badge_style_section',
            array(
                'label'     => __( 'Sales Badge', 'auxin-elements' ),
                'tab'       => Controls_Manager::TAB_STYLE,
            )
        );


        $this->start_controls_tabs( 'badge_colors' );

        $this->start_controls_tab(
            'badge_color_normal',
            array(
                'label' => __( 'Normal' , 'auxin-elements' ),
            )
        );

        $this->add_control(
            'badge_color',
            array(
                'label' => __( 'Color', 'auxin-elements' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .onsale, {{WRAPPER}} .auxin-onsale-badge' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'badge_color_hover',
            array(
                'label' => __( 'Hover' , 'auxin-elements' ),
            )
        );

        $this->add_control(
            'badge_hover_color',
            array(
                'label' => __( 'Color', 'auxin-elements' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .onsale:hover, {{WRAPPER}} .auxin-onsale-badge:hover' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name' => 'badge_typography',
                'scheme' => Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .onsale, {{WRAPPER}} .auxin-onsale-badge',
            )
        );

        $this->add_responsive_control(
            'wrapper_badge_padding',
            array(
                'label'      => __( 'Padding', 'auxin-elements' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .onsale, {{WRAPPER}} .auxin-onsale-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name' => 'header_background',
                'label' => __( 'Background', 'auxin-elements' ),
                'types' => array( 'classic', 'gradient' ),
                'selector' => '{{WRAPPER}} .onsale, {{WRAPPER}} .auxin-onsale-badge'
            )
        );

        $this->end_controls_section();

        if ( class_exists('AUXSHP') ) {
            /*-----------------------------------------------------------------------------------*/
            /*  Badge Style Section
            /*-----------------------------------------------------------------------------------*/

            $this->start_controls_section(
                'feat_badge_style_section',
                array(
                    'label'     => __( 'Featured Badge', 'auxin-elements' ),
                    'tab'       => Controls_Manager::TAB_STYLE,
                )
            );


            $this->start_controls_tabs( 'feat_badge_colors' );

            $this->start_controls_tab(
                'feat_badge_color_normal',
                array(
                    'label' => __( 'Normal' , 'auxin-elements' ),
                )
            );

            $this->add_control(
                'feat_badge_color',
                array(
                    'label' => __( 'Color', 'auxin-elements' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => array(
                        '{{WRAPPER}} .aux-product-featured-badge' => 'color: {{VALUE}};',
                    ),
                )
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'feat_badge_color_hover',
                array(
                    'label' => __( 'Hover' , 'auxin-elements' ),
                )
            );

            $this->add_control(
                'feat_badge_hover_color',
                array(
                    'label' => __( 'Color', 'auxin-elements' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => array(
                        '{{WRAPPER}} .aux-product-featured-badge:hover' => 'color: {{VALUE}};',
                    ),
                )
            );

            $this->end_controls_tab();

            $this->end_controls_tabs();

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                array(
                    'name' => 'feat_badge_typography',
                    'scheme' => Typography::TYPOGRAPHY_1,
                    'selector' => '{{WRAPPER}} .aux-product-featured-badge',
                )
            );

            $this->add_responsive_control(
                'wrapper_feat_badge_padding',
                array(
                    'label'      => __( 'Padding', 'auxin-elements' ),
                    'type'       => Controls_Manager::DIMENSIONS,
                    'size_units' => array( 'px', '%' ),
                    'selectors'  => array(
                        '{{WRAPPER}} .aux-product-featured-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ),
                )
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                array(
                    'name' => 'feat_badge_bg',
                    'label' => __( 'Background', 'auxin-elements' ),
                    'types' => array( 'classic', 'gradient' ),
                    'selector' => '{{WRAPPER}} .aux-product-featured-badge'
                )
            );

            $this->end_controls_section();

        }
        /*-----------------------------------------------------------------------------------*/
        /*  Button
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'btn_section',
            array(
                'label'      => __('Button', 'auxin-elements' ),
                'tab'       => Controls_Manager::TAB_STYLE,
            )
        );

        $this->start_controls_tabs( 'btn_bg_tab' );

        $this->start_controls_tab(
            'btn_bg_normal',
            array(
                'label' => __( 'Normal' , 'auxin-elements' )
            )
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name' => 'btn',
                'label' => __( 'Background', 'auxin-elements' ),
                'types' => array( 'classic', 'gradient' ),
                'selector' => '{{WRAPPER}} .add_to_cart_button, {{WRAPPER}} a.button', 
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'      => 'btn_shadow',
                'selector'  => '{{WRAPPER}} .add_to_cart_button, {{WRAPPER}} a.button'
            )
        );

        $this->add_control(
            'btn_text_color',
            array(
                'label' => __( 'Color', 'auxin-elements' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .add_to_cart_button, {{WRAPPER}} a.button' => 'color: {{VALUE}};',
                )
            )
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            array(
                'name' => 'btn_text_shadow',
                'label' => __( 'Text Shadow', 'auxin-elements' ),
                'selector' => '{{WRAPPER}} .add_to_cart_button, {{WRAPPER}} a.button',
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name' => 'btn_text_typography',
                'scheme' => Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .add_to_cart_button, {{WRAPPER}} a.button'
            )
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'btn_bg_hover',
            array(
                'label' => __( 'Hover' , 'auxin-elements' )
            )
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name' => 'btn_bg_hover',
                'label' => __( 'Background', 'auxin-elements' ),
                'types' => array( 'classic', 'gradient' ),
                'selector' => '{{WRAPPER}} .add_to_cart_button:hover, {{WRAPPER}} a.button:hover',
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'      => 'btn_shadow_hover',
                'selector'  => '{{WRAPPER}} .add_to_cart_button:hover, {{WRAPPER}} a.button:hover'
            )
        );

        $this->add_control(
            'btn_text_color_hover',
            array(
                'label' => __( 'Color', 'auxin-elements' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .add_to_cart_button, {{WRAPPER}} a.button' => 'color: {{VALUE}};',
                )
            )
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            array(
                'name' => 'btn_text_shadow_hover',
                'label' => __( 'Text Shadow', 'auxin-elements' ),
                'selector' => '{{WRAPPER}} .add_to_cart_button, {{WRAPPER}} a.button',
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name' => 'btn_text_typography_hover',
                'scheme' => Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .add_to_cart_button, {{WRAPPER}} a.button'
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'btn_padding',
            array(
                'label'      => __( 'Button Padding', 'auxin-elements' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .add_to_cart_button, {{WRAPPER}} a.button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();


    }

  /**
   * Render image box widget output on the frontend.
   *
   * Written in PHP and used to generate the final HTML.
   *
   * @since 1.0.0
   * @access protected
   */
  protected function render() {

    $settings = $this->get_settings_for_display();

    $args = array(
        'desktop_cnum'          => $settings['columns'],

        // Query section
        'product_type'          => $settings['product_type'], 

        // Query section
        'cat'                   => $settings['cat'],
        'num'                   => $settings['num'],
        'exclude_without_media' => $settings['exclude_without_media'],
        'order_by'              => $settings['order_by'],
        'order'                 => $settings['order'],
        'include'               => $settings['include'],
        'exclude'               => $settings['exclude'],
        'only_products__in'     => $settings['only_products__in'],

    );

    // // get the shortcode base blog page
    echo auxin_widget_products_grid_callback( $args );

  }

}
