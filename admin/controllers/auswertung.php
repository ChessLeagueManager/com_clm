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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerAuswertung extends JControllerLegacy
{
function __construct() {		
		parent::__construct();		
	}
	
function display($cachable = false, $urlparams = array()) { 
		JRequest::setVar('view','auswertung');
		parent::display(); 
	} 

/*
function datei() {
	$app	= JFactory::getApplication();
	$jinput = $app->input;
	$name	= $jinput->get('name', null, null);
	
	$addy_url = '&task=spieler_suchen&name='.$name;
	$adminLink = new AdminLink();
	$adminLink->view = "dewis";
	$adminLink->makeURL();
	
	//$app->enqueueMessage( 'Zurück Controller '.$name, 'warning');
	$app->redirect( $adminLink->url.$addy_url);
}
*/

}
?>