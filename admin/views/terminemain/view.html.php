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

class CLMViewTermineMain extends JViewLegacy
{
    public function display($tpl = null)
    {


        // Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
        $model =   $this->getModel();
        $_GET['hidemainmenu'] = 1;
        clm_core::$load->load_css("icons_images");
        JToolBarHelper::title(JText::_('TITLE_TERMINE'), 'clm_headmenu_termine.png');

        JToolBarHelper::custom('catmain', 'forward.png', 'forward_f2.png', JText::_('JCATEGORIES'), false);

        JToolBarHelper::addNew();
        JToolBarHelper::custom('copy', 'copy.png', 'copy_f2.png', JText::_('TERMINE_COPY'));

        JToolBarHelper::spacer();
        JToolBarHelper::editList();

        JToolBarHelper::spacer();
        JToolBarHelper::publishList();
        JToolBarHelper::unpublishList();

        $clmAccess = clm_core::$access;
        if ($clmAccess->access('BE_event_delete') !== false) {
            JToolBarHelper::spacer();
            JToolBarHelper::custom('delete', 'delete.png', 'delete_f2.png', JText::_('TERMINE_DELETE'));
        }
        JToolBarHelper::custom('import', 'upload.png', 'upload_f2.png', JText::_('TERMINE_IMPORT'), false);
        JToolBarHelper::custom('export', 'copy.png', 'copy_f2.png', JText::_('TERMINE_EXPORT'), false);
        JToolBarHelper::custom('download', 'download.png', 'download_f2.png', JText::_('TERMINE_DOWNLOAD'), false);

        // Daten an Template übergeben
        $this->user = $model->user;
        $this->termine = $model->termine;
        $this->param = $model->param;
        $this->pagination = $model->pagination;

        // zusätzliche Funktionalitäten
        //		JHtml::_('behavior.tooltip');
        require_once(JPATH_COMPONENT_SITE . DS . 'includes' . DS . 'tooltip.php');

        // Auswahlfelder durchsuchbar machen
        clm_core::$load->load_js("suche_liste");

        parent::display();
    }

}
