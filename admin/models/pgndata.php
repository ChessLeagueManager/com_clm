<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

class CLMModelPGNdata extends JModelLegacy {

	function __construct () {
	
		parent::__construct ();
		
		// Konfigurationsparameter auslesen
		$config = clm_core::$db->config();
	}
	
	
	// Turnierdaten
	function getTurnier () {
		$liga = JRequest::getVar('liga', '', 'default', 'string');
		$liga_arr = explode('.', $liga, 2);
		$tkz = $liga_arr[0];
		$tid = $liga_arr[1];
//echo "<br>1liga: $liga  tid: $tid  tkz: $tkz"; //die();
		JRequest::setVar('tkz', $tkz);
		JRequest::setVar('tid', $tid);
		$tkz = JRequest::getVar('tkz', '0', 'default', 'string');
		$tid = JRequest::getVar('tid', '0', 'default', 'string');
//echo "<br>2liga: $liga  tid: $tid  tkz: $tkz"; //die();
//echo "<br>ttid: $tid  tkz: $tkz"; //die();
		if ($tkz == 't') { 		// Teamwettbewerb
			$query = "SELECT * FROM #__clm_liga "
					.' WHERE id = '.$tid;
//echo "<br>query: "; var_dump($query); //die(); 
		} elseif ($tkz == 's') { 		// Teamwettbewerb
			$query = "SELECT * FROM #__clm_turniere "
					.' WHERE id = '.$tid;
//echo "<br>query: "; var_dump($query); //die(); 
		}
		$turnier	= clm_core::$db->loadObjectList($query);
//echo "<br>turnier: "; var_dump($turnier); //die(); 
		return $turnier;
	}
	
	function store () {
	
		$tid 	= JRequest::getVar ('tid', '0', 'default', 'string');
		$tkz 	= JRequest::getVar ('tkz', '0', 'default', 'string');
		$pgn_count 	= JRequest::getVar ('pgn_count', '-1', 'default', 'int');
//echo "<br>store  tid $tid  tkz $tkz  pgn_count $pgn_count";
		// DB-Zugriff
		for ($p = 0; $p < $pgn_count ; $p++) {    			
			
			if (JRequest::getVar ('runde'.$p, '-1', 'default', 'int') == (-1)) continue;
			if (JRequest::getVar ('dg'.$p, '0', 'default', 'int') == 0) continue;
			if (JRequest::getVar ('runde'.$p, '0', 'default', 'int') == 0) continue;
			if ($tkz == 't') 
				if (JRequest::getVar ('paar'.$p, '0', 'default', 'int') == 0) continue;
			if (JRequest::getVar ('brett'.$p, '0', 'default', 'int') == 0) continue;
			$query = "DELETE FROM #__clm_pgn "
				.' WHERE tkz = "'.$tkz.'"'
				.' AND tid = '.$tid
				.' AND dg = '.clm_escape(JRequest::getVar ('dg'.$p))
				.' AND runde = '.clm_escape(JRequest::getVar ('runde'.$p));
			if ($tkz == 't') 
				$query .= ' AND paar = '.clm_escape(JRequest::getVar ('paar'.$p));
			$query .= ' AND brett = '.clm_escape(JRequest::getVar ('brett'.$p));
//echo "<br>store-delete_query: "; var_dump($query); //die(); 
			clm_core::$db->query($query);
			$query = 'UPDATE #__clm_pgn '
				.' SET dg = '.clm_escape(JRequest::getVar ('dg'.$p))
				.' , runde = '.clm_escape(JRequest::getVar ('runde'.$p));
			if ($tkz == 't') 
				$query .= ' , paar = '.clm_escape(JRequest::getVar ('paar'.$p));
			$query .= ' , brett = '.clm_escape(JRequest::getVar ('brett'.$p))
				." , text = '".clm_escape(JRequest::getVar ('text'.$p))."'"
				." , error = ''"
				.' WHERE id = '.clm_escape(JRequest::getVar ('pgnnr'.$p));
//echo "<br>in_query: "; var_dump($query); //die(); 
			clm_core::$db->query($query);
//echo "<br>e: ".mysqli_errno.": ".mysqli_error;
			if ($tkz == 't') {
			  $query = 'UPDATE #__clm_rnd_spl '
				.' SET pgnnr = '.clm_escape(JRequest::getVar ('pgnnr'.$p))
				.' WHERE lid = '.$tid
				.' AND dg = '.clm_escape(JRequest::getVar ('dg'.$p))
				.' AND runde = '.clm_escape(JRequest::getVar ('runde'.$p))
				.' AND paar = '.clm_escape(JRequest::getVar ('paar'.$p))
				.' AND brett = '.clm_escape(JRequest::getVar ('brett'.$p));
			} elseif ($tkz == 's') {
			  $query = 'UPDATE #__clm_turniere_rnd_spl '
				.' SET pgn = '.clm_escape(JRequest::getVar ('pgnnr'.$p))
				.' WHERE turnier = '.$tid
				.' AND dg = '.clm_escape(JRequest::getVar ('dg'.$p))
				.' AND runde = '.clm_escape(JRequest::getVar ('runde'.$p))
				.' AND brett = '.clm_escape(JRequest::getVar ('brett'.$p));
			}
echo "<br>query: "; var_dump($query); //die(); 
			clm_core::$db->query($query);
		}
		return true;
	}
}
