<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2022 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

class CLMModelTurRegistrationEdit extends JModelLegacy
{
    // benötigt für Pagination
    public function __construct()
    {

        parent::__construct();

        // user
        $this->user = JFactory::getUser();

        // get parameters
        $this->_getParameters();

        // get Player
        $this->_getRegistrationData();

        // get turnier
        $this->_getTurnierData();

        // get max. start number
        $this->_getTurnierSnrMax();

    }


    // alle vorhandenen Parameter auslesen
    public function _getParameters()
    {

        if (!isset($this->param) or is_null($this->param)) {
            $this->param = array();
        }	// seit J 4.2 nötig um notice zu vermeiden
        // registrationid
        $this->param['registrationid'] = clm_core::$load->request_int('registrationid');

    }


    public function _getRegistrationData()
    {

        $query = 'SELECT * '
            . ' FROM #__clm_online_registration'
            . ' WHERE id = '.$this->param['registrationid']
        ;
        $this->_db->setQuery($query);
        $this->registrationData = $this->_db->loadObject();

    }


    public function _getTurnierData()
    {

        $query = 'SELECT * '
            . ' FROM #__clm_turniere'
            . ' WHERE id = '.$this->registrationData->tid
        ;
        $this->_db->setQuery($query);
        $this->turnierData = $this->_db->loadObject();

    }

    public function _getTurnierSnrMax()
    {

        $query = 'SELECT MAX(snr) as snrmax '
            . ' FROM #__clm_turniere_tlnr'
            . ' WHERE turnier = '.$this->registrationData->tid
        ;
        $this->_db->setQuery($query);
        $this->turnierSnrMax = $this->_db->loadObject();
        if (!isset($this->turnierSnrMax->snrmax)) {
            $this->turnierSnrMax->snrmax = 0;
        }
    }

}
