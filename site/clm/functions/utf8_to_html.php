<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
// Umcodieren der Umlaute
// z.B. für Ausgaben in Mails
function clm_function_utf8_to_html($str = '') {
	$str = str_replace('Ä', '&Auml;', $str);
	$str = str_replace('Á', '&Aacute;', $str);
	$str = str_replace('À', '&Agrave;', $str);
	$str = str_replace('Â', '&Acirc;', $str);
	$str = str_replace('Ç', '&Ccedil;', $str);
	$str = str_replace('Ë', '&Euml;', $str);
	$str = str_replace('É', '&Eacute;', $str);
	$str = str_replace('È', '&Egrave;', $str);
	$str = str_replace('Ê', '&Ecirc;', $str);
	$str = str_replace('Ï', '&Iuml;', $str);
	$str = str_replace('Ñ', '&Ntilde;', $str);
	$str = str_replace('Ö', '&Ouml;', $str);
	$str = str_replace('Ó', '&Oacute;', $str);
	$str = str_replace('Ò', '&Ograve;', $str);
	$str = str_replace('Ô', '&Ocirc;', $str);
	$str = str_replace('Õ', '&Otilde;', $str);
	$str = str_replace('Ü', '&Uuml;', $str);
	$str = str_replace('Ÿ', '&Yuml;', $str);
	$str = str_replace('ä', '&auml;', $str);
	$str = str_replace('á', '&aacute;', $str);
	$str = str_replace('à', '&agrave;', $str);
	$str = str_replace('â', '&acirc;', $str);
	$str = str_replace('ç', '&ccedil;', $str);
	$str = str_replace('ë', '&euml;', $str);
	$str = str_replace('é', '&eacute;', $str);
	$str = str_replace('è', '&egrave;', $str);
	$str = str_replace('ê', '&ecirc;', $str);
	$str = str_replace('ï', '&iuml;', $str);
	$str = str_replace('ñ', '&ntilde;', $str);
	$str = str_replace('ö', '&ouml;', $str);
	$str = str_replace('ó', '&oacute;', $str);
	$str = str_replace('ò', '&ograve;', $str);
	$str = str_replace('ô', '&ocirc;', $str);
	$str = str_replace('õ', '&otilde;', $str);
	$str = str_replace('ü', '&uuml;', $str);
	$str = str_replace('ÿ', '&yuml;', $str);
	$str = str_replace('ß', '&szlig;', $str);
	$str = str_replace('´', '&rsquo;', $str);
	return $str;		
}
?>
