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

class TableCLMTurnier_Ergebnisse extends JTable
{

	var $id				= null;
	var $sid				= null;
	var $turnier		= '';
	var $runde			= '';
	var $paar			= '';
	var $brett			= '';
	var $dg				= '';
	var $tln_nr			= '';
	var $heim			= '';
	var $spieler		= '';
	var $gegner			= '';
	var $ergebnis		= '';
	var $kampflos		= '';
	var $pgn				= '';
	var $ordering		= null;

	function __construct( &$_db ) {
		parent::__construct( '#__clm_turniere_rnd_spl', 'id', $_db );
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
}
