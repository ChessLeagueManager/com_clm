<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access'); 

defined('_JEXEC') or die( 'Invalid Token' );

	$mainframe	= JFactory::getApplication();

// Variablen initialisieren - initializing variables
$turnier 		= $this->turnier;

$user =JFactory::getUser();
	$link = JURI::base() .'index.php?option=com_clm&view=turnier_registration&turnier='. $turnier->id .'&Itemid='; 

if (1==1)	{

// Datensätze in Tabelle schreiben - insert data into table

// Variablen holen - get variables
$turParams = new clm_class_params($this->turnier->params);
$typeRegistration = $turParams->get('typeRegistration', 0);
$reg_check01 	= clm_core::$load->request_string('reg_check01','');
$reg_name 		= clm_core::$load->request_string('reg_name','');
$reg_vorname 	= clm_core::$load->request_string('reg_vorname','');
$reg_birthYear 	= clm_core::$load->request_string('reg_jahr','');
$reg_club 		= clm_core::$load->request_string('reg_club','');
$reg_mail 		= clm_core::$load->request_string('reg_mail','');
$reg_dwz 		= clm_core::$load->request_string('reg_dwz','');
$reg_elo 		= clm_core::$load->request_string('reg_elo','');
$reg_comment 	= clm_core::$load->request_string('reg_comment','');

if ($typeRegistration == 5) {
	$reg_spieler 		= clm_core::$load->request_int('reg_spieler',100);
	if ($reg_spieler < 99) {
		$reg_name 		= clm_core::$load->request_string('reg_name'.$reg_spieler,'');
		$reg_vorname 	= clm_core::$load->request_string('reg_vorname'.$reg_spieler,'');
		$reg_club 		= clm_core::$load->request_string('reg_club'.$reg_spieler,'');
		$reg_dwz 		= clm_core::$load->request_string('reg_dwz'.$reg_spieler,'');
		$reg_elo 		= clm_core::$load->request_string('reg_elo'.$reg_spieler,'');
		$reg_PKZ 		= clm_core::$load->request_string('reg_PKZ'.$reg_spieler,'');
		$reg_titel 		= clm_core::$load->request_string('reg_titel'.$reg_spieler,'');
		$reg_geschlecht	= clm_core::$load->request_string('reg_geschlecht'.$reg_spieler,'');
		$reg_birthYear	= clm_core::$load->request_string('reg_birthYear'.$reg_spieler,'');
		$reg_mgl_nr		= clm_core::$load->request_string('reg_mgl_nr'.$reg_spieler,'');
		$reg_zps 		= clm_core::$load->request_string('reg_zps'.$reg_spieler,'');
		$reg_dwz_I0 	= clm_core::$load->request_string('reg_dwz_I0'.$reg_spieler,'');
		$reg_FIDEid 	= clm_core::$load->request_string('reg_FIDEid'.$reg_spieler,'');
		$reg_FIDEcco	= clm_core::$load->request_string('reg_FIDEcco'.$reg_spieler,'');
	}
} else {
		$reg_PKZ 		= '';
		$reg_titel 		= '';
		$reg_geschlecht	= '';
		$reg_mgl_nr		= '';
		$reg_zps 		= '';
		$reg_dwz_I0 	= '';
		$reg_FIDEid 	= '';
		$reg_FIDEcco	= '';
}
$session = JFactory::getSession();
$reg_wert = $session->get('reg_wert');

// Überprüfen der Eingaben - check input
$msg = '';
if ($reg_name == '') 
	$msg .= '<br>'.JText::_('REGISTRATION_E_NAME');
if ($reg_club == '') 
	$msg .= '<br>'.JText::_('REGISTRATION_E_CLUB');
if (!clm_core::$load->is_email($reg_mail)) 
	$msg .= '<br>'.JText::_('REGISTRATION_E_MAIL');
if ($reg_dwz != '' AND (!is_numeric($reg_dwz) OR $reg_dwz < 0 OR $reg_dwz > 3000))
	$msg .= '<br>'.JText::_('REGISTRATION_E_NWZ');
if ($reg_elo != '' AND (!is_numeric($reg_elo) OR $reg_elo < 0 OR $reg_elo > 3000))
	$msg .= '<br>'.JText::_('REGISTRATION_E_MAIL');
if ($reg_check01 == '') 
	$msg .= '<br>'.JText::_('REGISTRATION_E_SPAM');
elseif ($reg_check01 != $reg_wert) 
	$msg .= '<br>'.JText::_('REGISTRATION_E_SPAMK');

	// Konfigurationsparameter auslesen - get configuration parameter
	$config = clm_core::$db->config();
	$from = $config->email_from;
	$fromname = $config->email_fromname;
	$htmlMail = $config->email_type;
	if ( $from == '' ) {
		$msg .= '<br>'.JText::_('REGISTRATION_E_INSTALL_MAIL');
	}
	if ( $fromname == '' ) {
		$msg .= '<br>'.JText::_('REGISTRATION_E_INSTALL_NAME');
	}

if ($msg != '') {
	$link = JURI::base() .'index.php?option=com_clm&view=turnier_registration&turnier='. $turnier->id .'&Itemid='; 
	if ($typeRegistration == 5) {
		$link .= '&layout=selection&f_source=sent&reg_spieler='.$reg_spieler;
	}
	$link .= '&reg_name='.$reg_name.'&reg_vorname='.$reg_vorname.'&reg_club='.$reg_club.'&reg_mail='.$reg_mail.'&reg_jahr='.$reg_birthYear;
	$link .= '&reg_dwz='.$reg_dwz.'&reg_elo='.$reg_elo.'&reg_comment='.$reg_comment;
	$msg = substr($msg,4);
	$mainframe->enqueueMessage( $msg, "error" );
	$mainframe->redirect( $link );
}
// kein Fehler => Meldung in Tabelle schreiben - no error => trabsfer data into table
	$db	=JFactory::getDBO();
	$query	= "INSERT INTO #__clm_online_registration "
		." ( `tid`, `name`, `vorname`, `club`, `email`, `elo`, `dwz`,"
		." `PKZ`, `titel`, `geschlecht`, `birthYear`, `mgl_nr`, `zps`, `dwz_I0`, `FIDEid`, `FIDEcco`,"
		." `comment`, `status`, `timestamp` ) "
		." VALUES ('$turnier->id','".clm_core::$db->escape($reg_name)."','".clm_core::$db->escape($reg_vorname)."','".clm_core::$db->escape($reg_club)."','$reg_mail','$reg_elo','$reg_dwz', "
		." '".clm_core::$db->escape($reg_PKZ)."','".clm_core::$db->escape($reg_titel)."','".clm_core::$db->escape($reg_geschlecht)."','".clm_core::$db->escape($reg_birthYear)."','".clm_core::$db->escape($reg_mgl_nr)."', "
		." '".clm_core::$db->escape($reg_zps)."','".clm_core::$db->escape($reg_dwz_I0)."','".clm_core::$db->escape($reg_FIDEid)."','".clm_core::$db->escape($reg_FIDEcco)."', "
		." '".clm_core::$db->escape($reg_comment)."', '0',".time()." ) "
		;
	clm_core::$db->query($query);

// Log - log
	$aktion = "Online Registration";
	$callid = uniqid ( "", false );
	$userid = clm_core::$access->getId ();	
	$parray = array('turnier' => $turnier->id, 'name' => clm_core::$db->escape($reg_name), 'vorname' => clm_core::$db->escape($reg_vorname), 'mail' => clm_core::$db->escape($reg_mail), 'club' => clm_core::$db->escape($reg_club));
	$query	= "INSERT INTO #__clm_logging "
		." ( `callid`, `userid`, `timestamp` , `type` ,`name`, `content`) "
		." VALUES ('".$callid."','".$userid."',".time().",5,'".$aktion."','".json_encode($parray,JSON_UNESCAPED_UNICODE)."') "
		;
	clm_core::$db->query($query);

	$subject = JText::_('REGISTRATION_ONLINE').' - '.$turnier->name;
	$body_daten = JText::_('REGISTRATION_PLAYER').': '.$reg_name."\n";
	if ($reg_vorname != '') $body_daten .=  JText::_('REGISTRATION_VORNAME').': '.$reg_vorname."\n";
	$body_daten .=	JText::_('REGISTRATION_CLUB').': '.$reg_club."\n"
				. JText::_('REGISTRATION_MAIL').': '.$reg_mail."\n";
	if ($reg_dwz != '') $body_daten .=  JText::_('REGISTRATION_DWZ').': '.$reg_dwz."\n";
	if ($reg_elo != '') $body_daten .=  JText::_('REGISTRATION_ELO').': '.$reg_elo."\n";
	if ($reg_comment != '') $body_daten .= JText::_('REGISTRATION_COMMENT').': '.$reg_comment."\n";
	$body_TL = $turnier->name."\n".'Eine neue Online-Anmeldung liegt vor: '."\n".$body_daten;
	$email_TL = $turnier->tlemail;
	$htmlMail = '0';
	// Email an TL - e-mail to tournament controller
	if ($email_TL != "") {
		$body_TL = $turnier->name."\n\n".JText::_('REG_TC_HELLO_1')."\n".JText::_('REG_TC_HELLO_2')."\n".$body_daten;
		clm_core::$cms->sendMail($from, $fromname, $email_TL, $subject, $body_TL, $htmlMail);
	}
	// Info-E-mail an den Spieler - info e-mail to player
	if ($reg_mail != "") {
		$body_AM = $turnier->name."\n\n".JText::_('REG_PLAYER_HELLO_1')."\n".JText::_('REG_PLAYER_HELLO_2')."\n".JText::_('REG_PLAYER_HELLO_3')."\n".$body_daten;
		$body_AM .= "\n".JText::_('REG_PLAYER_HELLO_4');
		clm_core::$cms->sendMail($from, $fromname, $reg_mail, $subject, $body_AM, $htmlMail);
	}
	

	$msg = JText::_( 'REGISTRATION_SUCCESS' );
	$mainframe->enqueueMessage( $msg );
	$link = JURI::base() .'index.php?option=com_clm&view=turnier_info&turnier='. $turnier->id .'&Itemid='; 
	$mainframe->redirect( $link );
}
?>


