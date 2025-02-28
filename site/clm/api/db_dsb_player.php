<?php
/**
 * @ Chess League Manager (CLM) Modul
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/

function dsb_player_import_cleanup($uuid)
{
	clm_core::$db->query("DROP TABLE IF EXISTS `#__clm_tmp_$uuid`");
	return 0;
}

function dsb_player_import_start($uuid)
{
	try {
		clm_core::$db->query("CREATE TABLE `#__clm_tmp_$uuid` LIKE `#__clm_dwz_spieler`");
		clm_core::$db->query("ALTER TABLE `#__clm_tmp_$uuid` ENGINE InnoDB;");
	} catch (Exception $e) {
		dsb_player_import_cleanup($uuid);
		throw $e;
	}
	return 0;
}

/* dsb_player_import_end:
 * At end of import, we update clm_dwz_player from temporary table
 * and set leaving date for record sets which aren't present in temporary table:
 */
function dsb_player_import_end($uuid, $incl_pd=0, $sid)
{
	/*
	 * Importiere alle Spieler aus der temporären Tabelle. Falls der Spieler
	 * bereits vorhanden ist, erfolgt ein Update.
	 */
	$sql_copy= <<<STR
		INSERT INTO `#__clm_dwz_spieler` (
			`sid`, `PKZ`, `ZPS`, `Mgl_Nr`, `Spielername`, `DWZ`, `DWZ_Index`, `Spielername_G`,
			`Geschlecht`, `Geburtsjahr`, `FIDE_Elo`, `FIDE_Land`, `FIDE_ID`, `Status`,
			`FIDE_Titel`)
		SELECT
			`sid`, `PKZ`, `ZPS`, `Mgl_Nr`, `Spielername`, `DWZ`, `DWZ_Index`, `Spielername_G`,
			`Geschlecht`, `Geburtsjahr`, `FIDE_Elo`, `FIDE_Land`, `FIDE_ID`, `Status`,
			`FIDE_Titel`
		FROM `#__clm_tmp_$uuid`
		ON DUPLICATE KEY UPDATE
			Spielername=VALUES(Spielername), Spielername_G=VALUES(Spielername_G),
			DWZ=VALUES(DWZ),DWZ_INDEX=VALUES(DWZ_INDEX),FIDE_Elo=VALUES(FIDE_Elo),
			FIDE_land=VALUES(FIDE_Land), FIDE_ID=VALUES(FIDE_ID), Status=VALUES(Status),
			FIDE_Titel= VALUES(FIDE_Titel), leavingdate='1970-01-01'
	STR;

	try {
		clm_core::$db->query($sql_copy);
	} catch (Exception $e) {
		clm_core::$db->query("ROLLBACK");
		dsb_player_import_cleanup($uuid);
		throw $e;
	}

	/*
	 * Für alle Spieler, die nicht in der neu importierten temporären Tabelle vorhanden
	 * sind, wird leavingdate auf das aktuelle Datum gesetzt.
	 */
	$sql=<<<STR
		UPDATE #__clm_dwz_spieler t1
			LEFT JOIN `#__clm_tmp_$uuid` t2 ON
				(t1.sid=t2.sid AND t1.ZPS=t2.ZPS AND t1.Mgl_Nr= t2.Mgl_nr)
		SET t1.leavingdate=DATE(NOW())
		WHERE t2.sid IS NULL AND t1.sid=$sid
	STR;
	if (!$incl_pd)
		$sql.= " AND t1.Status='A'";

	try {
		clm_core::$db->query($sql);
		clm_core::$db->query("COMMIT");
	} catch (Exception $e) {
		clm_core::$db->query("ROLLBACK");
		dsb_player_import_cleanup($uuid);
		throw $e;
	}
	dsb_player_import_cleanup($uuid);
	return 0;
}

function clm_api_db_dsb_player($spieler = array(), $incl_pd = 0, $file_v = 0, $status=0, $uuid) {
	@set_time_limit(0); // hope
	$sid = clm_core::$access->getSeason();
	$incl_pd = clm_core::$load->make_valid($incl_pd, 0, 0);
	$counter = 0;
	$finish= 0;

	switch ($status) {
		case 1: // START:
			dsb_player_import_start($uuid);
			break;
		case 3: // ABORT:
			dsb_player_import_cleanup($uuid);
			return 0;
			break;
		case 2: // END:
			$finish= 1;
			break;
		default:
			break;
	}

	if (!is_array($spieler) || count($spieler) == 0) {
		return array(true, "w_noPlayerToUpdate");
	}

	if ($file_v == 1) {
		// SQL Statement for copying from temporary table
		$sql_copy = <<<STR
			INSERT INTO `#__clm_tmp_$uuid` (
				`sid`,`ZPS`, `Mgl_Nr`, `Spielername`, `DWZ`, `DWZ_Index`, `Spielername_G`,
				`Geschlecht`, `Geburtsjahr`, `FIDE_Elo`, `FIDE_Land`, `FIDE_ID`, `Status`,
				`FIDE_Titel`)
		 VALUES ($sid,?,?,?,?,?,?,?,?,?,?,?,?,?)
		STR;
	} elseif ($file_v == 2) {
		// SQL Statement for copying from temporary table
		$sql_copy = <<<STR
			INSERT INTO `#__clm_tmp_$uuid` (
				`sid`, `PKZ`, `ZPS`, `Mgl_Nr`, `Spielername`, `DWZ`, `DWZ_Index`, `Spielername_G`,
				`Geschlecht`, `Geburtsjahr`, `FIDE_Elo`, `FIDE_Land`, `FIDE_ID`, `Status`,
				`FIDE_Titel`)
		 VALUES ($sid,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
		STR;
	} else {
		return array(true, "w_noPlayerFileType");
	}

	try {
		clm_core::$db->query("START TRANSACTION");
		$stmt= clm_core::$db->prepare($sql_copy);
	} catch (Exception $e) {
		dsb_player_import_cleanup();
		throw $e;
	}

	// Detaildaten zu Mitgliedern lesen
	for ($i = 0;$i < count($spieler);$i++) {
		if ($file_v == 1) {
			//ZPS,Mgl-Nr,Status,Spielername,Geschlecht,Spielberechtigung,Geburtsjahr,Letzte-Auswertung,DWZ,Index,FIDE-Elo,FIDE-Titel,FIDE-ID,FIDE-Land
			$einSpieler = str_getcsv($spieler[$i], ",", '"');
			if (count($einSpieler) != 14 || ($einSpieler[2] == 'P' && $incl_pd == 0)) {
				continue;
			}
			$out = clm_core::$load->player_dewis_to_clm($einSpieler[3], null, $einSpieler[1], $einSpieler[4], $einSpieler[12], $einSpieler[13]);

			$rating = ($einSpieler[8] != '0' ? $einSpieler[8] : "NULL");
			$rating_index = ($einSpieler[8] != '0' ? $einSpieler[9] : "NULL");
			$stmt->bind_param('sssssssssssss', $einSpieler[0], $out[0], $out[1], $rating, $rating_index, $out[2], $out[3], $einSpieler[6], $einSpieler[10], $out[4], $einSpieler[12], $einSpieler[2], $einSpieler[11]);
		} else {
			//ID,VKZ,Mgl-Nr,Status,Spielername,Geschlecht,Spielberechtigung,Geburtsjahr,Letzte-Auswertung,DWZ,Index,FIDE-Elo,FIDE-Titel,FIDE-ID,FIDE-Land
			$einSpieler = str_getcsv($spieler[$i], ",", '"');
			if (count($einSpieler) != 15 || ($einSpieler[3] == 'P' && $incl_pd == 0)) {
				continue;
			}
			$out = clm_core::$load->player_dewis_to_clm($einSpieler[4], null, $einSpieler[2], $einSpieler[5], $einSpieler[13], $einSpieler[14]);

			$rating = ($einSpieler[9] != '0' ? $einSpieler[9] : "NULL");
			$rating_index = ($einSpieler[9] != '0' ? $einSpieler[10] : "NULL");
			$stmt->bind_param('ssssssssssssss', $einSpieler[0], $einSpieler[1], $out[0], $out[1], $rating, $rating_index, $out[2], $out[3], $einSpieler[7], $einSpieler[11], $out[4], $einSpieler[13], $einSpieler[3], $einSpieler[12]);
		}
		try {
			$stmt->execute();
		} catch (Exception $e) {
				dsb_player_import_cleanup();
				throw $e;
		}
		$counter++;
	}

	try {
		clm_core::$db->query("COMMIT");
		$stmt->close();
	} catch (Exception $e) {
		dsb_player_import_cleanup();
		throw $e;
	}

	if ($finish)
			dsb_player_import_end($uuid, $incl_pd, $sid);

	return array(true, "m_dsbClubSuccess", $counter);
}
?>
