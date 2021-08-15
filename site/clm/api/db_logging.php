<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_api_db_logging() {
	$table = '#__clm_logging';
	$primaryKey = 'id';
	$columns = array(
		array( 'db' => 'id', 'dt' => 0),
		array( 'db' => 'type',   'dt' => 1),
		array( 'db' => 'name', 'dt' => 2),
		array( 'db' => 'content','dt' => 3),
		array( 'db' => 'timestamp', 'dt' => 4),
		array( 'db' => 'userid', 'dt' => 5),
		array( 'db' => 'callid', 'dt' => 6),
		array( 'db' => 'id', 'dt' => 7)
	);
	$allowed = array("type" => "i");
	$out = clm_class_DataTables::simple($_POST, $table, $primaryKey, $columns, $allowed,clm_core::$db);
	$lang = clm_core::$lang->logging;

	for($i=0;$i<count($out["data"]);$i++) {
		$out["data"][$i][0] = $i+1;
		if(!clm_core::$db->user->get($out["data"][$i][5])->isNew()) {
			$out["data"][$i][5] = clm_core::$db->user->get($out["data"][$i][5])->username;
		}
		$out["data"][$i][4] = date("d.m.y H:i:s",$out["data"][$i][4]);
//		$out["data"][$i][4] = clm_core::$cms->showDate($out["data"][$i][4], $format = "d.m.y H:i:s");
		
		$result = clm_class_log::refactor($out["data"][$i][1],$out["data"][$i][2],$out["data"][$i][3]);
		$out["data"][$i][1] = $result[0];
		$out["data"][$i][2] = $result[1];
		$out["data"][$i][3] = $result[2];
	}
	return array(true,"m_tableSuccess",$out);
}