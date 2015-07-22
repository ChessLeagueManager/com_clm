<?php
function clm_api_db_tournament_auto($id, $group = true, $report = false, $runde = 0, $dg = 0, $paar = 0) {
	$id = clm_core::$load->make_valid($id, 0, -1);
	if($group) {
		$table = "liga";
	} else {
		$table = "turniere";
	}

		// inoff. DWZ Auswerten/Löschen/Ignorieren
		$params = new clm_class_params(clm_core::$db->$table->get($id)->params);
		$autoDWZ = $params->get("autoDWZ",0);
		if($autoDWZ == 0) {
			clm_core::$api->direct("db_tournament_genDWZ",array($id,$group));
		} else if($autoDWZ == 1) {
			clm_core::$api->direct("db_tournament_delDWZ",array($id,$group));
		// DWZ nicht bei db_report_save aktualisieren (Ergebnismeldung)
		} else if($autoDWZ == 2 && !$report) {
			clm_core::$api->direct("db_tournament_genDWZ",array($id,$group));
		}
		// Rangliste Auswerten/Ignorieren
		$params = new clm_class_params(clm_core::$db->$table->get($id)->params);
		$autoDWZ = $params->get("autoRANKING",0);
		if($autoDWZ == 0) {
			clm_core::$api->db_tournament_ranking($id,$group);
		}
}
?>
