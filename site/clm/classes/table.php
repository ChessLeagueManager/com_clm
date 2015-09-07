<?php
// Diese Klasse generiert das Array für das View table
class clm_class_table {

	// Zugehörige Sprachdatei
	private $lang_name;
	// Zugehörige API für die Datenverarbeitung
	private $api_db;
	// Anzahl an Feldern (gesetzt bei der Datenverarbeitung)
	private $fields;
	// Feld nach dem anfänglich sortiert wird
	private $selected;
	// Sortierweise (true=asc,false=desc)
	private $orderType;
	// Bezeichnung der Buttons
	private $buttons_name = array();
	// Kurzbeschreibung der Buttons
	private $buttons_title = array();
	// value Tags der Buttons (Javascript greift diesen auf)
	private $buttons_value = array();
	// Klassen der Buttons (Ikonen- und Farbzuweisung)
	private $buttons_class = array();
	// Auswahlfelder zum Filtern
	private $filter_fields = array();

	public function __construct($lang_name,$api_db,$fields,$selected,$orderType=true) {
		$this->lang_name=$lang_name;
		$this->api_db=$api_db;
		$this->fields=$fields;
		$this->selected=$selected;
		if($orderType) {
			$this->orderType="asc";
		} else {
			$this->orderType="desc";
		}
	}
	// Füge einen Button hinzu mit gegebenen Werten
	public function add_button($name,$title,$value,$class) {
		$this->buttons_name[] = $name;
		$this->buttons_title[] = $title;
		$this->buttons_value[] = $value;
		$this->buttons_class[] = $class;
	}
	// Füge ein Auswahlfeld mit entsprechenden [$name = name Tag von select, 
	// $entrys = array mit (key = value Tag von option) und (value = Bezeichnung zum Laden aus der Sprachdatei für option), 
	// $selected = Vorauswahl (key)]
	public function add_filter_field($name,$entrys,$selected=null) {
		if($selected!==null) {
			$this->filter_fields[] = array($name,$entrys,$selected);
		} else {
			$this->filter_fields[] = array($name,$entrys);
		}
	}
	// Rückgabe der Arrays in passender Form für das View table
	public function result() {
		return array($this->lang_name,$this->api_db,$this->fields,$this->buttons_name,$this->buttons_title,$this->buttons_value,$this->buttons_class,$this->filter_fields,$this->selected,$this->orderType);
	}
}
?>
