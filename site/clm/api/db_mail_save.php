<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
function clm_api_db_mail_save($return_section, $return_view, $cids, $mail_to, $mail_subj, $mail_body) {

	// Bereitstellung der Detaildaten
	$out = clm_core::$api->db_mail($return_section, $return_view, $cids);
	if (!$out[0]) {
		return array(false, $out[1]);
	}
	$out = $out[2];
	
	/* Start: Übernahme der Detaildaten */
	$auser = $out["auser"];
	$mail_cc = clm_core::$load->sub_umlaute($auser[0]->name)." <".$auser[0]->email.">"; 

	$mail_to = '';
	// freie Mail an Benutzer
	if ($out["input"]["return_section"] == 'users') {
		$users = $out["users"];
		for ($x=0; $x < (count($users)); $x++) {
			if ($x > 0) $mail_to .= ', ';
			$mail_to .= clm_core::$load->sub_umlaute($users[$x]->name)." <".$users[$x]->email.">"; 
		}
	}
	// freie Mail an Mannschaftsleiter
	if ($out["input"]["return_section"] == 'mturniere' OR $out["input"]["return_section"] == 'ligen') {
		$teams = $out["teams"];
		for ($x=0; $x < (count($teams)); $x++) {
			if ($x > 0) $mail_to .= ', ';
			$mail_to .= clm_core::$load->sub_umlaute($teams[$x]->mfname)." <".$teams[$x]->mfmail.">"; 
		}
		$liga = $out["liga"];
		if (($liga[0]->sl > 0) AND ($liga[0]->slmail != $auser[0]->email)) {
			$mail_to .= ', '.clm_core::$load->sub_umlaute($liga[0]->slname)." <".$liga[0]->slmail.">"; 				
		}
	}
		
	// Datum und Uhrzeit für Meldung
	$now = clm_core::$cms->getNowDate();
	
	$rc = clm_core::$api->mail_send($mail_to, $mail_subj, $mail_body, 0, $mail_cc);

	if ($rc[0] === false) {
//		echo "<br>MailError ".$rc[1]; die();
		return array(false, "m_mailSendError: ".$rc[1]);
	}
	return array(true, "m_mailSendSuccess");
}
?>
