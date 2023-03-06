<?php
/**
 * A class for creating widgets dynamically base of Master widget list
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


if( ! class_exists( 'Auxin_Widget' ) ) :


class Auxin_Widget extends WP_Widget {

    private $defaults = array();
    public  $fields   = array();

    public  $widget_fun_name;

    private $dependency_list = array();
    private $widget_info;

    private $attach_ids_list = null;
    private $att_ids = null;

    /**
     * Sets up the widgets name, Id, description and etc
     */
    public function __construct( $widget_info ) {

        parent::__construct( $widget_info['base_ID'] , $name = $widget_info['name'], $widget_info['args'] );

        $this->widget_info          = $widget_info;
        $this->fields               = $widget_info['params'];
        $this->widget_fun_name      = $widget_info['auxin_output_callback'];

        $this->set_defaults();
    }


    private function set_defaults(){
        foreach ( $this->fields as $field ) {
            $this->defaults[ $field["id"] ] = $field["value"];
        }
        $this->defaults[ '__uid' ] = $this->widget_info['base_ID'] . '_' . substr( uniqid( ''. rand() ), -8 );
    }

    //
    /**
     * Outputs the content of the widget
     *
     * @param  array $args      The field keys and their real values
     * @param  array $instance  The widget name, id and global configs
     * @return string           The widget output for front-end
     */
    public function widget( $args, $instance ) {
        // if the 'widget_info' was available in passed array, we can determine
        // whether the array is from widget class or not

        // make sure to pass same class name for wrapper to widget too
        if( isset( $this->widget_info['base_class'] ) ){
            $args['base_class'] = $this->widget_info['base_class'];
        }
        $instance['widget_info'] = $args;

        if( function_exists( $this->widget_fun_name ) ){
            echo call_user_func( $this->widget_fun_name, $instance );
        } else {
            auxin_error( __('The callback for widget does not exists.', 'auxin-elements') );
        }
    }


    /**
   * Outputs the options form on admin
   *
   * @param array $instance The widget options
   */
    public function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, $this->defaults );

        echo '<div id="' . esc_attr( $this->defaults[ '__uid' ] ) . '" class="auxin-admin-widget-wrapper">';

        // creates id attributes for fields to be saved by update()
        foreach ( $this->fields as $field ) {
            $id   = $field['id'];
            // make sure description is set
            $field["description"] = ! isset( $field["description"] ) ? '': $field["description"];

            $this->watch_for_field_dependencies( $field );

            switch ( $field['type'] ) {

                case 'aux_taxonomy':

                    $categories = get_terms( array(
                        'taxonomy'   => $field['taxonomy'],
                        'orderby'    => 'count',
                        'hide_empty' => true
                    ));


                    $categories_list = array( ' ' => __('All Categories', 'auxin-elements' ) )  ;
                    foreach ( $categories as $key => $value ) {
                       $categories_list[ $value->term_id ] = $value->name;
                    }

                    if( gettype( $instance[ $id ] ) === "string" ) {
                        $select = array( $instance[ $id ] );
                    } else {
                        $select = $instance[ $id ];
                    }

                    echo '<div class="aux-element-field aux-multiple-selector ">',
                    '<label for="' . esc_attr( $this->get_field_id($id) ) . '" >'. esc_html( $field["name"] ) .'</label>',
                    '<select multiple="multiple" name="' . esc_attr( $this->get_field_name($id) ) . '" id="' . esc_attr( $this->get_field_name($id) ) . '" class="aux-orig-select2 aux-admin-select2 aux-select2-multiple">';
                    echo '<option value="">' . esc_html__('Choose ..', 'auxin-elements') . '</option>';

                    foreach ( $categories_list as $key => $value ) {
                        printf(
                            '<option value="%s" class="hot-topic" %s style="margin-bottom:3px;">%s</option>',
                            esc_attr( $key ),
                            in_array( $key, $select) ? 'selected="selected"' : '',
                            esc_html( $value )
                        );
                    }

                    echo '</select>';
                    if ( $field["description"] ) {
                        echo '<p class="option-description">' . esc_html( $field["description"] ) . '</p>';
                    }
                    echo '</div>';

                break;

                case 'iconpicker':
                case 'aux_iconpicker':
                    $font_icons = Auxin()->Font_Icons->get_icons_list('fontastic');
                    echo '<div class="aux-element-field aux-iconpicker">';
                    echo '<label for="'. esc_attr( $this->get_field_name($id) ).'" >'. esc_html( $field["name"]  ).'</label><br />';
                    echo sprintf( '<select name="%1$s" id="%1$s" class="aux-fonticonpicker aux-select" >', esc_attr( $this->get_field_name($id) ) );
                    echo '<option value="">' . esc_html__('Choose ..', 'auxin-elements') . '</option>';

                    if( is_array( $font_icons ) ){
                        foreach ( $font_icons as $icon ) {
                            $icon_id = trim( $icon->classname, '.' );
                            echo '<option value="'. esc_attr( $icon_id ) .'" '. selected( $instance[$id], $icon_id, false ) .' >'. esc_html( $icon->name ) . '</option>';

                        }
                    }

                    echo '</select>';
                    if ( $field["description"] ) {
                        echo '<p class="option-description">' . esc_html( $field["description"] ) . '</p>';
                    }
                    echo '</div>';

                break;



                case 'textarea_html':
                case 'textarea_raw_html':
                    echo '<div class="aux-element-field aux-visual-selector">',
                    '<label for="'.esc_attr( $this->get_field_id($id) ).'" >'. esc_html( $field["name"]  ).'</label>',
                    '<textarea class="widefat" id="'.esc_attr( $this->get_field_id($id) ).'" name="'.esc_attr( $this->get_field_name($id) ).'" name="'. esc_attr( $this->get_field_name($id) ) .'">',
                    wp_kses_post( $instance[$id] ).'</textarea>';
                    if ( $field["description"] ) {
                        echo '<p class="option-description textarea-desc">' . esc_html( $field["description"] ) . '</p>';
                    }
                    echo '</div>';
                break;

                case 'textbox':
                case 'textfield':
                    echo '<div class="aux-element-field aux-visual-selector">',
                        '<label for="'. esc_attr( $this->get_field_id($id) ) .'" >'. esc_html( $field["name"]  ).'</label>',
                        '<input class="widefat" id="'. esc_attr( $this->get_field_id($id) ) .'" name="'. esc_attr( $this->get_field_name($id) ) .'" type="text" value="'. esc_attr( $instance[$id] ) .'" />';
                    if ( $field["description"] ) {
                        echo '<p class="option-description">' . esc_html( $field["description"] ) . '</p>';
                    }
                    echo '</div>';
                break;

                case 'dropdown':
                case 'select':
                    if( is_array( $instance[$id] ) ){
                        if( ! empty( $instance['def_value'] ) ){
                            $current_value = $instance['def_value'];
                        } else {
                            $default_value = array_keys( $instance[ $id ] );
                            $current_value = $default_value[0];
                        }
                    } else {
                        $current_value = $instance[ $id ];
                    }
                    echo '<div class="aux-element-field aux-dropdown">',
                        '<label for="'. esc_attr( $this->get_field_id( $id ) ) .'" >'. esc_html( $field['name'] ) . '</label>',
                        '<select name="' . esc_attr( $this->get_field_name( $id ) ) . '" id="' . esc_attr( $this->get_field_id( $id ) ) . '" value="' . esc_attr( $current_value ) . '" >';
                    foreach ( $field['options'] as $key => $value ) {
                        printf( '<option value="%s" %s >%s</option>', esc_attr( $key ), selected( $current_value, $key, false ), esc_html( $value ) );
                    }
                    echo '</select>';

                    if ( $field["description"] ) {
                        echo '<p class="option-description">' . esc_html( $field["description"] ) . '</p>';
                    }
                    echo '</div>';
                break;

                // Select2 single
                case 'aux_select2_single' :
                    if( is_array( $instance[$id] ) ){
                        if( ! empty( $instance['def_value'] ) ){
                            $current_value = $instance['def_value'];
                        } else {
                            $default_value = array_keys( $instance[ $id ] );
                            $current_value = $default_value[0];
                        }
                    } else {
                        $current_value = $instance[ $id ];
                    }
                    echo '<div class="aux-element-field aux-multiple-selector ">',
                    '<label for="'. esc_attr( $this->get_field_id($id) ) .'" >'. esc_html( $field["name"] ) .'</label>',
                    '<div class="section-row-right" >' ,
                        '<select name="'. esc_attr( $this->get_field_name($id) ) .'" id="'. esc_attr( $this->get_field_id($id) ) .'" class="aux-orig-select2 aux-admin-select2 aux-select2-single" data-value="'. esc_attr( $current_value ) .'"  value="'. esc_attr( $current_value ) .'" style="width:150px" >';
                            foreach ( $field['options'] as $key => $value ) {
                                printf( '<option value="%s" %s >%s</option>', esc_attr( $key ), selected( $current_value, $key, false ), esc_html( $value ) );
                            }
                            echo '</select></div>';
                        if ( $field["description"] ) {
                            echo '<p class="option-description">' . esc_html( $field["description"] ) . '</p>';
                        }
                    echo '</div>' ;
                break;

                // defining of aux_select2_multiple field type for widget.
                case 'aux_select2_multiple' :

                    if( gettype( $instance[ $id ] ) ==="string" ) {
                        $select = explode( ',', $instance[ $id ] );
                    } else {
                        $select = $instance[ $id ];
                    }

                    echo '<div class="aux-element-field aux-multiple-selector ">';
                    echo '<select multiple="multiple" name="'. esc_attr( $this->get_field_name($id) ) .'" id="'. esc_attr( $this->get_field_id($id) ) .'"  style="width:100%" '  . ' class="wpb-multiselect wpb_vc_param_value aux-select2-multiple ' . esc_sql( $field['id'] ) . ' ' .  esc_attr( $field['type'] ) . '_field">';

                    foreach ( $field['options'] as $key => $value ) {
                        $active_attr = in_array( $key, $select) ? 'selected="selected"' : '';
                        echo sprintf( '<option value="%s" %s >%s</option>', esc_attr( $key ), $active_attr, esc_html( $value )  );
                    }

                    echo '</select>';

                    if ( $field["description"] ) {

                        echo '<p class="option-description">' . esc_html( $field["description"] ) . '</p>';
                    }

                    echo '</div>' ;

                break;

                case 'aux_visual_select':
                    echo '<div class="aux-element-field aux-visual-selector">';
                    echo '<label for="'. esc_attr( $this->get_field_id($id) ) .'" >'. esc_html( $field["name"] ) .'</label>';
                    echo '<select class="meta-select visual-select-wrapper" name="' . esc_attr( $this->get_field_name( $id ) ) . '" id="' . esc_attr( $this->get_field_id( $id ) ) . '" value="' . esc_attr( $instance[$id] ) . '" >';

                    $tmp_instance_id = $instance[$id];
                    foreach ( $field['choices'] as $id => $option_info ) {

                       echo sprintf( '<option value="%s" %s %s %s %s>%s</option>',
                            esc_attr( $id ),
                            ( $tmp_instance_id == $id ? ' selected ' : "" ),
                            ( empty( $option_info['css_class'] ) && ! empty( $option_info['image'] ) ? 'data-symbol="'. esc_attr( $option_info['image'] ) .'"' : '' ),
                            ( ! empty( $option_info['video_src'] ) ? 'data-video-src="'. esc_attr( $option_info['video_src'] ).'"' : '' ),
                            ( ! empty( $option_info['css_class'] ) ? 'data-class="'. esc_attr( $option_info['css_class'] ) .'"' : '' ),
                            esc_html( $option_info['label'] )
                        );
                    }

                    echo '</select>';
                    if ( $field["description"] ) {
                        echo '<p class="option-description visual-selector-desc">' . esc_html( $field["description"] ) . '</p>';
                    }
                    echo '</div>';
                break;

                case 'checkbox':
                case 'aux_switch':
                    $instance[$id] = isset( $instance[$id] ) ? (bool)$instance[$id]  : false;
                    $tick = $instance[$id]? 'checked="checked"': '';
                        echo '<div class="aux-element-field aux_switch">',
                            '<input class="hidden_aux_switch" type="hidden" value="0" id="_'. esc_attr( $this->get_field_id($id) ) .'-hidden" name="'. esc_attr( $this->get_field_name($id) ) .'"  >',
                            '<input class="checkbox widefat aux_switch" type="checkbox" ' . $tick . ' id="'. esc_attr( $this->get_field_id($id) ) .'" name="'. esc_attr( $this->get_field_name($id) ) .'" >',
                            '<label for="'. esc_attr( $this->get_field_id($id) ) .'" >'. esc_html( $field["name"] ) .'</label>';

                         if ( $field["description"] ) {
                            echo '<p class="option-description">' . esc_html( $field["description"] ) . '</p>';
                        }
                        echo '</div>';

                break;

                case 'color':
                case 'colorpicker':
                    echo '<div class="aux-element-field aux-colorpicker">',
                        '<label for="'. esc_attr( $this->get_field_id($id) ) .'" >'. esc_html( $field["name"] ) .'</label>',
                        '<div class="mini-color-wrapper"><input id="'. esc_attr( $this->get_field_id($id) ) .'" name="'. esc_attr( $this->get_field_name($id) ) .'" type="text"type="text" value="'. esc_attr( $instance[$id] ) .'"  ></div>';
                    if ( $field["description"] ) {
                        echo '<p class="option-description">' . esc_html( $field["description"] ) . '</p>';
                    }
                    echo '</div>';
                break;

                case 'aux_select_image':
                case 'attach_image':
                    // Store attachment src for avertaAttachMedia field
                    if( !empty($instance[$id]) ) {
                        $att_ids = explode( ',', $instance[$id] );
                        $attach_ids_list = auxin_get_the_resized_attachment_src( $att_ids, 80, 80, true );
                            if(!empty($att_ids)) {
                                printf( "<script>auxin.attachmedia = jQuery.extend( auxin.attachmedia, %s );</script>", wp_json_encode( array_unique( $attach_ids_list ) ) );
                            }
                    }
                    echo '<div class="aux-element-field av3_container aux_select_image axi-attachmedia-wrapper">',
                            '<label for="'. esc_attr( $this->get_field_id($id) ) .'" >'. esc_html( $field["name"] ) .'</label>',
                                '<input type="text" class="white" name="'. esc_attr( $this->get_field_name( $id ) ).'" ' . 'id="'. esc_attr( $this->get_field_id( $id ) ).'" ' . 'value="' . esc_attr( $instance[$id] ) .
                                '" data-media-type="image" data-limit="1" data-multiple="0"
                                data-add-to-list="'. esc_html__('Add Image', 'auxin-elements') .'"
                                data-uploader-submit="'. esc_html__('Add Image', 'auxin-elements') .'"
                                data-uploader-title="'. esc_html__('Select Image', 'auxin-elements') .'"> ';
                                if ( $field["description"] ) {
                                    echo '<p class="option-description">' . esc_html( $field["description"] ) . '</p>';
                                }
                    echo  '</div>';
                break;

                case 'aux_select_images':
                case 'attach_images':
                    // Store attachment src for avertaAttachMedia field
                    if( !empty($instance[$id]) ) {
                        $att_ids = explode( ',', $instance[$id] );
                        $attach_ids_list = auxin_get_the_resized_attachment_src( $att_ids, 80, 80, true );
                            if(!empty($att_ids)) {
                                printf( "<script>auxin.attachmedia = jQuery.extend( auxin.attachmedia, %s );</script>", wp_json_encode( array_unique( $attach_ids_list ) ) );
                            }
                    }
                    echo  '<div class="aux-element-field av3_container aux_select_image axi-attachmedia-wrapper">',
                            '<label for="'. esc_attr( $this->get_field_id($id) ) .'" >'. esc_html( $field["name"] ) .'</label>',
                                '<input type="text" class="white" name="'. esc_attr( $this->get_field_name($id) ) .'" ' . 'id="'. esc_attr( $this->get_field_id($id) ) .'" ' . 'value="' . esc_attr( $instance[$id] ) .
                                '" data-media-type="image" data-limit="9999" data-multiple="1"
                                data-add-to-list="'.esc_html__('Add Image', 'auxin-elements').'"
                                data-uploader-submit="'.esc_html__('Add Image', 'auxin-elements').'"
                                data-uploader-title="'.esc_html__('Select Image', 'auxin-elements').'"> ';
                                if ( $field["description"] ) {
                                    echo '<p class="option-description">' . esc_html( $field["description"] ) . '</p>';
                                }
                    echo  '</div>';
                break;

                case 'aux_select_video':
                case 'attach_video':

                  // Store attachment src for avertaAttachMedia field
                    if( !empty($instance[$id]) ) {
                        $att_ids = explode( ',', $instance[$id] );
                        $attach_ids_list = auxin_get_the_resized_attachment_src( $att_ids, 80, 80, true );
                            if(!empty($att_ids)) {
                                printf( "<script>auxin.attachmedia = jQuery.extend( auxin.attachmedia, %s );</script>", wp_json_encode( array_unique( $attach_ids_list ) ) );
                            }
                    }
                    echo '<div class="aux-element-field av3_container aux_select_image axi-attachmedia-wrapper">',
                                '<label for="'. esc_attr( $this->get_field_id($id) ) .'" >'. esc_html( $field["name"] ) .'</label>',
                                '<input type="text" class="white" name="'. esc_attr( $this->get_field_name($id) ) .'" ' . 'id="'. esc_attr( $this->get_field_id($id) ) .'" ' . 'value="' . esc_attr( $instance[$id] ) .
                                '" data-media-type="video" data-limit="1" data-multiple="0"
                                data-add-to-list="'.esc_html__('Add Video', 'auxin-elements').'"
                                data-uploader-submit="'.esc_html__('Add Video', 'auxin-elements').'"
                                data-uploader-title="'.esc_html__('Select Video', 'auxin-elements').'"> ';
                                if ( $field["description"] ) {
                                    echo '<p class="option-description">' . esc_html( $field["description"] ) . '</p>';
                                }
                    echo  '</div>';
                break;

                case 'aux_select_audio':
                case 'attach_audio':

                    // Store attachment src for avertaAttachMedia field
                    if( !empty($instance[$id]) ) {
                        $att_ids = explode( ',', $instance[$id] );
                        $attach_ids_list = auxin_get_the_resized_attachment_src( $att_ids, 80, 80, true );
                            if(!empty($att_ids)) {
                                printf( "<script>auxin.attachmedia = jQuery.extend( auxin.attachmedia, %s );</script>", wp_json_encode( array_unique( $attach_ids_list ) ) );
                            }
                    }
                    echo '<div class="aux-element-field av3_container aux_select_image axi-attachmedia-wrapper">',
                                '<label for="'. esc_attr( $this->get_field_id($id) ) .'" >'. esc_html( $field["name"] ) .'</label>',
                                '<input type="text" class="white" name="'. esc_attr( $this->get_field_name($id) ) .'" ' . 'id="'. esc_attr( $this->get_field_id($id) ) .'" ' . 'value="' . esc_attr( $instance[$id] ) .
                                '" data-media-type="audio" data-limit="1" data-multiple="0"
                                data-add-to-list="'.esc_html__('Add Audio', 'auxin-elements').'"
                                data-uploader-submit="'.esc_html__('Add Audio', 'auxin-elements').'"
                                data-uploader-title="'.esc_html__('Select Audio', 'auxin-elements').'"> ';
                                if ( $field["description"] ) {
                                    echo '<p class="option-description">' . esc_html( $field["description"] ) . '</p>';
                                }
                    echo  '</div>';

                default:

                break;
            }

        }

        echo '</div>';


        // axpp( $this->dependency_list );
        $this->print_dependencies();
    }


    /**
     * Loop to collect dependency map of metafields
     *
     * @param  array  $field field options
     * @return void
     */
    public function watch_for_field_dependencies( $field = array() ){
        if( empty( $field ) ){
            return;
        }

        $field_dependencies = array();

        if( isset( $field['dependency'] ) && ! empty( $field['dependency'] ) ){

            $depend = $field['dependency'];

            if( isset( $depend['element'] ) && ( isset( $depend['value'] ) && ! empty( $depend['value'] ) ) ){

                unset( $depend['relation'] );
                unset( $depend['callback'] );

                $field_dependencies[ $depend['element'] ] = array( 'value' => (array)$depend['value'] );
            }

        }

        if( $field_dependencies ){
            $this->dependency_list[ $field['id'] ] = $field_dependencies;
        }
    }

    /**
     * Print metafield dependencies
     *
     * @return string  JSON string containing metafield dependencies
     */
    public function print_dependencies(){
        // echo js dependencies
        printf( '<script>
                 function auxinCreateNamespace(n){for(var e=n.split("."),a=window,i="",r=e.length,t=0;r>t;t++)"window"!=e[t]&&(i=e[t],a[i]=a[i]||{},a=a[i]);return a;}
                 auxinCreateNamespace("auxin.elements.%3$s");
                 auxin.elements.%3$s.dependencies = %2$s;
                 auxin.elements.%3$s.baseid = "%1$s";</script>',
                 $this->widget_info['base_ID'],
                 wp_json_encode( $this->dependency_list ),
                 $this->defaults[ '__uid' ]
        );
    }


    /**
   * Processing widget options on save
   *
   * @param array $new_instance The new options
   * @param array $old_instance The previous options
   */
    function update( $new_instance, $old_instance ) {
        $instance     = $old_instance;
        // TODO: we exclode the defaults because on checkbox there is no this value on unchecked and it replaces with defaults
        // $new_instance = wp_parse_args( (array) $new_instance, $this->defaults );
        $new_instance = wp_parse_args( (array) $new_instance );
        foreach ( $this->fields as $field ) {
            $id = $field["id"];

            if( ! isset( $new_instance[ $id ] ) ) {
                continue;
            }

            if( $field["type"] == "aux_switch" ) {
                $instance[ $id ] = !empty($new_instance[$id ] ) ?  1 : 0;
            }
            if( $field["type"] == "aux_select2_multiple" ) {
                $instance[ $id ] = esc_sql( $new_instance[ $id ] );

            } else {
                $instance[ $id ] = is_array( $new_instance[ $id ] ) ? $new_instance[ $id ] : strip_tags( $new_instance[ $id ] );
            }

        }

        return $instance;
    }


} // end widget class

endif;
