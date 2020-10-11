<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_api_db_ecfv2_club($verband = - 1) {
	@set_time_limit(0); // hope
	$source = "https://www.ecfrating.org.uk/v2/new/api.php?v2/clubs/all_active";
	$sid = clm_core::$access->getSeason();
	$verband = clm_core::$load->make_valid($verband, 8, "");
	if (strlen($verband) != 4) {
		return array(false, "e_wrongUnitFormat", $verband);
	}
	$counter = 0;
	$out = file_get_contents($source);
	$a_out = json_decode($out);
	
	$str = '';
	$sql = "REPLACE INTO #__clm_dwz_vereine (`sid`,`ZPS`, `LV`, `Verband`, `Vereinname`) VALUES (?, ?, ?, ?, ?)";
	$stmt = clm_core::$db->prepare($sql);
	$i = 0;
	foreach ($a_out->clubs as $club) {
		$i++;
		if ($verband != $club->assoc_code) continue;
		$LV = 'E';
		$stmt->bind_param('sssss', $sid, $club->club_code, $LV, $club->assoc_code, $club->club_name);
		$result = $stmt->execute();
		$counter++;
		if ($result === false) { $str .= " ".$club->club_code; }
	}
	$stmt->close();
	return array(true, "m_dewisClubSuccess".$str, $counter);

}
?>
