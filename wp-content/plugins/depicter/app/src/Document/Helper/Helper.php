<?php
namespace Depicter\Document\Helper;


use Averta\WordPress\Utility\JSON;
use Depicter\Document\CSS\Selector;

class Helper
{
	/**
	 * Generate data-actions value for DOM
	 *
	 * @param array $actions
	 *
	 * @return string
	 */
	public static function getActions( $actions ) {
		if ( !is_array( $actions ) || empty( $actions ) ) {
			return '';
		}

		$output = [];
		foreach ( $actions as $key =>  $action ) {
			$action = (array) $action;

			$action_params = [];
			$action_params[] = $action['type'];
			$action_params[] = $action['trigger'];
			$action_params[] = (int) ! empty( $action['delay'] ) ? $action['delay'] : 0;

			if( !empty( $action['options'] ) ){
				 $action_params[] = $action['options'];
			}

			$output[] = $action_params;
		}

		return JSON::encode( $output );
	}


	/**
	 * Extract section ID from slug
	 *
	 * @param string $sectionSlug
	 *
	 * @return string
	 */
	public static function getSectionIdFromSlug( $sectionSlug = '' ){
		return ltrim( $sectionSlug, Selector::SECTION_PREFIX . '-' );
	}

	/**
	 * Extract element ID from slug
	 *
	 * @param string $elementSlug
	 *
	 * @return string
	 */
	public static function getElementIdFromSlug( $elementSlug = '' ){
		return ltrim( $elementSlug, Selector::ELEMENT_PREFIX . '-' );
	}

	public static function getSectionCssId( $documentId, $sectionId ){
		return Selector::getFullSelectorPath( $documentId, $sectionId );
	}

	public static function sectionsSlugsListToCssIDsList( $sectionsSlugList, $documentId ){

		if( empty( $sectionsSlugList ) || ! is_array( $sectionsSlugList ) ){
			return [];
		}

		$sectionsCssIDsList = [];

		foreach( $sectionsSlugList as $sectionSlug ){
			$sectionIdNumber = self::getSectionIdFromSlug( $sectionSlug );
			$sectionsCssIDsList[] = self::getSectionCssId( $documentId, $sectionIdNumber );
		}

		return $sectionsCssIDsList;
	}

	/**
	 * extract third party IDs from document content
	 * @param $content
	 *
	 * @return array|void
	 */
	public static function extractAssetIds( $content) {

		if ( empty( $content ) ) {
			return;
		}

		$assetIDs = [];

		$patterns = [
			'"source":"([^"]*)"',
			'"src":"([^"]*)"'
		];

		foreach ( $patterns as $pattern ) {
			preg_match_all( '/' . $pattern . '/', $content, $sources , PREG_SET_ORDER );
			if ( !empty( $sources ) ) {
				foreach ( $sources as $source ) {
					// check if assetID if for third party libraries or not
					if ( !empty( $source[1] ) && strpos( $source[1], '@') === 0 ) {
						$assetIDs[] = $source[1];
					}
				}
			}
		}

		return $assetIDs;
	}


	/**
	 * Get list of class which has specific elementId
	 *
	 * @param       $hideOnSections
	 * @param int   $documentId
	 * @param array $allSections
	 *
	 * @return string
	 */
	public static function getVisibleSectionsCssIdList( $hideOnSections, $documentId, $allSections = [] ) {

		if ( ! empty( $hideOnSections ) ) {
			foreach ( $allSections as $key => $section ) {
				if ( in_array( $section, $hideOnSections ) ) {
					unset( $allSections[ $key ] );
				}
			}
		}

		return implode( ',', Helper::sectionsSlugsListToCssIDsList( $allSections, $documentId ) );
	}

	/**
	 * Get list of class which has specific elementId
	 *
	 * @param       $hideOnSections
	 * @param       $documentId
	 *
	 * @return string
	 */
	public static function getInvisibleSectionsCssIdList( $hideOnSections, $documentId ) {
		if( !empty( $hideOnSections ) ){
			return implode( ',', Helper::sectionsSlugsListToCssIDsList( $hideOnSections, $documentId ) );
		}
		return '';
	}
}
