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

class CLMControllerTermineMain extends JControllerLegacy
{
    // Konstruktor
    public function __construct($config = array())
    {

        parent::__construct($config);

        // Register Extra tasks
        $this->registerTask('apply', 'save');
        $this->registerTask('unpublish', 'publish');

    }

    // Weiterleitung!
    public function add()
    {

        $adminLink = new AdminLink();
        $adminLink->view = "termineform";
        $adminLink->makeURL();

        $this->setRedirect($adminLink->url);

    }


    /**
    * Container für kopieren
    *
    */
    public function copy()
    {

        $result = $this->_copyDo();
        $app = JFactory::getApplication();

        if ($result[0]) { // erfolgreich?
            // ja, keine Meldung
        } else {
            $app->enqueueMessage($result[2], $result[1]);
        }
        $adminLink = new AdminLink();
        $adminLink->view = "terminemain";
        $adminLink->makeURL();
        $this->setRedirect($adminLink->url);

    }

    /**
    * eigentliche copy-Funktion
    *
    */
    public function _copyDo()
    {

        // Check for request forgeries
        defined('_JEXEC') or die('Restricted access');


        // zu bearbeitende IDs auslesen
        $cid = clm_core::$load->request_array_int('cid');
        // vorerst nur eine ID bearbeiten!
        $termineid = $cid[0];


        // Daten holen
        $row = JTable::getInstance('termine', 'TableCLM');

        if (!$row->load($termineid)) {
            return array(false,'warning',CLMText::errorText('TERMINE_TASK', 'NOTEXISTING'));
        }

        // alten Namen zwischenspeichern für Message und Log
        $nameOld = $row->name;

        // Daten für Kopie anpassen
        $row->id				= 0; // neue id wird von DB vergeben
        $row->name			= JText::_('COPY_OF').' '.$row->name;
        $row->published	= 0;

        if (!$row->store()) {
            return array(false,'error',$row->getErrorMsg());
        }

        // Ende
        return array(true);

    }


    // Weiterleitung!
    public function edit()
    {

        $cid = clm_core::$load->request_array_int('cid');

        $adminLink = new AdminLink();
        $adminLink->view = "termineform";
        $adminLink->more = array('task' => 'edit', 'id' => $cid[0]);
        $adminLink->makeURL();

        $this->setRedirect($adminLink->url);

    }


    public function publish()
    {

        // Check for request forgeries
        defined('_JEXEC') or die('Restricted access');

        // termine? evtl global inconstruct anlegen
        $user 		= JFactory::getUser();

        $cid = clm_core::$load->request_array_int('cid');

        $task = clm_core::$load->request_string('task', '');
        $publish	= ($task == 'publish'); // zu vergebender Wert 0/1

        // Inhalte übergeben?
        if (empty($cid)) {

            $app = JFactory::getApplication();
            $app->enqueueMessage(JText::_('NO_ITEM_SELECTED'), 'marning');

        } else { // ja, Inhalte vorhanden

            // erst jetzt alle Einträge durchgehen
            foreach ($cid as $key => $value) {

                // load the row from the db table
                $row = JTable::getInstance('termine', 'TableCLM');
                $row->load($value); // Daten zu dieser ID laden

                // Änderung nötig?
                if ($row->published != $publish) {
                    // Log
                    $clmLog = new CLMLog();
                    $clmLog->aktion = JText::_('TERMINE')." ".$row->name.": ".$task;
                    $clmLog->params = array();
                    $clmLog->write();
                    // Log geschrieben - Änderungen später
                } else {
                    unset($cid[$key]);
                }

            }
            // alle Einträge geprüft

            // immer noch Einträge vorhanden?
            if (!empty($cid)) {

                $row = JTable::getInstance('termine', 'TableCLM');
                $row->publish($cid, $publish);

                // Meldung erstellen
                $app = JFactory::getApplication();
                if ($publish) {
                    $app->enqueueMessage(CLMText::sgpl(count($cid), JText::_('TERMINE_TASK'), JText::_('TERMINE_TASKS'))." ".JText::_('CLM_PUBLISHED'));
                } else {
                    $app->enqueueMessage(CLMText::sgpl(count($cid), JText::_('TERMINE_TASK'), JText::_('TERMINE_TASKS'))." ".JText::_('CLM_UNPUBLISHED'));
                }

            } else {

                $app = JFactory::getApplication();
                $app->enqueueMessage(JText::_('NO_CHANGES'));

            }

        }

        $adminLink = new AdminLink();
        $adminLink->view = "terminemain";
        $adminLink->makeURL();
        $this->setRedirect($adminLink->url);

    }



    /**
    * Container für Löschung
    *
    */
    public function delete()
    {

        $result = $this->_deleteDo();
        $app = JFactory::getApplication();

        $app->enqueueMessage($result[2], $result[1]);

        $adminLink = new AdminLink();
        $adminLink->view = "terminemain";
        $adminLink->makeURL();
        $this->setRedirect($adminLink->url);

    }


    /**
    * eigentliche Lösch-Funktion
    *
    */
    public function _deleteDo()
    {

        // Check for request forgeries
        defined('_JEXEC') or die('Restricted access');

        // access? nur admin darf löschen
        $clmAccess = clm_core::$access;
        if ($clmAccess->access('BE_event_delete') === false) {
            return array(false,'warning',JText::_('NO_ACCESS'));
        }

        $cid = clm_core::$load->request_array_int('cid');
        // alle markierten Veranstaltungen
        $count = 0;
        foreach ($cid as $cid1) {
            $count++;
            // vorerst nur ein markiertes Turnier übernehmen // später über foreach mehrere?
            //$termineid = $cid[0];
            $termineid = $cid1;

            // termindaten laden
            $row = JTable::getInstance('termine', 'TableCLM');
            $row->load($termineid);

            // ob Termin existent?
            if (!$row->load($termineid)) {
                return array(false,'warning',CLMText::errorText('TERMINE_TASK', 'NOTEXISTING'));
            }

            // Termin löschen
            $query = " DELETE FROM #__clm_termine "
                ." WHERE id = ".$termineid
            ;
            clm_core::$db->query($query);
        }
        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = JText::_('TERMINE_TASK')." ".JText::_('CLM_DELETED');
        $clmLog->params = array( 'count' => $count);
        $clmLog->write();
        if ($count > 1) {
            $htext = $count.' x ';
        } else {
            $htext = $row->name;
        }
        return array(true,'message',$htext.": ".JText::_('TERMINE_TASK')." ".JText::_('CLM_DELETED'));

    }


    // Moves the record up one position
    public function orderdown()
    {

        $this->_order(1);

        $adminLink = new AdminLink();
        $adminLink->view = "terminemain";
        $adminLink->makeURL();
        $this->setRedirect($adminLink->url);

    }

    // Moves the record down one position
    public function orderup()
    {

        $this->_order(-1);

        $adminLink = new AdminLink();
        $adminLink->view = "terminemain";
        $adminLink->makeURL();
        $this->setRedirect($adminLink->url);

    }

    // Moves the order of a record
    // @param integer The direction to reorder, +1 down, -1 up
    public function _order($inc)
    {

        // Check for request forgeries
        defined('_JEXEC') or die('Restricted access');

        $cid = clm_core::$load->request_array_int('cid');
        $termineid = $cid[0];


        $row = JTable::getInstance('termine', 'TableCLM');
        if (!$row->load($termineid)) {
            $app = JFactory::getApplication();
            $app->enqueueMessage(CLMText::errorText('TERMINE_TASK', 'NOTEXISTING'), 'warning');
            return false;
        }
        $row->move($inc, '');

        $app = JFactory::getApplication();
        $app->enqueueMessage($row->name.": ".JText::_('ORDERING_CHANGED'));

        return true;

    }

    // Saves user reordering entry
    public function saveOrder()
    {

        // Check for request forgeries
        defined('_JEXEC') or die('Restricted access');

        if (clm_core::$access->getType() != 'admin' and clm_core::$access->getType() != 'tl') {
            $app = JFactory::getApplication();
            $app->enqueueMessage(JText::_('SECTION_NO_ACCESS'), 'warning');
            return false;
        }

        $cid = clm_core::$load->request_array_int('cid');

        $total		= count($cid);
        $cid = clm_core::$load->request_array_int('order');

        $row = JTable::getInstance('termine', 'TableCLM');
        $groupings = array();

        // update ordering values
        for ($i = 0; $i < $total; $i++) {
            $row->load((int) $cid[$i]);
            // track categories
            $groupings[] = $row->category;

            if ($row->ordering != $order[$i]) {
                $row->ordering = $order[$i];
                if (!$row->store()) {
                    $app = JFactory::getApplication();
                    $app->enqueueMessage($db->getErrorMsg(), 'error');
                    return false;
                }
            }
        }
        // execute updateOrder for each parent group
        $groupings = array_unique($groupings);
        foreach ($groupings as $group) {
            $row->reorder('sid = '.(int) $group);
        }

        $app = JFactory::getApplication();
        $app->enqueueMessage(JText::_('NEW_ORDERING_SAVED'));

        $adminLink = new AdminLink();
        $adminLink->view = "terminemain";
        $adminLink->makeURL();
        $this->setRedirect($adminLink->url);

    }

    //Pflege Kategorien
    public function catmain()
    {
        $this->setRedirect('index.php?option=com_clm&view=catmain');
    }


    // Weiterleitung!
    public function import()
    {

        $adminLink = new AdminLink();
        $adminLink->view = "termineimport";
        $adminLink->makeURL();

        $this->setRedirect($adminLink->url);

    }


    public function export()
    {

        // Check for request forgeries
        defined('_JEXEC') or die('Restricted access');

        $cid = clm_core::$load->request_array_int('cid');
        $result = clm_core::$api->db_terminliste($cid);

        $file_name = $result[2];
        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = JText::_('TERMINE_TASK')." ".JText::_('CLM_EXPORT');
        $clmLog->params = array('file_name' => $file_name);
        $clmLog->write();

        $app = JFactory::getApplication();

        $app->enqueueMessage(JText::_('TERMINE_TASK_EXPORTED'), 'message');

        $adminLink = new AdminLink();
        $adminLink->view = "terminemain";
        $adminLink->more = array('file_name' => $file_name);
        $adminLink->makeURL();

        $_POST["task"] = 'download';
        $app->redirect($adminLink->url);

    }


    public function download()
    {

        $app = JFactory::getApplication();
        $file_name = clm_core::$load->request_array_int('file_name');

        if ($file_name == '') {
            $file_name = 'Terminliste.csv';
        }

        $file = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'pgn'.DS.$file_name;
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Disposition: attachment; filename="'.$file_name.'"');
            header('Content-type: application/csv');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            flush();
            exit;
        } else {
            $app->enqueueMessage(JText::_('TERMINE_TASK')." ".JText::_('CLM_KEINE'), 'warning');
        }

        $adminLink = new AdminLink();
        $adminLink->view = "terminemain";
        $adminLink->makeURL();
        $app->redirect($adminLink->url);

    }

}
