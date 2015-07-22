<?php
function clm_function_is_one_element($value, $array) {
	foreach ($array as $one) {
		if ($one == $value) {
			return true;
		}
	}
	return false;
}
?>