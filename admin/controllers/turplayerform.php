<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2017 CLM Team.  All rights reserved
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
		$this->turnierid = JRequest::getInt('id');
		
		$this->_db		= JFactory::getDBO();
		
		$this->app =JFactory::getApplication();
		
		// Register Extra tasks
		$this->registerTask( 'apply', 'save' );
	
		$this->adminLink = new AdminLink();
		$this->adminLink->more = array('id' => $this->turnierid);
		$this->adminLink->view = "turplayerform";
	
	}

	
	// checkt, ob Turnier noch Teilnehmerplätze frei hat
	function _checkTournamentOpen($playersIn, $teil) {
	
		if ($playersIn >= $teil) {
			JError::raiseWarning( 500, CLMText::errorText('PLAYERLIST', 'FULL') );
			return false;
		}
		
		return true;
	
	}
	
	function save() {
	
		$this->_saveDo();

		// abschließend offene Restteilnehmerzahl

		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}
	
	
	function _saveDo() {
	
		JRequest::checkToken() or die( 'Invalid Token' );
		//CLM parameter auslesen
		$config = clm_core::$db->config();
		$countryversion = $config->countryversion;
	
		// Instanz der Tabelle
		$row = JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load( $this->turnierid ); // Daten zu dieser ID laden

		$clmAccess = clm_core::$access;      
		if (($row->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== true) OR $clmAccess->access('BE_tournament_edit_detail') === false) {
		//if (clm_core::$access->getType() != 'admin' AND clm_core::$access->getType() != 'tl') {
			JError::raiseWarning(500, JText::_('TOURNAMENT_NO_ACCESS') );
			return false;
		}
	
		$turnier = $this->turnierid;
		$task		= JRequest::getVar( 'task');
		$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
		// JArrayHelper::toInteger($cid, array(0));
	
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
	
		$name	= trim(JRequest::getVar('name'));
		// Spieler aus manueller Nachmeldung speichern
		if ($name != "") {
			
			// weitere Angaben holen
			$verein = JRequest::getString('verein');
			// $dwz = JRequest::getInt('dwz');
			$natrating = JRequest::getInt('natrating', 0);
			$fideelo = JRequest::getInt('fideelo', 0);
			$titel = JRequest::getString('titel');
			$geschlecht = JRequest::getString('geschlecht', 'NULL');
			$birthYear = JRequest::getString('birthYear', '0000');
			
			$twz = clm_core::$load->gen_twz($param_useastwz, $natrating, $fideelo);

			$query = " INSERT INTO #__clm_turniere_tlnr"
				." (`sid`, `turnier`, `snr`, `name`, `birthYear`, `geschlecht`, `verein`, `twz`, `start_dwz`, `FIDEelo`, `titel`, `mgl_nr` ,`zps`)"
				." VALUES"
				." ('".$tournament->data->sid."', '".$this->turnierid."', '".$maxSnr++."', '$name', '$birthYear', '$geschlecht', '$verein', '$twz', '$natrating', '$fideelo', '$titel', '".$maxFzps."', '99999')";
			$this->_db->setQuery($query);
			if ($this->_db->query()) {
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
						. " LEFT JOIN #__clm_dwz_vereine as v ON v.ZPS = a.ZPS"
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
						JError::raiseWarning( 500, JText::_('PLAYER')." ".$data->Spielername." ".JText::_('ALREADYIN') );
					} else {
					
						$twz = clm_core::$load->gen_twz($param_useastwz, $data->DWZ, $data->FIDE_Elo);
						
						$query = " INSERT INTO #__clm_turniere_tlnr"
								. " (`sid`, `turnier`, `snr`, `name`, `birthYear`, `geschlecht`, `verein`, `twz`, `start_dwz`, `FIDEelo`, `FIDEid`, `FIDEcco`, `titel`,`mgl_nr` ,`PKZ` ,`zps`) "
								. " VALUES"
								. " ('".$tournament->data->sid."','".$this->turnierid."', '".$maxSnr++."', '".clm_escape( $data->Spielername )."', '".$data->Geburtsjahr."', '".$data->Geschlecht."','".clm_escape( $data->Vereinname )."', '".$twz."', '".$data->DWZ."', '".$data->FIDE_Elo."', '".$data->FIDE_ID."', '".$data->FIDE_Land."', '".$data->FIDE_Titel."', '$mgl', '$PKZ', '$zps')";
						$this->_db->setQuery($query);
						
						if ($this->_db->query()) {
							$playersIn++;
							$this->app->enqueueMessage(JText::_('PLAYER')." ".$data->Spielername." ".JText::_('ADDED'));
						} else {
							JError::raiseWarning( 500, JText::_('DB_ERROR') );
						}
					}
				
				} else {
					JError::raiseWarning( 500, CLMText::errorText('PLAYER', 'UNKNOWN') );
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
			JError::raiseNotice( 500, JText::_('PARTICIPANTS_WANTED').": ".$openSpots );
		} else {
			JError::raiseNotice( 500, CLMText::errorText('PARTICIPANTLIST', 'FULL') );
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
		$add_nz = JRequest::getInt('add_nz');
		if ($add_nz == '1') {
			$tournament = new CLMTournament($this->turnierid, true);
			$tournament->makeMinusTln(); // Message werden dort erstellt
		}
		
		$this->adminLink->view = "turplayers";
		$this->adminLink->more = array('id' => $this->turnierid);
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
		
	}

}
