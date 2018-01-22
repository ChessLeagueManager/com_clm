<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2018 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableCLMRnd_man extends JTable
{

	var $id			= null;
	var $sid		= null;
	var $lid		= null;
	var $runde		= null;
	var $paar		= null;
	var $dg			= null;
	var $heim		= null;
	var $tln_nr		= null;
	var $gegner		= null;
	var $ergebnis	= null;
	var $kampflos	= null;
	var $brettpunkte	= null;
	var $manpunkte		= null;
	var $bp_sum		= null;
	var $mp_sum		= null;
	var $gemeldet		= null;
	var $editor		= null;
	var $dwz_editor		= null;
	var $zeit		= '1970-01-01 00:00:00';
	var $edit_zeit		= '1970-01-01 00:00:00';
	var $dwz_zeit		= '1970-01-01 00:00:00';
	var $published		= 0;
	var $checked_out	= 0;
	var $checked_out_time	= '1970-01-01 00:00:00';
	var $ordering		= null;
	var $ko_decision	= 0;
	var $comment		= '';
	var $pdate		= '1970-01-01';
	var $ptime		= '00:00:00';

	function __construct( &$_db ) {
		parent::__construct( '#__clm_rnd_man', 'id', $_db );
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
/**		// check for valid client name
		if (trim($this->name == '')) {
			$this->setError(JText::_( 'BNR_CLIENT_NAME' ));
			return false;
		}

		// check for valid client contact
		if (trim($this->sid == '')) {
			$this->setError(JText::_( 'Saison muss angegeben werden !' ));
			return false;
		}

**/		

		return true;
	}
}
