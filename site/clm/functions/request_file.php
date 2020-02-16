<?php
/*
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// Einlesen von Get-Parameter vom Typ String
function clm_function_request_file($input, $standard = null) {
	if (isset($_FILES[$input])) $result = $_FILES[$input];
	else return $standard;
	return $result;		
}
?>
