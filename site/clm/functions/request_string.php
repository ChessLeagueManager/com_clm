<?php
/*
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2019 CLM Team. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// Einlesen von Get-Parameter vom Typ String
function clm_function_request_string($input, $standard = '') {
	if (isset($_GET[$input])) $value = $_GET[$input];
	elseif (isset($_POST[$input])) $value = $_POST[$input];
	else return $standard;
	$result = clm_core::$load->make_valid($value, 8, $standard);	
	return $result;		
}
?>
