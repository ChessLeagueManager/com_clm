<?php
// http://www.php.net/manual/en/function.ctype-xdigit.php, thanks to tom at hgmail dot com ¶
function clm_function_is_color($colorCode) {
	if (!ctype_xdigit($colorCode) || (strlen($colorCode) != 6 && strlen($colorCode) != 3)) {
		return false;
	}
	return true;
}
?>
