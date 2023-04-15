<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerTurRegistrationEdit extends JControllerLegacy {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		// turnierid
		$this->registrationid = clm_core::$load->request_int('registrationid');
		$this->turnierid = clm_core::$load->request_int('turnierid');
		$this->snrmax = clm_core::$load->request_int('snrmax');
		
		$this->_db	= JFactory::getDBO();		
		$this->app 	= JFactory::getApplication();
		
		// Register Extra tasks
		$this->registerTask( 'apply', 'save' );
		$this->registerTask( 'copy_to', 'save' );
	
		$this->adminLink = new AdminLink();
		$this->adminLink->more = array('registrationid' => $this->registrationid);
		$this->adminLink->view = "turregistrationedit";
	
	}

	
	
	function save() {
	
		$this->_saveDo();

		$this->adminLink->makeURL();
		$this->app->redirect( $this->adminLink->url );
	
	}
	
	
	function _saveDo() {
	
		defined('_JEXEC') or die( 'Invalid Token' );
	
		// Instanz der Tabelle
		$rowt = JTable::getInstance( 'turniere', 'TableCLM' );
		$rowt->load( $this->turnierid ); // Daten zu dieser Turnier-ID laden

		$clmAccess = clm_core::$access;      
		if (($rowt->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== true) OR $clmAccess->access('BE_tournament_edit_detail') === false) {
			$this->app->enqueueMessage( JText::_('TOURNAMENT_NO_ACCESS'), 'warning' );
			return false;
		}
	
		// Task
		$task = clm_core::$load->request_string('task');
		
		// Instanz der Tabelle
		$row = JTable::getInstance( 'registrations', 'TableCLM' );
		$row->load( $this->registrationid ); // Daten zu dieser ID laden

		if ($task == 'copy_to') {
			// Turnierdaten
			$tournament = new CLMTournament($rowt->id, true);
			$playersIn = $tournament->getPlayersIn();
			$text = '';
			if ($playersIn >= $rowt->teil) {
				$text = CLMText::errorText('PLAYERLIST', 'FULL');
			}
			if ($row->status == 2) {
				$text = JText::_('REGISTRATION_ALREADY_MOVED');
			}
			if ($text != '') {
				$this->app->enqueueMessage( $text );
				// Weiterleitung zurück in Liste
				$this->adminLink->more = array('id' => $this->turnierid);
				$this->adminLink->view = "turregistrations"; // WL in Liste
				return false;
			}
		}
		// registration existent?
		if (!$row->id) {
			$this->app->enqueueMessage( CLMText::errorText('REGISTRATION', 'NOTEXISTING'), 'warning' );
			return false;
		}
		$post = $_POST; 
		if (!$row->bind($post)) {
			$this->app->enqueueMessage( $row->getError(), 'error' );
			return false;
		}

		$registrationname = $row->name;
		$pos = strpos($registrationname,',');
		$row->name = substr($registrationname,0,$pos);
		$row->vorname = substr($registrationname,($pos + 1));
		if ($row->dwz_I0 == '') $row->dwz_I0 = 0;
		if ($row->dwz == '') $row->dwz = NULL;
		if ($row->elo == '') $row->elo = NULL;
		if ($row->FIDEid == '') $row->FIDEid = NULL;
		if ($row->mgl_nr == '') $row->mgl_nr = 0;

		if ($task == 'apply' OR $task == 'save') $row->status  = 1;
		if ($task == 'copy_to') $row->status  = 2;
		if (!$row->check($post)) {
			$this->app->enqueueMessage( $row->getError(), 'error' );
			return false;
		}
		if (!$row->store()) {
			$this->app->enqueueMessage( $row->getError(), 'error' );
			return false;
		}

		if ($task == 'copy_to') {
			$turParams = new clm_class_params($rowt->params);
			$param_useastwz = $turParams->get('useAsTWZ', 0);

			// Teilnehmerdaten holen
			$tlnr = JTable::getInstance( 'turnier_teilnehmer', 'TableCLM' );
			$tlnr->sid		= $rowt->sid;
			$tlnr->turnier	= $row->tid;
			$tlnr->snr		= $this->snrmax + 1;  // 0
			$tlnr->name		= $registrationname;
			$tlnr->birthYear = $row->birthYear;
			$tlnr->geschlecht = $row->geschlecht;
			$tlnr->verein	= $row->club;
			$tlnr->email	= $row->email;
			if ($row->dwz == '' OR $row->dwz < 1) $tlnr->start_dwz = 0;
			else $tlnr->start_dwz = $row->dwz;
			$tlnr->start_I0	= $row->dwz_I0;
			if ($row->elo == '' OR $row->elo < 1) $tlnr->FIDEelo = 0;
			else $tlnr->FIDEelo	= $row->elo;
			$tlnr->twz		= 0;
			if ($param_useastwz == 0) {
				$tlnr->twz = max(array($tlnr->start_dwz, $tlnr->FIDEelo));
			} elseif ($param_useastwz == 1) {
				$tlnr->twz = $tlnr->start_dwz;
				if ($tlnr->twz == 0) {
					$tlnr->twz = $tlnr->FIDEelo;
				}
			} else {
				$tlnr->twz = $tlnr->FIDEelo;
				if ($tlnr->twz == 0) {
					$tlnr->twz = $tlnr->start_dwz;
				}
			}
			$tlnr->FIDEid	= $row->FIDEid;
			$tlnr->FIDEcco	= $row->FIDEcco;
			$tlnr->titel	= $row->titel;
			$tlnr->mgl_nr	= $row->mgl_nr;
			$tlnr->PKZ		= $row->PKZ;
			$tlnr->zps		= $row->zps;
			$tlnr->tel_no	= $row->tel_no;
			$tlnr->account	= $row->account;
			$tlnr->titel	= $row->titel;
			$tlnr->tlnrStatus = 1;
			$tlnr->published = 1;
			if (!$tlnr->check($post)) {
				$this->app->enqueueMessage( $tlnr->getError(), 'error' );
				return false;
			}
			if (!$tlnr->store()) {
				$this->app->enqueueMessage( $tlnr->getError(), 'error' );
				return false;
			}
			$text = JText::_('REGISTRATION_MOVED');
		} else {
			$text = JText::_('REGISTRATION_EDITED');
		}
		
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = $text;
		$clmLog->params = array('tid' => $this->turnierid, 'rid' => $this->registrationid, 'name' => $registrationname); // TurnierID wird als LigaID gespeichert
		$clmLog->write();
								   
		$this->app->enqueueMessage( $text );

		// wenn 'apply', weiterleiten in form
		if ($task == 'apply') {
			// Weiterleitung bleibt im Formular
			$this->adminLink->more = array('registrationid' => $this->registrationid);
		} else {
			// Weiterleitung in Liste
			$this->adminLink->more = array('id' => $this->turnierid);
			$this->adminLink->view = "turregistrations"; // WL in Liste
		}
		return true;
	
	}

	function cancel() {
		
		$this->adminLink->view = "turregistrations";
		$this->adminLink->more = array('id' => $this->turnierid);
		$this->adminLink->makeURL();
		$this->app->redirect( $this->adminLink->url );
		
	}

}
