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

jimport( 'joomla.application.component.controller' );

class CLMControllerSWTLiga extends JController
{
	function __construct() {		
		parent::__construct();		
	}
	
	function display() { 
		JRequest::setVar('view','swtliga');
		parent::display(); 
	} 
	
	function update() {		
		$model = $this->getModel('swtliga');
		$lid = JRequest::getVar('liga', 0, 'default', 'int');
		$sid = JRequest::getVar('sid', 0, 'default', 'int');
		$swt_file = JRequest::getVar('swt_file', '', 'default', 'string');
		if ($lid == 0) {
			JRequest::setVar('view', 'swt');
			JRequest::setVar('swt_file', $swt_file);
			$adminLink = new AdminLink ();
			$adminLink->view = 'swt';
			$adminlink->more = array('swt_file' => $swt_file, 'sid' => $sid);
			$adminLink->makeURL ();
			$msg = JText::_( 'SWT_LEAGUE_OVERWRITE_NOT_GIVEN' );
			$this->setRedirect($adminLink->url, $msg);
		} else {
		JRequest::setVar('view', 'swtligainfo');
		JRequest::setVar('update' , 1);
		
		parent::display(); 		
		}
	}
	
	function add() {		
		JRequest::setVar('view', 'swtligainfo');
		
		parent::display(); 		
	
	}
	
}
?>
