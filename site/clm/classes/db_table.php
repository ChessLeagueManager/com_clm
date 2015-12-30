<?php
class clm_class_db_table {
	private $db; // array ( db verbindung, db prefix)
	private $data; // enthält bereits gelesene Anfragen
	private $table; // enthält den Tabellennamen
	private $columns; // die verschiedenen Spalten und Default Werte der Tabelle
	private $columnsReady = false; // sind diese bereits gesetzt
	private $id; // array ( unique Tabellen Spalte, string oder int)
	private $delete; // zu löschende Einträge
	private $content = array(); // enthält ein Array mit IDs (als Key) nach der ersten Verwendung
	private $contentReady = false;
	// prepared statements (lesen/erzeugen/ändern/löschen)
	private $stmtReadReady = false;
	private $stmtRead;
	private $stmtDelReady = false;
	private $stmtDel;
	private $stmtCreateReady = false;
	private $stmtCreate;
	private $stmtUpdateReady = false;
	private $stmtUpdate;
	public function __construct($id, $db, $table) {
		$this->id = $id;
		$this->db = $db;
		$this->table = $table;
		$this->delete = array();
		$this->data = array();
	}
	// Eine ID ausgeben lassen
	public function get($id) {
		if (!isset($this->data[$id])) {
			$this->load($id);
		}
		return $this->data[$id];
	}
	// Eine ID löschen (zumindest als gelöscht markieren)
	public function del($id) {
		$this->delete[$id] = true;
		$this->content();
		if (isset($this->content[$id])) {
			unset($this->content[$id]);
		}
		if (isset($this->data[$id])) {
			unset($this->data[$id]);
		}
	}
	// Gibt eine Array mit allen in der Tabelle befindlichen IDs in den Keys zurück
	public function content() {
		if (!$this->contentReady) {
			$sql = "SELECT " . $this->id[0] . " FROM " . $this->db[1] . $this->table;
			$result = $this->db[0]->query($sql);
			while ($row = $result->fetch_object()) {
				$cache = $this->id[0];
				$this->content[$row->$cache] = $row->$cache;
			}
			$result->close();
			$this->contentReady = true;
		}
		return $this->content;
	}
	// schreibe alle Änderungen --> weitergabe an geöffnete Einträge
	public function write() {
		$needCheck = false;
		foreach ($this->data as $value) {
			if ($value->isChange()) {
				if ($value->isNew()) {
					if (!$this->stmtCreateReady) {
						$this->stmtCreate();
					}
					$value->createEntry($this->stmtCreate);
					$needCheck = true;
				} else {
					if (!$this->stmtUpdateReady) {
						$this->stmtUpdate();
					}
					$value->updateEntry($this->stmtUpdate);
				}
			}
		}
		if ($this->id[0] && $this->id[1] == "i" && ($this->deleteMarked() || $needCheck)) {
			$this->checkAutoIncrement();
		}
	}
	// lädt den Eintrag
	private function load($id) {
		if (!$this->stmtReadReady) {
			$this->stmtRead();
		}
		if (isset($this->delete[$id])) {
			$this->data[$id] = $this->createNewEntry($id);
			$this->delete[$id] = false; // aktuell noch vorhanden --> bei Eingang gelöscht
			$this->data[$id]->new = true; // das Objekt existiert noch, kann also überschrieben werden
			
		}
		$this->stmtRead->bind_param($this->id[1], $id);
		$this->stmtRead->execute();
		$out = $this->stmtFetchObject($id);
		if (count($out) > 0) {
			$this->data[$id] = $out[0];
		} else {
			$this->content[$id] = true;
			$this->data[$id] = $this->createNewEntry($id);
		}
	}
	// Umwandeln der Ergebnisse in ein db_entry Objekt
	private function stmtFetchObject($id) {
		$rows = array(); //init
		// bind results to named array
		$meta = $this->stmtRead->result_metadata();
		$fields = $meta->fetch_fields();
		foreach ($fields as $field) {
			$result[$field->name] = "";
			$resultArray[$field->name] = & $result[$field->name];
		}
		call_user_func_array(array($this->stmtRead, 'bind_result'), $resultArray);
		while ($this->stmtRead->fetch()) {
			$resultObject = new clm_class_db_entry($id, $this->table, $this->id);
			foreach ($resultArray as $key => $value) {
				if ($key != $this->id[0]) {
					if (!$this->columnsReady) {
						$this->columns[$key] = $value;
					}
					$resultObject->$key = $value;
				}
			}
			$this->columnsReady = true; // nach einer Abfrage sind die Columns bekannt
			$resultObject->setFinish(true);
			$rows[] = $resultObject;
		}
		return $rows;
	}
	// falls kein Eintrag mit der eingegebenen ID vorhanden ist, wird ein neuer Angelegt
	private function createNewEntry($id) {
		$resultObject = new clm_class_db_entry($id, $this->table, $this->id);
		if (!$this->columnsReady) {
			$this->findColumns();
		}
		foreach ($this->columns as $key => $value) {
			$resultObject->$key = $value;
		}
		$resultObject->setFinish(true);
		$resultObject->setNew(true);
		return $resultObject;
	}
	// löschen aller markierten Einträge
	private function deleteMarked() {
		$needCheck = false;
		foreach ($this->delete as $key => $value) {
			if ($value) {
				$this->deleteId($key);
				$needCheck = true;
			} else {
				if (!$this->data[$key]->change) {
					$this->deleteId($key);
					$needCheck = true;
				}
			}
		}
		return $needCheck;
	}
	// löschen eines Beitrages mit ID
	private function deleteId($id) {
		if (!$this->stmtDelReady) {
			$this->stmtDel();
		}
		$this->stmtDel->bind_param($this->id[1], $id);
		$this->stmtDel->execute();
	}
	// falls noch nicht bekannt, columns direkt bestimmen
	private function findColumns() {
		$query = $this->db[0]->query('SHOW COLUMNS FROM ' . $this->db[1] . $this->table);
		while ($value = $query->fetch_array()) {
			if ($value["Field"] != $this->id[0]) {
				$this->columns[$value["Field"]] = $value["Default"];
			}
		}
		$this->columnsReady = true;
	}
	// stmt --> lesen
	private function stmtRead() {
		$this->stmtRead = $this->db[0]->prepare("SELECT * FROM " . $this->db[1] . $this->table . " WHERE `" . $this->id[0] . "`=?");
		$this->stmtReadReady = true;
	}
	// stmt --> delete
	private function stmtDel() {
		$this->stmtDel = $this->db[0]->prepare("DELETE FROM " . $this->db[1] . $this->table . " WHERE `" . $this->id[0] . "`=?");
		$this->stmtDelReady = true;
	}
	// stmt --> create
	private function stmtCreate() {
		$sql = "INSERT INTO " . $this->db[1] . $this->table . " (`" . $this->id[0] . "`, ";
		$first = true;
		foreach ($this->columns as $key => $value) {
			if($first) {
				$first=false;
			} else {
				$sql.= ", ";	
			}
			$sql.= "`". $key . "`";
		}
		$sql.= ") VALUES (";
		for ($i = 0;$i < count($this->columns);$i++) {
			$sql.= "?, ";
		}
		$sql.= "?)";
		$this->stmtCreate = $this->db[0]->prepare($sql);
		$this->stmtCreateReady = true;
	}
	// stmt --> update
	private function stmtUpdate() {
		$sql = "UPDATE " . $this->db[1] . $this->table . " SET ";
		$first = true;
		foreach ($this->columns as $key => $value) {
			if($first) {
				$first=false;
			} else {
				$sql.= ", ";	
			}
			$sql.= "`". $key . "`=?";
		}
		$sql.= " WHERE `" . $this->id[0] . "`=?";
		$this->stmtUpdate = $this->db[0]->prepare($sql);
		$this->stmtUpdateReady = true;
	}
	// Findet den maximalen Wert der jeweiligen ID und setzt den darauf folgenden als den Auto Increment Wert
	private function checkAutoIncrement() {
		$query = "ALTER TABLE " . $this->db[1] . $this->table . " AUTO_INCREMENT=" . $this->getMax()+1;
		$this->db[0]->query($query);
	}
	public function getMax() {
		if ($this->id[1] == "i") {
			$query = "SELECT MAX(" . $this->id[0] . ") FROM " . $this->db[1] . $this->table;
			$result = $this->db[0]->query($query);
			$max = $result->fetch_row();
			$max = $max[0]; // array dereferencing fix php 5.3
			$result->close();
			return $max;
		} else {
			return false;
		}
	}
	public function reset() {
		$this->data = array();
	}
}
?>
