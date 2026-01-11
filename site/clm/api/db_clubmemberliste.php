<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
// Eingang: VereinsZPS, Saison und Sortierkriterium
function clm_api_db_clubmemberliste($sid,$zps,$format='pdf',$filter_sort = '0') {
	$lang = clm_core::$lang->turplayersliste;
	$out["input"]["sid"] = $sid;
	$out["input"]["zps"] = $zzps;
	$out["input"]["format"] = $format;
	$date = date("Y-m-d");

	//CLM parameter auslesen
	$config		= clm_core::$db->config();
	$countryversion = $config->countryversion;
	$test_button = $config->test_button;

  	$vereinModel = " SELECT a.*, s.name as season_name "
				." FROM #__clm_vereine as a "
				." LEFT JOIN #__clm_saison AS s ON s.id = a.sid "
				." WHERE a.sid = ".$sid
				." AND a.zps = '".$zps."'";
	$out["verein"] = clm_core::$db->loadObject($vereinModel);

  	$spielerModel = " SELECT * "
				." FROM #__clm_dwz_spieler "
				." WHERE sid = ".$sid
				." AND ZPS = '".$zps."'";
	if(!is_null($filter_sort) AND $filter_sort !="0") {
		$spielerModel .= " ORDER BY ".$filter_sort;
	} else {
		$spielerModel .= " ORDER BY Spielername ASC ";
	}
	$out["spieler"] = clm_core::$db->loadObjectList($spielerModel);

	// Kein Ergebnis -> Daten Inkonsistent oder falsche Eingabe
	if (!isset($out["spieler"][0])) {
		return array(false, "e_playerslisteError");
	}	

	$verein 	= $out["verein"];
	$spieler 	= $out["spieler"];
	$now = time();

if ($format == 'pdf') {
	ob_end_clean();
	
	$_POST['pdf_orientation'] = 'L';

	require_once (clm_core::$path.DS.'classes'.DS.'fpdf.php');

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
	$zelle = 5;
	// Wert von Zellenbreite abziehen
	// Bspl. für Standard (Null) für Liga mit 11 Runden und 1 Durchgang
	$breite = 0;
	$rbreite = 0;
	$nbreite = 0;
	// Überschrift Fontgröße Standard = 14
	$head_font = 14;
	// Fontgröße Standard = 9
	$font = 9;
	// Fontgröße Standard = 8
	$date_font = 6;
	// Leere Zelle zum zentrieren
/*	$leer = (3 * (10-($liga[0]->teil-$diff)))-$rbreite;
	if ( $liga[0]->b_wertung == 0) $leer = $leer + 4;
	if ($leer < 3) $leer = 2;
*/	$leer = 2;

	$pdf=new PDF();
	$pdf->AliasNbPages();
	
	$zanz = 0; $max_anz = 31;
	$x = 0;
	foreach ($spieler as $pl) {
		$x++;
		$zanz++;
		if ($zanz == $max_anz) $zanz = 1;
		if ($zanz == 1) {
			$pdf->AddPage('L');

			$pdf->SetFont('Times','B',$date_font+2);
				$pdf->Cell(10,3,' ',0,0);
				$pdf->Cell(175,3,clm_core::$load->utf8decode(JText::_('WRITTEN')).' '.clm_core::$load->utf8decode(JText::_('ON_DAY')).' '.clm_core::$load->utf8decode(JHTML::_('date',  $now, $lang->date_format_clm_pdf)),0,1,'R');
				$pdf->Ln(1);    	
				
			$pdf->SetFont('Times','B',$head_font);	
				$pdf->Cell($leer+7,$zelle,' ',0,0,'L');
				$pdf->Cell(140,7,clm_core::$load->utf8decode($verein->name),0,0,'L');
			$pdf->SetFont('Times','',$head_font-4);	
//				$pdf->Cell(95,7,'Standard-Startgeld:'.$turnier->entry_fee.' - '.$tanz->active.clm_core::$load->utf8decode(' TL bestätigt / ').$tanz->deactive.' TL in Wartestatus',0,1,'LB');
				$pdf->Ln(1);    	
			$pdf->SetFont('Times','',$font+2);
			$pdf->SetFillColor(100);
			$pdf->SetTextColor(255);
				$pdf->Cell($leer+2,$zelle,' ',0,0,'L');
				$pdf->Cell(8,$zelle,$lang->lnr,1,0,'C',1);
				$pdf->Cell(11,$zelle,$lang->titel,1,0,'L',1);
				$pdf->Cell(44,$zelle,$lang->name,1,0,'L',1);
				$pdf->Cell(10,$zelle,'Mitgl.Nr',1,0,'L',1);
				$pdf->Cell(20,$zelle,'PKZ',1,0,'C',1);
				$pdf->Cell(12,$zelle,$lang->geburtsjahr,1,0,'C',1);
				$pdf->Cell(6,$zelle,$lang->geschlecht,1,0,'C',1);
				$pdf->Cell(18,$zelle,'DWZ',1,0,'C',1);
				$pdf->Cell(12,$zelle,'Elo',1,0,'C',1);
				$pdf->Cell(19,$zelle,'Fide-ID',1,0,'C',1);
				$pdf->Cell(22,$zelle,'Eintritt',1,0,'C',1);
				$pdf->Cell(22,$zelle,'Austritt',1,0,'C',1);
				if ($test_button == 1) {
					$pdf->Cell(33,$zelle,'created',1,0,'C',1);
					$pdf->Cell(33,$zelle,'updated',1,0,'C',1);
				}
				$pdf->Cell(1,$zelle,'',0,1,'L');

			// Anzahl der Teilnehmer durchlaufen
			$pdf->SetFillColor(245);
			$pdf->SetTextColor(0);
		}
		if ($x%2 != 0) { $fc = 1; } else { $fc = 0; }
		$pdf->Cell($leer+2,$zelle,' ',0,0,'L');
		if ($pl->leavingdate > '1970-01-01') {
			$pdf->SetFillColor(190);
			$pdf->Cell(8,$zelle,$x,1,0,'C',1);
			$pdf->SetFillColor(245);
		} else {
			$pdf->Cell(8,$zelle,$x,1,0,'C',$fc);
		}
		$pdf->Cell(11,$zelle,clm_core::$load->utf8decode($pl->FIDE_Titel),1,0,'L',$fc);
		$pdf->BreakCell(44,$zelle,clm_core::$load->utf8decode($pl->Spielername),1,0,'L',$fc);
		$pdf->Cell(10,$zelle,clm_core::$load->utf8decode($pl->Mgl_Nr),1,0,'L',$fc);
		$pdf->Cell(20,$zelle,clm_core::$load->utf8decode($pl->PKZ),1,0,'C',$fc);
		$pdf->Cell(12,$zelle,clm_core::$load->utf8decode($pl->Geburtsjahr),1,0,'C',$fc);
		$pdf->Cell(6,$zelle,clm_core::$load->utf8decode($pl->Geschlecht),1,0,'C',$fc);
		if ($pl->DWZ > 0) $dwz = $pl->DWZ; else $dwz = '';
		if ($pl->DWZ_Index > 0) $dwz .= '-'.$pl->DWZ_Index;
		$pdf->Cell(18,$zelle,$dwz,1,0,'C',$fc);
		if ($pl->FIDE_Elo > 0) $elo = $pl->FIDE_Elo; else $elo = '';
		$pdf->Cell(12,$zelle,$elo,1,0,'C',$fc);
		if ($pl->FIDE_ID > 0) $fideid = $pl->FIDE_ID; else $fideid = '';
		$pdf->Cell(19,$zelle,$fideid,1,0,'L',$fc);
		if ($pl->joiningdate == '1970-01-01') $pl->joiningdate = '';
		if ($pl->leavingdate == '1970-01-01') $pl->leavingdate = '';
		$pdf->Cell(22,$zelle,$pl->joiningdate,1,0,'C',$fc);
		$pdf->Cell(22,$zelle,$pl->leavingdate,1,0,'C',$fc);
		if ($test_button == 1) {
			$pdf->Cell(33,$zelle,substr($pl->created,0,-3),1,0,'L',$fc);
			$pdf->Cell(33,$zelle,substr($pl->updated,0,-3),1,0,'L',$fc);
		}
		$pdf->Cell(1,$zelle,'',0,1,'L');
	}
	
	// Ausgabe
	$pdf->Output('Mitgliederliste '.clm_core::$load->utf8decode($verein->name).'.pdf','D');
	exit;
	
}

	return array(true, "m_playerslisteSuccess", $file_name);

}
?>
