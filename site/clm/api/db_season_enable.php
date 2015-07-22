<?php
function clm_api_db_season_enable($id = - 1) {
	$id = clm_core::$load->make_valid($id, 0, -1);
	// Nur Saisons mit einer gültigen ID und die geändert oder neu ist, kann aktiviert werden.
	if ($id != - 1 && (clm_core::$db->saison->get($id)->isChange() || !clm_core::$db->saison->get($id)->isNew())) {
		// Aktiviere die gewählte Saison
		clm_core::$db->saison->get($id)->published = 1;
		clm_core::$db->saison->get($id)->archiv = 0;
		clm_core::$db->saison->write(); // Schreibe die Änderungen
		// Archive alle anderen die bisher aktiv waren
		$sql = "UPDATE #__clm_saison SET archiv=1 WHERE published=1 AND archiv=0 AND id!=" . $id;
		clm_core::$db->query($sql);
		if (clm_core::$db->saison->get($id)->isNew() && clm_core::$access->getId() != - 1) {
			// Kopiere den aktuellen Benutzer in die neue Saison
			$user = clm_core::$db->user->get(clm_core::$access->getId());
			$sql = "REPLACE INTO #__clm_user " 
					. " (`sid`, `jid`, `name`, `username` ,`aktive` ,`email` ,`tel_fest` " 
					. " ,`tel_mobil`, `usertype`, `zps`, `mglnr` " . " ,`mannschaft`, `published`, `bemerkungen`, `bem_int` ) " 
					. ' VALUES ( ' . $id . ', ' . clm_core::$db->escape($user->jid) . ',"' . clm_core::$db->escape($user->name)
					. '", "' . clm_core::$db->escape($user->username) . '", 1, "' . clm_core::$db->escape($user->email)
					. '", "' . clm_core::$db->escape($user->tel_fest) . '" ' . ' ,"' . clm_core::$db->escape($user->tel_mobil) 
					. '", "admin", "' . clm_core::$db->escape($user->zps) . '", "' . clm_core::$db->escape($user->mglnr) . '" ' 
					. ' , 0, 1, "' . clm_core::$db->escape($user->bemerkungen) . '", "' 
					. clm_core::$db->escape($user->bem_int) . '" )';
		} else {
			$data = clm_core::$cms->getUserData();
			// Aktualisiere einen alten Benutzer oder Erstelle einen neuen in der aktuellen Saison
			$sql = "REPLACE INTO #__clm_user " . " (`sid`, `jid`, `username`, `name`, `usertype`,`aktive`,`published`, `email`) " 
					. ' VALUES ( ' . $id . ', ' 
					. clm_core::$db->escape(clm_core::$access->getJid()) . ', "' 
					. clm_core::$db->escape(clm_core::$access->getUsername()) . '", "' 
					. clm_core::$db->escape(clm_core::$access->getName()) . '", "admin",1,1, "'
					. clm_core::$db->escape($data[3]) .'")';
		}
		
		clm_core::$db->query($sql);
		clm_core::$access = new clm_class_access();
		return array(true, "m_enableSeasonSuccess");
	} else {
		return array(false, "e_noSeasonToEnable");
	}
}
?>
