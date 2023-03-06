<?php
/**
 * Add content meta box options for post
 *
 * 
 * @package    Auxin
 * @author     averta (c) 2014-2023
 * @link       http://averta.net
*/

// no direct access allowed
if ( ! defined('ABSPATH') )  exit;


/*======================================================================*/

function auxin_metabox_fields_post_content_options(){

    $model         = new Auxin_Metabox_Model();
    $model->id     = 'post-content';
    $model->title  = __('Content options', 'phlox');
    $model->fields = array(

        array(
            'title'         => __( 'Display Content Title', 'phlox' ),
            'description'   => __( 'Enable it to show the main title above post content.', 'phlox' ),
            'id'            => 'aux_post_title_show',
            'id_deprecated' => 'show_title',
            'type'          => 'dropdown',
            'default'       => 'default',
            'choices' => array(
                'default'   => __( 'Theme Default', 'phlox' ) ,
                'yes'       => __( 'Yes', 'phlox' ),
                'no'        => __( 'No', 'phlox' ),
            )
        ),

        array(
            'title'         => __('Content Title Alignment', 'phlox'),
            'description'   => __('Specifies alignment for the title in the page content.', 'phlox'),
            'id'            => 'page_content_title_alignment',
            'type'          => 'radio-image',
            'default'       => 'default',
            'dependency'    => array(
                array(
                     'id'      => 'aux_post_title_show',
                     'value'   => array( 'yes', 'default' ),
                     'operator'=> '=='
                )
            ),
            'choices'       => array(
                'default' => array(
                    'label'     => __('Default', 'phlox'),
                    'css_class' => 'axiAdminIcon-default',
                ),
                'left' => array(
                    'label'     => __('Left', 'phlox'),
                    'css_class' => 'axiAdminIcon-text-align-left'
                ),
                'center' => array(
                    'label'     => __('Center', 'phlox'),
                    'css_class' => 'axiAdminIcon-text-align-center'
                )
            )
        ),

        array(
            'title'         => __( 'Display Post Info', 'phlox' ),
            'description'   => __( 'Enable it to show post meta info.', 'phlox' ),
            'id'            => 'aux_post_info_show',
            'type'          => 'dropdown',
            'default'       => 'default',
            'choices' => array(
                'default'   => __( 'Theme Default', 'phlox' ) ,
                'yes'       => __( 'Yes', 'phlox' ),
                'no'        => __( 'No', 'phlox' ),
            )
        ),

        array(
            'title'       => __('Content Style', 'phlox'),
            'description' => __( 'You can reduce the width of text lines and increase the readability of context (does not affect the width of media). The default (first) choice is the one that you have specified in theme options.[Blog > Single Post > Content Style]', 'phlox' ),
            'id'          => 'post_content_style',
            'type'        => 'radio-image',
            'default'     => 'default',
            'choices'     => array(
                'default' => array(
                    'label'  => __('Default, set theme option', 'phlox'),
                    'image' => AUXIN_URL . 'images/visual-select/default4.svg'
                ),
                'simple'  => array(
                    'label'  => __( 'Simple' , 'phlox'),
                    'image' => AUXIN_URL . 'images/visual-select/content-normal.svg'
                ),
                'medium' => array(
                    'label'  => __( 'Medium Width Content' , 'phlox'),
                    'image' => AUXIN_URL . 'images/visual-select/content-less.svg'
                ),
                'narrow' => array(
                    'label'  => __( 'Narrow Content' , 'phlox'),
                    'image' => AUXIN_URL . 'images/visual-select/content-less.svg'
                )
            )
        )

    );

    return $model;
}
