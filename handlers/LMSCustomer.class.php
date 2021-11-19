<?php

/*!
*  \brief     Klasa reprezentująca klienta.
 *  \version 1.0
 */
class LMSCustomer {
	private $id;
	private $name;
	private $lastname;
	private $city;
	private $address;
	private $invoiceList = array();
	private $paymentList = array();
	private $paymentSummary = 0.0;

	function __construct($id) {
		$this->id = $id;
    }

	public function getID() {
		return $this->id;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function getLastname() {
		return $this->lastname;
	}

	public function setLastname($lastname) {
		$this->lastname = $lastname;
	}

	public function getCity() {
		return $this->city;
	}

	public function setCity($city) {
		$this->city = $city;
	}

	public function getAddress() {
		return $this->address;
	}

	public function setAddress($address) {
		$this->address = $address;
	}

	public function addPayment($value, $date, $comment='') {
		$this->paymentList[] = new LMSPayment($value, $date, $comment);
		$this->paymentSummary += $value;
	}

	public function getPaymentSummary() {
		return $this->paymentSummary;
	}

	public function getPaymentList() {
		foreach ($this->paymentList as $payment)
			$result[] = clone $payment;

		return $result;
	}

	public function getInvoiceSummary() {
		$summary = 0.0;
		foreach ($this->invoiceList as $invoice)
			$summary += $invoice->getSummary();
			
		return $summary;
	}

	public function getInvoiceList() {
		foreach ($this->invoiceList as $invoice)
			$result[] = clone $invoice;

		return $result;
	}

	public function addInvoice($inv_number) {
		if ($this->invoiceExists($inv_number))
			return $this->getInvoice($inv_number);
		else
			return $this->createInvoice($inv_number);
	}

	private function getInvoice($inv_number) {
		if ($this->invoiceExists($inv_number))
			return $this->invoiceList[$inv_number];

		return null;
	}

	public function invoiceExists($inv_number) {
		return array_key_exists($inv_number, $this->invoiceList);
	}

	public function getInvoiceNumberList() {
		return array_keys($this->invoiceList);
	}

	private function createInvoice($inv_number) {
		$this->invoiceList[$inv_number] = new LMSReportInvoice($inv_number);
		return $this->invoiceList[$inv_number];
	}
}

?>
