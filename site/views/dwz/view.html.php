<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2024 CLM Team. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
jimport('joomla.application.component.view');

class CLMViewDWZ extends JViewLegacy
{
    public function display($tpl = null)
    {
        $model	  = $this->getModel();
        $liga     = $model->getCLMLiga();
        $this->liga = $liga;

        $model = $this->getModel();
        $zps = $model->getCLMzps();
        $this->zps = $zps;

        $model	  = $this->getModel();
        $vereinsliste     = $model->getCLMVereinsliste();
        $this->vereinsliste = $vereinsliste;

        $model	  = $this->getModel();
        $saisons     = $model->getCLMSaisons();
        $this->saisons = $saisons;

        $document = JFactory::getDocument();
        //		$document->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js');
        clm_core::$cms->addScript(clm_core::$url."includes/jquery-3.7.1.min.js");
        $document->addScript(JURI::base().'components/com_clm/javascript/updateTableHeaders.js');

        /* Call the state object */
        //		$state = $this->get( 'state' );
        $mainframe = JFactory::getApplication();
        global $option;
        $lists['state'] = $mainframe->getUserStateFromRequest("$option.filter_state", 'filter_state', '', 'word');

        /* Get the values from the state object that were inserted in the model's construct function */
        $lists['order'] = $mainframe->getUserStateFromRequest("$option.filter_order", 'filter_order', 'DWZ', 'cmd'); // JRequest::getString('filter_order', 'a.id');
        $lists['order_Dir'] = $mainframe->getUserStateFromRequest("$option.filter_order_Dir", 'filter_order_Dir', 'desc', 'word');
        //$lists['order']     = $state->get( 'filter_order_dwz' );
        //$lists['order_Dir'] = $state->get( 'filter_order_Dir_dwz' );


        $this->lists = $lists;

        parent::display($tpl);
    }
}
