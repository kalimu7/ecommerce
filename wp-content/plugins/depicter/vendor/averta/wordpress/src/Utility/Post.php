<?php
namespace Averta\WordPress\Utility;

use Averta\Core\Utility\Str;

class Post
{

    /**
     * Generates an excerpt and trims it by characters from the content outside of loop
     *
     * @param int   $postId                    ID of the post.
     * @param int   $maxCharLength             The maximum number of words in a post excerpt.
     * @param array $excludeStripShortcodeTags
     * @param bool  $skipMoreTag
     *
     * @return string
     */
    public static function getExcerptTrimmedByChars( $postId = null, $maxCharLength = null, $excludeStripShortcodeTags = null, $skipMoreTag = false ) {
        $post = get_post( $postId );
        if( ! isset( $post ) ) return '';

        // If post password required and it doesn't match the cookie.
        if ( post_password_required( $post ) ){
            return get_the_password_form( $post );
        }

        $excerptMore = apply_filters( 'excerpt_more', " ..." );
        $excerptMore = apply_filters( 'averta/wordpress/excerpt/trim/chars/more', $excerptMore );

        if ( $post->post_excerpt ){
            $excerpt = apply_filters( 'get_the_excerpt', $post->post_excerpt );

        } else {
            $postContent      = $post->post_content;
            $content           = $postContent;

            // check for <!--more--> tag
            if ( ! $skipMoreTag && preg_match( '/<!--more(.*?)?-->/', $content, $matches ) ) {
                $content = explode( $matches[0], $content, 2 );

                if ( ! empty( $matches[1] ) ){
                    $moreLinkText = strip_tags( wp_kses_no_null( trim( $matches[1] ) ) );
                    $excerptMore   = ! empty( $moreLinkText ) ? $moreLinkText : $excerptMore;
                }

                return $content[0] . $excerptMore;
            }
            // If char length is defined use it, otherwise use default char length
            $maxCharLength  = empty( $maxCharLength ) ? apply_filters( 'averta/wordpress/excerpt/trim/chars/length', 250 ) : $maxCharLength;

            // Clean post content
            $excerpt = strip_tags( Sanitize::stripShortcodes( $content, $excludeStripShortcodeTags ) );
        }

        $excerpt = Str::trimByChars( $excerpt, $maxCharLength, $excerptMore );

        return apply_filters( 'averta/wordpress/excerpt/trim/chars/result', $excerpt, $post, $content, $postContent, $maxCharLength, $excerptMore );
    }


    /**
     * Generates an excerpt and trims it by words from the content outside of loop
     *
     * @param int   $postId                    ID of the post.
     * @param int   $excerptLength             The maximum number of words in a post excerpt.
     * @param array $excludeStripShortcodeTags
     * @param bool  $skipMoreTag
     *
     * @return string
     */
    public static function getExcerptTrimmedByWords( $postId = null, $excerptLength = null, $excludeStripShortcodeTags = null, $skipMoreTag = false ) {
        $post = get_post( $postId );
        if( ! isset( $post ) ) return '';

        // If post password required and it doesn't match the cookie.
        if ( post_password_required( $post ) )
            return get_the_password_form( $post );

        if ( $post->post_excerpt ) {
            $result = apply_filters( 'get_the_excerpt', $post->post_excerpt );

        } else {
            $content = $post->post_content;
            $content = apply_filters( 'the_content', $content );

            // If excerpt length is defined use it, otherwise use default excerpt length
            $excerptLength = empty( $excerptLength ) ? apply_filters( 'excerpt_length', 55 ) : $excerptLength;
            $excerptMore   = apply_filters( 'excerpt_more', " ..." );

            // check for <!--more--> tag
            if ( ! $skipMoreTag && preg_match( '/<!--more(.*?)?-->/', $content, $matches ) ) {
                $content = explode( $matches[0], $content, 2 );

                if ( ! empty( $matches[1] ) ){
                    $moreLinkText = strip_tags( wp_kses_no_null( trim( $matches[1] ) ) );
                    $excerptMore   = ! empty( $moreLinkText ) ? $moreLinkText : $excerptMore;
                }

                return $content[0] . $excerptMore;
            }

            // Clean post content
            $excerpt = strip_tags( Sanitize::stripShortcodes( $content, $excludeStripShortcodeTags ) );
            $result = wp_trim_words( $excerpt, $excerptLength, $excerptMore );
        }

        return apply_filters( 'averta/wordpress/excerpt/trim/words/result', $result );
    }

    /**
     * Retrieves a post meta field for the given post ID.
     *
     * @param int    $postId   ID of the object metadata is for.
     * @param string $metaKey  Metadata key. If not specified, retrieve all metadata for the specified object.
     * @param string $default  Default metadata value for the specified meta key
     *
     * @return mixed|string
     */
    public static function getMeta( $postId, $metaKey = '', $default = '' ){
        $post = get_post( $postId );

        if( empty( $post ) || empty( $post->ID ) )
            return $default;

        $metaValue = get_metadata( 'post', $post->ID, $metaKey, true );
        return '' === $metaValue ? $default : $metaValue;
    }

}
