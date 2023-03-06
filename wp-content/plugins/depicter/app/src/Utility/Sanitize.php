<?php
namespace Depicter\Utility;

use \Averta\WordPress\Utility\Sanitize as SanitizeBase;

class Sanitize extends SanitizeBase {

	public static function html( $input, $allowed_tags = null, $namespace = null, $auto_p = false ){
		// A fix to allow empty data url for src in image tag
		if( $namespace === 'depicter/output' ){
			add_filter( 'wp_kses_uri_attributes', [ __CLASS__, 'skipSrcEscapeTemporary' ], 25 );
		}
		$sanitized = parent::html( $input, $allowed_tags, $namespace, $auto_p );
		if( $namespace === 'depicter/output' ){
			remove_filter( 'wp_kses_uri_attributes', [ __CLASS__, 'skipSrcEscapeTemporary' ], 25 );
		}

		return $sanitized;
	}

	/**
     * Retrieves default WordPress HTML tags
     *
     * @return array
     */
	protected static function defaultAllowedTags(){
		$tags = parent::defaultAllowedTags();

		$tags['style'] = [
			'type'  => true
		];
		$tags['script'] = [
			'id'    => true,
			'src'   => true
		];
		$tags['link'] = [
			'rel'   => true,
			'id'    => true,
			'href'  => true,
			'media' => true,
		];
		return $tags;
	}

	/**
	 * Ignore src escaping because `wp_kses` strips `data:` from image placeholder source in PHP 8.0+
	 *
	 * @param array $uriAttributes
	 *
	 * @return array $uriAttributes
	 */
	public static function skipSrcEscapeTemporary( $uriAttributes ) {
	    if ( ( $key = array_search( 'src', $uriAttributes ) ) !== false) {
			unset( $uriAttributes[ $key ] );
		}
		return $uriAttributes;
	}
}
