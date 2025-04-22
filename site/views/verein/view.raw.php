<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2022 CLM Team. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

jimport('joomla.application.component.view');

class CLMViewVerein extends JViewLegacy
{
    public function display($tpl = "raw")
    {
        $config = clm_core::$db->config();
        $googlemaps_api = $config->googlemaps_api;
        $googlemaps     = $config->googlemaps;

        $model	  = $this->getModel();
        $verein     = $model->getCLMVerein();
        $this->verein = $verein;

        $model	  = $this->getModel();
        $vereinstats     = $model->getCLMVereinstats();
        $this->vereinstats = $vereinstats;

        $model	  = $this->getModel();
        $mannschaft     = $model->getCLMMannschaft();
        $this->mannschaft = $mannschaft;

        $model	  = $this->getModel();
        $vereinsliste     = $model->getCLMVereinsliste();
        $this->vereinsliste = $vereinsliste;

        $model	  = $this->getModel();
        $saisons     = $model->getCLMSaisons();
        $this->saisons = $saisons;

        $model	  = $this->getModel();
        $turniere     = $model->getCLMTurniere();
        $this->turniere = $turniere;

        $model	  = $this->getModel();
        $row     = $model->getCLMData();
        $this->row = $row;

        $model	  = $this->getModel();
        $name     = $model->getCLMName();
        $this->name = $name;

        $model	  = $this->getModel();
        $clmuser     = $model->getCLMCLMuser();
        $this->clmuser = $clmuser;

        $html	= clm_core::$load->request_string('html', '1');
        if ($html != "1") {
            $document = JFactory::getDocument();
            $document->setMimeEncoding('text/css');
        }

        parent::display($tpl);
    }
}
