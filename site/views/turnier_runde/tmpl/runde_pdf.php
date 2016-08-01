<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access');

$turnierid		= JRequest::getInt('turnier','1');
$config			= clm_core::$db->config();

$turParams = new clm_class_params($this->turnier->params);

require_once(JPATH_COMPONENT.DS.'includes'.DS.'fpdf.php');

class PDF extends FPDF
{
//Kopfzeile
function Header()
{
	require(JPATH_COMPONENT.DS.'includes'.DS.'pdf_header.php');
}
//Fusszeile
function Footer()
{
	require(JPATH_COMPONENT.DS.'includes'.DS.'pdf_footer.php');
}
}

// Zellenhöhe -> Standard 6
$zelle = 6;
// Zellenbreiten je Spalte
$breite = 0;
$br00 = 10;
$br01 = 50;
$br02 = 14;
$br03 = 10;
$br04 = 50;
$br05 = 14;
$br06 = 22;
// Fontgröße Standard = 10
$font = 10;

// Datum der Erstellung
$date =JFactory::getDate();
$now = $date->toSQL();

$pdf=new PDF();
$pdf->AliasNbPages();

// Anzahl der Mannschaften durchlaufen
$p=0;
$p1=false;
foreach ($this->matches as $key => $value) {
	$p++; // rowCount
//Anzahl Paarungen pro Seite
	if ($p > 36) $p = 1;
	if ($p == 1) {
$pdf->AddPage();
$pdf->SetFont('Times','',7);
	$pdf->Cell(10,3,' ',0,0);
	$pdf->Cell(175,3,utf8_decode(JText::_('WRITTEN')).' '.utf8_decode(JText::_('ON_DAY')).' '.utf8_decode(JHTML::_('date',  $now, JText::_('DATE_FORMAT_CLM_PDF'))),0,1,'R');
	
$pdf->SetFont('Times','',14);
	$pdf->Cell(10,15,' ',0,0);
	//$heading = utf8_decode($this->turnier->name).": ".utf8_decode(JText::_('TOURNAMENT_ROUND'))." ".$this->round->nr;
	$heading = utf8_decode($this->turnier->name).": ".utf8_decode($this->round->name);
	if ($this->round->datum != "0000-00-00" AND $turParams->get('displayRoundDate', 1) == 1) {
		$heading .=  ", ".utf8_decode(JHTML::_('date',  $this->round->datum, JText::_('DATE_FORMAT_CLM_F'))); 
		if(isset($this->round->startzeit) and $this->round->startzeit != '00:00:00') { $heading .= '  '.substr($this->round->startzeit,0,5).' Uhr'; }
	}
	$pdf->Cell(160,15,$heading,0,1,'L');
		
$pdf->SetFont('Times','',$font);
$pdf->SetTextColor(255);
$pdf->SetFillColor(0);
	$pdf->Cell($br00,$zelle," ",0,0,'C');
	$pdf->Cell($br00,$zelle,utf8_decode(JText::_('TOURNAMENT_TNR')),1,0,'C',1);
	$pdf->Cell($br01,$zelle,utf8_decode(JText::_('TOURNAMENT_WHITE')),1,0,'L',1);
	$pdf->Cell($br02,$zelle,utf8_decode(JText::_('TOURNAMENT_TWZ')),1,0,'C',1); 
	$pdf->Cell($br03,$zelle,"-",1,0,'C',1);
	$pdf->Cell($br04,$zelle,utf8_decode(JText::_('TOURNAMENT_BLACK')),1,0,'L',1); 
	$pdf->Cell($br05,$zelle,utf8_decode(JText::_('TOURNAMENT_TWZ')),1,0,'C',1); 
	$pdf->Cell($br06,$zelle,JText::_('RESULT'),1,0,'C',1);
	$pdf->Cell(1,$zelle," ",0,1,'C');
}
// Anzahl der Teilnehmer durchlaufen
$pdf->SetFont('Times','',$font);
$pdf->SetTextColor(0);
	if ($p1 == false) {
		$p1 = true; 
		$pdf->SetFillColor(255); }
	else {
		$p1 = false; 
		$pdf->SetFillColor(240); }	
if ( ($value->spieler != 0 AND $value->gegner != 0) OR $value->ergebnis != NULL) {

	$pdf->Cell($br00,$zelle," ",0,0,'C');
	$pdf->Cell($br00,$zelle,$value->brett,1,0,'C',1); 
	if ($this->turnier->typ != '3' AND $this->turnier->typ != '5') {
		if (isset($this->points[$value->spieler])) { $points = $this->points[$value->spieler]; }
		else { $points = 0; }
		$pdf->Cell($br01,$zelle,utf8_decode($value->wname)." (".$points.")",1,0,'L',1);
	} else {
		$pdf->Cell($br01,$zelle,utf8_decode($value->wname),1,0,'L',1);
	}
	$pdf->Cell($br02,$zelle,$value->wtwz,1,0,'C',1); 
	$pdf->Cell($br03,$zelle,"-",1,0,'C',1);
	if ($this->turnier->typ != '3' AND $this->turnier->typ != '5') {
		if (isset($this->points[$value->gegner])) { $points = $this->points[$value->gegner]; }
		else { $points = 0; }
		$pdf->Cell($br04,$zelle,utf8_decode($value->sname)." (".$points.")",1,0,'L',1); 
	} else {
		$pdf->Cell($br01,$zelle,utf8_decode($value->sname),1,0,'L',1);
	}
	$pdf->Cell($br05,$zelle,$value->stwz,1,0,'C',1); 
	if ($value->ergebnis != NULL) {
		if ($value->ergebnis == 2) { $ergebnis = chr(189).":".chr(189); }
		elseif ($value->ergebnis == 9) { $ergebnis = "0:".chr(189); }
		elseif ($value->ergebnis == 10) { $ergebnis = chr(189).":0"; }
		else { $ergebnis = CLMText::getResultString($value->ergebnis); }
		if (($this->turnier->typ == 3 OR $this->turnier->typ == 5) AND ($value->tiebrS > 0 OR $value->tiebrG > 0)) {
				$ergebnis .= '  ('.$value->tiebrS.':'.$value->tiebrG.')'; 
			}
	} else $ergebnis = " ";
	$pdf->Cell($br06,$zelle,$ergebnis,1,0,'C',1); 
	$pdf->Cell(1,$zelle," ",0,1,'C');
}	
}
// Ausgabe
$pdf->Output(utf8_decode(JText::_('TOURNAMENT_ROUND'))." ".$this->round->nr.' '.utf8_decode($this->turnier->name).'.pdf','D');


?>
