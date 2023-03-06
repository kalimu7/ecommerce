<?php
/**
 * Layout option for pages
 *
 * 
 * @package    Auxin
 * @author     averta (c) 2014-2023
 * @link       http://averta.net
*/

// no direct access allowed
if ( ! defined('ABSPATH') )  exit;


/*==================================================================================================

    Add Page Option meta box

 *=================================================================================================*/

function auxin_metabox_fields_general_layout(){

    $model         = new Auxin_Metabox_Model();
    $model->id     = 'layout-options';
    $model->title  = __( 'Layout Options', 'phlox');
    $model->fields = array(

        array(
            'title'         => __('Content Layout', 'phlox'),
            'description'   => __('If you select "Full", Content fills the full width of the page.', 'phlox'),
            'id'            => 'content_layout',
            'type'          => 'radio-image',
            'default'       => 'default',
            'dependency'    => '',
            'choices'       => array(
                'default'   => array(
                    'label'     => __( 'Theme Default', 'phlox' ),
                    'css_class' => 'axiAdminIcon-default'
                ),
                'boxed'     => array(
                    'label'     => __('Boxed Content', 'phlox' ),
                    'css_class' => 'axiAdminIcon-content-boxed'
                ),
                'full'      => array(
                    'label'     => __('Full Content', 'phlox' ),
                    'css_class' => 'axiAdminIcon-content-full'
                )
            )
        ),

        array(
            'title'         => __('Sidebar Layout', 'phlox'),
            'description'   => __('Specifies the position of sidebar on this page.', 'phlox'),
            'id'            => 'page_layout',
            'id_deprecated' => 'page_layout',
            'type'          => 'radio-image',
            'default'       => 'default',
            'choices'       => array(
                'default' => array(
                    'label'     => __( 'Theme Default', 'phlox' ),
                    'css_class' => 'axiAdminIcon-default'
                ),
                'no-sidebar' => array(
                    'label'  => __('No Sidebar', 'phlox'),
                    'css_class' => 'axiAdminIcon-sidebar-none'
                ),
                'right-sidebar' => array(
                    'label'  => __('Right Sidebar', 'phlox'),
                    'css_class' => 'axiAdminIcon-sidebar-right'
                ),
                'left-sidebar' => array(
                    'label'  => __('Left Sidebar' , 'phlox'),
                    'css_class' => 'axiAdminIcon-sidebar-left'
                ),
                'left2-sidebar' => array(
                    'label'  => __('Left Left Sidebar' , 'phlox'),
                    'css_class' => 'axiAdminIcon-sidebar-left-left'
                ),
                'right2-sidebar' => array(
                    'label'  => __('Right Right Sidebar' , 'phlox'),
                    'css_class' => 'axiAdminIcon-sidebar-right-right'
                ),
                'left-right-sidebar' => array(
                    'label'  => __('Left Right Sidebar' , 'phlox'),
                    'css_class' => 'axiAdminIcon-sidebar-left-right'
                ),
                'right-left-sidebar' => array(
                    'label'  => __('Right Left Sidebar' , 'phlox'),
                    'css_class' => 'axiAdminIcon-sidebar-left-right'
                )
            )
        ),
        array(
            'title'       => __('Sidebar Style', 'phlox'),
            'description' => __('Specifies the style of sidebar on this page.', 'phlox'),
            'id'          => 'page_sidebar_style',
            'type'        => 'radio-image',
            'default'     => 'default',
            'choices'     => array(
                'default' => array(
                    'label'     => __( 'Theme Default', 'phlox' ),
                    'image' => AUXIN_URL . 'images/visual-select/default-large.svg'
                ),
                'simple'  => array(
                    'label'  => __( 'Simple' , 'phlox'),
                    'image' => AUXIN_URL . 'images/visual-select/sidebar-style-1.svg'
                ),
                'border' => array(
                    'label'  => __( 'Bordered Sidebar' , 'phlox'),
                    'image' => AUXIN_URL . 'images/visual-select/sidebar-style-2.svg'
                ),
                'overlap' => array(
                    'label'  => __( 'Overlap Background' , 'phlox'),
                    'image' => AUXIN_URL . 'images/visual-select/sidebar-style-3.svg'
                )
            )
        ),

        array(
            'title'         => __('Display Content Top Margin', 'phlox'),
            'description'   => __('whether to display a space between title and content or not. If you need to start your content from very top of the page, disable it.', 'phlox'),
            'id'            => 'show_content_top_margin',
            'id_deprecated' => 'axi_show_content_top_margin',
            'type'          => 'select',
            'default'       => 'default',
            'choices'       => array(
                'default' => __( 'Theme Default', 'phlox' ),
                'yes'     => __( 'Yes', 'phlox' ),
                'no'      => __( 'No', 'phlox' ),
            ),
        ),

        array(
            'title'       => __( 'Display go to top button', 'phlox' ),
            'description' => __( 'Enable it to display Go to Top button.', 'phlox' ),
            'id'          => 'page_show_goto_top_btn',
            'type'        => 'select',
            'choices'     => array(
                'default' => __( 'Theme Default', 'phlox' ),
                'yes'     => __( 'Yes', 'phlox' ),
                'no'      => __( 'No', 'phlox' ),
            ),
            'default'     => 'default',
        ),

        array(
            'title'       => __( 'Go to top button position', 'phlox' ),
            'description' => __( 'Specifies the position of Go to Top button.', 'phlox' ),
            'id'          => 'page_goto_top_alignment',
            'type'        => 'select',
            'dependency'  => array(
                array(
                    'id'    => 'page_show_goto_top_btn',
                    'value' => array('yes')
                )
            ),
            'choices'   => array(
                'default' => __( 'Theme Default', 'phlox' ),
                'left'    =>  __( 'Left', 'phlox' ),
                'center'  =>  __( 'Center', 'phlox' ),
                'right'   =>  __( 'Right', 'phlox' ),
            ),
            'default'     => 'default',
        ),

        array(
            'title'       => __( 'Custom Page Max Width', 'phlox' ),
            'description' => __( 'Specifies the maximum width for this page.', 'phlox' ),
            'id'          => 'page_max_width_layout',
            'type'        => 'select',

            'choices'     => array(
                ''      => __( 'Theme Default', 'phlox' ),
                'nd'    => __( '1000 Pixels', 'phlox' ),
                'hd'    => __( '1200 Pixels', 'phlox' ),
                'xhd'   => __( '1400 Pixels', 'phlox' ),
                's-fhd' => __( '1600 Pixels', 'phlox' ),
                'fhd'   => __( '1900 Pixels', 'phlox' )
            ),
            'default'   => 'default'
        )

    );

    return $model;
}
