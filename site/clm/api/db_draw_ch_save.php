<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
/**
* Ergebnis der Auslosungsroutine für Einzelturnier im CH-Modus in DB speichern
*/
function clm_api_db_draw_ch_save($turnierid,$dg,$round,$pairings,$group=false) {
    //$lang = clm_core::$lang->swm_import;
	$debug = 0;
//echo "<br>s-pairings: "; var_dump($pairings);
//die();	
	//Turnierdetails laden
	$select_query = " SELECT * FROM #__clm_turniere "
					."	WHERE id = ".$turnierid;
	$turnier	= clm_core::$db->loadObject($select_query);
echo "<br>turnier:"; var_dump($turnier);

	//Löschen der gespeichertetn Paarungen
	$delete_query = " DELETE FROM #__clm_turniere_rnd_spl "
					." WHERE turnier = ".$turnierid
					." AND dg = ".$dg
					." AND runde = ".$round;
	clm_core::$db->query($delete_query);
	
	//Copy Paarungen/Ergebnisse
	$brett = 0;
	$record = new stdClass();
	foreach($pairings as $pairing) {
echo "<br>pairing:"; var_dump($pairing);
		if (isset($record->id)) unset($record->id);
		$brett++;
		$record->sid = $turnier->sid;
		$record->turnier = $turnierid;
		$record->runde = $round;
		$record->paar = NULL;
		$record->brett = $brett;            //$pairing["brett"];
		$record->dg = $dg;
		$record->tln_nr = $pairing["wsnr"];
		$record->heim = 1;
		$record->spieler = $pairing["wsnr"];
		$record->gegner = $pairing["bsnr"];
		if (isset($pairing["werg"])) $record->ergebnis = $pairing["werg"]; 
		else $record->ergebnis = NULL;
		$record->tiebrS = 0;
		$record->tiebrG = 0;
		$record->kampflos = NULL;
		$record->gemeldet = NULL;
		$record->pgn = NULL;
		$record->ordering = 0;
echo "<br>wrecord:"; var_dump($record); //die('00');
		if(!clm_core::$db->insertObject('#__clm_turniere_rnd_spl',$record,'id')) {
die('11');
			return false;	
		}
//die('22');
		if (isset($record->id)) unset($record->id);
		$record->tln_nr = $pairing["bsnr"];
		$record->heim = 0;
		$record->spieler = $pairing["bsnr"];
		$record->gegner = $pairing["wsnr"];
		if (isset($pairing["berg"])) $record->ergebnis = $pairing["berg"]; 
		else $record->ergebnis = NULL;
echo "<br>brecord:"; var_dump($record); //die('00');
		if(!clm_core::$db->insertObject('#__clm_turniere_rnd_spl',$record,'id')) {
die('33');
			return false;	
		}
//die('44');

	}
//die('55');
	return array(true,'fehlerfrei');
	
}

?>
