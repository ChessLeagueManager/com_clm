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

class CLMControllerTurRoundMatches extends JControllerLegacy {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		$this->_db	= JFactory::getDBO();
		$this->app	= JFactory::getApplication();
		
		// Register Extra tasks
		$this->registerTask( 'apply', 'save' );
		$this->registerTask( 'unapprove','approve' );
		
		// turnierid
		$this->turnierid = clm_core::$load->request_int('turnierid');
		$this->roundid = clm_core::$load->request_int('roundid');
		
	
		$this->adminLink = new AdminLink();
		$this->adminLink->view = "turroundmatches";
		$this->adminLink->more = array('turnierid' => $this->turnierid, 'roundid' => $this->roundid);
	
	}


	function add() {
	
		$this->_addDo();
	
		$this->adminLink->makeURL();
		$this->app->redirect( $this->adminLink->url );
	
	}


	function _addDo() {
		
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
	
		// Instanz der Tabelle
		$row = JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load($this->turnierid);

		$clmAccess = clm_core::$access;      
		if (($row->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_round') !== true) OR $clmAccess->access('BE_tournament_edit_round') === false) {
			$this->app->enqueueMessage( JText::_('TOURNAMENT_NO_ACCESS'),'warning' );
			return false;
		}
	
		$user =JFactory::getUser();
	
		// Reale RundenNummer, DG aus RundenID ermitteln, tl_ok
		$query = 'SELECT sid, nr, dg, tl_ok'
				. ' FROM #__clm_turniere_rnd_termine'
				. ' WHERE id = '.$this->roundid
				;
		$this->_db->setQuery($query);
		list($sid, $runde, $dg, $tl_ok) = $this->_db->loadRow();
	
		// wenn Runde schon bestätigt, hinzufügen nicht erlauben
		if ($tl_ok == 1) {
			$this->app->enqueueMessage( CLMText::errorText('ROUND', 'ALREADYAPPROVED'),'warning' );
			return false;
		}
	
		// bisher höchstes Brett dieser Runde ermitteln
		$query = 'SELECT MAX(brett)'
				. ' FROM #__clm_turniere_rnd_spl'
				. ' WHERE turnier = '.$this->turnierid.' AND runde = '.$runde
				;
		$this->_db->setQuery($query);
		list($brettMax) = $this->_db->loadRow();
		// nächstes Brett
		$brettNew = $brettMax + 1;
	
		// neue Partie eintragen
		$sqlValuesStrings = array();
		$sqlValuesStrings[] = "('$sid','".$this->turnierid."','$runde','$brettNew','1','1')";
		$sqlValuesStrings[] = "('$sid','".$this->turnierid."','$runde','$brettNew','1','0')";
		// Partie eintragen
		$query	= "INSERT INTO #__clm_turniere_rnd_spl"
					. " (`sid`, `turnier`, `runde`, `brett`, `dg`, `heim`)"
					. " VALUES "
					.implode(", ", $sqlValuesStrings)
					;
//		$this->_db->setQuery($query);
//		if (!$this->_db->query()) { 
		if (!clm_core::$db->query($query)) { 
			$this->app->enqueueMessage( $this->_db->getErrorMsg(),'error' );
		}
		
		$this->app->enqueueMessage( JText::_('MATCH_ADDED'),'notice' );
	
		// Rangliste neu berechnen!
		$tournament = new CLMTournament($this->turnierid, true);
		$tournament->calculateRanking();
		$tournament->setRankingPositions();
	
	
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = JText::_('MATCH_ADDED');
		$clmLog->params = array('tid' => $this->turnierid, 'rnd' => $this->roundid); // TurnierID wird als LigaID gespeichert
		$clmLog->write();
	
		return true;
	
	}




	function save() {
	
		$this->_saveDo();
	
		if (clm_core::$load->request_string('task') == 'save') {
			$this->adminLink->more = array('id' => $this->turnierid);
			$this->adminLink->view = "turrounds";
		}
		
		$this->adminLink->makeURL();
		$this->app->redirect( $this->adminLink->url );
	
	}


	function _saveDo() {
	
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
	
		// Instanz der Tabelle
		$row = JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load($this->turnierid);

		$clmAccess = clm_core::$access;      
		if (($row->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_round') !== true) OR $clmAccess->access('BE_tournament_edit_round') === false) {
			$this->app->enqueueMessage( JText::_('TOURNAMENT_NO_ACCESS'),'warning' );
			return false;
		}
	
		// Für runden_termine update
		$user 		= JFactory::getUser();
		
		// Turnierdaten sammeln
		$tournament = new CLMTournament($this->turnierid, true);
		$this->teil = $tournament->data->teil;
		$turParams = new clm_class_params($tournament->data->params);
		$pgnInput = $turParams->get('pgnInput', 1);
	
	
		// Anzahl gemeldeter Teilnehmer sammeln
		$sum_tln = $tournament->getPlayersIn();
	
		// Anzahl Spieler abgleichen bevor gemeldet werden kann
		if ($sum_tln != $this->teil) {
			$diff = $this->teil-$sum_tln;
			$this->app->enqueueMessage( JText::_( 'INSUFFICIENT_PLAYERS_REGISTERED' ),'warning' );
			$this->app->enqueueMessage( JText::_( 'ADD_PLAYERS_OR_CHANGE_PARAMETERS' ),'notice' );
			return false;
		}
	
		// Reale RundenNummer, DG aus RundenID ermitteln - zudem 'gemeldet' für späteren Abgleich
		$query = 'SELECT nr, dg, gemeldet, tl_ok'
				. ' FROM #__clm_turniere_rnd_termine'
				. ' WHERE id = '.$this->roundid
				;
		$this->_db->setQuery($query);
		list($runde, $dg, $gemeldet, $tl_ok) = $this->_db->loadRow();
		
		if ($tl_ok == 1) { // Runden schon bestätigt?
			$this->app->enqueueMessage( CLMText::errorText(JText::_('ROUND'), 'ALREADYAPPROVED' ),'warning' );
			return false;
		}
		
	
		// Datensätze in Spielertabelle schreiben
		// convert Array nur für SCHWARZ !!
		$convert = array(0 => 1, 0, 2, 3, 5, 4, 6, 7, 8, 10, 9);
		// andere Matrix für eid
	
		// daten ermitteln
		$array_w = clm_core::$load->request_array_string('w');
		$array_b = clm_core::$load->request_array_string('b');
		$array_res = clm_core::$load->request_array_string('res');
		if ($pgnInput == 1) {
			$array_pgn = clm_core::$load->request_array_string('pgn');
		}
		$array_ergebnis = array(); // Array für bisherige Ergebnisse (Weiß)
		if ($tournament->data->typ == 3 or $tournament->data->typ == 5) { // KO
			$array_tiebrS = clm_core::$load->request_array_string('tiebrS');
			$array_tiebrG = clm_core::$load->request_array_string('tiebrG');
			$array_tiebrS_old = array();
			$array_tiebrG_old = array();
		}
		$array_id = array(); // Array für ID des nicht-heim-Matches
		$array_idBlack = array(); // für MatchID des schwarzen Datensatzes
		
		$array_duplo = array(); // Array für alle Startnummern, um Doppelungen abzuchecken
		
		// alle Matches anhand der weißen Daten durchgehen - evtl fehlerhafte Daten ausschließen
		foreach ($array_w as $key => $value) {
			// $key 					- die ID des Matches
			// $value 				- die snr des Weißen
			// $array_b[$key]		- die snr des Schwarzen
			// $array_res[$key]	- dir ID des Ergebnisses
			
			// check: Match vorhanden? mit 'heim' und in diesem Turnier?
			if ($tournament->data->typ != 3 AND $tournament->data->typ != 5) { // nicht KO
				// brett ermitteln, um zweiten Match-Datensatz ermitteln zu können
				$query = 'SELECT brett, ergebnis, pgn'
						. ' FROM #__clm_turniere_rnd_spl'
						. ' WHERE id = '.$key.' AND turnier = \''.$this->turnierid.'\' AND heim = \'1\''
						;
				$this->_db->setQuery($query);
				if (!list($brettBlack, $array_ergebnis[$key], $array_pgnold[$key]) = $this->_db->loadRow()) {
					$this->app->enqueueMessage( JText::_('MATCH_UNKNOWN')." - ID: ".$key,'warning' );
					// aus Array entfernen!
					unset($array_w[$key]);
				}
			} else { // KO
				// brett ermitteln, um zweiten Match-Datensatz ermitteln zu können
				// zudem ergebnis und tiebr nachladen
				$query = 'SELECT brett, ergebnis, tiebrS, tiebrG, pgn'
						. ' FROM #__clm_turniere_rnd_spl'
						. ' WHERE id = '.$key.' AND turnier = \''.$this->turnierid.'\' AND heim = \'1\''
						;
				$this->_db->setQuery($query);
				if (!list($brettBlack, $array_ergebnis[$key], $array_tiebrS_old[$key], $array_tiebrG_old[$key], $array_pgnold[$key]) = $this->_db->loadRow()) {
					$this->app->enqueueMessage( JText::_('MATCH_UNKNOWN')." - ID: ".$key,'warning' );
					// aus Array entfernen!
					unset($array_w[$key]);
				}
			}
			
			
			// check: zweiter Datensatz zu diesem match vorhanden? anhand von $brett
			// ID ermitteln und zwischenspeichern
			$query = "SELECT id"
					. " FROM #__clm_turniere_rnd_spl"
					. " WHERE turnier = '".$this->turnierid."' AND runde = '$runde' AND dg = '$dg' AND heim = '0' AND brett = ".$brettBlack
					;
			$this->_db->setQuery($query);
			if (!$array_idBlack[$key] = $this->_db->loadResult()) {
				$this->app->enqueueMessage( JText::_('MATCH_UNKNOWN')." - ID: ".$key,'warning' );
				// aus Array entfernen!
				unset($array_w[$key]);
			} else {
				$array_id[$key] = $id;
			}
			
			// alle Startnummern in Duplo-Array eintragen (wenn Startnummer vorhanden)
			if ($value > 0) $array_duplo[] = $value;
			if ($array_b[$key] > 0) $array_duplo[] = $array_b[$key];
		
		}
	
		// Duplo-Kontrolle - nicht im freien System
		if ($tournament->data->typ != 6 AND count($array_duplo) > count(array_unique($array_duplo)) ) {
			$this->app->enqueueMessage( JText::_("PLAYER_ENTERED_TWICE"),'warning' );
			return false;
		}
	
		$countChanges = 0;
		// noch vorhandene Matches erneut durchgehen
		foreach ($array_w as $key => $value) {
	
			// liegt Änderung vor?
			if ($tournament->data->typ != 3 AND $tournament->data->typ != 5) { // nicht KO
				if ( (is_null($array_ergebnis[$key]) AND $array_res[$key] >= 0) 
						OR (!is_null($array_ergebnis[$key]) AND $array_res[$key] != $array_ergebnis[$key])
						OR ($pgnInput == 1 AND $array_pgn[$key] != $array_pgnold[$key])
						) { // Änderung?
					$countChanges++;
				}
			} else { // KO - checkt auch auf tiebreaker-Änderung!
				if ( (is_null($array_ergebnis[$key]) AND $array_res[$key] >= 0) 
						OR (!is_null($array_ergebnis[$key]) AND $array_res[$key] != $array_ergebnis[$key])
						OR ($array_tiebrS_old[$key] != $array_tiebrS[$key])
						OR ($array_tiebrG_old[$key] != $array_tiebrG[$key])
						OR ($pgnInput == 1 AND $array_pgn[$key] != $array_pgnold[$key])
						) { // Änderung?
					$countChanges++;
					// dann aber auch ermitteln, wer ausscheidet, und wer im Turnier bleibt.
					if ($array_res[$key] == 2 OR $array_res[$key] == 3 OR $array_res[$key] == 6 OR $array_res[$key] == 7) { // remis bzw. nicht gespielt
//					if ($array_res[$key] == 2) { // remis
						if ($array_tiebrS[$key] > $array_tiebrG[$key]) {
							// sofort schreiben
							$this->_updateTlnrKoStatus($value, 1, $runde);
							$this->_updateTlnrKoStatus($array_b[$key], 0, $runde);
						} elseif ($array_tiebrS[$key] < $array_tiebrG[$key]) {
							// sofort schreiben
							$this->_updateTlnrKoStatus($value, 0, $runde);
							$this->_updateTlnrKoStatus($array_b[$key], 1, $runde);
						} else {
							// gleich - beide noch drin!
							$this->_updateTlnrKoStatus($value, 1, $runde);
							$this->_updateTlnrKoStatus($array_b[$key], 1, $runde);
						}
					} elseif ($array_res[$key] == 1 OR $array_res[$key] == 5) {
						// sofort schreiben
						$this->_updateTlnrKoStatus($value, 1, $runde);
						$this->_updateTlnrKoStatus($array_b[$key], 0, $runde);
					} elseif ($array_res[$key] == 0 OR $array_res[$key] == 4) {
						// sofort schreiben
						$this->_updateTlnrKoStatus($value, 0, $runde);
						$this->_updateTlnrKoStatus($array_b[$key], 1, $runde);
					}
				
				}
			}
			
			// jetzt die beiden Datesätze updaten
			if ($array_res[$key] == '-1' OR $array_res[$key] == '-2') { // kein Resultat eingegeben
				$sqlResultW = "NULL";
				$sqlResultB = "NULL";
			} else {
				$sqlResultW = "'".$array_res[$key]."'";
				$sqlResultB = "'".$convert[$array_res[$key]]."'";
			}
			// für pgn
			if ($pgnInput == 1) {
				$sqlPGN = ", pgn = '".$array_pgn[$key]."'";
			} else {
				$sqlPGN = "";
			}
		
		
			if ($tournament->data->typ != 3 AND $tournament->data->typ != 5) { // nicht KO
				// Weiss
				$query = "UPDATE #__clm_turniere_rnd_spl"
							. " SET ergebnis = ".$sqlResultW.", tln_nr = '".$value."', spieler = '".$value."', gegner = '".$array_b[$key]."'".$sqlPGN
							. " WHERE id = ".$key
							;
				clm_core::$db->query($query);
//				$this->_db->setQuery($query);
//				$this->_db->query();
		
				// Schwarz
				$query = "UPDATE #__clm_turniere_rnd_spl"
							. " SET ergebnis = ".$sqlResultB.", tln_nr ='".$array_b[$key]."', spieler ='".$array_b[$key]."', gegner ='".$value."'".$sqlPGN
							." WHERE id = ".$array_idBlack[$key]
							;
				clm_core::$db->query($query);
//				$this->_db->setQuery($query);
//				$this->_db->query();
		
			} else { // KO - auch tiebreak schreiben
				// Weiss
				$query = "UPDATE #__clm_turniere_rnd_spl"
							. " SET ergebnis = ".$sqlResultW.", tiebrS = '".$array_tiebrS[$key]."', tiebrG = '".$array_tiebrG[$key]."', tln_nr = '".$value."', spieler = '".$value."', gegner = '".$array_b[$key]."'".$sqlPGN
							. " WHERE id = ".$key
							;
				clm_core::$db->query($query);
//				$this->_db->setQuery($query);
//				$this->_db->query();
		
				// Schwarz
				$query = "UPDATE #__clm_turniere_rnd_spl"
							. " SET ergebnis = ".$sqlResultB.", tiebrS = '".$array_tiebrG[$key]."', tiebrG = '".$array_tiebrS[$key]."', tln_nr ='".$array_b[$key]."', spieler ='".$array_b[$key]."', gegner ='".$value."'".$sqlPGN
							." WHERE id = ".$array_idBlack[$key]
							;
				clm_core::$db->query($query);
//				$this->_db->setQuery($query);
//				$this->_db->query();
		
			}
	
		}
		// ergebnisse eingetragen
		
		// Nachrichten:
		// 1 - Matches wurden gespeichert
		// 2 - Anzahl ($countChanges) geänderter Erebnisse
		$this->app->enqueueMessage( JText::_('MATCHES').' '.JText::_('SAVED'),'notice' );
		$stringAction = CLMText::sgpl($countChanges, JText::_('RESULT'), JText::_('RESULTS'))." ".JText::_('SAVED')."/".JText::_('EDITED');
		$this->app->enqueueMessage( $stringAction,'notice' );

		$tournament->calculateRanking();
		$tournament->setRankingPositions();
	
		// Runde gemeldet ?
		if ($gemeldet == NULL) {
			$query = "UPDATE #__clm_turniere_rnd_termine"
					. " SET gemeldet = ".$user->id.", zeit = NOW()"
					. " WHERE turnier = ".$this->turnierid." AND nr = ".$runde." AND dg = ".$dg
					;
		} else {
			$query = "UPDATE #__clm_turniere_rnd_termine"
					. " SET editor = ".$user->id.", edit_zeit = NOW()"
					. " WHERE turnier = ".$this->turnierid." AND nr = ".$runde." AND dg = ".$dg
					;
		}
		clm_core::$db->query($query);
//		$this->_db->setQuery($query);
//		$this->_db->query();
		
		// Berechne oder Lösche die inoff. DWZ nach dieser Änderung
		$turParams = new clm_class_params(clm_core::$db->turniere->get($this->turnierid)->params);
		$autoDWZ = $turParams->get("autoDWZ",0);
		if($autoDWZ == 0) {
			clm_core::$api->direct("db_tournament_genDWZ",array($this->turnierid,false));
		} else if($autoDWZ == 1) {
			clm_core::$api->direct("db_tournament_delDWZ",array($this->turnierid,false));
		}		
		
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = $stringAction;
		$clmLog->params = array('tid' => $this->turnierid, 'rnd' => $runde); // TurnierID wird als LigaID gespeichert
		$clmLog->write();
	
	
		return true;
	
	}

	
	function _updateTlnrKoStatus($snr, $kostatus, $runde) {
		$query = "UPDATE #__clm_turniere_tlnr"
					. " SET koStatus = '".$kostatus."', koRound = ".$runde
					. " WHERE turnier = ".$this->turnierid." AND snr = ".$snr;
		clm_core::$db->query($query);
//		$this->_db->setQuery($query);
//		$this->_db->query();
		
	}
	


	function reset() {
	
		$this->_resetDo();
	
		$this->adminLink->makeURL();
		$this->app->redirect( $this->adminLink->url );
	
	}

	function p_reset() {
	
		$this->_resetDo('P');
	
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}


	function _resetDo($typ = 'R') {
		
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
	
		// Instanz der Tabelle
		$row = JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load($this->turnierid);

		$clmAccess = clm_core::$access;      
		if (($row->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_round') !== true) OR $clmAccess->access('BE_tournament_edit_round') === false) {
			$this->app->enqueueMessage( JText::_('TOURNAMENT_NO_ACCESS'),'warning' );
			return false;
		}
	
		$user =JFactory::getUser();
	
		// Reale RundenNummer, DG aus RundenID ermitteln, tl_ok
		$query = 'SELECT nr, dg, tl_ok'
				. ' FROM #__clm_turniere_rnd_termine'
				. ' WHERE id = '.$this->roundid
				;
		$this->_db->setQuery($query);
		list($runde, $dg, $tl_ok) = $this->_db->loadRow();
	
		// wenn Runde schon bestätigt, zurücksetzen nicht erlauben
		if ($tl_ok == 1) {
			$this->app->enqueueMessage( CLMText::errorText('ROUND', 'ALREADYAPPROVED'),'warning' );
			return false;
		}
	
		if ($typ == 'R') {	
			// sind überhaupt Ergebnisse eingetragen?
			$query = "SELECT COUNT(*) FROM #__clm_turniere_rnd_spl"
				. " WHERE turnier = ".$this->turnierid." AND runde = ".$runde." AND dg = ".$dg." AND ergebnis IS NOT NULL"
				;
			$this->_db->setQuery($query);
			$resultCount = $this->_db->loadResult();
			if ($resultCount == 0) {
				$this->app->enqueueMessage( CLMText::errorText('RESULTS', 'NOTEXISTING'),'warning' );
				return false;
			}
		}
 
		// Runde anpassen
		$query = "UPDATE #__clm_turniere_rnd_termine"
				. " SET gemeldet = NULL, zeit ='1970-01-01 00:00:00', editor = ".$user->id.", edit_zeit = NOW()"
				. " WHERE id = ".$this->roundid
				;
		clm_core::$db->query($query);
		
		// Ergebnisse löschen
		$query = "UPDATE #__clm_turniere_rnd_spl"
				. " SET ergebnis = NULL, tiebrS = 0, tiebrG = 0, gemeldet = NULL "
				. " WHERE turnier = ".$this->turnierid." AND runde = ".$runde." AND dg = ".$dg
				;
		clm_core::$db->query($query);
		$anz_result = clm_core::$db->affected_rows();					   						
		
		if ($typ == 'P') {
			// Ansetzungen löschen
			$query = "UPDATE #__clm_turniere_rnd_spl"
				. " SET tln_nr = NULL, spieler = NULL, gegner = NULL, kampflos = NULL, pgn = NULL "
				. " WHERE turnier = ".$this->turnierid." AND runde = ".$runde." AND dg = ".$dg
				;
			clm_core::$db->query($query);
			$anz_pair = clm_core::$db->affected_rows();					   
		}
	
		// Teilnehmer zurücksetzen bei KO-Turnier
		if ($row->typ == '3' or $row->typ == '5') {
			$query = "UPDATE #__clm_turniere_tlnr"
				. " SET koRound = ".($runde-1).", koStatus = '1' "
				. " WHERE turnier = ".$this->turnierid." AND koRound >= ".$runde
				;
			clm_core::$db->query($query);
//			$this->_db->setQuery($query);
//			$this->_db->query();
		
		}
		
		if ($typ == 'P') 
			$this->app->enqueueMessage(CLMText::sgpl(($anz_pair/2), JText::_('PAIRING'), JText::_('PAIRINGS') )." ".JText::_('RESET'),'notice' );
		else 
			$this->app->enqueueMessage( CLMText::sgpl(($anz_result/2), JText::_('RESULT'), JText::_('RESULTS') )." ".JText::_('RESET'),'notice' );
	
		// Rangliste neu berechnen!
		$tournament = new CLMTournament($this->turnierid, true);
		$tournament->calculateRanking();
		$tournament->setRankingPositions();
	
	
		// Log schreiben
		$clmLog = new CLMLog();
		if ($typ == 'P') 
			$clmLog->aktion = JText::_('PAIRINGS')." ".JText::_('RESET');
		else 
			$clmLog->aktion = JText::_('RESULTS')." ".JText::_('RESET');
		$clmLog->params = array('tid' => $this->turnierid, 'rnd' => $runde); // TurnierID wird als LigaID gespeichert
		$clmLog->write();
	
		return true;
	
	}


	function approve() {
	
		$this->_approveDo();

		$this->adminLink->makeURL();
		$this->app->redirect( $this->adminLink->url );
	
	}
	

	function _approveDo() {

		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );

		// Turnierdaten holen
		$turnier =JTable::getInstance( 'turniere', 'TableCLM' );
		$turnier->load( $this->turnierid ); // Daten zu dieser ID laden

		$clmAccess = clm_core::$access;      
		if (($turnier->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_round') !== true) OR $clmAccess->access('BE_tournament_edit_round') === false) {
			$this->app->enqueueMessage( JText::_('TOURNAMENT_NO_ACCESS'),'warning' );
			return false;
		}

		// Rundendaten holen
		$round =JTable::getInstance( 'turnier_runden', 'TableCLM' );
		$round->load( $this->roundid ); // Daten zu dieser ID laden

		// Runde existent?
		if (!$round->id) {
			$this->app->enqueueMessage( CLMText::errorText('ROUND', 'NOTEXISTING'),'warning' );
			return false;
		
		// Runde gehört zu Turnier?
		} elseif ($round->turnier != $this->turnierid) {
			$this->app->enqueueMessage( CLMText::errorText('ROUND', 'NOACCESS'),'warning' );
			return false;
		
		}
		
		
		$task		= clm_core::$load->request_string('task');
		$approve	= ($task == 'approve'); // zu vergebender Wert 0/1
	
		// weiterer Check: Ergebnisse vollständig?
		if ($approve == 1) {
			$tournamentRound = new CLMTournamentRound($this->turnierid, $this->roundid);
			if (!$tournamentRound->checkResultsComplete()) {
				$this->app->enqueueMessage( CLMText::errorText('RESULTS', 'INCOMPLETE'),'warning' );
				return false;
			}
		}
	
	
		// jetzt schreiben
		if ($approve) $round->tl_ok = 1; else $round->tl_ok = 0;
		if (!$round->store()) {
			$this->app->enqueueMessage( $round->getError(),'error' );
			return false;
		}
									   
		if ($approve) {
			$this->app->enqueueMessage( JText::_('ROUND')." ".JText::_('CLM_APPROVED') );
		} else {
			$this->app->enqueueMessage( JText::_('ROUND')." ".JText::_('CLM_UNAPPROVED') );
		}
	
		// Log
		$clmLog = new CLMLog();
		$clmLog->aktion = JText::_('ROUND')." ".$row->name." (ID: ".$this->roundid."): ".$task;
		$clmLog->params = array('tid' => $this->turnierid); // TurnierID wird als LigaID gespeichert
		$clmLog->write();
	
	
		return true;
	
	}

	function goto_players() {
		
		$this->adminLink->more = array('id' => $this->turnierid);
		$this->adminLink->view = "turplayers";
		$this->adminLink->makeURL();
		
		$this->app->redirect( $this->adminLink->url );
	}
	
	function cancel() {
		
		$this->adminLink->more = array('id' => $this->turnierid);
		$this->adminLink->view = "turrounds";
		$this->adminLink->makeURL();
		
		$this->app->redirect( $this->adminLink->url );
		
	}

	function delete() {
	
		$this->_deleteDo();
	
		$this->adminLink->makeURL();
		$this->app->redirect( $this->adminLink->url );
	
	}


	function _deleteDo() {
		
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
		
		// Instanz der Tabelle
		$row = JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load($this->turnierid);

		$clmAccess = clm_core::$access;      
		if (($row->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_round') !== true) OR $clmAccess->access('BE_tournament_edit_round') === false) {
			$this->app->enqueueMessage( JText::_('TOURNAMENT_NO_ACCESS'),'warning' );
			return false;
		}
	
		$user =JFactory::getUser();
	
		// Reale RundenNummer, DG aus RundenID ermitteln, tl_ok
		$query = 'SELECT sid, nr, dg, tl_ok'
				. ' FROM #__clm_turniere_rnd_termine'
				. ' WHERE id = '.$this->roundid
				;
		$this->_db->setQuery($query);
		list($sid, $runde, $dg, $tl_ok) = $this->_db->loadRow();
	
		// wenn Runde schon bestätigt, löschen nicht erlauben
		if ($tl_ok == 1) {
			$this->app->enqueueMessage( CLMText::errorText('ROUND', 'ALREADYAPPROVED'),'warning' );
			return false;
		}
	
		// bisher höchstes Brett dieser Runde ermitteln
		$query = 'SELECT MAX(brett)'
				. ' FROM #__clm_turniere_rnd_spl'
				. ' WHERE turnier = '.$this->turnierid.' AND runde = '.$runde
				;
		$this->_db->setQuery($query);
		list($brettMax) = $this->_db->loadRow();
		//echo "<br>brettM: ".$brettMax."  "; //var_dump(list($brettMax));
		// letzte Paarung löschen
		$query	= "DELETE FROM #__clm_turniere_rnd_spl"
					. " WHERE sid = ".$sid." AND turnier = ".$this->turnierid 
					. "   AND runde = ".$runde." AND brett = ".$brettMax
					. " LIMIT 2 "
					;
//		$this->_db->setQuery($query);
//		if (!$this->_db->query()) { 
		if (!clm_core::$db->query($query)) { 
			$this->app->enqueueMessage( $this->_db->getErrorMsg(),'error' );
		}
		
		$this->app->enqueueMessage( JText::_('MATCH_DELETED'),'notice' );
	
		// Rangliste neu berechnen!
		$tournament = new CLMTournament($this->turnierid, true);
		$tournament->calculateRanking();
		$tournament->setRankingPositions();
	
	
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = JText::_('MATCH_DELETED');
		$clmLog->params = array('tid' => $this->turnierid, 'rnd' => $this->roundid); // TurnierID wird als LigaID gespeichert
		$clmLog->write();
	
		return true;
	
	}

	function draw() {
	
		$this->_drawDo();
	
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}

	function _drawDo() {
		
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
//echo "<br>turnierid:"; var_dump($this->turnierid);
//echo "<br>roundid:"; var_dump($this->roundid);

		// Reale RundenNummer, DG aus RundenID ermitteln, tl_ok
		$query = 'SELECT nr, dg, tl_ok'
				. ' FROM #__clm_turniere_rnd_termine'
				. ' WHERE id = '.$this->roundid
				;
		$this->_db->setQuery($query);
		list($runde, $dg, $tl_ok) = $this->_db->loadRow();
//echo "<br>runde:"; var_dump($runde);
//echo "<br>dg:"; var_dump($dg);
//die('');
		$result = clm_core::$api->db_draw_ch($this->turnierid,$dg,$runde,false,true);
//var_dump($result);
		if ($result[0] == false)
			$this->app->enqueueMessage( $result[1],'warning' );
		else
			$this->app->enqueueMessage( $result[1],'notice' );

//die('napi');
		
	}
	
 }
