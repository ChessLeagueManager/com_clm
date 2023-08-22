<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_function_is_email($email)
{
// Include the class
	if (!class_exists('idna_convert')) {
		$path = clm_core::$path . DS . "includes" . DS . "idna_convert.class" . '.php';
		require_once ($path);
	}
	if (is_null($email)) return false;
	if (strpos($email,'@') === false) return false;
	$parts = explode('@', $email);
	if (count($parts) != 2) return false;
// Instantiate it (depending on the version you are using) with
	$IDN = new idna_convert();
// Encode it to its punycode presentation
	$parts1 = $IDN->encode($parts[1]); 
    return (filter_var($parts[0].'@'.$parts1, FILTER_VALIDATE_EMAIL) !== false ? true : false);
}
?>