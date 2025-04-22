<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2021 All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

class CLMViewAccessgroupsMain extends JViewLegacy
{
    public function display($tpl = null)
    {

        $mainframe	= JFactory::getApplication();
        $option 	= clm_core::$load->request_string('option', '');

        //Daten vom Model
        $state = $this->get('State');
        $accessgroups =  $this->get('Accessgroups');

        $pagination =  $this->get('Pagination');
        $user 	= $this->get('User');

        //Benutzergruppen vorhanden
        if (count($accessgroups) == 0) {
            $accessgroupExists = false;
        } else {
            $accessgroupExists = true;
        }

        //Toolbar
        clm_core::$load->load_css("icons_images");
        JToolBarHelper::title(JText::_('TITLE_ACCESSGROUPS'), 'clm_headmenu_sonderranglisten.png');

        if ($accessgroupExists) {
            JToolBarHelper::publishList('publish');
            JToolBarHelper::unpublishList();
            JToolBarHelper::deleteList();
            JToolBarHelper::editList();
            JToolBarHelper::custom('copy', 'copy.png', 'copy_f2.png', JText::_('LEAGUE_BUTTON_4'));
            JToolBarHelper::addNew();
        }

        //		JHtml::_('behavior.tooltip');
        require_once(JPATH_COMPONENT_SITE . DS . 'includes' . DS . 'tooltip.php');

        //Suche
        $search 			= $state->get('search');
        $lists['search'] = $search;

        //Sortierung
        $lists['order_Dir'] = $state->get('filter_order_Dir');
        $lists['order']     = $state->get('filter_order');

        //Reihenfolge
        if ($lists['search'] != '' or ($lists['order'] != 'ordering' and $lists['order'] != 'accessgroup')) {
            $ordering = false;
        } else {
            $ordering = true;
        }


        //Daten an Template
        $this->accessgroups = $accessgroups;
        $this->lists = $lists;
        $this->user = $user;
        $this->pagination = $pagination;
        $this->ordering = $ordering;
        $this->accessgroupExists = $accessgroupExists;

        parent::display($tpl);
    }
}
