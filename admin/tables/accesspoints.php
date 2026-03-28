<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Table\Table;

class TableCLMAccessPoints extends Table
{
	var $id			= null;
	var $area		= '';
	var $accesstopic = '';
	var $accesspoint = '';
	var $rule		= '';
	var $published	= 0;
	var $ordering	= 0;


	function __construct( &$_db ) {
		parent::__construct( '#__clm_access_points', 'id', $_db );
	}

	/**
	 * Overloaded check function
	 *
	 * @access public
	 * @return boolean
	 * @see Table::check
	 * @since 1.5
	 */
	 // wegen Abwärtskompatibilität kein Überschreiben und Verwenden von check()
	function checkData() {

		// aktuelle Daten laden
		$accesspoint = new CLMAccesspoint($this->id, true);

		if (trim($this->accesspoint) == '') { // Name vorhanden
			$this->setError( CLMText::errorText('NAME', 'MISSING') );
			return false;
		}
		
		return true;
	
	}


}
