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
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;

class CLMControllerTurWaitlistEdit extends JControllerLegacy {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		$this->app =Factory::getApplication();
		
		// Register Extra tasks
		$this->registerTask( 'apply', 'save' );
		$this->registerTask( 'move_to', 'save' );
	}

	
	
	function save() {
	
		$this->_saveDo();

		// playerid
		$playerid = clm_core::$load->request_int('playerid');
		$turnierid = clm_core::$load->request_int('turnierid');
		// Task
		$task = clm_core::$load->request_string('task');
		$adminLink = new AdminLink();
		if ($task == 'apply') {
			// Weiterleitung bleibt im Formular
			$adminLink->more = array('playerid' => $playerid );
			$adminLink->view = "turwaitlistedit";
		} else {
			// Weiterleitung in Liste
			$adminLink->more = array('id' => $turnierid );
			$adminLink->view = "turwaitlist"; // Weiterleitung in Warteliste
		}
		$adminLink->makeURL();
		$this->app->redirect( $adminLink->url );
	
	}
	
	
	function _saveDo() {
	
		defined('_JEXEC') or die( 'Invalid Token' );
	
		// turnierid
		$playerid = clm_core::$load->request_int('playerid');
		$turnierid = clm_core::$load->request_int('turnierid');

		// Task
		$task = clm_core::$load->request_string('task');
		
		// Instanz der Tabelle
		$rowt = Table::getInstance( 'turniere', 'TableCLM' );
		$rowt->load( $turnierid ); // Daten zu dieser ID laden

		$clmAccess = clm_core::$access;      
		if (($rowt->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== true) OR $clmAccess->access('BE_tournament_edit_detail') === false) {
			$this->app->enqueueMessage(Text::_('TOURNAMENT_NO_ACCESS'),'warning');
			return false;
		}
	
		// Instanz der Tabelle
		$row = Table::getInstance( 'turnier_waitlist', 'TableCLM' );
		$row->load( $playerid ); // Daten zu dieser ID laden

		// Spieler existent?
		if (!$row->id) {
			$this->app->enqueueMessage(CLMText::errorText('PLAYER', 'NOTEXISTING'),'warning');
			return false;
		
		// Runde gehört zu Turnier?
		} elseif ($row->turnier != $turnierid) {
			$this->app->enqueueMessage(CLMText::errorText('PLAYER', 'NOACCESS'),'warning');
			return false;
		}
		
		$post = $_POST; 
		if (!$row->bind($post)) {
			$this->app->enqueueMessage($row->getError(),'error');
			return false;
		}
		if (is_null($row->mgl_nr) OR !is_numeric($row->mgl_nr)) $row->mgl_nr = 999;
		if ($row->start_dwz == '') $row->start_dwz = 0;
		if (is_null($row->start_I0) OR !is_numeric($row->start_I0)) $row->start_I0 = 0;
		if ($row->sum_punkte == '') $row->sum_punkte = 0;
		if ($row->sumTiebr1 == '') $row->sumTiebr1 = 0;
		if ($row->sumTiebr2 == '') $row->sumTiebr2 = 0;
		if ($row->sumTiebr3 == '') $row->sumTiebr3 = 0;
		if (is_null($row->FIDEid) OR !is_numeric($row->FIDEid)) $row->FIDEid = 0;
		if (is_null($row->birthYear) OR !is_numeric($row->birthYear)) $row->birthYear = '0000';
		if ($row->birthDay == '') $row->birthDay = NULL;
		if (is_null($row->s_punkte) OR !is_numeric($row->s_punkte)) $row->s_punkte = 0.0;
		if (($row->date_paid == '') OR ($row->date_paid <= '1970-01-01')) $row->date_paid = NULL;
		if ($row->amount_paid == '') $row->amount_paid = NULL;
		
		if (!$row->check($post)) {
			$this->app->enqueueMessage($row->getError(),'error');
			return false;
		}
		if (!$row->store()) {
			$this->app->enqueueMessage($row->getError(),'error');
			return false;
		}
		$text = Text::_('PARTICIPANT_EDITED').": ".$row->name;
		
		if ($task == "move_to") {
			// Turnierdaten
			$tournament = new CLMTournament($rowt->id, true);
			$playersIn = $tournament->getPlayersIn();
			$text = '';
			if ($playersIn >= $rowt->teil) {
				$text = CLMText::errorText('PLAYERLIST', 'FULL');
			}
			if ($text != '') {
				$this->app->enqueueMessage( $text );
				// Weiterleitung zurück in Liste
				return false;
			}
			//Record aus Warteliste lesen
			$select_query = " SELECT * FROM #__clm_turniere_tlnr_wl
						WHERE id = ".$row->id.";";
			$tlnr	= clm_core::$db->loadObject($select_query);
			//Record in Teilnehmerliste suchen
			$select_query = " SELECT * FROM #__clm_turniere_tlnr
						WHERE turnier = ".$tlnr->turnier." AND zps = '".$tlnr->zps."' AND mgl_nr = ".$tlnr->mgl_nr.";";
			$lookup	= clm_core::$db->loadObject($select_query);
			if (!is_null($lookup)) {
				$text = 'Spieler bereits in Teilnehmerliste';
				$this->app->enqueueMessage( $text );
				// Weiterleitung zurück in Anzeige
				return false;
			}

			$tlnr->id = 0;
			// letzte Startnummer aus der Teilnehmertabelle
			$query = 'SELECT MAX(snr) as snrmax '
				. ' FROM #__clm_turniere_tlnr'
				. ' WHERE turnier = '.$rowt->id
				;
			$turnierSnrMax = clm_core::$db->loadObject($query);	
			if (isset($turnierSnrMax->snrmax)) $snrmax = $turnierSnrMax->snrmax; 
			else $snrmax = 0;
			$tlnr->snr		= $snrmax + 1;  
			if (strlen($tlnr->zps) != 5 OR $tlnr->mgl_nr < 1) {
				// weiteren Daten aus TlnTabelle
				$db		= Factory::getDBO();
				$query = "SELECT MAX(mgl_nr), MAX(snr) FROM `#__clm_turniere_tlnr`"
					." WHERE turnier = ".$rowt->id
					." AND zps = 99999 "
					;
				$db->setQuery($query);
				list($maxFzps, $maxSnr) = $db->loadRow();
				$maxFzps++; // fiktive ZPS für manuell eingegeben Spieler
				$tlnr->zps = '99999';
				$tlnr->mgl_nr = $maxFzps;
			}
			if(!clm_core::$db->insertObject('#__clm_turniere_tlnr',$tlnr,'id')) {
				$this->app->enqueueMessage( $tlnr->getError(), 'error' );
				return false;
			}
			//Löschen aus Warteliste
			$delete_query = " DELETE FROM #__clm_turniere_tlnr_wl
						WHERE id = ".$row->id.";";
			clm_core::$db->query($delete_query);
			
			$text = Text::_('In Teilnehmerliste geschoben ').": ".$row->name;
		}
		
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = $text;
		$clmLog->params = array('sid' => $row->sid, 'tid' => $turnierid); // TurnierID wird als LigaID gespeichert
		$clmLog->write();
		
		$this->app->enqueueMessage( $text );

		return true;
	
	}

	function cancel() {
		
		// turnierid
		$turnierid = clm_core::$load->request_int('turnierid');
		$dview = clm_core::$load->request_string('dview','std');

		$adminLink = new AdminLink();
		$adminLink->view = "turplayers";
		$adminLink->more = array('id' => $turnierid, 'dview' => $dview);
		$adminLink->makeURL();
		$this->app->redirect( $adminLink->url );
		
	}

}
