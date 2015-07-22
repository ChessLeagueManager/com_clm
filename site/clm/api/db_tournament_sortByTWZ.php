<?php
	/**
	* sortiert die Spieler nach TWZ
	*/
	function clm_api_db_tournament_sortByTWZ($id,$group=true) {
		$id = clm_core::$load->make_valid($id, 0, -1);
		if($group) {
			// Turnier besitzt bereits Ergebnisse?
			$query = "SELECT COUNT(id) FROM `#__clm_rnd_spl`"
				." WHERE lid =".$id
				;
			$count	= clm_core::$db->count($query);
			if ($count > 0) {
				return array(false,"e_teamtournamentAlreadyStarted");
			}

			// Mannschaften Sortiert abfragen
			$noboards = (clm_core::$db->liga->get($id)->stamm)+(clm_core::$db->liga->get($id)->ersatz);
			$sid	= clm_core::$db->liga->get($id)->sid;
			$teil	= clm_core::$db->liga->get($id)->teil;

			$query = " SELECT m.tln_nr, m.id, AVG(d.DWZ) as twz "
				." FROM #__clm_mannschaften AS m "
				." LEFT JOIN #__clm_meldeliste_spieler AS a ON a.sid = m.sid AND a.lid = m.liga AND (a.zps = m.zps OR FIND_IN_SET(a.zps,m.sg_zps) != 0) AND a.mnr = m.man_nr "
				." LEFT JOIN #__clm_dwz_spieler AS d ON d.sid = a.sid AND d.DWZ !=0 AND d.Mgl_Nr = a.mgl_nr AND d.ZPS = a.zps "
				." WHERE m.liga = ".$id
				." AND m.sid = ".$sid
				." AND a.snr < ".($noboards+1)
				." GROUP BY m.tln_nr"
				." ORDER BY twz DESC, tln_nr ASC";
			$teams	= clm_core::$db->loadObjectList($query);
			if (count($teams)==0) {
				return array(false,"e_teamtournamentNotTeamFound");
			}
		
			// Sortierung Speichern
			$tlnr = 0;
			foreach ($teams as $value) {
				$tlnr++;
				clm_core::$db->liga->get($value->id)->tln_nr=$tlnr;
			}
		
			clm_core::$api->direct("db_tournament_ranking",array($id));
			
		} else {
			// noch nicht umgezogen
		}
		return array(true,"");
	}
