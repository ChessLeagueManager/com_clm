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

class CLMControllerRunden extends JControllerLegacy
{
    /**
     * Constructor
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->app = JFactory::getApplication();

        // Register Extra tasks
        $this->registerTask('add', 'edit');
        $this->registerTask('apply', 'save');
        $this->registerTask('unpublish', 'publish');
        $this->registerTask('unapprove', 'approve');
        $this->registerTask('notpossible', 'possible');
    }

    public function display($cachable = false, $urlparams = array())
    {
        $mainframe	= JFactory::getApplication();
        $option 	= clm_core::$load->request_string('option');
        $section 	= clm_core::$load->request_string('section');
        $db = JFactory::getDBO();
        $liga	= clm_core::$load->request_int('liga');

        // Parameter auslesen
        $config = clm_core::$db->config();
        $val = $config->menue;

        if ($val == 1) {
            $liga	= clm_core::$load->request_int('liga');
        }
        // für kaskadierende Menüführung
        if ($val == 1 and $liga > 0) {
            $mainframe->setUserState("$option.filter_lid", "$liga");
        }

        $filter_order		= $mainframe->getUserStateFromRequest("$option.filter_order", 'filter_order', 'a.id', 'cmd');
        $filter_order_Dir	= $mainframe->getUserStateFromRequest("$option.filter_order_Dir", 'filter_order_Dir', '', 'word');
        $filter_state		= $mainframe->getUserStateFromRequest("$option.filter_state", 'filter_state', '', 'word');
        $filter_sid		= $mainframe->getUserStateFromRequest("$option.filter_sid", 'filter_sid', 0, 'int');
        $filter_lid		= $mainframe->getUserStateFromRequest("$option.filter_lid", 'filter_lid', 0, 'int');
        $filter_catid		= $mainframe->getUserStateFromRequest("$option.filter_catid", 'filter_catid', 0, 'int');
        $search			= $mainframe->getUserStateFromRequest("$option.search", 'search', '', 'string');
        $search			= strtolower($search);
        $limit			= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart		= $mainframe->getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');

        //CLM parameter auslesen
        $clm_config = clm_core::$db->config();
        if ($clm_config->field_search == 1) {
            $field_search = "js-example-basic-single";
        } else {
            $field_search = "inputbox";
        }

        $where = array();
        //$where[]=' c.archiv = 0';
        if ($filter_catid) {
            $where[] = 'a.published = '.(int) $filter_catid;
        }
        if ($filter_sid) {
            $where[] = 'a.sid = '.(int) $filter_sid;
        }
        if ($filter_lid) {
            $where[] = 'a.liga = '.(int) $filter_lid;
        }
        if ($search) {
            $where[] = 'LOWER(a.name) LIKE "'.$db->escape('%'.$search.'%').'"';
        }

        if (isset($filter_state) and is_string($filter_state)) {
            if ($filter_state == 'P') {
                $where[] = 'a.published = 1';
            } elseif ($filter_state == 'U') {
                $where[] = 'a.published = 0';
            }
        }
        $where 		= (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
        if ($filter_order == 'a.id') {
            $orderby 	= ' ORDER BY id';
        } else {
            if ($filter_order == 'a.name' or $filter_order == 'a.nr' or $filter_order == 'a.datum' or $filter_order == 'd.name' or $filter_order == 'c.name' or $filter_order == 'a.meldung' or $filter_order == 'a.sl_ok' or $filter_order == 'a.published' or $filter_order == 'a.ordering') {
                $orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', a.id';
            } else {
                $filter_order = 'a.id';
                $orderby 	= ' ORDER BY id';
            }
        }
        // get the total number of records
        $query = ' SELECT COUNT(*) '
            .' FROM #__clm_runden_termine AS a'
            .' LEFT JOIN #__clm_saison AS c ON c.id = a.sid'
            . $where
        ;
        $db->setQuery($query);
        $total = $db->loadResult();

        jimport('joomla.html.pagination');
        $pageNav = new JPagination($total, $limitstart, $limit);

        // get the subset (based on limits) of required records
        $query = "SELECT a.*, c.name AS saison, c.published as sid_pub, u.name AS editor, d.name AS liga_name, "
            ." d.durchgang, d.runden ,d.rnd as erstellt, d.sl, d.liga_mt "
        . ' FROM #__clm_runden_termine AS a'
        . ' LEFT JOIN #__clm_saison AS c ON c.id = a.sid'
        . ' LEFT JOIN #__clm_liga AS d ON a.liga = d.id'
        . ' LEFT JOIN #__users AS u ON u.id = a.checked_out'
        . $where.$orderby;
        try {
            $db->setQuery($query, $pageNav->limitstart, $pageNav->limit);
            $rows = $db->loadObjectList();
        } catch (Exception $e) {
            $mainframe->enqueueMessage($db->stderr(), 'error');
        }
        // Filter
        // Statusfilter
        //$lists['state']	= JHTML::_('grid.state',  $filter_state );
        $lists['state'] = CLMForm::selectState($filter_state);
        // nach Saison sortieren
        $sql = 'SELECT id, name FROM #__clm_saison WHERE archiv =0';
        $db->setQuery($sql);
        $saisonlist[]	= JHtml::_('select.option', '0', JText::_('RUNDE_SAISON_WAE'), 'id', 'name');
        $saisonlist         = array_merge($saisonlist, $db->loadObjectList());
        //	$lists['sid']      = JHtml::_('select.genericlist', $saisonlist, 'filter_sid', 'class="js-example-basic-single" size="1" onchange="document.adminForm.submit();"','id', 'name', intval( $filter_sid ) );
        $lists['sid']      = JHtml::_('select.genericlist', $saisonlist, 'filter_sid', 'class="'.$field_search.'" size="1" onchange="document.adminForm.submit();"', 'id', 'name', intval($filter_sid));
        // Nur ausführen wenn Saison published = 1 !!

        //Zugangscheck
        $clmAccess = clm_core::$access;
        if (isset($rows[0]) && $rows[0]->liga_mt == "0") {
            $mppoint = 'league';
            $csection = 'ligen';
            $liga_type = '1';
        } else {
            $mppoint = 'teamtournament';
            $csection = 'mturniere';
            $liga_type = '0';
        }

        if ($clmAccess->access('BE_'.$mppoint.'_edit_round') === false) {
            $mainframe->enqueueMessage(JText::_('LIGEN_STAFFEL_TOTAL'), 'warning');
            $mainframe->redirect('index.php?option='. $option.'&view=view_tournament_group&liga='.$liga_type);
        } elseif ($clmAccess->access('BE_'.$mppoint.'_edit_round') === true) {
            $where_sl = '';
        } else {
            $where_sl = ' AND a.sl = '.clm_core::$access->getJid();
        }

        // Ligafilter
        $sql = 'SELECT a.id AS cid, a.name FROM #__clm_liga as a'
            ." LEFT JOIN #__clm_saison as s ON s.id = a.sid"
            ." WHERE s.archiv = 0 ".$where_sl;
        $db->setQuery($sql);

        $ligalist[]	= JHtml::_('select.option', '0', JText::_('RUNDE_LIGA_WAE'), 'cid', 'name');
        $ligalist	= array_merge($ligalist, $db->loadObjectList());
        //	$lists['lid']	= JHtml::_('select.genericlist', $ligalist, 'filter_lid', 'class="js-example-basic-single" size="1" onchange="document.adminForm.submit();"','cid', 'name', intval( $filter_lid ) );
        $lists['lid']	= JHtml::_('select.genericlist', $ligalist, 'filter_lid', 'class="'.$field_search.'" size="1" onchange="document.adminForm.submit();"', 'cid', 'name', intval($filter_lid));
        // Ordering
        $lists['order_Dir']	= $filter_order_Dir;
        $lists['order']		= $filter_order;
        // Suchefilter
        $lists['search'] = $search;
        if (isset($rows[0]) && $rows[0]->sl !== clm_core::$access->getJid() and $clmAccess->access('BE_'.$mppoint.'_edit_round') !== true) {
            $mainframe->enqueueMessage(JText::_('LIGEN_STAFFEL'), 'warning');
            $mainframe->redirect('index.php?option='. $option.'&view=view_tournament_group&liga='.$liga_type);
        }
        if (!isset($rows[0])) {
            $mainframe->enqueueMessage(JText::_('LIGEN_NOT_POSSIBLE'), 'warning');
            $mainframe->redirect('index.php?option='. $option.'&view=view_tournament_group&liga='.$liga_type);
        }

        require_once(JPATH_COMPONENT.DS.'views'.DS.'runden.php');
        CLMViewRunden::Runden($rows, $lists, $pageNav, $option);
    }

    public function edit()
    {
        $mainframe	= JFactory::getApplication();

        $db 		= JFactory::getDBO();
        $user 		= JFactory::getUser();
        $task 		= clm_core::$load->request_string('task');
        $id 		= clm_core::$load->request_int('id', 0);
        $cid = clm_core::$load->request_array_int('cid');
        if (is_null($cid)) {
            $cid[0] = $id;
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

        $row 		= JTable::getInstance('runden', 'TableCLM');
        // load the row from the db table
        $row->load($cid[0]);

        $liga = JTable::getInstance('ligen', 'TableCLM');
        $liga->load($row->liga);
        if ($liga->liga_mt == "0") {
            $mppoint = 'league';
            $csection = 'ligen';
        } else {
            $mppoint = 'teamtournament';
            $csection = 'mturniere';
        }

        $clmAccess = clm_core::$access;

        if ($clmAccess->access('BE_'.$mppoint.'_edit_round') === false) {
            $section = $csection;
            $msg = JText::_('Kein Zugriff: ').JText::_('RUNDE_STAFFEL_TOTAL1');
            $mainframe->enqueueMessage($msg, 'warning');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }

        if ($task == 'edit') {
            // illegaler Einbruchversuch über URL !
            // evtl. mitschneiden !?!
            $saison		= JTable::getInstance('saisons', 'TableCLM');
            $saison->load($row->sid);
            if ($saison->archiv == "1" and $clmAccess->access('BE_'.$mppoint.'_create') !== true) {
                $mainframe->enqueueMessage(JText::_('RUNDE_ARCHIV'), 'warning');
                $mainframe->redirect('index.php?option='. $option.'&section='.$section);
            }
            // Prüfen ob User Berechtigung zum editieren hat
            if ($liga->sl !== clm_core::$access->getJid() and $clmAccess->access('BE_'.$mppoint.'_edit_round') !== true) {
                $mainframe->enqueueMessage(JText::_('RUNDE_STAFFEL'), 'warning');
                $mainframe->redirect('index.php?option='. $option.'&section='.$section);
            }
            // do stuff for existing records
            $row->checkout($user->get('id'));
        } else {
            // do stuff for new records
            // Prüfen ob User Berechtigung zum Bearbeiten hat
            if ($liga->sl !== clm_core::$access->getJid() and $clmAccess->access('BE_'.$mppoint.'_edit_round') !== true) {
                $mainframe->enqueueMessage(JText::_('RUNDE_STAFFEL'), 'warning');
                $section = $csection;
                $link = 'index.php?option='.$option.'&section='.$section;
                $mainframe->redirect($link);
            }
            $row->published 	= 0;
        }

        // Ligaliste
        $sql = " SELECT a.id as liga, a.name FROM #__clm_liga as a"
            ." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
            ." WHERE  s.archiv = 0 AND a.sl = ".clm_core::$access->getJid()
        ;
        // wenn User Admin
        if ($clmAccess->access('BE_'.$mppoint.'_edit_result') === true) {
            $sql = "SELECT a.id as liga, a.name FROM #__clm_liga as a"
                ." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
                ." WHERE  s.archiv = 0 ";
        }
        $db->setQuery($sql);
        if (!clm_core::$db->query($sql)) {
            $mainframe->enqueueMessage($row->getErrorMsg(), 'warning');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }
        $ligalist[]	= JHtml::_('select.option', '0', JText::_('RUNDE_LIGA_WAE'), 'liga', 'name');
        $ligalist	= array_merge($ligalist, $db->loadObjectList());
        //	$lists['liga']	= JHtml::_('select.genericlist',   $ligalist, 'liga', 'class="js-example-basic-single" size="1"','liga', 'name', $row->liga );
        $lists['liga']	= JHtml::_('select.genericlist', $ligalist, 'liga', 'class="'.$field_search.'" size="1"', 'liga', 'name', $row->liga);
        //	$lists['published']	= JHtml::_('select.booleanlist',  'published', 'class="js-example-basic-single"', $row->published );
        $lists['published']	= JHtml::_('select.booleanlist', 'published', 'class="'.$field_search.'"', $row->published);
        // Saisonliste
        $sql = 'SELECT id as sid, name FROM #__clm_saison WHERE archiv = 0';
        $db->setQuery($sql);
        if (!clm_core::$db->query($sql)) {
            $mainframe->enqueueMessage($row->getErrorMsg(), 'warning');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }
        $saisonlist[]	= JHtml::_('select.option', '0', JText::_('RUNDE_SAISON_WAE'), 'sid', 'name');
        $saisonlist	= array_merge($saisonlist, $db->loadObjectList());
        //	$lists['saison']= JHtml::_('select.genericlist',   $saisonlist, 'sid', 'class="js-example-basic-single" size="1"','sid', 'name', $row->sid );
        $lists['saison'] = JHtml::_('select.genericlist', $saisonlist, 'sid', 'class="'.$field_search.'" size="1"', 'sid', 'name', $row->sid);
        // Liste Meldung
        $lists['complete'] = JHtml::_('select.booleanlist', 'meldung', 'class="inputbox"', $row->meldung);
        // Liste SL OK
        $lists['slok'] = JHtml::_('select.booleanlist', 'sl_ok', 'class="inputbox"', $row->sl_ok);

        require_once(JPATH_COMPONENT.DS.'views'.DS.'runden.php');
        CLMViewRunden::runde($row, $lists, $option);
    }


    public function save()
    {
        $mainframe	= JFactory::getApplication();

        // Check for request forgeries
        defined('clm') or die('Restricted access');

        $cliga 		= clm_core::$load->request_int('liga');

        $option 	= clm_core::$load->request_string('option');
        $section 	= clm_core::$load->request_string('section');
        $slok_old	= clm_core::$load->request_string('slok_old');

        $db 		= JFactory::getDBO();
        $task 		= clm_core::$load->request_string('task');
        $row 		= JTable::getInstance('runden', 'TableCLM');
        $msg = clm_core::$load->request_string('id');

        $post = $_POST;
        if (!$row->bind($post)) {
            $mainframe->enqueueMessage($row->getError(), 'error');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }
        if ($row->deadlineday == '' or $row->deadlineday == '0000-00-00') {
            $row->deadlineday = '1970-01-01';
        }
        if ($row->deadlinetime == '') {
            $row->deadlinetime = '24:00';
        }
        // pre-save checks
        if (!$row->check()) {
            $mainframe->enqueueMessage($row->getError(), 'error');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }
        // if new item, order last in appropriate group
        $aktion = JText::_('RUNDE_LOG_EDIT');
        if (!$row->id) {
            $aktion = JText::_('RUNDE_LOG_ADDED');
            $where = "sid = " . (int) $row->sid;
            $row->ordering = $row->getNextOrder($where);
        }
        // save the changes
        if (!$row->store()) {
            $mainframe->enqueueMessage($row->getError(), 'error');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }

        //	if ($row->startzeit != '00:00') {
        if ($row->startzeit != '00:00:00') {
            $startzeit = $row->startzeit.':00';
            $query = " UPDATE #__clm_runden_termine "
                ." SET startzeit = '".$startzeit."' "
                ." WHERE liga = ".$row->liga
                ." AND sid = ".$row->sid
                ." AND startzeit = '00:00:00' "
            ;
            $db->setQuery($query);
            clm_core::$db->query($query);
        }

        if ($row->datum != '0000-00-00' and $row->datum != '1970-01-01' and $row->deadlineday != '0000-00-00' and $row->deadlineday != '1970-01-01' and $row->deadlineday >= $row->datum) {
            $ts1 = strtotime($row->deadlineday);
            $ts2 = strtotime($row->datum);
            $seconds_diff = $ts1 - $ts2;
            $day_diff = (string) $seconds_diff / 3600 / 24;
            $query = " UPDATE #__clm_runden_termine "
                ." SET deadlineday = ADDDATE(datum,'".$day_diff."') "
                ." WHERE liga = ".$row->liga
                ." AND sid = ".$row->sid
                ." AND (deadlineday <= '1970-01-01') "
            ;
            $db->setQuery($query);
            clm_core::$db->query($query);
        }

        if ($row->deadlinetime != '00:00' and $row->deadlinetime != '24:00') {
            $deadlinetime = $row->deadlinetime.':00';
            $query = " UPDATE #__clm_runden_termine "
                ." SET deadlinetime = '".$deadlinetime."' "
                ." WHERE liga = ".$row->liga
                ." AND sid = ".$row->sid
                ." AND (deadlinetime = '00:00:00' OR deadlinetime = '24:00:00') "
            ;
            $db->setQuery($query);
            clm_core::$db->query($query);
        }

        switch ($task) {
            case 'apply':
                $msg = JText::_('RUNDE_AENDERUNG').$row->nr.JText::_('RUNDE_GESPEICHERT');
                $link = 'index.php?option='.$option.'&section='.$section.'&task=edit&id='.$row->id.'&liga='.$cliga;
                break;
            case 'save':
            default:
                $msg = JText::_('RUNDE ').$row->nr.JText::_('RUNDE_GESPEICHERT');
                $link = 'index.php?option='.$option.'&section='.$section.'&liga='.$cliga;
                break;
        }

        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = $aktion;
        $clmLog->params = array('sid' => $row->sid, 'lid' => $row->liga, 'rnd' => $row->nr);
        $clmLog->write();
        // Log schreiben bei Freigabe
        if (($row->sl_ok == 1) && ($slok_old != 1)) {
            $clmLog = new CLMLog();
            $clmLog->aktion = JText::_('RUNDE_LOG_FREIGABE');
            $clmLog->nr_aktion = 201;							//klkl
            $clmLog->params = array('sid' => $row->sid, 'lid' => $row->liga, 'rnd' => $row->nr);
            $clmLog->write();
        }
        if (($row->sl_ok != 1) && ($slok_old == 1)) {
            $clmLog = new CLMLog();
            $clmLog->aktion = JText::_('RUNDE_LOG_FREIGABE_DEL');
            $clmLog->nr_aktion = 202;							//klkl
            $clmLog->params = array('sid' => $row->sid, 'lid' => $row->liga, 'rnd' => $row->nr);
            $clmLog->write();
        }

        $mainframe->enqueueMessage($msg, "message");
        $mainframe->redirect($link);
    }


    public function cancel()
    {
        $mainframe	= JFactory::getApplication();
        // Check for request forgeries
        defined('clm') or die('Restricted access');
        $cliga 		= clm_core::$load->request_int('liga');

        $option 	= clm_core::$load->request_string('option');
        $section 	= clm_core::$load->request_string('section');
        $id		= clm_core::$load->request_string('id');
        $row 		= JTable::getInstance('runden', 'TableCLM');

        $msg = JText::_('RUNDE_AKTION');
        $mainframe->enqueueMessage($msg, "message");
        $mainframe->redirect('index.php?option='. $option.'&section='.$section.'&liga='.$cliga);
    }


    public function remove()
    {
        $mainframe	= JFactory::getApplication();

        // Check for request forgeries
        defined('clm') or die('Restricted access');

        $db 		= JFactory::getDBO();
        $cid = clm_core::$load->request_array_int('cid');
        $option 	= clm_core::$load->request_string('option');
        $section 	= clm_core::$load->request_string('section');

        if (count($cid) < 1) {
            $mainframe->enqueueMessage(JText::_('RUNDE_SELECT', true), "warning");
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }

        $row = JTable::getInstance('runden', 'TableCLM');
        $row->load($cid[0]);
        $liga = JTable::getInstance('ligen', 'TableCLM');
        $liga->load($row->liga);

        if ($liga->liga_mt == 0) {
            $mppoint = 'league';
            $csection = 'ligen';
        } else {
            $mppoint = 'teamtournament';
            $csection = 'mturniere';
        }

        $clmAccess = clm_core::$access;

        // Prüfen ob User Berechtigung zum löschen hat
        //if ( $liga->sl !== clm_core::$access->getJid() AND clm_core::$access->getType() !== 'admin') {
        if (($liga->sl !== clm_core::$access->getJid() and $clmAccess->access('BE_'.$mppoint.'_edit_round') !== true) or ($clmAccess->access('BE_'.$mppoint.'_edit_round') === false)) {
            $mainframe->enqueueMessage(JText::_('RUNDE_ST_LOESCHEN'), 'warning');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }
        $cids = implode(',', $cid);
        $query = " DELETE FROM #__clm_runden_termine "
        . ' WHERE id IN ( '. $cids .' )';
        $db->setQuery($query);
        if (!clm_core::$db->query($query)) {
            echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n";
        }

        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = JText::_('RUNDE_LOG_DELETED');
        $table	= JTable::getInstance('runden', 'TableCLM');
        $table->load($cid[0]);
        $clmLog->params = array('sid' => $table->sid, 'lid' => $table->liga, 'rnd' => $table->nr, 'cids' => $cids);
        $clmLog->write();

        $mainframe->redirect('index.php?option='. $option.'&section='.$section);
    }


    public function publish()
    {
        $mainframe	= JFactory::getApplication();

        // Check for request forgeries
        defined('clm') or die('Restricted access');

        $db 		= JFactory::getDBO();
        $user 		= JFactory::getUser();
        $cid = clm_core::$load->request_array_int('cid');
        $task		= clm_core::$load->request_string('task');
        $publish	= ($task == 'publish');
        $option 	= clm_core::$load->request_string('option');
        $section 	= clm_core::$load->request_string('section');

        if (empty($cid)) {
            $mainframe->enqueueMessage('No items selected', 'warning');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }
        $row = JTable::getInstance('runden', 'TableCLM');
        $row->load($cid[0]);
        $liga = JTable::getInstance('ligen', 'TableCLM');
        $liga->load($row->liga);
        if ($liga->liga_mt == 0) {
            $mppoint = 'league';
        } else {
            $mppoint = 'teamtournament';
        }

        $clmAccess = clm_core::$access;

        // Prüfen ob User Berechtigung hat
        if (($liga->sl !== clm_core::$access->getJid() and $clmAccess->access('BE_'.$mppoint.'_edit_round') !== true) or ($clmAccess->access('BE_'.$mppoint.'_edit_round') === false)) {
            $mainframe->enqueueMessage(JText::_('RUNDE_ST_PUBLIZIEREN'), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }
        $cids = implode(',', $cid);
        $query = 'UPDATE #__clm_runden_termine'
            . ' SET published = '.(int) $publish
            . ' WHERE id IN ( '. $cids .' )'
            . ' AND ( checked_out IS NULL OR checked_out = 0 OR ( checked_out = '.(int) $user->get('id') .' ) )';
        $db->setQuery($query);
        if (!clm_core::$db->query($query)) {
            $mainframe->enqueueMessage($db->getErrorMsg(), 'error');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }

        if (count($cid) == 1) {
            $row = JTable::getInstance('runden', 'TableCLM');
        }

        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = JText::_('RUNDE_LOG')." ".$task;
        $table	= JTable::getInstance('runden', 'TableCLM');
        $table->load($cid[0]);
        $clmLog->params = array('sid' => $table->sid, 'lid' => $table->liga, 'rnd' => $table->nr, 'cids' => $cids);
        $clmLog->write();

        $mainframe->redirect('index.php?option='. $option.'&section='.$section);
    }
    /**
    * Moves the record up one position
    */
    public function orderdown()
    {
        CLMControllerRunden::order(1);
    }

    /**
    * Moves the record down one position
    */
    public function orderup()
    {
        CLMControllerRunden::order(-1);
    }

    /**
    * Moves the order of a record
    * @param integer The direction to reorder, +1 down, -1 up
    */
    public function order($inc)
    {
        $mainframe	= JFactory::getApplication();

        // Check for request forgeries
        defined('clm') or die('Restricted access');

        $db		= JFactory::getDBO();
        $cid = clm_core::$load->request_array_int('cid');
        $option 	= clm_core::$load->request_string('option');
        $section 	= clm_core::$load->request_string('section');

        $limit 		= clm_core::$load->request_int('limit', 0);
        $limitstart 	= clm_core::$load->request_int('limitstart', 0);

        $row = JTable::getInstance('runden', 'TableCLM');
        $row->load($cid[0]);
        //	$row->move( $inc, 'liga = '.(int) $row->liga.' AND published != 0' );
        $row->move($inc, 'liga = '.(int) $row->liga);
        $row->reorder('liga = '.(int) $row->liga);

        $mainframe->redirect('index.php?option='. $option.'&section='.$section);
    }

    /**
    * Saves user reordering entry
    */
    public function saveOrder()
    {
        $mainframe	= JFactory::getApplication();

        // Check for request forgeries
        defined('clm') or die('Restricted access');

        $db			= JFactory::getDBO();
        $cid = clm_core::$load->request_array_int('cid');
        $option 	= clm_core::$load->request_string('option');
        $section 	= clm_core::$load->request_string('section');

        $total		= count($cid);
        $order = clm_core::$load->request_array_int('order');


        $row = JTable::getInstance('runden', 'TableCLM');
        $groupings = array();

        // update ordering values
        for ($i = 0; $i < $total; $i++) {
            $row->load((int) $cid[$i]);
            // track categories
            $groupings[] = $row->liga;

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
            $row->reorder('liga = '.(int) $group);
        }
        $msg 	= 'New ordering saved';
        $mainframe->enqueueMessage($msg, 'message');
        $mainframe->redirect('index.php?option='. $option.'&section='.$section);
    }

    public static function paarung()
    {
        defined('clm') or die('Restricted access');
        $mainframe	= JFactory::getApplication();

        $db 		= JFactory::getDBO();
        $user 		= JFactory::getUser();
        $cid 		= clm_core::$load->request_int('liga');
        $filter_lid 		= clm_core::$load->request_int('filter_lid');
        if ($filter_lid > 0 and !is_null($filter_lid)) {
            $cid = $filter_lid;
        }

        $option 	= clm_core::$load->request_string('option');
        $section 	= clm_core::$load->request_string('section');

        // keine Liga gewählt
        if ($cid < 1) {
            $msg = JText::_('LIGEN_PA_AENDERN');
            $mainframe->enqueueMessage($msg, 'warning');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }
        // Ligadaten und Paarungsdaten holen
        $query	= "SELECT a.id as lid, a.sid, a.sl, a.liga_mt "
            ." FROM #__clm_liga as a"
            ." WHERE a.id = ".$cid
        ;
        $db->setQuery($query);
        $liga = $db->loadObjectList();

        $clmAccess = clm_core::$access;

        // Prüfen ob User Berechtigung hat
        if ($liga[0]->liga_mt == 0) {
            if (($liga[0]->sl !== clm_core::$access->getJid() and $clmAccess->access('BE_league_edit_fixture') !== true) or ($clmAccess->access('BE_league_edit_fixture') === false)) {
                $msg = JText::_('LIGEN_NO_FIXTURE');
                $mainframe->enqueueMessage($msg, 'warning');
                $mainframe->redirect('index.php?option='. $option.'&section='.$section.'&liga='.$cid);
            }
        } else {
            if (($liga[0]->sl !== clm_core::$access->getJid() and $clmAccess->access('BE_teamtournament_edit_fixture') !== true) or ($clmAccess->access('BE_teamtournament_edit_fixture') === false)) {
                $msg = JText::_('LIGEN_NO_FIXTURE');
                $mainframe->enqueueMessage($msg, 'warning');
                $mainframe->redirect('index.php?option='. $option.'&section='.$section.'&liga='.$cid);
            }
        }

        // Link MUSS hardcodiert sein !!!
        $mainframe->redirect('index.php?option='.$option.'&section=paarung&task=edit&id='.$cid);
    }

    public static function pairingdates()
    {
        defined('clm') or die('Restricted access');
        $mainframe	= JFactory::getApplication();

        $db 		= JFactory::getDBO();
        $user 		= JFactory::getUser();
        $cid 		= clm_core::$load->request_int('liga');

        $option 	= clm_core::$load->request_string('option');
        $section 	= clm_core::$load->request_string('section');

        // keine Liga gewählt
        if ($cid < 1) {
            $msg = JText::_('LIGEN_PA_AENDERN');
            $mainframe->enqueueMessage($msg, 'warning');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section.'&liga='.$cid);
        }
        // Ligadaten und Paarungsdaten holen
        $query	= "SELECT a.id as lid, a.sid, a.sl, a.liga_mt "
            ." FROM #__clm_liga as a"
            ." WHERE a.id = ".$cid
        ;
        $db->setQuery($query);
        $liga = $db->loadObjectList();

        $clmAccess = clm_core::$access;

        // Prüfen ob User Berechtigung hat
        if ($liga[0]->liga_mt == 0) {
            if (($liga[0]->sl !== clm_core::$access->getJid() and $clmAccess->access('BE_league_edit_fixture') !== true) or ($clmAccess->access('BE_league_edit_fixture') === false)) {
                $msg = JText::_('LIGEN_NO_FIXTURE');
                $mainframe->enqueueMessage($msg, 'warning');
                $mainframe->redirect('index.php?option='. $option.'&section='.$section.'&liga='.$cid);
            }
        } else {
            if (($liga[0]->sl !== clm_core::$access->getJid() and $clmAccess->access('BE_teamtournament_edit_fixture') !== true) or ($clmAccess->access('BE_teamtournament_edit_fixture') === false)) {
                $msg = JText::_('LIGEN_NO_FIXTURE');
                $mainframe->enqueueMessage($msg, 'warning');
                $mainframe->redirect('index.php?option='. $option.'&section='.$section.'&liga='.$cid);
            }
        }

        // Link MUSS hardcodiert sein !!!
        $mainframe->redirect('index.php?option='.$option.'&section=pairingdates&task=edit&id='.$cid);
    }

    public function copy()
    {
        $mainframe	= JFactory::getApplication();
        // Check for request forgeries
        defined('clm') or die('Restricted access');
        $option 	= clm_core::$load->request_string('option');
        $section 	= clm_core::$load->request_string('section');
        $this->setRedirect('index.php?option='.$option.'&section='.$section);
        $cid = clm_core::$load->request_array_int('cid');
        if (is_null($cid)) {
            $cid[0] = $id;
        }
        $db	= JFactory::getDBO();
        $table	= JTable::getInstance('runden', 'TableCLM');
        $user	= JFactory::getUser();
        $n		= count($cid);

        $row = JTable::getInstance('runden', 'TableCLM');
        $row->load($cid[0]);
        $liga = JTable::getInstance('ligen', 'TableCLM');
        $liga->load($row->liga);
        if ($liga->liga_mt == 0) {
            $mppoint = 'league';
        } else {
            $mppoint = 'teamtournament';
        }

        $clmAccess = clm_core::$access;

        // Prüfen ob User Berechtigung hat
        if (($liga->sl !== clm_core::$access->getJid() and $clmAccess->access('BE_'.$mppoint.'_edit_round') !== true) or ($clmAccess->access('BE_'.$mppoint.'_edit_round') === false)) {
            $mainframe->enqueueMessage(JText::_('RUNDE_ST_KOPIE'), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }
        if ($n > 0) {
            foreach ($cid as $id) {
                if ($table->load((int)$id)) {
                    $table->id			= 0;
                    $table->name			= 'Kopie von ' . $table->name;
                    $table->published		= 0;

                    if (!$table->store()) {
                        $mainframe->enqueueMessage($table->getError(), 'warning');
                        $link = 'index.php?option='.$option.'&section='.$section;
                        $mainframe->redirect($link);
                    }
                } else {
                    $mainframe->enqueueMessage($table->getError(), 'warning');
                    $link = 'index.php?option='.$option.'&section='.$section;
                    $mainframe->redirect($link);
                }
            }
        } else {
            $mainframe->enqueueMessage(JText::_('RUNDE_NO_SELCET'), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }

        if ($n > 1) {
            $msg = JText::_('RUNDE_MSG_COPY_ENTRYS');
        } else {
            $msg = JText::_('RUNDE_MSG_COPY_ENTRY');
        }

        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = JText::_('RUNDE_LOG_COPIED');
        $table	= JTable::getInstance('runden', 'TableCLM');
        $table->load($cid[0]);
        $clmLog->params = array('sid' => $table->sid, 'lid' => $table->liga, 'rnd' => $table->nr, 'cids' => implode(',', $cid));
        $clmLog->write();

        $mainframe->enqueueMessage(JText::_($n.$msg), 'message');
        $mainframe->redirect('index.php?option='. $option.'&section='.$section);
    }

    public function check()
    {
        defined('clm') or die('Restricted access');
        $mainframe	= JFactory::getApplication();

        $db	= JFactory::getDBO();
        $cid = clm_core::$load->request_array_int('cid');
        $id  = clm_core::$load->request_int('id', 0);
        if (is_null($cid)) {
            $cid[0] = $id;
        }
        $option 	= clm_core::$load->request_string('option');
        $section 	= clm_core::$load->request_string('section');

        // keine Runde gewählt
        if ($cid[0] < 1) {
            $msg = JText::_('RUNDE_RUNDE_PRUE');
            $mainframe->enqueueMessage($msg, 'warning');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }

        $row = JTable::getInstance('runden', 'TableCLM');
        $row->load($cid[0]);
        $liga = JTable::getInstance('ligen', 'TableCLM');
        $liga->load($row->liga);
        if ($liga->liga_mt == "0") {
            $mppoint = 'league';
            $csection = 'ligen';
        } else {
            $mppoint = 'teamtournament';
            $csection = 'mturniere';
        }

        $clmAccess = clm_core::$access;

        // Prüfen ob User Berechtigung hat
        //if ( $liga->sl !== clm_core::$access->getJid() AND clm_core::$access->getType() !== 'admin') {
        if (($liga->sl !== clm_core::$access->getJid() and $clmAccess->access('BE_'.$mppoint.'_edit_round') !== true) or ($clmAccess->access('BE_'.$mppoint.'_edit_round') === false)) {
            $mainframe->enqueueMessage(JText::_('RUNDE_ST_PRUEFEN'), 'warning');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }
        $table	= JTable::getInstance('runden', 'TableCLM');
        $table->load($cid[0]);

        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = JText::_('RUNDE_LOG_VERIFIED');
        $table	= JTable::getInstance('runden', 'TableCLM');
        $table->load($cid[0]);
        $clmLog->params = array('sid' => $table->sid, 'lid' => $table->liga, 'rnd' => $table->nr);
        $clmLog->write();

        // Link MUSS hardcodiert sein !!!
        $mainframe->redirect('index.php?option='.$option.'&section=check&task=edit&id='.$cid[0]);
    }


    public function approve()
    {

        $rc = $this->_approveDo();

        $this->adminLink = new AdminLink();
        $this->adminLink->more = array('section' => 'runden', 'liga' => $rc[1], 'clm_backend' => 1, 'session_language' => 'de-DE');
        $this->adminLink->makeURL();
        $mainframe	= JFactory::getApplication();
        $mainframe->redirect($this->adminLink->url);

    }


    public function _approveDo()
    {

        // Check for request forgeries
        defined('_JEXEC') or die('Invalid Token');

        $cid = clm_core::$load->request_array_int('cid');
        $roundID = $cid[0];

        // Rundendaten holen
        $round = JTable::getInstance('runden', 'TableCLM');
        $round->load($roundID); // Daten zu dieser ID laden

        // Runde existent?
        if (!$round->id) {
            $this->app->enqueueMessage(CLMText::errorText('ROUND', 'NOTEXISTING'), 'warning');
            return array(false,0);
        }
        // Liga/MTurnier holen
        $row = JTable::getInstance('ligen', 'TableCLM');
        $row->load($round->liga); // Daten zu dieser ID laden

        $clmAccess = clm_core::$access;
        if (($row->sl != clm_core::$access->getJid() and $clmAccess->access('BE_tournament_edit_round') !== true) or $clmAccess->access('BE_tournament_edit_round') === false) {
            $this->app->enqueueMessage(JText::_('TOURNAMENT_NO_ACCESS'), 'warning');
            return array(false,$round->liga);
        }

        $task		= clm_core::$load->request_string('task');
        if ($task == 'approve') {
            $approve = 1;
        } else {
            $approve = 0;
        } // zu vergebender Wert 0/1

        // jetzt schreiben
        $round->sl_ok = $approve;
        if (!$round->store()) {
            $this->app->enqueueMessage($row->getError(), 'error');
            return array(false,$round->liga);
        }

        if ($approve) {
            $log_ext = $round->name." ".JText::_('RUNDE_APPROVED');
            $this->app->enqueueMessage($round->name." ".JText::_('RUNDE_APPROVED'));
        } else {
            $log_text = $round->name." ".JText::_('RUNDE_UNAPPROVED');
            $this->app->enqueueMessage($round->name." ".JText::_('RUNDE_UNAPPROVED'));
        }

        // Log
        $clmLog = new CLMLog();
        $clmLog->aktion = $log_text;
        $clmLog->params = array('lid' => $round->liga, 'runde' => $round->nr);
        $clmLog->write();

        return array(true,$round->liga);
    }

    public function possible()
    {

        $rc = $this->_possibleDo();

        $this->adminLink = new AdminLink();
        $this->adminLink->more = array('section' => 'runden', 'liga' => $rc[1], 'clm_backend' => 1, 'session_language' => 'de-DE');
        $this->adminLink->makeURL();
        $mainframe	= JFactory::getApplication();
        $mainframe->redirect($this->adminLink->url);

    }


    public function _possibleDo()
    {

        // Check for request forgeries
        defined('_JEXEC') or die('Invalid Token');

        $cid = clm_core::$load->request_array_int('cid');
        $roundID = $cid[0];

        // Rundendaten holen
        $round = JTable::getInstance('runden', 'TableCLM');
        $round->load($roundID); // Daten zu dieser ID laden

        // Runde existent?
        if (!$round->id) {
            $this->app->enqueueMessage(CLMText::errorText('ROUND', 'NOTEXISTING'), 'warning');
            return array(false,0);
        }
        // Liga/MTurnier holen
        $row = JTable::getInstance('ligen', 'TableCLM');
        $row->load($round->liga); // Daten zu dieser ID laden

        $clmAccess = clm_core::$access;
        if (($row->sl != clm_core::$access->getJid() and $clmAccess->access('BE_tournament_edit_round') !== true) or $clmAccess->access('BE_tournament_edit_round') === false) {
            $this->app->enqueueMessage(JText::_('TOURNAMENT_NO_ACCESS'), 'warning');
            return array(false,$round->liga);
        }

        $task		= clm_core::$load->request_string('task');
        if ($task == 'possible') {
            $possible = 1;
        } else {
            $possible = 0;
        } // zu vergebender Wert 0/1

        // jetzt schreiben
        $round->meldung = $possible;
        if (!$round->store()) {
            $this->app->enqueueMessage($row->getError(), 'error');
            return array(false,$round->liga);
        }

        if ($possible) {
            $log_ext = $round->name." ".JText::_('RUNDE_INPUT_RELEASED');
            $this->app->enqueueMessage($round->name." ".JText::_('RUNDE_INPUT_RELEASED'));
        } else {
            $log_text = $round->name." ".JText::_('RUNDE_INPUT_BLOCKED');
            $this->app->enqueueMessage($round->name." ".JText::_('RUNDE_INPUT_BLOCKED'));
        }

        // Log
        $clmLog = new CLMLog();
        $clmLog->aktion = $log_text;
        $clmLog->params = array('lid' => $round->liga, 'runde' => $round->nr);
        $clmLog->write();

        return array(true,$round->liga);
    }

}
