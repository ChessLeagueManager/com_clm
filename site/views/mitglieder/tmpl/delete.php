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

$mainframe = JFactory::getApplication();

// Variablen holen
$sid		= JRequest::getVar('saison');
$zps 		= JRequest::getVar('zps');
$mgl		= JRequest::getInt('mglnr');

$clmuser 	= $this->clmuser;
$spieler	= $this->spieler;

$user 		=JFactory::getUser();
$link = JURI::base() . 'index.php?option=com_clm&view=mitglieder_details&saison='. $sid .'&zps='. $zps ; 

if ($clmuser[0]->zps <> $zps) {
	$msg = JText::_( 'Sie sind nicht berechtigt, Aenderungen vorzunehmen.' );
	$mainframe->redirect( $link, $msg );
				}
				
// Login Status prüfen
if ($user->get('id') > 0 AND  $clmuser[0]->published > 0 AND $clmuser[0]->zps == $zps) {

	// Prüfen ob Datensatz schon vorhanden ist
	$db	=JFactory::getDBO();
	
	// Datensatz löschen
	$query	= "DELETE FROM #__clm_dwz_spieler"
		." WHERE ZPS = '$zps'"
		." AND Mgl_Nr = ".$mgl
		." AND sid =".$sid
		;
		
	$db->setQuery($query);
	$db->query();

	// Log
	$date =JFactory::getDate();
	$now = $date->toSQL();
	$user 		=JFactory::getUser();
	$jid_aktion =  ($user->get('id'));
	$aktion = "Spielerdaten gelöscht FE";

$msg = JText::_( 'Spielerdaten gelöscht FE' );
$linkback = JURI::base() . 'index.php?option=com_clm&view=mitglieder&saison='. $sid .'&zps='. $zps; 
$mainframe->redirect( $linkback, $msg );

return;
}
?>