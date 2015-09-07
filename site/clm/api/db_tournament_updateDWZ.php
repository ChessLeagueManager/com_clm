<?php
function clm_api_db_tournament_updateDWZ($id,$group=true) {
	$id = clm_core::$load->make_valid($id, 0, -1);
	if($group) {
		$table_main = "#__clm_liga";
		$table_list = "#__clm_meldeliste_spieler";
		$table_list_id = "lid";
		$elo  = ", FIDEelo=?";
	} else {
		$table_main = "#__clm_turniere";
		$table_list = "#__clm_turniere_tlnr";
		$table_list_id = "turnier";
		$elo = ", FIDEcco=?, FIDEelo=?, FIDEid=?, twz=?";
	}
	// Datum der zu übernehmenden DWZ Daten anhand der Saison festsetzen, eventuell useAsTWZ auslesen
	$lastDWZUpdate = clm_core::$db->saison->get(clm_core::$access->getSeason())->datum;
	if($group) {
		$table = clm_core::$db->liga->get($id);
	} else {
		$table = clm_core::$db->turniere->get($id);
	}
	$params = new clm_class_params($table->params);
	$params->set("dwz_date",$lastDWZUpdate);
	$table->params = $params->params();
	
	if(!$group) {
		$useAsTWZ = $params->get("useAsTWZ","0");
	}
	// Spieler DWZ Aktualisieren
	$query = 'SELECT a.zps as zps, a.mgl_nr as mgl_nr, b.DWZ as dwz, b.FIDE_Elo as FIDEelo, b.FIDE_ID as FIDEid, b.FIDE_Land as FIDEcco, b.DWZ_Index as dwz_index FROM '.$table_list.' as a'
			  .' INNER JOIN #__clm_dwz_spieler as b'
			  .' ON a.sid=b.sid AND a.zps=b.zps AND a.mgl_nr = b.Mgl_Nr '
			  .' WHERE a.'.$table_list_id.' = '.$id
					;
	$players = clm_core::$db->loadObjectList($query);

	$sql = "UPDATE ".$table_list." SET start_dwz=?, start_I0=?".$elo." WHERE ".$table_list_id."=? AND zps=? AND mgl_nr=?";
	$stmt = clm_core::$db->prepare($sql);
 	// Ergebnis Schreiben
	foreach ($players as $value)
 	{
 		if($group) {
			$stmt->bind_param('iiiisi', $value->dwz, $value->dwz_index,$value->FIDEelo, $id, $value->zps, $value->mgl_nr);
		} else {
			// TWZ Aktualisieren 	
			$twz = clm_core::$load->gen_twz($useAsTWZ,$value->dwz,$value->FIDEelo);
			$stmt->bind_param('iisiiiisi', $value->dwz, $value->dwz_index, $value->FIDEcco,$value->FIDEelo,$value->FIDEid,$twz, $id, $value->zps, $value->mgl_nr);
		}
		$stmt->execute();
	}
 	$stmt->close();
 	
	// Berechne oder Lösche die inoff. DWZ nach dieser Änderung
	if($group) {
		$params = clm_core::$db->liga->get($id)->params;
	} else {
		$params = clm_core::$db->turniere->get($id)->params;
	}	
	$turParams = new clm_class_params($params);
	$autoDWZ = $turParams->get("autoDWZ",0);
	if($autoDWZ == 0) {
		clm_core::$api->direct("db_tournament_genDWZ",array($id,$group));
	} else if($autoDWZ == 1) {
		clm_core::$api->direct("db_tournament_delDWZ",array($id,$group));
	}
	return array(true, "m_updateDWZSuccess"); 
}
?>
