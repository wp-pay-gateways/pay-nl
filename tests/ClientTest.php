<?php

use Pronamic\WordPress\Pay\Gateways\PayNL\Client;

/**
 * Title: Pay.nl client test
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 */
class Pronamic_WP_Pay_Gateways_PayNL_ClientTest extends WP_UnitTestCase {
	/**
	 * Pre HTTP request
	 *
	 * @see https://github.com/WordPress/WordPress/blob/3.9.1/wp-includes/class-http.php#L150-L164
	 * @return string
	 */
	public function pre_http_request( $preempt, $request, $url ) {
		$response = file_get_contents( dirname( __FILE__ ) . '/mocks/transaction-get-service-json-ideal-service-not-found.http' );

		$processed_response = WP_Http::processResponse( $response );

		$processed_headers = WP_Http::processHeaders( $processed_response['headers'], $url );

		$processed_headers['body'] = $processed_response['body'];

		return $processed_headers;
	}

	public function test_get_issuers() {
		add_filter( 'pre_http_request', array( $this, 'pre_http_request' ), 10, 3 );

		$client = new Client( '', '' );

		$issuers = $client->get_issuers();

		$this->assertFalse( $issuers );
		$this->assertInstanceOf( 'WP_Error', $client->get_error() );
	}
}
