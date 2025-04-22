<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2019 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access');

class CLMViewTermineForm extends JViewLegacy
{
    public function display($tpl = null)
    {

        // Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
        $model =   $this->getModel();

        clm_core::$load->load_css("icons_images");

        if (clm_core::$load->request_string('task', '') == 'edit') {
            $text = JText::_('EDIT');
        } else {
            $text = JText::_('NEW');
        }
        JToolBarHelper::title(JText::_('TITLE_TERMINE').": ".JText::_('TERMINE_TASK').': [ '. $text.' ]', 'clm_headmenu_termine.png');
        JToolBarHelper::save();
        JToolBarHelper::apply();
        JToolBarHelper::spacer();
        JToolBarHelper::cancel();

        // das MainMenu abschalten
        $_GET['hidemainmenu'] = 1 ;

        // Document/Seite
        $document = JFactory::getDocument();

        // JS-Array jtext -> Fehlertexte
        $document->addScriptDeclaration("var jserror = new Array();");
        $document->addScriptDeclaration('jserror["enter_name"] = "'.JText::_("TERMINE_ERROR_NAME").'";');
        $document->addScriptDeclaration('jserror["enter_startdate"] = "'.JText::_("TERMINE_ERROR_STARTDATE").'";');
        $document->addScriptDeclaration('jserror["dont_starttime"] = "'.JText::_("TERMINE_ERROR_STARTTIME").'";');
        $document->addScriptDeclaration('jserror["dont_endtime"] = "'.JText::_("TERMINE_ERROR_ENDTIME").'";');
        $document->addScriptDeclaration('jserror["dont_enddate"] = "'.JText::_("TERMINE_ERROR_ENDDATE").'";');
        $document->addScriptDeclaration('jserror["enddate_wrong"] = "'.JText::_("TERMINE_ERROR_ENDDATE_WRONG").'";');
        $document->addScriptDeclaration('jserror["dont_allday"] = "'.JText::_("TERMINE_ERROR_ALLDAY").'";');
        $document->addScriptDeclaration('jserror["dont_noendtime"] = "'.JText::_("TERMINE_ERROR_NOENDTIME").'";');

        // Script
        $document->addScript(CLM_PATH_JAVASCRIPT.'termineform.js');

        // Auswahlfelder durchsuchbar machen
        clm_core::$load->load_js("suche_liste");

        // Daten an Template übergeben
        $this->user = $model->user;
        $this->termine = $model->termine;
        $this->form = $model->form;


        parent::display();

    }

}
