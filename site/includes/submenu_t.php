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
// Konfigurationsparameter auslesen
$config = clm_core::$db->config();
$fe_submenu_t = $config->fe_submenu_t;
?>

<?php // Prüfen, ob active
if ($fe_submenu_t == 1) {
	$document = JFactory::getDocument();
	if ($config->template) {
		$document->addStyleSheet('components/com_clm/includes/submenu.css', 'text/css');
	}
	$itemid = JRequest::getVar('Itemid');
	include (JPATH_COMPONENT . DS . 'models' . DS . 'submenu_t.php');
	require_once (JPATH_COMPONENT . DS . 'includes' . DS . 'submenu_function.php');
	$document->addScript('components/com_clm/javascript/submenu.js');
	// erzeugen des Array für die Anzeige
	$array = array();
	// Informationen
	$array[0][0] = JText::_('TOURNAMENT_INFO');
	if (JRequest::getVar('view', 0) != "turnier_info") {
		$array[0][1] = 0;
	} else {
		$array[0][1] = 1;
	}
	$array[0][2][] = array("option", "com_clm");
	$array[0][2][] = array("view", "turnier_info");
	$array[0][2][] = array("turnier", $this->turnier->id);
	if ($itemid <> '') {
		$array[0][2][] = array("Itemid", $itemid);
	}
        $array[0][3]=array();
	// Tabelle
	$array[1][0] = JText::_('TOURNAMENT_TABLE');
	if (JRequest::getVar('view', 0) != "turnier_tabelle" || JRequest::getVar('spRang', -1) != - 1) {
		$array[1][1] = 0;
	} else {
		$array[1][1] = 1;
	}
	$array[1][2][] = array("option", "com_clm");
	$array[1][2][] = array("view", "turnier_tabelle");
	$array[1][2][] = array("turnier", $this->turnier->id);
	if ($itemid <> '') {
		$array[1][2][] = array("Itemid", $itemid);
	}
        $array[1][3]=array();
	for ($i = 0;$i < count($sub_spRang);$i++) {
		$array[1][3][$i][0] = $sub_spRang[$i]->name . " " . JText::_('TOURNAMENT_TABLE');
		if (JRequest::getVar('view', 0) != "turnier_tabelle" || JRequest::getVar('spRang', -1) != $sub_spRang[$i]->id) {
			$array[1][3][$i][1] = 0;
		} else {
			$array[1][3][$i][1] = 1;
		}
		$array[1][3][$i][2][] = array("option", "com_clm");
		$array[1][3][$i][2][] = array("view", "turnier_tabelle");
		$array[1][3][$i][2][] = array("turnier", $this->turnier->id);
		$array[1][3][$i][2][] = array("spRang", $sub_spRang[$i]->id);
		if ($itemid <> '') {
			$array[1][3][$i][2][] = array("Itemid", $itemid);
		}
	}
	// Rangliste
	$array[2][0] = JText::_('TOURNAMENT_RANKING');
	if (JRequest::getVar('view', 0) != "turnier_rangliste" || JRequest::getVar('spRang', -1) != - 1) {
		$array[2][1] = 0;
	} else {
		$array[2][1] = 1;
	}
	$array[2][2][] = array("option", "com_clm");
	$array[2][2][] = array("view", "turnier_rangliste");
	$array[2][2][] = array("turnier", $this->turnier->id);
	if ($itemid <> '') {
		$array[2][2][] = array("Itemid", $itemid);
	}
        $array[2][3]=array();
	for ($i = 0;$i < count($sub_spRang);$i++) {
		$array[2][3][$i][0] = $sub_spRang[$i]->name . " " . JText::_('TOURNAMENT_RANKING');
		if (JRequest::getVar('view', 0) != "turnier_rangliste" || JRequest::getVar('spRang', -1) != $sub_spRang[$i]->id) {
			$array[2][3][$i][1] = 0;
		} else {
			$array[2][3][$i][1] = 1;
		}
		$array[2][3][$i][2][] = array("option", "com_clm");
		$array[2][3][$i][2][] = array("view", "turnier_rangliste");
		$array[2][3][$i][2][] = array("turnier", $this->turnier->id);
		$array[2][3][$i][2][] = array("spRang", $sub_spRang[$i]->id);
		if ($itemid <> '') {
			$array[2][3][$i][2][] = array("Itemid", $itemid);
		}
	}
	// Teilnehmerliste
	$array[3][0] = JText::_('TOURNAMENT_PARTICIPANTLIST');
	if (JRequest::getVar('view', 0) != "turnier_teilnehmer") {
		$array[3][1] = 0;
	} else {
		$array[3][1] = 1;
	}
	$array[3][2][] = array("option", "com_clm");
	$array[3][2][] = array("view", "turnier_teilnehmer");
	$array[3][2][] = array("turnier", $this->turnier->id);
	if ($itemid <> '') {
		$array[3][2][] = array("Itemid", $itemid);
	}
        $array[3][3]=array();
		$array[3][3][0][0] = JText::_('TOURNAMENT_DWZ');
		if (JRequest::getVar('view', 0) != "turnier_dwz") {
			$array[3][3][0][1] = 0;
		} else {
			$array[3][3][0][1] = 1;
		}
		$array[3][3][0][2][] = array("option", "com_clm");
		$array[3][3][0][2][] = array("view", "turnier_dwz");
		$array[3][3][0][2][] = array("turnier", $this->turnier->id);
		if ($itemid <> '') {
			$array[3][3][0][2][] = array("Itemid", $itemid);
		}

	// Paarungsliste
	$array[4][0] = JText::_('SUBMENU_PAAR');
	if (JRequest::getVar('view', 0) != "turnier_paarungsliste" || JRequest::getVar('spRang', -1) != - 1) {
		$array[4][1] = 0;
	} else {
		$array[4][1] = 1;
	}
	$array[4][2][] = array("option", "com_clm");
	$array[4][2][] = array("view", "turnier_paarungsliste");
	$array[4][2][] = array("turnier", $this->turnier->id);
	if ($itemid <> '') {
		$array[4][2][] = array("Itemid", $itemid);
	}
        $array[4][3]=array();
	for ($i = 0;$i < count($sub_rounds);$i++) {
		$array[4][3][$i][0] = $sub_rounds[$i]->name;
		if (JRequest::getVar('view', 0) != "turnier_runde" || JRequest::getVar('runde', -1) != $sub_rounds[$i]->nr || JRequest::getVar('dg', -1) != $sub_rounds[$i]->dg) {
			$array[4][3][$i][1] = 0;
		} else {
			$array[4][3][$i][1] = 1;
		}
		$array[4][3][$i][2][] = array("option", "com_clm");
		$array[4][3][$i][2][] = array("view", "turnier_runde");
		$array[4][3][$i][2][] = array("turnier", $this->turnier->id);
		$array[4][3][$i][2][] = array("runde", $sub_rounds[$i]->nr);
		$array[4][3][$i][2][] = array("dg", $sub_rounds[$i]->dg);
		if ($itemid <> '') {
			$array[4][3][$i][2][] = array("Itemid", $itemid);
		}
	}
	echo clm_submenu($array);
}
 
