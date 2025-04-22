<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

$sid			= clm_core::$load->request_int('saison', 1);
$config			= clm_core::$db->config();

$termine		= $this->termine;

require_once(clm_core::$path.DS.'classes'.DS.'fpdf.php');

class PDF extends FPDF
{
    //Kopfzeile
    public function Header()
    {
        require(clm_core::$path.DS.'includes'.DS.'pdf_header.php');
    }
    //Fusszeile
    public function Footer()
    {
        require(clm_core::$path.DS.'includes'.DS.'pdf_footer.php');
    }
}

// Zellenhöhe -> Standard 5
$zelle = 5;
// Zellenbreiten je Spalte
$breite = 0;
$br00 = 10;
$br01 = 45;
$br02 = 50;
$br03 = 80;
$br99 = 175;
// Überschrift Fontgröße Standard = 14
$head_font = 14;
// Fontgröße Standard = 10
$font = 10;
// Fontgröße Standard = 9
$date_font = 9;
$page_length = 290; //375

$arrMonth = array(
        "January" => JText::_('MOD_CLM_TERMINE_M01'),
        "February" => JText::_('MOD_CLM_TERMINE_M02'),
        "March" => JText::_('MOD_CLM_TERMINE_M03'),
        "April" => JText::_('MOD_CLM_TERMINE_M04'),
        "May" => JText::_('MOD_CLM_TERMINE_M05'),
        "June" => JText::_('MOD_CLM_TERMINE_M06'),
        "July" => JText::_('MOD_CLM_TERMINE_M07'),
        "August" => JText::_('MOD_CLM_TERMINE_M08'),
        "September" => JText::_('MOD_CLM_TERMINE_M09'),
        "October" => JText::_('MOD_CLM_TERMINE_M10'),
        "November" => JText::_('MOD_CLM_TERMINE_M11'),
        "December" => JText::_('MOD_CLM_TERMINE_M12')
    );
$arrWochentag = array(
        "Monday" => JText::_('MOD_CLM_TERMINE_T01'),
        "Tuesday" => JText::_('MOD_CLM_TERMINE_T02'),
        "Wednesday" => JText::_('MOD_CLM_TERMINE_T03'),
        "Thursday" => JText::_('MOD_CLM_TERMINE_T04'),
        "Friday" => JText::_('MOD_CLM_TERMINE_T05'),
        "Saturday" => JText::_('MOD_CLM_TERMINE_T06'),
        "Sunday" => JText::_('MOD_CLM_TERMINE_T07') );

// Datum der Erstellung
$date = JFactory::getDate();
$now = $date->toSQL();

$pdf = new PDF();
$pdf->AliasNbPages();
$date = date("Y-m-d");

// START : Terminschleife
$tt = 0;
$t1 = 0;
for ($t = 0 ; $t < count($termine); $t++) {
    if ($date <= $termine[$t]->datum) {
        $t1++;
        //Anzahl Termine pro Seite
        //if ($tt > 42) $tt = 0;
        //if ($tt == 0) {
        if (($pdf->GetY() > ($page_length - 25)) or ($tt == 0)) {
            $pdf->AddPage();
            $pdf->SetFont('Times', '', $date_font);
            $pdf->Cell(10, 3, ' ', 0, 0);
            $pdf->Cell(175, 3, clm_core::$load->utf8decode(JText::_('WRITTEN')).' '.clm_core::$load->utf8decode(JText::_('ON_DAY')).' '.clm_core::$load->utf8decode(JHTML::_('date', $now, JText::_('DATE_FORMAT_CLM_PDF'))), 0, 1, 'R');

            $pdf->SetFont('Times', '', $head_font);
            $pdf->Cell(10, 10, ' ', 0, 0);
            $pdf->Cell(150, 10, clm_core::$load->utf8decode(JText::_('TERMINE_HEAD')), 0, 1, 'L');
        }
        $pdf->SetFont('Times', '', $font);
        $pdf->SetTextColor(0);
        $pdf->SetFillColor(255);
        // Datumsberechnungen
        $datum[$t] = strtotime($termine[$t]->datum);
        $datum_arr[$t] = explode("-", $termine[$t]->datum);
        $monatsausgabe = mktime(0, 0, 0, $datum_arr[$t][1] + 1, 0, 0);
        // Monatsberechnungen
        if ($t1 == 1 or ($datum_arr[$t][1] > $datum_arr[$t - 1][1]) or ($datum_arr[$t][0] > $datum_arr[$t - 1][0])) {
            // Jahresberechnungen
            if ($t1 == 1 or $datum_arr[$t][0] > $datum_arr[$t - 1][0]) {
                $tt++;
                $pdf->SetTextColor(255);
                $pdf->SetFillColor(90);
                $pdf->Cell($br00, $zelle, " ", 0, 0, 'C');
                $pdf->Cell($br99, $zelle, $datum_arr[$t][0], 1, 1, 'C', 1);
            }
            $tt++;
            $pdf->SetTextColor(0);
            $pdf->SetFillColor(240);
            $pdf->Cell($br00, $zelle, " ", 0, 0, 'C');
            $pdf->Cell($br99, $zelle, clm_core::$load->utf8decode($arrMonth[date('F', $monatsausgabe)]), 1, 1, 'L', 1);
        }
        $tt++;
        $pdf->SetTextColor(0);
        $pdf->SetFillColor(255);
        $pdf->Cell($br00, $zelle, " ", 0, 0, 'C');
        if ((isset($datum[$t - 1])) and ($datum[$t] == $datum[$t - 1]) and ($tt > 1)) {
            $ttext = '  ';
        } //$pdf->Cell($br01,$zelle," ",1,0,'C',1); }
        else {
            //$pdf->Cell($br01,$zelle,clm_core::$load->utf8decode($arrWochentag[date("l",$datum[$t])]). ", " . $datum_arr[$t][2].".".$datum_arr[$t][1].".".$datum_arr[$t][0],1,0,'L',1); }
            $ttext = clm_core::$load->utf8decode($arrWochentag[date("l", $datum[$t])]). ", " . $datum_arr[$t][2].".".$datum_arr[$t][1].".".$datum_arr[$t][0];
        }
        if ($termine[$t]->starttime != '00:00:00') {
            $ttext .= '  '.substr($termine[$t]->starttime, 0, 5);
        }
        $pdf->Cell($br01, $zelle, $ttext, 1, 0, 'R', 1);
        //$pdf->Cell($br02,$zelle,clm_core::$load->utf8decode($termine[$t]->name),1,0,'L',1);
        $yy1 = $pdf->GetY();
        $xx1 = $pdf->GetX() + $br02;
        if (isset($termine[$t]->zname) and $termine[$t]->zname != '') {
            $zt = clm_core::$load->utf8decode($termine[$t]->zname).' - ';
        } else {
            $zt = '';
        }
        $pdf->Multicell($br02, $zelle, $zt.clm_core::$load->utf8decode($termine[$t]->name), 1, 1, 'L', 1);
        $yy3 = $pdf->GetY();
        $pdf->SetY($yy1);
        $pdf->SetX($xx1);
        $pdf->Multicell($br03, $zelle, clm_core::$load->utf8decode($termine[$t]->typ), 1, 1, 'L', 1);
        $yy4 = $pdf->GetY();
        if ($yy4 < $yy3) {
            $pdf->SetY($yy3);
        }
    }
}
// ENDE : Terminschleife

// Ausgabe
if ($t1 < 1) {
    $pdf->AddPage();
    $pdf->SetFont('Times', '', $date_font);
    $pdf->Cell(10, 3, ' ', 0, 0);
    $pdf->Cell(175, 3, clm_core::$load->utf8decode(JText::_('WRITTEN')).' '.clm_core::$load->utf8decode(JText::_('ON_DAY')).' '.clm_core::$load->utf8decode(JHTML::_('date', $now, JText::_('DATE_FORMAT_CLM_PDF'))), 0, 1, 'R');

    $pdf->SetFont('Times','',$head_font);
    $pdf->Cell(10,10,' ',0,0);
    $pdf->Cell(150,10,clm_core::$load->utf8decode(JText::_('NO_TERMINE')),0,1,'L');
}
$pdf->Output(clm_core::$load->utf8decode(JText::_('TERMINE_COMPACT')).'.pdf','D');
exit;
