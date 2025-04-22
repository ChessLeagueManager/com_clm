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

class CLMViewRunde extends JViewLegacy
{
    public function display($tpl = 'pdf')
    {
        $model	  = $this->getModel();
        $liga     = $model->getCLMLiga();
        $this->liga = $liga;

        $model	  = $this->getModel();
        $mannschaft     = $model->getCLMMannschaft();
        $this->mannschaft = $mannschaft;

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
        $model	= $this->getModel();
        $einzel	= $model->getCLMEinzel();
        $this->einzel = $einzel;

        $model	= $this->getModel();
        $summe	= $model->getCLMSumme();
        $this->summe = $summe;

        $model	= $this->getModel();
        $ok	= $model->getCLMOK();
        $this->ok = $ok;

        $model	= $this->getModel();
        $punkte	= $model->getCLMPunkte();
        $this->punkte = $punkte;

        $model		= $this->getModel();
        $spielfrei	= $model->getCLMSpielfrei();
        $this->spielfrei = $spielfrei;

        // Paarungen Folgerunde
        $model	  = $this->getModel();
        $paar1     = $model->getCLMPaar1();
        $this->paar1 = $paar1;

        // Dokumenttyp setzen
        $document = JFactory::getDocument();
        $document->setMimeEncoding('application/pdf');

        parent::display($tpl);
    }
}
