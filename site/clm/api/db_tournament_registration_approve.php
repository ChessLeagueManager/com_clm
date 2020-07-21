<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// input: online registration key

function clm_api_db_tournament_registration_approve($reg_id) {
	if(!isset($reg_id) OR $reg_id == '') {
		return array(false,"e_unexpectedInput");
	}

	$query = "SELECT * FROM #__clm_online_registration WHERE pid = '".$reg_id."'";
	$registration = clm_core::$db->loadObjectList($query);
	if($registration == null) {
		return array(false,"e_nonexistingInput");
	}
	$tid = $registration[0]->tid;
	if(count($registration) != 1) {
		return array(false,"e_toomuchRecords",$tid);
	}
	if($registration[0]->approved == 1) {
		return array(false,"e_alreadyApproved",$tid);
	}
	if($registration[0]->timestamp < (time() - (72 * 60 * 60))) {
		return array(false,"e_toolateApproved",$tid);
	}

	$query = " UPDATE #__clm_online_registration "
			." SET  approved = 1"
			." WHERE pid = '".$reg_id."'";
	clm_core::$db->query($query);

	return array(true,"m_registrationApproveSuccess",$tid);
}
?>
