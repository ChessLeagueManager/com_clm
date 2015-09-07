<?php
function clm_api_db_season_save($id = - 1, $published = null, $archiv = null, $name = null, $bemerkungen = null, $bem_int = null, $datum = null) {
	$id = clm_core::$load->make_valid($id, 0, -1);
	// Eine bereits bestehende Saison wird bearbeitet
	if ($id != - 1 && !clm_core::$db->saison->get($id)->isNew()) {
		if (is_null($published)) {
			$published = clm_core::$db->saison->get($id)->published;
		}
		if (is_null($archiv)) {
			$archiv = clm_core::$db->saison->get($id)->archiv;
		}
		if (is_null($name)) {
			$name = clm_core::$db->saison->get($id)->name;
		}
		if (is_null($bemerkungen)) {
			$bemerkungen = clm_core::$db->saison->get($id)->bemerkungen;
		}
		if (is_null($bem_int)) {
			$bem_int = clm_core::$db->saison->get($id)->bem_int;
		}
		if (is_null($datum)) {
			$datum = clm_core::$db->saison->get($id)->datum;
		}
	}
	$published = clm_core::$load->make_valid($published, 9, 0, array(0, 1));
	$archiv = clm_core::$load->make_valid($archiv, 9, 0, array(0, 1));
	$name = clm_core::$load->make_valid($name, 8, "");
	$bemerkungen = clm_core::$load->make_valid($bemerkungen, 8, "");
	$bem_int = clm_core::$load->make_valid($bem_int, 8, "");
	$datum = clm_core::$load->make_valid($datum, 8, "0000-00-00 00:00:00");
	$notice = "m_changeSeasonSuccess";
	$enableSeason = false;
	if ($name == "") {

		return array(false, "e_needSeasonName", -1);
	}
	// Neue Saison erstellen falls übergebene ID ungültig oder nicht vorhanden
	if ($id == - 1 || clm_core::$db->saison->get($id)->isNew()) {
		$id = clm_core::$db->saison->getMax() + 1;
		if ($published == 1 && $archiv == 0) {
			$enableSeason = true;
		}
	} else {
		$publishedOld = clm_core::$db->saison->get($id)->published;
		$archivOld = clm_core::$db->saison->get($id)->archiv;
		// Es gibt nur eine aktive Saison, diese kann nicht deaktiviert werden.
		if ($publishedOld == 1 && $archivOld == 0 && ($published == 0 || $archiv == 1)) {
			$published = 1;
			$archiv = 0;
			$notice = "e_lastSeason";
		}
		// Die Season war vorher nicht aktiv und wird es nun
		if (($publishedOld == 0 || $archivOld == 1) && $published == 1 && $archiv == 0) {
			$enableSeason = true;
		}
	}
	clm_core::$db->saison->get($id)->name = $name;
	clm_core::$db->saison->get($id)->bemerkungen = $bemerkungen;
	clm_core::$db->saison->get($id)->bem_int = $bem_int;
	clm_core::$db->saison->get($id)->datum = $datum;
	if ($enableSeason) {
		$out = clm_core::$api->db_season_enable($id);
		$out[2] = $id;
		return $out;
	} else {
		clm_core::$db->saison->get($id)->published = $published;
		clm_core::$db->saison->get($id)->archiv = $archiv;
		clm_core::$db->saison->write();
		// Erneuere die Rechteverwaltung
		clm_core::$access = new clm_class_access();
		return array(true, $notice, $id);
	}
}
?>
