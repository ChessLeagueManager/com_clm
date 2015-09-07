<?php
function clm_api_db_tournament_delRounds($id, $group = true) {
	$id = clm_core::$load->make_valid($id, 0, -1);
	if($group) {
		if (clm_core::$db->liga->get($id)->isNew()) {
			return array(false, "e_teamtournamentNotExisting");
		}
		if (clm_core::$db->liga->get($id)->liga_mt == 0) {
			$right = "league";
		} else {
			$right = "teamtournament";
		}
		if ((clm_core::$db->liga->get($id)->tl != clm_core::$access->getJid() && clm_core::$access->access('BE_' . $right . '_edit_round') !== true) || (clm_core::$access->access('BE_' . $right . '_edit_round') === false)) {
			return array(false, "e_noRights");
		}
		clm_core::$api->direct("db_tournament_delDWZ", array($id, true));
		// Daten löschen
		$query = "DELETE FROM #__clm_rnd_man " . " WHERE lid = " . $id;
		clm_core::$db->query($query);
		$query = "DELETE FROM #__clm_rnd_spl " . " WHERE lid = " . $id;
		clm_core::$db->query($query);
		$query = "DELETE FROM #__clm_runden_termine " . " WHERE liga = " . $id;
		clm_core::$db->query($query);
		// Rundenbyte setzen
		clm_core::$db->liga->get($id)->rnd = 0;
		// Alle Punktsummen löschen !
		$query = " UPDATE #__clm_meldeliste_spieler " . " SET sum_saison = 0 WHERE lid = " . $id;
		clm_core::$db->query($query);
		$query = " UPDATE #__clm_mannschaften " . " SET summanpunkte = 0, sumbrettpunkte = 0, sumwins = 0, sumTiebr1 = 0, sumTiebr2 = 0, sumTiebr3 = 0 WHERE liga = " . $id;
		clm_core::$db->query($query);
	} else {
		if (clm_core::$db->turniere->get($id)->isNew()) {
			return array(false, "e_tournamentNotExisting");
		}
		if ((clm_core::$db->turniere->get($id)->tl != clm_core::$access->getJid() && clm_core::$access->access('BE_tournament_edit_round') !== true) || (clm_core::$access->access('BE_tournament_edit_round') === false)) {
			return array(false, "e_noRights");
		}
		clm_core::$api->direct("db_tournament_delDWZ", array($id, false));
		// Daten löschen
		$query = "DELETE FROM #__clm_turniere_rnd_spl " . " WHERE turnier = " . $id;
		clm_core::$db->query($query);
		$query = "DELETE FROM #__clm_turniere_rnd_termine " . " WHERE turnier = " . $id;
		clm_core::$db->query($query);
		// Rundenbyte setzen
		clm_core::$db->turniere->get($id)->rnd = 0;
		// Alle Punktsummen löschen !
		$query = " UPDATE #__clm_turniere_tlnr " . " SET sum_punkte = NULL " . " , sum_sobe = NULL, sum_bhlz = NULL, anz_spiele = '0' " . " , koStatus = '1', koRound = '0' " . " WHERE turnier = " . $id;
		clm_core::$db->query($query);
	}
	return array(true, "");
}
?>