<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2019 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access'); 

JRequest::checkToken() or die( 'Invalid Token' );

	$mainframe	= JFactory::getApplication();

// Variablen initialisieren
$turnier 		= $this->turnier;

$user =JFactory::getUser();
	$link = JURI::base() .'index.php?option=com_clm&view=turnier_registration&turnier='. $turnier->id .'&Itemid='; 

if (1==1)	{

// Datensätze in Tabelle schreiben

// Variablen holen
$turParams = new clm_class_params($this->turnier->params);
$typeRegistration = $turParams->get('typeRegistration', 0);
$reg_check01 	= JRequest::getVar('reg_check01','');
$reg_name 		= JRequest::getVar('reg_name','');
$reg_vorname 	= JRequest::getVar('reg_vorname','');
$reg_jahr 		= JRequest::getVar('reg_jahr','');
$reg_club 		= JRequest::getVar('reg_club','');
$reg_mail 		= JRequest::getVar('reg_mail','');
$reg_dwz 		= JRequest::getVar('reg_dwz','');
$reg_elo 		= JRequest::getVar('reg_elo','');
$reg_comment 		= JRequest::getVar('reg_comment','');

if ($typeRegistration == 5) {
	$reg_spieler 		= JRequest::getVar('reg_spieler',100);
	if ($reg_spieler < 99) {
		$reg_name 		= JRequest::getVar('reg_name'.$reg_spieler,'');
		$reg_vorname 	= JRequest::getVar('reg_vorname'.$reg_spieler,'');
		$reg_club 		= JRequest::getVar('reg_club'.$reg_spieler,'');
		$reg_dwz 		= JRequest::getVar('reg_dwz'.$reg_spieler,'');
		$reg_elo 		= JRequest::getVar('reg_elo'.$reg_spieler,'');
	}
}
$session = JFactory::getSession();
$reg_wert = $session->get('reg_wert');

// Überprüfen der Eingaben
$msg = '';
if ($reg_name == '') 
	$msg .= '<br>'.'Name nicht eingegeben';
if ($reg_club == '') 
	$msg .= '<br>'.'Club nicht eingegeben';
if (!clm_core::$load->is_email($reg_mail)) 
	$msg .= '<br>'.'Mail-Adresse nicht korrekt';
if ($reg_dwz != '' AND (!is_numeric($reg_dwz) OR $reg_dwz < 0 OR $reg_dwz > 3000))
	$msg .= '<br>'.'DWZ nicht korrekt';
if ($reg_elo != '' AND (!is_numeric($reg_elo) OR $reg_elo < 0 OR $reg_elo > 3000))
	$msg .= '<br>'.'Elo nicht korrekt';
if ($reg_check01 == '') 
	$msg .= '<br>'.'Spam-Wert nicht eingegeben';
elseif ($reg_check01 != $reg_wert) 
	$msg .= '<br>'.'Spam-Wert nicht korrekt';

	// Konfigurationsparameter auslesen
	$config = clm_core::$db->config();
	$from = $config->email_from;
	$fromname = $config->email_fromname;
	$htmlMail = $config->email_type;
	if ( $from == '' ) {
		$msg .= '<br>'.'CLM-Installation hat keine Mail-Adresse -> kein Emailversand möglich ';
	}
	if ( $fromname == '' ) {
		$msg .= '<br>'.'CLM-Installation hat keinen Namen -> kein Emailversand möglich ';
	}

if ($msg != '') {
	$link = JURI::base() .'index.php?option=com_clm&view=turnier_registration&turnier='. $turnier->id .'&Itemid='; 
	if ($typeRegistration == 5) {
		$link .= '&layout=selection&f_source=sent&reg_spieler='.$reg_spieler;
	}
	$link .= '&reg_name='.$reg_name.'&reg_vorname='.$reg_vorname.'&reg_club='.$reg_club.'&reg_mail='.$reg_mail.'&reg_jahr='.$reg_jahr;
	$link .= '&reg_dwz='.$reg_dwz.'&reg_elo='.$reg_elo.'&reg_comment='.$reg_comment;
	$msg = substr($msg,4);
	$mainframe->redirect( $link, $msg, "error" );
}
// kein Fehler -> Meldung in Tabelle schreiben
	$db	=JFactory::getDBO();
	$query	= "INSERT INTO #__clm_online_registration "
		." ( `tid`, `name`, `vorname`, `club`, `email`, `elo`, `dwz`, `comment`, "
		." `status`, `timestamp` ) "
		." VALUES ('$turnier->id','".clm_core::$db->escape($reg_name)."','".clm_core::$db->escape($reg_vorname)."','".clm_core::$db->escape($reg_club)."','$reg_mail','$reg_elo','$reg_dwz', "
		." '".clm_core::$db->escape($reg_comment)."', '0',".time()." ) "
		;
	clm_core::$db->query($query);

// Log
	$aktion = "Online Registration";
	$callid = uniqid ( "", false );
	$userid = clm_core::$access->getId ();	
	$parray = array('turnier' => $turnier->id, 'name' => clm_core::$db->escape($reg_name), 'vorname' => clm_core::$db->escape($reg_vorname), 'mail' => clm_core::$db->escape($reg_mail), 'club' => clm_core::$db->escape($reg_club));
	$query	= "INSERT INTO #__clm_logging "
		." ( `callid`, `userid`, `timestamp` , `type` ,`name`, `content`) "
		." VALUES ('".$callid."','".$userid."',".time().",5,'".$aktion."','".json_encode($parray,JSON_UNESCAPED_UNICODE)."') "
		;
	clm_core::$db->query($query);

	$subject = 'Turnieranmeldung'.' - '.$turnier->name;
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
	// Email an TL	
	if ($email_TL != "") {
		$body_TL = $turnier->name."\n\n".'Hallo Turnierleiter;'."\n".'Eine neue Online-Anmeldung liegt vor: '."\n".$body_daten;
		clm_core::$cms->sendMail($from, $fromname, $email_TL, $subject, $body_TL, $htmlMail);
	}
	if ($reg_mail != "") {
		$body_AM = $turnier->name."\n\n".'Hallo Teilnehmer,'."\n".'Vielen Dank für Ihre Anmeldung.'."\n".'Mit diesen Daten haben Sie sich angemeldet: '."\n".$body_daten;
		$body_AM .= "\n".'Ihre Angaben werden durch den Turnierleiter innerhalb weniger Tage geprüft und dann auf die offizielle Teilnehmerliste übernommen.';
		clm_core::$cms->sendMail($from, $fromname, $reg_mail, $subject, $body_AM, $htmlMail);
	}
	

$msg = JText::_( 'REGISTRATION_SUCCESS' );
	$link = JURI::base() .'index.php?option=com_clm&view=turnier_info&turnier='. $turnier->id .'&Itemid='; 
$mainframe->redirect( $link, $msg );
}
?>


