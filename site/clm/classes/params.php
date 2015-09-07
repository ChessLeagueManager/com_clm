<?php 
class clm_class_params {
	private $params;
	
	public function __construct($params = null) {
		$this->params = array();
		if (!empty($params) && is_string($params)) {
				$params = explode("\n", $params);
				foreach ($params as $value) {
					$value = explode("=",$value,2);
					if(count($value)==2) {
						if($value[1]!="") {
							$this->params[$value[0]]=$value[1];
						}
					}
				}
		}
	}
	public function get($key,$value=null) {
		return (isset($this->params[$key]) ? $this->params[$key] : $value);
	}
	public function set($key,$value) {
		$this->params[$key]=$value;
	}
	public function params() {
		$params = "";
		foreach ($this->params as $key => $value) {
			if($value!="") {
				$params .=$key."=".$value."\n";
			}
		}
		return $params;
	}
}
?>
