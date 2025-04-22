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

/**
 * Turnier
*/

class CLMTournamentRound extends stdClass
{
    public function __construct($turnierid, $roundid)
    {
        // $turnierid übergibt id des Turniers
        // $roundid übergibt id des Runde (nicht RundenNr!)

        // DB
        $this->_db				= JFactory::getDBO();

        // turnierid
        $this->turnierid = $turnierid;
        $this->roundid = $roundid;

        // Daten der Runde holen
        $this->_getRoundData();

    }


    public function _getRoundData()
    {

        $this->round = JTable::getInstance('turnier_runden', 'TableCLM');
        $this->round->load($this->roundid);

    }


    /**
    * check, ob alle Matches Ergebnisse haben
    * TODO: später durch ein Flag in der DB ersetzen
    */
    public function checkResultsComplete()
    {
        // Rückgabe:
        // TRUE - alle Ergebnisse eingetragen
        // FALSE - fehlende Ergebnisse
        // DB

        $query = "SELECT COUNT(*) FROM #__clm_turniere_rnd_spl"
                . " WHERE turnier = ".$this->turnierid." AND runde = ".$this->round->nr." AND dg = ".$this->round->dg." AND ergebnis IS NULL"
        ;
        $this->_db->setQuery($query);
        $ergebnisNullCount = $this->_db->loadResult();
        if ($ergebnisNullCount > 0) {
            return false;
        }
        return true;

    }



}
