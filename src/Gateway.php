<?php

/**
 * Title: Pay.nl gateway
 * Description:
 * Copyright: Copyright (c) 2005 - 2014
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0.2
 */
class Pronamic_WP_Pay_Gateways_PayNL_Gateway extends Pronamic_WP_Pay_Gateway {
	/**
	 * Slug of this gateway
	 *
	 * @var string
	 */
	const SLUG = 'pay_nl';

	/////////////////////////////////////////////////

	/**
	 * Constructs and initializes an Pay.nl gateway
	 *
	 * @param Pronamic_WP_Pay_Gateways_PayNL_Config $config
	 */
	public function __construct( Pronamic_WP_Pay_Gateways_PayNL_Config $config ) {
		parent::__construct( $config );

		$this->set_method( Pronamic_WP_Pay_Gateway::METHOD_HTTP_REDIRECT );
		$this->set_has_feedback( true );
		$this->set_amount_minimum( 1.20 );
		$this->set_slug( self::SLUG );

		$this->client = new Pronamic_WP_Pay_Gateways_PayNL_Client( $config->token, $config->service_id );
	}

	/////////////////////////////////////////////////

	/**
	 * Get payment methods
	 *
	 * @return mixed an array or null
	 */
	public function get_payment_methods() {
		return Pronamic_WP_Pay_PaymentMethods::IDEAL;
	}

	/////////////////////////////////////////////////

	/**
	 * Get supported payment methods
	 *
	 * @see Pronamic_WP_Pay_Gateway::get_supported_payment_methods()
	 */
	public function get_supported_payment_methods() {
		return array(
			Pronamic_WP_Pay_PaymentMethods::IDEAL => Pronamic_WP_Pay_PaymentMethods::IDEAL,
		);
	}

	/////////////////////////////////////////////////

	/**
	 * Start
	 *
	 * @param Pronamic_Pay_PaymentDataInterface $data
	 * @see Pronamic_WP_Pay_Gateway::start()
	 */
	public function start( Pronamic_Pay_PaymentDataInterface $data, Pronamic_Pay_Payment $payment, $payment_method = null ) {
		$result = $this->client->transaction_start(
			$data->get_amount(),
			Pronamic_WP_Pay_Gateways_PayNL_Util::get_ip_address(),
			urlencode( $payment->get_return_url() )
		);

		if ( isset( $result, $result->transaction ) ) {
			$transaction_id = $result->transaction->transactionId;
			$payment_url    = $result->transaction->paymentURL;

			$payment->set_transaction_id( $transaction_id );
			$payment->set_action_url( $payment_url );
		} else {
			$this->error = $this->client->get_error();
		}
	}

	/////////////////////////////////////////////////

	/**
	 * Update status of the specified payment
	 *
	 * @param Pronamic_Pay_Payment $payment
	 */
	public function update_status( Pronamic_Pay_Payment $payment ) {
		$result = $this->client->transaction_info( $payment->get_transaction_id() );

		if ( isset( $result, $result->paymentDetails ) ) {
			$state = $result->paymentDetails->state;

			$status = Pronamic_WP_Pay_Gateways_PayNL_States::transform( $state );

			$payment->set_status( $status );
		}
	}
}
