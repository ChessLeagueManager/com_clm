<?php 
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// Berechenen der inoffiziellen DWZ eines Turniers
function clm_api_db_tournament_genDWZ($id,$group=true) {
	$id = clm_core::$load->make_valid($id, 0, -1);
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;
	if($group) {
		$table_main = "#__clm_liga";
		$table_dates = "#__clm_runden_termine";
		$table_dates_id = "liga";
		$table_list = "#__clm_meldeliste_spieler";
		$table_list_id = "lid";
		$table_round = "#__clm_rnd_spl";
		$table_round_id = "lid";
		if ($countryversion == "de") 
			$playerId = "zps=? AND mgl_nr=? AND lid=?";
		else 
			$playerId = "zps=? AND PKZ=? AND lid=?";
		$birthAndID = "";
	} else {
		$table_main = "#__clm_turniere";
		$table_dates = "#__clm_turniere_rnd_termine";
		$table_dates_id = "turnier";
		$table_list = "#__clm_turniere_tlnr";
		$table_list_id = "turnier";
		$table_round = "#__clm_turniere_rnd_spl";
		$table_round_id = "turnier";
		$playerId = "snr=? AND turnier=?";
		$birthAndID = ", birthYear, snr";
	}
	// Alte Ergebnisse entfernen
 	clm_core::$api->db_tournament_delDWZ($id,$group);

	// Liga Punktebereich auslesen
	$query='SELECT sid, params'
			.' FROM '.$table_main
			.' WHERE id='.$id;
	$liga = clm_core::$db->loadObjectList($query);
	if(count($liga)==0) {
			return array(true, "e_calculateDWZNoLiga"); 	
	}
	$liga = $liga[0];
	// DWZ-Handling ermitteln 
	$tparams = new clm_class_params($liga->params);
	$param_dwz_date = $tparams->get('dwz_date', '1970-01-01');
	if ($param_dwz_date == '0000-00-00' OR $param_dwz_date == '1970-01-01') $old = true; // DWZ aus dwz_spieler
	else $old = false;				// DWZ aus Meldeliste
	// Vermeintliches Ende bestimmen
	$query='SELECT MAX(datum) as date'
			.' FROM '.$table_dates
			.' WHERE '.$table_dates_id.'='.$id;
	$datum = clm_core::$db->loadObjectList($query);
	if(count($datum)==0) {
			return array(true, "e_calculateDWZNoRound"); 	
	}
	$year = substr($datum[0]->date,0,4);
	if($year=="0000") {
		$year = date('Y'); // Falls kein Jahr angegeben wurde
	} else {
		$year = intval($year);
	}
	// Lese alle beteiligten Spieler aus
	$query='SELECT zps, mgl_nr, PKZ, start_dwz, start_I0, FIDEelo'.$birthAndID
			 . ' FROM '.$table_list
			 . ' WHERE '.$table_list_id.'='.$id;
	$spieler = clm_core::$db->loadObjectList($query);
	$dwz = new clm_class_dwz_rechner();

	if(count($spieler)==0) {
			return array(false, "e_DWZnoPlayer"); 
	}

	// Spieler zur DWZ Auswertung hinzufügen
	for ($i=0;$i < count($spieler);$i++)
 	{
		// SWT Importe besitzen keinen Index, falls die DWZ größer als 0 ist muss es jedoch einen geben.
		if($spieler[$i]->start_I0==0 && $spieler[$i]->start_dwz>0) {
			$spieler[$i]->start_I0=22;
		}
		if($group) {
 			$query='SELECT Geburtsjahr, DWZ as dsbDWZ, DWZ_Index as dsbDWZ_Index '
 					.' FROM #__clm_dwz_spieler'
 					.' WHERE sid='.$liga->sid
 					.' AND ZPS="'. clm_core::$db->escape($spieler[$i]->zps) .'"';
 		 	if ($countryversion == "de")
				$query .= ' AND Mgl_Nr="'. clm_core::$db->escape($spieler[$i]->mgl_nr) .'"';
 		 	else
				$query .= ' AND PKZ="'. clm_core::$db->escape($spieler[$i]->PKZ) .'"';
 			$birth = clm_core::$db->loadObjectList($query);
 
			if ($old) { 	// keine DWZ-Angaben in Meldeliste, Berechnung auf Basis dwz_spieler
				$spieler[$i]->start_dwz = $birth[0]->dsbDWZ;
				$spieler[$i]->start_I0  = $birth[0]->dsbDWZ_Index;
				if($spieler[$i]->start_I0==0 && $spieler[$i]->start_dwz>0) {  // Wiederholung wegen England ohne Index
					$spieler[$i]->start_I0=22;
				}
			} 
 			if(count($birth)==0) { // Spieler in der Saison gelöscht?
 				$birth = 0; // Spieler wird als älter als 25 angenommen
 			} else {
 		 		$birth = $birth[0]->Geburtsjahr;
 			}
 		 	if ($countryversion == "de")
				$dwz->addPlayer($spieler[$i]->zps.":".$spieler[$i]->mgl_nr,$year-$birth,$spieler[$i]->start_dwz,$spieler[$i]->start_I0);
 		 	else
				$dwz->addPlayer($spieler[$i]->zps.":".$spieler[$i]->PKZ,$year-$birth,$spieler[$i]->start_dwz,$spieler[$i]->start_I0);
		} else {
			if(intval($spieler[$i]->birthYear)==0) {
				$spieler[$i]->birthYear = $year-100;
			}
			$dwz->addPlayer("p".$spieler[$i]->snr,$year-$spieler[$i]->birthYear,$spieler[$i]->start_dwz,$spieler[$i]->start_I0,$spieler[$i]->FIDEelo);
		}
 		// addPlayer($id, $A, $R_o, $Index)
 	}

	// Wer hat sich diese Struktur ausgedacht?
	if($group) {
 	// Lese alle relevanten Partien aus
	if ($countryversion == "de")
		$query='SELECT zps, spieler, gzps, gegner, ergebnis';
	else
		$query='SELECT zps, PKZ as spieler, gzps, gPKZ as gegner, ergebnis';
	$query .= ' FROM '.$table_round
			. ' WHERE '.$table_round_id.'='.$id
			. ' AND heim = 1';
	} else {
 	// Lese alle relevanten Partien aus
	$query='SELECT ergebnis, spieler, gegner'
		 	. ' FROM '.$table_round
			. ' WHERE '.$table_round_id.'='.$id
			. ' AND ergebnis IS NOT NULL'
			. ' AND heim = 1';
	}
	$partien = clm_core::$db->loadObjectList($query);

 	// Partien zur DWZ Auswertung hinzufügen
	$someMatch=false;
	for ($i=0;$i < count($partien);$i++)
 	{
		
		list ($punkte, $gpunkte) = clm_core::$load->gen_result($partien[$i]->ergebnis,0);
		if($punkte==-1) {	// due to php 7.4  old: if($punkte[0]==-1) {
			continue;		
		}
		// addMatch($id1, $id2, $result)
		if($group) {
 			$dwz->addMatch($partien[$i]->zps.":".$partien[$i]->spieler,$partien[$i]->gzps.":".$partien[$i]->gegner,$punkte,$gpunkte);
		} else {
 			$dwz->addMatch("p".$partien[$i]->spieler,"p".$partien[$i]->gegner,$punkte, $gpunkte);
		}
		$someMatch = true;
	}
 	$result = $dwz->getAllPlayerObject();

	if(!$someMatch) {
			return array(false, "e_DWZnoMatch"); 
	}


 	$sql = "UPDATE ".$table_list." SET DWZ=?, I0=?, Punkte=?, Partien=?, We=?, Leistung=?, EFaktor=?, Niveau=? WHERE ".$playerId;

	$stmt = clm_core::$db->prepare($sql);
 	// Ergebnis Schreiben
	foreach ($result as $id2 => $value)
 	{

		// Korrektur Leistung: Anzeige bei weniger als 5 Spielen oder nur Siegen/Niederlagen nicht gewollt
//		if($value->n<5 || $value->W==0 || $value->W==$value->n) {
//			$value->R_p = 0;
//		}
		if($value->n<5 || $value->W==0 ) {
			$value->R_p = 0;
		}
		elseif($value->W==$value->n) {
			$value->R_p = $value->R_c + 667;
		}

		if($group) {
			$id2 = explode(":",$id2);
			if ($countryversion == "de")
				$stmt->bind_param('iididiiisii', $value->R_n,$value->R_nI,$value->W,$value->n,$value->W_e,$value->R_p,$value->E,$value->R_c,$id2[0],$id2[1],$id);
			else
				$stmt->bind_param('iididiiissi', $value->R_n,$value->R_nI,$value->W,$value->n,$value->W_e,$value->R_p,$value->E,$value->R_c,$id2[0],$id2[1],$id);
		} else {
			$id2 = explode("p",$id2);
			$stmt->bind_param('iididiiiii', $value->R_n,$value->R_nI,$value->W,$value->n,$value->W_e,$value->R_p,$value->E,$value->R_c,$id2[1],$id);
		}
		$stmt->execute();
	}
 	$stmt->close();

	if($group) {
		$table = clm_core::$db->liga->get($id);
	} else {
		$table = clm_core::$db->turniere->get($id);
	}
	if(!$table->isNew()) {
		$params = new clm_class_params($table->params);
		$params->set("inofDWZ","1");
		$table->params = $params->params();
	}
	return array(true, "m_calculateDWZSuccess"); 
}
?>
