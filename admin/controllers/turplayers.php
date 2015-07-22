<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2014 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

class CLMControllerTurPlayers extends JController {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		// turnierid
		$this->id = JRequest::getInt('id');
		
		$this->_db		= & JFactory::getDBO();
		
		// Register Extra tasks
		$this->registerTask( 'unactive','active' );

		$this->adminLink = new AdminLink();
		$this->adminLink->view = "turplayers";
		$this->adminLink->more = array('id' => $this->id);
	
	}

	function turform() {
		
		$this->adminLink->view = "turform";
		$this->adminLink->makeURL();
		
		$this->setRedirect( $this->adminLink->url );
	
	}


	// Weiterleitung!
	function add() {
		
		$this->adminLink->view = "turplayerform";
		$this->adminLink->makeURL();
		
		$this->setRedirect( $this->adminLink->url );
	
	}
	// Nachzügler aufnehmen =  Anzahl erhöhen + Weiterleitung!
	function add_nz() {
		$tournament = new CLMTournament($this->id, true);
		$tournament->makePlusTln(); // Message werden dort erstellt
		
		$this->adminLink->view = "turplayerform";
		$this->adminLink->more = array('id' => $this->id, 'add_nz' => 1 );
		$this->adminLink->makeURL();
		
		$this->setRedirect( $this->adminLink->url );
	
	}

	function plusTln() {
	
		$this->_plusTlnDo();

		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}


	function _plusTlnDo() {
	
		$tournament = new CLMTournament($this->id, true);
		$tournament->makePlusTln(); // Message werden dort erstellt
		
		return true;
	
	}



	function remove() {
	
		$this->_removeDo();

		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}


	function _removeDo() {
	
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
	
		// Turnierdaten holen
		$turnier =& JTable::getInstance( 'turniere', 'TableCLM' );
		$turnier->load( $this->id ); // Daten zu dieser ID laden

		// Turnier existent?
		if (!$turnier->id) {
			JError::raiseWarning( 500, CLMText::errorText('TOURNAMENT', 'NOTEXISTING') );
			return false;
		}
	
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'CLMAccess.class.php');
		$clmAccess = new CLMAccess();
		$clmAccess->accesspoint = 'BE_tournament_edit_detail';
		if (($turnier->tl != CLM_ID AND $clmAccess->access() !== true) OR $clmAccess->access() === false) {
		//if (CLM_usertype != 'admin' AND CLM_usertype != 'tl') {
			JError::raiseWarning(500, JText::_('TOURNAMENT_NO_ACCESS') );
			return false;
		}
	
		// Wenn Ergebnisse gemeldet keine nachträgliche Löschung erlauben
		$tournament = new CLMTournament($this->id);
		$tournament->checkTournamentStarted();
		if ($tournament->started) {
			JError::raiseWarning( 500, JText::_( 'DELETION_NOT_POSSIBLE' ).": ".JText::_('RESULTS_ENTERED') );
			return false;
		}
	
		// ausgewählte Einträge
		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
	
		if (count($cid) < 1) {
			JError::raiseWarning(500, JText::_( 'NO_ITEM_SELECTED', true ) );
			return false;
		}
		// alle Checks erledigt
	
	
		$cids = implode(',', $cid );
		$query = 'DELETE FROM #__clm_turniere_tlnr'
				.' WHERE turnier = '.$turnier->id.' AND id IN ( '. $cids .' )'
			;
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) {
			JError::raiseWarning(500, JText::_( 'DB_ERROR', true ) );
			return false;
		}
	
		$text = CLMText::sgpl(count($cid), JText::_('PLAYER'), JText::_('PLAYERS'))." ".JText::_('DELETED');
	
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = $text;
		$clmLog->params = array('sid' => $turnier->sid, 'tid' => $turnier->id, 'cids' => count($cid));
		$clmLog->write();
	
	
		// Message
		$app =& JFactory::getApplication();
		$app->enqueueMessage( $text );
	
		return true;
		
	}

	// Moves the record up one position
	function orderdown() {
		
		$this->_order(1);
		
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}

	// Moves the record down one position
	function orderup() {
		
		$this->_order(-1);
		
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}

	// Moves the order of a record
	// @param integer The direction to reorder, +1 down, -1 up
	function _order($inc) {
	
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
	
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'CLMAccess.class.php');
		$clmAccess = new CLMAccess();
		$clmAccess->accesspoint = 'BE_tournament_edit_detail';
		if ($clmAccess->access() === false) {
		//if (CLM_usertype != 'admin' AND CLM_usertype != 'tl') {
			JError::raiseWarning(500, JText::_('TOURNAMENT_NO_ACCESS') );
			return false;
		}
	
		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		$tlnid = $cid[0];
	
		$row =& JTable::getInstance( 'turnier_teilnehmer', 'TableCLM' );
		if ( !$row->load($tlnid) ) {
			JError::raiseWarning( 500, CLMText::errorText('PLAYER', 'NOTEXISTING') );
			return false;
		}
		$row->move($inc, '');
	
		$app =& JFactory::getApplication();
		$app->enqueueMessage( JText::_('ORDERING_CHANGED') );
		
		return true;
		
	}


	// Saves user reordering entry
	function saveOrder() {
	
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
	
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'CLMAccess.class.php');
		$clmAccess = new CLMAccess();
		$clmAccess->accesspoint = 'BE_tournament_edit_detail';
		if ($clmAccess->access() === false) {
		//if (CLM_usertype != 'admin' AND CLM_usertype != 'tl') {
			JError::raiseWarning(500, JText::_('TOURNAMENT_NO_ACCESS') );
			return false;
		}
	
		// alle enthaltenen IDs
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		$total		= count( $cid );
	
		// alle Order-Einträge
		$order		= JRequest::getVar( 'order', array(0), 'post', 'array' );
		JArrayHelper::toInteger($order, array(0));
	
		$row =& JTable::getInstance( 'turnier_teilnehmer', 'TableCLM' );
		
		$groupings = array();
	
		// update ordering values
		for( $i=0; $i < $total; $i++ ) {
			$row->load( (int) $cid[$i] );
			// track categories
			$groupings[] = $row->turnier;
	
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					JError::raiseError(500, $db->getErrorMsg() );
				}
			}
		}
		// execute update Order for each parent group
		$groupings = array_unique( $groupings );
		foreach ($groupings as $group){
			$row->reorder('turnier = '.(int) $group);
		}
		
		
		$app =& JFactory::getApplication();
		$app->enqueueMessage( JText::_('NEW_ORDERING_SAVED') );
	
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}


	function sortByTWZ() {
		$this->_sortBy('twz');
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	}
	
	function sortByRandom() {
		$this->_sortBy('random');
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	}
	
	function sortByOrdering() {
		$this->_sortBy('ordering');
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	}
	
	function _sortBy($by) {
		
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
	
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'CLMAccess.class.php');
		$clmAccess = new CLMAccess();
		$clmAccess->accesspoint = 'BE_tournament_edit_detail';
		if ($clmAccess->access() === false) {
		//if (CLM_usertype != 'admin' AND CLM_usertype != 'tl') {
			JError::raiseWarning(500, JText::_('TOURNAMENT_NO_ACCESS') );
			return false;
		}
	
		$tournament = new CLMTournament($this->id);
		$tournament->checkTournamentStarted();
		if ($tournament->started) {
			JError::raiseWarning( 500, JText::_( 'SORTING_NOT_POSSIBLE' ).": ".JText::_('RESULTS_ENTERED') );
			return false;
		}
	
		// Anzahl gemeldeter Spiele -> maximale Snr
		$query = "SELECT COUNT(id) FROM `#__clm_turniere_tlnr`"
			." WHERE turnier =".$this->id
			;
		$this->_db->setQuery($query);
		$maximum = $this->_db->loadResult();
	
		// alle Spieler in Reihenfolge laden
		if ($by == 'ordering') {
			$queryOrderBy = 'SELECT id FROM `#__clm_turniere_tlnr`'
								.' WHERE turnier = '.$this->id
								.' ORDER BY ordering ASC'
								;
			$stringMessage = JText::_('ORDERED_BY_ORDERING');
		} elseif ($by == 'twz') {
			$queryOrderBy = 'SELECT id FROM `#__clm_turniere_tlnr`'
								.' WHERE turnier = '.$this->id
								.' ORDER BY twz DESC'
								;
			$stringMessage = JText::_('ORDERED_BY_TWZ');
		} elseif ($by == 'random') {
			$queryOrderBy = 'SELECT id FROM `#__clm_turniere_tlnr`'
								.' WHERE turnier = '.$this->id
								.' ORDER BY RAND()'
								;
			$stringMessage = JText::_('ORDERED_BY_RANDOM');
		}
		$this->_db->setQuery($queryOrderBy);
		$players = $this->_db->loadObjectList();
	
		$table	=& JTable::getInstance( 'turnier_teilnehmer', 'TableCLM' );
		// Snr umsortieren
		$snr = 0;
		// alle Spieler durchgehen
		foreach ($players as $value) {
			$snr++;
			$table->load($value->id);
			$table->snr = $snr;
			$table->store();
		}
		
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = $stringMessage;
		$clmLog->params = array('sid' => $turnier->sid, 'tid' => $this->id, 'cids' => count($cid));
		$clmLog->write();
		
		$app =& JFactory::getApplication();
		$app->enqueueMessage( $stringMessage );
	
	}


	function setRanking() {
		$this->_setRankingDo();
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	}


	function _setRankingDo() {
		
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
	
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'CLMAccess.class.php');
		$clmAccess = new CLMAccess();
		$clmAccess->accesspoint = 'BE_tournament_edit_detail';
		if ($clmAccess->access() === false) {
		//if (CLM_usertype != 'admin' AND CLM_usertype != 'tl') {
			JError::raiseWarning(500, JText::_('TOURNAMENT_NO_ACCESS') );
			return false;
		}
	
		$tournament = new CLMTournament($this->id, true);
		$tournament->checkTournamentStarted();
		if (!$tournament->started) {
			JError::raiseWarning( 500, JText::_( 'RANKING_NOT_POSSIBLE' ).": ".JText::_('NO_RESULTS_ENTERED') );
			return false;
		} elseif ($tournament->data->typ == 3) {
			JError::raiseWarning( 500, JText::_( 'RANKING_NOT_POSSIBLE' ).": ".JText::_('MODUS_TYP_3') );
			return false;
		}
	
		$tournament->calculateRanking();
		$tournament->setRankingPositions();
	
		$stringMessage = JText::_('SET_RANKING_DONE');
	
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = $stringMessage;
		$clmLog->params = array('sid' => $tournament->data->sid, 'tid' => $this->id);
		$clmLog->write();
		
		$app =& JFactory::getApplication();
		$app->enqueueMessage( $stringMessage );
	
		return true;
	
	}



	function cancel() {
		
		$this->adminLink->view = "turmain";
		$this->adminLink->makeURL();
		
		$this->setRedirect( $this->adminLink->url );
		
	}
	function active() {

		$this->_activeDo();

		$this->adminLink->view = "turplayers";
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
		
	}

	function _activeDo() {

		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		$tlnrID = $cid[0];
	
		// Teilnehmerdaten holen
		$tlnr =& JTable::getInstance( 'turnier_teilnehmer', 'TableCLM' );
		$tlnr->load( $tlnrID ); // Daten zu dieser ID laden
		// Teilnehmer existent?
		if (!$tlnr->id) {
			JError::raiseWarning( 500, CLMText::errorText('PLAYER', 'NOTEXISTING') );
			return false;
		
		// Teilnehmer gehört zu Turnier?
		} elseif ($tlnr->turnier != $this->id) {
			JError::raiseWarning( 500, CLMText::errorText('PLAYER', 'NOACCESS') );
			return false;
	}

		$task		= JRequest::getCmd('task');
		$active	= ($task == 'active'); // zu vergebender Wert 0/1
		// jetzt schreiben
		$tlnr->tlnrStatus = $active;
		if (!$tlnr->store()) {
			JError::raiseError(500, $row->getError() );
			return false;
		}
	
		$app =& JFactory::getApplication();
		if ($active) {
			$app->enqueueMessage( $tlnr->name.": "." ".JText::_('PLAYER_ACTIVE') );
		} else {
			$app->enqueueMessage( $tlnr->name.": "." ".JText::_('PLAYER_DEACTIVE') );
		}
	
		// Log
		$clmLog = new CLMLog();
		$clmLog->aktion = JText::_('PLAYER')." ".$tlnr->name." (ID: ".$tlnrID."): ".$task;
		$clmLog->params = array('tid' => $this->id); // TurnierID wird als LigaID gespeichert
		$clmLog->write();
	
	
		return true;
	
	}

	// Prüft die DWZ und Elo der Teilnehmer gegen die aktuellen DSB-Daten mittels API-Schnittstelle
	function daten_dsb_API()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
	
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'CLMAccess.class.php');
		$clmAccess = new CLMAccess();
		$clmAccess->accesspoint = 'BE_tournament_edit_detail';
		if ($clmAccess->access() === false) {
			JError::raiseWarning(500, JText::_('TOURNAMENT_NO_ACCESS') );
			return false;
		}
		
		// Turnierdaten holen
		$turnier =& JTable::getInstance( 'turniere', 'TableCLM' );
		$turnier->load( $this->id ); // Daten zu dieser ID laden
		$turParams = new JParameter($turnier->params);
		$param_useastwz = $turParams->get('useAsTWZ', 0);

		$sid = $turnier->sid;
		// Teilnehmer auslesen
		$query = 'SELECT id FROM `#__clm_turniere_tlnr`'
					.' WHERE turnier = '.$this->id
					.' ORDER BY zps, mgl_nr DESC'
					;
		$this->_db->setQuery($query);
		$players = $this->_db->loadObjectList();
		$player	=& JTable::getInstance( 'turnier_teilnehmer', 'TableCLM' );
		
		// Initial-Werte setzen
		$vzps = '';
		$ct_update = 0;
		$cd_update = 0;
		$c_player = 0;
		// alle Teilnehmer durchgehen
		foreach ($players as $value_players) {
			$c_player++;
			$player->load($value_players->id);
			echo "<br>player: $c_player "; var_dump($player);
			// Spielerliste des Vereins
			if ($player->zps != $vzps) {
				// Daten als Array laden (Zeichensatz UTF-8!) vom DSB 
				$vzps = $player->zps;
				$dsbdaten = unserialize(file_get_contents("http://www.schachbund.de/php/dewis/verein.php?zps=".$vzps."&format=array"));		
			}
			// Abgleich der Mitgliedsnummer
			foreach($dsbdaten as $key => $value)
			{	
				$i_dsbmglnr = (integer) $value["mglnr"];
				if ($i_dsbmglnr != $player->mgl_nr) continue;
				// Array umbauen (nur relevante Spalten)    
				$dsbid = $value["id"];
				$dsbnachname = $value["nachname"];
				$dsbvorname = $value["vorname"];
				$dsbdwz = $value["dwz"];
				$dsbdwzindex = $value["dwzindex"];
				$dsbzps = $value["verein"];
				$dsbstatus = $value["status"];
				$dsbfideid = $value["fideid"];
				$dsbfideelo = $value["fideelo"];
				$dsbfidetitel = $value["fidetitel"];
				// Die Mitgliedsnummer müssen mindestens dreistellig sein, mit führenden Nullen auffüllen
				$dsbmglnr = $value["mglnr"];
				if (strlen ($dsbmglnr) == 1) {
					$dsbmglnr= "00" . $dsbmglnr;
				} elseif (strlen ($dsbmglnr) == 2) {
					$dsbmglnr= "0" . $dsbmglnr;
				}
				// Falls Namensänderungen anliegen (Heirat)
				$name = $dsbnachname.",".$dsbvorname;
				$name_g = strtoupper($name);
				$search = array("ä", "ö", "ü", "ß", "é");
				$replace = array("AE", "OE", "UE", "SS", "É");
				$name_g =  str_replace($search, $replace, $name_g);

				// Update Teilnehmer-Tabelle
				$player->name = $name;
				$player->twz = $this->_getTWZ($param_useastwz, $dsbdwz, $dsbfideelo);
				$player->NATrating 	= $dsbdwz;
				$player->FIDEelo	= $dsbfideelo;
				$player->FIDEid 	= $dsbfideid;
				$player->store();
				$ct_update++;

				// Update interne DWZ-Datenbank	
				// Prüfen ob Teilnehmer in interner DB ist
				$query	= "SELECT Mgl_Nr FROM #__clm_dwz_spieler "
					." WHERE ZPS ='$dsbzps '"
					." AND sid = '$sid'"
					." AND Mgl_Nr = '$dsbmglnr'"
					;
				$this->_db->setQuery($query);
				$mgl_exist = $this->_db->loadObjectList();
				if(!isset($mgl_exist[0])) break;
				$query	= "UPDATE #__clm_dwz_spieler "
					." SET DWZ = '$dsbdwz' "
					." , DWZ_Index = '$dsbdwzindex' "
					." , PKZ = '$dsbid' "
					." , Spielername = '$name' "
					." , Spielername_G = '$name_g' "
					." , FIDE_Elo = '$dsbfideelo' "
					." , FIDE_Titel = '$dsbfidetitel' "
					." , FIDE_ID = '$dsbfideid' "
					." , Status = '$dsbstatus' "
					." WHERE ZPS = '$dsbzps' "
					." AND sid = '$sid' "
					." AND Mgl_Nr = '$dsbmglnr' "
					;
				$this->_db->setQuery($query);
				$this->_db->query();
				if (mysql_errno() == 0) $cd_update++;
			}
		}
		
 	// Log schreiben
  	$clmLog = new CLMLog();
  	$clmLog->aktion = "DWZ-Update Teilnehmer";
  	$clmLog->params = array('sid' => $sid, 'lid' => $turnier->id, 'cids' => 'sp:'.$c_player.',tl:'.$ct_update.',db:'.$cd_update);
  	$clmLog->write();
  	
	$msg = JText::_( 'DB_MSG_DWZ_TOURNAMENT_UPDATE');
	
	$app =& JFactory::getApplication();
	$app->enqueueMessage( $msg );

	$this->adminLink->makeURL();
	$this->setRedirect( $this->adminLink->url );

	}

	// Prüft die DWZ und Elo der Teilnehmer gegen die aktuellen DSB-Daten mittels SOAP-Webservice
	function daten_dsb_SOAP()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
	
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'CLMAccess.class.php');
		$clmAccess = new CLMAccess();
		$clmAccess->accesspoint = 'BE_tournament_edit_detail';
		if ($clmAccess->access() === false) {
			JError::raiseWarning(500, JText::_('TOURNAMENT_NO_ACCESS') );
			return false;
		}
		
		// Turnierdaten holen
		$turnier =& JTable::getInstance( 'turniere', 'TableCLM' );
		$turnier->load( $this->id ); // Daten zu dieser ID laden
		$turParams = new JParameter($turnier->params);
		$param_useastwz = $turParams->get('useAsTWZ', 0);

		$sid = $turnier->sid;
		// Teilnehmer auslesen
		$query = 'SELECT id FROM `#__clm_turniere_tlnr`'
					.' WHERE turnier = '.$turnier->id
					.' ORDER BY zps, mgl_nr DESC'
					;
		$this->_db->setQuery($query);
		$players = $this->_db->loadObjectList();
		$player	=& JTable::getInstance( 'turnier_teilnehmer', 'TableCLM' );
		
		// Dewis Tabelle leeren
		$query = " DELETE FROM #__clm_dwz_dewis "
			." WHERE turnier = ".$turnier->id
			;
		$this->_db->setQuery($query);
		$this->_db->query();

		// Initial-Werte setzen
		$vzps = '';

	// SOAP Webservice
		try {
			$client = new SOAPClient( "https://dwz.svw.info/services/files/dewis.wsdl" );
			$c_player = 0;
			// alle Teilnehmer durchgehen
			foreach ($players as $value_players) {
				$c_player++;
				$player->load($value_players->id);
				// Spielerliste des Vereins
				if ($player->zps == '0' OR $player->zps == '') continue;
				if ($player->zps != $vzps) {
					$vzps = $player->zps;
					// VKZ des Vereins --> Vereinsliste
					$unionRatingList = $client->unionRatingList($vzps);
					$marray = $unionRatingList->members;
				}
				// Detaildaten zu Mitgliedern lesen
				foreach ($marray as $m) {
					if ($m->membership != $player->mgl_nr) continue;
					$tcard = $client->tournamentCardForId($m->pid);
					$query = " REPLACE INTO #__clm_dwz_dewis (`pkz`,`nachname`, `vorname`,`zps`,`mgl_nr`, `dwz` ,`dwz_index` ,`status` "
						." ,`geschlecht`,`geburtsjahr`,`fide_elo`,`fide_land`,`fide_id`,`turnier`) VALUES"
						." ('$m->pid','$m->surname','$m->firstname','$vzps','$m->membership','$m->rating','$m->ratingIndex','$m->state' "
						." ,'".$tcard->member->gender."' "
						." ,'".$tcard->member->yearOfBirth."' ,'".$tcard->member->elo."' "
						." ,'".$tcard->member->fideNation."' ,'".$tcard->member->idfide."' ,".$turnier->id
						." )"
						;
					$this->_db->setQuery($query);
					$this->_db->query();
					break;
				}
			}
		}
		catch (SOAPFault $f) {  print $f->faultstring;  }
	
		// Spieler aus der CLM DEWIS Tabelle holen
		$query = " SELECT a.* FROM #__clm_dwz_dewis as a"
			." WHERE turnier = ".$turnier->id
			;
		$this->_db->setQuery($query);
		$dsbdaten = $this->_db->loadObjectList();

		$ct_update = 0;
		$cd_update = 0;
		$c_player = 0;
		// alle Teilnehmer durchgehen
		foreach($dsbdaten as $value)
		{	$c_player++;
			// Array umbauen (nur relevante Spalten)    
			$dsbid = $value->pkz;
			$dsbnachname = $value->nachname;
			$dsbvorname = $value->vorname;
			$dsbdwz = $value->dwz;
			$dsbdwzindex = $value->dwz_index;
			$dsbzps = $value->zps;
			$dsbstatus = $value->status;
			$dsbgeschlecht = $value->geschlecht;
			$dsbgeburtsjahr = $value->geburtsjahr;
			$dsbfideid = $value->fide_id;
			$dsbfideelo = $value->fide_elo;
			$dsbfideland = $value->fide_land;
			// Die Mitgliedsnummer müssen mindestens dreistellig sein, mit führenden Nullen auffüllen
			$dsbmglnr = $value->mgl_nr;
			if (strlen ($dsbmglnr) == 1) {
				$dsbmglnr= "00" . $dsbmglnr;
			} elseif (strlen ($dsbmglnr) == 2) {
				$dsbmglnr= "0" . $dsbmglnr;
			}
			// Falls Namensänderungen anliegen (Heirat)
			$name = $dsbnachname.",".$dsbvorname;
			$name_g = strtoupper($name);
			$search = array("ä", "ö", "ü", "ß", "é");
			$replace = array("AE", "OE", "UE", "SS", "É");
			$name_g =  str_replace($search, $replace, $name_g);
			if ($dsbgeschlecht == 'm') $dsbgeschlecht = 'M';
			if ($dsbgeschlecht == 'f') $dsbgeschlecht = 'W';
			if ($dsbfideid == '' OR $dsbfideid == '0') $dsbfideland = '';

			// Update Teilnehmer-Tabelle
			$twz = $this->_getTWZ($param_useastwz, $dsbdwz, $dsbfideelo);
			$query	= "UPDATE #__clm_turniere_tlnr "
				." SET name = '$name' "
				." , geschlecht = '$dsbgeschlecht' "
				." , birthYear = '$dsbgeburtsjahr' "
				." , NATrating = '$dsbdwz' "
				." , FIDEelo = '$dsbfideelo' "
				." , FIDEcco = '$dsbfideland' "
				." , FIDEid = '$dsbfideid' "
				." , twz = '$twz' "
				." WHERE zps = '$dsbzps' "
				." AND sid = '$sid' "
				." AND mgl_nr = '$dsbmglnr' "
				." AND turnier = '$turnier->id' "
				;
			$this->_db->setQuery($query);
			$this->_db->query();
			if (mysql_errno() == 0) $ct_update++;

			// Update interne DWZ-Datenbank	
			// Prüfen ob Teilnehmer in interner DB ist
			$query	= "SELECT Mgl_Nr FROM #__clm_dwz_spieler "
				." WHERE ZPS ='$dsbzps '"
				." AND sid = '$sid'"
				." AND Mgl_Nr = '$dsbmglnr'"
				;
			$this->_db->setQuery($query);
			$mgl_exist = $this->_db->loadObjectList();
			if(!isset($mgl_exist[0])) continue;
			if ($dsbdwz != '0')
				$query	= "UPDATE #__clm_dwz_spieler "
					." SET DWZ = '$dsbdwz' "
					." , DWZ_Index = '$dsbdwzindex' "
					." , PKZ = '$dsbid' "
					." , Spielername = '$name' "
					." , Spielername_G = '$name_g' "
					." , FIDE_Elo = '$dsbfideelo' "
					." , FIDE_Land = '$dsbfideland' "
					." , FIDE_ID = '$dsbfideid' "
					." , Status = '$dsbstatus' "
					." WHERE ZPS = '$dsbzps' "
					." AND sid = '$sid' "
					." AND Mgl_Nr = '$dsbmglnr' "
					;
			else
				$query	= "UPDATE #__clm_dwz_spieler "
					." SET DWZ = NULL "
					." , DWZ_Index = NULL "
					." , PKZ = '$dsbid' "
					." , Spielername = '$name' "
					." , Spielername_G = '$name_g' "
					." , FIDE_Elo = '$dsbfideelo' "
					." , FIDE_Land = '$dsbfideland' "
					." , FIDE_ID = '$dsbfideid' "
					." , Status = '$dsbstatus' "
					." WHERE ZPS = '$dsbzps' "
					." AND sid = '$sid' "
					." AND Mgl_Nr = '$dsbmglnr' "
					;
			$this->_db->setQuery($query);
			$this->_db->query();
			if (mysql_errno() == 0) $cd_update++;
		}
		
 	// Log schreiben
  	$clmLog = new CLMLog();
  	$clmLog->aktion = "DWZ-Update Teilnehmer";
  	$clmLog->params = array('sid' => $sid, 'tid' => $turnier->id, 'cids' => 'sp:'.$c_player.',tl:'.$ct_update.',db:'.$cd_update);
  	$clmLog->write();
  	
	$msg = JText::_( 'DB_MSG_DWZ_TOURNAMENT_UPDATE');
	
	$app =& JFactory::getApplication();
	$app->enqueueMessage( $msg );

	$this->adminLink->makeURL();
	$this->setRedirect( $this->adminLink->url );

	}

	// TWZ aus Parameter des Turniers, NWZ und ELO ermitteln
	function _getTWZ ($param = 0, $natrating = 0, $fideelo = 0) {	
		$twz = 0;
		if ($param == 0) {
			$twz = max(array($natrating, $fideelo));
		} elseif ($param == 1) {
			$twz = $natrating;
			if ($twz == 0) {
				$twz = $fideelo;
			}
		} else {
			$twz = $fideelo;
			if ($twz == 0) {
				$twz = $natrating;
			}
		}
		return $twz;
	}

}