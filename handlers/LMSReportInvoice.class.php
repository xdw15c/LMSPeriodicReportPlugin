<?php

/*!
 *  \brief     Klasa reprezentująca fakturę.
 *  \version 1.0
 */
class LMSReportInvoice {
	private $inv_number;
	private $positionList = array();
	private $value = 0.0;

	function __construct($inv_number) {
		$this->inv_number = $inv_number;
    }

	/*!
	 * Zwraca numer aktualnie wybranej faktury.
	 */
	public function getNumber() {
		return $this->inv_number;
	}

	/*!
	 * Dodaje pozycję na fakturze
	 * @param float $value cena
	 * @param string $title opis dodawanego przedmiotu/usługi
	 */
	public function addPosition($value, $title) {
		$this->positionList[] = new LMSInvoicePosition($value, $title);
		$this->value += $value;
	}

	/*!
	 * Zwraca łączną kwotę na jaką została wystawiona faktura.
	 */
	public function getSummary() {
		return $this->value;
	}
}

?>
