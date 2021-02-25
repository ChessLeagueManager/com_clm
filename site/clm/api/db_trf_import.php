<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 *
 * Import einer Turnierdatei im TRF-Format von Sevilla, lichess, ... 
 * zur Zeit nur Einzelturnier im CH-Modus 
*/
function clm_api_db_trf_import($file,$season,$turnier,$group=false,$update=false,$test=false) {
	$group = false;
//echo "<br>file:"; var_dump($file); //die('ende-controller');	
    $lang = clm_core::$lang->trf_import;
//echo "<br>season:"; var_dump($season); //die('ende-controller');	
	if ($test) $debug = 1; else $debug = 0;
//	$debug = 1;
	if ($test)	echo "<br><br>Test - keine Übernahme der Daten ins CLM!"; 
	$new_ID = 0;
if ($debug > 0) { echo "<br><br>-- allgemeine Daten --";	}
if ($debug > 0) echo "<br><br>datei: ".$file; 		//echo "<br>end"; //die();
if ($debug > 0) echo "<br>saison: ".$season; 	//echo "<br>end"; //die();
if ($debug > 0) echo "<br>turnier: ".$turnier; 	//echo "<br>end"; //die();

	if (!$file) {
		echo '<p>Please choose a filename! / Bitte wählen Sie einen Dateinamen aus!</p>';
		return false;
	}
	
	$lines = file($file);
//echo "<br>2inputfile:"; var_dump($lines); 	
	$a_spielerdaten = array();
	$a_teamdaten = array();
	$a_rundendaten = array();
	$pab = 0;
	// Durchgehen des Arrays inkl. Zuordnund der Zeilen
	// read common tournament data
	// Allgemeine Turnierdaten auslesen
	foreach ($lines as $line_num => $line) {
if ($debug > 1) { echo "<br>line:"; var_dump($line); }
		$din = substr($line,0,3);
if ($debug > 1) { echo "<br>din:"; var_dump($din); }
		if ($din == '001') { $a_spielerdaten[] = str_replace("’","'",$line); continue; }
		if ($din == '013') { $a_teamdaten[] = str_replace("’","'",$line); continue; }
		$value = trim(substr($line,4));
if ($debug > 1) { echo "<br>value:"; var_dump($value); }
		switch ($din) {
			case '012': $name = addslashes($value); //break;
if ($debug > 1) { echo "<br>name:"; var_dump($name); } break;
			case '062': $teil = $value; break;
			case '132': $rundendata = $value; break;
			case 'XXR': $rundenzahl = $value; break;
			case 'XXS': $pos_pab = strpos($value,'PAB');
						if ($pos_pab !== false) $pab = substr($value, ($pos_pab + 4),3); break;
		}
	}
	if (isset($pab) AND $pab == '0.5') $erg_pab = 12; else $erg_pab = 13;
if ($debug > 0) { echo "<br>pab: $pab   erg: $erg_pab "; }
	if (!isset($rundendata)) $rundendata = '';
if ($debug > 0) { echo "<br>name:"; var_dump($name); }
//die('endeende');
//	Turnierdaten -> Tabelle clm_turniere
 
	If ($group) { 	// Mannschaftsturniere
		$typ = '4'; 														   // Mannschaft-Schweizer Sytem .TUM
		if (strpos($file,'.TUT') > 0 OR strpos($file,'.tut') > 0 ) $typ = '2'; // Mannschaft-Rundenturnier	 .TUT
		$keyS = '`name`,`sid`, `typ`, `dg`, `rnd`, `tl`, `published`, `name`, `bezirkTur`';
		$valueS = "'".$name."', ".$season.", '".$typ."', 1, 1, 0, 1, '".$name."', '0'";
	} else { 		// Einzelturniere
		$typ = '1'; 														   // Einzel-Schweizer Sytem
		$keyS = '`name`, `sid`, `typ`, `dg`, `rnd`, `tl`, `published`, `runden`, `teil`, `bezirkTur`';
		$valueS = "'".$name."',".$season.", '".$typ."', 1, 1, 0, 1, '".$rundenzahl."', '".$teil."', '0'";
		$params_array = array();
		$params_array[] = 'playerViewDisplaySex=0';
		$params_array[] = 'playerViewDisplayBirthYear=0';
		if (count($a_teamdaten) > 0) $params_array[] = 'teamranking=4';
if ($debug > 2) { echo "<br>params_array: ";	var_dump($params_array); }
		$params = implode("\n", $params_array);
		$keyS .= ', `params`';
		$valueS .= ", '".$params."'";
if ($debug > 1) { echo "<br>params: ";	var_dump($params); }

		$sql = 'INSERT INTO #__clm_swt_turniere ('.$keyS.') VALUES ('.$valueS.')';
if ($debug > 1) { echo "<br>sql: ";	var_dump($sql); }
//die();
		clm_core::$db->query($sql);
		$new_swt_tid = clm_core::$db->insert_id();
	}	
	
if ($debug > 0) { echo "<br>neue Turnier-ID (trf): ";	var_dump($new_swt_tid); }
//die('Turnierdaten');

//	Rundendaten -> Tabelle clm_turniere_rnd_termine
if ($debug > 0) { echo "<br><br>-- Rundendaten --";	}
if ($debug > 0) { echo "<br>Rundendaten: $rundendata ";	}
	for ($i = 0; $i < $rundenzahl; $i++) {
		// Aufbereiten der Rundendata YY/MM/DD
		$yy = substr($rundendata,($i * 10),2);
		$mm = substr($rundendata,(3+($i * 10)),2);
		$dd = substr($rundendata,(6+($i * 10)),2);
		$rdate = '20'.$yy.'-'.$mm.'-'.$dd;
		if (!clm_core::$load->is_date($rdate,'Y-m-d')) {
			$rdate = '1970-01-01';
		}
if ($debug > 0) { echo "<br>Runde ".($i+1)."  Datum: $rdate ";	}
		If ($group) { 	// Mannschaftsturniere
		} else { 		// Einzelturniere
			$keyS = '`sid`, `name`, `swt_tid`, `dg`, `nr`, `datum`, `published`';
			
			$valueS = $season.", 'Runde ".($i+1)."', ".$new_swt_tid.", 1, ".($i+1).", '".$rdate."', 1";
			$sql = "INSERT INTO #__clm_swt_turniere_rnd_termine (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br>sql: ";	var_dump($sql); }
			clm_core::$db->query($sql);
		}
	}
//die('Rundendaten');

	//	Spielerdaten -> Tabelle clm_turniere_tlnr
if ($debug > 0) { echo "<br><br>-- Spielerdaten --";	}
	$a_name = array();
	foreach ($a_spielerdaten as $line) {
		$snr = trim(substr($line,4,4));
		$geschlecht = trim(substr($line,9,1));
		$titel = trim(substr($line,10,3));
		$name = addslashes(trim(substr($line,14,33)));
		$FIDEelo = trim(substr($line,48,4));
		if (!is_numeric($FIDEelo)) $FIDEelo = '0';
		$FIDEcco = trim(substr($line,53,3));
		$FIDEid = trim(substr($line,57,11));
		if (!is_numeric($FIDEid)) $FIDEid = '0';
		$birthYear = trim(substr($line,69,4));
		if (!is_numeric($birthYear)) $birthYear = '0000';
		$points = trim(substr($line,80,4));
		$rang = trim(substr($line,85,4));
if ($debug > 0) { echo "<br>snr: $snr  geschlecht: $geschlecht  titel: $titel  name: $name  elo: $FIDEelo  cco: $FIDEcco  id: $FIDEid  birthyear: $birthYear  points: $points  rang: $rang"; }
		$a_name[$snr] = $name;
		If ($group) { 	// Mannschaftsturniere
		} else { 		// Einzelturniere	
			$keyS = '`sid`, `swt_tid`, `snr`, `name`, `tlnrStatus`, `geschlecht`, `birthYear`, `twz`, `FIDEelo`, `FIDEid`, `FIDEcco`, `published`';
			$valueS = $season.", ".$new_swt_tid.", ".$snr.", '".$name."', 1, '".$geschlecht."', '".$birthYear."', ".$FIDEelo.", ".$FIDEelo.", '".$FIDEid."', '".$FIDEcco."', 1";
/*			foreach ($tab_record['out'] as $tab) {
if ($debug > 2) { echo "<br>tab: ";	var_dump($tab); }
				if ($tab[1][2] == '0') continue;
				$keyS .= ',`'.$tab[1][2].'`';
				//$valueS .= ",'".clm_core::$db->escape($tab[0])."'";		
				$valueS .= ",'".$tab[0]."'";		
			}
*/
			$sql = "INSERT INTO #__clm_swt_turniere_tlnr (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br>sql: ";	var_dump($sql); }
			clm_core::$db->query($sql);
		}
	}
if ($debug > 0) { echo "<br>namen: ";	var_dump($a_name); }
//die('Spielerdaten');

	//	Einzel-Ergebnisdaten -> Tabelle clm_turniere_rnd_spl
if ($debug > 0) { echo "<br><br>-- Einzel-Ergebnisdaten --";	}
	$a_paarungen = array(); // pro Runde
	$paarung = array();
	$a_brett = array(); // pro Runde
	for ($i = 1; $i <= $rundenzahl; $i++) {
		$a_brett[$i] = 0;
	}
	
	foreach ($a_spielerdaten as $line) {
		$spieler = trim(substr($line,4,4));
		for ($i = 0; $i < $rundenzahl; $i++) {
			$paarung = array();
			$runde = $i+1;
			$gegner = trim(substr($line,(91+($i*10)),4));
			$color = trim(substr($line,(96+($i*10)),1));
			$result = trim(substr($line,(98+($i*10)),1));
if ($debug > 0) { echo "<br> ( Runde: ".$runde."  Spieler: $spieler  Gegner: $gegner  Color: $color  Result: $result ) ";	}
			
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
//			die('Runde 1 1.Spieler');
		}
/*			for ($i = 1; $i <= $rundenzahl; $i++) {
				for ($j = 1; $j <= $a_brett[$i]; $j++) {
if ($debug > 0) { echo "<br> ( Runde: ".$i."  Brett: $j ) "; var_dump( $a_paarung[$i][$j]);	}
				}
			}
if ($debug > 0) { echo "<br> Bretter: "; var_dump( $a_brett);	}
if ($debug > 0) { echo "<br> Gegner: "; var_dump( $a_gegner);	}
if ($spieler > 2) die('1.Spieler - alle Runden');
		
*/		
	}
		for ($i = 1; $i <= $rundenzahl; $i++) {
			for ($j = 1; $j <= $a_brett[$i]; $j++) {
if ($debug > 1) { echo "<br> ( Runde: ".$i."  Brett: $j ) "; var_dump( $a_paarung[$i][$j]);	}
if ($debug > 0) { echo "<br>Runde: ".$i."  Brett: $j   ".$a_name[$a_paarung[$i][$j]['spieler']]."  ".$a_paarung[$i][$j]['werg']; 
				if(isset($a_paarung[$i][$j]['gegner'])) echo "  ".$a_name[$a_paarung[$i][$j]['gegner']]."  ".$a_paarung[$i][$j]['berg'];	}
			}
		}
if ($debug > 0) { echo "<br> Bretter: "; var_dump( $a_brett);	}
if ($debug > 0) { echo "<br> Gegner: "; var_dump( $a_gegner);	}
//if ($spieler > 2) die('alle Spieler - alle Runden');

	
//die('Paarungsdaten');

		If ($group) { 	// Mannschaftsturniere
		} else { 		// Einzelturniere	
		  for ($i = 1; $i <= $rundenzahl; $i++) {
			for ($j = 1; $j <= $a_brett[$i]; $j++) {
if ($debug > 0) { echo "<br>Runde: ".$i."  Brett: $j   ".$a_name[$a_paarung[$i][$j]['spieler']]."  ".$a_paarung[$i][$j]['werg']; 
				if(isset($a_paarung[$i][$j]['gegner'])) echo "  ".$a_name[$a_paarung[$i][$j]['gegner']]."  ".$a_paarung[$i][$j]['berg'];	}
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
				switch ($a_paarung[$i][$j]['werg']) {
					case '0':
						$ergebnis = 0; break;
					case '1':
						$ergebnis = 1; break;
					case '=':
						$ergebnis = 2; break;
					case '-':
						$ergebnis = 4; break;
					case '+':
						$ergebnis = 5; break;
					case 'H':
						$ergebnis = 12; break;
					case 'U':
						$ergebnis = $erg_pab; break;
					default:
						$ergebnis = NULL;
				}
				$keyS = '`sid`, `swt_tid`, `dg`, `runde`, `brett`, `tln_nr`, `heim`, `spieler`, `gegner`, `ergebnis`, `pgn`';
				if (!is_null($ergebnis))
					$valueS = $season.", ".$new_swt_tid.", 1,".$runde.", ".$brett.", ".$spieler.", ".$heim.",".$spieler.", ".$gegner.", ".$ergebnis.", ''";
				else 
					$valueS = $season.", ".$new_swt_tid.", 1,".$runde.", ".$brett.", ".$spieler.", ".$heim.",".$spieler.", ".$gegner.", NULL, ''";
				$sql = "INSERT INTO #__clm_swt_turniere_rnd_spl (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br>sql: ";	var_dump($sql); }
				clm_core::$db->query($sql);
//die();			
				$heim = 0;
				switch ($a_paarung[$i][$j]['berg']) {
					case '0':
						$ergebnis = 0; break;
					case '1':
						$ergebnis = 1; break;
					case '=':
						$ergebnis = 2; break;
					case '-':
						$ergebnis = 4; break;
					case '+':
						$ergebnis = 5; break;
					case 'H':
						$ergebnis = 12; break;
					case 'U':
						$ergebnis = 7; break;
					default:
						$ergebnis = NULL;
				}
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
		}
//	}
//die('Ergebnisse');

		If ($group) { 	// Mannschaftsturniere
		} else { 		// Einzelturniere
			$tln_nr = 0;
			if (count($a_teamdaten) > 0) {
				foreach ($a_teamdaten as $line) {
					$tln_nr++;
if ($debug > 0) { echo "<br>team $tln_nr "; var_dump($line); }
					$name = substr($line, 4,32);
					$a_members = array();
					for ($j = 1; $j <= 14; $j++) {
						$member = substr($line, (31+($j*5)),5);
						if ($member == '') break;
						$a_members[$j] = (integer) $member;
					}
if ($debug > 0) { echo "<br>team $tln_nr "; var_dump($name); var_dump($a_members);}
					$keyS = '`swt_tid`, `name`, `sid`, `tln_nr`, `published`';
					$valueS = $new_swt_tid.",'".$name."',".$season.", ".$tln_nr.", 1";
					$query = "INSERT INTO #__clm_swt_turniere_teams (".$keyS.") VALUES (".$valueS.")";
if ($debug > 0) { echo "<br>query: ";	var_dump($query); }
					clm_core::$db->query($query);
					for ($j = 0; $j < count($a_members); $j++) {
						$jj = $j + 1;
						$query = "SELECT * FROM `#__clm_swt_turniere_tlnr`"
							. " WHERE swt_tid = ".$new_swt_tid." AND snr = ".$a_members[$jj];
						$result = clm_core::$db->loadObjectList($query);
if ($debug > 0) { echo "<br>result: ";	var_dump($result); }
						$query = "UPDATE #__clm_swt_turniere_tlnr"
							. " SET mtln_nr = ".$tln_nr
							. " WHERE swt_tid = ".$new_swt_tid." AND snr = ".$a_members[$jj];
if ($debug > 0) { echo "<br>tlnr-query: ";	var_dump($query); }
						clm_core::$db->query($query);
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
	
	return array(true, "m_SWMImportSuccess",$new_tid); 

}

?>
