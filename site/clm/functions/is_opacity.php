<?php
function is_opacity($opacity) {
	if (!is_numeric($opacity) || $opacity < 0 || $opacity > 1) {
		return false;
	}
	return true;
}
?>