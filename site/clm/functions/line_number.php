<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// Ermitteln der Anzahl Anzeigezeilen
// in Abhängigkeit von memory_limit und Spieleranzahl zur Auswahl
function clm_function_line_number($count,$type = 'decode') {
	$line_number = 1;
	$memory_limit = (integer) ini_get('memory_limit');

	if ($memory_limit >= 256) $memory_limit = 256;
	elseif ($memory_limit >= 192) $memory_limit = 192;
	elseif ($memory_limit >= 128) $memory_limit = 128;
	elseif ($memory_limit >= 64) $memory_limit = 64;
	elseif ($memory_limit >= 32) $memory_limit = 32;
	else return $line_number;
	if ($count >= 15001) $count = 20000;
	elseif ($count >= 12001) $count = 15000;
	elseif ($count >= 10001) $count = 12000;
	elseif ($count >= 8001) $count = 10000;
	elseif ($count >= 6001) $count = 8000;
	elseif ($count >= 4001) $count = 6000;
	else $count = 4000;
	
	$tab = array();
	$tab[32][20000] = 2; $tab[64][20000] = 5;  $tab[128][20000] = 12; $tab[192][20000] = 20; $tab[256][20000] = 25;
	$tab[32][15000] = 3; $tab[64][15000] = 8;  $tab[128][15000] = 20; $tab[192][15000] = 25; $tab[256][15000] = 35;
	$tab[32][12000] = 4; $tab[64][12000] = 10; $tab[128][12000] = 25; $tab[192][12000] = 35; $tab[256][12000] = 50;
	$tab[32][10000] = 5; $tab[64][10000] = 12; $tab[128][10000] = 32; $tab[192][10000] = 45; $tab[256][10000] = 60;
	$tab[32][8000] = 7;  $tab[64][8000] = 16;  $tab[128][8000] = 40;  $tab[192][8000] = 60;  $tab[256][8000] = 80;
	$tab[32][6000] = 12; $tab[64][6000] = 24;  $tab[128][6000] = 55;  $tab[192][6000] = 85;  $tab[256][6000] = 115;
	$tab[32][4000] = 15; $tab[64][4000] = 35;  $tab[128][4000] = 80;  $tab[192][4000] = 110; $tab[256][4000] = 140;

	$line_number = $tab[$memory_limit][$count];
	if ($line_number > 50) $line_number = 50;

	return $line_number;		
}
?>
