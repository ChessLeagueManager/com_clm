<?php
// Eingang: Verband
// Ausgang: Alle Vereine in diesem
function clm_api_db_clubs($verband) {
	$verband = clm_core::$load->make_valid($verband, 8, "000");
	$out = clm_core::$load->unit_range($verband);
	if(count($out)==0){
		return array(true, "w_wrongAssociationFormat", array());
	}	
	$config = clm_core::$db->config();
	// Vereine aus der CLM Vereinstabelle holen
	$sql = " SELECT * FROM #__clm_dwz_vereine " . " WHERE Verband >= '$out[0]' AND Verband <= '$out[1]' " . " AND sid = " . clm_core::$access->getSeason(). ' ORDER BY ';
	if ($config->verein_sort =="1") { 
		$sql = $sql."ZPS ASC";
	} else {
		$sql = $sql."Vereinname ASC";
	}
	$vereine = clm_core::$db->loadObjectList($sql);
	return array(true, (count($vereine) == 0 ? 'w_noAssociationFound' : ''), $vereine);
}
?>
