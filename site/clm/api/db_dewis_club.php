<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2018 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_api_db_dewis_club($verband = - 1) {
	@set_time_limit(0); // hope
	$source = "https://dwz.svw.info/services/files/dewis.wsdl";
	$sid = clm_core::$access->getSeason();
	$verband = clm_core::$load->make_valid($verband, 8, "");
	if (strlen($verband) != 3) {
		return array(false, "e_wrongUnitFormat");
	}
	$counter = 0;
	// SOAP Webservice
	try {
		$client = clm_core::$load->soap_wrapper($source);
		// Alle Vereine auslesen und Array verflachen
		$out = clm_core::$load->array_flatten($client->organizations(), array("club", "vkz"));
		unset($client);
	}
	catch(SOAPFault $f) {
		return array(false, "e_connectionError");
	}
	$str = '';
	$sql = "REPLACE INTO #__clm_dwz_vereine (`sid`,`ZPS`, `LV`, `Verband`, `Vereinname`) VALUES (?, ?, ?, ?, ?)";
	$stmt = clm_core::$db->prepare($sql);
	$compare = clm_core::$load->unit_range($verband);
	for ($i = 0;$i < count($out)/2;$i++) {
		if (strlen($out[$i * 2 + 1]) == 5 && substr($out[$i * 2 + 1], 0, 3) >= $compare[0] && substr($out[$i * 2 + 1], 0, 3) <= $compare[1] && !clm_core::$load->ends_with($out[$i * 2 + 1], "00")) {
			$LV = substr($out[$i * 2 + 1], 0, 1);
			$Verband = substr($out[$i * 2 + 1], 0, 3);
			$stmt->bind_param('sssss', $sid, $out[$i * 2 + 1], $LV, $Verband, $out[$i * 2]);
			$result = $stmt->execute();
			$counter++;
			if ($result === false) { $str .= " ".$out[$i * 2 + 1]; }
		}
	}
	$stmt->close();
	return array(true, "m_dewisClubSuccess".$str, $counter);
}
?>
