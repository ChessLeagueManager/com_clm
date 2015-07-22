<?php
function clm_function_is_one_element_sql($value, $array) {
	if (clm_core::$db->count($array[0] . clm_core::$db->connection()->real_escape_string($value) . $array[1]) > 0) {
		return true;
	}
	return false;
}
?>
