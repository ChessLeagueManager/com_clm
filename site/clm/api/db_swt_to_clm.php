<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2021 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 *
 * Copy der Turnierdaten aus den clm_swt_Tabellen in die clm_Dateien - Teil der Importe von SWT,SWM und TRF
*/
function clm_api_db_swt_to_clm($swt_tid,$tid,$group=false,$update=false) {
    $lang = clm_core::$lang->swm_import;
	$debug = 0;
	$new_ID = $tid;

	
  if (!$group) { // Einzelturnier
	//Copy Turnierdaten
	$select_query = " SELECT * FROM #__clm_swt_turniere
					WHERE swt_tid = ".$swt_tid.";";
	$turnier	= clm_core::$db->loadObject($select_query);
	unset($turnier->tid);
	unset($turnier->swt_tid);
//echo "<br>stc-turnier:"; var_dump($turnier);  //die();		
	if($update == 1 AND $tid != 0) {
if ($debug > 0) { echo "<br>update!!"; }
		$select_query = " SELECT * FROM #__clm_turniere
							WHERE id = ".$tid.";";
		//$db->setQuery($select_query);
		//$turnier_orig = $db->loadObject();
		$turnier_orig	= clm_core::$db->loadObject($select_query);
		$turnier->id = $turnier_orig->id;
		$turnier->catidAlltime = $turnier_orig->catidAlltime;
		$turnier->catidEdition = $turnier_orig->catidEdition;
		$turnier->ordering = $turnier_orig->ordering;
		if (!clm_core::$db->updateObject('#__clm_turniere',$turnier,'id')) {
			return array(false,$turnier->tid);
		}	
		$turnier->tid = $turnier_orig->id;
	} else {
		if(clm_core::$db->insertObject('#__clm_turniere',$turnier,'id')) {
			//Turnier-ID in #__clm_swt_turniere updaten, damit die neue turnier-id über die swt-id gefunden werden kann 
			//für den Fall, dass mit (F5) die Daten erneut gesendet werden und das Turnier bereits in die CLM-Datenbank kopiert wurde
			$turnier->swt_tid = $swt_tid;
			$turnier->tid = clm_core::$db->insert_id();
			unset($turnier->id);
if ($debug > 0) { echo "<br>turnier-insertid:"; var_dump($turnier); } //die();				
			if(!clm_core::$db->updateObject('#__clm_swt_turniere',$turnier,'swt_tid')) {
				return array(false,$turnier->tid);
			}					
		} else {
			return array(false,0);
		}
	}	
		
	//Copy Teilnehmer
	$delete_query = " DELETE FROM #__clm_turniere_tlnr
						WHERE turnier = ".$turnier->tid.";";
	clm_core::$db->query($delete_query);		
	
	$select_query = " SELECT * FROM #__clm_swt_turniere_tlnr
						WHERE swt_tid = ".$swt_tid.";";
	$teilnehmer	= clm_core::$db->loadObjectList($select_query);
	foreach($teilnehmer as $teil){
		unset($teil->id);
		unset($teil->swt_tid);
		$teil->turnier = $turnier->tid;
			
		if(!clm_core::$db->insertObject('#__clm_turniere_tlnr',$teil,'id')) {
			return false;
		}
	}
	
	//Copy RundenInfos
	$delete_query = " DELETE FROM #__clm_turniere_rnd_termine
						WHERE turnier = ".$turnier->tid.";";
	clm_core::$db->query($delete_query);		
		
	$select_query = " SELECT * FROM #__clm_swt_turniere_rnd_termine
						WHERE swt_tid = ".$swt_tid.";";
	$runden	= clm_core::$db->loadObjectList($select_query);
	foreach($runden as $runde){
		unset($runde->id);
		unset($runde->swt_tid);
		$runde->turnier = $turnier->tid;
			
		if(!clm_core::$db->insertObject('#__clm_turniere_rnd_termine',$runde,'id')) {
			return false;
		}
	}

	//Copy Paarungen/Ergebnisse
	$select_query = " SELECT * FROM #__clm_turniere_rnd_spl "
					." WHERE turnier = ".$turnier->tid
					." AND pgn != '' ;";
	$pgn_daten = clm_core::$db->loadObjectList($select_query);

	$pgn_array = array();
	foreach($pgn_daten as $pgn_dat) {
		$pgn_key = ($pgn_dat->spieler * 10000) + ($pgn_dat->gegner * 10) + $pgn_dat->heim;
		$pgn_array[$pgn_key] = new stdClass();
		$pgn_array[$pgn_key]->spieler = $pgn_dat->spieler; 
		$pgn_array[$pgn_key]->gegner = $pgn_dat->gegner; 
		$pgn_array[$pgn_key]->heim = $pgn_dat->heim; 
		$pgn_array[$pgn_key]->pgn = $pgn_dat->pgn; 
	}

	$delete_query = " DELETE FROM #__clm_turniere_rnd_spl
						WHERE turnier = ".$turnier->tid.";";
	clm_core::$db->query($delete_query);		

	
	$select_query = " SELECT * FROM #__clm_swt_turniere_rnd_spl
						WHERE swt_tid = ".$swt_tid.";";
	$paarungen = clm_core::$db->loadObjectList($select_query);
	foreach($paarungen as $paarung){
		unset($paarung->id);
		unset($paarung->swt_tid);
		$paarung->turnier = $turnier->tid;
			
		$pgn_key = ($paarung->spieler * 10000) + ($paarung->gegner * 10) + $paarung->heim;
		if (isset($pgn_array[$pgn_key])) {
			$paarung->pgn = $pgn_array[$pgn_key]->pgn;
		}

		if(!clm_core::$db->insertObject('#__clm_turniere_rnd_spl',$paarung,'id')) {
			return false;
		}
	}
	
	//Copy Mannschaftsdaten bei Einzelturnier mit Mannschaftswertung
	$delete_query = " DELETE FROM #__clm_turniere_teams
						WHERE tid = ".$turnier->tid.";";
	clm_core::$db->query($delete_query);		
		
	$select_query = " SELECT * FROM #__clm_swt_turniere_teams
						WHERE swt_tid = ".$swt_tid.";";
	$teams	= clm_core::$db->loadObjectList($select_query);
	if (!is_null($teams) AND count($teams) > 0) { 
		foreach($teams as $team){
			unset($team->id);
			unset($team->swt_tid);
			$team->tid = $turnier->tid;
			
			if(!clm_core::$db->insertObject('#__clm_turniere_teams',$team,'id')) {
				return false;
			}
		}
	}
	
	return array(true,$turnier->tid);
	
  }



  if ($group) { // Mannschaftswettbewerbe
  
	//Copy Turnierdaten
	$select_query = " SELECT * FROM #__clm_swt_liga
					WHERE id = ".$swt_tid.";";
	$turnier	= clm_core::$db->loadObject($select_query);
	unset($turnier->id);
//echo "<br>stc-turnier:"; var_dump($turnier);  //die();		
	if($update == 1 AND $tid != 0) {
if ($debug > 0) { echo "<br>update!!";  }
		$select_query = " SELECT * FROM #__clm_liga
							WHERE id = ".$tid.";";
		//$db->setQuery($select_query);
		//$turnier_orig = $db->loadObject();
		$turnier_orig	= clm_core::$db->loadObject($select_query);
		$turnier->id = $turnier_orig->id;
		$turnier->catidAlltime = $turnier_orig->catidAlltime;
		$turnier->catidEdition = $turnier_orig->catidEdition;
		$turnier->ordering = $turnier_orig->ordering;
		if (!clm_core::$db->updateObject('#__clm_liga',$turnier,'id')) {
			return array(false,$turnier->tid);
		}	
		$turnier->tid = $turnier_orig->id;
	} else {
		if(clm_core::$db->insertObject('#__clm_liga',$turnier,'id')) {
			//Turnier-ID in #__clm_swt_liga updaten, damit die neue turnier-id über die swt-id gefunden werden kann 
			//für den Fall, dass mit (F5) die Daten erneut gesendet werden und das Turnier bereits in die CLM-Datenbank kopiert wurde
			$new_ID = clm_core::$db->insert_id();
			$turnier->lid = $new_ID;
			$turnier->id = $swt_tid;
//			unset($turnier->id);
if ($debug > 0) { echo "<br>turnier-insertid:"; var_dump($turnier); } //die();				
			if(!clm_core::$db->updateObject('#__clm_swt_liga',$turnier,'id')) {
				return array(false,$turnier->id);
			}					
		} else {
			return array(false,0);
		}
	}	
		
	//Copy RundenInfos
	$delete_query = " DELETE FROM #__clm_runden_termine
						WHERE liga = ".$new_ID.";";
	clm_core::$db->query($delete_query);		
		
	$select_query = " SELECT * FROM #__clm_swt_runden_termine
						WHERE swt_liga = ".$swt_tid.";";
	$runden	= clm_core::$db->loadObjectList($select_query);
	foreach($runden as $runde){
		unset($runde->id);
		unset($runde->swt_liga);
		$runde->liga = $new_ID;
			
		if(!clm_core::$db->insertObject('#__clm_runden_termine',$runde,'id')) {
			return false;
		}
	}

	//Copy Spieler in Saisontabelle
	// Letzte vergebene Mitgl-Nr für Verein "-1"
	$sql = "SELECT MAX(Mgl_Nr) as mmax FROM #__clm_dwz_spieler"
			." WHERE ZPS = '-1' ";
	$mobj = clm_core::$db->loadObject($sql);
	$max_mglnr = $mobj->mmax;
//echo "<br>max_mglnr: $max_mglnr "; 

	$select_query = " SELECT * FROM #__clm_swt_dwz_spieler ";
	$spieler	= clm_core::$db->loadObjectList($select_query);
	foreach($spieler as $spieler){
		unset($spieler->id);
//echo "<br>1spieler-Mgl_Nr: $spieler->Mgl_Nr "; 
		$spieler->Mgl_Nr += $max_mglnr;
//echo "<br>2spieler-Mgl_Nr: $spieler->Mgl_Nr "; 
			
		if(!clm_core::$db->insertObject('#__clm_dwz_spieler',$spieler,'id')) {
			return false;
		}
	}

	//Copy Meldelisten
	$delete_query = " DELETE FROM #__clm_meldeliste_spieler
						WHERE lid = ".$new_ID.";";
	clm_core::$db->query($delete_query);		
	
	$select_query = " SELECT * FROM #__clm_swt_meldeliste_spieler
						WHERE swt_id = ".$swt_tid.";";
	$meldeliste	= clm_core::$db->loadObjectList($select_query);
	foreach($meldeliste as $teil){
		unset($teil->id);
		unset($teil->spielerid);
		unset($teil->swt_id);
		$teil->lid = $new_ID;
		$teil->mnr = $teil->man_id;
		unset($teil->man_id);
		$teil->mgl_nr += $max_mglnr;
		
		if(!clm_core::$db->insertObject('#__clm_meldeliste_spieler',$teil,'id')) {
			return false;
		}
	}
	
	//Copy Einzelpaarungen/Ergebnisse
	$select_query = " SELECT * FROM #__clm_rnd_spl "
					." WHERE lid = ".$new_ID
					." AND pgnnr != '' ;";
	$pgn_daten = clm_core::$db->loadObjectList($select_query);

	$pgn_array = array();
	foreach($pgn_daten as $pgn_dat) {
		$pgn_key = ($pgn_dat->spieler * 10000) + ($pgn_dat->gegner * 10) + $pgn_dat->heim;
		$pgn_array[$pgn_key] = $pgn_dat->pgnnr;
	}

	$delete_query = " DELETE FROM #__clm_rnd_spl
						WHERE lid = ".$new_ID.";";
	clm_core::$db->query($delete_query);		

	
	$select_query = " SELECT * FROM #__clm_swt_rnd_spl
						WHERE swt_id = ".$swt_tid.";";
	$paarungen = clm_core::$db->loadObjectList($select_query);
	foreach($paarungen as $paarung){
		unset($paarung->id);
		unset($paarung->swt_id);
		$paarung->lid = $new_ID;
		if ($paarung->spieler >= 1) $paarung->spieler += $max_mglnr;
		if ($paarung->gegner >= 1) $paarung->gegner += $max_mglnr;
			
		$pgn_key = ($paarung->spieler * 10000) + ($paarung->gegner * 10) + $paarung->heim;
		if (isset($pgn_array[$pgn_key])) {
			$paarung->pgnnr = $pgn_array[$pgn_key];
		}

		if(!clm_core::$db->insertObject('#__clm_rnd_spl',$paarung,'id')) {
			return false;
		}
	}
	
	//Copy Mannschaftsdaten
	$delete_query = " DELETE FROM #__clm_mannschaften
						WHERE liga = ".$new_ID.";";
	clm_core::$db->query($delete_query);		
		
	$select_query = " SELECT * FROM #__clm_swt_mannschaften
						WHERE swt_id = ".$swt_tid.";";
	$teams	= clm_core::$db->loadObjectList($select_query);
	if (!is_null($teams) AND count($teams) > 0) { 
		foreach($teams as $team){
			unset($team->id);
			unset($team->swt_id);
			$team->liga = $new_ID;
			
			if(!clm_core::$db->insertObject('#__clm_mannschaften',$team,'id')) {
				return false;
			}
		}
	}
	
	//Copy Mannschaftspaarungen
	$delete_query = " DELETE FROM #__clm_rnd_man
						WHERE lid = ".$new_ID.";";
	clm_core::$db->query($delete_query);		
		
	$select_query = " SELECT * FROM #__clm_swt_rnd_man
						WHERE swt_id = ".$swt_tid.";";
	$mpaarungen	= clm_core::$db->loadObjectList($select_query);
	if (!is_null($mpaarungen) AND count($mpaarungen) > 0) { 
		foreach($mpaarungen as $mpaarung){
			unset($mpaarung->id);
			unset($mpaarung->swt_id);
			$mpaarung->lid = $new_ID;
			
			if(!clm_core::$db->insertObject('#__clm_rnd_man',$mpaarung,'id')) {
				return false;
			}
		}
	}
	
	return array(true,$new_ID);
	
  }


}

?>
