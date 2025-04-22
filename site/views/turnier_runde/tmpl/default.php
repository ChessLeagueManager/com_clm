<?php
/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2022 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');
//JHtml::_('behavior.tooltip', '.CLMTooltip');
require_once(JPATH_COMPONENT . DS . 'includes' . DS . 'clm_tooltip.php');


// Konfigurationsparameter auslesen
$itemid = clm_core::$load->request_int('Itemid');
$config = clm_core::$db->config();
$commentParse = $config->tourn_comment_parse;
$pgn		= clm_core::$load->request_int('pgn');

// Userkennung holen
$user	= JFactory::getUser();
$jid	= $user->get('id');

if ($pgn == 1) {
    $result = clm_core::$api->db_pgn_template($this->turnier->id, $this->round->dg, $this->round->nr, $pgn, false);
    $_GET['pgn'] = 0;
    if (!$result[1]) {
        $msg = JText::_(strtoupper($result[1])).'<br><br>';
    } else {
        $msg = '';
    }
    $link = 'index.php?option='.$option.'&view=turnier_runde&liga='.$this->turnier->id.'&dg='.$$this->round->dg.'&runde='.$this->round->nr.'&pgn=0';
    if ($itemid != 0) {
        $link .= '&Itemid='.$itemid;
    }
    $mainframe->redirect($link, $msg);
}

// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');


echo "<div id='clm'><div id='turnier_runde'>";

$heading = $this->turnier->name;
$heading .= ": ".$this->round->name;

$archive_check = clm_core::$api->db_check_season_user($this->turnier->sid);
if (!$archive_check) {
    echo CLMContent::componentheading($heading);
    require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
    echo CLMContent::clmWarning(JText::_('NO_ACCESS')."<br/>".JText::_('NOT_REGISTERED'));
    // Turnier unveröffentlicht?
} elseif ($this->turnier->published == 0) {
    echo CLMContent::componentheading($heading);
    echo CLMContent::clmWarning(JText::_('TOURNAMENT_NOTPUBLISHED')."<br/>".JText::_('TOURNAMENT_PATIENCE'));

    // Runden nicht erstellt
} elseif ($this->turnier->rnd == 0) {
    echo CLMContent::componentheading($heading);
    require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
    echo CLMContent::clmWarning(JText::_('TOURNAMENT_NOROUNDS'));

} elseif ($this->round->published != 1) {
    echo CLMContent::componentheading($heading);
    require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
    echo CLMContent::clmWarning(JText::_('TOURNAMENT_ROUNDNOTPUBLISHED'));

    // Turnier/Runde kann ausgegeben werden
} else {
    $turParams = new clm_class_params($this->turnier->params);
    if ($this->round->datum != "0000-00-00" and $this->round->datum != "1970-01-01" and $turParams->get('displayRoundDate', 1) == 1) {
        $heading .=  ',&nbsp;'.JHTML::_('date', $this->round->datum, JText::_('DATE_FORMAT_CLM_F'));
        if (isset($this->round->startzeit) and $this->round->startzeit != '00:00:00') {
            $heading .= '  '.substr($this->round->startzeit, 0, 5).' Uhr';
        }
    }

    // PDF-Link
    echo CLMContent::createPDFLink('turnier_runde', JText::_('PDF_TOURNAMENTROUND'), array('turnier' => $this->turnier->id, 'layout' => 'runde', 'dg' => $this->round->dg, 'runde' => $this->round->nr));

    if ($jid != 0) {
        echo CLMContent::createPGNLink('turnier_runde', JText::_('ROUND_PGN_ALL'), array('turnier' => $this->turnier->id, 'dg' => $this->round->dg, 'runde' => $this->round->nr));
    }

    echo CLMContent::componentheading($heading);
    require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');

    // Table aufziehen
    echo '<table cellpadding="0" cellspacing="0" class="runde">';

    // Kopfzeile
    echo '<tr><td colspan="9">';
    echo '<div style="text-align:left; padding-left:1%">';
    echo '<b>'.$this->round->name.'</b>';
    echo '</div>';
    echo '</td></tr>';
    // Ende Kopfzeile


    // headers
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

    // alle Matches durchgehen
    $ia = -1;
    foreach ($this->matches as $value) {

        // Farbe

        if ($value->brett % 2 != 0) {
            $zeilenr = "zeile1";
        } else {
            $zeilenr = "zeile2";
        }

        if (($value->spieler != 0 and $value->gegner != 0) or !is_null($value->ergebnis)) {
            $ic = 0;
            echo '<tr class="'.$zeilenr.'">';
            echo '<td align="center">'.$value->brett.'</td>';
            echo '<td>';
            if (isset($this->points[$value->spieler])) {
                $points = $this->points[$value->spieler];
            } else {
                $points = 0;
            }
            if (isset($value->wname)) {
                $link = new CLMcLink();
                $link->view = 'turnier_player';
                $link->more = array('turnier' => $this->turnier->id, 'snr' => $value->spieler, 'Itemid' => $itemid);
                $link->makeURL();
                if ($this->turnier->typ != '3' and $this->turnier->typ != '5') {
                    echo $link->makeLink($value->wname). " (".$points.")";
                } else {
                    echo $link->makeLink($value->wname);
                }
            }
            echo '</td>';
            echo '<td align="center">'.CLMText::formatRating($value->wtwz).'</td>';
            echo '<td align="center">-</td>';
            echo '<td>';
            if (isset($this->points[$value->gegner])) {
                $points = $this->points[$value->gegner];
            } else {
                $points = 0;
            }
            if (isset($value->sname)) {
                $link = new CLMcLink();
                $link->view = 'turnier_player';
                $link->more = array('turnier' => $this->turnier->id, 'snr' => $value->gegner, 'Itemid' => $itemid);
                $link->makeURL();
                if ($this->turnier->typ != '3' and $this->turnier->typ != '5') {
                    echo $link->makeLink($value->sname). " (".$points.")";
                } else {
                    echo $link->makeLink($value->sname);
                }
            }
            echo '</td>';
            echo '<td align="center">'.CLMText::formatRating($value->stwz).'</td>';
            if (!is_null($value->ergebnis)) {
                echo '<td align="center">';
                if ($value->pgn == '' or !$this->pgnShow) {
                    echo CLMText::getResultString($value->ergebnis);
                } else {
                    if (is_numeric($value->pgn)) {
                        $pgntext = $value->text;
                    } else {
                        $pgntext = $value->pgn;
                    }
                    $ia++;
                    $ic = 1;
                    echo '<span class="editlinktip hasTip" title="'.JText::_('PGN_SHOWMATCH').'">';
                    echo '<a onclick="startPgnMatch('.$value->id.', \'pgnArea'.$ia.'\');" class="pgn">'.CLMText::getResultString($value->ergebnis).'</a>';
                    echo '</span>';
                    ?>
						<input type='hidden' name='pgn[<?php echo $value->id; ?>]' id='pgnhidden<?php echo $value->id; ?>' value='<?php echo str_replace("'", "&#039", $pgntext); ?>'>
						<?php
                }
                if (($this->turnier->typ == 3 or $this->turnier->typ == '5') and ($value->tiebrS > 0 or $value->tiebrG > 0)) {
                    echo '<br /><small>'.$value->tiebrS.':'.$value->tiebrG.'</small>';
                }
                echo '</td>';
                ?>
					<?php
            } else {
                echo '<td align="center"></td>';
            }

            echo '</tr>';
            if ($value->pgn != '' and $this->pgnShow and $ic == 1) { ?>
				<!--Bereich für pgn-Viewer-->
				<tr><td colspan="9"><span id="pgnArea<?php echo $ia; ?>"></span></td></tr>
			<?php }
            }

    }

    // tl_ok? Haken anzeigen!
    if ($this->displayTlOK and $this->round->tl_ok > 0) {
        echo '<tr><td colspan="9">';
        echo '<div style="float:right; padding-right:1%;"><label for="name" class="hasTip" title="'.JText::_('TOURNAMENT_ROUNDOK').'"><img src="'.CLMImage::imageURL('accept.png').'" /></label></div>';
        echo '</td></tr>';
    }

    echo '</table>';

    ?>
	
	<!--Bereich für pgn-Viewer-->
	<span id="pgnArea"></span>

	<?php

    if ($this->round->bemerkungen != '') {
        echo "<div id='desc'>";
        if ($commentParse) {
            echo JHtml::_('content.prepare', "\n" . $this->round->bemerkungen . "\n");
        } else {
            echo CLMText::formatNote($this->round->bemerkungen);
        }
        echo "</div>";
    }
}

require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php');
echo '</div></div>';

?>
