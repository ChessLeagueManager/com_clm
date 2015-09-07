<?php
class clm_class_config {
	private $config;
	private $cache;
	public function __construct($config) {
		$this->config = $config;
	}
	public function __get($name) {
		// Gibt es diesen Parameter überhaupt?
		if (!isset($this->config[$name])) {
			return NULL;
		}
		$info = clm_core::$db->config->get($this->config[$name][0]);
		if ($info->isNew() && !$info->isChange()) {
			return $this->config[$name][2];
		}
		return $info->value;
	}
	public function __set($id, $value) {
		// Gibt es diesen Parameter überhaupt?
		if (!isset($this->config[$id])) {
			return;
		}
		// die folgende Zeile kontrolliert/korrigiert vor dem Schreiben die Variable
		$value = clm_core::$load->make_valid($value, $this->config[$id][1], $this->config[$id][2], $this->config[$id][3]);
		clm_core::$db->config->get($this->config[$id][0])->value = $value;
	}
	public function getConfig() {
		return $this->config;
	}
}
?>
