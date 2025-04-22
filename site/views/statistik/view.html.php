<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
jimport('joomla.application.component.view');

class CLMViewStatistik extends JViewLegacy
{
    public function display($tpl = null)
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

        //$model		= $this->getModel();
        //$spieler	= $model->getCLMSpieler();
        //$this->spieler = $spieler;

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
        $gbrett		= $model->getCLMGBrett();
        $this->gbrett = $gbrett;

        $model		= $this->getModel();
        $rbrett		= $model->getCLMRBrett();
        $this->rbrett = $rbrett;

        $model		= $this->getModel();
        $kbrett		= $model->getCLMKBrett();
        $this->kbrett = $kbrett;

        $model		= $this->getModel();
        $kgmannschaft	= $model->getCLMkgMannschaft();
        $this->kgmannschaft = $kgmannschaft;

        $model		= $this->getModel();
        $kvmannschaft	= $model->getCLMkvMannschaft();
        $this->kvmannschaft = $kvmannschaft;

        $document = JFactory::getDocument();

        // Title in Browser
        $headTitle = CLMText::composeHeadTitle(array( $liga[0]->name, JText::_('LEAGUE_STATISTIK') ));
        $document->setTitle($headTitle);

        /* Call the state object */
        //		$state = $this->get( 'state' );
        $mainframe = JFactory::getApplication();
        global $option;
        $lists['state'] = $mainframe->getUserStateFromRequest("$option.filter_state", 'filter_state', '', 'word');

        /* Get the values from the state object that were inserted in the model's construct function */
        $lists['order'] = $mainframe->getUserStateFromRequest("$option.filter_order", 'filter_order', 'Punkte', 'cmd'); // JRequest::getString('filter_order', 'a.id');
        $lists['order_Dir'] = $mainframe->getUserStateFromRequest("$option.filter_order_Dir", 'filter_order_Dir', 'desc', 'word');
        //		$lists['order']     = $state->get( 'filter_order_bl' );
        //		$lists['order_Dir'] = $state->get( 'filter_order_Dir_bl' );

        $this->lists = $lists;

        parent::display($tpl);
    }
}
