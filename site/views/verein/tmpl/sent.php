<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access'); 

JRequest::checkToken() or die( 'Invalid Token' );

	$mainframe	= JFactory::getApplication();

// Variablen holen
$sid 		= JRequest::getVar('saison');
$zps 		= JRequest::getVar('zps');
$name 		= JRequest::getVar('name');
$new 		= JRequest::getVar('new');

// Variablen initialisieren
$liga 		= $this->liga;
$clmuser 	= $this->clmuser;
$row 		= $this->row;

$user =JFactory::getUser();
	$link = JURI::base() .'index.php?option=com_clm&view=verein&saison='. $sid .'&zps='. $zps;

// Login Status prüfen
if (!$user->get('id')) {
	$msg = JText::_( 'CLUB_DATA_SENT_LOGIN' );
	$mainframe->redirect( $link, $msg );
 			}
if ($clmuser[0]->published < 1) { 
	$msg = JText::_( 'CLUB_DATA_SENT_ACCOUNT' );
	$mainframe->redirect( $link, $msg );
			}
if ($clmuser[0]->zps <> $zps  OR $clmuser[0]->usertype == "spl") {
		$msg = JText::_( 'CLUB_DATA_SENT_FALSE' );
		$mainframe->redirect( $link, $msg );
 			}
// Login Status prüfen
if ($user->get('id') > 0 AND  $clmuser[0]->published > 0 AND $clmuser[0]->zps == $zps OR $clmuser[0]->usertype == "admin")
	{
// Prüfen ob Datensatz schon vorhanden ist
$db	=JFactory::getDBO();

// Datensätze in Meldelistentabelle schreiben

// Variablen holen
$lokal 		= JRequest::getVar('lokal');
$homepage 	= JRequest::getVar('homepage');
$adresse 	= JRequest::getVar('adresse');
$termine 	= JRequest::getVar('termine');
	$vs 		= JRequest::getVar('vs');
	$vs_mail	= JRequest::getVar('vs_mail');
	$vs_tel		= JRequest::getVar('vs_tel');
$tl 		= JRequest::getVar('tl');
$tl_mail	= JRequest::getVar('tl_mail');
$tl_tel		= JRequest::getVar('tl_tel');
	$jw 		= JRequest::getVar('jw');
	$jw_mail	= JRequest::getVar('jw_mail');
	$jw_tel		= JRequest::getVar('jw_tel');
$pw 		= JRequest::getVar('pw');
$pw_mail	= JRequest::getVar('pw_mail');
$pw_tel		= JRequest::getVar('pw_tel');
	$kw 		= JRequest::getVar('kw');
	$kw_mail	= JRequest::getVar('kw_mail');
	$kw_tel		= JRequest::getVar('kw_tel');
$sw 		= JRequest::getVar('sw');
$sw_mail	= JRequest::getVar('sw_mail');
$sw_tel		= JRequest::getVar('sw_tel');

// Vereinsdaten exisitieren
if ($new < 1) {
	$query	= "UPDATE #__clm_vereine"
		." SET lokal = '$lokal' "
		." , homepage = '$homepage' "
		." , adresse = '$adresse' "
		." , termine = '$termine' "
		." , vs = '$vs' "
		." , vs_mail = '$vs_mail' "
		." , vs_tel = '$vs_tel' "
		." , tl = '$tl' "
		." , tl_mail = '$tl_mail' "
		." , tl_tel = '$tl_tel' "
		." , jw = '$jw' "
		." , jw_mail = '$jw_mail' "
		." , jw_tel = '$jw_tel' "
		." , pw = '$pw' "
		." , pw_mail = '$pw_mail' "
		." , pw_tel = '$pw_tel' "
		." , kw = '$kw' "
		." , kw_mail = '$kw_mail' "
		." , kw_tel = '$kw_tel' "
		." , sw = '$sw' "
		." , sw_mail = '$sw_mail' "
		." , sw_tel = '$sw_tel' "
		." WHERE zps = '$zps' "
		;
	$db->setQuery($query);
	$db->query();
		}
// Vereinsdaten exisitieren NICHT
else {
	$query	= "INSERT INTO #__clm_vereine "
		." ( `name`, `sid`, `zps`, `vl`, `lokal`, `homepage`, `adresse`, "
		." `vs`, `vs_mail`, `vs_tel`, `tl`, `tl_mail`, `tl_tel`, "
		." `jw`, `jw_mail`, `jw_tel`, `pw`, `pw_mail`, `pw_tel`, "
		." `kw`, `kw_mail`, `kw_tel`, `sw`, `sw_mail`, `sw_tel`, `termine`,`published` ) "
		." VALUES ('$name','$sid','$zps','0','$lokal','$homepage','$adresse', "
		." '$vs','$vs_mail','$vs_tel','$tl','$tl_mail','$tl_tel', "
		." '$jw','$jw_mail','$jw_tel','$pw','$pw_mail','$pw_tel', "
		." '$kw','$kw_mail','$kw_tel','$sw','$sw_mail','$sw_tel', '$termine', '1') "
		;
	$db->setQuery($query);
	$db->query();
	}
// Log
	$aktion = "Vereinsdaten FE";
	$callid = uniqid ( "", false );
	$userid = clm_core::$access->getId ();	
	$parray = array('sid' => $sid, 'zps' => $zps);
	$query	= "INSERT INTO #__clm_logging "
		." ( `callid`, `userid`, `timestamp` , `type` ,`name`, `content`) "
		." VALUES ('".$callid."','".$userid."',".time().",5,'".$aktion."','".json_encode($parray)."') "
		;
	$db->setQuery($query);
	$db->query();

$msg = JText::_( 'CLUB_DATA_SENT_SAVED' );
$mainframe->redirect( $link, $msg );
}
?>


