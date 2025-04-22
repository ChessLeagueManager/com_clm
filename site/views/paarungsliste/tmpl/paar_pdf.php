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

$liga = $this->liga;
//Liga-Parameter aufbereiten
$paramsStringArray = explode("\n", $liga[0]->params);
$params = array();
foreach ($paramsStringArray as $value) {
    $ipos = strpos($value, '=');
    if ($ipos !== false) {
        $params[substr($value, 0, $ipos)] = substr($value, $ipos + 1);
    }
}
if (!isset($params['dwz_date'])) {
    $params['dwz_date'] = '1970-01-01';
}
if (!isset($params['round_date'])) {
    $params['round_date'] = '0';
}
$termin		= $this->termin;
$paar		= $this->paar;
$summe		= $this->summe;
$rundensumme	= $this->rundensumme;
$runden_modus = $liga[0]->runden_modus;
$a_html = array('<b>','</b>','<br>');
$a_pdf  = array('','','/n');

$runde_t = $liga[0]->runden + 1;
// Test alte/neue Standardrundenname bei 2 Durchgängen
if ($liga[0]->durchgang > 1) {
    if ($termin[$runde_t - 1]->name == JText::_('ROUND').' '.$runde_t) {  //alt
        for ($xr = 0; $xr < ($liga[0]->runden); $xr++) {
            $termin[$xr]->name = JText::_('ROUND').' '.($xr + 1)." (".JText::_('PAAR_HIN').")";
            $termin[$xr + $liga[0]->runden]->name = JText::_('ROUND').' '.($xr + 1)." (".JText::_('PAAR_RUECK').")";
        }
    }
}

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

if ($liga[0]->published == 0) {
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Times', '', 16);

    $pdf->Cell(10, 15, clm_core::$load->utf8decode(JText::_('NOT_PUBLISHED')), 0, 0);
    $pdf->Ln();
    $pdf->Cell(10, 15, clm_core::$load->utf8decode(JText::_('GEDULD')), 0, 0);
} else {
    // DWZ Durchschnitte - Aufstellung
    $lid = $liga[0]->id;
    $result = clm_core::$api->db_nwz_average($lid);
    $a_average_dwz_lineup = $result[2];

    // Zellenhöhe -> Standard 6
    $zelle = 6;
    // Wert von Zellenbreite abziehen
    // Bspl. für Standard (Null) für Liga mit 7 Runden und 1 Durchgang
    $breite = 0;
    if ($params['round_date'] == '0') {
        $nbreite = 50;
        $rbreite = 25;
    } else {
        $nbreite = 40;
        $rbreite = 20;
    }
    // Überschrift Fontgröße Standard = 14
    $head_font = 12;
    // Fontgröße Standard = 12
    $font = 10;
    // Seitenlänge
    $lspalte_paar = 230;
    $lspalte_comment = 200;

    //$counter = $liga[0]->runden*$liga[0]->durchgang;
    if ($liga[0]->teil / 2 == 6) {
        $lspalte_paar = 220;
    }
    if ($liga[0]->teil / 2 == 5) {
        $lspalte_paar = 230;
    }
    if ($liga[0]->teil / 2 == 4) {
        $lspalte_paar = 240;
    }
    if ($liga[0]->teil / 2 == 3) {
        $lspalte_paar = 250;
    }
    if ($liga[0]->teil / 2 == 2) {
        $lspalte_paar = 260;
    }

    // Datum der Erstellung
    $date = JFactory::getDate();
    $now = $date->toSQL();

    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();

    $pdf->SetFont('Times', '', 6);
    $pdf->Cell(10, 2, ' ', 0, 0);
    $pdf->Cell(175, 4, clm_core::$load->utf8decode(JText::_('WRITTEN')).' '.clm_core::$load->utf8decode(JText::_('ON_DAY')).' '.clm_core::$load->utf8decode(JHTML::_('date', $now, JText::_('DATE_FORMAT_CLM_PDF'))), 0, 1, 'R');

    $pdf->SetFont('Times', '', 16);

    $pdf->Cell(10, 10, ' ', 0, 0);
    $pdf->Cell(80, 10, clm_core::$load->utf8decode(JText::_('PAAR_OVERVIEW')).' : '.clm_core::$load->utf8decode($liga[0]->name), 0, 1, 'L');

    // Rundenschleife
    $z = 0;
    $z2 = 0;
    $sum_paar = 0;
    $rund_sum = 0;
    $term = 0;

    for ($dd = 0; $dd < ($liga[0]->durchgang); $dd++) {
        $pdf->SetFont('Times', '', $head_font - 1);
        //$pdf->Ln();

        if ($liga[0]->durchgang != 1) {
            $pdf->Cell(10, 15, ' ', 0, 0);
        }
        if ($liga[0]->durchgang == 2 and $dd == 0) {
            $pdf->Cell(80, 15, clm_core::$load->utf8decode(JText::_('PAAR_HIN')), 0, 1, 'L');
        }
        if ($liga[0]->durchgang == 2 and $dd == 1) {
            $pdf->Cell(80, 15, clm_core::$load->utf8decode(JText::_('PAAR_RUECK')), 0, 1, 'L');
        }
        if ($liga[0]->durchgang > 2) {
            $pdf->Cell(80, 15, clm_core::$load->utf8decode(JText::_('PAAR_DG'))." ".($dd + 1), 0, 1, 'L');
        }

        for ($x = 0; $x < ($liga[0]->runden); $x++) {
            // DWZ Durchschnitte - gespielt in Runde
            $runde1 = $x + 1;
            $dg1 = $dd + 1;
            $result = clm_core::$api->db_nwz_average($lid, $runde1, $dg1);
            $a_average_dwz_round = $result[2];

            if ($pdf->GetY() > $lspalte_paar) {
                $pdf->AddPage();
            }

            if (isset($termin[$term]) and ($termin[$term]->published == '1')) {
                if ($termin[$term]->nr == ($x + 1 + (($dd) * $liga[0]->runden))) {
                    if ($termin[$term]->datum != '1970-01-01') {
                        $datum = ', '.JHTML::_('date', $termin[$term]->datum, JText::_('DATE_FORMAT_CLM_F'));
                        if ($params['round_date'] == '0' and isset($termin[$term]->startzeit)
                            and $termin[$term]->startzeit != '00:00:00') {
                            $datum .= '  '.substr($termin[$term]->startzeit, 0, 5).' Uhr';
                        }
                        if ($params['round_date'] == '1' and isset($termin[$term]->enddatum)
                            and $termin[$term]->enddatum > '1970-01-01' and $termin[$term]->enddatum != $termin[$term]->datum) {
                            $datum .= ' - '.JHTML::_('date', $termin[$term]->enddatum, JText::_('DATE_FORMAT_CLM_F'));
                        }
                    } else {
                        $datum = '';
                    }
                    $term++;
                }
                $pdf->SetFont('Times', '', $head_font - 1);
                $pdf->Cell(10, 15, ' ', 0, 0);
                $pdf->Cell(173 - (8 * $breite), $zelle, clm_core::$load->utf8decode($termin[$term - 1]->name).clm_core::$load->utf8decode($datum), 1, 0, 'L');
                $pdf->Ln();

                $pdf->SetFont('Times', '', $font);
                $pdf->Cell(10, 15, ' ', 0, 0);
                $pdf->Cell(8 - $breite, $zelle, clm_core::$load->utf8decode(JText::_('PAAR')), 1, 0, 'C');
                $pdf->Cell(8 - $breite, $zelle, clm_core::$load->utf8decode(JText::_('TLN')), 1, 0, 'C');
                $pdf->Cell($nbreite - $breite, $zelle, clm_core::$load->utf8decode(JText::_('HOME')), 1, 0, 'C');
                $pdf->Cell(12 - $breite, $zelle, clm_core::$load->utf8decode(JText::_('DWZ')), 1, 0, 'C');
                $pdf->Cell($rbreite - $breite, $zelle, clm_core::$load->utf8decode(JText::_('RESULT')), 1, 0, 'C');
                $pdf->Cell(8 - $breite, $zelle, clm_core::$load->utf8decode(JText::_('TLN')), 1, 0, 'C');
                $pdf->Cell($nbreite - $breite, $zelle, clm_core::$load->utf8decode(JText::_('GUEST')), 1, 0, 'C');
                $pdf->Cell(12 - $breite, $zelle, clm_core::$load->utf8decode(JText::_('DWZ')), 1, 0, 'C');

                if ($params['round_date'] == '1') {
                    $pdf->Cell(25 - $breite, $zelle, clm_core::$load->utf8decode(JText::_('FIXTURE_DATE')), 1, 0, 'C');
                }

                $tbreite = (2 * (8 + 50 + 12)) + 25 - 7 * $breite;
                $pdf->Ln();

                // Teilnehmerschleife
                for ($y = 0; $y < ($liga[0]->teil) / 2; $y++) {
                    if (!isset($paar[$z])) {
                        break;
                    }
                    if ($paar[$z]->runde > ($x + 1)) {
                        break;
                    }
                    $pdf->SetFont('Times', '', $font);
                    $pdf->Cell(10, 15, ' ', 0, 0);
                    $pdf->Cell(8 - $breite, $zelle, $paar[$z]->paar, 1, 0, 'C');
                    $pdf->Cell(8 - $breite, $zelle, $paar[$z]->tln_nr, 1, 0, 'C');
                    $pdf->Cell($nbreite - $breite, $zelle, clm_core::$load->utf8decode($paar[$z]->hname), 1, 0, 'C');

                    if ($a_average_dwz_round[$paar[$z]->htln] != '-' and $paar[$z]->htln != 0 and $paar[$z]->gtln != 0) {
                        $pdf->Cell(12 - $breite, $zelle, $a_average_dwz_round[$paar[$z]->htln], 1, 0, 'C');
                    } else {
                        $pdf->Cell(12 - $breite, $zelle, $a_average_dwz_lineup[$paar[$z]->htln], 1, 0, 'C');
                    }

                    // Wenn Paarung existiert dann Ergebnis-Summen anzeigen
                    while ($summe[$sum_paar]->runde < ($x + 1)) {
                        $sum_paar++;
                    }
                    if ($summe[$sum_paar]->runde == ($x + 1) and $summe[$sum_paar]->paarung == ($y + 1)) {
                        $pdf->Cell($rbreite - $breite, $zelle, $summe[$sum_paar]->sum.' : '.$summe[$sum_paar + 1]->sum, 1, 0, 'C');
                        if (($runden_modus == 4 or $runden_modus == 5) and ($summe[$sum_paar]->sum == $summe[$sum_paar + 1]->sum) and
                            ($summe[$sum_paar]->sum > 0)) {
                            $remis_com = 1;
                        } else {
                            $remis_com = 0;
                        }
                        $sum_paar = $sum_paar + 2;
                    } else {
                        $pdf->Cell($rbreite - $breite, $zelle, ' --- ', 1, 0, 'C');
                    }

                    $pdf->Cell(8 - $breite, $zelle, $paar[$z]->gtln, 1, 0, 'C');
                    $pdf->Cell($nbreite - $breite, $zelle, clm_core::$load->utf8decode($paar[$z]->gname), 1, 0, 'C');

                    if ($a_average_dwz_round[$paar[$z]->htln] != '-' and $paar[$z]->htln != 0 and $paar[$z]->gtln != 0) {
                        $pdf->Cell(12 - $breite, $zelle, $a_average_dwz_round[$paar[$z]->gtln], 1, 0, 'C');
                        $z2++;
                    } else {
                        $pdf->Cell(12 - $breite, $zelle, $a_average_dwz_lineup[$paar[$z]->gtln], 1, 0, 'C');
                    }
                    if ($params['round_date'] == '1') {
                        if (isset($paar[$z]->pdate) and $paar[$z]->pdate > '1970-01-01') {
                            $pdatum = JHTML::_('date', $paar[$z]->pdate, JText::_('DATE_FORMAT_CLM_Y2'));
                            if ($paar[$z]->ptime > '00:00:00') {
                                $pdatum .= '  '.substr($paar[$z]->ptime, 0, 5);
                            }
                        } else {
                            $pdatum = '';
                        }
                        $pdf->Cell(25 - $breite, $zelle, $pdatum, 1, 0, 'C');
                    }

                    if ($remis_com == 1) {
                        $pdf->Cell(1, $zelle, '', 0, 1);
                        $remis_com = 0;
                        $pdf->Cell(10, $zelle, ' ', 0, 0);
                        $pdf->Cell(8 - $breite, $zelle, $paar[$z]->paar, 1, 0, 'C');
                        $ztext = "";
                        if ($paar[$z]->ko_decision == 1) {
                            if ($paar[$z]->wertpunkte > $paar[$z]->gwertpunkte) {
                                $ztext = JText::_('ROUND_DECISION_WP_HEIM')." ".$paar[$z]->wertpunkte." : ".
                                    $paar[$z]->gwertpunkte." für ".$paar[$z]->hname;
                            } else {
                                $ztext = JText::_('ROUND_DECISION_WP_GAST')." ".$paar[$z]->gwertpunkte." : ".
                                    $paar[$z]->wertpunkte." für ".$paar[$z]->gname;
                            }
                        }
                        if ($paar[$z]->ko_decision == 2) {
                            $ztext = JText::_('ROUND_DECISION_BLITZ_HEIM')." ".$paar[$z]->hname;
                        }
                        if ($paar[$z]->ko_decision == 3) {
                            $ztext = JText::_('ROUND_DECISION_BLITZ_GAST')." ".$paar[$z]->gname;
                        }
                        if ($paar[$z]->ko_decision == 4) {
                            $ztext = JText::_('ROUND_DECISION_LOS_HEIM')." ".$paar[$z]->hname;
                        }
                        if ($paar[$z]->ko_decision == 5) {
                            $ztext = JText::_('ROUND_DECISION_LOS_GAST')." ".$paar[$z]->gname;
                        }
                        $pdf->SetFont('Times', '', $font);
                        $pdf->Cell($tbreite, $zelle, clm_core::$load->utf8decode($ztext), 'TBR', 0, 'C');
                        $pdf->SetFont('Times', '', $font);
                    }
                    if ($paar[$z]->comment != "") {
                        $paar[$z]->comment = str_replace($a_html, $a_pdf, $paar[$z]->comment);
                        $pdf->Cell(1, $zelle, '', 0, 1);
                        $pdf->Cell(10, $zelle, ' ', 0, 0);
                        $xx = $pdf->GetX();
                        $yy = $pdf->GetY();
                        $pdf->SetXY(($xx + 8 - $breite), $yy);
                        $ztext = JText::_('PAAR_COMMENT').$paar[$z]->comment;
                        $pdf->SetFont('Times','',$font);
                        $pdf->MultiCell($tbreite,$zelle,clm_core::$load->utf8decode($ztext),'LTBR','L');
                        $pdf->SetFont('Times','',$font);
                        $yy1 = $pdf->GetY();
                        $pdf->SetXY($xx,$yy);
                        $pdf->Cell(8 - $breite,($yy1 - $yy),$paar[$z]->paar,1,0,'C');

                    }
                    $z++;
                    $pdf->Ln();
                }
                $pdf->Ln($zelle);
            } else {
                if (isset($termin[$term]) and ($termin[$term]->published == '0')) {
                    $term++;
                }
            }
        }
    }
}

$pdf->Output(clm_core::$load->utf8decode(JText::_('PAAR_OVERVIEW')).' '.clm_core::$load->utf8decode($liga[0]->name).'.pdf','D');
exit;
