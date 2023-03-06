<?php
/**
 * Add background setting meta box Model
 *
 * 
 * @package    Auxin
 * @author     averta (c) 2014-2023
 * @link       http://averta.net
*/

function auxin_metabox_fields_general_background(){

    $model         = new Auxin_Metabox_Model();
    $model->id     = 'general-background';
    $model->title  = __('Background Setting', 'phlox');
    $model->fields = array(

        array(
            'title'         => __('You can specify custom background for this page.<br/>', 'phlox'),
            'description'   => 'Please note that you need to set site layout to "Boxed" in order to see custom background. ("site layout" option is available in theme option panel)',
            'type'          => 'sep',
        ),
        array(
            'title'         => __('Enable Background', 'phlox'),
            'description'   => __('Enable it to display custom background for this page.', 'phlox'),
            'id'            => 'aux_custom_bg_show',
            'id_deprecated' => 'axi_show_custom_background',
            'type'          => 'switch',
            'default'       => '0'
        ),
        array(
            'title'         => __('Background color', 'phlox'),
            'description'   => __('Specifies background color.', 'phlox'),
            'id'            => 'aux_custom_bg',
            'id_deprecated' => 'axi_custom_background',
            'type'          => 'background',
            'default'       => '',
            'dependency'    => array(
                array(
                     'id'      => 'aux_custom_bg_show',
                     'value'   => '1',
                     'operator'=> '=='
                )
            ),
            'default' => array(
                'image'    => '',
                'repeat'   => 'repeat',
                'size'     => 'auto',
                'attach'   => 'scroll',
                'pattern'  => '',
                'position' => 'left-top',
                'color'    => ''
            )
        )

    );

    return $model;
}
