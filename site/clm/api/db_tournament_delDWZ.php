<?php
function clm_api_db_tournament_delDWZ($id, $group=true) {
	$id = clm_core::$load->make_valid($id, 0, -1);
	if($group) {
		$table_list = "#__clm_meldeliste_spieler";
		$table_list_id = "lid";
	} else {
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
	if(!$table->isNew()) {
		$params = new clm_class_params($table->params);
		$params->set("inofDWZ","");
		$table->params = $params->params();
	}
	return array(true,"m_delDWZ");
}
?>
