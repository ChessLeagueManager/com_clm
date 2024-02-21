<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 *
 * Import einer Kalenderdatei im ics-Format z.B. exportiert aus einer anderen CLM-Installation 
*/
function clm_api_db_term_import_ics($file,$test=false) {
    $lang = clm_core::$lang->terminliste;
	if ($test) $debug = 1; else $debug = 0;
//	$debug = 1;
	
	if ($test)	echo "<br><br>Test - keine Übernahme der Daten ins CLM!"; 
if ($debug > 0) { echo "<br><br>-- allgemeine Daten --";	}
if ($debug > 0) { echo "<br><br>dddatei: ".$file; } 		//echo "<br>end"; //die();
	$icsText = '';
	$fp = fopen($file, 'r+');
	fputs($fp, $icsText);
	rewind($fp);

	$data = '';
	$lines = [];
if ($debug > 0) { echo "<br><br>1lines :"; var_dump($lines); }
	while ( ($data = fgets($fp,200) ) !== FALSE ) {
if ($debug > 0) { echo "<br><br>1data :"; var_dump($data); }
    $lines[] = $data;
	}
if ($debug > 0) { echo "<br><br>2lines :"; var_dump($lines); }
//die();
if ($debug > 0) { echo "<br><br> $lang->name : $lang->beschreibung"; }
	$znr = 0;

	$uid2 = clm_core::$load->clm_path();	
if ($debug > 0) { echo "<br><br> uid2 :"; var_dump($uid2); }

	$ii = 0;
	$bvc = false;	$evc = false;
	$bve = false;	$evc = false;
	$bva = false;	$eva = false;
	$gpa = false;
	$vsb = '';
	$counter = 0;
	$a_event = array();
	// Durchgehen des Zeilen-Arrays 
	foreach ($lines as $line1) {
		$ii++;
if ($debug > 0) { echo "<br>line1 $ii :"; var_dump($line1); }
		if (!$bvc) {
			$fstr = 'BEGIN:VCALENDAR';
			if (substr($line1,0,strlen($fstr)) == $fstr) {
				$bvc = true;
				$bve = false;
if ($debug > 0) { echo "<br><br>$ii = $fstr "; }
			}
			continue;
		}
		if (!$bve) {
			$fstr = 'BEGIN:VEVENT';
			if (substr($line1,0,strlen($fstr)) == $fstr) {
				$bve = true;
				$eve = false;
				$gpa = false;
				$vsb = 'E';
				$event = array();
if ($debug > 0) { echo "<br><br>$ii = $fstr "; }
			}
			continue;
		}		
		if (ctype_upper(substr($line1,0,3))) {
			$gpa = true;
			$z = 0;
			$fstr = 'DTSTART';
			if (substr($line1,0,strlen($fstr)) == $fstr) {
				$event[$vsb][$fstr] = $line1;
				$akey = $fstr;
				continue;
			}
			$fstr = 'DTEND';
			if (substr($line1,0,strlen($fstr)) == $fstr) {
				$event[$vsb][$fstr] = $line1;
				$akey = $fstr;
				continue;
			}
			$fstr = 'DESCRIPTION';
			if (substr($line1,0,strlen($fstr)) == $fstr) {
				$event[$vsb][$fstr] = $line1;
				$akey = $fstr;
				continue;
			}
			$fstr = 'SUMMARY';
			if (substr($line1,0,strlen($fstr)) == $fstr) {
				$event[$vsb][$fstr] = $line1;
				$akey = $fstr;
				continue;
			}
			$fstr = 'LOCATION';
			if (substr($line1,0,strlen($fstr)) == $fstr) {
				$event[$vsb][$fstr] = $line1;
				$akey = $fstr;
				continue;
			}
			$fstr = 'CLM-HOST';
			if (substr($line1,0,strlen($fstr)) == $fstr) {
				$event[$vsb][$fstr] = $line1;
				$akey = $fstr;
				continue;
			}
			$fstr = 'ATTACH';
			if (substr($line1,0,strlen($fstr)) == $fstr) {
				$event[$vsb][$fstr] = $line1;
				$akey = $fstr;
				continue;
			}
			$fstr = 'UID';
			if (substr($line1,0,strlen($fstr)) == $fstr) {
				$event[$vsb][$fstr] = $line1;
				$akey = $fstr;
				continue;
			}
			$fstr = 'CREATED';
			if (substr($line1,0,strlen($fstr)) == $fstr) {
				$event[$vsb][$fstr] = $line1;
				$akey = $fstr;
				continue;
			}
			$fstr = 'LAST-MODIFIED';
			if (substr($line1,0,strlen($fstr)) == $fstr) {
				$event[$vsb][$fstr] = $line1;
				$akey = $fstr;
				continue;
			}
			$fstr = 'BEGIN:VALARM';
			if (substr($line1,0,strlen($fstr)) == $fstr) {
				$vsb = 'A';
				continue;
			}
			$fstr = 'TRIGGER';
			if (substr($line1,0,strlen($fstr)) == $fstr) {
				$event[$vsb][$fstr] = $line1;
				$akey = $fstr;
				continue;
			}
			$fstr = 'END:VALARM';
			if (substr($line1,0,strlen($fstr)) == $fstr) {
				$vsb = 'E';
				continue;
			}
			$fstr = 'END:VEVENT';
			if (substr($line1,0,strlen($fstr)) == $fstr) {
				$a_event[] = $event;
				$event = array();
				$bve = false;
				continue;
			}
			$akey = 'CLM-REST';
			continue;
		} else {	
			if (!$gpa OR $akey == 'CLM-REST') {
if ($debug > 0) { echo "<br><br>$ii ungültige Folgezeile $line1 "; }
				continue;
			}
			$z++;
			$event[$vsb][$akey.$z] = $line1;
			continue;
		}
	}	
if ($debug > 0) { echo "<br><br>a_event"; var_dump($a_event); }
if ($debug > 0) { echo "<br><br>event "; var_dump($event); }
	
//die();	
	$msg = array();;
	foreach ($a_event as $event) {
		$name = '';
		$beschreibung = '';
		$address = '';
		$host = '';
		$startdate ='1970-01-01'; $starttime = '00:00';
		$enddate ='1970-01-01'; $endtime = '00:00';
		$allday = 0;
		$noendtime = 0;
		$event_link = '';
		$uid = '';
		$created = '';
		$last_modified = '';

		$fstr = 'SUMMARY';
		if (isset($event['E'][$fstr])) {
			$pdp = strpos($event['E'][$fstr],':');
			if ($pdp === false) {
				$msg[] = 'Fehler '.$fstr;
			}
			$name = substr($event['E'][$fstr],$pdp+1);
		} else {
			$msg[] = $fstr.' fehlt';
		}
		$fstr = 'DTSTART';
		if (isset($event['E'][$fstr])) {
			$pdp = strpos($event['E'][$fstr],':');
			if ($pdp === false) {
				$msg[] = 'Fehler '.$fstr;
			}
			$startdate = substr($event['E'][$fstr],$pdp+1,4).'-'.substr($event['E'][$fstr],$pdp+5,2).'-'.substr($event['E'][$fstr],$pdp+7,2);
			$starttime = substr($event['E'][$fstr],$pdp+10,2).':'.substr($event['E'][$fstr],$pdp+12,2);
		} else {
			$msg[] = $fstr.' fehlt';
		}
		$fstr = 'DTEND';
		if (isset($event['E'][$fstr])) {
			$pdp = strpos($event['E'][$fstr],':');
			if ($pdp === false) {
				$msg[] = 'Fehler '.$fstr;
			}
			$enddate = substr($event['E'][$fstr],$pdp+1,4).'-'.substr($event['E'][$fstr],$pdp+5,2).'-'.substr($event['E'][$fstr],$pdp+7,2);
			$endtime = substr($event['E'][$fstr],$pdp+10,2).':'.substr($event['E'][$fstr],$pdp+12,2);
		}
		$fstr = 'DESCRIPTION';
		if (isset($event['E'][$fstr])) {
			$pdp = strpos($event['E'][$fstr],':');
			if ($pdp === false) {
				$msg[] = 'Fehler '.$fstr;
			} else {
				$beschreibung = substr($event['E'][$fstr],$pdp+1);
				$z = 1;
				while (isset($event['E'][$fstr.$z])) {
					$pdp = strpos($event['E'][$fstr],':');
					if ($pdp === false) {
						$msg = 'Fehler '.$fstr.$z;
						break;
					}
//					$beschreibung .= substr($event['E'][$fstr.$z],4);
					$beschreibung .= ltrim($event['E'][$fstr.$z],' ');
					$z++;
				}
			}
		}
		$fstr = 'LOCATION';
		if (isset($event['E'][$fstr])) {
			$pdp = strpos($event['E'][$fstr],':');
			if ($pdp === false) {
				$msg[] = 'Fehler '.$fstr;
			}
			$address = substr($event['E'][$fstr],$pdp+1);
		} else {
//			$msg[] = $fstr.' fehlt';
		}
		$fstr = 'CLM-HOST';
		if (isset($event['E'][$fstr])) {
			$pdp = strpos($event['E'][$fstr],':');
			if ($pdp === false) {
				$msg[] = 'Fehler '.$fstr;
			}
			$host = substr($event['E'][$fstr],$pdp+1);
		} else {
//			$msg[] = $fstr.' fehlt';
		}
		$fstr = 'ATTACH';
		if (isset($event['E'][$fstr])) {
			$pdp = strpos($event['E'][$fstr],':');
			if ($pdp === false) {
				$msg[] = 'Fehler '.$fstr;
			}
			$event_link = substr($event['E'][$fstr],$pdp+1);
		} else {
//			$msg[] = $fstr.' fehlt';
		}
		$fstr = 'UID';
		if (isset($event['E'][$fstr])) {
			$pdp = strpos($event['E'][$fstr],':');
			if ($pdp === false) {
				$msg[] = 'Fehler '.$fstr;
			}
			$uid = substr($event['E'][$fstr],$pdp+1);
		}
		$fstr = 'CREATED';
		if (isset($event['E'][$fstr])) {
			$pdp = strpos($event['E'][$fstr],':');
			if ($pdp === false) {
				$msg[] = 'Fehler '.$fstr;
			}
			$created = substr($event['E'][$fstr],$pdp+1);
		} else {
			$created = date("Ymd\THis\Z");
		}
		$fstr = 'LAST-MODIFIED';
		if (isset($event['E'][$fstr])) {
			$pdp = strpos($event['E'][$fstr],':');
			if ($pdp === false) {
				$msg[] = 'Fehler '.$fstr;
			}
			$last_modified = substr($event['E'][$fstr],$pdp+1);
		} else {
			$last_modified = date("Ymd\THis\Z");
		}

if ($debug > 0) { echo "<br><br>name "; var_dump($name); }
if ($debug > 0) { echo "<br><br>startdate "; var_dump($startdate); }
if ($debug > 0) { echo "<br><br>starttime "; var_dump($starttime); }
if ($debug > 0) { echo "<br><br>enddate "; var_dump($enddate); }
if ($debug > 0) { echo "<br><br>endtime "; var_dump($endtime); }
if ($debug > 0) { echo "<br><br>beschreibung "; var_dump($beschreibung); }
if ($debug > 0) { echo "<br><br>address "; var_dump($address); }
if ($debug > 0) { echo "<br><br>host "; var_dump($host); }
if ($debug > 0) { echo "<br><br>event_link "; var_dump($event_link); }
if ($debug > 0) { echo "<br><br>uid "; var_dump($uid); }
if ($debug > 0) { echo "<br><br>created "; var_dump($created); }
if ($debug > 0) { echo "<br><br>last_modified "; var_dump($last_modified); }
if ($debug > 0) { echo "<br><br>msg "; var_dump($msg); }

//die();


		$query = " INSERT INTO #__clm_termine "
		." ( `name`,`beschreibung`,`address`,`host`,`startdate`,`starttime`,`allday`,`enddate`,`endtime`,`noendtime`,`published`"
		.",`event_link`,`uid`,`created`,`last_modified`) "
		." VALUES ('$name','$beschreibung','$address','$host','$startdate','$starttime','$allday','$enddate','$endtime','$noendtime','0'"
		.",'$event_link','$uid','$created','$last_modified') "
		;
if ($debug > 0) { echo "<br>query :"; var_dump($query); }
//die();
		if (!$test) { 
			$counter++;
			clm_core::$db->query($query);
			$written_id = clm_core::$db->insert_id();
			if ($uid == '') {
				$query = "UPDATE #__clm_termine "
					." SET uid = '".'CLM-A'.$written_id.uniqid($uid2)."'"
					." WHERE id = ".$written_id
				;
				clm_core::$db->query($query);
			}
		}

	}

if ($debug > 0) { echo "<br>counter :"; var_dump($counter); }
if ($debug > 0) { die('-ende-'); }
	
		return array(true, "m_termImportSuccess",$counter); 
}

?>
