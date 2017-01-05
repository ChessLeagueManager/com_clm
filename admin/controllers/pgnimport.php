<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
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
		parent::__construct();		
	}
	
	function display($cachable = false, $urlparams = array()) { 
		JRequest::setVar('view','pgnimport');
		parent::display(); 
	} 
	
	function import() {		
		$adminLink = new AdminLink ();
		$adminLink->view = 'pgnimport';
		$adminLink->makeURL ();		
		$liga = JRequest::getVar('liga', '', 'default', 'string');
//echo "<br>ci-html-pgnimport: liga $liga "; var_dump($liga); //die();
		if ($liga == '') {	
			$msg = JText::_( 'PGN_CHOOSE_LEAGUE_MSG' );
			$this->setRedirect($adminLink->url, $msg);
		} else { 
			$msg = $this->getDataPGN();
//echo "<br>ci-html-pgnimport: msg $msg "; var_dump($msg); //die();
			//$msg = $pgn_del.' '.JText::_( 'PGN_DELETE_TOTAL' );
			$this->setRedirect($adminLink->url, $msg);
		}
	}

	function getDataPGN () {
		
		jimport( 'joomla.filesystem.file' );
		
		$liga = JRequest::getVar('liga', '', 'default', 'string');
		$liga_arr = explode('.', $liga, 2);
		$tkz = $liga_arr[0];
		$tid = $liga_arr[1];
//echo "<br>cip-liga: $liga  tid: $tid  tkz: $tkz"; //die();
		
		// Namen und Verzeichnis der SWT-Datei auslesen
		$filename = JRequest::getVar ('pgn_file', '', 'default', 'string');
//echo "<br>cip-filename:"; var_dump($filename); //die();
		if ($filename == '') {
			$msg = JText::_( 'PGN_FILE_NO' );
			return $msg;
		}
		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'swt' . DIRECTORY_SEPARATOR;
		
		$pgn = $path.$filename;
//echo "<br>cip-pgn:"; var_dump($pgn); //die();
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
			$pgn_data = utf8_encode($pgn_data);
		}
		$length = strlen($pgn_data);
//echo "<br>length: $length ";
		for ($ii = 0; $ii < $length; $ii++) {
//echo "<br>ii: $ii  jj: $jj ";
			if ($ii < $ij) continue;
			if (substr($pgn_data, $ii, 1) == '[' AND substr($pgn_data, $ii+1, 1) != '%') {
				$jj = strpos(substr($pgn_data, ($ii + 1)), ']');
//echo "<br>2ii: $ii  jj: $jj ";
				if ($jj === false) { echo "<br>Fehler]"; die(); }
				$game_par = substr($pgn_data, $ii+1, $jj);
//echo "<br>game_par: ";	var_dump($game_par);
				$game_arr = explode(' ', $game_par, 2);
//echo "<br>game_arr: ";	var_dump($game_arr); 
				//if (isset($game[$game_arr[0]]) AND substr($game_arr[0],0,1) != '%') { 
				if (isset($game[$game_arr[0]])) { 
					$game['tkz'] = $tkz;
					$game['tid'] = $tid;
					$game['text'] = substr($pgn_data, $ib, ($ii - $ib - 1));
					$total++;
//echo "<br><br>NewGame $total: "; var_dump($game); 
					$return_arr = $this->dbgame($game);
					if ($return_arr['error'] != '') $pgn_error++;
					//$pgn_arr[] = $return_arr;
//echo "<br>return:"; var_dump($return_arr);
					$game = array();
					$ib = $ii;
					//die(); 
				}
				//if (substr($game_arr[0],0,1) != '%') $game[$game_arr[0]] = substr($game_arr[1], 1, (strlen($game_arr[1]) - 2));
				$game[$game_arr[0]] = substr($game_arr[1], 1, (strlen($game_arr[1]) - 2));
//echo "<br>game1: ";	var_dump($game);
				$ij = $ii + $jj;
//die();
			}
		}
		if (count($game) > 0) {
					$game['tkz'] = $tkz;
					$game['tid'] = $tid;
					$game['text'] = substr($pgn_data, $ib, ($ii - $ib - 1));
					$total++;
//echo "<br><br>NewGame $total: "; var_dump($game); 
					$return_arr = $this->dbgame($game);
					if ($return_arr['error'] != '') $pgn_error++;
					//$pgn_arr[] = $return_arr;
//echo "<br>return:"; var_dump($return_arr);
		}
//echo "<br>game: ";	var_dump($game);
//echo "<br>return:"; var_dump($pgn_arr);
//echo "<br><br>Ende"; //die();	
		$msg = $total.' '.JText::_( 'PGN_IMPORT_TOTAL' ).'<br>'
			.($total - $pgn_error).' '.JText::_( 'PGN_IMPORT_ALLOCATED' ).'<br>';
		if ($pgn_error == 0) 
			$msg .= JText::_( 'PGN_IMPORT_CLOSE' ).'<br>'; 
		else
			$msg .= $pgn_error.' '.JText::_( 'PGN_IMPORT_OPEN' );
	
		return $msg;
		
	}

	// Test und Speichern einer einzelnen pgn-Notation
	function dbgame($game) {
//echo "<br><br>dbgame : "; var_dump($game); 
		set_time_limit(10);		
		$error_text = '';
		$tkz = $game['tkz']; 
		$tid = $game['tid'];  
		$return_arr = array();
		$return_arr['text'] = str_replace("'","\'",$game['text']);

		//Field Check
		if (!isset($game['White'])) {  
				$error_text = JText::_( 'PGN_E_NAME_WHITE' ); 
				$game['White'] = ''; }
// echo "<br><br>dbgame : "; var_dump($game); die(); 
		if (!isset($game['Black'])) { 
				$error_text = JText::_( 'PGN_E_NAME_BLACK' );
				$game['Black'] = ''; }

		$white_name = clm_core::$load->sub_umlaute($game['White']);
		$black_name = clm_core::$load->sub_umlaute($game['Black']);
		$white_name = str_replace(' ','',$white_name);
		$black_name = str_replace(' ','',$black_name);
		$white_name = str_replace('-','',$white_name);
		$black_name = str_replace('-','',$black_name);
//echo "<br>white_name: $white_name  black_name $black_name";

		//CLM parameter auslesen
		$config = clm_core::$db->config();
		$countryversion = $config->countryversion;

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
//echo "<br><br>dg: $dg  paar $paar  runde $runde  brett $brett "; //die();
		
		$gameslist_name = array();
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
//echo "<br><br>query: $query "; //die();
			$gameslist	= clm_core::$db->loadObjectList($query);
//echo "<br>md-dbgane-gameslist:"; var_dump($gameslist); //die();
			if (count($gameslist) < 2) { 
				$error_text = JText::_( 'PGN_E_NO_ROUND' );
//echo "<br>Fehler gl1"; //die();
			}
		} else { $gameslist = array(); }
		
		$gameslist_name = array();
		if (count($gameslist) > 2) { 
			foreach($gameslist as $gl) {
				$spieler_name = clm_core::$load->sub_umlaute($gl->Spielername);
				$gegner_name = clm_core::$load->sub_umlaute($gl->gSpielername);
				$spieler_name = str_replace(' ','',$spieler_name);
				$gegner_name = str_replace(' ','',$gegner_name);
				$spieler_name = str_replace('-','',$spieler_name);
				$gegner_name = str_replace('-','',$gegner_name);
//echo "<br>spieler_name: $spieler_name  gegner_name $gegner_name";
				if ($gl->weiss == 1) {												
					if ($white_name == $spieler_name AND $black_name == $gegner_name)
						$gameslist_name[] = $gl;
				} else {
					if ($white_name == $gegner_name AND $black_name == $spieler_name)
						$gameslist_name[] = $gl;
				}
			}
			if (count($gameslist_name) < 2) { 
				$error_text = JText::_( 'PGN_E_NO_NAME' );
//echo "<br>keine Daten Fehler gl_n"; //die();
			}
			if (count($gameslist_name) > 2) { 
				$error_text = JText::_( 'PGN_E_MANY_NAMES' );
//echo "<br>zu viele Daten mZuordung gl_n"; //die();
			}
		} else {
			$gameslist_name	= $gameslist;
		}
//echo "<br>gameslist: "; var_dump($gameslist); //die(); 
//echo "<br>gameslist_name: "; var_dump($gameslist_name); //die(); 
//echo "<br>error_text: "; var_dump($error_text); //die(); 
		$pgnnr = 0;
		$return_arr['error'] = $error_text;  
		if ($error_text == '') {
			foreach($gameslist_name as $gln) {
				if ($tkz == 't') $paar = $gln->paar; else $paar = 0;
				//$query = "INSERT IGNORE INTO #__clm_pgn "
				$query = "REPLACE INTO #__clm_pgn "
					." ( `tkz`, `tid`, `dg`, `runde`, `paar`, `brett`, `text`, `error` ) "
					." VALUES ('".$tkz."',".$tid.",".$gln->dg.",".$gln->runde.",".$paar.",".$gln->brett.",'".$return_arr['text']."','".$return_arr['error']."' )";
//echo "<br>query: "; var_dump($query); //die(); 
				clm_core::$db->query($query);
				$pgnnr = clm_core::$db->insert_id();
//echo "<br>1pgnnr: "; var_dump($pgnnr); //die(); 
				if ($pgnnr == 0) {
					$query = "SELECT id FROM #__clm_pgn "
						.' WHERE tkz = "'.$tkz.'"'
						.' AND tid = '.$tid
						.' AND dg = '.$gln->dg
						.' AND runde = '.$gln->runde
						.' AND paar = '.$paar
						.' AND brett = '.$gln->brett;
//echo "<br>query: "; var_dump($query); //die(); 
					$pgnnr_arr	= clm_core::$db->loadObjectList($query);
//echo "<br>pgnnr_arr: "; var_dump($pgnnr_arr); //die(); 
					$pgnnr = $pgnnr_arr[0]->id;
//echo "<br>2pgnnr: "; var_dump($pgnnr); //die(); 
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
//echo "<br>query: "; var_dump($query); //die(); 
				clm_core::$db->query($query);
				break;
			}
		} else {
			$query = "SELECT count(*) as anz FROM #__clm_pgn "
						.' WHERE tkz = "'.$tkz.'"'
						.' AND tid = '.$tid;
//echo "<br>max_query: "; var_dump($query); //die(); 
			$count_arr	= clm_core::$db->loadObjectList($query);
			$count_id = $count_arr[0]->anz;
//echo "<br>max_id: $count_id  count_arr"; var_dump($count_arr); //die(); 
			$query = "INSERT IGNORE INTO #__clm_pgn "
				." ( `tkz`, `tid`, `dg`, `runde`, `paar`, `brett`, `text`, `error` ) "
				." VALUES ('".$tkz."',".$tid.",0,0,0,".$count_id.",'".$return_arr['text']."','".$return_arr['error']."' )";
//echo "<br>er_query: "; var_dump($query); //die(); 
			clm_core::$db->query($query);
			$pgnnr = clm_core::$db->insert_id();
//echo "<br>3pgnnr: "; var_dump($pgnnr); //die(); 
		}
		$return_arr['pgnnr'] = $pgnnr;
		$query = "SELECT * FROM #__clm_pgn "
			.' WHERE id = '.$pgnnr;
//echo "<br>query: "; var_dump($query); //die(); 
		$pgnnr_arr	= clm_core::$db->loadObjectList($query);
//echo "<br>pgnnr: $pgnnr  pgnnr_arr: "; var_dump($pgnnr_arr); //die(); 
		$return_arr['tkz'] = $pgnnr_arr[0]->tkz;
		$return_arr['tid'] = $pgnnr_arr[0]->tid;
		$return_arr['dg'] = $pgnnr_arr[0]->dg;
		$return_arr['runde'] = $pgnnr_arr[0]->runde;
		if ($tkz == 't') { $return_arr['paar'] = $pgnnr_arr[0]->paar; }
		else $return_arr['paar'] = 0;
		$return_arr['brett'] = $pgnnr_arr[0]->brett;
//echo "<br>return:"; var_dump($return_arr);
//echo "<br>qu---------------------------------------------------------------"; //die();
		return $return_arr;
	}
	
	
	function maintain() {		
		$adminLink = new AdminLink ();
		$adminLink->view = 'pgnimport';
		$adminLink->makeURL ();		
		$liga = JRequest::getVar('liga', '', 'default', 'string');
//echo "<br>cm-html-pgnimport: liga $liga "; var_dump($liga); //die();
		if ($liga == '') {	
			$msg = JText::_( 'PGN_CHOOSE_LEAGUE_MSG' );
			$this->setRedirect($adminLink->url, $msg);
		} else { 
			JRequest::setVar('view', 'pgndata');		
			parent::display(); 	
		}
	}
	
	function delete_all() {		
		$adminLink = new AdminLink ();
		$adminLink->view = 'pgnimport';
		$adminLink->makeURL ();		
		$liga = JRequest::getVar('liga', '', 'default', 'string');
//echo "<br>ca-html-pgnimport: liga $liga "; var_dump($liga); //die();
		if ($liga == '') {	
			$msg = JText::_( 'PGN_CHOOSE_LEAGUE_MSG' );
			$this->setRedirect($adminLink->url, $msg);
		} else { 
			$pgn_del = $this->getDelPGN(true);
//echo "<br>ca-html-pgnimport: pgn_del $pgn_del "; var_dump($pgn_del); //die();
			$msg = $pgn_del.' '.JText::_( 'PGN_DELETE_TOTAL' );
			$this->setRedirect($adminLink->url, $msg);
		}
	}

	function delete_open() {		
		$adminLink = new AdminLink ();
		$adminLink->view = 'pgnimport';
		$adminLink->makeURL ();		
		$liga = JRequest::getVar('liga', '', 'default', 'string');
//echo "<br>ca-html-pgnimport: liga $liga "; var_dump($liga); //die();
		if ($liga == '') {	
			$msg = JText::_( 'PGN_CHOOSE_LEAGUE_MSG' );
			$this->setRedirect($adminLink->url, $msg);
		} else {
			$pgn_del = $this->getDelPGN(false);
//echo "<br>co-html-pgnimport: pgn_del $pgn_del "; var_dump($pgn_del); //die();
			$msg = $pgn_del.' '.JText::_( 'PGN_DELETE_TOTAL' );
			$this->setRedirect($adminLink->url, $msg);
		}
	}

	function getDelPGN ($all = false) {
		
		$liga = JRequest::getVar('liga', '', 'default', 'string');
		$liga_arr = explode('.', $liga, 2);
		$tkz = $liga_arr[0];
		$tid = $liga_arr[1];
//echo "<br>1liga: $liga  tid: $tid  tkz: $tkz"; //die();
		
		$query = "DELETE FROM #__clm_pgn "
			.' WHERE tkz = "'.$tkz.'"'
			.' AND tid = '.$tid;
		if ($all == false) 
			$query .= ' AND runde = 0 ';
//echo "<br>query: "; var_dump($query); //die(); 
		clm_core::$db->query($query);
		$total = clm_core::$db->affected_rows();		
//echo "<br>total:"; var_dump($total); //die();

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
//echo "<br>query: "; var_dump($query); //die(); 
		  clm_core::$db->query($query);
		  $total_spl = clm_core::$db->affected_rows();		
//echo "<br>total1:"; var_dump($total_spl); //die();
		}
		return $total;
	}	

	function cancel() {		
		$adminLink = new AdminLink ();
		$adminLink->view = 'swt';
		$adminLink->makeURL ();		
		$msg = JText::_( 'SWT_CANCEL_MSG' );
		$this->setRedirect($adminLink->url, $msg);	
	}
	
}

?>
