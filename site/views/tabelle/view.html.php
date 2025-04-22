<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2020 CLM Team. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

jimport('joomla.application.component.view');

class CLMViewTabelle extends JViewLegacy
{
    public function display($tpl = null)
    {
        $model	  = $this->getModel();
        $liga     = $model->getCLMLiga();
        $this->liga = $liga;

        $model	  = $this->getModel();
        $spielfrei     = $model->getCLMSpielfrei();
        $this->spielfrei = $spielfrei;

        $model	  = $this->getModel();
        $punkte     = $model->getCLMPunkte();
        $this->punkte = $punkte;

        /*		$model	  = $this->getModel();
                $dwzschnitt     = $model->getCLMDWZSchnitt();
                $this->dwzschnitt = $dwzschnitt;
        */
        parent::display($tpl);
    }
}
