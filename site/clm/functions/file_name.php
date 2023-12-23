<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// Ersetzen von Sonderzeichen u.ä. in Dateinamen
// beim Erstellen, z.B. Auswertungsdateien, Export von Ligadaten
function clm_function_file_name($filename = '') {
	$filename	= str_replace(' ', '_', $filename);
	$vz_array	= array('~','“','#','%','&','*',':','<','>','?','/','\\','{','|','}');
	$filename	= str_replace($vz_array, '_', $filename);
	$filename 	= clm_core::$load->sub_umlaute($filename);
	return $filename;		
}
?>
