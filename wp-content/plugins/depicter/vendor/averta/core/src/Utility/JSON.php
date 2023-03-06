<?php
namespace Averta\Core\Utility;


class JSON
{
	/**
     * Detect is JSON
     *
     * @param $args
     *
     * @return bool
     */
    public static function isJson(...$args)
    {
        if(is_array($args[0]) || is_object($args[0])) {
            return false;
        }

        if (trim($args[0]) === '') {
            return false;
        }

        json_decode(...$args);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * Remove extra white-spaces and tabs from json string
     *
     * @param string $json
     *
     * @return string
     */
    public static function normalize( $json )
    {
        if( ! is_string( $json ) ) {
            return $json;
        }

        if (trim( $json ) === '') {
            return '';
        }

        $decoded = json_decode( $json );
        return (json_last_error() == JSON_ERROR_NONE) ? json_encode( $decoded ) : $json;
    }

}
