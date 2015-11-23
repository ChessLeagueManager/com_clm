<?php
function clm_api_db_ecf_club($vereine = array()) {
	@set_time_limit(0); // hope
	$sid = clm_core::$access->getSeason();
	if (!is_array($vereine) || count($vereine) == 0) {
		return array(true, "w_noClubToUpdate");
	}
	// Organisation from config
	$config		= clm_core::$db->config();
	$lv	= $config->lv;
	// Entry from _clm_dwz_verbaende
	$sql = " SELECT * FROM #__clm_dwz_verbaende WHERE Verband = '".$lv."'";
	$verband = clm_core::$db->loadObjectList($sql);	
	
	$counter = 0;
	$sql = "REPLACE INTO #__clm_dwz_vereine (`sid`,`ZPS`, `LV`, `Verband`, `Vereinname`) VALUES (?, ?, ?, ?, ?)";
	$stmt = clm_core::$db->prepare($sql);
	for ($i = 0;$i < count($vereine);$i++) {
		//Club,Code,Union,County
		$einVerein = str_getcsv($vereine[$i], ",", '"');
		if (count($einVerein) != 4) {
			continue;
		}
		if ($verband[0]->Allocation != $einVerein[3]) {
			continue;
		}
		if (strlen($einVerein[0]) < 1) continue;
		if (substr($einVerein[0],(strlen($einVerein[0])-1),1) == "*") continue;
		$stmt->bind_param('sssss', $sid, $einVerein[1], $verband[0]->LV, $lv, $einVerein[0]);
		$stmt->execute();
		$counter++;
	}
	$stmt->close();
	return array(true, "m_ecfClubSuccess", $counter);
}
?>
