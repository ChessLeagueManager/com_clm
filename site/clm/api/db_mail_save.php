<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_api_db_mail_save($return_section, $return_view, $cids, $mail_to, $mail_subj, $mail_body) {

	// Bereitstellung der Detaildaten
	$out = clm_core::$api->db_mail($return_section, $return_view, $cids);
	if (!$out[0]) {
		return array(false, $out[1]);
	}
	$out = $out[2];
	
	/* Start: Übernahme der Detaildaten */
	$users = $out["users"];
	$auser = $out["auser"];

	$mail_cc = clm_core::$load->sub_umlaute($auser[0]->name)." <".$auser[0]->email.">"; 

	$mail_to = '';
	for ($x=0; $x < (count($users)); $x++) {
		if ($x > 0) $mail_to .= ', ';
		$mail_to .= clm_core::$load->sub_umlaute($users[$x]->name)." <".$users[$x]->email.">"; 
	}

	// Datum und Uhrzeit für Meldung
	$now = clm_core::$cms->getNowDate();
	
	// Konfigurationsparameter auslesen
	$config = clm_core::$db->config();
	$from = $config->email_from;
	$fromname = $config->email_fromname;
	$htmlMail = $config->email_type;
	if ( $from == '' ) {
			return array(false, "e_mailSendErrorNoMailsEmail");
	}
	if ( $fromname == '' ) {
			return array(false, "e_mailSSendErrorNoMailsName");
	}

	$headers = "From: ".$fromname." <".$from.">"." \r\n" .
				"CC: ".$mail_cc;

	$rc = mail($mail_to,$mail_subj,$mail_body,$headers);

	if ($rc === false) return array(false, "m_mailSendError");
	return array(true, "m_mailSendSuccess");
}
?>
