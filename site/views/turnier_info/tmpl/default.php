<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access');

$arbiter	= $this->arbiter;
// Konfigurationsparameter auslesen
$config 			= clm_core::$db->config();
$googlemaps   	= $config->googlemaps;
$googlemaps_ver   	= $config->googlemaps_ver;
$maps_zoom			= $config->maps_zoom;

// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');

// Load functions for map
require_once(JPATH_COMPONENT.DS.'includes'.DS.'geo_functions.php');

echo '<div="clm"><div id="turnier_info">';

// Konfigurationsparameter auslesen
$itemid 		= clm_core::$load->request_int( 'Itemid' );

// componentheading vorbereiten
$heading = $this->turnier->name;

// Turnier unveröffentlicht?
if ( $this->turnier->published == 0) { 
	
	echo CLMContent::componentheading($heading);
	echo CLMContent::clmWarning(JText::_('TOURNAMENT_NOTPUBLISHED')."<br/>".JText::_('TOURNAMENT_PATIENCE'));

// Turnier
} else {
	echo CLMContent::componentheading($heading);
	require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
	
	// Schiedsrichter aufbereiten
	if (is_null($arbiter) OR count($arbiter) < 1) {
		$s_arbiter = false;
	} else {
		$s_arbiter = true;
		$lang1 = clm_core::$lang->arbiter;
		$aca = ''; $adca = ''; $apo = '';
		$asa = ''; $aasa = ''; $aaca = '';
		foreach ($arbiter as $arb1) {
			if ($arb1->role == 'CA') $aca = $arb1->fname;
			elseif ($arb1->role == 'DCA') {
				if ($adca != '') $adca .= ', ';
				$adca .= $arb1->fname;
			}
			elseif ($arb1->role == 'PO') {
				if ($apo != '') $apo .= ', ';
				$apo .= $arb1->fname;
			}
			elseif ($arb1->role == 'SA') {
				if ($asa != '') $asa .= ', ';
				$asa .= $arb1->fname;
			}
			elseif ($arb1->role == 'ASA') {
				if ($aasa != '') $aasa .= ', ';
				$aasa .= $arb1->fname;
			}
			elseif ($arb1->role == 'ACA') {
				if ($aaca != '') $aaca .= ', ';
				$aaca .= $arb1->fname;
			}
		}
	}	
	?>

	<table>
	
	<tr>
		<th align="left" colspan="2" class="anfang"><?php echo JText::_('TOURNAMENT_DATA'); ?></th>
	</tr>
	
	<tr>
		<td align="left" width="16%"><?php echo JText::_('SEASON') ?>:</td>
		<td><?php echo $this->turnier->saisonname ?></td>
	</tr>

	<?php
	if ($this->turnier->dateStart != '0000-00-00' and $this->turnier->dateStart != '1970-01-01') {
	?>

		<tr>
			<td align="left" width="100">
				<?php
				if ($this->turnier->dateEnd != '0000-00-00' and $this->turnier->dateEnd != '1970-01-01') {
					echo JText::_('TOURNAMENT_TIMEFRAME');
				} else {
					echo JText::_('TOURNAMENT_DATE');
				}
				?>
			</td>
			<td>
				<?php 
				echo JHTML::_( 'date', $this->turnier->dateStart, JText::_('DATE_FORMAT_LC3')); 
				if ($this->turnier->dateEnd != '0000-00-00' and $this->turnier->dateEnd != '1970-01-01') {
					echo '&nbsp;'.JText::_('TOURNAMENT_TO').'&nbsp;'.JHTML::_( 'date', $this->turnier->dateEnd, JText::_('DATE_FORMAT_LC3'));
				}
				?>
			</td>
		</tr>
	<?php
	}
	?>

	<?php
	if ($this->turnier->bezirkTur == 1) {
	?>
		<tr>
			<td align="left" width="100"><?php echo JText::_('TOURNAMENT_ORGANIZER'); ?>:</td>
			<td><?php echo JText::_('TOURNAMENT_DISTRICTEVENT'); ?></td>
		</tr>
		<?php
		if (isset($this->turnier->organame)) {
		?>
			<tr>
				<td align="left" width="100"><?php echo JText::_('TOURNAMENT_HOSTER'); ?>:</td>
				<td><?php echo $this->turnier->organame; ?></td>
			</tr>
		<?php
		}
		?>
	
	<?php
	} else {
	?>
		<tr>
			<td align="left" width="100"><?php echo JText::_('TOURNAMENT_ORGANIZER'); ?>:</td>
			<td><?php echo $this->turnier->organame; ?></td>
		</tr>
	<?php
	}
	?>
	

	<tr>
		<td align="left" width="100"><?php echo JText::_('TOURNAMENT_MODUS'); ?>:</td>
		<td>
			<?php 
				// Modus
				switch ($this->turnier->typ) {
					case 1:
						$stringModus = JText::_('TOURNAMENT_MODUS_TYP_1');
						break;
					case 2:
						$stringModus = JText::_('TOURNAMENT_MODUS_TYP_2');
						break;
					case 3:
						$stringModus = JText::_('TOURNAMENT_MODUS_TYP_3');
						break;
					case 4:
						$stringModus = JText::_('TOURNAMENT_MODUS_TYP_4');
						break;
					case 5:
						$stringModus = JText::_('TOURNAMENT_MODUS_TYP_5');
						break;
					case 6:
						$stringModus = JText::_('TOURNAMENT_MODUS_TYP_6');
						break;
				}
				echo $stringModus.",&nbsp;";
				
				// Runden
				$stringRunden = '';
				if ($this->turnier->dg > 1) {
					$stringRunden = $this->turnier->dg.' x ';
				}
				$stringRunden .= $this->turnier->runden."&nbsp;".JText::_('TOURNAMENT_ROUNDS');
				if ($this->turnier->rnd == 1) { // ausgelost?
					echo CLMText::createCLMLink($stringRunden, 'turnier_paarungsliste', array('turnier' => $this->turnier->id, 'Itemid' => $itemid));
				} else {
					echo $stringRunden;
				}
			?>
		</td>
	</tr>

	<?php
	if ($this->turnier->tiebr1 > 0 OR $this->turnier->tiebr2 > 0 OR $this->turnier->tiebr3 > 0) {
	?>
		<tr>
			<td align="left" width="100"><?php echo JText::_('TOURNAMENT_TIEBREAKERS'); ?>:</td>
			<td>
				<?php
				$fwStringArray = array();
				for ($f=1; $f<=3; $f++) {
					$fieldName = 'tiebr'.$f;
					if ($this->turnier->$fieldName > 0) {
						$fwStringArray[] = JText::_('TOURNAMENT_TIEBR_'.$this->turnier->$fieldName);
					}
				}
				echo implode(", ", $fwStringArray);
				?>
			</td>
		</tr>
	<?php
	}
	?>
	
	

	<tr>
		<td align="left" width="100"><?php echo JText::_('TOURNAMENT_PARTICIPANTS'); ?>:</td>
		<td>
			<?php 
				// alle Teilnehmer eingetragen
				if ($this->turnier->teil == $this->turnier->playersIn) {
					echo CLMText::createCLMLink($this->turnier->teil.'&nbsp;'.JText::_('TOURNAMENT_PLAYERS'), 'turnier_teilnehmer', array('turnier' => $this->turnier->id, 'Itemid' => $itemid));
				} elseif ($this->turnier->playersIn == 0) { // niemand eingetragen
					echo $this->turnier->teil.'&nbsp;'.JText::_('TOURNAMENT_PLAYERS');
				} else {
					echo $this->turnier->teil.'&nbsp;'.JText::_('TOURNAMENT_PLAYERS').", ".CLMText::createCLMLink($this->turnier->playersIn.'&nbsp;'.JText::_('TOURNAMENT_REGISTERED'), 'turnier_teilnehmer', array('turnier' => $this->turnier->id, 'Itemid' => $itemid));
				}
			?>
		</td>
	</tr>
	
	<?php
	if ($this->turnier->playersTWZ > 0) {
	?>
		<tr>
		
			<td align="left" width="100"><?php echo JText::_('TOURNAMENT_TWZ_AVERAGE'); ?>:</td>
			<td>
				<?php 
				echo $this->turnier->TWZAverage; 
				if ($this->turnier->playersTWZ < $this->turnier->playersIn) { // spieler ohne TWZ?
					echo "&nbsp;(".$this->turnier->playersTWZ.'&nbsp;'.JText::_('TOURNAMENT_PLAYERS').")";
				}
				
				?>
			</td>
		</tr>
	
	<?php
	}
	?>
	
	
	<tr>
		<td align="left" width="100"><?php echo JText::_('TOURNAMENT_DIRECTOR'); ?>:</td>
		<td>
			<?php echo $this->turnier->tlname; ?>
		</td>
	</tr>

	<?php
	if ($this->turnier->invitationLength > 0) {
	?>
		<tr>
			<td align="left" width="100"><?php echo JText::_('TOURNAMENT_INVITATION'); ?>:</td>
			<td>
				<?php 
					echo CLMText::createCLMLink(JText::_('TOURNAMENT_INVITATIONREAD'), 'turnier_invitation', array('turnier' => $this->turnier->id, 'Itemid' => $itemid));
				?>
			</td>
		</tr>
	<?php
	}
	?>
	<?php if ($s_arbiter) { ?>
		<?php if ($aca != '') { ?>
			<tr>
				<td align="left" width="100"><?php echo $lang1->roleACA ?>:</td>
				<td><?php echo $aca; ?></td>
			</tr>
		<?php  } if ($adca != '') { ?>
			<tr>
				<td align="left" width="100"><?php echo $lang1->roleADCA ?>:</td>
				<td><?php echo $adca; ?></td>
			</tr>
		<?php  } if ($apo != '') { ?>
			<tr>
				<td align="left" width="100"><?php echo $lang1->roleAPO ?>:</td>
				<td><?php echo $apo; ?></td>
			</tr>
		<?php  } if ($asa != '') { ?>
			<tr>
				<td align="left" width="100"><?php echo $lang1->roleASA ?>:</td>
				<td><?php echo $asa; ?></td>
			</tr>
		<?php  } if ($aasa != '') { ?>
			<tr>
				<td align="left" width="100"><?php echo $lang1->roleAASA ?>:</td>
				<td><?php echo $aasa; ?></td>
			</tr>
		<?php  } if ($aaca != '') { ?>
			<tr>
				<td align="left" width="100"><?php echo $lang1->roleAACA ?>:</td>
				<td><?php echo $aaca; ?></td>
			</tr>
	<?php  } } ?>
	<?php
	if ($this->turnier->lokal > '' ) {
		$lat = $this->turnier->lokal_coord_lat;
		$lon = $this->turnier->lokal_coord_long;
		if ($this->turnier->lokal_coord == '' OR ($lat == 0 AND $lon == 0)) {
		?>
			<tr>
				<td align="left" width="100"><?php echo JText::_('CLUB_LOCATION'); ?>:</td>
				<td><?php echo $this->turnier->lokal; ?></td>
			</tr>
		<?php } else { ?>
			<tr>
				<td align="left" width="100"><?php echo JText::_('CLUB_LOCATION'); ?>:</td>
				<td>
		<?php	$spiellokal = explode(",", $this->turnier->lokal); 
			if ($spiellokal[0] ==! false ) $loc_text = $spiellokal[0]; else $loc_text = '';
			if (isset($spiellokal[1])) $loc_text .= '<br>'.$spiellokal[1]; 
			if (isset($spiellokal[2])) $loc_text .= '<br>'.$spiellokal[2]; 
			if (isset($spiellokal[3])) $loc_text .= '<br>'.$spiellokal[3]; 
			$img_marker = clm_core::$load->gen_image_url("table/marker-icon");
		?>
	<div style="position:relative;">
		<div id="mapdiv1" class="map" style="position:absolute;top:0;right:0;float:right;width:100%;height:300px;text-align:center;"></div>
		<?php if (($lat != 0 || $lon != 0) && $googlemaps_ver == 3) { ?>
			<div id="madinfo" style="position:absolute;top:0;right:0;float:right;z-index:1000;width:80%;height:200px;text-align:center;"><span style="font-weight: bold; background-color: #FFF"><?php echo $loc_text; ?></span></div>
		<?php } ?>
	</div>
 
	<script language="JavaScript">
		var Lat=<?php printf( '%0.7f', $lat ); ?>;
		var Lon=<?php printf( '%0.7f', $lon ); ?>;
		if (Lat == 0 && Lon == 0) {
		} else {
			<?php if ($googlemaps_ver == 1) {?>
				var popupText = `<?php printf($loc_text); ?>`;
				createLeafletMap(Lat, Lon, popupText, <?php echo $maps_zoom; ?>);
			<?php } ?>
			<?php if ($googlemaps_ver == 3) {?>
				createOSMap(Lat, Lon, `<?php echo $img_marker; ?>`, <?php echo $maps_zoom; ?>);
			<?php } ?>
		}
	</script>
		<div id="mapdiv0" class="map" style="width:100%;height:300px;"></div>																	   
		</td></tr>
		<?php } ?>		
			
		<?php } ?>		

	</table>

	<?php
	// Statistiken
	if ($this->turnier->rnd == 1) { // ausgelost?
	?>

		<table>
	
		<tr>
			<th align="left" colspan="2" class="anfang"><?php echo JText::_('TOURNAMENT_STATS'); ?></th>
		</tr>
	
		<tr>
			<td align="left" width="16%"><?php echo JText::_('TOURNAMENT_MATCHES'); ?>:</td>
			<td>
				<?php 
				echo $this->matchStats['count']; 
				$sum = $this->matchStats['winsW'] + $this->matchStats['remis'] + $this->matchStats['winsB'];
				if ($this->matchStats['played'] < $this->matchStats['count']) {
					echo ",&nbsp;".$this->matchStats['played']."&nbsp;".JText::_('TOURNAMENT_MATCHESPLAYED');
				}
				
				?>
			</td>
		</tr>
	
		<?php
		if ($this->matchStats['played'] > 0) {
		?>
	
			<tr>
				<td align="left" width="100"><?php echo JText::_('TOURNAMENT_WINSW'); ?>:</td>
				<td>
					<?php echo $this->matchStats['winsW']; ?> (<?php echo $this->matchStats['percW'] ?>%)
				</td>
			</tr>
		
			<tr>
				<td align="left" width="100"><?php echo JText::_('TOURNAMENT_REMIS'); ?>:</td>
				<td>
					<?php echo $this->matchStats['remis']; ?> (<?php echo $this->matchStats['percR'] ?>%)
				</td>
			</tr>
			
			<tr>
				<td align="left" width="100"><?php echo JText::_('TOURNAMENT_WINSB'); ?>:</td>
				<td>
					<?php echo $this->matchStats['winsB']; ?> (<?php echo $this->matchStats['percB'] ?>%)
				</td>
			</tr>
			
			<?php 
			if ($this->matchStats['default'] > 0) {
			?>
				<tr>
					<td align="left" width="100"><?php echo JText::_('TOURNAMENT_WINSDEFAULT'); ?>:</td>
					<td>
						<?php echo $this->matchStats['default']; ?>
					</td>
				</tr>
			
			
			<?php
			}
		}
		?>
		
		</table>
	
	<?php
	}
	?>
	
	<br />
	
	<?php

	if ($this->turnier->bemerkungen != '') {
	
		echo CLMText::formatNote($this->turnier->bemerkungen);
	
	}

	// Online Anmeldung
	$today = date("Y-m-d");
 	if (isset($this->turnier->dateRegistration) AND $this->turnier->dateRegistration >= $today)  { // Online Anmeldung vorgesehen?
	?>
		<tr>
			<td align="left" width="100"><?php echo JText::_(''); ?></td>
			<td>
				<?php 
					echo CLMText::createCLMLink(JText::_('REGISTRATION_TOURNAMENT'), 'turnier_registration', array('turnier' => $this->turnier->id, 'Itemid' => $itemid));
				?>
			</td>
		</tr>
	<?php
	}

}
	
require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); 
echo '</div></div>';
?>
