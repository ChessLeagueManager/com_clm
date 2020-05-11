<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access'); 

	$mainframe	= JFactory::getApplication();

// Variablen holen
$sid 		= clm_core::$load->request_int('saison');
$zps 		= clm_core::$load->request_string('zps');
$name 		= clm_core::$load->request_string('name');
$new 		= clm_core::$load->request_string('new');

// Variablen initialisieren
$liga 		= $this->liga;
$clmuser 	= $this->clmuser;
$row 		= $this->row;

$user =JFactory::getUser();
	$link = JURI::base() .'index.php?option=com_clm&view=verein&saison='. $sid .'&zps='. $zps;

// Login Status prüfen
if (!$user->get('id')) {
	$msg = JText::_( 'CLUB_DATA_SENT_LOGIN' );
	$mainframe->enqueueMessage( $msg );
	$mainframe->redirect( $link );
 			}
if ($clmuser[0]->published < 1) { 
	$msg = JText::_( 'CLUB_DATA_SENT_ACCOUNT' );
	$mainframe->enqueueMessage( $msg );
	$mainframe->redirect( $link );
			}
if ($clmuser[0]->zps <> $zps  OR $clmuser[0]->usertype == "spl") {
	$msg = JText::_( 'CLUB_DATA_SENT_FALSE' );
	$mainframe->enqueueMessage( $msg );
	$mainframe->redirect( $link );
 			}
// Login Status prüfen
if ($user->get('id') > 0 AND  $clmuser[0]->published > 0 AND $clmuser[0]->zps == $zps OR $clmuser[0]->usertype == "admin")
	{
// Prüfen ob Datensatz schon vorhanden ist
$db	=JFactory::getDBO();

// Datensätze in Meldelistentabelle schreiben

// Variablen holen
$lokal 		= clm_core::$load->request_string('lokal');
$homepage 	= clm_core::$load->request_string('homepage');
$adresse 	= clm_core::$load->request_string('adresse');
$termine 	= clm_core::$load->request_string('termine');
	$vs 		= clm_core::$load->request_string('vs');
	$vs_mail	= clm_core::$load->request_string('vs_mail');
	$vs_tel		= clm_core::$load->request_string('vs_tel');
$tl 		= clm_core::$load->request_string('tl');
$tl_mail	= clm_core::$load->request_string('tl_mail');
$tl_tel		= clm_core::$load->request_string('tl_tel');
	$jw 		= clm_core::$load->request_string('jw');
	$jw_mail	= clm_core::$load->request_string('jw_mail');
	$jw_tel		= clm_core::$load->request_string('jw_tel');
$pw 		= clm_core::$load->request_string('pw');
$pw_mail	= clm_core::$load->request_string('pw_mail');
$pw_tel		= clm_core::$load->request_string('pw_tel');
	$kw 		= clm_core::$load->request_string('kw');
	$kw_mail	= clm_core::$load->request_string('kw_mail');
	$kw_tel		= clm_core::$load->request_string('kw_tel');
$sw 		= clm_core::$load->request_string('sw');
$sw_mail	= clm_core::$load->request_string('sw_mail');
$sw_tel		= clm_core::$load->request_string('sw_tel');

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
	//$db->setQuery($query);
	clm_core::$db->query($query);
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
	//$db->setQuery($query);
	clm_core::$db->query($query);
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
	//$db->setQuery($query);
	clm_core::$db->query($query);

	$msg = JText::_( 'CLUB_DATA_SENT_SAVED' );
	$mainframe->enqueueMessage( $msg );
	$mainframe->redirect( $link );
}
?>


