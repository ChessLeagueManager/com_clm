<?php
	// Verbandseingliederung ermitteln
	function clm_function_unit_range($verband) {
		if (strlen($verband) != 3 || !preg_match('/^[0-9A-Z]*$/', $verband)) {
			return array();
		}
		// Deutschlandweit
		if (substr($verband, 0, 3) == '000') {
			return array($verband,"ZZZ");
		// ausgewählter Verband ist Landesverband
		} else if (substr($verband, 1, 2) == '00') {
			return array($verband,substr($verband, 0, 1) . "ZZ");
		// Verband ohne/mit Vereine
		} else if (substr($verband, 2, 1) == '0'){
			return array($verband,substr($verband, 0, 2) . "Z");
		// Verband mit Vereinen
		} else {
			return array($verband,$verband);
		}
	}
?>
