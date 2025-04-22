<?php
/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2025 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
 * Kommentare Deutsch - Comments English
*/
defined('_JEXEC') or die('Restricted access');
//JHtml::_('behavior.tooltip', '.CLMTooltip');
require_once(JPATH_COMPONENT . DS . 'includes' . DS . 'clm_tooltip.php');

// Konfigurationsparameter auslesen - get configuration parameters
$itemid = clm_core::$load->request_int('Itemid');
$config = clm_core::$db->config();
$pgn	= clm_core::$load->request_int('pgn');

// Userkennung holen - get user id
$user	= JFactory::getUser();
$jid	= $user->get('id');

if ($pgn == 1) {
    $nl = "\n";
    $file_name = clm_core::$load->utf8decode($this->turnier->name);
    $file_name = strtr($file_name, ' ./', '___');
    $file_name .= '.pgn';
    $pdatei = fopen($file_name, "wt");
    // alle Runden durchgehen - go through all rounds
    foreach ($this->rounds as $value) {
        // alle Matches durchgehen - go through all matches
        foreach ($this->matches[$value->nr] as $matches) {
            if (($matches->spieler != 0 and $matches->gegner != 0) or !is_null($matches->ergebnis)) {
                $gtmarker = "*";
                $resulthint = "";
                fputs($pdatei, '[Event "'.clm_core::$load->utf8decode($this->turnier->name).'"]'.$nl);
                fputs($pdatei, '[Site "?"]'.$nl);
                fputs($pdatei, '[Date "'.JHTML::_('date', $value->datum, JText::_('Y.m.d')).'"]'.$nl);
                fputs($pdatei, '[Round "'.$value->nr.'"]'.$nl);
                fputs($pdatei, '[Board "'.$matches->brett.'"]'.$nl);
                fputs($pdatei, '[White "'.clm_core::$load->utf8decode($matches->wname).'"]'.$nl);
                fputs($pdatei, '[Black "'.clm_core::$load->utf8decode($matches->sname).'"]'.$nl);
                fputs($pdatei, '[WhiteTeam "'.clm_core::$load->utf8decode($matches->wverein).'"]'.$nl);
                fputs($pdatei, '[BlackTeam "'.clm_core::$load->utf8decode($matches->sverein).'"]'.$nl);
                fputs($pdatei, '[WhiteElo "'.$matches->welo.'"]'.$nl);
                fputs($pdatei, '[BlackElo "'.$matches->selo.'"]'.$nl);
                fputs($pdatei, '[WhiteDWZ "'.$matches->wdwz.'"]'.$nl);
                fputs($pdatei, '[BlackDWZ "'.$matches->sdwz.'"]'.$nl);
                if ($matches->ergebnis == "2") {
                    fputs($pdatei, '[Result "1/2-1/2"]'.$nl);
                    $gtmarker = "1/2-1/2";
                } elseif ($matches->ergebnis == "0") {
                    fputs($pdatei, '[Result "0-1"]'.$nl);
                    $gtmarker = "0-1";
                } elseif ($matches->ergebnis == "1") {
                    fputs($pdatei, '[Result "1-0"]'.$nl);
                    $gtmarker = "1-0";
                } elseif ($matches->ergebnis == "5") {
                    fputs($pdatei, '[Result "1-0"]'.$nl);
                    $resulthint = "{".clm_core::$load->utf8decode(JText::_('PAAR_RESULT_HINT_1'))."}";
                    $gtmarker = "1-0";
                } elseif ($matches->ergebnis == "4") {
                    fputs($pdatei, '[Result "0-1"]'.$nl);
                    $resulthint = "{".clm_core::$load->utf8decode(JText::_('PAAR_RESULT_HINT_2'))."}";
                    $gtmarker = "0-1";
                } elseif ($matches->ergebnis == "6") {
                    fputs($pdatei, '[Result "*"]'.$nl);
                    $resulthint = "{".clm_core::$load->utf8decode(JText::_('PAAR_RESULT_HINT_3'))."}";
                    $gtmarker = "*";
                } else {
                    fputs($pdatei, '[Result "'.$matches->ergebnis.'"]'.$nl);
                }
                fputs($pdatei, '[PlyCount "0"]'.$nl);
                fputs($pdatei, '[EventDate "'.JHTML::_('date', $this->turnier->dateStart, JText::_('Y.m.d')).'"]'.$nl);
                fputs($pdatei, '[SourceDate "'.JHTML::_('date', $value->datum, JText::_('Y.m.d')).'"]'.$nl);
                fputs($pdatei, ' '.$nl);
                fputs($pdatei, $resulthint.' '.$gtmarker.$nl);
                fputs($pdatei, ' '.$nl);
            }
        }
    }
    fclose($pdatei);
    header('Content-Disposition: attachment; filename='.$file_name);
    header('Content-type: text/html');
    header('Cache-Control:');
    header('Pragma:');
    readfile($file_name);
    flush();
    JFactory::getApplication()->close();
}

// Stylesheet laden - load CSS
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');


// CLM-Container - CLM-Container
echo '<div id="clm"><div id="turnier_paarungsliste">';

// componentheading vorbereiten - prepare componentheading
$heading = $this->turnier->name.": ".JText::_('TOURNAMENT_PAIRINGLIST');

$archive_check = clm_core::$api->db_check_season_user($this->turnier->sid);
if (!$archive_check) {
    echo CLMContent::componentheading($heading);
    require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
    echo CLMContent::clmWarning(JText::_('NO_ACCESS')."<br/>".JText::_('NOT_REGISTERED'));
} elseif ($this->turnier->published == 0) {
    echo CLMContent::componentheading($heading);
    echo CLMContent::clmWarning(JText::_('TOURNAMENT_NOTPUBLISHED')."<br/>".JText::_('TOURNAMENT_PATIENCE'));

} elseif ($this->turnier->rnd == 0) {
    echo CLMContent::componentheading($heading);
    require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
    echo CLMContent::clmWarning(JText::_('TOURNAMENT_NOROUNDS'));

} else {
    // PDF-Link - PGF-link
    echo CLMContent::createPDFLink('turnier_paarungsliste', JText::_('TOURNAMENT_PAIRINGLIST_PRINT'), array('turnier' => $this->turnier->id, 'layout' => 'paarungsliste'));

    if ($jid != 0) {
        echo CLMContent::createPGNLink('turnier_paarungsliste', JText::_('TOURNAMENT_PGN_ALL'), array('turnier' => $this->turnier->id));
    }

    echo CLMContent::componentheading($heading);

    require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');

    function pgn_element($contents, $uelement, $ustart, $debug = 0)
    {
        $upos = strpos(($contents), $uelement.' ', $ustart);
        if ($upos === false) {
            return '';
        }
        $length = strlen($uelement);
        $vstart = $upos + $length + 2;
        $vend = strpos(($contents), '"', $vstart + 1);
        if ($vend === false) {
            return '';
        }
        $value = substr($contents, $vstart, $vend - $vstart);
        return $value;
    }

    $turParams = new clm_class_params($this->turnier->params);
    $param_source = $turParams->get('import_source', '0');
    $ia = -1;
    // alle Runden durchgehen - go through all rounds
    foreach ($this->rounds as $value) {

        // veröffentlicht? - published?
        if ($value->published == 1) {

            // Table aufziehen - create table
            echo '<table cellpadding="0" cellspacing="0" class="runde">';

            // Kopfzeile - table heading
            echo '<tr><td colspan="9">';
            echo '<div style="text-align:left; padding-left:1%">';
            echo '<b>';
            echo $value->name;
            if ($value->datum != "0000-00-00" and $value->datum != "1970-01-01" and $turParams->get('displayRoundDate', 1) == 1) {
                echo ',&nbsp;'.JHTML::_('date', $value->datum, JText::_('DATE_FORMAT_CLM_F'));
                if (isset($value->startzeit) and $value->startzeit != '00:00:00') {
                    echo '  '.substr($value->startzeit, 0, 5).' Uhr';
                }
            }
            echo '</b>';
            echo '</div>';
            echo '</td></tr>';
            // Ende Kopfzeile - end of header

            // Spaltenüberschriften - title of columns
            ?>
			<tr>
				<th align="center"><?php echo JText::_('TOURNAMENT_TNR'); ?></th>
				<th align="center"><?php echo JText::_('TOURNAMENT_WHITE'); ?></th>
				<th align="center"><?php echo JText::_('TOURNAMENT_TWZ'); ?></th>
				<th align="center">-</th>
				<th align="center"><?php echo JText::_('TOURNAMENT_BLACK'); ?></th>
				<th align="center"><?php echo JText::_('TOURNAMENT_TWZ'); ?></th>
				<th align="center"><?php echo JText::_('RESULT'); ?></th>
			</tr>
			<?php

            // alle Matches eintragen - register all matches
            $m = 0; // CounterFlag für Farbe - CounterFlag for colour
            $nb = 0; //Tischnummer - board number
            foreach ($this->matches[$value->nr + (($value->dg - 1) * $this->turnier->runden)] as $matches) {

                $m++;
                // Farbe - colour
                if ($m % 2 != 0) {
                    $zeilenr = "zeile1";
                } else {
                    $zeilenr = "zeile2";
                }

                if (($matches->spieler != 0 and $matches->gegner != 0) or !is_null($matches->ergebnis)) {
                    echo '<tr class="'.$zeilenr.'">';
                    $nb++;
                    $ic = 0;
                    echo '<td align="center">'.$nb.'</td>';
                    echo '<td>';
                    if (isset($this->points[$value->nr + (($value->dg - 1) * $this->turnier->runden)][$matches->spieler])) {
                        $points = $this->points[$value->nr + (($value->dg - 1) * $this->turnier->runden)][$matches->spieler];
                    } else {
                        $points = 0;
                    }
                    if (isset($this->players[$matches->spieler]->name)) {
                        $link = new CLMcLink();
                        $link->view = 'turnier_player';
                        $link->more = array('turnier' => $this->turnier->id, 'snr' => $matches->spieler, 'Itemid' => $itemid);
                        $link->makeURL();
                        if ($this->turnier->typ != '3' and $this->turnier->typ != '5') {
                            echo $link->makeLink($this->players[$matches->spieler]->name). " (".$points.")";
                        } else {
                            echo $link->makeLink($this->players[$matches->spieler]->name);
                        }
                    }
                    echo '</td>';
                    if (isset($this->players[$matches->spieler]->twz) and $this->players[$matches->spieler]->twz > 0) {
                        echo '<td align="center">'.CLMText::formatRating($this->players[$matches->spieler]->twz).'</td>';
                    } else {
                        echo '<td align="center">-</td>';
                    }
                    echo '<td align="center">-</td>';
                    echo '<td>';
                    if (isset($this->points[$value->nr + (($value->dg - 1) * $this->turnier->runden)][$matches->gegner])) {
                        $points = $this->points[$value->nr + (($value->dg - 1) * $this->turnier->runden)][$matches->gegner];
                    } else {
                        $points = 0;
                    }
                    if (isset($this->players[$matches->gegner]->name) and strlen($this->players[$matches->gegner]->name) > 0) {
                        $link = new CLMcLink();
                        $link->view = 'turnier_player';
                        $link->more = array('turnier' => $this->turnier->id, 'snr' => $matches->gegner, 'Itemid' => $itemid);
                        $link->makeURL();
                        if ($this->turnier->typ != '3' and $this->turnier->typ != '5') {
                            echo $link->makeLink($this->players[$matches->gegner]->name). " (".$points.")";
                        } else {
                            echo $link->makeLink($this->players[$matches->gegner]->name);
                        }
                    }
                    echo '</td>';
                    if (isset($this->players[$matches->gegner]->twz) and $this->players[$matches->gegner]->twz > 0) {
                        echo '<td align="center">'.CLMText::formatRating($this->players[$matches->gegner]->twz).'</td>';
                    } else { //if (strlen($this->players[$matches->gegner]->name) > 0) {
                        echo '<td align="center">-</td>';
                    }
                    //} else {
                    //	echo '<td align="center">&nbsp;</td>';
                    //}

                    if (!is_null($matches->ergebnis)) {
                        echo '<td align="center">';
                        if ($matches->pgn == '' or !$this->pgnShow) {
                            echo CLMText::getResultString($matches->ergebnis);
                        } else {
                            if (is_numeric($matches->pgn)) {
                                $pgntext = $matches->text;
                            } else {
                                $pgntext = $matches->pgn;
                            }
                            $ia++;
                            $ic = 1;
                            echo '<span class="editlinktip hasTip" title="'.JText::_('PGN_SHOWMATCH').'">';
                            echo '<a onclick="startPgnMatch('.$matches->id.', \'pgnArea'.$ia.'\');" class="pgn">'.CLMText::getResultString($matches->ergebnis).'</a>';
                            echo '</span>';
                            ?>
								<input type='hidden' name='pgn[<?php echo $matches->id; ?>]' id='pgnhidden<?php echo $matches->id; ?>' value='<?php echo str_replace("'", "&#039", $pgntext); ?>'>
								<?php
                        }

                        // echo CLMText::getResultString($matches->ergebnis);
                        if (($this->turnier->typ == 3 or $this->turnier->typ == '5') and ($matches->tiebrS > 0 or $matches->tiebrG > 0)) {
                            echo '<br /><small>'.$matches->tiebrS.':'.$matches->tiebrG.'</small>';
                        }
                        if ($param_source == 'lichess' and $matches->pgn != '' and $this->pgnShow) {
                            $site = pgn_element($matches->pgn, 'Site', 0);
                            echo ' <small style="float:right"><a href="'.$site.'" target="_blank">lichess</a></small>';
                        }
                        echo '</td>';
                    } else {
                        echo '<td align="center"></td>';
                    }
                    echo '</tr>';
                    if ($matches->pgn != '' and $this->pgnShow and $ic == 1) { ?>
						<!--Bereich für pgn-Viewer-->
						<tr><td colspan="9"><span id="pgnArea<?php echo $ia; ?>"></span></td></tr>
					<?php }
                    }

            }

            // tl_ok? Haken anzeigen! - tl_ok? shoe tick
            if ($this->displayTlOK and $value->tl_ok > 0) {
                echo '<tr><td colspan="9">';
                echo '<div style="float:right; padding-right:1%;"><label for="name" class="hasTip" title="'.JText::_('TOURNAMENT_ROUNDOK').'"><img  src="'.CLMImage::imageURL('accept.png').'" /></label></div>';
                echo '</td></tr>';
            }


            echo '</table>';

            if ($value->bemerkungen != '') {
                echo "<div id='desc'>";
                echo CLMText::formatNote($value->bemerkungen);
                echo "</div>";
            }
            echo '<br>';

        } else {
            echo '<table cellpadding="0" cellspacing="0" class="runde">';
            echo '<tr><td colspan="9"><div style="text-align:left; padding-left:1%"><b>'.$value->name.'</b>&nbsp;&nbsp;&nbsp;</div></tr>';
            echo '<tr><td><font color="#ff0000">'.JText::_('TOURNAMENT_ROUNDNOTPUBLISHED').'</font></td></tr>';
            echo '</table><br>';
        }

    }


}


require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php');

echo '</div></div>';
?>
