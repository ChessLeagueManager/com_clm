<?php
/*
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
// Einlesen von GET-/POST-Parameter vom Typ Integer

use Joomla\CMS\Factory;

function clm_function_request_int($input, $standard = 0) {
	if (isset($_GET[$input])) $value = $_GET[$input];
	elseif (isset($_POST[$input])) $value = $_POST[$input];
	elseif (!class_exists('Factory')) return $standard; // kein Joomla
	else {
		$app =Factory::getApplication(); // nur nötig wegen Menüeintragstypen
		$xy = $app->input->getInt($input);
		if (!is_null($xy)) $value = $xy;
		else return $standard; 
	}
	$result = clm_core::$load->make_valid($value, 0, $standard);	
	return $result;		
}
?>
