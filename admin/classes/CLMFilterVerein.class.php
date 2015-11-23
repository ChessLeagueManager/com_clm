<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Filter ist zu finden in folgenden Modulen :
//
// Mannschaften (mannschaften) 3*gesamt : *1 normal 2*modifiziert
// Benutzer (users)
// Mitgliederverwaltung (dwz)
// Vereine (vereine)
// ranglisten
// swt ******
// turnier_teilnehmer



class CLMFilterVerein
{

public static function vereine_filter($override)
	{
	$db =JFactory::getDBO();

	// Konfigurationsparameter auslesen
	$config = clm_core::$db->config();
	$lv	= $config->lv;
	$vl	= $config->vereineliste;
	$vs	= $config->verein_sort;
	$countryversion= $config->countryversion;
	$dat	= substr($lv, 1);
	$dat2	= substr($lv, 2);

	// 1 = Auswahl DB obwohl manuell aktiviert wurde ! (z.B. Vereine anlegen !!!)
	if ($override == 1) {$vl = 0;}

	// Vereinefilter
	// 0 = DB ; 1 = manuell
	if ($vl =="0"){
		$sid = clm_core::$access->getSeason();

	// 0 = deutsche Version
	// 1 = niederländische Version
	// 2 = englische Version
	/*
	if($version =="de"){
		if($dat == "00"){
		$ug = (substr($lv, 0, 1)).'0000';
		$og = (substr($lv, 0, 1)).'F999';
				}
		if($dat2 =="0" AND $dat !="00") {
		$ug = (substr($lv, 0, 2)).'000';
		$og = (substr($lv, 0, 2)).'999';
				}
		if($dat2 !="0" AND $dat !="00") {
		$ug =$lv.'00';
		$og =$lv.'99';
		}
		}
	*/
	/*
	if($version =="1"){
		if($lv=="00"){
		$ug =$lv;
		$og ="99";
		} else {
		$ug =$lv;
		$og =$lv;
		}
		}
	*/
		if($countryversion =="de") {
			$out = clm_core::$load->unit_range($lv);
			$sql = "SELECT ZPS as zps, Vereinname as name FROM #__clm_dwz_vereine as a "
				." LEFT JOIN #__clm_saison as s ON s.id= a.sid "
				." WHERE a.Verband >= '$out[0]' AND a.Verband <= '$out[1]' "
				." AND s.archiv = 0 AND s.published = 1 ORDER BY ";
			if($vs =="1") { $sql =$sql."a.ZPS ASC";}
			else {  $sql =$sql." a.Vereinname ASC";}
		}
		if($countryversion =="en") {				// nur für Chess Association geeignet
			$sql = "SELECT ZPS as zps, Vereinname as name FROM #__clm_dwz_vereine as a "
				." LEFT JOIN #__clm_saison as s ON s.id= a.sid "
				." WHERE a.Verband = '$lv' "
				." AND s.archiv = 0 AND s.published = 1 ORDER BY ";
			if($vs =="1") { $sql =$sql."a.ZPS ASC";}
			else {  $sql =$sql." a.Vereinname ASC";}
		}
	/*
	if($version =="2"){
		$sql = " SELECT ZPS FROM #__clm_dwz_vereine WHERE LV ='".$lv."'";
		$db->setQuery($sql);
		$engl_vereine = $db->loadObjectList();

		for ($x=0; $x < count($engl_vereine); $x++) {
			$zps_engl = $zps_engl."'".$engl_vereine[$x]->ZPS."'";
			if($x<(count($engl_vereine))-1) { $zps_engl = $zps_engl.','; }
			}
		$sql = "SELECT ZPS as zps, Vereinname as name FROM #__clm_dwz_vereine as a "
			." LEFT JOIN #__clm_saison as s ON s.id= a.sid "
			." WHERE a.ZPS IN ($zps_engl) "
			." AND s.archiv = 0 AND s.published = 1 ORDER BY ";
				if($vs =="1") { $sql =$sql."a.ZPS ASC";}
				else {  $sql =$sql." a.Vereinname ASC";}

		}
	*/
	} else {
	$sql = 'SELECT a.zps, a.name FROM #__clm_vereine as a'
		.' LEFT JOIN #__clm_saison AS s ON s.id = a.sid'
		." WHERE s.archiv = 0";
	}
	$db->setQuery($sql);
	$vereine = $db->loadObjectList();

	// Hinweis setzen wenn Filter leer !
	if (count($vereine) == 0 AND $vl == 1) {
	JError::raiseWarning( 500,  JText::_( 'FILTER_VLISTE'));
	JError::raiseNotice( 6000,  JText::_( 'FILTER_KEIN_VEREIN'));
	}
	if (count($vereine) == 0 AND $vl == 0) {
	JError::raiseWarning( 500,  JText::_( 'FILTER_VLISTE'));
	JError::raiseNotice( 6000,  JText::_( 'FILTER_URSACHE'));
	}

	$vlist[]	= JHtml::_('select.option',  '0', JText::_( 'FILTER_VEREIN' ), 'zps', 'name' );
	$vlist		= array_merge( $vlist, $vereine);
	return $vlist;
	}
}
