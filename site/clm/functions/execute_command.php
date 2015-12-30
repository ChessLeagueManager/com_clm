<?php
// Kommandos können als JSON String gesendet werden, diese werden hier abgearbeitet.
// Die entsprechenden Rückgabewerte werden dann ebenfalls als JSON String zurückgegeben.
function clm_function_execute_command() {
	if (!is_string($_POST["command"])) {
		return json_encode(array());
	}
	$command = json_decode($_POST["command"]);
	if (!is_array($command) || count($command) == 0) {
		return json_encode(array());
	}
	// Schnittstellenaufruf und Ausgabe als JSON String (mit Fehlercodes)
	if ($command[0] == 0 || $command[0] == 1 || $command[0] == 2) {
		$out = array();
		for ($i = 1;$i < count($command);$i++) {
			if(is_array($command[$i]) && count($command[$i])==1){
				$out[$i-1]=clm_core::$api->call($command[$i][0],array());
			} else if(is_array($command[$i]) && count($command[$i])==2) {
				$out[$i-1]=clm_core::$api->call($command[$i][0],$command[$i][1]);
			} else {
				$out[$i-1]=array(false,"e_wrongCommandFormat");
			}
		}
	} 
	if ($command[0] == 1) {
		if($out[0][0]) {
			$out = $out[0][2];	
		} else {
			$out = "e_noRights";
		}
	}
	if ($command[0] == 2) {
		$lang = clm_core::$lang->notification;
		for ($i = 0;$i < count($out);$i++) {
			if(!isset($out[$i][2])) {
				$out[$i][2] = "";
			}
			$out[$i][3] = $lang->{$out[$i][1]};
		}
	}
	return json_encode($out);
}
?>
