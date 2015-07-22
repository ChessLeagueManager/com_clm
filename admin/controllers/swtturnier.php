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

class CLMControllerSWTTurnier extends JControllerLegacy
{
	function __construct() {		
		parent::__construct();		
	}
	
	function display($cachable = false, $urlparams = array()) { 
		JRequest::setVar('view','swtturnier');
		parent::display(); 
	} 
	
	function update() {		
		$swt = JRequest::getVar('swt', '');
		
		JRequest::setVar('view', 'swtturnierinfo');
		JRequest::setVar('swt' , $swt);
		JRequest::setVar('update' , 1);
		
		parent::display(); 		
	
	}
	
	function add() {		
		$swt = JRequest::getVar('swt', '');
		
		JRequest::setVar('view', 'swtturnierinfo');
		JRequest::setVar('swt' , $swt);
		
		parent::display(); 		
	
	}
	
}
?>