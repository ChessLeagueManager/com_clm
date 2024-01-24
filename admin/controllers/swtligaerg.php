<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerSWTLigaerg extends JControllerLegacy
{
	function __construct() {
		$this->app =JFactory::getApplication();
		parent::__construct();
	}
	
	function display($cachable = false, $urlparams = array()) {
		$_REQUEST['view'] = 'swtligaerg';
		parent::display();
	} 
	
	function next () {
	
		$model = $this->getModel('swtligaerg');
		if ($model->store ()) {
			$_REQUEST['view'] = 'swtligaerg';
			$runde = clm_core::$load->request_int('runde', 0);
			$dgang = clm_core::$load->request_int('dgang', 0);
		// schon gespeicherte SWT-Daten aus der DB holen
			$db		=JFactory::getDBO ();
			$swt_id = clm_core::$load->request_int( 'swt_id', 0);
				//echo "swt_id: $swt_id"; //DBG
			$sql = ' SELECT id, teil as anz_mannschaften, stamm as anz_bretter, ersatz as anz_ersatzspieler, durchgang as anz_durchgaenge, runden as anz_runden, sieg, remis, nieder, antritt, man_sieg, man_remis, man_nieder, man_antritt, sieg_bed'
				. ' FROM #__clm_swt_liga'
				. ' WHERE id = '.$swt_id;
			$db->setQuery ($sql);
			$objs = $db->loadObjectList ();
				//var_dump($objs);
			$obj = $objs[0];
			$anz_runden			= $obj->anz_runden;
			$anz_durchgaenge	= $obj->anz_durchgaenge;
				//echo "runde, anz_runden: $runde, $anz_runden"; //DBG
				//echo "dgang, anz_durchgaenge: $dgang, $anz_durchgaenge"; //DBG
			if (($runde +1) == $anz_runden) {
				$_GET['runde'] = 0;
				$_GET['dgang'] = $dgang + 1; }
			else $_GET['runde'] = $runde + 1;
//			$this->_message = JText::_( 'SWT_STORE_SUCCESS' );
			parent::display ();
		}
		else {
			$_REQUEST['view'] = 'swtligaerg';
			$this->_message = JText::_( 'SWT_STORE_ERROR' );
			parent::display ();
		}
	}

	function finish () {
	
		$model = $this->getModel('swtligaerg');
		if ($model->store ()) {
//			$_REQUEST['view'] = 'swtligasave';
//			$this->_message = JText::_( 'SWT_STORE_SUCCESS' );
			//$app->enqueueMessage( JText::_('SWT_STORE_SUCCESS') );
			$swt_file = clm_core::$load->request_string('swt_file', '');
			$sid = clm_core::$load->request_int('sid', 0);
			$lid = clm_core::$load->request_int('lid', 0);
			$swt_id = clm_core::$load->request_int('swt_id', 0);
			$mturnier = clm_core::$load->request_string('mturnier', '0');
			$update = clm_core::$load->request_string('update', '0');
			$ungerade = clm_core::$load->request_string('ungerade', '0');
			$noOrgReference = clm_core::$load->request_string('noOrgReference', '0');		
			$noBoardResults = clm_core::$load->request_string('noBoardResults', '0');		
			$dwz_handling   = clm_core::$load->request_string( 'dwz_handling', '0');
			$strparams = clm_core::$load->request_string('strparams', '');
			$adminLink = new AdminLink();
			$adminLink->more = array('swt_file' => $swt_file, 'update' => $update, 'sid' => $sid, 'swt_id' => $swt_id, 'lid' => $lid,'mturnier' => $mturnier, 'ungerade' => $ungerade,
				'noOrgReference' => $noOrgReference, 'noBoardResults' => $noBoardResults, 'dwz_handling' => $dwz_handling, 'strparams' => $strparams);
			$adminLink->view = "swtligasave";
			$adminLink->makeURL();
			$this->app->redirect($adminLink->url); 		
//			parent::display ();
		}
		else {
			$_REQUEST['view'] = 'swtligaerg';
			$this->_message = JText::_( 'SWT_STORE_ERROR' );
			parent::display ();
		}
	}
	
	function cancel() {		
	
		$adminLink = new AdminLink ();
		$adminLink->view = 'swt';
		$adminLink->makeURL ();
		
		$msg = JText::_( 'SWT_CANCEL_MSG' );
//		$this->setRedirect($adminLink->url, $msg);
		$this->app->enqueueMessage( $msg );
		$this->app->redirect($adminLink->url); 		
	
	}
	
}
?>
