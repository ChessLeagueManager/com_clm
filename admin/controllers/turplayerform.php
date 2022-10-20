<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2022 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerTurPlayerForm extends JControllerLegacy {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		// turnierid
		$this->turnierid = clm_core::$load->request_int('id');
		
		$this->_db		= JFactory::getDBO();
		
		$this->app = JFactory::getApplication();
		
		// Register Extra tasks
		$this->registerTask( 'apply', 'save' );
	
		$this->adminLink = new AdminLink();
		$this->adminLink->more = array('id' => $this->turnierid);
		$this->adminLink->view = "turplayerform";
	
	}

	
	// checkt, ob Turnier noch Teilnehmerplätze frei hat
	function _checkTournamentOpen($playersIn, $teil) {
	
		if ($playersIn >= $teil) {
			$this->app->enqueueMessage(CLMText::errorText('PLAYERLIST', 'FULL'),'warning');
			return false;
		}
		
		return true;
	
	}
	
	function save() {
	
		$this->_saveDo();

		// abschließend offene Restteilnehmerzahl

		$this->adminLink->makeURL();
		$this->app->redirect( $this->adminLink->url );
	
	}
	
	
	function _saveDo() {
	
		defined('_JEXEC') or die( 'Invalid Token' );
		//CLM parameter auslesen
		$config = clm_core::$db->config();
		$countryversion = $config->countryversion;
	
		// Instanz der Tabelle
		$row = JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load( $this->turnierid ); // Daten zu dieser ID laden

		$clmAccess = clm_core::$access;      
		if (($row->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== true) OR $clmAccess->access('BE_tournament_edit_detail') === false) {
			$this->app->enqueueMessage(JText::_('TOURNAMENT_NO_ACCESS'),'warning');
			return false;
		}
	
		$turnier = $this->turnierid;
		$task		= clm_core::$load->request_string('task');
		$cid 		= clm_core::$load->request_array_string('cid');
	
		// weiteren Daten aus TlnTabelle
		$query = "SELECT MAX(mgl_nr), MAX(snr) FROM `#__clm_turniere_tlnr`"
			." WHERE turnier = ".$this->turnierid
			;
		$this->_db->setQuery($query);
		list($maxFzps, $maxSnr) = $this->_db->loadRow();
		$maxFzps++; // fiktive ZPS für manuell eingegeben Spieler
		$maxSnr++; // maximale snr für alle Spieler
		
	
		// Turnierdaten
		$tournament = new CLMTournament($this->turnierid, true);
		$playersIn = $tournament->getPlayersIn();
		$turParams = new clm_class_params($tournament->data->params);
		$param_useastwz = $turParams->get('useAsTWZ', 0);
	
		// Turnier schon vorher voll?
		if (!$this->_checkTournamentOpen($playersIn, $tournament->data->teil)) {
			return false;
		}
	
		$name	= trim(clm_core::$load->request_string('name'));
		// Spieler aus manueller Nachmeldung speichern
		if ($name != "") {
			
			// weitere Angaben holen
			$verein = clm_core::$load->request_string('verein');
			// $dwz = clm_core::$load->request_int('dwz');
			$natrating = clm_core::$load->request_int('natrating');
			$fideelo = clm_core::$load->request_int('fideelo');
			$titel = clm_core::$load->request_string('titel');
			$geschlecht = clm_core::$load->request_string('geschlecht', 'NULL');
			$birthYear = clm_core::$load->request_string('birthYear', '0000');
			
			$twz = clm_core::$load->gen_twz($param_useastwz, $natrating, $fideelo);
			if (is_null($twz) OR $twz == '') $twz = 0;						
			if (is_null($natrating) OR $natrating == '') $natrating = 0;						
			if (is_null($fideelo) OR $fideelo == '') $fideelo = 0;						

			$query = " INSERT INTO #__clm_turniere_tlnr"
				." (`sid`, `turnier`, `snr`, `name`, `birthYear`, `geschlecht`, `verein`, `twz`, `start_dwz`, `FIDEelo`, `titel`, `mgl_nr` ,`zps`)"
				." VALUES"
				." ('".$tournament->data->sid."', '".$this->turnierid."', '".$maxSnr++."', '$name', '$birthYear', '$geschlecht', '$verein', '$twz', '$natrating', '$fideelo', '$titel', '".$maxFzps."', '99999')";
//		$this->_db->setQuery($query);
//		if ($this->_db->query()) {
		if (clm_core::$db->query($query)) { 
				$this->app->enqueueMessage(JText::_('PLAYER')." ".$name." ".JText::_('ADDED'));
				$playersIn++; // den angemeldeten Spielern zufügen
				return true;
			} else {
				$this->app->enqueueMessage(JText::_('DB_ERROR'));
				return false;
			}
			
		}
		// wenn hier ein Spieler eingetragen wurde, geht es nicht mehr durch die Liste...
	
		
		foreach ($cid as $id) {
			
			// noch Platz im Turnier?
			if ($this->_checkTournamentOpen($playersIn, $tournament->data->teil)) {
			
				// ausgelesene Daten
				if ($countryversion =="de") {
					$PKZ = '';
					$mgl	= substr($id, 5);
					if(!is_numeric($mgl)) { $mgl = -1; }
					$zps	= substr($id, 0, 5);
				} else {  // engl. Anwendung
					$mgl = 0;
					$PKZ	= substr($id, 4);
					//if(!is_numeric($PKZ)) { $PKZ = -1; }
					$zps	= substr($id, 0, 4);
				}
				// weitere Daten des Spielers ermitteln
				// in CLM DB suchen
				$query = "SELECT a.Spielername, a.Geburtsjahr, a.Geschlecht, a.FIDE_Titel, a.FIDE_Elo, a.FIDE_ID, FIDE_Land, a.DWZ, v.Vereinname, a.PKZ"
						. " FROM `#__clm_dwz_spieler` as a"
						. " LEFT JOIN #__clm_dwz_vereine as v ON v.ZPS = a.ZPS AND v.sid = a.sid "
						. " LEFT JOIN #__clm_saison as s ON s.id = a.sid "
						. " WHERE a.ZPS = '$zps'"
						. " AND s.archiv = 0 "
						. " AND a.sid = ".clm_core::$access->getSeason();
				if ($countryversion =="de") {
					$query .= " AND a.Mgl_Nr = ".$mgl;
				} else {
					$query .= " AND a.PKZ = '".$PKZ."'";
				}

				$this->_db->setQuery($query);
				$data	= $this->_db->loadObject();
				if (isset($data->Spielername)) {
					if ($PKZ == '') $PKZ = $data->PKZ;
					// checken ob Spieler schon eingetragen, um Doppelungen zu vermeiden
					$query = "SELECT COUNT(*) FROM #__clm_turniere_tlnr"
							. " WHERE `turnier` = '".$this->turnierid."' AND `zps` = '$zps'";
					if ($countryversion =="de") {
						$query .= " AND mgl_nr = ".$mgl;
					} else {
						$query .= " AND PKZ = '".$PKZ."'";
					}
					$this->_db->setQuery($query);
					if ($this->_db->loadResult() > 0) {
						$this->app->enqueueMessage(JText::_('PLAYER')." ".$data->Spielername." ".JText::_('ALREADYIN'), 'warning');
					} else {
					
						$twz = clm_core::$load->gen_twz($param_useastwz, $data->DWZ, $data->FIDE_Elo);
						if (is_null($twz) OR $twz == '') $twz = 0;						
						if (is_null($data->DWZ) OR $data->DWZ == '') $data->DWZ = 0;						
						if (is_null($data->FIDE_Elo) OR $data->FIDE_Elo == '') $data->FIDE_Elo = 0;						
						if (is_null($data->FIDE_ID) OR $data->FIDE_ID == '') $data->FIDE_ID = 0;						
						
						$query = " INSERT INTO #__clm_turniere_tlnr"
								. " (`sid`, `turnier`, `snr`, `name`, `birthYear`, `geschlecht`, `verein`, `twz`, `start_dwz`, `FIDEelo`, `FIDEid`, `FIDEcco`, `titel`,`mgl_nr` ,`PKZ` ,`zps`) "
								. " VALUES"
								. " ('".$tournament->data->sid."','".$this->turnierid."', '".$maxSnr++."', '".clm_escape( $data->Spielername )."', '".$data->Geburtsjahr."', '".$data->Geschlecht."','".clm_escape( $data->Vereinname )."', '".$twz."', '".$data->DWZ."', '".$data->FIDE_Elo."', '".$data->FIDE_ID."', '".$data->FIDE_Land."', '".$data->FIDE_Titel."', '$mgl', '$PKZ', '$zps')";
//						$this->_db->setQuery($query);
//						if ($this->_db->query()) { 
						if (clm_core::$db->query($query)) { 
							$playersIn++;
							$this->app->enqueueMessage(JText::_('PLAYER')." ".$data->Spielername." ".JText::_('ADDED'));
						} else {
							$this->app->enqueueMessage(JText::_('DB_ERROR'), 'warning');
						}
					}
				
				} else {
					$this->app->enqueueMessage(CLMText::errorText('PLAYER', 'UNKNOWN'),'warning' );
				}
			
			} // sonst war Turnier voll
			
		}
	
	
		// je nach Task: Message und Weiterleitung
		switch ($task) {
			case 'apply':
				$stringAktion = JText::_('PLAYERS_ADDED');
				break;
			case 'save':
			default:
				$stringAktion = JText::_('PLAYERS_SAVED');
				$this->adminLink->view = "turplayers"; // WL in Liste
				break;
		}
	
		// Plätze frei?
		$openSpots = ($tournament->data->teil-$playersIn);
		if ($openSpots > 0) {
			$this->app->enqueueMessage(JText::_('PARTICIPANTS_WANTED').": ".$openSpots, 'notice' );
		} else {
			$this->app->enqueueMessage(CLMText::errorText('PARTICIPANTLIST', 'FULL'), 'notice' );
		}
	
	
		// Message
		// $this->app->enqueueMessage($stringAktion);
			
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = $stringAktion;
		$clmLog->params = array('sid' => $tournament->data->sid, 'tid' => $this->turnierid); // TurnierID wird als LigaID gespeichert
		$clmLog->write();

		return true;
	
	}

	function cancel() {
		$add_nz = clm_core::$load->request_int('add_nz');
		if ($add_nz == '1') {
			$tournament = new CLMTournament($this->turnierid, true);
			$tournament->makeMinusTln(); // Message werden dort erstellt
		}
		
		$this->adminLink->view = "turplayers";
		$this->adminLink->more = array('id' => $this->turnierid);
		$this->adminLink->makeURL();
		$this->app->redirect( $this->adminLink->url );
		
	}

}
