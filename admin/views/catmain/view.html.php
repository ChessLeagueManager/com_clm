<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

class CLMViewCatMain extends JViewLegacy
{
    public function display($tpl = null)
    {

        // Die Toolbar erstellen, die über der Seite angezeigt wird
        JToolBarHelper::title(JText::_('JCATEGORIES'));

        // nur, wenn Admin
        if (clm_core::$access->getType() == 'admin' or clm_core::$access->getType() == 'tl') {
            JToolBarHelper::custom('add', 'new.png', 'new_f2.png', JText::_('ADD'), false);
            JToolBarHelper::custom('copy', 'copy.png', 'copy_f2.png', JText::_('JTOOLBAR_DUPLICATE'));
        }
        JToolBarHelper::editList();
        // nur, wenn Admin
        if (clm_core::$access->getType() == 'admin' or clm_core::$access->getType() == 'tl') {
            JToolBarHelper::spacer();
            JToolBarHelper::publishList();
            JToolBarHelper::unpublishList();
        }

        if (clm_core::$access->getType() === 'admin') {
            JToolBarHelper::spacer();
            JToolBarHelper::custom('delete', 'delete.png', 'delete_f2.png', JText::_('DELETE'), true);
        }

        // Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
        $model =   $this->getModel();



        // Daten an Template übergeben
        $this->user = $model->user;

        $this->categories = $model->categories;

        $this->param = $model->param;

        $this->form = $model->form;

        $this->pagination = $model->pagination;

        // zusätzliche Funktionalitäten
        //		JHtml::_('behavior.tooltip');
        require_once(JPATH_COMPONENT_SITE . DS . 'includes' . DS . 'tooltip.php');

        // Auswahlfelder durchsuchbar machen
        clm_core::$load->load_js("suche_liste");

        parent::display();

    }

}
