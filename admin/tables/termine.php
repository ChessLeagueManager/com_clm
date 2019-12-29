<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2019 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableCLMTermine extends JTable
{

	var $id					= 0;
	var $name				= '';
	var $beschreibung		= '';
	var $address			= '';
	var $catidAlltime		= 0;
	var $catidEdition		= 0;
	var $category			= '';
	var $host				= '';
	var $startdate			= '1970-01-01';
	var $starttime			= '00:00:00';
	var $allday				= 0;
	var $enddate			= '1970-01-01';
	var $endtime			= '00:00:00';
	var $noendtime			= 0;
	var $attached_file		= '';
	var $attached_file_description	= '';
	var $published			= 0;
	var $checked_out		= 0;
	var $checked_out_time	= '1970-01-01 00:00:00';
	var $ordering			= 0;
	var $event_link			= '';

	function __construct( &$_db ) {
		parent::__construct( '#__clm_termine', 'id', $_db );
	}

	/**
	 * Overloaded check function
	 *
	 * @access public
	 * @return boolean
	 * @see JTable::check
	 * @since 1.5
	 */
	function check() {

		return true;
		
	}
	
	
	function checkData() {

		if (trim($this->name) == '') { // Name vorhanden
			$this->setError( CLMText::errorText('NAME', 'MISSING') );
			return false;
		}
		
		return true;
		
	}
	
	
}
