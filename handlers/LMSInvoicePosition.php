<?php

/*!
 *  \brief     Klasa reprezentująca pojedynczą pozycję na fakturze.
 *  \version 1.0
 */
class LMSInvoicePosition {
	private $value;
	private $title;

	function __construct($value, $title) {
		$this->value = $value;
		$this->title = $title;
    }

	/*!
	 * Zwraca opis pozycji na fakturze.
	 */
	public function getTitle() {
		return $this->title;
	}

	/*!
	 * Ustawia nowy opis pozycji na fakturze.
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/*!
	 * Zwraca wartość pozycji.
	 */
	public function getValue() {
		return $this->value;
	}

	/*!
	 * Ustawia nową cenę pozycji na fakturze.
	 * @param float $value
	 */
	public function setValue($value) {
		$this->value = $value;
	}
}

?>
