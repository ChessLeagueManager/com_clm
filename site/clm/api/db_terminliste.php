<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// Eingang: ausgewälte ID's oder nichts 
function clm_api_db_terminliste($cid = array()) {
	$lang = clm_core::$lang->terminliste;
	$out["input"]["cid"] = $cid;
	$date = date("Y-m-d");

	//CLM parameter auslesen
	$config		= clm_core::$db->config();
	$countryversion = $config->countryversion;

  	$terminModel = " SELECT * "
				." FROM #__clm_termine "
				." WHERE published != '0' AND ";
	if (is_array($cid) AND count($cid) > 0) {
		$terminModel .= " `id` IN (" . implode(',', array_map('intval', $cid)) . ")";
	} else {
		$terminModel .= " TO_DAYS(startdate) >= TO_DAYS('".$date."')";
	}
	$out["termine"] = clm_core::$db->loadObjectList($terminModel);

	// Kein Ergebnis -> Daten Inkonsistent oder falsche Eingabe
	if (!isset($out["termine"][0])) {
		return array(false, "e_terminlisteError");
	}	

	if (!is_array($cid) OR count($cid) < 1) { // Aufruf im Frontend
		return array(true, "m_terminlisteSuccess", $out);
	}
	
	// Aufruf im Backend
 
	$termine 	= $out["termine"];

	$output = array();
	
	$now = time();
	$first = true;

// Terminschleife 	
	foreach ($termine as $term) { 
		if ($first) {
			$first = false;
			$line = array();	
			$line[1] = $lang->name;
			$line[2] = $lang->beschreibung;
			$line[3] = $lang->address;
			$line[4] = $lang->host;
			$line[5] = $lang->event_link;
			$line[6] = $lang->startdate;
			$line[7] = $lang->starttime;
			$line[8] = $lang->allday;
			$line[9] = $lang->enddate;
			$line[10] = $lang->endtime;
			$line[11] = $lang->noendtime;
			$line[12] = $lang->published;
			$output[] = $line;
		}

		$line = array();	
		$line[1] = str_replace(array('„','“','"','”'),' ',$term->name);
		$line[1] = clm_core::$load->utf8decode(str_replace("'",' ',$line[1]));
		$line[2] = str_replace(array('„','“','"','”'),' ',$term->beschreibung);
		$line[2] = clm_core::$load->utf8decode(str_replace("'",' ',$line[2]));
		$line[3] = str_replace(array('„','“','"','”'),' ',$term->address);
		$line[3] = clm_core::$load->utf8decode(str_replace("'",' ',$line[3]));
		$line[4] = clm_core::$load->utf8decode($term->host);
		$line[5] = clm_core::$load->utf8decode($term->event_link);
		$line[6] = clm_core::$load->utf8decode($term->startdate);
		$line[7] = clm_core::$load->utf8decode($term->starttime);
		$line[8] = clm_core::$load->utf8decode($term->allday);
		$line[9] = clm_core::$load->utf8decode($term->enddate);
		$line[10] = clm_core::$load->utf8decode($term->endtime);
		$line[11] = clm_core::$load->utf8decode($term->noendtime);
		$line[12] = clm_core::$load->utf8decode($term->published);
		$output[] = $line;
    } 

// Ausgabe
	if(count($output)==0) {
		return array(false, "e_TerminlisteNoDataError");
	}
		
	$nl = "\n";
	$file_name = clm_core::$load->utf8decode($lang->title);   
	$file_name .= '.csv'; 
	$file_name = strtr($file_name,' ','_');
	$file_name = strtr($file_name,"/","_");
	if (!file_exists('components'.DS.'com_clm'.DS.'pgn'.DS)) mkdir('components'.DS.'com_clm'.DS.'pgn'.DS);
	$pdatei = fopen('components'.DS.'com_clm'.DS.'pgn'.DS.$file_name,"wt");
	foreach($output as $line1) {
		$return = fputcsv($pdatei, $line1);
	}
	fclose($pdatei);

	return array(true, "m_terminlisteSuccess", $file_name);

}
?>
