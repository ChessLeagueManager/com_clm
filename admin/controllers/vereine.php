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
// Include the AddressHandler class
require_once JPATH_COMPONENT_ADMINISTRATOR. '/helpers/addresshandler.php';

class CLMControllerVereine extends JControllerLegacy
{
    /**
     * Constructor
     */

    public function __construct($config = array())
    {
        $db	= JFactory::getDBO();
        $sql	= " SELECT COUNT(id) as count "
            ." FROM #__clm_liga "
            ." WHERE sl = ".clm_core::$access->getJid()
            ." AND rang <> 0"
        ;
        $db->setQuery($sql);
        $count_sl = $db->loadObjectList();

        DEFINE('CLM_sl_count', $count_sl[0]->count);
        parent::__construct($config);
        // Register Extra tasks
        $this->registerTask('add', 'edit');
        $this->registerTask('apply', 'save');
        $this->registerTask('unpublish', 'publish');
    }

    public function display($cachable = false, $urlparams = array())
    {
        $mainframe	= JFactory::getApplication();
        $option 	= clm_core::$load->request_string('option');
        $section 	= clm_core::$load->request_string('section');
        $db = JFactory::getDBO();

        $filter_order		= $mainframe->getUserStateFromRequest("$option.filter_order", 'filter_order', 'a.id', 'cmd');
        $filter_order_Dir	= $mainframe->getUserStateFromRequest("$option.filter_order_Dir", 'filter_order_Dir', '', 'word');
        $filter_state		= $mainframe->getUserStateFromRequest("$option.filter_state", 'filter_state', '', 'word');
        $filter_sid		= $mainframe->getUserStateFromRequest("$option.filter_sid", 'filter_sid', 0, 'int');
        $search			= $mainframe->getUserStateFromRequest("$option.search", 'search', '', 'string');
        $search			= strtolower($search);
        $limit			= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart		= $mainframe->getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');
        $filter_vid		= $mainframe->getUserStateFromRequest("$option.filter_vid", 'filter_vid', 0, 'var');
        //CLM parameter auslesen
        $clm_config = clm_core::$db->config();
        if ($clm_config->field_search == 1) {
            $field_search = "js-example-basic-single";
        } else {
            $field_search = "inputbox";
        }

        $where = array();
        $where[] = ' c.archiv = 0';
        if ($filter_sid) {
            $where[] = 'a.sid = '.(int) $filter_sid;
        }
        if ($search) {
            $where[] = 'LOWER(a.name) LIKE "'.$db->escape('%'.$search.'%').'"';
        }

        if ($filter_state) {
            if ($filter_state == 'P') {
                $where[] = 'a.published = 1';
            } elseif ($filter_state == 'U') {
                $where[] = 'a.published = 0';
            }
        }
        $where 		= (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
        if ($filter_order == 'a.id') {
            $orderby 	= ' ORDER BY ordering';
        } else {
            if ($filter_order == 'a.name' or $filter_order == 'a.zps' or $filter_order == 'a.homepage' or $filter_order == 'c.name' or $filter_order == 'a.published' or $filter_order == 'a.ordering') {
                $orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', a.id';
            } else {
                $filter_order = 'a.id';
                $orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir ;
            }
        }
        // get the total number of records
        $query = 'SELECT COUNT(*) '
            . ' FROM #__clm_vereine AS a'
            .' LEFT JOIN #__clm_saison AS c ON c.id = a.sid'
        . $where
        ;
        $db->setQuery($query);
        $total = $db->loadResult();

        jimport('joomla.html.pagination');
        $pageNav = new JPagination($total, $limitstart, $limit);

        // get the subset (based on limits) of required records
        $query = 'SELECT a.*,u.name AS editor '
        . ', c.name AS saison '
        . ' FROM #__clm_vereine AS a'
        . ' LEFT JOIN #__clm_saison AS c ON c.id = a.sid'
        . ' LEFT JOIN #__users AS u ON u.id = a.checked_out'
        . $where
        . $orderby	;

        try {
            $db->setQuery($query, $pageNav->limitstart, $pageNav->limit);
            $rows = $db->loadObjectList();
        } catch (Exception $e) {
            $mainframe->enqueueMessage($db->stderr(), 'error');
        }
        // state filter
        //$lists['state']	= JHTML::_('grid.state',  $filter_state );
        $lists['state'] = CLMForm::selectState($filter_state);

        // Saisonfilter
        $sql = 'SELECT id, name FROM #__clm_saison WHERE archiv =0';
        $db->setQuery($sql);
        $saisonlist[]	= JHTML::_('select.option', '0', JText::_('VEREINE_SAISON'), 'id', 'name');
        $saisonlist	= array_merge($saisonlist, $db->loadObjectList());
        //	$lists['sid']	= JHTML::_('select.genericlist', $saisonlist, 'filter_sid', 'class="js-example-basic-single" size="1" onchange="document.adminForm.submit();"','id', 'name', intval( $filter_sid ) );
        $lists['sid']	= JHTML::_('select.genericlist', $saisonlist, 'filter_sid', 'class="'.$field_search.'" size="1" onchange="document.adminForm.submit();"', 'id', 'name', intval($filter_sid));

        // table ordering
        $lists['order_Dir']	= $filter_order_Dir;
        $lists['order']		= $filter_order;

        // search filter
        $lists['search'] = $search;
        require_once(JPATH_COMPONENT.DS.'views'.DS.'vereine.php');
        CLMViewVereine::vereine($rows, $lists, $pageNav, $option);
    }

    public function geo()
    {
        $mainframe	= JFactory::getApplication();
        // Check for request forgeries
        defined('clm') or die('Invalid Token');
        $option 	= clm_core::$load->request_string('option');
        $section	= clm_core::$load->request_string('section');
        $this->setRedirect('index.php?option='.$option.'&section='.$section);
        $cid		= clm_core::$load->request_array_int('cid');
        $db		= JFactory::getDBO();
        $table		= JTable::getInstance('vereine', 'TableCLM');
        $user		= JFactory::getUser();
        $n		= count($cid);
        $clm_config = clm_core::$db->config();
        $unsuccessArray = array();
        $addressHandler = new AddressHandler();

        if (!$clm_config->googlemaps) {
            $mainframe->enqueueMessage(JText::_('VEREINE_GEO_OFF'), 'warning');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        } else {
            if ($n > 0) {
                foreach ($cid as $id) {
                    if ($table->load((int)$id)) {
                        $lokal_coord = $addressHandler->convertAddress($table->lokal);

                        if (is_null($lokal_coord) || $lokal_coord == -1) {
                            $unsuccessArray[] = $table->name;

                        } else {
                            $addressHandler->updateClubCoordinates($lokal_coord, $table->id);
                        }
                    } else {
                        $mainframe->enqueueMessage($table->getError(), 'error');
                        $link = 'index.php?option='.$option.'&section='.$section;
                        $mainframe->redirect($link);
                    }
                }
            } else {
                $mainframe->enqueueMessage(JText::_('VEREINE_NO_ITEMS'), 'warning');
                $link = 'index.php?option='.$option.'&section='.$section;
                $mainframe->redirect($link);
            }

            $msg = ($n - count($unsuccessArray)) . "/" . $n . " " . JText::_('VEREINE_GEO_UPDATE') . "!<br>";
            if (count($unsuccessArray) > 0) {
                $msg = $msg . JText::_('VEREINE_GEO_FAILURE') . implode("<br>", $unsuccessArray);
            }

            // Log schreiben
            $clmLog = new CLMLog();
            $clmLog->aktion = "Geodaten geupdated";
            $clmLog->params = array('rnd' => $cid[0], 'cids' => implode(',', $cid));
            $clmLog->write();

            $this->setMessage(JText::_($msg));
            $mainframe->enqueueMessage(JText::_($msg));
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }
    }

    public function edit()
    {
        $mainframe	= JFactory::getApplication();

        $db 		= JFactory::getDBO();
        $user 		= JFactory::getUser();
        $task 		= clm_core::$load->request_string('task');
        $cid 		= clm_core::$load->request_array_int('cid');
        if (is_null($cid)) {
            $cid[0] = clm_core::$load->request_int('id');
        }
        $option 	= clm_core::$load->request_string('option');
        $section 	= clm_core::$load->request_string('section');

        //CLM parameter auslesen
        $clm_config = clm_core::$db->config();
        if ($clm_config->field_search == 1) {
            $field_search = "js-example-basic-single";
        } else {
            $field_search = "inputbox";
        }

        $row = JTable::getInstance('vereine', 'TableCLM');
        // load the row from the db table
        $row->load($cid[0]);
        $sid = $row->sid;
        // Userberechtigung abfragen
        $clmAccess = clm_core::$access;
        if ($clmAccess->access('BE_club_create') === false and $task == 'add') {
            $mainframe->enqueueMessage(JText::_('VEREINE_ADMIN'), 'warning');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section, $msg, "message");
        }
        if ($task == 'edit') {
            // illegaler Einbruchversuch über URL !
            // evtl. mitschneiden !?!
            $saison		= JTable::getInstance('saisons', 'TableCLM');
            $saison->load($sid);
            if ($saison->archiv == "1") {  //AND clm_core::$access->getType() !== 'admin') {
                $mainframe->enqueueMessage(JText::_('VEREINE_NO_ARCHIV'), 'warning');
                $mainframe->redirect('index.php?option='. $option.'&section='.$section, $msg, "message");
            }
            if ($cid[0] == 0) { // AND $task =='edit') {
                $mainframe->enqueueMessage(JText::_('VEREINE_FALSCH'), 'warning');
                $link = 'index.php?option='.$option.'&section='.$section;
                $mainframe->redirect($link);
            }
            // do stuff for existing records
            $row->checkout($user->get('id'));
        } else {
            // do stuff for new records
            $row->published 	= 0;
        }

        if ($clmAccess->access('BE_club_create') === false) {
            $mainframe->enqueueMessage(JText::_('VEREINE_NO_BEARBEITEN'), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }

        $sql = 'SELECT id FROM #__clm_saison WHERE archiv = 0 and published = 1';
        $db->setQuery($sql);
        $sid = $db->loadResult();

        // Vereinefilter laden
        // 1 = Auswahl DB obwohl manuell aktiviert wurde !
        $vereinlist	= CLMFilterVerein::vereine_filter(1);
        //	$lists['verein']= JHTML::_('select.genericlist',   $vereinlist, 'zps', 'class="js-example-basic-single" size="1" style="width:300px" onchange="Tausch(this.id)"','zps', 'name', $row->zps );
        $lists['verein'] = JHTML::_('select.genericlist', $vereinlist, 'zps', 'class="'.$field_search.'" size="1" style="width:300px" onchange="Tausch(this.id)"', 'zps', 'name', $row->zps);

        // Vereinsleiterliste
        if ($task == 'edit') {
            $where = "WHERE ZPS = '".$row->zps."'";
        } else {
            $where = 'WHERE ZPS IS NULL' ;
        }
        $sql = 'SELECT Spielername as name, Mgl_Nr as vl'
        .' FROM #__clm_dwz_spieler '.$where
        .' AND sid ='.$sid
        ;
        $db->setQuery($sql);
        if (!clm_core::$db->query($sql)) {
            $mainframe->enqueueMessage($db->getErrorMsg(), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }
        $vllist[]	= JHTML::_('select.option', '0', JText::_('VEREINE_VEREINSLEITER'), 'vl', 'name');
        $vllist	= array_merge($vllist, $db->loadObjectList());
        //	$lists['vl']= JHTML::_('select.genericlist',   $vllist, 'vl', 'class="js-example-basic-single" size="1" style="width:300px"  onchange="VSTausch(this.id)"','vl', 'name', $row->vl );
        $lists['vl'] = JHTML::_('select.genericlist', $vllist, 'vl', 'class="'.$field_search.'" size="1" style="width:300px"  onchange="VSTausch(this.id)"', 'vl', 'name', $row->vl);

        // Saisonliste
        if ($task == "edit") {
            $sql = 'SELECT id as sid, name FROM #__clm_saison WHERE id='.$sid;
        } else {
            $sql = 'SELECT id as sid, name FROM #__clm_saison WHERE archiv =0';
        }
        $db->setQuery($sql);
        if (!clm_core::$db->query($sql)) {
            $mainframe->enqueueMessage($db->getErrorMsg(), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }
        if ($task != "edit") {
            $saisonlist[]	= JHTML::_('select.option', '0', JText::_('VEREINE_SAISON'), 'sid', 'name');
            $saisonlist	= array_merge($saisonlist, $db->loadObjectList());
        } else {
            $saisonlist	= $db->loadObjectList();
        }
        //	$lists['saison']= JHTML::_('select.genericlist',   $saisonlist, 'sid', 'class="js-example-basic-single" size="1" style="width:300px" ','sid', 'name', $row->sid );
        $lists['saison'] = JHTML::_('select.genericlist', $saisonlist, 'sid', 'class="'.$field_search.'" size="1" style="width:300px" ', 'sid', 'name', $row->sid);

        $lists['published']	= JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $row->published);
        $lists['cid']	= $cid[0];

        require_once(JPATH_COMPONENT.DS.'views'.DS.'vereine.php');
        CLMViewVereine::verein($row, $lists, $option);
    }

    public function save()
    {
        $mainframe	= JFactory::getApplication();

        // Check for request forgeries
        defined('_JEXEC') or die('Invalid Token');

        $option	= clm_core::$load->request_string('option');
        $section = clm_core::$load->request_string('section');
        $db 	= JFactory::getDBO();
        $task 	= clm_core::$load->request_string('task');
        $row 	= JTable::getInstance('vereine', 'TableCLM');
        $msg	= clm_core::$load->request_string('id');

        $post = $_POST;
        if (!$row->bind($post)) {
            $mainframe->enqueueMessage($db->getErrorMsg(), 'error');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }
        // pre-save checks
        if (!$row->check()) {
            $mainframe->enqueueMessage($db->getErrorMsg(), 'error');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }
        // if new item, order last in appropriate group
        $aktion = "Verein editiert";
        if (!$row->id) {
            $aktion = "Verein angelegt";
            $where = "sid = " . (int) $row->sid;
            $row->ordering = $row->getNextOrder($where);
        }
        // Kontrolle ob ZPS bereits existiert
        $query = " SELECT COUNT(zps) as count FROM #__clm_vereine "
            ." WHERE ZPS = '".clm_escape($row->zps)."'"
            ." AND sid = $row->sid "
        ;

        $db->setQuery($query);
        $zps_exist = $db->loadObjectList();
        if ($zps_exist[0]->count > 0 and !$row->id) {
            $mainframe->enqueueMessage(JText::_('VEREINE_IST'), 'error');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }

        // save the changes
        if (!$row->store()) {
            $mainframe->enqueueMessage($row->getError(), 'error');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        } else {
            //Geometry points need to be safed manually
            $addressHandler = new AddressHandler();
            $lokal_coord = $addressHandler->convertAddress($row->lokal);
            $addressHandler->updateClubCoordinates($lokal_coord, $row->id);

            if (is_null($lokal_coord)) {
                $mainframe->enqueueMessage(JTEXT::_('WARNING_ADDRESS_LOOKUP'), 'warning');
            }
        }


        switch ($task) {
            case 'apply':
                $msg = JText::_('VEREINE_AENDERN');
                $link = 'index.php?option='.$option.'&section='.$section.'&task=edit&id='. $row->id ;
                break;
            case 'save':
            default:
                $msg = JText::_('VEREINE_SPEICHERN');
                $link = 'index.php?option='.$option.'&section='.$section;
                break;
        }

        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = $aktion;
        //$clmLog->params = array('sid' => $row->sid, 'lid' => $row->liga, 'zps' => $row->zps);
        $clmLog->params = array('sid' => $row->sid, 'zps' => $row->zps);
        $clmLog->write();

        $mainframe->enqueueMessage($msg, 'message');
        $mainframe->redirect($link);
    }

    public function cancel()
    {
        $mainframe	= JFactory::getApplication();
        // Check for request forgeries
        defined('_JEXEC') or die('Invalid Token');

        $option		= clm_core::$load->request_string('option');
        $section	= clm_core::$load->request_string('section');
        $id		= clm_core::$load->request_string('id');
        $row 		= JTable::getInstance('vereine', 'TableCLM');

        $msg = JText::_('VEREINE_STOP');
        $mainframe->enqueueMessage($msg, 'message');
        $mainframe->redirect('index.php?option='. $option.'&section='.$section);
    }

    public function remove()
    {
        $mainframe	= JFactory::getApplication();

        // Check for request forgeries
        defined('_JEXEC') or die('Invalid Token');

        $db 		= JFactory::getDBO();
        $cid 		= clm_core::$load->request_array_int('cid');
        $option 	= clm_core::$load->request_string('option');
        $section	= clm_core::$load->request_string('section');

        if (count($cid) < 1) {
            $mainframe->enqueueMessage(JText::_('VEREINE_SELECT', true), 'warning');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }

        $clmAccess = clm_core::$access;
        if ($clmAccess->access('BE_club_create') === false) {
            $msg = JText::_('VEREINE_NO_LOESCH');
            $mainframe->enqueueMessage($msg, 'message');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }

        $cids = implode(',', $cid);
        $query = 'DELETE FROM #__clm_vereine'
        . ' WHERE id IN ( '. $cids .' )';
        $db->setQuery($query);
        if (!clm_core::$db->query($query)) {
            echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n";
        }

        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = $aktion;
        $clmLog->params = array('sid' => $row->sid, 'zps' => $row->zps, 'cids' => $cids);
        $clmLog->write();

        $mainframe->redirect('index.php?option='. $option.'&section='.$section);
    }

    public function publish()
    {
        $mainframe	= JFactory::getApplication();

        // Check for request forgeries
        defined('clm') or die('Invalid Token');

        $db 		= JFactory::getDBO();
        $user 		= JFactory::getUser();
        $cid		= clm_core::$load->request_array_int('cid');
        $task		= clm_core::$load->request_string('task');
        $publish	= ($task == 'publish');
        $option		= clm_core::$load->request_string('option');
        $section	= clm_core::$load->request_string('section');

        if (empty($cid)) {
            $mainframe->enqueueMessage('No items selected', 'warning');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }

        $clmAccess = clm_core::$access;
        if ($clmAccess->access('BE_club_create') === false) {
            $mainframe->enqueueMessage(JText::_('VEREINE_NO_PUBLIZIEREN'), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }

        $cids = implode(',', $cid);
        $query = 'UPDATE #__clm_vereine'
        . ' SET published = '.(int) $publish
        . ' WHERE id IN ( '. $cids .' )'
        . ' AND ( checked_out IS NULL OR checked_out = 0 OR ( checked_out = '.(int) $user->get('id') .' ) )';
        //$db->setQuery( $query );
        if (!clm_core::$db->query($query)) {
            $mainframe->enqueueMessage($db->getErrorMsg(), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }
        if (count($cid) == 1) {
            $row = JTable::getInstance('vereine', 'TableCLM');
        }

        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = "Verein ".$task;
        $table		= JTable::getInstance('vereine', 'TableCLM');
        $table->load($cid[0]);
        $clmLog->params = array('sid' => $table->sid, 'zps' => $table->zps, 'cids' => $cids);
        $clmLog->write();

        $mainframe->redirect('index.php?option='. $option.'&section='.$section);
    }
    /**
    * Moves the record up one position
    */
    public function orderdown()
    {
        CLMControllerVereine::order(1);
    }

    /**
    * Moves the record down one position
    */
    public function orderup()
    {
        CLMControllerVereine::order(-1);
    }

    /**
    * Moves the order of a record
    * @param integer The direction to reorder, +1 down, -1 up
    */
    public function order($inc)
    {
        $mainframe	= JFactory::getApplication();

        // Check for request forgeries
        defined('clm') or die('Invalid Token');

        $db		= JFactory::getDBO();
        $cid		= clm_core::$load->request_array_int('cid');
        $option 	= clm_core::$load->request_string('option');
        $section	= clm_core::$load->request_string('section');

        $limit 		= clm_core::$load->request_string('limit', 0);
        $limitstart = clm_core::$load->request_string('limitstart', 0);

        $row = JTable::getInstance('vereine', 'TableCLM');
        $row->load($cid[0]);
        $row->move($inc, 'sid = '.(int) $row->sid.' AND published != 0');

        $msg 	= 'Liste umsortiert !';
        $mainframe->enqueueMessage($msg, 'message');
        $mainframe->redirect('index.php?option='. $option.'&section='.$section);
    }

    /**
    * Saves user reordering entry
    */
    public function saveOrder()
    {
        $mainframe	= JFactory::getApplication();

        // Check for request forgeries
        defined('clm') or die('Invalid Token');

        $db			= JFactory::getDBO();
        $cid		= clm_core::$load->request_array_int('cid');
        $option 	= clm_core::$load->request_string('option');
        $section	= clm_core::$load->request_string('section');

        $total		= count($cid);
        $order		= clm_core::$load->request_array_int('order');

        $row = JTable::getInstance('vereine', 'TableCLM');
        $groupings = array();

        // update ordering values
        for ($i = 0; $i < $total; $i++) {
            $row->load((int) $cid[$i]);
            // track categories
            $groupings[] = $row->saison;

            if ($row->ordering != $order[$i]) {
                $row->ordering = $order[$i];
                if (!$row->store()) {
                    $mainframe->enqueueMessage($db->getErrorMsg(), 'error');
                    $mainframe->redirect('index.php?option='. $option.'&section='.$section);
                }
            }
        }
        // execute updateOrder for each parent group
        $groupings = array_unique($groupings);
        foreach ($groupings as $group) {
            $row->reorder('id = '.(int) $group);
        }
        $mainframe->enqueueMessage(JText::_('CLM_NEW_ORDERING_SAVED'));
        $mainframe->redirect('index.php?option='. $option.'&section='.$section);
    }

    public function copy()
    {
        $mainframe	= JFactory::getApplication();
        // Check for request forgeries
        defined('clm') or die('Invalid Token');
        $option 	= clm_core::$load->request_string('option');
        $section	= clm_core::$load->request_string('section');
        $this->setRedirect('index.php?option='.$option.'&section='.$section);
        $cid		= clm_core::$load->request_array_int('cid');
        $db		= JFactory::getDBO();
        $table		= JTable::getInstance('vereine', 'TableCLM');
        $user		= JFactory::getUser();
        $n		= count($cid);

        $clmAccess = clm_core::$access;
        if ($clmAccess->access('BE_club_create') === false) {
            $mainframe->enqueueMessage(JText::_('VEREINE_NO_KOPIE'), 'error');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }
        if ($n > 0) {
            foreach ($cid as $id) {
                if ($table->load((int)$id)) {
                    $table->id			= 0;
                    $table->name		= 'Kopie von ' . $table->name;
                    $table->published	= 0;

                    if (!$table->store()) {
                        $mainframe->enqueueMessage($table->getError(), 'error');
                        $link = 'index.php?option='.$option.'&section='.$section;
                        $mainframe->redirect($link);
                    }
                } else {
                    $mainframe->enqueueMessage($table->getError(), 'error');
                    $link = 'index.php?option='.$option.'&section='.$section;
                    $mainframe->redirect($link);
                }
            }
        } else {
            $mainframe->enqueueMessage(JText::_('VEREINE_NO_ITEMS'), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }

        if ($n > 1) {
            $msg = ' Einträge kopiert !';
        } else {
            $msg = ' Eintrag kopiert !';
        }

        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = "Verein kopiert";
        $clmLog->params = array('rnd' => $cid[0], 'cids' => implode(',', $cid));
        $clmLog->write();

        $this->setMessage(JText::_($n.$msg));
        $mainframe->enqueueMessage(JText::_($n.$msg));
        $mainframe->redirect('index.php?option='. $option.'&section='.$section);
    }


    public function dwz()
    {
        defined('_JEXEC') or die('Invalid Token');
        $mainframe	= JFactory::getApplication();

        $option 	= clm_core::$load->request_string('option');
        $section	= clm_core::$load->request_string('section');

        $cid 		= clm_core::$load->request_array_int('cid');

        // kein Verein gewählt
        if (!isset($cid[0])) {
            $mainframe->enqueueMessage(JText::_('VEREINE_NO_DWZ'), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }
        $row = JTable::getInstance('vereine', 'TableCLM');
        // load the row from the db table
        $row->load($cid[0]);
        $sid = $row->zps;
        $mainframe->setUserState("$option.filter_vid", $row->zps);
        $mainframe->redirect('index.php?option='.$option.'&section=dwz');
    }

    public function gruppen()
    {
        defined('clm') or die('Invalid Token');
        $mainframe	= JFactory::getApplication();
        $option 	= clm_core::$load->request_string('option');
        $section	= clm_core::$load->request_string('section');

        $clmAccess = clm_core::$access;
        if ($clmAccess->access('BE_club_edit_ranking') === false) {
            $mainframe->enqueueMessage(JText::_('VEREINE_NO_RANG'), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }
        // Link MUSS hardcodiert sein !!!
        $mainframe->redirect('index.php?option='.$option.'&section=gruppen');
    }

    public function rangliste()
    {
        defined('clm') or die('Invalid Token');
        $mainframe	= JFactory::getApplication();

        $option 	= clm_core::$load->request_string('option');
        $section	= clm_core::$load->request_string('section');

        $clmAccess = clm_core::$access;
        if ($clmAccess->access('BE_club_edit_ranking') === false) {
            $mainframe->enqueueMessage(JText::_('VEREINE_BEARBEITEN_RANG'), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }

        $cid 		= clm_core::$load->request_array_int('cid');

        $row = JTable::getInstance('vereine', 'TableCLM');
        // load the row from the db table
        $row->load($cid[0]);
        $sid = $row->zps;
        $mainframe->setUserState("$option.filter_vid", $row->zps);

        $mainframe->redirect('index.php?option='.$option.'&section=ranglisten');
    }

    public function copy_saison()
    {
        $mainframe	= JFactory::getApplication();
        // Check for request forgeries
        defined('clm') or die('Invalid Token');
        $db		= JFactory::getDBO();
        $option 	= clm_core::$load->request_string('option');
        $section	= clm_core::$load->request_string('section');
        $user		= JFactory::getUser();
        $jid		= $user->get('id');

        $clmAccess = clm_core::$access;
        if ($clmAccess->access('BE_club_copy') === false) {
            $mainframe->enqueueMessage(JText::_('VEREINE_ADMIN'), 'warning');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }
        // id Vorsaison bestimmen
        $sql = " SELECT id FROM #__clm_saison "
            ." WHERE archiv = 1 "
            ." ORDER BY id DESC LIMIT 1"
        ;
        $db->setQuery($sql);
        $check = $db->loadResult();

        // keine Vorsaison existent !
        if (!$check) {
            $mainframe->enqueueMessage(JText::_('VEREINE_NO_VORSAISON'), 'warning');
            $mainframe->redirect('index.php?option='.$option.'&section='.$section);
        }

        // id aktuelle Saison bestimmen
        $sql	= " SELECT id FROM #__clm_saison "
            ." WHERE archiv = 0 AND published = 1"
            ." ORDER BY id ASC LIMIT 1"
        ;
        $db->setQuery($sql);
        $sid	= $db->loadResult();

        // keine Sid gefunden
        if (!$sid) {
            $mainframe->enqueueMessage(JText::_('VEREINE_NO_SAISON'), 'warning');
            $mainframe->redirect('index.php?option='.$option.'&section='.$section);
        }

        // Anzahl Vereine bestimmen
        $sql = " SELECT COUNT(id) FROM #__clm_vereine WHERE sid = ".$check;
        $db->setQuery($sql);
        $count	= $db->loadResult();

        // keine User gefunden
        if (!$count) {
            $mainframe->enqueueMessage(JText::_('VEREINE_NO_VEREIN'), 'warning');
            $mainframe->redirect('index.php?option='.$option.'&section='.$section);
        }

        // schon vorhandenen Vereine in aktueller Saison bestimmen und in Array
        $sql = " SELECT zps FROM #__clm_vereine "
            ." WHERE sid =".$sid
            ." ORDER BY zps ASC "
        ;
        $db->setQuery($sql);
        $akt_user	= $db->loadObjectList();

        $arr_user = array();
        foreach ($akt_user as $jid_user) {
            $arr_user[] = "'".$jid_user->zps."'";
        }
        $users = implode(',', $arr_user);

        // Alle Vereine aus Vorsaison laden die noch nicht kopiert wurden
        $sql = " SELECT id FROM #__clm_vereine "
            ." WHERE sid = ".$check;
        if (count($akt_user) != "0") {
            $sql = $sql.' AND zps NOT IN ('.$users.') ';
        }
        $sql = $sql." ORDER BY id ASC "
        ;
        $db->setQuery($sql);
        $spieler	= $db->loadObjectList();

        // keine Vereine zu kopieren
        if (count($spieler) == "0") {
            $mainframe->enqueueMessage(JText::_('VEREINE_VORSAISON_IST'), 'warning');
            $mainframe->redirect('index.php?option='.$option.'&section='.$section);
        }

        // Vereine laden und mit neuer Saison speichern
        $row = JTable::getInstance('vereine', 'TableCLM');

        for ($x = 0; $x < count($spieler); $x++) {
            $row->load(($spieler[$x]->id));
            $row->id	= "0";
            $row->sid	= $sid;
            if (!$row->store()) {
                $mainframe->enqueueMessage($row->getError(), 'error');
                $mainframe->redirect('index.php?option='.$option.'&section='.$section);
            }
        }

        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = "Vereine Vorsaison kopiert";
        $clmLog->params = array('jid' => $jid, 'cids' => $users);
        $clmLog->write();

        $mainframe->enqueueMessage($msg, 'message');
        $mainframe->redirect('index.php?option='. $option.'&section='.$section);
    }
}
