<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2014 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access');

$sid			= JRequest::getInt('saison','1');
$config			= &JComponentHelper::getParams( 'com_clm' );

$termine		= $this->termine;
 
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

// Zellenhöhe -> Standard 5
$zelle = 5;
// Zellenbreiten je Spalte
$breite = 0;
$br00 = 10;
$br01 = 43;
$br02 = 50;
$br03 = 82;
$br99 = 175;
// Überschrift Fontgröße Standard = 14
$head_font = 14;
// Fontgröße Standard = 10
$font = 10;
// Fontgröße Standard = 9
$date_font = 9;
$page_length = 375;

$arrMonth = array(
    "January" => "Januar",
    "February" => "Februar",
    "March" => "März",
    "April" => "April",
    "May" => "Mai",
    "June" => "Juni",
    "July" => "Juli",
    "August" => "August",
    "September" => "September",
    "October" => "Oktober",
    "November" => "November",
    "December" => "Dezember"
	);
$arrWochentag = array( 
	"Monday" => "Montag", 
	"Tuesday" => "Dienstag", 
	"Wednesday" => "Mittwoch", 
	"Thursday" => "Donnerstag", 
	"Friday" => "Freitag", 
	"Saturday" => "Samstag", 
	"Sunday" => "Sonntag", 
	);
            
// Datum der Erstellung
$date =& JFactory::getDate();
$now = $date->toMySQL();

$pdf=new PDF();
$pdf->AliasNbPages();
$date = date("Y-m-d");
		
// START : Terminschleife
	$tt = 0; $t1 = 0;
	for ($t = 0 ; $t < count ($termine); $t++) {
        if ( $date <= $termine[$t]->datum ) {
			$t1++;
        //Anzahl Termine pro Seite
		if ($tt > 42) $tt = 0;
		if ($tt == 0) {
			$pdf->AddPage();
			$pdf->SetFont('Times','',$date_font);
			$pdf->Cell(10,3,' ',0,0);
			$pdf->Cell(175,3,utf8_decode(JText::_('WRITTEN')).' '.utf8_decode(JText::_('ON_DAY')).' '.utf8_decode(JHTML::_('date',  $now, JText::_('DATE_FORMAT_CLM_PDF'))),0,1,'R');
	
			$pdf->SetFont('Times','',$head_font);
			$pdf->Cell(10,10,' ',0,0);
			$pdf->Cell(150,10,utf8_decode(JText::_('TERMINE_HEAD')),0,1,'L');
		}
		$pdf->SetFont('Times','',$font);
		$pdf->SetTextColor(0);
		$pdf->SetFillColor(255); 
			// Datumsberechnungen
			$datum[$t] = strtotime($termine[$t]->datum);
			$datum_arr[$t] = explode("-",$termine[$t]->datum);
			$monatsausgabe = mktime (0 , 0 , 0 , $datum_arr[$t][1]+1, 0 ,0);
        	// Monatsberechnungen
			if ( $t1 == 1 OR ( $datum_arr[$t][1] > $datum_arr[$t-1][1]) OR ( $datum_arr[$t][0] > $datum_arr[$t-1][0]) ) {
				// Jahresberechnungen
				if ( $t1 == 1 OR $datum_arr[$t][0] > $datum_arr[$t-1][0] ) { 
					$tt++;
					$pdf->SetTextColor(255);
					$pdf->SetFillColor(90); 
					$pdf->Cell($br00,$zelle," ",0,0,'C');    
					$pdf->Cell($br99,$zelle,$datum_arr[$t][0],1,1,'C',1); }
				$tt++;
				$pdf->SetTextColor(0);
				$pdf->SetFillColor(240); 
				$pdf->Cell($br00,$zelle," ",0,0,'C');    
				$pdf->Cell($br99,$zelle,utf8_decode($arrMonth[date('F',$monatsausgabe)]),1,1,'L',1);    
			} 
			$tt++;
			$pdf->SetTextColor(0);
			$pdf->SetFillColor(255); 
			$pdf->Cell($br00,$zelle," ",0,0,'C');    
			if ((isset($datum[$t-1])) AND ($datum[$t] == $datum[$t-1]) AND ($tt > 1)) { $ttext = '  '; } //$pdf->Cell($br01,$zelle," ",1,0,'C',1); }
			else  { 
				//$pdf->Cell($br01,$zelle,utf8_decode($arrWochentag[date("l",$datum[$t])]). ", " . $datum_arr[$t][2].".".$datum_arr[$t][1].".".$datum_arr[$t][0],1,0,'L',1); }
			$ttext = utf8_decode($arrWochentag[date("l",$datum[$t])]). ", " . $datum_arr[$t][2].".".$datum_arr[$t][1].".".$datum_arr[$t][0]; }
			if ($termine[$t]->starttime != '00:00:00') $ttext .= '  '.substr($termine[$t]->starttime,0,5); 
			$pdf->Cell($br01,$zelle,$ttext,1,0,'R',1); 
			$pdf->Cell($br02,$zelle,utf8_decode($termine[$t]->name),1,0,'L',1);        
			$pdf->Multicell($br03,$zelle,utf8_decode($termine[$t]->typ),1,1,'L',1);        
        }   
		}
        // ENDE : Terminschleife 
        		
// Ausgabe
$pdf->Output(utf8_decode(JText::_('TERMINE_HEAD')).'.pdf','D');


?>