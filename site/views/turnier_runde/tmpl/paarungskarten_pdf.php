<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

$turnierid		= clm_core::$load->request_int('turnier',1);
$config			= clm_core::$db->config();

$turParams = new clm_class_params($this->turnier->params);

require_once(clm_core::$path.DS.'classes'.DS.'fpdf.php');

class PDF extends FPDF
{
//Kopfzeile
function Header()
{
	require(clm_core::$path.DS.'includes'.DS.'pdf_header.php');
}
//Fusszeile
function Footer()
{
	require(clm_core::$path.DS.'includes'.DS.'pdf_footer.php');
}
}

// Zellenhöhe -> Standard 6
$zelle = 6;
// Zellenbreiten je Spalte
$breite = 0;
$br00 = 2;
$br01 = 50;
$br02 = 2;
$br03 = 10;
$br04 = 4;
$br05 = 5;
$br39 = 39;
$brg  = 90;
$hog  = 58;
// Fontgröße Standard = 10
$font = 10;

// Datum der Erstellung
$date =Factory::getDate();
$now = $date->toSQL();

$pdf=new PDF();
$pdf->AliasNbPages();

// Anzahl der Mannschaften durchlaufen
$p=0;
$p1=false;

foreach ($this->matches as $key => $value) {
	$p++; // rowCount
//Anzahl Paarungen pro Seite
	if ($p > 8) $p = 1;
	if ($p == 1) {
$pdf->AddPage();
$pdf->SetFont('Times','',7);
	$pdf->Cell(10,3,' ',0,0);
	$pdf->Cell(175,3,clm_core::$load->utf8decode(Text::_('WRITTEN')).' '.clm_core::$load->utf8decode(Text::_('ON_DAY')).' '.clm_core::$load->utf8decode(HTMLHelper::_('date',  $now, Text::_('DATE_FORMAT_CLM_PDF'))),0,1,'R');
	
	$pdf->Cell(160,2,'',0,1,'L');
		
$pdf->SetFont('Times','',$font);
	$y = -30;
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
	
if ( ($value->spieler != 0 AND $value->gegner != 0) OR !is_null($value->ergebnis)) {

	if (is_null($value->wname)) $value->wname = '';
	if (is_null($value->sname)) $value->sname = '';
	if ($p % 2 != 0) $x = 10; else $x = 10 + $brg + 8;
	if ($x == 10) $y = $y + $hog;
	
$pdf->SetY($y);
$pdf->SetX($x);
$pdf->SetFont('Times','',$font);
	$pdf->Cell($brg,5,'',0,1,'L');	
$pdf->SetX($x);
	$pdf->Cell($br02,5,'','LT',0,'L');	
	$pdf->Cell($brg-$br02,5,clm_core::$load->utf8decode($this->turnier->name),'TR',1,'L');	
$pdf->SetX($x);
	$pdf->Cell($br02,1,'','L',0,'L');	
	$pdf->Cell($brg-$br02,1,'','R',1,'L');
$pdf->SetX($x);
	$pdf->Cell($br02,5,'','L',0,'L');	
	$pdf->Cell($brg-$br02,5,'Paarung in '.clm_core::$load->utf8decode($this->round->name),'R',1,'L');
$pdf->SetX($x);
$pdf->SetFont('Times','',$font+3);
	$pdf->Cell($br02,10,'','L',0,'L');	
	$pdf->Cell($brg-$br02,10,'Tisch '.$value->brett,'R',1,'L');
$pdf->SetX($x);
$pdf->SetFont('Times','',$font+2);
	$pdf->Cell($br04,5,'','L',0,'L');
	$pdf->BreakCell($br39,5,clm_core::$load->utf8decode($value->wname),'',0,'L');
	$pdf->Cell($br04,5,'-',0,0,'C');
	$pdf->BreakCell($br39,5,clm_core::$load->utf8decode($value->sname),'',0,'L');
	$pdf->Cell($br04,5,'','R',1,'L');
$pdf->SetX($x);
$pdf->SetFont('Times','',$font-1);
	$pdf->Cell($br04,5,'','L',0,'L');
	$pdf->BreakCell($br39,5,clm_core::$load->utf8decode($value->wverein),'',0,'L');
	$pdf->Cell($br04,5,'-',0,0,'C');
	$pdf->BreakCell($br39,5,clm_core::$load->utf8decode($value->sverein),'',0,'L');
	$pdf->Cell($br04,5,'','R',1,'L');
$pdf->SetX($x);
$pdf->SetFont('Times','',$font);
	$pdf->Cell($br02,2,'','L',0,'L');	
	$pdf->Cell($brg-$br02,2,'','R',1,'L');
$pdf->SetX($x);
	$pdf->Cell($br04,5,'','L',0,'L');
	$pdf->Cell($br39,5,'__________','',0,'R');
	$pdf->Cell($br04,5,'-',0,0,'C');
	$pdf->Cell($br39,5,'__________','',0,'L');
	$pdf->Cell($br04,5,'','R',1,'L');
$pdf->SetX($x);
$pdf->SetFont('Times','',$font-2);
	$pdf->Cell($br02,3,'','L',0,'L');	
	$pdf->Cell($brg-$br02,3,'Ergebnis','R',1,'C');	
$pdf->SetX($x);
$pdf->SetFont('Times','',$font);
	$pdf->Cell($br04,5,'','L',0,'L');
	$pdf->Cell($br39,5,'_______________','',0,'R');
	$pdf->Cell($br04,5,'-',0,0,'C');
	$pdf->Cell($br39,5,'_______________','',0,'L');
	$pdf->Cell($br04,5,'','R',1,'L');
$pdf->SetX($x);
$pdf->SetFont('Times','',$font-2);
	$pdf->Cell($br02,3,'','L',0,'L');	
	$pdf->Cell($brg-$br02,3,clm_core::$load->utf8decode('Bestätigung'),'R',1,'C');	
$pdf->SetX($x);
	$pdf->Cell($br02,2,'','LB',0,'L');	
	$pdf->Cell($brg-$br02,2,'','BR',1,'L');	
	
}	
}
// Ausgabe
//die();
$pdf->Output(clm_core::$load->utf8decode(Text::_('TOURNAMENT_ROUNDCARDS'))." ".$this->round->nr.' '.clm_core::$load->utf8decode($this->turnier->name).'.pdf','D');
exit;
?>
