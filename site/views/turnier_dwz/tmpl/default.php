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
$heading =  $this->turnier->name.": ".JText::_('TOURNAMENT_DWZ');

$params = new clm_class_params($this->turnier->params);
$inofDWZ = $params->get("inofDWZ","0");
$dwz_date = $params->get("dwz_date","0000-00-00");

if ( $this->turnier->published == 0) { 
   echo CLMContent::componentheading($heading);
	require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
	echo CLMContent::clmWarning(JText::_('TOURNAMENT_NOTPUBLISHED')."<br/>".JText::_('TOURNAMENT_PATIENCE'));

} elseif (count($this->players) == 0) {
    echo CLMContent::componentheading($heading);
	require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
	echo CLMContent::clmWarning(JText::_('TOURNAMENT_NOPLAYERSREGISTERED'));

} elseif ($inofDWZ == 0) {
   echo CLMContent::componentheading($heading);
	require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
	echo CLMContent::clmWarning(JText::_('TOURNAMENT_DWZ_NO'));
	
} else {

// PDF-Link
echo CLMContent::createPDFLink('turnier_dwz', JText::_('TOURNAMENT_DWZ'), array('turnier' => $this->turnier->id, 'layout' => 'dwz'));
    echo CLMContent::componentheading($heading);
	require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
	$turParams = new JRegistry();
	$turParams->loadString($this->turnier->params);

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
		if ($turParams->get('displayPlayerClub', 1) == 2) {
		?>
			<th class="tt_col_4"><?php echo JText::_('TOURNAMENT_CLUB'); ?></th>
		<?php
		}
		?>		
		
		<th class="tt_col_7"><?php echo JText::_('TOURNAMENT_RATING'); ?></th>
		<th class="tt_col_7"><?php echo JText::_('DWZ_W'); ?></th>
		<th class="tt_col_7"><?php echo JText::_('DWZ_WE'); ?></th>
		<th class="tt_col_7"><?php echo JText::_('DWZ_EF'); ?></th>
		<th class="tt_col_7"><?php echo JText::_('DWZ_RATING'); ?></th>
		<th class="tt_col_7"><?php echo JText::_('DWZ_LEVEL'); ?></th>
		<th class="tt_col_7"><?php echo JText::_('DWZ_POINTS'); ?></th>
		<th class="tt_col_7"><?php echo JText::_('DWZ_NEW'); ?></th>
		<th class="tt_col_7"><?php echo JText::_('DWZ_DIFF'); ?></th>
	
	
	
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
			if ($turParams->get('displayPlayerClub', 1) == 2) {
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
			
			// start_dwz
			echo '<td class="tt_col_7">'.CLMText::formatRating($value->start_dwz).'</td>';
			echo '<td class="tt_col_7">'.$value->Punkte.'</td>';
			echo '<td class="tt_col_7">'.CLMText::formatRating($value->We).'</td>';
			echo '<td class="tt_col_7">'.CLMText::formatRating($value->EFaktor).'</td>';
			echo '<td class="tt_col_7">'.CLMText::formatRating($value->Leistung).'</td>';
			echo '<td class="tt_col_7">'.CLMText::formatRating($value->Niveau).'</td>';
			echo '<td class="tt_col_7">';
			$Pkt = explode (".", $value->Punkte);
			if ($Pkt[1] != "0") {
				if ($Pkt[0] != "0") { 
					echo $Pkt[0].'&frac12;  /  '.$value->Partien.'</td>';
				} else { 
					echo '&frac12;  /  '.$value->Partien.'</td>';
				}
			} else { 
				echo $Pkt[0].'  /  '.$value->Partien.'</td>';
			} 
  			//echo '<td class="tt_col_7">'.CLMText::formatRating($value->Partien).'</td>';
			echo '<td class="tt_col_7">'.CLMText::formatRating($value->DWZ).'</td>';
			if ($value->DWZ == 0 OR $value->start_dwz == 0) echo '<td class="tt_col_7">'.CLMText::formatRating(0).'</td>';
			else echo '<td class="tt_col_7">'.($value->DWZ - $value->start_dwz).'</td>';
			
			
			?>
		
		</tr>

		<?php
	}

	echo '</table>';

	if($dwz_date == "0000-00-00") { 
		echo '<div class="hint">'.JText::_('TOURNAMENT_DWZ_DATE_NO').'</div><br/>';
	} else {
		echo '<div class="hint">'.JText::_('TOURNAMENT_DWZ_DATE').clm_core::$load->date_to_string($dwz_date,false,false).'.</div><br/>';
	}
}

require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); 
echo '</div></div>';

 
?>
