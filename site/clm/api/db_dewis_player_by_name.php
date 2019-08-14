<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2018 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_api_db_dewis_player_by_name($name = '', $vorname = '', $year = '') {
	@set_time_limit(0); // hope
	$source = "https://dwz.svw.info/services/files/dewis.wsdl";
	if ($name == '' OR $vorname == '' ) {
		return array(false, "e_wrongDataFormat");
	}
	$counter = 0;
	// SOAP Webservice
	try {
		$client = clm_core::$load->soap_wrapper($source);

		// Personenliste entspr. Namen
		$searchByNameList	= $client->searchByName($name,$vorname);
		$searchByNameList1	= $client->searchByName($name,$vorname);
		$searchByName = array();
		$stcard = 0;	
		// Detaildaten zu Mitgliedern lesen
		foreach ($searchByNameList->members as $p) {
			$stcard++;
			if ($year != '' AND $p->yearOfBirth != $year) {
				continue;
			}
			$searchByName[] = $p;
			$counter++;
		}
		unset($searchByNameList);
		unset($client);
	}
	catch(SOAPFault $f) {
		if($f->getMessage() == "that is not a valid name" || $f->getMessage() == "that name does not exists") {
			return array(true, "w_wrongName",0);
		}
		return array(false, "e_connectionError");
	}
	return array(true, "m_dewisPlayerSuccess", $counter,$searchByName);
}
?>
