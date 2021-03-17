<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_function_is_time($time)
{
	if( !(bool)preg_match('/^(?:2[0-3]||(([0-9]||0[0-9])||1[0-9])):[0-5][0-9]$/', trim($time)) ) {
		return false; //echo "Uhrzeit ungültig!\n"; 
	}
    return true;
}
?>
