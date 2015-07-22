<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 Thomas Schwietert & Andreas Dorn. All rights reserved
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

class CLMControllerTurMain extends JController {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		$this->_db		= & JFactory::getDBO();
		
		// Register Extra tasks
		$this->registerTask( 'apply','save' );
		$this->registerTask( 'unpublish','publish' );
	
		$this->adminLink = new AdminLink();
		$this->adminLink->view = "turmain";
	
	}

	
	// Weiterleitung!
	function catmain() {
		
		$this->adminLink->view = "catmain";
		$this->adminLink->makeURL();
		
		$this->setRedirect( $this->adminLink->url );
	
	}
	
	
	// Weiterleitung!
	function add() {
		
		$this->adminLink->view = "turform";
		$this->adminLink->makeURL();
		
		$this->setRedirect( $this->adminLink->url );
	
	}

	
	/**
	* Container für kopieren
	*
	*/
	function copy() {

		$this->_copyDo();

		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );

	}

	/**
	* eigentliche copy-Funktion
	*
	*/
	function _copyDo() {
		
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
		
		
		// zu bearbeitende IDs auslesen
		$cid		= JRequest::getVar( 'cid', null, 'post', 'array' );
		JArrayHelper::toInteger($cid);
		// vorerst nur eine ID bearbeiten!
		$turnierid = $cid[0];
		
		// access?
		$tournament = new CLMTournament($turnierid, true);
		if (!$tournament->checkAccess()) {
			JError::raiseWarning( 500, CLMText::errorText('JTOOLBAR_DUPLICATE', 'NOACCESS') );
			return false;
		}
		
		// Turnierdaten holen
		$row =& JTable::getInstance( 'turniere', 'TableCLM' );

		if ( !$row->load($turnierid) ) {
			JError::raiseWarning( 500, CLMText::errorText('TOURNAMENT', 'NOTEXISTING') );
			return false;
		}
		
		// alten Namen zwischenspeichern für Message und Log
		$nameOld = $row->name;
		
		// Daten für Kopie anpassen
		$row->id				= 0; // neue id wird von DB vergeben
		$row->name			= JText::_('COPY_OF').' '.$row->name;
		$row->rnd			= NULL;
		$row->published	= 0;


		if (!$row->store()) {	
			JError::raiseWarning( $row->getError() );
			return false; 
		}
		
	
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = JText::_('TOURNAMENT_COPIED').": ".$nameOld;
		$clmLog->params = array('sid' => $row->sid, 'tid' => $turnierid); // TurnierID wird als LigaID gespeichert
		$clmLog->write();
		
		// Message
		$app =& JFactory::getApplication();
		$app->enqueueMessage( $nameOld.": ".JText::_('TOURNAMENT_COPIED') );

		// Ende Runden erstellt
		return true;
	
	}


	// Weiterleitung!
	function edit() {
		
		$cid	= JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		
		// access?
		$tournament = new CLMTournament($turnierid, true);
		if (!$tournament->checkAccess(0,0,$row->tl)) {
			JError::raiseWarning( 500, JText::_('TOURNAMENT_NO_ACCESS') );
			//$this->adminLink = new AdminLink();
			$this->adminLink->view = "turmain";
			$this->adminLink->makeURL();
			$this->setRedirect( $this->adminLink->url );

		} else {

		$this->adminLink->view = "turform";
		$this->adminLink->more = array('task' => 'edit', 'id' => $cid[0]);
		$this->adminLink->makeURL();
		
		$this->setRedirect( $this->adminLink->url );
		}
	}


	function publish() {
		
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
	
		// TODO? evtl global inconstruct anlegen
		$user 		=& JFactory::getUser();
		
		$cid		= JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		
		$task		= JRequest::getCmd( 'task' );
		$publish	= ($task == 'publish'); // zu vergebender Wert 0/1
		
		// Inhalte übergeben?
		if (empty( $cid )) { 
			JError::raiseWarning( 500, 'NO_ITEM_SELECTED' );
		} else { // ja, Inhalte vorhanden
			require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'CLMAccess.class.php');
			$clmAccess = new CLMAccess();
			
			// erst jetzt alle Einträge durchgehen
			foreach ($cid as $key => $value) {
		
				// load the row from the db table
				$row =& JTable::getInstance( 'turniere', 'TableCLM' );
				$row->load( $value ); // Daten zu dieser ID laden
		
				// Prüfen ob User Berechtigung für dieses Turnier hat
				if (CLM_usertype != 'admin' AND CLM_usertype != 'tl') {
					$clmAccess->accesspoint = 'BE_tournament_edit_detail';
					if($clmAccess->access() === false) {	
						JError::raiseWarning( 500, $row->name.": ".JText::_( 'TOURNAMENT_NO_ACCESS' ) );					
						// daher diesen Eintrag aus dem cid-Array löschen
						unset($cid[$key]); 
					} elseif ($row->tl !== CLM_ID AND $clmAccess->access() !== true) {
						JError::raiseWarning( 500, $row->name.": ".JText::_( 'TOURNAMENT_NO_ACCESS' ) );		
						// daher diesen Eintrag aus dem cid-Array löschen
						unset($cid[$key]);
					} else {
						// Berechtigung vorhanden
						
						// Änderung nötig?
						if ($row->published != $publish) {
							// Log
							$clmLog = new CLMLog();
							$clmLog->aktion = JText::_('TOURNAMENT')." ".$row->name.": ".$task;
							$clmLog->params = array('sid' => $row->sid, 'tid' => $value); // TurnierID wird als LigaID gespeichert
							$clmLog->write();
							// Log geschrieben - Änderungen später
						} else {
							unset($cid[$key]);
						}
					}
				}		
			} 
			// alle Einträge geprüft
		
			// immer noch Einträge vorhanden?
			if ( !empty($cid) ) { 
		
				$row =& JTable::getInstance( 'turniere', 'TableCLM' );
				$row->publish( $cid, $publish );
			
				// Meldung erstellen
				$app =& JFactory::getApplication();
				if ($publish) {
					$app->enqueueMessage( CLMText::sgpl(count($cid), JText::_('TOURNAMENT'), JText::_('TOURNAMENTS'))." ".JText::_('CLM_PUBLISHED') );
				} else {
					$app->enqueueMessage( CLMText::sgpl(count($cid), JText::_('TOURNAMENT'), JText::_('TOURNAMENTS'))." ".JText::_('CLM_UNPUBLISHED') );
				}
			
			} else {
			
				$app =& JFactory::getApplication();
				$app->enqueueMessage(JText::_('NO_CHANGES'));
			
			}
	
		}
	
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}

	/**
	* Container für Erstellung der Runden
	*
	*/
	function createRounds() {

		$this->_createRoundsDo();

		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );

	}


	/**
	* Erstellung der Runden
	*
	*/
	function _createRoundsDo() {
		
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
		
		// zu bearbeitende IDs auslesen
		$cid	= JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		
		// vorerst nur eine ID bearbeiten!
		$turnierid = $cid[0];
		
		// Turnierdaten holen
		$row =& JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load( $turnierid ); // Daten zu dieser ID laden

		// Turnier existent?
		if (!$row->id) {
			JError::raiseWarning( 500, CLMText::errorText('TOURNAMENT', 'NOTEXISTING') );
			return false;
		}
		
		// access?
		$tournament = new CLMTournament($turnierid, true);
		if (!$tournament->checkAccess(0,0,$row->tl)) {
			JError::raiseWarning( 500, JText::_('TOURNAMENT_NO_ACCESS') );
			return false;
		}

		// Modus implementiert?
		if ($row->typ <= 0 OR $row->typ == 4 OR $row->typ > 6) {
			JError::raiseWarning( 500, CLMText::errorText('MODUS', 'NOTIMPLEMENTED') );
			return false;		
		// Runden bereits erstellt?
		} elseif ($row->rnd != NULL) {
			JError::raiseWarning( 500, CLMText::errorText('ROUNDS', 'ALREADYCREATED') );
			return false;		
		}
		
		// INIT der Turnierdaten
		$sid 			= $row->sid;
		$dg 			= $row->dg;
		if ($row->typ == 3) {
			// Rundenanzahl errechnen
			$runden = ceil(log($row->teil)/log(2));
		} elseif ($row->typ == 5) {
			// Rundenanzahl errechnen
			$runden = ceil(log($row->teil)/log(2)) + 1;
		} else {
			$runden		= $row->runden;
		}
		if ($row->typ == 2) {
			$publish = $row->published; // für Runden-Termine
		}
		// Startdatum gegeben UND (kein ODER gleiches Enddatum) - also Eintagesturnier!
		if ($row->dateStart != '0000-00-00' AND ($row->dateEnd == '0000-00-00' OR $row->dateEnd == $row->dateStart)) {
			$datum = $row->dateStart;
		} else {
			$datum = '0000-00-00';
		}

		// Anzahl Spieler
		$n = $row->teil;
		// Anzahl gerade machen
		if ($n%2 != 0) { 
			$n++;
		}
		
		// Anzahl Partien pro Runde
		$gameCount = $n/2;
		
		// Container für weitere Meldungen
		$messageReserve = array();

		///////////////////////
		// KO-System         //
		///////////////////////
		if ($row->typ == 3 or $row->typ == 5) {

			// array für alle DB-Einträge
			$sqlValuesStrings = array();
			
			// Rundenzähler
			$roundCount  = 0;
			if ($row->typ == 5) { $runden--; }

			// alle Runden durchgehen - werden umgekehrt benannt -> 3 = VF, 2 = HF, 1 = Finale  ?warum? Änderung mit 1.4.3
			//for ($r=$runden; $r>=1; $r--) {			
			for ($r=1; $r<=$runden; $r++) {
				// Anzahl Matches
				//if ($r == $runden) { // Auftaktunde
				if ($r == 1) { // Auftaktunde
					$matchCount = ( $row->teil - (pow(2, $runden)/2) );
				} else {
					//$matchCount = (pow(2, $r)/2);
					$matchCount = (pow(2, ($runden - $r + 1))/2);
				}		
				// alle Matches durchgehen
				for ($m=1; $m<=$matchCount; $m++) {
					$sqlValuesStrings[] = "('$sid','$turnierid','$r','$m','1','1')";
					$sqlValuesStrings[] = "('$sid','$turnierid','$r','$m','1','0')";
				}		
			}

			// kleines Finale einbauen
			if ($row->typ == 5) {
				$runden++; 
				$sqlValuesStrings[] = "('$sid','$turnierid','$runden','1','1','1')";
				$sqlValuesStrings[] = "('$sid','$turnierid','$runden','1','1','0')";
			}
			// alle Partien eintragen
			$query	= "INSERT INTO #__clm_turniere_rnd_spl"
						. " (`sid`, `turnier`, `runde`, `brett`, `dg`, `heim`)"
						. " VALUES "
						.implode(", ", $sqlValuesStrings)
						;
			$this->_db->setQuery($query);
			if (!$this->_db->query()) { 
				JError::raiseError(500, $this->_db->getErrorMsg() ); 
			}
			
		///////////////////////
		// Vollrunden System //
		///////////////////////
		} elseif ($row->typ == 2) {
			
			// array für alle DB-Einträge
			$sqlValuesStrings = array();
			
			// Rundenzähler
			$roundCount  = 0;
			
			// Schleife über Durchgänge
			for ($i = 1; $i < 1+$dg; $i++) {
				
				// INIT
				$y = 1; // ?
				
				// Heimrecht
				// (in Einzelturnieren canceln? - nein, wird zur Unterscheidung der benötigten (gedoppelten) datensätze genutzt)
				if ($i %2 != 0) {
					$dgh = 1;
					$dgg = 0;
				} else {
					$dgh = 0;
					$dgg = 1;
				}
				
				
				// Runde 1
				// Rundenzähler aktuell
				$roundCount++;
				$round = $roundCount;
				while ($round > $runden) { $round -= $runden; }

				for ($f=1; $f<=$gameCount; $f++) {
					$heim = $f;
					$gast = $n-$f+1;
					//$sqlValuesStrings[] = "('$sid','$turnierid','$roundCount','$f','$i','$dgh','$heim','$heim','$gast')";
					//$sqlValuesStrings[] = "('$sid','$turnierid','$roundCount','$f','$i','$dgg','$gast','$gast','$heim' )";
					$sqlValuesStrings[] = "('$sid','$turnierid','$round','$f','$i','$dgh','$heim','$heim','$gast')";
					$sqlValuesStrings[] = "('$sid','$turnierid','$round','$f','$i','$dgg','$gast','$gast','$heim' )";
				}
				// Ende Runde 1
	
				// RUnden 2 bis x
				for ($p = 2; $p < $n; $p++ ) {
					
					// Rundenzähler aktuell
					$roundCount++;
					$round = $roundCount;
					while ($round > $runden) { $round -= $runden; }

					
					// Paarungsschleife
					if ($p%2 != 0) { 
						$gerade = 0; $y++; 
					} else { 
						$gerade = 1; 
					}
					///////////////
					// 1.Element //
					///////////////
					if ( $gerade == 0 ) {
						$heim = $y;
						$gast = $n;
					} else {
						$heim = $n;
						$gast = ($n/2)+$y;
					}
					$sqlValuesStrings[] = "('$sid','$turnierid','$round','1','$i','$dgh','$heim','$heim','$gast')";
					$sqlValuesStrings[] = "('$sid','$turnierid','$round','1','$i','$dgg','$gast','$gast','$heim')";
	
					///////////////////
					// ab 2. Element //
					///////////////////
					
					// ungerade Runde
					if ( $gerade == 0 ) {
						for ($z = 2; $z < ($y+1); $z++) {
							$heim = $z+$y-1;
							$gast = $p-$z-$y+2;
							$sqlValuesStrings[] = "('$sid','$turnierid','$round','$z','$i','$dgh','$heim','$heim','$gast')";
							$sqlValuesStrings[] = "('$sid','$turnierid','$round','$z','$i','$dgg','$gast','$gast','$heim')";
						}
						for ($z = ($y+1); $z < (($n/2)+1); $z++) {
							$heim = $z+$y-1;
							$gast = $n+$p-$z-$y+1;
							$sqlValuesStrings[] = "('$sid','$turnierid','$round','$z','$i','$dgh','$heim','$heim','$gast')";
							$sqlValuesStrings[] = "('$sid','$turnierid','$round','$z','$i','$dgg','$gast','$gast','$heim')";
						}
					
					} else { // gerade Runde //
		
						for ($z = 2; $z < (($n/2)-$y+1); $z++) {
							$heim = ($n/2)+$y+$z-1;
							$gast = ($n/2)+$y-$z+1;
							$sqlValuesStrings[] = "('$sid','$turnierid','$round','$z','$i','$dgh','$heim','$heim','$gast')";
							$sqlValuesStrings[] = "('$sid','$turnierid','$round','$z','$i','$dgg','$gast','$gast','$heim')";
						}
						for ($z = (($n/2)-$y+1); $z < ($n/2)+1; $z++) {
							$heim = $p-($n/2)-$y+$z;
							$gast = ($n/2)+$y-$z+1;
							$sqlValuesStrings[] = "('$sid','$turnierid','$round','$z','$i','$dgh','$heim','$heim','$gast')";
							$sqlValuesStrings[] = "('$sid','$turnierid','$round','$z','$i','$dgg','$gast','$gast','$heim')";
						}
					}
					// Ende gerade/ungerade Runde
				}
				// Ende Runden 2 bis x
				
			}
			// Ende Durchgänge
		
			// alle Partien eintragen
			$query	= "INSERT INTO #__clm_turniere_rnd_spl"
						. " (`sid`, `turnier`, `runde`, `brett`, `dg`, `heim`, `tln_nr`, `spieler`, `gegner`)"
						. " VALUES "
						.implode(", ", $sqlValuesStrings)
						;
			$this->_db->setQuery($query);
			if (!$this->_db->query()) { 
				JError::raiseError(500, $this->_db->getErrorMsg() ); 
			}
		
			$messageReserve[] = JText::_('MODUS_TYP_2')." - ".JText::_('ROUNDS_ALL')." - ".JText::_('TOURNAMENT_MATCHES_ASSIGNED');
		
		///////////////
		// CH System //
		///////////////
		} elseif ($row->typ == 1) {
			
			for ($dg_dg = 1; $dg_dg < 1+$dg; $dg_dg++) {
				$y = 1;
				if ($dg_dg %2 != 0) {
					$dgh = 1;
					$dgg = 0;
				} else {
					$dgh = 0;
					$dgg = 1;
				}
				// Runde 1
				if ($row->teil%2 == 0) { // ohne 'spielfrei'
					for ($f=1; $f<=$gameCount; $f++) {
						if ($f%2 == 0) {
							$gast = $f;
							$heim = $n/2+$f; // $n-$f+1;
						} else {
							$heim = $f;
							$gast = $n/2+$f; // $n-$f+1;
						}
						$query	= "INSERT INTO #__clm_turniere_rnd_spl "
						." ( `sid`, `turnier`, `runde`, `brett`, `dg`, `heim`, `tln_nr`, `spieler`, `gegner` ) "
						." VALUES ('$sid','$turnierid','1','$f','$dg_dg','$dgh','$heim','$heim','$gast') "
						." ,('$sid','$turnierid','1','$f','$dg_dg','$dgg','$gast','$gast','$heim' )"
						;
						$this->_db->setQuery($query);
						if (!$this->_db->query()) { 
							JError::raiseError(500, $this->_db->getErrorMsg() ); 
						}
					}
					$messageReserve[] = JText::_('MODUS_TYP_1')." - ".JText::_('ROUND_CH_1')." - ".JText::_('TOURNAMENT_MATCHES_ASSIGNED');
				
				} else { // mit 'spielfrei' - letzter Spieler bekommt das Freilos
				
					// ein Match weniger in der Schleife!
					for ($f=1; $f<=$gameCount-1; $f++) {
						if ($f%2 == 0) {
							$gast = $f;
							$heim = ($n-2)/2+$f; // $n-$f+1;
						} else {
							$heim = $f;
							$gast = ($n-2)/2+$f; // $n-$f+1;
						}
						$query	= "INSERT INTO #__clm_turniere_rnd_spl "
						." ( `sid`, `turnier`, `runde`, `brett`, `dg`, `heim`, `tln_nr`, `spieler`, `gegner` ) "
						." VALUES ('$sid','$turnierid','1','$f','$dg_dg','$dgh','$heim','$heim','$gast') "
						." ,('$sid','$turnierid','1','$f','$dg_dg','$dgg','$gast','$gast','$heim' )"
						;
						$this->_db->setQuery($query);
						if (!$this->_db->query()) { 
							JError::raiseError(500, $this->_db->getErrorMsg() ); 
						}
					}
					$messageReserve[] = JText::_('MODUS_TYP_1')." - ".JText::_('ROUND_CH_1')." - ".JText::_('TOURNAMENT_MATCHES_ASSIGNED');
					// letztes Match: Freilos
					$gast = $n;
					$heim = ($n-1);
					$query	= "INSERT INTO #__clm_turniere_rnd_spl "
					." ( `sid`, `turnier`, `runde`, `brett`, `dg`, `heim`, `tln_nr`, `spieler`, `gegner` ,`ergebnis`) "
					." VALUES ('$sid','$turnierid','1','$f','$dg_dg','$dgh','$heim','$heim','$gast', '5') "
					." ,('$sid','$turnierid','1','$f','$dg_dg','$dgg','$gast','$gast','$heim', '4')"
					;
					$this->_db->setQuery($query);
					if (!$this->_db->query()) { 
						JError::raiseError(500, $this->_db->getErrorMsg() ); 
					}
					$messageReserve[] = JText::_('ROUND_CH_1')." - ".JText::_('MATCH_BYE_CREATED');
				
				
				}
				// Ende Runde 1

				// Ab 2. Runde
				for ($p = 2; $p <= $runden; $p++ ) { // Bugfix: <= statt < 
					$sqlValuesStrings = array();
					for ($f=1; $f<=$gameCount; $f++) {
						$sqlValuesStrings[] = "('$sid','$turnierid','$p','$f','$dg_dg','$dgh')";
						$sqlValuesStrings[] = "('$sid','$turnierid','$p','$f','$dg_dg','$dgg')";
					}
					// alle Runden abspeichern
					$query	= "INSERT INTO #__clm_turniere_rnd_spl"
					." ( `sid`, `turnier`, `runde`, `brett`,`dg`, `heim`)"
					." VALUES "
					.implode(", ", $sqlValuesStrings);
					$this->_db->setQuery($query);
					if (!$this->_db->query()) { 
						JError::raiseError(500, $this->_db->getErrorMsg() ); 
					}
				
				}
			}
		
		///////////////////
		// freies System //
		///////////////////
		} elseif ($row->typ == 6) {
		
			// Anzahl Spieler
			$n = $row->teil;
			// Anzahl gerade machen
			if ($n%2 != 0) { 
				$n++;
			}
			// angesetzte Partien pro Runde
			$gameCount = $n/2;
		
		
			// alle Runden
			for ($p = 1; $p <= $runden; $p++ ) { 
				$sqlValuesStrings = array();
				for ($f=1; $f<=$gameCount; $f++) {
					$sqlValuesStrings[] = "('$sid','$turnierid','$p','$f','1','1')";
					$sqlValuesStrings[] = "('$sid','$turnierid','$p','$f','1','0')";
				}
				// alle Runden abspeichern
				$query	= "INSERT INTO #__clm_turniere_rnd_spl"
				." ( `sid`, `turnier`, `runde`, `brett`,`dg`, `heim`)"
				." VALUES "
				.implode(", ", $sqlValuesStrings);
				$this->_db->setQuery($query);
				if (!$this->_db->query()) { 
					JError::raiseError(500, $this->_db->getErrorMsg() ); 
				}
			
			}
		
		}
		// Ende Typ/mpdus


		// Runden-Termine anlegen
		$sqlValuesStrings = array();
		for ($y=1; $y< 1+$dg; $y++) { // dg
			
			for ($x=1; $x< 1+$runden; $x++) {

				$nr	= $x; // + ($y-1)*$runden;
				if ($row->typ != 3 AND $row->typ != 5) {
					$name	= JText::_('ROUND')." ".$x;
					if ($dg == 2 AND $y == 1) $name .= " (".JText::_('TOURNAMENT_STAGE_1').")";
					elseif ($dg == 2 AND $y == 2) $name .= " (".JText::_('TOURNAMENT_STAGE_2').")";
					elseif ($dg > 2) $name .= " (".JText::_('DG')." ".$y.")";
				} elseif ($row->typ == 3) {
					//$name	= JText::_('ROUND_KO_'.$nr);
					$name	= JText::_('ROUND_KO_'.($runden - $nr +1));
				} elseif ($row->typ == 5) {
					//$name	= JText::_('ROUND_KO_'.$nr);
					$name	= JText::_('ROUND_KO_'.($runden - $nr));
				}
			
				$sqlValuesStrings[] = "('$sid', '$name', '$datum', '$turnierid', '$y', '$nr', '0', '0', '$publish')";
			}
		}
		// alle abspeichern
		$query	= "INSERT INTO #__clm_turniere_rnd_termine"
		." (`sid`, `name`, `datum`, `turnier`, `dg`, `nr`, `abgeschlossen`, `tl_ok`, `published`)"
		." VALUES "
		.implode(", ", $sqlValuesStrings);
		$this->_db->setQuery($query);
		if (!$this->_db->query()) { 
			JError::raiseError(500, $this->_db->getErrorMsg() ); 
		}


		// Rundenbyte setzen
		$query	= "UPDATE #__clm_turniere "
			." SET rnd = '1' "
			." WHERE id = $turnierid "
			;
		$this->_db->setQuery($query);
		$this->_db->query();
	
	
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = JText::_('ROUNDS_CREATED');
		$clmLog->params = array('sid' => $row->sid, 'tid' => $turnierid); // TurnierID wird als LigaID gespeichert
		$clmLog->write();
		
		// Message
		$app =& JFactory::getApplication();
		$app->enqueueMessage( $row->name.": ".JText::_('ROUNDS_CREATED') );
		if (count($messageReserve) > 0) {
			foreach ($messageReserve as $value) {
				$app->enqueueMessage( $value );
			}
		}

		// Ende Runden erstellt
		return true;

	}
	
	
	/**
	* Container für Löschung der Runden
	*
	*/
	function deleteRounds() {

		$this->_deleteRoundsDo();

		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );

	}
	
	/**
	* eigentliche Runden-Lösch-Funktion
	*
	*/
	
	function _deleteRoundsDo() {
	
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
		
		// zu bearbeitende IDs auslesen
		$cid	= JRequest::getVar( 'cid', null, 'post', 'array' );
		JArrayHelper::toInteger($cid);
		
		// vorerst nur eine ID bearbeiten!
		$turnierid = $cid[0];
		
		// Turnierdaten holen
		$row =& JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load( $turnierid ); // Daten zu dieser ID laden

		// Turnier existent?
		if (!$row->id) {
			JError::raiseWarning( 500, CLMText::errorText('TOURNAMENT', 'NOTEXISTING') );
			return false;
		}
		
		// access?
		$tournament = new CLMTournament($turnierid, true);
		if (!$tournament->checkAccess(0,0,$row->tl)) {
			JError::raiseWarning( 500, JText::_('TOURNAMENT_NO_ACCESS') );
			return false;
		}
		
		
		// Turnierdaten holen
		$row =& JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load( $turnierid ); // Daten zu dieser ID laden

		// Turnier existent?
		if (!$row->id) {
			JError::raiseWarning( 500, CLMText::errorText('TOURNAMENT', 'NOTEXISTING') );
			return false;
		
		// Runden bereits erstellt?
		} elseif ($row->rnd != 1) {
			JError::raiseWarning( 500, CLMText::errorText('ROUNDS', 'NOTEXISTING') );
			return false;
		
		}
	
	
	
		// Daten löschen
		$query = "DELETE FROM #__clm_turniere_rnd_spl "
			." WHERE turnier = ".$turnierid
			;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) { 
			JError::raiseError(500, $this->_db->getErrorMsg() ); 
		}
	
		$query = "DELETE FROM #__clm_turniere_rnd_termine "
			." WHERE turnier = ".$turnierid
			;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) { 
			JError::raiseError(500, $this->_db->getErrorMsg() ); 
		}
	
		// Rundenbyte setzen
		$query = "UPDATE #__clm_turniere "
			." SET rnd = NULL "
			." WHERE id = $turnierid "
			;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) { 
			JError::raiseError(500, $this->_db->getErrorMsg() ); 
		}
	
		// Alle Punktsummen löschen !
		$query = " UPDATE #__clm_turniere_tlnr "
			." SET sum_punkte = NULL "
			." , sum_sobe = NULL, sum_bhlz = NULL, anz_spiele = '0' "
			." , koStatus = '1', koRound = '0' "
			." WHERE turnier = ".$turnierid
			;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) { 
			JError::raiseError(500, $this->_db->getErrorMsg() ); 
		}
	
	
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = JText::_('ROUNDS_DELETED');
		$clmLog->params = array('sid' => $row->sid, 'tid' => $turnierid); // TurnierID wird als LigaID gespeichert
		$clmLog->write();
		
		// Message
		$app =& JFactory::getApplication();
		$app->enqueueMessage( $row->name.": ".JText::_('ROUNDS_DELETED') );

		// Ende Runden erstellt
		return true;
	
	}
	
	
	/**
	* Container für Löschung
	*
	*/
	function delete() {

		$this->_deleteDo();

		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );

	}
	
	
	/**
	* eigentliche Lösch-Funktion
	*
	*/
	function _deleteDo() {
		
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
	
		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		// vorerst nur ein markiertes Turnier übernehmen // später über foreach mehrere?
		$turnierid = $cid[0];
		
		
		// access?
		$tournament = new CLMTournament($turnierid, true);
		if (!$tournament->checkAccess()) {
			JError::raiseWarning( 500, JText::_('TOURNAMENT_NO_ACCESS') );
			return false;
		}
		
		
		// turnierdaten laden
		$row =& JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load( $turnierid );
		
		// falls Turnier existent?
		if ( !$row->load( $turnierid ) ) {
			JError::raiseWarning( 500, CLMText::errorText('TOURNAMENT', 'NOTEXISTING') );
			return false;
		
		// Runden vorhanden?
		} elseif ($row->rnd == 1) {
			JError::raiseWarning( 500, JText::_('FIRST_DELETE_ROUNDS') );
			return false;
		
		}
		
	
		// turnier löschen
		$query = " DELETE FROM #__clm_turniere "
			." WHERE id = ".$turnierid
			;
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) {
			JError::raiseError(500, $this->_db->getErrorMsg() ); 
		}
	
		// Runden wurden zuvor gelöscht
	
		// tlnr löschen
		$query	= "DELETE FROM #__clm_turniere_tlnr "
			." WHERE turnier = ".$turnierid
			;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) { 
			JError::raiseError(500, $this->_db->getErrorMsg() ); 
		}
		
		// sonderranglisten löschen
		$query	= "DELETE FROM #__clm_turniere_sonderranglisten "
			." WHERE turnier = ".$turnierid
			;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) { 
			JError::raiseError(500, $this->_db->getErrorMsg() ); 
		}
	
		
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = JText::_('TOURNAMENT_DELETED');
		$clmLog->params = array('sid' => $row->sid, 'tid' => $turnierid); // TurnierID wird als LigaID gespeichert
		$clmLog->write();
		
		
		// Message
		$app =& JFactory::getApplication();
		$app->enqueueMessage( $row->name.": ".JText::_('TOURNAMENT_DELETED') );
		
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
	
		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		$turnierid = $cid[0];
	
		// access?
		$tournament = new CLMTournament($turnierid, true);
		if (!$tournament->checkAccess()) {
			JError::raiseWarning( 500, JText::_('TOURNAMENT_NO_ACCESS') );
			return false;
		}
	
		$row =& JTable::getInstance( 'turniere', 'TableCLM' );
		if ( !$row->load( $turnierid ) ) {
			JError::raiseWarning( 500, CLMText::errorText('TOURNAMENT', 'NOTEXISTING') );
			return false;
		}
		$row->move( $inc, '' );
	
		$app =& JFactory::getApplication();
		$app->enqueueMessage( $row->name.": ".JText::_('ORDERING_CHANGED') );
		
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
			JError::raiseWarning( 500, JText::_('SECTION_NO_ACCESS') );
			return false;
		}
	
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
	
		$total		= count( $cid );
		$order		= JRequest::getVar( 'order', array(0), 'post', 'array' );
		JArrayHelper::toInteger($order, array(0));
	
		$row =& JTable::getInstance( 'turniere', 'TableCLM' );
		$groupings = array();
	
		// update ordering values
		for( $i=0; $i < $total; $i++ ) {
			$row->load( (int) $cid[$i] );
			// track categories
			$groupings[] = $row->saison;
	
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					JError::raiseError(500, $db->getErrorMsg() );
				}
			}
		}
		// execute updateOrder for each parent group
		$groupings = array_unique( $groupings );
		foreach ($groupings as $group){
			$row->reorder('saison = '.(int) $group);
		}
		
		$app =& JFactory::getApplication();
		$app->enqueueMessage( JText::_('NEW_ORDERING_SAVED') );
	
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}
	
	//Sonderranglisten
	function showSpecialrankings() {	
		$cid		= JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		
		if (!empty( $cid )) {
			$this->adminLink->more = array("filter_turnier" => $cid[0]);
		}
		
		$this->adminLink->view = "sonderranglistenmain";
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	}

}
