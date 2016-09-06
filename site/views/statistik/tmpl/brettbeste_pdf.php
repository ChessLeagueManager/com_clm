<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access');

$liga		= $this->liga;
$bestenliste = $this->bestenliste;
$sid		= JRequest::getInt( 'saison','1');
$lid		= JRequest::getInt('liga','1');

	//Parameter aufbereiten
	$paramsStringArray = explode("\n", $liga[0]->params);
	$params = array();
	foreach ($paramsStringArray as $value) {
		$ipos = strpos ($value, '=');
		if ($ipos !==false) {
			$params[substr($value,0,$ipos)] = substr($value,$ipos+1);
		}
	}	
	if (!isset($params['btiebr1']) OR $params['btiebr1'] == 0) {   //Standardbelegung
		$params['btiebr1'] = 1;
		$params['btiebr2'] = 2;
		$params['btiebr3'] = 3;
		$params['btiebr4'] = 4;
		$params['btiebr5'] = 0;
		$params['btiebr6'] = 0;
	}
	if (!isset($params['bnpdf']) OR $params['bnpdf'] == 0) {   //Standardbelegung
		$params['bnpdf'] = round(40/($liga[0]->stamm+1));
	}

// Konfigurationsparameter auslesen
$config = clm_core::$db->config();
	$telefon= $config->man_tel;
	$mobil	= $config->man_mobil;
	$mail	= $config->man_mail;

	// Userkennung holen
	$user	=JFactory::getUser();
	$jid	= $user->get('id');

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

// Überschrift Fontgröße Standard = 14
$head_font = 14;
// Fontgröße Standard = 9
$font = 9;
// Fontgröße Datum = 8
$date_font = 8;
// Zellenhöhe -> Standard 6
$zelle = 4;
// Linker Rand
$lrand = 10;



// Datum der Erstellung
$date =JFactory::getDate();
$now = $date->toSQL();

$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Times','',$date_font);
	$pdf->Cell($lrand,3,' ',0,0);
	$pdf->Cell(175,2,utf8_decode(JText::_('WRITTEN')).' '.utf8_decode(JText::_('ON_DAY')).' '.utf8_decode(JHTML::_('date',  $now, JText::_('DATE_FORMAT_CLM_PDF'))),0,1,'R');
	
	$pdf->SetFont('Times','B',$head_font);
	$pdf->Cell($lrand,$zelle+2,' ',0,0);
	$pdf->Cell(100,$zelle+2,utf8_decode(JText::_('LEAGUE_STAT_BEST')).' : '.utf8_decode($liga[0]->name),0,1,'L');

	$ex = 0; $ey = 0;
	for ($x=0; $x < ($liga[0]->stamm+1); $x++) { 
		$pdf->SetTextColor(0);
		$pdf->SetFillColor(255); 	
		if ($x < $liga[0]->stamm) $xtext = $x+1; else $xtext = JText::_('LEAGUE_STAT_ERSATZ'); 
		$pdf->Cell($lrand,$zelle+2,' ',0,0);
		$pdf->SetFont('Times','',$head_font-2);
		$pdf->Cell(100,$zelle+2,utf8_decode(JText::_('LEAGUE_STAT_BRETT')).' '.$xtext,0,1,'L');
		$pdf->SetFont('Times','',$font-1);
		$pdf->SetTextColor(0);
		$pdf->SetFillColor(200); 	
		$pdf->Cell($lrand,$zelle,' ',0,0);
		$pdf->Cell(8,$zelle,JText::_('LEAGUE_STAT_BRETT'),1,0,'C',1);
		$pdf->Cell(35,$zelle,JText::_('DWZ_NAME'),1,0,'L',1);
		$pdf->Cell(10,$zelle,JText::_('LEAGUE_STAT_DWZ'),1,0,'R',1);
		$pdf->Cell(45,$zelle,JText::_('LEAGUE_STAT_CLUB'),1,0,'L',1);
		for ($xx=1; $xx < 7; $xx++) {   //max. 6 Spalten
			$en = 0;
			if ($params['btiebr'.$xx] == 1) { $hstring = JText::_('LEAGUE_STAT_PLAYERPOINTS'); $width = 9; }
			elseif ($params['btiebr'.$xx] == 2) { $hstring = JText::_('LEAGUE_STAT_PLAYERGAMES'); $width = 9; }
			elseif ($params['btiebr'.$xx] == 3) { $hstring = JText::_('LEAGUE_STAT_PLAYERLEVEL'); $width = 12; }
			elseif ($params['btiebr'.$xx] == 4) { $hstring = JText::_('LEAGUE_STAT_RATING_PDF'); $en = 1; $width = 13; }
			elseif ($params['btiebr'.$xx] == 5) { $hstring = JText::_('LEAGUE_STAT_PERCENT'); $width = 9; }
			elseif ($params['btiebr'.$xx] == 6) { $hstring = JText::_('LEAGUE_STAT_PLAYERPOINTS'); $width = 11; $en = 3; $ey = 1; }
			elseif ($params['btiebr'.$xx] == 7) { $hstring = JText::_('LEAGUE_STAT_PLAYERGAMES'); $width = 11; $en = 3; $ey = 1; }
			elseif ($params['btiebr'.$xx] == 8) { $hstring = JText::_('LEAGUE_STAT_PERCENT'); $width = 9; $en = 3; $ey = 1; }
			elseif ($params['btiebr'.$xx] == 9) { $hstring = JText::_('LEAGUE_STAT_BNUMBERS_PDF'); $width = 13; }
			if ($params['btiebr'.$xx] > 0) { 
				if ($en == 1) $pdf->Cell($width,$zelle,utf8_decode($hstring).html_entity_decode('&sup1;',ENT_COMPAT),1,0,'C',1);
				elseif ($en == 3) $pdf->Cell($width,$zelle,utf8_decode($hstring).html_entity_decode('&sup3;',ENT_COMPAT),1,0,'C',1);
				else $pdf->Cell($width,$zelle,utf8_decode($hstring),1,0,'C',1);
			}
		}
		$pdf->Cell(1,$zelle,'',0,1,'C');
		$pdf->SetFont('Times','',$font);
		$pdf->SetTextColor(0);
		$xb = 1;
		foreach ( $bestenliste as $spielerbrett ) {
			if ($xb > $params['bnpdf']) break;
			if (($spielerbrett->snr == ($x+1) AND $x < $liga[0]->stamm) OR
			($spielerbrett->snr > $liga[0]->stamm AND $x >= $liga[0]->stamm)) { $xb++; 
				if ($xb%2 != 0) { $pdf->SetFillColor(240); }
				else { $pdf->SetFillColor(255); }
				$pdf->SetFont('Times','',$font);
			$pdf->Cell($lrand,$zelle,' ',0,0);
			$pdf->Cell(8,$zelle,$spielerbrett->snr,1,0,'C',1);
			$pdf->Cell(35,$zelle,utf8_decode($spielerbrett->Spielername),1,0,'L',1);
			$pdf->Cell(10,$zelle,$spielerbrett->DWZ,1,0,'R',1);
			$pdf->Cell(45,$zelle,utf8_decode($spielerbrett->Vereinname),1,0,'L',1);
			for ($xx=1; $xx < 7; $xx++) {   //max. 6 Spalten
				$en = 0;
				//if ($params['btiebr'.$xx] == 1) { $hstring = $spielerbrett->gpunkte; $width = 9; }
				if ($params['btiebr'.$xx] == 1) { $hstring = $spielerbrett->Punkte; $width = 9; }
				//elseif ($params['btiebr'.$xx] == 2) { $hstring = $spielerbrett->gpartien; $width = 9; }
				elseif ($params['btiebr'.$xx] == 2) { $hstring = $spielerbrett->Partien; $width = 9; }
				elseif ($params['btiebr'.$xx] == 3) { $hstring = $spielerbrett->Niveau; $width = 12; }
				elseif ($params['btiebr'.$xx] == 4) { if (($spielerbrett->Punkte == $spielerbrett->Partien) AND ($spielerbrett->Leistung > 0)) { $hstring = ($spielerbrett->Niveau)+667; $en = 2; $ex = 1; } else { $hstring = $spielerbrett->Leistung;} $width = 13; }
				//elseif ($params['btiebr'.$xx] == 5) { $hstring = round($spielerbrett->gprozent,1); $width = 9; }
				elseif ($params['btiebr'.$xx] == 5) { $hstring = round($spielerbrett->Prozent,1); $width = 9; }
				elseif ($params['btiebr'.$xx] == 6) { $hstring = $spielerbrett->epunkte; $width = 11; }
				elseif ($params['btiebr'.$xx] == 7) { $hstring = $spielerbrett->epartien; $width = 11; }
				elseif ($params['btiebr'.$xx] == 8) { $hstring = round($spielerbrett->eprozent,1); $width = 9; }
				elseif ($params['btiebr'.$xx] == 9) { $hstring = $spielerbrett->ebrett;  $width = 13; }
				if ($params['btiebr'.$xx] > 0) { 	
					if ($en == 2) $pdf->Cell($width,$zelle,$hstring.html_entity_decode('&sup2;',ENT_COMPAT),1,0,'C',1);
					else $pdf->Cell($width,$zelle,$hstring,1,0,'C',1);
				}
			}
			$pdf->Cell(1,$zelle,'',0,1,'C');
			}
		}
	}
	$pdf->SetFont('Times','',$font-2);
	$pdf->Cell($lrand,$zelle-2,'',0,1,'C');
	$pdf->Cell($lrand,$zelle-2,'',0,0,'C');
	$pdf->Cell(10,$zelle-2,html_entity_decode('&sup1;',ENT_COMPAT).JText::_('LEAGUE_RATING_COMMENT_PDF'),0,1,'L');
	if($ex >0) { $pdf->Cell($lrand,$zelle-2,'',0,0,'C');
				 $pdf->Cell(10,$zelle-2,html_entity_decode('&sup2;',ENT_COMPAT).JText::_('LEAGUE_RATING_IMPOSSIBLE_PDF'),0,1,'L'); }
	if($ey >0) { $pdf->Cell($lrand,$zelle-2,'',0,0,'C');
				 $pdf->Cell(10,$zelle-2,html_entity_decode('&sup3;',ENT_COMPAT).JText::_('LEAGUE_WITH_UNCONTESTED_PDF'),0,1,'L'); }

// Ausgabe
$pdf->Output(JText::_('LEAGUE_STAT_BEST').' '.utf8_decode($liga[0]->name).'.pdf','D');

?>
