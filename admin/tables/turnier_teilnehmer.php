<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2017 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableCLMTurnier_Teilnehmer extends JTable
{

	var $id			= null;
	var $sid		= null;
	var $turnier		= '';
	var $snr		= '';
	var $name       = '';
	var $birthYear  = '';
	var $geschlecht = '';
	var $verein     = '';
	var $twz        = '';
	var $start_dwz  = '';
	var $FIDEelo    = '';
	var $FIDEid     = '';
	var $FIDEcco    = '';
	var $titel      = '';
	var $mgl_nr		= '';
	var $PKZ		= '';
	var $zps		= '';
	var $status		= '';
	var $rankingPos	= '';
	var $tlnrStatus = 0;
	var $anz_spiele = 0;
	var $sum_punkte		= '';
	var $sum_bhlz		= '';
	var $sum_busum		= '';
	var $sum_sobe		= '';
	var $koStatus		= '';
	var $koRound		= '';
	var $sum_wins		= '';
	var $sumTiebr1		= '';
	var $sumTiebr2		= '';
	var $sumTiebr3		= '';
	var $DWZ		= '';
	var $I0			= '';
	var $Punkte		= 0;
	var $Partien		= 0;
	var $We			= 0;
	var $Leistung		= 0;
	var $EFaktor		= 0;
	var $Niveau		= 0;
	var $published		= 0;
	var $checked_out	= 0;
	var $checked_out_time	= 0;
	var $ordering		= null;


	function __construct( &$_db ) {
		parent::__construct( '#__clm_turniere_tlnr', 'id', $_db );
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
		if (trim($this->name) == '') { // Name vorhanden
			$this->setError( CLMText::errorText('NAME', 'MISSING') );
			return false;
		} elseif (!is_numeric($this->start_dwz)) { // TWZ = Zahl
			$this->setError( CLMText::errorText('RATING', 'NOTANUMBER') );
			return false;
		} elseif (!is_numeric($this->FIDEelo)) { // TWZ = Zahl
			$this->setError( CLMText::errorText('FIDE_ELO', 'NOTANUMBER') );
			return false;
		} elseif (!is_numeric($this->twz)) { // TWZ = Zahl
			$this->setError( CLMText::errorText('TWZ', 'NOTANUMBER') );
			return false;
		}

		return true;
	}
}
