<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableCLMRegistrations extends JTable
{
	var $id			= 0;
	var $tid		= 0;
	var $name		= '';
	var $vorname 	= '';
	var $birthYear	= '0000';
	var $geschlecht = null;
	var $club	 	= '';
	var $email		= '';
	var $dwz		= 0;
	var $dwz_I0		= 0;
	var $elo		= 0;
	var $FIDEid		= null;
	var $FIDEcco	= null;
	var $titel		= null;
	var $mgl_nr		= 0;
	var $PKZ		= null;
	var $zps		= '0';
	var $tel_no		= '';
	var $account	= '';
	var $comment	= null;
	var $status		= 0;
	var $timestamp	= 0;
	var $ordering	= 0;

	function __construct( &$_db ) {
		parent::__construct( '#__clm_online_registration', 'id', $_db );
	}

	/**
	 * Overloaded check function
	 */

	/**
	 * Overloaded check function
	 *
	 * @access public
	 * @return boolean
	 * @see JTable::check
	 * @since 1.5
	 */
	 // wegen Abwärtskompatibilität kein Überschreiben und Verwenden von check()
	 // kann bei Bedarf geändert werden, wenn alte Turnier-Implementierung gekappt wird.
	function checkData() {

		// aktuelle Turnierdaten laden
/*		$tournament = new CLMTournament($this->id, true);

		if (trim($this->name) == '') { // Name vorhanden
			$this->setError( CLMText::errorText('NAME', 'MISSING') );
			return false;
		}
*/
		return true;
	
	}


}
