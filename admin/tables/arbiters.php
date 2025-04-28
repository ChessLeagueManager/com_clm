<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableCLMArbiters extends JTable
{
	var $id			= 0;
	var $fideid		= 0;
	var $fidefed	= 'GER';
	var $title		= '';
	var $name		= '';
	var $vorname	= '';
	var $published	= 0;
	var $ordering	= 0;
	var $bemerkungen	= '';
	var $bem_int	= '';

	function __construct( &$_db ) {
		parent::__construct( '#__clm_arbiter', 'id', $_db );
	}

	/**
	 * Overloaded check function
	 *
	 * @access public
	 * @return boolean
	 * @see JTable::check
	 * @since 1.5
	 */
	function checkData() {

		// aktuelle Daten laden

		if (trim($this->name) == '') { // Name vorhanden
			$this->setError( CLMText::errorText('NAME', 'MISSING') );
			return false;
		
		}

		return true;
	
	}


}
