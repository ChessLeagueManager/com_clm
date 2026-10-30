<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.chessleaguemanager.org
 *
 * Import einer Turnierdatei im TRF-Format von Sevilla, lichess, ... 
 * zur Zeit nur Einzelturnier im CH-Modus
 * Ergänzungen zum Standard: XX? von JaVaFo, XC? von CLM
*/
function clm_api_db_trf_import($file,$season,$turnier,$group=false,$update=false,$test=false) {
	set_time_limit(180);
	$auser = clm_core::$access->getId();
	$group = false;
//  $group = false -> Einzelturnier       $group = true -> Teamwettbewerb
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
		return array(false,'Please choose a filename! / Bitte wählen Sie einen Dateinamen aus!');
	}
//die();	
	$lines = file($file);
//echo "<br>2inputfile:"; var_dump($lines); 	
	$a_spielerdaten = array();
	$a_spielerCC = array();
	$a_spielerCE = array();
	$a_spielerCR = array();
	$a_spielerCS = array();
	$a_spielerCZ = array();
	$a_teamdaten = array();
	$a_teamCD = array();
	$a_rundendaten = array();
	$params = '';
	$adate = '1970-01-01';
	$edate = '1970-01-01';
	// Standardwerte, falls XXS fehlt oder unvollständig
	$xxs_sieg = 1.0; $xxs_remis = 0.5; $xxs_siegs = 1.0; $xxs_remiss = 0.5;
	$xxs_nieder = 0.0; $xxs_niederk = 0.0;
	$xxs_zpb = 0.0; $xxs_hpb = 0.5; $xxs_fpb = 1.0; $xxs_pab = 1.0;
	$xxs_fw = 1.0; $xxs_fl = 0.0;
	// weitere Standardwerte
	$rundenzahl = 0;
	$city = '';
	$FIDEcco = '';
	$lokal = '';
	$tiebr1 = $tiebr2 = $tiebr3 = 0;
	$params = '';
	// Durchgehen des Arrays inkl. Zuordnund der Zeilen
	// read common tournament data
	// Allgemeine Turnierdaten auslesen
	foreach ($lines as $line_num => $line) {
if ($debug > 1) { echo "<br>line:"; var_dump($line); }
		$din = substr($line,0,3);
if ($debug > 1) { echo "<br>din:"; var_dump($din); }
		if ($din == '001') {
			$snr = (integer) trim(substr($line,4,4));			
			$a_spielerdaten[$snr] = str_replace("’","'",$line); continue; 
		}
		if ($din == '013') { $a_teamdaten[] = str_replace("’","'",$line); continue; }
		if ($din == '310') { $a_teamdaten[] = str_replace("’","'",$line); continue; }
		if ($din == '132') { $rundendata = substr($line,91); continue; }
		if ($din == 'XCT') { $rundentime = substr($line,91); continue; }
		$value = trim(substr($line,4));
if ($debug > 1) { echo "<br>value:"; var_dump($value); }
		switch ($din) {
			case '012': $name = addslashes($value); 
if ($debug > 1) { echo "<br>name:"; var_dump($name); } 
						break;
			case '022': $city = addslashes($value); 
if ($debug > 1) { echo "<br>city:"; var_dump($city); } 
						break;
			case '032': $FIDEcco = addslashes($value); 
if ($debug > 1) { echo "<br>FIDEcco:"; var_dump($FIDEcco); } 
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
						if (substr($adate,4,1) == '/') {
							// Aufbereiten des Startdatums YYYY/MM/DD
							$yyyy = substr($adate,0,4);
							$mm = substr($adate,5,2);
							$dd = substr($adate,8,2);
							$adate = $yyyy.'-'.$mm.'-'.$dd;
						}
if ($debug > 0) { echo "<br>adate:"; var_dump($adate); }
						if (!clm_core::$load->is_date($adate,'Y-m-d')) {
							$adate = '1970-01-01'; }
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
						if (substr($edate,4,1) == '/') {
							// Aufbereiten des Enddatums YYYY/MM/DD
							$yyyy = substr($edate,0,4);
							$mm = substr($edate,5,2);
							$dd = substr($edate,8,2);
							$edate = $yyyy.'-'.$mm.'-'.$dd;
						}
if ($debug > 0) { echo "<br>edate:"; var_dump($edate); }
						if (!clm_core::$load->is_date($edate,'Y-m-d')) {
							$edate = '1970-01-01'; }
						break;
			case '062': $teil = $value; break;
			case '082': $man = (integer) $value; 
if ($debug > 0) { echo "<br>man:"; var_dump($man); }
						if ($man > 0) $group = true;  
						break;
			case '142': $rundenzahl = $value; break;
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
			case 'XCC': $snr = trim(substr($line,4,4));
						$a_spielerCC[$snr] = str_replace("’","'",$line); break;
			case 'XCD': $snr = trim(substr($line,4,4));
						$a_teamCD[$snr] = str_replace("’","'",$line); break;
			case 'XCE': $snr = trim(substr($line,4,4));
						$a_spielerCE[$snr] = str_replace("’","'",$line); break;					
			case 'XCF': //$a_xcf = explode(' ', $value);
						$tiebr1 = (integer) trim(substr($line,4,2));
						$tiebr2 = (integer) trim(substr($line,7,2));
						$tiebr3 = (integer) trim(substr($line,10,2));
						break;
			case 'XCL': $lokal = clm_core::$load->utf8encode($value);
if ($debug > 1) { echo "<br>lokal:"; var_dump($lokal); } 
						break;
			case 'XCM': $typ = (integer) trim(substr($line,4,2));
						$dg = (integer) trim(substr($line,7,2));
						$rundenzahl = (integer) trim(substr($line,10,2));
						$stamm = (integer) trim(substr($line,13,3));
						$ersatz = (integer) trim(substr($line,17,3));
						break;
			case 'XCP': if ($params == '') $params = $value; 
						else $params .= "\n".$value;			
if ($debug > 1) { echo "<br>params:"; var_dump($params); } 
						break;
			case 'XCR': $snr = trim(substr($line,4,4));
						$a_spielerCR[$snr] = str_replace("’","'",$line); break;					
			case 'XCS': $snr = trim(substr($line,4,4));
						$a_spielerCS[$snr] = str_replace("’","'",$line); break;					
			case 'XCZ': $snr = trim(substr($line,4,4));
						$a_spielerCZ[$snr] = str_replace("’","'",$line); break;
		}
	}
	if (isset($xxs_pab) AND ($xxs_pab == '0.5' OR $xxs_pab == '.5')) $erg_pab = 12; 
	elseif (isset($xxs_pab) AND $xxs_pab == 1 ) $erg_pab = 5; 
	else $erg_pab = 13;
if ($debug > 0) { echo "<br>xxs_pab: $xxs_pab   erg: $erg_pab "; }
	if ($rundenzahl == 0) {  // xxr fehlt oder ohne Angabe
		foreach ($a_spielerdaten as $line) {
			$i = 0;
			while ((strlen($line) > (98+($i*10))) AND (substr($line,(98+($i*10)),1)) > ' ') {  // result gesetzt
//				echo "<br>$i ".substr($line,(98+($i*10)),1);
				$rundenzahl = $i+1;
				$i++;
			}	
if ($debug > 0) { echo "<br> ( Rundenzahl: ".$rundenzahl." )" ;	}
			break;
		}
	}
//die();
	if (!isset($rundendata)) $rundendata = '';
	if (!isset($rundentime)) $rundentime = '';
if ($debug > 0) { echo "<br>name:"; var_dump($name); }
//if ($debug > 0) { echo "<br>teamCD:"; var_dump($a_teamCD); }
//if ($debug > 0) { echo "<br>spielerCZ:"; var_dump($a_spielerCZ); }
//die('endeende');

//	Turnierdaten -> Tabelle clm_turniere
		$bem_int = $lang->trf_remark.' '.$filename;
		
if ($debug > 0) { echo "<br>group:"; var_dump($group); }
 
	If ($group) { 	// Mannschaftsturniere
		if (!isset($stamm) OR !is_numeric($stamm)) $stamm = 4;
		if (!isset($ersatz) OR !is_numeric($ersatz)) $stamm = 0;
		if (!isset($typ) OR !is_numeric($typ) OR $typ > 9) $typ = '4'; 														   // Mannschaft-Schweizer Sytem .TUM
		if ($tiebr1 == 0) $tiebr1 = 5;
		$keyS = '`name`,`sid`, `runden_modus`, `teil`, `durchgang`, `rnd`, `sl`, `published`, `runden`, `dateStart`, `dateEnd`, `bem_int`,';
		$keyS .= '`man_sieg`, `man_remis`, `man_nieder`, `man_antritt`, `sieg`, `remis`, `nieder`, `antritt`, `liga_mt`,';
		$keyS .= '`city`, `FIDEcco`, `lokal`, `tiebr1`, `tiebr2`, `tiebr3`, `params`, `heim`, `order`, `stamm`, `ersatz`';
if ($debug > 0) { echo "<br>keyS: ";	var_dump($keyS); }
		$valueS = "'".$name."', ".$season.", '".$typ."', '".$man."', 1, 1, 0, 1, '".$rundenzahl."', '".$adate."', '".$edate."', '".$bem_int."', ";
		$valueS .= "'".'2.00'."', '".'1.00'."', '".'0.00'."', '".'0.00'."', '".'1.00'."', '".'0.50'."', '".'0.00'."', '".'0.00'."', 1,";
		$valueS .= "'".$city."', '".$FIDEcco."', '".$lokal."', '".$tiebr1."', '".$tiebr2."', '".$tiebr3."', '".$params."', 0, 0, '".$stamm."', '".$ersatz."'";
if ($debug > 0) { echo "<br>valueS: ";	var_dump($valueS); }

		$sql = 'INSERT INTO #__clm_swt_liga ('.$keyS.') VALUES ('.$valueS.')';
if ($debug > 0) { echo "<br>sql: ";	var_dump($sql); }
//die();
		clm_core::$db->query($sql);
		$new_swt_tid = clm_core::$db->insert_id();
	} else { 		// Einzelturniere
		if (!isset($typ) OR !is_numeric($typ) OR $typ > 9) $typ = '1'; 														   // Einzel-Schweizer Sytem
		$bem_int = $lang->trf_remark.' '.$filename;
		$keyS = '`name`, `sid`, `typ`, `dg`, `rnd`, `tl`, `published`, `runden`, `teil`, `bezirkTur`, `dateStart`, `dateEnd`, `bem_int`, ';
		$keyS .= '`sieg`, `siegs`, `remis`, `remiss`, `nieder`, `niederk`,';
		$keyS .= '`city`, `FIDEcco`, `lokal`, `tiebr1`, `tiebr2`, `tiebr3`, `params`';
		$valueS = "'".$name."',".$season.", '".$typ."', 1, 1, 0, 1, '".$rundenzahl."', '".$teil."', '0', '".$adate."', '".$edate."', '".$bem_int."', ";
		$valueS .= "'".$xxs_sieg."', '".$xxs_siegs."', '".$xxs_remis."', '".$xxs_remiss."', '".$xxs_nieder."', '".$xxs_niederk."',";
		$valueS .= "'".$city."', '".$FIDEcco."', '".$lokal."', '".$tiebr1."', '".$tiebr2."', '".$tiebr3."', '".$params."'";
/*		$params_array = array();
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
*/
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
			$keyS = '`sid`, `name`, `swt_liga`, `nr`, `datum`, `startzeit`, `published`';
			$valueS = $season.", 'Runde ".($i+1)."', ".$new_swt_tid.", ".($i+1).", '".$rdate."','".$rtime."', 1";
			$sql = "INSERT INTO #__clm_swt_runden_termine (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br>sql: ";	var_dump($sql); }
			clm_core::$db->query($sql);

		} else { 		// Einzelturniere
			$keyS = '`sid`, `name`, `swt_tid`, `dg`, `nr`, `datum`, `startzeit`, `published`';
			$valueS = $season.", 'Runde ".($i+1)."', ".$new_swt_tid.", 1, ".($i+1).", '".$rdate."','".$rtime."', 1";
			$sql = "INSERT INTO #__clm_swt_turniere_rnd_termine (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br>sql: ";	var_dump($sql); }
			clm_core::$db->query($sql);
		}
	}
//die('Rundendaten');

//	Teamdaten 310 -> Tabelle clm_mannschaften
if ($debug > 0) { echo "<br><br>-- Teamdaten --";	}
  if ($group) {
	$t_rdata = array();
	foreach ($a_teamdaten as $line) {
		$tln_nr = (integer) trim(substr($line,4,3));
		$name = trim(substr($line,8,32));
//if ($debug > 0) { echo "<br>tplayer: ";	var_dump(substr($line,(60),50)); }
//if ($debug > 0) { echo "<br>tplayer: ";	var_dump(substr($line,(65+5),5)); }
//if ($debug > 0) { echo "<br>tplayer: ";	var_dump(substr($line,(75+5),5)); }
		$tplayer = array();
		$ip = 1;
		while ((strlen($line) > (65+($ip*5))) AND (substr($line,(65+($ip*5)),5)) > '   ') {  // result gesetzt
			$tplayer[$ip] = (integer) trim(substr($line,(65+($ip*5)),5));
			$ip++;
			if ($ip > 99) break;
		}
if ($debug > 0) { echo "<br>tplayer: ";	var_dump($tplayer); }
		// XCD team details
		if (isset($a_teamCD[$tln_nr])) {
			$zps = trim(substr($a_teamCD[$tln_nr],8,5));
			$man_nr = trim(substr($a_teamCD[$tln_nr],15,3));
		} else {
			$zps = '';
			$man_nr = 0;
		}
		$id = 1;
//echo "<br>Runde 0 "; var_dump($a_teamCD[$tln_nr]);			
//echo "<br>Runde 0 "; var_dump(substr($a_teamCD[$tln_nr],51,90));			
		for ($ir = 1; $ir <= $rundenzahl; $ir++) {
				$t_rdata[$tln_nr][$id][$ir] = new stdClass();
				$t_rdata[$tln_nr][$id][$ir]->ergebnis = (integer) trim(substr($a_teamCD[$tln_nr],50+($ir*25),2));
				$t_rdata[$tln_nr][$id][$ir]->kampflos = trim(substr($a_teamCD[$tln_nr],53+($ir*25),1));
				$t_rdata[$tln_nr][$id][$ir]->gegner = (integer) trim(substr($a_teamCD[$tln_nr],56+($ir*25),3));
				$t_rdata[$tln_nr][$id][$ir]->paar = (integer) trim(substr($a_teamCD[$tln_nr],59+($ir*25),2));
				$t_rdata[$tln_nr][$id][$ir]->heim = trim(substr($a_teamCD[$tln_nr],62+($ir*25),1));
				$t_rdata[$tln_nr][$id][$ir]->manpunkte = (integer) trim(substr($a_teamCD[$tln_nr],65+($ir*25),2));
				$t_rdata[$tln_nr][$id][$ir]->brettpunkte = trim(substr($a_teamCD[$tln_nr],68+($ir*25),6));
				if ($t_rdata[$tln_nr][$id][$ir]->brettpunkte <= ' ') $t_rdata[$tln_nr][$id][$ir]->nullmarker = 1; else $t_rdata[$tln_nr][$id][$ir]->nullmarker = 0;
//echo "<br>Runde $tln_nr $id $ir "; var_dump($t_rdata[$tln_nr][$id][$ir]);			
//die();
		}
//echo "<br>Runde $ip dann "; var_dump($p_rdata[$tp]);			
//die();

		$keyS = '`sid`, `swt_id`, `tln_nr`, `name`, `zps`, `man_nr`, `published`, `mf`, `sg_zps`, `liste` ';
		$valueS = $season.", ".$new_swt_tid.", ".$tln_nr.", '".$name."', '".$zps."', '".$man_nr."', 1, 0, '', ".$auser;
		$sql = "INSERT INTO #__clm_swt_mannschaften (".$keyS.") VALUES (".$valueS.")";
if ($debug > 0) { echo "<br>sql: ";	var_dump($sql); }
		clm_core::$db->query($sql);
		
		$isnr = 0;
		foreach ($tplayer as $tp) {
			$p_rdata = array();
			$isnr++;
if ($debug > 0) { echo "<br>tp: ";	var_dump($tp); }
			$spielerid = $tp;
			$sdata = $a_spielerdaten[$spielerid];
if ($debug > 0) { echo "<br>sdata: $tp";	var_dump($sdata); }
			for ($id = 1; $id <= $dg; $id++) {
			  for ($ir = 1; $ir <= $rundenzahl; $ir++) {
				$p_rdata[$id][$ir] = new stdClass();
				$p_rdata[$id][$ir]->gegner = (integer) trim(substr($sdata,81+($ir*10),4));
				$p_rdata[$id][$ir]->farbe = substr($sdata,86+($ir*10),1);
			  }
			}
if ($debug > 0) { echo "<br>p_rdata: $tp";	var_dump($p_rdata); }
			
			// XCZ club membership
			if (isset($a_spielerCZ[$tp])) {
				$pzps = trim(substr($a_spielerCZ[$tp],9,5));
				$mgl_nr = (integer) trim(substr($a_spielerCZ[$tp],15,4));
			} else {
				$pzps = '';
				$mgl_nr = 0;
			}
//echo "<br>Runde 0 "; var_dump($a_spielerCZ[$tp]);			
//echo "<br>Runde 0 "; var_dump(substr($a_spielerCZ[$tp],79,90));			
			if (isset($a_spielerCZ[$tp])) {
			  for ($id = 1; $id <= $dg; $id++) {
				for ($ir = 1; $ir <= $rundenzahl; $ir++) {
					$p_rdata[$id][$ir]->paar = (integer) trim(substr($a_spielerCZ[$tp],50+($ir*25),2));
					$p_rdata[$id][$ir]->brett = (integer) trim(substr($a_spielerCZ[$tp],53+($ir*25),3));
					$p_rdata[$id][$ir]->heim = (integer) trim(substr($a_spielerCZ[$tp],57+($ir*25),1));
					$p_rdata[$id][$ir]->ergebnis = (integer) trim(substr($a_spielerCZ[$tp],59+($ir*25),2));
					$p_rdata[$id][$ir]->kampflos = (integer) trim(substr($a_spielerCZ[$tp],62+($ir*25),1));
					$p_rdata[$id][$ir]->punkte = trim(substr($a_spielerCZ[$tp],64+($ir*25),4));
if ($debug > 1) { echo "<br>Spieler $tp Runde $ir "; var_dump($p_rdata[$id][$ir]); }			
				}
			  }
			}
if ($debug > 1) { echo "<br>Runden zu Spieler $tp dann "; var_dump($p_rdata); }			
//die();
			// XCR ratings
			if (isset($a_spielerCR[$tp])) {
				$start_dwz = (integer) trim(substr($a_spielerCR[$tp],9,4));
				$FIDEelo = (integer) trim(substr($a_spielerCR[$tp],14,4));
			} else {
				$start_dwz = 0;
				$FIDEelo = 0;
			}
			$keyS = '`spielerid`,`sid`, `swt_id`, `snr`, `zps`, `man_id`, `mgl_nr`, `start_dwz`, `FIDEelo` ';
			$valueS = $spielerid.", ".$season.", ".$new_swt_tid.", ".$isnr.", '".$pzps."', '".$man_nr."', '".$mgl_nr."', '".$start_dwz."', '".$FIDEelo."' " ;
			$sql = "INSERT INTO #__clm_swt_meldeliste_spieler (".$keyS.") VALUES (".$valueS.")";
if ($debug > 0) { echo "<br>sql: ";	var_dump($sql); }
			clm_core::$db->query($sql);

			// Ausgabe Spielerergebnisse nach _clm_swt_rnd_spl
			for ($id = 1; $id <= 1; $id++) {
				for ($ir = 1; $ir <= $rundenzahl; $ir++) {
					$data = $p_rdata[$id][$ir];
					if ($data->brett > 0) {
						if ($data->farbe == 'w') $data->weiss = '1'; else $data->weiss = '0';
						// Gegner club membership aus XCZ
						if (isset($a_spielerCZ[$data->gegner])) {
							$gzps = trim(substr($a_spielerCZ[$data->gegner],9,5));
							$gmgl_nr = (integer) trim(substr($a_spielerCZ[$data->gegner],15,4));
						} else {
							$gzps = '';
							$gmgl_nr = 0;
						}
					
						$keyS = '`sid`, `swt_id`, `runde`, `paar`, `brett`, `weiss`, `dg`, `heim`,';
						$keyS .= ' `tln_nr`, `spieler`, `zps`, `gegner`,  `gzps`, '
							. '`ergebnis`, `kampflos`, `punkte`, `gemeldet` ';
						$valueS = $season.", ".$new_swt_tid.", ".$ir.", '".$data->paar."', '".$data->brett."', '".$data->weiss."', ".$id.", '".$data->heim."'";
						$valueS .= ", '".$tln_nr."', '".$mgl_nr."', '".$pzps."', '".$gmgl_nr."', '".$gzps."', "
							. "'".$data->ergebnis."', '".$data->kampflos."', '".$data->punkte."', ".$auser;
						
						$sql = "INSERT INTO #__clm_swt_rnd_spl (".$keyS.") VALUES (".$valueS.")";
if ($debug > 0) { echo "<br>sql: ";	var_dump($sql); }
						clm_core::$db->query($sql);
					}
				}
			}
//die('rnd_spl');
		}
//die('rnd_spl');
if ($debug > 0) { echo "<br>tln_nr: ";	var_dump($tln_nr); }
		// Ausgabe Mannschaftsergebnisse nach _clm_swt_rnd_man
		for ($id = 1; $id <= 1; $id++) {
			for ($ir = 1; $ir <= $rundenzahl; $ir++) {
				$data = $t_rdata[$tln_nr][$id][$ir];
				
				$keyS = '`sid`, `swt_id`, `runde`, `paar`, `dg`, `heim`, `tln_nr`, `gegner`, '
					. '`ergebnis`, `kampflos`, `brettpunkte`, `manpunkte`, `gemeldet`';
				if ($data->nullmarker == 0) {	
					$valueS = $season.", ".$new_swt_tid.", ".$ir.", '".$data->paar."', ".$id.", '".$data->heim."', '".$tln_nr."', '".$data->gegner."', "
						. "'".$data->ergebnis."', '".$data->kampflos."', '".$data->brettpunkte."', '".$data->manpunkte."', ".$auser;
				} else {	
					$valueS = $season.", ".$new_swt_tid.", ".$ir.", '".$data->paar."', ".$id.", '".$data->heim."', '".$tln_nr."', '".$data->gegner."', "
						. " NULL, NULL, NULL, NULL, NULL ";
				}
				$sql = "INSERT INTO #__clm_swt_rnd_man (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br>sql: ";	var_dump($sql); }
				clm_core::$db->query($sql);
			}
		}

	}
  }
//die('Teamdaten 510');



//	Spielerdaten -> Tabelle clm_turniere_tlnr
if ($debug > 0) { echo "<br><br>-- Spielerdaten --";	}
  if (!$group) {
	$a_name = array();
	foreach ($a_spielerdaten as $line) {
		$snr = (integer) trim(substr($line,4,4));
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
		$birthMonth = trim(substr($line,74,2));
		if (!is_numeric($birthMonth) OR $birthMonth < '01' OR $birthMonth > '12') $birthMonth = '';
		$birthDay = trim(substr($line,77,2));
		if (!is_numeric($birthDay) OR $birthDay < '01' OR $birthDay > '31') $birthDay = '';
		if ($birthYear > '0000' AND $birthMonth > '00' AND $birthDay > '00') $birthDay = $birthYear.'-'.$birthMonth.'-'.$birthDay; 
		else $birthDay = NULL;
		$points = trim(substr($line,80,4));
		$rang = trim(substr($line,85,4));
if ($debug > 0) { echo "<br>snr: $snr  geschlecht: $geschlecht  titel: $titel  name: $name  elo: $FIDEelo  cco: $FIDEcco  id: $FIDEid  birthyear: $birthYear  points: $points  rang: $rang"; }
		$a_name[$snr] = $name;

		// XCC club name
		if (isset($a_spielerCC[$snr])) {
			$verein = addslashes(trim(substr($a_spielerCC[$snr],9,40)));
		} else {
			$verein = '';
		}
		// XCE email address
		if (isset($a_spielerCE[$snr])) {
			$email = addslashes(trim(substr($a_spielerCE[$snr],9,100)));
		} else {
			$email = '';
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
//			$FIDEelo   = 0;  // es wird dann FIDEelo aus Spielerkarte 001 genommen
			$twz = 0;
		}
		// XCS phone number
		if (isset($a_spielerCS[$snr])) {
			$tel_no = addslashes(trim(substr($a_spielerCS[$snr],9,30)));
		} else {
			$tel_no = '';
		}
		// XCZ club + member_no
		if (isset($a_spielerCZ[$snr])) {
			$zps = trim(substr($a_spielerCZ[$snr],9,5));
			$mgl_nr = trim(substr($a_spielerCZ[$snr],15,4));
			if (is_numeric($mgl_nr)) $mgl_nr = (integer) $mgl_nr; else $mgl_nr = 0;
		} else {
			$zps = '';
			$mgl_nr = 0;
		}

if ($debug > 0) { echo "<br>snr: $snr  XCZ zps: $zps  mgl_nr: $mgl_nr  XCC verein $verein  XCR start_dwz: $start_dwz  FIDEelo: $FIDEelo twz: $twz "; }
		If ($group) { 	// Mannschaftsturniere
		} else { 		// Einzelturniere	
			$keyS = '`sid`, `swt_tid`, `snr`, `name`, `tlnrStatus`, `geschlecht`, `birthYear`, `twz`, `FIDEelo`, `FIDEid`, `FIDEcco`, ';
			$keyS .= '`titel`,`zps`,`mgl_nr`,`verein`,`start_dwz`,`email`,`tel_no`,`published`,`birthDay`';
			$valueS = $season.", ".$new_swt_tid.", ".$snr.", '".$name."', 1, '".$geschlecht."', '".$birthYear."', ".$twz.", ".$FIDEelo.", '".$FIDEid."', '".$FIDEcco;
			$valueS .=  "', '".$titel."', '".$zps."', ".$mgl_nr.", '".$verein."', ".$start_dwz.", '".$email."', '".$tel_no."', 1";
			if ($birthDay > '0000-00-00') $valueS .= " , '".$birthDay."'"; else $valueS .= " , NULL";
			$sql = "INSERT INTO #__clm_swt_turniere_tlnr (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br>sql: ";	var_dump($sql); }
			clm_core::$db->query($sql);
		}

	}
//  }
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
			if ($gegner == '') continue;
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
					case 'F':
						$ergebnis = 11; break;
					case 'H':
						$ergebnis = 12; break;
					case 'Z':
						$ergebnis = 13; break;
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
  }	
		
	if (!$test) { 
		if ($turnier >= 10000) $turnier -= 10000;
		$result = clm_core::$api->db_swt_to_clm($new_swt_tid,$turnier,$group,$update);
		if ($debug > 1) { echo "<br>result:"; var_dump($result); }
		$new_tid = $result[1];
	} else {
		$new_tid = 0;
		echo "<br><br>Test - Ende des Protokolls!<br><br>"; 
	}	
	if ($group) $new_tid += 10000;
	return array(true, "m_SWMImportSuccess",$new_tid); 

}

?>
