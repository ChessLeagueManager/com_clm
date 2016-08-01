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
$br1_6 = 170;
// Fontgröße Standard = 10
$font = 10;

// Datum der Erstellung
$date =JFactory::getDate();
$now = $date->toSQL();

$pdf=new PDF();
$pdf->AliasNbPages();

// alle Runden durchgehen
$print_round = 0;
$y_length = 300;
foreach ($this->rounds as $value) {	
	// published?
if ($value->published == 1) {
	$print_round++;
	if (($print_round == 1) OR ($pdf->GetY() > (280 - $y_length))) {
		$pdf->AddPage();
		$pdf->SetFont('Times','',7);
		$pdf->Cell(10,3,' ',0,0);
		$pdf->Cell(175,3,utf8_decode(JText::_('WRITTEN')).' '.utf8_decode(JText::_('ON_DAY')).' '.utf8_decode(JHTML::_('date',  $now, JText::_('DATE_FORMAT_CLM_PDF'))),0,1,'R');
	
		$pdf->SetFont('Times','',14);
		$pdf->Cell(10,15,' ',0,0);
		$heading = utf8_decode($this->turnier->name).": ".utf8_decode(JText::_('TOURNAMENT_PAIRINGLIST'));
		$pdf->Cell(150,15,$heading,0,1,'L');
	}
	if ($print_round == 1) $y_start = $pdf->GetY();
	$pdf->SetFont('Times','',$font);
	$pdf->Cell(1,$zelle," ",0,1,'C');
	$pdf->Cell($br00,$zelle,' ',0,0);
	$heading = utf8_decode($value->name);
	if ($value->datum != "0000-00-00" AND $turParams->get('displayRoundDate', 1) == 1) {
		$heading .= ', '.utf8_decode(JHTML::_('date',  $value->datum, JText::_('DATE_FORMAT_CLM_F'))); 
		if(isset($value->startzeit) and $value->startzeit != '00:00:00') { $heading .= '  '.substr($value->startzeit,0,5).' Uhr'; } 		
	}
	$pdf->Cell($br1_6,$zelle,$heading,1,1,'L');

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

	// alle Matches eintragen
	$pdf->SetFont('Times','',$font);
	$pdf->SetTextColor(0);
	$m=0; // CounterFlag für Farbe
	$nb=0; //Tischnummer
	foreach ($this->matches[$value->nr + (($value->dg - 1) * $this->turnier->runden)] as $matches) {
		$m++; // Farbe
		if ($m%2 != 0) { $pdf->SetFillColor(255); }
		else { $pdf->SetFillColor(240); }
		
		if ( ($matches->spieler != 0 AND $matches->gegner != 0) OR $matches->ergebnis != NULL) {
			$nb++;
			$pdf->Cell($br00,$zelle," ",0,0,'C');
			$pdf->Cell($br00,$zelle,$nb,1,0,'C',1);
			if ($this->turnier->typ != '3' AND $this->turnier->typ != '5') {
				if (isset($this->points[$value->nr + (($value->dg - 1) * $this->turnier->runden)][$matches->spieler])) { 
					$points = $this->points[$value->nr + (($value->dg - 1) * $this->turnier->runden)][$matches->spieler]; }
				else { $points = 0; }
				$pdf->Cell($br01,$zelle,utf8_decode($this->players[$matches->spieler]->name)." (".$points.")",1,0,'L',1);
			} else {
				$pdf->Cell($br01,$zelle,utf8_decode($this->players[$matches->spieler]->name),1,0,'L',1);
			}
			$pdf->Cell($br02,$zelle,$this->players[$matches->spieler]->twz,1,0,'C',1); 
			$pdf->Cell($br03,$zelle,"-",1,0,'C',1);
			if ($this->turnier->typ != '3' AND $this->turnier->typ != '5') {
				if (isset($this->points[$value->nr + (($value->dg - 1) * $this->turnier->runden)][$matches->gegner])) { 
					$points = $this->points[$value->nr + (($value->dg - 1) * $this->turnier->runden)][$matches->gegner]; }
				else { $points = 0; }
				$pdf->Cell($br04,$zelle,utf8_decode($this->players[$matches->gegner]->name)." (".$points.")",1,0,'L',1); 
			} else {
				$pdf->Cell($br04,$zelle,utf8_decode($this->players[$matches->gegner]->name),1,0,'L',1); 
			}
			$pdf->Cell($br05,$zelle,$this->players[$matches->gegner]->twz,1,0,'C',1); 
			if ($matches->ergebnis != NULL) {
				if ($matches->ergebnis == 2) { $ergebnis = chr(189).":".chr(189); }
				elseif ($matches->ergebnis == 9) { $ergebnis = "0:".chr(189); }
				elseif ($matches->ergebnis == 10) { $ergebnis = chr(189).":0"; }
				else { $ergebnis = CLMText::getResultString($matches->ergebnis); }
				if (($this->turnier->typ == 3 OR $this->turnier->typ == 5) AND ($matches->tiebrS > 0 OR $matches->tiebrG > 0)) {
						$ergebnis .= '  ('.$matches->tiebrS.':'.$matches->tiebrG.')'; 
					}
			} else $ergebnis = " ";
			$pdf->Cell($br06,$zelle,$ergebnis,1,0,'C',1); 
			$pdf->Cell(1,$zelle," ",0,1,'C');
		}	
	}
	if ($print_round == 1) $y_length = $pdf->GetY() - $y_start;
	}
	}
	
// Ausgabe
$pdf->Output(utf8_decode(JText::_('TOURNAMENT_PAIRINGLIST'))." ".utf8_decode($this->turnier->name).'.pdf','D');


?>
