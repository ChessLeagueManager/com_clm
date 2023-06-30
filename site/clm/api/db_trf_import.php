<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 *
 * Import einer Turnierdatei im TRF-Format von Sevilla, lichess, ... 
 * zur Zeit nur Einzelturnier im CH-Modus
 * Ergänzungen zum Standard: XX? von JaVaFo, XC? von CLM
*/
function clm_api_db_trf_import($file,$season,$turnier,$group=false,$update=false,$test=false) {
	$group = false;
//echo "<br>file:"; var_dump($file); //die('ende-controller');	
    $lang = clm_core::$lang->imports;
//echo "<br>season:"; var_dump($season); //die('ende-controller');	
	if ($test) $debug = 1; else $debug = 0;
	if ($test)	echo "<br><br>Test - keine Übernahme der Daten ins CLM!"; 
	$new_ID = 0;
if ($debug > 0) { echo "<br><br>-- allgemeine Daten --";	}
if ($debug > 0) echo "<br><br>datei: ".$file; 		//echo "<br>end"; //die();
if ($debug > 0) echo "<br>saison: ".$season; 	//echo "<br>end"; //die();
if ($debug > 0) echo "<br>turnier: ".$turnier; 	//echo "<br>end"; //die();
	$f_pos = max(strrpos($file,'/'),strrpos($file,'\\'));
	$filename = substr($file,$f_pos + 1); 
	if (!$file) {
		echo '<p>Please choose a filename! / Bitte wählen Sie einen Dateinamen aus!</p>';
		return false;
	}
//die();	
	$lines = file($file);
//echo "<br>2inputfile:"; var_dump($lines); 	
	$a_spielerdaten = array();
	$a_spielerCZ = array();
	$a_spielerCC = array();
	$a_spielerCR = array();
	$a_teamdaten = array();
	$a_rundendaten = array();
	$adate = '1970-01-01';
	$edate = '1970-01-01';
	// Standardwerte, falls XXS fehlt oder unvollständig
	$xxs_sieg = 1.0; $xxs_remis = 0.5; $xxs_siegs = 1.0; $xxs_remiss = 0.5;
	$xxs_nieder = 0.0; $xxs_niederk = 0.0;
	$xxs_zpb = 0.0; $xxs_hpb = 0.5; $xxs_fpb = 1.0; $xxs_pab = 1.0;
	$xxs_fw = 1.0; $xxs_fl = 0.0;
	// Durchgehen des Arrays inkl. Zuordnund der Zeilen
	// read common tournament data
	// Allgemeine Turnierdaten auslesen
	foreach ($lines as $line_num => $line) {
if ($debug > 1) { echo "<br>line:"; var_dump($line); }
		$din = substr($line,0,3);
if ($debug > 1) { echo "<br>din:"; var_dump($din); }
		if ($din == '001') { $a_spielerdaten[] = str_replace("’","'",$line); continue; }
		if ($din == '013') { $a_teamdaten[] = str_replace("’","'",$line); continue; }
		if ($din == '132') { $rundendata = substr($line,91); continue; }
		if ($din == 'XCT') { $rundentime = substr($line,91); continue; }
		$value = trim(substr($line,4));
if ($debug > 1) { echo "<br>value:"; var_dump($value); }
		switch ($din) {
			case '012': $name = addslashes($value); 
if ($debug > 1) { echo "<br>name:"; var_dump($name); } 
						break;
			case '042': $adate = $value; 
						if (substr($adate,2,1) == '.') {
							// Aufbereiten des Startdatums DD.MM.YYYY
							$yyyy = substr($adate,6,4);
							$mm = substr($adate,3,2);
							$dd = substr($adate,0,2);
							$adate = $yyyy.'-'.$mm.'-'.$dd;
						}
						if (substr($adate,2,1) == '/') {
							// Aufbereiten des Startdatums DD/MM/YYYY
							$yyyy = substr($adate,6,4);
							$mm = substr($adate,3,2);
							$dd = substr($adate,0,2);
							$adate = $yyyy.'-'.$mm.'-'.$dd;
						}
						if (!clm_core::$load->is_date($adate,'Y-m-d')) {
							$adate = '1970-01-01'; }
if ($debug > 0) { echo "<br>adate:"; var_dump($adate); }
						break;
			case '052': $edate = $value; 
						if (substr($edate,2,1) == '.') {
							// Aufbereiten des Enddatums DD.MM.YYYY
							$yyyy = substr($edate,6,4);
							$mm = substr($edate,3,2);
							$dd = substr($edate,0,2);
							$edate = $yyyy.'-'.$mm.'-'.$dd;
						}
						if (substr($edate,2,1) == '/') {
							// Aufbereiten des Enddatums DD/MM/YYYY
							$yyyy = substr($edate,6,4);
							$mm = substr($edate,3,2);
							$dd = substr($edate,0,2);
							$edate = $yyyy.'-'.$mm.'-'.$dd;
						}
if ($debug > 0) { echo "<br>edate:"; var_dump($edate); }
						if (!clm_core::$load->is_date($edate,'Y-m-d')) {
							$edate = '1970-01-01'; }
						break;
			case '062': $teil = $value; break;
			case 'XXR': $rundenzahl = $value; break;
			case 'XXS': $a_xxs = explode(' ', $value);
						if (count($a_xxs) > 0) {
							foreach ($a_xxs as $d_xxs) {
								$e_xxs = explode('=', $d_xxs);
if ($debug > 0) { echo "<br>e_xxs"; var_dump($e_xxs); }
								if (count($e_xxs) == 2) {
									if ($e_xxs[1] == '.5') $e_xxs = '0.5';
									if (!is_numeric($e_xxs[1])) continue;
									switch (strtoupper($e_xxs[0])) {
										case 'WW':
											$xxs_sieg = $e_xxs[1]; break;
										case 'BW':
											$xxs_siegs = $e_xxs[1]; break;
										case 'WD':
											$xxs_remis = $e_xxs[1]; break;
										case 'BD':
											$xxs_remiss = $e_xxs[1]; break;
										case 'WL':
											$xxs_nieder = $e_xxs[1]; break;
										case 'BL':
											$xxs_nieder = $e_xxs[1]; break;
										case 'ZPB':
											$xxs_zpb = $e_xxs[1]; break;
										case 'HPB':
											$xxs_hpb = $e_xxs[1]; break;
										case 'FPB':
											$xxs_fpb = $e_xxs[1]; break;
										case 'PAB':
											$xxs_pab = $e_xxs[1]; break;
										case 'FW':
											$xxs_fw = $e_xxs[1]; break;
										case 'FL':
											$xxs_niederk = $e_xxs[1]; break;
										case 'W':
											$xxs_sieg = $e_xxs[1];
											$xxs_siegs = $e_xxs[1];
											$xxs_fpb = $e_xxs[1];
											$xxs_fw = $e_xxs[1]; break;
										case 'D':
											$xxs_remis = $e_xxs[1];
											$xxs_remiss = $e_xxs[1];
											$xxs_hpb = $e_xxs[1]; break;
										case 'L':
											$xxs_nieder = $e_xxs[1]; break;
									}
								}
							}
						}
						break;
			case 'XCA': $xca_rounds = $value; break;
			case 'XCZ': $snr = trim(substr($line,4,4));
						$a_spielerCZ[$snr] = str_replace("’","'",$line); break;
			case 'XCC': $snr = trim(substr($line,4,4));
						$a_spielerCC[$snr] = str_replace("’","'",$line); break;
			case 'XCR': $snr = trim(substr($line,4,4));
						$a_spielerCR[$snr] = str_replace("’","'",$line); break;					
		}
	}
	if (isset($xxs_pab) AND ($xxs_pab == '0.5' OR $xxs_pab == '.5')) $erg_pab = 12; 
	elseif (isset($xxs_pab) AND $xxs_pab == 1 ) $erg_pab = 5; 
	else $erg_pab = 13;
if ($debug > 0) { echo "<br>xxs_pab: $xxs_pab   erg: $erg_pab "; }
	if (!isset($rundendata)) $rundendata = '';
	if (!isset($rundentime)) $rundentime = '';
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
		$bem_int = $lang->trf_remark.' '.$filename;
		$keyS = '`name`, `sid`, `typ`, `dg`, `rnd`, `tl`, `published`, `runden`, `teil`, `bezirkTur`, `dateStart`, `dateEnd`, `bem_int`, ';
		$keyS .= '`sieg`, `siegs`, `remis`, `remiss`, `nieder`, `niederk`';
		$valueS = "'".$name."',".$season.", '".$typ."', 1, 1, 0, 1, '".$rundenzahl."', '".$teil."', '0', '".$adate."', '".$edate."', '".$bem_int."', ";
		$valueS .= "'".$xxs_sieg."', '".$xxs_siegs."', '".$xxs_remis."', '".$xxs_remiss."', '".$xxs_nieder."', '".$xxs_niederk."'";
		$params_array = array();
		$params_array[] = 'playerViewDisplaySex=0';
		$params_array[] = 'playerViewDisplayBirthYear=0';
		if (isset($xca_rounds) AND isset($rundenzahl) AND is_integer($xca_rounds) AND ($xca_rounds < $rundenzahl)) { 
			$params_array[] = 'accelerated_round='.$xca_rounds; }
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
//		$rundendata = substr($rundendata,87);
if ($debug > 0) { echo "<br>Rundendaten: $rundendata ";	}
	for ($i = 0; $i < $rundenzahl; $i++) {
		// Aufbereiten der Rundendata YY/MM/DD oder tt.mm.jj
		if (substr($rundendata,($i * 10)+2,1) == "/") {
			$yy = substr($rundendata,($i * 10),2);
			$mm = substr($rundendata,(3+($i * 10)),2);
			$dd = substr($rundendata,(6+($i * 10)),2);
			$rdate = '20'.$yy.'-'.$mm.'-'.$dd;
		}
		if (substr($rundendata,($i * 10)+2,1) == ".") {
			$dd = substr($rundendata,($i * 10),2);
			$mm = substr($rundendata,(3+($i * 10)),2);
			$yy = substr($rundendata,(6+($i * 10)),2);
			$rdate = '20'.$yy.'-'.$mm.'-'.$dd;
		}
if ($debug > 0) { echo "<br>rdate:"; var_dump($rdate); }
		if (!clm_core::$load->is_date($rdate,'Y-m-d')) {
			$rdate = '1970-01-01';
		}
if ($debug > 0) { echo "<br>Runde ".($i+1)."  Datum: $rdate ";	}
		$rtime = "00:00:00";
		if ((substr($rundentime,($i * 10)+2,1) == ":") && (substr($rundentime,($i * 10)+5,1) == ":")) {
			$rtime = substr($rundentime,($i * 10),8);
			if (!clm_core::$load->is_date("2001-01-01 " . $rtime, "Y-m-d H:i:s")) {
if ($debug > 0) { echo "<br>rtime vorher: >" . $rtime . "<"; }
				$dttme = DateTime::createFromFormat("Y-m-d H:i:s", "2001-01-01 " . $rtime);
				echo "<br>datetime(rtime): >" . $dttme . "<"; 
				$rtime = "00:00:00";
if ($debug > 0) { echo "<br>rtime nachher: >" . $rtime . "<"; }
			}
		}
if ($debug > 0) { echo "<br>Rundenzeiten: $rundentime ";	}
if ($debug > 0) { echo "<br>rtime:"; var_dump($rtime); }
		If ($group) { 	// Mannschaftsturniere
		} else { 		// Einzelturniere
			$keyS = '`sid`, `name`, `swt_tid`, `dg`, `nr`, `datum`, `startzeit`, `published`';
			$valueS = $season.", 'Runde ".($i+1)."', ".$new_swt_tid.", 1, ".($i+1).", '".$rdate."','".$rtime."', 1";
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
		$geschlecht = strtoupper(trim(substr($line,9,1)));
		$titel = strtoupper(trim(substr($line,11,3)));
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

		// XCZ club + member_no
		if (isset($a_spielerCZ[$snr])) {
			$zps = trim(substr($a_spielerCZ[$snr],9,5));
			$mgl_nr = trim(substr($a_spielerCZ[$snr],15,4));
			if (is_numeric($mgl_nr)) $mgl_nr = (integer) $mgl_nr; else $mgl_nr = 0;
		} else {
			$zps = '';
			$mgl_nr = 0;
		}
		// XCC club name
		if (isset($a_spielerCC[$snr])) {
			$verein = addslashes(trim(substr($a_spielerCC[$snr],9,40)));
		} else {
			$verein = '';
		}
		// XCR ratings
		if (isset($a_spielerCR[$snr])) {
			$start_dwz = trim(substr($a_spielerCR[$snr],9,4));
			if (is_numeric($start_dwz)) $start_dwz = (integer) $start_dwz; else $start_dwz = 0;
			$FIDEelo = trim(substr($a_spielerCR[$snr],14,4));
			if (is_numeric($FIDEelo)) $FIDEelo = (integer) $FIDEelo; else $FIDEelo = 0;
			$twz = trim(substr($a_spielerCR[$snr],19,4));
			if (is_numeric($twz)) $twz = (integer) $twz; else $twz = 0;
		} else {
			$start_dwz = 0;
			$FIDEelo   = 0;
			$twz = 0;
		}

if ($debug > 0) { echo "<br>snr: $snr  XCZ zps: $zps  mgl_nr: $mgl_nr  XCC verein $verein  XCR start_dwz: $start_dwz  FIDEelo: $FIDEelo twz: $twz "; }
		If ($group) { 	// Mannschaftsturniere
		} else { 		// Einzelturniere	
			$keyS = '`sid`, `swt_tid`, `snr`, `name`, `tlnrStatus`, `geschlecht`, `birthYear`, `twz`, `FIDEelo`, `FIDEid`, `FIDEcco`, ';
			$keyS .= '`titel`,`zps`,`mgl_nr`,`verein`,`start_dwz`,`published`';
			$valueS = $season.", ".$new_swt_tid.", ".$snr.", '".$name."', 1, '".$geschlecht."', '".$birthYear."', ".$twz.", ".$FIDEelo.", '".$FIDEid."', '".$FIDEcco;
			$valueS .=  "', '".$titel."', '".$zps."', ".$mgl_nr.", '".$verein."', ".$start_dwz.", 1";
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
