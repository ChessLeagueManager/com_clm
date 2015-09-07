<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2014 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access');
//JHtml::_('behavior.tooltip', '.CLMTooltip', $params);
JHtml::_('behavior.tooltip', '.CLMTooltip');

// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');


// Konfigurationsparameter auslesen
$itemid 		= JRequest::getVar( 'Itemid' );
// $turnierid		= JRequest::getInt('turnier','1');
$config			= clm_core::$db->config();
// $pdf_melde 		= $config->pdf_meldelisten;
$fixth_ttln		= $config->fixth_ttln;

// CLM-Container
echo "<div id='clm'><div id='turnier_teilnehmer'>";

// Componentheading
$heading =  $this->turnier->name.": ".JText::_('TOURNAMENT_PARTICIPANTLIST');

if ( $this->turnier->published == 0) { 
    echo CLMContent::componentheading($heading);
	echo CLMContent::clmWarning(JText::_('TOURNAMENT_NOTPUBLISHED')."<br/>".JText::_('TOURNAMENT_PATIENCE'));

} elseif (count($this->players) == 0) {
    echo CLMContent::componentheading($heading);
	require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
	echo CLMContent::clmWarning(JText::_('TOURNAMENT_NOPLAYERSREGISTERED'));

	
} else {

// PDF-Link
echo CLMContent::createPDFLink('turnier_teilnehmer', JText::_('TOURNAMENT_PARTICIPANTLIST'), array('turnier' => $this->turnier->id, 'layout' => 'teilnehmer'));
    echo CLMContent::componentheading($heading);
	require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
	$turParams = new clm_class_params($this->turnier->params);

	?>

	<table cellpadding="0" cellspacing="0" id="turnier_teilnehmer" <?php if ($fixth_ttln =="1") { ?>class="tableWithFloatingHeader"<?php } ?>>

	<tr>
		
		<th class="tt_col_1"><?php echo JText::_('TOURNAMENT_NUMBERABB'); ?></th>
		
		<?php
		if ($turParams->get('displayPlayerTitle', 1) == 1) {
		?>
			<th class="tt_col_2"><?php echo JText::_('TOURNAMENT_TITLE'); ?></th>
		<?php
		}
		?>
		
		<th class="tt_col_3"><?php echo JText::_('TOURNAMENT_PLAYERNAME'); ?></th>
		
		<?php
		if ($turParams->get('displayPlayerClub', 1) == 1) {
		?>
			<th class="tt_col_4"><?php echo JText::_('TOURNAMENT_CLUB'); ?></th>
		<?php
		}
		?>
		
		<?php
		if ($turParams->get('displayPlayerFederation', 0) == 1) {
		?>
			<th class="tt_col_5"><?php echo JText::_('TOURNAMENT_FEDERATION'); ?></th>
		<?php
		}
		?>
		
		
		<th class="tt_col_6"><?php echo JText::_('TOURNAMENT_TWZ'); ?></th>
	
		<?php
		if ($turParams->get('displayPlayerRating', 0) == 1) {
		?>
			<th class="tt_col_7"><?php echo JText::_('TOURNAMENT_RATING'); ?></th>
		<?php
		}
		?>
	
		<?php
		if ($turParams->get('displayPlayerElo', 0) == 1) {
		?>
			<th class="tt_col_8"><?php echo JText::_('TOURNAMENT_ELO'); ?></th>
		<?php
		}
		?>
	
	
	</tr>
	
	<?php

	$p=0;

	foreach ($this->players as $key => $value) {

		$p++; // rowCount

		// Farbe anpassen

		if ($p%2 != 0) { 

			$zeilenr = "zeile1"; 

		} else { 

			$zeilenr = "zeile2"; 

		}

		?>

		<tr class="<?php echo $zeilenr; ?>">
			
			<td class="tt_col_1"><?php echo $value->snr; ?></td>
			
			<?php
				
			// Title
			if ($turParams->get('displayPlayerTitle', 1) == 1) {
				echo '<td class="tt_col_2">'.$value->titel.'</td>';
			}
				
			$link = new CLMcLink();
			$link->view = 'turnier_player';
			$link->more = array('turnier' => $this->turnier->id, 'snr' => $value->snr, 'Itemid' => $itemid );
			$link->makeURL();
			
			// Name
			echo '<td class="tt_col_3">'.$link->makeLink($value->name). '</td>';
			
			// Club
			if ($turParams->get('displayPlayerClub', 1) == 1) {
				if ($this->tourn_linkclub == 1) {
					$link = new CLMcLink();
					$link->view = 'verein';
					$link->more = array('saison' => $value->sid, 'zps' => $value->zps, 'Itemid' => $itemid );
					$link->makeURL();
					echo '<td class="tt_col_4">'.$link->makeLink($value->verein).'</td>';
				} else {
					echo '<td class="tt_col_4">'.$value->verein.'</td>';
				}
			}
			
			// Federation
			if ($turParams->get('displayPlayerFederation', 0) == 1) {
				echo '<td class="tt_col_5">'.$value->FIDEcco.'</td>';
			}
			
			// TWZ
			echo '<td class="tt_col_6">'.CLMText::formatRating($value->twz).'</td>';
			
			// start_dwz
			if ($turParams->get('displayPlayerRating', 0) == 1) {
				echo '<td class="tt_col_7">';
					echo CLMText::formatRating($value->start_dwz);
				echo '</td>';
			}
			
			// FIDEelo
			if ($turParams->get('displayPlayerElo', 0) == 1) {
				echo '<td class="tt_col_8">';
					echo CLMText::formatRating($value->FIDEelo);
				echo '</td>';
			}
			
			?>
		
		</tr>

		<?php
	}

	echo '</table>';

}

require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); 
echo '</div></div>';

 
?>
