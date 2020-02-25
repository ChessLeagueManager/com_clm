<?php
/*
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// Einlesen von Post-Variablen vom Typ Integer
// Achtung - Key wird nicht übernommem
function clm_function_request_array_int($input, $standard = NULL, $wkey = false) {
	if (!isset($_POST[$input])) return $standard;
	$value = $_POST[$input];
	$result = array(); 
	foreach($value as $key => $value1) 
	{
		if ($wkey) $result[$key] = clm_core::$load->make_valid($value1, 0, $standard);
		else $result[] = clm_core::$load->make_valid($value1, 0, $standard);
	} 
	return $result;		
}
?>
