<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerCopyMeldeliste extends JControllerLegacy {
	

	// Konstruktor
	function __construct( $config = array() ) {

		parent::__construct( $config );
		
		$this->app =JFactory::getApplication();
					
		// Register Extra tasks
		$this->registerTask( 'apply', 'save' );
	
	}


	function save() {
	
		$id = clm_core::$load->request_int('id');
		$target	= JTable::getInstance('mannschaften', 'TableCLM');
		$target->load( (int)$id );

		$teamid = clm_core::$load->request_int('teamid');
		if ($teamid == 0) {
			$app	= JFactory::getApplication();
			$msg = 'Es wurde keine Meldeliste ausgewählt!';
			$app->enqueueMessage( $msg, 'warning' );
			$app->redirect( 'index.php?option=com_clm&view=copymeldeliste&id='.$id );
		}
		$source	= JTable::getInstance('mannschaften', 'TableCLM');
		$source->load( (int)$teamid );

		// Id's der Quell-Meldeliste ermitteln
		$query = "SELECT ml.id FROM `#__clm_meldeliste_spieler` as ml"
				." WHERE ml.lid = '".$source->liga."' AND ml.mnr = ".$source->man_nr
				." AND ml.zps = '".$source->zps."'"
				;
		$sourcelist = clm_core::$db->loadObjectList($query);	

		// neue Meldelisteneinträge schreiben
		$spieler	= JTable::getInstance( 'meldelisten', 'TableCLM' );
		$i = 0;
		foreach ($sourcelist as $s01) {
			$i++;
			$spieler->load( (int)$s01->id );
			$spieler->id = 0;
			$spieler->lid = $target->liga;
			$spieler->mnr = $target->man_nr;
			if (!$spieler->store()) {
				$this->app->enqueueMessage($spieler->getError(), 'error');
				$link = 'index.php?option=com_clm&section=mannschaften';
				$this->app->redirect($link);
			}
		}
		
		// Datum und Uhrzeit für Meldung
		$date =JFactory::getDate();
		$now = $date->toSQL();
		// Benutzer auslesen
		$user		= JFactory::getUser();
		$melder	= $user->get('id');
		// Eintrag der Mannschaft ergänzen durch Melder und Zeitpunkt
		$target->liste = $melder;
		$target->datum = $now;
		if (!$target->store()) {
				$this->app->enqueueMessage($spieler->getError(), 'error');
				$link = 'index.php?option=com_clm&section=mannschaften';
				$this->app->redirect($link);
			}
				
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = 'Meldeliste kopiert';
		$clmLog->params = array('to_lid' => $target->lid, 'zps' => $target->zps, 'from_lid' => $source->lid ); 
		$clmLog->write();

		$this->app->enqueueMessage( 'Meldeliste kopiert' );
		$link = 'index.php?option=com_clm&section=meldelisten&task=edit&id='.$id;
		$this->app->redirect($link);
	
	}




	function cancel() {
		
		$this->app->enqueueMessage( 'Akion abgebrochen' );
		$link = 'index.php?option=com_clm&section=mannschaften';
		$this->app->redirect( $link);
		
	}

}