<?php
namespace Depicter\Services;

use Averta\WordPress\Utility\JSON;
use Depicter\GuzzleHttp\Exception\GuzzleException;

class ClientService
{
	/**
	 * ClientService constructor.
	 */
	public function __construct(){
		if ( empty( \Depicter::options()->get( 'version_initial' ) ) ) {
			\Depicter::options()->set( 'version_initial', DEPICTER_VERSION );
		}
	}

	/**
	 * Register client info
	 *
	 * @return void
	 */
	public function authorize() {

		if (
			\Depicter::cache('base')->get( 'is_client_registered' ) &&
			\Depicter::auth()->getClientKey()
		) {
			return;
		}

		$params = [
			'form_params' => [
				'version'           => DEPICTER_VERSION,
				'version_initial'   => \Depicter::options()->get('version_initial'),
				'info'              => \Depicter::options()->get('info')
			]
		];

		try{
			$response = \Depicter::remote()->post( 'v1/client/register', $params );

			$payload = JSON::decode( $response->getBody(), true );

			if ( ! empty( $payload['client_key'] ) ) {
				\Depicter::options()->set( 'client_key', $payload['client_key'] );
			} elseif ( ! empty( $payload['errors'] ) ) {
				\Depicter::options()->set('register_error_message', $payload['errors'] );
			}

			\Depicter::cache('base')->set( 'is_client_registered', true, DAY_IN_SECONDS );

		} catch ( GuzzleException|\Exception $e ) {
			\Depicter::options()->set('register_error_message', $e->getMessage() );
		}
	}

	/**
	 * Send user feedback
	 *
	 * @param  array  $bodyParams
	 *
	 * @return bool
	 * @throws GuzzleException
	 */
	public function reportIssue( $bodyParams = [] ) {
		$response = \Depicter::remote()->post( 'v1/report/issue', [
			'form_params' => $bodyParams
		]);
		return $response->getStatusCode() == 200;
	}

	/**
	 * Send user error reports
	 *
	 * @param  array  $bodyParams
	 *
	 * @return bool
	 * @throws GuzzleException
	 */
	public function reportError( $bodyParams = [] ) {
		$response = \Depicter::remote()->post( 'v1/report/error', [
			'form_params' => $bodyParams
		]);
		return $response->getStatusCode() == 200;
	}

	/**
	 * send subscriber
	 * @param  array  $bodyParams
	 *
	 * @return bool
	 * @throws GuzzleException
	 */
	public function subscribe( $bodyParams = [] ) {
		$response = \Depicter::remote()->post( 'v1/subscriber/store', [
			'form_params' => $bodyParams
		]);
		return $response->getStatusCode() == 200;
	}


	/**
	 * Validate user activation status
	 */
	public function validateActivation() {
		try {
			$response = \Depicter::remote()->post( 'v1/client/validate/activation' );
			$info = JSON::decode( $response->getBody(), true);

			if( is_null( $info['success'] ) ){
				\Depicter::options()->set('activation_error_message', '' );

			} elseif ( ! empty( $info['data'] ) ) {
				\Depicter::options()->set('subscription_status', $info['data']['status'] );
				\Depicter::options()->set('subscription_expires_at', $info['data']['expires_at'] );
				\Depicter::options()->set('user_tier', $info['data']['user_tier'] );
				\Depicter::options()->set('activation_error_message', '' );

				if ( $info['data']['status'] == 'active' ) {
					return true;
				}
			} elseif( $info['success'] == 1 ){
				return true;
			}

			if( ! empty( $info['log'] ) ) {
				\Depicter::options()->set('activation_log_message', $info['log'] );
			}

		} catch ( GuzzleException $exception ) {
			\Depicter::options()->set('activation_error_message'  , $exception->getMessage() );
			\Depicter::options()->set('connection_error_message'  , $exception->getMessage() );
		}

		return false;
	}

}
