<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
/* Input-Parameter
 * $mail_to		Mailempfänger (string)
 * $mail_subj	Subjekt (string)
 * $mail_body   Nachricht (text oder html-text)
 * $htmlMail	Textformat (0=text, 1=html-text)
 * $mail_cc		Copy-Empfänger (null oder string)
 * $mail_bcc	Blindcopy-Empfänger (null oder string) 
*/
function clm_api_mail_send($mail_to, $mail_subj, $mail_body, $htmlMail=0, $mail_cc=null, $mail_bcc=null) {

	// Datum und Uhrzeit für Meldung
	$now = clm_core::$cms->getNowDate();
	
	// Konfigurationsparameter auslesen
	$config = clm_core::$db->config();
	$from = $config->email_from;
	$fromname = $config->email_fromname;
//	$htmlMail = $config->email_type;
	$suppress = $config->email_suppress;
	$replace = $config->email_replace;
	
	// keine Prüfungen notwendig, da keine Mailausgabe 
	if ($suppress == 1) {
			return array(true, "m_mailSendSuppressEmail");
	}
	if ($suppress == 2 AND !clm_core::$load->is_email($replace)) {
			return array(true, "m_mailSendSuppressNoReplace");
	}

	// Prüfungen der config-Parameter
	if ( $from == '' ) {
			return array(false, "e_mailSendErrorNoMailsEmail");
	}
	if ( $fromname == '' ) {
			return array(false, "e_mailSSendErrorNoMailsName");
	}

	// Zusammenstellung des Headers
	if ($htmlMail == 0) {
		$headers[] = 'MIME-Version: 1.0';
		$headers[] = 'Content-type: text/plain; charset=utf-8';
	}
	if ($htmlMail == 1) {
		$headers[] = 'MIME-Version: 1.0';
		$headers[] = 'Content-type: text/html; charset=utf-8';
	}

	$headers[] = "From: ".$fromname." <".$from.">";
	
	if ($suppress == 0) {
		$mail_body2 = "";
		if (!is_null($mail_cc) AND $mail_cc > ' ') {
			$headers[] = "Cc: ".$mail_cc;
		}
		if (!is_null($mail_bcc) AND $mail_bcc > ' ') {
			$headers[] = "Bcc: ".$mail_bcc;
		}
	} 
	if ($suppress == 2) {
		if ($htmlMail == 0) $nl = PHP_EOL;
		if ($htmlMail == 1) $nl = '<br>';
		$mail_body2 = $nl;
		$mail_body2 .= $nl."-------------------------------------";
		$mail_body2 .= $nl."Orig.Empfänger:".$mail_to;
		$mail_to	= $replace;
		if (!is_null($mail_cc) AND $mail_cc > ' ') {
			if ($htmlMail == 0) $mail_body2 .= $nl;
			$mail_body2 .= $nl."Orig.CC-Empfänger:".$mail_cc;
		}
		if (!is_null($mail_bcc) AND $mail_bcc > ' ') {
			if ($htmlMail == 0) $mail_body2 .= $nl;
			$mail_body2 .= $nl."Orig.BCC-Empfänger:".$mail_bcc;
		}
	}

	$rc = mail($mail_to,$mail_subj,$mail_body.$mail_body2,implode("\r\n", $headers));

	if ($rc === false) return array(false, "e_mailSendError".":".error_get_last()['message']);
	
	return array(true, "m_mailSendSuccess");
}
?>
