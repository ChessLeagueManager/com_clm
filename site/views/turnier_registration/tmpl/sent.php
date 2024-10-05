<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access'); 

	$mainframe	= JFactory::getApplication();

// Variablen initialisieren - initializing variables
$turnier 		= $this->turnier;

$user =JFactory::getUser();
	$link = JURI::base() .'index.php?option=com_clm&view=turnier_registration&turnier='. $turnier->id .'&Itemid='; 

// Datensätze in Tabelle schreiben - insert data into table

// Variablen holen - get variables
$turParams = new clm_class_params($this->turnier->params);
$typeRegistration = $turParams->get('typeRegistration', 0);
$optionEloAnalysis	= clm_core::$load->request_string('optionEloAnalysis', 0);
$typeAccount	= $turParams->get('typeAccount', 0);
$reg_dsgvo 		= clm_core::$load->request_int('reg_dsgvo',0);
$reg_check01 	= clm_core::$load->request_string('reg_check01','');
$reg_name 		= clm_core::$load->request_string('reg_name','');
$reg_vorname 	= clm_core::$load->request_string('reg_vorname','');
$reg_birthYear 	= clm_core::$load->request_string('reg_jahr','');
$reg_geschlecht = clm_core::$load->request_string('reg_geschlecht','');
$reg_club 		= clm_core::$load->request_string('reg_club','');
$reg_mail 		= clm_core::$load->request_string('reg_mail','');
$reg_tel_no 	= clm_core::$load->request_string('reg_tel_no','');
$reg_account 	= clm_core::$load->request_string('reg_account','');
$reg_dwz 		= clm_core::$load->request_string('reg_dwz','');
$reg_elo 		= clm_core::$load->request_string('reg_elo','');
$reg_FIDEid 	= clm_core::$load->request_string('reg_FIDEid','');
$reg_comment 	= clm_core::$load->request_string('reg_comment','');
$reg_PKZ 		= '';
$reg_zps 		= clm_core::$load->request_string('reg_zps','');
$reg_titel 		= '';
$reg_mgl_nr		= clm_core::$load->request_string('reg_mgl_nr','');
$reg_dwz_I0 	= 0;
$reg_FIDEcco	= '';

if ($typeRegistration == 5) {
	$reg_spieler 		= clm_core::$load->request_int('reg_spieler',100);
	if ($reg_spieler < 99) {
		$reg_name 		= clm_core::$load->request_string('reg_name'.$reg_spieler,'');
		$reg_vorname 	= clm_core::$load->request_string('reg_vorname'.$reg_spieler,'');
		$reg_club 		= clm_core::$load->request_string('reg_club'.$reg_spieler,'');
		$reg_dwz 		= clm_core::$load->request_string('reg_dwz'.$reg_spieler,'');
		$reg_elo 		= clm_core::$load->request_string('reg_elo'.$reg_spieler,'');
		$reg_PKZ 		= clm_core::$load->request_string('reg_PKZ'.$reg_spieler,'');
		$reg_zps 		= clm_core::$load->request_string('reg_zps'.$reg_spieler,'');
		$reg_titel 		= clm_core::$load->request_string('reg_titel'.$reg_spieler,'');
		$reg_geschlecht	= clm_core::$load->request_string('reg_geschlecht'.$reg_spieler,'');
		$reg_birthYear	= clm_core::$load->request_string('reg_birthYear'.$reg_spieler,'');
		$reg_mgl_nr		= clm_core::$load->request_string('reg_mgl_nr'.$reg_spieler,'');
		$reg_dwz_I0 	= clm_core::$load->request_string('reg_dwz_I0'.$reg_spieler,'');
		$reg_FIDEid 	= clm_core::$load->request_string('reg_FIDEid'.$reg_spieler,'');
		$reg_FIDEcco	= clm_core::$load->request_string('reg_FIDEcco'.$reg_spieler,'');
	}
} 

$session = JFactory::getSession();
$reg_wert = $session->get('reg_wert');
$c_year = date("Y"); 
// Überprüfen der Eingaben - check input
$msg = '';
if ($reg_name == '') 
	$msg .= '<br>'.JText::_('REGISTRATION_E_NAME');
if ($reg_club == '') 
	$msg .= '<br>'.JText::_('REGISTRATION_E_CLUB');
if ($reg_birthYear == '') 
	$msg .= '<br>'.JText::_('REGISTRATION_E_YEAR');
if ($reg_birthYear != '' AND (!is_numeric($reg_birthYear) OR $reg_birthYear < ($c_year - 110) OR $reg_birthYear > ($c_year -2)))
	$msg .= '<br>'.JText::_('REGISTRATION_E_YEARK');
if (!clm_core::$load->is_email($reg_mail)) 
	$msg .= '<br>'.JText::_('REGISTRATION_E_MAIL');
if ($typeAccount > '0') {
	if ($reg_account == '') $msg .= '<br>'.JText::_('REGISTRATION_E_ACCOUNT_NO');
	elseif ($typeAccount == '1') {
		if (substr($reg_account,0,22) == 'https://lichess.org/@/') {
			$reg_account1 = $reg_account;
			$s_account = 0;
		} else {
			$reg_account1 = 'https://lichess.org/@/'.$reg_account;
			$s_account = 1;
		}
		if (@file_get_contents($reg_account1,false,NULL,0,1) === false) $msg .= '<br>'.JText::_('REGISTRATION_E_ACCOUNT_NK');
		if ($s_account == 1) $reg_account = 'https://lichess.org/@/'.$reg_account;
	}}
if ($reg_dwz != '' AND (!is_numeric($reg_dwz) OR $reg_dwz < 0 OR $reg_dwz > 3000))
	$msg .= '<br>'.JText::_('REGISTRATION_E_NWZ');
if ($reg_elo != '' AND (!is_numeric($reg_elo) OR $reg_elo < 0 OR $reg_elo > 3000))
	$msg .= '<br>'.JText::_('REGISTRATION_E_ELO');
if ($optionEloAnalysis == 1) {
	if ($reg_FIDEid != '' AND (!is_numeric($reg_FIDEid) OR $reg_FIDEid < 10000))
		$msg .= '<br>'.JText::_('REGISTRATION_E_FIDEID');
}
if ($reg_check01 == '') 
	$msg .= '<br>'.JText::_('REGISTRATION_E_SPAM');
elseif ($reg_check01 != $reg_wert) 
	$msg .= '<br>'.JText::_('REGISTRATION_E_SPAMK');

	// Konfigurationsparameter auslesen - get configuration parameter
	$config = clm_core::$db->config();
	$from = $config->email_from;
	$fromname = $config->email_fromname;
	$htmlMail = $config->email_type;
	$privacy_notice = $config->privacy_notice;
	if ( $from == '' ) {
		$msg .= '<br>'.JText::_('REGISTRATION_E_INSTALL_MAIL');
	}
	if ( $fromname == '' ) {
		$msg .= '<br>'.JText::_('REGISTRATION_E_INSTALL_NAME');
	}
	if ($reg_dsgvo == '0' AND $privacy_notice != '') 
		$msg .= '<br>'.JText::_('REGISTRATION_E_CHECKBOX');

if ($msg != '') {
	$link = JURI::base() .'index.php?option=com_clm&view=turnier_registration&turnier='. $turnier->id .'&Itemid='; 
	if ($typeRegistration == 5) {
		$link .= '&layout=selection&f_source=sent&reg_spieler='.$reg_spieler;
	}
	$link .= '&reg_name='.$reg_name.'&reg_vorname='.$reg_vorname.'&reg_club='.$reg_club.'&reg_mail='.$reg_mail.'&reg_jahr='.$reg_birthYear.'&reg_geschlecht='.$reg_geschlecht;
	$link .= '&reg_dwz='.$reg_dwz.'&reg_elo='.$reg_elo.'&reg_FIDEid='.$reg_FIDEid.'&reg_tel_no='.$reg_tel_no.'&reg_account='.$reg_account.'&reg_comment='.$reg_comment.'&reg_dsgvo='.$reg_dsgvo;
	$link .= '&optionEloAnalysis='.$optionEloAnalysis;
	$msg = substr($msg,4);
	$mainframe->enqueueMessage( $msg, "error" );
	$mainframe->redirect( $link );
}
// kein Fehler => Meldung in Tabelle schreiben - no error => transfer data into table
	$randomUid = md5(uniqid('', true) . '|' . microtime());
	if (!is_numeric($reg_elo)) $reg_elo = 0; else $reg_elo = (int) $reg_elo;
	if (!is_numeric($reg_dwz)) $reg_dwz = 0; else $reg_dwz = (int) $reg_dwz;
	if (!is_numeric($reg_mgl_nr)) $reg_mgl_nr = 0; else $reg_mgl_nr = (int) $reg_mgl_nr;

	$sql = "INSERT INTO #__clm_online_registration "
		." ( `tid`, `name`, `vorname`, `club`, `email`, `elo`, `dwz`,"
		." `PKZ`, `titel`, `geschlecht`, `birthYear`, `mgl_nr`, `zps`, `dwz_I0`, `FIDEid`, `FIDEcco`,"
		." `tel_no`,`account`,`comment`, `status`, `timestamp`, "
		." `pid`, `approved` ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
	$ltime = time();
	$zero0 = 0;
	$zero1 = 0;
	$stmt = clm_core::$db->prepare($sql);
	$stmt->bind_param('issssiissssisiissssiisi', $turnier->id, $reg_name, $reg_vorname, $reg_club, $reg_mail, $reg_elo, $reg_dwz,
		$reg_PKZ, $reg_titel, $reg_geschlecht, $reg_birthYear, $reg_mgl_nr,
		$reg_zps, $reg_dwz_I0, $reg_FIDEid, $reg_FIDEcco,
		$reg_tel_no, $reg_account, $reg_comment, $zero0, $ltime,
		$randomUid, $zero1);

	$result = $stmt->execute();
	if ($result === false) { 
		$link = JURI::base() .'index.php?option=com_clm&view=turnier_registration&turnier='. $turnier->id .'&Itemid='; 
		if ($typeRegistration == 5) {
			$link .= '&layout=selection&f_source=sent&reg_spieler='.$reg_spieler;
		}
		$link .= '&reg_name='.$reg_name.'&reg_vorname='.$reg_vorname.'&reg_club='.$reg_club.'&reg_mail='.$reg_mail.'&reg_jahr='.$reg_birthYear.'&reg_geschlecht='.$reg_geschlecht;
		$link .= '&reg_dwz='.$reg_dwz.'&reg_elo='.$reg_elo.'&reg_FIDEid='.$reg_FIDEid.'&reg_tel_no='.$reg_tel_no.'&reg_account='.$reg_account.'&reg_comment='.$reg_comment.'&reg_dsgvo='.$reg_dsgvo;
		$msg = 'Speicherfehler clm_online_registration';
		$mainframe->enqueueMessage( $msg, "error" );
		$mainframe->redirect( $link );
	}

// Log - log
	$aktion = "Online Registration";
	$parray = array('turnier' => $turnier->id, 'name' => $reg_name, 'vorname' => $reg_vorname, 'mail' => clm_core::$db->escape($reg_mail), 'club' => $reg_club);
	clm_core::addDeprecated($aktion, json_encode($parray));

// Email Erstellung
	$subject = JText::_('REGISTRATION_ONLINE').' - '.$turnier->name;
	$body_daten = JText::_('REGISTRATION_PLAYER').': '.$reg_name."\n";
	if ($reg_vorname != '') $body_daten .=  JText::_('REGISTRATION_VORNAME').': '.$reg_vorname."\n";
	$body_daten .=	JText::_('REGISTRATION_JAHR').': '.$reg_birthYear."\n"
				. JText::_('REGISTRATION_CLUB').': '.$reg_club."\n"
				. JText::_('REGISTRATION_MAIL').': '.$reg_mail."\n";
	if ($reg_dwz != '') $body_daten .=  JText::_('REGISTRATION_DWZ').': '.$reg_dwz."\n";
	if ($reg_elo != '') $body_daten .=  JText::_('REGISTRATION_ELO').': '.$reg_elo."\n";
	if ($reg_FIDEid != '') $body_daten .=  JText::_('REGISTRATION_FIDEID').': '.$reg_FIDEid."\n";
	if ($reg_tel_no != '') $body_daten .=  JText::_('REGISTRATION_TEL_NO').': '.$reg_tel_no."\n";
	if ($reg_account != '') $body_daten .=  JText::_('REGISTRATION_ACCOUNT_'.$typeAccount).': '.$reg_account." \n\n";
	if ($reg_comment != '') $body_daten .=  JText::_('REGISTRATION_COMMENT').': '."\n".$reg_comment."\n";
	//$body_TL = $turnier->name."\n".'Eine neue Online-Anmeldung liegt vor: '."\n".$body_daten;
	$email_TL = $turnier->tlemail;
	$htmlMail = 0;
//	$url = JURI::base().'components/com_clm/clm/mail_approve.php?parameter='.$randomUid;
	$url = JURI::base().'index.php?option=com_clm&view=mailapprove&parameter='.$randomUid;
	$msg = '';
	// Email an TL - e-mail to tournament controller
	if ($email_TL != "") {
		$body_TL = $turnier->name."\n\n".JText::_('REG_TC_HELLO_1')."\n".JText::_('REG_TC_HELLO_2')."\n".$body_daten;
//		$result = clm_core::$cms->sendMail($from, $fromname, $email_TL, $subject, $body_TL, $htmlMail);
		$result = clm_core::$api->mail_send($email_TL, $subject, $body_TL, $htmlMail);
		if ($result[0] !== true) 
			$msg .= '<br>'.JText::_('REG_MAIL_NOT_TO_TL').'<br>'.$result[1];
	}
	// Info-E-mail an den Spieler - info e-mail to player
	if ($reg_mail != "") {
		$body_AM = $turnier->name."\n\n".JText::_('REG_PLAYER_HELLO_1')."\n".JText::_('REG_PLAYER_HELLO_2')."\n".JText::_('REG_PLAYER_HELLO_3')."\n".$body_daten;
		$body_AM .= "\n".JText::_('REG_PLAYER_HELLO_4');
		$body_AM .= "\n".$url;
		$body_AM .= "\n".JText::_('REG_PLAYER_HELLO_5');
//		$result = clm_core::$cms->sendMail($from, $fromname, $reg_mail, $subject, $body_AM, $htmlMail);
		$result = clm_core::$api->mail_send($reg_mail, $subject, $body_AM, $htmlMail);
		if ($result[0] !== true) 
			$msg .= '<br>'.JText::_('REG_MAIL_NOT_TO_PL').'<br>'.$result[1];
	}
	
	if ($msg == '') {
		$msg = JText::_( 'REGISTRATION_SUCCESS' );
		$mainframe->enqueueMessage( $msg );
	} else {
		$msg = substr($msg,4);
		$mainframe->enqueueMessage( $msg, 'error' );
		$msg = JText::_( 'REGISTRATION_SUCCESS' );
		$mainframe->enqueueMessage( $msg );
	}
	$link = JURI::base() .'index.php?option=com_clm&view=turnier_info&turnier='. $turnier->id .'&Itemid='; 
	$mainframe->redirect( $link );

?>


