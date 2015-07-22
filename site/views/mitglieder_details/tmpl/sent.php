<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access'); 

JRequest::checkToken() or die( 'Invalid Token' );

$mainframe = JFactory::getApplication();

// Variablen holen
$sid		= JRequest::getVar('saison');
$zps 		= JRequest::getVar('zps');
$mgl		= JRequest::getInt('mglnr');

$clmuser 	= $this->clmuser;
$spieler	= $this->spieler;
$verein		= $this->verein;

$user 		=& JFactory::getUser();
$link = JURI::base() . 'index.php?option=com_clm&view=mitglieder_details&saison='. $sid .'&zps='. $zps .'&mglnr='. $mgl; 

if ($clmuser[0]->zps <> $zps) {
	$msg = JText::_( 'Sie sind nicht berechtigt, Aenderungen vorzunehmen.' );
	$mainframe->redirect( $link, $msg );
				}
				
// Login Status prüfen
if ($user->get('id') > 0 AND  $clmuser[0]->published > 0 AND $clmuser[0]->zps == $zps) {

	// Prüfen ob Datensatz schon vorhanden ist
	$db	=& JFactory::getDBO();

	// Variablen holen
	$sid		= JRequest::getVar('saison');
	$name 		= JRequest::getVar('name');
	$mglnr		= JRequest::getVar('mglnr');
	$dwz 		= JRequest::getVar('dwz');
	$dwz_index 	= JRequest::getVar('dwz_index');
	$geschlecht	= JRequest::getVar('geschlecht');
	$geburtsjahr= JRequest::getVar('geburtsjahr');
	$zps		= JRequest::getVar('zps');
	
	if ($new < 1) {	
		// Datensatz updaten
		$query	= "UPDATE #__clm_dwz_spieler "
			." SET Spielername = '$name' "
			." , Mgl_Nr = '$mglnr' "
			." , DWZ = '$dwz' "
			." , DWZ_Index = '$dwz_index' "
			." , Geschlecht = '$geschlecht' "
			." , Geburtsjahr = '$geburtsjahr' "
			." WHERE ZPS = '$zps' "
			." AND sid = '$sid'"
			." AND Mgl_Nr = '$mglnr'"
			;
			
		$db->setQuery($query);
		$db->query();
		
		}
	// Neuer Spieler
	else {
		$query	= "INSERT INTO #__clm_dwz_spieler"
			." ( `sid`,`ZPS`, `Mgl_Nr`, `Status`, `Spielername`, `Geschlecht`, `Geburtsjahr`, `DWZ`, `DWZ_Index`) "
			." VALUES ('$sid', '$zps','$mglnr','N','$name','$geschlecht', '$geburtsjahr','$dwz','$dwz_index')"
			;
		$db->setQuery($query);
		$db->query();
		}

	// Log
	$date =& JFactory::getDate();
	$now = $date->toMySQL();
	$user 		=& JFactory::getUser();
	$jid_aktion =  ($user->get('id'));
	$aktion = "Spielerdaten FE";

	$query	= "INSERT INTO #__clm_log "
		." ( `aktion`, `jid_aktion`, `sid` , `Mgl_Nr`, `zps`, `datum`) "
		." VALUES ('$aktion','$jid_aktion','$sid','$mglnr','$zps','$now') "
		;
	$db->setQuery($query);
	$db->query();

$msg = JText::_( 'Spielerdaten geändert' );
$linkback = JURI::base() . 'index.php?option=com_clm&view=mitglieder&saison='. $sid .'&zps='. $zps; 
$mainframe->redirect( $linkback, $msg );

}
?>