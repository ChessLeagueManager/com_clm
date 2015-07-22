<?php
class clm_class_db_model {
	// Enthält die gewählten Models
	private $data;
	public function __construct() {
		$this->list=array();
	}
	public function add($array)
	{
	      $this->data[] = $array;
	}
	// lädt die gewählten Views
	public function get()
	{
		$out=array();
		for($i=0;$i<count($this->data);$i++)
		{
		$out[] = clm_core::$load->load_model($this->data[$i][0],$this->data[$i][1]);
		}
		return $out;
	}
}
?>
