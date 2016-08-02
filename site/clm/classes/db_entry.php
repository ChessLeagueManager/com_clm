<?php
class clm_class_db_entry {
	private $finish = false; // ist er noch dabei das Objekt zu befüllen
	private $new = false; // ist es ein neuer Eintrag
	private $change = false; // wurde am Eintrag etwas geändern, was gespeichert werden müsste
	private $table; // Name der Tabelle
	private $tableId; // array ( unique Tabellen Spalte, string oder int)
	private $id; // id des Elements
	private $data; // enthält ausgelsene Daten
	public function __construct($id, $table, $tableId) {
		$this->id = $id;
		$this->table = $table;
		$this->tableId = $tableId;
	}
	// gibt mir Daten aus
	public function __get($id) {
		if (isset($this->data[$id])) {
			return $this->data[$id];
		} else {
			return false;
		}
	}
	public function __set($id, $value) {
		$value=strval($value); // very important --> when $value=0 (int) and $this->data[$id]="something" (string) so is $value!=$this->data[$id] false -> intvar from "something" is 0
		if (!$this->finish) {
			$this->data[$id] = $value;
		} else {
			if (isset($this->data[$id]) && ($this->data[$id] != $value || $this->new)) {
				$this->data[$id] = $value;
				$this->change = true;
				return true;
			} else {
				return false;
			}
		}
	}
	// aktualisiere einen eintrag
	public function updateEntry($stmt) {
		$type = '';
		for ($i = 0;$i < count($this->data);$i++) {
			$type.= "s";
		}
		$type.= $this->tableId[1];
		call_user_func_array('mysqli_stmt_bind_param', array_merge(array($stmt, $type), $this->refValues($this->data), array(&$this->id)));
		$stmt->execute();
		$this->change = false;
	}
	// erzeuge einen neuen Eintrag
	public function createEntry($stmt) {
		$type = $this->tableId[1];
		for ($i = 0;$i < count($this->data);$i++) {
			$type.= "s";
		}
		call_user_func_array('mysqli_stmt_bind_param', array_merge(array($stmt, $type), array(&$this->id), $this->refValues($this->data)));
		$stmt->execute();
		$this->change = false;
		$this->new = false;
	}
	// Hilfsfunktion zum verwenden von prepared statements
	private function refValues(& $arr) {
		$refs = array();
		foreach ($arr as $key => $value) $refs[$key] = & $arr[$key];
		return $refs;
	}
	// Hilfsfunktionen um den Status festzulegen
	public function isFinish() {
		return $this->finish;
	}
	public function setFinish($value) {
		$this->finish = $value;;
	}
	public function isChange() {
		return $this->change;
	}
	public function setChange($value) {
		$this->change = $value;;
	}
	public function isNew() {
		return $this->new;
	}
	public function setNew($value) {
		$this->new = $value;;
	}
}
?>
