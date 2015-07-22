<?php
function clm_function_control_api($function) {
	switch ($function) {
		case "season_save":
			return clm_core::$access->access("BE_general_general");
		case "season_enable":
			return clm_core::$access->access("BE_general_general");
		case "season_delete":
			return clm_core::$access->access("BE_general_general");
		case "dates_display":
			return true;
	return false;
	}
}
?>
