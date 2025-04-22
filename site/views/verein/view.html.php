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
jimport('joomla.application.component.view');

class CLMViewVerein extends JViewLegacy
{
    public function display($tpl = null)
    {

        $config = clm_core::$db->config();
        $googlemaps     = $config->googlemaps;
        $googlemaps_ver     = $config->googlemaps_ver;

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

        if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] != 'off') {
            $prot = 'https';
        } else {
            $prot = 'http';
        }
        $document = JFactory::getDocument();

        if ($googlemaps == 1) {
            if ($googlemaps_ver == 1) { //Load Leaflet
                $document->addScript($prot.'://unpkg.com/leaflet@1.9.4/dist/leaflet.js');
                $document->addStyleSheet($prot.'://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
            } elseif ($googlemaps_ver == 3) { //Load OSM
                $document->addScript($prot.'://cdn.rawgit.com/openlayers/openlayers.github.io/master/en/v5.3.0/build/ol.js');
                $document->addStyleSheet($prot.'://cdn.rawgit.com/openlayers/openlayers.github.io/master/en/v5.3.0/css/ol.css');
            }
        }
        // Title in Browser
        if (isset($verein[0])) {
            $headTitle = CLMText::composeHeadTitle(array( $verein[0]->name ));
            $document->setTitle($headTitle);
        }

        parent::display($tpl);
    }
}
