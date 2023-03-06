<?php
/**
 * Code highlighter element
 *
 * 
 * @package    Auxin
 * @license    LICENSE.txt
 * @author     averta
 * @link       http://phlox.pro/
 * @copyright  (c) 2010-2023 averta
 */

function auxin_get_recent_posts_masonry_master_array( $master_array ) {

    $categories = get_terms( 'category', 'orderby=count&hide_empty=0' );
    $categories_list = array( ' ' => __('All Categories', 'auxin-elements' ) )  ;
    foreach ( $categories as $key => $value ) {
        $categories_list[$value->term_id] = $value->name;
    }

    // $tags = get_terms( 'post_tag', 'orderby=count&hide_empty=0' );
    // $tags_list;
    // foreach ($tags as $key => $value) {
    //     $tags_list["$value->term_id"] = $value->name;
    // }


    $master_array['aux_recent_posts_masonry'] = array(
        'name'                          => __('Masonry Recent Posts', 'auxin-elements' ),
        'auxin_output_callback'         => 'auxin_widget_recent_posts_masonry_callback',
        'base'                          => 'aux_recent_posts_masonry',
        'description'                   => __('It adds recent posts in masonry style.', 'auxin-elements' ),
        'class'                         => 'aux-widget-recent-posts-masonry aux-column-post-entry',
        'show_settings_on_create'       => true,
        'weight'                        => 1,
        'is_widget'                     => false,
        'is_shortcode'                  => true,
        'category'                      => THEME_NAME,
        'group'                         => '',
        'admin_enqueue_js'              => '',
        'admin_enqueue_css'             => '',
        'front_enqueue_js'              => '',
        'front_enqueue_css'             => '',
        'icon'                          => 'aux-element aux-pb-icons-masonry',
        'custom_markup'                 => '',
        'js_view'                       => '',
        'html_template'                 => '',
        'deprecated'                    => '',
        'content_element'               => '',
        'as_parent'                     => '',
        'as_child'                      => '',
        'params' => array(
            array(
                'heading'          => __('Title','auxin-elements' ),
                'description'      => __('Recent post title, leave it empty if you don`t need title.', 'auxin-elements'),
                'param_name'       => 'title',
                'type'             => 'textfield',
                'std'              => '',
                'value'            => '',
                'holder'           => 'textfield',
                'class'            => 'title',
                'admin_label'      => false,
                'dependency'       => '',
                'weight'           => '',
                'group'            => '',
                'edit_field_class' => ''
            ),
            array(
                'heading'           => __('Categories', 'auxin-elements'),
                'description'       => __('Specifies a category that you want to show posts from it.', 'auxin-elements' ),
                'param_name'        => 'cat',
                'type'              => 'aux_select2_multiple',
                'def_value'         => ' ',
                'holder'            => '',
                'class'             => 'cat',
                'value'             => $categories_list,
                'admin_label'       => false,
                'dependency'        => '',
                'weight'            => '',
                'group'             => __( 'Query', 'auxin-elements' ),
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __('Number of posts to show', 'auxin-elements'),
                'description'       => '',
                'param_name'        => 'num',
                'type'              => 'textfield',
                'value'             => '8',
                'holder'            => '',
                'class'             => 'num',
                'admin_label'       => false,
                'dependency'        => '',
                'weight'            => '',
                'group'             => __( 'Query', 'auxin-elements' ),
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __('Exclude posts without media','auxin-elements' ),
                'description'       => '',
                'param_name'        => 'exclude_without_media',
                'type'              => 'aux_switch',
                'value'             => '0',
                'class'             => '',
                'admin_label'       => false,
                'dependency'        => '',
                'weight'            => '',
                'group'             => __( 'Query', 'auxin-elements' ),
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __('Exclude custom post formats','auxin-elements' ),
                'description'       => '',
                'param_name'        => 'exclude_custom_post_formats',
                'type'              => 'aux_switch',
                'value'             => '0',
                'class'             => '',
                'admin_label'       => false,
                'dependency'        => '',
                'weight'            => '',
                'group'             => __( 'Query', 'auxin-elements' ),
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __('Exclude quote and link post formats','auxin-elements' ),
                'description'       => '',
                'param_name'        => 'exclude_quote_link',
                'type'              => 'aux_switch',
                'value'             => '0',
                'class'             => '',
                'admin_label'       => false,
                'dependency'        => array(
                    'element'       => 'exclude_custom_post_formats',
                    'value'         => array('0', 'false')
                ),
                'weight'            => '',
                'group'             => '',
                'edit_field_class'  => ''
            ),
                        array(
                'heading'            => __('Order by', 'auxin-elements'),
                'description'        => '',
                'param_name'         => 'order_by',
                'type'               => 'dropdown',
                'def_value'          => 'date',
                'holder'             => '',
                'class'              => 'order_by',
                'value'              => array (
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
                'admin_label'       => false,
                'dependency'        => '',
                'weight'            => '',
                'group'             => __( 'Query', 'auxin-elements' ),
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __('Order', 'auxin-elements'),
                'description'       => '',
                'param_name'        => 'order',
                'type'              => 'dropdown',
                'def_value'         => 'DESC',
                'holder'            => '',
                'class'             => 'order',
                'value'             =>array (
                    'DESC'          => __('Descending', 'auxin-elements'),
                    'ASC'           => __('Ascending', 'auxin-elements'),
                ),
                'admin_label'       => false,
                'dependency'        => '',
                'weight'            => '',
                'group'             => __( 'Query', 'auxin-elements' ),
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __('Only posts','auxin-elements' ),
                'description'       => __('If you intend to display ONLY specific posts, you should specify the posts here. You have to insert the post IDs that are separated by comma (eg. 53,34,87,25).', 'auxin-elements' ),
                'param_name'        => 'only_posts__in',
                'type'              => 'textfield',
                'value'             => '',
                'holder'            => '',
                'class'             => '',
                'admin_label'       => false,
                'dependency'        => '',
                'weight'            => '',
                'group'             => __( 'Query', 'auxin-elements' ),
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __('Include posts','auxin-elements' ),
                'description'       => __('If you intend to include additional posts, you should specify the posts here. You have to insert the Post IDs that are separated by comma (eg. 53,34,87,25)', 'auxin-elements' ),
                'param_name'        => 'include',
                'type'              => 'textfield',
                'value'             => '',
                'holder'            => '',
                'class'             => '',
                'admin_label'       => false,
                'dependency'        => '',
                'weight'            => '',
                'group'             => __( 'Query', 'auxin-elements' ),
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __('Exclude posts','auxin-elements' ),
                'description'       => __('If you intend to exclude specific posts from result, you should specify the posts here. You have to insert the Post IDs that are separated by comma (eg. 53,34,87,25)', 'auxin-elements' ),
                'param_name'        => 'exclude',
                'type'              => 'textfield',
                'value'             => '',
                'holder'            => '',
                'class'             => '',
                'admin_label'       => false,
                'dependency'        => '',
                'weight'            => '',
                'group'             => __( 'Query', 'auxin-elements' ),
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __('Start offset','auxin-elements' ),
                'description'       => __('Number of post to displace or pass over.', 'auxin-elements' ),
                'param_name'        => 'offset',
                'type'              => 'textfield',
                'value'             => '',
                'holder'            => '',
                'class'             => '',
                'admin_label'       => false,
                'dependency'        => '',
                'weight'            => '',
                'group'             => __( 'Query', 'auxin-elements' ),
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __('Display post media (image, video, etc)', 'auxin-elements' ),
                'param_name'        => 'show_media',
                'type'              => 'aux_switch',
                'def_value'         => '',
                'value'             => '1',
                'holder'            => '',
                'class'             => 'show_media',
                'admin_label'       => false,
                'dependency'        => '',
                'weight'            => '',
                'group'             => '',
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __('Display post title','auxin-elements' ),
                'description'       => '',
                'param_name'        => 'display_title',
                'type'              => 'aux_switch',
                'value'             => '1',
                'class'             => 'display_title',
                'admin_label'       => false,
                'dependency'        => '',
                'weight'            => '',
                'group'             => '',
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __('Display post meta','auxin-elements' ),
                'description'       => '',
                'param_name'        => 'show_info',
                'type'              => 'aux_switch',
                'value'             => '1',
                'class'             => '',
                'admin_label'       => false,
                'weight'            => '',
                'group'             => '',
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __('Display like button','auxin-elements' ),
                'description'       => sprintf(__('Enable it to display %s like button%s on gride template blog. Please note WP Ulike plugin needs to be activaited to use this option.', 'auxin-elements'), '<strong>', '</strong>'),
                'param_name'        => 'display_like',
                'type'              => 'aux_switch',
                'value'             => '1',
                'holder'            => '',
                'class'             => 'display_like',
                'admin_label'       => false,
                'dependency'        => '',
                'weight'            => '',
                'group'             => '',
                'edit_field_class'  => ''
            ),
            array(
                'heading'          => __('Load More Type','auxin-elements' ),
                'description'      => '',
                'param_name'       => 'loadmore_type',
                'type'             => 'aux_visual_select',
                'value'            => 'scroll',
                'class'            => 'loadmore_type',
                'admin_label'      => false,
                'dependency'       => '',
                'weight'           => '',
                'group'            => '' ,
                'edit_field_class' => '',
                'choices'          => array(
                    ''             => array(
                        'label' => __('None', 'auxin-elements' ),
                        'image' => AUXIN_URL . 'images/visual-select/load-more-none.svg'
                    ),
                    'scroll'       => array(
                        'label' => __('Infinite Scroll', 'auxin-elements' ),
                        'image' => AUXIN_URL . 'images/visual-select/load-more-infinite.svg'
                    ),
                    'next'         => array(
                        'label' => __('Next Button', 'auxin-elements' ),
                        'image' => AUXIN_URL . 'images/visual-select/load-more-button.svg'
                    ),
                    'next-prev'    => array(
                        'label' => __('Next Prev', 'auxin-elements' ),
                        'image' => AUXIN_URL . 'images/visual-select/load-more-next-prev.svg'
                    )
                )
            ),
            array(
                'heading'           => __('Excerpt length','auxin-elements' ),
                'description'       => __('Specify summary content in character.','auxin-elements' ),
                'param_name'        => 'excerpt_len',
                'type'              => 'textfield',
                'value'             => '160',
                'holder'            => '',
                'class'             => 'excerpt_len',
                'admin_label'       => false,
                'dependency'        => '',
                'weight'            => '',
                'group'             => '',
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __('Display author or read more', 'auxin-elements'),
                'description'       => __('Specifies whether to show author or read more on each post.', 'auxin-elements'),
                'param_name'        => 'author_or_readmore',
                'type'              => 'dropdown',
                'def_value'         => 'readmore',
                'holder'            => '',
                'class'             => 'author_or_readmore',
                'value'             => array (
                    'readmore'          => __('Read More', 'auxin-elements'),
                    'author'           => __('Author Name', 'auxin-elements'),
                ),
                'admin_label'       => false,
                'dependency'        => '',
                'weight'            => '',
                'group'             => '',
                'edit_field_class'  => ''
            ),
            array(
                'heading'     => __('Number of columns', 'auxin-elements'),
                'description' => '',
                'param_name'  => 'desktop_cnum',
                'type'        => 'dropdown',
                'def_value'   => '4',
                'holder'      => '',
                'class'       => 'desktop_cnum',
                'value'       => array(
                    '1'  => '1', '2' => '2', '3' => '3',
                    '4'  => '4', '5' => '5', '6' => '6'
                ),
                'admin_label'       => false,
                'dependency'        => '',
                'weight'            => '',
                'group'             => __( 'Layout', 'auxin-elements' ),
                'edit_field_class'  => ''
            ),
            array(
                'heading'     => __('Number of columns in tablet size', 'auxin-elements'),
                'description' => '',
                'param_name'  => 'tablet_cnum',
                'type'        => 'dropdown',
                'value'       => 'inherit',
                'holder'      => '',
                'class'       => 'tablet_cnum',
                'value'       => array(
                    'inherit' => 'Inherited from larger',
                    '1'  => '1', '2' => '2', '3' => '3',
                    '4'  => '4', '5' => '5', '6' => '6'
                ),
                'admin_label'       => false,
                'dependency'        => '',
                'weight'            => '',
                'group'             => __( 'Layout', 'auxin-elements' ),
                'edit_field_class'  => ''
            ),
            array(
                'heading'     => __('Number of columns in phone size', 'auxin-elements'),
                'description' => '',
                'param_name'  => 'phone_cnum',
                'type'        => 'dropdown',
                'def_value'   => '1',
                'holder'      => '',
                'class'       => 'phone_cnum',
                'value'       => array(
                    '1' => '1' , '2' => '2', '3' => '3'
                ),
                'admin_label'       => false,
                'dependency'        => '',
                'weight'            => '',
                'group'             => __( 'Layout', 'auxin-elements' ),
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __('Extra class name','auxin-elements' ),
                'description'       => __('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'auxin-elements' ),
                'param_name'        => 'extra_classes',
                'type'              => 'textfield',
                'value'             => '',
                'def_value'         => '',
                'holder'            => '',
                'class'             => 'extra_classes',
                'admin_label'       => false,
                'dependency'        => '',
                'weight'            => '',
                'group'             => '',
                'edit_field_class'  => ''
            )
        )
    );

    return $master_array;
}

add_filter( 'auxin_master_array_shortcodes', 'auxin_get_recent_posts_masonry_master_array', 10, 1 );




/**
 * Element without loop and column
 * The front-end output of this element is returned by the following function
 *
 * @param  array  $atts              The array containing the parsed values from shortcode, it should be same as defined params above.
 * @param  string $shortcode_content The shorcode content
 * @return string                    The output of element markup
 */
function auxin_widget_recent_posts_masonry_callback( $atts, $shortcode_content = null ){

    // Defining default attributes
    $default_atts = array(
        'title'                       => '',  // header title
        'cat'                         => ' ',
        'num'                         => '8', // max generated entry
        'only_posts__in'              => '',  // display only these post IDs. array or string comma separated
        'include'                     => '',  // include these post IDs in result too. array or string comma separated
        'exclude'                     => '',  // exclude these post IDs from result. array or string comma separated
        'offset'                      => '',
        'paged'                       => '',
        'order_by'                    => 'date',
        'order'                       => 'DESC',
        'exclude_without_media'       => 0,
        'exclude_custom_post_formats' => 0,
        'exclude_quote_link'          => 0,
        'exclude_post_formats_in'     => array(), // the list od post formats to exclude
        'show_media'                  => true,
        'display_like'                => true,
        'display_comments'            => true,
        'display_categories'          => true,
        'max_taxonomy_num'            => '',
        'content_layout'              => '', // entry-boxed
        'post_info_position'          => 'after-title', // entry-boxed
        'excerpt_len'                 => '160',
        'display_title'               => true,
        'show_excerpt'                => true,
        'show_content'                => true,
        'show_info'                   => true,
        'show_date'                   => true,
        'author_or_readmore'          => 'readmore',
        'display_author_header'       => true,
        'display_author_footer'       => false,
        // 'image_aspect_ratio'          => 0.75,
        'desktop_cnum'                => 4,
        'tablet_cnum'                 => 'inherit',
        'phone_cnum'                  => '1',
        'tag'                         => '',

        'crop'                        => false,
        'preloadable'                 => false,
        'preload_preview'             => true,
        'preload_bgcolor'             => '',

        'extra_classes'               => '',
        'extra_column_classes'        => '',
        'custom_el_id'                => '',
        'taxonomy_name'               => 'category',

        'template_part_file'          => 'theme-parts/entry/post-column',
        'extra_template_path'         => '',

        'universal_id'                => '',
        'reset_query'                 => true,
        'use_wp_query'                => false, // true to use the global wp_query, false to use internal custom query
        'wp_query_args'               => array(), // additional wp_query args
        'loadmore_type'               => '', // 'next' (more button), 'scroll', 'next-prev'
        'loadmore_per_page'           => '',
        'base'                        => 'aux_recent_posts_masonry',
        'base_class'                  => 'aux-widget-recent-posts-masonry aux-column-post-entry'
    );

    $result = auxin_get_widget_scafold( $atts, $default_atts, $shortcode_content );
    extract( $result['parsed_atts'] );

    $display_author_footer = auxin_is_true( $display_author_footer );
    $display_author_header = auxin_is_true( $display_author_header );

    // specify the post formats that should be excluded -------
    $exclude_post_formats_in = (array) $exclude_post_formats_in;

    if( $exclude_custom_post_formats ){
        $exclude_post_formats_in = array_merge( $exclude_post_formats_in, array( 'aside', 'gallery', 'image', 'link', 'quote', 'video', 'audio' ) );
    }
    if( $exclude_quote_link ){
        $exclude_post_formats_in[] = 'quote';
        $exclude_post_formats_in[] = 'link';
    }
    $exclude_post_formats_in = array_unique( $exclude_post_formats_in );

    // --------------

    ob_start();

    $tax_args = array();
    if( ! empty( $cat ) && $cat != " " && ( ! is_array( $cat ) || ! in_array( " ", $cat ) ) ) {
        $tax_args = array(
            array(
                'taxonomy' => $taxonomy_name,
                'field'    => 'term_id',
                'terms'    => ! is_array( $cat ) ? explode( ",", $cat ) : $cat
            )
        );
    }

    global $wp_query;

    if( ! $use_wp_query ){

        // create wp_query to get latest items -----------
        $args = array(
            'post_type'               => 'post',
            'orderby'                 => $order_by,
            'order'                   => $order,
            'offset'                  => $offset,
            'paged'                   => $paged,
            'tax_query'               => $tax_args,
            'post_status'             => 'publish',
            'posts_per_page'          => $num,
            'ignore_sticky_posts'     => 1,

            'include_posts__in'       => $include, // include posts in this list
            'posts__not_in'           => $exclude, // exclude posts in this list
            'posts__in'               => $only_posts__in, // only posts in this list

            'exclude_without_media'   => $exclude_without_media,
            'exclude_post_formats_in' => $exclude_post_formats_in
        );

        // ---------------------------------------------------------------------

        // add the additional query args if available
        if( $wp_query_args ){
            $args = wp_parse_args( $args, $wp_query_args );
        }

        // pass the args through the auxin query parser
        $wp_query = new WP_Query( auxin_parse_query_args( $args ) );
    }

    // widget header ------------------------------
    echo wp_kses_post( $result['widget_header'] );
    echo wp_kses_post( $result['widget_title'] );


    $phone_break_point  = 767;
    $tablet_break_point = 1025;

    $show_comments      = true; // shows comments icon
    $post_counter       = 0;
    $post_classes       = '';
    $column_class       = '';
    $item_class         = 'aux-col aux-post-masonry';
    $carousel_attrs     = '';
    $container_class    = 'aux-masonry-layout ';

    if( ! empty( $loadmore_type ) ) {
        $item_class        .= ' aux-ajax-item';
    }

    // generate columns class
    $column_class       = $container_class . ' aux-de-col' . $desktop_cnum;
    $tablet_cnum        = ('inherit' == $tablet_cnum  ) ? $desktop_cnum : $tablet_cnum ;
    $column_class       .=  ' aux-tb-col'.$tablet_cnum . ' aux-mb-col'.$phone_cnum;
    $column_class       .= 'entry-boxed' == $content_layout  ? ' aux-entry-boxed' : '';

    $column_class       .= ' aux-ajax-view ' . $extra_column_classes;

    $column_media_width = auxin_get_content_column_width( $desktop_cnum, 15, $content_width );

    $have_posts = $wp_query->have_posts();

    if( $have_posts ){

        echo ! $skip_wrappers ? sprintf( '<div data-element-id="%s" class="%s">', esc_attr( $universal_id ), esc_attr( $column_class ) ) : '';

        while ( $wp_query->have_posts() ) {

            $wp_query->the_post();
            $post = $wp_query->post;

            $post_vars = auxin_get_post_format_media(
                $post,
                array(
                    'request_from'       => 'archive',
                    'media_width'        => $phone_break_point,
                    'media_size'         => 'large',//array( 'width' =>     $column_media_width, 'height' =>     $column_media_width * $image_aspect_ratio ),
                    'upscale_image'      => true,
                    'crop'               => $crop,
                    'image_from_content' => ! $exclude_without_media, // whether to try to get image from content or not
                    'no_gallery'         => false,
                    'add_image_hw'       => false, // whether add width and height attr or not
                    'preloadable'        => $preloadable,
                    'preload_preview'    => $preload_preview,
                    'preload_bgcolor'    => $preload_bgcolor,
                    'image_sizes'        => 'auto',
                    'srcset_sizes'       => 'auto'
                )
            );

            extract( $post_vars );

            $the_format = get_post_format( $post );

            // add specific class to current classes for each column
            $post_classes  = $has_attach && $show_media ? 'post column-entry' : 'post column-entry no-media';

            printf( '<div class="%s post-%s">', esc_attr( $item_class ), esc_attr( $post->ID ) );
            include auxin_get_template_file( $template_part_file, '', $extra_template_path );
            echo    '</div>';
        }

        if( ! $skip_wrappers ) {
            // End tag for aux-ajax-view wrapper
            echo '</div>';
            // Execute load more functionality
            if( $wp_query->found_posts > $loadmore_per_page ) {
                echo auxin_get_load_more_controller( $loadmore_type );
            }

        } else {
            // Get post counter in the query
            echo '<span class="aux-post-count hidden">'. esc_html( $wp_query->post_count ) .'</span>';
            echo '<span class="aux-all-posts-count hidden">'. esc_html( $wp_query->found_posts ) .'</span>';
        }

    }

    if( $reset_query ){
        wp_reset_query();
    }

    // return false if no result found
    if( ! $have_posts ){
        ob_get_clean();
        return false;
    }

    // widget footer ------------------------------
    echo wp_kses_post( $result['widget_footer'] );

    return ob_get_clean();
}
