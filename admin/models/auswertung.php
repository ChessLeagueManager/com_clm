<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

class CLMModelAuswertung extends JModelLegacy {

	var $_swtFiles;

function __construct(){
		parent::__construct();
	}


function datei() {

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	$db	= JFactory::getDBO();
	$app	= JFactory::getApplication();
	$option	= JRequest::getCmd('option');
	$jinput = $app->input;
	$liga	= $jinput->get('filter_lid', null, null);
	$mt	= $jinput->get('filter_mt', null, null);
	$et	= $jinput->get('filter_et', null, null);
	$format	= $jinput->get('filter_format', null, null);
	$sid	= clm_core::$access->getSeason();

	// Link zum redirect generieren
	$adminLink = new AdminLink();
	$adminLink->view = "auswertung";
	$adminLink->makeURL();

	// Dateinamen zusammensetzen
	$date	=JFactory::getDate();
	$now	= $date->toSQL();
	$datum	= JHTML::_('date',  $now, JText::_('d-m-Y__H-i-s'));

	// Grunddaten für Ligen und Mannschaftsturniere laden
	if($liga !=null OR $mt !=null){
		// Mannschaftsturnier eine Liga ID zuweisen
		if($mt !=null){ $liga = $mt;}
		
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
			if($rnd_count < $counter AND $rnd_count == 0){
				$app->enqueueMessage(JText::_( 'DB_WTEXT0' ).JText::_( 'DB_ROUND' ).$rnd.JText::_( 'DB_DG' ).$dg, 'warning');
				$fehler = 1;
			} elseif($rnd_count < $counter){
				$app->enqueueMessage(JText::_( 'DB_WTEXT1' ).JText::_( 'DB_ROUND' ).$rnd.JText::_( 'DB_DG' ).$dg, 'warning');
				$fehler = 1;
			}
		  }
		}
	}
	
	// Grunddaten für Einzelturniere laden
	if($et !=null){
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
	if($et !=null) {
		$format = 2;  // Nur XML für Einzelturniere
		$typ	= $liga_name[0]->typ;
		if($typ =="1"){ $turnier_typ = 'SR'; } // SR: Einzelturnier; jeder gegen jeden
		if($typ =="2"){ $turnier_typ = 'SR'; }
		if($typ =="3"){ $turnier_typ = 'SW'; } // SW: Einzelturnier; Schweizer System
		if($typ =="4"){ $turnier_typ = 'SC'; } 
		if($typ =="5"){ $turnier_typ = 'SC'; } // SC: Einzelturnier; K.O. System (Pokal)
	}
	if($liga !=null) {
		if($mt !=null){ $format	= 2; } // Nur XML für Mannschaftsturniere. KEINE LIGA !
		
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
	if (count($spieler) == 0) { JError::raiseWarning( 500, JText::_( 'DB_NO_PLAYER' ) ); 
		$app->enqueueMessage(JText::_( 'DB_FILE_NOSUCCESS' ), 'warning');
		$app->redirect( $adminLink->url);
	}
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
	$erg[9]="0";	// 0:0,5 
	$erg[10]="R";	// 0,5:0

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
	$erg_bl[9]="R";   // 0:0,5 
	$erg_bl[10]="0";  // 0,5:0

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
		$sql = " SELECT a.*,v.Vereinname,s.PKZ,s.Geburtsjahr,s.Spielername,s.DWZ,s.FIDE_ID FROM `#__clm_rnd_spl` as a "
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
		
		if (is_null($spl->Spielername)) $name = explode(",", ',');
		else $name = explode(",", $spl->Spielername);

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

  
	////////////////////////
	// XLS Format England //
	////////////////////////

  if($format =="3"){
/**
 * Include the class for creating Excel XML docs
 */
include(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'ExcelWriterXML.php');

// Find Players
	$sql = " SELECT a.*,v.Vereinname,s.Spielername,s.Geschlecht FROM `#__clm_rnd_spl` as a "
		." LEFT JOIN #__clm_dwz_spieler as s ON s.sid = a.sid AND s.ZPS = a.zps AND s.PKZ = a.PKZ "
		." LEFT JOIN #__clm_dwz_vereine as v ON v.sid = a.sid AND v.ZPS = a.zps "
		." WHERE a.sid = ".$sid
		." AND a.lid = ".$liga_name[0]->id
		." GROUP BY a.zps, a.PKZ "
		." ORDER BY a.zps ASC , a.brett ASC, a.PKZ ASC "
		;
	$db->setQuery($sql);
	$spieler=$db->loadObjectList();
//echo "<br>pl:"; var_dump($spieler); die();
// Create a new instance of the Excel Writer
$xmla = new ExcelWriterXML('CLM grading.xml');

/**
 * Add some general properties to the document
 */
$xmla->docTitle('Grading - '.$liga_name[0]->name);
$user = clm_core::$cms->getUserData();
$xmla->docAuthor($user[2]);
$xmla->docCompany('ChessLeagueManager');
$xmla->docManager('CLM Team');

/**
 * Choose to show any formatting/input errors on a seperate sheet
 */
$xmla->showErrorSheet(true);

/**
 * Create a new sheet with the XML document
 */
$sheet1 = $xmla->addSheet('Header');
$sheet1->columnWidth(1,'130');
$sheet1->columnWidth(2,'130');
$sheet1->columnWidth(3,'440');
$sheet1->writeString(1,1,'Event Code');
$sheet1->writeString(1,2,'');
$sheet1->writeString(1,3,'Max 10 characters.  Your two-letter Grader Code followed by 1 - 8 characters of your choice');
$sheet1->writeString(2,1,'Submission Number');
$sheet1->writeString(2,2,'1');
$sheet1->writeString(2,3,'Use 1 for first submission and increase with each submission');
$sheet1->writeString(3,1,'Event Name');
$sheet1->writeString(3,2,$liga_name[0]->name);
$sheet1->writeString(3,3,'Max 60 characters.  Make the nature and location of the event clear.');
$sheet1->writeString(4,1,'Event Date');
$sheet1->writeString(4,2,'');
$sheet1->writeString(4,3,'Start Date of Event');
$sheet1->writeString(5,1,'Final Results Date');
$sheet1->writeString(5,2,'');
$sheet1->writeString(5,3,"Last Date of Event - same as preceding for one-day events");
$sheet1->writeString(6,1,"Grader Name");
$sheet1->writeString(6,2,'');
$sheet1->writeString(6,3,"Grader's Name");
$sheet1->writeString(7,1,"Grader Address");
$sheet1->writeString(7,2,'');
$sheet1->writeString(7,3,"Grader's email address ONLY. Will be used to return feedback.");
$sheet1->writeString(8,1,"Treasurer Name");
$sheet1->writeString(8,2,'');
$sheet1->writeString(8,3,"Treasurer's Name");
$sheet1->writeString(9,1,"Treasurer Address");
$sheet1->writeString(9,2,'');
$sheet1->writeString(9,3,"Treasurer's postal address - on one line with commas");
$sheet1->writeString(10,1,"Moves in first session");
$sheet1->writeString(10,2,'');
$sheet1->writeString(10,3,"e.g. 36 or 40 (leave blank for rapidplay)");
$sheet1->writeString(11,1,"Minutes for first session");
$sheet1->writeString(11,2,'');
$sheet1->writeString(11,3,"e.g. 90 or 120 (leave blank for rapidplay)");
$sheet1->writeString(12,1,"Moves in second session");
$sheet1->writeString(12,2,'');
$sheet1->writeString(12,3,"e.g. 20 or leave blank if immediate quickplay finish");
$sheet1->writeString(13,1,"Minutes in second session");
$sheet1->writeString(13,2,'');
$sheet1->writeString(13,3,"e.g. 60 or leave blank if preceding cell is blank");
$sheet1->writeString(14,1,"Minutes in final session");
$sheet1->writeString(14,2,'');
$sheet1->writeString(14,3,"e.g. 15 or leave blank if no quickplay finish");
$sheet1->writeString(15,1,"Minutes for game");
$sheet1->writeString(15,2,'');
$sheet1->writeString(15,3,"Minutes for rapidplay or all-in-one-session standardplay");
$sheet1->writeString(16,1,"Seconds added per move");
$sheet1->writeString(16,2,'');
$sheet1->writeString(16,3,"Seconds per move added in Fischer mode else blank");
$sheet1->writeString(17,1,"Grand Prix");
$sheet1->writeString(17,2,'');
$sheet1->writeString(17,3,"Enter Y if results from event are to be included in ECF Grand Prix");
$sheet1->writeString(18,1,"FIDE rated");
$sheet1->writeString(18,2,'');
$sheet1->writeString(18,3,"Enter Y if event is to be FIDE rated");

// Second sheet - player_list
$sheet2 = $xmla->addSheet('Player_List');
$sheet2->columnWidth(1,'40');
$sheet2->writeString(1,1,'PIN');
$sheet2->columnWidth(2,'50');
$sheet2->writeString(1,2,'BCFCode');
$sheet2->columnWidth(3,'120');
$sheet2->writeString(1,3,'Name');
$sheet2->columnWidth(4,'30');
$sheet2->writeString(1,4,'Gender');
$sheet2->columnWidth(5,'50');
$sheet2->writeString(1,5,'DOB');
$sheet2->columnWidth(6,'30');
$sheet2->writeString(1,6,'ClubCode');
$sheet2->columnWidth(7,'100');
$sheet2->writeString(1,7,'ClubName');
$sheet2->columnWidth(8,'60');
$sheet2->writeString(1,8,'BCFMemNo');
$sheet2->columnWidth(9,'60');
$sheet2->writeString(1,9,'FIDEName');
$sheet2->columnWidth(10,'200');
$sheet2->writeString(1,10,'Comment');
$sheet2->columnWidth(11,'20');
$sheet2->writeString(1,11,'Title');
$sheet2->columnWidth(12,'30');
$sheet2->writeString(1,12,'Initials');
$sheet2->columnWidth(13,'60');
$sheet2->writeString(1,13,'Forename');
$sheet2->columnWidth(14,'60');
$sheet2->writeString(1,14,'Surname');

$ccode = 0;
$crow = 1;
$pl_array = array();
foreach($spieler as $player){
	if ($ccode != $player->zps) {
		$ccode = $player->zps;
		$ccount = 0;
	}
	$ccount++;
	$scount = sprintf("%'.02d\n", $ccount);
	$crow++;
	$sheet2->writeString($crow,1,$player->zps.$scount);
	$sheet2->writeString($crow,2,$player->PKZ);
	$sheet2->writeString($crow,3,$player->Spielername);
	$sheet2->writeString($crow,4,$player->Geschlecht);
	$sheet2->writeString($crow,6,$player->zps);
	$sheet2->writeString($crow,7,$player->Vereinname);
	$pl_array[$player->zps.$player->PKZ] = new stdClass();
	$pl_array[$player->zps.$player->PKZ]->Spielername = $player->Spielername;
	$pl_array[$player->zps.$player->PKZ]->Vereinname = $player->Vereinname;
	$pl_array[$player->zps.$player->PKZ]->PIN = $player->zps.$scount;
}

// Third sheet - results_list
$sheet3 = $xmla->addSheet('Results_List');
$sheet3->columnWidth(1,'50');
$sheet3->writeString(1,1,'PIN1');
$sheet3->columnWidth(2,'50');
$sheet3->writeString(1,2,'PIN2');
$sheet3->columnWidth(3,'40');
$sheet3->writeString(1,3,'Result');
$sheet3->columnWidth(4,'40');
$sheet3->writeString(1,4,'Colour1');
$sheet3->columnWidth(5,'60');
$sheet3->writeString(1,5,'Date');
$sheet3->columnWidth(6,'30');
$sheet3->writeString(1,6,'Board');
$sheet3->columnWidth(7,'40');
$sheet3->writeString(1,7,'Round');
$sheet3->columnWidth(8,'200');
$sheet3->writeString(1,8,'Comment');

// Find games
		$sql = " SELECT a.*, hm.name as hmname, gm.name as gmname, m.pdate FROM `#__clm_rnd_spl` as a "
			." LEFT JOIN #__clm_rnd_man as m ON m.sid = a.sid AND m.lid = a.lid AND m.dg = a.dg AND m.runde = a.runde AND m.tln_nr = a.tln_nr "
			." LEFT JOIN #__clm_mannschaften as hm ON hm.sid = a.sid AND hm.liga = a.lid AND hm.tln_nr = a.tln_nr "
			." LEFT JOIN #__clm_mannschaften as gm ON gm.sid = a.sid AND gm.liga = a.lid AND gm.tln_nr = m.gegner "
			." WHERE a.sid = ".$sid
			." AND a.lid = ".$liga_name[0]->id
			." AND a.ergebnis < 3 "
			." AND a.heim = 1 "
			." ORDER BY a.dg ASC, a.runde ASC, a.paar ASC, a.brett ASC "
			;
	$db->setQuery($sql);
	$partien =$db->loadObjectList();
//echo "<br>par:"; var_dump($partien); die();

$crow = 1;
foreach($partien as $games){
	$crow++;
	if (isset($pl_array[$games->zps.$games->PKZ])) {
		$sheet3->writeString($crow,1,$pl_array[$games->zps.$games->PKZ]->PIN);
	}
	if (isset($pl_array[$games->gzps.$games->gPKZ])) {
		$sheet3->writeString($crow,2,$pl_array[$games->gzps.$games->gPKZ]->PIN);
	}
	if ($games->ergebnis == 0) {
		$sheet3->writeString($crow,3,'01');
	} elseif ($games->ergebnis == 1) {
		$sheet3->writeString($crow,3,'10');
	} elseif ($games->ergebnis == 2) {
		$sheet3->writeString($crow,3,'55');
	}
	if ($games->weiss == 0) {
		$sheet3->writeString($crow,4,'b');
	} elseif ($games->weiss == 1) {
		$sheet3->writeString($crow,4,'w');
	}
	$sheet3->writeString($crow,5,clm_core::$cms->showDate($games->pdate, "d M Y"));
	$sheet3->writeString($crow,6,$games->brett);
	$sheet3->writeString($crow,7,$games->runde);
	$sheet3->writeString($crow,8,$games->hmname." - ".$games->gmname);
}
	
/**
 * Send the headers, then output the data
 */
$xmla->sendHeaders();
$xml = $xmla->writeData();
	
  }

	// Slashes und Spaces aus Namen filtern und Namen mit Pfad zusammensetzen
	$dat_name	= str_replace(' ', '_', $liga_name[0]->name);
	$dat_name	= str_replace('/', '_', $dat_name);
	$dat_name 	= clm_core::$load->sub_umlaute($dat_name); 
	$file		= utf8_decode($dat_name).'__'.$datum;
	$path		= JPath::clean(JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'dewis');
	if($format =="1"){ $datei_endung = "txt";}
	if($format =="2"){ $datei_endung = "xml";}
	if($format =="3"){ $datei_endung = "xml";}
	$write		= $path.DS.$file.'.'.$datei_endung;

	// Datei schreiben ggf. Fehlermeldung absetzen
	jimport('joomla.filesystem.file');
	if (!JFile::write( $write, $xml )) { JError::raiseWarning( 500, JText::_( 'DB_FEHLER_SCHREIB' ) ); }
  
	$app->enqueueMessage(JText::_( 'DB_FILE_SUCCESS' ).utf8_encode($file), 'warning');
	$app->redirect( $adminLink->url);
	}

	
function xml_dateien()
	{
	jimport( 'joomla.filesystem.folder' );
	$option		= JRequest::getCmd('option');
	$filesDir 	= 'components'.DS.$option.DS.'dewis';
	$ex_dbf		= JFolder::files( $filesDir, 'dbf$',true, false);
	$ex_dbf[]	= 'index.html';
	$files		= JFolder::files( $filesDir, '',true, false, $ex_dbf );
	$count		= count($files);
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;
	
	if($count > 0){
	$dateien = '<table style="width:100%;">';
		for ($x=0; $x< $count; $x++ ) {
			$dateien .= '<tr>'
				.'<td width="60%"><a href="components/com_clm/dewis/'.utf8_encode($files[$x]).'" target="_blank">'.utf8_encode($files[$x]).'</a></td>'
				.'<td width="10%">&nbsp;&nbsp;</td>'
				.'<td width="15%"><a href="index.php?option=com_clm&view=auswertung&task=delete&datei='.$files[$x].'" '
				//.'onClick="submitform();"'
				.'>'.JText::_( 'DELETE').'</a></td>';
			if ($countryversion =="en-out") {  //download funktioniert so nicht, deshalb deaktiviert für 3.2.4
				$dateien .= '<td width="15%"><a href="index.php?option=com_clm&view=auswertung&task=download&datei='.$files[$x].'" '
							.'>'.JText::_( 'DB_DOWNLOAD').'</a></td>';
			} else { 
				$dateien .= '<td width="15%">  </td>';
			}
			$dateien .= '</tr>';
		}
	$dateien .= '</table>';
	} else { $dateien = 'Keine Dateien vorhanden !'; }
	
	return $dateien;
	}

function download()
	{
	// Check for request forgeries
	//JRequest::checkToken() or die( 'Invalid Token' );
	$option		= JRequest::getCmd('option');
	$datei		= JRequest::getVar('datei');
	$app		= JFactory::getApplication();
	
	if($datei){
		$file 	= JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'dewis'.DS.$datei;	
		header ( 'Content-Description: File Transfer' );
		header ( 'Content-Type: application/octet-stream' );
		header ( 'Content-Disposition: attachment; filename=' . $datei );
		header ( 'Expires: 0' );
		header ( 'Cache-Control: must-revalidate' );
		header ( 'Pragma: public' );
		header ( 'Content-Length: ' . filesize ( $file ) );
		ob_clean(); 
		flush();
		readfile ( $file );

		$msg =JText::_( 'DB_DWL_SUCCESS');
	}else{	$msg =JText::_( 'Keine Datei gefunden !');}

	$app->enqueueMessage( $msg, 'warning');	

	$adminLink = new AdminLink();
	$adminLink->view = "auswertung";
	$adminLink->makeURL();
	$app->redirect( $adminLink->url);
	}	
function delete()
	{
	// Check for request forgeries
	//JRequest::checkToken() or die( 'Invalid Token' );
	$option		= JRequest::getCmd('option');
	$datei		= JRequest::getVar('datei');
	$app		= JFactory::getApplication();
	
	if($datei){
		$filesDir 	= 'components'.DS.$option.DS.'dewis';
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
	$db =JFactory::getDBO();	
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
	$db =JFactory::getDBO();	
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
	$db =JFactory::getDBO();	
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
