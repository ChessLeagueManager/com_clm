<?php
function clm_api_db_dsb_club($vereine = array()) {
	@set_time_limit(0); // hope
	$sid = clm_core::$access->getSeason();
	if (!is_array($vereine) || count($vereine) == 0) {
		return array(true, "w_noClubToUpdate");
	}
	$counter = 0;
	$sql = "REPLACE INTO #__clm_dwz_vereine (`sid`,`ZPS`, `LV`, `Verband`, `Vereinname`) VALUES (?, ?, ?, ?, ?)";
	$stmt = clm_core::$db->prepare($sql);
	for ($i = 0;$i < count($vereine);$i++) {
		//ZPS,LV,Verband,Vereinname
		$einVerein = str_getcsv($vereine[$i], ",", '"');
		if (count($einVerein) != 4) {
			continue;
		}
		$stmt->bind_param('sssss', $sid, $einVerein[0], $einVerein[1], $einVerein[2], $einVerein[3]);
		$stmt->execute();
		$counter++;
	}
	$stmt->close();
	return array(true, "m_dsbClubSuccess", $counter);
}
?>
