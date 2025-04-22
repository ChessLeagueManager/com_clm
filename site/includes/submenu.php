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
defined('_JEXEC') or die('Restricted access');
$document = JFactory::getDocument();
// Konfigurationsparameter auslesen
$config = clm_core::$db->config();
$fe_submenu = $config->fe_submenu;
$test_button = $config->test_button;
$mobile = clm_core::$load->is_mobile();

if ($fe_submenu == 1) {
    $document = JFactory::getDocument();
    if (!isset($submenu_where[0])) {
        $submenu_where[0] = - 1;
    }
    if ($config->template) {
        $document->addStyleSheet('components/com_clm/includes/submenu.css', 'text/css');
    }
    $itemid = clm_core::$load->request_int('Itemid');
    // Datenbank einlesen
    include(JPATH_COMPONENT . DS . 'models' . DS . 'submenu.php');
    require_once(JPATH_COMPONENT . DS . 'includes' . DS . 'submenu_function.php');
    $document->addScript('components/com_clm/javascript/submenu.js');
    // erzeugen des Array für die Anzeige
    $array[0][3] = array();
    // Saison
    for ($i = 0;$i < count($saisonlist);$i++) {
        $array[0][3][$i][0] = $saisonlist[$i]->name;
        $array[0][3][$i][2][] = array("option", "com_clm");
        $array[0][3][$i][2][] = array("view", "info");
        $array[0][3][$i][2][] = array("saison", $saisonlist[$i]->id);
        if ($itemid <> '') {
            $array[0][3][$i][2][] = array("Itemid", $itemid);
        }
        if ($saisonlist[$i]->id == $sid) {
            $saison_activ = $i;
            $array[0][3][$i][1] = 1;
        } else {
            $array[0][3][$i][1] = 0;
        }
    }
    if (isset($saison_activ)) {
        $array[0][0] = $array[0][3][$saison_activ][0];
        $array[0][1] = $array[0][3][$saison_activ][1];
        $array[0][2] = $array[0][3][$saison_activ][2];
    } else {
        $array[0][0] = JText::_('SUBMENU_SAISON');
        $array[0][1] = 2;
        $array[0][2] = array();
    }
    // Liga
    $array[1][0] = JText::_('SUBMENU_LEAGUE');
    $array[1][1] = 2;
    $array[1][2] = array();
    $array[1][3] = array();
    for ($i = 0;$i < count($sub_liga);$i++) {
        $params_m = new clm_class_params($sub_liga[$i]->params);
        $array[1][3][$i][0] = $sub_liga[$i]->name;
        if (clm_core::$load->request_int('liga', -1) != $sub_liga[$i]->id || clm_core::$load->request_string('view', -1) == "info" || clm_core::$load->request_string('view', -1) == "termine") {
            $array[1][3][$i][1] = 0;
        } else {
            $liga_on = true;
            $array[1][3][$i][1] = 1;
        }
        $array[1][3][$i][2][] = array("option", "com_clm");
        // Auswahlkriterium des Standard View
        if ($sub_liga[$i]->runden_modus == 4 or $sub_liga[$i]->runden_modus == 5) {
            $viewA = "paarungsliste";
        } else {
            if ($params_m->get("firstView", "0") == "0") {
                $viewA = "rangliste";
            } elseif ($params_m->get("firstView", "0") == "1") {
                $viewA = "tabelle";
            } elseif ($params_m->get("firstView", "0") == "2") {
                $viewA = "paarungsliste";
            } else {
                $viewA = "teilnehmer";
            }
        }
        $array[1][3][$i][2][] = array("view", $viewA);
        $array[1][3][$i][2][] = array("saison", $sid);
        $array[1][3][$i][2][] = array("liga", $sub_liga[$i]->id);
        if ($itemid <> '') {
            $array[1][3][$i][2][] = array("Itemid", $itemid);
        }
    }
    // Mannschaften
    $array[2][0] = JText::_('SUBMENU_MSCH');
    if (isset($liga_on)) {
        $array[2][1] = 2;
        $array[2][2] = array();
        $array[2][3] = array();
        for ($i = 0;$i < count($sub_msch);$i++) {
            if (clm_core::$load->request_string('view', -1) != "mannschaft" || clm_core::$load->request_string('tlnr', -1) != $sub_msch[$i]->tln_nr) {
                $array[2][3][$i][1] = 0;
            } else {
                $array[2][3][$i][1] = 1;
            }
            $array[2][3][$i][2][] = array("option", "com_clm");
            $array[2][3][$i][2][] = array("view", "mannschaft");
            $array[2][3][$i][2][] = array("saison", $sid);
            $array[2][3][$i][2][] = array("liga", $lid);
            $array[2][3][$i][2][] = array("tlnr", $sub_msch[$i]->tln_nr);
            if ($itemid <> '') {
                $array[2][3][$i][2][] = array("Itemid", $itemid);
            }
            $array[2][3][$i][0] = $sub_msch[$i]->name;
        }
    } else {
        $array[2][1] = 3;
        $array[2][2] = array();
        $array[2][3] = array();
    }
    // Paarungsliste
    $array[3][0] = JText::_('SUBMENU_PAAR');
    $i33 = 0;
    if (isset($liga_on)) {
        if (!$mobile) {
            if (clm_core::$load->request_string('view', -1) != "paarungsliste") {
                $array[3][1] = 0;
            } else {
                $array[3][1] = 1;
            }
            $array[3][2][] = array("option", "com_clm");
            $array[3][2][] = array("view", "paarungsliste");
            $array[3][2][] = array("saison", $sid);
            $array[3][2][] = array("liga", $lid);
            if ($itemid <> '') {
                $array[3][2][] = array("Itemid", $itemid);
            }
        } else {
            $array[3][1] = 2;
            $array[3][2] = array();
        }
        $array[3][3] = array();

        if ($mobile) {
            $array[3][3][0][0] = JText::_('SUBMENU_PAAR');
            if (clm_core::$load->request_string('view', -1) != "paarungsliste") {
                $array[3][3][0][1] = 0;
            } else {
                $array[3][3][0][1] = 1;
            }
            $array[3][3][0][2][] = array("option", "com_clm");
            $array[3][3][0][2][] = array("view", "paarungsliste");
            $array[3][3][0][2][] = array("saison", $sid);
            $array[3][3][0][2][] = array("liga", $lid);
            if ($itemid <> '') {
                $array[3][3][0][2][] = array("Itemid", $itemid);
            }
            $i33++;
        }

        // Aktuelle Runde
        if (count($sub_runden) > 0) {
            $array[3][3][$i33][0] = JText::_('ROUND_CURRENT');
            require_once(JPATH_COMPONENT . DS . 'models' . DS . 'aktuell_runde.php');
            $rnd_dg = CLMModelAktuell_Runde::Runden();
            if (clm_core::$load->request_string('view', -1) != "runde" || clm_core::$load->request_string('runde', -1) != $rnd_dg[0] || clm_core::$load->request_string('dg', -1) != $rnd_dg[1]) {
                $array[3][3][$i33][1] = 0;
            } else {
                $array[3][3][$i33][1] = 1;
            }
            $array[3][3][$i33][2][] = array("option", "com_clm");
            $array[3][3][$i33][2][] = array("view", "aktuell_runde");
            $array[3][3][$i33][2][] = array("saison", $sid);
            $array[3][3][$i33][2][] = array("liga", $lid);
            if ($itemid <> '') {
                $array[3][3][$i33][2][] = array("Itemid", $itemid);
            }
            // Alle Runden
            $i33++;
            for ($i = 0;$i < count($sub_runden);$i++) {
                /*			if ($sub_runden[$i]->nr > $sub_runden[$i]->runden) { //klkl
                                $sub_liga_durchgang = "2";
                                $sub_runden_nr = $sub_runden[$i]->nr - $sub_runden[$i]->runden;
                            } else {
                                $sub_liga_durchgang = "1"; //klkl
                                $sub_runden_nr = $sub_runden[$i]->nr;
                            }
                */
                if ($sub_runden[$i]->nr < ($sub_runden[$i]->runden + 1)) {
                    $sub_liga_durchgang = "1";
                    $sub_runden_nr = $sub_runden[$i]->nr;
                } elseif ($sub_runden[$i]->nr < ((2 * $sub_runden[$i]->runden) + 1)) {
                    $sub_liga_durchgang = "2";
                    $sub_runden_nr = $sub_runden[$i]->nr - $sub_runden[$i]->runden;
                } elseif ($sub_runden[$i]->nr < ((3 * $sub_runden[$i]->runden) + 1)) {
                    $sub_liga_durchgang = "3";
                    $sub_runden_nr = $sub_runden[$i]->nr - (2 * $sub_runden[$i]->runden);
                } else {
                    $sub_liga_durchgang = "4";
                    $sub_runden_nr = $sub_runden[$i]->nr - (3 * $sub_runden[$i]->runden);
                }
                $array[3][3][$i + $i33][0] = $sub_runden[$i]->name;
                if (clm_core::$load->request_string('view', -1) != "runde" || clm_core::$load->request_string('runde', -1) != $sub_runden_nr || clm_core::$load->request_string('dg', -1) != $sub_liga_durchgang) {
                    $array[3][3][$i + $i33][1] = 0;
                } else {
                    $array[3][3][$i + $i33][1] = 1;
                }
                $array[3][3][$i + $i33][2][] = array("option", "com_clm");
                $array[3][3][$i + $i33][2][] = array("view", "runde");
                $array[3][3][$i + $i33][2][] = array("saison", $sid);
                $array[3][3][$i + $i33][2][] = array("liga", $lid);
                $array[3][3][$i + $i33][2][] = array("runde", $sub_runden_nr);
                $array[3][3][$i + $i33][2][] = array("dg", $sub_liga_durchgang);
                if ($itemid <> '') {
                    $array[3][3][$i + $i33][2][] = array("Itemid", $itemid);
                }
            }
        } else {
            $array[3][3] = array();
        }
    } else {
        $array[3][1] = 3;
        $array[3][2] = array();
        $array[3][3] = array();
    }
    // Mehr
    $array[4][0] = JText::_('SUBMENU_ETC');
    $array[4][1] = 2;
    $array[4][2] = array();
    // Liga Teilnehmer
    $array[4][3][0][0] = JText::_('SUBMENU_TEILNEHMER');
    if (isset($liga_on)) {
        if (clm_core::$load->request_string('view', -1) != "teilnehmer") {
            $array[4][3][0][1] = 0;
        } else {
            $array[4][3][0][1] = 1;
        }
        $array[4][3][0][2][] = array("option", "com_clm");
        $array[4][3][0][2][] = array("view", "teilnehmer");
        $array[4][3][0][2][] = array("saison", $sid);
        $array[4][3][0][2][] = array("liga", $lid);
        if ($itemid <> '') {
            $array[4][3][0][2][] = array("Itemid", $itemid);
        }
    } else {
        $array[4][3][0][2] = array();
        $array[4][3][0][1] = 3;
    }
    // DWZ der Liga
    $array[4][3][1][0] = JText::_('SUBMENU_DWZMSCH');
    if (isset($liga_on)) {
        if (clm_core::$load->request_string('view', -1) != "dwz_liga") {
            $array[4][3][1][1] = 0;
        } else {
            $array[4][3][1][1] = 1;
        }
        $array[4][3][1][2][] = array("option", "com_clm");
        $array[4][3][1][2][] = array("view", "dwz_liga");
        $array[4][3][1][2][] = array("saison", $sid);
        $array[4][3][1][2][] = array("liga", $lid);
        if ($itemid <> '') {
            $array[4][3][1][2][] = array("Itemid", $itemid);
        }
    } else {
        $array[4][3][1][1] = 3;
        $array[4][3][1][2] = array();
    }
    // Liga Statistiken
    $array[4][3][2][0] = JText::_('SUBMENU_STATS');
    if (isset($liga_on)) {
        if (clm_core::$load->request_string('view', -1) != "statistik") {
            $array[4][3][2][1] = 0;
        } else {
            $array[4][3][2][1] = 1;
        }
        $array[4][3][2][2][] = array("option", "com_clm");
        $array[4][3][2][2][] = array("view", "statistik");
        $array[4][3][2][2][] = array("saison", $sid);
        $array[4][3][2][2][] = array("liga", $lid);
        if ($itemid <> '') {
            $array[4][3][2][2][] = array("Itemid", $itemid);
        }
    } else {
        $array[4][3][2][2] = array();
        $array[4][3][2][1] = 3;
    }
    // Liga Org-Details
    $array[4][3][3][0] = JText::_('SUBMENU_LIGA_INFO');
    if (isset($liga_on)) {
        if (clm_core::$load->request_string('view', -1) != "liga_info") {
            $array[4][3][3][1] = 0;
        } else {
            $array[4][3][3][1] = 1;
        }
        $array[4][3][3][2][] = array("option", "com_clm");
        $array[4][3][3][2][] = array("view", "liga_info");
        $array[4][3][3][2][] = array("saison", $sid);
        $array[4][3][3][2][] = array("liga", $lid);
        if ($itemid <> '') {
            $array[4][3][3][2][] = array("Itemid", $itemid);
        }
    } else {
        $array[4][3][3][2] = array();
        $array[4][3][3][1] = 3;
    }

    // Linie/Abtrennung
    $array[4][3][4][0] = "";
    $array[4][3][4][1] = 4;
    $array[4][3][4][2] = array();
    $array[4][3][4][3] = array();

    // Saisonstatistiken
    $array[4][3][5][0] = JText::_('SUBMENU_SAISTATS');
    if (clm_core::$load->request_string('view', -1) != "info") {
        $array[4][3][5][1] = 0;
    } else {
        $array[4][3][5][1] = 1;
    }
    $array[4][3][5][2][] = array("option", "com_clm");
    $array[4][3][5][2][] = array("view", "info");
    $array[4][3][5][2][] = array("saison", $sid);
    if (isset($liga_on)) {
        $array[4][3][5][2][] = array("liga", $lid);
    }
    if ($itemid <> '') {
        $array[4][3][5][2][] = array("Itemid", $itemid);
    }
    $array[4][3][5][3] = array();
    // Termine
    $array[4][3][6][0] = JText::_('SUBMENU_TERMINE');
    if (clm_core::$load->request_string('view', -1) != "termine") {
        $array[4][3][6][1] = 0;
    } else {
        $array[4][3][6][1] = 1;
    }
    $array[4][3][6][2][] = array("option", "com_clm");
    $array[4][3][6][2][] = array("view", "termine");
    $array[4][3][6][2][] = array("saison", $sid);
    if (isset($liga_on)) {
        $array[4][3][6][2][] = array("liga", $lid);
    }
    if ($itemid <> '') {
        $array[4][3][6][2][] = array("Itemid", $itemid);
    }

    /*	if ($test_button) {
        if (!isset($_SERVER["HTTP_USER_AGENT"]))
            echo "<b>Kein HTTP_USER_AGENT defined:</b> "."Zugriff über Extern Modul"."<br>";
        else
            echo "<b>Ihr HTTP_USER_AGENT lautet:</b> ".$_SERVER['HTTP_USER_AGENT']."<br>";
        echo "<br>mobile:"; var_dump($mobile);
        }
    */
    echo clm_submenu($array);
}
