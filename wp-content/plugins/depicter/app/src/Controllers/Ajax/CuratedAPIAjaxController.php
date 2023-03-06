<?php
namespace Depicter\Controllers\Ajax;

use Averta\WordPress\Utility\JSON;
use Depicter\GuzzleHttp\Exception\GuzzleException;
use Depicter\Services\AssetsAPIService;
use Depicter\Utility\Http;
use Depicter\Utility\Sanitize;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;


class CuratedAPIAjaxController
{

	/**
	 * Search Elements
	 *
	 * @param RequestInterface $request
	 * @param string           $view
	 *
	 * @return ResponseInterface
	 * @throws GuzzleException
	 */
	public function searchElements( RequestInterface $request, $view )
	{
		$page     = !empty( $request->query('page'    ) ) ? Sanitize::int( $request->query('page') ) : 1;
		$perpage  = !empty( $request->query('perpage' ) ) ? Sanitize::int( $request->query('perpage') ) : 20;
		$category = !empty( $request->query('category') ) ? Sanitize::textfield( $request->query('category') ) : '';
		$search   = !empty( $request->query('s'  ) ) ? Sanitize::textfield( $request->query('s') ) : '';

		$options = [
			'page'      => $page,
			'perpage'   => $perpage,
			'category'  => $category,
			's'         => $search
		];

		try {
			return \Depicter::json( AssetsAPIService::searchElements( $options ) );
		} catch ( \Exception  $exception ) {

			$error = Http::getErrorExceptionResponse( $exception );

			return \Depicter::json([
				'errors' => $error
			])->withStatus( $error['statusCode'] );
		}

	}

	/**
	 * search Document Templates
	 *
	 * @param RequestInterface $request
	 * @param string           $view
	 *
	 * @return ResponseInterface
	 * @throws GuzzleException
	 */
	public function searchDocumentTemplates( RequestInterface $request, $view )
	{
		$page     = !empty( $request->query('page') ) ? Sanitize::int( $request->query('page') ) : 1;
		$perpage  = !empty( $request->query('perpage') ) ? Sanitize::int( $request->query('perpage') ) : 20;
		$category = !empty( $request->query('category') ) ? Sanitize::textfield( $request->query('category') ) : '';
		$search   = !empty( $request->query('s') ) ? Sanitize::textfield( $request->query('s') ) : '';
		$version  = !empty( $request->query('v') ) ? Sanitize::textfield( $request->query('v') ) : '1';
		$from     = !empty( $request->query('from') ) ? Sanitize::textfield( $request->query('from') ) : 'website';

		$options = [
			'page'      => $page,
			'perpage'   => $perpage,
			'category'  => $category,
			's'         => $search,
			'v'         => $version,
			'from'      => $from
		];

		try {
			return \Depicter::json( AssetsAPIService::searchDocumentTemplates( $options ) );

		} catch ( \Exception  $exception ) {
			$error = Http::getErrorExceptionResponse( $exception );

			return \Depicter::json([
				'errors' => $error
			])->withStatus( $error['statusCode'] );
		}

	}

	public function getDocumentTemplateCategories( RequestInterface $request, $view )
	{
		$category = !empty( $request->query('category') ) ? Sanitize::textfield( $request->query('category') ) : '';

		$options = [ 'category' => $category ];

		try {
			return \Depicter::json( AssetsAPIService::getDocumentTemplateCategories( $options ) );
		} catch ( \Exception  $exception ) {
			$error = Http::getErrorExceptionResponse( $exception );

			return \Depicter::json([
				'errors' => $error
			])->withStatus( $error['statusCode'] );
		}
	}

	/**
	 * Imports a template
	 *
	 * @param  RequestInterface  $request
	 * @param $view
	 *
	 * @return ResponseInterface
	 * @throws GuzzleException
	 */
	public function importDocumentTemplate( RequestInterface  $request, $view ) {
		$templateID = !empty( $request->query('ID') ) ? Sanitize::textfield( $request->query('ID') ) : '';
		$endpointVersion = !empty( $request->query('v') ) ? Sanitize::int( $request->query('v') ) : 1;

		try {
			$result = AssetsAPIService::getDocumentTemplateData( $templateID, [ 'v' => $endpointVersion ] );

			if ( !empty( $result->errors ) ) {
				return Http::getErrorJson( $result->errors );

			} elseif ( !empty( $result->hits ) ) {
				$editorData = JSON::encode( $result->hits );
				$editorData = preg_replace( '/"activeBreakpoint":".+?"/', '"activeBreakpoint":"default"', $editorData );
				$document = \Depicter::documentRepository()->create();

				$updateData = ['content' => $editorData ];
				if ( !empty( $result->title ) ) {
					$updateData['name'] = $result->title . ' ' . $document->getID();
				}
				if ( !empty( $result->image ) ) {
					$previewImage = file_get_contents( $result->image );
					\Depicter::storage()->filesystem()->write( \Depicter::documentRepository()->getPreviewImagePath( $document->getID() ) , $previewImage );
				}

				\Depicter::documentRepository()->update( $document->getID(), $updateData );
				\Depicter::media()->importDocumentAssets( $editorData );

				return \Depicter::json([
					'hits' => [
						'documentID' => $document->getID()
					]
				]);

			} else {
				// Return the error message received from server
				return \Depicter::json( $result );
			}

		} catch ( \Exception  $exception ) {

			$error = Http::getErrorExceptionResponse( $exception );

			return \Depicter::json([
				'errors' => $error
			])->withStatus( $error['statusCode'] );
		}
	}

	/**
	 * @param RequestInterface  $request
	 * @param $view
	 *
	 * @return ResponseInterface
	 * @throws GuzzleException
	 */
	public function previewDocumentTemplate( RequestInterface  $request, $view ) {
		$templateID = !empty( $request->query('ID') ) ? Sanitize::textfield( $request->query('ID') ) : '';
		try {
			$result = AssetsAPIService::previewDocumentTemplate( $templateID );
			return \Depicter::output( $result );
		} catch ( \Exception  $exception ) {
			$error = Http::getErrorExceptionResponse( $exception );

			return \Depicter::json([
				'errors' => $error
			])->withStatus( $error['statusCode'] );
		}
	}

	/**
	 * Search in Animations
	 *
	 * @param RequestInterface $request
	 * @param string           $view
	 *
	 * @return ResponseInterface
	 * @throws GuzzleException
	 */
	public function searchAnimations( RequestInterface $request, $view ){

		$page = !empty( $request->query('page') ) ? Sanitize::int( $request->query('page') ) : 1;
		$perpage = !empty( $request->query('perpage') ) ? Sanitize::int( $request->query('perpage') ) : 20;
		$phase = !empty( $request->query('phase') ) ? Sanitize::textfield( $request->query('phase') ) : '';
		$search   = !empty( $request->query('s'  ) ) ? Sanitize::textfield( $request->query('s') ) : '';
		$category   = !empty( $request->query('category'  ) ) ? Sanitize::textfield( $request->query('category') ) : '';

		$options = [
			'page'     => $page,
			'perpage'  => $perpage,
			'phase'    => $phase,
			's'        => $search,
			'category' => $category
		];


		try {
			return \Depicter::json( AssetsAPIService::searchAnimations( $options ) );

		} catch ( \Exception  $exception ) {
			$error = Http::getErrorExceptionResponse( $exception );

			return \Depicter::json([
				'errors' => $error
			])->withStatus( $error['statusCode'] );
		}

	}

	/**
	 * Get list of animation categories
	 *
	 * @param RequestInterface $request
	 * @param                  $view
	 *
	 * @return ResponseInterface
	 * @throws GuzzleException
	 */
	public function getAnimationsCategories( RequestInterface $request, $view )
	{

		$phase = !empty( $request->query('phase') ) ? Sanitize::textfield( $request->query('phase') ) : '';
		$options = [ 'phase' => $phase ];

		if( !empty( $request->query('elementType') ) ){
			$options['elementType'] = Sanitize::textfield( $request->query('elementType') );
		}

		try {
			$result = AssetsAPIService::getAnimationsCategories( $options );

			if ( !empty( $result['errors'] ) ) {
				return Http::getErrorJson( $result['errors'] );
			} else {
				return \Depicter::json( $result );
			}

		} catch ( \Exception  $exception ) {
			$error = Http::getErrorExceptionResponse( $exception );

			return \Depicter::json([
				'errors' => $error
			])->withStatus( $error['statusCode'] );
		}
	}

}
