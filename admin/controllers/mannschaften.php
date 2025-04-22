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

class CLMControllerMannschaften extends JControllerLegacy
{
    /**
     * Constructor
     */
    public function __construct($config = array())
    {
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

        $clmAccess = clm_core::$access;

        $db = JFactory::getDBO();

        $filter_order		= $mainframe->getUserStateFromRequest("$option.filter_order", 'filter_order', 'a.id', 'cmd');
        $filter_order_Dir	= $mainframe->getUserStateFromRequest("$option.filter_order_Dir", 'filter_order_Dir', '', 'word');
        $filter_state		= $mainframe->getUserStateFromRequest("$option.filter_state", 'filter_state', '', 'word');
        $filter_sid		= $mainframe->getUserStateFromRequest("$option.filter_sid", 'filter_sid', 0, 'int');
        $filter_lid		= $mainframe->getUserStateFromRequest("$option.filter_lid", 'filter_lid', 0, 'int');
        $filter_vid		= $mainframe->getUserStateFromRequest("$option.filter_vid", 'filter_vid', 0, 'string');
        $filter_catid		= $mainframe->getUserStateFromRequest("$option.filter_catid", 'filter_catid', 0, 'int');
        $search			= $mainframe->getUserStateFromRequest("$option.search", 'search', '', 'string');
        $search			= strtolower($search);
        $limit			= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart		= $mainframe->getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');

        $where = array();
        $where[] = ' c.archiv = 0';
        if ($filter_catid) {
            $where[] = 'a.published = '.(int) $filter_catid;
        }
        if ($filter_sid) {
            $where[] = 'a.sid = '.(int) $filter_sid.' AND c.archiv = 0';
        }
        if ($filter_lid) {
            $where[] = 'a.liga = '.(int) $filter_lid;
        }
        if ($filter_vid) {
            $where[] = "a.zps = '$filter_vid'";
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
        $count_man	= (count($where) ? ' WHERE ZPS =1 AND ' . implode(' AND ', $where) : '');
        $where 		= (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
        if ($filter_order == 'a.id') {
            $orderby 	= ' ORDER BY liga ASC, a.tln_nr '.$filter_order_Dir;
        } else {
            if ($filter_order == 'a.name' or $filter_order == 'a.man_nr' or $filter_order == 'd.name' or $filter_order == 'a.tln_nr' or $filter_order == 'a.mf' or $filter_order == 'a.liste' or $filter_order == 'b.Vereinname' or $filter_order == 'c.name' or $filter_order == 'a.ordering' or $filter_order == 'a.published') {
                $orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', a.id';
            } else {
                $filter_order = 'a.id';
                $orderby = "";
            }
        }
        // Zugangscheck
        if ($clmAccess->access('BE_team_edit') === false) {
            $section = 'info';
            $mainframe->enqueueMessage(JText::_('TEAM_NO_ACCESS'), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }
        if ($clmAccess->access('BE_team_edit') === true) {
            $where_sl = '';
        } else {
            $where_sl = ' AND d.sl = '.clm_core::$access->getJid();
        }
        // get the total number of records
        $query = ' SELECT COUNT(*) '
            .' FROM #__clm_mannschaften AS a'
            .' LEFT JOIN #__clm_saison AS c ON c.id = a.sid'
            .' LEFT JOIN #__clm_liga AS d ON a.liga = d.id'
        . $where.$where_sl
        ;
        $db->setQuery($query);
        $total = $db->loadResult();

        jimport('joomla.html.pagination');
        $pageNav = new JPagination($total, $limitstart, $limit);

        // Mannschaften ohne Verein zählen
        $query = ' SELECT COUNT(a.id) as id'
            .' FROM #__clm_mannschaften AS a '
            .' LEFT JOIN #__clm_saison AS c ON c.id = a.sid'
            .' LEFT JOIN #__clm_liga AS d ON a.liga = d.id'
            . $count_man.$where_sl    //.' LIMIT '.$limitstart.','.$limit
        ;
        $db->setQuery($query);
        $counter_man = $db->loadResult();

        if ($counter_man > 0) {
            $mainframe->enqueueMessage(JText::_('MANNSCHAFTEN_ES_GIBT').' '.$counter_man.' '.JText::_('MANNSCHAFTEN_ERROR_MANNSCHAFT_VEREIN'), 'notice');
        }

        // get the subset (based on limits) of required records
        $query = ' SELECT a.*, c.name AS saison, b.Vereinname as verein, u.name AS editor, d.name AS liga_name'
            .' FROM #__clm_mannschaften AS a'
            .' LEFT JOIN #__clm_saison AS c ON c.id = a.sid'
            .' LEFT JOIN #__clm_liga AS d ON a.liga = d.id'
            .' LEFT JOIN #__users AS u ON u.id = a.checked_out'
            .' LEFT JOIN #__clm_dwz_vereine AS b ON a.zps = b.ZPS AND b.sid = a.sid'
            .' LEFT JOIN #__clm_vereine AS e ON e.zps = a.zps AND e.sid = a.sid'
        . $where.$where_sl
        . $orderby
        ;
        try {
            $db->setQuery($query, $pageNav->limitstart, $pageNav->limit);
            $rows = $db->loadObjectList();
        } catch (Exception $e) {
            $mainframe->enqueueMessage($db->stderr(), 'error');
        }

        //CLM parameter auslesen
        $clm_config = clm_core::$db->config();
        if ($clm_config->field_search == 1) {
            $field_search = "js-example-basic-single";
        } else {
            $field_search = "inputbox";
        }

        // Filter
        // Statsusfilter
        //$lists['state']	= JHTML::_('grid.state',  $filter_state );
        $lists['state'] = CLMForm::selectState($filter_state);
        // Saisonfilter
        $sql = 'SELECT id, name FROM #__clm_saison WHERE archiv =0';
        $db->setQuery($sql);
        $saisonlist[]	= JHtml::_('select.option', '0', JText::_('MANNSCHAFTEN_SAISON'), 'id', 'name');
        $saisonlist         = array_merge($saisonlist, $db->loadObjectList());
        //	$lists['sid']      = JHtml::_('select.genericlist', $saisonlist, 'filter_sid', 'class="js-example-basic-single" size="1" onchange="document.adminForm.submit();"','id', 'name', intval( $filter_sid ) );
        $lists['sid']      = JHtml::_('select.genericlist', $saisonlist, 'filter_sid', 'class="'.$field_search.'" size="1" onchange="document.adminForm.submit();"', 'id', 'name', intval($filter_sid));
        // Ligafilter
        $sql = 'SELECT d.id AS cid, d.name FROM #__clm_liga as d'
            ." LEFT JOIN #__clm_saison as s ON s.id = d.sid"
            ." WHERE s.archiv = 0 ".$where_sl;
        $db->setQuery($sql);
        $ligalist[]	= JHtml::_('select.option', '0', JText::_('MANNSCHAFTEN_LIGA'), 'cid', 'name');
        $ligalist	= array_merge($ligalist, $db->loadObjectList());
        //	$lists['lid']	= JHtml::_('select.genericlist', $ligalist, 'filter_lid', 'class="js-example-basic-single" size="1" onchange="document.adminForm.submit();"','cid', 'name', intval( $filter_lid ) );
        $lists['lid']	= JHtml::_('select.genericlist', $ligalist, 'filter_lid', 'class="'.$field_search.'" size="1" onchange="document.adminForm.submit();"', 'cid', 'name', intval($filter_lid));

        // Vereinefilter laden
        $vlist	= CLMFilterVerein::vereine_filter(0);
        //	$lists['vid']	= JHtml::_('select.genericlist', $vlist, 'filter_vid', 'class="js-example-basic-single" size="1" onchange="document.adminForm.submit();"','zps', 'name', $filter_vid );
        $lists['vid']	= JHtml::_('select.genericlist', $vlist, 'filter_vid', 'class="'.$field_search.'" size="1" onchange="document.adminForm.submit();"', 'zps', 'name', $filter_vid);

        // Ordering
        $lists['order_Dir']	= $filter_order_Dir;
        $lists['order']		= $filter_order;
        // Suchefilter
        $lists['search'] = $search;
        require_once(JPATH_COMPONENT.DS.'views'.DS.'mannschaft.php');
        CLMViewMannschaften::mannschaften($rows, $lists, $pageNav, $option);
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
        $table		= JTable::getInstance('mannschaften', 'TableCLM');
        $user		= JFactory::getUser();
        $n		= count($cid);
        $clm_config = clm_core::$db->config();
        $unsuccessArray = array();
        $addressHandler = new AddressHandler();

        if (!$clm_config->googlemaps) {
            $mainframe->enqueueMessage(JText::_('MANNSCHAFT_GEO_OFF'), 'warning');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        } else {
            if ($n > 0) {
                foreach ($cid as $id) {
                    if ($table->load((int)$id)) {
                        $lokal_coord = $addressHandler->convertAddress($table->lokal);

                        if (is_null($lokal_coord) || $lokal_coord == -1) {
                            $unsuccessArray[] = $table->name;

                        } else {
                            $addressHandler->updateTeamCoordinates($lokal_coord, $table->id);
                        }
                    } else {
                        $mainframe->enqueueMessage($table->getError(), 'error');
                        $link = 'index.php?option='.$option.'&section='.$section;
                        $mainframe->redirect($link);
                    }
                }
            } else {
                $mainframe->enqueueMessage(JText::_('MANNSCHAFTEN_SELECT'), 'warning');
                $link = 'index.php?option='.$option.'&section='.$section;
                $mainframe->redirect($link);
            }

            $msg = ($n - count($unsuccessArray)) . "/" . $n . " " . JText::_('MANNSCHAFT_GEO_UPDATE') . "!<br>";
            if (count($unsuccessArray) > 0) {
                $msg = $msg . JText::_('MANNSCHAFT_GEO_FAILURE') . implode("<br>", $unsuccessArray);
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

        $row = JTable::getInstance('mannschaften', 'TableCLM');
        // load the row from the db table
        $row->load($cid[0]);
        $sid = $row->sid;
        if ($task == "add") {
            $sql = 'SELECT id FROM #__clm_saison WHERE archiv = 0 and published = 1';
            $db->setQuery($sql);
            $sid = $db->loadResult();
        } else {
            // Prüfen ob User Berechtigung zum editieren hat
            $sql = " SELECT sl, params FROM #__clm_liga "
                ." WHERE id =".$row->liga
            ;
            $db->setQuery($sql);
            $lid = $db->loadObjectList();
        }

        $clmAccess = clm_core::$access;

        if ($task == 'edit') {
            $saison		= JTable::getInstance('saisons', 'TableCLM');
            $saison->load($sid);
            // illegaler Einbruchversuch über URL !
            // evtl. mitschneiden !?!
            if ($saison->archiv == "1") { // AND clm_core::$access->getType() !== 'admin') {
                $mainframe->enqueueMessage(JText::_('MANNSCHAFTEN_ERROR_LIGA_ARCHIV'), 'warning');
                $mainframe->redirect('index.php?option='. $option.'&section='.$section, $msg, "message");
            }
            if ($clmAccess->access('BE_team_edit') === false) {
                $section = 'info';
                $mainframe->enqueueMessage(JText::_('TEAM_NO_ACCESS'), 'warning');
                $link = 'index.php?option='.$option.'&section='.$section;
                $mainframe->redirect($link);
            }

            if (isset($lid[0]) && $lid[0]->sl != clm_core::$access->getJid() and $clmAccess->access('BE_team_edit') !== true) {
                $mainframe->enqueueMessage(JText::_('MANNSCHAFTEN_ERROR_MANNSCHAFT_STAFFEL'), 'warning');
                $link = 'index.php?option='.$option.'&section='.$section;
                $mainframe->redirect($link);
            }
            if ($clmAccess->access('BE_team_edit') === true) {
                $where_sl = '';
            } else {
                $where_sl = ' AND a.sl = '.clm_core::$access->getJid();
            }
            // do stuff for existing records
            $row->checkout($user->get('id'));
        } else {
            if ($clmAccess->access('BE_team_create') === false) {
                $section = 'info';
                $mainframe->enqueueMessage(JText::_('TEAM_NO_ACCESS'), 'warning');
                $link = 'index.php?option='.$option.'&section='.$section;
                $mainframe->redirect($link);
            }
            $where_sl = '';
            // do stuff for new records
            $row->published = 0;
        }
        // Ligaliste
        $sql = " SELECT a.id as liga, a.name FROM #__clm_liga as a"
            ." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
            ." WHERE  s.archiv = 0 ".$where_sl;
        ;
        $db->setQuery($sql);
        $non_sl = $db->loadObjectList();
        // Falls kein SL einer Liga dann kann auch keine Mannschaft angelegt werden
        if (!isset($non_sl[0]->liga) and $clmAccess->access('BE_team_create') === false) {
            $mainframe->enqueueMessage(JText::_('MANNSCHAFTEN_ERROR_STAFFEL_MANNSCHAFT'), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }

        $db->setQuery($sql);
        if (!clm_core::$db->query($sql)) {
            $mainframe->enqueueMessage($db->getErrorMsg(), 'error');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }
        $ligalist[]	= JHtml::_('select.option', '0', JText::_('MANNSCHAFTEN_LIGA'), 'liga', 'name');
        $ligalist	= array_merge($ligalist, $db->loadObjectList());
        //	$lists['liga']	= JHtml::_('select.genericlist',   $ligalist, 'liga', 'class="js-example-basic-single" size="1" style="width:300px"','liga', 'name', $row->liga );
        $lists['liga']	= JHtml::_('select.genericlist', $ligalist, 'liga', 'class="'.$field_search.'" size="1" style="width:300px"', 'liga', 'name', $row->liga);
        $lists['published']	= JHtml::_('select.booleanlist', 'published', 'class="inputbox"', $row->published);

        // Vereinefilter laden
        $vereinlist	= CLMFilterVerein::vereine_filter(0);
        //	$lists['verein']= JHtml::_('select.genericlist',   $vereinlist, 'zps', 'class="js-example-basic-single" size="1" style="width:300px"','zps', 'name', $row->zps );
        $lists['verein'] = JHtml::_('select.genericlist', $vereinlist, 'zps', 'class="'.$field_search.'" size="1" style="width:300px"', 'zps', 'name', $row->zps);

        // Spielgemeinschaft
        //$lists['sg']= JHtml::_('select.genericlist',   $vereinlist, 'sg_zps', 'class="inputbox" size="1" ','zps', 'name', $row->sg_zps );
        // MFliste
        if ($task == 'edit') {
            $where = " AND ( a.zps = '".$row->zps."' OR FIND_IN_SET(a.zps,'".$row->sg_zps."')) AND a.published = 1";
        } else {
            $where = ' AND a.zps = 0 AND a.published = 1';
        }
        $tql = ' SELECT a.jid as mf, a.name as mfname'
            .' FROM #__clm_user AS a '
            ." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
            ." WHERE s.archiv = 0 "
            .$where;
        $db->setQuery($tql);
        if (!clm_core::$db->query($tql)) {
            $mainframe->enqueueMessage($db->getErrorMsg(), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }
        $mflist[]		= JHtml::_('select.option', '0', JText::_('MANNSCHAFTEN_MANNSCHAFTFUEHRER'), 'mf', 'mfname');
        $mflist			= array_merge($mflist, $db->loadObjectList());
        //	$lists['mf']	= JHtml::_('select.genericlist',   $mflist, 'mf', 'class="js-example-basic-single" size="1" style="width:300px"', 'mf', 'mfname', $row->mf );
        $lists['mf']	= JHtml::_('select.genericlist', $mflist, 'mf', 'class="'.$field_search.'" size="1" style="width:300px"', 'mf', 'mfname', $row->mf);
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
            $saisonlist[]	= JHtml::_('select.option', '0', JText::_('MANNSCHAFTEN_SAISON'), 'sid', 'name');
            $saisonlist	= array_merge($saisonlist, $db->loadObjectList());
        } else {
            $saisonlist	= $db->loadObjectList();
        }
        //	$lists['saison']= JHtml::_('select.genericlist',   $saisonlist, 'sid', 'class="js-example-basic-single" size="1" style="width:300px"','sid', 'name', $row->sid );
        $lists['saison'] = JHtml::_('select.genericlist', $saisonlist, 'sid', 'class="'.$field_search.'" size="1" style="width:300px"', 'sid', 'name', $row->sid);

        //Liga-Parameter aufbereiten
        $lid_params = array();
        if (isset($lid[0]->params)) {
            $paramsStringArray = explode("\n", $lid[0]->params);
            foreach ($paramsStringArray as $value) {
                $ipos = strpos($value, '=');
                if ($ipos !== false) {
                    $lid_params[substr($value, 0, $ipos)] = substr($value, $ipos + 1);
                }
            }
        }
        if (isset($lid_params['pgntype'])) {
            $lists['pgntype'] = $lid_params['pgntype'];
        }   //pgn Parameterübernahme
        else {
            $lists['pgntype'] = 0;
        }
        if (isset($lid_params['anz_sgp'])) {
            $lists['anz_sgp'] = $lid_params['anz_sgp'];
        }   //anz_sg Parameterübernahme
        else {
            $lists['anz_sgp'] = 1;
        }
        if (isset($lid_params['noOrgReference'])) {
            $lists['noOrgReference'] = $lid_params['noOrgReference'];
        }   //noOrgReference Parameterübernahme
        else {
            $lists['noOrgReference'] = 0;
        }
        //echo "<br><br>2lid_params:"; var_dump($lid_params); //die();
        // Spielgemeinschaft
        $sg_string = $row->sg_zps;
        $row->sg_zps = array();
        $row->sg_zps = explode(',', $sg_string);
        for ($i = 0; $i < $lists['anz_sgp']; $i++) {
            if (!isset($row->sg_zps[$i]) or $row->sg_zps[$i] === 0) {
                $row->sg_zps[$i] = '0';
            }
            //		$lists['sg'.$i]= JHtml::_('select.genericlist',   $vereinlist, 'sg_zps['.$i.']', 'class="js-example-basic-single" size="1" style="width:300px"','zps', 'name', $row->sg_zps[$i] );
            $lists['sg'.$i] = JHtml::_('select.genericlist', $vereinlist, 'sg_zps['.$i.']', 'class="'.$field_search.'" size="1" style="width:300px"', 'zps', 'name', $row->sg_zps[$i]);
        }
        require_once(JPATH_COMPONENT.DS.'views'.DS.'mannschaft.php');
        CLMViewMannschaften::mannschaft($row, $lists, $option);
    }


    public function save()
    {
        $mainframe	= JFactory::getApplication();

        // Check for request forgeries
        defined('clm') or die('Invalid Token');

        $option 	= clm_core::$load->request_string('option');
        $section 	= clm_core::$load->request_string('section');
        $db 		= JFactory::getDBO();
        $task 		= clm_core::$load->request_string('task');
        $row 		= JTable::getInstance('mannschaften', 'TableCLM');
        $pre_man	= clm_core::$load->request_int('pre_man');

        $post = $_POST;
        if (!$row->bind($post)) {
            $mainframe->enqueueMessage($db->getErrorMsg(), 'error');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }
        // Spielgemeinschaft
        if ($row->sg_zps != '') {
            $sg_array = array();
            $sg_array = $row->sg_zps;
            $row->sg_zps = '';
            $row->sg_zps = implode(',', $sg_array);
        }
        // pre-save checks

        if (!$row->check()) {
            $mainframe->enqueueMessage("Die Eingaben sind unvollständig.", 'error');
            switch ($task) {
                case 'apply':
                    if ($row->id != "") {
                        $link = 'index.php?option='.$option.'&section='.$section.'&task=edit&id='. $row->id ;
                    } else {
                        $link = 'index.php?option='.$option.'&section='.$section.'&task=add';
                    }
                    break;
                case 'save':
                default:
                    $link = 'index.php?option='.$option.'&section='.$section;
                    break;
            }
            $mainframe->redirect($link);
            return;
        }

        $liga_dat	= JTable::getInstance('ligen', 'TableCLM');
        $liga_dat->load($row->liga);

        // ich weiß nicht ob das so stimmt, aber bisher wurden die Variablen auch als leer gesetzt
        if ($liga_dat->rang != "" && $row->man_nr != "") {
            // prüfen ob Mannschaftsnummer schon vergeben wurde
            $query = " SELECT COUNT(man_nr) as countman FROM #__clm_mannschaften as m "
                ." LEFT JOIN #__clm_liga AS l ON m.liga = l.id"
                ." WHERE m.zps = '".$row->zps."'"
                ." AND m.man_nr = ".$row->man_nr
                ." AND m.sid =".$row->sid
                ." AND l.rang =".$liga_dat->rang
            ;
            $db->setQuery($query);
            $count_mnr = $db->loadObjectList();

            $query = " SELECT m.id FROM #__clm_mannschaften as m "
                ." LEFT JOIN #__clm_liga AS l ON m.liga = l.id"
                ." WHERE m.zps = '".$row->zps."'"
                ." AND m.man_nr = ".$row->man_nr
                ." AND m.sid =".$row->sid
                ." AND l.rang =".$liga_dat->rang
                ." ORDER BY m.id ASC "
                ." LIMIT 1 "
            ;
            $db->setQuery($query);
            $count_id = $db->loadObjectList();
        } else {
            $count_id = 0;
            $count_mnr = 0;
        }

        if ($count_mnr[0]->countman > 0 and (!isset($row->id) or $count_id[0]->id != $row->id)) {
            $mainframe->enqueueMessage(JText::_('MANNSCHAFTEN_ERROR_MANNSCHAFT_IST'), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }
        // Automatisches Ergänzen
        if ($row->name == JText::_('MANNSCHAFT').' '.$row->tln_nr and $row->lokal == '' and $task = 'apply') {
            $query = " SELECT id, name, lokal, adresse FROM #__clm_vereine "
                    ." WHERE zps = '".$row->zps."'"
                    ." AND sid =".$row->sid
                    ." LIMIT 1 "
            ;
            $db->setQuery($query);
            $club = $db->loadObjectList();
            if (isset($club[0])) {
                if ($row->name == 'Mannschaft '.$row->tln_nr) {
                    $row->name = $club[0]->name;
                }
                if ($row->name == JText::_('MANNSCHAFT').' '.$row->tln_nr) {
                    $row->name = $club[0]->name;
                }
                if ($row->lokal == '') {
                    $row->lokal = $club[0]->lokal;
                }
                if ($task == 'save') {
                    $task = 'apply';
                }
            } else {
                $mainframe->enqueueMessage(JText::_('MANNSCHAFTEN_ERROR_MANNSCHAFT_CLUB'), 'warning');
                $link = 'index.php?option='.$option.'&section='.$section.'&task=edit&id='. $row->id ;
                $mainframe->redirect($link);
            }
        }

        $aktion = JText::_('MANNSCHAFT_LOG_TEAM_EDIT');
        if (!$row->id) {
            $aktion = JText::_('MANNSCHAFT_LOG_TEAM_CREATE');
            $where = "sid = " . (int) $row->sid;
            $row->ordering = $row->getNextOrder($where);
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
            $addressHandler->updateTeamCoordinates($lokal_coord, $row->id);

            if (is_null($lokal_coord)) {
                $mainframe->enqueueMessage(JTEXT::_('WARNING_ADDRESS_LOOKUP'), 'warning');
            }
        }
        // Wenn Meldelistenmodus dann bei Änderung der Mannschaftsnummer Meldeliste updaten
        if ($liga_dat->rang == 0 and $pre_man != $row->man_nr) {
            $query = " UPDATE #__clm_meldeliste_spieler "
                ." SET  mnr = ".$row->man_nr
                ." WHERE sid = ".$row->sid
                ." AND lid = ".$row->liga
                ." AND mnr = ".$pre_man
                ." AND (zps = '".$row->zps."' OR zps = '".$row->sg_zps."')"
            ;
            //$db->setQuery($query);
            clm_core::$db->query($query);
        }

        // Ranking der Liga/MTurniers
        clm_core::$api->db_tournament_ranking($row->liga, true);

        switch ($task) {
            case 'apply':
                $msg = JText::_('MANNSCHAFTEN_AENDERUNGEN');
                $link = 'index.php?option='.$option.'&section='.$section.'&task=edit&id='. $row->id ;
                break;
            case 'save':
            default:
                $msg = JText::_('MANNSCHAFTEN_MANNSCHAFT');
                $link = 'index.php?option='.$option.'&section='.$section;
                break;
        }

        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = $aktion;
        $clmLog->params = array('sid' => $row->sid, 'lid' => $row->liga, 'zps' => $row->zps);
        $clmLog->write();

        $mainframe->enqueueMessage($msg);
        $mainframe->redirect($link);
    }


    public function cancel()
    {
        $mainframe	= JFactory::getApplication();
        // Check for request forgeries
        defined('clm') or die('Invalid Token');

        $option 	= clm_core::$load->request_string('option');
        $section 	= clm_core::$load->request_string('section');
        $msg = JText::_('MANNSCHAFTEN_AKTION');
        $mainframe->enqueueMessage($msg, 'message');
        $mainframe->redirect('index.php?option='. $option.'&section='.$section);
    }


    public function remove()
    {
        $mainframe	= JFactory::getApplication();

        // Check for request forgeries
        defined('clm') or die('Invalid Token');

        $clmAccess = clm_core::$access;

        $db 		= JFactory::getDBO();
        $cid 		= clm_core::$load->request_array_int('cid');
        $option 	= clm_core::$load->request_string('option');
        $section 	= clm_core::$load->request_string('section');

        if (count($cid) < 1) {
            $mainframe->enqueueMessage(JText::_('MANNSCHAFTEN_SELECT', true), 'warning');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }

        $row = JTable::getInstance('mannschaften', 'TableCLM');
        // load the row from the db table
        $row->load($cid[0]);

        // Prüfen ob User Berechtigung zum editieren hat
        $sql = " SELECT sl FROM #__clm_liga "
            ." WHERE id =".$row->liga
            ." AND sid =".$row->sid
        ;
        $db->setQuery($sql);
        $lid = $db->loadObjectList();

        // Zählen ob in den zugehörigen Ligen schon Ergebnisse gemeldet wurden
        $ligen = array();
        $vorher = 0;

        foreach ($cid as $id) {
            $row->load($id);
            if ($vorher != $row->liga) {
                $ligen[] = $row->liga;
                $vorher = $row->liga;
            }
        }
        $counter = implode(',', $ligen);

        if ($counter != "") {
            $query = " SELECT COUNT(id) as count FROM #__clm_rnd_man "
                .' WHERE lid IN ( '. $counter .' )'
                ." AND sid =".$row->sid
                .' AND gemeldet > 0';
            $db->setQuery($query);
            $liga_count = $db->loadObjectList();
            $count = $liga_count[0]->count;
        } else {
            $count = 0;
        }

        if ($count > 0) {
            $mainframe->enqueueMessage(JText::_('MANNSCHAFTEN_NO_LOESCH'), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }
        if ($clmAccess->access('BE_team_delete') === false) {
            $section = 'info';
            $mainframe->enqueueMessage(JText::_('TEAM_NO_ACCESS'), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }

        if ($lid[0]->sl != clm_core::$access->getJid() and $clmAccess->access('BE_team_delete') !== true) {
            $mainframe->enqueueMessage(JText::_('MANNSCHAFTEN_MANNSCHAFT_LOESCH'), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        } else {
            if ($clmAccess->access('BE_team_delete') === true) {
                $cids = implode(',', $cid);
                foreach ($cid as $cid) {
                    $row->load($cid);
                    $query = " DELETE FROM #__clm_meldeliste_spieler "
                        .' WHERE mnr ='.$row->man_nr
                        .' AND lid ='.$row->liga
                        ." AND sid =".$row->sid
                    ;
                    $db->setQuery($query);
                    //			$db->query();
                    clm_core::$db->query($query);
                }
                $query = " DELETE FROM #__clm_mannschaften "
                . ' WHERE id IN ( '. $cids .' )';

                $db->setQuery($query);
                //		if (!$db->query()) {
                if (!clm_core::$db->query($query)) {
                    echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n";
                }

                //		if (count($cid) == 1) { $msg = JText::_( 'MANNSCHAFT_MSG_DEL_ENTRY' ); }
                if (!is_array($cid)) {
                    $msg = JText::_('MANNSCHAFT_MSG_DEL_ENTRY');
                } else {
                    $msg = count($cid).JText::_('MANNSCHAFT_MSG_DEL_ENTRYS');
                }
            } else {
                $row->load($cid[0]);
                $del++;
                $query = " DELETE FROM #__clm_meldeliste_spieler "
                    .' WHERE mnr ='.$row->man_nr
                    .' AND lid ='.$row->liga
                    ." AND sid =".$row->sid
                ;
                $db->setQuery($query);
                //			$db->query();
                clm_core::$db->query($query);
                $query = " DELETE FROM #__clm_mannschaften WHERE id = ".$cid[0];
                $msg = JText::_('MANNSCHAFT_MSG_DEL_ENTRY');
            }
        }
        $db->setQuery($query);
        //		if (!$db->query()) {
        if (!clm_core::$db->query($query)) {
            echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n";
        }

        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = JText::_('MANNSCHAFT_LOG_TEAM_DELETE');
        $clmLog->params = array('cids' => $cids, 'zps' => $row->zps);
        $clmLog->write();

        $mainframe->enqueueMessage($msg, 'message');
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
        $option 	= clm_core::$load->request_string('option');
        $section 	= clm_core::$load->request_string('section');

        if (empty($cid)) {
            $mainframe->enqueueMessage('No items selected', 'warning');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }
        // Prüfen ob User Berechtigung zum publizieren hat
        $row = JTable::getInstance('mannschaften', 'TableCLM');
        $row->load($cid[0]);

        $sql = " SELECT sl FROM #__clm_liga "
            ." WHERE id =".$row->liga
            ." AND sid =".$row->sid
        ;
        $db->setQuery($sql);
        $lid = $db->loadObjectList();

        $clmAccess = clm_core::$access;
        if ($clmAccess->access('BE_team_edit') === false) {
            $section = 'info';
            $mainframe->enqueueMessage(JText::_('TEAM_NO_ACCESS'), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }

        if ($lid[0]->sl != clm_core::$access->getJid() and $clmAccess->access('BE_team_edit') !== true) {
            $mainframe->enqueueMessage(JText::_('MANNSCHAFTEN_MANNSCHAFT_PUB'), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        } else {

            if ($clmAccess->access('BE_team_edit') === true) {
                $cids = implode(',', $cid);
                $query = ' UPDATE #__clm_mannschaften'
                    .' SET published = '.(int) $publish
                    .' WHERE id IN ( '. $cids .' )'
                    .' AND ZPS !="0" '
                    .' AND ( checked_out IS NULL OR checked_out = 0 OR ( checked_out = '.(int) $user->get('id') .' ) )';
            } else {
                $query = 'UPDATE #__clm_mannschaften'
                    . ' SET published = '.(int) $publish
                    . ' WHERE id = '.$cid[0]
                    . ' AND ( checked_out IS NULL OR checked_out = 0 OR ( checked_out = '.(int) $user->get('id') .' ) )';
            }
        }
        //$db->setQuery( $query );
        if (!clm_core::$db->query($query)) {
            $mainframe->enqueueMessage($db->getErrorMsg(), 'error');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }
        if (count($cid) == 1) {
            $row = JTable::getInstance('mannschaften', 'TableCLM');
        }

        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = JText::_('MANNSCHAFT_LOG_TEAM')." ".$task;
        $table		= JTable::getInstance('mannschaften', 'TableCLM');
        $table->load($cid[0]);
        $clmLog->params = array('sid' => $table->sid, 'lid' => $table->liga, 'zps' => $table->zps, 'cids' => $cids);
        $clmLog->write();

        $mainframe->redirect('index.php?option='. $option.'&section='.$section);
    }
    /**
    * Moves the record up one position
    */
    public function orderdown()
    {
        CLMControllerMannschaften::order(1);
    }

    /**
    * Moves the record down one position
    */
    public function orderup()
    {
        CLMControllerMannschaften::order(-1);
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
        $section 	= clm_core::$load->request_string('section');

        $limit 		= clm_core::$load->request_string('limit', 0, '', 'int');
        $limitstart 	= clm_core::$load->request_string('limitstart', 0, '', 'int');

        $row = JTable::getInstance('mannschaften', 'TableCLM');
        $row->load($cid[0]);
        $row->move($inc, 'liga = '.(int) $row->liga.' AND published != 0');

        $msg 	= JText::_('MANNSCHAFT_MSG_SORT');
        $mainframe->enqueueMessage($msg);
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
        $section 	= clm_core::$load->request_string('section');

        $total		= count($cid);
        $order		= clm_core::$load->request_array_int('order');

        $row = JTable::getInstance('mannschaften', 'TableCLM');
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
        // execute update Order for each parent group
        $groupings = array_unique($groupings);
        foreach ($groupings as $group) {
            $row->reorder('liga = '.(int) $group);
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
        $section 	= clm_core::$load->request_string('section');
        $cid		= clm_core::$load->request_array_int('cid');
        $db		= JFactory::getDBO();
        $table		= JTable::getInstance('mannschaften', 'TableCLM');
        $user		= JFactory::getUser();
        $n		= count($cid);
        $this->setRedirect('index.php?option='.$option.'&section='.$section);

        // Prüfen ob User Berechtigung zum publizieren hat
        $row = JTable::getInstance('mannschaften', 'TableCLM');
        $row->load($cid[0]);

        $sql = " SELECT sl FROM #__clm_liga "
            ." WHERE id =".$row->liga
            ." AND sid =".$row->sid
        ;
        $db->setQuery($sql);
        $lid = $db->loadObjectList();

        $clmAccess = clm_core::$access;
        if ($clmAccess->access('BE_team_create') === false) {
            $section = 'info';
            $mainframe->enqueueMessage(JText::_('TEAM_NO_ACCESS'), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }

        if ($lid[0]->sl != clm_core::$access->getJid() and $clmAccess->access('BE_team_create') !== true) {
            $mainframe->enqueueMessage(JText::_('MANNSCHAFTEN_MANNSCHAFT_KOPIE'), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }
        $query = ' SELECT man_nr FROM #__clm_mannschaften '
            .' WHERE sid ='.$row->sid
            .' ORDER BY man_nr DESC LIMIT 1'
        ;
        $db->setQuery($query);
        $high_mnr = $db->loadResult();

        $query = ' SELECT tln_nr FROM #__clm_mannschaften '
            .' WHERE sid ='.$row->sid
            .' ORDER BY tln_nr DESC LIMIT 1'
        ;
        $db->setQuery($query);
        $high_tlnr = $db->loadResult();

        $p = 1;
        if ($n > 0) {

            foreach ($cid as $id) {

                if ($table->load((int)$id)) {

                    $table->id			= 0;
                    $table->name			= 'Kopie von ' . $table->name;
                    $table->published		= 0;
                    $table->man_nr			= $high_mnr + $p;
                    $table->tln_nr			= $high_tlnr + $p;
                    $table->liste			= 0;
                    $table->mf			= 0;
                    $p++;
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
            $mainframe->enqueueMessage(JText::_('MANNSCHAFTEN_ITEMS'), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }

        if ($n > 1) {
            $msg = JText::_('MANNSCHAFT_MSG_COPY_ENTRYS');
        } else {
            $msg = JText::_('MANNSCHAFT_MSG_COPY_ENTRY');
        }

        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = JText::_('MANNSCHAFT_LOG_TEAM_COPY');
        $table = JTable::getInstance('mannschaften', 'TableCLM');
        $table->load($cid[0]);
        $clmLog->params = array('sid' => $table->sid, 'lid' => $table->liga, 'zps' => $table->zps, 'cids' => implode(',', $cid));
        $clmLog->write();

        $this->setMessage(JText::_($n.$msg));
        $mainframe->enqueueMessage(JText::_($n.$msg));
        $mainframe->redirect('index.php?option='. $option.'&section='.$section);
    }

    public static function meldeliste()
    {
        defined('clm') or die('Invalid Token');
        $mainframe	= JFactory::getApplication();

        $db 		= JFactory::getDBO();
        $user 		= JFactory::getUser();
        $cid 		= clm_core::$load->request_array_int('cid');
        if (is_null($cid)) {
            $cid[0] = clm_core::$load->request_int('id');
        }
        $option 	= clm_core::$load->request_string('option');
        $section 	= clm_core::$load->request_string('section');

        // keine Meldeliste gewählt
        if ($cid[0] < 1) {
            $msg = JText::_('MANNSCHAFTEN_MELDELISTE');
            $mainframe->enqueueMessage($msg);
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }
        // load the row from the db table
        $row = JTable::getInstance('mannschaften', 'TableCLM');
        $row->load($cid[0]);

        // Konfigurationsparameter auslesen
        $config = clm_core::$db->config();
        $rang	= $config->rangliste;

        // load the row from the db table
        $rowliga	= JTable::getInstance('ligen', 'TableCLM');
        $liga		= $row->liga;
        $rowliga->load($liga);

        $link = 'index.php?option='.$option.'&section='.$section;

        // Prüfen ob User Berechtigung  hat
        $clmAccess = clm_core::$access;
        if ($clmAccess->access('BE_team_registration_list') === false) {
            $section = 'info';
            $mainframe->enqueueMessage(JText::_('TEAM_NO_ACCESS'), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }

        if ($rang == 0 and $rowliga->sl != clm_core::$access->getJid() and $clmAccess->access('BE_team_registration_list') !== true) {
            $mainframe->enqueueMessage(JText::_('MANNSCHAFTEN_MELDELISTE_BEARBEITEN'), 'warning');
            $mainframe->redirect($link);
        }

        if ($rowliga->rang > 0) {
            $mainframe->enqueueMessage(JText::_('MANNSCHAFTEN_NO_MELDELISTE'), 'warning');
            $mainframe->enqueueMessage(JText::_('MANNSCHAFTEN_MANNSCHAFT_RANG'), 'notice');
            $msg = JText::_('MANNSCHAFTEN_RANG_VEREIN');
            $mainframe->enqueueMessage($msg, 'message');
            $mainframe->redirect($link);
        }

        $row->checkout($user->get('id'));
        // Link MUSS hardcodiert sein !!!
        $mainframe->redirect('index.php?option='.$option.'&section=meldelisten&task=edit&id='.$cid[0]);
    }

    public static function delete_meldeliste()
    {
        // Check for request forgeries
        defined('clm') or die('Invalid Token');
        $mainframe	= JFactory::getApplication();

        $option 	= clm_core::$load->request_string('option');
        $section 	= clm_core::$load->request_string('section');
        $db 		= JFactory::getDBO();
        $task 		= clm_core::$load->request_string('task');
        $link = 'index.php?option='.$option.'&section='.$section;

        $cid 		= clm_core::$load->request_array_int('cid');
        if (is_null($cid)) {
            $cid[0] = clm_core::$load->request_int('id');
        }

        if (count($cid) < 1) {
            $mainframe->enqueueMessage(JText::_('MANNSCHAFTEN_LISTE_LOSCH'), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }

        // load the row from the db table
        $row 		= JTable::getInstance('mannschaften', 'TableCLM');
        $row->load($cid[0]);
        $rowliga	= JTable::getInstance('ligen', 'TableCLM');
        $liga		= $row->liga;
        $rowliga->load($liga);


        // Prüfen ob User Berechtigung zum löschen hat
        $clmAccess = clm_core::$access;
        if ($clmAccess->access('BE_team_registration_list') === false) {
            $section = 'info';
            $mainframe->enqueueMessage(JText::_('TEAM_NO_ACCESS'), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }

        if ($rang == 0 and $rowliga->sl != clm_core::$access->getJid() and $clmAccess->access('BE_team_registration_list') !== true) {
            $mainframe->enqueueMessage(JText::_('MANNSCHAFTEN_NO_MELDE_LOESCH'), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }

        // Wenn Rangliste dann nicht löschen
        if ($rowliga->rang > 0) {
            $mainframe->enqueueMessage(JText::_('MANNSCHAFTEN_NO_LOESCH'), 'warning');
            $mainframe->enqueueMessage(JText::_('MANNSCHAFTEN_MANNSCHAFT_RANG'), 'notice');
            $msg = JText::_('MANNSCHAFTEN_RANG_VEREIN');
            $mainframe->enqueueMessage($msg, 'message');
            $mainframe->redirect($link);
        }

        // Prüfen ob User Berechtigung zum publizieren hat
        if ($clmAccess->access('BE_team_registration_list') === false) {
            $section = 'info';
            $mainframe->enqueueMessage(JText::_('TEAM_NO_ACCESS'), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }
        if ($rowliga->sl != clm_core::$access->getJid() and $clmAccess->access('BE_team_registration_list') !== true) {

            $mainframe->enqueueMessage(JText::_('MANNSCHAFTEN_MELDE_LOESCH'), 'warning');
            $mainframe->redirect($link);
        }
        $zps	= $row->zps;
        $sg_zps	= $row->sg_zps;
        $man_nr	= $row->man_nr;
        $sid	= $row->sid;
        $lid	= $row->liga;

        $query	= "DELETE FROM #__clm_meldeliste_spieler"
            //." WHERE ( zps = '$zps' OR zps='$sg_zps')"
            ." WHERE ( zps = '$zps' OR FIND_IN_SET(zps,'".$sg_zps."') != 0 )"
            ." AND  mnr = ".$man_nr
            ." AND sid = ".$sid
            ." AND lid = ".$lid
            //." AND status = 0 "
        ;
        //$db->setQuery($query);
        clm_core::$db->query($query);

        $date 		= JFactory::getDate();
        $now 		= $date->toSQL();

        $query	= "UPDATE #__clm_mannschaften"
            ." SET edit_liste = ".clm_core::$access->getJid()
            ." , edit_datum = '$now'"
            ." , liste = 0"
            ." WHERE sid = ".$sid
            ." AND man_nr = ".$man_nr
            ." AND zps = '$zps'"
        ;
        //$db->setQuery($query);
        clm_core::$db->query($query);

        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = JText::_('MANNSCHAFT_LOG_LIST_DELETE');
        $clmLog->params = array('sid' => $sid, 'lid' => $lid, 'zps' => $zps, 'man' => $man_nr, 'cids' => $cid[0]);
        $clmLog->write();

        $msg = JText::_('MANNSCHAFTEN_MELDE_GELOESCHT');
        $mainframe->enqueueMessage($msg, 'message');
        $link = 'index.php?option='.$option.'&section='.$section;
        $mainframe->redirect($link);
    }

    public static function save_meldeliste()
    {
        $mainframe	= JFactory::getApplication();

        // Check for request forgeries
        defined('clm') or die('Invalid Token');

        $option 	= clm_core::$load->request_string('option');
        $section 	= clm_core::$load->request_string('section');
        $user		= JFactory::getUser();
        $meldung	= $user->get('id');

        $db 		= JFactory::getDBO();
        $task 		= clm_core::$load->request_string('task');
        $row 		= JTable::getInstance('mannschaften', 'TableCLM');
        $cid		= clm_core::$load->request_int('id');
        $row->load($cid);

        $stamm 		= clm_core::$load->request_string('stamm');
        $ersatz		= clm_core::$load->request_string('ersatz');
        $zps 		= clm_core::$load->request_string('zps');
        $mnr 		= clm_core::$load->request_string('mnr');
        $sid 		= clm_core::$load->request_string('sid');
        $max 		= clm_core::$load->request_string('max');
        $editor 	= clm_core::$load->request_string('editor');
        $liga 		= $row->liga;
        $sg_zps		= $row->sg_zps;

        // Datum und Uhrzeit für Meldung
        $date = JFactory::getDate();
        $now = $date->toSQL();

        // Konfigurationsparameter auslesen
        $config = clm_core::$db->config();
        $countryversion = $config->countryversion;

        // Liste wurde bereits abgegeben
        if ($row->liste > 0) {
            $aktion = JText::_('MANNSCHAFT_LOG_LIST_EDIT');
            $query	= "UPDATE #__clm_mannschaften"
                ." SET edit_liste = ".$meldung
                ." , edit_datum = '$now'"
                ." WHERE sid = ".$sid
                ." AND man_nr = ".$mnr
                ." AND zps = '$zps'"
            ;
        }
        // Liste wurde noch nicht abgegeben
        else {
            $aktion = JText::_('MANNSCHAFT_LOG_LIST_CREATE');
            $query	= "UPDATE #__clm_mannschaften"
                ." SET liste = ".$meldung
                ." , datum = '$now'"
                ." WHERE sid = ".$sid
                ." AND man_nr = ".$mnr
                ." AND zps = '$zps'"
            ;
        }
        //$db->setQuery($query);
        clm_core::$db->query($query);

        $query	= "DELETE FROM #__clm_meldeliste_spieler"
            . " WHERE lid = $liga"
            . " AND mnr = ".$mnr
            . " AND sid = ".$sid
            //."  AND ( zps = '$zps' OR zps='$sg_zps')"
            . " AND ( zps ='".$zps."' OR FIND_IN_SET(zps,'".$sg_zps."') != 0 )"
        ;

        //$db->setQuery($query);
        clm_core::$db->query($query);

        for ($y = 1; $y < 1 + ($stamm + $ersatz); $y++) {
            $spl	= clm_core::$load->request_string('spieler'.$y);
            $block	= clm_core::$load->request_int('check'.$y);
            $attr	= clm_core::$load->request_string('attr'.$y);
            if ($attr == '') {
                $attr = null;
            }
            $teil	= explode("-", $spl);
            if ($countryversion == "de") {
                $mgl_nr	= $teil[0];
                $PKZ    = '';
            } else {
                $mgl_nr	= 0;
                $PKZ    = $teil[0];
            }
            $tzps	= $teil[1];
            $dwz	= $teil[2];
            if ($dwz == '') {
                $dwz = '0';
            }
            $dwz_I0	= $teil[3];
            if ($dwz_I0 == '') {
                $dwz_I0 = '0';
            }

            if ($spl > 0) {
                $query	= "REPLACE INTO #__clm_meldeliste_spieler"
                    ." ( `sid`, `lid`, `mnr`, `snr`, `mgl_nr`, `PKZ`, `zps`, `ordering`, `gesperrt`, `start_dwz`, `start_I0`, `attr`) "
                    . " VALUES ('$sid','$liga','$mnr','$y','$mgl_nr','$PKZ','$tzps', 0, '$block','$dwz','$dwz_I0'";
                if (!is_null($attr)) {
                    $query	.= ",'$attr') ";
                } else {
                    $query	.= ", NULL) ";
                }
                //$db->setQuery($query);
                clm_core::$db->query($query);
            }
        }

        //Sperrkennzeichen synchronisieren
        for ($y = 1; $y < 1 + ($stamm + $ersatz); $y++) {
            $spl	= clm_core::$load->request_string('spieler'.$y, '');
            $block	= clm_core::$load->request_int('check'.$y, -1);
            if ($spl > 0) {
                $teil	= explode("-", $spl);
                if ($countryversion == "de") {
                    $mgl_nr	= $teil[0];
                    $PKZ    = '';
                } else {
                    $mgl_nr	= 0;
                    $PKZ    = $teil[0];
                }
                $tzps	= $teil[1];
                //			if ($block[$y] != $z_gesperrt) {
                $rc = clm_core::$api->db_syn_player_block($sid, $tzps, $mgl_nr, $block);
                if ($rc[0] === false) {
                    $msg = "m_updateError".$rc[1];
                    $mainframe->enqueueMessage($msg, 'error');
                } else {
                    $msg = $rc[1];
                    $mainframe->enqueueMessage($msg, 'message');
                }
                //			}
            }
        }

        $msg = $editor;
        switch ($task) {
            case 'apply':
                $msg = JText::_('MANNSCHAFTEN_AENDERUNGN');
                // Link MUSS hardcodiert sein !!!
                $link = 'index.php?option='.$option.'&section=meldelisten&task=edit&id='. $cid ;
                break;

            case 'save':
            default:
                $msg = JText::_('MANNSCHAFTEN_MANNSCHAFT_GESPEICHERT');
                $link = 'index.php?option='.$option.'&section='.$section;
                break;
        }

        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = $aktion;
        $clmLog->params = array('sid' => $sid, 'lid' => $liga, 'zps' => $zps, 'cids' => $cid);
        $clmLog->write();

        $mainframe->enqueueMessage($msg);
        $mainframe->redirect($link);
    }

    public static function apply_meldeliste()
    {
        $mainframe	= JFactory::getApplication();

        // Check for request forgeries
        defined('clm') or die('Invalid Token');

        $option 	= clm_core::$load->request_string('option');
        $section 	= clm_core::$load->request_string('section');
        $user		= JFactory::getUser();
        $meldung	= $user->get('id');

        $db 		= JFactory::getDBO();
        $task 		= clm_core::$load->request_string('task');
        $row 		= JTable::getInstance('mannschaften', 'TableCLM');
        $cid		= clm_core::$load->request_int('id');
        $row->load($cid);

        $stamm 		= clm_core::$load->request_string('stamm');
        $ersatz		= clm_core::$load->request_string('ersatz');
        $zps 		= clm_core::$load->request_string('zps');
        $mnr 		= clm_core::$load->request_string('mnr');
        $sid 		= clm_core::$load->request_string('sid');
        $max 		= clm_core::$load->request_string('max');
        $editor 	= clm_core::$load->request_string('editor');
        $liga 		= $row->liga;
        $sg_zps		= $row->sg_zps;

        // Datum und Uhrzeit für Meldung
        $date = JFactory::getDate();
        $now = $date->toSQL();

        // Konfigurationsparameter auslesen
        $config = clm_core::$db->config();
        $countryversion = $config->countryversion;

        // Liste wurde bereits abgegeben
        if ($row->liste > 0) {
            $aktion = JText::_('MANNSCHAFT_LOG_LIST_EDIT');
            $query	= "UPDATE #__clm_mannschaften"
                ." SET edit_liste = ".$meldung
                ." , edit_datum = '$now'"
                ." WHERE sid = ".$sid
                ." AND man_nr = ".$mnr
                ." AND zps = '$zps'"
            ;
        }
        // Liste wurde noch nicht abgegeben
        else {
            $aktion = JText::_('MANNSCHAFT_LOG_LIST_CREATE');
            $query	= "UPDATE #__clm_mannschaften"
                ." SET liste = ".$meldung
                ." , datum = '$now'"
                ." WHERE sid = ".$sid
                ." AND man_nr = ".$mnr
                ." AND zps = '$zps'"
            ;
        }
        //$db->setQuery($query);
        clm_core::$db->query($query);

        $query	= "DELETE FROM #__clm_meldeliste_spieler"
            . " WHERE lid = $liga"
            . " AND mnr = ".$mnr
            . " AND sid = ".$sid
            //."  AND ( zps = '$zps' OR zps='$sg_zps')"
            . " AND ( zps ='".$zps."' OR FIND_IN_SET(zps,'".$sg_zps."') != 0 )"
        ;

        //$db->setQuery($query);
        clm_core::$db->query($query);

        for ($y = 1; $y < 1 + ($stamm + $ersatz); $y++) {
            $spl	= clm_core::$load->request_string('spieler'.$y);
            $block	= clm_core::$load->request_int('check'.$y);
            $attr	= clm_core::$load->request_string('attr'.$y);
            if ($attr == '') {
                $attr = null;
            }
            $teil	= explode("-", $spl);
            if ($countryversion == "de") {
                $mgl_nr	= $teil[0];
                $PKZ    = '';
            } else {
                $mgl_nr	= 0;
                $PKZ    = $teil[0];
            }
            $tzps	= $teil[1];
            $dwz	= $teil[2];
            if ($dwz == '') {
                $dwz = '0';
            }
            $dwz_I0	= $teil[3];
            if ($dwz_I0 == '') {
                $dwz_I0 = '0';
            }

            if ($spl > 0) {
                $query	= "REPLACE INTO #__clm_meldeliste_spieler"
                    ." ( `sid`, `lid`, `mnr`, `snr`, `mgl_nr`, `PKZ`, `zps`, `ordering`, `gesperrt`, `start_dwz`, `start_I0`, `attr`) "
                    . " VALUES ('$sid','$liga','$mnr','$y','$mgl_nr','$PKZ','$tzps', 0,'$block','$dwz','$dwz_I0'";
                if (!is_null($attr)) {
                    $query	.= ",'$attr') ";
                } else {
                    $query	.= ", NULL) ";
                }
                //$db->setQuery($query);
                clm_core::$db->query($query);
            }
        }

        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = $aktion;
        $clmLog->params = array('sid' => $sid, 'lid' => $liga, 'zps' => $zps, 'cids' => $cid);
        $clmLog->write();

        $msg = JText::_('MANNSCHAFTEN_AENDERUNGN');
        // Link MUSS hardcodiert sein !!!
        $mainframe->enqueueMessage($msg);
        $link = 'index.php?option=com_clm&section=meldelisten&task=edit&id='. $cid ;
        $mainframe->redirect($link);
    }

    public static function spielfrei()
    {
        defined('clm') or die('Invalid Token');
        $mainframe	= JFactory::getApplication();

        $db 		= JFactory::getDBO();
        $user 		= JFactory::getUser();
        $cid 		= clm_core::$load->request_array_int('cid');
        $option 	= clm_core::$load->request_string('option');
        $section 	= clm_core::$load->request_string('section');

        // keine Meldeliste gewählt //
        if ($cid[0] < 1) {
            $msg = JText::_('MANNSCHAFTEN_MANNSCHAFT_AUS');
            $mainframe->enqueueMessage($msg, 'warning');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }
        // load the row from the db table
        $row = JTable::getInstance('mannschaften', 'TableCLM');
        $row->load($cid[0]);
        $tlnr = $row->tln_nr;


        // load the row from the db table
        $rowliga	= JTable::getInstance('ligen', 'TableCLM');
        $liga		= $row->liga;
        $rowliga->load($liga);

        $link = 'index.php?option='.$option.'&section='.$section;

        // Prüfen ob User Berechtigung zum publizieren hat
        $clmAccess = clm_core::$access;
        if ($clmAccess->access('BE_team_edit') === false) {
            $section = 'info';
            $mainframe->enqueueMessage(JText::_('TEAM_NO_ACCESS'), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }
        if ($rowliga->sl != clm_core::$access->getJid() and $clmAccess->access('BE_team_edit') !== true) {
            $mainframe->enqueueMessage(JText::_('MANNSCHAFTEN_MANNSCHAFT_SPIELFREI'), 'warning');
            $mainframe->redirect($link);
        }

        $query	= "DELETE FROM #__clm_rnd_spl"
            ." WHERE sid = ".$row->sid
            ." AND lid = ".$row->liga
            ." AND tln_nr = $tlnr "
        ;
        $db->setQuery($query);
        clm_core::$db->query($query);

        $query	= "UPDATE #__clm_rnd_man"
            ." SET brettpunkte = NULL, manpunkte = NULL, bp_sum = NULL, mp_sum = NULL, gemeldet = 1, wertpunkte = NULL "
            ." WHERE sid = ".$row->sid
            ." AND lid = ".$row->liga
            ." AND ( tln_nr = $tlnr OR gegner = $tlnr) "
        ;
        $db->setQuery($query);
        clm_core::$db->query($query);

        $query	= "UPDATE #__clm_mannschaften"
            ." SET name = 'spielfrei', zps = '0', man_nr = 0, liste = 0, edit_liste = 0, mf = 0, sg_zps = '0', published = 0 "
            ." WHERE sid = ".$row->sid
            ." AND liga = ".$row->liga
            ." AND tln_nr = $tlnr "
        ;
        $db->setQuery($query);
        clm_core::$db->query($query);
        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = JText::_('MANNSCHAFT_LOG_NO_GAMES');
        $clmLog->params = array('sid' => $row->sid, 'lid' => $row->liga, 'man' => $tlnr, 'cids' => $cid[0]);
        $clmLog->write();

        $msg = JText::_('MANNSCHAFTEN_MANNSCHAFT_SPIELF');
        $mainframe->enqueueMessage($msg, 'message');
        $mainframe->redirect('index.php?option='.$option.'&section='.$section);
    }


    public static function annull()			// Mannschaft annullieren d.h. Brett- und Wertpunkte in alle Begegnungen auf 0 setzen
    {
        defined('clm') or die('Invalid Token');
        $mainframe	= JFactory::getApplication();

        $db 		= JFactory::getDBO();
        $user 		= JFactory::getUser();
        $cid 		= clm_core::$load->request_array_int('cid');
        $option 	= clm_core::$load->request_string('option');
        $section 	= clm_core::$load->request_string('section');

        // keine Mannschaft gewählt //
        if ($cid[0] < 1) {
            $msg = JText::_('MANNSCHAFTEN_MANNSCHAFT_AUS');
            $mainframe->enqueueMessage($msg, 'warning');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }
        // load the row from the db table
        $row = JTable::getInstance('mannschaften', 'TableCLM');
        $row->load($cid[0]);
        $tlnr = $row->tln_nr;


        // load the row from the db table
        $rowliga	= JTable::getInstance('ligen', 'TableCLM');
        $liga		= $row->liga;
        $rowliga->load($liga);

        $link = 'index.php?option='.$option.'&section='.$section;

        // Prüfen ob User Berechtigung zum editieren hat
        $clmAccess = clm_core::$access;
        if ($clmAccess->access('BE_team_edit') === false) {
            $section = 'info';
            $mainframe->enqueueMessage(JText::_('TEAM_NO_ACCESS'), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }
        if ($rowliga->sl != clm_core::$access->getJid() and $clmAccess->access('BE_team_edit') !== true) {
            $mainframe->enqueueMessage(JText::_('MANNSCHAFTEN_MANNSCHAFT_ANNULL'), 'warning');
            $mainframe->redirect($link);
        }

        // Liga-Parameter auswerten
        $tparams = new clm_class_params($rowliga->params);
        $params_annul_proc = $tparams->get('annul_proc', '0');

        if ($params_annul_proc == '0') {
            $query	= "UPDATE #__clm_rnd_man"
                ." SET brettpunkte = 0, manpunkte = 0, bp_sum = NULL, mp_sum = NULL, gemeldet = 1, wertpunkte = 0 "
                ." WHERE sid = ".$row->sid
                ." AND lid = ".$row->liga
                ." AND ( tln_nr = $tlnr OR gegner = $tlnr) "
            ;
            $db->setQuery($query);
            clm_core::$db->query($query);
        } else {
            $query	= "UPDATE #__clm_rnd_man"
                ." SET brettpunkte = 0, manpunkte = 0, bp_sum = NULL, mp_sum = NULL, gemeldet = 1, wertpunkte = 0 "
                ." WHERE sid = ".$row->sid
                ." AND lid = ".$row->liga
                ." AND tln_nr = $tlnr "
            ;
            $db->setQuery($query);
            clm_core::$db->query($query);

            $query	= "UPDATE #__clm_rnd_man"
                ." SET brettpunkte = '".$rowliga->stamm."', manpunkte = 2, bp_sum = NULL, mp_sum = NULL, gemeldet = 1, wertpunkte = 0 "
                ." WHERE sid = ".$row->sid
                ." AND lid = ".$row->liga
                ." AND gegner = $tlnr "
            ;
            $db->setQuery($query);
            clm_core::$db->query($query);
        }

        clm_core::$api->db_tournament_ranking($row->liga, true);

        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = JText::_('MANNSCHAFT_LOG_ANNULL');
        $clmLog->params = array('sid' => $row->sid, 'lid' => $row->liga, 'man' => $tlnr, 'cids' => $cid[0]);
        $clmLog->write();

        $msg = JText::_('MANNSCHAFTEN_MANNSCHAFT_ANNULL');
        $mainframe->enqueueMessage($msg, 'message');
        $mainframe->redirect('index.php?option='.$option.'&section='.$section);
    }
}
