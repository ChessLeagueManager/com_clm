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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableCLMTurnier_Runden extends JTable
{

	var $id			= null;
	var $sid		= null;
	var $name		= '';
	var $turnier		= '';
	var $dg			= '';
	var $nr			= '';
	var $datum		= '1970-01-01';
	var $startzeit		= '00:00:00';
	var $abgeschlossen	= '';
	var $tl_ok		= '';
	var $published		= 0;
	var $bemerkungen	= '';
	var $bem_int		= '';
	var $gemeldet		= '';
	var $editor			= '';
	var $zeit			= '1970-01-01 00:00:00';
	var $edit_zeit		= '1970-01-01 00:00:00';
	var $checked_out	= 0;
	var $checked_out_time	= '1970-01-01 00:00:00';
	var $ordering		= null;

	function __construct( &$_db ) {
		parent::__construct( '#__clm_turniere_rnd_termine', 'id', $_db );
	}

	/**
	 * Overloaded check function
	 *
	 * @access public
	 * @return boolean
	 * @see JTable::check
	 * @since 1.5
	 */
	function check()
	{

		return true;
	}
	
	/**
	* bearbeitet und überprüft Daten
	*
	*/
	function checkData() {
	
		$this->name = trim($this->name);
		if ( strlen($this->name) == 0 ) {
			$this->setError( CLMText::errorText('ROUND_NAME', 'MISSING') );
			return false;
		} elseif ($this->nr < 1) {
			$this->setError( CLMText::errorText('ROUND_NR', 'MISSING') );
			return false;
		}
		// weitere 
		if ($this->tl_ok == 1) { // Bestätigung gesetzt?
			$tournamentRound = new CLMTournamentRound($this->turnier, $this->id);
			if (!$tournamentRound->checkResultsComplete()) {
				$this->setError( CLMText::errorText('RESULTS', 'INCOMPLETE') );
				return false;
			}
		
		}
	
		if ($this->startzeit != '') {
			if (!CLMText::isTime($this->startzeit, true, false)) {
				$this->setError( CLMText::errorText('RUNDE_STARTTIME', 'IMPOSSIBLE') );
				return false;
			
			}
		}
	
	
		return true;
	
	}
	
	
}
