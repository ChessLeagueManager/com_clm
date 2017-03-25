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

class TableCLMSaisons extends JTable
{

	var $id				= null;
	var $name			= '';
	var $published		= 0;
	var $archiv			= 0;
	var $bemerkungen	= '';
	var $bem_int		= '';
	var $checked_out	= 0;
	var $checked_out_time	= '1970-01-01 00:00:00';
	var $ordering		= null;
	var $datum			= '1970-01-01';
	
	function __construct( &$_db ) {
		parent::__construct( '#__clm_saison', 'id', $_db );
	}
}
