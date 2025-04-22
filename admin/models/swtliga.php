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
defined('_JEXEC') or die('Restricted access');

class CLMModelSWTliga extends JModelLegacy
{
    public function __construct()
    {
        parent::__construct();
        $filter_saison	= clm_core::$load->request_int('filter_saison', $this->_getAktuelleSaison());

        $this->setState('filter_saison', $filter_saison);
    }

    public function getSaisons()
    {
        if (empty($this->_saisons)) {
            $query =  ' SELECT id, name FROM #__clm_saison'
//					. ' WHERE published = 1 AND archiv = 0';
                    . ' WHERE published = 1';
            $this->_saisons = $this->_getList($query);
        }
        return $this->_saisons;
    }

    public function getLigen()
    {
        $filter_saison	= clm_core::$load->request_int('filter_saison', $this->_getAktuelleSaison());
        if (empty($this->_ligen)) {
            $query =  ' SELECT 
							id,
							name
						FROM 
							#__clm_liga 
						WHERE 
							sid = '.$filter_saison;
            //							sid = '.$this->getState( 'filter_saison' ).'';
            $this->_ligen = $this->_getList($query);
        }
        return $this->_ligen;
    }

    public function _getAktuelleSaison()
    {
        if (empty($this->_aktuelleSaison)) {

            $query =  ' SELECT `id`'
                    . ' FROM #__clm_saison'
                    . ' WHERE published = 1 AND archiv = 0';
            $var = $this->_getList($query);

        }

        if (isset($var[0]->id)) {
            return $var[0]->id;
        }

        return 0;

    }

}
