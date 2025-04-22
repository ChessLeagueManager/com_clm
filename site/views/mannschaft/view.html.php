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

jimport('joomla.application.component.view');

class CLMViewMannschaft extends JViewLegacy
{
    public function display($tpl = null)
    {
        $config = clm_core::$db->config();
        $googlemaps     = $config->googlemaps;
        $googlemaps_msch     = $config->googlemaps_msch;

        if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] != 'off') {
            $prot = 'https';
        } else {
            $prot = 'http';
        }
        $document = JFactory::getDocument();
        if ($googlemaps == 1) {
            if ($googlemaps_msch == 1) { //Load Leaflet
                $document->addScript($prot.'://unpkg.com/leaflet@1.9.4/dist/leaflet.js');
                $document->addStyleSheet($prot.'://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
            } elseif ($googlemaps_msch == 3) { //Load OSM
                $document->addScript($prot.'://cdn.rawgit.com/openlayers/openlayers.github.io/master/en/v5.3.0/build/ol.js');
                $document->addStyleSheet($prot.'://cdn.rawgit.com/openlayers/openlayers.github.io/master/en/v5.3.0/css/ol.css');
            }
        }

        //		$document->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js');
        clm_core::$cms->addScript(clm_core::$url."includes/jquery-3.7.1.min.js");
        $document->addScript(JURI::base().'components/com_clm/javascript/updateTableHeaders.js');

        $model	  = $this->getModel();
        $mannschaft     = $model->getCLMMannschaft();
        $this->mannschaft = $mannschaft;

        $model	  = $this->getModel();
        $vereine     = $model->getCLMVereine();
        $this->vereine = $vereine;

        $model	  = $this->getModel();
        $count     = $model->getCLMCount();
        $this->count = $count;

        $model	  = $this->getModel();
        $bp     = $model->getCLMBP();
        $this->bp = $bp;

        $model	  = $this->getModel();
        $sumbp     = $model->getCLMSumBP();
        $this->sumbp = $sumbp;

        $model	  = $this->getModel();
        $plan     = $model->getCLMSumPlan();
        $this->plan = $plan;

        $model	  = $this->getModel();
        $termin     = $model->getCLMTermin();
        $this->termin = $termin;

        $model	  = $this->getModel();
        $saison     = $model->getCLMSaison();
        $this->saison = $saison;

        $model	  = $this->getModel();
        $einzel     = $model->getCLMEinzel();
        $this->einzel = $einzel;

        parent::display($tpl);
    }
}
