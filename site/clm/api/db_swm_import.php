<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2019 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 *
 * Import einer Turnierdatei vom Swiss-Manager 
 * zur Zeit nur TUNx = Einzelturnier im CH-Modus und TURx = Einzelturnier als Vollturnier
*/
function clm_api_db_swm_import($file,$season,$turnier,$group=false,$update=false,$test=false) {
    $lang = clm_core::$lang->swm_import;
	if ($test) $debug = 1; else $debug = 0;
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
//	Turnierdaten -> Tabelle clm_turniere
if ($debug > 0) { echo "<br><br>-- Turnierdaten --";	}
	$tournament = zzparse_interpret($contents, 'general',$apos[1],($apos[2]-$apos[1]),$debug);
if ($debug > 1) { echo "<br>tournament: ";	var_dump($tournament); }
	$tournament2 = zzparse_interpret($contents, 'general2',$apos[2],($apos[3]-$apos[2]),$debug);
if ($debug > 1) { echo "<br>tournament2: ";	var_dump($tournament2); }
	$tournament["out"][1] = $tournament2["out"][1];
	$tournament["out"][4] = $tournament2["out"][4];
	$tournament["out"][201] = transcode_tiebr($tournament2,201,$debug); // Feinwertung
	$tournament["out"][202] = transcode_tiebr($tournament2,202,$debug); // Feinwertung
	$tournament["out"][203] = transcode_tiebr($tournament2,203,$debug); // Feinwertung
	$tournament["out"][204] = transcode_tiebr($tournament2,204,$debug); // Feinwertung
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
	$typ = '1';
	if (strpos($file,'.TUR') > 0 OR strpos($file,'.tur') > 0 ) $typ = '2';
 
	$keyS = '`sid`, `typ`, `dg`, `rnd`, `tl`, `published`, `name`, `bezirkTur`, `checked_out_time`';
	$valueS = $season.", '".$typ."', 1, 1, 0, 1, '".$name."', '0', '1970-01-01 00:00:00'";
	$params_array = array();
	foreach ($tournament['out'] as $tour) {
if ($debug > 1) { echo "<br>tour: ";	var_dump($tour); }
		if ($tour[1][2] == '0') continue;
		if (substr($tour[1][2],0,6) == 'params') {
			$params_array[] = substr($tour[1][2],7).'='.$tour[0];
		} else {
			$keyS .= ',`'.$tour[1][2].'`';
			$valueS .= ",'".clm_core::$db->escape($tour[0])."'";
		}
	}
	$params_array[] = 'playerViewDisplaySex=0';
	$params_array[] = 'playerViewDisplayBirthYear=0';	
if ($debug > 2) { echo "<br>params_array: ";	var_dump($params_array); }
	$params = implode("\n", $params_array);
	$keyS .= ', `params`';
	$valueS .= ", '".$params."'";
if ($debug > 2) { echo "<br>params: ";	var_dump($params); }

	$sql = "INSERT INTO #__clm_swt_turniere (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br>sql: ";	var_dump($sql); }
//die();
	clm_core::$db->query($sql);
	$new_swt_tid = clm_core::$db->insert_id();
if ($debug > 0) { echo "<br>neue Turnier-ID (swt): ";	var_dump($new_swt_tid); }

	$paramuseAsTWZ = $tournament["out"][24][0];

//	Rundendaten -> Tabelle clm_turniere_rnd_termine
if ($debug > 0) { echo "<br><br>-- Rundendaten --";	}
	$paarprorunde = array();
	$slength = 0;
	for ($i = 0; $i < $tournament["out"][1][0]; $i++) {
		$tab_record = zzparse_interpret($contents, 'round', ($apos[3]+$slength),($apos[4]-$apos[3]-$slength),$debug);
		$slength += $tab_record['length'];
if ($debug > 1) { echo "<br>tab_record: ";	var_dump($tab_record); }
		$keyS = '`sid`, `name`, `swt_tid`, `dg`, `nr`, `published`, `zeit`, `edit_zeit`, `checked_out_time`';
		$valueS = $season.", 'Runde ".($i+1)."', ".$new_swt_tid.", 1, ".($i+1).", 1, '1970-01-01 00:00:00', '1970-01-01 00:00:00', '1970-01-01 00:00:00'";
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
	$paarprorunde[$i+1] = $tab_record['out'][9003][0];
	}
if ($debug > 2) { echo "<br>paarprorunde: ";	var_dump($paarprorunde); }
//die('rr');

	//	Spielerdaten -> Tabelle clm_turniere_tlnr
if ($debug > 0) { echo "<br><br>-- Spielerdaten --";	}
	$slength = 0;
	for ($i = 0; $i < $tournament["out"][4][0]; $i++) {
		$tab_record = zzparse_interpret($contents, 'player', ($apos[4]+$slength),($apos[5]-$apos[4]-$slength),$debug);
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
if ($debug > 2) { echo "<br>paramuseAsTWZ: $paramuseAsTWZ  twz: $twz  tab_player: ";	var_dump($tab_record); }
		$tab_record['out'][2008][0] = substr($tab_record['out'][2008][0],0,4); //Geburtsjahr
		$name = $tab_record['out'][2040][0].",".$tab_record['out'][2041][0];
		$keyS = '`sid`, `swt_tid`, `twz`, `name`, `tlnrStatus`, `snr`, `checked_out_time`';
		$valueS = $season.", ".$new_swt_tid.", ".$twz.", '".$name."', '1', ".($i+1).", '1970-01-01 00:00:00'";
		foreach ($tab_record['out'] as $tab) {
if ($debug > 2) { echo "<br>tab: ";	var_dump($tab); }
		if ($tab[1][2] == '0') continue;
			$keyS .= ',`'.$tab[1][2].'`';
			//$valueS .= ",'".clm_core::$db->escape($tab[0])."'";		
			$valueS .= ",'".$tab[0]."'";		
		}
		$sql = "INSERT INTO #__clm_swt_turniere_tlnr (".$keyS.") VALUES (".$valueS.")";
if ($debug > 1) { echo "<br>sql: ";	var_dump($sql); }

		clm_core::$db->query($sql);
	}
//die('play');

	//	Ergebnisdaten -> Tabelle clm_turniere_rnd_spl
if ($debug > 0) { echo "<br><br>-- Ergebnisdaten --";	}
	$slength = 0;
	$runde = 1;
	$brett = 0;
	//for ($i = 0; $i < $tournament["out"][1][0] * $paar_zahl; $i++) {
	for ($i = 0; $i < 5000; $i++) {
		$tab_record = zzparse_interpret($contents, 'individual_pairing', ($apos[5]+$slength),($apos[6]-$apos[5]-$slength),$debug);
		$slength += $tab_record['length'];
if ($debug > 1) { echo "<br>tab_record: $i ";	var_dump($tab_record); }
		//if ($i < ($runde * $paar_zahl)) {
		if ($brett < $paarprorunde[$runde]) {
			$brett++;
		} else {
			$runde++;
			if ($runde > $tournament["out"][1][0]) break;
			$brett = 1;
		}
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
//die('erg');
	
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
function zzparse_interpret($binary, $part, $start = 0, $end = false, $debug = 0) {
	//if ($part == 'general') $debug = 3; 
if ($debug > 1) { echo "<br>part: $part   start: $start   end: $end"; }
    $lang = clm_core::$lang->swm_import;
if ($debug > 3) { echo "<br>binary: $binary   <br>ende"; }
	if ($end) $binary = substr($binary, $start, $end);
	$data = array();
	$data['out'] = array();
	$structure = zzparse_structure($part);
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
if ($debug > 2) { echo "   length: $length"; }
			if ($length < 0 OR $length > 256) { 
				$lenght = 0;
				if ($debug > 2) echo "<br>length-error  byte:$istart";
			}
			$istart++; $istart++;
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
			$data['out'][$line[1]][0] = addslashes(utf8_encode($substring));
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
function zzparse_structure($part) {
	
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
					array('vch',9001,'startzeit'),
					array('ign',30,0),
					array('ind',9002,'datum'),
					array('ign',2,0),
					array('int',9003,0),		//Anzahl Paarungen in Runde
					array('ign',69,0)
					);
	if ($part == 'player') 
		$fields = array(
					array('vch',2040,0),
					array('vch',2041,0),
					array('vch',2044,0),
					array('vch',2000,0),
					array('vch',2002,'titel'),
					array('vch',2034,'PKZ'),
					array('ign',6,0),
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

function transcode_tiebr($tournament,$line_nr,$debug) {
	/* SWM -> CLM	(Beschreibung)
		1 ->  1	 Spielerpunkte
		5 ->  2	 Manuelle Eingabe (Ordering)
		8 ->  2	 Fidewertung (Progressive Score)
		9 ->  0	 Fidewertung mit Streichwerten (Progressive Score)
		11 ->  25  Direktvergleich
		19 ->  3  Sonneborn-Berger mit Fide-Korr.  
		23 ->  1  Elosumme mit Streichwert
		36 ->  2  Elo-Durchschnitt
		37 ->  1  Buchholz
		42 ->  2  Spielerpunkte + Vorrunde  
		43 ->  1  Punkte bei Stichkamf
		44 ->  2  Matchpunkte
		52 ->  6  Sonneborn-Berger variabel
		53 ->  6  Mehr Schwarz-Partien
		54 ->  2  Recursive Eloperformance  
		55 ->  1  durchsch, recursive Eloperformance der Gegner
		59 ->  2  Eloperformance mit 2 Streichwerten
		60 ->  6  Eloperformance variabel
		61 ->  1  Arranz-Sytem
		68 ->  2  Anzahl Siege variabel
		70 ->  6  Summe Buchholz variabel
	*/
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
