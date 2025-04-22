<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2022 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

class CLMModelTurPlayersMail extends JModelLegacy
{
    // benötigt für Pagination
    public function __construct()
    {

        parent::__construct();


        // user
        $this->user = JFactory::getUser();

        // get parameters
        $this->_getParameters();

        // get players
        $this->_getPlayersData();

        // get turnier
        $this->_getTurnierData();



    }


    // alle vorhandenen Parameter auslesen
    public function _getParameters()
    {

        if (!isset($this->param) or is_null($this->param)) {
            $this->param = array();
        }	// seit J 4.2 nötig um notice zu vermeiden
        // turnier_id
        $this->param['turnierid'] = clm_core::$load->request_int('turnierid');

    }


    public function _getPlayersData()
    {

        $query = 'SELECT * '
            . ' FROM #__clm_turniere_tlnr'
            . ' WHERE turnier = '.$this->param['turnierid']
        ;
        $this->_db->setQuery($query);
        $this->playersData = $this->_db->loadObjectList();

    }


    public function _getTurnierData()
    {

        $query = 'SELECT * '
            . ' FROM #__clm_turniere'
            . ' WHERE id = '.$this->param['turnierid']
        ;
        $this->_db->setQuery($query);
        $this->turnierData = $this->_db->loadObject();
        $query = 'SELECT * '
            . ' FROM #__clm_user'
            . ' WHERE jid = '.$this->turnierData->tl
            . ' AND sid = '.$this->turnierData->sid
        ;
        $this->_db->setQuery($query);
        $this->tlData = $this->_db->loadObject();

    }


}
