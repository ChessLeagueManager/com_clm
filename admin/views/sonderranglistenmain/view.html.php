<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access');

class CLMViewSonderranglistenMain extends JViewLegacy
{
    public function display($tpl = null)
    {

        $mainframe	= JFactory::getApplication();
        $option 	= clm_core::$load->request_string('option');

        //Daten vom Model
        $state = $this->get('State');
        $sonderranglisten =  $this->get('Sonderranglisten');
        $turniere =  $this->get('Turniere');
        $pagination =  $this->get('Pagination');
        $user 	= $this->get('User');

        $filter_saisons	 =  $this->get('FilterSaisons');
        $filter_turniere =  $this->get('FilterTurniere');

        //Turnier vorhanden
        if (count($turniere) == 0) {
            $turnierExists = false;
        } else {
            $turnierExists = true;
        }

        //Toolbar
        clm_core::$load->load_css("icons_images");
        JToolBarHelper::title(JText::_('TITLE_SPECIALRANKINGS'), 'clm_headmenu_sonderranglisten.png');

        if ($turnierExists) {
            JToolBarHelper::publishList('publish');
            JToolBarHelper::unpublishList();
            JToolBarHelper::deleteList();
            JToolBarHelper::editList();
            JToolBarHelper::addNew();
            JToolBarHelper::custom('copy_set', 'copy.png', 'copy_f2.png', JText::_('SP_RANKING_COPY'), false);
        }

        //		JHtml::_('behavior.tooltip');
        require_once(JPATH_COMPONENT_SITE . DS . 'includes' . DS . 'tooltip.php');

        // Auswahlfelder durchsuchbar machen
        clm_core::$load->load_js("suche_liste");
        //CLM parameter auslesen
        $clm_config = clm_core::$db->config();
        if ($clm_config->field_search == 1) {
            $field_search = "js-example-basic-single";
        } else {
            $field_search = "inputbox";
        }
        //Suche und Filter
        $filter_saison		= $state->get('filter_saison');
        $filter_turnier		= $state->get('filter_turnier');
        $search 			= $state->get('search');

        //Suche
        $lists['search'] = $search;

        //Sortierung
        $lists['order_Dir'] = $state->get('filter_order_Dir');
        $lists['order']     = $state->get('filter_order');


        //Filter
        $options_filter_tur[]		= JHtml::_('select.option', '', JText::_('SPECIALRANKINGS_TOURNAMENTS'));
        foreach ($filter_turniere as $tur) {
            $options_filter_tur[]		= JHtml::_('select.option', $tur->id, $tur->name);
        }
        //		$lists['filter_turnier']	= JHtml::_('select.genericlist', $options_filter_tur, 'filter_turnier', 'class="js-example-basic-single" onchange="this.form.submit();"', 'value', 'text', $filter_turnier );
        $lists['filter_turnier']	= JHtml::_('select.genericlist', $options_filter_tur, 'filter_turnier', 'class="'.$field_search.'" onchange="this.form.submit();"', 'value', 'text', $filter_turnier);

        $options_filter_sai[]		= JHtml::_('select.option', '', JText::_('SPECIALRANKINGS_SEASONS'));
        foreach ($filter_saisons as $sai) {
            $options_filter_sai[]		= JHtml::_('select.option', $sai->id, $sai->name);
        }
        //		$lists['filter_saison']	= JHtml::_('select.genericlist', $options_filter_sai, 'filter_saison', 'class="js-example-basic-single" onchange="this.form.submit();"', 'value', 'text', $filter_saison );
        $lists['filter_saison']	= JHtml::_('select.genericlist', $options_filter_sai, 'filter_saison', 'class="'.$field_search.'" onchange="this.form.submit();"', 'value', 'text', $filter_saison);


        //Reihenfolge
        if ($lists['search'] != '' or ($lists['order'] != 'ordering' and $lists['order'] != 'turnier')) {
            $ordering = false;
        } else {
            $ordering = true;
        }


        //Daten an Template
        $this->sonderranglisten = $sonderranglisten;
        $this->lists = $lists;
        $this->user = $user;
        $this->pagination = $pagination;
        $this->ordering = $ordering;
        $this->turnierExists = $turnierExists;

        parent::display($tpl);
    }
}
