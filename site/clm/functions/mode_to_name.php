<?php
function clm_function_mode_to_name($mode,$group=true) {
	
	
		if(!$group && (!is_int($mode) || $mode < 1 || $mode > 6)) {
			return "";		
		}
		
		if($group && (!is_int($mode) || $mode < 1 || $mode > 5)) {
			return "";		
		}
		
		if($group) {
			$lang = "tournament_group";		
		} else {
			$lang = "tournament";		
		}
		
		$lang = clm_core::$lang->$lang;
		$mode = "mode".$mode;
		return $lang->$mode;
}
?>