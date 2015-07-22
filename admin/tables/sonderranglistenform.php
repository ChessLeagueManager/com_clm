<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableCLMSonderranglistenform extends JTable
{
	var $id						= null;
	var $turnier				= null;
	var $name					= '';
	var $use_rating_filter 		= false;
	var $rating_type			= 1;
	var $rating_higher_than 	= 0;
	var $rating_lower_than 		= 0;
	var $use_birthYear_filter	= false;
	var $birthYear_younger_than	= 0;
	var $birthYear_older_than	= 0;
	var $use_sex_filter			= false;
	var $sex					= '';
	var $use_sex_year_filter	= false;
	var $maleYear_younger_than	= 0;
	var $maleYear_older_than	= 0;
	var $femaleYear_younger_than	= 0;
	var $femaleYear_older_than	= 0;
	var $use_zps_filter 		= false;
	var $zps_higher_than 		= '';
	var $zps_lower_than 		= 'ZZZZZ';
	var $published				= 0;
	var $checked_out			= 0;
	var $checked_out_time		= 0;
	var $ordering				= null;

	function __construct( &$_db ) {
		parent::__construct( '#__clm_turniere_sonderranglisten', 'id', $_db );
	}

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
	
/*	function reorderAll() {
		$query = 'SELECT DISTINCT turnier FROM '.$this->_db->nameQuote($this->_tbl);
		$this->_db->setQuery($query);
		$turniere = $this->_db->loadResultArray();
		

		foreach($turniere as $turnier) {
			$this->reorder('turnier = '.$turnier);
		}
		return true;
	}
*/

}
