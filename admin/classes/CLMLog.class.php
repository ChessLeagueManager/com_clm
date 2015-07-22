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


/**
 * schreibt einen Log-Eintrag
*/
	
class CLMLog {

	function __construct() {

		// INIT
		// DB
		$this->_db				= & JFactory::getDBO();
		
		// datum
		$date 					= & JFactory::getDate();
		$this->_datum 			= $date->toMySQL();
		
		// user
		$user 					= & JFactory::getUser();
		$this->_jid_aktion	= $user->get('id');
		
		// aktion
		$this->aktion 			= NULL; // wird von außen befüllt
		$this->nr_aktion 		= 0; // wird von außen befüllt 
		
		// parameters
		$this->params 			= array();  // wird von außen befüllt mit key=>value-Paaren

		// Konfigurationsparameter auslesen
		$config = &JComponentHelper::getParams('com_clm');
		$this->logfile = $config->get('logfile', 1);
	
	}


	/**
	 * log schreiben
	 */
	function write() {

		if (($this->logfile == 1) || ($this->nr_aktion > 0)) {

			// bereits vorhandene felder und Werte
			$queryFields = array('`aktion`', '`nr_aktion`', '`jid_aktion`', '`datum`');									//klkl
			$queryValues = array('\''.$this->aktion.'\'', '\''.$this->nr_aktion.'\'', '\''.$this->_jid_aktion.'\'', '\''.$this->_datum.'\'');  //klkl
			
			if (!isset($this->params["sid"])) $this->params["sid"] = CLM_SEASON;
			// Parameter ergänzen
			foreach ($this->params as $key => $value) {
				$queryFields[] = '`'.$key.'`';
				$queryValues[] = '\''.$value.'\'';
			}
			
			$query	= 'INSERT INTO #__clm_log '
				. '('.implode($queryFields, ', ').')'
				. ' VALUES '
				. '('.implode($queryValues, ', ').')'
				;
	
			$this->_db->setQuery($query);
			$this->_db->query();

		}

	}


}
?>