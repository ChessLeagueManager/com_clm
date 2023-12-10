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

class CLMControllerPGNImport extends JControllerLegacy
{
	function __construct() {		
		$this->app =JFactory::getApplication();
		parent::__construct();		
	}
	
	function display($cachable = false, $urlparams = array()) { 
		$_REQUEST['view'] = 'pgnimport';
		parent::display(); 
	} 
	
	function import() {		
		$liga = clm_core::$load->request_string('liga', '');
		$pgn_file = clm_core::$load->request_string('pgn_file', '');
		$adminLink = new AdminLink ();
		$adminLink->view = 'pgnimport';
		$adminLink->more = array('liga' => $liga, 'pgn_file' => $pgn_file);
		$adminLink->makeURL ();		
		if ($liga == '') {	
			$msg = JText::_( 'PGN_CHOOSE_LEAGUE_MSG' );
			$this->app->enqueueMessage( $msg );
			$this->app->redirect($adminLink->url);
		} else { 
			$msg = $this->getDataPGN();
			$this->app->enqueueMessage( $msg );

			parent::display(); 		
		}
	}

	function getDataPGN () {
		
		//CLM parameter auslesen
		$config = clm_core::$db->config();
		$countryversion = $config->countryversion;
		$test_button = $config->test_button;
		jimport( 'joomla.filesystem.file' );
		
		$liga = clm_core::$load->request_string('liga', '');
		$liga_arr = explode('.', $liga, 2);
		$tkz = $liga_arr[0];
		$tid = $liga_arr[1];
		
		// Namen und Verzeichnis der SWT-Datei auslesen
		$filename = clm_core::$load->request_string('pgn_file', '');
if ($test_button > 0) {		
		echo "<br><br>Start pgn-Import <br>Dateiname: $filename";  
}
		if ($filename == '') {
			$msg = JText::_( 'PGN_FILE_NO' );
			return $msg;
		}
		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'swt' . DIRECTORY_SEPARATOR;
		
		$pgn = $path.$filename;
		$game = array();
		$pgn_arr = array();
		$total = 0; $pgn_error = 0;
		$ii = 0; $jj = 0; $ij = 0; $ib = 0; 
		$pgn_data = file_get_contents($pgn);
		if ($pgn_data === false) { 
			$msg = JText::_( 'PGN_FILE_ERROR' );
			return $msg;
		}
		if (mb_detect_encoding($pgn_data, 'UTF-8', true) === false) {
			$pgn_data = clm_core::$load->utf8encode($pgn_data);
		}
		$length = strlen($pgn_data);
		for ($ii = 0; $ii < $length; $ii++) {
			if ($ii < $ij) continue;
			if (substr($pgn_data, $ii, 1) == '[' AND substr($pgn_data, $ii+1, 1) != '%') {
				$jj = strpos(substr($pgn_data, ($ii + 1)), ']');
				if ($jj === false) { echo "<br>Fehler]"; die(); }
				$game_par = substr($pgn_data, $ii+1, $jj);
				$game_arr = explode(' ', $game_par, 2);
				if (isset($game[$game_arr[0]])) { 
					$game['tkz'] = $tkz;
					$game['tid'] = $tid;
					$game['text'] = substr($pgn_data, $ib, ($ii - $ib - 1));
					$total++;
					$return_arr = $this->dbgame($game);
					if ($return_arr['error'] != '') $pgn_error++;
					$game = array();
					$ib = $ii;
				}
				$game[$game_arr[0]] = substr($game_arr[1], 1, (strlen($game_arr[1]) - 2));
				$ij = $ii + $jj;
			}
		}
		if (count($game) > 0) {
					$game['tkz'] = $tkz;
					$game['tid'] = $tid;
					$game['text'] = substr($pgn_data, $ib, ($ii - $ib - 1));
					$total++;
					$return_arr = $this->dbgame($game);
					if ($return_arr['error'] != '') $pgn_error++;
		}

		$msg = $total.' '.JText::_( 'PGN_IMPORT_TOTAL' ).'<br>'
			.($total - $pgn_error).' '.JText::_( 'PGN_IMPORT_ALLOCATED' ).'<br>';
		if ($pgn_error == 0) 
			$msg .= JText::_( 'PGN_IMPORT_CLOSE' ).'<br>'; 
		else
			$msg .= $pgn_error.' '.JText::_( 'PGN_IMPORT_OPEN' );
if ($test_button > 0) {		
		echo "<br><br>Ende pgn-Import <br><br><br>"; 
}
	
		return $msg;
		
	}

	// Test und Speichern einer einzelnen pgn-Notation
	function dbgame($game) {
		
		//CLM parameter auslesen
		$config = clm_core::$db->config();
		$countryversion = $config->countryversion;
		$test_button = $config->test_button;
if ($test_button > 0) {		
		echo "<br><br>start dbgame <br> "; var_dump($game); 
}
		set_time_limit(10);
		$error_text = '';
		$tkz = $game['tkz']; 
		$tid = $game['tid'];  
		$return_arr = array();
		$return_arr['text'] = str_replace("'","\'",$game['text']);
		if (isset($game['pgnnr']) AND is_numeric($game['pgnnr']) AND $game['pgnnr'] > 0) $pgnnr = $game['pgnnr']; else $pgnnr = 0;
		if (isset($game['sid']) AND is_numeric($game['sid']) AND $game['sid'] > 0) $sid = $game['sid']; else $sid = 0;
if ($test_button > 0) {		
		echo "<br><br>pgnnr "; var_dump($pgnnr); 
		echo "<br><br>sid "; var_dump($sid); 
}
		//Field Check
		if (!isset($game['White'])) {  
				$error_text = JText::_( 'PGN_E_NAME_WHITE' ); 
				$game['White'] = ''; }
		if (!isset($game['Black'])) { 
				$error_text = JText::_( 'PGN_E_NAME_BLACK' );
				$game['Black'] = ''; }

		// Aufbereitung der Namen
		if ($pgnnr > 0) {
			$query =  ' SELECT * FROM #__clm_player_decode '
					.' WHERE sid = '.$sid
					.' AND source = "pgn_import"'
					.' AND oname = "'.$game['White'].'"';
			$sw_name = clm_core::$db->loadObject($query);
			if (!is_null($sw_name)) $game['White'] = $sw_name->nname;
			$query =  ' SELECT * FROM #__clm_player_decode '
					.' WHERE sid = '.$sid
					.' AND source = "pgn_import"'
					.' AND oname = "'.$game['Black'].'"';
			$bw_name = clm_core::$db->loadObject($query);
			if (!is_null($bw_name)) $game['Black'] = $bw_name->nname;

		} 
		$white_name = clm_core::$load->sub_umlaute($game['White']);
		$black_name = clm_core::$load->sub_umlaute($game['Black']);
		$white_name = str_replace(' ','',$white_name);
		$black_name = str_replace(' ','',$black_name);
		$white_name = str_replace('-','',$white_name);
		$black_name = str_replace('-','',$black_name);
if ($test_button > 0) {		
		echo "<br>white_name: $white_name "; 
		echo "<br>black_name: $black_name "; 
}
		// Ermittlung Runde/Brett
		$dg = 0;
		$paar = 0;
		$runde = 0;
		$brett = 0;
		if (isset($game['Round']) AND $game['Round'] > '' AND is_numeric($game['Round'])) {
			$rr = strpos($game['Round'], '.', 0);
			if ($rr === false)  { 
				$runde = $game['Round'];  
			} else {
				$runde_arr = explode('.', $game['Round'], 2);
				$runde = $runde_arr[0];  
				$brett = $runde_arr[1];  
			}
		}
		if (isset($game['Board']) AND $game['Board'] > '' AND is_numeric($game['Board'])) {
			$brett = $game['Board'];
		}
if ($test_button > 0) {		
		echo "<br>Runde: $runde  Brett: $brett  DG: $dg "; 
		echo "<br>tkz: $tkz  tid: $tid "; 
}
		// Liste der möglichen Paarungen entspr. Runde und Brett
		if ($error_text == '' AND isset($tkz) AND ($tkz == 't' OR $tkz == 's') AND isset($tid) AND $tid > '0' ) {
			if ($tkz == 't') {
			$query = 'SELECT a.*, d.Spielername, e.Spielername as gSpielername  FROM #__clm_rnd_spl as a';
			if ($countryversion =="de") {
				$query .= " LEFT JOIN #__clm_dwz_spieler as d ON ( d.ZPS = a.zps AND d.Mgl_Nr= a.spieler AND d.sid = a.sid) ";
				$query .= " LEFT JOIN #__clm_dwz_spieler as e ON ( e.ZPS = a.gzps AND e.Mgl_Nr= a.gegner AND e.sid = a.sid) ";
			} else {
				$query .= " LEFT JOIN #__clm_dwz_spieler as d ON ( d.ZPS = a.zps AND d.PKZ= a.PKZ AND d.sid = a.sid) ";
				$query .= " LEFT JOIN #__clm_dwz_spieler as e ON ( e.ZPS = a.gzps AND e.PKZ= a.gPKZ AND e.sid = a.sid) ";
			}
				$query .= ' WHERE lid = '.$tid;
			} else { 
				$query = 'SELECT a.*, d.name as Spielername, e.name as gSpielername  FROM #__clm_turniere_rnd_spl as a'
					." LEFT JOIN #__clm_turniere_tlnr as d ON ( d.turnier = a.turnier AND d.snr= a.spieler) "
					." LEFT JOIN #__clm_turniere_tlnr as e ON ( e.turnier = a.turnier AND e.snr= a.gegner) "
					.' WHERE a.turnier = '.$tid;
			}
			if ($dg > 0) $query .= ' AND a.dg = '.$dg;
			if ($runde > 0) $query .= ' AND a.runde = '.$runde;
			if ($brett > 0) $query .= ' AND a.brett = '.$brett;
			$gameslist	= clm_core::$db->loadObjectList($query);
			if (count($gameslist) < 2) { 
				$error_text = JText::_( 'PGN_E_NO_ROUND' );
			}
		} else { $gameslist = array(); }
if ($test_button > 0) {		
		echo "<br>query: "; var_dump($query); 
		echo "<br>Mögl. Paarungen: "; var_dump($gameslist); 
		echo "<br>Anz. Mögl. Paarungen: "; var_dump(count($gameslist)); 
}
		
		$gameslist_name = array();
		if (count($gameslist) > 2) { 
			foreach($gameslist as $gl) {
				$spieler_name = clm_core::$load->sub_umlaute($gl->Spielername);
				$gegner_name = clm_core::$load->sub_umlaute($gl->gSpielername);
				$spieler_name = str_replace(' ','',$spieler_name);
				$gegner_name = str_replace(' ','',$gegner_name);
				$spieler_name = str_replace('-','',$spieler_name);
				$gegner_name = str_replace('-','',$gegner_name);
if ($test_button > 0) {		
		echo "<br>gl: "; var_dump($gl); 
		echo "<br>white_name: $white_name "; 
		echo "<br>black_name: $black_name "; 
		echo "<br>spieler_name: $spieler_name ";
		echo "<br>gegner_name: $gegner_name ";
}
				if ($tkz == 't') { 
					if ($gl->weiss == 1) {												
						if ($white_name == $spieler_name AND $black_name == $gegner_name)
							$gameslist_name[] = $gl;
					} else {
						if ($white_name == $gegner_name AND $black_name == $spieler_name)
							$gameslist_name[] = $gl;
					}
				} else {
					if ($gl->heim == 1) {												
						if ($white_name == $spieler_name AND $black_name == $gegner_name)
							$gameslist_name[] = $gl;
					} else {
						if ($white_name == $gegner_name AND $black_name == $spieler_name)
							$gameslist_name[] = $gl;
					}
				}
			}
			if (count($gameslist_name) < 2) { 
				$error_text = JText::_( 'PGN_E_NO_NAME' );
			}
			if (count($gameslist_name) > 2) { 
				$error_text = JText::_( 'PGN_E_MANY_NAMES' );
			}
		} else {
			$gameslist_name	= $gameslist;
		}
if ($test_button > 0) {		
		echo "<br>Mögl. Namen: "; var_dump($gameslist_name); 
		echo "<br>Anz. Mögl. Namen: "; var_dump(count($gameslist_name)); 
		echo "<br>pgnnr: $pgnnr  Bisherige Fehler: "; var_dump($error_text); 
}
		$return_arr['error'] = $error_text;  
		if ($error_text == '') {
			foreach($gameslist_name as $gln) {
				if ($tkz == 't') $paar = $gln->paar; else $paar = 0;
				//Löschen eventuell bereits existierender Notationen zu Turnier/DG/Runde/Paar/Brett
				$query = "DELETE FROM #__clm_pgn "
						.' WHERE tkz = "'.$tkz.'"'
						.' AND tid = '.$tid
						.' AND dg = '.$gln->dg
						.' AND runde = '.$gln->runde
						.' AND paar = '.$paar
						.' AND brett = '.$gln->brett;
				clm_core::$db->query($query);
				$anzdel = clm_core::$db->affected_rows();
if ($test_button > 0) {		
		echo "<br>Delete Query: "; var_dump($query); 
		echo "<br>Gelöschte Records: $anzdel "; 
		echo "<br>pgnnr: $pgnnr "; 
}
				if ($pgnnr == 0) {	// Import einer pgn-Datei
					$query = "REPLACE INTO #__clm_pgn "
						." ( `tkz`, `tid`, `dg`, `runde`, `paar`, `brett`, `text`, `error` ) "
						." VALUES ('".$tkz."',".$tid.",".$gln->dg.",".$gln->runde.",".$paar.",".$gln->brett.",'".$return_arr['text']."','".$return_arr['error']."' )";
					clm_core::$db->query($query);
					$pgnnr = clm_core::$db->insert_id();
					if ($pgnnr == 0) {
						$query = "SELECT id FROM #__clm_pgn "
							.' WHERE tkz = "'.$tkz.'"'
							.' AND tid = '.$tid
							.' AND dg = '.$gln->dg
							.' AND runde = '.$gln->runde
							.' AND paar = '.$paar
							.' AND brett = '.$gln->brett;
						$pgnnr_arr	= clm_core::$db->loadObjectList($query);
						$pgnnr = $pgnnr_arr[0]->id;
					}
				} else {			// Bearbeiten offener Notationen
					$query = "UPDATE #__clm_pgn "
						." SET  dg = ".$gln->dg." , runde = ".$gln->runde." , paar = ".$paar." , brett = ".$gln->brett." , text = '".$return_arr['text']."', error = '".$return_arr['error']."' "
						." WHERE id = ".$pgnnr; 
if ($test_button > 0) {		
		echo "<br>Update Query: "; echo substr($query,0,500); echo "<br>".substr($query,500);
}
					$rc = clm_core::$db->query($query);
					$anzupd = clm_core::$db->affected_rows();
if ($test_button > 0) {		
		echo "<br>RC: "; var_dump($rc);
		echo "<br>Geupdatete Records: $anzupd "; 
}
				}	
				if ($tkz == 't') {
				  $query = 'UPDATE #__clm_rnd_spl '
					.' SET pgnnr = '.$pgnnr
					.' WHERE lid = '.$tid
					.' AND dg = '.$gln->dg
					.' AND runde = '.$gln->runde
					.' AND paar = '.$gln->paar
					.' AND brett = '.$gln->brett;
				} elseif ($tkz == 's') {
				  $query = 'UPDATE #__clm_turniere_rnd_spl '
					.' SET pgn = '.$pgnnr
					.' WHERE turnier = '.$tid
					.' AND dg = '.$gln->dg
					.' AND runde = '.$gln->runde
					.' AND brett = '.$gln->brett;
				}
				clm_core::$db->query($query);
				break;
			}
		} else {
			if ($pgnnr == 0) {	// Import einer pgn-Datei
				$query = "SELECT count(*) as anz FROM #__clm_pgn "
							.' WHERE tkz = "'.$tkz.'"'
							.' AND tid = '.$tid;
				$count_arr	= clm_core::$db->loadObjectList($query);
				$count_id = $count_arr[0]->anz;
				$query = "INSERT IGNORE INTO #__clm_pgn "
					." ( `tkz`, `tid`, `dg`, `runde`, `paar`, `brett`, `text`, `error` ) "
					." VALUES ('".$tkz."',".$tid.",0,0,0,".$count_id.",'".$return_arr['text']."','".$return_arr['error']."' )";
				clm_core::$db->query($query);
				$pgnnr = clm_core::$db->insert_id();
			} else {
			}
		}
		$return_arr['pgnnr'] = $pgnnr;
		$query = "SELECT * FROM #__clm_pgn "
			.' WHERE id = '.$pgnnr;
		$pgnnr_arr	= clm_core::$db->loadObjectList($query);
		$return_arr['tkz'] = $pgnnr_arr[0]->tkz;
		$return_arr['tid'] = $pgnnr_arr[0]->tid;
		$return_arr['dg'] = $pgnnr_arr[0]->dg;
		$return_arr['runde'] = $pgnnr_arr[0]->runde;
		if ($tkz == 't') { $return_arr['paar'] = $pgnnr_arr[0]->paar; }
		else $return_arr['paar'] = 0;
		$return_arr['brett'] = $pgnnr_arr[0]->brett;

		return $return_arr;
	}
	
	function using_ntable() {		
		$app =JFactory::getApplication();
		$liga = clm_core::$load->request_string('liga', '');
		$pgn_file = clm_core::$load->request_string('pgn_file', '');
		$adminLink = new AdminLink ();
		$adminLink->view = 'pgnimport';
		$adminLink->more = array('liga' => $liga, 'pgn_file' => $pgn_file);
		$adminLink->makeURL ();		
		if ($liga == '') {	
			$msg = JText::_( 'PGN_CHOOSE_LEAGUE_MSG' );
			$app->enqueueMessage( $msg );
			$app->redirect($adminLink->url);
		} else { 
			$msg = $this->getTablePGN();
			$app->enqueueMessage( $msg );
			$app->redirect($adminLink->url);
		}
	}

	function getTablePGN () {
		
		//CLM parameter auslesen
		$config = clm_core::$db->config();
		$countryversion = $config->countryversion;
		$test_button = $config->test_button;
		jimport( 'joomla.filesystem.file' );
		
		$liga = clm_core::$load->request_string('liga', '');
		$liga_arr = explode('.', $liga, 2);
		$tkz = $liga_arr[0];
		$tid = $liga_arr[1];
		if ($tkz == 't') { 		// Teamwettbewerb
			$query = "SELECT * FROM #__clm_liga "
					.' WHERE id = '.$tid;
		} elseif ($tkz == 's') { 		// Teamwettbewerb
			$query = "SELECT * FROM #__clm_turniere "
					.' WHERE id = '.$tid;
		}
		$turnier	= clm_core::$db->loadObjectList($query);
		if (isset($turnier[0]->sid)) $sid = $turnier[0]->sid; else $sid = 0;
if ($test_button > 0) {		
		echo "<br><br>Turnier: "; var_dump($turnier); 
		echo "<br><br>Start Offene gegen Tabelle <br> Turnier: "; var_dump($liga); 
		echo "<br><br>Saison-ID: "; var_dump($sid); 
}
		
		// offene Notationen auslesen
		$query = "SELECT * FROM #__clm_pgn "
			.' WHERE tkz = "'.$tkz.'"'
			.' AND tid = '.$tid
			.' AND runde = 0 ';
		$gameslist	= clm_core::$db->loadObjectList($query);
		$anz = clm_core::$db->affected_rows();
if ($test_button > 0) {		
		echo "<br><br>Select-Query: "; var_dump($query); 
		echo "<br><br>Offene Notationen: "; var_dump($gameslist); 
		echo "<br><br>Anzahl: "; var_dump($anz);
}
 		// Aufbereitung der Daten
		$total = 0; $pgn_error = 0;
		foreach ($gameslist as $gl) {
			$game = array();
			$pgn_arr = array();
			$ii = 0; $jj = 0; $ij = 0; $ib = 0; 
			$pgn_data = $gl->text;
			$length = strlen($pgn_data);
			for ($ii = 0; $ii < $length; $ii++) {
			if ($ii < $ij) continue;
			if (substr($pgn_data, $ii, 1) == '[' AND substr($pgn_data, $ii+1, 1) != '%') {
				$jj = strpos(substr($pgn_data, ($ii + 1)), ']');
				if ($jj === false) { echo "<br>Fehler]"; die(); }
				$game_par = substr($pgn_data, $ii+1, $jj);
				$game_arr = explode(' ', $game_par, 2);
				$game[$game_arr[0]] = substr($game_arr[1], 1, (strlen($game_arr[1]) - 2));
				$ij = $ii + $jj;
			}
			}
			$game['pgnnr'] = $gl->id;
			$total++;
			$game['tkz'] = $tkz;
			$game['tid'] = $tid;
			$game['sid'] = $sid;
			$game['text'] = $gl->text;
if ($test_button > 0) {		
		echo "<br><br>Notation: "; var_dump($game);
}
			$return_arr = $this->dbgame($game);
			if ($return_arr['error'] != '') $pgn_error++;
if ($test_button > 0) {		
		echo "<br><br>Return Notation: "; var_dump($return_arr); 
}
				$ij = $ii + $jj;
			} 
		$msg = $total.' '.JText::_( 'PGN_IMPORT_TOTAL' ).'<br>'
			.($total - $pgn_error).' '.JText::_( 'PGN_IMPORT_ALLOCATED' ).'<br>';
		if ($pgn_error == 0) 
			$msg .= JText::_( 'PGN_IMPORT_CLOSE' ).'<br>'; 
		else
			$msg .= $pgn_error.' '.JText::_( 'PGN_IMPORT_OPEN' );
if ($test_button > 0) {		
		echo "<br><br>Ende Update gegen Tabelle <br> $msg"; 
		die(); 
}
	
		return $msg;
		
	}

	function maintain() {		
		$liga = clm_core::$load->request_string('liga', '');
		$pgn_file = clm_core::$load->request_string('pgn_file', '');
		$adminLink = new AdminLink ();
		$adminLink->view = 'pgnimport';
		$adminLink->more = array('liga' => $liga, 'pgn_file' => $pgn_file);
		$adminLink->makeURL ();		
		if ($liga == '') {	
			$msg = JText::_( 'PGN_CHOOSE_LEAGUE_MSG' );
			$this->app->enqueueMessage( $msg );
			$this->app->redirect($adminLink->url);
		} else { 
			$adminLink = new AdminLink ();
			$adminLink->view = 'pgndata';
			$adminLink->more = array('liga' => $liga, 'pgn_file' => $pgn_file);
			$adminLink->makeURL ();		
			$this->app->redirect($adminLink->url);
		}
	}
	
	function delete_all() {		
		$liga = clm_core::$load->request_string('liga', '');
		$pgn_file = clm_core::$load->request_string('pgn_file', '');
		$adminLink = new AdminLink ();
		$adminLink->view = 'pgnimport';
		$adminLink->more = array('liga' => $liga, 'pgn_file' => $pgn_file);
		$adminLink->makeURL ();		
		if ($liga == '') {	
			$msg = JText::_( 'PGN_CHOOSE_LEAGUE_MSG' );
			$this->app->enqueueMessage( $msg );
			$this->app->redirect($adminLink->url);
		} else { 
			$pgn_del = $this->getDelPGN(true);
			$msg = $pgn_del.' '.JText::_( 'PGN_DELETE_TOTAL' );
			$this->app->enqueueMessage( $msg );
			$this->app->redirect($adminLink->url);
		}
	}

	function delete_open() {		
		$liga = clm_core::$load->request_string('liga', '');
		$pgn_file = clm_core::$load->request_string('pgn_file', '');
		$adminLink = new AdminLink ();
		$adminLink->view = 'pgnimport';
		$adminLink->more = array('liga' => $liga, 'pgn_file' => $pgn_file);
		$adminLink->makeURL ();		
		if ($liga == '') {	
			$msg = JText::_( 'PGN_CHOOSE_LEAGUE_MSG' );
			$this->app->enqueueMessage( $msg );
			$this->app->redirect($adminLink->url);
		} else {
			$pgn_del = $this->getDelPGN(false);
			$msg = $pgn_del.' '.JText::_( 'PGN_DELETE_TOTAL' );
			$this->app->enqueueMessage( $msg );
			$this->app->redirect($adminLink->url);
		}
	}

	function getDelPGN ($all = false) {
		
		$liga = clm_core::$load->request_string('liga', '');
		$liga_arr = explode('.', $liga, 2);
		$tkz = $liga_arr[0];
		$tid = $liga_arr[1];
		
		$query = "DELETE FROM #__clm_pgn "
			.' WHERE tkz = "'.$tkz.'"'
			.' AND tid = '.$tid;
		if ($all == false) 
			$query .= ' AND runde = 0 ';
		clm_core::$db->query($query);
		$total = clm_core::$db->affected_rows();		

		if ($all == true) {
		  if ($tkz == 't') { 		// Teamwettbewerb
			$query = 'UPDATE #__clm_rnd_spl '
				.' SET pgnnr = 0 '
				.' WHERE lid = '.$tid;
		  } elseif ($tkz == 's') { 		
			$query = 'UPDATE #__clm_turniere_rnd_spl '
				.' SET pgn = "0" '
				.' WHERE turnier = '.$tid;
		  } 
		  clm_core::$db->query($query);
		  $total_spl = clm_core::$db->affected_rows();		
		}
		return $total;
	}	

	function maintain_ntable() {		
		$liga = clm_core::$load->request_string('liga', '');
		$pgn_file = clm_core::$load->request_string('pgn_file', '');
		$adminLink = new AdminLink ();
		$adminLink->view = 'pgnimport';
		$adminLink->more = array('liga' => $liga, 'pgn_file' => $pgn_file);
		$adminLink->makeURL ();		
		if ($liga == '') {	
			$msg = JText::_( 'PGN_CHOOSE_LEAGUE_MSG' );
			$this->app->enqueueMessage( $msg );
			$this->app->redirect($adminLink->url);
		} else { 
			$adminLink = new AdminLink ();
			$adminLink->view = 'pgnntable';
			$adminLink->more = array('liga' => $liga, 'pgn_file' => $pgn_file);
			$adminLink->makeURL ();		
			$this->app->redirect($adminLink->url);
		}
	}

	function cancel() {		
		$liga = clm_core::$load->request_string('liga', '');
		$pgn_file = clm_core::$load->request_string('pgn_file', '');

		$adminLink = new AdminLink ();
		$adminLink->view = 'swt';
		$adminLink->more = array('liga' => $liga, 'pgn_file' => $pgn_file);
		$adminLink->makeURL ();		
		$msg = JText::_( 'SWT_CANCEL_MSG' );
		$this->app->enqueueMessage( $msg );
		$this->app->redirect($adminLink->url);
	}

	function pgn_upload() {
		$model = $this->getModel('pgnimport');
		$msg = $model->pgn_upload();
		$pgn_file = clm_core::$load->request_string('pgn_file', '');
		$liga = clm_core::$load->request_string('liga', '');
		
		$adminLink = new AdminLink();
		$adminLink->view = "pgnimport";
		$adminLink->more = array('pgn_file' => $pgn_file, 'liga' => $liga);
		$adminLink->makeURL();
			
		$this->app->enqueueMessage( $msg );
		$this->app->redirect($adminLink->url);
	}
	
	function pgn_delete(){
		$model = $this->getModel('pgnimport');
		$msg = $model->pgn_delete();
		$liga = clm_core::$load->request_string('liga', '');
		
		$adminLink = new AdminLink();
		$adminLink->view = "pgnimport";
//		$adminLink->more = array('pgn_file' => $pgn_file, 'liga' => $liga);
		$adminLink->makeURL();
			
		$this->app->enqueueMessage( $msg );
		$this->app->redirect($adminLink->url); 		
	}
	
	
}

?>
