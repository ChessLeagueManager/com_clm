<?php
class clm_class_db {
	// Verwendete Datenbank Konfiguration
	private $dbConfig;
	// die Verbindung selbst
	private $db;
	// enthält verwendete Tabellen / kann nachträglich erweitert werden (public)
	private $dbTable;
	// enthält db_table für einzelne Tabellenzugriffe
	private $data;
	private $config;
	private $config_order;
	public function __construct($dbConfig, $dbTable, $config) {
		$this->dbConfig = $dbConfig;
		$this->open();
		$this->data = array();
		$this->dbTable = $dbTable;
		$this->config = new clm_class_config($config);
	}
	public function __get($name) {
		// gibt es die Tabelle überhaupt
		if (!isset($this->dbTable[$name])) {
			return false;
		}
		// hat sie bereits eine Verbindung -> sonst stelle sie her
		if (!isset($this->data[$name])) {
			$this->data[$name] = new clm_class_db_table($this->dbTable[$name], $this->db, "clm_" . $name); // alle unsere Tabellen haben den Prefix clm_
			
		}
		// anfrage an db_table weiterleiten
		return $this->data[$name];
	}
	// schreibe alle Änderungen --> weitergabe an geöffnete Tabellen
	public function write() {
		foreach ($this->data as $value) {
			$value->write();
		}
	}
	// Eine Verbindung öffnen
	private function open() {
		mysqli_report(MYSQLI_REPORT_STRICT); 
		$db[0] = new mysqli($this->dbConfig[0], $this->dbConfig[2], $this->dbConfig[3], $this->dbConfig[1]);
		/* check connection */
		if (mysqli_connect_errno()) {
    		printf("CLM db.php: Connect failed: %s\n", mysqli_connect_error());
    		exit();
		}
		$db[0]->set_charset("utf8");
		$db[0]->query("SET SQL_BIG_SELECTS=1");
		$db[1] = $this->dbConfig[4];
		$this->db = $db;
	}
	public function query($query) {
		if (!$result = $this->db[0]->query(clm_core::$load->db_add_prefix($query, $this->db[1]))) {
		    clm_core::addError("sql invalid",$query." (".$this->db[0]->error.")"." Backtrace: ".clm_core::getBacktrace());
		} else {
			return $result;
		}
	}
	public function escape($string) {
		return $this->db[0]->real_escape_string($string);
	}
	public function prepare($query) {
		return $this->db[0]->prepare(clm_core::$load->db_add_prefix($query, $this->db[1]));
	}
	public function insert_id() {
		return $this->db[0]->insert_id;
	}
	public function affected_rows() {
		return $this->db[0]->affected_rows;
	}
	// gibt die Ergebnisse als Objekte in einem Array zurück
	public function loadObjectList($query) {
		// echo $query . "<br/><br/>";
		$result = $this->query($query);
		// printf("Errorcode: %d\n", $this->db[0]->errno);
		$array = array();
		while ($row = $result->fetch_object()) {
			$array[] = $row;
		}
		$result->close();
		return $array;
	}
	// gibt die Ergebnisse als Objekte in einem Array zurück
	public function loadAssocList($query) {
		// echo $query . "<br/><br/>";
		$result = $this->query($query);
		// printf("Errorcode: %d\n", $this->db[0]->errno);
		$array = array();
		while ($row = $result->fetch_array()) {
			$array[] = $row;
		}
		$result->close();
		return $array;
	}
	// gibt die Anzahl der Ergebnisse zurück, es sollte Count im sql Query verwendet werden
	public function count($query) {
		$result = $this->query($query);
		$count = $result->fetch_row();
		$result->close();
		return $count[0];
	}
	// gibt den Datenbank Prefix zurück
	public function prefix() {
		return $this->db[1];
	}
	public function connection() {
		return $this->db[0];
	}
	// Verbindet die Views mit den Daten der Models über ein Objekt
	public function initModel() {
		return new clm_class_db_model();
	}
	public function config() {
		return $this->config;
	}
	public function config_order() {
		if (is_null($this->config_order)) {
			include (clm_core::$path . "/includes/config_order.php");
			$this->config_order = $config_order;
		}
		return $this->config_order;
	}
}
?>
