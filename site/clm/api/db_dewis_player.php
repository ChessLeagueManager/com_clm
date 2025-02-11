<?php
/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_api_db_dewis_player($zps = - 1, $incl_pd = 0, $mgl_nr = array()) {
	@set_time_limit(0); // hope
	$source = "https://dwz.svw.info/services/files/dewis.wsdl";
	$sid = clm_core::$access->getSeason();
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$dewis_import_delay = $config->dewis_import_delay;

	$zps = clm_core::$load->make_valid($zps, 8, "");
	$incl_pd = clm_core::$load->make_valid($incl_pd, 0, 0);
	$counter = 0;
	if (strlen($zps) != 5) {
		return array(true, "e_wrongZPSFormat",$counter);
	}

	// SOAP Webservice
	try {
		$client = clm_core::$load->soap_wrapper($source);

		// VKZ des Vereins --> Vereinsliste

		usleep($dewis_import_delay);

		/*
		 * Temporäre Tabelle, die dazu verwendet wird nach dem Update
		 * das leavingdate auf das aktuelle Datum zu setzen, falls ein
		 * Spieler nicht mehr in der temporären Tabelle vorhanden ist.
		 */
		$sql= <<<STR
			CREATE TEMPORARY TABLE IF NOT EXISTS #__clm_tmp_dwz_spieler (
				sid INT UNSIGNED NOT NULL,
				ZPS char(5) NOT NULL,
				Mgl_Nr MEDIUMINT UNSIGNED NOT NULL,
				INDEX(sid, ZPS, Mgl_Nr));
		STR;

		clm_core::$db->query($sql);

		clm_core::$db->query("START TRANSACTION");

		// SQL statement for filling temporary table
		$sql_tmp= <<<STR
			INSERT INTO #__clm_tmp_dwz_spieler (sid, ZPS, Mgl_Nr)
			VALUES ($sid, ?,?)
		STR;

		$stmt_tmp= clm_core::$db->prepare($sql_tmp);

		$unionRatingList = $client->unionRatingList($zps);
		$str = '';

		// SQL Statement for copying from temporary table
		$sql_copy = <<<STR
			INSERT INTO #__clm_dwz_spieler (
				`sid`,`ZPS`, `Mgl_Nr`, `PKZ`, `Spielername`, `DWZ`, `DWZ_Index`, `Spielername_G`,
				`Geschlecht`, `Geburtsjahr`, `FIDE_Elo`, `FIDE_Land`, `FIDE_ID`,
				`FIDE_Titel`, `leavingdate`)
		 VALUES ($sid, '$zps', ?,?,?,?,?,?,?,?,?,?,?,?, '1970-01-01')
		 ON DUPLICATE KEY UPDATE
				Spielername=VALUES(Spielername), Spielername_G=VALUES(Spielername_G),
				DWZ=VALUES(DWZ),DWZ_INDEX=VALUES(DWZ_INDEX),FIDE_Elo=VALUES(FIDE_Elo),
				FIDE_land=VALUES(FIDE_Land), FIDE_ID=VALUES(FIDE_ID),
				FIDE_Titel= VALUES(FIDE_Titel), leavingdate='1970-01-01'
		STR;

		$stmt_copy= clm_core::$db->prepare($sql_copy);
		// Detaildaten zu Mitgliedern lesen
		foreach ($unionRatingList->members as $m) {
			if ($m->state == 'P' && $incl_pd == 0) {
				continue;
			}
			if (count($mgl_nr)!=0)
			{
				$update=false;
				foreach($mgl_nr as $mgl_nr1) {
					if(intval($mgl_nr1)==intval($m->membership)) {
						$update=true;
						break;
					}
				}
				if(!$update) {
					continue;
				}
			}

			usleep($dewis_import_delay);

			$tcard = $client->tournamentCardForId($m->pid);
			$out = clm_core::$load->player_dewis_to_clm($m->surname, $m->firstname, $m->membership, $m->gender, $m->idfide, $tcard->member->fideNation);

			$rating = ($m->rating != '0' ? $m->rating : "NULL");
			$rating_index = ($m->rating != '0' ? $m->ratingIndex : "NULL");
			$state = ($m->state != '' ? $m->state : "A");
			$stmt_copy->bind_param('ssssssssssss', $out[0], $m->pid, $out[1], $rating, $rating_index, $out[2], $out[3], $m->yearOfBirth, $m->elo, $out[4], $m->idfide, $m->fideTitle);
			$stmt_tmp->bind_param('si', $zps,$out[0]);
			$stmt_tmp->execute();
			$stmt_copy->execute();
			$str .= " ".$zps."-".$out[0];
			$counter++;
			unset($tcard);
		}

		$stmt_tmp->close();
		$stmt_copy->close();
		unset($unionRatingList);
		unset($client);

		$sql=<<<STR
			UPDATE #__clm_dwz_spieler t1 LEFT JOIN #__clm_tmp_dwz_spieler t2
				ON t1.sid = t2.sid AND t1.Mgl_Nr = t2.Mgl_Nr and t1.ZPS = t2.ZPS
			SET leavingdate=NOW()
				WHERE t2.sid IS NULL AND t1.zps='$zps' AND t1.sid= $sid
		STR;

		if (!$incl_pd)
			$sql.= " AND t1.Status='A' ";

		clm_core::$db->query($sql);

		// Temporäre tabelle löschen
		clm_core::$db->query("DROP TABLE #__clm_tmp_dwz_spieler");
    clm_core::$db->query("COMMIT");
	}
	catch(SOAPFault $f) {
		clm_core::$db->query("ROLLBACK");
		if($f->getMessage() == "that is not a valid union id" || $f->getMessage() == "that union does not exists") {
			return array(true, "w_wrongZPS",0);
		}
		return array(false, "e_connectionError");
	}
	clm_core::$db->query("COMMIT");
	return array(true, "m_dewisPlayerSuccess".$str, $counter);
}
?>
