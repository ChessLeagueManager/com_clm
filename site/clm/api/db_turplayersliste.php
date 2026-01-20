<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
// Eingang: ausgewälte ID's oder nichts 
function clm_api_db_turplayersliste($lid,$format='csv') {
	$lang = clm_core::$lang->turplayersliste;
	$out["input"]["lid"] = $lid;
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

	// Kein Ergebnis -> Daten Inkonsistent oder falsche Eingabe
	if (!isset($out["players"][0])) {
		return array(false, "e_playerslisteError");
	}	

  	$countModel = " SELECT COUNT(IF((tlnrStatus = 1),1,NULL)) as active, COUNT(IF((tlnrStatus = 0),1,NULL)) as deactive  "
				." FROM #__clm_turniere_tlnr "
				." WHERE turnier = ".$lid;
	$tanz = clm_core::$db->loadObject($countModel);

	$turnier 	= $out["turnier"];
	$players 	= $out["players"];
	$now = time();

if ($format == 'csv') {
	$output = array();
	
	$first = true;
// Playerschleife 	
	foreach ($players as $pl) { 
		if ($first) {
			$first = false;
			$line = array();	
			$line[1] = $lang->status;
			$line[2] = $lang->titel;
			$line[3] = $lang->name;
			$line[4] = $lang->verein;
			$line[5] = $lang->geburtsjahr;
			$line[6] = $lang->geschlecht;
			$line[7] = $lang->startgeld;
			$line[8] = $lang->zahltag;
			$line[9] = $lang->reason;
			$line[10] = $lang->zps;
			$line[11] = $lang->mglnr;
			$line[12] = $lang->dwz;
			$line[13] = $lang->elo;
			$line[14] = $lang->twz;
			$output[] = $line;
		}

		$line = array();	
		$line[1] = $pl->tlnrStatus;
		$line[2] = $pl->titel;
		$line[3] = str_replace(array('„','“','"','”'),' ',$pl->name);
		$line[3] = clm_core::$load->utf8decode(str_replace("'",' ',$line[3]));
		$line[4] = str_replace(array('„','“','"','”'),' ',$pl->verein);
		$line[4] = clm_core::$load->utf8decode(str_replace("'",' ',$line[4]));
		$line[5] = $pl->birthYear;
		$line[6] = $pl->geschlecht;
		$line[7] = $pl->amount_paid;
		$line[8] = $pl->date_paid;
		$line[9] = $pl->reason;
		$line[10] = $pl->zps;
		$line[11] = $pl->mgl_nr;
		$line[12] = $pl->start_dwz;
		$line[13] = $pl->FIDEelo;
		$line[14] = $pl->twz;

		$output[] = $line;
    } 

// Ausgabe
	if(count($output)==0) {
		return array(false, "e_playerslisteNoDataError");
	}
		
	$nl = "\n";
	$file_name = $lang->title.' '.$turnier->name;
	$file_name .= '.csv'; 
	$file_name = clm_core::$load->file_name($file_name);
	if (!file_exists('components'.DS.'com_clm'.DS.'pgn'.DS)) mkdir('components'.DS.'com_clm'.DS.'pgn'.DS);
	$pdatei = fopen('components'.DS.'com_clm'.DS.'pgn'.DS.$file_name,"wt");
	foreach($output as $line1) {
		$return = fputcsv($pdatei, $line1);
	}
	fclose($pdatei);

		$file = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'pgn'.DS.$file_name; 
		if (file_exists($file)) {
			header ( 'Content-Description: File Transfer' );
			header('Content-Disposition: attachment; filename="'.$file_name.'"');
			header('Content-type: application/csv');
			header ( 'Expires: 0' );
			header ( 'Cache-Control: must-revalidate' );
			header ( 'Pragma: public' );
			header ( 'Content-Length: ' . filesize ( $file ) );
			ob_clean();
			flush();
			readfile($file);
			flush();
			exit;
		} 
}
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
	foreach ($players as $pl) {
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
				$pdf->Cell(140,7,clm_core::$load->utf8decode($turnier->name),0,0,'L');
			$pdf->SetFont('Times','',$head_font-4);	
				$pdf->Cell(95,7,'Standard-Startgeld:'.$turnier->entry_fee.' - '.$tanz->active.clm_core::$load->utf8decode(' TL bestätigt / ').$tanz->deactive.' TL in Wartestatus',0,1,'LB');
				$pdf->Ln(1);    	
			$pdf->SetFont('Times','',$font+2);
			$pdf->SetFillColor(100);
			$pdf->SetTextColor(255);
				$pdf->Cell($leer+7,$zelle,' ',0,0,'L');
				$pdf->Cell(8,$zelle,$lang->lnr,1,0,'C',1);
				$pdf->Cell(8,$zelle,$lang->titel,1,0,'L',1);
				$pdf->Cell(45,$zelle,$lang->name,1,0,'L',1);
				$pdf->Cell(45,$zelle,$lang->verein,1,0,'L',1);
				$pdf->Cell(12,$zelle,$lang->geburtsjahr,1,0,'L',1);
				$pdf->Cell(6,$zelle,$lang->geschlecht,1,0,'C',1);
				$pdf->Cell(16,$zelle,$lang->startgeld,1,0,'R',1);
				$pdf->Cell(20,$zelle,$lang->zahltag,1,0,'C',1);
				$pdf->Cell(40,$zelle,'',1,0,'C',1);
				$pdf->Cell(40,$zelle,'',1,0,'C',1);
				$pdf->Cell(1,$zelle,'',0,1,'L');

			// Anzahl der Teilnehmer durchlaufen
			$pdf->SetFillColor(240);
			$pdf->SetTextColor(0);
		}
		if ($x%2 != 0) { $fc = 1; } else { $fc = 0; }
		$pdf->Cell($leer+7,$zelle,' ',0,0,'L');
		if ($pl->tlnrStatus == 0) {
			$pdf->SetFillColor(190);
			$pdf->Cell(8,$zelle,$x,1,0,'C',1);
			$pdf->SetFillColor(240);
		} else {
			$pdf->Cell(8,$zelle,$x,1,0,'C',$fc);
		}
		$pdf->Cell(8,$zelle,clm_core::$load->utf8decode($pl->titel),1,0,'L',$fc);
		$pdf->BreakCell(45,$zelle,clm_core::$load->utf8decode($pl->name),1,0,'L',$fc);
		$pdf->BreakCell(45,$zelle,clm_core::$load->utf8decode($pl->verein),1,0,'L',$fc);
		$pdf->Cell(12,$zelle,clm_core::$load->utf8decode($pl->birthYear),1,0,'L',$fc);
		$pdf->Cell(6,$zelle,clm_core::$load->utf8decode($pl->geschlecht),1,0,'C',$fc);
		$pdf->Cell(16,$zelle,clm_core::$load->utf8decode($pl->amount_paid),1,0,'R',$fc);
		$pdf->Cell(20,$zelle,clm_core::$load->utf8decode($pl->date_paid),1,0,'C',$fc);
		$pdf->Cell(40,$zelle,clm_core::$load->utf8decode($pl->reason),1,0,'L',$fc);
		$pdf->Cell(40,$zelle,'',1,0,'C',$fc);
		$pdf->Cell(1,$zelle,'',0,1,'L');
	}
	
	// Ausgabe
	$pdf->Output($lang->title.' '.clm_core::$load->utf8decode($turnier->name).'.pdf','D');
	exit;
	
}

	return array(true, "m_playerslisteSuccess", $file_name);

}
?>
