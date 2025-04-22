<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT . DS . 'includes' . DS . 'clm_tooltip.php');

// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');

//echo "<br>vtt-teams:"; var_dump($this->a_teams);

// Konfigurationsparameter auslesen
$itemid 	= clm_core::$load->request_int('Itemid');
$spRang		= clm_core::$load->request_int('spRang');	//Sonderranglisten
$option 	= clm_core::$load->request_string('option', 'com_clm');
$mainframe	= JFactory::getApplication();

$config = clm_core::$db->config();
// $pdf_melde = $config->pdf_meldelisten;
$fixth_ttab = $config->fixth_ttab;

// CLM-Container
echo '<div id="clm"><div id="turnier_tabelle">';

// Componentheading
if ($spRang != 0) {			//Sonderranglisten
    $heading = $this->turnier->name.": ".$this->turnier->spRangName." ".JText::_('TOURNAMENT_TABLE');
} else {
    $heading = $this->turnier->name.": ".JText::_('TOURNAMENT_TEAM');
}

$archive_check = clm_core::$api->db_check_season_user($this->turnier->sid);
if (!$archive_check) {
    echo CLMContent::componentheading($heading);
    require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
    echo CLMContent::clmWarning(JText::_('NO_ACCESS')."<br/>".JText::_('NOT_REGISTERED'));
} elseif ($this->turnier->published == 0) {
    echo CLMContent::componentheading($heading);
    echo CLMContent::clmWarning(JText::_('TOURNAMENT_NOTPUBLISHED')."<br/>".JText::_('TOURNAMENT_PATIENCE'));
} elseif ($spRang == 0 and $this->turnier->playersCount < $this->turnier->teil) { //�nderung wegen Sonderranglisten
    $msg = JText::_('TOURNAMENT_PLAYERLISTNOTCOMPLETE')."<br/>".JText::_('TOURNAMENT_NORANKINGEXISTING');
    $link = 'index.php?option='.$option.'&view=turnier_teilnehmer&turnier='.$this->turnier->id;
    if ($itemid != 0) {
        $link .= '&Itemid='.$itemid;
    }
    $mainframe->enqueueMessage($msg);
    $mainframe->redirect($link);

} elseif ($this->turnier->typ == 3) { // KO-System
    echo CLMContent::componentheading($heading);
    require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
    echo CLMContent::clmWarning(JText::_('TOURNAMENT_TABLENOTAVAILABLE'));
} elseif ($spRang != 0 and $this->turnier->playersCount == 0) { //Hinzugef�gt wegen Sonderranglisten
    echo CLMContent::componentheading($heading);
    require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
    echo CLMContent::clmWarning(JText::_('TOURNAMENT_SPECIALRANKING_NOPLAYERS'));

} else {
    // PDF-Link
    /*	echo CLMContent::createPDFLink('turnier_tabelle', JText::_('TOURNAMENT_TABLE'), array('turnier' => $this->turnier->id, 'layout' => 'tabelle', 'spRang' => $spRang) );
        if($spRang != 0){			//Sonderranglisten
          echo CLMContent::createViewLink('turnier_rangliste', JText::_('TABELLE_GOTO_RANGLISTE'), array('turnier' => $this->turnier->id, 'spRang' => $spRang, 'Itemid' => $itemid) );
        } else {
          echo CLMContent::createViewLink('turnier_rangliste', JText::_('TABELLE_GOTO_RANGLISTE'), array('turnier' => $this->turnier->id, 'Itemid' => $itemid) );
        }
    */   echo CLMContent::componentheading($heading);
    require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');

    $turParams = new clm_class_params($this->turnier->params);

    // Table
    echo '<table cellpadding="0" cellspacing="0" id="turnier_tabelle"';
    if ($fixth_ttab == "1") {
        echo 'class="tableWithFloatingHeader"';
    };

    // header
    echo '><tr>';
    echo '<th class="rang">'.JText::_('TOURNAMENT_RANKABB').'</th>';
    echo '<th class="name_float">'.JText::_('TOURNAMENT_TEAMNAME').'</th>';
    echo '<th class="fw_col">'.JText::_('TOURNAMENT_POINTS_ABB').'</th>';
    echo '<tr />';

    // alle Teams durchgehen
    $pc = 0;
    for ($p = 0; $p < $this->turnier->teamsCount; $p++) {
        //if ($this->players[$p]->anz_spiele == 0) continue;
        $pc++;
        if ($pc % 2 != 0) {
            $zeilenr = "zeile1";
        } else {
            $zeilenr = "zeile2";
        }

        echo '<tr class="'.$zeilenr.'">';
        echo '<td class="rang">'.$pc.'</td>';

        echo '<td class="verein">';
        $link = new CLMcLink();
        $link->view = 'turnier_team';
        $link->more = array('turnier' => $this->turnier->id, 'tln_nr' => $this->a_teams[$p]->tln_nr, 'Itemid' => $itemid );
        $link->makeURL();
        echo $link->makeLink($this->a_teams[$p]->name);
        echo '</td>';
        echo '<td class="fw_col">'.$this->a_teams[$p]->points.'</td>';
        echo '<tr />';
    }
    // ende alle Teams



    echo '</table>';


}

require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php');
echo '</div></div>';
