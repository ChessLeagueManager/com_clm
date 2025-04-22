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

class CLMControllerMTurniere extends JControllerLegacy
{
    /**
     * Constructor
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    public function display($cachable = false, $urlparams = array())
    {
        $mainframe	= JFactory::getApplication();

        $db 		= JFactory::getDBO();
        $user 		= JFactory::getUser();
        $cid 		= clm_core::$load->request_array_int('cid');
        if (is_null($cid)) {
            $cid[0] = clm_core::$load->request_int('id');
        }
        $option 	= clm_core::$load->request_string('option');
        $section 	= clm_core::$load->request_string('section');
        $row 		= JTable::getInstance('ligen', 'TableCLM');

        // load the row from the db table
        $row->load($cid[0]);

        $clmAccess = clm_core::$access;

        if ($clmAccess->access('BE_teamtournament_edit_detail') === false) {
            $msg = JText::_('Kein Zugriff: ').JText::_('MTURN_STAFFEL_TOTAL') ;
            $mainframe->enqueueMessage($msg, 'warning');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }
        if ($cid[0] == 0) {
            if ($clmAccess->access('BE_teamtournament_create') === false) {
                $msg = JText::_('LIGEN_ADMIN');
                $mainframe->enqueueMessage($msg, 'warning');
                $mainframe->redirect('index.php?option='. $option.'&section='.$section);
            }
            // Neue ID
            $row->published	= 0;
        } else {
            // Prüfen ob User Berechtigung zum editieren hat
            $saison		= JTable::getInstance('saisons', 'TableCLM');
            $saison->load($row->sid);
            // illegaler Einbruchversuch über URL !
            // evtl. mitschneiden !?!
            if ($saison->archiv == "1" and $clmAccess->access('BE_teamtournament_edit_detail') === false) {
                $msg = JText::_('MTURN_ARCHIV');
                $mainframe->enqueueMessage($msg, 'warning');
                $mainframe->redirect('index.php?option='. $option.'&section='.$section);
            }
            // Keine SL oder Admin
            if ($clmAccess->access('BE_teamtournament_edit_detail') === false) {
                $msg = JText::_('MTURN_STAFFEL_TOTAL');
                $mainframe->enqueueMessage($msg, 'warning');
                $mainframe->redirect('index.php?option='. $option.'&section='.$section);
            }
            if ($row->sl !== clm_core::$access->getJid() and $clmAccess->access('BE_teamtournament_edit_detail') !== true) {
                $msg = JText::_('MTURN_STAFFEL');
                $mainframe->enqueueMessage($msg, 'warning');
                $mainframe->redirect('index.php?option='. $option.'&section='.$section);
            }
            // do stuff for existing records
            $row->checkout($user->get('id'));
        }

        //CLM parameter auslesen
        $clm_config = clm_core::$db->config();
        if ($clm_config->field_search == 1) {
            $field_search = "js-example-basic-single";
        } else {
            $field_search = "inputbox";
        }

        // Listen
        // Heimrecht vertauscht
        $lists['heim']	= JHtml::_('select.booleanlist', 'heim', 'class="inputbox"', $row->heim);
        // Published
        $lists['published']	= JHtml::_('select.booleanlist', 'published', 'class="inputbox"', $row->published);
        // Anzeige Mannschaftsaufstellung
        $lists['anzeige_ma']	= JHTML::_('select.booleanlist', 'anzeige_ma', 'class="inputbox"', $row->anzeige_ma);
        // automat. Mail
        $lists['mail']	= JHtml::_('select.booleanlist', 'mail', 'class="inputbox"', $row->mail);
        // Staffelleitermail als BCC
        $lists['sl_mail']	= JHtml::_('select.booleanlist', 'sl_mail', 'class="inputbox"', $row->sl_mail);
        // Ordering für Rangliste
        $lists['order']	= JHtml::_('select.booleanlist', 'order', 'class="inputbox"', $row->order);

        //für alle ligen oder nur ausgewählte
        $userlist = $clmAccess->userlist('BE_teamtournament_edit_result', '>0');
        if ($userlist === false) {
            echo "<br>cl: ";
            var_dump($userlist);
            die('clcl');
        }
        $sllist[]	= JHtml::_('select.option', '0', JText::_('MTURN_TL'), 'jid', 'name');
        $sllist		= array_merge($sllist, $userlist);
        //	$lists['sl']	= JHtml::_('select.genericlist',   $sllist, 'sl', 'class="js-example-basic-single" size="1"', 'jid', 'name', $row->sl );
        $lists['sl']	= JHtml::_('select.genericlist', $sllist, 'sl', 'class="'.$field_search.'" size="1"', 'jid', 'name', $row->sl);
        // Saisonliste
        $sql = "SELECT id as sid, name FROM #__clm_saison WHERE archiv = 0";
        $db->setQuery($sql);
        if (!clm_core::$db->query($sql)) {
            $mainframe->enqueueMessage($row->getErrorMsg(), 'warning');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }
        $saisonlist[]	= JHtml::_('select.option', '0', JText::_('LIGEN_SAISON'), 'sid', 'name');
        $saisonlist	= array_merge($saisonlist, $db->loadObjectList());
        //	$lists['saison']= JHtml::_('select.genericlist',   $saisonlist, 'sid', 'class="js-example-basic-single" size="1"','sid', 'name', $row->sid );
        $lists['saison'] = JHtml::_('select.genericlist', $saisonlist, 'sid', 'class="'.$field_search.'" size="1"', 'sid', 'name', $row->sid);
        // Rangliste
        $query = " SELECT id, Gruppe FROM #__clm_rangliste_name ";
        if ($row->sid > 0) {
            $query .= " WHERE sid = ".$row->sid;
        } else {
            $query .= " WHERE sid = ".clm_core::$access->getSeason();
        }
        $db->setQuery($query);
        if (!clm_core::$db->query($query)) {
            $mainframe->enqueueMessage($row->getErrorMsg(), 'warning');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }
        $glist[]	= JHtml::_('select.option', '0', JText::_('LIGEN_ML'), 'id', 'Gruppe');
        $glist		= array_merge($glist, $db->loadObjectList());
        //	$lists['gruppe']= JHtml::_('select.genericlist',   $glist, 'rang', 'class="js-example-basic-single" size="1"', 'id', 'Gruppe', $row->rang );
        $lists['gruppe'] = JHtml::_('select.genericlist', $glist, 'rang', 'class="'.$field_search.'" size="1"', 'id', 'Gruppe', $row->rang);

        // ggf. Info, wenn Stichtag der Aufstellung nicht mit Meldeschluss der Rangliste übereinstimmt.
        if ($row->rang > 0) {
            $query = " SELECT * FROM #__clm_rangliste_name "
                ." WHERE id = '" .$row->rang. "'" . " AND sid = " .$row->sid;
            $gruppe = clm_core::$db->loadObjectList($query);
            $params = new clm_class_params($row->params);
            $deadline_roster = $params->get("deadline_roster", '1970-01-01');
            if ($deadline_roster != $gruppe[0]->Meldeschluss) {
                $mainframe->enqueueMessage(JText::_('LEAGUE_RANG_MELDESCHLUSS').' ('.$gruppe[0]->Meldeschluss.') ');
            }
        }

        require_once(JPATH_COMPONENT.DS.'views'.DS.'mturniere.php');
        CLMViewMTurniere::mturnier($row, $lists, $option, ($cid[0] == 0 ? true : false));
    }

    public function apply()
    {
        $this->saveIt(true);
    }

    public function save()
    {
        $this->saveIt(false);
    }

    public function saveIt($apply = false)
    {
        $mainframe	= JFactory::getApplication();

        // Check for request forgeries
        defined('clm') or die('Restricted access');

        $option		= clm_core::$load->request_string('option');
        $section	= clm_core::$load->request_string('section');
        $db 		= JFactory::getDBO();
        $row 		= JTable::getInstance('ligen', 'TableCLM');
        $msg		= clm_core::$load->request_string('id');
        $sid_alt	= clm_core::$load->request_string('sid_alt');
        $sid		= clm_core::$load->request_string('sid');

        $post = $_POST;
        if (!$row->bind($post)) {
            $mainframe->enqueueMessage($row->getError(), 'error');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }
        //Liga-Parameter zusammenfassen
        $row->params['anz_sgp'] = clm_core::$load->request_string('anz_sgp');
        if ($row->params['noBoardResults'] == '1') {
            $row->params['noOrgReference'] = '1';
        }
        if ($row->params['noOrgReference'] == '1') {
            $row->params['incl_to_season'] = '0';
        }

        $paramsStringArray = array();
        foreach ($row->params as $key => $value) {
            ////$paramsStringArray[] = $key.'='.intval($value);
            //if (substr($key,0,2) == "\'") $key = substr($key,2,strlen($key)-4);
            //if (substr($key,0,1) == "'") $key = substr($key,1,strlen($key)-2);
            $paramsStringArray[] = $key.'='.$value;
        }
        $row->params = implode("\n", $paramsStringArray);

        // pre-save checks
        if (!$row->check()) {
            $mainframe->enqueueMessage($row->getError(), 'error');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }
        $teil	= $row->teil;

        // if new item, order last in appropriate group
        $aktion = JText::_('LIGEN_AKTION_LEAGUE_EDIT');
        $neu_id = 0;
        $ungerade_id = 0;
        $ko_id = 0;
        if (!$row->id) {
            $neu_id = 1;
            $aktion = JText::_('LIGEN_AKTION_NEW_LEAGUE');
            $where = "sid = " . (int) $row->sid;
            $row->ordering = $row->getNextOrder($where);

            // Bei ungerader Anzahl Mannschaften Teilnehmerzahl um 1 erhöhen
            if (($row->runden_modus != 4) and ($row->runden_modus != 5)) { // vollrundig, Schweizer System
                if (($row->teil) % 2 != 0) {
                    $ungerade_id	= 1;
                    $row->teil	= $row->teil + 1;
                    $tln		= $row->teil;
                    $mainframe->enqueueMessage(JText::_('LIGEN_MANNSCH', true), 'notice');
                }
            }
            if ($row->runden_modus == 4) {
                $ko_id = 0;
                $tln_ko	= $row->teil;
                while ($row->teil < pow(2, $row->runden)) {
                    $ko_id++;
                    $row->teil = $row->teil + 1;
                }
                if ($ko_id > 0) {
                    $mainframe->enqueueMessage(JText::_('MTURN_MANNSCH_KO'), 'notice');
                }
            }
            if ($row->runden_modus == 5) {
                $ko_id = 0;
                $tln_ko	= $row->teil;
                while ($row->teil < pow(2, $row->runden - 1)) {
                    $ko_id++;
                    $row->teil = $row->teil + 1;
                }
                if ($ko_id > 0) {
                    $mainframe->enqueueMessage(JText::_('MTURN_MANNSCH_KO'), 'notice');
                }
            }
        }
        $row->liga_mt	= 1; //mtmt 0 = liga  1 = mannschaftsturnier
        // save the changes
        if (!$row->store()) {
            $mainframe->enqueueMessage($row->getError(), 'error');
            $mainframe->redirect('index.php?option='. $option.'&section='.$section);
        }
        $liga_man	= $row->id;
        $liga_rnd	= $row->runden;
        $liga_dg	= $row->durchgang;
        $publish	= $row->published;

        // Wenn sid gewechselt wurde, alle Daten in neue Saison verschieben
        //	if ($sid_alt != $sid AND $sid_alt != "") {
        if ($sid_alt != $sid and $sid_alt != "0") {
            $mainframe->enqueueMessage(JText::_('LIGEN_SAISON_AEND'), 'notice');
            $query = " UPDATE #__clm_mannschaften "
                ." SET sid = ".$sid
                ." WHERE liga = ".$liga_man
                ." AND sid = ".$sid_alt
            ;
            $db->setQuery($query);
            clm_core::$db->query($query);

            $query = " UPDATE #__clm_meldeliste_spieler "
                ." SET sid = ".$sid
                ." WHERE lid = ".$liga_man
                ." AND sid = ".$sid_alt
            ;
            $db->setQuery($query);
            clm_core::$db->query($query);

            $query = " UPDATE #__clm_rnd_man "
                ." SET sid = ".$sid
                ." WHERE lid = ".$liga_man
                ." AND sid = ".$sid_alt
            ;
            $db->setQuery($query);
            clm_core::$db->query($query);

            $query = " UPDATE #__clm_rnd_spl "
                ." SET sid = ".$sid
                ." WHERE lid = ".$liga_man
                ." AND sid = ".$sid_alt
            ;
            $db->setQuery($query);
            clm_core::$db->query($query);

            $query = " UPDATE #__clm_runden_termine "
                ." SET sid = ".$sid
                ." WHERE liga = ".$liga_man
                ." AND sid = ".$sid_alt
            ;
            $db->setQuery($query);
            clm_core::$db->query($query);
        }

        // Bei ungerader Anzahl Mannschaften "spielfrei" hinzufügen
        if (($row->runden_modus != 4) and ($row->runden_modus != 5)) { // vollrundig, Schweizer System
            if ($ungerade_id == "1") {

                $query = " INSERT INTO #__clm_mannschaften "
                    ." ( `sid`,`name`,`liga`,`zps`,`liste`,`edit_liste`,`man_nr`,`tln_nr`,`mf`) "
                    ." VALUES ('$sid','spielfrei','$liga_man','0','0','62','0','$tln','0') "
                ;
                $db->setQuery($query);
                clm_core::$db->query($query);
                $mainframe->enqueueMessage(JText::_('LIGEN_MANNSCH_1'), 'notice');
            }
        }
        // Bei KO-System  x Mannschaften "spielfrei" hinzufügen, wenn nötig
        if (($row->runden_modus == 4) or ($row->runden_modus == 5)) { // KO System
            for ($x = 1; $x < 1 + $ko_id; $x++) {
                $tln_ko++;
                $query = " INSERT INTO #__clm_mannschaften "
                    ." ( `sid`,`name`,`liga`,`zps`,`liste`,`edit_liste`,`man_nr`,`tln_nr`,`mf`) "
                    ." VALUES ('$sid','spielfrei','$liga_man','0','0','62','0','$tln_ko','0') "
                ;
                $db->setQuery($query);
                clm_core::$db->query($query);
            }
            if ($ko_id > 0) {
                $mainframe->enqueueMessage($ko_id.JText::_('MTURN_MANNSCH_KO_1'), 'notice');
            }
        }

        // Mannschaftsrunden anlegen
        if ($neu_id == "1") {
            clm_core::$api->db_tournament_genRounds($liga_man, true);
            // Mannschaften anlegen
            for ($x = 1; $x < 1 + $teil; $x++) {
                $man_name = JText::_('LIGEN_STD_TEAM')." ".$x;
                if ($x < 10) {
                    $man_nr = $liga_man.'0'.$x;
                } else {
                    $man_nr = $liga_man.$x;
                }
                $query = " INSERT INTO #__clm_mannschaften "
                    ." (`sid`,`name`,`liga`,`zps`,`liste`,`edit_liste`,`man_nr`,`tln_nr`,`mf` "
                    ." ,`sg_zps`,`datum`,`edit_datum`,`lokal`,`bemerkungen`,`bem_int`,`published`,`checked_out`,`checked_out_time`) "
                    ." VALUES ('$sid','$man_name','$liga_man','1','0','0','$man_nr','$x','0' "
                    ." ,'','1970-01-01 00:00:00','1970-01-01 00:00:00','','','','$publish',NULL,NULL) "
                ;
                $db->setQuery($query);
                clm_core::$db->query($query);
            }
        }

        clm_core::$api->db_tournament_ranking($liga_man, true);

        //require_once(JPATH_COMPONENT.DS.'controllers'.DS.'ergebnisse.php');
        //CLMControllerErgebnisse::calculateRanking($sid,$liga_man);

        if ($apply) {
            $msg = JText::_('LIGEN_AENDERN');
            $link = 'index.php?option='.$option.'&section='.$section.'&id='. $row->id ;
        } else {
            $msg = JText::_('LIGEN_LIGA');
            $link = 'index.php?option='.$option.'&view=view_tournament_group&liga=0';
        }

        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = $aktion;
        $clmLog->params = array('sid' => $row->sid, 'lid' => $row->id);
        $clmLog->write();


        $mainframe->enqueueMessage($msg);
        $mainframe->redirect($link);
    }


    public function cancel()
    {
        $mainframe	= JFactory::getApplication();
        // Check for request forgeries
        defined('clm') or die('Restricted access');

        $option		= clm_core::$load->request_string('option');
        $section	= clm_core::$load->request_string('section');
        $id			= clm_core::$load->request_string('id');
        $row 		= JTable::getInstance('ligen', 'TableCLM');

        $msg = JText::_('LIGEN_AKTION');
        $mainframe->enqueueMessage($msg);
        $mainframe->redirect('index.php?option='.$option.'&view=view_tournament_group&liga=0');
    }
}
