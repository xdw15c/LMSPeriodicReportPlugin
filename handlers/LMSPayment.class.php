<?php

/*!
 *  \brief     Klasa reprezentująca płatność.
 *  \version 1.0
 */
class LMSPayment {
	private $value;
	private $date;
	private $comment;

	/*!
	 * Tworzy nową wpłate.
	 * @param float $value kwota
	 * @param string $date data przyjęcia płatności
	 * @param string $comment tytuł/opis płatności (opcjonalne)
	 */
	function __construct($value, $date, $comment='') {
		$this->value = $value;
		$this->date = $date;
		$this->comment = $comment;
    }

	/*!
	 * Zwraca kwotę przypisaną do płatności.
	 */
	public function getValue() {
		return $this->value;
	}

	/*!
	 * Zwraca datę płatności.
	 */
	public function getDate() {
		return $this->date;
	}
}

?>
