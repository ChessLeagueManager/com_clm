<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
/**
* erstellt pgn-Ausgabe eines Mannschafts- oder Einzelturniers
*/
function clm_api_db_pgn_export($id,$group=true) {
	$id = clm_core::$load->make_valid($id, 0, -1);
		
	if($group) {
		// Liga auslesen
		$query = 'SELECT * FROM #__clm_liga'
			. ' WHERE id = '.$id;
		$turnier	= clm_core::$db->loadObjectList($query);
	
		// pgn-Notationen auslesen
		$query = " SELECT * FROM #__clm_pgn as a "
			." WHERE a.tkz = 't' "
			." AND a.tid = ".$id
			." AND a.dg > 0 "
			." ORDER BY a.dg, a.runde, a.paar, a.brett ";
		$pgn = clm_core::$db->loadObjectList($query);
		
	} else {
		// Turnier auslesen
		$query = 'SELECT * FROM #__clm_turniere'
			.' WHERE id = '.$id;
		$turnier	= clm_core::$db->loadObjectList($query);
	
		// pgn-Notationen auslesen
		$query = " SELECT * FROM #__clm_pgn as a "
			." WHERE a.tkz = 's' "
			." AND a.tid = ".$id
			." AND a.dg > 0 "
			." ORDER BY a.dg, a.runde, a.paar, a.brett ";
		$pgn = clm_core::$db->loadObjectList($query);
		if(is_null($pgn) OR count($pgn)==0) {
			$query = " SELECT pgn as text FROM #__clm_turniere_rnd_spl as a "
				." WHERE a.turnier = ".$id
				." AND a.dg > 0 "
				." AND a.heim = 1 "
				." ORDER BY a.dg, a.runde, a.brett ";
			$pgn = clm_core::$db->loadObjectList($query);
		}		
	}
	if(count($pgn)==0) {
		return array(false, "e_PgnNoDataError");
	}
		
	$nl = "\n";
	$file_name = clm_core::$load->utf8decode($turnier[0]->name);
	$file_name .= '.pgn'; 
	$file_name = strtr($file_name,' ','_');
	$file_name = strtr($file_name,"/","_");
	if (!file_exists('components'.DS.'com_clm'.DS.'pgn'.DS)) mkdir('components'.DS.'com_clm'.DS.'pgn'.DS);
	$pdatei = fopen('components'.DS.'com_clm'.DS.'pgn'.DS.$file_name,"wt");
	foreach($pgn as $pgn1) {
		fputs($pdatei, clm_core::$load->utf8decode($pgn1->text).$nl);
	}
	fclose($pdatei);
    header('Content-Disposition: attachment; filename='.$file_name);
	header('Content-type: text/html');
	header('Cache-Control:');
	header('Pragma:');
	flush();
	readfile('components'.DS.'com_clm'.DS.'pgn'.DS.$file_name);
	flush();
	exit;
	
	return array(true, "m_PgnExportSuccess"); 
}
?>
