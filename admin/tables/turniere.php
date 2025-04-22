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

class TableCLMTurniere extends JTable
{
    public $id			= 0;
    public $name		= '';
    public $sid		= 0;
    public $dateStart 	= '1970-01-01';
    public $dateEnd 	= '1970-01-01';
    public $catidAlltime = 0;
    public $catidEdition = 0;
    public $typ		= 0;
    public $tiebr1		= 0;
    public $tiebr2		= 0;
    public $tiebr3		= 0;
    public $rnd		= 0; // Bugfix HF
    public $teil		= 0;
    public $runden		= 0;
    public $dg			= 1;
    public $tl			= 0;
    public $bezirk		= '';
    public $bezirkTur = '1';
    public $vereinZPS = null;
    public $published		= 0;
    public $started		= 0;
    public $finished		= 0;
    // var $invitationText = ''; // soll nicht aus turform heraus gelöscht werden...
    public $bemerkungen	= '';
    public $bem_int		= '';
    public $checked_out	= null;
    public $checked_out_time	= null;
    public $ordering		= 0;
    public $params 		= '';
    public $sieg			= 1.0;
    public $siegs			= 1.0;
    public $remis 			= 0.5;
    public $remiss			= 0.5;
    public $nieder			= 0.0;
    public $niederk 		= 0.0;
    public $dateRegistration 	= '1970-01-01';

    public function __construct(&$_db)
    {
        parent::__construct('#__clm_turniere', 'id', $_db);
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
    public function checkData()
    {

        // aktuelle Turnierdaten laden
        $tournament = new CLMTournament($this->id, true);

        if (trim($this->name) == '') { // Name vorhanden
            $this->setError(CLMText::errorText('NAME', 'MISSING'));
            return false;

        } elseif ($this->sid <= 0) { // SaisonID > 0
            $this->setError(CLMText::errorText('SEASON', 'IMPOSSIBLE'));
            return false;

        } elseif (!is_numeric($this->teil)) { // Teilnehmerzahl = Zahl
            $this->setError(CLMText::errorText('PARTICIPANT_COUNT', 'NOTANUMBER'));
            return false;
        } elseif ($this->teil <= 1) { // Teilnehmerzahl >= 2
            $this->setError(CLMText::errorText('PARTICIPANT_COUNT', 'TOOLOW'));
            return false;
        } elseif ($this->teil > 500) { // Teilnehmerzahl
            $this->setError(CLMText::errorText('PARTICIPANT_COUNT', 'TOOBIG'));
            return false;
        } elseif ($this->teil < $tournament->getPlayersIn()) {
            $this->setError(CLMText::errorText('PARTICIPANT_COUNT', 'MOREPLAYERSIN'));
            return false;

        } elseif (!is_numeric($this->runden)) { // Runden = Zahl
            $this->setError(CLMText::errorText('ROUNDS_COUNT', 'NOTANUMBER'));
            return false;
        } elseif ($this->runden < 1) { // Runden >= 2
            $this->setError(CLMText::errorText('ROUNDS_COUNT', 'TOOLOW'));
            return false;
        } elseif ($this->runden > 50) { // Runden
            $this->setError(CLMText::errorText('ROUNDS_COUNT', 'TOOBIG'));
            return false;

        } elseif ($this->typ != 3 and ($this->dg < 1 or $this->dg > 4)) { // DG möglich
            $this->setError(CLMText::errorText('STAGE_COUNT', 'IMPOSSIBLE'));
            return false;


            // Runden schon erstellt? dann keine Änderung Typ, Runden, dg möglich!
        } elseif ($this->typ != $tournament->data->typ and $tournament->data->rnd == 1) {
            $this->setError(CLMText::errorText('MODUS', 'ROUNDSCREATED'));
            return false;
        } elseif ($this->runden != $tournament->data->runden and $tournament->data->rnd == 1) {
            $this->setError(CLMText::errorText('ROUNDS_COUNT', 'ROUNDSCREATED'));
            return false;
        } elseif ($this->dg != $tournament->data->dg and $tournament->data->rnd == 1) {
            $this->setError(CLMText::errorText('STAGE_COUNT', 'ROUNDSCREATED'));
            return false;
        } elseif ($this->teil != $tournament->data->teil and $tournament->data->rnd == 1) {
            $this->setError(CLMText::errorText('PARTICIPANT_COUNT', 'ROUNDSCREATED'));
            return false;

            /*		} elseif ($this->tl <= 0) {
                        $this->setError( CLMText::errorText('TOURNAMENT_DIRECTOR', 'MISSING') );
                        return false;
            */
        } elseif (
            ($this->tiebr2 != 0 and $this->tiebr2 == $this->tiebr1) or
            ($this->tiebr3 != 0 and ($this->tiebr3 == $this->tiebr1 or $this->tiebr3 == $this->tiebr2))
        ) {
            $this->setError(CLMText::errorText('TIEBREAKERS', 'NOTDISTINCT'));
            return false;
        }

        // Endtag gegegeben und Datum geändert?
        if ($this->dateEnd != '0000-00-00' and $this->dateEnd != '1970-01-01' and $this->dateEnd != '' and ($this->dateStart != $tournament->data->dateStart or $this->dateEnd != $tournament->data->dateEnd)) {
            // zerlegen
            list($startY, $startM, $startD) = explode("-", $this->dateStart);
            list($endY, $endM, $endD) = explode("-", $this->dateEnd);
            // Endtag kleiner Starttag?
            if (mktime(0, 0, 0, $startM, $startD, $startY) > mktime(0, 0, 0, $endM, $endD, $endY)) {
                $this->setError(CLMText::errorText('TOURNAMENT_DAYEND', 'TOOLOW'));
                return false;
            }
        }

        // wurde eine Feinwertung verändert?
        if ($this->tiebr1 != $tournament->data->tiebr1 or $this->tiebr2 != $tournament->data->tiebr2 or $this->tiebr3 != $tournament->data->tiebr3) {
            // Rangliste neu berechnen
            $tournament->setRankingPositions();
        }

        return true;

    }


}
