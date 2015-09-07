<?php
function clm_api_db_season_delDWZ($id, $group=true) {
//	echo "<br>id: ".$id; die('kk');
	$id = clm_core::$load->make_valid($id, 0, -1);
	if($group) {
		$table_list = "#__clm_dwz_spieler";
		$table_list_id = "sid";
	} 

	// Löschen der DWZ-Auswertung zur Saison
	$query = "UPDATE ".$table_list
		." SET DWZ_neu = 0"
		." , I0 = 0"
		." , Punkte = 0"
		." , Partien = 0"
		." , WE = 0"
		." , Leistung = 0"
		." , EFaktor = 0"
		." , Niveau = 0"
		." WHERE ".$table_list_id."= ".$id;

	clm_core::$db->query($query);
 
	return array(true,"m_delDWZ");
}
?>
