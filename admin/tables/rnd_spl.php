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

class TableCLMRnd_spl extends JTable
{

	var $id			= null;
	var $sid		= null;
	var $lid		= null;
	var $runde		= null;
	var $paar		= null;
	var $dg			= null;
	var $tln_nr		= null;
	var $brett		= null;
	var $heim		= null;
	var $weiss		= null;
	var $spieler		= null;
	var $zps		= null;
	var $gegner		= null;
	var $gzps		= null;
	var $ergebnis		= null;
	var $kampflos		= null;
	var $punkte		= null;
	var $gemeldet		= null;
	var $dwz_edit		= null;
	var $dwz_editor		= null;

	function __construct( &$_db ) {
		parent::__construct( '#__clm_rnd_spl', 'id', $_db );
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
