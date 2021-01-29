<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerSWTLigasave extends JControllerLegacy
{
	function __construct() {
				
		parent::__construct();
		
	}
	
	function display($cachable = false, $urlparams = array()) {
	
		$_REQUEST['view'] = 'swtligasave';
		parent::display();
		
	}
	
	function save () {
		
		$model = $this->getModel('swtligasave');
		if ($model->finalCopy () && $model->rundenTermine () && $model->userAnlegen ()) {
			$_REQUEST['view'] = 'swt';
			$msg = JText::_( 'SWT_STORE_SUCCESS' );
		}
		else {
			$_REQUEST['view'] = 'swtligaerg';
			$msg = JText::_( 'SWT_STORE_ERROR' );
		}
		$sid = clm_core::$load->request_int('sid');
		$lid = clm_core::$load->request_int('lid');
		$swt = clm_core::$load->request_string('swt');
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = 'SWT-Import - '.$msg;
		$clmLog->params = array('sid' => $sid, 'lid' => $lid, 'swt' => $swt);
		$clmLog->write();
		JFactory::getApplication()->enqueueMessage( $msg,'message' );
		parent::display ();
	}
		
}
?>
