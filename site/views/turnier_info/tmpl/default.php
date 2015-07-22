<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access');

// Stylesheet laden

require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');


echo "<div><div id='turnier_info'>";

// Konfigurationsparameter auslesen
$itemid 		= JRequest::getVar( 'Itemid' );

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
	
	?>

	<table>
	
	<tr>
		<th align="left" colspan="2" class="anfang"><?php echo JText::_('TOURNAMENT_DATA'); ?></th>
	</tr>
	
	<tr>
		<td align="left" width="100"><?php echo JText::_('SEASON') ?>:</td>
		<td><?php echo $this->turnier->saisonname ?></td>
	</tr>

	<?php
	if ($this->turnier->dateStart != '0000-00-00') {
	?>

		<tr>
			<td align="left" width="100">
				<?php
				if ($this->turnier->dateEnd != '0000-00-00') {
					echo JText::_('TOURNAMENT_TIMEFRAME');
				} else {
					echo JText::_('TOURNAMENT_DATE');
				}
				?>
			</td>
			<td>
				<?php 
				echo JHTML::_( 'date', $this->turnier->dateStart, JText::_('DATE_FORMAT_LC3')); 
				if ($this->turnier->dateEnd != '0000-00-00') {
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
			<td align="left" width="100"><?php echo JText::_('TOURNAMENT_MATCHES'); ?>:</td>
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

}

	
require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); 
echo '</div></div>';
?>
