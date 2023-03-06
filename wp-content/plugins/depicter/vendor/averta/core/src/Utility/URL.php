<?php
namespace Averta\Core\Utility;

class URL
{
    /**
     * Remove query args from url
     *
     * @param string $url
     * @param array $args
     * @return string
     */
    public static function removeQueryArg( $url, $args = [] ){

        if ( is_array( $args ) ) {
            foreach ( $args as $arg ) {
                $pattern = "/(?:&|(\?))$arg=[^&]*(?(1)&|)?/";
                $url = preg_replace( $pattern, '', $url);
            }
        } else {
            $pattern = "/(?:&|(\?))$args=[^&]*(?(1)&|)?/";
            $url = preg_replace( $pattern, '', $url);
        }

        $url = rtrim( $url, '?' );
        return rtrim( $url, '&' );
    }
}
