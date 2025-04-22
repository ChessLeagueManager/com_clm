<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
// no direct access
defined('_JEXEC') or die('Restricted access');

class TableCLMTurnier_Teilnehmer extends JTable
{
    public $id			= 0;
    public $sid		= 0;
    public $turnier	= 0;
    public $snr		= 0;
    public $name       = '';
    public $birthYear  = '';
    public $geschlecht = '';
    public $verein     = '';
    public $email     	= '';
    public $twz        = 0;
    public $start_dwz  = 0;
    public $start_I0   = 0;
    public $FIDEelo    = 0;
    public $FIDEid     = 0;
    public $FIDEcco    = '';
    public $titel      = '';
    public $mgl_nr		= 0;
    public $PKZ		= '';
    public $zps		= '';
    public $tel_no		= '';
    public $account	= '';
    public $status		= 0;
    public $rankingPos	= 0;
    public $tlnrStatus = 0;
    public $anz_spiele = 0;
    public $sum_punkte		= 0;
    public $sum_bhlz		= 0;
    public $sum_busum		= 0;
    public $sum_sobe		= 0;
    public $koStatus		= 1;
    public $koRound		= 0;
    public $sum_wins		= 0;
    public $sumTiebr1		= 0;
    public $sumTiebr2		= 0;
    public $sumTiebr3		= 0;
    public $DWZ		= 0;
    public $I0			= 0;
    public $Punkte		= 0;
    public $Partien	= 0;
    public $We			= 0;
    public $Leistung	= 0;
    public $EFaktor	= 0;
    public $Niveau		= 0;
    public $published		= 0;
    public $checked_out	= null;
    public $checked_out_time	= null;
    public $ordering		= 0;


    public function __construct(&$_db)
    {
        parent::__construct('#__clm_turniere_tlnr', 'id', $_db);
    }

    /**
     * Overloaded check function
     *
     * @access public
     * @return boolean
     * @see JTable::check
     * @since 1.5
     */
    public function check()
    {
        if (trim($this->name) == '') { // Name vorhanden
            $this->setError(CLMText::errorText('NAME', 'MISSING'));
            return false;
        } elseif (!is_numeric($this->start_dwz)) { // TWZ = Zahl
            $this->setError(CLMText::errorText('RATING', 'NOTANUMBER'));
            return false;
        } elseif (!is_numeric($this->FIDEelo)) { // TWZ = Zahl
            $this->setError(CLMText::errorText('FIDE_ELO', 'NOTANUMBER'));
            return false;
        } elseif (!is_numeric($this->twz)) { // TWZ = Zahl
            $this->setError(CLMText::errorText('TWZ', 'NOTANUMBER'));
            return false;
        }

        return true;
    }
}
