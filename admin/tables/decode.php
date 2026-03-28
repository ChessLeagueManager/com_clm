<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Table\Table;

class TableCLMDecode extends Table
{
	var $id			= 0;
	var $sid		= 0;
	var $source		= '';
	var $oname		= '';
	var $nname		= '';
	var $verein		= '';

	function __construct( $_db ) {
		parent::__construct( '#__clm_player_decode', 'id', $_db );
	}
	
	function check()
	{
		return true;
	}

}
