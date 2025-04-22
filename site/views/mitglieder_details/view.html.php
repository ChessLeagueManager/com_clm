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

class CLMViewMitglieder_Details extends JViewLegacy
{
    public function display($tpl = null)
    {
        $model	= $this->getModel();
        $spieler	= $model->getCLMSpieler();
        $this->spieler = $spieler;

        $model	= $this->getModel();
        $verein	= $model->getCLMVerein();
        $this->verein = $verein;

        $model	= $this->getModel();
        $clmuser = $model->getCLMCLMuser();
        $this->clmuser = $clmuser;

        parent::display($tpl);
    }
}
