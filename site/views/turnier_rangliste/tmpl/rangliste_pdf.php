<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team  All rights reserved
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

$heim = array(1 => "W", 0 => "S");
	
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
$br00 = 8;
$br01 = 8;
$br02 = 8;
$br03 = 51 - ($this->turnier->dg * 1);
$br04 = 10;
$br11 = 8 + ($this->turnier->dg * 2);
$br91 = 10;
$br92 = 10;
// Fontgröße Standard = 10
$font = 10;

// Datum der Erstellung
$date =JFactory::getDate();
$now = $date->toSQL();

$pdf=new PDF();
$pdf->AliasNbPages();

// Seitenformat bestimmen
$breite = $br00 + $br01 + $br03 + $br04 + ($this->turnier->runden * $br11) + $br91;
if ($turParams->get('displayPlayerSnr', 1) == 1) $breite = $breite + $br02;
for ($f=1; $f<=3; $f++) {
	$fwFieldName = 'tiebr'.$f;
	if ($this->turnier->$fwFieldName > 0 AND $this->turnier->$fwFieldName < 50 ) { $breite = $breite + $br92; }
}
if ($breite < 185) {$zanz = 36; $sformat = "P";}
else {$zanz = 22; $sformat = "L";}

if ($this->turnier->typ == 1) { // CH-System

// alle Spieler durchgehen
$p=0;
$p1=false;
$p2=0;
for ($p=0; $p<$this->turnier->playersCount; $p++) {
	$p2++; // rowCount
//Anzahl gemeldetet Spieler
	if ($p2 > $zanz) $p2 = 1;
	if ($p2 == 1) {
// header
$pdf->AddPage($sformat);
$pdf->SetFont('Times','',7);
	$pdf->Cell($br00,3,' ',0,0);
	$pdf->Cell(175,3,utf8_decode(JText::_('WRITTEN')).' '.utf8_decode(JText::_('ON_DAY')).' '.utf8_decode(JHTML::_('date',  $now, JText::_('DATE_FORMAT_CLM_PDF'))),0,1,'R');
	
$pdf->SetFont('Times','',14);
	$pdf->Cell($br00,15,' ',0,0);
	if (isset($this->turnier->spRangName) AND $this->turnier->spRangName > '')
		$pdf->Cell(150,15,utf8_decode($this->turnier->name).": ".utf8_decode($this->turnier->spRangName)." ".utf8_decode(JText::_('TOURNAMENT_RANKING')),0,1,'L');
	else
		$pdf->Cell(150,15,utf8_decode($this->turnier->name).": ".utf8_decode(JText::_('TOURNAMENT_RANKING')),0,1,'L');
		
$pdf->SetFont('Times','',$font);
$pdf->SetTextColor(255);
$pdf->SetFillColor(0);
	$pdf->Cell($br00,$zelle," ",0,0,'C');
	$pdf->Cell($br01,$zelle,JText::_('TOURNAMENT_RANKABB'),1,0,'C',1);
	if ($turParams->get('displayPlayerSnr', 1) == 1) {
		$pdf->Cell($br02,$zelle,JText::_('TOURNAMENT_NUMBERABB'),1,0,'C',1); }
	$pdf->Cell($br03,$zelle,JText::_('TOURNAMENT_PLAYERNAME'),1,0,'L',1);
	$pdf->Cell($br04,$zelle,JText::_('TOURNAMENT_TWZ'),1,0,'C',1);
	for ($rnd=1; $rnd<=$this->turnier->runden; $rnd++) {
		$pdf->Cell($br11,$zelle,$rnd,1,0,'C',1); }
	$pdf->Cell($br91,$zelle,JText::_('TOURNAMENT_POINTS_ABB'),1,0,'C',1); 
	// mgl. Feinwertungen
	for ($f=1; $f<=3; $f++) {
		$fwFieldName = 'tiebr'.$f;
		if ($this->turnier->$fwFieldName > 0 AND $this->turnier->$fwFieldName < 50) {
			$czelle = JText::_('TOURNAMENT_TIEBR_ABB_'.$this->turnier->$fwFieldName);
			if ($pdf->GetStringWidth($czelle) > $br92) $czelle = JText::_('TOURNAMENT_TIEBR_ABB_'.$this->turnier->$fwFieldName.'_PDF');
			$pdf->Cell($br92,$zelle,$czelle,1,0,'C',1); }
		}
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
	$pdf->Cell($br00,$zelle," ",0,0,'C');
	$pdf->Cell($br01,$zelle,CLMText::getPosString($this->players[$p]->rankingPos),1,0,'C',1);
	if ($turParams->get('displayPlayerSnr', 1) == 1) {
		$pdf->Cell($br02,$zelle,$this->players[$p]->snr,1,0,'C',1); }
	$pdf->Cell($br03,$zelle,utf8_decode($this->players[$p]->name),1,0,'L',1);
	$pdf->Cell($br04,$zelle,$this->players[$p]->twz,1,0,'C',1); 
	for ($rnd=1; $rnd<=$this->turnier->runden; $rnd++) {
		if (isset($this->matrix[$this->players[$p]->snr][$rnd]->ergebnis)) {
		  if (isset($this->posToPlayers[$this->matrix[$this->players[$p]->snr][$rnd]->gegner])) {
			if (substr(CLMText::getResultString($this->matrix[$this->players[$p]->snr][$rnd]->ergebnis),0,7) == '&frac12') {
				$pdf->Cell($br11,$zelle,$this->posToPlayers[$this->matrix[$this->players[$p]->snr][$rnd]->gegner].$heim[$this->matrix[$this->players[$p]->snr][$rnd]->heim].chr(189),1,0,'C',1);
			} else {
				$pdf->Cell($br11,$zelle,$this->posToPlayers[$this->matrix[$this->players[$p]->snr][$rnd]->gegner].$heim[$this->matrix[$this->players[$p]->snr][$rnd]->heim].CLMText::getResultString($this->matrix[$this->players[$p]->snr][$rnd]->ergebnis, 0),1,0,'C',1); 
			}
		  } else {
			if (substr(CLMText::getResultString($this->matrix[$this->players[$p]->snr][$rnd]->ergebnis),0,7) == '&frac12') {
				$pdf->Cell($br11,$zelle,$heim[$this->matrix[$this->players[$p]->snr][$rnd]->heim].chr(189),1,0,'C',1);
			} else {
				$pdf->Cell($br11,$zelle,$heim[$this->matrix[$this->players[$p]->snr][$rnd]->heim].CLMText::getResultString($this->matrix[$this->players[$p]->snr][$rnd]->ergebnis, 0),1,0,'C',1); 	  
			}
		  }
		}		
	    else { $pdf->Cell($br11,$zelle," ",1,0,'C',1); }
	}
	$pdf->Cell($br91,$zelle,$this->players[$p]->sum_punkte,1,0,'C',1); 
	// mgl. Feinwertungen
	for ($f=1; $f<=3; $f++) {
		$fwFieldName = 'tiebr'.$f;
		$plTiebrField = 'sumTiebr'.$f;
		if ($this->turnier->$fwFieldName > 0 AND $this->turnier->$fwFieldName < 50) {
			$pdf->Cell($br92,$zelle,CLMtext::tiebrFormat($this->turnier->$fwFieldName, $this->players[$p]->$plTiebrField),1,0,'C',1); 
		}
	}
	$pdf->Cell(1,$zelle," ",0,1,'C');
}	
}

elseif ($this->turnier->typ == 2) { // Vollrunde
// alle Spieler durchgehen
$p=0;
$p1=false;
$p2=0;
for ($p=0; $p<$this->turnier->teil; $p++) {
	$p2++; // rowCount
//Anzahl gemeldetet Spieler
	if ($p2 > $zanz) $p2 = 1;
	if ($p2 == 1) {
// header
$pdf->AddPage($sformat);
$pdf->SetFont('Times','',7);
	$pdf->Cell($br00,3,' ',0,0);
	$pdf->Cell(175,3,utf8_decode(JText::_('WRITTEN')).' '.utf8_decode(JText::_('ON_DAY')).' '.utf8_decode(JHTML::_('date',  $now, JText::_('DATE_FORMAT_CLM_PDF'))),0,1,'R');
	
$pdf->SetFont('Times','',14);
	$pdf->Cell($br00,15,' ',0,0);
	if (isset($this->turnier->spRangName) AND $this->turnier->spRangName > '')
		$pdf->Cell(150,15,utf8_decode($this->turnier->name).": ".utf8_decode($this->turnier->spRangName)." ".utf8_decode(JText::_('TOURNAMENT_RANKING')),0,1,'L');
	else
		$pdf->Cell(150,15,utf8_decode($this->turnier->name).": ".utf8_decode(JText::_('TOURNAMENT_RANKING')),0,1,'L');
		
$pdf->SetFont('Times','',$font);
$pdf->SetTextColor(255);
$pdf->SetFillColor(0);
	$pdf->Cell($br00,$zelle," ",0,0,'C');
	$pdf->Cell($br01,$zelle,JText::_('TOURNAMENT_RANKABB'),1,0,'C',1);
	if ($turParams->get('displayPlayerSnr', 1) == 1) {
		$pdf->Cell($br02,$zelle,JText::_('TOURNAMENT_NUMBERABB'),1,0,'C',1); }
	$pdf->Cell($br03,$zelle,JText::_('TOURNAMENT_PLAYERNAME'),1,0,'L',1);
	$pdf->Cell($br04,$zelle,JText::_('TOURNAMENT_TWZ'),1,0,'C',1);
	for ($rnd=1; $rnd<=$this->turnier->teil; $rnd++) {
		$pdf->Cell($br11,$zelle,$rnd,1,0,'C',1); }
	$pdf->Cell($br91,$zelle,JText::_('TOURNAMENT_POINTS_ABB'),1,0,'C',1); 
	// mgl. Feinwertungen
	for ($f=1; $f<=3; $f++) {
		$fwFieldName = 'tiebr'.$f;
		if ($this->turnier->$fwFieldName > 0 AND $this->turnier->$fwFieldName < 50) {
			$czelle = JText::_('TOURNAMENT_TIEBR_ABB_'.$this->turnier->$fwFieldName);
			while ($pdf->GetStringWidth($czelle) > $br92) $czelle = substr($czelle,0,strlen($czelle)-1);
			$pdf->Cell($br92,$zelle,$czelle,1,0,'C',1); }
		}
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
	$pdf->Cell($br00,$zelle," ",0,0,'C');
	$pdf->Cell($br01,$zelle,CLMText::getPosString($this->players[$p]->rankingPos),1,0,'C',1);
	if ($turParams->get('displayPlayerSnr', 1) == 1) {
		$pdf->Cell($br02,$zelle,$this->players[$p]->snr,1,0,'C',1); }
	$pdf->Cell($br03,$zelle,utf8_decode($this->players[$p]->name),1,0,'L',1);
	$pdf->Cell($br04,$zelle,$this->players[$p]->twz,1,0,'C',1); 
	for ($rnd=1; $rnd<=$this->turnier->teil; $rnd++) {
		if ($rnd == ($p+1)) { $pdf->Cell($br11,$zelle,"X",1,0,'C',1); }
		else {
		  $czelle = "";
		  for ($dg=1; $dg<=$this->turnier->dg; $dg++) {
			if ($dg > 1) $czelle .= "/"; 
			if (isset($this->matrix[$this->players[$p]->snr][$this->posToPlayers[$rnd]][$dg]->ergebnis)) {
				if (substr(CLMText::getResultString($this->matrix[$this->players[$p]->snr][$this->posToPlayers[$rnd]][$dg]->ergebnis),0,7) == '&frac12') {
					$czelle .= chr(189); }
				else {
					$czelle .= CLMText::getResultString($this->matrix[$this->players[$p]->snr][$this->posToPlayers[$rnd]][$dg]->ergebnis, 0); }
			}
			else { $czelle .= ""; } 
		  }
		  $pdf->Cell($br11,$zelle,$czelle,1,0,'C',1); 
		}
	}
	$pdf->Cell($br91,$zelle,$this->players[$p]->sum_punkte,1,0,'C',1); 
	// mgl. Feinwertungen
	for ($f=1; $f<=3; $f++) {
		$fwFieldName = 'tiebr'.$f;
		$plTiebrField = 'sumTiebr'.$f;
		if ($this->turnier->$fwFieldName > 0 AND $this->turnier->$fwFieldName < 50) {
			$pdf->Cell($br92,$zelle,CLMtext::tiebrFormat($this->turnier->$fwFieldName, $this->players[$p]->$plTiebrField),1,0,'C',1); }
		}
	$pdf->Cell(1,$zelle," ",0,1,'C');
}	
}

// Ausgabe
if (isset($this->turnier->spRangName) AND $this->turnier->spRangName > '')
	$pdf->Output(utf8_decode(JText::_('TOURNAMENT_RANKING')).' '.utf8_decode($this->turnier->name).' '.utf8_decode($this->turnier->spRangName).'.pdf','D');
else
	$pdf->Output(utf8_decode(JText::_('TOURNAMENT_RANKING')).' '.utf8_decode($this->turnier->name).'.pdf','D');

?>
