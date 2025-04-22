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
// no direct access
defined('_JEXEC') or die('Restricted access');

class CLMControllerCatForm extends JControllerLegacy
{
    // Konstruktor
    public function __construct($config = array())
    {

        parent::__construct($config);

        $this->app = JFactory::getApplication();

        // Register Extra tasks
        $this->registerTask('apply', 'save');

    }


    public function save()
    {

        $result = $this->_saveDo();

        if ($result[0]) { // erfolgreich?

            if ($result[1]) { // neue Kategorie?
                $this->app->enqueueMessage(JText::_('CATEGORY_CREATED'));
            } else {
                $this->app->enqueueMessage(JText::_('CATEGORY_EDITED'));
            }
        }
        // sonst Fehlermeldung schon geschrieben

        $task = clm_core::$load->request_string('task');

        $adminLink = new AdminLink();
        // wenn 'apply', weiterleiten in form
        if ($task == 'save' or !$result[0]) {
            // Weiterleitung in Liste
            $adminLink->view = "catmain"; // WL in Liste
        } else {
            // Weiterleitung bleibt im Formular
            $adminLink->view = "catform"; // WL in Liste
            $adminLink->more = array('task' => 'edit', 'id' => $result[2]);
        }
        $adminLink->makeURL();
        $this->app->redirect($adminLink->url);

    }


    public function _saveDo()
    {

        // Check for request forgeries
        defined('_JEXEC') or die('Invalid Token');

        if (clm_core::$access->getType() != 'admin' and clm_core::$access->getType() != 'tl') {
            $this->app->enqueueMessage(JText::_('SECTION_NO_ACCESS'), 'warning');
            return array(false);
        }

        // Task
        $task = clm_core::$load->request_string('task');

        // Instanz der Tabelle
        $row = JTable::getInstance('categories', 'TableCLM');

        $post = $_POST;
        if (!$row->bind($post)) {
            $this->app->enqueueMessage($row->getError(), 'error');
            return array(false);
        }
        if ($row->dateStart == '') {
            $row->dateStart = '1970-01-01';
        }
        if ($row->dateEnd == '') {
            $row->dateEnd = '1970-01-01';
        }
        // Parameter
        /*		$paramsStringArray = array();
                foreach ($row->params as $key => $value) {
                    $paramsStringArray[] = $key.'='.intval($value);
                }
                $row->params = implode("\n", $paramsStringArray);
        */

        if (!$row->checkData()) {
            // pre-save checks
            $this->app->enqueueMessage($row->getError(), 'warning');
            // Weiterleitung bleibt im Formular !!
            //			$this->adminLink->more = array('task' => $task, 'id' => $row->id);
            return array(false,false,$row->id);

        }

        // if new item, order last in appropriate group
        if (!$row->id) {
            $neu = true; // Flag für neue Kategorie
            $stringAktion = JText::_('CATEGORY_CREATED');
            // $where = "sid = " . (int) $row->sid; warum nur in Saison?
            $row->ordering = $row->getNextOrder(); // ( $where );
        } else {
            $neu = false;
            $stringAktion = JText::_('CATEGORY_EDITED');
        }

        // save the changes
        if (!$row->store()) {
            $this->app->enqueueMessage($row->getError(), 'error');
            return array(false,$neu,$row->id);
        }


        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = $stringAktion.": ".$row->name;
        $clmLog->params = array('catid' => $row->id);
        $clmLog->write();

        return array(true,$neu,$row->id);

    }


    public function cancel()
    {

        $adminLink = new AdminLink();
        $adminLink->view = "catmain";
        $adminLink->makeURL();
        $this->app->redirect($adminLink->url);

    }

}
