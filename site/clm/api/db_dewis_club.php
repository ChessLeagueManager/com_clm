<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
function clm_api_db_dewis_club($verband = - 1) {
	@set_time_limit(0); // hope
	$sid = clm_core::$access->getSeason();
	$verband = clm_core::$load->make_valid($verband, 8, "");
	if (strlen($verband) != 3) {
		return array(false, "e_wrongUnitFormat");
	}
	$compare = clm_core::$load->unit_range($verband);
	if (substr($verband,0,3) == '000') $verband = '';
	elseif (substr($verband,1,2) == '00') $verband = substr($verband, 0,1);
	elseif (substr($verband,2,1) == '0') $verband = substr($verband, 0,2);
	$counter = 0;
	// SOAP Webservice
	try {
		$client = new clm_class_OAuth2Client();
		// Vereine eines Schachverbandes
		$result = $client->callApiWithRefresh($client->apiBaseUrl . '/dwz/dwzliste/clubs?vkz='.$verband);
		$out = $result["body"]["data"];
		unset($client);
	}
	catch(SOAPFault $f) {
		return array(false, "e_connectionError");
	}
	$str = '';
	$sql = "REPLACE INTO #__clm_dwz_vereine (`sid`,`ZPS`, `LV`, `Verband`, `Vereinname`) VALUES (?, ?, ?, ?, ?)";
	$stmt = clm_core::$db->prepare($sql);
	for ($i = 0;$i < count($out);$i++) {
		if (strlen($out[$i]["clubVkz"]) != 5 && substr($out[$i]["clubVkz"], 3, 2) == '00') continue;
		if (substr($out[$i]["clubVkz"], 0, 3) >= $compare[0] && substr($out[$i]["clubVkz"], 0, 3) <= $compare[1] && !clm_core::$load->ends_with($out[$i]["clubVkz"], "00")) {
			$LV = $out[$i]["federation"];
			$Verband = $out[$i]["parentFederation"];
			$stmt->bind_param('sssss', $sid, $out[$i]["clubVkz"], $LV, $Verband, $out[$i]["clubName"]);
			$result = $stmt->execute();
			$counter++;
			if ($result === false) { $str .= " ".$out[$i]["clubVkz"]; }
		}
	}
	$stmt->close();
	return array(true, "m_onlineClubSuccess".$str, $counter);
}
?>
