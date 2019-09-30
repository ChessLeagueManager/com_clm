<?php
function clm_api_db_tournament_registration_del($id,$registration) {
	$id = clm_core::$load->make_valid($id, 0, -1);
	if(!is_array($registration) || count($registration)==0) {
		return array(false,"e_unexpectedInput");
	}

	for($i=0;$i<count($registration);$i++) {
		$registrationId = intval($registration[$i]);
		$query='DELETE FROM #__clm_online_registration WHERE id = '.$registrationId;
		clm_core::$db->query($query);
	}

	return array(true,"m_tournamentRegistrationDeleteSuccess");
}
?>
