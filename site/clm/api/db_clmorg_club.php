<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
function clm_api_db_clmorg_club($verband = - 1) {
	@set_time_limit(0); // hope
	$sid = clm_core::$access->getSeason();
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$dewis_import_delay = $config->dewis_import_delay;
	$clm_key = $config->clmorg_data_key;
	$clm_domain = $config->request_domain;

	$verband = clm_core::$load->make_valid($verband, 8, "");
//clm_core::$api->test_print('verband',$verband);	
	if (strlen($verband) != 3) {
		return array(false, "e_wrongUnitFormat");
	}
	$compare = clm_core::$load->unit_range($verband);
	if (substr($verband,0,3) == '000') $verband = '';
	elseif (substr($verband,1,2) == '00') $verband = substr($verband, 0,1);
	elseif (substr($verband,2,1) == '0') $verband = substr($verband, 0,2);
	$counter = 0;

	$clm_zps = $verband;

	$vereinsdatenurl = 'https://spielerdaten.chessleaguemanager.org:/vereine.php';

	// Webservice
	try {
        $ch = curl_init($vereinsdatenurl);
        $post_data = array('clm_key' => $clm_key, 'clm_zps' => $clm_zps, 'clm_domain' => $clm_domain);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode !== 200) {
            $error = json_encode(['error php_verein' => 'Token ist ung&uuml;ltig oder abgelaufen (httpCode=' . $httpCode . "/" . json_encode($response) . ")."]);
clm_core::$api->test_print('Error1',$error);
			return array(false, "http_code_not200", $error);
        }

		// Vereine einer Organisation
        $clublist = json_decode($response, true);
//clm_core::$api->test_print('Data',$clublist);
	}
	catch (RuntimeException $e) {
        $error = json_encode(['runtime error test_php_verein' => "❌ Fehler: " . $e->getMessage()]);
//clm_core::$api->test_print('Error',$error);
		return array(false, "Exception geflogen", $error);
	} 
//die();	
	$str = '';
	$sql = "REPLACE INTO #__clm_dwz_vereine (`sid`,`ZPS`, `LV`, `Verband`, `Vereinname`) VALUES (?, ?, ?, ?, ?)";
	$stmt = clm_core::$db->prepare($sql);
	foreach ($clublist['data'] as $club) {
		if (strlen($club["ZPS"]) != 5 && substr($club["ZPS"], 3, 2) == '00') continue;
		if (substr($club["ZPS"], 0, 3) >= $compare[0] && substr($club["ZPS"], 0, 3) <= $compare[1] && !clm_core::$load->ends_with($club["ZPS"], "00")) {
			$LV = $club["LV"];
			$Verband = $club["Verband"];
			$stmt->bind_param('sssss', $sid, $club["ZPS"], $LV, $Verband, $club["Vereinname"]);
			$result = $stmt->execute();
			$counter++;
			if ($result === false) { $str .= " ".$club["ZPS"]; }
		}
	}
	$stmt->close();
	return array(true, "m_clmorgClubSuccess".$str, $counter);
}
?>
