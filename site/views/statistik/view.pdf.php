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

class CLMViewStatistik extends JViewLegacy
{
    public function display($tpl = 'pdf')
    {
        $model		= $this->getModel();
        $liga		= $model->getCLMliga();
        $this->liga = $liga;

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
        $bestenliste	= $model->getCLMBestenliste();
        $this->bestenliste = $bestenliste;

        $model		= $this->getModel();
        $mannschaft	= $model->getCLMMannschaft();
        $this->mannschaft = $mannschaft;

        $model		= $this->getModel();
        $brett		= $model->getCLMBrett();
        $this->brett = $brett;

        $model		= $this->getModel();
        $rbrett		= $model->getCLMRBrett();
        $this->rbrett = $rbrett;

        $model		= $this->getModel();
        $kbrett		= $model->getCLMKBrett();
        $this->kbrett = $kbrett;

        // Dokumenttyp setzen
        $document = JFactory::getDocument();
        $document->setMimeEncoding('application/pdf');

        parent::display($tpl);
    }
}
