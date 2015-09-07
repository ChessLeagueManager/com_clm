<?php
function clm_function_starts_with($haystack, $needle) {
	return $needle === "" || strpos($haystack, $needle) === 0;
}
?>
