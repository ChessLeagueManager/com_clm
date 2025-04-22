<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2019 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
jimport('joomla.application.component.view');

class CLMViewTurnier_Rangliste extends JViewLegacy
{
    public function display($tpl = ch)
    {
        $model		= $this->getModel();
        $daten		= $model->getCLMTurnier();
        $this->daten = $daten;

        $model		= $this->getModel();
        $rang		= $model->getCLMRang();
        $this->rang = $rang;

        $model		= $this->getModel();
        $runde		= $model->getCLMRunde();
        $this->runde = $runde;

        parent::display($tpl);
    }
}
