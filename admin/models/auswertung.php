<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class CLMModelAuswertung extends JModel {

	var $_swtFiles;

function __construct(){
		parent::__construct();
	}


function datei() {

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	$db	= &JFactory::getDBO();
	$app	= JFactory::getApplication();
	$option	= JRequest::getCmd('option');
	$jinput = $app->input;
	$liga	= $jinput->get('filter_lid', null, null);
	$mt	= $jinput->get('filter_mt', null, null);
	$et	= $jinput->get('filter_et', null, null);
	$format	= $jinput->get('filter_format', null, null);
	$sid	= CLM_SEASON;

	// Link zum redirect generieren
	$adminLink = new AdminLink();
	$adminLink->view = "auswertung";
	$adminLink->makeURL();

	// Dateinamen zusammensetzen
	$date	=& JFactory::getDate();
	$now	= $date->toMySQL();
	$datum	= JHTML::_('date',  $now, JText::_('d-m-Y__H-i-s'));

	// Grunddaten für Ligen und Mannschaftsturniere laden
	if($liga !="0" OR $mt !="0"){
		// Mannschaftsturnier eine Liga ID zuweisen
		if($mt !="0"){ $liga = $mt;}
		
		$sql = " SELECT a.* FROM #__clm_liga as a"
			." LEFT JOIN #__clm_saison as s ON s.id = a.sid"
			." WHERE s.archiv = 0 AND a.id = ".$liga
			;
		$db->setQuery($sql);
		$liga_name = $db->loadObjectList();

		$anzahl_runden = (($liga_name[0]->runden)*($liga_name[0]->durchgang));
		
		// Zeit der letzten Runde
		$sql = " SELECT a.* FROM #__clm_runden_termine as a"
			." LEFT JOIN #__clm_saison as s ON s.id = a.sid"
			." WHERE s.archiv = 0 AND a.liga = ".$liga
			." ORDER BY a.nr DESC LIMIT 1 "
			;
		$db->setQuery($sql);
		$liga_date	= $db->loadObjectList();
		$end_date	= JHTML::_('date',  $liga_date[0]->datum, JText::_('Y-m-d'));
		
		// Vollständigkeit prüfen für Ligen und Mannschaftsturniere
		// 1.	Erwartete Anzahl von Einzelergebnissen auf Basis Anzahl spielfreie Mannschaften
		$sql = "SELECT COUNT(tln_nr) AS count FROM #__clm_mannschaften "
			." WHERE liga = ".$liga_name[0]->id
			." AND sid = ".$sid
			." AND man_nr = 0"
			;
		$db->setQuery($sql);
		$spielfrei = $db->loadObject();
		if (isset($spielfrei)) $count = $spielfrei->count;
		else $count = 0;
		if ($liga_name[0]->runden_modus == 1 OR $liga_name[0]->runden_modus == 2) {
			$counter= intval(($liga_name[0]->teil - $count)/2)*$liga_name[0]->stamm;
		} elseif ($liga_name[0]->runden_modus == 3) {
			$counter= intval(($liga_name[0]->teil - $count)/2)*$liga_name[0]->stamm;
		} else {
			$counter= 0;
		}
 
		// 2.	Einzelergebnisse pro Durchgang/Runde
		for ($dg = 1; $dg <= $liga_name[0]->durchgang; $dg++) { 
		  for ($rnd = 1; $rnd <= $liga_name[0]->runden; $rnd++) { 
			$sql = " SELECT ee.runde, ee.dg, COUNT(*) AS cnt_runde FROM `#__clm_rnd_spl` as ee"
				." LEFT JOIN #__clm_rnd_man as me ON me.lid = ee.lid AND me.runde = ee.runde AND me.dg = ee.dg AND me.tln_nr = ee.tln_nr"		
				." LEFT JOIN #__clm_mannschaften as m ON m.liga = me.lid AND m.tln_nr = me.gegner"		
				." WHERE ee.sid = ".$sid
				." AND ee.lid = ".$liga_name[0]->id
				." AND ee.dg = ".$dg
				." AND ee.runde = ".$rnd
				." AND ee.heim = 1 "
				." AND m.man_nr > 0 "
				." GROUP BY ee.dg, ee.runde "
				//." ORDER BY dg ASC, runde ASC, paar ASC "
				." ORDER BY ee.dg ASC, ee.runde ASC "
				;
			$db->setQuery($sql);
			$rnd_proof =$db->loadObjectList();
			if (isset($rnd_proof[0])) $rnd_count = $rnd_proof[0]->cnt_runde;
			else $rnd_count = 0;
			$fehler	= 0;
			if($rnd_count < $counter){
				$app->enqueueMessage( 'Warnung in Runde '.$rnd.', Durchgang '.$dg.'. Vermutlich existieren keine Ergebnisse !', 'warning');
				$fehler = 1;
			}
		  }
		}
	}
	
	// Grunddaten für Einzelturniere laden
	if($et !="0"){
		$liga = $et;
		$sql = " SELECT a.* FROM #__clm_turniere as a"
			." LEFT JOIN #__clm_saison as s ON s.id = a.sid"
			." WHERE s.archiv = 0 AND a.id = ".$liga
			;
		$db->setQuery($sql);
		$liga_name = $db->loadObjectList();

		$end_date	= JHTML::_('date',  $liga_name[0]->dateEnd, JText::_('Y-m-d'));
		$anzahl_runden	= ($liga_name[0]->runden)*($liga_name[0]->dg);
	}
	
	// Unterscheidung Einzel- und Mannschaftsturnier mit verschiedenen Ausgabemodi
	if($et !="0") {
		$format = 2;  // Nur XML für Einzelturniere
		$typ	= $liga_name[0]->typ;
		if($typ =="1"){ $turnier_typ = 'SR'; } // SR: Einzelturnier; jeder gegen jeden
		if($typ =="2"){ $turnier_typ = 'SR'; }
		if($typ =="3"){ $turnier_typ = 'SW'; } // SW: Einzelturnier; Schweizer System
		if($typ =="4"){ $turnier_typ = 'SC'; } 
		if($typ =="5"){ $turnier_typ = 'SC'; } // SC: Einzelturnier; K.O. System (Pokal)
	}
	if($liga !="0") {
		if($mt !="0"){ $format	= 2; } // Nur XML für Mannschaftsturniere. KEINE LIGA !
		
		$typ	= $liga_name[0]->runden_modus;
		if($typ =="1"){ $turnier_typ = 'TR'; } // TR: Mannschaftsturnier; jeder gegen jeden
		if($typ =="2"){ $turnier_typ = 'TR'; }
		if($typ =="3"){ $turnier_typ = 'TW'; } // TW: Mannschaftsturnier: Schweizer System
		if($typ =="4"){ $turnier_typ = 'TC'; }
		if($typ =="5"){ $turnier_typ = 'TC'; } // TC: Mannschaftsturnier: K.O.-System (Pokal)
	}

	////////////////
	// DSB Format //
	////////////////
	if($format =="1"){
	
	$fill[0] ="";
	$fill[1] =" ";
	$fill[2] ="  ";
	$fill[3] ="   ";
	$fill[4] ="    ";
	$fill[5] ="     ";
	$fill[6] ="      ";
	$fill[7] ="       ";
	$fill[8] ="        ";
	$fill[9] ="         ";
	$fill[10]="          ";
	$fill[11]="           ";
	$fill[12]="            ";
	$fill[13]="             ";
	$fill[14]="              ";
	$fill[15]="               ";
	$fill[16]="                ";
	$fill[17]="                 ";
	$fill[18]="                  ";
	$fill[19]="                   ";
	$fill[20]="                    ";
	$fill[21]="                     ";
	$fill[22]="                      ";
	$fill[23]="                       ";
	$fill[24]="                        ";
	$fill[25]="                         ";
	$fill[26]="                          ";
	$fill[27]="                           ";
	$fill[28]="                            ";
	$fill[29]="                             ";
	$fill[30]="                              ";
	$fill[31]="                               ";
	$fill[32]="                                ";
		
	$sql = " SELECT a.*,v.Vereinname,s.* FROM `#__clm_rnd_spl` as a "
		." LEFT JOIN #__clm_dwz_spieler as s ON s.sid = a.sid AND s.ZPS = a.zps AND s.Mgl_Nr = a.spieler "
		." LEFT JOIN #__clm_dwz_vereine as v ON v.sid = a.sid AND v.ZPS = a.zps "
		." WHERE a.sid = ".$sid
		." AND a.lid = ".$liga_name[0]->id
		." GROUP BY a.zps, a.spieler "
		." ORDER BY a.zps ASC , a.brett ASC, spieler ASC "
		;
	$db->setQuery($sql);
	$spieler=$db->loadObjectList();

	// Dateikopf
	$xml = utf8_decode($liga_name[0]->name)."\n"; // Turnierbezeichnung
	$xml .= "Erstellt mit CLM - ChessLeagueManager\n"; // Details zum Turnier oder Leerzeile
	$xml .= "MR  ".count($spieler)."  ".$liga_name[0]->runden."  ".$liga_name[0]->durchgang."\n"; // Kennzeichen zum Turnier
 	$xml .= " ttt. rrr nnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnn vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv lll ffffffffff pppppppppp gggggggg eeee dddd  zzzzz mmmm\n";

	$cnt = 1;
	$player = array();
	foreach($spieler as $spl){
		// laufende Nummer für Spieler erzeugen
		$player[$spl->zps][$spl->spieler] = $cnt;
		$cnt++;
		
		$name = explode(",", $spl->Spielername);

		$xml_data = $fill[(4-strlen($player[$spl->zps][$spl->spieler]))].$player[$spl->zps][$spl->spieler].'.'
			.$fill[(4-strlen($player[$spl->zps][$spl->spieler]))].$player[$spl->zps][$spl->spieler]
			.' '.utf8_decode($name[0]).', '.utf8_decode($name[1]).$fill[(32-(strlen(utf8_decode($name[0]))+strlen(utf8_decode($name[1]))+2))]
			.' '.substr(utf8_decode($spl->Vereinname).$fill[(32-strlen(utf8_decode($spl->Vereinname)))], 0,32)
			.' '.$spl->FIDE_Land.$fill[(3-strlen($spl->FIDE_Land))];

		if($spl->FIDE_ID =="0" OR $spl->FIDE_ID =="") { $fide_id = "";} else { $fide_id = $spl->FIDE_ID; }
			$xml_data .= ' '.$fill[(10-strlen($fide_id))].$fide_id
				.' '.$fill[(10-strlen($spl->PKZ))].$spl->PKZ
				.' '.$fill[(8-strlen($spl->Geburtsjahr))].$spl->Geburtsjahr;	

		if($spl->FIDE_Elo =="0" OR $spl->FIDE_Elo =="") { $fide_elo = "";} else { $fide_elo = $spl->FIDE_Elo; }
			$xml_data .= ' '.$fill[(4-strlen($fide_elo))].$fide_elo;
		
		if($spl->DWZ =="0" OR $spl->DWZ =="") { $DWZ = "";} else { $DWZ = $spl->DWZ; }
			$xml_data .= ' '.$fill[(4-strlen($DWZ))].$DWZ
				.'  '.$fill[(5-strlen($spl->zps))].$spl->zps
				.' '.$fill[(4-strlen($spl->spieler))].$spl->spieler
				.' '
				;
				//."\n";
		$spieler_data[$cnt] = $xml_data;
	}

	// Rundendaten holen
	$sql = " SELECT * FROM `#__clm_rnd_spl` "
		." WHERE sid = ".$sid
		." AND lid = ".$liga_name[0]->id
		//." AND weiss = 1 "
		." ORDER BY zps ASC, spieler ASC, dg ASC, runde ASC "
		;
	$db->setQuery($sql);
	$runden_daten =$db->loadObjectList();

	// Ergebnis ID vom CLM auf Dewis umschreiben
	// Die Kommentare sind CLM Ergebnisse im Klartext !
	$erg[0]="0";	// 0 - 1
	$erg[1]="1";	// 1 - 0
	$erg[2]="R";	// 0.5 - 0.5
	$erg[3]="0";	// 0-0
	$erg[4]="-";	// -/+
	$erg[5]="+";	// +/-
	$erg[6]=":";	// -/-
	$erg[7]=":";	// --- 
	$erg[8]=":";	// spielfrei 
	// NEUE ErgebnisID's
	$erg[9]="R";	// ½:0 
	$erg[10]="0";	// 0:½
	$erg[11]="0";	// 0:-
	$erg[12]=":";	// -:0

	// Umgekehrte Ergebnisse wegen Sortierung nach "Weiss"
	$erg_bl[0]="1"; // 0 - 1
	$erg_bl[1]="0"; // 1 - 0
	$erg_bl[2]="R"; // 0.5 - 0.5
	$erg_bl[3]="-"; // 0-0
	$erg_bl[4]="+"; // -/+
	$erg_bl[5]="-"; // +/-
	$erg_bl[6]=":"; // -/-
	$erg_bl[7]=":";	  // --- 
	$erg_bl[8]=":";	  // spielfrei 
	// NEUE ErgebnisID's
	$erg_bl[9]="0"; // ½:0 
	$erg_bl[10]="R";// 0:½
	$erg_bl[11]=":";// 0:-
	$erg_bl[12]="0";// -:0

	foreach($runden_daten as $rnd_data){
		$addy_1 = $fill[(3-strlen($player[$rnd_data->gzps][$rnd_data->gegner]))].$player[$rnd_data->gzps][$rnd_data->gegner];
		
		if($rnd_data->heim =="1"){
			$ergebnis_1 = $erg[$rnd_data->ergebnis]."W".$addy_1;
			if($rnd_data->ergebnis =="4" OR $rnd_data->ergebnis =="5" OR $rnd_data->ergebnis =="6"){
				$ergebnis_1 = $erg[$rnd_data->ergebnis].":".$addy_1;
			}
		}
		if($rnd_data->heim =="0"){
			$ergebnis_1 = $erg_bl[$rnd_data->ergebnis]."B".$addy_1;
			if($rnd_data->ergebnis =="4" OR $rnd_data->ergebnis =="5" OR $rnd_data->ergebnis =="6"){
				$ergebnis_1 = $erg_bl[$rnd_data->ergebnis].":".$addy_1;
			}
		}

		$spieler_runden[$player[$rnd_data->zps][$rnd_data->spieler]][$rnd_data->dg][$rnd_data->runde] = " ".$ergebnis_1 ;
	}

	// Daten zusammensetzen
	$cnt = 1;
	foreach($spieler_data as $spl_data){
	  for($zg=0; $zg < $liga_name[0]->durchgang; $zg++){
		for($zr=0; $zr < $liga_name[0]->runden; $zr++){
			if(!$spieler_runden[$cnt][($zg+1)][($zr+1)]) { $runde_temp .= "  :  0";	}
				else { $runde_temp .= $spieler_runden[$cnt][($zg+1)][($zr+1)]; }
		}
	  }
	  $xml .= $spl_data.$runde_temp."\n";
	  unset($runde_temp);
	  $cnt++;
	}


	// Zeit der ersten Runde
	$sql = " SELECT a.* FROM #__clm_runden_termine as a"
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid"
		." WHERE s.archiv = 0 AND a.liga = ".$liga
		." ORDER BY a.nr ASC LIMIT 1 "
		;
	$db->setQuery($sql);
	$liga_start	= $db->loadObjectList();
	
	// Probedaten
		$ort		= utf8_decode("Name Ort");
		$fide_land	= "GER";
		$datum_s	= JHTML::_('date',  $liga_start[0]->datum, JText::_('d.m.Y'));
		$datum_e	= JHTML::_('date',  $liga_date[0]->datum, JText::_('d.m.Y'));
		$zuege_1	= utf8_decode("90 Min 40 Züge");
		$zuege_2	= utf8_decode("60 Min 20 Züge");
		$zuege_3	= utf8_decode("30 Min Rest");
		$tl		= utf8_decode("Name Turnierleiter");
		$sr		= utf8_decode("Name Schiedsrichter");
	// Ende Probedaten
	
	$xml .= "###\n";
	$xml .= "Name:       ".utf8_decode($liga_name[0]->name)."\n";                    
	$xml .= "Ort:        ".$ort."\n";                    
	$xml .= "FIDE-Land:  ".$fide_land."\n";                                          
	$xml .= "Datum(S):   ".$datum_s.$fill[(21-strlen($datum_s))];
	$xml .= "Datum(E):   ".$datum_e."\n";
	$xml .= utf8_decode("Züge(1):    ").$zuege_1.$fill[(21-strlen($zuege_1))];
	$xml .= utf8_decode("Züge(2):    ").$zuege_2.$fill[(21-strlen($zuege_2))];
	$xml .= utf8_decode("Züge(3):    ").$zuege_3." \n";
	$xml .= "Turnierleitung: ".$tl."\n";                                                          
	$xml .= "Schiedsrichter: ".$sr."\n";                                                          
	$xml .= "Anwender: Erstellt mit CLM - ChessLeagueManager\n";                               
  
	}

	/////////////////////
	// ENDE DSB Format //
	/////////////////////
	
	
	
	//////////////////////
	// DEWIS XML Format //
	//////////////////////

	if($format =="2"){
	$xml ='<?xml version="1.0" encoding="UTF-8"?>'
		.'<dewis xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
		.'xmlns:dwz="https://dwz.svw.info/services/files/xml/tournamentImport.xsd">'
		;

	// Turniersektion
	$xml .= '<tournamentSection>'
		.'<tournament>'
		;
	
	// Ligadaten
	$xml .= '<label>'.$liga_name[0]->name.'</label>'
		.'<type>'.$turnier_typ.'</type>'
		.'<rounds>'.$anzahl_runden.'</rounds>'
		.'<endDate>'.$end_date.'</endDate>'
		.'<notes>Erstellt mit CLM - ChessLeagueManager</notes>'
		.'</tournament>'
		;
	
	// Rundendaten ermitteln
	if(!$et){
		$sql = " SELECT * FROM #__clm_runden_termine "
			." WHERE sid = '$sid' AND liga = '".$liga_name[0]->id."'"
			." ORDER BY nr ASC "
			;
	} else {
		$sql = " SELECT * FROM #__clm_turniere_rnd_termine "
			." WHERE sid = '$sid' AND turnier = '".$liga_name[0]->id."'"
			." ORDER BY nr ASC "
			;
	}
	$db->setQuery($sql);
	$runden=$db->loadObjectList();

	// Rundendaten
	$xml .= '<appointments>';

	/////////////////////////////////
	// TODO
	// Uhrzeit der Runden ???
	/////////////////////////////////
		
	foreach($runden as $rdata){
		$xml .= '<appointment>'
			.'<round>'.$rdata->nr.'</round>'
			.'<matchday>'.$rdata->datum.'</matchday>'
			.'<matchtime>'.$rdata->startzeit.'</matchtime>'
			.'</appointment>'
			;
	}
	$xml .='</appointments>'
		.'</tournamentSection>'
		;

	// Spielerdaten
	$xml .='<playerSection>'
		.'<players>'
		;

	if(!$et){
		$sql = " SELECT a.*,v.Vereinname,s.PKZ,s.Geburtsjahr,s.Spielername,s.DWZ FROM `#__clm_rnd_spl` as a "
			." LEFT JOIN #__clm_dwz_spieler as s ON s.sid = a.sid AND s.ZPS = a.zps AND s.Mgl_Nr = a.spieler "
			." LEFT JOIN #__clm_dwz_vereine as v ON v.sid = a.sid AND v.ZPS = a.zps "
			." WHERE a.sid = ".$sid
			." AND a.lid = ".$liga_name[0]->id
			." GROUP BY a.zps, a.spieler "
			." ORDER BY a.zps ASC , a.brett ASC, spieler ASC "
			;
	} else {
		$sql = " SELECT t.verein as Vereinname,t.zps, t.mgl_nr as spieler, t.name as Spielername, t.birthYear as Geburtsjahr, "
			." s.FIDE_ID, s.PKZ, s.DWZ "
			." FROM `#__clm_turniere_rnd_spl` as a "
			." LEFT JOIN #__clm_turniere_tlnr as t ON t.sid = a.sid AND t.snr = a.spieler AND t.turnier = a.turnier "
			." LEFT JOIN #__clm_dwz_spieler as s ON s.sid = a.sid AND s.ZPS = t.zps AND s.Mgl_Nr = t.mgl_nr "
			." WHERE a.sid = ".$sid
			." AND a.turnier = ".$liga_name[0]->id
			." GROUP BY t.zps, t.mgl_nr "
			." ORDER BY t.snr ASC "
			;
	}
	$db->setQuery($sql);
	$spieler=$db->loadObjectList();

	$cnt	= 1;
	$player	= array();
	foreach($spieler as $spl){
		if($spl->zps !="" AND $spl->spieler !=""){
			// laufende Nummer für Spieler erzeugen
			$player[$spl->zps][$spl->spieler] = $cnt;
			$cnt++;
		
		$name = explode(",", $spl->Spielername);

		$xml .= '<player>'
			.'<noPlayer>'.$player[$spl->zps][$spl->spieler].'</noPlayer>'
			.'<id>'.$spl->PKZ.'</id>'
			.'<surname>'.$name[0].'</surname>'
			.'<forename>'.$name[1].'</forename>'
			.'<dob>'.$spl->Geburtsjahr.'</dob>'
			.'<vkz>'.$spl->zps.'</vkz>'
			.'<club>'.$spl->Vereinname.'</club>'
			.'<noMember>'.$spl->spieler.'</noMember>'
			.'<idFide>'.$spl->FIDE_ID.'</idFide>'
			.'<rating>'.$spl->DWZ.'</rating>'
			.'</player>'
			;
		}
	}
	$xml .= '</players>';

	// Begegnungen
	if(!$et){
		$sql = " SELECT * FROM `#__clm_rnd_spl` "
			." WHERE sid = ".$sid
			." AND lid = ".$liga_name[0]->id
			." AND weiss = 1 "
			." ORDER BY dg ASC, runde ASC, paar ASC, brett ASC "
			;
	} else {
		$sql = " SELECT a.*, t.mgl_nr as spieler, t.zps as zps, u.mgl_nr as gegner, u.zps as gzps FROM `#__clm_turniere_rnd_spl` as a "
			." LEFT JOIN #__clm_turniere_tlnr as t ON t.sid = a.sid AND t.turnier = a.turnier AND t.snr = a.spieler "
			." LEFT JOIN #__clm_turniere_tlnr as u ON u.sid = a.sid AND u.turnier = a.turnier AND u.snr = a.gegner "
			." WHERE a.sid = ".$sid
			." AND a.turnier = ".$liga_name[0]->id
			." AND a.heim = 1 "
			." ORDER BY a.dg ASC, a.runde ASC, a.paar ASC, a.brett ASC "
			;
	}
			
	$db->setQuery($sql);
	$runden_daten =$db->loadObjectList();
//$app->enqueueMessage( 'SQL '.print_r($player).'-----'.$sql, 'warning');
	// Ergebnis ID vom CLM auf Dewis umschreiben
	// Die Kommentare sind CLM Ergebnisse im Klartext !
	$erg[0]="0:1";	// 0 - 1
	$erg[1]="1:0";	// 1 - 0
	$erg[2]="½:½";	// 0.5 - 0.5
	$erg[3]="0:0";	// 0-0
	$erg[4]="-:+";	// -/+
	$erg[5]="+:-";	// +/-
	$erg[6]="-:-";	// -/-
	$erg[7]="";	// --- 
	$erg[8]="";	// spielfrei 
	// NEUE ErgebnisID's
	$erg[9]="½:0";	// ½:0 
	$erg[10]="0:½";	// 0:½
	$erg[11]="0:-";	// 0:-
	$erg[12]="-:0";	// -:0

	// Umgekehrte Ergebnisse wegen Sortierung nach "Weiss"
	$erg_bl[0]="1:0"; // 0 - 1
	$erg_bl[1]="0:1"; // 1 - 0
	$erg_bl[2]="½:½"; // 0.5 - 0.5
	$erg_bl[3]="0:0"; // 0-0
	$erg_bl[4]="+:-"; // -/+
	$erg_bl[5]="-:+"; // +/-
	$erg_bl[6]="-:-"; // -/-
	$erg_bl[7]="";	  // --- 
	$erg_bl[8]="";	  // spielfrei 
	// NEUE ErgebnisID's
	$erg_bl[9]="0:½"; // ½:0 
	$erg_bl[10]="½:0";// 0:½
	$erg_bl[11]="-:0";// 0:-
	$erg_bl[12]="0:-";// -:0

	$xml .= '<games>';

	foreach($runden_daten as $rnd){
		if($rnd->dg > 1) { $runde_temp = ($rnd->dg-1)*$liga_name[0]->runden + $rnd->runde;}
			else { $runde_temp = $rnd->runde; }
		if($rnd->heim == "0" AND $rnd->weiss == "1") { $erg_temp = $erg_bl[$rnd->ergebnis];}
			else { $erg_temp = $erg[$rnd->ergebnis];}
		$xml .= '<game>'
			.'<round>'.$runde_temp.'</round>'
			.'<noWhite>'.$player[$rnd->zps][$rnd->spieler].'</noWhite>'
			.'<noBlack>'.$player[$rnd->gzps][$rnd->gegner].'</noBlack>'
			.'<result>'.$erg_temp.'</result>'
			.'</game>'
			;
	}
	$xml .= '</games>'
		.'</playerSection>'
		;

	/////////////////////////////////
	// TODO
	// Spielgemeinschaften !!
	/////////////////////////////////

	// NUR für Mannschaftsturniere
	// Mannschaftssektion
	if(!$et){
	$xml .= '<teamSection>'
		.'<teams>'
		;

	// Mannschaften
	$sql = " SELECT tln_nr, name, zps, man_nr FROM `#__clm_mannschaften` "
		." WHERE sid = '$sid' AND liga = '".$liga_name[0]->id."'"
		." ORDER BY tln_nr ASC "
		;
	$db->setQuery($sql);
	$mannschaften=$db->loadObjectList();

	foreach($mannschaften as $man){

		$xml .= '<team>'
			.'<lot>'.$man->tln_nr.'</lot>'
			.'<teamName>'.$man->name.'</teamName>'
			.'<lineup>'
			;
	
		// Mannschaftsaufstellung einer Mannschaft ermitteln		
		$sql = " SELECT r.zps,r.spieler FROM `#__clm_mannschaften` as a"
			." LEFT JOIN #__clm_rnd_spl as r ON r.sid = a.sid AND r.lid = a.liga AND r.tln_nr = a.tln_nr "
			." WHERE a.sid = '$sid' "
			." AND a.liga = '".$liga_name[0]->id."'"
			." AND a.tln_nr = '".$man->tln_nr."'"
			." GROUP BY r.zps, r.spieler "
			." ORDER BY a.tln_nr ASC, r.brett ASC, r.spieler  "
			;
		$db->setQuery($sql);
		$mannschaft=$db->loadObjectList();

			foreach($mannschaft as $lineup){
				$xml .= '<noPlayer>'.$player[$lineup->zps][$lineup->spieler].'</noPlayer>';
			}
	
		$xml .= '</lineup>'
			.'</team>'
			;
	}
	$xml .= '</teams>';
	
	// Begegnungen Mannschaften
	$sql = " SELECT a.runde,a.dg, a.tln_nr as home, r.tln_nr as guest, a.brettpunkte as hpoints, r.brettpunkte as gpoints "
		." FROM `#__clm_rnd_man` as a"
		." LEFT JOIN `#__clm_rnd_man` as r ON r.sid = a.sid AND r.lid = a.lid AND r.runde = a.runde "
		." AND r.dg = a.dg AND r.tln_nr = a.gegner AND r.heim = 0 "
		." WHERE a.sid = '$sid' "
		." AND a.lid = '".$liga_name[0]->id."'"
		." AND a.heim = 1 "
		." ORDER BY a.dg ASC, a.runde ASC, a.paar ASC "
		;
	$db->setQuery($sql);
	$runden_mannschaft=$db->loadObjectList();

	$xml .= '<encounter>';
		foreach($runden_mannschaft as $rnd_man){
			if($rnd_man->dg > 1) { $runde_temp = ($rnd_man->dg-1)*$liga_name[0]->runden + $rnd_man->runde;
			} else { $runde_temp = $rnd_man->runde; }

			$xml .= '<play>'
				.'<round>'.$runde_temp.'</round>'
				.'<noHomeTeam>'.$rnd_man->home.'</noHomeTeam>'
				.'<noGuestTeam>'.$rnd_man->guest.'</noGuestTeam>'
				.'<pointsHome>'.$rnd_man->hpoints.'</pointsHome>'
				.'<pointsGuest>'.$rnd_man->gpoints.'</pointsGuest>'
				.'</play>'
				;
		}
	$xml .= '</encounter>'
		.'</teamSection>';
	}
	$xml .='</dewis>';
	}
	///////////////////////
	// ENDE DEWIS Format //
	///////////////////////

	// Slashes aus Namen filtern und Namen mit Pfad zusammensetzen
	$dat_name	= ereg_replace("[/]", "_", $liga_name[0]->name);
	$file		= $dat_name.'__'.$datum;
	$path		= JPath::clean(JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'elobase');
	if($format =="1"){ $datei_endung = "txt";}
	if($format =="2"){ $datei_endung = "xml";}
	$write		= $path.DS.$file.'.'.$datei_endung;

	// Datei schreiben ggf. Fehlermeldung absetzen
	jimport('joomla.filesystem.file');
	if (!JFile::write( $write, $xml )) { JError::raiseWarning( 500, JText::_( 'DB_FEHLER_SCHREIB' ) ); }

	$app->enqueueMessage( 'Datei "'.$file.'" wurde geschrieben !', 'warning');
	$app->redirect( $adminLink->url);
	}

	
function xml_dateien()
	{
	jimport( 'joomla.filesystem.folder' );
	$option		= JRequest::getCmd('option');
	$filesDir 	= 'components'.DS.$option.DS.'elobase';
	$ex_dbf		= JFolder::files( $filesDir, 'dbf$',true, false);
	$ex_dbf[]	= 'index.html';
	$files		= JFolder::files( $filesDir, '',true, false, $ex_dbf );
	$count		= count($files);
	
	if($count > 0){
	$dateien = '<table style="width:100%;">';
		for ($x=0; $x< $count; $x++ ) {
			$dateien.='<tr>'
				.'<td width="70%"><a href="components/com_clm/elobase/'.$files[$x].'" target="_blank">'.$files[$x].'</a></td>'
				.'<td width="10%">&nbsp;&nbsp;</td>'
				.'<td width="20%"><a href="index.php?option=com_clm&view=auswertung&task=delete&datei='.$files[$x].'" '
				//.'onClick="submitform();"'
				.'>Löschen</a></td>'
				.'</tr>';
			//$dateien .= '<a href="components/com_clm/elobase/'.$files[$x].'" target="_blank">'.$files[$x].'</a><br>';
		}
	$dateien .= '</table>';
	} else { $dateien = 'Keine Dateien vorhanden !'; }
	
	return $dateien;
	}

function delete()
	{
	// Check for request forgeries
	//JRequest::checkToken() or die( 'Invalid Token' );
	$option		= JRequest::getCmd('option');
	$datei		= JRequest::getVar('datei');
	$app		= JFactory::getApplication();
	
	if($datei){
		$filesDir 	= 'components'.DS.$option.DS.'elobase';
		jimport('joomla.filesystem.file');
		JFile::delete( $filesDir.DS.$datei );
		$msg =JText::_( 'DB_DEL_SUCCESS');
	}else{	$msg =JText::_( 'Keine Datei gefunden !');}

	$app->enqueueMessage( $msg, 'warning');	

	$adminLink = new AdminLink();
	$adminLink->view = "auswertung";
	$adminLink->makeURL();
	$app->redirect( $adminLink->url);
	}	

function liga_filter() {
	$db =& JFactory::getDBO();	
	// Ligafilter
	$sql = 'SELECT d.id AS cid, d.name FROM #__clm_liga as d'
		." LEFT JOIN #__clm_saison as s ON s.id = d.sid"
		." WHERE s.archiv = 0 AND d.liga_mt = 0 ";
	$db->setQuery($sql);
	$ligalist[]	= JHTML::_('select.option',  '0', JText::_( 'MANNSCHAFTEN_LIGA' ), 'cid', 'name' );
	$ligalist	= array_merge( $ligalist, $db->loadObjectList() );
	$lists['lid']	= JHTML::_('select.genericlist', $ligalist, 'filter_lid', 'class="inputbox" size="1" onchange=""','cid', 'name', '' );
	
	return $lists['lid'];
	}
	
function turnier_filter() {
	$db =& JFactory::getDBO();	
	// Ligafilter
	$sql = 'SELECT d.id AS cid, d.name FROM #__clm_turniere as d'
		." LEFT JOIN #__clm_saison as s ON s.id = d.sid"
		." WHERE s.archiv = 0 ";
	$db->setQuery($sql);
	$ligalist[]	= JHTML::_('select.option',  '0', JText::_( 'Einzelturnier auswählen' ), 'cid', 'name' );
	$ligalist	= array_merge( $ligalist, $db->loadObjectList() );
	$lists['lid']	= JHTML::_('select.genericlist', $ligalist, 'filter_et', 'class="inputbox" size="1" onchange=""','cid', 'name', '' );
	
	return $lists['lid'];
	}

function mannschaftsturnier_filter() {
	$db =& JFactory::getDBO();	
	// Ligafilter
	$sql = 'SELECT d.id AS cid, d.name FROM #__clm_liga as d'
		." LEFT JOIN #__clm_saison as s ON s.id = d.sid"
		." WHERE s.archiv = 0 AND d.liga_mt = 1 ";
	$db->setQuery($sql);
	$ligalist[]	= JHTML::_('select.option',  '0', JText::_( 'Mannschaftsturnier auswählen' ), 'cid', 'name' );
	$ligalist	= array_merge( $ligalist, $db->loadObjectList() );
	$lists['lid']	= JHTML::_('select.genericlist', $ligalist, 'filter_mt', 'class="inputbox" size="1" onchange=""','cid', 'name', '' );
	
	return $lists['lid'];
	}

	
}

?>