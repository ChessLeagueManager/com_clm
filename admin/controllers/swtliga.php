<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
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
		$_REQUEST['view'] = 'swtliga';
		parent::display(); 
	} 
	
	function update() {		
		$model = $this->getModel('swtliga');
		$lid = clm_core::$load->request_int('liga', 0);
		$sid = clm_core::$load->request_int('sid', 0);
		$swt_file = clm_core::$load->request_string('swt_file', '');
		if ($lid == 0) {
			$_REQUEST['view'] = 'swt';
			$_REQUEST['swt_file'] = $swt_file;
			$adminLink = new AdminLink ();
			$adminLink->view = 'swt';
			$adminlink->more = array('swt_file' => $swt_file, 'sid' => $sid);
			$adminLink->makeURL ();
			$msg = JText::_( 'SWT_LEAGUE_OVERWRITE_NOT_GIVEN' );
			$this->setRedirect($adminLink->url, $msg);
		} else {
			$_REQUEST['view'] = 'swtligainfo';
			$_POST['update'] = '1';
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
			$_POST['noOrgReference'] = $params['noOrgReference'];
			$_POST['noBoardResults'] = $params['noBoardResults'];
		
		parent::display(); 		
		}
	}
	
	function add() {		
		$_REQUEST['view'] = 'swtligainfo';
		
		parent::display(); 		
	
	}
	
}
?>
