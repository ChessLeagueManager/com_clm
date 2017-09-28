<?php
/**
* Import einer Turnierdatei vom Swiss-Manager ( zur Zeit nur TUNx = Einzelturnier im CH-Modus
*/
function clm_api_db_swt_to_clm($swt_tid,$group=false,$update=false,$tid=0) {
    $lang = clm_core::$lang->swm_import;
	$debug = 0;
	$new_ID = 0;
	
	//Copy Turnierdaten
	$select_query = " SELECT * FROM #__clm_swt_turniere
					WHERE swt_tid = ".$swt_tid.";";
	$turnier	= clm_core::$db->loadObject($select_query);
	unset($turnier->tid);
	unset($turnier->swt_tid);
//echo "<br>stc-turnier:"; var_dump($turnier);  //die();		
	if($update == 1 AND $tid != 0) {
if ($debug > 0) { echo "<br>update??"; die(); }
			//$turnier->id = $tid;
			$select_query = " SELECT * FROM #__clm_turniere
								WHERE id = ".$tid.";";
			$db->setQuery($select_query);
			$turnier_orig = $db->loadObject();
			if ($turnier_orig->teil != $turnier->teil) {
				$turnier_orig->teil = $turnier->teil;
				if($db->updateObject('#__clm_turniere',$turnier_orig,'id')) {
					return true;
				} else {
					return false;
				}	
			} else {
				return true;
			} 
	} else {
		if(clm_core::$db->insertObject('#__clm_turniere',$turnier,'id')) {
			//Turnier-ID in #__clm_swt_turniere updaten, damit die neue turnier-id �ber die swt-id gefunden werden kann 
			//f�r den Fall, dass mit (F5) die Daten erneut gesendet werden und das Turnier bereits in die CLM-Datenbank kopiert wurde
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
	
	return array(true,$turnier->tid);
	
}

?>
