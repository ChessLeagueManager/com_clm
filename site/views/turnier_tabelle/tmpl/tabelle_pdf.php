<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.cheesleaguemanager.de
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
$br01 = 10;
$br02 = 10;
$br03 = 50;
$br04 = 60;
$br05 = 10;
$br06 = 10;
$br92 = 10;
// Fontgröße Standard = 10
$font = 10;

// Datum der Erstellung
$date =JFactory::getDate();
$now = $date->toSQL();

$pdf=new PDF();
$pdf->AliasNbPages();

$p2=0;
$p1=false;
// alle Spieler durchgehen
for ($p=0; $p<$this->turnier->playersCount; $p++) {
	$p2++; // rowCount
//Anzahl Spieler pro Seite
	if ($p2 > 36) $p2 = 1;
	if ($p2 == 1) {
$pdf->AddPage();
$pdf->SetFont('Times','',7);
	$pdf->Cell(10,3,' ',0,0);
	$pdf->Cell(175,3,utf8_decode(JText::_('WRITTEN')).' '.utf8_decode(JText::_('ON_DAY')).' '.utf8_decode(JHTML::_('date',  $now, JText::_('DATE_FORMAT_CLM_PDF'))),0,1,'R');
	
$pdf->SetFont('Times','',14);
	$pdf->Cell(10,15,' ',0,0);
	if (isset($this->turnier->spRangName) AND $this->turnier->spRangName > '')
		$pdf->Cell(150,15,utf8_decode($this->turnier->name).": ".utf8_decode($this->turnier->spRangName)." ".utf8_decode(JText::_('TOURNAMENT_TABLE')),0,1,'L');
	else
		$pdf->Cell(150,15,utf8_decode($this->turnier->name).": ".utf8_decode(JText::_('TOURNAMENT_TABLE')),0,1,'L');
		
$pdf->SetFont('Times','',$font);
$pdf->SetTextColor(255);
$pdf->SetFillColor(0);
	$pdf->Cell($br00,$zelle," ",0,0,'C');
	$pdf->Cell($br01,$zelle,JText::_('TOURNAMENT_RANKABB'),1,0,'C',1);
	if ($turParams->get('displayPlayerTitle', 1) == 1) {
		$pdf->Cell($br02,$zelle,JText::_('TOURNAMENT_TITLE'),1,0,'C',1); }
	$pdf->Cell($br03,$zelle,JText::_('TOURNAMENT_PLAYERNAME'),1,0,'L',1);
	if ($turParams->get('displayPlayerClub', 1) == 1) {
		$pdf->Cell($br04,$zelle,JText::_('TOURNAMENT_CLUB'),1,0,'L',1); }
	$pdf->Cell($br05,$zelle,JText::_('TOURNAMENT_TWZ'),1,0,'C',1);
	$pdf->Cell($br06,$zelle,JText::_('TOURNAMENT_GAMES_ABB'),1,0,'C',1); 
	$pdf->Cell($br06,$zelle,JText::_('TOURNAMENT_POINTS_ABB'),1,0,'C',1); 
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
	if ($turParams->get('displayPlayerTitle', 1) == 1) {
		$pdf->Cell($br02,$zelle,$this->players[$p]->titel,1,0,'C',1); }
	$pdf->Cell($br03,$zelle,utf8_decode($this->players[$p]->name),1,0,'L',1);
	if ($turParams->get('displayPlayerClub', 1) == 1) {
		$pdf->Cell($br04,$zelle,utf8_decode($this->players[$p]->verein),1,0,'L',1); }
	$pdf->Cell($br05,$zelle,CLMText::formatRating($this->players[$p]->twz),1,0,'C',1); 
	$pdf->Cell($br06,$zelle,$this->players[$p]->anz_spiele,1,0,'C',1); 
	$pdf->Cell($br06,$zelle,$this->players[$p]->sum_punkte,1,0,'C',1); 
	for ($f=1; $f<=3; $f++) {
		$fwFieldName = 'tiebr'.$f;
		$plTiebrField = 'sumTiebr'.$f;
		if ($this->turnier->$fwFieldName > 0 AND $this->turnier->$fwFieldName < 50) {
			$pdf->Cell($br92,$zelle,CLMtext::tiebrFormat($this->turnier->$fwFieldName, $this->players[$p]->$plTiebrField),1,0,'C',1); }
		}
	$pdf->Cell(1,$zelle," ",0,1,'C');
}	

// Ausgabe
if (isset($this->turnier->spRangName) AND $this->turnier->spRangName > '')
	$pdf->Output(utf8_decode(JText::_('TOURNAMENT_TABLE')).' '.utf8_decode($this->turnier->name).' '.utf8_decode($this->turnier->spRangName).'.pdf','D');
else
	$pdf->Output(utf8_decode(JText::_('TOURNAMENT_TABLE')).' '.utf8_decode($this->turnier->name).'.pdf','D');

?>
