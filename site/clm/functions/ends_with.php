<?php
function clm_function_ends_with($haystack, $needle) {
	return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}
?>
