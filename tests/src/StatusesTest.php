<?php

namespace Pronamic\WordPress\Pay\Gateways\PayNL;

use PHPUnit_Framework_TestCase;
use Pronamic\WordPress\Pay\Payments\PaymentStatus as Core_Statuses;

/**
 * Title: Pay.nl states constants tests
 * Description:
 * Copyright: 2005-2021 Pronamic
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 2.0.4
 * @since   1.0.0
 */
class StatusesTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Test transform.
	 *
	 * @dataProvider states_matrix_provider
	 */
	public function test_transform( $state, $expected ) {
		$status = Statuses::transform( $state );

		$this->assertEquals( $expected, $status );
	}

	public function states_matrix_provider() {
		return array(
			array( Statuses::PAID, Core_Statuses::SUCCESS ),
			array( Statuses::CANCELLED, Core_Statuses::CANCELLED ),
			array( 'not existing status', null ),
		);
	}
}
