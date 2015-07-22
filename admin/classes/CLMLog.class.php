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

/*
//
//	Diese Klasse ist veraltet und leitet alles auf die neue Log Klasse um.
// Beim Umzug der entsprechenden Views sollten die Abhängigkeiten zu dieser Klasse aufgelöst werden.
//
*/
	
class CLMLog {

	function __construct() {

		// aktion
		$this->aktion 			= NULL; // wird von außen befüllt
		$this->nr_aktion 		= 0; // wird von außen befüllt 
		// parameters
		$this->params 			= array();  // wird von außen befüllt mit key=>value-Paaren
	
	}

	/**
	 * log schreiben
	 */
	function write() {
		clm_core::addDeprecated($this->aktion, json_encode($this->params));
	}


}
?>
