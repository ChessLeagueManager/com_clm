<?php
function clm_api_db_tournament_del($id,$group=true) {
	$id = clm_core::$load->make_valid($id, 0, -1);
	
	if($group) {
		clm_core::$api->db_tournament_delRounds($id,true);
		$query = " DELETE FROM #__clm_liga WHERE id = ".$id;
		clm_core::$db->query($query);
		$query = "DELETE FROM #__clm_mannschaften WHERE liga = ".$id;
		clm_core::$db->query($query);
		$query = "DELETE FROM #__clm_meldeliste_spieler WHERE lid = ".$id;
		clm_core::$db->query($query);
	} else {
		clm_core::$api->db_tournament_delRounds($id,false);
		$query = " DELETE FROM #__clm_turniere " . " WHERE id = " . $id;
		clm_core::$db->query($query);
		$query = "DELETE FROM #__clm_turniere_tlnr " . " WHERE turnier = " . $id;
		clm_core::$db->query($query);
		$query = "DELETE FROM #__clm_turniere_sonderranglisten " . " WHERE turnier = " . $id;
		clm_core::$db->query($query);
	}
	return array(true, "");
}
?>