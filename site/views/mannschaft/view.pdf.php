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

class CLMViewMannschaft extends JViewLegacy
{
    public function display($tpl = 'pdf')
    {
        $model	  = $this->getModel();
        $mannschaft     = $model->getCLMMannschaft();
        $this->mannschaft = $mannschaft;

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

        //neu Einzelergebnisse (klkl)
        $model	  = $this->getModel();
        $einzel     = $model->getCLMEinzel();
        $this->einzel = $einzel;

        //neu Saison (klkl)
        $model	  = $this->getModel();
        $saison     = $model->getCLMSaison();
        $this->saison = $saison;

        // Dokumenttyp setzen
        $document = JFactory::getDocument();
        $document->setMimeEncoding('application/pdf');

        parent::display($tpl);
    }
}
