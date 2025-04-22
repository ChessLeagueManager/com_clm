<?php
/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_view_schedule_pdf($out)
{
    $lang = clm_core::$lang->schedule;

    // Variablen initialisieren
    $paar 		= $out["paar"];
    $club 		= $out["club"];

    //CLM parameter auslesen
    $config = clm_core::$db->config();
    $countryversion = $config->countryversion;


    //require_once(JPATH_COMPONENT.DS.'includes'.DS.'fpdf.php');
    require(clm_core::$path.DS.'classes'.DS.'fpdf.php');

    class PDF extends FPDF
    {
        //Kopfzeile
        public function Header()
        {
            //require(JPATH_COMPONENT.DS.'includes'.DS.'pdf_header.php');
            require(clm_core::$path.DS.'includes'.DS.'pdf_header.php');
        }
        //Fusszeile
        public function Footer()
        {
            //require(JPATH_COMPONENT.DS.'includes'.DS.'pdf_footer.php');
            require(clm_core::$path.DS.'includes'.DS.'pdf_footer.php');
        }
    }


    $pdf = new PDF();
    $pdf->AliasNbPages();

    // Überschrift Fontgröße Standard = 14
    $head_font = 14;
    // Fontgröße Standard = 9
    $font = 9;
    // Fontgröße Datum = 8
    $date_font = 8;
    // Zellenhöhe -> Standard 6
    $zelle = 6;
    // Wert von Zellenbreiten
    $breite0 = 10;
    $br_date = 28;
    $br_lname = 38;
    $br_dg = 6;
    $br_rd = 6;
    $br_team = 40;
    $br_result = 15;

    $pdf_orientation = 'P';
    $pdf_width = 195;
    $pdf_length	= 290;

    $now = time();
    $first = true;

    // Terminschleife
    $x = 0;
    foreach ($paar as $paar1) {
        if ($first or (($pdf_length - 20) < $pdf->GetY())) {
            $first = false;
            $pdf->AddPage();
            $pdf->SetFont('Times', '', $date_font);
            $pdf->SetFillColor(240);
            $pdf->SetTextColor(0);
            $pdf->Cell(10, 3, ' ', 0, 0);
            $pdf->Cell($pdf_width - 20, 4, clm_core::$load->utf8decode($lang->written.' '.$lang->date_on.' '.clm_core::$cms->showDate($now, $lang->date_format_clm_pdf)), 0, 1, 'R');
            $pdf->SetFont('Arial', 'B', $head_font);
            $pdf->Cell($breite0, 3, ' ', 0, 0);
            $pdf->Cell($pdf_width - 20, 7, clm_core::$load->utf8decode($club[0]->name." - ".$club[0]->season_name), 0, 1, 'L');
            $pdf->Cell(10, 2, ' ', 0, 1);
            $pdf->SetFont('Times', '', $font);
            $pdf->SetFillColor(120);
            $pdf->SetTextColor(255);
            $pdf->Cell($breite0, $zelle, ' ', 0, 0);
            $pdf->Cell($br_date, $zelle, $lang->date, 0, 0, 'C', 1);
            $pdf->Cell($br_lname, $zelle, $lang->lname, 0, 0, 'C', 1);
            $pdf->Cell($br_dg, $zelle, $lang->dg, 0, 0, 'C', 1);
            $pdf->Cell($br_rd, $zelle, $lang->round, 0, 0, 'C', 1);
            $pdf->Cell($br_team, $zelle, $lang->home, 0, 0, 'C', 1);
            $pdf->Cell($br_result, $zelle, $lang->result, 0, 0, 'C', 1);
            $pdf->Cell($br_team, $zelle, $lang->guest, 0, 1, 'C', 1);
            $pdf->SetFont('Times', '', $font);
            $pdf->SetFillColor(240);
            $pdf->SetTextColor(0);
        }
        $x++;
        if ($x % 2 != 0) {
            $fc = 1;
        } else {
            $fc = 0;
        }
        $pdf->Cell($breite0, $zelle, ' ', 0, 0, 'C');
        if ($paar1->rdate > "1970-01-01") {
            $prdate = clm_core::$cms->showDate($paar1->rdate, "d M Y");
            if ($paar1->rtime != "00:00:00" and $paar1->rtime != "24:00:00") {
                $prdate .= ' '.substr($paar1->rtime, 0, 5);
            }
        } else {
            $prdate = '';
        }
        $pdf->Cell($br_date, $zelle, clm_core::$load->utf8decode($prdate), 1, 0, 'C', $fc);
        $pdf->Cell($br_lname, $zelle, clm_core::$load->utf8decode($paar1->lname), 1, 0, 'C', $fc);
        $pdf->Cell($br_dg, $zelle, $paar1->dg, 1, 0, 'C', $fc);
        $pdf->Cell($br_rd, $zelle, $paar1->runde, 1, 0, 'C', $fc);
        $pdf->Cell($br_team, $zelle, clm_core::$load->utf8decode($paar1->hname), 1, 0, 'C', $fc);
        $pdf->Cell($br_result, $zelle, ($paar1->brettpunkte." : ".$paar1->gbrettpunkte), 1, 0, 'C', $fc);
        //$pdf->Cell(10,$zelle,$pdf->GetY(),1,0,'C',$fc);
        $pdf->Cell($br_team,$zelle,clm_core::$load->utf8decode($paar1->gname),1,1,'C',$fc);
    }

    // Ausgabe
    $filename = clm_core::$load->make_valid(clm_core::$load->utf8decode($club[0]->name." - ".$club[0]->season_name), 20, 'outputfile');
    $pdf->Output($filename.'.pdf','D');
    //$pdf->Output(clm_core::$load->utf8decode($club[0]->name." - ".$club[0]->season_name).'.pdf','D');
    exit();
    return;
}
?>
 
