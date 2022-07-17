<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2022 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// Ersetzen von Umlauten
// z.B. für internationale Schreibweise in pgn-Vorlagen
function clm_function_sub_umlaute($string = '') {
	$search = array("Ä", "Ö", "Ü", "ä", "ö", "ü", "ß", "á", "é");
	$replace = array("Ae", "Oe", "Ue", "ae", "oe", "ue", "ss", "a", "e");
	return str_replace($search, $replace, $string);		
}
?>
