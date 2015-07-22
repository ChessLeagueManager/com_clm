<?php
function clm_api_db_logging_new($ids) {
	$lines = array();
	$lines[] = time();
	$lines[] = "clm: ".clm;
	$lines[] = "php: ".phpversion();
	$lines[] = "mysql: ".clm_core::$db->connection()->server_info;
	$lines[] = "fopen: ".(ini_get('allow_url_fopen') ? "yes" : "no");
	$lines[] = "soap: ".(class_exists("SoapClient") ? "yes" : "no");
	$lines[] = "max_execution_time: ".ini_get('max_execution_time');

	if($ids==null) {
		$ids = clm_core::$db->logging->content();
	}
	if(is_array($ids)) {
		foreach($ids as $id) {
			$element = clm_core::$db->logging->get($id);
			if(is_numeric($id) && !$element->isNew()) {
				$lines[] =				
				$id .", ".
				$element->callid .", ".
				$element->userid .", ".
				$element->timestamp .", ".
				$element->type .", ".
				$element->name .", ".
				$element->content;
			}
		}
	}
	return array(true, "", $lines);
}
?>
