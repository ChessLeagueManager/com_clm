<?php
/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2022 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');
//JHtml::_( 'behavior.modal' );
//JHtml::_('behavior.tooltip', '.CLMTooltip');
require_once(JPATH_COMPONENT . DS . 'includes' . DS . 'clm_tooltip.php');

// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');


// Konfigurationsparameter auslesen
$itemid 		= clm_core::$load->request_int('Itemid');
$config = clm_core::$db->config();
$countryversion = $config->countryversion;
$turParams = new clm_class_params($this->turnier->params);
$typeAccount 	= $turParams->get('typeAccount', 0);

// CLM-Container
echo '<div id="clm"><div id="turnier_player">';


// Componentheading
$heading = $this->turnier->name.": ".JText::_('TOURNAMENT_PARTICIPANTINFO');

$archive_check = clm_core::$api->db_check_season_user($this->turnier->sid);
if (!$archive_check) {
    echo CLMContent::componentheading($heading);
    require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
    echo CLMContent::clmWarning(JText::_('NO_ACCESS')."<br/>".JText::_('NOT_REGISTERED'));
} elseif ($this->turnier->published == 0) {
    echo CLMContent::componentheading($heading);
    echo CLMContent::clmWarning(JText::_('TOURNAMENT_NOTPUBLISHED')."<br/>".JText::_('TOURNAMENT_PATIENCE'));
} else {
    echo CLMContent::componentheading($heading);
    require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
    ?>
<div class="tp_col_playerContent">
    
<?php
if ($this->playerPhoto != '') { ?>
<div class="tp_col_playerInfoDivWithPhoto">
<?php } else { ?>
<div class="tp_col_playerInfoDivWithoutPhoto">
	<?php } ?>
    
	<table cellpadding="0" cellspacing="0" class="tp_col_turnier_playerInfo">
			<tr>
				<th align="left" colspan="2" class="anfang"><?php echo JText::_('TOURNAMENT_PLAYERDATA'); ?></th>
			</tr>
			
			<?php
                if ($this->player->titel != '') {
                    ?>
				<tr>
					<td align="left" class="tp_col_1"><?php echo JText::_('TOURNAMENT_TITLE') ?>:</td>
					<td class="tp_col_data" ><?php echo $this->player->titel ?></td>
				</tr>
			<?php
                }
    ?>
			
			<tr>
				<td align="left" class="tp_col_1"><?php echo JText::_('TOURNAMENT_PLAYERNAME') ?>:</td>
				<td class="tp_col_data"><?php echo $this->player->name ?></td>
			</tr>
			
			<tr>
				<td align="left" class="tp_col_1"><?php echo JText::_('TOURNAMENT_TWZ') ?>:</td>
				<td class="tp_col_data"><?php echo CLMText::formatRating($this->player->twz) ?></td>
			</tr>
			<?php // start_dwz
    if ($turParams->get('displayPlayerRating', 0) == 1) { ?>
			<tr>
				<td align="left" class="tp_col_1"><?php echo JText::_('TOURNAMENT_RATING') ?>:</td>
				<?php 	if ($countryversion == "de") {
				    $mgl4 = ''.$this->player->mgl_nr;
				    while (strlen($mgl4) < 4) {
				        $mgl4 = '0'.$mgl4;
				    } ?>
					<td class="tp_col_data"><a href="http://schachbund.de/spieler.html?zps=<?php echo $this->player->zps; ?>-<?php echo $mgl4; ?>" target="_blank"><?php echo CLMText::formatRating($this->player->start_dwz) ?></td>
				<?php 	} else { ?>
					<td class="tp_col_data"><a href="http://www.ecfgrading.org.uk/new/player.php?PlayerCode=<?php echo $this->player->PKZ; ?>#top" target="_blank"><?php echo CLMText::formatRating($this->player->start_dwz) ?></td>
				<?php } ?>
			</tr>
			<?php } ?>
			
			<?php // ELO
            if ($turParams->get('displayPlayerElo', 0) == 1) { ?>
			<tr>
				<td align="left" class="tp_col_1"><?php echo JText::_('TOURNAMENT_ELO') ?>:</td>
				<?php // FIDE Link
                    if ($turParams->get('displayPlayerFideLink', 0) == 1) { ?>
						<td class="tp_col_data"><a href="http://ratings.fide.com/card.phtml?event=<?php echo $this->player->FIDEid;?>" target="_blank"><?php echo CLMText::formatRating($this->player->FIDEelo) ?></td>
					<?php } else { ?>
						<td class="tp_col_data"><?php echo CLMText::formatRating($this->player->FIDEelo) ?></td>
					<?php } ?>
			</tr>
			<?php } ?>
			
			<tr>
				<td align="left" class="tp_col_1"><?php echo JText::_('TOURNAMENT_CLUB') ?>:</td>
				<td class="tp_col_data">
					<?php
                    if ($this->tourn_linkclub == 1) {
                        $link = new CLMcLink();
                        $link->view = 'verein';
                        $link->more = array('saison' => $this->player->sid, 'zps' => $this->player->zps, 'Itemid' => $itemid );
                        $link->makeURL();
                        echo $link->makeLink($this->player->verein);
                    } else {
                        echo $this->player->verein;
                    }
    ?>
				</td>
			</tr>
			<?php // Online Account
            if ($typeAccount > 0) { ?>
			<tr>
				<td align="left" class="tp_col_1"><?php echo JText::_('REGISTRATION_ACCOUNT_'.$typeAccount) ?>:</td>
				<td class="tp_col_data"><a href="<?php echo $this->player->account ?>" target="blank"><?php echo $this->player->account ?></a></td>
			</tr>
			<?php } ?>
			
			<?php // Federation
            if ($turParams->get('displayPlayerFederation', 0) == 1) { ?>
			<tr>
				<td align="left" class="tp_col_1"><?php echo JText::_('TOURNAMENT_FEDERATION') ?>:</td>
				<td class="tp_col_data"><?php echo $this->player->FIDEcco ?></td>
			</tr>
			<?php } ?>
			
			<?php if ($turParams->get('playerViewDisplaySex', 0)  == 1  and $this->player->geschlecht != '') { ?>
			<tr>
				<td align="left" class="tp_col_1"><?php echo JText::_('TOURNAMENT_PLAYER_SEX') ?>:</td>
				<td class="tp_col_data"><?php echo $this->player->geschlecht; ?></td>
			</tr>
			<?php } ?>
			<?php if ($turParams->get('playerViewDisplayBirthYear', 0) == 1  and $this->player->birthYear != '0000') { ?>
			<tr>
				<td align="left" class="tp_col_1"><?php echo JText::_('TOURNAMENT_PLAYER_BIRTH_YEAR') ?>:</td>
				<td class="tp_col_data"><?php echo $this->player->birthYear; ?></td>
			</tr>
			<?php } ?>
			<?php if (isset($this->player->s_punkte) and $this->player->s_punkte != 0) { ?>
			<tr>
				<td align="left" class="tp_col_1"><?php echo JText::_('TOURNAMENT_SPECIAL_POINTS') ?>:</td>
				<td class="tp_col_data"><?php echo $this->player->s_punkte; ?></td>
			</tr>
			<?php } ?>
			</table>
</div>
<?php
if ($this->playerPhoto != '') { ?>
        <div style="max-width:<?php if ($this->joomGalleryPhotosWidth > 0) {
            echo $this->joomGalleryPhotosWidth + 8;
        }?>px" class="tp_col_playerPhoto">
	<div class="tp_col_playerPhotoFrame">

	<a class="modal" href="<?php echo JRoute::_('index.php?view=image&format=raw&type=orig&id='.$this->playerPhoto.'&option=com_joomgallery', true, -1); ?>" rel="{handler:'image'}">
<img src="<?php echo JRoute::_('index.php?view=image&format=raw&type=img&id='.$this->playerPhoto.'&option=com_joomgallery', true, -1); ?>"/></a>

	</div>
        </div>
			<?php } ?>
        </div>
 
	<?php
    if (isset($this->matches) and !is_null($this->matches) and count($this->matches) > 0) {
        ?>
	
		<table cellpadding="0" cellspacing="0" class="turnier_rangliste">
		
		<tr>
			<th align="left" colspan="6" class="anfang"><?php echo JText::_('TOURNAMENT_MATCHES'); ?></th>
		</tr>
		
		<?php
            // alle Matches...
            $ia = -1;
        foreach ($this->matches as $value) {
            $ic = 0;
            // Zeile
            echo '<tr>';

            // Runde
            echo '<td class="tp_col_1">';
            $link = new CLMcLink();
            $link->view = 'turnier_runde';
            $link->more = array('turnier' => $this->turnier->id, 'runde' => $value->runde, 'Itemid' => $itemid );
            $link->makeURL();
            echo $link->makeLink($value->roundName);
            echo "</td>";

            if ($value->ergebnis != 8) { // kein spielfrei eingetragen

                // Farbe
                echo '<td class="tp_col_2">';
                if ($value->heim == 1) {
                    echo JText::_('TOURNAMENT_WHITE_ABB');
                } else {
                    echo JText::_('TOURNAMENT_BLACK_ABB');
                }
                echo "</td>";

                // Gegner
                if (isset($this->points[$value->gegner])) {
                    $points = $this->points[$value->gegner];
                } else {
                    $points = 0;
                }
                echo '<td class="tp_col_3">';
                if (isset($value->gegner) and $value->gegner > 0) {
                    $link = new CLMcLink();
                    $link->view = 'turnier_player';
                    $link->more = array('turnier' => $this->turnier->id, 'snr' => $value->gegner, 'Itemid' => $itemid );
                    $link->makeURL();
                    echo $link->makeLink($value->oppName). " (".$points.")";
                }
                echo "</td>";

                // Club Gegner
                echo '<td class="tp_col_3c">';
                if (isset($value->gegner) and $value->gegner > 0) {
                    $link = new CLMcLink();
                    $link->view = 'verein';
                    $link->more = array('saison' => $this->turnier->sid, 'zps' => $value->oppZPS, 'Itemid' => $itemid );
                    $link->makeURL();
                    echo $link->makeLink($value->oppVerein);
                }
                echo "</td>";

                // TWZ
                echo '<td class="tp_col_4">';
                if (isset($value->oppTWZ) and $value->oppTWZ > 0) {
                    echo CLMText::formatRating($value->oppTWZ);
                }
                echo "</td>";

                // Ergebnis
                //if ($value->ergebnis != '') {
                if (!is_null($value->ergebnis)) {
                    echo '<td class="tp_col_5">';
                    if ($value->pgn == '' or !$this->pgnShow) {
                        echo CLMText::getResultString($value->ergebnis, 0);
                    } else {
                        if (is_numeric($value->pgn)) {
                            $pgntext = $value->text;
                        } else {
                            $pgntext = $value->pgn;
                        }
                        $ia++;
                        $ic = 1;
                        echo '<span class="editlinktip hasTip" title="'.JText::_('PGN_SHOWMATCH').'">';
                        echo '<a onclick="startPgnMatch('.$value->id.', \'pgnArea'.$ia.'\');" class="pgn">'.CLMText::getResultString($value->ergebnis, 0).'</a>';
                        echo '</span>';
                        ?>
							<input type='hidden' name='pgn[<?php echo $value->id; ?>]' id='pgnhidden<?php echo $value->id; ?>' value='<?php echo str_replace("'", "&#039", $pgntext); ?>'>
							<?php
                    }

                    if ($this->turnier->typ == 3 and ($value->tiebrS > 0 or $value->tiebrG > 0)) {
                        echo '<br /><small>'.$value->tiebrS.':'.$value->tiebrG.'</small>';
                    }
                    echo '</td>';
                } else {
                    echo '<td class="tp_col_5">'.JText::_('TOURNAMENT_MATCHNOTYETPLAYED')."</td>";
                }

            } else { // spielfrei
                echo '<td class="tp_col_2">-</td><td class="tp_col_3">-</td><td class="tp_col_3c">-</td><td class="tp_col_4">-</td><td class="tp_col_5">'.JText::_('RESULT_BYE').'</td>';
            }

            echo '</tr>';
            if ($value->pgn != '' and $this->pgnShow and $ic == 1) { ?>
				<!--Bereich für pgn-Viewer-->
				<tr><td colspan="9"><span id="pgnArea<?php echo $ia; ?>"></span></td></tr>
			<?php }
            // Ende der Zeile
        }

        // Abschlußzeile
        echo '<tr class="ende">';
        // total
        echo '<td  colspan="4">&nbsp;&nbsp;&nbsp;';
        echo JText::_('TEAM_TOTAL');
        echo '</td>';
        // TWZ-Schnitt
        echo '<td class="tp_col_4">';
        if ($this->player->countTWZplayers > 0) {
            echo '&Oslash;&nbsp;'.floor($this->player->sumTWZ / $this->player->countTWZplayers);
            if ($this->player->countTWZplayersNone > 0) {
                echo '<br />('.CLMText::sgpl($this->player->countTWZplayers, JText::_('TOURNAMENT_PLAYER'), JText::_('TOURNAMENT_PLAYERS'), $complete_string = true).')';
            }
        } else {
            echo '-';
        }
        echo '</td>';
        // Pkt
        echo '<td class="tp_col_5">';
        echo $this->player->sum_punkte." / ".$this->player->countMatchesPlayed;
        echo '<br />'.CLMText::getPosString($this->player->rankingPos, 2);
        echo '</td>';




        echo '</tr>';

        ?>
		</table>

		<!--Bereich für pgn-Viewer-->
		<span id="pgnArea"></span>


	<?php
    } ?>
<?php
}

require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php');
echo '</div></div>';
?>
