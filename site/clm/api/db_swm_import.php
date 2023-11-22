<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 *
 * Import einer Turnierdatei vom Swiss-Manager 
 * bisher nur TUNx = Einzelturnier im CH-Modus und TURx = Einzelturnier als Vollturnier
 * neu ab 3.9.1 auch TUMx = Mannschaftsturnier im CH-Modus und TUTx = Mannschaftsturnier als Vollturnier
*/
function clm_api_db_swm_import($file,$season,$turnier,$group=false,$update=false,$test=false) {
	if (strtolower(JFile::getExt($file) ) == 'tumx' OR strtolower(JFile::getExt($file) ) == 'tutx') {
		$group = true; 
	} else { $group = false; }
	$pos1 = strrpos($file, '/'); if ($pos1 === false) $pos1 = 0;
	$pos2 = strrpos($file, '\\'); if ($pos2 === false) $pos2 = 0;
	if ($pos2 > $pos1); $pos1 = $pos2;
	$filename = substr($file,$pos1+1);
    $lang = clm_core::$lang->swm_import;
	if ($test) $debug = 1; else $debug = 0;
	$auser = (integer) clm_core::$access->getId(); // aktueller CLM-User
	if ($test)	echo "<br><br>Test - keine Übernahme der Daten ins CLM!";

	$new_ID = 0;
if ($debug > 0) { echo "<br><br>-- allgemeine Daten --";	}
if ($debug > 0) echo "<br><br>datei: ".$filename; 		//echo "<br>end"; //die();
if ($debug > 0) echo "<br>saison: ".$season; 	//echo "<br>end"; //die();
if ($debug > 0) echo "<br>turnier: ".$turnier; 	//echo "<br>end"; //die();

	if ($update) {
		if ($group) {
			$select_query = " SELECT * FROM #__clm_liga WHERE id = ".$turnier;
			$uliga = clm_core::$db->loadObjectList($select_query);
		} else {
			$select_query = " SELECT * FROM #__clm_turniere WHERE id = ".$turnier;
			$uturnier = clm_core::$db->loadObjectList($select_query);
		}
	}
	
	if (!$file) {
		echo '<p>Please choose a filename! / Bitte wählen Sie einen Dateinamen aus!</p>';
		return false;
	}
	$contents = file_get_contents($file);
	if (!$contents) return false;
	
	// find start address per data block
	$hexstring = hexToStr('FF8944');
	$pos = 0; $i = 0; $apos = array();
	do {
		$ipos = strpos($contents,$hexstring,$pos);
		if ($ipos !== false) {
			$i++;
			$apos[$i] = $ipos + 3;
			$pos = $ipos + 3;
		}
	} while ($ipos !== false);
if ($debug > 1) { echo "<br>apos: ";	var_dump($apos); } //die();

	// read common tournament data
	// Allgemeine Turnierdaten auslesen
//	Turnierdaten -> Tabelle clm_turniere / clm_liga
if ($debug > 0) { echo "<br><br>-- Turnierdaten --";	}
	$tournament = zzparse_interpret($contents, 'general',$group, $apos[1],($apos[2]-$apos[1]),$debug);
if ($debug > 1) { echo "<br>tournament: ";	var_dump($tournament); }
	$tournament2 = zzparse_interpret($contents, 'general2',$group, $apos[2],($apos[3]-$apos[2]),$debug);
if ($debug > 1) { echo "<br>tournament2: ";	var_dump($tournament2); }
	$tournament["out"][1] = $tournament2["out"][1]; // Anzahl Runden
	$arunden = (integer) $tournament["out"][1][0]; // Anzahl Runden
	if ($group) {
		$tournament["out"][2] = $tournament2["out"][2]; // Anzahl Mannschaften
		$teil = (integer) $tournament["out"][2][0]; // Anzahl Mannschaften
		$tournament["out"][3] = $tournament2["out"][3]; // Anzahl Bretter
		$stamm = (integer) $tournament["out"][3][0]; // Anzahl Bretter
	}
	$tournament["out"][4] = $tournament2["out"][4]; // Anzahl Teilnehmer
	$tournament["out"][201] = transcode_tiebr($group,$tournament2,201,$debug); // Feinwertung
	$tournament["out"][202] = transcode_tiebr($group,$tournament2,202,$debug); // Feinwertung
	$tournament["out"][203] = transcode_tiebr($group,$tournament2,203,$debug); // Feinwertung
	$tournament["out"][204] = transcode_tiebr($group,$tournament2,204,$debug); // Feinwertung
	$tournament["out"][24] = transcode_twz($tournament2["out"][24]);  // TWZ-Ermittlung paramuseAsTWZ
	$tournament["out"][70] = $tournament2["out"][70]; // Startdatum
	$tournament["out"][71] = $tournament2["out"][71]; // Enddatum
	if ($tournament['out'][65][0] > '') $name = $tournament['out'][65][0]; // Turniername
	else $name = $tournament['out'][12][0];
	$tournament["out"][213] = transcode_fidecorrect($tournament2["out"][213]); // optionTiebreakersFideCorrect
	$tournament_fk = transcode_fidecorrect($tournament2["out"][214]); // optionTiebreakersFideCorrect
	if ($tournament_fk[0] > $tournament["out"][213][0]) $tournament["out"][213][0] = $tournament_fk[0];
	$tournament_fk = transcode_fidecorrect($tournament2["out"][215]); // optionTiebreakersFideCorrect
	if ($tournament_fk[0] > $tournament["out"][213][0]) $tournament["out"][213][0] = $tournament_fk[0];
	$bem_int = 'SWM-Importdatei: '.$filename.';';
	
	If ($group) { 	
		
		// Mannschaftsturniere
		$typ = '3'; 														   // Mannschaft-Schweizer Sytem .TUM
		if (strpos($file,'.TUT') > 0 OR strpos($file,'.tut') > 0 ) $typ = '1'; // Mannschaft-Rundenturnier	 .TUT
		$tiebr1 = $tournament["out"][201][0];
		$tiebr2 = $tournament["out"][202][0];
		$tiebr3 = $tournament["out"][203][0];
		$str_params = '';
		$params = new clm_class_params($str_params);
		$params->set("optionTiebreakersFideCorrect",(string) $tournament["out"][213][0]);
		$params->set("color_order",'1');	
		$params->set("time_control",$tournament["out"][106][0]);
		$str_params = $params->params();
		$keyS = '`name`,`sid`,`runden_modus`,`durchgang`,`rnd`,`sl`,`published`,`stamm`,`ersatz`,`teil`,`runden`,`params`,`liga_mt`,`tiebr1`,`tiebr2`,`tiebr3`,`bem_int`,`man_sieg`,`man_remis`';
		$valueS = "'".$name."', ".$season.", '".$typ."', 1, 1, 0, 1, '"
			.$stamm."', '0','".$teil."','".$arunden."','".$str_params."', 1,".$tiebr1.",".$tiebr2.",".$tiebr3.", '".$bem_int."', 2, 1";

		$sql = "INSERT INTO #__clm_swt_liga (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br>sql: ";	var_dump($sql); }
		clm_core::$db->query($sql);
		$new_swt_tid = clm_core::$db->insert_id();
	} else { 		
	
		// Einzelturniere
		$typ = '1'; 														   // Einzel-Schweizer Sytem .TUN
		if (strpos($file,'.TUR') > 0 OR strpos($file,'.tur') > 0 ) $typ = '2'; // Einzel-Rundenturnier	 .TUR
		$keyS = '`sid`, `typ`, `dg`, `rnd`, `tl`, `published`, `name`, `bezirkTur`, `checked_out_time`, `bem_int`';
		$valueS = $season.", '".$typ."', 1, 1, 0, 1, '".$name."', '0', '1970-01-01 00:00:00', '".$bem_int."'";
		$str_params = '';
		$params = new clm_class_params($str_params);
		foreach ($tournament['out'] as $tour) {
if ($debug > 1) { echo "<br>tour: ";	var_dump($tour); }
			if ($tour[1][2] == '0') continue;
			if (substr($tour[1][2],0,6) == 'params') {
if ($debug > 1) { echo "<br>pour: ";	var_dump($tour); }
				$params->set(substr($tour[1][2],7),$tour[0]);	
			} else {
				$keyS .= ',`'.$tour[1][2].'`';
				$valueS .= ",'".clm_core::$db->escape($tour[0])."'";
			}
		}
		$params->set("playerViewDisplaySex",'0');	
		$params->set("playerViewDisplayBirthYear",'0');	
		if ($update) {
			$old_params = new clm_class_params($uturnier[0]->params);
			$params->set("qualiUp",$old_params->get("qualiUp",'0'));
			$params->set("qualiUpPoss",$old_params->get("qualiUpPoss",'0'));
			$params->set("qualiDown",$old_params->get("qualiDown",'0'));
			$params->set("qualiDownPoss",$old_params->get("qualiDownPoss",'0'));
			$params->set("displayRoundDate",$old_params->get("displayRoundDate",'0'));
			$params->set("displayPlayerSnr",$old_params->get("displayPlayerSnr",'0'));
			$params->set("displayPlayerTitle",$old_params->get("displayPlayerTitle",'0'));
			$params->set("displayPlayerClub",$old_params->get("displayPlayerClub",'0'));
			$params->set("displayPlayerRating",$old_params->get("displayPlayerRating",'0'));
			$params->set("displayPlayerElo",$old_params->get("displayPlayerElo",'0'));
			$params->set("displayPlayerFideLink",$old_params->get("displayPlayerFideLink",'0'));
			$params->set("displayPlayerFederation",$old_params->get("displayPlayerFederation",'0'));
			$params->set("displayTlOK",$old_params->get("displayTlOK",'0'));
			$params->set("pgnInput",$old_params->get("pgnInput",'0'));
			$params->set("pgnPublic",$old_params->get("pgnPublic",'0'));
			$params->set("pgnDownload",$old_params->get("pgnDownload",'0'));
			$params->set("playerViewDisplaySex",$old_params->get("playerViewDisplaySex",'0'));
			$params->set("playerViewDisplayBirthYear",$old_params->get("playerViewDisplayBirthYear",'0'));
		}
		$str_params = $params->params();
if ($debug > 2) { echo "<br>str_params: ";	var_dump($str_params); }
		$keyS .= ', `params`';
		$valueS .= ", '".$str_params."'";
if ($debug > 2) { echo "<br>params: ";	var_dump($params); }

		$sql = "INSERT INTO #__clm_swt_turniere (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br>sql: ";	var_dump($sql); }
		clm_core::$db->query($sql);
		$new_swt_tid = clm_core::$db->insert_id();
	}	
	
if ($debug > 0) { echo "<br><br><b>neue Turnier-ID (swm): ".$new_swt_tid."</b>"; }
//die('Ende Turnierdaten');
	$paramuseAsTWZ = $tournament["out"][24][0];


//	Rundendaten -> Tabelle clm_turniere_rnd_termine / clm_runden_termine
if ($debug > 0) { echo "<br><br>-- Rundendaten --";	}
	$paarprorunde = array();
	$tableprorunde = array();
	$slength = 0;
	for ($i = 0; $i < $tournament["out"][1][0]; $i++) {
		$tab_record = zzparse_interpret($contents, 'round', $group, ($apos[3]+$slength),($apos[4]-$apos[3]-$slength),$debug);
		$slength += $tab_record['length'];
if ($debug > 1) { echo "<br>tab_record: ";	var_dump($tab_record); }
		$startzeit = $tab_record['out'][9001][0];
		$startzeit = clm_core::$load->make_valid($startzeit, 15, "00:00");
		$rdatum = $tab_record['out'][9002][0];
		if ($rdatum == '0000-00-00') $rdatum = '1970-01-01';

		If ($group) { 	// Mannschaftsturniere
			$keyS = '`sid`, `name`, `swt_liga`, `nr`, `published`, `datum`, `startzeit`';
			$valueS = $season.", 'Runde ".($i+1)."', ".$new_swt_tid.", ".($i+1).", 1, '".$rdatum."', '".$startzeit."'";
			foreach ($tab_record['out'] as $tab) {
if ($debug > 2) { echo "<br>tab: ";	var_dump($tab); }
				if ($tab[1][2] == '0') continue;
				$keyS .= ',`'.$tab[1][2].'`';
				//$valueS .= ",'".clm_core::$db->escape($tab[0])."'";		
				$valueS .= ",'".$tab[0]."'";		
			}

			$sql = "INSERT INTO #__clm_swt_runden_termine (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br>sql: ";	var_dump($sql); }
			clm_core::$db->query($sql);
		} else { 		// Einzelturniere
			$keyS = '`sid`, `name`, `swt_tid`, `dg`, `nr`, `published`, `datum`, `startzeit`';
			$valueS = $season.", 'Runde ".($i+1)."', ".$new_swt_tid.", 1, ".($i+1).", 1, '".$rdatum."', '".$startzeit."'";
			foreach ($tab_record['out'] as $tab) {
if ($debug > 2) { echo "<br>tab: ";	var_dump($tab); }
				if ($tab[1][2] == '0') continue;
				$keyS .= ',`'.$tab[1][2].'`';
				//$valueS .= ",'".clm_core::$db->escape($tab[0])."'";		
				$valueS .= ",'".$tab[0]."'";		
			}
			$sql = "INSERT INTO #__clm_swt_turniere_rnd_termine (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br>sql: ";	var_dump($sql); }
			clm_core::$db->query($sql);
		}
		$paarprorunde[$i+1] = $tab_record['out'][9003][0];
		$tableprorunde[$i+1] = $tab_record['out'][9004][0];
	}
if ($debug > 1) { echo "<br>paarprorunde: ";	var_dump($paarprorunde); }
if ($debug > 1) { echo "<br>tableprorunde: ";	var_dump($tableprorunde); }
//die('Ende Rundendaten');


	//	Spielerdaten -> Tabelle clm_turniere_tlnr
if ($debug > 0) { echo "<br><br>-- Spielerdaten --";	}
	$slength = 0;
	// Letzte vergebene Mitgl-Nr für Verein "-1"
//	$sql = "SELECT MAX(Mgl_Nr) as mmax FROM #__clm_swt_dwz_spieler"
//			." WHERE ZPS = '-1' ";
//	$mobj = clm_core::$db->loadObject($sql);
//	$max_mglnr = $mobj->mmax;
//if ($debug > 0) { echo "<br>max_mglnr: $max_mglnr ";	var_dump($max_mglnr); }
	$sql = "DELETE FROM #__clm_swt_dwz_spieler"
			." WHERE ZPS = '-1' ";
	clm_core::$db->query($sql);

//die('delete');
	$mglnr = 0;
	$a_team = array();
	$a_team[0] = 0;
	for ($i = 0; $i < $tournament["out"][4][0]; $i++) {
//if ($debug > 0) { echo "<br>i: ";	var_dump($i); }
		$tab_record = zzparse_interpret($contents, 'player', $group, ($apos[4]+$slength),($apos[5]-$apos[4]-$slength),$debug);
//if ($debug > 0) { echo "<br>tab: ";	var_dump($tab_record); } 
//if ($debug > 0) { echo "<br>tab: ";	var_dump($tab_record['out'][2021]); }
		// Ergänzen der Startnummer
		$tab_record['out'][2060][0] = $i +1;
		$tab_record['out'][2060][1][0] = 'int';
		$tab_record['out'][2060][1][1] = 2060;
		$tab_record['out'][2060][1][2] = 0;
if ($debug > 0) {echo "<br>2060 ".$lang->t2060.': '.$tab_record['out'][2060][0];}
//if ($debug > 0) { echo "<br>tab: ";	var_dump($tab_record['out'][2060]); }
		$slength += $tab_record['length'];
		if($paramuseAsTWZ == 0) { 
			if ($tab_record['out'][2003][0] >= $tab_record['out'][2004][0]) { $twz = $tab_record['out'][2003][0]; } //FIDEelo; 
			else { $twz = $tab_record['out'][2004][0]; } //start_dwz;  
		} elseif ($paramuseAsTWZ == 1) {
			if ($tab_record['out'][2004][0]  > 0) { $twz = $tab_record['out'][2004][0]; } //start_dwz; 
			else { $twz = $tab_record['out'][2003][0]; } //FIDEelo; 
		} elseif ($paramuseAsTWZ == 2) {
			if ($tab_record['out'][2003][0]  > 0) { $twz = $tab_record['out'][2003][0]; } //FIDEelo; 
			else { $twz = $tab_record['out'][2004][0]; } //start_dwz;
		} else $twz = 0;
		// Feld Typ überschreibt Feld Titel, falls dieses leer ist													
		if ($tab_record['out'][2002][0] == '') $tab_record['out'][2002][0] = $tab_record['out'][2045][0];
		$titel = $tab_record['out'][2002][0];
		if (strlen($titel) > 3) $titel = '';
if ($debug > 2) { echo "<br>paramuseAsTWZ: $paramuseAsTWZ  twz: $twz  tab_player: ";	var_dump($tab_record); }
		$tab_record['out'][2008][0] = substr($tab_record['out'][2008][0],0,4); //Geburtsjahr
		$geburtsjahr = $tab_record['out'][2008][0];
		$name = $tab_record['out'][2040][0].",".$tab_record['out'][2041][0];
		$PKZ = $tab_record['out'][2034][0];
		$dwz = (integer) $tab_record['out'][2004][0];
		$elo = (integer) $tab_record['out'][2003][0];
		$fideid = (integer) $tab_record['out'][2033][0];
		$land = $tab_record['out'][2006][0];
		if (strlen($land) > 3) $land = '';
		$FIDEcco = $tab_record['out'][2007][0];
		if (strlen($FIDEcco) > 3) $FIDEcco = '';

		If ($group) { 	// Mannschaftsturniere
			$manid = (integer) $tab_record['out'][2061][0];
			$brett = (integer) $tab_record['out'][2062][0];
			// Tabelle clm_dwz_spieler
			$mglnr++;
			$a_team[$mglnr] = $manid;
			$keyS = '`sid`, `PKZ`, `ZPS`, `Mgl_Nr`, `Spielername`, `Geburtsjahr`, `DWZ`, `FIDE_elo`, `FIDE_Titel`, `FIDE_ID`, `FIDE_Land`';
			$valueS = $season.", '".$PKZ."', '-1', ".$mglnr.", '".$name."', '".$geburtsjahr."', ".$dwz.", ".$elo.", '".$titel."', ".$fideid.", '".$land."'";
			$sql = "INSERT INTO #__clm_swt_dwz_spieler (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br>sql: ";	var_dump($sql); }
			clm_core::$db->query($sql);
//die();			
			// Tabelle clm_meldeliste_spieler
			$keyS = '`spielerid`, `sid`, `swt_id`, `man_id`, `snr`, `mgl_nr`, `PKZ`, `zps`, `start_dwz`, `FIDEelo`';
			$valueS = $mglnr.", ".$season.", ".$new_swt_tid.", ".$manid.", ".$brett.", ".$mglnr.", '".$PKZ."', '-1', ".$dwz.", ".$elo;
			$sql = "INSERT INTO #__clm_swt_meldeliste_spieler (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br>sql: ";	var_dump($sql); }
			clm_core::$db->query($sql);
//die();			
		} else { 		// Einzelturniere	
			$keyS = '`sid`, `swt_tid`, `twz`, `name`, `tlnrStatus`, `snr`, `FIDEcco`, `titel`';
			$valueS = $season.", ".$new_swt_tid.", ".$twz.", '".$name."', '1', ".($i+1).", '".$FIDEcco."', '".$titel."'";
			foreach ($tab_record['out'] as $tab) {
if ($debug > 2) { echo "<br>tab: ";	var_dump($tab); }
				if ($tab[1][2] == '0') continue;
				$keyS .= ',`'.$tab[1][2].'`';
				//$valueS .= ",'".clm_core::$db->escape($tab[0])."'";		
				$valueS .= ",'".$tab[0]."'";		
			}
			$sql = "INSERT INTO #__clm_swt_turniere_tlnr (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br>tlnr-sql: ";	var_dump($sql); }
			clm_core::$db->query($sql);
		}
	}
if ($debug > 0 AND $group) { echo "<br>a_team: ";	var_dump($a_team); }
//die('<br>Ende Spielerdaten');


	//	Einzel-Ergebnisdaten -> Tabelle clm_turniere_rnd_spl / clm_rnd_spl
if ($debug > 0) { echo "<br><br>-- Einzel-Ergebnisdaten --";	}
	$slength = 0;
	$runde = 1;
	$brett = 0;
	$paar = 1;
	$epaar = 0;
	if ($group) {
		$m_punkte = array();
		for ($i = 1; $i <= $arunden; $i++) {
			for ($j = 0; $j <= $teil; $j++) {	
				$m_punkte[$i][$j] = 0;
			}
		}
	}
	//for ($i = 0; $i < $tournament["out"][1][0] * $paar_zahl; $i++) {
	for ($i = 0; $i < 5000; $i++) {
		$tab_record = zzparse_interpret($contents, 'individual_pairing', $group, ($apos[5]+$slength),($apos[6]-$apos[5]-$slength),$debug);
		$slength += $tab_record['length'];
if ($debug > 1) { echo "<br>tab_record: $i ";	var_dump($tab_record); }
if ($debug > 0) { echo " ( Runde: $runde  Brett: $brett ) ";	}
		$spieler = $tab_record['out'][4007][0];
		$gegner = $tab_record['out'][4008][0];
		$heim = 1;
		$ergebnis = transcode_ergebnis($tab_record['out'][4002][0],$heim,$gegner);
		//if ($gegner > 60000) $gegner = 0;
		if ($gegner > 16000) $gegner = 0;
if ($debug > 1) { echo "<br>runde: $runde  brett: $brett  ergebnis: $ergebnis  -- "; var_dump($ergebnis);	}
		if (($spieler == 0 OR $gegner == 0) AND $ergebnis == 7) continue;
		if ($ergebnis == 99) continue;
//die();

		If ($group) { 	// Mannschaftsturniere
			$brett++;
			$epaar++;
			if ($brett > $stamm) {
				$brett = 1;
				$paar++;
			}
			if ($epaar > $paarprorunde[$runde]) {
				$runde++;
				$epaar = 1;
				$paar = 1;
			} 
			if ($runde > $arunden) break;
			if ($brett % 2 != 0) { $heim = 1; }
			else { $heim = 0; } 
			$ergebnis = transcode_ergebnis($tab_record['out'][4002][0],$heim,$gegner);
			$weiss = 0;
			if ($ergebnis < 3) $kampflos = 0; else $kampflos = 1;
			if (is_null($ergebnis)) $ergebnis = 8;
			if ($heim == 1) {
				if ($ergebnis == 1 OR $ergebnis == 5 ) $punkte = '1';
				elseif ($ergebnis == 2) $punkte = '0.5';
				else $punkte = '0';
			} else {
				if ($ergebnis == 0 OR $ergebnis == 4 ) $punkte = '1';
				elseif ($ergebnis == 2) $punkte = '0.5';
				else $punkte = '0';
			}
			$tln_nr = $a_team[$spieler];
if ($debug > 0) { echo " ( Runde: $runde  Paar: $paar  Brett: $brett  Teilnehmer: $tln_nr  Punkte: $punkte) ";	}
			$gtln_nr = $a_team[$gegner];
			$m_punkte[$runde][$tln_nr] += $punkte;
			$keyS = '`sid`, `swt_id`, `dg`, `runde`, `paar`, `tln_nr`, `brett`, `heim`, `weiss`, `spieler`, `zps`, `gegner`, `gzps`, `ergebnis`, `kampflos`, `punkte`, `gemeldet`';
			if (!is_null($ergebnis))
				$valueS = $season.",".$new_swt_tid.",1,".$runde.",".$paar.",".$tln_nr.",".$brett.",".$heim.",".$weiss.",".$spieler.", '-1', ".$gegner.", '-1', ".$ergebnis.", ".$kampflos.", '".$punkte."', ".$auser;
			else 
				$valueS = $season.",".$new_swt_tid.",1,".$runde.",".$paar.",".$tln_nr.",".$brett.",".$heim.",".$weiss.",".$spieler.", '-1', ".$gegner.", '-1', NULL, ".$kampflos.", '".$punkte."', ".$auser;
			$sql = "INSERT INTO #__clm_swt_rnd_spl (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br>sql: ";	var_dump($sql); }
			clm_core::$db->query($sql);
//die();
			$weiss = 1;
//			$ergebnis = transcode_ergebnis($tab_record['out'][4002][0],$heim,$gegner);
			if ($brett % 2 != 0) { $heim = 0; }
			else { $heim = 1; } 
			if ($ergebnis < 3) $kampflos = 0; else $kampflos = 1;
			if ($heim == 1) {
				if ($ergebnis == 1 OR $ergebnis == 5 ) $punkte = '1';
				elseif ($ergebnis == 2) $punkte = '0.5';
				else $punkte = '0';
			} else {
				if ($ergebnis == 0 OR $ergebnis == 4 ) $punkte = '1';
				elseif ($ergebnis == 2) $punkte = '0.5';
				else $punkte = '0';
			}
			$m_punkte[$runde][$gtln_nr] += $punkte;
if ($debug > 0) { echo " ( Runde: $runde  Paar: $paar  Brett: $brett  Teilnehmer: $gtln_nr  Punkte: $punkte) ";	}
			if (!is_null($ergebnis))
				$valueS = $season.",".$new_swt_tid.",1,".$runde.",".$paar.",".$gtln_nr.",".$brett.",".$heim.",".$weiss.",".$gegner.", '-1', ".$spieler.", '-1', ".$ergebnis.", ".$kampflos.", '".$punkte."', ".$auser;
			else 
				$valueS = $season.",".$new_swt_tid.",1,".$runde.",".$paar.",".$gtln_nr.",".$brett.",".$heim.",".$weiss.",".$gegner.", '-1', ".$spieler.", '-1', NULL, ".$kampflos.", '".$punkte."', ".$auser;
			$sql = "INSERT INTO #__clm_swt_rnd_spl (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br>sql: ";	var_dump($sql); }
			clm_core::$db->query($sql);
//if ($i > 6) die();

		} else { 		// Einzelturniere	
			if ($brett < $paarprorunde[$runde]) {
				$brett++;
			} else {
				$runde++;
				if ($runde > $tournament["out"][1][0]) break;
				$brett = 1;
			}
if ($debug > 0) { echo " ( Runde: $runde  Brett: $brett ) ";	}
			$heim = 1;
			$keyS = '`sid`, `swt_tid`, `dg`, `runde`, `brett`, `tln_nr`, `heim`, `spieler`, `gegner`, `ergebnis`, `pgn`';
			if (!is_null($ergebnis))
				$valueS = $season.", ".$new_swt_tid.", 1,".$runde.", ".$brett.", ".$spieler.", ".$heim.",".$spieler.", ".$gegner.", ".$ergebnis.", ''";
			else 
				$valueS = $season.", ".$new_swt_tid.", 1,".$runde.", ".$brett.", ".$spieler.", ".$heim.",".$spieler.", ".$gegner.", NULL, ''";
			$sql = "INSERT INTO #__clm_swt_turniere_rnd_spl (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br>sql: ";	var_dump($sql); }
			clm_core::$db->query($sql);
			$heim = 0;
			$ergebnis = transcode_ergebnis($tab_record['out'][4002][0],$heim,$gegner);
			$keyS = '`sid`, `swt_tid`, `dg`, `runde`, `brett`, `tln_nr`, `heim`, `spieler`, `gegner`, `ergebnis`, `pgn`';
			if (!is_null($ergebnis))
				$valueS = $season.", ".$new_swt_tid.", 1,".$runde.", ".$brett.", ".$gegner.", ".$heim.", ".$gegner.", ".$spieler.", ".$ergebnis.", ''";
			else
				$valueS = $season.", ".$new_swt_tid.", 1,".$runde.", ".$brett.", ".$gegner.", ".$heim.", ".$gegner.", ".$spieler.", NULL, ''";
			$sql = "INSERT INTO #__clm_swt_turniere_rnd_spl (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br>sql: ";	var_dump($sql); }
			clm_core::$db->query($sql);
		}
	}
//die('Ende Einzelergebnisse');


	//	Mannschaftsdaten -> Tabelle clm_mannschaften
  if ($group) { 	// Mannschaftsturniere
if ($debug > 0) { echo "<br><br>-- Mannschaftsdaten --";	}
	$bem_int = 'Import durch SWM-Datei';
	$slength = 0;
	for ($i = 0; $i < $tournament["out"][2][0]; $i++) {
//if ($debug > 0) { echo "<br>i: ";	var_dump($i); }
		$tab_record = zzparse_interpret($contents, 'team', $group, ($apos[6]+$slength),($apos[7]-$apos[6]-$slength),$debug);
		// Ergänzen der Startnummer
		$tab_record['out'][3060][0] = $i +1;
		$tab_record['out'][3060][1][0] = 'int';
		$tab_record['out'][3060][1][1] = 3060;
		$tab_record['out'][3060][1][2] = 0;
if ($debug > 1) {echo "<br>3060 ".$lang->t3060.': '.$tab_record['out'][3060][0];}
		$slength += $tab_record['length'];
		
		$name = $tab_record['out'][3040][0];
		$land = (integer) $tab_record['out'][3043][0];
		$tlnnr = (integer) $tab_record['out'][3060][0];
		$mannr = (integer) $tab_record['out'][3060][0];
		$keyS = '`sid`, `name`, `swt_id`, `zps`, `liste`, `man_nr`, `tln_nr`, `bem_int`, `published`';
		$valueS = $season.", '".$name."', ".$new_swt_tid.", '-1', 62, ".$mannr.", ".$tlnnr.", '".$bem_int."', 1";
		$sql = "INSERT INTO #__clm_swt_mannschaften (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br>sql: ";	var_dump($sql); }
		clm_core::$db->query($sql);
	}
  }
//die('Ende Mannschaftsdaten');

	
	//	Mannschafts-Paarungen -> Tabelle clm_rnd_man
  if ($group) { 	// Mannschaftsturniere
    $a_man_paar = array();
if ($debug > 0) { echo "<br><br>-- Mannschafts-Paarungen --";	}
	$slength = 0;
	$runde = 1;
	$table = 0;
	// Test auf Einzelergebnisse
	$sum_m_punkte = 0;
	array_walk_recursive($m_punkte, function($item, $key) use(&$sum_m_punkte) {
			$sum_m_punkte += $item; }
		, $sum_m_punkte); 
if ($debug > 0) { echo "<br>Summe Einzelergebnisse  $sum_m_punkte "; }
if ($debug > 0) if ($sum_m_punkte == 0) { echo "<br>Einzelergebnisse wurden nicht eingegeben! "; }
	//for ($i = 0; $i < $tournament["out"][1][0] * $paar_zahl; $i++) {
	for ($i = 0; $i < 5000; $i++) {
		$tab_record = zzparse_interpret($contents, 'team_pairing', $group, ($apos[7]+$slength),($apos[8]-$apos[7]-$slength),$debug);
		$slength += $tab_record['length'];
if ($debug > 1) { echo "<br>tab_record: $i ";	var_dump($tab_record); }
		//if ($i < ($runde * $paar_zahl)) {
		if ($table < $tableprorunde[$runde]) {
			$table++;
		} else {
			$runde++;
			if ($runde > $tournament["out"][1][0]) break;
			$table = 1;
		}
		$spieler = $tab_record['out'][5007][0];
		$gegner = $tab_record['out'][5008][0];
		$brettpunkte_heim = $tab_record['out'][5009][0] / 2;
		$brettpunkte_gast = $tab_record['out'][5010][0] / 2;
if ($debug > 0) { echo " ( Runde: $runde  Tisch: $table  Spieler: $spieler  Gegner: $gegner  Brettpunkte Heim $brettpunkte_heim  Brettpunkte Gast $brettpunkte_gast) ";	}
if ($debug > 1) { echo "<br>m_punkte: "; var_dump($m_punkte); }
		$heim = 1;

		if ($gegner < 16000) {			// normaler Kampf
			if ($sum_m_punkte == 0) $h_punkte = $brettpunkte_heim;
			else $h_punkte = $m_punkte[$runde][$spieler];
			if ($sum_m_punkte == 0) $g_punkte = $brettpunkte_gast;		
			else $g_punkte = $m_punkte[$runde][$gegner];
			if ($h_punkte > $g_punkte) $man_punkte = 2;
			elseif ($h_punkte < $g_punkte) $man_punkte = 0;
			else $man_punkte = 1;
			$kampflos = 0;
		} elseif ($gegner == 65535) {		// spielfrei für Heimmannschaft
			$h_punkte = $stamm;
			$g_punkte = 0;
			$man_punkte = 2;
			$kampflos = 1;
			$gegner = 0;
		} elseif ($gegner == 65534) {		// Heimmannschaft nicht ausgelost
			$h_punkte = 0;
			$g_punkte = 0;
			$man_punkte = 0;
			$kampflos = 1;
			$gegner = 0;
		} else {		// sonstiges zur Absicherung
			$h_punkte = 0;
			$g_punkte = 0;
			$man_punkte = 0;
			$kampflos = 0;
			$spieler = 0;
			$gegner = 0;
		}
if ($debug > 1) { echo "<br><br>runde: $runde  brett: $brett  ergebnis: $ergebnis  -- "; var_dump($ergebnis);	}

		$keyS = '`sid`, `swt_id`, `dg`, `runde`, `paar`, `heim`, `tln_nr`, `gegner`, `kampflos`, `brettpunkte`, `manpunkte`, `comment`, `icomment`';
		$valueS = $season.",".$new_swt_tid.", 1,".$runde.", ".$table.", ".$heim.",".$spieler.", ".$gegner.", ".$kampflos.", '".$h_punkte."', ".$man_punkte.", '', ''";
		$sql = "INSERT INTO #__clm_swt_rnd_man (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br><br>sql: ";	var_dump($sql); }
		clm_core::$db->query($sql);
		$a_man_paar[1][$runde][$table][$heim] = $spieler;
//die();
		$heim = 0;
		if ($gegner > 0) $man_punkte = 2 - $man_punkte;
		else $man_punkte = 0;
		$keyS = '`sid`, `swt_id`, `dg`, `runde`, `paar`, `heim`, `tln_nr`, `gegner`, `kampflos`, `brettpunkte`, `manpunkte`, `comment`, `icomment`';
		$valueS = $season.",".$new_swt_tid.", 1,".$runde.", ".$table.", ".$heim.",".$gegner.", ".$spieler.", ".$kampflos.", '".$g_punkte."', ".$man_punkte.", '', ''";
		$sql = "INSERT INTO #__clm_swt_rnd_man (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br>sql: ";	var_dump($sql); }
		clm_core::$db->query($sql);
		$a_man_paar[1][$runde][$table][$heim] = $gegner;
//die();
		}
//die('Ende Mannschaftspaarungen');

		// Korrektur Einzel-Paarungen
		$select_query = " SELECT * FROM #__clm_swt_rnd_spl
						WHERE swt_id = ".$new_swt_tid." AND tln_nr = 0";
		$paarungen = clm_core::$db->loadObjectList($select_query);

		foreach($paarungen as $paar){
			if (isset($a_man_paar[1][$paar->runde][$paar->paar][$paar->heim]))
				$paar->tln_nr = $a_man_paar[1][$paar->runde][$paar->paar][$paar->heim];

			if(!clm_core::$db->updateObject('#__clm_swt_rnd_spl',$paar,'id')) {
				return false;
			}
		}
//die('Ende Korrektur Einzel-Paarungen');
	}
	
	
	if (!$test) { 
		$result = clm_core::$api->db_swt_to_clm($new_swt_tid,$turnier,$group,$update);
		if ($debug > 1) { echo "<br>result:"; var_dump($result); }
		$new_tid = $result[1];
	} else {
		$new_tid = 0;
		echo "<br><br>Test - Ende des Protokolls!<br><br>"; 
	}
	return array(true, "m_SWMImportSuccess",$new_tid); 

}

/**
 * Interprets binary data depending on structure
 *
 * @param string $binary binary data
 * @param string $part name of structural file for this part
 * @param int $start (optional, looks at just a part of the data)
 * @param int $end (optional, looks at just a part of the data)
 * @return array data 
 *		array out: field title => value
 *		array bin: begin, end and type (for development)
 */
function zzparse_interpret($binary, $part, $group, $start = 0, $end = false, $debug = 0) {
	//if ($part == 'general') $debug = 3; 
if ($debug > 1) { echo "<br>part: $part   start: $start   end: $end"; }
    $lang = clm_core::$lang->swm_import;
if ($debug > 3) { echo "<br>binary: $binary   <br>ende"; }
	if ($end) $binary = substr($binary, $start, $end);
	$data = array();
	$data['out'] = array();
	if ($group) $structure = zzparse_structure_t($part);  // s-single = Einzelturnier
	else $structure = zzparse_structure_s($part);		  // t-team = Mannschaftswettbewerb
if ($debug > 3) { echo "<br>structure: "; var_dump($structure); } //die(); 
if ($debug > 3) { echo "<br>binary: $binary   <br>ende"; }
//	$test1 = bin2hex(substr($binary, 0, 20)); 
//if ($debug > 0) { echo "<br>test1: $test1"; }
 
	$istart = 0; //$start;
	$i	= 0;
if ($debug > 1) { echo "<br>0istart: $istart"; }
	foreach ($structure as $line) {
if ($debug > 1) { echo "<br><br>line $i:"; var_dump($line); }
		$i++;
		switch ($line[0]) {
		case 'asc':
			// Content is in ASCII format
			// cuts starting byte with value 00 which marks the end of string, 
			// rest is junk data
			$data['out'][$line['content']] = zzparse_tonullbyte($substring);
			break;
		case 'vch':
if ($debug > 2) { echo "<br>vch-istart: $istart"; }
			// Content is in ASCII format
			// first byte with length 
//			$test = bin2hex(substr($binary, $istart, 4)); 
//if ($debug > 0) { echo "<br>test: $test"; }
			$length = hexdec(bin2hex(substr($binary, $istart, 1)));
if ($debug > 1) { echo "   1length: $length"; }
			$length256 = hexdec(bin2hex(substr($binary, $istart+1, 1)));
			$length += 256 * $length256;
if ($debug > 1) { echo "   2length: $length"; }
/*			if ($length < 0 OR $length > 256) { 
				$lenght = 0;
				if ($debug > 2) echo "<br>length-error  byte:$istart";
			}
*/			$istart++; $istart++;
			$substring = substr($binary, $istart, 2 * $length);
if ($debug > 2) { echo "<br>substring: $substring"; }
			$istart += (2 * $length);		
if ($debug > 2) { echo "   vch-istart: $istart"; }
//die();
			$len = strlen($substring);
if ($debug > 2) { echo "<br>len: $len   substring: $substring"; }
			$output = '';
			for ($a = 0; $a < $len; $a++) {
//if ($debug > 2) { echo "<br>a: $a   byte: ".bin2hex(substr($substring,$a,1)); }
				if (bin2hex(substr($substring,$a,1)) != '00') $output .= bin2hex(substr($substring,$a,1));
			} 
			$len = strlen($output);
if ($debug > 2) { echo "<br>len: $len   output: $output  line2: ".$line[2]; }
			$substring = hexToStr($output);
			if (($line[2] === 'startzeit') AND (strlen($substring) < 6)) $substring .= ':00';
			$data['out'][$line[1]][0] = addslashes(clm_core::$load->utf8encode($substring));
			$tnr = 't'.$line[1];
if ($debug > 1) echo "<br>".$tnr.'/'.$lang->{$tnr}.'/'.$substring.'/'.$data['out'][$line[1]][0];
			break;
		case 'ign':
if ($debug > 2) { echo "<br>1ign-istart: $istart"; }
			// Content to ignore
			// first parameter (begin) with length 
			$length = $line[1];
if ($debug > 2) { echo "<br>ign length: $length"; }
			if ($length < 0 OR $length > 256) { 
				$lenght = 0;
				if ($debug > 2) echo "<br>length-error  byte:$istart";
			}
			$substring = substr($binary, $istart, $length);
if ($debug > 2) { echo "<br>content: $substring"; }
			$istart += +$length;		
if ($debug > 2) { echo "<br>ign-istart: $istart"; }
			break;
			
		case 'bin':
			// Content is binary value
			$data['out'][$line['content']] = zzparse_binary($substring);
			break;

		case 'bib':
			// Content is binary value, big endian
			$data['out'][$line['content']] = zzparse_binary(strrev($substring));
			break;

		case 'b2a':
			// Content is hexadecimal value
			$data['out'][$line['content']] = hexdec(zzparse_binary($substring));
			break;

		case 'int':
			// Content is integer value, little endian
			$substring = substr($binary, $istart, 1);
			$istart++;
			$data['out'][$line[1]][0] = hexdec(bin2hex(($substring)));
			break;

		case 'inb':
			// Content is integer value, big endian
			$substring = substr($binary, $istart, 2);
			$substring1 = substr($binary, $istart, 1);
			$substring2 = substr($binary, $istart+1, 1);
			$d1 = hexdec(bin2hex(($substring1)));
			$d2 = hexdec(bin2hex(($substring2)));
if ($debug > 2) { echo "<br>1substring: $substring  d1: $d1  d2: $d2"; }
			$istart++; $istart++;
			$data['out'][$line[1]][0] = hexdec(bin2hex(strrev($substring)));
			break;

		case 'in4':
			// Content is integer value, big endian
			$substring = substr($binary, $istart, 4);
			$istart++; $istart++; $istart++; $istart++;
			$data['out'][$line[1]][0] = hexdec(bin2hex(strrev($substring)));
			break;

		case 'ind':
			// Content is integer value, date
			$substring = substr($binary, $istart, 1);
			$d1 = hexdec(bin2hex(($substring)));
			$istart++; 
			$substring = substr($binary, $istart, 1);
			$d2 = hexdec(bin2hex(($substring)));
			$istart++; 
			$substring = substr($binary, $istart, 1);
			$d3 = hexdec(bin2hex(($substring)));
			$istart++; 
			$substring = substr($binary, $istart, 1);
			$d4 = hexdec(bin2hex(($substring)));
			$istart++; 
			$lt = $d1 + ($d2 * 256)+ ($d3 * 256 * 256)+ ($d4 * 256 * 256 * 256);
			if ($lt > 0) {
				$rdatum = substr($lt,0,4).'-'.substr($lt,4,2).'-'.substr($lt,6,2);
			} else {
				$rdatum = '0000-00-00';
			}
if ($debug > 2) { echo "<br>datum: ohne datum  i: $i  d1: $d1  d2: $d2  d3: $d3  d4: $d4  lt: $lt "; }
			$data['out'][$line[1]][0] = $rdatum;
			break;

		case 'boo':
			// Content is boolean
			$substring = chop(zzparse_binary($substring));
			switch ($substring) {
				case 'FF': $data['out'][$line['content']] = 1; break;
				case '00': $data['out'][$line['content']] = 0; break;
				default: $data['out'][$line['content']] = NULL; break;
			}
			break;
		
		case 'sel':
			$area = strtolower($line['content']);
			if (preg_match('/^sel\:\d+/', $line['type'])) $area = str_replace('sel:', '', $line['type']);
			$area .= '-selection';
			$selection = zzparse_structure($area, 'replacements');
			$value = zzparse_binary($substring);
			if (!in_array($value, array_keys($selection))) {
				$data['out'][$line['content']] = 'UNKNOWN: '.$value;
			} else {
				$data['out'][$line['content']] = $selection[$value];
			}
			break;
		}
if ($debug > 1) { echo "<br>line ".($i-1).":"; var_dump($line); }
		if ($line[0] != 'ign')	$data['out'][$line[1]][1] = $line;
if ($debug > 1 AND $line[0] != 'ign') { echo "<br>content:"; var_dump($data['out'][$line[1]]); 
										echo "<br>total:"; var_dump($data['out']); }
if ($debug > 1 AND $line[0] != 'ign') {	
		foreach ($data['out'] as $key => $value) {
			$tnr = 't'.$key;
			echo "<br>value: $key ".$lang->{$tnr}.': '; var_dump($value);
		} }
	}
if ($debug > 0) {
		echo "<br>";
		foreach ($data['out'] as $key => $value) {
			$tnr = 't'.$key;
			echo "<br>$key ".$lang->{$tnr}.': '.$value[0];
		} }
//if ($part == 'general2') die('chhh2');
/*echo "<br>data: "; var_dump($data);
for ($d = 0; $d < count($data['bin']); $d++) { 
	echo "<br>FeldNr:".$data['bin'][$d]['content']."  ".$data['out'][$data['bin'][$d]['content']];
}
//die();
*/	$data['length'] = $istart;
//echo "<br>data:"; var_dump($data);
	return $data;
}

/**
 * Reads a definition file for a part of the file structure
 *
 * Definition files may have several comment lines starting with # at the start.
 * The following lines must each contain the following values, separated by a
 * tabulator: 
 *		string starting hexadecimal code, 
 *		string ending hexadecimal code,
 *		string type (asc, bin, b2a, boo)
 *		string content = description of what is the data about
 * @param string $part
 * @param string $type (optional, 'fields' or 'replacements')
 * @return array structure of part
 * @see zzparse_interpret()
 */
function zzparse_structure_s($part) {
	
	if ($part == 'general') 
		$fields = array(
					array('ign',104,0),
					array('vch',12,0),		// Turnierüberschrift Zeile 1
					array('vch',18,0),		// Turnierüberschrift Zeile 2
					array('vch',69,'bemerkungen'),
					array('vch',102,0),		// Turnierdirektor
					array('vch',101,0),		// Veranstalter
					array('vch',66,0),		// Ort/Land
					array('vch',67,0),		// Schiedsrichter
					//array('ign',2,0),
					array('vch',103,0),		// Name der pgn-Datei
					array('vch',110,0),		// Name der Video-Datei
					array('vch',65,0),		// Turniername				
					array('vch',66,0),		// Ort/Land
					array('vch',104,0),		// Name der Turnierdatei						
					array('vch',301,0),		// unklar301 ??
					array('vch',105,0),		// Altersgruppen
					array('vch',106,0),		// Zeitkontrolle						
					array('ign',2,0),		
					array('vch',111,0),		// Vorlage
					array('ign',6,0),		
					array('vch',112,0),		// Förderation						
					array('vch',68,0),		// Hauptschiedsrichter				
					array('vch',113,0),		// Föderations-Repräsendant
					array('vch',107,0),		// email-Adresse
					array('vch',108,0),		// Homepage
					array('vch',109,0),		// Land						
					array('vch',114,0),		// Fed1 für nationale Ratingermittlung						
					array('vch',115,0),		// Fed2 für nationale Ratingermittlung						
					array('vch',116,0),		// Fed3 für nationale Ratingermittlung						
					array('vch',117,0),		// Fed4 für nationale Ratingermittlung						
					array('ign',4,0)
					);
	if ($part == 'general2') 
		$fields = array(
					array('inb',1,'runden'),
					array('ign',17,0),
					array('inb',4,'teil'),
					array('ign',8,0),
					array('inb',201,'tiebr1'),
					array('inb',202,'tiebr2'),
					array('inb',203,'tiebr3'),
					array('inb',204,0),
					array('ign',34,0),
					array('ind',70,'dateStart'),
					array('ind',71,'dateEnd'),
					array('ign',572,0),
					array('int',205,0),
					array('int',213,0),
					array('ign',17,0),
					array('int',206,0),
					array('int',214,0),
					array('ign',17,0),
					array('int',207,0),
					array('int',215,0),
					array('ign',17,0),
					array('int',208,0),
					array('int',216,0),
					array('ign',162,0),
					array('int',24,0)
					);
	if ($part == 'round') 
		$fields = array(
					array('vch',9001,0),
					array('ign',30,0),
					array('ind',9002,0),
					array('ign',2,0),
					array('int',9003,0),		//Anzahl Einzel-Paarungen in Runde
					array('ign',1,0),
					array('int',9004,0),		//Anzahl Mannschafts-Paarungen in Runde
					array('ign',67,0)
					);
	if ($part == 'player') 
		$fields = array(
					array('vch',2040,0),
					array('vch',2041,0),
					array('vch',2044,0),
					array('vch',2000,0),
					array('vch',2002,0),
					array('vch',2034,'PKZ'),
					array('vch',2046,0),
					array('vch',2047,0),
					array('vch',2048,0),
					array('vch',2001,'verein'),
					array('vch',2006,0),		// Land
					array('vch',2045,0),		// Typ
					array('vch',2042,0),		// Gruppe
					array('ign',8,0),
//					array('vch',2007,'FIDEcco'),
					array('vch',2007,0),
					array('ign',32,0),
					array('inb',2003,'FIDEelo'),
					array('inb',2004,'start_dwz'),
					array('ign',2,0),
					array('ind',2008,'birthYear'),
					array('inb',2010,'zps'),
					array('ign',4,0),
					array('in4',2033,'FIDEid'),
					array('ign',28,0),
					array('int',2021,0),
					array('ign',53,0)
					);
	if ($part == 'individual_pairing') 
		$fields = array(
					array('inb',4007,'spieler'),
					array('inb',4008,'gegner'),
					array('int',4002,'ergebnis'),
					array('ign',16,0)
					);

	return $fields;
}
function zzparse_structure_t($part) {
	
	if ($part == 'general') 
		$fields = array(
					array('ign',104,0),
					array('vch',12,0),		// Turnierüberschrift Zeile 1
					array('vch',18,0),		// Turnierüberschrift Zeile 2
					array('vch',69,'bemerkungen'),
					array('vch',102,0),		// Turnierdirektor
					array('vch',101,0),		// Veranstalter
					array('vch',66,0),		// Ort/Land
					array('vch',67,0),		// Schiedsrichter
					//array('ign',2,0),
					array('vch',103,0),		// Name der pgn-Datei
					array('vch',110,0),		// Name der Video-Datei
					array('vch',65,0),		// Turniername				
					array('vch',66,0),		// Ort/Land
					array('vch',104,0),		// Name der Turnierdatei						
					array('vch',301,0),		// unklar301 ??
					array('vch',105,0),		// Altersgruppen
					array('vch',106,0),		// Zeitkontrolle						
					array('ign',2,0),		
					array('vch',111,0),		// Vorlage
					array('ign',6,0),		
					array('vch',112,0),		// Förderation						
					array('vch',68,0),		// Hauptschiedsrichter				
					array('vch',113,0),		// Föderations-Repräsendant
					array('vch',107,0),		// email-Adresse
					array('vch',108,0),		// Homepage
					array('vch',109,0),		// Land						
					array('vch',114,0),		// Fed1 für nationale Ratingermittlung						
					array('vch',115,0),		// Fed2 für nationale Ratingermittlung						
					array('vch',116,0),		// Fed3 für nationale Ratingermittlung						
					array('vch',117,0),		// Fed4 für nationale Ratingermittlung						
					array('ign',4,0)
					);
	if ($part == 'general2') 
		$fields = array(
					array('inb',1,'runden'),
					array('ign',17,0),
					array('inb',4,'teil'),
					array('ign',8,0),
					array('inb',201,'tiebr1'),
					array('inb',202,'tiebr2'),
					array('inb',203,'tiebr3'),
					array('inb',204,0),
					array('ign',10,0),
					array('inb',2,0),
					array('inb',3,0),
					array('ign',20,0),
					array('ind',70,'dateStart'),
					array('ind',71,'dateEnd'),
					array('ign',572,0),
					array('int',205,0),
					array('int',213,0),
					array('ign',17,0),
					array('int',206,0),
					array('int',214,0),
					array('ign',17,0),
					array('int',207,0),
					array('int',215,0),
					array('ign',17,0),
					array('int',208,0),
					array('int',216,0),
					array('ign',162,0),
					array('int',24,0)
					);
	if ($part == 'round') 
		$fields = array(
					array('vch',9001,0),
					array('ign',30,0),
					array('ind',9002,0),
					array('ign',2,0),
					array('int',9003,0),		//Anzahl Einzel-Paarungen in Runde
					array('ign',1,0),
					array('int',9004,0),		//Anzahl Mannschafts-Paarungen in Runde
					array('ign',67,0)
					);
	if ($part == 'player') 
		$fields = array(
					array('vch',2040,0),
					array('vch',2041,0),
					array('vch',2044,0),
					array('vch',2000,0),
					array('vch',2002,'titel'),
					array('vch',2034,'PKZ'),
					array('vch',2046,0),
					array('vch',2047,0),
					array('vch',2048,0),
					array('vch',2001,'verein'),
					array('vch',2006,0),		// Land
					array('vch',2045,0),		// Typ
					array('vch',2042,0),		// Gruppe
					array('ign',8,0),
					array('vch',2007,'FIDEcco'),
					array('ign',32,0),
					array('inb',2003,'FIDEelo'),
					array('inb',2004,'start_dwz'),
					array('ign',2,0),
					array('ind',2008,'birthYear'),
					array('inb',2010,'zps'),
					array('ign',4,0),
					array('in4',2033,'FIDEid'),
					array('inb',2061,0),
					array('inb',2062,0),
					array('ign',24,0),
					array('int',2021,0),
					array('ign',53,0)
					);
	if ($part == 'individual_pairing') 
		$fields = array(
					array('inb',4007,'spieler'),
					array('inb',4008,'gegner'),
					array('int',4002,'ergebnis'),
					array('ign',16,0)
					);
	if ($part == 'team') 
		$fields = array(
					array('vch',3040,0),
					array('vch',3041,0),
					array('vch',3042,0),
					array('vch',3043,0),
					array('vch',3044,0),
					array('ign',96,0)
					);
	if ($part == 'team_pairing') 
		$fields = array(
					array('inb',5007,'spieler'),
					array('inb',5008,'gegner'),
					array('inb',5009,0),
					array('inb',5010,0),
					array('ign',7,0)
					);

	return $fields;
}

	
function hexToStr($hex){
    $string='';
    for ($i=0; $i < strlen($hex)-1; $i+=2){
        $string .= chr(hexdec($hex[$i].$hex[$i+1]));
    }
    return $string;
}

function transcode_twz($line) {
	/* SWM -> CLM	(Beschreibung)
		1 ->  1	 Elo national
		2 ->  2	 Elo international
		3 ->  2	 Elo int. dann nat.
		4 ->  0	 Elomaximum (Nat,Int)
		5 ->  1  Elo national only
		6 ->  2	 Elo international only  */
	$clm_twz = array (0 => 0, 1 => 1, 2 => 2, 3 => 2, 4 => 0, 5 => 1, 6 => 2);
		
	$line[0] = $clm_twz[$line[0]];
	$line[1][2] = 'params_useAsTWZ';
	
    return $line;
}

function transcode_fidecorrect($line) {
	/* SWM -> CLM	(Beschreibung)
		0 ->  0	 keine Korrektur
		9 ->  1  ...
		13 ->  1	 virtueller Gegner  */
	//$clm_twz = array (0 => 0, 1 => 1, 2 => 2, 3 => 2, 4 => 0, 5 => 1, 6 => 1);		
	//$line[0] = $clm_twz[$line[0]];
	
	if ($line[0] == 13 OR $line[0] == 9) $line[0] = 1;
	else $line[0] = 0;
	$line[1][2] = 'params_optionTiebreakersFideCorrect';
	
    return $line;
}

function transcode_tiebr($group,$tournament,$line_nr,$debug) {
	/* SWM -> ET  	MT	(Beschreibung)
		1  ->  1   	5 Spielerpunkte
		5  ->  2	0 Manuelle Eingabe (Ordering)
		8  ->  2	0 Fidewertung (Progressive Score)
		9  ->  0	0 Fidewertung mit Streichwerten (Progressive Score)
		11 -> 25   25 Direktvergleich
		19 ->  3   23 Sonneborn-Berger mit Fide-Korr.  
		23 ->  1    0 Elosumme mit Streichwert
		36 ->  2    0 Elo-Durchschnitt
		37 ->  1    1 Buchholz
		42 ->  2    1 Spielerpunkte + Vorrunde  
		43 ->  1    1 Punkte bei Stichkamf
		44 ->  2    9 Matchpunkte
		52 ->  6   23 Sonneborn-Berger variabel
		53 ->  6    0 Mehr Schwarz-Partien
		54 ->  2    0 Recursive Eloperformance  
		55 ->  1    0 durchsch, recursive Eloperformance der Gegner
		59 ->  2    0 Eloperformance mit 2 Streichwerten
		60 ->  6    0 Eloperformance variabel
		61 ->  1    1 Arranz-Sytem
		68 ->  2    4 Anzahl Siege variabel
		70 ->  6    2 Summe Buchholz variabel
	*/
if ($debug > 1) { echo "<br>group:"; var_dump($group); } 		
	$clm_array = array (0 => 0, 1 => 0, 5 => 51, 8 => 1, 9 => 1, 11 => 25, 19 => 13, 23 => 16, 36 => 16,
		37 => 1, 42 => 0, 43 => 0, 44 => 0, 52 => 13, 54 => 0, 55 => 0, 59 => 0, 60 => 0, 61 => 0, 68 => 4, 70 => 2);
if ($debug > 1) { echo "<br>1tiebr linenr:"; var_dump($line_nr); } 		
if ($debug > 1) { echo "<br>1tiebr line:"; var_dump($tournament["out"][$line_nr]); }		
if ($debug > 1) { echo "<br>1tiebr line+4:"; var_dump($tournament["out"][$line_nr+4]); } 		
	// Aufsplitten Streichwertungen
	$streich_schwach = $tournament["out"][$line_nr+4][0] % 16;
	$streich_stark   = ($tournament["out"][$line_nr+4][0] - $streich_schwach) / 16;	
if ($debug > 1) { echo "<br>streich_schwach: $streich_schwach   streich_stark: $streich_stark"; } 	
//die();	
	$line = $tournament["out"][$line_nr];
	$swm = $line[0];
	if ($group) {
		if ($line[0] == 1) {		// Brettpunkte
			$line[0] = 5; }
		elseif ($line[0] == 2) {		// Buchholz ohne Parameter
			$line[0] = 1; }
		elseif ($line[0] == 11) {		// Direkter Vergleich
			$line[0] = 25; }
		elseif ($line[0] == 12) {		// Anzahl Siege
			$line[0] = 4; }
		elseif ($line[0] == 36) {		// Elo-Schnitt
			if ($streich_stark == 0 AND $streich_schwach == 0) { $line[0] = 6; }
			else { $line[0] = 0; } }
		elseif ($line[0] == 37) {		// Buchholz
			if ($streich_stark == 0 AND $streich_schwach == 0) { $line[0] = 1; }
			elseif ($streich_stark == 0 AND $streich_schwach == 1) { $line[0] = 11; } // mit 1 Streichwert
			elseif ($streich_stark == 1 AND $streich_schwach == 1) { $line[0] = 7; }	// mittlere Buchholz
			else { $line[0] = 0; } }
		elseif ($line[0] == 52) {		// Sonneborn-Berger
			if ($streich_stark == 0 AND $streich_schwach == 0) { $line[0] = 3; }
			elseif ($streich_stark == 0 AND $streich_schwach == 1) { $line[0] = 23; } // mit 1 Streichwert
			else { $line[0] = 0; } }
		elseif ($line[0] == 68) {		// Anzahl Siege
			if ($streich_stark == 0 AND $streich_schwach == 0) { $line[0] = 4; }
			else { $line[0] = 0; } }
		elseif ($line[0] == 70) {		// Buchholz Summe
			if ($streich_stark == 0 AND $streich_schwach == 0) { $line[0] = 2; }
			elseif ($streich_stark == 0 AND $streich_schwach == 1) { $line[0] = 12; } // mit 1 Streichwert
			else { $line[0] = 0; } }
		else { $line[0] = 0; } 
	} else {
		if ($line[0] == 2) {		// Buchholz ohne Parameter
			$line[0] = 1; }
		elseif ($line[0] == 11) {		// Direkter Vergleich
			$line[0] = 25; }
		elseif ($line[0] == 12) {		// Anzahl Siege
			$line[0] = 4; }
		elseif ($line[0] == 36) {		// Elo-Schnitt
			if ($streich_stark == 0 AND $streich_schwach == 0) { $line[0] = 6; }
			else { $line[0] = 0; } }
		elseif ($line[0] == 37) {		// Buchholz
			if ($streich_stark == 0 AND $streich_schwach == 0) { $line[0] = 1; }
			elseif ($streich_stark == 0 AND $streich_schwach == 1) { $line[0] = 11; } // mit 1 Streichwert
			elseif ($streich_stark == 1 AND $streich_schwach == 1) { $line[0] = 5; }	// mittlere Buchholz
			else { $line[0] = 0; } }
		elseif ($line[0] == 52) {		// Sonneborn-Berger
			if ($streich_stark == 0 AND $streich_schwach == 0) { $line[0] = 3; }
			elseif ($streich_stark == 0 AND $streich_schwach == 1) { $line[0] = 13; } // mit 1 Streichwert
			else { $line[0] = 0; } }
		elseif ($line[0] == 68) {		// Anzahl Siege
			if ($streich_stark == 0 AND $streich_schwach == 0) { $line[0] = 4; }
			else { $line[0] = 0; } }
		elseif ($line[0] == 70) {		// Buchholz Summe
			if ($streich_stark == 0 AND $streich_schwach == 0) { $line[0] = 2; }
			elseif ($streich_stark == 0 AND $streich_schwach == 1) { $line[0] = 12; } // mit 1 Streichwert
			else { $line[0] = 0; } }
		else { $line[0] = 0; } 
	}
if ($debug > 1) { echo "<br>9tiebr line:"; var_dump($line); } 	
if ($debug > 0) { echo "<br>feinwertung  swm: $swm  ->  clm: ".$line[0]; } 	
//die();	
	
    return $line;
}

function transcode_ergebnis($ergebnis, $heim, $gegner) {
	/* SWM -> CLM	(Beschreibung)
		0 ->  NULL	 Ergebnis offen
		1 ->  1	 Weißsieg
		2 ->  2	 Remis
		3 ->  0	 Schwarzsieg
		4 ->  5  Weißsieg kampflos
		5 ->  4  Schwarzsieg kampflos
		6 ->  6  beide verlieren kampflos
		8 ->  8	 spielfrei   
		9 ->  8	 kampflos gewonnen
		1 + g>16000 -> 11 bye 1.0 (nicht ausgelost)
		2 + g>16000 -> 12 bye 0.5 (nicht ausgelost)
		3 + g>16000 -> 13 bye 0.0 (nicht ausgelost)
	*/
	if ($gegner > 16000 AND $ergebnis == 1) $ergebnis = 11;
	if ($gegner > 16000 AND $ergebnis == 2) $ergebnis = 12;
	if ($gegner > 16000 AND $ergebnis == 3) $ergebnis = 13;
	if ($heim == 1) 
		$clm_array = array (0 => NULL, 1 => 1, 2 => 2, 3 => 0, 4 => 5, 5 => 4, 6 => 6, 8 => 8, 9 => 5, 11 => 11, 12 => 12, 13 => 13);
	else	
		$clm_array = array (0 => NULL, 1 => 0, 2 => 2, 3 => 1, 4 => 4, 5 => 5, 6 => 6, 8 => 8, 9 => 4, 11 => 0, 12 => 0, 13 => 0);
//echo "<br>ergebnis  swm: $ergebnis  ->  clm: ";  	
	$ergebnis = $clm_array[$ergebnis];
//echo $ergebnis;  	
	
    return $ergebnis;
}

?>
