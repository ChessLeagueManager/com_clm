<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
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
 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function clm_api_mail_send($mail_to, $mail_subj, $mail_body, $htmlMail=0, $mail_cc=null, $mail_bcc=null) {

	// Datum und Uhrzeit für Meldung
	$now = clm_core::$cms->getNowDate();
	
	// Konfigurationsparameter auslesen
	$config = clm_core::$db->config();
	$from = $config->email_from;
	$fromname = $config->email_fromname;
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

	// Behandlung der subject-line
	// Wandle den Text in Quoted-Printable um
    $quoted_printable = quoted_printable_encode($mail_subj);
    // Entferne Zeilenumbrüche, falls vorhanden
    $quoted_printable = str_replace("=\r\n", "", $quoted_printable);
    // Erstelle das kodierte Subject im MIME-Header-Format
    $mail_subj = '=?UTF-8?Q?' . $quoted_printable . '?=';

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
		$mail_body2 .= $nl."Orig.Empfänger: ".$mail_to;
		$mail_to	= $replace;
		if (!is_null($mail_cc) AND $mail_cc > ' ') {
			if ($htmlMail == 0) $mail_body2 .= $nl;
			$mail_body2 .= $nl."Orig.CC-Empfänger: ".$mail_cc;
		}
		if (!is_null($mail_bcc) AND $mail_bcc > ' ') {
			if ($htmlMail == 0) $mail_body2 .= $nl;
			$mail_body2 .= $nl."Orig.BCC-Empfänger: ".$mail_bcc;
		}
	}

//	$rc = mail($mail_to,$mail_subj,$mail_body.$mail_body2,implode("\r\n", $headers));

	# $mailer = JFactory::getMailer();
	# $mailer->setSender($fromname . " <" . $from . ">");
	# $mailer->addRecipient($mail_to);
	# $mailer->setSubject($mail_subj);	# $mailer->setCc($mail_cc);
	# $mailer->setBcc($mail_bcc);
	# $mailer->setBody($mail_body.$mail_body2);
	# $rc = $mailer->Send();

	$tried = "";
	try {
		# Schritt 1: php Mail verwenden
		$tried = "phpmail";
		$rc = mail($mail_to,$mail_subj,$mail_body.$mail_body2,implode("\r\n", $headers));
		if ($rc == false) {
			if ($config->mail_smtp_active == 1) {
				$tried .= ", SMTP";
				# Schritt 2: SMTP verwenden
				# $parts = explode("<",$mail_to);
				$mailer = new PHPMailer(true);
				$mailer->isSMTP();
				if ($htmlMail == 1) {
					$mailer->isHTML(true);
				}
				$mailer->SMTPAutoTLS=$config->mail_smtp_autotls;
				$mailer->Host=$config->mail_smtp_host;
				$mailer->Port=$config->mail_smtp_port;
				$mailer->Helo=$config->mail_smtp_helo;
				$mailer->setFrom($from, $fromname);
				$mail_tolist = explode(",", $mail_to);
				foreach ($mail_tolist as $mailone) {
					list($name, $address) = explode("<", $mailone);
					$name = trim($name);
					$address = trim(str_replace(">", "", $address));
					if (isset($address) && ($address != "")) {
						$mailer->addAddress($address, $name);
					} else {
						$mailer->addAddress($mailone);
					}
				}
				if ($mail_cc != null) {
					$mail_tolist = explode(",", $mail_cc);
					foreach ($mail_tolist as $mailone) {
						list($name, $address) = explode("<", $mailone);
						$name = trim($name);
						$address = trim(str_replace(">", "", $address));
						if (isset($address) && ($address != "")) {
							$mailer->addCc($address, $name);
						} else {
							$mailer->addCc($mailone);
						}
					}
				} else {
					$mail_cc = "null";
				}
				if ($mail_bcc != null) {
					$mail_tolist = explode(",", $mail_bcc);	
					foreach ($mail_tolist as $mailone) {
						list($name, $address) = explode("<", $mailone);
						$name = trim($name);
						$address = trim(str_replace(">", "", $address));
						if (isset($address) && ($address != "")) {
							$mailer->addBcc($address, $name);
						} else {
							$mailer->addBcc($mailone);
						}
					}
				} else {
					$mail_bcc = "null";
				}
				$mailer->Body=$mail_body.$mail_body2;
				$mailer->Subject=$mail_subj;
				$rc = $mailer->send();
			}
		}
	}

	catch (\Throwable $e) {
		return array(false, "<p>Exception gefangen. (to: " . htmlspecialchars($mail_to) . ") (cc: " . htmlspecialchars($mail_cc) . ") (bcc: " . htmlspecialchars($mail_bcc) . ")</p><p>" . htmlspecialchars($e) . "</p>");
	}

//	if ($rc === false) return array(false, "e_mailSendError".":".error_get_last()['message']);
	if ($rc === false) return array(false, "m_mailSendError - Mail konnte nicht versendet werden (versucht wurde: " . $tried . ")");
	
	return array(true, "m_mailSendSuccess");
}
?>
