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

class TableCLMTurnier_Teilnehmer extends JTable
{

	var $id			= 0;
	var $sid		= 0;
	var $turnier	= 0;
	var $snr		= 0;
	var $name       = '';
	var $birthYear  = '';
	var $geschlecht = '';
	var $verein     = '';
	var $email     	= '';
	var $twz        = 0;
	var $start_dwz  = 0;
	var $start_I0   = 0;
	var $FIDEelo    = 0;
	var $FIDEid     = 0;
	var $FIDEcco    = '';
	var $titel      = '';
	var $mgl_nr		= 0;
	var $PKZ		= '';
	var $zps		= '';
	var $tel_no		= '';
	var $account	= '';
	var $status		= 0;
	var $rankingPos	= 0;
	var $tlnrStatus = 0;
	var $anz_spiele = 0;
	var $sum_punkte		= 0;
	var $sum_bhlz		= 0;
	var $sum_busum		= 0;
	var $sum_sobe		= 0;
	var $koStatus		= 1;
	var $koRound		= 0;
	var $sum_wins		= 0;
	var $sumTiebr1		= 0;
	var $sumTiebr2		= 0;
	var $sumTiebr3		= 0;
	var $DWZ		= 0;
	var $I0			= 0;
	var $Punkte		= 0;
	var $Partien	= 0;
	var $We			= 0;
	var $Leistung	= 0;
	var $EFaktor	= 0;
	var $Niveau		= 0;
	var $published		= 0;
	var $checked_out	= 0;
	var $checked_out_time	= '1970-01-01 00:00:00';
	var $ordering		= 0;


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
