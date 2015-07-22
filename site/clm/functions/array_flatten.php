<?php
// Quelle: http://stackoverflow.com/questions/6785355/convert-multidimensional-array-into-single-array
// Dank an: AlienWebguy
function clm_function_array_flatten($array, $keys = null) {
	// Konvertiert ein Object (stdClass) in eine Array falls nötig
	$array = clm_core::$load->object_to_array($array);
	if (!is_array($array)) {
		return FALSE;
	}
	$result = array();
	foreach ($array as $key => $value) {
		if (is_array($value)) {
			$result = array_merge($result, clm_function_array_flatten($value, $keys));
		} else {
			if (!is_null($keys) && !in_array($key, $keys)) {
				continue;
			}
			$result[] = $value;
		}
	}
	return $result;
}
?>
