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

class CLMViewInfo extends JViewLegacy
{
    public function display($tpl = null)
    {
        $model		= $this->getModel();
        $saison		= $model->getCLMSaison();
        $this->saison = $saison;

        $model		= $this->getModel();
        $remis		= $model->getCLMRemis();
        $this->remis = $remis;

        $model		= $this->getModel();
        $kampflos	= $model->getCLMKampflos();
        $this->kampflos = $kampflos;

        $model		= $this->getModel();
        $heim		= $model->getCLMHeim();
        $this->heim = $heim;

        $model		= $this->getModel();
        $gast		= $model->getCLMGast();
        $this->gast = $gast;

        $model		= $this->getModel();
        $gesamt		= $model->getCLMGesamt();
        $this->gesamt = $gesamt;

        $model		= $this->getModel();
        $spieler	= $model->getCLMSpieler();
        $this->spieler = $spieler;

        $model		= $this->getModel();
        $mannschaft	= $model->getCLMMannschaft();
        $this->mannschaft = $mannschaft;

        $model		= $this->getModel();
        $brett		= $model->getCLMBrett();
        $this->brett = $brett;

        $model		= $this->getModel();
        $wbrett		= $model->getCLMWBrett();
        $this->wbrett = $wbrett;

        $model		= $this->getModel();
        $sbrett		= $model->getCLMSBrett();
        $this->sbrett = $sbrett;

        $model		= $this->getModel();
        $rbrett		= $model->getCLMRBrett();
        $this->rbrett = $rbrett;

        $model		= $this->getModel();
        $kbrett		= $model->getCLMKBrett();
        $this->kbrett = $kbrett;

        $html	= clm_core::$load->request_string('html', '1');
        if ($html != "1") {
            $document = JFactory::getDocument();
            $document->setMimeEncoding('text/css');
        }

        parent::display($tpl);
    }
}
