<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access');

$liga		= $this->liga;
	//Liga-Parameter aufbereiten
	$paramsStringArray = explode("\n", $liga[0]->params);
	$params = array();
	foreach ($paramsStringArray as $value) {
		$ipos = strpos ($value, '=');
		if ($ipos !==false) {
			$params[substr($value,0,$ipos)] = substr($value,$ipos+1);
		}
	}	
	if (!isset($params['dwz_date'])) $params['dwz_date'] = '0000-00-00';
$termin		= $this->termin;
$dwzschnitt	= $this->dwzschnitt;
$dwzgespielt	= $this->dwzgespielt;
$paar		= $this->paar;
$summe		= $this->summe;
$rundensumme	= $this->rundensumme;
$runden_modus = $liga[0]->runden_modus;
$a_html = array('<b>','</b>');
$a_pdf  = array('','');
 
$runde_t = $liga[0]->runden + 1;  
// Test alte/neue Standardrundenname bei 2 Durchgängen
if ($liga[0]->durchgang > 1) {
	if ($termin[$runde_t-1]->name == JText::_('ROUND').' '.$runde_t) {  //alt
		for ($xr=0; $xr< ($liga[0]->runden); $xr++) { 
				$termin[$xr]->name = JText::_('ROUND').' '.($xr+1)." (".JText::_('PAAR_HIN').")";
				$termin[$xr+$liga[0]->runden]->name = JText::_('ROUND').' '.($xr+1)." (".JText::_('PAAR_RUECK').")";
		}
    }
}

require_once(JPATH_COMPONENT.DS.'includes'.DS.'fpdf.php');

class PDF extends FPDF
{
//Kopfzeile
function Header()
{
	require_once(JPATH_COMPONENT.DS.'includes'.DS.'pdf_header.php');
}
//Fusszeile
function Footer()
{
	require(JPATH_COMPONENT.DS.'includes'.DS.'pdf_footer.php');
}
}

if ( $liga[0]->published == 0) {
	$pdf=new PDF();
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetFont('Times','',16);

	$pdf->Cell(10,15,utf8_decode(JText::_('NOT_PUBLISHED')),0,0);
	$pdf->Ln();
	$pdf->Cell(10,15,utf8_decode(JText::_('GEDULD')),0,0);
 } else {
//Array für DWZ Schnitt setzen
$dwz = array();
for ($y=1; $y< ($liga[0]->teil)+1; $y++){ 
		if ($params['dwz_date'] == '0000-00-00') {
			if(isset($dwzschnitt[($y-1)]->dwz)) {
			$dwz[$dwzschnitt[($y-1)]->tlnr] = $dwzschnitt[($y-1)]->dwz; }
		} else {
			if(isset($dwzschnitt[($y-1)]->start_dwz)) {
			$dwz[$dwzschnitt[($y-1)]->tlnr] = $dwzschnitt[($y-1)]->start_dwz; }
		}
	}
// Zellenhöhe -> Standard 6
$zelle = 6;
// Wert von Zellenbreite abziehen
// Bspl. für Standard (Null) für Liga mit 7 Runden und 1 Durchgang
$breite = 0;
// Überschrift Fontgröße Standard = 14
$head_font = 12;
// Fontgröße Standard = 12
$font = 10;
// Seitenlänge
	$lspalte_paar = 230;
	$lspalte_comment = 200;
	
//$counter = $liga[0]->runden*$liga[0]->durchgang;
if ($liga[0]->teil/2 == 6) {$lspalte_paar = 220;}
if ($liga[0]->teil/2 == 5) {$lspalte_paar = 230;}
if ($liga[0]->teil/2 == 4) {$lspalte_paar = 240;}
if ($liga[0]->teil/2 == 3) {$lspalte_paar = 250;}
if ($liga[0]->teil/2 == 2) {$lspalte_paar = 260;}

// Datum der Erstellung
$date =JFactory::getDate();
$now = $date->toSQL();

$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Times','',6);
	$pdf->Cell(10,2,' ',0,0);
	$pdf->Cell(175,4,utf8_decode(JText::_('WRITTEN')).' '.utf8_decode(JText::_('ON_DAY')).' '.utf8_decode(JHTML::_('date',  $now, JText::_('DATE_FORMAT_CLM_PDF'))),0,1,'R');
	
$pdf->SetFont('Times','',16);

	$pdf->Cell(10,10,' ',0,0);
	$pdf->Cell(80,10,utf8_decode(JText::_('PAAR_OVERVIEW')).' : '.utf8_decode($liga[0]->name),0,1,'L');

// Rundenschleife
$z=0;
$z2=0;
$sum_paar=0;
$rund_sum=0;
$term = 0;

$pdf->SetFont('Times','',$head_font-1);
	//$pdf->Ln();

if ( $liga[0]->durchgang > 1) {
	$pdf->Cell(10,15,' ',0,0);
	if ( $liga[0]->durchgang == 2) $pdf->Cell(80,15,utf8_decode(JText::_('PAAR_HIN')),0,1,'L');
	else $pdf->Cell(80,15,utf8_decode(JText::_('PAAR_DG'))." 1",0,1,'L');
	}
else {
	$pdf->Cell(10,15,' ',0,0);
	$pdf->Cell(80,15,' ',0,1,'L');
	}
for ($x=0; $x< ($liga[0]->runden); $x++){

if ($pdf->GetY() > $lspalte_paar) {
		$pdf->AddPage();
	}

if (isset($termin[$term]) AND $termin[$term]->nr == ($x+1)) { if ($termin[$term]->datum > 0) {
	$datum = ', '.JHTML::_('date',  $termin[$term]->datum, JText::_('DATE_FORMAT_CLM_F')); 
	if(isset($termin[$term]->startzeit) and $termin[$term]->startzeit != '00:00:00') { $datum .= '  '.substr($termin[$term]->startzeit,0,5).' Uhr'; } }		
	else $datum = ''; 
	$term++; } 
	$pdf->SetFont('Times','',$head_font-1);
	$pdf->Cell(10,15,' ',0,0);
	$pdf->Cell(173-(8*$breite),$zelle,utf8_decode($termin[$term-1]->name).utf8_decode($datum),1,0,'L');
	$pdf->Ln();

	$pdf->SetFont('Times','',$font);
	$pdf->Cell(10,15,' ',0,0);
	$pdf->Cell(8-$breite,$zelle,utf8_decode(JText::_('PAAR')),1,0,'C');
	$pdf->Cell(8-$breite,$zelle,utf8_decode(JText::_('TLN')),1,0,'C');
	$pdf->Cell(50-$breite,$zelle,utf8_decode(JText::_('HOME')),1,0,'C');
	$pdf->Cell(12-$breite,$zelle,utf8_decode(JText::_('DWZ')),1,0,'C');
	$pdf->Cell(25-$breite,$zelle,utf8_decode(JText::_('RESULT')),1,0,'C');
	$pdf->Cell(8-$breite,$zelle,utf8_decode(JText::_('TLN')),1,0,'C');
	$pdf->Cell(50-$breite,$zelle,utf8_decode(JText::_('GUEST')),1,0,'C');
	$pdf->Cell(12-$breite,$zelle,utf8_decode(JText::_('DWZ')),1,0,'C');
	$tbreite = (2 * (8+50+12)) + 25 - 7*$breite;
	$pdf->Ln();

// Teilnehmerschleife 
for ($y=0; $y< ($liga[0]->teil)/2; $y++){
	if (!isset($paar[$z])) break; 	
	if ($paar[$z]->runde > ($x+1)) break;
	$pdf->SetFont('Times','',$font);
	$pdf->Cell(10,15,' ',0,0);
	$pdf->Cell(8-$breite,$zelle,$paar[$z]->paar,1,0,'C');
	$pdf->Cell(8-$breite,$zelle,$paar[$z]->tln_nr,1,0,'C');
	$pdf->Cell(50-$breite,$zelle,utf8_decode($paar[$z]->hname),1,0,'C');

	if (isset($dwzgespielt[$z2]) AND $dwzgespielt[$z2]->runde == ($x+1) AND $dwzgespielt[$z2]->paar == ($y+1) AND $dwzgespielt[$z2]->dg == 1 AND $paar[$z]->hmnr !=0 AND $paar[$z]->gmnr != 0)
		{ if ($params['dwz_date'] == '0000-00-00') $pdf->Cell(12-$breite,$zelle,round($dwzgespielt[$z2]->dwz),1,0,'C'); 
			else $pdf->Cell(12-$breite,$zelle,round($dwzgespielt[$z2]->start_dwz),1,0,'C'); }

		elseif (isset($dwz[($paar[$z]->htln)])) { $pdf->Cell(12-$breite,$zelle,round($dwz[($paar[$z]->htln)]),1,0,'C');}
		else { $pdf->Cell(12-$breite,$zelle,'',1,0,'C');}
// Wenn Paarung existiert dann Ergebnis-Summen anzeigen
while ( $summe[$sum_paar]->runde < ($x+1) ) $sum_paar++;
if ( $summe[$sum_paar]->runde == ($x+1) AND $summe[$sum_paar]->paarung == ($y+1)) {
	$pdf->Cell(25-$breite,$zelle,$summe[$sum_paar]->sum.' : '.$summe[$sum_paar+1]->sum,1,0,'C');
	if (($runden_modus == 4 OR $runden_modus == 5) AND ($summe[$sum_paar]->sum == $summe[$sum_paar+1]->sum) AND ($summe[$sum_paar]->sum > 0)) $remis_com = 1; else $remis_com = 0;
	$sum_paar = $sum_paar+2;
}
else { $pdf->Cell(25-$breite,$zelle,' --- ',1,0,'C'); }

	$pdf->Cell(8-$breite,$zelle,$paar[$z]->gtln,1,0,'C');
	$pdf->Cell(50-$breite,$zelle,utf8_decode($paar[$z]->gname),1,0,'C');

	if (isset($dwzgespielt[$z2]) AND $dwzgespielt[$z2]->runde == ($x+1) AND $dwzgespielt[$z2]->paar == ($y+1) AND $dwzgespielt[$z2]->dg == 1 AND $paar[$z]->hmnr !=0 AND $paar[$z]->gmnr != 0)
		{ 	if ($params['dwz_date'] == '0000-00-00') $pdf->Cell(12-$breite,$zelle,round($dwzgespielt[$z2]->gdwz),1,0,'C');
			else $pdf->Cell(12-$breite,$zelle,round($dwzgespielt[$z2]->gstart_dwz),1,0,'C'); 
			$z2++;
		}
		elseif (isset($dwz[($paar[$z]->gtln)])) { $pdf->Cell(12-$breite,$zelle,round($dwz[($paar[$z]->gtln)]),1,0,'C'); }
		else { $pdf->Cell(12-$breite,$zelle,'',1,0,'C');}
if ($remis_com == 1) { 
	$pdf->Cell(1,$zelle,'',0,1);
	$remis_com = 0; 
	$pdf->Cell(10,$zelle,' ',0,0);
	$pdf->Cell(8-$breite,$zelle,$paar[$z]->paar,1,0,'C');
	$ztext = "";
	if ($paar[$z]->ko_decision == 1) {
		if ($paar[$z]->wertpunkte > $paar[$z]->gwertpunkte) $ztext = JText::_('ROUND_DECISION_WP_HEIM')." ".$paar[$z]->wertpunkte." : ".$paar[$z]->gwertpunkte." für ".$paar[$z]->hname; 
			else $ztext = JText::_('ROUND_DECISION_WP_GAST')." ".$paar[$z]->gwertpunkte." : ".$paar[$z]->wertpunkte." für ".$paar[$z]->gname; }
	if ($paar[$z]->ko_decision == 2) $ztext = JText::_('ROUND_DECISION_BLITZ_HEIM')." ".$paar[$z]->hname;
	if ($paar[$z]->ko_decision == 3) $ztext = JText::_('ROUND_DECISION_BLITZ_GAST')." ".$paar[$z]->gname; 
	if ($paar[$z]->ko_decision == 4) $ztext = JText::_('ROUND_DECISION_LOS_HEIM')." ".$paar[$z]->hname;
	if ($paar[$z]->ko_decision == 5) $ztext = JText::_('ROUND_DECISION_LOS_GAST')." ".$paar[$z]->gname; 		
	$pdf->SetFont('Times','',$font);
	$pdf->Cell($tbreite,$zelle,utf8_decode($ztext),'TBR',0,'C');
	$pdf->SetFont('Times','',$font);
}
if ($paar[$z]->comment != "") { 
	$paar[$z]->comment = str_replace($a_html,$a_pdf,$paar[$z]->comment);
	$a_comment = explode('<br>',$paar[$z]->comment);
	foreach ($a_comment as $comment) { 
		$pdf->Cell(1,$zelle,'',0,1);
		$pdf->Cell(10,$zelle,' ',0,0);
		$pdf->Cell(8-$breite,$zelle,$paar[$z]->paar,1,0,'C');
		$ztext = JText::_('PAAR_COMMENT').$comment; 		
		$pdf->SetFont('Times','',$font);
		$pdf->Cell($tbreite,$zelle,utf8_decode($ztext),'TBR',0,'C');
		$pdf->SetFont('Times','',$font); }
}
$z++;
	$pdf->Ln();
}
	//$pdf->Ln();
	$pdf->Ln();
}
}
////////////////////
// zweiter Durchgang
////////////////////
if ( $liga[0]->durchgang > 1) {
// Rundenschleife

$pdf->SetFont('Times','',$head_font-1);
	//$pdf->Ln();
	$pdf->Cell(10,15,' ',0,0);
	if ( $liga[0]->durchgang == 2) $pdf->Cell(80,15,utf8_decode(JText::_('PAAR_RUECK')),0,1,'L');
	else $pdf->Cell(80,15,utf8_decode(JText::_('PAAR_DG'))." 2",0,1,'L');
	
for ($x=0; $x< ($liga[0]->runden); $x++){

if ($pdf->GetY() > $lspalte_paar) {
		$pdf->AddPage();
	}
if (isset($termin[$term]) AND $termin[$term]->nr == ($x+1+$liga[0]->runden)) { if ($termin[$term]->datum > 0) { 
	$datum = ', '.JHTML::_('date',  $termin[$term]->datum, JText::_('DATE_FORMAT_CLM_F')); 
	if(isset($termin[$term]->startzeit) and $termin[$term]->startzeit != '00:00:00') { $datum .= '  '.substr($termin[$term]->startzeit,0,5).' Uhr'; } 		
	else $datum = ''; }
	$term++; } 
 
	$pdf->SetFont('Times','',$head_font-1);
	$pdf->Cell(10,15,' ',0,0);
	$pdf->Cell(173-(8*$breite),$zelle,utf8_decode($termin[$term-1]->name).utf8_decode($datum),1,0,'L');
	$pdf->Ln();

	$pdf->SetFont('Times','',$font);
	$pdf->Cell(10,15,' ',0,0);
	$pdf->Cell(8-$breite,$zelle,utf8_decode(JText::_('PAAR')),1,0,'C');
	$pdf->Cell(8-$breite,$zelle,utf8_decode(JText::_('TLN')),1,0,'C');
	$pdf->Cell(50-$breite,$zelle,utf8_decode(JText::_('HOME')),1,0,'C');
	$pdf->Cell(12-$breite,$zelle,utf8_decode(JText::_('DWZ')),1,0,'C');
	$pdf->Cell(25-$breite,$zelle,utf8_decode(JText::_('RESULT')),1,0,'C');
	$pdf->Cell(8-$breite,$zelle,utf8_decode(JText::_('TLN')),1,0,'C');
	$pdf->Cell(50-$breite,$zelle,utf8_decode(JText::_('GUEST')),1,0,'C');
	$pdf->Cell(12-$breite,$zelle,utf8_decode(JText::_('DWZ')),1,0,'C');
	$pdf->Ln();

// Teilnehmerschleife 
for ($y=0; $y< ($liga[0]->teil)/2; $y++){
	if ($paar[$z]->runde > ($x+1)) break;
	if (!isset($paar[$z])) break; 	
	$pdf->SetFont('Times','',$font);
	$pdf->Cell(10,15,' ',0,0);
	$pdf->Cell(8-$breite,$zelle,$paar[$z]->paar,1,0,'C');
	$pdf->Cell(8-$breite,$zelle,$paar[$z]->tln_nr,1,0,'C');
	$pdf->Cell(50-$breite,$zelle,utf8_decode($paar[$z]->hname),1,0,'C');

	if (isset($dwzgespielt[$z2]) AND $dwzgespielt[$z2]->runde == ($x+1) AND $dwzgespielt[$z2]->paar == ($y+1) AND $dwzgespielt[$z2]->dg == 2 AND $paar[$z]->hmnr !=0 AND $paar[$z]->gmnr != 0)
		{ $pdf->Cell(12-$breite,$zelle,round($dwzgespielt[$z2]->dwz),1,0,'C'); }
		elseif (isset($dwz[($paar[$z]->htln)])) { $pdf->Cell(12-$breite,$zelle,round($dwz[($paar[$z]->htln)]),1,0,'C');}
		else { $pdf->Cell(12-$breite,$zelle,'',1,0,'C');}
// Wenn Paarung existiert dann Ergebnis-Summen anzeigen
while ( $summe[$sum_paar]->runde < ($x+1) ) $sum_paar++;
if ( $summe[$sum_paar]->runde == ($x+1) AND $summe[$sum_paar]->paarung == ($y+1)) {
	$pdf->Cell(25-$breite,$zelle,$summe[$sum_paar]->sum.' : '.$summe[$sum_paar+1]->sum,1,0,'C');
	$sum_paar = $sum_paar+2;
}
else { $pdf->Cell(25-$breite,$zelle,' --- ',1,0,'C'); }

	$pdf->Cell(8-$breite,$zelle,$paar[$z]->gtln,1,0,'C');
	$pdf->Cell(50-$breite,$zelle,utf8_decode($paar[$z]->gname),1,0,'C');

	if (isset($dwzgespielt[$z2]) AND $dwzgespielt[$z2]->runde == ($x+1) AND $dwzgespielt[$z2]->paar == ($y+1) AND $dwzgespielt[$z2]->dg == 1 AND $paar[$z]->hmnr !=0 AND $paar[$z]->gmnr != 0)
		{ 	$pdf->Cell(12-$breite,$zelle,round($dwzgespielt[$z2]->gdwz),1,0,'C');
			$z2++;
		}
		elseif (isset($dwz[($paar[$z]->gtln)])) { $pdf->Cell(12-$breite,$zelle,round($dwz[($paar[$z]->gtln)]),1,0,'C');}
		else { $pdf->Cell(12-$breite,$zelle,'',1,0,'C');}
$z++;
	$pdf->Ln();
}
	//$pdf->Ln();
	$pdf->Ln();
}
}
////////////////////
// dritter Durchgang
////////////////////
if ( $liga[0]->durchgang > 2) {
// Rundenschleife

$pdf->SetFont('Times','',$head_font-1);
	//$pdf->Ln();
	$pdf->Cell(10,15,' ',0,0);
	$pdf->Cell(80,15,utf8_decode(JText::_('PAAR_DG'))." 3",0,1,'L');
	
for ($x=0; $x< ($liga[0]->runden); $x++){

if ($pdf->GetY() > $lspalte_paar) {
		$pdf->AddPage();
	}
if (isset($termin[$term]) AND $termin[$term]->nr == ($x+1+(2 * $liga[0]->runden))) { if ($termin[$term]->datum > 0) {
	$datum = ', '.JHTML::_('date',  $termin[$term]->datum, JText::_('DATE_FORMAT_CLM_F')); 
	if(isset($termin[$term]->startzeit) and $termin[$term]->startzeit != '00:00:00') { $datum .= '  '.substr($termin[$term]->startzeit,0,5).' Uhr'; } 		
	else $datum = ''; }
	$term++; } 
 
	$pdf->SetFont('Times','',$head_font-1);
	$pdf->Cell(10,15,' ',0,0);
	$pdf->Cell(173-(8*$breite),$zelle,utf8_decode($termin[$term-1]->name).utf8_decode($datum),1,0,'L');
	$pdf->Ln();

	$pdf->SetFont('Times','',$font);
	$pdf->Cell(10,15,' ',0,0);
	$pdf->Cell(8-$breite,$zelle,utf8_decode(JText::_('PAAR')),1,0,'C');
	$pdf->Cell(8-$breite,$zelle,utf8_decode(JText::_('TLN')),1,0,'C');
	$pdf->Cell(50-$breite,$zelle,utf8_decode(JText::_('HOME')),1,0,'C');
	$pdf->Cell(12-$breite,$zelle,utf8_decode(JText::_('DWZ')),1,0,'C');
	$pdf->Cell(25-$breite,$zelle,utf8_decode(JText::_('RESULT')),1,0,'C');
	$pdf->Cell(8-$breite,$zelle,utf8_decode(JText::_('TLN')),1,0,'C');
	$pdf->Cell(50-$breite,$zelle,utf8_decode(JText::_('GUEST')),1,0,'C');
	$pdf->Cell(12-$breite,$zelle,utf8_decode(JText::_('DWZ')),1,0,'C');
	$pdf->Ln();

// Teilnehmerschleife 
for ($y=0; $y< ($liga[0]->teil)/2; $y++){
	if ($paar[$z]->runde > ($x+1)) break;
	if (!isset($paar[$z])) break; 	
	$pdf->SetFont('Times','',$font);
	$pdf->Cell(10,15,' ',0,0);
	$pdf->Cell(8-$breite,$zelle,$paar[$z]->paar,1,0,'C');
	$pdf->Cell(8-$breite,$zelle,$paar[$z]->tln_nr,1,0,'C');
	$pdf->Cell(50-$breite,$zelle,utf8_decode($paar[$z]->hname),1,0,'C');

	if (isset($dwzgespielt[$z2]) AND $dwzgespielt[$z2]->runde == ($x+1) AND $dwzgespielt[$z2]->paar == ($y+1) AND $dwzgespielt[$z2]->dg == 2 AND $paar[$z]->hmnr !=0 AND $paar[$z]->gmnr != 0)
		{ $pdf->Cell(12-$breite,$zelle,round($dwzgespielt[$z2]->dwz),1,0,'C'); }
		elseif (isset($dwz[($paar[$z]->htln)])) { $pdf->Cell(12-$breite,$zelle,round($dwz[($paar[$z]->htln)]),1,0,'C');}
		else { $pdf->Cell(12-$breite,$zelle,'',1,0,'C');}
// Wenn Paarung existiert dann Ergebnis-Summen anzeigen
while ( $summe[$sum_paar]->runde < ($x+1) ) $sum_paar++;
if ( $summe[$sum_paar]->runde == ($x+1) AND $summe[$sum_paar]->paarung == ($y+1)) {
	$pdf->Cell(25-$breite,$zelle,$summe[$sum_paar]->sum.' : '.$summe[$sum_paar+1]->sum,1,0,'C');
	$sum_paar = $sum_paar+2;
}
else { $pdf->Cell(25-$breite,$zelle,' --- ',1,0,'C'); }

	$pdf->Cell(8-$breite,$zelle,$paar[$z]->gtln,1,0,'C');
	$pdf->Cell(50-$breite,$zelle,utf8_decode($paar[$z]->gname),1,0,'C');

	if (isset($dwzgespielt[$z2]) AND $dwzgespielt[$z2]->runde == ($x+1) AND $dwzgespielt[$z2]->paar == ($y+1) AND $dwzgespielt[$z2]->dg == 1 AND $paar[$z]->hmnr !=0 AND $paar[$z]->gmnr != 0)
		{ 	$pdf->Cell(12-$breite,$zelle,round($dwzgespielt[$z2]->gdwz),1,0,'C');
			$z2++;
		}
		elseif (isset($dwz[($paar[$z]->gtln)])) { $pdf->Cell(12-$breite,$zelle,round($dwz[($paar[$z]->gtln)]),1,0,'C');}
		else { $pdf->Cell(12-$breite,$zelle,'',1,0,'C');}
$z++;
	$pdf->Ln();
}
	//$pdf->Ln();
	$pdf->Ln();
}
}
////////////////////
// vierter Durchgang
////////////////////
if ( $liga[0]->durchgang > 3) {
// Rundenschleife

$pdf->SetFont('Times','',$head_font-1);
	//$pdf->Ln();
	$pdf->Cell(10,15,' ',0,0);
	$pdf->Cell(80,15,utf8_decode(JText::_('PAAR_DG'))." 4",0,1,'L');
	
for ($x=0; $x< ($liga[0]->runden); $x++){

if ($pdf->GetY() > $lspalte_paar) {
		$pdf->AddPage();
	}
if (isset($termin[$term]) AND $termin[$term]->nr == ($x+1+(3 * $liga[0]->runden))) { if ($termin[$term]->datum > 0) {
	$datum = ', '.JHTML::_('date',  $termin[$term]->datum, JText::_('DATE_FORMAT_CLM_F')); 
	if(isset($termin[$term]->startzeit) and $termin[$term]->startzeit != '00:00:00') { $datum .= '  '.substr($termin[$term]->startzeit,0,5).' Uhr'; } 		
	else $datum = ''; }
	$term++; } 
	$pdf->SetFont('Times','',$head_font-1);
	$pdf->Cell(10,15,' ',0,0);
	$pdf->Cell(173-(8*$breite),$zelle,utf8_decode($termin[$term-1]->name).utf8_decode($datum),1,0,'L');
	$pdf->Ln();

	$pdf->SetFont('Times','',$font);
	$pdf->Cell(10,15,' ',0,0);
	$pdf->Cell(8-$breite,$zelle,utf8_decode(JText::_('PAAR')),1,0,'C');
	$pdf->Cell(8-$breite,$zelle,utf8_decode(JText::_('TLN')),1,0,'C');
	$pdf->Cell(50-$breite,$zelle,utf8_decode(JText::_('HOME')),1,0,'C');
	$pdf->Cell(12-$breite,$zelle,utf8_decode(JText::_('DWZ')),1,0,'C');
	$pdf->Cell(25-$breite,$zelle,utf8_decode(JText::_('RESULT')),1,0,'C');
	$pdf->Cell(8-$breite,$zelle,utf8_decode(JText::_('TLN')),1,0,'C');
	$pdf->Cell(50-$breite,$zelle,utf8_decode(JText::_('GUEST')),1,0,'C');
	$pdf->Cell(12-$breite,$zelle,utf8_decode(JText::_('DWZ')),1,0,'C');
	$pdf->Ln();

// Teilnehmerschleife 
for ($y=0; $y< ($liga[0]->teil)/2; $y++){
	if ($paar[$z]->runde > ($x+1)) break;
	if (!isset($paar[$z])) break; 	
	$pdf->SetFont('Times','',$font);
	$pdf->Cell(10,15,' ',0,0);
	$pdf->Cell(8-$breite,$zelle,$paar[$z]->paar,1,0,'C');
	$pdf->Cell(8-$breite,$zelle,$paar[$z]->tln_nr,1,0,'C');
	$pdf->Cell(50-$breite,$zelle,utf8_decode($paar[$z]->hname),1,0,'C');

	if (isset($dwzgespielt[$z2]) AND $dwzgespielt[$z2]->runde == ($x+1) AND $dwzgespielt[$z2]->paar == ($y+1) AND $dwzgespielt[$z2]->dg == 2 AND $paar[$z]->hmnr !=0 AND $paar[$z]->gmnr != 0)
		{ $pdf->Cell(12-$breite,$zelle,round($dwzgespielt[$z2]->dwz),1,0,'C'); }
		elseif (isset($dwz[($paar[$z]->htln)])) { $pdf->Cell(12-$breite,$zelle,round($dwz[($paar[$z]->htln)]),1,0,'C');}
		else { $pdf->Cell(12-$breite,$zelle,'',1,0,'C');}
// Wenn Paarung existiert dann Ergebnis-Summen anzeigen
while ( $summe[$sum_paar]->runde < ($x+1) ) $sum_paar++;
if ( $summe[$sum_paar]->runde == ($x+1) AND $summe[$sum_paar]->paarung == ($y+1)) {
	$pdf->Cell(25-$breite,$zelle,$summe[$sum_paar]->sum.' : '.$summe[$sum_paar+1]->sum,1,0,'C');
	$sum_paar = $sum_paar+2;
}
else { $pdf->Cell(25-$breite,$zelle,' --- ',1,0,'C'); }

	$pdf->Cell(8-$breite,$zelle,$paar[$z]->gtln,1,0,'C');
	$pdf->Cell(50-$breite,$zelle,utf8_decode($paar[$z]->gname),1,0,'C');

	if (isset($dwzgespielt[$z2]) AND $dwzgespielt[$z2]->runde == ($x+1) AND $dwzgespielt[$z2]->paar == ($y+1) AND $dwzgespielt[$z2]->dg == 1 AND $paar[$z]->hmnr !=0 AND $paar[$z]->gmnr != 0)
		{ 	$pdf->Cell(12-$breite,$zelle,round($dwzgespielt[$z2]->gdwz),1,0,'C');
			$z2++;
		}
		elseif (isset($dwz[($paar[$z]->gtln)])) { $pdf->Cell(12-$breite,$zelle,round($dwz[($paar[$z]->gtln)]),1,0,'C');}
		else { $pdf->Cell(12-$breite,$zelle,'',1,0,'C');}
$z++;
	$pdf->Ln();
}
	//$pdf->Ln();
	$pdf->Ln();
}
}

$pdf->Output(utf8_decode(JText::_('PAAR_OVERVIEW')).' '.utf8_decode($liga[0]->name).'.pdf','D');

?>
