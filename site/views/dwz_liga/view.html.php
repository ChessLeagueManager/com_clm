<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2020 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

jimport('joomla.application.component.view');

class CLMViewDWZ_Liga extends JViewLegacy
{
    public function display($tpl = null)
    {
        $model		= $this->getModel();
        $liga		= $model->getCLMLiga();
        $this->liga = $liga;

        $model		= $this->getModel();
        $spieler	= $model->getCLMSpieler();
        $this->spieler = $spieler;

        $model		= $this->getModel();
        $dwz		= $model->getCLMdwz();
        $this->dwz = $dwz;

        $model		= $this->getModel();

        parent::display($tpl);
    }
}
