<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerSWTLiga extends JControllerLegacy
{
	function __construct() {		
		parent::__construct();		
	}
	
	function display($cachable = false, $urlparams = array()) { 
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
			$db		=JFactory::getDBO ();
			$select_query = '  SELECT * FROM #__clm_liga '
							.' WHERE id = '.$lid.'; ';
			$db->setQuery ($select_query);
			$liga = $db->loadObject();
			//Liga-Parameter aufbereiten
			$paramsStringArray = explode("\n", $liga->params);
			$params = array();
			foreach ($paramsStringArray as $value) {
				$ipos = strpos ($value, '=');
				if ($ipos !==false) {
					$key = substr($value,0,$ipos);
					$params[$key] = substr($value,$ipos+1);
				}
			}	
			if (!isset($params['noOrgReference']))  {   //Standardbelegung
				$params['noOrgReference'] = '0'; }
			if (!isset($params['noBoardResults']))  {   //Standardbelegung
				$params['noBoardResults'] = '0'; }
			JRequest::setVar('noOrgReference', $params['noOrgReference']);
			JRequest::setVar('noBoardResults', $params['noBoardResults']);
		
		parent::display(); 		
		}
	}
	
	function add() {		
		JRequest::setVar('view', 'swtligainfo');
		
		parent::display(); 		
	
	}
	
}
?>
