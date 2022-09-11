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
defined('_JEXEC') or die('Restricted access');

class CLMModelTurPlayerEdit extends JModelLegacy {


	// benötigt für Pagination
	function __construct() {
		
		parent::__construct();


		// user
		$this->user =JFactory::getUser();
		
		// get parameters
		$this->_getParameters();

		// get Player
		$this->_getPlayerData();
		
		// get turnier
		$this->_getTurnierData();

		

	}


	// alle vorhandenen Parameter auslesen
	function _getParameters() {
	
		if (!isset($this->param) OR is_null($this->param)) $this->param = array();	// seit J 4.2 nötig um notice zu vermeiden
		// roundid
		$this->param['playerid'] = clm_core::$load->request_int('playerid');
	
	}

	
	function _getPlayerData() {
	
		$query = 'SELECT * '
			. ' FROM #__clm_turniere_tlnr'
			. ' WHERE id = '.$this->param['playerid']
			;
		$this->_db->setQuery($query);
		$this->playerData = $this->_db->loadObject();
	
	}


	function _getTurnierData() {
	
		$query = 'SELECT * '
			. ' FROM #__clm_turniere'
			. ' WHERE id = '.$this->playerData->turnier
			;
		$this->_db->setQuery($query);
		$this->turnierData = $this->_db->loadObject();
	
		$query = 'SELECT * '
			. ' FROM #__clm_turniere_teams'
			. ' WHERE tid = '.$this->playerData->turnier
			;
		$this->_db->setQuery($query);
		$this->teamData = $this->_db->loadObjectList();

	}


}

?>
