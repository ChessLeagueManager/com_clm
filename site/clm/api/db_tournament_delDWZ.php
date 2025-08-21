<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_api_db_tournament_delDWZ($id, $group=true) {
	$id = clm_core::$load->make_valid($id, 0, -1);
	if($group) {
	    $table_main = "#__clm_liga";
		$table_list = "#__clm_meldeliste_spieler";
		$table_list_id = "lid";
	} else {
	    $table_main = "#__clm_turniere";
		$table_list = "#__clm_turniere_tlnr";
		$table_list_id = "turnier";
	}

	// Löschen der DWZ-Auswertung zur LigaSaison
	$query = "UPDATE ".$table_list
		." SET DWZ = 0"
		." , I0 = 0"
		." , Punkte = 0"
		." , Partien = 0"
		." , WE = 0"
		." , Leistung = 0"
		." , EFaktor = 0"
		." , Niveau = 0"
		." WHERE ".$table_list_id."= ".$id;

	clm_core::$db->query($query);
	
	if($group) {
		$table = clm_core::$db->liga->get($id);
	} else {
		$table = clm_core::$db->turniere->get($id);
	}
	$optionEloAnalysis = '0';
	if(!$table->isNew()) {
		$params = new clm_class_params($table->params);
		$optionEloAnalysis = $params->get('optionEloAnalysis', '0');
		$params->set("inofDWZ","");
		$table->params = $params->params();
	    $params = $params->params();
	    $stmt = clm_core::$db->prepare("UPDATE $table_main SET params = ? WHERE id = ?");
	    $stmt->bind_param("si", $params, $id);
	    $stmt->execute();
	}

    if ($optionEloAnalysis == '0')
		return array(true, "m_delDWZ");

    $elo_result = clm_core::$api->db_tournament_delFIDERating($id,$group);

    if ($elo_result[0]) {
        return array(true,"m_delDWZ");
    } else {
        return $elo_result;
    }
}
?>
