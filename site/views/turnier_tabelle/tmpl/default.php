<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip', '.CLMTooltip');

// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');


// Konfigurationsparameter auslesen
$itemid 		= JRequest::getVar( 'Itemid' );
$spRang		= JRequest::getVar( 'spRang' ,0);	//Sonderranglisten

// $turnierid		= JRequest::getInt('turnier','1');
$config = clm_core::$db->config();
// $pdf_melde = $config->pdf_meldelisten;
$fixth_ttab = $config->fixth_ttab;
	
// CLM-Container
echo '<div ><div id="turnier_tabelle">';
	
// Componentheading
if($spRang != 0){			//Sonderranglisten
	$heading = $this->turnier->name.": ".$this->turnier->spRangName." ".JText::_('TOURNAMENT_TABLE'); 
} else {
	$heading = $this->turnier->name.": ".JText::_('TOURNAMENT_TABLE');
}

if ( $this->turnier->published == 0) { 
	echo CLMContent::componentheading($heading);
	echo CLMContent::clmWarning(JText::_('TOURNAMENT_NOTPUBLISHED')."<br/>".JText::_('TOURNAMENT_PATIENCE'));
} elseif ($spRang == 0 and $this->turnier->playersCount < $this->turnier->teil) { //�nderung wegen Sonderranglisten
	echo CLMContent::componentheading($heading);
   	require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
	echo CLMContent::clmWarning(JText::_('TOURNAMENT_PLAYERLISTNOTCOMPLETE')."<br/>".JText::_('TOURNAMENT_NORANKINGEXISTING'));

} elseif($this->turnier->typ == 3) { // KO-System
	echo CLMContent::componentheading($heading);
   	require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
	echo CLMContent::clmWarning(JText::_('TOURNAMENT_TABLENOTAVAILABLE'));
} elseif ($spRang != 0 and $this->turnier->playersCount == 0 ) { //Hinzugef�gt wegen Sonderranglisten
	echo CLMContent::componentheading($heading);
   	require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
	echo CLMContent::clmWarning(JText::_('TOURNAMENT_SPECIALRANKING_NOPLAYERS'));

} else {
// PDF-Link
	echo CLMContent::createPDFLink('turnier_tabelle', JText::_('TOURNAMENT_TABLE'), array('turnier' => $this->turnier->id, 'layout' => 'tabelle', 'spRang' => $spRang) );
	if($spRang != 0){			//Sonderranglisten
	  echo CLMContent::createViewLink('turnier_rangliste', JText::_('TABELLE_GOTO_RANGLISTE'), array('turnier' => $this->turnier->id, 'spRang' => $spRang, 'Itemid' => $itemid) );
	} else {
	  echo CLMContent::createViewLink('turnier_rangliste', JText::_('TABELLE_GOTO_RANGLISTE'), array('turnier' => $this->turnier->id, 'Itemid' => $itemid) );
	}
   echo CLMContent::componentheading($heading);
   require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');

	$turParams = new clm_class_params($this->turnier->params);

	// Table
	echo '<table cellpadding="0" cellspacing="0" id="turnier_tabelle"';
	if ($fixth_ttab =="1") { echo 'class="tableWithFloatingHeader"'; };

		// header
		echo '><tr>';
			echo '<th class="rang">'.JText::_('TOURNAMENT_RANKABB').'</th>';
			if ($turParams->get('displayPlayerTitle', 1) == 1) {
				echo '<th class="titel">'.JText::_('TOURNAMENT_TITLE').'</th>';
			}
			echo '<th class="name_float">'.JText::_('TOURNAMENT_PLAYERNAME').'</th>';
			if ($turParams->get('displayPlayerClub', 1) == 1) {
				echo '<th class="verein">'.JText::_('TOURNAMENT_CLUB').'</th>';
			}
			echo '<th class="twz">'.JText::_('TOURNAMENT_TWZ').'</th>';
			echo '<th class="fw_col">'.JText::_('TOURNAMENT_GAMES_ABB').'</th>';
			echo '<th class="fw_col">'.JText::_('TOURNAMENT_POINTS_ABB').'</th>';
			// mgl. Feinwertungen
			for ($f=1; $f<=3; $f++) {
				$fwFieldName = 'tiebr'.$f;
				if ($this->turnier->$fwFieldName > 0 AND $this->turnier->$fwFieldName < 50) {
					echo '<th class="fw_col">'.JText::_('TOURNAMENT_TIEBR_ABB_'.$this->turnier->$fwFieldName).'</th>';
				}
			}
		echo '<tr />';
		
		// alle Spieler durchgehen
		$pc = 0;
		for ($p=0; $p<$this->turnier->playersCount; $p++) {
			//if ($this->players[$p]->anz_spiele == 0) continue;
			$pc++;
			if ($pc%2 != 0) { 
				$zeilenr = "zeile1"; 
			} else { 
				$zeilenr = "zeile2"; 
			}

			echo '<tr class="'.$zeilenr.'">';
				echo '<td class="rang">'.CLMText::getPosString($this->players[$p]->rankingPos).'</td>';
				
				if ($turParams->get('displayPlayerTitle', 1) == 1) {
					echo '<td align="center" class="name_float">'.$this->players[$p]->titel.'</td>';
				}
				echo '<td class="verein">';
					$link = new CLMcLink();
					$link->view = 'turnier_player';
					$link->more = array('turnier' => $this->turnier->id, 'snr' => $this->players[$p]->snr, 'Itemid' => $itemid );
					$link->makeURL();
					echo $link->makeLink($this->players[$p]->name);
				echo '</td>';
				if ($turParams->get('displayPlayerClub', 1) == 1) {
					if ($this->tourn_linkclub == 1) {
						$link = new CLMcLink();
						$link->view = 'verein';
						$link->more = array('saison' => $this->players[$p]->sid, 'zps' => $this->players[$p]->zps, 'Itemid' => $itemid );
						$link->makeURL();
						echo '<td class="name_float">'.$link->makeLink($this->players[$p]->verein).'</td>';
					} else {
						echo '<td class="name_float">'.$this->players[$p]->verein.'</td>';
					}
				}
				echo '<td class="twz">'.CLMText::formatRating($this->players[$p]->twz).'</td>';
				echo '<td class="fw_col">'.$this->players[$p]->anz_spiele.'</td>';
				echo '<td class="fw_col">'.$this->players[$p]->sum_punkte.'</td>';
				// mgl. Feinwertungen
				for ($f=1; $f<=3; $f++) {
					$fwFieldName = 'tiebr'.$f;
					$plTiebrField = 'sumTiebr'.$f;
					if ($this->turnier->$fwFieldName > 0 AND $this->turnier->$fwFieldName < 50) {
						echo '<td class="fw_col">'.CLMtext::tiebrFormat($this->turnier->$fwFieldName, $this->players[$p]->$plTiebrField).'</td>';
					}
				}
			echo '<tr />';
		}
		// ende alle Spieler



	echo '</table>';
		

}

require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); 
echo '</div></div>';
?>
