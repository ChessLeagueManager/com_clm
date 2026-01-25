<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
// Lesen aus Tabelle und Anzeigen von Clublogos
// Eingabe: Club-Code(ZPS), Größe in pixel
function clm_function_show_club_logo($zps,$psize = 0,$ptype = 'html') {
	if ($zps < '0') return '';
	
	$query = " SELECT * FROM #__clm_images "
		." WHERE typ = 'club'"
		." AND key1 = '".$zps."'"
		." ORDER BY key2 DESC LIMIT 1 ";
	$rimage	= clm_core::$db->loadObjectList($query);
	if (isset($rimage[0]->image) AND !is_null($rimage[0]->image)) $image64 = $rimage[0]->image;
	else return '';
		
	$koef_w = ($rimage[0]->width / $rimage[0]->height);
	$koef_h = ($rimage[0]->height / $rimage[0]->width);

	if ($ptype == 'html') {
		$output = '<img src="'.$image64.'" alt="" ';
		if ($psize != 0) { // nicht Originalgröße
			if ($rimage[0]->width >= $rimage[0]->height) $output .= ' width="'.$psize.'" height ="'.intval($psize * $koef_h).'"';
			else $output .= ' height ="'.$psize.'" width ="'.intval($psize * $koef_w).'"';
		}
		$output .= ' align="middle" border="0" /> ';
		return $output;
	}
				  
	if ($ptype == 'pdf') { 
		$output = $image64;
		if ($psize != 0) { // nicht Originalgröße
			if ($rimage[0]->width >= $rimage[0]->height) {
				$width = $psize; $height = intval($psize * $koef_h);
			} else {
				$height = $psize; $width = intval($psize * $koef_w);
			}
		} else {
			$width = $rimage[0]->width;
			$height = $rimage[0]->height;
		}
		return array($output,$width,$height);
	}
	return '';
}
?>
