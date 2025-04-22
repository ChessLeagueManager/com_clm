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

class CLMViewPaarungsliste extends JViewLegacy
{
    public function display($tpl = null)
    {
        $model	  = $this->getModel();
        $liga     = $model->getCLMLiga();
        $this->liga = $liga;

        $model	  = $this->getModel();
        $termin     = $model->getCLMTermin();
        $this->termin = $termin;

        $model	  = $this->getModel();
        $paar     = $model->getCLMPaar();
        $this->paar = $paar;

        /*		$model	  = $this->getModel();
                $dwzschnitt     = $model->getCLMDWZSchnitt();
                $this->dwzschnitt = $dwzschnitt;
        */
        /*		$model	  = $this->getModel();
                $dwzgespielt     = $model->getCLMDWZgespielt();
                $this->dwzgespielt = $dwzgespielt;
        */
        $model	  = $this->getModel();
        $summe     = $model->getCLMSumme();
        $this->summe = $summe;

        $model	  = $this->getModel();
        $rundensumme     = $model->getCLMRundensumme();
        $this->rundensumme = $rundensumme;

        parent::display($tpl);
    }
}
