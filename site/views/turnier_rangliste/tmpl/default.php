<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT . DS . 'includes' . DS . 'clm_tooltip.php');

// Konfigurationsparameter auslesen
$itemid 	= clm_core::$load->request_int('Itemid');
$spRang		= clm_core::$load->request_int('spRang');	//Sonderranglisten
// Userkennung holen
$user	= JFactory::getUser();
$jid	= $user->get('id');

$pgn		= clm_core::$load->request_int('pgn');
$option 	= clm_core::$load->request_string('option', 'com_clm');
$mainframe	= JFactory::getApplication();
if ($pgn == 1 and $spRang == 0) {
    $result = clm_core::$api->db_pgn_export($this->turnier->id, false);
    $_GET['pgn'] = 0;
    if (!$result[0]) {
        $msg = JText::_(strtoupper($result[1])).'<br><br>';
    } else {
        $msg = '';
    }
    $link = 'index.php?option='.$option.'&view=turnier_rangliste&turnier='.$this->turnier->id.'&pgn=0';
    if ($itemid != 0) {
        $link .= '&Itemid='.$itemid;
    }
    $mainframe->enqueueMessage($msg);
    $mainframe->redirect($link);
}

// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');


$config = clm_core::$db->config();
// $pdf_melde = $config->pdf_meldelisten;
$fixth_tkreuz = $config->fixth_tkreuz;

// CLM-Container
echo '<div id="clm"><div id="turnier_rangliste">';

// Componentheading
if ($spRang != 0) {			//Sonderranglisten
    $heading = $this->turnier->name.": ".$this->turnier->spRangName." ".JText::_('TOURNAMENT_RANKING');
} else {
    $heading = $this->turnier->name.": ".JText::_('TOURNAMENT_RANKING');
}

$archive_check = clm_core::$api->db_check_season_user($this->turnier->sid);
if (!$archive_check) {
    echo CLMContent::componentheading($heading);
    require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
    echo CLMContent::clmWarning(JText::_('NO_ACCESS')."<br/>".JText::_('NOT_REGISTERED'));
} elseif ($this->turnier->published == 0) {
    echo CLMContent::componentheading($heading);
    echo CLMContent::clmWarning(JText::_('TOURNAMENT_NOTPUBLISHED')."<br/>".JText::_('TOURNAMENT_PATIENCE'));

} elseif ($spRang == 0 and $this->turnier->playersCount < $this->turnier->teil) { //Änderung wegen Sonderranglisten
    $msg = JText::_('TOURNAMENT_PLAYERLISTNOTCOMPLETE')."<br/>".JText::_('TOURNAMENT_NORANKINGEXISTING');
    $link = 'index.php?option='.$option.'&view=turnier_teilnehmer&turnier='.$this->turnier->id;
    if ($itemid != 0) {
        $link .= '&Itemid='.$itemid;
    }
    $mainframe->enqueueMessage($msg);
    $mainframe->redirect($link);

} elseif ($spRang != 0 and $this->turnier->playersCount == 0) { //Hinzugefügt wegen Sonderranglisten
    echo CLMContent::componentheading($heading);
    require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
    echo CLMContent::clmWarning(JText::_('TOURNAMENT_SPECIALRANKING_NOPLAYERS'));

} else {
    $turParams = new clm_class_params($this->turnier->params);
    // PDF-Link
    echo CLMContent::createPDFLink('turnier_rangliste', JText::_('TOURNAMENT_RANKING'), array('turnier' => $this->turnier->id, 'layout' => 'rangliste', 'spRang' => $spRang));
    if ($spRang != 0) {			//Sonderranglisten
        echo CLMContent::createViewLink('turnier_tabelle', JText::_('RANGLISTE_GOTO_TABELLE'), array('turnier' => $this->turnier->id, 'spRang' => $spRang, 'Itemid' => $itemid));
    } else {
        echo CLMContent::createViewLink('turnier_tabelle', JText::_('RANGLISTE_GOTO_TABELLE'), array('turnier' => $this->turnier->id, 'Itemid' => $itemid));
    }
    // PGN-Download gesamtes Turnier
    if (($jid != 0 and $turParams->get('pgnPublic', 0) == '1') or $turParams->get('pgnDownload', 0) == '1') {
        echo CLMContent::createPGNLink('turnier_rangliste', JText::_('RANGLISTE_PGN_ALL'), array('turnier' => $this->turnier->id), 1);
    }

    echo CLMContent::componentheading($heading);
    require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');

    $heim = array(1 => "W", 0 => "S");
    $fwFieldNames = array(1 => 'sum_bhlz', 'sum_busum', 'sum_sobe', 'sum_wins');

    // Anzahl FW-Spalten
    $rightcol = 0;
    // Breite fuer Rechte Spalte
    for ($f = 1; $f <= 3; $f++) {
        $fwFieldName = 'tiebr'.$f;
        if ($this->turnier->$fwFieldName > 0) {
            $rightcol++;
        }
    }

    if ($this->turnier->typ == 1) { // CH-System

        // div Table

        echo '<div id="tableoverflow">';

        // Linke Spalte Start
        echo '<div class="lefttable">';

        echo '<table cellpadding="0" cellspacing="0" id="lefttabletable"';
        if ($fixth_tkreuz == "1") {
            echo ' class="tableWithFloatingHeader"';
        };
        echo '>';

        // header

        echo '<tr>';
        echo '<th class="rang"><div>';
        $link = new CLMcLink();
        $link->view = 'turnier_rangliste';
        $link->more = array('turnier' => $this->turnier->id, 'orderby' => 'pos', 'Itemid' => $itemid);
        $link->makeURL();
        echo $link->makeLink(JText::_('TOURNAMENT_RANKABB'));
        echo '</div></th>';
        if ($turParams->get('displayPlayerSnr', 1) == 1) {
            echo '<th class="tln"><div>';
            $link = new CLMcLink();
            $link->view = 'turnier_rangliste';
            $link->more = array('turnier' => $this->turnier->id, 'orderby' => 'snr', 'Itemid' => $itemid);
            $link->makeURL();
            echo $link->makeLink(JText::_('TOURNAMENT_NUMBERABB'));
            echo '</div></th>';
        }
        echo '<th class="name"><div>'.JText::_('TOURNAMENT_PLAYERNAME').'</div></th>';
        echo '<th class="twz"><div>'.JText::_('TOURNAMENT_TWZ').'</div></th>';
        echo '</tr>';

        // alle Spieler durchgehen

        for ($p = 0; $p < $this->turnier->playersCount; $p++) {

            if ($p % 2 != 0) {
                $zeilenr = "zeile1";
            } else {
                $zeilenr = "zeile2";
            }

            echo '<tr class="'.$zeilenr.'">';
            echo '<td class="rang'.$this->players[$p]->quali.'"><div>'.CLMText::getPosString($this->players[$p]->rankingPos).'</div></td>';
            if ($turParams->get('displayPlayerSnr', 1) == 1) {
                echo '<td class="tln"><div>'.$this->players[$p]->snr.'</div></td>';
            }
            echo '<td class="name"><div>';
            $link = new CLMcLink();
            $link->view = 'turnier_player';
            $link->more = array('turnier' => $this->turnier->id, 'snr' => $this->players[$p]->snr, 'Itemid' => $itemid);
            $link->makeURL();
            echo $link->makeLink($this->players[$p]->name);
            echo '</div></td>';
            echo '<td class="twz"><div>'.CLMText::formatRating($this->players[$p]->twz).'</div></td>';
            echo '</tr>';
        }

        // ende alle Spieler

        echo '</table></div>';

        // Ende Linke Spalte


        // Rechte Spalte
        echo '<div class="righttable_ch'.$rightcol.'">';

        // Table
        echo '<table cellpadding="0" cellspacing="0" id="righttabletable_ch'.$rightcol.'"';
        if ($fixth_tkreuz == "1") {
            echo ' class="tableWithFloatingHeader"';
        };
        echo '>';

        // header

        echo '<tr>';
        echo '<th class="fw_col"><div>'.JText::_('TOURNAMENT_POINTS_ABB').'</div></th>';
        // mgl. Feinwertungen
        for ($f = 1; $f <= 3; $f++) {
            $fwFieldName = 'tiebr'.$f;
            if ($this->turnier->$fwFieldName > 0 and $this->turnier->$fwFieldName < 50) {
                echo '<th class="fw_col"><div>'.JText::_('TOURNAMENT_TIEBR_ABB_'.$this->turnier->$fwFieldName).'</div></th>';
            }
        }
        echo '</tr>';

        // alle Spieler durchgehen

        for ($p = 0; $p < $this->turnier->playersCount; $p++) {
            if ($p % 2 != 0) {
                $zeilenr = "zeile1";
            } else {
                $zeilenr = "zeile2";
            }

            echo '<tr class="'.$zeilenr.'">';
            echo '<td class="fw_col"><div>'.$this->players[$p]->sum_punkte.'</div></td>';
            // mgl. Feinwertungen
            for ($f = 1; $f <= 3; $f++) {
                $fwFieldName = 'tiebr'.$f;
                $plTiebrField = 'sumTiebr'.$f;
                if ($this->turnier->$fwFieldName > 0) {
                    echo '<td class="fw_col"><div>'.CLMtext::tiebrFormat($this->turnier->$fwFieldName, $this->players[$p]->$plTiebrField).'</div></td>';
                }
            }
            echo '</tr>';
        }

        echo '</table></div>';
        // Ende Rechte Spalte


        // Mittlere Spalte
        echo '<div class="midtable_ch">';

        echo '<table cellpadding="0" cellspacing="0" id="midtabletable_ch"';
        if ($fixth_tkreuz == "1") {
            echo ' class="tableWithFloatingHeader"';
        };
        echo '>';

        // header

        echo '<tr>';
        for ($rnd = 1; $rnd <= $this->turnier->runden; $rnd++) {
            echo '<th class="erg_ch"><div>'.$rnd.'</div></th>';
        }
        echo '</tr>';

        // alle Spieler durchgehen

        for ($p = 0; $p < $this->turnier->playersCount; $p++) {
            if ($p % 2 != 0) {
                $zeilenr = "zeile1";
            } else {
                $zeilenr = "zeile2";
            }

            echo '<tr class="'.$zeilenr.'">';
            // alle Runden

            for ($rnd = 1; $rnd <= $this->turnier->runden; $rnd++) {

                echo '<td class="erg_ch"><div>';

                // ergebnis ermitteln

                //						if (isset($this->matrix[$this->players[$p]->snr][$rnd]->ergebnis)) {
                if (isset($this->matrix[$this->players[$p]->snr][$rnd]->runde)) {
                    echo '<a href="index.php?option=com_clm&amp;view=turnier_runde&amp;turnier='.$this->turnier->id.'&amp;runde='.$this->matrix[$this->players[$p]->snr][$rnd]->runde.'&Itemid='.$itemid.'">';
                    if (isset($this->posToPlayers[$this->matrix[$this->players[$p]->snr][$rnd]->gegner])) {
                        echo $this->posToPlayers[$this->matrix[$this->players[$p]->snr][$rnd]->gegner];
                    }
                    echo $heim[$this->matrix[$this->players[$p]->snr][$rnd]->heim];
                    echo CLMText::getResultString($this->matrix[$this->players[$p]->snr][$rnd]->ergebnis, 0);

                    echo '</a>';
                } else {
                    echo '&nbsp;';
                }
                echo '</div></td>';
            }
            echo '</tr>';
        }

        // ende alle Spieler

        echo '</table></div>';

        // Ende Mittlere Spalte

        echo '</div>';
        echo '<div class="clr"></div>';
        // Ende div Table

    } elseif ($this->turnier->typ == 2) { // Vollrunde

        // div Table

        echo '<div id="tableoverflow">';

        // Linke Spalte
        echo '<div class="lefttable">';

        echo '<table cellpadding="0" cellspacing="0" id="lefttabletable"';
        if ($fixth_tkreuz == "1") {
            echo 'class="tableWithFloatingHeader"';
        };

        // header

        echo '<tr>';

        echo '<th class="rang"><div>';
        $link = new CLMcLink();
        $link->view = 'turnier_rangliste';
        $link->more = array('turnier' => $this->turnier->id, 'orderby' => 'pos', 'Itemid' => $itemid);
        $link->makeURL();
        echo $link->makeLink(JText::_('TOURNAMENT_RANKABB'));
        echo '</div></th>';
        if ($turParams->get('displayPlayerSnr', 1) == 1) {
            echo '<th class="tln"><div>';
            $link = new CLMcLink();
            $link->view = 'turnier_rangliste';
            $link->more = array('turnier' => $this->turnier->id, 'orderby' => 'snr', 'Itemid' => $itemid);
            $link->makeURL();
            echo $link->makeLink(JText::_('TOURNAMENT_NUMBERABB'));
            echo '</div></th>';
        }
        echo '<th class="name"><div>'.JText::_('TOURNAMENT_PLAYERNAME').'</div></th>';
        echo '<th class="twz"><div>'.JText::_('TOURNAMENT_TWZ').'</div></th>';

        echo '</tr>';

        // alle Spieler durchgehen

        for ($p = 0; $p < $this->turnier->teil; $p++) {
            if (!isset($this->players[$p])) {
                break;
            }
            if ($p % 2 != 0) {
                $zeilenr = "zeile1";
            } else {
                $zeilenr = "zeile2";
            }

            echo '<tr class="'.$zeilenr.'">';
            echo '<td class="rang'.$this->players[$p]->quali.'"><div>'.CLMText::getPosString($this->players[$p]->rankingPos).'</div></td>';
            if ($turParams->get('displayPlayerSnr', 1) == 1) {
                echo '<td class="tln"><div>'.$this->players[$p]->snr.'</div></td>';
            }
            echo '<td class="name"><div>';
            $link = new CLMcLink();
            $link->view = 'turnier_player';
            $link->more = array('turnier' => $this->turnier->id, 'snr' => $this->players[$p]->snr, 'Itemid' => $itemid);
            $link->makeURL();
            echo $link->makeLink($this->players[$p]->name);
            echo '</div></td>';
            echo '<td class="twz"><div>'.CLMText::formatRating($this->players[$p]->twz).'</div></td>';
            echo '</tr>';
        }

        // Ende Spieler

        echo '</table></div>';


        // Rechte Spalte
        echo '<div class="righttable'.$rightcol.'">';

        echo '<table cellpadding="0" cellspacing="0" id="righttabletable'.$rightcol.'"';
        if ($fixth_tkreuz == "1") {
            echo 'class="tableWithFloatingHeader"';
        };

        echo '<tr>';
        echo '<th class="fw_col"><div>'.JText::_('TOURNAMENT_POINTS_ABB').'</div></th>';
        // mgl. Feinwertungen
        for ($f = 1; $f <= 3; $f++) {
            $fwFieldName = 'tiebr'.$f;
            if ($this->turnier->$fwFieldName > 0 and $this->turnier->$fwFieldName < 50) {
                echo '<th class="fw_col"><div>'.JText::_('TOURNAMENT_TIEBR_ABB_'.$this->turnier->$fwFieldName).'</div></th>';
            }
        }

        echo '</tr>';

        // alle Spieler durchgehen

        for ($p = 0; $p < $this->turnier->teil; $p++) {
            if (!isset($this->players[$p])) {
                break;
            }
            if ($p % 2 != 0) {
                $zeilenr = "zeile1";
            } else {
                $zeilenr = "zeile2";
            }

            echo '<tr class="'.$zeilenr.'">';
            echo '<td class="fw_col"><div>'.$this->players[$p]->sum_punkte.'</div></td>';
            // mgl. Feinwertungen
            for ($f = 1; $f <= 3; $f++) {
                $fwFieldName = 'tiebr'.$f;
                $plTiebrField = 'sumTiebr'.$f;
                if ($this->turnier->$fwFieldName > 0 and $this->turnier->$fwFieldName < 50) {
                    echo '<td class="fw_col"><div>'.CLMtext::tiebrFormat($this->turnier->$fwFieldName, $this->players[$p]->$plTiebrField).'</div></td>';
                }
            }

            echo '</tr>';
        }

        // Ende Spieler

        echo '</table>';

        echo '</div>';
        echo '<div class="midtable">';
        echo '<table cellpadding="0" cellspacing="0" id="midtabletable"';
        if ($fixth_tkreuz == "1") {
            echo 'class="tableWithFloatingHeader"';
        };
        echo '<tr>';

        // alle Durchgänge
        for ($dg = 1; $dg <= $this->turnier->dg; $dg++) {
            for ($rnd = 1; $rnd <= $this->turnier->teil; $rnd++) {
                if (!isset($this->players[$rnd - 1])) {
                    break;
                }
                echo '<th class="erg"><div>'.$rnd.'</div></th>';
            }
        }

        echo '</tr>';

        // alle Spieler durchgehen

        for ($p = 0; $p < $this->turnier->teil; $p++) {
            if (!isset($this->players[$p])) {
                break;
            }
            if ($p % 2 != 0) {
                $zeilenr = "zeile1";
            } else {
                $zeilenr = "zeile2";
            }

            echo '<tr class="'.$zeilenr.'">';

            // alle Durchgänge
            for ($dg = 1; $dg <= $this->turnier->dg; $dg++) {

                for ($rnd = 1; $rnd <= $this->turnier->teil; $rnd++) {
                    if (!isset($this->players[$rnd - 1])) {
                        break;
                    }
                    if ($rnd == ($p + 1)) {
                        echo'<td class="trenner"><div>X</div></td>';
                    } else {
                        echo '<td class="erg"><div>';
                        if (isset($this->matrix[$this->players[$p]->snr][$this->posToPlayers[$rnd]][$dg]->ergebnis)) {
                            $link = new CLMcLink();
                            $link->view = 'turnier_runde';
                            $link->more = array('turnier' => $this->turnier->id, 'runde' => $this->matrix[$this->players[$p]->snr][$this->posToPlayers[$rnd]][$dg]->runde, 'dg' => $dg, 'Itemid' => $itemid);
                            $link->makeURL();
                            echo $link->makeLink(CLMText::getResultString($this->matrix[$this->players[$p]->snr][$this->posToPlayers[$rnd]][$dg]->ergebnis, 0));
                        }
                        echo '</div></td>';
                    }
                }
            }
            echo '</tr>';
        }

        // Ende Spieler

        echo '</table></div>';

        echo '</div>';

        echo '<div class="clr"></div>';

    }

}

require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php');
echo '</div></div>';
