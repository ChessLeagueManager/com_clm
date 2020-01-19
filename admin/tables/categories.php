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

class TableCLMCategories extends JTable
{
	var $id				= 0;
	var $parentid		= 0;
	var $name		= '';
	var $sid		= 0;
	var $dateStart = '1970-01-01';
	var $dateEnd   = '1970-01-01';
	var $tl			= 0;
	var $bezirk		= '';
	var $bezirkTur = '1';
	var $vereinZPS = '';
	var $published		= 0;
	var $started		= 0;
	var $finished		= 0;
	// var $invitationText = ''; // soll nicht aus catform heraus gelöscht werden...
	var $bemerkungen	= '';
	var $bem_int		= '';
	var $checked_out	= 0;
	var $checked_out_time	= '1970-01-01 00:00:00';
	var $ordering		= 0;
	var $params 		= '';

	function __construct( &$_db ) {
		parent::__construct( '#__clm_categories', 'id', $_db );
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
		// $category = new CLMCategory($this->id, TRUE);

		if (trim($this->name) == '') { // Name vorhanden
			$this->setError( CLMText::errorText('NAME', 'MISSING') );
			return false;
		
		/*
		} elseif ($this->sid <= 0) { // SaisonID > 0
			$this->setError( CLMText::errorText('SEASON', 'IMPOSSIBLE') );
			return false;
		
		} elseif ($this->tl <= 0) {
			$this->setError( CLMText::errorText('TOURNAMENT_DIRECTOR', 'MISSING') );
			return false;
		*/
		
		}

		return true;
	
	}


}
