<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2024 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.cheesleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
jimport('joomla.application.component.view');

class CLMViewTurnier_Tabelle extends JViewLegacy
{
    public function display($tpl = null)
    {

        $config	= clm_core::$db->config();

        $model		= $this->getModel();

        $document = JFactory::getDocument();

        //		$document->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js');
        clm_core::$cms->addScript(clm_core::$url."includes/jquery-3.7.1.min.js");
        $document->addScript(JURI::base().'components/com_clm/javascript/updateTableHeaders.js');

        // Title in Browser
        $headTitle = CLMText::composeHeadTitle(array( $model->turnier->name, JText::_('TOURNAMENT_TABLE') ));
        $document->setTitle($headTitle);

        $this->turnier = $model->turnier;
        $tourn_linkclub = $config->tourn_linkclub;
        $this->tourn_linkclub = $tourn_linkclub;
        $this->players = $model->players;

        parent::display($tpl);

    }
}
