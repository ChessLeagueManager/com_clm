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

use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;

class TableCLMRangspieler extends Table
{
	var $Gruppe		= '';
	var $ZPS		= '';
	var $ZPSmgl		= '00000';
	var $Mgl_Nr		= '';
	var $PKZ		= '';
	var $Rang		= '';
	var $man_nr		= '';
	var $sid		= '';

	function __construct( &$_db ) {
		parent::__construct( '#__clm_rangliste_spieler', 'id', $_db );
	}

	/**
	 * Overloaded check function
	 *
	 * @access public
	 * @return boolean
	 * @see Table::check
	 * @since 1.5
	 */
	function check()
	{
		// check for valid client name
	/*	if (trim($this->Gruppe == '')) {
			$this->setError(Text::_( 'BNR_CLIENT_NAME' ));
			return false;
		}
*/
		// check for valid client contact
/**		if (trim($this->sid == '')) {
			$this->setError(Text::_( 'Saison muss angegeben werden !' ));
			return false;
		}

**/		

		return true;
	}
}
