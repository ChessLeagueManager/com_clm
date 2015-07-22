<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
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
	
		// roundid
		$this->param['playerid'] = JRequest::getInt('playerid');
	
	}

	
	function _getPlayerData() {
	
		$query = 'SELECT snr, name, birthYear, geschlecht, verein, start_dwz, start_I0, FIDEelo, titel, twz, turnier, sum_punkte, koStatus, sumTiebr1, sumTiebr2, sumTiebr3'
			. ' FROM #__clm_turniere_tlnr'
			. ' WHERE id = '.$this->param['playerid']
			;
		$this->_db->setQuery($query);
		$this->playerData = $this->_db->loadObject();
	
	}


	function _getTurnierData() {
	
		$query = 'SELECT name, typ, tiebr1, tiebr2, tiebr3'
			. ' FROM #__clm_turniere'
			. ' WHERE id = '.$this->playerData->turnier
			;
		$this->_db->setQuery($query);
		$this->turnierData = $this->_db->loadObject();
	
	}


}

?>
