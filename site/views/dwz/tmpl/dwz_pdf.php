<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// Sorgt dafür das nur von innerhalb des Joomla Frameworks aufgerufen werden kann ! Serh wichtige Sicherheistmaßnahem um diversen Attacken zu entgehen
defined('_JEXEC') or die('Restricted access');

// Variablen aus URL Parametern holen
$sid	= JRequest::getInt('saison','1');
$itemid	= JRequest::getInt('Itemid','19');

// Variablen aus dem Model initialisieren
$zps	= $this->zps;
$liga	= $this->liga;

// Prüfen ob Verein vorhanden ist
 if (!$liga[0]->Vereinname) { ?>
<div class="componentheading"><?php echo utf8_decode(JText::_('CLUB_UNKNOWN')) ?></div>
<?php	} else {

// Konfigurationsparameter auslesen
$config = clm_core::$db->config();
$countryversion = $config->countryversion;

// FPDF Klasse einbinden
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

// Formatierungen um später mit frei konfigurierbaren Parametern arbeiten zu können
// Zellenhöhe -> Standard 6
$zelle = 6;
// Fontgröße Standard = 12
$font = 12;
// Leere Zelle zum zentrieren der Tabelle
$leer = 18;

// Datum der Erstellung mit Joomla Methode holen und in SQL Format konvertieren
$date	= JFactory::getDate();
$now	= $date->toSQL();

// FPDF Objekt aufrufen und neues Dokument starten
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

// Datum und Uhrzeit schreiben
$pdf->SetFont('Times','',5);
	$pdf->Cell(10,2,' ',0,0);
	$pdf->Cell(175,4,utf8_decode(JText::_('WRITTEN')).' '.utf8_decode(JText::_('ON_DAY')).' '.utf8_decode(JHTML::_('date',  $now, JText::_('DATE_FORMAT_CLM_PDF'))),0,1,'R');
	
// Überschrift
$pdf->SetFont('Times','',16);
	$pdf->Cell(10,15,' ',0,0);
	if ($countryversion =="de") {
		$pdf->Cell(80,15,utf8_decode(JText::_('CLUB_RATING')).' '.utf8_decode($liga[0]->Vereinname),0,1,'L');
	} else {
		$pdf->Cell(80,15,utf8_decode(JText::_('CLUB_RATING_EN')).' '.utf8_decode($liga[0]->Vereinname),0,1,'L');
	}
// Kopfzeile der Tabelle
$pdf->SetFont('Times','',$font);
	///////////////////////////////////////////////////////////////
	// Referenz siehe http://www.fpdf.de/funktionsreferenz/Cell  //
	///////////////////////////////////////////////////////////////
	//
	// $pdf->Cell(Breite, Höhe, Inhalt, Rahmen, Umbruch, Zentrierung)
	//
	// Breite : Numerischer Wert
	// Höhe : Numerischer Wert
	// Inhalt : Text in Hochkommata, PHP Code mit . ("Punkt") angefügt
	// Rahmen : 0 = aus, 1 = ein,
	//	oder Kombinationen aus L (links), T (oben), R (rechts), B (unten)
	// Umbruch : 0 = rechts, 1 = entspr.$pdf->Ln(), 2 = Umbruch ohne Zurücksetzen auf linken Rand
	// Zentrierung ; L = links, C = zentriert, R = rechts
	//
	///////////////////////////////////////////////////////////////

	$pdf->Cell($leer,$zelle,' ',0,0,'L');
	$pdf->Cell(7,$zelle,utf8_decode(JText::_('CLUB_NR')),1,0,'C');
	if ($countryversion =="de") {
		$pdf->Cell(16,$zelle,utf8_decode(JText::_('CLUB_MEMBER')),1,0,'C');
	} else {
		$pdf->Cell(18,$zelle,utf8_decode(JText::_('CLUB_MEMBER_PKZ')),1,0,'C');
	}
	$pdf->Cell(80,$zelle,utf8_decode(JText::_('CLUB_MEMBER_NAME')),1,0,'C');
	if ($countryversion =="de") {
		$pdf->Cell(16,$zelle,utf8_decode(JText::_('CLUB_MEMBER_RATING')),1,0,'C');
	} else {
		$pdf->Cell(16,$zelle,utf8_decode(JText::_('CLUB_MEMBER_RATING_EN')),1,0,'C');
	}
	$pdf->Cell(16,$zelle,utf8_decode(JText::_('CLUB_MEMBER_RATINGS')),1,0,'C');
	$pdf->Cell(16,$zelle,utf8_decode(JText::_('CLUB_MEMBER_ELO')),1,0,'C');
	// Zeilenumbruch
	$pdf->Ln();

// Schleife für Mitglieder durchlaufen
	$x= 1;

	foreach ($zps as $zps) {

	$pdf->Cell($leer,$zelle,' ',0,0,'L');
	$pdf->Cell(7,$zelle,$x,1,0,'C');
	if ($countryversion =="de") {
		$pdf->Cell(16,$zelle,$zps->Mgl_Nr,1,0,'C');
	} else {
		$pdf->Cell(18,$zelle,$zps->PKZ,1,0,'C');
	}
	$pdf->Cell(2,$zelle,' ','B', 0,'L');
	$pdf->Cell(78,$zelle,utf8_decode($zps->Spielername),'BT',0,'L');
	$pdf->Cell(16,$zelle,$zps->DWZ,1,0,'C');
	if ($countryversion =="de") {
		$pdf->Cell(16,$zelle,$zps->DWZ_Index,1,0,'C');
	} else {
		$pdf->SetFont('Times','',$font-2);
		$pdf->Cell(16,$zelle,'('.(600 + ($zps->DWZ * 8)).')',1,0,'C');
		$pdf->SetFont('Times','',$font);
	}
	if ($zps->FIDE_Elo > 0) {
		$pdf->Cell(16,$zelle,$zps->FIDE_Elo,1,0,'C');
	} else {
		$pdf->Cell(16,$zelle,'',1,0,'C');
	}
	// Zeilenumbruch
	$pdf->Ln();

	$x++;
	}

// PDF an Browser senden
$pdf->Output(utf8_decode(JText::_('CLUB_RATING')).' '.utf8_decode($liga[0]->Vereinname).'.pdf','D');
}
?>
