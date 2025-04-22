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

// Konfigurationsparameter auslesen
$config = clm_core::$db->config();
$fe_submenu_t = $config->fe_submenu_t;
$test_button = $config->test_button;
$mobile = clm_core::$load->is_mobile();

// Prüfen, ob active
if ($fe_submenu_t == 1) {
    $document = JFactory::getDocument();
    if ($config->template) {
        $document->addStyleSheet('components/com_clm/includes/submenu.css', 'text/css');
    }
    $itemid = clm_core::$load->request_int('Itemid');
    // Datenbank einlesen
    include(JPATH_COMPONENT . DS . 'models' . DS . 'submenu_t.php');
    require_once(JPATH_COMPONENT . DS . 'includes' . DS . 'submenu_function.php');
    $document->addScript('components/com_clm/javascript/submenu.js');

    // Turnierparameter bereitstellen
    $params = new clm_class_params($this->turnier->params);
    $params_teamranking = $params->get('teamranking', '0');

    // erzeugen des Array für die Anzeige
    $array = array();
    // Informationen
    $array[0][0] = JText::_('TOURNAMENT_INFO');
    if (clm_core::$load->request_string('view', 0) != "turnier_info") {
        $array[0][1] = 0;
    } else {
        $array[0][1] = 1;
    }
    $array[0][2][] = array("option", "com_clm");
    $array[0][2][] = array("view", "turnier_info");
    $array[0][2][] = array("turnier", $this->turnier->id);
    if ($itemid <> '') {
        $array[0][2][] = array("Itemid", $itemid);
    }
    $array[0][3] = array();

    // Tabelle
    $array[1][0] = JText::_('TOURNAMENT_TABLE');
    if (!$mobile) {
        $i0 = 0;
        if (clm_core::$load->request_string('view', 0) != "turnier_tabelle" || clm_core::$load->request_string('spRang', -1) != - 1) {
            $array[1][1] = 0;
        } else {
            $array[1][1] = 1;
        }
        $array[1][2][] = array("option", "com_clm");
        $array[1][2][] = array("view", "turnier_tabelle");
        $array[1][2][] = array("turnier", $this->turnier->id);
        if ($itemid <> '') {
            $array[1][2][] = array("Itemid", $itemid);
        }
    } else {
        $i0 = 1;
        $array[1][1] = 2;
        $array[1][2] = array();
    }
    $array[1][3] = array();

    if ($mobile) {
        $array[1][3][0][0] = JText::_('TOURNAMENT_TABLE');
        if (clm_core::$load->request_string('view', 0) != "turnier_tabelle" || clm_core::$load->request_string('spRang', -1) != - 1) {
            $array[1][3][0][1] = 0;
        } else {
            $array[1][3][0][1] = 1;
        }
        $array[1][3][0][2][] = array("option", "com_clm");
        $array[1][3][0][2][] = array("view", "turnier_tabelle");
        $array[1][3][0][2][] = array("turnier", $this->turnier->id);
        if ($itemid <> '') {
            $array[1][3][0][2][] = array("Itemid", $itemid);
        }
    }

    // Alle Sonderanglisten
    if (is_null($sub_spRang)) {
        $sub_spRang = array();
    }
    if (count($sub_spRang) > 0) {
        $j = -1;
        for ($i = $i0;$i < (count($sub_spRang) + $i0);$i++) {
            $j++;
            $array[1][3][$i][0] = $sub_spRang[$j]->name . " " . JText::_('TOURNAMENT_TABLE');
            if (clm_core::$load->request_string('view', 0) != "turnier_tabelle" || clm_core::$load->request_string('spRang', -1) != $sub_spRang[$j]->id) {
                $array[1][3][$i][1] = 0;
            } else {
                $array[1][3][$i][1] = 1;
            }
            $array[1][3][$i][2][] = array("option", "com_clm");
            $array[1][3][$i][2][] = array("view", "turnier_tabelle");
            $array[1][3][$i][2][] = array("turnier", $this->turnier->id);
            $array[1][3][$i][2][] = array("spRang", $sub_spRang[$j]->id);
            if ($itemid <> '') {
                $array[1][3][$i][2][] = array("Itemid", $itemid);
            }
        }
    } else {
        $i = $i0;
    }
    if ($params_teamranking > 1) {  // Einzelturnier mit Mannschaftswertung
        $array[1][3][$i][0] = JText::_('TOURNAMENT_TEAM');
        if (clm_core::$load->request_string('view', 0) != "turnier_team_tabelle") {
            $array[1][3][$i][1] = 0;
        } else {
            $array[1][3][$i][1] = 1;
        }
        $array[1][3][$i][2][] = array("option", "com_clm");
        $array[1][3][$i][2][] = array("view", "turnier_team_tabelle");
        $array[1][3][$i][2][] = array("turnier", $this->turnier->id);
        if ($itemid <> '') {
            $array[1][3][$i][2][] = array("Itemid", $itemid);
        }
    }

    // Rangliste
    $array[2][0] = JText::_('TOURNAMENT_RANKING');
    if (!$mobile) {
        $i0 = 0;
        if (clm_core::$load->request_string('view', 0) != "turnier_rangliste" || clm_core::$load->request_string('spRang', -1) != - 1) {
            $array[2][1] = 0;
        } else {
            $array[2][1] = 1;
        }
        $array[2][2][] = array("option", "com_clm");
        $array[2][2][] = array("view", "turnier_rangliste");
        $array[2][2][] = array("turnier", $this->turnier->id);
        if ($itemid <> '') {
            $array[2][2][] = array("Itemid", $itemid);
        }
    } else {
        $i0 = 1;
        $array[2][1] = 2;
        $array[2][2] = array();
    }
    $array[2][3] = array();

    if ($mobile) {
        $array[2][3][0][0] = JText::_('TOURNAMENT_RANKING');
        if (clm_core::$load->request_string('view', 0) != "turnier_rangliste" || clm_core::$load->request_string('spRang', -1) != - 1) {
            $array[2][3][0][1] = 0;
        } else {
            $array[2][3][0][1] = 1;
        }
        $array[2][3][0][2][] = array("option", "com_clm");
        $array[2][3][0][2][] = array("view", "turnier_rangliste");
        $array[2][3][0][2][] = array("turnier", $this->turnier->id);
        if ($itemid <> '') {
            $array[2][3][0][2][] = array("Itemid", $itemid);
        }
    }

    // Alle Sonderanglisten
    if (count($sub_spRang) > 0) {
        $j = -1;
        for ($i = $i0;$i < (count($sub_spRang) + $i0);$i++) {
            $j++;
            $array[2][3][$i][0] = $sub_spRang[$j]->name . " " . JText::_('TOURNAMENT_RANKING');
            if (clm_core::$load->request_string('view', 0) != "turnier_rangliste" || clm_core::$load->request_string('spRang', -1) != $sub_spRang[$j]->id) {
                $array[2][3][$i][1] = 0;
            } else {
                $array[2][3][$i][1] = 1;
            }
            $array[2][3][$i][2][] = array("option", "com_clm");
            $array[2][3][$i][2][] = array("view", "turnier_rangliste");
            $array[2][3][$i][2][] = array("turnier", $this->turnier->id);
            $array[2][3][$i][2][] = array("spRang", $sub_spRang[$j]->id);
            if ($itemid <> '') {
                $array[2][3][$i][2][] = array("Itemid", $itemid);
            }
        }
    }

    // Teilnehmerliste
    $array[3][0] = JText::_('TOURNAMENT_PARTICIPANTLIST');
    if (!$mobile) {
        $i0 = 0;
        if (clm_core::$load->request_string('view', 0) != "turnier_teilnehmer") {
            $array[3][1] = 0;
        } else {
            $array[3][1] = 1;
        }
        $array[3][2][] = array("option", "com_clm");
        $array[3][2][] = array("view", "turnier_teilnehmer");
        $array[3][2][] = array("turnier", $this->turnier->id);
        if ($itemid <> '') {
            $array[3][2][] = array("Itemid", $itemid);
        }
    } else {
        $i0 = 1;
        $array[3][1] = 2;
        $array[3][2] = array();
    }
    $array[3][3] = array();

    if ($mobile) {
        $array[3][3][0][0] = JText::_('TOURNAMENT_PARTICIPANTLIST');
        if (clm_core::$load->request_string('view', 0) != "turnier_teilnehmer") {
            $array[3][3][0][1] = 0;
        } else {
            $array[3][3][0][1] = 1;
        }
        $array[3][3][0][2][] = array("option", "com_clm");
        $array[3][3][0][2][] = array("view", "turnier_teilnehmer");
        $array[3][3][0][2][] = array("turnier", $this->turnier->id);
        if ($itemid <> '') {
            $array[3][3][0][2][] = array("Itemid", $itemid);
        }
    }

    $array[3][3][$i0][0] = JText::_('TOURNAMENT_DWZ');
    if (clm_core::$load->request_string('view', 0) != "turnier_dwz") {
        $array[3][3][$i0][1] = 0;
    } else {
        $array[3][3][$i0][1] = 1;
    }
    $array[3][3][$i0][2][] = array("option", "com_clm");
    $array[3][3][$i0][2][] = array("view", "turnier_dwz");
    $array[3][3][$i0][2][] = array("turnier", $this->turnier->id);
    if ($itemid <> '') {
        $array[3][3][$i0][2][] = array("Itemid", $itemid);
    }

    // Paarungsliste
    $array[4][0] = JText::_('SUBMENU_PAAR');
    if (!$mobile) {
        $i0 = 0;
        if (clm_core::$load->request_string('view', 0) != "turnier_paarungsliste" || clm_core::$load->request_string('spRang', -1) != - 1) {
            $array[4][1] = 0;
        } else {
            $array[4][1] = 1;
        }
        $array[4][2][] = array("option", "com_clm");
        $array[4][2][] = array("view", "turnier_paarungsliste");
        $array[4][2][] = array("turnier", $this->turnier->id);
        if ($itemid <> '') {
            $array[4][2][] = array("Itemid", $itemid);
        }
    } else {
        $i0 = 1;
        $array[4][1] = 2;
        $array[4][2] = array();
    }
    $array[4][3] = array();

    if ($mobile) {
        $array[4][3][0][0] = JText::_('SUBMENU_PAAR');
        if (clm_core::$load->request_string('view', -1) != "turnier_paarungsliste" || clm_core::$load->request_string('spRang', -1) != - 1) {
            $array[4][3][0][1] = 0;
        } else {
            $array[4][3][0][1] = 1;
        }
        $array[4][3][0][2][] = array("option", "com_clm");
        $array[4][3][0][2][] = array("view", "turnier_paarungsliste");
        $array[4][3][0][2][] = array("turnier", $this->turnier->id);
        if ($itemid <> '') {
            $array[4][3][0][2][] = array("Itemid", $itemid);
        }
    }

    // Alle Runden
    if (count($sub_rounds) > 0) {
        $j = -1;
        for ($i = $i0;$i < (count($sub_rounds) + $i0);$i++) {
            $j++;
            $array[4][3][$i][0] = $sub_rounds[$j]->name;
            if (clm_core::$load->request_string('view', 0) != "turnier_runde" ||
                clm_core::$load->request_string('runde', -1) != $sub_rounds[$j]->nr || clm_core::$load->request_string('dg', -1) != $sub_rounds[$j]->dg) {
                $array[4][3][$i][1] = 0;
            } else {
                $array[4][3][$i][1] = 1;
            }
            $array[4][3][$i][2][] = array("option", "com_clm");
            $array[4][3][$i][2][] = array("view", "turnier_runde");
            $array[4][3][$i][2][] = array("turnier", $this->turnier->id);
            $array[4][3][$i][2][] = array("runde", $sub_rounds[$j]->nr);
            $array[4][3][$i][2][] = array("dg", $sub_rounds[$j]->dg);
            if ($itemid <> '') {
                $array[4][3][$i][2][] = array("Itemid", $itemid);
            }
        }
    } else {
        $array[4][3] = array();
    }
    echo clm_submenu($array);
}
