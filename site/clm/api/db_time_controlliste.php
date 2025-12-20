<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
// Input: table clm_zeitmodus 
// Output: pdf-Liste Zeitmodi
function clm_api_db_time_controlliste($format='pdf') {
	$lang = clm_core::$lang->turplayersliste;
	$out = array();
	$out["input"]["format"] = $format;
	$date = date("Y-m-d");
	//CLM parameter auslesen
	$config		= clm_core::$db->config();
	$countryversion = $config->countryversion;

  	$tcModel = " SELECT * "
				." FROM #__clm_zeitmodus "
				." WHERE published = 1 "
				." ORDER BY time60, ordering ";
	$out["tc"] = clm_core::$db->loadObjectList($tcModel);

	// Kein Ergebnis -> Daten Inkonsistent oder falsche Eingabe
	if (!isset($out["tc"][0])) {
		return array(false, "e_tclisteError");
	}	

	$tc 	= $out["tc"];
	$now = time();

if ($format == 'pdf') {
	ob_end_clean();
	
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
	$zelle = 4;
	// Wert von Zellenbreite abziehen
	// Bspl. für Standard (Null) für Liga mit 11 Runden und 1 Durchgang
	$breite = 0;
	$rbreite = 0;
	$nbreite = 0;
	// Überschrift Fontgröße Standard = 14
	$head_font = 12;
	// Fontgröße Standard = 9
	$font = 8;
	// Fontgröße Standard = 8
	$date_font = 6;
	// Leere Zelle zum zentrieren
	$leer = 2;

	$pdf=new PDF();
	$pdf->AliasNbPages();
	$pdf->AddPage();

	$pdf->SetFont('Times','',$date_font);
		$pdf->Cell(10,3,' ',0,0);
		$pdf->Cell(175,2,clm_core::$load->utf8decode(JText::_('WRITTEN')).' '.clm_core::$load->utf8decode(JText::_('ON_DAY')).' '.clm_core::$load->utf8decode(JHTML::_('date',  $now, $lang->date_format_clm_pdf)),0,1,'R');
		
	$pdf->SetFont('Times','B',$head_font+2);	
		$pdf->Cell(180,10,'Timecontrolmodi',0,1,'C');
		$pdf->Ln(4);    	
	$pdf->SetFont('Times','',$font);
	$pdf->SetFillColor(100);
	$pdf->SetTextColor(255);
		$pdf->Cell($leer,$zelle,' ',0,0,'L');
		$pdf->Cell(5,$zelle,'lNr',1,0,'C',1);
		$pdf->Cell(13,$zelle,'Typ',1,0,'C',1);
		$pdf->Cell(40,$zelle,'Code',1,0,'L',1);
		$pdf->Cell(10,$zelle,'Zeit/60',1,0,'L',1);
		$pdf->Cell(120,$zelle,'Beschreibung',1,0,'L',1);
		$pdf->Cell(1,$zelle,' ',0,1,'L');

	// Anzahl der Teilnehmer durchlaufen
	$pdf->SetFillColor(240);
	$pdf->SetTextColor(0);
	$x = 0;
	foreach ($tc as $pl) {
		$x++;
		if ($x%2 != 0) { $fc = 1; } else { $fc = 0; }
		$pdf->Cell($leer,$zelle,' ',0,0,'L');
		$pdf->Cell(5,$zelle,$x,1,0,'C',$fc);
		$pdf->Cell(13,$zelle,clm_core::$load->utf8decode($pl->typ),1,0,'L',$fc);
		$pdf->Cell(40,$zelle,clm_core::$load->utf8decode($pl->trf),1,0,'L',$fc);
		$pdf->Cell(10,$zelle,$pl->time60,1,0,'L',$fc);
		$pdf->Cell(120,$zelle,clm_core::$load->utf8decode($pl->name),1,0,'L',$fc);
		$pdf->Cell(1,$zelle,' ',0,1,'L');
	}
	
	// Ausgabe
	$pdf->Output('Zeitmodus.pdf','D');
	exit;
	
	
}

	return array(true, "m_playerslisteSuccess", $file_name);

}
?>
