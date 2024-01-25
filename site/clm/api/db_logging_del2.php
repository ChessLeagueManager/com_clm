<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_api_db_logging_del2($id) {
	$anz_del = 0;
	if($id==null) {
		$sql =  "SELECT COUNT(*) AS anzahl FROM #__clm_logging";
		$count = clm_core::$db->loadObjectList($sql);
		$anzahl = $count[0]->anzahl - 100; 
		if ($anzahl > 0) {
			$sql =  "DELETE FROM #__clm_logging ORDER BY timestamp ASC limit ".$anzahl;
			clm_core::$db->query($sql);
			$anz_del = clm_core::$db->affected_rows();
		}
	}
	if(is_numeric($id)) {
		$sql =  "DELETE FROM #__clm_logging WHERE id=".$id;
		clm_core::$db->query($sql);
		$anz_del = clm_core::$db->affected_rows();
	}
	return array(true, $anz_del);
}
?>