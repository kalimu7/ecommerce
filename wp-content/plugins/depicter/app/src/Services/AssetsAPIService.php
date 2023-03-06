<?php
namespace Depicter\Services;

use Averta\WordPress\Utility\JSON;
use Depicter\GuzzleHttp\Exception\GuzzleException;

/**
 * A bridge for fetching assets from averta assets API
 *
 * @package Depicter\Services
 */
class AssetsAPIService
{
	/**
	 * Search Media AssetsProvider
	 *
	 * @param string $assetType
	 * @param array  $options
	 *
	 * @return array|mixed
	 * @throws GuzzleException
	 */
	public static function searchAssets( $assetType = 'photos', $options = [] )
	{
		$availableTypes = ['photos', 'videos', 'vectors'];
		if ( !in_array( $assetType, $availableTypes ) ) {
			return [];
		}

		$endpoint = \Depicter::remote()->endpoint(1) . 'v1/search/' . $assetType;
		$response = \Depicter::remote()->get("$endpoint?" . http_build_query($options));

		return JSON::decode( $response->getBody(), true );
	}

	/**
	 * Get Asset Hotlink URL
	 *
	 * @param string $id    Media ID
	 * @param string $size  Media size to return
	 *
	 * @param array $args
	 *
	 * @return mixed
	 */
	public static function getHotlink( $id, $size = 'full', $args = [ 'forcePreview' => 'false' ] )
	{
		$endpointNumber = 1;

		/**
		 * Regex to find possible endpoint number other than default one (1)
		 *
		 * @example if $id = @Fd2X@UnSiqwe8TLRnG8k, $endpointNumber is 2, and $id @UnSiqwe8TLRnG8k
		 */
		preg_match( '/^@Fd(\d{1,})X(.*)/', $id, $matches );
		if( !empty( $matches[1] ) && !empty( $matches[2] ) ){
			$endpointNumber = $matches[1];
			$id = $matches[2]; // set extracted id
		}

		$endpoint = \Depicter::remote()->endpoint( $endpointNumber ) . 'v1/media/' . $id . '/' . $size . '/';

		if ( !empty( $args ) && is_array( $args ) ) {
			$endpoint = add_query_arg( $args, $endpoint );
		}

		return $endpoint;
	}

	/**
	 * Search Elements
	 *
	 * @param array $options
	 *
	 * @return mixed
	 * @throws GuzzleException
	 */
	public static function searchElements( $options = [] ) {

		$endpoint = \Depicter::remote()->endpoint(1) . 'v1/curated/elements';
		$response = \Depicter::remote()->get( "$endpoint?" . http_build_query($options) );

		return JSON::decode( $response->getBody(), true );
	}

	/**
	 * Search Document Templates
	 *
	 * @param array $options
	 *
	 * @return mixed
	 * @throws GuzzleException
	 */
	public static function searchDocumentTemplates( $options = [] ) {

		$endpoint = \Depicter::remote()->endpoint(2) . 'v1/curated/document/templates';
		$response = \Depicter::remote()->get( "$endpoint?" . http_build_query($options) );

		return JSON::decode( $response->getBody(), true );
	}

	/**
	 * Get templates categories
	 *
	 * @param array $options
	 *
	 * @return mixed
	 * @throws GuzzleException
	 */
	public static function getDocumentTemplateCategories( $options = [] ) {

		$endpoint = \Depicter::remote()->endpoint(2) . 'v1/curated/document/templates/categories';
		$response = \Depicter::remote()->get( "$endpoint?" . http_build_query($options) );

		return JSON::decode( $response->getBody(), true );
	}

	/**
	 * Get Template Data
	 *
	 * @param int   $templateID  Template ID
	 * @param array $params      Query params
	 *
	 * @return mixed
	 * @throws GuzzleException
	 */
	public static function getDocumentTemplateData( $templateID, $params = [] ) {
		$endpoint = \Depicter::remote()->endpoint(2) . 'v1/curated/document/templates/' . $templateID;
		$response = \Depicter::remote()->get( $endpoint, ['query' => $params ] );

		return JSON::decode( $response->getBody(), false );
	}

	/**
	 * Preview a Document Template
	 *
	 * @param $templateID
	 *
	 * @return mixed
	 * @throws GuzzleException
	 */
	public static function previewDocumentTemplate( $templateID ) {
		$endpoint = \Depicter::remote()->endpoint(2) . 'v1/curated/document/templates/preview/?id=' . $templateID;
		$response = \Depicter::remote()->get( $endpoint );

		return $response->getBody();
	}

	/**
	 * Search Animations
	 *
	 * @param array $options
	 *
	 * @return mixed
	 * @throws GuzzleException
	 */
	public static function searchAnimations( $options = [] ) {

		$endpoint = \Depicter::remote()->endpoint(1) . 'v1/curated/animations';
		$response = \Depicter::remote()->get( "$endpoint?" . http_build_query($options) );

		return JSON::decode( $response->getBody(), true );
	}

	/**
	 * retrieving animation phases
	 *
	 * @param array $options
	 *
	 * @return mixed
	 * @throws GuzzleException
	 */
	public static function getAnimationsCategories( $options = [] ) {

		$endpoint = \Depicter::remote()->endpoint(1) . 'v1/curated/animations/categories';
		$response = \Depicter::remote()->get( "$endpoint?" . http_build_query($options) );

		return JSON::decode( $response->getBody(), true );
	}
}
