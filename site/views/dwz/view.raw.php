<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2023 CLM Team. All rights reserved
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
    public function display($tpl = "raw")
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

        $html	= clm_core::$load->request_string('html', '1');
        if ($html != "1") {
            $document = JFactory::getDocument();
            $document->setMimeEncoding('text/css');
        }
        /* Call the state object */
        $mainframe = JFactory::getApplication();
        global $option;
        $lists['state'] = $mainframe->getUserStateFromRequest("$option.filter_state", 'filter_state', '', 'word');

        /* Get the values from the state object that were inserted in the model's construct function */
        $lists['order'] = $mainframe->getUserStateFromRequest("$option.filter_order", 'filter_order', 'DWZ', 'cmd'); // JRequest::getString('filter_order', 'a.id');
        $lists['order_Dir'] = $mainframe->getUserStateFromRequest("$option.filter_order_Dir", 'filter_order_Dir', 'desc', 'word');

        $this->lists = $lists;

        parent::display($tpl);
    }
}
