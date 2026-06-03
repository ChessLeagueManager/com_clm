<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

// Eingang: ausgewälte ID's oder nichts 
function clm_api_db_brettzuordnung($lid,$runde,$format='pdf') {
	$lang = clm_core::$lang->turplayersliste;
//clm_core::$api->test_print('ulid',$lid);
//clm_core::$api->test_print('urunde',$runde);
	$out["input"]["lid"] = $lid;
	$out["input"]["runde"] = $runde;
	$out["input"]["format"] = $format;
	$date = date("Y-m-d");

	//CLM parameter auslesen
	$config		= clm_core::$db->config();
	$countryversion = $config->countryversion;

  	$turnierModel = " SELECT * "
				." FROM #__clm_turniere "
				." WHERE id = ".$lid;
	$out["turnier"] = clm_core::$db->loadObject($turnierModel);

  	$playersModel = " SELECT * "
				." FROM #__clm_turniere_tlnr "
				." WHERE turnier = ".$lid
				." ORDER BY name ";
	$out["players"] = clm_core::$db->loadObjectList($playersModel);

  	$boardsModel = " SELECT e.*, t.name, g.name as gname FROM #__clm_turniere_rnd_spl as e"
				." LEFT JOIN #__clm_turniere_tlnr as t ON e.turnier = t.turnier AND e.tln_nr = t.snr "
				." LEFT JOIN #__clm_turniere_tlnr as g ON e.turnier = g.turnier AND e.gegner = g.snr "
				." WHERE e.turnier = $lid AND e.runde = $runde "
				." ORDER BY t.name ";
	$out["boards"] = clm_core::$db->loadObjectList($boardsModel);
//clm_core::$api->test_print('boardsModel',$boardsModel);
//clm_core::$api->test_print('uboards',$out["boards"]);

	// Kein Ergebnis -> Daten Inkonsistent oder falsche Eingabe
	if (!isset($out["boards"][0])) {
		return array(false, "e_playerslisteError");
	}	

  	$countModel = " SELECT COUNT(IF((tlnrStatus = 1),1,NULL)) as active, COUNT(IF((tlnrStatus = 0),1,NULL)) as deactive  "
				." FROM #__clm_turniere_tlnr "
				." WHERE turnier = ".$lid;
	$tanz = clm_core::$db->loadObject($countModel);

	$turnier 	= $out["turnier"];
	$boards 	= $out["boards"];
	$now = time();

if ($format == 'pdf') {
	ob_end_clean();
	
	$_POST['pdf_orientation'] = 'P';

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
	foreach ($boards as $pl) {
		if ($pl->brett < 0) continue;
		if ($pl->name <= '') continue;
		$x++;
		$zanz++;
		if ($zanz == $max_anz) $zanz = 1;
		if ($zanz == 1) {
			$pdf->AddPage('L');

			$pdf->SetFont('Times','B',$date_font+2);
				$pdf->Cell(10,3,' ',0,0);
				$pdf->Cell(175,3,clm_core::$load->utf8decode(Text::_('WRITTEN')).' '.clm_core::$load->utf8decode(Text::_('ON_DAY')).' '.clm_core::$load->utf8decode(HTMLHelper::_('date',  $now, $lang->date_format_clm_pdf)),0,1,'R');
				$pdf->Ln(1);    	
				
			$pdf->SetFont('Times','B',$head_font);	
				$pdf->Cell($leer+7,$zelle,' ',0,0,'L');
				$pdf->Cell(84,7,clm_core::$load->utf8decode($turnier->name),0,0,'L');
			$pdf->SetFont('Times','',$head_font-2);	
				$pdf->Cell(40,7,' Brettzuordnung Runde:',0,0,'R');
			$pdf->SetFont('Times','',$head_font);	
				$pdf->Cell(1,7,$runde,0,1,'LB');
				$pdf->Ln(1);    	
			$pdf->SetFont('Times','',$font-1);
			$pdf->SetFillColor(100);
			$pdf->SetTextColor(255);
				$pdf->Cell($leer+7,$zelle,' ',0,0,'L');
				$pdf->Cell(8,$zelle,$lang->lnr,1,0,'C',1);
			$pdf->SetFont('Times','',$font+2);
				$pdf->Cell(45,$zelle,$lang->name,1,0,'L',1);
				$pdf->Cell(12,$zelle,$lang->brett,1,0,'L',1);
				$pdf->Cell(16,$zelle,$lang->farbe,1,0,'L',1);
				$pdf->Cell(45,$zelle,$lang->gegner,1,0,'L',1);
//				$pdf->Cell(40,$zelle,'',1,0,'C',1);
				$pdf->Cell(1,$zelle,'',0,1,'L');

			// Anzahl der Teilnehmer durchlaufen
			$pdf->SetFillColor(240);
			$pdf->SetTextColor(0);
		}
		if ($x%2 != 0) { $fc = 1; } else { $fc = 0; }
		$pdf->SetFont('Times','',$font-1);
		$pdf->Cell($leer+7,$zelle,' ',0,0,'L');
		$pdf->Cell(8,$zelle,$x,1,0,'C',$fc);
		$pdf->SetFont('Times','',$font+1);
		$pdf->BreakCell(45,$zelle,clm_core::$load->utf8decode($pl->name),1,0,'L',$fc);
		$pdf->Cell(12,$zelle,$pl->brett,1,0,'L',$fc);
		if ($pl->heim == 1) $farbe = 'Weiß';
		elseif ($pl->heim == 0) $farbe = 'Schwarz';
		else $farbe = '';
		$pdf->Cell(16,$zelle,clm_core::$load->utf8decode($farbe),1,0,'L',$fc);
		$pdf->BreakCell(45,$zelle,clm_core::$load->utf8decode($pl->gname),1,0,'L',$fc);
//		$pdf->Cell(40,$zelle,'',1,0,'C',$fc);
		$pdf->Cell(1,$zelle,'',0,1,'L');
	}
//die('stop-pdf');	
	// Ausgabe
	$pdf->Output('Brettzuordnung_Runde_'.$runde.' '.clm_core::$load->utf8decode($turnier->name).'.pdf','D');
	exit;
	
}

	return array(true, "m_brettzuordnungSuccess", $file_name);

}
?>
