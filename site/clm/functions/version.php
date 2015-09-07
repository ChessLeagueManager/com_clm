<?php
	function clm_function_version() {
		$out = explode (":", clm);
		$out[2]=$out[1];
		$out[1] = explode (".", $out[0]);
		return $out;
	}
?>
