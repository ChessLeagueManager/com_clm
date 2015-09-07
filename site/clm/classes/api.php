<?php
class clm_class_api {
	private $check = true;
	private static $bindings = null;
	private static $logs = null;
	
	public function __call($api, $args) {
		return $this->call($api,$args);
	}
	public function direct($api,$args = array())
	{
			$this->autoInclude($api);
			$cache = $this->check;
			$this->check=false;
			$output = call_user_func_array("clm_api_" . $api, $args);
			$this->check=$cache;
			self::log($api,$args,$output);
			return $output;
	}
	public function call($api,$args = array())
	{
		if ($this->check && !clm_core::$access->api($api,$args)) {
			return array(false,"e_noRights");
		}
		$this->autoInclude($api);
		$cache = $this->check;
		$this->check=false;
		$output = call_user_func_array("clm_api_" . $api, $args);
		$this->check=$cache;
		self::log($api,$args,$output);
		return $output;
	}
	private function autoInclude($api) {
		if (!function_exists("clm_api_" . $api)) {
			$path = clm_core::$path . DS . "api" . DS . $api . '.php';
			if(file_exists($path)) {
				require_once ($path);
			} else {
				clm_core::addError("api missing",$path." (".$api.")"." Backtrace: ".clm_core::getBacktrace());		
			}
		}
	}
	public function callStandalone($api) {
		self::loadBinding();
		if(!isset(self::$bindings[$api])) {
			return array(false,"e_noStandalone");
		}
		$args = array();
		// Filterung übernimmt die API Funktion
		foreach(self::$bindings[$api] as $key) {
			if(isset($_POST[$key])) {
				$args[]=$_POST[$key];
			} else if(isset($_GET[$key])) {
				$args[]=$_GET[$key];
			} else {
				$args[]=null;
			}
		}
		if ($this->check && !clm_core::$access->api($api,$args)) {
			return array(false,"e_noRights");
		}
		// Berechtigung wurde bereits überprüft
		return clm_core::$api->direct($api, $args);
	}
	private static function loadBinding() {
		if (is_null(self::$bindings)) {
			$path = clm_core::$path . DS . "includes" . DS . 'bindings.php';
			require($path);
			self::$bindings = $bindings;
		}
	}
	// Loggt falls notwendig den jeweiligen Aufruf
	private static function log($api, $args, $output) {
		// Verhindert das Einbinden und Durchsuchen der Log Datei falls nicht notwendig (optional)
		if(isset(clm_core::$log)) {
			return;
		}
		// Einbinden der Liste der zu loggenden APIs
		if (is_null(self::$logs)) {
			$path = clm_core::$path . DS . "includes" . DS . 'logs.php';
			require($path);
			self::$logs = $logs;
		}
		// Loggen?
		if(in_array($api,self::$logs)) {
			
			clm_core::addInfo($api,json_encode(array("input"=>$args,"output"=>$output)));
		}
		return;
	}
	public static function getBinding() {
		self::loadBinding();
		return self::$bindings;
	}
	public function check($check) {
		$this->check=$check;
	}

}
