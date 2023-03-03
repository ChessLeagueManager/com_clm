<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 *
 * Import einer Turnierdatei aus lichess  
*/
function clm_api_db_arena_import($arena_code,$season,$turnier,$group=false,$update=false,$test=false) {
    $lang = clm_core::$lang->imports;
	$group = false;
	if ($test) $debug = 1; else $debug = 0;
	$auser = (integer) clm_core::$access->getId(); // aktueller CLM-User
	if ($test)	echo "<br><br>Test - keine Übernahme der Daten ins CLM!"; 
	$new_ID = 0;
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;
	if ($countryversion =="de") { $time_diff = 1; } else { $time_diff = 0; }
if ($debug > 0) { echo "<br><br>-- allgemeine Daten --";	}
if ($debug > 0) echo "<br><br>datei-code: ".$arena_code;
//$file = 'D:\wamp64\www\hallearena\administrator/components/com_clm\swt\lichess_tournament_2021.02.05_0C3Z82xC_1-naumburger-onlineturnier.pgn';
//$file = 'https://lichess.org/api/tournament/Itf0NylM/games';

// Übername der Spieler aus result-Datei und Feststellung der Turnierart
	$file = 'https://lichess.org/api/tournament/'.$arena_code.'/results';
 		//echo "<br>end"; //die();
if ($debug > 0) echo "<br><br>datei: ".$file; 		//echo "<br>end"; //die();
if ($debug > 0) echo "<br>saison: ".$season; 	//echo "<br>end"; //die();
if ($debug > 0) echo "<br>turnier: ".$turnier; 	//echo "<br>end"; die();

	// Einlesen der Dateizeilen 
	// mit @ = Abschalten von Fehlermeldungen
	$lines = @file($file,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if ($debug > 1 ) { echo "<br>file: ".$file; 	var_dump($lines); echo "<br>end"; } //die(); }
	if (!$lines) { // code ist kein Turnier im Arena-Modus
		$file = 'https://lichess.org/api/swiss/'.$arena_code.'/results';		
		// mit @ = Abschalten von Fehlermeldungen
		set_time_limit(120);
		$lines = @file($file,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if ($debug > 1 ) { echo "<br>file: ".$file; 	var_dump($lines); echo "<br>end"; } //die(); }
		if (!$lines) { // code ist auch kein Turnier im CH-Modus
			return array(false, "e_ArenaCodeNoValid",$arena_code); 
		} else {
			if ($debug > 0) echo "<p>Tournament with CH-Mode! / Turnier im Schweizer Sytem!</p>";	
			$tur_mode = 'swiss';
		}
	} else {
		if ($debug > 0) echo "<p>Tournament with Arena-Mode! / Turnier im Arena-Modus!</p>";
		$tur_mode = 'arena';
	}
if ($debug > 1) { echo "<br>Turnier-Modus: ".$tur_mode; }
	// Spieler aus result-File
	$e_spieler = array();	// Spieler aus dem Ergenis/result-File
	foreach ($lines as $key => $line) {
		$a_line = json_decode($line);
if ($debug > 1 ) { echo "<br>a_line: "; 	var_dump($a_line); }
		$e_spieler[$a_line->username] = new stdclass();
		$e_spieler[$a_line->username]->snr = $key + 1;
		$e_spieler[$a_line->username]->rank = $a_line->rank;
		if (isset($a_line->score)) $e_spieler[$a_line->username]->score = $a_line->score;
		$e_spieler[$a_line->username]->elo = $a_line->rating;
		$e_spieler[$a_line->username]->name = addslashes($a_line->username);
		if (isset($a_line->tieBreak)) {
			$e_spieler[$a_line->username]->tieBreak = $a_line->tieBreak;
		} else {
			$e_spieler[$a_line->username]->tieBreak = 0;
		}
		if (isset($a_line->performance)) {
			$e_spieler[$a_line->username]->performance = $a_line->performance;
		} else {
			$e_spieler[$a_line->username]->performance = 0;
		}
		if (isset($a_line->absent)) {
			$e_spieler[$a_line->username]->absent = $a_line->absent;
		} else {		
			$e_spieler[$a_line->username]->absent = false;
		}		
	}
if ($debug > 1) { echo "<br><br>-- e_spieler --:";	var_dump($e_spieler); } 
//die();


	// Paarungen aus dem pgn-File
	$file = '';
	if ($tur_mode == 'arena')
		$file = 'https://lichess.org/api/tournament/'.$arena_code.'/games';
	if ($tur_mode == 'swiss')
		$file = 'https://lichess.org/api/swiss/'.$arena_code.'/games';
if ($debug > 1) echo "<br><br>datei: ".$file; 		//echo "<br>end"; //die();
	set_time_limit(120);
	$contents = @file_get_contents($file);
if ($debug > 1 AND !$contents) { echo "<br>file: ".$file; 	var_dump($contents); echo "<br>end"; } //die(); }
	if (!$contents) { // code ist kein Turnier
		return array(false, "e_ArenaCodeNoValid",$arena_code); 
	} 
	
if ($debug > 1) { echo "<br>contens: ".$contents; }
//die();

function pgn_element($contents, $uelement, $ustart, $debug = 0) {
if ($debug > 0) { echo "<br><br>-- uelement --".$uelement;	} //die();
	$upos = strpos(($contents),$uelement.' ',$ustart);
if ($debug > 0) { echo "<br><br>-- upos --".$upos;	} //die();
	if ($upos === false) return '';
	$length = strlen($uelement);
if ($debug > 0) { echo "<br><br>-- length --".$length;	} //die();
	$vstart = $upos + $length + 2;
if ($debug > 0) { echo "<br><br>-- vstart --".$vstart;	} //die();
	$vend = strpos(($contents),'"',$vstart+1);
	if ($vend === false) return '';
if ($debug > 0) { echo "<br><br>-- vend --".$vend;	} //die();
	$value = substr($contents,$vstart,$vend-$vstart);
if ($debug > 0) { echo "<br><br>-- value --".$value;	} //die();
	return $value;
}	
	$a_paar = array();
	$toFind = "Event";
	$start = 0;
	$i = 0;
	while(($pos = strpos(($contents),$toFind,$start)) !== false) {
		$i++;
if ($debug > 1) { echo '<br>'.$i.' Found '.$toFind.' at position '.$pos."\n"; }
		$start = $pos;
		$event = pgn_element($contents, 'Event', $start);
if ($debug > 0) { echo "<br><br>-- Event --".$event; } // var_dump($event); } //die();
		$site = pgn_element($contents, 'Site', $start);
if ($debug > 0) { echo "<br>-- Site --".$site;	} // var_dump($site); } //die();
		$date = pgn_element($contents, 'Date', $start);
if ($debug > 0) { echo "<br>-- Date --".$date;	} // var_dump($date); } //die();
		$white = pgn_element($contents, 'White', $start);
if ($debug > 0) { echo "<br>-- White --".$white;	} // var_dump($white); } //die();
		$black = pgn_element($contents, 'Black', $start);
if ($debug > 0) { echo "<br>-- Black --".$black;	} // var_dump($black); } //die();
		$result = pgn_element($contents, 'Result', $start);
if ($debug > 0) { echo "<br>-- Result --".$result;	} // var_dump($result); } //die();
		$utcdate = pgn_element($contents, 'UTCDate', $start);
if ($debug > 0) { echo "<br>-- UTCDate --".$utcdate;	} // var_dump($utcdate); } //die();
		$utctime = pgn_element($contents, 'UTCTime', $start);
if ($debug > 0) { echo "<br>-- UTCTime --".$utctime;	} // var_dump($utctime); } //die();
		$whiteelo = pgn_element($contents, 'WhiteElo', $start);
if ($debug > 0) { echo "<br>-- WhiteElo --".$whiteelo;	} // var_dump($whiteelo); } //die();
		$blackelo = pgn_element($contents, 'BlackElo', $start);
if ($debug > 0) { echo "<br>-- BlackElo --".$blackelo;	} // var_dump($blackelo); } //die();
		$a_paar[$i] = new stdclass();
		$a_paar[$i]->utcdate = $utcdate;
		$a_paar[$i]->utctime = $utctime;
		$a_paar[$i]->event = $event;
		$a_paar[$i]->site = $site;
		$a_paar[$i]->date = $date;
		$a_paar[$i]->white = $white;
		$a_paar[$i]->black = $black;
		$a_paar[$i]->result = $result;
		$a_paar[$i]->whiteelo = $whiteelo;
		$a_paar[$i]->blackelo = $blackelo;
		$a_paar[$i]->pgnnr = $i;
		
		if (($next = strpos(($contents),$toFind,$start+200)) !== false) {
			$a_pgn[$i] = substr($contents,($start-1),($next-$start));
		} else {
			$a_pgn[$i] = substr($contents,($start-1));
		}
        $start = $pos+200; // start searching from next position.
	}
if ($debug > 1) { echo "<br><br>-- a_pgn --:";	var_dump($a_pgn); } //die();
if ($debug > 1) { echo "<br><br>-- a_paar --:";	var_dump($a_paar); } //die();
	sort($a_paar);
if ($debug > 1) { echo "<br><br>-- a_paar --:";	var_dump($a_paar); } //die();
//die();	

	$a_spieler = array();
	$n_spieler = array();
	$s_spieler = array();
	foreach ($a_paar as $paar) {
if ($debug > 1) { echo "<br><br>-- paar --:";	var_dump($paar); } //die();
		if (!isset($a_spieler[$paar->white])) {
			$a_spieler[$paar->white] = new stdclass();
			$a_spieler[$paar->white]->elo = $paar->whiteelo;
			$a_spieler[$paar->white]->name = addslashes($paar->white);
		}
		if (!isset($a_spieler[$paar->black])) {
			$a_spieler[$paar->black] = new stdclass();
			$a_spieler[$paar->black]->elo = $paar->blackelo;
			$a_spieler[$paar->black]->name = addslashes($paar->black);
		}
	}
if ($debug > 1) { echo "<br><br>-- a_spieler --:";	var_dump($a_spieler); } 
	rsort($a_spieler);
if ($debug > 1) { echo "<br><br>-- a_spieler --:";	var_dump($a_spieler); } 

if ($debug > 0) { echo "<br>"; } 
	foreach ($a_spieler as $key => $spieler) {
		$n_spieler[$spieler->name] = new stdclass();
		$n_spieler[$spieler->name]->elo = $spieler->elo;
		$n_spieler[$spieler->name]->name = $spieler->name;
		$n_spieler[$spieler->name]->snr = $key + 1;
		$s_spieler[($key + 1)] = new stdclass();
		$s_spieler[($key + 1)]->elo = $spieler->elo;
		$s_spieler[($key + 1)]->name = $spieler->name;
		$s_spieler[($key + 1)]->snr = $key + 1;
		if (isset($e_spieler[$spieler->name])) { // notwendig wegen Dateninkonsistenz bei lichess (selten)
			$n_spieler[$spieler->name]->performance = $e_spieler[$spieler->name]->performance;
			$n_spieler[$spieler->name]->tieBreak = $e_spieler[$spieler->name]->tieBreak;
			$s_spieler[$key + 1]->performance = $e_spieler[$spieler->name]->performance;
			$s_spieler[$key + 1]->tieBreak = $e_spieler[$spieler->name]->tieBreak;
		} else {
			$n_spieler[$spieler->name]->performance = 0;
			$n_spieler[$spieler->name]->tieBreak = 0;
			$s_spieler[$key + 1]->performance = 0;
			$s_spieler[$key + 1]->tieBreak = 0;
		}
if ($debug > 0) { echo "<br>-- spieler --:".($key+1).' '.$spieler->name.' '.$spieler->elo;	} // var_dump($n_spieler); } 
	}
if ($debug > 1) { echo "<br><br>-- n_spieler --:";	var_dump($n_spieler); } 
if ($debug > 1) { echo "<br><br>-- s_spieler --:";	var_dump($s_spieler); } 

	// Rundendaten aus Paarungen
	$runde = 1;
	$brett = 0;
	$r_spieler = array();    // Spieler in Runde
	$a_runde = array();		 // Rundendetails
	$dateStart = '1970-01-01';
	foreach ($a_paar as $key => $paar) {
if ($debug > 1) { echo "<br><br>-- paarungen --:"; } //die();
		if (!isset($r_spieler[$runde][$paar->white]) AND !isset($r_spieler[$runde][$paar->black])) {
			$r_spieler[$runde][$paar->white] = 1;
			$r_spieler[$runde][$paar->black] = 1;
		} else {
			$runde++;
			$brett = 0;
			$r_spieler[$runde][$paar->white] = 1;
			$r_spieler[$runde][$paar->black] = 1;
		}
		$a_paar[$key]->runde = $runde;
		$brett++;
		$a_paar[$key]->brett = $brett;
		if (!isset($a_runde[$runde])) {
			$a_runde[($runde)] = new stdclass();
			$a_runde[($runde)]->nr = $runde;
			$a_runde[($runde)]->datum = $paar->utcdate;
			if ($dateStart == '1970-01-01') $dateStart = str_replace('.', '-', $paar->utcdate);
			$a_runde[($runde)]->startzeit = $paar->utctime;
			$a_runde[($runde)]->count = 1;
		} else $a_runde[$runde]->count++;
	}
	$anz_runden = $runde;
	if ($a_runde[($runde)]->datum != '0000-00-00') $dateEnd = str_replace('.', '-',$a_runde[($runde)]->datum);
	else $dateEnd = '1970-01-01';
if ($debug > 1) { echo "<br><br>-- r_spieler --:";	var_dump($r_spieler); } 
if ($debug > 1) { echo "<br><br>-- a_paar --:";	var_dump($a_paar); } 
if ($debug > 1) { echo "<br><br>-- a_runde --:";	var_dump($a_runde); } 

//die();


	// Allgemeine Turnierdaten auslesen
	//	Turnierdaten -> Tabelle clm_turniere / clm_liga
if ($debug > 0) { echo "<br><br>-- Turnierdaten --"; } //die();
	
	If ($group) { 	// Mannschaftsturniere
	} else { 		// Einzelturniere
		$typ = '1';				// Einzel-Schweizer Sytem 
		$name = $a_paar[0]->event;
		$teil = count($a_spieler);
		$runden = $anz_runden;
		$keyS = '`name`, `sid`, `typ`, `dg`, `rnd`, `teil`, `runden`, `tl`, `published`, `bezirkTur`, `dateStart`, `dateEnd`';
		$valueS = "'".$name."',".$season.", '".$typ."', 1, 1,".$teil.",".$runden.", 0, 1, '0', '".$dateStart."', '".$dateEnd."'";
		$bem_int = $lang->arena_remark.' '.$arena_code;
		$keyS .= ', `tiebr1`,`bem_int`, `sieg`, `siegs`, `remis`, `remiss`';
		if ($tur_mode == 'swiss')
			$valueS .= ", 3, '$bem_int', 1, 1, '0.5', '0.5'";
		else   // mode arena
			$valueS .= ", 30, '$bem_int', 2, 2, 1, 1";
if ($debug > 0) { echo "<br>-- Name --:".$name; } //die();
if ($debug > 0) { echo "<br>-- Teilnehmer --:".$teil; } //die();
if ($debug > 0) { echo "<br>-- Runden --:".$runden; } //die();
		
		$params_array = array();
		$params_array[] = 'playerViewDisplaySex=0';
		$params_array[] = 'playerViewDisplayBirthYear=0';	
		$params_array[] = 'displayPlayerRating=0';
		$params_array[] = 'displayPlayerElo=0';	
		$params_array[] = 'pgnInput=1';	
		$params_array[] = 'pgnPublic=1';	
		$params_array[] = 'pgnDownload=1';	
		$params_array[] = 'autoDWZ=2';	
		$params_array[] = 'useAsTWZ=8';	
		$params_array[] = 'inofDWZ=0';	
		$params_array[] = 'import_source=lichess';	
if ($debug > 2) { echo "<br>params_array: ";	var_dump($params_array); }
		$params = implode("\n", $params_array);
		$keyS .= ', `params`';
		$valueS .= ", '".$params."'";
if ($debug > 0) { echo "<br>-- Parameter --:".$params; } //die();

		$sql = "INSERT INTO #__clm_swt_turniere (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br>sql: ";	var_dump($sql); }
		clm_core::$db->query($sql);
		$new_swt_tid = clm_core::$db->insert_id();
	}	
	
if ($debug > 0) { echo "<br><br><b>neue Turnier-ID in swt-Tabellen: ".$new_swt_tid."</b>"; }
//die('Ende Turnierdaten');


//	Rundendaten -> Tabelle clm_turniere_rnd_termine / clm_runden_termine
if ($debug > 0) { echo "<br><br>-- Rundendaten --";	}
	foreach ($a_runde as $key => $runde) {
if ($debug > 1) { echo "<br>a_runde: ";	var_dump($runde); }
		$startzeit = clm_core::$load->make_valid($runde->startzeit, 15, "00:00");
		if ($startzeit != '00:00') {
			$hour = substr($startzeit,0,2) + $time_diff;
			$startzeit = sprintf("%02.0f", $hour).substr($startzeit,2,3);
		}
		$rdatum = str_replace('.', '-',$runde->datum);
		if ($rdatum == '0000-00-00') $rdatum = '1970-01-01';
		$name = $lang->round.' '.$runde->nr;
if ($debug > 0) { echo "<br><br>-- Name --:".$name; } //die();
if ($debug > 0) { echo "<br>-- Datum --:".$rdatum; } //die();
if ($debug > 0) { echo "<br>-- Startzeit --:".$startzeit; } //die();
		
		If ($group) { 	// Mannschaftsturniere
		} else { 		// Einzelturniere
			$keyS = '`sid`, `name`, `swt_tid`, `dg`, `nr`, `published`, `datum`, `startzeit`';
			$valueS = $season.", '".$name."', ".$new_swt_tid.", 1, ".$runde->nr.", 1, '".$rdatum."', '".$startzeit."'";
			$sql = "INSERT INTO #__clm_swt_turniere_rnd_termine (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br>sql: ";	var_dump($sql); }
			clm_core::$db->query($sql);
		}
	}
//die('Ende Rundendaten');


	//	Spielerdaten -> Tabelle clm_turniere_tlnr
if ($debug > 0) { echo "<br><br>-- Spielerdaten --<br>";	}
	foreach ($s_spieler as $key => $spieler) {
if ($debug > 1) { echo "<br>a_spieler: ";	var_dump($spieler); }
		$snr = (integer) $key;
		$name = $spieler->name;
		$twz = (integer) $spieler->elo;
		$elo = (integer) $spieler->elo;
		$start_dwz = (integer) $spieler->elo;
		$performance = $spieler->performance;
		$tieBreak = $spieler->tieBreak;
		$snr_max = $snr;
if ($debug > 0) { echo "<br>-- spieler --:".$key.' '.$name.' '.$elo;	} // var_dump($n_spieler); } 

		If ($group) { 	// Mannschaftsturniere
		} else { 		// Einzelturniere	
			$keyS = '`sid`, `swt_tid`, `snr`, `name`, `tlnrStatus`, `oname`, `twz`, `FIDEelo`, `start_dwz`';
			$valueS = $season.", ".$new_swt_tid.", ".$snr.", '".$name."', '1', '".$name."', ".$twz.", ".$elo.", ".$start_dwz;
			if ($tur_mode == 'arena') {
				$keyS .= ' ,`sumTiebr1`,`Leistung`';
				$valueS .= " ,".$performance." ,".$performance;
			}
			if ($tur_mode == 'swiss') {
				$keyS .= ' ,`sumTiebr1`,`Leistung`';
				$valueS .= " ,".$tieBreak." ,".$performance;
			}
			$sql = "INSERT INTO #__clm_swt_turniere_tlnr (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br>sql: ";	var_dump($sql); }
			clm_core::$db->query($sql);
		}
	}

//die('<br>Ende Spielerdaten');


	//	Einzel-Ergebnisdaten -> Tabelle clm_turniere_rnd_spl / clm_rnd_spl
if ($debug > 0) { echo "<br><br>-- Einzel-Ergebnisdaten --<br>";	}
	$runde = 1;
	$brett = 0;
	$paar = 1;
	$epaar = 0;
	$a_paar_spl = array();
	foreach ($a_paar as $key => $paar) {
		$spieler = $n_spieler[$paar->white]->snr;
		$gegner = $n_spieler[$paar->black]->snr;
		$heim = 1;
		$runde = $paar->runde;
		$brett = $paar->brett;
		$ergebnis = transcode_ergebnis($paar->result,$heim,$gegner);
if ($debug > 0) { echo "<br>Runde: $runde  Brett: $brett  Spieler: $spieler  Gegner: $gegner  Paar-result: $paar->result   Ergebnis: $ergebnis"; } //var_dump($ergebnis);	}
		if ($gegner > 16000) $gegner = 0;
if ($debug > 0) { echo "<br>Runde: $runde  Brett: $brett  Spieler: $spieler  Gegner: $gegner  Ergebnis: $ergebnis  -- "; } //var_dump($ergebnis);	}
		if (($spieler == 0 OR $gegner == 0) AND $ergebnis == 7) continue;
		if ($ergebnis == 99) continue;
//die();
		$a_paar_spl[$runde][$spieler][$gegner] = new stdclass();
		$a_paar_spl[$runde][$spieler][$gegner]->runde = $runde;
		$a_paar_spl[$runde][$spieler][$gegner]->spieler = $spieler;
		$a_paar_spl[$runde][$spieler][$gegner]->gegner = $gegner;

		If ($group) { 	// Mannschaftsturniere
		} else { 		// Einzelturniere	
			$heim = 1;
			$ergebnis = transcode_ergebnis($paar->result,$heim,$gegner);
			$a_paar_spl[$runde][$spieler][$gegner]->werg =	$ergebnis;
			$nota = $a_pgn[$paar->pgnnr];
if ($debug > 1) { echo "<br>nota: ";	var_dump($nota); } //die();
			$keyS = '`sid`, `swt_tid`, `dg`, `runde`, `brett`, `tln_nr`, `heim`, `spieler`, `gegner`, `ergebnis`, `pgn`';
			if (!is_null($ergebnis))
				$valueS = $season.", ".$new_swt_tid.", 1,".$runde.", ".$brett.", ".$spieler.", ".$heim.",".$spieler.", ".$gegner.", ".$ergebnis.", '".$nota."'";
			else 
				$valueS = $season.", ".$new_swt_tid.", 1,".$runde.", ".$brett.", ".$spieler.", ".$heim.",".$spieler.", ".$gegner.", NULL, '".$nota."'";
			$sql = "INSERT INTO #__clm_swt_turniere_rnd_spl (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br>sql: ";	var_dump($sql); }
//die();
			clm_core::$db->query($sql);
			$heim = 0;
			$ergebnis = transcode_ergebnis($paar->result,$heim,$gegner);
			$a_paar_spl[$runde][$spieler][$gegner]->berg =	$ergebnis;
			$keyS = '`sid`, `swt_tid`, `dg`, `runde`, `brett`, `tln_nr`, `heim`, `spieler`, `gegner`, `ergebnis`, `pgn`';
			if (!is_null($ergebnis))
				$valueS = $season.", ".$new_swt_tid.", 1,".$runde.", ".$brett.", ".$gegner.", ".$heim.", ".$gegner.", ".$spieler.", ".$ergebnis.", '".$nota."'";
			else
				$valueS = $season.", ".$new_swt_tid.", 1,".$runde.", ".$brett.", ".$gegner.", ".$heim.", ".$gegner.", ".$spieler.", NULL, '".$nota."'";
			$sql = "INSERT INTO #__clm_swt_turniere_rnd_spl (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br>sql: ";	var_dump($sql); }
			clm_core::$db->query($sql);
		}
	}
if ($debug > 1) { echo "<br>a_paar_spl: ";	var_dump($a_paar_spl); }
//die('Ende Einzelergebnisse');

	//	Einzel-Ergebnisdaten -> Tabelle clm_turniere_rnd_spl / clm_rnd_spl 
	// ergänzen aus TRF-Datei
  if ($tur_mode == 'swiss') {
if ($debug > 0) { echo "<br><br>-- Einzel-Ergebnisdaten ohne Partie aus TRF-Datei --<br>";	}
if ($debug > 0) { echo "<br><br>-- Einzel-Ergebnisdaten Korrekturen aus TRF-Datei --<br>";	}
	$a_spielerdaten = array();
	$file = 'https://lichess.org/swiss/'.$arena_code.'.trf';
if ($debug > 0) { echo "<br>TRF-Datei: ".$file; }
	$lines = file($file);
if ($debug > 1) { echo "<br>lines:"; var_dump($lines); }
	if (!$lines) { 
		echo "<p>Tournament Code isn't valid for TRF-Datei! / Turniercode ist nicht gültig für die TRF-Datei!</p>";		
	} else {
		foreach ($lines as $line_num => $line) {
if ($debug > 1) { echo "<br>line:"; var_dump($line); }
			$din = substr($line,0,3);
if ($debug > 1) { echo "<br>din:"; var_dump($din); }
			if ($din == '001') { $a_spielerdaten[] = str_replace("’","'",$line); continue; }
			if ($din == 'XXR') $rundenzahl = trim(substr($line,4));
		}
	}
if ($debug > 0) { echo "<br>Rundenzahl: ".$rundenzahl; }
if ($debug > 1) { echo "<br>a_spielerdaten:"; var_dump($a_spielerdaten); }
	
	$a_trf_nr = array(); // spielernamen aus der TRF_Datei (immer klein!!)
	$a_trf_elo = array(); // elo-werte aus der TRF_Datei
	foreach ($a_spielerdaten as $line) {
		$trf_nr = trim(substr($line,4,4));
		$name = addslashes(trim(substr($line,14,33)));
		$a_trf_nr[$trf_nr] = $name;
		$FIDEelo = trim(substr($line,48,4));
		if (!is_numeric($FIDEelo)) $FIDEelo = '0';
		$a_trf_elo[$trf_nr] = $FIDEelo;
	}
if ($debug > 1) { echo "<br>a_trf_nr:"; var_dump($a_trf_nr); }

	// Umsetzen der Spielernamen aus der pgn-Datei (original, aalso klein oder groß
if ($debug > 1) { echo "<br>n_spieler:"; var_dump($n_spieler); }
	$n_trf_name = array(); 
	foreach ($n_spieler as $key => $n_name) {
		$n_trf_name[strtolower($key)] = $n_name;
	}
if ($debug > 1) { echo "<br>a_trf_nr:"; var_dump($a_trf_nr); }
if ($debug > 1) { echo "<br>n_trf_name:"; var_dump($n_trf_name); }
	$a_paarungen = array(); // pro Runde
	$paarung = array();
	$a_brett = array(); // pro Runde
	for ($i = 1; $i <= $rundenzahl; $i++) {
		$a_brett[$i] = 0;
	}
	foreach ($a_spielerdaten as $line) {
		$spieler = trim(substr($line,4,4));
		if (!isset($n_trf_name[$a_trf_nr[$spieler]])) {  // es gibt im Ausnahmefall Spieler, die nur kampflose Ergebnisse haben
			$snr_max++;
			$snr = $snr_max;
			$name = $a_trf_nr[$spieler];
			$twz = $a_trf_elo[$spieler];
			$elo = $a_trf_elo[$spieler];
			$start_dwz = $a_trf_elo[$spieler];
			$n_trf_name[$a_trf_nr[$spieler]] = new stdclass();
			$n_trf_name[$a_trf_nr[$spieler]]->elo = $a_trf_elo[$spieler];
			$n_trf_name[$a_trf_nr[$spieler]]->name = $a_trf_nr[$spieler];
			$n_trf_name[$a_trf_nr[$spieler]]->snr = $snr;
if ($debug > 1) { echo "<br>a_trf_nr:"; var_dump($a_trf_nr); }
if ($debug > 1) { echo "<br>n_trf_name:"; var_dump($n_trf_name); }
//die();		
			$query = "UPDATE #__clm_swt_turniere"
				." SET teil = ".$snr
				." WHERE swt_tid = ".$new_swt_tid;
if ($debug > 1) { echo "<br>query:"; var_dump($query); }
			clm_core::$db->query($query);
			$keyS = '`sid`, `swt_tid`, `snr`, `name`, `tlnrStatus`, `oname`, `twz`, `FIDEelo`, `start_dwz`';
			$valueS = $season.", ".$new_swt_tid.", ".$snr.", '".$name."', '1', '".$name."', ".$twz.", ".$elo.", ".$start_dwz;
			$sql = "INSERT INTO #__clm_swt_turniere_tlnr (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br>sql: ";	var_dump($sql); }
			clm_core::$db->query($sql);
//die();		
		}
		$spieler = $n_trf_name[$a_trf_nr[$spieler]]->snr;
		for ($i = 0; $i < $rundenzahl; $i++) {
			$paarung = array();
			$runde = $i+1;
			$gegner = trim(substr($line,(91+($i*10)),4));
if ($debug > 1) { echo "<br>gegner:"; var_dump($gegner); }
			if ($gegner > 0) {
if ($debug > 1) { echo "<br>a_trf_nr[gegner]:"; var_dump($a_trf_nr[$gegner]); }
				$gegner = $n_trf_name[$a_trf_nr[$gegner]]->snr;
			}
if ($debug > 1) { echo "<br>gegner:"; var_dump($gegner); }
			$color = trim(substr($line,(96+($i*10)),1));
			$result = trim(substr($line,(98+($i*10)),1));
if ($debug > 1) { echo "<br> ( Runde: ".$runde."  Spieler: $spieler  Gegner: $gegner  Color: $color  Result: $result ) ";	}
			
			if (isset($a_gegner[$runde][$gegner])) { 
				$brett = $a_gegner[$runde][$gegner];
				if ($color != 'b') {
					$a_paarung[$runde][$brett]['spieler'] = $spieler;
					$a_paarung[$runde][$brett]['werg'] = $result;
				} else {
					$a_paarung[$runde][$brett]['gegner'] = $spieler;	
					$a_paarung[$runde][$brett]['berg'] = $result;
				}
			} else {
				$a_brett[$runde]++;
				$paarung['runde'] = $runde;	
				$brett = $a_brett[$runde];	
				$paarung['brett'] = $brett;	
				$a_gegner[$runde][$spieler] = $a_brett[$runde];					
				if ($color != 'b') {
					$paarung['spieler'] = $spieler;
					$paarung['werg'] = $result;
				} else {
					$paarung['gegner'] = $spieler;	
					$paarung['berg'] = $result;
				}
				$a_paarung[$runde][$brett] = $paarung;
			}
		}
	}
	
	for ($i = 1; $i <= $rundenzahl; $i++) {
		for ($j = 1; $j <= $a_brett[$i]; $j++) {
if ($debug > 1) { 
			echo "<br>Runde: ".$i."  Brett: ".$j."   ";
			if (isset($a_paarung[$i][$j]['spieler']) AND $a_paarung[$i][$j]['spieler'] > 0) 
				echo "  ".$s_spieler[$a_paarung[$i][$j]['spieler']]->name."  ".$a_paarung[$i][$j]['spieler']."  ".$a_paarung[$i][$j]['werg']; 
			if (isset($a_paarung[$i][$j]['gegner']) AND $a_paarung[$i][$j]['gegner'] > 0) 
				echo "  ".$s_spieler[$a_paarung[$i][$j]['gegner']]->name."  ".$a_paarung[$i][$j]['gegner']."  ".$a_paarung[$i][$j]['berg'];
			}
			$runde = $i;
			$brett = $j;
			$heim = 1;
			$spieler = $a_paarung[$i][$j]['spieler'];
			if (isset($a_paarung[$i][$j]['gegner'])) {
				$gegner = $a_paarung[$i][$j]['gegner'];
			} else { 
				$gegner = 0;
				$a_paarung[$i][$j]['berg'] = 0;
			}
			if ($a_paarung[$i][$j]['werg'] == 'U' OR $a_paarung[$i][$j]['werg'] == 'H') {
				// Einzel-Ergebnisdaten ohne Partie aus TRF-Datei
				if ($a_paarung[$i][$j]['werg'] == 'U') $ergebnis = 5;
				if ($a_paarung[$i][$j]['werg'] == 'H') $ergebnis = 12;
				$a_runde[$runde]->count++;
				$brett = $a_runde[$runde]->count;
if ($debug > 0) { echo "<br> Ergebnis ohne Partie: Runde: ".$runde."  Spieler: $spieler  Gegner: $gegner   Ergebnis: $ergebnis  ";	}
				$keyS = '`sid`, `swt_tid`, `dg`, `runde`, `brett`, `tln_nr`, `heim`, `spieler`, `gegner`, `ergebnis`, `pgn`';
				$valueS = $season.", ".$new_swt_tid.", 1,".$runde.", ".$brett.", ".$spieler.", ".$heim.",".$spieler.", ".$gegner.", ".$ergebnis.", ''";
				$sql = "INSERT INTO #__clm_swt_turniere_rnd_spl (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br>sql: ";	var_dump($sql); }
//die();
				clm_core::$db->query($sql);
//die();
				$heim = 0;
				if ($a_paarung[$i][$j]['werg'] == 'U') $ergebnis = 4;
				if ($a_paarung[$i][$j]['werg'] == 'H') $ergebnis = 12;
				$keyS = '`sid`, `swt_tid`, `dg`, `runde`, `brett`, `tln_nr`, `heim`, `spieler`, `gegner`, `ergebnis`, `pgn`';
				$valueS = $season.", ".$new_swt_tid.", 1,".$runde.", ".$brett.", ".$gegner.", ".$heim.", ".$gegner.", ".$spieler.", ".$ergebnis.", ''";
				$sql = "INSERT INTO #__clm_swt_turniere_rnd_spl (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br>sql: ";	var_dump($sql); }
//die();
				clm_core::$db->query($sql);
			} else {
				// Einzel-Ergebnisdaten Korrekturen aus TRF-Datei
				$p_result = $a_paarung[$i][$j]['werg']."-".$a_paarung[$i][$j]['berg'];
					if ($p_result != '--0' AND $p_result != '0--') {
					$wergebnis = transcode_ergebnis($p_result,'1',$gegner);
					$bergebnis = transcode_ergebnis($p_result,'0',$spieler);
if ($debug > 1) { echo "<br>Runde: ".$runde."  Brett: ".$brett."  Spieler: $spieler  Gegner: $gegner   PErgebnis: $p_result  WErgebnis: $wergebnis  BErgebnis: $bergebnis  ";	}
					if (isset($a_paar_spl[$runde][$spieler][$gegner])
						AND $a_paar_spl[$runde][$spieler][$gegner]->werg == $wergebnis 
						AND $a_paar_spl[$runde][$spieler][$gegner]->berg == $bergebnis) {
if ($debug > 1) { echo "<br> ja: Runde: ".$runde."  Brett: ".$brett."  Spieler: $spieler  Gegner: $gegner  ";	}
						} else {
if ($debug > 0) { echo "<br> Ergebniskorrektur: Runde: ".$runde."  Brett: ".$brett."  Spieler: $spieler  Gegner: $gegner  Ergebnis: $p_result ";	}
					$query = "UPDATE #__clm_swt_turniere_rnd_spl "
						." SET ergebnis = ".$wergebnis
						." WHERE swt_tid = ".$new_swt_tid." AND runde = ".$runde." AND tln_nr = ".$spieler;
if ($debug > 1) { echo "<br>query:"; var_dump($query); }
					clm_core::$db->query($query);
					$query = "UPDATE #__clm_swt_turniere_rnd_spl "
						." SET ergebnis = ".$bergebnis
						." WHERE swt_tid = ".$new_swt_tid." AND runde = ".$runde." AND tln_nr = ".$gegner;
if ($debug > 1) { echo "<br>query:"; var_dump($query); }
					clm_core::$db->query($query);
						}
					}
//die();
			}
		}
	}
	
  }

	
	if (!$test) { 
		$result = clm_core::$api->db_swt_to_clm($new_swt_tid,$turnier,$group,$update);
		if ($debug > 1) { echo "<br>result:"; var_dump($result); }
		$new_tid = $result[1];
	} else {
		$new_tid = 0;
		echo "<br><br>Test - Ende des Protokolls!<br><br>"; 
	}
	return array(true, "m_ArenaImportSuccess",$new_tid); 

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
	/* pgn 		-> CLM	(Beschreibung)
		0 		->  NULL	 Ergebnis offen
		1-0 	->  1	 Weißsieg
		1/2-1/2 ->  2	 Remis
		0-1 	->  0	 Schwarzsieg
		
		4 ->  5  Weißsieg kampflos
		5 ->  4  Schwarzsieg kampflos
		6 ->  6  beide verlieren kampflos
		8 ->  8	 spielfrei   
		9 ->  8	 kampflos gewonnen
		1 + g>16000 -> 11 bye 1.0 (nicht ausgelost)
		2 + g>16000 -> 12 bye 0.5 (nicht ausgelost)
		3 + g>16000 -> 13 bye 0.0 (nicht ausgelost)
	*/
	if ($heim == 1) {
		if ($ergebnis == '1-0') $rergebnis = 1;
		elseif ($ergebnis == '1/2-1/2') $rergebnis = 2;
		elseif ($ergebnis == '=-=') $rergebnis = 2;
		elseif ($ergebnis == '--+') $rergebnis = 4;
		elseif ($ergebnis == '+--') $rergebnis = 5;
		else $rergebnis = 0;
	} else	{
		if ($ergebnis == '0-1') $rergebnis = 1;
		elseif ($ergebnis == '1/2-1/2') $rergebnis = 2;
		elseif ($ergebnis == '=-=') $rergebnis = 2;
		elseif ($ergebnis == '--+') $rergebnis = 5;
		elseif ($ergebnis == '+--') $rergebnis = 4;
		else $rergebnis = 0;
	}
//echo "<br>heim: $heim  ergebnis  arena: $ergebnis  ->  clm: $rergebnis";  	
	
    return $rergebnis;
}

?>
