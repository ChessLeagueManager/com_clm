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

$turnierid		= JRequest::getInt('turnier','1');
$config			= clm_core::$db->config();

$turParams = new JRegistry();
$turParams->loadString($this->turnier->params);

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
$br01 = 10;
$br02 = 10;
$br03 = 50;
$br04 = 14;
$br05 = 10;
$br06 = 10;
$br07 = 6;
$br08 = 14;
$br09 = 6;
// Fontgröße Standard = 10
$font = 10;

// Datum der Erstellung
$date = JFactory::getDate();
$now = $date->toSQL();

$pdf=new PDF();
$pdf->AliasNbPages();

// Alle Spieler durchlaufen
$p=0;
$p1=false;
foreach ($this->players as $key => $value) {
	$p++; // rowCount
//Anzahl Spieler pro Seite
	if ($p > 36) $p = 1;
	if ($p == 1) {
$pdf->AddPage();
$pdf->SetFont('Times','',7);
	$pdf->Cell(10,3,' ',0,0);
	$pdf->Cell(175,3,utf8_decode(JText::_('WRITTEN')).' '.utf8_decode(JText::_('ON_DAY')).' '.utf8_decode(JHTML::_('date',  $now, JText::_('DATE_FORMAT_CLM_PDF'))),0,1,'R');
	
$pdf->SetFont('Times','',14);
	$pdf->Cell(10,15,' ',0,0);
	$pdf->Cell(150,15,utf8_decode($this->turnier->name).": ".utf8_decode(JText::_('TOURNAMENT_DWZ')),0,1,'L');
		
$pdf->SetFont('Times','',$font);
$pdf->SetTextColor(255);
$pdf->SetFillColor(0);
	$pdf->Cell($br00,$zelle," ",0,0,'C');
	$pdf->Cell($br01,$zelle,JText::_('TOURNAMENT_NUMBERABB'),1,0,'C',1);
	if ($turParams->get('displayPlayerTitle', 1) == 1) {
		$pdf->Cell($br02,$zelle,JText::_('TOURNAMENT_TITLE'),1,0,'C',1); }
	$pdf->Cell($br03,$zelle,JText::_('TOURNAMENT_PLAYERNAME'),1,0,'L',1);
	$pdf->Cell($br04,$zelle,JText::_('TOURNAMENT_RATING'),1,0,'L',1);
	$pdf->Cell($br05,$zelle,JText::_('DWZ_W'),1,0,'C',1);
	$pdf->Cell($br06,$zelle,JText::_('DWZ_WE_PDF'),1,0,'C',1); 
	$pdf->Cell($br07,$zelle,JText::_('DWZ_EF_PDF'),1,0,'C',1); 
	$pdf->Cell($br04,$zelle,JText::_('DWZ_RATING'),1,0,'C',1);
	$pdf->Cell($br04,$zelle,JText::_('DWZ_LEVEL'),1,0,'C',1); 
	$pdf->Cell($br08,$zelle,JText::_('DWZ_POINTS'),1,0,'C',1); 
	$pdf->Cell($br04,$zelle,JText::_('DWZ_NEW_PDF'),1,0,'C',1); 
	$pdf->Cell($br09,$zelle,JText::_('DWZ_DIFF'),1,0,'C',1);
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
	$pdf->Cell($br01,$zelle,$value->snr,1,0,'C',1);
	if ($turParams->get('displayPlayerTitle', 1) == 1) {
		$pdf->Cell($br02,$zelle,$value->titel,1,0,'C',1); }
	$pdf->Cell($br03,$zelle,utf8_decode($value->name),1,0,'L',1);
	if ($value->start_dwz == 0) $pdf->Cell($br04,$zelle,'-',1,0,'C',1);
	else $pdf->Cell($br04,$zelle,$value->start_dwz,1,0,'C',1); 
	$pdf->Cell($br05,$zelle,$value->Punkte,1,0,'C',1); 
	$pdf->Cell($br06,$zelle,$value->We,1,0,'C',1); 
	$pdf->Cell($br07,$zelle,$value->EFaktor,1,0,'C',1); 
	if ($value->Leistung == 0) $pdf->Cell($br04,$zelle,'-',1,0,'C',1);
	else $pdf->Cell($br04,$zelle,$value->Leistung,1,0,'C',1); 
	if ($value->Niveau == 0) $pdf->Cell($br04,$zelle,'-',1,0,'C',1);
	else $pdf->Cell($br04,$zelle,$value->Niveau,1,0,'C',1); 
	$Pkt = explode (".", $value->Punkte);
	if ($Pkt[1] != "0") {
		if ($Pkt[0] != "0") { 
			$Partien = $Pkt[0].chr(189).' / '.$value->Partien;
		} else { 
			$Partien = chr(189).' / '.$value->Partien;
		}
	} else { 
		$Partien = $Pkt[0].'  / '.$value->Partien;
	} 
	$pdf->Cell($br08,$zelle,$Partien,1,0,'C',1); 
	if ($value->DWZ == 0) $pdf->Cell($br04,$zelle,'-',1,0,'C',1);
	else $pdf->Cell($br04,$zelle,$value->DWZ,1,0,'C',1); 
	if ($value->DWZ == 0 OR $value->start_dwz == 0) 	$pdf->Cell($br09,$zelle,'-',1,0,'C',1); 
	else $pdf->Cell($br09,$zelle,($value->DWZ - $value->start_dwz),1,0,'C',1); 
	$pdf->Cell(1,$zelle," ",0,1,'C');
}	

// Ausgabe
$pdf->Output(utf8_decode(JText::_('TOURNAMENT_DWZ_PDF')).' '.utf8_decode($this->turnier->name).'.pdf','D');


?>
