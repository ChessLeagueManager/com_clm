<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access'); 

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$mainframe = Factory::getApplication();

// Variablen holen
$sid		= clm_core::$load->request_int('saison');
$zps 		= clm_core::$load->request_string('zps');
$mgl		= clm_core::$load->request_int('mglnr');

$clmuser 	= $this->clmuser;
$spieler	= $this->spieler;

$user 		=Factory::getUser();
$link = URI::base() . 'index.php?option=com_clm&view=mitglieder_details&saison='. $sid .'&zps='. $zps ; 

if ($clmuser[0]->zps <> $zps) {
	$msg = Text::_( 'Sie sind nicht berechtigt, Aenderungen vorzunehmen.' );
	$mainframe->enqueueMessage( $msg );
	$mainframe->redirect( $link );
				}
				
// Login Status prüfen
if ($user->get('id') > 0 AND  $clmuser[0]->published > 0 AND $clmuser[0]->zps == $zps) {

	// Prüfen ob Datensatz schon vorhanden ist
	$db	=Factory::getDBO();
	
	// Datensatz löschen
	$query	= "DELETE FROM #__clm_dwz_spieler"
		." WHERE ZPS = '$zps'"
		." AND Mgl_Nr = ".$mgl
		." AND sid =".$sid
		;
		
	$db->setQuery($query);
	clm_core::$db->query($query);

	// Log
	$date =Factory::getDate();
	$now = $date->toSQL();
	$user 		=Factory::getUser();
	$jid_aktion =  ($user->get('id'));
	$aktion = "Spielerdaten gelöscht FE";

$msg = Text::_( 'Spielerdaten gelöscht FE' );
$mainframe->enqueueMessage( $msg );
$linkback = URI::base() . 'index.php?option=com_clm&view=mitglieder&saison='. $sid .'&zps='. $zps; 
$mainframe->redirect( $linkback );

return;
}
?>