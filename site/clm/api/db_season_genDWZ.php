<?php 
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// Berechenen der inoffiziellen DWZ einer Saison
function clm_api_db_season_genDWZ($id,$group=true) {
	$id = clm_core::$load->make_valid($id, 0, -1);
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;
	if($group) {
		$table_main = "#__clm_liga";
		$table_dates = "#__clm_runden_termine";
		$table_dates_id = "liga";
		$table_list = "#__clm_meldeliste_spieler";
		$table_list_id = "sid";
		$table_round = "#__clm_rnd_spl";
		$table_round_id = "lid";
		if ($countryversion == "de") 
			$playerId = "zps=? AND mgl_nr=? AND sid=?";
		else 
			$playerId = "zps=? AND PKZ=? AND sid=?";
		$birthAndID = "";
	} 
	
	// Alte Ergebnisse entfernen
 	clm_core::$api->db_season_delDWZ($id,$group);

	// Ligen/M-Turniere zur Saison auslesen
	$query='SELECT id, liga_mt, params'
			.' FROM #__clm_liga'
			.' WHERE sid='.$id
			.' AND published = 1';
	$liga = clm_core::$db->loadObjectList($query);
	if(count($liga)==0) {
			return array(true, "e_calculateDWZNoLiga"); }	
	$liga_a = array();
	foreach($liga as $liga1){
		//Liga-Parameter aufbereiten
		$paramsStringArray = explode("\n", $liga1->params);
		$liga1->params = array();
		foreach ($paramsStringArray as $value) {
			$ipos = strpos ($value, '=');
			if ($ipos !==false) {
				$key = substr($value,0,$ipos);
				if (substr($key,0,2) == "\'") $key = substr($key,2,strlen($key)-4);
				if (substr($key,0,1) == "'") $key = substr($key,1,strlen($key)-2);
				$liga1->params[$key] = substr($value,$ipos+1);
			}
		}	
		if (!isset($liga1->params['incl_to_season'])) {
			if ($liga1->liga_mt == 0) 
				$liga1->params['incl_to_season'] = '1';
			else 
				$liga1->params['incl_to_season'] = '0'; }
		if ($liga1->params['incl_to_season'] == '1') {
			$liga_a[] = $liga1->id; }
	}
	if(count($liga_a)==0) {
			return array(true, "e_calculateDWZNoLiga"); }	
	$ligen = '';
	$ligen = implode(',',$liga_a);
	// Vermeintliches Ende bestimmen
	$query='SELECT MAX(datum) as date'
			.' FROM #__clm_runden_termine'
			.' WHERE FIND_IN_SET(liga,"'.$ligen.'") != 0';
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
	$query='SELECT a.zps, a.mgl_nr, a.PKZ, d.DWZ as start_dwz, d.DWZ_Index as start_I0, d.FIDE_elo as FIDEelo, d.Geburtsjahr'
			 . ' FROM #__clm_meldeliste_spieler as a'
			 . ' LEFT JOIN #__clm_dwz_spieler AS d ON d.sid = a.sid AND d.zps = a.zps AND d.mgl_nr = a.mgl_nr'
			 . ' WHERE a.sid ='.$id
//			 . ' GROUP by a.zps, a.mgl_nr, a.PKZ'
			;
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
 		 	if ($countryversion == "de")
				$dwz->addPlayer($spieler[$i]->zps.":".$spieler[$i]->mgl_nr,$year-$spieler[$i]->Geburtsjahr,$spieler[$i]->start_dwz,$spieler[$i]->start_I0);
 		 	else
				$dwz->addPlayer($spieler[$i]->zps.":".$spieler[$i]->PKZ,$year-$spieler[$i]->Geburtsjahr,$spieler[$i]->start_dwz,$spieler[$i]->start_I0);
		} 
 	}

	// Wer hat sich diese Struktur ausgedacht?
	if($group) {
 	// Lese alle relevanten Partien aus
		if ($countryversion == "de")
			$query='SELECT zps, spieler, gzps, gegner, ergebnis';
		else
			$query='SELECT zps, PKZ as spieler, gzps, gPKZ as gegner, ergebnis';
		$query .= ' FROM #__clm_rnd_spl'
			.' WHERE FIND_IN_SET(lid,"'.$ligen.'") != 0'
			.' AND heim = 1';
	} 
	$partien = clm_core::$db->loadObjectList($query);

 	// Partien zur DWZ Auswertung hinzufügen
	$someMatch=false;
	for ($i=0;$i < count($partien);$i++)
 	{
		
		list ($punkte, $gpunkte) = clm_core::$load->gen_result($partien[$i]->ergebnis,0);
		if($punkte==-1) {
			continue;		
		}
		if($group) {
 			$dwz->addMatch($partien[$i]->zps.":".$partien[$i]->spieler,$partien[$i]->gzps.":".$partien[$i]->gegner,$punkte, $gpunkte);
		} 
		$someMatch = true;
	}
 	$result = $dwz->getAllPlayerObject();

	if(!$someMatch) {
			return array(false, "e_DWZnoMatch"); 
	}


 	$sql = "UPDATE ".'#__clm_dwz_spieler'." SET DWZ_neu=?, I0=?, Punkte=?, Partien=?, We=?, Leistung=?, EFaktor=?, Niveau=? WHERE ".$playerId;

	$stmt = clm_core::$db->prepare($sql);
 	// Ergebnis Schreiben
	foreach ($result as $id2 => $value)
 	{
		// Korrektur Leistung: Anzeige bei weniger als 5 Spielen oder nur Siegen/Niederlagen nicht gewollt
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
		} 
		$stmt->execute();
	}
 	$stmt->close();

	return array(true, "m_calculateDWZSuccess",$ligen); 
}
?>
