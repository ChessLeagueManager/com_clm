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
// no direct access
defined('_JEXEC') or die('Restricted access');

class CLMControllerTermineForm extends JControllerLegacy
{
    // Konstruktor
    public function __construct($config = array())
    {

        parent::__construct($config);

        // Register Extra tasks
        $this->registerTask('apply', 'save');

    }


    public function save()
    {

        // Task
        $task = clm_core::$load->request_string('task', '');

        $result = $this->_saveDo();
        $app = JFactory::getApplication();

        if ($result[0]) { // erfolgreich?


            if ($result[1]) { // neues termine?
                $app->enqueueMessage(JText::_('TERMINE_TASK_CREATED'));
            } else {
                $app->enqueueMessage(JText::_('TERMINE_TASK_EDITED'));
            }

        } else {
            $app->enqueueMessage($result[2], $result[1]);
        }
        $adminLink = new AdminLink();
        // wenn 'apply', weiterleiten in form
        if ($task == 'apply') {
            // Weiterleitung bleibt im Formular
            $adminLink->view = "termineform";
            $adminLink->more = array('task' => 'edit', 'id' => $result[3]);
        } else {
            // Weiterleitung in Liste
            $adminLink->view = "terminemain"; // WL in Liste
        }
        $adminLink->makeURL();

        $app->redirect($adminLink->url);

    }


    public function _saveDo()
    {

        // Check for request forgeries
        defined('_JEXEC') or die('Invalid Token');

        // Task
        $task = clm_core::$load->request_string('task', '');
        // Instanz der Tabelle
        $row = JTable::getInstance('termine', 'TableCLM');


        $post = $_POST;
        if (!$row->bind($post)) {
            return array(false,'error',$row->getError());
        } elseif (!$row->checkData()) {
            // pre-save checks
            $this->adminLink->more = array('task' => $task, 'id' => $row->id);
            return array(false,'warning',$row->getError());

        }
        if ($row->startdate == '') {
            $row->startdate = '1970-01-01';
        }
        if ($row->enddate == '') {
            $row->enddate = '1970-01-01';
        }

        // if new item, order last in appropriate group
        if (!$row->id) {
            $neu = true; // Flag für neues termine
            $stringAktion = JText::_('TERMINE_TASK_CREATED');
            $row->ordering = $row->getNextOrder(); // ( $where );
        } else {
            $neu = false;
            $stringAktion = JText::_('TERMINE_TASK_EDITED');
        }

        // handling checkboxes
        if ($row->allday === 0) {
            $row->allday = 0;
        } else {
            $row->allday = 1;
        }
        if ($row->noendtime === 0) {
            $row->noendtime = 0;
        } else {
            $row->noendtime = 1;
        }

        // handling dates
        if ($row->startdate != '0000-00-00' and $row->startdate != '1970-01-01' and ($row->enddate == '0000-00-00' or $row->enddate == '1970-01-01')) {
            $row->enddate = $row->startdate;
        }

        // save the changes
        if (!$row->store()) {
            return array(false,'error',$row->getError(),$row->id);
        }



        return array(true,$neu,'message',$row->id);

    }


    public function cancel()
    {

        $adminLink = new AdminLink();
        $adminLink->view = "terminemain";
        $adminLink->makeURL();
        $app = JFactory::getApplication();
        $app->redirect($adminLink->url);

    }

}
