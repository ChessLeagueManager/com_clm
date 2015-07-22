<?php
// jede ID die verwendet wird sollte unbedingt einmalig sein,
// diese Klasse ermöglicht dies für verschiedene Anhängsel.
class clm_class_id {
	private $data;
	public function __construct() {
		$this->data = array();
	}
	public function __get($string) {
		if (!isset($data[$string])) {
			$data[$string] = 0;
		} else {
			$data[$string]++;
		}
		return "clm_id_".$string."_".$data[$string];
	}
}
?>
