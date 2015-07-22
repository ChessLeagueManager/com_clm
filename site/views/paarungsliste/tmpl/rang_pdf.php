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

//require('fpdf.php');

$lid = JRequest::getInt( 'liga', '1' ); 
$sid = JRequest::getInt( 'saison','1');
$view = JRequest::getVar( 'view');
// Variablen ohne foreach setzen
$liga=$this->liga;
$punkte=$this->punkte;
$spielfrei=$this->spielfrei;
$dwzschnitt=$this->dwzschnitt;

$name_liga = $liga[0]->name;

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

// Array für DWZ Schnitt setzen
$dwz = array();
for ($y=1; $y< ($liga[0]->teil)+1; $y++){
	$dwz[$dwzschnitt[($y-1)]->tlnr] = $dwzschnitt[($y-1)]->dwz; }

// Spielfreie Teilnehmer finden
$diff = count($spielfrei[0]->tln_nr);

// Zellenhöhe -> Standard 6
$zelle = 6;
// Wert von Zellenbreite abziehen
// Bspl. für Standard (Null) für Liga mit 7 Runden und 1 Durchgang
$breite = 0;
// Fontgröße Standard = 12
$font = 12;
// Leere Zelle zum zentrieren
$leer = 4+(9-$rnd)-$breite;

// Datum der Erstellung
$date =JFactory::getDate();
$now = $date->toSQL();

$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Times','',16);

	$pdf->Cell(10,15,' ',0,0);
	$pdf->Cell(80,15,utf8_decode($liga[0]->name),0,1,'L');

$pdf->SetFont('Times','',$font);
	$pdf->Cell($leer,$zelle,' ',0,0,'L');
	$pdf->Cell(7-$breite,$zelle,'Rg'.$i,1,0,'C');
	$pdf->Cell(7-$breite,$zelle,'Tln'.$i,1,0,'C');
	$pdf->Cell(60-2*$breite,$zelle,'Mannschaft'.$i,1,0,'L');

// erster Durchgang
	for ($rnd=0; $rnd < $liga[0]->teil-$diff ; $rnd++) {
	$pdf->Cell(8-$breite,$zelle,$rnd+1,1,0,'C');
	}
// zweiter Durchgang
	if ($liga[0]->durchgang > 1) {
	for ($rnd=0; $rnd < $liga[0]->teil-$diff ; $rnd++) {
	$pdf->Cell(8-$breite,$zelle,$rnd+1,1,0,'C');
	}}
	$pdf->Cell(10-$breite,$zelle,'MP',1,0,'C');
	$pdf->Cell(10-$breite,$zelle,'BP',1,0,'C');

	$pdf->Ln();


// Anzahl der Teilnehmer durchlaufen
//for ($z=0; $z< 4; $z++){
for ($x=0; $x< ($liga[0]->teil)-$diff; $x++){
	$pdf->Cell($leer,$zelle,' ',0,0,'L');
	$pdf->Cell(7-$breite,$zelle,$x+1,1,0,'C');
	$pdf->Cell(7-$breite,$zelle,$punkte[$x]->tln_nr,1,0,'C');
	$pdf->Cell(50-$breite,$zelle,utf8_decode($punkte[$x]->name),1,0,'L');
	$pdf->Cell(10-$breite,$zelle,round($dwz[($punkte[$x]->tln_nr)]),1,0,'C');

// unschön aber läuft bis ich eine bessere Lösung gefunden habe  --> eine SQL Abfrage im MODEL
	$query = " SELECT a.tln_nr,a.gegner,a.runde, a.brettpunkte"
		." FROM #__clm_rnd_man as a "
		." WHERE a.lid = ".$lid
		." AND a.sid = ".$sid
		." AND a.tln_nr = ".$punkte[$x]->tln_nr
		." AND a.dg = 1"
		." ORDER BY a.gegner "
		;
	$db 	=JFactory::getDBO();
	$db->setQuery( $query );
	$runden	=$db->loadObjectList();

	$query = " SELECT a.tln_nr,a.gegner,a.runde, a.brettpunkte"
		." FROM #__clm_rnd_man as a "
		." WHERE a.lid = ".$lid
		." AND a.sid = ".$sid
		." AND a.tln_nr = ".$punkte[$x]->tln_nr
		." AND a.dg = 2"
		." ORDER BY a.gegner "
		;
	$db->setQuery( $query );
	$runden_dg2 =$db->loadObjectList();

$count = 0;

// Anzahl der Runden durchlaufen 1.Durchgang
	for ($y=0; $y< $liga[0]->teil-$diff; $y++) {
		if ($y == $x) {
			$pdf->Cell(8-$breite,$zelle,'X',1,0,'C'); }
		else {
			if ($punkte[$y]->tln_nr > $runden[0]->tln_nr) {
				$pdf->Cell(8-$breite,$zelle,$runden[($punkte[$y]->tln_nr)-2]->brettpunkte,1,0,'C'); 
				}
			if ($punkte[$y]->tln_nr < $runden[0]->tln_nr) {
				$pdf->Cell(8-$breite,$zelle,$runden[($punkte[$y]->tln_nr)-1]->brettpunkte,1,0,'C'); 
				}}}

// Anzahl der Runden durchlaufen 2.Durchgang
	if ($liga[0]->durchgang > 1) {
	for ($y=0; $y< $liga[0]->teil-$diff; $y++) {
		if ($y == $x) {
			$pdf->Cell(8-$breite,$zelle,'X',1,0,'C'); }
		else {
			if ($punkte[$y]->tln_nr > $runden_dg2[0]->tln_nr) {
				$pdf->Cell(8,$zelle-$breite,$runden_dg2[($punkte[$y]->tln_nr)-2]->brettpunkte,1,0,'C');
		}
			if ($punkte[$y]->tln_nr < $runden_dg2[0]->tln_nr) {
				$pdf->Cell(8-$breite,$zelle,$runden_dg2[($punkte[$y]->tln_nr)-1]->brettpunkte,1,0,'C');
		}}}}
// Ende Runden
	$pdf->Cell(10-$breite,$zelle,$punkte[$x]->mp,1,0,'C');
	$pdf->Cell(10-$breite,$zelle,$punkte[$x]->bp,1,0,'C');

	$pdf->Ln();
	}
$pdf->Ln();
$pdf->Ln();

if ($liga[0]->bemerkungen <> "") {
	$pdf->Cell(10,$zelle,' ',0,0,'L');
	$pdf->Cell(150,$zelle,' Hinweis des Staffelleiters :',0,1,'B');
	$pdf->SetFont('Times','',$font-2);
	$pdf->Cell(15,$zelle,' ',0,0,'L');
	$pdf->MultiCell(150,$zelle,utf8_decode($liga[0]->bemerkungen),0,'L',0);
$pdf->Ln();
				}

	$pdf->SetFont('Times','',$font);
	$pdf->Cell(10,$zelle,' ',0,0,'L');
	$pdf->Cell(150,$zelle,'Staffelleiter :',0,1,'L');
	$pdf->SetFont('Times','',$font-2);
	$pdf->Cell(15,$zelle,' ',0,0,'L');
	$pdf->Cell(150,$zelle,$liga[0]->sl,0,1,'L');
	$pdf->Cell(15,$zelle,' ',0,0,'L');
	$pdf->Cell(150,$zelle,$liga[0]->email,0,1,'L');
$pdf->Ln();

$pdf->SetFont('Times','',5);
	$pdf->Cell(10,2,' ',0,0);
	$pdf->Cell(80,2,'erstellt am '.utf8_decode(JHTML::_('date',  $now, JText::_('DATE_FORMAT_CLM_PDF'))),0,1,'L');

// Ende Teilnehmer
$pdf->Output('Rangliste '.$liga[0]->name.'.pdf','D');
//$pdf->Output(JPATH_COMPONENT.DS.'views'.DS.$view.DS.'Rangliste '.$liga[0]->name.'.pdf');

//echo "_!!_".JPATH_COMPONENT_SITE.DS.$view;

?>