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

class TableCLMTurnier_Runden extends JTable
{
    public $id			= null;
    public $sid		= null;
    public $name		= '';
    public $turnier		= '';
    public $dg			= '';
    public $nr			= '';
    public $datum		= '1970-01-01';
    public $startzeit		= '00:00:00';
    public $abgeschlossen	= '';
    public $tl_ok		= '';
    public $published		= 0;
    public $bemerkungen	= '';
    public $bem_int		= '';
    public $gemeldet		= '';
    public $editor			= '';
    public $zeit			= '1970-01-01 00:00:00';
    public $edit_zeit		= '1970-01-01 00:00:00';
    public $checked_out	= null;
    public $checked_out_time	= null;
    public $ordering		= null;

    public function __construct(&$_db)
    {
        parent::__construct('#__clm_turniere_rnd_termine', 'id', $_db);
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

        return true;
    }

    /**
    * bearbeitet und überprüft Daten
    *
    */
    public function checkData()
    {

        $this->name = trim($this->name);
        if (strlen($this->name) == 0) {
            $this->setError(CLMText::errorText('ROUND_NAME', 'MISSING'));
            return false;
        } elseif ($this->nr < 1) {
            $this->setError(CLMText::errorText('ROUND_NR', 'MISSING'));
            return false;
        }
        // weitere
        if ($this->tl_ok == 1) { // Bestätigung gesetzt?
            $tournamentRound = new CLMTournamentRound($this->turnier, $this->id);
            if (!$tournamentRound->checkResultsComplete()) {
                $this->setError(CLMText::errorText('RESULTS', 'INCOMPLETE'));
                return false;
            }

        }

        if ($this->startzeit != '') {
            if (!CLMText::isTime($this->startzeit, true, false)) {
                $this->setError(CLMText::errorText('RUNDE_STARTTIME', 'IMPOSSIBLE'));
                return false;

            }
        }


        return true;

    }


}
