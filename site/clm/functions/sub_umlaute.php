<?php
// Ersetzen von Umlauten
// z.B. für internationale Schreibweise in pgn-Vorlagen
function clm_function_sub_umlaute($string = '') {
	$search = array("Ä", "Ö", "Ü", "ä", "ö", "ü", "ß", "é");
	$replace = array("Ae", "Oe", "Ue", "ae", "oe", "ue", "ss", "e");
	return str_replace($search, $replace, $string);		
}
?>
