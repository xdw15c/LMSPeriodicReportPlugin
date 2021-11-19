<?php

/*!
 *  \brief     Klasa zbiorcza umożliwiająca pracę z obiektami klasy Customer.
 *  \version 1.0
 */
class LMSCustomerCollection {
	private $customerList = array();

	public function addCustomer($ID) {
		$this->customerList[$ID] = new LMSCustomer($ID);
		return $this->customerList[$ID];
	}

	public function customerExists($id) {
		return array_key_exists($id, $this->customerList);
	}

	public function getCustomerQuantity() {
		return count($this->customerList);
	}

	public function getCustomerIdList() {
		return array_keys($this->customerList);
	}

	public function getCustomer($ID) {
		if ($this->customerExists($ID))
			return $this->customer($ID);

		return null;
	}

	private function customer($id) {
		return $this->customerList[$id];
	}

	public function getCustomerList() {
		$result = array();

		foreach ($this->customerList as $singleCustomer)
			$result[$singleCustomer->getID()] = clone $singleCustomer;

		return $result;
	}
}

?>
