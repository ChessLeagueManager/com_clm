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

class CLMModelTurInvite extends JModelLegacy {

	
	// benötigt für Pagination
	function __construct() {
		
		parent::__construct();

		
		// get parameters
		$this->_getParameters();

		// get all data
		$this->_getData();


	}


	// alle vorhandenen Parameter auslesen
	function _getParameters() {
	
		if (!isset($this->param) OR is_null($this->param)) $this->param = array();	// seit J 4.2 nötig um notice zu vermeiden
		// turnierid
		$this->param['id'] = clm_core::$load->request_int('id');
	
	}


	function _getData() {
	
		// ALTER TABLE `jclm_clm_turniere` ADD `invitationText` TEXT NOT NULL AFTER `published` ;
	
		// turnier
		$query = 'SELECT name, invitationText'
			. ' FROM #__clm_turniere'
			. ' WHERE id = '.$this->param['id']
			;
		$this->_db->setQuery($query);
		$this->turnier = $this->_db->loadObject();
	
		
	}
	
	
	


}

?>