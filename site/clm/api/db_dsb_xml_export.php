<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
/**
 * xml-Export eines Turniers / einer Liga - neues DSB-Format ab 08.06.2026 zur DWZ-Verrechnung
 */

function clm_api_db_dsb_xml_export($turnierid,$group=false,$test=false) {

	function uuidv4() {
        # Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);

        # set version to 0100 (4, RFC9562)
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        # set variant to 10   (2, RFC9562)
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        # Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
	}

	$lang = clm_core::$lang->draw;
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$test_button = $config->test_button;
	if ($test_button == 1) $test = true; else $test = false;
	if ($test) $debug = 1; else $debug = 0;
//clm_core::$api->test_print('test_button',$test_button);
//clm_core::$api->test_print('test',$test);
//clm_core::$api->test_print('debug',$debug);
	$new_ID = 0;
	if ($debug > 0) { echo "<br><br>-- allgemeine Daten --";	}
	if ($debug > 0) echo "<br><br>Turnier: ".$turnierid; 		//echo "<br>end"; //die();


	// Aufbau der header-Zeilen als Array
	function header_lines() {
		$config = clm_core::$db->config();
 		$fromname = clm_core::$load->utf8decode(clm_core::$load->sub_umlaute($config->email_fromname));
		$user_name = clm_core::$load->utf8decode(clm_core::$load->sub_umlaute(clm_core::$access->getName()));

		$lines = array();
		$warnings = array();
		$lines[] = "<header>";

		$timestamp = time();
		$datum = date("d.m.Y", $timestamp)."T".date("H:i:s", $timestamp);
		$lines[] = "<creationDate>".$datum."</creationDate>";

		if ($fromname <= '')
			$warnings[] = 'FFF Der Ergebnisdienst hat keinen Namen, siehe Einstellungen->EMail ';
		else
			$lines[] = "<sender>".$fromname." - ".$user_name."</sender>";
		$lines[] = "<system>".'CLM - chessleaguemanager.org'."</system>";
		$tournament_UUID = uuidv4();
		$lines[] = "<fileUUID>".$tournament_UUID."</fileUUID>";
		$lines[] = "</header>";

		return array($lines,$warnings);
	}


	// Aufbau der allgemeinen Turnierzeilen als Array
	function common_lines($group,$turnier,$players,$teams,$arundentermine) {
		$config = clm_core::$db->config();
 		$fromname = clm_core::$load->utf8decode(clm_core::$load->sub_umlaute($config->email_fromname));
		$cl_config = $config->cl_config;
		$user_name = clm_core::$load->utf8decode(clm_core::$load->sub_umlaute(clm_core::$access->getName()));
 		$turparams = new clm_class_params($turnier->params);

		$lines = array();
		$warnings = array();
		$lines[] = "<tournament>";

		$tournament_UUID = uuidv4();
		$lines[] = "<tournamentUUID>".$tournament_UUID."</tournamentUUID>";
		$lines[] = "<label>".clm_core::$load->utf8decode(clm_core::$load->sub_umlaute($turnier->name))."</label>";
		$lines[] = "<tournamentType>".$turnier->ktyp."</tournamentType>";
		$time_control = clm_core::$api->db_time_control($turparams->get("time_control",""));
		if ($time_control <= '')
			$warnings[] = 'FFF Es ist keine Bedenkzeitangabe eingetragen';
		else
			$lines[] = "<timecontrol>".clm_core::$load->utf8decode(clm_core::$load->sub_umlaute($time_control))."</timecontrol>";
		
		$lines[] = "<rounds>".$turnier->rounds."</rounds>";
		if ($turnier->dateStart <= '1970-01-01') {
			if (!isset($arundentermine[1]) OR  $arundentermine[1] <= '1970-01-01') {
				$warnings[] = 'FFF Es ist kein Startdatum des Turniers eingetragen';
			} else {
				$lines[] = "<startDate>".$arundentermine[1]."</startDate>";
				$warnings[] = 'WWW Es ist kein Startdatum des Turniers eingetragen - Termin erste Runde verwendet';
			}
		} else
			$lines[] = "<startDate>".$turnier->dateStart."</startDate>";
		if ($turnier->dateEnd <= '1970-01-01') {
			if (!isset($arundentermine[999]) OR  $arundentermine[999] <= '1970-01-01') {
				$warnings[] = 'FFF Es ist kein Enddatum des Turniers eingetragen';
			} else {
				$lines[] = "<endDate>".$arundentermine[999]."</endDate>";
				$warnings[] = 'WWW Es ist kein Enddatum des Turniers eingetragen - Termin letzte Runde verwendet';
			}
		} else
			$lines[] = "<endDate>".$turnier->dateEnd."</endDate>";
		if ($turnier->city <= '')
			$warnings[] = 'FFF Es ist kein Spielort bzw. -region eingetragen';
		else
			$lines[] = "<location>".$turnier->city."</location>";
		$lines[] = '<notes>Erstellt mit CLM - ChessLeagueManager.org ('.$cl_config.') - '.$fromname.' - '.$user_name.'</notes>';

		$lines[] = "</tournament>";

		return array($lines,$warnings);
	}

	// Aufbau der Spielerzeilen als Array
	function player_lines($group,$turnier,$players,$erg_array,$round) {	
		$config = clm_core::$db->config();
 		$turparams = new clm_class_params($turnier->params);
		
		$lines = array();
		$warnings = array();
		$lines[] = "<players>";
		
		for ($i = 0; $i <  count($players); $i++) { 
			$lines[] = "<player>";

			if (!isset($players[$i]->snr) OR $players[$i]->snr <= '' OR !is_numeric($players[$i]->snr))
				$warnings[] = 'FFF Die Startnummer ist nicht korrekt';
			else
				$lines[] = "<tournamentPlayerNumber>".$players[$i]->snr."</tournamentPlayerNumber>";
			if (isset($players[$i]->PKZ) AND ((is_numeric($players[$i]->PKZ) AND $players[$i]->PKZ > '0') OR 
				(((substr($players[$i]->PKZ, 0, 2) == "NU") OR (substr($players[$i]->PKZ, 0, 2) == "FI")) AND (is_numeric(substr($players[$i]->PKZ, 2))))))
				$lines[] = "<dsbId>".$players[$i]->PKZ."</dsbId>";
			$name = clm_core::$load->sub_umlaute($players[$i]->name);
			$a_name = explode(',',$name);
			if (!isset($a_name[0]) OR $a_name[0] <= '')
				$warnings[] = 'FFF Spieler '.$players[$i]->snr.': Es ist kein Nachname eingetragen';
			else
				$lines[] = "<surname>".$a_name[0]."</surname>";
			if (!isset($a_name[1]) OR $a_name[1] <= '')
				$warnings[] = 'FFF Spieler '.$players[$i]->snr.': Es ist kein Vorname eingetragen';
			else
				$lines[] = "<forename>".$a_name[1]."</forename>";
			if ($players[$i]->birthYear <= '0000' OR !is_numeric($players[$i]->birthYear))
				$warnings[] = 'FFF Spieler '.$players[$i]->snr.' '.clm_core::$load->sub_umlaute($players[$i]->name).': Das Geburtsjahr ist nicht korrekt';
			else
				$lines[] = "<dobYear>".$players[$i]->birthYear."</dobYear>";
			if (isset($players[$i]->birthDay) AND !is_null($players[$i]->birthDay)  AND $players[$i]->birthDay > '0000-00-00') 
				$lines[] = "<dobDate>".$players[$i]->birthDay."</dobDate>";
			if (isset($players[$i]->zps) AND  strlen($players[$i]->zps) == 5) 
				$lines[] = "<vkz>".$players[$i]->zps."</vkz>";
			if (isset($players[$i]->mgl_nr) AND is_numeric($players[$i]->mgl_nr) AND ($players[$i]->mgl_nr <= 99999) AND ($players[$i]->mgl_nr > 0) ) 
				$lines[] = "<numberClubMember>".$players[$i]->mgl_nr."</numberClubMember>";
			if (isset($players[$i]->verein) AND ($players[$i]->verein > '') ) 
				$lines[] = "<club>".clm_core::$load->sub_umlaute($players[$i]->verein)."</club>";
			if (isset($players[$i]->FIDEid) AND is_numeric($players[$i]->FIDEid) AND ($players[$i]->FIDEid > 0) ) 
				$lines[] = "<fideId>".$players[$i]->FIDEid."</fideId>";
			if (isset($players[$i]->FIDEelo) AND is_numeric($players[$i]->FIDEelo) AND ($players[$i]->FIDEelo <= 5000) AND ($players[$i]->FIDEelo > 0) ) 
				$lines[] = "<fideRating>".$players[$i]->FIDEelo."</fideRating>";
			if (isset($players[$i]->geschlecht)) {
				$geschlecht = strtolower($players[$i]->geschlecht);
				if ($geschlecht == 'm' OR $geschlecht == 'w') 
					$lines[] = "<sex>".$geschlecht."</sex>";
			}

/*			// Feldprüfungen$players
			if ($players[$i]->FIDEid < 1) {
				$lines[] = 'FFF Spieler '.$players[$i]->snr.' '.clm_core::$load->sub_umlaute($players[$i]->name)." ist ohne FIDE-ID";
			}	
*/
			$lines[] = "</player>";

		}
		$lines[] = "</players>";

		return array($lines,$warnings);
	}

	// Aufbau der Ergebniszeilen als Array
	function game_lines($group,$turnier,$players,$ergs,$arundentermine) {	
		$config = clm_core::$db->config();
 		$turparams = new clm_class_params($turnier->params);

/*		// Texte der möglichen Einzelergebnisse bereitstellen
		$query = " SELECT * FROM `#__clm_ergebnis` ";
		$terg = clm_core::$db->loadObjectList($query);
		$a_terg = array();
		foreach ($terg as $terg1) {
			$a_terg[$terg1->eid] = $terg1->xml_w;
		}
*/		
		$lines = array();
		$warnings = array();
		$lines[] = "<games>";
		
		for ($i = 0; $i <  count($ergs); $i++) { 
			$lines[] = "<game>";

			if (!isset($ergs[$i]->runde) OR $ergs[$i]->runde <= '' OR !is_numeric($ergs[$i]->runde))
				$warnings[] = 'FFF Die Rundennummer ist nicht korrekt';
			else {
				$lines[] = "<round>".$ergs[$i]->runde."</round>";
				if (isset($arundentermine[$ergs[$i]->runde]))
					$lines[] = "<date>".$arundentermine[$ergs[$i]->runde]."</date>";
			}
			if (!isset($ergs[$i]->spieler) OR !is_numeric($ergs[$i]->spieler) OR $ergs[$i]->spieler < 0 )
				$warnings[] = 'FFF Die Spielernummer Weiss ist nicht korrekt';
			elseif ($ergs[$i]->spieler == 0)
				$lines[] = "<noneWhitePlayer>".'true'."</noneWhitePlayer>";
			else
				$lines[] = "<tournamentPlayerNumberWhite>".$ergs[$i]->spieler."</tournamentPlayerNumberWhite>";
			if (!isset($ergs[$i]->gegner) OR !is_numeric($ergs[$i]->gegner) OR $ergs[$i]->gegner < 0 )
				$warnings[] = 'FFF Die Spielernummer Schwarz ist nicht korrekt';
			elseif ($ergs[$i]->gegner == 0)
				$lines[] = "<noneBlackPlayer>".'true'."</noneBlackPlayer>";
			else
				$lines[] = "<tournamentPlayerNumberBlack>".$ergs[$i]->gegner."</tournamentPlayerNumberBlack>";

			if ($ergs[$i]->ergebnis == '0') { $erg_w = '0'; $erg_b = '1'; }
			elseif ($ergs[$i]->ergebnis == '1') { $erg_w = '1'; $erg_b = '0'; }
			elseif ($ergs[$i]->ergebnis == '2') { $erg_w = '0.5'; $erg_b = '0.5'; }
			elseif ($ergs[$i]->ergebnis == '3') { $erg_w = '0'; $erg_b = '0'; }
			elseif ($ergs[$i]->ergebnis == '4') { $erg_w = '-'; $erg_b = '+'; }
			elseif ($ergs[$i]->ergebnis == '5') { $erg_w = '+'; $erg_b = '-'; }
			elseif ($ergs[$i]->ergebnis == '6') { $erg_w = '-'; $erg_b = '-'; }
			elseif ($ergs[$i]->ergebnis == '7') { $erg_w = '-'; $erg_b = '-'; }
			elseif ($ergs[$i]->ergebnis == '8') { $erg_w = '+'; $erg_b = '-'; }
			elseif ($ergs[$i]->ergebnis == '9') { $erg_w = '0'; $erg_b = '0.5'; }
			elseif ($ergs[$i]->ergebnis == '10') { $erg_w = '0.5'; $erg_b = '0'; }
			elseif ($ergs[$i]->ergebnis == '11') { $erg_w = '+'; $erg_b = '-'; }
			elseif ($ergs[$i]->ergebnis == '12') { $erg_w = '0.5'; $erg_b = '-'; }
			elseif ($ergs[$i]->ergebnis == '13') { $erg_w = '0'; $erg_b = '-'; }
			else { $erg_w = '-'; $erg_b = '-'; }						
			$lines[] = "<pointWhite>".$erg_w."</pointWhite>";
			$lines[] = "<pointBlack>".$erg_b."</pointBlack>";

			$lines[] = "</game>";
		}
		$lines[] = "</games>";

		return array($lines,$warnings);
	}

	
//---------------- main routine --------------------		
	$message = '';
	$ret = "";
	
	if ($group) {
		// Teamwettbewerburnier auslesen
		$query = 'SELECT * FROM #__clm_liga WHERE id = '.$turnierid;
		$turnier = clm_core::$db->loadObject($query);
		// Anzahl Runden
		$round = $turnier->durchgang * $turnier->runden;
		$turnier->rounds = $turnier->durchgang * $turnier->runden;
		// Setzen Turniertyp
		$typ	= $turnier->runden_modus;
		$turnier->ttyp = '';
		if($typ =="1"){ $turnier->ktyp = 'TR'; $turnier->ttyp = 'Team Round Robin'; } // TR: Mannschaftsturnier; jeder gegen jeden
		if($typ =="2"){ $turnier->ktyp = 'TR'; $turnier->ttyp = 'Team Round Robin'; }
		if($typ =="3"){ $turnier->ktyp = 'TW'; $turnier->ttyp = 'Team Swiss Dutch'; } // TW: Mannschaftsturnier: Schweizer System
		if($typ =="4"){ $turnier->ktyp = 'TC'; $turnier->ttyp = 'Team Knockout'; }
		if($typ =="5"){ $turnier->ktyp = 'TC'; $turnier->ttyp = 'Team Knockout'; } // TC: Mannschaftsturnier: K.O.-System (Pokal)
		// Turnierleiter mit Name und FIDE-ID
		$query = "SELECT s.spielername,s.fide_id FROM #__clm_dwz_spieler s,#__clm_user u,#__clm_liga t WHERE" .
		       " s.zps=u.zps AND s.mgl_nr=u.mglnr AND t.sl=u.jid AND t.sid=s.sid AND u.sid=t.sid AND t.id=" . $turnier->id . ";";
		$turnierleiterliste = clm_core::$db->loadObjectList($query);
		$turnier->leiter = "";
		if (isset($turnierleiterliste[0]) AND $turnierleiterliste[0]->spielername != "") {
			$turnier->leiter = $turnierleiterliste[0]->spielername . " (" . $turnierleiterliste[0]->fide_id . ")";
		}
										  

		// Hauptschiedsrichter auslesen
		$query = "SELECT at.*, a.name, a.vorname, a.fidefed FROM #__clm_arbiter_turnier as at "
				." LEFT JOIN #__clm_arbiter as a ON at.fideid = a.fideid "
				." WHERE at.liga = $turnierid "
				." AND at.trole = 'A' AND at.role = 'CA' ";
		$turnier->arbiter_CA	= clm_core::$db->loadObjectList($query);

		// Hauptschiedsrichter auslesen
		$query = "SELECT at.*, a.name, a.vorname, a.fidefed FROM #__clm_arbiter_turnier as at "
				." LEFT JOIN #__clm_arbiter as a ON at.fideid = a.fideid "
				." WHERE at.liga = $turnierid "
				." AND at.trole = 'A' AND at.role != 'CA' ";
		$turnier->arbiter_more	= clm_core::$db->loadObjectList($query);

		// Termine auslesen
		$query = "SELECT * FROM #__clm_runden_termine WHERE liga = " . $turnierid . " ORDER BY nr";
		$rundentermine = clm_core::$db->loadObjectList($query);
		
		$arundentermine = array();
		foreach ($rundentermine as $rundentermine1) {
			$arundentermine[$rundentermine1->nr] = $rundentermine1->datum;
			$arundentermine[999] = $rundentermine1->datum;
		}		
				
		// Spielerliste laden - alle Spieler
		$query = "SELECT ml.*, ml.Punkte as sum_punkte, d.DWZ as dwz, d.FIDE_Elo as FIDEelo, d.FIDE_ID as FIDEid, d.FIDE_Land as FIDEcco,"
			." d.FIDE_Titel as titel, d.spielername as name, d.Geburtsjahr as birthYear, d.Geschlecht as geschlecht, d.PKZ as dPKZ,"
			." 0 as rankingPos, 0 as tlnrStatus, m.tln_nr, v.Vereinname as verein "
			." FROM #__clm_meldeliste_spieler AS ml "
			." LEFT JOIN #__clm_mannschaften AS m ON (m.liga=ml.lid AND (m.zps=ml.zps OR FIND_IN_SET(ml.zps,m.sg_zps) != 0 OR (m.zps = '0' AND ml.zps = '-1'))"
				." AND m.man_nr = ml.mnr AND m.man_nr !=0 AND m.liste !=0) "
			." LEFT JOIN #__clm_dwz_spieler AS d ON (d.Mgl_Nr = ml.mgl_nr AND d.ZPS = ml.zps AND d.sid = ml.sid )"
			." LEFT JOIN #__clm_dwz_vereine AS v ON (v.ZPS = ml.zps AND v.sid = ml.sid )"
			." WHERE ml.lid = ".$turnierid
			." ORDER BY m.tln_nr, ml.snr"
			;
		$players = clm_core::$db->loadObjectList($query);

		// Einsätze der Spieler zählen
		$query = "SELECT zps, spieler, COUNT(*) as ceins FROM #__clm_rnd_spl " 
				." WHERE lid = " . $turnierid . " AND spieler > 0 AND runde <= " . $round
				." GROUP by zps, spieler ;";
		$eins = clm_core::$db->loadObjectList($query);

		$aeins = array();
		foreach ($eins as $eins1) {
			$aeins[$eins1->zps][$eins1->spieler] = $eins1->ceins;
		}

		$aplayers = array();
		$playersc = array();
		$lsnr = 0;
		foreach ($players as $player1) {
			if (!isset($aeins[$player1->zps][$player1->mgl_nr]) OR !is_numeric($aeins[$player1->zps][$player1->mgl_nr]) OR $aeins[$player1->zps][$player1->mgl_nr] <= 0) {
				continue;
			}
			if (isset($aplayers[$player1->zps][$player1->mgl_nr])) {
				$player1->snr_ml = $player1->snr;
				$player1->snr = 0;
				$player1->lsnr = 0;
				continue;
			}
			$lsnr++;
			$aplayers[$player1->zps][$player1->mgl_nr]['name'] = $player1->name;
			$aplayers[$player1->zps][$player1->mgl_nr]['lsnr'] = $lsnr;
			$player1->snr_ml = $player1->snr;
			$player1->snr = $lsnr;
			$player1->lsnr = $lsnr;
			$player1->PKZ = $player1->dPKZ;
			$playersc[] = $player1;
		}
		$players = $playersc;
	
		// Paarungen der Runden laden
		$query = "SELECT sp.*, ml.snr FROM #__clm_rnd_spl as sp " 
				." LEFT JOIN #__clm_mannschaften AS m ON ((m.liga=sp.lid) AND (m.tln_nr=sp.tln_nr)) "
				." LEFT JOIN #__clm_meldeliste_spieler AS ml ON ((sp.lid=ml.lid) AND (m.man_nr=ml.mnr) AND (sp.zps=ml.zps) AND (sp.spieler=ml.mgl_nr)) "
				." WHERE sp.lid = " . $turnierid . " AND sp.spieler > 0 AND sp.runde <= " . $round . " ORDER by sp.tln_nr, ml.snr, sp.runde;";
		$erg = clm_core::$db->loadObjectList($query);
		$i = 0;
		foreach ($erg as $erg1) {
			$i++;
			if (isset($aplayers[$erg1->zps][$erg1->spieler])) {
				$erg1->mgl_nr = $erg1->spieler;
				$erg1->spieler = $aplayers[$erg1->zps][$erg1->spieler]['lsnr'];
			} else {
				echo "<br>$i SPIELER : "; var_dump($erg1);
			}
			if (isset($aplayers[$erg1->gzps][$erg1->gegner])) {
				$erg1->gmgl_nr = $erg1->gegner;
				$erg1->gegner = $aplayers[$erg1->gzps][$erg1->gegner]['lsnr'];
			} else {
				$erg1->gegner = 0;
//				echo "<br>$i GEGNER : "; var_dump($erg1);
			}
		}

		// Mannschaften auslesen
		$query = "SELECT * FROM #__clm_mannschaften WHERE liga = " . $turnierid . " ORDER BY tln_nr";
		$teams = clm_core::$db->loadObjectList($query);
		
	} else {
		// Einzelturnierturnier auslesen
		$query = 'SELECT * FROM #__clm_turniere WHERE id = '.$turnierid;
		$turnier = clm_core::$db->loadObject($query);
		// Setzen Anzahl runden
		$round = $turnier->dg * $turnier->runden;
		$turnier->rounds = $turnier->dg * $turnier->runden;
		// Setzen Turniertyp
		$typ	= $turnier->typ;
		$turnier->ttyp = '';
		if($typ =="1"){ $turnier->ktyp = 'SW'; $turnier->ttyp = 'Individual Swiss Dutch'; } // SW: Einzelturnier; Schweizer System
		if($typ =="2"){ $turnier->ktyp = 'SR'; $turnier->ttyp = 'Individual Round Robin'; } // SR: Einzelturnier; jeder gegen jeden
		if($typ =="3"){ $turnier->ktyp = 'SC'; $turnier->ttyp = 'Individual Knockout'; } // SC: Einzelturnier; K.O. System (Pokal)
		if($typ =="4"){ $turnier->ktyp = 'SC'; $turnier->ttyp = 'Individual Knockout'; } 
		if($typ =="5"){ $turnier->ktyp = 'SC'; $turnier->ttyp = 'Individual Knockout'; } // SC: Einzelturnier; K.O. System (Pokal)
		if($typ =="6"){ $turnier->ktyp = 'SR'; $turnier->ttyp = 'Individual Round Robin'; } // SR: Einzelturnier; jeder gegen jeden
		// Turnierleiter mit Name und FIDE-ID
		$query = "SELECT s.spielername,s.fide_id FROM #__clm_dwz_spieler s,#__clm_user u,#__clm_turniere t WHERE" .
		       " s.zps=u.zps AND s.mgl_nr=u.mglnr AND t.tl=u.jid AND t.sid=s.sid AND u.sid=t.sid AND t.id=" . $turnier->id . ";";
		$turnierleiterliste = clm_core::$db->loadObjectList($query);
		$turnier->leiter = "";
		if (isset($turnierleiterliste[0]) AND $turnierleiterliste[0]->spielername != "") {
			$turnier->leiter = $turnierleiterliste[0]->spielername . " (" . $turnierleiterliste[0]->fide_id . ")";
		}
		
		// Hauptschiedsrichter auslesen
		$query = "SELECT at.*, a.name, a.vorname, a.fidefed FROM #__clm_arbiter_turnier as at "
				." LEFT JOIN #__clm_arbiter as a ON at.fideid = a.fideid "
				." WHERE at.turnier = $turnierid "
				." AND at.trole = 'A' AND at.role = 'CA' ";
		$turnier->arbiter_CA	= clm_core::$db->loadObjectList($query);

		// Hauptschiedsrichter auslesen
		$query = "SELECT at.*, a.name, a.vorname, a.fidefed FROM #__clm_arbiter_turnier as at "
				." LEFT JOIN #__clm_arbiter as a ON at.fideid = a.fideid "
				." WHERE at.turnier = $turnierid "
				." AND at.trole = 'A' AND at.role != 'CA' ";
		$turnier->arbiter_more	= clm_core::$db->loadObjectList($query);

		// Termine auslesen
		$query = "SELECT * FROM #__clm_turniere_rnd_termine WHERE turnier = " . $turnierid . " ORDER BY dg,nr;";
		$rundentermine = clm_core::$db->loadObjectList($query);
//clm_core::$api->test_print('rundentermine',$rundentermine);
		
		$arundentermine = array();
		foreach ($rundentermine as $rundentermine1) {
			$arundentermine[$rundentermine1->nr] = $rundentermine1->datum;
		}		
//clm_core::$api->test_print('arundentermine',$arundentermine);

		// Spielerliste laden - alle Spieler
		$query = "SELECT tl.*, d.PKZ as dPKZ FROM #__clm_turniere_tlnr as tl " 
				." LEFT JOIN #__clm_dwz_spieler AS d ON (d.Mgl_Nr = tl.mgl_nr AND d.ZPS = tl.zps AND d.sid = tl.sid )"
				." WHERE tl.turnier = " . $turnierid . " ORDER BY tl.snr;";
		$players = clm_core::$db->loadObjectList($query);

		$aplayers = array();
		foreach ($players as $player1) {
			$aplayers[$player1->snr]['name'] = $player1->name;
			if ($player1->PKZ <= '')
				$player1->PKZ = $player1->dPKZ;
		}
		
		// Paarungen der Runden laden
		$query = "SELECT * FROM #__clm_turniere_rnd_spl";
		$query .= " WHERE turnier = ".$turnierid." AND tln_nr IS NOT NULL AND runde <= ".$round." AND heim = 1"; 
		$query .= " ORDER by dg,runde,brett;";
		$erg = clm_core::$db->loadObjectList($query);
			
	}
	if ($debug > 0) { 
		echo "<br><br>-- Ergebnisse --";	
		$i = 0;
		foreach ($erg as $erg1) {
			$i++;
//			echo "<br>$i : "; var_dump($erg1);
		}
		
		echo "<br><br>-- Turnier --";	
			echo "<br>0 : "; var_dump($turnier);
			
		echo "<br><br>-- Rundentermine --";	
		$i = 0;
		foreach ($rundentermine as $termin1) {
			$i++;
//			echo "<br>$i : "; var_dump($termin1);
		}
		
		echo "<br><br>-- Spieler --";	
		$i = 0;
		foreach ($players as $player1) {
			$i++;
//			echo "<br>$i : "; var_dump($player1);
		}
		

	}

	$nl = "\n";
	$ret = '<?xml version="1.0" encoding="UTF-8"?>'.$nl;
	$ret .=	'<DSB_DWZ_TournamentReport xmlns="http://www.schachbund.de/XMLSchema" '.$nl;
	$ret .=	'      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'.$nl;
	$ret .= '	   xsi:schemaLocation="http://www.schachbund.de/XMLSchema';
	$ret .= '	   https://www.schachbund.de/files/wertungsportal/XMLSchema/2026/DSB_DWZ_Tounament_2_5.xsd" version="2.5">'.$nl;
//	var_dump($ret);
//	var_dump(clm_core::$load->utf8decode($ret));
	$lines_header = header_lines();
		echo "<br><br>-- Header --";	
		$i = 0;
		foreach ($lines_header[0] as $lines_header1) {
			$i++;
			echo "<br>$i : "; var_dump(clm_core::$load->utf8decode($lines_header1));
		}
		foreach ($lines_header[1] as $lines_header1) {
			$i++;
			echo "<br>W $i : "; var_dump(clm_core::$load->utf8decode($lines_header1));
		}

	$lines_common = common_lines($group,$turnier,$players,$teams,$arundentermine);
		echo "<br><br>-- General --";	
		$i = 0;
		foreach ($lines_common[0] as $lines_common1) {
			$i++;
			echo "<br>$i : "; var_dump(clm_core::$load->utf8decode($lines_common1));
		}
		foreach ($lines_common[1] as $lines_common1) {
			$i++;
			echo "<br>W $i : "; var_dump(clm_core::$load->utf8decode($lines_common1));
		}

	$lines_player = player_lines($group,$turnier,$players,$erg_array,$round);
	$nl = "\n";
		echo "<br><br>-- Player data --";	
		$i = 0;
		foreach ($lines_player[0] as $lines_player1) {
			$i++;
			echo "<br>$i : "; var_dump(clm_core::$load->utf8decode($lines_player1));
		}
		foreach ($lines_player[1] as $lines_player1) {
			$i++;
			echo "<br>W $i : "; var_dump(clm_core::$load->utf8decode($lines_player1));
		}

	$lines_games = game_lines($group,$turnier,$players,$erg,$arundentermine);
	$nl = "\n";
		echo "<br><br>-- Game data --";	
		$i = 0;
		foreach ($lines_games[0] as $lines_game1) {
			$i++;
			echo "<br>$i : "; var_dump(clm_core::$load->utf8decode($lines_game1));
		}
		foreach ($lines_games[1] as $lines_game1) {
			$i++;
			echo "<br>W $i : "; var_dump(clm_core::$load->utf8decode($lines_game1));
		}


	$elines = array();
	foreach($lines_header[0] as $line) {
		$ret .= clm_core::$load->utf8decode($line).$nl;
	}
	foreach($lines_header[1] as $line) {
		$elines[] = $line;
	}

	foreach($lines_common[0] as $line) {
		$ret .= clm_core::$load->utf8decode($line).$nl;
	}
	foreach($lines_common[1] as $line) {
		$elines[] = $line;
	}

	foreach($lines_player[0] as $line) {
		$ret .= clm_core::$load->utf8decode($line).$nl;
	}
	foreach($lines_player[1] as $line) {
		$elines[] = $line;
	}

	foreach($lines_games[0] as $line) {
		$ret .= clm_core::$load->utf8decode($line).$nl;
	}
	foreach($lines_games[1] as $line) {
		$elines[] = $line;
	}
/*	
	
	if (count($elines) > 0) {
		$ret = $ret . "### Error/Info Section  ".$nl;
		foreach($elines as $line) {
			$line_3 = '###'.substr($line,3);
			$ret = $ret . clm_core::$load->utf8decode($line_3).$nl;
		}
	}
*/
	$ret .= '</DSB_DWZ_TournamentReport>'.$nl;
//die();
	return array($ret,$elines);
}

?>
