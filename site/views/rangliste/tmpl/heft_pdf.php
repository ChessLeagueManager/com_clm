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

require(JPATH_COMPONENT.DS.'includes'.DS.'fpdf.php');

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

$lid = JRequest::getInt( 'liga', '1' ); 
	//Liga-Parameter aufbereiten
	if(isset($liga[0])){
		$paramsStringArray = explode("\n", $liga[0]->params);
	} else  {
		$paramsStringArray = array();
	}
	$params = array();
	foreach ($paramsStringArray as $value) {
		$ipos = strpos ($value, '=');
		if ($ipos !==false) {
			$params[substr($value,0,$ipos)] = substr($value,$ipos+1);
		}
	}	
	if (!isset($params['dwz_date'])) $params['dwz_date'] = '0000-00-00';
$sid = JRequest::getInt( 'saison','1');
$view = JRequest::getVar( 'view');
$o_nr = JRequest::getVar( 'o_nr');
// Variablen ohne foreach setzen
$liga=$this->liga;
$punkte=$this->punkte;
$spielfrei=$this->spielfrei;
$dwzschnitt=$this->dwzschnitt;
$mannschaft	=$this->mannschaft; 
$mleiter	=$this->mleiter; 
$count		=$this->count;      
$saison     =$this->saison; 
$bp			=$this->bp;
$sumbp		=$this->sumbp;
$einzel		=$this->einzel;
$plan		=$this->plan;
$termin		=$this->termin;
  
  
if ($liga[0]->rang > 0) $anz_player = 999;
else $anz_player = $liga[0]->stamm + $liga[0]->ersatz;

function vergleich($wert_a,$wert_b) {
	$a = 1000*($wert_a->dg) + 50*($wert_a->runde) + 2*($wert_a->paar) + $wert_a->heim;
	$b = 1000*($wert_b->dg) + 50*($wert_b->runde) + 2*($wert_b->paar) + $wert_b->heim;
	if ($a == $b) { return 0; }
	return ($a < $b) ? -1 : +1; 
}
$bpr = $bp;
usort($bpr, 'vergleich');

	// Konfigurationsparameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;
	$telefon= $config->man_tel;
	$mobil	= $config->man_mobil;
	$mail	= $config->man_mail;

	// Userkennung holen
	$user	=JFactory::getUser();
	$jid	= $user->get('id');

// Array für DWZ Schnitt setzen
$dwz = array();
	for ($y=1; $y< ($liga[0]->teil)+1; $y++) {
		if ($params['dwz_date'] == '0000-00-00') {
			if(isset($dwzschnitt[($y-1)]->dwz)) {
			$dwz[$dwzschnitt[($y-1)]->tlnr] = $dwzschnitt[($y-1)]->dwz; }
		} else {
			if(isset($dwzschnitt[($y-1)]->start_dwz)) {
			$dwz[$dwzschnitt[($y-1)]->tlnr] = $dwzschnitt[($y-1)]->start_dwz; }
		}
	}
// Spielfreie Teilnehmer finden
$diff = $spielfrei[0]->count;
$anzspl = ($liga[0]->teil - $diff) * $liga[0]->durchgang;

// Überschrift Fontgröße Standard = 14
$head_font = 16;
// Fontgröße Standard = 9
$font = 9;
if ($liga[0]->runden_modus == 3 AND $liga[0]->runden > 6) { $font = 8; }
// Fontgröße Standard = 8
$date_font = 8;
// Zellenhöhe -> Standard 6
$zelle = 6;
// Wert von Zellenbreite abziehen
// Bspl. für Standard (Null) für Liga mit 11 Runden und 1 Durchgang
$breite = 0;
$rbreite = 0;
$nbreite = 0;
$pdf_orientation = 'P';
if ($liga[0]->runden_modus == 1 OR $liga[0]->runden_modus == 2) {    // vollrundig
	if ($anzspl > 11) { 
		$breite = 1;
		$rbreite = 1;
		$nbreite = 2; }
	if ($anzspl > 14) { 
		$rbreite = 2;
		$nbreite = 10; }
	if ($anzspl > 20) {
		$breite = 2;
		$rbreite = 1;
		$nbreite = 16; 
		$font = 6; 
		$zelle = 5; 
		$pdf_orientation = 'L'; }
} elseif ($liga[0]->runden_modus == 3) {    // Schweizer System
	if ($liga[0]->runden > 6) {
		$breite = 1;
		$rbreite = 2;
		$nbreite = 6; }	
}
// Leere Zelle zum zentrieren
$leer = 2;
// Orientation Portrait/Landscape
	JRequest::setVar( 'pdf_orientation', $pdf_orientation);
	if (!isset($pdf_orientation) OR (strpos('PpLl', $pdf_orientation) === false)) $pdf_orientation = 'P';
	if ($pdf_orientation == 'L' OR $pdf_orientation == 'l') {
		$pdf_width = 285;
		$pdf_length = 180;
	} else {
		$pdf_width = 195;
		$pdf_length = 240;
	}
 
// Datum der Erstellung
$date =JFactory::getDate();
$now = $date->toSQL();

$pdf=new PDF();
$pdf->AliasNbPages();

//Deckblatt mit Tabelle
$pdf->AddPage($pdf_orientation);

$pdf->SetFont('Times','',$date_font);
	$pdf->Cell(10,3,' ',0,0);
	$pdf->Cell($pdf_width-20,2,utf8_decode(JText::_('WRITTEN')).' '.utf8_decode(JText::_('ON_DAY')).' '.utf8_decode(JHTML::_('date',  $now, JText::_('DATE_FORMAT_CLM_PDF'))),0,1,'R');

$pdf->SetFont('Times','B',$head_font);
	if ($pdf_orientation == 'L' OR $pdf_orientation == 'l') { $pdf->Ln(1); } else { $pdf->Ln(10); }
	if ($liga[0]->liga_mt == 0) $pdf->Cell($pdf_width-15,10,utf8_decode(JText::_('RANGLISTE_LIGAHEFT')),0,1,'C');
	else $pdf->Cell($pdf_width-15,10,utf8_decode(JText::_('RANGLISTE_TURNIERHEFT')),0,1,'C');
$pdf->SetFont('Times','B',$head_font+2);	
	if ($pdf_orientation == 'L' OR $pdf_orientation == 'l') {
		$pdf->Cell($pdf_width-105,10,utf8_decode($liga[0]->name),0,0,'C');
		$pdf->Cell(70,10,utf8_decode($saison[0]->name),0,1,'C');
	} else {
		$pdf->Cell($pdf_width-15,15,utf8_decode($liga[0]->name),0,1,'C');
		$pdf->Cell($pdf_width-15,10,utf8_decode($saison[0]->name),0,1,'C');
	}
	$pdf->Ln(5);    	
if ($liga[0]->runden_modus != 4 AND $liga[0]->runden_modus != 5) { 	
	$pdf->SetFont('Times','',$font+2);
	$pdf->SetFillColor(120);
	$pdf->SetTextColor(255);
	$pdf->Cell($leer,$zelle,' ',0,0,'L');
	$pdf->Cell(6-$rbreite,$zelle,JText::_('RANG'),1,0,'C',1);
	$pdf->Cell(6-$rbreite,$zelle,JText::_('TLN'),1,0,'C',1);
	$pdf->Cell(55-$nbreite-$breite,$zelle,JText::_('TEAM'),1,0,'L',1);

	if ($liga[0]->runden_modus == 1 OR $liga[0]->runden_modus == 2) {    // vollrundig
// erster Durchgang
	  for ($rnd=0; $rnd < $liga[0]->teil-$diff ; $rnd++) {
		$pdf->Cell(8-$breite,$zelle,$rnd+1,1,0,'C',1);
	}
// zweiter Durchgang
	if ($liga[0]->durchgang > 1) {
	for ($rnd=0; $rnd < $liga[0]->teil-$diff ; $rnd++) {
			$pdf->Cell(8-$breite,$zelle,$rnd+1,1,0,'C',1); }
	  }
// dritter Durchgang
	  if ($liga[0]->durchgang > 2) {
		for ($rnd=0; $rnd < $liga[0]->teil-$diff ; $rnd++) {
			$pdf->Cell(8-$breite,$zelle,$rnd+1,1,0,'C',1); }
	  }
// vierter Durchgang
	  if ($liga[0]->durchgang > 3) {
		for ($rnd=0; $rnd < $liga[0]->teil-$diff ; $rnd++) {
			$pdf->Cell(8-$breite,$zelle,$rnd+1,1,0,'C',1); }
	  }
	}
	if ($liga[0]->runden_modus == 3) { 				// Schweizer System
		for ($rnd=0; $rnd < $liga[0]->runden ; $rnd++) { 
			$pdf->Cell(14-$breite,$zelle,$rnd+1,1,0,'C',1); }
	}
	
	$pdf->Cell(8-$rbreite,$zelle,JText::_('MP'),1,0,'C',1);
	if ( $liga[0]->liga_mt == 0) { 
		$pdf->Cell(10-$breite,$zelle,JText::_('BP'),1,0,'C',1); 
		if ( $liga[0]->b_wertung > 0) {
			$pdf->Cell(10-$rbreite,$zelle,JText::_('WP'),1,0,'C',1); }
	} else {
		if ( $liga[0]->tiebr1 > 0 AND $liga[0]->tiebr1 < 50) { 
			$pdf->Cell(clm_core::$load->tbreak_width($liga[0]->tiebr1,1)-$rbreite,$zelle,JText::_('MTURN_TIEBRS_'.$liga[0]->tiebr1),1,0,'C',1); } 
		if ( $liga[0]->tiebr2 > 0 AND $liga[0]->tiebr2 < 50) { 
			$pdf->Cell(clm_core::$load->tbreak_width($liga[0]->tiebr2,1)-$rbreite,$zelle,JText::_('MTURN_TIEBRS_'.$liga[0]->tiebr2),1,0,'C',1); } 
		if ( $liga[0]->tiebr3 > 0 AND $liga[0]->tiebr3 < 50) { 
			$pdf->Cell(clm_core::$load->tbreak_width($liga[0]->tiebr3,1)-$rbreite,$zelle,JText::_('MTURN_TIEBRS_'.$liga[0]->tiebr3),1,0,'C',1); } 
	}
	$pdf->Ln();

// Anzahl der Teilnehmer durchlaufen
$pdf->SetFillColor(240);
$pdf->SetTextColor(0);
for ($x=0; $x< ($liga[0]->teil)-$diff; $x++){
	if ($x%2 != 0) { $fc = 1; } else { $fc = 0; }
	$pdf->Cell($leer,$zelle,' ',0,0,'L');
	$pdf->Cell(6-$rbreite,$zelle,$x+1,1,0,'C',$fc);
	$pdf->Cell(6-$rbreite,$zelle,$punkte[$x]->tln_nr,1,0,'C',$fc);
	$pdf->Cell(45-$nbreite,$zelle,utf8_decode($punkte[$x]->name),1,0,'L',$fc);
	if (isset($dwz[($punkte[$x]->tln_nr)])) $pdf->Cell(10-$breite,$zelle,round($dwz[($punkte[$x]->tln_nr)]),1,0,'C',$fc);
	else $pdf->Cell(10-$breite,$zelle,'',1,0,'C',$fc);
  
$runden = CLMModelRangliste::punkte_tlnr($sid,$lid,$punkte[$x]->tln_nr,1,$liga[0]->runden_modus);

// Anzahl der Runden durchlaufen 1.Durchgang
if ($liga[0]->runden_modus == 1 OR $liga[0]->runden_modus == 2) { 
	for ($y=0; $y< $liga[0]->teil-$diff; $y++) {
		if ($y == $x) {
			$pdf->SetFillColor(200);
			$pdf->Cell(8-$breite,$zelle,'X',1,0,'C',1); 
			$pdf->SetFillColor(240);}
		else {
			if ($punkte[$y]->tln_nr > $runden[0]->tln_nr) {
				$pdf->Cell(8-$breite,$zelle,$runden[($punkte[$y]->tln_nr)-2]->brettpunkte,1,0,'C',$fc); 
				}
			if ($punkte[$y]->tln_nr < $runden[0]->tln_nr) {
				$pdf->Cell(8-$breite,$zelle,$runden[($punkte[$y]->tln_nr)-1]->brettpunkte,1,0,'C',$fc); 
				}}}
}
if ($liga[0]->runden_modus == 3) { 
	for ($y=0; $y< $liga[0]->runden; $y++) { 
		if (!isset($runden[$y])) 
			$pdf->Cell(14-$breite,$zelle,"",1,0,'C',$fc); 
		elseif ($runden[$y]->name == "spielfrei") 
			$pdf->Cell(14-$breite,$zelle,"  +",1,0,'C',$fc); 
		else 
			$pdf->Cell(14-$breite,$zelle,$runden[$y]->brettpunkte." (".$runden[$y]->rankingpos.")",1,0,'C',$fc); 
	}
}
// Anzahl der Runden durchlaufen 2.Durchgang
	if ($liga[0]->durchgang > 1) {
			$runden_dg2 = CLMModelRangliste::punkte_tlnr($sid,$lid,$punkte[$x]->tln_nr,2,$liga[0]->runden_modus);
	for ($y=0; $y< $liga[0]->teil-$diff; $y++) {
		if ($y == $x) {
			$pdf->SetFillColor(200);
			$pdf->Cell(8-$breite,$zelle,'X',1,0,'C',1); 
			$pdf->SetFillColor(240);}
		else {
			if ($punkte[$y]->tln_nr > $runden_dg2[0]->tln_nr) {
				$pdf->Cell(8-$breite,$zelle,$runden_dg2[($punkte[$y]->tln_nr)-2]->brettpunkte,1,0,'C',$fc);
		}
			if ($punkte[$y]->tln_nr < $runden_dg2[0]->tln_nr) {
				$pdf->Cell(8-$breite,$zelle,$runden_dg2[($punkte[$y]->tln_nr)-1]->brettpunkte,1,0,'C',$fc);
		}}}}
// Anzahl der Runden durchlaufen 3.Durchgang
	if ($liga[0]->durchgang > 2) {
			$runden_dg3 = CLMModelRangliste::punkte_tlnr($sid,$lid,$punkte[$x]->tln_nr,3,$liga[0]->runden_modus);
	for ($y=0; $y< $liga[0]->teil-$diff; $y++) {
		if ($y == $x) {
			$pdf->SetFillColor(200);
			$pdf->Cell(8-$breite,$zelle,'X',1,0,'C',1); 
			$pdf->SetFillColor(240);}
		else {
			if ($punkte[$y]->tln_nr > $runden_dg3[0]->tln_nr) {
				$pdf->Cell(8-$breite,$zelle,$runden_dg3[($punkte[$y]->tln_nr)-2]->brettpunkte,1,0,'C',$fc);
		}
			if ($punkte[$y]->tln_nr < $runden_dg3[0]->tln_nr) {
				$pdf->Cell(8-$breite,$zelle,$runden_dg3[($punkte[$y]->tln_nr)-1]->brettpunkte,1,0,'C',$fc);
		}}}}
// Anzahl der Runden durchlaufen 4.Durchgang
	if ($liga[0]->durchgang > 3) {
			$runden_dg4 = CLMModelRangliste::punkte_tlnr($sid,$lid,$punkte[$x]->tln_nr,4,$liga[0]->runden_modus);
	for ($y=0; $y< $liga[0]->teil-$diff; $y++) {
		if ($y == $x) {
			$pdf->SetFillColor(200);
			$pdf->Cell(8-$breite,$zelle,'X',1,0,'C',1); 
			$pdf->SetFillColor(240);}
		else {
			if ($punkte[$y]->tln_nr > $runden_dg4[0]->tln_nr) {
				$pdf->Cell(8-$breite,$zelle,$runden_dg4[($punkte[$y]->tln_nr)-2]->brettpunkte,1,0,'C',$fc);
		}
			if ($punkte[$y]->tln_nr < $runden_dg4[0]->tln_nr) {
				$pdf->Cell(8-$breite,$zelle,$runden_dg4[($punkte[$y]->tln_nr)-1]->brettpunkte,1,0,'C',$fc);
		}}}}
// Ende Runden
	if ($punkte[$x]->abzug > 0) $pdf->Cell(8-$rbreite,$zelle,$punkte[$x]->mp.'*',1,0,'C',$fc);
	else $pdf->Cell(8-$rbreite,$zelle,$punkte[$x]->mp,1,0,'C',$fc);
	if ( $liga[0]->liga_mt == 0) {
		if ($punkte[$x]->bpabzug > 0) $pdf->Cell(10-$rbreite,$zelle,$punkte[$x]->bp.'*',1,0,'C',$fc);
		else $pdf->Cell(10-$rbreite,$zelle,$punkte[$x]->bp,1,0,'C',$fc); 
		if ($liga[0]->b_wertung > 0) {
			$pdf->Cell(10-$rbreite,$zelle,$punkte[$x]->wp,1,0,'C',$fc); } 
	} else {
		if ( $liga[0]->tiebr1 == 5 ) { // Brettpunkte
			if ($punkte[$x]->bpabzug > 0) $pdf->Cell(clm_core::$load->tbreak_width($liga[0]->tiebr1,1)-$rbreite,$zelle,CLMText::tiebrFormat($liga[0]->tiebr1, $punkte[$x]->sumtiebr1).'*',1,0,'C',$fc); 
			else $pdf->Cell(clm_core::$load->tbreak_width($liga[0]->tiebr1,1)-$rbreite,$zelle,CLMText::tiebrFormat($liga[0]->tiebr1, $punkte[$x]->sumtiebr1),1,0,'C',$fc); 
		} elseif ( $liga[0]->tiebr1 > 0 AND $liga[0]->tiebr1 < 50) {  
			$pdf->Cell(clm_core::$load->tbreak_width($liga[0]->tiebr1,1)-$rbreite,$zelle,CLMText::tiebrFormat($liga[0]->tiebr1, $punkte[$x]->sumtiebr1),1,0,'C',$fc); }
		if ( $liga[0]->tiebr2 == 5 ) { // Brettpunkte
			if ($punkte[$x]->bpabzug > 0) $pdf->Cell(clm_core::$load->tbreak_width($liga[0]->tiebr2,1)-$rbreite,$zelle,CLMText::tiebrFormat($liga[0]->tiebr2, $punkte[$x]->sumtiebr2).'*',1,0,'C',$fc); 
			else $pdf->Cell(clm_core::$load->tbreak_width($liga[0]->tiebr2,1)-$rbreite,$zelle,CLMText::tiebrFormat($liga[0]->tiebr2, $punkte[$x]->sumtiebr2),1,0,'C',$fc); 
		} elseif ( $liga[0]->tiebr2 > 0 AND $liga[0]->tiebr2 < 50) {  
			$pdf->Cell(clm_core::$load->tbreak_width($liga[0]->tiebr2,1)-$rbreite,$zelle,CLMText::tiebrFormat($liga[0]->tiebr2, $punkte[$x]->sumtiebr2),1,0,'C',$fc); }
		if ( $liga[0]->tiebr3 == 5 ) { // Brettpunkte
			if ($punkte[$x]->bpabzug > 0) $pdf->Cell(clm_core::$load->tbreak_width($liga[0]->tiebr3,1)-$rbreite,$zelle,CLMText::tiebrFormat($liga[0]->tiebr3, $punkte[$x]->sumtiebr3).'*',1,0,'C',$fc); 
			else $pdf->Cell(clm_core::$load->tbreak_width($liga[0]->tiebr3,1)-$rbreite,$zelle,CLMText::tiebrFormat($liga[0]->tiebr3, $punkte[$x]->sumtiebr3),1,0,'C',$fc); 
		} elseif ( $liga[0]->tiebr3 > 0 AND $liga[0]->tiebr3 < 50) {  
			$pdf->Cell(clm_core::$load->tbreak_width($liga[0]->tiebr3,1)-$rbreite,$zelle,CLMText::tiebrFormat($liga[0]->tiebr3, $punkte[$x]->sumtiebr3),1,0,'C',$fc); }
	}
	$pdf->Ln();
	}
$pdf->Ln();
$pdf->Ln();
} else $pdf->Ln(10);
 
if ($liga[0]->bemerkungen <> "") {
	$pdf->SetFont('Times','B',$font+2);
	$pdf->Cell(10,$zelle,' ',0,0,'L');
	$pdf->Cell(150,$zelle,' '.utf8_decode(JText::_('NOTICE')).' :',0,1,'B');
	$pdf->SetFont('Times','',$font);
	$pdf->Cell(15,$zelle,' ',0,0,'L');
	$pdf->MultiCell(150,$zelle,utf8_decode($liga[0]->bemerkungen),0,'L',0);
	$pdf->Ln();
	}

	$pdf->SetFont('Times','B',$font+2);
	$pdf->Cell(10,$zelle,' ',0,0,'L');
	$pdf->Cell(150,$zelle,JText::_('CHIEF').' :',0,1,'L');
	$pdf->SetFont('Times','',$font);
	$pdf->Cell(15,$zelle,' ',0,0,'L');
	$pdf->Cell(150,$zelle,utf8_decode($liga[0]->sl),0,1,'L');
	$pdf->Cell(15,$zelle,' ',0,0,'L');
	$pdf->Cell(150,$zelle,$liga[0]->email,0,1,'L');
$pdf->Ln();
// Ende Teilnehmer

// Paarungen pro Spieltag
$pdf_orientation = 'P';
	JRequest::setVar( 'pdf_orientation', $pdf_orientation);
$pdf->AddPage($pdf_orientation);
	$pdf_width = 195;
	$pdf_length = 240;

$pdf->SetFont('Times','',$date_font);
	$pdf->Cell(10,3,' ',0,0);
	$pdf->Cell(175,2,utf8_decode(JText::_('WRITTEN')).' '.utf8_decode(JText::_('ON_DAY')).' '.utf8_decode(JHTML::_('date',  $now, JText::_('DATE_FORMAT_CLM_PDF'))),0,1,'R');
	
$pdf->SetFont('Times','B',$head_font);
	$pdf->Cell(10,15,' ',0,0);
	$pdf->Cell(80,15,utf8_decode($liga[0]->name)."  ".utf8_decode($saison[0]->name),0,0,'L');

$pdf->SetFont('Times','B',$head_font-2);
	$pdf->Cell(10,5,' ',0,1);
	$pdf->Cell(10,5,' ',0,1);
	$pdf->Cell(10,8,' ',0,0);
	$pdf->Cell(80,8,utf8_decode(JText::_('TEAM_PLAN')),0,1,'L');
	
$pdf->SetFont('Times','',8);
	$pdf->Cell(10,8,' ',0,0);
	$pdf->Cell(12,8,JText::_('TEAM_ROUNDS'),0,0,'C');
	$pdf->Cell(12,8,JText::_('TEAM_PAIR'),0,0,'C');
	$pdf->Cell(30,8,JText::_('TEAM_DATE'),0,0,'L');
	$pdf->Cell(40,8,JText::_('TEAM_HOME'),0,0,'L');
	$pdf->Cell(40,8,JText::_('TEAM_GUEST'),0,0,'L');
	$pdf->Cell(8,8,'',0,1,'C');
	
	$cnt = 0;
	$ibpr = 0;
	foreach ($plan as $planl) { 
		//if (($planl->tln_nr !== $mannschaft[$m]->tln_nr) AND ($planl->gegner !== $mannschaft[$m]->tln_nr)) continue;
		//$datum =JFactory::getDate($planl->datum);
		$hpkt = "";
		$gpkt = "";
		if (isset($termin[$cnt]->nr)) {
			for ($icnt=0; $icnt<2; $icnt++) { 
				if (($planl->runde + $liga[0]->runden*($planl->dg -1)) > $termin[$cnt]->nr) {
					$cnt++;
					if ($pdf->GetY() > 240) {
						$pdf->AddPage();
						$pdf->SetFont('Times','',7);
						$pdf->Cell(10,3,' ',0,0);
						$pdf->Cell(175,2,utf8_decode(JText::_('WRITTEN')).' '.utf8_decode(JText::_('ON_DAY')).' '.utf8_decode(JHTML::_('date',  $now, JText::_('DATE_FORMAT_CLM_PDF'))),0,1,'R');
						$pdf->SetFont('Times','',8);
					}
					$pdf->Cell(8,2,'',0,1,'C');
				}
			}		
			if (($planl->runde + $liga[0]->runden*($planl->dg -1)) == $termin[$cnt]->nr) {
				$pdf->Cell(10,4,' ',0,0);
				$pdf->Cell(12,4,$planl->runde,0,0,'C');
				$pdf->Cell(12,4,$planl->paar,0,0,'C');
				if ($termin[$cnt]->datum == '0000-00-00') $pdf->Cell(30,4,'    ',0,0,'L');
				else $pdf->Cell(30,4,JHTML::_('date',  $termin[$cnt]->datum, JText::_('DATE_FORMAT_CLM')),0,0,'L');
				$pdf->Cell(40,4,utf8_decode($planl->hname),0,0,'L');
				$pdf->Cell(40,4,utf8_decode($planl->gname),0,0,'L');
				$pdf->Cell(2,4,'',0,0,'C');
			if (isset($bpr[$ibpr])) {
				if (($bpr[$ibpr]->runde == $planl->runde) AND ($bpr[$ibpr]->tln_nr == $planl->gegner)) $gpkt = $bpr[$ibpr]->brettpunkte;
				$ibpr++;
				if (($bpr[$ibpr]->runde == $planl->runde) AND ($bpr[$ibpr]->tln_nr == $planl->tln_nr)) $hpkt = $bpr[$ibpr]->brettpunkte;
				$ibpr++; 
			}
		$pdf->Cell(8,4,$hpkt,1,0,'C');
		$pdf->Cell(4,4,':',0,0,'C');
		$pdf->Cell(8,4,$gpkt,1,0,'C');
		$pdf->Cell(8,4,'',0,1,'C');
		
		}}
	}

//Mannschaften
$ic = 0;
$ie = 0;
$ib = 0;
for ($m=0; $m<$liga[0]->teil; $m++) {
	if ($mannschaft[$m]->name == "spielfrei") continue;
	
if ( $mannschaft[$m]->published == 0) {
	$pdf->AddPage();
	$pdf->SetFont('Times','',$head_font+2);
	$pdf->Cell(10,15,utf8_decode(JText::_('TEAM')).' '.utf8_decode($mannschaft[$m]->name),0,1);
	$pdf->Cell(10,15,utf8_decode(JText::_('NOT_PUBLISHED')),0,0);
	$pdf->Ln();
	$pdf->Cell(10,15,utf8_decode(JText::_('GEDULD')),0,0);
} elseif (($mannschaft[0]->runden * $mannschaft[0]->dg) > 30) {
	$pdf->AddPage();
	$pdf->SetFont('Times','',$head_font+2);
	$pdf->Cell(10,15,utf8_decode(JText::_('TEAM_PDF_LIMIT')),0,1);
	$pdf->Ln();
	$pdf->Cell(10,15,utf8_decode(JText::_('TEAM_PDF_ADVICE')),0,0);
} else {
$anzspl = $mannschaft[0]->runden * $mannschaft[0]->dg;

// Überschrift Fontgröße Standard = 14
$head_font = 14;
// Fontgröße Standard = 9
$font = 9;
// Fontgröße Datum = 8
$date_font = 8;
// Zellenhöhe -> Standard 6
$zelle = 6;
// Wert von Zellenbreite abziehen
// Bspl. für Standard (Null) für Liga mit 7 Runden und 1 Durchgang
$breite1 = 10;
$breite = 8;
$pdf_orientation = 'P';
if ($anzspl > 11) {
	$breite1 = 10;
	$breite = 6;
	$zelle = 5; 
} 
if ($anzspl > 20) {
	$breite1 = 8;
	$breite = 6;
	$font = 8; 
	$zelle = 5; 
	$pdf_orientation = 'L'; }
JRequest::setVar( 'pdf_orientation', $pdf_orientation);
// Orientation Portrait/Landscape
	if (!isset($pdf_orientation) OR (strpos('PpLl', $pdf_orientation) === false)) $pdf_orientation = 'P';
	if ($pdf_orientation == 'L' OR $pdf_orientation == 'l') {
		$pdf_width = 285;
		$pdf_length	= 190;
} else {
		$pdf_width = 195;
		$pdf_length	= 240;
	}

$pdf->AddPage($pdf_orientation);
$pdf->SetFont('Times','',$date_font);
	$pdf->Cell(10,3,' ',0,0);
	$pdf->Cell($pdf_width-20,4,utf8_decode(JText::_('WRITTEN')).' '.utf8_decode(JText::_('ON_DAY')).' '.utf8_decode(JHTML::_('date',  $now, JText::_('DATE_FORMAT_CLM_PDF'))),0,1,'R');
	
$mf_name = "";
$mf_email = "";
$mf_tel_fest = "";
$mf_tel_mobil = "";
foreach ($mleiter as $mll) {
	if ($mannschaft[$m]->mf == $mll->mf) {
		$mf_name = $mll->mf_name;
		if ($mail=="1" OR ($mail =="0" AND $jid !="0")) $mf_email = $mll->email;
		else $mf_email = JText::_('TEAM_MAIL')." ".JText::_('TEAM_REGISTERED');
		if ($telefon =="1" OR ($telefon =="0" AND $jid !="0")) $mf_tel_fest = $mll->tel_fest;
		else $mf_tel_fest = JText::_('TEAM_REGISTERED');
		if ($mobil =="1" OR ($mobil =="0" AND $jid !="0")) $mf_tel_mobil = $mll->tel_mobil;
		else $mf_tel_mobil = JText::_('TEAM_REGISTERED');
		break;
	}
}
$pdf->SetFont('Times','B',$head_font);
	$pdf->Cell(10,10,' ',0,0);
	if ($o_nr == 0) 
		$pdf->Cell(100,10,utf8_decode(JText::_('TEAM')).' : '.utf8_decode($mannschaft[$m]->name),0,1,'L');
	else {
		$pdf->Cell(100,10,utf8_decode(JText::_('TEAM')).' : '.utf8_decode($mannschaft[$m]->name),0,0,'L');
		$pdf->SetFont('Times','',$head_font-2);
		$ztext = "( ".$mannschaft[$m]->zps;
		if ($mannschaft[$m]->sg_zps > "0") $ztext .= " / ".$mannschaft[$m]->sg_zps;
		$ztext .= " )";
		$pdf->Cell(20,10,$ztext,0,1,'L');
	}	
$pdf->SetFont('Times','B',$head_font-2);
	$pdf->Cell(10,10,' ',0,0);
	$pdf->Cell(100,10,utf8_decode(JText::_('LEAGUE')).' : '.utf8_decode($mannschaft[$m]->liga_name)."  ".$saison[0]->name,0,1,'L');
$pdf->SetFont('Times','B',$font);
	$pdf->Cell(10,6,' ',0,0);
	$pdf->Cell(80,6,utf8_decode(Jtext::_('TEAM_LEADER')),0,0);
	$pdf->Cell(80,6,utf8_decode(Jtext::_('TEAM_LOCATION')),0,1);
$pdf->SetFont('Times','',$font);	
	$pdf->Cell(10,4,' ',0,0);
	if ($mf_name <> '') $pdf->Cell(80,4,utf8_decode($mf_name),0,0,'L');
	else $pdf->Cell(80,4,utf8_decode(JText::_('NOT_YET')),0,0,'L');
	if ($mannschaft[$m]->lokal <> '') $man = explode(",", $mannschaft[$m]->lokal);
	else $man = explode(",",utf8_decode(JText::_('NOT_YET')));
	if (isset($man[0])) $pdf->Cell(80,4,utf8_decode($man[0]),0,1);
	else $pdf->Cell(80,4,'',0,1);
$pdf->SetFont('Times','U',$font);	
	$pdf->Cell(10,4,' ',0,0);
	$pdf->Cell(80,4,utf8_decode($mf_email),0,0,'L');
$pdf->SetFont('Times','',$font);
	if (isset($man[1])) $pdf->Cell(80,4,utf8_decode($man[1]),0,1);
	else $pdf->Cell(80,4,'',0,1);
	
	$pdf->Cell(10,4,' ',0,0);
	if (($mf_tel_fest) <> '') $pdf->Cell(80,4,Jtext::_('TEAM_FON').utf8_decode($mf_tel_fest),0,0,'L');
	elseif ($mf_name <> '') $pdf->Cell(80,4,substr(Jtext::_('TEAM_NO_FONE'),0,(strlen(Jtext::_('TEAM_NO_FONE'))-4)),0,0,'L');
	else $pdf->Cell(80,4,'',0,0,'L');
	if (isset($man[2])) $pdf->Cell(80,4,utf8_decode($man[2]),0,1);
	else $pdf->Cell(80,4,'',0,1);
	
	$pdf->Cell(10,8,' ',0,0);
	if (($mf_tel_mobil) <> '') $pdf->Cell(80,4,Jtext::_('TEAM_MOBILE').utf8_decode($mf_tel_mobil),0,0,'L');
	elseif ($mf_name <> '') $pdf->Cell(80,4,substr(Jtext::_('TEAM_NO_MOBILE'),0,(strlen(Jtext::_('TEAM_NO_MOBILE'))-4)),0,0,'L');
	else $pdf->Cell(80,4,'',0,0,'L');
	if (isset($man[3])) $pdf->Cell(80,4,utf8_decode($man[3]),0,1);
	else $pdf->Cell(80,4,'',0,1);
	
if ($mannschaft[$m]->bemerkungen <> "") {
	$pdf->SetFont('Times','B',$font);
	$pdf->Cell(10,6,' ',0,0,'L');
	$pdf->Cell(150,6,utf8_decode(JText::_('TEAM_NOTICE')),0,1,'B');
	$pdf->SetFont('Times','',$font);
	$pdf->Cell(10,4,' ',0,0,'L');
	$pdf->MultiCell(150,4,utf8_decode($mannschaft[$m]->bemerkungen),0,'L',0);
	$pdf->Ln();
	}

$pdf->SetFont('Times','B',$font);
	$pdf->Ln();
	$pdf->Cell(10,8,' ',0,0);
	$pdf->Cell(80,8,utf8_decode(JText::_('TEAM_FORMATION')),0,1,'L');
	
	if ($liga[0]->anzeige_ma == 1) {
		$pdf->SetFont('Times','',$font);
		$pdf->Cell(80,8,utf8_decode(JText::_('TEAM_FORMATION_BLOCKED')),0,1,'C');
	}
	elseif (!$count) {
		$pdf->SetFont('Times','',$font);
		$pdf->Cell(80,8,utf8_decode(JText::_('NOT_YET')),0,1,'C');
	}
	else {

$pdf->SetFont('Times','',$font);
$pdf->SetFillColor(120);
$pdf->SetTextColor(255);
	$pdf->Cell($breite1,8,' ',0,0);
	$pdf->Cell(12,$zelle,JText::_('DWZ_NR'),0,0,'C',1);
	if (($o_nr == 0) or ($mannschaft[$m]->sg_zps <= "0"))
		$pdf->Cell(40,$zelle,JText::_('DWZ_NAME'),0,0,'L',1);
	else
		$pdf->Cell(48,$zelle,JText::_('DWZ_NAME'),0,0,'L',1);
	if ($countryversion == "de")
		$pdf->Cell(10,$zelle,JText::_('LEAGUE_STAT_DWZ'),0,0,'C',1);
	else
		$pdf->Cell(10,$zelle,JText::_('LEAGUE_STAT_DWZ_EN'),0,0,'C',1);
	//if (!$count) 
	for ($b=0; $b<$mannschaft[0]->runden; $b++) {
		$pdf->Cell($breite,$zelle,$b+1,0,0,'C',1);
	}
	if ($mannschaft[0]->dg >1) {
		for ($b=0; $b<$mannschaft[0]->runden; $b++) {
			$pdf->Cell($breite,$zelle,$b+1,0,0,'C',1);
	} }
	if ($mannschaft[0]->dg >2) {
		for ($b=0; $b<$mannschaft[0]->runden; $b++) {
			$pdf->Cell($breite,$zelle,$b+1,0,0,'C',1);
	} }
	if ($mannschaft[0]->dg >3) {
		for ($b=0; $b<$mannschaft[0]->runden; $b++) {
			$pdf->Cell($breite,$zelle,$b+1,0,0,'C',1);
	} }
	$pdf->Cell($breite,$zelle,JText::_('TEAM_POINTS'),0,0,'C',1);
	$pdf->Cell($breite,$zelle,JText::_('TEAM_GAMES'),0,0,'C',1);
	$pdf->Cell($breite+2,$zelle,JText::_('LEAGUE_STAT_PERCENT'),0,1,'C',1);
	

// Teilnehmerschleife 	
$pdf->SetFont('Times','',$font);
$pdf->SetFillColor(240);
$pdf->SetTextColor(0);
	//$ie = 0;
	$sumspl = 0;
	$sumgespielt = 0;
for ($x=0; $x< $anz_player; $x++){
	if ($x%2 != 0) { $fc = 1; } else { $fc = 0; }
	// Überlesen von Null-Sätzen 
	while (isset($count[$ic]) and $countryversion == "de" and $count[$ic]->mgl_nr == "0")  {
		$ic++; }
	if (!isset($count[$ic])) break;
	if ($count[$ic]->PKZ === NULL) { $count[$ic]->PKZ = ""; }
	if ($count[$ic]->tln_nr != $mannschaft[$m]->tln_nr) break;
	if (!isset($count[$ic]->rrang)) {
		$pdf->Cell($breite1,$zelle,' ',0,0);
		$pdf->Cell(12,$zelle,($x+1),1,0,'C',$fc);
	} else {
		if ($count[$ic]->rmnr > $mannschaft[$m]->man_nr) {
			if ((!isset($einzel[$ie])) or ($count[$ic]->tln_nr < $einzel[$ie]->tln_nr) or 
			    (($count[$ic]->tln_nr == $einzel[$ie]->tln_nr) and 
				(($count[$ic]->zps !== $einzel[$ie]->zps)||($count[$ic]->mgl_nr !== $einzel[$ie]->spieler)))) {
			$ic++;
			continue;
		  }
		}
		$pdf->Cell($breite1,$zelle,' ',0,0);
		$pdf->Cell(12,$zelle,($count[$ic]->rmnr.'-'.$count[$ic]->rrang),0,0,'C');
	}	
	if ($o_nr == 0) 
		$pdf->Cell(40,$zelle,utf8_decode($count[$ic]->name),1,0,'L',$fc);
	elseif ($mannschaft[$m]->sg_zps > "0") {
		$pdf->Cell(33,$zelle,utf8_decode($count[$ic]->name),1,0,'L',$fc);
		$pdf->SetFont('Times','',$font-1);
		$pdf->Cell(15,$zelle,"(".$count[$ic]->zps."-".$count[$ic]->mgl_nr.")",1,0,'L',$fc);
		$pdf->SetFont('Times','',$font);
	} else {
		$pdf->Cell(33,$zelle,utf8_decode($count[$ic]->name),1,0,'L',$fc);
		$pdf->SetFont('Times','',7);
		$pdf->Cell(7,$zelle,"(".$count[$ic]->mgl_nr.")",1,0,'L',$fc);
		$pdf->SetFont('Times','',8);
	}
	if ($params['dwz_date'] == '0000-00-00') $pdf->Cell(10,$zelle,$count[$ic]->dwz,1,0,'C',$fc);
	else $pdf->Cell(10,$zelle,$count[$ic]->start_dwz,1,0,'C',$fc);
	$pkt = 0;
	$spl = 0;
	$gespielt = 0;
  for ($c=0; $c<$mannschaft[$m]->dg; $c++) {
	for ($b=0; $b<$mannschaft[$m]->runden; $b++) {
	if ((isset($einzel[$ie]) AND $einzel[$ie])&&($einzel[$ie]->dg==$c+1)&&($einzel[$ie]->runde==$b+1)&&
			($einzel[$ie]->tln_nr==$mannschaft[$m]->tln_nr)&&($count[$ic]->zps==$einzel[$ie]->zps)&&
			((($countryversion == "de")&&($count[$ic]->mgl_nr==$einzel[$ie]->spieler))||(($countryversion == "en")&&($count[$ic]->PKZ==$einzel[$ie]->PKZ)))) {
		$dr_einzel = "?";
		if (($einzel[$ie]->punkte==0)&&($einzel[$ie]->kampflos==0)) $dr_einzel = "0";
		if (($einzel[$ie]->punkte==0)&&($einzel[$ie]->kampflos==1)) $dr_einzel = "-";
		if (($einzel[$ie]->punkte==0)&&($einzel[$ie]->kampflos==2)) $dr_einzel = "-";
		if (($einzel[$ie]->punkte==1)&&($einzel[$ie]->kampflos==0)) $dr_einzel = "1";
		if (($einzel[$ie]->punkte==1)&&($einzel[$ie]->kampflos==1)) $dr_einzel = "+";
		if (($einzel[$ie]->punkte==1)&&($einzel[$ie]->kampflos==2)) $dr_einzel = "+";
		if ($einzel[$ie]->punkte==0.5) $dr_einzel = chr(189);
		$pdf->Cell($breite,$zelle,$dr_einzel,1,0,'C',$fc);
		$spl++;
		$sumspl++;
		$pkt = $pkt + $einzel[$ie]->punkte;
		$ie++;
	  }
	  else $pdf->Cell($breite,$zelle,'',1,0,'C',$fc);
	  
	}
  }
	if ($spl>0) {
		$pdf->Cell($breite,$zelle,$pkt,1,0,'C',$fc);
		$pdf->Cell($breite,$zelle,$spl,1,0,'C',$fc);
		$prozent = round(100*($pkt/$spl));
		$pdf->Cell($breite+2,$zelle,$prozent,1,0,'C',$fc);
	}
	else {
		$pdf->Cell($breite,$zelle,"",1,0,'C',$fc);
		$pdf->Cell($breite,$zelle,"",1,0,'C',$fc);
		$pdf->Cell($breite+2,$zelle,"",1,0,'C',$fc);
	}
	$pdf->Cell($breite,$zelle,'',0,1,'C');
	$ic++;
}
	while (isset($count[$ic]) and isset($einzel[$ie]) and $count[$ic]->tln_nr > $einzel[$ie]->tln_nr) {
		$pdf->Cell($breite1,$zelle-1,' ',0,0);
//		$ztext = utf8_decode("Ergebnis übersprungen, da Spieler nicht in Aufstellung ");
//		$ztext .= ' Verein:'.$einzel[$ie]->zps.' Mitglied:'.$einzel[$ie]->spieler.' PKZ:'.$einzel[$ie]->PKZ;
//		$ztext .= ' Durchgang:'.$einzel[$ie]->dg.' Runde:'.$einzel[$ie]->runde;
//		$ztext .= ' Brett:'.$einzel[$ie]->brett.' Erg:'.$einzel[$ie]->punkte; 	
		$ztext = utf8_decode(JText::_( 'TEAM_WARNING' ));
		$ztext .= JText::_( 'TEAM_CLUB' ).$einzel[$ie]->zps.JText::_( 'TEAM_MEMBER' ).$einzel[$ie]->spieler.JText::_( 'TEAM_PKZ' ).$einzel[$ie]->PKZ;
		$ztext .= JText::_( 'TEAM_DG' ).$einzel[$ie]->dg.JText::_( 'TEAM_ROUND' ).$einzel[$ie]->runde;
		$ztext .= JText::_( 'TEAM_BOARD' ).$einzel[$ie]->brett.JText::_( 'TEAM_RESULT2' ).$einzel[$ie]->punkte; 	
		$pdf->Cell(50,$zelle-1,$ztext,0,1,'L');
		$ie++;
	}

	$x = $pdf->GetX();
	$y = $pdf->GetY();
	if (($o_nr == 0) or ($mannschaft[$m]->sg_zps <= "0"))
		$pdf->Line($x+$breite1,$y+2,$x+$pdf_width-8,$y+2);
	else
		$pdf->Line($x+$breite1,$y+2,$x+$pdf_width,$y+2);
	$pdf->Cell(8,$zelle,'',0,1,'C');
	
	$pdf->Cell($breite1,$zelle,' ',0,0);
	$pdf->Cell(12,$zelle,JText::_('TEAM_TOTAL'),1,0,'C',1);
	if (($o_nr == 0) or ($mannschaft[$m]->sg_zps <= "0"))
		$pdf->Cell(40,$zelle,'',0,0);
	else
		$pdf->Cell(48,$zelle,'',0,0);
	$pdf->Cell(10,$zelle,'',0,0,'R');
	$pktsumme = 0;
	$spl = 0;
  for ($c=0; $c<$mannschaft[$m]->dg; $c++) {
	for ($b=0; $b<$mannschaft[$m]->runden; $b++) {
		if (isset($bp[$ib]->runde) AND $bp[$ib]->runde == $b+1 AND $bp[$ib]->tln_nr == $mannschaft[$m]->tln_nr) { 
			$pdf->Cell($breite,$zelle,$bp[$ib]->brettpunkte,1,0,'C');
			$pktsumme = $pktsumme + $bp[$ib]->brettpunkte;
		}
		else $pdf->Cell($breite,$zelle,'',1,0,'C');
		$ib++;
	}
  }
	if ($sumspl>0) {
		$pdf->Cell($breite,$zelle,$pktsumme,1,0,'C');
		$pdf->Cell($breite,$zelle,$sumspl,1,0,'C');
		$prozent = round(100*($pktsumme/$sumspl));
		$pdf->Cell($breite+2,$zelle,$prozent,1,0,'C');
	}
else {
		$pdf->Cell($breite,$zelle,"",1,0,'C');
		$pdf->Cell($breite,$zelle,"",1,0,'C');
		$pdf->Cell($breite+2,$zelle,"",1,0,'C');
	}	
	$pdf->Cell($breite,$zelle,'',0,1,'C');
}
	$pdf->Ln();

// Spielplan
$pdf->SetFont('Times','B',$font);
	$pdf->Ln();
	$pdf->Cell(10,8,' ',0,0);
	$pdf->Cell(80,8,utf8_decode(JText::_('TEAM_PLAN')),0,1,'L');
	
$pdf->SetFont('Times','',$font);
	$pdf->Cell(10,8,' ',0,0);
	$pdf->Cell(12,8,JText::_('TEAM_ROUNDS'),0,0,'C');
	$pdf->Cell(12,8,JText::_('TEAM_PAIR'),0,0,'C');
	$pdf->Cell(30,8,JText::_('TEAM_DATE'),0,0,'L');
	$pdf->Cell(40,8,JText::_('TEAM_HOME'),0,0,'L');
	$pdf->Cell(40,8,JText::_('TEAM_GUEST'),0,0,'L');
	$pdf->Cell(8,8,'',0,1,'C');
	
	$cnt = 0;
	$ibpr = 0;
	foreach ($plan as $planl) { 
		if (($planl->tln_nr !== $mannschaft[$m]->tln_nr) AND ($planl->gegner !== $mannschaft[$m]->tln_nr)) continue;
		//$datum =JFactory::getDate($planl->datum);
		$pdf->Cell(10,4,' ',0,0);
		$pdf->Cell(12,4,$planl->runde,0,0,'C');
		$pdf->Cell(12,4,$planl->paar,0,0,'C');
		while (isset($termin[$cnt]->nr) AND ($planl->runde + $mannschaft[$m]->runden*($planl->dg -1)) > $termin[$cnt]->nr) { 
			$cnt++; }
		if (isset($termin[$cnt]->nr) AND ($planl->runde + $mannschaft[$m]->runden*($planl->dg -1))== $termin[$cnt]->nr) { 
			if ($termin[$cnt]->datum == '0000-00-00') $pdf->Cell(30,4,'    ',0,0,'L');
			else $pdf->Cell(30,4,JHTML::_('date',  $termin[$cnt]->datum, JText::_('DATE_FORMAT_CLM')),0,0,'L');
			//$pdf->Cell(30,4,JHTML::_('date',  $termin[$cnt]->datum, JText::_('DATE_FORMAT_CLM')),0,0,'L');
			$cnt++;
			$pdf->Cell(40,4,utf8_decode($planl->hname),0,0,'L');
			$pdf->Cell(40,4,utf8_decode($planl->gname),0,0,'L');
			$pdf->Cell(2,4,'',0,0,'C');
		while ($bpr[$ibpr]->runde < $planl->runde) { $ibpr++; }
		for ($b=0; $b<($liga[0]->teil); $b++) {
			if ((!isset($bpr[$ibpr])) OR ($bpr[$ibpr]->runde > $planl->runde)) break;
			if (($bpr[$ibpr]->runde == $planl->runde) AND ($bpr[$ibpr]->tln_nr == $planl->tln_nr)) $hpkt = $bpr[$ibpr]->brettpunkte;
			if (($bpr[$ibpr]->runde == $planl->runde) AND ($bpr[$ibpr]->tln_nr == $planl->gegner)) $gpkt = $bpr[$ibpr]->brettpunkte;
			$ibpr++; 
		}
		$pdf->Cell(8,4,$hpkt,1,0,'C');
		$pdf->Cell(4,4,':',0,0,'C');
		$pdf->Cell(8,4,$gpkt,1,0,'C');
		$pdf->Cell(8,4,'',0,1,'C');
		
		}
	}
}}
// Ausgabe
$pdf->Output(JText::_('RANGLISTE_LIGAHEFT').' '.utf8_decode($liga[0]->name).'.pdf','D');

?>
