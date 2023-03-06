<?php
namespace Depicter\Services;

class DocumentFontsV1Service
{
	/**
	 * @var array[]
	 */
	private $fonts = [];

	/**
	 * Get font families
	 *
	 * @param int      $documentId  The document ID to get belonging faces
	 * @param string   $type        Repository type of the font. `google`, `typescript`, ..
	 *
	 * @return int[]|string[]
	 */
	public function getFamilies( $documentId, $type = 'google' ) {
		return array_keys( $this->getFontsInfo( $documentId, $type ) );
	}

	/**
	 * Get collected fonts list
	 *
	 * @param int      $documentId  The document ID to get belonging faces
	 * @param string   $type        Repository type of the font. `google`, `typescript`, ..
	 *
	 * @return int[]|string[]
	 */
	public function getFontsInfo( $documentId, $type = 'google' ) {
		return ! empty( $this->fonts[ $documentId ][ $type ] ) ? $this->fonts[ $documentId ][ $type ] : [];
	}

	/**
	 * Get font faces
	 *
	 * @param int      $documentId  The document ID to get belonging faces
	 * @param string  $type        Repository type of the font. `google`, `typescript`, ..
	 *
	 * @return array
	 */
	public function getFontsLinkQuery( $documentId, $type = 'google' ) {
		if( empty( $this->fonts[ $documentId ][ $type ] ) ){
			return [];
		}

		$fontFaces = [];

		foreach ( $this->fonts[ $documentId ][ $type ] as $fontFamily => $fontInfo ) {
			
			if ( $fontFamily == 'inherit' ) {
				continue;
			}
			
			$weights = [];
			foreach ( $fontInfo['weights'] as $key => $weight ) {
				$weight = 'regular' == strtolower($weight) ? '400' : $weight;
				$weights[ $key ] = $weight;
			}

			// Sort weight number ascending
			ksort( $weights );

			// Make weight query string for current font
			$weightQuery = '';
			foreach ( $weights as $weightKey => $weight ){
				$weightQuery .= $weight . ',';
			}
			$weightQuery = rtrim($weightQuery, ',');

			// Collect font name and weights in a query string
			$fontFaces[] = $fontInfo['face'] . ':' . $weightQuery;
		}

		return $fontFaces;
	}

	/**
	 * Add a font to fonts list
	 *
	 * @param int          $documentId  The document ID that the font belongs to
	 * @param string       $fontName    Font family name
	 * @param string|array $fontWeight  Font weight
	 * @param string       $type        Repository type of the font. `google`, `typescript`, ..
	 */
	public function addFont( $documentId, $fontName, $fontWeight, $type = 'google' ) {
		if ( empty( $fontName ) ) {
			return;
		}

		$fontName = trim( $fontName );
		$fontWeight = (array) $fontWeight;

		$this->initFontForDocument( $documentId );

		if ( isset( $this->fonts[ $documentId ][ $type ][ $fontName ] ) ) {
			foreach ( $fontWeight as $key => $weight ) {
				if ( !in_array( $weight, $this->fonts[ $documentId ][ $type ][ $fontName ]['weights'] ) ) {
					$this->fonts[ $documentId ][ $type ][ $fontName ]['weights'][] = $weight;
				}
			}
		} else {
			$this->fonts[ $documentId ][ $type ][ $fontName ] = [
				'face'      => str_replace( ' ', '+', $fontName ),
				'weights'   => $fontWeight
			];
		}
	}

	protected function initFontForDocument( $documentId ){
		if( ! isset( $this->fonts[ $documentId ] ) ){
			$this->fonts[ $documentId ] = [
				'google' => []
			];
		}
	}

	/**
	 * Add list of fonts to fonts list
	 *
	 * @param int    $documentId  The document ID that fonts belong to
	 * @param array  $fontList
	 * @param string $type
	 */
	public function addFonts( $documentId, $fontList, $type = 'google' ) {
		if ( empty( $fontList ) ) {
			return;
		}

		foreach ( $fontList as $fontName => $fontWeight ){
			$this->addFont( $documentId, $fontName, $fontWeight, $type );
		}
	}

	/**
	 * Get fonts load link
	 *
	 * @param int    $documentId The document ID to get belonging faces
	 * @param string $type Repository type of the font. `google`, `typescript`, ..
	 *
	 * @return bool|string
	 */
	public function getFontsLink( $documentId, $type = 'google' ) {
		if ( $fontsQuery = $this->getFontsLinkQuery( $documentId, $type = 'google' ) ) {
			$fontsQuery = implode( "|", $fontsQuery );
			return 'https://fonts.googleapis.com/css?family=' . $fontsQuery . '&display=swap';
		}

		return '';
	}
}
