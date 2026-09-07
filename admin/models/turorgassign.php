<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;

class CLMModelTurorgAssign extends JModelLegacy {

	// benötigt für Pagination
	function __construct()
	{
		parent::__construct();

		// user
		$this->user =Factory::getUser();
		
		$this->_getData();

		$this->_getForms();

	}


	function _getData() {
		
		$lid = clm_core::$load->request_int('lid');
		$tid = clm_core::$load->request_int('tid');
		if ($lid > 0) { 		// Teamwettbewerb
			$query = "SELECT l.*, s.name as sname FROM #__clm_liga as l "
						." LEFT JOIN #__clm_saison as s ON l.sid = s.id "
						.' WHERE l.id = '.$lid;
		} elseif ($tid > 0) { 		// Einzel(Single)wettbewerb
			$query = "SELECT t.*, s.name as sname FROM #__clm_turniere as t"
						." LEFT JOIN #__clm_saison as s ON t.sid = s.id "
						.' WHERE t.id = '.$tid;
		}
		$this->turnier	= clm_core::$db->loadObjectList($query);
		$sid = $this->turnier[0]->sid;

		// mögliche Organisatoren in der Installation
		$query = "SELECT u.* FROM #__clm_user as u "
				." LEFT JOIN #__clm_usertype as ut ON ut.usertype = u.usertype " 
				." WHERE (ut.params LIKE '%BE_tournament_edit_detail=1%' OR ut.params LIKE '%BE_tournament_edit_detail=2%') "
				." AND u.sid = ".$sid
				." AND u.published = 1 AND ut.published = 1";
//echo "<br>query".$query;
		$this->organisers	= clm_core::$db->loadObjectList($query);
//echo "<br>query0"; var_dump($this->organisers[0]);
//echo "<br>query1"; var_dump($this->organisers[1]);
//die();		
		// mögliche Kassierer in der Installation
		$query = "SELECT u.* FROM #__clm_user as u "
				." WHERE u.usertype != 'spl' "
				." AND u.sid = ".$sid
				." AND u.published = 1";
//echo "<br>query".$query;
		$this->cashiers	= clm_core::$db->loadObjectList($query);
//echo "<br>query0"; var_dump($this->cashiers[0]);
//echo "<br>query1"; var_dump($this->cashiers[1]);
//die();		

		// Hauptturnierleiter
		$query = "SELECT a.*, u.username FROM #__clm_arbiter_turnier as a "
				." LEFT JOIN #__clm_user as u ON a.fideid = u.jid AND u.sid = ".$sid
				." WHERE a.liga = $lid AND a.turnier = $tid "
				." AND a.trole = 'T' AND a.role = 'TL' ";
		$this->organiser_TL	= clm_core::$db->loadObjectList($query);
		// weitere Turnierleiter
		$query = "SELECT a.*, u.username FROM #__clm_arbiter_turnier as a "
				." LEFT JOIN #__clm_user as u ON a.fideid = u.jid AND u.sid = ".$sid
				." WHERE a.liga = $lid AND a.turnier = $tid "
				." AND a.trole = 'T' AND a.role = 'TLW' "
				." ORDER BY a.id ";
		$this->organiser_TLW	= clm_core::$db->loadObjectList($query);
		// Turnierorganisatoren
		$query = "SELECT a.*, u.username FROM #__clm_arbiter_turnier as a"
				." LEFT JOIN #__clm_user as u ON a.fideid = u.jid AND u.sid = ".$sid
				." WHERE a.liga = $lid AND a.turnier = $tid "
				." AND a.trole = 'T' AND a.role = 'TO' "
				." ORDER BY a.id ";
		$this->organiser_TO	= clm_core::$db->loadObjectList($query);
		// Kassierer
		$query = "SELECT a.*, u.username FROM #__clm_arbiter_turnier as a"
				." LEFT JOIN #__clm_user as u ON a.fideid = u.jid AND u.sid = ".$sid
				." WHERE a.liga = $lid AND a.turnier = $tid "
				." AND a.trole = 'T' AND a.role = 'KA' "
				." ORDER BY a.id ";
		$this->organiser_KA	= clm_core::$db->loadObjectList($query);
		
	}


	// alle vorhandenen Filter
	function _getForms() {
	
		//CLM parameter auslesen
		$clm_config = clm_core::$db->config();
		if ($clm_config->field_search == 1) $field_search = "js-example-basic-single";
		else $field_search = "inputbox";
	
		if (!isset($this->form) OR is_null($this->form)) $this->form = array();
		
		
	}

}

?>
