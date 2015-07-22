<?php
function clm_api_db_season_delete($id = - 1) {
	$id = clm_core::$load->make_valid($id, 0, -1);

	if ($id == -1 || (!clm_core::$db->saison->get($id)->isChange() && clm_core::$db->saison->get($id)->isNew())) {
		return array(false, "e_noSeasonToDelete");
	}
			
	if ($id == clm_core::$access->getSeason())
	{
		return array(false, "e_activSeasonDelete");
	}
			
	// Datensätze löschen
	$query = 'DELETE FROM #__clm_saison' . ' WHERE id IN ( ' . $id . ' )';
	clm_core::$db->query($query);
	$query = " DELETE FROM #__clm_dwz_spieler " . " WHERE sid = " . $id;
	clm_core::$db->query($query);
	$query = " DELETE FROM #__clm_liga " . " WHERE sid = " . $id;
	clm_core::$db->query($query);
	$query = "DELETE FROM #__clm_mannschaften " . "WHERE sid = " . $id;
	clm_core::$db->query($query);
	$query = "DELETE FROM #__clm_meldeliste_spieler " . "WHERE sid = " . $id;
	clm_core::$db->query($query);
	$query = " DELETE FROM #__clm_rangliste_id " . " WHERE sid = " . $id;
	clm_core::$db->query($query);
	$query = " DELETE FROM #__clm_rangliste_name " . " WHERE sid = " . $id;
	clm_core::$db->query($query);
	$query = " DELETE FROM #__clm_rangliste_spieler " . " WHERE sid = " . $id;
	clm_core::$db->query($query);
	$query = "DELETE FROM #__clm_rnd_man " . "WHERE sid = " . $id;
	clm_core::$db->query($query);
	$query = "DELETE FROM #__clm_rnd_spl " . "WHERE sid = " . $id;
	clm_core::$db->query($query);
	$query = "DELETE FROM #__clm_runden_termine " . "WHERE sid = " . $id;
	clm_core::$db->query($query);
	$query = " DELETE FROM #__clm_turniere " . " WHERE sid = " . $id;
	clm_core::$db->query($query);
	$query = " DELETE FROM #__clm_turniere_rnd_spl " . " WHERE sid = " . $id;
	clm_core::$db->query($query);
	$query = " DELETE FROM #__clm_turniere_rnd_termine " . " WHERE sid = " . $id;
	clm_core::$db->query($query);
	$query = " DELETE FROM #__clm_turniere_tlnr " . " WHERE sid = " . $id;
	clm_core::$db->query($query);
	$query = " DELETE FROM #__clm_vereine " . " WHERE sid = " . $id;
	clm_core::$db->query($query);
	$query = "DELETE FROM #__clm_user " . "WHERE sid = '$id'";
	clm_core::$db->query($query);
	$query = "DELETE FROM #__clm_swt_liga " . "WHERE sid = '$id'";
	clm_core::$db->query($query);
	$query = "DELETE FROM #__clm_swt_mannschaften " . "WHERE sid = '$id'";
	clm_core::$db->query($query);
	$query = "DELETE FROM #__clm_swt_meldeliste_spieler " . "WHERE sid = '$id'";
	clm_core::$db->query($query);
	$query = "DELETE FROM #__clm_swt_rnd_man " . "WHERE sid = '$id'";
	clm_core::$db->query($query);
	$query = "DELETE FROM #__clm_swt_rnd_spl " . "WHERE sid = '$id'";
	clm_core::$db->query($query);
	$query = "DELETE FROM #__clm_swt_turniere " . "WHERE sid = '$id'";
	clm_core::$db->query($query);
	$query = "DELETE FROM #__clm_swt_turniere_rnd_spl " . "WHERE sid = '$id'";
	clm_core::$db->query($query);
	$query = "DELETE FROM #__clm_swt_turniere_rnd_termine " . "WHERE sid = '$id'";
	clm_core::$db->query($query);
	$query = "DELETE FROM #__clm_turniere_tlnr " . "WHERE sid = '$id'";
	clm_core::$db->query($query);
	
	return array(true,"m_deleteSeasonSuccess");
}
?>
