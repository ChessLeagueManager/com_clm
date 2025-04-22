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

class TableCLMCategories extends JTable
{
    public $id				= 0;
    public $parentid		= 0;
    public $name		= '';
    public $sid		= 0;
    public $dateStart = '1970-01-01';
    public $dateEnd   = '1970-01-01';
    public $tl			= 0;
    public $bezirk		= '';
    public $bezirkTur = '1';
    public $vereinZPS = '';
    public $published		= 0;
    public $started		= 0;
    public $finished		= 0;
    // var $invitationText = ''; // soll nicht aus catform heraus gelöscht werden...
    public $bemerkungen	= '';
    public $bem_int		= '';
    public $checked_out	= null;
    public $checked_out_time	= null;
    public $ordering		= 0;
    public $params 		= '';

    public function __construct(&$_db)
    {
        parent::__construct('#__clm_categories', 'id', $_db);
    }

    /**
     * Overloaded check function
     *
     * @access public
     * @return boolean
     * @see JTable::check
     * @since 1.5
     */
    public function checkData()
    {

        // aktuelle Daten laden
        // $category = new CLMCategory($this->id, TRUE);

        if (trim($this->name) == '') { // Name vorhanden
            $this->setError(CLMText::errorText('NAME', 'MISSING'));
            return false;

            /*
            } elseif ($this->sid <= 0) { // SaisonID > 0
                $this->setError( CLMText::errorText('SEASON', 'IMPOSSIBLE') );
                return false;

            } elseif ($this->tl <= 0) {
                $this->setError( CLMText::errorText('TOURNAMENT_DIRECTOR', 'MISSING') );
                return false;
            */

        }

        return true;

    }


}
