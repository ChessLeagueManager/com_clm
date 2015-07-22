<?php

/**
 * Aktualiserung der Datenbank auf die neueste Version
 * Dadurch wird die DB konsistent gehalten
 *
 * Alle Änderungen ab 1.2.0
 * @return gibt Fehler
 *
 */
	//function dbupgrade($collation) {

		// UTF8-Test
		$utf8 = SimpleClmInstaller::_isUtf8($collation);
		
		$engine = '';   // old: ENGINE=MyISAM

		$database = SimpleClmInstaller::_getDB();
		$string = '';
		//echo '<br>Zu installierende CLM-Version = ' . NEW_CLM_VERSION;
		//echo '<br>Vorhandene CLM-Version = ' . OLD_CLM_VERSION;

		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_liga
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_liga");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}
		// 1.2.3
		if (!isset($fieldtypes['params'])) {
			$sql = "ALTER TABLE `#__clm_liga` ADD `params` text not null;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte params zu Tabelle clm_liga hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Spalte params zu Tabelle clm_liga</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
				
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_turniere_tlnr
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_turniere_tlnr");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}
		//1.2.5
		if (!isset($fieldtypes['tlnrStatus'])) {
			$sql = "ALTER TABLE `#__clm_turniere_tlnr`
						ADD `tlnrStatus` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' AFTER `rankingPos`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte tlnrStatus in Tabelle clm_turniere_tlnr hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler bei Hinzufügen der Spalte tlnrStatus in Tabelle clm_turniere_tlnr</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['anz_spiele'])) {
			$sql = "ALTER TABLE `#__clm_turniere_tlnr`
						ADD `anz_spiele` TINYINT(2) UNSIGNED NOT NULL DEFAULT '0' AFTER `tlnrStatus`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte anz_spiele in Tabelle clm_turniere_tlnr hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler bei Hinzufügen der Spalte anz_spiele in Tabelle clm_turniere_tlnr</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		// 1.5.7
		if  ($fieldtypes['sumTiebr1'] != 'decimal(8,3)') { 
			$sql = "ALTER TABLE `#__clm_turniere_tlnr` CHANGE `sumTiebr1` `sumTiebr1` decimal(8,3) DEFAULT NULL "; 
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sumTiebr1 updated in Tabelle clm_turniere_tlnr<br />";
			} else {
				echo "<font color='red'>* Fehler beim Update der Spalte sumTiebr1  in Tabelle clm_turniere_tlnr</font><br />";
				SimpleClmInstaller::_debugDB($fielddefaults);
				return false;
			}
		}
		if  ($fieldtypes['sumTiebr2'] != 'decimal(8,3)') { 
			$sql = "ALTER TABLE `#__clm_turniere_tlnr` CHANGE `sumTiebr2` `sumTiebr2` decimal(8,3) DEFAULT NULL "; 
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sumTiebr2 updated in Tabelle clm_turniere_tlnr<br />";
			} else {
				echo "<font color='red'>* Fehler beim Update der Spalte sumTiebr2  in Tabelle clm_turniere_tlnr</font><br />";
				SimpleClmInstaller::_debugDB($fielddefaults);
				return false;
			}
		}
		if  ($fieldtypes['sumTiebr3'] != 'decimal(8,3)') { 
			$sql = "ALTER TABLE `#__clm_turniere_tlnr` CHANGE `sumTiebr3` `sumTiebr3` decimal(8,3) DEFAULT NULL "; 
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sumTiebr3 updated in Tabelle clm_turniere_tlnr<br />";
			} else {
				echo "<font color='red'>* Fehler beim Update der Spalte sumTiebr3  in Tabelle clm_turniere_tlnr</font><br />";
				SimpleClmInstaller::_debugDB($fielddefaults);
				return false;
			}
		}

		$fielddefaults = array();
		foreach ($fields as $field) {
			$fielddefaults[$field->Field] = $field->Default;
		}	 
		// 1.5.5
		if  ($fielddefaults['sumTiebr1'] != NULL) { 
			$sql = "ALTER TABLE `#__clm_turniere_tlnr` CHANGE `sumTiebr1` `sumTiebr1` decimal(8,3) DEFAULT NULL "; 
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sumTiebr1 updated in Tabelle clm_turniere_tlnr<br />";
			} else {
				echo "<font color='red'>* Fehler beim Update der Spalte sumTiebr1  in Tabelle clm_turniere_tlnr</font><br />";
				SimpleClmInstaller::_debugDB($fielddefaults);
				return false;
			}
		}
		if  ($fielddefaults['sumTiebr2'] != NULL) { 
			$sql = "ALTER TABLE `#__clm_turniere_tlnr` CHANGE `sumTiebr2` `sumTiebr2` decimal(8,3) DEFAULT NULL "; 
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sumTiebr2 updated in Tabelle clm_turniere_tlnr<br />";
			} else {
				echo "<font color='red'>* Fehler beim Update der Spalte sumTiebr2  in Tabelle clm_turniere_tlnr</font><br />";
				SimpleClmInstaller::_debugDB($fielddefaults);
				return false;
			}
		}
		if  ($fielddefaults['sumTiebr3'] != NULL) { 
			$sql = "ALTER TABLE `#__clm_turniere_tlnr` CHANGE `sumTiebr3` `sumTiebr3` decimal(8,3) DEFAULT NULL "; 
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sumTiebr3 updated in Tabelle clm_turniere_tlnr<br />";
			} else {
				echo "<font color='red'>* Fehler beim Update der Spalte sumTiebr3  in Tabelle clm_turniere_tlnr</font><br />";
				SimpleClmInstaller::_debugDB($fielddefaults);
				return false;
			}
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_mannschaften                           
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_mannschaften");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}
		// 1.2.6
		if (!isset($fieldtypes['sname'])) {
			$sql = "ALTER TABLE `#__clm_mannschaften` ADD `sname` varchar(20) default '' AFTER `rankingpos`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sname hinzugefügt in Tabelle clm_mannschaften<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Spalte sname in Tabelle clm_mannschaften</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		// 1.4.2
		if  ($fieldtypes['sg_zps'] != 'varchar(120)') { 
			$sql = "ALTER TABLE `#__clm_mannschaften` CHANGE `sg_zps` `sg_zps` varchar( 120 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL "; 
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sg_zps updated in Tabelle clm_mannschaften<br />";
			} else {
				echo "<font color='red'>* Fehler beim Update der Spalte sg_zps  in Tabelle clm_mannschaften</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		// 1.5.1
		if (!isset($fieldtypes['abzug'])) {
			$sql = "ALTER TABLE `#__clm_mannschaften` ADD `abzug` TINYINT(2) NOT NULL default '0' AFTER `sname`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte abzug hinzugefügt in Tabelle clm_mannschaften<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Spalte abzug in Tabelle clm_mannschaften</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}

		$fielddefaults = array();
		foreach ($fields as $field) {
			$fielddefaults[$field->Field] = $field->Default;
		}	 
		// 1.5.5
		if  ($fielddefaults['sumtiebr1'] != NULL) { 
			$sql = "ALTER TABLE `#__clm_mannschaften` CHANGE `sumtiebr1` `sumtiebr1` decimal(6,3) DEFAULT NULL "; 
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sumtiebr1 updated in Tabelle clm_mannschaften<br />";
			} else {
				echo "<font color='red'>* Fehler beim Update der Spalte sumtiebr1  in Tabelle clm_mannschaften</font><br />";
				SimpleClmInstaller::_debugDB($fielddefaults);
				return false;
			}
		}
		if  ($fielddefaults['sumtiebr2'] != NULL) { 
			$sql = "ALTER TABLE `#__clm_mannschaften` CHANGE `sumtiebr2` `sumtiebr2` decimal(6,3) DEFAULT NULL "; 
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sumtiebr2 updated in Tabelle clm_mannschaften<br />";
			} else {
				echo "<font color='red'>* Fehler beim Update der Spalte sumtiebr2  in Tabelle clm_mannschaften</font><br />";
				SimpleClmInstaller::_debugDB($fielddefaults);
				return false;
			}
		}
		if  ($fielddefaults['sumtiebr3'] != NULL) { 
			$sql = "ALTER TABLE `#__clm_mannschaften` CHANGE `sumtiebr3` `sumtiebr3` decimal(6,3) DEFAULT NULL "; 
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sumtiebr3 updated in Tabelle clm_mannschaften<br />";
			} else {
				echo "<font color='red'>* Fehler beim Update der Spalte sumtiebr3  in Tabelle clm_mannschaften</font><br />";
				SimpleClmInstaller::_debugDB($fielddefaults);
				return false;
			}
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_todo löschen (ab v 1.1.13)
		// ---------------------------------------------------------------------------
		//Tabelle wird gelöscht
		$sql = "DROP TABLE IF EXISTS #__clm_todo ;";
		$database->setQuery($sql);
		if ( !$database->query() ) {
			echo "Fehler beim Löschen Tabelle clm_todo";
			return false;
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_user                            
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_user");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}	 
		// 1.2.2
		if (!isset($fieldtypes['mglnr'])) {
			$sql = "ALTER TABLE `#__clm_user` ADD `mglnr` varchar(5) DEFAULT NULL AFTER `zps`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte mglnr hinzugefügt in Tabelle clm_user<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Spalte mglnr in Tabelle clm_user</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_access_point (neu mit v 1.2.4)
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_access_points");
		$fields = $database->loadObjectList();
		if ( !count($fields) ) {
			//Tabelle wird neu angelegt
			$sql = "CREATE TABLE IF NOT EXISTS `#__clm_access_points` (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`area` char(2) NOT NULL DEFAULT 'BE',
					`accesstopic` varchar(20) NOT NULL,
					`accesspoint` varchar(20) NOT NULL,
					`rule` char(3) NOT NULL DEFAULT 'NY',
					`published` int(1) NOT NULL DEFAULT '0',
					`ordering` int(11) NOT NULL DEFAULT '0',
					PRIMARY KEY (`id`)
				) ". $engine . $utf8 . ";";
			$database->setQuery($sql);
			if ( !$database->query() ) {
				echo "Fehler beim Anlegen Tabelle clm_access_points";
				return false;
			} else {
				$string .= "* Tabelle clm_access_points hinzugefügt<br />";
			}
		}
		
		$sql = "REPLACE INTO `#__clm_access_points` (`id`, `area`, `accesstopic`, `accesspoint`, `rule`, `published`, `ordering`) VALUES
			(1, 'BE', 'general', 'general', 'NY', 1, 1),
			(2, 'BE', 'season', 'general', 'NY', 1, 11),
			(3, 'BE', 'event', 'general', 'NY', 1, 21),
			(4, 'BE', 'event', 'delete', 'NY', 1, 24),
			(5, 'BE', 'tournament', 'general', 'NY', 1, 31),
			(6, 'BE', 'tournament', 'create', 'NY', 1, 32),
			(7, 'BE', 'tournament', 'edit_detail', 'NYT', 1, 33),
			(8, 'BE', 'tournament', 'delete', 'NY', 1, 34),
			(9, 'BE', 'tournament', 'edit_round', 'NYT', 1, 35),
			(10, 'BE', 'tournament', 'edit_result', 'NYT', 1, 36),
			(11, 'BE', 'tournament', 'edit_fixture', 'NYT', 1, 37),
			(12, 'BE', 'league', 'general', 'NY', 1, 41),
			(13, 'BE', 'league', 'create', 'NY', 1, 42),
			(14, 'BE', 'league', 'edit_detail', 'NYL', 1, 43),
			(15, 'BE', 'league', 'delete', 'NY', 1, 44),
			(16, 'BE', 'league', 'edit_round', 'NYL', 1, 45),
			(17, 'BE', 'league', 'edit_result', 'NYL', 1, 46),
			(18, 'BE', 'league', 'edit_fixture', 'NYL', 1, 47),
			(19, 'BE', 'teamtournament', 'general', 'NY', 1, 51),
			(20, 'BE', 'teamtournament', 'create', 'NY', 1, 52),
			(21, 'BE', 'teamtournament', 'edit_detail', 'NYT', 1, 53),
			(22, 'BE', 'teamtournament', 'delete', 'NY', 1, 54),
			(23, 'BE', 'teamtournament', 'edit_round', 'NYT', 1, 55),
			(24, 'BE', 'teamtournament', 'edit_result', 'NYT', 1, 56),
			(25, 'BE', 'teamtournament', 'edit_fixture', 'NYT', 1, 57),
			(26, 'BE', 'club', 'general', 'NY', 1, 61),
			(27, 'BE', 'club', 'create', 'NY', 1, 62),
			(28, 'BE', 'club', 'edit_member', 'NY', 1, 63),
			(29, 'BE', 'club', 'copy', 'NY', 1, 64),
			(30, 'BE', 'club', 'edit_ranking', 'NY', 1, 65),
			(31, 'BE', 'team', 'general', 'NY', 1, 71),
			(32, 'BE', 'team', 'create', 'NY', 1, 72),
			(33, 'BE', 'team', 'edit', 'NYL', 1, 73),
			(34, 'BE', 'team', 'delete', 'NY', 1, 74),
			(35, 'BE', 'team', 'registration_list', 'NYL', 1, 75),
			(36, 'BE', 'user', 'general', 'NY', 1, 81),
			(37, 'BE', 'user', 'copy', 'NY', 1, 84),
			(38, 'BE', 'accessgroup', 'general', 'NY', 1, 85),
			(39, 'BE', 'swt', 'general', 'NY', 1, 91),
			(40, 'BE', 'elobase', 'general', 'NY', 1, 101),
			(41, 'BE', 'database', 'general', 'NY', 1, 111),
			(42, 'BE', 'logfile', 'general', 'NY', 1, 121),
			(43, 'BE', 'logfile', 'delete', 'NY', 1, 122),
			(44, 'BE', 'config', 'general', 'NY', 1, 131);";
		$database->setQuery($sql);
		if ( $database->query() ) {
			$string .= "* CLM-Berechtigungen (Standard) geladen in Tabelle clm_access_points<br />";
		} else {
			echo "<font color='red'>* Fehler bei Laden der CLM-Berechtigungen (Standard)in Tabelle clm_access_pointse</font><br />";
			SimpleClmInstaller::_debugDB($fieldtypes);
			return false;
		}

		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_usertype
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_usertype");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}

		// 1.2.4
		if (!isset($fieldtypes['usertype'])) {
			$sql = "ALTER TABLE `#__clm_usertype` CHANGE `group` `usertype` VARCHAR( 15 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0'";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte group in Tabelle clm_usertype umbenannt in usertype<br />";
			} else {
				echo "<font color='red'>* Fehler bei Umbenennen Spalte group in Tabelle clm_usertype</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		// 1.2.4
		if (!isset($fieldtypes['kind'])) {
			$sql = "ALTER TABLE `#__clm_usertype` ADD `kind` VARCHAR( 4 ) NOT NULL DEFAULT 'USER' AFTER `usertype`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte kind in Tabelle clm_usertype hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler bei Hinzufügen Spalte kind in Tabelle clm_usertype</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		// 1.2.4
		if (!isset($fieldtypes['published'])) {
			$sql = "ALTER TABLE `#__clm_usertype` ADD `published` INT( 1 ) NOT NULL DEFAULT '0' AFTER `kind`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte published in Tabelle clm_usertype hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler bei Hinzufügen Spalte published in Tabelle clm_usertype</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		// 1.2.4
		if (!isset($fieldtypes['fe_params'])) {
			$sql = "ALTER TABLE `#__clm_usertype` ADD `fe_params` TEXT NOT NULL AFTER `ordering`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte fe_params in Tabelle clm_usertype hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler bei Hinzufügen Spalte fe_params in Tabelle clm_usertype</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		// 1.2.4
		if (!isset($fieldtypes['be_params'])) {
			$sql = "ALTER TABLE `#__clm_usertype` ADD `be_params` TEXT NOT NULL AFTER `fe_params`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte be_params in Tabelle clm_usertype hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler bei Hinzufügen Spalte be_params in Tabelle clm_usertype</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		 
		$sql = "REPLACE INTO `#__clm_usertype` (`id`, `name`, `user_clm`, `usertype`, `kind`, `published`, `ordering`, `fe_params`, `be_params`) VALUES
			(1, 'Administrator', 100, 'admin', 'CLM', 1, 0, '', 'BE_general_general=1\nBE_season_general=1\nBE_event_general=1\nBE_event_delete=1\nBE_tournament_general=1\nBE_tournament_create=1\nBE_tournament_edit_detail=1\nBE_tournament_delete=1\nBE_tournament_edit_round=1\nBE_tournament_edit_result=1\nBE_tournament_edit_fixture=1\nBE_league_general=1\nBE_league_create=1\nBE_league_edit_detail=1\nBE_league_delete=1\nBE_league_edit_round=1\nBE_league_edit_result=1\nBE_league_edit_fixture=1\nBE_teamtournament_general=1\nBE_teamtournament_create=1\nBE_teamtournament_edit_detail=1\nBE_teamtournament_delete=1\nBE_teamtournament_edit_round=1\nBE_teamtournament_edit_result=1\nBE_teamtournament_edit_fixture=1\nBE_club_general=1\nBE_club_create=1\nBE_club_edit_member=1\nBE_club_copy=1\nBE_club_edit_ranking=1\nBE_team_general=1\nBE_team_create=1\nBE_team_edit=1\nBE_team_delete=1\nBE_team_registration_list=1\nBE_user_general=1\nBE_user_copy=1\nBE_accessgroup_general=1\nBE_swt_general=1\nBE_elobase_general=1\nBE_database_general=1\nBE_logfile_general=1\nBE_logfile_delete=1\nBE_config_general=1'),
			(2, 'DV Referent', 90, 'dv', 'CLM', 1, 1, '', 'BE_general_general=1\nBE_season_general=0\nBE_event_general=1\nBE_tournament_general=1\nBE_tournament_create=1\nBE_tournament_edit_detail=1\nBE_tournament_delete=1\nBE_tournament_edit_round=1\nBE_tournament_edit_result=1\nBE_tournament_edit_fixture=1\nBE_league_general=1\nBE_league_create=1\nBE_league_edit_detail=1\nBE_league_delete=1\nBE_league_edit_round=1\nBE_league_edit_result=1\nBE_league_edit_fixture=1\nBE_teamtournament_general=1\nBE_teamtournament_create=1\nBE_teamtournament_edit_detail=1\nBE_teamtournament_delete=1\nBE_teamtournament_edit_round=2\nBE_teamtournament_edit_result=2\nBE_teamtournament_edit_fixture=2\nBE_club_general=1\nBE_club_create=1\nBE_club_edit_member=1\nBE_club_copy=0\nBE_team_general=1\nBE_team_create=1\nBE_team_edit=0\nBE_team_delete=0\nBE_team_registration_list=1\nBE_user_general=1\nBE_user_copy=0\nBE_accessgroup_general=0\nBE_swt_general=0\nBE_elobase_general=0\nBE_database_general=0\nBE_logfile_general=1\nBE_logfile_delete=0\nBE_config_general=0'),
			(3, 'Spielleiter', 89, 'dv1', 'CLM', 1, 2, '', 'BE_general_general=1\nBE_season_general=0\nBE_event_general=1\nBE_tournament_general=1\nBE_tournament_create=1\nBE_tournament_edit_detail=1\nBE_tournament_delete=1\nBE_tournament_edit_round=1\nBE_tournament_edit_result=1\nBE_tournament_edit_fixture=1\nBE_league_general=1\nBE_league_create=1\nBE_league_edit_detail=1\nBE_league_delete=1\nBE_league_edit_round=1\nBE_league_edit_result=1\nBE_league_edit_fixture=1\nBE_teamtournament_general=1\nBE_teamtournament_create=1\nBE_teamtournament_edit_detail=1\nBE_teamtournament_delete=1\nBE_teamtournament_edit_round=1\nBE_teamtournament_edit_result=1\nBE_teamtournament_edit_fixture=1\nBE_club_general=1\nBE_club_create=1\nBE_club_edit_member=1\nBE_team_general=1\nBE_team_create=1\nBE_team_registration_list=1\nBE_user_general=0\nBE_accessgroup_general=0\nBE_swt_general=0\nBE_elobase_general=0\nBE_database_general=0\nBE_logfile_general=1\nBE_logfile_delete=0\nBE_config_general=0'),
			(4, 'DWZ Referent', 80, 'dwz', 'CLM', 1, 4, '', 'BE_general_general=1\nBE_season_general=0\nBE_event_general=1\nBE_tournament_general=1\nBE_tournament_create=1\nBE_tournament_edit_detail=1\nBE_tournament_delete=1\nBE_tournament_edit_round=1\nBE_tournament_edit_result=1\nBE_tournament_edit_fixture=1\nBE_league_general=1\nBE_league_create=1\nBE_league_edit_detail=1\nBE_league_delete=1\nBE_league_edit_round=1\nBE_league_edit_result=1\nBE_league_edit_fixture=1\nBE_teamtournament_general=1\nBE_teamtournament_create=1\nBE_teamtournament_edit_detail=1\nBE_teamtournament_delete=1\nBE_teamtournament_edit_round=2\nBE_teamtournament_edit_result=2\nBE_teamtournament_edit_fixture=2\nBE_club_general=1\nBE_club_create=1\nBE_club_edit_member=1\nBE_team_general=1\nBE_team_create=1\nBE_team_registration_list=1\nBE_user_general=0\nBE_accessgroup_general=0\nBE_swt_general=1\nBE_elobase_general=1\nBE_database_general=1\nBE_logfile_general=1\nBE_logfile_delete=0\nBE_config_general=0'),
			(5, 'Turnier- und Staffelleiter', 75, 'tsl', 'CLM', 1, 8, '', 'BE_general_general=1\nBE_season_general=0\nBE_event_general=1\nBE_tournament_general=1\nBE_tournament_create=0\nBE_tournament_edit_detail=2\nBE_tournament_delete=0\nBE_tournament_edit_round=2\nBE_tournament_edit_result=2\nBE_tournament_edit_fixture=2\nBE_league_general=1\nBE_league_create=0\nBE_league_edit_detail=2\nBE_league_delete=0\nBE_league_edit_round=2\nBE_league_edit_result=2\nBE_league_edit_fixture=2\nBE_teamtournament_general=1\nBE_teamtournament_create=0\nBE_teamtournament_edit_detail=2\nBE_teamtournament_delete=0\nBE_teamtournament_edit_round=2\nBE_teamtournament_edit_result=2\nBE_teamtournament_edit_fixture=2\nBE_club_general=1\nBE_club_create=0\nBE_club_edit_member=0\nBE_team_general=1\nBE_team_create=0\nBE_team_edit=2\nBE_team_delete=0\nBE_team_registration_list=2\nBE_user_general=0\nBE_accessgroup_general=0\nBE_swt_general=0\nBE_elobase_general=0\nBE_database_general=0\nBE_logfile_general=1\nBE_logfile_delete=0\nBE_config_general=0'),
			(6, 'TL für Mannschaftsturniere', 72, 'mtl', 'CLM', 1, 9, '', 'BE_general_general=1\nBE_season_general=0\nBE_event_general=1\nBE_tournament_general=0\nBE_tournament_create=0\nBE_tournament_edit_detail=0\nBE_tournament_delete=0\nBE_tournament_edit_round=0\nBE_tournament_edit_result=0\nBE_tournament_edit_fixture=0\nBE_league_general=0\nBE_league_create=0\nBE_league_edit_detail=0\nBE_league_delete=0\nBE_league_edit_round=0\nBE_league_edit_result=0\nBE_league_edit_fixture=0\nBE_teamtournament_general=1\nBE_teamtournament_create=0\nBE_teamtournament_edit_detail=2\nBE_teamtournament_delete=0\nBE_teamtournament_edit_round=2\nBE_teamtournament_edit_result=2\nBE_teamtournament_edit_fixture=2\nBE_club_general=0\nBE_club_create=0\nBE_club_edit_member=0\nBE_team_general=1\nBE_team_create=0\nBE_team_edit=2\nBE_team_delete=0\nBE_team_registration_list=2\nBE_user_general=0\nBE_accessgroup_general=0\nBE_swt_general=0\nBE_elobase_general=0\nBE_database_general=0\nBE_logfile_general=1\nBE_logfile_delete=0\nBE_config_general=0'),
			(7, 'Staffelleiter', 70, 'sl', 'CLM', 1, 10, '', 'BE_general_general=1\nBE_season_general=0\nBE_event_general=1\nBE_tournament_general=0\nBE_tournament_create=0\nBE_tournament_edit_detail=0\nBE_tournament_delete=0\nBE_tournament_edit_round=0\nBE_tournament_edit_result=0\nBE_tournament_edit_fixture=0\nBE_league_general=1\nBE_league_create=0\nBE_league_edit_detail=2\nBE_league_delete=0\nBE_league_edit_round=2\nBE_league_edit_result=2\nBE_league_edit_fixture=2\nBE_teamtournament_general=0\nBE_teamtournament_create=0\nBE_teamtournament_edit_detail=0\nBE_teamtournament_delete=0\nBE_teamtournament_edit_round=0\nBE_teamtournament_edit_result=0\nBE_teamtournament_edit_fixture=0\nBE_club_general=0\nBE_club_create=0\nBE_club_edit_member=0\nBE_team_general=1\nBE_team_create=0\nBE_team_edit=2\nBE_team_delete=0\nBE_team_registration_list=2\nBE_user_general=0\nBE_accessgroup_general=0\nBE_swt_general=0\nBE_elobase_general=0\nBE_database_general=0\nBE_logfile_general=1\nBE_logfile_delete=0\nBE_config_general=0'),
			(8, 'Staffelleiter II', 71, 'sl2', 'CLM', 1, 11, '', 'BE_general_general=1\nBE_season_general=0\nBE_event_general=1\nBE_tournament_general=0\nBE_tournament_create=0\nBE_tournament_edit_detail=0\nBE_tournament_delete=0\nBE_tournament_edit_round=0\nBE_tournament_edit_result=0\nBE_tournament_edit_fixture=0\nBE_league_general=1\nBE_league_create=0\nBE_league_edit_detail=0\nBE_league_delete=0\nBE_league_edit_round=0\nBE_league_edit_result=2\nBE_league_edit_fixture=0\nBE_teamtournament_general=0\nBE_teamtournament_create=0\nBE_teamtournament_edit_detail=0\nBE_teamtournament_delete=0\nBE_teamtournament_edit_round=0\nBE_teamtournament_edit_result=0\nBE_teamtournament_edit_fixture=0\nBE_club_general=0\nBE_club_create=0\nBE_club_edit_member=0\nBE_team_general=0\nBE_team_create=0\nBE_team_registration_list=0\nBE_user_general=0\nBE_accessgroup_general=0\nBE_swt_general=0\nBE_elobase_general=0\nBE_database_general=0\nBE_logfile_general=0\nBE_logfile_delete=0\nBE_config_general=0'),
			(9, 'Turnierleiter', 69, 'tl', 'CLM', 1, 15, '', 'BE_general_general=1\nBE_season_general=0\nBE_event_general=1\nBE_tournament_general=1\nBE_tournament_create=0\nBE_tournament_edit_detail=2\nBE_tournament_delete=0\nBE_tournament_edit_round=2\nBE_tournament_edit_result=2\nBE_tournament_edit_fixture=2\nBE_league_general=0\nBE_league_create=0\nBE_league_edit_detail=0\nBE_league_delete=0\nBE_league_edit_round=0\nBE_league_edit_result=0\nBE_league_edit_fixture=0\nBE_teamtournament_general=0\nBE_teamtournament_create=0\nBE_teamtournament_edit_detail=0\nBE_teamtournament_delete=0\nBE_teamtournament_edit_round=0\nBE_teamtournament_edit_result=0\nBE_teamtournament_edit_fixture=0\nBE_club_general=0\nBE_club_create=0\nBE_club_edit_member=0\nBE_team_general=0\nBE_team_create=0\nBE_team_edit=2\nBE_team_delete=0\nBE_team_registration_list=0\nBE_user_general=0\nBE_accessgroup_general=0\nBE_swt_general=0\nBE_elobase_general=0\nBE_database_general=0\nBE_logfile_general=0\nBE_logfile_delete=0\nBE_config_general=0'),
			(10, 'Damenwart', 68, 'dw', 'CLM', 1, 20, '', 'BE_general_general=0\nBE_season_general=0\nBE_event_general=0\nBE_tournament_general=0\nBE_tournament_create=0\nBE_tournament_edit_detail=0\nBE_tournament_delete=0\nBE_tournament_edit_round=0\nBE_tournament_edit_result=0\nBE_tournament_edit_fixture=0\nBE_league_general=0\nBE_league_create=0\nBE_league_edit_detail=0\nBE_league_delete=0\nBE_league_edit_round=0\nBE_league_edit_result=0\nBE_league_edit_fixture=0\nBE_teamtournament_general=0\nBE_teamtournament_create=0\nBE_teamtournament_edit_detail=0\nBE_teamtournament_delete=0\nBE_teamtournament_edit_round=0\nBE_teamtournament_edit_result=0\nBE_teamtournament_edit_fixture=0\nBE_club_general=0\nBE_club_create=0\nBE_club_edit_member=0\nBE_team_general=0\nBE_team_create=0\nBE_team_registration_list=0\nBE_user_general=0\nBE_accessgroup_general=0\nBE_swt_general=0\nBE_elobase_general=0\nBE_database_general=0\nBE_logfile_general=0\nBE_logfile_delete=0\nBE_config_general=0'),
			(11, 'Jugendwart', 67, 'jw', 'CLM', 1, 21, '', 'BE_general_general=0\nBE_season_general=0\nBE_event_general=0\nBE_tournament_general=0\nBE_tournament_create=0\nBE_tournament_edit_detail=0\nBE_tournament_delete=0\nBE_tournament_edit_round=0\nBE_tournament_edit_result=0\nBE_tournament_edit_fixture=0\nBE_league_general=0\nBE_league_create=0\nBE_league_edit_detail=0\nBE_league_delete=0\nBE_league_edit_round=0\nBE_league_edit_result=0\nBE_league_edit_fixture=0\nBE_teamtournament_general=0\nBE_teamtournament_create=0\nBE_teamtournament_edit_detail=0\nBE_teamtournament_delete=0\nBE_teamtournament_edit_round=0\nBE_teamtournament_edit_result=0\nBE_teamtournament_edit_fixture=0\nBE_club_general=0\nBE_club_create=0\nBE_club_edit_member=0\nBE_team_general=0\nBE_team_create=0\nBE_team_registration_list=0\nBE_user_general=0\nBE_accessgroup_general=0\nBE_swt_general=0\nBE_elobase_general=0\nBE_database_general=0\nBE_logfile_general=0\nBE_logfile_delete=0\nBE_config_general=0'),
			(12, 'Vereinsspielleiter', 60, 'vtl', 'CLM', 1, 31, '', 'BE_general_general=0\nBE_season_general=0\nBE_event_general=0\nBE_tournament_general=0\nBE_tournament_create=0\nBE_tournament_edit_detail=0\nBE_tournament_delete=0\nBE_tournament_edit_round=0\nBE_tournament_edit_result=0\nBE_tournament_edit_fixture=0\nBE_league_general=0\nBE_league_create=0\nBE_league_edit_detail=0\nBE_league_delete=0\nBE_league_edit_round=0\nBE_league_edit_result=0\nBE_league_edit_fixture=0\nBE_teamtournament_general=0\nBE_teamtournament_create=0\nBE_teamtournament_edit_detail=0\nBE_teamtournament_delete=0\nBE_teamtournament_edit_round=0\nBE_teamtournament_edit_result=0\nBE_teamtournament_edit_fixture=0\nBE_club_general=0\nBE_club_create=0\nBE_club_edit_member=0\nBE_team_general=0\nBE_team_create=0\nBE_team_registration_list=0\nBE_user_general=0\nBE_accessgroup_general=0\nBE_swt_general=0\nBE_elobase_general=0\nBE_database_general=0\nBE_logfile_general=0\nBE_logfile_delete=0\nBE_config_general=0'),
			(13, 'Vereinsleiter', 50, 'vl', 'CLM', 1, 30, '', 'BE_general_general=0\nBE_season_general=0\nBE_event_general=0\nBE_tournament_general=0\nBE_tournament_create=0\nBE_tournament_edit_detail=0\nBE_tournament_delete=0\nBE_tournament_edit_round=0\nBE_tournament_edit_result=0\nBE_tournament_edit_fixture=0\nBE_league_general=0\nBE_league_create=0\nBE_league_edit_detail=0\nBE_league_delete=0\nBE_league_edit_round=0\nBE_league_edit_result=0\nBE_league_edit_fixture=0\nBE_teamtournament_general=0\nBE_teamtournament_create=0\nBE_teamtournament_edit_detail=0\nBE_teamtournament_delete=0\nBE_teamtournament_edit_round=0\nBE_teamtournament_edit_result=0\nBE_teamtournament_edit_fixture=0\nBE_club_general=0\nBE_club_create=0\nBE_club_edit_member=0\nBE_team_general=0\nBE_team_create=0\nBE_team_registration_list=0\nBE_user_general=0\nBE_accessgroup_general=0\nBE_swt_general=0\nBE_elobase_general=0\nBE_database_general=0\nBE_logfile_general=0\nBE_logfile_delete=0\nBE_config_general=0'),
			(14, 'Vereinsjugendwart', 40, 'vjw', 'CLM', 1, 32, '', 'BE_general_general=0\nBE_season_general=0\nBE_event_general=0\nBE_tournament_general=0\nBE_tournament_create=0\nBE_tournament_edit_detail=0\nBE_tournament_delete=0\nBE_tournament_edit_round=0\nBE_tournament_edit_result=0\nBE_tournament_edit_fixture=0\nBE_league_general=0\nBE_league_create=0\nBE_league_edit_detail=0\nBE_league_delete=0\nBE_league_edit_round=0\nBE_league_edit_result=0\nBE_league_edit_fixture=0\nBE_teamtournament_general=0\nBE_teamtournament_create=0\nBE_teamtournament_edit_detail=0\nBE_teamtournament_delete=0\nBE_teamtournament_edit_round=0\nBE_teamtournament_edit_result=0\nBE_teamtournament_edit_fixture=0\nBE_club_general=0\nBE_club_create=0\nBE_club_edit_member=0\nBE_team_general=0\nBE_team_create=0\nBE_team_registration_list=0\nBE_user_general=0\nBE_accessgroup_general=0\nBE_swt_general=0\nBE_elobase_general=0\nBE_database_general=0\nBE_logfile_general=0\nBE_logfile_delete=0\nBE_config_general=0'),
			(15, 'Vereinsdamenwart', 39, 'vdw', 'CLM', 1, 33, '', 'BE_general_general=0\nBE_season_general=0\nBE_event_general=0\nBE_tournament_general=0\nBE_tournament_create=0\nBE_tournament_edit_detail=0\nBE_tournament_delete=0\nBE_tournament_edit_round=0\nBE_tournament_edit_result=0\nBE_tournament_edit_fixture=0\nBE_league_general=0\nBE_league_create=0\nBE_league_edit_detail=0\nBE_league_delete=0\nBE_league_edit_round=0\nBE_league_edit_result=0\nBE_league_edit_fixture=0\nBE_teamtournament_general=0\nBE_teamtournament_create=0\nBE_teamtournament_edit_detail=0\nBE_teamtournament_delete=0\nBE_teamtournament_edit_round=0\nBE_teamtournament_edit_result=0\nBE_teamtournament_edit_fixture=0\nBE_club_general=0\nBE_club_create=0\nBE_club_edit_member=0\nBE_team_general=0\nBE_team_create=0\nBE_team_registration_list=0\nBE_user_general=0\nBE_accessgroup_general=0\nBE_swt_general=0\nBE_elobase_general=0\nBE_database_general=0\nBE_logfile_general=0\nBE_logfile_delete=0\nBE_config_general=0'),
			(16, 'Mannschaftsführer', 30, 'mf', 'CLM', 1, 40, '', 'BE_general_general=0\nBE_season_general=0\nBE_event_general=0\nBE_tournament_general=0\nBE_tournament_create=0\nBE_league_general=0\nBE_league_create=0\nBE_league_edit_detail=0\nBE_league_delete=0\nBE_league_edit_round=0\nBE_league_edit_result=0\nBE_league_edit_fixture=0\nBE_teamtournament_general=0\nBE_teamtournament_create=0\nBE_club_general=0\nBE_club_create=0\nBE_club_edit_member=0\nBE_team_general=0\nBE_team_create=0\nBE_team_registration_list=0\nBE_user_general=0\nBE_swt_general=0\nBE_elobase_general=0\nBE_database_general=0\nBE_logfile_general=0\nBE_config_general=0'),
			(17, 'Spieler', 20, 'spl', 'CLM', 1, 50, '', 'BE_general_general=0\nBE_season_general=0\nBE_event_general=0\nBE_tournament_general=0\nBE_tournament_create=0\nBE_league_general=0\nBE_league_create=0\nBE_league_edit_detail=0\nBE_league_delete=0\nBE_league_edit_round=0\nBE_league_edit_result=0\nBE_league_edit_fixture=0\nBE_teamtournament_general=0\nBE_teamtournament_create=0\nBE_club_general=0\nBE_club_create=0\nBE_club_edit_member=0\nBE_team_general=0\nBE_team_create=0\nBE_team_registration_list=0\nBE_user_general=0\nBE_swt_general=0\nBE_elobase_general=0\nBE_database_general=0\nBE_logfile_general=0\nBE_config_general=0'),
			(18, 'CLMreserve01', 1, 'reserve01', 'CLM', 0, 51, '', 'BE_general_general=0\nBE_season_general=0\nBE_event_general=0\nBE_tournament_general=0\nBE_tournament_create=0\nBE_tournament_edit_detail=0\nBE_tournament_delete=0\nBE_tournament_edit_round=0\nBE_tournament_edit_result=0\nBE_tournament_edit_fixture=0\nBE_league_general=0\nBE_league_create=0\nBE_league_edit_detail=0\nBE_league_delete=0\nBE_league_edit_round=0\nBE_league_edit_result=0\nBE_league_edit_fixture=0\nBE_teamtournament_general=0\nBE_teamtournament_create=0\nBE_teamtournament_edit_detail=0\nBE_teamtournament_delete=0\nBE_teamtournament_edit_round=0\nBE_teamtournament_edit_result=0\nBE_teamtournament_edit_fixture=0\nBE_club_general=0\nBE_club_create=0\nBE_club_edit_member=0\nBE_club_copy=0\nBE_team_general=0\nBE_team_create=0\nBE_team_edit=0\nBE_team_delete=0\nBE_team_registration_list=0\nBE_user_general=0\nBE_user_copy=0\nBE_accessgroup_general=0\nBE_swt_general=0\nBE_elobase_general=0\nBE_database_general=0\nBE_logfile_general=0\nBE_logfile_delete=0\nBE_config_general=0'),
			(19, 'CLMreserve02', 2, 'reserve02', 'CLM', 0, 52, '', 'BE_general_general=0\nBE_season_general=0\nBE_event_general=0\nBE_tournament_general=0\nBE_tournament_create=0\nBE_tournament_edit_detail=0\nBE_tournament_delete=0\nBE_tournament_edit_round=0\nBE_tournament_edit_result=0\nBE_tournament_edit_fixture=0\nBE_league_general=0\nBE_league_create=0\nBE_league_edit_detail=0\nBE_league_delete=0\nBE_league_edit_round=0\nBE_league_edit_result=0\nBE_league_edit_fixture=0\nBE_teamtournament_general=0\nBE_teamtournament_create=0\nBE_teamtournament_edit_detail=0\nBE_teamtournament_delete=0\nBE_teamtournament_edit_round=0\nBE_teamtournament_edit_result=0\nBE_teamtournament_edit_fixture=0\nBE_club_general=0\nBE_club_create=0\nBE_club_edit_member=0\nBE_club_copy=0\nBE_team_general=0\nBE_team_create=0\nBE_team_edit=0\nBE_team_delete=0\nBE_team_registration_list=0\nBE_user_general=0\nBE_user_copy=0\nBE_accessgroup_general=0\nBE_swt_general=0\nBE_elobase_general=0\nBE_database_general=0\nBE_logfile_general=0\nBE_logfile_delete=0\nBE_config_general=0'),
			(20, 'CLMreserve03', 3, 'reserve03', 'CLM', 0, 53, '', 'BE_general_general=0\nBE_season_general=0\nBE_event_general=0\nBE_tournament_general=0\nBE_tournament_create=0\nBE_tournament_edit_detail=0\nBE_tournament_delete=0\nBE_tournament_edit_round=0\nBE_tournament_edit_result=0\nBE_tournament_edit_fixture=0\nBE_league_general=0\nBE_league_create=0\nBE_league_edit_detail=0\nBE_league_delete=0\nBE_league_edit_round=0\nBE_league_edit_result=0\nBE_league_edit_fixture=0\nBE_teamtournament_general=0\nBE_teamtournament_create=0\nBE_teamtournament_edit_detail=0\nBE_teamtournament_delete=0\nBE_teamtournament_edit_round=0\nBE_teamtournament_edit_result=0\nBE_teamtournament_edit_fixture=0\nBE_club_general=0\nBE_club_create=0\nBE_club_edit_member=0\nBE_club_copy=0\nBE_team_general=0\nBE_team_create=0\nBE_team_edit=0\nBE_team_delete=0\nBE_team_registration_list=0\nBE_user_general=0\nBE_user_copy=0\nBE_accessgroup_general=0\nBE_swt_general=0\nBE_elobase_general=0\nBE_database_general=0\nBE_logfile_general=0\nBE_logfile_delete=0\nBE_config_general=0');";
		$database->setQuery($sql);
		if ( $database->query() ) {
			$string .= "* CLM-Benutzergruppen (Standard) geladen in Tabelle clm_usertype<br />";
		} else {
			echo "<font color='red'>* Fehler bei Laden der CLM-Benutzergruppen (Standard)in Tabelle clm_usertype</font><br />";
			SimpleClmInstaller::_debugDB($fieldtypes);
			return false;
		}

	// 1.3.2 Umbau der Tabellen des SWT-Imports
		if (substr(NEW_CLM_VERSION,0,5) > '1.3.1' AND substr(OLD_CLM_VERSION,0,5) < '1.3.2') {

	// Umbenennen der alten SWT-Tabellen, jeweils mit Existenz-Prüfung
			$sql = "SHOW TABLES LIKE '%_clm_swt_liga' "; 
			$database->setQuery($sql);
			if (count($database->loadObjectList ()) == 1) {
				$sql = "SHOW TABLES LIKE '%_clm_swt_liga_old' "; 
				$database->setQuery($sql);
				if (count($database->loadObjectList ()) == 0) {
					$sql = "RENAME TABLE `#__clm_swt_liga` TO `#__clm_swt_liga_old` "; 
					$database->setQuery($sql);
					if ( !$database->query() ) {
						echo "Fehler beim Umbenennen der alten Tabelle clm_swt_liga";
						return false;
					} else {
						$string .= "* alte Tabelle clm_swt_liga umbenannt in clm_swt_liga_old<br />";
			}	}	}
			$sql = "SHOW TABLES LIKE '%_clm_swt_man' "; 
			$database->setQuery($sql);
			if (count($database->loadObjectList ()) == 1) {
				$sql = "SHOW TABLES LIKE '%_clm_swt_man_old' "; 
				$database->setQuery($sql);
				if (count($database->loadObjectList ()) == 0) {
					$sql = "RENAME TABLE `#__clm_swt_man` TO `#__clm_swt_man_old` "; 
					$database->setQuery($sql);
					if ( !$database->query() ) {
						echo "Fehler beim Umbenennen der alten Tabelle clm_swt_man";
						return false;
					} else {
						$string .= "* alte Tabelle clm_swt_man umbenannt in clm_swt_man_old<br />";
			}	}	}
			$sql = "SHOW TABLES LIKE '%_clm_swt_rnd_man' "; 
			$database->setQuery($sql);
			if (count($database->loadObjectList ()) == 1) {
				$sql = "SHOW TABLES LIKE '%_clm_swt_rnd_man_old' "; 
				$database->setQuery($sql);
				if (count($database->loadObjectList ()) == 0) {
					$sql = "RENAME TABLE `#__clm_swt_rnd_man` TO `#__clm_swt_rnd_man_old` "; 
					$database->setQuery($sql);
					if ( !$database->query() ) {
						echo "Fehler beim Umbenennen der alten Tabelle clm_swt_rnd_man";
						return false;
					} else {
						$string .= "* alte Tabelle clm_swt_rnd_man umbenannt in clm_swt_rnd_man_old<br />";
			}	}	}
			$sql = "SHOW TABLES LIKE '%_clm_swt_rnd_spl' "; 
			$database->setQuery($sql);
			if (count($database->loadObjectList ()) == 1) {
				$sql = "SHOW TABLES LIKE '%_clm_swt_rnd_spl_old' "; 
				$database->setQuery($sql);
				if (count($database->loadObjectList ()) == 0) {
					$sql = "RENAME TABLE `#__clm_swt_rnd_spl` TO `#__clm_swt_rnd_spl_old` "; 
					$database->setQuery($sql);
					if ( !$database->query() ) {
						echo "Fehler beim Umbenennen der alten Tabelle clm_swt_rnd_spl";
						return false;
					} else {
						$string .= "* alte Tabelle clm_swt_rnd_spl umbenannt in clm_swt_rnd_spl_old<br />";
			}	}	}
			$sql = "SHOW TABLES LIKE '%_clm_swt_spl' "; 
			$database->setQuery($sql);
			if (count($database->loadObjectList ()) == 1) {
				$sql = "SHOW TABLES LIKE '%_clm_swt_spl_old' "; 
				$database->setQuery($sql);
				if (count($database->loadObjectList ()) == 0) {
					$sql = "RENAME TABLE `#__clm_swt_spl` TO `#__clm_swt_spl_old` "; 
					$database->setQuery($sql);
					if ( !$database->query() ) {
						echo "Fehler beim Umbenennen der alten Tabelle clm_swt_spl";
						return false;
					} else {
						$string .= "* alte Tabelle clm_swt_spl umbenannt in clm_swt_spl_old<br />";
			}	}	}
			$sql = "SHOW TABLES LIKE '%_clm_swt_spl_nach' "; 
			$database->setQuery($sql);
			if (count($database->loadObjectList ()) == 1) {
				$sql = "SHOW TABLES LIKE '%_clm_swt_spl_nach_old' "; 
				$database->setQuery($sql);
				if (count($database->loadObjectList ()) == 0) {
					$sql = "RENAME TABLE `#__clm_swt_spl_nach` TO `#__clm_swt_spl_nach_old` "; 
					$database->setQuery($sql);
					if ( !$database->query() ) {
						echo "Fehler beim Umbenennen der alten Tabelle clm_swt_spl_nach";
						return false;
					} else {
						$string .= "* alte Tabelle clm_swt_spl_nach umbenannt in clm_swt_spl_nach_old<br />";
			}	}	}
			$sql = "SHOW TABLES LIKE '%_clm_swt_spl_tmp' "; 
			$database->setQuery($sql);
			if (count($database->loadObjectList ()) == 1) {
				$sql = "SHOW TABLES LIKE '%_clm_swt_spl_tmp_old' "; 
				$database->setQuery($sql);
				if (count($database->loadObjectList ()) == 0) {
					$sql = "RENAME TABLE `#__clm_swt_spl_tmp` TO `#__clm_swt_spl_tmp_old` "; 
					$database->setQuery($sql);
					if ( !$database->query() ) {
						echo "Fehler beim Umbenennen der alten Tabelle clm_swt_spl_tmp";
						return false;
					} else {
						$string .= "* alte Tabelle clm_swt_spl_tmp umbenannt in clm_swt_spl_tmp_old<br />";
			}	}	}
 
	// Anlegen der neuen SWT-Tabellen
			$sql = "CREATE TABLE IF NOT EXISTS `#__clm_swt_liga` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`lid` int(11) DEFAULT NULL,
				`name` varchar(100) NOT NULL DEFAULT '',
				`sid` mediumint(3) unsigned DEFAULT NULL,
				`teil` mediumint(5) unsigned DEFAULT NULL,
				`stamm` mediumint(5) unsigned DEFAULT NULL,
				`ersatz` mediumint(5) unsigned DEFAULT NULL,
				`rang` tinyint(1) unsigned DEFAULT '0',
				`sl` mediumint(5) unsigned DEFAULT NULL,
				`runden` mediumint(5) unsigned DEFAULT NULL,
				`durchgang` mediumint(5) unsigned DEFAULT NULL,
				`mail` tinyint(1) unsigned DEFAULT NULL,
				`sl_mail` tinyint(1) unsigned DEFAULT NULL,
				`heim` tinyint(1) unsigned DEFAULT NULL,
				`order` tinyint(1) unsigned DEFAULT NULL,
				`rnd` tinyint(1) unsigned DEFAULT NULL,
				`auf` tinyint(1) NOT NULL,
				`auf_evtl` tinyint(1) NOT NULL,
				`ab` tinyint(1) NOT NULL,
				`ab_evtl` tinyint(1) NOT NULL,
				`sieg_bed` tinyint(2) unsigned DEFAULT NULL,
				`runden_modus` tinyint(2) unsigned DEFAULT NULL,
				`man_sieg` decimal(4,2) unsigned DEFAULT '1.00',
				`man_remis` decimal(4,2) unsigned DEFAULT '0.50',
				`man_nieder` decimal(4,2) unsigned DEFAULT '0.00',
				`man_antritt` decimal(4,2) unsigned DEFAULT '0.00',
				`sieg` decimal(2,1) unsigned DEFAULT '1.0',
				`remis` decimal(2,1) unsigned DEFAULT '0.5',
				`nieder` decimal(2,1) unsigned DEFAULT '0.0',
				`antritt` decimal(2,1) unsigned DEFAULT '0.0',
				`published` mediumint(3) unsigned DEFAULT NULL,
				`bemerkungen` text,
				`bem_int` text,
				`checked_out` tinyint(3) unsigned NOT NULL DEFAULT '0',
				`checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`ordering` int(11) NOT NULL DEFAULT '0',
				`b_wertung` tinyint(1) unsigned DEFAULT '0',
				`liga_mt` tinyint(1) unsigned DEFAULT '0',
				`tiebr1` tinyint(2) unsigned NOT NULL DEFAULT '0',
				`tiebr2` tinyint(2) unsigned NOT NULL DEFAULT '0',
				`tiebr3` tinyint(2) unsigned NOT NULL DEFAULT '0',
				`params` text NOT NULL,
				PRIMARY KEY (`id`),
				KEY `published` (`published`)
			) ". $engine . $utf8 . ";";
			$database->setQuery($sql);
			if ( !$database->query() ) {
				echo "Fehler beim Anlegen Tabelle clm_swt_liga";
				return false;
			} else {
				$string .= "* Tabelle clm_swt_liga hinzugefügt<br />";
			}
			$sql = "CREATE TABLE IF NOT EXISTS `#__clm_swt_mannschaften` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`sid` int(11) NOT NULL DEFAULT '0',
				`name` varchar(100) NOT NULL DEFAULT '',
				`swt_id` mediumint(5) unsigned DEFAULT NULL,
				`liga` mediumint(5) unsigned DEFAULT NULL,
				`zps` varchar(5) DEFAULT NULL,
				`liste` mediumint(3) NOT NULL DEFAULT '0',
				`edit_liste` mediumint(3) NOT NULL DEFAULT '0',
				`man_nr` mediumint(5) unsigned DEFAULT NULL,
				`tln_nr` mediumint(5) unsigned DEFAULT NULL,
				`mf` mediumint(5) unsigned DEFAULT NULL,
				`sg_zps` varchar(120) DEFAULT NULL,
				`datum` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`edit_datum` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`lokal` text NOT NULL,
				`termine` text,
				`adresse` text,
				`homepage` text,
				`bemerkungen` text NOT NULL,
				`bem_int` text NOT NULL,
				`published` tinyint(1) NOT NULL DEFAULT '0',
				`checked_out` tinyint(3) unsigned NOT NULL DEFAULT '0',
				`checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`ordering` int(11) NOT NULL DEFAULT '0',
				`summanpunkte` decimal(4,1) DEFAULT NULL,
				`sumbrettpunkte` decimal(4,1) DEFAULT NULL,
				`sumwins` tinyint(2) DEFAULT NULL,
				`sumtiebr1` decimal(6,3) DEFAULT '0.000',
				`sumtiebr2` decimal(6,3) DEFAULT '0.000',
				`sumtiebr3` decimal(6,3) DEFAULT '0.000',
				`rankingpos` tinyint(3) unsigned NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`),
				UNIQUE KEY `sid_name_swtid` (`sid`,`name`,`swt_id`),
				KEY `published` (`published`),
				KEY `sid` (`sid`),
				KEY `swt_id` (`swt_id`)
			) ". $engine . $utf8 . ";";
			$database->setQuery($sql);
			if ( !$database->query() ) {
				echo "Fehler beim Anlegen Tabelle clm_swt_mannschaften";
				return false;
			} else {
				$string .= "* Tabelle clm_swt_mannschaften hinzugefügt<br />";
			}
			$sql = "CREATE TABLE IF NOT EXISTS `#__clm_swt_meldeliste_spieler` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`spielerid` smallint(6) unsigned NOT NULL,
				`sid` mediumint(3) unsigned DEFAULT NULL,
				`swt_id` mediumint(5) unsigned DEFAULT NULL,
				`lid` mediumint(5) unsigned DEFAULT NULL,
				`man_id` mediumint(5) unsigned NOT NULL DEFAULT '0',
				`snr` mediumint(5) unsigned DEFAULT NULL,
				`mgl_nr` mediumint(5) unsigned NOT NULL DEFAULT '0',
				`zps` varchar(5) NOT NULL DEFAULT '0',
				`status` mediumint(5) NOT NULL DEFAULT '0',
				`ordering` int(11) NOT NULL DEFAULT '0',
				`DWZ` smallint(4) unsigned NOT NULL DEFAULT '0',
				`I0` smallint(4) unsigned NOT NULL DEFAULT '0',
				`Punkte` decimal(4,1) unsigned NOT NULL DEFAULT '0.0',
				`Partien` tinyint(3) NOT NULL DEFAULT '0',
				`We` decimal(6,3) NOT NULL DEFAULT '0.000',
				`Leistung` smallint(4) NOT NULL DEFAULT '0',
				`EFaktor` tinyint(2) NOT NULL DEFAULT '0',
				`Niveau` smallint(4) NOT NULL DEFAULT '0',
				`sum_saison` decimal(5,1) NOT NULL DEFAULT '0.0',
				`gesperrt` tinyint(1) unsigned DEFAULT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `sid_swtid_manid_zps_mglnr` (`sid`,`swt_id`,`man_id`,`zps`,`mgl_nr`)
			) ". $engine . $utf8 . ";";
			$database->setQuery($sql);
			if ( !$database->query() ) {
				echo "Fehler beim Anlegen Tabelle clm_swt_meldeliste_spieler";
				return false;
			} else {
				$string .= "* Tabelle clm_swt_meldeliste_spieler hinzugefügt<br />";
			}
			$sql = "CREATE TABLE IF NOT EXISTS `#__clm_swt_rnd_man` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`sid` mediumint(5) unsigned DEFAULT NULL,
				`swt_id` mediumint(5) unsigned DEFAULT NULL,
				`lid` mediumint(5) unsigned DEFAULT NULL,
				`runde` mediumint(5) unsigned DEFAULT NULL,
				`paar` mediumint(5) unsigned DEFAULT NULL,
				`dg` tinyint(1) unsigned DEFAULT NULL,
				`heim` tinyint(1) unsigned DEFAULT NULL,
				`tln_nr` mediumint(5) unsigned NOT NULL DEFAULT '0',
				`gegner` mediumint(5) unsigned NOT NULL DEFAULT '0',
				`brettpunkte` decimal(5,1) unsigned DEFAULT NULL,
				`manpunkte` mediumint(5) unsigned DEFAULT NULL,
				`bp_sum` decimal(5,1) unsigned DEFAULT NULL,
				`mp_sum` mediumint(5) unsigned DEFAULT NULL,
				`gemeldet` mediumint(5) unsigned DEFAULT NULL,
				`editor` mediumint(5) unsigned DEFAULT NULL,
				`dwz_editor` mediumint(5) unsigned DEFAULT NULL,
				`zeit` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`edit_zeit` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`dwz_zeit` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`published` tinyint(1) unsigned NOT NULL DEFAULT '0',
				`checked_out` tinyint(3) unsigned NOT NULL DEFAULT '0',
				`checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`ordering` int(11) NOT NULL DEFAULT '0',
				`wertpunkte` decimal(5,1) unsigned DEFAULT NULL,
				`ko_decision` tinyint(1) unsigned NOT NULL DEFAULT '0',
				`comment` text NOT NULL,
				PRIMARY KEY (`id`),
				KEY `published` (`published`),
				KEY `swt_id` (`swt_id`)
			) ". $engine . $utf8 . ";";
			$database->setQuery($sql);
			if ( !$database->query() ) {
				echo "Fehler beim Anlegen Tabelle clm_swt_rnd_man";
				return false;
			} else {
				$string .= "* Tabelle clm_swt_rnd_man hinzugefügt<br />";
			}
			$sql = "CREATE TABLE IF NOT EXISTS `#__clm_swt_rnd_spl` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`sid` mediumint(5) unsigned DEFAULT NULL,
				`swt_id` mediumint(5) unsigned DEFAULT NULL,
				`lid` mediumint(5) unsigned DEFAULT NULL,
				`runde` mediumint(5) unsigned DEFAULT NULL,
				`paar` mediumint(5) unsigned DEFAULT NULL,
				`dg` tinyint(1) unsigned DEFAULT NULL,
				`tln_nr` mediumint(5) unsigned DEFAULT NULL,
				`brett` mediumint(5) unsigned DEFAULT NULL,
				`heim` tinyint(1) unsigned DEFAULT NULL,
				`weiss` tinyint(1) unsigned DEFAULT NULL,
				`spieler` mediumint(5) unsigned DEFAULT NULL,
				`zps` varchar(5) DEFAULT NULL,
				`gegner` mediumint(5) unsigned DEFAULT NULL,
				`gzps` varchar(5) DEFAULT NULL,
				`ergebnis` mediumint(5) unsigned DEFAULT NULL,
				`kampflos` tinyint(1) unsigned DEFAULT NULL,
				`punkte` decimal(5,1) unsigned DEFAULT NULL,
				`gemeldet` mediumint(5) unsigned DEFAULT NULL,
				`dwz_edit` mediumint(5) unsigned DEFAULT NULL,
				`dwz_editor` mediumint(5) unsigned DEFAULT NULL,
				PRIMARY KEY (`id`)
			) ". $engine . $utf8 . ";";
			$database->setQuery($sql);
			if ( !$database->query() ) {
				echo "Fehler beim Anlegen Tabelle clm_swt_rnd_spl";
				return false;
			} else {
				$string .= "* Tabelle clm_swt_rnd_spl hinzugefügt<br />";
			}
			$sql = "CREATE TABLE IF NOT EXISTS `#__clm_swt_turniere` (
				`swt_tid` int(11) NOT NULL AUTO_INCREMENT,
				`tid` int(11) NOT NULL DEFAULT '0',
				`name` varchar(100) NOT NULL DEFAULT '',
				`sid` mediumint(3) unsigned DEFAULT NULL,
				`dateStart` date NOT NULL,
				`dateEnd` date NOT NULL,
				`catidAlltime` smallint(6) unsigned NOT NULL DEFAULT '0',
				`catidEdition` smallint(6) unsigned NOT NULL DEFAULT '0',
				`typ` tinyint(1) unsigned DEFAULT NULL,
				`tiebr1` tinyint(2) unsigned NOT NULL DEFAULT '0',
				`tiebr2` tinyint(2) unsigned NOT NULL DEFAULT '0',
				`tiebr3` tinyint(2) unsigned NOT NULL DEFAULT '0',
				`rnd` tinyint(1) unsigned DEFAULT NULL,
				`teil` mediumint(5) unsigned DEFAULT NULL,
				`runden` mediumint(5) unsigned DEFAULT NULL,
				`dg` mediumint(5) unsigned DEFAULT NULL,
				`tl` mediumint(5) unsigned DEFAULT NULL,
				`bezirk` varchar(8) DEFAULT NULL,
				`bezirkTur` enum('0','1') NOT NULL DEFAULT '1',
				`vereinZPS` varchar(5) DEFAULT NULL,
				`published` tinyint(1) NOT NULL DEFAULT '0',
				`started` tinyint(1) NOT NULL DEFAULT '0',
				`finished` tinyint(1) NOT NULL DEFAULT '0',
				`invitationText` text,
				`bemerkungen` text,
				`bem_int` text,
				`checked_out` tinyint(3) unsigned NOT NULL DEFAULT '0',
				`checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`ordering` int(11) NOT NULL DEFAULT '0',
				`params` text NOT NULL,
				PRIMARY KEY (`swt_tid`),
				KEY `published` (`published`)
			) ". $engine . $utf8 . ";";
			$database->setQuery($sql);
			if ( !$database->query() ) {
				echo "Fehler beim Anlegen Tabelle clm_swt_turniere";
				return false;
			} else {
				$string .= "* Tabelle clm_swt_turniere hinzugefügt<br />";
			}
			$sql = "CREATE TABLE IF NOT EXISTS `#__clm_swt_turniere_rnd_spl` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`sid` mediumint(5) unsigned DEFAULT NULL,
				`turnier` mediumint(5) unsigned DEFAULT NULL,
				`swt_tid` mediumint(5) unsigned DEFAULT NULL,
				`runde` mediumint(5) unsigned DEFAULT NULL,
				`paar` mediumint(5) unsigned DEFAULT NULL,
				`brett` mediumint(5) unsigned DEFAULT NULL,
				`dg` tinyint(1) unsigned DEFAULT NULL,
				`tln_nr` mediumint(5) unsigned DEFAULT NULL,
				`heim` tinyint(1) unsigned DEFAULT NULL,
				`spieler` mediumint(5) unsigned DEFAULT NULL,
				`gegner` mediumint(5) unsigned DEFAULT NULL,
				`ergebnis` mediumint(5) unsigned DEFAULT NULL,
				`tiebrS` tinyint(2) unsigned NOT NULL DEFAULT '0',
				`tiebrG` tinyint(2) unsigned NOT NULL DEFAULT '0',
				`kampflos` tinyint(1) unsigned DEFAULT NULL,
				`pgn` text NOT NULL,
				`ordering` int(11) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`),
				UNIQUE KEY `turnier_runde_brett_heim` (`swt_tid`,`runde`,`brett`,`heim`)
			) ". $engine . $utf8 . ";";
			$database->setQuery($sql);
			if ( !$database->query() ) {
				echo "Fehler beim Anlegen Tabelle clm_swt_turniere_rnd_spl";
				return false;
			} else {
				$string .= "* Tabelle clm_swt_turniere_rnd_spl hinzugefügt<br />";
			}
			$sql = "CREATE TABLE IF NOT EXISTS `#__clm_swt_turniere_rnd_termine` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`sid` mediumint(5) unsigned DEFAULT NULL,
				`name` varchar(100) NOT NULL DEFAULT '',
				`turnier` mediumint(5) unsigned DEFAULT NULL,
				`swt_tid` mediumint(5) unsigned DEFAULT NULL,
				`dg` tinyint(1) unsigned DEFAULT NULL,
				`nr` mediumint(5) unsigned DEFAULT NULL,
				`datum` date NOT NULL DEFAULT '0000-00-00',
				`startzeit` time NOT NULL default '00:00:00',
				`abgeschlossen` mediumint(3) NOT NULL DEFAULT '0',
				`tl_ok` tinyint(1) NOT NULL DEFAULT '0',
				`published` tinyint(1) NOT NULL DEFAULT '0',
				`bemerkungen` text,
				`bem_int` text,
				`gemeldet` mediumint(3) unsigned DEFAULT NULL,
				`editor` mediumint(3) unsigned DEFAULT NULL,
				`zeit` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`edit_zeit` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`checked_out` tinyint(3) unsigned NOT NULL DEFAULT '0',
				`checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`ordering` int(11) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`),
				UNIQUE KEY `turnier_runde` (`swt_tid`,`nr`),
				KEY `published` (`published`)
			) ". $engine . $utf8 . ";";
			$database->setQuery($sql);
			if ( !$database->query() ) {
				echo "Fehler beim Anlegen Tabelle clm_swt_turniere_rnd_termine";
				return false;
			} else {
				$string .= "* Tabelle clm_swt_turniere_rnd_termine hinzugefügt<br />";
			}
			$sql = "CREATE TABLE IF NOT EXISTS `#__clm_swt_turniere_tlnr` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`sid` mediumint(3) unsigned DEFAULT NULL,
				`turnier` mediumint(4) unsigned DEFAULT NULL,
				`swt_tid` mediumint(4) unsigned DEFAULT NULL,
				`snr` mediumint(5) unsigned DEFAULT NULL,
				`name` varchar(150) DEFAULT NULL,
				`birthYear` year(4) NOT NULL DEFAULT '0000',
				`geschlecht` char(1) DEFAULT NULL,
				`verein` varchar(150) DEFAULT NULL,
				`twz` smallint(4) unsigned DEFAULT NULL,
				`NATrating` smallint(4) unsigned DEFAULT NULL,
				`FIDEelo` smallint(4) unsigned DEFAULT NULL,
				`FIDEid` int(8) unsigned DEFAULT NULL,
				`FIDEcco` char(3) DEFAULT NULL,
				`titel` char(3) DEFAULT NULL,
				`mgl_nr` mediumint(5) unsigned NOT NULL DEFAULT '0',
				`zps` varchar(5) NOT NULL DEFAULT '0',
				`status` mediumint(5) NOT NULL DEFAULT '0',
				`rankingPos` smallint(5) unsigned NOT NULL DEFAULT '0',
				`sum_punkte` decimal(4,1) DEFAULT NULL,
				`sum_bhlz` decimal(5,2) DEFAULT NULL,
				`sum_busum` decimal(6,2) DEFAULT NULL,
				`sum_sobe` decimal(5,2) DEFAULT NULL,
				`sum_wins` tinyint(1) unsigned NOT NULL DEFAULT '0',
				`sumTiebr1` decimal(6,3) DEFAULT NULL,
				`sumTiebr2` decimal(6,3) DEFAULT NULL,
				`sumTiebr3` decimal(6,3) DEFAULT NULL,
				`koStatus` enum('0','1') NOT NULL DEFAULT '1',
				`koRound` tinyint(1) unsigned NOT NULL DEFAULT '0',
				`DWZ` smallint(4) unsigned NOT NULL DEFAULT '0',
				`I0` smallint(4) unsigned NOT NULL DEFAULT '0',
				`Punkte` decimal(4,1) unsigned NOT NULL DEFAULT '0.0',
				`Partien` tinyint(3) NOT NULL DEFAULT '0',
				`We` decimal(6,3) NOT NULL DEFAULT '0.000',
				`Leistung` smallint(4) NOT NULL DEFAULT '0',
				`EFaktor` tinyint(2) NOT NULL DEFAULT '0',
				`Niveau` smallint(4) NOT NULL DEFAULT '0',
				`published` tinyint(1) NOT NULL DEFAULT '0',
				`checked_out` tinyint(3) unsigned NOT NULL DEFAULT '0',
				`checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`ordering` int(11) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`,`zps`,`mgl_nr`,`status`),
				UNIQUE KEY `turnier_snr` (`swt_tid`,`snr`)
			) ". $engine . $utf8 . ";";
			$database->setQuery($sql);
			if ( !$database->query() ) {
				echo "Fehler beim Anlegen Tabelle clm_swt_turniere_tlnr";
				return false;
			} else {
				$string .= "* Tabelle clm_swt_turniere_tlnr hinzugefügt<br />";
			}
		}
		
		// 1.3.2
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_swt_meldeliste_spieler
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW INDEX FROM #__clm_swt_meldeliste_spieler");
		$indices = $database->loadObjectList('Key_name');
		if (isset($indices['sid_swtid_zps_mglnr'])) {
			$sql = "ALTER TABLE #__clm_swt_meldeliste_spieler DROP INDEX sid_swtid_zps_mglnr ";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Index 'sid_swtid_zps_mglnr' gelöscht in Tabelle clm_swt_meldeliste_spieler<br />";
			} else {
				echo "<font color='red'>* Fehler beim Löschen Index 'sid_swtid_zps_mglnr' in Tabelle clm_swt_meldeliste_spieler</font><br />";
				echo mysql_error()."<br /><br />";
				echo "Vorhandene Feldnamen in Indices: ";
				SimpleClmInstaller::_debugDB($indices);
				return false;
			}
		}
		if (!isset($indices['sid_swtid_manid_zps_mglnr'])) {
			$sql = "ALTER TABLE #__clm_swt_meldeliste_spieler ADD UNIQUE INDEX `sid_swtid_manid_zps_mglnr` (`sid`,`swt_id`,`man_id`,`zps`,`mgl_nr`)";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Index 'sid_swtid_manid_zps_mglnr' hinzugefügt in Tabelle clm_swt_meldeliste_spieler<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Index 'sid_swtid_manid_zps_mglnr' in Tabelle clm_swt_meldeliste_spieler</font><br />";
				echo mysql_error()."<br /><br />";
				echo "Vorhandene Feldnamen in Indices: ";
				SimpleClmInstaller::_debugDB($indices);
				return false;
			}
		}

		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_turniere_rnd_spl
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_turniere_rnd_spl");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}

		// 1.4.1
		if (!isset($fieldtypes['gemeldet'])) {
			$sql = "ALTER TABLE `#__clm_turniere_rnd_spl` ADD `gemeldet`
						MEDIUMINT(3) UNSIGNED DEFAULT NULL AFTER `kampflos`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte gemeldet zu Tabelle clm_turniere_rnd_spl hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Spalte gemeldet zu Tabelle clm_turniere_rnd_spl</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		// 1.4.3
		if  ($fieldtypes['tiebrS'] != 'decimal(2,1) unsigned') { 
			$sql = "ALTER TABLE `#__clm_turniere_rnd_spl` CHANGE `tiebrS` `tiebrS` decimal(2,1) unsigned DEFAULT '0.0' NOT NULL "; 
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte tiebrS updated in Tabelle clm_turniere_rnd_spl<br />";
			} else {
				echo "<font color='red'>* Fehler beim Update der Spalte tiebrS in Tabelle clm_turniere_rnd_spl</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		// 1.4.3
		if  ($fieldtypes['tiebrG'] != 'decimal(2,1) unsigned') { 
			$sql = "ALTER TABLE `#__clm_turniere_rnd_spl` CHANGE `tiebrG` `tiebrG` decimal(2,1) unsigned DEFAULT '0.0' NOT NULL "; 
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte tiebrG updated in Tabelle clm_turniere_rnd_spl<br />";
			} else {
				echo "<font color='red'>* Fehler beim Update der Spalte tiebrG in Tabelle clm_turniere_rnd_spl</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}

		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_swt_liga
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_swt_liga");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}
		// 1.4.2
		if (!isset($fieldtypes['params'])) {
			$sql = "ALTER TABLE `#__clm_swt_liga` ADD `params` text not null;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte params zu Tabelle clm_swt_liga hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Spalte params zu Tabelle clm_swt_liga</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}

		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_swt_mannschaften                            
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_swt_mannschaften");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}	 
		// 1.4.2
		if  ($fieldtypes['sg_zps'] != 'varchar(120)') { 
			$sql = "ALTER TABLE `#__clm_swt_mannschaften` CHANGE `sg_zps` `sg_zps` varchar( 120 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL "; 
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sg_zps updated in Tabelle clm_swt_mannschaften<br />";
			} else {
				echo "<font color='red'>* Fehler beim Update der Spalte sg_zps  in Tabelle clm_swt_mannschaften</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
			
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_dwz_dewis (neu mit v 1.4.3)
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_dwz_dewis");
		$fields = $database->loadObjectList();
		if ( !count($fields) ) {
			//Tabelle wird neu angelegt
			$sql = "CREATE TABLE IF NOT EXISTS `#__clm_dwz_dewis` (
				`id` int(11) NOT NULL auto_increment,
				`pkz` varchar(9) default NULL,
				`nachname` varchar(150) NOT NULL default '',
				`vorname` varchar(150) NOT NULL default '',
				`zps` varchar(5) NOT NULL default '',
				`mgl_nr` varchar(4) NOT NULL default '',
				`dwz` smallint(4) unsigned default NULL,
				`dwz_index` smallint(4) unsigned default NULL,
				`status` char(1) default NULL,
				`geschlecht` char(1) default NULL,
				`geburtsjahr` year(4) NOT NULL default '0000',
				`fide_elo` smallint(4) unsigned default NULL,
				`fide_land` char(3) default NULL,
				`fide_id` int(8) unsigned default NULL,
				`liga` smallint(4) unsigned default NULL,
				`turnier` smallint(4) unsigned default NULL,
				PRIMARY KEY  (`id`)
				) ". $engine . $utf8 . ";";			
			$database->setQuery($sql);
			if ( !$database->query() ) {
				echo "Fehler beim Anlegen Tabelle clm_dwz_dewis";
				return false;
			} else {
				$string .= "* Tabelle clm_dwz_dewis hinzugefügt<br />";
			}
		}
		
		$database->setQuery ("SHOW COLUMNS FROM #__clm_dwz_dewis");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}
		// 1.4.3
		if (!isset($fieldtypes['liga'])) {
			$sql = "ALTER TABLE `#__clm_dwz_dewis` ADD `liga` smallint(4) unsigned default NULL AFTER `fide_id`;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte liga zu Tabelle clm_dwz_dewis hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Spalte liga zu Tabelle clm_dwz_dewis</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['turnier'])) {
			$sql = "ALTER TABLE `#__clm_dwz_dewis` ADD `turnier` smallint(4) unsigned default NULL AFTER `liga`;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte turnier zu Tabelle clm_dwz_dewis hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Spalte turnier zu Tabelle clm_dwz_dewis</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_dwz_dewis_merge (neu mit v 1.4.3)
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_dwz_dewis_merge");
		$fields = $database->loadObjectList();
		if ( !count($fields) ) {
			//Tabelle wird neu angelegt
			$sql = "CREATE TABLE IF NOT EXISTS `#__clm_dwz_dewis_merge` (
				`zps` varchar(5) NOT NULL default '',
				`mgl` varchar(4) NOT NULL default '',
				PRIMARY KEY  (`zps`,`mgl`)
				) ". $engine . $utf8 . ";";			
			$database->setQuery($sql);
			if ( !$database->query() ) {
				echo "Fehler beim Anlegen Tabelle clm_dwz_dewis_merge";
				return false;
			} else {
				$string .= "* Tabelle clm_dwz_dewis_merge hinzugefügt<br />";
			}
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_runden_termine                            
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_runden_termine");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}	 
		// 1.4.5
		if (!isset($fieldtypes['startzeit'])) {
			$sql = "ALTER TABLE `#__clm_runden_termine` ADD `startzeit` time NOT NULL default '00:00:00' AFTER `datum`;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte startzeit zu Tabelle clm_runden_termine hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Spalte startzeit zu Tabelle clm_runden_termine</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		// 1.4.6
		if (!isset($fieldtypes['deadlineday'])) {
			$sql = "ALTER TABLE `#__clm_runden_termine` ADD `deadlineday` date NOT NULL default '0000-00-00' AFTER `startzeit`;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte deadlineday zu Tabelle clm_runden_termine hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Spalte deadlineday zu Tabelle clm_runden_termine</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['deadlinetime'])) {
			$sql = "ALTER TABLE `#__clm_runden_termine` ADD `deadlinetime` time NOT NULL default '24:00:00' AFTER `deadlineday`;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte deadlinetime zu Tabelle clm_runden_termine hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Spalte deadlinetime zu Tabelle clm_runden_termine</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_turniere_rnd_termine                            
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_turniere_rnd_termine");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}	 
		// 1.4.5
		if (!isset($fieldtypes['startzeit'])) {
			$sql = "ALTER TABLE `#__clm_turniere_rnd_termine` ADD `startzeit` time NOT NULL default '00:00:00' AFTER `datum`;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte startzeit zu Tabelle clm_turniere_rnd_termine hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Spalte startzeit zu Tabelle clm_turniere_rnd_termine</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_swt_turniere_rnd_termine                            
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_swt_turniere_rnd_termine");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}	 
		// 1.4.5
		if (!isset($fieldtypes['startzeit'])) {
			$sql = "ALTER TABLE `#__clm_swt_turniere_rnd_termine` ADD `startzeit` time NOT NULL default '00:00:00' AFTER `datum`;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte startzeit zu Tabelle clm_swt_turniere_rnd_termine hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Spalte startzeit zu Tabelle clm_swt_turniere_rnd_termine</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		// 1.5.4
		$database->setQuery ("SHOW INDEX FROM #__clm_swt_turniere_rnd_termine");
		$indices = $database->loadObjectList('Key_name');
		if (isset($indices['turnier_runde'])) {
			$sql = "ALTER TABLE #__clm_swt_turniere_rnd_termine DROP INDEX turnier_runde ";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Index 'turnier_runde' gelöscht in Tabelle clm_swt_turniere_rnd_termine<br />";
			} else {
				echo "<font color='red'>* Fehler beim Löschen Index 'turnier_runde' in Tabelle clm_swt_turniere_rnd_termine</font><br />";
				echo mysql_error()."<br /><br />";
				echo "Vorhandene Feldnamen in Indices: ";
				SimpleClmInstaller::_debugDB($indices);
				return false;
			}
		}
		if (!isset($indices['turnier_dg_runde'])) {
			$sql = "ALTER TABLE #__clm_swt_turniere_rnd_termine ADD UNIQUE INDEX `turnier_dg_runde` (`swt_tid`,`dg`,`nr`)";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Index 'turnier_dg_runde' hinzugefügt in Tabelle clm_swt_turniere_rnd_termine<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Index 'turnier_dg_runde' in Tabelle clm_swt_turniere_rnd_termine</font><br />";
				echo mysql_error()."<br /><br />";
				echo "Vorhandene Feldnamen in Indices: ";
				SimpleClmInstaller::_debugDB($indices);
				return false;
			}
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_swt_turniere_rnd_spl                            
		// ---------------------------------------------------------------------------
		// 1.5.4
		$database->setQuery ("SHOW INDEX FROM #__clm_swt_turniere_rnd_spl");
		$indices = $database->loadObjectList('Key_name');
		if (isset($indices['turnier_runde_brett_heim'])) {
			$sql = "ALTER TABLE #__clm_swt_turniere_rnd_spl DROP INDEX turnier_runde_brett_heim ";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Index 'turnier_runde_brett_heim' gelöscht in Tabelle clm_swt_turniere_rnd_spl<br />";
			} else {
				echo "<font color='red'>* Fehler beim Löschen Index 'turnier_runde_brett_heim' in Tabelle clm_swt_turniere_rnd_spl</font><br />";
				echo mysql_error()."<br /><br />";
				echo "Vorhandene Feldnamen in Indices: ";
				SimpleClmInstaller::_debugDB($indices);
				return false;
			}
		}
		if (!isset($indices['turnier_dg_runde_brett_heim'])) {
			$sql = "ALTER TABLE #__clm_swt_turniere_rnd_spl ADD UNIQUE INDEX `turnier_dg_runde_brett_heim` (`swt_tid`,`dg`,`runde`,`brett`,`heim`)";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Index 'turnier_dg_runde_brett_heim' hinzugefügt in Tabelle clm_swt_turniere_rnd_spl<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Index 'turnier_dg_runde_brett_heim' in Tabelle clm_swt_turniere_rnd_spl</font><br />";
				echo mysql_error()."<br /><br />";
				echo "Vorhandene Feldnamen in Indices: ";
				SimpleClmInstaller::_debugDB($indices);
				return false;
			}
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_termine                            
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_termine");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}	 
		// 1.4.5
		if (!isset($fieldtypes['starttime'])) {
			$sql = "ALTER TABLE `#__clm_termine` ADD `starttime` time NOT NULL default '00:00:00' AFTER `startdate`;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte starttime zu Tabelle clm_termine hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Spalte starttime zu Tabelle clm_termine</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['endtime'])) {
			$sql = "ALTER TABLE `#__clm_termine` ADD `endtime` time NOT NULL default '00:00:00' AFTER `enddate`;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte endtime zu Tabelle clm_termine hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Spalte endtime zu Tabelle clm_termine</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['allday'])) {
			$sql = "ALTER TABLE `#__clm_termine` ADD `allday` tinyint(3) NOT NULL default '0' AFTER `starttime`;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte allday zu Tabelle clm_termine hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Spalte allday zu Tabelle clm_termine</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['noendtime'])) {
			$sql = "ALTER TABLE `#__clm_termine` ADD `noendtime` tinyint(3) NOT NULL default '0' AFTER `endtime`;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte noendtime zu Tabelle clm_termine hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Spalte noendtime zu Tabelle clm_termine</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}

		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_meldeliste_spieler                            
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_meldeliste_spieler");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}	 
		// 1.5.2
		if (!isset($fieldtypes['start_dwz'])) {
			$sql = "ALTER TABLE `#__clm_meldeliste_spieler` ADD `start_dwz` smallint(4) UNSIGNED default NULL AFTER `ordering`;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte start_dwz zu Tabelle clm_meldeliste_spieler hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Spalte start_dwz zu Tabelle clm_meldeliste_spieler</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['start_I0'])) {
			$sql = "ALTER TABLE `#__clm_meldeliste_spieler` ADD `start_I0` smallint(4) UNSIGNED default NULL AFTER `start_dwz`;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte start_I0 zu Tabelle clm_meldeliste_spieler hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Spalte start_I0 zu Tabelle clm_meldeliste_spieler</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}

		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_swt_turniere_tlnr                            
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_swt_turniere_tlnr");
		$fields = $database->loadObjectList();
		//echo "<br>swt_turniere_tlnr: "; var_dump($fields); die('kk');
		$fielddefaults = array();
		foreach ($fields as $field) {
			$fielddefaults[$field->Field] = $field->Default;
		}	 
		// 1.5.5
		if  ($fielddefaults['sumTiebr1'] != NULL) { 
			$sql = "ALTER TABLE `#__clm_swt_turniere_tlnr` CHANGE `sumTiebr1` `sumTiebr1` decimal(6,3) DEFAULT NULL "; 
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sumTiebr1 updated in Tabelle clm_swt_turniere_tlnr<br />";
			} else {
				echo "<font color='red'>* Fehler beim Update der Spalte sumTiebr1  in Tabelle clm_swt_turniere_tlnr</font><br />";
				SimpleClmInstaller::_debugDB($fielddefaults);
				return false;
			}
		}
		if  ($fielddefaults['sumTiebr2'] != NULL) { 
			$sql = "ALTER TABLE `#__clm_swt_turniere_tlnr` CHANGE `sumTiebr2` `sumTiebr2` decimal(6,3) DEFAULT NULL "; 
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sumTiebr2 updated in Tabelle clm_swt_turniere_tlnr<br />";
			} else {
				echo "<font color='red'>* Fehler beim Update der Spalte sumTiebr2  in Tabelle clm_swt_turniere_tlnr</font><br />";
				SimpleClmInstaller::_debugDB($fielddefaults);
				return false;
			}
		}
		if  ($fielddefaults['sumTiebr3'] != NULL) { 
			$sql = "ALTER TABLE `#__clm_swt_turniere_tlnr` CHANGE `sumTiebr3` `sumTiebr3` decimal(6,3) DEFAULT NULL "; 
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sumTiebr3 updated in Tabelle clm_swt_turniere_tlnr<br />";
			} else {
				echo "<font color='red'>* Fehler beim Update der Spalte sumTiebr3  in Tabelle clm_swt_turniere_tlnr</font><br />";
				SimpleClmInstaller::_debugDB($fielddefaults);
				return false;
			}
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_turniere_sonderranglisten                            
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_turniere_sonderranglisten");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}	 
		// 1.5.7
		if (!isset($fieldtypes['use_sex_year_filter'])) {
			$sql = "ALTER TABLE `#__clm_turniere_sonderranglisten` ADD `use_sex_year_filter` enum('0','1') default '0' AFTER `zps_lower_than`;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte use_sex_year_filter zu Tabelle clm_turniere_sonderranglisten hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Spalte use_sex_year_filter zu Tabelle clm_turniere_sonderranglisten</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['maleYear_younger_than'])) {
			$sql = "ALTER TABLE `#__clm_turniere_sonderranglisten` ADD `maleYear_younger_than` year(4) default NULL AFTER `use_sex_year_filter`;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte maleYear_younger_than zu Tabelle clm_turniere_sonderranglisten hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Spalte maleYear_younger_than zu Tabelle clm_turniere_sonderranglisten</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['maleYear_older_than'])) {
			$sql = "ALTER TABLE `#__clm_turniere_sonderranglisten` ADD `maleYear_older_than` year(4) default NULL AFTER `maleYear_younger_than`;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte maleYear_older_than zu Tabelle clm_turniere_sonderranglisten hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Spalte maleYear_older_than zu Tabelle clm_turniere_sonderranglisten</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['femaleYear_younger_than'])) {
			$sql = "ALTER TABLE `#__clm_turniere_sonderranglisten` ADD `femaleYear_younger_than` year(4) default NULL AFTER `maleYear_older_than`;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte femaleYear_younger_than zu Tabelle clm_turniere_sonderranglisten hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Spalte femaleYear_younger_than zu Tabelle clm_turniere_sonderranglisten</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['femaleYear_older_than'])) {
			$sql = "ALTER TABLE `#__clm_turniere_sonderranglisten` ADD `femaleYear_older_than` year(4) default NULL AFTER `femaleYear_younger_than`;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte femaleYear_older_than zu Tabelle clm_turniere_sonderranglisten hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Spalte femaleYear_older_than zu Tabelle clm_turniere_sonderranglisten</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		// Termination
		if ( $string == '' ) {
			$string = "* Alles in Ordnung, kein DB-Upgrade erforderlich<br />";
		}
		//return $string;
		//}
	
		
?>
