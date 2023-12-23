<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 *
 * Import einer CLM-Kalenderdatei im CSV-Format z.B. exportiert aus einer anderen CLM-Installation 
*/
function clm_api_db_term_import($file,$test=false) {
    $lang = clm_core::$lang->terminliste;
	if ($test) $debug = 1; else $debug = 0;
	$debug = 1;
	if ($test)	echo "<br><br>Test - keine Übernahme der Daten ins CLM!"; 
if ($debug > 0) { echo "<br><br>-- allgemeine Daten --";	}
if ($debug > 0) { echo "<br><br>datei: ".$file; } 		//echo "<br>end"; //die();

	$fp = fopen($file, 'r+');
	fputs($fp, $csvText);
	rewind($fp);

	$lines = [];
	while ( ($data = fgetcsv($fp) ) !== FALSE ) {
    $lines[] = $data;
	}
	$znr = 0;
	$ii = 0;
	$counter = 0;
	// Durchgehen des Zeilen-Arrays 
	foreach ($lines as $line) {
		$ii++;
if ($debug > 0) { echo "<br><br>line $ii :"; var_dump($line); }
if ($debug > 0) { echo "<br>line0 $ii :"; var_dump($line[0]); }
		if (!isset($line[0]) OR $line[0] == '' OR $line[0] == ' ') continue;
		$znr++;
if ($debug > 0) { echo "<br>znr $ii :"; var_dump($znr); }
		if ($znr == 1) {
			if ($line[0] != $lang->name OR $line[1] != $lang->beschreibung) {
				echo '<p>Inputdatei ist keine CLM-Kalenderdatei!</p>';
				return false;
			}
			continue;	
		}
		$line[0] = clm_core::$load->utf8encode($line[0]);
		$line[1] = clm_core::$load->utf8encode($line[1]);
		$line[2] = clm_core::$load->utf8encode($line[2]);
		if (substr($line[5],2,1) == '.')
			$line[5] = substr($line[5],6,4).'-'.substr($line[5],3,2).'-'.substr($line[5],0,2);
		if (substr($line[8],2,1) == '.')
			$line[8] = substr($line[8],6,4).'-'.substr($line[8],3,2).'-'.substr($line[8],0,2);
		//Name,Beschreibung,Ort,Organisator,Event-Link,Starttag,Startzeit,Ganztagsevent,Endedatum,Endezeit,Ende unbestimmt,freigegeben
		$query = " INSERT INTO #__clm_termine "
		." ( `name`,`beschreibung`,`address`,`host`,`startdate`,`starttime`,`allday`,`enddate`,`endtime`,`noendtime`,`published`,`event_link`) "
		." VALUES ('$line[0]','$line[1]','$line[2]','$line[3]','$line[5]','$line[6]','$line[7]','$line[8]','$line[9]','$line[10]','0','$line[4]') "
		;
if ($debug > 0) { echo "<br>query :"; var_dump($query); }
		if (!$test) { 
			$counter++;
			clm_core::$db->query($query);
		}
	}

	return array(true, "m_termImportSuccess",$counter); 
}

?>
