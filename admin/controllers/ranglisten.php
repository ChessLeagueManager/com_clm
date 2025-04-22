<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2024 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
// no direct access
defined('_JEXEC') or die('Restricted access');

class CLMControllerRanglisten extends JControllerLegacy
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
        $this->registerTask('update_clm', 'update_clm');
    }

    public function display($cachable = false, $urlparams = array())
    {
        $mainframe	= JFactory::getApplication();
        $option 	= clm_core::$load->request_string('option');
        $section	= clm_core::$load->request_string('section');
        $db		= JFactory::getDBO();

        $filter_gid		= $mainframe->getUserStateFromRequest("$option.filter_gid", 'filter_gid', 0, 'int');
        $filter_order		= $mainframe->getUserStateFromRequest("$option.filter_order", 'filter_order', 'a.id', 'cmd');
        $filter_order_Dir	= $mainframe->getUserStateFromRequest("$option.filter_order_Dir", 'filter_order_Dir', '', 'word');
        $filter_state		= $mainframe->getUserStateFromRequest("$option.filter_state", 'filter_state', '', 'word');
        $filter_sid		= $mainframe->getUserStateFromRequest("$option.filter_sid", 'filter_sid', 0, 'int');
        $filter_gid		= $mainframe->getUserStateFromRequest("$option.filter_gid", 'filter_gid', 0, 'int');
        $filter_vid		= $mainframe->getUserStateFromRequest("$option.filter_vid", 'filter_vid', 0, 'var');
        $filter_sg_vid1	= $mainframe->getUserStateFromRequest("$option.filter_sg_vid1", 'filter_sg_vid1', 0, 'var');
        $filter_sg_vid2	= $mainframe->getUserStateFromRequest("$option.filter_sg_vid2", 'filter_sg_vid2', 0, 'var');
        $filter_sg_vid3	= $mainframe->getUserStateFromRequest("$option.filter_sg_vid3", 'filter_sg_vid3', 0, 'var');
        $filter_sg_vid4	= $mainframe->getUserStateFromRequest("$option.filter_sg_vid4", 'filter_sg_vid4', 0, 'var');
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
        $where[] = ' c.archiv = 0';
        $zps	= clm_core::$load->request_string('zps');
        if ($zps > 0) {
            $filter_vid = $zps;
        }
        if ($filter_catid) {
            $where[] = 'a.published = '.(int) $filter_catid;
        }
        if ($filter_sid) {
            $where[] = 'a.sid = '.(int) $filter_sid;
        }
        if ($filter_gid) {
            $where[] = 'a.gid = '.(int) $filter_gid;
        }
        if ($filter_vid) {
            $where[] = "a.zps = '$filter_vid'";
        }


        if ($filter_state) {
            if ($filter_state == 'P') {
                $where[] = 'a.published = 1';
            } elseif ($filter_state == 'U') {
                $where[] = 'a.published = 0';
            }
        }

        $where2 = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

        if ($search) {
            $where[] = 'LOWER(v.Vereinname) LIKE "'.$db->escape('%'.$search.'%').'"';
        }
        $where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

        // get the total number of records
        $query = ' SELECT COUNT(*) '
            .' FROM #__clm_rangliste_id AS a'
            .' LEFT JOIN #__clm_saison AS c ON c.id = a.sid'
            . $where2
        ;
        $db->setQuery($query);
        $total = $db->loadResult();


        if ($filter_order == 'a.id') {
            $orderby 	= ' ORDER BY sid '.$filter_order_Dir.', n.id '.$filter_order_Dir;
        } else {
            if ($filter_order == 'a.Gruppe' or  $filter_order == 'a.Meldeschluss' or $filter_order == 'a.rang' or  $filter_order == 'a.user' or  $filter_order == 'a.saison' or $filter_order == 'a.ordering' or $filter_order == 'a.published') {
                $orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', a.id';
            } else {
                $filter_order = 'a.id';
                $orderby 	= '';
            }
        }


        jimport('joomla.html.pagination');
        $pageNav = new JPagination($total, $limitstart, $limit);

        // get the subset (based on limits) of required records
        $query = 'SELECT a.*, c.name AS saison, v.Vereinname as vname, n.Gruppe as gname '
        . ' FROM #__clm_rangliste_id AS a'
        . ' LEFT JOIN #__clm_saison AS c ON c.id = a.sid'
        . ' LEFT JOIN #__clm_dwz_vereine AS v ON v.ZPS = a.zps AND v.sid = a.sid'
        . ' LEFT JOIN #__clm_rangliste_name AS n ON n.sid = a.sid AND n.id = a.gid'
        //. ' LEFT JOIN #__clm_liga AS d ON a.liga = d.id'
        . ' LEFT JOIN #__users AS u ON u.id = a.checked_out'
        . $where
        . $orderby;

        try {
            $db->setQuery($query, $pageNav->limitstart, $pageNav->limit);
            $rows = $db->loadObjectList();
        } catch (Exception $e) {
            $mainframe->enqueueMessage($db->stderr(), 'error');
        }

        // Filter
        // Saisonfilter
        $sql = 'SELECT id, name FROM #__clm_saison WHERE archiv =0';
        $db->setQuery($sql);
        $saisonlist[]	= JHtml::_('select.option', '0', JText::_('RANGLISTE_SAISON_WAE'), 'id', 'name');
        $saisonlist         = array_merge($saisonlist, $db->loadObjectList());
        //	$lists['sid']      = JHtml::_('select.genericlist', $saisonlist, 'filter_sid', 'class="js-example-basic-single" size="1" onchange="document.adminForm.submit();"','id', 'name', intval( $filter_sid ) );
        $lists['sid']      = JHtml::_('select.genericlist', $saisonlist, 'filter_sid', 'class="'.$field_search.'" size="1" onchange="document.adminForm.submit();"', 'id', 'name', intval($filter_sid));

        // Gruppenfilter
        $sql = 'SELECT id, Gruppe FROM #__clm_rangliste_name WHERE published =1'.' AND sid = '.$filter_sid;
        $db->setQuery($sql);
        $gruppenlist[]	= JHtml::_('select.option', '0', JText::_('RANGLISTE_GRUPPE_AUS'), 'id', 'Gruppe');
        $gruppenlist         = array_merge($gruppenlist, $db->loadObjectList());
        //	$lists['gid']      = JHtml::_('select.genericlist', $gruppenlist, 'filter_gid', 'class="js-example-basic-single" size="1" onchange="document.adminForm.submit();"','id', 'Gruppe', intval( $filter_gid ) );
        $lists['gid']      = JHtml::_('select.genericlist', $gruppenlist, 'filter_gid', 'class="'.$field_search.'" size="1" onchange="document.adminForm.submit();"', 'id', 'Gruppe', intval($filter_gid));

        $db->setQuery($sql);
        $gruppenlistt[]	= JHtml::_('select.option', '0', JText::_('neue Rangfolgegruppe auswählen \n nur nötig beim Kopieren'), 'id', 'Gruppe');
        $gruppenlistt         = array_merge($gruppenlistt, $db->loadObjectList());
        $lists['tgid']      = JHtml::_('select.genericlist', $gruppenlistt, 'filter_tgid', 'class="'.$field_search.'" size="1" ', 'id', 'Gruppe', 0);

        // Vereinefilter laden
        $vereinefilter = CLMFilterVerein::vereine_filter(0);
        //	$lists['vid']	= JHtml::_('select.genericlist', $vereinefilter, 'filter_vid', 'class="js-example-basic-single" size="1" onchange="document.adminForm.submit();"','zps', 'name', $filter_vid );
        $lists['vid']	= JHtml::_('select.genericlist', $vereinefilter, 'filter_vid', 'class="'.$field_search.'" size="1" onchange="document.adminForm.submit();"', 'zps', 'name', $filter_vid);
        //	$lists['sg_vid']	= JHtml::_('select.genericlist', $vereinefilter, 'filter_sg_vid', 'class="js-example-basic-single" size="1" onchange="document.adminForm.submit();"','zps', 'name', $filter_sg_vid );
        $lists['sg_vid1']	= JHtml::_('select.genericlist', $vereinefilter, 'filter_sg_vid1', 'class="'.$field_search.'" size="1" onchange="document.adminForm.submit();"', 'zps', 'name', $filter_sg_vid1);
        $lists['sg_vid2']	= JHtml::_('select.genericlist', $vereinefilter, 'filter_sg_vid2', 'class="'.$field_search.'" size="1" onchange="document.adminForm.submit();"', 'zps', 'name', $filter_sg_vid2);
        $lists['sg_vid3']	= JHtml::_('select.genericlist', $vereinefilter, 'filter_sg_vid3', 'class="'.$field_search.'" size="1" onchange="document.adminForm.submit();"', 'zps', 'name', $filter_sg_vid3);
        $lists['sg_vid4']	= JHtml::_('select.genericlist', $vereinefilter, 'filter_sg_vid4', 'class="'.$field_search.'" size="1" onchange="document.adminForm.submit();"', 'zps', 'name', $filter_sg_vid4);

        // Suchefilter
        $lists['search'] = $search;
        // Ordering
        $lists['order_Dir']	= $filter_order_Dir;
        $lists['order']		= $filter_order;

        require_once(JPATH_COMPONENT.DS.'views'.DS.'ranglisten.php');
        CLMViewRanglisten::Ranglisten($rows, $lists, $pageNav, $option);
    }

    public function edit()
    {
        $mainframe	= JFactory::getApplication();
        $option 	= clm_core::$load->request_string('option');
        $section	= clm_core::$load->request_string('section');

        $filter_vid	= $mainframe->getUserStateFromRequest("$option.filter_vid", 'filter_vid', 0, 'var');
        $filter_sg_vid1	= $mainframe->getUserStateFromRequest("$option.filter_sg_vid1", 'filter_sg_vid1', 0, 'var');
        $filter_sg_vid2	= $mainframe->getUserStateFromRequest("$option.filter_sg_vid2", 'filter_sg_vid2', 0, 'var');
        $filter_sg_vid3	= $mainframe->getUserStateFromRequest("$option.filter_sg_vid3", 'filter_sg_vid3', 0, 'var');
        $filter_sg_vid4	= $mainframe->getUserStateFromRequest("$option.filter_sg_vid4", 'filter_sg_vid4', 0, 'var');
        $filter_sid	= $mainframe->getUserStateFromRequest("$option.filter_sid", 'filter_sid', 0, 'int');
        $filter_gid	= $mainframe->getUserStateFromRequest("$option.filter_gid", 'filter_gid', 0, 'int');

        //CLM parameter auslesen
        $clm_config = clm_core::$db->config();
        if ($clm_config->field_search == 1) {
            $field_search = "js-example-basic-single";
        } else {
            $field_search = "inputbox";
        }

        $db 		= JFactory::getDBO();
        $user 		= JFactory::getUser();
        $task 		= clm_core::$load->request_string('task');
        $cid 		= clm_core::$load->request_array_int('cid');
        if (is_null($cid)) {
            $cid[0] = clm_core::$load->request_int('id');
        }

        $row = JTable::getInstance('ranglisten', 'TableCLM');
        $vname = "";
        $sg_vname = array();
        $gname = "";
        $sname = "";

        if ($task == 'edit') {
            // illegaler Einbruchversuch über URL !
            // evtl. mitschneiden !?!
            $saison		= JTable::getInstance('saisons', 'TableCLM');
            $saison->load($row->sid);
            if ($saison->archiv == "1") {  // AND clm_core::$access->getType() !== 'admin') {
                $mainframe->enqueueMessage(JText::_('RANGLISTE_ARCHIV'), 'warning');
                $mainframe->redirect('index.php?option='. $option.'&section=vereine');
            }
            if ($cid[0] == "" and $task == 'edit') {
                $mainframe->enqueueMessage(JText::_('RANGLISTE_FALSCH'), 'warning');
                $mainframe->redirect('index.php?option='. $option.'&section=vereine');
            }
            // load the row from the db table
            $row->load($cid[0]);

            $sql = " SELECT Vereinname as vname FROM #__clm_dwz_vereine "
                ." WHERE ZPS ='".$row->zps."'"
                ." AND sid =".$row->sid
            ;
            $db->setQuery($sql);
            $vid 	= $db->loadObjectList();
            $vname	= $vid[0]->vname;

            $sg_vname	= array();
            if ($row->sg_zps > '00000') {
                //			$sql = " SELECT Vereinname as vname FROM #__clm_dwz_vereine "
                $sql = " SELECT ZPS, Vereinname as vname FROM #__clm_dwz_vereine "
//				." WHERE ZPS ='".$row->sg_zps."'"
                    ." WHERE ( FIND_IN_SET(ZPS,'".$row->sg_zps."') != 0 )"
                    ." AND sid =".$row->sid
                ;
                $db->setQuery($sql);
                $sg_vid 	= $db->loadObjectList();
                //			$sg_vname	= $sg_vid[0]->vname;
                $i = 0;
                foreach ($sg_vid as $sgvid) {
                    $i++;
                    $sg_vname[$i]	= $sgvid->vname;
                }
            }

            $sql = " SELECT Gruppe as gname FROM #__clm_rangliste_name "
                ." WHERE id =".$row->gid
                ." AND sid =".$row->sid
            ;
            $db->setQuery($sql);
            $gruppe	= $db->loadObjectList();
            $gname	= $gruppe[0]->gname;

            $sql = " SELECT name as sname FROM #__clm_saison "
                ." WHERE id =".$row->sid
            ;
            $db->setQuery($sql);
            $saison	= $db->loadObjectList();
            $sname	= $saison[0]->sname;
        }
        // Prüfen ob die gewählte Liste bereits existiert
        if ($filter_gid and $filter_vid and $filter_sid) {
            $sql = " SELECT id FROM #__clm_rangliste_id "
                ." WHERE gid =".intval($filter_gid)
                ." AND sid = ".intval($filter_sid)
                ." AND zps = '$filter_vid'"
            ;
            $db->setQuery($sql);
            $gid_check = $db->loadObjectList();
            if (count($gid_check) == 0) {
                $exist = 0;
            } else {
                $exist = $gid_check[0]->id;
            }
        } else {
            $exist = 0;
        }
        // Prüfen ob der Verein bereits als Partnerverein existiert
        if ($filter_gid and $filter_vid and $filter_sid) {
            $sql = " SELECT id FROM #__clm_rangliste_id "
                ." WHERE gid =".intval($filter_gid)
                ." AND sid = ".intval($filter_sid)
                ." AND sg_zps = '$filter_vid'"
            ;
            $db->setQuery($sql);
            $gid_check = $db->loadObjectList();
            if (count($gid_check) == 0) {
                $pa_exist = 0;
            } else {
                $pa_exist = $gid_check[0]->id;
            }
        } else {
            $pa_exist = 0;
        }
        // Prüfen ob eine Liste für den Partnerverein bereits existiert
        if ($filter_gid and $filter_sg_vid1 and $filter_sid) {
            $sql_sg_zps = $filter_sg_vid1.','.$filter_sg_vid2.','.$filter_sg_vid3.','.$filter_sg_vid4;
            $sql = " SELECT id FROM #__clm_rangliste_id "
                ." WHERE gid =".intval($filter_gid)
                ." AND sid = ".intval($filter_sid)
//			." AND (zps = '$filter_sg_vid1' OR zps = '$filter_sg_vid2' OR zps = '$filter_sg_vid3' OR zps = '$filter_sg_vid4'"
                ." AND (FIND_IN_SET(ZPS,'".$sql_sg_zps."') != 0 )"
            ;
            $db->setQuery($sql);
            $gid_check = $db->loadObjectList();
            if (count($gid_check) == 0) {
                $sg_exist = 0;
            } else {
                $sg_exist = $gid_check[0]->id;
            }
        } else {
            $sg_exist = 0;
        }
        // Prüfen ob Gruppe existiert
        if ($filter_gid and $filter_sid) {
            $sql = " SELECT id FROM #__clm_rangliste_name "
                ." WHERE sid = ".intval($filter_sid)
            ;
            $db->setQuery($sql);
            $gid_id		= $db->loadObjectList();
            $gid_exist	= $gid_id[0]->id;
        } else {
            $gid_exist = 0;
        }
        // Rangliste in Abhängigkeit der Auswahl von vid,lid,sid ausgeben
        if ($task == 'edit') {

            $sql = " SELECT Gruppe, Meldeschluss, geschlecht, alter_grenze, `alter`, status, anz_sgp "
                ." FROM #__clm_rangliste_name"
                ." WHERE id =".$row->gid
                ." AND sid = ".$row->sid
            ;
            $sql_zps	= $row->zps;
            $sql_sg_zps	= $row->sg_zps;
            $sql_gid	= $row->gid;
            $sql_sid	= $row->sid;

        } else {
            $sql = " SELECT Gruppe, Meldeschluss, geschlecht, alter_grenze, `alter`, status, anz_sgp "
                ." FROM #__clm_rangliste_name"
                ." WHERE id =".intval($filter_gid)
                ." AND sid = ".intval($filter_sid)
            ;
            $sql_zps	= $filter_vid;
            $sql_gid	= intval($filter_gid);
            $sql_sid	= intval($filter_sid);
        }
        $db->setQuery($sql);
        $gid	= $db->loadObjectList();

        $ges = "";
        $geb = "";
        $sta = "";
        $anz_sgp = 0;

        if ($gid) {
            $anz_sgp = $gid[0]->anz_sgp;
            $melde = explode("-", $gid[0]->Meldeschluss);
            $jahr = $melde[0];

            if ($gid[0]->alter_grenze == "1") {
                $geb = " AND a.Geburtsjahr < ".($jahr - $gid[0]->alter);
            }
            if ($gid[0]->alter_grenze == "2") {
                $geb = " AND a.Geburtsjahr > ".($jahr - ($gid[0]->alter + 1));
            }
            if ($gid[0]->geschlecht == 1) {
                $ges = " AND a.Geschlecht = 'W' ";
            }
            if ($gid[0]->geschlecht == 2) {
                $ges = " AND a.Geschlecht = 'M' ";
            }
            if ($gid[0]->status == 'A') {
                $sta = " AND a.Status = 'A' ";
            }
        }
        if ($task == 'edit') {
            $sql = " SELECT r.Rang, r.man_nr, a.sid,a.ZPS,a.Mgl_Nr,a.PKZ, a.DWZ,"
                ." a.DWZ_Index,a.Geburtsjahr,a.Spielername,a.Status, a.gesperrt"
                ." FROM #__clm_dwz_spieler as a"
                ." LEFT JOIN #__clm_rangliste_id as i ON i.sid = a.sid AND (i.zps = '$sql_zps') "
                ." LEFT JOIN #__clm_rangliste_spieler as r ON r.sid = a.sid AND r.ZPSmgl = a.ZPS AND r.Mgl_Nr = a.Mgl_Nr AND r.Gruppe = i.gid AND r.ZPS = i.zps "
//			." WHERE (a.ZPS = '$sql_zps' OR a.ZPS = '$sql_sg_zps')"
                ." WHERE (a.ZPS ='".$sql_zps."' OR FIND_IN_SET(a.ZPS,'".$sql_sg_zps."') != 0 )"
                ." AND i.id = ".$cid[0]
                ." AND a.sid =".$sql_sid
                .$geb.$ges.$sta
//			." ORDER BY r.man_nr,r.Rang ASC, a.DWZ DESC, a.DWZ_Index ASC, a.Spielername ASC "
                ." ORDER BY IFNULL(r.man_nr,999),r.Rang ASC, a.DWZ DESC, a.DWZ_Index ASC, a.Spielername ASC "
            ;

        } else {
            $sql_sg_zps	= '00000';
            if ($anz_sgp > 0) {
                $sql_sg_zps = $filter_sg_vid1;
            }
            if ($anz_sgp > 1) {
                $sql_sg_zps .= ','.$filter_sg_vid2;
            }
            if ($anz_sgp > 2) {
                $sql_sg_zps .= ','.$filter_sg_vid3;
            }
            if ($anz_sgp > 3) {
                $sql_sg_zps .= ','.$filter_sg_vid4;
            }
            $sql = " SELECT a.sid,a.ZPS,a.Mgl_Nr,a.PKZ,a.DWZ,a.DWZ_Index,a.Geburtsjahr,a.Spielername,a.Status, a.gesperrt"
                ." FROM #__clm_dwz_spieler as a"
//			." WHERE (a.ZPS = '$sql_zps' OR a.ZPS = '$sql_sg_zps')"
                ." WHERE (a.ZPS ='".$sql_zps."' OR FIND_IN_SET(a.ZPS,'".$sql_sg_zps."') != 0 )"
                ." AND sid =".$sql_sid
                .$geb.$ges.$sta
                ." ORDER BY a.DWZ DESC, a.DWZ_Index ASC, a.Spielername ASC "
            ;
        }
        $db->setQuery($sql);
        $spieler = $db->loadObjectList();

        if ($task == 'add') {
            foreach ($spieler as $spl) {
                if (!isset($spl->gesperrt)) {
                    $spl->gesperrt = '0';
                }
            }
        }

        // Anzahl Einträge zählen
        /*	$sql = " SELECT COUNT(ZPS) as ZPS FROM #__clm_rangliste_spieler "
                ." WHERE Gruppe =".$sql_gid
                ." AND sid = ".$sql_sid
                ." AND zps = '$sql_zps'"
                ;
            $db->setQuery($sql);
            $count_id	= $db->loadObjectList();
            $count		= $count_id[0]->ZPS;
        */
        if (is_null($spieler)) {
            $count = 0;
        } else {
            $count = count($spieler);
        }
        if (isset($row->liga)) {
            $sql = " SELECT sl FROM #__clm_liga "
                ." WHERE id =".$row->liga
            ;
            $db->setQuery($sql);
            $lid = $db->loadObjectList();
        } else {
            $lid = 0;
        }
        $clmAccess = clm_core::$access;
        if ($clmAccess->access('BE_club_edit_ranking') === false and $task == 'edit') {
            $mainframe->enqueueMessage(JText::_('RANGLISTE_STAFFEL'), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }
        if ($task == 'edit') {
            // do stuff for existing records
            $row->checkout($user->get('id'));
        } else {
            // do stuff for new records
            $row->published 	= 0;
        }

        $lists['published']	= JHtml::_('select.booleanlist', 'published', 'class="inputbox"', $row->published);

        // Saisonliste //
        $sql = "SELECT id, name FROM #__clm_saison WHERE archiv =0";
        $db->setQuery($sql);
        $saisonlist[]	= JHtml::_('select.option', '0', JText::_('RANGLISTE_SAISON_WAE'), 'id', 'name');
        $saisonlist         = array_merge($saisonlist, $db->loadObjectList());
        //	$lists['sid']      = JHtml::_('select.genericlist', $saisonlist, 'filter_sid', 'class="js-example-basic-single" size="1" style="width:300px" onchange="javascript:edit();"','id', 'name', intval( $filter_sid ) );
        $lists['sid']      = JHtml::_('select.genericlist', $saisonlist, 'filter_sid', 'class="'.$field_search.'" size="1" style="width:300px" onchange="javascript:edit();"', 'id', 'name', intval($filter_sid));

        if ($filter_sid == 0) {
            $filter_sid = clm_core::$access->getSeason();
        }
        // Gruppenliste //
        $sql = "SELECT id as gid, Gruppe FROM #__clm_rangliste_name"
            ." WHERE sid =".intval($filter_sid)
            ." AND published = 1"
        ;
        $db->setQuery($sql);
        if (!clm_core::$db->query($sql)) {
            $mainframe->enqueueMessage($db->getErrorMsg(), 'warning');
            $mainframe->redirect('index.php?option='.$option.'&section='.$section);
        }
        $gruppenlist[]	= JHtml::_('select.option', '0', JText::_('RANGLISTE_GRUPPE_AUS'), 'gid', 'Gruppe');
        $gruppenlist	= array_merge($gruppenlist, $db->loadObjectList());
        //	$lists['gruppe']= JHtml::_('select.genericlist',   $gruppenlist, 'filter_gid', 'class="js-example-basic-single" size="1" style="width:300px" onchange="javascript:edit();"','gid', 'Gruppe', intval( $filter_gid ) );
        $lists['gruppe'] = JHtml::_('select.genericlist', $gruppenlist, 'filter_gid', 'class="'.$field_search.'" size="1" style="width:300px" onchange="javascript:edit();"', 'gid', 'Gruppe', intval($filter_gid));

        // Vereinliste
        // Vereinefilter laden
        $vereinlist	= CLMFilterVerein::vereine_filter(0);
        //	$lists['vid']	= JHtml::_('select.genericlist', $vereinlist, 'filter_vid', 'class="js-example-basic-single" size="1" style="width:300px" onchange="javascript:edit();"','zps', 'name', $filter_vid );
        $lists['vid']	= JHtml::_('select.genericlist', $vereinlist, 'filter_vid', 'class="'.$field_search.'" size="1" style="width:300px" onchange="javascript:edit();"', 'zps', 'name', $filter_vid);
        //	$lists['sg_vid']	= JHtml::_('select.genericlist', $vereinlist, 'filter_sg_vid', 'class="js-example-basic-single" size="1" style="width:300px" onchange="javascript:edit();"','zps', 'name', $filter_sg_vid );
        $lists['sg_vid1']	= JHtml::_('select.genericlist', $vereinlist, 'filter_sg_vid1', 'class="'.$field_search.'" size="1" style="width:300px" onchange="javascript:edit();"', 'zps', 'name', $filter_sg_vid1);
        $lists['sg_vid2']	= JHtml::_('select.genericlist', $vereinlist, 'filter_sg_vid2', 'class="'.$field_search.'" size="1" style="width:300px" onchange="javascript:edit();"', 'zps', 'name', $filter_sg_vid2);
        $lists['sg_vid3']	= JHtml::_('select.genericlist', $vereinlist, 'filter_sg_vid3', 'class="'.$field_search.'" size="1" style="width:300px" onchange="javascript:edit();"', 'zps', 'name', $filter_sg_vid3);
        $lists['sg_vid4']	= JHtml::_('select.genericlist', $vereinlist, 'filter_sg_vid4', 'class="'.$field_search.'" size="1" style="width:300px" onchange="javascript:edit();"', 'zps', 'name', $filter_sg_vid4);
        $lists['anz_sgp']	= $anz_sgp;

        require_once(JPATH_COMPONENT.DS.'views'.DS.'ranglisten.php');
        $jid = 0;
        CLMViewRanglisten::Rangliste($spieler, $row, $lists, $option, $jid, $vname, $sg_vname, $gname, $sname, $cid, $exist, $sg_exist, $pa_exist, $count, $gid_exist);
    }

    public function save()
    {
        $mainframe	= JFactory::getApplication();

        // Check for request forgeries
        defined('clm') or die('Invalid Token');

        $option 	= clm_core::$load->request_string('option');
        $section	= clm_core::$load->request_string('section');

        $db		= JFactory::getDBO();
        $task		= clm_core::$load->request_string('task');
        $pre_task	= clm_core::$load->request_string('pre_task');
        $msg		= clm_core::$load->request_int('id');
        $exist		= clm_core::$load->request_string('exist');
        $row		= JTable::getInstance('ranglisten', 'TableCLM');
        if ($pre_task == 'edit' and is_numeric($msg)) {
            $row->load($msg);
        }
        if ($exist != "0" and $pre_task != "edit") {
            $mainframe->enqueueMessage(JText::_('RANGLISTE_LISTE_IST'), 'warning');
            $mainframe->enqueueMessage(JText::_('RANGLISTE_AENDERN'), 'notice');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }

        $post = $_POST;
        if (!$row->bind($post)) {
            $mainframe->enqueueMessage($row->getError(), 'error');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }
        // pre-save checks
        if (!$row->check()) {
            $mainframe->enqueueMessage($row->getError(), 'error');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }
        if ($pre_task == "add") {
            $filter_vid	= $mainframe->getUserStateFromRequest("$option.filter_vid", 'filter_vid', 0, 'var');
            $filter_sg_vid1	= $mainframe->getUserStateFromRequest("$option.filter_sg_vid1", 'filter_sg_vid1', 0, 'var');
            $filter_sg_vid2	= $mainframe->getUserStateFromRequest("$option.filter_sg_vid2", 'filter_sg_vid2', 0, 'var');
            $filter_sg_vid3	= $mainframe->getUserStateFromRequest("$option.filter_sg_vid3", 'filter_sg_vid3', 0, 'var');
            $filter_sg_vid4	= $mainframe->getUserStateFromRequest("$option.filter_sg_vid4", 'filter_sg_vid4', 0, 'var');
            $filter_sid	= $mainframe->getUserStateFromRequest("$option.filter_sid", 'filter_sid', 0, 'int');
            $filter_gid	= $mainframe->getUserStateFromRequest("$option.filter_gid", 'filter_gid', 0, 'int');

            $zps = $filter_vid ;
            if ($filter_sg_vid1 > '00000') {
                $sg_zps = $filter_sg_vid1;
            } else {
                $sg_zps = '00000';
            }
            if ($filter_sg_vid2 > '00000') {
                $sg_zps .= ','.$filter_sg_vid2;
            }
            if ($filter_sg_vid3 > '00000') {
                $sg_zps .= ','.$filter_sg_vid3;
            }
            if ($filter_sg_vid3 > '00000') {
                $sg_zps .= ','.$filter_sg_vid4;
            }
            $sid = intval($filter_sid);
            $gid = intval($filter_gid);

            $row->zps = $zps;
            $row->sg_zps = $sg_zps;
            $row->sid = $sid;
            $row->gid = $gid;

            $aktion = JText::_('RANGLISTE_LOG_ADDED');
        } else {

            $zps = $row->zps;
            $sg_zps = $row->sg_zps;
            $sid = $row->sid;
            $gid = $row->gid;

            $aktion = JText::_('RANGLISTE_LOG_EDIT');
        }

        if (!$row->id) {
            $where = "sid = " . (int) $row->sid;
            $row->ordering = $row->getNextOrder($where);
        }
        // save the changes
        if (!$row->store()) {
            $mainframe->enqueueMessage($row->getError(), 'error');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }


        // Rangnummern in #__clm_rangliste_spieler abspeichern
        $count		= clm_core::$load->request_int('count');

        $query	= " DELETE FROM #__clm_rangliste_spieler "
            ." WHERE Gruppe = ".$gid
            ." AND ZPS = '$zps'"
            ." AND sid =".$sid
        ;
        //$db->setQuery($query);
        clm_core::$db->query($query);

        //vor Löschen der Meldelisten Start-DWZ, u.a. sichern
        $query	= " SELECT * FROM #__clm_meldeliste_spieler "
            ." WHERE status = ".$gid
            ." AND (ZPS = '$zps' OR ZPS = '$sg_zps')"
            ." AND sid =".$sid
        ;
        $db->setQuery($query);
        $dwz_meldeliste	= $db->loadObjectList();
        $old_ml = array();
        foreach ($dwz_meldeliste as $dwz_ml) {
            $old_ml[$dwz_ml->lid.' '.$dwz_ml->mnr.' '.$dwz_ml->zps.' '.$dwz_ml->mgl_nr] = $dwz_ml;
        }

        //Löschen der Meldelisten
        $query	= " DELETE FROM #__clm_meldeliste_spieler "
            ." WHERE status = ".$gid
            ." AND (ZPS = '$zps' OR ZPS = '$sg_zps')"
            ." AND sid =".$sid
        ;
        //$db->setQuery($query);
        clm_core::$db->query($query);

        $query	= " SELECT a.liga, a.man_nr FROM #__clm_mannschaften as a"
            ." LEFT JOIN #__clm_liga as l ON l.id = a.liga and l.sid = a.sid"
            ." WHERE a.zps = '$zps'"
            ." AND a.sid =".$sid
            ." AND l.rang = ".$gid
            ." GROUP BY a.man_nr "
            ." ORDER BY a.man_nr ASC "
        ;
        $db->setQuery($query);
        $lid_rang	= $db->loadObjectList();

        $ZPSmgl	= array();
        $mgl	= array();
        $pkz	= array();
        $mnr	= array();
        $rang	= array();
        $block	= array();

        // Rangliste und Arrays schreiben
        for ($y = 0; $y < $count; $y++) {
            $ZPSmgl[]	= trim(clm_core::$load->request_string('ZPSM'.$y));
            $mgl[]	= clm_core::$load->request_string('MGL'.$y);
            $pkz[]	= clm_core::$load->request_string('PKZ'.$y);
            $mnr[]	= clm_core::$load->request_string('MA'.$y);
            $rang[]	= clm_core::$load->request_string('RA'.$y);
            $block[]	= clm_core::$load->request_int('check'.$y);

            if ($mnr[$y] !== "99" and $mnr[$y] !== "0" and $mnr[$y] !== "") {
                $query = " INSERT INTO #__clm_rangliste_spieler "
                    ." (`Gruppe`, `ZPS`, `ZPSmgl`, `Mgl_Nr`, `PKZ`, `Rang`, `man_nr`, `sid`, `gesperrt`) "
                    ." VALUES ('$gid','$zps','$ZPSmgl[$y]','$mgl[$y]','$pkz[$y]','$rang[$y]','$mnr[$y]','$sid','$block[$y]') "
                ;
                //$db->setQuery($query);
                clm_core::$db->query($query);
            }
        }

        // Meldelisten schreiben
        for ($x = 0; $x < count($lid_rang); $x++) {
            $liga	= $lid_rang[$x]->liga;
            $man_nr	= $lid_rang[$x]->man_nr;

            $sn_cnt = 1;
            $snr_counter = 1;

            for ($y = 0; $y < $count; $y++) {
                //		$dkey = $liga.' '.$man_nr.' '.$zps.' '.intval($mgl[$y]);
                $dkey = $liga.' '.$man_nr.' '.$ZPSmgl[$y].' '.intval($mgl[$y]);
                if (isset($old_ml[$dkey])) {
                    $z_ordering	 = $old_ml[$dkey]->ordering;
                    $z_start_dwz = $old_ml[$dkey]->start_dwz;
                    $z_start_I0	 = $old_ml[$dkey]->start_I0;
                    $z_DWZ		 = $old_ml[$dkey]->DWZ;
                    $z_I0	 	 = $old_ml[$dkey]->I0;
                    $z_Punkte	 = $old_ml[$dkey]->Punkte;
                    $z_Partien	 = $old_ml[$dkey]->Partien;
                    $z_We		 = $old_ml[$dkey]->We;
                    $z_Leistung	 = $old_ml[$dkey]->Leistung;
                    $z_EFaktor	 = $old_ml[$dkey]->EFaktor;
                    $z_Niveau	 = $old_ml[$dkey]->Niveau;
                    $z_sum_saison = $old_ml[$dkey]->sum_saison;
                    $z_gesperrt	 = $old_ml[$dkey]->gesperrt;
                } else {
                    $z_ordering = 0;
                    $z_start_dwz = null;
                    $z_start_I0 = null;
                    //echo "<br>n:".$dkey."  :"; var_dump($z_dwz);
                    $z_DWZ		 = 0;
                    $z_I0	 	 = 0;
                    $z_Punkte	 = 0;
                    $z_Partien	 = 0;
                    $z_We		 = 0;
                    $z_Leistung	 = 0;
                    $z_EFaktor	 = 0;
                    $z_Niveau	 = 0;
                    $z_sum_saison = 0;
                    $z_gesperrt	 = 0;
                }
                //if (($mnr[$y] >= $lid_rang[$x]->man_nr AND $rang[$y] < 1000 ) OR ($mnr[$y] == $lid_rang[$x]->man_nr) ) {
                if (is_null($z_gesperrt) or $z_gesperrt == '') {
                    $z_gesperrt = '0';
                }
                if ($mnr[$y] >= $lid_rang[$x]->man_nr) {
                    if ($z_start_dwz == null or $z_start_dwz == 0) {
                        $query = " INSERT INTO #__clm_meldeliste_spieler "
                            ." (`sid`, `lid`, `mnr`, `snr`, `mgl_nr`, `zps`,`status`,`ordering`,`start_dwz`,`start_I0`"
                            .",`DWZ`, `I0`, `Punkte`, `Partien`, `We`, `Leistung`,`EFaktor`,`Niveau`,`sum_saison`,`gesperrt`)"
                            ." VALUES ('$sid','$liga','$man_nr','$snr_counter','$mgl[$y]','$ZPSmgl[$y]','$gid','$z_ordering',NULL,NULL"
                            .",'$z_DWZ','$z_I0','$z_Punkte','$z_Partien','$z_We','$z_Leistung','$z_EFaktor','$z_Niveau','$z_sum_saison','$block[$y]') "
                        ;
                    } else {
                        $query = " INSERT INTO #__clm_meldeliste_spieler "
                              ." (`sid`, `lid`, `mnr`, `snr`, `mgl_nr`, `zps`,`status`,`ordering`,`start_dwz`,`start_I0`"
                              .",`DWZ`, `I0`, `Punkte`, `Partien`, `We`, `Leistung`,`EFaktor`,`Niveau`,`sum_saison`,`gesperrt`)"
                              ." VALUES ('$sid','$liga','$man_nr','$snr_counter','$mgl[$y]','$ZPSmgl[$y]','$gid','$z_ordering','$z_start_dwz','$z_start_I0'"
                              .",'$z_DWZ','$z_I0','$z_Punkte','$z_Partien','$z_We','$z_Leistung','$z_EFaktor','$z_Niveau','$z_sum_saison','$block[$y]') "
                        ;
                    }
                    clm_core::$db->query($query);
                    $sn_cnt++;
                    $snr_counter++;
                }
            }
            if ($sn_cnt > 1) {
                $query = " UPDATE #__clm_mannschaften "
                    ." SET  liste = 1"
                    ." WHERE sid = $sid AND liga = $liga AND man_nr = $man_nr AND zps = '$zps' "
                ;
                //$db->setQuery($query);
                clm_core::$db->query($query);
            }
        }

        if ($sn_cnt == "1") {
            $mainframe->enqueueMessage(JText::_('RANGLISTE_MN_RANG'), 'notice');
            $mainframe->enqueueMessage(JText::_('RANGLISTE_ERGEBNIS'), 'notice');
            $mainframe->enqueueMessage(JText::_('RANGLISTE_MN_MANAGER'), 'notice');
        }

        //Sperrkennzeichen sybchronisieren
        for ($y = 0; $y < $count; $y++) {
            $block_a	= clm_core::$load->request_int('BLOCK_A'.$y);
            if ($block[$y] != $block_a) {
                $rc = clm_core::$api->db_syn_player_block($sid, $ZPSmgl[$y], intval($mgl[$y]), $block[$y]);
                if ($rc[0] === false) {
                    $msg = "m_updateError".$rc[1];
                    $mainframe->enqueueMessage($msg, 'error');
                } else {
                    $msg = $rc[1];
                    $mainframe->enqueueMessage($msg, 'message');
                }
            }
        }

        switch ($task) {
            case 'apply':
                $msg = JText::_('RANGLISTE_MSG_CHANGES_SAVED');
                $link = 'index.php?option='.$option.'&section='.$section.'&task=edit&id='. $row->id ;
                break;
            case 'save':
            default:
                $msg = JText::_('RANGLISTE_MSG_SAVED');
                $link = 'index.php?option='.$option.'&section='.$section;
                break;
        }

        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = $aktion;
        $clmLog->params = array('sid' => $sid, 'gid' => $gid, 'zps' => $zps);
        $clmLog->write();

        $mainframe->enqueueMessage($msg, 'message');
        $mainframe->redirect($link);
    }

    public function cancel()
    {
        $mainframe	= JFactory::getApplication();
        // Check for request forgeries
        defined('clm') or die('Invalid Token');

        $option 	= clm_core::$load->request_string('option');
        $section	= clm_core::$load->request_string('section');
        $id		= clm_core::$load->request_int('id');
        $row 		= JTable::getInstance('ranglisten', 'TableCLM');
        $filter_vid	= $mainframe->getUserStateFromRequest("$option.filter_vid", 'filter_vid', 0, 'var');
        $msg = JText::_('RANGLISTE_AKTION'); //$filter_vid;
        $mainframe->enqueueMessage($msg, 'message');
        $mainframe->redirect('index.php?option='. $option.'&section='.$section);
    }

    public function remove()
    {
        $mainframe	= JFactory::getApplication();

        // Check for request forgeries
        defined('clm') or die('Invalid Token');

        $db 		= JFactory::getDBO();
        $cid 		= clm_core::$load->request_array_int('cid');
        $option 	= clm_core::$load->request_string('option');
        $section	= clm_core::$load->request_string('section');

        if (count($cid) < 1) {
            $mainframe->enqueueMessage(JText::_('RANGLISTE_SELECT', true), 'warning');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }

        // Prüfen ob User Berechtigung zum löschen hat //
        $sql = " SELECT l.sl FROM #__clm_mannschaften as a "
            ." LEFT JOIN #__clm_liga as l ON ( l.id= a.liga AND l.sid = a.sid) "
            ." WHERE a.id = ".$cid[0]
        ;
        $db->setQuery($sql);
        $lid = $db->loadObjectList();

        // Wenn User nicht Admin oder DWZ prüfen ob SL der Staffel //
        $clmAccess = clm_core::$access;
        if ($clmAccess->access('BE_club_edit_ranking') === false) {
            $mainframe->enqueueMessage(JText::_('RANGLISTE_RL_STAFFEL', true), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        } else {
            $row	= JTable::getInstance('ranglisten', 'TableCLM');
            $row->load($cid[0]);

            $query	= " DELETE FROM #__clm_rangliste_spieler "
                ." WHERE Gruppe =".$row->gid
                ." AND ZPS ='$row->zps'"
                ." AND sid =".$row->sid
            ;
            //$db->setQuery( $query );
            if (!clm_core::$db->query($query)) {
                echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n";
            }

            $query = " DELETE FROM #__clm_rangliste_id "
                ." WHERE id = $cid[0]"
                ." AND sid =".$row->sid
            ;
            //$db->setQuery( $query );
            if (!clm_core::$db->query($query)) {
                echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n";
            }

            $query = " DELETE FROM #__clm_meldeliste_spieler "
                ." WHERE status = ".$row->gid
                ." AND (zps = '$row->zps' OR zps = '$row->sg_zps')"
                ." AND sid =".$row->sid
            ;
            //$db->setQuery( $query );
            if (!clm_core::$db->query($query)) {
                echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n";
            }
        }
        //		}

        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = $aktion;
        $clmLog->params = array('cid' => $cids, 'zps' => $row->zps);
        $clmLog->write();

        if (count($cid) == 1) {
            $msg = JText::_('RANGLISTE_MSG_DEL_ENTRY');
        } else {
            $msg = count($cid)." ".JText::_('RANGLISTE_MSG_DEL_ENTRYS');
        }

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
        $section	= clm_core::$load->request_string('section');

        if (empty($cid)) {
            $mainframe->enqueueMessage('No items selected', 'warning');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }
        // Prüfen ob User Berechtigung zum publizieren hat
        $sql = " SELECT l.sl FROM #__clm_mannschaften as a "
            ." LEFT JOIN #__clm_liga as l ON ( l.id= a.liga AND l.sid = a.sid) "
            ." WHERE a.id = ".$cid[0]
        ;
        $db->setQuery($sql);
        $lid = $db->loadObjectList();

        // Wenn User nicht Admin oder DWZ prüfen ob SL der Staffel
        $clmAccess = clm_core::$access;
        if ($clmAccess->access('BE_club_edit_ranking') === false) {
            $mainframe->enqueueMessage(JText::_('RANGLISTE_STAL_RANG'), 'warning');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        } else {
            $cids = implode(',', $cid);
            $query = 'UPDATE #__clm_rangliste_id'
                . ' SET published = '.(int) $publish
                . ' WHERE id IN ( '. $cids .' )'
                . ' AND ( checked_out IS NULL OR checked_out = 0 OR ( checked_out = '.(int) $user->get('id') .' ) )';
        }
        //$db->setQuery( $query );
        if (!clm_core::$db->query($query)) {
            $mainframe->enqueueMessage($db->getErrorMsg(), 'error');
            $link = 'index.php?option='.$option.'&section='.$section;
            $mainframe->redirect($link);
        }
        if (count($cid) == 1) {
            $row = JTable::getInstance('ranglisten', 'TableCLM');
        }

        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = JText::_('RANGLISTE_LOG')." ".$task;
        $table		= &JTable::getInstance('ranglisten', 'TableCLM');
        $table->load($cid[0]);
        $clmLog->params = array('sid' => $table->sid, 'lid' => $lid, 'zps' => $table->zps, 'cids' => $cids);
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
        $section	= clm_core::$load->request_string('section');

        $limit 		= clm_core::$load->request_int('limit', 0, '', 'int');
        $limitstart 	= clm_core::$load->request_int('limitstart', 0, '', 'int');

        $row = JTable::getInstance('mannschaften', 'TableCLM');
        $row->load($cid[0]);
        $row->move($inc, 'liga = '.(int) $row->liga.' AND published != 0');

        $msg 	= JText::_('RANGLISTE_MSG_SORT');
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
                    $mainframe->enqueueMessage(JText::_('RANGLISTE_SELECT', true), 'warning');
                    $mainframe->redirect('index.php?option='. $option.'&section='.$section);
                }
            }
        }
        // execute update Order for each parent group
        $groupings = array_unique($groupings);
        foreach ($groupings as $group) {
            $row->reorder('liga = '.(int) $group);
        }
        $msg 	= 'New ordering saved';
        $mainframe->redirect('index.php?option='. $option.'&section='.$section);
    }


    public function copy()
    {
        $mainframe	= JFactory::getApplication();
        // Check for request forgeries
        defined('clm') or die('Invalid Token');
        $option 	= clm_core::$load->request_string('option');
        $section	= clm_core::$load->request_string('section');
        $cid		= clm_core::$load->request_array_int('cid');
        $db		= JFactory::getDBO();
        $table		= JTable::getInstance('ranglisten', 'TableCLM');
        $user		= JFactory::getUser();
        $n		= count($cid);
        $this->setRedirect('index.php?option='.$option.'&section='.$section);
        $tgid	= clm_core::$load->request_int('filter_tgid', 0);

        // Prüfen ob User Berechtigung zum kopieren hat
        $clmAccess = clm_core::$access;
        if ($clmAccess->access('BE_club_edit_ranking') === false) {
            $mainframe->enqueueMessage(JText::_('RANGLISTE_ADMIN', true), 'warning');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }
        if ($tgid == 0) {
            $mainframe->enqueueMessage(JText::_('keine Zielrangfolgegruppe ausgewählt', true), 'warning');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }
        $table->load((int)$cid[0]);
        echo "<br>table  sid ".$table->sid." gid ".$table->gid; //die();
        $query = "SELECT * FROM  #__clm_rangliste_name "
            ." WHERE sid = ".$table->sid
            ." AND id = ".$table->gid
        ;
        $rg_source = clm_core::$db->loadObjectList($query);
        $query = "SELECT * FROM  #__clm_rangliste_name "
            ." WHERE sid = ".$table->sid
            ." AND id = ".$tgid
        ;
        $rg_target = clm_core::$db->loadObjectList($query);

        if ($rg_source[0]->geschlecht != $rg_target[0]->geschlecht or $rg_source[0]->alter_grenze != $rg_target[0]->alter_grenze or
                $rg_source[0]->alter != $rg_target[0]->alter or $rg_source[0]->status != $rg_target[0]->status or $rg_source[0]->anz_sgp != $rg_target[0]->anz_sgp) {
            $mainframe->enqueueMessage(JText::_('Eigenschaften der Zielranggruppe weichen ab', true), 'warning');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }

        $nn = 0;
        if ($n > 0) {
            foreach ($cid as $id) {
                if ($table->load((int)$id)) {

                    $query = "SELECT * FROM  #__clm_rangliste_id "
                        ." WHERE sid = ".$table->sid
                        ." AND zps = '".$table->zps."'"
                        ." AND gid = ".$tgid
                    ;
                    $rangfolge = clm_core::$db->loadObjectList($query);
                    if (is_null($rangfolge) or count($rangfolge) > 0) {
                        $mainframe->enqueueMessage(JText::_('Zielrangfolge existiert bereits', true), 'warning');
                        continue;
                    }
                    $table->id			= 0;
                    $table->published		= 0;
                    $qugid		= $table->gid;
                    $table->gid		= $tgid;

                    if (!$table->store()) {
                        $mainframe->enqueueMessage($table->getError(), 'warning');
                        $mainframe->redirect('index.php?option='. $option.'&section='.$section);
                    }
                    $nn++;
                    $query = "SELECT * FROM  #__clm_rangliste_spieler "
                        ." WHERE sid = ".$table->sid
                        ." AND ZPS = '".$table->zps."'"
                        ." AND Gruppe = ".$qugid
                    ;
                    $rangfolge = clm_core::$db->loadObjectList($query);
                    foreach ($rangfolge as $rf) {
                        if (is_null($rf->gesperrt)) {
                            $rf->gesperrt = 0;
                        }
                        $query = " REPLACE INTO #__clm_rangliste_spieler "
                            ." (`Gruppe`, `ZPS`, `ZPSmgl`, `Mgl_Nr`, `PKZ`, `Rang`, `man_nr`, `sid`, `gesperrt`) "
                            ." VALUES ('$tgid','$rf->ZPS','$rf->ZPSmgl','$rf->Mgl_Nr','$rf->PKZ','$rf->Rang','$rf->man_nr','$rf->sid','$rf->gesperrt') "
                        ;
                        clm_core::$db->query($query);
                    }

                } else {
                    $mainframe->enqueueMessage($table->getError(), 'warning');
                    $mainframe->redirect('index.php?option='. $option.'&section='.$section);
                }

            }
        } else {
            $mainframe->enqueueMessage(JText::_('RANGLISTE_NO_SELECT', true), 'warning');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }

        if ($nn == 0) {
            $msg = JText::_('Rangfolge nicht kopiert');
        } elseif ($nn > 1) {
            $msg = JText::_('RANGLISTE_MSG_COPY_ENTRYS');
        } else {
            $msg = JText::_('RANGLISTE_MSG_COPY_ENTRY');
        }

        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = JText::_('RANGLISTE_LOG_COPIED');
        $table		= JTable::getInstance('ranglisten', 'TableCLM');
        $table->load($cid[0]);
        $clmLog->params = array('sid' => $table->sid, 'lid' => $lid, 'zps' => $table->zps, 'cids' => implode(',', $cid));
        $clmLog->write();

        $mainframe->enqueueMessage($nn.' '.$msg, 'message');
        $mainframe->redirect('index.php?option='. $option.'&section='.$section);
    }
}
